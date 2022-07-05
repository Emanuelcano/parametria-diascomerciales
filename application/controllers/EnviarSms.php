<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';

use Restserver\Libraries\REST_Controller;


class EnviarSms extends REST_Controller
{

    public function __construct($config = 'rest')
    {
        // header("Access-Control-Allow-Origin: *");
        // header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        // header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        parent::__construct();
        $this->load->model('Modulos_m', '', TRUE);
        $this->load->model('InfoBipModel', '', TRUE);
        $this->load->helper(array('form', 'url','my_date','formato'));
        $this->load->config('form_validation');
        $this->load->library('form_validation');        
        $this->load->library('Infobip_library');
        $this->load->library('Pepipost_library');
    }



    public function Enviar_post()
    {

        $request = $this->post();
        $this->form_validation->set_rules('to', 'Telefono', 'required|numeric');
        $this->form_validation->set_rules('text', 'text', 'required|max_length[160]');

        if ($this->form_validation->run() === TRUE) {
            $to = $this->post('to');
            $text = $this->post('text');
            $encript_code = $this->post('code');
            $code = $this->decrypt($encript_code);
            $text = sanear_string($text);
            $username = 'Solventa';
            $password = 'Havanna12$%98';
            $header = array("Content-Type: application/json", "Accept: application/json");
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/2/text/single",
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
                CURLOPT_USERPWD => "$username:$password",
                CURLOPT_POSTFIELDS => '{  "from":"Webapp",  "to":"' . $to . '",  "text":"' . $text . ' ' . $code . '"}'
            ));


