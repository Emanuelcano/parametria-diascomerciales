<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 * 
 */
class ApiAdminTracker extends REST_Controller 
{
	public function __construct()
	{
		parent::__construct();
		// MODELS
		$this->load->model('tracker_model','',TRUE);
		$this->load->model('solicitud_m','solicitud_model',TRUE);
		
		// LIBRARIES
		$this->load->library('form_validation');
		$this->load->helper(['jwt', 'authorization']); 
	}
	
	public function save_post()
	{
		if($this->_validate_save_input())
		{


			$solicitud = $this->input->post('id_solicitud');
			if (strlen($solicitud) > 7) {
				$decode = AUTHORIZATION::decodeDataEncript( $solicitud );
				$solicitud = $decode;

 			}

			$params['id_solicitud']  = $solicitud;
			$params['observaciones'] = nl2br($this->input->post('observaciones'));
			$params['fecha'] = date('Y-m-d');
			$params['hora'] = date('H:i:s');
			
			// Valida si existe el campo id_credito
			$id_credit = $this->input->post('id_credito');
			if(isset($id_credit))
			{
				$params['id_credito'] = $id_credit;
			}

			// Valida si existe el campo id_cliente
			$id_client = $this->input->post('id_cliente');
			if(isset($id_client))
			{
				$params['id_cliente'] = $id_client;
			}
			
			// Valida si existe el campo operador
			$operator = $this->input->post('operador');
			if(isset($operator))
			{
				$params['operador'] = $operator;
			}else{
				$params['operador'] = 'Proceso Automático';
			}

			// Valida si existe el campo id_operador
			$id_operator = $this->input->post('id_operador');
			if(isset($id_operator))
			{
				$params['id_operador'] = $id_operator;
			}
			
			// Valida si existe el campo id_tipo_gestion
			$type_manag = $this->input->post('id_tipo_gestion');
			if(isset($type_manag))
			{
				$params['id_tipo_gestion'] = $type_manag;
			}

			// Guarda el track de gestion.
			$inserted_id = $this->tracker_model->save($params);
			if($inserted_id)
			{
				
				$this->solicitud_model->edit($solicitud, ['fecha_ultima_actividad' => date('Y-m-d H:i:s')]);
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
					'channel-track-'.$solicitud,
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


/***************************************************************************/
// VALIDATIONS
/***************************************************************************/
	public function _validate_save_input()
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