<?php

class RespuestasBancos_model extends CI_Model {


    public function __construct()
    {

        $this->db_respuestas_bancos = $this->load->database("respuestas_bancos", TRUE);
    }

    public function getStatusCron()
    {

        $this->db_respuestas_bancos->select('status');
        $this->db_respuestas_bancos->from('status_cron');
        $this->db_respuestas_bancos->where('id', 1);

        return $this->db_respuestas_bancos->get()->row();
    }

    public function insertBatch($data)
    {
        $this->db_respuestas_bancos->insert_batch('debitos_bancolombia_rec', $data);
    }

    /*
    * Verifica que el archivo no se haya subido anteriormente
    */
    public function fileExistInDB($fileName)
    {

        $result = false;

        $this->db_respuestas_bancos->select('referencia');
        $this->db_respuestas_bancos->from("debitos_bancolombia_rec");
        $this->db_respuestas_bancos->where("referencia", $fileName);
        
        $query = $this->db_respuestas_bancos->get();

        if ($query->num_rows() > 0)
            return true;
        
        return $result;
    }    

    public function setStatusCron($status = 0)
    {
        $data = array(
            'status_cron.status' => $status
        );

        $this->db_respuestas_bancos->where('status_cron.id', 1);
        $this->db_respuestas_bancos->update('status_cron', $data);
    }

    public function fileExistInRcgaDB($fileName)
    {
        $result = false;

        $this->db_respuestas_bancos->select('referencia');
        $this->db_respuestas_bancos->from("debitos_bancolombia_rcga");
        $this->db_respuestas_bancos->where("referencia", $fileName);
        
        $query = $this->db_respuestas_bancos->get();

        if ($query->num_rows() > 0)
            return true;
        
        return $result;
    }

    public function fileExistInRnovDB($fileName)
    {
        $result = false;

        $this->db_respuestas_bancos->select('referencia');
        $this->db_respuestas_bancos->from("debitos_bancolombia_rnov");
        $this->db_respuestas_bancos->where("referencia", $fileName);
        
        $query = $this->db_respuestas_bancos->get();

        if ($query->num_rows() > 0)
            return true;
        
        return $result;
    }

    public function getArchivosEnviosLista()
    {

        $sql = 'SELECT referencia, CAST(fecha AS DATE) AS fecha FROM `debitos_bancolombia_rcga` 
                    UNION 
                        SELECT referencia, CAST(fecha AS DATE) AS fecha FROM `debitos_bancolombia_rnov` GROUP BY referencia ORDER BY fecha;';

        $query = $this->db_respuestas_bancos->query($sql);

        return $query->result();
    }

    public function getEnviosPorDiaRcga($fecha)
    {

        $this->db_respuestas_bancos->select(' 
            rcga.referencia AS FactAchivo,
            rcga.id_detalle_credito,
            CAST(rcga.fecha_subida AS DATE) AS fecha_subida,
            rcga.cod_resp AS Fact,
            CAST(rcga.fecha_a_debitar AS DATE) AS Debita
        ');

        $this->db_respuestas_bancos->from('debitos_bancolombia_rcga rcga');

        $this->db_respuestas_bancos->where('CAST(rcga.fecha_subida AS DATE) =',  $fecha);

        $query = $this->db_respuestas_bancos->get();

        //echo $this->db_respuestas_bancos->last_query();

        return $query->result();
    }

    public function getEnviosPorDiaRnov($fecha)
    {

        $this->db_respuestas_bancos->select(' 
            rnov.convenio,
            rnov.referencia AS NovAchivo,
            rnov.id_detalle_credito,
            CAST(rnov.fecha_subida AS DATE) AS fecha_subida,
            rnov.cod_resp AS Nov,
            CAST(rnov.fecha_inicio AS DATE) AS Inicia,
            CAST(
                rnov.fecha_finalizacion AS DATE
            ) AS Finaliza');

        $this->db_respuestas_bancos->from('debitos_bancolombia_rnov rnov');

        $this->db_respuestas_bancos->where('CAST(rnov.fecha_subida AS DATE) =',  $fecha);

        $query = $this->db_respuestas_bancos->get();

        //echo $this->db_respuestas_bancos->last_query();

        return $query->result();
    }

    public function getNombreArchivos($fecha, $tipoArchivo = "rcga")
    {

        $this->db_respuestas_bancos->select($tipoArchivo . '.referencia AS archivo');

        $this->db_respuestas_bancos->from('debitos_bancolombia_' . $tipoArchivo . ' AS ' . $tipoArchivo);

        $this->db_respuestas_bancos->where('CAST(' . $tipoArchivo . '.fecha_subida AS DATE) =',  $fecha);

        switch ($tipoArchivo) 
        {
            case 'rcga':
                $this->db_respuestas_bancos->where( $tipoArchivo . '.cod_resp',               "OK0");
                break;
            
            case 'rnov':
                $this->db_respuestas_bancos->where_in( $tipoArchivo . '.cod_resp',            array("OK0", "D65"));
                break;

            default:
                $this->db_respuestas_bancos->where( $tipoArchivo . '.cod_resp',               "OK0");
                break;
        }

        $this->db_respuestas_bancos->group_by($tipoArchivo . '.referencia');

        $query = $this->db_respuestas_bancos->get();

        //echo $this->db_respuestas_bancos->last_query();

        return $query->result();
    }

    public function getTotalEnviosXFecha($fecha, $tipoArchivo = "rcga")
    {
        $this->db_respuestas_bancos->select('count(' . $tipoArchivo . '.referencia) AS total');

        $this->db_respuestas_bancos->from('debitos_bancolombia_' . $tipoArchivo . ' AS ' . $tipoArchivo);

        $this->db_respuestas_bancos->where('CAST(' . $tipoArchivo . '.fecha_subida AS DATE) =',  $fecha);

        $query = $this->db_respuestas_bancos->get();

        //echo $this->db_respuestas_bancos->last_query();

        return $query->row();
    }

    public function getTotalXCodResp($fecha, $tipoArchivo = "rcga")
    {

        $this->db_respuestas_bancos->select("
            CASE 
                WHEN cod_resp = 'D11' THEN 'Estado Invalido para realizar Debitos' 
                WHEN cod_resp = 'D03' THEN 'Cuenta No Numerica' 
                WHEN cod_resp = 'D10' THEN 'Cuenta Inesxitente' 
                WHEN cod_resp = 'D12' THEN 'NIT Desconocido' 
                WHEN cod_resp = 'D65' THEN 'Inscripcion Activa'
                WHEN cod_resp = 'OK0' THEN 'Validacion Exitosa' 
            ELSE 'SIN CODIGO' 
            END AS codigo_respuesta, 
            COUNT(cod_resp) AS total");

        $this->db_respuestas_bancos->from('debitos_bancolombia_' . $tipoArchivo . ' AS ' . $tipoArchivo);

        $this->db_respuestas_bancos->where('CAST(' . $tipoArchivo . '.fecha_subida AS DATE) =',  $fecha);

        $this->db_respuestas_bancos->group_by('cod_resp');

        $query = $this->db_respuestas_bancos->get();

        //echo $this->db_respuestas_bancos->last_query();

        return $query->result();
    }

}