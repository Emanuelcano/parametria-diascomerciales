<?php
class Modulos_model extends BaseModel {

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('gestion', TRUE);
    }

    public function get_active_modulos()
    {
            $query = $this->db->get_where('modulos', 'id_estado = 1');
            //$error = $this->db->error();
            return $query->result();
    }
    
}