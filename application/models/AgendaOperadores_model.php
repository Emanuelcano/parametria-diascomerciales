<?php
class AgendaOperadores_model extends CI_Model {
    
    public function __construct(){        
        $this->load->database();
        $this->load->helper('formato_helper');
        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
    }
    public function getAgendaOperadores($params)
    {
        $this->db->select('*');
        $this->db->from('gestion.agenda_operadores ');
        $this->db->where(['id_operador'=>$params['id_operador']]);
        (isset($params['id_solicitud'])) ? $this->db->where(['id_solicitud'=>$params['id_solicitud']]) : "";
        $this->db->order_by('fecha_hora_llamar', 'asc');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    public function deleteAgendaOperador($params){
        $query = $this->db->delete('gestion.agenda_operadores',array('id'=>$params['id']));
        return $this->db->affected_rows();;
    }
}

?>
