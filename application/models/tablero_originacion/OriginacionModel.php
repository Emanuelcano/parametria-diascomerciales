<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OriginacionModel extends CI_Model
{
	public function __Construct()
	{
		parent::__Construct();
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_parametria = $this->load->database('parametria', TRUE);
        $this->db_gestion = $this->load->database('gestion', TRUE);
	}

    public function operadores_asignados($datos)
    {
        $this->db_gestion->select('op.idoperador, op.nombre_apellido, sum(ca.asignados) asignados, sum(ca.aprobados) aprobados');
		$this->db_gestion->from('control_asignaciones ca');
		$this->db_gestion->join('operadores AS op', 'ca.id_operador = op.idoperador', 'LEFT');
		$this->db_gestion->where('op.estado = 1');
		$this->db_gestion->where("(op.tipo_operador IN (1, 7) OR op.idoperador = 108)");
		if (isset($datos["equipo"]) && $datos["equipo"] != 'GENERAL' && $datos["equipo"] != ''){$this->db_gestion->where('op.equipo', $datos["equipo"]);}
		if (isset($datos["tipo"])){  $this->db_gestion->where( 'ca.fecha_control BETWEEN '.$datos['fechas'].'');}
		$this->db_gestion->group_by('ca.id_operador');
		$query = $this->db_gestion->get();
		// var_dump($this->db_gestion->last_query());die;
		return $query->result_array();
    }

    public function get_objetivo_tablero($param)
    {
        $this->db_parametria->select('*');
		$this->db_parametria->from('objetivos_tablero');
		$this->db_parametria->where('id', $param['id']);
		$query = $this->db_parametria->get();
		return $query->result();
    }

    public function get_solicitudes_asignadas($operador, $param)
    {
        $this->db_solicitudes->select('COUNT(id) cantidad,operador_asignado');	
		$this->db_solicitudes->from('solicitud');	
		$this->db_solicitudes->join('gestion.operadores op', 'op.idoperador = operador_asignado');	
        $this->db_solicitudes->where('id in (SELECT id_solicitud FROM gestion.relacion_operador_solicitud WHERE fecha_registro BETWEEN '.$param["dia"].')');	

		if(isset($param["situacion"]) && $param["situacion"] == "independiente") {	$this->db_solicitudes->where('id_situacion_laboral = 3');  }
		if(isset($param["situacion"]) && $param["situacion"] == "dependiente") {	$this->db_solicitudes->where('id_situacion_laboral in (1, 4, 7)');  }		
		$this->db_solicitudes->where('tipo_solicitud = "'.$param["tipo_solicitud"].'"');
		if(isset($param['estado'])){ $this->db_solicitudes->where('solicitud.estado in (' . $param['estado']. ')');}
		$this->db_solicitudes->where('solicitud.operador_asignado > 0');
		$this->db_solicitudes->where('op.estado = 1');
		$this->db_solicitudes->where('tipo_operador IN (1, 7, 9)');
		$this->db_solicitudes->group_by('operador_asignado');
		$query = $this->db_solicitudes->get();	
		// var_dump($this->db_solicitudes->last_query());
		return $query->result_array();
    }	
	
	public function originacion_hoy($id_operador, $fecha){
		$this->db_solicitudes->select('id_solicitud');
		$this->db_solicitudes->from('gestion.relacion_operador_solicitud');
		$this->db_solicitudes->where("fecha_registro = $fecha", NULL, FALSE);
		$subQuery = $this->db_solicitudes->get_compiled_select();
		$this->db_solicitudes->select('solicitud.id as id_solicitud2, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		// $this->db_solicitudes->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitudes->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitudes->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitudes->where('op.idoperador = operador_asignado');
		$this->db_solicitudes->where('lab.id_situacion = id_situacion_laboral');
		$this->db_solicitudes->where('solicitud.tipo_solicitud', 'PRIMARIA');
		$this->db_solicitudes->where('tipo_operador IN (1,7,9)');
		$this->db_solicitudes->where('solicitud.operador_asignado',$id_operador);
		$this->db_solicitudes->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitudes->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitudes->get();
		// var_dump($this->db_solicitudes->last_query());die;
		return $query->result_array();
	}

	public function originacion_hoy_total($equipo, $fecha){
		$this->db_solicitudes->select('id_solicitud');
		$this->db_solicitudes->from('gestion.relacion_operador_solicitud');
		$this->db_solicitudes->where("fecha_registro BETWEEN '$fecha 00:00:00.000000' AND '$fecha 23:59:59.000000'", NULL, FALSE);
		$subQuery = $this->db_solicitudes->get_compiled_select();
		$this->db_solicitudes->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,nombre_apellido AS operador');
		// $this->db_solicitudes->select('solicitud.id, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitudes->from('solicitud');
		$this->db_solicitudes->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		if (isset($equipo["equipo"]) && $equipo["equipo"] != 'GENERAL' && $equipo["equipo"] != ''){	$this->db_solicitudes->where('operadores.equipo', $equipo["equipo"]);}
		$this->db_solicitudes->where('operadores.estado = 1');
		$this->db_solicitudes->where("operadores.tipo_operador = 1");
		$this->db_solicitudes->where('solicitud.tipo_solicitud', 'PRIMARIA');
		$this->db_solicitudes->where('tipo_operador IN (1,7,9)');
		$this->db_solicitudes->join('gestion.operadores operadores','operadores.idoperador = solicitud.operador_asignado');
		$this->db_solicitudes->join('parametria.situacion_laboral lab', 'lab.id_situacion = solicitud.id_situacion_laboral', 'LEFT');
		$this->db_solicitudes->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitudes->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitudes->get();

		return $query->result_array();
	}

	public function originacion_hoy_aprob($id_operador, $fecha, $tipo)
	{
		$this->db_solicitudes->select('id_solicitud');
		$this->db_solicitudes->from('gestion.relacion_operador_solicitud');
		$this->db_solicitudes->where("fecha_registro BETWEEN '$fecha 00:00:00.000000' AND '$fecha 23:59:59.000000'", NULL, FALSE);
		$subQuery = $this->db_solicitudes->get_compiled_select();
		$this->db_solicitudes->select('solicitud.id as id_solicitud2, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitudes->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitudes->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitudes->where('op.idoperador = operador_asignado');
		$this->db_solicitudes->where('lab.id_situacion = id_situacion_laboral');
		if ($tipo == 'Dependiente') {
			$this->db_solicitudes->where('solicitud.id_situacion_laboral IN (1, 4, 7)');
		}else{
			$this->db_solicitudes->where('solicitud.id_situacion_laboral = 3');
		}
		$this->db_solicitudes->where('solicitud.tipo_solicitud', 'PRIMARIA');
		$this->db_solicitudes->where('solicitud.estado IN ("APROBADO","TRANSFIRIENDO","PAGADO")');
		$this->db_solicitudes->where('solicitud.operador_asignado',$id_operador);
		$this->db_solicitudes->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitudes->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitudes->get();
		return $query->result_array();
	}

	public function originacion_hoy_asig($id_operador, $fecha, $tipo)
	{
		$this->db_solicitudes->select('id_solicitud');
		$this->db_solicitudes->from('gestion.relacion_operador_solicitud');
		$this->db_solicitudes->where("fecha_registro BETWEEN '$fecha 00:00:00.000000' AND '$fecha 23:59:59.000000'", NULL, FALSE);
		$subQuery = $this->db_solicitudes->get_compiled_select();

		$this->db_solicitudes->select('solicitud.id as id_solicitud2, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitudes->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitudes->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitudes->where('op.idoperador = operador_asignado');
		$this->db_solicitudes->where('lab.id_situacion = id_situacion_laboral');
		if ($tipo == 'Dependiente') {
			$this->db_solicitudes->where('solicitud.id_situacion_laboral IN (1, 4, 7)');
		}else{
			$this->db_solicitudes->where('solicitud.id_situacion_laboral = 3');
		}
		$this->db_solicitudes->where('solicitud.tipo_solicitud', 'PRIMARIA');
		$this->db_solicitudes->where('solicitud.operador_asignado',$id_operador);
		$this->db_solicitudes->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitudes->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitudes->get();
		return $query->result_array();
	}

	public function originacion_aprob_total($equipo, $desde, $hasta, $tipo){
		$this->db_solicitudes->select('id_solicitud');
		$this->db_solicitudes->from('gestion.relacion_operador_solicitud');
		$this->db_solicitudes->where('fecha_registro BETWEEN "'.$desde.' 00:00:00" AND "'.$hasta.' 23:59:59"', NULL, FALSE);
		$subQuery = $this->db_solicitudes->get_compiled_select();

		$this->db_solicitudes->select('solicitud.id as id_solicitud2, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitudes->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitudes->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitudes->where('op.idoperador = operador_asignado');
		$this->db_solicitudes->where('lab.id_situacion = id_situacion_laboral');
		if ($tipo == 'Dependiente') {
			$this->db_solicitudes->where('solicitud.id_situacion_laboral IN (1, 4, 7)');
		}else{
			$this->db_solicitudes->where('solicitud.id_situacion_laboral = 3');
		}
		$this->db_solicitudes->where('solicitud.estado IN ("APROBADO","TRANSFIRIENDO","PAGADO")');
		$this->db_solicitudes->where('solicitud.tipo_solicitud', 'PRIMARIA');
		$this->db_solicitudes->where('op.estado = 1');
		$this->db_solicitudes->where('tipo_operador IN (1,7,9)');
		$this->db_solicitudes->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitudes->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitudes->get();
		// var_dump($this->db_solicitudes->last_query());die;
		return $query->result_array();
	}

	public function originacion_asig_total($equipo, $desde, $hasta, $tipo){
		$this->db_solicitudes->select('id_solicitud');
		$this->db_solicitudes->from('gestion.relacion_operador_solicitud');
		$this->db_solicitudes->where('fecha_registro BETWEEN "'.$desde.' 00:00:00" AND "'.$hasta.' 23:59:59"', NULL, FALSE);
		$subQuery = $this->db_solicitudes->get_compiled_select();

		$this->db_solicitudes->select('solicitud.id as id_solicitud2, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitudes->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitudes->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitudes->where('op.idoperador = operador_asignado');
		$this->db_solicitudes->where('lab.id_situacion = id_situacion_laboral');
		if ($tipo == 'Dependiente') {
			$this->db_solicitudes->where('solicitud.id_situacion_laboral IN (1, 4, 7)');
		}else{
			$this->db_solicitudes->where('solicitud.id_situacion_laboral = 3');
		}
		$this->db_solicitudes->where('solicitud.tipo_solicitud', 'PRIMARIA');
		$this->db_solicitudes->where('op.estado = 1');
		$this->db_solicitudes->where('tipo_operador IN (1,7,9)');
		$this->db_solicitudes->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitudes->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitudes->get();
		// var_dump($this->db_solicitudes->last_query());die;
		return $query->result_array();
	}

	public function originacionAprob_entre($id_operador, $desde, $hasta, $tipo)
	{
		$this->db_solicitudes->select('id_solicitud');
		$this->db_solicitudes->from('gestion.relacion_operador_solicitud');
		$this->db_solicitudes->where('fecha_registro BETWEEN "'.$desde.' 00:00:00" AND "'.$hasta.' 23:59:59"', NULL, FALSE);
		$subQuery = $this->db_solicitudes->get_compiled_select();

		$this->db_solicitudes->select('solicitud.id as id_solicitud2, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitudes->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitudes->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitudes->where('op.idoperador = operador_asignado');
		$this->db_solicitudes->where('lab.id_situacion = id_situacion_laboral');
		if ($tipo == 'Dependiente') {
			$this->db_solicitudes->where('solicitud.id_situacion_laboral IN (1, 4, 7)');
		}else{
			$this->db_solicitudes->where('solicitud.id_situacion_laboral = 3');
		}
		$this->db_solicitudes->where('solicitud.estado IN ("APROBADO","TRANSFIRIENDO","PAGADO")');
		$this->db_solicitudes->where('solicitud.tipo_solicitud', 'PRIMARIA');
		$this->db_solicitudes->where('solicitud.operador_asignado',$id_operador);
		$this->db_solicitudes->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitudes->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitudes->get();
		// var_dump($this->db_solicitudes->last_query());die;
		return $query->result_array();
	}

	public function originacionAsig_entre($id_operador, $desde, $hasta, $tipo)
	{
		$this->db_solicitudes->select('id_solicitud');
		$this->db_solicitudes->from('gestion.relacion_operador_solicitud');
		$this->db_solicitudes->where('fecha_registro BETWEEN "'.$desde.' 00:00:00" AND "'.$hasta.' 23:59:59"', NULL, FALSE);
		$subQuery = $this->db_solicitudes->get_compiled_select();

		$this->db_solicitudes->select('solicitud.id as id_solicitud2, paso, fecha_alta, solicitud.documento,nombres,apellidos,lab.nombre_situacion,tipo_solicitud, respuesta_analisis,sb.monto_maximo,IFNULL(scd.capital_solicitado, 0),solicitud.estado,operador_asignado AS ID,op.nombre_apellido AS operador');
		$this->db_solicitudes->from('solicitud, gestion.operadores op, parametria.situacion_laboral lab');
		$this->db_solicitudes->where("solicitud.id IN ($subQuery)", NULL, FALSE);
		$this->db_solicitudes->where('op.idoperador = operador_asignado');
		$this->db_solicitudes->where('lab.id_situacion = id_situacion_laboral');
		if ($tipo == 'Dependiente') {
			$this->db_solicitudes->where('solicitud.id_situacion_laboral IN (1, 4, 7)');
		}else{
			$this->db_solicitudes->where('solicitud.id_situacion_laboral = 3');
		}
		$this->db_solicitudes->where('solicitud.tipo_solicitud', 'PRIMARIA');
		$this->db_solicitudes->where('solicitud.operador_asignado',$id_operador);
		$this->db_solicitudes->join('solicitudes.solicitud_beneficios sb', 'sb.id_solicitud = solicitud.id', 'LEFT');
		$this->db_solicitudes->join('solicitudes.solicitud_condicion_desembolso scd', 'scd.id_solicitud = solicitud.id', 'LEFT');
		$query = $this->db_solicitudes->get();
		// var_dump($this->db_solicitudes->last_query());die;
		return $query->result_array();
	}
}