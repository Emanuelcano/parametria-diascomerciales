<style>
	body, #modulo-content, .content {
		background-color: #ecf0f5 !important;
	}

	/* custom inclusion of right, left and below tabs */

	.tabs-below > .nav-tabs,
	.tabs-right > .nav-tabs,
	.tabs-left > .nav-tabs {
		border-bottom: 0;
	}

	.tab-content > .tab-pane,
	.pill-content > .pill-pane {
		display: none;
	}

	.tab-content > .active,
	.pill-content > .active {
		display: block;
	}

	.tabs-below > .nav-tabs {
		border-top: 1px solid #ddd;
	}

	.tabs-below > .nav-tabs > li {
		margin-top: -1px;
		margin-bottom: 0;
	}

	.tabs-below > .nav-tabs > li > a {
		-webkit-border-radius: 0 0 4px 4px;
		-moz-border-radius: 0 0 4px 4px;
		border-radius: 0 0 4px 4px;
	}

	.tabs-below > .nav-tabs > li > a:hover,
	.tabs-below > .nav-tabs > li > a:focus {
		border-top-color: #ddd;
		border-bottom-color: transparent;
	}

	.tabs-below > .nav-tabs > .active > a,
	.tabs-below > .nav-tabs > .active > a:hover,
	.tabs-below > .nav-tabs > .active > a:focus {
		border-color: transparent #ddd #ddd #ddd;
	}

	.tabs-left > .nav-tabs > li,
	.tabs-right > .nav-tabs > li {
		float: none;
	}

	.tabs-left > .nav-tabs > li > a,
	.tabs-right > .nav-tabs > li > a {
		min-width: 74px;
		margin-right: 0;
		margin-bottom: 3px;
	}

	.tabs-left > .nav-tabs {
		float: left;
		margin-right: 19px;
		border-right: 1px solid #ddd;
	}

	.tabs-left > .nav-tabs > li > a {
		margin-right: -1px;
		-webkit-border-radius: 4px 0 0 4px;
		-moz-border-radius: 4px 0 0 4px;
		border-radius: 4px 0 0 4px;
	}

	.tabs-left > .nav-tabs > li > a:hover,
	.tabs-left > .nav-tabs > li > a:focus {
		border-color: #eeeeee #dddddd #eeeeee #eeeeee;
	}

	.tabs-left > .nav-tabs .active > a,
	.tabs-left > .nav-tabs .active > a:hover,
	.tabs-left > .nav-tabs .active > a:focus {
		border-color: #ddd transparent #ddd #ddd;
		*border-right-color: #ffffff;
	}

	.tabs-right > .nav-tabs {
		float: right;
		margin-left: 19px;
		border-left: 1px solid #ddd;
	}

	.tabs-right > .nav-tabs > li > a {
		margin-left: -1px;
		-webkit-border-radius: 0 4px 4px 0;
		-moz-border-radius: 0 4px 4px 0;
		border-radius: 0 4px 4px 0;
	}

	.tabs-right > .nav-tabs > li > a:hover,
	.tabs-right > .nav-tabs > li > a:focus {
		border-color: #eeeeee #eeeeee #eeeeee #dddddd;
	}

	.tabs-right > .nav-tabs .active > a,
	.tabs-right > .nav-tabs .active > a:hover,
	.tabs-right > .nav-tabs .active > a:focus {
		border-color: #ddd #ddd #ddd transparent;
		*border-left-color: #ffffff;
	}


</style>
<?php $this->load->view('supervisores/menu/menu_supervisores'); ?>
<div id="contenedorCampania">
	<div class="row">
		<div class="col-lg-12" id="view-new_campain">
			<div class="content">
				<div class="card">
					<div class="content">
						<div class="col-md-12">
							<?php $this->load->view('cronograma_campanias/partials/campaignDetails', ['campaignId' => $campaign['id_logica'] ,'proveedores' => $proveedores]); ?>
						</div>
						<div class="col-md-12">
							<?php $this->load->view('cronograma_campanias/partials/queEnviar.php', ['templates' => $whatsappTemplates]); ?>
						</div>
						<div class="col-md-12">
							<?php $this->load->view('cronograma_campanias/partials/aQuienEnviar.php', []); ?>
						</div>
						<div class="col-md-12">
<!--							--><?php //$this->load->view('cronograma_campanias/partials/cuandoEnviar.php', []); ?>
							<?php $this->load->view('cronograma_campanias/partials/cuandoEnviar2.php', ['campaignId' => $campaign['id_logica'], 'type' => $campaign["type_logic"]]); ?>
						</div>
						<div class="col-md-12">
							<?php $this->load->view('cronograma_campanias/partials/comoEnviar.php', []); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-lg-12" id="view-calendar">
		<div class="content">
			<div class="card card-calendar">
				<div class="content">
					<div id="fullCalendar"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="ModalView" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Detalle del Mensaje</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4 text-right"><strong>Tipo de Campaña</strong></div>
					<div class="col-md-8"><p id="modal-campain-type"></p></div>
					<div class="col-md-4 text-right"><strong>Fecha y hora programada</strong></div>
					<div class="col-md-8"><p id="modal-campain-date"></p></div>
					<div class="col-md-4 text-right"><strong>Mensaje enviado</strong></div>
					<div class="col-md-8"><p id="modal-campain-msg"></p></div>
					<input type="hidden" id="modal-id-msg-prog-date">
					<input type="hidden" id="modal-id-msg-prog">
					<input type="hidden" id="modal-id-campain">
					<input type="hidden" id="modal-canceled">
				</div>
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-md-6 text-left">
						<button class="btn btn-warning" id="modal-disable">Deshabilitar</button>
						<button class="btn btn-success" id="modal-enable">Habilitar</button>
					</div>
					<div class="col-md-6">
						<button type="button" class="btn btn-info" data-dismiss="modal" onclick="edit_campaing_modal($('#modal-id-campain').val())">Ver mas detalles</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	
	</div>
