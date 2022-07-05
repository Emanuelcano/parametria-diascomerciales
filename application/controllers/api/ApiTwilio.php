<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiTwilio extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		// Models
		$this->load->model('solicitud_m', 'solicitud_model', TRUE);
		// Library
		$this->load->library('Twilio_library');
	}

	public function send_sms_validation_get($id_solicitud)
	{
		$solicitud = $this->solicitud_model->simple_list(['id' => $id_solicitud]);
        $phone_number = PHONE_COD_COUNTRY.$solicitud[0]['telefono'];
        $msg =  'Tu codigo de validacion celular es: '. $solicitud[0]['codigo_enviado_sms'];

		$status = parent::HTTP_OK;
		$response['sms'] = $this->twilio_library->simple_sms_message($phone_number, $msg);
 		$response['status']['code'] = parent::HTTP_OK;
		$response['status']['ok'] = TRUE;
		$this->response($response, $status);
	}

	public function send_sms_token_login_get($phone_number, $token)
	{
        $msg = "Tu codigo de inicio es: $token .";

		$status = parent::HTTP_OK;
		$response['sms'] = $this->twilio_library->simple_sms_message($phone_number, $msg);
 		$response['status']['code'] = parent::HTTP_OK;
		$response['status']['ok'] = TRUE;
		$this->response($response, $status);
	}

	public function send_sms_massive_same_msg_post()
	{
		$input_data = json_decode(trim(file_get_contents('php://input')), true);
		$list_phone = $input_data['phones'];
		$msg = $input_data['messages'];
		
		$status = parent::HTTP_OK;
		$response['sms'] = $this->twilio_library->massive_sms_same_message($list_phone, $msg);
 		$response['status']['code'] = parent::HTTP_OK;
		$response['status']['ok'] = TRUE;
		$this->response($response, $status);
	}

	public function send_sms_massive_distinct_msg_post()
	{
		$input_data = json_decode(trim(file_get_contents('php://input')), true);
				
		$status = parent::HTTP_OK;
		$response['sms'] = $this->twilio_library->massive_sms_distinct_message($input_data);
 		$response['status']['code'] = parent::HTTP_OK;
		$response['status']['ok'] = TRUE;
		$this->response($response, $status);
	}
}