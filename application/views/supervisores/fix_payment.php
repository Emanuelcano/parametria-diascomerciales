<style></style>
<div class="row fixPago">
	<div class="col-md-12 search-section-0">
		<div id="section_search_credito_from" style="background: #FFFFFF; margin-top:10px;">
			<form id="form_search_0" class="row col-md-9" method="POST">
				<div class="form-group row">
					<label for="search" class="col-sm-12 control-label ">Buscar cliente con pago mal imputado</label>
					<div class="col-sm-6">
						<input id="search" name="search" type="text" class="form-control" placeholder="Documento">
					</div>
					<button type="submit" class="btn btn-info col-sm-1" title="Buscar" style="font-size: 12px;"><i
							class="fa fa-search"></i> Buscar</button>
					<button type="reset" class="btn btn-default col-sm-1" title="Limpiar" style="font-size: 12px;"><i
							class="fa fa- fa-remove"></i> Limpiar</button>
				</div>

			</form>
		</div>
	</div>

	<div class="col-md-12 ajustes0" style="display:none;">
		<div id="box_client_title" data-id_cliente="" class="box box-info">
			<div class="box-header" id="titulo"
				style="padding:0;background-color: #fffdfa;box-shadow: 0px 9px 10px -9px #888888;">
				<div class="row">

					<div class="col-md-4 text-center">
						<h4><i class="fa fa-user"></i>
							<span id="nombre-cliente"> </span>
						</h4>
					</div>
					<div class="col-md-2 text-center">
						<h4><i class="fa fa-id-card"></i>
							<span id="documento-cliente"> </span>
						</h4>
					</div>
					<div class="col-md-2 text-center">

						<h4><i class="fa fa-phone"></i>
							<span id="telefono-cliente"> </span>
						</h4>
					</div>
					<div class="col-md-3 text-center">
						<h4><i class="fa fa-envelope"></i>
							<span id="mail-cliente"> </span>
						</h4>
					</div>
					<div class="col-md-1 text-center">
						<a id="close_credito" class="btn btn-danger btn-xs"
							title="Cerrar y continuar gestionando otro crédito"
							style="border-radius:50%;margin-top: 7px;"><i class="fa fa-close"></i></a>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-12">
			<table class="table table-striped table-bordered text-center" id="pagos-cliente-from">
				<thead style="background: #cccaf4" style="background: #cccaf4">
					<th></th>
					<th>Credito</th>
					<th>Medio</th>
					<th>Referencia Externa</th>
					<th>REFERENCIA Interna</th>
					<th>Fecha pago</th>
					<th>Monto</th>
					<th>Estado</th>
					<th>Resultado</th>
				</thead>
				<tbody>
				</tbody>
			</table>

		</div>
	</div>


	<div class="col-md-12 search-section-1" style="display:none;">
	
		<div class="col-md-12" ><hr></div>
		<div id="section_search_credito_to" style="background: #FFFFFF; margin-top:10px;">
			<form id="form_search_1" class="row col-md-9" method="POST">
				<div class="form-group row">
					<label for="search" class="col-sm-12 control-label ">Cliente a imputar pago</label>
					<div class="col-sm-6">
						<input id="search" name="search" type="text" class="form-control" placeholder="Documento">
					</div>
					<button type="submit" class="btn btn-info col-sm-1" title="Buscar" style="font-size: 12px;"><i
							class="fa fa-search"></i> Buscar</button>
					<button type="reset" class="btn btn-default col-sm-1" title="Limpiar" style="font-size: 12px;"><i
							class="fa fa- fa-remove"></i> Limpiar</button>
				</div>

			</form>			
		</div>
	</div>


	<div class="col-md-12 ajustes1" style="display:none; padding_top: 50px;">	
		<div id="box_client_title" data-id_cliente="" class="box box-info">
			<div class="box-header" id="titulo"
				style="padding:0;background-color: #fffdfa;box-shadow: 0px 9px 10px -9px #888888;">
				<div class="row">

					<div class="col-md-4 text-center">
						<h4><i class="fa fa-user"></i>
							<span id="nombre-cliente"> </span>
						</h4>
					</div>
					<div class="col-md-2 text-center">
						<h4><i class="fa fa-id-card"></i>
							<span id="documento-cliente"> </span>
						</h4>
					</div>
					<div class="col-md-2 text-center">

						<h4><i class="fa fa-phone"></i>
							<span id="telefono-cliente"> </span>
						</h4>
					</div>
					<div class="col-md-3 text-center">
						<h4><i class="fa fa-envelope"></i>
							<span id="mail-cliente"> </span>
						</h4>
					</div>
					<div class="col-md-1 text-center">
						<a id="close_credito" class="btn btn-danger btn-xs"
							title="Cerrar y continuar gestionando otro crédito"
							style="border-radius:50%;margin-top: 7px;"><i class="fa fa-close"></i></a>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-12">
			<p><h4>El o los pagos seleccionados a reasignar se van a distribuir en los siguientes créditos según su estado</h4></p>
		</div>
		<div class="col-sm-12">
			<table class="table table-striped table-bordered text-center" id="pagos-cliente-to">
				<thead style="background: #cccaf4" style="background: #cccaf4">
					<th>CREDITO</th>
					<th>OTORGADO</th>
					<th>VENCIMIENTO</th>
					<th>A PAGAR</th>
				</thead>
				<tbody>
				</tbody>
			</table>

		</div>

		<div class="col-sm-2 col-md-offset-10">
			<button type="button" class="btn btn-success" id="btnProcesar">Procesar</button>
			<button type="button" class="btn btn-default" id="btnCancelar">Cancelar</button>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/supervisores/fix_payment.js'); ?>"></script>
