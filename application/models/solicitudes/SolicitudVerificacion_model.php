<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SolicitudVerificacion_model extends BaseModel {

   public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('solicitudes', TRUE);       
    }

    public function guardarReferencia($data){   
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->insert('solicitudes.verificacion_referencia', $data);
        //echo $sql = $this->db->last_query();die;
        return $this->db->insert_id();
    }
    
    public function cantidadGuardada($id_solicitud,$id_tipo_verificacion){   
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('count(*) as cantidad');
        $this->db->from('solicitudes.verificacion_referencia');    
        $this->db->where('solicitudes.verificacion_referencia.id_solicitud ='.$id_solicitud.' AND id_tipo_verificacion='.$id_tipo_verificacion);
        $query = $this->db->get();        
        return $query->result_array(); 
    }
    
    
}
