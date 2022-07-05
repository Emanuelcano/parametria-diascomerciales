<?php

class MenuModulo extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('layout/Layout');
		$this->load->library('moduleMenu/ModuleMenu');
		$this->load->model('menu_modulo/MenuModulo_model','menuModulo');
	}
	
	public function index()
	{
		$data['modulos'] = $this->menuModulo->getModulos();
		$layout = new Layout('menuModulos/menuModulos_view', $data);
		$layout->viewLayout();
	}
	
	public function modulo($id)
	{
		$data['id'] = $id;
		$data['modulo'] = $this->menuModulo->getModuloById($id);
		$menu = new ModuleMenu($id);
		$data['menu'] = $menu->getMenuRender(false);
		
		$layout = new Layout('menuModulos/partials/modulo', $data);
		$layout->viewLayout();
	}
	
	public function nuevo($id)
	{
		$data['modulo'] = $this->menuModulo->getModuloById($id);
		
		$layout = new Layout('menuModulos/partials/new', $data);
		$layout->viewLayout();
	}
	
	public function editar($id)
	{
		$data['boton'] = $this->menuModulo->getButtonById($id);
		$data['modulo'] = $this->menuModulo->getModuloById($data['boton']['id_modulo']);
		
		$layout = new Layout('menuModulos/partials/edit', $data);
		$layout->viewLayout();
	}
	
	public function guardar()
	{
		$data = $this->input->post();

		if (!isset($data['estado'])) {
			$data['estado'] = 0;
		} else {
			if ($data['estado'] == 'on') {
				$data['estado'] = 1;
			} else {
				$data['estado'] = 0;
			}
		}

		$idModulo = $data['id_modulo'];
		$this->menuModulo->guardar($data);
		redirect('menu_modulos/MenuModulo/modulo/'.$idModulo);
	}
	
	public function actualizar()
	{
		$data = $this->input->post();
		
		if (!isset($data['estado'])) {
			$data['estado'] = 0;
		} else {
			if ($data['estado'] == 'on') {
				$data['estado'] = 1;
			} else {
				$data['estado'] = 0;
			}
		}
		
		$idModulo = $data['id_modulo'];
		$this->menuModulo->actualizar($data);
		redirect('menu_modulos/MenuModulo/modulo/'.$idModulo);
	}
	
	public function borrar()
	{
		$data = $this->input->post();
		$idModulo = $data['id_modulo'];
		$this->menuModulo->borrar($data);
		redirect('menu_modulos/MenuModulo/modulo/'.$idModulo);
	}
}

