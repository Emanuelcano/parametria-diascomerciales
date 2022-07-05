<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>

<style>
.nav-tabs.nav-justified>.active>a, .nav-tabs.nav-justified>.active>a:focus, .nav-tabs.nav-justified>.active>a:hover {
    border-bottom-color: #605ca8;
    background-color: #605ca89c;
    color: white;
}
.mx-0{
    margin-left: 0px !important;
    margin-right: 0px !important;
}
.p-0{
    padding: 0;
}
.cont {
    max-height: 300px;
    overflow: auto;
}
.cont::-webkit-scrollbar {
    width:5px;
}
.cont::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    border-radius:10px;  
}
.cont::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5);
    border-radius:10px;
    
}
.total {
    background-color: #fdf9de;
    padding: 5px;
}
</style>

<span class="hidden-xs">
    <?php
    $usuario = $this->session->userdata("username");
    $tipoUsuario = $this->session->userdata("tipo");
    ?>
</span>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario ?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario; ?>">
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">


<div class="row mx-0" style="display: block;  background: #FFFFFF;">
			<div class="col-lg-12" style="display: block;  background: #FFFFFF; margin-top: 7rem;">
				<div class="box-body">
					<a class="btn btn-app hide" onclick="vistaIndicadores()">
		                <i class="fa fa-line-chart"></i> Indicadores
	              	</a>
	              	<a class="btn btn-app" onclick="listaOperadores()">
						<i class="fa fa-users"></i> Operadores
					</a>
						<?php if ($this->session->userdata("idoperador") == 24 || $this->session->userdata("idoperador") == 95) {
							
						?>
						<a class="btn btn-app hide" onclick="vistaAsignaciones()">
							<i class="fa fa-exchange"></i> Asignaciones
						</a>
						<?php 
							}
						?>
						<a class="btn btn-app" onclick="cargarVistaAusencias()">
							<i class="fa fa-calendar"></i> Ausencias
						</a>
						<?php
							// if($this->session->userdata("id_usuario") == 3514 || $this->session->userdata("id_usuario") == 6756 ||$this->session->userdata("id_usuario") == 47745)
								echo '<a class="btn btn-app" id="cargarVistaHorarios" onclick="cargarVistaHorarios()"><i class="fa fa-clock-o"></i> Horarios</a>';
						?>              	
            	</div>
			</div>		
</div>

<div class="row mx-0" style="display: block;  background: #FFFFFF;">
	<div class="col-lg-12 main" id="main" style="background: #FFFFFF; float:left;">

	</div>
	<div class="col-lg-12 main" id="main-ausencias" style="background: #FFFFFF; float:left; display:none;">
			<div class="box box-info mx-0">
				<div class="box-header with-border" id="titulo">
					<h6 class="box-title"><small><strong>Gestión de ausencias</strong></small></h6>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<br>
							<h5><b>REGISTRAR AUSENCIA</b></h5>	
						</div>
						<!-- <div class="form-group col-sm-3">
								<select id="slc-ausencia" class="form-control" onChange="">
										
								</select>
						</div> -->

						<div class="form-group col-sm-3" id="div_slc-ausencia">
							<select id="slc-ausencia" class="form-control js-example-basic" data-live-search="true" onChange="">
								<option selected="true" disabled="disabled" value="">Seleccione Operador</option>
											
							</select>
						</div>
						<div class="col-xs-3">
							<input type="text" id="date_range" name="date_range" class="form-control fecha-ausencia" style="height:30px" autocomplete="off">
						</div>
						<div class="col-xs-3">
							<input type="text" id="motivo-ausencia" placeholder="Motivo de la ausencia" name="motivo-ausencia" class="form-control" style="height:30px" autocomplete="off">
						</div>
						<div class="col-xs-2">
							<button  type="button" class="btn btn-success" title="registrar" style="font-size: 12px; width: 100%;" id="registrar-ausencia" ><i class="fa fa-check"></i> REGISTRAR AUSENCIA</button>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12">
							<h5><b>AUSENCIAS PROGRAMADAS</b></h5>
							<hr style="border-top: 1px solid #ddd;">	
						</div>
						<div class="col-xs-12">
							<table data-page-length='10' align="center" id="tp_ausencias" class="hidden table table-bordered table=hover display" width="100%">
								<thead>
									<tr class="info">
										<th>Fecha Inicio</th>
										<th>Fecha Finalizacion</th>
										<th>Motivo</th>
										<th>Registrada por</th>
										<th>Fecha de Registro</th>
										<th>Estado</th>
										<th>Acciones</th>
									</tr>
								</thead>
								
							</table>
						</div>
					</div>
				</div>
			</div>
	</div>
	<div class="col-lg-12 main" id="main-horarios" style="background: #FFFFFF; float:left; display:none;">
			<div class="box box-info mx-0">
				<div class="box-header with-border" id="titulo">
					<h6 class="box-title"><small><strong>Gestión de Horarios</strong></small></h6>
				</div>
				<div class="col-xs-12">
					<div class="row">
						<div class="col-xs-12">
							<br>
							<h5><b>REGISTRAR HORARIO</b></h5>	
						</div>
						<div class="form-group col-sm-2">
							<span><h4>Operador</h4></span>
							<select id="slc_operadores_horario" class="form-control js-example-basic" data-live-search="true" onChange="" style="width:100%">
 								<?php foreach ($lista_operadores as $lista_operadore): ?>
									<option value="<?= $lista_operadore["idoperador"]?>"><?= $lista_operadore["nombre_apellido"]?></option>
								<?php endforeach; ?>			
							</select>
						</div>
						<div class="form-group col-sm-4" style="padding-left: 8em;">
							<div class="row">
								<div class="col-xs-2"><h4>Dia</h4></div>
								<div class="col-xs-4" style="padding-top: 0.7em"><input class="form-check-input" type="checkbox" onClick="toggle(this)"> Seleccionar Todos</div>

							</div>
							<div class="contenedor" style="display: flex; padding-top: 1em;" id="dayCheck">
								<?php $semanaDays=['lunes','martes','miercoles','jueves','viernes','sabado'];?>
								<?php foreach ($semanaDays as $semanaDay):?>
									<div class="form-check form-check" style="padding-right: 1em;">
										<input class="form-check-input diaSemana" name="<?= $semanaDay?>" type="checkbox" value="<?= $semanaDay?>">
										<span class="form-check-label"><?=$semanaDay?></span>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="form-group col-sm-4">
								<div class='col-sm-4'>
									<span><h4>Hora Entrada</h4></span>
									<div class='input-group timepicker'>
										<input type='text' class="form-control" id='datetime_entrada' placeholder="HORA ENTRADA"/>
										<div class="input-group-addon">
											<div class="fa fa-clock-o"></div>
										</div>
									</div>
								</div>
								<div class='col-sm-4'>
									<span><h4>Hora Salida</h4></span>
									<div class='input-group timepicker'>
										<input type='text' class="form-control" id='datetime_salida'placeholder="HORA SALIDA"/>
										<div class="input-group-addon">
											<div class="fa fa-clock-o"></div>
										</div>
									</div>
								</div>
								<div class='col-sm-2' id="cancelEditHorario" style="float: left; margin-top: 2.7em; display:none;">
									<a class="btn btn-danger"  data-estado="0" title="Cancelar Editar Horario" onclick="cancelEditHorario();"><i class="fa fa-ban"></i></a>
								</div>
						</div>
						<div class="form-group col-sm-2" style="padding-top: 1em;">
							<button  type="button" class="btn btn-success" title="registrar" style="font-size: 12px; width: 100%;" id="registrar-horario" ><i class="fa fa-check"></i> REGISTRAR HORARIO</button>
							<button  type="button" class="btn btn-success" title="actualizar" style="font-size: 12px; width: 100%; display:none" id="actualizar-horario" ><i class="fa fa-check"></i> ACTUALIZAR HORARIO</button>
						</div>

					</div>
				</div>
				<div id="datatable_horario " style="padding:2em">
					<?= $this->load->view('operadores/table_horarios_operadores', null, true); ?>   
				</div>
			</div>
	</div>
