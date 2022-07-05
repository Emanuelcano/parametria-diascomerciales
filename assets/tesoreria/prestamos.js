var prestamoDataLine = {};

$(document).ready(function() {
	base_url = $("input#base_url").val();

	buttonsTableHander();

	//INICIALIZA LA TABLA tp_PrestamosPagar
	initTablePrestamosPagar();

	$("#modalConfirmar").modal("hidden", function() {
		prestamoDataLine = {};
	});

	modalConfirm();
});
function initTablePrestamosPagar() {
	let ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/ApiPrestamo/tablePrestamosPagar"
	};
	let columnDefs = [
		{
			targets: [0],
			visible: false,
			searchable: false
		},
		{
			targets: [1, 4, 5],
			createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; vertical-align: middle; text-align: left;"
				);
			}
		},
		{
			targets: [2, 3, 8],
			createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;"
				);
			}
		},
		{
			targets: [0, 6, 7, 9],
			createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;"
				);
			}
		},
		{
			targets: 10,
			createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr("style", "height: 5px; padding: 4px;");
				$(td).attr("align", "left");
			}
		},
		{
			targets: 11,
			orderable: false,
			createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr("style", "height: 5px; padding: 4px;");
				$(td).attr("align", "center");
			}
		}
	];
	let columns = [
		{
			data: "id_solicitud",
			render: function(data, type, row, meta) {
				return '<input type="checkbox" name="chkTodos">';
			}
		},
		{ data: "id_solicitud" },
		{ data: "fecha_ultima_actividad" },
		{
			data: "documento",
			render: function(data, type, row, meta) {
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Numero de Cedula">&nbsp;&nbsp;</i>';
				return data + " " + copy;
			}
		},
		{
			data: "nombres",
			render: function(data, type, row, meta) {
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Nombre Solicitante">&nbsp;&nbsp;</i>';
				return data + " " + copy;
			}
		},
		{ data: "Nombre_Banco" },
		{
			data: "numero_cuenta",
			render: function(data, type, row, meta) {
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Numero de Cuenta">&nbsp;&nbsp;</i>';
				return data + " " + copy;
			}
		},
		{ data: "codigo_TipoCuenta" },
		{
			data: "capital_solicitado",
			render: function(data, type, row, meta) {
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Monto a Transferir">&nbsp;&nbsp;</i>';
				return number_format(data, 2) + " " + copy;
			}
		},
		{ data: "estado" },
		{
			data: "nombre_comp",
			render: function(data, type, row, meta) {
				var subirComprobante =
					"<button class='btn btn-xs bg-navy btnSubirComprobante' title='Subir Comprobante'" +
					"<div class='image-upload'>" +
					"<span><i class='fa fa-upload'></i></span>" +
					"</div></button>";

				var inputComprobante = "<input type='file' class='hidden'>";
				var nombre_comp = row.comprobante ? data : "";

				return subirComprobante + nombre_comp + inputComprobante;
			}
		},
		{
			data: null,
			render: function(data, type, row, meta) {
				var clickProcesando =
					"<button class='btn btn-xs btn-primary btnProcesar' title='Procesando'>" +
					"<i class='fa fa-cogs' ></i></button>";

				var habilitarPago = row.comprobante === null ? " disabled" : "";
				var pagarPrestamo =
					"<button class='btn btn-xs btn-success btnPagar'title='Pagar Prestamo'>" +
					"<i class='fa fa-check-square-o' ></i></button>";

				var rechazarPago =
					"<button class='btn btn-xs btn-danger btnRechazar' title='Rechazar Prestamo'>" +
					"<i class='fa fa-thumbs-down' ></i></button>";

				var posponerPago =
					"<button class='btn btn-xs bg-yellow btnPosponer' title='Transferencia rechazada'>" +
					"<i class='fa fa-exclamation-circle'></i></button>";

				var botones =
					clickProcesando +
					" " +
					pagarPrestamo +
					" " +
					rechazarPago +
					" " +
					posponerPago;
				return botones;
			}
		}
	];
	TablaPaginada(
		"tp_PrestamosPagar",
		2,
		"asc",
		"",
		"",
		ajax,
		columns,
		columnDefs
	);
}
var buttonsTableHander = function() {
	$("#tp_PrestamosPagar tbody")
		.off()
		.on("click", "button", function() {
			var data = $("#tp_PrestamosPagar")
				.DataTable()
				.row($(this).parents("tr"))
				.data();

			if ($(this).hasClass("btnSubirComprobante")) {
				console.log("btnSubirComprobante");
				$(this)
					.parent("td")
					.children("input")
					.click();
			} else if ($(this).hasClass("btnProcesar")) {
				console.log("btnProcesar");
				clickProcesando(
					data.id_solicitud,
					data.id_solicitud,
					data.nombres,
					data.capital_solicitado
				);
			} else if ($(this).hasClass("btnPagar")) {
				console.log("btnPagar");
				clickPagarPrestamo(
					data.id_solicitud,
					data.id_solicitud,
					data.nombres,
					data.capital_solicitado
				);
			} else if ($(this).hasClass("btnRechazar")) {
				console.log("btnRechazar");
				clickRechazarPrestamo(
					data.id_solicitud,
					data.id_solicitud,
					data.nombres,
					data.capital_solicitado
				);
			} else if ($(this).hasClass("btnPosponer")) {
				//console.log("btnPosponer");
				clickPosponerPago(
					data.id_solicitud,
					data.id_solicitud,
					data.nombres,
					data.capital_solicitado
				);
			}
		});

	$("#tp_PrestamosPagar tbody").on("change", "input", function() {
		var data = $("#tp_PrestamosPagar")
			.DataTable()
			.row($(this).parents("tr"))
			.data();
		onfileSelected(
			data.id_solicitud,
			data.id_solicitud,
			data.nombres,
			data.capital_solicitado,
			data.documento
		);
	});

	$("#tp_PrestamosPagar tbody").on("click", "i.copy", function() {
		var text = $(this)
			.parent("td")
			.text();
		copiar(text);
	});
};

