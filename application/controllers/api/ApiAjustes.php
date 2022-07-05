<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiAjustes extends REST_Controller
{      
	public function __construct()
	{
		parent::__construct();

		$this->load->library('User_library');
        $this->load->helper(['jwt', 'authorization']); 

		$auth = $this->user_library->check_token();

		if($auth->status == parent::HTTP_OK)
		{
			// MODELS
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
            $this->load->model('Cliente_model', 'cliente_model', TRUE);
            $this->load->model('CreditoDetalle_model', 'credito_detalle_model', TRUE);
			$this->load->model('credito_model','credito_model',TRUE);
            $this->load->model('PagoCredito_model', 'pago_credito_model', TRUE);
            $this->load->model('BankEntidades_model', 'bank_model', TRUE);
            $this->load->model('BankTipoCuenta_model', 'type_account_model', TRUE);
            $this->load->model('operadores/Operadores_model','operadores',TRUE);
            $this->load->model('Chat', 'chat', TRUE);
            $this->load->model('legales/Legales_model', 'legales', TRUE);
			$this->load->model('galery_model', 'galery_model', TRUE);


			// LIBRARIES
			$this->load->library('form_validation');
		}else{
			$this->session->sess_destroy();
	       	$this->response(['redirect'=>base_url('login')],$auth->status);
		}
    }
    /**
     * metodo que consulta la solicitud que sera ajustada por el supervisor
     * @param id_solicitud ID de la solicitud consultada
     */
    public function buscar_solicitud_get($id_solicitud) {

        if(strlen($id_solicitud) > 0) {
            $solicitud = $this->solicitud_model->getSolicitud($id_solicitud);   
            if(!empty($solicitud)){
                if($solicitud[0]['operador_asignado'] !=null && $solicitud[0]['operador_asignado'] !=0){
                    $nombre = $this->operadores->get_operadores_by(['id_operador_buscar' => $solicitud[0]['operador_asignado']]);
                    $value = get_object_vars($nombre[0]);
                    $nombre_operador = $value['nombre_apellido'];
                }else{
                    $nombre_operador = 'Sin operador asignado';
                }
                $response['pasos'] = $this->verificar_paso_solicitud($solicitud[0]['paso'], $solicitud[0]['estado']);
                $response['estados'] = $this->verificar_estado_solicitud($solicitud[0]['estado'], $solicitud[0]['paso'], $solicitud[0]['fecha_alta']);
                $response['situaciones'] = $this->verificar_situacion_solicitud($solicitud[0]['estado'], $solicitud[0]['paso']);

                if($solicitud[0]['estado'] == 'PAGADO'){
                    $bloqueo = $this->legales->consultar_bloqueo($solicitud[0]['documento']);
                    $baja = $this->legales->consultar_baja($solicitud[0]['documento']);
                    $transito = $this->solicitud_model->get_solicitudes_transito($solicitud[0]['documento']);
                    $credito_vm = $this->credito_model->get_creditos_cliente(['id_cliente'=>$solicitud[0]['id_cliente'], 'credito_estado'=>"vigente','mora"]);
                    
                    if(empty($bloqueo) && empty($baja) && empty($transito) && empty($credito_vm)){
                        $nivelC = $this->solicitud_model->solicitud_beneficio_nc($solicitud[0]['id_cliente']);
                        if (!empty($nivelC)) {
                            $response['beneficios']['disponible'] = $nivelC[0]->monto_disponible + $nivelC[0]->beneficio_monto_fijo;
                            $response['beneficios']['incremento'] = 10000;
                            $response['beneficios']['max'] = ($nivelC[0]->monto_disponible + $nivelC[0]->beneficio_monto_fijo)*2;
                            $response['beneficios']['ini'] = (round(($nivelC[0]->monto_disponible + $nivelC[0]->beneficio_monto_fijo)/100000)*100000)-100000;
                            $response['beneficios']['plazo'] = $nivelC[0]->beneficio_plazo;
                        }

                    }
                } else{
                    $beneficios = $this->solicitud_model->solicitud_beneficio($id_solicitud);
                    $estados = ["APROBADO","TRANSFIRIENDO","PAGADO","RECHAZADO","ANULADO"];

                    if (!empty($beneficios) && !in_array($solicitud[0]['estado'], $estados)) {
                        $response['beneficios']['disponible'] = $beneficios[0]->monto_maximo;
                        $response['beneficios']['incremento'] = 50000;
                        $response['beneficios']['plazo'] = 0;
                        $response['beneficios']['ini'] = $beneficios[0]->monto_maximo;
                        if($solicitud[0]['tipo_solicitud'] == 'PRIMARIA')
                            $response['beneficios']['max'] = 550000;

                        if($solicitud[0]['tipo_solicitud'] == 'RETANQUEO')
                            $response['beneficios']['max'] = $beneficios[0]->monto_maximo + 200000;

                    }

                }


                $response['operadores'] = $this->operadores->get_operadores_by(['estado' => "1",'tipo_operadores' => 1]);
                $sol_ajustes = $this->solicitud_model->getSolicitudAjustesBy(['id_solicitud' => $id_solicitud, 'estado' => 0]); 
                $response['sol_ajustes'] = [];               
                foreach ($sol_ajustes as $sol => $value) {
                    $value->name_operador = $this->operadores->get_operadores_by(['id_operador_buscar' => $value->id_operador])[0]->nombre_apellido;
                    $response['sol_ajustes'][] = $value;
                }

                $response['verificacion']='';

                $veriff = $this->solicitud_model->getVeriff_scan_all($id_solicitud, ['respuesta_match'=>"'declined','approved'"]);
                if (!empty($veriff)) {
                    $response['verificacion']='<a class="btn btn-success" id="verificacion"> AUTORIZAR </a>';

                }

                $habilitar =  $this->solicitud_model->solicitudes_pagare($id_solicitud);
                $response['boton'] = [];
                //if (empty($habilitar)) {
                    
                    $crear =  $this->solicitud_model->solicitudes_crear_pagare($id_solicitud);
                    if (!empty($crear)) {
                        $response['boton'] = '<a class="btn btn-success" data-fun="1" id="pagare"> CREAR </a>';
                       
                    } else {
                        $firmar =  $this->solicitud_model->solicitudes_firmar_pagare($id_solicitud);
                        if (!empty($firmar))
                            $response['boton'] = '<a class="btn btn-warning" data-fun= "2" id="pagare"> FIRMAR </a>';

                    }
                //}

                

                /*** Bancos ***/
                $bancos = $this->bank_model->search(['id_estado_banco' => 1]);
                $response['bancos'] = $bancos;
                /*** Tipos Cuenta ***/
                $tipos_cuenta = $this->type_account_model->search(['id_estado_tipocuenta' => 1]);
                $response['tipos_cuenta'] = $tipos_cuenta;
                /*** Datos bancarios del cliente ***/
                $datos_bancarios = $this->solicitud_model->getDatosBancarios($id_solicitud);
                $response['datos_bancarios'] = $datos_bancarios;
                /*** Imágenes ***/
                $imagenes = $this->solicitud_model->getImagenSolicitud($id_solicitud);
                if (!empty($imagenes)) {
                    $response['imagenes'] = $imagenes;
                } else {
                    $response['imagenes'] = "";
                }
                $buro = $this->solicitud_model->getBuro($id_solicitud);
                $response['buro'] = $buro;

                // $response['solicitud_imagenes'] = $this->galery_model->get_solicitud_imagenes(['id_solicitud' => $id_solicitud, 'id_imagen_requerida_in' => [7,17]]);
                $response['solicitud_imagenes'] = $this->galery_model->search_images(['id_solicitud' => $id_solicitud, 'id_imagen_requerida_in' => [7,17]]);
                $response['status']['ok']	 = TRUE;
                $response['solicitud'] 	 = $solicitud[0];
                $response['nombre_operador'] = $nombre_operador;

            } else{
                $response['status']['ok']	 = FALSE;
                $response['mensaje']	 = 'No se encontro la solicitud';
            }
        } else {
            $response['status']['ok']	 = FALSE;
            $response['mensaje']	 = 'Clave de busqueda invalida';
        }

        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);
    }

    public function autorizar_verificacion_post(){
        $id_solicitud = $this->post('id_solicitud');

        if ( isset( $id_solicitud ) ) {
            $id_encript =  AUTHORIZATION::encodeData( $id_solicitud );
            $endPoint =URL_API_IDENTITY."api/veriff/auth/session";

            $data = [
                "id_solicitud" => $id_encript,
                "estado" => 1,
            ];
            $res = json_decode($this->curl($endPoint, "POST", $data));
            $response['status']['ok']	 = FALSE;

            if ($res->success) 
                $response['status']['ok']	 = TRUE;

        }else{
            AUTHORIZATION::trackear_error($this->post(),$this->input->request_headers(),$this->input->ip_address(),"reprocesar_pagare_post");
            $response['status']['ok']	 = FALSE;
        }
        $status = parent::HTTP_OK;
		$this->response($response, $status);

    }

    public function reprocesar_pagare_post()
    {
        $id_solicitud = $this->post('id_solicitud');
        $funcion = $this->post('funcion');

        if ( isset( $id_solicitud ) ) {
            $id_encript =  AUTHORIZATION::encodeData( $id_solicitud );
            $endPoint = '';
            /*if($funcion == 1)
                $endPoint =PAGARE_URL."/api/uanataca/request/crear_documentos";

            if($funcion == 2)
                $endPoint =PAGARE_URL."/api/uanataca/request/firmar_documento";
            */
            $endPoint = PAGARE_URL."/api/uanataca/pagare/actualizar_pagares/".trim($id_solicitud)."/".$this->session->userdata['idoperador'];

            /*$data = [
                "id_solicitud" => $id_encript,
            ];*/
            $response = json_decode($this->curl($endPoint, "POST", []));

        }else{
            AUTHORIZATION::trackear_error($this->post(),$this->input->request_headers(),$this->input->ip_address(),"reprocesar_pagare_post");
            $response['status']['ok']	 = FALSE;
        }
        $status = parent::HTTP_OK;
		$this->response($response, $status);
    }

    public function actualizar_paso_post(){
        $id_solicitud = $this->post('id_solicitud');
        $new_paso = $this->post('new_paso');

        $solicitud = $this->solicitud_model->getSolicitud($id_solicitud);

        if(!empty($solicitud)) {

            $pasos_disponibles = $this->verificar_paso_solicitud($solicitud[0]['paso'], $solicitud[0]['estado']);
            //si el nuevo paso esta entre los pasos disponibles
            if (in_array($new_paso, $pasos_disponibles)) {
                $data = ['paso'=>$new_paso];
                if($this->solicitud_model->edit($id_solicitud, $data)){
                    $response['status']['ok']	 = TRUE;
                    $response['mensaje'] 	 = 'Solicitud actualizada al paso '.$new_paso;
                    //actualizamos el chatbot
                    $this->_actualizar_chatbot($solicitud[0]['documento']);
                } else{
                    $response['status']['ok']	 = FALSE;
                    $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada al paso '.$new_paso;
                }
            } else{
                $response['status']['ok']	 = FALSE;
                $response['mensaje'] 	 = 'La solicitud no puede ser actualizada al paso '.$new_paso;
            }
        }else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje']	 = 'No se encontro la solicitud';
        }

        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);
    }

    public function actualizar_cupo_post(){
        $id_solicitud = $this->post('id_solicitud', true);
        $monto = $this->post('monto', true);
        $plazo = $this->post('plazo', true);
        $solicitud = $this->solicitud_model->getSolicitud($id_solicitud);
        if(!empty($solicitud)){
            //validacion de estados

            if($plazo > 0){
                //update
                $data = [
                    'beneficio_plazo' => $plazo,
                    'monto_disponible' => $monto,
                    'beneficio_monto_fijo' => 0 //$monto * 10 /100
                ];
                $update = $this->solicitud_model->update_niveles_clientes($solicitud[0]['id_cliente'], $data);
            } else {
                $data = [
                    'monto_maximo' => $monto
                ];
                $update = $this->solicitud_model->update_solicitud_beneficio($id_solicitud, $data);
                if ($update) {
                    $condicion = $this->solicitud_model->getSolicitud_desembolso($id_solicitud);
    
                    if (!empty($condicion)) {
    
                        //recalcular desembolso
                        $endPoint = URL_BACKEND."api/condicion_desembolso/recalcular";
                        $data = [
                            "id_solicitud" => $id_solicitud,
                            "solicitado_nuevo" => $monto,
                            "plazo_nuevo" => '',
                            "fecha_nueva" => '',
                            "fecha_otorgamiento" => ''
                        ];
                        $resp = $this->curl($endPoint, "POST", $data);	
                        //var_dump($response);die;	
                
                    }
                    $response['status']['ok']	 = TRUE;
                    $response['mensaje'] 	 = 'Monto maximo actualizado';
                }
            }
            
            if ($update) {
                $response['status']['ok']	 = TRUE;
                $response['mensaje'] 	 = 'Monto maximo actualizado';
            }
             else {
                $response['status']['ok']	 = FALSE;
                $response['mensaje'] 	 = 'No fue posible actualizar el Monto';
            }
        }else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje'] 	 = 'No se encontro la solicitud';
        }
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $this->response($response, $status);
    }

    public function reasignar_solicitud_post(){
        $id_solicitud = $this->post('id_solicitud');
        $solicitud = $this->solicitud_model->getSolicitud($id_solicitud);
        $operador = $this->post('operador');

        if(!empty($solicitud)){
            if($solicitud[0]['estado'] != null && $operador == 0){                
                $data['estado'] = 'ANALISIS';
            }
                $data['operador_asignado'] = $operador;
            
            if($this->solicitud_model->edit($id_solicitud, $data)){
                $response['status']['ok']	 = TRUE;
                $response['mensaje'] 	 = 'Solicitud actualizada';
                $response['operador_asignado'] 	 = ($operador == 0)? 'Al azar':$operador;
                $response['operador_anterior'] 	 = ($solicitud[0]['operador_asignado'] == 0)? 'Sin operador':$solicitud[0]['operador_asignado'];
            }else{
                $response['status']['ok']	 = FALSE;
                $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada';
            }
        }else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada';
        }
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $this->response($response, $status);
    }


    public function levantar_rechazado_post(){
        $id_solicitud = $this->post('id_solicitud');
        $data = ['respuesta_analisis' => 'APROBADO'];
        if($this->solicitud_model->edit($id_solicitud, $data)){
            $response['status']['ok']	 = TRUE;
            $response['mensaje'] 	 = 'Solicitud actualizada';
        }else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada';
        }
        $solicitud = $this->solicitud_model->getSolicitud($id_solicitud);
        if($solicitud['0']['pagare_enviado'] !=1){
            
            $veriff_scan = $this->solicitud_model->getVeriff_scan($id_solicitud);
            if(empty($veriff_scan)) {
              
                $desembolso = $this->solicitud_model->getSolicitud_desembolso($id_solicitud);
                if(empty($desembolso)) {    
                    
                    $datos_bancarios = $this->solicitud_model->getDatosBancarios($id_solicitud);
                    if(empty($datos_bancarios)) {   
                        
                        $referencia = $this->solicitud_model->getReferencia($id_solicitud);
                        $count_referencia = count($referencia);
                        if(empty($referencia)) {   
                            $solicitud_laboral = $this->solicitud_model->getSolicitud_Laboral($id_solicitud);
                            $cantidad = count($solicitud_laboral);
                            switch ($cantidad) {
                                case 1:
                                    $data = ['paso'=>5, 'estado' => NULL];
                                break;
                                case 0:
                                    $data = ['paso'=>6, 'estado' => NULL];
                                break;
                            }
                            if($this->solicitud_model->edit($id_solicitud, $data)){
                                $response['status']['ok']	 = TRUE;
                                $response['mensaje'] 	 = 'Solicitud actualizada';
                            }else{
                                $response['status']['ok']	 = FALSE;
                                $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada ';
                            }

                        }else{
                            if($count_referencia > 2) {    

                                $solicitud_laboral = $this->solicitud_model->getSolicitud_Laboral($id_solicitud);
                                $cantidad = count($solicitud_laboral);
                                switch ($cantidad) {
                                    case 1:
                                        $data = ['paso'=>5, 'estado' => NULL];
                                    break;
                                    case 0:
                                        $data = ['paso'=>6, 'estado' => NULL];
                                    break;
                                }
                                if($this->solicitud_model->edit($id_solicitud, $data)){
                                    $response['status']['ok']	 = TRUE;
                                    $response['mensaje'] 	 = 'Solicitud actualizada';
                                }else{
                                    $response['status']['ok']	 = FALSE;
                                    $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada ';
                                }
                            }else{
                                switch ($count_referencia) {
                                    case 2:
                                        $data = ['paso'=>8, 'estado' => NULL];
                                    break;
                                    case 1:
                                        $data = ['paso'=>5, 'estado' => NULL];
                                    break;
                                    case 0:
                                        $data = ['paso'=>6, 'estado' => NULL];
                                    break;
                                }
                                if($this->solicitud_model->edit($id_solicitud, $data)){
                                    $response['status']['ok']	 = TRUE;
                                    $response['mensaje'] 	 = 'Solicitud actualizada';
                                }else{
                                    $response['status']['ok']	 = FALSE;
                                    $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada';
                                }
                            }
                        }
                    }else{
                        $data = ['paso'=>9, 'estado' => NULL];
                        if($this->solicitud_model->edit($id_solicitud, $data)){
                            $response['status']['ok']	 = TRUE;
                            $response['mensaje'] 	 = 'Solicitud actualizada';
                        }else{
                            $response['status']['ok']	 = FALSE;
                            $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada';
                        }
                    }
                }else{
                    $data = ['paso'=>13, 'estado' => NULL];
                    if($this->solicitud_model->edit($id_solicitud, $data)){
                        $response['status']['ok']	 = TRUE;
                        $response['mensaje'] 	 = 'Solicitud actualizada';
                    }else{
                        $response['status']['ok']	 = FALSE;
                        $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada';
                    }                
                }
            }else{
                $data = ['paso'=>16, 'estado' => NULL];
                if($this->solicitud_model->edit($id_solicitud, $data)){
                    $response['status']['ok']	 = TRUE;
                    $response['mensaje'] 	 = 'Solicitud actualizada';
                }else{
                    $response['status']['ok']	 = FALSE;
                    $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada';
                }
            }
        }else{
            $data = ['paso'=>16, 'estado' => 'ANALISIS'];
            if($this->solicitud_model->edit($id_solicitud, $data)){
                $response['status']['ok']	 = TRUE;
                $response['mensaje'] 	 = 'Solicitud actualizada';
            }else{
                $response['status']['ok']	 = FALSE;
                $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada';
            }
        }      

        if($response['status']['ok']){
            //actualizamos el chatbot
            $this->_actualizar_chatbot($solicitud[0]['documento']);
        }

        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $this->response($response, $status);
    }
    public function actualizar_estado_post(){
        $id_solicitud = $this->post('id_solicitud');
        $new_estado = $this->post('new_estado');

        $solicitud = $this->solicitud_model->getSolicitud($id_solicitud);
        if(!empty($solicitud)) {
            $estados_disponibles = $this->verificar_estado_solicitud( $solicitud[0]['estado'], $solicitud[0]['paso'], $solicitud[0]['fecha_alta']);
            //si el nuevo paso esta entre los pasos disponibles
            if (in_array($new_estado, $estados_disponibles)) {
                $data = ['estado'=>$new_estado];
                if ($new_estado == 'ANULADO') {
                    $data = ['estado'=>$new_estado, 'fecha_ultima_actividad'=>$solicitud[0]['fecha_alta']];
                }
                if($this->solicitud_model->edit($id_solicitud, $data)){
                    $response['status']['ok']	 = TRUE;
                    $response['mensaje'] 	 = 'Solicitud actualizada al estado '.$new_estado;

                    //actualizamos el chatbot
                    $this->_actualizar_chatbot($solicitud[0]['documento']);
                } else{
                    $response['status']['ok']	 = FALSE;
                    $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada al paso '.$new_estado;
                }
            } else{
                $response['status']['ok']	 = FALSE;
                $response['mensaje'] 	 = 'La solicitud no puede ser actualizada al estado '.$new_estado;
            }
        }else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje']	 = 'No se encontro la solicitud';
        }

        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);
    }


    public function actualizar_situacion_post(){
        $id_solicitud = $this->post('id_solicitud');
        $new_situacion = $this->post('new_situacion');

        $solicitud = $this->solicitud_model->getSolicitud($id_solicitud);

        if(!empty($solicitud)) {

            $situaciones_disponibles = $this->verificar_situacion_solicitud( $solicitud[0]['estado'], $solicitud[0]['paso']);
            //si el nuevo paso esta entre los pasos disponibles
            //var_dump(array_column($situaciones_disponibles,'id_situacion'));die;
            if (in_array($new_situacion, array_column($situaciones_disponibles,'id_situacion'))) {
                $data = ['id_situacion_laboral'=>$new_situacion];
                if($this->solicitud_model->edit($id_solicitud, $data)){
                    $response['status']['ok']	 = TRUE;
                    $response['mensaje'] 	 = 'Solicitud actualizada a la situacion '.$new_situacion;
                } else{
                    $response['status']['ok']	 = FALSE;
                    $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada a la situacion '.$new_situacion;
                }
            } else{
                $response['status']['ok']	 = FALSE;
                $response['mensaje'] 	 = 'La solicitud no puede ser actualizada a la situacion '.$new_situacion;
            }
        }else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje']	 = 'No se encontro la solicitud';
        }

        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);
    }

    public function actualizar_telefono_post(){
        $id_solicitud = $this->post('id_solicitud');
        $new_telefono = trim($this->post('new-telefono'));
        $solicitud = $this->solicitud_model->getSolicitud($id_solicitud);

        
        if(!empty($solicitud)){
            $verificacion = $this->verificacion_telefono($new_telefono, $solicitud[0]['documento']);
            if($solicitud[0]['paso'] != 2 && $verificacion) {
                $data = ['telefono'=>$new_telefono];
                if($this->solicitud_model->edit($id_solicitud, $data)){
                    $response['status']['ok']	 = TRUE;
                    $response['mensaje'] 	 = 'Teléfono actualizado ';
                } else{
                    $response['status']['ok']	 = FALSE;
                    $response['mensaje'] 	 = 'El teléfono no pudo ser actualizado';
                }
            }else{
                $response['status']['ok']	 = FALSE;
                $response['mensaje']	 = 'El teléfono que intenta actualizar ya está asociado a otro cliente';
            }
        }else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje']	 = 'sin solicitud';
        }

        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);
    }
    
    public function Anular_telefono_post(){
        $id_solicitud = $this->post('id_solicitud');
        $solicitud = $this->solicitud_model->getSolicitud($id_solicitud)[0];
        if(!empty($solicitud)){
            if(substr(trim($solicitud['telefono']), 0, 2) != '11') {
                $data = ['telefono'=> '11'.trim($solicitud['telefono'])];
                $data2 = ['numero'=> '11'.trim($solicitud['telefono'])];
                $telefonos = $this->solicitud_model->get_agenda_personal_solicitud(['documento' => $solicitud['documento'], 'numero' => trim($solicitud['telefono'])]);
                if($this->solicitud_model->edit($id_solicitud, $data)){
                    foreach ($telefonos as $key => $value) {
                        $this->solicitud_model->update_telefono_solicitante($value['id'], $data2);
                    }
                    $response['status']['ok']	 = TRUE;
                    $response['mensaje'] 	 = 'Teléfono Anulado ';
                } else{
                    $response['status']['ok']	 = FALSE;
                    $response['mensaje'] 	 = 'El teléfono no pudo ser Anulado';
                }

            }else{
                $response['status']['ok']	 = FALSE;
                $response['mensaje']	 = 'El teléfono que intenta actualizar ya está anulado';
            }
        }else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje']	 = 'sin solicitud';
        }

        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);
    }


    public function aplicar_descuento_post(){

        $id_cuota = $this->post('id_cuota', TRUE);
        $descuento = $this->post('descuento', TRUE);
        $desc_id = 0;
        $fecha = date('Y-m-d');
        $credito = $this->credito_model->get_creditos_cliente(['id_cuota' => $id_cuota ]);
        $solicitud = $this->solicitud_model->getSolicitudesBy(['id_credito'=> $credito[0]['id_credito']]);


        //si el credito tiene descuento lo buscamos
        $desc = $this->pago_credito_model->get_descuento($id_cuota);
        if(isset($desc->id))
            $desc_id = $desc->id;
        

        $monto_cobrar = floatval($credito[0]['monto_cobrar']);
        //$resp = $this->credito_model->get_creditos_cliente(['id_cuota' => $id_cuota ]);
        $monto_descuento = floatval($monto_cobrar)-floatval($descuento);
        if(!is_null($descuento) && floatval($descuento) >=0 && !is_null($id_cuota)){
            $data=[
                'descuento' => $descuento,
                'monto_cobrar' => $monto_descuento
            ];
            $params=[
                'id' => $id_cuota
            ];
            $resp = $this->credito_detalle_model->update($params, $data);
            
            if ($resp > 0){ 
                
                $data = array(
                    'id_detalle_credito' => $id_cuota,
                    'fecha' => date('Y-m-d H:i:s'),
                    'monto' => $descuento,
                    'medio_pago' => 'descuento',
                    'tipo_pago' => 'descuento',
                    'fecha_pago' => $fecha.' 23:59:59',
                    'estado' => 1,
                    'estado_razon' => 'Aprobada',
                    'referencia_interna' => $this->session->userdata['idoperador'],
                );

                $accion = "UPDATE";
                if ($desc_id > 0) {
                    $resp = $this->pago_credito_model->update_pago_credito(['id_pago' =>$desc_id], $data);
                    $id =   $desc_id;
                } else{
                    //insert en pago_credito_model
                    $resp = $this->pago_credito_model->insert_pago($data);
                    $accion = "INSERT";
                    $id = $resp;
                }
                
                if ($resp > 0){
                    $res = $this->reprocesar_credito(["id_credito" => $credito[0]['id_credito']]);
                    
                    if($res->success){
                        $response['status']['ok']	 = TRUE;
                        $response['mensaje'] 	 = 'Descuento aplicado';
                        $response['fecha'] 	 = date('d-m-Y');
                        $response['credito'] 	 = $credito[0]['id_credito'];
                        $response['cuota']      = $credito[0]["id"];
                        $response['monto_anterior'] 	 = $monto_cobrar;
                        $response['descuento'] 	 = $descuento;
                        $response['total'] 	 = $monto_descuento;
                        $response['operador'] 	 = $this->session->userdata('idoperador');
                        $response['solicitud'] 	 = $solicitud[0]->id;

                        $data = array( 
                            'id_operador'=>$this->session->userdata("idoperador"),
                            'id_registro_afectado'=>$id,
                            'tabla'=> 'pago_credito',
                            'detalle'=> '[DESCUENTO] Datos :'.json_encode($response),
                            'accion'=> $accion,
                            'fecha_hora'=> date("Y-m-d H:i:s")
                        );
                        $track = $this->operadores->track_interno($data);
            
                    }else{
                        $response['status']['ok'] = FALSE;
                        $response['message'] = 'Descuento aplicado, pero no fue posible actualizar el desglose.';
                        
                    }
                    
                }else{
                    $response['status']['ok'] = FALSE;
                    $response['message'] = 'Descuento no registradocomo pago.';
                }
            } else{
                $response['status']['ok']	 = FALSE;
                $response['mensaje']	 = 'El descuento no pudo ser aplicado';
            }
        } else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje']	 = 'La informacion suministrada no es valida';
        }

        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);

    }

    public function reimputar_pagos_post(){
        //var_dump($this->input->post());
        $desglose = $this->input->post('desglose');
        $fecha = $this->input->post('fecha_pago');
        $monto = $this->input->post('monto_pago');

        if($this->_validate_pagos()){
            //actualizamos la fecha de pago en la tabla pagos_creditos
            
            $params = [
                'id_pago' => $this->input->post('id_pago')
            ];
            $data = [
                'fecha_pago' => $this->input->post('fecha_pago'),
                'monto' =>$monto
            ];
            $resultPago = $this->pago_credito_model->update_pago_credito($params, $data);
            
            if($resultPago > 0){

                $data = array( 
                        'id_operador'=>$this->session->userdata("idoperador"),
                        'id_registro_afectado'=>$this->input->post('id_pago'),
                        'tabla'=> 'pago_credito',
                        'detalle'=> '[Ajuste de Pago] Datos :'.json_encode($data),
                        'accion'=> "UPDATE",
                        'fecha_hora'=> date("Y-m-d H:i:s")
                    );
                $track = $this->operadores->track_interno($data);

                $res = $this->reprocesar_credito(["id_credito" => $this->input->post('id_credito')]);

                $response['status']['ok'] = TRUE;
                $response['message'] = 'Pago actualizado';
            } else{
                $response['status']['ok'] = FALSE;
                $response['message'] = 'No fue posible actualizar el pago';
            }
            

        }else{
           
            $response['status']['ok'] = FALSE;
            $response['message'] = "Campos invalidos";
				
        }

        
        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);

    }




    public function reprocesar_credito_get($credito){
        //var_dump($this->input->post());
        //$credito = $this->input->post('id_credito');
        $solicitud = $this->solicitud_model->getSolicitudesBy(['id_credito'=> $credito]);
        
        $data =[
            'id_credito' => $credito,
        ];
        $res = $this->reprocesar_credito($data);
        if($res->success){
            $response['status']['ok'] = TRUE;
            $response['message'] = 'Credito procesado';
            $response['cliente'] = $solicitud[0]->id_cliente;
            $response['solicitud'] = $solicitud[0]->id;
            $response['credito'] = $solicitud[0]->id_credito;
            $response['operador'] = $this->session->userdata('idoperador');
            $response['fecha'] = date('d-m-Y');

            $data = array( 
                'id_operador'=>$this->session->userdata("idoperador"),
                'id_registro_afectado'=>$solicitud[0]->id_cliente,
                'tabla'=> 'detalle_Credito',
                'detalle'=> '[REPROCESO] Datos :'.json_encode($response),
                'accion'=> "UPDATE",
                'fecha_hora'=> date("Y-m-d H:i:s")
            );
            $track = $this->operadores->track_interno($data);

        }else{
            $response['status']['ok'] = FALSE;
            $response['message'] = 'No fue posible actualizar el desglose.';
            
        }
                

        
        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);

    }
    
    public function agregar_desglose_pago_post(){

        if($this->_validate_pagos_desglose()){
            $data = [
                'id_credito_detalle' => $this->input->post('cuota', TRUE),
                'id_pago_credito' => $this->input->post('pago', TRUE) ,
                'monto' => $this->input->post('monto', TRUE),
                'tipo' => $this->input->post('tipo', TRUE)
            ];
            $result = $this->pago_credito_model->insert_desglose_pago($data);
            if ($result > 0){
                $resp = $this->credito_model->get_creditos_cliente(['id_credito' => $this->input->post('id_credito') ]);
                $data =[
                    'id_credito' => $resp[0]['id_credito'],
                ];
                $this->reprocesar_credito($data);
                $response['status']['ok'] = TRUE;
                $response['message'] = 'Pago registrado';
            } else{
                $response['status']['ok'] = FALSE;
                $response['message'] = "No se pudo registrar el pago";
            }

        }else{
            
            $response['status']['ok'] = FALSE;
            $response['message'] = "campos invalidos";
                
        }

        $status = parent::HTTP_OK;
 		$response['status']['code']  = $status;
		$this->response($response, $status);
    }