</div>

<div class="modal fade modal-ausencia" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">MODIFICAR AUSENCIA</h4>
      </div>
      <div class="modal-body">
		  <div class="row">
	  					<div class="col-xs-6">
							<h5>Nueva Fecha</h5>
							<input type="text" id="date_range-modal" name="date_range-modal" class="form-control fecha-ausencia" style="height:30px" autocomplete="off">
						</div>
						<div class="col-xs-6">
							<h5>Motivo</h5>
							<input type="text" id="motivo-ausencia-modal" placeholder="Motivo de la ausencia" name="motivo-ausencia-modal" class="form-control" style="height:30px" autocomplete="off">
						</div>
			</div>
      </div>
      <div class="modal-footer">
        
      </div>
    </div> 
  </div> 
</div> 
<script src="<?php echo base_url('assets/function.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/sweetalert2.js') ?>"></script>
<script src="<?php echo base_url('assets/js/operadores/operadores.js') ?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<script type="text/javascript">
    $('document').ready(function() {
		/*$('.btn.btn-app').click('on', function (params) {
			$('.main').html('');
		});*/
		
			$('#datetime_entrada').timepicker({
			showMeridian:false
			});
			$('#datetime_salida').timepicker({
			showMeridian:false
			});	


			$('.fecha-ausencia').val(moment().format('DD-MM-YYYY') +' | ' + moment().format('DD-MM-YYYY'));
		    
			$('.fecha-ausencia').daterangepicker({
				autoUpdateInput: false,
				"locale": 
				{
					"format": "DD-MM-YYYY",
					"separator": " | ",
					"applyLabel": "Guardar",
					"cancelLabel": "Cancelar",
					"fromLabel": "Desde",
					"toLabel": "Hasta",
					"daysOfWeek": ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
					"monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
					"firstDay": 1
				},
				startDate: moment(),
				endDate  : moment(),
				timePicker: false,
				}, function (start, end) 
				{
					$('#daterange-btn span').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'))
				}
			);

			$('.fecha-ausencia').on('apply.daterangepicker', function(ev, picker) 
			{
				$(this).val(picker.startDate.format('DD-MM-YYYY') + ' | ' + picker.endDate.format('DD-MM-YYYY'));
			});

			$('.fecha-ausencia').on('cancel.daterangepicker', function(ev, picker) 
			{ 
				$(this).val(picker.startDate.format('DD-MM-YYYY') + ' | ' + picker.endDate.format('DD-MM-YYYY')); 
			});
			$('#slc_operadores_horario').select2({
					placeholder: '.: Selecciona Operador :.',
					multiple: false
				});
			
	});
</script>
