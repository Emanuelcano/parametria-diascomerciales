var SITUACIONES_LABORALES = [];
$(document).ready(function () {
	// Pipelining function for DataTables. To be used to the `ajax` option of DataTables
	$.fn.dataTable.pipeline = function (opts) {
		// Configuration options
		var conf = $.extend({
			pages: 5,     // number of pages to cache
			url: '',      // script url
			data: null,   // function or object with parameters to send to the server
			// matching how `ajax.data` works in DataTables
			method: 'GET' // Ajax HTTP method
		}, opts);

		// Private variables for storing the cache
		var cacheLower = -1;
		var cacheUpper = null;
		var cacheLastRequest = null;
		var cacheLastJson = null;

		return function (request, drawCallback, settings) {
			var ajax = false;
			var requestStart = request.start;
			var drawStart = request.start;
			var requestLength = request.length;
			var requestEnd = requestStart + requestLength;

			if (settings.clearCache) {
				// API requested that the cache be cleared
				ajax = true;
				settings.clearCache = false;
			}
			else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
				// outside cached data - need to make a request
				ajax = true;
			}
			else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) ||
				JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) ||
				JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)
			) {
				// properties changed (ordering, columns, searching)
				ajax = true;
			}

			// Store the request for checking next time around
			cacheLastRequest = $.extend(true, {}, request);

			if (ajax) {
				// Need data from the server
				if (requestStart < cacheLower) {
					requestStart = requestStart - (requestLength * (conf.pages - 1));

					if (requestStart < 0) {
						requestStart = 0;
					}
				}

				cacheLower = requestStart;
				cacheUpper = requestStart + (requestLength * conf.pages);

				request.start = requestStart;
				request.length = requestLength * conf.pages;

				// Provide the same `data` options as DataTables.
				if (typeof conf.data === 'function') {
					// As a function it is executed with the data object as an arg
					// for manipulation. If an object is returned, it is used as the
					// data object to submit
					var d = conf.data(request);
					if (d) {
						$.extend(request, d);
					}
				}
				else if ($.isPlainObject(conf.data)) {
					// As an object, the data given extends the default
					$.extend(request, conf.data);
				}
				settings.jqXHR = $.ajax({
					"type": conf.method,
					"url": conf.url,
					"data": request,
					"dataType": "json",
					"cache": false,
					"success": function (json) {
						cacheLastJson = $.extend(true, {}, json);

						if (cacheLower != drawStart) {
							json.data.splice(0, drawStart - cacheLower);
						}
						if (requestLength >= -1) {
							json.data.splice(requestLength, json.data.length);
						}

						drawCallback(json);
					}
				});
			}
			else {
				json = $.extend(true, {}, cacheLastJson);
				json.draw = request.draw; // Update the echo for each response
				json.data.splice(0, requestStart - cacheLower);
				json.data.splice(requestLength, json.data.length);

				drawCallback(json);
			}
		}
	};

	// Register an API method that will empty the pipelined data, forcing an Ajax
	// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
	$.fn.dataTable.Api.register('processing()', function () {
		return this.iterator('table', function (settings) {
			settings.clearCache = true;
		});
	});
	initTable_track_reglas_asig_automatica();
	get_situaciones_laborales(); // dentro initTable_reglas_asig_automatica();
});

function get_situaciones_laborales() {
	let base_url = $("#base_url").val();
	$.ajax({
		type: "GET",
		url: base_url + 'api/ApiSupervisores/get_all_situaciones_laborales',
		dataType: "JSON ",
		async: false,
		success: function (response) {
			SITUACIONES_LABORALES = response.data;
			initTable_reglas_asig_automatica();
		},
	});

}

function buscar_situaciones_laborales() {
	let base_url = $("#base_url").val();
	$.ajax({
		type: "GET",
		url: base_url + 'api/ApiSupervisores/get_all_situaciones_laborales',
		dataType: "JSON ",
		async: false,
		success: function (response) {
			SITUACIONES_LABORALES = response.data;
			initTable_reglas_asig_automatica();
		},
	});

}

