<style type="text/css">
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

	.accordion_gest_agendaemail {
		background-color: #d8d5f9;
		box-shadow: 0px 9px 10px -9px #888888;
		z-index: 1;
		cursor: pointer;
		width: 100%;
		border: none;
		outline: none;
		transition: 0.4s;
	}

	.accordion_gest_agendaemail:hover {
		background-color: #c8bef6;
	}

	.accordion_gest_agendaemail.active {
		background-color: #c8bef6;
	}

	.active.accordion_gest_agendaemail:after {
		content: "\2B9E";
	}

	.accordion_gest_agendaemail:after {
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

	#box_agenda_email th {
		font-weight: 400;
		text-align: center
	}

	#box_agenda_email td {
		font-weight: 700;
	}

	#box_agenda_email .popover {
		border: 0px;
		/* width: 400px; */
        max-width:600px;
	}

	#box_agenda_email .popover-title {
		background-color: #f7f7f7;
		font-size: 14px;
		color: inherit;
	}

	#box_agenda_email .popover-content {
		background-color: inherit;
		color: #333;
		padding: 10px;
		padding-left: 3px;
	}

	.div-table {
		display: table;
		box-sizing: content-box;
		width: 100%;
		border-spacing: 2px;
	}

	.div-table-row {
		display: table-row;
		width: auto;
		clear: both;
	}

	.div-table-col {
		float: left;
		display: table-column;
		border-bottom: 1px solid #e0dddd;
		cursor: pointer;
		font-size: 13px;
		padding: 8px;
		box-sizing: content-box;
		width: 98%;
		text-align: left;
	}

	.div-table-col:hover {
		background-color: #efefef;
	}

</style>

<!-- DIRECTORIO MAIL -->
<div id="box_agenda_email" class="box box-info">
	<div class="box-header with-border" id="titulo"></div>
	<input type="hidden" name="inp_age_documento" id="inp_age_documento" value="<?=$documento?>">
	<input type="hidden" name="inp_age_tipo_canal" id="inp_age_tipo_canal" value="<?=$tipo_canal?>">
	<div class="box-body" style="font-size: 12px;">
		<div class="container-fluid">
			<div class="row">
				<button class="col-sm-12 text-center accordion_gest_agendaemail ">
					<h4 class="title_btn_agendaemail">DIRECTORIO MAIL</h4>
				</button>
				<div class="panel_10 body_agendaemail" style="display:none;">
					<div class="container-fluid">
						<div class="row">
							<div class="box-body" style="font-size: 12px;">
								<div class="container-fluid grid-striped">
									<div class="col-md-12">&nbsp;</div>
									<button style="margin-bottom: 0.5em; float: right;" class="btn btn-success" id="agregarMailAgenda" onclick=var_agendaemail.modal_newmail()><i class="fa fa-plus"></i> AGENDAR</button>
									<table class="table table-striped table-bordered display" id="table_agenda_mail">
										<thead>
											<tr class="table-light">
												<th>Cuenta</th>
												<th>Contacto</th>
												<th>Fuente</th>
												<th>Antiguedad</th>
												<th>Estado</th>
												<th></th>
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
<!-- INICIO MODAL -->
<div class="modal fade" id="preview_mail_html" tabindex="-1" role="dialog" aria-labelledby="preview_mail_html">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
						aria-hidden="true">&times;</span></button>
				<div class="col-md-12 text-center"
					style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;">
					<h4>PRE-VISUALIZACION MAIL </h4>
				</div>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-success sendTemplateMail" onclick=var_agendaemail.sendEmail()><i class='fa fa-send'></i></button>

			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="box_add_mail_new" tabindex="-1" role="dialog" aria-labelledby="agendaLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header text-center">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
						aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="agendaLabel"></label></h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 text-center"
						style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;">
						<h4>AGENDAR CORREO</h4>
					</div>
					<div class="col-sm-12"><br></div>
					<div class="col-sm-12" id="form-tel">
						<div class="form-group col-sm-12">
							<label for="new-cuenta" class="col-form-label">Cuenta:</label>
							<input type="email" class="form-control" placeholder="example@dominio.com" id="new-cuenta-mail" onkeypress="return var_agendaemail.validateEmail(event)">
						</div>

						<div class="form-group col-sm-12">
							<label for="new-contacto" class="col-form-label">Contacto:</label>
							<input type="text" class="form-control" id="new-contacto-mail">
						</div>
						<div class="form-group col-sm-6">
							<label for="new-fuente-mail" class="col-form-label">Fuente:</label>
							<select class="form-control" id="new-fuente-mail">
								<option value="PERSONAL">Personal</option>
								<option value="REFERENCIA">Referencia</option>
								<option value="LABORAL">Laboral</option>
								<option value="BURO_CELULAR">Buro - Celular - D</option>
								<option value="BURO_CELULAR_T">Buro - Celular - T</option>
								<option value="BURO_LABORAL">Buro - Laboral - D</option>
								<option value="BURO_REFERENCIA">Buro - Referencia - D</option>
							</select>
						</div>
						<div class="form-group col-sm-6">
							<label for="new-estado-mail" class="col-form-label">Estado:</label>
							<select class="form-control" id="new-estado">
								<option value="1">Activo</option>
								<option value="0">Fuera de servicio</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-success" id="agendar_mail" onclick="var_agendaemail.agendarMail(<?= $documento ?>)" disabled><i class="fa fa-plus"></i> AGREGAR</button>
			</div>
		</div>
	</div>
</div>

<!-- FIN MODAL -->

<!-- FIN DIRECTORIO MAIL -->
