<div class="col-md-5 text-right">
	<strong>Tipo:</strong>
</div>
<div class="col-md-7">
	<span id="ventas_tipo"></span>
</div>
<div class="col-md-5 text-right">
	<strong>Grupo:</strong>
</div>
<div class="col-md-7">
	<span id="ventas_grupo"></span>
</div>
<div id="ventas_grupo_value">
	<div class="col-md-5 text-right">
		<strong>Valor:</strong>
	</div>
	<div class="col-md-7">
		<span id="ventas_valor"></span>
	</div>
</div>

<script>
	function fillVenta(data) {
		$("#fieldsMora").hide();
		$("#fieldsPreventiva").hide();
		$("#fieldsVentas").show();
		
		
		$("#ventas_tipo").html(data.tipo);
		$("#ventas_grupo").html(data.grupo_ventas);
		$("#ventas_valor").html(data.grupo_ventas_value);
	}
</script>
