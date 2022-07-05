$(() => {
	sspayment.init();
})
sspayment = []
sspayment.var = []
sspayment.var.base_url = $("#base_url").val();
sspayment.credito = {};
sspayment.credito['from'] = {};
sspayment.credito['to'] = {};

sspayment.init = () => {

	$.fn.dataTable.Api.register('processing()', function () {
		return this.iterator('table', function (settings) {
			settings.clearCache = true;
		});
	});
	$("#section_search_credito_from #result").hide();

	$("#section_search_credito_from #form_search_0").on('submit', function (event) {
		event.preventDefault();
		if ($("#section_search_credito_from #form_search_0 #search").val().trim() == "") {
			Swal.fire("Campos Incompletos", "Debe ingresar un valor en el campo de busqueda y definir el criterio bajo el cual se realizará la misma ", "warning");
		} else {
			sspayment.buscarsolicitud_From($("#section_search_credito_from #form_search_0 #search").val(), null, null, 'documento');
		}
	});

	$("#section_search_credito_to #form_search_1").on('submit', function (event) {
		event.preventDefault();
		if ($("#section_search_credito_to #form_search_1 #search").val().trim() == "") {
			Swal.fire("Campos Incompletos", "Debe ingresar un valor en el campo de busqueda y definir el criterio bajo el cual se realizará la misma ", "warning");
		} else {
			sspayment.buscarSolicitud_to($("#section_search_credito_to #form_search_1 #search").val(), null, null, 'documento');
		}
	});

	$("#section_search_credito_from #form_search_0").on('reset', function (event) {
		event.preventDefault();
		$("#section_search_credito_from #result").hide();
	});

	$("#section_search_credito_to #form_search_1").on('reset', function (event) {
		event.preventDefault();
		$("#section_search_credito_to #result").hide();
	});

	$("#section_search_credito_from #search").on('keyup', function (event) {
		event.preventDefault();
		if ($(this).val().length == 0) {
			$("#section_search_credito_from #result").hide();
			$("#texto").empty();
		}
	});

	$("#section_search_credito_to #search").on('keyup', function (event) {
		event.preventDefault();
		if ($(this).val().length == 0) {
			$("#section_search_credito_to #result").hide();
			$("#texto").empty();
		}
	});
	$('.fixPago .ajustes1 #btnProcesar').on('click', function (event) {
		event.preventDefault();
		sspayment.BtnProcesar()
	})
};


