<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_library
{
	private $CI;
	private $token = NULL;

    public function __construct()
    {
        $this->CI =& get_instance();
		$this->CI->load->model('user_model','',TRUE);
    }

    public function check_token()
    {
    	$auth['status'] = 200;
    	return (object) $auth;
    	
    	if($this->CI->input->method(TRUE) == 'POST')
		{
    		$this->token = $this->CI->input->post('token');
			$event = $this->CI->input->post('action');
		}else
		{
    		$this->token = $this->CI->input->get('token');
			$event = $this->CI->input->get('action');
		}

		if(!isset($this->token))
		{
    		$this->token = $this->CI->session->userdata('token');
		}
				
		$auth = $this->CI->user_model->check_token($this->token, $event);

		return $auth;
	}
}
