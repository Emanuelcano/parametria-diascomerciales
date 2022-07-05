<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tablero_model extends CI_Model
{
	public function __Construct()
	{
		parent::__Construct();
		$this->db = $this->load->database('gestion', TRUE);
		$this->db_solicitud = $this->load->database('solicitudes', TRUE);
		$this->db_maestro = $this->load->database('maestro', TRUE);
		$this->db_parametria = $this->load->database('parametria', TRUE);
		$this->db_chat = $this->load->database('chat', TRUE);
	}

	// chats activos periodo por operador
	public function chats_activos($vencimiento_menos, $vencimiento_mas){
		$this->db_chat->select("COUNT(id) as cantidad, id_operador");
		$this->db_chat->from("new_chats");
		$this->db_chat->where("status_chat = 'activo' and `from` in ( SELECT `maestro`.`agenda_telefonica`.`numero` FROM `maestro`.`creditos` INNER JOIN `maestro`.`clientes` ON `maestro`.`clientes`.`id` = `creditos`.`id_cliente` INNER JOIN `maestro`.`agenda_telefonica` ON `maestro`.`agenda_telefonica`.`id_cliente` = `creditos`.`id_cliente` INNER JOIN `maestro`.`credito_detalle` ON `maestro`.`credito_detalle`.`id_credito` = `maestro`.`creditos`.`id` WHERE `maestro`.`credito_detalle`.`fecha_vencimiento` BETWEEN '".$vencimiento_menos."' AND '".$vencimiento_mas."' AND `maestro`.`creditos`.`estado` = 'vigente' AND `maestro`.`agenda_telefonica`.`fuente` = 'PERSONAL' GROUP BY `maestro`.creditos.id_cliente )");
		$this->db_chat->group_by('id_operador');
		$query = $this->db_chat->get();
		return $query->result();
	}
	// Templates enviados por operador
	public function templates_enviados($vencimiento_menos, $vencimiento_mas){
		$this->db_chat->select("COUNT(sent_messages.id) as cantidad, `sent_messages`.id_operador");
		$this->db_chat->from("`new_chats`, sent_messages");
		$this->db_chat->where("`new_chats`.id = `sent_messages`.id_chat and `sent_messages`.id_template > 0 and `new_chats`.`from` in ( SELECT `maestro`.`agenda_telefonica`.`numero` FROM `maestro`.`creditos` INNER JOIN `maestro`.`clientes` ON `maestro`.`clientes`.`id` = `creditos`.`id_cliente` INNER JOIN `maestro`.`agenda_telefonica` ON `maestro`.`agenda_telefonica`.`id_cliente` = `creditos`.`id_cliente` INNER JOIN `maestro`.`credito_detalle` ON `maestro`.`credito_detalle`.`id_credito` = `maestro`.`creditos`.`id` WHERE `maestro`.`credito_detalle`.`fecha_vencimiento` BETWEEN '".$vencimiento_menos."' AND '".$vencimiento_mas."' AND `maestro`.`creditos`.`estado` = 'vigente' AND `maestro`.`agenda_telefonica`.`fuente` = 'PERSONAL' GROUP BY `maestro`.creditos.id_cliente )");
		$this->db_chat->group_by('`sent_messages`.id_operador');
		$query = $this->db_chat->get();
		return $query->result();
	}


	public function cargarIndicadores_operador($operador, $param)
	{
		$this->db->select('sum(ca.asignados) asignados, sum(ca.validados) validados , sum(ca.verificados) verificados');
		$this->db->from('control_asignaciones ca');
		$this->db->group_by('ca.id_operador');
		$this->db->where('ca.id_operador =' .$operador);
		if (isset($param["dia"])){		$this->db->where('DAY(ca.fecha_control) = ' .$param["dia"]);}
		if (isset($param["mes"])){		$this->db->where('MONTH(fecha_control)  = ' .$param["mes"]);}
		if (isset($param["anho"])){		$this->db->where('YEAR(fecha_control)  = ' .$param["anho"]);}
		//if (isset($param["equipo"]) && $param["equipo"] != 'GENERAL' && $param["equipo"] != ''){	$this->db->where('op.equipo', "'".$param["equipo"]."'");}
		
		$query = $this->db->get();
		return $query->result();
	} 

	public function cargarIndicadores_tablero($operador, $param)
	{
		$this->db->select('sum(ca.asignados) cantidad');
		$this->db->from('control_asignaciones ca');
		$this->db->group_by('ca.id_operador');
		$this->db->where('ca.id_operador =' .$operador);
		if (isset($param["dia"])){		$this->db->where('DAY(ca.fecha_control) = ' .$param["dia"]);}
		if (isset($param["mes"])){		$this->db->where('MONTH(fecha_control)  = ' .$param["mes"]);}
		if (isset($param["anho"])){		$this->db->where('YEAR(fecha_control)  = ' .$param["anho"]);}
		//if (isset($param["equipo"]) && $param["equipo"] != 'GENERAL' && $param["equipo"] != ''){	$this->db->where('op.equipo', "'".$param["equipo"]."'");}
		
		$query = $this->db->get();
		return $query->result();
	} 

	public function get_opreadores_asignacion($param)
	{
		$this->db->select('op.idoperador, op.nombre_apellido, sum(ca.asignados) asignados, sum(ca.aprobados) aprobados');
		$this->db->from('control_asignaciones ca');
		$this->db->join('operadores AS op', 'ca.id_operador = op.idoperador');
		$this->db->where('op.estado = 1');
		if (isset($param["tipo_operador"])){	$this->db->where('op.tipo_operador', $param["tipo_operador"]);}
		if (isset($param["equipo"]) && $param["equipo"] != 'GENERAL' && $param["equipo"] != ''){	$this->db->where('op.equipo', $param["equipo"]);}
		if (isset($param["sub"])){  $this->db->where( 'op.idoperador IN (SELECT id_operador FROM control_asignaciones WHERE fecha_control BETWEEN "'.$param["inicio"].'" AND "'.$param["fin"].'")');}
		$this->db->group_by('ca.id_operador');
		$query = $this->db->get();
		// var_dump($this->db->last_query());die;
		return $query->result_array();
	} 
	public function get_objetivo($param){
		$this->db->select('*');
		$this->db->from('objetivos_tablero');
		$this->db->where('id', $param['id']);
		$query = $this->db->get();
		return $query->result();
	}

	public function get_retanqueo($operador, $param){
		$this->db->select('COUNT(track_gestion.id) cantidad');
		$this->db->from('track_gestion');
		$this->db->from('solicitudes.solicitud sol');
		$this->db->where('id_operador =' .$operador);
		$this->db->where('observaciones LIKE "[APROBADO]"');
		if (isset($param["dia"])){		$this->db->where('DAY(fecha) = ' .$param["dia"]);}
		if (isset($param["mes"])){		$this->db->where('MONTH(fecha)  = ' .$param["mes"]);}
		if (isset($param["anho"])){		$this->db->where('YEAR(fecha)  = ' .$param["anho"]);}
		if (isset($param["retanqueo"]) && $param["retanqueo"] =="true"){	$this->db->where('id_solicitud IN (SELECT id FROM solicitudes.solicitud WHERE tipo_solicitud="RETANQUEO")');}
		if (isset($param["retanqueo"]) && $param["retanqueo"] =="false"){	$this->db->where('id_solicitud IN (SELECT id FROM solicitudes.solicitud WHERE tipo_solicitud!="RETANQUEO")');}
		$this->db->where('sol.id = track_gestion.id_solicitud');
		
		$query = $this->db->get();
		//var_dump($this->db->last_query());
		return $query->result_array();
	}

	// total de solicitudes asignadas por tipo y fecha	
	public function get_solicitudes_asignadas($operador, $param) {	


		/*$this->db->select('COUNT(id) cantidad');	
		$this->db->from('relacion_operador_solicitud');	
		$this->db->where('id_operador =' .$operador);	

		if (isset($param["dia"])){		$this->db->where('DAY(fecha_registro) = ' .$param["dia"]);}	
		if (isset($param["mes"])){		$this->db->where('MONTH(fecha_registro)  = ' .$param["mes"]);}	
		if (isset($param["anho"])){		$this->db->where('YEAR(fecha_registro)  = ' .$param["anho"]);}	

		$this->db->where('id_solicitud in (SELECT id FROM `solicitudes`.`solicitud`	WHERE `operador_asignado` = '.$operador.' AND `tipo_solicitud` LIKE "'.$param["tipo_solicitud"].'")');	



		$query = $this->db->get();	
		//var_dump($this->db->last_query());die;	
		return $query->result_array();*/
		
		


		$this->db_solicitud->select('COUNT(id) cantidad,operador_asignado');	
		$this->db_solicitud->from('solicitud');	

		if (isset($param["dia"])){	
			$this->db_solicitud->where('id in (SELECT id_solicitud FROM gestion.relacion_operador_solicitud	WHERE fecha_registro BETWEEN "' .$param["dia"].' 00:00:00" and "' .$param["dia"].' 23:59:59" )');	

		} else {	
			$this->db_solicitud->where("id in (SELECT id_solicitud FROM gestion.relacion_operador_solicitud	WHERE  fecha_registro >='". $param['fecha']."')");	
			
		}

		if(isset($param["situacion"]) && $param["situacion"] == "independiente") {	$this->db_solicitud->where('id_situacion_laboral = 3');  }
		if(isset($param["situacion"]) && $param["situacion"] == "dependiente") {	$this->db_solicitud->where('id_situacion_laboral in (1, 4, 7)');  }
		
		$this->db_solicitud->where('tipo_solicitud = "'.$param["tipo_solicitud"].'"');

		if(isset($param['estado'])){ $this->db_solicitud->where('estado in (' . $param['estado']. ')');}
		$this->db_solicitud->where('operador_asignado > 0');


		$this->db_solicitud->group_by('operador_asignado');

	
		
		
		
		$query = $this->db_solicitud->get();	
		//var_dump($this->db->last_query());
		return $query->result_array();

	}	

	//total de solicitudes aprobadas por tipo y fecha	

	public function get_solicitudes_aprobadas($operador, $param){	


		/* $this->db->select('COUNT(id) cantidad');	
		$this->db->from('relacion_operador_solicitud');	
		$this->db->where('id_operador =' .$operador);	

		if (isset($param["dia"])){		$this->db->where('DAY(fecha_registro) = ' .$param["dia"]);}	
		if (isset($param["mes"])){		$this->db->where('MONTH(fecha_registro)  = ' .$param["mes"]);}	
		if (isset($param["anho"])){		$this->db->where('YEAR(fecha_registro)  = ' .$param["anho"]);}	

		if (isset($param["dia"])){	
				$this->db->where('id_solicitud in (SELECT id_solicitud FROM `track_gestion`	WHERE `id_operador` = '.$operador.	
						' AND `observaciones` LIKE "[APROBADO]" AND DAY(fecha) = ' .$param["dia"].' AND MONTH(fecha)  = ' .$param["mes"].' AND YEAR(fecha)  = ' .$param["anho"].')');	

		} else {	
			$this->db->where('id_solicitud in (SELECT id_solicitud FROM `track_gestion`	WHERE `id_operador` = '.$operador.	
						' AND `observaciones` LIKE "[APROBADO]" AND MONTH(fecha)  = ' .$param["mes"].' AND YEAR(fecha)  = ' .$param["anho"].')');	

		}	

		$this->db->where('id_solicitud in (SELECT id FROM `solicitudes`.`solicitud`	WHERE `id_operador` = '.$operador.	
				'  AND `tipo_solicitud` LIKE "'.$param["tipo_solicitud"].'") ');	
		$query = $this->db->get();	
		//var_dump($this->db->last_query());	
		return $query->result_array(); */	


		$this->db->select('COUNT(id) cantidad');	
		$this->db->from('relacion_operador_solicitud');	
		$this->db->where('id_operador =' .$operador);	

		if (isset($param["dia"])){		$this->db->where('DAY(fecha_registro) = ' .$param["dia"]);}	
		if (isset($param["mes"])){		$this->db->where('MONTH(fecha_registro)  = ' .$param["mes"]);}	
		if (isset($param["anho"])){		$this->db->where('YEAR(fecha_registro)  = ' .$param["anho"]);}	

		if (isset($param["dia"])){	
				$this->db->where('id_solicitud in (SELECT id_solicitud FROM `track_gestion`	WHERE `id_operador` = '.$operador.	
						' AND `observaciones` LIKE "[APROBADO]" AND DAY(fecha) = ' .$param["dia"].' AND MONTH(fecha)  = ' .$param["mes"].' AND YEAR(fecha)  = ' .$param["anho"].')');	

		} else {	
			$this->db->where('id_solicitud in (SELECT id_solicitud FROM `track_gestion`	WHERE `id_operador` = '.$operador.	
						' AND `observaciones` LIKE "[APROBADO]" AND MONTH(fecha)  = ' .$param["mes"].' AND YEAR(fecha)  = ' .$param["anho"].')');	

		}	

		$this->db->where('id_solicitud in (SELECT id FROM `solicitudes`.`solicitud`	WHERE `id_operador` = '.$operador.	
				'  AND `tipo_solicitud` LIKE "'.$param["tipo_solicitud"].'") ');	
		$query = $this->db->get();	
		//var_dump($this->db->last_query());	
		return $query->result_array();	

	}

	public function primarias_hoy($id_operador, $tipo = null, $estado = null){

		$this->db->select("fecha");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery1 = $this->db->get_compiled_select();

		$this->db->select("hora");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery2 = $this->db->get_compiled_select();		

		$this->db->select("observaciones");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery3 = $this->db->get_compiled_select();	

		$this->db->select("operador");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery4 = $this->db->get_compiled_select();	

		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("fecha_registro >= CURRENT_DATE", NULL, FALSE);
		$subQuery = $this->db_solicitud->get_compiled_select();
		$this->db_solicitud->select("solicitud.id as id_solicitud2, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador, ($subQuery1) AS fecha_ultimo_track, ($subQuery2) AS hora_ultimo_track, ($subQuery3) AS ultimo_track, ($subQuery4) AS operador_track");
		// $this->db_solicitud->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitud->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitud->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitud->where('op.idoperador = operador_asignado');
		$this->db_solicitud->where('lab.id_situacion = id_situacion_laboral');
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'PRIMARIA');

		if (isset($estado) && !is_null($estado)){$this->db_solicitud->where('solicitud.estado IN ("APROBADO","TRANSFIRIENDO","PAGADO")');}
		if (isset($tipo)){
			if ($tipo == 'dependiente') {
				$this->db_solicitud->where("id_situacion_laboral in (1, 4, 7)");
			}else{
				$this->db_solicitud->where('id_situacion_laboral = 3');
			}
		}

		$this->db_solicitud->where('solicitud.operador_asignado',$id_operador);
		$this->db_solicitud->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitud->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitud->get();
		// var_dump($this->db_solicitud->last_query());die;
		return $query->result_array();
	}
	public function primarias_hoy_total($equipo, $tipo = null, $estado = null){

		$this->db->select("fecha");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery1 = $this->db->get_compiled_select();

		$this->db->select("hora");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery2 = $this->db->get_compiled_select();		

		$this->db->select("observaciones");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery3 = $this->db->get_compiled_select();	

		$this->db->select("operador");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery4 = $this->db->get_compiled_select();	

		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("fecha_registro >= CURRENT_DATE", NULL, FALSE);
		$subQuery = $this->db_solicitud->get_compiled_select();
		$this->db_solicitud->select("solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,nombre_apellido AS operador, ($subQuery1) AS fecha_ultimo_track, ($subQuery2) AS hora_ultimo_track, ($subQuery3) AS ultimo_track, ($subQuery4) AS operador_track");
		// $this->db_solicitud->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitud->from('solicitud');
		$this->db_solicitud->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		if (isset($equipo["equipo"]) && $equipo["equipo"] != 'GENERAL' && $equipo["equipo"] != ''){	$this->db_solicitud->where('operadores.equipo', $equipo["equipo"]);}
		$this->db_solicitud->where('operadores.estado = 1');
		// $this->db_solicitud->where("operadores.tipo_operador = 1");
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'PRIMARIA');

		if (isset($estado) && !is_null($estado)){$this->db_solicitud->where('solicitud.estado IN ("APROBADO","TRANSFIRIENDO","PAGADO")');}
		if (isset($tipo)){
			if ($tipo == 'dependiente') {
				$this->db_solicitud->where("id_situacion_laboral in (1, 4, 7)");
			}else{
				$this->db_solicitud->where('id_situacion_laboral = 3');
			}
		}

		$this->db_solicitud->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado');
		$this->db_solicitud->join('parametria.situacion_laboral lab', 'lab.id_situacion = solicitud.id_situacion_laboral', 'LEFT');
		$this->db_solicitud->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitud->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitud->get();
		// var_dump($this->db_solicitud->last_query());die;

		return $query->result_array();
	}

	public function primarias_ayer($id_operador,$fecha_ayer, $tipo = null, $estado = null){
		$this->db->select("fecha");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery1 = $this->db->get_compiled_select();

		$this->db->select("hora");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery2 = $this->db->get_compiled_select();		

		$this->db->select("observaciones");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery3 = $this->db->get_compiled_select();	

		$this->db->select("operador");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery4 = $this->db->get_compiled_select();	

		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("(SUBSTRING_INDEX(fecha_registro,' ', 1) ='$fecha_ayer')");
		$subQuery = $this->db_solicitud->get_compiled_select();

		$this->db_solicitud->select("solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador, ($subQuery1) AS fecha_ultimo_track, ($subQuery2) AS hora_ultimo_track, ($subQuery3) AS ultimo_track, ($subQuery4) AS operador_track");
		$this->db_solicitud->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitud->where('op.idoperador = operador_asignado');
		$this->db_solicitud->where('lab.id_situacion = id_situacion_laboral');
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'PRIMARIA');

		if (isset($estado) && !is_null($estado)){$this->db_solicitud->where('solicitud.estado IN ("APROBADO","TRANSFIRIENDO","PAGADO")');}
		if (isset($tipo)){
			if ($tipo == 'dependiente') {
				$this->db_solicitud->where("id_situacion_laboral in (1, 4, 7)");
			}else{
				$this->db_solicitud->where('id_situacion_laboral = 3');
			}
		}

		$this->db_solicitud->where('solicitud.operador_asignado',$id_operador);
		$query = $this->db_solicitud->get();
		// var_dump($this->db->last_query());die;
		// var_dump($subQuery);die;

		return $query->result_array($fecha_ayer);
	}

	public function primarias_ayer_totales($fecha_ayer,$equipo, $tipo = null, $estado = null){

		$this->db->select("fecha");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery1 = $this->db->get_compiled_select();

		$this->db->select("hora");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery2 = $this->db->get_compiled_select();		

		$this->db->select("observaciones");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery3 = $this->db->get_compiled_select();	

		$this->db->select("operador");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery4 = $this->db->get_compiled_select();	


		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("(SUBSTRING_INDEX(fecha_registro,' ', 1) ='$fecha_ayer')");
		$subQuery = $this->db_solicitud->get_compiled_select();

		$this->db_solicitud->select("solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,nombre_apellido AS operador, ($subQuery1) AS fecha_ultimo_track, ($subQuery2) AS hora_ultimo_track, ($subQuery3) AS ultimo_track, ($subQuery4) AS operador_track");
		$this->db_solicitud->from('solicitud');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		if (isset($equipo["equipo"]) && $equipo["equipo"] != 'GENERAL' && $equipo["equipo"] != ''){	$this->db_solicitud->where('operadores.equipo', $equipo["equipo"]);}
		$this->db_solicitud->where('operadores.estado = 1');
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'PRIMARIA');

		if (isset($estado) && !is_null($estado)){$this->db_solicitud->where('solicitud.estado IN ("APROBADO","TRANSFIRIENDO","PAGADO")');}
		if (isset($tipo)){
			if ($tipo == 'dependiente') {
				$this->db_solicitud->where("id_situacion_laboral in (1, 4, 7)");
			}else{
				$this->db_solicitud->where('id_situacion_laboral = 3');
			}
		}
		$this->db_solicitud->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado');
		$this->db_solicitud->join('parametria.situacion_laboral lab', 'lab.id_situacion = solicitud.id_situacion_laboral', 'LEFT');
		$query = $this->db_solicitud->get();
		// var_dump($this->db_solicitud->last_query());die;
		return $query->result_array();
	}

	public function primarias_mes($id_operador, $tipo = null, $estado = null){

		$this->db->select("fecha");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery1 = $this->db->get_compiled_select();

		$this->db->select("hora");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery2 = $this->db->get_compiled_select();		

		$this->db->select("observaciones");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery3 = $this->db->get_compiled_select();	

		$this->db->select("operador");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery4 = $this->db->get_compiled_select();	

		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("MONTH(fecha_registro) = MONTH(CURRENT_DATE)");
		$this->db_solicitud->where("YEAR(fecha_registro) = YEAR(CURRENT_DATE)");
		$subQuery = $this->db_solicitud->get_compiled_select();

		$this->db_solicitud->select("solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador, ($subQuery1) AS fecha_ultimo_track, ($subQuery2) AS hora_ultimo_track, ($subQuery3) AS ultimo_track, ($subQuery4) AS operador_track");
		$this->db_solicitud->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitud->where('op.idoperador = operador_asignado');
		$this->db_solicitud->where('lab.id_situacion = id_situacion_laboral');
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'PRIMARIA');

		if (isset($estado) && !is_null($estado)){$this->db_solicitud->where('solicitud.estado IN ("APROBADO","TRANSFIRIENDO","PAGADO")');}
		if (isset($tipo)){
			if ($tipo == 'dependiente') {
				$this->db_solicitud->where("id_situacion_laboral in (1, 4, 7)");
			}else{
				$this->db_solicitud->where('id_situacion_laboral = 3');
			}
		}

		$this->db_solicitud->where('solicitud.operador_asignado',$id_operador);
		$query = $this->db_solicitud->get();
		// var_dump($this->db_solicitud->last_query());die;
		return $query->result_array();
	}

	public function primarias_mes_total($equipo, $tipo = null, $estado = null){
		$this->db->select("fecha");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQueryA = $this->db->get_compiled_select();

		$this->db->select("hora");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQueryB = $this->db->get_compiled_select();		

		$this->db->select("observaciones");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQueryC = $this->db->get_compiled_select();	

		$this->db->select("operador");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = solicitud.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQueryD = $this->db->get_compiled_select();	

		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("MONTH(fecha_registro) = MONTH(CURRENT_DATE)");
		$this->db_solicitud->where("YEAR(fecha_registro) = YEAR(CURRENT_DATE)");
		$subQuery = $this->db_solicitud->get_compiled_select();
		
		$this->db->select('fecha_registro');
		$this->db->from('gestion.relacion_operador_solicitud');
		$this->db->where('id_solicitud = solicitud.id');
		$this->db->order_by('id DESC');
		$this->db->limit('1');
		$subQuery2 = $this->db->get_compiled_select();
		
		$this->db_solicitud->select("solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,nombre_apellido AS operador, ($subQuery2) AS fecha_Asignacion, ($subQueryA) AS fecha_ultimo_track, ($subQueryB) AS hora_ultimo_track, ($subQueryC) AS ultimo_track, ($subQueryD) AS operador_track");
		$this->db_solicitud->from('solicitud');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		if (isset($equipo["equipo"]) && $equipo["equipo"] != 'GENERAL' && $equipo["equipo"] != ''){	$this->db_solicitud->where('operadores.equipo', $equipo["equipo"]);}
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'PRIMARIA');
		$this->db_solicitud->where('operadores.estado = 1');
		if (isset($estado) && !is_null($estado)){$this->db_solicitud->where('solicitud.estado IN ("APROBADO","TRANSFIRIENDO","PAGADO")');}
		if (isset($tipo)){
			if ($tipo == 'dependiente') {
				$this->db_solicitud->where("id_situacion_laboral in (1, 4, 7)");
			}else{
				$this->db_solicitud->where('id_situacion_laboral = 3');
			}
		}
		$this->db_solicitud->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado');
		$this->db_solicitud->join('parametria.situacion_laboral lab', 'lab.id_situacion = solicitud.id_situacion_laboral', 'LEFT');
		$query = $this->db_solicitud->get();
		// var_dump($this->db_solicitud->last_query()); die();
		// var_dump($query); die();
		return $query->result_array();
	}

	public function retanqueo_hoy($id_operador){
		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("fecha_registro >= CURRENT_DATE", NULL, FALSE);
		$subQuery = $this->db_solicitud->get_compiled_select();
		$this->db_solicitud->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitud->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitud->where('op.idoperador = operador_asignado');
		$this->db_solicitud->where('lab.id_situacion = id_situacion_laboral');
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'RETANQUEO');
		$this->db_solicitud->where('solicitud.operador_asignado',$id_operador);
		$query = $this->db_solicitud->get();
		return $query->result_array();
	}

	public function retanqueo_hoy_total($equipo){
		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("fecha_registro >= CURRENT_DATE", NULL, FALSE);
		$subQuery = $this->db_solicitud->get_compiled_select();
		$this->db_solicitud->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,nombre_apellido AS operador');
		$this->db_solicitud->from('solicitud');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		if (isset($equipo["equipo"]) && $equipo["equipo"] != 'GENERAL' && $equipo["equipo"] != ''){	$this->db_solicitud->where('operadores.equipo', $equipo["equipo"]);}
		$this->db_solicitud->where('operadores.estado = 1');
		$this->db_solicitud->where("operadores.tipo_operador = 1");
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'RETANQUEO');
		$this->db_solicitud->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado');
		$this->db_solicitud->join('parametria.situacion_laboral lab', 'lab.id_situacion = solicitud.id_situacion_laboral', 'LEFT');
		$query = $this->db_solicitud->get();
		return $query->result_array();
	}

	public function retanqueo_ayer($id_operador){
		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("(SUBSTRING_INDEX(fecha_registro,' ', 1) ='$fecha_ayer')");
		$subQuery = $this->db_solicitud->get_compiled_select();
		$this->db_solicitud->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitud->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitud->where('op.idoperador = operador_asignado');
		$this->db_solicitud->where('lab.id_situacion = id_situacion_laboral');
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'RETANQUEO');
		$this->db_solicitud->where('solicitud.operador_asignado',$id_operador);
		$query = $this->db_solicitud->get();
		return $query->result_array();
	}

	public function retanqueo_ayer_total($fecha_ayer,$equipo){
		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("(SUBSTRING_INDEX(fecha_registro,' ', 1) ='$fecha_ayer')");
		$subQuery = $this->db_solicitud->get_compiled_select();
		$this->db_solicitud->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,nombre_apellido AS operador');
		$this->db_solicitud->from('solicitud');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		if (isset($equipo["equipo"]) && $equipo["equipo"] != 'GENERAL' && $equipo["equipo"] != ''){	$this->db_solicitud->where('operadores.equipo', $equipo["equipo"]);}
		$this->db_solicitud->where('operadores.estado = 1');
		$this->db_solicitud->where("operadores.tipo_operador = 1");
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'RETANQUEO');
		$this->db_solicitud->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado');
		$this->db_solicitud->join('parametria.situacion_laboral lab', 'lab.id_situacion = solicitud.id_situacion_laboral', 'LEFT');
		$query = $this->db_solicitud->get();
		return $query->result_array();
	}

	public function retanqueo_mes($id_operador){
		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("MONTH(fecha_registro) = MONTH(CURRENT_DATE)");
		$this->db_solicitud->where("YEAR(fecha_registro) = YEAR(CURRENT_DATE)");
		$subQuery = $this->db_solicitud->get_compiled_select();
		$this->db_solicitud->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitud->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitud->where('op.idoperador = operador_asignado');
		$this->db_solicitud->where('lab.id_situacion = id_situacion_laboral');
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'RETANQUEO');
		$this->db_solicitud->where('solicitud.operador_asignado',$id_operador);
		$query = $this->db_solicitud->get();
		return $query->result_array();
	}

	public function retanqueo_mes_total($equipo){
		$this->db_solicitud->select('id_solicitud');
		$this->db_solicitud->from('gestion.relacion_operador_solicitud');
		$this->db_solicitud->where("MONTH(fecha_registro) = MONTH(CURRENT_DATE)");
		$this->db_solicitud->where("YEAR(fecha_registro) = YEAR(CURRENT_DATE)");
		$subQuery = $this->db_solicitud->get_compiled_select();
		$this->db_solicitud->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,nombre_apellido AS operador');
		$this->db_solicitud->from('solicitud');
		$this->db_solicitud->where("id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitud->where("operadores.tipo_operador = 1");
		if (isset($equipo["equipo"]) && $equipo["equipo"] != 'GENERAL' && $equipo["equipo"] != ''){	$this->db_solicitud->where('operadores.equipo', $equipo["equipo"]);}
		$this->db_solicitud->where('solicitud.tipo_solicitud', 'RETANQUEO');
		$this->db_solicitud->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado');
		$this->db_solicitud->join('parametria.situacion_laboral lab', 'lab.id_situacion = solicitud.id_situacion_laboral', 'LEFT');
		$query = $this->db_solicitud->get();
		// var_dump($this->db_solicitud->last_query()); die();
		// var_dump($query); die();
		return $query->result_array();
	}
	public function mora($periodo_mas_mora,$periodo_menos_mora,$situacion) {
		$this->db_maestro->select('count(solicitud.id) cantidad, solicitud.operador_asignado AS operador_asignado, solicitud.operador_asignado AS consultor, operadores.nombre_apellido');	
		$this->db_maestro->from('`credito_detalle`,creditos, solicitudes.solicitud,gestion.operadores');		
		$this->db_maestro->where('credito_detalle.id_credito = creditos.id AND credito_detalle.fecha_vencimiento BETWEEN "'.$periodo_menos_mora.'" AND "'.$periodo_mas_mora.'" AND solicitud.id_credito = creditos.id AND solicitud.tipo_solicitud="PRIMARIA" AND operadores.idoperador = solicitud.operador_Asignado AND solicitud.id_situacion_laboral  '.$situacion.'');	
		$this->db_maestro->group_by('solicitud.operador_Asignado');
		$query = $this->db_maestro->get();	
		return $query->result_array();
	}

	public function mora_total($periodo_mas_mora,$periodo_menos_mora,$cred_estado) {
		$this->db_maestro->select('count(solicitud.id) cantidad, solicitud.operador_asignado AS operador_asignado, solicitud.operador_asignado AS consultor');	
		$this->db_maestro->from('`credito_detalle`,clientes,creditos, solicitudes.solicitud,gestion.operadores');
		if($cred_estado ===''){
			$this->db_maestro->where('credito_detalle.id_credito = creditos.id AND credito_detalle.fecha_vencimiento BETWEEN "'.$periodo_menos_mora.'" AND "'.$periodo_mas_mora.'" AND solicitud.id_credito = creditos.id AND clientes.id = creditos.id_cliente AND solicitud.tipo_solicitud="PRIMARIA" AND operadores.idoperador = solicitud.operador_Asignado');	
		}else{
			$this->db_maestro->where('credito_detalle.id_credito = creditos.id AND credito_detalle.fecha_vencimiento BETWEEN  "'.$periodo_menos_mora.'" AND "'.$periodo_mas_mora.'" AND solicitud.id_credito = creditos.id AND clientes.id = creditos.id_cliente AND solicitud.tipo_solicitud="PRIMARIA" AND operadores.idoperador = solicitud.operador_Asignado AND creditos.estado = "mora"');	
		}	
		$this->db_maestro->group_by('solicitud.operador_Asignado');
		$query = $this->db_maestro->get();	
		return $query->result_array();
	}

	public function mora_mes($operador,$periodo_mas_mora,$periodo_menos_mora) {

		$this->db->select("fecha");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = Sol.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery1 = $this->db->get_compiled_select();

		$this->db->select("hora");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = Sol.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery2 = $this->db->get_compiled_select();		

		$this->db->select("observaciones");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = Sol.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery3 = $this->db->get_compiled_select();	

		$this->db->select("operador");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = Sol.id");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$subQuery4 = $this->db->get_compiled_select();	

		// $this->db_maestro->select('Sol.id, Sol.paso, Sol.fecha_alta, Sol.documento, Sol.nombres, Sol.apellidos, Lab.nombre_situacion, Sol.tipo_solicitud, Sol.respuesta_analisis, Sol.estado, Sol.operador_asignado AS ID, Oper.nombre_apellido AS operador ');
		$this->db_maestro->select("Sol.id AS id_solicitud, Cl.documento AS documento, Cl.nombres AS NOMBRE, Cl.apellidos AS APELLIDO, Lab.nombre_situacion AS LABORAL, CrDet.monto_cobrar AS monto_cobrar, CrDet.fecha_vencimiento AS fecha_vencimiento, CrDet.estado AS estado, CrDet.dias_atraso, Oper.nombre_apellido AS operador, 
		($subQuery1) AS fecha_ultimo_track, ($subQuery2) AS hora_ultimo_track, ($subQuery3) AS ultimo_track, ($subQuery4) AS operador_track");
		$this->db_maestro->from('maestro.credito_detalle AS CrDet');		
		$this->db_maestro->join('maestro.creditos AS Cr', 'CrDet.id_credito = Cr.id');
		$this->db_maestro->join('solicitudes.solicitud AS Sol', 'Cr.id = Sol.id_credito');
		$this->db_maestro->join('gestion.operadores AS Oper', 'Sol.operador_asignado = Oper.idoperador');
		$this->db_maestro->join('parametria.situacion_laboral AS Lab', 'Lab.id_situacion = Sol.id_situacion_laboral');
		$this->db_maestro->join('maestro.clientes AS Cl', 'Cr.id_cliente = Cl.id ');
		$this->db_maestro->where('CrDet.fecha_vencimiento BETWEEN  "'.$periodo_menos_mora.'" AND "'.$periodo_mas_mora.'"');
		$this->db_maestro->where('Sol.tipo_solicitud = "PRIMARIA" ');
		$this->db_maestro->where('Cr.estado = "mora" ');
		$this->db_maestro->where("operador_asignado = $operador");
		$query = $this->db_maestro->get();	
		log_message('debug',$this->db_maestro->last_query());
		return $query->result_array();

	}

	public function mora_solicitudes_asignadas($operador, $param) {

		$this->db_maestro->select('solicitud.id AS id_solicitud,operadores.idoperador AS operador_asignado, count(credito_detalle.estado) AS estado, 
		solicitud.operador_asignado AS consultor, count(solicitud.id_situacion_laboral) AS tipo_laboral');	
		$this->db_maestro->from('credito_detalle,clientes,creditos, solicitudes.solicitud,gestion.operadores');		
		$this->db_maestro->where("credito_detalle.id_credito = creditos.id AND clientes.id = creditos.id_cliente AND solicitud.id_credito = creditos.id AND operadores.idoperador = solicitud.operador_Asignado AND credito_detalle.fecha_vencimiento='". $param['fecha']."'");	
		if(isset($param["situacion"]) && $param["situacion"] == "independiente") {	$this->db_maestro->where('solicitud.id_situacion_laboral = 3');  }
		if(isset($param["situacion"]) && $param["situacion"] == "dependiente") {	$this->db_maestro->where('solicitud.id_situacion_laboral in (1, 4, 7)');  }
		$this->db_maestro->where('solicitud.tipo_solicitud = "'.$param["tipo_solicitud"].'"');
		if(isset($param['estado'])){ $this->db_maestro->where('credito_detalle.estado="'.$param['estado'].'"');}
		$this->db_maestro->group_by('operadores.idoperador');
		$query = $this->db_maestro->get();	
		return $query->result_array();
		
	}
	public function get_objetivo_mora($param){
		$this->db_parametria->select('*');
		$this->db_parametria->from('objetivos_tablero');
		$this->db_parametria->where('id', $param['id']);
		$query = $this->db_parametria->get();
		return $query->result();
	}
	public function update_mora($param, $data){
		$this->db_parametria->where('id', $param['id']);
		$update  = $this->db_parametria->update('parametria.objetivos_tablero', $data);
		if($this->db_parametria->affected_rows() > 0){
            return 1;
        } else {
            return -1;
        }
	}
	public function get_asignaciones_tablero($params =[]){

		$this->db_maestro->select('count(solicitud.id) AS cantidad, solicitud.operador_asignado AS consultor,operadores.nombre_apellido'); 
		$this->db_maestro->from('credito_detalle,creditos, solicitudes.solicitud,gestion.operadores');	
		
		
		$this->db_maestro->where('credito_detalle.id_credito = creditos.id');		
		$this->db_maestro->where("solicitud.id_credito = creditos.id");	
		$this->db_maestro->where("operadores.idoperador = solicitud.operador_Asignado");	
		$this->db_maestro->where("credito_detalle.fecha_vencimiento BETWEEN '".$params['fecha_inicio']."' AND '".$params['fecha_fin']."'");	
	
	
		if(isset($params['operador'])) 		 		{$this->db_maestro->where("solicitud.operador_asignado",$params['operador']);}		
		if(isset($params['estado']))			 	{$this->db_maestro->where('credito_detalle.estado',$params['estado']);}
		if(isset($params['tipo_solicitud'])) 		{$this->db_maestro->where("solicitud.tipo_solicitud",$params['tipo_solicitud']);}		
		if(isset($params["situacion"]) && $params["situacion"] == "independiente") 		{	$this->db_maestro->where('solicitud.id_situacion_laboral = 3');  }
		if(isset($params["situacion"]) && $params["situacion"] == "dependiente") 		{	$this->db_maestro->where('solicitud.id_situacion_laboral in (1, 4, 7)');  }
	
		$this->db_maestro->group_by('operadores.idoperador');
		$query = $this->db_maestro->get();	
		//var_dump($this->db_maestro->last_query()); die();
		return $query->result();
	}

}