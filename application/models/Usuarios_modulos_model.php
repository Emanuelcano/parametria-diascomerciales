<?php
class Usuarios_modulos_model extends BaseModel {

    public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('gestion', TRUE);
    }

    public function asignar_modulos($data)
    {
        $this->db->insert( 'usuarios_modulos' ,$data);  
        return $this->db->insert_id();
    }
    public function get_modulos_usuario($usuario)
    {
        $query = $this->db->get_where( 'usuarios_modulos' ,'id_usuario = ' .$usuario);  
        return $query->result();
    }
    public function get_asignado($usuario, $modulo)
    {
        $query = $this->db->get_where( 'usuarios_modulos' ,'id_usuario = ' .$usuario .' AND id_modulo = ' .$modulo);  
        return $query->result();
    }

    public function delete_asignaciones($usuario)
    {
        $query = $this->db->delete( 'usuarios_modulos' ,'id_usuario = ' .$usuario );  
        return $query;
    }

    public function get_modulos_usuario_nombre($usuario)
    {
        $this->db->select('modulos.nombre');
		$this->db->from('usuarios_modulos');
		$this->db->join('modulos', 'usuarios_modulos.id_modulo = modulos.id');
		$this->db->where('id_usuario', $usuario); 
        
        $query = $this->db->get();
		$nombre_modulos = $query->result();
        return $nombre_modulos;
    }
}