<div class="col-md-6" style="border-right: 1px solid #ddd">
	<input type="hidden" id="message_id">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title">Nuevo Mensaje</h3>
		</div>
		<div class="card-body">
			<div class="form-group">
				<label for="end" class="col-sm-2 control-label">Datos</label>
				<div class="col-sm-10">
					<select class="js-example-basic-multiple" name="criterios[]" id="selectVariablesMensaje" multiple="multiple" style="width: 100%" placeholder="Objetivo">
						<option value=0>.:Objetivos:.</option>
						<?php foreach ($logicas as $logica) { ?>
							<option
									value="<?=$logica->idrango?>"
									data-base_datos="<?=$logica->base_datos?>"
									data-tabla_primaria="<?=$logica->tabla_primaria?>"
									data-tabla="<?=$logica->tabla?>"
									data-campo="<?=$logica->campo?>"
									data-condicion="<?=$logica->condicion?>" ><?=$logica->denominacion?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<form name="frm_que_enviar" id="frm_que_enviar" class="form-group" role="form" method="POST">
				<div class="form-group">
					<label for="start" class="col-sm-2 control-label">Mensaje</label>
					<div class="col-sm-10">
						<input type="hidden" id="textarea_mensaje_last_position">
						<textarea name="mensaje" class="form-control" id="mensaje" rows="5"
								  required="required"></textarea>
						<form class="form-horizontal" name="frm_modalAddLog" id="frm_modalAddLog" method="POST" action="<?php echo base_url();?>api/ApiCampanias/guardarLogicas">
							<textarea name="query_contenido" class="form-control hide" id="query_contenido" rows="5" required="required"></textarea>
						</form>
					</div>
				</div>

				<div class="form-group">
					<label for="color" class="col-sm-2 control-label">Estado</label>
					<div class="col-sm-4">
						<select name="sl_estado_mensaje" id="sl_estado_mensaje" class="form-control">

							<option value="1" selected="selected">ACTIVO</option>
							<option value="0">INACTIVO</option>
						</select>
					</div>
					<div class="custom-control custom-checkbox">
						<input type="checkbox" class="custom-control-input" name="chk_predeterminado"
							   id="chk_predeterminado">
						<label class="custom-control-label" for="chk_predeterminado">Predeterminado</label>
					</div>

				</div>
				<div class="form-group text-right">
					<div class="col-sm-12">
						<button type="button" id="save_message" class="btn btn-primary"><i class="fa fa-save"></i>
							Guardar mensaje
						</button>
					</div>
				</div>
				<br>

			</form>
		</div>
	</div>


</div>
<div class="col-md-6">
	<div class="card">
		<div class="card-header">
			<h3 class="card-title">Mensajes</h3>
		</div>
		<!-- /.card-header -->
		<div class="card-body">
			<div class="row">
				<div class="" id="tb_mensajes">
					<table align="center" id="table_message"
						   class="table table-responsive table-striped table=hover display" width="100%">
						<thead style="font-size: smaller; ">
						<tr class="info">
							<th style="text-align: center;">Mensaje</th>
							<th style="text-align: center;">Estado</th>
							<th style="text-align: center;">Predeterminado</th>
							<th style="text-align: center;">Accion</th>
						</tr>
						</thead>
						<tbody style="font-size: 12px; text-align: center;">
						</tbody>
					</table>
				</div>


			</div>
		</div>
		<!-- /.card-body -->
	</div>
</div>
