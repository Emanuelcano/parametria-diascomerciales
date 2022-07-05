$(document).ready(function () {
	base_url = $("input#base_url").val();
	$("#cargando").css("display", "none");
});

//Tabla Beneficiarios
function initTablaBeneficiarios() {
	let ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/Apibeneficiario/tabla_beneficiarios"
	};

	let columnDefs = [
		{
			pageLength: 15,
			lengthChange: true,
			paging: true
		},
		{
			targets: [0],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr("style", "display:none");
			}
		},
		{
			targets: [1],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"font-size: 12px; vertical-align: middle; text-align: center;padding: 4px;"
				);
			}
		},
		{
			targets: [2],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"font-size: 12px; vertical-align: middle; text-align: left;padding: 4px;"
				);
			}
		},
		{
			targets: [3],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr("style", "padding: 4px;");
			}
		}
		
	];
	let columns = [
		{ data: "id_beneficiario" },
		{ data: "nro_documento" },
		{ data: "denominacion" },
		{
			data: "estado",
			render: function (data, type, row, meta) {
				var estado = data == 1 ? "ACTIVO" : "INACTIVO";
				return estado;
			}
		},
		{
			data: null,
			render: function (data, type, row, meta) {
				var vista = true;
				
				var verBeneficiario =
					'<a class="btn btn-xs bg-navy" style="margin-left: 37px;" title="Ver Datos del beneficiario"' +
					'onclick="cargarBeneficiario(' +
					row.id_beneficiario +
					"," +
					vista +
					');">' +
					'<i class="fa fa-eye" ></i>' +
					"</a>";
				var editarBeneficiario =
					'<a class="btn btn-xs btn-success" title="Editar beneficiario"' +
					'onclick="cargarBeneficiario(' +
					row.id_beneficiario +
					');">' +
					'<i class="fa fa-pencil-square-o" ></i>' +
					"</a>";
				var cambiarEstado =
					'<a class="btn btn-xs bg-yellow" title="Cambiar Estado"' +
					'onclick="cambiarestado(' +
					row.estado +
					"," +
					row.id_beneficiario +
					');">' +
					'<i class="fa fa-exchange" ></i>' +
					"</a>";
				var botones =
					verBeneficiario + " " + editarBeneficiario + " " + cambiarEstado;
				return botones;
			}
		}
	];

	TablaPaginada(
		"tp_Beneficiarios",
		0,
		"asc",
		"",
		"",
		ajax,
		columns,
		columnDefs
	);
}

//Carga un beneficiario para ver o editar
function cargarBeneficiario(id, vista = "") {
	var data = {
		id_beneficiario: id,
		vista: vista
	};
	var base_url =
		$("input#base_url").val() + "api/Apibeneficiario/cargar_beneficiario";
	$.ajax({
		type: "POST",
		url: base_url,
		data: data,
		success: function (response) {
			$("#main").html(response);
			initTablaBeneficiarios();
		}
	});
}

//Cambia a ACTIVO o INACTIVO un beneficiario
function cambiarestado(estado, id_beneficiario) {
	if (estado == 1) {
		var cambioEstado = 0;
	} else {
		cambioEstado = 1;
	}
	var data = {
		cambioEstado: cambioEstado,
		id_beneficiario: id_beneficiario
	};
	var base_url =
		$("input#base_url").val() + "api/Apibeneficiario/cambio_estado";
	$.ajax({
		type: "POST",
		url: base_url,
		data: data,
		success: function (response) {
			if (response["errors"]) {
				alert(response["errors"]);
			} else {
				$("#tp_Beneficiarios")
					.DataTable()
					.ajax.reload();
			}
		}
	});
}

//ABM BENEFICIARIO

function abmTipoBeneficiario() {
	$("#mostrartipo").modal("show");
}
function abmRubroBeneficiario() {
	$("#mostrarrubro").modal("show");
}
function abmFormaPagoBeneficiario() {
	$("#mostrarfomapago").modal("show");
}
function abmDocumentoBeneficiario() {
	$("#mostrardocumento").modal("show");
}
function abmMoneda() {
	$("#mostrarmoneda").modal("show");
}

