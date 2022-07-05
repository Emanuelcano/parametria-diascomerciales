<?php 
class Operaciones_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		// LOAD SCHEMA
		$this->db = $this->load->database('gestion', TRUE);
		$this->dbt= $this->load->database('telefonia', TRUE);
	}

	public function get_by($params = array())
	{
		$this->db->select("botones_operador.*")->from("botones_operador");
		if(isset($params['id'])){ $this->db->where('id',$params['id']);}
		if(isset($params['idgrupo_respuesta'])){ $this->db->where('idgrupo_respuesta',$params['idgrupo_respuesta']);}
		if(isset($params['estado'])){ $this->db->where('estado',$params['estado']);}
		if(isset($params['idtipo_operador'])){ $this->db->where('relaciones.idtipo_operador',$params['idtipo_operador']);}
		if(isset($params['order'])){ $this->order($params['order']);}

		$query = $this->db->get();

        //echo $sql = $this->db->last_query();die;

		return $query->result_array();
	}

	public function search($params = array())
	{
		$this->db->select("botones_operador.*")->from("botones_operador");
		$this->db->join('relacion_botones_operador_tipo_operador relaciones', 'relaciones.idboton = botones_operador.id' );
		if(isset($params['id'])){ $this->db->where('id',$params['id']);}
		if(isset($params['botones_operador.idgrupo_respuesta'])){ $this->db->where('botones_operador.idgrupo_respuesta',$params['botones_operador.idgrupo_respuesta']);}
		if(isset($params['estado'])){ $this->db->where('estado',$params['estado']);}
		if(isset($params['idtipo_operador'])){ $this->db->where('relaciones.idtipo_operador',$params['idtipo_operador']);}
		if(isset($params['order'])){ $this->order($params['order']);}

		$query = $this->db->get();

        //echo $sql = $this->db->last_query();die;

		return $query->result_array();
	}
	
	public function search_reasons($params=array())
	{
		$this->db->select("*")->from("detalle_respuestas");
		if(isset($params['iddetalle_respuesta'])){ $this->db->where('iddetalle_respuesta',$params['iddetalle_respuesta']);}
		if(isset($params['idgrupo_respuestas'])){ $this->db->where('idgrupo_respuestas',$params['idgrupo_respuestas']);}
		if(isset($params['denominacion'])){ $this->db->where('denominacion',$params['denominacion']);}
		if(isset($params['order'])){ $this->order($params['order']);}

		$query = $this->db->get();
		return $query->result_array();
	}

	public function edit($id, $data)
	{
		die(__METHOD__." NOT IMPLEMENTED");
	}

	public function save($data = array())
	{
		$result = $this->db->insert('botones_operador',$data);

		return $result;
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