<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'controllers/supervisores/supervisor_ventas/AsigAutomatico.php';
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

use function GuzzleHttp\debug_resource;

/**
 *
 */
class ApiSupervisores extends REST_Controller
{      
    private $_free_methods = array('buscar','listar');

    public function __construct()
    {

        parent::__construct();
        $method = $this->uri->segment(3);

        $this->load->library('User_library');
        $auth = $this->user_library->check_token();
        $this->load->library('Neotell_library');

        if($auth->status == parent::HTTP_OK || in_array($method, $this->_free_methods))
        {
            // MODELS
            $this->load->model('supervisores/Supervisores_model','Supervisores_model',TRUE);
            $this->load->model('Solicitud_m','solicitud_model',TRUE);
            $this->load->model('tracker_model','tracker_model',TRUE);
            $this->load->model('InfoBipModel','InfoBipModel',TRUE);
            $this->load->model('SolicitudAsignacion_model','solicitud_asignacion',TRUE);
            $this->load->model('security/Security_model','Security_model',TRUE);
            $this->load->model('operadores/Operadores_model');
			$this->load->model('cronograma_campanias/Cronogramas_model', 'cronograma_model', TRUE);
            $this->wolkvox['URL_CAMPANIAS'] = $this->config->item('URL_CAMPANIAS');
            $this->wolkvox['Token_ApiSearchCampania'] = $this->config->item('Token_ApiSearchCampania');
            $this->wolkvox['Token_FirmadoSearchCampania'] = $this->config->item('Token_FirmadoSearchCampania');
            $this->load->helper('requests_helper');
            // LIBRARIES
            $this->load->library('form_validation');
        }else{
            $this->session->sess_destroy();
            $this->response(['redirect'=>base_url('login')],$auth->status);
        }
    }


//Begin Esthiven Garcia Abril 2020
   

   public function get_campanias_for_central_post(){
        if ($this->input->is_ajax_request()) {
            $buscar = $this->input->post("buscar");
            $datos = $this->Supervisores_model->get_all_campanias_for_central($buscar);
            echo json_encode($datos);
            
        }
        else
        {
            show_404();
        }
    }

    public function get_operadores_for_central_post(){
        if ($this->input->is_ajax_request()) {
            $buscar = $this->input->post("buscar");
            $sl_equipos = $this->input->post("sl_equipos");
            $datos = $this->Supervisores_model->get_all_operadores_for_central($buscar,$sl_equipos);
            //var_dump($datos);die;
            echo json_encode($datos);
            
        }
        else
        {
            show_404();
        }
    }


    public function get_plantillas_for_central_post(){
        if ($this->input->is_ajax_request()) {
            $buscar = $this->input->post("buscar");
            $datos = $this->Supervisores_model->get_all_plantillas_for_central($buscar);
            echo json_encode($datos);
            
        }
        else
        {
            show_404();
        }
    }


    public function get_criterios_for_central_post(){
        if ($this->input->is_ajax_request()) {
            $buscar = $this->input->post("buscar");
            $datos = $this->Supervisores_model->get_all_criterios_for_central($buscar);
            echo json_encode($datos);
            
        }
        else
        {
            show_404();
        }
    }

    public function get_logicas_post(){
        if ($this->input->is_ajax_request()) {
            $datos = $this->Supervisores_model->get_all_logicas();
            echo json_encode($datos);
            
        }
        else
        {
            show_404();
        }
    }
	
	public function get_campania_post()
	{
		if ($this->input->is_ajax_request()) {
			
			$params = [
				'id_camp' => $this->input->post('id_campania')
			];
			
			$datos = $this->apiCronogramasPost(
				URL_CAMPANIAS . 'api/ApiCronogramaCampania/getCampania',
				$params
			);
			$datos = json_decode($datos);
			
			$response = ['status' => self::HTTP_OK, 'ok' => true, 'data' => $datos->data];
			$this->response($response);
		} else {
			show_404();
		}
    }
    
    public function get_all_campanias_post(){
        if ($this->input->is_ajax_request()) {
	
			$datos = $this->apiCronogramasPost(
				URL_CAMPANIAS . 'api/ApiCronogramaCampania/getAllCampanias',
				null
			);
			$datos = json_decode($datos);
			
            echo json_encode($datos->data);
            
        }
        else
        {
            show_404();
        }
    }
	
	public function get_mensaje_post()
	{
		if ($this->input->is_ajax_request()) {
			
			$params = array(
				'id_mensaje' => $this->input->post('id_mensaje'),
			);
			
			$datos = $this->apiCronogramasPost(
				URL_CAMPANIAS . 'api/ApiCronogramaCampania/getMensaje',
				$params
			);
			$datos = json_decode($datos);
			
			$status = parent::HTTP_OK;
			if ( !empty($datos->data)) {
				$response = ['status' =>  $status, 'data' => $datos->data];
			} else {
				$response = ['status' => $status, 'data' => null];
			}
			$this->response($response,  $status);
		} else {
			show_404();
		}
	}
	
	public function get_all_active_mensajes_post()
	{
		if ($this->input->is_ajax_request()) {
			
			$params = array(
				'id_camp' => $this->input->post('id_camp'),
			);
			
			$datos = $this->apiCronogramasPost(
				URL_CAMPANIAS . 'api/ApiCronogramaCampania/getAllActiveMensajes',
				$params
			);
			$datos = json_decode($datos);
			
			$status = parent::HTTP_OK;
			$response = ['status' =>  $status, 'data' => $datos->data];
			$this->response($response,  $status);
			
		} else {
			show_404();
		}
	}
	
	public function get_all_mensajes_post()
	{
		if ($this->input->is_ajax_request()) {
			
			$params = [
				'id_camp' => $this->input->post('id_camp')
			];
			$datos = $this->apiCronogramasPost(
				URL_CAMPANIAS . 'api/ApiCronogramaCampania/getAllMensajes',
				$params
			);
			$datos = json_decode($datos);
			
			$status = parent::HTTP_OK;
			$response = ['status' => $status, 'data' => $datos->data];
			$this->response($response, $status);
		} else {
			show_404();
		}
	}

     public function get_all_message_post()
    {

        if ($this->input->is_ajax_request()) {

            $data = $this->Supervisores_model->get_all_mensajes();
            if ( !empty($data)) {
                $status = parent::HTTP_OK;
                $response = ['status' => $status, 'data' => $data];  
            }else{
                $status = '200';
                $response = ['status' => $status, 'message' => 'No hay mensajes disponibles! ', 'data' => $data];
            }
            
        }
        else
        {
            show_404();
        }



        
        // REST_Controller provide this method to send responses
        $this->response($response, $status);

    }


public function BuscarDatosConstruidos_post(){



///$curreA= number_format($this->input->post('currency_rangeA'), 2,'.', ' ');
//$curreB= number_format($this->input->post('currency_rangeB'), 2,'.', ' ');
  $params = array(
    'id_usuario' => $this->session->userdata('id_usuario'),
    'sl_central' => $this->input->post('sl_central'),
    'sl_tipo_campaing' => $this->input->post('sl_tipo_campaing'),
    'sl_campania' => $this->input->post('sl_campania'),
    'sl_antiguedad' => $this->input->post('sl_antiguedad'),
    'sl_tipo_solicitud' => $this->input->post('sl_tipo_solicitud'),
    'sl_logica' => $this->input->post('sl_logica'),
    'dias_atrasoA' => $this->input->post('dias_atrasoA'),
    'dias_atrasoB' => $this->input->post('dias_atrasoB'),
    'currency_rangeA' => $this->input->post('currency_rangeA'),
    'currency_rangeB' => $this->input->post('currency_rangeB'),
    'date_rangeA' => $this->input->post('date_rangeA'),
    'date_rangeB' => $this->input->post('date_rangeB'),
    'sl_limite' => $this->input->post('sl_limite'),
    'exclusiones' => $this->input->post('exclusiones'),
    'personal' => $this->input->post('personal'),
    'sl_orden' => $this->input->post('sl_orden'),
    'sl_tipo_orden' => $this->input->post('sl_tipo_orden'),
    'sl_limite' => $this->input->post('sl_limite'),
    'limite_a' => $this->input->post('limite_a'),
    'limite_b' => $this->input->post('limite_b'),
    'sl_ex_retanqueo' => $this->input->post('sl_ex_retanqueo'),
    'txt_retanqueos' => $this->input->post('txt_retanqueos'),
    'sl_condicion' => $this->input->post('sl_condicion'),
    'sl_equipo_asig' => $this->input->post('sl_equipo_asig'),
    'chk_exclusion_gestion' => $this->input->post('chk_exclusion_gestion'),
    'chk_exclusion_emergia' => $this->input->post('chk_exclusion_emergia'),
    'chk_exclusion_bajas' => $this->input->post('chk_exclusion_bajas'),
     );

  if ($this->input->post('sl_tipo_campaing')== "PREVIEW") {
      $params['sl_distribucion'] = $this->input->post('sl_distribucion'); 
      $params['sl_equipo'] = $this->input->post('sl_equipo'); 
      $params['sl_operadores'] = $this->input->post('sl_operadores'); 
  }

// var_dump($params);die;

$headers = array('Accept' => 'application/json');
$end_point = URL_CAMPANIAS."ApiSearchCampania";

$otherop = array (
'binarytransfer' => 1,
'timeout' => 1000000,
'connect_timeout' => 1000000,
);
$request = Requests::post($end_point, $headers, $params, $otherop);


$response = $request->body;
$aux=json_decode($response,TRUE);

// print_r(($response));die;
    if ($aux['ok']) {
        
    //var_dump(['data'=>$aux['data']]);die;
    echo  json_encode(['data'=>$aux['data']]);
        
    }else{

    
    echo  "ERROR EN TRANSFERENCIA".$aux['data'];


    }

}

public function GenerarCsv_post(){

    $this->load->library('PHPExcel');
    
    $Planilla = $this->phpexcel;

    

      $params = array(
        'id_usuario' => $this->session->userdata('id_usuario'),
        'sl_central' => $this->input->post('sl_central'),
        'sl_campania' => $this->input->post('sl_campania'),
        'sl_tipo_campaing' => $this->input->post('sl_tipo_campaing'),
        'criterios' => $this->input->post('criterios'),
        'sl_antiguedad' => $this->input->post('sl_antiguedad'),
        'sl_estado' => $this->input->post('sl_estado'),
        'sl_tipo_solicitud' => $this->input->post('sl_tipo_solicitud'),
        'sl_logica' => $this->input->post('sl_logica'),
        'dias_atrasoA' => $this->input->post('dias_atrasoA'),
        'dias_atrasoB' => $this->input->post('dias_atrasoB'),
        'currency_rangeA' => $this->input->post('currency_rangeA'),
        'currency_rangeB' => $this->input->post('currency_rangeB'),
        'date_rangeA' => $this->input->post('date_rangeA'),
        'date_rangeB' => $this->input->post('date_rangeB'),
        'sl_limite' => $this->input->post('sl_limite'),
        'exclusiones' => $this->input->post('exclusiones'),
        'personal' => $this->input->post('personal'),
        'sl_orden' => $this->input->post('sl_orden'),
        'sl_tipo_orden' => $this->input->post('sl_tipo_orden'),
        'sl_limite' => $this->input->post('sl_limite'),
        'limite_a' => $this->input->post('limite_a'),
        'limite_b' => $this->input->post('limite_b'),
        'sl_ex_retanqueo' => $this->input->post('sl_ex_retanqueo'),
        'txt_retanqueos' => $this->input->post('txt_retanqueos'),
        'sl_condicion' => $this->input->post('sl_condicion'),
        'sl_equipo_asig' => $this->input->post('sl_equipo_asig'),
        'chk_exclusion_gestion' => $this->input->post('chk_exclusion_gestion'),
        'chk_exclusion_emergia' => $this->input->post('chk_exclusion_emergia'),
        'chk_exclusion_bajas' => $this->input->post('chk_exclusion_bajas'),
         );

if ($this->input->post('sl_tipo_campaing')== "PREVIEW") {
      $params['sl_distribucion'] = $this->input->post('sl_distribucion'); 
      $params['sl_equipo'] = $this->input->post('sl_equipo'); 
      $params['sl_operadores'] = $this->input->post('sl_operadores'); 
  }



if ($this->input->post('name')!="" || $this->input->post('descripcion')!="") {

    
    

        $param_to_label= array(

            'central' => $this->input->post('sl_central'),
            'plantilla_name' => $this->input->post('name'),
            'descripcion_plantilla' => $this->input->post('descripcion'),
            'sl_central' => $this->input->post('sl_central'),
            'sl_campania' => $this->input->post('sl_campania'),
            'criterios' => json_encode($this->input->post('criterios')),
            'sl_antiguedad' => $this->input->post('sl_antiguedad'),
            'sl_estado' => $this->input->post('sl_estado'),
            'sl_logica' => $this->input->post('sl_logica'),
            'dias_atrasoA' => $this->input->post('dias_atrasoA'),
            'dias_atrasoB' => $this->input->post('dias_atrasoB'),
            'currency_rangeA' => $this->input->post('currency_rangeA'),
            'currency_rangeB' => $this->input->post('currency_rangeB'),
            'date_rangeA' => $this->input->post('date_rangeA'),
            'date_rangeB' => $this->input->post('date_rangeB'),
            'sl_limite' => $this->input->post('sl_limite'),
            'personal' => $this->input->post('personal'),
            'estado' => 1,
        );

        
             
        
        $last_plantilla_insert= $this->Supervisores_model->guardarPlantillaCampania($param_to_label);
        if($last_plantilla_insert){
            /*TRACKEO LA PLANTILLA*/
                $param_to_track = array(
                    'central' => $this->input->post('sl_central'), 
                    'id_plantilla' => $last_plantilla_insert, 
                    'sl_central' => $this->input->post('sl_central'), 
                    'sl_campania' => $this->input->post('sl_campania'), 
                    'criterios' => json_encode($this->input->post('criterios')), 
                    'sl_antiguedad' => $this->input->post('sl_antiguedad'), 
                    'sl_estado' => $this->input->post('sl_estado'), 
                    'sl_logica' => $this->input->post('sl_logica'), 
                    'dias_atrasoA' => $this->input->post('dias_atrasoA'), 
                    'dias_atrasoB' => $this->input->post('dias_atrasoB'), 
                    'currency_rangeA' => $this->input->post('currency_rangeA'), 
                    'currency_rangeB' => $this->input->post('currency_rangeB'), 
                    'date_rangeB' => $this->input->post('date_rangeA'), 
                    'date_rangeB' => $this->input->post('date_rangeB'),
                    'sl_limite' => $this->input->post('sl_limite'), 
                    'action' => 'PLANTILLA_DEFINIDA', 
                    'fecha_insert' =>  date("Y-m-d H:m:s"),
                    'ip_insert' =>  $this->input->ip_address(),
                    'id_usuario' =>  $this->session->userdata('id_usuario'),
                    'personal' => $this->input->post('personal'),
                    'estado' =>  1,
                );
            $this->Supervisores_model->trackearGenCampania($param_to_track);
        }else{
            /*TRAQUEO EL ERROR DE LA PLANTILLA*/    
                $param_to_track = array(
                    'central' => $this->input->post('sl_central'), 
                    'id_plantilla' => 0, 
                    'sl_central' => $this->input->post('sl_central'), 
                    'sl_campania' => $this->input->post('sl_campania'), 
                    'criterios' => json_encode($this->input->post('criterios')), 
                    'sl_antiguedad' => $this->input->post('sl_antiguedad'), 
                    'sl_estado' => $this->input->post('sl_estado'), 
                    'sl_logica' => $this->input->post('sl_logica'), 
                    'dias_atrasoA' => $this->input->post('dias_atrasoA'), 
                    'dias_atrasoB' => $this->input->post('dias_atrasoB'), 
                    'currency_rangeA' => $this->input->post('currency_rangeA'), 
                    'currency_rangeB' => $this->input->post('currency_rangeB'), 
                    'date_rangeB' => $this->input->post('date_rangeA'), 
                    'date_rangeB' => $this->input->post('date_rangeB'),
                    'sl_limite' => $this->input->post('sl_limite'), 
                    'action' => 'ERROR_EN_DEFINICION_PLANTILLA', 
                    'fecha_insert' =>  date("Y-m-d H:m:s"),
                    'ip_insert' =>  $this->input->ip_address(),
                    'id_usuario' =>  $this->session->userdata('id_usuario'),
                    'personal' => $this->input->post('personal'),
                    'estado' =>  1,
                    );
            $this->Supervisores_model->trackearGenCampania($param_to_track);
        }
        
        
        //echo $this->input->post('name')." ".$this->input->post('descripcion');die;
    }

//var_dump($params);die;
$headers = array('Accept' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$end_point = URL_CAMPANIAS."ApiCsvCampania";
$otherop = array (
    'binarytransfer' => 1,
    'timeout' => 1000000,
    'connect_timeout' => 1000000,
);
$request = Requests::post($end_point, $headers, $params, $otherop);


$response = $request->body;
$aux=json_decode($response,TRUE);
//print_r($response);die;

        if ($aux['ok']) {

        echo  "public/csv/".$aux['file'];
            
        }else{


        echo  "ERROR EN TRANSFERENCIA".$aux['file'];


        }

}

    
//METODO QUE CONECTA A COMUNICAIONES, PARA CREAR CSV GENERAL DESDE QUERY DE CAMPANIAS
public function GenerarCsvCampaniasGenerales_post(){
    $params = ['idLogicaCSV' => $this->input->post('idLogicaCSV')];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => URL_CAMPANIAS."ApiCsvCampaniaGenerales",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => -1,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_POSTFIELDS=>$params
    ));

