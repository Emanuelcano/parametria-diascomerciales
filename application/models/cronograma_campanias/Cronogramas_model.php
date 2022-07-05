<?php

class Cronogramas_model extends CI_Model
{
	const CAMPAIGN_RECEIVER_CLIENTES = 'CLIENTES';
	const CAMPAIGN_RECEIVER_SOLICITANTES = 'SOLICITANTES';
	
	const CAMPAIGN_CLIENT_TYPE_ALL = "TODOS";
	const CAMPAIGN_CLIENT_TYPE_PRIMARIA = "PRIMARIA";
	const CAMPAIGN_CLIENT_TYPE_RETANQUEO = "RETANQUEO";
	
	const CAMPAIGN_ACTION_ALL = "TODOS";
	const CAMPAIGN_ACTION_INCLUIR = "INCLUIR";
	const CAMPAIGN_ACTION_EXCLUIR = "EXCLUIR";
	
	const CAMPAIGN_STATUS_VIGENTE = "VIGENTE";
	const CAMPAIGN_STATUS_CANCELADO = "CANCELADO";
	const CAMPAIGN_STATUS_MORA = "MORA";
	
	const CAMPAIGN_FILTER_DIAS_ATRASO = "DIAS ATRASO";
	const CAMPAIGN_FILTER_FECHA_VENCIMIENTO = "FECHA VENCIMIENTO";
	const CAMPAIGN_FILTER_MONTO_COBRAR = "MONTO COBRAR";
	
	const CAMPAIGN_LOGIC_IGUAL_A = "IGUAL A";
	const CAMPAIGN_LOGIC_MAYOR_A = "MAYOR A";
	const CAMPAIGN_LOGIC_MENOR_A = "MENOR A";
	const CAMPAIGN_LOGIC_DISTINTO_A = "DISTINTO A";
	const CAMPAIGN_LOGIC_ENTRE = "ENTRE";
	
	const CAMPAIGN_ORIGIN_FECHA_DIA = 'FECHA DEL DIA';
	const CAMPAIGN_ORIGIN_FECHA_FIJA = 'FECHA FIJA';
	const CAMPAIGN_ORIGIN_DIAS_DIA_MENOS = 'FECHA DEL DIA MENOS x DIAS';
	const CAMPAIGN_ORIGIN_DIAS_DIA_MAS = 'FECHA DEL DIA MAS x DIAS';
	
	const CAMPAIGN_METODO_ENVIO_API = 'Automatico (API)';
	const CAMPAIGN_METODO_ENVIO_CSV = 'Carga Masiva (CSV)';
	const CAMPAIGN_METODO_ENVIO_SLACK = 'SLACK (CSV)';
	
	const CAMPAIGN_METODO_FORMATO_CSV = 'CSV';
	const CAMPAIGN_METODO_FORMATO_XLS = 'XLS';
	
	/**
	 * Devuelte las constantes del modelo Cronogramas
	 *
	 * @return array
	 */
	static function getConstants()
	{
		$oClass = new ReflectionClass(__CLASS__);
		return $oClass->getConstants();
	}
	
	/**
	 * Cronogramas constructor.
	 */
	public function __construct()
	{
		$this->db_solicitudes = $this->load->database('solicitudes', true);
		$this->maestro = $this->load->database('maestro', true);
		$this->solicitudes = $this->load->database('solicitudes', true);
		$this->db_buros = $this->load->database('api_buros', true);
		$this->db_gestion = $this->load->database('gestion', true);
		$this->db_campania = $this->load->database('campanias', true);
		$this->db_chat = $this->load->database('chat', true);
		$this->load->helper(array('formato_helper', 'form', 'url', 'my_date', 'formato'));
		$this->load->library('Sqlexceptions');
		$this->Sqlexceptions = new Sqlexceptions();
	}
	
