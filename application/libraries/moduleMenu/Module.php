<?php

class Module
{
	private $CI;
	private $data;
	private $currentRelativePath;
	
	public function __construct($moduleId, $currentRelativePath)
	{
		$this->CI =& get_instance();
		$this->db_gestion = $this->CI->load->database('gestion', true);
		$this->data = $this->db_gestion->get_where('modulos', array('id' => $moduleId))->row_array();
		$this->currentRelativePath = $currentRelativePath;
	}
	
	/**
	 * Obtiene los botones del modulo
	 * 
	 * @return array
	 */
	public function getButtons($onlyEnabled = true)
	{
		$buttons = array();
		
		$this->db_gestion->select('*')
			->where('id_modulo', $this->data['id'])
			->order_by('position', 'asc');
		
		if ($onlyEnabled) {
			$this->db_gestion->where('estado', 1);
			$this->db_gestion->or_where('id', 1);
		}
		
		$query = $this->db_gestion->get('modulo_buttons');
		
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$active = ($this->currentRelativePath === $row['path']);
				$button = new Button($row);
				$buttons[] = $button->render($active);
			}
		}
		
		return $buttons;
	}
	
}