sspayment.buscarsolicitud_From = (search, fecha = null, operador = null, criterio = null) => {
	let base_url = $("#base_url").val();
	const formData = new FormData();
	formData.append("search", search);
	formData.append("fecha", fecha);
	formData.append("operador", operador);
	formData.append("criterio", criterio);

	$.ajax({
			url: base_url + 'api/credito/buscar/',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
		})
		.done((response) => {

			if (response.creditos.length > 0) {			
				$.ajax({
					url: sspayment.var.base_url + 'api/credito/get_pagos',
					type: 'POST',					
					dataType: 'json',
					data: { "idcliente": response.creditos[0]['id_cliente'] },
				})
				.done(function (resp) {
					sspayment.consultar_cliente(response.creditos[0]['id_cliente'], 0)
					sspayment.consultar_pagos(resp.data);
					$(".search-section-0").hide();
					$(".ajustes0").show();

					$(".ajustes0 #close_credito").on('click', () => {
						$(".ajustes0").hide();
						$(".search-section-0").show();
						$("#seccion-descuento").addClass('hide');
						$("#seccion-pagos").addClass('hide');
						if ($('.fixPago table#pagos-cliente-from > tbody  > tr').find('td:first input:checkbox:checked').length > 0){
							$('.fixPago table#pagos-cliente-from > tbody  > tr').each((index, tr) => {
								if ($(tr).find('td:first input').prop('checked')) {
									$(tr).find('td:first input').prop('checked',false);
								}
							});
						}
						$(".search-section-1").hide();							
						$(".ajustes1").hide();
					});
				})
				.fail(function (resp) {
					swal.fire('Ups..', 'Error inserperado', 'error');
				});

			} else {
				swal.fire('Ups..', 'Valide el Documento del cliente', 'error');
			}

		});
}
sspayment.buscarSolicitud_to = (search, fecha = null, operador = null, criterio = null) => {
	let base_url = $("#base_url").val();
	const formData = new FormData();
	formData.append("search", search);
	formData.append("fecha", fecha);
	formData.append("operador", operador);
	formData.append("criterio", criterio);

	$.ajax({
			url: base_url + 'api/credito/buscar/',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
		})
		.done((response) => {
			if (response.creditos.length > 0) {			
				$.ajax({
					url: sspayment.var.base_url + 'api/credito/get_pagos',
					type: 'POST',					
					dataType: 'json',
					data: { "idcliente": response.creditos[0]['id_cliente'] },
				})
				.done(function (resp) {
					sspayment.consultar_cliente(response.creditos[0]['id_cliente'], 1)
					sspayment.consultar_pagos1(resp.data);
					$(".search-section-1").hide();
					$(".ajustes1").show();

					$(".ajustes1 #close_credito").on('click', () => {
						$(".ajustes1").hide();
						$(".search-section-1").show();
						$("#seccion-descuento").addClass('hide');
						$("#seccion-pagos").addClass('hide');
						if ($('.fixPago table#pagos-cliente-to > tbody  > tr').find('td:first input:checkbox:checked').length > 0){
							$(".search-section-1").hide();
							$('.fixPago table#pagos-cliente-to > tbody  > tr').each((index, tr) => {
								if ($(tr).find('td:first input').prop('checked')) {
									$(tr).find('td:first input').prop('checked',false);
								}
							});
						}
					});
				})
				.fail(function (resp) {
					swal.fire('Ups..', 'Error inserperado', 'error');
				});

			} else {
				swal.fire('Ups..', 'Valide el Documento del cliente', 'error');
			}
		});
}

sspayment.consultar_pagos = (detalle_pagos) => {

	$(".fixPago table#pagos-cliente-from tbody").html('');

	detalle_pagos.forEach((val, key) => {
		$(".fixPago table#pagos-cliente-from tbody").append(
			"<tr><td><input type='checkbox' id='checkbox1' data-id='" + val.id + "' data-pago_credito_id='" + val.pago_credito_id + "' onchange='sspayment.checkboxChange_from(this)'/></td>" +
			"<td>" + val.id_credito + "</td>" +
			"<td>" + ((val.medio_pago == null) ? '' : val.medio_pago) + "</td>" +
			"<td>" + ((val.referencia_externa == null) ? '' : val.referencia_externa) + "</td>" +
			"<td>" + ((val.referencia_interna == null) ? '' : val.referencia_interna) + "</td>" +
			"<td>" + ((val.fecha_pago == null) ? '' : val.fecha_pago) + "</td>" +
			"<td>" + ((val.monto == null) ? 0 : val.monto) + "</td>" +
			"<td>" + ((val.estado == null) ? 'vigente' : val.estado) + "</td>" +
			"<td>" + ((val.estado_razon == null) ? '' : val.estado_razon) + "</td></tr>");
	});
}

