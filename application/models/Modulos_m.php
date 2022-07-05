<?php
class Modulos_m extends BaseModel {

    protected $_table_name = 'parametria.modulos';
    protected $_primary_key = 'id';
    protected $_order_by = 'id';

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('parametria', TRUE);
    }

   
    public function get_modulo(){

        $this->db->select('r.*');  
        $query = $this->db->get_where('parametria.modulos r', 'r.id_estado =1');

        return $query->result();
    }
}
