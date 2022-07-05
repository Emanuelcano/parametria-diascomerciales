<?php

class Event_model extends CI_Model
{
	
	/**
	 *
	 */
	public function __construct()
	{
		$this->maestro = $this->load->database('maestro', true);
		$this->db_campania = $this->load->database('campanias', true);
	}
	

	/**
	 * Obtiene todos los eventos por el origen
	 * 
	 * @param $origin
	 *
	 * @return mixed
	 */
	public function getAllByOrigin($origin)
	{
		$result = $this->maestro->select('*')
			->from('events')
			->where('origin', $origin)
			->order_by('run_date', 'ASC')
			->order_by('run_month', 'ASC')
			->order_by('run_day', 'ASC')
			->order_by('run_hour', 'ASC')
			->get()->result_array();
		return $result;
	}
	
	/**
	 * Obtiene todos los eventos
	 * 
	 * @return mixed
	 */
	public function getAll()
	{
		$result = $this->maestro->select('*')
			->from('events')
			->get()->result_array();
		return $result;
	}
	
	/**
	 * Obtiene un evento por ID
	 * 
	 * @param $id
	 *
	 * @return mixed
	 */
	public function get($id)
	{
		$result = $this->maestro->select('*')
			->from('events')
			->where('id', $id)
			->get()->row_array();
		return $result;
	}
	
	/**
	 * Guarda un evento
	 * 
	 * @param $data
	 *
	 * @return false
	 */
	public function save($data)
	{
		$this->maestro->insert('events', $data);
		$id =  $this->maestro->insert_id();
		
		if ($id) {
			return $id;
		}
		
		return false;
	}
	
	/**
	 * Borra un evento
	 * 
	 * @param $id
	 *
	 * @return bool|void
	 */
	public function delete($id)
	{
		$this->maestro->where('id', $id);
		$this->maestro->delete('events');
		
		$result = $this->maestro->affected_rows();
		if ($result) {
			return true;
		}
	}
	
	/**
	 * Deshabilita un evento
	 * 
	 * @param $id
	 *
	 * @return bool
	 */
	public function disable($id)
	{
		return $this->changeStatus($id, 0);
	}
	
	/**
	 * Habilita un evento
	 * 
	 * @param $id
	 *
	 * @return bool
	 */
	public function enable($id)
	{
		return $this->changeStatus($id, 1);
	}
	
	/**
	 * Cambia el estado a un evento
	 * 
	 * @param $id
	 * @param $status
	 *
	 * @return bool
	 */
	public function changeStatus($id, $status)
	{
		$this->maestro->where('id', $id);
		$this->maestro->update('events', ['enabled' => $status]);
		
		$result = $this->maestro->affected_rows();
		if ($result) {
			return true;
		}
		
		return false;
	}
	

	public function getCampaniabyId($idCamp)
	{
		$this->db_campania->select("type_logic");
		$this->db_campania->from("campania");
		$this->db_campania->where("id_logica", $idCamp);
		$tipo = $this->db_campania->get();
		return $tipo->result_array();
	}

	public function updateData($array, $tipo)
	{
		$this->maestro->set('id_campania', $array["id_campania"]);
		$this->maestro->set('id_mensaje', $array["id_mensaje"]);
		$this->maestro->set('type_env', $tipo);
		$this->maestro->where('id', $array['id']);
		$this->maestro->update('events');

		$result = $this->maestro->affected_rows();
		if ($result) {
			return true;
		}else{
			return false;
		}
		
	}
}

