$(document).ready(function () {
	var acc_endeud = document.getElementsByClassName("accordion_listas_restrictivas");
	var iendeud;
	for (iendeud = 0; iendeud < acc_endeud.length; iendeud++) {
		acc_endeud[iendeud].addEventListener("click", function() {
			this.classList.toggle("active");
			if ($(this).hasClass('active')) {
				$('.title_button_verlistasr').text('LISTAS RESTRICTIVAS');
				loadListas();
			} else {
				$('.title_button_verlistasr').text('VER LISTAS RESTRICTIVAS');
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

function loadListas(){
	$("#infoLoadinglistas").show();
	$("#listas_restrictivas_content").hide();
	
	$.ajax({
		url		:  base_url + 'atencion_cliente/Gestion/getListasRestrictivasView/' +  $("#id_solicitud").val(),
		type	: 'GET'
	}).done( (response ) => {
		$("#listas_restrictivas_content").html(response);
		$("#infoLoadinglistas").hide();
		$("#listas_restrictivas_content").show();
	});

}

	
