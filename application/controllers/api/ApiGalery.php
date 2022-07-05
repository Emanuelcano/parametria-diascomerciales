<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 * 
 */
class ApiGalery extends REST_Controller 
{
	protected $end_folder = 'public/IMAGENES_SOLICITANTES';

	public function __construct()
	{
		parent::__construct();
		
		// MODELS
		$this->load->model('galery_model','',TRUE);
                $this->load->model('Solicitud_m','',TRUE);
		// LIBRARIES
		$this->load->library('form_validation');
	}
	
/**
* Esta funcion sube un archivo y muestra el resultado
* @return Array con el resumen de un archivo.
*/
    public function upload_image_post()
    {
        $config['upload_path'] = $this->get_end_folder();
        $config['allowed_types'] = 'mp4|jpg|png|jpeg|pdf|webm';
        $config['overwrite'] = FALSE;
        $type_image = $this->galery_model->search_required(['id'=>$this->input->post('id_img_required')]);
        $config['file_name'] = $this->input->post('documento').'_'.$this->input->post('id_solicitud').'_'.$type_image[0]['sufijo'];
        
        if($this->_validate_form_fields())
        {

	        $this->load->library('upload');
            $this->upload->initialize($config);              
	        if ($this->upload->do_upload('file'))
	        {
                    $file = $this->upload->data();
                    $file['uri'] = base_url($config['upload_path'].$file['file_name']);
                    $file['descripcion'] = $type_image[0]['descripcion'];
                    $file['origin'] = $type_image[0]['origen'];
                    $response['doc'] = $file;

                    $element['id_solicitud'] = $this->input->post('id_solicitud');
                    $element['scan_reference'] = $this->input->post('scan_reference');
                    $element['id_imagen_requerida'] = $type_image[0]['id'];
                    $element['patch_imagen'] = $config['upload_path'].$file['file_name'];
                    $element['extension'] = $file['file_ext'];
                    $element['is_image'] = $file['is_image'];
                    $element['fecha_carga'] = date('Y-m-d H:i:s');
                    $element['origen'] = $type_image[0]['origen'];
                           
	           if($this->galery_model->save_image($element))
	           {
                        $status = parent::HTTP_OK;
                        $response['status']['code'] = $status;
                        $response['status']['ok'] = TRUE;
                        $response['message'] = "Registro guardado";
	           }else{
                        $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                        $response['status']['code'] = $status;
                        $response['status']['ok'] = FALSE;
                        $response['errors'] = 'Error al guardar la imagen';
	            }


	        }else{
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response['status']['code'] = $status;
                    $response['status']['ok'] = FALSE;
                    $response['errors'] = $this->upload->display_errors();
	        }
        }else{
            $status = parent::HTTP_BAD_REQUEST;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = $this->form_validation->error_array();
        }
      
       	$this->response($response,$status);

    }

    public function uploadMedia_veriff_post()
    {
        $id_session = $this->galery_model->get_sessionid($this->input->post('id_solicitud'));
          
        if ($id_session->id_solicitud !== $this->input->post('id_solicitud') ) {
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = 'Error al iniciar el proceso';
            $this->response($response,$status);
        }

        $config['upload_path'] = $this->get_end_folder();
        $config['allowed_types'] = '*';
        $config['non_allowed_types'] = 'exe|php|py|sql|xls|bat';
        $config['overwrite'] = FALSE;
        $type_image = $this->galery_model->search_required(['id'=>$this->input->post('id_img_required')]);
        $config['file_name'] = $this->input->post('documento').'_'.$this->input->post('id_solicitud').'_'.$type_image[0]['sufijo'];
        
        if($this->_validate_form_fields())
        {

	        $this->load->library('upload');
            $this->upload->initialize($config);              
	        if ($this->upload->do_upload('file'))
	        {
                    $file = $this->upload->data();
                    $file['uri'] = base_url($config['upload_path'].$file['file_name']);
                    $file['descripcion'] = $type_image[0]['descripcion'];
                    $file['origin'] = $type_image[0]['origen'];
                    $response['doc'] = $file;

                    $element['id_solicitud'] = $this->input->post('id_solicitud');
                    $element['scan_reference'] = $this->input->post('scan_reference');
                    $element['id_imagen_requerida'] = $type_image[0]['id'];
                    $element['patch_imagen'] = $config['upload_path'].$file['file_name'];
                    $element['extension'] = $file['file_ext'];
                    $element['is_image'] = $file['is_image'];
                    $element['fecha_carga'] = date('Y-m-d H:i:s');
                    $element['origen'] = $type_image[0]['origen'];
                           
	           if($this->galery_model->save_image($element))
	           {
                        $status = parent::HTTP_OK;
                        $response['status']['code'] = $status;
                        $response['status']['ok'] = TRUE;
                        $response['message'] = "Registro guardado";
	           }else{
                        $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                        $response['status']['code'] = $status;
                        $response['status']['ok'] = FALSE;
                        $response['errors'] = 'Error al guardar la imagen';
	            }


	        }else{
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response['status']['code'] = $status;
                    $response['status']['ok'] = FALSE;
                    $response['errors'] = $this->upload->display_errors();
	        }
        }else{
            $status = parent::HTTP_BAD_REQUEST;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = $this->form_validation->error_array();
        }
      
       	$this->response($response,$status);

    }

