<!-- INICIO FORMULARIO SOLICITUDES INDICADORES -->
<div id="box_solicitud_gestion_filtro" class="box box-default">
	<div class="box-header with-border">
		<form id="filtro_gestion" class="form-horizontal" method="GET" action="">
			<div class="col-md-1 form-group" style="margin-right: 5px;">
				<label class="control-label">Canal:</label>
				<select require id="canal" name="canal" class="form-control input-sm" >
					<option value="">.:Seleccione una opción:.</option>
					<option value="SOLVENTA">Solventa</option>
					<option value="MEJORPRESTAMO">mejorPrestamo</option>
				</select>
        	</div>
            <div class="col-md-1 form-group" style="margin-right: 5px;">
				<label class="control-label">Tipo:</label>
				<?php echo form_dropdown('tipo_solicitud', $dato, '','id="tipo_solicitud" class="form-control input-sm"'); ?>
            </div>
            <div class="col-md-1 form-group" style="margin-right: 5px;">
				<label class="control-label">Buro:</label>
				<select id="respuesta_buro" name="respuesta_buro" class="form-control input-sm">
					<option value="TODOS">TODOS</option>
					<option value="SIN_ANALIZAR">SIN ANALIZAR</option>
					<option value="APROBADO">APROBADOS</option>
					<option value="RECHAZADO">RECHAZADOS</option>
				</select>
            </div>
            <div class="col-md-1 form-group" style="margin-right: 5px;">
				<label class="control-label">Gestión:</label>
				<select id="track_gestion" name="track_gestion" class="form-control input-sm">
					<option value="TODOS">TODOS</option>
					<option value="SIN_GESTION">SIN GESTION</option>
				</select>
            </div>
			<div class="col-md-1 form-group" style="margin-right: 5px;">
				<label class="control-label">Estados:</label>
				<?php echo form_dropdown('estado', $estados, '','id="estado" class="form-control input-sm"'); ?>			
            </div>
            <div class="col-md-2 form-group" style="margin-right: 5px;">
				<label class="control-label">Razón Rechazo:</label>
				<select id="razon_rechazo" name="razon_rechazo" class="form-control input-sm">
					<option value="TODOS">TODOS</option>
				</select>
            </div>
            <div class="col-md-1 form-group" style="margin-right: 5px;">
				<label class="control-label">Operadores:</label>
				<select id="operador_asignado" name="operador_asignado" class="form-control input-sm">
					<option value="TODOS">TODOS</option>
				</select>
            </div>
			<div class="col-md-2 form-group" style="margin-right: 5px;">
				<label class="control-label">Fechas:</label>
				<div class="input-group date">
					<div class="input-group-addon">
						<i class="fa fa-calendar"></i>
					</div>
					<input require id="rango_fecha" name="rango_fecha" type="text" class="form-control pull-right input-sm date_range" autocomplete="off">
				</div>
			</div>
			<div class="col-md-2">
				<div class="row">&nbsp;</div>
	            <!-- BOTONES -->
	            <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
	            <a id="btn_excel"  href="#" class="btn btn-success btn-sm" role="button"><i class="fa fa-file-excel-o" download="file.xls"></i>Exportar Excel</a>
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
		<div class="row" id="gestion_resultado" style="overflow:auto;min-height: 150px; display: none;">
		    <table align="center" id="table_gestion_solicitudes" class="table table-striped table=hover display" width="100%">
		        <thead style="font-size: smaller; ">
		            <tr class="info">
		                <th style="text-align: center;">Canal</th>
		                <th style="text-align: center;">Tipo</th>
		                <th style="text-align: center;">ID</th>
		                <th style="text-align: center;">Fecha alta</th>
		                <th style="text-align: center;">Documento</th>
		                <th style="text-align: center;">Nombres</th>
		                <th style="text-align: center;">Apellidos</th>
		                <th style="text-align: center;">Buro</th>
		                <th style="text-align: center;">Estado</th>
		                <th style="text-align: center;">Id Opera.</th>
		                <th style="text-align: center;">Operador</th>
		                <th style="text-align: center;">Última Gestion</th>
		            </tr>
		        </thead>
		        <tbody style="font-size: 11px; text-align: center;">
		        </tbody>
		    </table>	
		</div>
	</div><!-- box-body -->
</div>

