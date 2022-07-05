<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uanataca_library
{

    const TIMEOUT = 120;

	public function __construct()
	{
		//$this->CI =& get_instance();
		$this->ws = PAGARE_URL;
        set_time_limit(self::TIMEOUT);
	}

	public function create($id_solicitud)
	{
		$params = array('solicitud_id' => $id_solicitud);
		$endpoint = '/api/uanataca/pagare/actualizar_pagares/'.$id_solicitud;
		return $this->_curl_uanataca($this->ws.$endpoint, 'POST', $params);
	}

	public function sign($id_solicitud)
	{
		$params = array('solicitud_id' => $id_solicitud, 'password' => $password);
		return $this->_curl_deceval($this->endpoint.'pagareapi/firmarPagare', 'POST', $params);
	}

	public function anular_pagare($id_solicitud, $motivos)
	{
		$params = array('solicitud_id' => $id_solicitud,'motivo_anulacion' => $motivos);
		return $this->_curl_deceval($this->endpoint.'pagareapi/anularPagare', 'POST', $params);
	}

	public function cancelar_pagare($id_solicitud)
	{
		$params = array('solicitud_id' => $id_solicitud);
		return $this->_curl_deceval($this->endpoint.'pagareapi/cancelarPagare', 'POST', $params);
	}

	public function reenviar_pagare($id_solicitud)
	{
		$params = array('solicitud_id' => $id_solicitud);
		return $this->_curl_deceval($this->endpoint.'pagareapi/reenviarPagare', 'POST', $params);
	}

	private function _curl_uanataca($endPoint, $method='GET', $params=[])
	{
		$curl = curl_init();
		$options[CURLOPT_URL] = $endPoint;
		$options[CURLOPT_CUSTOMREQUEST] = $method;
		$options[CURLOPT_RETURNTRANSFER] = TRUE;
		$options[CURLOPT_ENCODING] = '';
		$options[CURLOPT_MAXREDIRS] = 10;
		$options[CURLOPT_TIMEOUT] = self::TIMEOUT;
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
