<?php
class NivelesClientes_model extends CI_Model {
    
    public function __construct(){        
        $this->load->database();
        $this->load->helper('formato_helper');
        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
    }
    
    public function findOneBy($params = [], $fields = [])
    {	
    	$this->db->select($fields);
    	$this->db->from('maestro.niveles_clientes');

        foreach($params as $key => $param){
        	$this->db->where($key , $param);
        }

        $query = $this->db->get();

        return $query->result();
    }
    public function insert($data = [])
    {
        
    }
    public function update($param = [], $data = [])
    {
        
    }

}

?>
