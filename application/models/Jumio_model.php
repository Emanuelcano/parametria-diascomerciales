<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Jumio_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function search($params=array())
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select("*")->from("jumio_scans");
		if(isset($params['id_solicitud']) && !empty($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
		$this->db->where_in('source',['WEB_CAM','WEB_UPLOAD']);
	 	if(isset($params['order'])){ $this->order($params['order']);}
		
		$query = $this->db->get();
        //echo $sql = $this->db->last_query();die;
		
		return $query->result_array();
	}

	public function search_eid($params=array())
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select("id, id_solicitud, fecha, fecha_fin, respuesta_supervivencia, respuesta_match, id_video scanReference, ruta_response, 'eid' as validador, status_verification as status_session")->from("eid_scans");
		if(isset($params['id_solicitud']) && !empty($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
	 	if(isset($params['order'])){ $this->order($params['order']);}
		
		$query = $this->db->get();
        //echo $sql = $this->db->last_query();die;
		
		return $query->result_array();
	}

	public function search_veriff($params=array())
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select("id, id_solicitud, fecha, fecha_fin, respuesta_identificacion, respuesta_supervivencia, response_code, respuesta_match, attempts scanReference, ruta_response, 'veriff' as validador,  status_session")->from("veriff_scan");
		
		if(isset($params['id_solicitud']) && !empty($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
	 	if(isset($params['order'])){ $this->order($params['order']);}
		
		$query = $this->db->get();
        //echo $sql = $this->db->last_query();die;
		
		return $query->result_array();
	}

	public function search_meta($params=array())
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$this->db->select("id, id_solicitud, fecha_inicio fecha, fecha_fin, identity respuesta_identificacion, liveness respuesta_supervivencia, facematch respuesta_match, session_id scanReference, ruta_response, 'meta' as validador, identity status_session")->from("meta_scan");
		
		if(isset($params['id_solicitud']) && !empty($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
	 	if(isset($params['order'])){ $this->order($params['order']);}
		
		$query = $this->db->get();
		
		return $query->result_array();
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
