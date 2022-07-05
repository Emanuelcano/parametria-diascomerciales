<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

   
class CuentasBancarias extends REST_Controller {

    public function __construct($config = 'rest')
    {   
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct(); 
        $this->load->model('Em_cuentas_bancarias_m','',TRUE);  
        $this->load->helper(array('form', 'url'));
       
    }

 	public function buscarCuentasBancarias_get(){

         $data = $this->Em_cuentas_bancarias_m->get_cuentas_bancarias(); 
         $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }


}