<?php
class Quota_model extends CI_Model {
    
    public function __construct()
    {  
        parent::__construct();      
    }
    
    public function search($params)
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('*');
        $this->db->from('credito_detalle');

        if(!empty($params['id_credito'])){ $this->db->where('id_credito',$params['id_credito']);}

        $query = $this->db->get();

        return $query->result_array();
    }
}
?>
