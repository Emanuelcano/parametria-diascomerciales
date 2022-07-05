<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wolkvox_library
{
	private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
		
		
    }


/*
|--------------------------------------------------------------------------
| Area de Componente llamadas Ing. Esthiven Garcia
|--------------------------------------------------------------------------
*/

/*
|-------------------------------------------------------------------------------------------------------------------
| Area de Reportes Ing. Esthiven Garcia
| esta seccion consume las acciones de integracion en componente de llamadas como llamar colgar mute etc.
| Api documentacion link: https://www.wolkvox.com/apis.php seccion API Agentes.
|-------------------------------------------------------------------------------------------------------------------
*/


 public function llamada_manual($tlf_cliente,$id_cliente){
        
        //var_dump($tlf_cliente,$id_cliente);die;
        $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=dial&phone=".$tlf_cliente."&id_customer=".$id_cliente;
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        }

        return $res;
        
    }

    public function colgar_llamada()
    {

        $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=haup";
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        }

        return $res;        

    }

public function mutear_llamada()
    {
            
     $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=mute";
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        
        
        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        }   
        
        return $res; 
        
    }

    public function desmutear_llamada()
    {
           
            $curl = curl_init();
            $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=mute";
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';
            $options[CURLOPT_RETURNTRANSFER] = TRUE;
            $options[CURLOPT_ENCODING] = '';
            $options[CURLOPT_MAXREDIRS] = 10;
            $options[CURLOPT_TIMEOUT] = 30;
            $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
            curl_setopt_array($curl,$options);
            $res = json_decode(curl_exec($curl));
            $err = curl_error($curl);
            curl_close($curl);
            
            if ($err)
            {
              return 'cURL Error #:' . $err;die;
            }  

            return $res; 

    }


    public function codificar_only($cod1,$cod2,$commets)
    {
        
        $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=codd&cod=".$cod1."&cod2=".$cod2."&comm=".$commets;
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        }

        return $res; 

    }

    
    public function codificar_and_ready($cod1,$commets)
    {
        
        $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=chur&cod=".$cod1."&comm=".$commets;
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        //var_dump($res);die;
        

        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        }

        return $res; 

    }

    public function llamada_en_espera()
    {
        $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=hold";
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        } 

        return $res; 


    }

    public function reprogramar_llamada($tlf_cliente)
    {
        $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=rcal&date=".date("YmdHis")."&dial=".$tlf_cliente."&type_recall=auto";
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        

        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        } 

        return $res; 

    }

    public function llamada_auxiliar($cliente)
    {
        $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=diax&phone=".$cliente;
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        }

        return $res; 
    }

    public function transferir_llamada($num_trans)
    {
        $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=tran&phone=".$num_trans;
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        }

        return $res; 
    }

public function preciona_dtmf($num_press)
    {
        $curl = curl_init();
        $options[CURLOPT_URL] = "http://localhost:8084/apiagentbox?action=keyp&key=".$num_press;
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 30;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
        curl_setopt_array($curl,$options);
        $res = json_decode(curl_exec($curl));
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err)
        {
          return 'cURL Error #:' . $err;die;
        }

        return $res; 
    }


/*
|--------------------------------------------------------------------------
| Area de Reportes Ing. Esthiven Garcia
|--------------------------------------------------------------------------
*/

/*
|-------------------------------------------------------------------------------------------------------------------
| Area de Reportes Ing. Esthiven Garcia
| Reporte de llamadas generales recoge resultados de las llamadas y genera un track del response en json
| Api documentacion link: https://www.wolkvox.com/apis.php seccion ApiCampañas numero #6
|-------------------------------------------------------------------------------------------------------------------
*/

	public function campanias_general($bhour,$fhour)
    {
        
        $curl = curl_init();
        $options[CURLOPT_URL] = URL_CAMPANIASGRAL.$bhour."&date_end=".$fhour;
        $options[CURLOPT_CUSTOMREQUEST] = 'GET';
        $options[CURLOPT_RETURNTRANSFER] = TRUE;
        $options[CURLOPT_ENCODING] = '';
        $options[CURLOPT_MAXREDIRS] = 10;
        $options[CURLOPT_TIMEOUT] = 120;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

        curl_setopt_array($curl,$options);
        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $aux=[];
       
        $pieces = explode("REG|", $res);
        foreach ($pieces as $key => $value) {
            array_push($aux, trim($value));
        }

        $pieces= $aux;

        $piesa_dos = $pieces; // piece2
        $piesa_dos = array_map('rtrim', $piesa_dos);
        $piesa_dos = array_filter($piesa_dos, "strlen");

        
        $result = array_map(function($val) {
            return explode('|', $val);
        }, $piesa_dos);

        //var_dump($result);die;
        
        
        $data = [];
        return $result;

        if ($err)
        {
          echo 'cURL Error #:' . $err;die;
        }

        
        
    }

    public function massive_transfer_data_to_campaing_ventas($rsQuery)
    {


    	$curl = curl_init();
            $options[CURLOPT_URL] = URL_TRANSFER.$rsQuery;
            $options[CURLOPT_CUSTOMREQUEST] = 'GET';
            $options[CURLOPT_RETURNTRANSFER] = TRUE;
            $options[CURLOPT_ENCODING] = '';
            $options[CURLOPT_MAXREDIRS] = 10;
            $options[CURLOPT_TIMEOUT] = 120;
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = false;
            $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

            curl_setopt_array($curl,$options);
            $res = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            return $res;

            if ($err)
            {
              echo 'cURL Error #:' . $err;die;
            }

        
        
    }

    

	

}