<?php
class Gestion_model extends BaseModel
//class Reportes_model extends CI_Model
{
	public $columns_solicitud = array('*');
	public function __construct()
	{
		parent::__construct();
	}

	public function solicitudes($params = [])
	{
		$this->set_columns_solicitud(['solicitud.id','solicitud.fecha_alta','solicitud.fecha_ultima_actividad','solicitud.documento','solicitud.nombres','solicitud.apellidos','solicitud.estado','solicitud.respuesta_analisis','solicitud.tipo_solicitud','solicitud.operador_asignado','operadores.nombre_apellido operador_nombre_apellido']);
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select($this->columns_solicitud)->from("solicitud");
        $this->db->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado', 'LEFT');
		$this->set_filters($params);
		$query = $this->db->get();
		
		return $query->result_array();
	}

	public function set_filters($params)
	{
		if(isset($params['tipo_solicitud'])){ $this->db->where('tipo_solicitud',$params['tipo_solicitud']); }
		if(isset($params['estado'])){ $this->db->where_in('solicitud.estado',$params['estado']); }
		if(isset($params['canal'])){ $this->db->where('canal',$params['canal']); }
		if(isset($params['desde'])){ $this->db->where('fecha_alta >=',$params['desde']); }
		if(isset($params['hasta'])){ $this->db->where('fecha_alta <=',$params['hasta']); }
		if(isset($params['operador_asignado'])){ $this->db->where_in('operador_asignado',$params['operador_asignado']); }
		if(isset($params['respuesta_analisis'])){ $this->db->where_in('respuesta_analisis',$params['respuesta_analisis']); }
	
	}


	public function set_columns_solicitud($columns)
	{
		$this->columns_solicitud = $columns;
	}
}