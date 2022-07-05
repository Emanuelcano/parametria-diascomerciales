<?php

class Whatsapp extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('is_logged_in')) {
            // MODELS
            $this->load->model('chat');
            // LIBRARIES
            // $this->load->library('form_validation');
            // HELPERS
            // $this->load->helper('date');
        } else {
            redirect(base_url('login'));
        }  
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    public function index() {
        $link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/', $link);
        $permisos = FALSE;
        foreach ($this->session->modulos as $key => $value):
            if($value->url == $link_array[1])
            {
                $permisos = TRUE;
                break;
            } 
        endforeach;
        if ($permisos) {

            $title['title'] = 'WhatsApp';
            $this->load->view('layouts/adminLTE', $title);

            $canalChat = $this->chat->getCanalChat();
            $operadores = $this->chat->getOperadores();
            $data = array(
                'canalChat'  => $canalChat,
                'operadores' => $operadores
            );
            $this->load->view('whatsapp/whatsapp_view', $data);
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }
}
