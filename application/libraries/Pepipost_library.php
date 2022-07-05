<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pepipost_library
{
	public function __construct()
	{
		 $this->CI =& get_instance();
        $this->CI->load->model('InfoBipModel', TRUE);
        $this->endpoint = base_url();
	}
   
	public function curl_pepipost($personas,$template,$subject)
	{	
		$data = array(                
            'personalizations' => $personas,
            'from' => array(
                'fromEmail' => 'no-reply@solventa.co',
                'fromName' => 'SOLVENTA'
            ),
            'subject' => $subject,
            'content' => '%20',
            'templateId' => $template,
            'settings' => array(
                'clicktrack' => 1,
                'opentrack' =>1
            )
        );
	    $a = json_encode($data,true);
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.pepipost.com/v2/sendEmail",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $a,
        CURLOPT_HTTPHEADER => array(
        "api_key: f9a5f6fe35da420771aab0c95fd9910a",
        "content-type: application/json"
        ),
        ));

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