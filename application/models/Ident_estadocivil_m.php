<?php
class Ident_estadocivil_m extends BaseModel {

    protected $_table_name = 'ident_estadocivil';
    protected $_primary_key = 'id_EstadoCivil';
    protected $_order_by = 'id_EstadoCivil';

    public function __construct() {
        parent::__construct();
    }


    public function get_estadocivil(){

        $this->db->select('r.*');  
        $query = $this->db->get_where('ident_estadocivil r');
        return $query->result();
    }
    public function get_estadociviltodo(){

        $this->db->select('*');  
        $this->db->join('estados es','e.id_estado_EstadoCivil = es.id_estado', 'left');
         $query = $this->db->get_where('ident_estadocivil e');
        return $query->result();

        
    }

    public function update_all($data= false){
        $this->db->where('id_estado <', 3);
        $this->db->update('id_EstadoCivil', $data);

    }
    public function update_uno($data= false){

        $this->db->set('id_estado', 3);
        $this->db->where('id_id_EstadoCivil', $data['id_EstadoCivil']);
        $this->db->update('id_EstadoCivil');


    }

}