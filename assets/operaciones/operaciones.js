$(document).ready(function () {
	$("#vistaBeneficiarios").click();
	base_url = $("input#base_url").val();
	$("#cargando").css("display", "none");
});

var _mSelect2 = $(".select2").select2();

function limpiarFormulario() {
	$(".select2")
		.val(null)
		.trigger("change");
	document.getElementById("formDatosBasicos").reset();
}

function formatDate(input) {
	var datePart = input.match(/\d+/g),
		year = datePart[0],
		month = datePart[1],
		day = datePart[2];
	return day + "/" + month + "/" + year;
}

function vistaBeneficiarios() {
	$("#tp_Beneficiarios")
		.DataTable()
		.ajax.reload();
	base_url =
		$("input#base_url").val() + "operaciones/Operaciones/vistaBeneficiarios";
	$.ajax({
		type: "POST",
		url: base_url,
		success: function (response) {
			$("#main").html(response);
			initTablaBeneficiarios();
			$("#cargando").css("display", "none");
		}
	});
}

function vistaGastos() {
	$("#tp_Gastos")
		.DataTable()
		.ajax.reload();
	base_url = $("input#base_url").val() + "operaciones/Operaciones/vistaGastos";
	$.ajax({
		type: "POST",
		url: base_url,
		success: function (response) {
			$("#main").html(response);
			initTablaGastos();
			$("#cargando").css("display", "none");
		}
	});
}
