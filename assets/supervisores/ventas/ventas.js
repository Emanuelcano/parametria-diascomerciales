$('#slc-ausencia').change('on', function (params) {
    getAusencias();
});


function vistaGestionAsigAutomatica() {
    $('#main').show();
    $("#main-ausencias").hide();
	$("#main-horarios").hide();
    base_url = $("input#base_url").val() + "supervisores/Supervisores/vistaGestionAsigAutomatica";
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        } 
    });
}


function vistaGestionObligatoria() {
    $('#main').show();
    $("#main-ausencias").hide();
	$("#main-horarios").hide();
    base_url = $("input#base_url").val() + "supervisores/Supervisores/vistaGestionObligatoria";
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}
function cargarVistaTiempos() {
    $('#main').show();
    $("#main-ausencias").hide();
	$("#main-horarios").hide();
    base_url = $("input#base_url").val() + "supervisores/Supervisores/vistacargarTiemposGestion";
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}

function nuevaConfigurcion() {
    base_url = $("input#base_url").val() + "supervisores/Supervisores/vistaNuevaConfigurcion";
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}

function editarConfigurcion(e) {
    let elemenoC = e.getAttribute('value');
    base_url = $("input#base_url").val() + "supervisores/Supervisores/vistaEditarConfigurcion";
    $.ajax({
        type: "GET",
        url: base_url,
        data: { id: elemenoC },
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}
function listaOperadores() {
    $('#main').show();
    $("#main-ausencias").hide();
	$("#main-horarios").hide();
	$("#tp_HorarioOperadores").css("display", "none");
	user_modulos = [];
	base_url = $("input#base_url").val() + "supervisores/Supervisores/VistaOperadoresVentas";
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


function initTableOperadporesVentas_vacio() {
    $('#tp_operadoresVentas').dataTable().fnDestroy()
    $('#tp_operadoresVentas').dataTable({
		"oLanguage": {
			"sEmptyTable": "Seleccione un tipo de operador para gestionar"
		}
	});    
}


function initTableOperadporesVentas(tipo_operador) {
    let base_url = $("#base_url").val();
    $('#tp_operadoresVentas').dataTable().fnDestroy()
    $('#tp_operadoresVentas').dataTable( {
        "responsive":true,
        'scrollY': "400px",
        'scrollCollapse': true,
        "processing":true,
        "language": spanish_lang,
		"emptyTable": "Ningún dato disponible para el tipo de operador.",
		"error": {
			"system": "Ha ocurrido un error en el sistema intente nuevamente."
		},
        'iDisplayLength': 10,
        'paging':false,
        'info':true,
        "searching": true, 
        "order": [[ 2, "desc" ]],
        "ajax":
                $.fn.dataTable.pipeline( {
                "url": base_url + 'api/ApiOperadores/operadoresVentas/'+tipo_operador,
                "type" : "GET",
                "pages": 5
            } ),
            columns:[
                {
                    'data': null,
                    render: function (data, type, row, meta) {
                        if(data['gestion_obligatoria'] == 0){
                            let checkbox =
                            '<input type="checkbox" value="' + data['idoperador'] + '" name="operadorCheckbox" class="form-check-input" id="' + data['idoperador'] + '">';
                            return checkbox;
                        }else {
                            let checkbox =
                            '<input type="checkbox" value="' + data['idoperador'] + '" name="operadorCheckbox" class="form-check-input" id="' + data['idoperador'] + '" checked>';
                            return checkbox;
                        }
                        
                        
                    }
                },
                { 'data': "nombre_apellido" },
				{ 'data': "descripcion" },//tipo operador
                { 'data': "equipo" },//equipo 
                {
                    'data': null,
                    render: function (data, type, row, meta) {
                        switch (data['gestion_obligatoria']) {
                            case "0":
                                estado = "DESHABILITADO";
                                break;
                            case "1":
                                estado = "HABILITADO";
                                break;
                            default:
                                break;
                        }
                        return estado;
                    }
                },
            ],
        });    
}

$(document).on('click','#button_tipo_operador_buscar', function () {
	let tipo_operador = $('#input_tipo_operador_buscar').val();
	if (tipo_operador != 0) {

		initTableOperadporesVentas(tipo_operador);
		$('#operadoresVentas > div > div.col-lg-11.text-center > a').attr('disabled',false);

	} else {

		$('#input_tipo_operador_buscar').focus();

	}

});

$(document).on('change','#input_tipo_operador_buscar', function () {
	$('#operadoresVentas > div > div.col-lg-11.text-center > a').attr('disabled',true);
});

function inicializar_select_tipo_operador() {
	let base_url = $("#base_url").val();
	$.ajax({
		url: base_url + 'api/operadores/get_tipos_operadores',
		type: 'GET',
		dataType: 'JSON',
		success: function (response) {
			$.each(response.data, function (i, item) {
				$('#input_tipo_operador_buscar').append($('<option>', { 
					value: item.idtipo_operador,
					text : item.descripcion 
				}));
			});
		},
		error: function (jqXHR,estado,error) {

		}
	});
}

function procesarOperadores()
{
	if(!$("#operadoresVentas > div > div.col-lg-11.text-center > a").attr('disabled')){

		Swal.fire({
			title: 'Cambio de estado',
			text: '¿Estás seguro de que quieres habilitar a los operadores seleccionados?',
			icon: 'warning',
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar',
			showCancelButton: 'true'
		}).then((result) =>{
			if(result.value){
				let tipo_operador = $('#input_tipo_operador_buscar').val();
				let elements = document.querySelectorAll('input[name=operadorCheckbox]:checked');
				let operadores = new Array; 
				for (let checkbox of elements) 
				{  
					if (checkbox.checked)
					operadores.push(checkbox.value);    
				}
				let base_url = $("#base_url").val(); 
				$.ajax({
					url: base_url + 'api/operadores/update_configuracion_solicitud_obligatoria',
					type: 'post',
					dataType: 'JSON',
					data: { operadores : operadores, 
							tipo_operador : tipo_operador
					},
					success: function (response) {
						if(response.status.ok == true) {
							if (elements.length > 0) {
								Swal.fire({
									title: 'Gestiones oblicatorias.',
									text: 'Se habilitó a los operadores seleccionados.',
									icon: 'success',
									type: 'success',
									confirmButtonText: 'OK'
								})
							} else {
								Swal.fire({
									title: 'Solicitud oblicatoria.',
									text: 'Se desabilitaron todos los operadores.',
									icon: 'success',
									type: 'error',
									confirmButtonText: 'OK'
								})
							}

							let tipo_operador = $('#input_tipo_operador_buscar').val();
							initTableOperadporesVentas(tipo_operador);

						}else{
							Swal.fire({
								title: 'Gestiones oblicatorias.',
								text: 'No se pudo habilitar a la los operadores seleccionados.',
								icon: 'error',
								type: 'error',
								confirmButtonText: 'OK'
								})
						}
					},
					error: function (jqXHR,estado,error) {
						Swal.fire({
							title: 'Solicitud oblicatoria.',
							text: 'Problema de comunicación.',
							icon: 'error',
							type: 'error',
							confirmButtonText: 'OK'
							})
					}
				})
			}
		})

	}
    
}



function cancelar() {

    $('#main').show();
    $("#main-ausencias").hide();
	$("#main-horarios").hide();
    base_url = $("input#base_url").val() + "supervisores/Supervisores/vistaGestionObligatoria";
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");

        }
    }).done(function() {
        var obj=document.getElementById('configA');
        obj.click();
      });
}

