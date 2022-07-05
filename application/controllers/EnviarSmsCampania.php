<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';


use Restserver\Libraries\REST_Controller;


class EnviarSmsCampania extends REST_Controller
{

    public function __construct($config = 'rest')
    {
        parent::__construct();
        $this->load->model('Modulos_m', '', TRUE);
        $this->load->model('InfoBipModel', '', TRUE);
        $this->load->helper(array('form', 'url','my_date','formato'));
        $this->load->config('form_validation');
        $this->load->library('Infobip_library');
        $this->load->library('Nrs360_library');
        $this->load->library('Pepipost_library');
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);

    }
//SINO SE LLEGA A EJECUTAR ES PORQUE ES FERIADO EN COLOMBIA


//ENVIO MAIL MORA CON PEPIPOST
public function EnviarMoraMail_get()  
    {
    $fechaHoy = date("Y-m-d");
    $valor = feriados($fechaHoy);
    if($valor != true){
        $tipo = "moraSms"; 
        $datosVerificacion = $this->InfoBipModel->clientesMora($tipo); 
        $this->InfoBipModel->insertarMora($datosVerificacion, $tipo);       
        $datos = $this->InfoBipModel->moraCampania($tipo);  
        if(isset($datos)){ 
          foreach ($datos as $key => $a) {
            $numero_prestamo = $a->id_credito;  
            $fecha1 = $a->vencimiento;
            $vencimiento = date("d/m/Y", strtotime($fecha1));    
            $fecha2 = date("Y-m-d");            
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $dia = strftime("%A");           
            $dias_pasados = diasEntreFechas($fecha1,$fecha2); 
            if($fecha1 > $fecha2){//ANTES DEL VENCIMIENTO                                 
              if($dias_pasados == 1){ 
                    $masAntes[] = $this->guardarDatosMailMora($a,$tipo,21305);          
              }elseif($dias_pasados == 4){
                    $antes[] = $this->guardarDatosMailMora($a,$tipo,21306);              
              }  
            }            
            elseif($fecha1 == $fecha2){//EL DIA QUE SE VENCE  
                $mientras[] = $this->guardarDatosMailMora($a,$tipo,21307); 
            }
            elseif($fecha1 < $fecha2){ //DESPUES DE QUE SE VENCIO
                $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);  
                if($diasHabiles == 2){   
                        $despues[] = $this->guardarDatosMailMora($a,$tipo,21308);
                }
                elseif($diasHabiles == 10){                  
                    $masDespues[] = $this->guardarDatosMailMora($a,$tipo,20956);
                }
                elseif($diasHabiles == 22){
                    $datacredito[] = $this->guardarDatosMailMora($a,$tipo,21172);
                } 
            }
        }          
       if(isset($masAntes)){
           $response = $this->pepipost_library->curl_pepipost($masAntes,21305,"Cuota por vencer");
         }else{
            $response = ['status' => 200, 'data' => 'No hay Mails para enviar'];
        }
        if(isset($antes)){
           $response1 = $this->pepipost_library->curl_pepipost($antes,21306,"Cuota por vencer");
         }else{
            $response1 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
        }
        if(isset($mientras)){
            $response2 = $this->pepipost_library->curl_pepipost($mientras,21307,"Vencimiento");
         }else{
            $response2 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
        }
        if(isset($despues)){
          $response3 = $this->pepipost_library->curl_pepipost($despues,21308,"Cuota Vencida");
         }else{
            $response3 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
        }
        if(isset($masDespues)){
           $response4 = $this->pepipost_library->curl_pepipost($masDespues,20956,"Cuota Vencida");
         }else{
            $response4 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
        }
        if(isset($datacredito)){
           $response5 = $this->pepipost_library->curl_pepipost($datacredito,21172,"Cuota Vencida");
         }else{
            $response5 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
        }
          $response = array($response, $response1, $response2, $response3, $response4, $response5);  
    } else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
    }
       
      $this->response($response);
      }
  }  

//ENVIO SMS MORA INFOBIP
 public function EnviarMoraSmsInfobip_get()  
    {
    $fechaHoy = date("Y-m-d");
    $valor = feriados($fechaHoy);
    if($valor != true){
        $tipo = "moraSms"; 
        $datosVerificacion = $this->InfoBipModel->clientesMora($tipo); 
        $this->InfoBipModel->insertarMora($datosVerificacion, $tipo);       
        $datos = $this->InfoBipModel->moraCampania($tipo);  
    if(isset($datos)){
       foreach ($datos as $key => $a) {
            $flag = false;
            $tipo_solicitud = $a->tipo_solicitud;
            $numero_prestamo = $a->id_credito;  
            $monto_prestado1 = $a->monto_prestado;
            $monto_prestado = arreglar_prestamo($monto_prestado1);
            $prestamo = arreglar_prestamo($a->monto);
            $nombres = arreglar_string($a->nombres);      
            $fecha1 = $a->vencimiento;
            $vencimiento = date("d/m/Y", strtotime($fecha1));    
            $fecha2 = date("Y-m-d");            
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $dia = strftime("%A");           
            $dias_pasados = diasEntreFechas($fecha1,$fecha2);  
            $diaSemana = saber_dia($fecha1);
            $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);
            $vencimientoCortado = substr($vencimiento, 0, 5);
            if($fecha1 > $fecha2){//ANTES DEL VENCIMIENTO
                if($tipo_solicitud == 'PRIMARIA'){
                    $monto_prestado1 = $monto_prestado1 + $monto_prestado1*20/100;                    
                    $monto_prestado = arreglar_prestamo($monto_prestado1);
                }                
                if($dias_pasados == 0 || $dias_pasados == 1 || $dias_pasados == 4){ 
                        $text = $nombres.', paga tu cuota antes del '.$diaSemana.' '.$vencimientoCortado.' y obten $'.$monto_prestado.' en tu proximo prestamo. Puedes escribirnos por WhatsApp aqui solven.me/wamoa. Solventa';
                        $data[] = $this->guardarDatosMora($a,$tipo,$text);
                        $creditos[] = $numero_prestamo;                   
                }
            }
            elseif($fecha1 == $fecha2){//EL DIA QUE SE VENCE
                $text = $nombres.', Hoy '.$diaSemana.' '.$vencimientoCortado.' vence tu cuota. Pagala hoy y recibes $'.$monto_prestado.' en tu proximo prestamo. Si quieres ayuda escribenos aqui solven.me/wamob. Solventa'; 
                $data[] = $this->guardarDatosMora($a,$tipo,$text);
                $creditos[] = $numero_prestamo;
            }
            elseif($fecha1 < $fecha2){ //DESPUES DE QUE SE VENCIO
                if(($dia == 'Monday' && $dias_pasados >=0 && $dias_pasados <=3) || $dias_pasados == 5 || $dias_pasados == 2 || $dias_pasados == 3){
                        $text = $nombres.', aun puedes pagar tu cuota hoy y obtener $'.$monto_prestado.' en tu proximo prestamo. Si necesitas ayuda escribenos aqui solven.me/wamoc. Solventa';
                        $data[] = $this->guardarDatosMora($a,$tipo,$text);
                        $creditos[] = $numero_prestamo;
                }
                elseif($diasHabiles == 10){               
                        $text = 'Hola '.$nombres.'! Tu prestamo #'.$numero_prestamo.' tiene un retraso de 10 días en el pago. Evita entrar en mora. Puedes enviarnos un WhatsApp aqui solven.me/wamod. Solventa';
                        $data[] = $this->guardarDatosMora($a,$tipo,$text); 
                        $creditos[] = $numero_prestamo;
                }
                elseif($diasHabiles == 22){
                    $text = 'Paga tu prestamo hoy o seras reportado en DATACREDITO. Paga en EFECTY con el N° de convenio 010671. Puedes escribirnos aqui solven.me/wl2m. Solventa Prestamos'; 
                    $data[] = $this->guardarDatosMora($a,$tipo,$text);  
                    $creditos[] = $numero_prestamo;
                }
            } 
        }
        if(isset($data)){
           $response = $this->infobip_library->infobip_curl_mora($data,"Cliente",$tipo,$creditos);
        } 
        else{
            $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
        } }
    else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
    }       
      $this->response($response);
  }}

