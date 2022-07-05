<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
use function GuzzleHttp\debug_resource;
/**
 *
 */
class ApiLegales extends REST_Controller
{      
    private $_free_methods = array('buscar','listar');
    public function __construct()
    {
        parent::__construct();
        $method = $this->uri->segment(3);
        $this->load->library('User_library');
        $auth = $this->user_library->check_token();
        if($auth->status == parent::HTTP_OK || in_array($method, $this->_free_methods))
        {
            $this->load->model('operadores/Operadores_model');
            $this->load->model('legales/Legales_model');
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
            $this->load->model('Solicitud_m');
            // LIBRARIES
            $this->load->library('form_validation');
            // HELPERS
            $this->load->helper('date');
        }else{
            $this->session->sess_destroy();
            $this->response(['redirect'=>base_url('login')],$auth->status);
        }
    }
//Begin Esthiven Garcia Abril 2020
  public function dar_baja_post()
    {   
        $documento = $_POST['documento'];
        if (isset($_POST['observaciones'])) {
            $observacion = $_POST['observaciones'];
            $razon = $_POST['razon'] .' '. $_POST['observaciones'];
            $insercion = $this->Legales_model->set_registros($observacion, $documento);
        }else {
            $razon = $_POST['razon'];
            $insercion = $this->Legales_model->set_registros($razon, $documento);
        }
        if (!empty($_POST['observaciones'])) {
            $observaciones = $_POST['observaciones'];
            $razonTrack = $_POST['razon'] .' '. $observaciones;
            $this->trakear($documento, $razonTrack);            
        }else {
            $this->trakear($documento);            
        }
        if ($razon == 'FALLECIDO') {
            $cuotas_pend = $this->Legales_model->cuotas_pendientes($documento);
            if ($cuotas_pend == true) {
                $estado_fallecido = $this->Legales_model->credito_vigente($documento);
                if ($estado_fallecido) {
                    $actualizar_credito = $this->Legales_model->actualizar_estado($documento);
                }
            }
        }
    }
    
    
    //insertando en el track
    private function trakear($documento = null, $razon = null){
        if ($documento == null && $razon == null) {
            $documento = $this->input->post('documento');
            $razon = $this->input->post('razon');
        }
        if ($this->input->post('razon') == 'FALLECIDO') {
            $razon = '[FALLECIDO]<br>';
        }
        $busqueda = $this->Legales_model->solicitud_documento($documento);
        $parametros = array(
        'id_solicitud'=>$busqueda[0]['id'],
        'observaciones'=>$razon, 
        'id_tipo_gestion' => 400,
        'id_operador' => $this->session->userdata("idoperador")
        );
    $headers = array('Accept' => 'application/json');
    $end_point = URL_BACKEND."api/track_gestion";
    $otherop = array (
    'binarytransfer' => 1,
    'timeout' => 1000000,
    'connect_timeout' => 1000000,
    );
    $request = Requests::post($end_point, $headers, $parametros, $otherop);
    $response = $request->body;
    $aux=json_decode($response,TRUE);
    if ($aux['status']['ok']==true) {
    echo  $aux['message'];
    }else{
    echo  "Error al trakear ".$aux['errors']['id_solicitud'];
    }
    }

    public function bloquear_post()
    {
        $observaciones = $_POST['observaciones'];
        $razon = $_POST['razon'] .' '. $observaciones;
        $documento = $_POST['documento'];
        $insert_bloq =$this->Legales_model->bloquear_cliente($observaciones, $documento);
        $this->trakear($documento, $razon);
    }

    public function reactivar_post()
    {
        $observaciones = $_POST['observaciones'];
        $razon = $_POST['razon'] .' '. $observaciones;
        $documento = $_POST['documento'];


        //var_dump($razon);die;
            $eliminar_registro = $this->Legales_model->eliminar_registro($documento);
            if ($eliminar_registro) {
                echo 'Se ha reactivado correctamente <br>';
                $this->trakear($documento, $razon);
            }else {
                echo 'Hubo un error al tratar de reactivar el registro <br>';
            }
    }

    public function reactivarBloqueo_post()
    {
        $observaciones = $_POST['observaciones'];
        $razon = $_POST['razon'] .' '. $observaciones;
        $documento = $_POST['documento'];


        //var_dump($razon);die;
            $eliminar_bloqueo = $this->Legales_model->eliminar_bloquedo($documento);
            if ($eliminar_bloqueo) {
                echo 'Se ha reactivado correctamente <br>';
                $this->trakear($documento, $razon);
            }else {
                echo 'Hubo un error al tratar de reactivar el registro <br>';
            }
    }
}