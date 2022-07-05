<div class="panel panel-default">
	<div class="panel-heading">
		<H4><strong>DATOS CAMPAÑA</strong></H4>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				Descripción<br>
				<input class="form-control" type="text" name="descripcion" id="descripcion" value="" style="width:100%;"  autocomplete="off">
			</div>
		</div>
			
		<div class="row" style="margin-top: 18px;">
			<div class="col-sm-2">
				<span>Tipo</span>
				<select style="width: 100%;" class="form-control" name="tipoo" id="tipoo" onclick="validate()">
					<option value="" selected="selected"></option>
					<option value="PREVENTIVA"> PREVENTIVA </option>
					<option value="MORA"> MORA </option>
					<option value="VENTAS"> VENTAS </option>
				</select>
			</div>
			<div class="col-sm-2">
				<span>Grupo</span>
				<select style="width: 100%;" class="form-control" name="grupo" id="grupo" onclick="validate()">
					<option value="" selected="selected"> </option>
					<option value="TODAS"> TODAS </option>
					<option value="PRIMARIA"> PRIMARIA </option>
					<option value="RETANQUEO"> RETANQUEO </option>
				</select>
				<select style="width: 100%; display: none" class="form-control" name="grupoVentas" id="grupoVentas" onchange="validate()">
					<option value="" selected="selected"> </option>
					<option value="TODOS"> TODOS </option>
					<option value="ALTA_CLIENTE"> ALTA CLIENTE </option>
					<option value="ULTIMO_OTORGAMIENTO"> ULTIMO OTORGAMIENTO </option>
					<option value="CANTIDAD_CREDITOS"> CANTIDAD CREDITOS </option>
					<option value="MAYOR_ATRASO"> MAYOR ATRASO </option>
				</select>
			</div>
			<div id="input_ventas" style="display: none">
				<div class="col-sm-2" id="ventas_mayor">
					<span>Mayor o igual a</span>
					<input style="width: 100%;" autocomplete="off" class="form-control datepicker" name="ventas_alta_cliente_input" id="ventas_alta_cliente_input" >
					<input style="width: 100%;" autocomplete="off" class="form-control datepicker" name="ventas_ultimo_otorgamiento_input" id="ventas_ultimo_otorgamiento_input" >
					<input style="width: 100%;" autocomplete="off" onchange="ValidarNumeros(event)" class="form-control entero" name="ventas_cantidad_creditos_input" id="ventas_cantidad_creditos_input" >
				</div>
				<div class="col-sm-2" id="ventas_menor">
					<span>Menor o igual a</span>
					<input style="width: 100%;" autocomplete="off" onchange="ValidarNumeros(event)" class="form-control entero" name="ventas_mayor_atraso_input" id="ventas_mayor_atraso_input" >
				</div>
			</div>
			<div id="input_clientes" style="display: none">
				<div class="col-sm-2">
					<span>Acción</span>
					<select style="width: 100%;" class="form-control" name="accion" id="accion" onclick="validate()">
						<option value="" selected="selected"> </option>
						<option value="IN"> INCLUIR </option>
						<option value="NOT IN"> EXCLUIR </option>
					</select>
				</div>
				<div class="col-sm-2">
					<span>Creditos x cliente</span>
					<input style="width: 100%;" class="form-control entero" name="credito_cliente" id="credito_cliente"  onkeypress="ValidarNumeros(event)" autocomplete="off">
				</div>
			</div>
			<div id="input" style="display: none">
				<div class="col-sm-2">
					<span>Desde</span>
					<input style="width: 100%;" class="form-control entero" name="desde" id="desde"  onkeypress="ValidarNumeros(event)" autocomplete="off">
				</div>
				<div class="col-sm-2">
					<span>Hasta</span>
					<input style="width: 100%;" class="form-control entero" name="hasta" id="hasta" onkeypress="ValidarNumeros(event)" autocomplete="off">
				</div>
			</div>
		</div>
		<div class="row" style="margin-top: 18px;">
			<div class="col-sm-2">
				<span>Orden</span>
				<select style="width: 100%;" class="form-control" name="orden" id="orden">
					<option value="" selected="selected"> </option>
					<option value="1">MENOR A MAYOR</option>
					<option value="0">MAYOR A MENOR</option>
				</select>
			</div>
			<div class="col-sm-2">
				<span>Asignar</span>
				<select style="width: 100%;" class="form-control" name="asignar" id="asignar">
					<option value="5" selected="selected"> 0 </option>
					<option value="5"> 5 </option>
					<option value="10"> 10 </option>
					<option value="15"> 15 </option>
					<option value="20"> 20 </option>
				</select>
			</div>
			<div class="col-sm-2">
				<span>Regestionar a dias</span>
				<select style="width: 100%;" class="form-control" name="re_gestionar" id="re_gestionar">
					<option value="1"> 1 </option>
					<option value="2"> 2 </option>
					<option value="3"> 3 </option>
					<option value="4"> 4 </option>
					<option value="5"> 5 </option>
					<option value="6"> 6 </option>
					<option value="7"> 7 </option>
					<option value="8"> 8 </option>
					<option value="9"> 9 </option>
					<option value="10"> 10 </option>
				</select>
			</div>
			<div class="col-sm-2">
				<span>Autollamada</span>
				<select style="width: 100%;" class="form-control" name="autollamada" id="autollamada">
					<option value="NO" selected="selected"> NO </option>
					<option value="SI"> SI </option>
				</select>
			</div>
			<div class="col-sm-2">
				<span>Equipo:</span><br>
				<select class="form-control" name="equipoQuery" id="equipoQuery">
					<option value="TODOS" selected="selected">TODOS</option>
					<option value="COLOMBIA">COLOMBIA</option>
					<option value="ARGENTINA">ARGENTINA</option>
				</select>
			</div>
			<div class="col-sm-2">
				<span>Estado</span>
				<select style="width: 100%;" class="form-control" name="estado" id="estado">
					<option value="" selected="selected"></option>
					<option value="1"> ACTIVA </option>
					<option value="0"> INACTIVA </option>
				</select>
				<input type="hidden" id="id_campania">
				<input type="hidden" id="tipo_btn">
			</div>
		
		</div>
		<div class="row" style="margin-top: 18px;">
			<div class="col-md-12">
					<span>Exclusiones:</span><br>
					<select class="form-control select2-multiple" name="exclusiones[]" id="exclusiones" multiple="multiple"></select>
			</div>
		</div>
		
			
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			startDate: new Date(),
			maxDate: new Date()
		});
	})
</script>
