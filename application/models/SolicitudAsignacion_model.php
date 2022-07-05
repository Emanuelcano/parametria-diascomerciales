<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SolicitudAsignacion_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function search($params=array())
    {
        $fields = array('id', 'id_operador', 'asignados', 'fecha_control', 'verificados', 'validados', 'aprobados', 'rechazados', 'estado', 'update_at');
    
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->select($fields)->from("control_asignaciones");

        if(isset($params['id_operador']) && !empty($params['id_operador'])){ $this->db->where('id_operador',$params['id_operador']);}
        if(isset($params['fecha_control']) && !empty($params['fecha_control'])){ $this->db->where('fecha_control',$params['fecha_control']);}
      
        if(isset($params['order'])){ $this->order($params['order']);}
        
        $query = $this->db->get();
        //echo $sql = $this->db->last_query();die;
        
        return $query->result_array();
    }

    public function update($id, $data)
    {
        $this->db = $this->load->database('gestion',TRUE);
        $this->db->where('id',$id);
        $update = $this->db->update('control_asignaciones', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function updateBy($param, $data)
    {
        $this->db = $this->load->database('gestion',TRUE);
        if(isset($param['id_operador']))
            $this->db->where('id_operador',$param['id_operador']);
        if(isset($param['fecha_inicio']))
            $this->db->where("fecha_control >= '". $param['fecha_inicio']."'");
 
        $update = $this->db->update('control_asignaciones', $data);
        $update = $this->db->affected_rows();
        return $update;
    }
    public function order($orders)
    {
        foreach ($orders as $index => $order)
        {
            $ord = (isset($order[1]))? $order[1]: 'DESC';
            $this->db->order_by($order[0], $ord);
        }
    }

    public function insert($data)
    {
        $this->db = $this->load->database('gestion',TRUE);
       
        $insert = $this->db->insert('control_asignaciones', $data);
        $this->db->insert_id();
        $insert = $this->db->affected_rows();
        return $insert;
        
    }

}