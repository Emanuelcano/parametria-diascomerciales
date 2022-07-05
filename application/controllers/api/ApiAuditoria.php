<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;



class ApiAuditoria extends REST_Controller
{      

    public function __construct()
    {

        parent::__construct();
        $method = $this->uri->segment(3);

        $this->load->library('User_library');
        $auth = $this->user_library->check_token();

        if($auth->status == parent::HTTP_OK )
        {
            // MODELS
            $this->load->model('supervisores/Supervisores_model','Supervisores_model',TRUE);
            $this->load->model('Solicitud_m','solicitud_model',TRUE);
            $this->load->model('operadores/Operadores_model','operadores_model',TRUE);
            $this->load->model('auditoria_originacion_cobranzas/Auditoria_model','auditoria_model',TRUE);
            $this->load->model('Credito_model', 'credito_model', TRUE);
            $this->load->helper('requests_helper');
            // LIBRARIES
            $this->load->library('form_validation');
        }else{
            $this->session->sess_destroy();
            $this->response(['redirect'=>base_url('login')],$auth->status);
        }
    }


    /**
    * Obtengo la data para mostrar en la tabla de llamados pendientes de auditar.
    *   
    * @return Array $data
    */

    public function auditarLlamadas_get()
    {        
        $fecha_desde = $this->input->get('fecha_desde') ? $this->input->get('fecha_desde') : '';
        $fecha_hasta = $this->input->get('fecha_hasta') ? $this->input->get('fecha_hasta') : '';
        $pais        = $this->input->get('pais') != 'All' ? $this->input->get('pais') : '';
        $operador    = $this->input->get('operador') ? $this->input->get('operador') : '';
        $tipoOperador    = $this->input->get('tipoOperador') ? $this->input->get('tipoOperador') : '(1,4,5,6)';
        $telefono    = $this->input->get('telefono') ? $this->input->get('telefono') : '';
        $central    = $this->input->get('central') ? $this->input->get('central') : 0;
        $limit       = $this->get('start');
        $offset      = $this->get('length');
        if (($fecha_hasta != '' || $fecha_hasta != null) 
        && ($fecha_desde == '' || $fecha_desde == null)) {
            $fecha_desde = date('Y-m-d', strtotime($fecha_hasta."- 1 days"));
        }
        
        if (($fecha_desde == '' || $fecha_desde == null) 
        && ($fecha_hasta == '' || $fecha_hasta == null)
        ) {
            if (ENVIRONMENT == 'development') {
                // $fecha_desde = "2022-05-21";
                // $fecha_hasta = "2022-05-21";
                            
                $fecha_desde = date('Y-m-d');
                $fecha_hasta = $fecha_desde;
            }else{
                $fecha_desde = date('Y-m-d');
                $fecha_hasta = $fecha_desde;
            }
        }

        if (($fecha_hasta == '' || $fecha_hasta == null) 
        && ($fecha_desde != '' || $fecha_desde != null)) {
            // Fecha desde ya la recibimos desde el formulario. Damos el valor false a fecha hasta para poder aplicar otro filtro de búsqueda.
            // $hoy = date('Y-m-d');
            $fecha_hasta =  date('Y-m-d');
        }
        $filtro = [
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'pais' => $pais,
            'operador' => $operador,
            'tipoOperador' => $tipoOperador,
            'telefono' => $telefono,
        ];
        $trackLlamados = $this->auditoria_model->getTrackPorOperador(3, null, $filtro, $limit, $offset);
        
        $data['data'] = $trackLlamados;
        // var_dump($data);die;

        if (count($trackLlamados) >= 1) {
            $this->response($data, REST_Controller::HTTP_OK);
        } else {
            $status = parent::HTTP_OK;
            $response['status']['code']  = $status;
            $response['status']['ok']	 = FALSE;
            $response['message']	     = 'No hay llamados pendientes de auditar.';
            $this->response($response, $status);
        }
    }

