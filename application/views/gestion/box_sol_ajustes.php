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

	.accordion_gest_sa {
		background-color: #d8d5f9;
		box-shadow: 0px 9px 10px -9px #888888;
		z-index: 1;
		cursor: pointer;
		width: 100%;
		border: none;
		outline: none;
		transition: 0.4s;
	}

	.accordion_gest_sa:hover {
		background-color: #c8bef6;
	}

	.accordion_gest_sa.active {
		background-color: #c8bef6;
	}
	.active.accordion_gest_sa:after {
		content: "\2B9E";
	}
	.accordion_gest_sa:after {
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
	#box_sol_ajustes th {
		font-weight: 400;
		text-align : center
	}
	#box_sol_ajustes td {
		font-weight: 700;
	}	
	#box_sol_ajustes td.st_numero {
		text-align: center;
	}
	
	#box_sol_ajustes td.st_monto {
		text-align: right;
	}


</style>

<div id="box_sol_ajustes" class="box box-info">
	<div class="box-header with-border" id="titulo"></div>
	<div class="box-body" style="font-size: 12px;">
		<div class="container-fluid">
			<div class="row">		
				<button class="col-sm-12 text-center accordion_gest_sa active">
					<h4 class="title_button_sol_ajuste">AJUSTES</h4>
				</button>
				<div class="body_sol_ajuste" style="display:block;">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12" style="margin-top: 10px"> 
								<div id="box_client_data" class="box box-info">
									<div class="box-body" style="font-size: 12px;">
									
										<div class="col-md-12" id="form_datos">
											<table class="table modificable " id="table-agenda-telefono">
												<thead>
													<th>TIPO</th>
													<th>CLASE</th>
													<th>DESCRIPCION AJUSTE</th>
													<th></th>
												</thead>
												<tbody>
													<tr>
														<td class="col-md-1">														
															<select class="form-control input-sm" name="ajus_tipo" id="ajus_tipo" >
																<option value="none" selected disabled hidden>Seleccione</option>
																<?php
																	foreach ($solicitud_ajustes as $key => $value){
																		echo '<option value="'.$value->id.'" >'.$value->descripcion.'</option>';
																	}
																?>		
															</select>
														</td>
														<td class="col-md-4">
															<select class="form-control input-sm" name="ajus_clases" id="ajus_clases">
																<option value="none" selected disabled hidden>Seleccione un tipo de ajuste</option>
															</select>
															<div class="hidden" id="ajus_requisitos">																
																<!-- <span>requisitos:<br></span>
																<span>  * no se<br></span>
																<span>  * quien sabe<br></span> -->
															</div>
														</td>
														<td class="col-md-6">
															<input id="ajus_descripcion" name="ajus_descripcion" type="text" class="form-control input-sm" >
														</td>
														<td class="col-md-1">
															<button id="btn_save_comment" class="btn btn-success align-bottom" onclick="var_sol_ajustes.procesar()">Procesar</button>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
										
										<div class="col-md-12" id="form_datos">
											<table class="table modificable " id="table-solicitudes-ajustes" style="width : 100%">
												<thead>
													<th>FECHA SOLICITUD</th>
													<th>ID SOLICITUD</th>
													<th>Operador Solicitante</th>
													<th>TIPO</th>
													<th>Clase</th>
													<th>Comentario</th>
													<th>Estado</th>
													<th>Fecha Procesado</th>
													<th>Procesado por operador</th>
													<th>Observaciones</th>
													<th>resultado</th>
													<th></th>
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
