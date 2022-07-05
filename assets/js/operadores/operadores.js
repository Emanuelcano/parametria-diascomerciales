var avatar = "";
var modulos = [];
var user_modulos = [];
var solicitudes = [];
$(document).ready(function () {
	base_url = $("input#base_url").val();
	initTableHorariosOperadores();
	listaOperadores();
	cargarModulos('all');
	//cargarVistaAusencias();
	$('#tp_ausencias').DataTable({
		order: []
	});
	$('#slc-ausencia').change('on', function (params) {
		getAusencias();
	});
	$('#registrar-ausencia').click('on', function (params) {
		registrarAusencia();
	});
	$('#registrar-horario').click('on', function (params) {
		registrarHorario();
	});
	$('#actualizar-horario').click('on', function (params) {
		updatedoHorario();
	});
	$('#cargarVistaHorarios').click('on', function (params) {
		$('#slc_operadores_horario').val("");
		$('#datetime_entrada').val("");
		$('#datetime_salida').val("");
		$(".form-check-input").prop("disabled", false);
		$("#slc_operadores_horario").prop("disabled", false);
		$(".form-check-input").prop("checked", false);
		$('#cancelEditHorario').css("display", "none");
		$("#registrar-horario").css("display", "block");
		$("#actualizar-horario").css("display", "none");

	});


});

function show() {
	//$('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
	var cambio = document.getElementById("password");
	if (cambio.type == "password") {
		cambio.type = "text";
		$('#show').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
	} else {
		cambio.type = "password";
		$('#show').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
	}
};

function vistaIndicadores() {
	$("#main-ausencias").hide();
	$("#main-horarios").hide();
	$("#datatable_horario").css("display", "none");

	base_url = $("input#base_url").val() + "operadores/Operadores/indicadores";

	$.ajax({
		type: "POST",
		url: base_url,

		success: function (response) {
			validarSession(response);

			$("#main").html(response);
			//$("#cargando").css("display","none");
			$('#tp_Indicadores').DataTable({
				order: [[5, 'desc']]
			});
		}
	})

}
/*funcion vieja que debe ser modificada porque retorna la vista completa*/
function listaOperadores() {
	$("#main-ausencias").hide();
	$("#main-horarios").hide();
	$("#tp_HorarioOperadores").css("display", "none");

	user_modulos = [];
	base_url = $("input#base_url").val() + "operadores/Operadores/lista_operadores";

	$.ajax({
		type: "POST",
		url: base_url,

		success: function (response) {
			validarSession(response);
			$("#main").html(response);
			$("#cargando").css("display", "none");
			TablaPaginada('tp_Operadores', 0, 'asc');
		}
	})
}
function cargarVistaHorarios() {
	$('#main').html('');
	$("#main-horarios").show();
	$("#main-ausencias").hide();
	$("#tp_HorarioOperadores").show();
	$("#tp_HorarioOperadores_wrapper").show();

}
function cargarVistaAusencias() {
	$('#main').html('');
	$("#main-ausencias").show();
	$("#main-horarios").hide();
	$("#tp_HorarioOperadores").css("display", "none");
	$("#tp_HorarioOperadores_wrapper").css("display", "none");


	$('#tp_ausencias').addClass('hidden');
	let operadores = [];
	let opciones = '<option disabled value="" selected>Seleccione un operador</option>';
	base_url = $("input#base_url").val() + "api/operadores/get_lista_operadores_activos";

	$.ajax({
		type: "GET",
		url: base_url,
		success: function (response) {
			if (typeof (response.data) != 'undefined') {
				operadores = response.data;
				operadores.forEach(item => {
					opciones += '<option value="' + item.idoperador + '" >' + item.nombre_apellido + '</option>';
				});
				$('#slc-ausencia').html(opciones);
				$('#slc-ausencia').select2({
					placeholder: '.: Selecciona Operador :.',
					multiple: false
				});
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: response.message,
				});
			}
		}
	});

}

function getAusencias() {

	let operador = $('#slc-ausencia').val();
	let ausencias = [];

	if (operador != null) {
		base_url = $("input#base_url").val() + "api/operadores/get_ausencias_operador/" + operador;

		$.ajax({
			type: "GET",
			url: base_url,
			success: function (response) {
				if (typeof (response.data) != 'undefined') {

					let tabla = [];
					ausencias = response.data;
					ausencias.forEach(item => {

						var f1 = new Date(moment(item.fecha_final).format('YYYY-MM-DD') + ' 00:00:00');
						var f2 = new Date(moment().format('YYYY-MM-DD') + ' 00:00:00');

						tabla.push([
							moment(item.fecha_inicio.substr(0, 10)).format('DD-MM-YYYY'),
							moment(item.fecha_final.substr(0, 10)).format('DD-MM-YYYY'),
							item.motivo,
							item.operador_responsable,
							item.fecha_creacion,
							(item.estado == 0 ? 'Inactivo' : 'Activo'),
							(f1 >= f2 ? '<td><a class="btn btn-xs ' + (item.estado == 0 ? 'btn-success' : 'btn-danger') + '" data-id_reg="' + item.id + '" data-estado="' + item.estado + '" title="Anular ausencia" onclick="cambiarEstadoAusencia(this);"> <i class="fa ' + (item.estado == 0 ? 'fa-check' : 'fa-ban') + '" ></i></a>' +
								' <a class="btn btn-xs btn-info" data-motivo="' + item.motivo + '" data-fecha_final="' + moment(item.fecha_final.substr(0, 10)).format('DD-MM-YYYY') + '" data-fecha_inicio="' + moment(item.fecha_inicio.substr(0, 10)).format('DD-MM-YYYY') + '" data-id_reg="' + item.id + '" title="Modificar ausencia" onclick="modificarAusencia(this);"> <i class="fa fa-pencil" ></i></a>' : '')
						]);
					});
					$("#tp_ausencias").removeClass('hidden');
					$('#tp_ausencias').DataTable().clear().rows.add(tabla).draw();

				} else {
					$('#tp_ausencias').DataTable().clear().draw();
				}
			}
		});
	}
}

