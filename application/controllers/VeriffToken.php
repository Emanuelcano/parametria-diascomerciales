<?php

use Pusher\PusherException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

defined('BASEPATH') OR exit('No direct script access allowed');

class VeriffToken extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // MODELS
        $this->load->model('user_model');
        $this->load->model('operator_model', '', TRUE);
        $this->load->model('operadores/Operadores_model', 'operadores_model', TRUE);
        $this->load->model('tracker_model', 'tracker_model', TRUE);
        $this->load->model('trackerUser_model', 'trackeruser_model', TRUE);
        // HELPERS 
        $this->load->helper(array('form', 'url', 'encrypt'));
        
    }
    
    public function index()
    {
        if($this->session->userdata('temp_tocken') != null){
            $this->verificacion_token();
        } else{
            redirect('login');
        }
    }
    
    /**
     * @throws PusherException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function verificacion_token(){
        //validamos que tenga un numero para el encvio del codigo
        $id_usuario = $this->session->userdata('id_usuario');
        $telefono = $this->session->userdata('telefono');
        $codigo = $this->session->userdata('temp_tocken');
        $intentos = $this->session->userdata('intentos_tocken');

        if(!is_null($telefono) && strlen($telefono) > 9)
        {
            //el usuario ya esta logueado?
            $is_log_in = $this->trackeruser_model->is_login($id_usuario);
            if(!empty($is_log_in) && is_null($is_log_in[0]->fecha_fin)){

                $this->user_model->logout($id_usuario);
                $this->session->sess_destroy();
                //Dispatcher logic
                $this->load->helper('cookie');
                delete_cookie('__data_operator');
                //TODO Log in error
            }
                if($intentos == 0){

                        
                        //envio del token via mensaje
                        if(ENVIRONMENT == 'development')
                        {
                            $endPoint = "";
                        } else{
                            $endPoint = URL_CAMPANIAS."api/ApiTwilio/send_sms_token_login";
                        }
                        $data = ['phone_number'=>$telefono, 'token'=>$codigo];
                        $curl = curl_init();
                        $options[CURLOPT_URL] = $endPoint;
                        $options[CURLOPT_POSTFIELDS] = $data;
                        $options[CURLOPT_CUSTOMREQUEST] = 'POST';
                        $options[CURLOPT_RETURNTRANSFER] = TRUE;
                        $options[CURLOPT_ENCODING] = '';
                        $options[CURLOPT_MAXREDIRS] = 10;
                        $options[CURLOPT_TIMEOUT] = 30;
                        $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
                
                        curl_setopt_array($curl,$options);
                        $res = json_decode(curl_exec($curl));
                        $err = curl_error($curl);
                        curl_close($curl);

                        if(isset($res->sms->status) && $res->sms->status->ok){
                            $response = $res;
                            $this->session->set_userdata('intentos_tocken', $intentos+1);
                        } else {
                            $response = null;
                        }
                        if ($err)
                        {
                        echo 'cURL Error #:' . $err;die;
                        }

                    }

                

        }else {
            $this->session->set_flashdata('msg_error', 'El usuario no posee un numero de telefono valido para el envio del token');

            
        }

        $this->load->view('veriff_token');

    }


}
