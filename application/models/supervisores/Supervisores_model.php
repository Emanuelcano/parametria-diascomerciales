<?php

class Supervisores_model extends CI_Model {
    

    public function __construct() {
        parent::__construct();
		
        // LOAD SCHEMA
        $this->db = $this->load->database('gestion', TRUE);
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->db_telefonia = $this->load->database('telefonia', TRUE);
        $this->db_parametria = $this->load->database('parametria', TRUE);
        $this->db_campania = $this->load->database('campanias', TRUE);
		$this->db_auditoria = $this->load->database('auditoria', TRUE);
		$this->db_chat = $this->load->database('chat', TRUE);
    }

    //TRAE TODOS LOS CRITERIOS DE FORMULACION
    public function get_all_criterios()
    {
        $this->db_telefonia->order_by("id", "ASC");
        return $this->db_telefonia->get("track_criterios")->result();
    }

    public function get_all_logicas()
    {
        $this->db_parametria->order_by("idrango", "ASC");
        return $this->db_parametria->get("cp_tabla_logicas")->result();
    }

    //TRAE TODOS LAS CAMPAÑAS PERTENECIENTES A LA CENTRAL SELECCIONADA
    function get_all_campanias_for_central($valor){
        $this->db_telefonia->where('central', $valor);
        //$this->db_telefonia->order_by("id", "ASC");

        $consulta = $this->db_telefonia->get('track_campanias');
        return $consulta->result();
    }


    //TRAE TODOS LAS CRITERIOS PERTENECIENTES A LA CENTRAL SELECCIONADA
    function get_all_criterios_for_central($valor){
        $this->db_telefonia->where('central', $valor);
        $this->db_telefonia->or_where('central','crm');
        //$this->db_telefonia->order_by("id", "ASC");

        $consulta = $this->db_telefonia->get('track_criterios');
        return $consulta->result();
    }

        //TRAE TODOS OPERADORES ACTIVOS SIN SUSPENCION ACTIVA
    public function get_all_operadores_for_central($valor,$sl_equipos){


        $this->db = $this->load->database('gestion', TRUE);

            $this->db->select('D.idoperador');
            $this->db->from('ausencias_operadores AS D');
            $this->db->where('D.fecha_inicio >=', date("Y-m-d").' 00:00:00');
            $this->db->where('D.fecha_final <=', date("Y-m-d").' 23:59:59');
            $this->db->group_by("idoperador");
            $subQuery = $this->db->get_compiled_select();
            //var_dump($subQuery);die;

        $this->db->select('O.idoperador,O.nombre_apellido,O.tipo_operador,O.estado,A.id_agente');
        $this->db->from('gestion.operadores as O');
        $this->db->join('telefonia.track_operadores AS A', 'O.idoperador = A.id_operador');       
        
        if (!empty($sl_equipos)) {   //ORIGINACION,COBRANZA
            
            $this->db->where_in('O.tipo_operador',$sl_equipos);
            

        }
        
        $this->db->where("O.idoperador NOT IN ($subQuery)", null, false);
        
        $this->db->where('O.estado', 1);
        $this->db->where('A.central', $valor);
        //var_dump($this->db->last_query());die;
        //$this->db->order_by("id", "ASC");
        $query = $this->db->get();        
        return $query->result();
    }
    

    //TRAE TODAS LAS PLANTILLAS PERTENECIENTES A LA CENTRAL SELECCIONADA
    function get_all_plantillas_for_central($valor){
        $this->db_telefonia->where('central', $valor);
        
        //$this->db_telefonia->order_by("id", "ASC");

        $consulta = $this->db_telefonia->get('track_plantillas');
        return $consulta->result();
    }


    public function get_all_plantillas_for_campanias($valor){
        $this->db_maestro->where('id_logica', $valor);
        $consulta = $this->db_maestro->get('campanias_mail_templates');
        return $consulta->result();
    }
    
    //Trae la cantidad de Beneficiarios
    public function get_cantidad_beneficiarios() {
        $this->db_telefonia = $this->load->database('maestro', TRUE);
        $this->db_telefonia->select('be.*');
        $query = $this->db_telefonia->get_where('maestro.beneficiarios be');
        $cantidad = count($query->result());
        return $cantidad;
    }
    
    //Trae cantidad de gastos
    public function get_cantidad_gastos() {
        $this->db = $this->load->database('maestro', TRUE);
        $this->db->select('g.*');
        $query = $this->db->get_where('maestro.gastos g');
        $cantidad = count($query->result());
        return $cantidad;
    }

    function guardarPlantillaCampania($data){
        $this->db_telefonia->insert('track_plantillas',$data);

        if ($this->db_telefonia->affected_rows() > 0) {
            return $id = $this->db_telefonia->insert_id();
        }
        else{
            return false;
        }
    }

    function trackearGenCampania($data){
        $this->db_telefonia->insert('track_generador_campania',$data);

        if ($this->db_telefonia->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }
    }
    
    //Trae los nombres de los modulos por usuario de gastos
    public function get_modulos_usuario($usuario)
    {
        $this->db = $this->load->database('gestion', TRUE);
        $this->db->select('gestion.usuarios_modulos.id_usuario, gestion.modulos.nombre');
        $this->db->from('gestion.usuarios_modulos');
        $this->db->join('gestion.modulos', 'gestion.usuarios_modulos.id_modulo = gestion.modulos.id','left');       
        $this->db->where('gestion.usuarios_modulos.id_usuario', $usuario);
        $query = $this->db->get();        
        return $query->result_array();        
    }
    /***********************************************************/
    /*** Se obtienen las solicitudes de imputación completas ***/
    /***********************************************************/
    public function getSolicitudImputacion() {
        $sql = "select 
            DATE_FORMAT(si.fecha_solicitud, '%d/%m/%Y %H:%i') fecha_solicitud,
            DATE_FORMAT(si.fecha_proceso, '%d/%m/%Y') fecha_proceso,
            si.resultado,
            cl.documento, 
            CONCAT(cl.nombres,' ',cl.apellidos) as nombre,  
            si.por_procesar,
            si.id as id_solicitud_imputacion,
            (SELECT id FROM solicitudes.solicitud sol WHERE sol.id_cliente = si.id_cliente ORDER BY sol.id DESC LIMIT 1) AS id_solicitud,
            si.fecha_pago fecha_pago,
            si.referencia,
            si.monto_pago,
            si.banco_origen,
            si.banco_destino,
            si.comprobante,
            si.comentario,
            si.medio_pago,
            op.nombre_apellido solicitante
        FROM maestro.solicitud_imputacion si 
            INNER JOIN maestro.clientes cl ON si.id_cliente = cl.id 
            LEFT JOIN gestion.operadores op on si.id_operador_solicita = op.idoperador
            where si.por_procesar in (0,1,2) AND si.fecha_solicitud > DATE_SUB(CURRENT_DATE(), INTERVAL 10 DAY)
            order by si.fecha_solicitud DESC";
            $result = $this->db->query($sql);
            return $result->result();
        }    
    public function getPrecargaImputacion() {
            $sql = "select 
                DATE_FORMAT(si.fecha_precarga, '%d/%m/%Y %H:%i') fecha_solicitud,
                DATE_FORMAT(si.fecha_proceso, '%d/%m/%Y') fecha_proceso,
                si.resultado,
                cl.documento, 
                CONCAT(cl.nombres,' ',cl.apellidos) as nombre,  
                si.por_procesar,
                si.id as id_solicitud_imputacion,
                ( SELECT id FROM solicitudes.solicitud sol WHERE sol.id_cliente = si.id_cliente ORDER BY sol.id DESC LIMIT 1 ) AS id_solicitud,
                si.fecha_pago fecha_pago,
                si.referencia,
                si.monto_pago,
                si.banco_origen,
                si.banco_destino,
                si.comprobante,
                si.comentario,
                si.medio_pago,
                si.id_cliente,
                op.nombre_apellido solicitante
            FROM maestro.solicitud_imputacion si 
                INNER JOIN maestro.clientes cl ON si.id_cliente = cl.id 
                LEFT JOIN gestion.operadores op on si.id_operador_solicita = op.idoperador
            where si.por_procesar = 3
        order by si.fecha_solicitud DESC";
        $result = $this->db->query($sql);
        return $result->result();
    }

    public function getSolicitudImputacionByCliente($id_cliente) {
        $sql = "select 
            DATE_FORMAT(si.fecha_solicitud, '%d/%m/%Y %H:%i') fecha_solicitud,
            DATE_FORMAT(si.fecha_proceso, '%d/%m/%Y') fecha_proceso,
            si.resultado,
            cl.documento, 
            CONCAT(cl.nombres,' ',cl.apellidos) as nombre,  
            si.por_procesar,
            si.id as id_solicitud_imputacion,
            (SELECT id FROM solicitudes.solicitud sol WHERE sol.id_cliente = si.id_cliente ORDER BY sol.id DESC LIMIT 1) AS id_solicitud,
            si.fecha_pago fecha_pago,
            si.referencia,
            si.monto_pago,
            si.banco_origen,
            si.banco_destino,
            si.comprobante,
            si.comentario,
            si.medio_pago,
            op.nombre_apellido solicitante
        FROM maestro.solicitud_imputacion si 
            INNER JOIN maestro.clientes cl ON si.id_cliente = cl.id 
            LEFT JOIN gestion.operadores op on si.id_operador_solicita = op.idoperador
            where si.por_procesar in (0,1,2) and cl.id = $id_cliente
            order by si.fecha_solicitud DESC";
            $result = $this->db->query($sql);
            return $result->result();
        }    

  

