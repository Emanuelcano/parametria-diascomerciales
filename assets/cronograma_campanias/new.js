const campaignService = document.querySelector('#campaignService');

campaignService.addEventListener('change', () => {
	const containerCampaingService = document.querySelector('#containerCampaingService');
	const containerCampaignChannel = document.querySelector('#containerCampaignChannel');

	containerCampaingService.style.width = '100%';
	containerCampaignChannel.style.display = 'none';

	if (campaignService.value === 'WSP') {

		containerCampaingService.style.width = '50%';
		containerCampaignChannel.style.display = 'inline-block';
	}

});

$(document).ready(function () {
	$("#btn_save_campaing" ).click(function() {
		event.preventDefault();
		base_url = $("input#base_url").val();
		$id_campania = $("#txt_hd_id_camp").val();
		$titulo= $("#campaignTitle").val();
		$estado= $("#campaignStatus").val();
		$color= $("#campaignColor").val();
		$pro= $("#campaignProvider").val();
		$servicio= $("#campaignService").val();
		$modalidad= $("#sl_modalidad").val();
		$canal = $("#canal").val();
		

		if ($titulo==""){
			swal("Verifica","No ingreso nombre de la campaña","error");
			return false;

			// }else if ($estado==0){
			// comentado porque no permite guardar el estado "deshabilitado"
			// swal("Verifica","No selecciono estado de la campaña","error");
			// return false;
		}else if ($color==0){
			swal("Verifica","No selecciono un color para la campaña","error");
			return false;

		}else if ($pro==0){
			swal("Verifica","No selecciono proveedor","error");
			return false;
		}else if ($servicio==0){
			swal("Verifica","No selecciono servicio","error");
			return false;
		}else if ($modalidad==0){
			swal("Verifica","No selecciono modalidad","error");
			return false;
		}else if ($servicio === 'WSP' && $canal === ''){
			swal("Verifica","No selecciono canal","error");
		}else{
				guardarCampania();
		}
	});
});

function guardarCampania() {

	const btnGuardar = $("#btn_save_campaing");
	
	let icon = $('<i>').addClass('fa fa-spinner fa-spin');
	btnGuardar.html(icon);
	btnGuardar.prop('disabled', true);
	
	let saveData = {
		'nombre_logica': $("#campaignTitle").val(),
		'sl_estado_campain': $("#campaignStatus").val(),
		'sl_color': $("#campaignColor").val(),
		'sl_proveedor': $("#campaignProvider").val(),
		'sl_tipo_servicio': $("#campaignService").val(),
		'sl_modalidad': $("#campaignMode").val(),
		'canal': $("#canal").val(),
	};

	
	
	$.ajax({
		url:base_url+"api/ApiSupervisores/save_campain/",
		type:"POST",
		data: saveData,
		success:function(response){
			// console.log(response);
			if (response.ok) {
				window.location.href = base_url+"cronograma_campanias/Cronogramas/edit/" + response.id_campaing_return;
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Ocurrio un error en el guardado de la campaña',
					text: response.message,
				});
				btnGuardar.prop('disabled', false);
			}
			
			btnGuardar.html('Guardar');
		}
	});
}
