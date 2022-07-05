

var calendar;
const campaignService = document.querySelector('#campaignService');

campaignService.addEventListener('change', () => {
	const containerCampaingService = document.querySelector('#containerCampaingService');
	const containerCampaignChannel = document.querySelector('#containerCampaignChannel');
	
	const divQueEnviarSMS = document.querySelector('#queEnviarSms');
	const divQueEnviarWhatsapp = document.querySelector('#queEnviarWhatsapp');

	containerCampaingService.style.width = '100%';
	containerCampaignChannel.style.display = 'none';

	if (campaignService.value === 'SMS') {
		divQueEnviarSMS.style.display = 'block';
		divQueEnviarWhatsapp.style.display = 'none';
	}
	
	if (campaignService.value === 'WSP') {
		divQueEnviarSMS.style.display = 'none';
		divQueEnviarWhatsapp.style.display = 'block';

		containerCampaingService.style.width = '50%';
		containerCampaignChannel.style.display = 'inline-block';
	}
	
});

$(document).ready(function() {
	
	const idCampania = $("#txt_hd_id_camp").val();
	
	load_campaing_details(idCampania);
	iniciarTablaMensajes(idCampania);
	refreshMensajesSelect(idCampania);
	init_mensajes_programados(idCampania);
	iniciarTagInput();
	loadFilters();
	loadSlackSelect(idCampania);
	loadComoEnviar(idCampania);
	getWhatsappTemplates(idCampania);
	
	crearSelect();
	
	
	
	
	// ============================================================== 
	// SELECT VARIABLES MENSAJE
	// ============================================================== 
	$('#selectVariablesMensaje').select2({
		placeholder: '.: Selecciona los criterios :.',
		multiple : true,
	});

	
	const selectTemplateWhatsapp = $("#templateWhatsapp").select2();
	selectTemplateWhatsapp.on('select2:selecting', function(e) {
		let id = e.params.args.data.id;
		if (id != ''){
			$("#previewTemplateWhatsapp").html(e.params.args.data.text);
		} else {
			$("#previewTemplateWhatsapp").html('');
		}
	})

	$('#mensaje').blur(function() {
		$("#textarea_mensaje_last_position").val($(this).caret());
	});
	
	$('#selectVariablesMensaje').on('select2:select', function (e) {
		//debugger;
		$(".enable-control").attr("disabled",false);
		var data = e.params.data;
		//var xyz  = data.select2().find(':selected').data('campo');
		var base_datos = ($(e.params.data.element).data("base_datos"));
		var tabla_primaria = ($(e.params.data.element).data("tabla_primaria"));
		var tabla = ($(e.params.data.element).data("tabla"));
		var campo = ($(e.params.data.element).data("campo"));
		var condicion = ($(e.params.data.element).data("condicion"));
		var termino = "FROM";
		let cadena = $("#query_contenido").val();
		var posicion = cadena.indexOf(termino);

		var objetivos = $("#mensaje").val();
		let largoMensaje = objetivos.length;
		let textarea_mensaje_last_position = $("#textarea_mensaje_last_position").val();
		let parte1 = objetivos.substring(0,textarea_mensaje_last_position);
		let parte2 = objetivos.substring(textarea_mensaje_last_position, largoMensaje);
		let variable = "$" + data.text + " ";
		objetivos = parte1+variable+parte2;
		$("#mensaje").val(objetivos);

		if (posicion !== -1){
			let nueva_cadena = '';
			nueva_cadena = cadena.replace(termino, " , "+campo+ " FROM ");

			if(nueva_cadena.search('INNER JOIN ' +tabla+' '+condicion ) == -1){
				nueva_cadena+= ' INNER JOIN ' +tabla+' '+condicion
			}

			$("#query_contenido").val("");
			$("#query_contenido").val(nueva_cadena);
		}else{

			var query_contenido = $("#query_contenido").val()

			if (!query_contenido.indexOf("INNER JOIN credito_detalle ON maestro.credito_detalle.id_credito = creditos.id") > -1) {
				$("#query_contenido").append("SELECT "+campo+" FROM "+base_datos+"."+tabla_primaria+" INNER JOIN "+tabla+" "+condicion);
			}else{
				$("#query_contenido").append("SELECT "+campo+" FROM "+base_datos+"."+tabla_primaria);
			}
		}

	});
	
	//============================================================== 
	// A QUIEN ENVIAR
	//==============================================================

	$('#sl_status').select2({
		placeholder: '.: Selecciona los estados :.',
		multiple : true
	});
	
	$("#sl_destino").change(function(){
		if ($(this).val() == CAMPAIGN_RECEIVER_CLIENTES) {
			$("#seccion_solicitantes").hide();
			$("#seccion_clientes").show();
		} else {
			$("#seccion_clientes").hide();
			$("#seccion_solicitantes").show();
		}
	});

	$("#sl_clientType").change(function(){
		let selectedValue = $(this).val();
		if (selectedValue == CAMPAIGN_CLIENT_TYPE_ALL || selectedValue == CAMPAIGN_CLIENT_TYPE_RETANQUEO) {
			$("#sl_actions").val('');
			$("#group_actions").show();

			$("#group_x_creditos").hide();
			$("#x_creditos").val('');
		} else {
			$("#sl_actions").val('');
			$("#group_actions").hide();
			$("#sl_actions").val('');

			$("#group_x_creditos").hide();
			$("#x_creditos").val('');
		}
	});

	$("#sl_actions").change(function(){
		let selectedValue = $(this).val();
		if (selectedValue == CAMPAIGN_ACTION_ALL) {
			$("#group_x_creditos").hide();
			$("#x_creditos").val('');
		} else {
			$("#group_x_creditos").show();
		}
	})

	$("#sl_filters").change(function(){
		let filter = $(this).val();
		let logic = $("#sl_logics").val();
		if (filter != 0 && logic != 0) {
			filtroYLogicaInputs(filter, logic)
		}

	});

	$("#sl_logics").change(function(){
		let filter = $("#sl_filters").val();
		let logic = $(this).val();
		if (filter != 0 && logic != 0) {
			filtroYLogicaInputs(filter, logic)
		}
	});

	$("#testQuery").click(function(){
		testQuery();
	});

	$("#saveFilters").click(function(){
		saveFilters();
	});

	$('.weekday').clockTimePicker({
		duration: true,
		minimum: '7:00',
		maximum: '22:00',
		precision: 5
	});

	$('.weekend').clockTimePicker({
		duration: true,
		minimum: '8:00',
		maximum: '18:00',
		precision: 5
	});

	$('#save_message').click(function(){
		$("#loadingMensajes").show();
		check_mensaje_predet();
	});
	
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

		}else{
			if ($id_campania != '') {
				//update
				updateCampania();
			} else {
				guardarCampania();
			}

			//debugger;
			/*EVALUO SI LA CAMPANIA EXISTE Y SI TIENE ALGUN PASO GUARDADO PARA DIRIGIRLO A LA FUNCION CORRESPONDIENTE*/
			// $.ajax({
			//     url:base_url+"api/ApiSupervisores/search_campania/",
			//     type:"POST",
			//     data:{id_campania:$id_campania},
			//     success:function(response){
			//        
			//         // console.log(response);
			//         if (response.ok) {
			//            
			//             guardarCampaniaGeneral(response.paso);
			//            
			//
			//         } else {
			//             guardarCampaniaPaso0();
			//            
			//         }
			//     }
			// });


		}
	});

	$("#addWhatsappTemplate").click(function () {

		$('#loadingEnvioWhatsapp').show();
		$('#loadingTemplateAgregados').show();
		let icon = $('<i>').addClass('fa fa-spinner fa-spin');
		$(this).html(icon);
		
		let data = {
			'camp_id': $("#txt_hd_id_camp").val(),
			'templateId': $("#templateWhatsapp").val(),
		}

		$.ajax({
			url:  base_url + "ApiSaveWappTemplateId",
			type: "POST",
			data: data,
			success: function (response) {
				getWhatsappTemplates($("#txt_hd_id_camp").val());
				$("#addWhatsappTemplate").text('Guardar');
				$('#loadingEnvioWhatsapp').hide();
				$('#loadingTemplateAgregados').hide();
			}
		});
	});
})