function cambiarEstadoAusencia(element) {
	let reg = $(element).data('id_reg');
	let estado = $(element).data('estado');
	let operador = $('#slc-ausencia').val();
	if (estado == 1) {
		estado_texto = "desactivar";
		estado = 0;
	} else {
		estado_texto = "Activar";
		estado = 1;
	}
	Swal.fire({
		title: 'Cambio de estado',
		text: '¿Estas seguro de que quieres ' + estado_texto + ' el registro?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {
		if (result.value) {

			const formData = new FormData();
			formData.append("ausencia", reg);
			formData.append("estado", estado);
			base_url = $("input#base_url").val() + "api/operadores/set_estado_ausencia";

			$.ajax({
				type: "POST",
				url: base_url,
				data: formData,
				contentType: false,
				processData: false,
				success: function (response) {
					if (response.status.ok) {
						getAusencias();
					} else {
						Swal.fire({
							icon: 'error',
							text: response.message
						});
					}
				}
			});
		}
	});
}
function initTableHorariosOperadores() {
	let ajax = {
		type: "GET",
		url: $("input#base_url").val() + "api/ApiOperadores/tableHorariosOperadores"
	};
	let columns = [
		{ data: "id" },
		{ data: "nombre_apellido" },
		{
			data: "dias_trabajo"
		},
		{
			data: "hora_entrada"
		},
		{
			data: "hora_salida",

		},
		{
			data: "estado_horario",
			render: function (data, type, row, meta) {
				switch (data) {
					case "0":
						estado = "DESHABILITADO";
						break;
					case "1":
						estado = "HABILITADO";
						break;
					default:
						estado = data;
						break;
				}
				return estado;
			}
		},
		{ data: "fecha_modificacion" },
		{ data: "nombre_usuario" },
		{
			data: null,
			render: function (data, type, row, meta) {

				var EditarHorario =
					'<a class="btn btn-xs btn-primary" title="Editar Horario" onclick="editarHorario(' +
					row.id + ');"><i class="fa fa-pencil-square-o" ></i></a>';
				var HabilitarHorario =
					'<a class="btn btn-xs btn-success" id="btnHabilitarHorario" data-estado="1" title="Habilitar Horario", onclick="altaHorario(' +
					row.id + ');"><i class="fa fa-thumbs-up" ></i></a>';
				var DeshabilitarHorario =
					'<a class="btn btn-xs btn-danger" id="btnDeshabilitarHorario" data-estado="0" title="Deshabilitar Horario" onclick="bajaHorario(' +
					row.id + ');"><i class="fa fa-thumbs-down" ></i></a>';
				if (data['estado_horario'] == 1) {
					return EditarHorario + " " + DeshabilitarHorario;

				} else {
					return EditarHorario + " " + HabilitarHorario;
				}
			}
		}
	];
	let columnDefs = [
	{
		targets: [8],
		createdCell: function (td, cellData, rowData, row, col) {
			$(td).attr("style", "text-align: center;");
		}
	},
	];
	TablaPaginada(
		"tp_HorarioOperadores",
		2,
		"asc",
		"",
		"",
		ajax,
		columns,
		columnDefs
	);
}

function editarHorario(id_horario) {
	$("#registrar-horario").css("display", "none");
	$("#actualizar-horario").css("display", "block");
	if (id_horario != null) {
		base_url = $("input#base_url").val() + "api/operadores/get_horario_operador_update/" + id_horario;
		$.ajax({
			type: "GET",
			url: base_url,
			success: function (response) {
				if (typeof (response.data) != 'undefined') {
					$(".form-check-input").prop("disabled", false);
					$(".form-check-input").prop("checked", false);

					horario = response.data
					// console.log("horario", horario);

					dias_trabajo_string = horario[0].dias_trabajo;
					dias_trabajo = dias_trabajo_string.split(',');
					$('#slc_operadores_horario').val(horario[0].id_operador);
					$('#datetime_entrada').val(horario[0].hora_entrada);
					$('#datetime_salida').val(horario[0].hora_salida);
					$('#actualizar-horario').data('id_horario', horario[0].id);
					$('#slc_operadores_horario').prop("disabled", true);
					$('#cancelEditHorario').css("display", "block");

					// $('#actualizar-horario').attr('data-id_horario', horario[0].id);
					for (let value of dias_trabajo) {
						// console.log(value);
						$(".form-check-input[name='" + value + "']").prop("checked", true);

					}
				}
			}
		});
	}

}

function altaHorario(id_horario) {
	Swal.fire({
		title: 'Cambio de estado',
		text: 'Estas seguro de que quieres HABILITAR horario?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {
		if (result.value) {
			var id_estado = $("#btnHabilitarHorario").attr("data-estado");

			var data = {
				id_horario: id_horario,
				id_estado: id_estado,
			}
			var base_url =
				$("input#base_url").val() + "api/operadores/cambioEstadoHorario";
			$.ajax({
				type: "POST",
				url: base_url,
				data: data,
				success: function (response) {
					if (response["errors"]) {
						toastr["error"]("No se pudo actualizar el Horario", "ERROR");
					} else {
						toastr["success"]("Se actualizo correctamente", "Actualizado");
						$('#tp_HorarioOperadores').DataTable().ajax.reload();
					}
				}
			});
		}
	});
}

