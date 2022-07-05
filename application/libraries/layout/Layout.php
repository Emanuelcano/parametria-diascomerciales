<?php

require_once 'application/libraries/layout/Autoloader.php';

class Layout
{
	private $header;
	private $footer;
	private $content;
	private $data;
	private $CI;
	private $menu;
	
	/**
	 * @param string $content
	 * @param array $data
	 * @param string $header
	 * @param string $footer
	 */
	public function __construct(string $content = '', array $data = [], string $header = '', string $footer = '')
	{
		$this->CI =& get_instance();
		$this->db_gestion = $this->CI->load->database('gestion', true);
		
		$this->header = ($header) ?: 'layouts/adminLTE__header';
		$this->footer = ($footer) ?: 'layouts/adminLTE__footer';
		$this->content = $content;
		$this->data = $data;
		
		$currentButton = $this->getButtonByCurrentUrl();
		
//		revisar, porque si no tiene botones deberia mostarse igual. Actualmente tira 404 por no tener botones de menu
//		if ($currentButton['estado'] == 0) {
//			$this->content = 'layouts/adminLTE__404';
//		}
		
//		if (!isset($this->data['title']) or $this->data['title'] == '') {
//			$this->data['title'] = $currentButton['title'];
//		}
//		
		
		//$this->menu = new ModuleMenu($currentButton['id_modulo'], $currentButton['path']);
	}
	
	/**
	 * Obtiene el boton actual segun la url
	 * 
	 * @return mixed|null
	 */
	private function getButtonByCurrentUrl()
	{
		$buttons = $this->db_gestion->select('*')
			->from('modulo_buttons')
			->get()->result_array();
		
		$current = current_url();
		$currentButton = null;
		
		foreach ($buttons as $button) {
			$relative = $button['path'];
			$arrCurrent = explode('/', $current);
			$arrRelative = explode('/', $relative);
			$containsSearch = count(array_intersect($arrRelative, $arrCurrent)) === count($arrRelative);
			if ($containsSearch) {
				$currentButton = $button;
				break;
			}
		}
		
		return $currentButton;
	}
	
	/**
	 * Renderiza el layout
	 * 
	 * @return void
	 */
	public function viewLayout(): void
	{
		$this->data['buttons'] = "";
		$this->data['layoutContent'] = $this->CI->load->view($this->content, $this->data, true);
		
		$this->CI->load->view($this->header, $this->data);
		$this->CI->load->view('layouts/layoutContent', $this->data);
		$this->CI->load->view($this->footer, $this->data);
	}
	
	/**
	 * Obtiene el content como variable
	 * 
	 * @return mixed
	 */
	public function getContent()
	{
		return $this->CI->load->view($this->content, $this->data, true);
	}
	
	
}
