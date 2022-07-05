<?php 
class Operator_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		// LOAD SCHEMA
		$this->db = $this->load->database('gestion', TRUE);
	}

	public function search($params=array())
	{
		$this->db->select("*")->from("operadores");
		if(isset($params['idoperador'])){ $this->db->where('idoperador',$params['idoperador']);}
		if(isset($params['id_usuario'])){ $this->db->where('id_usuario',$params['id_usuario']);}
		if(isset($params['estado'])){ $this->db->where('estado',$params['estado']);}
		if(isset($params['hora_ingreso'])){ $this->db->like('hora_ingreso',$params['hora_ingreso']);}
		if(isset($params['hora_salida'])){ $this->db->like('hora_salida',$params['hora_salida']);}
		
		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}

		$query = $this->db->get();
		return $query->result_array();
	}

	public function getOpeBySolicitud($param=[])
	{
        $this->db->select('op.idoperador, op.nombre_apellido, op.avatar');
        $this->db->from('operadores op');
        $this->db->join('solicitudes.solicitud sol', 'op.idoperador = sol.operador_asignado');
        $query = $this->db->where('sol.id',$param['id']);
        $query = $this->db->get();

        return $query->result_array();
    }

	public function edit($id, $data)
	{
		die(__METHOD__." NOT IMPLEMENTED");
	}

	public function save($data = array(), $model = 'operadores')
	{
		$result = $this->db->insert($model,$data);
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