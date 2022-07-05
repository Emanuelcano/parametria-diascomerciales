$(document).ready(function () {
	var acc_endeud = document.getElementsByClassName("accordion_referencias_cruzadas_email");
	var iendeud;
	for (iendeud = 0; iendeud < acc_endeud.length; iendeud++) {
		acc_endeud[iendeud].addEventListener("click", function() {
			this.classList.toggle("active");
			if ($(this).hasClass('active')) {
				$('.title_button_verlistasr').text('REFERENCIAS_CRUZADAS EMAIL');
				loadReferenciasCruzadasEmail();
			} else {
				$('.title_button_verlistasr').text('VER REFERENCIAS_CRUZADAS EMAIL');
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

function loadReferenciasCruzadasEmail(){
	$("#infoLoadingReferenciasEmail").show();
	$("#referencia_cruzadas_email").hide();
	
	$.ajax({
		url		:  base_url + 'atencion_cliente/Gestion/getReferenciasCruzadasEmailView/' +  $("#client").data('number_doc'),
		type	: 'GET'
	}).done( (response ) => {
		$("#referencia_cruzadas_email").html(response);
		$("#infoLoadingReferenciasEmail").hide();
		$("#referencia_cruzadas_email").show();
	});

}

	