//ENVIO WHATSAPP MORA CON TWILIO
   public function EnviarMoraWhatsapp_get()  
    {
    $fechaHoy = date("Y-m-d");
    $valor = feriados($fechaHoy);
    if($valor != true){
        $tipo = "moraSms"; 
        $datosVerificacion = $this->InfoBipModel->clientesMora($tipo); 
        $this->InfoBipModel->insertarMora($datosVerificacion, $tipo);       
        $datos = $this->InfoBipModel->moraCampania($tipo);          
    if(isset($datos)){
       foreach ($datos as $key => $a) {
            $flag = false;          
            $idCliente = $a->id_cliente;         
            $fecha1 = $a->vencimiento;
            $vencimiento = date("d/m/Y", strtotime($fecha1));    
            $fecha2 = date("Y-m-d");            
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $dia = strftime("%A");           
            $dias_pasados = diasEntreFechas($fecha1,$fecha2); 
            $diaSemana = saber_dia($fecha1);
            if($fecha1 < $fecha2){ //DESPUES DE QUE SE VENCIO
                if(($dia == 'Monday' && $dias_pasados >=0 && $dias_pasados <=3) || $dias_pasados == 1){
                        $idTemplate = "template_44";
                        $this->WhatsappCampaña($idCliente,$idTemplate);                 
                }
            }   
        }
      } 
    else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
    }
} }   
 

//ENVIO MAIL RETANQUEO CON PEPIPOST
public function EnviarMailRetanqueo_get(){ 
    $fechaHoy = date("Y-m-d");
    $valor = feriados($fechaHoy);
    if($valor != true){
        $tipo = "retanqueoSms";
        $datosVerificacion = $this->InfoBipModel->getClientesRetanqueo($tipo);
        $this->InfoBipModel->insertarRetanqueos($datosVerificacion, $tipo);
        $datos = $this->InfoBipModel->retanqueosCampania($tipo);
        if(isset($datos)){ 
            foreach ($datos as $key => $a) {  
                $estado = $a->estado;
                if($estado == 1 ){ 
                    if ($a->beneficio == 0){ 
                        $mail[] = $this->guardarDatosMailRetanqueo($a,$tipo,21049); 
                    }else{
                        $mail3[] = $this->guardarDatosMailRetanqueo($a,$tipo,21250);                         
                    }
                }elseif($estado > 5 && $estado <= 13){
                    $dia = strftime("%A");
                    if($dia == "Thursday"){
                            $mail2[] = $this->guardarDatosMailRetanqueo($a,$tipo,21080); 
                    }
                }   
            }   
            if(isset($mail)){
                $response1 = $this->pepipost_library->curl_pepipost($mail,21049,"Prestamo Disponible");
            }else{
                $response1 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
            }
            if(isset($mail2)){
                $response2 = $this->pepipost_library->curl_pepipost($mail2,21080,"Prestamo Disponible");
            }else{
                $response2 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
            }
            if(isset($mail3)){ 
               $response3 = $this->pepipost_library->curl_pepipost($mail3,21250,"Prestamo Disponible");
            }else{
                $response3 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
            }
            $response=array($response1,$response2,$response3);
        }
        else{
            $response = ['status' => 200, 'data' => 'No hay mensajes ni mails para enviar'];
            }             
        $this->response($response);
   } 
}

//ENVIO SMS RETANQUEO INFOBIP
 public function EnviarSmsRetanqueo_get(){
    $fechaHoy = date("Y-m-d");
    $valor = feriados($fechaHoy);
    if($valor != true){
        $tipo = "retanqueoSms";
        $datosVerificacion = $this->InfoBipModel->getClientesRetanqueo($tipo);
        $this->InfoBipModel->insertarRetanqueos($datosVerificacion, $tipo);
        $datos = $this->InfoBipModel->retanqueosCampania($tipo);
        if(isset($datos)){ 
            foreach ($datos as $key => $a) {  
                $estado = $a->estado;
                if($estado == 2){                   
                   $data[] = $this->guardarDatosRetanqueo($a,$tipo);                  
                }
                elseif($estado > 2 && $estado <= 5){
                        $fecha1 = $a->fecha_actualizacion;
                        $fecha2 = date("Y-m-d");
                        $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);
                        if($fecha1 < $fecha2 && $diasHabiles >= 0){ 
                            $data[] = $this->guardarDatosRetanqueo($a,$tipo); 
                        }
                }
                elseif($estado > 5 && $estado <= 13){
                    $dia = strftime("%A");
                    if($dia == "Thursday"){ 
                           $data[] = $this->guardarDatosRetanqueo($a,$tipo);  
                    }
                }                                   
            }
            if(isset($data)){
              $response = $this->infobip_library->infobip_curl($data,"Cliente",$tipo);
            }else{
                $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
            }           
            $response=array($response);
        }
        else{
            $response = ['status' => 200, 'data' => 'No hay mensajes ni mails para enviar'];
            }             
        $this->response($response);
   } 
}

