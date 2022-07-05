<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';

use Restserver\Libraries\REST_Controller;

class ApiCondicionDesembolso extends REST_Controller {
    //Tipo solicitud
    const RETANQUEO = "RETANQUEO";

	public function __construct()
	{
		parent::__construct();

		$this->load->library('User_library');
		$auth = $this->user_library->check_token();
		if($auth->status == parent::HTTP_OK)
		{
			// MODELS
            $this->load->model('operaciones_model', '', TRUE);
            $this->load->model('Solicitud_m');
            $this->load->model('Credito_model', 'credito_model', TRUE);
            $this->load->model('CreditoDetalle_model', 'credito_detalle_model', TRUE);
            $this->load->model('CreditoCondicion_model', '', TRUE);
            $this->load->model('Quota_model', 'quota_model', TRUE);
            $this->load->model('NivelesClientes_model', 'niveles_clientes_model', TRUE);
            $this->load->model('SolicitudBeneficios_model', '', TRUE);
            
			// LIBRARIES
			$this->load->library('form_validation');
		}else{
			$this->session->sess_destroy();
	       	$this->response(['redirect'=>base_url('login')],$auth->status);
		}
	}

	public function recalcular_post(){
        //SET VALUES FROM POST
		$id_solicitud = $this->input->post("id_solicitud");
		$fecha_nueva =(empty($this->input->post("fecha_nueva")))? "" : date('Y-m-d',strtotime($this->input->post("fecha_nueva")));
		$solicitado_nuevo = $this->input->post("solicitado_nuevo");
		$fecha_otorgamiento = (empty($this->input->post("fecha_otorgamiento")))? "" : date('Y-m-d',strtotime($this->input->post("fecha_otorgamiento")));

		$this->db->trans_start();

        $condicionDesembolso = $this->Solicitud_m->getSolicitudDesembolso($id_solicitud);
        $condicionDesembolso = $condicionDesembolso[0];

		$solicitud = $this->Solicitud_m->getSolicitudesBy(['id_solicitud' => $id_solicitud]);
        $solicitud = $solicitud[0];

        $plazo_nuevo = (empty($this->input->post("plazo_nuevo"))) ? $condicionDesembolso['plazo'] : $this->input->post("plazo_nuevo");


        $monotoAnterior = $condicionDesembolso['capital_solicitado'];
        $montoNuevo = $solicitado_nuevo;

        $plazoAnterior = $condicionDesembolso['plazo'];
        $plazoNuevo = $plazo_nuevo;

        $vencimientoAnterior = $condicionDesembolso['fecha_pago_inicial'];
        $vencimientoNuevo = $fecha_nueva;

        if($condicionDesembolso['idcondicion_simulador'] > 0){
            $idcondicion_simulador = $condicionDesembolso['idcondicion_simulador'];
        }else{
            $idcondicion_simulador = $this->Solicitud_m->getSolicitudCondicion($id_solicitud)[0]['idcondicion_simulador'];
        }

        $condicionSimulador = $this->Solicitud_m->getCondicionSimulador(['id' => $idcondicion_simulador]);
        $condicionSimulador = $condicionSimulador[0];

        if($condicionDesembolso['tasa_interes'] == 0
            || $condicionDesembolso['seguro'] == 0
            || $condicionDesembolso['administracion'] == 0
            || $condicionDesembolso['iva'] == 0
            ){
                if($solicitud->tipo_solicitud === self::RETANQUEO){
                    //SETUP FOR TABLA NIVELES_CLIENTES
                    $params_nc['id_cliente']= $solicitud->id_cliente; 
                    $fields = [
                        'id',
                        'id_cliente',
                        'monto_disponible',
                        'beneficio_plazo',
                        'tasa_interes',
                        'seguro',
                        'gastos_administrativos1',
                        'gastos_plataforma1',
                        'gastos_plataforma2',
                        'gastos_administrativos2',
                        'iva'
                    ];

                    $niveles_clientes = $this->niveles_clientes_model->findOneBy($params_nc, $fields);
                    $niveles_clientes = $niveles_clientes[0];
                    
                    if(!empty($niveles_clientes)){
                        
                        $condicionDesembolso['tasa_interes'] = $niveles_clientes->tasa_interes;
                        $condicionDesembolso['plazo'] = $niveles_clientes->beneficio_plazo;
                        $condicionDesembolso['seguro'] = $niveles_clientes->seguro;
                        $condicionDesembolso['administracion'] = $niveles_clientes->gastos_administrativos1;
                        $condicionDesembolso['tecnologia'] = $niveles_clientes->gastos_plataforma1;
                        $condicionDesembolso['iva'] = $niveles_clientes->iva;

                        $solicitudBeneficioData=[
                            'monto_maximo'=> $niveles_clientes->monto_disponible,
                            'plazo_maximo'=> $niveles_clientes->beneficio_plazo,
                        ];
                        //UPDATE TABLA solicitudes.solicitud_beneficios con datos de niveles_clientes.
                        $prueba =$this->SolicitudBeneficios_model->update(['id_solicitud'=>$id_solicitud], $solicitudBeneficioData);
                    }
                }else{
                    if(!empty($condicionSimulador)){

                        $condicionDesembolso['tasa_interes'] = $condicionSimulador['tasa_interes'];
                        $condicionDesembolso['seguro'] = $condicionSimulador['seguro'];
                        $condicionDesembolso['administracion'] = $condicionSimulador['gastos_administrativos1'];
                        $condicionDesembolso['tecnologia'] = $condicionSimulador['gastos_plataforma1'];
                        $condicionDesembolso['iva'] = $condicionSimulador['iva'];
                    }

                }
                $this->Solicitud_m->updteCondicionDesembolso($id_solicitud, $condicionDesembolso);
        }


		$response = [];
		$newDetalleCredito = $this->calcular_condicion_desembolso($solicitud, $condicionDesembolso, $condicionSimulador, $plazoAnterior, $plazo_nuevo, $fecha_nueva, $solicitado_nuevo, $fecha_otorgamiento);
        
        //UPDATE TABLA SOLICITUD_CONDICION_DESEMBOLSO
		$response['condicion_desembolso'] = $this->Solicitud_m->updteCondicionDesembolso($id_solicitud, $newDetalleCredito['condicionDesembolso']);

        //SI EXISTE UN CREDITO
		if($solicitud->id_credito > 0){
			$id_credito =  $solicitud->id_credito;
			
            $creditosDetalle = $this->credito_detalle_model->search(['id_credito'=> $id_credito]);
            $recalculable = true;
            if(!empty($creditosDetalle)){
                foreach($creditosDetalle as $key => $creditoDetalle){
                    if(strtolower($creditoDetalle->estado) == "pagado" ){
                        $recalculable = false;
                        break;
                    }
                }

                if($recalculable && $plazo_nuevo == $plazoAnterior){

    			     //UPDATE TABLA CREDITOS
    			     $response['credito'] = $this->credito_model->update(['id' => $id_credito], $newDetalleCredito['credito']);
        			//UPDATE TABLA CREDITO_CONDICION
        			$response['credito_condicion'] = $this->CreditoCondicion_model->update(['id_credito' => $id_credito], $newDetalleCredito['creditoCondicion']);

                    //UPDATE TABLA CREDITO DETALLE
                    foreach($creditosDetalle as $key => $creditoDetalle){
                        $credito_detalle_id = $creditoDetalle->id;
                        $data = $newDetalleCredito['creditoDetalle'][$key];
                        $response['credito_detalle'] = $this->credito_detalle_model->update(['id'=>$credito_detalle_id], $data);
                    }
                }
                
            }
            
		}
        $this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
		    $response['error'] = "Hubo un error al calcular la condicion de desembolso.";
		}else{
		    $response['message'] = "El desembolso se actualizo correctamente.";
		    
		    $dataTrackGestion = [
                'id_solicitud'=>(int)$solicitud->id,
                'observaciones'=>"Monto anterior:  $monotoAnterior , Monto nuevo: $montoNuevo , Plazo anterior: $plazoAnterior , Plazo nuevo: $plazo_nuevo , Vencimiento anterior: $vencimientoAnterior , Vencimiento nuevo: $vencimientoNuevo", 
                'id_cliente'=>(int)$solicitud->id_cliente, 
                'id_credito'=>(int)$solicitud->id_credito,
                'operador' => $this->session->userdata('user')->first_name." ".$this->session->userdata('user')->last_name,
                'id_tipo_gestion' => 0
            ];
            $endPoint =  base_url('api/admin/track_gestion');
            $response['trackGestion'] = $this->trackGestion($endPoint, 'POST', $dataTrackGestion);

			$this->db->trans_commit();
		} 

