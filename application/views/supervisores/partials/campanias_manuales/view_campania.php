<div id="view" class="hide" style="text-align: center;">
	<div class="col-md-15" style="margin-bottom: 18px;">
		<div class="col-md">
			<div class="panel panel-warning text-center">
				<div class="panel-heading" style="text-align: left;font-size:17px;color: #6b6b6b;">CAMPAÑA: <b id='nombre_campania' style="text-transform: uppercase;"></b>
					<i class="fa fa-close icon_close" onclick="cerrar()" style="float: right; margin-right: 9px;top: -3px;"></i>
					<i title="Actualizar" class="fa fa-refresh" aria-hidden="true" onclick="actualizar()" style="float: right; margin-right: 9px;font-size: 27px;"></i>
					<i title="Buscar por fecha" class="fa fa-calendar" onclick="consultas_gestiones_crm('fecha')" aria-hidden="true" style="float: right; margin-right: 28px; font-size: 27px;"></i>
					<i title="Ver detalle campaña" class="fa fa-list-alt" id="detalleCampania" aria-hidden="true" style="float: right; margin-right: 28px; font-size: 27px; cursor: pointer"></i>
					<input type="hidden" id="ver_campania" value="">
				</div>
				<div class="panel-body" style="padding: 11px;font-size: 12px;font-weight: 600;">
					<div class="col-sm" >
						<div class="col-md-5">
							<div class="panel panel-warning text-center">
								<div class="panel-heading"><b style="color: #6b6b6b;">OPERADORES</b></div>
								<div class="panel-body" style="padding: 11px;font-size: 12px;font-weight: 600;">
									<div class="col-sm-8" >
										<select style="width: 100%;" class="form-control js-example-basic" data-live-search="true" onChange="" id="operadoresCampania" name="operadoresCampania">
										</select>
									</div>
									<div class="col-sm-4">
										<button type="button" class="form-control btn btn-info" onclick="operador_campania()">ACTIVAR</button>
									</div>
									<div class="col-sm" style="padding-top: 43px;">
										<table align="center" id="operadores_activos" class="table table-responsive table-striped table=hover display" width="100%" >
											<thead style="font-size: smaller; ">
											<tr class="info">
												<th>OPERADORES EN LA CAMPAÑA</th>
												<th>ESTADO</th>
												<th></th>
											</tr>
											</thead>
											<tbody style="font-size: 12px; text-align: center;" id="tb_body"></tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-7">
							<div class="col-md-3">
								<div class="panel panel-info text-center">
									<div class="panel-heading">
										<b style="color: #6b6b6b;font-size: 13px;">TOTAL CASOS</b>
										<a href="#" id="downloadTotalCasos"><i title="Descargar Total Casos" class="fa fa-cloud-download" style="color:gray; font-size: 2rem;float: right;"></i></a>
									</div>
									<div class="panel-body" style="padding: 11px;font-size: 21px;color: #6b6b6b;font-weight: 600;">
										<div class="col-sm">
											<div class="text-center">
												<div id="cantidad_campania"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="panel text-center" style="border-color: #c6e5fd;background-color: #c6e5fd;">
									<div class="panel-heading" style="border-color: #c6e5fd;"><b style="color: #6b6b6b; font-size: 13px;">EN GESTION</b></div>
									<div class="panel-body" style="padding: 11px;font-size: 21px;color: #6b6b6b;font-weight: 600;background-color:white;">
										<div class="col-sm">
											<div id="gestionando"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="panel panel-success text-center">
									<div class="panel-heading">
										<b style="color: #6b6b6b; font-size: 13px;">GESTIONADOS</b>
										<a href="#" id="downloadGestionados"><i title="Descargar Casos Gestionados" class="fa fa-cloud-download" style="color:gray; font-size: 2rem;float: right;"></i></a>
									</div>
									<div class="panel-body" style="padding: 11px;font-size: 21px;color: #6b6b6b;font-weight: 600;">
										<div class="col-sm">
											<div class="col-sm" id="gestionados_hoy"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="panel text-center" style="border-color: #e9dede; background-color: #e9dede;">
									<div class="panel-heading" style="border-color: #dfdede;"><b style="color: #6b6b6b;font-size: 13px;">SIN GESTION</b></div>
									<div class="panel-body" style="padding: 11px;font-size: 21px;color: #6b6b6b;font-weight: 600; background-color: white;">
										<div class="col-sm">
											<div class="col-sm-2" id="sin_gestion"></div>
											<div class="col-sm-4" id="porcen_sin_gestion" style="float: right;"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="panel text-center" style="background-color: #c4d8ea;border-color: #dfe5eb;">
									<div class="panel-heading" style="border-color: #dfe5eb;"><b style="color: #6b6b6b;font-size: 13px;">TIEMPO PROMEDIO</b></div>
									<div class="panel-body" style="padding: 11px;font-size: 21px;color: #6b6b6b;font-weight: 600; background-color:white;">
										<div class="col-sm">
											<div class="col-sm" id="tiempo_promedio"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="panel panel-success panel-success text-center">
									<div class="panel-heading"><b style="color: #6b6b6b;font-size: 13px;" id="nombre_mayor_gestion"></b>
										<i title="Operador con mas gestiones" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem;float: right;"></i>                                            </div>
									<div class="panel-body" style="padding: 11px;font-size: 21px;color: #6b6b6b;font-weight: 600;">
										<div class="col-sm">
											<div class="col-sm" id="cantidad_mayor_gestion"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="panel panel-danger text-center">
									<div class="panel-heading"><b style="color: #6b6b6b;font-size: 13px;" id="nombre_menor_gestion"></b>
										<i title="Operador con menos gestiones" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem;float: right;"></i>
									</div>
									<div class="panel-body" style="padding: 11px;font-size: 21px;color: #6b6b6b;font-weight: 600;">
										<div class="col-sm">
											<div class="col-sm" id="cantidad_menor_gestion"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="panel text-center" style="background-color: #ead1c4;border-color: #dfe5eb; visibility: hidden ">
									<div class="panel-heading" style="border-color: #dfe5eb;"><b style="color: #6b6b6b;font-size: 13px;">SOLO ES RELLENO </b></div>
									<div class="panel-body" style="padding: 11px;font-size: 21px;color: #6b6b6b;font-weight: 600; background-color:white;">
										<div class="col-sm">
											<div class="col-sm">0</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="panel text-center" style="background-color: #eac4e5;border-color: #dfe5eb;">
									<div class="panel-heading" style="border-color: #dfe5eb;"><b style="color: #6b6b6b;font-size: 13px;">TIEMPO RESTANTE</b></div>
									<div class="panel-body" style="padding: 11px;font-size: 21px;color: #6b6b6b;font-weight: 600; background-color:white;">
										<div class="col-sm">
											<div class="col-sm" id="tiempo_restante"></div>
										</div>
									</div>
								</div>
							</div>
							<!-- Busqueda por fecha/Operador -->
							<div class="modal fade" id="consulta_por_fecha" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title" id="myModalLabel"><b id="title_consulta"></b></h4>
										</div>
										<div class="modal-body">
											<div class="form-group" id="busqueda_por_fecha">
												<input type="date" required="true" class="form-control" id="gestion_fecha"/>
												<input  class="form-control btn btn-info" style="margin-top:2%;" onclick="consultar_gestion_fecha()" value="Buscar" />
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 text-center">
								<h5 class="bg-warning" style="padding: 6px 0px 12px;">
									<strong>
										GESTIONES POR HORA
									</strong>
								</h5>
								<canvas id="myChartLine_gestiones" width="15" height="3"></canvas>
							</div>
							<div class="col-md-12 text-center">
								<h5 class="bg-warning" style="padding: 6px 0px 6px;">
									<strong>
										TIPIFICACIONES POR HORA
									</strong>
								</h5>
								<canvas id="myChartLine_tipificaciones" width="15" height="3"></canvas>
							</div>
							<div class="col-md-12">
								<h3 class="box-title" style="float: left; margin: 9px 0px 6px 0px;"><small><strong>GESTIONES</strong></small>&nbsp;</h3>
								<table align="center" id="gestionados_crm" class="table table-responsive table-striped table=hover display" width="100%" >
									<thead style="font-size: smaller; ">
									<tr class="info">
										<th style="text-align: left;background-color: #c4d8ea;">Operador</th>
										<th style="text-align: left;background-color: #c4d8ea;">Asignados</th>
										<th style="text-align: left;background-color: #c4d8ea;">Gestionados</th>
										<th style="text-align: left;background-color: #c4d8ea;">Menor tiempo</th>
										<th style="text-align: left;background-color: #c4d8ea;">Mayor tiempo</th>
										<th style="text-align: left;background-color: #c4d8ea;">Tiempo descanso gestion</th>
										<th style="text-align: left;background-color: #c4d8ea;">Tiempo activo gestion</th>
										<th style="text-align: left;background-color: #c4d8ea;">Tiempo inactivo</th>
										<th style="text-align: left;background-color: #c4d8ea;">Tiempo promedio</th>
									</tr>
									</thead>
									<tbody style="font-size: 12px; text-align: center;" id="tb_body"></tbody>
									<tfoot align="right">
										<tr><th></th><th></th><th></th><th></th><th></th><th></th></tr>
									</tfoot>
								</table>
							</div>
							
							<div class="col-md-5">
								<h3 class="box-title" style="float: left; margin: 9px 0px 6px 0px;"><small><strong>TIPIFICACIONES</strong></small>&nbsp;</h3>
								<table align="center" id="tipificaciones_crm" class="table table-responsive table-striped table=hover display" width="100%" >
									<thead style="font-size: smaller; ">
									<tr class="info">
										<th style="text-align: left;background-color: #c4d8ea;">Tipo Contacto</th>
										<th style="text-align: left;background-color: #c4d8ea;">Respuesta</th>
										<th style="text-align: left;background-color: #c4d8ea;">Total</th>
									</tr>
									</thead>
									<tbody style="font-size: 12px; text-align: center;" id="tb_body"></tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
  $(document).ready(function() {
	$("#downloadGestionados").click(function() {
		let base_url = $("input#base_url").val();
		let idCampania = $("#ver_campania").val()
		window.open(base_url + 'api/campanias/downlaodCSVTotalCasosGestionados/' + idCampania, '_self');
	});
	
	$("#downloadTotalCasos").click(function() {
		let base_url = $("input#base_url").val();
		let idCampania = $("#ver_campania").val()
		window.open(base_url + 'api/campanias/downloadCSVTotalCasos/' + idCampania, '_self');	
	})
  });
</script>
<?php $this->load->view('supervisores/partials/campanias_manuales/modal_detalle_campania'); ?>