//ENVIO SMS DESEMBOLSO VERIFICADO
public function EnviarSmsDesembolsoVerificado_get(){
    $fechaHoy = date("Y-m-d");
    $valor = feriados($fechaHoy);
    if($valor != true){
    $tipo = "desembolsoVerificado"; 
    $datosVerificacion = $this->InfoBipModel->desembolso($tipo,"VERIFICADO");
    $insertarDatos = $this->InfoBipModel->insertarSolicitantesDesemblolso($datosVerificacion, $tipo);
    $datos = $this->InfoBipModel->desembolsoCampania($tipo,"VERIFICADO");
    if(isset($datos)){
        foreach ($datos as $key => $a) { 
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $time = time();
            $idSolicitud = $a->id_solicitud;
            $tiempo = date("H:i", $time);  
            $estado = $a->estado;                   
            $nombres = arreglar_string($a->nombres);   
            $prestamo = arreglar_prestamo($a->monto);
            $fecha1 = new DateTime($a->fecha_ingreso);
            $fecha1 = $fecha1->format('Y-m-d');       
            $fecha2 = date("Y-m-d");
            $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);
            if($tiempo >= '10:50' && $tiempo <= '11:45'){ 
                if($fecha1 < $fecha2 && $estado == 1 && $diasHabiles >= 0){ 
                    $mail[] = $this->guardarDatosMailVerificado($a,$tipo);
                }
                elseif($estado == 1 && $diasHabiles > 0){
                    $mail[] = $this->guardarDatosMailVerificado($a,$tipo);
                }
                $fecha1 = $a->fecha_actualizacion;    
                $fecha2 = date("Y-m-d");
                $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);
                if($estado == 3 && $fecha1 < $fecha2 && $diasHabiles >= 0){
                    $text = 'Hola '.$nombres.'! No logramos comunicarnos contigo para desembolsar tu Prestamo de $ '.$prestamo.'. Envianos un WhatsApp entrando aqui solven.me/wadpb. Solventa';
                    $data[] = $this->guardarDatos($a,$tipo,$text);   
                }
                elseif($estado == 5 && $fecha1 < $fecha2 && $diasHabiles >= 0){
                    $mail[] = $this->guardarDatosMailVerificado($a,$tipo); 
                }
                elseif($estado >= 7 && $estado < 10 && $diasHabiles >= 3){
                    $text = 'Hola '.$nombres.'! No logramos comunicarnos contigo para desembolsar tu Prestamo de $ '.$prestamo.'. Envianos un WhatsApp entrando aqui solven.me/wadpd. Solventa';
                          $data[] = $this->guardarDatos($a,$tipo,$text);     
                }
            }
            elseif($tiempo >= '14:50' && $tiempo <= '15:45'){
                $fecha1 = new DateTime($a->fecha_ingreso);
                $fecha1 = $fecha1->format('Y-m-d'); 
                $fecha2 = date("Y-m-d");
                $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);   
                 if($estado == 2 && $fecha1 < $fecha2 && $diasHabiles >= 0){        
                    $text = 'Hola '.$nombres.'! Estamos llamandote para desembolsar tu Prestamo de $ '.$prestamo.'. Puedes enviarnos un WhatsApp entrando aqui solven.me/wadpa. Solventa';  
                    $data[] = $this->guardarDatos($a,$tipo,$text); 
                }
                $fecha1 = $a->fecha_actualizacion;    
                $fecha2 = date("Y-m-d");
                $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);
                if($estado == 4 && $diasHabiles >= 0){
                    $mail[] = $this->guardarDatosMailVerificado($a,$tipo);
                }
                elseif($estado == 6 && $diasHabiles >= 0){
                    $text = 'Hola '.$nombres.'! No logramos comunicarnos contigo para desembolsar tu Prestamo de $ '.$prestamo.'. Envianos un WhatsApp entrando aqui solven.me/wadpc. Solventa';
                    $data[] = $this->guardarDatos($a,$tipo,$text);
                }
            }
             if($estado == 0){
                $this->InfoBipModel->actualizarEstadoValidado(1,$idSolicitud,$tipo);
            } 
          }
          if(isset($data)){
                 $response = $this->infobip_library->infobip_curl($data,"Solicitantes",$tipo);
            }
            else{
                $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
            }
           /* if(isset($mail)){
            $response1 = $this->pepipost_library->curl_pepipost($mail,555555,"Desembolso Solventa");
         }else{
            $response1 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
        }*/
          //  $response=array($response,$response1);
        $response=array($response);
        }
        else{
            $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
            }             
        $this->response($response);   
       } 
}  

//ENVIO SMS DESEMBOLSO VALIDADO
public function EnviarSmsDesembolsoValidado_get() {
   
   $fechaHoy = date("Y-m-d");
    $valor = feriados($fechaHoy);
    if($valor != true){
    $tipo = "desembolsoValidado"; 
    $datosVerificacion = $this->InfoBipModel->desembolso($tipo,"VALIDADO");
    $insertarDatos = $this->InfoBipModel->insertarSolicitantesDesemblolso($datosVerificacion, $tipo);
    $datos = $this->InfoBipModel->desembolsoCampania($tipo,"VALIDADO");

    if(isset($datos)){
        foreach ($datos as $key => $a) { 
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $time = time();
            $tiempo = date("H:i", $time);
            $estado = $a->estado;    
            $idSolicitud = $a->id_solicitud;                
            $nombres = arreglar_string($a->nombres);   
            $prestamo = $a->monto;
            $prestamo = arreglar_prestamo($prestamo);  
            $fecha = $a->fecha_actualizacion;              
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $fechaIngreso2 = $a->fecha_ingreso;
            $fechaIngreso = new DateTime($fechaIngreso2); 
            $fechaAhora = new DateTime("now"); 
            $intervalo = $fechaIngreso->diff($fechaAhora);
            $horas = $intervalo->format('%H');
            if($estado == 1){              
                $text = 'Hola '.$nombres.'! Para desembolsar tus $ '.$prestamo.' necesitas firmar el Pagare enviado a tu correo. Puedes enviarnos un WhatsApp aqui solven.me/wadva. Solventa';                                                     
                    $data[] = $this->guardarDatos($a,$tipo,$text); 
            }
            if($tiempo >= '10:50' && $tiempo <= '11:45'){
                $fecha1 = $a->fecha_actualizacion;
                $fecha2 = date("Y-m-d");
                $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);
                if(($estado == 2 || $estado == 4) && $diasHabiles > 0 && $fecha1 <= $fecha2){
                    $mail[] = $this->guardarDatosMailVerificado($a,$tipo);   
                }
                elseif($estado >= 6 && $estado <= 9 && $diasHabiles>=3){
                    $text = 'Hola '.$nombres.'! Para desembolsar tus $ '.$prestamo.' necesitas firmar el Pagare enviado a tu correo. Puedes enviarnos un WhatsApp aqui solven.me/wadvd. Solventa';   
                    $data[] = $this->guardarDatos($a,$tipo,$text);  
                }
            }
            elseif($tiempo >= '14:50' && $tiempo <= '15:45'){
                $fecha1 = $a->fecha_actualizacion; 
                $fecha2 = date("Y-m-d");
                $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);
                if($estado == 3 && $diasHabiles > 0 ){
                    $text = 'Hola '.$nombres.'! Para desembolsar tus $ '.$prestamo.' necesitas firmar el Pagare enviado a tu correo. Puedes enviarnos un WhatsApp aqui solven.me/wadvb. Solventa';
                    $data[] = $this->guardarDatos($a,$tipo,$text);  
                }
                elseif($estado == 5 && $diasHabiles > 0){
                    $text = 'Hola '.$nombres.'! Para desembolsar tus $ '.$prestamo.' necesitas firmar el Pagare enviado a tu correo. Puedes enviarnos un WhatsApp aqui solven.me/wadvc. Solventa';
                    $data[] = $this->guardarDatos($a,$tipo,$text);  
                }
            }
            if($estado == 0){
                $this->InfoBipModel->actualizarEstadoValidado(1,$idSolicitud,$tipo);
            }    
                   
        }              
        if(isset($data)){
                 $response = $this->infobip_library->infobip_curl($data,"Solicitantes",$tipo);
            }
            else{
                $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
            }
           /* if(isset($mail)){
            $response1 = $this->pepipost_library->curl_pepipost($mail,444444,"Desembolso Solventa");
         }else{
            $response1 = ['status' => 200, 'data' => 'No hay Mails para enviar'];
        }*/
          //  $response=array($response,$response1);
        $response=array($response);
        }
        else{
            $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
            }             
        $this->response($response);      
     } }



