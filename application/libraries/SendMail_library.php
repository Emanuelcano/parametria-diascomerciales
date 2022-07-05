<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SendMail_library
{

    public function __construct()
    {
		$this->endpoint = URL_SEND_MAIL.'api/sendmail';
    }
	/**
	 * [Envio de Mail de codigo de validacion]
	 * @param  [email]  	[Email]
	 * @param  [name]       [Nombres]
	 * @param  [lastName]   [Apellidos]
	 * @param  [code]       [Codigo de validacion]
	 * @return [Array]		[Detalles del resultado del envio]
	 */
	public function email_validation_code($email, $name, $lastName, $code)
	{
		$params = $this->get_params();
		$params['template'] = 1;
		$params['to'] = $email;
    	$params['subject'] = "Validación de Cuenta Solventa";
		$params['name'] = $name;
		$params['lastname'] = $lastName;
		$params['code'] = $code;
		$params['message'] = '.';


		return $this->_push_mail($params);

		$response['email_sendmail'] = $this->_push_mail($params);
 		$response['status']['code'] = 200;
		$response['status']['ok'] = TRUE;
		return $response;
	}

	private function _push_mail($params)
	{
		if(ENVIRONMENT == 'development')
		{
			$params['to'] = TEST_EMAIL;
		}
       
        return $this->curl_sendmail($this->endpoint,'POST', $params);
	}

	private function curl_sendmail($endPoint, $method='GET', $params=[])
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

	public function get_params()
	{
		 // if it is a multipart forma data form
        $body = array (
            "jwt" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzMxNjExNjgsImV4cCI6MTU3MzE2NDc2OCwiZGF0YSI6eyJpZCI6IjYyNTgiLCJhZG1pbiI6ZmFsc2UsInRpbWUiOjE1NzMxNjExNjgsInRpbWVUb2xpdmUiOm51bGx9fQ.gsGPp2FXAk4I7KEXPNuleh6kqYP5ahWYud-baZBpOFE",
            "from" => "no-reply@solventa.com",
            'from_name' => 'Solventa SAS',
        );
		return $body;
	}

	public function send_mail2($to,$cc ='',$cco='', $subject, $message = '', $full_path_txt='', $filename = '', $bbc = '', $template = 0)
    {        
        $url_api_medios_de_pago = 'https://sendmail.solventa.co/api/sendmail';   //Produccion
        if($subject!==false){
            $subj = $subject;
        }
        $body = array (
            "jwt"       => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzMxNjExNjgsImV4cCI6MTU3MzE2NDc2OCwiZGF0YSI6eyJpZCI6IjYyNTgiLCJhZG1pbiI6ZmFsc2UsInRpbWUiOjE1NzMxNjExNjgsInRpbWVUb2xpdmUiOm51bGx9fQ.gsGPp2FXAk4I7KEXPNuleh6kqYP5ahWYud-baZBpOFE",
            "from"      => "hola@solventa.com",
            'to'        => $to,
            'cc'        => (!empty($cc))? $cc : $cc ="",
            'cco'       => (!empty($cco))? $cco : $cco = "",
            'from_name' => 'Solventa Colombia',
            'subject'   => $subj,
            'template'  => $template,
            'message'   => $message
        );
        if($bbc!=''){
			$body['cc'] = $bbc;
        }
        if(file_exists($full_path_txt[0])){
            $files[0]['file'] = $full_path_txt[0];
            array_walk($files, function($filePath, $key) use(&$body) {
                $body[$key] = curl_file_create($filePath['file']);
            });
        }
        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_TIMEOUT, 320);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
            curl_setopt($fp, CURLOPT_SSL_VERIFYPEER, false);
        });
        $headers = array('Content-Type' => 'multipart/form-data');
        $response = Requests::post($url_api_medios_de_pago, $headers, array(), array('hooks' => $hooks));
        return (json_decode($response->body));
    }
}