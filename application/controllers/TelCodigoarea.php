<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

   
class TelCodigoarea extends REST_Controller {

    public function __construct($config = 'rest')
    {   
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct(); 
        $this->load->model('Tel_codigoarea_m','',TRUE);  
        $this->load->helper(array('form', 'url'));
       
    }

     public function Index_post($data=false,$mode = false)
  {
  
        if ($this->input->post('nivelestudio')) {
          if($this->guardarTelCodigoarea()){
            $data['status'] = 'El Codigo de area se creo Correctamente';
            
          }else{
            $data['status'] ='Hubo un error en la inserción';
          }
        }
        echo json_encode($data);      
    }

    public function buscarTelCodigoarea_get(){

         $data = $this->Tel_codigoarea_m->Tel_codigoarea_m(); 
         $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }
    public function guardarTelCodigoarea(){

       
          
            $ciudad_tel = $this->input->post('ciudad_tel');
            $departamento_tel = $this->input->post('departamento_tel');
            $areaCode = $this->input->post('areaCode');
            $set['ciudad_tel']=$ciudad_tel;
            $set['departamento_tel']=$departamento_tel;
            $set['areaCode']=$areaCode;
            $this->Tel_codigoarea_m->save($set);

          return true;  
    
    
    }
    public function activarTelCodigoarea_post(){
  
          $data['id_ciudad_tel'] = $this->input->post('id_ciudad_tel');
          
          $update[' id_estado_tel'] = $this->input->post('  id_estado_tel');
          
        $this->Tel_codigoarea_m->save($update,$data['id_ciudad_tel']);
          
          
          $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
    }
   
    public function editarTelCodigoarea_post(){

          $data['id_ciudad_tel'] = $this->input->post('id_ciudad_tel');
          $update['ciudad_tel'] = $this->input->post('ciudad_tel');
          $update['departamento_tel'] = $this->input->post('departamento_tel');
          $update['areaCode'] = $this->input->post('areaCode');
        
        $this->Tel_codigoarea_m->save($update,$data['id_ciudad_tel']);
          
          
          $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }
    public function eliminarTelCodigoarea_post(){
 
          
          $data['id_ciudad_tel'] = $this->input->post('id_ciudad_tel');
          
        
        $this->Tel_codigoarea_m->delete($data['id_ciudad_tel']);
          
          
          $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }



}