public function guardarDatosRetanqueo($a,$tipo){
            $beneficio = $a->beneficio;
            $monto = $a->monto;
            $prestamo = arreglar_prestamo($monto + ($monto * $beneficio / 100));             
            $idUsuario = $a->id_usuario;
            $estado = $a->estado;
            $dni = $a->documento;
            $nombres = arreglar_string($a->nombres);   
           if (ENVIRONMENT == 'development') {
            $telefono = TEST_PHONE_NUMBER;
            }else {
                $telefono = $a->telefono;
                $telefono = "+57" . $telefono;
            }
            switch($estado){
                case "2":
                 $link = "solven.me/reta";
                break;
                case "3":
                 $link = "solven.me/retb";
                break;
                case "4":
                 $link = "solven.me/retc";
                break;
                case "5":
                 $link = "solven.me/retd";
                break;
                case "6":
                 $link = "solven.me/rete";
                break;
                case "7":
                 $link = "solven.me/retf";
                break;
                case "8":
                 $link = "solven.me/retf";
                break;
                case "9":
                 $link = "solven.me/retf";
                break;
                case "10":
                 $link = "solven.me/retf";
                break; 
            }
            $text = 'Hola '.$nombres.'! Tienes disponible un Prestamo de $ '.$prestamo.'. Solicitalo ahora y ten el dinero en el dia. Solicitar aqui '.$link.'. Solventa';       
            
            $idUsuario = $a->id_usuario;
            $text = sanear_string($text);
            $data = array(
                'from' => 'Solventa',
                'to' => $telefono,
                'text' => $text
            );
            $this->InfoBipModel->insertarSmsParaEnviar($telefono,$dni,$tipo,$text,0);
            $this->InfoBipModel->actualizarCampaniaRetanqueo($idUsuario, $estado, $tipo);
            return $data;
}

public function guardarDatosMailRetanqueo($a,$tipo,$template){
        $idUsuario = $a->id_usuario;
        $estado = $a->estado;
        $monto = $a->monto;
        $beneficio = $a->beneficio;
        $dni = $a->documento;
        $extra = arreglar_prestamo($monto * $beneficio / 100);
        $prestamo = arreglar_prestamo($a->monto);
        $prestamoTotal = arreglar_prestamo($monto + ($monto * $beneficio / 100));
        $nombres = arreglar_string($a->nombres);   
        if (ENVIRONMENT == 'development') {
                $email = TEST_EMAIL;
            }
            else {
                $email = $a->email;
            }
        $per = array(
                'recipient' => $email,
                "attributes" => array(
                    "FNAME" => $nombres,                    
                    "MONTO" => $prestamoTotal,
                    "CAMPANA" => $tipo,
                    "MEXTRA" => $extra,
                    "MSEXTRA" => $prestamo,           
                ),
                'x-apiheader' => $tipo . ',' . $template
            );
        $this->InfoBipModel->insertarMailParaEnviar($email,$dni,$template,$tipo,0);
        $this->InfoBipModel->actualizarCampaniaRetanqueo($idUsuario, $estado, $tipo); 
        return $per;
}

public function guardarDatosMailVerificado($a,$tipo,$template){
        $idSolicitud = $a->id_solicitud;
        $estado = $a->estado;
        $prestamo = arreglar_prestamo($a->monto);     
        $nombres = arreglar_string($a->nombres);   
        if (ENVIRONMENT == 'development') {
                $email = TEST_EMAIL;
            }
            else {
                $email = $a->email;
            }
        $per = array(
                'recipient' => $email,
                "attributes" => array(
                    "FNAME" => $nombres,                    
                    "MONTO" => $prestamo,
                    "CAMPANA" => $tipo,          
                ),
                'x-apiheader' => $tipo.','.$template
            );
        $this->InfoBipModel->actualizarCampaniaDesembolso($idSolicitud, $estado, $tipo);
        return $per;
}

public function guardarDatosMailMora($a,$tipo,$template){
        $tipo_solicitud = $a->tipo_solicitud;
        $numero_prestamo = $a->id_credito;  
        $vencimiento = $a->vencimiento;
        $dia = saber_dia($a->vencimiento);
        $diaActual = saber_dia(date("Y-m-d"));
        $fechaActual = date("d/m/Y", strtotime(date("Y-m-d"))); 
        $fActualCortado = substr($fechaActual, 0, 5);
        $vencimiento = date("d/m/Y", strtotime($vencimiento));
        $vencimientoCortado = substr($vencimiento, 0, 5);
        $prestamo = $a->monto;
        $prestamo = arreglar_prestamo($prestamo);     
        $nombres = arreglar_string($a->nombres);   
        $apellidos = arreglar_string($a->apellidos);
        $monto = $a->monto_prestado; 
        $extra = arreglar_prestamo($monto * 20 / 100);
        $dni = $a->documento;
        if (ENVIRONMENT == 'development') {
                $email = TEST_EMAIL;
            }
            else {
                $email = $a->email;
            }
        $per = array(
                'recipient' => $email,
                "attributes" => array(
                    "FNAME" => $nombres,
                    "LNAME" => $apellidos,
                    "NPREST" => $numero_prestamo,                     
                    "CUOTA" => $prestamo,
                    "CAMPANA" => $tipo,
                    "FVEN" => $vencimiento,
                    "FVENCOR" => $vencimientoCortado,
                    "DIAACTUAL" => $diaActual,
                    "FPARTIDA" => $fActualCortado,
                    "DIA" => $dia,
                    "MEXTRA" => $extra,                     
                ),
                'x-apiheader' => $tipo.','.$template.','.$numero_prestamo
            );
        $this->InfoBipModel->insertarMailParaEnviar($email,$dni,$template,$tipo,$numero_prestamo);  
        return $per;
}

