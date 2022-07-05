<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'third_party/REST_Controller.php';
require APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

class Reportes extends REST_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->model('Solicitud_m');
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_parametria  = $this->load->database('parametria', TRUE);
    }

    private function conect_curl($data){
        
        if ($data[0]->clientid == false) {
            $data[0]->clientid = '1561616662.1574979832';
        }

        //https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=1561616662.1574979832&ec=Test&ea=UTMs&cn=test%20campaign&cs=Facebook_test&cm=cpc&ni=1

        if ($data[0]->tipo_solicitud === 'RETANQUEO') {
            $url = "https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=".$data[0]->clientid."&ec=Retanqueo&ea=Dinero%20Desembolsado&ev=".(int)($data[0]->total_devolver - $data[0]->capital_solicitado)."&el=".$data[0]->tipo_solicitud."&ni=1";
            $update = array('informado_google' => 1);
            $this->db_solicitudes->where('id_solicitud', $data[0]->id);
            $this->db_solicitudes->update('solicitud_txt', $update);
            checkDbError($this->db_solicitudes);

            $ddf = fopen(APPPATH.'logs/conect_curl.log','a');
            fwrite($ddf,"[".date("Y-m-d H:m:s")."] RETANQUEO URL: $url\r\n");
            fclose($ddf);
        }else{
            //  GCLID DEFINIDO
            if ($data[0]->gclid !== 'undefined' || $data[0]->gclid !== null) {
                $url = "https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=".$data[0]->clientid."&ec=Desembolso&ea=Dinero%20Desembolsado&ev=".(int)($data[0]->total_devolver - $data[0]->capital_solicitado)."&gclid=".$data[0]->gclid."&ni=1";
                //echo "<br/>caso 1: ".$url;
                $ddf = fopen(APPPATH.'logs/conect_curl.log','a');
                fwrite($ddf,"[".date("Y-m-d H:m:s")."] GCLID DEFINIDO URL: $url\r\n");
                fclose($ddf);
            }
            elseif (($data[0]->gclid === 'undefined' || $data[0]->gclid === null) && ( $data[0]->utm_medium !== 'undefined' || $data[0]->utm_medium !== null)) {
               $url = "https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=".$data[0]->clientid."&ec=Desembolso&ea=Dinero%20Desembolsado&ev=".(int)($data[0]->total_devolver - $data[0]->capital_solicitado)."&cn=".$data[0]->utm_campaign."&cs=".$data[0]->utm_source."&cm=".$data[0]->utm_medium."&ni=1 ";
               //echo "<br/>caso 2: ". $url;
               $ddf = fopen(APPPATH.'logs/conect_curl.log','a');
               fwrite($ddf,"[".date("Y-m-d H:m:s")."] utm_medium DEFINIDO URL: $url\r\n");
               fclose($ddf);
            }
            elseif (($data[0]->gclid === 'undefined' || $data[0]->gclid === null) && ( $data[0]->utm_medium === 'undefined' || $data[0]->utm_medium === null)) {
               $url = "https://www.google-analytics.com/collect?v=1&t=event&tid=UA-126952508-3&cid=".$data[0]->clientid."&ec=Desembolso&ea=Dinero%20Desembolsado&ev=".(int)($data[0]->total_devolver - $data[0]->capital_solicitado)."&ni=1";
               //echo "<br/>caso 3: ". $url;
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

    public function generar_txt_bbva_get(){

       
        $response = array_base();
        /**
         * inicio una nueva transacción
         */
        $this->db_solicitudes->trans_begin();
        /**
         * CONTROL de duplicación de desembolsos
         */
        $this->db_solicitudes->select('sub_txt.id_solicitud');
        $this->db_solicitudes->from('solicitud_txt sub_txt');
        $subQuery = $this->db_solicitudes->get_compiled_select();
        /**
         * consulta por la solicitud
         */            
        $this->db_solicitudes->select('solicitud.*, '.$this->db_parametria->database.'.ident_tipodocumento.*, solicitud_condicion_desembolso.capital_solicitado');
        $this->db_solicitudes->from('solicitud');
        $this->db_solicitudes->join($this->db_parametria->database.'.ident_tipodocumento',$this->db_parametria->database.'.ident_tipodocumento.id_tipoDocumento = solicitud.id_tipo_documento');
        $this->db_solicitudes->join('solicitud_condicion_desembolso', 'solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'left');
        $this->db_solicitudes->where('estado',Solicitud_m::ESTADO_APROBADO);
        $this->db_solicitudes->where("solicitud.id NOT IN ($subQuery)", NULL, FALSE);

        $query = $this->db_solicitudes->get();
        //echo $this->db_solicitudes->last_query();die;
        checkDbError($this->db_solicitudes);

        $result_db_solicitudes = ($query!==false && $query->num_rows() > 0) ? $query->result() : false;

        $body = '';
        $arrayDatos = [];
        $arrayGoogle = [];

        if($result_db_solicitudes !== false){
            foreach ($result_db_solicitudes as $key => $solicitud) {
                $Codigo_Banco_receptor = '';
                $Numero_de_Cuenta_BBVA = '';
                $Tipo_de_cuenta_Nacham = '';
                $Numero_de_cuenta_Nacham = '';

                $datos_bancarios = $this->Solicitud_m->getDatosBancariosTXT($solicitud->id);
                
                if($datos_bancarios !== false){
                    $id_tipo_cuenta = $datos_bancarios->id_tipo_cuenta;
                    $Numero_de_Cuenta_BBVA = $datos_bancarios->numero_cuenta;
                    $Codigo_Banco_receptor = $datos_bancarios->codigo;

                    if($Codigo_Banco_receptor=='0013'){
                        $Tipo_de_cuenta_Nacham = '00';

                        $ultimo_seis_cbu = substr($Numero_de_Cuenta_BBVA, -6);
                        
                        $longitud = strlen($Numero_de_Cuenta_BBVA);
                        $recortada = $longitud-6;
                        $resultado = substr($Numero_de_Cuenta_BBVA, 0, $recortada);
                        $codigo_oficina = txt_number($resultado, 4);

                        $tipo_cuenta = '0000';
                        if($id_tipo_cuenta==1){
                            $tipo_cuenta = '0100';
                        }else if($id_tipo_cuenta==46){
                            $tipo_cuenta = '0200';
                        }

                        $Numero_de_Cuenta_BBVA = $codigo_oficina . '00' . $tipo_cuenta . $ultimo_seis_cbu;

                        $Numero_de_cuenta_Nacham = txt_number(0,17);
                    }else{
                        if($id_tipo_cuenta==1){
                            $Tipo_de_cuenta_Nacham = '01';
                        }else if($id_tipo_cuenta==46){
                            $Tipo_de_cuenta_Nacham = '02';
                        }
                        $Numero_de_cuenta_Nacham = txt_texto($Numero_de_Cuenta_BBVA,17);
                        $Numero_de_Cuenta_BBVA = '0';
                    }
                }else{
                    //echo "Id solicitud: ".$solicitud->id." no tiene datos bancarios \r\n";                    die;
                }
                //columnas del excel
                $Tipo_identificación_receptor = $solicitud->codigo;
                $Numero_Identificacion_receptor = $solicitud->documento.'0';
                $Foma_de_pago = 1;
                
                $capital_solicitado = explode('.', $solicitud->capital_solicitado);
                
                $Vr_operacion_parte_entera = $capital_solicitado[0];
                $Vr_operacion_parte_decimal = 0; //isset($capital_solicitado[1]) ? $capital_solicitado[1] : 0;//siempre 00
                $Anio = 0;
                $Mes  = 0;
                $Dia  = 0;
                $Codigo_Oficina_pagadora = 0;
                $Nombre_beneficiario = trim($solicitud->nombres) . ' ' . trim($solicitud->apellidos);
                $Nombre_beneficiario = cleanString($Nombre_beneficiario);

                $Dirección_No_1 = 'BOGOTA';
                $Dirección_No_2 = '';
                $Email = '';
                $Concepto_1 = 'PRESTAMO';

                //columnas del txt
                $body .= txt_number($Tipo_identificación_receptor,2);
                $body .= txt_number($Numero_Identificacion_receptor,16);
                $body .= txt_number($Foma_de_pago,1);
                $body .= txt_number($Codigo_Banco_receptor,4);
                $body .= txt_number($Numero_de_Cuenta_BBVA,16);
                $body .= txt_number($Tipo_de_cuenta_Nacham,2);
                $body .= $Numero_de_cuenta_Nacham;
                $body .= txt_number($Vr_operacion_parte_entera,13);
                $body .= txt_number($Vr_operacion_parte_decimal,2);
                $body .= txt_number($Anio,4);
                $body .= txt_number($Mes,2);
                $body .= txt_number($Dia,2);
                $body .= txt_number($Codigo_Oficina_pagadora,4);
                $body .= txt_texto($Nombre_beneficiario,36);
                $body .= txt_texto($Dirección_No_1,36);
                $body .= txt_texto($Dirección_No_2,36);
                $body .= txt_texto($Email,48);
                $body .= txt_texto($Concepto_1,40);

                //datos de mail
                $arrayDatos[$solicitud->id]['id']        = $solicitud->id;
                $arrayDatos[$solicitud->id]['documento'] = $solicitud->documento;
                $arrayDatos[$solicitud->id]['nombre']    = trim($solicitud->nombres);
                $arrayDatos[$solicitud->id]['apellido']  = trim($solicitud->apellidos);
                $arrayDatos[$solicitud->id]['monto']     = $Vr_operacion_parte_entera . '.' . $Vr_operacion_parte_decimal;

                array_push($arrayGoogle, $solicitud->id);
                
                $data = array(
                    'estado' => Solicitud_m::ESTADO_TRANSFIRIENDO,
                );
                $this->db_solicitudes->where('id', $solicitud->id);
                $this->db_solicitudes->limit(1);
                $this->db_solicitudes->update('solicitud', $data);
                checkDbError($this->db_solicitudes);
                /**
                 * inserto la huelle de que esta solicitud ya fue a transfiriendo
                 */
                $filename = date('Ymd_Hi').'_BBVA.txt';
                $data = array(
                    'id_solicitud' => $solicitud->id,
                    'id_banco' => 28, //BBVA COLOMBIA DE PARAMETRIA.bank_entidades,
                    'ruta_txt' => $filename,
                    'fecha_procesado' => date('Y-m-d H:i:s')
                    
                );
                $this->db_solicitudes->insert('solicitud_txt', $data);
                checkDbError($this->db_solicitudes);

                if($key+1 == count($result_db_solicitudes))
                    break;
                else
                    $body .= BR;
            }
            /**
             * controla las transacciones de solicitudes
             */
            $return = $this->db_solicitudes->trans_status();

            if ($return === FALSE){
                $this->db_solicitudes->trans_rollback();
                $response['title_response'] = 'Ocurrió un error interno.';
                $this->response($response, parent::HTTP_OK);
            }else{
                $this->db_solicitudes->trans_commit();                
                /**
                 * escribe el archivo con el contenido
                 */
                $filename = date('Ymd_Hi').'_BBVA.txt';
                $dir_file = APPPATH . '/logs/';
                $file_dir = $dir_file . $filename;
                $file = fopen($file_dir,"w");
                $useEncoding = true;
                if($useEncoding){
                    $body = iconv( mb_detect_encoding( $body ), 'Windows-1252//TRANSLIT', $body );
                }
                fwrite($file, $body);
                /**
                 * genero el body del mail
                 */
                $message = self::generate_body_mail($arrayDatos, $filename);
                /**
                 * envio el mail
                 */
                $isTest = false;
                if($isTest === true){
                    $bcc = 'luis.hernandez@solventa.com.ar';
                    $target = 'sebastian.marquez@solventa.com.ar';
                    $resultSend = self::send_mail_bbva($message, $target, $file_dir, $filename, $bcc);                    
                }else{
                    $target    = 'administracion@solventa.com';
                    $targetBcc = 'hector.lema@solventa.com.ar';
                    $cc        = 'sebastian.marquez@solventa.com.ar';
                    
                    $resultSend    = self::send_mail_bbva($message, $target, $file_dir, $filename, $targetBcc);
                    $resultSendBcc = self::send_mail_bbva($message, $cc, $file_dir, $filename);
                }


                $result = json_decode($resultSend);

                if( true || $result->status == 200){
                    //$this->db_solicitudes->trans_commit();
                    $response['success'] = true;
                    $response['title_response'] = 'Se envió adjunto el txt con los pagos a realizar a ' . $target;
                    // SE ENVIAN DATOS A GOOGLE

                    if($isTest !== true){
                        for ($g=0; $g < count($arrayGoogle); $g++) {
                            set_time_limit(0);

                            $this->db_solicitudes->select('solicitud.id,solicitud.clientid,solicitud.gclid,solicitud.utm_medium,solicitud.utm_source,solicitud.utm_campaign, solicitud.tipo_solicitud, solicitud_condicion_desembolso.capital_solicitado, solicitud_condicion_desembolso.total_devolver');
                            $this->db_solicitudes->from('solicitud');
                            $this->db_solicitudes->join('solicitud_condicion_desembolso', 'solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'left');
                            $this->db_solicitudes->where('solicitud.id', $arrayGoogle[$g]);
                            $queryGoogle = $this->db_solicitudes->get();
                            checkDbError($this->db_solicitudes);
                            $result_db = ($queryGoogle!==false && $queryGoogle->num_rows() > 0) ? $queryGoogle->result() : false;
                            if ($result_db) {
                               $this->conect_curl($result_db);
                            }
                            
                        }
                    }
                }                
            }
        }else{
            $response['title_response'] = 'No se encontraron pagos para informar.';
        }
        $this->response($response, 200);
        exit;
        /**
         * descarga el txt
         */
        /*header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Length: ". filesize("$file_dir").";");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/octet-stream; "); 
        header("Content-Transfer-Encoding: binary");

        readfile($file_dir);*/

        die;        
    }
    public static function generate_body_mail($arrayDatos = '', $filename = ''){
        ob_start();
        ?>
        <table border="0" width="800px">
            <thead>
                <tr>
                    <th colspan="5" align="left"><?=date("d/m/Y H:i")?>hs.</th>
                </tr>
                <tr>
                    <th colspan="5" align="left"><?=$filename?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" align="left">Créditos a cancelar banco: BBVA</td>
                </tr>
                <tr>
                    <td style="width:20%;"  align="right">Id_solicitud</td>
                    <td style="width:20%;">Documento</td>
                    <td style="width:20%;">Nombre</td>
                    <td style="width:20%;">Apellido</td>
                    <td syle="width:20%;"  align="right">Monto a Pagar</td>
                </tr>
                <?php
                $total_transferir = 0;
                if(count($arrayDatos) > 0){ 
                    foreach ($arrayDatos as $id_solicitud => $value) { ?>
                    <tr>
                        <td align="right"><?=$value['id']?></td>
                        <td><?=$value['documento']?></td>
                        <td><?=$value['nombre']?></td>
                        <td><?=$value['apellido']?></td>
                        <td align="right">$<?=formatMontoMillares($value['monto'],0)?></td>
                    </tr>
                    <?php 
                    $total_transferir += $value['monto'];
                    }
                } ?>
            </tbody>
            <tfoot>
                <tr><td colspan="5">&nbsp;</td></tr>
                <tr>
                    <td colspan="2">Total de clientes a pagar por BBVA</td>
                    <td colspan="3"><strong><?=count($arrayDatos)?></strong></td>
                </tr>
                <tr>
                    <td colspan="2">Total de pesos a pagar por BBVA</td>
                    <td colspan="3"><strong>$<?=formatMontoMillares($total_transferir,0)?></strong></td>
                </tr>
                <tr><td colspan="5">&nbsp;</td></tr>
                <tr>
                    <td colspan="2">TOTAL UNIDADES A PAGAR</td>
                    <td colspan="3"><strong><?=count($arrayDatos)?></strong></td>
                </tr>
                <tr>
                    <td colspan="2">TOTAL PESOS A PAGAR</td>
                    <td colspan="3"><strong>$<?=formatMontoMillares($total_transferir,0)?></strong></td>
                </tr>
                <tr><td colspan="5">&nbsp;</td></tr>
            </tfoot>                
        </table>
        <?php
        $page = ob_get_contents();
        ob_end_clean();
        return $page;
    }
    public function send_mail_bbva($message = '', $target = '', $full_path_txt = '', $filename = '', $bbc = ''){
        
        //$url_api_medios_de_pago = 'http://sendmail.solventa.local/api/sendmail'; //Desarrollo
        $url_api_medios_de_pago = URL_SEND_MAIL.'api/sendmail';   //Produccion
        
        $body = array (
            "jwt"       => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzMxNjExNjgsImV4cCI6MTU3MzE2NDc2OCwiZGF0YSI6eyJpZCI6IjYyNTgiLCJhZG1pbiI6ZmFsc2UsInRpbWUiOjE1NzMxNjExNjgsInRpbWVUb2xpdmUiOm51bGx9fQ.gsGPp2FXAk4I7KEXPNuleh6kqYP5ahWYud-baZBpOFE",
            "from"      => "hola@solventa.com",
            'to'        => $target,
            'from_name' => 'Solventa Colombia',
            'subject'   => 'Pago de prestamos BBVA txt ' . $filename,
            'template'  => 0,
            'message'   => $message
        );
        if($bbc!=''){
            $body['cc'] = $bbc;
        }
        if(file_exists($full_path_txt)){
            $files = array();
            $files['file'] = $full_path_txt;

            array_walk($files, function($filePath, $key) use(&$body) {
                $body[$key] = curl_file_create($filePath);
            });
        }

        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_TIMEOUT, 30);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });

        $headers = array('Content-Type' => 'multipart/form-data');

        $response = Requests::post($url_api_medios_de_pago, $headers, array(), array('hooks' => $hooks));

        return ($response->body);
    }
    public function testmail_get(){
        $message = 'testmail_get files PLAIN';
        $localIp = gethostbyname(gethostname());
        
        $url_api_medios_de_pago = 'http://sendmail.solventa.local/api/sendmail'; //Desarrollo
        $url_api_medios_de_pago = URL_SEND_MAIL.'api/sendmail';   //Produccion

        // if it is a multipart forma data form
        $body = array (
            //"jwt" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzEyNjE4MDcsImV4cCI6MTU3MTI2NTQwNywiZGF0YSI6eyJpZCI6IjEiLCJhZG1pbiI6dHJ1ZSwidGltZSI6MTU3MTI2MTgwNywidGltZVRvbGl2ZSI6bnVsbH19.EjL-hI9PKhF9p84Id425mdYHo0LmQINtW8MrKpXFX5U",
            "jwt" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzMxNjExNjgsImV4cCI6MTU3MzE2NDc2OCwiZGF0YSI6eyJpZCI6IjYyNTgiLCJhZG1pbiI6ZmFsc2UsInRpbWUiOjE1NzMxNjExNjgsInRpbWVUb2xpdmUiOm51bGx9fQ.gsGPp2FXAk4I7KEXPNuleh6kqYP5ahWYud-baZBpOFE",
            "from" => "sebastian.marquez@solventa.com.ar",
            'to' => 'a.sebastian.marquez@gmail.com',
            'from_name' => 'Solventa Colombia',
            'subject' => 'Pago de prestamos BBVA',
            'template' => 0,
            'message' => $message,
        );

        $files = array();
        $files['file'] = 'C:/www\backend-colombia/application/logs/txt_bbva_20191106115126.txt';

        array_walk($files, function($filePath, $key) use(&$body) {
            $body[$key] = curl_file_create($filePath);
        });

        $headers = array('Content-Type' => 'multipart/form-data');
            
        $hooks = new Requests_Hooks();

        $hooks->register('curl.before_send', function($fp) use ($body){
            curl_setopt($fp, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($fp, CURLOPT_POSTFIELDS, $body);
        });

        $response = Requests::post($url_api_medios_de_pago, $headers, array(), array('hooks' => $hooks));
    }
    public function mover_solicitudes_a_txt_get(){
        $this->db_solicitudes->trans_begin();
        /**
         * consulta por la solicitud
         */
        $this->db_solicitudes->select('sub_txt.id_solicitud');
        $this->db_solicitudes->from('solicitud_txt sub_txt');
        $subQuery = $this->db_solicitudes->get_compiled_select();

        $this->db_solicitudes->select('solicitud.*, '.$this->db_parametria->database.'.ident_tipodocumento.*, solicitud_condicion_desembolso.capital_solicitado');
        $this->db_solicitudes->from('solicitud');
        $this->db_solicitudes->join($this->db_parametria->database.'.ident_tipodocumento',$this->db_parametria->database.'.ident_tipodocumento.id_tipoDocumento = solicitud.id_tipo_documento');
        $this->db_solicitudes->join('solicitud_condicion_desembolso', 'solicitud_condicion_desembolso.id_solicitud = solicitud.id', 'left');
        $this->db_solicitudes->where_in('estado',[Solicitud_m::ESTADO_TRANSFIRIENDO, Solicitud_m::ESTADO_PAGADO]);
        
        $this->db_solicitudes->where("solicitud.id NOT IN ($subQuery)", NULL, FALSE);

        $query = $this->db_solicitudes->get();
        echo $this->db_solicitudes->last_query();

        checkDbError($this->db_solicitudes);

        $result_db_solicitudes = ($query!==false && $query->num_rows() > 0) ? $query->result() : false;

        if($result_db_solicitudes !== false){
            foreach ($result_db_solicitudes as $key => $solicitud) {
                echo "solicitud id: " . $solicitud->id . '<br>';
                $data = array(
                    'id_solicitud' => $solicitud->id,
                    'id_banco' => 28, //BBVA COLOMBIA DE PARAMETRIA.bank_entidades
                    
                );
                $this->db_solicitudes->insert('solicitud_txt', $data);
                checkDbError($this->db_solicitudes);
            }
        }else {
            echo 'No hay solicitudes para mover a txt.';
        }
        /**
         * controla las transacciones de solicitudes
         */
        $return = $this->db_solicitudes->trans_status();

        if ($return === FALSE){
            $this->db_solicitudes->trans_rollback();
            $response['title_response'] = 'Ocurrió un error interno.';
            $this->response($response, parent::HTTP_OK);
        }else{
            $this->db_solicitudes->trans_commit();
        }
    }
}