    public function getpagosimputados($id_cliente) {
        $sql = "SELECT * FROM `pago_credito` 
                WHERE id_detalle_credito IN ( SELECT id FROM `credito_detalle` WHERE id_credito IN ( SELECT id FROM `creditos` WHERE `id_cliente` = $id_cliente ) ) 
                ORDER BY id DESC 
                LIMIT 2";
        $result = $this->db_maestro->query($sql);
        return $result->result();
    }
	
	/**
	 * Obtiene todas las campanias manuales
	 *
	 * @return array
	 */
	public function getCampaniasManuales()
	{
		$result = $this->db_parametria->select('*')
			->from('campanias_manuales')
			->get()->result_array();
		return $result;
	}
	
	/**
	 * Obtiene todos las campanias manuales activas
	 *
	 * @return array
	 */
	public function getCampaniasActivas()
	{
		$result = $this->db_parametria->select('*')
			->from('campanias_manuales')
			->where('estado', 1)
			->get()->result_array();
		return $result;
	}
	
	/** 
	 * ==========================================
	 * FUNCTION get_all_campanias_manuales 
	 * DEPRECADA el 04/11/2021 utilizar 
	 * @see getCampaniasManuales 
	 * en su lugar.
	 * ==========================================
	 **/

    //Lista campañas manuales
    public function listar_campanias_manuales($param){
        $this->db_parametria->select('*');
        $query = $this->db_parametria->from('campanias_manuales');
        if(!empty($param['id'])){
            $query = $this->db_parametria->where('id',$param['id']);
        }
        if(!empty($param['estado'])){
            $query = $this->db_parametria->where('estado',$param['estado']);
        }
        $query = $this->db_parametria->get();        
        return $query->result_array();
    }
    //Genera nueva campaña
    function guardar_campania_crm($data){
        $this->db_parametria->insert('campanias_manuales',$data);
        if ($this->db_parametria->affected_rows() > 0) {
            return $id = $this->db_parametria->insert_id();
        }
        else{
            return false;
        }
    }
	
	
	/**
	 * Actualiza el estado de una campania
	 * 
	 * @param $idCampania
	 * @param $estado
	 *
	 * @return bool
	 */
	public function updateEstadoCampania($idCampania, $estado)
	{
		$this->db_parametria = $this->load->database('parametria', TRUE);
		$this->db_parametria->where('id', $idCampania);
		$data = [
			'estado' => $estado
		];
		$this->db_parametria->update('campanias_manuales', $data);
		return ($this->db_parametria->affected_rows() > 0);
	}
	
	/**
	 * ======================================
	 * FUNCTION Update_estado
	 * DEPRECADA EL 06/11/2021 USAR
	 * @see updateEstadoCampania
	 * en su lugar
	 * ======================================
	 */
    //actualiza campaña
    function Update_estado($id, $params){
        $this->db_parametria = $this->load->database('parametria', TRUE);
        $this->db_parametria->where('id', $id);
        $this->db_parametria->update('campanias_manuales', $params);
        $update = $this->db_parametria->affected_rows();
        return $update;
        if ($this->db_parametria->affected_rows() > 0) {
            return $this->db_parametria->affected_rows();
            }
        else{
            return -1;
        }
     }


    /***********************************************************/
    /*** Se obtienen las campañias creadas completas ***/
    /***********************************************************/

    public function get_all_campanias()
    {
        
        $this->db_campania->select('L.id_logica,P.nombre_proveedor as proveedor,L.type_logic,L.nombre_logica,L.estado');
        $this->db_campania->from('campania AS L');
        $this->db_campania->join('proveedores P', 'L.id_proveedor = P.id_proveedor','left');       
        $this->db_campania->order_by("L.id_logica", "ASC");
        
        $query = $this->db_campania->get();        
        return $query->result_array();


        
    }   

