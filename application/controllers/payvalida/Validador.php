<?php

class Validador extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('layout/Layout');
		$this->load->model("Payvalida_Model", "payvalida");
		$this->load->model("Cliente_model", "cliente");
	}
	
	public function index()
	{
		$data['origin'] = [
			'search' => '',
			'type' => ''
		];
		
		$data['title'] = 'Payvalida validador';
		$layout = new Layout('payvalida/validadorIndex', $data);
		$layout->viewLayout();
	}
	
	public function showByOrderId($orderId)
	{
		$referencia = $this->payvalida->getReferenciaByOrderId($orderId);
		if ($referencia) {
			$origin = [
				'search' => $orderId,
				'type' => 'orderId'
			];
			$this->show($referencia, $origin);
		} else {
			die('no hay referencia');
		}
	}
	
	public function show($reference, $origin = [])
	{
		if (empty($origin)) {
			$data['origin'] = ['search' => $reference,'type' => 'referencia'];
		} else {
			$data['origin'] = $origin;
		}
		
		if (ENVIRONMENT === "production") {
			$url = 'https://api.payvalida.com/api/v3/porders/';
		} else {
			$url = 'https://api-test.payvalida.com/api/v3/porders/';
		}
		
		//		$orderId = '11C20432'; //pendiente;
		//		$orderId = '8918C48751'; //vencida;
		//		$orderId = '2425T92502'; //aceptada;

//		$reference = 41616680;
		$orderId = $this->payvalida->getOrderIdByReference($reference);
		
		$url .= $this->buildUrl($orderId);
		
		if ($orderId !== false) {
			$response = $this->curl('GET', $url);
			$data['haypayvalida'] = ($response->CODE === '0000');
			$data['payvalida'] = (array)$response->DATA;
		} else {
			$data['haypayvalida'] = false;
			$data['payvalida'] = [];
		}
		
		unset($data['payvalida']['checkout']);
		unset($data['payvalida']['TRANSACTION']);
		
		$data['movimientos'] = $this->payvalida->getMovimientoByOrderId($orderId);
		
		if ($data['movimientos'] !== false) {
			$logs = $this->curl('POST', SOLVENTA_NOTIFICACIONES_URL . 'api/payvalidaLogs', ['pv_po_id' => $data['movimientos']['pv_order_id']]);
			$data['logs'] = $logs->data;
		} else {
			$data['logs'] = false;
		}
		
		$cliente = $this->cliente->getClienteById($data['movimientos']['id_cliente']);
//		$cliente = $this->cliente->getClienteById(1295);
		
		if ($cliente !== 0) {
			$data['cliente'] = $cliente[0];
		} else {
			$data['cliente'] = [];
		}
		
		
		$data['title'] = 'Payvalida validador';
		$layout = new Layout('payvalida/validador', $data);
		$layout->viewLayout();
		
//		$data['title'] = 'Payvalida validador';
//		$data['heading'] = 'Validador';
//		$this->load->view('layouts/adminLTE__header', $data);
//		$this->load->view('payvalida/validador', $data);
//		$this->load->view('layouts/adminLTE__footer', $data);
		
	}
	
	public function buildUrl($orderId)
	{
		$checksum = hash('SHA512', $orderId . PAYVALIDA_MERCHANT . PAYVALIDA_FIXED_HASH);
		$query = [
			'merchant' => PAYVALIDA_MERCHANT,
			'checksum' => $checksum,
		];
		
		return $orderId . '?' . http_build_query($query);
	}
	
	private function curl($method, $url, $data = [])
	{
		$a = json_encode($data, true);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => $a,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json"
			),
		));
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			echo 'cURL Error #:' . $err;
			die;
		}
		
		
		return json_decode($response);
	}
}
