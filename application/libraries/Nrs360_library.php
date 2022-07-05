<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nrs360_library
{
	public function __construct()
	{

        $this->CI =& get_instance();
        $this->CI->load->model('InfoBipModel', TRUE);
		$this->endpoint = base_url();
	}

public function nrs_curl_mora($post,$creditos){  
	//var_dump($post);    
    try {
    	
    	$mensaje = $post['message'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, DASHBOARD_360."api/rest/sms");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/json",
        "Authorization: Basic " . base64_encode('Solventa' . ":" . 'Solvent4')));

        $data = curl_exec($ch);
        $err = curl_error($ch);
       
        curl_close($ch);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {
            $datosCod = json_decode($data, true); 
            $this->CI->InfoBipModel->insertarSmsNrs($datosCod,$creditos,$mensaje);
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
        }        
    
    } catch (Exception $exc) {
        $response = $exc->getTraceAsString();
    }
        return $response;
    }
  }