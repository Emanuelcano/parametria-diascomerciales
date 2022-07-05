<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

   
class App extends REST_Controller {

       

	/**
	 * Devuelve SPA de VUE
	 */
	 public function __construct()
    {
    	 header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        parent::__construct();
 
    }
	public function index_get()
	{	
        $message = array (
			'message' => 'Backend API by Solventa SAS, All rights reserved.',
			'version' => '1.0.0',
			'author' => 'Solventa SAS.'
		);
		$status = parent::HTTP_OK;
           $response = ['status' => $status, $message];
           $this->response($response, $status);

		
	}
}