public function prepararSms($sub,$telefono,$numero){
    switch ($numero) {
        case '1':
            $text = '{NAME}, paga tu cuota antes del {DISEM} {VENCOR} y obten ${MPRES} en tu proximo prestamo. Puedes escribirnos por WahatsApp aqui solven.me/wamoa. Solventa';    
        break;
        case '2':            
            $text = '{NAME}, Hoy {DISEM} {VENC} vence tu cuota. Pagala hoy y recibes ${MPRES} en tu proximo prestamo. Si quieres ayuda escribenos aqui solven.me/wamob. Solventa';   
        break;
        case '3':
            $text = '{NAME}, aun puedes pagar tu cuota hoy y obtener ${MPRES} en tu proximo prestamo. Si necesitas ayuda escribenos aqui solven.me/wamoc. Solventa';   
        break;
        case '4':
            $text = 'Hola {NAME}! Tu prestamo # {NPRE}! tiene un retraso de 10 días en el pago. Evita entrar en mora. Puedes enviarnos un WhatsApp aqui solven.me/wamod. Solventa';    
        break;
        case '5':
            $text = 'Hola {NAME}! Tu prestamo # {NPRE} tiene un retraso en el pago de 25 días. Evita entrar en mora. Puedes enviarnos un WhatsApp aqui solven.me/wamoe. Solventa';    
        break;
    }
    $data = array(
            'message' => $text,
            'to' => $telefono,
            'from' => 'Solventa',
            'sub' => $sub,
            'campaignName' => 'moraNrs',
            'notificationUrl' => base_url().'RecibirSms/recibirSmsNrs'
    );
    return $data;
}

public function prepararSmsQuincena($sub,$telefono,$numero){
    switch ($numero) {
        case '1':
            $text = 'Hola {NAME}, puedes pagar tu cuota hoy y obtener ${MPRES} en tu proximo prestamo. Aprovecha la quincena y paga aqui solven.me/mnrs. Solventa';          
        break;
        case '2':            
            $text = 'Tu prestamo # {NPRE} tiene un pago retrasado de {DIAS} dias. Aprovecha la quincena y paga aqui solven.me/mnrs. No pierdas los beneficios del retanqueo. Solventa';
        break;
        case '3':
            $text = 'Hola {NAME}! Tu prestamo # {NPRE} tiene un retraso de {DIAS} dias en el pago.  Aprovecha la quincena y paga tu cuota aquí solven.me/mnrs. Solventa'; 
        break;
        case '4':
            $text = '{NAME}, Hoy {DISEM} {VENC} vence tu cuota. Pagala hoy y recibes ${MPRES} en tu proximo prestamo. Si quieres ayuda escribenos aqui solven.me/wamob. Solventa'; 
        break;
    }
    $data = array(
            'message' => $text,
            'to' => $telefono,
            'from' => 'Solventa',
            'sub' => $sub,
            'campaignName' => 'pruebaNrs',
            'notificationUrl' => base_url().'RecibirSms/recibirSmsNrs'
    );
    return $data;
}

public function guardarSmsQuincenaNrs($numero){
    switch ($numero) {
        case '1':
            $text = 'Hola {NAME}, puedes pagar tu cuota hoy y obtener ${MPRES} en tu proximo prestamo. Aprovecha la quincena y paga aqui solven.me/mnrs. Solventa';          
        break;
        case '2':            
            $text = 'Tu prestamo # {NPRE} tiene un pago retrasado de {DIAS} dias. Aprovecha la quincena y paga aqui solven.me/mnrs. No pierdas los beneficios del retanqueo. Solventa';
        break;
        case '3':
            $text = 'Hola {NAME}! Tu prestamo # {NPRE} tiene un retraso de {DIAS} dias en el pago.  Aprovecha la quincena y paga tu cuota aquí solven.me/mnrs. Solventa'; 
        break;
        case '4':
            $text = '{NAME}, Hoy {DISEM} {VENC} vence tu cuota. Pagala hoy y recibes ${MPRES} en tu proximo prestamo. Si quieres ayuda escribenos aqui solven.me/wamob. Solventa'; 
        break;
    }
    return $text;
}

public function guardarDatosMora($a,$tipo,$text){
            $idUsuario = $a->id_usuario;
            $dni = $a->documento;
            if (ENVIRONMENT == 'development') {
                $telefono = TEST_PHONE_NUMBER;
            }
            else{
                $telefono = $a->telefono;
                $telefono = "+57" . $telefono;
            }
            $numero_prestamo = $a->id_credito;  
            $estado = $a->estado; 
            $idUsuario = $a->id_usuario;          
            $text = sanear_string($text);
            $data = array(
                'from' => 'Solventa',
                'to' => $telefono,
                'text' => $text
            );
            $this->InfoBipModel->insertarSmsParaEnviar($telefono,$dni,$tipo,$text,$numero_prestamo);           
            return $data;
}

public function guardarDatosMoraNrs($a,$tipoSms){                    
            $tipo_solicitud = $a->tipo_solicitud;
            $numero_prestamo = $a->id_credito;  
            $monto_prestado1 = $a->monto_prestado;
            $telefono = $a->telefono;
            $telefono = "57".$telefono;
            $dni = $a->documento;
            $idCliente = $a->id_cliente;
            $monto_prestado = arreglar_prestamo($monto_prestado1);
            $prestamo = arreglar_prestamo($a->prestamo);
            $nombres = arreglar_string($a->nombres);   
            $estado = $a->estado;
            $idUsuario = $a->id_usuario; 
            $time = time();
            $tiempo = date("H:i", $time);
            $fecha1 = $a->vencimiento;
            $vencimiento = date("d/m/Y", strtotime($fecha1));    
            $fecha2 = date("Y-m-d");            
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $dia = strftime("%A");           
            $dias_pasados = diasEntreFechas($fecha1,$fecha2); 
            $diaSemana = saber_dia($fecha1);
            $vencimientoCortado = substr($vencimiento, 0, 5);      
            if($tipoSms == 1){//ANTES DEL VENCIMIENTO
                if($tipo_solicitud == 'PRIMARIA'){
                    $monto_prestado1 = $monto_prestado1 + $monto_prestado1*20/100;                    
                    $monto_prestado = arreglar_prestamo($monto_prestado1);
                }                      
                $data = array(
                    'NAME' => $nombres,
                    'DISEM' => $diaSemana,
                    'VENCOR' => $vencimientoCortado,
                    'MPRES' => $monto_prestado,                    
                ); 
            }elseif($tipoSms == 2){//DIA VENCIMIENTO
                $data = array(
                    'NAME' => $nombres,
                    'DISEM' => $diaSemana,
                    'VENC' => $vencimiento,
                    'MPRES' => $monto_prestado,
                );
            }elseif($tipoSms == 3){//DESPUES DE VENCIMIENTO
                $data = array(
                    'NAME' => $nombres,
                    'MPRES' => $monto_prestado,
                );                
            }elseif($tipoSms == 4){
                $data = array(
                    'NAME' => $nombres,
                    'NPRE' => $numero_prestamo,
                );
                
            }elseif($tipoSms == 5){
                $data = array(
                    'NAME' => $nombres,
                    'NPRE' => $numero_prestamo,
                );
                
            }  
            return $data;
}

