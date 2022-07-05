<?php
class Tel_codigoarea_m extends BaseModel {

    protected $_table_name = 'tel_codigoarea';
    protected $_primary_key = 'id_ciudad_tel';
    protected $_order_by = 'id_ciudad_tel';

    public function __construct() {
        parent::__construct();
    }


    public function get_tel_codigoarea(){

        $this->db->select('r.*');  
        $query = $this->db->get_where('ident_estadocivil r');
        return $query->result();
    }
    public function get_tel_codigoareatodo(){

        $this->db->select('*');  
        $this->db->join('estados es','e.id_estado_EstadoCivil = es.id_estado', 'left');
         $query = $this->db->get_where('ident_estadocivil e');
        return $query->result();

        
    }

    public function update_all($data= false){
        $this->db->where('id_estado <', 3);
        $this->db->update('id_ciudad_tel', $data);

    }
    public function update_uno($data= false){

        $this->db->set('id_estado', 3);
        $this->db->where('id_ciudad_tel', $data['id_ciudad_tel']);
        $this->db->update('id_ciudad_tel');


    }

}