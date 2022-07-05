<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiFilterWhatsApp extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		// Models
		 $this->load->model('chat', 'chat_model', TRUE);
		// Library
		// $this->load->library('Twilio_library');
        $this->load->helper('formato_helper');
	}

public function FilterMessage_post($arraData = null)
{

  if($arraData==null)
  {

    $data=[
      
       
      "documento"=>$this->input->post('documento'), 
      "metodo_notificacion"=>$this->input->post('metodo_notificacion'), //  1 para envio 2 para recepcion
      "tipo_envio"=>($this->input->post('metodo_notificacion')==1)?"SALIENTE":"ENTRANTE", // tipo usuario consumo: (cliente , operador) 
      "usuario"=>($this->input->post('metodo_notificacion')==1)?"operador":"cliente", // tipo usuario consumo: (cliente , operador) 
      "nombre_cliente"=>$this->input->post('nombre_cliente'),
      "numero_client"=>$this->input->post('numero_client'),
    ];

    if(!empty($this->input->post('operador')))
    {
      if($this->input->post('metodo_notificacion')==1)
      {
        $data["operador"] = $this->input->post('operador');
      }
    }

    $origen = $this->input->post('origen');
    $message = $this->input->post('message');

  }else{
    $data=[
      
       
      "documento"=>$arraData['documento'], 
      "metodo_notificacion"=>$arraData['metodo_notificacion'], //  1 para envio 2 para recepcion
      "tipo_envio"=>($arraData['metodo_notificacion']==1)?"SALIENTE":"ENTRANTE", // tipo usuario consumo: (cliente , operador) 
      "usuario"=>($arraData['metodo_notificacion']==1)?"operador":"cliente", // tipo usuario consumo: (cliente , operador) 
      "nombre_cliente"=>$arraData['nombre_cliente'],
      "numero_client"=>$arraData['numero_client'],
    ];

    if(!empty($arraData['operador']))
    {
      if($this->input->post('metodo_notificacion')==1)
      {
        $data["operador"] = $arraData['operador'];
      }
    }
    $origen = $arraData['origen'];
    $message = $arraData['message'];

  }

  
  //  print_r($message);die;
    //    pasar a minisculas para la evaluacion
       $filtrar = search_word($message,$origen);
       // str_replace para quitar por espacios los signos de puntuacion ,!? etc 
      $msgsinespacios = procesartexto($message);

      $arraySoloPalabras= [];
      $arraySoloGrupo= [];
     
      foreach ($filtrar as $key => $value) {
        $arraySoloGrupo[$value['palabra']]=$value['grupo'];
        

      }
      // var_dump($arraySoloGrupo);die;
        $arraySoloGrupo[$value['palabra']]=$value['grupo'];
        $arraymatch = array_intersect_key($arraySoloGrupo, array_flip($msgsinespacios));
       
        $toStringWords = (json_encode(array_keys($arraymatch)));
        $toString = (json_encode(array_values($arraymatch)));
        
        $signos = array("{","[","}","]");
        $grupos = str_replace($signos, "", strtolower($toString));
        $words = str_replace($signos, "", strtolower($toStringWords));
        // var_dump($words,$grupos);die;
          
          $rs_grupo= $this->chat_model->consultar_info_grupo($grupos,$origen);
          // var_dump($grupos);
          $bandera = false;
          foreach ($rs_grupo as $key => $value2) 
          {
            $arrayPalabrasNew= [];
            foreach ($arraymatch as $palabras => $grupos) {

              if ($value2['id_grupo_notificacion'] ==  $grupos)
              {
                $arrayPalabrasNew[] = $palabras;
              }
            }
            $palabrasNew= json_encode($arrayPalabrasNew);
            $palabrasNewString = str_replace($signos, "", strtolower($palabrasNew));
           
            $flag_notificacion= false;
                $notifi_parse= explode(",",$value2['metodo_notificacion']);
                foreach ($notifi_parse as $key => $value3) {
                  // var_dump($value3 == $data["metodo_notificacion"]);
                      if ($value3 == $data["metodo_notificacion"]) {
                        $flag_notificacion=true;
                      }
                     
                }
                if ($value2['action'] != "send") {
                  $bandera = true;
                }
                if ($flag_notificacion) {
                    /***********************************************************************************************************************************************************/
                    /*  lasiguiente estructura debera estar fijada en la base para que nuestro metodo no rompa a la hora de realizar la lectura y reescritura correspondiente
                    /*  Mensaje de notificacion automatica el {USUARIO} {CLIENTE} esta usando la palabra {PALABRA} en la conversacion {NUMERO_CLIENT}
                    /***********************************************************************************************************************************************************/
                    
                    $resultado = str_replace("{TIPO_ENVIO}", "".$data['tipo_envio']."", $value2['mensaje_notificar']);
                    if($this->input->post('metodo_notificacion')==2){
                      $resultado = str_replace("*Operador:* {OPERADOR}", "", $resultado);
                    }else{
                      $resultado = str_replace("{OPERADOR}", "".$data['operador']."", $resultado);

                    }
                    $resultado = str_replace("{ORIGEN}", "".$origen."", $resultado);
                    $resultado = str_replace("{DOCUMENTO}", "".$data['documento']."", $resultado);
                    $resultado = str_replace("{CLIENTE}", "".$data['nombre_cliente']."", $resultado);
                    $resultado = str_replace("{PALABRA_MENSAJE}",""."*".$palabrasNewString."*"."", $resultado);
                    $resultado = str_replace("{NUMERO_CLIENT}", "".$data['numero_client']."", $resultado);
                    $resultado = str_replace("{MENSAJE}", "".$message."", $resultado);
                   
                    $value2['mensaje_notificar'] = $resultado;
                          $data_track = 
                          [
                            "id_grupo_notificacion" => $value2['id_grupo_notificacion'],
                            "nombre_grupo" => $value2['nombre_grupo'],
                            "notificados_slack" => $value2['notificados_slack'],
                            "notificados_correos" => $value2['notificados_correos'],
                            "notificados_sms" => $value2['notificados_sms'],
                            "mensaje_filtrado" => $message,
                            "mensaje_notificar" => $value2['mensaje_notificar'],
                            "medios_notificacion" => $value2['medios_notificacion'],
                            "metodo_notificacion" => $value2['metodo_notificacion'],
                            "operador" => (!empty($data['operador']))?$data['operador']:null,
                            "cliente" => (!empty($data['nombre_cliente']))?$data['nombre_cliente']:null,
                            "num_documento" => (!empty($data['documento']))?$data['documento']:null,
                            "num_contacto" => (!empty($data['numero_client']))?$data['numero_client']:null,
                            "palabras" => (!empty($palabrasNewString))?$palabrasNewString:null,
                            "action" => $value2['action'],
                            "origen" => $origen,
                            "fecha_notificacion" => date("Y-m-d H:i:s"),
                          ];
                          $this->chat_model->insert_track_notificaciones($data_track);  

                    if (!empty($value2['notificados_slack'])) {
                      // var_dump($value2);die;
                      $response = $this->send_slack($value2);

                    }else if (!empty($value2['notificados_correos'])) {

                      $response = $this->send_mail($value2);

                    }else if (!empty($value2['notificados_sms'])) {
                      
                      $response = $this->send_sms($value2);
                    }
                }
           }

           if ($bandera == true) {
             $response['code']  = 400;
             $response['ok']    = FALSE;
             $response['resp']  = "Accion Rechazada";
          }else{
              $response['code']  = 200;
              $response['ok']    = TRUE;
              $response['resp']  = "Accion permitida";

           }
           return $response;
}