// Guardar un tipo especifico de beneficiario
function guardarTipoBeneficiario() {
	var data = {
		denominacion: $("#den_ben").val()
	};
	var base_url =
		$("input#base_url").val() + "api/Apibeneficiario/guardar_tipo_beneficiario";
	if (data["denominacion"] == "") {
		alert("Debe ingresar una denominacion de tipo de beneficiario");
	} else {
		$.ajax({
			type: "POST",
			url: base_url,
			data: data,
			success: function (response) {
				if (response.errors) {
					alert(response.errors);
				} else {
					alert(response.message);
					$("#tipoBeneficiario").append(
						"<option value= " +
						response.id +
						">" +
						$("#den_ben").val() +
						"</option>"
					);
				}
			}
		});
	}
}

// Guardar un tipo especifico de moneda
function guardarMoneda() {
	var data = {
		denominacion: $("#den_moneda").val()
	};
	var base_url =
		$("input#base_url").val() + "api/Apibeneficiario/guardar_moneda";
	if (data["denominacion"] == "") {
		alert("Debe ingresar una denominacion de moneda");
	} else {
		$.ajax({
			type: "POST",
			url: base_url,
			data: data,
			success: function (response) {
				if (response.errors) {
					alert(response.errors);
				} else {
					alert(response.message);
					$("#moneda").append(
						"<option value= " +
						response.id +
						">" +
						$("#den_moneda").val() +
						"</option>"
					);
				}
			}
		});
	}
}

// Guardar un tipo especifico de rubro
function guardarRubroBeneficiario() {
	var data = {
		denominacion: $("#den_rub").val()
	};
	var base_url =
		$("input#base_url").val() + "api/Apibeneficiario/guardar_rubro";
	if (data["denominacion"] == "") {
		alert("Debe ingresar una denominacion de rubro de beneficiario");
	} else {
		$.ajax({
			type: "POST",
			url: base_url,
			data: data,
			success: function (response) {
				if (response.errors) {
					alert(response.errors);
				} else {
					alert(response.message);
					$("#rubroBeneficiario").append(
						"<option value= " +
						response.id +
						">" +
						$("#den_rub").val() +
						"</option>"
					);
				}
			}
		});
	}
}

// Guardar un tipo especifico de forma de pago
function guardarFormaPago() {
	var data = {
		denominacion: $("#den_fp").val()
	};
	var base_url = $("input#base_url").val() + "api/Apibeneficiario/forma_pago";
	if (data["denominacion"] == "") {
		alert("Debe ingresar una denominacion de forma de pago");
	} else {
		$.ajax({
			type: "POST",
			url: base_url,
			data: data,
			success: function (response) {
				if (response.errors) {
					alert(response.errors);
				} else {
					alert(response.message);
					$("#formaPago").append(
						"<option value= " +
						response.id +
						">" +
						$("#den_fp").val() +
						"</option>"
					);
				}
			}
		});
	}
}

// Guardar un tipo especifico de tipo de docuemnto
function guardarTipoDocumento() {
	var data = {
		nombre_tipoDocumento: $("#nombre").val(),
		convencion_tipoDocumento: $("#convencion").val(),
		codigo: $("#cod").val(),
		id_estado_tipoDocumento: 1
	};
	var base_url =
		$("input#base_url").val() + "api/Apibeneficiario/tipo_documento";
	if (
		data["nombre_tipoDocumento"] == "" ||
		data["convencion_tipoDocumento"] == "" ||
		data["codigo"] == ""
	) {
		alert("Los tres datos son obligatorios");
	} else {
		$.ajax({
			type: "POST",
			url: base_url,
			data: data,
			success: function (response) {
				if (response.errors) {
					alert(response.errors);
				} else {
					alert(response.message);
					$("#tipoDocumento").append(
						"<option value= " +
						response.id +
						">" +
						$("#convencion").val() +
						"</option>"
					);
				}
			}
		});
	}
}
function nuevoBeneficiario() {
	$("#formSection").css("display", "block");
	$(".select2")
		.val(null)
		.trigger("change");
	document.getElementById("formDatosBasicos").reset();
	$("#table_beneficiario").css("display", "none");
}