    public function saveAudioReportado_post()
    {
        $id_track = $this->input->post('id_track');
        $fecha_audio = $this->input->post('fecha_audio');
        $tipo_incidente = $this->input->post('tipo_incidente');
        $operador = $this->input->post('operador');
        $id_soliciud = $this->input->post('id_solicitud');
        $data = [
            'razon' => $tipo_incidente,
            'fecha_reporte' => date('Y-m-d H:i:s'),
            'operador' => $operador,
            'id_solicitud' => $id_soliciud,
            'id_audio' => $id_track
        ];
        
        $rs_result= $this->auditoria_model->save_audio_reportado($data);
        
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $response['status']['ok']	 = TRUE;
        
        $this->response($response, $status);
    }


    /**
    * Busca por el id del audio de llamado y ve si el caso fue auditado o reportado.
    *   
    * @return Array $data
    */

    private function getInfoLlamado_post($audios, $id_solicitud)
    {
        foreach ($audios as $audio) {

            // Recibe el id del audio del llamado y ve si fue auditado
            $caso_auditados = $this->auditoria_model->casosAuditados_get($audio->id_track, $id_solicitud);
            $caso_reportado = $this->auditoria_model->casosReportados_get($audio->id_track);
            $audio->auditado = 0;
            if (isset($caso_auditados) || isset($caso_reportado)) {
                $audio->auditado = 1;
            }
            
        }
        return $audios;
    }
    /**
    * Busca por nro telefonico y devueve la info del llamado y la url para poder escuchar el audio.
    *   
    * @return Object $response
    */

    public function getOperadores_get($equipo)
    {

        // $filtro['tipo_operadores'] = [1,4];
        // print_r($equipo);die;
        $filtro['where'] = 'estado = 1 AND tipo_operador IN (1,4,5,6)';
        // $filtro['where'] = 'estado = 1 AND tipo_operador = 6';
        if($equipo != 'seleccione_equipo'){
            $filtro['where'] = 'equipo = "'.$equipo.'" AND estado = 1 AND tipo_operador IN (1,4,5,6)';
            // $filtro['where'] = 'equipo = "'.$equipo.'" AND estado = 1 AND tipo_operador = 6';
        }
        

        $response = $this->operadores_model->get_operadores_by($filtro);
        // print_r($response);die;
        $status = parent::HTTP_OK;
        $this->response($response, $status);
                
    }

    /**
    * Busca por nro telefonico y devueve la info del llamado y la url para poder escuchar el audio.
    *   
    * @return Object $response
    */

    public function getInfoAudio($telefono, $central = "0")
    {
        $end_point = SERVER_FILES_SOLVENTA_URL. '/api/ApiNeotell/consultaAudiosGeneral';

        $hooks = new Requests_Hooks();
        $hooks->register('curl.before_send', function($fp){
            curl_setopt($fp, CURLOPT_TIMEOUT, 300);
        });
        $request = Requests::post($end_point, array(),['telefono' => $telefono, 'central' => $central]);
        $response = json_decode($request->body);

        return $response;
    }

    /**
    * Guardo el archivo fisico que se carga en el orulario de la auditoria del llamado.
    *   
    * @return Json con el status y la url del archivo.
    */

    public function uploadFile_post($id_solicitud)
    {
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $response['status']['ok']	 = FALSE;
        $response['message']	 = 'No se pudo guardar el archivo correctamente.';

        $fichero = dirname(BASEPATH) . '/public/auditoria_originacion_cobranzas/originacion/';
        
        if (!file_exists($fichero)) {
            mkdir($fichero, 0700, true);
        }
        
        $fecha_creacion_archivo = date('Ymd_Hisu');
        $nombre_archivo = "'".$fecha_creacion_archivo."-".$id_solicitud."'";
        
        $config['upload_path'] = $fichero; 
        $config['file_name'] = $nombre_archivo;
        $config['allowed_types'] = '*';
        $config['overwrite'] = TRUE;
        $config['max_size'] = "320000";
        $this->load->library('upload');
        $this->upload->initialize($config);
       
        if($this->upload->do_upload('file')){

            $file = $this->upload->data();
            $archivo_ruta = '/public/auditoria_originacion_cobranzas/originacion/'.$file['file_name'];
                        
            $status = parent::HTTP_OK;
            $response = [
                'status'  => ['code' => $status, 'ok' => TRUE],
                'url' => $archivo_ruta
            ];

        }
        $this->response($response, $status);
    }

