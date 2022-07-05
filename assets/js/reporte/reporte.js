$(document).ready(function() {




});





$('body').on('click','#controlador_reporte button[id="btnBuscar"]',function(event){
	event.preventDefault();
	var reservation = $("#reservation").val();

	if (reservation=='') {
		Swal.fire("Verifique!", "Debe indicar una fecha para la busqueda", "error");

	} else {
		$.ajax({
			type: "GET",
			url: $("input#base_url").val()+'reporte/reporte/reporte',
			data:{reservation:reservation},
			success:function(response)
			{
					validarSession(response);
					$("#main").html(response);
			}
		});
	}
});

$('body').on('click','#controlador_reporte button[id="btnExportarLista"]',function(event){
	event.preventDefault();
	var reservation = $("#reservation").val();

	if (reservation=='') {
		Swal.fire("Verifique!", "Debe indicar una fecha inicial para exportar", "error");

	}else{
		window.open($("input#base_url").val()+'auditoria/auditoria/generarExcelEgresos'+'/'+fecha_Desde+'/'+fecha_hasta+'/', '_self');
		return false;
	}

});





