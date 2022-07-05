<?php
class Ident_nivelestudio_m extends BaseModel {

    protected $_table_name = 'ident_nivelestudio';
    protected $_primary_key = 'id_NivelEstudio';
    protected $_order_by = 'id_NivelEstudio';

    public function __construct() {
        parent::__construct();
    }


    public function get_nivelestudio(){

        $this->db->select('r.*');  
        $query = $this->db->get_where('ident_nivelestudio r');
        return $query->result();
    }
    public function get_nivelestudiotodo(){

        $this->db->select('*');  
        $this->db->join('estados es','e.id_estado_NivelEstudio = es.id_estado', 'left');
         $query = $this->db->get_where('ident_nivelestudio e');
        return $query->result();

        
    }

    public function update_all($data= false){
        $this->db->where('id_estado <', 3);
        $this->db->update('id_NivelEstudio', $data);

    }
    public function update_uno($data= false){

        $this->db->set('id_estado', 3);
        $this->db->where('id_id_NivelEstudio', $data['id_NivelEstudio']);
        $this->db->update('id_NivelEstudio');


    }

}