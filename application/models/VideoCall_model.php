<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class VideoCall_model extends CI_Model {

    public function __construct() {  
        parent::__construct();      
        $this->db_videollamadas = $this->load->database('videollamadas', TRUE);
        $this->db_solicitud = $this->load->database('solicitudes', TRUE);
        $this->db_gestion = $this->load->database('gestion', TRUE);
    }

    public function get_status_videoCall($params) {
        $this->db_videollamadas->select('*');
        $this->db_videollamadas->from('videollamada');
        $this->db_videollamadas->where('documento ="'.$params['documento'].'"');
        $this->db_videollamadas->where('idOperador', $params['idOperador']);
        if(isset($params['status'])){ $this->db_videollamadas->where('status',$params['status']);}
        if(isset($params['activeRoom'])){ $this->db_videollamadas->where('activeRoom',$params['activeRoom']);}
        if(isset($params['id_solicitud'])){ $this->db_videollamadas->where('id_solicitud',$params['id_solicitud']);}
        $query = $this->db_videollamadas->get();
        // echo $sql = $this->db_videollamadas->last_query();die;
        return $query->row();
    }

    public function get_user_testing($operador) {
        $this->db_videollamadas->select('*');
        $this->db_videollamadas->from('test_user');
        $this->db_videollamadas->where('idoperador', $operador);
        $this->db_videollamadas->where('estado',1);
        $query = $this->db_videollamadas->get();
        return $query->row();
    }
        
    public function get_user_videollamadas($operador) {
        $this->db_gestion->select('*');
        $this->db_gestion->from('users_servicios');
        $this->db_gestion->where('idoperador', $operador);
        $this->db_gestion->where('videollamadas', 1);
        $query = $this->db_gestion->get();
        return $query->row();
    }

    public function get_list_videollamadas($params) {
        $this->db_videollamadas->select('*');
        $this->db_videollamadas->from('videollamada');
        if(isset($params['documento'])){ $this->db_videollamadas->where('documento',$params['documento']);}
        if(isset($params['idOperador'])){ $this->db_videollamadas->where('idOperador',$params['idOperador']);}
        if(isset($params['status'])){ $this->db_videollamadas->where('status',$params['status']);}
        if(isset($params['activeRoom'])){ $this->db_videollamadas->where('activeRoom',$params['activeRoom']);}
        if(isset($params['id_solicitud'])){ $this->db_videollamadas->where('id_solicitud',$params['id_solicitud']);}
        if(isset($params['CompositionStatus'])){ $this->db_videollamadas->where('CompositionStatus',$params['CompositionStatus']);}
        $query = $this->db_videollamadas->get();
        // echo $sql = $this->db_videollamadas->last_query();die;
        return $query->result();
    }

    public function get_solicitud_videos($params) {
        $this->db_solicitud->select('*');
        $this->db_solicitud->from('solicitud_imagenes');
        $this->db_solicitud->where('id_imagen_requerida', 26);
        $this->db_solicitud->where('is_image', 0);
        $this->db_solicitud->where('origen','BACK');
        $this->db_solicitud->where('id_solicitud', $params['id_solicitud']);
        $this->db_solicitud->where('scan_reference', $params['cid']);
        $query = $this->db_solicitud->get();
        return $query->result_array();
    }

}
