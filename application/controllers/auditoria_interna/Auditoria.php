<?php

class Auditoria extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('is_logged_in')) {
            // MODELS
            $this->load->model('AuditoriaInterna_model','auditoria_interna_model',TRUE);
            $this->load->model('operaciones/Gastos_model');
            $this->load->model('supervisores/Supervisores_model');
            $this->load->model('operaciones/Beneficiarios_model');
            $this->load->model('operaciones/Operaciones_model');
            $this->load->model('Solicitud_m');
            $this->load->model('tracker_model', 'tracker_model', TRUE);
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
            /*** Se  activa el trackeo al entrar al módulo de auditoría ***/
            $id_operador = $this->session->userdata('idoperador');
            $param = array(
                "id_solicitud"   => 0,
                "id_operador"    => $id_operador,
                "id_cliente"     => null,
                "fecha"          => null,
                "hora"           => null,
                "Documento"      => null,
                "tipo"           => null,
                "buro"           => null,
                //"cuenta"       =>
                //"reto"         =>
                "estado"         => null,
                "tipo_operacion" => null,
                "bstatus"        => 2
            );
            
            // Se resetea cualquier estatus que tenga el auditor en la tabla auditoria_interna_online.
            $this->tracker_model->set_off_all_operation($id_operador);
            $existe = $this->tracker_model->exists_operador($id_operador);
            if($existe) {
                /*** Se actualiza el track en la tabla de auditoria_interna_online ***/
                $this->tracker_model->update_track_auditoria_online($param, $id_operador);
            } else {
                // Inserto track en tabla auxiliar de auditoria online
                $this->tracker_model->insert_track_auditoria_online($param);
            }

            $title['title'] = 'Auditoria Interna';
            $this->load->view('layouts/adminLTE', $title);
            $data['solicitudes_posterior'] = [];//$this->auditoria_interna_model->search_solicitudes_por_auditar();
            $data['solicitudes_on_pendientes'] = '3';//$this->Operaciones_model->get_cantidad_beneficiarios();
            $data['solicitudes_pos_pendientes'] = '3';//count($data['solicitudes_posterior']);
            
            $this->load->view('auditoria_interna/auditoria', ['data' => $data]);
            
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    public function vistaGeneraAuditoria() {
        
        $operadores = $this->auditoria_interna_model->search_operadores();

        $data = array('operadores_data' => $operadores);
        //var_dump($data);die;
       
        $this->load->view('auditoria_interna/gestion_auditoria_online',$data);
        return $this;
    }


    
//FIN Esthiven Garcia Abril 2020
}
