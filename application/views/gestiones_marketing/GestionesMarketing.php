<script src="<?php echo base_url('assets/templates/templates.js');?>"></script>
<style>
    .loader {
		border: 16px solid #f3f3f3; /* Light grey */
		border-top: 16px solid #3498db; /* Blue */
		border-radius: 50%;
		width: 100px;
		height: 100px;
		animation: spin 2s linear infinite;
		display: none;
		align-items: center;
		justify-content: center;
		margin: 0 auto;
	}
	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

    .collapse_editor{
        display: none;
    }

	hr{
        border: 0 none #e8e1e0;
        border-top: 1px solid #e8e1e0;
        height: 1px;
        margin: 5px 0;
        display: block;
        clear: both;
    }

	.form_grafica{
		margin-left:3%;
	}

	#settings_and_table_container{
		margin-left:1.1%;
	}

	#graph{
		left:-60px;
	}
</style>
		<h4 style="padding-left:2.8%" for="search-cliente">Generación de grafico:</h4>

		<div class="row" id="settings_and_graph_container" style="display:block; padding-left: 2%; padding-right: 2%;">
			<!-- menu graph -->
			<div class="col-md-5">
				<form>
					<div class="row form_grafica">
						<div class="form-row">
							<div class="form-group col-md-10">
								<label for="selectTipo">Tipo:</label>
								<select class="form-control" id="selectTipo" name="selectTipo">
										<option value="DESEMBOLSO">DESEMBOLSO</option>
										<option value="APROBADO_BURO">APROBADO BURO</option>
										<option value="TOTAL">TOTAL</option>
								</select>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-10">
								<label for="fecha_desde">Fecha desde:</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input type="input" autocomplete="off" class="form-control datetimepicker" name="date_since" id="date_since">
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-10">
								<label for="fecha_hasta">Fecha hasta:</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input type="input" autocomplete="off" class="form-control datetimepicker" name="date_to" id="date_to">
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-12 pull-right">
								<a class="btn btn-info" id="search_gestion_marketing_graph"><i class="fa fa-search"></i> BUSCAR</a>
				
							</div>
						</div>
					</div>
				</form>
			</div>

<div class="modal fade" id="modalLoading" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS SE GENERA EL GRAFICO</h4>
					<div class="col-md-12 hide" id="succes">
						<!-- Primary box -->
						<div class="box box-solid box-primary">
							<div class="box-header">
								<h3 class="box-title">BUSQUEDA DE PLANTILLAS</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
									<button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body">
								<span id="respuesta"></span>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div><!-- /.col -->
				</div>
			<div class="modal-body">
				<div class="data"></div>
				<div class="loader"></div> 
			</div>
				<div class="modal-footer clearfix">
				</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

			<!-- menu graph -->
			<div class="col-md-7" id="graph">
				
			</div>
		</div>
	</div>

	<!-- inputs hidden--> 
	<input type="hidden" name="" id="providers_hidden">
	<input type="hidden" name="" id="type_hidden">
	<input type="hidden" name="" id="date_since_hidden">
	<input type="hidden" name="" id="date_to_hidden">
	<!-- inputs hidden  -->

	<hr>
	<!-- menu table -->
	<div class="box-header with-border col-md-12"></div>
		<h4 style="padding-left:2.8%" for="search-cliente">Generación de reportes:</h4>
		<div class="row" id="settings_and_table_container" style="display:block;">
			<div class="col-md-12" style="padding-left:3%;">
				<form>
					<div class="row">
						<div class="form-row">
							<div class="form-group col-md-2" id="provider_container">
								<label for="selectProvider">Proveedor: </label>
								<select class="js-example-basic-multiple" name="selectProvider[]" id="selectProvider"
									multiple="multiple" style="width: 100%" placeholder="selectProvider">
								</select>
							</div>
						
							<div class="form-group col-md-2">
								<label for="selectTipo_table">Tipo:</label>
								<select class="form-control" id="selectTipo_table" name="selectTipo_table">
										<option value="DESEMBOLSO">DESEMBOLSO</option>
										<option value="APROBADO_BURO">APROBADO BURO</option>
										<option value="TOTAL">TOTAL</option>
								</select>
							</div>
						
							<div class="form-group col-md-2">
								<label for="fecha_desde">Fecha desde:</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input type="input" autocomplete="off" class="fechas form-control datetimepicker" name="date_since_table" id="date_since_table">
								</div>
								<!-- <input type="text" class="form-control datepicker_table" id="date_since_table" name="date_since_table"> -->
							</div>
						
							<div class="form-group col-md-2">
								<label for="fecha_hasta">Fecha hasta:</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input type="input" autocomplete="off" class="fechas form-control datetimepicker" name="date_to_table" id="date_to_table">
								</div>
								<!-- <input type="text" class="form-control datepicker_to_table" id="date_to_table" name="date_to_table"> -->
							</div>
					
							<div class="form-group col-md-2" style="margin-top: 25px;">
								<a class="btn btn-info" id="search_gestion_marketing_table"><i class="fa fa-search"></i> BUSCAR</a>
							</div>
							<div class="form-group col-md-2 pull-left" id="btn_descarga_container" style="display: none; margin-top: 17px; margin-left: -130px;padding: 8px;">
								<button type="button" id="btn_descargar_data_marketing" class="btn btn-success" title="Exportar Excel"><i class="fa fa-file-excel-o"></i> Exportar Excel</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- menu table -->
