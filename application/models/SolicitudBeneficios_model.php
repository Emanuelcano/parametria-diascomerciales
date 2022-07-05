<?php
/**
 * 
 */
class SolicitudBeneficios_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->db = $this->load->database('solicitudes', TRUE);
	}

	public function search($params=[])
	{
		$this->db->select("*")->from("solicitud_beneficios");
		if(isset($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
		if(isset($params['monto_maximo'])){ $this->db->where('monto_maximo',$params['monto_maximo']);}
		if(isset($params['plazo_maximo'])){ $this->db->where('plazo_maximo',$params['plazo_maximo']);}

		if(isset($params['order'])){ $this->order($params['order']);}
		if(isset($params['limit'])){ $this->db->limit($params['limit']);}

		$query = $this->db->get();
        //echo $sql = $this->db->last_query();echo "<br>";

		return $query->result_array();
	}

	public function update($params=[], $data){
		
		foreach($params as $key => $param){
			$this->db->where($key,$param);
		}
		if(count($params) > 0 ){
 			$update = $this->db->update('solicitudes.solicitud_beneficios', $data);
		}
       
        $update = $this->db->affected_rows();
        return $update;
	}
}