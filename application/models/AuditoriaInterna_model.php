<?php
class AuditoriaInterna_model extends CI_Model {
    
    const ESTADO_VIGENTE = 'vigente';
    const ESTADO_MORA = 'mora';
    const ESTADO_CANCELADO = 'cancelado';
       
    public function __construct(){  
        parent::__construct();      
        $this->load->library('Sqlexceptions');
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_telefonia = $this->load->database('telefonia',TRUE);
        $this->db_gestion = $this->load->database('gestion',TRUE);
        $this->db_maestro = $this->load->database('maestro',TRUE);
        $this->Sqlexceptions = new Sqlexceptions();
    }
   

   

    public function simple_list($params = [])
    {

        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('creditos.id, creditos.monto_prestado, creditos.fecha_otorgamiento, creditos.estado, clientes.documento , clientes.nombres, clientes.apellidos');
        
        $this->db->from('clientes');
        $this->db->from('creditos');
        $this->db->from('agenda_telefonica');
        //$this->db->from('agenda_mail');
        
        //$this->db->where('creditos.estado IN ("vigente", "mora")');
        $this->db->where('creditos.id_cliente = clientes.id');
        $this->db->where('agenda_telefonica.id_cliente = clientes.id');
        //$this->db->where('agenda_mail.id_cliente = clientes.id');
        
        $this->db->group_start(); 
        $this->db->or_like('creditos.id', $params['search'], 'both');
        $this->db->or_like('agenda_telefonica.numero', $params['search'], 'both');
        //$this->db->or_like('agenda_mail.cuenta', $params['search'], 'both');
        $this->db->or_like('clientes.documento', (string)$params['search'], 'both');
        $this->db->or_like('clientes.nombres', (string)$params['search'], 'both');
        $this->db->or_like('clientes.apellidos', (string)$params['search'], 'both');
        $this->db->group_end();
        $this->db->group_by('creditos.id');

        $query = $this->db->get();     
        return $query->result_array();
    }


