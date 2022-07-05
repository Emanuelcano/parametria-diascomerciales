$(() => {

	var_agendaemail.accordion_gest_agendaemail('.accordion_gest_agendaemail', '.title_btn_agendaemail', '.body_agendaemail');
	var_agendaemail.loadtblagendaemail();


	$(".accordion_gest_agendaemail").on("click", () => {
		var_agendaemail.accordion_gest_agendaemail('.accordion_gest_agendaemail', '.title_btn_agendaemail', '.body_agendaemail');
	});

	$("#new-cuenta-mail").on('change', () => {
		var_agendaemail.validateEmail();
	})
});
	var_agendaemail = [];
	var_agendaemail.data = {};
	var_agendaemail.data.email = {};
	var_agendaemail.data.agendaMailTemplate = {};

var_agendaemail.accordion_gest_agendaemail = (v1, v2, v3) => {
	$(v1).toggleClass("active");
	$(v1).hasClass('active') ? $(v2).text('DIRECTORIO MAIL') : $(v2).text('VER DIRECTORIO MAIL');
	$(v3).css('display') === 'block' ? $(v3).css('display', 'none') : $(v3).css('display', 'block');
}

var_agendaemail.loadtblagendaemail = () => {
	let documento = $("#inp_age_documento").val();
	var_agendaemail.data.documento = documento;
	var hoy_moment = moment(new Date(), "YYYY-MM-DD");
	let ajax = {
		'type': "GET",
		'url': base_url + 'atencion_cliente/agendaMail/' + documento,
		dataType: 'json'
	}

	let columns = [{
			"data": "cuenta"
		},
		{
			"data": "contacto"
		},
		{
			"data": "fuente",
			"render": (data, type, row, meta) => {
				switch (data) {
					case "PERSONAL":
						return "Personal";
					case "REFERENCIA":
						return "Referencia";
					case "LABORAL":
						return "Laboral";
					case "BURO_T":
						return "Buro - Mail - T";
					case "BURO_D":
						return "Buro - Laboral - D";
					default:
						return "";
				}
			}
		},
		{
			"data": "primer_reporte",
			className: 'dt-body-center',
			"render": (data, type, row, meta) => {
				if (data != null && data != "") {
					antiguedad = hoy_moment.diff(moment(data, "YYYY-MM-DD"), 'months');
				} else
					antiguedad = 0;
				return antiguedad
			}
		},
		{
			"data": "id",
			"render": (data, type, row, meta) => {
				html = '<select class="form-control selectpicker slc_estado_mail" onchange=var_agendaemail.select_action(this) data-id="' + data + '" data-documento="' + row["documento"] + '" id="slc_estado_mail">';
				html += '<option value="1" ' + ((row["estado"] == 1) ? "selected" : "") + '>Activo</option><option value="0" ' + ((row["estado"] == 0) ? "selected" : "") + '>Fuera de servicio</option>';
				html += '</select>';
				return html
			}
		},
		{
			"data": "cuenta",
			"render": (data, type, row, meta) => {
				var_agendaemail.data.email[row["id"]] = row;
				if (row["estado"] == 0) {
					html = '<button disabled style="background:grey; color: white;" class="btn btn-sm" ><i class="fa fa-envelope" aria-hidden="true"></i></button>';
				} else {
					html = '<a style="background:#00c0ef; color: white;" class="btn btn-sm" role="button" id="btn_showemail_' + row["id"] + '" data-id=' + row["id"] + ' onclick=var_agendaemail.btn_action_email(this)><i class="fa fa-envelope" aria-hidden="true"></i></a>';
				}
				return html
			}
		},
	]
	TablaPaginada('table_agenda_mail', 2, 'asc', '', '', ajax, columns);

}

