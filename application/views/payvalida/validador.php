<div class="row" id="containerValidador" style="margin-left: 10px; margin-right: 10px">
	<div class="col-lg-8 col-lg-offset-2">
		<?php $this->load->view('payvalida/partials/buscador.php', $origin) ?>
	</div>
	<div class="col-lg-4 col-lg-offset-2">
		<?php $this->load->view('payvalida/partials/movimientos.php', $movimientos) ?>
	</div>
	<div class="col-lg-4">
		<?php $this->load->view('payvalida/partials/payvalidaInfo.php', $payvalida) ?>
	</div>
	<div class="col-lg-8  col-lg-offset-2">
		<?php $this->load->view('payvalida/partials/logs.php', $logs) ?>
	</div>
</div>
<style>
	body, #containerValidador, #modulo-content {
		background-color: #ecf0f5 !important;
	}
</style>