function bajaHorario(id_horario) {
	Swal.fire({
		title: 'Cambio de estado',
		text: 'Estas seguro de que quieres DESHABILITAR horario?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {
		if (result.value) {
			var id_estado = $("#btnDeshabilitarHorario").attr("data-estado");
			var data = {
				id_horario: id_horario,
				id_estado: id_estado,
			}
			var base_url =
				$("input#base_url").val() + "api/operadores/cambioEstadoHorario";
			$.ajax({
				type: "POST",
				url: base_url,
				data: data,
				success: function (response) {
					if (response["errors"]) {
						toastr["error"]("No se pudo actualizar el Horario", "ERROR");
					} else {
						toastr["success"]("Se actualizo correctamente", "Actualizado");
						$('#tp_HorarioOperadores').DataTable().ajax.reload();
					}
				}
			});
		}
	});

}



function updatedoHorario() {
	$("#registrar-horario").css("display", "block");
	$("#actualizar-horario").css("display", "none");
	$('#slc_operadores_horario').prop("disabled", false);

	let id_operador = $('#slc_operadores_horario').val();
	let hora_entrada = $('#datetime_entrada').val();
	let hora_salida = $('#datetime_salida').val();
	var dias_trabajos = [];
	$('.form-check-input:checked').each(function () {
		dias_trabajos.push(this.name);
	});

	Swal.fire({
		title: 'Cambio de estado',
		text: 'Estas seguro de que quieres ACTUALIZAR horario?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {
		if (result.value) {
			// var id_horario = $('#actualizar-horario').data("data-id_horario");
			var id_horario = $("#actualizar-horario").data("id_horario");
			// console.log(id_horario);
			const formData = new FormData();
			formData.append("id", id_horario);
			formData.append("id_operador", id_operador);
			formData.append("hora_entrada", hora_entrada);
			formData.append("hora_salida", hora_salida);
			formData.append("dias_trabajos", dias_trabajos);
			var base_url =
				$("input#base_url").val() + "api/operadores/updatedoHorario";
			$.ajax({
				type: "POST",
				url: base_url,
				data: formData,
				processData: false,
				contentType: false,
				success: function (response) {
					if (response["errors"]) {
						toastr["error"]("No se pudo actualizar el Horario", "ERROR");
					} else {
						toastr["success"]("Se actualizo correctamente", "Actualizado");
						$('#tp_HorarioOperadores').DataTable().ajax.reload();
						$('#slc_operadores_horario').val("");
						$('#datetime_entrada').val("");
						$('#datetime_salida').val("");
						$(".form-check-input").prop("disabled", false);
						$(".form-check-input").prop("checked", false);
					}
				}
			});
		}
	});
}

//Select call get horarios
$('#slc_operadores_horario').change(function () {
	id_operador = $("#slc_operadores_horario option:selected").val();
	// console.log("id_operador select", id_operador);
	if (id_operador != null) {
		base_url = $("input#base_url").val() + "api/operadores/get_horario_operador/" + id_operador;

		$.ajax({
			type: "GET",
			url: base_url,
			success: function (response) {
				// console.log(response);
				if (typeof (response.data) != 'undefined') {
					$(".form-check-input").prop("disabled", false);
					$(".form-check-input").prop("checked", false);
					horario = response.data
					$('#datetime_entrada').val(horario[0].hora_entrada);

					for (var i = 0; i < horario.length; i++) {
						dias_trabajo_string = horario[i].dias_trabajo;
						dias_trabajo = dias_trabajo_string.split(',');

						for (let value of dias_trabajo) {
							console.log(value);
							$(".form-check-input").prop("cchecked", false);
							$(".form-check-input[name='" + value + "']").prop("disabled", true);
							$(".form-check-input[name='" + value + "']").prop("checked", true);
						}

					}

					$('#datetime_salida').val(horario[0].hora_salida);

				}
			}, error(e) {
				console.log(e);
			}
		});
	}
});

function registrarHorario() {
	let id_operador = $('#slc_operadores_horario').val();
	let hora_entrada = $('#datetime_entrada').val();
	let hora_salida = $('#datetime_salida').val();
	var dias_trabajos = [];
	$('.form-check-input:checked').each(function () {
		if (this.disabled == false && !this.name =='') {
				dias_trabajos.push(this.name);
		}
	});

	if (id_operador != "" && hora_entrada != "" && hora_salida != "" && dias_trabajos != "" && id_operador != null) {
		Swal.fire({
			title: 'Registro de Horario',
			text: 'Estas seguro de que quieres REGISTRAR horario?',
			icon: 'warning',
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar',
			showCancelButton: 'true'
		}).then((result) => {
			if (result.value) {
				const formData = new FormData();
				formData.append("id_operador", id_operador);
				formData.append("hora_entrada", hora_entrada);
				formData.append("hora_salida", hora_salida);
				formData.append("dias_trabajos", dias_trabajos);
				base_url = $("input#base_url").val() + "api/operadores/registar_horario_operadores";

				$.ajax({
					type: "POST",
					url: base_url,
					data: formData,
					processData: false,
					contentType: false,

					success: function (response) {
						if (response.ok) {
							// getAusencias();
							Swal.fire({
								icon: 'success',
								text: response.message
							});
							$('#tp_HorarioOperadores').DataTable();
							$('#slc_operadores_horario').val("");
							$('#datetime_entrada').val("");
							$('#datetime_salida').val("");
							$(".form-check-input").prop("disabled", false);
							$(".form-check-input").prop("checked", false);

						} else {
							Swal.fire({
								icon: 'error',
								text: response.message
							});
						}
					}
				});
			}
		});
	} else {
		Swal.fire({
			icon: 'warning',
			text: 'Todos los campos son obligatorios'
		});
	}
}

