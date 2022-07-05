<?php
require_once APPPATH . 'controllers/comunicaciones/Twilio.php';
require_once APPPATH . 'controllers/api/ApiTemplates.php';

class Templates extends CI_Controller {

    public function __construct() {
        parent::__construct();;

        if ($this->session->userdata('is_logged_in')) {
            // Models
            $this->load->model('ComunicationTemplate_model', '', TRUE);
        } else {
            redirect(base_url('login'));
        }  

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    //ingreso a la vista de  gestion templates
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

            $title['title'] = 'Templates';
            $this->load->view('layouts/adminLTE', $title);

           
            $data = array();
            $this->load->view('whatsapp/templates_view', $data);
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    //Obtengo listado de templates por tipo
    public function templateList($type) {
        $data = [];
        if($type != 'email'){
            $data['templates_email'] = [];
            $data['templates'] = $this->ComunicationTemplate_model->templateList($type);
        } else {
            $data['templates_email'] = $this->ComunicationTemplate_model->templateEmailList();
            $data['templates'] = [];
        }

        return  $this->load->view('whatsapp/templates_list_view', $data);
    } 

    // swith status template 
    public function changeStatus($id, $status){
        
        $data = [];
        $data['estado'] = $status;
        $response = $this->ComunicationTemplate_model->changeStatus($id, $data);
        echo $response;
        die;
    }

    //obtengo un template
    public function getTemplate($id, $type){
        $data = [];
        if($type != 'email'){
            $data['template'] = $this->ComunicationTemplate_model->getTemplate($id);
            $data['variables'] = $this->ComunicationTemplate_model->getTemplateVariablesId( $data['template'][0]['id'] );
            $data['proveedores'] = $this->ComunicationTemplate_model->getProveedores($type);
            $data['grupos'] =  $this->ComunicationTemplate_model->getGroup();
        } else {
            $data['template_email'] = $this->ComunicationTemplate_model->getEmailTemplate($id);
        }
        
        echo json_encode($data);
    }

    //obtengo proveedores 
    public function getCreateData($type){
        $data = [];
        $data['proveedores'] = $this->ComunicationTemplate_model->getProveedores($type);
        $data['grupos'] = $this->ComunicationTemplate_model->getGroup();
        echo json_encode($data);
    }

    //testeo template
    public function testTemplates(){
        $templates_id = $this->input->post('templates');
        $number = $this->input->post('number');

        $CI =& get_instance();
        $CI->load->library('twilio'); 
        $this->twilio->send_template_message_new();
    }
    public function getVariable($template_id, $variable){
        $data = [];
        $data['variable'] = $this->ComunicationTemplate_model->getVariable($template_id, $variable);

        echo json_encode($data);
    }
    
    //agrego de a un campo para crear nuevas variables
    public function createVariablesList(){
        $request = $this->input->post();

        $data = [];
        $data['variable_exist'] = (int)$request["variable_exist"];
   
        return json_encode($this->load->view('whatsapp/variable_list_view', $data), true);
    }

    // testeo template unitario sin envio
    public function testUnitTemplate($documento){
        
        if(!$documento){
            echo 'false';
            return;
        }

        $solicitud_id = $this->ComunicationTemplate_model->getLastRequest($documento);

        echo $solicitud_id[0]->id;
    }

    public function documentoExits($documento = null){

        if(!$documento){
            return false;
        }

        $documento = $this->ComunicationTemplate_model->documentoExits($documento);    
        if(!empty($documento)) {
			$response["data"] = ['documento' => $documento[0]['documento']];
		} else {			
			$response["data"] = ['documento' => ""];
		}

        echo json_encode($response);
    }
}
