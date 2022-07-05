<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SolicitudPasos_model extends CI_Model {

   public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('gestion', TRUE);       
    }

    public function getSolicitudPaso($id_paso){       
        $this->load->database('gestion',TRUE);
        $this->db->select('*');
        $this->db->from('gestion.pasos_solicitud');
        $this->db->where('paso', $id_paso);        
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }   
  
    
}
