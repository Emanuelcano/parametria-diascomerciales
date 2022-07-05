<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
	<div class="modal-dialog modal-lg" role="document" style="transform: translate(25%); width: 60%">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Detalles configuracion campaña</h4>
			</div>
			<div class="modal-body">
				<div class="container">
					<div class="row">
						<div class="col-md-4">
							<div class="row">
								<div id="fieldsMora" style="display: none">
									<?php $this->load->view('supervisores/partials/campanias_manuales/modal_detalle_parcials/detalle_mora'); ?>
								</div>
								<div id="fieldsPreventiva" style="display: none">
									<?php $this->load->view('supervisores/partials/campanias_manuales/modal_detalle_parcials/detalle_preventiva'); ?>
								</div>
								<div id="fieldsVentas" style="display: none">
									<?php $this->load->view('supervisores/partials/campanias_manuales/modal_detalle_parcials/detalle_ventas'); ?>
								</div>
							</div>
							<hr style="border-top: 1px solid #dddddd;">
							<div class="row">
								<div class="col-md-5 text-right">
									<strong>Orden:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_orden">1</span>
								</div>
								<div class="col-md-5 text-right">
									<strong>Asignar:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_asignar">1</span>
								</div>
								<div class="col-md-5 text-right">
									<strong>Regestionar a dias:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_regestionar">1</span>
								</div>
								<div class="col-md-5 text-right">
									<strong>Autollamada:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_autollamada">1</span>
								</div>
								<div class="col-md-5 text-right">
									<strong>Equipo:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_equipoquery">1</span>
								</div>
							</div>
							<hr style="border-top: 1px solid #dddddd;">
							<div class="row">
								<div class="col-md-5 text-right">
									<strong>Exclusiones:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_exclusiones">1</span>
								</div>
							</div>
							<hr style="border-top: 1px solid #dddddd;">
							<div class="row">
								<div class="col-md-5 text-right">
									<strong>Equipo Operadores:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_equipoOperadores">1</span>
								</div>
								<div class="col-md-5 text-right">
									<strong>Tipo Operadores:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_tipoOperadores">1</span>
								</div>
							</div>
							<hr style="border-top: 1px solid #dddddd;">
							<div class="row">
								<div class="col-md-5 text-right">
									<strong>Primera Gestion:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_min1">1</span>
								</div>
								<div class="col-md-5 text-right">
									<strong>Tiempo Extra:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_extra">1</span>
								</div>
								<div class="col-md-5 text-right">
									<strong>Renovaciones:</strong>
								</div>
								<div class="col-md-7">
									<span id="modal_renovaciones">1</span>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div id="modal_whatsapp" style="display: none">
								<div class="col-md-6 text-left">
									<strong>Template Whatsapp</strong>
								</div>
								<div class="col-md-6 text-right">
									<strong>CANAL:</strong>
									<div style="display: inline-block" id="modal_canal_whatsapp"></div>
								</div>
								<br>
								<div class="well" style="margin-top: 15px" id="modal_template_whatsapp"></div>
							</div>

							<div id="modal_sms" style="display: none">
								<div class="col-md-6 text-left">
									<strong>Template SMS</strong>
								</div>
								<br>
								<div class="well" style="margin-top: 15px" id="modal_template_SMS"></div>
							</div>
							<div id="modal_email" style="display: none;">
								<div class="col-md-6 text-left">
									<strong>Template Email</strong>
								</div>
								<br>
								<div class="well" style="margin-top: 15px;max-height: 300px; overflow-y: auto"
									 id="modal_template_Email"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
	$(document).ready(function () {
		$("#detalleCampania").click(function () {
			let base_url = $("input#base_url").val();
			$.ajax({
				type: "POST",
				url: base_url + 'api/campanias/campania_campos',
				data: {'id_reg': $("#ver_campania").val()}
			}).done(function (response) {
				$.ajax({
					type: "POST",
					url: base_url + 'api/campanias/getCampaniaExtraInfo',
					data: {
						'template_whatsapp': response.result[0].whatsapp,
						'template_sms': response.result[0].sms,
						'template_email': response.result[0].mail,
						'exclusiones': response.result[0].id_exclusion,
					  	'operadores': response.result[0].operadores
					}
				}).done(function (response2) {
					fillTipoCampania(response.result[0], response2.data);
					showCampaniaDetalleModal();
				});
			});

		})
	});

	/**
	 * Completa, muesta y oculta los campos correspondientes al tipo de campania
	 *
	 * @param data
	 * @param extra
	 */
	function fillTipoCampania(data, extra) {
		if (data.tipo === 'MORA') {
			fillMora(data);
		} else if (data.tipo === 'PREVENTIVA') {
			fillPreventiva(data);
		} else if (data.tipo === 'VENTAS') {
			fillVenta(data);
		} else {
			console.log('error de tipo');
		}

		let orden = ''
		if (data.orden == 0) {
			orden = 'MAYOR A MENOR'
		}
		if (data.orden == 1) {
			orden = 'MENOR A MAYOR'
		}
		
		$("#modal_orden").html(orden);
		$("#modal_asignar").html(data.asignar);

		$("#modal_regestionar").html(data.re_gestionar);
		$("#modal_autollamada").html(data.autollamada);
		$("#modal_equipoquery").html(data.equipoQuery);
		$("#modal_exclusiones").html(extra.exclusiones.join('<br>'));
		
		console.log(data);
		$("#modal_equipoOperadores").html(data.equipo);
		$("#modal_tipoOperadores").html(extra.operadores.join('<br>'));

		$("#modal_min1").html(data.minutos_gestion + " Minutos");
		$("#modal_extra").html(data.minutos_extra + " Minutos");
		$("#modal_renovaciones").html(data.cantidad_extensiones);
		
		let grupoWhatsapp = $("#modal_whatsapp");
		let templateSms = $("#modal_sms");
		let templateEmail = $("#modal_email");
		templateSms.hide();
		templateEmail.hide();
		grupoWhatsapp.hide();
		console.log(!data.whatsapp);
		if (data.whatsapp && data.whatsapp != "0") {
			grupoWhatsapp.show();
			$("#modal_template_whatsapp").html(extra.templates.whatsapp);
			
			let canal_w = '';
			if (data.canal_whatsapp === '15140334') {
				canal_w = 'ORIGINACION';
			} else if (data.canal_whatsapp === '15185188') {
				canal_w = 'COBRANZA';
			}
			$("#modal_canal_whatsapp").html(canal_w);
		}
		if (data.sms && data.sms != "0") {
			$("#modal_template_SMS").html(extra.templates.sms);
			templateSms.show();
		}
		if (data.mail && data.mail != "0") {
			$("#modal_template_Email").html(extra.templates.email);
			templateEmail.show();
		}
	}

	function showCampaniaDetalleModal() {
		$('#myModal').modal();
		$('#myModal').on('hide.bs.modal', function (e) {
			$(".wrapper").css('min-height', '5%');
		})
	}

</script>
