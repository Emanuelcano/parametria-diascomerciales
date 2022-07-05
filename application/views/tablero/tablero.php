
<style>
body.modo-oscuro {
    background: #3c3b3b !important;
    color: white !important;
}
body.modo-oscuro #tp_Indicadores{
	color: #05af05;
    font-weight: 600;
}
body.modo-oscuro #tp_Indicadores h5 small, body.modo-oscuro th{
	color: white;
    font-weight: 600;
}
body.modo-oscuro td, body.modo-oscuro th{
	background: #3c3b3b !important;
	border-width: 2px !important;
	border-color: white !important;
}
body.modo-oscuro table.dataTable {
    border-collapse: collapse;
    border-spacing: 0;
}
body.modo-oscuro .dataTables_info, body.modo-oscuro .dataTables_length, body.modo-oscuro .paginate_button, body.modo-oscuro .dataTables_filter{
	color: white !important;
}
body.modo-oscuro .panel {
	border: 2px solid white;
	background-color: transparent;
}
body.modo-oscuro .panel-danger {
	border: 2px solid white;
	background-color: red !important;
	color: white !important;
}
body .panel-danger {
	color: red;
}
table.dataTable {	
    border-collapse: collapse !important;	
    border-spacing: 0;	
}

body.modo-oscuro .panel .panel-title {
	color: white;
}
body.modo-oscuro .panel .panel-heading {
	background-color: transparent;
}
</style>
<span class="hidden-xs">
	<?php
	
	$usuario     = $this->session->userdata("username");
	$tipoUsuario = $this->session->userdata("tipo");
	?>
</span>

<?php //echo base_url()."assets/template/dist/img/loading.gif"; ?>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">


<div style="margin-top:80px;">
		<div class="col-md-12">
		<a id="modo-oscuro" class="btn bg-black">Modo Oscuro</a>
		</div>
		<div class="col-lg-12" id="main" style="display: block;">

		</div>

</div>
<script src="<?php echo base_url('assets/function.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/tablero/tablero.js') ?>"></script>

