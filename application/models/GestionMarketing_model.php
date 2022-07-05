<?php
class GestionMarketing_model extends CI_Model {
       
    public function __construct(){  
        parent::__construct();      
        $this->load->library('Sqlexceptions');
        $this->Sqlexceptions = new Sqlexceptions();
        $this->solicitudes = $this->load->database('solicitudes', TRUE);
        $this->parametria = $this->load->database('parametria', TRUE);
        $this->maestro = $this->load->database('maestro', TRUE);
    }

    //metodos

    // retorno calculo de desembolsos
    public function desembolsos($nombre_proveedor = null, $fecha_desde = "", $fecha_hasta = "", $type) {

        $this->parametria->select("nombre_proveedor AS nombre");
        $this->parametria->from("parametria.senders_providers");
        $this->parametria->where("nombre_proveedor = solicitud.utm_source");
        $subQuery = $this->parametria->get_compiled_select(); 

        $this->parametria->select("nombre_proveedor AS nombre");
        $this->parametria->from("parametria.senders_providers");
        $subQuery2 = $this->parametria->get_compiled_select(); 

        if ($type == 0) {
            if ($nombre_proveedor == "") {
                $this->solicitudes->select("COUNT(*) cantidad, ($subQuery) AS utm_source, solicitud.tracking_id");
            }
        }else{
            $this->solicitudes->select("
            solicitud.fecha_alta, 
            solicitud.id,
            solicitud.tracking_id, 
            solicitud.documento,
            concat(solicitud.nombres,' ',  solicitud.apellidos) 'nombre_completo',
            solicitud.paso as paso,
            nombre_situacion AS situacion_laboral, 
            solicitud.estado,
            solicitud.estado, 
            solicitud.utm_medium, 
            solicitud.utm_source, 
            solicitud.utm_campaign, 
            solicitud.tipo_solicitud, 
            solicitud_condicion_desembolso.capital_solicitado, 
            solicitud_condicion_desembolso.total_devolver,
            solicitud_txt.fecha_procesado AS fecha_desembolso,
            solicitud.email,
	        solicitud.telefono,
            solicitud.tracking_id");
        }
        $this->solicitudes->from("solicitud");
        $this->solicitudes->join("parametria.situacion_laboral", "parametria.situacion_laboral.id_situacion = solicitud.id_situacion_laboral", "left");
        $this->solicitudes->join("solicitud_condicion_desembolso", "solicitud_condicion_desembolso.id_solicitud = solicitud.id", "left");
        $this->solicitudes->join("solicitud_txt", "solicitud_txt.id_solicitud = solicitud.id");      
        if (!empty($nombre_proveedor)) {
            $this->solicitudes->where("utm_source IN ($nombre_proveedor)");
        }else{
            $this->solicitudes->where("utm_source IN ($subQuery2)");
        }
        $this->solicitudes->where("solicitud_txt.fecha_procesado  BETWEEN '$fecha_desde 00:00:00' AND '$fecha_hasta 23:59:59'");
        $this->solicitudes->where('solicitud_txt.pagado = 1');
        $this->solicitudes->where('solicitud.tipo_solicitud = "PRIMARIA"');
        $this->solicitudes->where("(solicitud.tracking_id != 'undefined' AND solicitud.tracking_id != '' AND solicitud.tracking_id IS NOT NULL)");
        if ($type == 0) {
            $this->solicitudes->group_by("utm_source");
            $this->solicitudes->order_by("solicitud.id DESC");
        }
        $data = $this->solicitudes->get();
        // var_dump($this->solicitudes->last_query());die;  
        $dataT = $data->result_array();
        return $dataT;
    }

    // retorno aprobados Buro
    public function aprobadosBuro($nombre_proveedor = null, $fecha_desde = "", $fecha_hasta = "", $type) {

        $this->parametria->select("nombre_proveedor AS nombre");
        $this->parametria->from("parametria.senders_providers");
        $this->parametria->where("nombre_proveedor = solicitud.utm_source");
        $subQuery = $this->parametria->get_compiled_select(); 
        
        $this->parametria->select("nombre_proveedor AS nombre");
        $this->parametria->from("parametria.senders_providers");
        $subQuery2 = $this->parametria->get_compiled_select(); 
        
        if ($type == 0) {
            $this->solicitudes->select("COUNT(*) cantidad, ($subQuery) AS utm_source, solicitud.tracking_id");
        }else{

            $this->solicitudes->select("
            solicitud.fecha_alta, 
            solicitud.id,
            solicitud.tracking_id, 
            solicitud.documento,
            concat(solicitud.nombres,' ',  solicitud.apellidos) AS 'nombre_completo',
            solicitud.paso as paso,
            nombre_situacion AS situacion_laboral, 
            solicitud.estado, 
            solicitud.utm_medium, 
            solicitud.utm_source, 
            solicitud.utm_campaign, 
            solicitud.tipo_solicitud,
            solicitud.email,
	        solicitud.telefono,
            solicitud.tracking_id");
        }
        $this->solicitudes->from("solicitud");
        $this->solicitudes->join("parametria.situacion_laboral", "parametria.situacion_laboral.id_situacion = solicitud.id_situacion_laboral", "left");
        $this->solicitudes->where("fecha_alta BETWEEN '$fecha_desde 00:00:00' AND '$fecha_hasta 23:59:59'");
        $this->solicitudes->where("tipo_solicitud = 'PRIMARIA'");
        $this->solicitudes->where("(solicitud.tracking_id != 'undefined' AND solicitud.tracking_id != '' AND solicitud.tracking_id IS NOT NULL)");
        if (!empty($nombre_proveedor)) {
            $this->solicitudes->where("utm_source IN ($nombre_proveedor)");
        }else{
            $this->solicitudes->where("utm_source IN ($subQuery2)");
        }
        $this->solicitudes->where("respuesta_analisis = 'APROBADO'");
        if ($type == 0) {
            $this->solicitudes->group_by("utm_source");
            $this->solicitudes->order_by("solicitud.id DESC");
        }
        $data = $this->solicitudes->get();
        // var_dump($this->solicitudes->last_query());die;  
        $dataT = $data->result_array();
        return $dataT;
    }

    // retorno total Leads
    public function totalLeads($nombre_proveedor = "", $fecha_desde = "", $fecha_hasta = "", $type){

        $this->parametria->select("nombre_proveedor AS nombre");
        $this->parametria->from("parametria.senders_providers");
        $this->parametria->where("nombre_proveedor = solicitud.utm_source");
        $subQuery = $this->parametria->get_compiled_select(); 

        $this->parametria->select("nombre_proveedor AS nombre");
        $this->parametria->from("parametria.senders_providers");
        $subQuery2 = $this->parametria->get_compiled_select(); 

        if ($type == 0) {
            $this->solicitudes->select("COUNT(*) cantidad, ($subQuery) AS utm_source, solicitud.tracking_id");
            
        }else{
            $this->solicitudes->select("
                            solicitud.fecha_alta, 
                            solicitud.id,
                            solicitud.tracking_id, 
                            solicitud.documento,
                            concat(solicitud.nombres,' ',  solicitud.apellidos) AS 'nombre_completo',
                            solicitud.paso as paso, 
                            nombre_situacion AS situacion_laboral,
                            solicitud.estado, 
                            solicitud.utm_medium, 
                            solicitud.utm_source, 
                            solicitud.utm_campaign, 
                            solicitud.tipo_solicitud,
                            solicitud.email,
	                        solicitud.telefono,
                            solicitud.tracking_id");
        }

        $this->solicitudes->from("solicitud");
        $this->solicitudes->join("parametria.situacion_laboral", "parametria.situacion_laboral.id_situacion = solicitud.id_situacion_laboral", "left");
        $this->solicitudes->where("fecha_alta BETWEEN '$fecha_desde 00:00:00' AND '$fecha_hasta 23:59:59'");
        $this->solicitudes->where("tipo_solicitud = 'PRIMARIA'");
        $this->solicitudes->where("(solicitud.tracking_id != 'undefined' AND solicitud.tracking_id != '' AND solicitud.tracking_id IS NOT NULL)");
        if (!empty($nombre_proveedor)) {
            $this->solicitudes->where("utm_source IN ($nombre_proveedor)");
        }else{
            $this->solicitudes->where("utm_source IN ($subQuery2)");
        }
        if ($type == 0) {
            $this->solicitudes->group_by("utm_source");
            $this->solicitudes->order_by("solicitud.id DESC");
        }

        $data = $this->solicitudes->get();
        // var_dump($this->solicitudes->last_query());die;
        $dataT = $data->result_array();
        return $dataT;
    }

    //retorno nombre de proveedores
    public function getProviders() {

        $sql =$this->db->query("SELECT 
                                    nombre_proveedor AS nombre 
                                FROM parametria.senders_providers");

        return $sql->result_array();
    }
}
?>
