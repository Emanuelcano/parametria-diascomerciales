<?php
class AUTHORIZATION
{
    public static function validateTimestamp($token)
    {
        $CI    =& get_instance();
        $time  = time();
        $token = self::validateToken($token);
        if ($token != false /* && ($time < $token->exp) */ ) {
            return $token;
        }/* else if($token != false && $token->data->admin){
            return $token;
        } */
        return false;
    }
    public static function validateToken($token)
    {
        $CI =& get_instance();
        try {
            return JWT::decode($token, $CI->config->item('jwt_key'));
        } catch (\Throwable $th) {
            return false;
        }
    }
    public static function generateToken($data)
    {
        $CI =& get_instance();
        return JWT::encode($data, $CI->config->item('jwt_key'));
    }
    public static function getData($token)
    {
        $CI =& get_instance();
        return JWT::decode($token, $CI->config->item('jwt_key'))->data;
    }
    public static function validateGeneralToken($headers)
    {
        if(isset($headers['Origin']) && in_array($headers['Origin'], ALLOWED_URLS) ){
            if( !isset($headers['Authorization'])){
                echo 'Acceso no autorizado';
                die; 
            }
            else {
                if(sha1(FRONT_TOKEN) == $headers['Authorization']){
                    if(AUTHORIZATION::validateTimestamp(FRONT_TOKEN)){
                        return true;
                    }
                    return false;
                }
                else{
                    echo 'Token invalido';
                    die;
                }
            }
        } else{
            echo 'Acceso no autorizado';
            die; 
        }
    }
    /*
        $data    = Array de datos a encriptar en el payload
        $expTime = Tiempo a expirar el cifrado (Calculado en segundos)
    */
    public static function encodeData($data, $expTime = null)
    {
        if($expTime == null){
            $expTime = (60*60);
        }
        $time = time();
        $solData = [
            'iat' => $time, // Tiempo que inició el token
            'exp' => $time + $expTime, // Tiempo que expirará el token (+1 hora)
            'data' => $data
        ];
        $dataToEncrypt = self::generateToken($solData);
        $base64prefix  = base64_encode( rand(100000,999999) );
        $base64subfix  = base64_encode( rand(10000,99999) );
        //$dataEncripted = base64_encode( $dataToEncrypt );
        $encrypt_method = METHOD_ENCRYPT;
        //llaves
        $secret_key = PUBLIC_KEY_BACK;
        $secret_iv = SECRET_KEY_BACK;
        // ofuscar secret key
        $key = hash('sha256', $secret_key);
        // iv
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        //Creacion de hash
        $dataEncripted = openssl_encrypt($dataToEncrypt, $encrypt_method, $key, 0, $iv);
        $cipherData    = $base64prefix.$dataEncripted.$base64subfix;
        return $cipherData;
        //var_dump($cipherData);die;
    }

    public static function decodeDataEncript($encrypted)
    { 
        $sinprefix        = substr($encrypted, 8);
        //print_r($sinprefix);die;
        $sinsubfix        = substr( $sinprefix, 0, -8);
        $encrypt_method = METHOD_ENCRYPT;
        //llaves
        $secret_key = PUBLIC_KEY_BACK;
        $secret_iv = SECRET_KEY_BACK;
        // ofuscar secret key
        $key = hash('sha256', $secret_key);
        // iv
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        //Creacion de hash

        try{
            $dataDesencripted = openssl_decrypt($sinsubfix, $encrypt_method, $key, 0, $iv);
            //print_r($dataDesencripted);die;
            if ($dataDesencripted) {
                    if(self::validateToken($dataDesencripted)){
                        //print_r(self::getData($dataDesencripted));die;
                        return self::getData($dataDesencripted);
                        
                    }
                    else{
                        //print_r("Token invalido!");
                        show_404();
                        
                    }
            }else{

                    show_404();

            }

            
            
        }
        catch (\Throwable $th) {
            return false;
        }
        // var_dump($dataDesencripted);die;
    }

    public static function crearFicheroSolicitud($mensaje){
        
        $carpeta = 'logs/';
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }
            $fecha = date("Y-m-d H:i:s");
            $file = fopen("logs/track_seguridad_comunicaciones.txt", "a");
            fwrite($file, $mensaje);
            //fwrite($file, '');
            fclose($file);
        return $file;
    }
    public static function trackear_error($post,$header,$ip,$metodo)
    {
                
                $datos_request[]['metodo'] = $metodo;
                $datos_request[] = $post;
                $datos_request[] = $header;
                $datos_request[]['ip'] = $ip;
                $str_datos  = file_get_contents("http://api.ipinfodb.com/v3/ip-city/?key=45abd2951ee0a74973b579544185c02820ca02a4a692f615786a68d9e7e8903a&ip=".$ip."&format=json");
                $datos      = json_decode($str_datos,true);
                $now = date("Y-m-d H:m:s");
                $datos_request[]['ip'] = $datos;
                self::crearFicheroSolicitud('####### INICIO SOLICITUD '.$now.' ##############'.PHP_EOL);
                self::crearFicheroSolicitud(json_encode($datos_request).PHP_EOL);
                self::crearFicheroSolicitud('####### FIN SOLICITUD '.$now.' ##############'.PHP_EOL);
    }

}