    public function getAllCalificaciones_get()
    {
        $response = $this->auditoria_model->getAllCalificacion();
        $status = parent::HTTP_OK;
        $this->response($response, $status);
    }

    /**
    * Guardo las calificaciones de la auditoria.
    *   
    * @return Object con el status y response.
    */

    public function audioAuditado($tipo_operador, $estado_solicitud, $parametros, $observacion, $id_solicitud, $ids_track)
    {
        $auditor = $this->session->userdata('idoperador');
        $audio_auditado = array(
            'observaciones'     => $observacion,
            'id_solicitud'     => $id_solicitud,
            'operador_auditor' => $auditor,
            'fecha_auditoria'  => date('Y-m-d H:i:s')
        );
        
        $id_audio_auditado = $this->auditoria_model->saveLlamadoAuditado_post($audio_auditado);

        $resultado_auditoria = [];
        foreach ($parametros as $k => $val) {
            $e = 0;
            foreach ($val as $key => $value) {
                $resultado_auditoria[$k][$e]["id_parametro"] = $value["id"];
                $resultado_auditoria[$k][$e]["nombre_parametro"] = $value["name"];
                $resultado_auditoria[$k][$e]["evaluacion"] = $value["evaluacion"];
                $resultado_auditoria[$k][$e]["id_grupo"] = $value["id_grupo"];
                $e++;
            }
        }
        
        foreach ($resultado_auditoria as $key => $value) {
            foreach ($value as $k => $v) {                
                $calificacion = $this->auditoria_model->getIdCalificacion($v["evaluacion"]);
                
                $resultados = array(
                    'id_auditoria'    => $id_audio_auditado,
                    'id_parametro' => $v["id_parametro"],
                    'id_calificacion'      => $calificacion[0]["id"]
                );
                $auditoria = $this->auditoria_model->saveRespuesta($resultados);
            }
        }

        foreach ( $ids_track as $id_track ){
            
            $data = [
                'id_auditoria' => $id_audio_auditado,
                'id_audio' => $id_track,
                'operador' => $auditor,
                'fecha_reporte' => date('Y-m-d H:i:s'),
                'tipo_reporte' => 'Auditado',
            ];
            
            $audio_auditado = $this->auditoria_model->save_audio_auditado($data);

        }
            
        if($auditoria){
            return true;
        }else{
            return false;
        }
    }