//Resfrescar para estimular responsive
$('#configA').on('click', function () {
	initTableConfiguraciones();
});

//Resfrescar para estimular responsive
$('#configO').on('click', function () {
	let tipo_operador = $('#input_tipo_operador_buscar').val();
	if (tipo_operador != 0) {

		initTableOperadporesVentas(tipo_operador);
		$('#operadoresVentas > div > div.col-lg-11.text-center > a').attr('disabled',false);

	} else {
		initTableOperadporesVentas();
		$('#input_tipo_operador_buscar').focus();

	}
});

function initTableConfiguraciones(){
    $('#nuevaConfig').hide();
    let base_url = $("#base_url").val();
    $('#tp_configuraciones').dataTable().fnDestroy()
        $('#tp_configuraciones').dataTable( {
            "responsive":true,
            "scrollX": true,
            "processing":true,
            "language": spanish_lang,
            'iDisplayLength': 10,
            'paging':true,
            'info':true,
            "searching": true, 
            // "serverSide": true,
            "order": [ 14, "desc" ],
            "ajax":
                    $.fn.dataTable.pipeline( {
                    "url": base_url + 'api/solicitud/configuracionesGestionObligatoria',
                    "type" : "GET",
                    "pages": 5
                } ),
				'columns': [
					{ 'data': "nombre_apellido", 'className': "text-center"  },//Usuario Administrador
					{//Estado de la campaña
						'data': null,
						render: function (data, type, row, meta) {
							switch (data['estado']) {
								case '0':
									estado = '<i class="fa fa-exclamation-triangle col-lg-12" aria-hidden="true" style="color:red;text-align: center!important;font-size: 18px;" title="Inactiva"></i>';
									break;
								case '1':
									estado = '<i class="fa fa-check col-lg-12" aria-hidden="true" style="color:green;text-align: center!important;font-size: 18px;" title="Activa"></i>';
									break;
								default:
									break;
							}
							return estado;
						}, 'className': "text-center" 
					},
					{//Operadores en gestión}
						'data': 'tipo_operador_descripcion', 'className': "text-center" 
					},
					{ 'data': "min_proceso_obligatorio",'className': "text-center" },//Duración de campaña automática
					{ 'data': "dias_busqueda", 'className': "text-center" },//Días de búsqueda
					{ 'data': "min_gestion", 'className': "text-center"  },//Tiempo de gestión
					{//Extensiones consecutivas
						'data': null,
						render: function (data, type, row, meta) {
							switch (data['extensiones_consecutivas']) {
								case '0':
									estado = "Inactivas";
									break;
								case '1':
									estado = "Activas";
									break;
								default:
									break;
							}
							return estado;
						}, 'className': "text-center" 
					},
					{ 'data': "min_extension", 'className': "text-center"  },//Tiempo de extensión de solicitud
					{ 'data': "min_get_solicitudes", 'className': "text-center"  },//Periodo actualización de solicitudes
					{ 'data': "horas_ultima_gestion", 'className': "text-center"  },//Periodo últimas gestionadas
					{ 'data': "min_gestion_chats", 'className': "text-center"  },//Tiempo de gestión de chats
					{ 'data': "min_chat_documentos", 'className': "text-center"  },//Periodo de actualización documentos
					{ 'data': "seg_ejecucion", 'className': "text-center"  },//Tiempo de preparación
					{ 'data': "porcentaje_warning", 'className': "text-center" },//Tiempo estado alerta
					{ 'data': "porcentaje_alerta_extension", 'className': "text-center"  },//Tiempo estado preventivo
					{ 'data': "segundos_alert_ext", 'className': "text-center" },//Tiempo ventana de alerta
					{
						'data': null,
						render: function (data, type, row, meta) {
							
							let EditarEstado = '<a class="btn btn-xs bg-yellow" value="' + data['id'] + '" id="cambiarEstado" title="Cambiar Estado" ><i class="fa fa-exchange" ></i>';
							let EditarConfig = '<a class="btn btn-xs bg-blue" value="' + data['id'] + '" id="editConfig" title="Editar Configuración" onclick="editarConfigurcion(this);"><i class="fa fa-pencil" ></i><div>';
							return EditarEstado + EditarConfig;
						}, 'className': "text-center"
					}
				],   
        });
}