	public function uploadMedia_metamap_post()
    {
        $config['upload_path'] = $this->get_end_folder();
        $config['allowed_types'] = '*';
        $config['non_allowed_types'] = 'exe|php|py|sql|xls|bat';
        $config['overwrite'] = FALSE;
        $type_image = $this->galery_model->search_required(['id'=>$this->input->post('id_img_required')]);
        $config['file_name'] = $this->input->post('documento').'_'.$this->input->post('id_solicitud').'_'.$type_image[0]['sufijo'];
        if($this->_validate_form_fields())
        {
            $this->load->library('upload');
            $this->upload->initialize($config);              
            if ($this->upload->do_upload('file'))
            {
                    $file = $this->upload->data();
                    $file['uri'] = base_url($config['upload_path'].$file['file_name']);
                    $file['descripcion'] = $type_image[0]['descripcion'];
                    $file['origin'] = $type_image[0]['origen'];
                    $response['doc'] = $file;
                    $element['id_solicitud'] = $this->input->post('id_solicitud');
                    $element['scan_reference'] = $this->input->post('scan_reference');
                    $element['id_imagen_requerida'] = $type_image[0]['id'];
                    $element['patch_imagen'] = $config['upload_path'].$file['file_name'];
                    $element['extension'] = $file['file_ext'];
                    $element['is_image'] = $file['is_image'];
                    $element['fecha_carga'] = date('Y-m-d H:i:s');
                    $element['origen'] = $type_image[0]['origen'];
               if($this->galery_model->save_image($element))
               {
                        $status = parent::HTTP_OK;
                        $response['status']['code'] = $status;
                        $response['status']['ok'] = TRUE;
                        $response['message'] = "Registro guardado";
               }else{
                        $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                        $response['status']['code'] = $status;
                        $response['status']['ok'] = FALSE;
                        $response['errors'] = 'Error al guardar la imagen';
                }
            }else{
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response['status']['code'] = $status;
                    $response['status']['ok'] = FALSE;
                    $response['errors'] = $this->upload->display_errors();
            }
        }else{
            $status = parent::HTTP_BAD_REQUEST;
            $response['status']['code'] = $status;
            $response['status']['ok'] = FALSE;
            $response['errors'] = $this->form_validation->error_array();
        }
        $this->response($response,$status);
    }

    public function search_get()
    {
		$status = parent::HTTP_OK;
   		$response['status']['code'] = $status;
		$response['status']['ok'] = TRUE;
		$response['images'] = $this->galery_model->search_images($this->input->get());
    		$this->response($response,$status);
    }

    public function get_end_folder()
    {
    	$end_folder = $this->end_folder.'/'.date('Y').'/'.date('m').'/'.date('d').'/';
		    // Carpeta de destino.
	  	// $this->file_full_path= $end_folder.$this->file_name;

	    // Valida que la carpeta de destino exista, si no existe la crea.
	    if(!file_exists($end_folder))
	    {
	    	// Si no puede crear el directorio.
	        if(!mkdir($end_folder, 0777, true))
	        {
	        	$this->response['status']['ok'] = FALSE;
	        	$this->add_errors('No fué posible crear el directorio en .' . $end_folder);
	   			$this->response['errors'] = 'No fué posible crear el directorio en .' . $end_folder;
	   			return FALSE;
	        }
	    }
	    return $end_folder;
    }
    
    public function getReferenciaId_post()
    {
        $parametros = array(
            'id_referencia' => $this->input->post('id_referencia'),
        );       
        $referencias = $this->galery_model->get_referencia($parametros);     
        $parentesco  = $this->Solicitud_m->getParentesco();   
        $situacion_laboral = $this->Solicitud_m->getSolicitud($referencias->id_solicitud);
        $datos[] = array( 
                'referencias' => $referencias,
                'parentesco'  => $parentesco,
                'laboral'     => $situacion_laboral[0]["id_situacion_laboral"]
               );         
        echo json_encode($datos);
    }
    