function registrarAusencia() {
	let operador = $('#slc-ausencia').val();
	let fecha = $('#date_range').val();
	let motivo = $('#motivo-ausencia').val(); swal
		.fire({
			title: "Esta seguro?",
			text: "Quiere registar Horario",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Si, registrar"
		})
		.then(function (result) {
			if (operador != null && fecha != '' && motivo != '') {
				const formData = new FormData();
				formData.append("id", operador);
				formData.append("fecha", fecha);
				formData.append("motivo", motivo);

				base_url = $("input#base_url").val() + "api/operadores/registrar_ausencia_operador";

				$.ajax({
					type: "POST",
					url: base_url,
					data: formData,
					processData: false,
					contentType: false,

					success: function (response) {
						if (response.ok) {
							getAusencias();
							Swal.fire({
								icon: 'success',
								text: response.message
							});
						} else {
							Swal.fire({
								icon: 'error',
								text: response.message
							});
						}
					}
				});
			} else {
				Swal.fire({
					icon: 'warning',
					text: 'Todos los campos son obligatorios'
				});
			}
		});

}

function modificarAusencia(element) {
	let reg = $(element).data('id_reg');
	let inicio = $(element).data('fecha_inicio');
	let final = $(element).data('fecha_final');
	let old_motivo = $(element).data('motivo');
	$(".modal-ausencia .modal-body #date_range-modal").val(inicio + ' | ' + final);
	$(".modal-ausencia .modal-body #motivo-ausencia-modal").val(old_motivo);
	$(".modal-ausencia .modal-footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">CANCELAR</button><button type="button" class="btn btn-success" data-id_reg="' + reg + '">GUARDAR</button>');

	$('.modal-ausencia .modal-footer .btn-success').click('on', function () {

		let fecha = $('#date_range-modal').val();
		let motivo = $('#motivo-ausencia-modal').val();
		if (fecha != '' && motivo != '') {
			Swal.fire({
				title: 'Modifición de ausencia',
				text: '¿Estas seguro de que quieres actualizar el registro de ausencia?',
				icon: 'warning',
				confirmButtonText: 'Aceptar',
				cancelButtonText: 'Cancelar',
				showCancelButton: 'true'
			}).then((result) => {
				if (result.value) {

					const formData = new FormData();
					formData.append("id", reg);
					formData.append("fecha", fecha);
					formData.append("motivo", motivo);

					base_url = $("input#base_url").val() + "api/operadores/update_ausencia";

					$.ajax({
						type: "POST",
						url: base_url,
						data: formData,
						processData: false,
						contentType: false,

						success: function (response) {
							if (response.ok) {
								getAusencias();
								Swal.fire({
									icon: 'success',
									text: response.message
								});
								$('.modal-ausencia').modal('hide');
							} else {
								Swal.fire({
									icon: 'error',
									text: response.message
								});
							}
						}
					});
				}
			});
		} else {
			Swal.fire({
				icon: 'warning',
				text: 'Todos los campos son obligatorios'
			});
		}
	});

	$('.modal-ausencia').modal('show');

}

function cargarOperador(operador, elemento, usuario) {
	base_url = $("input#base_url").val() + "operadores/Operadores/datos_operador";
	var data = "operador=" + operador;

	if (elemento != "edit") {
		data += "&edit=" + $(elemento).attr('id');
	}
	$.ajax({
		type: "POST",
		url: base_url,
		data: data,

		success: function (response) {
			validarSession(response);

			$("#main").html(response);
			TablaPaginada('tp_Operadores', 0, 'asc');
			cargarModulos(usuario);

			$('#slc_tipoOperador').change('on', function (){
				if($('#slc_tipoOperador').val() == 11){
					$('.automaticas').removeClass('hide');
				}else{
					$('.automaticas').addClass('hide');
				}
			});
		}, error(e) {
			cargarModulos(usuario);
		}
	});
}

function cargarAvatar(id_operador) {
	const formData = new FormData();
	formData.append("idoperador", id_operador);
	formData.append("file", avatar);

	base_url = $("input#base_url").val() + "api/operadores/cargar_avatar";

	$.ajax({
		type: "POST",
		url: base_url,
		data: formData,
		processData: false,
		contentType: false,

		success: function (response) {
			if (response['errors']) {
				errores = response['errors'];
				for (error in errores) {
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: errores[error],
					});
					break;
				}
			} else {
				if (response['status']['code'] != 200) {

					Swal.fire({
						text: response['message']
					});
				}
			}
		}
	});
}

function actualizarOperador() {
	const formData = new FormData();
	var id_operador = $("#id_operador").val();
	var id_usuario = $("#id_usuario").val();
	var nombre = $("#nombre").val();
	var apellido = $("#apellido").val();
	var estado = $("#slc_estado").val();
	var nombre_pila = $("#pila").val();
	var telefono_fijo = $("#telefono").val();
	var extension = $("#telefono_ext").val();
	var telefono_wapp = $("#telefono_wapp").val();
	var email = $("#email").val();
	var tipo_operador = $("#slc_tipoOperador").val();
	var equipo = $("#slc_equipo").val();
	var token = $("#verificacion-token").val();
	var automaticas = $("#automaticas").val();
	
	var modulos_disponibles = obtenerSeleccion();

	formData.append("idoperador", id_operador);
	formData.append("id_usuario", id_usuario);
	formData.append("nombre", nombre);
	formData.append("apellido", apellido);
	formData.append("nombre_pila", nombre_pila);
	formData.append("telefono_fijo", telefono_fijo);
	formData.append("wathsapp", telefono_wapp);
	formData.append("extension", extension);
	formData.append("mail", email);
	formData.append("tipo_operador", tipo_operador);
	formData.append("modulos", modulos_disponibles);
	formData.append("estado", estado);
	formData.append("equipo", equipo);
	formData.append("token", token);
	formData.append("automaticas", automaticas);


	if(telefono_wapp.indexOf("+")==0 && telefono_wapp.length >=13){

		base_url = $("input#base_url").val() + "api/operadores/actualizar_operador";
		$.ajax({
		type: "POST",
		url: base_url,
		data: formData,
		processData: false,
		contentType: false,

		success: function (response) {
			if (response['errors']) {
				errores = response['errors'];
				for (error in errores) {
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						text: errores[error],
					});
					break;
				}
			} else {
				if (avatar != "") {
					cargarAvatar($("#id_operador").val());
					avatar = "";
				}

				Swal.fire({
					icon: 'success',
					text: response['message'],
				});
			}
		}
	});
	} else{
		Swal.fire("El campo Whatsapp debe tener un mínimo de 13 caracteres y debe tener el siguiente formato '+COD123456789', donde COD corresponde al código del país ","", "warning");
	}
}

