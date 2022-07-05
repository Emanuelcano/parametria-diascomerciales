<title>Solventa - <?php echo $title; ?></title>
<span class="hidden-xs">
	<?php

	$usuario     = $this->session->userdata("username");
	$tipoUsuario = $this->session->userdata("tipo");
	?>
</span>

<?php //echo base_url()."assets/template/dist/img/loading.gif"; ?>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">

<span class="hidden-xs">
	<?php
	/*if (empty($this->session->userdata("sesion"))) {
		redirect(base_url() . "auth/logout");
    }*/


	$usuario     = $this->session->userdata("username");
	$tipoUsuario = $this->session->userdata("tipo");
	?>
</span>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario;?>">

<aside id="controlador_reporte" style="padding-left: 0.5%;padding-top: 3%;">
	<section class="content" style="min-height: 0px;">
		<div class="row">
			<div class="col-md-4">
				<div class="card card-primary">
<!--				<div class="card-header">-->
<!--					<h3 class="card-title">Seleccione la fecha del reporte que desea generar</h3>-->
<!--				</div>-->
					<div class="card-body">
							<label>Seleccione la fecha del reporte que desea generar:</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								<input type="date" class="form-control float-right" name="reservation" id="reservation" required>
								<div class="input-group-btn">
									<button class="btn btn-primary" type="button" id="btnBuscar" title="Buscar">
										<i class="glyphicon glyphicon-search"></i>
									</button>
<!--									<button class="btn btn-success" type="button" id="btnExportarLista" title="Exportar a Excel">-->
<!--										<i class="fa fa-file-excel-o"></i> Exportar-->
<!--									</button>-->
								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="content" id="main"></section>
</aside>


<script src="<?php echo base_url('assets/report/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/dataTables.buttons.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/buttons.bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/jszip.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/pdfmake.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/vfs_fonts.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/buttons.html5.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/buttons.print.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/buttons.colVis.min.js'); ?>"></script>

<script src="<?php echo base_url('assets/js/reporte/reporte.js') ?>"></script>
<script src="<?php echo base_url('assets/function.js'); ?>"></script>

