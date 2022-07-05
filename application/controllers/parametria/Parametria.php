<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Parametria extends CI_Controller
{

  public function __construct()
    {
      parent::__construct();
      
      $this->load->library('form_validation'); //carga libreria de validacion
      
      
 
	}

    public function index()
      {

        $title['title'] = "Parametria";
        /* $this->load->view('layouts/header',$title);
        $this->load->view('layouts/nav');
        $this->load->view('layouts/sidebar'); */
        $this->load->view('layouts/adminLTE', $title);
  

        $this->load->view('parametria/dashboardParametria');
      
      }

}