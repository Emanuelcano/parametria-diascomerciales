<div class="col-md-5 text-right">
	<strong>Tipo:</strong>
</div>
<div class="col-md-7">
	<span id="preventiva_tipo"></span>
</div>
<div class="col-md-5 text-right">
	<strong>Grupo:</strong>
</div>
<div class="col-md-7">
	<span id="preventiva_grupo"></span>
</div>
<div id="preventiva_desdehasta">
	<div class="col-md-5 text-right">
		<strong>Desde:</strong>
	</div>
	<div class="col-md-7">
		<span id="preventiva_desde"></span>
	</div>
	<div class="col-md-5 text-right">
		<strong>Hasta:</strong>
	</div>
	<div class="col-md-7">
		<span id="preventiva_hasta"></span>
	</div>
</div>

<div id="preventiva_creditosCliente">
	<div class="col-md-5 text-right">
		<strong>Accion:</strong>
	</div>
	<div class="col-md-7">
		<span id="preventiva_accion"></span>
	</div>
	<div class="col-md-5 text-right">
		<strong>Creditos x Cliente:</strong>
	</div>
	<div class="col-md-7">
		<span id="preventiva_creditosXCliente"></span>
	</div>
</div>

<script>

	/**
	 *	Completa los campos para la configuracion de mora
	 *
	 * @param data
	 */
	function fillPreventiva(data) {
		$("#fieldsMora").hide();
		$("#fieldsPreventiva").show();
		$("#fieldsVentas").hide();

		$("#preventiva_tipo").html(data.tipo);
		$("#preventiva_grupo").html(data.grupo);

		if (data.grupo === 'TODAS') {
			populateFieldsPreventiva(false, true, '', '', data.accion, data.credito_cliente);
		}

		if (data.grupo === 'PRIMARIA') {
			populateFieldsPreventiva(false, false);
		}

		if (data.grupo === 'RETANQUEO') {
			populateFieldsPreventiva(false, true, '', '', data.accion, data.credito_cliente);
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
	function populateFieldsPreventiva(showDesdeHasta, showCreditos, desde = '', hasta = '', accion = '', creditos = '') {
		if (showDesdeHasta) {
			$("#preventiva_desdehasta").show();
		} else {
			$("#preventiva_desdehasta").hide();
		}

		if (showCreditos) {
			$("#preventiva_creditosCliente").show();
		} else {
			$("#preventiva_creditosCliente").hide();
		}

		$("#preventiva_desde").html('&nbsp;' + desde);
		$("#preventiva_hasta").html('&nbsp;' + hasta);

		if (accion === 'IN') {
			accion = 'INCLUIR'
		} else if (accion === 'NOT IN') {
			accion = 'EXCLUIR'
		}

		$("#preventiva_accion").html('&nbsp;' + accion);
		$("#preventiva_creditosXCliente").html('&nbsp;' + creditos);
	}
</script>
