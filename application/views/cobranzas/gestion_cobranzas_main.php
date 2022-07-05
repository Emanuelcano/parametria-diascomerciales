<link rel="stylesheet" href="<?php echo base_url('assets/css/custom-gestion.css');?>">
<link rel="stylesheet" href="<?php echo base_url('assets/css/Chart.min.css');?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>

<script src="<?php echo base_url('assets/gestion/gestion.js?'.microtime());?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/diccionario.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/Chart.min.js');?>" ></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
	<script src="<?php echo base_url('assets/js/cobranzas/cobranzas.js');?>"></script>
<style>
.cortar{
  text-overflow:ellipsis;
  white-space:nowrap; 
  overflow:hidden; 
}
.panel {
     margin-bottom: 0px; 
}
.panel-red{
	background: red;
}
.panel-green{
	background: #398439;
}
	
</style>
<input type="hidden" name="tipo_equipo" id="tipo_equipo" value="<?php echo $this->session->userdata('equipo'); ?>">
<input type="hidden" name="txt_id_credi" id="txt_id_credi" value="<?php if (isset($id_credito))echo $id_credito?>">
<input type="hidden" name="txt_render_v" id="txt_render_v" value="<?php if (isset($render_view))echo $render_view?>">
<?php 

	if (isset($render_view) && $render_view=="true") { 
	?>
	<script type="text/javascript" id="etiqueta">
		var id_credito = $("#txt_id_credi").val();
		var render_view = $("#txt_render_v").val();
		$("input#hdd_leyendo_caso").val("1");
		consultar_credito(id_credito,render_view);
	</script>
	<?php 
		$this->session->set_userdata('render_view',null);
		$this->session->set_userdata('id_credito',null);
	} 
?>
<script>
	 $(function () {
		
		<?php if(!empty($autollamada) and $autollamadaNumero != '') { ?>
	  		//momentaneo hasta que wolkvox solucione su problema
	  		if (sessionStorage.switch_valor == 'activo_neotell') {
				llamar();
			}else if (sessionStorage.switch_valor == 'activo_neotell_colombia') {
				llamar();
			} else if (sessionStorage.switch_valor == 'activo_twilio') {
                $("#startup-button").trigger("click");
                setTimeout(function(){
                    llamar();		
                }, 5000);
			}
		
			function llamar() {
				$("#icontoClose").trigger("click");
                if (sessionStorage.switch_valor == 'activo_neotell') {
				$("#txt_num_man").val('<?= str_replace('+','',PHONE_COD_COUNTRY) .  $autollamadaNumero?>');
				}else if (sessionStorage.switch_valor == 'activo_neotell_colombia') {
				$("#txt_num_man").val('<?= $autollamadaNumero?>');
                }else if (sessionStorage.switch_valor == 'activo_twilio') {
                    $("#txt_num_man").val('<?= PHONE_COD_COUNTRY .  $autollamadaNumero?>');
                }
				$("#btn_call").trigger("click");
			}
		<?php } ?>
	 });
