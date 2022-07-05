$(document).ready(function () {
	var acc_endeud = document.getElementsByClassName("accordion_info_endeudamiento");
	var iendeud;
	for (iendeud = 0; iendeud < acc_endeud.length; iendeud++) {
		acc_endeud[iendeud].addEventListener("click", function() {
			this.classList.toggle("active");
			if ($(this).hasClass('active')) {
				$('.title_button_info_endeudamiento').text('INFORMACIÓN ENDEUDAMIENTO');
				loadInfoEndeudamiento();
			} else {
				$('.title_button_info_endeudamiento').text('VER INFORMACIÓN ENDEUDAMIENTO');
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

function loadInfoEndeudamiento(){
	$("#infoLoading1").show();
	$("#infoLoading2").show();
	$("#infoLoading3").show();
	$("#infoLoading4").show();
	$("#financiero_al_dia").hide();
	$("#financiero_en_mora").hide();
	$("#real_al_dia").hide();
	$("#real_en_mora").hide();
	
	$.ajax({
		url		:  base_url + 'atencion_cliente/Gestion/getSectorFinancieroAlDiaView/' + $("#inp_sl_documento").val(),
		type	: 'GET'
	}).done( (response ) => {
		$("#financiero_al_dia").html(response);
		$("#infoLoading1").hide();
		$("#financiero_al_dia").show();
	});

	$.ajax({
		url		:  base_url + 'atencion_cliente/Gestion/getSectorFinancieroEnMoraView/' + $("#inp_sl_documento").val(),
		type	: 'GET'
	}).done( (response ) => {
		$("#financiero_en_mora").html(response);
		$("#infoLoading2").hide();
		$("#financiero_en_mora").show();
	});

	$.ajax({
		url		:  base_url + 'atencion_cliente/Gestion/getSectorRealAlDiaView/' + $("#inp_sl_documento").val(),
		type	: 'GET'
	}).done( (response ) => {
		$("#real_al_dia").html(response);
		$("#infoLoading3").hide();
		$("#real_al_dia").show();
	});

	$.ajax({
		url		:  base_url + 'atencion_cliente/Gestion/getSectorRealEnMoraView/' + $("#inp_sl_documento").val(),
		type	: 'GET'
	}).done( (response ) => {
		$("#real_en_mora").html(response);
		$("#infoLoading4").hide();
		$("#real_en_mora").show();
	});

	$.ajax({
		url		:  base_url + 'atencion_cliente/Gestion/getSectorFinancieroExtinguidoView/' + $("#inp_sl_documento").val(),
		type	: 'GET'
	}).done( (response ) => {
		$("#financiero_extinguido").html(response);
		$("#infoLoading5").hide();
		$("#financiero_extinguido").show();
	});

	$.ajax({
		url		:  base_url + 'atencion_cliente/Gestion/getSectorRealExtinguidoView/' + $("#inp_sl_documento").val(),
		type	: 'GET'
	}).done( (response ) => {
		$("#real_exitinguido").html(response);
		$("#infoLoading6").hide();
		$("#real_exitinguido").show();
	});
}

	