    $data = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        $data = $err;
        $status = parent::HTTP_BAD_REQUEST;
        $ok=FALSE;
    } else {
        $status = parent::HTTP_OK;
        $ok=TRUE;
    }
    
    $response = ['status' => $status, 'data' => $data , 'ok'=>$ok];
    $aux=json_decode($response['data'],TRUE);
    $hoy = new DateTime();
    $hoyCO_format =$hoy->format('d-m-Y H:i:s');
    $hoyAR = $hoy->modify('+2 hours');
    $hoyAR_format=$hoyAR->format('d-m-Y H:i:s');

    if ($aux['ok']) {

        // echo  "public/csv/".$aux['file'];  

        $cont = 0;
        $messageAux = '';
        foreach ($_SESSION['user'] as $key) {

            if ($cont == 0) {
                $messageAux = '<br> Id usuario: '.$key;
            }else if($cont == 1){
                $messageAux = $messageAux . '<br> Nombre: '.$key;
            }else if($cont == 2){
                $messageAux = $messageAux . '<br> Apellido: '.$key;
            }

            $cont++;
        }
        $fileName = "public/csv".$aux['file'];  
        //Envio de mail al generar CSV
        $from = 'no-reply@solventa.com';
        $to = 'sthiven.garcia@solventa.com';
        $from_name = 'Solventa SAS';
        $subject = 'Aviso de reporte CSV';
        $message = 'Se genero reporte con nombre: '.$aux['file'].'<br> Hora Argentina: '.$hoyAR_format.'<br> Hora Colombia: '. $hoyCO_format.' '.$messageAux;
        $cc = 'nicolaiev.brito@solventa.com';
        $cco = 'qa@solventa.com';

        $this->EnviarMailGeneral($from, $to, $from_name, $subject, $message, $cc, $cco, $fileName);

    }else{
        echo  "ERROR EN TRANSFERENCIA".$aux['file'];
    }

}

public function EnviarMailGeneral($from=null, $to=null, $from_name=null, $subject=null, $message=null, $cc=null, $cco=null,$fileName=null){

    $filename2 = realpath($fileName);
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $mimetype = $finfo->file($filename2);

    $file = new CURLFILE($filename2, $mimetype);

    if(file_exists($filename2)){
        $data = array(
            'from' => $from,
            'to' => $to,
            'from_name' => $from_name,
            'subject' => $subject,
            'message' => $message,
            'cc' => $cc,
            'cco' => $cco,
            'file' => $file
        );
        $request_headers = ['Authorization:eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzE0MzEzNDQsImV4cCI6MTU3MTQzNDk0NCwiZGF0YSI6eyJpZCI6IjEiLCJhZG1pbiI6dHJ1ZSwidGltZSI6MTU3MTQzMTM0NCwidGltZVRvbGl2ZSI6bnVsbH19.yOaR-uR1qjjGS_Z6VbTyBKN_zs-Xxx5Y_Xt2_dMZEa0'];
        // var_dump($data);die;
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => URL_SEND_MAIL.'api/sendmail',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $request_headers
        ));

        $response = curl_exec($ch);
        curl_close($ch);
        echo $response;
    }else{
        echo json_encode(['message' => "No se adjunto el archivo correctamente.", 'status' => 404]);
    }
}

public function GenerarTransmasiva_post(){

      $params = array(
        'id_usuario' => $this->session->userdata('id_usuario'),
        'sl_central' => $this->input->post('sl_central'),
        'sl_campania' => $this->input->post('sl_campania'),
        'sl_tipo_campaing' => $this->input->post('sl_tipo_campaing'),
        'criterios' => $this->input->post('criterios'),
        'sl_antiguedad' => $this->input->post('sl_antiguedad'),
        'sl_estado' => $this->input->post('sl_estado'),
        'sl_tipo_solicitud' => $this->input->post('sl_tipo_solicitud'),
        'sl_logica' => $this->input->post('sl_logica'),
        'dias_atrasoA' => $this->input->post('dias_atrasoA'),
        'dias_atrasoB' => $this->input->post('dias_atrasoB'),
        'currency_rangeA' => $this->input->post('currency_rangeA'),
        'currency_rangeB' => $this->input->post('currency_rangeB'),
        'date_rangeA' => $this->input->post('date_rangeA'),
        'date_rangeB' => $this->input->post('date_rangeB'),
        'sl_limite' => $this->input->post('sl_limite'),
        'exclusiones' => $this->input->post('exclusiones'),
        'personal' => $this->input->post('personal'),
        'sl_orden' => $this->input->post('sl_orden'),
        'sl_tipo_orden' => $this->input->post('sl_tipo_orden'),
        'sl_limite' => $this->input->post('sl_limite'),
        'limite_a' => $this->input->post('limite_a'),
        'limite_b' => $this->input->post('limite_b'),
        'sl_ex_retanqueo' => $this->input->post('sl_ex_retanqueo'),
        'txt_retanqueos' => $this->input->post('txt_retanqueos'),
        'sl_condicion' => $this->input->post('sl_condicion'),
        'sl_equipo_asig' => $this->input->post('sl_equipo_asig'),
        'chk_exclusion_gestion' => $this->input->post('chk_exclusion_gestion'),
        'chk_exclusion_emergia' => $this->input->post('chk_exclusion_emergia'),
        'chk_exclusion_bajas' => $this->input->post('chk_exclusion_bajas'),
         );

if ($this->input->post('sl_tipo_campaing')== "PREVIEW") {
      $params['sl_distribucion'] = $this->input->post('sl_distribucion'); 
      $params['sl_equipo'] = $this->input->post('sl_equipo'); 
      $params['sl_operadores'] = $this->input->post('sl_operadores'); 
}
//var_dump($params);die;

$headers = array('Accept' => 'application/json');
$end_point = URL_CAMPANIAS."ApiMasivaCampania";
$otherop = array (
    'binarytransfer' => 1,
    'timeout' => 1000000,
    'connect_timeout' => 1000000,
);
$request = Requests::post($end_point, $headers, $params, $otherop);


$response = $request->body;
$aux=json_decode($response,TRUE);


        if ($aux['ok']) {

        echo  json_encode(['data'=>$aux['data']]);
            
        }else{

        
        echo  "ERROR EN TRANSFERENCIA".$aux['data'];


        }

    }


