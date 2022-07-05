<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

class ApiTableroFlujo extends REST_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->library('User_library');
		$auth = $this->user_library->check_token();

        if($auth->status == parent::HTTP_OK) {
            // MODELS
            $this->load->model('tablero_flujo/TableroFlujo_model');
            // LIBRARIES
            // $this->load->library('form_validation');
            // HELPERS
            // $this->load->helper('date');
		}else{
			$this->session->sess_destroy();
            $this->response(['redirect'=>base_url('login')], $auth->status);
		}
    }

    public function getDataPieBarLine_post() {
        if ($this->input->is_ajax_request()) {
            $tipoSolicitud = $this->input->post('selectTipo');
            $fechas = explode('|', $this->input->post('fecha'));
            $fechaIni = date_format(date_create(trim($fechas[0])), 'Y-m-d') . ' 00:00:00.000000';
            $fechaFin = date_format(date_create(trim($fechas[1])), 'Y-m-d') . ' 23:59:59.000000';

            $pie       = $this->TableroFlujo_model->getCountRespuestaAnalisis($tipoSolicitud, $fechaIni, $fechaFin);
            $bar       = $this->TableroFlujo_model->getStatusFlujo($tipoSolicitud, $fechaIni, $fechaFin);
            $lineOne   = $this->TableroFlujo_model->getSolicitudesPorHora($tipoSolicitud, $fechaIni, $fechaFin);
            $lineTwo   = $this->TableroFlujo_model->getAprobadasPorHora($tipoSolicitud, $fechaIni, $fechaFin);
            $lineThree = $this->TableroFlujo_model->getRechazadasPorHora($tipoSolicitud, $fechaIni, $fechaFin);

            $status = parent::HTTP_OK;
            $response['status']['ok']   = TRUE;
            $response['status']['code'] = $status;
            $response['pie']            = $pie;
            $response['bar']            = $bar;
            $response['lineOne']        = $lineOne;
            $response['lineTwo']        = $lineTwo;
            $response['lineThree']      = $lineThree;

            return $this->response($response, $status);
        } else {
            show_404();
        }
    }    
}
