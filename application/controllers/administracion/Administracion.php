<?php
class Administracion extends CI_Controller {

    protected $CI; 
 
    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('is_logged_in')) {
            // MODELS
            $this->load->model('operaciones/Gastos_model');
            $this->load->model('operaciones/Beneficiarios_model');
            $this->load->model('operaciones/Operaciones_model');
            $this->load->model('administracion/Administracion_model');
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

//INICIO Sabrina Basteiro Noviembre 2019
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
            $title['title'] = 'Administracion';
            $this->load->view('layouts/adminLTE', $title);
            $cantidad_beneficiarios = $this->Operaciones_model->get_cantidad_beneficiarios();
            $cantidad_gastos = $this->Operaciones_model->get_cantidad_gastos();
            $cantidad_gastos_pendientes = $this->Administracion_model->cantidad_gastos_pendientes();
            $data = array('cant_beneficiarios' => $cantidad_beneficiarios, 'cant_gastos' => $cantidad_gastos,'cantidad_gastos_pendientes' => $cantidad_gastos_pendientes);
            $this->load->view('administracion/administracion', ['data' => $data]);
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    public function vistaBeneficiarios() {
        $tipo_benef = $this->Beneficiarios_model->get_tipo_beneficiario();
        $lista_rubro = $this->Beneficiarios_model->get_lista_rubro();
        $forma_pago = $this->Beneficiarios_model->get_forma_pago();
        $moneda = $this->Beneficiarios_model->get_moneda();
        $tipo_documento = $this->Beneficiarios_model->get_tipo_documento();
        $provincia = $this->Beneficiarios_model->get_provincia();
        $banco = $this->Beneficiarios_model->get_banco();
        $tipo_cuenta = $this->Beneficiarios_model->get_tipo_cuenta();
        $beneficiarios = $this->Beneficiarios_model->get_beneficiarios();
        $data = array('tipo_benef' => $tipo_benef, 'lista_rubro' => $lista_rubro, 'forma_pago' => $forma_pago, 'moneda' => $moneda, 'tipo_documento' => $tipo_documento, 'provincia' => $provincia, 'banco' => $banco, 'tipo_cuenta' => $tipo_cuenta, 'beneficiarios' => $beneficiarios);
        $this->load->view('operaciones/vistaBeneficiarios', ['data' => $data]);
        return $this;
    }
  
    public function vistaGastos() {
        $tipo_gasto = $this->Gastos_model->get_tipo_gasto();
        $moneda = $this->Gastos_model->get_moneda();
        $clase_gasto = $this->Gastos_model->get_clase_gasto();
        $descripcion_gasto = $this->Gastos_model->get_descripcion_gasto();
        $beneficiarios = $this->Gastos_model->get_beneficiarios();
        $forma_pago = $this->Gastos_model->get_forma_pago();
        $empresa = $this->Gastos_model->get_empresa();        
        $data = array('beneficiario' => $beneficiarios, 'forma_pago' => $forma_pago, 'moneda' => $moneda, 'id_empresa' => $empresa, 'tipo_gasto' => $tipo_gasto, 'clase_gasto' => $clase_gasto, 'descripcion_gasto' => $descripcion_gasto);
        $this->load->view('operaciones/vistaGastos', ['data' => $data]);
        return $this;
    }

    
    public function vistaGastosPendientes() {   
        $tipo_gasto = $this->Gastos_model->get_tipo_gasto();
        $moneda = $this->Gastos_model->get_moneda();
        $clase_gasto = $this->Gastos_model->get_clase_gasto();
        $descripcion_gasto = $this->Gastos_model->get_descripcion_gasto();
        $beneficiarios = $this->Gastos_model->get_beneficiarios();
        $forma_pago = $this->Gastos_model->get_forma_pago();
        $empresa = $this->Gastos_model->get_empresa();        
        $data = array('beneficiario' => $beneficiarios, 'forma_pago' => $forma_pago, 'moneda' => $moneda, 'id_empresa' => $empresa, 'tipo_gasto' => $tipo_gasto, 'clase_gasto' => $clase_gasto, 'descripcion_gasto' => $descripcion_gasto);
        $this->load->view('administracion/vistaAdministracion', ['data' => $data]);
        return $this;
    }
    
    
//FIN Sabrina Basteiro Noviembre 2019
}
