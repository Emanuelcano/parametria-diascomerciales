<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

   
class RecibirSms extends REST_Controller {

    public function __construct($config = 'rest')
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
       
        parent::__construct(); 
        $this->load->model('InfoBipModel','',TRUE);  
        $this->load->helper(array('form', 'url'));
        $this->load->config('form_validation');
        $this->load->library('form_validation');

    }


    public function Enviar_post(){
       $resultados = array(array( 
               "messageId" => "5908971644001839114",
               "to"=> "41793026727",
               "receivedAt"=> "2015-03-01T12:54:44.560+0000",
               "from" => "385998779111",
               "text" => "HEY Pedro",
               "cleanText" => "hello world",
               "keyword" => "HEY",
               "smsCount" => 1
        ));
        $datosCod = json_encode(['results' => $resultados], true);
        $header = array("Content-Type: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://backend-colombia.co/RecibirSms/recibir",
          CURLOPT_HTTPHEADER => $header,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
          CURLOPT_USERAGENT=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
          CURLOPT_POSTFIELDS => $datosCod
        ));

        $resultado = curl_exec($curl);
        $codigoRespuesta = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        if($codigoRespuesta === 200){ 
            echo "salio bien";  
            $respuestaDecodificada = json_decode($resultado);
           var_dump($respuestaDecodificada);
        }else{ 
           echo "salio mal"; 
           echo $resultado;
        }
            curl_close($curl);
    }

    public function RespuestaMail_post(){
      $data =array(array("TRANSID"=>"14652378013752608",
                    "RCPTID"=>"0",
                    "RESPONSE"=>"smtp;250 2.0.0 OK 1465276276 mo3si31128106wjb.147 – gsmtp",
                    "EMAIL"=>"test2@gmail.com",
                    "TIMESTAMP"=>"1465276276",
                    "CLIENTID"=>"10001",
                    "FROMADDRESS"=>"info@mydomain.com",
                    "EVENT"=>"sent",
                    "MSIZE"=>"1216"));
       $datosCod = json_encode($data, true);
        $header = array("Content-Type: application/json");
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://backend-colombia.co/RecibirSms/recibirEstadosMails",
          CURLOPT_HTTPHEADER => $header,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
          CURLOPT_USERAGENT=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
          CURLOPT_POSTFIELDS => $datosCod
        ));

        $resultado = curl_exec($curl);
        $codigoRespuesta = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        if($codigoRespuesta === 200){ 
            $respuestaDecodificada = json_decode($resultado);
           var_dump($respuestaDecodificada);
        }else{ 
           echo "salio mal"; 
           echo $resultado;
        }
            curl_close($curl);

    }
    
    public function recibir_post(){
        $resultados = $this->input->raw_input_stream;
       // $resultados = $this->input->post('results');
        if(!isset($resultados)){
        $status = parent::HTTP_OK;
        $response['status']['ok'] = false;
        $response['status']['respuesta'] = "NO SE ENVIO NINGUN MENSAJE";
        }
        else{
            $status = parent::HTTP_OK;
        $response['status']['code'] = $status;
        $response['status']['ok'] = true;
        
        $datosCod = json_encode($resultados, true);        
        $datosCod = json_decode($datosCod,true); 
        //$resultados = $this->input->post('results');  
        $this->custom_log->write_log("INFO", $_REQUEST);
        $this->InfoBipModel->insertarMensaje($datosCod);   
        }
	    $this->response($response);
    }

public function recibirlogmail_post(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.pepipost.com/v2/logs?events=bounce&startdate=2020-01-01&enddate=2020-01-27",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
          CURLOPT_USERAGENT=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
          CURLOPT_HTTPHEADER => array(
        "api_key: f9a5f6fe35da420771aab0c95fd9910a",
        "content-type: application/json"
          ),
        ));

        $resultado = curl_exec($curl);
        $codigoRespuesta = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        if($codigoRespuesta === 200){ 
            $respuestaDecodificada = json_decode($resultado);
           var_dump($respuestaDecodificada);
        }else{ 
           echo "salio mal"; 
           echo $resultado;
        }
            curl_close($curl);
}
   //RECIBR ESTADISTICAS POR PEPIPOST

     public function recibirEstadosMails_post(){



        $resultados = $this->input->raw_input_stream;
       // $resultados = $this->input->post();
        //$datosCod = json_encode($resultados, true);        
        //$datosCod = json_decode($datosCod,true);
        $this->InfoBipModel->insertarMailsBulk($resultados); 
        $status = parent::HTTP_OK;
        $response['status']['code'] = $status;
        $response['status']['ok'] = true;
        $response['status']['resultados'] = $resultados;

        $this->response($response);
       /* $resultados = $this->input->post('results');
        if(!isset($resultados)){
        $status = parent::HTTP_OK;
        $response['status']['ok'] = false;
        $response['status']['respuesta'] = "NO SE ENVIO NINGUN MENSAJE";
        }
        else{
        $status = parent::HTTP_OK;
        $response['status']['code'] = $status;
        $response['status']['ok'] = true;*/
        
    //    $datosCod = json_encode($resultados, true);        
    //    $datosCod = json_decode($datosCod,true); 
        //$resultados = $this->input->post('results');  
    //    $this->custom_log->write_log("INFO", $_REQUEST);
    //    $this->InfoBipModel->insertarMails($datosCod);   
        
       // $this->response($response);
    }


 public function recibirSmsNrs_post(){

        $resultados = $this->input->raw_input_stream;
        if(isset($resultados)){
        $this->InfoBipModel->insertarSmsNrs($resultados); 
        $status = parent::HTTP_OK;
        $response['status']['code'] = $status;
        $response['status']['ok'] = true;
        $response['status']['resultados'] = $resultados;
        $this->response($response);
      }
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
 
    $string = str_replace( array("#"),'',$string);
    $string = str_replace( array("@"),'',$string);
    $string = str_replace( array("|"),'',$string);
    $string = str_replace( array("!"),'',$string);
    $string = str_replace( array("$"),'',$string);
    $string = str_replace( array("%"),'',$string);
    $string = str_replace( array("·"),'',$string);
    $string = str_replace( array(">"),'',$string);
    $string = str_replace( array("<"),'',$string);

    return $string;
}

 private function decrypt($code){
    $this->load->library('encryption');
    $this->encryption->initialize(
        array
        (
        'cipher' => 'aes-256',
        'mode' => 'ctr',
        'key' => 's0lvent@*'
        )
    );
    return $this->encryption->decrypt($code);
}
}
