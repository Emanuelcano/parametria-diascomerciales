<?php
defined('BASEPATH') or exit('No direct script access allowed');

set_time_limit(0);

class Generacion extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('formato_helper');  
        $this->load->helper('file');
        $this->load->helper('requests_helper'); 
        
        $this->load->library('form_validation');
        //$this->load->library('logger');
    }

    public function generarDebitos()
    {
        $post = $this->input->post();

        $endpoint = URL_MEDIOS_PAGOS . 'bancolombia/GeneracionDebitoAutomatico/generar_vista_debitos'; 

        if($post['action'] == "enviar")
            $endpoint = URL_MEDIOS_PAGOS . 'bancolombia/GeneracionDebitoAutomatico/generar_archivos_debitos';  

        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($post)
        {
            curl_setopt($fp, CURLOPT_TIMEOUT, 600);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $post);
        });

        $response = Requests::post($endpoint, array('Accept' => 'application/json'), array(), array('hooks' => $hooks));

        $return = $response->body;

        if($post['action'] == "enviar")
            $return = json_encode($response->body);  

        echo $return;
    }

    public function descargarArchivos()
    {
        $post = $this->input->post();

        $endpoint = URL_MEDIOS_PAGOS . 'bancolombia/GeneracionDebitoAutomatico/generar_archivos_descarga';  

        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($post)
        {
            curl_setopt($fp, CURLOPT_TIMEOUT, 600);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $post);
        });

        $response = Requests::post($endpoint, array('Accept' => 'application/json'), array(), array('hooks' => $hooks));

        echo $response->body;
    }

    public function generarPreNotificacionBancoBogota()
    {
        $post = $this->input->post();
        //$post = "";

        $endpoint = URL_MEDIOS_PAGOS . 'bancobogota/GeneracionBancoBogota/generar_pre_notificacion';  

        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($post)
        {
            curl_setopt($fp, CURLOPT_TIMEOUT, 600);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $post);
        });

        $response = Requests::post($endpoint, array('Accept' => 'application/json'), array(), array('hooks' => $hooks));

        echo $response->body;
    }

}