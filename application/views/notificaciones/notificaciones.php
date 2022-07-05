<link rel="stylesheet" href="<?php echo base_url('assets/css/dualSelectList.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/fullcalendar/main.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>
<link rel="stylesheet" href="../assets/css/custom-gestion.css">

<style type="text/css">

	.box-body
	{
		padding: 0!important;
	}
    #fullcalendar {
        width: 100%;height: 100%;
    }
	#main-ausencias{
		padding-right: 10px!important;
		padding-left: 10px!important;
		margin-top: -133px!important;
	
	}
	#titulo{
		padding-left: 60px!important;
		padding-bottom: 20px!important;
	}

	#titulo > h6 > small > strong {
		margin-left: -40px!important;
		
	}

	#main-ausencias > div > div.col-xs-12 > div:nth-child(1) > div.col-xs-12
	{
		padding-left: 60px!important;
	}

	#main-ausencias > div > div.col-xs-12 > div:nth-child(2) > div:nth-child(1)
	{
		padding-left: 60px!important;
		padding-right: 60px!important;
	}

	#div_slc-ausencia{
		margin-left: 3%!important;
		
	}

    #main-ausencias > div > div.col-xs-12 > div:nth-child(1) > div.col-xs-12{
		margin-bottom: 1%;
	}

	div#main {
		height: 10px!important;
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

<div id="mostrarModal" class="modalAjuste" style="width:0%; height: 0%; margin-left: -15%; margin-top: 2%;">
    <div id="modalInfo"></div>
</div>

<div id="fadeMostrarModal" class="overlayAjuste" onclick = "limpiarAnalista();
        document.getElementById('mostrarModal').style.display = 'none';
        document.getElementById('fadeMostrarModal').style.display = 'none';
        $('#modalInfo').html('');
        $('input#gestionando').val('no');"></div>

<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
    <div class="col-md-12" align="center" id="divLiquidar" style="display: block; height: 100%;margin-top: 1%">
        <div class="box-header with-border" class="col-lg-12">
            <div class="col-lg-12" id="cuerpoCreditosBuscar" style="display: block">

                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="vistaGrupos();">
                        <span class="badge bg-red" id="sumatoria_campaña"><?php print_r($data['cant_camp_manuales']); ?></span>
                        <i class="fa fa-file"></i> Validacion textos
                    </a>
                </div>

                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="vistaPalabras();">
                        <span class="badge bg-red" id="cantidad_beneficiario"><?php echo $data['cant_beneficiarios']; ?></span>
                        <i class="fa fa-file"></i> Alertas Visado
                    </a>
                </div>
                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="vistaPalabras();">
                        <span class="badge bg-red" id="cantidad_beneficiario"><?php echo $data['cant_beneficiarios']; ?></span>
                        <i class="fa fa-file"></i> Alerta Indicadores
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

<div class="row mx-0" style="display: block;  background: #FFFFFF;">
	<div class="col-lg-12 main" id="main" style="background: #FFFFFF; float:left;display:none;">

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

<script type="text/javascript" src="<?php echo base_url('assets/function.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<script type="text/javascript" src="<?php echo base_url('assets/notificaciones/notificaciones.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/supervisores/select2Bootstrap.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/dualSelectListSkill.jquery.js');?>" ></script>
<script src="<?php echo base_url('assets/gestion/box_operadores_cobranzas.js') ?>"></script>
<script src="<?php echo base_url('assets/gestion/box_distribucion_cobranzas.js') ?>"></script>
<script src="<?php echo base_url('assets/js/sweetalert2.js') ?>"></script>

