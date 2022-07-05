<?php
class Estilos_m extends BaseModel {

    protected $_table_name = 'estilos';
    protected $_primary_key = 'id_estilos';
    protected $_order_by = 'id_estilos';

    public function __construct() {
        parent::__construct();
    }


    public function get_estilo(){

        $this->db->select('r.*');  
        $query = $this->db->get_where('estilos r', 'r.id_estado=1');
        return $query->result();
    }
    public function get_estilotodo(){

        $this->db->select('*');  
        $this->db->join('estados es','e.id_estado = es.id_estado', 'left');
         $query = $this->db->get_where('estilos e');
        return $query->result();

        
    }

    public function update_all($data= false){
        $this->db->where('id_estado <', 3);
        $this->db->update('estilos', $data);

    }
    public function update_uno($data= false){

        $this->db->set('id_estado', 3);
        $this->db->where('id_estilos', $data['estilos']);
        $this->db->update('estilos');


    }

}