function getWhatsappTemplates(campaignId) {
	$("#loadingEnvioWhatsapp").show();
	$("#loadingTemplateAgregados").show();
	
	fetch(base_url + "ApiGetWhatsappTemplatesByCampaignId",{
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			'campaignId': campaignId,
		})
	}).then(response => response.json())
		.then(response => {
			const table = $("#tabla-templates-whatsapp");
			const selectTemplates = $(".select-template");
			table.empty();
			if (response.data != "") {
				selectTemplates.empty();
				selectTemplates.append($('<option>').attr('value', '').text('Seleccione un Template'));
			}
			
			// selectTemplates.append(`<option value="">.: Seleccione mensaje a enviar:.</option>`);
			response.data.forEach(element => {
				renderTemplateWhatsapp(element);
			});
			$("#loadingEnvioWhatsapp").hide();
			$("#loadingTemplateAgregados").hide();
		});
}

function renderTemplateWhatsapp(template) {
	const table = $("#tabla-templates-whatsapp");

	let tr = $("<tr>");
	let td = $("<td>");
	let btn = $("<button>").addClass('btn btn-danger btn-xs').attr('id','delTemplate-'+template.id).click( () => { deleteWhatsappTemplate(template.id) } );
	let icon = $("<i>").addClass('fa fa-trash');
	btn.append(icon);
	let aux = $('<div>').append(btn);
	tr.append(td.clone().text(template.id_template));
	tr.append(td.clone().text(template.msg_string)).addClass('text-center');
	tr.append(td.clone().html(aux));
	table.append(tr);
	let selectTemplates = $(".select-template");
	let option = $("<option>").val(template.id_template).text(template.id_template+" - "+template.msg_string);
	selectTemplates.append(option);
}

function deleteWhatsappTemplate(id) {
	$('#loadingTemplateAgregados').show();
	
	fetch(base_url + "ApiDeleteWhatsappTemplateById",{
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify({
			'id': id,
		})
	}).then(response => response.json())
		.then(response => {
			getWhatsappTemplates($("#txt_hd_id_camp").val());
		});
}


function check_mensaje_predet() {
	$id_campania = $("#txt_hd_id_camp").val();
	$id_mensaje = $("#message_id").val();

	var parametros = {
		"id_campania": $id_campania,
		"id_mensaje": $id_mensaje,
	};

	$.ajax({
		url: base_url + "api/ApiSupervisores/check_campain_predet/",
		type: "POST",
		data: parametros,
		success: function (response) {
			$chkPre = $("#chk_predeterminado").is(":checked");
			if (response == true && $chkPre) {
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'Solo puede haber un mensaje predeterminado por campaña'
				});
				$("#loadingMensajes").hide();
			} else {
				save_message();
				$('#mensaje').val('');
				$('#message_id').val('');
				$("#textarea_mensaje_last_position").val(0);
				$('#sl_criterios').val(null).trigger('change');
			}
		},
		error: function () {
			$("#loadingMensajes").hide();
		}
	});
	
}

