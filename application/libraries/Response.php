<?php
class Response {
		var $template_data = array();
		var $data = array();
		
		function set($content_area, $value)
		{
		$this->template_data[$content_area] = $value;
		}
	
		function html($template = '', $name ='', $view = '' , $view_data = array(), $return = FALSE)
		{               
		$this->CI =& get_instance();
		
		$this->set($name , $this->CI->load->view($view, $view_data, TRUE));
		$this->CI->load->view('layouts/'.$template, $this->template_data);
		}

		function load($page='', $status=200, $data = array(), $mode= false)
    	{
		//print_r($data);die();

    	//si llega el parametro mode se lo pasamos al formulario que corresponda
		$data['modeForm']  = ($mode) ? '/'.$mode : '';

		//Código de respuesta 400 = error en la validación de campos, respuesta = 200 respuesta de global porcessing
		
		//Si es modo formulario cargo estructura de forms, siempre que sea invocado de internamente cargo template por defecto
		$struct = ($mode == FORM) ? 'formsFrame' : 'template';

       	//Si es modo API devuelvo un json, de caso contrario envio la vista que corresponda 
       	
       	$output = ($mode == API) ? $this->json_response($data['response'], $status) : $this->html($struct, 'contents' , $page, $data);
  
    	return $output;
    	}

		function json_response($message = '', $code = 200)
		{
				// $message = is_array($message)?$message['response']:$message;
			
   				header_remove();
			    http_response_code($code);
			    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
			    header('Content-Type: application/json;charset=utf-8');
			    $status = array(
			        200 => '200 OK',
			        400 => '400 Bad Request',
			        422 => 'Unprocessable Entity',
			        500 => '500 Internal Server Error'
			        );
			    header('Status: '.$status[$code]);
			    echo json_encode(array(
			        'status' => $code , 
			        'message' => str_replace(['<p>', '</p>'], '',$message)
			    ), JSON_UNESCAPED_UNICODE); //  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES

		}
}