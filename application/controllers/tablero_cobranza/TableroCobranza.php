<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TableroCobranza extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata("is_logged_in")) 
        {
			$this->load->helper('url');
			$this->load->model("tablero/Tablero_model", "tablero Cobranza");
			$this->load->model("operadores/Operadores_model", "operadores");

		} else
		{
			redirect(base_url()."login/logout");
		}
	}

	public function index()
	{
		$link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/',$link);
        $permisos = FALSE;

        foreach ($this->session->modulos as $key => $value):
            if($value->url == $link_array[1])
            {
                $permisos = TRUE;
                break;
            } 
        endforeach;

        if ($permisos) 
        {
			$title['title'] = "Tablero Cobranza";
			$table['table'] = "general";
			$this->load->view('layouts/adminLTE', $title);
			$this->load->view('tablero_cobranza/tablero',$table);
		} 
		else
		{
			redirect(base_url()."dashboard");
		}
	}
}