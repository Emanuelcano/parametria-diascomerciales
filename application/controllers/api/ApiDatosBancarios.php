<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiDatosBancarios extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library('User_library');
		$auth = $this->user_library->check_token();

		if($auth->status == parent::HTTP_OK)
		{
			// MODELS
			$this->load->model('SolicitudDatosBancarios_model','datos_bancarios',TRUE);
			// LIBRARIES
			$this->load->library('form_validation');
		}else{
			$this->session->sess_destroy();
	       	$this->response(['redirect'=>base_url('login')],$auth->status);
		}
	}

	public function update_post()
	{
		if($this->_validate_save_input())
		{
			$id_solicitud = $this->input->post('id_solicitud');

			// Valida si existe el campo id_banco
			if($this->input->post('id_banco') !== null)
			{
				$data['id_banco'] = $this->input->post('id_banco');
			}

			// Valida si existe el campo id_tipo_cuenta
			if($this->input->post('id_tipo_cuenta') !== null)
			{
				$data['id_tipo_cuenta'] = $this->input->post('id_tipo_cuenta');
			}

			// Valida si existe el campo numero_cuenta
			if($this->input->post('numero_cuenta') !== null)
			{
				$data['numero_cuenta']  = $this->input->post('numero_cuenta');
			}
			$data_bank = $this->datos_bancarios->search(['id_solicitud' => $id_solicitud]);
			if(!empty($data_bank))
			{
				$result = $this->datos_bancarios->edit($data_bank[0]['id'], $data);
			}
			else{
				$result = $this->datos_bancarios->save(array_merge($data,['id_solicitud' => $id_solicitud]));
			}

			if($result)
			{
	 			$status = parent::HTTP_OK;
				$response = ['status'  => ['code' => $status, 'ok' => TRUE],
			 				 'message' => "Registro guardado",
			 				];
			}else
			{
				$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar el registro"];
			}

		}else
		{
			$status = parent::HTTP_OK;
			$response = ['status' => ['code' => $status, 'ok' => FALSE],'errors' => $this->form_validation->error_array()];
		}

		$this->response($response);
	}

/***************************************************************************/
// VALIDATIONS
/***************************************************************************/
	private function _validate_save_input()
	{
		$this->form_validation->set_rules('id_solicitud', 'ID de la solicitud', 'required');
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