		$this->response($response);

	}
	private function calcular_condicion_desembolso($solicitud, $condicion, $condicionSimulador = [], $plazoAnterior, $plazo_nuevo, $fecha_nueva, $solicitado_nuevo, $fecha_otorgamiento){

        $prestamo = (empty($solicitado_nuevo)) ? $condicion['capital_solicitado'] : $solicitado_nuevo;
        $nueva_fecha = (empty($fecha_nueva)) ? $condicion['fecha_pago_inicial']: $fecha_nueva;
        $plazo_nuevo = (empty($plazo_nuevo)) ? $plazoAnterior : $plazo_nuevo;

        //EXISTE UN CREDITO REL A LA SOLICITUD
        if($solicitud->id_credito > 0){ 
            $credito = $this->credito_model->search(["id"=> $solicitud->id_credito]);
            $credito = $credito[0];
            $fecha_otorgamiento = (empty($fecha_otorgamiento)) ? $credito['fecha_otorgamiento']: $fecha_otorgamiento;
            
            $plazo_dias = date_diff_days($fecha_otorgamiento, $nueva_fecha);

        }else{
            $plazo_dias = date_diff_days(date("Y-m-d"), $nueva_fecha);
        }

        if($plazo_nuevo == 1){
            $tasa_interes = $condicion['tasa_interes'];
            $seguro = $condicion['seguro'];
            $administracion_fija = $condicion['administracion'];
            $tecnologia_fija = $condicion['tecnologia'];
            $tecnologia = ($tecnologia_fija * $plazo_dias );

        }elseif($plazo_nuevo > 1){

            $aux = ($plazo_nuevo - 1) * 30;
            $plazo_dias =  $plazo_dias + $aux;
            //
            $tasa_interes = $condicionSimulador['tasa_interes'];
            $seguro = $condicionSimulador['seguro'];
            $administracion_fija = $condicion['administracion'];
            $tecnologia_fija = $condicion['tecnologia'];
            $tecnologia = $tecnologia_fija * $plazo_nuevo;
        }

        $valor_solicitado = $prestamo;
        
        $fondo_garantia = (float)$condicionSimulador['fondo_garantia'];
        if ($fondo_garantia < 100){
            $fondo_aval = $valor_solicitado * $fondo_garantia / 100;
        } else {
            $fondo_aval = $fondo_garantia;
        }
        
        $TEA_percent = ($tasa_interes / 100); //%
        $Seguro_percent	= ($seguro / 100); //%

        $interes = ($valor_solicitado * ( (1 + $TEA_percent) ** ($plazo_dias/360) - 1 )) ;
    
        $seguro = ($valor_solicitado * $Seguro_percent);

        $administracion = $administracion_fija;

        $subtotal = $valor_solicitado + $interes + $seguro + $administracion;


        $iva = (($administracion + $tecnologia) * 0.19);
        
        $total_pagar = ceil($subtotal + $tecnologia + $iva + $fondo_aval);
        // Array para actualizar tb solicitud_condicion_desembolso
    	$newCondicionDesembolso = [
    		'dias' => $plazo_dias,
    		'total_devolver' => $total_pagar,
    		'plazo' => $plazo_nuevo,
    		'capital_solicitado' => $valor_solicitado,
    		'fecha_pago_inicial' => $nueva_fecha,

    	];
        // Array para actualizar tb credito_condicion
    	$newCreditoCondicion = [
    		'dias' => $plazo_dias,
    		'total_devolver' => $total_pagar,
    		'plazo' => $plazo_nuevo,
    		'capital_solicitado' =>	$valor_solicitado,
    		'fecha_pago_inicial' => $nueva_fecha,
    		'aval' => $fondo_aval
    	];

    	// Array para actualizar tb creditos
    	$newCredito = [
    		'monto_devolver' => $total_pagar,
    		'plazo' => $plazo_nuevo,
    		'monto_prestado' =>$valor_solicitado,
    		'fecha_primer_vencimiento' => $nueva_fecha,
    		'fecha_otorgamiento' => date('Y-m-d',strtotime($fecha_otorgamiento))
    	];

    	// Array para actualizar tb credito_detalle

        $newCreditoDetalle = [];
        for($i = 0; $i < $plazo_nuevo; $i++){
            //SUMA DE DIAS RESTANTES PARA CADA PLAZO
            if($i > 0) {
                $plazo_aux = $plazo_nuevo - 1;
                $aux = $plazo_dias / $plazo_aux;
                $nueva_fecha = date('Y-m-d', strtotime($nueva_fecha. " + $aux days"));
            }
            $newCreditoDetalle[$i] = [
                'monto_cuota' =>  $total_pagar / $plazo_nuevo,
                'monto_cobrar' => $total_pagar / $plazo_nuevo,
                'fecha_vencimiento' => $nueva_fecha,
                'capital' => (float)$valor_solicitado
            ];

        }

		return ["condicionDesembolso"=>$newCondicionDesembolso, "creditoCondicion"=> $newCreditoCondicion, "credito"=>$newCredito, "creditoDetalle"=>$newCreditoDetalle];

	}

	private function trackGestion($endPoint, $method = 'POST',  $params=[] ){
        $token = $this->session->userdata('token');
        $curl = curl_init();
        $options[CURLOPT_HTTPHEADER] = ['Authorization:'.$token];
        $options[CURLOPT_POSTFIELDS] = $params;
        $options[CURLOPT_URL] = $endPoint;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

        if(ENVIRONMENT == 'development')
        {
            $options[CURLOPT_CERTINFO] = 1;
            $options[CURLOPT_SSL_VERIFYPEER] = 0;
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        
        curl_setopt_array($curl,$options);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err)
        {
          $response['error'] = 'cURL Error #:' . $err;
        }

        return $response;
    }
}

?>