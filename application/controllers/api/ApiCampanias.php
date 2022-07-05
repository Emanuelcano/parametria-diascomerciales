<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiCampanias extends REST_Controller
{      
    private $_free_methods = array('buscar','listar');

    public function __construct()
    {

        parent::__construct();
        $method = $this->uri->segment(3);

        $this->load->library('User_library');
        $auth = $this->user_library->check_token();

        if($auth->status == parent::HTTP_OK || in_array($method, $this->_free_methods))
        {
            // MODELS
            $this->load->model("InfoBipModel");
            $this->load->model('supervisores/Supervisores_model','Supervisores_model',TRUE);
            $this->load->model('Solicitud_m','solicitud_model',TRUE);
            $this->load->model('operadores/Operadores_model','operadores_model',TRUE);
            $this->load->model('tracker_model','tracker_model',TRUE);
            $this->load->model('Operaciones_model', 'operaciones_model',TRUE);
            $this->load->model('SolicitudAsignacion_model','solicitud_asignacion',TRUE);
            $this->load->model('security/Security_model','Security_model',TRUE);
			$this->load->model("Cliente_model", 'cliente_model', TRUE);
			$this->load->model('cronograma_campanias/Cronogramas_model', 'cronograma_model', TRUE);
			$this->load->model('Chat');
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
   

public function guardarEvento_post(){

    $dFechaR              = $this->input->post('start');
    $dFecInicio           = $dFechaR." ".$this->input->post('hora_ini_campania');


    $dFechaR2             = $this->input->post('end');
    $dFecFinal            = $dFechaR2." ".$this->input->post('hora_fin_campania');

//var_dump($dFechaR ,$dFechaR2, $dFecInicio,$dFecFinal);die;
    $params = array (
      'id_logica' => $this->input->post('id_logica'), 
      'id_proveedor' => $this->input->post('id_proveedor'), 
      'title' => $this->input->post('title'), 
      'color' => $this->input->post('color'), 
      'star' => $dFecInicio, 
      'end' => $dFecFinal, 
      'test_mode' => "NO", 
      'ejecutado' => "PENDIENTE", 
      'estado' => 1, 
    );

if ($this->InfoBipModel->guardar_evento($params) == true) {
        echo 'Campañia Guardada';
    } else {
        echo 'No se pudo guardar los datos';
    }


}

 public function guardarProveedor_post(){
    
    $array_data= $this->input->post();
    //var_dump($array_data);
    
    if ($this->InfoBipModel->guardar_prov($array_data) == true) {
        echo 'Proveedor Guardado';
    } else {
        echo 'No se pudo guardar los datos';
    }

 }

 public function guardarLogicas_post(){
    
    $array_data= $this->input->post();
    //var_dump($array_data);die;
if ($this->consultaTesting_post($array_data['query_contenido'], false)==true) {
    if ($array_data['txt_type_submit']=="insert") {
        /*INSERTA*/
        $datos = array(
                'nombre_logica'          => $array_data['nombre_logica'],
                'id_proveedor'           => $array_data['id_proveedor'],
                'type_logic'             => $array_data['type_logic'],
                'query_contenido'        => $array_data['query_contenido'],
                'mensaje'                => $array_data['mensaje'],
                'estado'                 => $array_data['estado'],
                
            );
        //var_dump($datos);die;
        if ($this->InfoBipModel->guardar_logicas($datos) == true) {
            echo 'Logica Guardada con exito!';
        } else {
            echo 'No se pudo guardar los datos';
        }
    }else{

    /*ACTUALIZA*/
        $datos = array(
                'nombre_logica'          => $array_data['nombre_logica'],
                'id_proveedor'           => $array_data['id_proveedor'],
                'type_logic'             => $array_data['type_logic'],
                'query_contenido'        => $array_data['query_contenido'],
                'mensaje'                => $array_data['mensaje'],
                'estado'                 => $array_data['estado'],
                
            );

            if ($this->InfoBipModel->actualizar_logicas($array_data['id_logica'], $datos) == true) {
                echo 'Logica Actualizada';
            } else {
                echo 'No se pudo actualizar la logica';
            }
    }
}


    

 }


 public function consultaTesting_post($query_contenido=null , $render=null){
    
    
if (IS_NULL($query_contenido)) {
    
    $query_contenido = $this->input->post('query_contenido');
    $render = $this->input->post('render');
    

}

//echo $this->comandosValidos($query_contenido);

    if ($this->comandosValidos($query_contenido)==1) {
        $query_rs= $this->InfoBipModel->consulta_testing($query_contenido);
        if (IS_NULL($render) ) {
        print_r($query_rs);
        }else{
            return true;
        }
        
    }else{

        print_r("Ejecuto un comando invalido");
    }

 }


function comandosValidos ($comando){


    if(stristr($comando, 'DROP')) {
        return "Comando invalido";

    }else if(stristr($comando, 'INSERT')) {
        return "Comando invalido";

    }else if(stristr($comando, 'UPDATE')) {
        return "Comando invalido";
    }else if(stristr($comando, 'DELETE')) {
        return "Comando invalido";

    }else if(stristr($comando, 'TRUNCATE')) {
        return "Comando invalido";
    }else{
        return true;
    }
}

 public function consultaLogicasbyId_post(){
    
    $id_logica= $this->input->post('id_logica');
    $query_rs= $this->InfoBipModel->consultaLogicasbyId($id_logica);
    echo json_encode($query_rs);
    
    

 }

 public function get_all_proveedores_post(){
    
    $rs_pro= $this->InfoBipModel->get_all_proveedores();
    
    echo json_encode($rs_pro);

 }


  public function get_all_logicas_post(){
    
    $rs_log= $this->InfoBipModel->get_all_logicas();
    
    echo json_encode($rs_log);

 }

 public function buscar_cronogramas_get(){

        $event_data= $this->InfoBipModel->buscar_eventos();
        foreach ($event_data as $row) {
                    $data[] = array(
                    'id' => $row['id_cronograma'],
                    'id_proveedor' => $row['id_proveedor'],
                    'id_logica' => $row['id_logica'],
                    'test_mode' => $row['test_mode'],
                    'ejecutado' => $row['ejecutado'],
                    'estado' => $row['estado'],
                    'title' => $row['title'],
                    'start' => $row['star'],
                    'end' => $row['end'],
                    'color' => $row['color']
                   );
            }
        echo json_encode($data);


}
	
	
	/**
	 * Obtiene los casos de campania
	 */
	public function getCasosCampania_post()
	{
		$id = $this->input->post('id');
		if (!is_null($id)) {
			$casos = $this->getCasosCampaniaById($id);
		} else {
			$casos = $this->getCasosCampaniaByPost();
		}
		
		if(!empty($casos)){
			$status = parent::HTTP_OK;
			$response = ['data' => $casos, 'cantidad'=> count($casos)];
		}else{
			$status = parent::HTTP_OK;
			$response = ['data' => ''];
		}
		return $this->response($response, $status);
	}

	/**
	 * Obtiene los casos de la campania crm por post
	 *
	 * @param false $returnQuery En TRUE devuelve el objeto query por si es necesita agregarse mas cosas
	 * 
	 * @return array
	 */
	private function getCasosCampaniaByPost($returnQuery = false)
	{
		$tipo = $this->input->post('tipo');
		$grupo = $this->input->post('grupo');
		$desde = $this->input->post('desde');
		$hasta = $this->input->post('hasta');
		$orden = $this->input->post('orden');
		$exclusiones = $this->input->post('exclusiones');
		$creditosCliente = $this->input->post('credito_cliente');
		$accion = $this->input->post('accion');
		$grupoVentas = $this->input->post('grupoVentas');
		
		$ventasAltaCliente = $this->input->post('ventas_alta_cliente_input');
		$ventasUltimoOtorgamiento = $this->input->post('ventas_ultimo_otorgamiento_input');
		$ventasCantidadCreditos = $this->input->post('ventas_cantidad_creditos_input');
		$ventasMayorAtraso = $this->input->post('ventas_mayor_atraso_input');
		
		if ($ventasAltaCliente) {
			$grupoVentasValue = $ventasAltaCliente;
		} else if ($ventasUltimoOtorgamiento) {
			$grupoVentasValue = $ventasUltimoOtorgamiento;
		} else if ($ventasCantidadCreditos) {
			$grupoVentasValue = $ventasCantidadCreditos;
		} else if ($ventasMayorAtraso) {
			$grupoVentasValue = $ventasMayorAtraso;
		} else {
			$grupoVentasValue = '';
		}
		
		$equipoQuery = $this->input->post('equipoQuery');
		
		return $this->Supervisores_model->getCasosCampaniaCrm($tipo, $grupo, $desde, $hasta, $orden, $exclusiones, $creditosCliente, $accion, $grupoVentas, $grupoVentasValue, $equipoQuery, $returnQuery);
	}
	
	
	/**
	 * Obtiene los casos de una campania por Id de campania
	 * 
	 * @param integer $id
	 * @param false $returnQuery En TRUE devuelve el objeto query por si es necesita agregarse mas cosas
	 *
	 * @return mixed
	 */
	private function getCasosCampaniaById($idCampania = 0, $returnQuery = false)
	{
		$campania = $this->Supervisores_model->getCampaniaCrmById($idCampania)[0];
		
		$tipo = $campania['tipo'];
		$grupo = $campania['grupo'];
		$desde = $campania['desde'];
		$hasta = $campania['hasta'];
		$orden = $campania['orden'];
		$exclusiones = $campania['id_exclusion'];
		$creditosCliente = $campania['credito_cliente'];
		$accion = $campania['accion'];
		$grupoVentas = $campania['grupo_ventas'];
		$grupoVentasValue = $campania['grupo_ventas_value'];
		$equipoQuery = $campania['equipoQuery'];
		
		return $this->Supervisores_model->getCasosCampaniaCrm($tipo, $grupo, $desde, $hasta, $orden, $exclusiones, $creditosCliente, $accion, $grupoVentas, $grupoVentasValue, $equipoQuery, $returnQuery, $idCampania);
	}
	
	/**
	 * Obtiene la cantidad de casos en una fecha
	 * 
	 * @return int
	 */
	public function getCantidadCasosEnFecha_post()
	{
		$desde = $this->input->post('desde'). ' 00:00:00';
		$hasta = $this->input->post('hasta'). ' 23:59:59';
		$idCampania = $this->input->post('idCampania');
		
		$casos =$this->Supervisores_model->getCasosEnFecha($desde, $hasta, $idCampania);
		if(empty($casos)){
			$cantidad = 0;
		}else{
			$cantidad = $casos[0]['casos'];
		}
		
		$status = parent::HTTP_OK;
		$response = ['cantidad'=> $cantidad];
		
		return $this->response($response, $status);
	}
	
	/**
	 * ======================================
	 * FUNCTION busqueda_campania_crm_post 
	 * DEPRECADA EL 25/10/2021 USAR
	 * @see getCasosCampania_post
	 * en su lugar
	 * ======================================
	 **/
    
    //Simula campaña desde supervisores
    public function get_campania_crm_post(){
        if(!empty($this->input->post('exclusiones'))){
            if(count($this->input->post('exclusiones')) >= 2){
                $id_exclusiones  = json_encode($this->input->post('exclusiones'));
                $id_exclusion = str_replace('[','', $id_exclusiones);
                $id_exclusion = str_replace(']','', $id_exclusion);
                $exclusion = str_replace('"','', $id_exclusion);
            }else{
                $id_exclusiones  = json_encode($this->input->post('exclusiones'));
                $id_exclusion = str_replace('[','', $id_exclusiones);
                $id_exclusion = str_replace(']','', $id_exclusion);
                $exclusion = str_replace('"','', $id_exclusion);
            }
        }else{
            $exclusion ='';
        }
		
		$grupoVentasValue = '';
		if ($this->input->post('ventas_alta_cliente_input') != '') {
			$grupoVentasValue = $this->input->post('ventas_alta_cliente_input');
		}
		if ($this->input->post('ventas_ultimo_otorgamiento_input') != '') {
			$grupoVentasValue = $this->input->post('ventas_ultimo_otorgamiento_input');
		}
		if ($this->input->post('ventas_cantidad_creditos_input') != '') {
			$grupoVentasValue = $this->input->post('ventas_cantidad_creditos_input');
		}
		if ($this->input->post('ventas_mayor_atraso_input') != '') {
			$grupoVentasValue = $this->input->post('ventas_mayor_atraso_input');
		}
        $data = [
            'descripcion'  => $this->input->post('descripcion'),
            'tipo'  =>$this->input->post('tipo'),
            'grupo' =>$this->input->post('grupo'),
            'desde' => $this->input->post('desde'),
            'hasta' =>  $this->input->post('hasta'),
            're_gestionar'=>  $this->input->post('re_gestionar'),
            'orden' =>$this->input->post('orden'),
            'asignar'=>$this->input->post('asignar'),
            'estado' =>$this->input->post('estado'),
            'fecha_hora'=>date('Y-m-d H:i:s'),
            'id_exclusion'=>$exclusion,
            'credito_cliente'=>$this->input->post('credito_cliente'),
            'accion'=>$this->input->post('accion'),
			'autollamada' => $this->input->post('autollamada'),
			'whatsapp' => $this->input->post('whatsapp'),
			'canal_whatsapp' => $this->input->post('canal_whatsapp'),
			'sms' => $this->input->post('sms'),
			'mail' => $this->input->post('mail'),
			'operadores' => implode(',',$this->input->post('operadores')),
			'equipo' => $this->input->post('equipo'),
			'automatico' => ( $this->input->post('automatico')) ? 1 : 0,
			'minutos_gestion' => $this->input->post('minutos_gestion'),
			'minutos_extra' => $this->input->post('minutos_extra'),
			'cantidad_extensiones' => $this->input->post('cantidad_extensiones'),
			'equipoQuery' => $this->input->post('equipoQuery'),
			'grupo_ventas' => $this->input->post('grupoVentas'),
			'grupo_ventas_value' => $grupoVentasValue
        ];
        $carga = $this->Supervisores_model->guardar_campania_crm($data);
        //crea campaña manual
        if($carga > 0){
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => TRUE],'message' => "Campaña generada con exito",'id_reg' => $carga];
        }else{
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => false]];
        }
        return $this->response($response, $status);

    }
	
	
	/**
	 * Obtiene el estado de la campania
	 */
	public function getEstadoCampania_post()
	{
		$idCampania  = $this->input->post('id_campania');
		$campania = $this->Supervisores_model->getCampaniaCrmById($idCampania)[0];
		
		$response = ['data'  => $campania['estado']];
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * ======================================
	 * FUNCTION get_estado_campania_post
	 * DEPRECADA EL 06/11/2021 USAR
	 * @see getEstadoCampania_post
	 * en su lugar
	 * ======================================
	 **/
    public function get_estado_campania_post(){
        $id_reg  = $this->input->post('id_reg');
        if(!empty($id_reg)){ 
            $parametros = array('id_campania' => $id_reg);
            $consulta = $this->Supervisores_model->consulta_estado_campania($parametros);
            //consulta estado campaña
            if(count($consulta) == 0){
                $param = array('id'=> $id_reg );
                $estado_parametria = $this->Supervisores_model->listar_campanias_manuales($param);
                //consulta campaña
                if($estado_parametria[0]['estado'] == 1){
                    $estado = 0;
                }else{
                    $estado = 1;
                }
                $params = array ('estado' => $estado);
                $update_estado = $this->Supervisores_model->Update_estado($estado_parametria[0]['id'], $params);
                //actualiza campaña
                if($update_estado > 0){
                    $data = array ('id_campania' => $id_reg, 'id_operador' => $this->session->userdata("idoperador"), 'estado' => $estado, 'fecha_hora' => date('Y-m-d H:i:s'));
                    $this->Supervisores_model->track_campanias_manuales($data);
                    //Track
                    $status = parent::HTTP_OK;
                    $response = ['status'  => ['code' => $status, 'ok' => TRUE],'message' => "El estado se cambio con exito"]; 
                }else{
                    $status = parent::HTTP_OK;
                    $response = ['status'  => ['code' => $status, 'ok' => false]];
                }
            }else{
                $status = parent::HTTP_OK;
                $response = ['status'  => ['code' => $status, 'ok' => TRUE, 'alert' => TRUE]];
            }
        }else{
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => false]];
        }
        return $this->response($response, $status);
    }
	
	/**
	 * Activa una campania manual y trackea el cambio de estado
	 */
	public function activarCampaniaManual_post()
	{
		$idCampania = $this->input->post('id_reg');
		$this->Supervisores_model->updateEstadoCampania($idCampania, 1);
		
		//trackeo cambio de estado de campania
		$data = array (
			'id_campania' => $idCampania,
			'id_operador' => $this->session->userdata("idoperador"),
			'estado' => 1,
			'fecha_hora' => date('Y-m-d H:i:s')
		);
		$this->Supervisores_model->track_campanias_manuales($data);
		
		$status = parent::HTTP_OK;
		$response = ['status'  => ['code' => $status, 'ok' => true]];
		return $this->response($response, $status);
	}
	
	/**
	 * Desactiva la campania manual, quita todos los operadores de la misma y traquea su remocion
	 * como asi tambien se trackea el cambio de estado de la campania
	 */
	public function desactivarCampania_post()
	{
		$idCampania = $this->input->post('id_reg');
		$oepradoresEnCampania = $this->Supervisores_model->getOperadoresAsignadosACampaniaByEstado($idCampania);
		foreach ($oepradoresEnCampania as $operadores) {
			$idOperador = $operadores['id_operador'];
			$this->Supervisores_model->trackCambioEstadoOperadorCampaniaADesactivado($idOperador, $idCampania);
			$this->Supervisores_model->removeCasosAlOperador($idOperador);
		}
		$this->Supervisores_model->desasignarCampaniaAOperadores($idCampania);
		$this->Supervisores_model->updateEstadoCampania($idCampania, 0);
		
		//trackeo cambio de estado de campania
		$data = array (
			'id_campania' => $idCampania,
			'id_operador' => $this->session->userdata("idoperador"),
			'estado' => 0,
			'fecha_hora' => date('Y-m-d H:i:s')
		);
		$this->Supervisores_model->track_campanias_manuales($data);
		
		$status = parent::HTTP_OK;
		$response = ['status'  => ['code' => $status, 'ok' => true]];
		return $this->response($response, $status);
	}
	
	/**
	 * ======================================
	 * FUNCTION get_confirmar_estado_post
	 * DEPRECADA EL 06/11/2021 USAR
	 * @see desactivarCampania_post
	 * @see activarCampaniaManual_post
	 * en su lugar
	 * ======================================
	 **/
    //cambia estado campaña crm
    public function get_confirmar_estado_post(){
        $id_reg  = $this->input->post('id_reg');
        $parametros = array('id_campania' => $id_reg);
        $consulta = $this->Supervisores_model->consulta_estado_campania($parametros);
        //consulta estado operador campaña manual
        if(count($consulta) > 0){
            for($i=0;$i<count($consulta);$i++){
                $data = array (
                    'id_campania' => $consulta[$i]['id_campania'],
                    'id_operador' => $consulta[$i]['id_operador'],
                    'estado' => 'desactivado',
                    'id_operador_afecta' => $this->session->userdata("idoperador"),
                    'fecha' => date('Y-m-d'),
					'hora_ini' => date('H:i:s')
                );
                $this->Supervisores_model->track_operadores_campanias_manuales($data);
                //Track
                $data = array(
                    'id_operador' => $consulta[$i]['id_operador'],
                );
                $result_delete = $this->Supervisores_model->relacion_casos_operador_manual($data);
                //Elimina caso del operador
            }
            $parametros = array('id_campania' => $id_reg);
            $limiar_tabla = $this->Supervisores_model->limiar_relacion_campania($parametros);
            //Elimina operadores desde id_campania
            if($limiar_tabla > 0){
                $param = array('id'=> $id_reg );
                $estado_parametria = $this->Supervisores_model->listar_campanias_manuales($param);
                //consulta estado operador campaña manual
                if($estado_parametria[0]['estado'] == 1){
                    $estado = 0;

                }else{
                    $estado = 1;
                }
                $params = array ('estado' => $estado);
                $update_estado = $this->Supervisores_model->Update_estado($estado_parametria[0]['id'], $params);
                //actualiza campaña
                if($update_estado > 0){
                    $data = array (
                        'id_campania' => $id_reg,
                        'id_operador' => $this->session->userdata("idoperador"),
                        'estado' => $estado,
                        'fecha_hora' => date('Y-m-d H:i:s')
                    );
                    $this->Supervisores_model->track_campanias_manuales($data);
                    //Track
                }
                $status = parent::HTTP_OK;
                $response = ['status'  => ['code' => $status, 'ok' => true]];
            }
        }else{
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => false]];
        }
        return $this->response($response, $status);
    }
    //cambia estado campaña inactivo
    public function campania_campos_post(){
        $param = array('id'=> $this->input->post('id_reg'));
        $campania = $this->Supervisores_model->listar_campanias_manuales($param);
        //Consulta campaña
        if($campania > 0){
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => true], 'result'=> $campania];
        }else{
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => false]];
        }
        return $this->response($response, $status);
    }
	
	
	/**
	 * Obtiene datos adicionales como los templates y las expclusiones
	 */
	public function getCampaniaExtraInfo_post()
	{
		$this->load->model('Chat', 'chat', TRUE);
		$this->load->model('solicitud_m', 'solicitud_model', TRUE);
		
		$rtrn = [
			'templates' => [
				'whatsapp' => '',
				'sms' => '',
				'email' => '',
			],
			'exclusiones' => [],
			'operadores' => []
		];
		
		$templateWhatsappId = $this->input->post('template_whatsapp');
		$templateSmsId = $this->input->post('template_sms');
		$templateEmailId = $this->input->post('template_email');
		$operadoresId = $this->input->post('operadores');
		
		if (!empty($templateWhatsappId)) {
			$auxWhatsapp = $this->chat->getWhatsappTemplate($templateWhatsappId);
			$rtrn['templates']['whatsapp'] = $auxWhatsapp[0]['msg_string'];
		}
		
		if (!empty($templateSmsId)) {
			$auxSms = $this->chat->getSmsTemplate($templateSmsId);
			$rtrn['templates']['sms'] = $auxSms[0]['msg_string'];
		}
		
		if (!empty($templateEmailId)) {
			$auxEmail = $this->chat->getEmailTempalte($templateEmailId);
			$rtrn['templates']['email'] = $auxEmail[0]['html_contenido'];
		}
		
		$exclusionesId = $this->input->post('exclusiones');
		
		if (!empty($exclusionesId)) {
			$arrayEx = explode(',', $exclusionesId);
			
			foreach ( $arrayEx as $exclusionId) {
				$excluido  = $this->solicitud_model->getBotonOperadorById($exclusionId);
				$rtrn['exclusiones'][] = $excluido[0]->descripcion;
			}
		}
		
		if (!empty($operadoresId)) {
			$arrayOp = explode(',', $operadoresId);
			
			foreach ($arrayOp as $item) {
				$operador = $this->operadores_model->getTipoOperadorById($item);
				$rtrn['operadores'][] = $operador[0]['descripcion'];
			}
		}
		
		$status = parent::HTTP_OK;
		$response = ['data' => $rtrn];
		return $this->response($response, $status);
	}
    //consulta campañas crm
    public function update_campania_crm_post(){
        $id = $this->input->post('id');
        $exclusion = '';
        if(!empty($this->input->post('exclusiones'))){
            $id_exclusiones = json_encode($this->input->post('exclusiones'));
            $id_exclusion = str_replace('[','', $id_exclusiones);
            $id_exclusion = str_replace(']','', $id_exclusion);
            $exclusion = str_replace('"','', $id_exclusion);
        }
	
		$grupoVentasValue = '';
		if ($this->input->post('ventas_alta_cliente_input') != '') {
			$grupoVentasValue = $this->input->post('ventas_alta_cliente_input');
		}
		if ($this->input->post('ventas_ultimo_otorgamiento_input') != '') {
			$grupoVentasValue = $this->input->post('ventas_ultimo_otorgamiento_input');
		}
		if ($this->input->post('ventas_cantidad_creditos_input') != '') {
			$grupoVentasValue = $this->input->post('ventas_cantidad_creditos_input');
		}
		if ($this->input->post('ventas_mayor_atraso_input') != '') {
			$grupoVentasValue = $this->input->post('ventas_mayor_atraso_input');
		}
        $params = [
            'descripcion'  => $this->input->post('descripcion'),
            'tipo'  =>$this->input->post('tipo'),
            'grupo' =>$this->input->post('grupo'),
            'desde' => $this->input->post('desde'),
            'hasta' =>  $this->input->post('hasta'),
            're_gestionar'=>  $this->input->post('re_gestionar'),
            'orden' =>$this->input->post('orden'),
            'asignar'=>$this->input->post('asignar'),
            'id_exclusion'=> $exclusion,
            'credito_cliente'=>$this->input->post('credito_cliente'),
            'accion'=>$this->input->post('accion'),
			'autollamada' => $this->input->post('autollamada'),
			'whatsapp' => $this->input->post('whatsapp'),
			'canal_whatsapp' => $this->input->post('canal_whatsapp'),
			'sms' => $this->input->post('sms'),
			'mail' => $this->input->post('mail'),
			'operadores' => implode(',',$this->input->post('operadores')),
			'equipo' => $this->input->post('equipo'),
			'automatico' => ( $this->input->post('automatico') == 'true') ? 1 : 0,
			'minutos_gestion' => $this->input->post('minutos_gestion'),
			'minutos_extra' => $this->input->post('minutos_extra'),
			'cantidad_extensiones' => $this->input->post('cantidad_extensiones'),
			'equipoQuery' => $this->input->post('equipoQuery'),
			'grupo_ventas' => $this->input->post('grupoVentas'),
			'grupo_ventas_value' => $grupoVentasValue
        ];
        $param = array('id'=> $id);
        $estado_parametria = $this->Supervisores_model->listar_campanias_manuales($param);
        //Consulta campaña
        $update_estado = $this->Supervisores_model->Update_estado($id, $params);
        //actualiza campaña
        if($update_estado > 0){
            unset($estado_parametria[0]['id']);
            unset($estado_parametria[0]['fecha_hora']);
            $anterior = array_diff($estado_parametria[0], $params);
            $nuevo = array_diff($params, $estado_parametria[0]);
            $date = array( 
                'id_campania'=>$id,
                'id_operador'=>$this->session->userdata("idoperador"),
                'detalle'=> 'valores anteriores : '.json_encode($anterior).' nuevos valores: '.json_encode($nuevo),
                'fecha_hora'=> date('Y-m-d H:i:s')
            );
            $track = $this->Supervisores_model->track_actualizacion($date);
            //track cambio en campaña
            if($track > 0){
                $status = parent::HTTP_OK;
                $response = ['status'  => ['code' => $status, 'ok' => true]];
            }else{
                $status = parent::HTTP_OK;
                $response = ['status'  => ['code' => $status, 'ok' => false]];
            }
        
        }else{
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => false]];
        }
        return $this->response($response, $status);
    }
    //actualiza campañas crm
	
	/**
	 * Activa una campania para un operador y cambia su estado a activo.
	 */
	public function activarCampania_post()
	{
		$idCampania = $this->input->post('id_campania');
		$idOperador = $this->input->post('id_operador');
		$resultAsignacion = $this->Supervisores_model->asignarOperadorACampaniaManual($idOperador, $idCampania);
		
		if ($resultAsignacion) {
			$this->Supervisores_model->trackCambioEstadoOperadorCampania($idOperador, $idCampania, 'activo');
		}
		
		$result = $this->asignarCasosOperadorCampania($idOperador);
		$response = ['status'  => ['code' => parent::HTTP_OK, 'ok' => $result]];
		
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * ======================================
	 * FUNCTION activar_campania_post 
	 * DEPRECADA EL 04/11/2021 USAR
	 * @see activarCampania_post
	 * en su lugar
	 * ======================================
	 */
	
	/**
	 * ======================================
	 * FUNCTION salir_campania_post 
	 * DEPRECADA EL 05/11/2021 USAR
	 * @see salirCampania_post
	 * en su lugar
	 * ======================================
	 **/
	
	/**
	 * ======================================
	 * FUNCTION estado_relacion_operador_post
	 * DEPRECADA EL 05/11/2021 USAR
	 * @see cambiarOperadorADescanso_post
	 * en su lugar
	 * ======================================
	 **/
	
	/**
	 * ======================================
	 * FUNCTION reactivar_campania_post
	 * DEPRECADA EL 05/11/2021 USAR
	 * @see reactivarOperador_post
	 * en su lugar
	 * ======================================
	 **/
	
	/**
	 * Activa mas casos al Operador por POST
	 *
	 */
	public function activar_casos_post()
	{
		$idOperador = $this->input->post('id_operador');
		
		$result = $this->asignarCasosOperadorCampania($idOperador);
		$this->desactivarPusherCampaniasManuales($idOperador);
		$response = ['status'  => ['code' => parent::HTTP_OK, 'ok' => $result]];
		return $this->response($response, parent::HTTP_OK);
	}
	
	//AZS: revisar si es desactivacion o que operacion realiza
	public function desactivarPusherCampaniasManuales($idOperador)
	{
		$dotEnv = Dotenv\Dotenv::create(FCPATH);
		$dotEnv->load();
		$pusher = new Pusher\Pusher(
			getenv('PUSHER_KEY'),
			getenv('PUSHER_SECRET'),
			getenv('PUSHER_APP_ID'),
			['cluster' => getenv('PUSHER_CLUSTER')]
		);
		$res = $pusher->trigger(
			'channel-operador-' . $idOperador,
			'activacion-campania-component',
			[
				'status' => 'ok'
			]
		);
	}
	
	/**
	 * Obtiene los casos asignados al operador detallados 
	 */
	public function getCasosAsignadosDetallados_post()
	{
		//AZS: en la funcion original elimina los casos que no son del dia. Revisar si es necesario o porque lo hace.
		
		$idOperador = $this->session->userdata("idoperador");
		$result = $this->Supervisores_model->getCasosAsignadosDetallados($idOperador);
		
		if(!empty($result)){
			$status = parent::HTTP_OK;
			$response = ['status'  => ['code' => $status, 'ok' => true], 'data' => $result];
		}else{
			$status = parent::HTTP_OK;
			$response = ['status'  => ['code' => $status, 'ok' => false]];
		}
		return $this->response($response, $status);
	}
	
	/**
	 * ======================================
	 * FUNCTION refrescar_tabla_post 
	 * DEPRECADA EL 05/11/2021 USAR
	 * @see getCasosAsignadosDetallados_post
	 * en su lugar
	 * ======================================
	 **/
    
	/**
	 * Guarda la gestion del operador
	 */
	public function guardarGestionOperador_post()
	{
		$params = array(
			'id_credito' => $this->input->post('id_credito'),
			'id_operador' => $this->input->post('id_operador')
		);
		$idCampania = $this->input->post('id_campania');
		$casos = $this->Supervisores_model->getCasosAsignados($this->input->post('id_operador'));

		if(!empty($casos)){
			$id_detalle_respuestas = ($this->input->post('idDetalleRespuesta') !== null) ? $this->input->post('idDetalleRespuesta') : 0;
			$result = $this->registrarCasoComoGestionado($idCampania, $casos[0]['fecha_hora'], $id_detalle_respuestas);
		}
	}
	
	/**
	 * Gestiona el cierre del caso, removiendo el caso del operador, comprobando si aun tiene casos pendientes
	 * y si no es asi le asigna mas
	 *
	 * @param $idOperador
	 * @param $idCredito
	 *
	 */
	public function gestionarCierreCaso($idOperador, $idCredito)
	{
		$this->Supervisores_model->removeCasosAlOperador($idOperador, $idCredito);
		
		$casosAsignados = $this->Supervisores_model->getCasosAsignados($idOperador);
		
		if (count($casosAsignados) == 0) {
			$this->asignarCasosOperadorCampania($idOperador);
		}
	}
	
	/**
	 * Registra el caso como gestionado
	 * 
	 * @param $idCampania
	 * @param $fechaAsignacion
	 * @param int $idDetalleRespuestas
	 *
	 * @return mixed
	 */
	private function registrarCasoComoGestionado($idCampania, $fechaAsignacion, $idDetalleRespuestas)
	{
		$parametros = array(
			'id_campania' => $idCampania,
			'id_credito' => $this->input->post('id_credito'),
			'id_operador' => $this->input->post('id_operador'),
			'id_gestion' => $this->input->post('id_gestion'),
			'fecha_hora' => date('Y-m-d H:i:s'),
			'fecha_asignacion' => $fechaAsignacion,
			'id_detalle_respuestas' => $idDetalleRespuestas
		);
		
		$result = $this->Supervisores_model->registrarCasoComoGestionado($parametros);
		
		return $result;
	}
	
    //Consulta y crea track de casos gestionados
    public function operadores_activos_post(){
        $param = array('id_campania'=> $this->input->post('id_campania'));
        $data = $this->Supervisores_model->operadores_activos($param);
        //operadores por campaña
        for($j=0; $j<count($data); $j++){
            $operadores_activos[] = array( 'id_operador'=> $data[$j]['id_operador'],'nombre_apellido'=> $data[$j]['nombre_apellido'], 'estado'=> $data[$j]['estado'],'id_campania'=> $data[$j]['id_campania']);
        }
        if(!empty($operadores_activos)){
            $resultado = $operadores_activos;
        }else{
            $resultado = '';
        }
        echo json_encode(['data'=> $resultado]);
    }
    //Op activos
    public function operadores_post(){
        $param = array();
        $data = $this->Supervisores_model->operadores_activos($param);
        for($j=0; $j<count($data); $j++){
            $operadores_activos[] = array( 'id_operador'=> $data[$j]['id_operador'],'nombre_apellido'=> $data[$j]['nombre_apellido'], 'estado'=> $data[$j]['estado'],'descripcion'=>'('.$data[$j]['descripcion'].')');
        }
        $inactivos = $this->Supervisores_model->operadores_inactivos();
        for($i=0; $i<count($inactivos); $i++){
            $operadores_inactivos[] = array( 'id_operador'=> $inactivos[$i]['idoperador'],'nombre_apellido'=>$inactivos[$i]['nombre_apellido'], 'estado'=> 'Inactivo','descripcion'=>'');  
        }
        if(!empty($operadores_activos)){
            $resultado = array_merge($operadores_activos, $operadores_inactivos);
        }else{
            $resultado = $operadores_inactivos;
        }
        if(!empty($resultado))
        {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'data' =>$resultado];  
        } else {
            $status = parent::HTTP_OK;
            $response = ['status' => $status, 'message' => 'No fue posible cargar la lista de operadores'];
        }    
        $this->response($response);
    
    }
    //Operadores asignados a campaña manual
    public function casos_gestion_post(){
        $params = array('id'=> $this->input->post('id'),'estado'=> 1);
        $campania = $this->Supervisores_model->listar_campanias_manuales($params);
        //Consulto campaña desde id_campania
        if(!empty($this->input->post('fecha'))){
            $desde = $this->input->post('fecha'). ' 00:00:00';
            $hasta = $this->input->post('fecha'). ' 23:59:59';
        }else{
            $desde = date('Y-m-d'). ' 00:00:00';
            $hasta = date('Y-m-d'). ' 23:59:59';
        }
        $fecha = "BETWEEN '$desde' AND '$hasta'";
        $parametros = array('id_campania'=> $this->input->post('id'),'estado'=> 1, 'fecha'=> $fecha);
        $data = $this->Supervisores_model->casos_gestionados($parametros);
        $data = count($data);        
        $consulta_gestion_hoy = array(
            'id_campania' => $this->input->post('id'),
            'fecha' => $fecha,
        );
        $gestionados_hoy = count($this->Supervisores_model->casos_gestionados($consulta_gestion_hoy));
        $gestionando = $this->Supervisores_model->casos_gestion_momento($parametros);
        $gestionando = count($gestionando);
        if(empty($gestionando)){
            $gestionando = 0;
        }
        if(empty($data)){
           $data = 0;
        }
        $casos = $this->Supervisores_model->operadores_gestion($parametros);
        if(!empty($casos)){
            $mayor_gestion = $casos[0];
            $menor_gestionado =end($casos);
        }else{
            $mayor_gestion = '';
            $menor_gestionado = ''; 
        }
        
        
        $status = parent::HTTP_OK;
        $response = ['status'  => ['code' => $status, 'ok' => true],
            'data' => $data,
            'gestionando'=> $gestionando,
            'nombre_campania'=>$campania[0]['descripcion'],
            'gestionados_hoy'=> $gestionados_hoy,
            'mayor_gestion'=> $mayor_gestion,
            'menor_gestion'=> $menor_gestionado,
        ];
        return $this->response($response, $status);
    }
    //casos gestionados campaña
    public function campania_activa_operador_post(){
        $param = array('id_operador' => $this->input->post('id'));
        $data = $this->Supervisores_model->consulta_estado_campania($param);
        //Consulta operador si esta en otra campaña
        if(!empty($data)){
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => true], 'data' => $data];
        }else{
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => false]];
        }
        return $this->response($response, $status);
    }
	
	/**
	 * ======================================
	 * FUNCTION consultar_campania_post 
	 * DEPRECADA EL 05/11/2021
	 * ======================================
	 **/
	
    //Actualiza campaña y tabla en gestion
    public function consultar_asigando_operador_post(){
        $params = array(
            'id_operador'=> $this->input->post('id_operador'),
            'id_credito'=> $this->input->post('id_credito'),
        );
        $consult = $this->Supervisores_model->getCasosAsignados($this->input->post('id_operador'));
        if(!empty($consult)){
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => true], 'data'=> $consult[0]['id_credito']];
        }else{
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => false], 'data'=> ''];
        }
        return $this->response($response, $status);

    }
    //consulta si el caso esta asignado al operador
    public function tiempo_promedio_post(){
        $id_operador = '';
        if(!empty($this->input->post('id_operador'))){
            $id_operador =$this->input->post('id_operador');
        }
        if(!empty($this->input->post('fecha'))){
            $desde = $this->input->post('fecha'). ' 00:00:00';
            $hasta = $this->input->post('fecha'). ' 23:59:59';
        }else{
            $desde = date('Y-m-d'). ' 00:00:00';
            $hasta = date('Y-m-d'). ' 23:59:59';
        }
        $fecha = "BETWEEN '$desde' AND '$hasta'";
        $tiempos = $this->Supervisores_model->getTiemposOperador($this->input->post('id_campania'), $id_operador, $fecha);
        if(!empty($tiempos)){
            $tiempo_promedio = $this->tiempos($tiempos,false);
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => true], 'data' => $tiempo_promedio];
        }else{
            $status = parent::HTTP_OK;
            $response = ['status'  => ['code' => $status, 'ok' => false]];
        }
        return $this->response($response, $status);
    }//Tiempo promedio operadores gestion
    public function tipo_por_operador_post(){
        //Obtenemos casos totales por hora;
        if(!empty($this->input->post('fecha_consulta'))){
            $fecha_dia = $this->input->post('fecha_consulta');
            $desde = $fecha_dia. ' 00:00:00';
            $hasta = $fecha_dia. ' 23:59:59';
        
        }else{
            $desde = date('Y-m-d'). ' 00:00:00';
            $hasta = date('Y-m-d'). ' 23:59:59';
            $fecha_dia = date('Y-m-d');
        }
        $fecha = "BETWEEN '$desde' AND '$hasta'";
        $parametros = array('fecha'=>$fecha,'id_campania'=> $this->input->post('id_campania'));
        $obtener_operadores = $this->Supervisores_model->operadores_gestion($parametros);
        foreach($obtener_operadores as $values){
             $operadores[] = $values['id_operador'];
        }
        $k = 7;
        if(!empty($operadores)){
            for($j = 0; $j<count($operadores); $j++){  
                for($i=7;$i<21; $i++){
                    if($k > 9){
                        $hora_inicio = $i.':00:00';
                        $hora_fin = $i.':59:59';
                    }else{
                        $hora_inicio = '0'.$i.':00:00';
                        $hora_fin = '0'.$i.':59:59';
                    }
                    $desde = $fecha_dia.' '.$hora_inicio;
                    $hasta = $fecha_dia.' '.$hora_fin;
                    $fecha = "BETWEEN '$desde' AND '$hasta'";
                    $param = array('fecha'=>$fecha,'id_campania'=> $this->input->post('id_campania'));
                    $resultado[] = $this->Supervisores_model->cantidad_gestiones_hora($param);
                    $params = array('fecha'=>$fecha,'id_campania'=> $this->input->post('id_campania'), 'id_operador'=> $operadores[$j]);
                    $casos = $this->Supervisores_model->operadores_gestion($params);
                    if(!empty($casos)){
                        $gestion_operador_hora = $casos[0];
                    }else{
                        $gestion_operador_hora = 0;
                    }
                    $totales[] = $gestion_operador_hora;
                    $k += 1;
                }

				$nombre_operador = '';
                for ($i = 0; $i<count($totales); $i++) {
                    if(!empty($totales[$i])){
                        $cantidad_op[] = $totales[$i]['total'];
                        $nombre_operador = $totales[$i]['nombre_apellido'];
                    }else{
                        $cantidad_op[] = 0;
                    }
                }
            
                $hora[] = array('label'=>$nombre_operador,
                'data'=> $cantidad_op,
                'backgroundColor'=>'rgba(255, 99, 132, 0)',
                'borderColor'=>'rgba('.rand(0,255).','.rand(0,255).','.rand(0,255).', 1)','borderWidth'=> 1,
                );
                $totales =[];
                $cantidad_op =[];
            }
        
            for ($i = 0; $i<count($resultado); $i++) {
                if(!empty($resultado[$i])){ $cantidad_hora[] = $resultado[$i][0]['cantidad'];
                }else{ $cantidad_hora[] = 0; }
            }
//            $hora[] = array('label'=>'Solicitudes por hora',
//            'data'=> $cantidad_hora,
//            'backgroundColor'=>'rgba(255, 99, 132, 0)',
//            'borderColor'=>'rgba(54, 162, 235, 1)','borderWidth'=> 1,
//            );
        
        }else{
            $hora[] = array('label'=>'Solicitudes por hora',
            'data'=> [0,0,0,0,0,0,0,0,0,0,0,0,0,0],
            'backgroundColor'=>'rgba(255, 99, 132, 0)',
            'borderColor'=>'rgba(54, 162, 235, 1)','borderWidth'=> 1,
            );
        }
        $valor = json_encode($hora);  

        if(!empty($valor)){
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => true],'data' => $valor];
        }else{
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => false]];
        }
        return $this->response($response, $status);
    }//Grafico gestiones hora por hora

    public function tipificacion_operadores_post(){
        if(!empty($this->input->post('fecha_consulta'))){
            $fecha_dia = $this->input->post('fecha_consulta');
            $desde = $fecha_dia. ' 00:00:00';
            $hasta = $fecha_dia. ' 23:59:59';
        
        }else{
            $desde = date('Y-m-d'). ' 00:00:00';
            $hasta = date('Y-m-d'). ' 23:59:59';
            $fecha_dia = date('Y-m-d');
        }
        $fecha = "BETWEEN '$desde' AND '$hasta'";
        $parametros = array('fecha'=>$fecha,'id_campania'=> $this->input->post('id_campania'));
        $tipificaciones = $this->Supervisores_model->tipificacion_gestionada($parametros);
        $k = 7;
        if(!empty($tipificaciones)){
            $id_tipificaciones = array();
            foreach($tipificaciones as $value){
                if(!in_array($value["id"],$id_tipificaciones)){
                    $id_tipificaciones[] = $value["id"];
                }
            }
            for($i = 0; $i<count(array_unique($id_tipificaciones)); $i++){
                for($j=7;$j<21; $j++){
                    if($k > 9){
                        $hora_inicio = $j.':00:00';
                        $hora_fin = $j.':59:59';
                    }else{
                        $hora_inicio = '0'.$j.':00:00';
                        $hora_fin = '0'.$j.':59:59';
                    }
                    $desde = $fecha_dia.' '.$hora_inicio;
                    $hasta = $fecha_dia.' '.$hora_fin;
                    $fecha = "BETWEEN '$desde' AND '$hasta'";
                    
                    $params = array('fecha'=>$fecha,'id_campania'=> $this->input->post('id_campania'), 'id_gestion'=> $id_tipificaciones[$i]);
                    $casos = $this->Supervisores_model->tipificacion_operadores($params);
    
                    if(!empty($casos)){
                        $gestion_operador_hora = $casos[0];
                    }else{
                        $gestion_operador_hora = 0;
                    }
                    $totales[] = $gestion_operador_hora;
                    $k += 1;
                }
				$etiqueta = '';
                for ($h = 0; $h<count($totales); $h++) {
                    if(($totales[$h]['total'] != 0)){
                        $cantidad_tipificacion[] = $totales[$h]['total'];
                        $etiqueta = $totales[$h]['tipo'];
    
                    }else{
                        $cantidad_tipificacion[] = 0;
                    }
                }
                $tipificacion_hora[] = array('label'=>$etiqueta,
                'data'=> $cantidad_tipificacion,
                'backgroundColor'=>'rgba(255, 99, 132, 0)',
                'borderColor'=>'rgba('.rand(0,255).','.rand(0,255).','.rand(0,255).', 1)','borderWidth'=> 1,
                );
                $totales =[];
                $cantidad_tipificacion =[];
            }
        }else{
            $tipificacion_hora[] = array('label'=>'Solicitudes por hora',
            'data'=> [0,0,0,0,0,0,0,0,0,0,0,0,0,0],
            'backgroundColor'=>'rgba(255, 99, 132, 0)',
            'borderColor'=>'rgba(54, 162, 235, 1)','borderWidth'=> 1,
            ); 
        }
    
        $valor = json_encode($tipificacion_hora);  
        if(!empty($valor)){
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => true],'data' => $valor];
        }else{
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => false]];
        }
        return $this->response($response, $status);
    }    //Grafico gestiones Tipificaciones por hora

    public function tiempos($tiempos,$tipo){
        if(empty($tipo)){
            foreach ($tiempos as $key=> $value_tiempos){
                $total_segundos[] = $value_tiempos['segundos'];
            }
            $tiempo_segundos = round(array_sum($total_segundos) / count($total_segundos));
            $adjunto ='';
        }else{
            $tiempo_segundos = $tiempos['segundos'];
            $adjunto = '<br> Documento: '.$tiempos['documento'];
        }
		
		$tiempo_promedio = gmdate('H:i:s', $tiempo_segundos);

        return $tiempo_promedio;
    }
    public function calcular_tiempos($estado,$hora_compara){
        $n=0;
        $total = 0;
        $tiempos = array();
        if($estado[0]['hora_fin'] == '00:00:00'){
            $datetime1 = date_create($estado[0]['hora_ini']);
            $datetime2 = date_create($hora_compara);
            $interval = date_diff($datetime1, $datetime2);
            $tiempo = $interval->h.':'.$interval->i.':'.$interval->s;
            $n = 1;
        }
        for($j = $n; $j<count($estado);$j++){
            $tiempos[] = $estado[$j]['total'];
        }
        if(!empty($tiempo)){
            $tiempos[] = $tiempo;
        }
        foreach($tiempos as $h) {
            $parts = explode(":", $h);
            $total += $parts[2] + $parts[1]*60 + $parts[0]*3600;        
        }   
        return $tiempo_gestion = gmdate("H:i:s", $total);
    }
    public function tabla_gestiones_operadores_post(){
        if(!empty($this->input->post('fecha'))){
            $fecha_dia = $this->input->post('fecha');
            $desde = $fecha_dia. ' 00:00:00';
            $hasta = $fecha_dia. ' 23:59:59';
            if($fecha_dia != date('Y-m-d')){
                $hora_compara = '20:00:00';
            }else{
                $hora_compara = date('H:i:s');
            }
        }else{
            $desde = date('Y-m-d'). ' 00:00:00';
            $hasta = date('Y-m-d'). ' 23:59:59';
            $fecha_dia = date('Y-m-d');
            $hora_compara = date('H:i:s');
        }
        $fecha = "BETWEEN '$desde' AND '$hasta'";
        $parametros = array('fecha'=>$fecha,'id_campania'=> $this->input->post('id_campania'));
        $obtener_operadores = $this->Supervisores_model->operadores_gestion($parametros);
        foreach($obtener_operadores as $values){
             $operadores[] = $values['id_operador'];
        }
        if(!empty($operadores)){
            for($i=0;$i<count($operadores);$i++){
                $tiempo_descanso_gestion = '--';
                $tiempo_total_gestion = '--';
                $tiempo_inactivo_gestion = '--'; 
                $datos = array('id_campania'=> $this->input->post('id_campania'),'fecha'=> $fecha, 'id_operador'=> $operadores[$i]);
                $tiempos = $this->Supervisores_model->getTiemposOperador( $this->input->post('id_campania'), $operadores[$i], $fecha);
				
                if(!empty($tiempos)){
                    $tiempo_promedio = $this->tiempos($tiempos,false);
                    $tiempo_menor = $tiempos[0];
                    $tiempo_promedio_menor = $this->tiempos($tiempo_menor,true);
                    $tiempo_mayor = end($tiempos);
                    $tiempo_promedio_mayor = $this->tiempos($tiempo_mayor,true);  
                }else{
                    $tiempo_promedio = '--';
                    $tiempo_promedio_menor = '--';
                    $tiempo_promedio_mayor = '--';
                }
                $datos_array = array('id_campania'=> $this->input->post('id_campania'),'fecha'=> $fecha_dia, 'id_operador'=> $operadores[$i]);
                $datos_array['estado_campania'] = 'activo';
                $estado_activo = $this->Supervisores_model->consulta_operadores_campanias($datos_array);
                if(!empty($estado_activo)){
                    $tiempo_total_gestion = $this->calcular_tiempos($estado_activo,$hora_compara);
                }
                $datos_array['estado_campania'] = 'descanso';
                $estado_descanso = $this->Supervisores_model->consulta_operadores_campanias($datos_array);
                if(!empty($estado_descanso)){
                    $tiempo_descanso_gestion = $this->calcular_tiempos($estado_descanso,$hora_compara);
                }

                $datos_array['estado_campania'] = 'inactivo';
                $estado_inactivo = $this->Supervisores_model->consulta_operadores_campanias($datos_array);
                if(!empty($estado_inactivo)){
                    $tiempo_inactivo_gestion = $this->calcular_tiempos($estado_inactivo,$hora_compara);
                }
                
                $gestiones_ahora = count($this->Supervisores_model->getCasosAsignados( $operadores[$i]));
                $gestiones = $this->Supervisores_model->operadores_gestion($datos);
                $tabla_operador[] = array(
                    'tiempo_descanso_gestion'=>$tiempo_descanso_gestion,
                    'tiempo_total_gestion'=>$tiempo_total_gestion,
                    'tiempo_inactivo'=>$tiempo_inactivo_gestion,
                    'mayor_tiempo'=>$tiempo_promedio_mayor,
                    'menor_tiempo'=>$tiempo_promedio_menor,
                    'asignados'=>$gestiones_ahora,
                    'tiempo_promedio'=>$tiempo_promedio,
                    'id_operador'=>$operadores[$i],
                    'nombre_apellido'=>$gestiones[0]['nombre_apellido'],
                    'gestiones'=> $gestiones[0]['total']
                );
                $tiempo_descanso_gestion='';
                $tiempo_total_gestion='';
                $tiempo_inactivo_gestion = '';
            }
        }
        if(!empty($tabla_operador)){
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => true],'data' => $tabla_operador];
        }else{
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => false], 'data' => ''];
        }
        return $this->response($response, $status);
    }
    public function tabla_tipificaciones_total_post(){
        if(!empty($this->input->post('fecha'))){
            $desde = $this->input->post('fecha'). ' 00:00:00';
            $hasta = $this->input->post('fecha'). ' 23:59:59';
        }else{
            $desde = date('Y-m-d'). ' 00:00:00';
            $hasta = date('Y-m-d'). ' 23:59:59';
        }
        $fecha = "BETWEEN '$desde' AND '$hasta'";

        $params = array('fecha'=>$fecha,'id_campania'=> $this->input->post('id_campania'));
        $casos = $this->Supervisores_model->tipificacion_operadores($params);
        $result_denominacion = $this->Supervisores_model->denominacion_operadores($params);
        $data = array_merge($casos, $result_denominacion);
        if(!empty($casos)){
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => true],'data' => $data];
        }else{
            $status = parent::HTTP_OK;
            $response = ['status' => ['code' => $status, 'ok' => false], 'data'=> ''];
        }
        return $this->response($response, $status);
    }
	
	/**
	 * Obtiene el total de las tipificaciones de una campania
	 */
	public function tablaTotalTipificaciones_post()
	{
		$idCampania = $this->input->post('id_campania');
		$fecha = $this->input->post('fecha');
		
		if(!empty($fecha)){
			$desde = $fecha. ' 00:00:00';
			$hasta = $fecha. ' 23:59:59';
		}else{
			$desde = date('Y-m-d'). ' 00:00:00';
			$hasta = date('Y-m-d'). ' 23:59:59';
		}
		$tipificaciones = $this->Supervisores_model->getContadorTipificaciones($idCampania, $desde, $hasta);
		
		if(!empty($tipificaciones)){
			$status = parent::HTTP_OK;
			$response = ['status' => ['code' => $status, 'ok' => true],'data' => $tipificaciones];
		}else{
			$status = parent::HTTP_OK;
			$response = ['status' => ['code' => $status, 'ok' => false], 'data'=> ''];
		}
		return $this->response($response, $status);
	}
	
	
	/**
	 * Obtiene los operadores de una campania
	 */
	public function getOperadoresPorCampania_post()
	{
		$campaniaId = $this->input->post('id');
		
		$param = ['id'=> $campaniaId];
		$campania = $this->Supervisores_model->listar_campanias_manuales($param);
		
		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => true],'data' => []];
		if (isset($campania[0])) {
			$equipo = $campania[0]['equipo'];
			if  ($equipo == 'TODOS') {
				$response = $this->getTodosOperadoresPorCampania($campaniaId);
			} else {
				$response = $this->getOperadoresPorCampaniaYEquipos($campaniaId, $equipo);
			}
		}
		
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Obtiene todos los operadores de una campania
	 * 
	 * @param $campaniaId
	 *
	 * @return array
	 */
	private function getTodosOperadoresPorCampania($campaniaId)
	{
		$data = $this->operadores_model->getOperadoresPorCampania($campaniaId);
		
		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => true],'data' => $data];
		return $response;
	}
	
	/**
	 * Obtiene los operadorores de una campania por el equipo definido en la misma
	 * 
	 * @param $campaniaId
	 * @param $equipo
	 *
	 * @return array
	 */
	private function getOperadoresPorCampaniaYEquipos($campaniaId, $equipo)
	{
		$data = $this->operadores_model->getOperadoresPorCampaniaYEquipos($campaniaId, $equipo);
		
		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => true],'data' => $data];
		return $response;
	}
	
	/**
	 * Obtiene los operadores por tipo y equipo
	 */
	public function getOperadoresPorTipoYEquipo_post()
	{
		$tipoOperadoresIds = $this->input->post('tipoOperadores');
		$equipo = $this->input->post('equipo');
		$response = $this->operadores_model->getOperadoresPorTipoYEquipo($tipoOperadoresIds, $equipo);
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a activo
	 */
	public function cambiarEstadoOperadorActivo_post()
	{
		$idOperador = $this->input->post('idOperador');
		$result = $this->operadores_model->cambiarEstadoOperadorActivo($idOperador);
		
		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => $result]];
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a Inactivo
	 */
	public function cambiarEstadoOperadorInactivo_post()
	{
		$idOperador = $this->input->post('idOperador');
		$result = $this->operadores_model->cambiarEstadoOperadorInactivo($idOperador);
		
		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => $result]];
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a desactivado
	 */
	public function cambiarEstadoOperadorDesactivado_post()
	{
		$idOperador = $this->input->post('idOperador');
		$result = $this->operadores_model->cambiarEstadoOperadorDesactivado($idOperador);
		
		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => $result]];
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a descanso
	 */
	public function cambiarEstadoOperadorDescanso_post()
	{
		$idOperador = $this->input->post('idOperador');
		$result = $this->operadores_model->cambiarEstadoOperadorDescanso($idOperador);

		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => $result]];
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * quita del caso a un operador
	 */
	public function removeCreditoDeOperador_post()
	{
		$idCredito = $this->input->post('idCredito');
		$idOperador = $this->input->post('idOperador');
		$result = $this->operadores_model->removeCreditoDeOperador($idCredito, $idOperador);
		
		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => $result]];
		
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Registra el comienzo de la gestion caso
	 */
	public function startGestion_post()
	{
		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => true]];
		$idCampania = $this->input->post('idCampania');
		if(!empty($idCampania) and $idCampania != 0) {
			$parametros = array(
				'id_campania' => $this->input->post('idCampania'),
				'id_credito' => $this->input->post('idCredito'),
				'id_operador' => $this->input->post('idOperador'),
				'fecha_inicio_gestion' => date('Y-m-d H:i:s')
			);
			
			$result = $this->Supervisores_model->registrarCasoComoGestionado($parametros);
			
			$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => $result]];
		}
		
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Registra el fin de la gestion del caso
	 */
	public function endGestion_post()
	{
		$idCampania = $this->input->post('idCampania');
		if (!empty($idCampania) and $idCampania != 0) {
			$idCredito = $this->input->post('idCredito');
			$idOperador = $this->input->post('idOperador');
			
			$this->gestionarCierreCaso($idOperador, $idCredito);
			$this->operadores_model->setFinGestionCaso($idCredito, $idOperador, $idCampania);
		}
		
		$response = ['status' => ['code' => parent::HTTP_OK, 'ok' => true]];
		
		return $this->response($response, parent::HTTP_OK);
	}
	
	/**
	 * Obtiene el tiempo restante de gestion para finalizar la campania dada.
	 * 
	 * Actualmente no se esta guardando las horas de una jornada laboral. Pero al momento de hacerse
	 * se puede reemplazar el 8 que esta por defecto
	 */
	public function calcularTiempoRestanteCampania_post()
	{
		$idCampania = $this->input->post('idCampania');
		$horasJornadaLaboral = 8;
		
		$minutosTotalesRestantes = $this->getMinutosRestantesCampania($idCampania);
		$tiempoRestanteFormateado = $this->minutes2human($minutosTotalesRestantes);
		$tiempoRestanteFormateadoJornada = $this->minutes2human($minutosTotalesRestantes,$horasJornadaLaboral);
		
		$dataResponse = [
			'totalRestante' => $tiempoRestanteFormateado, 
			'totalJornadaRestante' => $tiempoRestanteFormateadoJornada
		];
		
		return $this->response($dataResponse, parent::HTTP_OK);
	}
	
	/**
	 * Obtiene los minutos restantes de gestion para finalizar la campania dada
	 * 
	 * @param $idCampania
	 *
	 * @return int
	 */
	private function getMinutosRestantesCampania($idCampania)
	{
		$campania = $this->Supervisores_model->getCampaniaCrmById($idCampania)[0];
		
		$casos = $this->getCasosCampaniaById($idCampania);
		$totalCasos = count($casos);
		
		$casosGestionados = $this->Supervisores_model->casos_gestionados(['id_campania'=> $idCampania]);
		$totalCasosGestionados = count($casosGestionados);
		$casosRestantes = $totalCasos - $totalCasosGestionados;
		
		$minutosTotalesRestantes = 0;
		if ($casosRestantes > 0) {
			$minutosPorCaso = $campania['minutos_gestion'] + ($campania['minutos_extra'] * ($campania['cantidad_extensiones'] - 1) );
			$totalMinutos = $minutosPorCaso * $casosRestantes;
			
			$operadoresAsignados = $this->Supervisores_model->getTodosOperadoresAsignados($idCampania);
			$cantidadOpAsignados = count($operadoresAsignados);
			$cantidadOpAsignados = ($cantidadOpAsignados == 0) ? 1 : $cantidadOpAsignados;
			
			$minutosTotalesRestantes = ceil($totalMinutos / $cantidadOpAsignados);
			
		}
		
		return $minutosTotalesRestantes;
	}
	
	/**
	 * formatea minutos en Dias Hora:Minutos:Segundos
	 *
	 * @param $minutes
	 * @param int $horasJornada Indica la cantidad de horas de una jornada laboral. Por defecto son 24 que corresponde al dia completo
	 *
	 * @return string
	 */
	private function minutes2human($minutes, $horasJornada = 24) {
		$minutosJornadaDiaria = $horasJornada*60;
		$d = floor ($minutes / $minutosJornadaDiaria);
		$h = floor (($minutes - $d * $minutosJornadaDiaria) / 60);
		$m = $minutes - ($d * $minutosJornadaDiaria) - ($h * 60);
		
		return $d . ' Dia ' . date('H:i:s', mktime($h,$m,0));
	}
	
	/**
	 * Asigna casos al operador
	 *
	 * @param $idOperador
	 *
	 * @return bool
	 */
	public function asignarCasosOperadorCampania($idOperador)
	{
		$campania = $this->operadores_model->getCampaniaAsignada($idOperador);
		
		$rtrn = false;
		if(!empty($campania)) {
			$queryCasos = $this->getCasosCampaniaById($campania[0]['id'], true);
			$casosSinAsignar = $this->Supervisores_model->getCasosCampaniaSinAsignar($queryCasos, $campania[0]['asignar'], $campania[0]['re_gestionar']);
			
			if (!empty($casosSinAsignar)){
				foreach ($casosSinAsignar as $casoSinAsignar) {
					$this->Supervisores_model->asignarCasoAOperador($campania[0]['id'], $idOperador, $casoSinAsignar['id']);
				}
				$rtrn = true;
			}
		}
		
		return $rtrn;
	}
	
	/**
	 * Cambia el estado del operador en campania a descanso, trackea dicho cambio y le remueve los casos asignados
	 */
	public function cambiarOperadorADescanso_post()
	{
		$idOperador = $this->input->post('id_operador');
		$campania = $this->operadores_model->getCampaniaAsignada($idOperador);
		$idCampania = $campania[0]['id'];
		
		$this->operadores_model->cambiarEstadoOperadorDescanso($idOperador);
		$this->Supervisores_model->updateEstadoOperadorEnCampaniaADescanso($idOperador, $idCampania);
		$this->Supervisores_model->removeCasosAlOperador($idOperador);
		$result = $this->Supervisores_model->trackCambioEstadoOperadorCampaniaADescanso($idOperador, $idCampania);
		
		$status = parent::HTTP_OK;
		$response = ['status'  => ['code' => $status, 'ok' => $result]];
		
		return $this->response($response, $status);
	}
	
	/**
	 * Reactiva al operador en la campania. Le cambia el estado, le asigna nuevos casos y traquea el cambio de estado
	 */
	public function reactivarOperador_post()
	{
		$idOperador = $this->input->post('id_operador');
		$campania = $this->operadores_model->getCampaniaAsignada($idOperador);
		$idCampania = $campania[0]['id'];
		
		$this->Supervisores_model->updateEstadoOperadorEnCampaniaAActivo($idOperador, $idCampania);
		$this->asignarCasosOperadorCampania($idOperador);
		$result = $this->Supervisores_model->trackCambioEstadoOperadorCampaniaAActivo($idOperador, $idCampania);
		
		$status = parent::HTTP_OK;
		$response = ['status'  => ['code' => $status, 'ok' => $result]];
		
		return $this->response($response, $status);
	}
	
	/**
	 * Saca al operador de la campania
	 */
	public function salirCampania_post()
	{
		$idOperador = $this->input->post('id_operador');
		$campania = $this->operadores_model->getCampaniaAsignada($idOperador);
		$idCampania = (isset($campania[0]['id'])) ? $campania[0]['id'] : 0;
		
		$this->Supervisores_model->desasignarCampaniaAOperador($idOperador, $idCampania);
		$this->Supervisores_model->removeCasosAlOperador($idOperador);
		$result = $this->Supervisores_model->trackCambioEstadoOperadorCampaniaAInactivo($idOperador, $idCampania);
		
		$status = parent::HTTP_OK;
		$response = ['status'  => ['code' => $status, 'ok' => $result]];
		
		return $this->response($response, $status);
	}
	
	/**
	 * Genera la descarga de un CSV del total de casos de una campania
	 *
	 * @param $idCampania
	 */
	public function downloadCSVTotalCasos_get($idCampania)
	{
		$queryCasos = $this->getCasosCampaniaById($idCampania, true);
		$casosSinAsignar = $this->Supervisores_model->queryExportCsvTotalCasos($queryCasos);
		
		$headers = ['ID Credito', 'Documento', 'Telefono', 'Monto', 'Dias de atraso', 'Estado'];
		
		$data = [];
		
		foreach ($casosSinAsignar as $caso) {
			$data[] = [
				$caso['id'],
				$caso['documento'],
				$this->getTelefonoPersonal($caso['id_cliente']),
				$caso['deuda'],
				$caso['dias_atraso'],
				$caso['estado']
			];
		}
		
		$this->generateAndDownloadCSV("TotalCasos-Campania-$idCampania.csv", $headers, $data);
	}
	
	/**
	 * Genera el CSV del total de casos gestionados de una campania
	 *
	 * @param $idCampania
	 */
	public function downlaodCSVTotalCasosGestionados_get($idCampania)
	{
		$this->generateCSVCasosGestionados($idCampania);
	}
	
	/**
	 * Genera el CSV del total de casos gestionados por un operador de una campania
	 *
	 * @param $idCampania
	 * @param $idOperador
	 */
	public function downlaodCSVCasosGestionadosPorOperador_get($idCampania, $idOperador)
	{
		$this->generateCSVCasosGestionados($idCampania, $idOperador);
	}
	
	/**
	 * Genera el CSV del total de casos gestionados de una campania u operador
	 *
	 * @param $idCampania
	 * @param int $idOperador
	 */
	private function generateCSVCasosGestionados($idCampania, $idOperador = 0)
	{
		$desde = date('Y-m-d') . ' 00:00:00';
		$hasta = date('Y-m-d') . ' 23:59:59';
		$gestionOperador = $this->Supervisores_model->getCasosGestionadosPorOperador($idCampania, $desde, $hasta, $idOperador);
		
		$headers = ['ID Credito', 'Documento', 'Telefono', 'Monto', 'Dias de atraso', 'Operador', 'Tipo contacto', 'Respuesta', 'Tiempo gestion (hh:mm:ss)'];
		
		$data = [];
		foreach ($gestionOperador as $caso) {
			$data[] = [
				$caso['idcredito'],
				$caso['documento'],
				$this->getTelefonoPersonal($caso['id_cliente']),
				$caso['deuda'],
				$caso['dias_atraso'],
				$caso['operador'],
				$caso['contacto'],
				$caso['respuesta'],
				date('H:i:s', mktime(0, 0, $caso['tiempo']))
			];
		}
		
		$fileName = "GestionOperador-$idOperador-Campania-$idCampania.csv";
		if ($idOperador == 0) {
			$fileName = "Gestionados-Campania-$idCampania.csv";
		}
		
		$this->generateAndDownloadCSV($fileName, $headers, $data);
	}
	
	
	/**
	 * Genera y descarga un csv con los datos proporcionados
	 *
	 * @param $fileName
	 * @param $headers
	 * @param $data
	 */
	private function generateAndDownloadCSV($fileName, $headers, $data)
	{
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="' . $fileName . '";');
		
		// open the "output" stream
		// see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
		$fd = fopen('php://output', 'w');
		ob_clean(); // clean slate
		
		fputcsv($fd, $headers, ';');
		foreach ($data as $record) {
			fputcsv($fd, $record, ';');
		}
		
		ob_flush(); // dump buffer
		fclose($fd);
	}
	
	/**
	 * Genera un CSV con el detalle de la Tipificacion de una campania por el Tipo de respuesta
	 * 
	 * @param $idCampania
	 * @param $idTipo
	 */
	public function downloadCSVTipificacionPorTipo_get($idCampania, $idTipo)
	{
		$gestionOperador = $this->Supervisores_model->getDetalleTipificacionPorTipo($idCampania, $idTipo);
		
		$headers = ['ID Credito', 'Documento', 'Telefono', 'Monto', 'Dias de atraso', 'Operador'];
		
		$data = [];
		foreach ($gestionOperador as $caso) {
			$data[] = [
				$caso['idcredito'],
				$caso['documento'],
				$this->getTelefonoPersonal($caso['id_cliente']),
				$caso['monto'],
				$caso['dias_atraso'],
				$caso['operador'],
			];
		}
		
		$fileName = "Tipificacion-tipo-$idTipo-Campania-$idCampania.csv";
		$this->generateAndDownloadCSV($fileName, $headers, $data);
	}
	
	/**
	 * Genera un CSV con el detalle de la Tipificacion de una campania por el Detalle de respuesta
	 * 
	 * @param $idCampania
	 * @param $idDetalle
	 */
	public function downloadCSVTipificacionPorDetalle_get($idCampania, $idDetalle)
	{
		$gestionOperador = $this->Supervisores_model->getDetalleTipificacionPorDetalleRespuesta($idCampania, $idDetalle);
		
		$headers = ['ID Credito', 'Documento', 'Telefono', 'Monto', 'Dias de atraso', 'Operador'];
		
		$data = [];
		foreach ($gestionOperador as $caso) {
			$data[] = [
				$caso['idcredito'],
				$caso['documento'],
				$this->getTelefonoPersonal($caso['id_cliente']),
				$caso['monto'],
				$caso['dias_atraso'],
				$caso['operador'],
			];
		}

		$fileName = "Tipificacion-detalle-$idDetalle-Campania-$idCampania.csv";
		$this->generateAndDownloadCSV($fileName, $headers, $data);
	}
	
	
	/**
	 * Obtiene el primer telefono personal de la persona
	 * 
	 * @param $idCliente
	 *
	 * @return string
	 */
	private function getTelefonoPersonal($idCliente)
	{
		$telefonos = $this->cliente_model->get_agenda_personal([
			"id_cliente" => $idCliente,
			"fuente" => "PERSONAL"
		]);
		
		$telefonoPersonal = '';
		if (!empty($telefonos)) {
			$telefonoPersonal = $telefonos[0]['numero'];
		}
		
		return $telefonoPersonal;
	}
 	
	/**
	 * Envia todos los templates whatsapp de una campania
	 */
	public function sendCampaniaWhatsappTemplates_post()
	{
		$idCampania = $this->input->post('idCampania');
		$templateId = $this->input->post('templateId');
		$canal = $this->input->post('canal');
		$campaign = $this->cronograma_model->getCampaignById($idCampania);
		
		if (is_null($canal)) {
			$canal = $campaign['canal'];
		}
		
		$cantidadEnvios = $this->input->post('envios') ?? 20;
		$tiempoEspera = $this->input->post('tiempo') ?? 2;
		$limit = $this->input->post('limit') ?? 0;
		$log = $this->input->post('log') ?? false;
		$verbose = (boolean)$this->input->post('v') ?? false;
		
		$requests = $this->run_query($campaign);
		if ($log) {
			echo "registros: " . count($requests) . PHP_EOL;
			echo "Enviando Mensajes" . PHP_EOL;
		}
		
		
		$requestsWhatsapp = [];
		if (!empty($requests)) {
			$jsonRequest = json_decode($requests, true);

			foreach ($jsonRequest['data'] as $row) {
				$requestsWhatsapp[] = $this->getWhatsappMsgConfig($templateId, $canal , $row['idSolicitud'], $row['telefono']);
			}

			$idSolicitudesEnviadas = $this->enviarWhatsappRequests($requestsWhatsapp, $cantidadEnvios, $tiempoEspera, $limit, $log, $verbose, $canal, $templateId);
			
			if ($log) {
				echo "Fin Proceso" . PHP_EOL;
			}
			
			$status = parent::HTTP_OK;
			$response = ['status'  => $status, 'data' => $idSolicitudesEnviadas];
			
			return $this->response($response, $status);
		} else {
			$status = parent::HTTP_BAD_REQUEST;
			$response = ['status'  => $status];
			
			return $this->response($response, $status);
		}
		
		
	}
	
	public function run_query($campania)
	{
		$params = $this->getCampainFilterValues($campania);

		$response = $this->apiCronogramasPost(
			URL_CAMPANIAS . "api/ApiCronogramaCampania/getCronogramaQueryAffected",
			$params
		);
		
		return $response;
	}
	
	private function getCampainFilterValues($campania)
	{
		$filtros = $this->cronograma_model->getFiltrosCampanias(['camp_id' => $campania['id_logica']]);

		$params = array(
			'id_campania' => $filtros['id_campania'],
			'destiny' => ($filtros['destiny'] ?? ''),
			'client_type' => ($filtros['client_type'] ?? ''),
			'accion' => ($filtros['action'] ?? ''),
			'x_credits' => ($filtros['xCredits'] ?? ''),
			'estatus' => ($filtros['estatus'] ?? ''),
			'filtro' => ($filtros['filtro'] ?? ''),
			'logic' => ($filtros['logic'] ?? ''),
			'valor1' => ($filtros['valor1'] ?? ''),
			'valor2' => ($filtros['valor2'] ?? ''),
			'origen_desde' => ($filtros['origen_desde'] ?? ''),
			'origen_desde_valor' => ($filtros['origen_desde_valor'] ?? ''),
			'origen_hasta' => ($filtros['origen_hasta'] ?? ''),
			'origen_hasta_valor' => ($filtros['origen_hasta_valor'] ?? ''),
		);
		return $params;
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
	
	/**
	 * Obtengo la informacion necesaria para realizar el envio masivo de templates de whatsapp
	 *
	 * @param $idCampania
	 * @param $templateId
	 * @param $canal | ($canal == '15140334') ? 1 : 2 | 1 = ventas, 2 = cobranzas
	 *
	 * @return array
	 */
	private function getCampaniaWhatsappRequestParameters($idCampania, $templateId, $canal)
	{
		$this->load->helper('formato');
		
		$casos = $this->getCasosCampaniaById($idCampania);
		
		$requests = [];
		foreach ($casos as $caso) {
			$solicitud = $this->solicitud_model->getSolicitudesBy(['id_credito' => $caso['id']]);
			
			if (isset($solicitud[0])) {
				$solicitud = $solicitud[0];
				
				$config = $this->getWhatsappMsgConfig($templateId, $canal, $solicitud->id, $solicitud->telefono);
				if (!empty($config)) {
					$requests[] = $config;	
				}
			}
		}
		return $requests;
	}
	
	/**
	 * Procesa un csv y hace envios de templates por whatsapp
	 */
	public function sendCampaniaWhatsappTemplatesByCSV_post()
	{
		$this->load->helper('csv_format_helper');
		
		$templateId = $this->input->post('templateId');
		$canal = $this->input->post('canal');
		
		$cantidadEnvios = $this->input->post('envios') ?? 20;
		$tiempoEspera = $this->input->post('tiempo') ?? 2;
		$limit = $this->input->post('limit') ?? 0;
		$log = $this->input->post('log') ?? false;
		$verbose = (boolean)$this->input->post('v') ?? false;
		
		$rules = [
			'delimiter' => ';',
			'columns' =>[
				[
					'name' => 'id',
					'required' => true,
					'type' => 'number',
				],
				[ 'required' => false ],
				[ 'required' => false ],
				[ 'required' => false ],
				[ 'required' => false ],
				[ 'required' => false ],
				[
					'name' => 'numero',
					'required' => true ,
					'type' => 'number'
				]
			]
		];
		
		$fileName = $_FILES['file']['tmp_name'];
		$csvValidation = checkCsvFormat($rules,$fileName);
		
		if ($csvValidation['result']) {
			$csvLines = [];
			$fh = fopen($fileName, 'r+');
			while( ($row = fgetcsv($fh, 8192,';')) !== FALSE ) {
				$csvLines[] = $row;
			}
			unset($csvLines[0]);
			
			$requests = [];
			foreach ($csvLines as $csvLine) {
				$solicitudId = $csvLine[0]; //id 
				$telefono = $csvLine[6]; //telefono
				
				$requests[] = $this->getWhatsappMsgConfig($templateId, $canal , $solicitudId, $telefono);
			}
			
			$this->enviarWhatsappRequests($requests, $cantidadEnvios, $tiempoEspera, $limit, $log, $verbose);
		} else {
			var_dump($csvValidation['error']);
			die();
		}
		
	}
	
	/**
	 * Obtiene un array con los datos necesarios para el envio del template
	 *
	 * @param $templateId
	 * @param $canal
	 * @param $solicitudId
	 * @param $solicitudTelefono
	 *
	 * @return array
	 */
	private function getWhatsappMsgConfig($templateId, $canal , $solicitudId, $solicitudTelefono): array
	{
		$msgWhatsapp = mensaje_whatapp_maker($templateId, $solicitudId);
		$requests = [];
		if (isset($msgWhatsapp['ok']) and $msgWhatsapp['ok']) {
			$templateWhatsapp = $msgWhatsapp['message'];
			
			$requests = [
				'canal' => ($canal == '15140334') ? 1 : 2,
				'idSolicitud' => $solicitudId,
				'telefono' => $solicitudTelefono,
				'template' => $templateWhatsapp,
				'idTemplate' => $templateId
			];
		}
		return $requests;
	}
	
	/**
	 * Envia los request de whastapp
	 *
	 * @param $requests
	 * @param $cantidadEnvios | Cantidad maxima de envios por segundo
	 * @param $tiempoEspera | Una vez alcanzada la cantidad maxima de envios por segundo esperara esta cantidad de segundos
	 * @param $limit | cantidad limite que enviara
	 * @param bool $log | indica si el proceso mostrara mensajes
	 * @param bool $verbose | indica si el proceso mostrara mensajes detallados
	 *
	 * @return void
	 */
	private function enviarWhatsappRequests($requests, $cantidadEnvios, $tiempoEspera, $limit, $log = false, $verbose = false)
	{
		$tracksIdSolicitudes = [];
		$idSolicitudes = [];
		foreach ($requests as $k => $request) {
			if ($k % ($cantidadEnvios - 1) == 0) {
				sleep($tiempoEspera);
			}
			
			if ($verbose) {
				echo "Enviando Mensaje a solicitud " . $request['idSolicitud'] . PHP_EOL;
			}
			
			$idSolicitudes[] = $request['idSolicitud'];
			$response = sendWhatsappTemplate(
				$request['canal'],
				$request['idSolicitud'],
				$request['telefono'],
				$request['template'],
				$request['idTemplate']
			);
			
			$jsonResponse = json_decode($response);
			if (isset($jsonResponse->template)) {
				//hago el trackeo luego para agilizar el envio
				$tracksIdSolicitudes[] = $request['idSolicitud'];
			}
			
			if ($limit != 0) {
				if (($k+1) == $limit) {
					break;
				}
			}
		}
		
		if ($log) {
			echo "Templates enviados: " . count($tracksIdSolicitudes) . PHP_EOL;
			echo "Registrando tracks" . PHP_EOL;
		}
		
		foreach ($tracksIdSolicitudes as $k => $idSolicitud) {
			$idOperador = (!is_null($this->session->userdata('idoperador')))
				? $this->session->userdata('idoperador')
				: '192';
			
			trackEnvioWhatsapp($idSolicitud, $idOperador);
		}
		
		return $idSolicitudes;
	}
	
	public function envioPreliminarCampaniasWhatsapp_post()
	{
		$idCampania = $this->input->post('idCampania');
		$idTemplate = $this->input->post('idTemplate');
		$canal = $this->input->post('canal');
		
		$data = [
			'idCampania' => $idCampania,
			'templateId' => $idTemplate,
			'canal' => $canal,
			'limit' => 5,
			'v' => false,
			'log' => false,
		];
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => base_url()."api/campanias/sendCampaniaWhatsappTemplates",
			// CURLOPT_HTTPHEADER => $header,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 999999,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			CURLOPT_POSTFIELDS => $data
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		$jsonResponse = json_decode($response);
		
		$infoEnvios = [];
		foreach ( $jsonResponse->data as $idSolicitud) {
			$infoEnvios[] = $this->getEnvioInfo($idSolicitud, $canal, $idTemplate);
		}
		
		$status = parent::HTTP_OK;
		$response = ['status'  => $status, 'data' => $infoEnvios];
		
		return $this->response($response, $status);
	}
	
	private function getEnvioInfo($idSolicitud, $canal, $idTemplate)
	{
		return $this->Chat->getChatInfoByIdSolicitud($idSolicitud, $canal, $idTemplate);
	}
	
}
?>


