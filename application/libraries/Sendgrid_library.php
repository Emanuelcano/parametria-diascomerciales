<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sendgrid_library
{
	public function __construct()
	{
		$this->endpoint = base_url();
	}

	public function send_sms($id_solicitud)
	{
		$params = array('id_solicitud' => $id_solicitud);
		return $this->_curl_sendgrid($this->endpoint.'EnviarMailCampania/EnvioMailVerificacion', 'POST', $params);
	}

	private function _curl_sendgrid($endPoint, $method='GET', $params=[])
	{
		$curl = curl_init();
		$options[CURLOPT_URL] = $endPoint;
		$options[CURLOPT_CUSTOMREQUEST] = $method;
		$options[CURLOPT_RETURNTRANSFER] = TRUE;
		$options[CURLOPT_ENCODING] = '';
		$options[CURLOPT_MAXREDIRS] = 10;
		$options[CURLOPT_TIMEOUT] = 30;
		$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

		if(strtoupper($method) == 'POST')
		{
			$options[CURLOPT_POSTFIELDS ] =   http_build_query($params);
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