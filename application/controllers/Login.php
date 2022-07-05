<?php

use Pusher\PusherException;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
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

    public function index()
    {
        if($this->session->userdata('user') !== NULL && $this->session->userdata('is_logged_in')){
            $this->user_model->logout($this->session->userdata['id_usuario']);
            $this->session->sess_destroy();
            $this->session->set_userdata('envios_tocken', 0);
            $this->session->set_userdata('intentos_tocken', 0);

        }else{
            if(TOKEN_LOGIN){
                $token_fecha = explode(' ',$this->session->userdata('temp_tocken_time'))[0];
                $dias = round((strtotime(date("Y-m-d")) - strtotime($token_fecha)) / (60 * 60 * 24));
                
                //el ultimo token generado tiene mas de 1dia?
                if(!is_null($token_fecha) && $dias > 0){
                    $this->session->set_userdata('envios_tocken', 0);
                    $this->session->set_userdata('intentos_tocken', 0);
                }
            }
            
        }
        
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] == PATH_LOGIN_AUTH){
            return $this->load->view('login');
        } else {
            if(ENVIRONMENT == 'development'){
                return $this->load->view('login');
            }else{
                redirect(PATH_LOGIN_AUTH);
            }
        }

    }

    /**
     * @throws PusherException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function login_old()
    {
        /**
         * el nuevo login esta en apiLogin
         */
        $data['title'] = 'Login';
        $user          = $this->input->post('user');
        $password      = encrypt($this->input->post('password'));
        $restriccion_horario = ["1", "4", "5", "6"];
        
        if ($this->_validate_inputs())
        {
            $user_logged = $this->user_model->get_user_login($user, $password);
            if (!empty($user_logged))
            {
                    $user_data = $this->operator_model->search(['id_usuario' => $user_logged[0]->id]);
                    $this->session->set_userdata('id_usuario', $user_logged[0]->id);
                    $this->session->set_userdata('user', $user_logged[0]);
                    $this->session->set_userdata('user_id', $user_logged[0]->id);
                    
                    $this->session->set_userdata('telefono', $user_logged[0]->whatsapp);


                    
                    //0 ausente y 1 activo
                    $ausencia = $this->revisar_ausencia($user_data[0]['idoperador']);
                    $horario = $this->revisar_horario($user_data[0]['idoperador']);
                    
                    if(in_array($user_data[0]['tipo_operador'], $restriccion_horario) && $ausencia == 0){
                        $this->session->sess_destroy();
                        var_dump(json_encode(["ok"=> FALSE, "message" => "Operador fura de horario"]));
                        die;
                    }

                    if(in_array($user_data[0]['tipo_operador'], $restriccion_horario) && !$horario ){
                        $this->session->sess_destroy();
                        var_dump(json_encode(["ok"=> FALSE, "message" => "Operador fura de horario"]));
                        die;
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

                        /*** Verificar que la clave de acceso para los operadores  tenga los 30 días de vigencia ***/
                        $tipoOperador = $user_data[0]['tipo_operador'];
                        $modified_on = new DateTime($user_logged[0]->modified_on);
                        
                        /** retorna FALSE si la clave esta vencida */
                        $clave = $this->revisar_clave($modified_on);
                        if(!$clave){
                            $this->session->sess_destroy();
                            var_dump(json_encode(["ok"=> FALSE, "message" => "Operador fura de horario", "cave" => $clave]));
                            die;
                        }
                    }

                //$this->verificacion_token($user_logged);
                redirect('veriff_token');
            } else {
                //$this->session->set_flashdata('msg_error', 'Usuario y/o clave incorrecta.');
                //$this->session->set_userdata('cambioClave', false);
                //redirect('login');
                var_dump(json_encode(["ok"=> FALSE, "message" => "Usuario y/o clave incorrecta"]));
                die;
            }
        } else {
            //$this->session->set_userdata('cambioClave', false);
            //$this->load->view('login', $data);
            var_dump(json_encode(["ok"=> FALSE, "message" => "Debe completar ambos campos"]));
            die;
        }    
    }

    /**
     * @throws PusherException
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function logout()
    {
        if ($this->session->userdata('user') !== NULL) {

            /*** Se deja de Trackear en caso de que se haya activado por un Auditor ***/
            $this->tracker_model->set_off_all_operation($this->session->userdata['idoperador']);

            $this->user_model->logout($this->session->userdata['id_usuario']);
            $this->session->sess_destroy();
            //Dispatcher logic
            $this->load->helper('cookie');
            delete_cookie('__data_operator');
            //TODO Log in error
        }
        $this->session->set_userdata('leyendo_caso', 0);
        redirect('login');
    }

/***************************************************************************/
/***************************************************************************/
// VALIDATIONS
/***************************************************************************/
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
            return FALSE;
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
            return FALSE;
        }
    }


/*
|--------------------------------------------------------------------------
| Method Verificacion de permisos Ing. Esthiven Garcia 02/10/2020
|--------------------------------------------------------------------------
|
| Con este methodo se pretende validar si tiene permisos especificos para acciones en el sistema en el crm este metodo recibe parametros generales para validar  | cualquier modulo y acción.
| 
|
*/
/*public function verifyAccxUserxAction(){

$cedula=$this->session->userdata('id_usuario');
$sistema = $this->input->post('id_sistema');
$sub_mnu_sistema = $this->input->post('id_mnu_sis');
$sub_sistema = $this->input->post('id_sub_sistema');
$privilegio = $this->input->post('id_privilegio');
            
$user_logged = $this->user_model->get_access_for_user($user, $password);
if($this->Seg_permisologias_model->mostrarPermisoDeUsuarioPorAccion($sistema,$sub_sistema,
$sub_mnu_sistema,$privilegio,$cedula)==true)
echo 'Permisos Concedidos';

else
echo 'No posee permisos para entrar en este sistema contacte a dpto de sistemas';

}*/

/*
|--------------------------------------------------------------------------
| Fin Method Verificacion de permisos Ing. Esthiven Garcia 02/10/2020
|--------------------------------------------------------------------------
*/


}
