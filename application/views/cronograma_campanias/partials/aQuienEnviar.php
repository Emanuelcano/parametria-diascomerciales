<script>
	<?php
	foreach ($filterValues as $const => $value) {
		if (strpos($const, 'CAMPAIGN') !== false) {
			echo "var $const = '$value';";
		}
	}
	?>
</script>

<div class="box box-success">
	<div class="box-header with-border">
		<h3 class="box-title">¿A quien enviar?</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-2">
				<div class="form-group">
					<label>Destino:</label>
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
						<select class="form-control input-sm" name="sl_destino" id="sl_destino">
							<option value="0" selected="selected">.:Seleccione:.</option>
							<?php foreach ($receivers as $item) { ?>
								<option value="<?=$item?>"><?=$item?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-8"></div>
			<div class="col-md-2 text-right">
				<button class="btn btn-info" id="testQuery">Probar Query <i class="fa fa-spinner fa-spin" id="spinnerTestQuery" style="display: none"></i></button>
			</div>
		</div>
		<div id="seccion_solicitantes" style="display: none">
			seccion solicitantes
		</div>
		<div id="seccion_clientes" style="display: none">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>Tipo de cliente:</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
							<select class="form-control input-sm" name="sl_clientType" id="sl_clientType">
								<option value="0" selected="selected">.:Seleccione:.</option>
								<?php foreach ($clientTypes as $item) { ?>
									<option value="<?=$item?>"><?=$item?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group" id="group_actions" style="display:none;">
						<label>Acción:</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
							<select class="form-control input-sm" name="sl_actions" id="sl_actions">
								<option value="0" selected="selected">.:Seleccione:.</option>
								<?php foreach ($actions as $item) { ?>
									<option value="<?=$item?>"><?=$item?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group" id="group_x_creditos" style="display:none;">
						<label>Clientes con x créditos:</label>
						<input class="form-control entero input-sm" type="number" id="x_creditos" name="x_creditos" autocomplete="off" min="1" oninput="validity.valid||(value='');">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Estado de su crédito</label>
						<div>
							<select class="form-control select2-multiple input-sm" name="sl_status[]" id="sl_status" multiple="multiple" style="width: 100%">
								<?php foreach ($status as $item) { ?>
									<option value="<?=$item?>"><?=$item?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>Filtros:</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-filter"></i></span>
							<select class="form-control input-sm" name="sl_filters" id="sl_filters">
								<option value="0" selected="selected">.:Seleccione:.</option>
								<?php foreach ($filters as $item) { ?>
									<option value="<?=$item?>"><?=$item?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Lógico:</label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-random"></i></span>
							<select class="form-control input-sm" name="sl_logics" id="sl_logics">
								<option value="0" selected="selected">.:Seleccione:.</option>
								<?php foreach ($logics as $item) { ?>
									<option value="<?=$item?>"><?=$item?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div id="valor_1_origen_desde" style="display:none;">
						<div class="col-md-6">
							<div class="form-group">
								<div class="form-group">
									<label>Origen desde:</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-random"></i></span>
										<select class="form-control input-sm" name="sl_origen_desde" id="sl_origen_desde">
											<option value="0" selected="selected">.:Seleccione:.</option>
											<?php foreach ($origins as $item) { ?>
												<option value="<?=$item?>"><?=$item?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Valor:</label>
								<input class="form-control entero input-sm" type="text" id="origen_desde_valor" name="origen_desde_valor" autocomplete="off">
							</div>
						</div>
					</div>
					<div id="valor_1_only" style="display:none;">
						<div class="form-group">
							<label>Valor:</label>
							<input class="form-control entero input-sm" type="text" id="valor_1" name="valor_1" autocomplete="off">
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div id="valor_2_origen_hasta" style="display:none;">
						<div class="col-md-6">
							<div class="form-group">
								<div class="form-group">
									<label>Origen hasta:</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-random"></i></span>
										<select class="form-control input-sm" name="sl_origen_hasta" id="sl_origen_hasta">
											<option value="0" selected="selected">.:Seleccione:.</option>
											<?php foreach ($origins as $item) { ?>
												<option value="<?=$item?>"><?=$item?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Valor:</label>
								<input class="form-control entero input-sm" type="text" id="origen_hasta_valor" name="origen_hasta_valor" autocomplete="off">
							</div>
						</div>
					</div>
					<div id="valor_2_only" style="display: none">
						<div class="form-group">
							<label>Valor:</label>
							<input class="form-control entero input-sm" type="text" id="valor_2" name="valor_2" autocomplete="off">
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="box-footer text-right">
		<button class="btn btn-primary" id="saveFilters"><i class="fa fa-save"></i> Guardar Filtros</button>
	</div>
	<div class="overlay" id="loadingAQuien" style="display: none">
		<i class="fa fa-refresh fa-spin"></i>
	</div>
</div>
