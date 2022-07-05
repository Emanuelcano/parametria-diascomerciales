<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'third_party/REST_Controller.php';
require APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class ApiModulos extends REST_Controller {
    
    public function __construct() {
        parent::__construct();
        
       $this->load->model('Modulos_m','',TRUE);  
        $this->load->helper(array('form', 'url'));
    }

    public function modulos_get()
    {
 
		        
        
        $data = $this->Modulos_m->get();
        if ($data) {
              // Set HTTP status code
            $status = parent::HTTP_OK;
            // Prepare the response
            $response = ['status' => $status, 'data' => $data];  
        }else{
            $status = parent::HTTP_NO_CONTENT;
            $response = ['status' => $status, 'message' => 'No hay modulos disponibles! '];
        }
        
        // REST_Controller provide this method to send responses
        $this->response($response, $status);
    }
}




