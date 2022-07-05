<?php

class Ajustes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('is_logged_in')) 
        {
            // MODELS
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
            $this->load->model('Cliente_model', 'cliente_model', TRUE);
            $this->load->model('operadores/Operadores_model','operadores',TRUE);

            // LIBRARIES

            // HELPERS
            
        } else {
            redirect(base_url('login'));
        }
    }

    public function index($id_solicitud = NULL)
    {

        $data['title']   = 'Ajustes';
        $data['heading'] = 'Ajustes';
        $this->load->view('layouts/adminLTE__header', $data);
        $this->load->view('ajustes/ajustes_main', $data);
        $this->load->view('layouts/adminLTE__footer', $data);

        //$this->output->enable_profiler(ENABLE_PROFILER);

        return $this;

    }

    public function get_table_ajustes() {        
        $sol_ajustes = $sol_ajustes = $this->solicitud_model->getSolicitudAjustesAll();
        $data['data'] = [];               
        foreach ($sol_ajustes as $sol => $value) {
            $value->name_operador = $this->operadores->get_operadores_by(['id_operador_buscar' => $value->id_operador])[0]->nombre_apellido;
            if ($value->id_operador_procesa == null && $value->id_operador_procesa == ''){
                $value->name_operador_procesa = "";
            } else {
                $value->name_operador_procesa = $this->operadores->get_operadores_by(['id_operador_buscar' => $value->id_operador_procesa])[0]->nombre_apellido;
            }
            $data['data'][] = $value;
        }
        echo json_encode($data);
    }

}
