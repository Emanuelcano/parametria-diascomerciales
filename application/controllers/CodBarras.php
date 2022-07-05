<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CodBarras extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	 public function __construct()
    {
        parent::__construct();
 		
        $this->load->library('response');
        $this->load->library('CREDIBEL_BarcodeGeneratorPNG');
        
    }
	public function index($mode=false)
	{	
		$status=200;
		$page='CodBarras/codbarras';
		$data['title']='CodBarras';
    	
		// $data['response']=$this->generarCodBarrasPng($dni);
		
    	$data['response']=1;
		$this->response->load($page, $status, $data, $mode);
		
	}

	public function generarCodBarrasPng() {
		$Producto = $this->input->post('Producto');
      $ram=rand(9999, 99999999);
      $dni=95739793;
      $height = "35";
      $scale = "2";
      $bgcolor = "#FFFFFF";
      $color = "#000000";
      $type = "png";
      $encoder = "SAETA";
      $IMG_RAPIPAGO = $ram;
      $numCodBarras = '3348' . $Producto . '0100';

      $nombreimg=$IMG_RAPIPAGO . '_' . $numCodBarras .'.'.$type;
      $generator = new CREDIBEL_BarcodeGeneratorPNG();
     
      $ruta='public/codbarras/' . $IMG_RAPIPAGO . '_' . $numCodBarras .'.'.$type;
      file_put_contents('public/codbarras/' . $IMG_RAPIPAGO . '_' . $numCodBarras .'.'.$type, $generator->getBarcode($numCodBarras, $generator::TYPE_CODE_128, 2, 70));

      echo ($ruta);
    
  }



}