            $data = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $data = $err;
                $status = parent::HTTP_BAD_REQUEST;
            } else {


                $status = parent::HTTP_OK;
            }


            $response = ['status' => $status, 'data' => $data];
            $this->response($response, $status);
        } else {


            $errors =  (preg_replace('/\s\s+/', ' ', validation_errors()));
            $response = ['message' => $errors, 'status' => parent::HTTP_NOT_FOUND];
            $this->response($response, parent::HTTP_UNAUTHORIZED);
            exit();
        }
    }

    public function EnviarSms_post()
    {
        $id  = $_REQUEST['id_usuario'];
        //$id = "10325";
        $datos = $this->InfoBipModel->solicitantesDesembolso($id);
        if (!empty($datos)) {
            foreach ($datos as $key => $a) {
                if (ENVIRONMENT == 'development') {
                    $telefono = TEST_PHONE_NUMBER;
                } else {
                    $telefono = $a->telefono;
                    $telefono = "+57" . $telefono;
                }
                $nombres = $a->nombres;
                $nombres = sanear_string($nombres);
                $trozos = explode(" ", $nombres);
                $nombres = ucwords(strtolower($trozos[0]));
                $prestamo = $a->monto;
                if (strlen($prestamo) > 6) {
                    $pri = substr($prestamo, 0, strlen($prestamo) - 6);
                    $seg = substr($prestamo, strlen($prestamo) - 6, 6);
                    $prestamo = $pri . "." . $seg;
                }
                $pri = substr($prestamo, 0, strlen($prestamo) - 3);
                $seg = substr($prestamo, strlen($prestamo) - 3, 3);
                $prestamo = $pri . "." . $seg;
                if ($a->estado == "VALIDADO") {
                    $text = '¡Hola ' . $nombres . '! Para desembolsar tus $ ' . $prestamo . ' tienes que firmar el pagaré que enviamos a tu correo. Puedes enviarnos un WhatsApp aquí bit.ly/wasolv. Solventa';
                } elseif ($a->estado == "VERIFICADO") {
                    $text = '¡Hola ' . $nombres . '! Estamos llamandote para desembolsar tu Prestamo de $' . $prestamo . ', puedes enviarnos un WhatsApp entrando aqui bit.ly/wasolv. Solventa';
                }
                $text = sanear_string($text);
                $data = array(
                    'from' => 'Solventa',
                    'to' => $telefono,
                    'text' => $text
                );
            }
            $a = json_encode(['messages' => $data], true);
            $username = 'Solventa';
            $password = 'Havanna12$%98';
            $header = array("Content-Type: application/json", "Accept: application/json");
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/2/text/single",
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
                CURLOPT_USERPWD => "$username:$password",
                CURLOPT_POSTFIELDS => $a
            ));


            $data = curl_exec($curl);

            $status = parent::HTTP_OK;



            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $data = $err;
                $status = parent::HTTP_BAD_REQUEST;
                $response['status']['code'] = $status;
                $response['solicitud'] = "ERROR EN MENSAJE";
                $response['status']['ok'] = false;
            } else {

                $response['status']['ok'] = TRUE;
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['solicitud'] = "MENSAJE ENVIADO";
            }
            $response = ['status' => $status, 'data' => json_decode($data)];
        } else {

            $status = parent::HTTP_BAD_REQUEST;
            $response['status']['code'] = $status;
            $response['solicitud'] = "LA PERSONA NO CUMPLE LAS CONDICIONES PARA ENVIAR EL MENSAJE";
            $response['status']['ok'] = false;
        }

        $this->response($response, $status);
    }


   public function short_post(){

        $this->load->library('Shortener_library');
        $url = URL_PRESTAMOS_SOLVENTA.'?gclid=EAIaIQobChMIqOGiqMz25gIVCASRCh2e0wbWEAAYAiAAEgJU6PD_BwE';     
        $shortUrl = $this->shortener_library->solicitarUrl($url);
        return $shortUrl;
    }

    public function EnviarSmsVerificar_post()
    {
        //$id = 40068;
        $id  = $_REQUEST['id_solicitud'];
        $datos = $this->InfoBipModel->verificacionSms($id);    
        if (!empty($datos)) {                           
            foreach ($datos as $key => $a) {              
                if (ENVIRONMENT == 'development') {
                    $telefono = TEST_PHONE_NUMBER;
                } else {
                    $telefono = $a->celular;
                    $telefono = "57" . $telefono;
                }
                $nombres = $a->nombres;
                $nombres = sanear_string($nombres);
                $trozos = explode(" ", $nombres);
                $dni = $a->documento;
                $nombres = ucwords(strtolower($trozos[0]));
                $apellidos = $a->apellidos;
                $apellidos = sanear_string($apellidos);

                $trozos = explode(" ", $apellidos);
                $apellidos = ucwords(strtolower($trozos[0]));
                $prestamo = $a->monto;
                if (strlen($prestamo) > 6) {
                    $pri = substr($prestamo, 0, strlen($prestamo) - 6);
                    $seg = substr($prestamo, strlen($prestamo) - 6, 6);
                    $prestamo = $pri . "." . $seg;
                }
                $pri = substr($prestamo, 0, strlen($prestamo) - 3);
                $seg = substr($prestamo, strlen($prestamo) - 3, 3);
                $prestamo = $pri . "." . $seg;
                $text = $nombres . ' ' . $apellidos . ', recibimos tu solicitud de Prestamo por $ ' . $prestamo . ' en este momento. Si tu no iniciaste la solicitud escribenos aqui solven.me/anom?a='.$id.'. Solventa';
                $text = sanear_string($text);
                $data[] = array(
                    'from' => 'Solventa',
                    'to' => $telefono,
                    'text' => $text
                );
                $this->InfoBipModel->insertarSmsParaEnviar($telefono,$dni,'verificacion',$text,0);   
          }
            $a = json_encode(['messages' => $data], true);
            $username = 'Solventa';
            $password = 'Havanna12$%98';
            $header = array("Content-Type: application/json", "Accept: application/json");
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/1/text/multi",
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
                CURLOPT_USERPWD => "$username:$password",
                CURLOPT_POSTFIELDS => $a
            ));


            $data2 = curl_exec($curl);

            $status = parent::HTTP_OK;

            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $data = $err;
                $status = parent::HTTP_BAD_REQUEST;
                $response['status']['code'] = $status;
                $response['error'] = "ERROR EN MENSAJE";
                $response['status']['ok'] = false;
            } else {
                $datosCod = json_decode($data2, true);
                $this->InfoBipModel->insertarRespuestasVerificacion($datosCod,$data,"Solicitantes");
                $response['status']['ok'] = TRUE;
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['menssage'] = "MENSAJE ENVIADO";
            }
            $response = ['status' => $status, 'data' => json_decode($data2)];
        } else {

            $status = parent::HTTP_BAD_REQUEST;
            $response['status']['code'] = $status;
            $response['error'] = "LA PERSONA NO CUMPLE LAS CONDICIONES PARA ENVIAR EL MENSAJE";
            $response['status']['ok'] = false;
        }

        $this->response($response, $status);
    }

    

    private function decrypt($code)
    {
        $this->load->library('encryption');
        $this->encryption->initialize(
            array(
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => 's0lvent@*'
            )
        );
        return $this->encryption->decrypt($code);
    }