public function ClearCampanias_post(){

    $this->load->library('PHPExcel');
    
    $Planilla = $this->phpexcel;

      $params = array(
        'id_usuario' => $this->session->userdata('id_usuario'),
        'sl_central' => $this->input->post('sl_central'),
        'sl_campania' => $this->input->post('sl_campania'),
        
         );

//

$headers = array('Accept' => 'application/json');
$end_point = URL_CAMPANIAS."ApiClearMasivaCampania";
$otherop = array (
    'binarytransfer' => 1,
    'timeout' => 1000000,
    'connect_timeout' => 1000000,
);
$request = Requests::post($end_point, $headers, $params, $otherop);


$response = $request->body;
//var_dump($response);die;
$aux=json_decode($response,TRUE);


        if ($aux['ok']) {

        echo  json_encode(['data'=>$aux['data']]);
            
        }else{

        
        echo  "ERROR EN TRANSFERENCIA".$aux['data'];


        }

    }





private function trackGestion3($endPoint, $method = 'POST',  $params=[] ){
    $dataTrackGestion = [
                'id_solicitud'=>(int)$solicitud->id,
                'observaciones'=>"Monto anterior:  $monotoAnterior , Monto nuevo: $montoNuevo , Plazo anterior: $plazoAnterior , Plazo nuevo: $plazo_nuevo , Vencimiento anterior: $vencimientoAnterior , Vencimiento nuevo: $vencimientoNuevo", 
                'id_cliente'=>(int)$solicitud->id_cliente, 
                'id_credito'=>(int)$solicitud->id_credito,
                'operador' => $this->session->userdata('user')->first_name." ".$this->session->userdata('user')->last_name,
                'id_tipo_gestion' => 0
            ];
            //$endPoint =  base_url('api/admin/track_gestion');
            //$endPoint =  "https://localhost:8085/campanias/ApiCsvCampania";
            $end_point = URL_CAMPANIAS."ApiSearchCampania";
            $this->trackGestion($endPoint, 'POST', $dataTrackGestion);

}

private function trackGestion2($endPoint, $method = 'POST',  $params=[] ){
    $token = $this->session->userdata('token');
    $curl = curl_init();
    $options[CURLOPT_HTTPHEADER] = ['Authorization:'.$token];
    $options[CURLOPT_POSTFIELDS] = $params;
    $options[CURLOPT_URL] = $endPoint;
    $options[CURLOPT_CUSTOMREQUEST] = $method;
    $options[CURLOPT_RETURNTRANSFER] = TRUE;
    $options[CURLOPT_ENCODING] = '';
    $options[CURLOPT_MAXREDIRS] = 10;
    $options[CURLOPT_TIMEOUT] = 30;
    $options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;

    if(ENVIRONMENT == 'development')
    {
        $options[CURLOPT_CERTINFO] = 1;
        $options[CURLOPT_SSL_VERIFYPEER] = 0;
        $options[CURLOPT_SSL_VERIFYHOST] = 0;
    }
    
    curl_setopt_array($curl,$options);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err)
    {
      $response['error'] = 'cURL Error #:' . $err;
    }

    return $response;
}

 
    
    
//FIN Esthiven Garcia Abril 2020
//**** cesar****
public function createAgenteWolvox_post(){
    $params = array('name_agent' => $this->input->post('name_agent'));
    $headers = array('Accept' => 'application/json');
    $end_point = URL_CAMPANIAS."ApiCreateAgent";

    $otherop = array (
    'binarytransfer' => 1,
    'timeout' => 100,
    'connect_timeout' => 100,
    );

    $request = Requests::post($end_point, $headers, $params, $otherop);
    //{"code": 200,"ok": true,"resp":{"result":"OK","user":"12934","password":"S47S72", "message":"agent successfully created"}
    $response = $request->body;
    $aux=json_decode($response,TRUE);
    if ($aux['ok']) {

        echo $aux["resp"]["user"];
    }else{
        echo  "ERROR EN TRANSFERENCIA".$aux['data'];
    }
}

public function desactiveAgentWolvox_post(){
    $params = array('id_agent' => $this->input->post('id_agente'));
    $headers = array('Accept' => 'application/json');
    $end_point = URL_CAMPANIAS."ApiDesactiveAgent";
    $otherop = array (
    'binarytransfer' => 1,
    'timeout' => 100,
    'connect_timeout' => 100,
    );

    $request = Requests::post($end_point, $headers, $params, $otherop);

    $response = $request->body;
    $aux=json_decode($response,TRUE);
    if ($aux['ok']) {
    //    echo $aux["resp"];  
       echo "Agente DESHABILITADO Wolvox";  

    }else{
        echo  "ERROR EN TRANSFERENCIA".$aux['data'];

    }
}
public function activeAgentWolvox_post(){
    $params = array('id_agent' => $this->input->post('id_agente'));
    $headers = array('Accept' => 'application/json');
    $end_point = URL_CAMPANIAS."ApiActiveAgent";
    $otherop = array (
    'binarytransfer' => 1,
    'timeout' => 100,
    'connect_timeout' => 100,
    );

    $request = Requests::post($end_point, $headers, $params, $otherop);

    $response = $request->body;
    $aux=json_decode($response,TRUE);
    if ($aux['ok']) {
    //    echo $aux["resp"];  
       echo "Agente HABILITADO Wolvox";  

    }else{
        echo  "ERROR EN TRANSFERENCIA".$aux['data'];

    }
}

public function createCampaniaWolvox_post(){
    $fecha_ini= $this->input->post('bhour');
    $fecha_ini_final1 = substr($fecha_ini, 0,2);
    $fecha_ini_final2 = substr($fecha_ini, 3,4);
    $fecha_ini_final= $fecha_ini_final1.$fecha_ini_final2;
    
    $fecha_fin= $this->input->post('fhour');
    $fecha_fin_final1 = substr($fecha_fin, 0,2);
    $fecha_fin_final2 = substr($fecha_fin, 3,4);
    $fecha_fin_final= $fecha_fin_final1.$fecha_fin_final2;

    // var_dump("hola", $fecha_ini_final, $fecha_fin_final); die;
    $params = array('ifpreview' => $this->input->post('ifpreview'),'name_camp' => $this->input->post('name_camp'),'desc_camp' => $this->input->post('desc_camp'),'id_skill' => $this->input->post('id_skill'),'bhour' => $fecha_ini_final,'fhour' =>  $fecha_fin_final,'opt1' => $this->input->post('opt1'),'opt2' => $this->input->post('opt2'),'opt3' => $this->input->post('opt3'),'opt4' => $this->input->post('opt4'),'opt5' => $this->input->post('opt5'),'opt6' => $this->input->post('opt6'),'opt7' => $this->input->post('opt7'));
    $headers = array('Accept' => 'application/json');
    $end_point = URL_CAMPANIAS."ApiCreateCampaing";
    $otherop = array (
    'binarytransfer' => 1,
    'timeout' => 100,
    'connect_timeout' => 100,
    );

    $request = Requests::post($end_point, $headers, $params, $otherop);
        // {"code":200,"ok":true,"resp":" \nid_campaing:18698\r\nOK\r\n \n"}
    $response = $request->body;

    $aux=json_decode($response,TRUE);
    // var_dump($aux);die;
    if ($aux['resp']) {
    //    echo $aux["resp"]["id_campaing"];  

       echo $aux["resp"];  
    }else{
        echo  "ERROR EN TRANSFERENCIA".$aux['resp'];

    }
}

    public function search_campania_post(){

        $id_campania = $this->input->post('id_campania');
        $searchCamp = $this->Supervisores_model->search_campania($id_campania);
        //var_dump($searchCamp[0]['paso']);die;
            if ( $searchCamp > 0 ) 
                {
                    // Set HTTP status code
                    $status = parent::HTTP_OK;
                    $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Campaña Registrada', 'paso' => $searchCamp[0]['paso']];  
                } else
                {
                    $status = '200';
                    $response = ['status' => $status, 'ok' => FALSE, 'message' => 'Campaña no Registrada', 'paso' =>0];  
                }
        $this->response($response);
    }
	
	public function update_campain_post()
	{	
		$idCampain = $this->input->post('txt_hd_id_camp');
		$nombre_logica = $this->input->post('nombre_logica');
		$sl_estado_campain = $this->input->post('sl_estado_campain');
		$sl_color = $this->input->post('sl_color');
		$sl_proveedor = $this->input->post('sl_proveedor');
		$sl_tipo_servicio = $this->input->post('sl_tipo_servicio');
		$sl_modalidad = $this->input->post('sl_modalidad');
		$canal = $this->input->post('canal');
		
		$arraData = array(
			'camp_id'             => $idCampain,
			'nombre_logica'       => $nombre_logica,
			'color'               => $sl_color,
			'id_proveedor'        => $sl_proveedor,
			'type_logic'          => $sl_tipo_servicio,
			'modalidad'           => $sl_modalidad,
			'estado'              => $sl_estado_campain,
			'canal'              => $canal,
		);
		
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/updateCampain',
			$arraData
		);
		$result = json_decode($response);

		$status = parent::HTTP_OK;
		if ( $result->data > 0 ) {
			$response = ['status' => $status, 'ok' => TRUE, 'message' => 'Campaña Actualizada', 'id_campaing_return' => 0];
		} else {
			$response = ['status' => $status, 'ok' => FALSE, 'message' => 'Campaña no Actualizada', 'id_campaing_return' =>0];
		}
		$this->response($response);
    }

    public function save_campain_post(){

        $nombre_logica = $this->input->post('nombre_logica');
        $sl_estado_campain = $this->input->post('sl_estado_campain');
        $sl_color = $this->input->post('sl_color');
        $sl_proveedor = $this->input->post('sl_proveedor');
        $sl_tipo_servicio = $this->input->post('sl_tipo_servicio');
        $sl_modalidad = $this->input->post('sl_modalidad');
        $canal = $this->input->post('canal');

        $arraData = array(  
          'nombre_logica'       => $nombre_logica,
          'color'               => $sl_color,
          'id_proveedor'        => $sl_proveedor,
          'type_logic'          => $sl_tipo_servicio,
          'modalidad'           => $sl_modalidad,
          'estado'              => $sl_estado_campain,
          'canal'               => $canal,
          'fecha_insert'        => date("Y-m-d H:i:s"),
          'usuario_insert'      => $this->session->userdata('id_usuario'),
          'ip_insert'           => $this->input->ip_address(),
        );
	
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/saveCampain',
			$arraData
		);
		$crearCamp = json_decode($response);

            if ( $crearCamp->data > 0 ) 
                {
                    // Set HTTP status code
                    $status = parent::HTTP_OK;
                    $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Campaña Registrada', 'id_campaing_return' => $crearCamp->data];  
                } else
                {
                    $status = '200';
                    $response = ['status' => $status, 'ok' => FALSE, 'message' => 'Campaña no Registrada', 'id_campaing_return' =>0];  
                }
        $this->response($response);
    }
	
	public function check_campain_predet_post()
	{
		$params = [
			'id_camp' => $this->input->post('id_campania'),
			'id_mensaje' => $this->input->post('id_mensaje')
		];
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/checkCampaniaPredet',
			$params
		);
		$response = json_decode($response);

		$this->response((!empty($response->data)));
    }
	
	public function save_message_post(){

        $mensaje = $this->input->post('mensaje');
        $sl_estado_message = $this->input->post('estado');
        $chkPre = $this->input->post('chkPre');
        $id_campania = $this->input->post('id_campania');
        $id_mensaje = $this->input->post('id_mensaje');
	
        $query_contenido =$this->CrearQueryMessage_post($mensaje); 

        $arraData = array(  
          'id_campania'     => $id_campania,
          'mensaje'         => $mensaje,
          'query_contenido' => $query_contenido,
          'estado'          => $sl_estado_message,
          'prederterminado' => $chkPre,

        );
	
        if ($id_mensaje == "") {
        	//nuevo mensaje
			$status = parent::HTTP_OK;
	
			$response = $this->apiCronogramasPost(
				URL_CAMPANIAS . 'api/ApiCronogramaCampania/saveMessage',
				$arraData
			);
			$response = json_decode($response);
			
			if ( $response->data == TRUE )
			{
				$response = ['status' => $status, 'ok' => TRUE, 'message' => 'Mensaje Registrado'];
			} else {
				$response = ['status' => $status, 'ok' => FALSE, 'message' => 'Mensaje no Registrado'];
			}
		} else {
			$status = parent::HTTP_OK;

        	//actualizacion
			$arraData['id_mensaje'] = $id_mensaje;
			$response = $this->apiCronogramasPost(
				URL_CAMPANIAS . 'api/ApiCronogramaCampania/updateMessage',
				$arraData
			);
			$response = json_decode($response);
			
			if ( $response->data == TRUE )
			{
				$response = ['status' => $status, 'ok' => TRUE, 'message' => 'Mensaje actualizado'];
			} else {
				$response = ['status' => $status, 'ok' => FALSE, 'message' => 'Mensaje no actualizado'];
			}
		}
            
        $this->response($response);
    }
	
	public function delete_message_post()
	{
		$id_mensaje = $this->input->post('id_mensaje');
		
		$arraData['id_mensaje'] = $id_mensaje;
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/deleteMessage',
			$arraData
		);
		$response = json_decode($response);
		
		$status = parent::HTTP_OK;
		if ( $response->data == TRUE )
		{
			$response = ['status' => $status, 'ok' => TRUE, 'message' => 'Mensaje Borrado'];
		} else {
			$response = ['status' => $status, 'ok' => FALSE, 'message' => 'Mensaje no Borrado'];
		}
		
		$this->response($response);
    }

    public function CrearQueryMessage_post($message=null){

        if ( IS_NULL($message) ) {
                
                $message=$this->input->post('message');

            }
            
            $msj1 = str_replace('$ ', '', $message);
            $msj2 = explode("$", $msj1);
            $this->db->select('*');
            $this->db->from('parametria.cp_tabla_logicas');
            $query = 'SELECT * FROM maestro.creditos';
            $campos = '';
            for ($i=0; $i < count($msj2); $i++) { 
                $val = '';
                if ($i == 0) {
                    $msj2[$i] = $val;
                }else{
                    $val = strtok($msj2[$i], " ");
                    $msj2[$i] = $val;
                    if ($i == 1) {
                        $campos = $val;
                    }else{
                        $campos = $campos . ", " . $val;
                    }
                }
            }
            $this->db->where_in('denominacion', $msj2);
            $clientes = $this->db->get();
            $cont = 0;
            $col = '';
            $innerJoin = '';
            foreach ($clientes->result() as $key) {
                //debug_resource();
                $idrango = json_encode($key->idrango);
                $condicion = json_encode($key->condicion);
                $campo = json_encode($key->campo);
                $tabla = json_encode($key->tabla);
                if ($cont == 0) {
                    $col = $campo;
                    // $innerJoin = $tabla;
                }else{
                    $col = $col . ", " . $campo;
                }
                if (!strstr($innerJoin, $tabla) and $tabla != '"transac_sql"') {
                    $innerJoin = ' INNER JOIN ';
                    $innerJoin = $innerJoin . " " . $tabla . " " . $condicion;
                    $query = $query.$innerJoin;
                }
                $cont++;
            }
            
			if ($col != '') {
				//si no hay ninguna variable que reemplazar se omite el reemplazo del * para que la query base
				// select * from maestro.creditos no de error al quitarselo
				$query = str_replace('*', $col, $query);
			}
            
            $query = str_replace('"', '', $query);
            $query = str_replace('\/', '/', $query);
            // print_r ($query);
            return $query;
    }

