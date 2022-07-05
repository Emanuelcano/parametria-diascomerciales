<?php
/**
 * @author   Sebastian Marquez <sebastian.marquez@solventa.com.ar>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
define('BR',"\r\n");
define('BRK',"<br>");

function generateCode($q = 10)
{
    $random = mt_rand(100000015,mt_getrandmax());    
    $code = substr($random,0,$q);
    return $code;
}
/**
 * chequea si la ejecución de un query tiene un error y guarda el log
 * @param CI_DB_mysqli_driver $resource
 * @return void
 */
function checkDbError(CI_DB_mysqli_driver $resource){
    $CI =& get_instance();
    $CI->load->library('custom_log');

   if ($resource->error()['code']!=0){
        $CI->custom_log->write_log("ERROR DB",$resource->error());
        $CI->custom_log->write_log("ERROR", $resource->last_query());
   }
    
    return;
}
/**
 * genera los alias de los campos de una tabla
 * @param $table es el nombre de la tabla
 * @param $prefijo es el prefijo que le quiero agregar al campo
 * esto sirve para querys con join que requieren sumar todos los campos a la query y evitar el 'ambiguis field'
 */
function get_fields($table = '', $prefijo = '')
{
    $CI =& get_instance();
    $data = array();
    $result = $CI->db->list_fields($table);
    foreach($result as $field)
    {
        $data[] = $prefijo != '' ? $prefijo . '.' . $field . ' AS ' . $prefijo . '_' . $field : $field;
    }
    $fields = implode(',', $data);
    return $fields;
}
/**
 * funciones de exportacion txt
 */
