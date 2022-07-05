<link rel="stylesheet" href="<?php echo base_url('assets/css/dualSelectList.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/fullcalendar/main.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>
<link rel="stylesheet" href="../assets/css/custom-gestion.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/supervisores/select2Bootstrap.css'); ?>"/>
<style type="text/css">
	#main-ausencias
	{
		padding-right: 30px!important;
		padding-left: 30px!important;
		margin-top: -94px!important;
		
	}
    #fullcalendar 
	{
        width: 100%;height: 100%;
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



<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
    <div class="col-md-12" align="center" id="divLiquidar" style="display: block; height: 100%;margin-top: 3%">
        <div class="box-header" class="col-lg-12">
            <div class="col-lg-12" id="" style="display: block">

                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="vistaGestionObligatoria();">
                        
                        <i class="fa fa-headphones"></i> Solicitudes obligatorias
                    </a>
                </div>
                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="cargarVistaAusencias()">
                        <i class="fa fa-calendar"></i> Ausencias
                    </a>
                </div>
                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="listaOperadores()">
                        <i class="fa fa-users"></i> Operadores
                    </a>
                </div>
				<div class="box-body pull-left">
                    <a class="btn btn-app" onclick="cargarVistaTiempos()">
                        <i class="fa fa-whatsapp"></i> CHAT UAC
                    </a>
                </div>
				<div class="box-body pull-left">
                    <a class="btn btn-app" onclick="vistaGestionAsigAutomatica();">
                        
					<i class="fa fa-users"></i> Asig. Autom&aacute;tico
                    </a>
                </div>
                

            </div>
        </div>
    </div>
</div>

<div id="dashboard_principal">
    <section class="content">

        <div class="col-lg-12" id="main" style="display: block">

        </div>

        

    </section>
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
<script type="text/javascript">
    $('document').ready(function() {
			
			$('#slc-ausencia').change('on', function (params) {
				getAusencias();
			});
			$('#registrar-ausencia').click('on', function (params) {
				registrarAusencia();
			});
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

<script type="text/javascript" src="<?php echo base_url('assets/supervisores/ventas/ventas.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('assets/function.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/dualSelectListSkill.jquery.js');?>" ></script>
<script src="<?php echo base_url('assets/js/sweetalert2.js') ?>"></script>

