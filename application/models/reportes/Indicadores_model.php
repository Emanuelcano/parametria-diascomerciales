<?php
class Indicadores_model extends BaseModel
//class Reportes_model extends CI_Model
{
	public $columns = array('*');
	public function __construct()
	{
		parent::__construct();
	}

	public function search($params = [])
	{

		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select($this->columns)->from("solicitud");
		
		$this->_set_filters($params);
		$query = $this->db->get();
        echo $sql = $this->db->last_query();die;
		
		return $query->result_array();
	}
	
	private function _set_filters($params)
	{

		if(!empty($params))
		{
			if(isset($params['columns']))	{ $this->set_columns($params['columns']);}
			if(isset($params['literal']))	{ $this->equal($params['literal']);}
			if(isset($params['order']))		{ $this->order($params['order']);}
		}
	}

	public function indicadores_estados($desde, $hasta, $tipo_solicitud= null)
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select("estado, respuesta_analisis, tipo_solicitud, DATE_FORMAT(fecha_alta, '%Y-%m-%d') AS fecha_solicitud, count(*) AS cantidad")->from("solicitud");
		$this->db->where('DATE_FORMAT(fecha_alta, "%Y-%m-%d") >=', $desde);
		$this->db->where('DATE_FORMAT(fecha_alta, "%Y-%m-%d") <=', $hasta);
		//$this->db->where_not_in('estado', ['ANULADO']);

		if(isset($tipo_solicitud)){ $this->db->where('tipo_solicitud', $tipo_solicitud);}
		
		$this->db->group_by(['fecha_solicitud','estado','respuesta_analisis']);

		$query = $this->db->get();
		
		return $query->result_array();
	}

	public function indicadores_track_gestion_sin_gestion($desde, $hasta, $tipo_solicitud= null)
	{
		$this->db = $this->load->database('solicitudes', TRUE);

		$this->db->select("DATE_FORMAT(solicitud.fecha_alta, '%Y-%m-%d') AS fecha_solicitud, count(*) AS cantidad")->from("solicitud");
        //$this->db->join('gestion.track_gestion ', "gestion.track_gestion.id_solicitud = solicitud.id", 'LEFT');
        // Este filtro es para comprobar que la solicitud tuvo gestion dentro del rango de fecha
        $this->db->join('gestion.track_gestion ', "gestion.track_gestion.id_solicitud = solicitud.id AND gestion.track_gestion.fecha >='".$desde ."' 
        											AND gestion.track_gestion.fecha <= '".$hasta."'
        											AND id_operador !=0", 'LEFT');
		$this->db->where('DATE_FORMAT(solicitud.fecha_alta, "%Y-%m-%d") >=', $desde);
		$this->db->where('DATE_FORMAT(solicitud.fecha_alta, "%Y-%m-%d") <=', $hasta);
		$this->db->where('respuesta_analisis', 'APROBADO');
		$this->db->where('gestion.track_gestion.id', NULL, FALSE);
		if(isset($tipo_solicitud)){ $this->db->where('solicitud.tipo_solicitud', $tipo_solicitud); }
		$this->db->group_by(['fecha_solicitud']);
		$this->db->order_by('solicitud.fecha_alta', 'DESC');

		$query = $this->db->get();
       // echo $sql = $this->db->last_query();
		
		return $query->result_array();
	}

	public function indicadores_track_gestion_gestion_automatica($desde, $hasta, $tipo_solicitud= null)
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$sql = "SELECT DATE_FORMAT(solicitud.fecha_alta, '%Y-%m-%d') AS fecha_solicitud,
    				COUNT(*) AS cantidad
				FROM solicitud
	   			LEFT JOIN (SELECT *, MAX(id) AS maximo 
	       				   	FROM gestion.track_gestion 
	       				   	WHERE fecha >= '".$desde."' AND fecha <= '".$hasta."'
							GROUP BY id_solicitud
							HAVING id= max(id) AND observaciones = '[ANALISIS]' AND id_operador =0) track ON track.id_solicitud = solicitud.id 
				WHERE DATE_FORMAT(solicitud.fecha_alta, '%Y-%m-%d') >= '".$desde."'
	        		AND DATE_FORMAT(solicitud.fecha_alta, '%Y-%m-%d') <= '".$hasta."'
	        		AND respuesta_analisis = 'APROBADO'
	        		AND track.id_solicitud IS NOT NULL ";
		if(isset($tipo_solicitud)){ $sql .= "AND solicitud.tipo_solicitud = '".$tipo_solicitud."' "; }
		$sql .= "GROUP BY fecha_solicitud ";
		$sql .= "ORDER BY solicitud.fecha_alta DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function indicadores_solicitud_visado($desde, $hasta, $tipo_solicitud= null)
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select("visado,  DATE_FORMAT(fecha_alta, '%Y-%m-%d') AS fecha_solicitud, COUNT(*) AS cantidad")->from("solicitud");
		$this->db->join('solicitud_visado ', "solicitud_visado.id_solicitud = solicitud.id AND DATE_FORMAT(fecha_creacion, '%Y-%m-%d') >='".$desde ."' 
    											AND DATE_FORMAT(fecha_creacion, '%Y-%m-%d') <= '".$hasta."'", 'LEFT');
		$this->db->where('DATE_FORMAT(fecha_alta, "%Y-%m-%d") >=', $desde);
		$this->db->where('DATE_FORMAT(fecha_alta, "%Y-%m-%d") <=', $hasta);
		$this->db->where('estado', 'APROBADO');
		if(isset($tipo_solicitud)){ $this->db->where('tipo_solicitud', $tipo_solicitud);}
		$this->db->group_by(['fecha_solicitud', 'solicitud_visado.visado']);
		$this->db->order_by('solicitud.fecha_alta', 'DESC');

		$query = $this->db->get();
        //echo '<pre>';echo $sql = $this->db->last_query();echo '<pre>';;
		
		return $query->result_array();
	}

	public function get_tipo_solicitud($value = null)
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select($value)->from('solicitud');
		$this->db->group_by($value);
		$this->db->having('COUNT(*) > 1');
		$query = $this->db->get();
		return $query->result_array();
	
	}
	

	/***************************************************************************/
	// SETTERS
	/***************************************************************************/
	public function set_columns($columns)
	{
		$this->columns = $columns;
		return $this;
	}
}