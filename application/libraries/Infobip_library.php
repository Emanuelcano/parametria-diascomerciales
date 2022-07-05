<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Infobip_library
{
	public function __construct()
	{

        $this->CI =& get_instance();
        $this->CI->load->model('InfoBipModel', TRUE);
		$this->endpoint = base_url();
	}

	public function send_sms($id_solicitud)
	{
		$params = array('id_solicitud' => $id_solicitud);
		return $this->_curl_infobip($this->endpoint.'EnviarSms/EnviarSmsVerificar', 'POST', $params);
    }
    
    public function send_smsAcuerdo($id_cliente)
	{
		$params = array('idCliente' => $id_cliente);
		return $this->_curl_infobip(URL_CAMPANIAS.'Cobranzas/acuerdoPago', 'POST', $params);
    }
    
    public function send_smsCobranza($id_cliente, $id_mensaje, $telefono)
	{
		$params = array(
            'idCliente' => $id_cliente,
            'idMensaje' => $id_mensaje,
            'telefono' => $telefono,
        );

		return $this->_curl_infobip(URL_CAMPANIAS.'Cobranzas/SmsCobranzas', 'POST', $params);
    }

    public function send_ivr_codigo_verificacion($solicitud, $plantilla, $modelo)
	{
		$params = array(
            'tipo_envio' => 'ivr',
            'cuerpo_mensaje' => $plantilla,
            'id_solicitud' => $solicitud,
            'modelo_metodo' => $modelo
        );

		return $this->_curl_infobip(URL_CAMPANIAS.'Envio_General', 'POST', $params);
    }
    public function send_sms_codigo_verificacion($solicitud, $plantilla, $modelo)
	{
		$params = array(
            'tipo_envio' => 'sms',
            'cuerpo_mensaje' => $plantilla,
            'id_solicitud' => $solicitud,
            'modelo_metodo' => $modelo
        );

		return $this->_curl_infobip(URL_CAMPANIAS.'Envio_General', 'POST', $params);
    }

    public function send_mailCobranzaDesglose($id_credito, $cuenta)
	{
		$params = array(
            'idCredito' => $id_credito,
            'emails' => $cuenta,
        );
		return $this->_curl_infobip(URL_CAMPANIAS.'Cobranzas/mailDesglose', 'POST', $params);
    }
    public function send_mailCobranza($id_cliente, $id_template, $cuenta)
	{
		$params = array(
            'idCliente' => $id_cliente,
            'idMail' => $id_template,
            'mail' => $cuenta,
        );
		return $this->_curl_infobip(URL_CAMPANIAS.'Cobranzas/MailCobranzas', 'POST', $params);
    }
    
  private function _curl_infobip($endPoint, $method='GET', $params=[])
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


    /**
     * [Envio de un mensaje a muchos destinatarios]
     * @param  [Array] $data [array de arrays destinos, destinatarios y mensajes]
     * @param  [String] $tipoPersona ["Cliente" o "Solicitante"]
     * @return [Array]                [Detalles del resultado del envio]
     */

	public function infobip_curl($data,$tipoPersona,$tipocampaña){ 
        $a = json_encode(['messages' => $data], true);
        $username = 'Solventa';
        $password = 'Havanna12$%98';
        $header = array("Content-Type: application/json", "Accept: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/1/text/multi",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_POSTFIELDS => $a
        ));

        $data2 = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {
            $datosCod = json_decode($data2, true);
            $this->CI->InfoBipModel->insertarRespuestasMultiples($datosCod,$data,$tipoPersona,$tipocampaña); 
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
            $response = ['status' => $response, 'data' => json_decode($data2)];
        }
        return $response;
	}


