<?php
class Reporte_model extends CI_Model
{
	public function __construct()
	{
		$this->db = $this->load->database('gestion', TRUE);
        $this->db_telefonia = $this->load->database('telefonia', TRUE);
        $this->db_solicitudes = $this->load->database('solicitudes', TRUE);
        $this->db_maestro = $this->load->database('maestro', TRUE);
        $this->api_buros = $this->load->database('api_buros', TRUE);
		parent::__construct();

	}

	public function getReporte($fechaReporte)
	{

		$query = $this->db->query("SELECT if(sa.tipo_identificacion = 'C.C.',1,4) as tnui, sa.numero_identificacion, c.id, sa.nombres_apellidos, 0 as estado_1,
				year(c.fecha_otorgamiento)*10000+month(c.fecha_otorgamiento)*100+DAY(c.fecha_otorgamiento) as fecha,
				year(c.fecha_primer_vencimiento)*10000+month(c.fecha_primer_vencimiento)*100+DAY(c.fecha_primer_vencimiento) as fecha_fin,'00' AS titular, 0 AS forma_pago,
				'01' AS novedad, 0 as data1, year(c.fecha_otorgamiento)*10000+month(c.fecha_otorgamiento)*100+DAY(c.fecha_otorgamiento) as fecha2, 1 as estado, 20191201 as fecha_r,
				'00' as valor_1,0 as valor_2,'' as valor_3,000 as valor_4, c.monto_prestado,0 as valor_5,0 as valor_6,0 as valor_7,MIN(d.fecha_vencimiento) as fech_vence,0 as valor_8,
				'BOGOTA' as ciudad_1, 11001000 as code_ciudad,'BOGOTA' as ciudad_2,'' as valor_9,'' as valor_10,s.telefono
			FROM maestro.creditos AS c
			LEFT JOIN maestro.credito_detalle AS d ON d.id_credito = c.id
			LEFT JOIN maestro.clientes AS cl ON c.id_cliente = cl.id
			CROSS JOIN solicitudes.solicitud AS s ON c.id = s.id_credito
			CROSS JOIN solicitudes.solicitud_analisis AS sa ON s.id = sa.id_solicitud
			WHERE c.estado LIKE 'vigente' and sa.numero_identificacion <> 0  AND c.fecha_primer_vencimiento >= '$fechaReporte'
			GROUP BY c.id
			ORDER BY sa.numero_identificacion ASC
			");
		return $query->result_array();
	}

	public function getVencimiento($array)
	{
	
		$newDate_ini = date("Y-m-d", strtotime($array['sl_desde']));
		$newDate_fin = date("Y-m-d", strtotime($array['sl_hasta']));
		$estado = $array['sl_estado'];
		$tipo = $array['sl_tipo_solicitud'];

		$this->db_maestro->select('
		solicitud.id AS id_solicitud,
		clientes.documento AS documento,
		clientes.nombres AS NOMBRE,	
		clientes.apellidos AS APELLIDO,
		CONCAT("57", agenda_telefonica.numero) AS telefono,
		solicitud.tipo_solicitud,
		solicitud.`id_situacion_laboral`,
		REPLACE(REPLACE(REPLACE(FORMAT(credito_detalle.monto_cobrar,0),".", "@"),",","."),"@",",") AS monto_cobrar,	
		DATE_FORMAT(credito_detalle.fecha_vencimiento, "%d-%m-%Y") AS fecha_vencimiento,
		credito_detalle.estado AS estado,
		credito_detalle.dias_atraso,operador_asignado AS ID,
		nombre_apellido AS operador');

		$this->db_maestro->from('credito_detalle,clientes,creditos,solicitudes.solicitud,gestion.operadores,agenda_telefonica');

		$this->db_maestro->where('credito_detalle.id_credito = creditos.id');

		if ($estado == 'mora') {			
			$this->db_maestro->where('credito_detalle.estado',$estado);
		}else if($estado == 'vigente'){
			$this->db_maestro->where('credito_detalle.estado is null');			
		}else if($estado == 'pagado'){
			$this->db_maestro->where('credito_detalle.estado',$estado);
		}

		$this->db_maestro->where('credito_detalle.fecha_vencimiento BETWEEN "'. $newDate_ini .'" AND "'. $newDate_fin.'"');
		$this->db_maestro->where('clientes.id = creditos.id_cliente');
		$this->db_maestro->where('solicitud.id_credito = creditos.id');

		if ($tipo != 'TODOS') {			
			$this->db_maestro->where('solicitud.tipo_solicitud',$tipo);
		}
		$this->db_maestro->where('operadores.idoperador = solicitud.operador_Asignado');
		$this->db_maestro->where('agenda_telefonica.id_cliente = clientes.id');
		$this->db_maestro->where("agenda_telefonica.fuente",'PERSONAL');
		$this->db_maestro->group_by('clientes.id');

		$query=$this->db_maestro->get();
		//print_r($this->db_maestro->last_query());die;
		return $query->result();

	}

	public function MostrarOperadores($array)
	{
		$datoInicio =date("Y-m-d", strtotime($array['datoInicio'])).' 00:00:00.000000';
		$datoFin = date("Y-m-d", strtotime($array['datoFin'])).' 23:59:59.000000';
		$this->db->select('o.idoperador, o.nombre_apellido');
		$this->db->from('operadores as o');
		$this->db->join('relacion_operador_solicitud as r', 'o.idoperador = r.id_operador');
		$this->db->where('r.fecha_registro BETWEEN "'.$datoInicio.'" AND "'.$datoFin.'"');
		$this->db->group_by('o.idoperador');

		$operadores = $this->db->get();
		return $operadores->result();
	}

	public function DatosAsignacion($array)
	{
		$fechaInicio = date("Y-m-d", strtotime($array['date_inicio'])).' 00:00:00.000000';
		$fechaFin = date("Y-m-d", strtotime($array['date_fin'])).' 23:59:59.000000';
		$operador = $array['sl_operador'];

		$this->db_solicitudes->select("MAX(fecha_consulta)");
		$this->db_solicitudes->from("solicitud_analisis");
		$this->db_solicitudes->where("id_solicitud = idsolicitud");
		$subQuerySl = $this->db_solicitudes->get_compiled_select();

		$this->db->select("MAX(fecha_registro)");
		$this->db->from("gestion.relacion_operador_solicitud");
		$this->db->where("id_solicitud = idsolicitud");
		$this->db->where("id_operador = idoperador");
		$subQuerySl2 = $this->db->get_compiled_select();

		$this->db->select("fecha");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = idsolicitud");
		$this->db->where("id_tipo_gestion = 130");
		$this->db->order_by("id DESC");
		$this->db->limit("1");
		$subQuerySl3 = $this->db->get_compiled_select();

		$this->db->select("hora");
		$this->db->from("gestion.track_gestion");
		$this->db->where("id_solicitud = idsolicitud");
		$this->db->where("id_tipo_gestion = 130");
		$this->db->order_by("id DESC");
		$this->db->limit("1");
		$subQuerySl4 = $this->db->get_compiled_select();

		$this->db->select("id_solicitud");
		$this->db->from("gestion.relacion_operador_solicitud");
		$this->db->where("fecha_registro > '".$fechaFin."'");
		$this->db->where("estado = 'A'");
		$subQueryWhereSub = $this->db->get_compiled_select();

		$this->db->select("id_solicitud");
		$this->db->from("gestion.relacion_operador_solicitud");
		$this->db->where("fecha_registro BETWEEN '".$fechaInicio."' AND '".$fechaFin."'");
		$this->db->where("estado = 'A'");
		$this->db->where("id_solicitud NOT IN($subQueryWhereSub)");
		$subQueryWhere = $this->db->get_compiled_select();



		$this->db_solicitudes->select("solicitud.id AS idsolicitud,paso,fecha_alta,($subQuerySl) as fecha_analisis,solicitud.documento,nombres,apellidos,nombre_situacion,
		tipo_solicitud,	id_situacion_laboral,respuesta_analisis,solicitud.estado,operador_asignado AS idoperador,nombre_apellido AS operador,($subQuerySl2) AS fecha_asignado,
		($subQuerySl3) as fecha_aprobado,($subQuerySl4) as hora_aprobado");

		$this->db_solicitudes->from("solicitud");
		$this->db_solicitudes->join("gestion.operadores", "operadores.idoperador = solicitud.operador_asignado");
		$this->db_solicitudes->join("parametria.situacion_laboral", "situacion_laboral.id_situacion = solicitud.id_situacion_laboral", "left");

		$this->db_solicitudes->where("id IN ($subQueryWhere)");
		$this->db_solicitudes->where("tipo_solicitud ='PRIMARIA'");
		
		if ($operador != 0) {
			$this->db_solicitudes->where("operador_asignado =".$operador);
		}

		$rs_Asignados = $this->db_solicitudes->get();
		return $rs_Asignados->result_array();	
	}

	public function datosCasosDevueltos($array)
	{
		$fechaInicio = date("Y-m-d", strtotime($array['dato_inicio'])).' 00:00:00';
		$fechaFin = date("Y-m-d", strtotime($array['dato_fin'])).' 23:59:59';
		$operador = $array['slc_operador'];
		//-------------------INICIO subQuerySl2-----------------------

		$this->db->select('idoperador');
		$this->db->from('gestion.operadores');
		$this->db->where('tipo_operador  = 11');
		$subQuerySl2 = $this->db->get_compiled_select();

		//-------------------FIN subQuerySl2-----------------------
		//-------------------INICIO subQuerySl-----------------------

		$this->db->select('observaciones');
		$this->db->from('gestion.track_gestion');
		$this->db->where('id_solicitud = solicitud.id');
		$this->db->where('id_tipo_gestion = 8');
		if ($operador != 0) {
			$this->db->where('`id_operador` = '.$operador);
		}else {
			$this->db->where('`id_operador` IN ('.$subQuerySl2.')');
		}
		$this->db->group_by('id_solicitud');
		$this->db->order_by('track_gestion.id');
		$subQuerySl = $this->db->get_compiled_select();
		

		//-------------------FIN subQuerySl-----------------------
		//-------------------INICIO subQuery_where2-----------------------

		$this->db->select('idoperador');
		$this->db->from('gestion.operadores');
		$this->db->where('tipo_operador = 11');
		$subQuery_where2 = $this->db->get_compiled_select();

		//-------------------FIN subQuery_where2-----------------------
		//-------------------INICIO subQuery_where-----------------------

		$this->db->select('id_solicitud');
		$this->db->from('gestion.track_gestion');
		$this->db->where('fecha` BETWEEN "'.$fechaInicio.'" AND "'.$fechaFin.'"');
		if ($operador != 0) {
			$this->db->where('id_operador = '.$operador.'');
		}else {
			$this->db->where('id_operador IN ('.$subQuery_where2.')');
		}
		$this->db->where('t.id_tipo_gestion = 170');
		$subQuery_where = $this->db->get_compiled_select();

		//-------------------FIN subQuery_where-----------------------
			
			$this->db_solicitudes->select('
			solicitud.id AS id_solic,
			solicitud.documento,
			solicitud.nombres,
			solicitud.apellidos,
			l.nombre_situacion,
			o.nombre_apellido as operador_gestion,
			solicitud.estado AS estado_solicitud,
			t.operador AS operador_devuelve, 
			('.$subQuerySl.') AS comentarios');

			$this->db_solicitudes->from('solicitud');
			$this->db_solicitudes->join('parametria.situacion_laboral as l', 'l.id_situacion = solicitud.id_situacion_laboral');
			$this->db_solicitudes->join('gestion.track_gestion as t', 't.id_solicitud = solicitud.id');
			$this->db_solicitudes->join('gestion.operadores as o', 'o.idoperador = solicitud.operador_asignado');

			$this->db_solicitudes->where('solicitud.id IN ('.$subQuery_where.')');
			$this->db_solicitudes->where('t.id_tipo_gestion = 170');
			$this->db_solicitudes->group_by('solicitud.id');
			$resultados = $this->db_solicitudes->get();
			return $resultados->result_array();		
		
	}

	public function operadoresFraude()
	{
		$this->db->select('idoperador, nombre_apellido');
		$this->db->from('operadores');
		$this->db->where('tipo_operador = 11');
		$this->db->where('estado = 1');
		$this->db->group_by('idoperador');
		$operadoresFraude= $this->db->get();
		return $operadoresFraude->result_array();
	}

	public function reporte_gastos($array)
	{
		$fecha_inicio = date("Y-m-d", strtotime($array['sl_desde']));
		$fecha_fin = date("Y-m-d", strtotime($array['sl_hasta']));
		
		$this->db_maestro->select('gastos.id_gasto,beneficiarios.nro_documento,beneficiarios.denominacion,gastos.concepto,gastos.nro_factura,
		gastos.fecha_emision,gastos.fecha_vencimiento,gastos.exento,gastos.sub_total,gastos.descuento,gastos.impuesto,gastos.retefuente,
		gastos.reteica,gastos.impuesto_consumo,	gastos.total_pagar,	gastos.fecha_creacion,gastos.estado');
		$this->db_maestro->from('gastos');
		$this->db_maestro->join('beneficiarios', 'beneficiarios.id_beneficiario = gastos.id_beneficiario');
		$this->db_maestro->where('gastos.fecha_creacion BETWEEN "'.$fecha_inicio.' 00:00:00.000000" AND "'.$fecha_fin.' 23:59:59.000000"');
		$rs_gastos = $this->db_maestro->get();
		return $resultado_gastos = $rs_gastos->result_array();

	}

	public function reporte_cobros($fecha_inicio_cobro, $fecha_fin_cobro)
	{
		$this->api_buros->select("direccion");
		$this->api_buros->from("api_buros.datacredito2_reconocer_direccion");
		$this->api_buros->join("api_buros.datacredito2_reconocer_naturalnacional", "api_buros.datacredito2_reconocer_naturalnacional.identificacion = identificacion_tercero");
		$this->api_buros->where("api_buros.datacredito2_reconocer_direccion.IdConsulta = api_buros.datacredito2_reconocer_naturalnacional.id");
		$this->api_buros->order_by("datacredito2_reconocer_direccion.id DESC");
		$this->api_buros->limit(1);
		$subDireccion = $this->api_buros->get_compiled_select();
		
		$this->api_buros->select("nombreCiudad");
		$this->api_buros->from("api_buros.datacredito2_reconocer_direccion");
		$this->api_buros->join("api_buros.datacredito2_reconocer_naturalnacional", "api_buros.datacredito2_reconocer_naturalnacional.identificacion = identificacion_tercero");
		$this->api_buros->where("api_buros.datacredito2_reconocer_direccion.IdConsulta = datacredito2_reconocer_naturalnacional.id");
		$this->api_buros->order_by("datacredito2_reconocer_direccion.id DESC");
		$this->api_buros->limit(1);
		$subCiudad = $this->api_buros->get_compiled_select();
		
		$this->api_buros->select("nombreDepartamento");
		$this->api_buros->from("api_buros.datacredito2_reconocer_direccion");
		$this->api_buros->join("api_buros.datacredito2_reconocer_naturalnacional", "api_buros.datacredito2_reconocer_naturalnacional.identificacion = identificacion_tercero");
		$this->api_buros->where("api_buros.datacredito2_reconocer_direccion.IdConsulta = api_buros.datacredito2_reconocer_naturalnacional.id");
		$this->api_buros->order_by("datacredito2_reconocer_direccion.id DESC");
		$this->api_buros->limit(1);
		$subDepartamento = $this->api_buros->get_compiled_select();
		
		$this->api_buros->select("Direccion");
		$this->api_buros->from("api_buros.pecoriginacion_direcciones");
		$this->api_buros->join("api_buros.dataconsulta", "api_buros.dataconsulta.NumeroIdentificacion = identificacion_tercero");
		$this->api_buros->where("api_buros.pecoriginacion_direcciones.IdConsulta = api_buros.dataconsulta.id");
		$this->api_buros->order_by("pecoriginacion_direcciones.id DESC");
		$this->api_buros->limit(1);
		$subDireccion2 = $this->api_buros->get_compiled_select();
		
		$this->api_buros->select("Ciudad");
		$this->api_buros->from("api_buros.pecoriginacion_direcciones");
		$this->api_buros->join("api_buros.dataconsulta", "api_buros.dataconsulta.NumeroIdentificacion = identificacion_tercero");
		$this->api_buros->where("api_buros.pecoriginacion_direcciones.IdConsulta = api_buros.dataconsulta.id");
		$this->api_buros->order_by("pecoriginacion_direcciones.id DESC");
		$this->api_buros->limit(1);
		$subCiudad2 = $this->api_buros->get_compiled_select();

		$this->db_maestro->select("credito_detalle.id_credito AS idc,'2' AS tipo_de_comprobante,'' AS consecutivo, clientes.documento AS identificacion_tercero,
		'' AS sucursal,	'' AS codigo_centro,CURRENT_DATE() AS fecha_elaboracion,'' AS sigla_moneda,'' AS tasa_cambio, CONCAT(CONCAT(clientes.nombres, ' '),
			clientes.apellidos) AS nombre_cliente,agenda_mail.cuenta AS email_contacto,'' AS orden_compra,'' AS orden_entrega,'' AS fecha_orden_entrega,
		'8' AS codigo_producto,	'Servicios gravados' AS descripcion_producto,'901255144' AS identificador_vendedor,'' AS codigo_bodega,	'1' AS cantidad_producto,
		(credito_detalle.administracion + credito_detalle.tecnologia) AS valor_unitario,
		(
		  (((credito_detalle.tecnologia_mora + credito_detalle.multa_mora - credito_detalle.descuento)-((credito_detalle.tecnologia_mora + credito_detalle.multa_mora - credito_detalle.descuento)-(credito_detalle.tecnologia_mora + credito_detalle.multa_mora - credito_detalle.descuento)/1.19)))
		) AS valor_unitario2, 0 AS valor_descuento,'' AS base_AIU,'1' AS codigo_impuesto_cargo,
		'' AS codigo_impuesto_cargo_dos,'' AS codigo_impuesto_retencion,'' AS codigo_reteICA,'' AS codigo_reteIVA,'7' AS codigo_forma_pago,
		(
			credito_detalle.administracion + credito_detalle.tecnologia + credito_detalle.iva
		) AS valor_forma_pago,
		(
			(credito_detalle.tecnologia_mora + credito_detalle.multa_mora - credito_detalle.descuento)
		) AS valor_forma_pago2,
		credito_detalle.fecha_vencimiento AS fecha_vencimiento,
		credito_detalle.fecha_cobro AS fecha_cobro,
		agenda_telefonica.numero AS numero_contacto, 
		($subDireccion) AS direccion_cliente,
		($subCiudad) AS ciudad_cliente,
		($subDepartamento) AS departamento_cliente,
		($subDireccion2) AS direccion_cliente2,
		($subCiudad2) AS ciudad_cliente2");

		$this->db_maestro->from('credito_detalle');
		$this->db_maestro->join('creditos', 'creditos.id = credito_detalle.id_credito');
		$this->db_maestro->join('clientes', 'clientes.id = creditos.id_cliente');
		$this->db_maestro->join('agenda_mail', 'agenda_mail.id_cliente = clientes.id');
		$this->db_maestro->join('agenda_telefonica', 'agenda_telefonica.id_cliente = clientes.id');

		$this->db_maestro->where("credito_detalle.estado = 'pagado'");
		$this->db_maestro->where("creditos.plazo = 1");
		$this->db_maestro->where("fecha_cobro BETWEEN '".$fecha_inicio_cobro." 00:00:00' AND '".$fecha_fin_cobro." 23:59:59'");
		$this->db_maestro->where("agenda_mail.fuente = 'PERSONAL'");
		$this->db_maestro->where("agenda_telefonica.fuente = 'PERSONAL'");
		$this->db_maestro->where("clientes.id NOT IN (SELECT id_cliente FROM creditos WHERE estado IN ('vigente','mora'))");
		$subQueryA = $this->db_maestro->get_compiled_select();
		// $querytotal = $this->db_maestro->get();

		$subQueryUnion ="credito_detalle.interes + credito_detalle.seguro + credito_detalle.tecnologia_mora + credito_detalle.multa_mora";

		$this->db_maestro->select("credito_detalle.id_credito AS idc,'2' AS tipo_de_comprobante,'' AS consecutivo,	clientes.documento AS identificacion_tercero,
		'' AS sucursal,'' AS codigo_centro,CURRENT_DATE() AS fecha_elaboracion,'' AS sigla_moneda,'' AS tasa_cambio,CONCAT(CONCAT(clientes.nombres, ' '),
			clientes.apellidos) AS nombre_cliente,agenda_mail.cuenta AS email_contacto,'' AS orden_compra,'' AS orden_entrega,'' AS fecha_orden_entrega,
		'9' AS codigo_producto,'Servicios no gravados' AS descripcion_producto,'901255144' AS identificador_vendedor,'' AS codigo_bodega,
		'1' AS cantidad_producto,(credito_detalle.interes) AS valor_unitario, 0 AS valor_unitario_multa, 0 AS valor_descuento,'' AS base_AIU,'' AS codigo_impuesto_cargo,
		'' AS codigo_impuesto_cargo_dos,'' AS codigo_impuesto_retencion,'' AS codigo_reteICA,'' AS codigo_reteIVA,'7' AS codigo_forma_pago,(credito_detalle.interes) AS valor_forma_pago, 0 AS valor_forma_pago2,
		credito_detalle.fecha_vencimiento AS fecha_vencimiento,credito_detalle.fecha_cobro AS fecha_cobro,
		agenda_telefonica.numero AS numero_contacto, 
		($subDireccion) AS direccion_cliente,
		($subCiudad) AS ciudad_cliente,
		($subDepartamento) AS departamento_cliente,
		($subDireccion2) AS direccion_cliente2,
		($subCiudad2) AS ciudad_cliente2");

		$this->db_maestro->from('credito_detalle');
		$this->db_maestro->join('creditos', 'creditos.id = credito_detalle.id_credito');
		$this->db_maestro->join('clientes', 'clientes.id = creditos.id_cliente');
		$this->db_maestro->join('agenda_mail', 'agenda_mail.id_cliente = clientes.id');
		$this->db_maestro->join('agenda_telefonica', 'agenda_telefonica.id_cliente = clientes.id');

		$this->db_maestro->where("credito_detalle.estado = 'pagado'");
		$this->db_maestro->where("creditos.plazo = 1");
		$this->db_maestro->where("fecha_cobro BETWEEN '".$fecha_inicio_cobro." 00:00:00' AND '".$fecha_fin_cobro." 23:59:59'");
		$this->db_maestro->where("agenda_mail.fuente = 'PERSONAL'");
		$this->db_maestro->where("agenda_telefonica.fuente = 'PERSONAL'");
		$this->db_maestro->where("clientes.id NOT IN (SELECT id_cliente FROM creditos WHERE estado IN ('vigente','mora'))");
		$subQueryB = $this->db_maestro->get_compiled_select();
		
		$querytotal = $this->db_maestro->query("($subQueryA) UNION ($subQueryB)");
		$data = $querytotal->result_array();
        return $data;
	}
}
?>
