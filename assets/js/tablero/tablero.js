$(document).ready(function() {
	base_url = $("input#base_url").val();
	vistaIndicadores();
	setInterval(function() {
		vistaIndicadores();
	}, 900000);

	$("#modo-oscuro").click("on", function() {
		if ($("body").hasClass("modo-oscuro")) {
			$("body").removeClass("modo-oscuro");
			$("#modo-oscuro").html("Modo Oscuro");
			$("#modo-oscuro").removeClass("btn-default");
			$("#modo-oscuro").addClass("bg-black");
		} else {
			$("body").addClass("modo-oscuro");
			$("#modo-oscuro").html("Modo Luminoso");
			$("#modo-oscuro").removeClass("bg-black");
			$("#modo-oscuro").addClass("btn-default");
		}
	});
});

function vistaIndicadores() {
	base_url = $("input#base_url").val() + "tablero/Tablero/indicadores";

	$.ajax({
		type: "GET",
		url: base_url,

		success: function(response) {
			validarSession(response);
			$("#main").html(response);
			//TablaPaginada('tp_Indicadores',3,'desc');
			$("#tp_Indicadores").DataTable({
				order: [[19, "asc"]]
			});
		}
	});
}
