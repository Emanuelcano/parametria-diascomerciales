<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud_m extends CI_Model {

    const ESTADO_APROBADO      = 'APROBADO';
    const ESTADO_PAGADO        = 'PAGADO';
    const ESTADO_RECHAZADO     = 'RECHAZADO';
    const ESTADO_VALIDADO      = 'VALIDADO';
    const ESTADO_VERIFICADO    = 'VERIFICADO';
    const ESTADO_TRANSFIRIENDO = 'TRANSFIRIENDO';
    //imagenes requeridas
    const TRANSFERENCIA_DESEMBOLSO = 16;

    protected $_table_name = 'solicitud';
    protected $_primary_key = 'id';
    protected $_order_by = 'id';

    public function __construct() {
        parent::__construct();
        $this->db_gestion = $this->load->database('gestion', TRUE);
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_parametria  = $this->load->database('parametria', TRUE);
        $this->db_usuarios_solventa = $this->load->database('usuarios_solventa',TRUE);
        $this->db_chat = $this->load->database('chat',TRUE);
        $this->db_chatbot = $this->load->database('chatbot',TRUE);
    }

    public function buscarSolicitud_get(){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('*');  
        $this->db->join('solicitudes.solicitud_condicion_desembolso sc','e.id = sc.id_solicitud', 'inner');
        $this->db->join('solicitudes.solicitud_datos_bancarios db','e.id = db.id_solicitud', 'inner');
        $this->db->join('parametria.bank_entidades ban','db.id_banco = ban.id_banco', 'inner');
        $this->db->join('parametria.bank_tipocuenta bat','db.id_tipo_cuenta = bat.id_TipoCuenta', 'inner');
        $this->db->join('solicitudes.comprobante_credito comp','e.id = comp.id_solicitud_compr', 'left');
        $this->db->order_by("comp.id", "desc");
        $this->db->where('e.estado =', "APROBADO");
        $this->db->or_where('e.estado =',"TRANSFIRIENDO");
        $query = $this->db->get('solicitudes.solicitud e');
        //echo $sql = $this->db->last_query();die;
        return $query->result();

    }

    public function get_solicitudes_transito($documento)
    {
        $sql = "SELECT * FROM `solicitud` WHERE `documento` LIKE '$documento' AND (`estado` IN ('APROBADO','VALIDADO','VERIFICADO','ANALISIS') OR estado IS NULL)";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }
    
    public function findSolicitudes(){
        $this->db = $this->load->database('solicitudes', TRUE);
        //$subq = '(SELECT MAX(patch_imagen) FROM solicitud_imagenes WHERE solicitud_imagenes.id_imagen_requerida='.$this->db->escape(self::TRANSFERENCIA_DESEMBOLSO).' AND solicitud_imagenes.id_solicitud=ids) AS comprobante';
        $this->db->select('e.id as id_solicitud, e.nombres, e.apellidos, '
                . 'e.fecha_ultima_actividad, e.documento, ban.Nombre_Banco, '
                . 'db.numero_cuenta, bat.codigo_TipoCuenta, sc.capital_solicitado, '
                . 'e.estado, '
                . 'max(comp.id_imagen_requerida), max(comp.id) as comprobante, max(comp.patch_imagen) as nombre_comp');  
        $this->db->join('solicitudes.solicitud_condicion_desembolso sc','e.id = sc.id_solicitud', 'inner');
        $this->db->join('solicitudes.solicitud_datos_bancarios db','e.id = db.id_solicitud', 'inner');
        $this->db->join('parametria.bank_entidades ban','db.id_banco = ban.id_banco', 'inner');
        $this->db->join('parametria.bank_tipocuenta bat','db.id_tipo_cuenta = bat.id_TipoCuenta', 'inner');
        $this->db->join('solicitudes.solicitud_imagenes comp','e.id = comp.id_solicitud and comp.id_imagen_requerida='.$this->db->escape(self::TRANSFERENCIA_DESEMBOLSO),'left');
        $this->db->order_by("e.fecha_ultima_actividad", "ASC");
        $this->db->group_by("e.id, ban.Nombre_Banco, db.numero_cuenta,  bat.codigo_TipoCuenta, sc.capital_solicitado, e.estado");
        $this->db->where('e.estado =', "APROBADO");
        $this->db->or_where('e.estado =',"TRANSFIRIENDO");
        $query = $this->db->get('solicitudes.solicitud e');
        //echo $sql = $this->db->last_query();die;
        return $query->result();

    }

    public function getSolicitudes($params){
        //(SELECT solicitud_reto.resultado FROM solicitud_reto WHERE solicitud_reto.id_solicitud = IDS ORDER BY solicitud_reto.id DESC LIMIT 1) AS reto_resultado,
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.*,solicitud.resultado_ultimo_reto as reto_resultado,
                            solicitud.id AS IDS,
                            parametria.ident_tipodocumento.*,
                            parametria.situacion_laboral.*,
                            datos_bancarios.respuesta banco_resultado,
                            datos_bancarios.id_banco id_banco,
                            datos_bancarios.numero_cuenta banco_cuenta,
                            datos_bancarios.id_tipo_cuenta id_tipo_cuenta,
                            datos_valores.capital_solicitado valor_transaccion
                            ');
        $this->db->from('solicitudes.solicitud');
        $this->db->join('parametria.ident_tipodocumento', 'parametria.ident_tipodocumento.id_tipoDocumento = solicitud.id_tipo_documento','left');
        $this->db->join('parametria.situacion_laboral', 'parametria.situacion_laboral.id_situacion = solicitud.id_situacion_laboral','left');
        $this->db->join('solicitudes.solicitud_datos_bancarios datos_bancarios','datos_bancarios.id_solicitud = solicitud.id', 'left');
        $this->db->join('solicitudes.solicitud_condicion_desembolso datos_valores','datos_valores.id_solicitud = solicitud.id', 'left');
        $this->db->join('(SELECT * from `solicitud_reto` order by solicitud_reto.id DESC limit 1) AS solicitud_reto ', 'solicitud_reto.id_solicitud = solicitud.id','left');

        if(isset($params['id'])){ $this->db->where('solicitud.id',$params['id']);}
        if(isset($params['respuesta_analisis'])){ $this->db->where('solicitud.respuesta_analisis',$params['respuesta_analisis']);}

        if(isset($params['!='])){ $this->not_equal($params['!=']); }
        if(isset($params['OR'])){ $this->or_where($params['OR']); }
        if(isset($params['OR_LIKE_BOTH'])){ $this->or_like_both($params['OR_LIKE_BOTH']); }
        //if(isset($params['OR_LIKE_NONE'])){ $this->or_like_none($params['OR_LIKE_NONE']); }

        if(isset($params['>='])){ $this->more_than($params['>=']); }
        if(isset($params['<='])){ $this->less_than($params['<=']); }
        if(isset($params['LITERAL'])){ $this->literal($params['LITERAL']); }
        if(isset($params['params']['paso'])){ $this->db->where('solicitud.paso',$params['params']['paso']);}
        if(isset($params['params']['estado'])){ $this->db->where('solicitud.estado',$params['params']['estado']);}
        if(isset($params['params']['banco'])){ $this->db->where('datos_bancarios.id_banco',$params['params']['banco']);}
        if(isset($params['params']['limit'])){$this->db->limit($params['params']['limit']);}

        $this->db->group_by("solicitud.id");
        
        if(isset($params['order'])){ $this->order($params['order']);}

        $query = $this->db->get();
       // echo $sql = $this->db->last_query();die;
      
      
        return $query->result_array();
    }
    
    public function listado_solicitudes_por_visar($fecha_desde, $fecha_hasta, $aut_dep_ind)
    {
        // Este metodo muestra el detalle de los casos pendiente de visado y los filtra dependiendo si son automaticos, dependientes o independientes. Tambien muestra el total de casos pendientes de visar.

        $this->db = $this->load->database('solicitudes', TRUE);
        // GUARDO EN UNA VARIABLES LOS IDS QUE VOY A FILTRAR EN EL WHERE
        $casos_por_visar = $this->casos_por_visar_where($aut_dep_ind, $fecha_desde, $fecha_hasta);

        $this->db->select("solicitud.*,datos_bancarios.respuesta AS banco_resultado,situacion.nombre_situacion,gestion.operadores.nombre_apellido AS operador_nombre_pila,IFNULL( last_track.observaciones, ' ' ) AS last_track");
        $this->db->from('solicitudes.solicitud as solicitud');
        $this->db->join('solicitudes.solicitud_datos_bancarios as datos_bancarios','datos_bancarios.id_solicitud = solicitud.id', 'left');
        $this->db->join('gestion.operadores as operadores','operadores.idoperador = solicitud.operador_asignado', 'left');
        $this->db->join('solicitudes.solicitud_ultima_gestion last_track','last_track.id_solicitud = solicitud.id', 'left');
        $this->db->join('parametria.situacion_laboral situacion','situacion.id_situacion= solicitud.id_situacion_laboral', 'left');
        $this->db->where("solicitud.id IN ($casos_por_visar)");

        $query = $this->db->get();

        // var_dump($this->db->last_query());die;
        return $query->result_array();

    }

    private function casos_por_visar_where($tipo, $fecha_desde, $fecha_hasta)
    {
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_gestion = $this->load->database('gestion', TRUE);

        $subquerySolicitudVisado = $this->db_solicitudes->select('id_solicitud')
			->from('solicitud_visado')
            ->get_compiled_select();


        $subqueryTrackGestion = $this->db_gestion->select('id_solicitud')
			->from('gestion.track_gestion')
            ->where('id_tipo_gestion',130)
            ->where("fecha BETWEEN '$fecha_desde' and '$fecha_hasta'")
            ->get_compiled_select();

        $subqueryTrackGestionNOTIN = $this->db_gestion->select('id_solicitud')
			->from('gestion.track_gestion')
            ->where('id_tipo_gestion',130)
            ->where("fecha > '$fecha_hasta'")
            ->get_compiled_select();

        $query = $this->db_solicitudes->select('id');
        $query->from('solicitud');
        $query->where('estado', 'APROBADO');
        if ($tipo == 'AUT') {
            $query->where('operador_asignado', 108);
        }
        if ($tipo == 'DEP') {
            $query->where('operador_asignado != ', 108);
            $query->where('id_situacion_laboral !=', 3);
        }
        if ($tipo == 'IND') {
            $query->where('operador_asignado != ', 108);
            $query->where('id_situacion_laboral =', 3);
        }
        $query->where("id NOT IN ($subquerySolicitudVisado)");
        $query->where("id IN ($subqueryTrackGestion)");
        $query->where("id NOT IN ($subqueryTrackGestionNOTIN)");

        return $query->get_compiled_select();
    }
 
    public function simple_list($params = [], $limit = NULL, $offset = NULL){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.*,datos_bancarios.respuesta AS banco_resultado,situacion.nombre_situacion,gestion.operadores.nombre_apellido AS operador_nombre_pila,IFNULL( last_track.observaciones, "" ) AS last_track');
        $this->db->from('solicitudes.solicitud as solicitud');

        $this->db->join('solicitudes.solicitud_datos_bancarios as datos_bancarios','datos_bancarios.id_solicitud = solicitud.id', 'left');
        $this->db->join('gestion.operadores as operadores','operadores.idoperador = solicitud.operador_asignado', 'left');
        $this->db->join('solicitudes.solicitud_ultima_gestion last_track','last_track.id_solicitud = solicitud.id', 'left');
        $this->db->join('parametria.situacion_laboral situacion','situacion.id_situacion= solicitud.id_situacion_laboral', 'left');
        
        if(isset($params['VISADO'])||isset($params['NOVISADO'])){
            $this->db->join('solicitudes.solicitud_visado solicitud_visado','solicitud_visado.id_solicitud = solicitud.id', 'left');
            if(isset($params['VISADO'])){
                $this->db->where('solicitud_visado.visado = 1');
            }else{
                $this->db->where('solicitud_visado.visado is null');
            }
        }
        $this->db->group_start(); 
        if(isset($params['LITERAL2'])){ $this->db->where($params['LITERAL2']); }
        if(isset($params['id'])){ $this->db->where('solicitud.id',$params['id']);}
        if(isset($params['solicitud.estado'])){ $this->db->where('solicitud.estado',$params['solicitud.estado']);}
        if(isset($params['solicitud.respuesta_analisis'])){ $this->db->where('solicitud.respuesta_analisis',$params['solicitud.respuesta_analisis']);}
        if(isset($params['solicitud.resultado_ultimo_reto'])){ $this->db->where('solicitud.resultado_ultimo_reto',$params['solicitud.resultado_ultimo_reto']);}
        if(isset($params['datos_bancarios.respuesta'])){ $this->db->where('datos_bancarios.respuesta',$params['datos_bancarios.respuesta']);}
        if(isset($params['solicitud.operador_asignado'])){ $this->db->where('solicitud.operador_asignado',$params['solicitud.operador_asignado']);}
        if(isset($params['solicitud.tipo_solicitud'])){ $this->db->where('solicitud.tipo_solicitud',$params['solicitud.tipo_solicitud']);}
        if(isset($params['visado_validacion'])){ $this->db->where($params['visado_validacion']);}
        
        if(isset($params['!='])){ $this->not_equal($params['!=']); }
        if(isset($params['OR'])){ $this->or_where($params['OR']); }
        if(isset($params['OR_LIKE_BOTH'])){ $this->or_like_both($params['OR_LIKE_BOTH']); }
        //if(isset($params['OR_LIKE_NONE'])){ $this->or_like_none($params['OR_LIKE_NONE']); }
        if(isset($params['LIKE_BOTH'])){ $this->like_both($params['LIKE_BOTH']); }
        if(isset($params['LIKE'])){ $this->like($params['LIKE']); }
        if(isset($params['NOT_IN'])){ $this->not_in($params['NOT_IN']); }
        
        if(isset($params['>='])){ $this->more_than($params['>=']); }
        if(isset($params['<='])){ $this->less_than($params['<=']); }
        if(isset($params['LITERAL'])){ $this->literal($params['LITERAL']); }
        $this->db->group_end();
        
        $this->db->group_by("solicitud.id, datos_bancarios.respuesta ");
        if(isset($params['order'])){ $this->order($params['order']);}

        if(isset($limit) && isset($offset))
        {
            $this->db->limit($offset, $limit);
        }else if(isset($limit) && !isset($offset))
        {
            $this->db->limit($limit);
        } else {
            $this->db->limit('300');
        }

        $query = $this->db->get();
        
        //var_dump($this->db->last_query());die;
      
        return $query->result_array();
    }

    public function getSolicitudAnalisis($params)
    {

        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('*');
        $this->db->from('solicitud_analisis');
        $this->db->where('id_solicitud', $params['id']);
        $query = $this->db->get();

        //return $this->db->last_query();
        $res = $query->result_array();
        return $res;

    }

    public function get_nombre_operador($id_operador){
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->select('descripcion');
        $this->db->from('tipo_operador');
        $this->db->where('idtipo_operador', $id_operador);
        $query = $this->db->get();
        //return $this->db->last_query();
        $res = $query->result_array();
        return $res;
    }

    public function getSolicitud($id_solicitud)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.*, parametria.ident_tipodocumento.*, parametria.situacion_laboral.*, solicitud_reto.resultado reto_resultado');
        $this->db->from('solicitud');
        $this->db->where('solicitud.id', $id_solicitud);
        $this->db->join('parametria.ident_tipodocumento', 'parametria.ident_tipodocumento.id_tipoDocumento = solicitud.id_tipo_documento','left');
        $this->db->join('parametria.situacion_laboral', 'parametria.situacion_laboral.id_situacion = solicitud.id_situacion_laboral','left');
        $this->db->join('solicitud_reto', 'solicitud_reto.id_solicitud = solicitud.id','left');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getVeriff_scan($id_solicitud){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('veriff_scan.id_solicitud');
        $this->db->from('solicitudes.veriff_scan');
        $this->db->where('veriff_scan.id_solicitud='.$id_solicitud);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function solicitudes_pagare($id_solicitud)
    {
        $sql = "SELECT * FROM solicitud WHERE id = $id_solicitud  AND ((pagare_enviado = 1 AND pagare_firmado = 1 ) OR (documento IN (SELECT documento FROM pagare_revolvente  WHERE firmado = 1)))";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }

    public function solicitudes_crear_pagare($id_solicitud)
    {
        $sql = "SELECT  *  FROM solicitud WHERE  id = $id_solicitud AND paso = 16  AND ( ( pagare_enviado = 0  AND codigo_firma IS NULL ) OR id NOT IN (  SELECT id_solicitud  FROM  solicitud_imagenes  WHERE   id_solicitud = $id_solicitud  AND id_imagen_requerida IN (24, 29) )) AND documento NOT IN ( SELECT  documento FROM pagare_revolvente WHERE firmado = 1)";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }

    public function solicitudes_firmar_pagare($id_solicitud)
    {
        $sql = "SELECT * FROM solicitud WHERE id = $id_solicitud AND paso = 16 AND validacion_telefono = 1 AND id NOT IN ( SELECT id_solicitud FROM solicitud_imagenes WHERE id_solicitud = $id_solicitud AND  id_imagen_requerida IN (25, 30) ) AND documento NOT IN ( SELECT documento FROM pagare_revolvente WHERE firmado = 1 )";
        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }
    
    public function get_casos_aprobacion_automatica_no_bancolombia(){
        $sql ="SELECT * FROM solicitud WHERE 
            fecha_alta BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 2 HOUR)
            AND id_situacion_laboral = 1 
            AND tipo_solicitud = 'PRIMARIA' 
            AND respuesta_analisis LIKE 'APROBADO' 
            AND estado NOT IN ( 'APROBADO', 'TRANSFIRIENDO', 'RECHAZADO', 'PAGADO', 'ANULADO') 
            AND pagare_enviado = 1 
            AND pagare_firmado = 1 
            AND id IN (
                SELECT 
                    id_solicitud 
                FROM 
                    veriff_scan 
                WHERE respuesta_match = 'approved' ) AND id IN ( SELECT id_solicitud FROM solicitud_datos_bancarios WHERE id_banco != 4) AND id NOT IN (
                    SELECT 
                        id_solicitud 
                    FROM 
                        gestion.`track_gestion` 
                    WHERE 
                        `observaciones` LIKE '%[APROBADO][AUTOMATICO CASO COMPLETO]%'
                ) 
                AND id NOT IN (
                    SELECT 
                        id_solicitud 
                    FROM 
                        solicitud_analisis 
                    WHERE 
                        antiguedad_laboral = 0 
                        OR antiguedad_laboral IS NULL
                ) ORDER BY  fecha_alta ASC";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result();
    }

    public function get_casos_aprobacion_automatica_bancolombia(){
        $sql ="SELECT * FROM solicitud WHERE 
            fecha_alta BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY) AND DATE_SUB(CURRENT_DATE(), INTERVAL 2 HOUR)
            AND id_situacion_laboral = 1 
            AND tipo_solicitud = 'PRIMARIA' 
            AND respuesta_analisis LIKE 'APROBADO' 
            AND estado NOT IN ( 'APROBADO', 'TRANSFIRIENDO', 'RECHAZADO', 'PAGADO', 'ANULADO') 
            AND pagare_enviado = 1 
            AND pagare_firmado = 1 
            AND id IN (
                SELECT 
                    id_solicitud 
                FROM 
                    veriff_scan 
                WHERE respuesta_match = 'approved' ) AND id IN ( SELECT id_solicitud FROM solicitud_datos_bancarios WHERE id_banco = 4) AND id NOT IN (
                    SELECT 
                        id_solicitud 
                    FROM 
                        gestion.`track_gestion` 
                    WHERE 
                        `observaciones` LIKE '%[APROBADO][AUTOMATICO CASO COMPLETO]%'
                ) 
                AND id NOT IN (
                    SELECT 
                        id_solicitud 
                    FROM 
                        solicitud_analisis 
                    WHERE 
                        antiguedad_laboral = 0 
                        OR antiguedad_laboral IS NULL
                ) ORDER BY  fecha_alta ASC";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result();
    }

    public function getVeriff_scan_all($id_solicitud, $param=[]){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('*');
        $this->db->from('solicitudes.veriff_scan');
        $this->db->where('veriff_scan.id_solicitud='.$id_solicitud);
        if(isset($param['respuesta_match'])) {  $this->db->where("veriff_scan.respuesta_match in (".$param['respuesta_match'].")"); };
        $query = $this->db->get();
        //echo $this->db->last_query();die;

        return $query->result_array();
    }

    public function getSolicitud_desembolso($id_solicitud){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud_condicion_desembolso.id_solicitud');
        $this->db->from('solicitudes.solicitud_condicion_desembolso');
        $this->db->where('solicitud_condicion_desembolso.id_solicitud='.$id_solicitud);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getReferencia($id_solicitud){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('referencias.id_solicitud, solicitud.id_situacion_laboral');
        $this->db->from('solicitudes.solicitud_referencias as referencias, solicitudes.solicitud as solicitud');
        $this->db->where('referencias.id_solicitud= solicitud.id and solicitud.id='.$id_solicitud.' and solicitud.id_situacion_laboral in(1,4,7)');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getSolicitud_Laboral($id_solicitud){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.id_situacion_laboral');
        $this->db->from('solicitudes.solicitud');
        $this->db->where('solicitud.id ='.$id_solicitud.' and solicitud.id_situacion_laboral=3');
        $query = $this->db->get();
        return $query->result_array();
    }


    /*** Obtiene las imágenes  ***/
    public function existe_imagen($id_solicitud, $param) {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('si.id, si.patch_imagen');
        $this->db->from('solicitud_imagenes si');
        if (isset($param['imagen'])) {
            $this->db->where('si.id_imagen_requerida', $param['imagen']);
        }
        $this->db->where('si.id_solicitud', $id_solicitud);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*** Obtiene las imágenes si existen de la certificación bancaria ***/
    public function getImagenSolicitud($id_solicitud) {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('si.id, si.patch_imagen');
        $this->db->from('solicitud_imagenes si');
        $this->db->where('si.id_imagen_requerida', 17);
        $this->db->where('si.id_solicitud', $id_solicitud);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*** Se obtiene el Buro por el que se hará la validación ***/
    public function getBuro($id_solicitud) {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('sa.buro');
        $this->db->from('solicitud_analisis sa');
        $this->db->where('sa.id_solicitud', $id_solicitud);
        $query = $this->db->get();
        return $query->result_array();
    }

    /*** Actualiza la antiguedad de la cuenta en meses ***/
    public function actualizarAntiguedadCuenta($id, $data) {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id_solicitud', $id);
        $update = $this->db->update('solicitud_analisis', $data);
        return $update;
    }

    public function getDatosBancarios($id_solicitud)
    {
        $this->db =  $this->load->database('parametria',TRUE);
        $this->db->select('*');
        $this->db->from('solicitudes.solicitud_datos_bancarios');
        $this->db->where('id_solicitud', $id_solicitud);
        $this->db->join('bank_entidades', 'bank_entidades.id_banco = solicitud_datos_bancarios.id_banco','left');
        $this->db->join('bank_tipocuenta', 'bank_tipocuenta.id_TipoCuenta = solicitud_datos_bancarios.id_tipo_cuenta','left');
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }

    public function get_error_jumio($id_solicitud)
    {
        $this->db =  $this->load->database('solicitudes',TRUE);
        $this->db->select('ej.descripcion');
        $this->db->from('solicitudes.jumio_tracks jt');
        $this->db->join('parametria.errores_jumio ej', 'ej.codigo_error = jt.codigo_error','left');
        $this->db->where('id_solicitud', $id_solicitud);
        $query = $this->db->get();
        $res = $query->result_array();
        // echo $sql = $this->db->last_query();die;
        // var_dump($res); die;
        return $res;
    }

    public function validation_jumio($id_solicitud)
    {
        $this->db =  $this->load->database('solicitudes',TRUE);
        $this->db->select('*');
        $this->db->from('solicitudes.jumio_scans');
        $this->db->where('id_solicitud', $id_solicitud);
        $this->db->like('respuesta_identificacion','APPROVED_VERIFIED','none');
        $this->db->like('respuesta_supervivencia','true','none');
        $this->db->like('respuesta_match','MATCH','none');
        $query = $this->db->get();
        $res = $query->result_array();
        return count($res);
    }
    
    public function getDatosPersonales($id_solicitud)
    {
        $this->db =  $this->load->database('solicitudes',TRUE);
        $this->db->select('*, parametria.tiempo_residencia.descripcion_tiempo tiempo_de_residencia, parametria.operadores_telefonicos.nombre_operador nombre_operador, parametria.cantidad_hijos.cantidad cantidad, parametria.nivel_estudio.nombre_nivel_estudio nombre_nivel_estudio, parametria.motivo_solicitud.nombre_motivo nombre_motivo');
        $this->db->from('solicitudes.solicitud_datos_personales');
        $this->db->where('id_solicitud', $id_solicitud);
        $this->db->join('parametria.tiempo_residencia', 'parametria.tiempo_residencia.id = solicitudes.solicitud_datos_personales.tiempo_residente','left');
        $this->db->join('parametria.operadores_telefonicos', 'parametria.operadores_telefonicos.id = solicitudes.solicitud_datos_personales.operador_movil','left');
        $this->db->join('parametria.cantidad_hijos', 'parametria.cantidad_hijos.id = solicitudes.solicitud_datos_personales.cantidad_hijos','left');
        $this->db->join('parametria.nivel_estudio', 'parametria.nivel_estudio.id = solicitudes.solicitud_datos_personales.nivel_educativo','left');
        $this->db->join('parametria.motivo_solicitud', 'parametria.motivo_solicitud.id = solicitudes.solicitud_datos_personales.motivo_solicitud','left');
        $query = $this->db->get();
        //echo $sql = $this->db->last_query();die;
        return $query->result_array();
    }

    public function getSolicitudReferencia($id_solicitud)
    {
        $this->load->database('parametria',TRUE);
        $this->db->select('*');
        $this->db->from('solicitudes.solicitud_referencias');
        $this->db->where('id_solicitud', $id_solicitud);
        $this->db->join('parametria.ident_tipodocumento', 'ident_tipodocumento.id_tipoDocumento = solicitud_referencias.id_tipo_documento','left');
        $this->db->join('parametria.parentesco', 'parentesco.id_parentesco = solicitud_referencias.id_parentesco','left');
        $query = $this->db->get();
        //echo $sql = $this->db->last_query();die;
        $res = $query->result_array();
        return $res;
    }

    public function getSolicitudDatosLaborales($id_solicitud)
    {
        $this->db_solicitudes->select('*');
        $this->db_solicitudes->from('solicitud_datos_laborales');
        $this->db_solicitudes->where('id_solicitud', $id_solicitud);
        $query = $this->db_solicitudes->get();
        //echo $sql = $this->db->last_query();die;
        $res = $query->result_array();
        return $res;
    }

    public function getSolicitudCondicion($id_solicitud)
    {
        $this->db->select('*');
        $this->db->from('solicitudes.solicitud_condicion');
        $this->db->where('id_solicitud', $id_solicitud);
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }
    
    public function getSolicitudReferenciaBotones($id_solicitud)
    {
        $this->db->select('*');
        $this->db->from('solicitudes.verificacion_referencia');
        $this->db->where('id_solicitud', $id_solicitud);
        $query = $this->db->get();
        $res = $query->result_array();
        //echo $sql = $this->db->last_query();die;
        return $res;
    }
    
    
/**
 * betza inicio
 */
    public function getSolicitudesReferencia($id_solicitud)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('s.*, p.nombre_parentesco parentesco');
        $this->db->from('solicitud_referencias s');
        $this->db->join('parametria.parentesco p', 's.id_parentesco = p.id_parentesco', 'left');
        $this->db->where('id_solicitud', $id_solicitud);

        $query = $this->db->get();
        return $query->result();
    }

    public function guardar_solicitud_sms($data,$id_solicitud){
        $this->db = $this->load->database('solicitudes', TRUE);
        $update = $this->db->update('solicitud',$data,'id = '.$id_solicitud);
        return $update;
    }

    public function get_configuracion_obligatorias($parametros = []){


        $this->db_parametria->select('*');
        $this->db_parametria->from('controles_solicitudes_obligatorias');
        $this->db_parametria->where('estado', "1");
        if(isset( $parametros['tipo_operador'])) {  $this->db_parametria->where('tipo_operador', $parametros['tipo_operador']); }

        $this->db_parametria->limit(1);
        $query = $this->db_parametria->get(); 
        return $query->result();
    }

    public function add_configuracion_obligatorias($data)
    {
        return $this->db_parametria->insert('controles_solicitudes_obligatorias', $data); 
    }

	public function update_config_gestion_ob($data,$update_rows, $estado_inactivo) 
    {   
        if ($estado_inactivo['estado'] == 0 ) {
            $this->db_parametria->where('estado', 1);
            if(isset($update_rows['tipo_operador'])){$this->db_parametria->where('tipo_operador', $update_rows['tipo_operador']);}
            $this->db_parametria->update('controles_solicitudes_obligatorias', $estado_inactivo);
        }
		 
		if(isset($data->id)){
            $this->db_parametria->where('id', $data->id);
            return $this->db_parametria->update('controles_solicitudes_obligatorias', $update_rows);
		}
    }
	//actualizar estado de todas las solicitudes con el mismo tipo de operador
	public function update_estados_solicitud_add($update_rows) 
    {   
        $this->db_parametria->where('estado', 1);
        $this->db_parametria->where('tipo_operador', $update_rows['tipo_operador']);
        $this->db_parametria->update('controles_solicitudes_obligatorias', $update_rows);
        
    }


    public function find_configuracion_obligatorias_get($filtro = false)
    {
		$this->db_parametria->select('controles_solicitudes_obligatorias.*, gestion.operadores.nombre_apellido,  gestion.tipo_operador.descripcion as tipo_operador_descripcion');
        $this->db_parametria->from('controles_solicitudes_obligatorias');
        $this->db_parametria->order_by('controles_solicitudes_obligatorias.estado', 'DESC');
        $this->db_parametria->order_by('controles_solicitudes_obligatorias.fecha_modificacion', 'DESC');
        $this->db_parametria->join($this->db_gestion->database . '.operadores', $this->db_gestion->database . '.operadores.idoperador = parametria.controles_solicitudes_obligatorias.id_operador');
        $this->db_parametria->join($this->db_gestion->database . '.tipo_operador', $this->db_gestion->database . '.tipo_operador.idtipo_operador = parametria.controles_solicitudes_obligatorias.tipo_operador');
        if(isset($filtro['id'])){$this->db_parametria->where('controles_solicitudes_obligatorias.id', $filtro['id']);}
        if(isset($filtro['estado'])){$this->db_parametria->where('controles_solicitudes_obligatorias.estado', $filtro['estado']);}
        if(isset($filtro['tipo_operador'])){$this->db_parametria->where('controles_solicitudes_obligatorias.tipo_operador', $filtro['tipo_operador']);}
        $query = $this->db_parametria->get();
        $resultado = $query->result();

        return $resultado;
    }

	public function cambiar_estado_gestion_ob($data,$update_rows) 
    {   			
       $this->db_parametria->update('controles_solicitudes_obligatorias', ['estado' => 0, 'id_operador' => 394], "id != {$data->id} AND tipo_operador = {$data->tipo_operador} AND estado = '1'");
	   $this->db_parametria->update('controles_solicitudes_obligatorias', $update_rows, ['id' => $data->id]);
       return $this->db_parametria->affected_rows();
    
    }

    public function get_solicitudes_obligatoria_visado()
    {
        $this->db_solicitudes->select('solicitud.id');
        $this->db_solicitudes->from('solicitudes.solicitud as solicitud, gestion.track_gestion as track');
        // $this->db_solicitudes->join('solicitud_visado as solicitud_visado', 'solicitud_visado.id_solicitud = solicitud.id', 'left');
        $this->db_solicitudes->where('solicitud.id = track.id_solicitud and track.id_tipo_gestion = 130 and track.fecha <= "'. date('Y-m-d H:i:s' , strtotime ( "-5 minutes" , strtotime(date("Y-m-d H:i:s")))).'"');
		$this->db_solicitudes->where("solicitud.id NOT IN (SELECT id_solicitud FROM solicitud_visado)");
        $this->db_solicitudes->where('(solicitud.estado = "APROBADO" )');
        //$this->db_solicitudes->where('solicitud.operador_asignado','108');
        $this->db_solicitudes->where('solicitud.id not in (select id_solicitud from gestion.track_solicitudes_abiertas)');
		$this->db_solicitudes->group_by('solicitud.id');
        $this->db_solicitudes->order_by('track.fecha', 'ASC');
        $this->db_solicitudes->order_by('track.hora', 'ASC');

        $query = $this->db_solicitudes->get(); 
        // echo '<pre>';
		//print_r($this->db_solicitudes->last_query()); exit(0);
        return  $query->result();
    }
	/**
     * insert table track_solicitudes_abiertas para el control de las solicitudes abiertas por visado
     * en la gestion automatica
     */
	public function insert_solicitudes_abiertas($param)
    {
		$this->db_gestion->select('id_solicitud'); 
        $this->db_gestion->from('track_solicitudes_abiertas');
        $this->db_gestion->where('id_solicitud', $param['id_solicitud']);
        $query = $this->db_gestion->get();

        if($this->db_gestion->affected_rows() == 0){

            $this->db_gestion->insert('track_solicitudes_abiertas', $param);

            if($this->db_gestion->affected_rows() != 1){
                return -1;
            } else{
                return $this->db_gestion->insert_id();
            }
        }else{
            return -1;
        }
        
    }
    public function delete_solicitudes_abiertas($param)
    {
		$this->db_gestion->delete('track_solicitudes_abiertas', "fecha_registro <= '". date('Y-m-d H:i:s' , strtotime ( "-60 minutes" , strtotime(date("Y-m-d H:i:s"))))."'" );
		$this->db_gestion->delete('track_solicitudes_abiertas', $param);
       
        if($this->db_gestion->affected_rows() > 0){
            return true;
        } else{
            return false;
        }
		
    }
    



    public function get_solicitudes_gestion_obligatoria($param){
        
        //solicitud relacion operador
        $this->db_solicitudes->select("id_solicitud");
        $this->db_solicitudes->from('gestion.relacion_operador_solicitud');
        $this->db_solicitudes->where('id_operador',$param['id_operador']);
        $this->db_solicitudes->where('estado', 'A');
        $relacion = $this->db_solicitudes->get_compiled_select();


        //solicitudes ya gestionadas
        $this->db_solicitudes->select("id_solicitud");
        $this->db_solicitudes->from('gestion.track_gestion');
        $this->db_solicitudes->where('fecha = CURRENT_DATE ()');
        $this->db_solicitudes->where('hora >= SUBTIME( DATE_FORMAT(NOW(), "%H:%i:%S"), "'.$param['horas'].':00:00" )');
        $gestionadas = $this->db_solicitudes->get_compiled_select();

        //solicitudes para gestionar
        $this->db_solicitudes->select("id_solicitud");
        $this->db_solicitudes->from('solicitudes.solicitud_ultima_gestion');
        $this->db_solicitudes->where("id_operador = ".$param['id_operador']." and ( fecha = CURRENT_DATE () AND hora >= SUBTIME(DATE_FORMAT(NOW(), '%H:%i:%S'), '".$param['horas'].":00:00'))");
        $solicitudesREgestion = $this->db_solicitudes->get_compiled_select();


        // chats con documentos enviados 
        $this->db_solicitudes->select("nc.documento");
        $this->db_solicitudes->from('chat.new_chats nc, chat.received_messages rm ');
        $this->db_solicitudes->where('nc.id = rm.id_chat');
        $this->db_solicitudes->where('id_operador',$param['id_operador']);
        $this->db_solicitudes->where('rm.media_content_type0 is not NULL');
        $this->db_solicitudes->where('rm.fecha_creacion < DATE_SUB( CURRENT_DATE(), INTERVAL '.$param['min_chat'].' MINUTE ) ');
        $this->db_solicitudes->where('nc.documento is not null');
        $documentosChat = $this->db_solicitudes->get_compiled_select();


        //solicitudes de prioridad 0
        $this->db_solicitudes->select("solicitud.id, '0' prioridad");
        $this->db_solicitudes->from('solicitudes.solicitud, solicitudes.veriff_scan');
        $this->db_solicitudes->where('paso IN (16)');
        $this->db_solicitudes->where('operador_asignado',$param['id_operador']);
        $this->db_solicitudes->where('pagare_firmado = 1');
        $this->db_solicitudes->where("estado NOT IN ('APROBADO', 'TRANSFIRIENDO', 'RECHAZADO', 'PAGADO', 'ANULADO')");
        $this->db_solicitudes->where("fecha_alta >= DATE_SUB(CURRENT_DATE (), INTERVAL ".$param['dias']." DAY)");
        $this->db_solicitudes->where('veriff_scan.id_solicitud = solicitud.id');
        $this->db_solicitudes->where("veriff_scan.respuesta_match in ('approved', 'resubmission_requested')");
        $this->db_solicitudes->where("solicitud.id not IN ($solicitudesREgestion)");

        $prioridad_urgente = $this->db_solicitudes->get_compiled_select();


        //solicitudes de prioridad 0
        $this->db_solicitudes->select("solicitud.id, '0' prioridad");
        $this->db_solicitudes->from('solicitudes.solicitud, solicitudes.veriff_scan');
        $this->db_solicitudes->where('paso IN (16)');
        $this->db_solicitudes->where('operador_asignado',$param['id_operador']);
        $this->db_solicitudes->where('pagare_firmado = 1');
        $this->db_solicitudes->where("estado NOT IN ('APROBADO', 'TRANSFIRIENDO', 'RECHAZADO', 'PAGADO', 'ANULADO')");
        $this->db_solicitudes->where("fecha_alta >= DATE_SUB(CURRENT_DATE (), INTERVAL ".$param['dias']." DAY)");
        $this->db_solicitudes->where('veriff_scan.id_solicitud = solicitud.id');
        $this->db_solicitudes->where("veriff_scan.respuesta_match in ('approved', 'resubmission_requested')");
        $this->db_solicitudes->where("solicitud.id IN ($solicitudesREgestion)");

        $this->db_solicitudes->where("documento in ($documentosChat)");


        $prioridad_cero = $this->db_solicitudes->get_compiled_select();


        //solicitudes de prioridad 0 verificacion metamap
         $this->db_solicitudes->select("solicitud.id, '0' prioridad");
         $this->db_solicitudes->from('solicitudes.solicitud, solicitudes.meta_scan');
         $this->db_solicitudes->where('paso IN (16)');
         $this->db_solicitudes->where('operador_asignado',$param['id_operador']);
         $this->db_solicitudes->where('pagare_firmado = 1');
         $this->db_solicitudes->where("estado NOT IN ('APROBADO', 'TRANSFIRIENDO', 'RECHAZADO', 'PAGADO', 'ANULADO')");
         $this->db_solicitudes->where("fecha_alta >= DATE_SUB(CURRENT_DATE (), INTERVAL ".$param['dias']." DAY)");
         $this->db_solicitudes->where('meta_scan.id_solicitud = solicitud.id');
         $this->db_solicitudes->where("meta_scan.identity in ('verified', 'reviewNeeded', 'verification_updated')");
         $this->db_solicitudes->where("solicitud.id IN ($solicitudesREgestion)");

         $prioridad_cero_metamap = $this->db_solicitudes->get_compiled_select();


        //solicitudes de prioridad 1
        $this->db_solicitudes->select("solicitud.id, '1' prioridad");
        $this->db_solicitudes->from('solicitudes.solicitud, solicitudes.veriff_scan');
        $this->db_solicitudes->where('paso IN (16)');
        $this->db_solicitudes->where('operador_asignado',$param['id_operador']);
        $this->db_solicitudes->where('pagare_firmado = 1');
        $this->db_solicitudes->where("estado NOT IN ('APROBADO', 'TRANSFIRIENDO', 'RECHAZADO', 'PAGADO', 'ANULADO')");
        $this->db_solicitudes->where("fecha_alta >= DATE_SUB(CURRENT_DATE (), INTERVAL ".$param['dias']." DAY)");
        $this->db_solicitudes->where('veriff_scan.id_solicitud = solicitud.id');
        $this->db_solicitudes->where("veriff_scan.respuesta_match in ('approved', 'resubmission_requested')");
        //$this->db_solicitudes->group_start();
        $this->db_solicitudes->where("solicitud.id NOT IN ($solicitudesREgestion)");
        //$this->db_solicitudes->or_where("solicitud.id NOT IN (select DISTINCT(id_solicitud) from gestion.track_gestion)");
        //$this->db_solicitudes->group_end();

        $prioridad_uno = $this->db_solicitudes->get_compiled_select();



        //solicitudes de prioridad 2
        $this->db_solicitudes->select("solicitud.id, '2' prioridad");
        $this->db_solicitudes->from('solicitudes.solicitud, solicitudes.veriff_scan');
        $this->db_solicitudes->where('paso IN (16)');
        $this->db_solicitudes->where('operador_asignado',$param['id_operador']);
        $this->db_solicitudes->where('pagare_firmado = 0');
        $this->db_solicitudes->where("estado NOT IN ('APROBADO', 'TRANSFIRIENDO', 'RECHAZADO', 'PAGADO', 'ANULADO')");
        $this->db_solicitudes->where("fecha_alta >= DATE_SUB(CURRENT_DATE (), INTERVAL ".$param['dias']." DAY)");
        $this->db_solicitudes->where('veriff_scan.id_solicitud = solicitud.id');
        $this->db_solicitudes->where("veriff_scan.respuesta_match in ('approved', 'resubmission_requested')");
        //$this->db_solicitudes->group_start();
        $this->db_solicitudes->where("solicitud.id NOT IN ($solicitudesREgestion)");
        //$this->db_solicitudes->or_where("solicitud.id NOT IN (select DISTINCT(id_solicitud) from gestion.track_gestion)");
        //$this->db_solicitudes->group_end();

        $prioridad_dos = $this->db_solicitudes->get_compiled_select();



        //solicitudes de prioridad 3
        $this->db_solicitudes->select("solicitud.id, '3' prioridad");
        $this->db_solicitudes->from('solicitudes.solicitud');
        $this->db_solicitudes->where('paso IN (16)');
        $this->db_solicitudes->where('operador_asignado',$param['id_operador']);
        $this->db_solicitudes->where("estado NOT IN ('APROBADO', 'TRANSFIRIENDO', 'RECHAZADO', 'PAGADO', 'ANULADO')");
        $this->db_solicitudes->where("fecha_alta >= DATE_SUB(CURRENT_DATE (), INTERVAL ".$param['dias']." DAY)");
        $this->db_solicitudes->where("solicitud.id NOT IN (SELECT id_solicitud FROM veriff_scan WHERE respuesta_match in ('approved', 'resubmission_requested'))");
        //$this->db_solicitudes->group_start();
        $this->db_solicitudes->where("solicitud.id NOT IN ($solicitudesREgestion)");
        //$this->db_solicitudes->or_where("solicitud.id NOT IN (select DISTINCT(id_solicitud) from gestion.track_gestion)");
        //$this->db_solicitudes->group_end();

        $prioridad_tres = $this->db_solicitudes->get_compiled_select();


        //solicitudes de prioridad 4
        $this->db_solicitudes->select("solicitud.id, '4' prioridad");
        $this->db_solicitudes->from('solicitudes.solicitud');
        $this->db_solicitudes->where('paso IN (13,10,9,8, 6, 5)');
        $this->db_solicitudes->where('operador_asignado',$param['id_operador']);
        $this->db_solicitudes->group_start();
        $this->db_solicitudes->where("estado != 'RECHAZADO'");
        $this->db_solicitudes->or_where("estado IS NULL");
        $this->db_solicitudes->group_end();

        $this->db_solicitudes->where("fecha_alta >= DATE_SUB(CURRENT_DATE (), INTERVAL ".$param['dias']." DAY)");
        $this->db_solicitudes->where("solicitud.id NOT IN (SELECT id_solicitud FROM veriff_scan WHERE respuesta_match in ('approved', 'resubmission_requested'))");
        //$this->db_solicitudes->group_start();
        $this->db_solicitudes->where("solicitud.id NOT  IN ($solicitudesREgestion)");
        //$this->db_solicitudes->or_where("solicitud.id NOT IN (select DISTINCT(id_solicitud) from gestion.track_gestion)");
        //$this->db_solicitudes->group_end();
        $this->db_solicitudes->order_by('paso', 'DESC');   

        $prioridad_cuatro = $this->db_solicitudes->get_compiled_select();

        $sql = "($prioridad_urgente) UNION ($prioridad_cero) UNION ($prioridad_cero_metamap) UNION ($prioridad_uno) UNION ($prioridad_dos) UNION ($prioridad_tres)  UNION ($prioridad_cuatro)";


        $query = $this->db_solicitudes->query($sql);


        //print_r($this->db_solicitudes->last_query());die;
        return $query->result();

 }

    public function insert_gestion_obligatoria($data){
        $this->db_gestion->insert('gestion.track_gestion_obligatoria', $data);
        $this->db_gestion->insert_id();

        if($this->db_gestion->affected_rows() != 1){
            return -1;
        } else{
            return $this->db_gestion->insert_id();
        }
    }

    public function update_gestion_obligatoria($data, $param)
    {
        if (isset($param['id'])) {  $this->db_gestion->where('id', $param['id']);   }
        if (isset($param['id_solicitud'])) {  $this->db_gestion->where('id_solicitud', $param['id_solicitud']);   }


        $update =$this->db_gestion->update('gestion.track_gestion_obligatoria', $data);

        if($this->db_gestion->affected_rows() < 1){
            return -1;
        } else{
            return $this->db_gestion->affected_rows();
        }
    }
 
/**
 * betza fin
 */
    public function getSolicitudDesembolso($id_solicitud)
    {
        $this->db->select('*');
        $this->db->from('solicitudes.solicitud_condicion_desembolso');
        $this->db->where('id_solicitud', $id_solicitud);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getCondicionSimulador($param =[])
    {
        $this->db->select('*');
        $this->db->from('parametria.condiciones_simulador');
        if (isset($param['id'])) {
            $this->db->where('id',$param['id']);
        }
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function edit($id, $data)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id', $id);
        $update =$this->db->update('solicitud', $data);
        return $update;
    }

    public function editBlock($ids, $data)
    {
        
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where("id in ($ids)");
        $update =$this->db->update('solicitud', $data);
        
        return $update;
    }

    public function edit_relacion_solicitud_operador($ids, $data, $operador, $fecha)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where("id_solicitud in ($ids)");
        $this->db->where("id_operador",$operador);
        $this->db->where("fecha_registro >= '$fecha'");
        $update =$this->db->update('gestion.relacion_operador_solicitud', $data);
        $update = $this->db->affected_rows();
     
        return $update;
    }

    public function get_asignaciones($id_solicitud ){
        $sql = "SELECT *  FROM relacion_operador_solicitud
                    WHERE id_solicitud = $id_solicitud ORDER BY id DESC LIMIT 1";

        $resultado = $this->db_gestion->query($sql);
        return $resultado->result();
    }

    public function delete_asignaciones($id_solicitud ){
        $this->db_gestion->delete('relacion_operador_solicitud', array('id_solicitud' => $id_solicitud));
        $delete = $this->db_gestion->affected_rows();
        return $delete;
    }

    public function get_asignaciones_control($id_operador, $fecha_asignado ){
        $sql = "SELECT * FROM control_asignaciones 
                    WHERE id_operador = $id_operador AND fecha_control = '$fecha_asignado'";

        $resultado = $this->db_gestion->query($sql);
        return $resultado->result();
    }

    public function edit_asignaciones_control($id_operador, $fecha_asignado, $data)
    {
        $this->db_gestion->where("id_operador = $id_operador AND fecha_control = '$fecha_asignado'");
        $update =$this->db_gestion->update('control_asignaciones', $data);
        return $update;
    }

    public function get_solicitudes_asignadas($id_operador, $fecha)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.id');
        $this->db->from('solicitudes.solicitud solicitud, gestion.relacion_operador_solicitud relacion');
        
        $this->db->where('solicitud.id = relacion.id_solicitud');
        $this->db->where('solicitud.operador_asignado', $id_operador);
        $this->db->where("(solicitud.estado not in ('APROBADO','TRANSFIRIENDO','PAGADO','RECHAZADO','ANULADO')  or solicitud.estado is null)");
        $this->db->where("relacion.fecha_registro >= '$fecha'");
        $query = $this->db->get();
        
        //var_dump($this->db->last_query());
        //die;
        return $query->result_array();
    }

    public function order($orders)
    {
        foreach ($orders as $index => $order)
        {
            $ord = (isset($order[1]))? $order[1]: 'DESC';
            $this->db->order_by($order[0], $ord);
        }
    }

    public function not_equal($params)
    {
        foreach($params AS $key => $value)
        {
            $this->db->where($key. '!=', $value);
        }
        return $this;
    }

    public function not_in($params)
    {
        foreach($params AS $key => $value)
        {
            $this->db->where_not_in($key, $value);
        }
        return $this;
    }

    public function or_not_in($params)
    {
        foreach($params AS $key => $value)
        {
            $this->db->or_where_not_in($key, $value);
        }
        return $this;
    }

    public function or_where($params)
    {
         foreach($params AS $key => $value)
        {
            if(isset($value))
            {
                $this->db->or_where($key, $value);
            }else{
                $this->db->or_where($key, $value, FALSE);
            }
        }
        return $this;
    }

    public function like($params)
    {
         foreach($params AS $key => $value)
        {
            $this->db->like($key, $value, 'none');
        }
        return $this;
    }

    public function or_like_both($params)
    {
         foreach($params AS $key => $value)
        {
            $this->db->or_like($key, $value, 'both');
        }
        return $this;
    }

    public function like_none($params)
    {
         foreach($params AS $key => $value)
        {
            $this->db->or_like($key, $value,'none');
        }
        return $this;
    }

    public function like_both($params)
    {
         foreach($params AS $key => $value)
        {
            $this->db->like($key, $value, 'both');
        }
        return $this;
    }
    
    public function less_than($params, $equal=TRUE)
    {
         if($equal)
        { 
            $condition = '<='; 
        }else{
            $condition = '<';
        }
        foreach($params AS $key => $value)
        {
            $this->db->where($key.' '.$condition, $value);
        }
        return $this;
    }
    
    public function more_than($params, $equal=TRUE)
    {
        if($equal)
        { 
            $condition = '>='; 
        }else{
            $condition = '>';
        }
        foreach($params AS $key => $value)
        {
            $this->db->where($key.' '.$condition, $value);
        }
        return $this;
    }

    /**
     * get datos bancarios para txt
     */
    public function getDatosBancariosTXT($solicitud_id = 0){
        $this->db_parametria->select($this->db_parametria->database.'.bank_entidades.*, '.$this->db_solicitudes->database.'.solicitud_datos_bancarios.numero_cuenta, '.$this->db_solicitudes->database.'.solicitud_datos_bancarios.id_tipo_cuenta');
        $this->db_parametria->from('bank_entidades');
        $this->db_parametria->join($this->db_solicitudes->database.'.solicitud_datos_bancarios', $this->db_solicitudes->database.'.solicitud_datos_bancarios.id_solicitud = ' . $solicitud_id);
        $this->db_parametria->where('bank_entidades.id_Banco = '.$this->db_solicitudes->database.'.solicitud_datos_bancarios.id_banco');
        $this->db_parametria->limit(1);

        $query = $this->db_parametria->get();

        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    public function resetTelefono($id)
    {
        $data = array(
                    'cantidad_sms' => 0
                );
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id', $id);
        $update =$this->db->update('solicitud', $data);
        return $update;

    }

    public function resetEmail($id)
    {

        $data = array(
                    'cantidad_mail' => 0
                );
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id', $id);
        $update =$this->db->update('solicitud', $data);
        return $update;

    }

    public function literal($params)
    {
        foreach($params AS $key => $value)
        {
            $this->db->where($value);
        }
        return $this;
    }

    public function updteCondicionDesembolso($id_solicitud, $data){
        $this->db->where('id_solicitud',$id_solicitud);
        $update = $this->db->update('solicitudes.solicitud_condicion_desembolso', $data);
        $update = $this->db->affected_rows();
        return $update;
    }

    public function getSolicitudesBy($params){
            $this->db_solicitudes->select('*');
            $this->db_solicitudes->from("solicitud");

            if(isset($params['id_solicitud'])){ $this->db_solicitudes->where('id',$params['id_solicitud']);}
            if(isset($params['documento'])){ $this->db_solicitudes->where('documento = "'.$params['documento'].'"');}
            if(isset($params['id_credito'])){ $this->db_solicitudes->where('id_credito ', $params['id_credito']);}
            if(isset($params['id_cliente'])){ $this->db_solicitudes->where('id_cliente ', $params['id_cliente']);}
            if(isset($params['limite'])){ $this->db_solicitudes->limit(1);}
            $this->db_solicitudes->order_by('solicitud.id', 'DESC');
            $query = $this->db_solicitudes->get();
            if($query == false){
                $result = [];
            }else{
                $result =  $query->result();
            }
            return $result;
    }
        
//Sabrina Basteiro Inicio

        
    public function getTxt($id_solicitud) {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('*')->from('solicitud_txt');
        $this->db->where('id_solicitud',$id_solicitud);
        $query = $this->db->get();
        //echo $sql = $this->db->last_query();die;    
        return $query->result_array();
    }    
        
    //Insertar solicitud en tabla de visado
    public function insertarVisado($data) {       
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->insert('solicitudes.solicitud_visado', $data);
        return $this->db->insert_id();
    }
    
    //Verificar si ya se guardo en la tabla visado la solicitud
    public function existeVisado($id) {
        if($id){
            $this->db = $this->load->database('solicitudes', TRUE);
            $this->db->select('*')->from('solicitud_visado');
            $this->db->where('id_solicitud',$id);
            $this->db->get();
            return $this->db->insert_id();
        }
    }

    public function getVisado($id) {
            $this->db = $this->load->database('solicitudes', TRUE);
            $this->db->select('*')->from('solicitud_visado');
            $this->db->where('id_solicitud',$id);
            $query=$this->db->get();
            return $query->result();
    }
    
    //Actualizo la tabla solicitudes_alertas para agregar el escalado de analisis
    public function actualizarAnalizado($data){ 
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id_solicitud', $data['id_solicitud']);
        $update =$this->db->update('solicitud_alertas', $data);
        //echo $sql = $this->db->last_query();die;
        return $update;
    }
    
    
    //Cuando una persona de tipo fraude rechaza la solicitud se cambia el visado a 0
    public function actualizarVisado($id,$data) { 
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id', $id);
        $update =$this->db->update('solicitud_visado', $data);        
        return $update;
    }
    
    //Trae las solitudes aprobadas que no esten en la tabla de visado
    public function get_solicitudes_visado($params = [], $limit = NULL, $offset = NULL){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.*,solicitud_visado.visado, datos_bancarios.respuesta banco_resultado, situacion.nombre_situacion, operadores.nombre_apellido operador_nombre_pila,
        IFNULL(last_track.observaciones, "") last_track');
        $this->db->from('solicitudes.solicitud');
        $this->db->join('solicitudes.solicitud_datos_bancarios datos_bancarios','datos_bancarios.id_solicitud = solicitud.id', 'left');
        
        $this->db->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado', 'left');
        $this->db->join('solicitudes.solicitud_ultima_gestion last_track','last_track.id_solicitud = solicitud.id', 'left');
        $this->db->join('solicitudes.solicitud_visado solicitud_visado','solicitud_visado.id_solicitud = solicitud.id', 'left');
        $this->db->join('parametria.situacion_laboral situacion','situacion.id_situacion= solicitud.id_situacion_laboral', 'left');

        if(isset($params['id'])){ $this->db->where('solicitud.id',$params['id']);}
        if(isset($params['visado'])){ 
            $this->db->where('solicitud_visado.visado',$params['visado']);
        } else{
            $this->db->where('solicitud_visado.visado is null');

        }
        if(isset($params['solicitud.estado'])){ $this->db->where('solicitud.estado',$params['solicitud.estado']);}
        if(isset($params['solicitud.respuesta_analisis'])){ $this->db->where('solicitud.respuesta_analisis',$params['solicitud.respuesta_analisis']);}
        if(isset($params['solicitud.resultado_ultimo_reto'])){ $this->db->where('solicitud.resultado_ultimo_reto',$params['solicitud.resultado_ultimo_reto']);}
        if(isset($params['datos_bancarios.respuesta'])){ $this->db->where('datos_bancarios.respuesta',$params['datos_bancarios.respuesta']);}
        if(isset($params['solicitud.operador_asignado'])){ $this->db->where('solicitud.operador_asignado',$params['solicitud.operador_asignado']);}
        if(isset($params['solicitud.tipo_solicitud'])){ $this->db->where('solicitud.tipo_solicitud',$params['solicitud.tipo_solicitud']);}
        if(isset($params['datos_bancarios.id_banco'])){ $this->db->where('datos_bancarios.id_banco',$params['datos_bancarios.id_banco']);}

        if(isset($params['!='])){ $this->not_equal($params['!=']); }
        if(isset($params['OR'])){ $this->or_where($params['OR']); }
        if(isset($params['OR_LIKE_BOTH'])){ $this->or_like_both($params['OR_LIKE_BOTH']); }
        //if(isset($params['OR_LIKE_NONE'])){ $this->or_like_none($params['OR_LIKE_NONE']); }
        if(isset($params['LIKE_BOTH'])){ $this->like_both($params['LIKE_BOTH']); }
        if(isset($params['LIKE'])){ $this->like($params['LIKE']); }
        if(isset($params['NOT_IN'])){ $this->not_in($params['NOT_IN']); }
        if(isset($params['LITERAL'])){ $this->literal($params['LITERAL']); }
        
        $this->db->group_by("solicitud.id");

        if(isset($params['order'])){ 
            $this->order($params['order']);
        }
       

        if(isset($limit) && isset($offset))
        {
            $this->db->limit($offset, $limit);
        }else if(isset($limit) && !isset($offset))
        {
            $this->db->limit($limit);
        }

        $query = $this->db->get();
        
     //echo $sql = $this->db->last_query();die;
      
        return $query->result_array();
    }
       
    //Trae las solitudes para el operador 3, analizar
    public function get_solicitudes_analisis($params = [], $limit = NULL, $offset = NULL){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.*, solicitud_alertas.descripcion as operador_nombre_pila , situacion.nombre_situacion,
        IFNULL(last_track.observaciones, "") last_track');
        $this->db->from('solicitudes.solicitud');
        $this->db->join('solicitudes.solicitud_alertas','solicitud_alertas.id_solicitud = solicitud.id', 'left');
        $this->db->join('solicitudes.solicitud_ultima_gestion last_track','last_track.id_solicitud = solicitud.id', 'left');
        $this->db->join('parametria.situacion_laboral situacion','situacion.id_situacion= solicitud.id_situacion_laboral', 'left');

        if(isset($params['id'])){ $this->db->where('solicitud.id',$params['id']);}
        if(isset($params['solicitud.respuesta_analisis'])){ $this->db->where('solicitud.respuesta_analisis',$params['solicitud.respuesta_analisis']);}
        if(isset($params['!='])){ $this->not_equal($params['!=']); }
        if(isset($params['OR'])){ $this->or_where($params['OR']); }
        if(isset($params['OR_LIKE_BOTH'])){ $this->or_like_both($params['OR_LIKE_BOTH']); }
        //if(isset($params['OR_LIKE_NONE'])){ $this->or_like_none($params['OR_LIKE_NONE']); }
        if(isset($params['LIKE_BOTH'])){ $this->like_both($params['LIKE_BOTH']); }
        if(isset($params['LIKE'])){ $this->like($params['LIKE']); }
        if(isset($params['NOT_IN'])){ $this->not_in($params['NOT_IN']); }
        if(isset($params['LITERAL'])){ $this->literal($params['LITERAL']); }
        
        $this->db->group_by("solicitud.id");
        //$this->db->order_by("solicitud.id","DESC");
        //$this->db->order_by("solicitud.fecha_ultima_actividad","DESC");
        if(isset($params['order'])){ $this->order($params['order']);}

        if(isset($limit) && isset($offset))
        {
            $this->db->limit($offset, $limit);
        }else if(isset($limit) && !isset($offset))
        {
            $this->db->limit($limit);
        }

        $query = $this->db->get();
        
        //echo $sql = $this->db->last_query();die;
      
        return $query->result_array();
    }    
 
    public function getCantCred($documento)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('COUNT(id)');
        $this->db->from('solicitud');
        $this->db->where('solicitud.estado', 'PAGADO');
        $this->db->where('solicitud.documento = "'. $documento.'"');   
        $query = $this->db->get();
        //echo $sql = $this->db->last_query();die;    
        return $query->result_array();
    }
    
    public function getMailLog($mail){       
        $this->load->database('usuarios_solventa',TRUE);
        $this->db->select('*');
        $this->db->from('usuarios_solventa.login_attempts');
        $this->db->where('login', $mail);        
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }
    
     public function getParentesco(){       
        $this->load->database('parametria',TRUE);
        $this->db->select('*');
        $this->db->from('parametria.parentesco');
        $this->db->where('id_estado_Parentesco', 1);        
        $query = $this->db->get();
        $res = $query->result_array();
        return $res;
    }
    
    public function getDiasAtraso($id_cliente)
    {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('dias_atraso');
        $this->db->from('credito_detalle');
        $this->db->where('id_credito IN (SELECT id FROM creditos WHERE ID_CLIENTE ="'.$id_cliente.'")');  
        $this->db->order_by('id_credito', 'ASC');
        $query = $this->db->get();
        //echo $sql = $this->db->last_query();die;    
        return $query->result_array();
    }
    
    public function borrar_login($email)
    {
        $this->db = $this->load->database('usuarios_solventa', TRUE);
        $this->db->delete('login_attempts', array('login' => $email));
        $delete = $this->db->affected_rows();
        return $delete;
        //echo $sql = $this->db->last_query();die; 
    }
    
    public function listado_formato($id_operador,$periodo = ''){
        if($periodo == 'hoy'){
            $fechas = "= CURRENT_DATE()";
        } else if ($periodo == 'ayer'){
            $fechas = "= DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)";
        } else {
            $fechas = ">= DATE_SUB(CURRENT_DATE(), INTERVAL 3 DAY)";
        }
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.*, datos_bancarios.respuesta banco_resultado,operadores.nombre_apellido operador_nombre_pila, situacion.nombre_situacion,
        IFNULL(last_track.observaciones, "") last_track');
        $this->db->from('solicitud');
        $this->db->join('solicitudes.solicitud_datos_bancarios datos_bancarios','datos_bancarios.id_solicitud = solicitud.id', 'left');
        $this->db->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado', 'left');
        $this->db->join('solicitudes.solicitud_ultima_gestion last_track','last_track.id_solicitud = solicitud.id', 'left');
        $this->db->join('parametria.situacion_laboral situacion','situacion.id_situacion= solicitud.id_situacion_laboral', 'left');
        $this->db->where('solicitud.id IN (SELECT id_solicitud FROM gestion.relacion_operador_solicitud WHERE id_operador = '.$id_operador.' AND DATE_FORMAT(fecha_registro,"%Y-%m-%d")'.$fechas.')');  
        $this->db->group_by('solicitud.id');
        $this->db->order_by('solicitud.id', 'DESC');
        $this->db->order_by('solicitud.fecha_ultima_actividad', 'DESC');
        $query = $this->db->get();
        //echo $sql = $this->db->last_query();die;    
        return $query->result_array(); 
    }

    public function listado_por_revisar_desembolso($id_operador,$periodo = ''){
        
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.*, datos_bancarios.respuesta banco_resultado,operadores.nombre_apellido operador_nombre_pila, situacion.nombre_situacion,
        IFNULL(last_track.observaciones, "") last_track');
        $this->db->from('solicitud');
        $this->db->join('solicitudes.solicitud_datos_bancarios datos_bancarios','datos_bancarios.id_solicitud = solicitud.id', 'left');
        $this->db->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado', 'left');
        $this->db->join('solicitudes.solicitud_ultima_gestion last_track','last_track.id_solicitud = solicitud.id', 'left');
        $this->db->join('parametria.situacion_laboral situacion','situacion.id_situacion= solicitud.id_situacion_laboral', 'left');
        $this->db->join('solicitudes.validar_desembolso val','val.id_solicitud = solicitud.id');
        ///$this->db->where('solicitud.id IN (SELECT id_solicitud FROM gestion.relacion_operador_solicitud WHERE id_operador = '.$id_operador.')');  
        $this->db->where('val.respuesta IS NOT NULL  and val.revisada = 0 and val.id_operador = '.$id_operador);  
        $this->db->group_by('solicitud.id, datos_bancarios.respuesta');
        $this->db->order_by('solicitud.id', 'DESC');
        $this->db->order_by('solicitud.fecha_ultima_actividad', 'DESC');
        $query = $this->db->get();
        //echo $sql = $this->db->last_query();die;    
        return $query->result_array(); 
    }

    public function obtenerDocumento($id_solicitud){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('solicitud.documento');
        $this->db->from('solicitud');
        $this->db->where('solicitud.id',$id_solicitud);
        $query = $this->db->get();
        //var_dump($query->result());die;
        return $query->result();
    }

    public function getValidarMail($mail_a_modificar,$documentoBuscar){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('*');
        $this->db->from('solicitud');
        $this->db->where('solicitud.documento != "' .$documentoBuscar.'"');
        $this->db->LIKE('solicitud.email', $mail_a_modificar);   
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function updateMailUser($id_usuario,$data){
        $this->db = $this->load->database('usuarios_solventa', TRUE);
        $this->db->where('id', $id_usuario);
        $update =$this->db->update('users', $data);
        //echo ($this->db->last_query());die;
        return $update;
 
    }

    public function update_val_telefono($id_solicitud,$data){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id', $id_solicitud);
        $update =$this->db->update('solicitud', $data);
        return $update;
 
    }

    public function getSolicitudesPendientes_m($id_operador){
        $this->db = $this->load->database('solicitudes', TRUE);
        $query = $this->db->query("(SELECT id_solicitud FROM `eid_scans` WHERE `status` LIKE 'completed' AND id_solicitud in ( SELECT id FROM solicitud WHERE operador_Asignado = ".$id_operador." AND pagare_enviado = 0 AND fecha_alta >= DATE_SUB(CURRENT_DATE(), INTERVAL 20 DAY) AND estado NOT IN ('APROBADO','RECHAZADO','PAGADO','TRANSFIRIENDO') ) ) UNION ( SELECT id_solicitud FROM `jumio_scans` WHERE `respuesta_identificacion` LIKE 'APPROVED_VERIFIED' AND `respuesta_supervivencia` LIKE 'true' AND `respuesta_match` LIKE 'MATCH' AND id_solicitud IN ( SELECT id FROM solicitud WHERE operador_Asignado = ".$id_operador." AND pagare_enviado = 0 AND fecha_alta >= DATE_SUB(CURRENT_DATE(), INTERVAL 20 DAY) AND estado NOT IN ('APROBADO','RECHAZADO','PAGADO','TRANSFIRIENDO')))");
        return $query->result_array();
    }

    public function vencimiento_botones_front($fecha){
        $this->db_parametria->select('*');
        $this->db_parametria->from('vencimiento_botones_front');
        $this->db_parametria->where('"'.$fecha.'" between fecha_inicio and fecha_hasta');
        $query = $this->db_parametria->get();
        return $query->result_array();
    }

    public function get_vencimientos_flujo_mora(){
        $this->db_parametria->select('segundo_vencimiento');
        $this->db_parametria->from('vencimiento_botones_front');
        $this->db_parametria->where(' segundo_vencimiento <= "'.date('Y-m-d', strtotime('+4 day')).'"');
        $query = $this->db_parametria->get();
        return $query->result_array();
    }

    public function get_situacion_laboral($param){
        $this->db_parametria->select('*');
        $this->db_parametria->from('situacion_laboral');
        if(isset($param['estado'])) {$this->db_parametria->where('id_estado_situacion', $param['estado']);}
        if(isset($param['id_notIn'])) {$this->db_parametria->where('id_situacion not in ('.$param['id_notIn'].')');}
        $query = $this->db_parametria->get();
        return $query->result();
    }

    public function verificacion_telefono($telefono, $documento){
        $this->db_solicitudes->select('*');
        $this->db_solicitudes->from('solicitud');
        $this->db_solicitudes->where("telefono = $telefono AND documento != '$documento'" );
        $query = $this->db_solicitudes->get();
        
        if($this->db_solicitudes->affected_rows() == 0){
            return TRUE;
        }else{
            return FALSE;
        }
       
    }

    public function solicitud_beneficio_nc($id_cliente){
        $this->db_solicitudes->select('id, beneficio_monto_fijo, monto_disponible,  beneficio_plazo');
        $this->db_solicitudes->from('maestro.niveles_clientes');
        $this->db_solicitudes->where("id_cliente",  $id_cliente);
        $query = $this->db_solicitudes->get();
        return $query->result();
       
    }

    public function solicitud_beneficio($id_solicitud){
        $this->db_solicitudes->select('id, monto_maximo');
        $this->db_solicitudes->from('solicitud_beneficios');
        $this->db_solicitudes->where("id_solicitud",  $id_solicitud);
        $query = $this->db_solicitudes->get();
        return $query->result();

    }

    public function update_niveles_clientes($cliente,$data){
        $this->db_maestro->where('id_cliente', $cliente);
        $update =$this->db_maestro->update('niveles_clientes', $data);
        //var_dump($this->db_solicitudes->last_query());die;
        if($this->db_maestro->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public function update_solicitud_beneficio($id_solicitud,$data){
        $this->db_solicitudes->where('id_solicitud', $id_solicitud);
        $update =$this->db_solicitudes->update('solicitud_beneficios', $data);
        //var_dump($this->db_solicitudes->last_query());die;
        if($this->db_solicitudes->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }
    
    public function insert_track_gestion_apertura($data){
        $this->db_gestion->insert('track_apertura_casos', $data);
       
        if($this->db->affected_rows() != 1){
            return -1;
        } else{
            return $this->db->insert_id();
        }
    }

    public function insertar_validar_desembolso($data) {       
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->insert('validar_desembolso', $data);
       
        if($this->db->affected_rows() != 1){
            return -1;
        } else{
            return $this->db->insert_id();
        }
    }

    public function get_verificacion_desembolso($param){
        $this->db_solicitudes->select('*');
        $this->db_solicitudes->from('solicitudes.validar_desembolso');
        if(isset($param['id_solicitud']))   {   $this->db_solicitudes->where('id_solicitud',$param['id_solicitud']);    }
        if(isset($param['revisada']))   {   $this->db_solicitudes->where('revisada',$param['revisada']);    }
        if(isset($param['respuesta']))   {   $this->db_solicitudes->where('respuesta is not null');    }
        if(isset($params['limite']))     {   $this->db_solicitudes->limit(1);}
        $this->db_solicitudes->order_by('id', 'DESC');

        $query = $this->db_solicitudes->get();
        
        
        return $query->result();
       
    }
 
    public function update_validar_desembolso($id,$data){
        $this->db_solicitudes->where('id', $id);
        $update =$this->db_solicitudes->update('validar_desembolso', $data);

        if($this->db_solicitudes->affected_rows() < 1){
            return -1;
        } else{
            return $this->db_solicitudes->affected_rows();
        }

    }

    public function get_pagare_revolvente($param) {
        $this->db_solicitudes->select('*');
        $this->db_solicitudes->from('solicitudes.pagare_revolvente');
        if(isset($param['documento']))   {   $this->db_solicitudes->where('documento = "'.$param['documento'].'"');    }
        $query = $this->db_solicitudes->get();
        return $query->result();
    }

//Sabrina Basteiro FIN

    public function visado_automatico($id_solicitud){
        $sql = " SELECT solicitud.id AS solicitud_id FROM solicitud, solicitud_analisis ";
        $sql .= "WHERE solicitud.id = $id_solicitud AND solicitud.operador_asignado != 108 AND solicitud.estado = 'APROBADO' ";
        $sql .= "AND solicitud.id_situacion_laboral IN (1) AND solicitud_analisis.id_solicitud = solicitud.id AND solicitud_analisis.antiguedad_laboral > 4 AND solicitud.id ";
        $sql .= "NOT IN ( SELECT sub_vis.id_solicitud FROM solicitud_visado sub_vis WHERE sub_vis.visado = 1 )";
        $query = $this->db_solicitudes->query($sql);
        return $query->result();
    }


    /*****************************************************/
    /*** Obtener las solicitudes Desembolsos a Validar ***/
    /*****************************************************/
    public function getDesembolsos() {
        $sql = "select 
            vd.id, 
            vd.id_solicitud, 
            DATE_FORMAT(vd.fecha_hora_solicitud, '%d/%m/%Y %H:%i') AS fecha_hora_solicitud,
            vd.respuesta,
            vd.comprobante,
            DATE_FORMAT(s.fecha_alta, '%d/%m/%Y') AS fecha_alta,
            s.documento,
            s.estado,
            s.tipo_solicitud,
            CONCAT(s.nombres, ' ', s.apellidos) AS nombre_apellido,
            stxt.origen_pago,
            DATE_FORMAT(stxt.fecha_procesado, '%d/%m/%Y %H:%i') AS fecha_procesado,
            stxt.pagado,
            stxt.ruta_txt,
            op.nombre_apellido AS nombre_apellido_operador
        FROM solicitudes.validar_desembolso vd
            INNER JOIN solicitudes.solicitud s ON s.id = vd.id_solicitud
            INNER JOIN solicitudes.solicitud_txt stxt ON vd.id_solicitud = stxt.id_solicitud
            INNER JOIN gestion.operadores op ON vd.id_operador = op.idoperador
        WHERE vd.respuesta IS NULL
        ORDER BY vd.fecha_hora_solicitud;";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }

    /***************************************************************/
    /*** Obtener la cantidad de solicitudes pendientes a Validar ***/
    /***************************************************************/
    public function getCantDesembolsos() {
        $sql = "select COUNT(*) AS cantidad
        FROM solicitudes.validar_desembolso vd
        WHERE vd.respuesta IS NULL;";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }

    /***********************************************************************/
    /*** Se obtienen la Cantidad de Solicitud de Imputaciones pendientes ***/
    /***********************************************************************/
    public function getCantImputacionesPendientes()
    {
        $sql = "select count(1) AS cantidad
            FROM maestro.solicitud_imputacion si
            WHERE si.por_procesar = 0
                AND si.resultado IS NULL";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }

    public function get_agenda_mail($param)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('*');
        $this->db->from('solicitante_agenda_mail sagm');
        if(isset($param['documento'])) { $this->db->where('sagm.documento = "'.$param['documento'].'"');}
        if(isset($param['personal'])) { $this->db->where('sagm.documento = "'.$param['documento'].'" AND sagm.fuente = "PERSONAL"');}
        $query = $this->db->get();
        return $query->result_array();
    }

    public function rs_result($sql){

        $query2 = $this->db_maestro->query($sql);
        $arreglo=[];
        foreach ($query2->list_fields() as $field)
        {
            $arreglo['campos'][]=$field;
        }
        
        $data  = $query2->result_array();
        $arreglo['result']=$data;

        return $arreglo;
        
            
    }

    public function get_template_mail($param=''){
        $this->db = $this->load->database('campanias', TRUE);
        $this->db->select('cmt.*,crt.id_logica, cl.query_contenido,cl.nombre_logica');
        $this->db->from('campanias_mail_templates cmt');
        $this->db->join('campanias.campanias_relacion_templates crt','crt.id_template = cmt.id', 'left');
        $this->db->join('campanias.agenda_mail_logica cl','cl.id_logica = crt.id_logica', 'left');
        if(isset($param['id'])) { $this->db->where('cmt.id',$param['id']);}
        if(isset($param['canal'])) {$this->db->like('canal', $param["canal"]); }
        $this->db->where('cmt.estado',1);
        $this->db->where('crt.estado',1);
        $this->db->where('crt.flag_unico',1);
        $query = $this->db->get();
        // echo $sql = $this->db->last_query();die;
        return $query->result_array();
    }

    public function get_agenda_personal_solicitud($param)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('*');
        $this->db->from('solicitante_agenda_telefonica sag');
        if(isset($param['documento'])) { $this->db->where('sag.documento = "'.$param['documento'].'"');}
        if(isset($param['id'])) { $this->db->where('sag.id',$param['id']);}
        if(isset($param['estado'])) { $this->db->where('sag.estado',$param['estado']);}
        if(isset($param['numero'])) { $this->db->where('sag.numero',$param['numero']);}
        if(isset($param['order'])) {$this->db->group_by('sag.numero');}
        if(isset($param['fuentes'])) {$this->db->where('sag.fuente in ('.$param['fuentes'].')');}
        if(isset($param['order_camp'])) {$this->db->order_by('sag.'.$param['order_camp'].' DESC');}
        if(isset($param['limit'])) {$this->db->limit($param['limit']);}
        
        $query = $this->db->get();
        //echo $sql = $this->db->last_query();die;
        return $query->result_array();
    }

    public function get_numeros_chat($documento){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('chat.*, sag.id id_agenda, sag.fuente, sag.contacto, parentesco.Nombre_Parentesco');
        $this->db->from('chat.new_chats chat, solicitudes.solicitante_agenda_telefonica sag');
        $this->db->join('parametria.parentesco parentesco', 'parentesco.id_parentesco = sag.id_parentesco', 'left');
        $this->db->where('sag.documento',(string)$documento);
        $this->db->where('sag.numero = chat.from');
        $this->db->group_by("chat.id");

        //$this->db->where('chat.to',$canal);
        
        $query = $this->db->get();
        // echo $sql = $this->db->last_query();die;
        return $query->result_array();
    }

    public function get_agenda_whatsapp($param) {
        $this->db = $this->load->database('chat', TRUE);
        $this->db->select('*');
        $this->db->from('new_chats w');
        if(isset($param['documento'])) { $this->db->where('w.documento = "'.$param['documento'].'"');}
        if(isset($param['from'])) { $this->db->where('w.from',(string)$param['from']);}
        if(isset($param['status_chat'])) { $this->db->where('w.status_chat',$param['status_chat']);}
        if(isset($param['orden'])){ $this->db->order_by($param['orden']);}
        if(isset($param['limit'])){ $this->db->limit($param['limit']);}
        $query = $this->db->get();
        return $query->result_array();
    }

    public function update_telefono_solicitante($id,$data)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id', $id);
        $update =$this->db->update('solicitante_agenda_telefonica', $data);

        return $update;
    }
 
    public function agregar_telefono_solicitante($data)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->insert('solicitante_agenda_telefonica' ,$data);
        $this->db->insert_id();
        $query = $this->db->affected_rows(); 
        // echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function validar_agregar_agenda_telefono($condicional){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->from('solicitante_agenda_telefonica');
        $this->db->select('*');  
        $this->db->where($condicional);
        $query = $this->db->get();
        // echo $sql = $this->db->last_query();die;
        return $query->num_rows();

    }

    public function update_mail_solicitante($id,$data)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id', $id);
        $update =$this->db->update('solicitante_agenda_mail', $data);
        // echo $sql = $this->db->last_query();die;
        return $update;
    }

    public function agregar_mail_solicitante($data)
    {
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->insert('solicitante_agenda_mail' ,$data);
        $this->db->insert_id();
        $query = $this->db->affected_rows(); 
        // echo $sql = $this->db->last_query();die;
        return $query;
    }

    public function validar_agregar_agenda_mail($data){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->from('solicitante_agenda_mail');
        if($data["fuente"] == 'PERSONAL'){
            $this->db-> where('fuente',$data["fuente"]);
            $this->db-> where('cuenta',$data["cuenta"]);
        }elseif($data["fuente"] == 'REFEERENCIA'){
            $this->db-> where('documento = "'.$data["documento"].'"');
            $this->db-> where('fuente','PERSONAL');
            $this->db-> where('cuenta',$data["cuenta"]);        
        }else{
            $this->db-> where('documento = "'.$data["documento"].'"');
            $this->db-> where('cuenta',$data["cuenta"]);
        }
        $query = $this->db->get();
        // echo $sql = $this->db->last_query();die;
        return $query->num_rows();

    }

    public function update_primer_reporte_agenda_tlf(){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->select('documento');
        $this->db->from('solicitante_agenda_telefonica');
        $this->db->where('primer_reporte', NULL);
        $this->db->where('fuente', 'PERSONAL DECLARADO');

        $query = $this->db->get();
        // echo ($this->db->last_query());die;
        $documents = $query->result_array();
        // var_dump($documents);die;
        for ($i=0;$i < count($documents) ;$i++){
            $query2 = $this->db->query("UPDATE solicitante_agenda_telefonica SET primer_reporte = (SELECT primer_reporte FROM solicitante_agenda_telefonica WHERE fuente in ('BURO_CELULAR_T') AND documento = '".$documents[$i]['documento']."') WHERE fuente in ('PERSONAL DECLARADO')AND numero = (SELECT numero FROM solicitante_agenda_telefonica WHERE fuente in ('BURO_CELULAR_T')AND documento = '".$documents[$i]['documento']."')AND documento = (SELECT documento FROM solicitante_agenda_telefonica WHERE fuente in ('BURO_CELULAR_T')AND documento = '".$documents[$i]['documento']."')");
        }
        
        // echo ($this->db->last_query());die;
        return $query2->num_rows();
    }

    public function creditosByPeriodo($vencimiento_desde, $vencimiento_hasta, $tipo, $fecha_ciclo = null)
    {
        $sql = "SELECT  COUNT(credito_detalle.id) total FROM maestro.credito_detalle, solicitudes.solicitud
                WHERE `fecha_vencimiento` BETWEEN '$vencimiento_desde' AND '$vencimiento_hasta'
                    AND solicitud.id_credito = credito_detalle.id_credito";
        if ($tipo != "TODOS") {
            $sql .= " AND solicitud.tipo_solicitud = '$tipo'";
        }
        if(!is_null($fecha_ciclo)){
            $sql .= " AND credito_detalle.estado = 'pagado' 
                      AND credito_detalle.fecha_cobro <= '$fecha_ciclo'";
        }

        $resultado = $this->db_solicitudes->query($sql);
        //var_dump($this->db_solicitudes->last_query());
        return $resultado->result_array();
    }

/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Busqueda de Botones de gestiones operadores Ing.Esthiven Garcia 17/03/2021
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/

    public function BuscarBotonesOperador(){
        $this->db_gestion->select('*');
        $this->db_gestion->from('botones_operador');
        $this->db_gestion->order_by("id", "asc");
        //echo $this->db->last_query();
        
        $query = $this->db_gestion->get();
        return $query->result();
    }
	
	/**
	 * Obtiene un boton de operador por Id
	 * 
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getBotonOperadorById($id)
	{
		$this->db_gestion->select('*');
		$this->db_gestion->from('botones_operador');
		$this->db_gestion->where("id", $id);
		//echo $this->db->last_query();
		
		$query = $this->db_gestion->get();
		return $query->result();
	}

    public function getTipo_Ajuste(){
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->select('*');  
        $this->db->from('tipo_ajuste');
        $this->db->order_by("id", "asc");
        $query = $this->db->get();
        return $query->result();
    }
    
    public function getClase_Ajuste($params){
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->select('*');  
        $this->db->from('clase_ajuste');
        
        if(isset($params['id'])){ $this->db->where('clase_ajuste.id',$params['id']);}
        if(isset($params['id_tipo_ajuste'])){ $this->db->where('clase_ajuste.id_tipo_ajuste',$params['id_tipo_ajuste']);}
        
        if(isset($params['order'])){ $this->db->order($params['order']);}
        $query = $this->db->get();
        return $query->result();
    }

    public function getrequisitos_Clase($id_clase_ajuste){
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->select('*');  
        $this->db->from('requisitos_clase_ajuste');
        $this->db->where('id_clase_ajuste',$id_clase_ajuste);
        $query = $this->db->get();
        return $query->result();
    }    

    public function saveSolicitudAjustes($data){
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->insert('gestion.ajustes', $data);
        return $this->db->insert_id();
    }

    public function updateSolicitudAjustes($id, $data) {
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->where('id', $id);
        $update = $this->db->update('gestion.ajustes', $data);
        return $update;
    }
    
    public function getSolicitudAjustes($documento){
        
        $this->db = $this->load->database('gestion', TRUE);
        $sql = "SELECT 
            ajustes.id, 
            ajustes.fecha_solicitud AS fecha_solicitud_ajuste,
            ajustes.id_solicitud AS id_solicitud_cliente,
            ajustes.id_operador idops,
            (SELECT nombre_apellido FROM operadores WHERE idOperador = idops) AS operador_solicitante,
            tipo_ajuste.descripcion as tipo,
            clase_ajuste.descripcion as clase,
            ajustes.descripcion as comentario,
            ajustes.estado as estado,
            ajustes.fecha_proceso as fecha_procesado,
            ajustes.id_operador_procesa idopp,
            (SELECT nombre_apellido FROM operadores WHERE idOperador = idopp) AS procesado_por_operador,
            ajustes.observaciones,
            ajustes.resultado
        FROM 
            `ajustes`, tipo_ajuste,clase_ajuste
        WHERE 
            `documento` LIKE '$documento'
            AND tipo_ajuste.id = ajustes.id_tipo_ajuste
            AND clase_ajuste.id = ajustes.id_clase_ajuste";

        $resultado = $this->db->query($sql);
        return $resultado->result_array();
    }
    
    public function getSolicitudAjustesBy($params){
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->select('aj.*');
        $this->db->select('ta.descripcion AS descrip_tipo');
        $this->db->select('ca.descripcion AS descrip_clase');
        $this->db->from('ajustes as aj');
        $this->db->join('tipo_ajuste AS ta', 'aj.id_tipo_ajuste = ta.id');
        $this->db->join('clase_ajuste AS ca', 'aj.id_clase_ajuste = ca.id ');
        if(isset($params['id'])){ $this->db->where('id',$params['id']);}
        if(isset($params['id_operador'])){ $this->db->where('id_operador',$params['id_operador']);}
        if(isset($params['estado'])){ $this->db->where('estado',$params['estado']);}
        if(isset($params['estado_in'])){ $this->db->where_in('estado',$params['estado_in']);}
        if(isset($params['recibido'])){ $this->db->where('recibido',$params['recibido']);}
        if(isset($params['id_solicitud'])){ $this->db->where('id_solicitud',$params['id_solicitud']);}
        if(isset($params['order'])){ $this->db->order($params['order']);}
        $query = $this->db->get();
        // echo $this->db->last_query();die();
        return $query->result();
    }
    
    public function getSolicitudAjustesByOperador($id_operador){
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->select('aj.*');
        $this->db->select('ta.descripcion AS descrip_tipo');
        $this->db->select('ca.descripcion AS descrip_clase');
        $this->db->from('ajustes as aj');
        $this->db->join('tipo_ajuste AS ta', 'aj.id_tipo_ajuste = ta.id');
        $this->db->join('clase_ajuste AS ca', 'aj.id_clase_ajuste = ca.id ');
        $this->db->where('id_operador',$id_operador);
        $this->db->where('recibido', 0);
        $this->db->where_in('estado', [1, 3]);
        $query = $this->db->get();
        // echo $this->db->last_query();die();
        return $query->result();
    }
    
    public function getSolicitudAjustesAll(){
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->select('aj.*');
        $this->db->select('ta.descripcion AS descrip_tipo');
        $this->db->select('ca.descripcion AS descrip_clase');
        $this->db->from('ajustes as aj');
        $this->db->join('tipo_ajuste AS ta', 'aj.id_tipo_ajuste = ta.id');
        $this->db->join('clase_ajuste AS ca', 'aj.id_clase_ajuste = ca.id ');
        $this->db->where('estado', 0);
        $query = $this->db->get();
        // echo $this->db->last_query();die();
        return $query->result();
    }
	
	public function getListasRestrictivas($solicitudId)
	{
		$query = $this->db->select('*')
			->from('api_buros.infolaft_content')
			->where('id_solicitud', $solicitudId);
		
		$result = $this->db->get()->result_array();
		
		return $result;
    }
	
	public function getReferenciasCruzadas($documento_solicitud)
	{
		$subquery = $this->db_solicitudes->select('numero')
			->from('solicitante_agenda_telefonica')
			->where('documento like', (string)$documento_solicitud)
			->where('fuente in (1, 3, 8, 9, 10, 11)')
			->get_compiled_select();
		
		$this->db_solicitudes->select('*')
			->from('solicitante_agenda_telefonica')
			->where("numero in ($subquery)", null)
			->where('documento <>', (string)$documento_solicitud);
		
		$result1 = $this->db_solicitudes->get()->result_array();
		
		$rtrn = $result1;
		
		if (count($result1) > 0) {
			foreach ($result1 as $k => $item) {
				$numeroCruzado = $item['numero'];
				
				$querySolicitud = $this->db_solicitudes->select('*')
					->from('solicitante_agenda_telefonica')
					->where('numero',$numeroCruzado)
					->where('documento = "'. $documento_solicitud.'"')
					->where('fuente IN (1, 3, 8, 9, 10, 11)');
				$resultSolicitud = $this->db_solicitudes->get()->result_array();
				
				if (isset($resultSolicitud[0])) {
					$rtrn[$k]['fuente_cruzada'] = $resultSolicitud[0]['fuente'];
					$rtrn[$k]['primer_reporte_cruzado'] = $resultSolicitud[0]['primer_reporte'];
				} else {
					$rtrn[$k]['fuente_cruzada'] = "";
					$rtrn[$k]['primer_reporte_cruzado'] = null;
				}
				
				// ==========================================================
				
				$documentoCruzado = $item['documento'];
				
				$subQuery2 = $this->db_solicitudes->select('count(id)')
					->from('solicitud ')
					->where("documento like '$documentoCruzado'")
					->where("id_credito > 0")
					->get_compiled_select();
				
				$this->db_solicitudes->select("count(id) AS nro_solicitudes, nombres, apellidos, max(id_cliente) AS es_cliente, ($subQuery2) AS nro_creditos")
					->from('solicitud')
					->where("documento like '$documentoCruzado'");
				$result3 = $this->db_solicitudes->get()->result_array();

				if(isset($result3[0])) {
					$rtrn[$k]['es_cliente'] = $result3[0]['es_cliente'];
					$rtrn[$k]['nombres'] = $result3[0]['nombres'];
					$rtrn[$k]['apellidos'] = $result3[0]['apellidos'];
					$rtrn[$k]['nro_solicitudes'] = $result3[0]['nro_solicitudes'];
					$rtrn[$k]['nro_creditos'] = $result3[0]['nro_creditos'];
					
				} else {
					$rtrn[$k]['es_cliente'] = null;
					$rtrn[$k]['nombres'] = "";
					$rtrn[$k]['apellidos'] = "";
					$rtrn[$k]['nro_solicitudes'] = "";
					$rtrn[$k]['nro_creditos'] = "";
				}
				
				// ==========================================================
				
				$rtrn[$k]['dias_atraso'] = "";
				$rtrn[$k]['estado'] = "";
				$rtrn[$k]['monto_cobrar'] = "";
				
				if (!is_null($rtrn[$k]['es_cliente'])) {
					$subquery3 = $this->db_solicitudes->select('id')
						->from('maestro.creditos')
						->where('id_cliente', $rtrn[$k]['es_cliente'])
						->get_compiled_select();

					$this->db_solicitudes->select('dias_atraso ')
						->from('maestro.credito_detalle')
						->where("id_credito in ($subquery3)");
					$result4 = $this->db_solicitudes->get()->result_array();
					
					$query4Value = "";
					foreach ($result4 as $item4) {
						$query4Value .= $item4['dias_atraso'] . "-";
					}
					if (strlen($query4Value) > 0) {
						$query4Value = substr_replace($query4Value ,"",-1);
					}
					
					$rtrn[$k]['dias_atraso'] = $query4Value;
					// ==========================================================
					
					$this->db_solicitudes->select('estado,monto_cobrar')
						->from('maestro.credito_detalle')
						->where("id_credito in ($subquery3)")
						->order_by('id', 'DESC')
						->limit(1);
					
					$result5 = $this->db_solicitudes->get()->result_array();
					
					if(isset($result5[0])) {
						$rtrn[$k]['estado'] = $result5[0]['estado'];
						$rtrn[$k]['monto_cobrar'] = $result5[0]['monto_cobrar'];
					} 
				}
			}
			
			return $rtrn;
		}
    }
	
	public function getReferenciasCruzadasEmail($documento_solicitud)
	{
		$subquery = $this->db_solicitudes->select('cuenta')
			->from('solicitante_agenda_mail')
			->where('documento like', (string)$documento_solicitud)
			->where('fuente in (1, 3, 8, 9, 10, 11)')
			->get_compiled_select();
		
		$this->db_solicitudes->select('*')
			->from('solicitante_agenda_mail')
			->where("cuenta in ($subquery)", null)
			->where('documento <>', (string)$documento_solicitud);
		
		$result1 = $this->db_solicitudes->get()->result_array();
		
		$rtrn = $result1;
		
		if (count($result1) > 0) {
			foreach ($result1 as $k => $item) {
				$emailCruzado = $item['cuenta'];
				
				$querySolicitud = $this->db_solicitudes->select('*')
					->from('solicitante_agenda_mail')
					->where('cuenta',$emailCruzado)
					->where('documento  = "'. $documento_solicitud.'"')
					->where('fuente IN (1, 3, 8, 9, 10, 11)');
				$resultSolicitud = $this->db_solicitudes->get()->result_array();
				
				if (isset($resultSolicitud[0])) {
					$rtrn[$k]['fuente_cruzada'] = $resultSolicitud[0]['fuente'];
					$rtrn[$k]['primer_reporte_cruzado'] = $resultSolicitud[0]['primer_reporte'];
				} else {
					$rtrn[$k]['fuente_cruzada'] = "";
					$rtrn[$k]['primer_reporte_cruzado'] = null;
				}
				
				// ==========================================================
				
				$documentoCruzado = $item['documento'];
				
				$subQuery2 = $this->db_solicitudes->select('count(id)')
					->from('solicitud ')
					->where("documento like '$documentoCruzado'")
					->where("id_credito > 0")
					->get_compiled_select();
				
				$this->db_solicitudes->select("count(id) AS nro_solicitudes, nombres, apellidos, max(id_cliente) AS es_cliente, ($subQuery2) AS nro_creditos")
					->from('solicitud')
					->where("documento like '$documentoCruzado'");
				$result3 = $this->db_solicitudes->get()->result_array();
				
				if(isset($result3[0])) {
					$rtrn[$k]['es_cliente'] = $result3[0]['es_cliente'];
					$rtrn[$k]['nombres'] = $result3[0]['nombres'];
					$rtrn[$k]['apellidos'] = $result3[0]['apellidos'];
					$rtrn[$k]['nro_solicitudes'] = $result3[0]['nro_solicitudes'];
					$rtrn[$k]['nro_creditos'] = $result3[0]['nro_creditos'];
					
				} else {
					$rtrn[$k]['es_cliente'] = null;
					$rtrn[$k]['nombres'] = "";
					$rtrn[$k]['apellidos'] = "";
					$rtrn[$k]['nro_solicitudes'] = "";
					$rtrn[$k]['nro_creditos'] = "";
				}
				
				// ==========================================================
				
				$rtrn[$k]['dias_atraso'] = "";
				$rtrn[$k]['estado'] = "";
				$rtrn[$k]['monto_cobrar'] = "";
				
				if (!is_null($rtrn[$k]['es_cliente'])) {
					$subquery3 = $this->db_solicitudes->select('id')
						->from('maestro.creditos')
						->where('id_cliente', $rtrn[$k]['es_cliente'])
						->get_compiled_select();
					
					$this->db_solicitudes->select('dias_atraso ')
						->from('maestro.credito_detalle')
						->where("id_credito in ($subquery3)");
					$result4 = $this->db_solicitudes->get()->result_array();
					
					$query4Value = "";
					foreach ($result4 as $item4) {
						$query4Value .= $item4['dias_atraso'] . "-";
					}
					if (strlen($query4Value) > 0) {
						$query4Value = substr_replace($query4Value ,"",-1);
					}
					
					$rtrn[$k]['dias_atraso'] = $query4Value;
					// ==========================================================
					
					$this->db_solicitudes->select('estado,monto_cobrar')
						->from('maestro.credito_detalle')
						->where("id_credito in ($subquery3)")
						->order_by('id', 'DESC')
						->limit(1);
					
					$result5 = $this->db_solicitudes->get()->result_array();
					
					if(isset($result5[0])) {
						$rtrn[$k]['estado'] = $result5[0]['estado'];
						$rtrn[$k]['monto_cobrar'] = $result5[0]['monto_cobrar'];
					}
				}
			}
			
			return $rtrn;
		}
    }
    
	public function getSectorFinancieroalDia($documento)
	{
		//sub queries
		$subqueryConsulta = $this->db->select('id')
			->from('api_buros.dataconsulta')
			->where('NumeroIdentificacion = "'. $documento.'"')
			->get_compiled_select();
		
		
		$query = $this->db->select('*')
			->from('api_buros.pecoriginacion_sectorfinancieroaldia')
			->where("IdConsulta IN ($subqueryConsulta)", null)
			->where("str_to_date(FechaCorte, '%d/%m/%Y') >= DATE_SUB( CURRENT_DATE(), INTERVAL 180 DAY)");
		
		$result = $this->db->get()->result_array();
		
		return $result;
	}
	
	public function getSectorFinancieroEnMora($documento)
	{
		//sub queries
		$subqueryConsulta = $this->db->select('id')
			->from('api_buros.dataconsulta')
			->where('NumeroIdentificacion = "'. $documento.'"')
			->get_compiled_select();
		
		
		$query = $this->db->select('*')
			->from('api_buros.pecoriginacion_sectorfinancieroenmora')
			->where("IdConsulta IN ($subqueryConsulta)", null)
			->where("str_to_date(FechaCorte, '%d/%m/%Y') >= DATE_SUB( CURRENT_DATE(), INTERVAL 180 DAY)");
		
		$result = $this->db->get()->result_array();
		
		return $result;
	}
	
	public function getSectorRealAlDia($documento)
	{
		//sub queries
		$subqueryConsulta = $this->db->select('id')
			->from('api_buros.dataconsulta')
			->where('NumeroIdentificacion = "'. $documento.'"')
			->get_compiled_select();
		
		
		$query = $this->db->select('*')
			->from('api_buros.pecoriginacion_sectorrealaldia')
			->where("IdConsulta IN ($subqueryConsulta)", null)
			->where("str_to_date(FechaCorte, '%d/%m/%Y') >= DATE_SUB( CURRENT_DATE(), INTERVAL 180 DAY)");
		
		$result = $this->db->get()->result_array();
		
		return $result;
	}
	
	public function getSectorRealEnMora($documento)
	{
		//sub queries
		$subqueryConsulta = $this->db->select('id')
			->from('api_buros.dataconsulta')
			->where('NumeroIdentificacion = "'. $documento.'"')
			->get_compiled_select();
		
		
		$query = $this->db->select('*')
			->from('api_buros.pecoriginacion_sectorrealenmora')
			->where("IdConsulta IN ($subqueryConsulta)", null)
			->where("str_to_date(FechaCorte, '%d/%m/%Y') >= DATE_SUB( CURRENT_DATE(), INTERVAL 180 DAY)");
		
		$result = $this->db->get()->result_array();
		
		return $result;
	}
	
	public function getSectorFinancieroExtinguido($documento)
	{
		//sub queries
		$subqueryConsulta = $this->db->select('id')
			->from('api_buros.dataconsulta')
			->where('NumeroIdentificacion = "'. $documento.'"')
			->get_compiled_select();
		
		
		$query = $this->db->select('*')
			->from('api_buros.pecoriginacion_sectorfinancieroextinguidas')
			->where("IdConsulta IN ($subqueryConsulta)", null);
		
		$result = $this->db->get()->result_array();
		
		return $result;
	}
	
	public function getSectorRealExtinguido($documento)
	{
		//sub queries
		$subqueryConsulta = $this->db->select('id')
			->from('api_buros.dataconsulta')
			->where('NumeroIdentificacion = "'. $documento.'"')
			->get_compiled_select();
		
		
		$query = $this->db->select('*')
			->from('api_buros.pecoriginacion_sectorrealextinguidas')
			->where("IdConsulta IN ($subqueryConsulta)", null);
		
		$result = $this->db->get()->result_array();
		
		return $result;
	}

    public function updateverificacion_galery($params){
        $this->db = $this->load->database('solicitudes', TRUE);
        $this->db->where('id', $params['id']);
        $update = $this->db->update('veriff_scan', $params);
        return $update;

    }

    public function getValidacionObservacion($id_solicitud){
        
        $sql = "SELECT * FROM `solicitud` WHERE id = $id_solicitud 
            AND `estado` NOT IN ('APROBADO','TRANSFIRIENDO','RECHAZADO','PAGADO','ANULADO')
            AND id IN     (SELECT id_solicitud FROM `solicitud_imagenes` WHERE `id_solicitud` = $id_solicitud AND `id_imagen_requerida` = 18 
                AND id_solicitud IN (SELECT id_solicitud FROM `veriff_scan` WHERE `id_solicitud` = $id_solicitud AND respuesta_match = 'approved')) 
            AND id NOT IN (SELECT id_solicitud FROM `veriff_scan` WHERE `id_solicitud` = $id_solicitud AND respuesta_match = 'approved' AND respuesta_identificacion IS NOT NULL)";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }
	
	public function saveTelefonoReferenciaFamiliar($documento, $telefono, $tipo, $contacto, $estado, $idParentezco, $llamada, $sms)
	{
		$this->saveTelefonoReferencia($documento, $telefono, $tipo, "REFERENCIA", $contacto, $estado, $idParentezco, $llamada, $sms);
    }
	
	public function saveTelefonoReferenciaLaboral($documento, $telefono, $tipo, $contacto, $estado, $idParentezco, $llamada, $sms)
	{
		$this->saveTelefonoReferencia($documento, $telefono, $tipo, "LABORAL", $contacto, $estado, $idParentezco, $llamada, $sms);
	}
    
    private function saveTelefonoReferencia($documento, $telefono, $tipo, $fuente, $contacto, $estado, $idParentezco, $llamada, $sms) {
    	$data = [
    		'documento' => $documento,
    		'numero' => $telefono,
    		'tipo' => $tipo,
    		'fuente' => $fuente,
    		'contacto' => $contacto,
    		'estado' => $estado,
    		'id_parentesco' => $idParentezco,
    		'llamada' => $llamada,
    		'sms' => $sms,
		];
    	$this->db_solicitudes->insert('solicitante_agenda_telefonica', $data);
	}
	
	public function updateTelefonoRefernciaLaboral($documento, $antiguoNumero, $nuevoNumero, $contacto)
	{
		$this->db_solicitudes->set('numero', $nuevoNumero);
		$this->db_solicitudes->set('contacto', $contacto);
		$this->db_solicitudes->where('numero', $antiguoNumero);
		$this->db_solicitudes->where('fuente', 'LABORAL');
		$this->db_solicitudes->where('documento', $documento);
		$this->db_solicitudes->update('solicitante_agenda_telefonica');
	}
	
	public function updateTelefonoReferenciaFamiliar($documento, $antiguoNumero, $nuevoNumero, $contacto, $idParentezco)
	{
		$this->db_solicitudes->set('numero', $nuevoNumero);
		$this->db_solicitudes->set('contacto', $contacto);
		$this->db_solicitudes->set('id_parentesco', $idParentezco);
		$this->db_solicitudes->where('numero', $antiguoNumero);
		$this->db_solicitudes->where('fuente', 'REFERENCIA');
		$this->db_solicitudes->where('documento', $documento);
		$this->db_solicitudes->update('solicitante_agenda_telefonica');
	}

    public function get_payment_link($id_cliente){
        $this->db_maestro->select('*');
        $this->db_maestro->from('payment_link');
        $this->db_maestro->where('id_cliente',$id_cliente);
        $query = $this->db_maestro->get();
        return $query->result();
    }

    public function insert_payment_link($array_insert) {
        return $this->db_maestro->insert_batch('payment_link',$array_insert);
    }

    public function get_por_visar($fecha_buscar){
        $sql = "(
            SELECT
                'AUT' as columna,
                COUNT(id) as sumatoria
            FROM
                `solicitud`
            WHERE
                `estado` LIKE 'APROBADO'
                AND operador_asignado = 108
                AND id NOT IN (
                    SELECT
                        id_solicitud
                    FROM
                        solicitud_visado
                )
                AND id IN (
                    SELECT
                        id_solicitud
                    FROM
                        gestion.track_gestion
                    WHERE
                        id_tipo_gestion = 130
                        AND fecha = '". $fecha_buscar ."'
                )
                AND id NOT IN (
                    SELECT
                        id_solicitud
                    FROM
                        gestion.track_gestion
                    WHERE
                        id_tipo_gestion = 130
                        AND fecha > '". $fecha_buscar ."'
                )
        )
        UNION
            (
                SELECT
                    'DEP' as columna,
                    COUNT(id) as sumatoria
                FROM
                    `solicitud`
                WHERE
                    `estado` LIKE 'APROBADO'
                    AND operador_asignado != 108
                    AND id_situacion_laboral != 3
                    AND id NOT IN (
                        SELECT
                            id_solicitud
                        FROM
                            solicitud_visado
                    )
                    AND id IN (
                        SELECT
                            id_solicitud
                        FROM
                            gestion.track_gestion
                        WHERE
                            id_tipo_gestion = 130
                            AND fecha = '". $fecha_buscar ."'
                    )
                    AND id NOT IN (
                        SELECT
                            id_solicitud
                        FROM
                            gestion.track_gestion
                        WHERE
                            id_tipo_gestion = 130
                            AND fecha > '". $fecha_buscar ."'
                    )
            )
        UNION
            (
                SELECT
                    'IND' as columna,
                    COUNT(id) as sumatoria
                FROM
                    `solicitud`
                WHERE
                    `estado` LIKE 'APROBADO'
                    AND operador_asignado != 108
                    AND id_situacion_laboral = 3
                    AND id NOT IN (
                        SELECT
                            id_solicitud
                        FROM
                            solicitud_visado
                    )
                    AND id IN (
                        SELECT
                            id_solicitud
                        FROM
                            gestion.track_gestion
                        WHERE
                            id_tipo_gestion = 130
                            AND fecha = '". $fecha_buscar ."'
                    )
                    AND id NOT IN (
                        SELECT
                            id_solicitud
                        FROM
                            gestion.track_gestion
                        WHERE
                            id_tipo_gestion = 130
                            AND fecha > '". $fecha_buscar ."'
                    )
            )";

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }
    
    public function get_referencia_solicitud($params){
        $this->db_solicitudes->select('sol_ref.id, sol_ref.id_mensaje_chat, sol_ref.respuesta_chat_validacion, chat_sm.sms_status, chat_sm.body');
        $this->db_solicitudes->from('solicitud_referencias as sol_ref');
        $this->db_solicitudes->join($this->db_chat->database.'.sent_messages as chat_sm', 'id_mensaje_chat = chat_sm.id', 'left');
        $this->db_solicitudes->where('sol_ref.id_mensaje_chat <> ""');
        $this->db_solicitudes->where_in('sol_ref.id_parentesco', [1,2,3,4]);
        $this->db_solicitudes->where('sol_ref.id_solicitud', $params['id_solicitud']);
        $this->db_solicitudes->order_by('sol_ref.id', 'desc');
        $this->db_solicitudes->limit(1);
        $query = $this->db_solicitudes->get();
        return $query->result();
    }

    public function get_permisos_servicios($params) {
        $this->db_gestion->select('*');
        $this->db_gestion->from('users_servicios');
        $this->db_gestion->where('idoperador', $params['idoperador']);
        $this->db_gestion->where($params['action'], 1);
        $query = $this->db_gestion->get();
        return $query->row();
    }

    public function obtenerDataVisado($id_solicitud)
    {
        $this->db_solicitudes->select("ROUND((sc.total_devolver/sc.plazo),0) AS total_devolver, sc.fecha_pago_inicial AS fecha_primer_pago");
        $this->db_solicitudes->from("solicitud_condicion_desembolso AS sc");
        $this->db_solicitudes->where("sc.id_solicitud =".$id_solicitud);
        $this->db_solicitudes->where('sc.fecha_pago_inicial >= DATE_FORMAT(NOW(), "%Y-%m-%d")');

        $data = $this->db_solicitudes->get();
        // var_dump($this->db_solicitudes->last_query());die;
        return $data->result_array();
    }

    public function obtenerChat($numero)
    {
        $this->db_chat->select('id as id_chat, status_chat');
        $this->db_chat->from('new_chats');   
        $this->db_chat->where("from",$numero);   
        $this->db_chat->where('to ='.TWILIO_PROD_GESTION);
        $this->db_chat->order_by("id","DESC");
        $this->db_chat->limit(1);
        
        $query = $this->db_chat->get()->result_array();
        // var_dump($this->db_chat->last_query());die;
        return $query;
    }

    public function get_data_enviar($documento)
    {
        $this->db_maestro->select('cl.id, cd.monto_cobrar, cd.id AS credito_detalle_id');
        $this->db_maestro->from("clientes AS cl");
        $this->db_maestro->join("creditos AS c", "c.id_cliente = cl.id");
        $this->db_maestro->join("credito_detalle AS cd", "cd.id_credito = c.id");
        $this->db_maestro->where("c.estado IN ('mora', 'vigente')");
        $this->db_maestro->where("cl.documento ='$documento'");
        $this->db_maestro->group_by("c.id_cliente");

        $data = $this->db_maestro->get();
        // var_dump($this->db_maestro->last_query());die;
        return $data->result_array();
    }

    public function get_biometria_items()
    {
        $this->db_chatbot->select('*');
        $this->db_chatbot->from('biometria_items');
        $this->db_chatbot->where('estado', 1);
        $this->db_chatbot->order_by('id', 'asc');
        $query = $this->db_chatbot->get();
        return $query->result_array();
    }
    
    public function search_acuerdo($id_cliente, $medio_pago)
    {
        $this->db->select('id, monto AS monto_acuerdo');
        $this->db->from('acuerdos_pago');
        $this->db->where('id_cliente', $id_cliente);
        $this->db->where('estado', 'pendiente');
        $this->db->where('medio', $medio_pago);
        $data = $this->db->get();
        // var_dump($this->db->last_query());die;
        return $data->result_array();
    }

    public function transferenciaRechazada($idOperador, $tipoOperador)
    {
        $sql = 'SELECT 
            `solicitud`.*,
            DATE_FORMAT(solicitud.fecha_alta, "%d-%m-%Y") AS date_ultima_actividad, DATE_FORMAT(solicitud.fecha_alta, "%H:%I:%S") AS hours_ultima_actividad,
            `datos_bancarios`.`respuesta` AS `banco_resultado`, 
            `situacion`.`nombre_situacion`, 
            `gestion`.`operadores`.`nombre_apellido` AS `operador_nombre_pila`, 
            IFNULL(last_track.observaciones, "") AS last_track 
        FROM 
            `solicitudes`.`solicitud_txt` 
            INNER JOIN `solicitudes`.`solicitud` as `solicitud` ON  `solicitud`.`id` = `solicitud_txt`.`id_solicitud`
            LEFT JOIN `solicitudes`.`solicitud_datos_bancarios` as `datos_bancarios` ON `datos_bancarios`.`id_solicitud` = `solicitud`.`id` 
            LEFT JOIN `gestion`.`operadores` as `operadores` ON `operadores`.`idoperador` = `solicitud`.`operador_asignado` 
            LEFT JOIN `solicitudes`.`solicitud_ultima_gestion` `last_track` ON `last_track`.`id_solicitud` = `solicitud`.`id` 
            LEFT JOIN `parametria`.`situacion_laboral` `situacion` ON `situacion`.`id_situacion` = `solicitud`.`id_situacion_laboral` 
        WHERE 
            `solicitud_txt`.`pagado` = 2 
            AND `solicitud`.`estado` = "TRANSFIRIENDO"
            AND `solicitud`.`fecha_ultima_actividad` >= DATE_SUB(CURRENT_DATE(), INTERVAL 15 DAY)';

            if ($tipoOperador == 1 || $tipoOperador == 4) {
                $sql.= 'AND `solicitud`.`operador_asignado` = '.$idOperador;
            }

        $resultado = $this->db_solicitudes->query($sql);
        return $resultado->result_array();
    }

    
    //GESTION TRACK DE DESCANSO OPERADOR INSERT
	public function insert_track_gestion_descanso(array $data) 
	{
		$this->db_gestion->insert('gestion.track_gestion_descanso', $data);
		if ($this->db_gestion->error()['code'] != 0){
			return false;
		}
		
		return true;
		
	}
    //GESTION TRACK DE DESCANSO OPERADOR UPDATE
	public function update_track_gestion_descanso(array $data, array $where) : bool
	{
		return $this->db_gestion->update('gestion.track_gestion_descanso', $data, $where);
		
		if($this->db->affected_rows()!=1){
			return false;
		}
		return true;
	}

    public function get_solicitud_pagare($id_solicitud) {
        $this->db_solicitudes->select('pk');
        $this->db_solicitudes->from('solicitud_pagare');
        $this->db_solicitudes->where('id_solicitud', $id_solicitud);
        $this->db_solicitudes->order_by('pk', 'desc');
        $this->db_solicitudes->limit(1);
        $pk = $this->db_solicitudes->get()->row()->pk;
        $this->db_solicitudes->flush_cache();
        
        $this->db_solicitudes->select('*');
        $this->db_solicitudes->from('solicitud_pagare');
        $this->db_solicitudes->where('pk', $pk);
        $this->db_solicitudes->order_by('id', 'desc');
        $query = $this->db_solicitudes->get();
        return $query->result();
    }

    public function find_lastchat($tlf_cliente,$to,$idoperador)
    {
        $this->db_chat->select('id,status_chat');
        $this->db_chat->from('new_chats');
        $this->db_chat->where('from',$tlf_cliente);
        $this->db_chat->where('id_operador',$idoperador);
        $this->db_chat->where('to',$to);
        $this->db_chat->order_by('id','DESC');
        $query = $this->db_chat->get();
        return $query->row();
    }

}
