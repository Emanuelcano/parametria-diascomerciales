<?php

class Notificaciones extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('is_logged_in')) {
            // MODELS
            $this->load->model("InfoBipModel");
            $this->load->model('operaciones/Gastos_model');
            $this->load->model('supervisores/Supervisores_model');
            $this->load->model('operaciones/Beneficiarios_model');
            $this->load->model('operaciones/Operaciones_model');
            $this->load->model('operadores/Operadores_model');
            $this->load->model('User_model');

            $this->load->model('Chat');

            $this->load->model('Chat');
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
			$this->load->model('Devolucion_model', 'devolucion_model', TRUE);
            $this->load->helper("encrypt");
            $this->load->model('Usuarios_modulos_model');


            $this->load->model('Solicitud_m');
            // LIBRARIES
            $this->load->library('form_validation');
            // HELPERS
            $this->load->helper('date');
        } else {
            redirect(base_url('login'));
        }  
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

//Begin Esthiven Garcia Abril 2020
    public function index() {
        $link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/',$link);
        $permisos = FALSE;
        foreach ($this->session->modulos as $key => $value):
            if($value->url == $link_array[1])
            {
                $permisos = TRUE;
                break;
            } 
        endforeach;
        if ($permisos) 
        {
            $title['title'] = 'Operaciones';
            $this->load->view('layouts/adminLTE', $title);
            $cantidad_beneficiarios = $this->Operaciones_model->get_cantidad_beneficiarios();
            $cantidad_gastos = $this->Operaciones_model->get_cantidad_gastos();
            $cantidad_devoluciones = $this->devolucion_model->cantidad_solicitudes_devoluciones();
            $criterios = $this->Supervisores_model->get_all_criterios();
            $cantidad_camp_manuales = count($this->Supervisores_model->getCampaniasManuales());
            $data = array('cant_beneficiarios' => $cantidad_beneficiarios, 'cant_camp_manuales' => $cantidad_camp_manuales, 'cant_gastos' => $cantidad_gastos, 'tipos_criterios'=>$criterios, 'total_devoluciones' => $cantidad_devoluciones[0]->cantidad);
            $data['title']   = 'Supervisor Cobranzas';
            $data['heading'] = 'Cobranzas';

            $this->load->view('notificaciones/notificaciones', ['data' => $data]);
            
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    public function vistaGrupos() {

        $lista_operadores=$this->Operadores_model->get_lista_operador_central();
        $lista_skills=$this->Operadores_model->get_lista_skill_central();
        $this->load->view('notificaciones/configuracion_grupo',['lista_operadores'=>$lista_operadores,'lista_skills'=>$lista_skills ]);
        return $this;
    }
    public function vistaPalabras() {

        $lista_operadores=$this->Operadores_model->get_lista_operador_central();
        $lista_skills=$this->Operadores_model->get_lista_skill_central();
        // var_dump($lista_skills);die();
        $this->load->view('notificaciones/configuracion_centrales',['lista_operadores'=>$lista_operadores,'lista_skills'=>$lista_skills ]);
        return $this;
    }

    



}