function initTable_reglas_asig_automatica() {

	const base_url = $("#base_url").val();

	$('#dt_reglas_asig_automatica').dataTable().fnDestroy();

	$('#dt_reglas_asig_automatica').dataTable({
		"responsive": true,
		// "scrollX": true,
		"processing": true,
		"language": spanish_lang,
		'iDisplayLength': 10,
		'paging': true,
		'info': true,
		"searching": true,
		// "serverSide": true,
		"order": [1, "asc"],
		"ajax":
			$.fn.dataTable.pipeline({
				"url": base_url + 'api/ApiSupervisores/get_reglas_automaticas',
				"type": "GET",
				"pages": 5
			}),
		'columns': [
			{ 'data': "id", 'className': "text-center" },//Usuario Administrador
			{
				'data': null,
				render: function (data, type, row, meta) {
					let opciones = '';
					SITUACIONES_LABORALES.forEach(function (item) {
						if (data['situacion_laboral'] == item.id_situacion) {
							opciones += item.nombre_situacion;

						} 
					});
					opciones += '<input type="hidden" value ="' + data['situacion_laboral'] + '" id="situacion_laboral_' + data['id'] + '"></input>';
					return opciones;
				}

				, 'className': "text-center"
			},
			{
				'data': null,
				render: function (data, type, row, meta) {
					return '<input type="number" value ="' + data['antiguedad'] + '" placeholder="Meses" class="form-control cambioInput" id="antiguedad_' + data['id'] + '"></input>';
				}

				, 'className': "text-center"
			},
			{
				'data': null,
				render: function (data, type, row, meta) {
					return '<input type="number" value ="' + data['prediccion'] + '"  class="form-control cambioInput" id="prediccion_' + data['id'] + '"></input> <input type="hidden" value ="' + data['estado'] + '"  class="form-control" id="estado_' + data['id'] + '"></input>';
				}

				, 'className': "text-center"
			},
			{
				'data': null,
				render: function (data, type, row, meta) {
					switch (data['estado']) {
						case "0":
							return "Inactivo";
							break;
						case "1":
							return "Activo";
							break;
						default:
							break;
					}
				}, 'className': "text-center"
			},
			{
				'data': null,
				render: function (data, type, row, meta) {
					let EditarEstado = '<a class="btn btn-xs bg-yellow" value="' + data['id'] + '" id="cambiarEstado_AsigAutomatica" title="Cambiar Estado" ><i class="fa fa-exchange" ></i>';
					let EditarConfig = '<a class="btn btn-xs bg-blue editarAsigAutomatica_' + data['id'] + '" value="' + data['id'] + '" id="editarAsigAutomatica" title="Editar Configuración" onclick="editarAsigAutomatica(this);" disabled><i class="fa fa-pencil" ></i><div>';
					return EditarEstado + EditarConfig;
				}, 'className': "text-center"
			}
		],
	});
}


function initTable_track_reglas_asig_automatica() {
	// $('#nuevaConfig').hide();
	// $("#dt_track_asig_automatica > tbody > tr > td").hasClass("dataTables_empty")
	const base_url = $("#base_url").val();
	// console.log(base_url + 'api/ApiSupervisores/get_track_reglas_automaticas');
	$('#dt_track_asig_automatica').dataTable().fnDestroy();

	$('#dt_track_asig_automatica').dataTable({
		"responsive": true,
		// "scrollX": true,
		// "processing":true,
		"language": spanish_lang,
		'iDisplayLength': 10,
		'paging': true,
		'info': true,
		"searching": true,
		// "serverSide": true,
		"order": [1, "desc"],
		"ajax":
			$.fn.dataTable.pipeline({
				"url": base_url + 'api/ApiSupervisores/get_track_reglas_automaticas',
				"type": "GET",
				"pages": 5
			}),
		'columns': [
			{ 'data': "id", 'className': "text-center" },
			{ 'data': "fecha_hora", 'className': "text-center" },
			{ 'data': "id_operador", 'className': "text-center" },//el nombre se escribe en el backend
			{
				'data': null,
				render: function (data, type, row, meta) {
					let opciones = '';
					SITUACIONES_LABORALES.forEach(function (item) {
						if (data['situacion_laboral'] == item.id_situacion) {
							opciones += item.nombre_situacion;

						} 
					});
					return opciones;
				}

				, 'className': "text-center"
			},
			{ 'data': "antiguedad", 'className': "text-center" },
			{ 'data': "prediccion", 'className': "text-center" },
			{
				'data': null,
				render: function (data, type, row, meta) {
					switch (data['estado']) {
						case "0":
							return "Inactivo";
							break;
						case "1":
							return "Activo";
							break;
						default:
							break;
					}
				}, 'className': "text-center"
			},
		],
	});
	
	if($('#dt_track_asig_automatica > tbody > tr > td').hasClass('dataTables_empty')){
		$('#dt_track_asig_automatica > tbody > tr > td').html('Sin registros');
	
	}
}


