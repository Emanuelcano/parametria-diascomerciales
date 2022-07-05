<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiGestion extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
        // Models
            $this->load->model('solicitud_m', 'solicitud_m', TRUE);
            $this->load->model('credito_model', 'credito_model', TRUE);

            $this->load->model('supervisores/Supervisores_model', 'supervisores_model', TRUE);

            $this->load->model('galery_model', '', TRUE);

		
	}
       
    public function get_detalle_marcacion_get($id_solicitud)
	{
        $fecha_hasta = date('Y-m-d H:i:s');
        //get solicitud
        $solicitud = $this->solicitud_m->getSolicitudesBy([ 'id_solicitud' => $id_solicitud ]);
        //get referencia
        $referencia = $this->solicitud_m->getSolicitudesReferencia($id_solicitud);
        //get referencia
        $visado = $this->solicitud_m->getVisado($id_solicitud);

        $agenda =[];
        if (!empty($solicitud)) {
            array_push($agenda, $solicitud[0]);
                
            foreach ($referencia as $key => $value) {
                array_push($agenda, $value);
            }
            
            if(!empty($visado))
            {
                $fecha_hasta = $visado[0]->fecha_creacion;
            }
        } 
        //$agenda = array_unique($agenda);
 //var_dump();
        
        $llamadas=[];
        $aux=[];
        foreach ($agenda as $key => $value) {
            //$aux = $this->credito_model->get_track_detalle_llamadas(["telefono" => $value]);
            //$llamadas = array_merge($llamadas,  $aux );

            //buscamos cada registro por numer de telefono ya que los id_cliente que vienen de la central estan mal
            $aux = $this->credito_model->get_track_detalle_llamadas(['telefono' => $value->telefono, 'fecha_inicio' => $solicitud[0]->fecha_alta, 'fecha_hasta' => $fecha_hasta]);
            foreach ($aux as $key2 => $value2) {
                array_push($llamadas,array_merge((array)$value, (array)$value2));
            }
        }
        
        //var_dump('('.implode(",", $agenda).')');
        //print_r($llamadas);die;
		
        if(!empty($llamadas)){
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = TRUE;
		   	$response['data'] 	 = $llamadas;
		} else {
			$status = parent::HTTP_OK;
			$response['status']['code']  = $status;
		   	$response['status']['ok']	 = FALSE;
		   	$response['message']	 = 'No hay registros de llamada para esta solicitud';
		}
			
		
		$this->response($response, $status);
	}  
    
    
    public function enlace_audios_neotell_post()
    {

        $telefono = $this->input->post('telefono');
        $rs_result= $this->credito_model->get_enlace_audios_neotell($telefono);
        return $rs_result;

    }

    public function getInfoAudio_post()
    {
        
        $end_point = SERVER_FILES_SOLVENTA_URL. '/api/ApiNeotell/consultaAudiosNeotell';
        $telefono = $this->input->post('telefono');
        $central = $this->input->post('central');
        // var_dump($telefono, $central);die;
        $resp = Requests::post($end_point, [], ['telefono' => $telefono,"central" => $central]);
        // var_dump($resp);die;
        $response = json_decode($resp->body);
        $llamados = json_decode($response->resp);

        if($response->code == 200){
            foreach ($llamados as $audio) {
                $esReportaron = $this->esAudioReportado($audio->id_track);
                $audio->reportado = $esReportaron;
                
                $audios[] = $audio;
            }
            
            $response->resp = json_encode($llamados);
        }
        
        $this->response($response, REST_Controller::HTTP_OK);
    }


    private function esAudioReportado($id_track)
    {
        $rs_result= $this->supervisores_model->get_audio_reportado($id_track);
        
        if(!empty($rs_result)){
            return true;
        }else{
            return false;
        }
    }
    
    public function saveAudioReportado_post()
    {
        $id_track = $this->input->post('id_track');
        $fecha_audio = $this->input->post('fecha_audio');
        $tipo_incidente = $this->input->post('tipo_incidente');
        $operador = $this->input->post('operador');
        $data = [
            'id_audio' => $id_track,
            'comentario' => $tipo_incidente,
            'operador' => $operador,
            'fecha_reporte' => date('Y-m-d H:i:s'),
            'tipo_reporte' => 'Reportado',
        ];
        
        $rs_result= $this->supervisores_model->save_audio_reportado($data);
        
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $response['status']['ok']	 = TRUE;
        
        $this->response($response, $status);
    }

    public function getwhatsapp_scans_get($id_solicitud) {
        $whatsapp_scans = $this->galery_model->get_whatsapp_scans(['id_solicitud' => $id_solicitud]);
        if (isset($whatsapp_scans) && !empty($whatsapp_scans)) {
            $response->status    = TRUE;
            $response->data          = $whatsapp_scans;
        } else {
            $response->status    = FALSE;
            $response->message       = 'No hay registros de whatsapp para esta solicitud';
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }


}