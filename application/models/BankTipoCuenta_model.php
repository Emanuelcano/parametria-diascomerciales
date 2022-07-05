<?php
/**
 * 
 */
class BankTipoCuenta_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('parametria', TRUE);
	}

	public function search($params=[])
	{
		$this->db->select("*")->from("bank_tipocuenta");
		if(isset($params['id_tipocuenta'])){ $this->db->where('id_TipoCuenta',$params['id_tipocuenta']);}
		if(isset($params['codigo_tipocuenta'])){ $this->db->where('codigo_TipoCuenta',$params['codigo_tipocuenta']);}
		if(isset($params['nombre_tipocuenta'])){ $this->db->where('Nombre_TipoCuenta',$params['nombre_tipocuenta']);}
		if(isset($params['id_estado_tipocuenta'])){ $this->db->where('id_estado_TipoCuenta',$params['id_estado_tipocuenta']);}

		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}

		$query = $this->db->get();
        //echo $sql = $this->db->last_query();echo "<br>";

		return $query->result_array();
	}
}