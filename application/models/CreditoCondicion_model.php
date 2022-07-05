<?php
class CreditoCondicion_model extends CI_Model {

    public function __construct(){        
        $this->load->model('Credito_model');

        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
    }

    public function search($params=[])
    {
        $this->db = $this->load->database('maestro',TRUE);
        $this->db->select('*');
        $this->db->from('credito_condicion');

        if(!empty($params['id_credito'])){ $this->db->where('id_credito',trim($params['id_credito'])); }

        $query = $this->db->get();

        return $query->result_array();
    }

    public function update($params=[], $data){
        if(isset($params['id_credito'])){$this->db->where('id_credito',$params['id_credito']);}
        $update = $this->db->update('maestro.credito_condicion', $data);
        $update = $this->db->affected_rows();
        return $update;
    }
}
?>
