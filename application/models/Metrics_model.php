<?php 
class Metrics_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		// LOAD SCHEMA
		$this->db = $this->load->database('parametria', TRUE);
	}

    public function get_ranges($params=[])
    {
		$this->db = $this->load->database('parametria', TRUE);
        $this->db->select('*');
        $this->db->from('an_tabla_rangos');
        if(isset($params['estado'])){ $this->db->where('estado', $params['estado']); }
        $query = $this->db->get();
        
        return $query->result_array();
    }

    public function getAnalisisFlex($param)
    {   
        $this->db->select($param['campo'].' as '. $param['tabla'].'_'.$param['campo']);
        $this->db->from($param['base'].'.'.$param['tabla']);
        $this->db->where('id_solicitud', $param['id_solicitud']);
        $this->db->order_by('id','desc');
        $query = $this->db->get();
        // var_dump($this->db->last_query());
        $res = $query->result_array();
        return $res;
    }

    //obtenemos las especificaciones de una variable local
    public function get_variable_global($param){

        
        $this->db->select('*');
        $this->db->from('variables_globales');
        if (isset($param['nombre_variable'])) { $this->db->where('nombre_variable', $param['nombre_variable']); }
        if (isset($param['contexto'])) { $this->db->where('contexto', $param['contexto']); }
 
        $query = $this->db->get();
        // var_dump($this->db->last_query());
        $res = $query->result();
        return $res;
    }


}