public function short(){

        $this->load->library('Shortener_library');
        $url = URL_PRESTAMOS_SOLVENTA.'?gclid=EAIaIQobChMIqOGiqMz25gIVCASRCh2e0wbWEAAYAiAAEgJU6PD_BwE';     
        $shortUrl = $this->shortener_library->solicitarUrl($url);
        return $shortUrl;
    }

public function pruebasms_post()
    {
         
                    //$telefono = TEST_PHONE_NUMBER;
              $telefono = "+541149743446";
              $text = 'Aprovecha tu hora de almuerzo para pagar en cualquier sucursal EFECTY, con el N° de convenio 010671. Whatsappeanos a solven.me/w14m. Solventa Prestamos.';
               $text = sanear_string($text);
                $data = array(
                    'from' => 'Solventa',
                    'to' => $telefono,
                    'text' => $text
                );
                $dni = "35962725";
            if(isset($data)){
            $a = json_encode($data, true);
            $username = 'Solventa';
            $password = 'Havanna12$%98';
            $header = array("Content-Type: application/json", "Accept: application/json");
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://mrgz4.api.infobip.com/sms/2/text/single",
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
                CURLOPT_USERPWD => "$username:$password",
                CURLOPT_POSTFIELDS => $a
            ));


            $data = curl_exec($curl);

            $status = parent::HTTP_OK;



            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $data = $err;
                $status = parent::HTTP_BAD_REQUEST;
                $response['status']['code'] = $status;
                $response['solicitud'] = "ERROR EN MENSAJE";
                $response['status']['ok'] = false;
            } else {
                $datosCod = json_decode($data, true);    
                $response['status']['ok'] = TRUE;
                $this->InfoBipModel->insertarRespuesta($datosCod,$dni);
                $status = parent::HTTP_OK;
                $response['status']['code'] = $status;
                $response['solicitud'] = "MENSAJE ENVIADO";
            }
            $response = ['status' => $status, 'data' => json_decode($data)];
        } else {

            $status = parent::HTTP_BAD_REQUEST;
            $response['status']['code'] = $status;
            $response['solicitud'] = "LA PERSONA NO CUMPLE LAS CONDICIONES PARA ENVIAR EL MENSAJE";
            $response['status']['ok'] = false;
        }

        $this->response($response, $status);
    }

public function EnviarCelulares_post(){
    $datos = $this->InfoBipModel->celulares();

      if(isset($datos)){
   
        foreach ($datos as $key => $a) { 
        
          $text = 'Hola! No logramos comunicarnos contigo. Aun tu prestamo tiene un retraso en el pago. Es necesario que nos envíes un WhatsApp aqui solven.me/wacom. Solventa.';
          if (ENVIRONMENT == 'development') {
                $telefono = TEST_PHONE_NUMBER;
            }
            else {
                $telefono = $a->numero;
                $telefono = "+57" . $telefono;
            }         
            $text = sanear_string($text);
            $data[] = array(
                'from' => 'Solventa',
                'to' => $telefono,
                'text' => $text
            );
    
           $this->InfoBipModel->actualizarEstadoCelulares($a->numero); 
       }
      if(isset($data)){ 
        $response = $this->infobip_library->infobip_curl_bulk($data);
      } 
      else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
      } 
    }else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
         $response = array($response);
    }  
    $this->response($response);
}