function save_message() {
	$mensaje = $("#mensaje").val();
	$estado = $("#sl_estado_mensaje").val();
	$chkPre = $("#chk_predeterminado").is(":checked");
	$id_campania = $("#txt_hd_id_camp").val();
	$id_mensaje = $("#message_id").val();

	var parametros = {
		"mensaje": $mensaje,
		"estado": $estado,
		"chkPre": $chkPre ? 1 : 0,
		"id_campania": $id_campania,
		"id_mensaje": $id_mensaje,
	};

	
	$.ajax({
		url: base_url + "api/ApiSupervisores/save_message/",
		type: "POST",
		data: parametros,
		success: function (response) {

			if (response.ok) {
				cargar_mensajes();
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Ocurrio un error en el guardado el mensaje',
					text: response.message
				});
				$("#loadingMensajes").hide();
			}
		},
		error: function () {
			$("#loadingMensajes").hide();
		}
	});
}

function cargar_mensajes() {
	$('#table_message').DataTable().ajax.reload();
	refreshMensajesSelect($("#txt_hd_id_camp").val());
}

function refreshMensajesSelect(id_campania) {
	$("#loadingCuandoEnviar").show();
	var parametros = {
		'id_camp': id_campania
	};
	$.ajax({
		type: "POST",
		url: base_url + 'api/ApiSupervisores/get_all_active_mensajes',
		data:  parametros,
		success: function (response) {
			loadMensajesSelect(response)
		},
		complete: function () {
			$("#loadingMensajes").hide();
			$("#loadingCuandoEnviar").hide();
		}
	});
	dataSelecte(parametros.id_camp);
}

function loadMensajesSelect(data) {
	loadMensajeOptions('Lunes', data);
	loadMensajeOptions('Martes', data);
	loadMensajeOptions('Miercoles', data);
	loadMensajeOptions('Jueves', data);
	loadMensajeOptions('Viernes', data);
	loadMensajeOptions('Sabado', data);
	loadMensajeOptions('Domingo', data);
}

function loadMensajeOptions(day, data) {
	if (data.data !== -1) {
		var array = data.data;
		$("#select-mensaje-"+day).ddslick('destroy');
		$("#select-mensaje-"+day).html('');
		if (array != '') {
			for (i in array) {
				let predet = ''
				if (array[i].pre == 1) {
					predet = "selected='selected'";
				}

				let html = "<option value='"+array[i].id_mensaje+"' "+predet+" data-imagesrc='' data-description='"+array[i].mensaje+"'></option>";
				// console.log(html);
				$("#select-mensaje-"+day).append(html);
			}
		}
		$("#select-mensaje-"+day).ddslick({
			width: '100%'
		});

		$(".dd-options").removeAttr('style')	
	}
}

function deleteMensaje(idMensaje) {
	Swal.fire({
		title: 'Borrar mensaje',
		text: 'Estas seguro de que quieres BORRAR el mensaje?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {

		$id_campania = $("#txt_hd_id_camp").val();
		if (result.value) {
			base_url = $("input#base_url").val();
			var parametros = {
				"id_campania": $id_campania,
				"id_mensaje": idMensaje,
			};

			$("#loadingMensajes").show();
			$.ajax({
				url: base_url + "api/ApiSupervisores/check_delete_msg/",
				type: "POST",
				data: parametros,
				success: function (response) {
					if (response == true) {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'No se puede borrar el mensaje. Esta siendo utilizado como mensaje programado. Si aun desea eliminar este mensaje debera eliminar primero los mensajes programados en la seccion "¿Cuando enviar?"'
						});
						$("#loadingMensajes").hide();
					} else {
						var parametros = {
							"id_mensaje": idMensaje,
						};

						$.ajax({
							url: base_url + "api/ApiSupervisores/delete_message/",
							type: "POST",
							data: parametros,
							success: function (response) {
								if (response.status == 200) {
									cargar_mensajes();
								} else {
									Swal.fire({
										icon: 'error',
										title: 'Ocurrio un error al borrar el mensaje',
										text: response.message
									});
									$("#loadingMensajes").hide();
								}
							},
							complete: function () {
								$("#loadingMensajes").hide();
							}
						});
					}
				}
			});
		}
	});
}

function editarMensaje(idMensaje) {
	$("#loadingMensajes").show();
	base_url = $("input#base_url").val();
	var parametros = {
		"id_mensaje": idMensaje,
	};

	$.ajax({
		url: base_url + "api/ApiSupervisores/get_mensaje/",
		type: "POST",
		data: parametros,
		success: function (response) {
			if (response.status == 200) {
				if (!$.trim(response.data)) {
					Swal.fire({
						icon: 'error',
						title: 'Mensaje no encontrado',
						text: response.message
					});
				} else {
					loadMensajeForEdit(response.data[0]);
				}
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Ocurrio un error al cargar el mensaje',
					text: response.message
				});
				$("#loadingMensajes").hide();
			}
		},
		complete: function () {
			$("#loadingMensajes").hide();
		}
	});
}

function loadMensajeForEdit(data) {
	$("#message_id").val(data.id_mensaje);
	$("#mensaje").val(data.mensaje);
	$("#sl_estado_mensaje").val(data.estado);
	if (data.prederterminado == 1) {
		$("#chk_predeterminado").prop('checked', true);
	} else {
		$("#chk_predeterminado").prop('checked', false);
	}
}

