
function listaOperadores() {
	$('#main').show();
    $("#main-ausencias").hide();
	$("#main-horarios").hide();
	$("#tp_HorarioOperadores").css("display", "none");
	user_modulos = [];
	base_url = $("input#base_url").val() + "supervisores/Supervisores/VistaOperadoresCobranzas";
	$.ajax({
		type: "POST",
		url: base_url,
		success: function (response) {
			$("#main").html(response);
			$("#cargando").css("display", "none");
			TablaPaginada('tp_Operadores', 0, 'asc');
		}
	})
}



function cargarOperador(operador, elemento, usuario) {
    $.ajax({
        type: "POST",
        url: $("input#base_url").val() + "supervisores/Supervisores/datos_operador",
        data: {
            'operador': operador, 
            'elemento': elemento
        },
        success: function (response) {
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
        
        }
    });
}

function cargarModulos(operador) {
	base_url = $("input#base_url").val() + "api/operadores/get_modulos_nombre/" + operador;
	$.ajax({
		type: "GET",
		url: base_url,
		success: function (response) {
			if (response.status == '200') {
				$('document').ready(function() {
					if(response.data.length === 0){
						let opciones = '<p style="background-color: #f7f1fb!important;font-size: 14px;color: red;font-weight: 500;padding:1%; width: 100%">El usuario no tiene modulos asignados.</p>';
						$('#modulosBoxOperadores').html(opciones);		
					}else{
						let user_modulos = response.data;
						user_modulos.forEach(item => {
							$("#modulosBoxOperadores").append('<p style="background-color: #f7f1fb!important;font-size: 12px;color: #000;font-weight: 400;padding:5px; width: 100%">'+ item.nombre +'</p>');					
						});
					}	
				})
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

function actualizarOperador() {
	base_url = $("input#base_url").val() + "api/operadores/actualizar_operador_cobranza";
	$.ajax({
	type: 'POST',
	url: base_url,
	dataType: 'JSON',
	data: {
		id_operador: $("#id_operador").val(),
		id_usuario: $("#id_usuario").val(),
		estado:$("#slc_estado").val(),
		tipo_operador: $("#slc_tipoOperador").val(),
		equipo: $("#slc_equipo").val(),
		token: $("#verificacion-token").val()
	},
	success: function (response) {
		if(response.status.code === 200) {
			Swal.fire({
				icon: 'success',
				text: response.message,
			});
		}else
		{
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: response.message,
			});
		}
	}
	});	
}

function cambiarEstado(operador, estado) {
	var estado_texto = "";
	var cambioEstado;
	if (estado == 1) {
		estado_texto = "Desactivar"
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