function cambiarEstado(operador, estado) {
	var estado_texto = "";
	var cambioEstado;
	if (estado == 1) {
		estado_texto = "desactivar"
		cambioEstado = 0;
	} else {
		estado_texto = "Activar"
		cambioEstado = 1;
	}
	Swal.fire({
		title: 'Cambio de estado',
		text: 'Estas seguro de que quieres ' + estado_texto + ' el operador?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {
		if (result.value) {

			const formData = new FormData();
			formData.append("idoperador", operador);
			formData.append("estado", cambioEstado);

			base_url = $("input#base_url").val() + "api/operadores/cambiar_estado";

			$.ajax({
				type: "POST",
				url: base_url,
				data: formData,
				processData: false,
				contentType: false,

				success: function (response) {
					if (response['errors']) {
						errores = response['errors'];
						for (error in errores) {
							Swal.fire({
								icon: 'error',
								title: 'Oops...',
								text: errores[error],
							});
							break;
						}
					} else {
						listaOperadores();
					}
				}
			});
		}
	});

}

function nuevoOperador() {
	base_url = $("input#base_url").val() + "operadores/Operadores/crear_operador";
	user_modulos = [];
	$.ajax({
		type: "POST",
		url: base_url,

		success: function (response) {
			validarSession(response);
			$("#main").html(response);
			modulosInit();

			$('#slc_tipoOperador').change('on', function (){
				if($('#slc_tipoOperador').val() == 11){
					$('.automaticas').removeClass('hide');
				}else{
					$('.automaticas').addClass('hide');
				}
			});

		}
	});
}

function modulosInit() {
	var items = [];
	var items_asignados = [];

	for (var n = 0; n < modulos.length; ++n) {
		if (typeof (user_modulos.find(element => element.id_modulo === modulos[n].id)) === "undefined") {
			items.push(modulos[n].id + "-" + modulos[n].nombre);
		} else {
			items_asignados.push(modulos[n].id + "-" + modulos[n].nombre);
		}
	}

	var dsl = $('#dualSelectExample').DualSelectList({
		'candidateItems': items,
		'selectionItems': items_asignados,
		'colors': {
			'itemText': 'black',
			'itemBackground': '#f7f1fb ',
			'itemHoverBackground': 'rgb(156, 153, 216)'
		}
	});
}

function obtenerSeleccion() {
	var selection = $(".right-panel").find('div.dsl-panel-item');
	var asignados = "";

	for (var n = 0; n < selection.length; ++n) {
		if (n > 0)
			asignados += ',';
		asignados += selection.eq(n).text().split('-')[0];
	}
	return asignados;
}

function cargarModulos(operador) {
	base_url = $("input#base_url").val() + "api/operadores/get_modulos/" + operador;

	$.ajax({
		type: "GET",
		url: base_url,

		success: function (response) {
			if (response.status == '200') {

				if (operador === "all") {
					modulos = response.data;
				} else {
					user_modulos = response.data;
					modulosInit();
				}
			} else {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: response.message,
				});
			}
		}
	});
}

function registrarOperador() {
	base_url = $("input#base_url").val() + "api/operadores/registrar";

	const formData = new FormData();
	var id_usuario = $("#id_usuario").val();
	var nombre = $("#nombre").val();
	var apellido = $("#apellido").val();
	var nombre_pila = $("#pila").val();
	var telefono_fijo = $("#telefono").val();
	var extension = $("#telefono_ext").val();
	var telefono_wapp = $("#telefono_wapp").val();
	var email = $("#email").val();
	var estado = $("#slc_estado").val();
	var tipo_operador = $("#slc_tipoOperador").val();
	var usuario = $("#user").val();
	var password = $("#password").val();
	var documento = $("#nroDocumentoFiscal").val();
	var equipo = $("#slc_equipo").val();
	var token = $("#verificacion-token").val();
	var automaticas = $("#automaticas").val();

	var modulos_disponibles = obtenerSeleccion();

	formData.append("id_usuario", id_usuario);
	formData.append("nombre", nombre);
	formData.append("apellido", apellido);
	formData.append("nombre_pila", nombre_pila);
	formData.append("telefono_fijo", telefono_fijo);
	formData.append("wathsapp", telefono_wapp);
	formData.append("extension", extension);
	formData.append("mail", email);
	formData.append("tipo_operador", tipo_operador);
	formData.append("estado", estado);
	formData.append("avatar", avatar);
	formData.append("modulos", modulos_disponibles);
	formData.append("password", password);
	formData.append("usuario", usuario);
	formData.append("documento", documento);
	formData.append("equipo", equipo);
	formData.append("token", token);
	formData.append("automaticas", automaticas);

	if(telefono_wapp.indexOf("+")==0 && telefono_wapp.length >=13){


		$.ajax({
			type: "POST",
			url: base_url,
			data: formData,
			processData: false,
			contentType: false,

			success: function (response) {
				if (response['errors']) {
					errores = response['errors'];
					for (error in errores) {
						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: errores[error],
						});
						break;
					}
				} else {
					if (avatar != "") {
						cargarAvatar(response['id_operador']);
						avatar = "";
					}
					Swal.fire({
						icon: 'success',
						text: response['message'],
					});
				}
			}
		});

		
	} else{
		Swal.fire("El campo Whatsapp debe tener un mínimo de 13 caracteres y debe tener el siguiente formato '+COD123456789', donde COD corresponde al código del país ","", "warning");
	}
}

