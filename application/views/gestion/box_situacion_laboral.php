<style>
	a[data-title]:hover:after {
		opacity: 1;
		transition: all 0.1s ease 0.5s;
		visibility: visible;
	}

	a[data-title]:after {
		content: attr(data-title);
		background-color: #000000c9;
		color: #f4f4f4;
		position: absolute;
		padding: 7px;
		white-space: nowrap;
		box-shadow: 1px 1px 3px #222222;
		opacity: 0;
		z-index: 1;
		height: 30px;
		visibility: hidden;
		left: 20px;
		bottom: -6px
	}

	a[data-title] {
		position: relative;
		float: right;
	}

	.texto-success {
		color: green;
	}

	.texto-warning {
		color: red;
	}

	.texto-danger {
		color: grey;
	}

	.accordion_gest_st_ila,
	.accordion_gest_st_ilarus,
	.accordion_gest_st_ilMareigua,
	.accordion_gest_st_ilt,
	.accordion_gest_st {
		background-color: #d8d5f9;
		box-shadow: 0px 9px 10px -9px #888888;
		z-index: 1;
		cursor: pointer;
		width: 100%;
		border: none;
		outline: none;
		transition: 0.4s;
	}

	.accordion_gest_st:hover {
		background-color: #c8bef6;
	}

	.accordion_gest_st.active {
		background-color: #c8bef6;
	}
	.active.accordion_gest_st_ila:after,
	.active.accordion_gest_st_ilarus:after,
	.active.accordion_gest_st_ilMareigua:after,
	.active.accordion_gest_st_ilt:after,
	.active.accordion_gest_st:after {
		content: "\2B9E";
	}
	.accordion_gest_st_ila:after,
	.accordion_gest_st_ilarus:after,
	.accordion_gest_st_ilMareigua:after,
	.accordion_gest_st_ilt:after,
	.accordion_gest_st:after {
		content: "\2B9F";
		color: black;
		font-weight: bold;
		float: right;
		margin-top: -2em;
	}

	.panel_10 {
		background-color: white;
	}
	.active_panel {
		display: block;
	}
	.gs_laboral {
		background-color: #e0dff5;
	}
	#box_gestion_laboral th {
		font-weight: 400;
		text-align : center
	}
	#box_gestion_laboral td {
		font-weight: 700;
	}	
	#box_gestion_laboral td.st_numero {
		text-align: center;
	}
	
	#box_gestion_laboral td.st_monto {
		text-align: right;
	}


</style>