function iniciarTablaMensajes(id_camp) {
		var tableMessage = $('#table_message').DataTable({
			ajax: {
				url: base_url + 'api/ApiSupervisores/get_all_mensajes',
				'data': {'id_camp': id_camp},
				"type": "POST",
			},
			"columns": [
				{"data": "mensaje"}, //mensaje
				{"data": "estado"}, //estado
				{"data": "pre"} //predeterminado
			],

			"columnDefs": [
				{
					"targets": 0,
					"width": "400px",
				},
				{
					"targets": 1,
					"data": "estado",
					"render": function (data, type, row, meta) {
						if (data == 1) {
							return '<span class="label label-success">ACTIVO</span>';
						} else {
							return '<span class="label label-default">INACTIVO</span>';
						}
					}
				},
				{
					"targets": 2,
					"width": "20px",
					"data": "pre",
					"render": function (data, type, row, meta) {
						if (data == 1) {
							return '<i class="fa fa-check"></i>';
						} else {
							return '';
						}
					}
				},
				{
					"targets": 3,
					"width": "100px",
					"data": "id_mensaje",
					"render": function (data, type, row, meta) {
						return '<button onclick="editarMensaje(' + data + ')" class="btn btn-success btn-sm" title="Editar Mensaje" ><i class="fa fa-pencil"></i></button>' +
							'&nbsp;<button onclick="deleteMensaje(' + data + ')" class="btn btn-danger btn-sm" title="Borrar mensaje" ><i class="fa fa-trash"></i></button>';
					}
				},
			],

			"language": {
				"sProcessing": "Procesando...",
				"sLengthMenu": "Mostrar _MENU_ registros",
				"sZeroRecords": "No se encontraron resultados",
				"sEmptyTable": "Ningún dato disponible en esta tabla",
				"sInfo": "Del _START_ al _END_ de un total de _TOTAL_ reg.",
				"sInfoEmpty": "0 registros",
				"sInfoFiltered": "(filtrado de _MAX_ reg.)",
				"sInfoPostFix": "",
				"sSearch": "Buscar:",
				"sUrl": "",
				"sInfoThousands": ",",
				"sLoadingRecords": "Cargando...",
				"oPaginate": {
					"sFirst": "Primero",
					"sLast": "Último",
					"sNext": "Sig",
					"sPrevious": "Ant"
				},
				"oAria": {
					"sSortAscending": ": Activar para ordenar la columna de manera ascendente",
					"sSortDescending": ": Activar para ordenar la columna de manera descendente"
				}
			},
			'pageLength': 10,
			'order': [[0, "asc"]],
		});
}

function filtroYLogicaInputs(filtro, logica) {
	if (filtro == CAMPAIGN_FILTER_DIAS_ATRASO) {
		if(logica == CAMPAIGN_LOGIC_ENTRE) {
			$("#valor_1_only").show();
			$("#valor_2_only").show();

			$("#valor_1_origen_desde").hide();
			$("#sl_origen_desde").val('');
			$("#origen_desde_valor").val('');

			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		} else {
			$("#valor_1_only").show();
			$("#valor_2_only").hide();
			$("#valor_2").val('');

			$("#valor_1_origen_desde").hide();
			$("#sl_origen_desde").val('');
			$("#origen_desde_valor").val('');

			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		}
	} else if (filtro == CAMPAIGN_FILTER_FECHA_VENCIMIENTO) {
		if ( logica == CAMPAIGN_LOGIC_ENTRE ) {
			$("#valor_1_only").hide();
			$("#valor_1").val('');
			$("#valor_2_only").hide();
			$("#valor_2").val('');

			$("#valor_1_origen_desde").show();
			$("#valor_2_origen_hasta").show();
		} else {
			$("#valor_1_only").hide();
			$("#valor_1").val('');
			$("#valor_2_only").hide();
			$("#valor_2").val('');

			$("#valor_1_origen_desde").show();
			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		}
	} else if (filtro == CAMPAIGN_FILTER_MONTO_COBRAR) {
		if (logica == CAMPAIGN_LOGIC_ENTRE) {
			$("#valor_1_only").show();
			$("#valor_2_only").show();

			$("#valor_1_origen_desde").hide();
			$("#sl_origen_desde").val('');
			$("#origen_desde_valor").val('');

			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		} else {
			$("#valor_1_only").show();
			$("#valor_2_only").hide();
			$("#valor_2").val('');

			$("#valor_1_origen_desde").hide();
			$("#sl_origen_desde").val('');
			$("#origen_desde_valor").val('');

			$("#valor_2_origen_hasta").hide();
			$("#sl_origen_hasta").val('');
			$("#origen_hasta_valor").val('');
		}
	}

}

function testQuery() {
	let destiny = $("#sl_destino").val();
	let clientType = $("#sl_clientType").val();
	let action = $("#sl_actions").val();
	let xCredits = $("#x_creditos").val();
	let status = $("#sl_status").val();
	let filter = $("#sl_filters").val();
	let logic = $("#sl_logics").val();
	let origen_desde = $("#sl_origen_desde").val();
	let origen_desde_valor = $("#origen_desde_valor").val();
	let valor1 = $("#valor_1").val();
	let origen_hasta = $("#sl_origen_hasta").val();
	let origen_hasta_valor = $("#origen_hasta_valor").val();
	let valor2 = $("#valor_2").val();

	let data = {
		'id_campania': $("#txt_hd_id_camp").val(),
		'destiny': destiny,
		'clientType': clientType,
		'action': action,
		'xCredits': xCredits,
		'status': status,
		'filter': filter,
		'logic': logic,
		'origen_desde': origen_desde,
		'origen_desde_valor': origen_desde_valor,
		'valor1': valor1,
		'origen_hasta': origen_hasta,
		'origen_hasta_valor': origen_hasta_valor,
		'valor2': valor2
	}

	$("#spinnerTestQuery").show();

	$.ajax({
		url: base_url + "api/ApiSupervisores/test_query/",
		type: "POST",
		data: data,
		success: function (response) {
			let msg = 'Se encontraron ' + response + " registros."
			$("#spinnerTestQuery").hide();
			Swal.fire({
				icon: 'success',
				text: msg
			});
			// if (response.status == 200) {
			// } else {
			// }
		}
	});


}

