<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

   
class CuentaBancaria extends REST_Controller {

    public function __construct($config = 'rest')
    {   
        
        parent::__construct(); 
        $this->load->model('Em_cuentas_bancarias_m','',TRUE);  
        $this->load->helper(array('form'));
        access_control_allow();
       
    }

 	public function cambiarestadocuenta_post(){

         $id_cuentabancaria = $this->post('id_cuentabancaria');
         $id_estado = $this->post('id_estado');
         $data['id_cuentabancaria']=$id_cuentabancaria;
         $data['id_estado']=$id_estado;
         $status= $this->Em_cuentas_bancarias_m->cambiarestadocuenta($data); 

         if ($status==false) {
             $data =$status;

            $status = parent::HTTP_OK;
          
          $response =  array_base();
          $response['success'] = $status;
          $response['data'] = $data;
           $this->response($response, $status);
         }
         
         
    }

}