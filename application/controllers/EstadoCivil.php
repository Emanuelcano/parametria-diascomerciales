<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

   
class EstadoCivil extends REST_Controller {

    public function __construct($config = 'rest')
    {   
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct(); 
        $this->load->model('Ident_estadocivil_m','',TRUE);  
        $this->load->helper(array('form', 'url'));
       
    }

     public function Index_post($data=false,$mode = false)
	{
	
        if ($this->input->post('estadocivil')) {
        	if($this->guardarestadocivil()){
        		$data['status'] = 'El estadocivil se creo Correctamente';
        		
        	}else{
        		$data['status'] ='Hubo un error en la inserción';
        	}
        }
        echo json_encode($data);      
    }

    public function buscarEstadoCivil_get(){

         $data = $this->Ident_estadocivil_m->get_estadociviltodo(); 
         $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }
    public function guardarestadocivil(){

       
        	
            $estadocivil = $this->input->post('estadocivil');
            $codigocivil = $this->input->post('codigocivil');
            $set['Nombre_EstadoCivil']=$estadocivil;
            $set['Codigo']=$codigocivil;
            $this->Ident_estadocivil_m->save($set);

	        return true;  
		
		
    }
    public function activarestadocivil_post(){
  
          $data['id_EstadoCivil'] = $this->input->post('id_EstadoCivil');
          
  		    $update['id_estado_EstadoCivil'] = $this->input->post('id_estado_EstadoCivil');
          
          $this->Ident_estadocivil_m->save($update,$data['id_EstadoCivil']);
          
          // $idestadocivils = $this->Ident_estadocivil_m->activar_estadocivil($data);
          
           $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
    }
    
    public function editarEstadocivil_post(){
 
          
          $data['id_EstadoCivil'] = $this->input->post('id_EstadoCivil');
          $update['Nombre_EstadoCivil'] = $this->input->post('Nombreestadocivil');
          $update['Codigo'] = $this->input->post('codigoestadocivil');
        
        $this->Ident_estadocivil_m->save($update,$data['id_EstadoCivil']);
          
          
          $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }
    public function eliminarestadocivil_post(){
 
          
          $data['id_EstadoCivil'] = $this->input->post('id_EstadoCivil');
          
        
        $this->Ident_estadocivil_m->delete($data['id_EstadoCivil']);
          
          
          $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }



}
