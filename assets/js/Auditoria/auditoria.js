$(document).ready(function() {

	
	
	
});





$('body').on('click','#dashboard_principal button[id="btnBuscar"]',function(event){
event.preventDefault();
var slc_tiporepo = $("#slc_tiporepo").val();
var fecha_Desde = $("#fecha_Desde").val();
var fecha_hasta = $("#fecha_hasta").val();


$.ajax({
		type: "POST",
		url: $("input#base_url").val()+'auditoria/auditoria/indicadores',
		data:{slc_tiporepo:slc_tiporepo,fecde:fecha_Desde,fech:fecha_hasta},
		success:function(response)
		{
			response.split(':');
			var seg1 = response[0].split(':');
			var seg2 = response[1].split(':');
			var seg3 = response[2].split(':');
			var seg4 = response[3].split(':');
			var tiempeje= seg1+seg2+seg3+seg4;

			//console.log('resultado: '+tiempeje*1000);

			



			if (fecha_Desde=='') {
			Swal.fire("Verifique!", "Debe indicar una fecha inicial para la busqueda", "error");

			}else if(fecha_hasta==''){
			Swal.fire("Verifique!", "Debe indicar una fecha final para la busqueda", "error");

			}else{
			validarSession(response);
			tiempoRespuesta(tiempeje);
			$("#main").html(response);
			//TablaPaginada('tp_Indicadores',3,'desc');

			}
			
			

			
			
		}
	})

});

$('body').on('click','#dashboard_principal button[id="btnExportarLista"]',function(event){
event.preventDefault();
var slc_tiporepo = $("#slc_tiporepo").val();
var fecha_Desde = $("#fecha_Desde").val();
var fecha_hasta = $("#fecha_hasta").val();

if (fecha_Desde=='') {
Swal.fire("Verifique!", "Debe indicar una fecha inicial para exportar", "error");

}else if(fecha_hasta==''){
Swal.fire("Verifique!", "Debe indicar una fecha final para exportar", "error");

}else if(slc_tiporepo==1){
	
window.open($("input#base_url").val()+'auditoria/auditoria/generarExcelIngresos'+'/'+fecha_Desde+'/'+fecha_hasta+'/', '_self');
return false;

}else if(slc_tiporepo==2){
window.open($("input#base_url").val()+'auditoria/auditoria/generarExcelEgresos'+'/'+fecha_Desde+'/'+fecha_hasta+'/', '_self');
return false;
}


});





