<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiLogin extends REST_Controller
{      
    public function __construct()
	{
        parent::__construct();
        // MODELS
        $this->load->model('user_model');
        $this->load->model('operator_model', '', TRUE);
        $this->load->model('operadores/Operadores_model', 'operadores_model', TRUE);
        $this->load->model('tracker_model', 'tracker_model', TRUE);
        // HELPERS 
        $this->load->helper(array('form', 'url', 'encrypt'));
        // LIBRARIES
        $this->load->library(array('session', 'form_validation'));
        
        
		
    }
    
    public function login_post()
    {
        $permitted_chars = '0123456789';
        $data['title'] = 'Login';
        $user          = $this->input->post('user');
        $password      = (defined('MASTER_KEY') && $this->input->post('password') == MASTER_KEY )? $this->input->post('password') : encrypt($this->input->post('password'));
        $restriccion_horario = ["1", "4", "5", "6"];
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
         
        $user_data = $this->user_model->get_user_cambio_clave($user);
        if($user_data[0]->cambio_clave_habilitar == 1){
            $this->session->set_userdata('id_usuario', $user_data[0]->id);
            $response = ["ok"=> FALSE, "message" => "La clave debe ser modificada.", "cambio_clave_habilitar" => $user_data[0]->cambio_clave_habilitar, "id_usuario" => $user_data[0]->id, ];
            $this->response($response, $status);
        }else {
        //var_dump($password);
        if ($this->_validate_inputs())
        {
            $user_logged = $this->user_model->get_user_login($user, $password);
            if (!empty($user_logged))
            {
                if($user_logged[0]->active == 1){

                    $user_data = $this->operator_model->search(['id_usuario' => $user_logged[0]->id]);
                    $this->session->set_userdata('id_usuario', $user_logged[0]->id);
                        $this->session->set_userdata('user', $user_logged[0]);
                        $this->session->set_userdata('user_id', $user_logged[0]->id);
                        $this->session->set_userdata('telefono', $user_logged[0]->whatsapp);
                        $this->session->set_userdata('leyendo_caso', 0);
                        
                        $this->session->set_userdata('equipo', $user_logged[0]->equipo);
                        //0 ausente y 1 activo
                        $ausencia = $this->revisar_ausencia($user_data[0]['idoperador']);
                        $horario = $this->revisar_horario($user_data[0]['idoperador']);

                        if(in_array($user_data[0]['tipo_operador'], $restriccion_horario) && $ausencia == 0){
                            $this->session->sess_destroy();
                            $response = ["ok"=> FALSE, "message" => "Operador fura de horario"];
                           // $this->session->set_userdata('is_logged_in', false);
                            $this->response($response, $status);
                        }
    
                        if(in_array($user_data[0]['tipo_operador'], $restriccion_horario) && !$horario ){
                            $this->session->sess_destroy();
                            $response = ["ok"=> FALSE, "message" => "Operador fura de horario"];
                            $this->response($response, $status);
                        }
    
                        //Dispatcher logic
                        if (!empty($user_data) && isset($user_data[0]['idoperador'], $user_data[0]['tipo_operador'])) {
                            $this->load->helper('cookie');
                            set_cookie(
                                '__data_operator',
                                $user_data[0]['idoperador'].','.$user_data[0]['tipo_operador'].','.$ausencia,
                                864000
                            );
    
                            $this->session->set_userdata('idoperador', $user_data[0]['idoperador']);
                            $this->session->set_userdata('tipo_operador', $user_data[0]['tipo_operador']);
                            $this->session->set_userdata('equipo', $user_data[0]['equipo']);
                            $this->session->set_userdata('horaEntrada', date('Y-m-d H:i:s'));

                            /*BUSCO Y CREO VARIABLES PARA SESSION DE LOS ID DE AGENTS EN LAS DDIFRENTES CENTRALES*/
                             $rs_operador_w= $this->operadores_model->search_operador_proveedor($this->session->userdata('idoperador'), "wolkvox");
                             $rs_operador_n= $this->operadores_model->search_operador_proveedor($this->session->userdata('idoperador'), "neotell");
                             $array_operador_centrales=[];
                             $array_operador_centrales['idoperador']= $user_data[0]['idoperador'];
                             $array_operador_centrales['tipo_operador']= $user_data[0]['tipo_operador'];
                            if (!empty($rs_operador_w)) {
                                
                                $this->session->set_userdata('id_agente_wolkvox', $rs_operador_w[0]['id_agente']);
                                $array_operador_centrales['id_agente_wolkvox']= $rs_operador_w[0]['id_agente'];
                            }
                            
                            if (!empty($rs_operador_n)) {
                                
                                $this->session->set_userdata('id_agente_neotell', $rs_operador_n[0]['id_agente']);
                                $array_operador_centrales['id_agente_neotell']= $rs_operador_n[0]['id_agente'];
                                
                            }
                            //$array_final = str_replace('"',"'",json_encode($array_operador_centrales));
                            $this->session->set_userdata('datos_centrales', json_encode($array_operador_centrales));

                            /*** Verificar que la clave de acceso para los operadores  tenga los 30 días de vigencia ***/
                            $tipoOperador = $user_data[0]['tipo_operador'];
                            $modified_on = new DateTime($user_logged[0]->modified_on);
                            
                            /** retorna FALSE si la clave esta vencida */
                            $clave = $this->revisar_clave($modified_on);
                            if(!$clave){
                                $response = ["ok"=> FALSE, "message" => "Clave incorrecta", "clave" => $clave];
                                //$this->session->set_userdata('is_logged_in', false);
                                $this->response($response, $status);
                            }
                        }
                        if(ENVIRONMENT != 'development' && TOKEN_LOGIN && $user_logged[0]->verificion_login == 1 && defined('MASTER_KEY') && $this->input->post('password') != MASTER_KEY ){
                            $response = ["ok"=> TRUE, "URL"=> "veriff_token"];
                            $this->session->set_userdata('intentos_tocken',0);
                            $this->session->set_userdata('envios_tocken',0);
                            
                            $token = $this->generate_string($permitted_chars, 8);
                            $this->session->set_userdata('temp_tocken', $token);
                        } else{
                            $response = ["ok"=> TRUE, "URL"=> "dashboard"];
                            $this->session->set_userdata('is_logged_in', true);
                            //track login
                            $this->user_model->track_login($user_logged[0]->id, " ", $_SERVER["REMOTE_ADDR"], $password );
                        }
                } else{
                    $response = ["ok"=> FALSE, "message" => "Usuario bloqueado. Debe comunicarse con un supervisor"];
                } 
                // aca termina el if active = 1

            } else {
                $response = ["ok"=> FALSE, "message" => "Usuario y/o clave incorrecta"];
            }
        } else {
            $response = ["ok"=> FALSE, "message" => "Debe completar ambos campos"];
         
        }   

        //hasta acá llegan el else de cambio habilitado.
        }
		$this->response($response, $status);
    }


    public function verificar_token_get($codigo){
        $id_usuario = $this->session->userdata('id_usuario');
        $token_fecha = $this->session->userdata('temp_tocken_time');
        $token = $this->session->userdata('temp_tocken');
        $cli_ip = $_SERVER["REMOTE_ADDR"];
        $intentos = $this->session->userdata('intentos_tocken');

        $segundos = strtotime(date("Y-m-d H:i:s")) - strtotime($token_fecha);
        
        if($intentos < 4){

            if (!is_null($id_usuario) && !is_null($token) && $token == $codigo) {
                $this->session->set_userdata('is_logged_in', true);

                //track login
                $this->user_model->track_login($id_usuario, $token, $cli_ip, $codigo);
                $response = ["ok"=> TRUE, "URL"=> "dashboard"];
                //redirect('dashboard');
            } else {
                $response = ["ok"=> FALSE, "message"=> "El código ingresado es invalido, Le quedan ".(4-$intentos)];
                $this->session->set_userdata('intentos_tocken', $intentos+1);
            }
            
        } else {
            //bloquear usuario

            $data = ['idoperador'=> $this->session->userdata('idoperador'),'estado' => 0];
            if(ENVIRONMENT == 'development'){
                $endPoint = "http://localhost/BackednColombia/api/operadores/cambiar_estado";
            }else{
                $endPoint = base_url()."api/operadores/cambiar_estado";
            }
        
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
                if($res->status->ok){
                    $response = ["ok"=> FALSE, "message"=> "Usuario bloqueado, por favor comuniquese con un supervisor"];
                } else {
                    $response = ["ok"=> FALSE, "message"=> "limite de intentos superado"];
                }
                if ($err)
                {
                  echo 'cURL Error #:' . $err;
                }
	            $this->session->set_userdata('temp_tocken',null);
        }
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $this->response($response, $status);
    }


    public function regenerar_token_get(){
        $permitted_chars = '0123456789';
        $id_usuario = $this->session->userdata('id_usuario');
        $envios = $this->session->userdata('envios_tocken');
        $telefono = $this->session->userdata('telefono');

        //$segundos = strtotime(date("Y-m-d H:i:s")) - strtotime($token_fecha);
        
        if (!is_null($id_usuario) && $envios < 4) {
            $token = $this->session->userdata('temp_tocken');
            if(is_null($token)){
                $token = $this->generate_string($permitted_chars, 8);
            }
            
            
            //envio del token via mensaje
            
            if (!is_null($token)) {
                if(ENVIRONMENT == 'development')
                {
                    $endPoint = "";
                } else{
                    $endPoint = URL_CAMPANIAS."api/ApiTwilio/send_sms_token_login";
                }
                $data = ['phone_number'=>$telefono, 'token'=>$token];
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
                    $this->session->set_userdata('envios_tocken', $envios+1);
                    $response = ["ok"=> TRUE, "message"=> "codigo generado y enviado"];
                } else {
                    $response = ["ok"=> FALSE, "message"=> "codigo generado  no enviado"];
                }
                if ($err)
                {
                echo 'cURL Error #:' . $err;die;
                }

               
            }else{
                $response = ["ok"=> FALSE, "message"=> "no se genero el token"];
            }
        } else {
            $response = ["ok"=> FALSE, "message"=> "Ha superado el limite de envios"];
        }
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $this->response($response, $status);
    }


   // -----
    private function _validate_inputs()
    {
        $this->form_validation->set_rules('user', 'Usuario', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        $this->form_validation->set_message('required', 'El campo %s es obligatorio');

        if ($this->form_validation->run()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function revisar_horario($id_operador){
        /*** Verificar los horarios de acceso según el tipo de operador ***/
        $horarios = $this->user_model->getHorarios($id_operador);
        if ($horarios) {
            $intDiaSemanaHoy = date('N');
            switch ($intDiaSemanaHoy) {
                case 1:
                    $strDiaSemanaHoy = 'lunes';
                    break;
                case 2:
                    $strDiaSemanaHoy = 'martes';
                    break;
                case 3:
                    $strDiaSemanaHoy = 'miercoles';
                    break;
                case 4:
                    $strDiaSemanaHoy = 'jueves';
                    break;
                case 5:
                    $strDiaSemanaHoy = 'viernes';
                    break;
                case 6:
                    $strDiaSemanaHoy = 'sabado';
                    break;
                case 7:
                    $strDiaSemanaHoy = 'domingo';
                    break;
            }
            $existeEnArrDias = false;
            foreach ($horarios as $horario) {
                $horarioDias = $horario['dias_trabajo'];
                $arrDias = explode(',', $horarioDias);
                $existeEnArrDias = in_array($strDiaSemanaHoy, $arrDias);
                if ($existeEnArrDias) {
                    $horaEntrada = strtotime(date('d-m-Y ' . $horario['hora_entrada']));
                    $horaSalida = strtotime(date('d-m-Y ' . $horario['hora_salida']));
                    break;
                }
            }
            if ($existeEnArrDias) {
                $horaHoy = strtotime(date("d-m-Y H:i", time()));
                if ($horaHoy >= $horaEntrada && $horaHoy <= $horaSalida) {

                    //$this->verificacion_token($user_logged);
                    //redirect('veriff_token');
                    return TRUE;
                } else {
                    //$this->session->set_flashdata('msg_error', 'Usuario fuera de horario');
                    //redirect('login');
                    return FALSE;
                }
            } else {
                //$this->session->set_flashdata('msg_error', 'Usuario fuera de horario');
                //redirect('veriff_token');
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

    function revisar_ausencia($id_operador){
        $hoy = date('Y-m-d');
        $ausencia = $this->operadores_model->get_ausencias_operador(['id_operador'=>$id_operador, 'estado'=> 1, 'entre_fecha'=>$hoy]);
        
        //0 ausente y 1 activo
        if (empty($ausencia)) {
            $ausencia = 1;
        }else{
            $ausencia = 0; 
            $this->session->set_userdata('ausencia', $ausencia);
        }
        return $ausencia;
    }

    /*****
     * return false si la clave esta vencida
     */
    function revisar_clave($modified_on){
        $today = new DateTime(date("Y-m-d"));
        $days = $today->diff($modified_on)->days;

        if($days >= 30) {
            //$this->session->set_userdata('cambioClave', true);
            //return $this->load->view('login', $data);
            return FALSE;
        } else {
            //$this->session->set_userdata('cambioClave', false);
            return TRUE;
        }
    }

    function generate_string($input, $strength = 16) {
        $input_length = strlen($input);
        $random_string = '';
        
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }
        $this->session->set_userdata('temp_tocken', $random_string);
        $this->session->set_userdata('temp_tocken_time', date("Y-m-d H:i:s"));
          
        return $random_string;
    }


}