var modalConfirm = function() {
	let method;
	$("#btnConfirmar").on("click", function(e) {
		method = $(e.currentTarget).attr("callback");

		if (method === "acceptComprobante") {
			acceptComprobante();
		} else if (method === "pagarPrestamo") {
			pagarPrestamo();
		} else if (method === "procesando") {
			procesando();
		} else if (method === "rechazarPrestamo") {
			rechazarPrestamo();
		} else if (method === "posponerPago") {
			posponerPago();
		}
	});
	$("#btnCancelar").on("click", function(e) {
		method = $(e.currentTarget).attr("callback");

		if (method === "acceptComprobante") {
			$(prestamoDataLine.idInputFile).val("");
		}
		$("#modalConfirmacion").modal("hide");
	});
};

// FILE SELECTED
function onfileSelected(
	idSolicitud,
	indextr,
	nombres,
	capital_solicitado,
	documento
) {
	//SETUP MODAL DIALOG SETUP
	modalOptions.general.id = "#modalConfirmacion";
	modalOptions.general.callback = "acceptComprobante";
	modalOptions.header.title = "Estas seguro de Subir el Comprobante?";
	modalOptions.body.pContent =
		"De " +
		nombres +
		" por el monto de $" +
		number_format(capital_solicitado, 2);
	modalDialogSetup(modalOptions);
	//END MODAL DIALOG SETUP

	//SET OBJECT con datos de la tabla de Prestamos.
	prestamoDataLine.idSolicitud = idSolicitud;
	prestamoDataLine.idInputFile = $(event.target);
	prestamoDataLine.file = event.target.files[0];
	prestamoDataLine.fakePath = $(event.target).val();
	prestamoDataLine.documento = documento;
	//
}
function acceptComprobante() {
	//ARMADO DE FORMDATA//
	const formData = new FormData();
	const TRANSFERENCIA_DESEMBOLSO = 16;

	if (prestamoDataLine.file !== null) {
		formData.append("file", prestamoDataLine.file);
		formData.append("file", prestamoDataLine.fakePath);
	}
	if (prestamoDataLine.idSolicitud !== null) {
		formData.append("id_solicitud", prestamoDataLine.idSolicitud);
	}

	if (prestamoDataLine.documento !== null) {
		formData.append("documento", prestamoDataLine.documento);
	}
	formData.append("id_img_required", TRANSFERENCIA_DESEMBOLSO);
	//END

	base_url = $("input#base_url").val() + "api/galery/subir_imagen";
	$.ajax({
		type: "POST",
		url: base_url,
		processData: false,
		contentType: false,
		data: formData,
		beforeSend: function() {
			disabledButtons(true);
		},
		complete: function() {
			disabledButtons(false);
		},
		success: function(response) {
			if (response.status.ok) {
				$("#modalConfirmacion").modal("hide");
				$("#tp_PrestamosPagar")
					.DataTable()
					.ajax.reload();
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#alertMessage").html("");
			if (typeof jqXHR.responseJSON.errors == "string") {
				modalOptions.body.alert.message.push(jqXHR.responseJSON.errors);
			} else {
				$.each(jqXHR.responseJSON.errors, function(i, el) {
					modalOptions.body.alert.message.push(el + "<br>");
				});
			}

			modalOptions.body.alert.type = "alert-danger";
			modalAlertMessage(modalOptions);
		}
	});
}

function clickPagarPrestamo(idSolicitud, indextr, nombres, capital_solicitado) {
	//SETUP MODAL DIALOG SETUP
	modalOptions.general.id = "#modalConfirmacion";
	modalOptions.general.callback = "pagarPrestamo";
	modalOptions.header.title = "Estas seguro de Pagar el Prestamo?";
	modalOptions.body.pContent =
		"A " +
		nombres +
		" por el monto de $" +
		number_format(capital_solicitado, 2);
	modalDialogSetup(modalOptions);
	//END MODAL DIALOG SETUP

	prestamoDataLine.idSolicitud = idSolicitud;
	prestamoDataLine.indextr = indextr;
}
function pagarPrestamo() {
	const formData = new FormData();
	if (prestamoDataLine.idSolicitud !== null) {
		formData.set("id", prestamoDataLine.idSolicitud);
	}

	base_url = $("input#base_url").val() + "api/ApiPrestamo/pagarPrestamo";
	let params = {
		url: base_url,
		formData: formData
	};
	sendRequest(params);
}

function clickRechazarPrestamo(
	idSolicitud,
	indextr,
	nombres,
	capital_solicitado
) {
	//SETUP MODAL DIALOG SETUP
	modalOptions.general.id = "#modalConfirmacion";
	modalOptions.general.callback = "rechazarPrestamo";
	modalOptions.header.title = "Estas seguro de Rechazar el Prestamo?";
	modalOptions.body.pContent =
		"A " +
		nombres +
		" por el monto de $" +
		number_format(capital_solicitado, 2);
	modalDialogSetup(modalOptions);
	//END MODAL DIALOG SETUP

	prestamoDataLine.idSolicitud = idSolicitud;
	prestamoDataLine.indextr = indextr;
}
function rechazarPrestamo() {
	const formData = new FormData();
	if (prestamoDataLine.idSolicitud !== null) {
		formData.set("id", prestamoDataLine.idSolicitud);
	}

	base_url = $("input#base_url").val() + "api/ApiPrestamo/rechazar";
	let params = {
		url: base_url,
		formData: formData
	};
	sendRequest(params);
}

function clickPosponerPago(idSolicitud, indextr, nombres, capital_solicitado) {
	//SETUP MODAL DIALOG SETUP
	modalOptions.general.id = "#modalConfirmacion";
	modalOptions.general.callback = "posponerPago";
	modalOptions.header.title = "Estas seguro de rechazar la Transferencia?";
	modalOptions.body.pContent =
		"A " +
		nombres +
		" por el monto de $" +
		number_format(capital_solicitado, 2);
	modalDialogSetup(modalOptions);
	//END MODAL DIALOG SETUP

	prestamoDataLine.idSolicitud = idSolicitud;
	prestamoDataLine.indextr = indextr;
}
function posponerPago() {
	const formData = new FormData();
	if (prestamoDataLine.idSolicitud !== null) {
		formData.set("id", prestamoDataLine.idSolicitud);
	}

	base_url = $("input#base_url").val() + "api/ApiPrestamo/posponerPago";
	let params = {
		url: base_url,
		formData: formData
	};
	sendRequest(params);
}

function clickProcesando(idSolicitud, indextr, nombres, capital_solicitado) {
	//SETUP MODAL DIALOG SETUP
	modalOptions.general.id = "#modalConfirmacion";
	modalOptions.general.callback = "procesando";
	modalOptions.header.title = "Estas seguro de cambiar el estado a Procesando?";
	modalOptions.body.pContent =
		"A " +
		nombres +
		" por el monto de $" +
		number_format(capital_solicitado, 2);
	modalDialogSetup(modalOptions);
	//END MODAL DIALOG SETUP

	prestamoDataLine.idSolicitud = idSolicitud;
	prestamoDataLine.indextr = indextr;
}
function procesando() {
	const formData = new FormData();

	if (prestamoDataLine.idSolicitud !== null) {
		formData.set("id", prestamoDataLine.idSolicitud);
	}
	base_url = $("input#base_url").val() + "api/ApiPrestamo/procesar";
	let params = {
		url: base_url,
		formData: formData
	};
	sendRequest(params);
}

function sendRequest(params) {
	$.ajax({
		type: "POST",
		url: params.url,
		processData: false,
		contentType: false,
		data: params.formData,
		dataType : "json",
		beforeSend: function() {
			disabledButtons(true);
		},
		complete: function() {
			disabledButtons(false);
		},
		success: function(response) {
			//console.log(response);
			$("#alertMessage").html("");
			modalOptions.body.alert.message = [];
			if (typeof response.response.respuesta !== 'undefined' && response.response.respuesta ) {
				$("#modalConfirmacion").modal("hide");
				$("#tp_PrestamosPagar")
					.DataTable()
					.ajax.reload();
				toastr["success"](response.response.mensaje, "Notifiación.");
			} else {
				console.log(response.response.error);
				//if(response.response.error.length > 0){
				$.each(response.response.error, function(i, el) {
					modalOptions.body.alert.message.push(el);
				});
				modalOptions.body.alert.type = "alert-danger";
				modalAlertMessage(modalOptions);
				//}
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			$("#alertMessage").html("");
			console.log([jqXHR.responseJSON, textStatus, errorThrown]);
			modalOptions.body.alert.message = [];
			modalOptions.body.alert.message.push(
				"Error interno comuniquese con soporte."
			);
			modalOptions.body.alert.type = "alert-danger";
			modalAlertMessage(modalOptions);
		}
	});
}