public function send_slack($data)
{
  

        $params = array(
            'to' => $data['notificados_slack'],
            'msg' => $data['mensaje_notificar'],
        );
    

  $end_point = URL_CAMPANIAS."ApiSlackSendMessage";
  $curl = curl_init();
  curl_setopt_array($curl, array(
          CURLOPT_URL => $end_point,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_VERBOSE => 1,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_POSTFIELDS => $params
  ));

  $data2 = curl_exec($curl);
  // var_dump($data2);die;
  $err   = curl_error($curl);
  curl_close($curl);
  if ($err){
      
      $response['code']  = 200;
      $response['ok']    = FALSE;
      $response['resp']  = "Archivo no enviado por slack ".$err;
  }else{
      $response['code']  = 200;
      $response['ok']    = TRUE;
      $response['resp']  = "Archivo enviado por slack";
  }
    
    return $response;

}
    public function send_new_message_post()
    {


      $message = $this->input->post('message');
      $chatID = $this->input->post('chatID');
      $operatorID = $this->input->post('operatorID');
      $metodo_notificacion = $this->input->post('metodo_notificacion');
      $origen = $this->input->post('origen');

      $rs_operador = $this->chat_model->consult_operadores($operatorID);
      if($rs_operador != 0  )
      {
                    

            //    pasar a minisculas para la evaluacion
            $filtrar = search_word($message,$origen);
            // str_replace para quitar por espacios los signos de puntuacion ,!? etc 
            $msgsinespacios = procesartexto($message);
            
            $arraySoloPalabras= [];
            $arraySoloGrupo= [];
            
            foreach ($filtrar as $key => $value) {
              $arraySoloGrupo[$value['palabra']]=$value['grupo'];
              
              
            }
            $arraymatch = array_intersect_key($arraySoloGrupo, array_flip($msgsinespacios));
            if(!empty($arraymatch))
            {
              $toStringWords = (json_encode(array_keys($arraymatch)));
                  $toString = (json_encode(array_values($arraymatch)));
                  $signos = array("{","[","}","]");
                  $grupos = str_replace($signos, "", strtolower($toString));
                  $words = str_replace($signos, "", strtolower($toStringWords));
        
                  $rs_grupo= $this->chat_model->consultar_info_grupo($grupos,$origen);
                  $bandera = false;
                  foreach ($rs_grupo as $key => $value2) 
                  {
                    $flag_notificacion= false;
                        $notifi_parse= explode(",",$value2['metodo_notificacion']);
                        foreach ($notifi_parse as $key => $value3) {
                          // var_dump($value3 == $data["metodo_notificacion"]);
                              if ($value3 == 1) {
                                $flag_notificacion=true;
                              }
                            
                        }
                        if ($value2['action'] != "send") {
                          $bandera = true;
                        }
                        
                  }
        
                  if ($bandera == true) {
                        
                        $rs_consult = $this->chat_model->search_data_chat($chatID,$operatorID);
                        $arrayData = array(
                          'message' => $message,
                          'metodo_notificacion' => $metodo_notificacion,
                          'operador' => $rs_consult[0]['operador'],
                          "nombre_cliente" => $rs_consult[0]['nombre_cliente'],
                          "numero_client" => $rs_consult[0]['numero_client'],
                          "documento" => $rs_consult[0]['documento'],
                          "origen" => $origen
                        );
                        // var_dump($arrayData);die;
                        if($rs_consult != 0 ){
                            $response = $this->FilterMessage_post($arrayData);
                        }   


                  }else{
                      $response['code']  = 200;
                      $response['ok']    = TRUE;
                      $response['resp']  = "Accion permitida";
                      $response['palabras']  = $words;
        
                  }
            }else{
              $response['code']  = 404;
              $response['ok']    = FALSE;
              $response['resp']  = "Palabra no encontrada" ;
            }
      }else{
        $response['code']  = 200;
        $response['ok']    = TRUE;
        $response['resp']  = "Accion permitida operador omitido";
      }
      
            
             $this->response($response);


    }


    public function get_new_message_post()
    {


      $message = $this->input->post('Body');
      $from = $this->input->post('telfNum');
      $origen = $this->input->post('origen');
      $metodo_notificacion = $this->input->post('metodo_notificacion');
      // var_dump($metodo_notificacion);die;
          
            
            //    pasar a minisculas para la evaluacion
            $filtrar = search_word($message,$origen);
            // str_replace para quitar por espacios los signos de puntuacion ,!? etc 
            $msgsinespacios = procesartexto($message);
            
            $arraySoloPalabras= [];
            $arraySoloGrupo= [];
            
            foreach ($filtrar as $key => $value) {
              $arraySoloGrupo[$value['palabra']]=$value['grupo'];
              
              
            }
            $arraymatch = array_intersect_key($arraySoloGrupo, array_flip($msgsinespacios));
            if(!empty($arraymatch))
            {
              $toStringWords = (json_encode(array_keys($arraymatch)));
                  $toString = (json_encode(array_values($arraymatch)));
                  $signos = array("{","[","}","]");
                  $grupos = str_replace($signos, "", strtolower($toString));
                  $words = str_replace($signos, "", strtolower($toStringWords));
                  $rs_grupo= $this->chat_model->consultar_info_grupo($grupos,$origen);
                  // print_r($rs_grupo);die;
                  $bandera = false;
                  foreach ($rs_grupo as $key => $value2) 
                  {
                    $flag_notificacion= false;
                        $notifi_parse= explode(",",$value2['metodo_notificacion']);
                        foreach ($notifi_parse as $key => $value3) {
                          // var_dump($value3 );
                              if ($value3 == 2) {
                                $flag_notificacion=true;
                              }
                            
                        }
                        
                        if ($value2['action'] != "send") {
                          $bandera = true;
                        }
                        
                  }
                  // var_dump($flag_notificacion);
                  // die;
                  if ($flag_notificacion == true) {
                    $numClean = substr($from,3);
                    // var_dump($numClean,$from);die;
                    $rs_consult = $this->chat_model->search_data_agenda_num($numClean);
                    //  var_dump($rs_consult);die;
                        $arrayData = array(
                          'message' => $message,
                          'metodo_notificacion' => $metodo_notificacion,
                          "nombre_cliente" => $rs_consult[0]['contacto'],
                          "numero_client" =>  $from,
                          "documento" => $rs_consult[0]['documento'],
                          "origen" =>  $origen

                        );
                        //  var_dump($arrayData);die;
                        $response = $this->FilterMessage_post($arrayData);
                         


                  }else{
                      $response['code']  = 200;
                      $response['ok']    = TRUE;
                      $response['resp']  = "Accion permitida";
                      $response['palabras']  = $words;
        
                  }
            }else{
              $response['code']  = 404;
              $response['ok']    = FALSE;
              $response['resp']  = "Palabra no encontrada" ;
            }
     
      
            
             $this->response($response);


    }

    public function send_sms($data)
    {

    }
    public function send_mail($data)
    {

    }

    
}