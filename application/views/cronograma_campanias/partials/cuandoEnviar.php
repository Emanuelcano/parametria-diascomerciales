<div class="box box-success">
	<div class="box-header with-border">
		<h3 class="box-title">¿Cuando enviar?</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-6" style="padding-right: 0px">
				<div class="col-md-3">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12 text-center"><h4>&nbsp;</h4></div>
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-12 text-right" style="height: 57px"><h4>Hora</h4></div>
									<div class="col-md-12 text-right" style="height: 50px"><h4>Mensaje</h4></div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3" style="border-left: 1px solid #ddd;">
					<?php $this->load->view('supervisores/partials/dia_semana_cuando_enviar.php', ['day' => 'Lunes', 'weekend' => false]); ?>
				</div>
				<div class="col-md-3" style="border-left: 1px solid #ddd;">
					<?php $this->load->view('supervisores/partials/dia_semana_cuando_enviar.php', ['day' => 'Martes', 'weekend' => false]); ?>
				</div>
				<div class="col-md-3" style="border-left: 1px solid #ddd;">
					<?php $this->load->view('supervisores/partials/dia_semana_cuando_enviar.php', ['day' => 'Miercoles', 'weekend' => false]); ?>
				</div>
			</div>
			<div class="col-md-6" style="padding-left: 0px">
				<div class="col-md-3" style="border-left: 1px solid #ddd;">
					<?php $this->load->view('supervisores/partials/dia_semana_cuando_enviar.php', ['day' => 'Jueves', 'weekend' => false]); ?>
				</div>
				<div class="col-md-3" style="border-left: 1px solid #ddd;">
					<?php $this->load->view('supervisores/partials/dia_semana_cuando_enviar.php', ['day' => 'Viernes', 'weekend' => false]); ?>
				</div>
				<div class="col-md-3" style="border-left: 1px solid #ddd;">
					<?php $this->load->view('supervisores/partials/dia_semana_cuando_enviar.php', ['day' => 'Sabado', 'weekend' => true]); ?>
				</div>
				<div class="col-md-3" style="border-left: 1px solid #ddd;">
					<?php $this->load->view('supervisores/partials/dia_semana_cuando_enviar.php', ['day' => 'Domingo', 'weekend' => true]); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="overlay" id="loadingCuandoEnviar" style="display: none">
		<i class="fa fa-refresh fa-spin"></i>
	</div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal-mensajes-programados">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Mensaje programado</h4>
			</div>
			<div class="modal-body">
				<p id="modal-mensjes-body"></p>
			</div>
			<p>&nbsp;</p>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<style>

	.dd-container {
		margin-top: 10px;
	}

	.dd-options {
		width: 300px; !important;
		display: block;
	}

	.dd-desc {
		color: black;
	}

	.dd-container .collapse a, .collapse a {
		padding: 8px 15px 8px 15px; !important;
	}

	.dd-container .collapse a, .collapse a:hover {
		padding: 8px 15px 8px 15px; !important;
		background-color: rgb(238, 238, 238);
	}

	.no-float {
		display: table-cell;
		float: none;
	}

	.time {
		display: inline-block;
		font-size: 26px;
		padding: 5px;
		text-align: center;
		width: 100%;
		margin-top: 5px;
	}
</style>
