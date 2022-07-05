<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller {


    public function __construct() {
        parent::__construct();
        
        if ($this->session->userdata("is_logged_in")) {
            $this->load->model("user_model", '', TRUE);
            $this->load->model('tracker_model', 'tracker_model', TRUE);
            $this->load->helper(['jwt', 'authorization']); 
            $this->session->set_userdata('operadorEncrypter', AUTHORIZATION::encodeData($this->session->userdata['idoperador'] ));
        } else {
            redirect(base_url() . "login");
        }
    }

    public function index() 
    {
        if ($this->session->userdata && $this->session->userdata("id_usuario")) 
        {
            $this->session->set_userdata('leyendo_caso', 0);
            /*** Se deja de Trackear en caso de que se haya activado por un Auditor ***/
            $this->tracker_model->set_off_all_operation($this->session->userdata['idoperador']);

            $params = array();
            $params['id_usuario'] = $this->session->userdata("id_usuario");
            $modulos = $this->user_model->get_usuario_modulos($params);
            $this->session->modulos = $modulos;
            $data = array(
                'modulos' => $modulos,
                'title' => 'Dashboard'
            );
            
            $data['isAdmin'] = false;
//            $this->load->view('layouts/adminLTE',$data);

            $this->load->view('layouts/adminLTE__header', $data);
            $this->load->view('dashboard', $data);
            $this->load->view('layouts/adminLTE__footer', $data);

        } else 
        {
            redirect(base_url() . "login");
        }
    }
}