	/**
	 * Obtiene los usuarios afectados por la query generada
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function getQueryAffected($data)
	{
		$campania = $this->getCampaignById(['id_camp' => $data['id_campania']]);
		$typeLogic = $campania['type_logic'];
		
		// ======================================= DESTINATARIO =======================================
		
		if ($data['destiny'] == Cronogramas::CAMPAIGN_RECEIVER_SOLICITANTES) {
			$db = 'solicitudes';
		} else {
			$db = 'maestro';
		}
		
		// ======================================= SUB QUERYS =======================================
		
		$subqueryBajaDatos = $this->$db->select('documento')
			->from('solicitudes.baja_datos')
			->get_compiled_select();
		
		$subqueryPorProcesar = $this->$db->select('id_cliente')
			->from('maestro.solicitud_imputacion')
			->where_in('por_procesar', [0,3])
			->get_compiled_select();
		
		$subQueryAction = $this->$db->select('id_cliente')
			->from('creditos')
			->group_by('id_cliente')
			->having('COUNT(creditos.id) >', $data['x_credits'])
			->get_compiled_select();
		
		// ======================================= TIPO CLIENTE ======================================= 
		
		if ($data['accion'] == Cronogramas::CAMPAIGN_ACTION_INCLUIR) {
			$this->$db->where("creditos.id_cliente IN ($subQueryAction)", null);
		} else if ($data['accion'] == Cronogramas::CAMPAIGN_ACTION_EXCLUIR) {
			$this->$db->where("creditos.id_cliente NOT IN ($subQueryAction)", null);
		}
		
		if ($data['client_type'] != Cronogramas::CAMPAIGN_CLIENT_TYPE_ALL) {
			$this->$db->where('solicitud.tipo_solicitud', $data['client_type']);
		}
		
		// ======================================= ESTADO =======================================
		
		$arrayStatus = explode(",", $data['estatus']);
		if (isset($data['estatus']) and count($arrayStatus) != 3) {
			if (
				in_array(Cronogramas::CAMPAIGN_STATUS_VIGENTE, $arrayStatus) and
				in_array(Cronogramas::CAMPAIGN_STATUS_CANCELADO, $arrayStatus)
			) {
				$this->$db->where("(credito_detalle.estado IS NULL OR credito_detalle.estado = 'pagado')");
			} else if (
				in_array(Cronogramas::CAMPAIGN_STATUS_VIGENTE, $arrayStatus) and
				in_array(Cronogramas::CAMPAIGN_STATUS_MORA, $arrayStatus)
			) {
				$this->$db->where("(credito_detalle.estado IS NULL OR credito_detalle.estado = 'mora')");
			} else if (
				in_array(Cronogramas::CAMPAIGN_STATUS_CANCELADO, $arrayStatus) and
				in_array(Cronogramas::CAMPAIGN_STATUS_MORA, $arrayStatus)
			) {
				$this->$db->where("(credito_detalle.estado = 'pagado' OR credito_detalle.estado = 'mora')");
			} else {
				if (in_array(Cronogramas::CAMPAIGN_STATUS_VIGENTE, $arrayStatus)) {
					$this->$db->where('credito_detalle.estado IS NULL', null);
				} else if (in_array(Cronogramas::CAMPAIGN_STATUS_CANCELADO, $arrayStatus)) {
					$this->$db->where('credito_detalle.estado', 'pagado');
				} else if (in_array(Cronogramas::CAMPAIGN_STATUS_MORA, $arrayStatus)) {
					$this->$db->where('credito_detalle.estado', 'mora');
				}
			}
		}
		
		// ======================================= FILTRO Y LOGICA =======================================
		
		// -------------------- DIAS DE ATRASO ----------------------------------------
		if ($data['filtro'] == Cronogramas::CAMPAIGN_FILTER_DIAS_ATRASO) {
			if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_IGUAL_A) {
				$this->$db->where('credito_detalle.dias_atraso', $data['valor1']);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_MAYOR_A) {
				$this->$db->where("credito_detalle.dias_atraso > ", $data['valor1']);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_MENOR_A) {
				$this->$db->where("credito_detalle.dias_atraso < ", $data['valor1']);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_DISTINTO_A) {
				$this->$db->where("credito_detalle.dias_atraso <> ", $data['valor1']);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_ENTRE) {
				$this->$db->where("credito_detalle.dias_atraso BETWEEN " . $data['valor1'] . " AND " . $data['valor2']);
			}
		}
		// -------------------- FECHA VENCIMIENTO ----------------------------------------
		
		if ($data['origen_desde'] == Cronogramas::CAMPAIGN_ORIGIN_FECHA_DIA) {
			$value1 = ' CURRENT_DATE() ';
		} else if ($data['origen_desde'] == Cronogramas::CAMPAIGN_ORIGIN_FECHA_FIJA) {
			$value1 = "'".$data['origen_desde_valor']."'";
		} else if ($data['origen_desde'] == Cronogramas::CAMPAIGN_ORIGIN_DIAS_DIA_MENOS) {
			$value1 = " DATE_SUB(CURRENT_DATE(), INTERVAL " . $data['origen_desde_valor'] . " DAY) ";
		} else if ($data['origen_desde'] == Cronogramas::CAMPAIGN_ORIGIN_DIAS_DIA_MAS) {
			$value1 = " DATE_ADD(CURRENT_DATE(), INTERVAL " . $data['origen_desde_valor'] . " DAY) ";
		}
		
		if ($data['origen_hasta'] == Cronogramas::CAMPAIGN_ORIGIN_FECHA_DIA) {
			$value2 = ' CURRENT_DATE() ';
		} else if ($data['origen_hasta'] == Cronogramas::CAMPAIGN_ORIGIN_FECHA_FIJA) {
			$value2 = "'".$data['origen_hasta_valor']."'";
		} else if ($data['origen_hasta'] == Cronogramas::CAMPAIGN_ORIGIN_DIAS_DIA_MENOS) {
			$value2 = " DATE_SUB(CURRENT_DATE(), INTERVAL " . $data['origen_hasta_valor'] . " DAY) ";
		} else if ($data['origen_hasta'] == Cronogramas::CAMPAIGN_ORIGIN_DIAS_DIA_MAS) {
			$value2 = " DATE_ADD(CURRENT_DATE(), INTERVAL " . $data['origen_hasta_valor'] . " DAY) ";
		}
		
		if ($data['filtro'] == Cronogramas::CAMPAIGN_FILTER_FECHA_VENCIMIENTO) {
			if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_IGUAL_A) {
				$this->$db->where("credito_detalle.fecha_vencimiento", $value1);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_MAYOR_A) {
				$this->$db->where("credito_detalle.fecha_vencimiento > ", $value1);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_MENOR_A) {
				$this->$db->where("credito_detalle.fecha_vencimiento < ", $value1);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_DISTINTO_A) {
				$this->$db->where("credito_detalle.fecha_vencimiento <> ", $value1);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_ENTRE) {
				$this->$db->where("credito_detalle.fecha_vencimiento BETWEEN " . $value1 . " AND " . $value2 );
			} 
		}
		// -------------------- MONTO COBRAR ----------------------------------------
		
		if ($data['filtro'] == Cronogramas::CAMPAIGN_FILTER_MONTO_COBRAR) {
			if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_IGUAL_A) {
				$this->$db->where("credito_detalle.monto_cobrar", $data['valor1']);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_MAYOR_A) {
				$this->$db->where("credito_detalle.monto_cobrar > ", $data['valor1']);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_MENOR_A) {
				$this->$db->where("credito_detalle.monto_cobrar < ", $data['valor1']);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_DISTINTO_A) {
				$this->$db->where("credito_detalle.monto_cobrar <> ", $data['valor1']);
			} else if ($data['logic'] == Cronogramas::CAMPAIGN_LOGIC_ENTRE) {
				$this->$db->where("credito_detalle.monto_cobrar BETWEEN " . $data['valor1'] . " AND " . $data['valor2']);
			}
		}
		
		$this->$db->where("agenda.fuente", 'PERSONAL');
		$this->$db->where("agenda_email.fuente", 'PERSONAL');
		
		$this->$db->where("clientes.documento NOT IN ($subqueryBajaDatos)", null);
		$this->$db->where("clientes.id NOT IN ($subqueryPorProcesar)", null);
		
		if ($data['client_type'] != Cronogramas::CAMPAIGN_CLIENT_TYPE_ALL) {
			//no necesito incluir solicitudes
			$select = '
				`agenda`.`numero` telefono, 
				`clientes`.`documento`, 
				`clientes`.`id` id_cliente, 
				`credito_detalle`.`estado` `credito_estado`, 
				`credito_detalle`.`dias_atraso`, 
				`clientes`.`nombres`, 
				CONCAT(\'"\',DATE_FORMAT(`credito_detalle`.`fecha_vencimiento`,\'%d-%m-%Y\'),\'"\') AS `fecha_vencimiento`,
				REPLACE(REPLACE(REPLACE(FORMAT(`credito_detalle`.`monto_cobrar`,0),".",	"@"),",","."),"@",",") AS `monto_cobrar`, 
				`credito_detalle`.`id_credito` ';
			
			if ($typeLogic == 'MAIL') {
				$select .= ',`agenda_email`.`cuenta` email';
			}
			
		} else {
			// primaria y retanqueo
			$select = '
				`agenda`.`numero` telefono, 
				`clientes`.`documento`, 
				`clientes`.`id` id_cliente, 
				`credito_detalle`.`estado` `credito_estado`, 
				`credito_detalle`.`dias_atraso`, 
				`clientes`.`nombres`, 
				CONCAT(\'"\',DATE_FORMAT(`credito_detalle`.`fecha_vencimiento`,\'%d-%m-%Y\'),\'"\') AS `fecha_vencimiento`,
				REPLACE(REPLACE(REPLACE(FORMAT(`credito_detalle`.`monto_cobrar`,0),".",	"@"),",","."),"@",",") AS `monto_cobrar`,
				`credito_detalle`.`id_credito`';
			
			if ($typeLogic == 'MAIL') {
				$select .= ',`agenda_email`.`cuenta` email';
			}
		}
		
		$this->$db->select($select)
			->from('`maestro`.`creditos` AS `creditos`')
			->join('`solicitudes`.`solicitud` AS `solicitud`', '`solicitud`.`id_credito` = `creditos`.`id`')
			->join('`maestro`.`credito_detalle` AS `credito_detalle`', '`credito_detalle`.`id_credito` = `creditos`.`id`')
			->join('`maestro`.`clientes` AS `clientes`', '`creditos`.`id_cliente` = `clientes`.`id`')
			->join('`maestro`.`agenda_telefonica` AS `agenda`', '`clientes`.`id` = `agenda`.`id_cliente`')
		;
		
		if ($typeLogic == 'MAIL') {
			$this->$db->join('`maestro`.`agenda_mail` as `agenda_email`', '`clientes`.`id` = `agenda_email`.`id_cliente`');
		}

//		$this->$db->limit(10);
		$this->$db->group_by('`maestro`.`clientes`.`id`');
		
		$query = $this->$db->get()->result();

//		echo '<pre>' . var_export($this->$db->last_query(), true) . '</pre>';
//		die();
		return $query;
	}
	
	/**
	 * Obtiene los mensajes programados de un dia y hora especifica de la campania
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function checkMensajeProgramdoDayHour($data)
	{
		$this->db_campania->select('*');
		$this->db_campania->from('campanias_mensajes_programados');
		$this->db_campania->where('id_campania', $data['id_camp']);
		$this->db_campania->where('day', $data['day']);
		$this->db_campania->where('hour', $data['hour']);
		$query = $this->db_campania->get();
		return $query->result_array();
	}
	
	/**
	 * Guarda el mensaje y a que hora debera ejecutarse
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function saveMensajeProgramado($data)
	{
		$this->db_campania->insert('campanias_mensajes_programados', $data);
		
		if ($this->db_campania->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Obtiene todos los mensajes programados del dia y campaña especificados
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public function getAllMensajesProgramados($data)
	{
		$this->db_campania->select('mp.*, m.mensaje');
		$this->db_campania->from('campanias_mensajes_programados mp');
		$this->db_campania->join('campanias_mensajes AS m', 'mp.id_mensaje = m.id_mensaje');
		$this->db_campania->where('mp.id_campania', $data['id_camp']);
		$this->db_campania->where('day', $data['day']);
		$this->db_campania->order_by("hour", "ASC");
		$query = $this->db_campania->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return -1;
		}
	}
	
	/**
	 * Borra el mensaje programado
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function deleteMensajeProgramado($data)
	{
		$this->db_campania->where('id', $data['id_mensaje']);
		$this->db_campania->delete('campanias_mensajes_programados');
		$delete = $this->db_campania->affected_rows();
		return $delete;
	}
	
	/**
	 * Comprueba si existe el mensaje en algun mensaje programado
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function checkDeleteMsg($data)
	{
		$this->db_campania->select('*');
		$this->db_campania->from('campanias_mensajes_programados');
		$this->db_campania->where('id_campania', $data['id_campania']);
		$this->db_campania->where('id_mensaje', $data['id_mensaje']);
		$query = $this->db_campania->get();
		return $query->result_array();
	}
	
	/**
	 * Obtiene el mensaje programado especifico
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public function getMensajeProgramado($data)
	{
		$this->db_campania->select('mp.*, m.mensaje, m.query_contenido');
		$this->db_campania->from('campanias_mensajes_programados mp');
		$this->db_campania->join('campanias_mensajes AS m', 'mp.id_mensaje = m.id_mensaje');
		$this->db_campania->where('mp.id', $data['id']);
		$query = $this->db_campania->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return -1;
		}
	}
	
	/**
	 * Busca los eventos para el calendario
	 *
	 * @param null $idCampania
	 *
	 * @return mixed
	 */
	public function buscarEventos($idCampania = null)
	{
		$sql = "select c.nombre_logica                     as title,
					   c.color                             as color,
					   cmp.hour                            as start,
					   concat(substring(cmp.hour, 1, 6), ':10') as end,
					   cmp.id_campania,
					   c.type_logic,
					   cm.mensaje,
					   cmp.day,
       				   cmp.id id,
					   case
						   when day = 'Lunes' then 1
						   when day = 'Martes' then 2
						   when day = 'Miercoles' then 3
						   when day = 'Jueves' then 4
						   when day = 'Viernes' then 5
						   when day = 'Sabado' then 6
						   when day = 'Domingo' then 7 end as weekDay
				from campanias_mensajes_programados cmp
						 inner join campania c on cmp.id_campania = c.id_logica
						 inner join campanias_mensajes cm on cm.id_mensaje = cmp.id_mensaje
				";
		
		if (!is_null($idCampania)) {
			$sql .= " having cmp.id_campania = " . $idCampania;
		}
		
		$consulta = $this->db_campania->query($sql);
		return $consulta->result_array();
	}
	
