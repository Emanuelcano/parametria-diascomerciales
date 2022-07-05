<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Payvalida_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('dispatcher');
        $this->db_maestro = $this->load->database('maestro', true);
    }
	
	public function getOrderIdByReference($reference)
	{
		$result = $this->db_maestro->select('order_id')
			->from('payvalida_movimientos')
			->where('referencia', $reference)
			->get()->row_array();
		
		return $result ? $result['order_id'] : false;
	}
	
	public function getReferenciaByOrderId($orderId)
	{
		$result = $this->db_maestro->select('referencia')
			->from('payvalida_movimientos')
			->where('order_id', $orderId)
			->get()->row_array();
		
		return $result ? $result['referencia'] : false;
	}
	
	public function getMovimientoByOrderId($orderId)
	{
		$result = $this->db_maestro->select('*')
			->from('payvalida_movimientos')
			->where('order_id', $orderId)
			->get()->row_array();
		
		return $result ? $result : false;
	}
}