</div>



<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" style="width:850px; margin-left: -100px;">
			<form class="form-horizontal" name="frm_modalAdd" id="frm_modalAdd" method="POST" action="<?php echo base_url();?>api/ApiCampanias/guardarEvento">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Agendar Campañia</h4>
				</div>
				<div class="modal-body">
					
					<div class="form-group">
						
						<label for="title" class="col-sm-2 control-label">Titulo</label>
						<div class="col-sm-10">
							<input type="text" name="title" class="form-control" id="title" placeholder="Titulo">
						</div>
					
					</div>
					<div class="form-group">
						
						<label for="color" class="col-sm-2 control-label">Proveedores</label>
						<div class="col-sm-8">
							<select name="id_proveedor" class="form-control" id="id_pro">
								<?php  foreach ($this->session->flashdata('proveedores_rs') as $value) {?>
									<option value="<?php echo $value['id_proveedor'];?>" selected="selected"><?php echo $value['nombre_proveedor'];?></option>
								<?php }  ?>
							</select>
						</div>
						<div class="col-sm-2"><a href="#" id="btn_nuevo_pro" onclick="$('#ModalAddPro').modal('show')" class="btn btn-success" title="Agregar Nuevo Proveedor" ><i class="fa fa-plus"></i></a></div>
					
					</div>
					
					<div class="form-group">
						
						<label for="color" class="col-sm-2 control-label">Logicas (Querys)</label>
						<div class="col-sm-8">
							<select name="id_logica" class="form-control" id="id_log">
<!--								--><?php // foreach ($this->session->flashdata('logicas_rs') as $value) {?>
<!--									<option value="--><?php //echo $value['id_logica'];?><!--" selected="selected">--><?php //echo $value['id_logica'].".-".$value['nombre_logica'];?><!--</option>-->
<!--								--><?php //}  ?>
							</select>
						</div>
						<div class="col-sm-1"><a href="#" id="btn_viewlogic" class="btn btn-primary" title="Ver/Actualizar Logica" ><i class="fa fa-eye"></i></a></div>
						<div class="col-sm-1"><a href="#" id="btn_nuevalogica" onclick="nuevaLogica();" class="btn btn-success" title="Agregar Nueva Logica" ><i class="fa fa-plus"></i></a></div>
					
					</div>
					
					<div class="form-group">
						
						<label for="color" class="col-sm-2 control-label">Color</label>
						<div class="col-sm-10">
							<select name="color" class="form-control" id="color">
								<option value="">Seleccionar</option>
								<option style="color:#0071c5;" value="#0071c5">&#9724; Azul oscuro</option>
								<option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquesa</option>
								<option style="color:#008000;" value="#008000">&#9724; Verde</option>
								<option style="color:#FFD700;" value="#FFD700">&#9724; Amarillo</option>
								<option style="color:#FF8C00;" value="#FF8C00">&#9724; Naranja</option>
								<option style="color:#FF0000;" value="#FF0000">&#9724; Rojo</option>
								<option style="color:#000;" value="#000">&#9724; Negro</option>
							
							</select>
						</div>
					
					</div>
					<div class="form-group">
						
						<label for="start" class="col-sm-2 control-label">Fecha Inicial</label>
						<div class="col-sm-8">
							<input type="date" name="start" class="form-control" id="start">
						</div>
						<div class="form-group col-sm-2" id="div_hora_ini_campania">
                        
                        <span class='input-group timepicker' >
                          <input type='text' class="form-control" name='hora_ini_campania' id='hora_ini_campania' placeholder="HORA INICIO"/>
                          <span class="input-group-addon">
                            <span class="fa fa-clock-o"></span>
                          </span>
                        </span>
							<!-- <input type="text" maxlength="4" class="form-control" id="hora_ini_campania" placeholder="HORA INICIO" onkeypress="return solo_numeros(event)"></input> -->
						</div>
					
					</div>
					<div class="form-group">
						
						<label for="start" class="col-sm-2 control-label">Fecha final</label>
						<div class="col-sm-8">
							<input type="date" name="end" class="form-control datepicker" id="end">
						</div>
						<div class="form-group col-sm-2" id="div_hora_fin_campania">
                        
                        <span class='input-group timepicker' >
                          <input type='text' class="form-control" name='hora_fin_campania' id='hora_fin_campania' placeholder="HORA FINAL"/>
                          <span class="input-group-addon">
                            <span class="fa fa-clock-o"></span>
                          </span>
                        </span>
							<!-- <input type="text" maxlength="4" class="form-control" id="hora_ini_campania" placeholder="HORA INICIO" onkeypress="return solo_numeros(event)"></input> -->
						</div>
					
					</div>
					
					<div id="view_test_camp"></div>
				</div>
				<div class="modal-footer">
					<button type="button" id="" class="btn btn-info">Test template</button>
					<button type="button" id="btn_test_mensaje" class="btn btn-warning">Test Campañia</button>
					<button type="submit" class="btn btn-primary">Guardar</button>
					<button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	var slackNotificados = <?=json_encode($slackNotificados)?>
</script>
<script type="text/javascript" src="<?php echo base_url('assets/clock-timepicker/jquery-clock-timepicker.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/ddslick/jquery.ddslick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/taginput/bootstrap-tagsinput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/caret/jquery.caret.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo asset('assets/cronograma_campanias/edit.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo asset('assets/cronograma/cronograma.css'); ?>" />
<script type="text/javascript" src="<?php echo asset('assets/cronograma/cronograma.js'); ?>"></script>