function saveFilters() {
	let destiny = $("#sl_destino").val();
	let clientType = $("#sl_clientType").val();
	let action = $("#sl_actions").val();
	let xCredits = $("#x_creditos").val();
	let status = $("#sl_status").val();
	let filter = $("#sl_filters").val();
	let logic = $("#sl_logics").val();
	let origen_desde = $("#sl_origen_desde").val();
	let origen_desde_valor = $("#origen_desde_valor").val();
	let valor1 = $("#valor_1").val();
	let origen_hasta = $("#sl_origen_hasta").val();
	let origen_hasta_valor = $("#origen_hasta_valor").val();
	let valor2 = $("#valor_2").val();

	let data = {
		'id_campania': $("#txt_hd_id_camp").val(),
		'destiny': destiny,
		'clientType': clientType,
		'action': action,
		'xCredits': xCredits,
		'status': status,
		'filter': filter,
		'logic': logic,
		'origen_desde': origen_desde,
		'origen_desde_valor': origen_desde_valor,
		'valor1': valor1,
		'origen_hasta': origen_hasta,
		'origen_hasta_valor': origen_hasta_valor,
		'valor2': valor2
	}

	$.ajax({
		url: base_url + "api/ApiSupervisores/save_campain_filter/",
		type: "POST",
		data: data,
		success: function (response) {
			let msg = 'Se guardaron los filtros correctamente'
			$("#spinnerTestQuery").hide();
			Swal.fire({
				icon: 'success',
				text: msg
			});
		}
	});
}

function saveMethod() {

	let data = {
		'camp_id': $("#txt_hd_id_camp").val(),
		'metodo': $("#sl_metodo").val(),
	}

	$.ajax({
		url: base_url + "api/ApiSupervisores/save_method/",
		type: "POST",
		data: data,
		success: function (response) {
		}
	});
}

function saveFormat() {
	let data = {
		'camp_id': $("#txt_hd_id_camp").val(),
		'formato': $("#sl_formato").val(),
	}

	$.ajax({
		url: base_url + "api/ApiSupervisores/save_format/",
		type: "POST",
		data: data,
		success: function (response) {
		}
	});
}

function save_mensaje_programado(day) {

	$id_campania = $("#txt_hd_id_camp").val();
	$hour = $("#hour_" + day).val();
	$id_mensaje = $('#select-mensaje-' + day).data('ddslick').selectedData.value;

	var parametros = {
		"id_camp": $id_campania,
		"id_msg": $id_mensaje,
		"hour": $hour,
		"day": day,
	};

	$.ajax({
		url: base_url + "api/ApiSupervisores/check_mensaje_programado_day_hour/",
		type: "POST",
		data: parametros,
		success: function (response) {
			// console.log(response)
			if (response == true) {

				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: 'No pede haber dos mensajes programados para el mismo horario. Por favor cambie la hora o elimine el existente.'
				});

			} else {

				$.ajax({
					url: base_url + "api/ApiSupervisores/save_mensaje_programado/",
					type: "POST",
					data: parametros,
					success: function (response) {
						if (response.ok) {
							cargar_mensajes_programados();
							reloadCalendar();
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Ocurrio un error en el guardado el mensaje programado',
								text: response.message
							});
						}
					}
				});

			}
		}
	});
}

function init_mensajes_programados(id_campania) {
	init_mensajes_programados_table(id_campania,'Lunes');
	init_mensajes_programados_table(id_campania,'Martes');
	init_mensajes_programados_table(id_campania,'Miercoles');
	init_mensajes_programados_table(id_campania,'Jueves');
	init_mensajes_programados_table(id_campania,'Viernes');
	init_mensajes_programados_table(id_campania,'Sabado');
	init_mensajes_programados_table(id_campania,'Domingo');
}

function init_mensajes_programados_table(id_camp, day) {
	var tableMessage =$('#tabla_mensajes_programados_'+day).DataTable({
		ajax: {
			url: base_url + 'api/ApiSupervisores/get_all_mensajes_programados',
			'data': {'id_camp': id_camp, 'day': day},
			"type": "POST",
			// "dataSrc": function ( json ) { //debug ajax response
			// 	console.log(json);
			// 	return json;
			// }
		},
		"columns": [
			{"data": "hour"}, //mensaje
		],

		"columnDefs": [
			{
				"targets": 0,
				"orderable": false,
				"width": "100px",
			},
			{
				"targets": 1,
				"orderable": false,
				"width": "210px",
				"data": "id",
				"render": function (data, type, row, meta) {
					return '<button  title="Generar CSV" onclick="window.open(base_url + \'api/ApiSupervisores/generate_csv/' +row.id+ '/' +row.id_campania +'\' ,\'_blank\');" class="btn btn-xs btn-warning"><i class="fa fa-file" aria-hidden="true"></i></button>&nbsp;' +
						'<button title="Ver Mensaje" onclick=\'showMensajesProgramadosModal("' +row.mensaje+ '")\' class="btn btn-xs btn-info btn_info_msg_prog" ><i class="fa fa-eye"></i></button>&nbsp;' +
						' <button title="Borrar Mensaje programado" onclick="deleteMensajeProgramador(' + data + ')" class="btn btn-xs btn-danger" ><i class="fa fa-trash"></i></button>';
				}
			},
		],
		"paging": false,
		"searching": false,
		"bInfo" : false,
		order: [],
	});

}