$('body').on('click','#tp_configuraciones a[id="cambiarEstado"]',function(event){

    Swal.fire({
		title: 'Cambio de estado',
		text: '¿Estás seguro de que quieres modificar el estado?',
		icon: 'warning',
		confirmButtonText: 'Aceptar',
		cancelButtonText: 'Cancelar',
		showCancelButton: 'true'
	}).then((result) =>{
        if(result.value){
            let base_url = $("#base_url").val();
            let config = $(this).attr('value');
            $.ajax({
                url: base_url + 'api/solicitud/update_estado_configuracion_solicitud_obligatoria',
                type: 'post',
                dataType: 'JSON',
                data: { id: config },
                success: function (response) {
                    if(response.status.ok == true) {
                        
                        
                        Swal.fire({
                            title: 'Solicitud oblicatoria.',
                            text: 'El estado de la configuración fue actualizada con éxito.',
                            icon: 'success',
                            
                            confirmButtonText: 'OK'
                        })
                        
                        initTableConfiguraciones();
                        
                           
                    }else{
                        Swal.fire({
                            title: 'Solicitud oblicatoria.',
                            text: 'No se pudo actualizada la configuración.',
                            icon: 'error',
                            
                            confirmButtonText: 'OK'
                            })
                    }
                },
                error: function (jqXHR,estado,error) {
                    Swal.fire({
                        title: 'Solicitud oblicatoria.',
                        text: 'Problema de comunicación.',
                        icon: 'error',
                        
                        confirmButtonText: 'OK'
                        })
                }
            })
        }
    })

});

