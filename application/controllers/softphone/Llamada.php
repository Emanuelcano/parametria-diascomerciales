<?php
//defined('BASEPATH') or exit('No direct script access allowed');

class Llamada extends CI_Controller
{
    protected $CI;
    public function __construct()
    {
        parent::__construct();
        
        if ($this->session->userdata("is_logged_in")) 
        {
            $this->load->model('operadores/Operadores_model');
            $this->load->model('softphone/CargaMasiva_model');
            
            $this->load->model('User_model');
            $this->load->model('Usuarios_modulos_model');
            $this->load->model("tablero/Tablero_model", "tablero");
            $this->load->helper("encrypt");
            $this->load->library('Wolkvox_library');
       } 
        else
        {
            redirect(base_url()."login/logout");
        }
    }

    public function index()
    {
        $link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/',$link);
        $permisos = FALSE;

        foreach ($this->session->modulos as $key => $value):
            if($value->url == $link_array[1])
            {
                $permisos = TRUE;
                break;
            } 
        endforeach;

        if ($permisos) 
        {
            $title['title'] = "Operadores";
            //$this->load->view('layouts/header',$title);
            //$this->load->view('layouts/nav');
            //$this->load->view('layouts/sidebar');
            $this->load->view('layouts/adminLTE', $title);
            $this->load->view('operadores/operadores');
            //$this->load->view('layouts/footer');
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }


/*
|--------------------------------------------------------------------------
| Area de Componente llamadas Ing. Esthiven Garcia
|--------------------------------------------------------------------------
*/

/*
|-------------------------------------------------------------------------------------------------------------------
| Area de Reportes Ing. Esthiven Garcia
| esta seccion consume las acciones de integracion en componente de llamadas como llamar colgar mute etc.
| Api documentacion link: https://www.wolkvox.com/apis.php seccion API Agentes.
|-------------------------------------------------------------------------------------------------------------------
*/

    public function llamada_manual(){
        
        $tlf_cliente = $this->input->post('txt_num_man');
        $id_cliente = 0;
        
        $service_response = $this->wolkvox_library->llamada_manual($tlf_cliente,$id_cliente);
        
        ///RESPUESTA DEL SERVICIO ({"status":"ok","id_call":"12700.1048.1596126371.28824"})
        $response = explode(",", $service_response);
        
        //var_dump($response[0]);die;
        if($response[0]=='({"status":"ok"'){
            
            echo "Llamada conectada";
        } else {
            
            echo  "error al llamar.";
        }


        
        
    } 

    public function colgar_llamada()
    {
            $service_response = $this->wolkvox_library->colgar_llamada();
           ///RESPUESTA DEL SERVICIO ({"status":"ok"})

            
            if ($service_response=='({"status":"ok"})') {
                echo "llamada colgada";
            }else{
                echo "error al colgar";
            }



    }


    public function mutear_llamada()
    {
            $service_response = $this->wolkvox_library->mutear_llamada();
           ///RESPUESTA DEL SERVICIO ({"status":"ok"})

            
            if ($service_response=='({"status":"ok"})') {
                echo "Muteado";
            }else{
                echo "error al mutear";
            }
        
	}

    public function desmutear_llamada()
    {
            $service_response = $this->wolkvox_library->mutear_llamada();
           ///RESPUESTA DEL SERVICIO ({"status":"ok"})

            
            if ($service_response=='({"status":"ok"})') {
                echo "Muteado";
            }else{
                echo "error al mutear";
            }

    }

    public function codificar_only()
    {

        ////RESPUESTA DEL SERVICIO ({"status":"ok","id_call":"12700.3197.1596140265.54501"})
        $cod1 = $this->input->post('cod1');
        $cod2 = $this->input->post('cod2');
        $commets = $this->input->post('commets');
        
        $service_response = $this->wolkvox_library->codificar_only($cod1,$cod2,$commets);
        echo $service_response;
        ///RESPUESTA DEL SERVICIO ({"status":"ok","id_call":"12700.1048.1596126371.28824"})
        /*$response = explode(",", $service_response);
        
        //var_dump($response[0]);die;
        if($response[0]=='({"status":"ok"'){
            
            echo "codificado";
        } else {
            
            echo  "error codificar";
        }*/

        

    }

    



    public function codificar_and_ready()
    {
        ////RESPUESTA DEL SERVICIO ({"status":"ok","id_call":"12700.3197.1596140265.54501"})
        $cod1 = $this->input->post('cod1');
        //$cod2 = $this->input->post('cod2');
        $commets = $this->input->post('commets');
        
        $service_response = $this->wolkvox_library->codificar_and_ready($cod1,$commets);
        
        ///RESPUESTA DEL SERVICIO ({"status":"ok","id_call":"12700.1048.1596126371.28824"})
        $response = explode(",", $service_response);
        
            echo "true";
        

        /*if(!is_null($service_response) && $response[0]=='({"status":"ok"'){
            
            return true;
        } else {
            
            echo  "error codificar";
        }*/
        

    }

    public function llamada_en_espera()
    {
         ///RESPUESTA DEL SERVICIO ({"status":"ok"})

         $service_response = $this->wolkvox_library->llamada_en_espera();

            
            if ($service_response=='({"status":"ok"})') {
                echo "llamada en espera";
            }else{
                echo "error try to hold";
            }


    }

    public function reprogramar_llamada()
    {
        $cliente = $this->input->post('txt_num_man');
        $fecha_prog = $this->input->post('fecha_prog');
        var_dump($fecha_prog,$cliente);die;
        $str_datos  = file_get_contents("http://localhost:8084/apiagentbox?action=rcal&date=20200306111020&dial=".$cliente."&type_recall=auto");//manual/auto
        $datos      = json_decode($str_datos,true);
        $status     = $datos["status"];
        
        if ($status=="ok") {
            echo "llamada reprogramada";
        }else{
            echo "error intentar reprogramar llamada";
        } 

    }

    public function llamada_auxiliar()
    {
        
        ///RESPUESTA DEL SERVICIO ({"status":"ok","id_call":"12700.1048.1596126371.28824"})

        $tlf_cliente = $this->input->post('txt_num_man');
        
        $service_response = $this->wolkvox_library->llamada_auxiliar($tlf_cliente);
        
        echo $service_response;
        /*$response = explode(",", $service_response);
        
        
        if($response[0]=='({"status":"ok"'){
            
            echo "llamada auxiliar activa";
        } else {
            
            echo  "error llamada auxiliar";
        }*/


    }

    public function marcar_numero()
    {
            $num_press = $this->input->post('num_press');
            $service_response = $this->wolkvox_library->preciona_dtmf($num_press);
           ///RESPUESTA DEL SERVICIO ({"status":"ok"})
            $response = explode(":", $service_response);
            
            echo $response[0];
            /*if($response[1]=='"ok"})'){
            
                echo "numero discado";
            } else {
                
                echo  "numero no discado";
            }*/



    }


     public function transferir_llamada()
    {
            $num_trans = $this->input->post('num_trans');
            $service_response = $this->wolkvox_library->transferir_llamada($num_trans);
           ///RESPUESTA DEL SERVICIO ({"status":"ok"})
            $response = explode(":", $service_response);
            
            if($response[1]==='"ok"})'){
            
                echo "llamada transferida";
            } else {
                
                echo  "Error transferencia llamada";
            }



    }

/*
|--------------------------------------------------------------------------
| Area de Redireccion automatica de gestion Ing. Esthiven Garcia
|--------------------------------------------------------------------------
*/

/*
|-------------------------------------------------------------------------------------------------------------------
| Area de Redireccion Ing. Esthiven Garcia
| esta seccion escucha mediante el servicio en el manager del wolkvox seccion integracion web la peticion de la llamada ejecutada en el puerto 8084 del apache
| el mismo esta en constante escucha para las acciones integradas entre wolkvox y la base de wolkvox la integracion estructura segun el tipo de operador y
| apertura la gestion comercial o cobranza segun sea el caso y despliega el componente de la llama de manera automatica
| Api documentacion link: https://www.wolkvox.com/apis.php seccion Integracion web.
|-------------------------------------------------------------------------------------------------------------------
*/
// ApiUpCrmGestion?
// id_cliente={id_customer}&
// cola={id_queue}&
// id_agente={id_agent}&
// telefono={TELEFONO}&
// id_solicitud={ID_SOLICITUD}&
// id_credito={ID_CREDITO}&
// nombre_customer={NOMBRE_APELLIDO}&
// documento={DOCUMENTO}&
// monto_disponible={MONTO_DISPONIBLE}
public function levantaCRMGestion(){
            $tipo_opera = $this->session->userdata("tipo_operador");
            $flag = true;
            foreach ($this->input->get() as $key => $value) {
                if(empty($value)){
                    $flag = false;
                }
            }

     if ( $this->input->get('id_credito')!='{ID_CREDITO}') {

            if ($tipo_opera==1 && $flag) {
                $this->session->set_userdata('id_cliente',$this->input->get('id_cliente'));
                $this->session->set_userdata('cola', $this->input->get('cola'));
                $this->session->set_userdata('id_agente', $this->input->get('id_agente'));
                $this->session->set_userdata('telefono', $this->input->get('telefono'));
                if ($this->input->get('id_solicitud')=="{ID_SOLICITUD}") {
                $this->session->set_userdata('id_solicitud', $this->input->get('id_customer'));
                }else{
                $this->session->set_userdata('id_solicitud', $this->input->get('id_solicitud'));

                }
                
                $this->session->set_userdata('nombre_customer', $this->input->get('nombre_customer'));
                $this->session->set_userdata('render_view', "true");
                

                redirect(base_url() . "atencion_cliente/renderGestion"); 

            }else if ($tipo_opera==4 && $flag) {

                $this->session->set_userdata('id_cliente', $this->input->get('id_cliente'));
                $this->session->set_userdata('cola', $this->input->get('cola'));
                $this->session->set_userdata('id_agente', $this->input->get('id_agente'));
                if ( $this->input->get('cola')=="4057") {
                    /*VALIDA OPERADOR ACTIVO Y ASIGNA CASO*/
                     //$modulos = $this->Operadores_model->update_agent_solicitud($this->input->get('id_solicitud'), $this->input->get('id_agente'));
                    /*UPDATE ENDPOINT CASO*/
                    /*VALIDA CHAT ACTIVO PARA ASIGNACION A BOT*/
                     $modulos = $this->Operadores_model->get_chat_active($this->input->get('id_agente'),$this->input->get('telefono'));
                    /*UPDATE ENDPOINT*/
                }
                $this->session->set_userdata('telefono', $this->input->get('telefono'));
                $this->session->set_userdata('documento', $this->input->get('documento'));
                $this->session->set_userdata('monto_disponible', $this->input->get('monto_disponible'));
                if ($this->input->get('id_solicitud')=="{ID_SOLICITUD}") {
                $this->session->set_userdata('id_solicitud', $this->input->get('id_cliente'));
                }else{
                $this->session->set_userdata('id_solicitud', $this->input->get('id_solicitud'));

                }
                
                $this->session->set_userdata('nombre_customer', $this->input->get('nombre_customer'));
                $this->session->set_userdata('render_view', "true");
                

                redirect(base_url() . "atencion_cliente/renderGestion");     

            }else if ( ($tipo_opera==5 || $tipo_opera==6 || $tipo_opera==9) && $flag) {


                $this->session->set_userdata('id_cliente', $this->input->get('id_cliente'));
                $this->session->set_userdata('cola', $this->input->get('cola'));
                $this->session->set_userdata('id_agente', $this->input->get('id_agente'));
                $this->session->set_userdata('telefono', $this->input->get('telefono'));
                $this->session->set_userdata('id_credito', $this->input->get('id_credito'));
                $this->session->set_userdata('nombre_customer', $this->input->get('nombre_customer'));
                

                redirect(base_url() . "atencion_cobranzas/renderCobranzas");    
                
            
            
            
            }else{

                $params = array();
                $params['id_usuario'] = $this->session->userdata("id_usuario");
                $modulos = $this->User_model->get_usuario_modulos($params);
                $this->session->modulos = $modulos;
            
                $data = array(

        
                "modulos"               => $modulos,
                "id_solicitud"          => $this->input->get('id_cliente'),
                "cola"                  => $this->input->get('cola'),
                "id_agente"             => $this->input->get('id_agente'),
                "telefono"              => $this->input->get('telefono'),
                "id_credito"            => $this->input->get('id_credito'),
                "nombre_customer"       => $this->input->get('nombre_customer'),
                "render_view"           => "true",

                );



                $data['title']   = 'Dashboard';
                $data['heading'] = 'Dashboard';

                $this->load->view('layouts/adminLTE__header', $data);
                $this->load->view('dashboard', $data);
                $this->load->view('layouts/adminLTE__footer', $data);



            }

    }
            
            
        


    }

    public function searchCodTipificacion(){
        
        if ($this->input->is_ajax_request()) {
            $tipo_opera = $this->session->userdata("tipo_operador");
                        
            $cods_tipi = $this->CargaMasiva_model->getcodTipificacion($tipo_opera);
            echo json_encode($cods_tipi);

        } else {
            show_404();
        }
        
       
    }

    public function updateFlagNewFlow(){
        
        if ($this->input->is_ajax_request()) {
            
            /* UPDATE FLAG SOLICITUD*/
            $rs_update = $this->Operadores_model->update_flag_solicitud($this->input->post('idSolicitud'));            
            /* UPDATE FLAG SOLICITUD*/
            /* CHANGE STATE WOLKVOX LINEAR REMOVE REGISTER*/

            /* CHANGE STATE WOLKVOX LINEAR REMOVE REGISTER*/
            
            

        } else {
            show_404();
        }
        
       
    }




    
}