public function SmsCobranzas_post(){
   
   $idMensaje = $_REQUEST['idMensaje'];
   $telefono = $_REQUEST['telefono'];
   $idCliente = $_REQUEST['idCliente'];
   //$idMensaje = 1;
   //$telefono = 541199237794;
   //$idCliente = 3229;//33 
   $tipo = 'cobranzas';    
   $mensaje = $this->InfoBipModel->mensajeCobranzas($idMensaje);
   $persona = $this->InfoBipModel->fusionDeuda($idCliente);
   /*if($idMensaje == 1){
            $persona = $this->InfoBipModel->deudaCliente($idCliente); 
   }else{
            $persona = $this->InfoBipModel->datosCobranzas($idCliente);
   }*/
   if(!empty($persona)){
    if(($idMensaje == 1 && !is_null($persona[0]['deuda'])) || !is_null($persona[0]['monto'])){
        $mensajeReemplazado = $this->InfoBipModel->cambiarMensaje($mensaje,$persona,$idMensaje);
       if (ENVIRONMENT == 'development') {
                    $telefono = TEST_PHONE_NUMBER;
       }else {
                $telefono = "+57" . $telefono;
       } 
               $data[] = array(
                    'from' => 'Solventa',
                    'to' => $telefono,
                    'text' => $mensajeReemplazado
              );
        $dni = $persona[0]['documento'];
        $this->InfoBipModel->insertarSmsParaEnviar($telefono,$dni,$tipo,$mensajeReemplazado,0);
        $response = $this->infobip_library->infobip_curl_cobranzas($data,$mensajeReemplazado);
    }else{
        $response = ['status' => 200, 'data' => 'No se puede enviar mensaje']; 
    }              
   }else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];        
               
   } 
   $this->response($response);   
}

public function acuerdoPago_post(){
    //$idCliente = 326;
    $idCliente = $_REQUEST['idCliente'];
    $persona = $this->InfoBipModel->clientesAcuerdoPago($idCliente);
    $mail = "";
    $telefono = "";    
    $mails = array();
    $listadotelefonos = array();
    $tipo = 'acuerdoPago';
    if(isset($persona)){
        foreach ($persona as $key => $a) {
           $fechaAcuerdo = date("d/m/Y", strtotime($a->fecha)); 
           $cortado = substr($fechaAcuerdo, 0, 5);
           $dia = saber_dia($a->fecha);
           $nombres = arreglar_string($a->nombres);
           $medioPago = ucwords(strtolower($a->medio));
           $prestamo = arreglar_prestamo($a->monto);
           $prestamoTotal = arreglar_prestamo($a->totalMonto);
           $mail = $a->email;
           $cuotas = $a->cantidad;
           $telefono = $a->telefono;
           $dni = $a->documento;
           if(!in_array($mail,$mails)){
             $personaMail[] = $this->guardarDatosMailMora($a,'AcuerdoPago',21272);
             $mails[] = $mail; 
           }
           if(!in_array($telefono,$listadotelefonos)){
            if($a->tipo == 'simple'){
            $text = 'Hola '.$nombres.', tu acuerdo de pago es de $ '.$prestamo.'. Paga por '.$medioPago.' antes del '.$dia.' '.$cortado.'. Si necesitas ayuda escribenos aqui solven.me/waamo. Solventa';
            }else{
               $text =  'Hola, tu acuerdo de pago es de $ '.$prestamoTotal.' en '.$cuotas.' cuotas. Paga por '.$medioPago.' antes del '.$dia.' '.$cortado.'. Si necesitas ayuda escribenos aqui solven.me/waamp. Solventa';
            }
            $this->InfoBipModel->insertarSmsParaEnviar($telefono,$dni,$tipo,$text,0);   
            $response2[] = $this->infobip_library->infobip_curl_Pago($a,$text);
            $listadotelefonos[] = $telefono;
           }
        }
        $response1 = $this->pepipost_library->curl_pepipost($personaMail,21272,"Acuerdo De Pago");
        $response = array($response1,$response2);           
    }else{
            $response = ['status' => 200, 'data' => 'No hay Mails ni mensajes para enviar'];
    }
   $this->response($response);
}