$('body').on('keyup', '.cambioInput', function (e) {
	let obtenido = e.currentTarget.id.split('_');
	let id = obtenido[obtenido.length - 1];

	if (e.currentTarget.defaultValue != this.value) {
		$(".editarAsigAutomatica_" + id).removeAttr('disabled');

	} else {
		$(".editarAsigAutomatica_" + id).attr('disabled', 'disabled');

	}

});




$('body').on('click', '#dt_reglas_asig_automatica a[id="editarAsigAutomatica"]', function (event) {

	let id = $(this).attr('value');

	if ($(".editarAsigAutomatica_" + id).attr('disabled') == null) {

		Swal.fire({
			title: 'Actualizar Regla.',
			text: '¿Estás seguro de que quieres modificar la regla?',
			icon: 'warning',
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar',
			showCancelButton: 'true'
		}).then((result) => {
			if (result.value) {
				// debugger;
				let base_url = $("#base_url").val();
				let id = $(this).attr('value');
				let situacion_laboral = $("#situacion_laboral_" + id).val();
				let antiguedad = $("#antiguedad_" + id).val();
				let prediccion = $("#prediccion_" + id).val();
				let estado = $("#estado_" + id).val();

				$.ajax({
					url: base_url + 'api/ApiSupervisores/update_reglas_automatico',
					type: 'post',
					dataType: 'JSON',
					data: {
						id: id,
						situacion_laboral: situacion_laboral,
						antiguedad: antiguedad,
						prediccion: prediccion,
						estado: estado
					},
					success: function (response) {
						// debugger;
						if (response.ok == true) {
							Swal.fire({
								title: 'Asignación Automática.',
								text: 'El regla fue actualizada con éxito.',
								icon: 'success',

								confirmButtonText: 'OK'
							})

							initTable_track_reglas_asig_automatica();
							$(".editarAsigAutomatica_" + id).attr('disabled', 'disabled')
							// initTable_reglas_asig_automatica();


						} else {
							Swal.fire({
								title: 'Asignación Automática.',
								text: 'No se pudo actualizada la regla.',
								icon: 'error',

								confirmButtonText: 'OK'
							})
						}
					},
					error: function (jqXHR, estado, error) {
						// debugger;
						Swal.fire({
							title: 'Asignación Automática.',
							text: 'Problema de comunicación.',
							icon: 'error',

							confirmButtonText: 'OK'
						})
					}
				})
			}
		})

	}

});


$('body').on('click', '#dt_reglas_asig_automatica a[id="cambiarEstado_AsigAutomatica"]', function (event) {

	Swal.fire({
		title: 'Cambio de estado',
		text: '¿Estás seguro de que quieres modificar el estado?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {
		if (result.value) {
			// debugger;
			let base_url = $("#base_url").val();
			let id = $(this).attr('value');
			let situacion_laboral = $("#situacion_laboral_" + id).val();
			let antiguedad = $("#antiguedad_" + id).val();
			let prediccion = $("#prediccion_" + id).val();
			let estado = $("#estado_" + id).val();

			$.ajax({
				url: base_url + 'api/ApiSupervisores/cambio_estado_reglas_automatico',
				type: 'post',
				dataType: 'JSON',
				data: {
					id: id,
					situacion_laboral: situacion_laboral,
					antiguedad: antiguedad,
					prediccion: prediccion,
					estado: estado
				},
				success: function (response) {
					// debugger;
					if (response.ok == true) {
						Swal.fire({
							title: 'Asignación Automática.',
							text: 'El estado de la configuración fue actualizada con éxito.',
							icon: 'success',

							confirmButtonText: 'OK'
						})

						initTable_track_reglas_asig_automatica();
						initTable_reglas_asig_automatica();


					} else {
						Swal.fire({
							title: 'Asignación Automática.',
							text: 'No se pudo actualizada la configuración.',
							icon: 'error',

							confirmButtonText: 'OK'
						})
					}
				},
				error: function (jqXHR, estado, error) {
					// debugger;
					Swal.fire({
						title: 'Asignación Automática.',
						text: 'Problema de comunicación.',
						icon: 'error',

						confirmButtonText: 'OK'
					})
				}
			})
		}
	})

});
