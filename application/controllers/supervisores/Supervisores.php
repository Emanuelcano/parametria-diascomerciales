<?php
class Supervisores extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('is_logged_in')) {
            // MODELS
            $this->load->model("InfoBipModel");
            $this->load->model('operaciones/Gastos_model');
            $this->load->model('supervisores/Supervisores_model');
            $this->load->model('operaciones/Beneficiarios_model');
            $this->load->model('operaciones/Operaciones_model');
            $this->load->model('operadores/Operadores_model');
            $this->load->model('User_model');

            $this->load->model('Chat');

            $this->load->model('Chat');
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
			$this->load->model('Devolucion_model', 'devolucion_model', TRUE);
            $this->load->helper("encrypt");
            $this->load->model('Usuarios_modulos_model');
            $this->load->model("RecaudosSImputar_model");
            $this->load->model('ImputacionCredito_model','imputacionCredito',TRUE);
			$this->load->model('cronograma_campanias/Cronogramas_model', 'cronograma_model', TRUE);
            $this->load->model('Solicitud_m');
            // LIBRARIES
            $this->load->library('form_validation');
			$this->load->library('layout/Layout');
            // HELPERS
            $this->load->helper('date');
        } else {
            redirect(base_url('login'));
        }  
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }

//Begin Esthiven Garcia Abril 2020
    public function index() {
        $link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/',$link);
        $permisos = FALSE;
        foreach ($this->session->modulos as $key => $value):
            if($value->url == $link_array[1])
            {
                $permisos = TRUE;
                break;
            } 
        endforeach;
        if ($permisos) 
        {
            $title['title'] = 'Operaciones';
            $this->load->view('layouts/adminLTE', $title);
            $cantidad_beneficiarios = $this->Operaciones_model->get_cantidad_beneficiarios();
            $cantidad_gastos = $this->Operaciones_model->get_cantidad_gastos();
            $cantidad_devoluciones = $this->devolucion_model->cantidad_solicitudes_devoluciones();
            $criterios = $this->Supervisores_model->get_all_criterios();
            $cantidad_camp_manuales = count($this->Supervisores_model->getCampaniasManuales());
            $data = array('cant_beneficiarios' => $cantidad_beneficiarios, 'cant_camp_manuales' => $cantidad_camp_manuales, 'cant_gastos' => $cantidad_gastos, 'tipos_criterios'=>$criterios, 'total_devoluciones' => $cantidad_devoluciones[0]->cantidad);
            $data['title']   = 'Supervisor Cobranzas';
            $data['heading'] = 'Cobranzas';

			$data['show'] = $this->input->get('show');
            $this->load->view('supervisores/supervisores', ['data' => $data]);
            
            return $this;
        } 
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    public function VistaOperadoresCobranzas()
    {   
        
        $filtro['tipo_operadores'] = [5,6,13];
        $data['lista_operadores'] = $this->Operadores_model->get_operadores_by($filtro);
        //echo "<pre>";
        //print_r($data['lista_operadores']);
        //echo "</pre>";
        //die;
        $this->load->view('supervisores/cobranzas/box_operadores_cobranzas', ['data' => $data]);
        return $this;
    }

    public function datos_operador()
    {
        $filtro[0]['columna'] = 'op.idoperador';
        $filtro[0]['valor'] = $this->input->post('operador');
        $filtro[0]['or'] = FALSE;
        $data['operador'] = $this->Operadores_model->get_operador_by($filtro)[0];
        $data['tipos_operador'] = $this->Operadores_model->get_tipos_operador();
        $usuario = $this->User_model->get_user_inf($data['operador']->id_usuario);
        
        if ( !empty($usuario))
        {
            $data['user_auth']['user'] = $usuario[0]->username;
            $data['user_auth']['password'] = decrypt($usuario[0]->password);
            $data['user_auth']['name'] = $usuario[0]->first_name;
            $data['user_auth']['lastname'] = $usuario[0]->last_name;
        } else 
        {
            $data['user_auth']['user'] = "";
            $data['user_auth']['password'] = "";
            $data['user_auth']['name'] = "";
            $data['user_auth']['lastname'] = "";
        }
        
        $data['modulos'] = $this->Usuarios_modulos_model->get_modulos_usuario($data['operador']->id_usuario);

        if ($this->input->post('elemento') == 'edit') 
        {
            $this->load->view('supervisores/cobranzas/box_editar_operador_cobranzas', ['data' => $data]);
        } else
        {
            $this->load->view('supervisores/cobranzas/box_ver_operador_cobranzas', ['data' => $data]);
        }
    }

    public function VistaConfigCentrales() {

        $lista_operadores=$this->Operadores_model->get_lista_operador_central();
        $lista_skills=$this->Operadores_model->get_lista_skill_central();
        // var_dump($lista_skills);die();

        $this->load->view('supervisores/configuracion_centrales',['lista_operadores'=>$lista_operadores,'lista_skills'=>$lista_skills ]);
        return $this;
    }
    public function VistaFixPayment() {       
        $this->load->view('supervisores/fix_payment',[]);
        return $this;
    }

    public function VistaCampaniasSMS() {
		//pronto estara deprecada
		
         $this->session->set_flashdata('proveedores_rs', $this->InfoBipModel->get_proveedores_cronograma_campanias());
         $this->session->set_flashdata('logicas_rs', $this->InfoBipModel->get_all_logicas());
	
		$headers = array();
		$end_point = URL_CAMPANIAS."api/ApiCronogramaCampania/getFilterValues";
	
		$otherop = array (
			'timeout' => 500,
			'connect_timeout' => 500,
		);
		$request = Requests::get($end_point, $headers, [], $otherop);
	
		$response = $request->body;
		$aux=json_decode($response,TRUE);
         
         $receivers = $this->getAllReceivers($aux['data']);
         $clientTypes = $this->getAllClientTypes($aux['data']);
         $actions = $this->getAllActions($aux['data']);
         $status = $this->getAllStatus($aux['data']);
         $filters = $this->getAllFilters($aux['data']);
         $logics = $this->getAllLogics($aux['data']);
         $origins = $this->getAllOrigins($aux['data']);
         $metodoEnvio = $this->getAllMetodosEnvio($aux['data']);
         $metodoFormatoEnvio = $this->getAllMetodosFormatoEnvio($aux['data']);
		 $campanias = $this->get_all_campanias();
         $prelanzamiento = $this->cronograma_model->getPrelanzamiento();
	
         $data = [
         	'receivers' => $receivers,
         	'clientTypes' => $clientTypes,
         	'actions' => $actions,
         	'status' => $status,
         	'filters' => $filters,
         	'logics' => $logics,
         	'origins' => $origins,
			'envios' => $metodoEnvio,
			'formatos' => $metodoFormatoEnvio,
			'filterValues' => $aux['data'],
			'campanias' => $campanias,
			'prelanzamiento' => $prelanzamiento
		 ];
	
		 
//		$layout = new Layout('supervisores/configuracion_campania_sms', ['data' => $data]);
//		$layout->viewLayout();
		$this->load->view('supervisores/configuracion_campania_sms', ['data' => $data]);
		return $this;
    }


    public function vistaGeneraCampania() {
        $tipo_benef = $this->Beneficiarios_model->get_tipo_beneficiario();
        $lista_rubro = $this->Beneficiarios_model->get_lista_rubro();
        $forma_pago = $this->Beneficiarios_model->get_forma_pago();
        $moneda = $this->Beneficiarios_model->get_moneda();
        $tipo_documento = $this->Beneficiarios_model->get_tipo_documento();
        $provincia = $this->Beneficiarios_model->get_provincia();
        $banco = $this->Beneficiarios_model->get_banco();
        $tipo_cuenta = $this->Beneficiarios_model->get_tipo_cuenta();
        $beneficiarios = $this->Beneficiarios_model->get_beneficiarios();
        $criterios = $this->Supervisores_model->get_all_criterios();
        $data = array('tipos_criterios'=>$criterios);
       
        //$this->load->view('operaciones/vistaBeneficiarios', ['data' => $data]);
        $this->load->view('supervisores/gestion_campanias_main',$data);
        return $this;
    }

    public function vistaGeneraCampaniaManual() {
		$templatesWhatsapp = $this->Chat->getWhatsappTemplates();
		$templatesSMS = $this->Chat->getSMSTemplates();
		$templatesEmail = $this->Chat->getEmailTemplates();
		
		$tiposOperadores = $this->Operadores_model->get_tipos_operador();
		
		$data = [
			'templatesWhatsapp' => $templatesWhatsapp,
			'templatesSMS' => $templatesSMS,
			'templatesEmail' => $templatesEmail,
			'tiposOperadores' => $tiposOperadores
		];
		
        $this->load->view('supervisores/gestion_campanias_manuales_main', ['data' => $data]);
        return $this;
    }
    
    
    public function vistaAjustarCuentas() {
        //$this->load->view('operaciones/vistaBeneficiarios', ['data' => $data]);
        $this->load->view('supervisores/ajustar_cuentas');
        return $this;
    }

    public function vistaSolicitarDevolucion() {
        //$this->load->view('operaciones/vistaBeneficiarios', ['data' => $data]);
        $this->load->view('supervisores/solicitar_devolucion');
        return $this;
    }

    public function vistaSolicitarImputacion(){
        $this->load->model('Cliente_model');
        $bancoOrigen = $this->Cliente_model->getBancoOrigen();
        $bancoDestino = $this->Cliente_model->getBancoDestino();
        $data = array(
            'bancosOrigen' => $bancoOrigen,
            'bancosDestino' => $bancoDestino
        );
        $this->load->view('supervisores/solicitar_imputacion', $data);
        return $this;
    }

public function render_mail_template($idlogic){

    //var_dump($idlogic);die;
    $rs_plantilla  = $this->Supervisores_model->get_all_plantillas_for_campanias($idlogic);
    echo $rs_plantilla[0]->html_contenido;
    /*$data["insumo"] = $this->pdf->res_productoxvenc($IDcriterio, $fecven, $fecven2);
    $this->load->vars($data);
    $datos = array(

        "datos_empresa" => $datos_empresa,
        "fechaActual"   => $fechaActual,

    );

    $this->load->view("SAI/reportes/reporte-productosxvenc", $datos);*/
}

    public function send_msj_imputacion() {

        $doc = $this->input->post('doc');
        $action = $this->input->post('action');

        $sms = $this->Solicitud_m->get_agenda_personal_solicitud(["documento" => $doc,"fuentes" => "'PERSONAL DECLARADO'","estado" => 1]);
        if (count($sms)> 0) {
            $whatsapp = $this->Solicitud_m->get_agenda_whatsapp(["documento" => $doc,"status_chat" => 'activo', "from" => $sms[0]['numero']]);
            $response['action'] = $action;
            $msj = [];
            switch ($action) {
                case 0:
                    $msj['sms'] = 'Hola '.$sms[0]['contacto'].'.No pudimos procesar el comprobante que nos enviaste. Por favor comunícate con nosotros al whatsapp [link_whatsapp] para poder procesar tu pago.';
                    $msj['ws']  = 'No pudimos procesar el comprobante que nos enviaste. Por favor escríbenos por aquí para poder procesar tu pago.';
                break;
                case 1:
                    $msj['sms'] = 'Hola '.$sms[0]['contacto'].'. Ya solicitamos que tu pago sea procesado.';
                    $msj['ws']  = 'Ya solicitamos que tu pago sea procesado. Una vez se realice la imputación recibirás otra notificación.';
                    break;
                case 2:
                    $msj['sms'] = 'Hola '.$sms[0]['contacto'].'. Tu pago será procesado de forma automática. Si recibes alguna notificación de cobro, no requieres comunicarte con nosotros, debes esperar que tu pago se procese.';
                    $msj['ws'] = 'Hola '.$sms[0]['contacto'].'. Tu pago será procesado de forma automática. Si recibes alguna notificación de cobro, no requieres comunicarte con nosotros, debes esperar que tu pago se procese.';
                    break;
                default:
                    break;
            }
        }        

        if (count($sms) > 0) {
            $endPoint = URL_CAMPANIAS."ApiEnvioComuGeneral";
            $response['sms'] = Requests::post($endPoint, [], ["tipo_envio" => 2,"servicio" => 2,"text" => $msj['sms'],"numero" => "+57".$sms[0]['numero']]); 
        }
        if (count($whatsapp) > 0) {
            $endPoint = base_url()."comunicaciones/twilio/send_new_message";
            $response['ws'] = Requests::post($endPoint, [], ['chatID'  => $whatsapp[0]['id'],'message' => $msj['ws'],'operatorID' => 192 ]); 
        }
        echo json_encode($response);
    }
    
    
//FIN Esthiven Garcia Abril 2020

    /*************************************************/
    /*** Se obtienen las solicitudes de imputación ***/
    /*************************************************/
    public function buscarSolicitudImputacion(){
        $data = [];
        $data = $this->Supervisores_model->getSolicitudImputacion();

        echo json_encode(['data'=>$data]);
    }
    public function buscarPrecargaImputacion(){
        $data = [];
        $data = $this->Supervisores_model->getPrecargaImputacion();
        echo json_encode(['data'=>$data]);
    }
    public function getPagosImputados(){
        $doc = $this->input->post('id_cliente');
        $data = [];
        $data = $this->Supervisores_model->getPagosImputados($doc);
        echo json_encode(['data'=>$data]);
    }

    public function listar_campanias_get(){
        $param = array();
        $data= $this->Supervisores_model->listar_campanias_manuales($param);
        $datos  = $this->solicitud_model->BuscarBotonesOperador();
        $arrays = json_decode(json_encode($datos), true);
        for($i=0;$i<count($data);$i++){
            if(strlen($data[$i]['id_exclusion']) >= 1){
                $array= explode(",", $data[$i]['id_exclusion']);
                foreach($array as $key=> $values){
                    foreach($arrays as $keys=> $value){
                        if($value['id'] == $values){
                            $exclusiones[$i][] = $value['etiqueta'];
                            $data[$i]['id_exclusion'] = $exclusiones[$i];
                        }
                    }
                }                
            } 
        }
        echo json_encode(['data'=>$data]);
    }
	
	
	/**
	 * @return array
	 */
	private function getAllReceivers($data)
	{
		return [
			$data['CAMPAIGN_RECEIVER_CLIENTES'],
			$data['CAMPAIGN_RECEIVER_SOLICITANTES'],
		];
	}
	
	/**
	 * @return array
	 */
	private function getAllActions($data)
	{
		return [
			$data['CAMPAIGN_ACTION_ALL'],
			$data['CAMPAIGN_ACTION_INCLUIR'],
			$data['CAMPAIGN_ACTION_EXCLUIR']
		];
	}
	
	/**
	 * @return array
	 */
	private function getAllStatus($data)
	{
		return [
			$data['CAMPAIGN_STATUS_VIGENTE'],
			$data['CAMPAIGN_STATUS_CANCELADO'],
			$data['CAMPAIGN_STATUS_MORA']
		];
	}
	
	/**
	 * @return array
	 */
	private function getAllFilters($data)
	{
		return [
			$data['CAMPAIGN_FILTER_DIAS_ATRASO'],
			$data['CAMPAIGN_FILTER_FECHA_VENCIMIENTO'],
			$data['CAMPAIGN_FILTER_MONTO_COBRAR']
		];
		
	}
	
	/**
	 * @return array
	 */
	private function getAllLogics($data)
	{
		return [
			$data['CAMPAIGN_LOGIC_IGUAL_A'],
			$data['CAMPAIGN_LOGIC_MAYOR_A'],
			$data['CAMPAIGN_LOGIC_MENOR_A'],
			$data['CAMPAIGN_LOGIC_DISTINTO_A'],
			$data['CAMPAIGN_LOGIC_ENTRE']
		];
	}
	
	
	/**
	 * @return array
	 */
	private function getAllClientTypes($data)
	{
		return [
			$data['CAMPAIGN_CLIENT_TYPE_ALL'],
			$data['CAMPAIGN_CLIENT_TYPE_PRIMARIA'],
			$data['CAMPAIGN_CLIENT_TYPE_RETANQUEO'],
		];
	}
	
	/**
	 * @return array
	 */
	private function getAllOrigins($data)
	{
		return [
			$data['CAMPAIGN_ORIGIN_FECHA_DIA'],
			$data['CAMPAIGN_ORIGIN_DIAS_DIA_MENOS'],
			$data['CAMPAIGN_ORIGIN_DIAS_DIA_MAS'],
			$data['CAMPAIGN_ORIGIN_FECHA_FIJA']
		];
	}
	
	private function getAllMetodosEnvio($data)
	{
		return [
			$data['CAMPAIGN_METODO_ENVIO_API'],
			$data['CAMPAIGN_METODO_ENVIO_CSV'],
			$data['CAMPAIGN_METODO_ENVIO_SLACK'],
		];
	}
	
	private function getAllMetodosFormatoEnvio($data)
	{
		return [
			$data['CAMPAIGN_METODO_FORMATO_CSV'],
			$data['CAMPAIGN_METODO_FORMATO_XLS'],
		];
	}
	
	public function get_all_campanias()
	{
		$headers = array('Accept' => 'application/json');
		
		$otherop = array(
			'binarytransfer' => 1,
			'timeout' => 500,
			'connect_timeout' => 500,
		);
		
		$request = Requests::post(URL_CAMPANIAS . 'api/ApiCronogramaCampania/getAllCampanias', $headers, null, $otherop);
		$response = $request->body;
		
		$datos = json_decode($response);

		return $datos->data;
	}

    public function VistaDistribucionCobranzas()
    {   
        $data['lista_distribucion'] = $this->Supervisores_model->ConsultaDistribucion();
        $this->load->view('supervisores/box_distribucion_cobranzas', ['data' => $data]);
        return $this;
    }
    public function vistacargarTiemposGestion()
    {   
        $data['lista_distribucion'] = $this->Supervisores_model->ConsultaDistribucion();
        $this->load->view('supervisores/vista_tiempo_gestion', ['data' => $data]);
        return $this;
    }

    
    /** MODULO SUPERVISOR VENTAS 
     * Camilo Franco
    */

    public function vistaSupervisorVentas() 
    {   
        $link = $_SERVER['PHP_SELF'];
        $link_array = explode('index.php/',$link);
        $permisos = FALSE;
        foreach ($this->session->modulos as $key => $value):
            if($value->url == $link_array[1])
            {
                $permisos = TRUE;
                break;
            } 
        endforeach;
        if($permisos){

            $title['title'] = 'Supervisor Ventas';
            $this->load->view('layouts/adminLTE', $title);
    
            $this->load->view('supervisores/vista_supervisor_ventas');
            return $this;
        }
        else
        {
            redirect(base_url()."dashboard");
        }
    }

    public function vistaGestionObligatoria() 
    {
        $this->load->view('supervisores/supervisor_ventas/gestion_obligatoria');
        return $this;
    }

    public function vistaNuevaConfigurcion() 
    {
		$data['tipo_operador_list'] = $this->Operadores_model->get_tipos_operador();
        $this->load->view('supervisores/supervisor_ventas/nueva_configuracion_go', $data);
        return $this;
    }
    public function vistaEditarConfigurcion() 
    {
        $filtro['id'] = $this->input->get();
        $config = $this->solicitud_model->find_configuracion_obligatorias_get($filtro['id']);
        $data['tipo_operador_list'] = $this->Operadores_model->get_tipos_operador();
        
        $this->load->view('supervisores/supervisor_ventas/update_configuracion_go', ['config' => $config, 'tipo_operador_list' => $data['tipo_operador_list']]);
        return $this;
    }
    public function VistaOperadoresVentas()
    {   
        $filtro['tipo_operadores'] = [1,4];
        $data['lista_operadores'] = $this->Operadores_model->get_operadores_by($filtro);
        
        $this->load->view('supervisores/cobranzas/box_operadores_cobranzas', ['data' => $data]);
        return $this;
    }

    public function vistaRecuadosSinImputar()
    {
        return $this->load->view("supervisores/RecaudosSImputar_view");
    }

    public function obtenerRecuadosSImputar()
    {
        if(isset($_POST["id"])){
            $id = $_POST["id"];
            $data = $this->RecaudosSImputar_model->get_recaudos($id);
            echo json_encode($data);
        }else{
            $data = $this->RecaudosSImputar_model->get_recaudos();
            echo json_encode(["data"=>$data]);
        }
    }

    public function validarDocumento()
    {   
        $docValidar = $_POST["documentoVal"];
        $rs_result = $this->RecaudosSImputar_model->validarDocumento($docValidar);
        echo json_encode($rs_result);
    }

    public function imputarPago()
    {
        $documento = $_POST["documento"];
        $id = $_POST["id"];
        $dataCliente = $this->RecaudosSImputar_model->validarDocumento($documento);
        $dataSinImp = $this->RecaudosSImputar_model->get_recaudos($id);
        $insert = [
            'id_cliente' => $dataCliente[0]['id'],
            'id_credito' => $dataCliente[0]['id_credito'],
            'id_creditos_detalle' => $dataCliente[0]['id_credito_detalle'],
            'fecha_transferencia' => $dataSinImp[0]['fecha_recaudo'],
            'monto_transferencia' => $dataSinImp[0]['monto_total'],
            'id_banco_origen' => $dataCliente[0]["id_banco"],
            'id_cuenta_destino' => 4,
            'referencia' => '',
            'comprobante' => '',
            'fecha_creacion' => date('Y-m-d H:i:s')
        ];
        $insert_id = $this->imputacionCredito->insert($insert);
        
        if($insert_id > 0){
            $data_registro_pago = [
                'id_credito_detalle' => $dataCliente[0]['id_credito_detalle'],
                'monto' => $dataSinImp[0]['monto_total'],
                'fecha_pago' => $dataSinImp[0]['fecha_recaudo'],
                'referencia' => '',
                'id_archivo_adjunto' => $insert_id
            ];
            $registro_pago = $this->registrar_pago($data_registro_pago);   
            $registro_pago = json_decode($registro_pago);

            if(!empty($registro_pago->response->pago)){
                $respuesta = $registro_pago->response->respuesta;
                $id_pago_credito = $registro_pago->response->pago;
            }else{          
                $respuesta_array = explode("{", $registro_pago);
                $respuesta_array = explode(",", $respuesta_array[2]);
                $pago_array = $respuesta_array[0];
                $pago = explode(":", $pago_array);
                $pago = str_replace('"','',$pago[1]);
                $respuesta = explode(":", $respuesta_array[2]);
                $respuesta = str_replace('}}','',$respuesta);
                $respuesta = $respuesta[1];
                $id_pago_credito = $pago;
            }
        }

        if($respuesta){
            $data_imputar_pago = [
                'id_cliente' => $dataCliente[0]['id'],
                'monto'      => $dataSinImp[0]['monto_total'],
                'fecha_pago' => $dataSinImp[0]['fecha_recaudo'],
                'medio_pago' => $dataSinImp[0]["origen_pago"],
                'id_pago_credito' => $id_pago_credito
            ];

            $imputar_pago = $this->imputar_pago($data_imputar_pago);
            $imputarPago = json_decode($imputar_pago);
            if(!empty($imputarPago->success)) {
                $response = $imputarPago->success;              
            }

            if(isset($response)){
                $resultado = 'Imputado';
                $procesado = 1;
                $response = $imputar_pago;

                $id_solicitud = $this->imputacionCredito->getIdSolicitudCredito($dataCliente[0]['id_credito']);
                $rmRecSinImp = $this->RecaudosSImputar_model->rmRecaudo($insert_id, $id);

                $response = true;
            }else{
                $response = false;    
            }
        }
        echo json_encode($response);
    }

    private function registrar_pago($data){
        if(ENVIRONMENT == 'development')
        {
            //Consume metodo local de prueba.
            $headers = array('Accept' => 'application/json');
            $hooks = new Requests_Hooks();

            $hooks->register('curl.before_send', function($fp){
                curl_setopt($fp, CURLOPT_TIMEOUT, 300);
            });

            $end_point = "https://testmediospagos.solventa.co/transaccion/RegistrarPago/registrar_pago";
            $request = Requests::post($end_point, $headers, $data,array('hooks' => $hooks));
            $response = $request->body;

        }else{
            $hooks = new Requests_Hooks();

            $hooks->register('curl.before_send', function($fp){
                curl_setopt($fp, CURLOPT_TIMEOUT, 300);
            });
            $headers = array('Accept' => 'application/json');
            $end_point = URL_MEDIOS_PAGOS."transaccion/RegistrarPago/registrar_pago";
            $request = Requests::post($end_point, $headers, $data, array('hooks' => $hooks));
            $response = $request->body;
        }
        return $response;
    }

    private function imputar_pago($data){
        if(ENVIRONMENT == 'development')
        {
            $headers = array('Accept' => 'application/json');
            $headers = array('Accept' => 'application/json');
            $hooks = new Requests_Hooks();
            $hooks->register('curl.before_send', function($fp){
                curl_setopt($fp, CURLOPT_TIMEOUT, 3000);
            });
            $end_point = "https://testmediospagos.solventa.co/transaccion/RegistrarPago/imputacion";
            $request = Requests::post($end_point, $headers, $data, array('hooks' => $hooks));
            $response = $request->body;

        }else{
            $hooks = new Requests_Hooks();
            $hooks->register('curl.before_send', function($fp){
                curl_setopt($fp, CURLOPT_TIMEOUT, 300);
            });
            $headers = array('Accept' => 'application/json');
            $end_point = URL_MEDIOS_PAGOS."transaccion/RegistrarPago/imputacion";

            $request = Requests::post($end_point, $headers, $data, array('hooks' => $hooks));
            $response = $request->body;
        }
        return  $response;
    }
    
	public function vistaGestionAsigAutomatica() 
    {
        $this->load->view('supervisores/supervisor_ventas/gestion_asig_automatica/gestion_asig_automatica');
 
    }    
}