function registrarConfiguracion() {
   
    if (validarForConfig() != true) {
        let base_url = $("#base_url").val()
        $.ajax({
            url: base_url + 'api/solicitud/add_configuracion_solicitud_obligatoria',
            type: 'POST',
            dataType: 'JSON',
            data: {
				tipoOperador:            $('#tipoOperador').val(),
                segEjecucion:            $('#segEjecucion').val(), 
                minSolicitud:            $('#minSolicitud').val(), 
                minGestion:              $('#minGestion').val(),
                porcentajePreventivo:    $('#porcentajePreventivo').val(),
                porcentajeAlerta:        $('#porcentajeAlerta').val(),
                segundosAlerta:          $('#segundosAlerta').val(),
                minutosExtension:        $('#minutosExtension').val(),
                extensionesConsecutivas: $('#extensionesConsecutivas').val(),
                minGestionChats:         $('#minGestionChats').val(),
                minProcesoObligatorio:   $('#minProcesoObligatorio').val(),
                minDocChats:             $('#minDocChats').val(),
                diasBusqueda:            $('#diasBusqueda').val(),
                horaUltimaGestion:       $('#horaUltimaGestion').val(),
                operadores:              $('#operadores').val(),
                estado:                  $('#estado').val(),
                },
                success: function (response) {
                    
                    if(response.status.ok == true) {
                        
                        Swal.fire({
                            title: 'Solicitud oblicatoria.',
                            text: 'La nueva configuración fue creada con éxito.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                            })
                            cancelar();
                    }else{
                       
                        Swal.fire({
                            title: 'Solicitud oblicatoria.',
                            text: 'No se pudo crear la nueva configuración.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                            })
                    }
                },
                error: function (jqXHR,estado,error) {
                    
                    Swal.fire({
                        title: 'Solicitud oblicatoria.',
                        text: 'Problema de comunicación.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                        })
                }
        })
        
    } else {
        Swal.fire({
            title: 'Solicitud oblicatoria.',
            text: 'Todos los campos son obligatorios.',
            icon: 'error',
            confirmButtonText: 'OK'
            })
    }
    
}

function editConfig(){
    if (validarForConfig() != true) {
        let base_url = $("#base_url").val()
        $.ajax({
            url: base_url + 'api/solicitud/update_configuracion_solicitud_obligatoria',
            type: 'post',
            dataType: 'JSON',
            data: {
                id:                      $('#idConfig').val(),
				tipoOperador:            $('#tipoOperador').val(),
                segEjecucion:            $('#segEjecucion').val(), 
                minSolicitud:            $('#minSolicitud').val(), 
                minGestion:              $('#minGestion').val(),
                porcentajePreventivo:    $('#porcentajePreventivo').val(),
                porcentajeAlerta:        $('#porcentajeAlerta').val(),
                segundosAlerta:          $('#segundosAlerta').val(),
                minutosExtension:        $('#minutosExtension').val(),
                extensionesConsecutivas: $('#extensionesConsecutivas').val(),
                minGestionChats:         $('#minGestionChats').val(),
                minProcesoObligatorio:   $('#minProcesoObligatorio').val(),
                minDocChats:             $('#minDocChats').val(),
                diasBusqueda:            $('#diasBusqueda').val(),
                horaUltimaGestion:       $('#horaUltimaGestion').val(),
                operadores:              $('#operadores').val(),
                estado:                  $('#estado').val(),
                },
                success: function (response) {
                    if(response.status.ok == true) {
                        Swal.fire({
                            title: 'Solicitud oblicatoria.',
                            text: 'La configuración fue actualizada con éxito.',
                            icon: 'success',
                           
                            confirmButtonText: 'OK'
                            })
                            cancelar();
                    }else{
                        Swal.fire({
                            title: 'Solicitud oblicatoria.',
                            text: 'No se pudo actualizadar la nueva configuración.',
                            icon: 'error',
                            
                            confirmButtonText: 'OK'
                            })
                    }
                },
                error: function (jqXHR,estado,error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Solicitud oblicatoria.',
                        text: 'Problema de comunicación.',
                        
                        confirmButtonText: 'OK'
                        })
                }
        })
    } else {
        Swal.fire({
            title: 'Solicitud oblicatoria.',
            text: 'Todos los campos son obligatorios.',
            icon: 'error',
            
            confirmButtonText: 'OK'
            })
    }
    
}

function validarForConfig(){

    let tipoOperador = document.getElementById("tipoOperador").value;
    let segEjecucion = document.getElementById("segEjecucion").value;
    let minSolicitud = document.getElementById("minSolicitud").value;  
    let minGestion = document.getElementById("minGestion").value;
    let porcentajePreventivo = document.getElementById("porcentajePreventivo").value;
    let porcentajeAlerta = document.getElementById("porcentajeAlerta").value;
    let minutosExtension = document.getElementById("minutosExtension").value;
    let extensionesConsecutivas = document.getElementById("extensionesConsecutivas").value;
    let minGestionChats = document.getElementById("minGestionChats").value;
    let minProcesoObligatorio = document.getElementById("minProcesoObligatorio").value;
    let minDocChats = document.getElementById("minDocChats").value;
    let diasBusqueda = document.getElementById("diasBusqueda").value;
    let horaUltimaGestion = document.getElementById("horaUltimaGestion").value;
    let estado = document.getElementById("estado").value;
    
    if ((tipoOperador == "") 
    || (segEjecucion == "") 
    || (minSolicitud == "") 
    || (minGestion == "") 
    || (porcentajePreventivo == "") 
    || (porcentajeAlerta == "") 
    || (segundosAlerta == "")  
    || (minutosExtension == "") 
    || (extensionesConsecutivas == "") 
    || (minGestionChats == "")
    || (minProcesoObligatorio == "")
    || (minDocChats == "")
    || (diasBusqueda == "")
    || (horaUltimaGestion == "")
    || (estado == "") 
    ) {  
        //COMPRUEBA CAMPOS VACIOS
        return true;
    }
    
}


function cargarVistaAusencias() {
	$('#main').html('');
	$("#main-ausencias").show();
	$("#main-horarios").hide();
	$("#tp_HorarioOperadores").css("display", "none");
	$("#tp_HorarioOperadores_wrapper").css("display", "none");

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
                    if(item.idtipo_operador == 1 || item.idtipo_operador == 4 ){

                        opciones += '<option value="' + item.idoperador + '" >' + item.nombre_apellido + '</option>';
                    }
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

$('#registrar-ausencia').click('on', function (params) {
    registrarAusencia();
    
});

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
                            
                            Swal.fire({
                                icon: 'success',
								text: response.message
							});
                            getAusencias();
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
                   
					$('#tp_ausencias').DataTable({
                        "order": [[ 0, "desc" ]]
                    }).destroy().clear().rows.add(tabla).draw();

				} else {
					$('#tp_ausencias').DataTable().clear().draw();
				}
			}
		});
	}
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