    public function get_all_mensajes($id_campania)
    {
        
        $this->db_campania->select('M.id_mensaje,M.mensaje,M.prederterminado as pre,M.estado');
        $this->db_campania->from('campanias_mensajes AS M');
        $this->db_campania->join('campania C', 'M.id_campania = C.id_logica'); 
        $this->db_campania->where('C.id_logica',$id_campania);
        $this->db_campania->order_by("M.id_mensaje", "ASC");
        $query = $this->db_campania->get();        
        //var_dump($this->db_campania->last_query());die;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else{
            return -1 ; 

        }
        


        
    }

    public function save_campain($post_data)
    {
       $this->db_campania->insert('campania', $post_data);
       
        if ($this->db_campania->affected_rows() > 0) {
            return $this->db_campania->insert_id();
        }
        else{
            return -1;
        }
       
    }
	
	/**
	 * Obtiene los caos sin asignar de la campania manual
	 * 
	 * @param $query
	 * @param $limit
	 * @param $regestionar
	 *
	 * @return array
	 */
	public function getCasosCampaniaSinAsignar($query, $limit, $regestionar)
	{
		//se compila la query para poder realizar las subquerys correspondientes
		$queryOriginal = $query->get_compiled_select();
		
		$subqueryOperadorManual = $this->db_maestro->select('id_credito')
			->from('gestion.relacion_casos_operador_manual')
			->get_compiled_select();
		
		$subqueryCampaniaManual = $this->db_maestro->select('id_credito')
			->from('gestion.relacion_casos_campania_manual')
			->where("fecha_inicio_gestion > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL $regestionar DAY)")
			->get_compiled_select();
		
		//se encapsula la query original para luego filtrarla con las subquerys
		$newQuery = $this->db_maestro->select('original.*')
			->from("($queryOriginal) as original" )
			->where("original.id NOT IN ($subqueryOperadorManual)")
			->where("original.id NOT IN ($subqueryCampaniaManual)" )
			->limit($limit);
		
		$result = $newQuery->get()->result_array();
//		echo '<pre>' . var_export($this->db_maestro->last_query(), true) . '</pre>';
//		die();
		return $result;
	}
	
	/**
	 * Obtiene una campania manual por id
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getCampaniaCrmById($id)
	{
		$query = $this->db_parametria->select('*')
			->from('campanias_manuales')
			->where('id', $id);
		
		return $query->get()->result_array();
	}
	
	/**
	 * Obtiene la query de la campania con los parametros proporcionados
	 *
	 * @param $tipo
	 * @param $grupo
	 * @param int $desde
	 * @param int $hasta
	 * @param int $orden
	 * @param string $exclusiones
	 * @param int $creditosCliente
	 * @param string $accion
	 * @param string $grupoVentas
	 * @param string $grupoVentasValue
	 * @param string $equipoQuery
	 * @param bool $returnQuery
	 * @param int $idCampania
	 *
	 * @return array
	 */
	public function getCasosCampaniaCrm($tipo, $grupo, $desde = 0, $hasta = 0, $orden = 0, $exclusiones = '', $creditosCliente = 0, $accion = '', $grupoVentas = '', $grupoVentasValue = '', $equipoQuery = '', $returnQuery = false, $idCampania = 0)
	{
		
		// ===================================
		// SUBQUERYS
		// ===================================
		
		if ( $idCampania != 0 ) {
			// Las exclusiones toman como valor de comparación la última gestiona realizada.
			// Si a algunos de los casos se le da como gestiona una de esas exclusiones
			// la query pasara a no contarlo. (antes tenía otro "last track" y ahora uno que lo excluye)
			// Es por eso que agrego esta subquery para incluir todos los casos que el last_track coincide con las
			// exclusiones, pero que originalmente no coincidían.
			$lastTrackInclusion = $this->db_maestro->select('id_credito')
				->from ('gestion.relacion_casos_campania_manual')
				->where('id_campania', $idCampania)
				->get_compiled_select();
		}
		
		$subqueryDistribucionPreventiva = $this->db_campania->select('*')
			->from('distribucion_preventiva')
			->where('equipo', $equipoQuery)
			->get()->result_array();
		
		$subQueryDitribucionEquipos = $this->db_maestro->select('*')
			->from('distribucion_equipos')
			->where('equipo', $equipoQuery)
			->get()->result_array();
		
		$subQueryVentas = $this->db_maestro->select('id_cliente')
			->from('creditos')
			->where_in('creditos.estado', ['vigente', 'mora'])
			->get_compiled_select();
		
		$subQueryBlackList = $this->db_maestro->select('documento')
			->from('solicitudes.riesgo_crediticio')
			->get_compiled_select();
		
		$subQueryBaja = $this->db_maestro->select('documento')
			->from('solicitudes.baja_datos')
			->get_compiled_select();
		
		$subQuerySolicitudes = $this->db_maestro->select('id_cliente')
			->from('solicitudes.solicitud')
			->where("(`estado` IN ('APROBADO','VALIDADO','VERIFICADO','ANALISIS','TRANSFIRIENDO') OR estado IS NULL)")
			->where('id_cliente > 0')
			->get_compiled_select();
		
		$subQueryOtorgamiento = $this->db_maestro->select('id_cliente')
			->from('creditos')
			->where("fecha_otorgamiento > '$grupoVentasValue 00:00:00'")
			->get_compiled_select();
		
		$subsubQueryMayorAtraso = $this->db_maestro->select('id_credito')
			->from('credito_detalle')
			->where("dias_atraso > $grupoVentasValue")
			->get_compiled_select();
		$subqueryMayorAtraso = $this->db_maestro->select("id_cliente")
			->from('creditos')
			->where("id IN ($subsubQueryMayorAtraso)")
			->get_compiled_select();
		
		$subQueryCantidadCreditos = $this->db_maestro->select('id_cliente')
			->from('creditos')
			->group_by('id_cliente')
			->having('COUNT(creditos.id) > ' . $creditosCliente . '')
			->get_compiled_select();
		
		$subMoraQueryEquipoQuery = $this->db_maestro->select('id_credito')
			->from('maestro.distribucion_equipos')
			->where('equipo', $equipoQuery)
			->get_compiled_select();
		
		$subPreventivaQueryEquipoQuery = $this->db_maestro->select('id_credito')
			->from('campanias.distribucion_preventiva')
			->where('equipo', $equipoQuery)
			->get_compiled_select();
		
		$subqueryPorProcesar = $this->db_maestro->select('id_cliente')
			->from('maestro.solicitud_imputacion')
			->where_in('por_procesar', [0,3])
			->get_compiled_select();
		
		$subqueryTelefonosBaja = $this->db_maestro->select('telefono')
			->from('maestro.baja_telefonos')
			->get_compiled_select();
		
		// ===================================
		// QUERY
		// ===================================
		$query = $this->db_maestro->select('solicitud.fecha_ultima_actividad as ultima_actividad, 
				creditos.id, 
				creditos.monto_prestado, 
				credito_detalle.fecha_vencimiento, 
				credito_detalle.monto_cobrar as deuda, 
				credito_detalle.dias_atraso,
				IFNULL(credito_detalle.estado, "vigente") AS estado, 
				clientes.documento, 
				clientes.nombres, 
				clientes.apellidos, 
				IFNULL(last_track.observaciones, "") AS last_track'
		)
			->from('maestro.creditos as creditos')
			->join('solicitudes.solicitud as solicitud', 'solicitud.id_credito = creditos.id ')
			->join('maestro.credito_detalle as credito_detalle', 'credito_detalle.id_credito = creditos.id ')
			->join('solicitudes.solicitud_ultima_gestion last_track', 'last_track.id_solicitud = solicitud.id ', 'left')
			->join('maestro.clientes as clientes', 'clientes.id = creditos.id_cliente')
			->join('agenda_telefonica as agenda', 'agenda.id_cliente = clientes.id ')
			->where("clientes.id NOT IN ($subqueryPorProcesar)", null)
			->where('agenda.fuente', 'PERSONAL')
			->where("agenda.numero NOT IN ($subqueryTelefonosBaja) ")
		;
		
		// ===================================
		// MORA
		// ===================================
		if ($tipo == 'MORA') {
			$query->where('(credito_detalle.estado is null or credito_detalle.estado = "mora")')
				->where("credito_detalle.dias_atraso BETWEEN $desde AND $hasta")
				->group_by('credito_detalle.id');
			
			if ($grupo != 'TODAS') {
				$query->where('solicitud.tipo_solicitud', $grupo);
			}
		}
		
		// ===================================
		// PREVENTIVA
		// ===================================
		if ($tipo == 'PREVENTIVA') {
			$query->where('(credito_detalle.estado is null or credito_detalle.estado = "mora")')
				->where('credito_detalle.fecha_vencimiento BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL 5 DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL 5 DAY)')
				->group_by('credito_detalle.id');
			
			if ($grupo != 'TODAS') {
				$query->where('solicitud.tipo_solicitud', $grupo);
			}
		}
		
		// ===================================
		// VENTAS
		// ===================================
		if ($tipo == 'VENTAS') {
			$this->db_maestro->where("creditos.id_cliente NOT IN ($subQueryVentas)")
				->where('clientes.id = creditos.id_cliente')
				->where("clientes.documento NOT IN ($subQueryBlackList)")
				->where("clientes.documento NOT IN ($subQueryBaja)")
				->where("clientes.id NOT IN ($subQuerySolicitudes)")
				->group_by('clientes.id');
			if ($grupoVentas == 'ALTA_CLIENTE') {
				$query->where("clientes.fecha_alta >= '$grupoVentasValue 00:00:00'");
			}
			
			if ($grupoVentas == 'ULTIMO_OTORGAMIENTO') {
				$query->where("clientes.id IN ($subQueryOtorgamiento)");
			}
			
			if ($grupoVentas == 'CANTIDAD_CREDITOS') {
				$query->having("COUNT(creditos.id) >= $grupoVentasValue");
			}
			
			if ($grupoVentas == 'MAYOR_ATRASO') {
				$query->where("clientes.id NOT IN ($subqueryMayorAtraso)");
				$query->order_by('credito_detalle.dias_atraso', 'desc');
			}
		}
		
		// ===================================
		// ACCION
		// ===================================
		if ($accion != '') {
			if ($accion == 'IN') {
				$query->where("creditos.id_cliente IN ($subQueryCantidadCreditos)");
			}
			
			if ($accion == 'NOT IN') {
				$query->where("creditos.id_cliente NOT IN ($subQueryCantidadCreditos)");
			}
		}
		
		// ===================================
		// EQUIPO QUERY
		// ===================================
		if ($equipoQuery != '') {
			if ($tipo == 'MORA') {
				if ($equipoQuery != 'TODAS') {
					if (count($subQueryDitribucionEquipos) > 0) {
						$query->where("creditos.id IN ($subMoraQueryEquipoQuery)");
					}
					
				}
			} else if ($tipo == 'PREVENTIVA') {
				if ($equipoQuery != 'TODAS') {
					if (count($subqueryDistribucionPreventiva) > 0) {
						$query->where("creditos.id IN ($subPreventivaQueryEquipoQuery)");
					}
				}
			}
			//ESTA PENDIENTE AUN LA QUERY EN VENTAS. 
		}
		
		// ===================================
		// ORDEN
		// ===================================
		if ($orden != 0) {
			if ($tipo == 'MORA') {
				if ($orden == 0) {
					$query->order_by('credito_detalle.dias_atraso', 'DESC');
				} else if ($orden == 1) {
					$query->order_by('credito_detalle.dias_atraso', 'ASC');
				}
			}
			
			if ($tipo == 'PREVENTIVA') {
				if ($orden == 0) {
					$query->order_by('credito_detalle.fecha_vencimiento', 'DESC');
				} else if ($orden == 1) {
					$query->order_by('credito_detalle.fecha_vencimiento', 'ASC');
				}
			}
			
			if ($tipo == 'VENTAS') {
				if ($orden == 0) {
					$query->order_by('clientes.id', 'DESC');
				} else if ($orden == 1) {
					$query->order_by('clientes.id', 'ASC');
				}
			}
		}
		
		// ===================================
		// EXCLUSIONES
		// ===================================
		if ($exclusiones != '') {
			if ( $idCampania != 0 ) {
				//explicación en la subquery al principio de la función
				$query->where("(`last_track`.`id_tipo_gestion` NOT IN(" . $exclusiones . ") or creditos.id in ($lastTrackInclusion))");
			} else {
				$query->where_not_in('last_track.id_tipo_gestion', $exclusiones);	
			}
		}
		
		
		if ($returnQuery) {
			$result = $query;
		} else {
			$result = $query->get()->result_array();
		}
		
//		echo '<pre>' . var_export($this->db_maestro->last_query(), true) . '</pre>';
//		die();
		return $result;
	}
	
	/** 
	 * ===========================
	 * FUNCION busqueda_campania_crm 
	 * DEPRECADA EL 25/10/2021 USAR
	 * @see getCasosCampaniaCrm
	 * en su lugar
	 * ===========================
	 */

    function clientes_cantidad_creditos($param){
        $this->db_maestro->select('id_cliente');
        $this->db_maestro->from('creditos');
        $this->db_maestro->group_by('id_cliente');
        $this->db_maestro->having('COUNT(creditos.id) > '.$param['cantidad'].'');
        $query = $this->db_maestro->get();        
        return $query->result_array();

    }
    //consulta estado operador campaña manual
    function consulta_estado_campania($param){
        $this->db->select('*');
        $this->db->from('relacion_operador_campania_manual');
        if(!empty($param['id_operador'])){$this->db->where('id_operador', $param['id_operador']);}
        if(!empty($param['id_campania'])){$this->db->where('id_campania', $param['id_campania']);}
        if(!empty($param['estado'])){$this->db->where('estado', $param['estado']);}
        $query = $this->db->get();        
        return $query->result_array();
    }
    //activa operador campaña manual
    function insert_relacion_operador_campania_manual($data){
        $this->db->insert('relacion_operador_campania_manual',$data);
        if ($this->db->affected_rows() > 0) {
            return $id = $this->db->insert_id();
        }
        else{
            return false;
        }
    }
    //Track casos asignados
    function track_campanias_manuales($data){
        $this->db->insert('track_campanias_manuales',$data);
        if ($this->db->affected_rows() > 0) {
            return $id = $this->db->insert_id();
        }
        else{
            return false;
        }
    }
    //Track operadores campaña manuales
    function track_operadores_campanias_manuales($data){
        $this->db->insert('track_operadores_campanias_manuales',$data);
        if ($this->db->affected_rows() > 0) {
            return $id = $this->db->insert_id();
        }
        else{
            return false;
        }
    }
    //Track cambios campaña manuales
    function track_actualizacion($date){
        $this->db->insert('track_actualizacion_campanias_manuales',$date);
        if ($this->db->affected_rows() > 0) {
            return $id = $this->db->insert_id();
        }
        else{
            return false;
        } 
    }
	
	/**
	 * ======================================
	 * FUNCTION limiar_relacion_campania
	 * DEPRECADA EL 05/11/2021 USAR
	 * @see desasignarCampaniaAOperador
	 * en su lugar
	 * ======================================
	 **/
	
	
	/**
	 * Desasigna un operador de la campania
	 * 
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function desasignarCampaniaAOperador($idOperador, $idCampania)
	{
		$this->db->where('id_operador', $idOperador);
		$this->db->where('id_campania', $idCampania);
		$this->db->delete('relacion_operador_campania_manual');
		
		return ($this->db->affected_rows() > 0);
	}
	
	/**
	 * Desasigna todos los operadores de la campania
	 *
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function desasignarCampaniaAOperadores($idCampania)
	{
		$this->db->where('id_campania', $idCampania);
		$this->db->delete('relacion_operador_campania_manual');
		
		return ($this->db->affected_rows() > 0);
	}
	
	/**
	 * Elimina uno o todos los casos del operador
	 * 
	 * @param $idOperador
	 * @param int $idCredito
	 *
	 * @return boolean
	 */
	function removeCasosAlOperador($idOperador, int $idCredito = 0)
	{
		$this->db->where('id_operador', $idOperador);
		if($idCredito != 0){
			$this->db->where('id_credito',$idCredito);
		}
		$this->db->delete('relacion_casos_operador_manual');
		
		return ($this->db->affected_rows() > 0);
	}
	
	/** 
	 * ======================================
	 * FUNCTION relacion_casos_operador_manual
	 * DEPRECADA EL 01/11/2021 USAR 
	 * @see removeCasosAlOperador 
	 * en su lugar
	 * ======================================
	 **/
	
	/**
	 * Actualiza el estado de un operador en la campania a descanso
	 * 
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function updateEstadoOperadorEnCampaniaADescanso($idOperador, $idCampania)
	{
		return $this->updateEstadoOperadorEnCampania($idOperador, $idCampania, 'descanso');
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a inactivo
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function updateEstadoOperadorEnCampaniaAInactivo($idOperador, $idCampania)
	{
		return $this->updateEstadoOperadorEnCampania($idOperador, $idCampania, 'inactivo');
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a activo
	 * 
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function updateEstadoOperadorEnCampaniaAActivo($idOperador, $idCampania)
	{
		return $this->updateEstadoOperadorEnCampania($idOperador, $idCampania, 'activo');
	}
	
	/**
	 * Actualiza el estado de un operador en la campania a desactivado
	 *
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function updateEstadoOperadorEnCampaniaADesactivado($idOperador, $idCampania)
	{
		return $this->updateEstadoOperadorEnCampania($idOperador, $idCampania, 'desactivado');
	}
	
	/**
	 * Actualiza el estado de un operador en la campania
	 * 
	 * @param $idOperador
	 * @param $idCampania
	 * @param $estado
	 *
	 * @return bool
	 */
	private function updateEstadoOperadorEnCampania($idOperador, $idCampania, $estado)
	{
		$data = [
			'estado' => $estado
		];
		$this->db->where('id_operador', $idOperador);
		$this->db->where('id_campania', $idCampania);
		$this->db->update('relacion_operador_campania_manual', $data);
		$update = $this->db->affected_rows();
		
		return ($this->db->affected_rows() > 0);
	}
	
	/**
	 * ======================================
	 * FUNCTION update_estado_capania_manual
	 * DEPRECADA EL 05/11/2021 USAR
	 * @see updateEstadoOperadorEnCampaniaADescanso
	 * @see updateEstadoOperadorEnCampaniaAInactivo
	 * @see updateEstadoOperadorEnCampaniaAActivo
	 * en su lugar
	 * ======================================
	 **/
	
	public function getCasosCampania($reGestionar, $order, $limit)
	{
		$subqueryCreditosOperador = $this->db_maestro->select('id_credito')
			->from('gestion.relacion_casos_campania_manual')
			->get_compiled_select();
		
		$subQueryCreditosCampanias = $this->db_maestro->select('id_credito')
			->from('gestion.relacion_casos_campania_manual')
			->where("fecha_hora > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL $reGestionar DAY")
			->get_compiled_select(); 
		
		$query = $this->db_maestro->select('`creditos`.`id`')
			->from('`maestro`.`creditos` AS `creditos`')
			->join('`solicitudes`.`solicitud` AS `solicitud`', '`solicitud`.`id_credito` = `creditos`.`id`')
			->join('`maestro`.`credito_detalle` AS `credito_detalle`', '`credito_detalle`.`id_credito` = `creditos`.`id`')
			->join('`maestro`.`clientes` AS `clientes`', '`clientes`.`id` = `creditos`.`id_cliente`')
			->join('`solicitudes`.`solicitud_ultima_gestion` AS `last_track`', '`last_track`.`id_solicitud` = `solicitud`.`id`')
			->where(' (`credito_detalle`.`estado` IS NULL OR `credito_detalle`.`estado` = "mora") ')
			->where(" creditos.id NOT IN ($subqueryCreditosOperador)")
			->where(" creditos.id NOT IN ($subQueryCreditosCampanias)")
		
			->group_by('`credito_detalle`.`id` ')
			->order_by("ORDER BY `credito_detalle`.`dias_atraso` $order LIMIT $limit")
			->get();
			
		$result = $query->result_array();
			
		$this->db->join('gestion.operadores op', 'rl.id_operador = op.idoperador', 'left');
	}
	
    //activa y asigna casos
    function activar_casos_cobranza($params){
        $sql   = '
        SELECT `creditos`.`id`
        FROM `maestro`.`creditos` AS `creditos`
        JOIN `solicitudes`.`solicitud` AS `solicitud` ON `solicitud`.`id_credito` = `creditos`.`id`
        JOIN `maestro`.`credito_detalle` AS `credito_detalle` ON `credito_detalle`.`id_credito` = `creditos`.`id`
        JOIN `maestro`.`clientes` AS `clientes` ON `clientes`.`id` = `creditos`.`id_cliente`
        JOIN `solicitudes`.`solicitud_ultima_gestion` AS `last_track` ON `last_track`.`id_solicitud` = `solicitud`.`id`
        WHERE  (`credito_detalle`.`estado` IS NULL OR `credito_detalle`.`estado` = "mora")
        '.$params["desde_hasta"].' '.$params["equipoQuery"].'
        AND creditos.id NOT IN (
            SELECT id_credito FROM gestion.relacion_casos_operador_manual
        )
        AND creditos.id NOT IN (
            SELECT id_credito FROM gestion.relacion_casos_campania_manual WHERE fecha_hora > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL '.$params["re_gestionar"].' DAY)
        )
         '.$params["exclusion"].' '.$params["creditos_accion"].'
         GROUP BY `credito_detalle`.`id` 
         ORDER BY `credito_detalle`.`dias_atraso` '.$params["orden"].' LIMIT '.$params["asignar"].'   ';
          $result = $this->db_maestro->query($sql);
          return $result->result_array();
    }
	
	
	/**
	 * Inserta o actualiza el registro de un caso gestionado
	 * 
	 * @param $date 
	 *
	 * @return boolean
	 */
	function registrarCasoComoGestionado($date)
	{
		$this->db->where('id_campania', $date['id_campania']);
		$this->db->where('id_credito', $date['id_credito']);
		$this->db->where('id_operador', $date['id_operador']);
		$q = $this->db->get('relacion_casos_campania_manual');
		
		if ($q->num_rows() > 0) {
			$q2 = $q->result_array();
			$id = $q2[0]['id'];
			
			$this->db->where('id', $id);
			$this->db->update('relacion_casos_campania_manual', $date);
		} else {
			$this->db->insert('relacion_casos_campania_manual', $date);
		}
		
		return ($this->db->affected_rows() > 0);
	}
	
    //inserta relacion casos op manual
    function insert_relacion_casos($data){
        $this->db->insert('relacion_casos_operador_manual',$data);
        if ($this->db->affected_rows() > 0) {
            return $id = $this->db->insert_id();
        }
        else{
            return false;
        }
    }
   
    public function save_message($id_campania,$query_contenido,$arraData){
        
        $this->db_campania->insert('campanias_mensajes', $arraData);
       
        if ($this->db_campania->affected_rows() > 0) {
            $id_mensaje = $this->db_campania->insert_id();

            return TRUE;
            
        }
        else{
            return FALSE;
        }

        
    }

    public function change_steap_campain ($id_campania,$arrayData)
    {
        $this->db_campania->where('id_logica', $id_campania);
        $this->db_campania->update('campania', $arrayData);
        if ($this->db_campania->affected_rows() > 0) {
            return true;
        }
        else{
            return false;
        }
    }
	
	
	/**
	 * Obtiene los casos asignados al operador detallados
	 * 
	 * @param $idOperador
	 *
	 * @return array
	 */
	public function getCasosAsignadosDetallados($idOperador)
	{
		$casosAsignados = $this->getCasosAsignados($idOperador);
		$idCasos = [];
		foreach ($casosAsignados as $casosAsignado) {
			$idCasos[] = $casosAsignado['id_credito'];
		}
		
		$result = $this->db_maestro->select('
			`solicitud`.`fecha_ultima_actividad`      as `ultima_actividad`,
		   `creditos`.`id`,
		   `creditos`.`monto_prestado`,
		   `credito_detalle`.`fecha_vencimiento`,
		   `credito_detalle`.`monto_cobrar`          as `deuda`,
		   `credito_detalle`.`dias_atraso`,
		   IFNULL(credito_detalle.estado, "vigente") AS estado,
		   `clientes`.`documento`,
		   `clientes`.`nombres`,
		   `clientes`.`apellidos`,
		   IFNULL(last_track.observaciones, "")      AS last_track
		')
			->from('gestion.relacion_casos_operador_manual rcom')
			->join('maestro.creditos as creditos', 'creditos.id = rcom.id_credito')
			->join('solicitudes.solicitud as solicitud', 'solicitud.id_credito = rcom.id_credito')
			->join('maestro.credito_detalle as credito_detalle', 'credito_detalle.id_credito = rcom.id_credito')
			->join('solicitudes.solicitud_ultima_gestion as last_track', 'last_track.id_solicitud = solicitud.id', 'left')
			->join('maestro.clientes as clientes', 'clientes.id = creditos.id_cliente')
			->where_in('rcom.id_credito',  $idCasos)
			->get()->result_array();
		
		return $result;
	}
	
    //Actualiza asignacion de casos a operadores 
    function refrescar_campanias_automaticas($param){
        $sql ='SELECT `solicitud`.`fecha_ultima_actividad` as `ultima_actividad`, `creditos`.`id`, `creditos`.`monto_prestado`, `credito_detalle`.`fecha_vencimiento`, 
	    `credito_detalle`.`monto_cobrar` as `deuda`, 
        `credito_detalle`.`dias_atraso`,
	    IFNULL(credito_detalle.estado, "vigente") AS estado, 
	    `clientes`.`documento`, 
	    `clientes`.`nombres`, 
	    `clientes`.`apellidos`, 
	    IFNULL(last_track.observaciones, "") AS last_track 
        FROM `maestro`.`creditos` as `creditos` 
        JOIN `solicitudes`.`solicitud` as `solicitud` ON `solicitud`.`id_credito` =  `creditos`.`id`
        JOIN `maestro`.`credito_detalle` as `credito_detalle` ON `credito_detalle`.`id_credito` =  `creditos`.`id`
        LEFT JOIN `solicitudes`.`solicitud_ultima_gestion` `last_track` ON `last_track`.`id_solicitud` = `solicitud`.`id` 
        JOIN `maestro`.`clientes` as `clientes` ON `clientes`.`id` = `creditos`.`id_cliente` 
        WHERE `credito_detalle`.`id_credito` IN (SELECT id_credito FROM gestion.relacion_casos_operador_manual WHERE id_operador = '.$param['id_operador'].' '.$param['id_creditos'].')
        '.$param['exclusion'].' 
        GROUP BY  `credito_detalle`.`id` 
        ORDER BY `credito_detalle`.`dias_atraso` ASC';
        $result = $this->db_maestro->query($sql);
        return $result->result_array();
    }
	
	
	/**
	 * Comprueba si un credito esta asignado a un operador
	 * 
	 * @param $idCredito
	 * @param $idOperador
	 *
	 * @return boolean
	 */
	public function checkCreditoPerteneceAOperador($idCredito, $idOperador)
	{
		$this->db->select('*');
		$this->db->from('relacion_casos_operador_manual');
		$this->db->where('id_operador', $idOperador);
		$this->db->where('id_credito', $idCredito);
		$result = $this->db->get()->result_array();
		return (count($result) > 0);
	}
	
	/**
	 * Obtiene los casos asignados al operador
	 * 
	 * @param $idOperador
	 *
	 * @return array
	 */
	public function getCasosAsignados($idOperador)
	{
		$this->db->select('*')
			->from('relacion_casos_operador_manual')
			->where('id_operador', $idOperador)
			->order_by('id', 'DESC');
		
		return $this->db->get()->result_array();
	}
	
	/**
	 * ======================================
	 * FUNCTION consulta_casos_asignados
	 * DEPRECADA EL 01/11/2021 USAR 
	 * @see getCasosAsignados  
	 * @see checkCreditoPerteneceAOperador 
	 * en su lugar
	 * ======================================
	 **/

    //Operadores con campaña activa
    function operadores_activos($params){
        $this->db->select('cm.descripcion, rl.*, op.nombre_apellido');
        $query = $this->db->from('relacion_operador_campania_manual rl');
        $this->db->join('gestion.operadores op', 'rl.id_operador = op.idoperador', 'left');
        $this->db->join('parametria.campanias_manuales cm', 'rl.id_campania = cm.id', 'left');
        if(!empty($params['id_campania'])){
            $query = $this->db->where('id_campania',$params['id_campania']);
        }
        $query = $this->db->get();        
        return $query->result_array();
    }
    //casos gestionados
    function casos_gestionados($param){
        $this->db->select('*');
        $this->db->from('relacion_casos_campania_manual');
        if(!empty($param['id_campania'])){$this->db->where('id_campania', $param['id_campania']);}
        if(!empty($param['id_operador'])){$this->db->where('id_operador', $param['id_operador']);}
        if(!empty($param['fecha'])){$this->db->where('fecha_hora '.$param['fecha']);}
        $query = $this->db->get();        
        return $query->result_array();

    }

    //cantidad de veces gestionados por campanaia
    function cantidad_gestionados($param){
        $this->db->select('rc.*, rc.id_operador, op.nombre_apellido, TIMESTAMPDIFF(MINUTE,rc.fecha_asignacion,rc.fecha_hora) as minutos');
        $this->db->from('relacion_casos_campania_manual rc');
        $this->db->join('operadores op', 'op.idoperador = rc.id_operador');
        if(!empty($param['id_campania'])){$this->db->where('id_campania', $param['id_campania']);}
        if(!empty($param['fecha'])){$this->db->where('fecha_hora '.$param['fecha']);}
        if(!empty($param['id_operador'])){$this->db->where('id_operador',$param['id_operador']);}
        $query = $this->db->get();        
        return $query->result_array();
    }

    //Operadores tipo 5,6
    function operadores_inactivos(){
        $this->db->select('idoperador, nombre_apellido');
        $this->db->from('operadores');
        $this->db->where('tipo_operador in(5,6) and estado = 1 and idoperador NOT IN (SELECT id_operador FROM gestion.relacion_casos_operador_manual)');
        $query = $this->db->get();        
        return $query->result_array();

    }
    //Casos gestionados en este momento
    function casos_gestion_momento($param){
        $this->db->select('ro.*');
        $this->db->from('relacion_operador_campania_manual ro');
        $this->db->join('relacion_casos_operador_manual rc', 'rc.id_operador = ro.id_operador', 'left');
        $this->db->where('ro.id_campania',$param['id_campania']);
        $this->db->where('ro.estado = "activo"');
        $query = $this->db->get();
        return $query->result_array();

    }


    function search_campania ($id_campania)
    {

        $this->db_campania->where('id_logica', $id_campania);
   
        $query = $this->db_campania->get('campania');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else{
            return -1 ; 

        }
    }
	
	
	/**
	 * Obtiene los tiempos de gestion del operador
	 *
	 * @param $idCampania
	 * @param null $idOperador
	 * @param null $fecha
	 *
	 * @return array
	 */
	function getTiemposOperador($idCampania, $idOperador = null, $fecha = null)
	{
		$this->db->select('rl.id_operador, rl.id_campania, TIMESTAMPDIFF(SECOND,rl.`fecha_inicio_gestion`,rl.`fecha_fin_gestion`) as segundos, rl.id_credito, sol.documento');
		$this->db->from('relacion_casos_campania_manual rl');
		$this->db->join('solicitudes.solicitud sol', 'sol.id_credito = rl.id_credito');
		if (!is_null($idOperador)) {
			$this->db->where('rl.id_operador', $idOperador);
		}
		if (!is_null($fecha)) {
			$this->db->where("rl.fecha_hora $fecha");
		}
		$this->db->where('rl.id_campania', $idCampania);
		$this->db->where('rl.fecha_fin_gestion is not null');
		$this->db->order_by('segundos ASC');
		
		$query = $this->db->get();
//		echo '<pre>' . var_export($this->db->last_query(), true) . '</pre>';
//		die();
		return $query->result_array();
	}
	
	// ====================================================================
	// Deprecated
	// reason: "reemplazada por la funcion getTiemposOperador()
	// ====================================================================
    function minutos_promedio_op($param){
        $this->db->select('rl.id_operador, rl.id_campania, TIMESTAMPDIFF(MINUTE,rl.`fecha_asignacion`,rl.`fecha_hora`) as minutos, rl.id_credito, sol.documento');
        $this->db->from('relacion_casos_campania_manual rl');
        $this->db->join('solicitudes.solicitud sol','sol.id_credito = rl.id_credito');
        if(!empty($param['id_operador'])){
            $this->db->where('rl.id_operador',$param['id_operador']);
        }
        if(!empty($param['fecha'])){
            $this->db->where('rl.fecha_hora '.$param['fecha'].'');
        }
        $this->db->where('rl.id_campania',$param['id_campania']);
        $this->db->order_by('minutos ASC');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    //Cantidad de gestiones del operador ordenadas
    function operadores_gestion($param){
        $this->db->select('rl.`id_operador`,COUNT(1) AS total,op.nombre_apellido, rl.fecha_hora');
        $this->db->from('relacion_casos_campania_manual AS rl');
        $this->db->join('gestion.operadores op', 'rl.id_operador = op.idoperador', 'left');
        $this->db->where('rl.fecha_hora '.$param['fecha'].'');
        if(!empty($param['id_operador'])){
            $this->db->where('rl.id_operador',$param['id_operador']);
        }else{
            $this->db->where('rl.id_operador = op.idoperador');

        }
        $this->db->where('rl.id_campania',$param['id_campania']);
        $this->db->having('COUNT(1) > 0');
        $this->db->group_by('rl.id_operador');
        $this->db->order_by('total DESC');
        $query = $this->db->get();
        return $query->result_array();       
    }
    //Cantidad de gestiones por hora de operador
    function cantidad_gestiones_hora($param){
        $this->db->select('COUNT(1) AS cantidad');
        $this->db->from('relacion_casos_campania_manual');
        $this->db->where('fecha_hora '.$param['fecha'].'');
        $this->db->where('id_campania',$param['id_campania']);
        if(!empty($param['id_operador'])){
            $this->db->where('id_operador',$param['id_operador']);
        }
        $this->db->having('COUNT(*) > 0');
        $query = $this->db->get();
        return $query->result_array();
    }
	
	/**
	 * Obtiene los casos de la campania en la fecha dada
	 * 
	 * @param $desde
	 * @param $hasta
	 * @param $idCampania
	 *
	 * @return mixed
	 */
	function getCasosEnFecha($desde, $hasta, $idCampania){
		$this->db->select('casos');
		$this->db->from('track_campanias_manuales');
		$this->db->where("fecha_hora BETWEEN '$desde' AND '$hasta'");
		$this->db->where('id_campania',$idCampania);
		$this->db->order_by('casos DESC');
		$query = $this->db->get();
		return $query->result_array();
	}
	
    //Track campaña
    function consulta_casos_fecha($param){
        $this->db->select('casos');
        $this->db->from('track_campanias_manuales');
        $this->db->where('fecha_hora '.$param['fecha'].'');
        $this->db->where('id_campania',$param['id_campania']);
        $this->db->order_by('casos DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
    function tipificacion_operadores($param){
        $this->db->select('rl.*, bt.etiqueta as tipo, COUNT(1) AS total');
        $this->db->from('relacion_casos_campania_manual AS rl');
        $this->db->join('botones_operador AS bt','bt.id = rl.id_gestion');
        $this->db->where('rl.fecha_hora '.$param['fecha'].'');
        $this->db->where('rl.id_campania',$param['id_campania']);
        if(!empty($param['id_gestion'])){
            $this->db->where('rl.id_gestion',$param['id_gestion']);
        }else{
            $this->db->where('rl.id_gestion = rl.id_gestion');
            $this->db->group_by('tipo');
            $this->db->order_by('total DESC');
        }   
        $query = $this->db->get();
        return $query->result_array();
    }    
    function tipificacion_gestionada($param){
        $this->db->select('bt.etiqueta,bt.id');
        $this->db->from('relacion_casos_campania_manual AS rl');
        $this->db->join('botones_operador AS bt','bt.id = rl.id_gestion');
        $this->db->where('rl.fecha_hora '.$param['fecha'].'');
        $this->db->where('rl.id_campania',$param['id_campania']);
        $this->db->where('rl.id_gestion in( select id FROM botones_operador)');
        $query = $this->db->get();
        return $query->result_array();
    }
    function consulta_operadores_campanias($param){
        $this->db->select('TIMEDIFF(hora_fin, hora_ini) AS total, id, id_operador,hora_ini,hora_fin');
        $this->db->from('track_operadores_campanias_manuales');
        $this->db->where('fecha', $param['fecha']);
        if(!empty($param['id_campania'])){$this->db->where('id_campania',$param['id_campania']);}
        if(!empty($param['id_operador'])) {$this->db->where('id_operador',$param['id_operador']);}
        if(!empty($param['estado_campania'])) {$this->db->where('estado',$param['estado_campania']);}
        $this->db->group_by('id, id_operador');
        $this->db->order_by('id DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
    function actualizar_track_op_campania($params,$id){
        $this->db->where('id', $id);
        $this->db->update('track_operadores_campanias_manuales', $params);
        $update = $this->db->affected_rows();
        return $update;
        if ($this->db->affected_rows() > 0) {
            return $this->db->affected_rows();
            }
        else{
            return -1;
        }
    }
    function denominacion_operadores($param){
        $this->db->select('dr.denominacion AS tipo, COUNT(1) AS total');
        $this->db->from('relacion_casos_campania_manual AS rl');
        $this->db->join('detalle_respuestas AS dr','dr.iddetalle_respuesta = rl.id_detalle_respuestas');
        $this->db->where('rl.fecha_hora '.$param['fecha'].'');
        $this->db->where('rl.id_campania',$param['id_campania']);
        $this->db->group_by('tipo');
        $this->db->order_by('total DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
    

    
     //Creditos x equipo
    #function BuscarCreditosxEquipoTurno($param){
    function BuscarCreditosxEquipoTurno($param){


        
if ($param["sl_antiguedad"]==="cd.fecha_vencimiento") {
    if($param["sl_logica"]==="IGUAL_A"){

    $rango_where=$param["sl_antiguedad"] . ' = "' . $param['minvalue'].'"';

    }else if($param["sl_logica"]==="MAYOR_A"){

    $rango_where=$param["sl_antiguedad"] . ' >="' . $param['minvalue'].'"';

    }else if($param["sl_logica"]==="MENOR_A"){

    $rango_where=$param["sl_antiguedad"] . ' < "' . $param['minvalue'].'"';

    }else if($param["sl_logica"]==="DISTINTO_A"){

    $rango_where=$param["sl_antiguedad"] . ' <> "' . $param['minvalue'].'"';

    }else if($param["sl_logica"]==="ENTRE"){
    
    $rango_where=$param["sl_antiguedad"] . ' BETWEEN "'.$param["minvalue"].'" AND "'.$param["maxvalue"].'" ';
    }

}else if ($param["sl_antiguedad"]==="cd.monto_cobrar") {

      if($param["sl_logica"]==="IGUAL_A"){

            $rango_where=$param["sl_antiguedad"] . ' = "' . $param['minvalue'].'"';

          }else if($param["sl_logica"]==="MAYOR_A"){

            $rango_where=$param["sl_antiguedad"] . ' > "' . $param['minvalue'].'"';

          }else if($param["sl_logica"]==="MENOR_A"){

            $rango_where=$param["sl_antiguedad"] . ' < "' . $param['minvalue'].'"';

          }else if($param["sl_logica"]==="DISTINTO_A"){

            $rango_where=$param["sl_antiguedad"] . ' <> "' . $param['minvalue'].'"';

          }else if($param["sl_logica"]==="ENTRE"){

        $rango_where=$param["sl_antiguedad"] . ' BETWEEN "'.$param["minvalue"].'" AND "'.$param["maxvalue"].'" ';
      }




}else if ($param["sl_antiguedad"]==="cd.dias_atraso") {
            

    
     if($param["sl_logica"]==="IGUAL_A"){
            

            $rango_where=$param["sl_antiguedad"] . ' = "' . $param['minvalue'].'"';

            
          }else if($param["sl_logica"]==="MAYOR_A"){

            $rango_where=$param["sl_antiguedad"] . ' > "' . $param['minvalue'].'"';
            

          }else if($param["sl_logica"]==="MENOR_A"){

            $rango_where=$param["sl_antiguedad"] . ' < "' . $param['minvalue'].'"';
            

          }else if($param["sl_logica"]==="DISTINTO_A"){

            $rango_where=$param["sl_antiguedad"] . ' <> "' . $param['minvalue'].'"';
            

          }else if($param["sl_logica"]==="ENTRE"){
            
            $rango_where=$param["sl_antiguedad"] . ' BETWEEN "'.$param["minvalue"].'" AND "'.$param["maxvalue"].'" ';
      }


}

    $this->db_maestro->select('id_credito');
    $this->db_maestro->from('distribucion_equipos');

    $subQuery1= $this->db_maestro->get_compiled_select();


    $this->db_maestro->select('cd.*');
    $this->db_maestro->from('credito_detalle as cd');


    $condicion1 = array_search("mora",$param["sl_condicion"],false);
    $condicion2 = array_search("vigente",$param["sl_condicion"],false);
    $conteo = (count($param["sl_condicion"]));

    
    
    if (isset($param["sl_condicion"]) && isset($condicion1) && $conteo==1) {
      
      
      $this->db_maestro->where_in("cd.estado ",$param["sl_condicion"]);
      
    
    }else if (isset($condicion1) && isset($condicion2)) {
      
      $this->db_maestro->where(" (cd.estado='mora' OR cd.estado is null) ");
      
    }


    
    $this->db_maestro->where($rango_where);
    $this->db_maestro->where("cd.id_credito NOT IN ($subQuery1)", null, false);
    $this->db_maestro->group_by("cd.id_credito");
    $this->db_maestro->order_by("cd.dias_atraso", "DESC");

    // var_dump( $this->db_maestro->get_compiled_select());die;
    $query = $this->db_maestro->get();
    //var_dump($this->db_maestro->last_query());die;
    return $query->result_array();

}

    
    public function insertEquipo_caso($data)
    {
        $this->db_maestro->insert('distribucion_equipos', $data);

        if ($this->db_maestro->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function searchestadoxOperador($idoperador)
    {
        
        $this->db->select('*');
        $this->db->from('operadores');
        $this->db->where('idoperador',$idoperador);
        $query = $this->db->get();
        //var_dump($this->db_maestro->last_query());die;
        return $query->result_array();

    }

    public function buscarOperadorNeotell($idoperador)
    {
        
        $this->db_telefonia->select('*');
        $this->db_telefonia->from('track_operadores');
        $this->db_telefonia->where('id_operador',$idoperador);
        $this->db_telefonia->where('central',"neotell");
        $query = $this->db_telefonia->get();
        //var_dump($this->db_telefonia->last_query());die;
        return $query->result_array();

    }
	
	
	/**
	 * Obtiene los templates de whatsapp, sms e mail de una campania manual
	 * 
	 * @param $campaniaId
	 * @param $telefono
	 *
	 * @return array
	 */
	public function getTemplatesCampanias($campaniaId, $telefono)
	{
		$templates = [
			'whatsapp' => [
				'template' => '',
				'canal' => '',
				'numero' => ''
			],
			'sms' => '',
			'email' => [
				'template' => '',
				'logica' => ''
			]
		];
		
		$query = $this->db_parametria->select('*')
			->from('campanias_manuales')
			->where('id', $campaniaId);
		
		$result = $query->get()->result_array();
		
		if (!empty($result)) {
			$templates = [
				'whatsapp' => [
					'template' => $result[0]['whatsapp'],
					'canal' => $result[0]['canal_whatsapp'],
					'numero' => $telefono
				],
				'sms' => $result[0]['sms'],
				'email' => [
					'template' => $result[0]['mail'],
					'logica' => $this->getTemplateLogicaByTemplateId($result[0]['mail']),
				]
			];
		}
		
		return $templates;
		
	}
	
	
	/**
	 * Obtiene el Id de la logica del template dado
	 * 
	 * @param $idTemplate
	 *
	 * @return int
	 */
	public function getTemplateLogicaByTemplateId($idTemplate)
	{
		$result = $this->db_campania->select('aml.id_logica')
			->from('agenda_mail_logica aml')
			->join('campanias_relacion_templates crt', 'aml.id_logica = crt.id_logica')
			->join('campanias_mail_templates cmt', 'crt.id_template = cmt.id')
			->where('cmt.id', $idTemplate)
			->get()
			->result_array();
		
		if (count($result) > 0) {
			return $result[0]['id_logica'];
		} else {
			return 0;
		}
	}
	
	
	/**
	 * Obtiene la distribucion de equipos del equipo dado en la base maestro
	 * 
	 * @param $equipo
	 *
	 * @return array
	 */
	public function getDistribucionEquipos($equipo)
	{
		$result = $this->db_maestro->select('*')
			->from('distribucion_equipos')
			->where('equipo', $equipo)
			->get()
			->result_array();

		return $result;
	}
	
	/**
	 * Obtiene la distribucion preventiva del equipo dado en la base campanias
	 *
	 * @param $equipo
	 *
	 * @return array
	 */
	public function getDistribucionPreventiva($equipo)
	{
		$result = $this->db_campania->select('*')
			->from('distribucion_preventiva')
			->where('equipo', $equipo)
			->get()
			->result_array();
		
		return $result;
	}

    function ConsultaDistribucion(){
      
      $this->db_maestro->select('*');
      $this->db_maestro->from('distribucion_equipos');
      
    //   var_dump( $this->db_maestro->get_compiled_select());die;
      $query = $this->db_maestro->get();
      //var_dump($this->db_maestro->last_query());die;
      return $query->result_array();

  	}
	
	/**
	 * Obtiene todos los operadores asignados de una campania
	 * 
	 * @param $idCampania
	 *
	 * @return array
	 */
	public function getTodosOperadoresAsignados($idCampania)
	{
		return $this->getOperadoresAsignadosACampaniaByEstado($idCampania);
	}
	
	/**
	 * Obtiene los oepradores asignados activos de una campania
	 * 
	 * @param $idCampania
	 *
	 * @return array
	 */
	public function getOperadoresAsignadosActivos($idCampania)
	{
		return $this->getOperadoresAsignadosACampaniaByEstado($idCampania, 'activo');
	}
	
	/**
	 * Obtiene los operadores asignados inactivoss de una campania
	 * 
	 * @param $idCampania
	 *
	 * @return array
	 */
	public function getOperadoresAsignadosInactivos($idCampania)
	{
		return $this->getOperadoresAsignadosACampaniaByEstado($idCampania, 'inactivo');
	}
	
	/**
	 * Obtiene los operadores asignados en descanso ede una campania
	 * 
	 * @param $idCampania
	 *
	 * @return array
	 */
	public function getOperadoresAsignadosEnDescanso($idCampania)
	{
		return $this->getOperadoresAsignadosACampaniaByEstado($idCampania, 'descanso');
	}
	
	/**
	 * Obtiene los Operadores de una campania por estado
	 *
	 * @param $idCampania
	 * @param $estado
	 *
	 * @return array
	 */
	public function getOperadoresAsignadosACampaniaByEstado($idCampania, $estado = '')
	{
		$query = $this->db->select('*')
			->from('relacion_operador_campania_manual')
			->where('id_campania', $idCampania);
		
		if ($estado != ''){
			$query->where('estado', $estado);
		}
		
		return $query->get()->result_array();
	}
	
	/**
	 * Asigna un operador a una campania
	 *
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function asignarOperadorACampaniaManual($idOperador, $idCampania)
	{
		$data = [
			'id_operador' => $idOperador,
			'id_campania' => $idCampania,
			'estado' => 'activo', //activo
			'fecha_hora' => date('Y-m-d H:i:s')
		];
		$this->db->insert('relacion_operador_campania_manual',$data);
		return ($this->db->affected_rows() > 0);
	}
	
	/**
	 * Trackea el cambio de estado de un operador en una campania a descanso
	 *
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function trackCambioEstadoOperadorCampaniaADescanso($idOperador, $idCampania)
	{
		return $this->trackCambioEstadoOperadorCampania($idOperador, $idCampania, 'descanso');
	}
	
	/**
	 * Trackea el cambio de estado de un operador en una campania a inactivo
	 *
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function trackCambioEstadoOperadorCampaniaAInactivo($idOperador, $idCampania)
	{
		return $this->trackCambioEstadoOperadorCampania($idOperador, $idCampania, 'inactivo');
	}
	
	/**
	 * Trackea el cambio de estado de un operador en una campania a activo
	 * 
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function trackCambioEstadoOperadorCampaniaAActivo($idOperador, $idCampania)
	{
		return $this->trackCambioEstadoOperadorCampania($idOperador, $idCampania, 'activo');
	}
	
	/**
	 * Trackea el cambio de estado de un operador en una campania a desactivado
	 *
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function trackCambioEstadoOperadorCampaniaADesactivado($idOperador, $idCampania)
	{
		return $this->trackCambioEstadoOperadorCampania($idOperador, $idCampania, 'desactivado');
	}
	
	/**
	 * Trackea el cambio de estado de un operador en una campania a activado
	 *
	 * @param $idOperador
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function trackCambioEstadoOperadorCampaniaAActivado($idOperador, $idCampania)
	{
		return $this->trackCambioEstadoOperadorCampania($idOperador, $idCampania, 'activado');
	}
	
	/**
	 * Trackea el cambio de estado de un operador en una campania
	 *
	 * @param $idOperador
	 * @param $idCampania
	 * @param $estado
	 *
	 * @return bool
	 */
	public function trackCambioEstadoOperadorCampania($idOperador, $idCampania, $estado) {
		$diaActual = date('Y-m-d');
		$horaActual = date('H:i:s');
		
		//compruebo si tiene estado previo activo
		$result = $this->db->select('*')
			->from('track_operadores_campanias_manuales')
			->where('fecha', $diaActual)
			->where('id_operador', $idOperador)
			->where('id_campania', $idCampania)
			->order_by('id', 'DESC')
			->get()->result_array();
		
		if (!empty($result)) {
			//cierro el ultimo estado anterior
			$data = [
				'hora_fin'=> $horaActual
			];
			
			$this->db->where('id', $result[0]['id']);
			$this->db->update('track_operadores_campanias_manuales', $data);
		}
		
		$data = [
			'id_operador' => $idOperador,
			'id_campania' => $idCampania,
			'estado' => $estado,
			'id_operador_afecta' => (!empty($this->session->userdata("idoperador")))? $this->session->userdata("idoperador") : 0 ,
			'fecha' => $diaActual,
			'hora_ini' => $horaActual
		];
		
		$this->db->insert('track_operadores_campanias_manuales',$data);
		
		return ($this->db->affected_rows() > 0);
	}
	
	/**
	 * Asigna un caso (credito) a un operador
	 *
	 * @param $idCampania
	 * @param $idOperador
	 * @param $idCaso
	 *
	 * @return bool
	 */
	public function asignarCasoAOperador($idCampania, $idOperador, $idCaso)
	{
		$data = [
			'id_campania' => $idCampania,
			'id_operador' => $idOperador,
			'id_credito' => $idCaso,
			'fecha_hora' => date('Y-m-d H:i:s')
		];
		$this->db->insert('relacion_casos_operador_manual',$data);
		
		return ($this->db->affected_rows() > 0);
	}
	
	
	/**
	 * Obtiene la query de exportacion de los csv de campanias manuales
	 * 
	 * @param $query
	 * @param false $returnQuery
	 *
	 * @return mixed
	 */
	public function queryExportCsvTotalCasos($query, $returnQuery = false)
	{
		//se compila la query para poder realizar las subquerys correspondientes
		$queryOriginal = $query->get_compiled_select();
		
		$newQuery = $this->db_maestro->select("
			original.*, 
			clientes.id id_cliente,
			agenda_telefonica.numero as telefono,
			if (rccm.fecha_hora <> '0000-00-00 00:00:00' AND rccm.fecha_hora > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 1 DAY ), 'GESTIONADO', 'SIN GESTION') estado
		")
			->from("($queryOriginal) as original" )
			->join("maestro.clientes", "clientes.documento = original.documento")
			->join("maestro.agenda_telefonica", "clientes.id = agenda_telefonica.id_cliente AND fuente = 'PERSONAL'", 'left')
			->join("gestion.relacion_casos_campania_manual rccm", 'original.id = rccm.id_credito', 'left');
		
		if ($returnQuery) {
			$result = $newQuery;
		} else {
			$result = $newQuery->get()->result_array();
		}
		
		return $result;
	}
	
	/**
	 * Obtiene el detalle de la tipificacionde los casos de una campania por el tipo de contacto
	 * 
	 * @param $idCampania
	 * @param $idTipoContacto
	 *
	 * @return mixed
	 */
	public function getDetalleTipificacionPorTipo($idCampania, $idTipoContacto)
	{
		return $this->getDetalleTipificacionCampania($idCampania, $idTipoContacto);
	}
	
	/**
	 * Obtiene el detalle de la tipificacionde los casos de una campania por el detalle respuesta
	 * 
	 * @param $idCampania
	 * @param $idDetalle
	 *
	 * @return mixed
	 */
	public function getDetalleTipificacionPorDetalleRespuesta($idCampania, $idDetalle)
	{
		return $this->getDetalleTipificacionCampania($idCampania, 0, $idDetalle);
	}
	
	/**
	 * Obtiene el detalle de la tipificacion de una campania
	 * 
	 * @param $idCampania
	 * @param int $tipo
	 * @param int $detalle
	 *
	 * @return mixed
	 */
	private function getDetalleTipificacionCampania($idCampania, $tipo = 0, $detalle = 0)
	{
		$today = date("Y/m/d") . ' 00:00:00';
		$this->db->select('
			c.id                                                                     as idcredito,
			cli.documento,
			cli.id  																 as id_cliente,
			agenda.numero                                                            as telefono,
			cd.monto_cobrar                                                          as monto,
			cd.dias_atraso,
			op.nombre_apellido                                                       as operador
		')
			->from('relacion_casos_campania_manual rccm')
			->join('maestro.creditos c', 'c.id = rccm.id_credito')
			->join('maestro.credito_detalle cd', 'cd.id_credito = c.id')
			->join('maestro.clientes cli', 'cli.id = c.id_cliente')
			->join('gestion.operadores op', 'op.idoperador = rccm.id_operador')
			->join('maestro.agenda_telefonica agenda', "agenda.id_cliente = c.id_cliente and agenda.fuente = 'PERSONAL'")
			->where('rccm.id_campania', $idCampania)
			->where("rccm.fecha_hora > '$today'");
		
		if ($tipo != 0) {
			$this->db->where('rccm.id_gestion', $tipo );
		} else {
			$this->db->where('rccm.id_detalle_respuestas', $detalle);
		}
		
		$query = $this->db->get();
		return $query->result_array();
	}
	
	/**
	 * Obtiene los casos gestionados de la campania por el operador
	 * 
	 * @param $idCampania
	 * @param $desde
	 * @param $hasta
	 * @param int $idOperador
	 *
	 * @return mixed
	 */
	public function getCasosGestionadosPorOperador($idCampania, $desde, $hasta, $idOperador = 0)
	{
		$this->db->select('
			c.id                                                                     as idcredito,
		   	cli.documento,
		   	cli.id  																 as id_cliente,
		   	agenda.numero                                                            as telefono,
		   	cd.monto_cobrar                                                          as deuda,
		   	cd.dias_atraso,
		   	op.nombre_apellido                                                       as operador,
		   	bo.descripcion as contacto,
		   	dr.denominacion as respuesta,
		   	TIMESTAMPDIFF(SECOND, rccm.fecha_inicio_gestion, rccm.fecha_fin_gestion) as tiempo
		')
			->from('relacion_casos_campania_manual AS rccm')
			->join('gestion.operadores op', 'op.idoperador = rccm.id_operador')
			->join('maestro.creditos c', 'c.id = rccm.id_credito')
			->join('maestro.credito_detalle cd', 'cd.id_credito = c.id')
			->join('maestro.clientes cli', 'cli.id = c.id_cliente')
			->join('gestion.botones_operador bo', 'bo.id = rccm.id_gestion')
			->join('gestion.detalle_respuestas dr', 'dr.iddetalle_respuesta = rccm.id_detalle_respuestas')
			->join('maestro.agenda_telefonica agenda', "agenda.id_cliente = c.id_cliente and agenda.fuente = 'PERSONAL'")
			->where('rccm.id_campania',$idCampania)
			->where("rccm.fecha_hora BETWEEN '$desde' AND '$hasta'");

		if($idOperador != 0){
			$this->db->where('rccm.id_operador',$idOperador);
		}else{
			$this->db->where('rccm.id_operador = op.idoperador');
		}

		$query = $this->db->get();
		return $query->result_array();
	}
	
	/**
	 * Obtiene las cantidades de tipificaciones de una campania
	 * 
	 * @param $idCampania
	 * @param string $desde
	 * @param string $hasta
	 *
	 * @return mixed
	 */
	public function getContadorTipificaciones($idCampania, $desde = '', $hasta = '')
	{
		$this->db->select('bo.id idTipo, bo.etiqueta, dr.iddetalle_respuesta idDetalle, dr.denominacion, count(1) cantidad')
			->from('gestion.relacion_casos_campania_manual rccm')
			->join('gestion.botones_operador bo', 'bo.id = rccm.id_gestion', 'left')
			->join('gestion.detalle_respuestas dr', 'dr.iddetalle_respuesta = rccm.id_detalle_respuestas', 'left')
			->where('id_campania', $idCampania)
			->group_by('bo.etiqueta')
			->group_by('dr.denominacion');
		
		if ($desde != '' and $hasta != '') {
			$this->db->where("rccm.fecha_hora between '$desde' and '$hasta'");
		} else if ($desde != '') {
			$this->db->where("rccm.fecha_hora >= '$desde'");
		} else {
			$this->db->where("rccm.fecha_hora <= '$hasta'");
		}

		$query = $this->db->get();
		return $query->result_array();
	}

	// Guardo en auditoria el audio reportado. 
    function save_audio_reportado($data){
        $this->db_auditoria->insert('audios_auditados',$data);
        
		return $this->db_auditoria->insert_id();
    }

	function get_audio_reportado($id_track){
		$query = $this->db_auditoria->select('*')
			->from('audios_auditados')
			->where('id_audio', $id_track)
			->get();
		return $query->row_array();
	}
	
	//supervisor ventas gestion asignacion automatica
	public function get_reglas_automatico()
	{
		$query = $this->db_parametria->select('*')
			->from('reglas_automatico')
			->get();
		return $query->result_array();
	
	}
	
	public function get_track_reglas_automatico()
	{
		$query = $this->db_parametria->select('*')
			->from('track_reglas_automatico')
			->get();
		return $query->result_array();
	
	}
	
	public function cambio_estado_reglas_automatico($data, $where) : bool
	{
		$this->db_parametria->update('parametria.reglas_automatico', $data, $where);
		if($this->db_parametria->affected_rows() == 1){
			return 1;		
		
		}
		return 0;
	
	}
	
	public function update_reglas_automatico($data, $where) : bool
	{
		$this->db_parametria->update('parametria.reglas_automatico', $data, $where);
		if($this->db_parametria->affected_rows() == 1){
			return 1;		
		
		}
		return 0;
	
	}
	
	public function set_track_reglas_automatico($data) : bool
	{
		$this->db_parametria->insert('parametria.track_reglas_automatico', $data);
		if($this->db_parametria->affected_rows() == 1){
			return 1;		
		
		}
		return 0;
	
	}
	
	public function get_operador($where){
        $this->db->select('idoperador, nombre_apellido');
        $this->db->from('operadores');
        $this->db->where($where);
        $query = $this->db->get();        
        return $query->result_array();

    }
    
	public function get_all_situaciones_laborales(){
        $this->db_parametria->select('id_situacion, nombre_situacion');
        $this->db_parametria->from('situacion_laboral');
        $this->db_parametria->where('id_estado_situacion = 1');
        $query = $this->db_parametria->get();   
        return $query->result_array();

    }

	
	public function get_all_config()
    {
        $this->db_chat->order_by("id", "ASC");
        return $this->db_chat->get("tiempos_gestion")->result();
    }


	public function updateParams($dataArray){
		$data = [
			$dataArray['campo'] =>$dataArray['new_val']
		];
		$this->db_chat->where('id', $dataArray['id']);
			$this->db_chat->update('tiempos_gestion', $data);
			$update = $this->db_chat->affected_rows();
			if ($update > 0) {
				return $update;
				}
			else{
				return 0;
			}
	}

	public function insert_operadores_gestion_chat($data)
	{
		$this->db->insert('operadores_gestion_chat', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
	}
	public function delete_operadores_gestion_chat($id_operador)
	{
		$this->db->where('id_operador', $id_operador);
        $this->db->delete('operadores_gestion_chat');
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
	}

	public function search_operador_campanias_chat($id_operador)
	{
		$this->db->where("id_operador", $id_operador);
		$this->db->where("estado", 1);
		$resultados = $this->db->get('operadores_gestion_chat');
			if ($resultados->num_rows()>0) {
				// return $resultados2->row();
				return TRUE;
			}else{
				return FALSE;

			}
	}
	public function update_gestion_obligatoria($id_operador)
	{
		$data = [
			"gestion_obligatoria" =>0
		];
		$this->db->where('idoperador', $id_operador);
			$this->db->update('operadores', $data);
			$update = $this->db->affected_rows();
			if ($update > 0) {
				return $update;
			}else{
				return 0;
			}
	}
	
	public function obtenerDataCampanias($idCampania, $template = null)
	{
		$this->db_campania->select("cm.id_mensaje, cm.mensaje as msg_string, c.type_logic");
		$this->db_campania->from("campania AS c");
		$this->db_campania->join("campanias_mensajes as cm", "cm.id_campania = c.id_logica", "left");
		$this->db_campania->where("c.id_logica", $idCampania);
		if (!is_null($template)) {
			$this->db_campania->where("cm.id_mensaje", $template);
			$this->db_campania->group_by("cm.id_mensaje");
		}
		$data = $this->db_campania->get();
		return $data->result_array();
	}

	public function obtenerData($idEvents)
	{
		$this->db_maestro->select("params");
		$this->db_maestro->from("events");
		$this->db_maestro->where("id", $idEvents);
		$events = $this->db_maestro->get();
		return $events->result_array();
	}
}