var_agendaemail.btn_action_email = (elem) => {

	id = $(elem).data('id');	
	if ($("#btn_showemail_" + id).parent().find('div.popover:visible').length > 0) {
		$('div.popover:visible').popover('toggle');
		return
	} else if ($('div.popover:visible').length > 0) 
		$('[id*="btn_showemail_"]').popover('hide');
	var_agendaemail.data.idEmail = id;
	$(elem).attr("data-toggle", 'popover-email');
	const formData = new FormData();
	formData.append("documento", $("#inp_age_documento").val());
	formData.append("canal", $('#inp_age_tipo_canal').val());
	$.ajax({
			url: base_url + 'atencion_cliente/agendaMailTemplate',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false
		})
		.done((response) => {
			var datax = JSON.parse(response);
			var_agendaemail.data.agendaMailTemplate = datax;
			html = "";
			datatr = "";
			for (let index = 0; index < datax.length; index++)
				datatr += '<div class="div-table-row"><button type="button" class="div-table-col btn btn-link showtemplate"  style="text-decoration: none; color: #333" onclick="var_agendaemail.previewtemplate(this)" data-template_id="' + datax[index]["id"] + '" id="' + datax[index]["id"] + '">Asunto: <strong>' + datax[index]['nombre_logica'] + '</strong> - Template: <strong>'+datax[index]['nombre_template']+'</strong> </button></div>';

			html += '<div class="div-table">';
			html += datatr;
			html += '</div>';
			$("#btn_showemail_" + id).popover({
				placement: 'left',
				trigger: 'focus',
				html: true,
				sanitize: false,
				title: "<b>Email</b>",
				content: html
			})
			$("#btn_showemail_" + id).popover('toggle');
			var_agendaemail.get_data_template();
		});
}

var_agendaemail.showtemplateHtml = (template) => {
	let data = var_agendaemail.data;
	$("#btn_showemail_" + data.idEmail).popover('hide');
	$('#preview_mail_html .sendTemplateMail').data('mail', data.email[data.idEmail].cuenta);
	$('#preview_mail_html .sendTemplateMail').data('id_template', template['id']);
	$('#preview_mail_html .sendTemplateMail').data('id_logica', template['id_logica']);

	const formData = new FormData();
	formData.append("documento", data.documento);
	formData.append("id_template_mail", template['id']);
	$.ajax({
			url: base_url + 'api/solicitud/agendaMailTemplateHtml',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
		})
		.done(function (response) {
			var template_mail_preview = response;
			$('#preview_mail_html .modal-body').html(template_mail_preview.message);
			if (template_mail_preview.deshabilitar == true) {
				$('.sendTemplateMail').prop('disabled', true);
			} else {
				$('.sendTemplateMail').prop('disabled', false);
			}
			$('#preview_mail_html').modal('show');
		});

}

var_agendaemail.select_action = (elem) => {
	let estado = $(elem).val();
	let id = $(elem).data('id');
	let formData = new FormData();
	formData.append('estado', estado);
	formData.append('id', id);

	swal.fire({
	title: "¿Esta seguro?",
	text: "¿Estas seguro cambiar estado?",
	type: "warning",
	showCancelButton: true,
	confirmButtonColor: "#3085d6",
	cancelButtonColor: "#d33",
	confirmButtonText: "Si"
	}).then(function (result) {
		if (result.value) {
			
			$.ajax({
			url: base_url + 'api/solicitud/actualizarMailEstado',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			crossDomain: true,
			
		})
			.done(function (response) {
				swal.fire('Exito', 'Se cambio el estado del mail', 'success');
				$("#table_agenda_mail").DataTable().ajax.reload();
			})
			.fail(function () {
				swal.fire('Fail','Error al cambiar estado','error');
			})
			.always(function () {
			});

		} else 
			$("#table_agenda_mail").DataTable().ajax.reload();
		
	});
}

