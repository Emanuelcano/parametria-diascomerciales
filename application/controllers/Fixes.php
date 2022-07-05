<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;


class Fixes extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
        // MODELS
        $this->load->model('tracker_model','',TRUE);
        $this->load->model('Fixes_model','fixes_model',TRUE);
        
        // LIBRARIES
        $this->load->library('form_validation');
    }
    
    public function delete_last_track_repetidas()
    {
        if (!is_cli()) {
            //show_404();
        }
        //$id_solicitud = $this->get('idSol');
        $solicitudes = $this->fixes_model->get_last_track_repetidas();
        //print_r($solicitudes);
        
        foreach ($solicitudes as $key => $value) {
            set_time_limit(0);
            $ultimas_gestiones = $this->fixes_model->get_last_track(['id_solicitud'=> $value->id_solicitud]);
            if(!empty($ultimas_gestiones)){
                array_shift($ultimas_gestiones);
                $ids = implode(",",array_column($ultimas_gestiones, 'id'));
                $result= $this->fixes_model->delete_last_track(['id'=> $ids]);
            }

        }
        die;

    }

    public function migrar_mails_solicitudes_agenda(){
        if (!is_cli()) {
            //show_404();
        }
        $documentos = $this->fixes_model->get_mails_solicitudes();
        //$documentos = array_unique(array_column($documentos, 'documento'));
        ini_set("memory_limit",-1);
        foreach ($documentos as $key => $value) {
            set_time_limit(0);
            $solicitudes_mails = $this->fixes_model->get_agenda_mail_solicitudes($value->documento);
            if(!empty($solicitudes_mails)){
                
                foreach ($solicitudes_mails as $key2 => $value2) {
                    //el email que vamos a insetar ya existe?
                    $is_in = $this->fixes_model->email_registrado($value2->documento, trim($value2->email));

                    if (!$is_in) {
                        //la cuenta es cuenta de inicio de sesion?
                        $is_login = $this->fixes_model->user_email(trim($value2->email));
                        if($is_login){
                            $data = [
                                'documento' => $value2->documento,
                                'contacto'  => $value2->contacto,
                                'fuente'    => "PERSONAL",
                                'cuenta'    => trim($value2->email),
                                'estado'    => 1,
                                'antiguedad'=> 0,
                                'usuario'   => 1
                            ];
                        } else {
                            $data = [
                                'documento' => $value2->documento,
                                'contacto'  => $value2->contacto,
                                'fuente'    => "PERSONAL",
                                'cuenta'    => trim($value2->email),
                                'estado'    => 1,
                                'antiguedad'=> 0,
                                'usuario'   => 0
                            ];
                        }

                        //insert
                        $result = $this->fixes_model->agendar_mail($data);
                        if (!$result) {
                            echo '<br>****FALLO INSERCION****<br>';
                            echo '<pre>';
                            var_dump($data);
                            echo '</pre>';
                        }
                    }
                }
            }
        }
        
        die;
    }

    public function migrar_telefonos_solicitudes_agenda(){
        if (!is_cli()) {
            //show_404();
        }
        $documentos = $this->fixes_model->get_telefono_solicitudes();
        //$documentos = array_unique(array_column($documentos, 'documento'));
        ini_set("memory_limit",-1);
        //por cada documento
        foreach ($documentos as $key => $value) {
            set_time_limit(0);
            //var_dump($value);die;
            $solicitudes_telefonos = $this->fixes_model->get_agenda_telefono_solicitudes($value->documento);
            if(!empty($solicitudes_telefonos)){
                
                //por cada numero personal en solicitudes
                foreach ($solicitudes_telefonos as $key2 => $value2) {
                    
                    //es el numero mas nuevo ingresado?
                    if($key2 == 0){
                    
                        //ponemos es estado 1 activo el numero
                        $data = [
                            'documento'     => $value2->documento,
                            'contacto'      => $value2->contacto,
                            'fuente'        => "PERSONAL DECLARADO",
                            'tipo'          => "MOVIL",
                            'numero'        => trim($value2->telefono),
                            'estado'        => 1,
                            'antiguedad'    => 0,
                            'id_parentesco' => 0,
                            'llamada'       => 1,
                            'sms'           => 1,
                            'whatsapp'      => 1
                        ];
                    } else {
                        $data = [
                            'documento'     => $value2->documento,
                            'contacto'      => $value2->contacto,
                            'fuente'        => "PERSONAL DECLARADO",
                            'tipo'          => "MOVIL",
                            'numero'        => trim($value2->telefono),
                            'estado'        => 0,
                            'antiguedad'    => 0,
                            'id_parentesco' => 0,
                            'llamada'       => 0,
                            'sms'           => 0,
                            'whatsapp'      => 0
                        ];
                    }
                    
                    //si el telefono esta regitrado devielve el id del registro, caso contrario -1
                    $is_in = $this->fixes_model->telefono_registrado($value2->documento, trim($value2->telefono), "PERSONAL DECLARADO");
                    //el numero no esta en la agenda?
                    if ($is_in < 0) {
                        //insert
                        $result = $this->fixes_model->agendar_telefono($data);
                        if (!$result) {
                            echo '<br>****FALLO INSERCION PERSONAL DECLARADO****<br>';
                            echo '<pre>';
                            var_dump($data);
                            echo '</pre>';
                        }
                    } else {
                        //update
                        $result = $this->fixes_model->actualizar_telefono($data, $is_in);
                        /*if (!$result) {
                            echo '<br>****FALLO UPDATE PERSONAL****<br>';
                            echo '<pre>';
                            var_dump($data);
                            echo '</pre>';
                        }*/
                    }
                }

                
            }

            //referencias
            $solicitudes_telefonos = $this->fixes_model->get_agenda_telefono_referencia($value->documento);
            if(!empty($solicitudes_telefonos)){
                //por cada referencia
                foreach ($solicitudes_telefonos as $key2 => $value2) {
                    $parentesco = 'REFERENCIA';
                    //parentesco    
                    
                    if(is_null($value2->id_parentesco)){
                        $parentesco = "LABORAL";
                    }
                    
                    //ponemos es estado 1 actoivo el numero
                    $data = [
                        'documento'     => $value2->documento,
                        'contacto'      => ((!isset($value2->contacto) || is_null($value2->contacto))? "":$value2->contacto),
                        'fuente'        => $parentesco,
                        'tipo'          => "MOVIL",
                        'numero'        => trim($value2->telefono),
                        'estado'        => 1,
                        'antiguedad'    => 0,
                        'id_parentesco' => ((!isset($value2->id_parentesco) || is_null($value2->id_parentesco))? 0:$value2->id_parentesco),
                        'llamada'       => 1,
                        'sms'           => 1,
                        'whatsapp'      => 0
                    ];
                    
                    
                    //si el telefono esta regitrado devielve el id del registro, caso contrario -1
                    $is_in = $this->fixes_model->telefono_registrado($value2->documento, trim($value2->telefono), $parentesco);
                    //el numero no esta en la agenda?
                    if ($is_in < 0) {
                        //insert
                        $result = $this->fixes_model->agendar_telefono($data);
                        if (!$result) {
                            echo '<br>****FALLO INSERCION REFERENCIA****<br>';
                            echo '<pre>';
                            var_dump($data);
                            echo '</pre>';
                        }
                    } 
                }

            }
        }
        
        die;
    }

    private function implode_key($glue, $arr, $key){
        $arr2=array();
        foreach($arr as $f){
            if(!isset($f[$key])) continue;
            $arr2[]=$f[$key];
        }
        // return implode($glue, $arr2);
        return $arr2;
    }
    /*
    * FIX DE CLIENTES DUPLICADOS PASO 1
    */
    public function clientesDuplicados(){
        $data = $this->fixes_model->get_clientesDuplicados();
        var_dump($data);
        foreach ($data as $key => $value) {
            $clientes = $this->fixes_model->get_cliente($value->documento);
            echo "get_cliente <br>";
            var_dump($clientes);


            $arrayClientes = $this->implode_key(",",$clientes,'id');
            $stringCliente = (implode("','",$arrayClientes));

            // echo "get_solicitud <br>";
            $solicitud = $this->fixes_model->get_solicitud($value->documento);
            // var_dump($solicitud);

            // echo "creditosSueltos <br>";
            $creditosSueltos = $this->fixes_model->get_creditosSueltos($value->documento, $stringCliente);
            // var_dump($creditosSueltos);

            foreach ($creditosSueltos as $key2 => $credito) {
                if ($credito->estado == 'vigente' && $credito->suma == '0.00') {
                    echo "Proximo a Eliminar: ". $credito->id. "<br>";
                    echo "dropCreditoDetalle <br>";
                    var_dump($this->fixes_model->dropCreditoDetalle($credito->id)); // CAMBIAR A DELETE CON WHERE
                    echo "dropCreditoCondicion <br>";
                    var_dump($this->fixes_model->dropCreditoCondicion($credito->id)); // CAMBIAR A DELETE CON WHERE
                    echo "dropCreditoDuplicado <br>";
                    var_dump($this->fixes_model->dropCreditoDuplicado($credito->id)); // CAMBIAR A DELETE CON WHERE
                    echo "dropCliente <br>";
                    var_dump($this->fixes_model->dropCliente($credito->id_cliente)); // CAMBIAR A DELETE CON WHERE
                }

                if ($credito->estado == 'cancelado' && $credito->suma != '0.00') {
                    echo "Proximo a Eliminar Cancelado: ". $credito->id. "<br>";
                    $this->fixes_model->updateSolicitud($credito->id_cliente,$credito->id,$solicitud[0]->id);
                    var_dump($this->fixes_model->dropCreditoDetalle($solicitud[0]->id_credito)); // CAMBIAR A DELETE CON WHERE
                    echo "dropCreditoCondicion <br>";
                    var_dump($this->fixes_model->dropCreditoCondicion($solicitud[0]->id_credito)); // CAMBIAR A DELETE CON WHERE
                    echo "dropCreditoDuplicado <br>";
                    var_dump($this->fixes_model->dropCreditoDuplicado($solicitud[0]->id_credito)); // CAMBIAR A DELETE CON WHERE
                    echo "dropCliente <br>";
                    var_dump($this->fixes_model->dropCliente($solicitud[0]->id_cliente)); // CAMBIAR A DELETE CON WHERE
                }
            }
        }
    }

    /*
    * FIX DE CREDITOS DUPLICADOS PASO 2
    */
    public function creditosDuplicados(){
        $data = $this->fixes_model->get_creditosOriginales();
        // var_dump($data);
        $y = 0;
        foreach ($data as $key => $value) {
            // BUSCAMOS LOS ID DE CREDITOS POR CLIENTE
            $creditosDuplicados = $this->fixes_model->get_CreditosDuplicados($value->id_cliente);
            echo " <br>get_cliente: ".$value->id_cliente. "<br>";
            // var_dump($creditosDuplicados);
            // echo $creditosDuplicados[0]->id . " - ";
            // echo $creditosDuplicados[1]->id;

            // BUSCADOS LOS ID DE CREITOS DETALLES PARA BORRARLOS
            $creditosDetalle = $this->fixes_model->get_CreditosDetalle($creditosDuplicados[0]->id,$creditosDuplicados[1]->id);
            // var_dump($creditosDetalle);

            // SI EL ID DE CREDITO ESTA EN LA SOLICITUD
            $solicitud = $this->fixes_model->get_solicitudxCreditoCliente($creditosDuplicados[1]->id, $value->id_cliente);
            if ($solicitud != FALSE){
                // SE ACTUALIZA EN LA SOLICITUD EL ID DEL CREDITO 
                $this->fixes_model->updateSolicitud2($creditosDuplicados[0]->id, $solicitud[0]->id);

                // SE BORRA CREDITO DETALLE
                $this->fixes_model->dropCreditoDetalle2($creditosDuplicados[1]->id);

                // SE BORRA CREDITO 
                $this->fixes_model->dropCredito2($creditosDuplicados[1]->id);

                $y++;
                echo "Credito #". $y." <strong>". $creditosDuplicados[1]->id . " </strong>si existe en solicitud<br>";
                // var_dump($solicitud);
            }else{
                // SE BORRA CREDITO DETALLE
                $this->fixes_model->dropCreditoDetalle2($creditosDuplicados[1]->id);
                echo "Tiene el id_credito correcto la solicitud";
                // SE BORRA CREDITO 
                $this->fixes_model->dropCredito2($creditosDuplicados[1]->id);

            }
        }
    }

    public function update_personal_to_declarado(){
        $result = $this->fixes_model->update_personal_to_declarados();
        var_dump("$result registros actualizados");
        die;
    }

    public function fix_monto_devolver_creditos() {
        $result = $this->fixes_model->getCreditosSinMontoDevolver();
        foreach ($result as $key => $value) {
            $data =[
                'id_solicitud' => $value->id_solicitud,
                'fecha_nueva' => $value->fecha_primer_vencimiento,
                'fecha_otorgamiento' => $value->fecha_otorgamiento 
            ];
        
            $update = $this->fixes_model->updateCreditoEstado($value->id_credito, 'mora');
            var_dump($update);

            $update = $this->fixes_model->updateCreditoDetalleEstado($value->cuota, 'mora');
            var_dump($update);

            $recalcular = $this->curl(base_url().'api/condicion_desembolso/recalcular', 'POST', $data);
            var_dump('********************************************************************************************');
            var_dump($data);
            var_dump($recalcular);

            var_dump('<br>');

            $reprocesar = $this->curl(base_url().'api/ajustes/reprocesar_credito/'.$value->id_credito, 'GET', '');
            var_dump('********************************************************************************************');
            var_dump($value->id_credito);
            var_dump($reprocesar);

            var_dump('<br>');

        }
        die;
    }


    private function curl($endPoint, $method = 'POST',  $params=[] ){
        $curl = curl_init();
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


    public function FallaFechaFront()
    {
        $creditos = $this->fixes_model->get_vencimientoFecha0000("0000-00-00");
        $this->fixes_model->UpdateDataMaestro("creditos","fecha_primer_vencimiento","2021-03-15",$creditos);

        $solicitudes = $this->fixes_model->get_solicitudByStringId($creditos);

        $condicion = $this->fixes_model->get_solicitudCondicion("solicitud_condicion",$solicitudes);
        $this->fixes_model->UpdateDataSolicitudes("solicitud_condicion","fecha_pago_inicial","2021-03-15",$condicion);

        $condicionDesembolso = $this->fixes_model->get_solicitudCondicion("solicitud_condicion_desembolso",$solicitudes);
        $this->fixes_model->UpdateDataSolicitudes("solicitud_condicion_desembolso","fecha_pago_inicial","2021-03-15",$condicionDesembolso);

        $condicionCreditos = $this->fixes_model->get_CreditoCondicion($creditos);
        $this->fixes_model->UpdateDataMaestro2("credito_condicion","fecha_pago_inicial","2021-03-15",$condicionCreditos);

        $creditoDetalle = $this->fixes_model->get_CreditoDetalle($creditos);

        foreach ($creditoDetalle as $key => $value) {

            $this->fixes_model->UpdateDataMaestro("credito_detalle","fecha_vencimiento","2021-03-15",$value->id);

            $endPoint = "https://mediospagos.solventa.co/maestro/CronCreditos/calcular_cuota_fecha?id_credito_detalle=$value->id&fecha=2021-03-15";
            // $endPoint = "http://localhost/medios-de-pago-2.0/maestro/CronCreditos/calcular_cuota_fecha?id_credito_detalle=$value->id&fecha=2021-03-15";                            

            print_r($endPoint);

            $result = $this->_curl_calcular_cuota_fecha($endPoint, $method='GET', $params=[]);

            $data = json_decode($result);

            $capital        = $data->response->desglose->capital;
            $interes        = $data->response->desglose->interes;
            $seguro         = $data->response->desglose->seguro;
            $administracion = $data->response->desglose->administracion;
            $tecnologia     = $data->response->desglose->tecnologia;
            $iva            = $data->response->desglose->iva;

            $actulizarData = array( 'capital'           => $capital,
                                    'interes'           => $interes,
                                    'seguro'            => $seguro,
                                    'administracion'    => $administracion,
                                    'tecnologia'        => $tecnologia,
                                    'iva'               => $iva,
                                    'tecnologia_mora'   => 0,
                                    'interes_mora'      => 0,
                                    'dias_atraso'       => 0,
                                    'multa_mora'        => 0
                                    );

            print_r($data->response->desglose); 

            print_r($this->fixes_model->UpdateDataMaestro3("credito_detalle",$actulizarData, $value->id));

            $endPoint2 = "https://mediospagos.solventa.co/maestro/CronCreditos/reprocesar";

            $result = $this->_curl_calcular_cuota_fecha($endPoint2, $method='POST', $params=["id_credito" => $value->id_credito]);

            // die;
        }
    }
    /*
    * Fix para actualizar el error de total_devolver y dias en 0 en credito_condicion,
    * Actualizar monto_devolver 0 en creditos y
    * Actualizar monto_cuota 0 en credito detalle
    */
    public function MontosDiasEnCero(){
        $data = $this->fixes_model->getDataCero();
        // print_r($data);

        foreach ($data as $key => $value) {
            print_r($value);
            echo "<br/>";
            echo "<br/>UpdateCreditoCondicion: "; print_r($this->fixes_model->UpdateCreditoCondicion($value));
            echo "<br/>UpdateCreditos: "; print_r($this->fixes_model->UpdateCreditos($value));
            echo "<br/>UpdateCreditoDetalle: "; print_r($this->fixes_model->UpdateCreditoDetalle($value));
            echo "<br/>";
        }
        echo "Fin!!";
    }

    /**
     * correccion de acuerdos cumplidos
     */
    public function FixAcuerdosCumplidos()
    {
       
        $clientes = $this->fixes_model->clientesAcuerdosPagosAbril();
        foreach ($clientes as $y => $cliente) {
            var_dump("<br>");
            var_dump("<br>");

            var_dump("*****cliente*****");
            var_dump("<br>");
            var_dump($cliente->id_cliente);
            // Traer todos los creditos del cliente en estado pagado cuyo ultimio cobro fue delpues del 01/04/21
            $credito_detalle = $this->fixes_model->getCreditosDetalleCancelado($cliente->id_cliente);
            
            foreach ($credito_detalle as $k => $val){
                // buscar pagos que concuerden con la fecha de cobro
                var_dump("<br>");
                var_dump("<br>");

                var_dump("*****cuota*****");
                var_dump("<br>");
                var_dump($val->id .' ESTADO '.$val->estado);
                var_dump("*****FECHA COBRA*****");
                var_dump("<br>");
                var_dump($val->fecha_cobro);


                $monto = 0;
                $pagos = $this->fixes_model->pagosParaAcuerdos( $val->id, $val->fecha_cobro);
                if (!empty($pagos) && !is_null($pagos[0]->monto)) {
                   $monto = $pagos[0]->monto;
                }

                var_dump("<br>");
                var_dump("<br>");
                var_dump("*****MONTO0*****");
                var_dump("<br>");
                var_dump($monto);
                // Acuerdo de pagos / planes de pagos
                $acuerdos_detalle = $this->fixes_model->getAcuerdoByClienteFechaNew($cliente->id_cliente, $val->fecha_cobro);
                var_dump("<br>");
                var_dump("<br>");
                var_dump("*****ACUERDOS*****");
                var_dump("<br>");
                var_dump($acuerdos_detalle);
                foreach ($acuerdos_detalle as $k => $acuerdo) {
                        if (!is_null($acuerdo->monto)) {
                            
                            if( $monto > 0 && $val->estado == 'pagado'){
                                $this->fixes_model->updateEstadoAcuerdo($acuerdo->id , $estado = 'cumplido');
                                var_dump('\n Acuerdo: '.$acuerdo->id.' CUMPLIDO \n ' );
            
                            } else{
                                if ( $monto > 0 && floatval($monto) >= floatval(($acuerdo->monto) - ($acuerdo->monto * 0.02)) ) {
                                    $this->fixes_model->updateEstadoAcuerdo($acuerdo->id , $estado = 'cumplido');
                                    var_dump('\n Acuerdo: '.$acuerdo->id.' CUMPLIDO \n' );
                                }
                            }
                        
                        }
                }
            }
        }

    }
    public function prueba(){
        $this->load->model('operaciones/Beneficiarios_model','Beneficiarios_model',TRUE);
        echo $sql = $this->db_maestro->get_compiled_select();die;

        var_dump($this->Beneficiarios_model->detalleBeneficiario(['id_beneficiario' => 1,'nombre_municipio'=>1]));die;
    }


    public function procesarSituacionLaboral(){

        $post = $this->input->post();        


		
                
                $archivo_ruta = 'public/Base con laboral retanqueos morosos 16.11.2021.xlsx';
				
				
				
				$element['patch_imagen'] = $archivo_ruta;
				$element['extension'] = 'xlsx';
				$element['fecha_carga'] = date('Y-m-d H:i:s');
				
				$reader = new Xlsx();
				$reader->setReadDataOnly(true);
				$spreadsheet = $reader->load($element['patch_imagen']);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                
                
				if(count($sheetData) > 0){
                    $registros = count($sheetData);
					$registros_procesados = 0; 
                    
                    for ($i=2 ; $i <= count($sheetData); $i++) { 
                        
                        set_time_limit(0);
                        $documento              = trim($sheetData[$i]['A']);      //VALOR_MOVILIZADO
                        $nombre                 = trim($sheetData[$i]['B']);

                        $cliente = $this->fixes_model->get_cliente($documento);

                        
                    

                        if (empty($cliente)) {
                            echo '<br>';
                            var_dump( 'El dolcumento no esta en la, base de clientes :'. $documento ); 
                            echo '<br>';
                            continue;
                        }

         
					
                        $data = [
                            'id_cliente'                        =>  $cliente[0]["id"],
                            'tipoIdentificacionAportanteId'	    => 0,
                            'numeroIdentificacionAportante'	    => trim($sheetData[$i]['D']),
                            'razonSocialAportante'              =>	trim($sheetData[$i]['E']),
                            'tipoCotizantePersonaNatural'	    => 0,
                            'tiene_salario_integral_actualmente' => 0,	
                            'anoPeriodoValidado'			    => 2021,
                            'mesPeriodoValidado'	            => 11,		
                            'realizoPago'			            => 0,
                            'ingresos'	                        => 0,
                            'promedioIngreso'	                => 0,
                            'mediasIngreso'	                    => 0,
                            'fecha_registro'                    => date('Y-m-d')
                        ];
                            

                        $respuesta = $this->fixes_model->insert_situacion_laboral($data);


                            if(!$respuesta){
                                echo '<br>';
                                var_dump( 'No se inserto el El registro nuemro : '. $i ); 
                                echo '<br>';
                            }
                        

                        
                        

					}
                    
				} else{
					$response['status']['ok'] = FALSE;
					$response['message'] = "Archivo sin registros";
				}


            echo 'proceso terminado';
            die;		
    }
    public function insert_blackList()
    {
        $clientes = $this->fixes_model->clientesRiesgo();
        
        foreach ($clientes as $key => $value) {
            $result = $this->fixes_model->insert_riesgo_crediticio($value);
            echo '<br>';
            var_dump($result);
            echo '<br>';
        }
        die;
    }

    public function fixTrackComprobante(){

        $track = $this->fixes_model->getTrackBug();
        $result = [];
        $lista_completa = "";
        foreach ($track as $key => $value) {
            $str = $value->observaciones;
            $find = '<a href="public/supervisores/comprobantes/';
            $new_str  = str_replace($find, '<a href="'.URL_BACKEND.'public/supervisores/comprobantes/', $str);
            //var_dump($new_str);die;
            //var_dump($str_new);die;
            $result[$value->id_solicitud]['id'] = $value->id;
            $result[$value->id_solicitud]['updated'] = $this->fixes_model->updateTrackBug($value->id, $new_str);
            $result[$value->id_solicitud]['new_observaciones'] = "'".$new_str."'";
            $lista_completa .= $value->id.",";
            
        }
        print_r(json_encode($result));
        echo "<br>";        
        print_r($lista_completa);
    }

}
