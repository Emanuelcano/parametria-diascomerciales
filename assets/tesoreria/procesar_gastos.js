var prestamoDataLine = {};

$(document).ready(function () {
	base_url = $("input#base_url").val();

	buttonsTableHander();
	//INICIALIZA LA TABLA tp_procesar_gastos
	initTableProcesarPago();

	$("#modalConfirmar").modal("hidden", function () {
		prestamoDataLine = {};
	});

	modalConfirm();
});
function initTableProcesarPago() {
	let ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/ApiGastos/tableProcesarGasto"
	};
	let columns = [
		{ data: "convencion_tipoDocumento" },
		{
			data: "nro_documento",
			render: function (data, type, row, meta) {
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Documento">&nbsp;&nbsp;</i>';
				return data + " " + copy;
			}
		},
		{
			data: "denominacion",
			render: function (data, type, row, meta) {
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Beneficiario">&nbsp;&nbsp;</i>';
				return data + " " + copy;
			}
		},
		{
			data: "nro_factura",
			render: function (data, type, row, meta) {
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Nro Factura">&nbsp;&nbsp;</i>';
				return data + " " + copy;
			}
		},
		// {
		// 	data: "fecha_vencimiento",
		// 	render: function(data, type, row, meta) {
		// 		var copy =
		// 			'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Fecha Vencimiento">&nbsp;&nbsp;</i>';
		// 		return moment(data).format("DD-MM-YYYY") + " " + copy;
		// 	}
		// },
		{ data: "Nombre_Banco" },
		{ data: "Nombre_TipoCuenta" },
		{
			data: "nro_cuenta1",
			render: function (data, type, row, meta) {
				// console.log(data);
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Nro Cuenta">&nbsp;&nbsp;</i>';
				if (data == 0 || data == null) {
					return row.nro_cuenta2 + " " + copy;
				} else {
					return data + " " + copy;
				}
			}
		},
		{
			data: "total_pagar",
			render: function (data, type, row, meta) {
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Total a Pagar">&nbsp;&nbsp;</i>';
				return number_format(data, 2) + " " + copy;
			}
		},
		{
			data: "concepto",
			render: function (data, type, row, meta) {
				var copy =
					'<i class="fa fa-clone pull-right copy" style="color: red;" title="Copiar Descripcion">&nbsp;&nbsp;</i>';
				return data + " " + copy;
			}
		},
		// {
		// 	data: null,
		// 	render: function(data, type, row, meta) {
		// 		if (data.estado == "3") {
		// 			return "APROBADO";
		// 		}
		// 	}
		// },

		{
			data: "url_comprobante_pago",
			render: function (data, type, row, meta) {
				var subirComprobante =
					"<button class='btn btn-xs bg-navy btnSubirComprobante' title='Subir Comprobante'" +
					"<div class='image-upload'>" +
					"<span><i class='fa fa-upload'></i></span>" +
					"</div></button>";

				var inputComprobante = "<input type='file' class='hidden'>";
				var url_comprobante_pago = row.comprobante ? data : "";

				return subirComprobante + url_comprobante_pago + inputComprobante;
			}
		},
		{
			data: null,
			render: function (data, type, row, meta) {
				var ProcesarPago =
					"<button class='btn btn-xs btn-success btnPagar' data-estado ='5' title='Procesar Pago'>" +
					"<i class='fa fa-check-square-o' ></i></button>";
				var VerProcesaPago =
					"<button class='btn btn-xs bg-navy btnInfo' title='Ver Info'>" +
					"<i class='fa fa-eye' ></i></button>";
				return ProcesarPago + " " + VerProcesaPago;
			}
		}
	];
	let columnDefs = [
		{
			targets: [9, 10],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr("style", "text-align: center;");
			}
		},
		{
			targets: [8],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"max-width: 20em;text-overflow:ellipsis;white-space:nowrap;overflow:hidden;"
				);
			}
		}
	];
	TablaPaginada(
		"tp_ProcesarGastos",
		2,
		"asc",
		"",
		"",
		ajax,
		columns,
		columnDefs
	);
}
var buttonsTableHander = function () {
	$("#tp_ProcesarGastos tbody")
		.off()
		.on("click", "button", function () {
			var data = $("#tp_ProcesarGastos")
				.DataTable()
				.row($(this).parents("tr"))
				.data();

			if ($(this).hasClass("btnSubirComprobante")) {
				console.log("btnSubirComprobante");
				$(this)
					.parent("td")
					.children("input")
					.click();
			} else if ($(this).hasClass("btnPagar")) {
				console.log("btnPagar");
				clickProcesarGasto(data.id_gasto, data.denominacion, data.total_pagar);
			} else if ($(this).hasClass("btnInfo")) {
				console.log("btnInfo");
				modalInfo(
					data.id_gasto,
					data.convencion_tipoDocumento,
					data.nro_documento,
					data.denominacion,
					data.concepto,
					data.nro_factura,
					data.Nombre_Banco,
					data.Nombre_TipoCuenta,
					data.nro_cuenta1,
					data.nro_cuenta2,
					data.total_pagar,
					data.url_comprobante_pago
				);
				buttonsModalInfo(data); // Se envia data completa de row table a bottones del footer modal
			}
		});

	$("#tp_ProcesarGastos tbody").on("change", "input", function () {
		var data = $("#tp_ProcesarGastos")
			.DataTable()
			.row($(this).parents("tr"))
			.data();
		onfileSelected(data.id_gasto);
	});

	$("#tp_ProcesarGastos tbody").on("click", "i.copy", function () {
		var text = $(this)
			.parent("td")
			.text();
		copiar(text.replace(/,/g, "").trim());
	});
};

