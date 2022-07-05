<?php

class payvalidaPayment
{
	protected $countryCode;
	protected $currency;
	protected $method;
	
	private $CI;
	
	private $paymentMethods = [];

	/**
	 * @param $method
	 */
	public function __construct($method)
	{
		$this->CI =& get_instance();
		$this->CI->load->model('Credito_model', 'credito', true);
		
		if ($this->checkExistenceMethod($method)) {
			$this->method = $method;
			$this->filler();
		} else {
			throw new \InvalidArgumentException("El metodo de pago $method no es un metodo valido");
		}
	}
	
	/**
	 * Llena los valores correspondientes segun el metodo de pago
	 *
	 * @return void
	 */
	private function filler()
	{
		$data = $this->paymentMethods[$this->method];
		$this->countryCode = $data['countryCode'];
		$this->currency = $data['currency'];
	}
	
	/**
	 * Comprueba que sea un metodo de pago permitido
	 *
	 * @param $method
	 *
	 * @return bool
	 */
	private function checkExistenceMethod($method): bool
	{
		$paymentMethods = $this->CI->credito->getPayvalidaEnabledPaymentMethods();

		foreach ($paymentMethods as $paymentMethod) {
			$this->paymentMethods[$paymentMethod['api_name']] = [
				'countryCode' => (int) $paymentMethod['country_code'],
				'currency' => $paymentMethod['currency'],
			];
		}
		
		return isset($this->paymentMethods[$method]);
	}
	
	/**
	 * @return mixed
	 */
	public function getCountryCode()
	{
		return $this->countryCode;
	}
	
	/**
	 * @return mixed
	 */
	public function getCurrency()
	{
		return $this->currency;
	}
	
	/**
	 * @return mixed
	 */
	public function getMethod()
	{
		return $this->method;
	}
	
}
