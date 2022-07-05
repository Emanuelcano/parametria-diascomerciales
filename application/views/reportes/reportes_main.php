<link rel="stylesheet" href="<?php echo base_url('assets/css/dualSelectList.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/fullcalendar/main.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>
<link rel="stylesheet" href="../assets/css/custom-gestion.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/supervisores/select2Bootstrap.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/dualSelectListSkill.jquery.js');?>" ></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>
<link rel="stylesheet" href="<?php echo base_url('assets/css/custom-gestion.css');?>">
<script type="text/javascript" src="<?php echo base_url('assets/jquery-validate/jquery.validate.min.js');?>" ></script>
<script type="text/javascript" src="<?php echo base_url('assets/reportes/reportes.js');?>" ></script>
<style type="text/css">
    #fullcalendar {
        width: 100%;height: 100%;
    }
</style>
<span class="hidden-xs">
    <?php
    $usuario = $this->session->userdata("username");
    $tipoUsuario = $this->session->userdata("tipo");
    ?>
</span>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario ?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario; ?>">

<div id="mostrarModal" class="modalAjuste" style="width:0%; height: 0%; margin-left: -15%; margin-top: 2%;">
    <div id="modalInfo"></div>
</div>

<div id="fadeMostrarModal" class="overlayAjuste" onclick = "limpiarAnalista();
        document.getElementById('mostrarModal').style.display = 'none';
        document.getElementById('fadeMostrarModal').style.display = 'none';
        $('#modalInfo').html('');
        $('input#gestionando').val('no');"></div>

<div id = "dashboard_principal" style="display: block; background: #FFFFFF;">
	<div class="box-header with-border" class="col-lg-12">
		<div class="col-lg-12" id="cuerpoCreditosBuscar" style="display: block">
			<div class="box-body pull-left">
				<a class="btn btn-app" onclick="control_solicitudes();">
					<span class="badge bg-red" id="btn_solicitud"><?php print_r('0'); ?></span>
					<i class="fa fa-file"></i> Solicitudes
				</a>
			</div>

			<div class="box-body pull-left">
				<a class="btn btn-app" onclick="control_originacion();">
					<span class="badge bg-red" id="btn_originacion" ><?php echo '0'; ?></span>
					<i class="fa fa-file"></i> Originación
				</a>
			</div>

			<div class="box-body pull-left">
				<a class="btn btn-app" id="btn_cobranza" onclick="control_reporte();">
					<i class="fa fa-file" aria-hidden="true"></i> Cobranzas
				</a>
			</div>

			<div class="box-body pull-left">
				<a class="btn btn-app" id="btn_cobranza" onclick="contable_reporte();">
					<i class="fa fa-file" aria-hidden="true"></i> Contable
				</a>
			</div>
		</div>
	</div>
</div>

<div>
    <section class="content">

        <div class="col-lg-12" id="main" style="display: block">

        </div>

    </section>
</div>




	