<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/icheck/skins/flat/green.css'); ?>"/>


<link rel="stylesheet" href="<?php echo base_url('assets/datetimepicker/bootstrap-datetimepicker.min.css');?>" />
<script src="<?php echo base_url('assets/moment/moment.min.js');?>" ></script>
<script src="<?php echo base_url('assets/datetimepicker/bootstrap-datetimepicker.min.js');?>"></script>
<div class="box box-success">
	<div class="box-header with-border">
		<h3 class="box-title">¿Cuando enviar?</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-6">
				
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
<!--				<div id="cronograma" data-endpoint="/cronograma/campania" data-origin="CAMPAING---><?//=$campaignId?><!--" data-method="post" data-params='{"campaignId":--><?//=$campaignId?><!--}'></div>-->
				<div id="cronograma"></div>
			</div>
			<div class="col-md-6" id="eventList"></div>
		</div>
	</div>
	<div class="overlay" id="loadingCuandoEnviar2" style="display: none">
		<i class="fa fa-refresh fa-spin"></i>
	</div>
</div>
<div class="modal fade" id="modal-info">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Info Template </h4>
			</div>
			<div class="modal-body">
				<div id="modal-info-text"><pre></pre></div>
				<div class="modalLoading" id="loadingInfoTemplate">
					<i class="fa fa-refresh fa-spin"></i>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<script>
  var crono
	$(document).ready(function(){
		crono = $('#cronograma').cronograma({
			title: 'Nuevo Evento',
			color: 'primary',
		  	origin: 'CAMPAING-<?=$campaignId?>',
			events_render_target: '#eventList',
		  	endpoint: {
				  url: base_url + 'api/campanias/sendCampaniaWhatsappTemplates',
				  method: 'post',
				  params: {
					type_env: '<?=$type?>',
					idCampania: <?=$campaignId?>
				}
			}
			
		});
			
		// crono.addParameter('azs','sky');
	});
</script>
