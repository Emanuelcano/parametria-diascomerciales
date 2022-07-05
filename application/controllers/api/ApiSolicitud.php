<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

/**
 *
 */
class ApiSolicitud extends REST_Controller
{      
	private $_free_methods = array('buscar','listar');
	const TIPO_ADMINISTRADOR = ['ADMINISTRADOR'];

	public function __construct()
	{

		parent::__construct();
		$method = $this->uri->segment(3);

		$this->load->library('User_library');
		$this->load->helper(['jwt', 'authorization']); 

		$auth = $this->user_library->check_token();

		if($auth->status == parent::HTTP_OK || in_array($method, $this->_free_methods))
		{
			// MODELS
			$this->load->model('Solicitud_m','solicitud_model',TRUE);
			$this->load->model('tracker_model','tracker_model',TRUE);
			$this->load->model('SolicitudAsignacion_model','solicitud_asignacion',TRUE);
			$this->load->model('galery_model', 'galery_model', TRUE);
			$this->load->model('operadores/Operadores_model', 'operadores_model', TRUE);
			$this->load->model('Cliente_model', 'cliente_model', TRUE);
			$this->load->model('Credito_model', 'credito_model', TRUE);
			$this->load->model('Chat', 'chat', TRUE);
			$this->load->model('Tracker_model', 'tracker_model', TRUE);
			$this->load->model('PagoCredito_model', 'pago_credito_model', TRUE);


			// LIBRARIES
			$this->load->library('form_validation');
			$this->load->library('Infobip_library');

		}else{
			$this->session->sess_destroy();
			$this->response(['redirect'=>base_url('login')],$auth->status);
		}
	}

