<?php
/**
 * 
 */
class SolicitudDatosBancarios_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('solicitudes', TRUE);
	}

	public function search($params=[])
	{
		$this->db->select("*")->from("solicitud_datos_bancarios");
		if(isset($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
		if(isset($params['id_banco'])){ $this->db->where('id_banco',$params['id_banco']);}
		if(isset($params['id_tipo_cuenta'])){ $this->db->where('id_tipo_cuenta',$params['id_tipo_cuenta']);}
		if(isset($params['numero_cuenta'])){ $this->db->where('numero_cuenta',$params['numero_cuenta']);}
		if(isset($params['respuesta'])){ $this->db->where('respuesta',$params['respuesta']);}

		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}

		$query = $this->db->get();
        //echo $sql = $this->db->last_query();echo "<br>";

		return $query->result_array();
	}

	public function edit($id, $data)
    {
        $this->db->where('id', $id);
    	return $this->db->update('solicitud_datos_bancarios', $data);
       
    }

   	public function save($data = array())
	{
		$result = $this->db->insert('solicitud_datos_bancarios',$data);
		return $this->db->insert_id();
	}

	public function order($orders)
	{
		foreach ($orders as $index => $order) 
		{
			$ord = (isset($order[1]))? $order[1]: 'DESC';
			$this->db->order_by($order[0], $ord);
		}
	}

}