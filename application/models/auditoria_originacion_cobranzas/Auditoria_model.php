<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auditoria_model extends CI_Model 
{

    public function __construct() {
        parent::__construct();
        $this->db_auditoria   = $this->load->database('auditoria', TRUE);
        $this->db_gestion     = $this->load->database('gestion', TRUE);
        $this->db_maestro     = $this->load->database('maestro', TRUE);
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_parametria  = $this->load->database('parametria', TRUE);
        $this->db_telefonia   = $this->load->database('telefonia', TRUE);
        
        //Server FILE_SOLVENTA
        $this->db_file_solventa   = $this->load->database('files-solventa', TRUE);
        
    }


    public function getTrackPorOperador($id_track = null, $id_operador = null, $filtro = null, $limit, $offset)
    {


        // $subqueryTrackGestionNOTIN = $this->db_auditoria->select('id_solicitud')
        //     ->from('auditoria.auditoria_auditados')
        //     ->get_compiled_select();
        // print_r($subqueryTrackGestionNOTIN);die;


        $query = $this->db_gestion->select(
                        $this->db_gestion->database.'.track_gestion.fecha AS fecha,'.
                        $this->db_gestion->database.'.track_gestion.hora AS hora,'.
                        $this->db_solicitudes->database.'.solicitante_agenda_telefonica.numero AS numero_telefonico,'.
                        $this->db_solicitudes->database.'.solicitante_agenda_telefonica.contacto AS contacto,'.
                        $this->db_solicitudes->database.'.solicitud.id AS solicitud,'.
                        $this->db_solicitudes->database.'.solicitud.documento AS documento,'.
                        // $this->db_parametria->database.'.situacion_laboral.nombre_situacion AS situacion_laboral,'.
                        // $this->db_solicitudes->database.'.solicitud.estado AS estado_solicitud,'.
                        // $this->db_maestro->database.'.creditos.estado AS estado_credito,'.
                        // $this->db_gestion->database.'.acuerdos_pago.estado AS estado_acuerdo,'.
                        $this->db_gestion->database.'.track_gestion.operador AS operador_asignado,'.
                        $this->db_gestion->database.'.operadores.equipo AS operador_equipo,'.
                        $this->db_gestion->database.'.operadores.idoperador AS id_operador,'.
                        $this->db_gestion->database.'.operadores.tipo_operador AS tipo_operador'
                    )
                    ->from('track_gestion')
                    ->join(
                        $this->db_solicitudes->database.'.solicitud',  
                        $this->db_solicitudes->database.'.solicitud.id = '.$this->db_gestion->database.'.track_gestion.id_solicitud', 
                        'left'
                    )
                    ->join(
                    $this->db_gestion->database.'.operadores', 
                    $this->db_gestion->database.'.operadores.idoperador ='.$this->db_gestion->database.'.track_gestion.id_operador', 
                    'left'
                    )
                    ->join(
                        $this->db_solicitudes->database.'.solicitante_agenda_telefonica',  
                        $this->db_solicitudes->database.'.solicitante_agenda_telefonica.documento = '.$this->db_solicitudes->database.'.solicitud.documento', 
                        'left'
                    )
                    ->join(
                        $this->db_parametria->database.'.situacion_laboral', 
                        $this->db_parametria->database.'.situacion_laboral.id_situacion ='.$this->db_solicitudes->database.'.solicitud.id_situacion_laboral', 
                        'left'
                    )
                    ->join(
                        $this->db_maestro->database.'.creditos', 
                        $this->db_maestro->database.'.creditos.id ='.$this->db_solicitudes->database.'.solicitud.id_credito', 
                        'left'
                    );
                    // ->join(
                    //     $this->db_gestion->database.'.acuerdos_pago', 
                    //     $this->db_gestion->database.'.acuerdos_pago.id_credito ='.$this->db_maestro->database.'.creditos.id', 
                    //     'left'
                    // );  
                if ($id_track != null) {
                    $query->where('id_tipo_gestion', $id_track);
                }  
                if ($id_operador != null) {
                    $query->where('id_operador', $id_operador);
                }
                
                $query->where($this->db_gestion->database.'.track_gestion.fecha BETWEEN "'.$filtro['fecha_desde'].'" AND "'.$filtro['fecha_hasta'].'"');

                $query->where($this->db_solicitudes->database.'.solicitante_agenda_telefonica.fuente', 'PERSONAL DECLARADO');
                
                if ($filtro['pais'] != '' || $filtro['pais'] != null) {
                    $query->where($this->db_gestion->database.'.operadores.equipo = "'.$filtro['pais'].'"');
                }
                
                if ($filtro['operador'] != '' || $filtro['operador'] != null) {
                    $query->where($this->db_gestion->database.'.track_gestion.id_operador = "'.$filtro['operador'].'"');
                }

                if ($filtro['telefono'] != '' || $filtro['telefono'] != null) {
                    $query->where($this->db_solicitudes->database.'.solicitud.telefono = "'.$filtro['telefono'].'"');
                }

                if ($filtro['tipoOperador'] == "(1,4,5,6)") {
                    $query->where($this->db_gestion->database.'.operadores.tipo_operador IN '.$filtro['tipoOperador']);
                }else{
                    $query->where($this->db_gestion->database.'.operadores.tipo_operador ='.$filtro['tipoOperador']);
                }
                $query->where($this->db_gestion->database.'.operadores.estado = 1');
                // $query->whre_not_in($this->db_solicitudes->database.'.solicitud.id', $this->db_auditoria->database.'.auditoria.id_solicitud');
                // $query->where($this->db_solicitudes->database.".solicitud.id NOT IN ($subqueryTrackGestionNOTIN)");

                
                $query->order_by($this->db_gestion->database.'.track_gestion.id', 'DESC');
                $query->group_by($this->db_gestion->database.'.track_gestion.id_solicitud');
                $query->limit($offset, $limit);
                

        // $query->get();
        // echo $query->last_query();die;
        return $query->get()->result();
    }

    public function getAuditoriasPorAuditor($id_auditor)
    {
        $query = $this->db_auditoria->select('auditoria.id, auditoria.fecha_auditoria,(SELECT nombre_apellido FROM gestion.operadores WHERE gestion.operadores.idoperador = auditoria.operador_auditor) AS operador_auditor,
        auditoria.observaciones, auditoria.id_solicitud,'
            .$this->db_solicitudes->database.'.solicitante_agenda_telefonica.numero AS numero_telefonico,'
            .$this->db_solicitudes->database.'.solicitante_agenda_telefonica.contacto AS contacto,'
            .$this->db_solicitudes->database.'.solicitud.documento AS documento,'
            .$this->db_gestion->database.'.operadores.nombre_apellido AS operador_asignado,'
            )
        ->from('auditoria')
        ->join(
            $this->db_solicitudes->database.'.solicitud',  
            $this->db_solicitudes->database.'.solicitud.id = '.$this->db_auditoria->database.'.auditoria.id_solicitud', 
            'left'
        )
        ->join(
            $this->db_solicitudes->database.'.solicitante_agenda_telefonica',  
            $this->db_solicitudes->database.'.solicitante_agenda_telefonica.documento = '.$this->db_solicitudes->database.'.solicitud.documento', 
            'left'
        )
        ->join(
            $this->db_gestion->database.'.operadores', 
            $this->db_gestion->database.'.operadores.idoperador ='.$this->db_solicitudes->database.'.solicitud.operador_asignado', 
            'left'
        )
        ->join(
            $this->db_auditoria->database.'.audios_auditados', 
            $this->db_auditoria->database.'.audios_auditados.id_auditoria ='. $this->db_auditoria->database.'.auditoria.id', 
            'left'
        )
        ->where($this->db_solicitudes->database.'.solicitante_agenda_telefonica.fuente', 'PERSONAL DECLARADO')
        ->where('operador_auditor', $id_auditor)
        ->where($this->db_auditoria->database.'.audios_auditados.tipo_reporte', 'Auditado');
        
        $query->order_by($this->db_auditoria->database.'.auditoria.id', 'DESC');
        $query->group_by($this->db_auditoria->database.'.auditoria.id');

        // $query->get();
        // echo $query->last_query();die;
        return $query->get()->result();
    }


    /**
    * Guardo informacion del llamado auditado en la tabla auditoria.audios_auditados.
    *   
    * @return number $id
    */
    
    public function saveLlamadoAuditado_post($data) 
    {
        $this->db_auditoria->insert( 'auditoria' ,$data);  
        return $this->db_auditoria->insert_id();
    }

    /**
    * Veo si el audio fue auditado.
    *
    * @param number $id_track  
    * @return Objet
    */
    
    public function casosAuditados_get($id_track)
    {
        $query = $this->db_auditoria->select('*')
                ->from('audios_auditados')
                ->where('id_audio', $id_track);
                // ->where('tipo_reporte', 'Auditado')
                // ->where('tipo_reporte', 'Reportado')
                // ->where('tipo_reporte', 'No corresponde');
                
        return $query->get()->row();
    }

    /**
    * Guardo las calificaciones de los distintos parametros auditados en ese llamado.
    *   
    * @return id
    */
    
    public function saveRespuesta($data)
    {        
        $this->db_auditoria->insert('detalle_auditoria', $data);
        return $this->db_auditoria->insert_id();
    }

    /**
    * Obtengo el id del parametro evaluado.
    * 
    * @param string $abreviatura  
    * @return id
    */

    public function getIdParametroEvaluar($abreviatura)
    {
        $id_parametro_evaluar = $this->db_auditoria->select('id')
            ->from('parametros')
            ->where('nombre', $abreviatura)
            ->get()->result();
        return $id_parametro_evaluar;
    }

    public function getGrupoParametroCobranza()
    {
        $grupo_parametro = $this->db_auditoria->select('gp.id, gp.nombre')
            ->from('grupo_parametros AS gp')
            ->where('gp.estado', '1')
            ->where('gp.id_tipo_grupo IS NULL')
            ->get()->result_array();
        return $grupo_parametro;
    }

    public function getParametroCobranza($id_grupo_parametro)
    {
        $parametros = $this->db_auditoria->select('parametros.id, parametros.id_grupo, parametros.nombre as name, grupo_parametros.tag_id, grupo_parametros.nombre , parametros.descripcion')
            ->from('parametros')
            ->join('grupo_parametros', 'grupo_parametros.id = parametros.id_grupo', 'left')
            ->where('parametros.id_grupo', $id_grupo_parametro)
            ->where('parametros.estado', '1')
            ->where('parametros.canal', "cobranzas")
            ->get()->result_array();
            // var_dump($this->db_auditoria->last_query());die;
        return $parametros;
    }

    public function getGrupoParametroOriginacion($estado)
    {
        if (empty($estado)) {
            $estado = "NULL";
        }
        $grupo_parametro = $this->db_auditoria->select('gp.id, gp.nombre')
            ->from('grupo_parametros AS gp')
            ->where('gp.estado', '1')
            ->where('gp.id_tipo_grupo IN (SELECT tg.id FROM tipo_grupo AS tg WHERE tg.nombre_grupo LIKE "%'.$estado.'%")')
            ->get()->result_array();
            // var_dump($this->db_auditoria->last_query());die;
        return $grupo_parametro;
    }

    public function getParametroOriginacion($id_grupo_parametro, $estado)
    {
        $parametros = $this->db_auditoria->select('parametros.id, parametros.id_grupo, parametros.nombre as name, grupo_parametros.tag_id, grupo_parametros.nombre , parametros.descripcion')
            ->from('parametros')
            ->join('grupo_parametros', 'grupo_parametros.id = parametros.id_grupo', 'left')
            ->join('tipo_grupo AS t', 't.id = grupo_parametros.id_tipo_grupo')
            ->where('parametros.id_grupo', $id_grupo_parametro)
            ->where('parametros.estado', '1')
            ->where('parametros.canal', "originacion")
            ->where('t.nombre_grupo LIKE "%'.$estado.'%"')
            ->get()->result_array();
            // var_dump($this->db_auditoria->last_query());die;
        return $parametros;
    }

    public function getCalificacionesActivas()
    {
        $calificaciones = $this->db_auditoria->select('id, nombre, etiqueta')
            ->from('calificaciones')
            ->where('estado', '1')
            ->get()->result_array();
        return $calificaciones;
    }

    /**
    * Obtengo el id de la calificación del parametro evaluado.
    * 
    * @param string $abreviatura  
    * @return id
    */

    public function getIdCalificacion($abreviatura)
    {
        $id_calificacion = $this->db_auditoria->select('id')
            ->from('calificaciones')
            ->where('etiqueta', $abreviatura)
            ->get()->result_array();
        return $id_calificacion;
    }

    public function getAllCalificacion()
    {
        $calificaciones = $this->db_auditoria->select('*')
            ->from('calificaciones')
            ->where('estado', '1')
            ->get()->result();
        return $calificaciones;
    }

    function save_audio_auditado($data)
    {
        $this->db_auditoria->insert('audios_auditados',$data);
        
		return $this->db_auditoria->insert_id();
    }

    public function getDetalleAuditoria($id_auditoria)
    {
        $auditoria = $this->db_auditoria->select('auditoria.id, auditoria.fecha_auditoria,(SELECT nombre_apellido FROM gestion.operadores WHERE gestion.operadores.idoperador = auditoria.operador_auditor) AS operador_auditor,
        auditoria.observaciones, auditoria.id_solicitud,
                                                '.$this->db_solicitudes->database.'.solicitante_agenda_telefonica.numero AS numero_telefonico,'
                                                .$this->db_solicitudes->database.'.solicitante_agenda_telefonica.contacto AS contacto,'
                                                .$this->db_solicitudes->database.'.solicitud.documento AS documento,'
                                                .$this->db_gestion->database.'.operadores.nombre_apellido AS operador_asignado,')
            ->from('auditoria')
            ->join(
                $this->db_solicitudes->database.'.solicitud',  
                $this->db_solicitudes->database.'.solicitud.id = '.$this->db_auditoria->database.'.auditoria.id_solicitud', 
                'left'
            )
            ->join(
                $this->db_solicitudes->database.'.solicitante_agenda_telefonica',  
                $this->db_solicitudes->database.'.solicitante_agenda_telefonica.documento = '.$this->db_solicitudes->database.'.solicitud.documento', 
                'left'
            )
            ->join(
                $this->db_gestion->database.'.operadores', 
                $this->db_gestion->database.'.operadores.idoperador ='.$this->db_solicitudes->database.'.solicitud.operador_asignado', 
                'left'
            )
            ->where('auditoria.id', $id_auditoria)
            ->group_by('auditoria.id')
            ->get()->row();

        $detalle_auditoria = $this->db_auditoria->select('parametros.descripcion as parametro_evaluado, 
                                                          calificaciones.nombre as calificacion,
                                                          grupo_parametros.nombre as grupo_parametro')
            ->from('detalle_auditoria')
            ->join('parametros', 'detalle_auditoria.id_parametro = parametros.id', 'left')
            ->join('calificaciones', 'detalle_auditoria.id_calificacion = calificaciones.id', 'left')
            ->join('grupo_parametros', 'parametros.id_grupo = grupo_parametros.id', 'left')
            ->where('detalle_auditoria.id_auditoria', $id_auditoria)
            ->order_by('grupo_parametros.id', 'asc')
            ->get()->result_array();

        $audios_auditados = $this->db_auditoria->select('id_audio')
            ->from('audios_auditados')
            ->where('id_auditoria', $id_auditoria)  
            ->get()->result_array();

        $auditoria = [
            'auditoria' => $auditoria,
            'detalle_auditoria' => $detalle_auditoria,
            'audios_auditados' => $audios_auditados
        ];

        return $auditoria;
    }

    public function save_audio_reportado($data)
    {
        $this->db_auditoria->insert('audios_reportados',$data);
        
		return $this->db_auditoria->insert_id();
    }

    public function audioAuditados($id_solicitud, $id_operador)
    {
        $query = $this->db_auditoria->select('id')
        ->from('auditoria')
        ->where('id_solicitud', $id_solicitud);
        
        $data = $this->db_auditoria->count_all_results();
        return $data;
    }

    public function audios($telefono)
    {
        $this->db_file_solventa->select("id_track,nombre_archivo,numero_solicitud,path_audio,fecha_audio,fecha_track");
		$this->db_file_solventa->from('track_audios_service');
		$this->db_file_solventa->where("numero_solicitud = '$telefono'");
		$this->db_file_solventa->order_by("fecha_audio","DESC");
		$subQueryA = $this->db_file_solventa->get_compiled_select();
		
        $this->db_file_solventa->select("id_track,nombre_archivo,numero_telefono as numero_solicitud,path_audio,fecha_audio,fecha_track");
		$this->db_file_solventa->from('track_audios_service_twilio');
		$this->db_file_solventa->where("numero_telefono = '$telefono'");
		$this->db_file_solventa->order_by("fecha_audio","DESC");
		$subQueryB = $this->db_file_solventa->get_compiled_select();

		$querytotal = $this->db_file_solventa->query("($subQueryA) UNION ($subQueryB)");
        // print_r($this->db_telefonia->last_query());die;
        if ($querytotal->num_rows()>0) 
            {
                return $querytotal->result_array();
            }else{
                return false;
            } 
    }

    public function casosReportados_get($id_track)
    {
        $query = $this->db_auditoria->select('*')
                ->from('audios_reportados')
                ->where('id_audio', $id_track);
                
        return $query->get()->row();
    }

    public function getAuditoriasPorAuditorSearch($id_auditor, $params = null)
    {
        $query = $this->db_auditoria->select('auditoria.id, auditoria.fecha_auditoria,(SELECT nombre_apellido FROM gestion.operadores WHERE gestion.operadores.idoperador = auditoria.operador_auditor) AS operador_auditor,
        auditoria.observaciones, auditoria.id_solicitud,'
            .$this->db_solicitudes->database.'.solicitante_agenda_telefonica.numero AS numero_telefonico,'
            .$this->db_solicitudes->database.'.solicitante_agenda_telefonica.contacto AS contacto,'
            .$this->db_solicitudes->database.'.solicitud.documento AS documento,'
            .$this->db_gestion->database.'.operadores.nombre_apellido AS operador_asignado,'
            )
        ->from('auditoria')
        ->join(
            $this->db_solicitudes->database.'.solicitud',  
            $this->db_solicitudes->database.'.solicitud.id = '.$this->db_auditoria->database.'.auditoria.id_solicitud', 
            'left'
        )
        ->join(
            $this->db_solicitudes->database.'.solicitante_agenda_telefonica',  
            $this->db_solicitudes->database.'.solicitante_agenda_telefonica.documento = '.$this->db_solicitudes->database.'.solicitud.documento', 
            'left'
        )
        ->join(
            $this->db_gestion->database.'.operadores', 
            $this->db_gestion->database.'.operadores.idoperador ='.$this->db_solicitudes->database.'.solicitud.operador_asignado', 
            'left'
        )
        ->join(
            $this->db_auditoria->database.'.audios_auditados', 
            $this->db_auditoria->database.'.audios_auditados.id_auditoria ='. $this->db_auditoria->database.'.auditoria.id', 
            'left'
        )
        ->where($this->db_solicitudes->database.'.solicitante_agenda_telefonica.fuente', 'PERSONAL DECLARADO')
        ->where('operador_auditor', $id_auditor)
        
        ->where($this->db_auditoria->database.'.audios_auditados.tipo_reporte', 'Auditado');
        if (isset($params["documento"]) && !empty($params["documento"])) {
            $this->db_auditoria->where($this->db_solicitudes->database.'.solicitud.documento', $params["documento"]);
        }

        if (isset($params["telefono"]) && !empty($params["telefono"])) {
            $this->db_auditoria->where($this->db_solicitudes->database.'.solicitante_agenda_telefonica.numero', $params["telefono"]);
        }
        
        $query->order_by($this->db_auditoria->database.'.auditoria.id', 'DESC');
        $query->group_by($this->db_auditoria->database.'.auditoria.id');

        // $query->get();
        // echo $query->last_query();die;
        return $query->get()->result();
    }
}