function showMensajesProgramadosModal(msg) {
	$("#modal-mensjes-body").html(msg);
	$("#modal-mensajes-programados").modal('show');
}

function cargar_mensajes_programados() {
	$('#tabla_mensajes_programados_Lunes').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Martes').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Miercoles').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Jueves').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Viernes').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Sabado').DataTable().ajax.reload();
	$('#tabla_mensajes_programados_Domingo').DataTable().ajax.reload();
}



function reloadCalendar() {
	// calendar.refetchEvents();
}

$("#btn_calendar" ).click(function() {
	$("#result").addClass("hide");
	$("#view-calendar").removeClass("hide");
	var initialLocaleCode = 'es';
	$calendar = $('#fullCalendar');

	today = new Date();
	y = today.getFullYear();
	m = today.getMonth();
	d = today.getDate();

	var calendarEl = document.getElementById('fullCalendar');
	calendar = new FullCalendar.Calendar(calendarEl, {
		locale: 'es',
		slotEventOverlap: false,
		allDayDefault: false,
		events: getCalendarUrl(),
		disableResizing:true,
		initialDate: today,
		headerToolbar: {
			left: 'prev,next today',
			center: 'title',
			right: 'dayGridMonth,timeGridWeek,timeGridDay'
		},
		buttonText: {
			prev: "Ant",
			next: "Sig",
			today: "Hoy",
			month: "Mes",
			week: "Semana",
			day: "Día",
			list: "Agenda"
		},

		allDayText: "Todo el día",
		navLinks: true, // can click day/week names to navigate views
		selectable: true,
		editable: true,
		dayMaxEvents: true, // allow "more" link when too many events
		selectMirror: true,
		select: function(arg) {
			//$('#ModalAdd #start').val(arg.start);
			//$('#ModalAdd #end').val(arg.end);
			// $('#ModalAdd').modal('show');
		},
		eventClick: function(info) {
			//alert('Event: ' + info.event.title);
			//alert('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
			//alert('View: ' + info.view.type);
			// 	console.log('aaaaaaaaaa');
			// 	console.log(info.event.extendedProps.tipo);
			let auxHour = info.event.extendedProps.hora.split( " ");

			$('#ModalView #modal-id-msg-prog').val(info.event.extendedProps.id_msg_prog);
			$('#ModalView #modal-campain-type').html(info.event.extendedProps.tipo);
			$('#ModalView #modal-campain-date').html('Todos los ' + info.event.extendedProps.dia + ' a las ' + auxHour[1].slice(0, -3));
			$('#ModalView #modal-campain-msg').html(info.event.extendedProps.mensaje);
			$('#ModalView #modal-id-campain').val(info.event.extendedProps.id_campain);
			$('#ModalView #modal-canceled').val(info.event.extendedProps.canceled);
			$('#ModalView #modal-id-msg-prog-date').val(auxHour[0]);
			if (info.event.extendedProps.canceled == true) {
				$('#ModalView #modal-disable').hide();
				$('#ModalView #modal-enable').show();
			} else {
				$('#ModalView #modal-disable').show();
				$('#ModalView #modal-enable').hide();
			}
			$('#ModalView').modal('show');
			// change the border color just for fun
			info.el.style.borderColor = 'red';
		},
		eventRemove: function(arg){
			alert(arg.event);
		},
		eventContent: function(arg) {
			// console.log(arg);
			let divEl = document.createElement('div');
			divEl.setAttribute('class', 'fc-event-title');

			let htmlTitle = arg.event._def.extendedProps['html'];
			if (arg.event.extendedProps.isHTML) {
				divEl.innerHTML = htmlTitle
			} else {
				divEl.innerHTML = arg.event.title
			}

			let divColor = document.createElement('div');
			divColor.setAttribute('class', 'fc-daygrid-event-dot');
			divColor.setAttribute('style', 'border-color:' + arg.event.backgroundColor);

			let divHour = document.createElement('div');
			divHour.setAttribute('class', 'fc-event-time');
			let auxHour = arg.event.extendedProps.hora.split( " ");
			divHour.innerHTML = auxHour[1].slice(0, -3);

			let arrayOfDomNodes = [ divColor, divHour,divEl ]
			return { domNodes: arrayOfDomNodes }
		},
		eventDidMount: function(info) {

			// $('.fc-daygrid-event').each(function (index, value) {
			// 	console.log(value)
			// var title_header = $(this).children('fc-event-title').clone();
			//
			// $(title_header).insertAfter($(this).children('fc-event-title').first());
			// And I was going to remove the elements which have the classes fc-list-day-side-text and fc-list-day-text from the two <th> elements.
			// })

			// info.el.find('span.fc-event-title').html(info.el.find('span.fc-event-title').text());
			// console.log('dasdasd');

			// var tooltip = new Tooltip(info.el, {
			// 	title: info.event.extendedProps.description,
			// 	placement: 'top',
			// 	trigger: 'hover',
			// 	container: 'body'
			// });
		}


	});
	calendar.render();
});

function iniciarTagInput() {
	$.ajax({
		type: "POST",
		url: base_url + "api/ApiSupervisores/get_campaign_notification_emails/",
		data: {
			'camp_id': $("#txt_hd_id_camp").val()
		},
		success: function (response) {
			if (response !== -1) {
				response.forEach(function (arrayItem) {
					//el add dispara el evento de beforeItemAdd, generando un ajax. Para evitar que los items
					//se dupliquen se agrego un checkeo en el backend de si existe el email para la campania.
					//De existir no se agregara a la DB pero si se vera reflejado en el frontend ya que es necesario verlo
					$('#txt_notificar').tagsinput('add', arrayItem.email, {preventPost: true});
				});
			}
		}
	})
}

