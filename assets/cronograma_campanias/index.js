$(document).ready(function(){
	$('#table_campania').DataTable();
	
	$("#btn_new_campaing").click(function(){
		window.location.href = base_url + "cronograma_campanias/Cronogramas/new";
	});
	
});

