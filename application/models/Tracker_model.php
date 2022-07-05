<?php 
class Tracker_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		// LOAD SCHEMA
		$this->db = $this->load->database('gestion', TRUE);
	}

	public function search($params=array(), $limit = NULL, $offset = NULL)
	{
		$this->db = $this->load->database('gestion', TRUE);
		$this->db->select("*")->from("track_gestion");
		if(isset($params['id'])){ $this->db->where('id',$params['id']);}
		if(isset($params['track_gestion.id'])){ $this->db->where('track_gestion.id',$params['track_gestion.id']);}
		if(isset($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
		if(isset($params['id_credito'])){ $this->db->where('id_credito',$params['id_credito']);}
		if(isset($params['id_cliente'])){ $this->db->where('id_cliente',$params['id_cliente']);}
		if(isset($params['fecha'])){ $this->db->where('fecha',$params['fecha']);}
		if(isset($params['id_operador'])){ $this->db->where('id_operador',$params['id_operador']);}
		if(isset($params['etiqueta'])){ $this->db->where('etiqueta',$params['etiqueta']);}
		$this->db->join('botones_operador', 'botones_operador.id = track_gestion.id_tipo_gestion', 'left');
		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}

		if(isset($limit) && isset($offset))
        {
            $this->db->limit($offset, $limit);
        }else if(isset($limit) && !isset($offset))
        {
            $this->db->limit($limit);
        }

		$query = $this->db->get();
        //echo $sql = $this->db->last_query();echo "<br>";

		return $query->result_array();
	}

	

	public function edit($id, $data)
	{
		die(__METHOD__." NOT IMPLEMENTED");
	}

	public function save($data = array())
	{
		$result = $this->db->insert('track_gestion',$data);
		$insert_id = $this->db->insert_id();
		$this->save_last_track($data);
		return $insert_id;
	}

	public function order($orders)
	{
		foreach ($orders as $index => $order) 
		{
			$ord = (isset($order[1]))? $order[1]: 'DESC';
			$this->db->order_by($order[0], $ord);
		}
	}



	/* Autor:*/
    /* Fecha:*/
    /* INICIO TRACKEO PARA AUDITORIA ONLINE*/
    public function insert_track_auditoria_online($params)
    {
        $this->db->insert('gestion.auditoria_interna_online', $params);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    

    //actualizar
    function set_off_all_operation($id, $data = array("bstatus"=>0)){
        $this->db->where('id_operador', $id);
        $this->db->update('gestion.auditoria_interna_online', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }
    }
	/*** Se verifica que el operador activo está en la tabla auditoria_online  ***/
	function exists_operador($id_operador) {
		$this->db->select("count(1) as existe");
		$this->db->from("gestion.auditoria_interna_online");
		$this->db->where('id_operador', $id_operador);
		$resultado = $this->db->get()->result_array()[0]['existe'];
        if ($resultado > 0) {
			return true;
        }
        else{
            return false;
		}
	}
	
	/*** Se actualiza el track del operador activo en la tabla auditoria_interna_online  ***/
	function update_track_auditoria_online($data, $id_operador) {
		$this->db->where( 'id_operador', $id_operador);
		$this->db->update('gestion.auditoria_interna_online', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }
	}

	/*** Se verifica si existe un Auditor activo en la tabla auditoria_online  ***/
	function exists_auditor() {
		$this->db->select("count(1) as existe");
		$this->db->from("gestion.auditoria_interna_online");
		$this->db->where('bstatus', 2);
		$resultado = $this->db->get()->result_array()[0]['existe'];
        if ($resultado > 0) {
			return true;
        }
        else{
            return false;
		}
	}

    /* FIN TRACKEO PARA AUDITORIA ONLINE*/

	public function save_last_track($data = array())
	{
		$this->db = $this->load->database('solicitudes', TRUE);
		$replace = $this->db->replace('solicitud_ultima_gestion', $data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	public function save_track_extencion_gestion($data = array())
	{
		$this->db = $this->load->database('gestion', TRUE);
		$this->db->insert('track_gestion_extensiones', $data);
		$insert_id = $this->db->insert_id();

		if($this->db->affected_rows() < 1){
            return -1;
        } else{
            return $this->db->affected_rows();
        }
	}
	
	/**
	 * Comprueba si la solicitud ya tiene algun trackeo
	 * 
	 * @param $idSolicitud
	 *
	 * @return bool
	 */
	public function checkSolicitudHasTrack($idSolicitud)
	{
		$this->db_solicitudes = $this->load->database('solicitudes', TRUE);
		$query = $this->db_solicitudes->select('*')
			->from('solicitud_ultima_gestion')
			->where('id_solicitud', $idSolicitud)
			->get()->result_array();
		
		return (count($query) > 0);
	}
	
	
	/**
	 *  Comprueba si una solicitud tiene track en el dia
	 * 
	 * @param $idSolicitud
	 *
	 * @return bool
	 */
	public function checkSolicitudHasTrackToday($idSolicitud)
	{
		$this->db = $this->load->database('gestion', TRUE);
		$query = $this->db->select('*')
			->from('track_gestion')
			->where('fecha = current_date()')
			->where('id_solicitud', $idSolicitud)
			->get()->result_array();

		return (count($query) > 0);
	}

}
