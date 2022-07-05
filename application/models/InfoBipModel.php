<?php
class InfoBipModel extends CI_Model
{

  public function __construct()
  {
    $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
    $this->db_maestro = $this->load->database('maestro', TRUE);
    $this->db_usuarios = $this->load->database('usuarios_solventa', TRUE);
    $this->db_buros = $this->load->database('api_buros', TRUE);
    $this->db_gestion = $this->load->database('gestion', TRUE);
    $this->db_campania = $this->load->database('campanias', TRUE);
    $this->load->helper(array('formato_helper','form', 'url','my_date','formato'));
    $this->load->library('Sqlexceptions');
    $this->Sqlexceptions = new Sqlexceptions();
  }

  function cambiarFecha($fecha)
  {

    $str = str_replace("T", " ", $fecha);
    $str = explode("+", $str, 1);
    $str = implode("", $str);
    $str =  substr($str, 0, 19);
    return $str;
  }

  public function verificacionSms($id)
  {
   /* $this->db_solicitudes->select('solicitud.id, solicitud.telefono, '.$this->db_solicitudes->database . '.solicitud_condicion_desembolso.capital_solicitado as monto, solicitud.nombres as nombres, solicitud.apellidos as apellidos');
    $this->db_solicitudes->from('solicitud');
   $this->db_solicitudes->join($this->db_solicitudes->database . '.solicitud_condicion_desembolso',$this->db_solicitudes->database.'.solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'inner');
    $this->db_solicitudes->where('solicitud.id =', $id);
    $query = $this->db_solicitudes->get()->result();*/
    //$query = $this->db_solicitudes->get_compiled_select();
    //echo $query; 
    $rechazados = $this->telefonosRechazados();

    $this->db_buros->select($this->db_solicitudes->database.'.solicitud.telefono');
    $this->db_buros->from($this->db_solicitudes->database.'.solicitud');
    $this->db_buros->where($this->db_solicitudes->database.'.solicitud.id =',$id);
    $subQuery3 = $this->db_buros->get_compiled_select();
    

   $this->db_buros->select('datacredito2_reconocer_celular.celular,' . $this->db_solicitudes->database . '.solicitud.nombres as nombres,' . $this->db_solicitudes->database . '.solicitud.apellidos as apellidos, ' . $this->db_solicitudes->database . '.solicitud.documento,' . $this->db_solicitudes->database . '.solicitud.documento as documento,'.$this->db_solicitudes->database . '.solicitud_condicion_desembolso.capital_solicitado as monto');
    $this->db_buros->from('datacredito2_reconocer_celular');
    $this->db_buros->join($this->db_buros->database . '.datacredito2_reconocer_naturalnacional',$this->db_buros->database.'.datacredito2_reconocer_naturalnacional.id = datacredito2_reconocer_celular.IdConsulta', 'inner');
    $this->db_buros->join($this->db_solicitudes->database . '.solicitud', $this->db_solicitudes->database . '.solicitud.documento = '.$this->db_buros->database.'.datacredito2_reconocer_naturalnacional.identificacion', 'inner');
    $this->db_buros->join($this->db_solicitudes->database . '.solicitud_condicion_desembolso',$this->db_solicitudes->database.'.solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'inner');
    $this->db_buros->where($this->db_solicitudes->database . '.solicitud.id =', $id);
    $this->db_maestro->where_not_in('datacredito2_reconocer_celular.celular', $rechazados);
    $this->db_buros->where("datacredito2_reconocer_celular.celular not in ($subQuery3)", null, false);
    //$query = $this->db_buros->get_compiled_select();
    //echo $query;
    $query = $this->db_buros->get()->result();
    return $query;
  }
  
  public function verificacionMail($id)
  {

 /*   $this->db_solicitudes->select('solicitud.id, solicitud.email, DATE(solicitud.fecha_alta) as fecha, '.$this->db_solicitudes->database . '.solicitud_condicion_desembolso.capital_solicitado as monto, TIME_FORMAT(TIME(solicitud.fecha_alta), "%H:%i") as hora, solicitud.nombres as nombres, solicitud.apellidos as apellidos');
    $this->db_solicitudes->from('solicitud');
    $this->db_solicitudes->join($this->db_solicitudes->database . '.solicitud_condicion_desembolso',$this->db_solicitudes->database.'.solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'inner');
    $this->db_solicitudes->where('solicitud.id =', $id);*/
    //$query = $this->db_solicitudes->get()->result();  
    //return $query;
    $this->db_buros->select($this->db_solicitudes->database.'.solicitud.email');
    $this->db_buros->from($this->db_solicitudes->database.'.solicitud');
    $this->db_buros->where($this->db_solicitudes->database.'.solicitud.id =',$id);
    $subQuery3 = $this->db_buros->get_compiled_select();

    $this->db_buros->select('datacredito2_reconocer_email.email,'.$this->db_solicitudes->database . '.solicitud_condicion_desembolso.capital_solicitado as monto,' . $this->db_solicitudes->database . '.solicitud.nombres as nombres, DATE(' . $this->db_solicitudes->database . '.solicitud.fecha_alta) as fecha, TIME_FORMAT(TIME(' . $this->db_solicitudes->database . '.solicitud.fecha_alta), "%H:%i") as hora,' . $this->db_solicitudes->database . '.solicitud.apellidos as apellidos');
    $this->db_buros->from('datacredito2_reconocer_email');
    $this->db_buros->join($this->db_buros->database . '.datacredito2_reconocer_naturalnacional', $this->db_buros->database . '.datacredito2_reconocer_naturalnacional.id = datacredito2_reconocer_email.IdConsulta', 'inner');
    $this->db_buros->join($this->db_solicitudes->database . '.solicitud', $this->db_solicitudes->database . '.solicitud.documento = '.$this->db_buros->database.'.datacredito2_reconocer_naturalnacional.identificacion', 'inner');
      $this->db_buros->join($this->db_solicitudes->database . '.solicitud_condicion_desembolso',$this->db_solicitudes->database.'.solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'inner');
    $this->db_buros->where($this->db_solicitudes->database . '.solicitud.id =', $id);
    $this->db_buros->where("datacredito2_reconocer_email.email not in ($subQuery3)", null, false);
    //$query = $this->db_solicitudes->get_compiled_select();
    $query = $this->db_buros->get()->result();
    return $query;
    //echo $query;*/
  }

  public function solicitantes($id)
  {
    $this->db_solicitudes->select('solicitud.id, solicitud.telefono, solicitud.documento');
    $this->db_solicitudes->from('solicitud');   
    $this->db_solicitudes->where('solicitud.id =', $id);
    $query = $this->db_solicitudes->get()->result();
    //$query = $this->db_solicitudes->get_compiled_select();
    //echo $query;
    return $query;
  //CAMBIAR NOMBRES DE BASE DE DATOS DE USUARIOS EN MODELO Y CONFIG/DATABASE
 }

 public function celulares(){
    $this->db_maestro->select('numero');
    $this->db_maestro->from('celulares');
    $this->db_maestro->where('estado =', '0');
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;
    $query = $this->db_maestro->get()->result();
    return $query;
 }

 public function actualizarEstadoCelulares($telefono){
    $this->db_maestro->set('estado', '1');
    $this->db_maestro->where('numero', $telefono);
    $this->db_maestro->update('celulares');
 }


  public function insertarMensaje($data)
  {
    $data = json_decode($data, true);
    foreach ($data as $value) {
      foreach ($value as $a) {
        $fecha = $this->cambiarFecha($a['receivedAt']);
        $id = $a['messageId'];
        $to = $a['to'];
        $from = $a['from'];
        $text = $a['text'];
        $cleanText = $a['cleanText'];
        $keyword = $a['keyword'];
      }
    }
    $this->db_maestro->query("INSERT INTO mensajes (idMensaje, numeroCliente, numeroEmpresa, fecha_recibido, mensajeCompleto, mensajeLimpio, palabraClave) values ('$id','$to','$from','$fecha','$text','$cleanText','$keyword')");
  }

   public function insertarMailsBulk($data) //FALTA CREAR BIEN EL INSERT Y LA BASE DE DATOS
  {
    $data = json_decode($data, true);
    
    foreach ($data as $value =>$a) {
        
        $id = $a['TRANSID'];
        $mail = $a['EMAIL'];
        $valor = $a['X-APIHEADER'];
        $trozos = explode(",", $valor);
        $tipo_campania = $trozos[0];
        $template = $trozos[1];
        if(isset($trozos[2])){
          $credito = $trozos[2];
        }else{
          $credito = 0;
        }
        $fecha = date('Y-m-d H:i:s', $a['TIMESTAMP']);
        $evento = $a['EVENT'];
        $text = $a['RESPONSE'];
      $this->db_maestro->query("INSERT INTO respuesta_envios_mails (idMail, campania, template, email, respuesta, evento, fecha, credito) values ('$id', '$tipo_campania', '$template','$mail','$text','$evento','$fecha', '$credito')");
     }   
  }

public function insertarMailIndividual($data) //FALTA CREAR BIEN EL INSERT Y LA BASE DE DATOS
  {
    $a = json_decode($data, true); 
   
        $id = $a['TRANSID'];
        $mail = $a['EMAIL'];
        $fecha = date('Y-m-d H:i:s', $a['TIMESTAMP']);
        $evento = $a['EVENT'];
        $text = $a['RESPONSE'];
      $this->db_maestro->query("INSERT INTO respuesta_envios_mails (idMail, email, respuesta, evento, fecha) values ('$id','$mail','$text','$evento','$fecha')");
       
  }

public function insertarSmsNrs($dataDeco,$creditos,$mensaje){ 
 if(isset($dataDeco['result'])){
  foreach ($dataDeco['result'] as $a) {
      $telefono = $a['to'];
      $id = $a['id'];
      $telefono = substr($a['to'], 2);
      foreach ($creditos as $key => $credito) {
        $this->db_maestro->select('clientes.documento');
        $this->db_maestro->from('clientes');            
        $this->db_maestro->join($this->db_maestro->database.'.agenda_telefonica', $this->db_maestro->database.'.agenda_telefonica.id_cliente = clientes.id', 'inner');      
        $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id_cliente = clientes.id', 'inner');
        $this->db_maestro->where($this->db_maestro->database.'.agenda_telefonica.numero =', $telefono);
        $this->db_maestro->where($this->db_maestro->database.'.creditos.id =', $credito);
        $query = $this->db_maestro->get()->result();
        if(isset($query)){
          foreach ( $query as $clave => $q ) {
            $documento =  $q->documento; 
            $campana = 'moraSms';
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $date = date("Y-m-d H:i:s"); 
            $this->db_gestion->query("INSERT INTO respuesta_envios_nrs (campana, id_mensaje, documento, telefono, credito, fecha, mensaje) values ('$campana','$id','$documento','$telefono','$credito', '$date', '$mensaje')"); 
          }
        }
      }    
    }     
  }
}

  public function insertarRespuestaConTexto($data, $dni, $text)
  {
    foreach ($data as $value) {
      foreach ($value as $a) {
        $messageId = $a['messageId'];
        $to = $a['to'];
        $respuestaEnvio = $a['status']['name'];
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $date = date("Y-m-d H:i:s"); 
      }
    }
    $this->db_maestro->query("INSERT INTO respuesta_envios_sms (id_mensaje, mensaje, documento, telefono, respuesta_envio, fecha) values ('$messageId', '$text', '$dni','$to','$respuestaEnvio', '$date')");
  }


  public function insertarRespuesta($data, $dni, $text)
  {

    foreach ($data as $value) {
      foreach ($value as $a) {
        $messageId = $a['messageId'];
        $to = $a['to'];
        $respuestaEnvio = $a['status']['name'];
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $date = date("Y-m-d H:i:s"); 
      }
    }
    $this->db_maestro->query("INSERT INTO respuesta_envios_sms (id_mensaje, documento, telefono, respuesta_envio, fecha) values ('$messageId','$dni','$to','$respuestaEnvio', '$date')");
  }

  public function insertarRespuestasMultiples($dataCod,$data,$tipoPersona,$campania)
  {
    
    foreach ($dataCod['messages'] as $a) { 
    $documento = 0;
      foreach ($data as $t) { 
           $n1 = $t['to']; $n2 = '+'.$a['to'];
        if( $n1 == $n2 ){
          
          $resultado = substr($a['to'], 2);
          if($tipoPersona == "Cliente"){
            $this->db_maestro->select('clientes.documento');
            $this->db_maestro->from('clientes');            
            $this->db_maestro->join($this->db_maestro->database.'.agenda_telefonica', $this->db_maestro->database.'.agenda_telefonica.id_cliente = clientes.id', 'inner');
            $this->db_maestro->where($this->db_maestro->database.'.agenda_telefonica.numero =', $resultado);
            $query = $this->db_maestro->get()->result();
            if(isset($query)){
              foreach ( $query as $clave => $q ) {
                $documento =  $q->documento;
              }
            }
            else{
              $documento = "No";
            }  
          }elseif($tipoPersona == "Solicitantes"){
            $this->db_solicitudes->select('solicitud.documento');
            $this->db_solicitudes->from('solicitud');
            $this->db_solicitudes->where('solicitud.telefono =', $resultado);
            $query = $this->db_solicitudes->get()->result();
            if(isset($query)){
              foreach ( $query as $clave => $q ) {
                $documento =  $q->documento;
              }
            }
            else{
              $documento = "No";
            }    
          }
          date_default_timezone_set('America/Argentina/Buenos_Aires');
          $date = date("Y-m-d H:i:s"); 
          $messageId = $a['messageId'];
          $to = $a['to'];      
          $respuestaEnvio = $a['status']['name'];
          $texto = $t['text'];
          $this->db_maestro->query("INSERT INTO respuesta_envios_sms (id_mensaje, campania, documento, telefono, respuesta_envio, fecha, mensaje) values ('$messageId', '$campania', '$documento', '$to','$respuestaEnvio', '$date', '$texto')");

        }        
      }
      }
      
  }

  public function insertarRespuestasMultiplesCreditos($dataCod,$data,$tipoPersona,$campania,$creditos)
  {
    
    foreach ($dataCod['messages'] as $a) { 
      $documento = 0;
      foreach ($data as $t) { 
        $n1 = $t['to']; $n2 = '+'.$a['to'];
        if( $n1 == $n2 ){
          $resultado = substr($a['to'], 2);
          if($tipoPersona == "Cliente"){
            foreach ($creditos as $credito) {   
              $this->db_maestro->select('clientes.documento,'.$this->db_maestro->database.'.creditos.id as credito');
              $this->db_maestro->from('clientes');            
              $this->db_maestro->join($this->db_maestro->database.'.agenda_telefonica', $this->db_maestro->database.'.agenda_telefonica.id_cliente = clientes.id', 'inner');
              $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id_cliente = clientes.id', 'inner');
              $this->db_maestro->where($this->db_maestro->database.'.agenda_telefonica.numero =', $resultado);
              $this->db_maestro->where($this->db_maestro->database.'.creditos.id =', $credito);
              //$query = $this->db_maestro->get_compiled_select();
              //echo $query.' ';
              $query = $this->db_maestro->get()->result();
              if(isset($query)){
                foreach ( $query as $clave => $q ) {
                  $documento =  $q->documento;
                  date_default_timezone_set('America/Argentina/Buenos_Aires');
                  $date = date("Y-m-d H:i:s"); 
                  $messageId = $a['messageId'];
                  $to = $a['to'];      
                  $respuestaEnvio = $a['status']['name'];
                  $texto = $t['text'];
                  $this->db_maestro->query("INSERT INTO respuesta_envios_sms (id_mensaje, campania, documento, telefono, respuesta_envio, fecha, mensaje, credito) values ('$messageId', '$campania', '$documento', '$to','$respuestaEnvio', '$date', '$texto', '$credito')");
                }
              } 
            }          
          }       
        }
      }      
    }
}

  public function insertarRespuestasMultiplesBuros($dataCod,$data,$tipoPersona,$campania)
  {
    
    foreach ($dataCod['messages'] as $a) {  
    $documento = 0;
      foreach ($data as $t) { 
           $n1 = $t['to']; $n2 = '+'.$a['to'];
        // echo $n1 . ' ' . $n2 . ' ';
        if( $n1 == $n2 ){
     
          $resultado = substr($a['to'], 2);
          if($tipoPersona == "Cliente"){
            $this->db_maestro->select('clientes.documento');
            $this->db_maestro->from('clientes');            
            $this->db_maestro->join($this->db_buros->database.'.datacredito2_reconocer_naturalnacional', $this->db_buros->database.'.datacredito2_reconocer_naturalnacional.identificacion = clientes.documento', 'inner');
            $this->db_maestro->join($this->db_buros->database.'.datacredito2_reconocer_celular', $this->db_buros->database.'.datacredito2_reconocer_celular.IdConsulta = '.$this->db_buros->database.'.datacredito2_reconocer_naturalnacional.id', 'inner');
            $this->db_maestro->where($this->db_buros->database.'.datacredito2_reconocer_celular.celular =', $resultado);
            $query = $this->db_maestro->get()->result();
            if(isset($query)){
              foreach ( $query as $clave => $q ) {
                $documento =  $q->documento;
              }
            }
            else{
              $documento = "No";
            }  
          }elseif($tipoPersona == "Solicitantes"){
            $this->db_solicitudes->select('solicitud.documento');
            $this->db_solicitudes->from('solicitud');
            $this->db_solicitudes->where('solicitud.telefono =', $resultado);
            $query = $this->db_solicitudes->get()->result();
            if(isset($query)){
              foreach ( $query as $clave => $q ) {
                $documento =  $q->documento;
              }
            }
            else{
              $documento = "No";
            }    
          }
          date_default_timezone_set('America/Argentina/Buenos_Aires');
          $date = date("Y-m-d H:i:s"); 
          $messageId = $a['messageId'];
          $to = $a['to'];      
          $respuestaEnvio = $a['status']['name'];
          $texto = $t['text'];
          $this->db_maestro->query("INSERT INTO respuesta_envios_sms (id_mensaje, campania, documento, telefono, respuesta_envio, fecha, mensaje) values ('$messageId', '$campania', '$documento', '$to','$respuestaEnvio', '$date', '$texto')");

        }        
      }
      }
      
  }

   public function insertarRespuestasVerificacion($dataCod,$data,$tipoPersona)
  {
    foreach ($dataCod['messages'] as $a) {  
    $documento = 0;
      foreach ($data as $t) { 
           $n1 = $t['to']; $n2 = '+'.$a['to'];
        // echo $n1 . ' ' . $n2 . ' ';
        if( $n1 == $n2 ){
     
          $resultado = substr($a['to'], 2);
          if($tipoPersona == "Cliente"){
            $this->db_maestro->select('clientes.documento');
            $this->db_maestro->from('clientes');            
            $this->db_maestro->join($this->db_maestro->database.'.agenda_telefonica', $this->db_maestro->database.'.agenda_telefonica.id_cliente = clientes.id', 'inner');
            $this->db_maestro->where($this->db_maestro->database.'.agenda_telefonica.numero =', $resultado);
            $query = $this->db_maestro->get()->result();
            if(isset($query)){
              foreach ( $query as $clave => $q ) {
                $documento =  $q->documento;
              }
            }
            else{
              $documento = "No";
            }  
          }elseif($tipoPersona == "Solicitantes"){

            $this->db_buros->select('datacredito2_reconocer_celular.IdConsulta');
            $this->db_buros->from('datacredito2_reconocer_celular');
            $this->db_buros->where('datacredito2_reconocer_celular.celular =',$resultado);
            $subQuery3 = $this->db_buros->get_compiled_select();

            $this->db_buros->select('datacredito2_reconocer_naturalNacional.identificacion');
            $this->db_buros->from('datacredito2_reconocer_naturalNacional');
            $this->db_buros->where("datacredito2_reconocer_naturalNacional.id in ($subQuery3)", null, false);  
            $query = $this->db_buros->get()->result();
            //$query = $this->db_buros->get_compiled_select();
            //echo $query;die();

            if(isset($query)){
              foreach ( $query as $clave => $q ) {
                $documento =  $q->identificacion;
              }
            }
            else{
              $documento = "No";
            }    
          }
          date_default_timezone_set('America/Argentina/Buenos_Aires');
          $date = date("Y-m-d H:i:s"); 
          $messageId = $a['messageId'];
          $to = $a['to'];      
          $respuestaEnvio = $a['status']['name'];
          $texto = $t['text'];
          $campania = 'verificacion';
          $this->db_maestro->query("INSERT INTO respuesta_envios_sms (id_mensaje, documento, telefono, respuesta_envio, fecha, campania, mensaje) values ('$messageId', '$documento', '$to','$respuestaEnvio', '$date', '$texto', '$campania')");
        }        
      }
    }      
  }

 public function insertarRespuestaMultiplesSinDni($dataCod,$data){   

    foreach ($dataCod['messages'] as $a) {    
      foreach ($data as $t) {
        $n1 = $t['to']; $n2 = '+'.$a['to'];
        if( $n1 == $n2 ){        
          
          date_default_timezone_set('America/Argentina/Buenos_Aires');
          $date = date("Y-m-d H:i:s"); 
          $messageId = $a['messageId'];
          $to = $a['to'];      
          $respuestaEnvio = $a['status']['name'];
          $texto = $t['text'];
         $this->db_maestro->query("INSERT INTO respuesta_envios_sms (id_mensaje, telefono, respuesta_envio, fecha, mensaje) values ('$messageId','$to','$respuestaEnvio', '$date', '$texto')");
        }  
      }     
    }
 }

public function insertarSmsParaEnviar($telefono,$dni,$tipo,$text,$numero_prestamo){
   date_default_timezone_set('America/Argentina/Buenos_Aires');
   $date = date("Y-m-d H:i:s");
   $this->db_gestion->query("INSERT INTO enviar_sms_infobip (telefono, documento, mensaje, credito, campania, fecha_campania) values ('$telefono','$dni', '$text', '$numero_prestamo', '$tipo' ,'$date')");   
} 

public function insertarMailParaEnviar($mail,$dni,$template,$tipo,$numero_prestamo){
   date_default_timezone_set('America/Argentina/Buenos_Aires');
   $date = date("Y-m-d H:i:s");
   $this->db_gestion->query("INSERT INTO enviar_mail_pepipost (mail, documento, template, credito, campania, fecha_campania) values ('$mail','$dni', '$template', '$numero_prestamo', '$tipo' ,'$date')");   
} 


  public function solicitantesDesembolso($id)
  {
    $status_array = array('VERIFICADO', 'VALIDADO');
    $this->db_solicitudes->select('solicitud.id, solicitud.id_usuario, solicitud.documento, solicitud.estado, solicitud.email, solicitud.telefono, solicitud.nombres, ' . $this->db_solicitudes->database . '.solicitud_condicion_desembolso.capital_solicitado as monto');
    $this->db_solicitudes->from('solicitud');
    $this->db_solicitudes->join($this->db_solicitudes->database . '.solicitud_condicion_desembolso', $this->db_solicitudes->database . '.solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'inner');
    $this->db_solicitudes->where('resultado_ultimo_reto =', 'CORRECTA');
    $this->db_solicitudes->where('respuesta_analisis =', 'APROBADO');
    $this->db_solicitudes->where_in('estado', $status_array);
    $this->db_solicitudes->where('id_usuario =', $id);
    $query = $this->db_solicitudes->get()->result();
    return $query;
  }


  public function desembolso($tipo,$estado) //BORRARLE EL WHERE QUE ESTA MAL
  {
    $this->db_maestro->select('id_solicitud');
    $this->db_maestro->from('maestro.campanias');    
    $this->db_maestro->where('tipo_campania =',$tipo);
    $subQuery = $this->db_maestro->get_compiled_select();

   
    $this->db_solicitudes->select('solicitud.id, solicitud.id_usuario, solicitud.documento, solicitud.estado, solicitud.email, solicitud.telefono, solicitud.nombres, ' . $this->db_solicitudes->database . '.solicitud_condicion_desembolso.capital_solicitado as monto, ');
    $this->db_solicitudes->from('solicitud');
    $this->db_solicitudes->join($this->db_solicitudes->database . '.solicitud_condicion_desembolso', $this->db_solicitudes->database . '.solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'inner');
    $this->db_solicitudes->where('resultado_ultimo_reto =', 'CORRECTA');
    $this->db_solicitudes->where('validacion_telefono =', '1');  
    $this->db_solicitudes->where('respuesta_analisis =', 'APROBADO');
    $this->db_solicitudes->where('solicitud.estado =', $estado);
    $this->db_solicitudes->where("id_solicitud not in ($subQuery)", null, false);
    $query = $this->db_solicitudes->get()->result();
    return $query;
    //$query = $this->db_solicitudes->get_compiled_select();
    //echo $query;
  
  }


  public function getClientesRetanqueo($tipo)
  {

    $this->db_maestro->select('id_cliente');
    $this->db_maestro->from('creditos');
    $this->db_maestro->where('estado =', 'mora');
    $this->db_maestro->or_where('estado =', 'vigente');
    $subQuery = $this->db_maestro->get_compiled_select();

    $this->db_maestro->select('id_cliente');
    $this->db_maestro->from('creditos');
    $this->db_maestro->where("id_cliente not in ($subQuery)", null, false);
    $subQuery2 = $this->db_maestro->get_compiled_select();

    $this->db_maestro->select('id_usuario');
    $this->db_maestro->from('campanias');
    $this->db_maestro->where('tipo_campania =',$tipo);
    $subQuery3 = $this->db_maestro->get_compiled_select();

    $this->db_maestro->select('clientes.id, clientes.nombres, clientes.apellidos, clientes.documento,' . $this->db_usuarios->database . '.users.id as usuario,' . $this->db_maestro->database . '.agenda_telefonica.numero as telefono,' . $this->db_maestro->database . '.creditos.estado as estado,
        cast(' . $this->db_maestro->database . '.niveles_clientes.monto_disponible + (' . $this->db_maestro->database . '.niveles_clientes.monto_disponible * ' . $this->db_maestro->database . '.niveles_clientes.beneficio_monto_porcentual / 100) as int) as monto,
        ' . $this->db_maestro->database . '.agenda_mail.cuenta as email');
    $this->db_maestro->from('clientes');
    $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id_cliente = clientes.id', 'inner');
    $this->db_maestro->join($this->db_solicitudes->database.'.solicitud', $this->db_solicitudes->database.'.solicitud.id_cliente = clientes.id', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.agenda_telefonica', $this->db_maestro->database.'.agenda_telefonica.id_cliente = clientes.id', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.agenda_mail', $this->db_maestro->database.'.agenda_mail.id_cliente = clientes.id', 'inner');
    $this->db_maestro->join($this->db_usuarios->database.'.users', $this->db_usuarios->database.'.users.id = clientes.id_usuario', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.niveles_clientes', $this->db_maestro->database.'.niveles_clientes.id_cliente = clientes.id', 'inner');
    $this->db_maestro->where("clientes.id in ($subQuery2)", null, false);
    $this->db_maestro->where("clientes.id_usuario not in ($subQuery3)", null, false);    
    $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.fuente =','PERSONAL');
    $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.estado =',1);
    $this->db_maestro->group_by('clientes.id');    
    $query = $this->db_maestro->get()->result();
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;
    return $query;
    /*SELECT `clientes`.`id`, `clientes`.`nombres`, `clientes`.`documento`, `usuarios`.`users`.`id` as `usuario`,`maestro`.`agenda_telefonica`.`numero` as `telefono`, `maestro`.`creditos`.`estado` as `estado`,
    cast(maestro.niveles_clientes.monto_disponible + (maestro.niveles_clientes.monto_disponible *maestro.niveles_clientes.beneficio_monto_porcentual / 100) as int) as monto, `maestro`.`agenda_mail`.`cuenta` as `email`
    FROM `clientes`INNER JOIN `maestro`.`creditos` ON `maestro`.`creditos`.`id_cliente` = `clientes`.`id`INNER JOIN `solicitudes`.`solicitud` ON `solicitudes`.`solicitud`.`id_cliente` = `clientes`.`id`
    INNER JOIN `maestro`.`agenda_telefonica` ON `maestro`.`agenda_telefonica`.`id_cliente` = `clientes`.`id`INNER JOIN `maestro`.`agenda_mail` ON `maestro`.`agenda_mail`.`id_cliente` = `clientes`.`id`
    INNER JOIN `usuarios`.`users` ON `usuarios`.`users`.`id` = `clientes`.`id_usuario`INNER JOIN `maestro`.`niveles_clientes` ON `maestro`.`niveles_clientes`.`id_cliente` = `clientes`.`id`
    WHERE clientes.id in (SELECT `id_cliente` FROM ` creditos`WHERE id_cliente not in (SELECT `id_cliente`FROM  `creditos` WHERE `estado` = 'mora' OR `estado` = 'vigente'))
    GROUP BY `clientes`.`id`*/

    //problemas con el query, manda null al metodo del controlador
  }

   public function insertarMora($datos, $tipo)
  {    
    foreach ($datos as $key => $a) {
      date_default_timezone_set('America/Argentina/Buenos_Aires');
      $date = date("Y-m-d H:i:s"); 
      $id = $a->usuario;
      $documento = $a->documento;
      $telefono = $a->telefono;
      $email = $a->email;
      $estado = "1";
      $this->db_maestro->query("INSERT INTO campanias (documento, telefono, email, estado, id_usuario, tipo_campania, fecha_ingreso) values ('$documento','$telefono','$email','$estado','$id', '$tipo', '$date')");
    }
  }


  public function insertarSolicitantesDesemblolso($datos, $tipo)
  {    
    foreach ($datos as $key => $a) {
      date_default_timezone_set('America/Argentina/Buenos_Aires');
      $date = date("Y-m-d H:i:s"); 
      $id = $a->id;
      $documento = $a->documento;
      $telefono = $a->telefono;
      $email = $a->email;
      $estado = "0";
      $this->db_maestro->query("INSERT INTO campanias (documento, telefono, email, estado, id_solicitud, tipo_campania, fecha_ingreso) values ('$documento','$telefono','$email','$estado','$id', '$tipo', '$date')");
    }
  }

  public function insertarRetanqueos($datos, $tipo)
  {
  
    foreach ($datos as $key => $a) {
      date_default_timezone_set('America/Argentina/Buenos_Aires');
      $date = date("Y-m-d H:i:s"); 
      $documento = $a->documento;
      $telefono = $a->telefono;
      $email = $a->email;
      $usuario = $a->usuario;
      $estado = "1";
      $id = "0";
      $this->db_maestro->query("INSERT INTO campanias (documento, telefono, email, estado, id_solicitud, id_usuario, tipo_campania, fecha_ingreso) values ('$documento','$telefono','$email','$estado','$id', '$usuario', '$tipo', '$date')");
     
      //$this->db_maestro->query("INSERT INTO campanias (documento, telefono, email, estado, id_solicitud, id_usuario, tipo_campania) values ('$documento','$telefono','$email','$estado','$id', '$usuario', '$tipo')");
    }
  }

public function solicitudesRetanqueo(){
    $this->db_solicitudes->select('solicitud.id_usuario');
    $this->db_solicitudes->from('solicitud');
    $this->db_solicitudes->where('solicitud.tipo_solicitud =', 'RETANQUEO');
    $this->db_solicitudes->where('solicitud.estado !=', 'PAGADO');
    $query = $this->db_solicitudes->get()->result();
  
  foreach ($query as $key => $value) {
     $idUsuario = $value->id_usuario;
        $data[] = $idUsuario;     
  }
  if(!isset($data)){
    $data = null;
  }
  return $data;  
}
  
  public function retanqueosCampania($data)
  {
    //$rechazados = $this->telefonosRechazados();

    $solRetanqueos = $this->solicitudesRetanqueo();

    $this->db_maestro->select('id_cliente');
    $this->db_maestro->from('creditos');
    $this->db_maestro->where('estado =', 'mora');
    $this->db_maestro->or_where('estado =', 'vigente');
    $subQuery = $this->db_maestro->get_compiled_select();

    $this->db_maestro->select('id_cliente');
    $this->db_maestro->from('creditos');
    $this->db_maestro->where("id_cliente not in ($subQuery)", null, false);
    $subQuery2 = $this->db_maestro->get_compiled_select();

    $this->db_maestro->select('campanias.documento, campanias.id_usuario, campanias.fecha_actualizacion, campanias.telefono, '.$this->db_maestro->database.'.clientes.nombres as nombres, campanias.email, campanias.estado, '.$this->db_maestro->database . '.niveles_clientes.monto_disponible as monto,' . $this->db_maestro->database . '.niveles_clientes.beneficio_monto_porcentual as beneficio, campanias.tipo_campania');
    $this->db_maestro->from('campanias');
    $this->db_maestro->join($this->db_maestro->database.'.clientes', $this->db_maestro->database.'.clientes.id_usuario = campanias.id_usuario', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.niveles_clientes', $this->db_maestro->database.'.niveles_clientes.id_cliente = clientes.id', 'inner');
    $this->db_maestro->where('tipo_campania =', $data);
    //$this->db_maestro->where_not_in('campanias.telefono', $rechazados);
    $this->db_maestro->where($this->db_maestro->database.".clientes.id in ($subQuery2)", null, false);
    $this->db_maestro->where_not_in('campanias.id_usuario', $solRetanqueos);
    $this->db_maestro->group_by($this->db_maestro->database.'.clientes.id');      
    $query = $this->db_maestro->get()->result();
    return $query; 
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;die();   
  }

  public function desembolsoCampania($data,$estado)
  {
      $rechazados = $this->telefonosRechazados();

    $this->db_maestro->select('campanias.documento, campanias.fecha_actualizacion, campanias.fecha_ingreso, '.$this->db_solicitudes->database.'.solicitud.estado as estadoPrestamo,campanias.id_usuario, campanias.telefono, '.$this->db_solicitudes->database.'.solicitud_condicion_desembolso.capital_solicitado as monto, '.$this->db_solicitudes->database.'.solicitud.nombres as nombres, '.$this->db_solicitudes->database.'.solicitud.apellidos as apellidos, campanias.email, campanias.fecha_ingreso, campanias.estado, campanias.id_solicitud, campanias.tipo_campania');
    $this->db_maestro->from('campanias');
    $this->db_maestro->join($this->db_solicitudes->database . '.solicitud_condicion_desembolso', $this->db_solicitudes->database.'.solicitud_condicion_desembolso.id_solicitud = campanias.id_solicitud', 'inner');
    $this->db_maestro->join($this->db_solicitudes->database . '.solicitud', $this->db_solicitudes->database.'.solicitud.id = campanias.id_solicitud', 'inner');
    $this->db_maestro->where('tipo_campania =', $data);    
    $this->db_maestro->where_not_in('campanias.telefono', $rechazados);
    $this->db_maestro->where($this->db_solicitudes->database.'.solicitud.estado =', $estado);
    $this->db_maestro->where($this->db_solicitudes->database.'.solicitud.respuesta_analisis =', 'APROBADO');
    $this->db_maestro->where($this->db_solicitudes->database.'.solicitud.resultado_ultimo_reto =', 'CORRECTA');
    $this->db_maestro->where($this->db_solicitudes->database.'.solicitud.validacion_telefono =', '1');  
    $query = $this->db_maestro->get()->result();
    return $query;
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;   
  }

  public function moraCampania($data){ /// 
    //  $rechazados = $this->telefonosRechazados();
    $status_array = array('mora', 'vigente');
      $this->db_maestro->select('campanias.documento, campanias.id_usuario,'.$this->db_maestro->database.'.clientes.id as id_cliente,'.$this->db_maestro->database.'.credito_detalle.fecha_vencimiento as vencimiento, campanias.telefono, '.$this->db_maestro->database.'.clientes.nombres as nombres, '.$this->db_maestro->database.'.creditos.id as id_credito,'.$this->db_maestro->database.'.clientes.apellidos as apellidos, '.$this->db_maestro->database.'.credito_detalle.monto_cobrar as monto, '.$this->db_maestro->database.'.creditos.monto_prestado as monto_prestado,campanias.email, campanias.estado, campanias.tipo_campania,'.$this->db_solicitudes->database.'.solicitud.tipo_solicitud as tipo_solicitud');
    $this->db_maestro->from('campanias');
    $this->db_maestro->join($this->db_maestro->database.'.clientes', $this->db_maestro->database.'.clientes.id_usuario = campanias.id_usuario', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id_cliente = '.$this->db_maestro->database.'.clientes.id', 'inner');
     $this->db_maestro->join($this->db_solicitudes->database.'.solicitud', $this->db_solicitudes->database.'.solicitud.id_credito = '.$this->db_maestro->database.'.creditos.id', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.credito_detalle', $this->db_maestro->database.'.credito_detalle.id_credito = '.$this->db_maestro->database.'.creditos.id', 'inner');
    $this->db_maestro->where('tipo_campania =', $data);
    $this->db_maestro->where($this->db_solicitudes->database.'.solicitud.id_credito !=', null);
    //$this->db_maestro->where_not_in('campanias.telefono', $rechazados);
    $this->db_maestro->where_in($this->db_maestro->database.'.creditos.estado',$status_array);

    $this->db_maestro->group_by('campanias.id_usuario');
    $query = $this->db_maestro->get()->result();
    return $query; 
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;die();
  }

public function telefonosEnviadosMora(){
  $now = date('Y-m-d');
  $this->db_maestro->select('respuesta_envios_sms.telefono, respuesta_envios_sms.fecha');
  $this->db_maestro->from('respuesta_envios_sms');
  $this->db_maestro->where('respuesta_envios_sms.campania = ','moraSms');
  $this->db_maestro->like('respuesta_envios_sms.mensaje ','25 dias');
  $this->db_maestro->or_like('respuesta_envios_sms.mensaje ','10 dias');
  $query = $this->db_maestro->get()->result();
  
  foreach ($query as $key => $value) {
     $telefono = $value->telefono;
     $fecha1 = $value->fecha;
     $fecha = date("Y-m-d", strtotime($fecha1));
     if($fecha == $now){
      $telefono = substr($telefono, 2);
        $data[] = $telefono;
     }
  }
  if(!isset($data)){
    $data = null;
  }
  return $data;
}

   public function moraCampania2($data){ /// problemas con variable monto en linea 390 del contorlador porque no se que poner
    
    $datos = $this->telefonosEnviadosMora();
    $this->db_maestro->select('campanias.documento, campanias.id_usuario,'.$this->db_maestro->database.'.credito_detalle.fecha_vencimiento as vencimiento, campanias.telefono, campanias.estado, campanias.tipo_campania');
    $this->db_maestro->from('campanias');
    $this->db_maestro->join($this->db_maestro->database.'.clientes', $this->db_maestro->database.'.clientes.id_usuario = campanias.id_usuario', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id_cliente = '.$this->db_maestro->database.'.clientes.id', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.credito_detalle', $this->db_maestro->database.'.credito_detalle.id_credito = '.$this->db_maestro->database.'.creditos.id', 'inner');
    $this->db_maestro->where('tipo_campania =', $data);
    $this->db_maestro->where($this->db_maestro->database.'.credito_detalle.fecha_vencimiento >', '2020-02-10');
    $this->db_maestro->where_in('campanias.telefono',$datos);
    $this->db_maestro->group_by('campanias.id_usuario');
    $query = $this->db_maestro->get()->result();
    return $query; 
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;
  }

public function telefonosEnviados(){
  $now = date('Y-m-d');
  $this->db_maestro->select('respuesta_envios_sms.telefono, respuesta_envios_sms.fecha');
  $this->db_maestro->from('respuesta_envios_sms');
  $query = $this->db_maestro->get()->result();
  
  foreach ($query as $key => $value) {
     $telefono = $value->telefono;
     $fecha1 = $value->fecha;
     $fecha = date("Y-m-d", strtotime($fecha1));
     if($fecha == $now){
      $telefono = substr($telefono, 2);
        $data[] = $telefono;
     }
  }
  if(!isset($data)){
    $data = null;
  }
  return $data;
}

public function telefonosRechazados(){

  $this->db_maestro->select('telefono');
  $this->db_maestro->from('respuesta_envios_sms');
  $this->db_maestro->like('respuesta_envio','REJECTED');
  $query = $this->db_maestro->get()->result();
  
  foreach ($query as $key => $value) {
     $telefono = $value->telefono;
     $telefono = '57'.$telefono;
     $data[] = $telefono; 
  }
  return $data;
}

public function telefonosRechazadosDatacredito(){

  $this->db_maestro->select('telefono');
  $this->db_maestro->from('respuesta_envios_sms');
  $this->db_maestro->like('mensaje','reportado en DATACREDITO');
  $query = $this->db_maestro->get()->result();
  
  foreach ($query as $key => $value) {
     $telefono = $value->telefono;
     $telefono = substr($telefono, 2);
     $data[] = $telefono; 
  }
  return $data;
  //var_dump($data);
}

public function telefonosParaActualizar(){
  $this->db_maestro->select('numero,'.$this->db_maestro->database.'.clientes.id_usuario');
  $this->db_maestro->from('agenda_telefonica');
  $this->db_maestro->join($this->db_maestro->database.'.clientes', $this->db_maestro->database.'.clientes.id = agenda_telefonica.id_cliente', 'inner');
  $this->db_maestro->where('fuente =','PERSONAL');
  $query = $this->db_maestro->get()->result();
  
  foreach ($query as $key => $value) {
     $telefono = $value->numero;
     $usuario = $value->id_usuario;
    $this->db_maestro->set('telefono', $telefono);
    $this->db_maestro->where('id_usuario', $usuario);
    $this->db_maestro->update('campanias');
  }
}

public function nuevaCampaniaMoraQuincenal($data){ /// problemas con variable monto en linea 390 del contorlador porque no se que poner
    
    //$rechazados = $this->telefonosRechazadosDatacredito(); //PARA DATACREDITO
    //$datos = $this->telefonosEnviados();

   $status_array = array('mora', 'vigente');
      $this->db_maestro->select('campanias.documento, campanias.id_usuario,'.$this->db_maestro->database.'.credito_detalle.fecha_vencimiento as vencimiento, '.$this->db_maestro->database.'.clientes.id as id_cliente, campanias.telefono, '.$this->db_maestro->database.'.clientes.nombres as nombres, '.$this->db_maestro->database.'.creditos.id as id_credito,'.$this->db_maestro->database.'.clientes.apellidos as apellidos, '.$this->db_maestro->database.'.credito_detalle.monto_cobrar as monto, '.$this->db_maestro->database.'.creditos.monto_prestado as monto_prestado,campanias.email, campanias.estado, campanias.tipo_campania,'.$this->db_solicitudes->database.'.solicitud.tipo_solicitud as tipo_solicitud');
    $this->db_maestro->from('campanias');
    $this->db_maestro->join($this->db_maestro->database.'.clientes', $this->db_maestro->database.'.clientes.id_usuario = campanias.id_usuario', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id_cliente = '.$this->db_maestro->database.'.clientes.id', 'inner');
     $this->db_maestro->join($this->db_solicitudes->database.'.solicitud', $this->db_solicitudes->database.'.solicitud.id_credito = '.$this->db_maestro->database.'.creditos.id', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.credito_detalle', $this->db_maestro->database.'.credito_detalle.id_credito = '.$this->db_maestro->database.'.creditos.id', 'inner');
    $this->db_maestro->where('tipo_campania =', $data);
    $this->db_maestro->where($this->db_solicitudes->database.'.solicitud.id_credito !=', null);
    //$this->db_maestro->where_not_in('campanias.telefono', $rechazados);
    //$this->db_maestro->having(array('COUNT(campanias.id_usuario) <' => 1));
    $this->db_maestro->where_in($this->db_maestro->database.'.creditos.estado',$status_array);
  /*  if(!is_null($datos) || !empty($datos) || isset($datos)){
    $this->db_maestro->where_not_in('campanias.telefono',$datos);
    }*/
    //$this->db_maestro->limit(1000);
    $this->db_maestro->group_by('campanias.id_usuario');
    $query = $this->db_maestro->get()->result();
    return $query; 
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;
  }

  public function nuevaCampaniaMoraQuincenalDatacredito($data){ /// problemas con variable monto en linea 390 del contorlador porque no se que poner
    
    $rechazados = $this->telefonosRechazadosDatacredito(); //PARA DATACREDITO
    //$datos = $this->telefonosEnviados();

   $status_array = array('mora', 'vigente');
      $this->db_maestro->select('campanias.documento, campanias.id_usuario,'.$this->db_maestro->database.'.credito_detalle.fecha_vencimiento as vencimiento, '.$this->db_maestro->database.'.clientes.id as id_cliente, campanias.telefono, '.$this->db_maestro->database.'.clientes.nombres as nombres, '.$this->db_maestro->database.'.creditos.id as id_credito,'.$this->db_maestro->database.'.clientes.apellidos as apellidos, '.$this->db_maestro->database.'.credito_detalle.monto_cobrar as monto, '.$this->db_maestro->database.'.creditos.monto_prestado as monto_prestado,campanias.email, campanias.estado, campanias.tipo_campania,'.$this->db_solicitudes->database.'.solicitud.tipo_solicitud as tipo_solicitud');
    $this->db_maestro->from('campanias');
    $this->db_maestro->join($this->db_maestro->database.'.clientes', $this->db_maestro->database.'.clientes.id_usuario = campanias.id_usuario', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id_cliente = '.$this->db_maestro->database.'.clientes.id', 'inner');
     $this->db_maestro->join($this->db_solicitudes->database.'.solicitud', $this->db_solicitudes->database.'.solicitud.id_credito = '.$this->db_maestro->database.'.creditos.id', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.credito_detalle', $this->db_maestro->database.'.credito_detalle.id_credito = '.$this->db_maestro->database.'.creditos.id', 'inner');
    $this->db_maestro->where('tipo_campania =', $data);
    $this->db_maestro->where($this->db_solicitudes->database.'.solicitud.id_credito !=', null);
    $this->db_maestro->where_not_in('campanias.telefono', $rechazados);
    //$this->db_maestro->having(array('COUNT(campanias.id_usuario) <' => 1));
    $this->db_maestro->where_in($this->db_maestro->database.'.creditos.estado',$status_array);
   /* if(!is_null($datos) || !empty($datos) || isset($datos)){
    $this->db_maestro->where_not_in('campanias.telefono',$datos);
    }*/
    //$this->db_maestro->limit(1000);
    $this->db_maestro->group_by('campanias.id_usuario');
    $query = $this->db_maestro->get()->result();
    return $query; 
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;
  }

  public function clientesMora($tipo)
  {
     
    $this->db_maestro->select('id_usuario');
    $this->db_maestro->from('campanias');
    $this->db_maestro->where('tipo_campania =',$tipo);
    $subQuery3 = $this->db_maestro->get_compiled_select();

    $status_array = array('mora', 'vigente');
    $this->db_maestro->select('creditos.fecha_primer_vencimiento as fecha,' . $this->db_maestro->database . '.clientes.id_usuario as usuario, ' . $this->db_maestro->database . '.clientes.documento, creditos.estado,' . $this->db_maestro->database . '.agenda_mail.cuenta as email,' . $this->db_maestro->database . '.agenda_telefonica.numero as telefono, ' . $this->db_maestro->database . '.clientes.nombres as nombres,');
    $this->db_maestro->from('creditos');
    $this->db_maestro->join($this->db_maestro->database . '.clientes', $this->db_maestro->database . '.clientes.id = creditos.id_cliente', 'inner');
    $this->db_maestro->join($this->db_maestro->database . '.agenda_telefonica', $this->db_maestro->database . '.agenda_telefonica.id_cliente = creditos.id_cliente', 'inner');
    $this->db_maestro->join($this->db_maestro->database . '.agenda_mail', $this->db_maestro->database . '.agenda_mail.id_cliente = creditos.id_cliente', 'inner');
    $this->db_maestro->where("maestro.clientes.id_usuario not in ($subQuery3)", null, false);
    $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.fuente =','PERSONAL');
    $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.estado =',1);
    //$this->db_maestro->where('creditos.fecha_primer_vencimiento >', '2020-01-03'); //sacar
    $this->db_maestro->where_in('creditos.estado', $status_array);
    //$this->db_maestro->limit(10);  //sacar
    $query = $this->db_maestro->get()->result();
    return $query;
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;
  }

public function clientesAcuerdoPago($idCliente){
 
    $this->db_maestro->select('clientes.nombres,clientes.documento, '.$this->db_gestion->database . '.acuerdos_pago.tipo,'.$this->db_gestion->database . '.acuerdos_pago.medio, SUM('.$this->db_gestion->database . '.acuerdos_pago.monto) as totalMonto, count('.$this->db_gestion->database .'.acuerdos_pago.monto) as cantidad,'.$this->db_gestion->database . '.acuerdos_pago.monto, MIN('.$this->db_gestion->database . '.acuerdos_pago.fecha) as fecha, '.$this->db_maestro->database . '.agenda_telefonica.numero as telefono,'.$this->db_maestro->database . '.agenda_mail.cuenta as email');
    $this->db_maestro->from('clientes');
    $this->db_maestro->join($this->db_gestion->database . '.acuerdos_pago', $this->db_gestion->database . '.acuerdos_pago.id_cliente = clientes.id', 'inner');
    $this->db_maestro->join($this->db_gestion->database . '.plan_detalle', $this->db_gestion->database . '.plan_detalle.id = '.$this->db_gestion->database . '.acuerdos_pago.id_plan_detalle', 'left');
    $this->db_maestro->join($this->db_maestro->database . '.agenda_mail', $this->db_maestro->database . '.agenda_mail.id_cliente = clientes.id', 'inner');  
    $this->db_maestro->join($this->db_maestro->database . '.agenda_telefonica', $this->db_maestro->database . '.agenda_telefonica.id_cliente = clientes.id', 'inner'); 
    $this->db_maestro->where('clientes.id =',$idCliente);
    $this->db_maestro->where($this->db_gestion->database . '.acuerdos_pago.estado =','pendiente');
    $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.fuente =','PERSONAL');
    $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.estado =',1);
    $this->db_maestro->where($this->db_maestro->database . '.agenda_mail.fuente =','PERSONAL');
    $this->db_maestro->where($this->db_maestro->database . '.agenda_mail.estado =',1);
    $query = $this->db_maestro->get()->result();
    return $query;
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;die();
}
  public function clientesMora1dia()
  {
    $dia = strftime("%A");

    $this->db_maestro->select('id');
    $this->db_maestro->from('credito_detalle');
    $this->db_maestro->where('credito_detalle.estado =','mora');
    if($dia != "Monday"){
      $this->db_maestro->where('DATEDIFF(now(),fecha_vencimiento) =','1');
    }else{
      $this->db_maestro->where('DATEDIFF(now(),fecha_vencimiento) >','0');
      $this->db_maestro->where('DATEDIFF(now(),fecha_vencimiento) <','4');
    }
    $subQuery = $this->db_maestro->get_compiled_select();

    $this->db_maestro->select('credito_detalle.fecha_vencimiento as vencimiento,credito_detalle.monto_cobrar as monto, ,' . $this->db_maestro->database . '.clientes.documento,' . $this->db_maestro->database . '.agenda_telefonica.numero as telefono, ' . $this->db_maestro->database . '.clientes.nombres as nombres,');
    $this->db_maestro->from('credito_detalle');
    $this->db_maestro->join($this->db_maestro->database . '.creditos', $this->db_maestro->database . '.creditos.id = credito_detalle.id_credito', 'inner');
    $this->db_maestro->join($this->db_maestro->database . '.clientes', $this->db_maestro->database . '.clientes.id ='.$this->db_maestro->database . '.creditos.id_cliente', 'inner');
    $this->db_maestro->join($this->db_maestro->database . '.agenda_telefonica', $this->db_maestro->database . '.agenda_telefonica.id_cliente = '.$this->db_maestro->database . '.clientes.id', 'inner');
    $this->db_maestro->where("credito_detalle.id in ($subQuery)", null, false);
    $query = $this->db_maestro->get()->result();
    return $query;
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;
  }


public function actualizarHorario($idUsuario, $estado, $tipo){

    $now = date('Y-m-d');
    $this->db_maestro->set('fecha_actualizacion', $now);
    $this->db_maestro->where('id_usuario', $idUsuario);
    $this->db_maestro->where('tipo_campania', $tipo);
    $this->db_maestro->update('campanias');
}

public function actualizarEstadoValidado($estado,$idsolicitud,$tipo){

    $this->db_maestro->set('estado', $estado);
    $this->db_maestro->where('id_solicitud', $idsolicitud);
    $this->db_maestro->where('tipo_campania', $tipo);
    $this->db_maestro->update('campanias');
}

  public function actualizarCampaniasMora($idUsuario, $estado, $tipo)
  {
    $estado = $estado + 1;
    $now = date('Y-m-d');
    $this->db_maestro->set('fecha_actualizacion', $now);
    $this->db_maestro->set('estado', $estado);
    $this->db_maestro->where('id_usuario', $idUsuario);
    $this->db_maestro->where('tipo_campania', $tipo);
    $this->db_maestro->update('campanias');
  }
 
  
public function actualizarCampaniaRetanqueo($idUsuario, $estado, $tipo)
  {
    $estado = $estado + 1;
    date_default_timezone_set('America/Argentina/Buenos_Aires');
      $date = date("Y-m-d H:i:s"); 
     $this->db_maestro->set('fecha_actualizacion', $date);   
    $this->db_maestro->set('estado', $estado);
    $this->db_maestro->where('id_usuario', $idUsuario);
    $this->db_maestro->where('tipo_campania', $tipo);
    $this->db_maestro->update('campanias');
  }

  public function actualizarCampaniaDesembolso($idSolicitud, $estado, $tipo)
  {
    $estado = $estado + 1;  
    $now = date('Y-m-d');
    $this->db_maestro->set('fecha_actualizacion', $now);   
    $this->db_maestro->set('estado', $estado);
    $this->db_maestro->where('id_solicitud', $idSolicitud);
    $this->db_maestro->where('tipo_campania', $tipo);
    $this->db_maestro->update('campanias');
  }


  public function pagados(){
    $this->db_maestro->select($this->db_gestion->database.'.sms_creditos_pagados.id_credito');
    $this->db_maestro->from($this->db_gestion->database.'.sms_creditos_pagados');
    $subQuery = $this->db_maestro->get_compiled_select();

    $this->db_maestro->select('credito_detalle.id');
    $this->db_maestro->from('credito_detalle');
    $this->db_maestro->where("(credito_detalle.estado is null or credito_detalle.estado = 'mora')");
    $this->db_maestro->where("credito_detalle.id not in ($subQuery)", null, false);
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;die();
    $query = $this->db_maestro->get()->result();
    return $query;
  } 

  public function pagados2(){
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $time = time();
    $tiempo = date("Y-m-d H:i:s"); 
    $minutoAnadir=10; //tiene que ser 10 para 10 minutos
    $segundos_horaInicial=strtotime($tiempo); 
    $segundos_minutoAnadir=$minutoAnadir*60; 
    $nuevaHora=date("Y-m-d H:i:s",$segundos_horaInicial-$segundos_minutoAnadir);

    $this->db_maestro->select('credito_detalle.id as idCredito,' .$this->db_maestro->database.'.clientes.id as idCliente');
    $this->db_maestro->from('credito_detalle');    
    $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id = credito_detalle.id_credito', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.clientes', $this->db_maestro->database.'.creditos.id_cliente = '.$this->db_maestro->database.'.clientes.id', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.pago_credito', $this->db_maestro->database.'.pago_credito.id_detalle_credito = credito_detalle.id', 'inner');
    $this->db_maestro->where('credito_detalle.estado =','pagado');
    $this->db_maestro->where($this->db_maestro->database.'.pago_credito.fecha_pago >=',$nuevaHora);
    $this->db_maestro->where($this->db_maestro->database.'.pago_credito.fecha_pago <=',$tiempo);
    $this->db_maestro->where($this->db_maestro->database.'.pago_credito.estado =', 1);
    $this->db_maestro->group_by('credito_detalle.id');
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;die();
    $query = $this->db_maestro->get()->result();
    return $query;
  }

  public function getdocClientes()
  {
    $this->db_maestro->select('documento');
    $this->db_maestro->from('clientes');
    $query = $this->db_maestro->get()->result_array();
    foreach ($query as $row) {
      $data[] = $row['documento'];
    }
    return $data;
  }

   public function insertarPagados($datos)
  {    
    foreach ($datos as $key => $a) {
      date_default_timezone_set('America/Argentina/Buenos_Aires');
      $date = date("Y-m-d H:i:s"); 
      $id = $a->id;
      $estado = "1";
      $this->db_gestion->query("INSERT INTO sms_creditos_pagados (id_credito, estado) values ('$id','$estado')");
    }
  }

  public function pagadosSms(){
// seleccinar registros pagados en credito_detalle y ver si se encuentran en la tabla de sms_creditos_pagar
    $this->db_gestion->select($this->db_maestro->database.'.credito_detalle.id');
    $this->db_gestion->from($this->db_maestro->database.'.credito_detalle');
    $this->db_gestion->where($this->db_maestro->database.'.credito_detalle.estado =', 'pagado');
    $subQuery = $this->db_gestion->get_compiled_select();

    $this->db_gestion->select('id_credito');
    $this->db_gestion->from('sms_creditos_pagados');
    $this->db_gestion->where('estado =', 1);
    $this->db_gestion->where("sms_creditos_pagados.id_credito in ($subQuery)", null, false);
    $query = $this->db_gestion->get()->result(); 

    foreach ($query as $key => $value) {
      $data[] = $value->id_credito;
      }
      if(isset($data)){  
      return $data;
      }
}


public function actualizarPagados($idCredito){

      $this->db_gestion->set('estado', 2);   
      $this->db_gestion->where('id_credito', $idCredito);
      $this->db_gestion->update('sms_creditos_pagados');
  
}

public function notificarPagados($data){
    $this->db_maestro->select('credito_detalle.id as credito,' .$this->db_maestro->database.'.creditos.id_cliente, '.$this->db_maestro->database.'.agenda_telefonica.numero as telefono');
    $this->db_maestro->from('credito_detalle');
    $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id = credito_detalle.id_credito', 'inner');
    $this->db_maestro->join($this->db_maestro->database.'.agenda_telefonica', $this->db_maestro->database.'.agenda_telefonica.id_cliente = '.$this->db_maestro->database.'.creditos.id_cliente', 'inner');
    $this->db_maestro->where_in('credito_detalle.id', $data);
    $query = $this->db_maestro->get()->result();
    return $query;
}

public function mensajeCobranzas($idMensaje){

  $this->db_gestion->select('texto');
  $this->db_gestion->from('template_sms');
  $this->db_gestion->where('id =', $idMensaje);
  $query = $this->db_gestion->get()->result();
  return $query;
}

public function fusionDeuda($cliente){
  $this->db_maestro->select('SUM('.$this->db_maestro->database.'.credito_detalle.monto_cobrar) as deuda, clientes.nombres, clientes.documento, '.$this->db_gestion->database.'.acuerdos_pago.fecha, '.$this->db_gestion->database.'.acuerdos_pago.medio, '.$this->db_gestion->database.'.acuerdos_pago.monto');
  $this->db_maestro->from('clientes');
  $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id_cliente = clientes.id', 'left');
  $this->db_maestro->join($this->db_maestro->database.'.credito_detalle', $this->db_maestro->database.'.credito_detalle.id_credito = '.$this->db_maestro->database.'.creditos.id', 'left');
  $this->db_maestro->join($this->db_gestion->database.'.acuerdos_pago', $this->db_gestion->database.'.acuerdos_pago.id_cliente ='.$this->db_maestro->database.'.clientes.id', 'left');
  $this->db_maestro->where("(".$this->db_gestion->database.".acuerdos_pago.id_cliente = '".$cliente."' and ".$this->db_gestion->database.".acuerdos_pago.estado = 'pendiente')");
  $this->db_maestro->where($this->db_maestro->database.'.credito_detalle.estado =', 'mora');
  $this->db_maestro->group_by('clientes.id');
  $query = $this->db_maestro->get();      
  return $query->result_array(); 
  //$query = $this->db_maestro->get_compiled_select();
  //echo $query;die();
}

public function cambiarMensaje($mensajes,$persona,$idMensaje){
        foreach ($persona as $value) { 
         $deuda = $value['deuda'];
         $monto = $value['monto'];
         if(isset($monto)){
          $monto = arreglar_prestamo($monto);
         }
         if(isset($deuda)){          
          $deuda = arreglar_prestamo($deuda);
         }
         $medio = strtoupper($value['medio']);
         $dia = saber_dia($value['fecha']);
         $nombre = arreglar_string($value['nombres']);
         $fechaEnvio = date("Y-m-d");
         $fechaEnvio = date("d/m/Y", strtotime($fechaEnvio));       
         $fechaAcuerdo = date("d/m/Y", strtotime($value['fecha']));
         $fActualEnvio = substr($fechaEnvio, 0, 5);
         $fActualAcuerdo = substr($fechaAcuerdo, 0, 5);
      }  
      foreach ($mensajes as $sms) {
         $mensaje = $sms->texto;            
      }                             
        $mensaje = str_replace("[monto_deuda]", $deuda, $mensaje);
        $mensaje = str_replace("[medio]", $medio, $mensaje);
        $mensaje = str_replace("[fecha_envio]", $fechaEnvio, $mensaje);
        $mensaje = str_replace("[dia_acuerdo]", $fechaAcuerdo, $mensaje);
        $mensaje = str_replace("[monto_acuerdo]", $monto, $mensaje);
        $mensaje = str_replace("[Nombre_cliente]", $nombre, $mensaje);
        $mensaje = str_replace("[diatextual]", $dia, $mensaje);  
      return $mensaje;
}

public function posibilidadRetanqueo($idCliente){
    $this->db_maestro->select('id_cliente');
    $this->db_maestro->from('creditos');
    $this->db_maestro->where("(estado = 'mora' or estado = 'vigente')");    
    $this->db_maestro->where('id_cliente =', $idCliente);
    $this->db_maestro->group_by('id_cliente');
    $query = $this->db_maestro->get()->result();
    //$query = $this->db_maestro->get_compiled_select();  
    //echo $query;die();
    foreach ($query as $key => $value) {      
      $data[] = $value->id_cliente;
    }
      if(isset($data)){
        return 2;
      }else{
        return 1;
      }

   }

 public function datosPersonaRetanqueo($idcreditoDetalle){

   $this->db_maestro->select('credito_detalle.monto_cobrar, credito_detalle.dias_atraso, '.$this->db_maestro->database.'.clientes.nombres,' .$this->db_maestro->database.'.creditos.id as nPrestamo, ' .$this->db_maestro->database.'.agenda_telefonica.numero as telefono,' . $this->db_maestro->database . '.niveles_clientes.monto_disponible as disponible,'.$this->db_maestro->database . '.niveles_clientes.beneficio_monto_porcentual as porcentaje');
  $this->db_maestro->from('credito_detalle');
  $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id = credito_detalle.id_credito', 'inner');
  $this->db_maestro->join($this->db_maestro->database.'.clientes', $this->db_maestro->database.'.clientes.id = '.$this->db_maestro->database.'.creditos.id_cliente', 'inner');
  $this->db_maestro->join($this->db_maestro->database.'.niveles_clientes', $this->db_maestro->database.'.niveles_clientes.id_cliente = '.$this->db_maestro->database.'.clientes.id', 'inner');
  $this->db_maestro->join($this->db_maestro->database.'.agenda_telefonica', $this->db_maestro->database.'.agenda_telefonica.id_cliente = '.$this->db_maestro->database.'.clientes.id', 'inner');
  $this->db_maestro->where('credito_detalle.id =', $idcreditoDetalle);  
  $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.fuente =','PERSONAL');
  $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.estado =',1);
  //$query = $this->db_maestro->get_compiled_select();
  //echo $query;die();
  $query = $this->db_maestro->get()->result();
  return $query;
 }  

 public function datosPersonaSinRetanqueo($idcreditoDetalle){

   $this->db_maestro->select('credito_detalle.fecha_vencimiento, ' .$this->db_maestro->database.'.agenda_telefonica.numero as telefono,credito_detalle.monto_cobrar, '.$this->db_maestro->database.'.clientes.nombres,' .$this->db_maestro->database.'.creditos.id as nPrestamo');
  $this->db_maestro->from('credito_detalle');
  $this->db_maestro->join($this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id = credito_detalle.id_credito', 'inner');
  $this->db_maestro->join($this->db_maestro->database.'.clientes', $this->db_maestro->database.'.clientes.id = '.$this->db_maestro->database.'.creditos.id_cliente', 'inner');
  $this->db_maestro->join($this->db_maestro->database.'.agenda_telefonica', $this->db_maestro->database.'.agenda_telefonica.id_cliente = '.$this->db_maestro->database.'.clientes.id', 'inner');
  $this->db_maestro->where('credito_detalle.id =', $idcreditoDetalle);  
  $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.fuente =','PERSONAL');
  $this->db_maestro->where($this->db_maestro->database . '.agenda_telefonica.estado =',1);
  //$query = $this->db_maestro->get_compiled_select();
  //echo $query;die();
  $query = $this->db_maestro->get()->result();
  return $query;
 } 

 public function credito_otorgado(){

    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $time = time();
    $tiempo = date("Y-m-d H:i:s"); 
    $minutoAnadir=10; //tiene que ser 10 para 10 minutos
    $segundos_horaInicial=strtotime($tiempo); 
    $segundos_minutoAnadir=$minutoAnadir*60; 
    $nuevaHora=date("Y-m-d H:i:s",$segundos_horaInicial-$segundos_minutoAnadir);

    $this->db_solicitudes->select('solicitud.id,solicitud.clientid,solicitud.gclid,solicitud.utm_medium,solicitud.utm_source,solicitud.utm_campaign, solicitud.tipo_solicitud, ' .$this->db_solicitudes->database.'.solicitud_condicion_desembolso.capital_solicitado, ' .$this->db_solicitudes->database.'.solicitud_condicion_desembolso.total_devolver,' .$this->db_maestro->database.'.creditos.fecha_otorgamiento');
    $this->db_solicitudes->from('solicitud');
    $this->db_solicitudes->join( $this->db_solicitudes->database.'.solicitud_condicion_desembolso', $this->db_solicitudes->database.'.solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'inner');
    $this->db_solicitudes->join( $this->db_maestro->database.'.creditos', $this->db_maestro->database.'.creditos.id = solicitud.id_credito', 'inner');
    $this->db_solicitudes->where($this->db_maestro->database.'.creditos.fecha_otorgamiento >=',$nuevaHora);
    $this->db_solicitudes->where($this->db_maestro->database.'.creditos.fecha_otorgamiento <=',$tiempo);
    //$query = $this->db_maestro->get_compiled_select();
    //echo $query;die();
    $query = $this->db_solicitudes->get()->result();
    return $query;
 }


/*
|--------------------------------------------------------------------------
| API CAMPAÑIAS AUTOMATICAS Ing. Esthiven Garcia 28/09/2020
|--------------------------------------------------------------------------
|
| En este modulo se pretende definir y probar campañias antes de su salida a producción y la agendar su ejecución automatica
| 
|
*/
 public function buscar_eventos(){

  $consulta = $this->db_maestro->get("campanias_cronograma");
  return $consulta->result_array();
 }

  public function get_all_proveedores(){

  $consulta = $this->db_maestro->get("proveedores");
  return $consulta->result_array();
 }
	
public function get_proveedores_cronograma_campanias() {
 	$this->db_maestro->where('tipo_servicio !=', 'TELEFONIA');
	$consulta = $this->db_maestro->get("proveedores");
	
	return $consulta->result_array();
}

 public function guardar_prov($data){
        $this->db_maestro->insert('proveedores',$data);

        if ($this->db_maestro->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }
    }

public function guardar_evento($data){
    $this->db_maestro->insert('campanias_cronograma',$data);

    if ($this->db_maestro->affected_rows() > 0) {
        return true;
    }
    else{
        return false;
    }
}


    public function guardar_logicas($data){
        $this->db_maestro->insert('campanias_logicas',$data);

        if ($this->db_maestro->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }
    }

   
  public function actualizar_logicas ($id, $data){
          $this->db_maestro->where('id_logica', $id);
          $this->db_maestro->update('campanias_logicas', $data);
          if ($this->db_maestro->affected_rows() > 0) {
              return true;
          }
          else{
              return false;
          }
      }

 public function consulta_testing($sql){

    $query2 = $this->db_maestro->query($sql);
    $arreglo=[];
    $num = $query2->num_rows();
    $arreglo['numero_resultados_totales'][]=$num;

    foreach ($query2->list_fields() as $field)
    {
             $arreglo['campos'][]=$field;
    }
    
    $data  = $query2->result_array();
    $arreglo['result']=$data;
   
    return $arreglo;

  }

 public function get_all_logicas(){

  $consulta = $this->db_maestro->get("campanias_logicas");
  return $consulta->result_array();
 }

 public function consultaLogicasbyId($valor){
 
 $this->db_maestro->where('id_logica', $valor);
  $consulta = $this->db_maestro->get("campanias_logicas");
  return $consulta->result_array();
 }

 public function campain_sms_logicas($id_query){
    $this->db_campania->select('M.id_mensaje,M.query_contenido,M.mensaje,M.prederterminado as pre,M.estado');
    $this->db_campania->from('campanias_mensajes AS M');
    $this->db_campania->join('campania C', 'M.id_campania = C.id_logica'); 
    $this->db_campania->where('C.id_logica',$id_query);
    $this->db_campania->order_by("M.id_mensaje", "ASC");
    $query = $this->db_campania->get();        
    //var_dump($this->db_campania->last_query());die;
    if ($query->num_rows() > 0) {
        return $query->result_array();
    }else{
        return -1 ; 

    }
 }

 public function campain_sms_testing($sql){

    $query2 = $this->db_maestro->query($sql);
    $arreglo=[];
    foreach ($query2->list_fields() as $field)
    {
             $arreglo['campos'][]=$field;
    }
    
    $data  = $query2->result_array();
    $arreglo['result']=$data;
        //echo $this->db_maestro->last_query(); die;
    //var_dump($arreglo);die;
    return $arreglo;
    
           
  }

}
