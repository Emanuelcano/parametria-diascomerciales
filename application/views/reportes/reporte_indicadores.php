<!-- INICIO FORMULARIO SOLICITUDES INDICADORES -->

<div id="box_solicitud_indicadores_filtro" class="box box-default">
		<div class="box-header with-border">
			<form id="filtro_indicadores" class="form-horizontal" method="GET" action="">
				<div class="col-md-1 form-group" style="margin-right: 5px;">
					<label class="control-label">Canal:</label>
					<select id="canal" name="canal" class="form-control input-sm" >
						<option value="">.:Seleccione una opción:.</option>
						<option value="SOLVENTA">Solventa</option>
						<option value="MEJORPRESTAMO">mejorPrestamo</option>
					</select>
            	</div>
	            <div class="col-md-2 form-group" style="margin-right: 5px;">
					<label class="control-label">Tipo:</label>

					<?php echo form_dropdown('tipo_solicitud', $dato, '','id="tipo_solicitud" class="form-control input-sm"'); ?>
				
	            </div>
				<div class="col-md-2 form-group" style="margin-right: 5px;">
					<label class="control-label">Fechas:</label>
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<input id="date_range" name="desde" type="text" class="form-control pull-right input-sm date_range" autocomplete="off" required>
					</div>
				</div>
	            <div class="col-md-1 form-group" style="margin-right: 5px;">
					<label class="control-label">Forma:</label>
					<select id="tipo_informe" name="tipo_informe" class="form-control input-sm" >
						<option value="">.:Seleccione una opción:.</option>
						<option value="RESUMIDO">RESUMIDO</option>
						<option value="DETALLADO">DETALLADO</option>
					</select>
	            </div>
	            <div class="col-md-2 form-group">
					<div class="row">&nbsp;</div>
	           		<!-- BOTONES -->
		            <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
		            <a id="btn_excel" href="#" class="btn btn-success btn-sm" role="button"><i class="fa fa-file-excel-o" download="file.xls"></i>Exportar Excel</a>
	            </div>
			</form>
		</div><!-- box-header -->
		<div class="box-body"> 
                <div class="loader" id="loader-6" style="display:none">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                </div>
			<div class="row" id="solicitudes_resultado" style="overflow:auto;">
			</div>
		</div><!-- box-body -->
	</div>

	

<!-- FIN FORMULARIO SOLICITUDES INDICADORES -->

<script type="text/javascript">
	$(document).ready(($) =>
	{
	/******************************/
	// INICIO INDICADORES
	/******************************/
	 	var formulario_indicadores = $("#box_solicitud_indicadores_filtro #filtro_indicadores");
		// Busco las solicitudes y las muestra en html
		formulario_indicadores.on('submit',function (event){
			$("#box_solicitud_indicadores_filtro #solicitudes_resultado").html('');
			if(formulario_indicadores.valid())
			{
				let canal = $(this).find('#canal').val();
				let tipo_solicitud = $(this).find('#tipo_solicitud').val();
				let rango_fecha = $(this).find('#date_range').val();
				let tipo_informe = $(this).find('#tipo_informe').val();
				get_resultado_indicadores(canal, tipo_solicitud, rango_fecha, tipo_informe);
			}else{
				return false;
			}	
		});

		formulario_indicadores.find("#btn_excel").on('click', function(event){
			event.preventDefault();
			if(formulario_indicadores.valid())
			{
				let base_url = $('#base_url').val();
				let canal = formulario_indicadores.find('#canal').val();
				let tipo_solicitud = formulario_indicadores.find('#tipo_solicitud').val();
				let tipo_informe = formulario_indicadores.find('#tipo_informe').val();
				let rango_fecha = formulario_indicadores.find('#date_range').val();
				let fechas = rango_fecha.split('|');
				var desde ="";
				var hasta ="";
				if (fechas.length > 1)
				{
					desde = fechas[0].trim();
				    hasta = fechas[1].trim();
				}
				window.open(base_url+'api/reportes/solicitudes/indicadores/excel/'+desde+'/'+hasta+'/'+canal+'/'+tipo_informe+'/'+tipo_solicitud, '_self');
				return false;

			}else{
				return false;
			}
		})

		// Ajax
		var get_resultado_indicadores = (canal, tipo_solicitud, rango_fecha, tipo_informe) =>
		{
			$.ajax({
				url: $("#base_url").val()+'reporte/solicitud/indicadores',
				type: 'POST',
				dataType: 'html',
				data: {'canal':canal, 'tipo_solicitud':tipo_solicitud, 'rango_fecha':rango_fecha, 'tipo_informe':tipo_informe},
				beforeSend:function(){
					$("#box_solicitud_indicadores_filtro #solicitudes_resultado").html('');
					$('#box_solicitud_indicadores_filtro #loader-6').show();
									}
			})
			.done(function(response) {
				$('#box_solicitud_indicadores_filtro #loader-6').hide();
				$("#box_solicitud_indicadores_filtro #solicitudes_resultado").html(response);
			})
			.fail(function() {
			})
		}

	    // Documentacion 
	    // https://jqueryvalidation.org/valid/
	  	formulario_indicadores.validate({
		    debug: true,
		    rules: {
		      canal: "required",
		      tipo_solicitud: "required",
		      desde: "required",
		      tipo_informe: "required",
		    },
		    messages: {
				canal: "",
				tipo_solicitud: "",
				tipo_informe: "",
				desde:"",
		    },
			ignoreTitle: true,
			highlight: function ( element, errorClass, validClass ) {
				$( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
			},
			unhighlight: function (element, errorClass, validClass) {
				$( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
			},
		  });
	/******************************/
	// FIN INDICADORES
	/******************************/
	})
</script>