</br>

<div id="marketing_table_container" style="padding-left: 2%; padding-right: 2%;"></div>

<div class="modal fade" id="modalTabla" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS CARGAN LOS DATOS DE LA TABLA</h4>
					<div class="col-md-12 hide" id="succes">
						<!-- Primary box -->
						<div class="box box-solid box-primary">
							<div class="box-header">
								<h3 class="box-title">BUSQUEDA DE PLANTILLAS</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
									<button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body">
								<span id="respuesta"></span>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div><!-- /.col -->
				</div>
			<div class="modal-body">
				<div class="data"></div>
				<div class="loader"></div> 
			</div>
				<div class="modal-footer clearfix">
				</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>

	$('.datetimepicker').datepicker({
		tooltips: {
			today: 'Ve a la fecha actual',
			clear: 'Limpiar Selecion',
			close: 'Cerrar Caledario',
			selectMonth: 'Seleccione Mes',
			prevMonth: 'Mes Anterior',
			nextMonth: 'Mes Siguiente',
			selectYear: 'Selecione un Año',
			prevYear: 'Año Anterior',
			nextYear: 'Año Siguiente',
			selectDecade: 'Seleccione Decada',
			prevDecade: 'Decada Anterior',
			nextDecade: 'Siguiente Decada',
			prevCentury: 'Previous Century',
			nextCentury: 'Next Century'
		},
		icons: {
			time: "fa fa-clock-o",
			date: "fa fa-calendar",
			up: "fa fa-chevron-up",
			down: "fa fa-chevron-down",
			previous: 'fa fa-chevron-left',
			next: 'fa fa-chevron-right',
			today: 'fa fa-screenshot',
			clear: 'fa fa-trash',
			close: 'fa fa-remove'
		},
		dateFormat: 'dd-mm-yy'
	});
	
	//Al cargar el documento
	$(document).ready(function () {
		//renderizo options de select providers en modal
		let options = "";
		$.ajax({
            type: "GET",
            url: base_url + 'getProviders'})
            .done(function (response) {
				let obj = JSON.parse(response);
				
				obj.providers.map( function (value, index){
					options += `<option value="` + value['nombre'] + `">` + value['nombre'] + `</option>`;
				});   
				
				$('#selectProvider').html(options);

				$('#selectProvider').select2({
                placeholder: '.: Selecciona los criterios :.',
                multiple : true
                });

				$(".datepicker").datepicker();
                $(".datepicker_to").datepicker();      
				$(".datepicker_table").datepicker();
				$(".datepicker_to_table").datepicker();  

				//obtengo fecha de hoy 
				let hoy = new Date();
				let today = hoy.getDate()  + '/' + (hoy.getMonth() + 1) + '/' + hoy.getFullYear();
				//obtengo fecha de una semana atras
				let now = new Date();
				let seven_days = 1000 * 60 * 60 * 24 * 7;
				let resta = now.getTime() - seven_days;
				let seven_days_before = new Date(resta);
				let week_ago =  seven_days_before.getDate()  + '/' + (seven_days_before.getMonth() + 1) + '/' + seven_days_before.getFullYear();
				// console.log("today: " + today);
				// console.log("week_ago: " + week_ago);
				// console.log(typeof today);
				// console.log(typeof week_ago);
				//seteo fechas 
				$('input[name="date_since"]').val(week_ago);
				$('input[name="date_to"]').val(today);
				$('#selectTipo option[value="TOTAL"]').attr('selected','selected')
			
				//ejecuto evento
				$('a#search_gestion_marketing_graph').trigger('click');


			});
	});

    //Solicitud para realizar graficos
    $(document).on('click', 'a#search_gestion_marketing_graph', function(){
		var $loader = $('.loader');
		let base_url = $("#base_url").val();
		let select_tipo = $("#selectTipo").val();
		let fecha_desde = $("#date_since").val();
		let fecha_hasta = $("#date_to").val();
		// console.log("fecha_desde:  " + fecha_desde + " fecha_hasta " + fecha_hasta );
		if(!select_tipo){
			Swal.fire('', '<h4>Debe ingresar un tipo</h4>', 'warning');
		}
		if(!fecha_desde){
			Swal.fire('', '<h4>Debe ingresar la fecha de origen</h4>', 'warning');
		}
		if(!fecha_hasta){
			Swal.fire('', '<h4>Debe ingresar la fecha de destino</h4>', 'warning');
		} else {
			// $('#myChartBar').empty();
			$.ajax({
			dataType: "JSON",
			data: {
				"select_tipo": select_tipo, 
				"fecha_desde": fecha_desde,
				"fecha_hasta": fecha_hasta,
				"providers": "",
				"type": 0
			},
			url: base_url + 'getSolicitudes',
			type: 'POST',
			beforeSend: function() {
					$("#modalLoading").modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    });
                    $loader.show();
				}
			,success: function(respuesta){
				console.log(respuesta)
				setTimeout(function(){
                    $loader.hide();
                    $("#modalLoading").modal('hide');
                    }, 700);
				$('#graph').empty();
				$('#graph').append('<canvas id="myChartBar" width="9" height="3"></canvas>');
				var labelsBar = [];
				var dataBar = [];
		
				respuesta.providers.map(function (dato, index) {
					labelsBar[index] = dato.utm_source;
					dataBar[index] = parseInt(dato.cantidad)         
				});
				backgroundColorBar = [
					'rgba(255, 99, 132, 0.75)', // red
					'rgba(139, 195, 74, 0.75)', // green
					'rgba(54, 162, 235, 0.75)', // blue
					'rgba(75, 192, 192, 0.75)', // green
					'rgba(255, 206, 86, 0.75)', // yellow
					'rgba(153, 102, 255, 0.75)', // purple
					'rgba(255, 159, 64, 0.75)', // orange
					'purple',
					'blue',
					'yellow',
					'rgba(191, 0, 0, 0.75)', // otro rojo
					'red',
					'orange'
				];

				
				
				datasets = [
					{
						label: '',
						data: dataBar,						
						backgroundColor: backgroundColorBar,
						borderWidth: 1,
						datalabels: {
							anchor: 'end',
							align: 'end',
							offset: 2,
							labels: {
								value: {
									font: {
										weight: 'bold',
									}
								}
							},
							formatter: function(value, context) {
								return value;
							}
						}  
					}
				];
				
				renderGraph('myChartBar', 'bar', datasets, labelsBar, fecha_desde, fecha_hasta);
				$('#settings_and_table_container').css('display','block');
				$('#modalSetDataMarketing').modal('hide');
			},error: function (respuesta) {
					setTimeout(function(){
                    $loader.hide();
                    $("#modalLoading").modal('hide');
                    }, 700);
					console.log("respuesta error");
					console.log(respuesta) ;
			}
		});
    };

	});	

	
    //Solicitud para realizar tabla
    $(document).on('click', 'a#search_gestion_marketing_table', function(){
		$("#marketing_table_container").hide();
		var $loader = $('.loader');
		let base_url = $("#base_url").val();
		let select_tipo = $("#selectTipo_table").val();
		let fecha_desde = $("#date_since_table").val();
		let fecha_hasta = $("#date_to_table").val();
		let providers = $('select[name="selectProvider[]"]').val();
		providers = providers.toString();
		if(!select_tipo){
			Swal.fire('', '<h4>Debe ingresar un tipo</h4>', 'warning');
		}
		if(!fecha_desde){
			Swal.fire('', '<h4>Debe ingresar la fecha de origen</h4>', 'warning');
		}
		if(!fecha_hasta){
			Swal.fire('', '<h4>Debe ingresar la fecha de destino</h4>', 'warning');
		}
		if(!providers){
			Swal.fire('', '<h4>Debe ingresar un proveedor</h4>', 'warning');
		} else{
				
			//seteo datos hidden para reutilizarlos en la exportacion
			$('#providers_hidden').val(providers);
			$('#type_hidden').val(select_tipo);
			$('#date_since_hidden').val(fecha_desde);
			$('#date_to_hidden').val(fecha_hasta);
		
			$.ajax({
				data: {
					"select_tipo": select_tipo, 
					"fecha_desde": fecha_desde,
					"fecha_hasta": fecha_hasta,
					"providers": providers,
					"type": 1
				},
				url: base_url + 'getSolicitudes',
				type: 'POST',
				beforeSend: function() {
					$("#modalTabla").modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    });
                    $loader.show();
				},
				success: function(data){
					setTimeout(function(){
                    $loader.hide();
                    $("#modalTabla").modal('hide');
                    }, 700);
					$("#marketing_table_container").html(data);
					// $('#modalSetDataMarketing').modal('hide');
					$("#marketing_table_container").show();
					$('#btn_descarga_container').css('display','block');
					
					
				},
				error: function (respuesta) {
					setTimeout(function(){
                    $loader.hide();
                    $("#modalTabla").modal('hide');
                    }, 700);
					console.log("respuesta error");
					console.log(respuesta) ;
				},
				always: function (respuesta) {
					setTimeout(function(){
                    $loader.hide();
                    $("#modalTabla").modal('hide');
                    }, 700);
					console.log("respuesta always");
					console.log(respuesta);
				}
			});
		}
	});

	//descargar datos a excel
	$(document).on('click', 'button#btn_descargar_data_marketing', function() {

		let base_url = $("#base_url").val();
		let select_tipo = $("#type_hidden").val();
		let fecha_desde = $("#date_since_hidden").val();
		let fecha_hasta = $("#date_to_hidden").val();
		let providers = $('#providers_hidden').val();
		// console.log("base_url: "  + base_url);
		// console.log("providers: " +  providers +  +  " fecha_desde " + fecha_desde + " fecha_hasta: " +  fecha_hasta  +  " select_tipo: " +  select_tipo);

		$.ajax({
			data: {
				"select_tipo": select_tipo, 
				"fecha_desde": fecha_desde,
				"fecha_hasta": fecha_hasta,
				"providers": providers,
				"type": 1
			},
			url: base_url + 'gestiones_marketing/GestionesMarketing/downloadData',
			type: 'POST',
			success: function(data){
				// console.log(data);
				let url = base_url+"public/csv/"+data;
				window.open(url, '_self');
			},
			error: function (respuesta) {
				console.log("respuesta error");
				console.log(respuesta) ;
			},
			always: function (respuesta) {
				console.log("respuesta always");
				console.log(respuesta);
			}
		});
	});

    //Render graficos y opciones para la grafica
    function renderGraph(el, type, datasets, labelData, fecha_desde, fecha_hasta) {
        let ctx = document.getElementById(el).getContext('2d');
        x = new Chart(ctx, {
            type: type,
            data: {
                labels: labelData,
                datasets: datasets
            },
            options: {
                plugins: {
                    datalabels: {
                        color: '#000000',
                        align: 'end',
                    }
                },
				legend: {
					display:false
				},
				title: {
					display: true,
					text: `TABLERO DE PROVEEDORES ${fecha_desde} - ${fecha_hasta}`
				}
            }
        });
    }

</script>
<script src="<?php echo base_url('assets/js/Chart.min.js');?>"></script>
<script src="<?php echo base_url('assets/js/chartjs-plugin-datalabels.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/supervisores/select2Bootstrap.css'); ?>"/>


