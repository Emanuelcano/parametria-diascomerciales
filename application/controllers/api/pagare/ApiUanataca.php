<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiUanataca extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		// Library
		$this->load->library('pagare/Uanataca_library');
		//modelo
		$this->load->model('Solicitud_m', 'solicitud_model', TRUE);

	}

	public function create_post($id_solicitud)
	{
		//set pagare_firmado to 0
		$this->solicitud_model->edit($id_solicitud, ['pagare_firmado' => 0]);
		$response = $this->uanataca_library->create($id_solicitud);

		$this->response($response, $response->status->code);
	}


	public function reenviar_post($id_solicitud)
	{
		
		$solicitud = $this->solicitud_model->getSolicitudesBy(['id_solicitud'=> $id_solicitud]);
		$codigo = $solicitud[0]->codigo_firma;

		if ($solicitud[0]->tipo_solicitud == 'RETANQUEO') {
			
			if(is_null($solicitud[0]->codigo_firma) || $solicitud[0]->codigo_firma == 0){
				
				$data = [ 'id_solicitud' => $id_solicitud ];
				
				
				$response = $this->generarCodigoPagare($id_solicitud);
				if(isset($response['codigo'])){
					$codigo = $response['codigo'];
					unset($response['codigo']);
				}
			}
			//reconsultamos para obtener el valor del codigo generado
			if($codigo == 0)
				$codigo = $this->solicitud_model->getSolicitudesBy(['id_solicitud'=> $id_solicitud])[0]->codigo_firma;

			if(!is_null($codigo) || $codigo > 0){

				/** ENVIO DE SMS */
				//buscamos el numero de telefono en la agenda
				$numero = $this->solicitud_model->get_agenda_personal_solicitud(['documento' => $solicitud[0]->documento, 'fuentes'=>"'PERSONAL','PERSONAL DECLARADO'", 'limit' =>1, 'order_camp' =>'id', 'estado' => 1]);
				if (!empty($numero)){

					$numero = $numero[0]['numero'];
					$data = [ 	'provider_send' => 1,
								'phone_number' => $numero,
								'otp' => $codigo ];
					$headers = array('Accept' => 'application/json');
					$hooks = new Requests_Hooks();

					$hooks->register('curl.before_send', function($fp){
						curl_setopt($fp, CURLOPT_TIMEOUT, 300);
					});

					$end_point = URL_CAMPANIAS."api/ApiTwilio/send_sms_otp_pagare_new";
					$request = Requests::post($end_point, $headers, $data, array('hooks' => $hooks));
					
					$responseREQ = $request->body;
					$aux=json_decode($responseREQ,TRUE);
					if ($aux['status']['ok']) {	

						$this->solicitud_model->edit($id_solicitud, ['pagare_enviado' => 1]);
						$this->solicitud_model->edit($id_solicitud, ['fecha_envio_pagare' => date('Y-m-d H:i:s')]);
						$response['status']['ok'] = TRUE;
						$response['message'] = 'Codigo generado y enviado';
						
					} else{

						$response['status']['ok'] = FALSE;
						$response['message'] = 'Codigo generado. MSN no enviado';
					}
				} else{

					$response['status']['ok'] = FALSE;
					$response['message'] = 'Cliente sin numero personal activo';
				}
			} else {
				$response['status']['ok'] = FALSE;
				$response['message'] = 'Codigo no existente';
			}
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$this->response($response, $status);

		} else{
			
			$this->solicitud_model->edit($id_solicitud, ['pagare_firmado' => 0]);
			$response = $this->uanataca_library->create($id_solicitud);
			$this->response($response, parent::HTTP_OK);
		}

	}
	private function generarCodigoPagare($id_solicitud)
    {
        $array= [];
		
		$response['status']['ok'] = FALSE;
		$response['message'] = "El codigo no pudo ser generado";

        
        if(!is_null($id_solicitud)){
            $code = rand(100000, 999999);
            $data = array(
                'codigo_firma'     => $code
            );
            $mensaje = 'Codigo Generado: '.$code. ' Id_solicitud: '.$id_solicitud;
            $this->solicitud_model->guardar_solicitud_sms($data, $id_solicitud);
            $response_data = true;
			$response['status']['ok'] = TRUE;
			$response['message'] = "El codigo fue generado generado con exito";
			$response['codigo'] = $code;
        }
		
		return$response;
	}
}