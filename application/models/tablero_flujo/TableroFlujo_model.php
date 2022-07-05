<?php
class TableroFlujo_model extends CI_Model {
    public function __construct() {  
        parent::__construct();      
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
    }
    /***************************/
    /*** Tipo de Solicitudes ***/
    /***************************/
    public function getTipoSolicitud() {
        $sql = "SELECT 
                    DISTINCT solicitud.tipo_solicitud 
                FROM solicitudes.solicitud";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }
    /***********/
    /*** Pie ***/
    /***********/
    public function getCountRespuestaAnalisis($tipo_solicitud, $fechaIni, $fechaFin) {
        $sql = "select 
                IFNULL(`respuesta_analisis`, 'SIN ANALIZAR') AS respuesta_analisis, 
                COUNT(1) AS cantidad
            FROM 
                `solicitudes`.`solicitud`
            WHERE 
                fecha_alta BETWEEN '" . $fechaIni . "' AND '" . $fechaFin .
                "' AND tipo_solicitud = '" . $tipo_solicitud .
                "' AND (respuesta_analisis IN ('APROBADO', 'RECHAZADO') OR respuesta_analisis IS NULL)
            GROUP BY 
                respuesta_analisis";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result();
    }
    /***********/
    /*** Bar ***/
    /***********/
    public function getStatusFlujo($tipo_solicitud, $fechaIni, $fechaFin) {

        $this->db_solicitudes->select("COUNT(id)");
        $this->db_solicitudes->from("solicitud");
        $this->db_solicitudes->where("fecha_alta BETWEEN '$fechaIni' AND '$fechaFin'");
        $this->db_solicitudes->where("tipo_solicitud = '$tipo_solicitud'");
        $this->db_solicitudes->where("paso = 16");               
        $this->db_solicitudes->where("pagare_firmado = 0");
        $subquery1 = $this->db_solicitudes->get_compiled_select();

        $this->db_solicitudes->select("COUNT(id)");
        $this->db_solicitudes->from("solicitud");
        $this->db_solicitudes->where("fecha_alta BETWEEN '$fechaIni' AND '$fechaFin'");
        $this->db_solicitudes->where("tipo_solicitud = '$tipo_solicitud'");
        $this->db_solicitudes->where("paso = 16");               
        $this->db_solicitudes->where("pagare_firmado = 1");
        $subquery2 = $this->db_solicitudes->get_compiled_select();

        $sql = "SELECT paso,
        CASE 
            WHEN paso = 2 THEN 1
            WHEN paso = 3 THEN 2
            WHEN paso = 4 THEN 3
            WHEN paso = 5 THEN 6
            WHEN paso = 6 THEN 5
            WHEN paso = 7 THEN 4
            WHEN paso = 8 THEN 7
            WHEN paso = 9 THEN 8
            WHEN paso = 10 THEN 9
            WHEN paso = 12 THEN 10
            WHEN paso = 13 THEN 11
            WHEN paso = 16 AND pagare_firmado = 0 THEN 12
            WHEN paso = 16 AND pagare_firmado = 1 THEN 13
            WHEN paso = 18 THEN 14
            ELSE 0 END AS orden,
        CASE
            WHEN paso = 2 THEN 'Registro de Usuario'
            WHEN paso = 3 THEN 'Validación Teléfono'
            WHEN paso = 4 THEN 'Situación Laboral'
            WHEN paso = 5 THEN 'Referencia Familiar'
            WHEN paso = 6 THEN 'Referencia Laboral'
            WHEN paso = 7 THEN 'Confirmacion Cta'
            WHEN paso = 8 THEN 'Registro Cta Bancaria'
            WHEN paso = 9 THEN 'Aceptación Oferta'
            WHEN paso = 10 THEN 'Aceptación Cargos'
            WHEN paso = 12 THEN 'Validación mail'
            WHEN paso = 13 THEN 'Verificación identidad'
            WHEN paso = 16 AND pagare_firmado = 0 THEN 'Por firma pagare'
            WHEN paso = 16 AND pagare_firmado = 1 THEN 'Solicitud Completa'
            WHEN paso = 18 THEN 'Rechazado'
            ELSE 'Sin clasificar' END AS descripcion_paso,
        CASE
            WHEN paso = 2 THEN COUNT(id)
            WHEN paso = 3 THEN COUNT(id)
            WHEN paso = 4 THEN COUNT(id)
            WHEN paso = 5 THEN COUNT(id)
            WHEN paso = 6 THEN COUNT(id)
            WHEN paso = 7 THEN COUNT(id)
            WHEN paso = 8 THEN COUNT(id)
            WHEN paso = 9 THEN COUNT(id)
            WHEN paso = 10 THEN COUNT(id)
            WHEN paso = 12 THEN COUNT(id)
            WHEN paso = 13 THEN COUNT(id)
            WHEN paso = 16 AND pagare_firmado = 0 THEN ($subquery1) WHEN paso = 16
            AND pagare_firmado = 1 THEN ($subquery2) WHEN paso = 18 THEN COUNT(id)
            ELSE COUNT(id) END AS cantidad        
        FROM solicitud 
        WHERE fecha_alta BETWEEN '$fechaIni' AND '$fechaFin'
            AND tipo_solicitud = '$tipo_solicitud'
            AND paso IN (2,3,4,5,6,7,8,9,10,12,13,16,18)
        GROUP BY orden
        ORDER BY orden ASC";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result();
    }
    /************/
    /*** line ***/
    /************/
    /*** Solicitudes por hora ***/
    public function getSolicitudesPorHora($tipo_solicitud, $fechaIni, $fechaFin) {
        $sql = "select
                HOUR(fecha_alta) AS hora,
                COUNT(id) AS cantidad
            FROM 
                solicitudes.solicitud 
            WHERE 
            fecha_alta BETWEEN '" . $fechaIni . "' AND '" . $fechaFin .
                "' AND tipo_solicitud = '" . $tipo_solicitud .
            "' GROUP BY 
                HOUR(fecha_alta);";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result();
    }
    /*** Solicitudes Analizadas por hora ***/
    /*** APROBADAS ***/
    public function getAprobadasPorHora($tipo_solicitud, $fechaIni, $fechaFin) {
        $sql = "select
                HOUR(solicitudes.solicitud_analisis.fecha_consulta) AS hora,
                COUNT(1) AS cantidad
            FROM
                solicitudes.solicitud
            INNER JOIN 
                solicitudes.solicitud_analisis ON solicitudes.solicitud.id = solicitudes.solicitud_analisis.id_solicitud
            WHERE 
                solicitudes.solicitud.tipo_solicitud = '" . $tipo_solicitud .
            "' AND solicitudes.solicitud_analisis.fecha_consulta BETWEEN '" . $fechaIni . "' AND '" . $fechaFin .  
            "' AND solicitudes.solicitud_analisis.respuesta = 'APROBADO'
            GROUP BY 
                HOUR(solicitudes.solicitud_analisis.fecha_consulta);";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result();
    }
    /*** RECHAZADAS ***/
    public function getRechazadasPorHora($tipo_solicitud, $fechaIni, $fechaFin) {
        $sql = "select
                HOUR(solicitudes.solicitud_analisis.fecha_consulta) AS hora,
                COUNT(1) AS cantidad
            FROM
                solicitudes.solicitud
            INNER JOIN 
                solicitudes.solicitud_analisis ON solicitudes.solicitud.id = solicitudes.solicitud_analisis.id_solicitud
            WHERE 
                solicitudes.solicitud.tipo_solicitud = '" . $tipo_solicitud .
            "' AND solicitudes.solicitud_analisis.fecha_consulta BETWEEN '" . $fechaIni . "' AND '" . $fechaFin .  
            "' AND solicitudes.solicitud_analisis.respuesta = 'RECHAZADO'
            GROUP BY 
                HOUR(solicitudes.solicitud_analisis.fecha_consulta);";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result();
    }
}
