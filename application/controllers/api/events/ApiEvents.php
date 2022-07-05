<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

class ApiEvents extends REST_Controller
{
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper(['jwt', 'authorization']);
		$this->load->library('User_library');
		$auth = $this->user_library->check_token();
		
		if($auth->status == parent::HTTP_OK)
		{
			$this->load->model('event/Event_model', 'events', TRUE);
			$this->load->model('cronograma_campanias/Cronogramas_model', 'cronograma_model', TRUE);
		}else{
			$this->session->sess_destroy();
			$this->response(['redirect'=>base_url('login')],$auth->status);
		}
	}
	
	/**
	 * Guarda un evento enviado por post
	 * 
	 * @return void
	 */
	public function save_post()
	{
		$data = $this->post();

		if (!isset($data['endpoint']['params']["templateId"])) {
			$id_mensaje = $this->cronograma_model->getMensageId($data['endpoint']['params']["idCampania"]);
			$data['endpoint']['params']["templateId"] = $id_mensaje[0]["id_mensaje"];
		}

		if($data['endpoint']['params']["type_env"] == "WSP"){
			$endpoint = $data['endpoint']['url'];
		}else{
			$endpoint = "";
		}

		$insertData = [
			'type' => $data['moment']['type']??'',
			'run_date' => $data['moment']['datetime']??'', //reservado para eventos unicos. Datetime Y-m-d H:i:s
			'run_hour' => $data['moment']['hour']??'', //reservado para eventos repetitivos. Todos los repetitivos tienen este campo seteado. H:i:s
			
			// reservado para eventos semanales. 7 caracteres, empezando por el Lunes.
			// Indicando que dias de la semana esta activo con un 1 e inactivos con 0
			// Ejemplo: 1010001 = lunes, miercoes y domingo
			'run_weak_days' => $data['moment']['weak']??'',
			'run_day' => $data['moment']['day']??'', //reservado para eventos mensuales. Indica el dia del mes que correra
			'run_month' => $data['moment']['date']??'', // reservado para eventos anuales. Indica mm/dd que correra
			'enabled' => 1,
			'origin' =>  $data['origin']??'', //codigo que indica donde se ha generado el evento
			'endpoint' => $endpoint,
			'method' => $data['endpoint']['method'],
			'params' => json_encode($data['endpoint']['params']??''),
			"id_campania" => $data['endpoint']['params']["idCampania"],
			"id_mensaje" => $data['endpoint']['params']["templateId"],
			"type_env" => $data['endpoint']['params']["type_env"]
		];
	
		
		$insertedId = $this->events->save($insertData);
		
		
		if ($insertedId) {
			$status = parent::HTTP_CREATED;
			$response['status']  = $status;
			$response['message']  = 'Evento creado con exito.';
		} else {
			$status = parent::HTTP_BAD_REQUEST;
			$response['status']  = $status;
			$response['message']  = 'Error al crear el evento';
		}
		
		$this->response($response, $status);
	}
	
	/**
	 * Borra un evento
	 * 
	 * @return void
	 */
	public function delete_post()
	{
		$data = $this->post();
		$result = $this->events->delete($data['id']);
		if ($result) {
			$status = parent::HTTP_OK;
			$response['message']  = 'Evento borrado correctamente';
		} else {
			$status = parent::HTTP_BAD_REQUEST;
			$response['status']  = $status;
			$response['message']  = 'Error al borrar el evento';
		}
		
		$this->response($response, $status);
	}
	
	/**
	 * Obtiene un evento por post
	 * 
	 * @return void
	 */
	public function get_post()
	{
		$data = $this->post();
		$result = $this->events->get($data['id']);
		if ($result) {
			$status = parent::HTTP_OK;
			$response['status']  = $status;
			$response['data']  = json_encode($result);
		} else {
			$status = parent::HTTP_BAD_REQUEST;
			$response['status']  = $status;
			$response['message']  = 'Error al obtener el evento';
		}
		
		$this->response($response, $status);
	}
	
	/**
	 * Obtiene todos los eventos de un origen en particular
	 * 
	 * @return void
	 */
	public function getAllOrigin_post()
	{
		$data = $this->post();
		$result = $this->events->getAllByOrigin($data['origin']);

		if (is_array($result) && !empty($result)) {
			foreach ($result as $key => $value) {
				if (!is_null($value['id_campania'])) {
					$tipo = $this->events->getCampaniabyId($value['id_campania']);
				}else{
					$params = json_decode($value['params']);
					$tipo = $this->events->getCampaniabyId($params->id_campania);
				}
				if (is_null($value['id_campania']) || empty($value['type_env']) || is_null($value['id_mensaje'])) {
					$data = json_decode($value["params"]);
					$result[$key]["id_campania"] = intval($data->idCampania);
					$result[$key]["id_mensaje"] = intval($data->templateId);
					$result[$key]["type_env"] = $tipo[0]["type_logic"];
					$actulizarData = $this->events->updateData($result[$key], $tipo[0]["type_logic"]);
				}
				if ($tipo[0]["type_logic"] == "SMS") {
					$mensaje = $this->cronograma_model->getMensajeSms($result[$key]["id_campania"], $result[$key]["id_mensaje"]);
					$result[$key]["data_sms"] = $mensaje[0]["mensaje"];
				}
			}
		}
		$status = parent::HTTP_OK;
		$response['status']  = $status;
		$response['data']  = $result;
		
		$this->response($response, $status);
	}
	
	/**
	 * Obtiene todos los eventos
	 * 
	 * @return void
	 */
	public function getAll_post()
	{
		$result = $this->events->getAll();
		
		if ($result) {
			$status = parent::HTTP_OK;
			$response['status']  = $status;
			$response['data']  = json_encode($result);
		} else {
			$status = parent::HTTP_BAD_REQUEST;
			$response['status']  = $status;
			$response['message']  = 'Error al obtener el evento';
		}
		
		$this->response($response, $status);
	}
	
	/**
	 * Habilita un evento
	 * 
	 * @return void
	 */
	public function enable_post()
	{
		$data = $this->post();
		$this->changeStatus($data['id'], 1);
	}
	
	/**
	 * Deshabilita un evento
	 * 
	 * @return void
	 */
	public function disable_post()
	{
		$data = $this->post();
		$this->changeStatus($data['id'], 0);
	}
	
	/**
	 * Cambia de estado un evento.
	 * 
	 * @param $id
	 * @param $status
	 *
	 * @return void
	 */
	private function changeStatus($id, $status)
	{
		if ($status == 1) {
			$result = $this->events->enable($id);
		} else {
			$result = $this->events->disable($id);
		}
		
		if ($result) {
			$status = parent::HTTP_OK;
			$response['status']  = $status;
			$response['message']  = 'Evento actualizado correctamente';
		} else {
			$status = parent::HTTP_BAD_REQUEST;
			$response['status']  = $status;
			$response['message']  = 'Error al actualizar el evento';
		}
		
		$this->response($response, $status);
	}
	
}
