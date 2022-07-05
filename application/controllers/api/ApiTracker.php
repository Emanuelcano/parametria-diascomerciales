<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 * 
 */
class ApiTracker extends REST_Controller 
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('User_library');
		$auth = $this->user_library->check_token();

		if($auth->status == parent::HTTP_OK)
		{
			// MODELS
			$this->load->model('tracker_model','',TRUE);
			$this->load->model('operator_model','',TRUE);
			$this->load->model('solicitud_m','solicitud_model',TRUE);
			// LIBRARIES
			$this->load->library('form_validation');
		}else{
			$this->session->sess_destroy();
	       	$this->response(['redirect'=>base_url('login')],$auth->status);
		}
	}
	
	public function save_post()
	{
		if($this->_validate_save_input())
		{
			$params = $this->input->post();
			$params['observaciones'] = nl2br($this->input->post('observaciones'));
			$params['fecha'] = date('Y-m-d');
			$params['hora'] = date('H:i:s');
			$oper_data = $this->operator_model->search(['idoperador'=>$this->input->post('id_operador')]);
			$params['id_operador'] = $oper_data[0]['idoperador'];
			$params['operador'] = $oper_data[0]['nombre_apellido'];
			// Guarda el track de gestion.
			$inserted_id = $this->tracker_model->save($params);
			if($inserted_id)
			{
				$id_solicitud = $this->input->post('id_solicitud');
				$this->solicitud_model->edit($id_solicitud, ['fecha_ultima_actividad' => date('Y-m-d H:i:s')]);
	 			$status = parent::HTTP_OK;
	 			$comment = $this->tracker_model->search(['track_gestion.id'=>$inserted_id]);
	 			$comment[0]['fecha_string'] = date_to_string($comment[0]['fecha'],'d F a');
				$response = ['status' => ['code' => $status, 'ok' => TRUE],
							 'message' => "Registro guardado",
							 'comment'=>$comment[0]
							];

				$dotEnv = Dotenv\Dotenv::create(FCPATH);
				$dotEnv->load();
				//pusher
				$pusher = new Pusher\Pusher(
					getenv('PUSHER_KEY'),
					getenv('PUSHER_SECRET'),
					getenv('PUSHER_APP_ID'),
					['cluster' => getenv('PUSHER_CLUSTER')]
				);
				$res = $pusher->trigger(
					'channel-track-'.$id_solicitud,
					'received-track-component',$comment[0]);
			}else{
	 			$status = parent::HTTP_INTERNAL_SERVER_ERROR;
				$response = ['status' => ['code' => $status, 'ok' => FALSE], 'errors' => "Falló al guardar el registro"];
			}
		}else
		{
			$status = parent::HTTP_OK;
			$response['status']['code'] = $status;
			$response['status']['ok'] = FALSE;
			$response['errors'] = $this->form_validation->error_array();
		}

		$this->response($response,$status);
		
	}

	public function search_get()
	{
		$params = $this->input->get();

 		$status = parent::HTTP_OK;
		$response = ['status' => ['code' => $status, 'ok' => TRUE], 'data' => $this->tracker_model->search($params)];
		$this->response($response, $status);
		;
	}

/***************************************************************************/
// VALIDATIONS
/***************************************************************************/
	public function _validate_save_input()
	{
		$this->form_validation->set_rules('observaciones', 'Observacion', 'required');
		$this->form_validation->set_rules('id_tipo_gestion', 'tipo de contacto', 'required');
		$this->form_validation->set_rules('id_solicitud', 'ID de la solicitud', 'required');
		$this->form_validation->set_rules('id_operador', 'ID del operador', 'required');

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