function habilitarCambiarClave() {
	Swal.fire({
		title: 'Habilitar cambio de clave',
		text: 'Estas seguro de que quieres habilitar el cambio de clave del operador?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {
		if (result.value) {
			const formData = new FormData();
			var id_usuario = $("#id_usuario").val();
			var habilitar = 1;
			formData.append("id_usuario", id_usuario);
			formData.append("cambio_clave_habilitar", habilitar);
			base_url = $("input#base_url").val() + "api/ApiOperadores/habilitar_cambio_clave";
			$.ajax({
				type: "POST",
				url: base_url,
				data: formData,
				processData: false,
				contentType: false,
				success: function (response) {
					if (response['message_error']) {

						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: response['message_error'],
						});
					}else{
						Swal.fire({
							icon: 'success',
							text: response['message'],
						});
				}
				}
			});
		}
	});
}

function cambiarClave() {
	Swal.fire({
		title: 'Cambio de clave',
		text: 'Estas seguro de que quieres cambiar la clave del operador?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) => {
		if (result.value) {

			const formData = new FormData();
			var id_usuario = $("#id_usuario").val();
			var password = $("#password").val();
			formData.append("id_usuario", id_usuario);
			formData.append("password", password);

			base_url = $("input#base_url").val() + "api/operadores/actualizar_clave";
			$.ajax({
				type: "POST",
				url: base_url,
				data: formData,
				processData: false,
				contentType: false,

				success: function (response) {
					if (response['message_error']) {

						Swal.fire({
							icon: 'error',
							title: 'Oops...',
							text: response['message_error'],
						});


					}else{
					if (response['errors']) {
						errores = response['errors'];
						for (error in errores) {
							Swal.fire({
								icon: 'error',
								title: 'Oops...',
								text: errores[error],
							});
							break;
						}
					} else {
						if (avatar != "") {
							cargarAvatar($("#id_operador").val());
							avatar = "";
						}

						Swal.fire({
							icon: 'success',
							text: response['message'],
						});

					}

				}

				}
			});
		}
	});
}

function nuevoTipo() {
	Swal.fire({
		title: 'Agregar nuevo tipo de operador',
		text: 'Ingerese el nuevo tipo de operador',
		input: 'text',
		inputAttributes: {
			autocapitalize: 'off'
		},
		showCancelButton: true,
		confirmButtonText: 'Aceptar',
		showLoaderOnConfirm: true,
		allowOutsideClick: () => !Swal.isLoading()
	}).then((result) => {

		if (result.value) {
			const formData = new FormData();
			var id_usuario = $("#id_usuario").val();
			var tipo = result.value.toUpperCase();

			formData.append("id_usuario", id_usuario);
			formData.append("tipo", tipo);

			base_url = $("input#base_url").val() + "api/operadores/nuevo_tipo_operador";
			$.ajax({
				type: "POST",
				url: base_url,
				data: formData,
				processData: false,
				contentType: false,

				success: function (response) {
					if (response['errors']) {
						errores = response['errors'];
						for (error in errores) {
							Swal.fire({
								icon: 'error',
								title: 'Oops...',
								text: errores[error],
							});
							break;
						}
					} else {
						if (response['status']['code'] == 200)
							$("#slc_tipoOperador").append('<option value="' + response["id"] + '">' + result.value.toUpperCase() + '</option>');

						Swal.fire(response['message']);
					}
				}
			});
		}
	})
}

function vistaAsignaciones() {
	$("#main-ausencias").hide();
	$("#main-horarios").hide();

	base_url = $("input#base_url").val() + "operadores/Operadores/asignaciones";

	$.ajax({
		type: "POST",
		url: base_url,

		success: function (response) {
			validarSession(response);
			$("#main").html(response);
		}
	})
}

function actualizarAsignaciones(elemento) {
	let tab = $(elemento).closest('.tab-pane').attr('id');
	var selects = Array.from($("#" + tab).find("select.slc_operadores"));

	selects.forEach(item => {
		if ($(item).prop('value') != "" && typeof ($(item).prop('value')) != 'undefined') {
			cargarAsignaciones(item);
		}
	});
}

function cargarAsignaciones(elemento) {
	let id = $(elemento).attr('id');
	let tab = $(elemento).closest('.tab-pane').attr('id');
	let operador = $("#" + id).val();
	let tipo = "receptor";
	const formData = new FormData();

	$('input:checkbox').prop('checked', false);

	if ($("#" + tab + " select .receptor").hasClass('op-' + operador)) {
		$("#" + tab + " select .receptor").removeClass('hidden');
		$("#" + tab + " select .receptor.op-" + operador).addClass('hidden');
	}

	if ($(elemento).hasClass('designado')) { tipo = "designado"; }

	formData.append("operador", operador);
	formData.append("inicio", $("#desde-" + tab).val());
	formData.append("fin", $("#hasta-" + tab).val());
	formData.append("tipo", tipo);

	base_url = $("input#base_url").val() + "api/operadores/consultar_asignaciones";

	$.ajax({
		type: "POST",
		url: base_url,
		data: formData,
		processData: false,
		contentType: false,

		success: function (response) {
			if ($("#operador-1").val() != null)
				$("#operador-2").attr("disabled", false);
			if ($("#operador-5").val() != null)
				$("#operador-6").attr("disabled", false);

			let data = response.data.solicitudes;
			let asignacion = "";

			if ($(elemento).hasClass('designado')) { solicitudes = []; }

			if (data.length > 0) {
				data.forEach(element => {

					if ($(elemento).hasClass('designado')) {
						solicitudes.push(element.id_solicitud);
					}

					asignacion += '<tr id="' + element.id_solicitud + '"><td>' + element.id_solicitud + '</td><td class="text-center">';

					if (typeof (element.new_chat) != "undefined" && element.new_chat != null)
						asignacion += '<i  class="fa fa-check text-success"  id ="' + element.new_chat[0].id + '">';

					asignacion += '</td><td>' + element.fecha_registro + '</td></tr>';
				});
			}
			$("#asig-" + id + " tbody").html(asignacion);
			$("#cant-" + id).html(data.length);
			$("#cant-chats-" + id).html(response.data.cantidad_chats);
		}
	})
}

