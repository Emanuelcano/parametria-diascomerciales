<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Restserver\Libraries\REST_Controller;

use function PHPSTORM_META\type;

class ApiTemplates extends REST_Controller
{
    protected $_solicitud;

    public function __construct()
    {
        parent::__construct();
        
        if ($this->session->userdata('is_logged_in')) {
            // Models
            $this->load->model('ComunicationTemplate_model', '', TRUE);
        } else {
            redirect(base_url('login'));
        }  

        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

    //Envío de mensaje SMS
    public function sendSms_post(){
      
        $request = $this->input->post();
       
        $data = []; 
        $data['tipo_envio'] = 'SMS';
        $data['servicio'] = $request['supplier'];
        $data['numero'] = $request['phoneN'] ;
        $data['text'] = $request['Template'];
        
        $response = $this->castCurlResponse($data, 'SMS');
        echo $response;
    }

    //casteo respuesta de los endpoint dado que devuelven un string simil json
    private function  castCurlResponse($data, $metodo) {

        $response_aux = "";
        $response = ($metodo == 'IVR' || $metodo ==  'SMS' ?  $this->curlSend($data) : $data);
        //$response = '{"error_info":{"error_message":"Template name is already used","error_code":154},"message":"Error in Template api call"}';
        $response_aux .="[". $response ."]";
        $response = json_decode($response_aux);

        
        return  ($metodo == 'IVR' || $metodo ==  'SMS' ? $response[0]->status->code : (!empty($response[0]->data) ?  $response[0]->data : $response[0]->error_info));
    }

   //Envío por IVR
    public function sendIvr_post(){
        $request = $this->input->post();

        $data = [];
        $data['tipo_envio'] = 'IVR';
        $data['servicio'] = $request['supplier'];
        $data['numero'] = $request['phoneN'] ;
        $data['text'] = $request['Template'] ;
        $response = $this->castCurlResponse($data, 'IVR');
        
        echo json_encode($response);  
    }

    //Ejecucion deCurl con data(SMS/IVR)
    public function curlSend($data){
        $curl = curl_init();
        if($data["servicio"] == "7" || $data["servicio"] == "10") {
            $data["tipo_envio"] = (($data["tipo_envio"] == 'IVR') ? 1 : 2 );
            unset($data["servicio"]);
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => URL_CAMPANIAS . '/api/ApiMessageBird/envioSMS_gral_messagebird',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('tipo_envio' => '1','numero' => '3022529113','text' => 'prueba alan'),
                CURLOPT_HTTPHEADER => array(
                  'Cookie: ci_session=3aqrmoddneg14jvde4elgp1c4ehhjdkk'
                ),
            ));
        } else {
  
            curl_setopt_array($curl, array(
            CURLOPT_URL => URL_CAMPANIAS. '/ApiEnvioGeneralTrack',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            ));
        }

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    //preparo los datos para insertarlos en pepipost API
    public function prepareData($data, $metodo) {
        $html = "";
        $arreglo_variables =  str_replace('"', '', str_replace('}', '', str_replace('{', '', $data['campanias_mail_templates']['arreglo_variables_rplc'])));
            if ($metodo == 'insert'){
                $html_contenido = $data['campanias_mail_templates']['html_contenido'];
            } else {
                $html .= "<html><head>".  $data['campanias_mail_templates']['html_contenido'] . "</body></html>";
                $html_contenido = $this->insert( $html, '</style>', '</head><body>');
            }
            print($html_contenido);
            die; // se debe continuar
            
    
            $html_contenido_aux= "";
            foreach (explode(",", $arreglo_variables) as $key => $value) {
                if($key == 0) {
                    $html_contenido_aux =  $html_contenido;
                } else {
                    $html_contenido_aux =  str_replace('$'.$value, '['. $value .']', $html_contenido_aux);
                }
            }
    
            return '{
                "templateName":  "' . str_replace(' ', '_', $data['campanias_mail_templates']['nombre_template']) . '",
                "content": "' . urlencode($html_contenido_aux) . '"
            }';
    }

    function insert ($string, $keyword, $body) {
        return substr_replace($string, PHP_EOL . $body, strpos($string, $keyword) + strlen($keyword), 0);
     }
     
    
    //inserto data en pepipost
    private function insertTemplatePepipost($data){

        $json = $this->prepareData($data, 'insert');
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => TEMPLATE_PEPIPOST,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                    "api_key: f9a5f6fe35da420771aab0c95fd9910a",
                    "content-type: application/json"
                ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $response_aux = $this->castCurlResponse($response, 'PEPIPOST');
        
        return $response_aux;
    }

    //Actualizo data en pepipost
    public function updateTemplatePepiPost($data, $id) {
        
        $json = $this->prepareData($data, 'update');
        print("json: <br>");
        print($json);
        print(" data: <br>");
        var_dump($data);
        print(" id: $id");
  
        

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => TEMPLATE_PEPIPOST . '/' . $id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => array(
                'api_key: f9a5f6fe35da420771aab0c95fd9910a',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $response;

    }
            

    //guardo data en DB
    public function saveTemplate_post(){
        
        $this->load->helper('cookie');
        $sessionCookie = get_cookie('__data_operator');
        $sessionCookie = explode(',', $sessionCookie);
        $operatorID    = (int)$sessionCookie[0]; 
    
        $request = $this->input->post();
    
        $id = $request['template_id'];
        $type = $request['template_type'];
        
        if($type != 'email'){
            //Datos WAPP, SMS, IVR
            $data['canal'] = $request['canal'] ? $request['canal'] : '';
            $data['msg_string'] = $request['msg_string'] ? $request['msg_string'] :  '';
            $data['tipo_template'] = $request['tipo_template'] ? $request['tipo_template'] : '';
            $data['grupo'] = $request['grupo'] ? $request['grupo'] : '';
            $data['proveedor'] = $request['proveedor'] ? $request['proveedor'] : '';
            $data['creation_date'] = date("Y-m-d H:i:s");   
            $data['id_operador_creacion'] =  $operatorID;
            $data['estado'] =  1;
        } else {
            //Datos Email
            $data['agenda_mail_logica']['query_contenido'] = $request['query_contenido'] ? $request['query_contenido'] : '';
            $data['agenda_mail_logica']['nombre_logica'] = $request['nombre_logica'] ? $request['nombre_logica'] : '';
            $data['agenda_mail_logica']['estado'] = 1;

            $data['campanias_mail_templates']['canal'] = $request['canal_email'] ? $request['canal_email'] : '';
            $data['campanias_mail_templates']['nombre_template'] = $request['nombre_template'] ? $request['nombre_template'] : '';
            $data['campanias_mail_templates']['arreglo_variables_rplc'] = $request['arreglo_variables_rplc'] ? $request['arreglo_variables_rplc'] : '';
            $data['campanias_mail_templates']['html_contenido'] = $request['html_contenido'] ? $request['html_contenido'] : '';
            $data['campanias_mail_templates']['creation_date'] = date("Y-m-d H:i:s");   
            $data['campanias_mail_templates']['operador_id'] =  $operatorID;
            $data['campanias_mail_templates']['estado'] = 1;
            
            $data['campanias_relacion_templates']['flag_unico'] = 1;
            $data['campanias_relacion_templates']['id_template'] = 1;
            $data['campanias_relacion_templates']['estado'] = 1;
        }

        if(!$id){
            if($type != 'email'){
                //inserto data en DB
                $new_template_id = $this->ComunicationTemplate_model->insert_template($data);
                $this->insertVariables($request, $new_template_id[0]->id);
            } else{
                //inserto data en pepipost
                $pepipost_id = $this->insertTemplatePepipost($data);
                if(empty($pepipost_id->error_message)){
                    $data['agenda_mail_logica']['mensaje'] =$pepipost_id->templateId;
                    $data['campanias_mail_templates']['id'] = $pepipost_id->templateId;
                    $data['campanias_relacion_templates']['id_template'] =$pepipost_id->templateId;
                  
                    //inserto data en DB
                    $response = $this->ComunicationTemplate_model->insert_email_template($data);
                    echo $response["status"];

                } else {
                    echo $pepipost_id->error_message;
                }
            }
        } else {
            if($type != 'email'){
                //Actualizo data en DB
                $this->ComunicationTemplate_model->update_template($data, $id);
                if($request['variable'] != ""){
                        $data_variable = [];
                        $data_variable["tipo"] = $request['tipo_variable'];
                        $data_variable["campo"] = $request['campo'];
                        $data_variable["condicion"] = $request['tipo_template'] == 2 ? "" : $request['condicion'];
                        $data_variable["formato"] = $request['tipo_template'] == 2 ? "" : $request['formato'];
                        $this->ComunicationTemplate_model->update_variable($data_variable, $request['variable'], $id);
                    }
                if($request["variables_id"] != "") {
                    //inserto variables en db
                    $this->insertVariables($request);
                }
            } else {
                //Actualizo data en pepipost
                
                $response = $this->updateTemplatePepiPost($data, $id);
                var_dump($response);
                
                
                //Actualizo data en DB
                print("en metodo saveTemplate: ");
                print(" id: $id <br> " );
                print(" data: <br> " );
                var_dump($data);
                die;
                
                $this->ComunicationTemplate_model->update_email_template($data, $id);
            }
        }

        return true;
    }

    //preparo la insercion de las nuevas variables
    public function insertVariables($request, $id_template = null ) {
        $variables_id = explode(",",$request["variables_id"]);
        $values_tipo_variable = explode(",",$request["values_tipo_variable"]);
        $values_campo = explode(",",$request["values_campo"]);
        $values_condicion = explode(",",$request["values_condicion"]);
        $values_formato = explode(",",$request["values_formato"]);
        $data = [];

        foreach ($variables_id as $key => $value) {
            $data["id_template"] =  $id_template ? $id_template : $request["template_id"];
            $data["variable"] = $value;
            $data["tipo"] = $values_tipo_variable[$key];
            $data["campo"] =  $values_campo[$key];
            $data["condicion"] = !empty($values_condicion) ? $values_condicion[$key] : "";
            $data["formato"] = !empty($values_formato) ? $values_formato[$key] : "";
    
         
            $this->ComunicationTemplate_model->insert_variable($data);
        }
    }
    
}