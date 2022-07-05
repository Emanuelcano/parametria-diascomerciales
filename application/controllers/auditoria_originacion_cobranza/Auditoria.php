<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

class Auditoria extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent::__construct();
        $method = $this->uri->segment(3);

        $this->load->library('User_library');
        $auth = $this->user_library->check_token();
        if ($this->session->userdata('is_logged_in')) {
            // MODELS
            $this->load->model('operadores/Operadores_model', 'operadores_model');
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
            $this->load->model('Credito_model', 'credito_model', TRUE);
            $this->load->model('Usuarios_modulos_model');
            $this->load->model('auditoria_originacion_cobranzas/Auditoria_model','auditoria_model',TRUE);
            $this->load->model('BankEntidades_model', 'bank_model', TRUE);
            $this->load->model('BankTipoCuenta_model', 'type_account_model', TRUE);

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

    
    /** MODULO AUDITORIA 
    *   Camilo Franco
    */

    public function index() 
    {   
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
        if($permisos){
            $title['title'] = 'Auditoria';
            $this->load->view('layouts/adminLTE', $title);

            $data['title']   = 'Auditoria de Originación & Cobranzas';
            $data['heading'] = 'Auditoria';
    
            $this->load->view('auditoria_originacion_cobranza/auditoria', ['data' => $data]);
            return $this;
        }
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    /**
    * Cargo la vista para auditar llamadas y WhatsApp de originacion.
    * 
    * @return vista
    */

    public function auditoriaOriginacion() 
    {
        $data = false;
        $this->load->view('auditoria_originacion_cobranza/modulos/box_llamadas_whatsapp_btns', compact("data"));
        return $this;
    }

    public function auditoriaCobranzas() 
    {
        $this->load->view('auditoria_originacion_cobranza/modulos/box_llamadas_whatsapp_btns');
        return $this;
    }
    public function misAuditorias() 
    {
        $this->load->view('auditoria_originacion_cobranza/modulos/box_auditoria_table');
        return $this;
    }

    private function getInfoLlamadoAuditado($audios, $id_solicitud)
    {
        foreach ($audios as $audio) {
            $noCorrespondeaSolicitud = $this->auditoria_model->noCorrespondeASolicitud_get($audio->id_track, $id_solicitud);

            // Recibe el id del audio del llamado y ve si fue auditado
            $caso_auditados = $this->auditoria_model->casosAuditados_get($audio->id_track, $id_solicitud);
            $audio->auditado = 0;
            if (isset($caso_auditados) || isset($noCorrespondeaSolicitud)) {
                $audio->auditado = 1;
            }
            
        }

        return $audios;
    }

    private function getParametrosAuditar()
    {
        $grupos = $this->auditoria_model->getGrupoParametro();
        $parametros = [];
        foreach($grupos as $grupo)
        {
            $parametros[$grupo['nombre']] = $this->auditoria_model->getParametro($grupo['id']);
        }

        return $parametros;
    }

    public function getInfoAudio($telefono, $central = "0")
    {

        $end_point = SERVER_FILES_SOLVENTA_URL. '/api/ApiNeotell/consultaAudiosGeneral';

        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_send', function($fp){
            curl_setopt($fp, CURLOPT_TIMEOUT, 300);
        });
        $request = Requests::post($end_point, array(),['telefono' => $telefono, 'central' => $central]);
        $response = json_decode($request->body);

        return $response;
    }

    /**
    * Cargo la vista para auditar llamadas de originacion.
    * 
    * @return vista
    */

    public function auditarLlamadas() 
    {
        // $filtro['tipo_operadores'] = [1,4];
        $filtro['tipo_operadores'] = 6;
        $filtro['where']           = 'estado = 1';
        $data['lista_operadores'] = $this->operadores_model->get_operadores_by($filtro);
        $this->load->view('auditoria_originacion_cobranza/modulos/box_auditar_llamada', $data);
    }

    private function get_banks()
    {
        return $this->bank_model->search(['id_estado_banco' => 1]);
    }


    private function get_types_account()
    {
        return $this->type_account_model->search(['id_estado_tipocuenta' => 1]);
    }  
    
    
    private function get_txt($id_solicitud)
    {
        return $this->solicitud_model->getTxt($id_solicitud);
    }     

    private function get_detalle_credito($id_credito = null) 
    {
        
        return $this->credito_model->getCreditoInfo($id_credito);
    }

    private function get_acuerdos_clientes($id_cliente = null)
    {
        $acuerdos = 'Sin acuerdos';
        if ($id_cliente != null) {
            $acuerdos = $this->credito_model->getAcuerdosDePagoPorIdCliente($id_cliente) ? $this->credito_model->getAcuerdosDePagoPorIdCliente($id_cliente) : 'Sin acuerdos';
        }
        return $acuerdos;
    }
}

