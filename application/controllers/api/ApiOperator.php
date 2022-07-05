<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiOperator extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		// MODELS
		$this->load->model('operator_model','',TRUE);
		$this->load->model('AgendaOperadores_model', '', TRUE);
		// LIBRARIES
		$this->load->library('form_validation');
	}

	public function save_post()
	{


	}

	public function search_get()
	{
		$status = parent::HTTP_OK;
		$response['operators'] = $this->operator_model->search($params = $this->input->get());
 		$response['status']['code'] = parent::HTTP_OK;
		$response['status']['ok'] = TRUE;
		$this->response($response, $status);
	}

	public function getOpeBySolicitud_get($idSolicitud)
	{
            $params['id'] = $idSolicitud;
            $status = parent::HTTP_OK;
            $response['operator'] = $this->operator_model->getOpeBySolicitud($params);
            $response['status']['code'] = parent::HTTP_OK;
            $response['status']['ok'] = TRUE;
            $this->response($response, $status);
	}

	public function agendar_post()
	{		
		$config = [
			[
				'field' => 'id_solicitud',
				'label' => 'id_solicitud',
				'rules' => 'required'
			],
			[
				'field' => 'id_operador',
				'label' => 'id_operador',
				'rules' => 'required'
			],
			[
				'field' => 'nombres',
				'label' => 'Nombres',
				'rules' => 'required'
			],
			[
				'field' => 'apellidos',
				'label' => 'Apellidos',
				'rules' => 'required'
			],
			[
				'field' => 'fecha_agenda',
				'label' => 'Fecha',
				'rules' => 'required'
			],
			[
				'field' => 'hora_agenda',
				'label' => 'Hora',
				'rules' => 'required'
			],
			[
				'field' => 'motivo',
				'label' => 'Motivo',
				'rules' => 'required'
			]
		];
		$date = $this->input->post('fecha_agenda');
		$time = $this->input->post('hora_agenda');
		$combinedDT = date('Y-m-d H:i:s', strtotime("$date $time"));
		$data = [
			'id_solicitud' => $this->input->post('id_solicitud'),
			'id_operador' => $this->input->post('id_operador'),
			'nombres' => strtoupper($this->input->post('nombres')),
			'apellidos' => strtoupper($this->input->post('apellidos')),
			'fecha_hora_agendado' => date('Y-m-d H:i:s'),
			'fecha_hora_llamar' => $combinedDT,
			'fecha_llamar' => $this->input->post('fecha_agenda'),
			'hora_llamar' => $this->input->post('hora_agenda'),
			'motivo' => $this->input->post('motivo')
		];
	
		$response = array_base();
		$countCasos = count($this->AgendaOperadores_model->getAgendaOperadores(['id_operador'=>$this->input->post('id_operador')]));
		$countCasosPorOpe = count($this->AgendaOperadores_model->getAgendaOperadores(
			[
				'id_solicitud'=>$this->input->post('id_solicitud'),
				'id_operador'=>$this->input->post('id_operador') 
			])
		);
		
		if( $countCasosPorOpe > 0){
			$status = self::HTTP_OK;
			$message = "Ya existe en la agenda.";
			$response = ['status' => $status,'error' =>true, 'message' => $message ];
		}else if($countCasos >= 12){
			$status = self::HTTP_OK;
			$message = "<br><strong>Supero el número de solicitudes agendadas <br> debe eliminar al menos un registro.</strong><br>";
			$response = ['status' => $status,'error' =>true, 'message' => $message ];
		}else{
			
			if($this->_validate_save_input($config)){
				$id_agenda_operador = $this->operator_model->save($data, 'gestion.agenda_operadores');
				if($id_agenda_operador){
					$status = self::HTTP_OK;
					$message = "<br><strong>Se agendo correctamente.</strong><br>";					
					$response = ['status' => $status,'error' =>false, 'data' => ['id'=>$id_agenda_operador], 'message' => $message ];
				}else{
					$status = self::HTTP_INTERNAL_SERVER_ERROR;
					$message = "No se pudo agendar.";
					$response = ['status' => $status, 'error' =>$id_agenda_operador['error'], 'message' => $message ];
				}
			}else{
				$status = parent::HTTP_BAD_REQUEST;
				$validationErr = $this->form_validation->error_array();
				$response = ['status' => $status,'error' =>$validationErr ];
			}
		}
		$this->response($response);
	}

/***************************************************************************/
// VALIDATIONS
/***************************************************************************/
	public function _validate_save_input($config)
	{
		$this->form_validation->set_rules($config);
		$this->form_validation->set_message('required', 'El campo %s es obligatorio');
		if($this->form_validation->run())
			return true;
		else
			return false;
	}
}
