<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiSendMail extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		// Models
		$this->load->model('solicitud_m', 'solicitud_model', TRUE);
		// Library
		$this->load->library('SendMail_library');
	}

	public function send_email_validation_get($id_solicitud)
	{
		$solicitud = $this->solicitud_model->simple_list(['id' => $id_solicitud]);

		$status = parent::HTTP_OK;
		if(!empty($solicitud) && isset($solicitud[0]['codigo_enviado_mail']) && $solicitud[0]['codigo_enviado_mail'] != 0)
		{
 			$response['status']['code'] = parent::HTTP_OK;
			$response['status']['ok'] = TRUE;
			$response['email'] = $this->sendmail_library->email_validation_code($solicitud[0]['email'], $solicitud[0]['nombres'],$solicitud[0]['apellidos'], $solicitud[0]['codigo_enviado_mail']);
		}else{
			$response['status']['code'] = parent::HTTP_OK;
			$response['status']['ok'] = FALSE;
			$response['email'] = '';
		}
		$this->response($response, $status);
	}
        
        public function desbloquear_usuario_post(){
            $email = $this->input->post('email');
            $response=[];
            if($this->solicitud_model->borrar_login($email))
            {
                    $status = parent::HTTP_OK;
                    $response['status']['ok'] = TRUE;
            }else{
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response['status']['ok'] = FALSE;
            }
            $this->response($response, $status);
        }

}