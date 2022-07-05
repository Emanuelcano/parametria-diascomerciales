<?php 

defined('BASEPATH') or exit('No direct script access allowed');


class Dias_Comerciales_model extends CI_Model
    {

        function __construct()
            {
                
                parent::__construct();
                $this->db_parametria = $this->load->database('parametria', TRUE);

            }

    public function get_lista_dias_comerciales()
                {       
                
                    $this->db_parametria->select('*');
                    $this->db_parametria->from('dia_comercial');
                    
                    $query = $this->db_parametria->get();
                    
                    return $query->result_array();

                }
    public function registrar_dia_comercial($data)
        {
            
           $query = $this->db_parametria->insert( 'dia_comercial' ,$data);  
        
                return $query;

        }
    public function cargar_Dia($id)
        {
            $this->db_parametria->select('id, fecha, descripcion');
            $this->db_parametria->from('dia_comercial');
            $this->db_parametria->where('id', $id);
            $query = $this->db_parametria->get();
            return $query->result_array();
        }
    

    public function actualizar_dia_comercial($data,$id)
    {
    
        $query = $this->db_parametria->update('dia_comercial',$data, ['id' => $id]);

            return $query;
    }

   

    }