<div class="col-md-5 text-right">
	<strong>Tipo:</strong>
</div>
<div class="col-md-7">
	<span id="mora_tipo"></span>
</div>
<div class="col-md-5 text-right">
	<strong>Grupo:</strong>
</div>
<div class="col-md-7">
	<span id="mora_grupo"></span>
</div>
<div id="mora_desdehasta">
	<div class="col-md-5 text-right">
		<strong>Desde:</strong>
	</div>
	<div class="col-md-7">
		<span id="mora_desde"></span>
	</div>
	<div class="col-md-5 text-right">
		<strong>Hasta:</strong>
	</div>
	<div class="col-md-7">
		<span id="mora_hasta"></span>
	</div>
</div>

<div id="mora_creditosCliente">
	<div class="col-md-5 text-right">
		<strong>Accion:</strong>
	</div>
	<div class="col-md-7">
		<span id="mora_accion"></span>
	</div>
	<div class="col-md-5 text-right">
		<strong>Creditos x Cliente:</strong>
	</div>
	<div class="col-md-7">
		<span id="mora_creditosXCliente"></span>
	</div>
</div>

<script>

	/**
	 *	Completa los campos para la configuracion de mora
	 *
	 * @param data
	 */
	function fillMora(data) {
		$("#fieldsMora").show();
		$("#fieldsPreventiva").hide();
		$("#fieldsVentas").hide();

		$("#mora_tipo").html(data.tipo);
		$("#mora_grupo").html(data.grupo);

		if (data.grupo === 'TODAS') {
			populateFields(true, true, data.desde, data.hasta, data.accion, data.credito_cliente);
		}

		if (data.grupo === 'PRIMARIA') {
			populateFields(true, false, data.desde, data.hasta);
		}

		if (data.grupo === 'RETANQUEO') {
			populateFields(true, true, data.desde, data.hasta, data.accion, data.credito_cliente);
		}
	}

	/**
	 *	Rellena los campos y muestra los grupos correspondientes
	 *
	 * @param showDesdeHasta
	 * @param showCreditos
	 * @param desde
	 * @param hasta
	 * @param accion
	 * @param creditos
	 */
	function populateFields(showDesdeHasta, showCreditos, desde = '', hasta = '', accion = '', creditos = '') {
		if (showDesdeHasta) {
			$("#mora_desdehasta").show();
		} else {
			$("#mora_desdehasta").hide();
		}

		if (showCreditos) {
			$("#mora_creditosCliente").show();
		} else {
			$("#mora_creditosCliente").hide();
		}

		$("#mora_desde").html('&nbsp;' + desde);
		$("#mora_hasta").html('&nbsp;' + hasta);

		if (accion === 'IN') {
			accion = 'INCLUIR'
		} else if (accion === 'NOT IN') {
			accion = 'EXCLUIR'
		}
		
		$("#mora_accion").html('&nbsp;' + accion);
		$("#mora_creditosXCliente").html('&nbsp;' + creditos);
	}
</script>
