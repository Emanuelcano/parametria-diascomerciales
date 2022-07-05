<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

   
class NivelEstudio extends REST_Controller {

    public function __construct($config = 'rest')
    {   
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct(); 
        $this->load->model('Ident_nivelestudio_m','',TRUE);  
        $this->load->helper(array('form', 'url'));
       
    }

     public function Index_post($data=false,$mode = false)
  {
  
        if ($this->input->post('nivelestudio')) {
          if($this->guardarnivelestudio()){
            $data['status'] = 'El nivel de estudio se creo Correctamente';
            
          }else{
            $data['status'] ='Hubo un error en la inserción';
          }
        }
        echo json_encode($data);      
    }

    public function buscarNivelEstudio_get(){

         $data = $this->Ident_nivelestudio_m->get_nivelestudiotodo(); 
         $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }
    public function guardarnivelestudio(){

       
          
            $estadocivil = $this->input->post('nivelestudio');
            $codigocivil = $this->input->post('codigonivel');
            $set['Nombre_NivelEstudio']=$estadocivil;
            $set['Codigo']=$codigocivil;
            $this->Ident_nivelestudio_m->save($set);

          return true;  
    
    
    }
    public function activarnivelestudio_post(){
  
          $data['id_NivelEstudio'] = $this->input->post('id_NivelEstudio');
          
          $update['id_estado_NivelEstudio'] = $this->input->post('id_estado_NivelEstudio');
          
        $this->Ident_nivelestudio_m->save($update,$data['id_NivelEstudio']);
          
          
          $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
    }
   
    public function editarNivelestudio_post(){
 
          
          $data['id_NivelEstuido'] = $this->input->post('id_NivelEstudio');
          $update['Nombre_NivelEstudio'] = $this->input->post('Nombre_NivelEstudio');
          $update['Codigo'] = $this->input->post('Codigo');
        
        $this->Ident_nivelestudio_m->save($update,$data['id_NivelEstuido']);
          
          
          $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }
    public function eliminarnivelestudio_post(){
 
          
          $data['id_NivelEstudio'] = $this->input->post('id_NivelEstudio');
          
        
        $this->Ident_nivelestudio_m->delete($data['id_NivelEstudio']);
          
          
          $status = parent::HTTP_OK;
           $response = ['status' => $status, 'data' => $data];
           $this->response($response, $status);
         
    }



}