var_agendaemail.sendEmail = () => {
	// debugger
	let data = var_agendaemail.data;
	let formData = new FormData();
	formData.append('documento', data.documento);
	formData.append('mail', data.email[data.idEmail].cuenta);
	formData.append('id_template', data.templateSelected["id"]);
	formData.append('id_logica', data.templateSelected["id_logica"]);

	swal.fire({
		title: "¿Esta seguro?",
		text: "¿Estas seguro de enviar Mail seleccionado?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Si, Enviar"
	}).then((result) => {
		if (result.value) {
			$.ajax({
					url: base_url + 'api/solicitud/enviarMailAgendaPepipost',
					type: 'POST',
					data: formData,
					processData: false,
					contentType: false,
					crossDomain: true,
					beforesend: function () {
						request.setRequestHeader("Access-Control-Allow-Origin", '*');
					},
				})
				.done(function (response) {
                    $("#table_agenda_mail").DataTable().ajax.reload();
					$("#preview_mail_html").modal('hide');

					let id_solicitud = $('#agendar_tlf_new').data('id_solicitud');
					let comment = 
						'<b>[ENVIO EMAIL]</b><br><b>Mail: </b>' + data.email[data.idEmail].cuenta + 
						'</b><br><b>Asunto: </b>' + var_agendaemail.data.templateSelected.nombre_logica + 
						'</b><br><b>Template: </b>' + var_agendaemail.data.templateSelected.nombre_template;
					let type_contact = 6;
					let id_operador = $("section").find("#id_operador").val();
					saveTrack(comment, type_contact, id_solicitud, id_operador);
					consultar_solicitud($('#agendar_tlf_new').data('id_solicitud'));
					swal.fire('Exito', 'Se envio Mensaje Mail', 'success');
				})
				.fail(function () {
					swal.fire('Fail', 'Mail no pudo ser enviado al cliente', 'error');
				});

		} else {
			$("#table_agenda_mail").DataTable().ajax.reload();
		}

	})
}

var_agendaemail.modal_newmail = () => {
    $('#box_add_mail_new input[type=text]').val('');
    $('#box_add_mail_new input[type=number]').val('');
    $('#box_add_mail_new').modal('show');
}

var_agendaemail.validateReEmail = (mail) => {
	const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(mail);
}

var_agendaemail.validateEmail = () => {
	const $result = $("#new-cuenta-mail");
	const mail = $("#new-cuenta-mail").val();
	$result.text("");
  
	if (var_agendaemail.validateReEmail(mail)) {
		$result.css("border-color", "lightgreen");
		$('#agendar_mail').prop('disabled', false);
  
	  } else {
		$result.css("border-color", "red");
		$('#agendar_mail').prop('disabled', true);
	  }
	return true;
 }

var_agendaemail.agendarMail = (documento) => {
    let id_solicitud = $('#agendar_tlf_new').data('id_solicitud');
    let comment = '<b>[AGENDA]</b><br> Se Agrego a la Agenda nuevo  <b>Mail:</b> ' + $("#new-cuenta-mail").val() + ' <b>NOMBRE:</b> ' + $("#new-contacto-mail").val();
    let type_contact = 183;
    let id_operador = $("section").find("#id_operador").val();

    const formData = new FormData();
    formData.append("documento", documento);
    formData.append("cuenta", $("#new-cuenta-mail").val());
    formData.append("fuente", $("#new-fuente-mail").val());
    formData.append("contacto", $("#new-contacto-mail").val());
    formData.append("estado", $("#new-estado").val());       
	$.ajax({
		url: base_url + 'api/solicitud/agendarMail',
		type: 'POST',
		data: formData,
		processData: false,
		contentType: false,
	}).done(function (response) {
		if (response.ok) {
			$("#table_agenda_mail").DataTable().ajax.reload();        
			Swal.fire("¡Perfecto!", response.message, 'success');
			$("#box_add_mail_new").modal("hide");
			saveTrack(comment, type_contact, id_solicitud, id_operador);
		} else {
			Swal.fire("¡Ups!", response.message, 'error');
		}
			
	});
}

var_agendaemail.get_data_template = () => {

	let id_cliente =$('#box_client_title').data('id_cliente');
	if (id_cliente != '') {
		const formData = new FormData();
		formData.append("id_cliente", id_cliente);
		$.ajax({
			url: base_url + 'api/solicitud/get_template_data',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
		});
	}
}

var_agendaemail.previewtemplate = (elem) => {
		let id_template = $(elem).attr('id');
		var template = $.grep(var_agendaemail.data.agendaMailTemplate, (v) => {
			return v.id === id_template;
		});
		var_agendaemail.data.templateSelected = template[0];
		var_agendaemail.showtemplateHtml(template[0]);
}