    public function registrarFamiliar_post(){
        $response = array_base();
        $nombre_apellido    = $this->input->post('nombre_apellido');
        $telefono           = $this->input->post('telefono');
        $parentesco         = $this->input->post('parentesco');
        $empresa_cargo      = $this->input->post('empresa_cargo');
        $empresa_direccion  = $this->input->post('empresa_direccion');
        $empresa_barrio     = $this->input->post('empresa_barrio');
        $id_solicitud       = $this->input->post('id_solicitud');
        $data = [
            "nombres_apellidos" => $nombre_apellido,
            "telefono"          => $telefono,
            "empresa_cargo"     => $empresa_cargo,
            "empresa_direccion" => $empresa_direccion,
            "empresa_barrio"    => $empresa_barrio,
            "id_parentesco"     => $parentesco,
            "id_solicitud"      => $id_solicitud,
            "carga_consultor"   => 1
        ];
        $registrado = $this->galery_model->registrarFamiliar($data);
        if($registrado > 0 ){
            $response['success'] = true;
            $response['data']['id_referencia'] = $registrado;
            $response['data']['insert'] = true;
            
        }
        $this->response($response);               
    }
        
    public function editarFamiliar_post(){
        $response = array_base();
        $nombre_apellido   = $this->input->post('nombre_apellido');
        $telefono          = $this->input->post('telefono');
        $parentesco        = $this->input->post('parentesco');
        $empresa_cargo     = $this->input->post('empresa_cargo');
        $empresa_direccion = $this->input->post('empresa_direccion');
        $empresa_barrio    = $this->input->post('empresa_barrio');
        $id_solicitud      = $this->input->post('id_solicitud');
        $id_referencia     = $this->input->post('id_referencia');
        $data = [
            "nombres_apellidos" => $nombre_apellido,
            "telefono"          => $telefono,
            "id_parentesco"     => $parentesco,
            "empresa_cargo"     => $empresa_cargo,
            "empresa_direccion" => $empresa_direccion,
            "empresa_barrio"    => $empresa_barrio,
            "id_solicitud"      => $id_solicitud,
            "id"                => $id_referencia,
            "carga_consultor"   => 1
        ];
        $registrado = $this->galery_model->editarFamiliar($data);
        if($registrado > 0){
            $response['success'] = true;
            $response['data']['update'] = true;
        }
        $this->response($response);               
    }
	
	public function saveTelefonoReferenciaFamiliar_post()
	{
		$documento = $this->input->post('documento');
		$telefono = $this->input->post('telefono');
		$tipo = $this->input->post('tipo');
		$contacto = $this->input->post('contacto');
		$estado = $this->input->post('estado');
		$idParentezco = $this->input->post('idParentezco');
		$llamada = $this->input->post('llamada');
		$sms = $this->input->post('sms');
			
		$this->Solicitud_m->saveTelefonoReferenciaFamiliar($documento, $telefono, $tipo, $contacto, $estado, $idParentezco, $llamada, $sms);
		
	}
	
	public function saveTelefonoReferenciaLaboral_post()
	{
		$documento = $this->input->post('documento');
		$telefono = $this->input->post('telefono');
		$tipo = $this->input->post('tipo');
		$contacto = $this->input->post('contacto');
		$estado = $this->input->post('estado');
		$idParentezco = $this->input->post('idParentezco');
		$llamada = $this->input->post('llamada');
		$sms = $this->input->post('sms');
		
		$this->Solicitud_m->saveTelefonoReferenciaLaboral($documento, $telefono, $tipo, $contacto, $estado, $idParentezco, $llamada, $sms);
	}
	
	public function updateTelefonoReferenciaLaboral_post()
	{
		$documento = $this->input->post('documento');
		$telefono = $this->input->post('telefono');
		$telefonoOriginal = $this->input->post('telefonoOriginal');
		$contacto = $this->input->post('contacto');
		
		$this->Solicitud_m->updateTelefonoRefernciaLaboral($documento, $telefonoOriginal, $telefono, $contacto);
	}
	
	public function updateTelefonoReferenciaFamiliar_post()
	{
		$documento = $this->input->post('documento');
		$telefono = $this->input->post('telefono');
		$telefonoOriginal = $this->input->post('telefonoOriginal');
		$contacto = $this->input->post('contacto');
		$idParentezco = $this->input->post('idParentezco');
		
		$this->Solicitud_m->updateTelefonoReferenciaFamiliar($documento, $telefonoOriginal, $telefono, $contacto, $idParentezco);
	}
	
/***************************************************************************/
// VALIDATIONS
/***************************************************************************/
	private function _validate_form_fields()
	{
		$this->form_validation->set_rules('id_solicitud', 'ID de solicitud', 'required');
		$this->form_validation->set_rules('documento', 'Documento', 'required');
		$this->form_validation->set_rules('id_img_required', 'Tipo de imagen', 'required');
		//$this->form_validation->set_rules('file', 'Archivo', 'required');

		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		
		if($this->form_validation->run())
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

}