function consultarOperador(elemento) {
	let id = $(elemento).attr('id');
	let operador = $("#" + id).val();

	base_url = $("input#base_url").val() + "api/operadores/consultar_operador/" + operador;

	$.ajax({
		type: "GET",
		url: base_url,

		success: function (response) {
			if (typeof (response.data) != "undefined") {
				let data = response.data;
				let tr = "";
				tr += '<tr><td>' + data.idoperador + '</td><td>' + data.nombre_apellido + '</td><td>' + data.descripcion + '</td></tr>';
				$("#operador-seleccionado tbody").html(tr);
			} else {
				Swal.fire("No fue posible recuperar la informacion del operador seleccionado");
			}
		}
	});
}

function consultarSolicitud(col) {
	let valor = $("#" + col).val();
	base_url = $("input#base_url").val() + "api/operadores/consultar_solicitud/" + col + "/" + valor;

	$.ajax({
		type: "GET",
		url: base_url,
		processData: false,
		contentType: false,

		success: function (response) {
			let data = response.data;

			if (typeof (response.data) != "undefined") {
				let tr = "";
				let data = response.data;

				data.forEach(element => {
					tr += '<tr id="ch-sol-' + element.id + '"><td><div class="form-check"> ' +
						'<input type="checkbox" class="form-check-input"  value="' + element.id + '" name ="ch-solicitudes" onChange = "calcularTotalPorOperador(this, \'cant-solicitud-seleccionada\', 1)">' +
						'<label class="form-check-label" for="ch-sol-' + element.id + '"></label>' +
						'</div></td><td>' + element.id + '</td><td class="text-center">';

					if (typeof (element.new_chat) != "undefined" && element.new_chat != null)
						tr += '<i  class="fa fa-check text-success"  id ="' + element.new_chat.id + '">';

					tr += '</td><td>';
					if (element.operador_asignado != 0)
						tr += element.operador_asignado['nombre_apellido']

					tr += '</td><td>' + element.fecha_ultima_actividad + '</td><td>' + element.estado + '</td></tr>';
				});

				$("#solicitud-seleccionada tbody").html(tr);
			} else {
				Swal.fire("No se encontro ninguna solicitud que coincida con el parametro de busqueda");
			}
		}
	});
}

function checkSeleccionados(name) {
	var operadores = [];
	$.each($("input[name='" + name + "']:checked"), function () {
		operadores.push($(this).val());
	});

	return operadores;
}

function calcularTotalPorOperador(element, content, caso) {
	let name = $(element).attr('name');
	let id = $(element).attr('id');
	let seleccionados = [];
	seleccionados = checkSeleccionados(name)

	switch (caso) {
		case 1: //solo se desea mostrar la cantidad de opciones seleccionadas
			$("#" + content).html(seleccionados.length);
			break;

		case 2: //se desea mostrar el total de asignaciones por operador
			let cantidadXoperador = 0;
			let chatXoperador = 0;
			let total = parseInt($("#cant-operador-4").html());
			let totalChats = parseInt($("#cant-chats-operador-4").html());

			if (seleccionados.length > total) { $("#" + id).prop('checked', false); } else { }

			if (seleccionados.length != 0) {
				cantidadXoperador = Math.round(total / seleccionados.length);
				chatXoperador = Math.round(totalChats / seleccionados.length);
			}


			$("#" + content).html(cantidadXoperador);
			$("#cant-chats-por-operador").html(chatXoperador);

			break;

		default:
			break;
	}
}

