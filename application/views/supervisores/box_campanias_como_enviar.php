<div id="box_como_enviar" class="row">
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
								<?php foreach ($data['envios'] as $item) { ?>
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
							<?php foreach ($data['formatos'] as $item) { ?>
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
						<select class="slackIdsMultiple" name="slackIds[]" multiple="multiple" style="width: 100%">
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
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.email.multiple.js'); ?>"></script>

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

	function loadSlackUsersAndChannels() {
		var users = [];
		var channels = [];
		var slacksIds = [];

		$.ajax({
			type: "GET",
			async: false,
			url: base_url + "api/ApiSupervisores/getSlackActiveUsersAndChannels/",
			success: function (response) {
				$.ajax({
					type: "POST",
					url: base_url + "api/ApiSupervisores/getSlackNotificados/",
					async: false,
					data: {
						'id_campania': $("#txt_hd_id_camp").val()
					},
					success: function (response) {
						response.forEach(function (arrayItem) {
							slacksIds.push(arrayItem.slack_id)
						});
					}
				})

				for (var i = 0, l = response.length; i < l; i++) {
					let item = {
						"id": response[i].slack_id,
						"text": response[i].nombre,
						"selected": slacksIds.includes(response[i].slack_id)
					}
					if (response[i].type == 'user') {
						users.push(item);
					} else {
						channels.push(item);
					}
				}
			}
		});

		var data = [
			{
				"text": "Usuarios",
				"children": users
			},
			{
				"text": "Grupos",
				"children": channels
			}
		];

		return data;
	}

	function loadSlackSelect(id_campania) {

		$('.slackIdsMultiple').select2({
			placeholder: "Select a value",
			data: loadSlackUsersAndChannels(),
		});

		$(".slackIdsMultiple").on("change", function () {
			let ids = $(this).val();

			$.ajax({
				type: "POST",
				url: base_url + "api/ApiSupervisores/saveSlackNotificados/",
				data: {
					'slack_ids': ids,
					'camp_id': $("#txt_hd_id_camp").val()
				},
				success: function (response) {
					console.log(response)
				}
			})


		});

	}
	
	function loadComoEnviar(id_campania) {
		$.ajax({
			type: "POST",
			url: base_url + "api/ApiSupervisores/get_campania/",
			data: {
				'id_campania': id_campania
			},
			success: function (response) {
				$("#sl_metodo").val(response.data[0].metodo);
				$("#sl_formato").val(response.data[0].formato);
			}
		})
	}

	function loadFilters() {
		$.ajax({
			type: "POST",
			url: base_url + "api/ApiSupervisores/get_filtros_campanias/",
			data: {
				'camp_id': $("#txt_hd_id_camp").val()
			},
			success: function (response) {
				$("#sl_destino").val(response[0].destiny);
				$("#sl_destino").trigger('change')

				//======================= Tipo clientes =======================
				$("#sl_clientType").val(response[0].client_type);
				$("#sl_clientType").trigger('change');

				//======================= Accion y x Creditos =======================
				if (response[0].client_type != CAMPAIGN_CLIENT_TYPE_PRIMARIA) {
					$("#sl_actions").val(response[0].accion);
					$("#sl_actions").trigger('change');

					if (response[0].accion != CAMPAIGN_ACTION_ALL) {
						$("#x_creditos").val(response[0].x_credits)
					}
				}

				$("#sl_status").val(response[0].estatus.split(','));
				$('#sl_status').trigger('change');

				//======================= Filtros y logico =======================

				let filtro = response[0].filtro;
				let logic = response[0].logic;

				$("#sl_filters").val(filtro);
				$("#sl_filters").trigger('change');

				$("#sl_logics").val(logic);
				$("#sl_logics").trigger('change');


				if (filtro == CAMPAIGN_FILTER_DIAS_ATRASO && logic == CAMPAIGN_LOGIC_ENTRE) {
					$("#valor_1").val(response[0].valor1);
					$("#valor_2").val(response[0].valor2);
				} else {
					$("#valor_1").val(response[0].valor1);
				}

				if (filtro == CAMPAIGN_FILTER_FECHA_VENCIMIENTO && logic == CAMPAIGN_LOGIC_ENTRE) {
					$("#sl_origen_desde").val(response[0].origen_desde);
					$("#origen_desde_valor").val(response[0].origen_desde_valor);
					$("#sl_origen_hasta").val(response[0].origen_hasta);
					$("#origen_hasta_valor").val(response[0].origen_hasta_valor);
				} else {
					$("#sl_origen_desde").val(response[0].origen_desde);
					$("#origen_desde_valor").val(response[0].origen_desde_valor);
				}

				if (filtro == CAMPAIGN_FILTER_MONTO_COBRAR && logic == CAMPAIGN_LOGIC_ENTRE) {
					$("#valor_1").val(response[0].valor1);
					$("#valor_2").val(response[0].valor2);
				} else {
					$("#valor_1").val(response[0].valor1);
				}


			}
		})
	}

	function iniciarTagInput() {
		$.ajax({
			type: "POST",
			url: base_url + "api/ApiSupervisores/get_campaign_notification_emails/",
			data: {
				'camp_id': $("#txt_hd_id_camp").val()
			},
			success: function (response) {
				response.forEach(function (arrayItem) {
					//el add dispara el evento de beforeItemAdd, generando un ajax. Para evitar que los items
					//se dupliquen se agrego un checkeo en el backend de si existe el email para la campania.
					//De existir no se agregara a la DB pero si se vera reflejado en el frontend ya que es necesario verlo
					$('#txt_notificar').tagsinput('add', arrayItem.email, {preventPost: true});
				});
			}
		})
	}
</script>
<style>
	.bootstrap-tagsinput {
		width: 100% !important
	}

	.bootstrap-tagsinput .tag {
		font-size: 13px;
	}

</style>