<!-- <input id="tipo_operador" type="hidden" value="<?//=$this->session->userdata("tipo_operador");?>"> -->
<!-- INFORMACION LABORAL -->
<div id="box_gestion_laboral" class="box box-info">
	<?php $t_oper = $this->session->userdata('tipo_operador'); ?>
	<div class="box-header with-border" id="titulo"></div>
	<div class="box-body" style="font-size: 12px;">
		<input type="hidden" id="inp_sl_documento" name="documento" value=<?=$solicitude['documento']?>>
		<div class="container-fluid">
			<div class="row">		
				<button class="col-sm-12 text-center accordion_gest_st active">
					<h4 class="title_button_versitlab">LABORAL</h4>
				</button>
				<div class="panel_10" style="display:block;">
					<div class="container-fluid">
						<div class="row">
							<!-- Declarado -->
							<div class="col-md-8" style="margin-top: 10px"> 
								<div id="box_client_data" class="box box-info">
									<div class="box-header with-border gs_laboral" id="titulo">
										<div class="row">
											<div class="col-md-12">
												<div class="col-md-12" style="text-align: center;">
													<strong>DECLARADO</strong>
												</div>
											</div>
										</div>
									</div>
									<div class="box-body" style="font-size: 12px;">
										<div class="container-fluid grid-striped">
											<table class="table table-striped table-bordered display">
												<thead>
													<tr class="table-light" style="width:100px">
														<th style="width: 15%;">SITUACION</th>
														<th style="width: 35%;">EMPRESA</th>
														<th style="width: 35%;">CARGO</th>
														<th style="width: 15%;">INGRESO DECLARADO</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><strong><?= !empty($solicitude['nombre_situacion'])?$solicitude['nombre_situacion']:'-'; ?></strong></td>
														<td>
															<?php
																if (isset($analisis['antiguedad_laboral'])){
																	$antiguedad_laboral = $analisis['antiguedad_laboral'];
																} else {
																	$antiguedad_laboral = NULL;
																}
																if ($solicitude['id_situacion_laboral'] != 4){
																	$id_sl = $solicitude['id_situacion_laboral'];
																	if ($id_sl == 7 || $id_sl == 1) {
																		echo '<strong id="empresa_cargo">'.(isset($ref_personal[0]['nombres_apellidos'])?$ref_personal[0]['nombres_apellidos']:'').'</strong>';
																	} else if(!empty($analisis['razon_social_aportante'])){
																		echo '<strong style="color: blue;">'.$analisis['razon_social_aportante'].'</strong>';
																	} else if($antiguedad_laboral == 0){
																		echo '<strong style="color: red;">BURO SIN INF LABORAL</strong>';
																	} else {
																		echo '-';
																	}
																} else {
																	if(isset($solicitude['actividad'])){
																		echo '<strong style="color: blue;">'.$solicitude['actividad'].'</strong>';
																	}
																	if(isset($solicitude['actividad_direccion'])){
																		echo '<strong style="color: blue;">'.$solicitude['actividad_direccion'].'</strong>';
																	}
															  }
															?>
														</td>
														<td>
															<?php
																if ($solicitude['id_situacion_laboral'] != 4){
																	$id_sl = $solicitude['id_situacion_laboral'];
																	if ($id_sl == 7 || $id_sl == 1) {
																		echo (isset($ref_personal[0]['empresa_cargo'])?$ref_personal[0]['empresa_cargo']:'-');
																	} 
																} else {
																	echo '-';
																}
															?>
														</td>
														<td class='st_monto'><?='$ '.number_format($solicitude['ingreso_mensual'],0,",",".");?></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>	 
							<!-- Fin Declarado -->

							<!-- Indicadores -->
							<div class="col-md-4" style="margin-top: 10px"> 
								<div id="box_client_data" class="box box-info">
									<div class="box-header with-border gs_laboral" id="titulo">
										<div class="row">
											<div class="col-md-12">
												<div class="col-md-12" style="text-align: center;">
													<strong>INDICADORES</strong>
												</div>
											</div>
										</div>
									</div>
									<div class="box-body" style="font-size: 12px;">
										<div class="container-fluid grid-striped">
											<table class="table table-striped table-bordered display">
												<thead>
													<tr class="table-light">
														<th>ESTIMADO</th>
														<th>REAL</th>
														<th>Antigüedad</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class='st_monto'>
															<?='$ '.number_format($analisis['ingreso_estimado'],0,",",".");?> 
														</td>
														<td class='st_monto'>
															<?='$ '.number_format($analisis['ingreso_real_reciente'],0,",",".");?> 
														</td>
														<td class='st_numero'>
															<?=number_format($analisis['antiguedad_laboral'],0,",",".");?> 
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>	 
							<!-- Fin Indicadores -->

							<div class="col-md-12">
								
								<div class="nav-tabs">
									<ul class="nav nav-tabs" style="padding-left: 15px; font-size: 14px; font-weight: 600;  ">
										<li class="active"><a href="#tab_marigua" role="tab"  data-toggle="tab">M</a></li>
										<li><a href="#tab_experian" role="tab" data-toggle="tab">E</a></li>
										<li><a href="#tab_arus" role="tab" data-toggle="tab">A</a></li>
										<li><a href="#tab_transunion" role="tab" data-toggle="tab">T</a></li>
									</ul>
									<div class="tab-content">
										<div class="tab-pane active" id="tab_marigua">											
											<div class="col-md-12" style="margin-top: 25px">
												<div id="box_client_data" class="box box-info">
													<div class="box-header with-border gs_laboral" id="titulo">
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-12" style="text-align: center;">
																	<strong>INFORMACION LABORAL M</strong>
																	<?php if ($t_oper == 2 || $t_oper == 9 || $t_oper == 11 ): ?>
																	<button style="float: right;" id="btn_sit_lab_mareigua" class="btn btn-link btn-xs" data-servicio="mareigua" onClick="var_sit_lab.update_inf_laboral(this)">
																		<i class="fa fa-refresh" style="font-size: 18px; color: #00c0ef; cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Actualizar Información laboral de Mareigua"></i></button>
																	<?php endif ?>
																</div>
															</div>
														</div>
													</div>
													<div class="box-body" style="font-size: 12px;">
														<div class="container-fluid grid-striped">
															<table class="table table-striped table-bordered display" id="table_sl_ILMareigua" style="weight : 100%">
																<thead>
																	<tr class="table-light">
																		<th>EPS</th>
																		<th>AFP</th>
																		<th>OCUPACION</th>
																		<th>ROTACION</th>
																		<th>MENOR SALARIO</th>
																		<th>MAYOR SALARIO</th>
																		<th>SALARIO PROMEDIO</th>
																		<th>FECHA CONSULTA</th>
																	</tr>
																</thead>
															</table>
														</div>
													</div>
												</div>
											</div>
											
											<div class="col-md-10 col-md-offset-1" style="margin-top: 10px">
												<div id="box_client_data" class="box box-info">
													<div class="box-header with-border" id="titulo">
														<div class="row">
															<div class="col-md-12">
																<button class="col-sm-12 text-center accordion_gest_st_ilMareigua">
																	<h4 class="title_button_versitlab_ilMareigua">VER APORTES</h4>
																</button>
															</div>
														</div>
													</div>
													<div class="box-body body_versitlab_ilMareigua" style="font-size: 12px; display:none;">
														<div class="container-fluid grid-striped">
															<table class="table table-striped table-bordered display" id="table_sl_ILMareigua_aportes" style="width:100%">
																<thead>
																	<tr class="table-light">
																		<th>PERIODO</th>
																		<th>NIT</th>
																		<th>EMPRESA</th>
																		<th>SALARIO</th>
																	</tr>
																</thead>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane" id="tab_experian">
											<div class="col-md-12" style="margin-top: 25px">
												<div id="box_client_data" class="box box-info">
													<div class="box-header with-border gs_laboral" id="titulo">
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-12" style="text-align: center;">
																	<strong>INFORMACION LABORAL E</strong>
																	<?php if ($t_oper == 2 || $t_oper == 9 || $t_oper == 11 ): ?>
																	<button style="float: right;" id="btn_sit_lab_experian" class="btn btn-link btn-xs" data-servicio="experian" onClick="var_sit_lab.update_inf_laboral(this)">
																		<i class="fa fa-refresh" style="font-size: 18px; color: #00c0ef; cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Actualizar Información laboral de Experian"></i></button>
																	<?php endif ?>
																</div>
															</div>
														</div>
													</div>
													<div class="box-body" style="font-size: 12px;">
														<div class="container-fluid grid-striped">
															<table class="table table-striped table-bordered display"  id="table_sl_ILE" style="width:100%">
																<thead>
																	<tr class="table-light">
																		<th>EPS</th>
																		<th>AFP</th>
																		<th>OCUPACION</th>
																		<th>ROTACION</th>
																		<th>MENOR SALARIO</th>
																		<th>MAYOR SALARIO</th>
																		<th>SALARIO PROMEDIO</th>
																		<th>FECHA CONSULTA</th>
																	</tr>
																</thead>
															</table>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-10 col-md-offset-1" style="margin-top: 10px">
												<div id="box_client_data" class="box box-info">
													<div class="box-header with-border" id="titulo">
														<div class="row">
															<div class="col-md-12">
																<button class="col-sm-12 text-center accordion_gest_st_ila ">
																	<h4 class="title_button_versitlab_ila">VER APORTES</h4>
																</button>
															</div>
														</div>
													</div>
													<div class="box-body body_versitlab_ila" style="font-size: 12px; display:none;">
														<div class="container-fluid grid-striped">
															<table class="table table-striped table-bordered display" id="table_sl_ILE_aportes" style="width:100%">
																<thead>
																	<tr class="table-light">
																		<th>PERIODO</th>
																		<th>NIT</th>
																		<th>EMPRESA</th>
																		<th>SALARIO</th>
																	</tr>
																</thead>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane" id="tab_arus">
											<div class="col-md-12" style="margin-top: 25px">
												<div id="box_client_data" class="box box-info">
													<div class="box-header with-border gs_laboral" id="titulo">
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-12" style="text-align: center;">
																	<strong>INFORMACION LABORAL A</strong>
																	<?php if ($t_oper == 2 || $t_oper == 9 || $t_oper == 11 ): ?>
																	<button style="float: right;" id="btn_sit_lab_arus" class="btn btn-link btn-xs" data-servicio="arus" onClick="var_sit_lab.update_inf_laboral(this)">
																		<i class="fa fa-refresh" style="font-size: 18px; color: #00c0ef; cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Actualizar Información laboral de Arus"></i></button>
																	<?php endif ?>
																</div>
															</div>
														</div>
													</div>
													<div class="box-body" style="font-size: 12px;">
														<div class="container-fluid grid-striped">
															<table class="table table-striped table-bordered display" id="table_sl_ILArus" style="width:100%">
																<thead>
																	<tr class="table-light">
																		<th>EPS</th>
																		<th>AFP</th>
																		<th>OCUPACION</th>
																		<th>ROTACION</th>
																		<th>MENOR SALARIO</th>
																		<th>MAYOR SALARIO</th>
																		<th>SALARIO PROMEDIO</th>
																		<th>FECHA CONSULTA</th>
																	</tr>
																</thead>

															</table>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-10 col-md-offset-1" style="margin-top: 10px">
												<div id="box_client_data" class="box box-info">
													<div class="box-header with-border" id="titulo">
														<div class="row">
															<div class="col-md-12">
																<button class="col-sm-12 text-center accordion_gest_st_ilarus">
																	<h4 class="title_button_versitlab_ilarus">VER APORTES</h4>
																</button>
															</div>
														</div>
													</div>
													<div class="box-body body_versitlab_ilarus" style="font-size: 12px; display:none;">
														<div class="container-fluid grid-striped">
															<table class="table table-striped table-bordered display" id="table_sl_ILArus_aportes" style="width:100%">
																<thead>
																	<tr class="table-light">
																		<th>PERIODO</th>
																		<th>NIT</th>
																		<th>EMPRESA</th>
																		<th>SALARIO</th>
																	</tr>
																</thead>
															</table>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="tab-pane" id="tab_transunion">
											<div class="col-md-12" style="margin-top: 25px"> 
												<div id="box_client_data" class="box box-info">
													<div class="box-header with-border gs_laboral" id="titulo">
														<div class="row">
															<div class="col-md-12">
																<div class="col-md-12" style="text-align: center;">
																	<strong>INFORMACION LABORAL T</strong>
																</div>
															</div>
														</div>
													</div>
													<div class="box-body" style="font-size: 12px;">
														<div class="container-fluid grid-striped">
															<table class="table table-striped table-bordered display" id="table_sl_ILT" style="width:100%">
																<thead>
																	<tr class="table-light">
																		<th>EPS</th>
																		<th>AFP</th>
																		<th>OCUPACION</th>
																		<th>ROTACION</th>
																		<th>MENOR SALARIO</th>
																		<th>MAYOR SALARIO</th>
																		<th>SALARIO PROMEDIO</th>
																		<th>FECHA CONSULTA</th>
																	</tr>
																</thead>
																<tbody>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>	 
												<div class="col-md-10 col-md-offset-1" style="margin-top: 10px"> 
													<div id="box_client_data" class="box box-info">
														<div class="box-header with-border" id="titulo">
															<div class="row">
																<div class="col-md-12">
																	<button class="col-sm-12 text-center accordion_gest_st_ilt ">
																		<h4 class="title_button_versitlab_ilt">VER APORTES</h4>
																	</button>
																</div>
															</div>
														</div>
														<div class="box-body body_versitlab_ilt" style="font-size: 12px; display:none;">
															<div class="container-fluid grid-striped">
																<table class="table table-striped table-bordered display" id="table_sl_ILT_aportes" style="width:100%">
																	<thead>
																		<tr class="table-light">
																			<th>PERIODO</th>
																			<th>NIT</th>
																			<th>EMPRESA</th>
																			<th>SALARIO</th>
																		</tr>
																	</thead>
																	<tbody>
																	</tbody>
																</table>
															</div>
														</div>
													</div>
												</div>	 
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- FIN INFORMACION LABORAL -->
