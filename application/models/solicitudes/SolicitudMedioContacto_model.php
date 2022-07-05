<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SolicitudMedioContacto_model extends CI_Model {

   public function __construct() {
        parent::__construct();
        $this->db = $this->load->database('gestion', TRUE);       
    }  
    
    //SELECT DISTINCT(rt.telefono) FROM datacredito2_reconocer_telefono rt 
    //JOIN datacredito2_reconocer_naturalnacional rn ON  rt.idConsulta=rn.id
    //WHERE rn.identificacion=8104980
            
    

    public function getTelefonos($documento, $tipo_buro = ""){
        if ($tipo_buro == "DataCredito"){
            $this->load->database('api_buros',TRUE);
            $this->db->select('DISTINCT(rt.telefono)');
            $this->db->from('api_buros.datacredito2_reconocer_telefono rt ');
            $this->db->join('api_buros.datacredito2_reconocer_naturalnacional rn', 'api_buros.rt.idConsulta = api_buros.rn.id','left');
            $this->db->where("rn.identificacion = '$documento'");        
        } else {
            $this->load->database('api_buros',TRUE);
            $this->db->select('DISTINCT(tel.Telefono)  AS telefono');
            $this->db->from('api_buros.pecoriginacion_telefonos tel');
            $this->db->join('api_buros.dataconsulta', 'api_buros.dataconsulta.id = tel.IdConsulta','left');
            $this->db->where("api_buros.dataconsulta.NumeroIdentificacion = '$documento'");    
        }
        
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }
    
     public function getCelulares($documento, $tipo_buro = ""){ 
        if ($tipo_buro == "DataCredito"){ 
            $this->load->database('api_buros',TRUE);
            $this->db->select('DISTINCT(rc.celular)');
            $this->db->from('api_buros.datacredito2_reconocer_celular rc ');
            $this->db->join('api_buros.datacredito2_reconocer_naturalnacional rn', 'api_buros.rc.idConsulta = api_buros.rn.id','left');
            $this->db->where("rn.identificacion = '$documento'");  
        } else {
            $this->load->database('api_buros',TRUE);
            $this->db->select('DISTINCT(cel.Celular) AS celular');
            $this->db->from('api_buros.pecoriginacion_celulares cel');
            $this->db->join('api_buros.dataconsulta', 'api_buros.dataconsulta.id = cel.IdConsulta','left');
            $this->db->where('api_buros.dataconsulta.NumeroIdentificacion=', $documento);    
        }
        $query = $this->db->get();
        
        $res = $query->result_array();
        //echo $sql = $this->db->last_query();die;
        return $res;
    }
    
    public function getResWhatsApp($documento = ""){   
        $this->db = $this->load->database('chat', TRUE);
           $this->db->select('sms_status');
            $query1=$this->db->get_where('sent_messages','body LIKE "%, soy Asistente de Solventa%" AND id_chat IN (SELECT id FROM new_chats WHERE documento LIKE "%'.$documento.'%")');
            $resultado_query1 = $query1->result(); 
            if (empty($resultado_query1)){
                $query2=$this->db->get_where('sent_messages','id_chat IN (SELECT id FROM new_chats WHERE documento LIKE "%'.$documento.'%")');
                $resultado_query2 = $query2->result();  
                if (empty($resultado_query2)){
                    $this->db->select('*');
                    $query3=$this->db->get_where('received_messages','id_chat IN (SELECT id FROM new_chats WHERE documento LIKE "%'.$documento.'%")');
                    if(!empty($query3->result())){
                        return "L";
                    } 
                } else {
                   return $resultado_query2;  
                }                               
            } else {            
               return $resultado_query1;    
            } 
            
        
        //echo $sql = $this->db->last_query();die;              
              
    }    
    
}
