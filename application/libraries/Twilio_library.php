<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Twilio_library
{
	private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
		$this->CI->load->model('solicitud_m', 'solicitud_model', TRUE);
		$this->endpoint = $this->CI->config->item('twilio_endpoint_sms');
    }
	/**
	 * [Envio de un mensaje simple a un destinatario]
	 * @param  [String] $phone_number [Numero de telefono sin caracteristica]
	 * @param  [String] $message      [Mensaje]
	 * @return [Array]                [Detalles del resultado del envio]
	 */
	public function simple_sms_message($phone_number, $message)
	{
		$response['phone_number'] = $phone_number;
		$response['sms_twilio'] = $this->_push_sms($phone_number, $message);
 		$response['status']['code'] = 200;
		$response['status']['ok'] = TRUE;
		return $response;
	}

	public function massive_sms_same_message($list_phones, $message)
	{
		$response = array();
		foreach ($list_phones as $key => $phone_number) {
			$response[$key]['phone_number'] = $phone_number;
			$response[$key]['sms_twilio'] = $this->_push_sms($phone_number, $message);
	 		$response[$key]['status']['code'] = 200;
			$response[$key]['status']['ok'] = TRUE;
		}
		return $response;
	}

	public function massive_sms_distinct_message($list_phones)
	{
		$response = array();
		foreach ($list_phones as $key => $receptor)
		{
			$response[$key]['phone_number'] = $receptor['phone'];
			$response[$key]['sms_twilio'] = $this->_push_sms($receptor['phone'], $receptor['message']);
	 		$response[$key]['status']['code'] = 200;
			$response[$key]['status']['ok'] = TRUE;
		}
		return $response;
	}

	private function _push_sms($phone_number, $message)
	{
		if(ENVIRONMENT == 'development')
		{
			$phone_number = TEST_PHONE_NUMBER;
		}else{
			$phone_number = $phone_number;
		}
       
        $params = ['To' => $phone_number,
        			'From' =>'+12013471132',
        			'Body' => $message];

        return $this->_curl_twilio($this->endpoint,'POST', $params);
	}

	private function _curl_twilio($endPoint, $method='GET', $params=[])
	{
		$curl = curl_init();
		$options[CURLOPT_URL] = $endPoint;
		$options[CURLOPT_CUSTOMREQUEST] = $method;
		$options[CURLOPT_RETURNTRANSFER] = TRUE;
		$options[CURLOPT_ENCODING] = '';
		$options[CURLOPT_MAXREDIRS] = 10;
		$options[CURLOPT_TIMEOUT] = 30;
		$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
		$options[CURLOPT_USERPWD] = "AC6b46e7311003df1b23ce538a408a054c:3c62c73ede6c95e32763adaa450437eb";

		if(strtoupper($method) == 'POST')
		{
			$options[CURLOPT_POSTFIELDS ] =   http_build_query( $params);
		}

		curl_setopt_array($curl,$options);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err)
		{
		  echo 'cURL Error #:' . $err;die;
		}

		return json_decode($response);
	}
	
}