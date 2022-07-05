<?php

class Galery extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// MODELS
		$this->load->model('galery_model','',TRUE);
		$this->load->model('solicitud_m','solicitud_model',TRUE);
        $this->load->helper(['jwt', 'authorization']); 

	}

	public function search($params=[])
	{
		return  $this->galery_model->search($params);
	}
	
	public function images()
	{
		$this->output->enable_profiler(TRUE);
		$data=[];
       /* $data['tracks'] = $this->order_tracker($this->search());
        $data['actions'] = [];
        foreach ($this->operaciones_model->search(['estado'=>1]) as $key => $action)
        {
        	array_push($data['actions'],$action);
        	if($action['idgrupo_respuesta'] !=0)
        	{
				$data['actions'][$key]['options'] = $this->operaciones_model->search_reasons(['idgrupo_respuestas'=>$action['idgrupo_respuesta']]);
        	}
        }
        $data['reasons'] =  $this->operaciones_model->search_reasons(['estado'=>1]);*/
		$this->load->view('layouts/adminLTE',$data);
        $this->load->view('gestion/box_galery', $data);
	}

        public function actualizarDatos(){
            $id_solicitud = $this->input->post('id_solicitud');            
            $end_point    = APIBURO_URL."api/TransUnion/consulta_incomevalidator_actuliza_laboral?id_solicitud=$id_solicitud";
            $request      = Requests::get($end_point, array(),array());
            $response     = json_decode($request->body); 
            $resultado    = $response->data->resultado;
            //$this->galery_model->actualizar_datos($id_solicitud,$resultado);
            echo json_encode(["update_datos"=>$resultado]);            
        }
        
        
        public function actualizarNumeroCuenta()
        {
            $id_solicitud       = $this->input->post('id_solicitud', true);
            $id_encript =  AUTHORIZATION::encodeData( $id_solicitud  );

            $numero_cuenta      = $this->input->post('numero_cuenta');
            $id_banco           = $this->input->post('id_banco');
            $id_tipo_cuenta     = $this->input->post('id_tipo_cuenta');
            $banco_antiguo      = $this->input->post('banco_ant');
            $tipo_cta_antiguo   = $this->input->post('tipo_cuenta_ant');
            $nro_cuenta_antiguo = $this->input->post('numero_cuenta_ant');
            $id_operador        = $this->input->post('id_operador');
            $buro               = $this->input->post('buro'); 
            $fecha = date_to_string(date('Y-m-d'), 'd F a');
            $hora  = date('h:i A', strtotime(date('H:i')));
            $operador    = $this->input->post('nombre_operador');
            $banco       = $this->input->post('nombre_banco');
            $tipo_cuenta = $this->input->post('nombre_tipo_cuenta');
            $tipo_solicitud ='';
            $verificacion_manual = $this->input->post('verificacion_manual');
            $fecha_apertura = $this->input->post('fecha_apertura');

            $solicitud = $this->solicitud_model->getSolicitud($id_solicitud);
            $analisis = $this->solicitud_model->getSolicitudAnalisis(['id' => $id_solicitud]);
            
            if(!empty($solicitud) ){
                $tipo_solicitud = $solicitud[0]['tipo_solicitud'];
            }

            $respuesta = "RECHAZADO";


            

            /*** Si Viene de validar cuenta en Ajuste ***/
            if ($verificacion_manual) {
                $respuesta = "APROBADO";

                //track
                $razon = 'CUENTA CON VALIDACION MANUAL';
                $observaciones = "<strong>[VALIDACION MANUAL DE CUENTA]:</strong><br>"
                . "<strong>Nueva cuenta:</strong> ".$numero_cuenta. "<br>"
                . "<strong>Banco: </strong>" . $banco. "<br>"
                . "<strong>Tipo Cuenta: </strong>" . $tipo_cuenta . "<br>"
                . "<strong>Fecha de apertura: </strong>" . date_to_string($fecha_apertura) . "<br>"
                . "<strong>Fecha y hora validación: </strong>" . date("d/m/Y H:i:s") . "<br>"
                . "-----------------------------------------<br>"
                . "<strong>Cuenta Anterior:</strong> ".$nro_cuenta_antiguo. "<br>"
                . "<strong>Banco: </strong>".$banco_antiguo. "<br>"
                . "<strong>Tipo Cuenta: </strong>".$tipo_cta_antiguo;
                
                
                if(is_null($solicitud[0]["respuesta_analisis"]) || $solicitud[0]["respuesta_analisis"] == ""){
                    
                    if(!empty($analisis) && $analisis[0]["respuesta"] == "APROBADO"){
                        // llamos a los servicios del buro
                        /** PENDIENTE ENDPOINT 
                         * Si el endpoint devielve APROBADO*/
                        $endPoint = APIBURO_URL."api/AnalisisCrediticio/completarBuro";
                        $resp = $this->consultarBuros($endPoint, 'POST', ["id_solicitud" => $solicitud[0]['id']] );
                        if(!is_null($resp)){
                            
                            if ($resp->data->buro_completo == "APROBADO") {
                                $this->solicitud_model->edit($id_solicitud, ['paso' => 2]);
                            } else {
                                $this->solicitud_model->edit($id_solicitud, ['paso' => 18]);
                            }
                        }
                    }
                    
                } else {
                    if($solicitud[0]["respuesta_analisis"] == "APROBADO"){
                        if ($solicitud[0]['paso'] == 8) {
                            $this->solicitud_model->edit($id_solicitud, ['paso' => 9]);
                        }
                        $hoy = new DateTime;
                        $fecha_apertura = new DateTime($fecha_apertura);
                        $diferencia = $hoy->diff($fecha_apertura);
                        $meses = ($diferencia->y * 12) + $diferencia->m;
                        $this->solicitud_model->actualizarAntiguedadCuenta($id_solicitud, ['antiguedad_cuenta_bancaria' => $meses]);
                        
                    }
                }
                $this->galery_model->actualizar_solicitud($id_solicitud,$numero_cuenta,$id_banco,$id_tipo_cuenta, $tipo_solicitud, $razon);
                $this->galery_model->actualizar_cuenta_maestro($id_solicitud,$numero_cuenta,$id_banco,$id_tipo_cuenta, $tipo_solicitud, $razon);
            } else {
                $razon = null;
                $observaciones = "<strong>Nueva cuenta:</strong> ".$numero_cuenta. "<br>"
                . "<strong>Banco: </strong>".$banco."<br>"
                . "<strong>Tipo Cuenta: </strong>".$tipo_cuenta."<br>"
                . "-----------------------------------------<br>"
                . "<strong>Cuenta Anterior:</strong> ".$nro_cuenta_antiguo. "<br>"
                . "<strong>Banco: </strong>".$banco_antiguo. "<br>"
                . "<strong>Tipo Cuenta: </strong>".$tipo_cta_antiguo. "<br>";
                
                //var_dump($tipo_solicitud);die;
                if($tipo_solicitud != 'RETANQUEO' ){
                    if($buro == "DataCredito"){
                        $endPoint =  APIBURO_URL."api/Datacredito/consulta_cbu";
                        $resp = $this->consultarBuros($endPoint, 'POST', ["id_solicitud"=>$id_encript] );
                            if (!is_null($resp)) {
                                $respuesta =    $resp->data->resultado;
                            }
                    } else if($buro == "TransUnion"){
                        
                        $paso = 9;
                        if(is_null($solicitud[0]["respuesta_analisis"]) || $solicitud[0]["respuesta_analisis"] == ""){
                            $endPoint =  APIBURO_URL."api/AnalisisCrediticio/consulta_pecoriginacion_cbu";
                            
                            if(is_null($solicitud[0]["id_usuario"]) && is_null( $solicitud[0]["email"]))
                                $paso = 2;

                            $resp = $this->consultarBuros($endPoint, 'POST', ["id_solicitud"=>$id_encript] );
                            if(!is_null($resp)){                            
                                if($resp->data->cbu == "APROBADO"){
                                    $respuesta = "APROBADO";
                                    if ($resp->data->buro_completo == "APROBADO") {
                                            if($solicitud[0]['paso'] == 8)
                                                $this->solicitud_model->edit($id_solicitud, ['paso' => $paso]);
                                    } else {
                                        $this->solicitud_model->edit($id_solicitud, ['paso' => 18]);
                                    }
                                }
                                
                            }
                            
                        }else{
                            $endPoint =  APIBURO_URL."api/TransUnion/consulta_pecoriginacion_cbu";
                            $resp = $this->consultarBuros($endPoint, 'POST', ["id_solicitud"=>$id_encript] );
                            if (!is_null($resp)) {
                                $respuesta =    $resp->data->resultado;
                            }
                        }
                        

                    }
                }else{
                    $respuesta = 'RECHAZADO';
                }    
                $this->galery_model->actualizar_solicitud($id_solicitud,$numero_cuenta,$id_banco,$id_tipo_cuenta, $tipo_solicitud, $razon);
                $this->galery_model->actualizar_cuenta_maestro($id_solicitud,$numero_cuenta,$id_banco,$id_tipo_cuenta, $tipo_solicitud, $razon);
            }

            $tracker_url = base_url()."api/ApiTracker/save";
            $data_track = [
                "id_solicitud" => $id_solicitud,
                "id_operador" => $id_operador,
                "observaciones" => $observaciones."<br><b>Validacion de Cuenta: $respuesta</b>",
                "id_tipo_gestion" => 14               
            ];             
            
            $data_insert = [
                "id_solicitud" => $id_solicitud,
                "operador" => $operador,
                "observaciones" => $observaciones."<br><b>Respuesta: $respuesta</b>",
                "id_tipo_gestion" => 14,
                "fecha_string" =>$fecha,
                "fa_icon" => "fa-retweet",
                "color" => "blue",
                "hora" => $hora,
                "descripcion" => "Actualizacion de cuenta"
            ];
            
            $request_track_edit = Requests::post($tracker_url, array(),$data_track);
            $track = json_decode($request_track_edit->body);
            echo json_encode(["track"=>$data_insert,"update_cbu"=>$respuesta]);
        }
        
        public function verificar(){
            $id_ref       = $this->input->post('id_ref');
            $verificacion = $this->input->post('verificacion');
            $id_solicitud = $this->input->post('id_solicitud');
            $id_operador  = $this->input->post('id_operador');
            $tipo         = $this->input->post('referencia_tipo');
            $response     = "";
            if($this->galery_model->verificar($id_ref,$verificacion)){
                $response = "Referencia verificada";
            } else {
                $response = "Error al verificar referencia";
            }    
            //Trackear
            $tracker_url = base_url()."api/ApiTracker/save";
            $data_track = [
               "id_solicitud" => $id_solicitud,
               "id_operador" => $id_operador,
               "observaciones" => 
               "<strong>Tipo: </strong> ".$tipo. "<br>"
               . "<strong>Estado: </strong> ".$verificacion,
               "id_tipo_gestion" => 15               
            ];
            $fecha = date_to_string(date('Y-m-d'), 'd F a');
            $hora  = date('h:i A', strtotime(date('H:i')));
            $operador = $this->input->post('nombre_operador');
            $data_insert = [
                "id_solicitud" => $id_solicitud,
                "operador" => $operador,
                "observaciones" =>  
                "<strong>Tipo: </strong> ".$tipo. "<br>".
                "<strong>Estado: </strong>". $verificacion,
                "id_tipo_gestion" => 15,
                "fecha_string" =>$fecha,
                "fa_icon" => "fa-group",
                "color" => "aqua",
                "hora" => $hora,
                "descripcion" => "Verificacion de Referencia"
            ];
            
            $request_track_edit = Requests::post($tracker_url, array(),$data_track);
            $track = json_decode($request_track_edit->body);
            echo json_encode(["track"=>$data_insert,"response"=>$response]);
        }
        
        public function actualizarPagado(){
            $id_solicitud = $this->input->post('id_solicitud');    
            $pagado = $this->galery_model->updatePagado(['id_solicitud'=>$id_solicitud]);
            echo $pagado;               
        }       
        
        private function consultarBuros($endPoint, $method = 'POST',  $params=[] ){
            $curl = curl_init();
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

        public function setRotation() {
            $data = $this->input->post();
            $pagado = $this->galery_model->update_image(['id'=>$data['sid']], ['rotation'=>$data['grados']]);
            echo json_encode($pagado);
        }
}