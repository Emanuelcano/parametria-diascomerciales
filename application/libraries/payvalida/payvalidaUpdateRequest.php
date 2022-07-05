<?php

namespace payvalida;

require_once APPPATH . 'libraries/payvalida/payvalidaRequest.php';

class payvalidaUpdateRequest extends payvalidaRequest
{
	protected $orderId;
	
	/**
	 * @param $orderId
	 * @param $method | metodo de pago
	 * @param $tipo | tipo de pago C = cuota, A = acuerdo, T = total
	 */
	public function __construct($orderId, $method, $tipo)
	{
		$this->orderId = $orderId;
		list($id, $aux) = explode('C', $orderId);
		parent::__construct($id, $method, $tipo);
		$this->operation = self::OPERATION_UPDATE;
	}
	
	/**
	 * Sobre escribo el metodo para poder insertarle el orderId y que no le genere uno nuevo
	 *
	 * @return void
	 */
	public function generate()
	{
		parent::generate();
		
		$this->order = $this->orderId;
	}
	
	public function updateByArray($data)
	{
		foreach ($data as $k => $datum) {
			$this->$k = $datum;
		}
	}
}