function asignarSolicitudes(elemento) {
	let tipo_asignacion = $(elemento).attr('id');
	let selecciones = [];
	const formData = new FormData();
	base_url = $("input#base_url").val() + "api/operadores/asignar_solicitudes";

	$(elemento).prop("disabled", true);
	formData.append("tipo_asignacion", tipo_asignacion);

	switch (tipo_asignacion) {
		case "asig-1":
			if ($("#operador-1").val() != "" && $("#operador-1").val() != null && $("#operador-2").val() != null && $("#operador-2").val() != "" && solicitudes.length > 0) {
				formData.append("designado", $("#operador-1").val());
				formData.append("receptor", $("#operador-2").val());
				formData.append("fin", $("#hasta-1").val());
				formData.append("inicio", $("#desde-1").val());
				formData.append("solicitudes", solicitudes);

				$.ajax({
					type: "POST",
					url: base_url,
					data: formData,
					processData: false,
					contentType: false,

					success: function (response) {
						Swal.fire(response.message);
						cargarAsignaciones($("#operador-1"));
						cargarAsignaciones($("#operador-2"));
						$(elemento).prop("disabled", false);
					}, error(e) {
						$(elemento).prop("disabled", false);
					}
				});

			} else {
				Swal.fire("Seleccione los operadores correspondientes para realizar la asignación y asegurece de que existan solicitudes para asignar");
				$(elemento).prop("disabled", false);
			}
			break;

		case "asig-2":
			selecciones = checkSeleccionados("ch-solicitudes");

			if (selecciones.length > 0 && $("#operador-3").val() != "" && $("#operador-3").val() != null) {
				formData.append("receptor", $("#operador-3").val());
				formData.append("solicitudes", selecciones);
				$(elemento).prop("disabled", false);

				$.ajax({
					type: "POST",
					url: base_url,
					data: formData,
					processData: false,
					contentType: false,

					success: function (response) {
						Swal.fire(response.message);
						$(elemento).prop("disabled", false);

					}, error(e) {
						$(elemento).prop("disabled", false);
					}
				});

			} else {
				Swal.fire("Seleccione las solicitudes correspondientes para realizar la asignación ");
				$(elemento).prop("disabled", false);
			}

			break;

		case "asig-3":
			selecciones = checkSeleccionados("ch-operadores");

			if (selecciones.length > 0 && $("#operador-4").val() != "" && $("#operador-4").val() != null) {
				formData.append("designado", $("#operador-4").val());
				formData.append("receptores", selecciones);
				formData.append("fin", $("#hasta-3").val());
				formData.append("inicio", $("#desde-3").val());
				formData.append("solicitudes", solicitudes);

				$.ajax({
					type: "POST",
					url: base_url,
					data: formData,
					processData: false,
					contentType: false,

					success: function (response) {
						Swal.fire(response.message);
						$('.total').html("0");
						cargarAsignaciones($("#operador-4"));
						$(elemento).prop("disabled", false);
					}, error(e) {
						$(elemento).prop("disabled", false);
					}
				});

			} else {
				Swal.fire("Seleccione los operadores correspondientes para realizar la asignación y asegurece de que existan solicitudes para asignar");
				$(elemento).prop("disabled", false);
			}

			break;

		case "asig-4":
			if ($("#operador-5").val() != null && $("#operador-6").val() != null) {
				formData.append("designado", $("#operador-5").val());
				formData.append("receptor", $("#operador-6").val());
				formData.append("fin", $("#hasta-4").val());
				formData.append("inicio", $("#desde-4").val());

				$.ajax({
					type: "POST",
					url: base_url,
					data: formData,
					processData: false,
					contentType: false,

					success: function (response) {
						Swal.fire(response.message);
						$('.total').html("0");
						cargarAsignaciones($("#operador-6"));
						$(elemento).prop("disabled", false);
					}, error(e) {
						$(elemento).prop("disabled", false);
					}
				});

			} else {
				Swal.fire("Seleccione los operadores correspondientes para realizar la asignación ");
				$(elemento).prop("disabled", false);
			}
			break;

		case "asig-5":
			selecciones = checkSeleccionados("ch-chat");

			if (selecciones.length > 0 && $("#operador-7").val() != null) {
				formData.append("receptor", $("#operador-7").val());
				formData.append("chats", selecciones);

				$.ajax({
					type: "POST",
					url: base_url,
					data: formData,
					processData: false,
					contentType: false,

					success: function (response) {
						Swal.fire(response.message);
						consultarChats();
						$(elemento).prop("disabled", false);

					}, error(e) {
						$(elemento).prop("disabled", false);
					}
				});

			} else {
				Swal.fire("Seleccione los Chats correspondientes para realizar la asignación ");
				$(elemento).prop("disabled", false);
			}
			break;
		default:
			break;
	}

}

function consultarChats() {

	base_url = $("input#base_url").val() + "api/operadores/consultar_chats/" + $("#telefono").val();

	$.ajax({
		type: "GET",
		url: base_url,

		success: function (response) {
			if (typeof (response.data) != "undefined") {
				let tr = "";
				let data = response.data;

				data.forEach(element => {
					tr += '<tr id="ch-chat-' + element.id + '"><td><div class="form-check"> ' +
						'<input type="checkbox" class="form-check-input"  value="' + element.id + '" name ="ch-chat" onChange = "calcularTotalPorOperador(this, \'cant-chats-operador-7\', 1)">' +
						'<label class="form-check-label" for="ch-chat-' + element.id + '"></label>' +
						'</div></td><td>' + element.id + '</td><td>';

					if (element.id_operador != 0)
						tr += element.id_operador

					tr += '</td><td>' + element.fecha_ultima_recepcion + '</td><td>' + element.status_chat + '</td><td>' + element.type + '</td></tr>';
				});

				$("#chats-seleccionados tbody").html(tr);

			} else {
				Swal.fire("No se encontro ninguna solicitud que coincida con el parametro de busqueda");
			}
		}
	});
}

var _validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];
function verificarExtensionImagen(oInput) {
	if (oInput.type == "file") {
		var sFileName = oInput.value;
		if (sFileName.length > 0) {
			var blnValid = false;
			for (var j = 0; j < _validFileExtensions.length; j++) {
				var sCurExtension = _validFileExtensions[j];
				if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
					blnValid = true;
					break;
				}
			}

			if (!blnValid) {

				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: sFileName + " es un archivo invalido, las extenciones validas son: " + _validFileExtensions.join(", ")
				});

				oInput.value = "";
				return false;
			}
			else {
				document.getElementById('preview').src = window.URL.createObjectURL(oInput.files[0]);
				avatar = oInput.files[0];
			}
		}
	}
	return true;
}

function reset() {
	solicitudes = [];
	$("select").val("");
	$("input[type='number']").val("");
	$('input:checkbox').prop('checked', false);
	$('table#asig-operador-4 tbody').html("");
	$('table#operador-seleccionado tbody').html("");
	$('table#solicitud-seleccionada tbody').html("");
	$('table#asig-operador-2 tbody').html("");
	$('table#asig-operador-1 tbody').html("");
	$('.total').html("0");
}

function toggle(source) {
	checkboxes = document.getElementsByClassName('diaSemana');
	for (var i = 0, n = checkboxes.length; i < n; i++) {
		checkboxes[i].checked = source.checked;
	}
	
}
function cancelEditHorario() {
	$('#slc_operadores_horario').val("");
	$('#datetime_entrada').val("");
	$('#datetime_salida').val("");
	$(".form-check-input").prop("disabled", false);
	$("#slc_operadores_horario").prop("disabled", false);
	$(".form-check-input").prop("checked", false);
	$('#cancelEditHorario').css("display", "none");
	$("#registrar-horario").css("display", "block");
	$("#actualizar-horario").css("display", "none");

}