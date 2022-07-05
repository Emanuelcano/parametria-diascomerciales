<div class="box box-success">
	<div class="box-header with-border">
		<h3 class="box-title">¿Que Enviar?</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<div class="box-body">
		<div id="queEnviarSms">
			<?php $this->load->view('cronograma_campanias/partials/queEnviarSMS', []); ?>
		</div>
		<div id="queEnviarWhatsapp" style="display: none">
			<?php $this->load->view('cronograma_campanias/partials/queEnviarWhatsapp', ['templates' => $templates, 'campaign' => $campaign]); ?>
		</div>
	</div>
	<div class="overlay" id="loadingMensajes">
		<i class="fa fa-refresh fa-spin"></i>
	</div>
</div>