<script type="text/javascript">
	$(document).ready(($) =>
	{
		/******************************/
	  	// GESTION
	  	/******************************/
	 	var formulario_gestion = $("#box_solicitud_gestion_filtro #filtro_gestion");
			// EVENTOS
			// Busco las solicitudes y las muestra en html
		var table_gestion = $("#solicitudes_gestion #gestion_resultado #table_gestion_solicitudes");
		var resultado_gestion = $("#solicitudes_gestion #gestion_resultado");

		// Datatables
		var tabla_gestion_resultados = table_gestion.DataTable({
	    "language": spanish_lang,
	    'iDisplayLength': 25,
	    'paging':true,
	    'info':true,
	    "searching": true,
		'columns':[
					{"data":null,
							"render":function(data, type, row, meta)
	                        {
	                        	return formulario_gestion.find('#canal').val();
	                        }
	                },
					{"data":"tipo_solicitud" },
					{"data":"id" },
					{"data":"fecha_alta" },
					{"data":"documento" },
					{"data":"nombres" },
					{"data":"apellidos" },
					{"data":"respuesta_analisis" },
					{"data":"estado" },
					{"data":"operador_asignado" },
					{"data":"operador_nombre_apellido" },
					{"data":"fecha_ultima_actividad" },
					// {"data":null,
					// 		"render":function(data, type, row, meta)
	                //         {
	                //         	return "";
	                //         }
	                // },
				  ]
		});

		formulario_gestion.on('submit',function (event)
		{
			event.preventDefault();
			//$("#box_solicitud_indicadores_filtro #solicitudes_resultado").html('');
			if(formulario_gestion.valid())
			{
				table_gestion_solicitudes();
			}else{
				return false;
			}	

			
		});

	    function table_gestion_solicitudes()
	    { 
	    	var formData = new FormData(formulario_gestion[0]);			
			
		

	    	$.ajax({
	            url:$('#base_url').val()+"api/reportes/solicitudes/gestion",
	            type:'POST',
	            data:formData,
	            cache: false,
	            contentType: false,
	            processData:false,
	            beforeSend:function(){
	            	resultado_gestion.hide();
					$('#solicitudes_gestion #loader-6').show();
			        tabla_gestion_resultados.clear();
					}
				})
	            .done(function(response){
					$('#solicitudes_gestion #loader-6').hide();
			        
			        tabla_gestion_resultados.rows.add(response.solicitudes);
			        tabla_gestion_resultados.draw();
	            	resultado_gestion.show();
	            });
	    } 

			
		formulario_gestion.find("#btn_excel").on('click', function(event){
			event.preventDefault();
			if(formulario_gestion.valid())
			{
				let base_url = $('#base_url').val();
				let canal = formulario_gestion.find('#canal').val();
				let tipo_solicitud = formulario_gestion.find('#tipo_solicitud').val();
				let buro = formulario_gestion.find('#respuesta_buro').val();
				let gestion = formulario_gestion.find('#track_gestion').val();
				let estado = formulario_gestion.find('#estado').val();
				let razon_rechazo = formulario_gestion.find('#razon_rechazo').val();
				let operador_asignado = formulario_gestion.find('#operador_asignado').val();
				let rango_fecha = formulario_gestion.find('#rango_fecha').val();
				let fechas = rango_fecha.split('|');
				var desde ="";
				var hasta ="";
				 if (fechas.length > 1)
				 {
				desde = fechas[0].trim();
			    hasta = fechas[1].trim();
				 }


				window.open(base_url+'api/reportes/solicitudes/gestion/excel/'+canal+'/'+tipo_solicitud+'/'+buro+'/'+gestion+'/'+estado+'/'+razon_rechazo+'/'+operador_asignado+'/'+desde+'/'+hasta, '_self');
				
				 return false;
			}else{
				return false;
			}
		})

	

			formulario_gestion.validate({
		    debug: true,
		    rules: {
		      canal: "required",
		      tipo_solicitud: "required",
		      rango_fecha: "required",
		      tipo_informe: "required",
		    },
		    messages: {
				canal: "",
				tipo_solicitud: "",
				tipo_informe: "",
				rango_fecha:"",
				respuesta_buro:"",
				track_gestion:"",
				estados:"",
				razon_rechazo:"",
				operadores:"",
		    },
			ignoreTitle: true,
			highlight: function ( element, errorClass, validClass ) {
				$( element ).parents( ".form-group" ).addClass( "has-error" ).removeClass( "has-success" );
				return false;
			},
			unhighlight: function (element, errorClass, validClass) {
				$( element ).parents( ".form-group" ).addClass( "has-success" ).removeClass( "has-error" );
			},
		  });

		/******************************/
		// FIN GESTION
		/******************************/
	})
</script>

