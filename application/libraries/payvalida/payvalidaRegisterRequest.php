<?php

namespace payvalida;

require_once APPPATH . 'libraries/payvalida/payvalidaRequest.php';

class payvalidaRegisterRequest extends payvalidaRequest
{
	/**
	 * @param $creditoDetalleId
	 * @param $method | methodo de pago
	 * @param $tipo | tipo de pago C = cuota, A = acuerdo, T = total
	 */
	public function __construct($creditoDetalleId, $method, $tipo)
	{
		parent::__construct($creditoDetalleId, $method, $tipo);
		$this->operation = self::OPERATION_REGISTER;
	}
}