    public function misAuditorias_get()
    {
        $status = parent::HTTP_OK;
        $response['status']['code']  = $status;
        $response['status']['ok']	 = FALSE;
        $response['message']	 = 'No fue posible obtener las auditorias.';

        $auditor = $this->session->userdata('idoperador');
        $data['data'] = $this->auditoria_model->getAuditoriasPorAuditor($auditor);
        if($data){
            
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }

    public function auditoriaGestionOperadorOriginacion_post() 
    {
        $id_solicitud = $this->input->post('id_solicitud');
        $telefono = $this->input->post('telefono');
        $central = $this->input->post('central');
        $tipo_operador = $this->input->post('tipo_operador');
        $data['solicitude'] = $this->_get_solicitude($id_solicitud);
        if(is_null($data['solicitude'][0]['estado'])){
            $data['solicitude'][0]['estado'] = "NULL";
        }

        $data['credito'] = $this->get_detalle_credito($data['solicitude'][0]['id_credito'], $data['solicitude'][0]['id_cliente']);

        $data['acuerdos_pago'] = $this->get_acuerdos_clientes($data['solicitude'][0]['id_cliente']);

        if ($tipo_operador == 5 || $tipo_operador == 6) {
            $data['parametros'] = $this->getParametrosAuditarCobranza();
        }else{
            $data['parametros'] = $this->getParametrosAuditarOriginacion($data['solicitude'][0]['estado']);
        }
        if (empty($data['parametros']) || is_null($data['parametros'])) {
            $data['parametros'] = $this->getParametrosAuditarCobranza();
        }

        $data['analisis'] = $this->get_solicitud_analisis($id_solicitud);
        
        $data['calificaciones'] = $this->auditoria_model->getCalificacionesActivas();

        $data['datos_bancarios'] = $this->get_data_bank($id_solicitud);
        // var_dump($tipo_operador);die;
        $data['tipo_operador'] = $tipo_operador;
        
        $audios = $this->getInfoAudio($telefono, $central);
        $infoLlamada = [];
        
        if($audios->code != 400){
            $esAuditado = $this->getInfoLlamado_post(json_decode($audios->resp), $id_solicitud);
            foreach ($esAuditado as $auditado) {
                if ($auditado->auditado == 0 ) {
                    if (strstr($auditado->path_audio, "neotell")) {
                        $auditado->origen = "Neotell";
                    }else if(strstr($auditado->path_audio, "twilio")){
                        $auditado->origen = "Twilio";
                    }
                    $infoLlamada[] = $auditado;
                }
            }
            if(!empty($infoLlamada)){
                $data['audios'] = $infoLlamada;
                $this->load->view('auditoria_originacion_cobranza/modulos/box_auditar_gestion', $data);
                return $this;
            }else{
                echo json_encode(400);
            }
        }else{ 
            echo json_encode(400);
        }
    }

    private function _get_solicitude($id_solicitude)
    {
        return $this->solicitud_model->getSolicitudes(['id' => $id_solicitude]);
    }

    private function get_solicitud_analisis($id_solicitude)
    {
        return $this->solicitud_model->getSolicitudAnalisis(['id' =>$id_solicitude]);
    }

    private function get_data_bank($id_solicitude)
    {
        return $this->solicitud_model->getDatosBancarios($id_solicitude);
    }

    private function get_acuerdos_clientes($id_cliente = null)
    {
        $acuerdos = 'Sin acuerdos';
        if ($id_cliente != null) {
            $acuerdos = $this->credito_model->getAcuerdosDePagoPorIdCliente($id_cliente) ? $this->credito_model->getAcuerdosDePagoPorIdCliente($id_cliente) : 'Sin acuerdos';
        }
        return $acuerdos;
    }

    private function getParametrosAuditarCobranza()
    {
        $grupos = $this->auditoria_model->getGrupoParametroCobranza();
        
        $parametros = [];
        foreach($grupos as $grupo)
        {
            $parametros[$grupo['nombre']] = $this->auditoria_model->getParametroCobranza($grupo['id']);
            // var_dump($parametros[$grupo['nombre']]);die;
        }

        return $parametros;
    }

    private function getParametrosAuditarOriginacion($estado)
    {
        $grupos = $this->auditoria_model->getGrupoParametroOriginacion($estado);
        
        $parametros = [];
        foreach($grupos as $grupo)
        {
            $parametros[$grupo['nombre']] = $this->auditoria_model->getParametroOriginacion($grupo['id'], $estado);
            // var_dump($parametros[$grupo['nombre']]);die;
        }

        return $parametros;
    }

    private function get_detalle_credito($id_credito = null, $id_cliente = null) 
    {
        return $this->credito_model->getCreditoInfo($id_credito, $id_cliente);
    }

    public function verificarAudios_post()
    {
        if (isset($_POST["id_track"][0])) {
            $data_arr = ["id_track" => $_POST["id_track"][0], "id_solicitud" => $_POST["id_solicitud"]];
        }else{
            $data_arr = $this->input->post();
        }
        $id_sol = $_POST["id_solicitud"];
        $data = json_encode($data_arr);
        $dataObject = [json_decode($data)];
        
        $rs = $this->getInfoLlamado_post($dataObject, $id_sol);
        if (isset($rs[0]->auditado) && $rs[0]->auditado == 1) {
            echo json_decode(true);
        }else{
            echo json_decode(false);
        }
    }

    public function detalleAuditoria_post()
    {
        $data['auditoria'] = $this->auditoria_model->getDetalleAuditoria($_POST["id_auditoria"]);
        // var_dump($data);die;
        $llamados = $this->getInfoAudio($data['auditoria']['auditoria']->numero_telefonico);
        $arr_llamadas = [];
        
        foreach (json_decode($llamados->resp) as $llamado) {
            $arr_llamadas[$llamado->id_track] = $llamado; 
        }
        
        foreach($data['auditoria']['audios_auditados'] as $audio) {
            $llamadas[] = $arr_llamadas[$audio['id_audio']];
        }
        $data['audios'] = $llamadas;
        
        $data['solicitude'] = $this->_get_solicitude($data['auditoria']['auditoria']->id_solicitud);
        
        $data['credito'] = $this->get_detalle_credito($data['solicitude'][0]['id_credito']);

        $data['acuerdos_pago'] = $this->get_acuerdos_clientes($data['solicitude'][0]['id_cliente']);
        // var_dump($data["auditoria"]["detalle_auditoria"]);die;
        $this->load->view('auditoria_originacion_cobranza/modulos/box_mostrar_detalle_auditoria', $data);
    }

    public function tipo_operador_post()
    {   
        $operador = $this->input->post("tipo_operador");
        if ($operador == "(1,4,5,6)") {
            $filtro['where'] = 'estado = 1 AND tipo_operador IN '.$operador;
            if($this->input->post("equipo") != 'seleccione_equipo'){
                $filtro['where'] = 'equipo = "'.$this->input->post("equipo").'" AND estado = 1 AND tipo_operador IN '.$operador;
            }
        }else{
            $filtro['where'] = 'estado = 1 AND tipo_operador = '.$operador;
            if($this->input->post("equipo") != 'seleccione_equipo'){
                $filtro['where'] = 'equipo = "'.$this->input->post("equipo").'" AND estado = 1 AND tipo_operador = '.$operador;
            }
        }

        $response = $this->operadores_model->get_operadores_by($filtro);
        $status = parent::HTTP_OK;
        $this->response($response, $status);
    }

    public function searchMisAuditorias_post()
    {
        $auditor = $this->session->userdata('idoperador');
        $params=[];
        if ($this->input->post("documento") != "") {
            $params["documento"] = $this->input->post("documento");
        }
        if($this->input->post("telefono") != ""){
            $params["telefono"] = $this->input->post("telefono");
        }

        if (!isset($params["telefono"]) && !isset($params["documento"])) {
            $data["data"] = $this->auditoria_model->getAuditoriasPorAuditorSearch($auditor);
        }else{
            $data["data"] = $this->auditoria_model->getAuditoriasPorAuditorSearch($auditor, $params);
        }

        if($data){
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }

    public function obtener_parametros_post()
    {
        $tipo_operador = $this->input->post("tipo_operador");
        $estado_solicitud = $this->input->post("estado");
        $evaluacion = $this->input->post("evaluacion");
        $observacion = $this->input->post("observacion");
        $id_solicitud = $this->input->post("id_solicitud");
        $id_track = $this->input->post("ids_track");
        if ($tipo_operador == 5 || $tipo_operador == 6) {
            $parametros = $this->getParametrosAuditarCobranza();
        }else{
            $parametros = $this->getParametrosAuditarOriginacion($estado_solicitud);
        }
        $conteo_parametro = 0;
        foreach ($parametros as $key => $value) {
            $conteo_parametro += count($value);
        }
        if (count($evaluacion) == $conteo_parametro) {
            
            $e = 0;
            foreach ($parametros as $key => $value) {
                for ($i=0; $i < count($value); $i++) { 
                    $parametros[$key][$i]["evaluacion"] = $evaluacion[$e];
                    $e++;
                }
            }
            $auditar = $this->audioAuditado($tipo_operador, $estado_solicitud, $parametros, $observacion, $id_solicitud, $id_track);
            
            if ($auditar) {
                $data["status"] = 200;
                $data["mensaje"] = "Llamada auditada correctamente";
            }else{
                $data["status"] = 400;
                $data["mensaje"] = "Error al auditar";
            }
            
        }else{
            $data["status"] = 400;
            $data["mensaje"] = "Todos los campos deben estar checkeados.";
        }
        echo json_encode($data);
    }
}
?>


