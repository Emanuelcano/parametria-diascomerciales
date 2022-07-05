<div class="box box-success">
	<div class="box-header with-border">
		<h3 class="box-title">¿Como enviar?</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		</div>
	</div>
	<div class="box-body">
		<div class="col-md-3">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">¿Como enviar?</h3>
				</div>
				<form name="frm_que_enviar" id="frm_que_enviar" class="form-group" role="form" method="POST">
					<div class="card-body">
						<div class="form-group">
							<label for="end" class="col-sm-2 control-label">Metodo</label>
							<div class="col-sm-10">
								<select name="sl_metodo" id="sl_metodo" class="form-control">
									<?php foreach ($envios as $item) { ?>
										<option value="<?= $item ?>"><?= $item ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card" id="cardFormat">
				<div class="card-header">
					<h3 class="card-title">Formato</h3>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="end" class="col-sm-2 control-label">Formato</label>
						<div class="col-sm-10">
							<select name="sl_formato" id="sl_formato" class="form-control">
								<?php foreach ($formatos as $item) { ?>
									<option value="<?= $item ?>"><?= $item ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Lista de Notificados</h3>
				</div>
				<!-- /.card-header -->
				<div class="card-body">
					<div class="form-group" id="groupSlackUsers">
						<label for="start" class="col-sm-2 control-label">Notificar a:</label>
						<div class="col-sm-8">
							<select class="slackIdsMultiple" name="slackIds[]" id="destinatario_slack"  multiple="multiple" style="width: 100%">
							</select>
						</div>
					</div>

					<div class="form-group" id="groupEmails">
						<label for="start" class="col-sm-2 control-label">Notificar a:</label>
						<div class="col-sm-8">
							<input type="text" name="txt_notificar" id="txt_notificar" class="form-control"
								   required="required" data-role="tagsinput"/>
						</div>

					</div>
					<div class="row">
						<div class="" id="tb_remitentes"></div>


					</div>
				</div>
				<!-- /.card-body -->
			</div>
		</div>
	</div>
	<div class="overlay" id="loadingComoEnviar" style="display: none">
		<i class="fa fa-refresh fa-spin"></i>
	</div>
</div>
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/js/jquery.email.multiple.js'); ?><!--"></script>-->

<script>
	$(document).ready(function ($) {

		$("#sl_metodo").change(function () {
			if ($(this).val() == CAMPAIGN_METODO_ENVIO_SLACK) {
				$("#groupSlackUsers").show();
				$("#cardFormat").show();
				$("#groupEmails").hide();
			} else {
				$("#groupEmails").show();
				$("#cardFormat").hide();
				$("#groupSlackUsers").hide();
			}
			saveMethod();
		})

		$("#sl_formato").change(function () {
			saveFormat();
		});

		$("#azure").click(function () {
			$.ajax({
				type: "POST",
				url: base_url + "api/ApiSupervisores/send_email_notification/",
				data: {
					'camp_id': $("#txt_hd_id_camp").val(),
					'id_msj_program': 10
				},
				success: function (response) {
				}
			})
		})

		$('#txt_notificar').tagsinput({
			trimValue: true,
			allowDuplicates: false,
			tagClass: 'label label-primary'
		});

		$('#txt_notificar').on('beforeItemAdd', function (event) {
			var tag = event.item;

			//expresion regular para comprobar si es un email
			if (/^[a-z0-9._-]+@[a-z0-9._-]+\.[a-z]{2,6}$/.test(tag)) {
				if (!event.options || !event.options.preventPost) {
					$.ajax({
						type: "POST",
						url: base_url + "api/ApiSupervisores/add_campaign_notification_email/",
						data: {
							'email': tag,
							'camp_id': $("#txt_hd_id_camp").val()
						},
						success: function (response) {
						}
					})
				}
			} else {
				//no es un email, no lo agrego
				event.cancel = true;
			}
		});

		$('#txt_notificar').on('beforeItemRemove', function (event) {
			var tag = event.item;
			if (!event.options || !event.options.preventPost) {
				Swal.fire({
					title: 'Borrar Email',
					text: 'Estas seguro de que quieres BORRAR el email?',
					icon: 'warning',
					confirmButtonText: 'Aceptar',
					cancelButtonText: 'Cancelar',
					showCancelButton: 'true'
				}).then((result) => {
					if (result.value) {

						base_url = $("input#base_url").val();

						$.ajax({
							type: "POST",
							url: base_url + "api/ApiSupervisores/remove_campaign_notification_email/",
							data: {
								'email': tag,
								'camp_id': $("#txt_hd_id_camp").val()
							},
							success: function (response) {
								$('#txt_notificar').tagsinput('remove', tag, {preventPost: true});
							}
						})

					} else {
						$('#txt_notificar').tagsinput('add', tag, {preventPost: true});
					}
				});

			}

		});

	});
</script>
<style>
	.bootstrap-tagsinput {
		width: 100% !important
	}

	.bootstrap-tagsinput .tag {
		font-size: 13px;
	}

</style>