	public function aprobacion_automatica_no_bancolombia_post(){
		$casos = $this->solicitud_model->get_casos_aprobacion_automatica_no_bancolombia();

		foreach ($casos as $key => $value) {
			$data['estado'] = "APROBADO";
			$data['operador_asignado'] = 108;
			$operador_old = $value->operador_asignado;

			if($this->solicitud_model->edit((int)$value->id, $data))
			{
				if ($operador_old != 108) {
					//obtenemos asignaciones
					/*$asignaciones = $this->solicitud_model->get_asignaciones((int)$value->id);
					$fecha_asignado = explode(' ',$asignaciones[0]->fecha_registro)[0];
					//borramos registro
					$borrar = $this->solicitud_model->delete_asignaciones((int)$value->id);
					//obtenemos  el control de las asignaciones 
					$control = $this->solicitud_model->get_asignaciones_control($operador_old, $fecha_asignado );
					foreach ($control as $key2 => $cont) {
						$info = [
							'asignados' => (int)$cont->asignados - 1,
							'primaria'	=> (int)$cont->primaria - 1,
							'dependientes' => (int)$cont->dependientes - 1,
						];
						$update = $this->solicitud_model->edit_asignaciones_control($operador_old, $fecha_asignado, $info);

					}*/

					//track cambio de  operador con el id y el nombre
					$data_operador = $this->operadores_model->get_lista_operadores_by(['idoperador' => $operador_old]);             

					$dataTrackGestion = [
						'id_solicitud'=>(int)$value->id,
						'observaciones'=>'[REASIGNACION OPERADOR][OPERADOR ANTERIOR]<br>ID operador: ' .$data_operador[0]['idoperador'].'<br>Nombre operador: '.$data_operador[0]['nombre_apellido'], 
						'id_tipo_gestion' => 130,
						'id_operador' => $data['operador_asignado']
					];
					$endPoint =  base_url('api/admin/track_gestion');
					//Remember pasarlo al controller  -> verificar el paso de dataTrackGestion al controller.
					$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);

				}

				$dataTrackGestion = [
					'id_solicitud'=>(int)$value->id,
					'observaciones'=>'[APROBADO][AUTOMATICO CASO COMPLETO]<br>Solicitud con aprobación automática por tiempo', 
					'id_tipo_gestion' => 130,
					'id_operador' => $data['operador_asignado']
				];
				//Remember pasarlo al controller  -> verificar el paso de dataTrackGestion al controller.
				$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
			}
		}
		die;
	}

	public function aprobacion_automatica_bancolombia_post(){
		$casos = $this->solicitud_model->get_casos_aprobacion_automatica_bancolombia();
		foreach ($casos as $key => $value) {
			$data['estado'] = "APROBADO";
			$data['operador_asignado'] = 108;
			$operador_old = $value->operador_asignado;

			if($this->solicitud_model->edit((int)$value->id, $data))
			{
				if ($operador_old != 108) {
					//obtenemos asignaciones
					/*$asignaciones = $this->solicitud_model->get_asignaciones((int)$value->id);
					$fecha_asignado = explode(' ',$asignaciones[0]->fecha_registro)[0];
					//borramos registro
					$borrar = $this->solicitud_model->delete_asignaciones((int)$value->id);
					//obtenemos  el control de las asignaciones 
					$control = $this->solicitud_model->get_asignaciones_control($operador_old, $fecha_asignado );
					foreach ($control as $key2 => $cont) {
						$info = [
							'asignados' => (int)$cont->asignados - 1,
							'primaria'	=> (int)$cont->primaria - 1,
							'dependientes' => (int)$cont->dependientes - 1,
						];
						$update = $this->solicitud_model->edit_asignaciones_control($operador_old, $fecha_asignado, $info);

					}*/

					//track cambio de  operador con el id y el nombre
					$data_operador = $this->operadores_model->get_lista_operadores_by(['idoperador' => $operador_old]);             

					$dataTrackGestion = [
						'id_solicitud'=>(int)$value->id,
						'observaciones'=>'[REASIGNACION OPERADOR][OPERADOR ANTERIOR]<br>ID operador: ' .$data_operador[0]['idoperador'].'<br>Nombre operador: '.$data_operador[0]['nombre_apellido'], 
						'id_tipo_gestion' => 130,
						'id_operador' => $data['operador_asignado']
					];
					$endPoint =  base_url('api/admin/track_gestion');
					//Remember pasarlo al controller  -> verificar el paso de dataTrackGestion al controller.
					$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);

				}

				$dataTrackGestion = [
					'id_solicitud'=>(int)$value->id,
					'observaciones'=>'[APROBADO][AUTOMATICO CASO COMPLETO]<br>Solicitud con aprobación automática por tiempo', 
					'id_tipo_gestion' => 130,
					'id_operador' => $data['operador_asignado']
				];
				//Remember pasarlo al controller  -> verificar el paso de dataTrackGestion al controller.
				$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
			}
		}
		die;
	}

	public function save_post()
	{

	}

	public function search_post() 
	{
		$id_operador = $this->session->userdata['tipo_operador'];
		$params = [];
		// Seteo los parametros de busqueda
		$params = array_merge($params, $this->_set_search($this->input->post('search'), $this->input->post('criterio')));
		// Seteo los parametros de busqueda de estado
		if ($this->input->post('estado') == 'ANALISIS' && $id_operador == 1 && $this->input->post('operador_asignado') !== null){	
			$params['LITERAL2'] = "(solicitud.estado = 'ANALISIS' and solicitud.operador_asignado = ".$this->input->post('operador_asignado').")";
		} else {
			$params = array_merge($params, $this->_set_status($this->input->post('estado')));
		}

		$visado = null;
		if($this->session->userdata('tipo_operador') == '11' && $this->input->post('estado') == 'VISADO' ){
			$params['VISADO'] = 1;
			$visado = true;

		}
		if($this->session->userdata('tipo_operador') == '11' && $this->input->post('estado') == 'NOVISADO' ){
			$params['NOVISADO'] = 1;
			$visado = true;
		}
		//
		
		// Rango de fechas
		$dates = explode('|',$this->input->post('date_range'));
		$date_start = isset($dates[0])?$dates[0]:NULL;
		$date_end 	= isset($dates[1])?$dates[1]:NULL; 

		if($visado && !is_null($date_end) && !is_null($date_end)){
			$start 	= date_create_from_format('d-m-Y', trim($date_start));
			$end 	= date_create_from_format('d-m-Y', trim($date_end));
			$start = $start->format('Y-m-d');
			$end = $end->format('Y-m-d');

			$params['visado_validacion']= "solicitud.id IN (SELECT id_solicitud FROM gestion.track_gestion WHERE id_tipo_gestion = 130 AND fecha BETWEEN '".trim($start)."' AND '".trim($end)."')";

		}else {
			$params = array_merge($params, $this->_set_date_range($date_start, $date_end, 'd-m-Y', $visado));
			
		}
		$operator = $this->input->post('operador_asignado');
		
		$search = $this->_set_search($this->input->post('search'), $this->input->post('criterio'));  
		if(isset($operator) && !empty(trim($operator)))
		{
					$params['solicitud.operador_asignado'] = $operator;
		} 
				
				
				
				if(!empty($search) && $id_operador == 1){
					unset($params['solicitud.operador_asignado']);
				}
		$type_solicitud = $this->input->post('tipo_solicitud');
		if(isset($type_solicitud) && !empty(trim($type_solicitud)))
		{
			$params['solicitud.tipo_solicitud'] = $type_solicitud;
		}
		$params['order'] = [['solicitud.id', 'desc'],['solicitud.fecha_ultima_actividad', 'desc']];
				
		$solicitude = $this->solicitud_model->simple_list($params);

		foreach ($solicitude as $key => $solicitud) {
			//$solicitude[$key]['last_track'] = $this->get_last_track($solicitud);
			$date = date_create_from_format('Y-m-d H:i:s',$solicitud['fecha_ultima_actividad']);

			$solicitude[$key]['date_ultima_actividad'] = ($date) ? $date->format('d-m-Y'):'';
			$solicitude[$key]['hours_ultima_actividad'] = ($date) ? $date->format('H:i:s'):'';
		}

		$status = parent::HTTP_OK;
		$response['status']['code']  = $status;
		$response['status']['ok']	 = TRUE;
		$response['solicitude'] 	 = $solicitude;
 
		$this->response($response, $status);
	}

	public function update_post()
	{
		$id = $this->input->post('id_solicitud');
		$estado = strtoupper($this->input->post('estado'));
		$paso = strtoupper($this->input->post('paso'));
		
		if(isset($estado) && !empty($estado))
		{
			$data = $this->input->post();
			$response = $this->_update_status($id, $data);
		}

		if(isset($paso) && !empty($paso))
		{	
			$data = $this->input->post();
			$response = $this->_update_step($id, $data);
		}
		
	}
		
	public function update_image_post()
	{
			$id_solicitud = $this->input->post();
			$end_point = URL_API_IDENTITY."api/jumio/Cron/reprocesar_imagenes?id_solicitud=12";
			$request = Requests::get($end_point, array(),array());
			$response = $request->body;
			$this->response($response);           
	}

	private function _update_status($id, $changes)
	{
		//$id = $this->input->post('id_solicitud');
		//$estado = strtoupper($this->input->post('estado'));
		//$id_operator = $this->input->post('id_operador');
		$estado = isset($changes['estado'])?$changes['estado']:NULL;
		$id_operator = isset($changes['id_operador'])?$changes['id_operador']:NULL;  
		$data_operador = $this->operadores_model->get_lista_operadores_by(['idoperador' => $id_operator]);             
		$solicitude = $this->solicitud_model->getSolicitudes(['id'=>$id]);
		if(!empty($solicitude))
		{
						//Si soy operador el rechazado tendria q ser 3 pero si soy fraude el rechazado tendria que ser 5
						$id_operador   = $this->session->userdata('tipo_operador');
						$tipo_operador = $this->solicitud_model->get_nombre_operador($id_operador);  
						//reasignamos la solicitud y los chat de estasolicitud
						$endPoint = base_url()."api/operadores/asignar_solicitudes";
						
				switch ($estado)
				{
					// Si se verifica la solicitud
					case 'VERIFICADO':
						if ($solicitude[0]['estado'] =="ANALISIS" || ($solicitude[0]['estado'] == "")){
							$data['estado'] = $estado;
							if ($this->session->userdata('tipo_operador') == '1') 
							    $data['operador_asignado'] = $id_operator;
						
							$status = parent::HTTP_OK;
							// Si se pudo actualizar la solicitud.
							if($this->solicitud_model->edit($id, $data)){
									// Variable de control para la respuesta.
									$updated = TRUE;
									$solicitude[0]['estado'] = "VERIFICADO";
									// Envio de SMS
									//$this->load->library('Infobip_library');
									//$this->infobip_library->send_sms($solicitude[0]['id']);
									// Envio de Mail
									//$this->load->library('Sendgrid_library');
									//$this->sendgrid_library->send_sms($solicitude[0]['id']);
							}else{
									// Variable de control para la respuesta.
									$updated = FALSE;
									$response['error'] = 'Error al actualizar la solicitud id='.$id;
							}
						}else{
							$status = parent::HTTP_OK;
							$response['status']['code'] = $status;
							$response['status']['ok'] = FALSE;
							$response['solicitud'] = $solicitude;
							$response['error'] = 'La solicitud se encuentra en estado '.$solicitude[0]['estado'];
						}			 		
						break;
					case 'PAGARE':
						$this->load->library('Deceval_library');
						$response['pagare'] = $this->deceval_library->crear_pagare($solicitude[0]['id']);
						$solicitude[0]['estado'] = "PAGARE";
						
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = FALSE;
						$updated = TRUE;
						break;

					case 'VALIDADO':
						if ($solicitude[0]['estado'] =="VERIFICADO")
						{
							$data['estado'] = $estado;

                            if ($this->session->userdata('tipo_operador') == '1') 
							    $data['operador_asignado'] = $id_operator;							$status = parent::HTTP_OK;

							
							if($this->solicitud_model->edit($id, $data))
							{
								$solicitude[0]['estado'] = "VALIDADO";
								// Variable de control para la respuesta.
								$updated = TRUE;
							}else{
								// Variable de control para la respuesta.
								$updated = FALSE;
								$response['error'] = 'Error al actualizar la solicitud id='.$id;
							}
						}else{
							$status = parent::HTTP_OK;
							$response['status']['code'] = $status;
							$response['status']['ok'] = FALSE;
							$response['solicitud'] = $solicitude;
							$response['error'] = 'La solicitud se encuentra en estado '.$solicitude[0]['estado'];
						}	
						break;
					case 'APROBADO':
						// Para hacer un cambio al estado aprobado, la solicitud tiene que estar en un estado anterior de VALIDADO o ser un usuario ADMINISTRADOR
						if ($solicitude[0]['estado'] =="VALIDADO" || in_array(strtoupper($data_operador[0]['descripcion']), self::TIPO_ADMINISTRADOR))
						{
							$data['estado'] = $estado;
							if ($this->session->userdata('tipo_operador') == '1') 
								$data['operador_asignado'] = $id_operator;
								
							$status = parent::HTTP_OK;

							
							if($this->solicitud_model->edit($id, $data))
							{
								$solicitude[0]['estado'] = "APROBADO";
								// Variable de control para la respuesta.
								$updated = TRUE;
								// Envio de Whatsapp
								$phone_number = PHONE_COD_COUNTRY.$solicitude[0]['telefono'];
								$msg =  '¡Felicitaciones! *Su crédito está APROBADO*, recibirás una notificación cuando te enviemos la transferencia.';
								$endpoint = URL_BACKEND.'comunicaciones/twilio/send_new_message';
								$result = $this->solicitud_model->obtenerChat($solicitude[0]['telefono']);

								if (!empty($result) && $result[0]["status_chat"] == "activo") {
									$params = ['chatID' => $result[0]['id_chat'], 'operatorID' => "108", 'message' => $msg];
									$respuestaEnvio = $this->curl($endpoint, "POST", $params);
									$datos = json_decode($respuestaEnvio);
									if(isset($datos->messages)){
										$response['envio']['status'] = parent::HTTP_OK;
										$response['envio']['mensaje'] = 'Envió realizado correctamente';
									}else{
										$response['envio']['status'] = parent::HTTP_BAD_REQUEST;
										$response['envio']['mensaje'] = 'No se ha podido realizar el envió';
									}
								}else{
									$response['envio']['status'] = parent::HTTP_BAD_REQUEST;
									$response['envio']['mensaje'] = 'El chat no se encuentra activo';
								}


								$dataTrackGestion = [
									'id_solicitud'=>(int)$id,
									'observaciones'=>'[APROBADO]', 
									'id_tipo_gestion' => 130,
									'id_operador' => $id_operator
								];
								$endPoint =  base_url('api/admin/track_gestion');
								//Remember pasarlo al controller  -> verificar el paso de dataTrackGestion al controller.
								$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);

								//la solicitud se puede visar?
								/*$visado = $this->solicitud_model->visado_automatico($id);
								if(!empty($visado)){
									//insertamos visado
									if($this->insertarVisado($id,108,1)){
										$solicitude[0]['estado'] = "VISADO";
										
										$dataTrackGestion = [
											'id_solicitud'=>(int)$id,
											'observaciones'=>'[VISADO]', 
											'id_tipo_gestion' => 7,
											'id_operador' => 108
										];
										$endPoint =  base_url('api/admin/track_gestion');
										//Remember pasarlo al controller  -> verificar el paso de dataTrackGestion al controller.
										$response['trackGestion'] = $this->curl($endPoint, 'POST', $dataTrackGestion);
										
									}
									                                     
								}*/
							}	
						}else{
							$updated = FALSE;
							$status = parent::HTTP_OK;
							$response['status']['code'] = $status;
							$response['status']['ok'] = FALSE;
							$response['solicitud'] = $solicitude;
							$response['error'] = 'La solicitud se encuentra en estado '.$solicitude[0]['estado'];
						}	
						break;
					case 'VISADO':
						$status = parent::HTTP_OK;
						if ($solicitude[0]['estado'] =="APROBADO")
						{
							$data['estado'] = $estado;
							
							if ($this->session->userdata('tipo_operador') == '1')
							$data['operador_asignado'] = $id_operator;
							
							//Envio de Whatsapp
							$result = $this->solicitud_model->obtenerDataVisado($solicitude[0]['id']);
							if(empty($result)){
								$response['envio']['status'] = parent::HTTP_BAD_REQUEST;
								$response['envio']['mensaje'] = 'Solicitud NO visada. Verifique que la fecha del primer pago este correcta';
								$updated = FALSE;
								break;
							}
							$monto_pagar = number_format($result[0]['total_devolver'], 0, ',', '.');
							
+							// $telefono = "+5493884133854";
							$telefono= $solicitude[0]['telefono'];

							$fecha_formateada = date_to_string($result[0]['fecha_primer_pago'], 'L d F a');
							$fechaDividida = explode(" ", $fecha_formateada);
							$dia = $fechaDividida[0];
							$fechaComp = $fechaDividida[1].' de '.$fechaDividida[2].' del '.$fechaDividida[3];
							$msg = "TU DINERO VA EN CAMINO. \n\nRecuerda que*tu cuota a pagar es de $$monto_pagar y vence el día $dia $fechaComp. \n\nPuedes pagarnos por estos medios de pagos recomendados: PSE, EFECTY, CORRESPONSAL BANCARIO o DEPOSITO.\n\nAl momento de pagar, escríbenos por este WhatsApp y te recordaremos todos los medios de pago.\n\n¡Ya eres parte de la familia SOLVENTA!.";
							
							$endpoint = URL_BACKEND.'comunicaciones/twilio/send_new_message';
							$estadoChat = $this->solicitud_model->obtenerChat($telefono);

							if (!empty($estadoChat) && $estadoChat[0]["status_chat"] == "activo") {
								$params = ['chatID' => $estadoChat[0]['id_chat'], 'operatorID' => "108", 'message' => $msg];
							
								$respuestaEnvio = $this->curl($endpoint, "POST", $params);
								$datos = json_decode($respuestaEnvio);
								if(isset($datos->messages)){
									$response['envio']['status'] = parent::HTTP_OK;
									$response['envio']['mensaje'] = 'Envió realizado correctamente';
								}else{
									$response['envio']['status'] = parent::HTTP_BAD_REQUEST;
									$response['envio']['mensaje'] = 'No se ha podido realizar el envió';
								}
							}else{
								$response['envio']['status'] = parent::HTTP_BAD_REQUEST;
								$response['envio']['mensaje'] = 'El chat no se encuentra activo';
							}
							//Fin envio whatsapp

							
							//Inserta datos en tabla visado
							$visado = 1;
							if($this->insertarVisado($id,$id_operator,$visado))
							{								
								$solicitude[0]['estado'] = "VISADO";
								$updated = TRUE;
							//Sacar la fila de la tabla
							} else {
								$updated = FALSE;
							}                                    
						}else{
			
							$response['status']['code'] = $status;
							$response['status']['ok'] = FALSE;
							$response['solicitud'] = $solicitude;
							$response['error'] = 'La solicitud se encuentra en estado '.$solicitude[0]['estado'];
						}	
						break;  
					case 'ESCALADO ANALIZADO':     
							$data['id'] = $id;
							$data['estado'] = $estado;
							if ($this->session->userdata('tipo_operador') == '1') 
							    $data['operador_asignado'] = $id_operator;
							$data['escalado'] = 1;
							$status = parent::HTTP_OK;                                                    
							if($this->updateAnalizado($data)){  
								$solicitude[0]['estado'] = "ESCALADO ANALIZADO";
								$updated = TRUE;
							} else {
								$updated = FALSE;
							}                                                
						break;	    
					case 'RECHAZADO':
							$data['estado'] = $estado;
							$data['paso'] = 18;
							if ($this->session->userdata('tipo_operador') == '1') 
							    $data['operador_asignado'] = $id_operator;
							$status = parent::HTTP_OK;
							// busco que tenga un comentario la gestion de rechazo
							$track_reject = $this->tracker_model->search(['id_solicitud'=>$solicitude[0]['id'],'etiqueta'=>'RECHAZADO']);
							if(empty($track_reject))
							{
								$status = 400;
								$response['error'] = 'La solicitud no tiene comentario de rechazo';
								$updated = FALSE;
							}else
							{
								if($this->solicitud_model->edit($id, $data))
								{
									
									// SOLO SI ES UN OPERADOR DE ANALISTA DE RIESGO - FRAUDE
								   if($tipo_operador[0]["descripcion"] == "FRAUDE")
									{
										$existe_visado=$this->solicitud_model->existeVisado($id);
										$visado = 0;
										if(!empty($existe_visado))
										{
											//Si el archivo existe en la tabla visado la actualiza
											$this->actualizarVisado($existe_visado,$visado);
										} else 
										{
											//Si no existe lo crea
											$this->insertarVisado($id,$id_operator,$visado);   
										}
									}

									$solicitude[0]['estado'] = "RECHAZADO";
									// Variable de control para la respuesta.
									$updated = TRUE;
								}else{
									// Variable de control para la respuesta.
									$updated = FALSE;
								}
							}
						break; 
					 
						default:
						break;
				}
				
				if($updated)
				{
					$response['status']['code'] = $status;
					$response['status']['ok'] = TRUE;
					$response['solicitud'] = $solicitude;
					$response['message'] = 'Se actualizó la solicitud';
					 // Incremento los contadores
					 $this->_upper_control($id_operator, $estado);

					//actualizo el campo del chat_bot
					$this->_actualizar_chatbot($solicitude[0]['documento']);
				}else{
					$response['status']['code'] = $status;
					$response['status']['ok'] = FALSE;
								}			
		} else {
					$status = parent::HTTP_OK;
					$response['status']['code'] = $status;
					$response['status']['ok'] = FALSE;
					$response['error'] = 'La solicitud id='.$id.' no existe';
		}
		$this->response($response, $status);            
	}
	
	private function _update_step($id, $changes)
	{

		$send_sms = FALSE;
		$paso = isset($changes['paso'])?$changes['paso']:NULL;
		$id_operator = isset($changes['id_operador'])?$changes['id_operador']:NULL;
		
		$solicitude = $this->solicitud_model->getSolicitudes(['id'=>$id]);
		if(!empty($solicitude))
		{
			$data['paso'] = $paso;
			if ($this->session->userdata('tipo_operador') == '1') 
				$data['operador_asignado'] = $id_operator;
			
			if($this->solicitud_model->edit($id, $data))
			{
				$this->_actualizar_chatbot($solicitude[0]['documento']);

				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['solicitud'] = $this->solicitud_model->getSolicitudes(['id'=>$id]);
				$response['message'] = 'Se actualizó la solicitud';
				
				/*
				//seteamos el verificador de identidad
				$verificador = $this->galery_model->get_verificador_imagenes(["estado_back" => '1']);
				$data['solicitar_imagenes'] = $verificador;
				if(!$this->solicitud_model->update_solicitud_beneficio($id, $data)){
					$response['message'] = 'Se actualizó la solicitud pero no el verificador de imagen';
				}
				*/
				
				switch ($paso)
				{
					case '13':
						$nombre = explode(' ',$solicitude[0]['nombres']);
						$msg = $nombre[0].', no pudimos verificar tu identidad, para continuar con el desembolso ingresa con tu cuenta aquí solven.me/refos. Solventa.';
						$send_sms = TRUE;

						$result = $this->galery_model->get_veriff_scan_by(['id_solicitud' =>$id]);
						
						if(!empty($result)){
							$integracion = $result[0]->id_integracion;

							//normal
							if($integracion == 1){
								$endPoint = URL_API_IDENTITY."api/veriff/auth/session?id_solicitud=$id&estado=1";
								$response['response'] = $this->curl($endPoint, 'GET', [] );								
								
							}else if($integracion == 2){ 
								//video obligatorio
								$endPoint = URL_API_IDENTITY."api/veriff/auth_video/session?id_solicitud=$id&estado=1";
								$response['response'] = $this->curl($endPoint, 'GET',  [] );

							} else{
								$response['message'] = 'Se actualizó la solicitud pero no se consulto al verificador1';
							}

						}else{
							$response['message'] = 'Se actualizó la solicitud pero no se consulto al verificador';
						}

						break;
					
					default:
						# code...
						break;
				}
				if($send_sms)
				{
					// Envio de SMS
					$this->load->library('Twilio_library');
					$phone_number = PHONE_COD_COUNTRY.$solicitude[0]['telefono'];
					$this->twilio_library->simple_sms_message($phone_number, $msg);
				}
			}else{
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['error'] = 'Error al actualizar la solicitud id='.$id;
			}
		}else
		{
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$response['status']['ok'] = FALSE;
			$response['error'] = 'La solicitud id='.$id.' no existe';
		}
	
		$this->response($response, $status);

	}

	public function list_solicitudes_post()
	{
		//$this->output->enable_profiler(ENABLE_PROFILER);
		// Inicio de las variables
		$params['LITERAL'] = [];
		$limit = $this->input->post('start');
		$offset = $this->input->post('length');
		$type_operator = $this->session->userdata('tipo_operador');
		$operator = $this->session->userdata('idoperador');

		$operador = $this->operadores_model->get_lista_operadores_by(['idoperador'=>$operator]);
		
		// Seteo el orden.
		$order = $this->input->post('order');
		$direction = "DESC";
		if(isset($order[0]['column'])&& $order[0]['column'] != "0")
		{
			$order = $this->input->post('columns')[$order[0]['column']]['data'];
			if(isset($direction[0]['dir'])){
				$direction = $this->input->post('order')[0]['dir'];
			}
		}
		$params['order'] = $this->_get_order($order, $direction);
		//var_dump($params['order']);die;
		// Fin seteo del orden
			   
		// Seteo la busqueda si existe
		$search = $this->input->post('search')['value'];
		if(isset($search) && !empty($search))
		{
			array_push($params['LITERAL'],$this->_set_search_datatables($this->input->post('search')['value']));
		}
		// Seteo de rangos de fechas.
		$date_end	= date('Y-m-d');
		$date_start	= date('Y-m-d', strtotime($date_end . '- 15 days'));
		$params = array_merge($params, $this->_set_date_range($date_start, $date_end));
		
		// Seteo por tipo de operador.
		switch ($type_operator)
		{
			case '1': 
				array_push($params['LITERAL'],'(solicitud.operador_asignado = '.$operator.')');
				array_push($params['LITERAL'],'(solicitud.estado NOT IN ("PAGADO","RECHAZADO","ANULADO") OR  solicitud.estado IS NULL)');
				array_push($params['LITERAL'],'(solicitud.respuesta_analisis = "APROBADO" OR solicitud.respuesta_analisis IS NULL)');
			   
				break;
			case '4':
				array_push($params['LITERAL'],'(solicitud.operador_asignado = '.$operator.' )');
				array_push($params['LITERAL'],'(solicitud.estado NOT IN ("PAGADO","RECHAZADO","ANULADO") OR  solicitud.estado IS NULL)');
				$params['solicitud.respuesta_analisis']     = "APROBADO";
				break;
			case '3':  
				//Usuario de analisis

				$params['solicitud.respuesta_analisis'] = "APROBADO";
				array_push($params['LITERAL'],'(solicitud.id IN (SELECT id_solicitud FROM solicitud_alertas WHERE escalado=0))');
				array_push($params['LITERAL'],'(solicitud.estado NOT IN ("PAGADO","RECHAZADO","TRANSFIRIENDO","ANULADO") OR  solicitud.estado IS NULL)');
				// Cuento la cantidad de resultados
				//$data['recordsFiltered'] = $this->solicitud_model->get_solicitudes_analisis($params,NULL,NULL,TRUE);
				// Obtengo el resultado.
				$data['data'] = $this->solicitud_model->get_solicitudes_analisis($params, $limit, $offset);   
				break; 
			
			case '11':
				//Aca va la logica de fraude, visado
				$params['solicitud.respuesta_analisis'] = "APROBADO";
				$params['solicitud.estado'] = "APROBADO";
				if(!is_null($this->post('banco')) && $this->post('banco') > 0){
					$params['datos_bancarios.id_banco'] = $this->post('banco');
				}

				
				 if(!empty($operador) && $operador[0]['automaticas'] == 0){
						array_push($params['LITERAL'],'(solicitud.id NOT IN (SELECT id_solicitud FROM solicitudes.solicitud_visado)) and (solicitud.operador_asignado != 108 || (solicitud.operador_asignado = 108 and solicitud.tipo_solicitud="RETANQUEO"))');

				} else{

						array_push($params['LITERAL'],'(solicitud.id NOT IN (SELECT id_solicitud FROM solicitudes.solicitud_visado))');
				}

				// Cuento la cantidad de resultados
				//$data['recordsFiltered'] = $this->solicitud_model->get_solicitudes_visado($params,NULL,NULL,TRUE);
				// Obtengo el resultado.
				 $data['data'] = $this->solicitud_model->get_solicitudes_visado($params, $limit, $offset);   
				break;
			default:
				$params['solicitud.respuesta_analisis'] = "APROBADO";
				array_push($params['LITERAL'],'(solicitud.estado NOT IN ("PAGADO","RECHAZADO","ANULADO") OR  solicitud.estado IS NULL)');
		}
	   
		if ($type_operator != 11 && $type_operator != 3){
			// Cuento la cantidad de resultados
			//$data['recordsFiltered'] = $this->solicitud_model->simple_list($params,NULL,NULL,TRUE);

			// Obtengo el resultado.
			$data['data']	= $this->solicitud_model->simple_list($params, $limit, $offset);
		}
		//var_dump($data['data']);die;

		
		foreach ( $data['data'] as $key => $solicitud) {

			//$data['data'][$key]['last_track'] = $this->get_last_track($solicitud);
			$date = date_create_from_format('Y-m-d H:i:s',$solicitud['fecha_ultima_actividad']);

			$data['data'][$key]['date_ultima_actividad'] = ($date) ? $date->format('d-m-Y'):'';
			$data['data'][$key]['hours_ultima_actividad'] = ($date) ? $date->format('H:i:s'):'';
		}
		$status = parent::HTTP_OK;
		$this->response($data, $status);
	}

	//Traer errores de desembolso
	
	public function listar_desembolso_post()
	{
		// Inicio de las variables
		$params['LITERAL'] = [];
		$limit = $this->input->post('start');
		$offset = $this->input->post('length');
		$type_operator = $this->session->userdata('tipo_operador');
		$operator = $this->session->userdata('idoperador');
		
		// Seteo el orden.
		$order = $this->input->post('order');
		if(isset($order[0]['column']))
		{
			$order = $this->input->post('columns')[$order[0]['column']]['data'];
		}
		$direction = $this->input->post('order')[0]['dir'];
		if(isset($direction[0]['dir']))
		{
			$direction = $this->input->post('order')[0]['dir'];
		}
		$params['order'] = $this->_get_order($order, $direction);
		// Fin seteo del orden
			   
		// Seteo la busqueda si existe
		$search = $this->input->post('search')['value'];
		if(isset($search) && !empty($search))
		{
			array_push($params['LITERAL'],$this->_set_search_datatables($this->input->post('search')['value']));
		}
		
		// Seteo de rangos de fechas.
		$date_end	= date('Y-m-d');
		$date_start	= date('Y-m-d', strtotime($date_end . '- 15 days'));
		$params = array_merge($params, $this->_set_date_range($date_start, $date_end));

		
		// Seteo por tipo de operador.
		switch ($type_operator)
		{
			case '1':
				array_push($params['LITERAL'],'(solicitud.operador_asignado = '.$operator.')');
				array_push($params['LITERAL'],'(solicitud.id IN (SELECT id_solicitud FROM gestion.track_gestion WHERE observaciones LIKE "%[NO PAGADO]%" AND DATEDIFF(CURRENT_DATE,fecha) <=15))');
				$params['solicitud.estado'] = "TRANSFIRIENDO";
				break;
			case '2':
				array_push($params['LITERAL'],'(solicitud.operador_asignado IN (SELECT idoperador FROM gestion.operadores WHERE equipo IN (SELECT equipo FROM gestion.operadores WHERE idoperador='.$operator.')))');
				array_push($params['LITERAL'],'(solicitud.id IN (SELECT id_solicitud FROM gestion.track_gestion WHERE observaciones LIKE "%[NO PAGADO]%" AND DATEDIFF(CURRENT_DATE,fecha) <=15))');
				$params['solicitud.estado'] = "TRANSFIRIENDO";
			default :
				array_push($params['LITERAL'],'(solicitud.id IN (SELECT id_solicitud FROM gestion.track_gestion WHERE observaciones LIKE "%[NO PAGADO]%" AND DATEDIFF(CURRENT_DATE,fecha) <=15))');
				$params['solicitud.estado'] = "TRANSFIRIENDO";

		}
		//$data['recordsFiltered'] = $this->solicitud_model->simple_list($params,NULL,NULL,TRUE);
		// Obtengo el resultado.

		
		$data['data']	= $this->solicitud_model->simple_list($params, $limit, $offset);  
		
		foreach ( $data['data'] as $key => $solicitud) {

			//$data['data'][$key]['last_track'] = $this->get_last_track($solicitud);
			$date = date_create_from_format('Y-m-d H:i:s',$solicitud['fecha_ultima_actividad']);

			$data['data'][$key]['date_ultima_actividad'] = ($date) ? $date->format('d-m-Y'):'';
			$data['data'][$key]['hours_ultima_actividad'] = ($date) ? $date->format('H:i:s'):'';
		}
	   
		$status = parent::HTTP_OK;
		$this->response($data, $status);
	}


	public function listar_registro_por_visar_post()
	{       
		$status = parent::HTTP_INTERNAL_SERVER_ERROR;
		$response = [
					'status' => ['code' => $status, 'ok' => FALSE], 
					'errors' => "No se pudo obtener la información de las solicitudes pendientes de visar."
				];

		$dia = $this->input->get('dia');
		$aut_dep_ind  = $this->input->get('aut_dep_ind'); 

		if ($dia == 'total') {
			$fecha_actual = date("Y-m-d");
			// $fecha_actual = '2022-03-19';

			$fecha_menos_ocho_dia = date("Y-m-d",strtotime($fecha_actual."- 7 days"));
			$data = array();

			$data['data'] =  $this->solicitud_model->listado_solicitudes_por_visar($fecha_menos_ocho_dia, $fecha_actual, $aut_dep_ind) ;

		} else {
			$aut_dep_ind  = $this->input->get('aut_dep_ind'); 
			$data['data'] = $this->solicitud_model->listado_solicitudes_por_visar($dia, $dia, $aut_dep_ind);
		}

		foreach ( $data['data'] as $key => $solicitud) 
		{
			$date = date_create_from_format('Y-m-d H:i:s',$solicitud['fecha_ultima_actividad']);

			$data['data'][$key]['date_ultima_actividad'] = ($date) ? $date->format('d-m-Y'):'';
			$data['data'][$key]['hours_ultima_actividad'] = ($date) ? $date->format('H:i:s'):'';
		}
		$status = parent::HTTP_OK;
		$this->response($data, $status);
	}
	
	public function send_ivr_validation_get($id_solicitud)
	{
		//$solicitud = $this->solicitud_model->simple_list(['id' => $id_solicitud]);
		$id_encript =  AUTHORIZATION::encodeData( $id_solicitud);

		$status = parent::HTTP_OK;
		$plantilla = 'Hola nombre! Su código de validación, es: cod_val repito, su codigo de validación, es: cod_val';
		$modelo = 'busqueda_otp';
		$response['sms'] = $this->infobip_library->send_ivr_codigo_verificacion($id_encript, $plantilla, $modelo);
		$this->response($response, $status); 
	}

	public function send_sms_validation_get($id_solicitud)
	{
		//$solicitud = $this->solicitud_model->simple_list(['id' => $id_solicitud]);
		$id_encript =  AUTHORIZATION::encodeData( $id_solicitud);
		$status = parent::HTTP_OK;
		$plantilla = 'Hola nombre! Tu código de validación en Solventa es: cod_val';
		$modelo = 'busqueda_otp';
		$response['sms'] = $this->infobip_library->send_sms_codigo_verificacion($id_encript, $plantilla, $modelo);
		$this->response($response, $status); 
	}

	
	
	//Traer errores de desembolso
	
	public function listar_x_registro_post()
	{       
		$periodo      = $this->input->get('periodo');   
		$id_operator  = $this->session->userdata('idoperador'); 
		
		if($periodo == 'validaciones'){
			
			$data['data'] = $this->solicitud_model->listado_por_revisar_desembolso($id_operator,$periodo);  

		}else{

			$data['data'] = $this->solicitud_model->listado_formato($id_operator,$periodo);  
		}
		
		foreach ( $data['data'] as $key => $solicitud) {

			//$data['data'][$key]['last_track'] = $this->get_last_track($solicitud);
			$date = date_create_from_format('Y-m-d H:i:s',$solicitud['fecha_ultima_actividad']);

			$data['data'][$key]['date_ultima_actividad'] = ($date) ? $date->format('d-m-Y'):'';
			$data['data'][$key]['hours_ultima_actividad'] = ($date) ? $date->format('H:i:s'):'';
		}
	   
		$status = parent::HTTP_OK;
		$this->response($data, $status);
	}

	public function getSolicitudesPendientes_get(){
		$id_operador = $this->session->userdata("idoperador");
		$solicitudesPendientes = $this->solicitud_model->getSolicitudesPendientes_m($id_operador);
		if (!empty($solicitudesPendientes)){
			$status = parent::HTTP_OK;
			$response['solicitudes'] = $solicitudesPendientes;
			$response['status']['code'] = $status;
			$response['status']['ok'] = TRUE;
			$response['message'] = 'Solicitudes pendientes encontradas';
		}else{
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$response['status']['ok'] = FALSE;
			$response['message'] = 'No hay solicitudes Pendientes';
		}

		$this->response($response, $status);
	}

	public function validar_telefono_get($id){
		//$id = $this->post('id_solicitud');
		$solicitud = $this->solicitud_model->getSolicitudes(['id'=>$id]);
		if(isset($solicitud[0]['validacion_telefono']) && $solicitud[0]['validacion_telefono'] != null)
		{
			if($solicitud[0]['validacion_telefono'] == 0)
			{	
				$parametros = array('validacion_telefono' => 1);
				$this->solicitud_model->update_val_telefono($id, $parametros);

				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['message'] = 'El telefono se verifico';

			}else{
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['message'] = 'El telefono ya se encuentra verificado';
			}
		}else{
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$response['status']['ok'] = FALSE;
			$response['message'] = 'No existe validacion de telefono';
		}

		$this->response($response, $status);
	}

	function enviarSmsIvrAgendaTelefonica_post(){
		$text = $this->input->post('text');
        $servicio = $this->input->post('servicio');
        $tipo_envio = $this->input->post('tipo_envio');
		
        $curl = curl_init();
		if($servicio == "10" || $servicio == "7") {

			$numero = $this->input->post('numero_ms');
			$params=['numero' => $numero, 'text' => $text, 'tipo_envio' => (($tipo_envio == "IVR") ? 1 : 2)];

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://comunicaciones.solventa.co/api/ApiMessageBird/envioSMS_gral_messagebird',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $params,
				CURLOPT_HTTPHEADER => array(
					'Cookie: ci_session=3aqrmoddneg14jvde4elgp1c4ehhjdkk'
				),
			));
		} else {

			$numero = $this->input->post('numero');
			$params=['numero' => $numero, 'text' => $text, 'servicio' => $servicio, 'tipo_envio' => $tipo_envio];

			curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://comunicaciones.solventa.co/ApiEnvioGeneralTrack',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $params,
			));
		}

		$data = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			$data = $err;
			$status = parent::HTTP_BAD_REQUEST;
			$ok=FALSE;
		} else {
			$status = parent::HTTP_OK;
			$ok=TRUE;
		}
		$response = ['status' => $status, 'data' => $data , 'ok'=>$ok];
		$this->response($response, $status);

    }

	function enviarMailAgendaPepipost_post(){
        $documento=$this->input->post('documento');
        $mail=$this->input->post('mail');
        // $mail='damianlema@gmail.com';
        $id_template=$this->input->post('id_template');
        $id_logica=$this->input->post('id_logica');
		$params=['documento'=>$documento, 'mail'=>$mail, 'id_template'=>$id_template,'id_logica'=>$id_logica];
		
        $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => URL_CAMPANIAS."ApiEnviarMailAgendaPepipost",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                    CURLOPT_POSTFIELDS=>$params
                ));
		$data = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			$data = $err;
			$status = parent::HTTP_BAD_REQUEST;
			$ok=FALSE;
		} else {
			$status = parent::HTTP_OK;
			$ok=TRUE;
		}
		$response = ['status' => $status, 'data' => $data , 'ok'=>$ok];
		$this->response($response, $status);

    }

	public function updateAgendaProveedor_post(){
		$id_template=$this->input->post('id_template');
		$proveedor=$this->input->post('proveedor');
		// dump($id_template,$proveedor);die;
		if($id_template!= null && $proveedor!=null){
			$result = $this->chat->updateAgendaProveedor($id_template,['proveedor'=>$proveedor]);	
			if (!empty($result)){
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['message'] = 'Se actualizo proveedor';
			}else{
				$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al actualizar proveedor"];
			}
		}else{
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['message'] = 'Falló al actualizar proveedor';
		}
		
		$this->response($response, $status);
	}

	public function updateEstadoServicio_post(){
		$params=[];
		$numero=$this->input->post('numero');
		if ($verificado_llamada=$this->input->post('verificado_llamada')){
			$params = ["verificado_llamada"=> $verificado_llamada];
		}
		if($verificado_whatsapp=$this->input->post('verificado_whatsapp')){
			$params = ["verificado_whatsapp"=> $verificado_whatsapp];
		}
		if($verificado_sms=$this->input->post('verificado_sms')){
			$params = ["verificado_sms"=> $verificado_sms];
		}
		if ($estado=$this->input->post('estado')){
			$params = ["estado"=> $estado];
		}
		if($llamada=$this->input->post('llamada')){
			$params = ["llamada"=> $llamada];
		}
		if($whatsapp=$this->input->post('whatsapp')){
			$params = ["whatsapp"=> $whatsapp];
		}
		if ($sms=$this->input->post('sms')){
			$params = ["sms"=> $sms];
		}

		if(!empty($params)){
			$result = $this->chat->updateEstadoServicio($numero,['data'=>$params]);	
			if (!empty($result)){
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['message'] = 'Se actualizo Servicio';
			}else{
				$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al actualizar Servicio"];
			}
		}else{
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['message'] = 'Falló al actualizar Servicio';
		}
		
		$this->response($response, $status);
	}	

	public function update_data_client_post()
	{
		$id = $this->input->post('id_solicitud');
		$solicitud = $this->solicitud_model->getSolicitudes(['id'=>$id]);
		if($this->_validate_update_data_client())
		{
			// Actualizo el campo email.
			$email = $this->input->post('email');
			//$documento_de_la_solicitud_a_cambiar_mail= $this->solicitud_model->obtenerDocumento($id);
			$solicitud = $this->solicitud_model->getSolicitudes(['id'=>$id]);
			$documentoBuscar=$this->input->post('documento');
			$mail_a_modificar=$email;
			if($this->solicitud_model->getValidarMail($mail_a_modificar,$documentoBuscar) == 0)
			{
				if(isset($email))
				{
					$data['email'] = $email;
				}

				if($this->solicitud_model->edit($id, $data))
				{
					if(isset($solicitud[0]['id_usuario']) && $solicitud[0]['id_usuario'] != null){
						$id_usuario = $solicitud[0]['id_usuario'];
						$parametros = array(
						'email' => $email,
						'username' => $email
						);
						$this->solicitud_model->updateMailUser($id_usuario,$parametros);
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = TRUE;
						$response['solicitud'] = $solicitud;
						$response['message'] = 'Se actualizó la solicitud';
					}else{
						$status = parent::HTTP_OK;
						$response['status']['code'] = $status;
						$response['status']['ok'] = TRUE;
						$response['solicitud'] = $solicitud;
						$response['message'] = 'Se actualizo la solicitud pero no el usuario';
					}
				}else{
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['error'] = 'Error al actualizar la solicitud id='.$id;
				}

			}
			else{
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['error'] = 'Error el correo ya esta siendo utilizado';		
			}
				
		}

		$this->response($response, $status);
	}

	public function validar_desembolso_post(){
		$id_solicitud = $this->post('param1');
		$id_operador = $this->post('param2');
		$hoy = date("Y-m-d H:i:s");

		if(!is_null($id_operador) && !is_null($id_solicitud)){

			$data = [
				"id_operador" => $id_operador,
				"id_solicitud" => $id_solicitud,
				"revisada" => "0",
				"fecha_hora_solicitud" => $hoy
			];

			$result = $this->solicitud_model->insertar_validar_desembolso($data);
			
			if($result > 0){
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = TRUE;
				$response['message'] = 'Solicitud enviada con exito';
			} else {
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['message'] = 'No fue posible enviar la solicitud';
			}
		}else {
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$response['status']['ok'] = FALSE;
			$response['message'] = 'No fue posible enviar la solicitud';
		}

		
		$this->response($response, $status);
		
	}

	public function consultar_solicitud_cliente_get($id_cliente)
	{
		if(!is_null($id_cliente)){
			$solicitud = $this->solicitud_model->getSolicitudesBy(['id_cliente'=> $id_cliente, 'limite' => 1]);
			if(!empty($solicitud)){
				$response['status']['ok'] = TRUE;
				$response['data'] = $solicitud[0];
			} else{
				$response['status']['ok'] = FALSE;
				$response['message'] = 'No se encontro la informacin del cliente';
			}
		} else {
			$response['status']['ok'] = FALSE;
			$response['message'] = 'El credito seleccionado no tiene un cliente asociado';
		}
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function consultar_cliente_get($documento)
	{
		if(!is_null($documento)){
			$cliente = $this->cliente_model->getClienteBy(['documento'=> $documento]);
			if(!empty($cliente)){
				$response['status']['ok'] = TRUE;
				$response['data'] = $cliente[0];
			} else{
				$response['status']['ok'] = FALSE;
				$response['message'] = 'No se encontro la informacion del cliente';
			}
		} else {
			$response['status']['ok'] = FALSE;
			$response['message'] = 'Datos invalidos';
		}
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function configuracionesGestionObligatoria_get()
    {   
		$data['configuraciones'] = $this->solicitud_model->find_configuracion_obligatorias_get(false);

        if(!empty($data['configuraciones']))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>TRUE,'data' => $data['configuraciones']];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'ok'=>FALSE,'message' => 'No fue posible cargar la tabla de configuraciones'];
        }
        $this->response($response);
    }

	public function update_estado_configuracion_solicitud_obligatoria_post()
	{
		$status = parent::HTTP_OK;
		$response['status']['ok'] = FALSE;
		$response['message'] = 'No se pudo actualizar la configuración.';

		$filtro['id'] = $this->input->post('id');
		$config = $this->solicitud_model->find_configuracion_obligatorias_get($filtro);
		$estado_inactivo['estado'] = null;
		$update_rows['estado'] = ($config[0]->estado == 0) ? 1 : 0 ;
		$update_rows['id_operador'] = $this->session->userdata('idoperador');
	
		$result = $this->solicitud_model->cambiar_estado_gestion_ob($config[0],$update_rows);

		if($result != false){
			$response['status']['ok'] = TRUE;
			$response['message'] = "La configuración fue actualizada con éxito.";
			$response['gestion'] = $result;
		}
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function add_configuracion_solicitud_obligatoria_post()
	{
		$status = parent::HTTP_OK;
		$response['status']['ok'] = FALSE;
		$response['message'] = 'No se pudo registrar la nueva configuración.';

		if(!$this->_validate_registro_gestiones_obligatorias())
		{
			$status = parent::HTTP_OK;
			$response['status']['ok'] = FALSE;
			$response['message'] = validation_errors();
		
		}else{
			$hoy = date("Y-m-d H:i:s");
			$data = [
				'tipo_operador' => $this->input->post('tipoOperador'),
				'seg_ejecucion' => $this->input->post('segEjecucion'),
				'min_get_solicitudes' => $this->input->post('minSolicitud'),
				'min_gestion' => $this->input->post('minGestion'),
				'min_extension' => $this->input->post('minutosExtension'),
				'extensiones_consecutivas' => $this->input->post('extensionesConsecutivas'),
				'id_operador' => $this->session->userdata('idoperador'),
				'fecha_modificacion' => $hoy,
				'estado' => $this->input->post('estado'),
				'porcentaje_alerta_extension'=> $this->input->post('porcentajePreventivo'),
				'segundos_alert_ext'=> $this->input->post('segundosAlerta'),
				'porcentaje_warning'=> $this->input->post('porcentajeAlerta'),
				'min_gestion_chats'=> $this->input->post('minGestionChats'),
				'min_proceso_obligatorio'=> $this->input->post('minProcesoObligatorio'),
				'dias_busqueda'=> $this->input->post('diasBusqueda'),
				'horas_ultima_gestion'=> $this->input->post('horaUltimaGestion'),
				'min_chat_documentos'=> $this->input->post('minDocChats')
			];
			if ($data['estado'] == '1') {
				$filtro['estado'] = 1;
				$filtro['tipo_operador'] = $data['tipo_operador'];//añado tipo de operador al filtro
				
				$configActiva = $this->solicitud_model->find_configuracion_obligatorias_get($filtro);

				if ($configActiva != NULL) {
					$update_rows = array(
						'estado' => '0',
						'tipo_operador'=> $data['tipo_operador']
						
					);
					$this->solicitud_model->update_estados_solicitud_add($update_rows);
				}
				
			}
			
			$result = $this->solicitud_model->add_configuracion_obligatorias($data);

			if($result > 0){
				$response['status']['ok'] = TRUE;
				$response['message'] = "La nueva configuración fue creada con éxito.";
				$response['gestion'] = $result;
			}

			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$this->response($response, $status);
		}
		
		$this->response($response, $status);
	}

	public function update_configuracion_solicitud_obligatoria_post()
	{
		$status = parent::HTTP_OK;
		$response['status']['ok'] = FALSE;
		$response['message'] = 'No se pudo registrar la nueva configuración.';


		if(!$this->_validate_registro_gestiones_obligatorias())
		{
			$status = parent::HTTP_OK;
			$response['status']['ok'] = FALSE;
			$response['message'] = validation_errors();
		
		}else{
			$hoy = date("Y-m-d H:i:s");
			$data = new stdClass();
			$data->id =  $this->input->post('id');
			$update_rows = [
				'tipo_operador' => $this->input->post('tipoOperador'),
				'seg_ejecucion' => $this->input->post('segEjecucion'),
				'min_get_solicitudes' => $this->input->post('minSolicitud'),
				'min_gestion' => $this->input->post('minGestion'),
				'min_extension' => $this->input->post('minutosExtension'),
				'extensiones_consecutivas' => $this->input->post('extensionesConsecutivas'),
				'id_operador' => $this->session->userdata('idoperador'),
				'fecha_modificacion' => $hoy,
				'estado' => $this->input->post('estado'),
				'porcentaje_alerta_extension'=> $this->input->post('porcentajePreventivo'),
				'segundos_alert_ext'=> $this->input->post('segundosAlerta'),
				'porcentaje_warning'=> $this->input->post('porcentajeAlerta'),
				'min_gestion_chats'=> $this->input->post('minGestionChats'),
				'min_proceso_obligatorio'=> $this->input->post('minProcesoObligatorio'),
				'dias_busqueda'=> $this->input->post('diasBusqueda'),
				'horas_ultima_gestion'=> $this->input->post('horaUltimaGestion'),
				'min_chat_documentos'=> $this->input->post('minDocChats')
			];
			
			$estado_inactivo['estado'] = null;
			if ($update_rows['estado'] == '1') {
				$estado_inactivo = array(
					'estado' => '0',
				);
			}
			
			$result = $this->solicitud_model->update_config_gestion_ob($data,$update_rows, $estado_inactivo);
			
			if($result > 0){
				$response['status']['ok'] = TRUE;
				$response['message'] = "La configuración fue editada con éxito.";
				$response['gestion'] = $result;
			}

			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$this->response($response, $status);
		}
		$this->response($response, $status);
	}

	
	public function consultar_solicitudes_obligatorias_get()
	{
		$tipo_operador = $this->session->userdata('tipo_operador');
		$data = $this->solicitud_model->get_configuracion_obligatorias($tipo_operador);
		$hoy = date("Y-m-d H:i:s");
		$render_view;
			if (!empty($data)) {

				$operador = $this->session->userdata('idoperador');
				

				if ($this->session->userdata('tipo_operador') == 11) {

					//delete registro de solicitudes abiertas
					$data_delete = [
						'id_operador' => $this->session->userdata('idoperador'),
					];
					$delete = $this->solicitud_model->delete_solicitudes_abiertas($data_delete);

					$solicitudes_todas_sin_visar = $this->solicitud_model->get_solicitudes_obligatoria_visado();
					$solicitudes[0] = (empty($solicitudes_todas_sin_visar))? []:(array)$solicitudes_todas_sin_visar[0]; 

					

					//insertamos nueva apertura para el operador
					$data_insert = [
						'id_solicitud' => (int)$solicitudes[0]['id'],
						'id_operador' => $this->session->userdata('idoperador'),
						'fecha_registro' => $hoy,
					];
					$insert = $this->solicitud_model->insert_solicitudes_abiertas($data_insert);

					if($insert < 0)
						$solicitudes[0] = (array)$solicitudes_todas_sin_visar[(count($solicitudes_todas_sin_visar)-1)]; 
										
				} else {
					$hour = date('H:i:s');
					$data = [
						'id_operador' => $operador, //$this->session->userdata('idoperador'),
						'dias' => $data[0]->dias_busqueda,
						'horas' => $data[0]->horas_ultima_gestion,
						'min_chat' => $data[0]->min_chat_documentos,
						'inicio_transf_rechazada' => $data[0]->inicio_transf_rechazada,
						'fin_transf_rechazada' => $data[0]->fin_transf_rechazada
					];
					if ($hour >= $data['inicio_transf_rechazada'] && $hour <= $data['fin_transf_rechazada']) {
						$solicitudes = $this->solicitud_model->transferenciaRechazada($operador, $tipo_operador);
						$render_view = 'transRechazada';
					} else {
						$solicitudes = $this->solicitud_model->get_solicitudes_gestion_obligatoria($data);
						$render_view = false;
					}
				}

				$response['habilitado'] = TRUE;
				
				if(!empty($solicitudes)){
					$response['status']['ok'] = TRUE;
					$response['data'] = $solicitudes;
					$response['render_view'] = $render_view;
				} else{
					$response['status']['ok'] = FALSE;
					$response['sin_pendientes'] = TRUE;
					$response['message'] = 'No hay solicitudes obligatorias pendientes';
				}
				
			} else {
				$response['habilitado'] = FALSE;
				$response['status']['ok'] = FALSE;
				$response['message'] = 'No hay una configuracion activa';
			}
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}
	
	//GUARDAR TRACK DE TIEMPO DE DESCASO DEL OPERADOR
	public function gestionar_descanso_operador_post()
	{
		$post = $this->post();
		$time_actual = date("Y-m-d H:i:s");
		$response['status']['ok'] = false;
		
		if($post['en_descanso'] == 0){
			$data = [
				'id_operador' => $this->session->userdata('idoperador'),
				'id_gestion' => $post['id_gestion'],
				'track_gestion_motivo'=> $post['motivo'],
				'track_gestion_inicio' => $time_actual
			];
			
			if($this->solicitud_model->insert_track_gestion_descanso($data)){
				$response['status']['ok'] = true;
			
			}
		
		}elseif($post['en_descanso'] == 1){
			$where = [
				'id_operador' => $this->session->userdata('idoperador'),
				'id_gestion' => $post['id_gestion']
			
			];
			$data['track_gestion_fin'] = $time_actual;
		
			if($this->solicitud_model->update_track_gestion_descanso($data, $where)){
				$response['status']['ok'] = true;
			
			}
		}
		
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function iniciar_gestion_obligatoria_post()
	{
		$id_solicitud = $this->post('id_solicitud');
		$hoy = date("Y-m-d H:i:s");
		$data = [
			'id_solicitud' => $id_solicitud,
			'id_operador' => $this->session->userdata('idoperador'),
			'fecha_hora_inicio_gestion' => $hoy,
			'fecha_hora_entrega_caso' => $hoy,
			'gestionado'=> 0,
			'extension_tiempo_gestion' =>0
		];

		$result = $this->solicitud_model->insert_gestion_obligatoria($data);
		if($result > -1){
			$dataTrackGestion = [
                'id_solicitud'=>(int)$id_solicitud,
                'observaciones'=> 'Gestion obligatoria iniciada <br> Fecha: '.date("d-m-Y H:i:s"), 
                'id_operador'=>  $this->session->userdata('idoperador'),  
                'id_tipo_gestion' => 180
            ];

            $endPoint =  base_url('api/track_gestion');
			if($this->session->userdata('tipo_operador') != 9)
				$track = json_decode($this->curl($endPoint, 'POST', $dataTrackGestion));

			$response['status']['ok'] = TRUE;
			$response['message'] = "Gestion iniciada";
			$response['gestion'] = $result;
		} else{
			$response['status']['ok'] = FALSE;
			$response['message'] = 'No se pudo registrar el inicio de la gestion';
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function extension_gestion_obligatoria_post() 
	{
		$id_solicitud = (int)$this->post('id_solicitud');
		$id_gestion = $this->post('id_gestion');
		$id_control = $this->post('control');
		$hoy = date("Y-m-d H:i:s");

		if ($id_solicitud > 0) {
			$param = [
				'id' => $id_gestion, 
				'id_solicitud' => $id_solicitud,
			];
			$data = [
				'extension_tiempo_gestion' =>1,
				'fecha_hora_extension' => $hoy
			];
	
			$result = $this->solicitud_model->update_gestion_obligatoria($data, $param);
			$dataTrackGestion = [
				'id_solicitud'=>(int)$id_solicitud,
				'observaciones'=> 'Solicitud de extensión del tiempo para la gestión de la solicitud<br> Fecha: '.date("d-m-Y H:i:s"), 
				'id_operador'=>  $this->session->userdata('idoperador'), 
				'id_tipo_gestion' => 181
			];	
			$endPoint =  base_url('api/track_gestion');

			if($this->session->userdata('tipo_operador') != 9)
				$track = json_decode($this->curl($endPoint, 'POST', $dataTrackGestion));
		} else {
			
			$data = [
				'id_operador' => $this->session->userdata('idoperador'),
				'fecha_hora_extension' => $hoy,
				'observaciones'=> 'Solicitud de extensión del tiempo para gestiones varias <br> Fecha: '.date("d-m-Y H:i:s"), 
				'id_tipo_gestion' => 181
			];
	
			$result = $this->tracker_model->save_track_extencion_gestion($data);
		}
		


		if($result > -1){
			$response['status']['ok'] = TRUE;
			$response['message'] = "Extension agregada";
		} else{
			$response['status']['ok'] = FALSE;
			$response['message'] = 'No se pudo dar una extension para la gestion';
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}

	public function cerrar_gestion_obligatoria_post() 
	{
		$id_solicitud = $this->post('id_solicitud');
		$id_gestion = $this->post('id_gestion');

		$gestionado = ($this->post('track') == "true")? 1:0;
		$hoy = date("Y-m-d H:i:s");

		$param = [
			'id' => $id_gestion, 
			'id_solicitud' => $id_solicitud,
		];
		$data = [
			'gestionado' =>$gestionado,
			'fecha_hora_cierre' => $hoy
		];

		$result = $this->solicitud_model->update_gestion_obligatoria($data, $param);

		$dataTrackGestion = [
			'id_solicitud'=>(int)$id_solicitud,
			'observaciones'=> 'Gestion obligatoria '.(($this->post('track') == "true")? 'gestionada':'cerrada y sin gestión').'<br> Fecha: '.date("d-m-Y H:i:s"), 
			'id_operador'=>  $this->session->userdata('idoperador'),  
			'id_tipo_gestion' => 182
		];

		$endPoint =  base_url('api/track_gestion');

		//delete registro de solicitudes abiertas
		$data_delete = [
			'id_operador' => $this->session->userdata('idoperador'),
		];
		$delete = $this->solicitud_model->delete_solicitudes_abiertas($data_delete);


		if($this->session->userdata('tipo_operador') != 9)
			$track = json_decode($this->curl($endPoint, 'POST', $dataTrackGestion));


		if($result > -1){
			$response['status']['ok'] = TRUE;
			$response['message'] = "Gestión cerrada";
		} else{
			$response['status']['ok'] = FALSE;
			$response['message'] = 'No se pudo registrar el cierre de la gestión';
		}

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
	}


	/**
	 * privates functions
	 */

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

	private function _upper_control($id_operador, $tipo_control)
	{
		$current_date = date('Y-m-d');
		
		$params = array('id_operador'=> $id_operador, 'fecha_control' => $current_date);
		// Consulto los datos
		$control = $this->solicitud_asignacion->search($params);
		// Si existe el registro incremento de acuerdo a la accion.
		if(!empty($control))
		{
			switch ($tipo_control)
			{
				case 'VERIFICADO':
					$field_update = ['verificados' => $control[0]['verificados'] +1];
					break;
				case 'VALIDADO':
					$field_update = ['validados' => $control[0]['validados'] +1];
					break;
				case 'APROBADO':
					$field_update = ['aprobados' => $control[0]['aprobados'] +1];
					break;
				case 'RECHAZADO':
					$field_update = ['rechazados' => $control[0]['rechazados'] +1];
					break;
			}
			$field_update['update_at'] = date('Y-m-d H:i:s');
			$response = $this->solicitud_asignacion->update($control[0]['id'], $field_update);

			return ($response!=0)? TRUE : FALSE;

		}

		return FALSE;
	}

	private function curl($endPoint, $method = 'POST',  $params=[]){
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

/**********************************************************************/
// GETTERS
/**********************************************************************/


	public function get_last_track($solicitud)
	{
		$data=[];
		$params['id_solicitud'] = $solicitud['id'];
		$params['limit'] = 1;
		$params['order'] = [['fecha', 'DESC'], ['hora', 'DESC'],['track_gestion.id','DESC']];
		
		$track = $this->tracker_model->search($params);
		if(!empty($track))
		{
			$track = $track[0]['observaciones'].' - '.$track[0]['operador'];
			$data=$track;
		}
		return $data;
	}


/**********************************************************************/
// SETTERS
/**********************************************************************/

	private function _set_search($search, $criterio)
	{
		$aux = [];
		if(!empty($search))
		{
			switch ($criterio) {
				case 'id':
					$aux['id']                 = $search;
					break;
				case 'documento':
					$aux['OR']['solicitud.documento'] = $search;
					break;
				case 'telefono':
					$aux['OR']['solicitud.telefono']= $search;
					break;
				case 'nombre':
					$aux['OR']['solicitud.nombres']= $search;
					break;
				case 'apellido':
					$aux['OR']['solicitud.apellidos']= $search;
					break;
				case 'email':
					$aux['OR']['solicitud.email']= $search;
					break;
				default:
					# code...
					break;
			}
			/*if(is_numeric($search))
			{
				if(strlen($search) < 7)
				{
						$aux['id']                 = $search;
				}else{
					$aux['id']                 = $search;
					$aux['OR_LIKE_BOTH']['solicitud.documento'] = $search;
					$aux['OR_LIKE_BOTH']['solicitud.telefono']= $search;
				}

			}else if(strpos($search,'@'))
			{
				$aux['OR_LIKE_BOTH']['solicitud.email']= $search;

			}else
			{
				$aux['OR_LIKE_BOTH']['solicitud.nombres']= $search;
				$aux['OR_LIKE_BOTH']['solicitud.apellidos']= $search;
			}*/
		}
		return $aux;
	}

	private function _set_search_datatables($search)
	{
		$aux = '';
		$type_operator = $this->session->userdata('tipo_operador');
		if(!empty($search))
		{
			
			if(is_numeric($search))
			{
				if(strlen($search) < 7)
				{
					$aux .= 'solicitud.id LIKE "%'.$search.'%"';
				}else{
					$aux .= 'solicitud.documento LIKE "%'.$search.'%"';
				}
			}else
			{
				$aux .= '(solicitud.nombres LIKE "%'.$search.'%" OR ';
				$aux .= 'solicitud.apellidos LIKE "%'.$search.'%" OR ';
				$aux .= 'solicitud.tipo_solicitud LIKE "%'.$search.'%" OR ';
				$aux .= 'solicitud.respuesta_analisis LIKE "%'.$search.'%" OR ';
				$aux .= 'solicitud.estado LIKE "%'.$search.'%" OR ';
				
				$aux .= 'solicitud.resultado_ultimo_reto LIKE "%'.$search.'%" OR ';
				if($type_operator != 3){
					$aux .= 'operadores.nombre_apellido LIKE "%'.$search.'%" OR ';
					$aux .= 'datos_bancarios.respuesta LIKE "%'.$search.'%" OR ';
				}
				$aux .= 'situacion.nombre_situacion LIKE "%'.$search.'%" )';
			}
		}

		return $aux;
	}

	private function _set_status($search)
	{
		$aux = [];
		$status = strtoupper($search);
		switch ($status) {
			case 'ANALISIS':
			case 'VERIFICADO':
			case 'VALIDADO':
			case 'APROBADO':
			case 'TRANSFIRIENDO':
			case 'PAGADO':
			case 'RECHAZADO':
				$aux['solicitud.estado'] = $status;
				break;
			case 'BURO_APROBADO':
				$aux['solicitud.respuesta_analisis'] = 'APROBADO';
				break;
			case 'BURO_RECHAZADO':
				$aux['solicitud.respuesta_analisis'] = 'RECHAZADO';
				break;
			case 'CUENTA_ACEPTADA':
				$aux['datos_bancarios.respuesta'] = 'ACEPTADA';
				break;
			case 'CUENTA_RECHAZADA':
				$aux['datos_bancarios.respuesta'] = 'RECHAZADA';
				break;
			case 'RETO_CORRECTA':
				$aux['solicitud.resultado_ultimo_reto'] = 'CORRECTA';
				break;
			case 'RETO_INCORRECTA':
				$aux['solicitud.resultado_ultimo_reto'] = 'INCORRECTA';
				break;
			case 'CREDITO_VIGENTE':
				$aux['creditos.estado'] = 'vigente';
				break;
			case 'CREDITO_MORA':
				$aux['creditos.estado'] = 'mora';
				break;
			case 'CREDITO_CANCELADO':
				$aux['creditos.estado'] = 'cancelado';
				break;
			case 'VISADO':
				$aux['solicitud.estado'] = 'APROBADO';
				break;
			case 'NOVISADO':
				$aux['solicitud.estado'] = 'APROBADO';
				break;
			default:
				# code...
				break;
		}

		return $aux;
	}

	private function _set_date_range($date_start, $date_end = null, $format='Y-m-d', $visado = null)
	{

		
		$response = [];
		if(!isset($date_end) || empty($date_end))
		{
			$end = date('Y-m-d 23:59:59');
			if(!is_null($visado)){
				$end = date('Y-m-d');
			}
		}else
		{
			$end = date_create_from_format($format, trim($date_end));
			if(!is_null($visado)){
				$end = $end->format('Y-m-d');
			} else{
				$end = $end->format('Y-m-d 23:59:59');
			}
		}
		if(isset($date_start) && !empty($date_start))
		{
			$start 	= date_create_from_format($format, trim($date_start));
			$response['>=']['solicitud.fecha_ultima_actividad'] = $start->format('Y-m-d 00:00:00');
			if(!is_null($visado)){
				$response['>=']['solicitud.fecha_ultima_actividad'] = $start->format('Y-m-d');
			}
			$response['<=']['solicitud.fecha_ultima_actividad'] = $end; 
		}

		return $response;
	}

	private function _get_order($order, $direction = 'desc')
	{
		$type_operator = $this->session->userdata('tipo_operador');
		$response = [['solicitud.id', 'desc'],['solicitud.fecha_ultima_actividad', 'desc']];
		if(!empty($order))
		{
			switch ($order) 
			{
				case 'date_ultima_actividad':
					$response = [['solicitud.fecha_ultima_actividad', $direction]];
					break;
				case 'hours_ultima_actividad':
					$response = [['solicitud.fecha_ultima_actividad', $direction]];
					break;
				case 'operador_nombre_pila':

					
					if($type_operator != 3)
						$response = [['operadores.nombre_pila', $direction]];
					else
						$response = [['solicitud_alertas.descripcion', $direction]];
					break;
				case 'banco_resultado':
					if($type_operator != 3)
						$response = [['datos_bancarios.respuesta', $direction]];
					
					break;
				case 'nombre_situacion':
					$response = [['situacion.nombre_situacion', $direction]];
					break;
				default:
					$response =[['solicitud.'.$order,$direction]];
					break;
			}
		}

				return $response;
	}

	//Sabrina Basteiro INICIO
	//Inserta datos en tabla visado
	public function insertarVisado($id,$id_operator,$visado){
		$fecha_alta = date('Y-m-d H:i:s');
		$parametros = array(
			'id_solicitud' => $id,
			'id_operador' => $id_operator,
			'fecha_creacion' => $fecha_alta, 
			'visado' => $visado,    
		);
		return $this->solicitud_model->insertarVisado($parametros);  
	}
	
	//Actualiza la tabla solicitud_alertas
	public function updateAnalizado($data){
		$fecha = date('Y-m-d H:i:s');
		$parametros = array(
			'id_solicitud' => $data['id'],
			'operador' => $data['operador_asignado'],
			'escalado' => $data['escalado'],
			'fecha_hora' => $fecha
		);
		return $this->solicitud_model->actualizarAnalizado($parametros);  
	}
		
	public function actualizarVisado($id_visado,$visado){
		$fecha_actualizacion = date('Y-m-d H:i:s');
		$parametros = array(
			'fecha_actualizacion' => $fecha_actualizacion,   
			'visado' => $visado,    
		);
		return $this->solicitud_model->actualizarVisado($id_visado,$parametros);
	}          

	public function actualizar_agenda_localidad_post(){
		$result = 0;
		$data=[];
		$departamento="";
		$ciudad="";
		$estado="";
	
		if($this->post('departamento')!= null && $this->post('ciudad')!=null)
		{
			$departamento = $this->post('departamento');
			$ciudad = $this->post('ciudad');
			$data =[
				'departamento' => $this->post('departamento'),
				'ciudad' => $this->post('ciudad')
			];
			$result = $this->solicitud_model->update_telefono_solicitante($this->post('id'), $data);
			if ($result > 0) 
			{
				$status = parent::HTTP_OK;
				$response = ['status' => $status, 'message' => "Número actualizado", 'departamento' => $departamento, 'ciudad' => $ciudad];					
			} else {
				$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => $status, 'message' => "El número no pudo ser actualizado"];
			}
		}else {
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['message'] = $this->form_validation->error_array();
		}
		$this->response($response);
	}

	public function actualizar_agenda_estado_post(){
		$id=$this->post('id');
		$estado = $this->post('estado');
		// dump($estado,$id);die;
		if($estado!= null)
		{
			$data =[
				'estado' =>$estado
			];
			$result = $this->solicitud_model->update_telefono_solicitante($id, $data);
			if ($result > 0) 
			{
				$status = parent::HTTP_OK;
				$response = ['status' => $status, 'message' => "Número actualizado"];					
			} else {
				$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => $status, 'message' => "El número no pudo ser actualizado"];
			}
		}else {
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['message'] = $this->form_validation->error_array();
		}
		$this->response($response);
	}

	public function actualizar_mail_estado_post(){
		$id=$this->post('id');
		$estado = $this->post('estado');
		// dump($estado,$id);die;
		if($estado!= null)
		{
			$data =[
				'estado' =>$estado
			];
			$result = $this->solicitud_model->update_mail_solicitante($id, $data);
			if ($result > 0) 
			{
				$status = parent::HTTP_OK;
				$response = ['status' => $status, 'message' => "Correo actualizado"];					
			} else {
				$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => $status, 'message' => "El Correo no pudo ser actualizado"];
			}
		}else {
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['message'] = $this->form_validation->error_array();
		}
		$this->response($response);
	}

	public function cambio_estado_servicio_post(){
		$id=$this->post('id');
		$variable=$this->post('variable');
		$valor = $this->post('valor');
		// dump($estado,$id);die;
		if($valor!= null)
		{
			$data =[
				$variable=>$valor
			];
			// dump($data);die();
			$result = $this->solicitud_model->update_telefono_solicitante($id, $data);
			if ($result > 0) 
			{
				$status = parent::HTTP_OK;
				$response = ['status' => $status, 'message' => "Estado servicio actualizado"];					
			} else {
				$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => $status, 'message' => "Estado servicio no pudo ser actualizado"];
			}
		}else {
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['message'] = $this->form_validation->error_array();
		}
		$this->response($response);
	}

	public function agendar_telefono_solicitante_post()
	{
		$id = $this->post('id_solicitud');
		$data =[
				'documento' => $this->post('documento'),
				'numero' => $this->post('numero'),
				'tipo' => strtoupper($this->post('tipo')),
				'fuente' => strtoupper($this->post('fuente')),
				'contacto' => $this->post('contacto'),
				'estado' => $this->post('estado'),
				'ciudad' => $this->post('ciudad'),
				'departamento' => $this->post('departamento'),
				'id_parentesco'=>$this->post('id_parentesco'),
				'verificado_llamada'=>$this->post('verificado_llamada'),
				'verificado_sms'=>$this->post('verificado_sms'),
				'verificado_whatsapp'=>$this->post('verificado_whatsapp'),
				'llamada'=>$this->post('llamada'),
				'sms'=>$this->post('sms'),
				'whatsapp'=>$this->post('whatsapp'),

			];
			// dump($data["fuente"]);
			if ($data["fuente"]=='PERSONAL'){
				$buscar_1 = $this->solicitud_model->validar_agregar_agenda_telefono('numero="'.$data["numero"].'" AND fuente = "PERSONAL DECLARADO"');
			}else if($data["fuente"] == 'REFEERENCIA'){
				$buscar_2 = $this->solicitud_model->validar_agregar_agenda_telefono('documento="'.$data["documento"].'" AND  numero="'.$data["numero"].'" AND fuente = "PERSONAL"');
			}else if($data["fuente"] == 'PERSONAL WHATSAPP'){
				$buscar_3 = $this->solicitud_model->validar_agregar_agenda_telefono('documento="'.$data["documento"].'" AND numero="'.$data["numero"].'" AND fuente = "PERSONAL LLAMADA"');
			}else if($data["fuente"] == 'PERSONAL LLAMADA'){
				$buscar_4 = $this->solicitud_model->validar_agregar_agenda_telefono('documento="'.$data["documento"].'" AND numero="'.$data["numero"].'" AND  fuente = "PERSONAL WHATSAPP"');
			}else{
				$buscar_5 = $this->solicitud_model->validar_agregar_agenda_telefono('documento="'.$data["documento"].'" AND numero="'.$data["numero"].'" AND fuente ="'.$data["fuente"].'"');
			}

			if(isset($buscar_1)){
				if($buscar_1 >0 ){
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El número ya existe o el Tipo de número PERSONAL ya esta agendado"];
				}else
				{	
					$result = $this->solicitud_model->agregar_telefono_solicitante($data);

					if ($result > 0) {
						if ($data['fuente'] == 'PERSONAL'){
							$this->solicitud_model->edit($id,['telefono'=>$data['numero']]);
						}
						$status = parent::HTTP_OK;
						$response = ['status' => $status,'update_principal'=>true,'message' => "Número agregado a la agenda", 'agenda_telefonica' => $data];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "No se pudo agregar a la agenda"];
					}
				}
			}
			if(isset($buscar_2)){
				if($buscar_2 > 0){
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El número de REFERENCIA no puede ser un numero PERSONAL"];
				}else
				{	
					$result = $this->solicitud_model->agregar_telefono_solicitante($data);

					if ($result > 0) {
						$status = parent::HTTP_OK;
						$response = ['status' => $status,'message' => "Número agregado a la agenda", 'agenda_telefonica' => $data];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "No se pudo agregar a la agenda"];
					}
				}
			}

			if(isset($buscar_3)){
				if($buscar_3 > 0){
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El número PERSONAL WHATSAAP no puede ser un numero PERSONAL LLAMADA"];
				}else
				{	
					$result = $this->solicitud_model->agregar_telefono_solicitante($data);

					if ($result > 0) {
						$status = parent::HTTP_OK;
						$response = ['status' => $status,'message' => "Número agregado a la agenda", 'agenda_telefonica' => $data];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "No se pudo agregar a la agenda"];
					}
				}
			}

			if (isset($buscar_4)){
				if($buscar_4 > 0){
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El número PERSONAL LLAMADA no puede ser un numero PERSONAL WHATSAAP"];
				}else
				{	
					$result = $this->solicitud_model->agregar_telefono_solicitante($data);

					if ($result > 0) {
						$status = parent::HTTP_OK;
						$response = ['status' => $status,'message' => "Número agregado a la agenda", 'agenda_telefonica' => $data];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "No se pudo agregar a la agenda"];
					}
				}
			}

			if(isset($buscar_5)){
				if($buscar_5 > 0){
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "El número se encuentra AGENDADOS"];
				}else
				{	
					$result = $this->solicitud_model->agregar_telefono_solicitante($data);

					if ($result > 0) {
						$status = parent::HTTP_OK;
						$response = ['status' => $status,'message' => "Número agregado a la agenda", 'agenda_telefonica' => $data];					
					} else {
						$status = parent::HTTP_INTERNAL_SERVER_ERROR;
						$response = ['status' => $status, 'message' => "No se pudo agregar a la agenda"];
					}
				}
			}
		
		$this->response($response);
	}

	public function get_update_numero_get($id){
		if($id!= null)
		{			
			$result = $this->solicitud_model->get_agenda_personal_solicitud(["id" =>$id]);
			if ($result > 0) 
			{
				$status = parent::HTTP_OK;
				$response = ['status' => $status, 'message' => 'Exito', 'data' => $result];					
			} else {
				$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => $status, 'message' => "No existe id"];
			}
		}else {
				$status = parent::HTTP_OK;
				$response['status']['code'] = $status;
				$response['status']['ok'] = FALSE;
				$response['message'] = $this->form_validation->error_array();
		}
		$this->response($response);
	}
		
	public function agendar_mail_solicitante_post(){
		if($this->post('documento')!= null){
			$data =[
					'documento' => $this->post('documento'),
					'cuenta' => $this->post('cuenta'),
					'fuente' => strtoupper($this->post('fuente')),
					'contacto' => $this->post('contacto'),
					'estado' => $this->post('estado'),
				];
			// dump($data);die;
			$buscar= $this->solicitud_model->validar_agregar_agenda_mail($data);
			
			if($buscar>0){
					$status = parent::HTTP_OK;
					$response = ['status' => $status,'ok'=>FALSE, 'message' => "El e-mail ya existe o el Tipo de MAIL PERSONAL ya esta agendado"];
					// var_dump('El número ya existe o el Tipo de número PERSONAL ya esta agendado');
			}else
			{		
				$result = $this->solicitud_model->agregar_mail_solicitante($data);

				if ($result > 0) {
					$status = parent::HTTP_OK;
					$response = ['status' => $status, 'ok'=>TRUE, 'message' => "E-mail agregado a la agenda", 'agenda_telefonica' => $data];
					// var_dump('Número agregado a la agenda');

				} else {
					$status = parent::HTTP_INTERNAL_SERVER_ERROR;
					$response = ['status' => $status, 'message' => "No se pudo agregar a la agenda"];
					// var_dump('No se pudo agregar a la agenda');

				}
			}
		}
		$this->response($response);
	}

	public function update_edit_agenda_tlf_post(){
		$id= $this->post('id');
		$data =[
				'numero' => $this->post('numero'),
				'tipo' => strtoupper($this->post('tipo')),
				'fuente' => strtoupper($this->post('fuente')),
				'contacto' => $this->post('contacto'),
				'estado' => $this->post('estado'),
				'ciudad' => $this->post('ciudad'),
				'departamento' => $this->post('departamento'),
				'id_parentesco'=>$this->post('id_parentesco'),
				'verificado_llamada'=>$this->post('verificado_llamada'),
				'verificado_sms'=>$this->post('verificado_sms'),
				'verificado_whatsapp'=>$this->post('verificado_whatsapp'),
				'llamada'=>$this->post('llamada'),
				'sms'=>$this->post('sms'),
				'whatsapp'=>$this->post('whatsapp'),
			];
			$result = $this->solicitud_model->update_telefono_solicitante($id,$data);
			
			if ($result > 0) {
				$status = parent::HTTP_OK;
				$response = ['status' => $status, 'message' => "Se actualizo datos del contacto"];					
			} else {
				$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => $status, 'message' => "No se pudo actualizar contacto"];
			}
		$this->response($response);
	}

 	public function get_mail_template_html_post(){
        $id_template= $this->input->post('id_template_mail');
        $documento = $this->input->post('documento');
        $aux5 = $this->solicitud_model->get_template_mail(["id" =>$id_template]);
        $consulta_con_variable = $aux5[0]['query_contenido'];
        $html_view = $aux5[0]['html_contenido'];
        $respuesta = [];
        $message =  $html_view;
		$status = parent::HTTP_OK;

        if (isset($consulta_con_variable)){
            $consulta_sin_variable= str_replace('$documento', $documento, $consulta_con_variable);
            $rs_result = $this->solicitud_model->rs_result($consulta_sin_variable);
            if( count($rs_result['result']) > 0){
                foreach ($rs_result['result'] as $key=> $value){
                        foreach ($value as $key2 => $value2) {
                            ${$key2}= $value2;
                            $search = '$'.$key2;
                            $message= str_replace($search, $value2, $message);
                            $respuesta=['status'=>$status,'ok' => TRUE,'message' =>$message ,'deshabilitar' => FALSE];

                        }
                } 

            }else{
                // $message = '<div style="text-align:center;font-size: 3em;"><h1 style="color:#6b2378;font-family:Verdana; margin: 14%;">No posee datos para PREVIEW y ENVIAR este Template</h1></div>';
                $message = '<div style="text-align:center;font-size: 3em;"><h1 style="color:#6b2378;font-family:Verdana; margin: 14%;">No posee datos para PREVIEW y ENVIAR este Template</h1></div>';
 
                $respuesta=['status'=>$status,'ok' => TRUE,'message' => $message ,'deshabilitar' => TRUE];
                }   
        }else{
            $message = '<div style="text-align:center;font-size: 3em;"><h1 style="color:#6b2378;font-family:Verdana; margin: 14%;">No posee datos para PREVIEW y ENVIAR este Template</h1></div>';
                            $respuesta=['status'=>$status,'ok' => TRUE,'message' =>$message ,'deshabilitar' => TRUE];

        }
         $this->response($respuesta);
        // var_dump($respuesta);
        // echo json_encode($respuesta);
    }

	public function get_template_data_post() {
        $data = $this->input->post();

		$exist = $this->solicitud_model->get_payment_link($data['id_cliente']);
		if (count($exist) == 0) {

			if ( !empty($data['id_cliente'])) 
				$id_cliente_encrypted = AUTHORIZATION::encodeData( $data['id_cliente'] );

			$end_point = CHATBOT_URL .'chatbot/chatbot_live/generate_urlpago';
			$Medios_Pago = array('PSE', 'efectivo');
			foreach ($Medios_Pago as $value){
				if ( !empty($data['id_cliente'])) 
				$medio_pago_encrypted = AUTHORIZATION::encodeData( $value );
				$resp = Requests::post($end_point, [], ['id_cliente'  => $id_cliente_encrypted ,'medio_pago'  => $medio_pago_encrypted ]);
				$dataresp = json_decode($resp->body);
				$insert_data[] = array(
					'id_cliente' => $data['id_cliente'],
					'link_payment'=> $dataresp->url,
					'type_payment'=> $value
				  );
			}
			$response['data'][] = $this->solicitud_model->insert_payment_link($insert_data);
		}else{
			$response['success'] = true;
			$response['title_response'] = "ya registrado";
		}

		$this->response($response);

	}

	public function update_primer_reporte_agenda_tlf_get(){
		$result= $this->solicitud_model->update_primer_reporte_agenda_tlf();
		if ($result > 0) {
			$status = parent::HTTP_OK;
			$response = ['status' => $status, 'message' => "Se actualizo datos del contacto"];					
		} else {
			$status = parent::HTTP_INTERNAL_SERVER_ERROR;
			$response = ['status' => $status, 'message' => "No se pudo actualizar contacto"];
		}
		$this->response($response);    
	}

	public function update_niveles_get($id)
	{
		if (!is_null($id) && $id > 0) {
			$end_point = URL_MEDIOS_PAGOS."maestro/CronCreditos/update_nivel?id_cliente=".$id;
			$request = Requests::get($end_point, array(),array());
			$response = json_decode($request->body);

		} else{
			$status = parent::HTTP_OK;
			$response = ['status' => $status, 'ok' => FALSE, 'message' => "No se pudo actualizar el nivel del cliente"];
		}

		$this->response($response);
	}

	public  function send_link_biometria_post()
	{
 			//var_dump($this->post());die;
			$telefono = "";
			if ( !is_null($this->post('solicitud'))) {
				$id_encript =  AUTHORIZATION::encodeData( $this->post('solicitud') );

				if ( !empty($this->post('numero'))) {
					$telefono = AUTHORIZATION::encodeData( $this->post('numero') );
				}
				$endPoint = URL_API_IDENTITY."api/veriff/auth/sendurlsession/";
				$resp = Requests::post($endPoint, [], ['id_solicitud'  => $id_encript ,'telefono' => $telefono  ]); 
				if ($resp->success) {

					$response = json_decode($resp->body);
					
				} else{
					$response['success'] = false;
					$response['title_response'] = "error de comunicacion";
				}

			} else {
				$response['success'] = false;
				$response['title_response'] = "solicitud invalida";
			}

			$status = parent::HTTP_OK;
			$this->response($response, $status);
	}
	
	public function checkSolicitudHasTrack_post()
	{
		$idSolicitud = $this->post('id_solicitud');
		$result = $this->tracker_model->checkSolicitudHasTrack($idSolicitud);

		$status = parent::HTTP_OK;
		$response = ['status' => $status, 'ok' => TRUE, 'data' => $result];

		$this->response($response);
	}
	
	public function checkSolicitudHasTrackToday_post()
	{
		$id_solicitud = $this->post('id_solicitud');
		$result = $this->tracker_model->checkSolicitudHasTrackToday($id_solicitud);
		
		$status = parent::HTTP_OK;
		$response = ['status' => $status, 'ok' => TRUE, 'data' => $result];
		
		$this->response($response);
	}

	/***
    * CASOS POR VISAR
    * Camilo Franco
    */

    public function casosPorVisar_get() 
    {
		$status = parent::HTTP_OK;
		$response = ['status' => $status, 'ok' => FALSE, 'data' => 'No se pudo obtener la infomación de casos pendientes de visar.'];

        $fecha_actual = date("Y-m-d");
		// $fecha_actual = '2022-03-19';
        $fecha_menos_ocho_dia = date("Y-m-d",strtotime($fecha_actual."- 7 days"));
		
		$result = array();
        
		for($i = $fecha_menos_ocho_dia; 
		$i <= $fecha_actual; 
		$i = date("Y-m-d",strtotime($i."+ 1 days")))
        {
			$indicadores = $this->solicitud_model->get_por_visar($i);

			$total_dia = $indicadores[0]["sumatoria"] + $indicadores[1]["sumatoria"] + $indicadores[2]["sumatoria"];
			if( $total_dia > 0){
				$fecha = date('d/m/y',strtotime($i));
				$aux = [
						'fecha_sin_format' => $i,
						'fecha' => $fecha,
						'valores' => $indicadores
					];
				array_push( $result, $aux);
			}
		}

		$autTotal = 0;
		$depTotal = 0;
		$indTotal = 0;
		$total = 0;
		foreach ($result as $dia) {
			$autTotal += $dia['valores'][0]['sumatoria'];
			$depTotal += $dia['valores'][1]['sumatoria'];
			$indTotal += $dia['valores'][2]['sumatoria'];
		}
		$total    = $autTotal + $depTotal + $indTotal;
		$totalesVisar = [
			'autTotal' => $autTotal,
			'depTotal' => $depTotal,
			'indTotal' => $indTotal,
			'total' => $total,
		];

		if( isset($result) || $result == '') {
			$status = parent::HTTP_OK;
			$response = ['status' => $status, 'ok' => TRUE, 'data' => $result, 'totalesVisar' => $totalesVisar];
		}

		$this->response($response);
    }