function loadFilters() {
	$("#loadingAQuien").show();
	$.ajax({
		type: "POST",
		url: base_url + "api/ApiSupervisores/get_filtros_campanias/",
		data: {
			'camp_id': $("#txt_hd_id_camp").val()
		},
		success: function (response) {
			if(response.length > 0){
				$("#sl_destino").val(response[0].destiny);
				$("#sl_destino").trigger('change')

				//======================= Tipo clientes =======================
				$("#sl_clientType").val(response[0].client_type);
				$("#sl_clientType").trigger('change');

				//======================= Accion y x Creditos =======================
				if (response[0].client_type != CAMPAIGN_CLIENT_TYPE_PRIMARIA) {
					$("#sl_actions").val(response[0].accion);
					$("#sl_actions").trigger('change');

					if (response[0].accion != CAMPAIGN_ACTION_ALL) {
						$("#x_creditos").val(response[0].x_credits)
					}
				}

				$("#sl_status").val(response[0].estatus.split(','));
				$('#sl_status').trigger('change');

				//======================= Filtros y logico =======================

				let filtro = response[0].filtro;
				let logic = response[0].logic;

				$("#sl_filters").val(filtro);
				$("#sl_filters").trigger('change');

				$("#sl_logics").val(logic);
				$("#sl_logics").trigger('change');


				if (filtro == CAMPAIGN_FILTER_DIAS_ATRASO && logic == CAMPAIGN_LOGIC_ENTRE) {
					$("#valor_1").val(response[0].valor1);
					$("#valor_2").val(response[0].valor2);
				} else {
					$("#valor_1").val(response[0].valor1);
				}

				if (filtro == CAMPAIGN_FILTER_FECHA_VENCIMIENTO && logic == CAMPAIGN_LOGIC_ENTRE) {
					$("#sl_origen_desde").val(response[0].origen_desde);
					$("#origen_desde_valor").val(response[0].origen_desde_valor);
					$("#sl_origen_hasta").val(response[0].origen_hasta);
					$("#origen_hasta_valor").val(response[0].origen_hasta_valor);
				} else {
					$("#sl_origen_desde").val(response[0].origen_desde);
					$("#origen_desde_valor").val(response[0].origen_desde_valor);
				}

				if (filtro == CAMPAIGN_FILTER_MONTO_COBRAR && logic == CAMPAIGN_LOGIC_ENTRE) {
					$("#valor_1").val(response[0].valor1);
					$("#valor_2").val(response[0].valor2);
				} else {
					$("#valor_1").val(response[0].valor1);
				}
			}

			// console.log('bbbbb');
			// $("#loadingAQuien").hide();
		},
		complete: function () {
			$("#loadingAQuien").hide();
		}
	})
}

function loadSlackSelect(id_campania) {
	$("#loadingComoEnviar").show();
	loadSlackUsersAndChannels();
}

function loadComoEnviar(id_campania) {
	$.ajax({
		type: "POST",
		url: base_url + "api/ApiSupervisores/get_campania/",
		data: {
			'id_campania': id_campania
		},
		success: function (response) {
			$("#sl_metodo").val(response.data[0].metodo);
			$("#sl_formato").val(response.data[0].formato);
		}
	})
}

function loadSlackUsersAndChannels() {
	var users = [];
	var channels = [];
	var slacksIds = [];

	fetch(base_url + "api/ApiSupervisores/getSlackActiveUsersAndChannels/", {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json'
		}
	}).then(function (response) {
		return response.json();
	}).then(function (data) {
		if (data.success) {
			
			
			data.data.forEach(function (item) {
				if (item.type == 'user') {
					users.push({
						id: item.id,
						text: item.name
					});
				} else {
					channels.push({
						id: item.id,
						text: item.name
					});
				}
			});

			$("#sl_slack_users").select2({
				data: users
			});

			$("#sl_slack_channels").select2({
				data: channels
			});

			$("#sl_slack_users").val([]);
			$("#sl_slack_channels").val([]);

			$("#sl_slack_users").trigger('change');
			$("#sl_slack_channels").trigger('change');
		}
	});
	
	
	
	$.ajax({
		type: "GET",
		// async: false,
		url: base_url + "api/ApiSupervisores/getSlackActiveUsersAndChannels/",
		success: function (response) {
			slackNotificados.forEach(function (arrayItem) {
				slacksIds.push(arrayItem.slack_id)
			});

			for (var i = 0, l = response.length; i < l; i++) {
				let item = {
					"id": response[i].slack_id,
					"text": response[i].nombre,
					"selected": slacksIds.includes(response[i].slack_id)
				}
				if (response[i].type == 'user') {
					users.push(item);
				} else {
					channels.push(item);
				}
			}
			
			$("#loadingComoEnviar").hide();

			var data = [
				{
					"text": "Usuarios",
					"children": users
				},
				{
					"text": "Grupos",
					"children": channels
				}
			];
			
			$('.slackIdsMultiple').select2({
				placeholder: "Select a value",
				data: data,
			});

			$(".slackIdsMultiple").on("change", function () {
				let ids = $(this).val();

				$.ajax({
					type: "POST",
					url: base_url + "api/ApiSupervisores/saveSlackNotificados/",
					data: {
						'slack_ids': ids,
						'camp_id': $("#txt_hd_id_camp").val()
					},
					success: function (response) {
						console.log(response)
					}
				})


			});
			
		}
	});
}