function getFormatNumber($number = "",$length=0,$string="",$type = STR_PAD_LEFT){
    $lastNumber = str_pad($number, $length, $string, $type);
    return $lastNumber;
}
function mb_str_pad( $input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
{
    $diff = strlen( $input ) - mb_strlen( $input );
    return str_pad( $input, $pad_length + $diff, $pad_string, $pad_type );
}
function getLimitedText($Name, $visible_characters=10, $end = true) {
    $texto_end = $end ? '...' : '';
    return (strlen(trim($Name)) <= $visible_characters) ? $Name : substr($Name, 0, $visible_characters) . $texto_end;
}

function txt_texto($texto = '', $limite_texto = 0,$type = STR_PAD_RIGHT){
    $texto = getLimitedText($texto, $limite_texto, false);
    $texto = mb_str_pad($texto, $limite_texto, " ", $type);
    return $texto;
}
function txt_number($texto = '', $limite_texto = 0,$type = STR_PAD_LEFT){
    $texto = getLimitedText($texto, $limite_texto, false);
    $texto = str_pad($texto, $limite_texto, "0", $type);
    return $texto;
}
/**
 * formato de montos
 */
function formatMontoMillares($monto = 0, $decimal = 2){
    return formatMonto($monto,true, $decimal);
}
function formatMonto($monto=0, $con_millares = false, $decimal = 2)
{
    if($con_millares){
        if($decimal==0)
            $monto = intval($monto);
        return number_format($monto, $decimal);
    }else{
        return number_format($monto, $decimal,".","");
    }
}
function format_price($ammount = 0, $decimal = 2, $separador_decimal = '.'){
    return number_format($ammount, $decimal, $separador_decimal, '');
}

function cleanString($text) {
    $utf8 = array(
        '/[áàâãªä]/u'   =>   'a',
        '/[ÁÀÂÃÄ]/u'    =>   'A',
        '/[ÍÌÎÏ]/u'     =>   'I',
        '/[íìîï]/u'     =>   'i',
        '/[éèêë]/u'     =>   'e',
        '/[ÉÈÊË]/u'     =>   'E',
        '/[óòôõºö]/u'   =>   'o',
        '/[ÓÒÔÕÖ]/u'    =>   'O',
        '/[úùûü]/u'     =>   'u',
        '/[ÚÙÛÜ]/u'     =>   'U',
        '/ç/'           =>   'c',
        '/Ç/'           =>   'C',
        '/ñ/'           =>   'n',
        '/Ñ/'           =>   'N',
        '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
        '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
        '/[“”«»„]/u'    =>   ' ', // Double quote
        '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
    );
    return preg_replace(array_keys($utf8), array_values($utf8), $text);
}
/**
 * diferencia de dias entre 2 fechas
 */
function date_diff_days($inicio='',$fin=''){
    $datetime1 = new DateTime($inicio);
    $datetime2 = new DateTime($fin);
    $interval = $datetime1->diff($datetime2);
    return  intval($interval->format('%R%a'));        
}

function arreglar_prestamo($prestamo){         
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
    
 
 function sanear_string($string)
    {

        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C',),
            $string
        );

        //$string = str_replace(array("#"), '', $string);
        $string = str_replace(array("@"), '', $string);
        $string = str_replace(array("|"), '', $string);

        // $string = str_replace( array("!"),'',$string);
        // $string = str_replace( array("$"),'',$string);

        $string = str_replace(array("%"), '', $string);
        $string = str_replace(array("·"), '', $string);
        $string = str_replace(array(">"), '', $string);
        $string = str_replace(array("<"), '', $string);

        return $string;
    }
  
   function arreglar_string($nombres){
    $nombres = sanear_string($nombres);
    $trozos = explode(" ", $nombres);
    $nombres = ucwords(strtolower($trozos[0]));
    return $nombres;
}


/**
 * Formato para los mensajes de whatapp
 */

function mensaje_whatapp_maker($template, $id_solicitud){
    $ci =& get_instance();
    $ci->load->model('Chat', 'chat', TRUE);
    //$ci->load->model('Solicitud_m', 'solicitud_model', TRUE);
    
    $response['ok'] = FALSE;
    $response['message'] ="";
    //buscamos la informacion del template a enviar
    //$detalle_template = $ci->chat->get_template_details_by(['id_template_twilio' => $template]);
    $detalle_template = $ci->chat->get_template_details_by(['id_template' => $template]);
    $detalle_template_dinamico=[];

    //var_dump($detalle_template);die;
    if(!empty($detalle_template)){
        $mensaje = $detalle_template[0]->msg_string;

            $response['message'] =  $mensaje ;
            if(!is_null($detalle_template[0]->variable)){

            
                foreach ($detalle_template as $key1 => $template_variable) {
                    if($template_variable->tipo == 2){
                        $valor_variable = formato_variable($template_variable->formato,$template_variable->campo);
                        $mensaje = str_replace("{{".$template_variable->variable."}}",$valor_variable, $mensaje);
                        $response['message'] = $mensaje;
                        $response['ok'] =TRUE;
                    } else{
                            array_push($detalle_template_dinamico, $template_variable);
                    }
                }
                /*echo '<pre>';
                var_dump($detalle_template_dinamico);die;*/
                
                if(!empty($detalle_template_dinamico)){
                    $query_variables = construir_query($detalle_template_dinamico);
                    $query = traducir_variable_global($query_variables, 'solicitud', $id_solicitud);

                    //se tiene el query con las variables globales reemplazadas
                    if($query['ok']){
                        $result = $ci->chat->get_query_result($query['result']);
                        //obtenemos resultados en la query
                        if(!empty($result)){
                            $result= $result[0];
                            //por cada variabre del template
                            foreach ($detalle_template_dinamico as $key => $value) {
                                preg_match_all('/\{\{[0-9]+\}\}/', $mensaje, $matches);
                                $matches = $matches[0];
                                $aux = (explode('.', $value->campo));
                                $campo = array_pop($aux);
                                
                            
                                //sustituimos variables del template
                                foreach ($matches as $key2 => $variable) {
                                    //if($variable == "{{".$value->variable."}}")
                                
                                    $valor_variable = formato_variable($value->formato, ((isset($result["$campo"]))? $result["$campo"]:$result["$value->campo"]));
                                    $mensaje = str_replace( "{{".$value->variable."}}", $valor_variable, $mensaje);
                                }
                            }
                            

                            $response['message'] = $mensaje;
                            $response['ok'] =TRUE;
                        }else{
                            $response['message'] = "hubo un problema al consultar las variables";
                            $response['ok'] =FALSE;
                        }
                    } else{
                        $response['message'] = $query['message'];
                        $response['ok'] =FALSE;
                    }
                }
        
            }else{
                $response['message'] = $mensaje;
                $response['ok'] =TRUE;
            }
    }
    
    
    return $response;

}


function traducir_variable_global($texto, $contexto = null, $id_solicitud) {
    $ci =& get_instance();
    $ci->load->model('Metrics_model', 'parametria', TRUE);
    $ci->load->model('Chat', 'chat', TRUE);


    $response['ok'] = TRUE;
    $response['message'] ="";
    //busco las variables
    $cadena = str_replace('$id_solicitud', $id_solicitud, $texto);
    $cadena =  str_replace('$id_operador', (!is_null($ci->session->userdata('idoperador')))?$ci->session->userdata('idoperador'):'192', $cadena);
    $response['result'] = $cadena;
    
    preg_match_all('/\$[\w]+/', $cadena, $matches);
    $matches = $matches[0];
    
    
    foreach ($matches as $key => $value) {
        $detalle_template = $ci->parametria->get_variable_global(['nombre_variable' => $value, 'contexto' => $contexto]);
        if(!empty($detalle_template)){
            
            $query = construir_query($detalle_template);
            $query = str_replace('$id_solicitud', $id_solicitud, $query);
            $result = $ci->chat->get_query_result($query);
            
            if(!empty($result)){
                $result = $result[0];
               
            //var_dump($result);die;
                if(!is_null(array_values($result)[0]) && !in_array('**', $result) ){
                    $cadena = str_replace($value, array_values($result)[0], $cadena);
                    $response['ok'] = TRUE;
                    $response['result'] = $cadena;

                }else{
                    $response['message']='no hay valor de variable global';
                    $response['ok'] = FALSE;    
                }
            } else{
                $response['message']='no hay valor de variable global';
                $response['ok'] = FALSE;
            }
        }else{
            $response['ok'] = FALSE;
            $response['message']='no hay configuracion de variable global';
        }
    }
    
    return $response;
}


function construir_query($configuracion){

    $select ="";
    $from ="";
    $where ="";
    $campos  =   array_column($configuracion, 'campo');
    
    //tablas
    $condiciones  =   array_column($configuracion, 'condicion');
    $tablas_aux = $tablas =[];
    foreach ($condiciones as $key2 => $condicion) {
        $tablas_aux = explode("AND",$condicion);
        
        foreach ($tablas_aux as $key3 => $sentencia) {
            $aux = explode("=",$sentencia);
            
            foreach ($aux as $key4 => $referencia) {
                $aux_ref = explode(".",$referencia);
                if(count($aux_ref) == 3){
                    array_pop($aux_ref);
                }
                if(count($aux_ref) == 2){
                    array_push($tablas,implode(".", array_map('trim',$aux_ref)));
                }
            }
        }
    }
    $aux =[];
    foreach ($campos as $key => $campo) {
        array_push($aux, "IFNULL($campo,'**') as '$campo'");
    }
    $select = implode(",",$aux);
    $from   = implode(",",array_unique($tablas));
    $where  = implode(" AND ",array_column($configuracion, 'condicion'));
    
    $query = "select $select from $from where $where";
   
    
    return $query;

}

function formato_variable($formato, $valor=''){

    $result="";
    switch ($formato) {
        case 'fecha':
            $hoy = $valor;
            if($valor == "CURRENT_DATE()")
                $hoy = date('Y-m-d');

                $result = date('d/m/Y',strtotime($hoy));
                //var_dump($result);die;
            break;

        case 'valor_completo':
            $result = $valor;
            break;
        
        case 'primer_nombre':
            $result = explode(' ',$valor)[0];
            break;

        case 'valor':
            $result = number_format(floatval($valor), 0,",",".");
            break;

        default:
            $result = $valor;
            break;
    }

    return $result;
}


/**
 * generar un txt
 * @param 
 */
function create_txt($content_file = '' ,$file_name = '', $dir_file = ''){
    if($dir_file != '')
        $dir_file = FCPATH . '/public/' . $dir_file . '/';
    else
        $dir_file = FCPATH . '/public/';

    $dir_anio = $dir_file . date('Y') . '/';
    $dir_mes  = $dir_anio . date('m') . '/';
    $dir_dia  = $dir_mes . date('d') . '/';

    if(!is_dir($dir_anio)){
        mkdir($dir_anio, 0755, true) || chmod($dir_anio, 0755);
        chmod($dir_anio, 0755);        
    }else{
        //echo "existe A: $absolute_base_destino...<br>";
    }
    if(!is_dir($dir_mes)){
        mkdir($dir_mes, 0755, true) || chmod($dir_mes, 0755);
        chmod($dir_mes, 0755);        
    }else{
        //echo "existe A: $dir_anio_destino...<br>";
    }

    if(!is_dir($dir_dia) /*&& is_writable($dir_mes_destino)*/){
        mkdir($dir_dia, 0755, true) || chmod($dir_dia, 0755);
        chmod($dir_dia, 0755);
    }else{
        //echo "existe B: $dir_mes_destino...<br>";
    }
    // fix slashe's
    $dir_file = str_replace("\\/", "/", $dir_dia);
    $dir_file = str_replace("\\", "/", $dir_file);

    $current_path = getcwd();

    chdir(APPPATH . '/helpers/');

    $full_path = $dir_file . $file_name;
    $file = fopen($full_path,"w");
    $useEncoding = true;
    if($useEncoding){
        $content_file = iconv( mb_detect_encoding( $content_file ), 'Windows-1252//TRANSLIT', $content_file );
    }
    fwrite($file, $content_file);
    fclose($file);
    $result = file_exists($full_path);
    
    chdir($current_path);

    if($result){
        return $full_path;
    }
    return false;
}

function SanearData($data)
{
    $CI =& get_instance();
    $dataSaneada = $CI->security->xss_clean(strip_tags($data));
    $dataSaneada =  preg_replace('/[#$%^&*()+=\\[\]\';\/{}|"<>?~\\\\]/', '', $dataSaneada);
    return $dataSaneada;
}

function search_word($message,$origen)
{
    if($origen == "ORIGINACION") //1 originacion 2 cobranzas
    {
        $file = fopen(realpath("docs/database/base_palabras_filtros.txt"), "r");
    }else{
        $file = fopen(realpath("docs/database/base_palabras_filtrosCobranzas.txt"), "r");

    }
         $rs_array = [];
        while(!feof($file)) {
            $base = fgets($file);
            $partes= explode(",",$base);
            foreach ($partes as $key => $value) {
                $parte2 = explode("|",$value);
                if(isset($parte2[0]) && isset($parte2[1]))
                {
                    $rs_array[]=['palabra'=>$parte2[0],'grupo'=>$parte2[1]];
                    
                }
                
                
            }
            
        }
        
        fclose($file);
        return($rs_array);#zz $rs_array;
}
function search_word_by_grupo($grupo,$origen)
{
    if($origen == "ORIGINACION") //1 originacion 2 cobranzas
    {

        $file = fopen(realpath("docs/database/base_palabras_filtros.txt"), "r");
    }else{
        $file = fopen(realpath("docs/database/base_palabras_filtrosCobranzas.txt"), "r");

    }
         $rs_array = [];
        while(!feof($file)) {
            $base = fgets($file);
            $partes= explode(",",$base);
            foreach ($partes as $key => $value) {
                $parte2 = explode("|",$value);
                if((isset($parte2[0]) && isset($parte2[1]) && $parte2[1]==$grupo))
                {
                    $rs_array[]=['palabra'=>$parte2[0],'grupo'=>$parte2[1]];
                    
                }
                
                
            }
            
        }
        
        fclose($file);
        return($rs_array);#zz $rs_array;
}

function search_word_by_word($word,$origen)
{

    if($origen == "ORIGINACION") //1 originacion 2 cobranzas
    {

        $file = fopen(realpath("docs/database/base_palabras_filtros.txt"), "r");
    }else{
        $file = fopen(realpath("docs/database/base_palabras_filtrosCobranzas.txt"), "r");

    }
         $rs_array = [];
        while(!feof($file)) {
            $base = fgets($file);
            $partes= explode(",",$base);
            foreach ($partes as $key => $value) {
                $parte2 = explode("|",$value);
                
                if((isset($parte2[0]) && isset($parte2[1]) && $parte2[0]==$word))
                {
                    $rs_array[]=['palabra'=>$parte2[0],'grupo'=>$parte2[1]];
                    
                }
                
                
            }
            
        }
        fclose($file);
        return($rs_array);#zz $rs_array;
}
function reprocesar($mensaje)
{
    
    $DataInsert = [];
    // if ()
    // {

    // }
    foreach ($mensaje as $key => $value) {
        $operador2 = explode(":* ",$value);
        // var_dump(trim($operador2[0]));
        if (!empty($operador2[0]) && !empty($operador2[1])){
            if(trim($operador2[0]) == "*Operador"){
                $signos = array("\r");
                $palabrastoUpdate = str_replace($signos, "", $operador2[1]);
                $DataInsert['operador'] = $palabrastoUpdate;
            }else  if(trim($operador2[0]) == "*Documento"){
                $signos = array("\r");
                $palabrastoUpdate = str_replace($signos, "", $operador2[1]);
                $DataInsert['num_documento'] = $palabrastoUpdate;


            }else  if(trim($operador2[0]) == "*Telefono"){
                $signos = array("\r");
                $palabrastoUpdate = str_replace($signos, "", $operador2[1]);
                $DataInsert['num_contacto'] = $palabrastoUpdate;


            }else  if(trim($operador2[0]) == "*Mensaje emitido"){
                $signos = array("{","[","}","]",'"',"*","\r");
                $palabrastoUpdate = str_replace($signos, "", $operador2[1]);
                $DataInsert['palabras'] = $palabrastoUpdate;

            }

            
        }
        
    }
    // var_dump($DataInsert);die;
    return ($DataInsert);
} 

function procesartexto($message)
{

    $signos = array("¿", "?", "(", ")", "=", "/", "&", "%", "$", "#",'"',"¡","!","|","°",",",".",":",";","-","_","´","¨","+","*","{","[","}","]","1","2","3","4","5","6","7","8","9","0","+","-","*","/");
    $onlymessage = str_replace($signos, " ", strtolower($message));
    $msgsinespacios=explode(" ",$onlymessage);
    return $msgsinespacios;
}
