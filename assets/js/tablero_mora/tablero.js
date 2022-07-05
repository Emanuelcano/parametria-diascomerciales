$(document).ready(function() {
	base_url = $("input#base_url").val();
	vistaIndicadores();
	configurar_tablero();
	setInterval(function() {
		vistaIndicadores();
		configurar_tablero();

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
function showModal() {
	document.getElementById('openModal').style.display = 'block';
	configurar_tablero();
}
function CloseModal() {
	document.getElementById('openModal').style.display = 'none';
}
function vistaIndicadores() {
	base_url = $("input#base_url").val() + "mora/Tablero/indicadores";

	$.ajax({
		type: "GET",
		url: base_url,

		success: function(response) {
			validarSession(response);
			$("#main").html(response);
			//TablaPaginada('tp_Indicadores',3,'desc');
			var table = $('#tp_Indicadores').DataTable({
				order: [[1, "asc"]],
				autoWidth: false
			});
			var parts = $("#fecha_vencimiento").val().split('-');
			var dmyDate = parts[2] + '/' + parts[1] + '/' + parts[0];
			$("#fecha_vencimiento_mostrar").text(dmyDate);
		
			var parts = $("#fecha").val().split('-');
			var dmyDate = parts[2] + '/' + parts[1] + '/' + parts[0];
			$("#rango").text(dmyDate);
		// table
		// .order( [ 10, 'desc' ] )
		// .draw();
		}
	});
	
}
function configurar_tablero(){

let base_url = $("#base_url").val();

$.ajax({
	url: base_url+'api/tableromora/mora',
	type: 'GET',
	dataType: 'json'
})
.done(function(response) {
	if(response.status.ok) {
		$("#descripcion").val(response.data[0]['descripcion']);
		$("#estado").val(response.data[0]['estado']);
		$("#mora_dependientes").val(response.data[0]['mora_dependientes']);
		$("#mora_independientes").val(response.data[0]['mora_independientes']);
		$("#objetivo_porcentaje").val(response.data[0]['objetivo_porcentaje']);
		$("#tablero").val(response.data[0]['tablero']);
		$("#condicion").val(response.data[0]['condicion']);
		$("#objetivos_dependientes").val(response.data[0]['objetivo_dependientes']);
		$("#objetivos_independientes").val(response.data[0]['objetivos_independientes']);
		$("#objetivos_mora").val(response.data[0]['objetivo_mora']);
		$("#fecha_mora_mostrar").val( response.data[0]['fecha_mora_mostrar']);
		$("#proximo_vencimiento").val( response.data[0]['proximo_vencimiento']);
	}
})
}
function actualizar(){
	var descripcion = document.getElementById("descripcion").value;
	var estado	= document.getElementById("estado").value;
	var mora_dependientes	=	document.getElementById("mora_dependientes").value;
	var mora_independientes	=	document.getElementById("mora_independientes").value;
	var objetivo_porcentaje	=	document.getElementById("objetivo_porcentaje").value;
	var tablero	=	document.getElementById("tablero").value;
	var condicion	=	document.getElementById("condicion").value;
	var objetivos_dependientes	=	document.getElementById("objetivos_dependientes").value;
	var objetivos_independientes	=	document.getElementById("objetivos_independientes").value;
	var objetivos_mora	=	document.getElementById("objetivos_mora").value;
	var fecha_mora_mostrar	=	document.getElementById("fecha_mora_mostrar").value;
	var proximo_vencimiento	=	document.getElementById("proximo_vencimiento").value;
	if(descripcion ===''||estado===''||mora_dependientes===''||mora_independientes===''||objetivo_porcentaje===''||tablero===''||condicion===''||
	objetivos_dependientes===''||objetivos_independientes===''||objetivos_mora===''||fecha_mora_mostrar===''||proximo_vencimiento===''){
		Swal.fire({
			icon: 'error',
			title: 'Error',
			text: 'Los campos no pueden estar vacios',
		});
		CloseModal();
	}else{
		var data = {
			'descripcion' : descripcion,
			'estado' : estado,
			'mora_dependientes': mora_dependientes,
			'mora_independientes' : mora_independientes,
			'objetivo_porcentaje' : objetivo_porcentaje,
			'tablero' : tablero,
			'condicion': condicion,
			'objetivos_dependientes' : objetivos_dependientes,
			'objetivos_independientes': objetivos_independientes,
			'objetivos_mora' : objetivos_mora,
			'fecha_mora_mostrar' : fecha_mora_mostrar,
			'proximo_vencimiento': proximo_vencimiento
		};
		let base_url = $("#base_url").val();
		$.ajax({
			url: base_url+'api/tableromora/actualizar',
			type: 'POST',
			dataType: 'json',
			data:data
		})
		.done(function(response) {
			if(response.status.ok) {
				CloseModal();
				Swal.fire({
					title: '¡Exito!',
					text: response.mensaje,
					icon: 'success'
				});
				vistaIndicadores();
			}else{
				CloseModal();
				Swal.fire({
					title: 'Error',
					text: response.mensaje,
					icon: 'error'
				});
			}
		})
	}
}