var buttonsModalInfo = function (data) {
	$("#modalInfo .modal-footer")
		.off()
		.on("click", "button", function () {
			if ($(this).hasClass("btnSubirComprobante")) {
				console.log("btnSubirComprobante Modal");
				$(this)
					.parent("div")
					.children("input")
					.click();
			} else if ($(this).hasClass("btnPagar")) {
				console.log("btnPagar Modal");
				clickProcesarGasto(data.id_gasto, data.denominacion, data.total_pagar);
			}
		});

	$("#modalInfo .modal-footer").on("change", "input", function () {
		onfileSelected(data.id_gasto);
	});

	$("#modalInfo .modal-body").on("click", "i.copiar", function () {
		// alert("copiar text modal");
		var text = $(this)
			.parent("span")
			.text();
		copiar(text.replace(/,/g, "").trim());
	});
};

$(".modal-body").css("line-height", "2em");

$("#btnCerrarInfo").on("click", function (e) {
	$("#modalInfo").modal("hide");
});

function modalInfo(
	idGasto,
	tipo_documento,
	nro_documento,
	denominacion,
	concepto,
	nro_factura,
	nombre_banco,
	tipo_cuenta,
	cuenta1,
	cuenta2,
	total_pagar,
	url_comprobante_pago
) {
	if (cuenta1 == 0 || cuenta1 == null) {
		cuenta1 = cuenta2;
	}
	// alert("Holaaaa");
	modalOptions.general.id = "#modalInfo";
	modalOptions.header.title = "Informacion para Pago del Gasto";
	modalOptions.body.pContent =
		"<div class='container'><div class='row'><div class='col-md-12'><span class='col-md-2 text-right'><b>Tipo Documento</b></span><span class='col-md-4 text-left'>" +
		tipo_documento +
		"</span></div><div class='col-md-12'><span class='col-md-2 text-right'><b>Nro Documento</b></span><span class='col-md-4 txt-left'>" +
		nro_documento +
		" " +
		"<i class='fa fa-clone copiar' style='color:red;' title='Copiar Nro Documento'>&nbsp;&nbsp;</i></span></div><div class='col-md-12'><span class='col-md-2 text-right'><b>Beneficiario</b></span><span class='col-md-4 txt-left'>" +
		denominacion +
		" " +
		"<i class='fa fa-clone copiar' style='color:red;' title='Copiar Beneficiario'>&nbsp;&nbsp;</i></span></div><div class='col-md-12'><span class='col-md-2 text-right'><b>Nro Factura</b></span><span class='col-md-4 txt-left'>" +
		nro_factura +
		" " +
		"<i class='fa fa-clone copiar' style='color:red;' title='Copiar Nro Factura'>&nbsp;&nbsp;</i></span></div><div class='col-md-12'><span class='col-md-2 text-right'><b>Banco</b></span><span class='col-md-4 txt-left'>" +
		nombre_banco +
		"</span></div><div class='col-md-12'><span class='col-md-2 text-right'><b>Tipo Cuenta</b></span><span class='col-md-4 txt-left'>" +
		tipo_cuenta +
		"</span></div><div class='col-md-12'><span class='col-md-2 text-right'><b>Nro Cuenta</b></span><span class='col-md-4 txt-left'>" +
		cuenta1 +
		" " +
		"<i class='fa fa-clone copiar' style='color:red;' title='Copiar Nro Cuenta'>&nbsp;&nbsp;</i></span></div><div class='col-md-12'><span class='col-md-2 text-right'><b>Monto Pagar</b></span><span class='col-md-4 txt-left'>" +
		number_format(total_pagar, 2) +
		" " +
		"<i class='fa fa-clone copiar' style='color:red;' title='Copiar Nro Documento'>&nbsp;&nbsp;</i></span></div><div class='col-md-12'><span class='col-md-2 text-right'><b>Descripcion</b></span><span class='col-md-4 txt-left'>" +
		concepto +
		" " +
		"<i class='fa fa-clone copiar' style='color:red;' title='Copiar Concepto'>&nbsp;&nbsp;</i></span></div><div class='col-md-12'><span class='col-md-2 text-right'><b>Comprobante</b></span><span class='col-md-3 txt-left'><a target='_blank' href=" +
		url_comprobante_pago +
		">" +
		url_comprobante_pago +
		"</a>" +
		"</div>           </div></div>";
	modalDialogSetup(modalOptions);
}

