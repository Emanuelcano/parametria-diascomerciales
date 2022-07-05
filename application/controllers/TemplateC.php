<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

class TemplateC extends REST_Controller {

    public function __construct($config = 'rest')
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct(); 
        $this->load->model('Estilos_m','',TRUE);  
        $this->load->helper(array('form', 'url'));
        
    }

     public function Index_post($data=false,$mode = false)
	{
		
        

        if ($this->input->post('nombreestilo')) {
        	if($this->guardarestilo()){
        		$data['status'] = 'El estilo se creo Correctamente';
        		
        	}else{
        		$data['status'] ='Hubo un error en la inserción';
        	}
        }
        echo json_encode($data);      
    }

    public function BuscarEstilo_get(){

		
         
        
         $data = $this->Estilos_m->get_estilotodo(); 

          $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
    }
    public function guardarestilo(){

		$config['upload_path']= getcwd() .'/public/css';
		$config['allowed_types']        = 'css';
       	
      	$this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('fileestilo'))
        {
            return  false;
        }
        else
        {	
        	$todo = array('upload_data' => $this->upload->data());
            $fila = 1;
            $nombrearchivo =  $todo['upload_data']['file_name'];
            $set['id_estado']=0;
            $nombre = $this->input->post('nombreestilo');
            $set['nombre']=$nombre;
            $set['nombre_archivo']=$nombrearchivo;
            $this->Estilos_m->save($set);

	        return true;  
		}
		
    }
    public function activarestilo_post(){
  
          $data['estilos'] = $this->input->post('idFolder');
          
  		    $upd['id_estado'] = 0;
          $this->Estilos_m->update_all($upd);
          
          $update['id_estado'] =1;
          $this->Estilos_m->save($update,$data['estilos']);
          
          // $idestilos = $this->Estilos_m->activar_estilo($data);
          
         echo  $status = $data['estilos'];
    }
    public function eliminarestilo_post(){
 
          $upd['estilos'] = $this->input->post('estilos');
      
      
          $this->Estilos_m->update_uno($upd);
          
          
         echo  $status = 200;
    }



}
