<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

   
class Infobip extends REST_Controller {

    public function __construct($config = 'rest')
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
       
        parent::__construct(); 
        $this->load->model('Modulos_m','',TRUE);  
        $this->load->model('InfoBipModel','',TRUE);  
        $this->load->helper(array('form', 'url'));
        $this->load->config('form_validation');
        $this->load->library('form_validation');

    }

    public function cambiarArrayCreacionCliente2_post(){
      $arr = $this->InfoBipModel->getClientesRetanqueo();            
          foreach($arr as $key => $a) {
               if(isset($a->emailSolicitud)){$email = "sinmail@hotmail.com";}
               $data[] = array( 
                  'externalId' => $a->documento,
                  'firstName' => $a->nombres,
                  'lastName' => $a->apellidos,
                  'customAttributes'=> array(
                  'Cliente' => true),
                  'contactInformation'=> array(
                  'phone'=>[array('number'=>$a->telefonoSolicitud)],
                  'email'=>[array('address'=>$email)] 
               ));
            }
            echo $data2 = json_encode(['people' => $data],true);
   }


    public function crearPersonasInfoBip_post(){        
      echo $datosCod = $this->InfoBipModel->cambiarArrayCreacionSolicitante();
      /*$username = 'Solventa';
      $password = 'Havanna12$%98';
      $url = "https://mrgz4.api.infobip.com/people/2/persons/batch";
      $header = array("Content-Type: application/json","Accept: application/json");       
      $ch = curl_init($url); 
      curl_setopt_array($ch, array(
          CURLOPT_URL => $url,            
          CURLOPT_POSTFIELDS => $datosCod,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_HTTPHEADER => $header,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_USERPWD => "$username:$password",
      ));
      $resultado = curl_exec($ch);
      $codigoRespuesta = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
      if($codigoRespuesta === 200){    
      $respuestaDecodificada = json_decode($resultado);
      }else{ 
         echo "salio mal"; 
         echo $resultado;
      }
          curl_close($ch);*/
      }
      
    public function crearPersonasInfoBipSolicitantes_post(){        
        $datosCod = $this->cambiarArrayCreacionSolicitante();
        $username = 'Solventa';
        $password = 'Havanna12$%98';
        $url = "https://mrgz4.api.infobip.com/people/2/persons/batch";
        $header = array("Content-Type: application/json","Accept: application/json");       
        $ch = curl_init($url); 
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,            
            CURLOPT_POSTFIELDS => $datosCod,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => "$username:$password",
        ));
        $resultado = curl_exec($ch);
        $codigoRespuesta = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if($codigoRespuesta === 200){    
        $respuestaDecodificada = json_decode($resultado);
        }else{ 
           echo "salio mal"; 
           echo $resultado;
        }
            curl_close($ch);
        }

   public function actualizarPersonasInfoBip_patch(){        
         $datosCod = $this->cambiarArrayActualizacion();
         $username = 'Solventa';
         $password = 'Havanna12$%98';
         $url = "https://mrgz4.api.infobip.com/people/2/persons/batch";
         $header = array("Content-Type: application/json","Accept: application/json");       
         $ch = curl_init($url); 
         curl_setopt_array($ch, array(
             CURLOPT_URL => $url,            
             CURLOPT_POSTFIELDS => $datosCod,
             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
             CURLOPT_CUSTOMREQUEST => "PATCH",
             CURLOPT_HTTPHEADER => $header,
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_USERPWD => "$username:$password",
         ));
         $resultado = curl_exec($ch);
         curl_close($ch);
         }

   public function actualizarInformacionContactoInfoBip_put(){        
            $arr = $this->cambiarArrayInformacionContacto();
            $i = 0;
           foreach($arr as $key => $a) {
               $data[] = array(
                   'contactInformation'=> array(                     
                     'phone'=>[array('number'=>$a['number'])],
                     'email'=>[array('address'=>$a['address'])]
                  ));
            $datosCod = json_encode($data[$i],true);
            $username = 'Solventa';
            $password = 'Havanna12$%98';
            $url = "https://mrgz4.api.infobip.com/people/2/persons/contactInformation?externalId".$a['externalId']; 
            $header = array("Content-Type: application/json","Accept: application/json");       
            $ch = curl_init($url); 
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,            
                CURLOPT_POSTFIELDS => $datosCod,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD => "$username:$password",
            ));
            $resultado = curl_exec($ch);
            $codigoRespuesta = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            if($codigoRespuesta === 200){    
            $respuestaDecodificada = json_decode($resultado);
            }else{ 
               echo "salio mal"; 
               echo $resultado;
            }
            curl_close($ch);
            $i++;
         }         
          
      }

      public function createClientes_post(){
    
          $response = array(  
                     
                     array(
                      'externalId'=>'35962725',
                      'firstName'=>'Mauro',
                      'lastName'=>'Berteri',
                      'address'=>'67 Farringdon Road',
                      'city'=>'Buenos Aires',
                      'country'=>'Argentina',
                      'gender'=>'FEMALE',
                      'birthDate'=>'1966-01-15',
                      'middleName'=>"Janie",
                      'profilePicture'=>'http://profile.com',
                      'customAttributes'=> array(
                         'PromoNavidad'=>false,
                      )),
                         array(
                          'externalId'=>'35234396',
                          'firstName'=>'Marianela',
                          'lastName'=>'Suarez',
                          'address'=>'67 Farringdon Road',
                          'city'=>'Buenos Aires',
                          'country'=>'Argentina',
                          'gender'=>'FEMALE',
                          'birthDate'=>'1966-01-15',
                          'middleName'=>"Janie",
                          'profilePicture'=>'http://profile.com',
                          'customAttributes'=> array(
                             'PromoNavidad'=>false,
                             'Monto Disponible2' => "400.000"
                          ),
                          'contactInformation'=> array(
                             'phone'=>[
                                array(                                 
                                
                                
                                   'number'=>'5491149743446'
                                )],                       
                             'email'=>[
                                array(
                                   'address'=>'jane63234@acme.com',        
                                   'address'=>'janesmith723235@acme.com'
                                )
                             ])),
                             array(
                              'externalId'=>'33400799',
                              'firstName'=>'Belen',
                              'lastName'=>'Museri',
                              'address'=>'67 Farringdon Road',
                              'city'=>'Buenos Aires',
                              'country'=>'Argentina',
                              'gender'=>'FEMALE',
                              'birthDate'=>'1966-01-15',
                              'middleName'=>"Janie",
                              'profilePicture'=>'http://profile.com',
                              'customAttributes'=> array(
                                 'PromoNavidad'=>false,
                              ),
                              'contactInformation'=> array(
                                 'phone'=>[
                                    array(                                 
                                    
                                    
                                       'number'=>'5491149694735'
                                    )])));
                                 
      $datosCod = json_encode(['people' => $response],true);  
      $username = 'Solventa';
      $password = 'Havanna12$%98';
      //$datosCod = json_encode($response);     
      $apiKey = "1CACEB1B8F3E67052A695F5B6D146757";
      $url = "https://mrgz4.api.infobip.com/people/2/persons/batch";
      $header = array("Content-Type: application/json","Accept: application/json");//,"Authorization: ".$apiKey);      
      $ch = curl_init($url); 
      curl_setopt_array($ch, array(
          CURLOPT_URL => $url,            
          CURLOPT_POSTFIELDS => $datosCod,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_HTTPHEADER => $header,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_USERPWD => "$username:$password",
      ));
      $resultado = curl_exec($ch);
      $codigoRespuesta = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
      if($codigoRespuesta === 200){    
      $respuestaDecodificada = json_decode($resultado);
      }else{
     
          echo $resultado;
      }
          curl_close($ch);
      }
   }