<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DebitosAutomaticos_m extends CI_Model {

    protected $_table_name = 'solicitud';
    protected $_primary_key = 'id';
    protected $_order_by = 'id';

    public function __construct() {
        parent::__construct();
        $this->db_gestion = $this->load->database('gestion', TRUE);
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_parametria  = $this->load->database('parametria', TRUE);
        $this->db_usuarios_solventa = $this->load->database('usuarios_solventa',TRUE);
    }

    public function insert($table, $data) {
        $this->db->insert($table, $data);
        //var_dump($this->db->last_query());
        //var_dump($this->db->error());
        return $this->db->insert_id();
    }
    public function update($table,$where, $data) {
        $this->db->where($where);
        $this->db->update($table, $data);
        //var_dump($this->db->last_query());
        //var_dump($this->db->error());
        return $this->db->affected_rows();
    }
    public function select($table,$where) {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($where);
        $query = $this->db->get();
        return $query->row();
    }
}