public function guardarDatosMoraNrsQuincena($a,$tipoSms){
            if (ENVIRONMENT == 'development') {
                $telefono = TEST_PHONE_NUMBER;
            }else{
                $telefono = $a->telefono;
                $telefono = "57" . $telefono;
            }                    
            $tipo_solicitud = $a->tipo_solicitud;
            $dni = $a->documento;
            $numero_prestamo = $a->id_credito;  
            $monto_prestado1 = $a->monto_prestado;
            $monto_prestado = arreglar_prestamo($monto_prestado1);
            $prestamo = arreglar_prestamo($a->monto);
            $nombres = arreglar_string($a->nombres);   
            $fecha1 = $a->vencimiento;
            $vencimiento = date("d/m/Y", strtotime($fecha1));    
            $fecha2 = date("Y-m-d");            
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $dia = strftime("%A");           
            $dias_pasados = diasEntreFechas($fecha1,$fecha2);   
            $diaSemana = saber_dia($fecha1); 
            if($tipoSms == 1){//ANTES DEL VENCIMIENTO                
                if($tipo_solicitud == 'PRIMARIA'){
                    $monto_prestado1 = $monto_prestado1 + $monto_prestado1*20/100;                    
                    $monto_prestado = arreglar_prestamo($monto_prestado1);
                }   
                $data = array(
                    'NAME' => $nombres,
                    'MPRES' => $monto_prestado,                    
                ); 
            }elseif($tipoSms == 2){//DIA VENCIMIENTO
                $data = array(
                    'DIAS' => $dias_pasados, 
                    'NPRE' => $numero_prestamo,
                );
            }elseif($tipoSms == 3){//DESPUES DE VENCIMIENTO
                $data = array(
                    'NAME' => $nombres,
                    'DIAS' => $dias_pasados, 
                    'NPRE' => $numero_prestamo,
                ); 
            }
            elseif($tipoSms == 4){//DIA DE VENCIMIENTO
                $data = array(
                    'NAME' => $nombres,
                    'DISEM' => $diaSemana,
                    'VENC' => $vencimiento,
                    'MPRES' => $monto_prestado,
                ); 
            }
            $text = $this->guardarSmsQuincenaNrs($tipoSms);
            $this->InfoBipModel->insertarSmsParaEnviar($telefono,$dni,'moraNrs',$text,$numero_prestamo);               
            return $data;
}


public function guardarDatos($a,$tipo,$text){
    $estado = $a->estado;
    $dni = $a->documento;
    if (ENVIRONMENT == 'development') {
        $telefono = TEST_PHONE_NUMBER;
    }else {
    $telefono = $a->telefono;
    $telefono = "+57" . $telefono;
    }
    $idSolicitud = $a->id_solicitud;  
    $text = sanear_string($text);
    $data = array(
          'from' => 'Solventa',
           'to' => $telefono,
           'text' => $text
          ); 
   $this->InfoBipModel->insertarSmsParaEnviar($telefono,$dni,$tipo,$text,0);                      
   $this->InfoBipModel->actualizarCampaniaDesembolso($idSolicitud, $estado, $tipo);
   return $data;                                
}

  public function NuevasMorasQuincena_get()  
    {    
         $tipo = "moraQuincenal"; 
        $datosVerificacion = $this->InfoBipModel->clientesMora($tipo); 
       $this->InfoBipModel->insertarMora($datosVerificacion, $tipo);       
       $datos = $this->InfoBipModel->nuevaCampaniaMoraQuincenal($tipo);       
    if(isset($datos)){ 
       foreach ($datos as $key => $a) {
            $tipo_solicitud = $a->tipo_solicitud;
            $numero_prestamo = $a->id_credito;  
            $monto_prestado1 = $a->monto_prestado;
            $monto_prestado = arreglar_prestamo($monto_prestado1);
            $prestamo = arreglar_prestamo($a->monto);
            $nombres = arreglar_string($a->nombres);   
            $estado = $a->estado;
            $idUsuario = $a->id_usuario;                 
            $fecha1 = $a->vencimiento;
            $vencimiento = date("d/m/Y", strtotime($fecha1));    
            $fecha2 = date("Y-m-d");            
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $dia = strftime("%A");
            $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);            
            $dias_pasados = diasEntreFechas($fecha1,$fecha2);  
            $diaSemana = saber_dia($fecha1);
            if($fecha1 > $fecha2){//ANTES DEL VENCIMIENTO
                if($tipo_solicitud == 'PRIMARIA'){
                    $monto_prestado1 = $monto_prestado1 + $monto_prestado1*20/100;                    
                    $monto_prestado = arreglar_prestamo($monto_prestado1);
                }
                $vencimientoCortado = substr($vencimiento, 0, 5);
                if($dias_pasados >=0 && $dias_pasados <=4){
                        $text = 'Hola '.$nombres.', puedes pagar tu cuota hoy y obtener $'.$monto_prestado.' en tu proximo prestamo. Aprovecha la quincena y paga aqui solven.me/mqui. Solventa';   
                        $data[] = $this->guardarDatosMora($a,$tipo,$text);
                        $creditos[] = $numero_prestamo;                         
                }
            }
            elseif($fecha1 == $fecha2){//EL DIA QUE SE VENCE
                $text = $nombres.', Hoy '.$diaSemana.' '.$vencimiento.' vence tu cuota. Pagala hoy y recibes $'.$monto_prestado.' en tu proximo prestamo. Si quieres ayuda escribenos aqui solven.me/mqui. Solventa'; 
                $data[] = $this->guardarDatosMora($a,$tipo,$text);
                $creditos[] = $numero_prestamo;  
            }       
            elseif($fecha1 < $fecha2){ //DESPUES DE QUE SE VENCIO.
                if($dias_pasados > 0 && $dias_pasados <= 25){   
                      $text = 'Tu prestamo # '.$numero_prestamo.' tiene un pago retrasado de '.$dias_pasados.' dias. Aprovecha la quincena y paga aqui solven.me/mqui. No pierdas los beneficios del retanqueo. Solventa';
                        $data[] = $this->guardarDatosMora($a,$tipo,$text);
                }               
                elseif($dias_pasados > 25 && $dias_pasados <=30 ){
                    $text = 'Hola '.$nombres.'! Tu prestamo # '.$numero_prestamo.' tiene un retraso de '.$dias_pasados.' dias en el pago.  Aprovecha la quincena y paga tu cuota aquí solven.me/mqui. Solventa'; 
                    $data[] = $this->guardarDatosMora($a,$tipo,$text);
                    $creditos[] = $numero_prestamo;  
                }
            }
        }
     if(isset($data)){ 
            $response = $this->infobip_library->infobip_curl_mora($data,"Cliente",$tipo,$creditos);
    } 
    else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
    }
         
    } else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
         $response = array($response);
    }
       
        $this->response($response);

}