</script>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>" data-tipo-operador="<?php echo $this->session->userdata('tipo_operador'); ?>">
<!-- Esto es para que el header no superponga el buscador -->
<div class="box-header with-border" class="col-lg-12"><div class="col-md-12">&nbsp;</div></div>
	<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
		<div class="box-header with-border col-lg-12" id="separador_cobranzas">
			
			<?php if ($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO ){ 
						if ($this->session->userdata['tipo_operador'] == 6 || $this->session->userdata['tipo_operador'] == 5 ) { ?>
							<div class="row desempenho text-center"  style="padding-top:10px;">
								<div class="col-md-4" id="gestionar_operador">
									<div class="panel panel-info text-center">
										<div class="panel-heading "><b>GESTIONAR CASOS POR OPERADOR</b></div>
										<div class="panel-body" style="padding: 11px;font-size: 12px;font-weight: 600;">
											<div class="col-sm-6" >
												<div class="col-md-13">
													<div class="panel panel-danger text-center">
														<div class="panel-heading "><b>PERIODO</b></div>	
														<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
															<select class="form-control" id="slc-periodo">
																<?php echo "<option value='01-03-2021' selected >01-03-2021</option>";
																	foreach ($periodos as $key => $fecha) {
																		echo "<option value='$fecha' >$fecha</option>";
																	} 
																	echo "<option value='30-12-2020'>30-12-2020</option>";
																	echo "<option value='15-12-2020'>15-12-2020</option>";
																?>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<a class="btn btn-primary" style="width: 100%;margin-bottom: 7px;" id="consultar-gestion">APLICAR</a>
												<br>
												<select style="width: 100%!important;" class="form-control js-example-basic" data-live-search="true" onChange="" id="slc-operadores" name="operadores">
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-4" id="ver_campania_manual">
									<div class="panel panel-warning text-center">
										<div class="panel-heading "><b>CAMPAÑAS MANUALES</b></div>
										<div class="panel-body" style="padding: 11px;font-size: 12px;font-weight: 600;">
											<div class="col-sm-7" >
												<div class="col-md-13"> 
													<div class="panel panel-danger text-center">
														<div class="panel-heading "><b id="campania">SELECCIONE LA CAMPAÑA</b></div>	
														<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
															<select class="form-control" id="slc_campania">
																<option value="0"> .: Seleccione Campaña :.</option>
																<?php foreach ($campanias as $campania) { ?>
																	<option
																			value="<?=$campania['id']?>"
																			<?php if (isset($campaniaActiva['id']) and $campaniaActiva['id'] == $campania['id']) { ?> selected="selected" <?php } ?>
																			data-auto="<?=$campania['automatico']?>">
																		<?=$campania['descripcion']?>
																	</option>
																<?php } ?>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-4">
												<a class="btn btn-primary disabled" style="width: 100%;" id="activar_campania" onclick="activarCampaniaManual()">ACTIVAR</a>
												<a class="btn hide" style="width: 100%; background-color: #e48e2b;color:white;" id="salir_campania" onclick="salirCampaniaManual(function() {location.reload()})">SALIR DE CAMPAÑA</a>
												<a class="btn hide" style="width: 100%; margin-top: 3%; background-color: #c1c1c1;color:white" id="descanso_campania" onclick="descanso_campania_manual(function() {})">DESCANSO</a>
												<a class="btn hide bg-green" style="width: 100%; margin-top: 3%;" id="reactivar_campania" onclick="reactivarCampaniaManual()">REACTIVAR</a>

												<br>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-4" id="acuerdos_contenido">
									<div class="panel panel-success text-center">
										<div class="panel-heading "><b>ACUERDOS</b></div>
										<div class="panel-body" style="padding: 11px;font-size: 12px;font-weight: 600;">
											<div class="col-sm-7" >
												<div class="col-md-13"> 
													<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
														<select class="form-control" id="buscar_acuerdos_periodos">
															<option value="" select></option>
															<option value="1">VENCEN HOY</option>
															<option value="2">VENCEN MAÑANA</option>
															<option value="3">VENCEN EN DOS DIAS</option>
															<option value="4">VENCIERON AYER</option>
															<option value="5">VENCIERON HACE DOS DIAS</option>
															<option value="6">VENCIERON HACE TRES DIAS</option>
															<option value="7">VENCIERON HACE CUATRO DIAS</option>
															<option value="8">VENCIERON HACE CINCO DIAS</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-sm-4">
												<a class="btn btn-primary disabled" id="buscar_acuerdos" style="width: 100%;">APLICAR</a>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row desempenho text-center" style="width: 102%;">
								<div class="col-md-12" style="margin-top: 7px;"> 
									<div class="panel panel-warning text-center" >
										<div class="panel-heading" ><b>GESTION</b><b id="titulo_gestion"></b></div>
										<div class="panel-body " style="padding: 5px;font-size: 12px;font-weight: 600;">
											<div class="row">
												<div class="col-md-1" style="border-right:1px solid #faebcc;">
													<div class="panel">
														<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
															ACUERDOS: <br>
															<a id="url_excel" href="#"><span style="font-size: 2.3em; padding-top:2px" id="acuerdos_alcanzados_anterior">0</span></a>
														</div>
													</div>
												</div>
												<div class="col-md-1" style="border-right:1px solid #faebcc;">
													<div class="panel">
														<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
															CUMPLIDOS: <br>
															<span style="font-size: 2.3em; padding-top:2px" id="acuerdos_cumplidos_anterior">0</span>
														</div>
													</div>
												</div>
												<div class="col-md-2" style="border-right:1px solid #faebcc;">
													<div class="panel">
														<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;margin-left: -13px;">
															MORA 2-45: <br><span style="font-size: 1.3em;" id="suma_acuerdos_quincena_anterior_0_40">&nbsp;</span>
														</div>
														<div class="panel-body panel-green" id="mora-13-60" style="margin: 0px 2px 0px -12px;padding: 5px;font-size: 1.3em;font-weight: 600;justify-content: center;">
															<div style = "color: white;" id="acuerdos_0_40">&nbsp;</div>
														</div>
													</div>
												</div>
												<div class="col-md-2" style="border-right:1px solid #faebcc;">
													<div class="panel">
														<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
															MORA 46-120: <br><span style="font-size: 1.3em;"  id="suma_acuerdos_quincena_anterior_41_90">&nbsp;</span>
														</div>
														<div class="panel-body  panel-red" id="mora-61-120" style="margin: 0px 2px 0px 2px;padding: 5px;font-size: 1.3em;font-weight: 600;justify-content: center;">
															<div style = "color: white;" id="acuerdos_61_120">&nbsp;</div>
														</div>
													</div>
												</div>
												<div class="col-md-2" style="border-right:1px solid #faebcc;">
													<div class="panel">
														<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
															MAYOR 120: <br><span style="font-size: 1.3em;" id="suma_acuerdos_quincena_anterior_91_120">&nbsp;</span>
														</div>
														<div class="panel-body  panel-red" id="mora-121-180" style="margin: 0px 2px 0px 2px;padding: 5px;font-size: 1.3em;font-weight: 600;justify-content: center;">
															<div style = "color: white;" id="acuerdos_121_180">&nbsp;</div>	
														</div>
													</div>
												</div>
												
												<div class="col-md-2" >
													<div class="panel">
														<div class="panel-body" style="height: 67px;padding: 5px;font-size: 12px;font-weight: 600;border-right:1px solid #faebcc;">
															COMISION: <br><span style="font-size: 2.5em;"id="suma_comision"></span>
														</div>
													</div>
												</div>
												<div class="col-md-2" >
													<div class="panel">
														<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
															<a class="btn btn-success" style="width: 100%;margin-bottom: -4px;margin-top: -6px;" id="quincena_actual">ACTUAL</a>
														</div>
														<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
															<a class="btn btn-primary" style="width: 100%;margin-bottom: -6px;" id="quincena_anterior">ANTERIOR</a>
														</div>
													</div>
												</div>
											</div>
											
										</div>
									</div>
								</div>
							</div>
						<?php }
						
							if ($this->session->userdata['tipo_operador'] == 1 || $this->session->userdata['tipo_operador'] == 2 || $this->session->userdata['tipo_operador'] == 4 || $this->session->userdata['tipo_operador'] == 9 || $this->session->userdata['tipo_operador'] == 13 ) { ?>
								<div class="row desempenho text-center" style="padding-top:10px;">
									<div class="col-md-3"> 
										<div class="panel panel-info text-center">
											<div class="panel-heading "><b>EMPLEADOS</b></div>
											<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
												<div class="col-sm-4">VENDIDOS<p class="vendidos-empleados">--</p></div>
												<div class="col-sm-4" style="border-right: 2px solid #bce8f1; border-left: 2px solid #bce8f1">MORA <p class=" mora-empleados">--</p></div>
												<div class="col-sm-4">% <p class=" porcent-empleados">--</p></div>
											</div>
										</div>
									</div>
									<div class="col-md-3 " style="padding-left:0px;">
										<div class="panel panel-warning text-center">
											<div class="panel-heading " style=""><b>INDEPENDIENTES</b></div>
											<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
												<div class="col-sm-4" style="padding-left: 0px;" >VENDIDOS <p class="vendidos-independientes">--</p></div>
												<div class="col-sm-4" style="border-right: 2px solid #faebcc; border-left: 2px solid #faebcc">MORA <p class=" mora-independientes">--</p></div>
												<div class="col-sm-4">%<p class=" porcent-independientes">--</p></div>
											</div>
										</div>
									</div>
									<div class="col-md-2 ">
										<div class="panel panel-success text-center">
											<div class="panel-heading "><b>MORA DEL PERIODO</b></div>
											<div class="panel-body" style="padding: 5px;font-size: 24px;font-weight: 600;">
												<b><p class="mora-periodo">--</p></b>
											</div>
										</div>
									</div>
									<div class="col-md-2">
										<div class="panel panel-danger text-center">
											<div class="panel-heading"><b>PERIODO</b></div>	
											<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
												<select class="form-control" id="slc-periodo">
													<?php
													/**
													 * TO DO : Arreglar periodos de cobranza, en la variable lastDayOfMonth esta set 30 por defecto, debe ser dinamico
													 * para contemplar meses como febrero y años bisiestos 
													 */
													echo "<option value='01-03-2021' selected >01-03-2021</option>";
													foreach ($periodos as $key => $fecha) {
														echo "<option value='$fecha' >$fecha</option>";
													} 
													echo "<option value='30-12-2020'>30-12-2020</option>";
													echo "<option value='15-12-2020'>15-12-2020</option>";
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-2"style="padding-left:0px; width: 11%;padding-left: 0px;padding-right: 4px;">
										<a class="btn btn-primary" style="width: 100%;margin-bottom: 14px;" id="consultar-gestion">APLICAR</a>
										<br>
										<?php //if ($this->session->userdata['tipo_operador'] == 6 || $this->session->userdata['tipo_operador'] == 2 || $this->session->userdata['tipo_operador'] == 9 || $this->session->userdata['tipo_operador'] == 13 ) {?>
											<select style="width: 100%!important;" class="form-control js-example-basic" data-live-search="true" onChange="" id="slc-operadores" name="operadores">
											</select>
										<?php //} ?>
									</div>
								</div>
							<?php } ?>
							<?php if ($this->session->userdata['tipo_operador'] == 1 || $this->session->userdata['tipo_operador'] == 2 || $this->session->userdata['tipo_operador'] == 4 || $this->session->userdata['tipo_operador'] == 9 || $this->session->userdata['tipo_operador'] == 13 ) { ?>
								<div class="col-md-3 desempenho" style="padding-left: 0px;margin-top: 1%;">
									<div class="panel panel-success text-center">
										<div class="panel-heading "><b>ACUERDOS</b></div>
										<div class="panel-body" style="padding: 11px;font-size: 12px;font-weight: 600;">
											<div class="col-sm-7" style="">
												<div class="col-md-13" style=""> 
													<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
													<?php if($this->session->userdata['tipo_operador'] == 2 || $this->session->userdata['tipo_operador'] == 9 || $this->session->userdata['tipo_operador'] == 13 ){ ?>
															<select class="form-control js-example-basic" data-live-search="true" onChange="" id="acuerdos-operadores" name="operadores">
															</select>
													<?php } ?>
														<select class="form-control" id="buscar_acuerdos_periodos" style="margin-top: 5%;">
															<option value="" select></option>
															<option value="1">VENCEN HOY</option>
															<option value="2">VENCEN MAÑANA</option>
															<option value="3">VENCEN EN DOS DIAS</option>
															<option value="4">VENCIERON AYER</option>
															<option value="5">VENCIERON HACE DOS DIAS</option>
															<option value="6">VENCIERON HACE TRES DIAS</option>
															<option value="7">VENCIERON HACE CUATRO DIAS</option>
															<option value="8">VENCIERON HACE CINCO DIAS</option>
														</select>
													</div>
												</div>
											</div>
											<div class="col-sm-5">
												<a class="btn btn-primary disabled" id="buscar_acuerdos" style="width: 100%;">APLICAR</a>
											</div>
										</div>
									</div>
								</div>
							<?php }
								 if ($this->session->userdata['tipo_operador'] == 2 || $this->session->userdata['tipo_operador'] == 9 || $this->session->userdata['tipo_operador'] == 13 ) { ?>
							<br>
								<div class="row desempenho text-center" style="margin-left: 1px;width: 100%;">
									<div class="col-md-7" style="padding-left:0px;">
										<div class="panel panel-warning text-center">
											<div class="panel-heading " style=""><b>OPERADOR</b></div>
												<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
													<div class="col-md-2">
														<div class="panel panel-info">
															<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
																GESTIONES: <br><span id="gestiones">0</span>
															</div>
														</div>
													</div>
													<div class="col-md-2">
														<div class="panel panel-warning">
															<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
																ACUERDOS: <span id="acuerdos"> 0 <br><i class="fa fa-arrow-right"></i> $0 </span>
															</div>
														</div>
													</div>
													<div class="col-md-2">
														<div class="panel panel-success">
															<div class="panel-body" style="padding:5px;font-size: 12px;font-weight: 600;">
																CUMPLIDOS: <span id="cumplidos">0 <br><i class="fa fa-arrow-right"></i> $0 </span>
															</div>
														</div>
													</div>
													<div class="col-md-2">
														<div class="panel panel-danger">
															<div class="panel-body" style="padding: 5px;font-size: 12px;font-weight: 600;">
																INCUMPLIDOS: <span id="incumplidos">0 <br><i class="fa fa-arrow-right"></i> $0 </span>
															</div>
														</div>
													</div>
													<div class="col-md-2" id="scl-busqueda" style="width: 20%;">
														<input type="text" id="date_range" name="date_range" class="form-control" style="height:30px" autocomplete="off">
													</div>
													<div class="col-md-1" id="busqueda">
														<button  type="button" class="btn btn-info" title="Buscar" style="font-size: 12px; width: 100%;" onClick="getGestionesDesempeño()"><i class="fa fa-search"></i></button>
													</div>
												</div>
											</div>
										</div>
									</div>
							<?php }
			} ?>
			<div class="col-md-12">
				<?php if($this->session->userdata['tipo_operador'] == 6 ||  $this->session->userdata['tipo_operador'] == 5){ ?>
					<div id="creditos">
						<?php if (!isset($render_view)) $this->load->view('cobranzas/section_search_creditos'); ?>
					</div> 
					<div class="col-md-12">
						<div id="result_table" class="hide">
							<table align="center" id="asignados" class="table table-responsive table-striped table=hover display" width="100%" >
								<thead style="font-size: smaller; ">
									<tr class="info">
										<th></th>
										<th style="text-align: left;">Ultima Gestion</th>
										<th style="text-align: left;">N°</th>
										<th style="text-align: left;">Documento</th>
										<th style="text-align: left;">Cliente</th>
										<th style="text-align: left;">Monto Prestado</th>
										<th style="text-align: left;">Fecha Vencimiento</th>
										<th style="text-align: left;">Deuda al Dia</th>
										<th style="text-align: left;">Estado</th>
										<th style="text-align: left;">Gestion</th>
									</tr>
								</thead>
								<tbody style="font-size: 12px; text-align: center;"></tbody>
							</table>
						</div>
					</div>
				<?php }else{ ?>
					<?php if (!isset($render_view)) $this->load->view('cobranzas/section_search_creditos'); ?>
				<?php } ?>
			</div>
		</div>
		<div id="texto" class="col-lg-12" style="display: block; background: #EBEDEF;"></div>
	    <div id="texto_agenda" class="col-lg-1"></div>
	</div>
	
	<script type="text/javascript">
		var abrirCasoAutomatico = false;
		var estadoOperador  = '<?=$estadoOperador?>';
				
		$('document').ready(function () {

			<?php if (!empty($campaniaActiva)) {
			if ($estadoOperador == 'descanso') { ?>
					dashboardEnDescanso();
			<?php } else {
				if ($campaniaActiva['automatico'] == 1) { ?>
						abrirCaso(<?=$abrirCasoAutomatico?>);
				<?php } else { ?>
						dashboardCampaniaAsignada();
				<?php }
				}
			} else { ?>
					dashboardSinCampaniaAsignada();
			<?php } ?>
		  
			$("#slc_campania").change(function() {
				if ($(this).val() !== '0') {
					$("#activar_campania").removeClass("disabled");
				}else{
					$('#activar_campania').addClass('disabled');
				}
			});
			
			<?php if(!$tieneCampania) { ?>
				activarServerSideEvent();
			<?php } ?>
			
			document.getElementById("fecha").innerHTML = 'Fecha Vencimiento';
			document.getElementById("deuda_dia").innerHTML = 'Deuda al Día';

			$('#date_range').val(moment().format('DD-MM-YYYY') + ' | ' + moment().format('DD-MM-YYYY'));

			$('#date_range').daterangepicker({
					autoUpdateInput: false,
					ranges:
						{
							'Hoy': [moment(), moment()],
							'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
							'Últimos 7 Días': [moment().subtract(6, 'days'), moment()],
							'Últimos 30 Días': [moment().subtract(29, 'days'), moment()],
							'Mes Anterior': [moment().startOf('month'), moment().endOf('month')],
							'Últimos Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
						},
					"locale":
						{
							"format": "DD-MM-YYYY",
							"separator": " | ",
							"applyLabel": "Guardar",
							"cancelLabel": "Cancelar",
							"fromLabel": "Desde",
							"toLabel": "Hasta",
							"customRangeLabel": "Personalizar",
							"daysOfWeek": ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
							"monthNames": ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
							"firstDay": 1
						},
					startDate: moment(),
					endDate: moment(),
					timePicker: false,
				}, function (start, end) {
					$('#daterange-btn span').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'))
				}
			);

			$('#date_range').on('apply.daterangepicker', function (ev, picker) {
				$(this).val(picker.startDate.format('DD-MM-YYYY') + ' | ' + picker.endDate.format('DD-MM-YYYY'));
			});

			$('#date_range').on('cancel.daterangepicker', function (ev, picker) {
				$(this).val('');
			});

			getGestionesDesempeño();
			getGestionesConsultor();
			let tipo = $("#id_operador").data('tipo-operador');
			if (tipo == 1 || tipo == 2 || tipo == 9 || tipo == 13) {
				buscarCreditoCobranza(null, $("#slc-periodo").val(), null, null);
			}
			$("#consultar-gestion").click('on', function () {
				document.getElementById("fecha").innerHTML = 'Fecha Vencimiento';
				document.getElementById("deuda_dia").innerHTML = 'Deuda al Día';

				$('#creditos').removeClass('hide');
				getGestionesConsultor();
				buscarCreditoCobranza(null, $("#slc-periodo").val(), $("#slc-operadores").val(), null);
			});


			$("#buscar_acuerdos_periodos").click('on', function () {
				if ($('#buscar_acuerdos_periodos').val() > 0) {
					if (tipo == 2 || tipo == 9 || tipo == 13) {
						if ($('#acuerdos-operadores').val() != null) {
							$('#buscar_acuerdos').removeClass("disabled");
						}
					} else {
						$('#buscar_acuerdos').removeClass("disabled");
					}
				} else {
					$('#buscar_acuerdos').addClass("disabled");
				}
			})
			$("#buscar_acuerdos").click('on', function () {
				if ($('#buscar_acuerdos_periodos').val() > 0) {
					if (tipo == 2 || tipo == 9 || tipo == 13) {
						if ($('#acuerdos-operadores').val() != null) {
							buscar_acuerdos_crm($('#acuerdos-operadores').val());
						}
					} else {
						buscar_acuerdos_crm();
					}
					$("#result_table").addClass('hide');
					$("#section_search_credito #result").show();
					document.getElementById("fecha").innerHTML = 'Fecha acuerdo';
					document.getElementById("deuda_dia").innerHTML = 'Monto Acuerdo';
				} else {
					Swal.fire("Seleccione un periodo", "Debe seleccionar un valor en el campo Periodo", "warning");
				}
			});
			if (tipo == 5 || tipo == 6) {

				$("#quincena_actual").click('on', function () {
					document.getElementById("titulo_gestion").innerHTML = ' QUINCENA ACTUAL';
					document.getElementById("titulo_gestion").style.color = '#00a65a';
					consultar_tablero_acuerdos($("#id_operador").val(), 'actual');
				});
				$("#quincena_anterior").click('on', function () {
					document.getElementById("titulo_gestion").innerHTML = ' QUINCENA ANTERIOR';
					document.getElementById("titulo_gestion").style.color = 'rgb(59 193 209)';
					consultar_tablero_acuerdos($("#id_operador").val(), 'anterior');
				});


				$("#section_search_credito #form_search").on('submit', function (event) {
					event.preventDefault();
					if ($("#slc-criterio").val() == "" || $("#slc-criterio").val() == null || $("#search").val().trim() == "") {
						Swal.fire("Campos Incompletos", "Debe ingresar un valor en el campo de busqueda y definir el criterio bajo el cual se realizará la misma ", "warning");
					} else {
						document.getElementById("fecha").innerHTML = 'Fecha Vencimiento';
						document.getElementById("deuda_dia").innerHTML = 'Deuda al Día';
						$("#result").removeClass('hide');
						$("#creditos").removeClass('hide');
						$("#result_table").addClass('hide');
					}
				});
				$("#section_search_credito #form_search").on('reset', function (event) {
					$("#result_table").removeClass('hide');
					$("#result").addClass('hide');
				});

			}
			let operadores = [];
			let opciones = '<option disabled value="" selected>Seleccione un operador</option>';
			$.ajax({
				url: base_url + 'atencion_cobranzas/cobranzas/get_operadores',
				type: 'POST',
				dataType: 'json',
				success: function (response) {
					if (typeof (response) != 'undefined') {
						operadores = response;
						operadores.forEach(item => {
							opciones += '<option value="' + item.idoperador + '">' + item.nombre_apellido + '</option>';
						});
						$('#slc-operadores').html(opciones);
						$('#slc-operadores').select2({
							placeholder: '.: Selecciona Operador :.',
							multiple: false
						});
						$('#acuerdos-operadores').html(opciones);
						$('#acuerdos-operadores').select2({
							placeholder: '.: Operador :.',
							multiple: false
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: 'No se cargaron operadores',
						});
					}
				}
			});
		})

		function abrirCaso(idCredito) {
			consultar_credito(idCredito, true);
		}
		
		/**
		 * Asigna al operador a la campania, le cambia el estado a activo y le asigna casos
		 */
		function activarCampaniaManual() {
			let btnActivarCampania = $("#activar_campania");
			btnActivarCampania.html('<i class="fa fa-spin fa-spinner"></i>');
			btnActivarCampania.addClass('disabled');

			let base_url = $("#base_url").val();
			$.ajax({
				url: base_url + 'api/campanias/activarCampania',
				type: 'POST',
				data: {
					"id_campania": $('#slc_campania').val(),
					"id_operador": $('#id_operador').val()
				},
			})
				.done(function (response) {
					if (response.status.ok) {
						$('#result_table').removeClass('hide');
						location.reload();
					} else {
						Swal.fire("¡Información!",
							"¡No quedan casos por gestionar!",
							"info");
					}
				})
				.fail(function (response) {
					Swal.fire({
						title: "¡Ups!",
						text: 'Error inesperado',
						icon: 'error'
					});
				})
				.always(function() {
					btnActivarCampania.removeClass('disabled');
					$("#activar_campania").html('ACTIVAR');
				})
		}

		function dashboardEnDescanso() {
			var table_search = $('#table_search').DataTable();
			table_search.clear().draw();
			table_search.destroy();
			$('#gestionar_operador').addClass('hide');
			$('#buscar_acuerdos_periodos').prop('disabled', false);
			$('#buscar_acuerdos').addClass("disabled");
			$("#slc_campania").prop("disabled", true);
			$('#reactivar_campania').removeClass('hide');
			$('#salir_campania').removeClass('hide');
			$('#activar_campania').addClass('disabled');
			$('#activar_campania').addClass('hide');
			$('#result_table').addClass('hide');
			$('#descanso_campania').addClass('hide');
			var table = $('#asignados').DataTable();
			$('#ver_campania_manual').removeClass('col-md-4');
			$('#ver_campania_manual').addClass('col-md-6');
			table.clear().draw();
			table.destroy();
		}

		function dashboardSinCampaniaAsignada() {
			$("#section_search_credito #result").hide();
			$('#gestionar_operador').removeClass('hide');
			$('#buscar_acuerdos_periodos').prop('disabled', false);
			$('#buscar_acuerdos').addClass("disabled");
			$("#slc_campania").prop("disabled", false);
			$('#activar_campania').removeClass('hide');
			$('#reactivar_campania').addClass('hide');
			$('#salir_campania').addClass('hide');
			$('#descanso_campania').addClass('hide');
			$('#result_table').addClass('hide');
			$('#ver_campania_manual').removeClass('col-md-6');
			$('#ver_campania_manual').addClass('col-md-4');
		}

		function dashboardCampaniaAsignada() {
			$("#section_search_credito #result").hide();
			$('#activar_campania').addClass('hide');
			$('#gestionar_operador').addClass('hide');
			$('#buscar_acuerdos_periodos').prop('disabled', true);
			$('#buscar_acuerdos').addClass("disabled");
			$("#slc_campania").prop("disabled", true);
			$('#activar_campania').addClass('disabled');
			$('#salir_campania').removeClass('hide');
			$('#descanso_campania').removeClass('hide');
			$('#result_table').removeClass('hide');
			$('#reactivar_campania').addClass('hide');
			$('#ver_campania_manual').removeClass('col-md-4');
			$('#ver_campania_manual').addClass('col-md-6');
			getCasosAsignadosDetallados();
		}
		
		
		
	</script>