var modalConfirm = function () {
	let method;
	$("#btnConfirmar").on("click", function (e) {
		method = $(e.currentTarget).attr("callback");
		if (method === "acceptComprobante") {
			acceptComprobante();
		} else if (method === "procesarGasto") {
			procesarGasto();
		}
	});
	$("#btnCancelar").on("click", function (e) {
		method = $(e.currentTarget).attr("callback");

		if (method === "acceptComprobante") {
			$(prestamoDataLine.idInputFile).val("");
		}
		$("#modalConfirmacion").modal("hide");
	});
};

// FILE SELECTED
function onfileSelected(id_gasto) {
	//SETUP MODAL DIALOG SETUP
	modalOptions.general.id = "#modalConfirmacion";
	modalOptions.general.callback = "acceptComprobante";
	modalOptions.header.title = "Estas seguro de Subir el Comprobante?";
	modalOptions.body.pContent = "ID Gasto " + id_gasto;
	modalDialogSetup(modalOptions);
	//END MODAL DIALOG SETUP

	//SET OBJECT con datos de la tabla de Prestamos.
	prestamoDataLine.idGasto = id_gasto;
	prestamoDataLine.idInputFile = $(event.target);
	prestamoDataLine.file = event.target.files[0];
	prestamoDataLine.fakePath = $(event.target).val();
	//
}
function acceptComprobante() {
	//ARMADO DE FORMDATA//
	const formData = new FormData();

	if (prestamoDataLine.file !== null) {
		formData.append("file", prestamoDataLine.file);
		formData.append("file", prestamoDataLine.fakePath);
	}
	if (prestamoDataLine.idGasto !== null) {
		formData.append("id_gasto", prestamoDataLine.idGasto);
	}
	//END

	base_url =
		$("input#base_url").val() + "api/ApiGastos/comprobanteProcesarGasto";
	$.ajax({
		type: "POST",
		url: base_url,
		processData: false,
		contentType: false,
		data: formData,
		beforeSend: function () {
			disabledButtons(true);
		},
		complete: function () {
			disabledButtons(false);
		},
		success: function (response) {
			if (response.status.ok) {
				$("#modalConfirmacion").modal("hide");
				$("#tp_ProcesarGastos")
					.DataTable()
					.ajax.reload();
				Swal.fire({
					title: "¡Perfecto!",
					text: response.message,
					icon: "success"
				});
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$("#alertMessage").html("");
			if (typeof jqXHR.responseJSON.errors == "string") {
				modalOptions.body.alert.message.push(jqXHR.responseJSON.errors);
			} else {
				$.each(jqXHR.responseJSON.errors, function (i, el) {
					modalOptions.body.alert.message.push(el + "<br>");
				});
			}

			modalOptions.body.alert.type = "alert-danger";
			modalAlertMessage(modalOptions);
		}
	});
}

function clickProcesarGasto(idGasto, denominacion, total_pagar) {
	//SETUP MODAL DIALOG SETUP
	modalOptions.general.id = "#modalConfirmacion";
	modalOptions.general.callback = "procesarGasto";
	modalOptions.header.title =
		"Estas seguro de actualizar el estado del Gasto a PAGADO?";
	modalOptions.body.pContent =
		"<B>Id:</b>" +
		idGasto +
		"<br><b>Beneficiario:</b>  " +
		denominacion +
		"<br><b>Monto:</b> $" +
		number_format(total_pagar, 2) +
		"<br><b>Cuenta Debitar:</b> " +
		"<span style='background:#FFEBB0;'>" +
		$("#select_banco option:selected").text() +
		" - " +
		$("#select_banco option:selected").data("cuenta");
	+"</span";
	modalDialogSetup(modalOptions);
	//END MODAL DIALOG SETUP
	prestamoDataLine.idGasto = idGasto;
}
function procesarGasto() {
	var estado = $(".btnPagar").attr("data-estado");
	const formData = new FormData();
	if (prestamoDataLine.idGasto !== null) {
		formData.set("id_gasto", prestamoDataLine.idGasto);
		formData.set("id_estado", estado);
	}
	base_url = $("input#base_url").val() + "api/ApiGastos/actualiza_estado_gasto";
	let params = {
		url: base_url,
		formData: formData
	};
	sendRequestProcesar(params);
}

function sendRequestProcesar(params) {
	$.ajax({
		type: "POST",
		url: params.url,
		processData: false,
		contentType: false,
		data: params.formData,
		beforeSend: function () {
			disabledButtons(true);
		},
		complete: function () {
			disabledButtons(false);
		},
		success: function (response) {
			$("#alertMessage").html("");
			modalOptions.body.alert.message = [];

			if (response.status.ok) {
				$("#modalConfirmacion").modal("hide");
				$("#tp_ProcesarGastos")
					.DataTable()
					.ajax.reload();
				toastr["success"](response.message, "Actualizacion Gasto a Pagado.");
			} else {
				console.log(response.error);
				//if(response.response.error.length > 0){
				$.each(response.error, function (i, el) {
					modalOptions.body.alert.message.push(el);
				});
				modalOptions.body.alert.type = "alert-danger";
				modalAlertMessage(modalOptions);
				//}
			}
		},
		error: function (jqXHR, textStatus, errorThrown) {
			$("#alertMessage").html("");
			modalOptions.body.alert.message.push(
				"No se pudo completar la actualizacion."
			);
			modalOptions.body.alert.type = "alert-danger";
			modalAlertMessage(modalOptions);
		}
	});

	setTimeout(() => {
		$("#btnCerrarInfo").click();
	}, 500);
}

$("select").change(function () {
	// var checkedButtons = false;
	$(this)
		.find("option:selected")
		.each(function () {
			$("#info_banco").html(
				"<p>Numero de cuenta: " +
				$(this).attr("data-cuenta") +
				"</p><p>Tipo de cuenta: " +
				$(this).attr("data-tipo") +
				"</p><p>Estado: " +
				$(this).attr("data-estado") +
				"</p>"
			);
		});
});

$("#date_range").daterangepicker(
	{
		autoUpdateInput: false,
		ranges: {
			Hoy: [moment(), moment()],
			Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
			"Últimos 7 Días": [moment().subtract(6, "days"), moment()],
			"Últimos 30 Días": [moment().subtract(29, "days"), moment()],
			"Mes Anterior": [moment().startOf("month"), moment().endOf("month")],
			"Últimos Mes": [
				moment()
					.subtract(1, "month")
					.startOf("month"),
				moment()
					.subtract(1, "month")
					.endOf("month")
			]
		},
		locale: {
			format: "DD-MM-YYYY",
			separator: " | ",
			applyLabel: "Guardar",
			cancelLabel: "Cancelar",
			fromLabel: "Desde",
			toLabel: "Hasta",
			customRangeLabel: "Personalizar",
			daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
			monthNames: [
				"Enero",
				"Febrero",
				"Marzo",
				"Abril",
				"Mayo",
				"Junio",
				"Julio",
				"Agosto",
				"Septiembre",
				"Octubre",
				"Noviembre",
				"Diciembre"
			],
			firstDay: 1
		},
		startDate: moment().subtract(29, "days"),
		endDate: moment(),
		timePicker: false
	},

	function (start, end) {
		$("#daterange-btn span").html(
			start.format("DD-MM-YYYY") + " - " + end.format("DD-MM-YYYY")
		);
	}
);

$("#date_range").on("apply.daterangepicker", function (ev, picker) {
	$(this).val(
		picker.startDate.format("DD-MM-YYYY") +
		" | " +
		picker.endDate.format("DD-MM-YYYY")
	);
});

$("#date_range").on("cancel.daterangepicker", function (ev, picker) {
	$(this).val("");
});

table_search = $("#table_search_gasto").DataTable({
	iDisplayLength: 10,
	responsive: true,
	processing: true,
	language: spanish_lang,
	paging: true,
	info: true,
	searching: true,
	columns: [
		{ data: "id_gasto" },
		{
			data: "fecha_ultima_modificacion",
			render: function (data, type, row, meta) {
				var fecha_ultima_modificacion = moment(data).format("DD/MM/YYYY");
				return fecha_ultima_modificacion;
			}
		},
		{ data: "nro_factura" },
		{ data: "denominacion" },
		{
			data: "fecha_vencimiento",
			render: function (data, type, row, meta) {
				var fecha_vencimiento = moment(data).format("DD/MM/YYYY");
				return fecha_vencimiento;
			}
		},
		{ data: "total_pagar", render: $.fn.dataTable.render.number(".", ",", 2) },
		{
			data: "estado",
			render: function (data, type, row, meta) {
				switch (data) {
					case "1":
						estado = "PENDIENTE";
						break;
					case "2":
						estado = "ANULADO";
						break;
					case "3":
						estado = "APROBADO";
						break;
					case "4":
						estado = "RECHAZADO";
						break;
					case "5":
						estado = "PAGADO";
						break;
					case "6":
						estado = "NO PAGADO";
						break;
					default:
						estado = data;
						break;
				}
				return estado;
			}
		},
		{
			data: null,
			render: function (data, type, row, meta) {
				if (row.estado != "1") {
					var disabled = "disabled";
				}
				var vista = true;
				var verGasto =
					'<a class="btn btn-xs bg-navy" title="Ver Datos del gasto" onclick="cargarGastosTesoreria(' +
					row.id_gasto +
					"," +
					vista +
					');"><i class="fa fa-eye" ></i></a>';
				var botones = verGasto;
				return botones;
			}
		}
	],
	columnDefs: [
		{
			targets: [0],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr("style", "display:none");
			}
		},
		{
			targets: [1, 6, 7],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; text-align: center; vertical-align: middle"
				);
			}
		},
		{
			targets: [2, 3],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; text-align: left; vertical-align: middle"
				);
			}
		},
		{
			targets: [5],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; text-align: center; vertical-align: middle"
				);
			}
		},
		{
			targets: [4],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;"
				);
			}
		}
	]
});

$("#section_search_gasto #form_search_gasto").on("submit", function (event) {
	event.preventDefault();
	// $("#section_search_gasto #result_gasto").show();

	var data = $(this).serialize();
	buscarGastoTesoreria(data);
});

$("#section_search_gasto #form_search_gasto").on("reset", function (event) {
	$("#result_gasto").css('display', 'none')
});

$("#section_search_gasto #search_gasto").on("keyup", function () {
	if ($(this).val().length == 0) {
		$("#result_gasto").css('display', 'none')
		$("#texto").empty();
	}
});

function buscarGastoTesoreria(search) {
	paginacion = 0;
	let base_url = $("#base_url").val();
	table_search.processing(true);
	$.ajax({
		url: base_url + "api/ApiGastos/tabla_gastos_busqueda",
		type: "POST",
		dataType: "json",
		data: search
	})
		.done(function (response) {
			table_search.processing(false);
			table_search.clear();
			table_search.rows.add(response.gastos);
			table_search.draw();
			$("#result_gasto").css('display', 'block');
		})
		.fail(function (response) {
			//console.log("error");
		})
		.always(function (response) {
			//console.log("complete");
		});
}
