<?php

class Cronogramas extends CI_Controller {

    protected $CI;

    public function __construct() {
        parent::__construct();

			$this->load->helper("encrypt");
			$this->load->helper('date');
	
			$this->load->model("InfoBipModel");
            $this->load->model('operaciones/Gastos_model');
            $this->load->model('supervisores/Supervisores_model', 'supervisores');
            $this->load->model('operaciones/Beneficiarios_model');
            $this->load->model('operaciones/Operaciones_model');
            $this->load->model('operadores/Operadores_model');
            $this->load->model('User_model');
            $this->load->model('Chat');
            $this->load->model('Solicitud_m', 'solicitud_model', TRUE);
			$this->load->model('Devolucion_model', 'devolucion_model', TRUE);
            $this->load->model('Usuarios_modulos_model');
            $this->load->model("RecaudosSImputar_model");
            $this->load->model('ImputacionCredito_model','imputacionCredito',TRUE);
            $this->load->model('Solicitud_m');
            $this->load->model('cronograma_campanias/Cronogramas_model', 'cronograma_model', TRUE);

            $this->load->library('form_validation');
			$this->load->library('layout/Layout');
            
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }
	
	public function index()
	{
		$data = [
			'campanias' => $this->cronograma_model->getAllCampanias(),
			'prelanzamiento' => $this->cronograma_model->getPrelanzamiento()
		];
		
		$layout = new Layout('cronograma_campanias/cronograma_campanias_index_view', $data);
		$layout->viewLayout();
	}
	
	public function new()
	{
		$data = [
			'proveedores' => $this->InfoBipModel->get_proveedores_cronograma_campanias(),
		];
		
		$layout = new Layout('cronograma_campanias/cronograma_campanias_new_view.php', $data);
		$layout->viewLayout();
	}
	
	public function edit($campaignId)
	{
		$constants = $this->cronograma_model->getConstants();
		$data = [
			'campaign' => $this->cronograma_model->getCampaignById($campaignId),
			'proveedores' => $this->InfoBipModel->get_proveedores_cronograma_campanias(),
			'whatsappTemplates' => $this->cronograma_model->getActiveWhatsappTemplate(),
			'logicas' => $this->supervisores->get_all_logicas(),
			'filterValues' => $constants,
			'receivers' => $this->getAllReceivers($constants),
			'clientTypes' => $this->getAllClientTypes($constants),
			'actions' => $this->getAllActions($constants),
			'status' => $this->getAllStatus($constants),
			'filters' => $this->getAllFilters($constants),
			'logics' => $this->getAllLogics($constants),
			'origins' => $this->getAllOrigins($constants),
			'envios' => $this->getAllMetodosEnvio($constants),
			'formatos' => $this->getAllMetodosFormatoEnvio($constants),
			'slackNotificados' => $this->cronograma_model->getSlackNotificados($campaignId),
		];
		
		$layout = new Layout('cronograma_campanias/cronograma_campanias_edit_view.php', $data);
		$layout->viewLayout();
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
	
	public function cron()
	{
		//cronograma_model
		$uniqueEvents = $this->cronograma_model->getAllNearUniqueEvents();
		
		$yearEvents = $this->cronograma_model->getAllNearYearEvent();
		$monthEvents = $this->cronograma_model->getAllNearMonthEvent();
		$weakEvents = $this->cronograma_model->getAllNearWeakEvent();
		
		$events = array_merge($yearEvents, $monthEvents, $weakEvents);
		
		$aux1 = [];
		foreach ($events as $item) {
			$aux1[] = $item['id'];
		}
		
		$eventsToLaunch = [];
		if (!empty($aux1)) {
			$eventsToLaunch = $this->cronograma_model->getFilteredEventsForTime($aux1);
		}
		$dayEvents = $this->cronograma_model->getAllEventsNearWithHours();
		
		$events = array_merge($eventsToLaunch, $dayEvents, $uniqueEvents);
		// var_dump("aqui");
		// die();
		$k = 0;
		foreach ($events as $event) {
			$jsonParams = json_decode($event['params']);
			$slackNotifications = $this->cronograma_model->getSlackNotificados($jsonParams->idCampania);
			$slackIds = [];
			if (!empty($slackNotifications)) {
				$campania = $this->cronograma_model->getCampaignById($jsonParams->idCampania);
				foreach ($slackNotifications as $slackNotification) {
					$slackIds[] = $slackNotification['slack_id'];	
				}
			}
			if($uniqueEvents[$k]["type_env"] == "WSP"){
				$this->cronograma_model->savePreLanzamiento($jsonParams->idCampania, $jsonParams->templateId, $event['id']);
				
					$msg = "La campaña \"".$campania['nombre_logica']."\" esta lista para ser enviada. Por favor dirigase aqui https://backend.solventa.co/backend/cronograma_campanias/Cronogramas para visualizarla.";
					$this->send_slack(implode(',',$slackIds), $msg);
			}else{

				$curl = curl_init();
				$options[CURLOPT_URL] = URL_BACKEND."api/ApiSupervisores/generate_csv/".$uniqueEvents[$k]["id_mensaje"]."/".$uniqueEvents[$k]["id_campania"]."/".$uniqueEvents[$k]["id"];
				$options[CURLOPT_CUSTOMREQUEST] = "GET";
				$options[CURLOPT_RETURNTRANSFER] = TRUE;
				$options[CURLOPT_ENCODING] = '';
				$options[CURLOPT_MAXREDIRS] = 10;
				$options[CURLOPT_TIMEOUT] = 500;
				$options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
				curl_setopt_array($curl,$options);
				$res = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);
			}
			$k++;
		}
	}
	
	public function send_slack($to, $msg)
	{
		$params = array(
			'to' => $to,
			'msg' => $msg,
		);
		
		$end_point = URL_CAMPANIAS."ApiSlackSendMessage";
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $end_point,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_VERBOSE => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
			CURLOPT_POSTFIELDS => $params
		));
		
		$data2 = curl_exec($curl);
		// var_dump($data2);die;
		$err   = curl_error($curl);
		curl_close($curl);
		
		if ($err){
			$response = FALSE;
		}else{
			$response = TRUE;
		}
		
		return $response;
		
	}
	
	public function previewLanzamiento($idCampania, $idTemplate, $idEvent, $idPrelanzamiento)
	{
		$campaign = $this->cronograma_model->getCampaignById($idCampania);
		$canal = $campaign['canal'];

		$data = [
			'idPrelanzamiento' => $idPrelanzamiento,
			'idEvent' => $idEvent,
			'idCampania' => $idCampania,
			'campaing' => $campaign,
			'templateId' => $idTemplate,
			'canal' => $canal,
		];
		
		$layout = new Layout('cronograma_campanias/cronograma_campanias_preview_lanzamiento.php', $data);
		$layout->viewLayout();
	}
	
	public function markAsSendedPrelanzamiento()
	{
		$idPrelanzamiento = $this->input->post('id');
		$this->cronograma_model->markAsSendedPrelanzamiento($idPrelanzamiento);
	}
	
}

