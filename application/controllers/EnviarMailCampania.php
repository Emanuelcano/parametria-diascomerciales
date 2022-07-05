<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

   
class EnviarMailCampania extends REST_Controller {

    public function __construct($config = 'rest')
    {
       // header("Access-Control-Allow-Origin: *");
       // header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
       // header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
       
        parent::__construct(); 
        $this->load->model('Modulos_m','',TRUE);
	    $this->load->model('InfoBipModel','',TRUE);
        $this->load->helper(array('form', 'url','formato'));
        $this->load->config('form_validation');
        $this->load->library('form_validation');

    }


    //PEPIPOST
    public function EnviarCampaniasMail_post(){ 
       $tipo = "nuevaMora"; 
       $datosVerificacion = $this->InfoBipModel->clientesMora($tipo); 
       $this->InfoBipModel->insertarMora($datosVerificacion, $tipo);       
       $datos = $this->InfoBipModel->nuevaCampaniaMoraQuincenal($tipo);
        $i = 0;       
        foreach($datos as $key => $a) {
            if($i < 6){
             if (ENVIRONMENT == 'development') {
                switch($i){
                    case 0: $email = "francol.campioni@gmail.com";
                    break;
                    case 1: $email = "desarrollo01@solventa.com.ar";
                    break;
                    case 2: $email = "franco.campioni@solventa.com.ar";
                    break;
                    case 3: $email = "berterimauro@gmail.com";
                    break;
                    case 4: $email = "belen.museri@solventa.com.ar";
                    break;
                    case 5: $email = "marianelaasuarez@gmail.com";
                    break;
                }
                //$email = TEST_EMAIL;
            } else {
                $email = $a->email; 
            }
            $vencimiento = $a->vencimiento;
         $dia = $this->saber_dia($a->vencimiento);
         $diaActual = $this->saber_dia(date("Y-m-d"));
        $fechaActual = date("d/m/Y", strtotime(date("Y-m-d"))); 
        $fActualCortado = substr($fechaActual, 0, 5);
        $vencimiento = date("d/m/Y", strtotime($vencimiento));
        $vencimientoCortado = substr($vencimiento, 0, 5);
        $prestamo = $a->monto;
        $prestamo = $this->arreglar_prestamo($prestamo);     
        $nombres = $a->nombres;
        $nombres = sanear_string($nombres);
        $trozos = explode(" ", $nombres);
        $nombres = ucwords(strtolower($trozos[0]));
        $apellidos = $a->apellidos;
        $apellidos = sanear_string($apellidos);
        $trozos2 = explode(" ", $apellidos);
        $apellidos = ucwords(strtolower($trozos2[0])); 
           $per[] = array(
                'recipient' => $email,
                "attributes" => array(
                    "FNAME" => $nombres,
                    "LNAME" => $apellidos,
                    "NPREST" => $a->id_credito,                     
                    "MONTO" => $prestamo,
                    "CAMPANA" => $tipo,
                    "FVEN" => $vencimiento,
                    "FVENCOR" => $vencimientoCortado,
                    "DIAACTUAL" => $diaActual,
                    "DACTUAL" => $fActualCortado,
                    "DIA" => $dia,                     
                ),
                'x-apiheader' => $tipo.',21172',
            );
            $data = array(                
            'personalizations' => $per,
            'from' => array(
                'fromEmail' => 'info@solventa.co',
                'fromName' => 'SOLVENTA'
            ),
            'subject' => 'Mail de Prueba',
            'content' => '%20',
            'templateId' => 21172,
            'settings' => array(
                'clicktrack' => 1,
                'opentrack' =>1
            )
        );
            $i++;
        }
        }          
        $a = json_encode($data,true);

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.pepipost.com/v2/sendEmail",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $a,
        CURLOPT_HTTPHEADER => array(
        "api_key: f9a5f6fe35da420771aab0c95fd9910a",
        "content-type: application/json"
      ),
    ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }

    }

