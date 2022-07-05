<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Neotell_library
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


    public function POSITION_NEOTELL($id_agente,$server){
        if ($server== "activo_neotell")
        {
          $url = UP_CASOS_NEOTELL;
        }else if($server == "activo_neotell_colombia")
        {
          $url = UP_CASOS_NEOTELL_COLOMBIA;
        }
        $curl = curl_init();
        $options[CURLOPT_URL] = $url."?USUARIO=$id_agente";
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

    public function DISPONIBLE_NEOTELL($id_agente,$server)
    {
        if ($server== "activo_neotell")
        {
          $url = DISPONIBLE_NEOTELL;
        }else if($server == "activo_neotell_colombia")
        {
          $url = DISPONIBLE_NEOTELL_COLOMBIA;
        }
        $curl = curl_init();
        $options[CURLOPT_URL] = $url."?USUARIO=$id_agente";
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


     public function SHOWING_CONTAC_NEOTELL($USUARIO,$BASE,$IDCONTACTO,$DATA,$server)
    {
        if ($server== "activo_neotell")
        {
          $url = SHOWING_CONTACT_NEOTELL;
        }else if($server == "activo_neotell_colombia")
        {
          $url = SHOWING_CONTACT_NEOTELL_COLOMBIA;
        }
        $curl = curl_init();
        $options[CURLOPT_URL] = $url."?USUARIO=$USUARIO&BASE=$BASE&IDCONTACTO=$IDCONTACTO&DATA=$DATA";
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



    public function LLAMAR_NEOTELL($id_agente,$TELEFONO,$server)
    {
        if ($server== "activo_neotell")
        {
          $url = LLAMAR_NEOTELL;
        }else if($server == "activo_neotell_colombia")
        {
          $url = LLAMAR_NEOTELL_COLOMBIA;
        }
        $curl = curl_init();
        $options[CURLOPT_URL] = $url."?USUARIO=$id_agente&TELEFONO=$TELEFONO";
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


        public function COLGAR_NEOTELL($id_agente,$server)
    {
        if ($server== "activo_neotell")
        {
          $url = HANGUP_NEOTELL;
        }else if($server == "activo_neotell_colombia")
        {
          $url = HANGUP_NEOTELL_COLOMBIA;
        }
        $curl = curl_init();
        $options[CURLOPT_URL] = $url."?USUARIO=$id_agente";
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


    

	

}