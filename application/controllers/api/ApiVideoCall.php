<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;

//**************************** */

class ApiVideoCall extends REST_Controller
{
	public function __construct()
	{
        parent::__construct();

        $this->load->model('VideoCall_model','videollamadas', TRUE );
        $this->load->model('Solicitud_m','solicitud_model',TRUE);

		$this->load->library('User_library');
		$this->load->helper(['jwt', 'authorization']); 
    }
    
    public function get_token_post() {
        $data = $this->input->post();
        $documento  = $data['documento'];
        $id_solicitud  = $data['id_solicitud'];
        $action     = ($data['newtoken'] == 'true') ? true : false;
        $idoperador = $this->session->userdata('idoperador');
        $row = $this->videollamadas->get_status_videoCall(array('idOperador' => $idoperador, 'documento' => $documento, 'status' =>0, 'activeRoom' => 0 ));
        $tel = $this->_get_Agendatelefono($documento, $id_solicitud);
        $response['t']['sms'] = (count($tel['sms']) > 0) ? true : false;
        $response['t']['ws'] = (count($tel['ws']) > 0) ? true : false;
        $response['status'] = false; 
        if (isset($row)) { 
            $link = URL_VIDEOLLAMADA."llamada/?roomCode=";

            $linkresponse = array("cliente" => $link.$row->clienteToken, "operador" => $link.$row->opToken);
            $response['status'] = true; 
            $response['link'] = $linkresponse; 
        } elseif($action) {
            if ( !empty($idoperador)) 
                $idoperador_encrypted = AUTHORIZATION::encodeData( $idoperador );
    
            if ( !empty($idoperador)) 
                $documento_encrypted = AUTHORIZATION::encodeData( $documento );
            if ( !empty($idoperador)) 
                $id_solicitud_encrypted = AUTHORIZATION::encodeData( $id_solicitud );
    
            $end_point = URL_VIDEOLLAMADA.'api/Videollamada/tokenCreation/';
            $resp = Requests::post($end_point, [], ['documento'  => $documento_encrypted ,'idOperador'  => $idoperador_encrypted, 'id_solicitud'  => $id_solicitud_encrypted ]);    
            $response['link'] = json_decode($resp->body);
            $response['status'] = true; 
        }

        $this->response($response);
    }

    public function get_token_v2_post() {
        $data = $this->input->post();
        $documento  = $data['documento'];
        $id_solicitud  = $data['id_solicitud'];
        $action     = ($data['newtoken'] == 'true') ? true : false;
        $idoperador = $this->session->userdata('idoperador');
        $row = $this->videollamadas->get_status_videoCall(array('idOperador' => $idoperador, 'documento' => $documento, 'status' => 0 , 'id_solicitud' => $id_solicitud ));
        $tel = $this->_get_Agendatelefono($documento, $id_solicitud);
        $response['t']['sms'] = (count($tel['sms']) > 0) ? true : false;
        $response['t']['ws'] = (count($tel['ws']) > 0) ? true : false;
        $response['status'] = false;
        
        if ($action && !isset($row)) {
            if ( !empty($idoperador)) 
                $idoperador_encrypted = AUTHORIZATION::encodeData( $idoperador );
            if ( !empty($idoperador)) 
                $documento_encrypted = AUTHORIZATION::encodeData( $documento );
            if ( !empty($idoperador)) 
                $id_solicitud_encrypted = AUTHORIZATION::encodeData( $id_solicitud );

            $end_point = URL_VIDEOLLAMADA.'api/Videollamada/tokenCreation/';
            $resp = Requests::post($end_point, [], ['documento'  => $documento_encrypted ,'idOperador'  => $idoperador_encrypted, 'id_solicitud'  => $id_solicitud_encrypted ]);
            $response['link'] = json_decode($resp->body);
            $response['status'] = true;
        } elseif(isset($row)) { 
            $link = URL_VIDEOLLAMADA."llamada/?roomCode=";

            $linkresponse = array("cliente" => $link.$row->clienteToken, "operador" => $link.$row->opToken);
            $response['status'] = true; 
            $response['link'] = $linkresponse; 
        }

        $this->response($response);
    }

    public function get_status_videoCall_post(){
        $data = $this->input->post();
        $documento  = $data['documento'];
        $idoperador = $this->session->userdata('idoperador');
        $row = $this->videollamadas->get_status_videoCall(array('idOperador' => $idoperador, 'documento' => $documento, 'status' =>0 ));
        $this->response($row);
    }

