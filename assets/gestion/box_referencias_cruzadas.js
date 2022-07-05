$(document).ready(function () {
	var acc_endeud = document.getElementsByClassName("accordion_referencias_cruzadas");
	var iendeud;
	for (iendeud = 0; iendeud < acc_endeud.length; iendeud++) {
		acc_endeud[iendeud].addEventListener("click", function() {
			this.classList.toggle("active");
			if ($(this).hasClass('active')) {
				$('.title_button_verlistasr').text('REFERENCIAS_CRUZADAS');
				loadReferenciasCruzadas();
			} else {
				$('.title_button_verlistasr').text('VER REFERENCIAS_CRUZADAS');
			}
			var panel_endeud = this.nextElementSibling;
			if (panel_endeud.style.display === "block") {
				panel_endeud.style.display = "none";
			} else {
				panel_endeud.style.display = "block";
			}
		});
	}

	
}); // fin del ready document

function loadReferenciasCruzadas(){
	$("#infoLoadingReferencias").show();
	$("#referencia_cruzadas").hide();
	
	$.ajax({
		url		:  base_url + 'atencion_cliente/Gestion/getReferenciasCruzadasView/' +  $("#client").data('number_doc'),
		type	: 'GET'
	}).done( (response ) => {
		$("#referencia_cruzadas").html(response);
		$("#infoLoadingReferencias").hide();
		$("#referencia_cruzadas").show();
	});

}

	
