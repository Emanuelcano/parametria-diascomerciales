<?php

class TableroFlujo extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('is_logged_in')) {
            // MODELS
            $this->load->model('tablero_flujo/TableroFlujo_model');
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

            $title['title'] = 'Tablero Flujo';
            $this->load->view('layouts/adminLTE', $title);

            $x = $this->TableroFlujo_model->getTipoSolicitud();
            $data = array('tipoSolicitud' => $x);
            $this->load->view('tablero_flujo/tableroFlujo_view', $data);
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }
}