sspayment.consultar_pagos1 = (detalle_pagos) => {

	$(".fixPago table#pagos-cliente-to tbody").html('');

	sspayment.credito['to'].credito = [];
	detalle_pagos.forEach((val, key) => {

		style = (val.estado == 'pagado' ? 'background:#e0e3e8' : '');
		sspayment.credito['to'].credito.push(parseInt(val.id_credito))
		$(".fixPago table#pagos-cliente-to tbody").append(
			"<tr  style=" + style + ">" +
			//"<td><input type='checkbox' id='checkbox1' data-id='" + val.id_credito + "'  onchange='sspayment.checkboxChange_to(this)'/></td>" +
			"<td>" + val.id_credito + "-" + val.numero_cuota + "</td>" +
			"<td>" + moment(val.fecha_otorgamiento).format('DD-MM-YYYY') + "</td>" +
			"<td>" + moment(val.fecha_vencimiento).format('DD-MM-YYYY') + "</td>" +
			"<td>" +(( val.estado == null) ? 'Vigente' : val.estado) + "</td></tr>");  // is null show vigente
	});
}
sspayment.checkboxChange_from = (data) => {
	sspayment.credito['from'].pagosaprocesar = [];

	if ($('.fixPago table#pagos-cliente-from > tbody  > tr').find('td:first input:checkbox:checked').length > 0)
		$(".search-section-1").show();
	else {
		$(".search-section-1").hide();
		$(".ajustes1").hide();
	}
	$('.fixPago table#pagos-cliente-from > tbody  > tr').each((index, tr) => {
		if ($(tr).find('td:first input').prop('checked')) {
			sspayment.credito['from'].pagosaprocesar.push($(tr).find('td:first input').data('id') + "|" + $(tr).find('td:first input').data('pago_credito_id') + "|" + $(tr).find('td:eq(1)').html())
		}
	});
}

sspayment.consultar_cliente = (id_cliente, action) => {
	let base_url = $("input#base_url").val() + "api/solicitud/consultar_solicitud_cliente/" + id_cliente;
	$.ajax({
		type: "GET",
		url: base_url,
		success: (response) => {
			if (response.status.ok) {
				$(".ajustes" + action + " #nombre-cliente").html(response.data.nombres + ' ' + response.data.apellidos);
				$(".ajustes" + action + " #documento-cliente").html(((response.data.documento != null) ? response.data.documento : ''));
				$(".ajustes" + action + " #telefono-cliente").html(((response.data.telefono != null) ? response.data.telefono : ''));
				$(".ajustes" + action + " #mail-cliente").html(((response.data.email != null) ? response.data.email : ''));

				if (action == 0) {
					sspayment.credito['from'].cliente = {}
					sspayment.credito['from'].cliente = response.data
				} else {
					sspayment.credito['to'].cliente = {}
					sspayment.credito['to'].cliente = response.data
				}
			} else {
				Swal.fire({
					title: "Cliente invalido",
					text: response.message,
					icon: 'error'
				})
			}
		}
	});
}

sspayment.BtnProcesar = () => {
	swal.fire({
		title: "Corregir pagos.",
		html:
			'<br>Esta seguro que desea corregir este pago, ' +
			'<br> el cambio no se puede revertir',
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		cancelButtonText: "No",
		confirmButtonText: "Si"
	}).then(function (result) {
		if (result.value) {
			if (sspayment.credito.to.credito.length > 0) {
				swal.fire('Procesando.. ', '<br><i class="fa fa-spinner fa-spin fa-5x fa-fw"></i>', 'warning');
				$.ajax({
						url: sspayment.var.base_url + 'api/credito/fix_pago',
						type: 'POST',					
						dataType: 'json',
						data: JSON.parse(JSON.stringify(sspayment.credito))
					})
					.done(function (response) {
						if (response.status.ok){
							swal.close()
							swal.fire('Exito', 'El credito fue actualizado', 'success');
							$(".ajustes0").hide();
							$(".search-section-0").show();
							$("#seccion-descuento").addClass('hide');
							$("#seccion-pagos").addClass('hide');
							if ($('.fixPago table#pagos-cliente-from > tbody  > tr').find('td:first input:checkbox:checked').length > 0){
								$('.fixPago table#pagos-cliente-from > tbody  > tr').each((index, tr) => {
									if ($(tr).find('td:first input').prop('checked')) {
										$(tr).find('td:first input').prop('checked',false);
									}
								});
							}
							$(".search-section-1").hide();							
							$(".ajustes1").hide();
						}
					})
					.fail(function (response) {
						swal.fire('Ups..', 'Error inserperado', 'error');
					});
			} else {
				swal.fire('Error','Debe seleccionar un credito para aplicarlo ','error');
			}
		}

	})
}