    //get lista de solicitudes por auditar
    public function search_solicitudes_por_auditar($fechaIni, $fechaFin)
    {
            $result = $this->db_telefonia->query("
                SELECT solicitude.*, audios.audio_name, audios.id audio
                FROM gestion.relacion_audios_clientes AS audios,
                    (SELECT `solicitud`.`id`,
                            solicitud.tipo_solicitud,
                            `solicitud`.`documento`,
                            `solicitud`.`nombres`,
                            `solicitud`.`apellidos`,
                            `solicitud`.`telefono`,
                            `referencias`.`telefono` telefono_referencia,
                            `solicitud`.`fecha_alta`,
                            `visado`.`fecha_creacion`,
                            `analisis`.`score`,
                            `desembolso`.`capital_solicitado`,
                            `operadores`.`nombre_apellido` name_agent
                    FROM `solicitudes`.`solicitud` AS `solicitud`
                        JOIN `solicitudes`.`solicitud_visado` AS `visado`
                            ON `solicitud`.`id` = `visado`.`id_solicitud`
                        LEFT JOIN `solicitudes`.`solicitud_referencias` AS `referencias`
                            ON `solicitud`.`id` = `referencias`.`id_solicitud`
                        LEFT JOIN `solicitudes`.`solicitud_analisis` AS `analisis`
                            ON `solicitud`.`id` = `analisis`.`id_solicitud`
                        LEFT JOIN
                        `solicitudes`.`solicitud_condicion_desembolso` AS `desembolso`
                            ON `solicitud`.`id` = `desembolso`.`id_solicitud`
                        LEFT JOIN `gestion`.`operadores` AS `operadores`
                            ON `solicitud`.`operador_asignado` = `operadores`.`idoperador`
                    WHERE `solicitud`.`estado` IN ('APROBADO', 'TRANSFIRIENDO')
                    GROUP BY solicitud.id, referencias.telefono) AS solicitude
                WHERE (   SUBSTRING(REPLACE(audios.telefono, ' ', ''), -10) =
                        SUBSTRING(REPLACE(solicitude.telefono, ' ', ''), -10)
                    OR SUBSTRING(REPLACE(audios.telefono, ' ', ''), -10) =
                        SUBSTRING(REPLACE(solicitude.telefono_referencia, ' ', ''), -10)
                    )
                    AND audios.id NOT IN (SELECT id_audio FROM solicitudes.solicitud_auditoria)
                    AND audios.fecha_hora_llamada BETWEEN '$fechaIni' AND '$fechaFin' 
                GROUP BY solicitude.id
                ORDER BY solicitude.score ASC, solicitude.capital_solicitado DESC
            ");
            //var_dump($this->db_telefonia->last_query());die;
            return $result->result();
    }
/*
    public function search_solicitudes_por_auditar(){
        $result = $this->db_telefonia->query("
        select ll.*, ss.id id_solicitud, ss.fecha_alta, ss.fecha_creacion, ss.id_cliente cliente_solicitud, ss.operador_asignado, ss.estado, ss.telefono, ss.nombres, ss.apellidos, ss.email, ss.documento documento_solicitud, ss.score
        from        (   select a.*, l.skill_result, l.name_agent, l.talk_time from gestion.relacion_audios_clientes a JOIN telefonia.track_llamadas l on a.id_call = l.id_call GROUP BY l.id_call) as ll, 
                    (	select s.*, sr.nombres_apellidos, sr.telefono referencia, sv.fecha_creacion, sa.score from solicitudes.solicitud as s 
                        LEFT JOIN solicitudes.solicitud_referencias sr on s.id = sr.id_solicitud
                        LEFT JOIN solicitudes.solicitud_analisis sa on s.id = sa.id_solicitud 
                        JOIN solicitudes.solicitud_visado as sv on s.id = sv.id_solicitud WHERE s.estado IN ('APROBADO','TRANSFIRIENDO')) as ss
                        
        WHERE
        SUBSTRING(REPLACE(ll.telefono,' ',''), -10) = SUBSTRING(REPLACE(ss.telefono,' ',''), -10) 
        AND
        ll.fecha_hora_llamada BETWEEN ss.fecha_alta and ss.fecha_creacion
        and 
        ll.id not in (SELECT id_audio FROM solicitudes.solicitud_auditoria) 
        GROUP BY ll.id_call
        ORDER BY ss.score ASC, ss.capital_");
        //$query = $this->db_telefonia->get();
        //var_dump($this->db_telefonia->last_query());die;
        return $result->result();
    }
*/
    public function search_operadores()
    {
        $criterios2 = array ('1','4','5','6');
        $this->db_gestion->select('idoperador,nombre_apellido');
        $this->db_gestion->from('operadores');
        $this->db_gestion->where('estado',1);
        $this->db_gestion->where_in('tipo_operador', $criterios2);
        $query = $this->db_gestion->get();     
        //var_dump($this->db_gestion->last_query());die;
        return $query->result_array();
    }


    public function auditoria_por_operador($id)
    {

        $this->db_gestion->select('a.id_auditoria,
            a.id_cliente,
            a.id_solicitud,
            DATE_FORMAT(a.fecha, "%d/%m/%Y") fecha,
            a.hora,
            a.documento,
            a.tipo,
            a.estado,
            a.tipo_operacion,
            CONCAT(s.nombres,s.apellidos) solicitante,
            DATE_FORMAT(s.fecha_alta, "%d/%m/%Y") fech_alta, 
            DATE_FORMAT(sv.fecha_creacion, "%d/%m/%Y") fech_aprobado,
            a.buro,
            a.cuenta,
            a.reto,
            sc.capital_solicitado monto_aprobado');
        $this->db_gestion->from('auditoria_interna_online a');
        
        $this->db_gestion->join('solicitudes.solicitud s',' a.id_solicitud = s.id','left');
        $this->db_gestion->join('solicitudes.solicitud_visado sv',' sv.id_solicitud = a.id_solicitud','left');
        $this->db_gestion->join('solicitudes.solicitud_condicion_desembolso sc',' sc.id_solicitud = a.id_solicitud','left');
        $this->db_gestion->where('a.bstatus',1);
        $this->db_gestion->where('a.id_operador',$id);
        $query = $this->db_gestion->get();     
        // var_dump($this->db_gestion->last_query());die;
        return $query->result_array();   
    }

    public function getTlfClientes($buscar) {
        
        if($buscar['operacion'] === 'COBRANZA') {
            $this->db_maestro->select('CONCAT(IFNULL(T.areaCode,""), A.numero ) AS telefono,T.areaCode AS indicativo,A.id_cliente,A.tipo,
                A.fuente');
            $this->db_maestro->from('agenda_telefonica AS A');
            $this->db_maestro->join('parametria.tel_codigoarea AS T','CONVERT ( A.ciudad USING utf8 ) LIKE CONCAT( "%", CONVERT ( T.ciudad_tel USING utf8 ), "%" )','left');
            $this->db_maestro->where('A.id_cliente',$buscar['id_cliente']);
            $this->db_maestro->where('A.estado',1);
            $query = $this->db_maestro->get();
        } else {
            $this->db_solicitudes->select('A.telefono, "PERSONAL" AS fuente');
            $this->db_solicitudes->from('solicitud AS A');
            $this->db_solicitudes->where('A.id', $buscar['id_solicitud']);
            $query = $this->db_solicitudes->get();
        }
        
        //var_dump($this->db_maestro->last_query());die;
        return $query->result_array();

    }

    public function getjornadaActual($id_auditor){
        $this->db_solicitudes->select('*');
        $this->db_solicitudes->from('solicitudes.solicitud_auditoria');
        $this->db_solicitudes->where('id_auditor',$id_auditor);
        //$this->db_solicitudes->where('fecha_bjornada', 'DATE_FORMAT('.date("Y-m-d").', "%Y/%m/%d" ) ');
        $this->db_solicitudes->like('fecha_bjornada', date("Y-m-d H:i:s"));
        //$this->db_solicitudes->where('fecha_bjornada like',date("Y-m-d H:i:s"));
        $query = $this->db_solicitudes->get();     
        //var_dump($this->db_solicitudes->last_query());die;
        return $query->result_array();

    }

    public function get_auditoria($parametros){
        $this->db_solicitudes->select('*');
        $this->db_solicitudes->from('solicitud_auditoria');
        if(isset($parametros['id_auditoria'])){ $this->db_solicitudes->where('id', $parametros['id_auditoria']);}
        $query = $this->db_solicitudes->get();
        return $query->result();
    }

    
    //guardar Auditoria
    function setGuardaAuditoria($data){
        $this->db_solicitudes->insert('solicitud_auditoria',$data);

        if ($this->db_solicitudes->affected_rows() > 0) {
            return $this->db_solicitudes->insert_id();
        }
        else{
            return -1;
        }
    }

    /*** Se obtienen las auditorías realizadas por el auditor logueado ***/
    public function getAuditoriasRealizadas( $id_auditor, $id_operador )
    {
        $this->db_gestion->select('auditoria.id,
            auditoria.id_solicitud,
            auditoria.fecha_auditado,
            auditoria.observaciones,
            auditoria.gestion,
            auditoria.tlf_cliente,
            auditoria.tipo_auditoria,
            auditoria.proceso,
            operadores.nombre_apellido'
        );
        $this->db_gestion->from('solicitudes.solicitud_auditoria AS auditoria');
        $this->db_gestion->join('gestion.auditoria_interna_online AS gestionAuditada', 'auditoria.id_track_auditoria = gestionAuditada.id_auditoria');
        $this->db_gestion->join('gestion.operadores AS operadores', 'operadores.idoperador = gestionAuditada.id_operador');
        $this->db_gestion->where('auditoria.id_auditor', $id_auditor);
        $this->db_gestion->where('gestionAuditada.id_operador', $id_operador);
        $this->db_gestion->where('DATEDIFF(NOW(), auditoria.fecha_auditado) = 0');
        $query = $this->db_gestion->get();     
        //var_dump($this->db_gestion->last_query());die;
        return $query->result_array();   
    }

    /*** Se actualiza la auditoría ***/
    function setActualizarAuditoria( $data, $id_auditoria ) {
        $this->db_solicitudes->where( 'id', $id_auditoria);
        $this->db_solicitudes->update('solicitud_auditoria', $data);

        if ($this->db_solicitudes->affected_rows() > 0) {
            return $this->db_solicitudes->affected_rows();
        }
        else{
            return -1;
        }
    }
    /******************************************/
	/*** Se obtienen los audios por auditar ***/
	/******************************************/
    public function getLlamadasPorAuditar($id_audio) {
        $result = $this->db_telefonia->query("
            SELECT rac.id,
                rac.fecha_hora_llamada,
                tll.name_agent,
                (SUBSTRING(REPLACE(rac.telefono, ' ', ''), - 10)) AS telefono,
                sat.contacto,
                sat.fuente,
                tll.talk_time,
                tll.tipo_llamada,
                tll.who_hangs_up,
                tll.central,
                rac.path_audio
            FROM gestion.relacion_audios_clientes rac
                INNER JOIN telefonia.track_llamadas tll ON rac.id_call = tll.id_call
                LEFT JOIN solicitudes.solicitante_agenda_telefonica sat ON sat.documento = rac.documento
            WHERE rac.id = $id_audio
                AND SUBSTRING(REPLACE(rac.telefono, ' ', ''), - 10) = sat.numero;
        ");
        return $result->result();
    }
    /*******************************************************************************/
    /*** Se obtienen las auditorías realizadas por el auditor logueado POSTERIOR ***/
    /*******************************************************************************/
    public function getAuditoriaAudioPosterior( $id_auditor, $id_audio ) {
        $this->db_gestion->select('sa.id,
            sa.id_solicitud,
            sa.fecha_auditado,
            sa.observaciones,
            sa.gestion,
            sa.tlf_cliente,
            sa.tipo_auditoria,
            sa.proceso,
            "" AS nombre_apellido'
        );
        $this->db_gestion->from('solicitudes.solicitud_auditoria sa');
        $this->db_gestion->where('sa.id_auditor', $id_auditor);
        $this->db_gestion->where('sa.id_audio', $id_audio);
        $resultado = $this->db_gestion->get();     
        return $resultado->result_array();   
    }
}
?>