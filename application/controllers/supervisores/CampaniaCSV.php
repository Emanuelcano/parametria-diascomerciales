<?php

class CampaniaCSV extends CI_Controller {

public function __construct() {
    parent::__construct();
    $this->load->helper(array('download', 'file', 'url', 'html', 'form'));
    // $this->folder = 'C:/xampp/htdocs/BackednColombia/public/csv/'; //LOCAL
    $this->folder = base_url().'public/csv/'; //SERVIDOR
}
  
public function index()
{
    $this->load->view('upload_form', array('error' => ' ' ));
}

public function downloads(){

    $nombreArchivo = $_GET['file'];
        
    $data = file_get_contents($this->folder.$nombreArchivo);
    force_download($nombreArchivo,$data);
    
}

}