    public function cerrar_llamada_post() {
        $data = $this->input->post();

        $end_point = URL_VIDEOLLAMADA."api/videollamada/close_room/".$data['sid']."/".$data['opToken'];
        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_send', function($fp){
            curl_setopt($fp, CURLOPT_TIMEOUT, 300);
        });
        $request = Requests::get($end_point, array(),array('hooks' => $hooks));
        $resp = json_decode($request->body);
        $response['resp'] = $resp;
        if($resp){            
            $response['status']['ok'] = TRUE;
        } else{
            $response['status']['ok'] = FALSE;
        }
		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
    }

    function _get_Agendatelefono($doc, $id_solicitud) {
        $tlfSolicitud = $this->solicitud_model->getSolicitudes(['id' => $id_solicitud])[0]['telefono'];
        $data = [
            "documento" => $doc,
            "numero" => $tlfSolicitud,
            "fuentes" => "'PERSONAL DECLARADO', 'PERSONAL'",
            "estado" => 1,
            "limit" => 1
        ];
        $sms = $this->solicitud_model->get_agenda_personal_solicitud($data);
        $response['sms'] = $sms;
        if (count($sms) > 0 ) {
            $response['ws'] = $this->solicitud_model->get_agenda_whatsapp(["documento" => $doc,"status_chat" => 'activo', "from" => $sms[0]['numero']]);
        }
        return $response;
    }


    public function send_msj_post(){
        $data = $this->input->post();
        $doc = $data['documento'];
        $link = $data['l'];
        $action = $data['action'];
        $agenda = $this->_get_Agendatelefono($doc, $data['id_solicitud']);
        $name = explode(' ', $agenda['sms'][0]['contacto']);
        $msj = "Hola ".$name[0].", soy de SOLVENTA vamos a contactarnos a traves de videollamadas por el siguiente link." ;

        if ($action == 'sms') {            
            if (count($agenda['sms']) > 0) {
                $endPoint = URL_CAMPANIAS."ApiEnvioComuGeneral"; 
                $response['sms'] = Requests::post($endPoint, [], ["tipo_envio" => 2,"servicio" => 2,"text" => $msj.$link ,"numero" => "+57".$agenda['sms'][0]['numero']]); 
                $response['sms']->msj = "Sms Enviado";
            }
        }
        if ($action == 'ws') { 
            if (count($agenda['ws']) > 0) {
                $endPoint = base_url()."comunicaciones/twilio/send_new_message";
                $response['ws']->r1 = Requests::post($endPoint, [], ['chatID'  => $agenda['ws'][0]['id'],'message' => $msj, 'operatorID' => 192 ]); 
                sleep(2);
                $response['ws']->r2 = Requests::post($endPoint, [], ['chatID'  => $agenda['ws'][0]['id'],'message' => $link,'operatorID' => 192 ]); 
                $response['ws']->msj = "Sms Enviado";
            }
        }

		$status = parent::HTTP_OK;
		$response['status']['code'] = $status;
		$this->response($response, $status);
    }

    public function update_video_post() {
        $data = $this->input->post();
        $id_solicitud = $data['id_solicitud'];
        $documento  = $data['documento'];
        $condition = [
            'id_solicitud' => $id_solicitud,
            'documento' => $documento,
            'status' => 1,
            'recording' => 1,
            'CompositionStatus' => 'composition-available'
        ];

        $list_videollamadas =  $this->videollamadas->get_list_videollamadas($condition);

        $dataResp = new stdClass();

        foreach ($list_videollamadas as $key => $value) {
            $datos = new stdClass();
            $datos->video = $this->videollamadas->get_solicitud_videos(['id_solicitud' => $id_solicitud, 'cid' => $value->cid]);
            $datos->count = count($datos->video);

            if ($datos->count == 0) {
                $endPoint = URL_VIDEOLLAMADA."api/videollamada/downloadComposition/" . $value->cid;
                // $datos->endpointResp = $endPoint;  
                $datos->endpointResp = Requests::get($endPoint);  
            }
            $dataResp->resp[] = $datos;
        }
        $dataResp->success = TRUE;
        $dataResp->message = "Actualizado";

        $this->response($dataResp);
    }

    public function get_video_status_get($id_solicitud) {
        $datos = new stdClass();
        $data = $this->videollamadas->get_list_videollamadas(['id_solicitud' => $id_solicitud, 'CompositionStatus' => 'composition-available']);
        $count = count($data);
        if ($count > 0) {
            $datos->success = TRUE;
            $datos->message = "Actualizando";
            foreach ($data as $key => $value) {
                $video = $this->videollamadas->get_solicitud_videos(['id_solicitud' => $id_solicitud, 'cid' => $value->cid]);
                $countv = count($video);
                if ($countv > 0) {
                    $datos->success = FALSE;
                }
            }
        } else {
            $datos->success = FALSE;
            $datos->message = "No hay videos disponibles";
        }
        $this->response($datos);
    }
}

?>