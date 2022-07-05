<div class="panel panel-default">
	<div class="panel-body">
		<div class="row" style="margin-top: 18px;">
			<div class="col-sm-10" style="margin-top: 12px;">
				<span>Operadores:</span><br>
				<select class="form-control select2-multiple selectOperadores" name="operadores[]" id="operadores" multiple="multiple">
					<?php foreach ($data['tiposOperadores'] as $tiposOperador) { ?>
						<option value="<?=$tiposOperador['idtipo_operador']?>"><?=$tiposOperador['descripcion']?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-2" style="margin-top: 12px;">
				<span>Equipo:</span><br>
				<select class="form-control selectOperadores" name="equipo" id="equipo">
					<option value="TODOS" selected="selected">TODOS</option>
					<option value="GENERAL">GENERAL</option>
					<option value="COLOMBIA">COLOMBIA</option>
					<option value="ARGENTINA">ARGENTINA</option>
				</select>
			</div>
		</div>
		<br><br>
		<div class="row">
			<div class="col-md-12">
				<div class="checkbox">
					<label>
						GESTION AUTOMATICA DE CASOS <input type="checkbox" id="automatico"> 
					</label>
				</div>
			</div>
		</div>
		<div class="row" id="opcionesAutomatico" style="display: none">
			<div class="col-md-4">
				<span>Minutos 1era Gestion:</span><br>
				<select class="form-control selectTiempos" name="min1" id="min1" style="width: 100%">
					<option value="0" selected="selected"></option>
					<?php for ($i = 1; $i <= 10; $i++) { ?>
						<option value="<?=$i?>"><?=$i?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-4">
				<span>Minutos Extra:</span><br>
				<select class="form-control selectTiempos" name="minExtra" id="minExtra" style="width: 100%">
					<option value="0" selected="selected"></option>
					<?php for ($i = 1; $i <= 10; $i++) { ?>
						<option value="<?=$i?>"><?=$i?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-4">
				<span>Cantidad Extensiones:</span><br>
				<select class="form-control selectTiempos" name="extensiones" id="extensiones" style="width: 100%">
					<option value="1">1</option>
					<?php for ($i = 2; $i <= 5; $i++) { ?>
						<option value="<?=$i?>"><?=$i?></option>
					<?php } ?>
				</select>
			</div>
			
			
			<div class="col-md-2" style="margin-top: 5px">
				<p><strong>DURACION:</strong></p>
				<p><strong>TOTAL:</strong></p>
			</div>
			<div class="col-md-2 text-right" style="margin-top: 5px">
				<p id="calculoTiempoGestion"></p>
			 	<p id="TotalTiempoGestion"></p>
			</div>
			<div class="col-md-2" style="margin-top: 5px">
				<p><strong>DURACION:</strong></p>
			</div>
			<div class="col-md-2 text-right" style="margin-top: 5px">
				<p id="calculoTiempoExtra"></p>
				<p id="TotalTiempoExtra"></p>
			</div>
			<div class="col-md-1" style="margin-top: 5px">
				<p><strong>DURACION:</strong></p>
			</div>
			<div class="col-md-3 text-right" style="margin-top: 5px">
				<p id="calculoTiempoExtensiones"></p>
				<p id="TotalTiempoExtensiones"></p>
			</div>
			<div class="col-md-6 text-right">
				&nbsp;
			</div>
			<div class="col-md-3 text-right">
				<p><strong>TOTAL HORAS LABORALES:</strong></p>
			</div>
			<div class="col-md-3 text-right">
				<p id="totalHorasLaborales"></p>
			</div>
			<div class="col-md-12">
				<p><strong>Horas Laborales: <span id="horasLaboralesNumero">1</span></strong></p>
				<input type="range" id="horasLaborales" min="1" max="10" value="1" data-rangeSlider>
			</div>

			<div class="col-md-12" style="margin-top: 15px;">
				<p><strong>Cantidad operadores: <span id="cantOperadoresNumero">1</span> <span id="alertCantOperadores"
																							  style="display: none"><i
									class="fa fa-exclamation-triangle" style="color:red" aria-hidden="true"
									title="Los operadores exceden los casos disponibles"></i></span></strong></p>
				<input type="range" id="cantOperadores" min="1" max="1" value="1" data-rangeSlider>
			</div>
			<div class="col-md-12" style="margin-top: 15px;">
				<div class="alert alert-danger alert-campania" role="alert" style="display: none">
					Con la configuracion actual la campaña excede el dia laboral. Por favor considere reconfigurar los tiempos o la campaña.
				</div>&nbsp;
			</div>
		</div>
		<input type="hidden" id="cantidadCasos" value="0">
		<input type="hidden" id="cantidadOperadores" value="0">
	</div>
</div>


<script>
	$(document).ready(function(){
		$('#cantOperadores').on('input', function() {
			let cantOperadoresSeleccionados = Number($(this).val());
			let cantOperdoresMaximos = Number($("#cantidadOperadores").val());
			let cantCasos = Number($("#cantidadCasos").val());
			
			let html = cantOperadoresSeleccionados + "/"+ cantOperdoresMaximos;

			$("#cantOperadoresNumero").html(html);
			calcularCampaniaTiempoEstimado();
		});
		
		$('#horasLaborales').on('input', function() {
			$("#horasLaboralesNumero").html($(this).val() + " Horas");
			calcularCampaniaTiempoEstimado();
		});
		
		$(".selectTiempos").change(function(){
			calcularCampaniaTiempoEstimado();
		})
	  
	  	$(".selectOperadores").change(function() {
			getCantidadOperadores(function () {
				calcularCampaniaTiempoEstimado();
			})
		})
	});
	
	
</script>

