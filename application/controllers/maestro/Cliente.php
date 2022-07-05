<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'third_party/REST_Controller.php';
require APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

class Cliente extends REST_Controller {   
    public function __construct(){
        parent::__construct();
        $this->load->helper('requests_helper');
        $this->load->helper('formato_helper');
        $this->load->model('CreditoDetalle_model');

        access_control_allow();
    }
    /**
     * consulta las cuotas de un cliente
     * FIXME: cambiar a post
     */
    public function consulta_cuotas_get(){
        $response = array_base();
        $id_cliente = $this->input->get('id_cliente');
        $data = $this->CreditoDetalle_model->getCuotasCliente($id_cliente);
        
        if($data !== false){
            $response['success'] = true;
            $response['data'] = $data;
        }

        $this->response($response, parent::HTTP_OK);
    }
    /**
     * consulta los datos de un cliente
     * FIXME: cambiar a post
     */
    public function consulta_cliente_get(){
        $response = array_base();
        $id_cliente = $this->input->get('id_cliente');
        $data = $this->Cliente_model->getCliente($id_cliente);
        
        if($data !== false){
            $response['success'] = true;
            $response['data'] = $data;
        }

        $this->response($response, parent::HTTP_OK);
    }
}