public function NuevasMorasQuincenaNrs_get()  //CAMBIARLE EL NOMBRE DE LA CAMPAÑA PORQUE FIGURA EL DE PRUEBA
    {    
    $tipo = "moraQuincenal"; 
    $datosVerificacion = $this->InfoBipModel->clientesMora($tipo); 
    $this->InfoBipModel->insertarMora($datosVerificacion, $tipo);       
    $datos = $this->InfoBipModel->nuevaCampaniaMoraQuincenal($tipo);       
    if(isset($datos)){ $i=0;
       foreach ($datos as $key => $a) {
            if (ENVIRONMENT == 'development') {
                $telefono = TEST_PHONE_NUMBER;
            }else{
                $telefono = $a->telefono;
                $telefono = "57" . $telefono;
            }//$telefono = '573022529113';
            $numero_prestamo = $a->id_credito;                      
            $fecha1 = $a->vencimiento;
            $vencimiento = date("d/m/Y", strtotime($fecha1));    
            $fecha2 = date("Y-m-d");            
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $dia = strftime("%A");
            $diasHabiles = getWorkingDaysColombia($fecha1,$fecha2);            
            $dias_pasados = diasEntreFechas($fecha1,$fecha2);  
            $diaSemana = saber_dia($fecha1);
            if($fecha1 < $fecha2){//DESPUES DE QUE SE VENCIO
                if($dias_pasados > 0 && $dias_pasados <= 25){ if($i<2){                        
                        $sub2[] = $this->guardarDatosMoraNrsQuincena($a,2);
                        $telefono2[] = $telefono;
                        $creditos[] = $numero_prestamo;$i++;} 
                }               
                elseif($dias_pasados > 25 && $dias_pasados <=30 ){
                   $sub3[] = $this->guardarDatosMoraNrsQuincena($a,3);
                   $telefono3[] = $telefono;
                   $creditos[] = $numero_prestamo;
            }}
            elseif($fecha1 > $fecha2){//ANTES DEL VENCIMIENTO
                if($dias_pasados >= 0 && $dias_pasados <= 4){                      
                     $sub1[] = $this->guardarDatosMoraNrsQuincena($a,1);
                        $telefono1[] = $telefono;
                        $creditos[] = $numero_prestamo;   
                }
            }
            elseif($fecha1 = $fecha2){//DIA DEL VENCIMIENTO                     
                     $sub4[] = $this->guardarDatosMoraNrsQuincena($a,4);
                     $telefono4[] = $telefono;
                     $creditos[] = $numero_prestamo;  
            } 
        }
     if(isset($sub1)){
        $antesSms = $this->prepararSmsQuincena($sub1,$telefono1,1);
        $response = $this->nrs360_library->nrs_curl_mora($antesSms,$creditos);
      }else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
      }
     if(isset($sub2)){
       $despuesSms = $this->prepararSmsQuincena($sub2,$telefono2,2);
       $response1 = $this->nrs360_library->nrs_curl_mora($despuesSms,$creditos);
      }else{
        $response1 = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
      }
      if(isset($sub3)){
        $muchoDespuesSms = $this->prepararSmsQuincena($sub3,$telefono3,3);
        $response2 = $this->nrs360_library->nrs_curl_mora($muchoDespuesSms,$creditos);
      }else{
        $response2 = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
      }
      if(isset($sub4)){
        $igualSms = $this->prepararSmsQuincena($sub4,$telefono4,4);
        $response3 = $this->nrs360_library->nrs_curl_mora($igualSms,$creditos);
      }else{
        $response3 = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
      }
        $response = $response1; //array($response,$response1,$response2,$response3);
         
    } else{
        $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
    }
       
        $this->response($response);

    }

public function smsPagados_get(){
    $listaPersonas = $this->InfoBipModel->pagados2();
    if(!empty($listaPersonas)){
        foreach ($listaPersonas as $key => $b) {
           $credito = $b->idCredito;
           $cliente = $b->idCliente;
           //echo $cliente;die();
           $respuesta = $this->InfoBipModel->posibilidadRetanqueo($cliente);
           if($respuesta == 1){           
              $persona = $this->InfoBipModel->datosPersonaRetanqueo($credito);
               foreach ($persona as $key => $a) {
                //var_dump($persona);die();
                  $creditos[] = $a->nPrestamo;
                  /*if($a->telefono == 3134007350){
                    echo $credito;die();
                  } */           
                  $nombres = arreglar_string($a->nombres);                
                  $beneficio = $a->disponible;
                  $porcentaje = $a->porcentaje;
                  $cuota = arreglar_prestamo($a->monto_cobrar);
                  $dias_pasados = $a->dias_atraso;
                  $nPrestamo = $a->nPrestamo;
                  if($dias_pasados < 30){
                     $beneficioRetanqueo = $beneficio + (($beneficio * $porcentaje)/100);        
                  }elseif($dias_pasados >= 30 && $dias_pasados <=59){
                     $beneficioRetanqueo = $beneficio - (($beneficio * 30)/100);
                  }elseif($dias_pasados >= 60 && $dias_pasados <=89){
                     $beneficioRetanqueo = $beneficio - (($beneficio * 50)/100);
                  }elseif($dias_pasados >= 90 && $dias_pasados <=110){
                     $beneficioRetanqueo = $beneficio - (($beneficio * 70)/100);
                  }elseif($dias_pasados >= 120){
                     $beneficioRetanqueo = 0;
                  }
                  if($beneficioRetanqueo > 0){
                     $montoRetanqueo = arreglar_prestamo($beneficioRetanqueo);
                     $text = 'Hola '.$nombres.', registramos el pago de tu cuota. Tienes un nuevo Prestamo disponible de $'.$montoRetanqueo.'. Solicitalo ahora aqui solven.me/repare. Solventa';
                   }else{
                        $text = 'Hola '.$nombres.' Registramos el pago de $'.$cuota.' por la cuota de tu Prestamo #'.$nPrestamo.'. Solventa';
                   } 
                   $listado[] = array(
                            'from' => 'Solventa',
                            'to' => '57'.$a->telefono,
                            'text' => $text
                                );
              }          
           }else{
              $persona2 = $this->InfoBipModel->datosPersonaSinRetanqueo($credito);
              foreach ($persona2 as $key => $c) {
                $creditos[] = $c->nPrestamo; 
                $nombres = arreglar_string($c->nombres);
                $cuota = arreglar_prestamo($c->monto_cobrar);
                $nPrestamo = $c->nPrestamo;
                $text = 'Hola '.$nombres.' Registramos el pago de $'.$cuota.' por la cuota de tu Prestamo #'.$nPrestamo.'. Solventa';
                $listado[] = array(
                           'from' => 'Solventa',
                           'to' => '57'.$c->telefono,
                           'text' => $text
                            );  
                }
           }    
        }//var_dump($listado);die();
        if(isset($listado)){
            $response = $this->infobip_library->infobip_curl_mora($listado,"Cliente",'cuotasPagadas',$creditos);   
        }else{
            $response = ['status' => 200, 'data' => 'No hay mensajes para enviar'];
        }
    }else{
       $response = ['status' => 200, 'data' => 'No hay mensajes para enviar2']; 
    } 
    $this->response($response);   
}