/***************************************************************************/
// VALIDATIONS
/***************************************************************************/
	public function _validate_save_input()
	{

	}

	public function _validate_registro_gestiones_obligatorias()
	{
	
		$this->form_validation->set_rules('tipoOperador', 'Tipo operador', 'required|numeric');
		$this->form_validation->set_rules('segEjecucion', 'Segundos de ejecución', 'required|numeric');
		$this->form_validation->set_rules('minSolicitud', 'Minutos de consulta de solicitudes', 'required|numeric');
		$this->form_validation->set_rules('minGestion', 'Minutos de la gestión', 'required|numeric');
		$this->form_validation->set_rules('minutosExtension', 'Minutos de extensión', 'required|numeric');
		$this->form_validation->set_rules('extensionesConsecutivas', 'Extensiones consecutivas', 'required|numeric');
		$this->form_validation->set_rules('estado', 'Estado', 'required|numeric');
		$this->form_validation->set_rules('porcentajePreventivo', 'Estado', 'required|numeric');
		$this->form_validation->set_rules('segundosAlerta', 'Estado', 'required|numeric');
		$this->form_validation->set_rules('porcentajeAlerta', 'Estado', 'required|numeric');
		$this->form_validation->set_rules('minGestionChats', 'Estado', 'required|numeric');
		$this->form_validation->set_rules('minProcesoObligatorio', 'Estado', 'required|numeric');
		$this->form_validation->set_rules('diasBusqueda', 'Estado', 'required|numeric');
		$this->form_validation->set_rules('horaUltimaGestion', 'Estado', 'required|numeric');
		$this->form_validation->set_rules('minDocChats', 'Estado', 'required|numeric');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	public function _validate_update_data_client()
	{
		$this->form_validation->set_rules('id_solicitud', 'ID de la solicitud', 'required');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	public function data_enviar_post()
	{
		$canal = intval($this->input->post('canal'));
		$documento = intval($this->input->post('documento'));
		$fuente = intval($this->input->post('fuente'));
		$medio_pago = $this->input->post('medio_pago');
		$cod_convenio = intval($this->input->post('cod_convenio'));

		if (ENVIRONMENT == 'development') {
			$telefono = "+5493884133854";
		}else{
			if (strstr($this->input->post('telefono'), "+54")) {
				$telefono = $this->input->post('telefono');
			}else{
				$telefono = PHONE_COD_COUNTRY.intval($this->input->post('telefono'));
			}
		}

		$data = $this->solicitud_model->get_data_enviar($documento);
		$id_cliente = $data[0]['id'];
		$dataPost = 
		[
			'canal' => $canal,
			'fuente' =>  $fuente,
			'id_cliente' => $id_cliente,
			'medio_pago' => $medio_pago,
			'codigo_convenio' => $cod_convenio,
			'telefono' => $telefono
		];

		$search_acuerdo = $this->solicitud_model->search_acuerdo($id_cliente, $medio_pago);

		if (!empty($search_acuerdo)) {
			$dataPost["monto_pagar"] = intval($search_acuerdo[0]["monto_acuerdo"]);
		}else{
			$dataPost["monto_pagar"] = intval($data[0]['monto_cobrar']);
		}

		// if ($medio_pago == "baloto") {
		// 	if (!empty($search_acuerdo)) {
		// 		$datosRefe = [
		// 			"id" => $search_acuerdo[0]["id"],
		// 			"metodoDePago" => "baloto",
		// 			"tipo" => "A"
		// 		];
		// 	}else{
		// 		$datosRefe = [
		// 			"id" => $data[0]["credito_detalle_id"],
		// 			"metodoDePago" => "baloto",
		// 			"tipo" => "C"
		// 		];
		// 	}
			
		// 	$end = URL_BACKEND.'api/ApiPrestamo/registrarPayValida';
		// 	$referencia = $this->curl($end, 'POST', $datosRefe);
		// 	$dataRef = json_decode($referencia);
		// 	$dataPost["fecha_vencimiento"] = $dataRef->vencimiento;
		// 	$dataPost["documento"] = $dataRef->referencia;
		// }else{
			$dataPost["documento"] = $documento;
		// }

		$endPoint = URL_CAMPANIAS.'EnvioDescargaInfoPago/envioInfoPago';
		$result = $this->curl($endPoint, 'POST', $dataPost);
		$respuesta = json_decode($result);
		if(isset($respuesta->sms) || $respuesta->status == 200){
			$rs_result["status"] = parent::HTTP_OK;
			$rs_result["mensaje"] = "Se realizo el envió correctamente";
		}else{
			$rs_result["status"] = parent::HTTP_BAD_REQUEST;
			$rs_result["mensaje"] = $respuesta->data;
		}
		echo json_encode($rs_result);
	}

	public function buscarCredito_post()
	{
		$documento = intval($_POST['documento']);
		$buscar = $this->solicitud_model->get_data_enviar($documento);
		if (empty($buscar)) {
			echo json_encode(false);
		}else{
			echo json_encode(true);
		}
	}

	public function transferenciaRechazada_post()
    {
		$data['data']= $this->solicitud_model->transferenciaRechazada($this->session->userdata('idoperador'), $this->session->userdata('tipo_operador'));
		$status = parent::HTTP_OK;
		$this->response($data, $status);
    }
}