public function guardarDatosMailMora($a,$tipo,$template){ 
           $fechaAcuerdo = date("d/m/Y", strtotime($a->fecha));
           $dia = saber_dia($a->fecha);
           $nombres = $a->nombres;
           $nombres = sanear_string($nombres);
           $trozos = explode(" ", $nombres);
           $nombres = ucwords(strtolower($trozos[0]));
           $medioPago = ucwords(strtolower($a->medio));
           $prestamo = arreglar_prestamo($a->monto);    
        if(ENVIRONMENT == 'development') {
                $email = TEST_EMAIL;
            }
        else{
                $email = $a->email;
        }
        $per = array(
                'recipient' => $email,
                "attributes" => array(
                    "FNAME" => $nombres,
                    "MDPAGO" => $medioPago,
                    "DAPAGO" => $fechaAcuerdo,
                    "MAPAGAR" => $prestamo,                            
                ),
                'x-apiheader' => $tipo.','.$template
            );  
        return $per;
}

public function MailCobranzas_post(){
   //$idMail = 21447;
   //$mails = 'desarrollo01@solventa.com.ar';
   //$idCliente = 33;//33 
   $tipo = 'cobranzas'; 
   $idMail = $_REQUEST['idMail'];
   $mails = $_REQUEST['mail'];
   $idCliente = $_REQUEST['idCliente'];
   $persona = $this->InfoBipModel->fusionDeuda($idCliente);
   if(($idMail == 21272 && !is_null($persona[0]['deuda'])) || ($idMail != 21272 && !is_null($persona[0]['monto']))){    
       $deuda = $persona[0]['deuda'];
       $nombres = $persona[0]['nombres'];
       $fechaAcuerdo = $persona[0]['fecha'];
       $montoAcuerdo = $persona[0]['monto'];
       $medioAcuerdo = strtoupper($persona[0]['medio']);
       $dni = $persona[0]['documento'];                       
       $fechaEnvio = date("Y-m-d");             
       $dia = saber_dia($fechaEnvio);
       $fechaEnvio = date("d/m/Y", strtotime($fechaEnvio));       
       $cortado = substr($fechaEnvio, 0, 5);   
       $fechaAcuerdo = date("d/m/Y", strtotime($fechaAcuerdo));
       if (ENVIRONMENT == 'development') {
          $email = TEST_EMAIL;
       }
       else {
          $email = $a->email;
       }
       $personas[] = array(
                    'recipient' => $email,
                    "attributes" => array(
                        "FNAME" =>  arreglar_string($nombres),
                        //"LNAME" => $apellidos,
                        //"NPREST" => $a->id_credito,                     
                        "MAPAGAR" => arreglar_prestamo($montoAcuerdo),
                        "CAMPANA" => $tipo,
                        "DAPAGO" => $fechaAcuerdo,
                        "MDPAGO" => $medioAcuerdo,
                        "DEUDA" => arreglar_prestamo($deuda),
                        //"FVENCOR" => $vencimientoCortado,
                        "FCOR" => $cortado,
                        "DIAACTUAL" => $fechaEnvio,
                        "DIA" => $dia,                     
                    ),
                    'x-apiheader' => $tipo.','.$idMail
                );//var_dump($personas);die();       
       $this->InfoBipModel->insertarMailParaEnviar($email,$dni,$idMail,$tipo,0); 
       $response = $this->pepipost_library->curl_pepipost($personas,$idMail,"Cobranzas");
   }else{
        $response = ['status' => 200, 'data' => 'La persona no cumple las condiciones para enviar el mail'];
   }
   $this->response($response); 
}
/*
 public function mailsParaEnviar($idCliente,$mails,$idMail,$tipo){
        $personas = $this->InfoBipModel->fusionDeuda($idCliente);    
            foreach ($personas as $persona) {
                $cuota = $persona['deuda'];
                $nombres = $persona['nombres'];
                $fechaAcuerdo = $persona['fecha'];
                $montoAcuerdo = $persona['monto'];
                $medioAcuerdo = $persona['medio'];
                $dni = $persona['documento'];
            }            
            $fechaEnvio = date("Y-m-d");
            $fechaEnvio = date("d/m", strtotime($fechaEnvio));
            $fechaAcuerdo = date("d/m", strtotime($fechaAcuerdo));
            echo $fechaEnvio;die();              
            if (ENVIRONMENT == 'development') {
                $email = TEST_EMAIL;
            }
            else {
                $email = $a->email;
            }
            $per[] = array(
                'recipient' => $email,
                "attributes" => array(
                    "FNAME" => $nombres,
                    //"LNAME" => $apellidos,
                    //"NPREST" => $a->id_credito,                     
                    "MAPAGAR" => $montoAcuerdo,
                    "CAMPANA" => $tipo,
                    "DAPAGO" => $fechaAcuerdo,
                    "MDPAGO" => $medioAcuerdo,
                    //"FVENCOR" => $vencimientoCortado,
                    "DIAACTUAL" => $fechaEnvio,
                    //"DIA" => $dia,                     
                ),
                'x-apiheader' => $tipo.','.$idMail
            );       
        $this->InfoBipModel->insertarMailParaEnviar($email,$dni,$idMail,$tipo,0);  
        return $per;
    }*/

