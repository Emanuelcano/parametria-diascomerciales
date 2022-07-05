<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TableroCobranza_model extends CI_Model
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

	public function getCobradores(){
		
		$this->db->select('*');
		$this->db->from('operadores');
		$this->db->where('estado',1);
		$this->db->where_in('tipo_operador',[5,6]);
		$query = $this->db->get();

		return $query->result_array();
	}

	public function getAcuerdosAlcanzados($periodos){
		$desde_1 = " '".$periodos['desde_1']."' ";
		$hasta_1 = " '".$periodos['hasta_1']."' ";
		$desde_2 = " '".$periodos['desde_2']."' ";
		$hasta_2 = " '".$periodos['hasta_2']."' ";
		//$query = $this->db->query("CALL sp_tablero_cobranza('".$desde_1."','".$hasta_1."','" .$desde_2."','".$hasta_2."')");

		$sql = "SELECT
		op.nombre_apellido,
		tb1.id_operador,
		acuerdo_quincena_anterior,
		acuerdos_cumpl_quincena_anterior,
		(acuerdos_cumpl_quincena_anterior / acuerdo_quincena_anterior) * 100 AS conversion_quincena_anterior,
		acuerdos_alcanzados_actual,
		acuerdos_cumplidos_actual,
		(acuerdos_cumplidos_actual / acuerdos_alcanzados_actual) * 100 as conversion_quincena_actual,
		suma_acuerdos_quincena_anterior,
		cantidad_quincena_anterior,
		suma_acuerdos_quincena_actual,
		cantidad_quincena_actual
		from
		(
		SELECT
			`id_operador`,
			COUNT(acuerdos_pago.id) AS acuerdo_quincena_anterior
		FROM
			gestion.`acuerdos_pago`
            JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
			JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
		WHERE
			id_operador IN (SELECT
					`idoperador`
				FROM
					`operadores`
				WHERE
					`estado` = 1
						AND `tipo_operador` IN (5 , 6))
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND acuerdos_pago.`estado` IN (1 , 2, 3)
            	AND cd.`dias_atraso` > 1
		GROUP BY `id_operador`
		) tb1
		left JOIN
		(
		SELECT
			`id_operador`, COUNT(acuerdos_pago.id) AS acuerdos_cumpl_quincena_anterior
		FROM
			gestion.`acuerdos_pago`
            JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
			JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
		WHERE
			id_operador IN (SELECT
					`idoperador`
				FROM
					`operadores`
				WHERE
					`estado` = 1
						AND `tipo_operador` IN (5 , 6))
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND acuerdos_pago.`estado` IN (1)
            	AND cd.`dias_atraso` > 1		GROUP BY `id_operador`
		) tb2
		ON tb1.id_operador=tb2.id_operador
		left join
		(
		SELECT
			`id_operador`, COUNT(acuerdos_pago.id) AS acuerdos_alcanzados_actual
		FROM
			gestion.`acuerdos_pago`
            JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
			JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
		WHERE
			`id_operador` IN (
				SELECT
					idoperador
				FROM
					`operadores`
				WHERE
					`estado` = '1'
					AND `tipo_operador` IN (5, 6)
			)
			AND `fecha_hora` BETWEEN $desde_2 AND $hasta_2
			AND acuerdos_pago.`estado` IN (1,2,3)
            AND cd.`dias_atraso` > 1
            GROUP BY `id_operador`
		) tb3 ON tb1.id_operador = tb3.id_operador
		left join
		(
		SELECT
			`id_operador`, COUNT(acuerdos_pago.id) AS acuerdos_cumplidos_actual
		FROM
			gestion.`acuerdos_pago`
            JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
			JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
		WHERE
			`id_operador` IN (
				SELECT
					idoperador
				FROM
					`operadores`
				WHERE
					`estado` = '1'
					AND `tipo_operador` IN (5, 6)
			)
			AND `fecha_hora` BETWEEN $desde_2 AND $hasta_2
			AND acuerdos_pago.`estado` IN (1)
            AND cd.`dias_atraso` > 1
		GROUP BY `id_operador`
		) tb4 ON tb1.id_operador = tb4.id_operador
		left join
		(
			SELECT
			id_operador, SUM(acuerdos_pago.monto) AS suma_acuerdos_quincena_anterior, count(acuerdos_pago.id) cantidad_quincena_anterior
		FROM
			gestion.`acuerdos_pago`
            JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
			JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
		WHERE
			`id_operador` IN (
				SELECT
					idoperador
				FROM
					`operadores`
				WHERE
					`estado` = '1'
					AND `tipo_operador` IN (5, 6)
			)
			AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
			AND acuerdos_pago.`estado` IN (1)
            AND cd.`dias_atraso` > 1
		GROUP BY
			id_operador
		) tb5 ON tb1.id_operador = tb5.id_operador
		left join
		(
		SELECT
			id_operador, SUM(acuerdos_pago.monto) AS suma_acuerdos_quincena_actual, count(acuerdos_pago.id) AS cantidad_quincena_actual
		FROM
			gestion.`acuerdos_pago`
            JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
			JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
		WHERE
			`id_operador` IN (
				SELECT
					idoperador
				FROM
					`operadores`
				WHERE
					`estado` = '1'
					AND `tipo_operador` IN (5, 6)
			)
			AND `fecha_hora` BETWEEN $desde_2 AND $hasta_2
			AND acuerdos_pago.`estado` IN (1)
            AND cd.`dias_atraso` > 1
		GROUP BY
			id_operador
		) tb6 ON tb1.id_operador = tb6.id_operador
		INNER JOIN gestion.operadores op
		on op.idoperador = tb1.id_operador
		order by suma_acuerdos_quincena_actual desc";
		$query = $this->db->query($sql);
		// var_dump($this->db->last_query());die;
		return $query->result_array();
	}

	public function getTramoMoraActual($periodos){
		$desde_1 = " '".$periodos['desde_1']."' ";
		$hasta_1 = " '".$periodos['hasta_1']."' ";

		// var_dump($desde_1, $hasta_1, $desde_2, $hasta_2);die;

		$sql = "SELECT
		op.nombre_apellido,
		tb1.id_operador,
		acuerdos_alcanzados_actual,
		acuerdos_cumplidos_actual,
		(
			acuerdos_cumplidos_actual / acuerdos_alcanzados_actual
		) * 100 as conversion_quincena_actual,
		suma_acuerdos_quincena_actual_0_40,
		cantidad_quincena_actual_0_40,
		suma_acuerdos_quincena_actual_41_90,
		cantidad_quincena_actual_41_90,
		suma_acuerdos_quincena_actual_91_120,
		cantidad_quincena_actual_91_120
		from
		(
			SELECT
				`id_operador`,
				COUNT(acuerdos_pago.id) AS acuerdos_alcanzados_actual
			FROM
				gestion.`acuerdos_pago`
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND acuerdos_pago.`estado` IN (1, 2, 3)
				AND cd.`dias_atraso` > 1
			GROUP BY
				`id_operador`
		) tb1
		left join (
			SELECT
				`id_operador`,
				COUNT(acuerdos_pago.id) AS acuerdos_cumplidos_actual
			FROM
				gestion.`acuerdos_pago`
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND acuerdos_pago.`estado` IN (1)
				AND cd.`dias_atraso` > 1
			GROUP BY
				`id_operador`
		) tb2 ON tb1.id_operador = tb2.id_operador
		left join (
			SELECT
				id_operador,
				SUM(monto) AS suma_acuerdos_quincena_actual_0_40,
				count(ap.id) cantidad_quincena_actual_0_40
			FROM
				gestion.`acuerdos_pago` ap
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = ap.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND ap.`estado` IN (1)
				AND ((ap.dias_atraso is not null and ap.`dias_atraso` BETWEEN 2
				AND 45) or (ap.dias_atraso is null and cd.dias_atraso BETWEEN 2
				AND 45))
			GROUP BY
				id_operador
		) tb3 ON tb1.id_operador = tb3.id_operador
		left join (
			SELECT
				id_operador,
				SUM(monto) AS suma_acuerdos_quincena_actual_41_90,
				count(ap.id) cantidad_quincena_actual_41_90
			FROM
				gestion.`acuerdos_pago` ap
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = ap.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND ap.`estado` IN (1)
				AND ((ap.dias_atraso is not null and ap.`dias_atraso` BETWEEN 46
				AND 120) or (ap.dias_atraso is null and cd.dias_atraso BETWEEN 46
				AND 120))
			GROUP BY
				id_operador
		) tb4 ON tb1.id_operador = tb4.id_operador
		left join (
			SELECT
				id_operador,
				SUM(monto) AS suma_acuerdos_quincena_actual_91_120,
				count(ap.id) cantidad_quincena_actual_91_120
			FROM
				gestion.`acuerdos_pago` ap
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = ap.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND ap.`estado` IN (1)
				AND ((ap.dias_atraso is not null and ap.`dias_atraso` > 120) or (ap.dias_atraso is null and cd.dias_atraso > 120))
			GROUP BY
				id_operador
		) tb5 ON tb1.id_operador = tb5.id_operador
		
		INNER JOIN gestion.operadores op on op.idoperador = tb1.id_operador";
		$query = $this->db->query($sql);
		return $query->result_array();

	}
	public function getTramoMoraAnterior($periodos){
		$desde_1 = " '".$periodos['desde_1']."' ";
		$hasta_1 = " '".$periodos['hasta_1']."' ";

		$sql = "SELECT
		op.nombre_apellido,
		tb1.id_operador,
		acuerdos_alcanzados_anterior,
		acuerdos_cumplidos_anterior,
		(
			acuerdos_cumplidos_anterior / acuerdos_alcanzados_anterior
		) * 100 as conversion_quincena_anterior,
		suma_acuerdos_quincena_anterior_0_40,
		cantidad_quincena_anterior_0_40,
		suma_acuerdos_quincena_anterior_41_90,
		cantidad_quincena_anterior_41_90,
		suma_acuerdos_quincena_anterior_91_120,
		cantidad_quincena_anterior_91_120
		from
		(
			SELECT
				`id_operador`,
				COUNT(acuerdos_pago.id) AS acuerdos_alcanzados_anterior
			FROM
				gestion.`acuerdos_pago`
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND acuerdos_pago.`estado` IN (1, 2, 3)
				AND cd.`dias_atraso` > 1
			GROUP BY
				`id_operador`
		) tb1
		left join (
			SELECT
				`id_operador`,
				COUNT(acuerdos_pago.id) AS acuerdos_cumplidos_anterior
			FROM
				gestion.`acuerdos_pago`
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND acuerdos_pago.`estado` IN (1)
				AND cd.`dias_atraso` > 1
			GROUP BY
				`id_operador`
		) tb2 ON tb1.id_operador = tb2.id_operador
		left join (
			SELECT
				id_operador,
				SUM(monto) AS suma_acuerdos_quincena_anterior_0_40,
				count(ap.id) cantidad_quincena_anterior_0_40
			FROM
				gestion.`acuerdos_pago` ap
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = ap.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND ap.`estado` IN (1)
				AND ((ap.dias_atraso is not null and ap.`dias_atraso` BETWEEN 2
				AND 45) or (ap.dias_atraso is null and cd.dias_atraso BETWEEN 2
				AND 45))
			GROUP BY
				id_operador
		) tb3 ON tb1.id_operador = tb3.id_operador
		left join (
			SELECT
				id_operador,
				SUM(monto) AS suma_acuerdos_quincena_anterior_41_90,
				count(ap.id) cantidad_quincena_anterior_41_90
			FROM
				gestion.`acuerdos_pago` ap
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = ap.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND ap.`estado` IN (1)
				AND ((ap.dias_atraso is not null and ap.`dias_atraso` BETWEEN 46
				AND 120) or (ap.dias_atraso is null and cd.dias_atraso BETWEEN 46
				AND 120))
			GROUP BY
				id_operador
		) tb4 ON tb1.id_operador = tb4.id_operador
		left join (
			SELECT
				id_operador,
				SUM(monto) AS suma_acuerdos_quincena_anterior_91_120,
				count(ap.id) cantidad_quincena_anterior_91_120
			FROM
				gestion.`acuerdos_pago` ap
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = ap.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` BETWEEN $desde_1 AND $hasta_1
				AND ap.`estado` IN (1)
				AND ((ap.dias_atraso is not null and ap.`dias_atraso` > 120 ) or (ap.dias_atraso is null and cd.dias_atraso > 120))
			GROUP BY
				id_operador
		) tb5 ON tb1.id_operador = tb5.id_operador
		
		INNER JOIN gestion.operadores op on op.idoperador = tb1.id_operador";
		$query = $this->db->query($sql);
		//var_dump($this->db->error());
		return $query->result_array();

	}
	public function acuerdos_gestiones($fecha,$id_operador){
		$sql = "SELECT
			op.nombre_apellido,
			tb1.id_operador,
			acuerdos_alcanzados_anterior,
			acuerdos_cumplidos_anterior,
			(acuerdos_cumplidos_anterior / acuerdos_alcanzados_anterior ) * 100 as conversion_quincena_anterior,
			suma_acuerdos_quincena_anterior_0_40,
			cantidad_quincena_anterior_0_40,
			suma_acuerdos_quincena_anterior_41_90,
			cantidad_quincena_anterior_41_90,
			suma_acuerdos_quincena_anterior_91_120,
			cantidad_quincena_anterior_91_120
		from
		(	SELECT	`id_operador`,COUNT(acuerdos_pago.id) AS acuerdos_alcanzados_anterior
			FROM
				gestion.`acuerdos_pago`
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN ( SELECT idoperador FROM `operadores` WHERE `estado` = '1' AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` $fecha
				AND acuerdos_pago.`estado` IN (1, 2, 3)
				AND cd.`dias_atraso` > 1
				AND id_operador = $id_operador
			GROUP BY `id_operador`
		) tb1
		left join (
			SELECT `id_operador`, COUNT(acuerdos_pago.id) AS acuerdos_cumplidos_anterior
			FROM
				gestion.`acuerdos_pago`
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN ( SELECT idoperador
					FROM `operadores`
					WHERE `estado` = '1' AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` $fecha
				AND acuerdos_pago.`estado` IN (1)
				AND cd.`dias_atraso` > 1
				AND id_operador = $id_operador
			GROUP BY `id_operador`
		) tb2 ON tb1.id_operador = tb2.id_operador
		left join (
			SELECT id_operador, SUM(monto) AS suma_acuerdos_quincena_anterior_0_40, count(ap.id) cantidad_quincena_anterior_0_40
			FROM gestion.`acuerdos_pago` ap
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = ap.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN ( SELECT idoperador FROM `operadores` WHERE `estado` = '1' AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` $fecha
				AND ap.`estado` IN (1)
				AND ((ap.dias_atraso is not null and ap.`dias_atraso` BETWEEN 2
				AND 45) or (ap.dias_atraso is null and cd.dias_atraso BETWEEN 2
				AND 45))
				AND id_operador = $id_operador
			GROUP BY id_operador
		) tb3 ON tb1.id_operador = tb3.id_operador
		left join (
			SELECT
				id_operador,
				SUM(monto) AS suma_acuerdos_quincena_anterior_41_90,
				count(ap.id) cantidad_quincena_anterior_41_90
			FROM
				gestion.`acuerdos_pago` ap
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = ap.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN (
					SELECT
						idoperador
					FROM
						`operadores`
					WHERE
						`estado` = '1'
						AND `tipo_operador` IN (5, 6)
				)
				AND `fecha_hora` $fecha
				AND ap.`estado` IN (1)
				AND ((ap.dias_atraso is not null and ap.`dias_atraso` BETWEEN 46
				AND 120) or (ap.dias_atraso is null and cd.dias_atraso BETWEEN 46
				AND 120))
				AND id_operador = $id_operador
			GROUP BY
				id_operador
		) tb4 ON tb1.id_operador = tb4.id_operador
		left join (
			SELECT
				id_operador,
				SUM(monto) AS suma_acuerdos_quincena_anterior_91_120,
				count(ap.id) cantidad_quincena_anterior_91_120
			FROM
				gestion.`acuerdos_pago` ap
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = ap.id
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id
			WHERE
				`id_operador` IN ( SELECT idoperador FROM `operadores` WHERE `estado` = '1' AND `tipo_operador` IN (5, 6) )
				AND `fecha_hora` $fecha
				AND ap.`estado` IN (1)
				AND ((ap.dias_atraso is not null and ap.`dias_atraso` > 120) or (ap.dias_atraso is null and cd.dias_atraso > 120 ))
				AND id_operador = $id_operador
			GROUP BY id_operador
		) tb5 ON tb1.id_operador = tb5.id_operador
		
		INNER JOIN gestion.operadores op on op.idoperador = tb1.id_operador";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function generador_excel($fecha,$id_operador){
		$sql= "SELECT 
			acuerdos_pago.id AS id_acuerdo,
			acuerdos_pago.fecha_hora as fecha_gestion,
			acuerdos_pago.fecha as fecha_acuerdo,
			acuerdos_pago.monto as monto_acuerdo,
			acuerdos_pago.estado as estado_acuerdo,
			cd.dias_atraso, acuerdos_pago.id_cliente, cl.documento, cl.nombres,
			cl.apellidos, acuerdos_pago.id_operador, op.nombre_apellido
			FROM  gestion.`acuerdos_pago` 
				JOIN gestion.acuerdos_detalle ad ON ad.id_acuerdo = acuerdos_pago.id 
				JOIN gestion.operadores op ON op.idoperador = acuerdos_pago.id_operador
				JOIN maestro.credito_detalle cd ON ad.id_credito_detalle = cd.id 
				JOIN maestro.clientes cl ON cl.id = acuerdos_pago.id_cliente
			WHERE  `id_operador` IN ( SELECT  idoperador  FROM  `operadores` WHERE  `estado` = '1'  AND `tipo_operador` IN (5, 6) ) 
			AND `fecha_hora` $fecha AND acuerdos_pago.`estado` IN (1, 2, 3)  AND cd.`dias_atraso` > 1 $id_operador";
		$query = $this->db->query($sql);
		// var_dump($this->db->last_query());die;
		return $query->result_array();

	}
}