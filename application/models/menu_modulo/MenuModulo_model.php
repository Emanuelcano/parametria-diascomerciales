<?php

class MenuModulo_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->db_gestion = $this->load->database('gestion', true);
		$this->db_chat = $this->load->database('chat', true);
		$this->db_telefonia = $this->load->database('telefonia', true);
		$this->db_solicitudes = $this->load->database('solicitudes', true);
		$this->db_parametria = $this->load->database('parametria', true);
	}
	
	public function getModulos()
	{
		$modulos = $this->db_gestion->select('*')
			->from('modulos')
			->get()->result_array();
		
		return $modulos;
	}
	
	public function getButtons($modulo_id)
	{
		$modulo_buttons = $this->db_gestion->select('*')
			->from('modulo_buttons')
			->where('id_modulo', $modulo_id)
			->get()->result_array();
		
		return $modulo_buttons;
	}
	
	public function getModuloById($id)
	{
		$modulo = $this->db_gestion->select('*')
			->from('modulos')
			->where('id', $id)
			->get()->row_array();
		
		return $modulo;
	}
	
	public function getButtonById($id)
	{
		$modulo = $this->db_gestion->select('*')
			->from('modulo_buttons')
			->where('id', $id)
			->get()->row_array();
		
		return $modulo;
	}
	
	public function guardar($data)
	{
		$result = $this->db_gestion->insert('modulo_buttons',$data);
		return $result;
	}
	
	public function actualizar($data)
	{
		$this->db_gestion->where('id', $data['id']);
		$update = $this->db_gestion->update('modulo_buttons', $data);
		
		return $update;
	}
	
	public function borrar($data)
	{
		$this->db_gestion->where('id', $data['id']);
		$this->db_gestion->delete('modulo_buttons');
		$delete = $this->db_gestion->affected_rows();
		return $delete;
	}
}
