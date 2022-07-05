<?php

namespace payvalida;

require_once APPPATH . 'libraries/payvalida/payment/payvalidaPayment.php';

use function get_instance;
use const PAYVALIDA_MERCHANT;

class payvalidaRequest
{
	const OPERATION_REGISTER = 'register';
	const OPERATION_UPDATE = 'update';
	
	protected $CI;
	
	protected $id;
	protected $type;
	protected $cliente;
	protected $orderId;
	
	protected $operation;
	protected $paymentMethod;
	
	public $merchant;
	public $email;
	public $country;
	public $order;
	public $money;
	public $amount;
	public $description;
	public $method;
	public $recurrent;
	public $expiration;
	public $iva;
	public $checksum;
	public $user_di;
	public $user_name;
	public $redirect_timeout;
	
	/**
	 * @param $id
	 * @param $method | metodo de pago
	 * @param $tipo | tipo de pago C = cuota, A = acuerdo, T = total
	 */
	public function __construct($id, $method, $type)
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Credito_model', 'credito', true);
		$this->CI->load->model('cliente_model', 'cliente', true);
		$this->paymentMethod = new \payvalidaPayment($method);
		
		$this->id = $id;
		$this->type = $type;
		
		$this->generate();
	}
	
	/**
	 * Genera todos los valores necesarios para el request
	 *
	 * @return void
	 */
	protected function generate()
	{
		$this->setTipoPagoInfo();
		
		$this->email = $this->getPersonalEmail();
		$this->country = $this->paymentMethod->getCountryCode();
		$this->order = $this->id . $this->type . rand(10000, 99999);
		$this->money = $this->paymentMethod->getCurrency();
		$this->method = $this->paymentMethod->getMethod();
		$this->recurrent = false;
		
		$date = strtotime("+" . PAYVALIDA_EXPIRATION_DATE . " day");
		$this->expiration = date('d/m/Y', $date);
		$this->iva = '0';
		
		$this->user_di = $this->cliente['documento'];
		$this->redirect_timeout = "";
	}
	
	/**
	 * @return void
	 */
	private function setTipoPagoInfo()
	{
		if ($this->type == 'C') {
			$this->setTipoPagoCuota();
		}
		
		if ($this->type == 'A') {
			$this->setTipoPagoAcuerdo();
		}
		
		if ($this->type == 'T') {
			$this->setTipoPagoTotal();
		}
	}
	
	/**
	 * Setea la info necesaria para la cuota
	 */
	private function setTipoPagoCuota()
	{
		$creditoDetalle = $this->CI->credito->getCreditoByCreditoDetalleId($this->id);
		if (empty($creditoDetalle)) {
			throw new \InvalidArgumentException("Cuota no encontrada");
		}
		
		$this->cliente = $this->CI->cliente->getClienteById($creditoDetalle[0]['id_cliente'])[0];
		$this->amount = $creditoDetalle[0]['monto_cobrar'];
		$this->description = 'pago de cuota';
	}
	
	
	/**
	 * Setea la info necesaria para el acuerdo
	 */
	private function setTipoPagoAcuerdo()
	{
		$acuerdo = $this->CI->credito->getPayvalidaAcuerdoPayment($this->id);
		if (empty($acuerdo)) {
			throw new \InvalidArgumentException("Acuerdo no encontrado");
		}
		
		$this->cliente = $this->CI->cliente->getClienteById($acuerdo[0]['id_cliente'])[0];
		$this->amount =  $acuerdo[0]['monto'];
		$this->description = 'pago de acuerdo';
	}
	
	/**
	 * Setea la info necesaria para el pago total de la deuda
	 */
	private function setTipoPagoTotal()
	{
		$total = $this->CI->credito->getPayvalidaTotalPayment($this->id);

		if (empty($total)) {
			throw new \InvalidArgumentException("Credito no encontrado");
		}

		$this->cliente = $this->CI->cliente->getClienteById($this->id)[0];
		$this->amount =  $total[0]['deuda'];
		$this->description = 'pago total';
	}
	
	/**
	 * Obtiene el email personal del cliente
	 *
	 * @return mixed|string
	 */
	private function getPersonalEmail()
	{
		$emails = $this->CI->cliente->get_agenda_mail([
			"id_cliente" => $this->cliente['id'],
			"fuente" => "PERSONAL"
		]);
		
		$email = '';
		if (isset($emails[0])) {
			$email = trim($emails[0]['cuenta']);
		}
		return $email;
	}
	
	/**
	 * Obtiene un array con los datos necesarios para el envio a payvalida
	 *
	 * @return array
	 */
	public function toArray()
	{
		$this->checksum = hash('SHA512', $this->email . $this->country . $this->order . $this->money . $this->amount . PAYVALIDA_FIXED_HASH);
		
		return [
			"merchant" => PAYVALIDA_MERCHANT,
			"email" => $this->email,
			"country" => $this->country,
			"order" => $this->order,
			"money" => $this->money,
			"amount" => $this->amount,
			"description" => $this->description,
			"method" => $this->method ,
			"recurrent" => $this->recurrent,
			"expiration" => $this->expiration,
			"iva" => $this->iva,
			"checksum" => $this->checksum,
			"user_di" => $this->user_di,
			"user_name" => $this->user_name,
			"redirect_timeout" => $this->redirect_timeout
		];
	}
	
	/**
	 * Guarda un regisstro del envio a payvalida
	 *
	 * @return mixed
	 */
	public function savePayvalidaMovimientos()
	{
		$this->amount = (string) round($this->amount) . '.00';
		$date = \DateTime::createFromFormat('d/m/Y', $this->expiration);

		return $this->CI->credito->savePayvalidaMovimientos(
			$this->operation,
			$this->type,
			$this->id,
			$this->cliente['id'],
			$this->order,
			$this->amount,
			$this->method,
			$date->format('Y-m-d'),
			json_encode($this->toArray())
		);
	}
	
	
}