public function pruebahora_get(){
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $time = time();
    $tiempo = date("Y-m-d H:i:s"); 
    $minutoAnadir=10; 
    $segundos_horaInicial=strtotime($tiempo); 
    $segundos_minutoAnadir=$minutoAnadir*60; 
    $nuevaHora=date("Y-m-d H:i:s",$segundos_horaInicial-$segundos_minutoAnadir); 
}

 public function WhatsappCampaña($idCliente,$template){
       $post["userID"] = "99999999";
        $post["idCliente"] = $idCliente;
        $post["Template"] = $template;        //"template_44";
        // $header = array("Content-Type: application/json", "Accept: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => base_url()."comunicaciones/TwilioCobranzas/send_template_message",
           // CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_POSTFIELDS => $post
        ));

        $data2 = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $data = $err;
            $response['solicitud'] = "ERROR EN MENSAJE";
            $response['status']['code'] = 400;
            $response['status']['ok'] = false;
        } else {           
            $response['status']['code'] = 200;
            $response['status']['ok'] = TRUE;
            $response['solicitud'] = "MENSAJE ENVIADO";
            $response = ['status' => $response, 'data' => json_decode($data2)];
        }        
    }  


public function retan_get(){
   /* $cliente = 232;
    $retanqueo = $this->InfoBipModel->posibilidadRetanqueo($cliente);
    var_dump($retanqueo);*/
    $desembolsos = $this->InfoBipModel->credito_otorgado();
    var_dump($desembolsos);die();
}

public function conect_curl_get(){
        
        $desembolsos = $this->InfoBipModel->credito_otorgado();
        if(isset($desembolsos)){
            foreach ($desembolsos as $key => $a) {
                if ($a->clientid == false) {
                    $a->clientid = '1561616662.1574979832';
                }
                //https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=1561616662.1574979832&ec=Test&ea=UTMs&cn=test%20campaign&cs=Facebook_test&cm=cpc&ni=1

                if ($a->tipo_solicitud === 'RETANQUEO') {
                    $url = "https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=".$a->clientid."&ec=TestRetanqueo&ea=Test%20Desembolsado&ev=".(int)($a->total_devolver - $a->capital_solicitado)."&el=".$a->tipo_solicitud."";
                    $update = array('informado_google' => 1);
                    $this->db_solicitudes->where('id_solicitud', $a->id);
                    $this->db_solicitudes->update('solicitud_txt', $update);
                    checkDbError($this->db_solicitudes);

                    $ddf = fopen(APPPATH.'logs/conect_curl.log','a');
                    fwrite($ddf,"[".date("Y-m-d H:m:s")."] RETANQUEO URL: $url\r\n");
                    fclose($ddf);
                }else{
                    //  GCLID DEFINIDO
                    if ($a->gclid !== 'undefined' || $a->gclid !== null) {
                        $url = "https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=".$a->clientid."&ec=TestDesembolso&ea=Test%20Desembolsado&ev=".(int)($a->total_devolver - $a->capital_solicitado)."&gclid=".$a->gclid."";echo $url;die();
                        //echo "<br/>caso 1: ".$url;
                        $ddf = fopen(APPPATH.'logs/conect_curl.log','a');
                        fwrite($ddf,"[".date("Y-m-d H:m:s")."] GCLID DEFINIDO URL: $url\r\n");
                        fclose($ddf);
                    }
                    elseif (($a->gclid === 'undefined' || $a->gclid === null) && ( $a->utm_medium !== 'undefined' || $a->utm_medium !== null) && ( $a->utm_source !== 'undefined' || $a->utm_source !== null) && ( $a->utm_campaign !== 'undefined' || $a->utm_campaign !== null)) {
                       $url = "https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=".$a->clientid."&ec=TestDesembolso&ea=Test%20Desembolsado&ev=".(int)($a->total_devolver - $a->capital_solicitado)."&cn=".$a->utm_campaign."&cs=".$a->utm_source."&cm=".$a->utm_medium."";echo $url;die();
                       //echo "<br/>caso 2: ". $url;
                       $ddf = fopen(APPPATH.'logs/conect_curl.log','a');
                       fwrite($ddf,"[".date("Y-m-d H:m:s")."] utm_medium DEFINIDO URL: $url\r\n");
                       fclose($ddf);
                    }
                    elseif (($a->gclid === 'undefined' || $a->gclid === null) && ( $a->utm_medium === 'undefined' || $a->utm_medium === null) && ( $a->utm_source === 'undefined' || $a->utm_source === null) && ( $a->utm_campaign === 'undefined' || $a->utm_campaign === null)) {
                       $url = "https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=".$a->clientid."&ec=TestDesembolso&ea=Test%20Desembolsado&ev=".(int)($a->total_devolver - $a->capital_solicitado)."";
                       echo $url;die();//echo "<br/>caso 3: ". $url;
                       $ddf = fopen(APPPATH.'logs/conect_curl.log','a');
                       fwrite($ddf,"[".date("Y-m-d H:m:s")."] utm_medium Y GCLID INDEFINIDO URL: $url\r\n");
                       fclose($ddf);
                    }
                } 
                $solventa = curl_init();
                curl_setopt($solventa, CURLOPT_URL, $url);
                curl_setopt($solventa, CURLOPT_TIMEOUT, 2);
                curl_setopt($solventa, CURLOPT_RETURNTRANSFER, 0);
                $result = curl_exec($solventa);
                curl_close($solventa);
            }    
        }               
    }

}
