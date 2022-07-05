<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiImage extends REST_Controller
{
    public function __construc()
    {
		parent::__construct();
    }

    //FUNCIÓN PARA CREAR LA MINIATURA A LA MEDIDA QUE LE DIGAMOS
    public function create_thumbnail_post($filename)
    {
        $config['image_library'] = 'gd2';
        //CARPETA EN LA QUE ESTÁ LA IMAGEN A REDIMENSIONAR
        $config['source_image']   = 'uploads/' . $filename;
        $config['create_thumb']   = true;
        $config['maintain_ratio'] = true;
        //CARPETA EN LA QUE GUARDAMOS LA MINIATURA
        $config['new_image'] = 'uploads/thumbs/';
        $config['width']     = 150;
        $config['height']    = 150;
        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
    }

    public function save_image()
    {
        $config = [
            "upload_path"   => "./productos",
            "allowed_types" => "gif|jpg|png",
            "max_size"      => "32000",
        ];
        $this->load->library('upload', $config);
        $subir = $this->upload->do_upload('file');
        $data  = array("upload_data" => $this->upload->data());
        $datos = array(
            'avatar'    => $data['upload_data']['file_name'],
        );
        if ($this->Res_producto_model->guardar($datos) == true) {
            echo 'Registro Guardado';
        } else {
            echo 'No se pudo guardar los datos';
        }
        if ($subir) {
            $this->_create_thumbnail($data['upload_data']['file_name']);
            echo ', Foto Subida';
        } else {
            echo ', Sin foto';
            //echo $this->upload->display_errors();
        }
    }

}