public function infobip_curl_mora($data,$tipoPersona,$tipocampaña,$creditos){ 
        $a = json_encode(['messages' => $data], true);
        $username = 'Solventa';
        $password = 'Havanna12$%98';
        $header = array("Content-Type: application/json", "Accept: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/1/text/multi",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_POSTFIELDS => $a
        ));

        $data2 = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {
            $datosCod = json_decode($data2, true);
            $this->CI->InfoBipModel->insertarRespuestasMultiplesCreditos($datosCod,$data,$tipoPersona,$tipocampaña,$creditos);
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
            $response = ['status' => $response, 'data' => json_decode($data2)];
        }
        return $response;
    }

    public function infobip_curl_bulk($data){ //Para personas especificas sin necesidad de consulta
        $a = json_encode(['messages' => $data], true);
        $username = 'Solventa';
        $password = 'Havanna12$%98';
        $header = array("Content-Type: application/json", "Accept: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/1/text/multi",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_POSTFIELDS => $a
        ));

        $data2 = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {
            $datosCod = json_decode($data2, true);
            $this->CI->InfoBipModel->insertarRespuestaMultiplesSinDni($datosCod,$data);
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
            $response = ['status' => $response, 'data' => json_decode($data2, true)];
        }
        return $response;
    }

 /**
     * [Envio de un mensaje a un destinatario]
     * @param  [Array] $data [ destino, destinatario y mensaje]
     * @return [Array]                [Detalles del resultado del envio]
     */
    public function infobip_curl_individual($idSolicitud,$text){ 
        $datos = $this->CI->InfoBipModel->solicitantes($idSolicitud);
        if (!empty($datos)) {
            foreach ($datos as $key => $a) {
                if (ENVIRONMENT == 'development') {
                    $telefono = TEST_PHONE_NUMBER;
                } else {
                    $telefono = $a->telefono;
                    $telefono = "+57" . $telefono;
                }
                $dni = $a->documento;                
                $text = sanear_string($text);
                $data = array(
                    'from' => 'Solventa',
                    'to' => $telefono,
                    'text' => $text
                );
            }

        $a = json_encode($data,true);
        $username = 'Solventa';
        $password = 'Havanna12$%98';
        $header = array("Content-Type: application/json", "Accept: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/2/text/single",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_POSTFIELDS => $a
        ));

        $data = curl_exec($curl);
        $err = curl_error($curl);
       
        curl_close($curl);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {
            $datosCod = json_decode($data, true);
            $this->CI->InfoBipModel->insertarRespuesta($datosCod,$dni);
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
        }
        
    }
    else{
        $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
    }
    return $response;
}

 public function infobip_curl_Pago($a,$text){
                if (ENVIRONMENT == 'development') {
                    $telefono = TEST_PHONE_NUMBER;
                } else {
                    $telefono = $a->telefono;
                    $telefono = "+57" . $telefono;
                }
                $dni = $a->documento;            
                $text = sanear_string($text);
                $data = array(
                    'from' => 'Solventa',
                    'to' => $telefono,
                    'text' => $text
                );        
        $a = json_encode($data,true);
        $username = 'Solventa';
        $password = 'Havanna12$%98';
        $header = array("Content-Type: application/json", "Accept: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/2/text/single",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_POSTFIELDS => $a
        ));

        $data = curl_exec($curl);
        $err = curl_error($curl);
       
        curl_close($curl);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {
            $datosCod = json_decode($data, true);
            $this->CI->InfoBipModel->insertarRespuestaConTexto($datosCod,$dni,$text);
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
        }        
    
    return $response;
}

public function infobip_curl_individual_telefono($telefono,$text){  
           
          $text = sanear_string($text);
          $data = array(
                    'from' => 'Solventa',
                    'to' => $telefono,
                    'text' => $text);
        $a = json_encode($data,true);
        $username = 'Solventa';
        $password = 'Havanna12$%98';
        $header = array("Content-Type: application/json", "Accept: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/2/text/single",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_POSTFIELDS => $a
        ));

        $data = curl_exec($curl);
        $err = curl_error($curl);
       
        curl_close($curl);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {
            $datosCod = json_decode($data, true);
            $this->CI->InfoBipModel->insertarRespuesta($datosCod,0);
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
        }        
    
    return $response;
}


public function infobip_curl_cobranzas($data,$mensaje){
        $a = json_encode(['messages' => $data], true);
        $username = 'Solventa';
        $password = 'Havanna12$%98';
        $header = array("Content-Type: application/json", "Accept: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/1/text/multi",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
            CURLOPT_USERPWD => "$username:$password",
            CURLOPT_POSTFIELDS => $a
        ));

        $data2 = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {
            $datosCod = json_decode($data2, true);           
            $this->CI->InfoBipModel->insertarRespuestasMultiples($datosCod,$data,'Cliente','cobranzas');            
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
            $response = ['status' => $response, 'data' => json_decode($data2), 'mensaje' => $mensaje];
        }
        return $response;
    }

}