	/**
	 * @param $idCampania
	 *
	 * @return array
	 */
	public function buscarEventosCancelados($idCampania = null)
	{
		if (!is_null($idCampania)) {
			$this->db_campania->join('campanias_mensajes_programados cmp', 'id_mensaje_programado = cmp.id');
			$this->db_campania->where('cmp.id_campania', $idCampania);
		}
		
		$query = $this->db_campania->get('campanias_mensajes_programados_cancelados');
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return [];
		}
	}
	
	/**
	 * Obtiene los emails notificados de la campania
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public function getEmailNotificados($data)
	{
		$this->db_campania->where('id_campania', $data['camp_id']);
		
		$query = $this->db_campania->get('campanias_emails_notificados');
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return -1;
		}
	}
	
	/**
	 * Guardia un email de notificados en la campania
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function saveEmailNotificados($data)
	{
		$this->db_campania->insert('campanias_emails_notificados', $data);
		if ($this->db_campania->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Borra un email de la lista de notificados de la campania
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function deleteEmailNotificados($data)
	{
		$this->db_campania->where('email', $data['email']);
		$this->db_campania->where('id_campania', $data['camp_id']);
		$this->db_campania->delete('campanias_emails_notificados');
		$delete = $this->db_campania->affected_rows();
		return $delete;
	}
	
	/**
	 * Busca un email de notificados en la campania
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function checkCampaniaNotificados($data)
	{
		$this->db_campania->select('*');
		$this->db_campania->from('campanias_emails_notificados');
		$this->db_campania->where('id_campania', $data['']);
		$this->db_campania->where('email', $data['']);
		$query = $this->db_campania->get();
		return $query->result_array();
	}
	
	/**
	 * Actualiza una campania
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public function updateCampain($data)
	{
		$this->db_campania->where('id_logica', $data['camp_id']);
		unset($data['camp_id']);
		$this->db_campania->update('campania', $data);
		
		if ($this->db_campania->trans_status() === false) {
			return -1;
		} else {
			return 1;
		}
		
	}
	
	/**
	 * Guarda una campania
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public function saveCampain($data)
	{
		$this->db_campania->insert('campania', $data);
		
		if ($this->db_campania->affected_rows() > 0) {
			return $this->db_campania->insert_id();
		} else {
			return -1;
		}
		
	}
	
	/**
	 * Obtiene todos los mensajes de una campania
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public function getAllActiveMensajes($data)
	{
		$this->db_campania->select('M.id_mensaje,M.mensaje,M.prederterminado as pre,M.estado');
		$this->db_campania->from('campanias_mensajes AS M');
		$this->db_campania->join('campania C', 'M.id_campania = C.id_logica');
		$this->db_campania->where('C.id_logica', $data['id_camp']);
		$this->db_campania->where('M.estado', 1);
		$this->db_campania->order_by("M.id_mensaje", "ASC");
		$query = $this->db_campania->get();
		//var_dump($this->db_campania->last_query());die;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return -1;
		}
	}
	
	/**
	 * Check if the campain already has an default message excluding the editing message
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function checkCampaniaPredet($data)
	{
		$this->db_campania->select('*');
		$this->db_campania->from('campanias_mensajes');
		$this->db_campania->where('id_campania', $data['id_camp']);
		$this->db_campania->where('prederterminado', '1');
		$this->db_campania->where('id_mensaje !=', $data['id_mensaje']);
		$query = $this->db_campania->get();
		return $query->result_array();
	}
	
	/**
	 * Guarda un mensaje de la campania
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function saveMessage($data)
	{
		$this->db_campania->insert('campanias_mensajes', $data);
		if ($this->db_campania->affected_rows() > 0) {
			$id_mensaje = $this->db_campania->insert_id();
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Update campain message
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function updateMessage($data)
	{
		$this->db_campania->where('id_mensaje', $data['id_mensaje']);
		unset($data['id_mensaje']);
		$this->db_campania->update('campanias_mensajes', $data);
		if ($this->db_campania->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Delete campain message
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function deleteMessage($data)
	{
		$this->db_campania->where('id_mensaje', $data['id_mensaje']);
		$this->db_campania->delete('campanias_mensajes');
		$delete = $this->db_campania->affected_rows();
		return $delete;
	}
	
	/**
	 * Obtiene un mensaje especifico
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public function getMensaje($data)
	{
		$this->db_campania->select('*');
		$this->db_campania->from('campanias_mensajes');
		$this->db_campania->where('id_mensaje', $data['id_mensaje']);
		$query = $this->db_campania->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return -1;
		}
	}
	
	/**
	 * @param $data
	 *
	 * @return int
	 */
	public function getAllMensajes($data)
	{
		$this->db_campania->select('M.id_mensaje,M.mensaje,M.prederterminado as pre,M.estado');
		$this->db_campania->from('campanias_mensajes AS M');
		$this->db_campania->join('campania C', 'M.id_campania = C.id_logica');
		$this->db_campania->where('C.id_logica', $data['id_camp']);
		$this->db_campania->order_by("M.id_mensaje", "ASC");
		$query = $this->db_campania->get();
		//var_dump($this->db_campania->last_query());die;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return -1;
		}
	}
	
	
	/**
	 * Obtain a campaign by id
	 * 
	 * @param $idCampania
	 *
	 * @return mixed
	 */
	public function getCampaignById($idCampania)
	{
		return $this->db_campania->select('L.*, P.nombre_proveedor as proveedor')
			->from('campania as L')
			->join('maestro.proveedores P', 'L.id_proveedor = P.id_proveedor', 'left')
			->where('id_logica', $idCampania)
			->get()->row_array();
	}
	
	/**
	 * Obtiene todas las campanias
	 *
	 * @return mixed
	 */
	public function getAllCampanias()
	{
		$this->db_campania->select('L.id_logica,P.nombre_proveedor as proveedor,L.type_logic,L.nombre_logica,L.estado');
		$this->db_campania->from('campania AS L');
		$this->db_campania->join('maestro.proveedores P', 'L.id_proveedor = P.id_proveedor', 'left');
		$this->db_campania->order_by("L.id_logica", "ASC");
		
		$query = $this->db_campania->get();
		return $query->result_array();
	}
	
	
	/**
	 * Guarda los filtros de una campania
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function saveFiltrosCampanias($data)
	{
		$this->db_campania->insert('campanias_filtros', $data);
		
		if ($this->db_campania->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Comprueba si existen filtros guardados en la campania
	 *
	 * @param $idCampania
	 *
	 * @return bool
	 */
	public function checkCampaniasFiltros($idCampania)
	{
		$this->db_campania->select('*');
		$this->db_campania->from('campanias_filtros');
		$this->db_campania->where('id_campania', $idCampania);
		$query = $this->db_campania->get();
		if ($query->num_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Actualiza los filtros del a campania
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function updateFiltrosCampanias($data)
	{
		$this->db_campania->where('id_campania', $data['id_campania']);
		$this->db_campania->update('campanias_filtros', $data);
		
		if ($this->db_campania->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Obtiene los filtros de una campania
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function getFiltrosCampanias($data)
	{
		$this->db_campania->select('*');
		$this->db_campania->from('campanias_filtros');
		$this->db_campania->where('id_campania', $data['camp_id']);
		$query = $this->db_campania->get();
		return $query->row_array();
	}
	
	
	/**
	 * Guarda el metodo de envio
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function saveMethod($data)
	{
		$this->db_campania->set('metodo', $data['metodo']);
		$this->db_campania->where('id_logica', $data['camp_id']);
		$this->db_campania->update('campania');
		
		if ($this->db_campania->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Guarda el formato de envio
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function saveFormat($data)
	{
		$this->db_campania->set('formato', $data['formato']);
		$this->db_campania->where('id_logica', $data['camp_id']);
		$this->db_campania->update('campania');
		
		if ($this->db_campania->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Ejecuta una query
	 *
	 * @param $sql
	 * @param string $db
	 *
	 * @return mixed
	 */
	public function runQuery($sql, $db = 'maestro')
	{
		$result = $this->$db->query($sql);
		return $result->result();
	}
	
	
	/**
	 * Inserta o actualiza la cancelacion de mensajes programados
	 * @param $data
	 *
	 * @return bool
	 */
	public function updateMsgProgCanceled($data)
	{
		
		$this->db_campania->where('id_mensaje_programado', $data['id_mensaje_programado']);
		$this->db_campania->where('fecha', $data['fecha']);
		$this->db_campania->from('campanias_mensajes_programados_cancelados');
		$q = $this->db_campania->get();
		
		
		if ( $q->num_rows() > 0 ) {
			$this->db_campania->where('id_mensaje_programado', $data['id_mensaje_programado']);
			$this->db_campania->update('campanias_mensajes_programados_cancelados', $data);
		} else {
			$this->db_campania->insert('campanias_mensajes_programados_cancelados', $data);
		}
		
//		echo '<pre>' . var_export($this->db_campania->last_query(), true) . '</pre>';
//		die();
		
		if ($this->db_campania->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Obtiene los slackid notificados de la campania
	 *
	 * @param $data
	 *
	 * @return int
	 */
	public function getSlackNotificados($campaignId)
	{
		$this->db_campania->where('id_campania', $campaignId);
		
		$query = $this->db_campania->get('campanias_slack_notificados');
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return [];
		}
	}
	
	/**
	 * Guardia un slackid de notificados en la campania
	 *
	 * @param $data
	 *
	 */
	public function saveSlacklNotificados($data)
	{
		$this->db_campania->where('slack_id', $data['slack_id']);
		$this->db_campania->where('id_campania', $data['id_campania']);
		
		$query = $this->db_campania->get('campanias_slack_notificados');
		if ($query->num_rows() > 0) {
			//existe, no hago nada
		} else {
			$this->db_campania->insert('campanias_slack_notificados', $data);
		}
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getProximosMensajesProgramados()
	{
		$hora = date("H:i");
		
		$dayOfWeek = date("l", strtotime('now'));
		switch ($dayOfWeek) {
			case 'Sunday':
				$dia = 'Domingo';
				break;
			case 'Monday':
				$dia = 'Lunes';
				break;
			case 'Tuesday':
				$dia = 'Martes';
				break;
			case 'Wednesday':
				$dia = 'Miercoles';
				break;
			case 'Thursday':
				$dia = 'Jueves';
				break;
			case 'Friday':
				$dia = 'Viernes';
				break;
			case 'Saturday':
				$dia = 'Sabado';
				break;
		}
		
		//pregunto por los proximos 5 min
		$this->db_campania->where('hour = ADDTIME("' . $hora . ':00", "500")');
		$this->db_campania->where('day', $dia);
		$query = $this->db_campania->get('campanias_mensajes_programados');
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return [];
		}
	}
	
	/**
	 * Obtiene todos los whatsapp templates activos
	 * 
	 * @return mixed
	 */
	public function getActiveWhatsappTemplate() {
		return $this->getAllWhatsappTemplates(1);
	}
	
	/**
	 * Obtiene todos los whatsapp templates
	 * 
	 * @param $status
	 *
	 * @return mixed
	 */
	public function getAllWhatsappTemplates($status = null)
	{
		$result =  $this->db_chat->select('*')
			->from('templates')
			->where('tipo_template', 'WAPP');

		if ($status !== null) {
			$result->where('estado', $status);
		}
		
		return $result->get()->result_array();
	}
	
	/**
	 * Guarda los whatsapp templates
	 * 
	 * @param $campaignId
	 * @param $templateId
	 *
	 * @return false
	 */
	public function saveCronogramaCampaniasWhatsappTemplateId($campaignId, $templateId)
	{
		$data = array(
			'id_campania' => $campaignId,
			'id_template' => $templateId,
		);
		$this->db_campania->insert('campanias_templates_whatsapp', $data);
		$id = $this->db_campania->insert_id();
		
		if ($id > 0) {
			return $id;
		} else {
			return false;
		}
	}
	
	/**
	 * obtiene los whatsapp templates asociados a una campania
	 * 
	 * @param $campaignId
	 *
	 * @return array
	 */
	public function getCronogramaCampaniasWhatsappTemplates($campaignId)
	{
		$result = $this->db_campania->select('w.*, t.msg_string')
			->from('campanias.campanias_templates_whatsapp w')
			->join('chat.templates t', 'w.id_template = t.id', 'left')
			->where('w.id_campania', $campaignId)
			->get()->result_array();
		
		if ($result) {
			return $result;
		} else {
			return [];
		}
	}
	
	/**
	 * borra un whatsapp template de una campania
	 * 
	 * @param $id
	 *
	 * @return bool
	 */
	public function deleteWhatsappTemplateById($id)
	{
		$this->db_campania->where('id', $id);
		$this->db_campania->delete('campanias_templates_whatsapp');
		$affectd = $this->db_campania->affected_rows();
		
		return $affectd > 0;
	}
	
	/**
	 * Obtiene un template por id
	 * 
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getWhatsappTemplateById($id)
	{
		$result = $this->db_chat->select('*')
			->from('templates')
			->where('id', $id)
			->get()->row_array();
		
		return $result;
	}
	
	/**
	 * Obtiene todos los eventos unicos que coincidan con la fecha y hora actuales
	 * 
	 * @return mixed
	 */
	public function getAllNearUniqueEvents()
	{
		$current_date = date('Y-m-d H:i:s');
		$response = $this->maestro->select('*')
			->from('events')
			->where("TIMESTAMPDIFF(SECOND,run_date,'$current_date') between '0' and '60'")
			->where('enabled', 1)
			->get()->result_array();
		
		return $response;
	}
	
	/**
	 * Filtra los registros por id obtienendo solo los que corresponden a la hora actual
	 * 
	 * @param $ids
	 *
	 * @return mixed
	 */
	public function getFilteredEventsForTime($ids)
	{
		$current_time = date('H:i:s');
		$response = $this->maestro->select('*')
			->from('events')
			->where("TIME_TO_SEC(TIMEDIFF('$current_time', run_hour)) between '0' and '60'")
			->where('enabled', 1)
			->where_in('id', $ids)
			->get()->result_array();
		
		return $response;
	}
	
	/**
	 * Obtiene todos los eventos que coincidan con la hora actual
	 * 
	 * @return mixed
	 */
	public function getAllEventsNearWithHours()
	{
		$current_time = date('H:i:s');
		$response = $this->maestro->select('*')
			->from('events')
			->where("TIME_TO_SEC(TIMEDIFF('$current_time', run_hour)) between '0' and '60'")
			->where("type != 'unique'")
			->where('run_weak_days = ""')
			->where('run_day = ""')
			->where('run_month = ""')
			->where('enabled', 1)
			->get()->result_array();
		
		return $response;
	}
	
	/**
	 * Obtiene todos los eventos que coincidan con el dd/mm actual
	 * 
	 * @return mixed
	 */
	public function getAllNearYearEvent()
	{
		$current_date = date('Y-m-d H:i:s');
		$response = $this->maestro->select('*')
			->from('events')
			->where("concat(concat(LPAD(month('$current_date'), 2 , '0' ), '/'), LPAD(day('$current_date'),2,'0')) = run_month")
			->where('enabled', 1)
			->get()->result_array();
		
		return $response;
	}
	
	/**
	 * Obtiene todos los eventos que coincidan con el dia actual
	 * 
	 * @return mixed
	 */
	public function getAllNearMonthEvent()
	{
		$current_date = date('Y-m-d H:i:s');
		$response = $this->maestro->select('*')
			->from('events')
			->where("LPAD(day('$current_date'),2,'0') = run_day")
			->where('enabled', 1)
			->get()->result_array();
		
		return $response;
	}
	
	/**
	 * Obtiene todos los eventos del dia de la semana actual
	 * 
	 * @return mixed
	 */
	public function getAllNearWeakEvent()
	{
		$weekDay = date('w');
		$response = $this->maestro->select('*')
			->from('events')
			->where("SUBSTRING(run_weak_days, $weekDay, 1) = '1'")
			->where('enabled', 1)
			->get()->result_array();
		
		return $response;
	}
	
	
	/**
	 * Guarda un prelanzamiento
	 * 
	 * @param $idCampania
	 * @param $templateId
	 * @param $idEvent
	 *
	 * @return mixed
	 */
	public function savePreLanzamiento($idCampania, $templateId, $idEvent)
	{
		$data = [
			'id_campania' => $idCampania,
			'id_template' => $templateId,
			'id_event' => $idEvent,
		];
		
		$this->db_campania->insert('campania_prelanzamiento', $data);
		
		return $this->db_campania->insert_id();
	}
	
	/**
	 * Obtiene todos los prelanzamientos creados hasta hace 1 hora
	 * 
	 * @return mixed
	 */
	public function getPrelanzamiento()
	{
		$result = $this->db_campania->select('c.*, cp.id_template, cp.id_event , cp.id prelanzamiento_id')
			->from('campania_prelanzamiento cp')
			->join('campania c', 'c.id_logica = cp.id_campania')
			->where('cp.created_at >= DATE_SUB(NOW(),INTERVAL 1 HOUR)')
			->where('cp.estado', 0)
			->get()->result_array();
		
		return $result;
	}
	
	/**
	 * Marca un prelanzamiento como enviado
	 * 
	 * @param $id
	 *
	 * @return void
	 */
	public function markAsSendedPrelanzamiento($id)
	{
		$this->db_campania->where('id', $id);
		$this->db_campania->update('campania_prelanzamiento', ['estado' => 1]);
	}

	public function getMensageId($idCampania)
	{
		$this->db_campania->select("id_mensaje");
		$this->db_campania->from("campanias_mensajes");
		$this->db_campania->where("id_campania", $idCampania);
		$mensaje = $this->db_campania->get();
		return $mensaje->result_array();
	}

	public function getMensajeSms($idCampania, $templateId)
	{
		$this->db_campania->select("c.mensaje");
		$this->db_campania->from("campanias_mensajes AS c");
		$this->db_campania->where("c.id_mensaje", $templateId);
		$this->db_campania->where("c.id_campania", $idCampania);
		$mensaje = $this->db_campania->get();
		return $mensaje->result_array();
	}
}