/*
|--------------------------------------------------------------------------
| API GENERAR CSV CAMPAÑIAS SMS Ing. Esthiven garcia
|--------------------------------------------------------------------------
|
| Este metodo genera un archivo CSV tomando la info automatiza del proceso de campanias.
| 
|
*/
    public function como_enviar_post()
    {
        $id_campania = $this->input->post('id_campania');
        $txt_notificar = $this->input->post('txt_notificar');
        $msj1 = str_replace('"', '', $txt_notificar);
        $msj2 = explode(";", $msj1);
        for ($i=0; $i < count($msj2); $i++) { 
            
            switch ($i) {
                case 0:
                    $to =$msj2[$i];
                    break;
                case 1:
                    $cc =$msj2[$i];
                    break;
                case 2:
                    $cco =$msj2[$i];
                    break;
                case 3:
                    break;
            }
            
            
            
        }

        $csvgenerado = $this->generarCsvCampania_post($id_campania,$to,$cc,$cco);

        $status = parent::HTTP_OK;
        $response = ['status' => $status, 'ok' => TRUE, 'message' => 'Mensaje Registrado','file'=> $csvgenerado];
    }

    public function generarCsvCampania_post($id_campania=null,$to=null,$cc=null,$cco=null){
        $this->load->library('PHPExcel');
        $Planilla = $this->phpexcel;
        
        if ( IS_NULL($id_campania) && IS_NULL($to) && IS_NULL($cc) && IS_NULL($cco) ) {
                
                $id_campania=$this->input->post('id_campania');
                $to=$this->input->post('to');
                $cc=$this->input->post('cc');
                $cco=$this->input->post('cco');

            }
        
        $consulta = $this->InfoBipModel->campain_sms_logicas($id_campania);
        //var_dump($consulta[0]['query_contenido']);die;
        $rs_result = $this->InfoBipModel->campain_sms_testing($consulta[0]['query_contenido']);
        if (!empty($rs_result['result'])) {
            $contador=1;
            $col = "A";
            $aux_letras = [];
            $letras = range($col,'Z');
            foreach($rs_result['campos'] as $key1 => $value1){
                $aux_letras[] = $letras[$key1];
                $colum        = $letras[$key1].$contador;
                $Planilla->setActiveSheetIndex(0)->setCellValue($colum, $value1);
            }
            $count = 2;
            foreach ($rs_result['result'] as $key=> $value){
                foreach ($value as $key2 => $value2) {
                    $indice = array_keys($rs_result['campos'], $key2);
                    // var_dump($indice);die;
                    $aux = $aux_letras[$indice[0]]. $count;
                    $Planilla->setActiveSheetIndex(0)->setCellValue($aux, $value2);
                }
                $count++;
            }
        }

        $Planilla->getActiveSheet()->setTitle('Planilla 1');
        
        $objGravar = PHPExcel_IOFactory::createWriter($Planilla, 'Excel2007');
        
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment;filename="ReporteCampaniaCSV'.date("Ymd").'-'.$id_campania.'"');
        
        header('Cache-Control: max-age=0');
        header('Content-type: application/vnd.ms-excel');
        $objGravar->save(URL_CSV_FOLDER.'ReporteCampaniaCSV'.date("Ymd").'-'.$id_campania.".xlsx");//server
        //$objWriter->save('C:/wamp3.1.9/www/BackednColombia/public/csv/ReporteCampaniaCSV'.date("Ymd").'-'.$idQuery.".xlsx");//local
        /*SECCION DE TRAKEO ENVIO MAIL*/
        $fileName = 'public/csv/ReporteCampaniaCSV'.date("Ymd").'-'.$id_campania.".xlsx";
        $arquivo= "ReporteCampaniaCSV".date("Ymd").'-'.$id_campania.".xlsx";
        /*FIN SECCION DE TRAKEO ENVIO MAIL*/
        //$fileName = "public/csv".$aux['file'];  
        //Envio de mail al generar CSV

        $hoy = new DateTime();
        $hoyCO_format =$hoy->format('d-m-Y H:i:s');
        $hoyAR = $hoy->modify('+2 hours');
        $hoyAR_format=$hoyAR->format('d-m-Y H:i:s');
        $from = 'no-reply@solventa.com';
        $to = 'sthiven.garcia@solventa.com';
        $from_name = 'Solventa SAS';
        $subject = 'Aviso de reporte CSV';
        $message = 'Se genero reporte con nombre: '.$arquivo.'<br> Hora Argentina: '.$hoyAR_format.'<br> Hora Colombia: '. $hoyCO_format;
        //$cc = "";//'nicolaiev.brito@solventa.com';
        //$cco = "";//'qa@solventa.com';
        
        $trackmail= $this->EnviarMailGeneral($from, $to, $from_name, $subject, $message, $cc, $cco,$fileName);
        var_dump($trackmail);
        
        return $fileName;
        
    }


    public function BuscarBotonesOperador_post()
    {
        if ($this->input->is_ajax_request()) {
            
            $datos  = $this->solicitud_model->BuscarBotonesOperador();
            echo json_encode($datos);

        } else {
            show_404();
        }
    }
	
	/**
	 * Checkea si existe un mensaje programado de la campaña en la hora y dia especificado
	 *
	 */
	public function check_mensaje_programado_day_hour_post()
	{
		$params = array(
			'id_camp' => $this->input->post('id_camp'),
			'hour' => $this->input->post('hour') . ':00',
			'day' => $this->input->post('day'),
		);
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . "api/ApiCronogramaCampania/checkMensajeProgramdoDayHour",
			$params
		);
		$response = json_decode($response);
		$this->response((!empty($response->data)));
	}
    
	public function save_mensaje_programado_post()
	{
		$idCampain = $this->input->post('id_camp');
		$idMensaje = $this->input->post('id_msg');
		$hour = $this->input->post('hour');
		$day = $this->input->post('day');
		
		$arraData = array(
			'id_campania' => $idCampain,
			'id_mensaje' => $idMensaje,
			'day' => $day,
			'hour' => $hour . ':00',
		);
		
		$result = $this->apiCronogramasPost(
			URL_CAMPANIAS . "api/ApiCronogramaCampania/saveMensajeProgramado",
			$arraData
		);
		$result = json_decode($result);

		$status = parent::HTTP_OK;
		if ( $result->data === true ) {
			$response = ['status' => $status, 'ok' => TRUE, 'message' => 'Mensaje programado guardado correctamente', 'id_campaing_return' => 0];
		} else {
			$response = ['status' => $status, 'ok' => FALSE, 'message' => 'Mensaje programado no guardado', 'id_campaing_return' =>0];
		}
		$this->response($response);
    }
	
	public function get_all_mensajes_programados_post()
	{
		$params = [
			'id_camp' => $this->input->post('id_camp'),
			'day' => $this->input->post('day')
		];

		$result = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/getAllMensajesProgramados',
			$params
		);
		$result = json_decode($result);
		
		$status = parent::HTTP_OK;
		$response = ['status' =>  $status, 'data' => $result->data];
		$this->response($response,  $status);
    }
	
	public function delete_mensaje_programado_post()
	{
		$params = [
			'id_mensaje' => $this->input->post('id_mensaje')
		];
		
		$result = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/deleteMensajeProgramado',
			$params
		);
		$result = json_decode($result);
		$status = parent::HTTP_OK;
		$response = ['status' => $status, 'ok' => TRUE, 'message' => 'Mensaje Borrado'];

		if ( $result->data != TRUE )
		{
			$status = parent::HTTP_OK;
			$response = ['status' => $status, 'ok' => FALSE, 'message' => 'Mensaje programado no Borrado'];
		}
		
		$this->response($response);
    }
	
	public function check_delete_msg_post()
	{
		$params = [
			'id_campania' => $this->input->post('id_campania'),
			'id_mensaje' => $this->input->post('id_mensaje')
		];
		
		$result = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/checkDeleteMsg',
			$params
		);
		$response = json_decode($result);
		
		$this->response((!empty($response->data)));

    }
	
	
	/**
	 *    Comprueba si el operador esta en campania manual
	 */
	public function checkIfOperatorIsInManualCampaign_post()
	{
		$response = false;
		$user_data = $this->input->post("USUARIO");
		$res = $this->neotell_library->POSITION_NEOTELL($user_data);
		$user_parse = strstr($res, "not logged.");
		if ($user_parse == false) {
			$xml = simplexml_load_string($res);
			$pieces = explode("|", $xml[0]);
			foreach ($pieces as $key => $value) {
				if ($value != "") {
					$varclean = explode("=", $value);
					$aux[$varclean[0]] = $varclean[1];
				}
			}
			
			if ($aux['SAL_CAMPAÑA_DEFAULT'] == NEOTELL_MANUAL_CAMPAIGN_ID) {
				$response = true;
			}
		}
		
		$array_res = [];
		$array_res["data"] = $response;
		echo json_encode($array_res);
	}
	
    public function up_casos_neotell_post()
    {
        
        $user_data= $this->input->post("USUARIO");
        $id_operador= $this->session->userdata("idoperador");
        $server= $this->input->post("server");
        // var_dump($server);die;
        $rs_response = $this->Supervisores_model->search_operador_campanias_chat($id_operador);
        if($rs_response)
        {
            $array_res=[];
            $array_res["respuesta_cadena"]="Operador asignado a campaña de chat no disponible para llamadas";
            echo json_encode($array_res);
        }else{

                        if (!is_null($this->input->post('bandera')))
                        {
                            $res = $this->test_response_neotell_post();
                        }else{
                            $res = $this->neotell_library->POSITION_NEOTELL($user_data,$server);

                        }
                        
                        $aux=[];
                        
                        $user_parse =strstr($res, "not logged.");
                        $user_lic = strstr($res, "No more API licenses.");
                      
                        // var_dump($res);
                       
                        if($user_lic == false){
                            if ($user_parse == false) {
                            
                                            $xml = simplexml_load_string($res);

                                            $pieces = explode("|", $xml[0]);
                                            //var_dump($pieces);
                                            foreach ($pieces as $key => $value) {

                                                
                                                if ($value !="") {
                                                    
                                                    $varclean = explode("=", $value);
                                                    $aux[$varclean[0]] = $varclean[1];     

                                                }           

                                            
                                            }

                                    //die;
                                        if ($aux['ESTADO_CRM'] == "GetContact") {
                                            $USUARIO = $user_data;
                                            $BASE = $aux['BASE'];//id CAMPAÑA BASE CORRIENDO
                                            $DATA = $aux['DATA'];//ID CREDITO CARGADO EN CAMPAÑA
                                            $IDCONTACTO = $aux['IDCONTACTO'];//IDCONTACTO

                                        
                                            /* ARMO DATOS PARA consumir ENDPOINT CRM_ShowingContact*/
                                            
                                            $res_show = $this->neotell_library->SHOWING_CONTAC_NEOTELL($USUARIO,$BASE,$IDCONTACTO,$DATA,$server);
                                            

                                            /*REDIRECCIONO CASO A COBRANZAS*/

                                                $this->session->set_userdata('id_cliente', $aux['IDCONTACTO']);
                                                $this->session->set_userdata('cola', $aux['COLA']);
                                                $this->session->set_userdata('id_agente', $aux['USUARIO']);
                                                $this->session->set_userdata('telefono', $aux['TELEFONO']);
                                                $this->session->set_userdata('id_credito', $aux['DATA']);
                                                $this->session->set_userdata('nombre_customer', "NO NAME");
                                                
                                                
                                                

                                            $aux['redirect_url'] = base_url()."atencion_cobranzas/renderCobranzas";
                                        
                                            
                                            $this->session->set_userdata('leyendo_caso', 1);

                                            echo json_encode($aux);
                                            


                                        }else{
                                            $array_res["respuesta_cadena"]="Disponible";
                                            echo json_encode($array_res);
                                        }

                            }else{
                                $array_res=[];
                                $array_res["respuesta_servicio"]=$res;
                                $array_res["respuesta_cadena"]="Agente no logeado en neotell";
                                echo json_encode($array_res);
                                
                                
                            }
                        }else{
                            $array_res=[];
                            $array_res["respuesta_servicio"]=$res;
                            $array_res["respuesta_cadena"]="Posibles causas del problema: Sin licencias activas ,sin conexion a internet o Sin usuario en la tabla de track_operadores de telefonia";
                            echo json_encode($array_res);                    
                        }

        }
        

        
        

    }


    public function cerrar_caso_post()
    {
        if ($this->session->userdata('leyendo_caso')== 1) {
                
                $this->session->set_userdata('leyendo_caso', 0);
                echo "CASO CERRADO";
        }else{

                echo "CASO CERRADO";
        }

    }


    public function disponible_user_neotell_post()
    {

        $user = $this->Supervisores_model->buscarOperadorNeotell($this->session->userdata("idoperador"));
        $user_data= $user[0]['id_agente'];
        $respuesta= $this->Neotell_library->DISPONIBLE_NEOTELL($user_data);
        var_dump($respuesta);
    }
	
	public function buscar_cronogramas_get($idCampania = null){
		
		$event_data = $this->generarCalendario($idCampania);
		
		foreach ($event_data as $row) {
			
			$isToday = (date('Ymd') == date('Ymd', strtotime($row['start'])));

			if ($row['canceled']) {
				$title = '<em><del style="color: #636363">' . $row['title'] . '</del></em>';
			} else {
				$title = $row['title'];
			}
			
			if ($isToday) {
				$finished = $this->checkedEventFinished($row);
				if ($finished) {
					$title .= ' ✅';
				} else {
					$title .= " ⏰";
				}
			}
			
			$data[] = array(
				'title' => $title,
				'startTime' => $row['start'],
				'start' => $row['start'],
				'endTime' => $row['end'],
				'end' => $row['end'],
				'color' => $row['color'],
				//				'daysOfWeek' => [$row['daysOfWeek']],
				'extendedProps' => [
					'id_msg_prog' => $row['id_msg_prog'],
					'id_campain' => $row['id_campania'],
					'mensaje' => $row['mensaje'],
					'tipo' => $row['type_logic'],
					'dia' => $row['day'],
					'hora' => $row['start'],
					'canceled' => $row['canceled']
				]
			);
			
		}

		echo json_encode($data);
	}
	
	public function checkedEventFinished($event)
	{
		return (new DateTime() > new DateTime($event['start']));
	}
	
	public function generarCalendario($idCampania = null)
	{
		
		$start = $day = strtotime('last day of previous month');
		$end = strtotime('last day of next month');
		
		$result = $this->apiCronogramasGet(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/buscarEventos/'.$idCampania
		);
		$event_data = json_decode($result);
		
		$resultCancelados = $this->apiCronogramasGet(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/buscarEventosCancelados/'.$idCampania
		);
		$event_canceled_data = json_decode($resultCancelados)->data;
		$events = [];
		while($day < $end)
		{
			$day = strtotime("+1 day", $day);
			$diaSemana = date('N', $day);
			$date = date('Y-m-d', $day);
			
			foreach ($event_data->data as $k=>$event_datum) {
				$event_datum2 = get_object_vars($event_datum);
				if ($diaSemana == $event_datum2['weekDay']) {
					$event_datum2['start'] = $date . " " . $event_datum2['start'];
					$event_datum2['end'] = $date . " " . $event_datum2['end'];
					
					$canceledEventInfo = $this->getCanceledEventInfo($date, $event_datum2, $event_canceled_data);
					
					$event_datum2['canceled'] = false;
					$event_datum2['id_msg_prog'] = $event_datum2['id'];
					if (!is_null($canceledEventInfo)) {
						$event_datum2['canceled'] = true;
						$event_datum2['id_msg_prog'] = $canceledEventInfo->id_mensaje_programado;
						$event_datum2['canceled_date'] = $canceledEventInfo->fecha;
					}
					
					unset($event_datum2['canceled_date']);
					unset($event_datum2['is_canceled']);
					
					$events[] = $event_datum2;
				}
			}
			
		}
		return $events;
	}
	
	private function getCanceledEventInfo($date, $event,$canceledArray)
	{
		$canceled = null;
		foreach ($canceledArray as $cancelados) {
			if($date == $cancelados->fecha and $cancelados->canceled == '1' and $event['id'] == $cancelados->id_mensaje_programado ) {
				$canceled = $cancelados;
			}
		}
		
		return $canceled;
	}
	
	public function test_query_post()
	{
		$params = $this->getCampainFilterValues();
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . "api/ApiCronogramaCampania/getCronogramaQueryAffectedCount",
			$params
		);
		$this->response($response);
	}
	
	public function save_campain_filter_post()
	{
		$params = $this->getCampainFilterValues();
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/saveFiltrosCampanias',
			$params
		);
		$response = json_decode($response);
		$this->response($response->data);
	}
	
	private function getCampainFilterValues()
	{
		$post = $this->input->post();
		
		$params = array(
			'id_campania' => $post['id_campania'],
			'destiny' => ($post['destiny'] ?? ''),
			'client_type' => ($post['clientType'] ?? ''),
			'accion' => ($post['action'] ?? ''),
			'x_credits' => ($post['xCredits'] ?? ''),
			'estatus' => ($post['status'] ? implode(",", $post['status']) : ''),
			'filtro' => ($post['filter'] ?? ''),
			'logic' => ($post['logic'] ?? ''),
			'valor1' => ($post['valor1'] ?? ''),
			'valor2' => ($post['valor2'] ?? ''),
			'origen_desde' => ($post['origen_desde'] ?? ''),
			'origen_desde_valor' => ($post['origen_desde_valor'] ?? ''),
			'origen_hasta' => ($post['origen_hasta'] ?? ''),
			'origen_hasta_valor' => ($post['origen_hasta_valor'] ?? ''),
		);
		return $params;
	}
	

	public function get_campaign_notification_emails_post()
	{
		$post = $this->input->post();
		
		$params = array(
			'camp_id' => ($post['camp_id'] ?? ''),
		);
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS.'api/ApiCronogramaCampania/getEmailNotificados',
			$params
		);
		$response = json_decode($response);
		$this->response($response->data);
	}
	
	public function add_campaign_notification_email_post()
	{
		$arraData = array(
			'id_campania' => $this->input->post('camp_id'),
			'email' => $this->input->post('email'),
		);

		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS.'api/ApiCronogramaCampania/checkCampaniaNotificados',
			$arraData
		);
		$responseCheck = json_decode($response);
		
		if (empty($responseCheck->data)) {
			$response = $this->apiCronogramasPost(
				URL_CAMPANIAS.'api/ApiCronogramaCampania/saveEmailNotificados',
				$arraData
			);
			$response = json_decode($response);
		}
		
		$this->response( ($response->data ?? false) );
	}
	
	public function remove_campaign_notification_email_post()
	{
		$params = [
			'email' => $this->input->post('email'),
			'camp_id' => $this->input->post('camp_id')
		];
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS.'api/ApiCronogramaCampania/deleteEmailNotificados',
			$params
		);
		$response = json_decode($response);
		
		$this->response((!empty($response->data)));
	}
	
	public function send_email_notification_post()
	{
		$metodo = $this->input->post('metodo');
		$emails = $this->input->post('emails');
		$campaignId = $this->input->post('camp_id');
		$idMenProgram = $this->input->post('id_msj_program');
		
//		$mensaje = $this->Supervisores_model->get_mensaje_programado($idMenProgram);
		//TODO: agregar variables al mensaje
		// necesito un where para aplicar al query_contenido.
		// probablemente se obtenga del usuario y la relacion con su credito de la query de a quien enviar.
		// preguntar que pasa si el usuario tiene mas de 1 credito. Como saber a cual referenciar.
		
//		var_dump($mensaje[0]['query_contenido']);
//		$contenido = $this->Supervisores_model->testQuery($mensaje[0]['query_contenido']);
//		var_dump($contenido);
		
		$campania = $this->Supervisores_model->get_campaniaWithProveedor($campaignId);
		
		$data = [];
		$data['campaniaTitulo'] = $campania['title'];
		$data['fechaEnvio'] = date("d/m/Y");
		$data['horaEnvio'] = $mensaje['hour'];
		$data['proveedor'] = $campania['nombre_proveedor'];
		$data['servicio'] = $campania['tipo_servicio'];
		$data['mensaje'] = $mensaje['mensaje'];
		$data['mensajesEnviados'] = 0;
		
		$data['asunto'] = $data['campaniaTitulo'] . " " . $data['fechaEnvio'] . " " . $data['horaEnvio'];
		
		$csvFile = null;
		if ($metodo == Supervisores_model::CAMPAIGN_METODO_ENVIO_CSV) {
			//generar csv
		}
		
		foreach ($emails as $email) {
//			$this->send_notification($data,$metodo, $email, $csvFile );
		}
		
	}
	
	private function send_notification($data, $metodo, $email, $archivo = null)
	{
		
		if ($metodo == Supervisores_model::CAMPAIGN_METODO_ENVIO_CSV and !empty($archivo)) {
			//adjunto el archivo
			$files = array();
			$files['file'] = 'full_path_txt';
			
			array_walk($files, function($filePath, $key) use(&$body) {
				$body[$key] = curl_file_create($filePath);
			});
		}
		
		$message = '.';
		//$url_api_medios_de_pago = 'http://sendmail.solventa.local/api/sendmail'; //Desarrollo
//		$url_api_medios_de_pago = URL_SEND_MAIL.'api/sendmail';   //Produccion
		// if it is a multipart forma data form
		$body = array (
			//"jwt" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzEyNjE4MDcsImV4cCI6MTU3MTI2NTQwNywiZGF0YSI6eyJpZCI6IjEiLCJhZG1pbiI6dHJ1ZSwidGltZSI6MTU3MTI2MTgwNywidGltZVRvbGl2ZSI6bnVsbH19.EjL-hI9PKhF9p84Id425mdYHo0LmQINtW8MrKpXFX5U",
			"jwt" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE1NzMxNjExNjgsImV4cCI6MTU3MzE2NDc2OCwiZGF0YSI6eyJpZCI6IjYyNTgiLCJhZG1pbiI6ZmFsc2UsInRpbWUiOjE1NzMxNjExNjgsInRpbWVUb2xpdmUiOm51bGx9fQ.gsGPp2FXAk4I7KEXPNuleh6kqYP5ahWYud-baZBpOFE",
			"from" => "analisis.fraude@solventa.com.ar",                 //DESDE DONDE
			'to' => $email,
			'from_name' => 'Solventa SAS',
			'subject' => 'Nueva solicitud de Préstamo',
			'template' => 7,
			'campania' => $data['campaniaTitulo'],
			'fecha_envio' => $data['fechaEnvio'],
			'hora_envio' => $data['horaEnvio'],
			'proveedor' => $data['proveedor'],
			'servicio' => $data['servicio'],
			'mensaje' => $data['mensaje'],
			'mensajes_enviados' => $data['mensajesEnviados'],
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
		
		return $respuesta;
	}
	
	public function get_filtros_campanias_post()
	{
		$params = [
			'camp_id' => $this->input->post('camp_id')
		];
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/getFiltrosCampanias',
			$params
		);
		$response = json_decode($response);
		
		$this->response($response->data);
	}
	
	public function save_method_post()
	{
		$params = [
			'camp_id' => $this->input->post('camp_id'),
			'metodo' => $this->input->post('metodo')
		];
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/saveMethod',
			$params
		);
		$response = json_decode($response);
		
		$this->response($response->data);
	}
	
	public function save_format_post()
	{
		$params = [
			'camp_id' => $this->input->post('camp_id'),
			'formato' => $this->input->post('formato')
		];
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/saveFormat',
			$params
		);
		$response = json_decode($response);
		
		$this->response($response->data);
	}
	
	public function disable_msg_prog_post()
	{
		$params = [
			'id_mensaje_programado' => $this->input->post('id_msg_prog'),
			'fecha' => $this->input->post('id_msg_prog_date'),
			'canceled' => true
		];
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/disableMsgProg',
			$params
		);
		
		$response = json_decode($response);
		
		$this->response($response->data);
	}
	
	public function enable_msg_prog_post()
	{
		$params = [
			'id_mensaje_programado' => $this->input->post('id_msg_prog'),
			'fecha' => $this->input->post('id_msg_prog_date'),
			'canceled' => false
		];
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/enableMsgProg',
			$params
		);
		
		$response = json_decode($response);
		
		$this->response($response->data);
	}
	
	private function apiCronogramasPost($endPoint, $params)
	{
		$headers = array('Accept' => 'application/json');
		
		$otherop = array(
			'binarytransfer' => 1,
			'timeout' => 500,
			'connect_timeout' => 500,
		);
		
		$request = Requests::post($endPoint, $headers, $params, $otherop);
		$response = $request->body;
		return $response;
	}
	
	private function apiCronogramasGet($endPoint)
	{
		$headers = array('Accept' => 'application/json');
		
		$otherop = array(
			'binarytransfer' => 1,
			'timeout' => 500,
			'connect_timeout' => 500,
		);
		
		$request = Requests::get($endPoint, $headers, $otherop);
		$response = $request->body;
		return $response;
	}
	
	public function generate_csv_get($idMensajeProgramado, $idCampania, $idEvento = null)
	{
        $campaniaResposne = $this->apiCronogramasPost(
            URL_CAMPANIAS . 'api/ApiCronogramaCampania/getCampania',
			['id_camp' => $idCampania]
		);
        
		$campaniaResposne = json_decode($campaniaResposne);
		$campaniaResposneData = (array)$campaniaResposne->data;
        
        if (!is_null($idEvento)) {
            $mpResposne = $this->apiCronogramasPost(
                URL_CAMPANIAS . 'api/ApiCronogramaCampania/getMensajeById',
                ['idEvento' => $idEvento, "idMensaje" => $idMensajeProgramado, "idCampania" => $idCampania]
            );
        }else{
            $mpResposne = $this->apiCronogramasPost(
                URL_CAMPANIAS . 'api/ApiCronogramaCampania/getMensajeProgramado',
                ['id' => $idMensajeProgramado]
            );
        }
		$mpResposne = json_decode($mpResposne);
		$mpResposneData = (array)$mpResposne->data;		
        
        if (!is_null($idEvento)) {
            $params = ['idEvento' => $idEvento, "idMensaje" => $idMensajeProgramado, "idCampania" => $idCampania];
        }else{
            $params = ['id_mensaje_programado' => $idMensajeProgramado];
        }
		$response = $this->apiCronogramasPost(
            URL_CAMPANIAS . 'api/ApiCronogramaCampania/generarCsvCampania',$params
		);
		$response = json_decode($response);
		$responseData = (array)$response->data;

		if (!is_null($idEvento)) {
		    $fecha = date("Y-m-d", strtotime($mpResposneData[0]->run_date));
            $this->load->helper("my_date");
            
            $fechaFormat = date_to_string($fecha, "L d F a");
            $hora = date("H:i:s", strtotime($mpResposneData[0]->run_date));
            $fechaHora = str_replace(" ", "_", $fechaFormat);
            $mpResposneData[0]->day = $fechaFormat;
            $mpResposneData[0]->hour = $hora;
        }else{
            $fechaHora = $mpResposneData[0]->day . ' ' .
                str_replace(':','', $mpResposneData[0]->hour);
        }
		$filename =
        $campaniaResposneData[0]->nombre_logica.'-'.$fechaHora;
		//no envia archivos con nombres que contengan una ñ
		$filename = str_replace('ñ', 'ni', $filename);
		
		if ($campaniaResposneData[0]->formato == 'CSV') {
			$filename .= '.csv';
			
			if ($campaniaResposneData[0]->metodo == 'SLACK (CSV)') {				
				$this->sendCsvBySlack($idCampania, $responseData, $filename, $mpResposneData[0]);
			} else {				
				header("Content-type: application/csv");
				header('Content-Disposition: attachment; filename='.$filename);
				header("Content-Transfer-Encoding: UTF-8");
				header("Cache-Control: no-cache, no-store, must-relative");
				header("Pragma: no-cache");
				header("Expires: 0");
				
				$f = fopen('php://output', 'w');
				
				foreach ($responseData as $campos) {
					fputcsv($f, $campos, ';');
				}
				
				fclose($f);
			}
		} else {
			$filename .= '.xls';
			
			$this->load->library('PHPExcel');
			$Planilla = $this->phpexcel;
			$Planilla->setActiveSheetIndex(0);
			
			$headers = $responseData[0];
			foreach ($headers as $position => $header) {
				$letter =  $this->number_to_alphabet($position+1);
				$Planilla->getActiveSheet(0)->setCellValue($letter.'1', $header);
			}
			
			foreach ($responseData as $k => $registros) {
				if ($k != 0) {
					foreach ($registros as $position => $registro) {
						$letter =  $this->number_to_alphabet($position+1);
						$Planilla->setActiveSheetIndex(0)->setCellValue($letter.($k+1), $registro);
					}
				}
			}
			
			$objWriter = PHPExcel_IOFactory::createWriter($Planilla, 'Excel2007');
			if ($campaniaResposneData[0]->metodo == 'SLACK (CSV)') {
                $objWriter->save(APPPATH.$filename);
				$this->sendXlsBySlack($idCampania, $responseData, APPPATH . $filename, $mpResposneData[0]);
			} else {
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="' . $filename . '"');
				header('Cache-Control: max-age=0');
				
				$objWriter->save('php://output');
			}
			
		}
		
	}
	
	private function number_to_alphabet($number) {
		$number = intval($number);
		if ($number <= 0) {
			return '';
		}
		$alphabet = '';
		while($number != 0) {
			$p = ($number - 1) % 26;
			$number = intval(($number - $p) / 26);
			$alphabet = chr(65 + $p) . $alphabet;
		}
		return $alphabet;
	}
	
	public function getSlackActiveUsersAndChannels_get()
	{
		$response = $this->apiCronogramasGet(
			URL_CAMPANIAS . 'api/ApiSlack/getActiveUsersAndChannels'
		);
		
		$response = json_decode($response);
		
		$this->response($response->data);
	}
	
	public function getSlackNotificados_post()
	{
		$params = [
			'id_camp' => $this->input->post('id_campania')
		];
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/getSlackNotificados',
			$params
		);
		$response = json_decode($response);
		
		$this->response($response->data);
	}
	
	public function saveSlackNotificados_post()
	{
		$params = [
			'slack_ids' => $this->input->post('slack_ids'),
			'camp_id' => $this->input->post('camp_id')
		];
		
		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/saveSlackNotificados',
			$params
		);

		$response = json_decode($response);
		
		$this->response($response->data);
	}
	
	
	/**
	 * Guarda el whatsapp template del whatsapp
	 * 
	 * @return void
	 */
	public function saveWappTemplateId_post()
	{
		$templateId = $this->input->post('templateId');
		$camp_id = $this->input->post('camp_id');
		
		$response = $this->cronograma_model->saveCronogramaCampaniasWhatsappTemplateId($camp_id, $templateId);
		
		$this->response(['data'=>$response]);
	}
	
	/**
	 * Obtiene los whatsapp templates de una campania
	 * 
	 * @return void
	 */
	public function getWhatsappTemplatesByCampaignId_post()
	{
		$campaignId = $this->post('campaignId');
		$response = $this->cronograma_model->getCronogramaCampaniasWhatsappTemplates($campaignId);
		
		$this->response(['data'=>$response]);
	}
	
	/**
	 * Borra un whatsapp template de una campania 
	 * 
	 * @return void
	 */
	public function deleteWhatsappTemplateById_post()
	{
		$id = $this->post('id');
		$response = $this->cronograma_model->deleteWhatsappTemplateById($id);
		
		$this->response(['data'=>$response]);
	}
	
	/**
	 * Obtiene un whatsapp template por ID
	 * 
	 * @return void
	 */
	public function getWhatsappTemplateById_post()
	{
		$id = $this->post('id');
		$response = $this->cronograma_model->getWhatsappTemplateById($id);
        
		$this->response(['data'=>$response]);
	}

    public function LLAMADA_NEOTELL_post()
    {
        $agente= $this->input->post("USUARIO");
        $telefono= $this->input->post("TELEFONO");
        $server= $this->input->post("server");
        if ($agente!="" || $telefono!="") {
            
            $res = $this->neotell_library->LLAMAR_NEOTELL($agente,$telefono,$server);
            
        }else{
            print_r("Telefono y id agente no encontrado para procesar la peticion");
        }


        return $res;
    }

    public function COLGAR_NEOTELL_post()
    {
        $agente= $this->input->post("USUARIO");
        $server= $this->input->post("server");
        
        if ($agente!="") {
            
            $res = $this->neotell_library->COLGAR_NEOTELL($agente, $server);
            
        }else{
            print_r("Agente no encontrado para procesar la peticion");
        }


        return $res;
    }

    public function DISPONIBLE_NEOTELL_post()
    {
        $id_agente= $this->input->post("USUARIO");
        $server= $this->input->post("server");
        
        if ($id_agente!="") {
            
            $res = $this->neotell_library->DISPONIBLE_NEOTELL($id_agente, $server);
            
        }else{
            print_r("Agente no encontrado para procesar la peticion");
        }


        return $res;
    }

    public function BuscarCreditosxEquipoTurno_post()
    {
        $sl_antiguedad = $this->input->post('sl_antiguedad');
        $sl_condicion = $this->input->post('sl_condicion');
        $sl_tipo_solicitud = $this->input->post('sl_tipo_solicitud');
        $sl_logica = $this->input->post('sl_logica');

        /*VALORES*/
        $currency_rangeA = $this->input->post('currency_rangeA');//100.00
        $currency_rangeB = $this->input->post('currency_rangeB');
        $dias_atrasoA = $this->input->post('dias_atrasoA');
        $dias_atrasoB = $this->input->post('dias_atrasoB');
        $id_usuario  = $this->input->post('id_usuario');

        $dFecha      = $this->input->post('date_rangeA');
        $dia         = substr($dFecha, 3, 2); //29/07/2017
        $mes         = substr($dFecha, 0, 2);
        $anio        = substr($dFecha, 6, 4);
        $fechadesde = $anio . "-" . $mes . "-" . $dia;


        $dFechaR      = $this->input->post('date_rangeB');
        $dia2         = substr($dFechaR, 3, 2); //29/07/2017
        $mes2         = substr($dFechaR, 0, 2);
        $anio2        = substr($dFechaR, 6, 4);
        $fechahasta = $anio2 . "-" . $mes2 . "-" . $dia2;

        // No está vacía (true) la convierto en mi variable global parametros minimos
        if ($this->input->post('date_rangeA')!="") {$minvalue = $fechadesde;}else if ($this->input->post('currency_rangeA')!="") {$minvalue = $currency_rangeA;}else{$minvalue = $dias_atrasoA;}
        if ($this->input->post('date_rangeB')!="") {$maxvalue = $fechahasta;}else if ($this->input->post('currency_rangeB')!="") {$maxvalue = $currency_rangeB;}else{$maxvalue = $dias_atrasoB;}
        //if (!empty($this->input->post('date_rangeB'))) {$maxvalue = $fechahasta;}else{$maxvalue = $currency_rangeB;}
        //var_dump($minvalue,$maxvalue);die;
        


        $param = array(
            
            'sl_condicion' => $sl_condicion, 
            'sl_antiguedad' => $sl_antiguedad, 
            'sl_logica' => $sl_logica, 
            'minvalue' => $minvalue, 
            'maxvalue' => $maxvalue, 
            
        );


            $datos  = $this->Supervisores_model->BuscarCreditosxEquipoTurno($param);
            if (!empty($datos)) {
                $status = parent::HTTP_OK;
                $response['status']['code'] = parent::HTTP_OK;
                $response['status']['ok'] = TRUE;
                $response['casos_distribuidos'] = $datos;
                
                $this->response($response, $status);
            }else{
                $status = parent::HTTP_OK;
                $response['status']['code'] = parent::HTTP_BAD_REQUEST;
                $response['status']['ok'] = TRUE;
                $response['casos_distribuidos'] = 0;
                
                $this->response($response, $status);
            }
            
            
        
    }

    function DistribuirCasos_post()
    {
        $sl_antiguedad = $this->input->post('sl_antiguedad');
        $sl_condicion = $this->input->post('sl_condicion');
        $sl_tipo_solicitud = $this->input->post('sl_tipo_solicitud');
        $sl_logica = $this->input->post('sl_logica');

        /*VALORES*/
        $currency_rangeA = $this->input->post('currency_rangeA');//100.00
        $currency_rangeB = $this->input->post('currency_rangeB');
        $dias_atrasoA = $this->input->post('dias_atrasoA');
        $dias_atrasoB = $this->input->post('dias_atrasoB');
        $id_usuario  = $this->input->post('id_usuario');

        $dFecha      = $this->input->post('date_rangeA');
        $dia         = substr($dFecha, 3, 2); //29/07/2017
        $mes         = substr($dFecha, 0, 2);
        $anio        = substr($dFecha, 6, 4);
        $fechadesde = $anio . "-" . $mes . "-" . $dia;


        $dFechaR      = $this->input->post('date_rangeB');
        $dia2         = substr($dFechaR, 3, 2); //29/07/2017
        $mes2         = substr($dFechaR, 0, 2);
        $anio2        = substr($dFechaR, 6, 4);
        $fechahasta = $anio2 . "-" . $mes2 . "-" . $dia2;

        // No está vacía (true) la convierto en mi variable global parametros minimos
        if ($this->input->post('date_rangeA')!="") {$minvalue = $fechadesde;}else if ($this->input->post('currency_rangeA')!="") {$minvalue = $currency_rangeA;}else{$minvalue = $dias_atrasoA;}
        if ($this->input->post('date_rangeB')!="") {$maxvalue = $fechahasta;}else if ($this->input->post('currency_rangeB')!="") {$maxvalue = $currency_rangeB;}else{$maxvalue = $dias_atrasoB;}
        //if (!empty($this->input->post('date_rangeB'))) {$maxvalue = $fechahasta;}else{$maxvalue = $currency_rangeB;}
        //var_dump($minvalue,$maxvalue);die;
        


        $param = array(
            
            'sl_condicion' => $sl_condicion, 
            'sl_antiguedad' => $sl_antiguedad, 
            'sl_logica' => $sl_logica, 
            'minvalue' => $minvalue, 
            'maxvalue' => $maxvalue, 
            
        );

            // var_dump($this->input->post());die;
            $datos  = $this->Supervisores_model->BuscarCreditosxEquipoTurno($param);
            
            $colombia=1;
            $argentina_m=3;
            $argentina_t=3;
            $casos_colombia=0;
            $casos_argentina_m=0;
            $casos_argentina_t=0;

            //var_dump($datos);die;
            foreach ($datos as $key => $value) {
                
                if ($colombia < 7 ) {
                    #insertar en tabla pasada id_creidito,equipo igual a colombia 60%
                    $params = array(
                        "id_credito" => $value['id_credito'],
                        "equipo" => "COLOMBIA",

                    );
                    if ($this->Supervisores_model->insertEquipo_caso($params)) {
                     $casos_colombia ++;
                     
                    }

                    
                    $colombia ++;
                    $argentina_m = 1;
                }else{
                            if ($argentina_m < 5) {
                                    $params = array(
                                        "id_credito" => $value['id_credito'],
                                        "equipo" => "ARGENTINA",

                                    );
                                    if ($this->Supervisores_model->insertEquipo_caso($params)) {
                                     $casos_argentina_m ++;
                                     
                                    }
                                #insertar en tabla pasada id_creidito,equipo igual a argentina_m 20%
                                $argentina_m ++;
                                
                            }else{
                                
                                    $colombia = 1;

                                        $params = array(
                                            "id_credito" => $value['id_credito'],
                                            "equipo" => "COLOMBIA",

                                        );
                                        if ($this->Supervisores_model->insertEquipo_caso($params)) {
                                         $casos_colombia ++;
                                         
                                        }

                                        
                                        $colombia ++;
                                
                            }
                }
                
            }
            $status = parent::HTTP_OK;
            $response['status']['code'] = parent::HTTP_OK;
            $response['status']['ok'] = TRUE;
            $response['casos_colombia'] = $casos_colombia;
            $response['casos_argentina'] = $casos_argentina_m;
            $this->response($response, $status);


    }
	
	/**
	 * Envia un archivo CSV por la api de slack
	 * 
	 * @param $idCampania
	 * @param array $responseData
	 * @param $filename
	 */
	private function sendCsvBySlack($idCampania, array $responseData, $filename, $mensajeProgramado): void
	{
		$params = [
			'id_camp' => $idCampania
		];
		
		$responseSlacksIds = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/getSlackNotificados',
			$params
		);
		$responseSlacksIds = json_decode($responseSlacksIds);
		$responseSlacksIdsData = (array)$responseSlacksIds->data;
		
		$f = fopen($filename, 'w');
		foreach ($responseData as $campos) {
			fputcsv($f, $campos, ';');
		}
		fclose($f);

		$msg = $this->getSlackCsvMsg($idCampania, $mensajeProgramado);
		
		foreach ($responseSlacksIdsData as $responseSlacksIdsDatum) {
			
			$filename2 = realpath($filename);
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			$mimetype = $finfo->file($filename2);
			
			$file = new CURLFILE($filename2, $mimetype);
			$data = array(
				'to' => $responseSlacksIdsDatum->slack_id,
				'msg' => $msg,
				'file' => $file
			);
			$request_headers = [];
			// var_dump($data);die;
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => URL_CAMPANIAS . 'api/ApiSlack/sendMessage',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $data,
				CURLOPT_HTTPHEADER => $request_headers
			));
			
			$response = curl_exec($ch);
			curl_close($ch);
		}
		
		echo "<script>window.close();</script>";
		unlink($filename);
	}
	
	/**
	 * Envia un archivo XLS por la api de slack
	 *
	 * @param $idCampania
	 * @param array $responseData
	 * @param $filename
	 */
	private function sendXlsBySlack($idCampania, array $responseData, $filename, $mensajeProgramado): void
	{
		$params = [
			'id_camp' => $idCampania
		];
		
		$responseSlacksIds = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/getSlackNotificados',
			$params
		);
		$responseSlacksIds = json_decode($responseSlacksIds);
		$responseSlacksIdsData = (array)$responseSlacksIds->data;
		
		$msg = $this->getSlackCsvMsg($idCampania, $mensajeProgramado);
		
		foreach ($responseSlacksIdsData as $responseSlacksIdsDatum) {
			
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			$mimetype = $finfo->file($filename);
			
			$file = new CURLFILE($filename, $mimetype);
			$data = array(
				'to' => $responseSlacksIdsDatum->slack_id,
				'msg' => $msg,
				'file' => $file
			);
			$request_headers = [];
			// var_dump($data);die;
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => URL_CAMPANIAS . 'api/ApiSlack/sendMessage',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $data,
				CURLOPT_HTTPHEADER => $request_headers
			));
			
			$response = curl_exec($ch);
			curl_close($ch);
		}
		
		echo "<script>window.close();</script>";
		unlink($filename);
	}
	
	private function getSlackCsvMsg($idCampania, $mensajeProgramado)
	{
		$campResp = $this->apiCronogramasPost(
			URL_CAMPANIAS . 'api/ApiCronogramaCampania/getCampania',
			['id_camp' => $idCampania]
		);
		$campResp = json_decode($campResp);
		$campRespData = (array) $campResp->data;
		
		$msg = 'Campaña: ' . $campRespData[0]->nombre_logica . PHP_EOL;
		$msg .= 'Proveedor: ' . $campRespData[0]->proveedor . PHP_EOL;
		$msg .= 'Servicio: ' . $campRespData[0]->type_logic . PHP_EOL;
		$msg .= 'Dia: ' . $mensajeProgramado->day . PHP_EOL;
		$msg .= 'Hora: ' . $mensajeProgramado->hour . PHP_EOL;
		
		if ($campRespData[0]->type_logic == "IVR" or $campRespData[0]->type_logic == "SMS") {
			$msg .= 'Mensaje: ' . PHP_EOL . $mensajeProgramado->mensaje . PHP_EOL;
		}
		
		return $msg;
	}

    public function test_response_neotell_post()
    {
        $array_response= [];
        $array_response['DEVICE']= "SIP\/Ariana.cirielli";
        $array_response['IP']= "192.168.1.101";
        $array_response['PORT']= "53414";
        $array_response['STATUS']= "OK";
        $array_response['SERVER']= "NEO1";
        $array_response['USUARIO']= "1013";
        $array_response['INICIO_LOGIN']= "12\/08\/2021 13:14:24";
        $array_response['SAL_CAMPA\u00d1A_DEFAULT']= "19";
        $array_response['INICIO_LOGIN_CAMPA\u00d1A']= "12\/08\/2021 13:28:35";
        $array_response['CHANNEL']= "SIP\/Ariana.cirielli-00002c88";
        $array_response['SUB_ESTADO']= "AGENT";
        $array_response['SUB_ESTADO_UTL']= "AGENT";
        $array_response['RINGING']= "01\/01\/0001 00:00:00";
        $array_response['DIALING']= "01\/01\/0001 00:00:00";
        $array_response['AGENT']= "01\/01\/0001 00:00:00";
        $array_response['TIEMPO_LLAMADA']= "12\/08\/2021 13:59:09";
        $array_response['SUBTIPO_DESCANSO']= "0";
        $array_response['INICIO_DESCANSO']= "01\/01\/2000 00:00:00";
        $array_response['TIEMPO_DESCANSO']= "01\/01\/2000 00:00:00";
        $array_response['ESTADO_CRM']= "GetContact";
        $array_response['INICIO_CRM']= "12\/08\/2021 13:59:09";
        $array_response['CAMPA\u00d1A']= "19";
        $array_response['CAMPA\u00d1A_ULT']= "19";
        $array_response['COLA']= "200019";
        $array_response['COLA_ULT']= "200019";
        $array_response['DNIS']= "";
        $array_response['ANI']= "";
        $array_response['TELEFONO']= "573132863910";
        $array_response['ANI_TELEFONO_ULT']= "573132863910";
        $array_response["ANI_TELEFONO_ULT"] = "573132863910";
        $array_response["TIPO_LLAMADA"] = "OriginateExt";
        $array_response["TIPO_LLAMADA_ULT"] = "OriginateExt";
        $array_response["ORIGEN_LLAMADA"] = "NO PROCESADO";
        $array_response["ORIGEN_LLAMADA_ULT"] = "NO PROCESADO";
        $array_response["DIRECCION"] = "SALIENTE";
        $array_response["DIRECCION_ULT"] = "SALIENTE";
        $array_response["CRM"] = "-1";
        $array_response["BASE"] = "134";
        $array_response["IDCONTACTO"] = "641175";
        $array_response["DATA"] = "1785";
        $array_response["CLAVE"] = "";
        $array_response["CAMPO_BUSQUEDA"] = "";
        $array_response["IDAGENDA"] = "";
        $array_response["IDLLAMADA"] = "883545";
        $array_response["IDLLAMADA_ULT"] = "883545";
        $array_response["CONFERENCIA"] = "";
        $array_response["GRABANDO"] = "SI";
        $array_response["GRABACION"]="573132863910-883545-20210812135850.mp3";
        $array_response["TELEFONO_DESVIO"] = "";
        $array_response["SAL_TIPO_DISCADOR"] = "Predictive";
        $array_response["SAL_CRM"] = "-1";
        $array_response["SAL_BASE"] = "134";
        $array_response["CANALES_ASOCIADOS"] = "\u00a6\u00a6SIP\/IPLAN-00002c81";
        $array_response["redirect_url"] = "https:\/\/testbackend.solventa.co\/backend\/atencion_cobranzas\/renderCobranzas";
        // function defination to convert array to xml

       /* $pieces = explode("|", $xml[0]);
           
            foreach ($pieces as $key => $value) {

                
                if ($value !="") {
                    
                    $varclean = explode("=", $value);
                    $aux[$varclean[0]] = $varclean[1];     

                }           

             
            }*/

$array_ini= [];
foreach ($array_response as $key => $value) {
    $array_ini[]= $key."=".$value;
}
$array_nuevo= implode("|", $array_ini);

//var_dump($array_nuevo);die;

$response= '<?xml version="1.0" encoding="utf-8"?>
<string xmlns="http://tempuri.org/">'.$array_nuevo.'</string>';
return $response;
//var_dump(json_encode($array_response));

    }


    private function array_to_xml( $data, &$xml_data ) {
        foreach( $data as $key => $value ) {
            if( is_array($value) ) {
                if( is_numeric($key) ){
                    $key = 'item'.$key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
         }
    }

    public function listar_configuraciones_post()
    {
        $dataArray = [];
        $rs_consulta = $this->Supervisores_model->get_all_config();
        $rs_operadores = $this->Operadores_model->get_operadores_by_tipo();
        $dataArray["config"]= $rs_consulta;
        $dataArray["operadores"]= $rs_operadores;
    //  var_dump($dataArray);die;
                if(!empty($rs_consulta)){
				    $status = parent::HTTP_OK;
				    $response =  
                    [
                        'code' => $status, 
                        'ok' => TRUE,
                        'data' => $dataArray
                    ];
               }else
               {
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response =  
                    [
                        'code' => $status, 
                        'ok' => FALSE,
                        'error' => "Falló al consultar los grupos"
                    ];

			   }                

	       
        
       	$this->response($response,$status);

    }

    public function cambiar_parametros_post()
    {
        $rs_update = $this->Supervisores_model->updateParams($this->input->post());
        // var_dump($rs_update);die;
            if($rs_update != 0 ){
                $status = parent::HTTP_OK;
                $response =  
                [
                    'code' => $status, 
                    'ok' => TRUE,
                    'data' => $rs_update
                ];
            }else{
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response =  
                    [
                        'code' => $status, 
                        'ok' => FALSE,
                        'error' => "Falló la actualizacion del campo"
                    ];

            }   
        
        $this->response($response,$status);

    }

    public function cambiar_estado_operador_post()
    {
        $id_operador = $this->input->post('id_operador');
        $tipo_operador = $this->input->post('tipo_operador');
        $activo = $this->input->post('activo');

        if ($tipo_operador == 5 || $tipo_operador == 6)
        {
            /*desvinculo operador de campañas automaticas cobranzas*/
                $data = ["id_operador"=>$id_operador];
                 $curl = curl_init();

                    curl_setopt_array($curl, array(
                    CURLOPT_URL => DESVINCULAR_CAMPANIAS_AUTOMATICAS,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $data,
                    
                ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                    echo "cURL Error #:" . $err;
                    } else {
                    echo $response;
                    }
            /*desvinculo operador de campañas automaticas cobranzas*/

        }
        // actualizo saco asignacion gestion obligatoria

        $rs_search = $this->Supervisores_model->update_gestion_obligatoria($id_operador);
        // busco para insertar
        $rs_search = $this->Supervisores_model->search_operador_campanias_chat($id_operador);
        if(!$rs_search)
        {

            // inserto registro
            $data =array(
                "id_operador" =>$id_operador,
                "estado" =>($activo==1)?1:0,
                "fecha" =>date("Y-m-d H:i:s") ,
            );
            $rs_insert = $this->Supervisores_model->insert_operadores_gestion_chat($data);
        }

        


    }

    public function desactivar_estado_operador_post()
    {
        $id_operador = $this->input->post('id_operador');
        $tipo_operador = $this->input->post('tipo_operador');
        $activo = $this->input->post('activo');
       
        $rs_delete = $this->Supervisores_model->delete_operadores_gestion_chat($id_operador);
    }
    
    
	public function get_reglas_automaticas_get() 
    {
        try {
		
		    $asig_automatica = new AsigAutomatico($this->Supervisores_model);
		    $result = $asig_automatica->get_reglas_automatico();
		
		    if (!empty($result)) {
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => true, 'data' => $result];
		    } else {
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => false, 'message' => 'No fue posible cargar la tabla.'];
		    }
		    $this->response($response);
		
		} catch (Throwable $th) {
		
		}

 
    }
    
	public function get_track_reglas_automaticas_get() 
    {
        try {
		
		    $asig_automatica = new AsigAutomatico($this->Supervisores_model);
		    $result = $asig_automatica->get_track_reglas_automatico();
		
		    if (!empty($result)) {
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => true, 'data' => $result];
		    } else {
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => false, 'message' => 'No fue posible cargar la tabla'];
		    }
		    $this->response($response);
		
		} catch (Throwable $th) {
		
		}

 
    }
    
    
	public function cambio_estado_reglas_automatico_post() 
    {
        try {
			$parametros = $this->input->post();
		    
		    $asig_automatica = new AsigAutomatico($this->Supervisores_model);
			$operador = $asig_automatica->get_operador($this->session->userdata('id_usuario'));
		    $result = $asig_automatica->cambio_estado_reglas_automatico($parametros['id'], $parametros['estado'], $parametros);
		
		    if ($result) {
				$asig_automatica->set_track_reglas_automatico($operador);
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => true];
		    } else {
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => false, 'message' => 'No fue posible cambiar estado'];
		    }
		    $this->response($response);
		
		} catch (Throwable $th) {
		
		}

 
    }
    
	public function update_reglas_automatico_post() 
    {
        try {
			$parametros = $this->input->post();
			
		    $asig_automatica = new AsigAutomatico($this->Supervisores_model);
			
			$operador = $asig_automatica->get_operador($this->session->userdata('id_usuario'));
		    $result = $asig_automatica->update_reglas_automatico($parametros);
		
		    if ($result) {
				$asig_automatica->set_track_reglas_automatico($operador);
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => true];
		    } else {
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => false, 'message' => 'No fue posible cambiar estado'];
		    }
		    $this->response($response);
		
		} catch (Throwable $th) {
		
		}

 
    }
    
    
	public function get_all_situaciones_laborales_get() 
    {
        try {
		
		    $asig_automatica = new AsigAutomatico($this->Supervisores_model);
		    $result = $asig_automatica->get_all_situaciones_laborales();
		
		    if (!empty($result)) {
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => true, 'data' => $result];
		    } else {
		        $status = parent::HTTP_OK;
		        $response = ['status' => $status, 'ok' => false, 'message' => 'No fue posible cargar situacion laboral.'];
		    }
		    $this->response($response);
		
		} catch (Throwable $th) {
		
		}

 
    }

    public function searchTypeLogic_post()
    {
        $data_type = $this->Supervisores_model->obtenerDataCampanias($this->input->post("idCampania"));
        echo json_encode($data_type);
    }

    public function dataMostrar_post()
    {
        $id = $this->post('id');
        $dataCampania = $this->Supervisores_model->obtenerData($id);
        $id_campania = json_decode($dataCampania[0]["params"]);
        $data = $this->Supervisores_model->obtenerDataCampanias($id_campania->idCampania, $id_campania->templateId);
        if (!empty($data) && $data[0]["type_logic"] != "WSP") {
            $this->response(['data'=>$data[0]]);
        }else{
            $this->getTemplateById($id_campania->templateId);
        }
    }

    public function getTemplateById($idTemplate)
    {
		$response = $this->cronograma_model->getWhatsappTemplateById($idTemplate);        
		$this->response(['data'=>$response]);
    }
}
