<?php

class Operaciones_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // LOAD SCHEMA
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->db_gestion = $this->load->database('gestion', TRUE);

    }
    
    //Trae la cantidad de Beneficiarios
    public function get_cantidad_beneficiarios() {

        $this->db_maestro->select('be.*');
        $query = $this->db_maestro->get_where('beneficiarios be');
        $cantidad = count($query->result());
        return $cantidad;
    }
    
    //Trae cantidad de gastos
    public function get_cantidad_gastos() {

        $this->db_maestro->select('g.*');
        $query = $this->db_maestro->get_where('gastos g');
        $cantidad = count($query->result());
        return $cantidad;
    }
    
    //Trae los nombres de los modulos por usuario de gastos
    public function get_modulos_usuario($usuario)
    {
        $this->db_gestion->select('usuarios_modulos.id_usuario, modulos.nombre');
        $this->db_gestion->from('usuarios_modulos');
        $this->db_gestion->join('modulos', 'usuarios_modulos.id_modulo = modulos.id','left');       
        $this->db_gestion->where('usuarios_modulos.id_usuario', $usuario);
        $query = $this->db_gestion->get();        
        return $query->result_array();        
    }
    
}