public function saber_dia($fecha) {
  $fecha = substr($fecha, 0, 10);
  $dia = date('l', strtotime($fecha));
  $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
  $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $nombredia = str_replace($dias_EN, $dias_ES, $dia);
  return $nombredia;
}

    //SENDGRID
    public function EnvioMailVerificacion_post(){ 

     $id  = $_REQUEST['id_solicitud'];          
     $datos = $this->InfoBipModel->verificacionMail($id);
     if(!empty($datos)){     
       foreach($datos as $key => $a) {  
           $fecha = $a->fecha;
           $hora = $a->hora;
           $fechaNueva = date("d/m/Y", strtotime($fecha));
           $nombres = $a->nombres;
           $nombres = sanear_string($nombres); 
           $trozos = explode(" ", $nombres);
           $nombres = ucwords(strtolower($trozos[0]));
           $text = "mailto:analisis.fraude@solventa.com?subject=IMPORTANTE%20alerta%20solicitud%20erronea&body=Hola%0D%0DMi%20nombre%20completo%20es:%0D%0DEste%20correo%20no%20pertenece%20a%20".$nombres."%0DSaludos.";         
           $apellidos = $a->apellidos;
           $apellidos = sanear_string($apellidos);          
           $trozos = explode(" ", $apellidos);
           $apellidos = ucwords(strtolower($trozos[0]));
           $prestamo = $a->monto;
           $prestamo = $this->arreglar_prestamo($prestamo); 
           if (ENVIRONMENT == 'development') {
                $email = TEST_EMAIL;
            } else {
                $email = $a->email; 
            }  
        $message = '.';
        $localIp = gethostbyname(gethostname());
        $url_api_medios_de_pago = URL_SEND_MAIL.'api/sendmail';   //Produccion
        // if it is a multipart forma data form
        $body = array (
            //"jwt" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzEyNjE4MDcsImV4cCI6MTU3MTI2NTQwNywiZGF0YSI6eyJpZCI6IjEiLCJhZG1pbiI6dHJ1ZSwidGltZSI6MTU3MTI2MTgwNywidGltZVRvbGl2ZSI6bnVsbH19.EjL-hI9PKhF9p84Id425mdYHo0LmQINtW8MrKpXFX5U",
            "jwt" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzMxNjExNjgsImV4cCI6MTU3MzE2NDc2OCwiZGF0YSI6eyJpZCI6IjYyNTgiLCJhZG1pbiI6ZmFsc2UsInRpbWUiOjE1NzMxNjExNjgsInRpbWVUb2xpdmUiOm51bGx9fQ.gsGPp2FXAk4I7KEXPNuleh6kqYP5ahWYud-baZBpOFE",
            "from" => "analisis.fraude@solventa.com.ar",                 //DESDE DONDE
            'to' => $email,
            'from_name' => 'Solventa SAS',
            'subject' => 'Nueva solicitud de Préstamo',
            'template' => 7,
            'message' => $message,
            'name' => $nombres,
            'lastname' => $apellidos,
            'amount_request' => $prestamo, 
            'date' => $fechaNueva,
            'time' => $hora,
            'link' => $text,
            'id_solicitud' => $id,
        );
        $headers = array('Content-Type' => 'multipart/form-data');
        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });
        try{
        $response = Requests::post($url_api_medios_de_pago, $headers, array(), array('hooks' => $hooks));     
        $respuesta[] = ['email'=> $email, 'status' => json_decode($response->body)];
        }
        catch(Exception $e){
            $status = parent::HTTP_BAD_REQUEST;
            $respuesta['status']['code'] = $status;
            $respuesta['error'] = "Muchos reintentos fallidos";
            $respuesta['status']['ok'] = false;
        }
    }
        //$respuestas[] = $response;
       
    } 
    else{
        $status = parent::HTTP_BAD_REQUEST;
            $respuesta['status']['code'] = $status;
            $respuesta['error'] = "La persona no tiene emails comprobados en el buro";
            $respuesta['status']['ok'] = false;            
    }   
        $this->response($respuesta);
    //[$response->body]
 
    //foreach($respuestas as $a)
	//{
     //   $res[] = $a->body;
	//}
   //     $this->response(json_encode($res));
   
   }

   public function arreglar_prestamo($prestamo){         
            $pos = strpos($prestamo,'.');
            if($pos === false){
                if (strlen($prestamo) > 6) {
                $pri = substr($prestamo, 0, strlen($prestamo) - 6);
                $seg = substr($prestamo, strlen($prestamo) - 6, 6);
                $prestamo = $pri . "." . $seg;
                }
                $pri = substr($prestamo, 0, strlen($prestamo) - 3);
                $seg = substr($prestamo, strlen($prestamo) - 3, 3);
                $prestamo = $pri . "." . $seg;
            }
            else{
            $trozosPrestamo = explode(".", $prestamo);
            $parte = $trozosPrestamo[0];

            if (strlen($parte) > 6) {
                $pri = substr($parte, 0, 1);
                $seg = substr($parte, 1, strlen($parte) - 4);
                $ter = substr($parte, strlen($parte) - 3, 6);
                $parte = $pri . "." . $seg . "." . $ter;
            }
            else{
            $pri = substr($parte, 0, strlen($parte) - 3);
            $seg = substr($parte, strlen($parte) - 3, 3);
            $parte = $pri . "." . $seg;
            }
            $coma = substr($trozosPrestamo[1], 0, 2);
            $prestamo = $parte . ",". $coma;
            }
            return $prestamo;
    }

   //SENDGRID
    public function EnvioMailDisculpa_post(){ 
  
     $datos = $this->InfoBipModel->disculpaCampania('moraSms');
     if(!empty($datos)){          
       foreach($datos as $key => $a) {
            if (ENVIRONMENT == 'development') {
                $email = 'francol.campioni@gmail.com';
            } else {
                $email = $a->email; 
            }  
                 
        $message = '.';
        $localIp = gethostbyname(gethostname());
        $url_api_medios_de_pago = URL_SEND_MAIL.'api/sendmail';   //Produccion
        // if it is a multipart forma data form
        $body = array (
            //"jwt" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzEyNjE4MDcsImV4cCI6MTU3MTI2NTQwNywiZGF0YSI6eyJpZCI6IjEiLCJhZG1pbiI6dHJ1ZSwidGltZSI6MTU3MTI2MTgwNywidGltZVRvbGl2ZSI6bnVsbH19.EjL-hI9PKhF9p84Id425mdYHo0LmQINtW8MrKpXFX5U",
            "jwt" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzMxNjExNjgsImV4cCI6MTU3MzE2NDc2OCwiZGF0YSI6eyJpZCI6IjYyNTgiLCJhZG1pbiI6ZmFsc2UsInRpbWUiOjE1NzMxNjExNjgsInRpbWVUb2xpdmUiOm51bGx9fQ.gsGPp2FXAk4I7KEXPNuleh6kqYP5ahWYud-baZBpOFE",
            "from" => "hola@solventa.com",                 //DESDE DONDE
            'to' => $email,
            'from_name' => 'Solventa SAS',
            'subject' => 'IMPORTANTE - Error de notificación',
            'message' => '<html><body><span>
                          Hola,<br>
                          Recientemente, algunos de nuestros usuarios recibieron una alerta de mora en el pago de su Préstamo.<br>
                          Si usted recibió el mensaje y todavía no venció su Préstamo o este mismo no se encuentra en mora, por favor ignorar el mensaje.<br>
                          Disculpe las molestias.<br>
                          Que tenga un buen día.<br>
                          Saludos,<br>
                          Equipo Solventa.
                          </span></body></html>',
        );
        $headers = array('Content-Type' => 'multipart/form-data');
        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });
        try{
        $response = Requests::post($url_api_medios_de_pago, $headers, array(), array('hooks' => $hooks));     
        $respuesta[] = ['email'=> $email, 'status' => json_decode($response->body)];
        }
        catch(Exception $e){
            $status = parent::HTTP_BAD_REQUEST;
            $respuesta['status']['code'] = $status;
            $respuesta['error'] = "Muchos reintentos fallidos";
            $respuesta['status']['ok'] = false;
        }
    }
        //$respuestas[] = $response;
       
    } 
    else{
        $status = parent::HTTP_BAD_REQUEST;
            $respuesta['status']['code'] = $status;
            $respuesta['error'] = "La persona no tiene emails comprobados en el buro";
            $respuesta['status']['ok'] = false;            
    }   
        $this->response($respuesta);
   
   }

   
}