public function guardarDatosMora($a,$tipo,$text){
            if (ENVIRONMENT == 'development') {
                $telefono = TEST_PHONE_NUMBER;
            }
            else {
                $telefono = $a->telefono;
                $telefono = "+57" . $telefono;
            }     
            $text = sanear_string($text);
            $data = array(
                'from' => 'Solventa',
                'to' => $telefono,
                'text' => $text
            );         
            return $data;
}

public function SmsMoraDiaria_get(){    
       $tipo = "mora1dia"; 
       $datosVerificacion = $this->InfoBipModel->clientesMora1dia();
       if(isset($datosVerificacion)){ 
            foreach ($datosVerificacion as $key => $a) {
                $text = 'aun puedes pagar tu cuota hoy y obtener $ en tu proximo prestamo. Si necesitas ayuda escribenos aqui solven.me/wamoc. Solventa';
                $data[] = $this->guardarDatosMora($a,$tipo,$text);
            }
            if(isset($data)){ 
                $response = $this->infobip_library->infobip_curl($data,"Cliente",$tipo);
            } 
            else{
                $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
            }
       }else{
            $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
       }
       $this->response($response);   
}

public function nuevo360_get(){

    $post["to"] = array("573022529113","573022529113");
    $post["message"] = "Pruebas de mensajes Solventa {NAME}, monto de {PRESTAMO}";
    $post["from"] = "Solventa";
    $post["sub"] = array(array( 'NAME' => 'Carolina', 'PRESTAMO' => '1.000.000'),array('NAME' => 'Florencia', 'PRESTAMO' => '50.000'));   
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,DASHBOARD_360."api/rest/sms");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/json",
        "Authorization: Basic " . base64_encode('Solventa' . ":" . 'Solvent4')));

        $data = curl_exec($ch);
        $err = curl_error($ch);
       
        curl_close($ch);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {
            $datosCod = json_decode($data, true);
            //$this->CI->InfoBipModel->insertarRespuestaConTexto($datosCod,$dni,$text);
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
        }        
    
    } catch (Exception $exc) {
        $response = $exc->getTraceAsString();
    }
    $this->response($response);
}

public function actualizacionTelefonos_get(){
     $this->InfoBipModel->telefonosParaActualizar();
}