/*********************************/

    private function _actualizar_chatbot($documento){
            
        $chats = $this->chat->get_chats_agenda($documento);
        $endPoint = CHATBOT_URL."chatbot/chatbot_live/update_estatus/";
        if(!empty($chats)){
            foreach ($chats as $key => $value) {
                $data = [
                    "id_chat" => $value->id,
                    "clave" => CHATBOT_KEY,
                    "update" => 1
                ];
                $response = $this->curl($endPoint, "POST", $data);		
            }
        }
    }

    private function reprocesar_credito($data){

        $endPoint = "https://testmediospagos.solventa.co/maestro/CronCreditos/reprocesar";
        if(ENVIRONMENT != 'development'){
            $endPoint = URL_MEDIOS_PAGOS."maestro/CronCreditos/reprocesar";
        }
                
        $curl = curl_init();
		$options[CURLOPT_URL] = $endPoint;
        $options[CURLOPT_POSTFIELDS] = $data;
		$options[CURLOPT_CUSTOMREQUEST] = 'POST';
		$options[CURLOPT_RETURNTRANSFER] = TRUE;
		$options[CURLOPT_ENCODING] = '';
		$options[CURLOPT_MAXREDIRS] = 10;
		$options[CURLOPT_TIMEOUT] = 30;
		$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

		curl_setopt_array($curl,$options);

		$res = json_decode(curl_exec($curl));
		$err = curl_error($curl);
		curl_close($curl);

		if($res->success){
			$response = $res;
		} else {
            $response = null;
        }
		if ($err)
		{
		  echo 'cURL Error #:' . $err;die;
		}
		return $response;
    }

    private function verificar_paso_solicitud($paso, $estado){
        $pasos_posibles     = [5,6,8,10,13,16]; //pasos a los que puede cambiar una solicitud
        $pasos_habilitados  = [];   //pasos a los que puede cambiar una solicitud segun el estado en el que se encuentra
        $estados_bloqueo    = ['PAGADO', 'TRANSFIRIENDO','APROBADO', 'RECHAZADO']; //estados que no permiten un cambio de paso
        
        if (!in_array($estado, $estados_bloqueo) && $paso != 2) {
            if($paso < 13){
                $pasos_habilitados  = [5,6,8,10];
            } else {
                $pasos_habilitados  = [5,6,8,10,13,16];
            }
        }
        return $pasos_habilitados;
    }
    private function verificar_estado_solicitud($estado, $paso, $alta){
        $now = time(); // or your date as well
        $date = strtotime($alta);
        $datediff = $now - $date;

        $datediff =  round($datediff / (60 * 60 * 24));

        if($datediff > 30 && (!in_array($estado, ['PAGADO', 'TRANSFIRIENDO','APROBADO', 'RECHAZADO']) || is_null($estado))){
            return $estados_habilitados = ['ANULADO'];
        }

        $estados_posibles     = ['ANALISIS', 'VERIFICADO', 'VALIDADO', 'RECHAZADO']; //estado a los que puede cambiar una solicitud
        $estados_habilitados  = [];   //estados a los que puede cambiar una solicitud segun el estado en el que se encuentra
        $estados_bloqueo    = ['PAGADO', 'TRANSFIRIENDO']; //estados que no permiten un cambio de estado
        
        if (!in_array($estado, $estados_bloqueo) && $paso != 2) {
                $estados_habilitados  = $estados_posibles;
        }
        return $estados_habilitados;
    }

    private function verificar_situacion_solicitud($estado, $paso){
       
        $situacion_posible  = [];   //situacion a las que puede cambiar una solicitud segun el estado en el que se encuentra
        $estados_bloqueo    = ['PAGADO', 'APROBADO', 'TRANSFIRIENDO']; //estados que no permiten un cambio de situacion
        
        if (!in_array($estado, $estados_bloqueo) && $paso != 2) {
            $situacion = $this->solicitud_model->get_situacion_laboral(['estado' => '1', 'id_notIn' => '2,6']);
            $situacion_posible = $situacion;
        }
        return $situacion_posible;
    }

    private function verificacion_telefono($telefono, $documento) {
        $verificacion = $this->solicitud_model->verificacion_telefono($telefono, $documento);
        return $verificacion;
    }

    private function _validate_pagos()
    {
        
        $this->form_validation->set_rules('fecha_pago', 'Fecha Pago', 'required');
        $this->form_validation->set_rules('id_pago', 'ID-PAGO', 'required');
        $this->form_validation->set_rules('id_credito', 'ID-CREDITO', 'required');
        $this->form_validation->set_rules('monto_pago', 'Monto de Pago', 'required');
        
		$this->form_validation->set_message('required', 'El campo %s no esta definido');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

    private function _validate_pagos_desglose()
    {
        
        $this->form_validation->set_rules('monto', 'Monto Cuota', 'required');
        $this->form_validation->set_rules('cuota', 'Cuota', 'required');
        $this->form_validation->set_rules('tipo', 'Tipo', 'required');
        $this->form_validation->set_rules('pago', 'ID-PAGO', 'required');
        $this->form_validation->set_rules('id_credito', 'ID-CREDITO', 'required');
    
		$this->form_validation->set_message('required', 'El campo %s no esta definido');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

    public function update_datospers_post(){
        $datos = $this->post();
        $id_solicitud = $datos['id'];
        $solicitud = $this->solicitud_model->getSolicitud($id_solicitud)[0];


        $response['resp'] = $this->solicitud_model->edit($id_solicitud, $datos);

        if(!empty($response['resp'])){
            $response['status']['ok']	 = TRUE;
            $response['mensaje'] 	 = 'Solicitud actualizada';            
            $response['datos']['old']['nombre']  = $solicitud['nombres'];
            $response['datos']['old']['apellido']  = $solicitud['apellidos'];
            $response['datos']['new']['nombre']  = $datos['nombres'];
            $response['datos']['new']['apellido']  = $datos['apellidos'];
           
        }else{
            $response['status']['ok']	 = FALSE;
            $response['mensaje'] 	 = 'La solicitud no pudo ser actualizada';
        }
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $this->response($response, $status);
    }

    private function curl($endPoint, $method = 'POST',  $params=[] ){
        //PENDIENTE REEMPLAZAR POR LA LIBRERIA REQUEST.
        $token = $this->session->userdata('token');
        $curl = curl_init();
        $options[CURLOPT_HTTPHEADER] = ['Authorization:'.$token];
        $options[CURLOPT_POSTFIELDS] = $params;
        $options[CURLOPT_URL] = $endPoint;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 300;
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