function load_campaing_details(id_campania) {
	$("#loadingDetails").show();
	
	$.ajax({
		url:base_url+"api/ApiSupervisores/get_campania/",
		type:"POST",
		data:{id_campania:id_campania},
		success:function(response){
			const containerCampaingService = document.querySelector('#containerCampaingService');
			const containerCampaignChannel = document.querySelector('#containerCampaignChannel');
			
			$("#campaignTitle").val(response.data[0].nombre_logica);
			$("#campaignProvider").val(response.data[0].id_proveedor);
			$("#campaignStatus").val(response.data[0].estado);
			$("#campaignService").val(response.data[0].type_logic);
			$("#campaignColor").val(response.data[0].color);
			$("#campaignMode").val(response.data[0].modalidad);
			$("#sl_metodo").val(response.data[0].metodo);
			$("#canal").val(response.data[0].canal);

			if (response.data[0].type_logic === 'WSP') {
				$("#queEnviarSms").hide();
				$("#queEnviarWhatsapp").show();
				
				containerCampaingService.style.width = '50%';
				containerCampaignChannel.style.display = 'inline-block';
				
			} else {
				$("#queEnviarSms").show();
				$("#queEnviarWhatsapp").hide();
			}
			
			if (response.data[0].metodo == CAMPAIGN_METODO_ENVIO_SLACK) {
				$("#groupSlackUsers").show();
				$("#groupEmails").hide();
			} else {
				$("#groupEmails").show();
				$("#groupSlackUsers").hide();
			}

			if (response.ok) {

			} else {

			}
		},
		complete:function(){
			$("#loadingDetails").hide();
		}
	});


}

function guardarCampania() {
	// var formData= new FormData($("#frm_campania")[0]);
	// $.ajax({
	// 	url:base_url+"api/ApiSupervisores/save_campain/",
	// 	type:"POST",
	// 	data:formData,
	// 	cache: false,
	// 	async:false,
	// 	contentType: false,
	// 	processData:false,
	// 	success:function(response){
	// 		// console.log(response);
	// 		if (response.ok) {
	// 			$("#detalle_campania").show();
	// 			$("#txt_hd_id_camp").val(response.id_campaing_return);
	// 			iniciarTablaMensajes(response.id_campaing_return);
	// 			init_mensajes_programados(response.id_campaing_return)
	// 			loadSlackSelect(response.id_campaing_return)
	// 			loadComoEnviar(response.id_campaing_return)
	// 		} else {
	// 			Swal.fire({
	// 				icon: 'error',
	// 				title: 'Ocurrio un error en el guardado de la campaña',
	// 				text: response.message,
	// 			});
	// 		}
	// 	}
	// });
}

function updateCampania() {
	// var formData= new FormData($("#frm_campania")[0]);

	var datos = {
		"txt_hd_id_camp": $("#txt_hd_id_camp").val(),
		"nombre_logica": $("#campaignTitle").val(),
		"sl_estado_campain": $("#campaignStatus").val(),
		"sl_color": $("#campaignColor").val(),
		"sl_proveedor": $("#campaignProvider").val(),
		"sl_tipo_servicio": $("#campaignService").val(),
		"sl_modalidad": $("#campaignMode").val(),
		"canal": $canal = $("#canal").val(),
	}
	
	$.ajax({
		url:base_url+"api/ApiSupervisores/update_campain/",
		type:"POST",
		data:datos,
		// cache: false,
		// async:false,
		// contentType: false,
		// processData:false,
		success:function(response){
			// console.log(response);
			if (!response.ok) {
				Swal.fire({
					icon: 'error',
					title: 'Ocurrio un error en el guardado de la campaña',
					text: response.message,
				});
			} else {
				Swal.fire({
					icon: 'success',
					title: 'Campaña actualizada',
					text: 'la campaña fue actualizada correctamente.'
				});
			}
		}
	});
}

function dataSelecte(idCampania) {
	$("#select_message").empty();
	$.ajax({
		type: "post",
		url: base_url + "ApiSearchTypeLogic",
		data: {"idCampania":idCampania},
		success: function (response) {
			
			let respuesta = eval(response)
			if (respuesta != "" && respuesta[0].type_logic != "WSP") {
				$("#eventTypeContainer").remove();
				for (let i = 0; i < respuesta.length; i++) {
					let option_val = $('<option>').attr('value', respuesta[i].id_mensaje).text(respuesta[i].msg_string);
					$("#select_message").append(option_val)
				}
				$("#select_message").select2({
					placeholder: '.: Selecciona el Mensaje :.',
				});
			}else{
				$("#select_message").select2({
					placeholder: '.: Selecciona el Template :.',
				});
			}

			$("#select_message").on('change', function() {
				let val = $(this).val();
				if (val !== '') {
					$('#cronograma-main-container').show();
					$('#Guardar').show();
					const idTemplate = val
					crono.addParameter('templateId', idTemplate);
				} else {
					$('#cronograma-main-container').hide();
					$('#Guardar').hide();
				}
			});
		}
	});
}

function crearSelect() {
	const templateSelector = $(".multi-purpose-container");
	templateSelector.css('margin-top', '10px');
	templateSelector.css('margin-left', '10px');
	templateSelector.css('margin-right', '10px');
	var selectTemplates = $('<select>').addClass('form-control').addClass('select-template').attr("id", "select_message");
	var option = $('<option>').attr('value', '').text('Seleccione un Template o Mensaje');
	selectTemplates.append(option);
	templateSelector.append(selectTemplates);
}