public function EventoAnalitycs_post(){ //GOOGLE ANALYTICS

    $desembolsos = $this->InfoBipModel->credito_otorgado();
        if(isset($desembolsos)){
            foreach ($desembolsos as $key => $a) {
                if ($a->clientid == false) {
                    $a->clientid = '1561616662.1574979832';
                }
                if ($a->tipo_solicitud === 'RETANQUEO') {

                        $data = array(
                        'v' => 1,
                        'tid' => 'UA-126952508-3',
                        'cid' => $a->clientid,
                        't' => 'event'
                        );
                        $data['ec'] = "Retanqueo";
                        $data['ea'] = "Dinero%20Desembolsado";
                        $data['el'] = $a->tipo_solicitud;
                        $data['ev'] = (int)($a->total_devolver - $a->capital_solicitado);
                        $data['uip']= $_SERVER['REMOTE_ADDR'];
                        $data['ua']=  $_SERVER['HTTP_USER_AGENT'];
                        $data['ni'] = 1;
                }else{
                    //  GCLID DEFINIDO
                    if ($a->gclid !== 'undefined' && !is_null($a->gclid)) {
                        $data = array(
                        'v' => 1,
                        'tid' => 'UA-126952508-3',
                        'cid' => $a->clientid,
                        't' => 'event'
                        );
                        $data['ec'] = "Desembolso";
                        $data['ea'] = "Dinero%20Desembolsado";
                        $data['ev'] = (int)($a->total_devolver - $a->capital_solicitado);
                        $data['gclid'] = $a->gclid;
                        $data['uip']= $_SERVER['REMOTE_ADDR'];
                        $data['ua']=  $_SERVER['HTTP_USER_AGENT'];
                        $data['ni'] = 1;
                    }
                    elseif (($a->gclid === 'undefined' || $a->gclid === null) && ( $a->utm_medium !== 'undefined' && !is_null($a->utm_medium)) && ( $a->utm_source !== 'undefined' && !is_null($a->utm_source)) && ( $a->utm_campaign !== 'undefined' || !is_null($a->utm_campaign))) {

                        $data = array(
                        'v' => 1,
                        'tid' => 'UA-126952508-3',
                        'cid' => $a->clientid,
                        't' => 'event'
                        );
                        $data['ec'] = "Desembolso";
                        $data['ea'] = "Dinero%20Desembolsado";
                        $data['ev'] = (int)($a->total_devolver - $a->capital_solicitado);
                        $data['cn'] = $a->utm_campaign;
                        $data['cs'] = $a->utm_source;
                        $data['cm'] = $a->utm_medium;
                        $data['uip']= $_SERVER['REMOTE_ADDR'];
                        $data['ua']=  $_SERVER['HTTP_USER_AGENT'];
                        $data['ni'] = 1;
                    }
                    elseif (($a->gclid === 'undefined' || $a->gclid === null) && ( $a->utm_medium === 'undefined' || $a->utm_medium === null) && ( $a->utm_source === 'undefined' || $a->utm_source === null) && ( $a->utm_campaign === 'undefined' || $a->utm_campaign === null)) {

                        $data = array(
                            'v' => 1,
                            'tid' => 'UA-126952508-3',
                            'cid' => $a->clientid,
                            't' => 'event'
                        );
                        $data['ec'] = "Desembolso";
                        $data['ea'] = "Dinero%20Desembolsado";
                        $data['ev'] = (int)($a->total_devolver - $a->capital_solicitado);
                        $data['uip']= $_SERVER['REMOTE_ADDR'];
                        $data['ua']=  $_SERVER['HTTP_USER_AGENT'];
                        $data['ni'] = 1;
                    }
                }
            $url = 'https://www.google-analytics.com/collect';
            $content = http_build_query($data);
            $content = utf8_encode($content);
            //$header = array("Content-Type: application/json", "Accept: application/json");
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => array('Content-type: application/x-www-form-urlencoded'),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
                CURLOPT_POSTFIELDS => $content
            ));
             
            $data = curl_exec($ch);
            $err = curl_error($ch);

            curl_close($ch);

            if ($err) {
                $data = $err;
                $status = parent::HTTP_BAD_REQUEST;
            } else {
                $status = parent::HTTP_OK;
            }
            $response[] = ['status' => $status, 'data' => $data];
          }    
            
            
        }else{
            $response = ['status' => 200, 'data' => 'NO HAY HITS PARA ENVIAR'];
        } 
        $this->response($response);   
    }

 
}