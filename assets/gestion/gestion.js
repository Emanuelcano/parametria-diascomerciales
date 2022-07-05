var gestion = 0;
var max_time = 0; 
var tiempo_timer, cronometro_id, timer_sol;
var solicitudes_obligatorias = [];
var track_generado = extension = false;
var timer = seg = min = 0;
var s;
var estado_gestion = true;
var telefono_original = '';
var telefono_familiar_original = '';
var nombre_apellido_original = '';
var nombre_apellido_familiar_original = '';
var id_parentezco_original = '';
var en_descanso = false;
$(() =>{

    if (typeof(GET_SOLICITUDES) != "undefined" && GET_SOLICITUDES > 0) {
        timer_sol = setInterval(function () {search_solicitudes_obligatorias()}, GET_SOLICITUDES * 60 * 1000); // actualizar lista cada 30 min
    }
    
    if(!Swal.isVisible()){
        var_agendarcita.myInt = setInterval(var_agendarcita.getCasosAgendados, 60000);
        var_agendarcita.myInt2 = setInterval(var_agendarcita.getSolicitudAjustes, 120000);
    }
    var_videollamada.init();
});

function init(){
    search_solicitudes_obligatorias();
    s = GET_START;
    timer = 100;
    $("header .progress .progress-bar").html("La apertura de solicitudes iniciara en: "+new Date(s).toISOString().substr(11, 8));
    cronometrar(s);
}    

function cronometrar(tiempo){
    escribir(tiempo);
    //$("header .progress .btn.btn-xs.btn-warning.cronometro-button").addClass( "disabled");
    cronometro_id = setInterval(function(){escribir(tiempo);},1000);
    //console.log("inicio cronometro: "+ cronometro_id);
}

function escribir(tiempo){
    s--;
    timer = timer - 100000/(tiempo*1000) ;
    
    $("header .progress").removeClass( "hide");
    $("header .progress .cronometro-button").removeClass( "hide");
    $("header .progress .progress-bar").css( "width", timer+"%");
    $("header .progress .progress-bar").html("La apertura de solicitudes iniciara en: " +new Date(s * 1000).toISOString().substr(11, 8));

    if(timer <= WARNING_TIME && timer > DANGER_TIME){
        $("header .progress .progress-bar").removeClass( "progress-bar-success");
        $("header .progress .progress-bar").addClass( "progress-bar-warning");
    } else {
        if(timer <= DANGER_TIME){
            $("header .progress .progress-bar").removeClass( "progress-bar-warning");
            $("header .progress .progress-bar").addClass( "progress-bar-danger");
            $("header .progress .btn.btn-xs.btn-warning.cronometro-button").removeClass( "disabled");
            $("header .progress .btn.btn-xs.btn-info.cronometro-button").removeClass( "disabled");
            
        }
    }

    //*document.getElementById("hms").innerHTML = new Date(s * 1000).toISOString().substr(11, 8); 
    /*if (s == 10) 
        search_solicitudes_obligatorias();*/
    
    if (s == 0) {
        
        
        if (solicitudes_obligatorias.length > 0){
            cronometro_id = clearInterval(cronometro_id);
            consultar_solicitud(solicitudes_obligatorias[0].id, $('#txt_render_v').val(), true);
            $("header .progress .progress-bar").removeClass( "progress-bar-warning");
            $("header .progress .progress-bar").removeClass( "progress-bar-danger");
            $("header .progress .progress-bar").addClass( "progress-bar-success");
            $("header .progress").addClass( "hide");
            $("header .progress .progress-bar").html('');
        } else {
            //se agregan 3 minutos mas
            max_time = EXTENSION_TIME;
            timer = 100;
            seg = 1;
            min = EXTENSION_TIME/60000;
            s = EXTENSION_TIME/1000;
            $("header .progress .progress-bar").addClass( "progress-bar-success");
            $("header .progress .progress-bar").removeClass( "progress-bar-danger");
            search_solicitudes_obligatorias();
        }
    }
}

function restart_solicitudes_obligatorias() {

    Swal.fire({
        title: 'Retomar la gestion de solicitudes',
        text: 'Al aceptar dara inicio a la apertura automatica de solicitudes para su gestion.',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true',
    }).then((result) => {
        if (result.value) {

            $("#horaEntrada").val(moment().format('YYYY-MM-DD HH:mm:ss'));
            $("header .progress .progress-bar").removeClass( "progress-bar-warning");
            $("header .progress .progress-bar").removeClass( "progress-bar-danger");
            $("header .progress .progress-bar").addClass( "progress-bar-success");
            $("header .progress").addClass( "hide");
            $("header .progress .progress-bar").html('');
            
            if(solicitudes_obligatorias.length > 0 ){
                consultar_solicitud(solicitudes_obligatorias[0].id, $('#txt_render_v').val(), true);
            }else{
                //$(".btn-danger.cronometro-button").addClass("disabled")
                search_solicitudes_obligatorias(true);
            }

        }

    });

        
}

function solicitudeUpdate(idSolicitude, id_operador, type_contact = 0, message = "", fecha_alta) {
    $.ajax({
        url: base_url + 'api/solicitud/actualizar',
        type: 'POST',
        dataType: 'json',
        data: { "id_solicitud": idSolicitude, "estado": message, "id_operador": id_operador, "action": message },
    })
        .done(function (response) {

            if(response.envio){

                if (response.envio.status == 200) {
                    Swal.fire("Notificación enviada",response.envio.mensaje,'success');
                }else{
                    Swal.fire("Solicitud NO visada",response.envio.mensaje,'warning');
                }
            }

            if (response.status.ok) {
                track_generado = true;
                var estado = response.solicitud[0].estado;
                $("#box_botones_gestion #analysis_buro").val(response.solicitud[0].respuesta_analisis);
                $("#box_botones_gestion #solicitud_status").val(estado);
                let gestion_mensaje = "[" + message + "]";
                let analysis_buro = $("#box_botones_gestion #analysis_buro").val();
                let solicitud_status = $("#box_botones_gestion #solicitud_status").val();
                button_status(analysis_buro, solicitud_status);
                //Muestro el nuevo estado
                if (estado == 'VISADO' || estado == 'RECHAZADO' || estado == 'ESCALADO ANALIZADO') {
                    var row = document.getElementById('icono');
                    if (row) {
                        row.parentElement.parentElement.remove(row);
                    }
                }
                if (estado == 'VERIFICADO') {
                    $("#nombre_estado").html('<i style="font-size: 20px; margin-right: 8px; color: orange" class="fa fa-eye">&nbsp;<label style="font-family: arial;">' + estado + '</label></i>');
                    toastr["info"]("VERIFICADO", "ESTADO:");
                }
                else if (estado == 'VALIDADO') {
                    $("#nombre_estado").html('<i style="font-size: 20px; margin-right: 8px; color: brown" class="fa fa-check-square-o">&nbsp;<label style="font-family: arial;">' + estado + '</label></i>');
                    toastr["info"]("VALIDADO", "ESTADO:");
                }
                else if (estado == 'APROBADO') {
                    $("#nombre_estado").html('<i style="font-size: 20px; margin-right: 8px; color: green" class="fa fa-check">&nbsp;<label style="font-family: arial;">' + estado + '</label></i>');
                    toastr["success"]("APROBADO", "ESTADO:");
                }
                else if (estado == 'VISADO') {
                    toastr["info"]("VISADO", "ESTADO:");
                }
                else if (estado == 'PAGARE') {
                    gestion_mensaje += " Envio del pagaré al cliente.";
                    toastr["success"]("PAGARE ENVIADO", "ESTADO:");
                }
                else if (estado == 'RECHAZADO') {
                    $("#nombre_estado").html('<i style="font-size: 20px; margin-right: 8px; color: red" class="fa fa-times-circle">&nbsp;<label style="font-family: arial;">' + estado + '</label></i>');
                    toastr["error"]("RECHAZADO", "ESTADO:");
                } else if (estado == 'ESCALADO ANALIZADO') {
                    toastr["info"]("ESCALADO ANALIZADO", "ESTADO:");
                    $('#analizado').prop('disabled', true);
                }
                if(typeof(response.trackGestion) == 'undefined'){
                    saveTrack(gestion_mensaje, type_contact, idSolicitude, id_operador);
                }


            } else {
                $(this).attr('disabled', false);
            }
        })
        .fail(function (response) {
            if (message == 'RECHAZADO') {
                if (toastr["error"]("Por favor ingrese un comentario de tipo RECHAZO", "NO SE RECHAZO")) {
                    $('#rejected').prop('disabled', false);
                }
                window.location.href = '#box_tracker';
            }
            $(this).attr('disabled', false);
        })
        .always(function () {

        });
}

function solicitudeUpdateStep(idSolicitude, id_operador, step, type_contact = 0, message = "") {
    $.ajax({
        url: base_url + 'api/solicitud/actualizar',
        type: 'POST',
        dataType: 'json',
        data: { "id_solicitud": idSolicitude, "id_operador": id_operador, "action": message, 'paso': step },
    })
        .done(function (response) {
            if (response.status.ok) {
                let gestion_mensaje = "[" + message + "]";
                saveTrack(gestion_mensaje, type_contact, idSolicitude, id_operador);
            }
        })
        .fail(function (response) {
            window.location.href = response.responseJSON.redirect;
        })
        .always(function () {

        });
}

//Actualiza las imagenes pegandole a un endpoint 
function solicitudeUpdateImage(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/Gestion/update_image',
        type: 'POST',
        dataType: 'html',
        data: { "id_solicitud": id_solicitud },
    })
        .done(function (res) {
            var response = JSON.parse(res);
            if(response.success){
                toastr["success"]("Se actualizo correctamente", "IMAGENES ACTUALIZADAS");
                cargar_box_galery(id_solicitud);
                
            }else {
                toastr["error"](response.title_response, "ERROR");

            }

        })
        .fail(function (response) {
            window.location.href = response.responseJSON.redirect;
        })
        .always(function () {

        });
}

/***************************************************************************/
// carga de imagenes
/***************************************************************************/
function cargar_box_ref_documentos(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/imagesDocumentos/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
        data: {
            d : $("#client").data('number_doc')
        }
    })
        .done(function (response) {
            $(".box_ref_documentos").html(response);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}
function cargar_box_videos_referencia(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/videosReferencia/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".box_videos").html(response);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}
function cargar_box_ref_archivos(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/imagenesArchivos/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".box_ref_archivos").html(response);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}
function cargar_box_galery(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/imagenesGaleria/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".box_galery").html(response);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}
function cargar_box_metricas(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/get_metricas/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".metricas").html(response);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}

function cargar_box_title(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/get_title/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".title").html(response);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}

function cargar_box_datos_contacto(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/get_datos_contacto/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".datos-contacto").html(response);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}

/***************************************************************************/
// Tracker
/***************************************************************************/

var get_track = (id_solicitud) => {
    
    var documento= $("#client").data("number_doc");
    $.ajax({
        url: base_url + 'solicitud/gestion/track/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $("#tracker").text();
            $("#tracker").css('background-color', '');
            $("#tracker").html(response);
            get_chat_whatsapp(documento);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}
/***************************************************************************/
// Chat Whatsapp
/***************************************************************************/
function get_chat_whatsapp(documento) {
    if ($(".row-chat-track").find('#whatsapp').length > 0) {
        $.ajax({
            url: base_url + 'solicitud/gestion/whatsapp_paginado/' + documento + '/' + paginacion + '/1/',
            type: 'GET',
        })
            .done(function (response) {
                $("#whatsapp").html(response);

            })
            .fail(function () {
            })
            .always(function () {
            });
    }
}

function button_status() {
    // buttons
    let verified = $("#box_botones_gestion #verified");
    let validated = $("#box_botones_gestion #validated");
    let approved = $("#box_botones_gestion #approved");
    let visado = $("#box_botones_gestion #visado");
    let analizado = $("#box_botones_gestion #analizado");
    let rejected = $("#box_botones_gestion #rejected");
    let analysis_buro = $("#box_botones_gestion #analysis_buro").val();
    let solicitud_status = $("#box_botones_gestion #solicitud_status").val();
    let step = $("#box_botones_gestion #step").val();
    let tipo_operador = $("#box_botones_gestion #tipo_operador").val();

    if (tipo_operador == "AUDITOR VENTAS") {
        $(verified).prop('disabled', true);
        $(validated).prop('disabled', true);
        $(approved).prop('disabled', true);
        $(visado).prop('disabled', true);
        $(analizado).prop('disabled', false);
        $(rejected).prop('disabled', false);

    } else if (analysis_buro == "APROBADO" && step >= 16) {

        if (solicitud_status == "") {
            $(verified).prop('disabled', false);
            $(validated).prop('disabled', true);
            $(visado).prop('disabled', true);
            $(analizado).prop('disabled', true);
            $(approved).prop('disabled', true);
            $(rejected).prop('disabled', false);
        }
        else if (solicitud_status == "ANALISIS") {
            $(verified).prop('disabled', false);
            $(validated).prop('disabled', true);
            $(visado).prop('disabled', true);
            $(analizado).prop('disabled', false);
            $(approved).prop('disabled', true);
            $(rejected).prop('disabled', false);
        }
        else if (solicitud_status == "VERIFICADO") {
            $(verified).prop('disabled', true);
            $(validated).prop('disabled', false);
            $(visado).prop('disabled', true);
            $(analizado).prop('disabled', false);
            $(approved).prop('disabled', true);
            $(rejected).prop('disabled', false);
        } else if (solicitud_status == "VALIDADO") {
            $(verified).prop('disabled', true);
            $(validated).prop('disabled', true);
            $(visado).prop('disabled', true);
            $(analizado).prop('disabled', false);
            $(approved).prop('disabled', false);
            $(rejected).prop('disabled', false);
        } else if (solicitud_status == "APROBADO") {
            $(verified).prop('disabled', true);
            $(validated).prop('disabled', true);
            $(approved).prop('disabled', true);
            $(visado).prop('disabled', false);
            $(analizado).prop('disabled', false);
            $(rejected).prop('disabled', false);
        } else if (solicitud_status == "VISADO") {
            $(verified).prop('disabled', true);
            $(validated).prop('disabled', true);
            $(approved).prop('disabled', true);
            $(visado).prop('disabled', true);
            $(analizado).prop('disabled', false);
            $(rejected).prop('disabled', false);

        } else {
            $(verified).prop('disabled', true);
            $(validated).prop('disabled', true);
            $(visado).prop('disabled', true);
            $(analizado).prop('disabled', true);
            $(approved).prop('disabled', true);
            $(rejected).prop('disabled', false);
        }
    } else {
        $(verified).prop('disabled', true);
        $(validated).prop('disabled', true);
        $(visado).prop('disabled', true);
        $(analizado).prop('disabled', true);
        $(approved).prop('disabled', true);
        $(rejected).prop('disabled', false);
    }
}


function guardarRespuestaOperador(id_operador, id_gestion, denominacion) {
	let base_url = $("#base_url").val();
	var id_credito = $('#id_credito').val();
	var id_operador = id_operador;
	var id_gestion = id_gestion;
	
	
	
	
}

/**
 * Guarda la gestion de un operador
 *
 * @param id_operador
 * @param id_gestion
 * @param idDetalleRespuesta
 */
function guardarGestionOperador(id_operador, id_gestion, idDetalleRespuesta, id_campania) {
	let base_url = $("#base_url").val();
	var id_credito = $('#id_credito').val();
	var id_operador = id_operador;
	var id_gestion = id_gestion;
	$.ajax({
		url: base_url + 'api/campanias/consultar_asigando_operador',
		type: 'POST',
		data: {'id_operador': id_operador, 'id_credito': id_credito}
	}).done(function (response) {
		if (response.data == '') {

		} else {
			$.ajax({
				url: base_url + 'api/campanias/guardarGestionOperador',
				type: 'POST',
				data: {
					'id_credito': id_credito,
					'id_operador': id_operador,
					'id_gestion': id_gestion,
					'idDetalleRespuesta': idDetalleRespuesta,
					'id_campania': id_campania
				}
			})
				.done(function (response) {})
				.fail(function (response) {})
				.always(function () {
					$('#btn_save_comment').removeClass('disabled');
				});
		}
	})
		.fail(function (response) {})
}

function saveTrack(comment, typeContact, idSolicitude, idOperator) {
    
    $('#btn_save_comment').addClass('disabled');
    $.ajax({
        url: base_url + 'api/track_gestion',
        type: 'POST',
        dataType: 'json',
        data: { 'observaciones': comment, 'id_tipo_gestion': typeContact, 'id_solicitud': idSolicitude, 'id_operador': idOperator }
    })
        .done(function (response) {
            if (response.status.ok) {
                //addElemTimeLine(response.comment);
                updateFlagNewFlow(idSolicitude);
                track_generado = true;
            }
        })
        .fail(function (response) {
            //window.location.href = response.responseJSON.redirect;

        })
        .always(function () {
            $('#btn_save_comment').removeClass('disabled');
        });
}

function updateFlagNewFlow(idSolicitude){
   /* $.ajax({
        url: base_url + 'softphone/Llamada/updateFlagNewFlow',
        type: 'POST',
        dataType: 'json',
        data: { 'idSolicitud': idSolicitude }
    })
        .done(function (response) {

        })
        .fail(function (response) {
            

        })
        .always(function () {
            
        });*/
}

function consultar_solicitud(id_solicitud, render_view = false, solicitud_urgente=false) {
	console.log("ver 1");
    $("#porVisarTableContenedor").hide();
    if (render_view == false) {
        //event.preventDefault();
    }
    paginacion = 0;

    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'solicitud/' + id_solicitud +'/'+ render_view,
        type: 'GET',
    })
        .done(function (response) {
            $("#tabla_desembolso").hide();
            $("#tabla_solicitudes").hide();
            $("#solicitudPendientes").hide();
            $("#botones_filtro").hide();
            $("#section_search_solicitud #form_search").hide();
            $("#section_search_solicitud #result").hide();
            $("#texto_agenda").hide();
            $("#texto_sol_ajustes").hide();
            $("#texto").text();
            $("#texto").html(response);
            cargar_box_title(id_solicitud);
            if (render_view == 'transRechazada') {
                cargar_box_cargar_documento(id_solicitud);
                cargar_box_slider_documentos(id_solicitud);
            } else {
                cargar_box_datos_contacto(id_solicitud);
                cargar_box_ref_documentos(id_solicitud);
                cargar_box_ref_archivos(id_solicitud);
                cargar_box_galery(id_solicitud);
            }
            get_track(id_solicitud);
            //si la solicitud consultada es urgente inicio la gestion obligatoria
            if (solicitud_urgente) {
                console.log("consultar solicitud ", id_solicitud);
                iniciar_gestion_obligatoria(id_solicitud);
            }
            $("#slc_bancos").hide();
        })
        .fail(function () {
        })
        .always(function () {
        });

}


function envioLinkDePago (element) {
    let base_url = $("#base_url").val();
    let titulo = "Enviar link PSE";
    if (element.getAttribute('data-medio-pago') == "efectivo") {
        titulo = "Enviar link Efectivo Baloto, Gana, Daviplata, otros";
    }

    if ($('#box_client_title').data("id_cliente") != "") {

        Swal.fire({
            title: titulo,
            text: '',
            icon: 'warning',
            confirmButtonText: 'Enviar link de pago',
            cancelButtonText: 'Cancelar',
            showCancelButton: 'true'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url:  base_url + "api/credito/envio_link_pago_whatsapp",
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    type: "POST", 
                    dataType: 'JSON',
                    data: {
                        id_cliente: $('#box_client_title').data("id_cliente"), 
                        telefono:   element.getAttribute('data-mobilephone'), 
                        medio_pago: element.getAttribute('data-medio-pago'),
                        tipo_pago:  element.getAttribute('data-tipo-acuerdo') ? element.getAttribute('data-tipo-acuerdo') : '',
                        id_acuerdo: element.getAttribute('data-id-acuerdo')   ? element.getAttribute('data-id-acuerdo')   : '',
                        canal:      element.getAttribute('data-canal-chat')
                    },
                    success: function (response) {
                        if(response.success == true) {
                            swal("Envio de link de pago." ,  response.title_response ,  "success")
                            let id_solicitud = $("#id_solicitud").val();
                            let id_operador = $("#id_operador").val();
                            let type_contact = 2;
                            let comment = "<b>[ENVIO WHATSAPP]</b>" + "<br><b>Se envio link de pago</b>";
                            saveTrack(comment, type_contact, id_solicitud, id_operador);
                        }else{
                            swal("Envio de link de pago." ,  'El link de pago no pudo ser enviado' ,  "error")
                        }
                    },
                    error: function (jqXHR,estado,error) {
                        swal("Envio de link de pago." ,  'Problema de comunicación.' ,  "error")
                    }
                })
                
            }
        });
        
    }else{
        swal("Envio de link de pago." ,  'El cliente no esta habilitado para esta operacion' ,  "error")
    }
}


/**
 * Solicitudes obligatorias
 * 
 */

function search_solicitudes_obligatorias(si_llama_desde_cerrar = false) {

    let e = $("#horaEntrada").val();
	let entrada = new Date(moment(e).toDate());

            var route = $("input#base_url").val()+"api/solicitud/consultar_solicitudes_obligatorias";
            $.ajax({
                url: route,
                type: 'GET',
                dataType: 'JSON'
            })
            .done(function(response) {
                if (response.status.ok) {
					solicitudes_obligatorias = response.data;
                    $('#txt_render_v').val(response.render_view);
					// debugger;              
					if (solicitudes_obligatorias.length > 0) {
						
						if ($("#tabla_solicitudes").css('display') == 'block') {
							$("#tabla_solicitudes").hide();
						}
						
						if (si_llama_desde_cerrar) {    //renderizar solicitud.

							consultar_solicitud(solicitudes_obligatorias[0].id, render_view = false, solicitud_urgente = true);

						}
						

					}
                } 
                if(!response.habilitado) {
                        Swal.fire("Gestión automática desactivada",'El sistema de gestión automática ha sido desactivado.<br> Al finalizar el caso, actualiza la página.','info');
                        $("#close_solicitude").on('click', function(){location.reload();});
                        estado_gestion = false;
                }
                if(response.habilitado && !estado_gestion) {
                        Swal.fire("Gestión automática activada",'El sistema de gestion obligatoria ha sido activada.<br> Actualice la pagina una vez que termine la gestión','info');
                        $("#close_solicitude").on('click', function(){location.reload();});
                        estado_gestion = true;
                }
				if(response.habilitado && response.sin_pendientes) {
					if (document.getElementById('texto').childNodes.length == 0) {
						Swal.fire("Sin solicitudes", response.message, 'info');
						$("#close_solicitude").on('click', function () { location.reload(); });
						estado_gestion = false;
						listarSolicitudes();
					}
			
				}
            })
            .fail(function() {
            })
            .always(function() {
            });
    //}
}


//DETENER Y REINICIAR TIEMPO DE GESTION.
async function temporizador_control_descanso() {

	let id_solicitud = $("#id_solicitud").val();

	if (!en_descanso) {
		const { value: descanso } = await Swal.fire({
			title: 'Iniciar pausa de gestión',
			input: 'select',
			inputOptions: {
				'baño': 'Baño',
				'almuerzo': 'Almuerzo'
			},
			inputPlaceholder: 'Seleccione un motivo.',
			showCancelButton: true,
			inputValidator: (value) => {
				return new Promise((resolve) => {
					if (value === 'almuerzo' || value === 'baño') {
						//AÑADIR TRACK DE PAUSA DE GESTION
						$.ajax({
							url: base_url + 'api/solicitud/gestionar_descanso_operador',
							type: 'POST',
							dataType: 'json',
							data: { 'id_gestion': gestion, 'motivo': value, 'en_descanso': 0 }
						})
							.done(function (response) {
								if (response.status.ok) {
									//dibujar boton
									$('#cronometro-button-descanso > i').removeClass('fa-pause');
									$('#cronometro-button-descanso > i').addClass('fa-play');
									resolve()
								} else {
									resolve('No se pudo generar el seguimiento del descanso');
								}


							});

					} else {
						resolve('Necesitas elegir un motivo:)')
					}

				})
			}
		});

		if (descanso) {
			clearInterval(tiempo_timer);
			en_descanso = true;
			Swal.fire(`Detuviste la gestión por el motivo: ${descanso}`);
		};

	} else {
		en_descanso = false;
		//AÑADIR TRACK DE DESPAUSA DE GESTION
		$.ajax({
			url: base_url + 'api/solicitud/gestionar_descanso_operador',
			type: 'POST',
			dataType: 'json',
			data: { 'id_gestion': gestion, 'en_descanso': 1 }
		})
			.done(function (response) {
				if (response.status.ok) {
					console.log("Gestion reiniciada, track creado");
					//dibujar boton
					$('#cronometro-button-descanso > i').removeClass('fa-play');
					$('#cronometro-button-descanso > i').addClass('fa-pause');
				}
			});

		tiempo_timer = setInterval(function () {
			timer = timer - 100000 / max_time;
			if (seg <= 0) {
				min = min - 1;
				seg = 59;
			} else {
				seg = seg - 1;
			}
			$("header .progress .progress-bar").css("width", timer + "%");
			$("header .progress .progress-bar").html("0" + min + ":" + ((seg < 10) ? ("0" + seg) : seg));

			if (timer <= WARNING_TIME && timer > DANGER_TIME) {
				$("header .progress .progress-bar").removeClass("progress-bar-success");
				$("header .progress .progress-bar").addClass("progress-bar-warning");
			} else {
				if (timer <= DANGER_TIME && gestion > 0) {
					// debugger;
					if (!extension && $("header .progress .progress-bar").hasClass("progress-bar-warning"))
						solicitar_extension(id_solicitud);

					$("header .progress .progress-bar").removeClass("progress-bar-warning");
					$("header .progress .progress-bar").addClass("progress-bar-danger");
				}
			}

			if (min == 0 && seg == 0) {
				cerrar_solicitud_obligatoria();
			}

		}, 1000);

	}

}



function iniciar_gestion_obligatoria(id_solicitud) {
    
    tiempo_timer = clearInterval(tiempo_timer);
    cronometro_id = clearInterval(cronometro_id);

    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url+'api/solicitudes/iniciar_gestion_obligatoria',
        type: 'POST',
        dataType: 'json',
        data: {'id_solicitud':id_solicitud}
    })
    .done(function(response) {
        if(response.status.ok)
        {

            //toastr["success"](response.message, "GESTION OBLIGATORIA");
            gestion = response.gestion;  
            extension = track_generado = false;

            solicitudes_obligatorias.shift();
            console.log("inicia gestion obligatorias con solicitudes:"+solicitudes_obligatorias.length);

            $("header .progress").removeClass( "hide");
            $(".btn-danger.cronometro-button").addClass("disabled");
            //$("header .progress .cronometro-button").addClass( "hide");

            max_time = parseInt($("#horaEntrada").data("min_gestion"))*60000;
            min = parseInt($("#horaEntrada").data("min_gestion"));
            timer = 100;
            seg = 1;


            tiempo_timer = setInterval(function(){ 
                timer = timer - 100000/max_time ;
                

                if(seg <= 0){
                    min = min - 1 ;
                    seg = 59;
                } else{
                    seg = seg -1;
                }
                $("header .progress .progress-bar").css( "width", timer+"%");
                $("header .progress .progress-bar").html( "0"+min+":"+((seg < 10)? ("0"+seg):seg));

                if(timer <= WARNING_TIME && timer > DANGER_TIME){
                    $("header .progress .progress-bar").removeClass( "progress-bar-success");
                    $("header .progress .progress-bar").addClass( "progress-bar-warning");
                } else {
                    if(timer <= DANGER_TIME && gestion > 0){
                        if(!extension &&  $("header .progress .progress-bar").hasClass( "progress-bar-warning"))
                            solicitar_extension(id_solicitud);

                        $("header .progress .progress-bar").removeClass( "progress-bar-warning");
                        $("header .progress .progress-bar").addClass( "progress-bar-danger");
                    }
                }
   

                if(min == 0 && seg == 0) {
                    cerrar_solicitud_obligatoria();
                }


            }, 1000);
            
        } else{
            //toastr["error"](response.message, "GESTION OBLIGATORIA"); 
            cerrar_solicitud_obligatoria();
        }
    });

}

function solicitar_extension(id_solicitud){

    Swal.fire({
        title: '¡Extencion de tiempo!',
        text: '¿Necesitas '+EXTENSION_TIME/60000+' minutos más?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true',
        allowOutsideClick: false,
        allowEscapeKey: false,
        timer: ALERT_TIME
    }).then((result) => {
        if (result.value) {

            $.ajax({
                url: base_url+'api/solicitudes/extension_gestion_obligatoria',
                type: 'POST',
                dataType: 'json',
                data: {'id_solicitud':id_solicitud, 'id_gestion': gestion, 'control':$("#horaEntrada").data("control")}
            })
            .done(function(response) {
                if(response.status.ok)
                {
                    //toastr["success"](response.message, "EXTENSION DE TIEMPO");
                    if(!EXTENCIONES_CONSECUTIVAS)
                        extension = true;

                    //se agregan 3 minutos mas
                    max_time = EXTENSION_TIME;
                    timer = 100;
                    seg = 1;
                    min = EXTENSION_TIME/60000;
                    s = EXTENSION_TIME/1000;
                    $("header .progress .progress-bar").addClass( "progress-bar-success");
                    $("header .progress .progress-bar").removeClass( "progress-bar-danger");


                } else{
                    //toastr["error"](response.message, "EXTENSION DE TIEMPO"); 
                }
            });

        }

    });

}

function cerrar_solicitud_obligatoria() {
	//si la gestion esta en pausa(en_gestion == true), y es cerrada, reactiva la gestion para mantener track y buen funcionamiento del boton de pausa. 
	if (en_descanso) {
		temporizador_control_descanso();			
	}

	let id_solicitud = $("#id_solicitud").val();

	$("#id_operador").data('validaciones', $("#validaciones").val());
	$("#dashboard_principal #texto").html("");
	$("#section_search_solicitud #form_search").show();
	$("#tabla_solicitudes").show();
	$("#solicitudPendientes").show();
	$("#tabla_desembolso").show();
	$("#botones_filtro").show();
	$("#texto_agenda").show();
	$("#texto_sol_ajustes").show();
	var_agendarcita.getCasosAgendados();
	var_agendarcita.getSolicitudAjustes();
	$("#sol_validaciones .badge").html($("#id_operador").data('validaciones'));

	//cerrar solicitud obligatoria
	timer = 0;
	tiempo_timer = clearInterval(tiempo_timer);
	$("header .progress .progress-bar").removeClass("progress-bar-warning");
	$("header .progress .progress-bar").removeClass("progress-bar-danger");
	$("header .progress .progress-bar").addClass("progress-bar-success");
	$("header .progress").addClass("hide");
	$("header .progress .progress-bar").html("");

	if (typeof (pusher) != "undefined") {
		$.each(channels, function (index, value) {
			pusher.unsubscribe(value.name);
			value.unbind();
		});
		channels = [];
	}
	//diferencia en horas de la fecha de inicio de jornada y la fecha actual
	let e = $("#horaEntrada").val();
	let entrada = new Date(moment(e).toDate());
	let hoy = new Date(moment().toDate());
	// let diff = parseInt(Math.abs( (hoy - entrada)/60000));
	let diff = parseInt(Math.abs(entrada.getTime() - hoy.getTime()) / (1000 * 60) % 60);//minutos //parseInt(Math.abs( (hoy - entrada)/60000));

	console.log("solicitudes cant:" + solicitudes_obligatorias.length);

	//si esta dentro del tiempo de buscar solicitudes
	if(diff <= PROCESO_OBLIGATORIO){
		timer = 100;
		console.log('minutos en buscar solicitudes->'+diff);
		console.log('Tiempo obligatorio en buscar solicitudes->'+PROCESO_OBLIGATORIO);
		/**
		 * si se gestiono el 80% de las soliciltudes o transcurrieron 30 min desde la ultima actualizacion de la lista de solicitudes
		 * se actualiza la misma
		 */
  
		console.log("cerrar solicitud", solicitudes_obligatorias[0]);
		//sacamos la solicitud de la lista de solicitudes por gestionar
		if(solicitudes_obligatorias.length > 0 && solicitudes_obligatorias[0].id == id_solicitud)
			solicitudes_obligatorias.shift();
			
		  
	} else {
		s = GESTION_CHAT * 60;
		timer = 100;
		$("header .progress .progress-bar").html("La apertura de solicitudes iniciara en: " + new Date(s * 1000).toISOString().substr(11, 8));
		$(".btn-danger.cronometro-button").removeClass("disabled");
		cronometrar(s);
		setTimeout(function () { $("#horaEntrada").val(moment().format('YYYY-MM-DD HH:mm:ss')); }, GESTION_CHAT * 60 * 1000);
		Swal.fire("Inicia la Gestión Manual", 'Accede a los casos pendientes por responder.<br>En breve volverás a la gestión automática', 'info');
	}

	//gestionamos el cierre de la solicitud

	$.ajax({
		url: base_url + 'api/solicitudes/cerrar_gestion_obligatoria',
		type: 'POST',
		dataType: 'json',
		data: { 'id_solicitud': id_solicitud, 'id_gestion': gestion, 'track': track_generado }
	})
		.done(function (response) {
			if (response.status.ok) {
				//toastr["success"](response.message, "GESTION OBLIGATORIA");
				extension = true;

				$("header .progress .progress-bar").addClass("progress-bar-success");
				
                
                if (solicitudes_obligatorias.length <= 4 && solicitudes_obligatorias.length > 0) {
                    console.log("paso 1");
                    search_solicitudes_obligatorias();
                    consultar_solicitud(solicitudes_obligatorias[0].id, render_view = $('#txt_render_v').val(), solicitud_urgente=true);
                }else{
                    console.log("paso 2");
                    search_solicitudes_obligatorias(true);
                
                
                } 

			} else {
            //toastr["error"](response.message, "GESTION OBLIGATORIA"); 
			}
		});

}



function actualizarPagare(id_solicitud){

    let base_url = $("#base_url").val();
    //const formData = new FormData();

    //formData.append("id_solicitud", id_solicitud);
    $.ajax({
        url: URL_PAGARE+'api/uanataca/pagare/actualizar_pagares/'+id_solicitud,
        type: 'POST',
        data: '',
        processData: false,
        contentType: false,
    })
        .done(function (response) {
            if(response.status.ok){
                toastr["success"](response.message, "ACTUALIZACION DE PAGARE");
                cargar_box_ref_documentos(id_solicitud);
            } else{
                toastr["error"](response.message, "ACTUALIZACION DE PAGARE");
            }
        })
        .fail(function () {
        })
        .always(function () {
        });

    
 }

// Pantalla de comparacion
function compareImages(elem) {
    event.preventDefault();
    $("#compare").show();
    if ($(elem).hasClass("screen_1")) {
        $("#screen_1 img").attr("src", $(elem).data("src"));
        $("#box_galery .images .screen_1").each(function (index, elem) {
            $(elem).removeClass("btn-primary").addClass("btn-default");
        })
        $("#box_galery .item-galery .screen_1").each(function (index, elem) {
            $(elem).removeClass("btn-primary").addClass("btn-default");
        })
        $(elem).addClass("btn-primary");
    } else if ($(elem).hasClass("screen_2")) {
        $("#screen_2 img").attr("src", $(elem).data("src"));
        $("#box_galery .images .screen_2").each(function (index, elem) {
            $(elem).removeClass("btn-primary").addClass("btn-default");
        })
        $("#box_galery .item-galery .screen_2").each(function (index, elem) {
            $(elem).removeClass("btn-primary").addClass("btn-default");
        })
        $(elem).addClass("btn-primary");
    }
}
// Agrega un elemento a la galeria
function addImage(image, position = 'after') {
    let date = new Date();
    html = '<div class="col-md-2 item-galery" style="margin-left: 100px; width: 152px">';
    html += '<img class="img-thumbnail" src="' + image.uri + '?' + date.getTime() + '">';
    html += '<div class="caption">';
    html += '<p style="font-size: smaller">' + image.descripcion + '</p>';
    html += '<a href="#" class="screen_1 btn btn-default" onclick="compareImages(this)" data-src="' + image.uri + '">1</a>';
    html += '<a href="#" class="screen_2 btn btn-default" onclick="compareImages(this)" data-src="' + image.uri + '">2</a>';
    html += '</div>';
    html += '</div>';

    if (position == 'after') {
        $("#box_galery #fotos").append(html);
    } else {
        $("#box_galery #fotos").prepend(html);
    }
}
function addDoc(doc, position = 'before') {
    html = '<a href="' + doc.uri + '" target="_blank" class="list-group-item" style="font-size:smaller;">' + doc.descripcion + '</a>';
    if (position == 'after') {
        $("#box_documento #field_files").append(html);
    } else {
        $("#box_documento #field_files").prepend(html);
    }
}


function buscarCredito(search) {
    let base_url = $("#base_url").val();
    table_search.processing(true);
    $.ajax({
        url: base_url + 'api/solicitud/buscar/',
        type: 'POST',
        dataType: 'json',
        data: search,
    })
        .done(function (response) {
            table_search.processing(false);
            table_search.clear();
            table_search.rows.add(response.solicitude);
            table_search.draw();
            $("#section_search_solicitud #result").show();

        })
        .fail(function (response) {
            //console.log("error");
        })
        .always(function (response) {
            //console.log("complete");
        });

}

function enviarDetalle(id_credito, id_cliente, cuentas) {
    let id_solicitud = $("#id_solicitud").val();
    let id_operador = $("section").find("#id_operador").val();

    const formData = new FormData();
    formData.append("id_cliente", id_cliente);
    formData.append("id_credito", id_credito);
    formData.append("cuentas", cuentas);

    let type_contact = 13;
    let mensaje = "Desglose de credito " + id_credito;
    let comment = "<b>[Mail]</b><br>Fecha: " + moment().format('DD-MM-YYYY') + "<br> Mensaje: " + mensaje;

    Swal.fire({
        title: '¡Atención!',
        text: '¿Estas seguro de que quieres enviar el correo?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: base_url + 'api/credito/enviarMailDesglose',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {
                if (response.status == '200') {

                    saveTrack(comment, type_contact, id_solicitud, id_operador);
                    Swal.fire("¡Perfecto!",response.message,'success');
                } else {
                    Swal.fire("¡Ups!",response.message,'error');
                }

            })
                .fail(function (response) {
                    //console.log("error");
                })
                .always(function (response) {
                    //console.log("complete");
                });

        }
    });
}

function consultarCredito(id_credito) {
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/credito/consultar_credito/' + id_credito,
        type: 'GET',
    })
        .done(function (response) {
            $("#detalle-proyeccion #tabla-detalle-pago tbody").html('');

            if (typeof (response.data[0]) != "undefined" && response.data[0] != null) {
                let credito = "";
                let creditoInfo = response.data;
                let descuentos = 0;
                let deuda = 0;
                let cobrado = 0;
                let ultimo_pago = '';
                let dias = 0;
                let cuotas_mora = 0;
                $("#detalle-proyeccion h3").html('DETALLE DEL CREDITO ' + id_credito + ' - <span class="text-' + ((creditoInfo[0].estado_credito == 'mora') ? 'red' : (creditoInfo[0].estado_credito == 'cancelado') ? 'green' : 'orange') + '">' + creditoInfo[0].estado_credito.toUpperCase() + '</span>');

                //detalle de pago por cuota
                creditoInfo.forEach(function (detalle, indice, array) {

                    descuentos += parseFloat(detalle.descuento);

                    if (detalle.estado != 'pagado') {
                        deuda += parseFloat(detalle.monto_cobrar);
                    }
                    cobrado += parseFloat(detalle.monto_cobrado);
                    if (dias < parseInt(detalle.dias_atraso)) {
                        dias = parseInt(detalle.dias_atraso);
                    }

                    if (detalle.estado == 'mora') {
                        cuotas_mora++;
                    }
                    if (detalle.fecha_cobro != null && (moment(ultimo_pago) < moment(detalle.fecha_cobro) || ultimo_pago == '')) {
                        ultimo_pago = detalle.fecha_cobro;
                    }

                    cuota = "<tr style='";
                    cuota += (detalle.estado == 'pagado') ? 'background: rgba(0, 128, 0, 0.2)' : '';
                    cuota += "'>";
                    cuota += "<td>" + detalle.numero_cuota + "</td>";
                    cuota += "<td>" + moment(detalle.fecha_vencimiento).format('DD-MM-YYYY') + "</td>";
                    cuota += "<td>$" + formatNumber(detalle.capital) + "</td>";
                    cuota += "<td>$" + formatNumber(detalle.interes) + "</td>";
                    cuota += "<td>$" + formatNumber(detalle.seguro) + "</td>";
                    cuota += "<td>$" + formatNumber(detalle.administracion) + "</td>";
                    cuota += "<td>$" + formatNumber(detalle.tecnologia) + "</td>";
                    cuota += "<td>$" + formatNumber(detalle.iva) + "</td>";
                    cuota += "<td style='background: rgba(255, 235, 59, 0.09);'>$" + formatNumber(detalle.monto_cuota) + "</td>";
                    cuota += "<td style='background: rgba(244, 67, 54, 0.25);'>";
                    cuota += (typeof (detalle.dias_atraso) != 'undefined') ? detalle.dias_atraso : '0';
                    cuota += "</td>";
                    cuota += "<td style='background: rgba(244, 67, 54, 0.25);'>$";
                    cuota += (typeof (detalle.interes_mora) != 'undefined') ? formatNumber(detalle.interes_mora) : formatNumber(0);
                    cuota += "</td>";
                    cuota += "<td style='background: rgba(244, 67, 54, 0.25);'>$";
                    cuota += (typeof (detalle.honorarios) != 'undefined') ? formatNumber(detalle.honorarios.sms_ivr_email) : formatNumber(0);
                    cuota += "</td>";
                    cuota += "<td style='background: rgba(244, 67, 54, 0.25);'>$";
                    cuota += (typeof (detalle.honorarios) != 'undefined') ? formatNumber(detalle.honorarios.rastreo) : formatNumber(0);
                    cuota += "</td>";
                    cuota += "<td style='background: rgba(244, 67, 54, 0.25);'>$";
                    cuota += (typeof (detalle.honorarios) != 'undefined') ? formatNumber(detalle.honorarios.prejuridico) : formatNumber(0);
                    cuota += "</td>";
                    cuota += "<td style='background: rgba(244, 67, 54, 0.25);'>$";
                    cuota += (typeof (detalle.honorarios) != 'undefined') ? formatNumber(detalle.honorarios.bpo) : formatNumber(0);
                    cuota += "</td>";

                    cuota += "<td style='background: rgba(3, 169, 244, 0.23);'>$";
                    cuota += (typeof (detalle.descuento) != 'undefined') ? formatNumber(detalle.descuento) : '0,00 ';
                    cuota += "</td>";
                    cuota += "<td>$";
                    cuota += (detalle.estado == 'pagado') ? '0,00' : formatNumber(detalle.monto_cobrar);
                    cuota += "</td>";
                    cuota += "<td>$" + formatNumber(detalle.monto_cobrado) + "</td>";
                    cuota += "<td>";

                    cuota += (detalle.fecha_cobro != null) ? moment(detalle.fecha_cobro).format('DD-MM-YYYY') : ' ';
                    cuota += "</td>";
                    cuota += "<td style='";
                    cuota += (detalle.estado == 'mora') ? 'background: rgba(244, 67, 54, 0.31)' : '';
                    cuota += (detalle.estado == 'pagado') ? 'background: rgba(0, 128, 0, 0.38)' : '';
                    cuota += (detalle.estado != 'pagado' && detalle.estado != 'mora') ? 'background: rgba(255, 235, 59, 0.18)' : '';
                    cuota += "'>";
                    cuota += (detalle.estado != 'pagado' && detalle.estado != 'mora') ? 'vigente' : detalle.estado;
                    cuota += "</td>";
                    cuota += "<td>";
                    cuota += (detalle.detalle_pagos.length > 0) ? ("<a class='btn' style='border-radius:50%; color:#333; padding:0px;' onclick='mostrarPagos(\"row-" + detalle.id + "\")'> <i class='fa fa-plus row-" + detalle.id + "'></i> </a>") : " ";
                    cuota += "</td>";
                    cuota += "</tr>";

                    //pagos de la cuota
                    let cadena = "";

                    if (detalle.detalle_pagos.length > 0) {
                        let pagos = detalle.detalle_pagos;

                        cadena += '<tr class="row-subtable hide" id="row-' + detalle.id + '" style="background: #e7ecee;">';
                        cadena += '<td colspan="21">';
                        cadena += '<table class="table table-bordered" style="box-shadow: 0px 2px 5px -2px; width:80%; margin: 0 auto;">'
                        cadena += '<thead style="background: rgba(138, 138, 138, 0.49);"><th>FECHA Y HORA</th><th >MEDIO</th><th >REFERENCIA EXTERNA</th><th >REFERENCIA INTERNA</th><th >FECHA PAGO</th><th >MONTO</th><th >ESTADO</th><th >RESULTADO</th></thead>';
                        cadena += '<tbody>';
                        pagos.forEach(function (pago, indice, array) {


                            cadena += '<tr style="';
                            cadena += (pago.estado == 1) ? 'background: rgba(0, 0, 0, 0.1);' : '';
                            cadena += '">';
                            cadena += '<td>' + moment(pago.fecha).format('DD-MM-YYYY H:mm:ss') + '</td>';
                            cadena += '<td>' + pago.medio_pago + '</td>';
                            cadena += '<td>';
                            cadena += (pago.referencia_externa != null) ? pago.referencia_externa : ' ';
                            cadena += '</td>';
                            cadena += '<td>';
                            cadena += (pago.referencia_interna != null) ? pago.referencia_interna : ' ';
                            cadena += '</td>';
                            cadena += '<td>' + moment(pago.fecha_pago).format('DD-MM-YYYY H:mm:ss') + '</td>';
                            cadena += '<td>$' + formatNumber(pago.monto) + '</td>';
                            cadena += '<td>' + pago.estado_razon + '</td>';
                            cadena += '<td ';
                            cadena += (pago.estado == 1) ? ' style="background: rgba(0, 128, 0, 0.2);"' : '';
                            cadena += '>';
                            cadena += (pago.estado == 1) ? 'COBRO REALIZADO' : 'No cobrado';
                            cadena += '</td>';
                            cadena += '</tr>';
                        });
                        cadena += '</tbody>';
                        cadena += '</table>'
                        cadena += '</td>';
                        cadena += '</tr>';
                    }

                    $("#detalle-proyeccion #tabla-detalle-pago tbody.principal").append(cuota + cadena);
                });

                credito += "<tr>";
                credito += "<td>" + moment(creditoInfo[0].fecha_otorgamiento.substr(0, 10)).format('DD-MM-YYYY') + "</td>";
                credito += "<td>$" + formatNumber(creditoInfo[0].monto_prestado) + "</td>";
                credito += "<td>" + creditoInfo[0].plazo + " - " + (creditoInfo[0].plazo) * 30 + "</td>";
                credito += "<td>" + moment(creditoInfo[0].fecha_primer_vencimiento.substr(0, 10)).format('DD-MM-YYYY') + "</td>";
                credito += "<td>$" + formatNumber(creditoInfo[0].monto_devolver) + "</td>";

                credito += "<td style='background: rgba(3, 169, 244, 0.23);'>$" + formatNumber(descuentos) + "</td>";
                credito += "<td style='background: rgba(139, 195, 74, 0.1);'>$" + formatNumber(deuda) + "</td>";
                credito += "<td style='background: rgba(139, 195, 74, 0.1);'>$" + formatNumber(cobrado) + "</td>";
                credito += "<td style='background: rgba(139, 195, 74, 0.1);'>" + ((ultimo_pago != '') ? moment(ultimo_pago.substr(0, 10)).format('DD-MM-YYYY') : '') + "</td>";

                credito += "<td style='";
                credito += (creditoInfo[0].estado_credito == 'mora') ? 'background: rgba(244, 67, 54, 0.31)' : '';
                credito += (creditoInfo[0].estado_credito == 'cancelado') ? 'background: rgba(0, 128, 0, 0.38)' : '';
                credito += (creditoInfo[0].estado_credito == 'pendiente') ? 'background: rgba(255, 235, 59, 0.18)' : '';
                credito += "'>";
                credito += (creditoInfo[0].estado_credito != null) ? creditoInfo[0].estado_credito : ' ';
                credito += "</td>";

                credito += "<td>" + dias + "</td>";
                credito += "<td>" + cuotas_mora + "</td>";

                $("#detalle-proyeccion #detalle-credito-proyeccion tbody").html(credito);
                $("#detalle-proyeccion a#enviarDetalle").click("on", function () { enviarDetalle(id_credito); })
                $('#detalle-proyeccion').modal('show');
            }
        })
        .fail(function () {
        })
        .always(function () {
        });
}

function get_llamadas_detalle_originacion(id_solicitud) {


    $.ajax({
        url: base_url + 'solicitud/gestion/api/gestion/marcacion/' + id_solicitud,
        type: 'GET'
    })
        .done(function (response) {
            $('#table-track_marcacion').dataTable().fnDestroy();
            if (response.status.ok) {

                let tabla = [];
                ausencias = response.data;
                ausencias.forEach(item => {

                    var callType = JSON.parse(callTypes);
                    var actor = JSON.parse(actores)

                    tabla.push([
                        moment(item.fecha).format('YYYY-MM-DD hh:mm:ss'),
                        (item.skill_result != null) ? item.skill_result : '',
                        (item.nombre != null) ? item.nombre : '',
                        item.name_agent,
                        item.telephone_number.substr(-10),
                        (typeof (item.nombres) != 'undefined') ? (item.nombres + ' ' + item.apellidos) : item.nombres_apellidos,
                        (typeof (item.parentesco) != 'undefined') ? 'REFERENCIA' : 'PERSONAL',
                        (item.talk_time != null) ? item.talk_time : '',
                        (typeof (callType[item.tipo_llamada]) != 'undefined') ? callType[item.tipo_llamada] : '',
                        (item.descri_typing_code != null) ? item.descri_typing_code : '',
                        (item.descri_typing_code2 != null) ? item.descri_typing_code2 : '',
                        actor[item.who_hangs_up],
                        item.central,
                        (item.path_audio != null) ? ('<a class="btn btn-xs btn-success reproducir-audio" data-audio="' + item.audio + '" title="Reproducir"><i class="fa fa-play"></i></a><a class="btn btn-info btn-xs descargar-audio" data-audio="' + item.audio + '" ><i class="fa fa-download"></i></a>') : ' ',
                    ]);
                });
                //$("#table-track_marcacion").removeClass('hidden');
                $('#table-track_marcacion').DataTable({ "order": [[0, "desc"]], 'pageLength': 10 }).clear().rows.add(tabla).draw();
                $('a.reproducir-audio').click('on', function () { recuperarAudio(this); });
                $('a.descargar-audio').click('on', function () { recuperarAudio(this); });
                $('#table-track_marcacion_paginate').click("on", function () {
                    $("a.reproducir-audio").attr("onclick", "").unbind("click");
                    $("a.descargar-audio").attr("onclick", "").unbind("click");
                    $('a.reproducir-audio').click('on', function () { recuperarAudio(this); });
                    $('a.descargar-audio').click('on', function () { recuperarAudio(this); });
                });

            } else {
                $('#table-track_marcacion').DataTable().clear().draw();
            }
        })
        .fail(function () {
        })
        .always(function () {
        });

}

function recuperarAudio(element) {
    let id_audio = $(element).data('audio');
    $(element).addClass('disabled');
    $.ajax({
        url: base_url + 'api/credito/reproducir_audio/' + id_audio,
        type: 'GET'
    })
        .done(function (response) {


            if (response.status.ok) {

                if ($(element).hasClass('descargar-audio')) {
                    var url = response.url_audio
                    var filename = url.substring(url.lastIndexOf("/") + 1).split("?")[0];
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = filename;
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    delete a;
                } else {
                    $(".reproduccion-audios .modal-content").html('<p>Para descargar el audio: Hacer click derecho sobre el audio y seleccionar la opcion "Guardar audio como"</p><audio src="' + response.url_audio + '" preload="auto" controls style="width: 100%;"><p>Tu navegador no implementa el elemento audio.</p></audio>');
                    $(".reproduccion-audios").modal('toggle');
                    $('.modal.reproduccion-audios').on('hidden.bs.modal', function (e) {
                        $(".reproduccion-audios .modal-content audio").attr('src', '');
                    });
                }
            } else {

            }
            $(element).removeClass('disabled');
        })
        .fail(function () {
            $(element).removeClass('disabled');
        })
        .always(function () {
            $(element).removeClass('disabled');
        });

}

function mostrarPagos(elementoId) {
    if ($("#" + elementoId).hasClass('hide')) {
        $("." + elementoId).removeClass('fa-plus');
        $("." + elementoId).addClass('fa-minus');
        $("#" + elementoId).removeClass('hide');
    } else {
        $("." + elementoId).removeClass('fa-minus');
        $("." + elementoId).addClass('fa-plus');
        $("#" + elementoId).addClass('hide');
    }
}

function resetTelefono(id_solicitud) {
    event.preventDefault();
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'atencion_cliente/Gestion/resetTelefono/' + id_solicitud,
        type: 'GET',
    })
        .done(function (response) {
            $("#celdaValTelefono").html('&nbsp;-&nbsp;0');
        })
        .fail(function () {
            //console.log("error");
        })
        .always(function () {
            //console.log("complete");
        });

}

function resetEmail(id_solicitud) {
    event.preventDefault();
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'atencion_cliente/Gestion/resetEmail/' + id_solicitud,
        type: 'GET',
    })
        .done(function (response) {
            $("#celdaValEmail").html('&nbsp;-&nbsp;0');
        })
        .fail(function () {
            //console.log("error");
        })
        .always(function () {
            //console.log("complete");
        });

}

var_agendarcita = [];
var_agendarcita.currentBoxAgenda = {};

var_agendarcita.agendar = ( id_solicitud, id_operador, nombres, apellidos) => {
    event.preventDefault();
    let fecha_agenda = $("#inp_fecha").val();
    let hora_agenda = $("#inp_hora").val();
    let motivo = $("#inp_motivo").val();
    let base_url = $("#base_url").val();

    swal.fire({
		title: "¿Esta seguro?",
		text: "¿Estas seguro que desea agendar?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Si, Agendar"
	}).then((result) => {
		if (result.value) {
            $.ajax({
                url: base_url + 'api/agendar',
                type: 'POST',
                dataType: 'json',
                data: { 'id_solicitud': id_solicitud, 'id_operador': id_operador, 'nombres': nombres, 'apellidos': apellidos, 'fecha_agenda': fecha_agenda, 'hora_agenda': hora_agenda, 'motivo': motivo }
            })
            .done((response) => {
                if (response.status == 200 ) {
                    if (response.error) {
                        swal.fire('Fail', response.message, 'error');
                    } else {
                        $('#td_agendar_del').removeClass('hidden')
                        $('#td_agendar_del button').data('id_agenda', response.data.id)
                        $('#td_agendar').addClass('hidden')
                        swal.fire('Exito', response.message, 'success');
                    }
                } else if (response.status == 400) {
                    err = '';
                    for (const [key, value] of Object.entries(response.error)) {
                        err +='<p>'+value+'</p>';
                      }
                    swal.fire('Fail', err, 'error');
                } else {
                    swal.fire('Fail', response.message, 'error');
                }
            })
            .fail((err) => {
                console.log(err);
            });
		}
	})
}


var_agendarcita.getCasosAgendados = (resp = Swal.isVisible()) => {
    let idOperador = $("#texto_agenda").data('id-operador');
    if(!resp){
        if(idOperador != null){
            $.ajax({
                url: base_url+'getAgendaOperadores/'+ idOperador,
                type: 'GET'
            })
            .done((response) => {
                $("#texto_agenda").html(response);
            })
            .fail((err) => {
                console.log("error: " + err);
            });
        }
    }

}

var_agendarcita.getSolicitudAjustes = (resp = Swal.isVisible()) => {
    let idOperador = $("#id_operador").val();
    if(!resp){
        if(idOperador != null){
            $.ajax({
                url: base_url+'getSolicitudAjustes/'+ idOperador,
                type: 'GET'
            })
            .done((response) => {
                $("#texto_sol_ajustes").html(response);
            })
            .fail((err) => {
                console.log("error: " + err);
            });
        }
    }
}

var_agendarcita.showSolicitudAjustes = (elem) => {
    var id_solicitud = $(elem).data('id_solicitud');
    var id = $(elem).data('id');
    var data = {
        id: id,
        recibido : 1
    };
    var base_url = $("input#base_url").val() + "atencion_cliente/updateSolajustes";
    $.ajax({
        type: "POST",
        url: base_url,
        data: data,
        success: function (response) {
            consultar_solicitud(id_solicitud);
        }
    });
}

var_agendarcita.showSolicitud = (evn) => {
    var id_solicitud = $(evn).data('id_solicitud');
    // $('#box_'+id_solicitud+' .box').append( var_agendarcita.loading.loader() );
    consultar_solicitud(id_solicitud);
}
var_agendarcita.delSolicitud = (evn) => {
    let idAgenda = $(evn).data('id_agenda');

    swal.fire({
		title: "¿Esta seguro?",
		text: "¿Está seguro de eliminar el caso agendado?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Si, Eliminar"
	}).then((result) => {
		if (result.value) {
            $.ajax({
                url: base_url+'atencion_cliente/Gestion/deleteAgendaOperador',
                type: 'POST',
                dataType: 'json',
                data: {'id':idAgenda}
            })
            .done((response) => {
                if (response['success']) {
                    if (typeof $("#client").data('number_doc') === 'undefined' ){
                        $(evn).parent().parent().remove();
                    } else {
                        $('#td_agendar_del').addClass('hidden')
                        $('#td_agendar').removeClass('hidden')
                    }
                    swal.fire('Exito', "Se elimino correctamente.", 'success');
                    var_agendarcita.getCasosAgendados(false);
                    var_agendarcita.getSolicitudAjustes(false);
                } else {
                    swal.fire('Fail', 'Error al eliminar.', 'error');
                }
            })
            .fail((jqXHR, textStatus, errorThrown) => {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
                console.log("error");
            });
		}
	})        
}

var_agendarcita.loading = {
    init () {
        return false 
    },
    loader () {
        return '<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>'
    }
}

function send_sms(id_solicitud) {
    // event.preventDefault();
    let base_url = $("#base_url").val();
    var send = false;
    $.ajax({
        url: base_url + 'api/enviar_sms/codigo_validacion/' + id_solicitud,
        type: 'GET',
        "async": false,
        "beforeSend": function (xhr) { $("#btn_send_sms").hide(); }
    })
        .done(function (response) {
            if (response.status.ok) {
                send = true;
            } else {
                send = false;
            }
        })
        .fail(function () {
            console.log("error");
        });

    return send;
}


function send_ivr(id_solicitud) {
    // event.preventDefault();
    let base_url = $("#base_url").val();
    var send = -1;
    $.ajax({
        url: base_url + 'api/enviar_ivr/codigo_validacion/' + id_solicitud,
        type: 'GET',
        "async": false,
        "beforeSend": function (xhr) { $("#btn_send_ivr").hide(); }
    })
        .done(function (response) {
            send = response.sms;
        })
        .fail(function () {
            console.log("error");
        });

    return send;
}

function send_email_validation(id_solicitud) {
    // event.preventDefault();
    let base_url = $("#base_url").val();
    var send = false;
    $.ajax({
        url: base_url + 'api/enviar_email/codigo_validacion/' + id_solicitud,
        type: 'GET',
        "async": false,
        "beforeSend": function (xhr) { $("#btn_send_email").hide(); }
    })
        .done(function (response) {
            if (response.status.ok) {
                send = true;
            } else {
                send = false;
            }
        })
        .fail(function () {
            console.log("error");
        });

    return send;
}

function edit_solicitud_bank(id_solicitud, data) {
    let base_url = $("#base_url").val();
    data.id_solicitud = id_solicitud;
    $.ajax({
        url: base_url + 'api/solicitud/banco/actualizar',
        data: data,
        type: 'POST',
    })
        .done(function (response) {
            //console.log("done");
        })
        .fail(function () {
            console.log("error");
        })
        .always(function () {
            console.log("complete");
        })
}

function val_telefono_client(id_solicitud) {
    let base_url = $("#base_url").val();
    var send = false;
    $.ajax({
        url: base_url + 'api/solicitud/validar_telefono/' + id_solicitud,
        type: 'GET',
    })
        .done(function (response) {
            if (response.status.ok) {
                let comment = '[VALIDACION TELEFONO]';
                let typeContact = 8;
                let idOperator = $("#id_operador").val();
                saveTrack(comment, typeContact, id_solicitud, idOperator);
                Swal.fire({
                    title: "¡Perfecto!",
                    text: response.message,
                    icon: 'success'
                });
                $('#btnValTelefono').addClass('hide');
                $('#dato_telefono').removeClass('bg-danger');
                $('#dato_telefono').addClass('bg-success');
                $('#icon_tlf').removeClass('fa fa-times-circle');
                $('#icon_tlf').css('color', 'green');
                $('#icon_tlf').addClass('fa fa-check');

            } else {
                Swal.fire({
                    title: "Ups!",
                    text: response.message,
                    icon: 'error'
                });
            }
        })
        .fail(function () {
            console.log("error");
        });

    return send;
}

function edit_solicitud_data_client(id_solicitud, data) {
    var send = false;
    let base_url = $("#base_url").val();
    data.id_solicitud = id_solicitud;
    $.ajax({
        url: base_url + 'api/solicitud/actualizar/cliente',
        data: data,
        type: 'POST',
        "async": false,
    })
        .done(function (response) {
            if (response.status.ok) {
                send = response;
            } else {
                send = false;
            }
        })
        .fail(function () {
            console.log("error");
        })
        .always(function () {
            console.log("complete");
        })

    return send;
}
//solicitudes Pendientes
function getSolicitudesPendientes() {
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/ApiSolicitud/getSolicitudesPendientes',
        type: 'GET',
        dataType: 'json',
    })
        .done(function (response) {
            if (response.status.ok) {
                $("#solicitudPendientes .btnSP").remove();
                $("#solicitudPendientes .parrafoSP").remove();
                let cadena = "<p class='parrafoSP'><b>Solicitudes pendiente por enviar pagare</b></p>";
                response.solicitudes.forEach(function (id_solicitud) {
                    let id_solicitud_val = Object.values(id_solicitud);
                    cadena += '<button class="btnSP btn btn-warning" style="margin-right:0.5em;" onclick="consultar_solicitud(' + id_solicitud_val + ')">' + id_solicitud_val + '</button>'
                });
                $("#solicitudPendientes").html(cadena);
            }
        })
        .fail(function (response) {
            //console.log("error");
        })
        .always(function (response) {
            //console.log("complete");
        });
}

function send_sms_desembolso(id_usuario) {
    // event.preventDefault();
    let base_url = $("#base_url").val();
    let data = { 'id_usuario': id_usuario };
    var send = false;
    $.ajax({
        url: base_url + 'EnviarSms/EnviarSms',
        type: 'POST',
        data: data,
    })
        .done(function (response) {
            if (response.status == 200) {
                send = true;
            } else {
                send = false;
            }
        })
        .fail(function () {
            console.log("error");
        });

    return send;
}
var toastr;
if (toastr) {
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-center",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
}

function reenvioValidarCuenta() {
    //Guardar lo que esta solicitud_datos_bacarios a solicitud_datos_bacarios_intentos    
    var id_solicitud = $("#id_solicitud").val();
    var numero_cuenta = $("#nro_cuenta_original").val();
    var id_banco = $("#client_bank").val();
    var id_tipo_cuenta = $("#client_account").val();
    var id_operador = $("#id_operador").val();
    var numero_cuenta_ant = $("#cuenta_antigua").val();
    var banco_ant = $("#banco_antiguo").val();
    var tipo_cuenta_ant = $("#tipo_cuenta_antigua").val();
    var nombre_operador = $("#operador_nombre").val();
    var nombre_banco = $("#client_bank option:selected").html();
    var nombre_tipo_cuenta = $("#client_account option:selected").html();
    var buro = $("#buro").val();

    if (!numero_cuenta || !id_banco || !id_tipo_cuenta) {
        toastr["warning"]("Debe ingresar un número de cuenta, banco y tipo de cuenta", "ACTUALIZACION DE CUENTA");
    } else {
        var base_url = $("#base_url").val();
        var data = {
            "id_solicitud": id_solicitud,
            "numero_cuenta": numero_cuenta,
            "id_banco": id_banco,
            "id_tipo_cuenta": id_tipo_cuenta,
            "numero_cuenta_ant": numero_cuenta_ant,
            "banco_ant": banco_ant,
            "tipo_cuenta_ant": tipo_cuenta_ant,
            "id_operador": id_operador,
            "nombre_operador": nombre_operador,
            "nombre_banco": nombre_banco,
            "nombre_tipo_cuenta": nombre_tipo_cuenta,
            "buro": buro
        }
        var base_url = $("input#base_url").val() + "gestion/Galery/actualizarNumeroCuenta";
        $.ajax({
            type: "POST",
            url: base_url,
            data: data,
            dataType: 'json',
            success: function (response) {
                //actualizar espacio de trackeo
                //addElemTimeLine(response.track);
                $('#banco_antiguo').val(nombre_banco);
                $('#tipo_cuenta_antigua').val(nombre_tipo_cuenta);
                $('#cuenta_antigua').val(numero_cuenta);
                if (response.update_cbu == "APROBADO") {
                    toastr["success"](response.update_cbu, "CUENTA ACTUALIZADA");
                    $('.fondo').removeClass("bg-danger").addClass("bg-success");
                    $('#icono_cuenta').removeClass("fa-times-circle").addClass("fa-times-check");
                    $('#icono_cuenta').css('color', 'green');

                } else {
                    toastr["error"](response.update_cbu, "CUENTA ACTUALIZADA");
                    $('.fondo').removeClass("bg-success").addClass("bg-danger");
                    $('#icono_cuenta').removeClass("fa-times-check").addClass("fa-times-circle");
                    $('#icono_cuenta').css('color', 'red');
                }
            }
        });
    }
}

//BOX DE REFERENCIAS

function agregar_referencia_familiar() {
    $("#tabla_agregar_familiar tbody").children('tr').eq(3).children('td').eq(1).html('<button class="btn btn-success pull-right" data-id-referencia = "" onclick="registrar_familiar(this)" id="registrar_familiar">REGISTRAR</button>');
    $("#tabla_agregar_familiar").show();
    $(".agregar_referencia_familiar").val("");
    $("#parentesco_agregar").val("");
    $("#tabla_vista").hide();
}


function agregar_referencia_personal(id, tipo) {
    $("#tabla_agregar_personal tbody").children('tr').eq(5).children('td').eq(1).html('<button class="btn btn-success pull-right" data-id-referencia = "" referencia="' + tipo + '" onclick="registrar_personal(this)" id="registrar_personal">REGISTRAR</button>');
    $("#tabla_agregar_personal").show();
    $(".agregar_referencia_personal").val("");
    $("#parentesco_personal").val("");
    $("#tabla_vista_personal").hide();
}

function ver_referencia_familiar(id_referencia) {
    var base_url = $("#base_url").val() + "ApiGalery/getReferenciaId";
    var data = {
        "id_referencia": id_referencia
    }
    //$("#id_ref_family").html('');
    $("#div_referencia").html('<input hidden id="id_ref_family" value="' + id_referencia + '">');
    $.ajax({
        type: "POST",
        url: base_url,
        data: data,
        dataType: 'json',
        success: function (response) {
            // console.logconsole.log(response);
            $("#tabla_agregar_familiar").hide();
            $("#tabla_vista").show();
            $("#tabla_vista tbody").children('tr').eq(0).children('td').eq(1).html(response[0]['referencias']['nombres_apellidos']);
            $("#tabla_vista tbody").children('tr').eq(1).children('td').eq(1).html(response[0]['referencias']['telefono']);
            $("#tabla_vista tbody").children('tr').eq(2).children('td').eq(1).html(response[0]['referencias']['Nombre_Parentesco']);
            $("#tabla_vista tbody").children('tr').eq(3).children('td').eq(1).html(response[0]['referencias']['verificacion']);
            if (response[0]['referencias']['verificacion'] != null) {
                $("#tabla_vista tbody ").children('tr').eq(4).children('td').eq(1).children('select').val(response[0]['referencias']['verificacion']);
            } else {
                $("#tabla_vista tbody ").children('tr').eq(4).children('td').eq(1).children('select').val("");
            }
        }
    });
}

function ver_referencia_personal(id_referencia) {

    var base_url = $("#base_url").val() + "ApiGalery/getReferenciaId";
    var data = {
        "id_referencia": id_referencia,
        "situacion": $("#situacion_laboral").val()
    }

    //$("#id_ref_family").html('');
    $("#div_referencia_personal").html('<input hidden id="id_ref_personal" value="' + id_referencia + '">');
    $.ajax({
        type: "POST",
        url: base_url,
        data: data,
        dataType: 'json',
        success: function (response) {

            $("#tabla_agregar_personal").hide();
            $("#tabla_vista_personal").show();

            if (typeof (response[0]['situacion']) != "undefined" && response[0]['situacion'] == '3') {
                //independientes
                $("#tabla_vista_personal tbody").children('tr').eq(0).children('td').eq(1).html(response[0]['referencias']['actividad']);
                $("#tabla_vista_personal tbody").children('tr').eq(1).children('td').eq(1).html(response[0]['referencias']['actividad_direccion']);
                $("#tabla_vista_personal tbody").children('tr').eq(2).children('td').eq(1).html(response[0]['referencias']['facebook']);
                $("#tabla_vista_personal tbody").children('tr').eq(3).children('td').eq(1).html(response[0]['referencias']['instagram']);
                $("#tabla_vista_personal tbody").children('tr').eq(4).children('td').eq(1).html(response[0]['referencias']['tipo_empresa']);
                $("#tabla_vista_personal tbody").children('tr').eq(5).children('td').eq(1).html(response[0]['referencias']['tipo_contrato']);
            } else {
                $("#tabla_vista_personal tbody").children('tr').eq(0).children('td').eq(1).html(response[0]['referencias']['nombres_apellidos']);
                $("#tabla_vista_personal tbody").children('tr').eq(1).children('td').eq(1).html(response[0]['referencias']['telefono']);
                //Si es personal mostrar parentesco, sino cargo, direccion y barrio
                if (response[0]['referencias']['Nombre_Parentesco'] == null) {
                    $("#tabla_vista_personal tbody").children('tr').eq(2).children('td').eq(1).html(response[0]['referencias']['empresa_cargo']);
                    $("#tabla_vista_personal tbody").children('tr').eq(3).children('td').eq(1).html(response[0]['referencias']['empresa_direccion']);
                    $("#tabla_vista_personal tbody").children('tr').eq(4).children('td').eq(1).html(response[0]['referencias']['empresa_barrio']);
                    $("#tabla_vista_personal tbody").children('tr').eq(5).children('td').eq(1).html(response[0]['referencias']['verificacion']);
                } else {
                    $("#tabla_vista_personal tbody").children('tr').eq(2).children('td').eq(1).html(response[0]['referencias']['Nombre_Parentesco']);
                    $("#tabla_vista_personal tbody").children('tr').eq(3).children('td').eq(1).html(response[0]['referencias']['verificacion']);
                }
                if (response[0]['referencias']['verificacion'] != null) {
                    $("#tabla_vista_personal tbody ").children('tr').eq(4).children('td').eq(1).children('select').val(response[0]['referencias']['verificacion']);
                } else {
                    $("#tabla_vista_personal tbody ").children('tr').eq(4).children('td').eq(1).children('select').val("");
                }
            }
        }
    });
}

function editar_referencia_personal(elem) {
    var id_referencia = $(elem).data('id-referencia');
    var index = $(elem).parent().parent().index();
    var base_url = $("#base_url").val() + "ApiGalery/getReferenciaId";
    var data = {
        "id_referencia": id_referencia
    }
    $.ajax({
        type: "POST",
        url: base_url,
        data: data,
        dataType: 'json',
        success: function (response) {
            //console.log(response);
            $("#tabla_vista_personal").hide();
            $("#tabla_agregar_personal").show();
            var select = '<select class="form-control col-md-12" id="parentesco_personal" name="parentesco">';
            for (var i = 0; i < response[0]['parentesco'].length; i++) {
                if (response[0]['parentesco'][i].id_parentesco >= 5) {
                    if (response[0]['referencias']['id_parentesco'] == response[0]['parentesco'][i].id_parentesco) {
                        select += '<option selected value="' + response[0]['parentesco'][i].id_parentesco + '">' + response[0]['parentesco'][i].Nombre_Parentesco + '</option>';
                    } else {
                        select += '<option value="' + response[0]['parentesco'][i].id_parentesco + '">' + response[0]['parentesco'][i].Nombre_Parentesco + '</option>';
                    }
                }
            }
            select += '</select>';
            $("#tabla_agregar_personal tbody").children('tr').eq(0).children('td').eq(1).html('<input type="text" id="nombre_apellido_personal" class="agregar_referencia_personal" autocomplete="off" value="' + response[0]['referencias']['nombres_apellidos'] + '">');
						nombre_apellido_original = response[0]['referencias']['nombres_apellidos'];
						telefono_original = response[0]['referencias']['telefono'];
            $("#tabla_agregar_personal tbody").children('tr').eq(1).children('td').eq(1).html('<input type="text" id="telefono_personal" onkeypress="ValidarNumeros(event)" class="agregar_referencia_personal" value="' + response[0]['referencias']['telefono'] + '" autocomplete="off">');
            if (response[0]['laboral'] == "1" || response[0]['laboral'] == "7") {
                $("#tabla_agregar_personal tbody").children('tr').eq(2).children('td').eq(1).html('<input type="text" id="empresa_cargo1" class="agregar_referencia_personal" value="' + response[0]['referencias']['empresa_cargo'] + '" autocomplete="off">');
                $("#tabla_agregar_personal tbody").children('tr').eq(3).children('td').eq(1).html('<input type="text" id="empresa_direccion" class="agregar_referencia_personal" value="' + response[0]['referencias']['empresa_direccion'] + '" autocomplete="off">');
                $("#tabla_agregar_personal tbody").children('tr').eq(4).children('td').eq(1).html('<input type="text" id="empresa_barrio" class="agregar_referencia_personal" value="' + response[0]['referencias']['empresa_barrio'] + '" autocomplete="off">');
                $("#tabla_agregar_personal tbody").children('tr').eq(5).children('td').eq(1).html('<button class="btn btn-info pull-right" data-index-ref=' + index + ' data-id-referencia=' + id_referencia + '  onclick="registrar_personal(this)" id="actualizar_personal">ACTUALIZAR</button>');
            } else {
                $("#tabla_agregar_personal tbody").children('tr').eq(2).children('td').eq(1).html(select);
                $("#tabla_agregar_personal tbody").children('tr').eq(3).children('td').eq(1).html('<button class="btn btn-info pull-right" data-index-ref=' + index + ' data-id-referencia=' + id_referencia + '  onclick="registrar_personal(this)" id="actualizar_personal">ACTUALIZAR</button>');
            }
        }
    });
}

function editar_referencia_familiar(elem) {
    var id_referencia = $(elem).data('id-referencia');
    var index = $(elem).parent().parent().index();
    var base_url = $("#base_url").val() + "ApiGalery/getReferenciaId";
    var data = {
        "id_referencia": id_referencia
    }
    $.ajax({
        type: "POST",
        url: base_url,
        data: data,
        dataType: 'json',
        success: function (response) {
            //console.log(response);
            $("#tabla_vista").hide();
            $("#tabla_agregar_familiar").show();
            var select = '<select class="form-control col-md-12" id="parentesco_agregar" name="parentesco">';
            for (var i = 0; i < response[0]['parentesco'].length; i++) {
                if (response[0]['parentesco'][i].id_parentesco >= 1 && response[0]['parentesco'][i].id_parentesco <= 4) {
                    if (response[0]['referencias']['id_parentesco'] == response[0]['parentesco'][i].id_parentesco) {
                        select += '<option selected value="' + response[0]['parentesco'][i].id_parentesco + '">' + response[0]['parentesco'][i].Nombre_Parentesco + '</option>';
                    } else {
                        select += '<option value="' + response[0]['parentesco'][i].id_parentesco + '">' + response[0]['parentesco'][i].Nombre_Parentesco + '</option>';
                    }
                }
            }
            select += '</select>';
						id_parentezco_original = response[0]['referencias']['id_parentesco'];
            nombre_apellido_familiar_original = response[0]['referencias']['nombres_apellidos'];
            $("#tabla_agregar_familiar tbody").children('tr').eq(0).children('td').eq(1).html('<input type="text" id="nombre_apellido_agregar" class="agregar_referencia_familiar" autocomplete="off" value="' + response[0]['referencias']['nombres_apellidos'] + '">');
						telefono_familiar_original = response[0]['referencias']['telefono'];
            $("#tabla_agregar_familiar tbody").children('tr').eq(1).children('td').eq(1).html('<input type="text" id="telefono_agregar" onkeypress="ValidarNumeros(event)" class="agregar_referencia_familiar" value="' + response[0]['referencias']['telefono'] + '" autocomplete="off">');
            $("#tabla_agregar_familiar tbody").children('tr').eq(2).children('td').eq(1).html(select);
            $("#tabla_agregar_familiar tbody").children('tr').eq(3).children('td').eq(1).html('<button class="btn btn-info pull-right" data-index-ref=' + index + ' data-id-referencia=' + id_referencia + '  onclick="registrar_familiar(this)" id="actualizar_familiar">ACTUALIZAR</button>');
        }
    });
}

function registrar_familiar(elem) {
    var id_referencia = $(elem).data('id-referencia');
    var index_lista_referencia = $(elem).data('index-ref');
    var nombre_apellido = $("#nombre_apellido_agregar").val();
    var telefono = $("#telefono_agregar").val();
    var parentesco = $("#parentesco_agregar :selected").val();
    var id_solicitud = $("#id_solicitud").val();
    //Si es edit traigo los datos
    if (id_referencia != "") {
        var base_url = $("#base_url").val() + "ApiGalery/editarFamiliar";
    } else {
        var base_url = $("#base_url").val() + "ApiGalery/registrarFamiliar";
    }
    if (nombre_apellido == "" || telefono == "" || parentesco == "") {
        toastr["warning"]("Por favor inserte todos los datos de referencia", "REFERENCIA FAMILIAR");
    } else {
        var data = {
            "nombre_apellido": nombre_apellido,
            "telefono": telefono,
            "parentesco": parentesco,
            "id_solicitud": id_solicitud,
            "id_referencia": id_referencia
        }
        $.ajax({
            type: "POST",
            url: base_url,
            data: data,
            dataType: 'json',
            success: function (response) {
							if (response.success) {
								var dataAgendaFamiliar = null
								if (telefono_familiar_original == "") {
									//nuevo
									var url_agenda_telefonica = $("#base_url").val() + "ApiGalery/saveTelefonoReferenciaFamiliar";
									dataAgendaFamiliar = {
										"documento": $("#client").data("number_doc"),
										"telefono": telefono,
										"tipo": 'MOVIL',
										"contacto": nombre_apellido,
										"estado": 1,
										"idParentezco": parentesco,
										"llamada": 1,
										"sms": 1,
									}
								} else if (telefono_familiar_original != telefono || nombre_apellido_familiar_original != nombre_apellido || id_parentezco_original != parentesco) {
									//update
									var url_agenda_telefonica = $("#base_url").val() + "ApiGalery/updateTelefonoReferenciaFamiliar";
									dataAgendaFamiliar = {
										"documento": $("#client").data("number_doc"),
										"telefono": telefono,
										"contacto": nombre_apellido,
										"telefonoOriginal": telefono_familiar_original,
										"idParentezco": parentesco,
									}
								}

								if (dataAgendaFamiliar != null) {
									$.ajax({
										type: "POST",
										url: url_agenda_telefonica,
										data: dataAgendaFamiliar,
										success: function (response) {
											toastr["success"]("Se guardó el telefono en la agenda telefonica", "REFERENCIA FAMILIAR");
										},
										error: function (xhr, ajaxOptions, thrownError) {
											toastr["error"]("Se produjo el siguiente error al guardar el telefono en la agenda telefonica:" + thrownError, "REFERENCIA FAMILIAR");
										}
									});
								}
                	
                    toastr["success"]("Se guardó la Referencia", "REFERENCIA FAMILIAR");
                    $("#tabla_agregar_familiar").hide();
                    $("#tabla_vista").show();
                    if (response.data.insert) {
                        $new_ref = "<tr>";
                        $new_ref += "<td class='analisis_col_izq' style='text-align: left;'><strong>* " + data.nombre_apellido + "</strong></td>";
                        $new_ref += '<td class="analisis_col_der">';
                        $new_ref += '<i style="float:right;font-size: 20px;color: cornflowerblue; margin-left: 6px;" onclick="ver_referencia_familiar(' + response.data.id_referencia + ')" class="verif fa fa-eye ver-ref"></i>';
                        $new_ref += '<i style="float:right;font-size: 20px;color: blue" data-id-referencia="' + response.data.id_referencia + '" onclick="editar_referencia_familiar(this)" class="verif fa fa-edit"></i>';
                        $new_ref += '</td>';
                        $new_ref += "</tr>";
                        $("#tabla_lista_referencias > tbody:last-child").append($new_ref);
                    } else if (response.data.update) {
                        //console.log(index_lista_referencia);
                        // console.log(data.nombre_apellido);
                        $("#tabla_lista_referencias tbody").children('tr').eq(index_lista_referencia).children('td').eq(0).html("<strong>* " + data.nombre_apellido + "<strong>");
                    }

                } else {
                    toastr["error"]("No se guardo la referencia familiar", "REFERENCIA FAMILIAR");
                }

            }
        });
    }
}

function registrar_personal(elem) {
    var id_referencia = $(elem).data('id-referencia');
    var index_lista_referencia = $(elem).data('index-ref');
    var nombre_apellido = $("#nombre_apellido_personal").val();
    var telefono = $("#telefono_personal").val();
    var parentesco = $("#parentesco_personal :selected").val();
    var empresa_cargo = $("#empresa_cargo1").val();
    var empresa_direccion = $("#empresa_direccion").val();
    var empresa_barrio = $("#empresa_barrio").val();
    var id_solicitud = $("#id_solicitud").val();
    var tipo_referencia = $("#registrar_personal").attr('referencia');

    // Laboral independiente
    var actividad = $("#actividad").val();
    var actividad_direccion = $("#actividad_direccion").val();
    var facebook = $("#facebook").val();
    var instagram = $("#instagram").val();
    var situacion = 0;

    //Si es edit traigo los datos
    if (id_referencia != "") {
        var base_url = $("#base_url").val() + "ApiGalery/editarFamiliar";
    } else {
        var base_url = $("#base_url").val() + "ApiGalery/registrarFamiliar";
    }
    if (tipo_referencia == "personal") {
        if (nombre_apellido == "" || telefono == "" || parentesco == "") {
            toastr["warning"]("Por favor inserte todos los datos de referencia", "REFERENCIA PERSONAL");
            return false;
        }
    } else if (tipo_referencia == "laboral") {
        if (nombre_apellido == "" || telefono == "" || empresa_cargo == "" || empresa_direccion == "" || empresa_barrio == "") {
            toastr["warning"]("Por favor inserte todos los datos de referencia", "REFERENCIA PERSONAL");
            return false;
        }
    } else if (tipo_referencia == "laboralI") {
        situacion = 3;
        if (actividad == "" || actividad_direccion == "") {
            toastr["warning"]("Por favor inserte todos los datos de referencia", "REFERENCIA PERSONAL");
            return false;
        }
    }
    var data = {
        "nombre_apellido": nombre_apellido,
        "telefono": telefono,
        "parentesco": parentesco,
        "id_solicitud": id_solicitud,
        "empresa_cargo": empresa_cargo,
        "empresa_direccion": empresa_direccion,
        "empresa_barrio": empresa_barrio,
        "id_referencia": id_referencia,
        "actividad": actividad,
        "actividad_direccion": actividad_direccion,
        "facebook": facebook,
        "instagram": instagram,
        "situacion": situacion
    }

    $.ajax({
        type: "POST",
        url: base_url,
        data: data,
        dataType: 'json',
        success: function (response) {
					var dataAgenda = null
        	if (telefono_original == "") {
        		//nuevo
						var url_agenda_telefonica = $("#base_url").val() + "ApiGalery/saveTelefonoReferenciaLaboral";
						dataAgenda = {
							"documento": $("#client").data("number_doc"),
							"telefono": telefono,
							"tipo": 'MOVIL',
							"contacto": nombre_apellido,
							"estado": 1,
							"idParentezco": 0,
							"llamada": 1,
							"sms": 1,
						}
					} else if (telefono_original != telefono || nombre_apellido_original != nombre_apellido) {
        		//update
						var url_agenda_telefonica = $("#base_url").val() + "ApiGalery/updateTelefonoReferenciaLaboral";
						dataAgenda = {
							"documento": $("#client").data("number_doc"),
							"telefono": telefono,
							"contacto": nombre_apellido,
							"telefonoOriginal": telefono_original
						}
					}

        	if (dataAgenda != null) {
						$.ajax({
							type: "POST",
							url: url_agenda_telefonica,
							data: dataAgenda,
							success: function (response) {
								toastr["success"]("Se guardó el telefono en la agenda telefonica", "REFERENCIA PERSONAL");
							},
							error: function (xhr, ajaxOptions, thrownError) {
								toastr["error"]("Se produjo el siguiente error al guardar el telefono en la agenda telefonica:" + thrownError, "REFERENCIA PERSONAL");
							}
						});
					}
					
            if (response.success) {
                toastr["success"]("Se guardó la Referencia", "REFERENCIA PERSONAL");
                $("#tabla_agregar_personal").hide();
                $("#tabla_vista_personal").show();
                if (response.data.insert) {
                    $new_ref = "<tr>";
                    $new_ref += "<td class='analisis_col_izq' style='text-align: left;'><strong>* " + data.nombre_apellido + "</strong></td>";
                    $new_ref += '<td class="analisis_col_der">';
                    $new_ref += '<i style="float:right;font-size: 20px;color: cornflowerblue; margin-left: 6px;" onclick="ver_referencia_personal(' + response.data.id_referencia + ')" class="verif fa fa-eye ver-ref"></i>';
                    if (situacion != 3)
                        $new_ref += '<i style="float:right;font-size: 20px;color: blue" data-id-referencia="' + response.data.id_referencia + '" onclick="editar_referencia_personal(this)" class="verif fa fa-edit"></i>';
                    $new_ref += '</td>';
                    $new_ref += "</tr>";
                    $("#tabla_lista_referencias_personal > tbody:last-child").append($new_ref);
                } else if (response.data.update) {
                    //console.log(index_lista_referencia);
                    // console.log(data.nombre_apellido);
                    $("#tabla_lista_referencias_personal tbody").children('tr').eq(index_lista_referencia).children('td').eq(0).html("<strong>* " + data.nombre_apellido + "<strong>");
                }

            } else {
                toastr["error"]("No se guardo la referencia familiar", "REFERENCIA PERSONAL");
            }

        }
    });
}

function verificar_familiar() {
    var verificacion = $("#familiar").val();
    var id_ref = $("#id_ref_family").val();
    var id_solicitud = $("#id_solicitud").val();
    var tipo = "Referencia Familiar"
    var id_operador = $("#id_operador").val();
    var nombre_operador = $("#operador_nombre").val();
    var referencia_tipo = $("#referencia_tipo").val();
    if (verificacion == "") {
        toastr["warning"]("Por favor seleccionar un valor de verificacion", "VERIFICAR");
    } else {
        var base_url = $("#base_url").val() + "gestion/Galery/verificar";
        var data = {
            "verificacion": verificacion,
            "id_ref": id_ref,
            "id_solicitud": id_solicitud,
            "tipo": tipo,
            "id_operador": id_operador,
            "nombre_operador": nombre_operador,
            "referencia_tipo": referencia_tipo
        }
        $.ajax({
            type: "POST",
            url: base_url,
            data: data,
            dataType: 'json',
            success: function (response) {
                //addElemTimeLine(response.track);
                toastr["success"](response.response, "VERIFICAR");
                $('#estado_familiar').html(verificacion);
            }
        });
    };
}

function verificar_personal() {
    var verificacion = $("#personal").val();
    var id_ref = $("#id_ref_personal").val();
    var id_solicitud = $("#id_solicitud").val();
    var tipo = "Referencia Familiar"
    var id_operador = $("#id_operador").val();
    var nombre_operador = $("#operador_nombre").val();
    var referencia_tipo = $("#referencia_tipo_personal").val();
    if (verificacion == "") {
        toastr["warning"]("Por favor seleccionar un valor de verificacion", "VERIFICAR");
    } else {
        var base_url = $("#base_url").val() + "gestion/Galery/verificar";
        var data = {
            "verificacion": verificacion,
            "id_ref": id_ref,
            "id_solicitud": id_solicitud,
            "tipo": tipo,
            "id_operador": id_operador,
            "nombre_operador": nombre_operador,
            "referencia_tipo": referencia_tipo
        }
        $.ajax({
            type: "POST",
            url: base_url,
            data: data,
            dataType: 'json',
            success: function (response) {
                //addElemTimeLine(response.track);
                toastr["success"](response.response, "VERIFICAR");
                $('#estado_personal').html(verificacion);
            }
        });
    };
}

function ValidarNumeros(event) {
    const reg = new RegExp(/^\d+$/, 'g');
    //const cadena = reg.test(event.key);
    if (!reg.test(event.key)) {
        event.preventDefault();
    }
}

function reenvioDatos() {
    var id_solicitud = $("#id_solicitud").val();
    var base_url = $("#base_url").val();
    var data = {
        "id_solicitud": id_solicitud
    }
    var base_url = $("input#base_url").val() + "gestion/Galery/actualizarDatos";
    $.ajax({
        type: "POST",
        url: base_url,
        data: data,
        dataType: 'json',
        success: function (response) {
            //actualizar toda la solicitud
            consultar_solicitud(id_solicitud);
        }
    });
}

function desbloquear_usuario(email) {
    var email = $("#email").text();
    var base_url = $("input#base_url").val() + 'api/desbloquear_usuario';
    var data = {
        "email": email
    }
    var send = false;
    $.ajax({
        url: base_url,
        type: 'POST',
        data: data,
        dataType: 'json',
        "async": false,
        "beforeSend": function (xhr) { $("#desbloquear").hide(); }
    })
        .done(function (response) {
            if (response.status.ok) {
                desbloquear = toastr["success"]("Se desbloqueo el usuario correctamente", "Desbloqueo de Email");
            }
            else {
                desbloquear = toastr["error"]("Error al desbloquear usuario", "Desbloqueo de Email");
            }
        })
        .fail(function () {
            console.log("error");
        });

    return desbloquear;
}

function listarxregistro(periodo, dia = null, aut_dep_ind = null ) {
    //var base_url = $("input#base_url").val() + 'api/solicitud/listar_x_registro?periodo=' + periodo;
    let linkA = document.querySelectorAll('#listarxregistroPorVisar');
    linkA.forEach(function (link) {
        // console.log(link);
        link.classList.add('disabled');
    });

    let end_point = $("input#base_url").val() + 'api/solicitud/listar_x_registro?periodo=' + periodo;
    if (dia != null) {

        end_point = $("input#base_url").val() + 'api/solicitud/listar_registro_por_visar?dia=' + dia + '&aut_dep_ind=' + aut_dep_ind;
    } 

    $("#section_search_solicitud #result").hide();
    $("#tabla_solicitudes").show();
    $("#solicitudPendientes").show();
    $("#tabla_desembolso").show();
    table = $('#tp_atencionCliente').DataTable();
    table.destroy();
    $('#tp_atencionCliente').DataTable({
        "processing": true,
        "language": spanish_lang,
        'iDisplayLength': 10,
        'paging': true,
        'info': true,
        "searching": true,
        "serverSide": true,
        "ajax":
            $.fn.dataTable.pipeline({
                "url": end_point,
                "type": "POST",
                "pages": 5
            }),
        'columns': [
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return '<a href="#" class="btn btn-xs btn-primary solicitud" title="Consultar" id="icono" id_solicitud="' + data.id + '" onclick="consultar_solicitud(' + data.id + ')"><i class="fa fa-cogs"></i></a>';
                }, "orderable": false,
            },
            { "data": "id" },
            { "data": "date_ultima_actividad" },
            { "data": "hours_ultima_actividad" },
            { "data": "documento" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return data.nombres + ' ' + data.apellidos;
                }

            },
            {
                "data": "nombre_situacion",
                // "render":function(data, type, row, meta){
                //     if(data.nombre_situacion != null)
                //     {
                //         return data.nombre_situacion.toUpperCase();

                //     }else{
                //         return '';
                //     }
                // }
            },
            { "data": "paso" },
            { "data": "tipo_solicitud" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.respuesta_analisis != null) {
                        if (data.respuesta_analisis.toUpperCase() == "APROBADO") {
                            return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                        } else if (data.respuesta_analisis.toUpperCase() == "RECHAZADO") {
                            return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                },
                "orderable": false
            },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.banco_resultado != null) {
                        if (data.banco_resultado.toUpperCase() == "ACEPTADA") {
                            return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                        } else if (data.banco_resultado.toUpperCase() == "RECHAZADA") {
                            return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                },
                "orderable": false
            },
            /*{"data":null,
                "render":function(data, type, row, meta)
                    {
                        if(data.resultado_ultimo_reto != null)
                        {
                            if(data.resultado_ultimo_reto.toUpperCase()=="CORRECTA")
                            {
                                return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                            }else if(data.resultado_ultimo_reto.toUpperCase()=="INCORRECTA")
                            {
                                return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                            }else{
                                return '';
                            }
                        }else{
                                return '';
                        }
                    },
                    "orderable": false
            },*/
            { "data": "estado" },
            { "data": "operador_nombre_pila" },
            { "data": "last_track", "orderable": false, }
        ],
        "columnDefs": [
            {
                "targets": 0,
                "orderable": false,
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 3%; text-align: center;');
                }

            },
            {
                "targets": 1,
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 7%; text-align: center;');
                }
            },
            {
                "targets": [1, 2, 3, 4],
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 7%;');
                }
            },
            {
                "targets": 5,
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'text-align: left; width: 10%;');
                }
            },
            {
                "targets": [7, 6, 8, 11, 12, 13],
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 7%;');
                }
            },
            {
                "targets": [9, 10],
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 1%;');
                    linkA.forEach(function (link) {
                        link.classList.remove('disabled');
                    });
                    $('#mostratTodasLasSolicitudes').removeClass('disabled');
                }
            }
        ]
    });
}

function enviar_pagare(id_solicitud, boton) {
    $.ajax({
        url: base_url + 'api/pagare/uanataca/crear/' + id_solicitud,
        type: 'POST',
        dataType: 'json',
        timeout: 120000, // 2 minutos
        data: { "id_solicitud": id_solicitud },
        beforeSend: function () {
            boton.prop('disabled', true);
            boton.html('ENVIANDO...');
        }
    })
        .done(function (response) {
            //console.log(response);
            if (response.status.ok) {
                boton.html('PAGARÉ ENVIADO');
                toastr["success"](response.message, "Pagaré:");
                //let id_solicitud = $("#id_solicitud").val();
                let id_operador = $("#box_botones_gestion #id_operador").val();
                let estado = $(this).data('reference');
                let type_contact = boton.data('type_gestion');
                let comment = '[PAGARÉ] ' + response.message;
                saveTrack(comment, type_contact, id_solicitud, id_operador);
            }
        })
        .fail(function (response) {
            boton.prop('disabled', false);
            boton.html('ENVIAR PAGARÉ');
            toastr["error"](response.error, "Pagaré:");
        })
        .always(function () {

        });
}

function reenviar_pagare(id_solicitud, boton) {
    $.ajax({
        url: base_url + 'api/pagare/uanataca/reenviar/' + id_solicitud,
        type: 'POST',
        dataType: 'json',
        timeout: 120000, // 2 minutos
        data: { "id_solicitud": id_solicitud, "reenviado": true },
        beforeSend: function () {
            boton.prop('disabled', true);
            boton.html('ENVIANDO...');
        }
    })
        .done(function (response) {
            //console.log(response);
            if (response.status.ok) {
                boton.html('PAGARÉ ENVIADO');
                toastr["success"](response.message, "Pagaré:");
                //let id_solicitud = $("#id_solicitud").val();
                let id_operador = $("#box_botones_gestion #id_operador").val();
                let estado = $(this).data('reference');
                let type_contact = boton.data('type_gestion');
                let comment = '[PAGARÉ] ' + response.message;
                saveTrack(comment, type_contact, id_solicitud, id_operador);
            }
        })
        .fail(function (response) {
            boton.prop('disabled', false);
            boton.html('ENVIAR PAGARÉ');
            toastr["error"](response.error, "Pagaré:");
        })
        .always(function () {

        });
}


function validarDesembolso(param1, param2) {
    $.ajax({
        url: base_url + 'api/solicitud/validarDesembolso',
        type: 'POST',
        dataType: 'json',
        data: { "param1": param1, "param2": param2 },
        beforeSend: function () {
            $("#validar-desembolso").prop('disabled', true);
            $("#validar-desembolso").html('ENVIANDO...');
        }
    })
        .done(function (response) {
            if (response.status.ok) {
                $("#validar-desembolso").remove();
                toastr["success"](response.message, "VALIDACION DE DESEMBOLSO: ");
                let type_contact = '150';
                let comment = '[VERIFICACION DE DESEMBOLSO] <br>La solicitud de verificacion ha sido enviada. <br>OPREADOR: ' + $("#id_operador").val() + ' - ' + $("#id_operador").data('user') + '<br> FECHA: ' + moment().format('DD-MM-YYYY');
                saveTrack(comment, type_contact, param1, param2);
            } else {
                $("#validar-desembolso").prop('disabled', false);
                $("#validar-desembolso").html('VALIDAR');
                toastr["error"](response.error, "VALIDACION DE DESEMBOLSO: ");
            }
        })
        .fail(function (response) {
            $("#validar-desembolso").prop('disabled', false);
            $("#validar-desembolso").html('VALIDAR');
            toastr["error"](response.error, "VALIDACION DE DESEMBOLSO: ");
        })
        .always(function () {

        });
}

btn_procesar_galery = (elem) => {
    
    id = $(elem).data('id-verify')
    Swal.fire({
        title: '¡Atención!',
        text: '¿Estas seguro de que quieres registrar ?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            observaciones = $('#observacion_valid_galery').val();
            $.ajax({
                type: "POST",
                url: base_url + "atencion_cliente/update_verifygalery",
                data: { "respuesta_identificacion": observaciones, "id": id },
                dataType: "json",
                success: function (response) {                    
                    let id_operador = $("section").find("#id_operador").val();
                    let id_solicitud = $("#id_solicitud").val();
                    let type_contact = 25;
                    let comment = '[COMENTARIO VIDEO] <br>' + 
                        $('#observacion_valid_galery').val();
                    saveTrack(comment, type_contact, id_solicitud, id_operador);
                    consultar_solicitud(id_solicitud);
                }
            });
        }
    });
}
var_videollamada = []
var_videollamada.toastr = null
var_videollamada.callWaiting = []

var channelvideollamada = null;
var_videollamada.init = () => {
    var_videollamada.toastr = toastr
    var_videollamada.toastr.options = {
        "positionClass": "toast-top-right test",        
        "toastClass": 'toast videollamada prueba',
        "containerId": 'toast-container',
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "0",
        "extendedTimeOut": "0",
        "closeOnHover": false
    };
    
    var_videollamada.toastr.options.onclick = (e) => {
	    $notificacion = $(e.currentTarget)
        var_videollamada.callWaiting[$notificacion.find('span#solicitud').data('idsolicitud')] = {
            status : true
        }
        consultar_solicitud($notificacion.find('span#solicitud').data('idsolicitud'));
    }
    
    channelvideollamada = pusherprivate.subscribe('private-encrypted-videollamadas-'+$('#id_operador').val());
    channels.push(channelvideollamada);
    
    channelvideollamada.bind('callWaiting', function(resp) {
        data = JSON.parse(resp);
        if ($("div.toast").length == 0)
            var_videollamada.toastr.error("<span id='solicitud' data-idsolicitud = "+data.id_solicitud+">"+data.nombreCliente+"<span>", "Llamada en espera " + data.id_solicitud);
        else {
            $("div.toast").each(function() {
                var idsolicitud = $(this).find('span#solicitud').data('idsolicitud');
                if (parseInt(idsolicitud) != parseInt(data.id_solicitud)) {
                    var_videollamada.toastr.error("<span id='solicitud' data-idsolicitud = "+data.id_solicitud+">"+data.nombreCliente+"<span>", "Llamada en espera " + data.id_solicitud);        
                }
            });
        }
    });
}


function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}


function casosPorVisar() {
    // desabilitar el boton con id btnPorVisar
    $("#btnPorVisar").prop('disabled', true);
    // Primero veo si existe la tabla o mensajeSinCasos y si existen las elimino.
    if ($("table[id='CamposTabla']")) {
        $("table[id='CamposTabla']").remove();
        $('#tituloCasosVisar').hide();
    }
    if ($("p[id='mensajeSinCasos']")) {
        $("p[id='mensajeSinCasos']").remove();
    }
    if ($("table[id='totalsTable']")) {
        $("table[id='totalsTable']").remove();
    }
    $('#porVisarTotal').show()
   
    $.ajax({
        type: "GET",
        url: $("input#base_url").val() + 'casosPorVisar',
        success: function (response) {
            if (response.ok == true) {

                $("#porVisarTableContenedor").show();
                let casosPorVisar = response.data;
                let totales = response.totalesVisar;

                casosPorVisar.forEach(casosDia => {

                    camposTabla(); 
                    columnaDia(casosDia);
                    columnasPorVisar(casosDia);
                    cantidadDeCasos(casosDia);
                });
                
                if(totales.total != 0){
                    totalesTabla(totales);
                    $('#tituloCasosVisar').show();
                }  
                $("#btnPorVisar").prop('disabled', false); 

            }
            if(response.data == '') {
                let opciones = '<p id="mensajeSinCasos" style="background-color: #f7f1fb!important;font-size: 14px;color: red;font-weight: 500;padding:1%; width: 100%;margin-top: 2%;margin-left:8%">SIN CASOS POR VISAR.</p>';
                $('#porVisarTable').html(opciones);
                $("#btnPorVisar").prop('disabled', false);
            }
            if(response.ok == false) {
                $("#btnPorVisar").prop('disabled', false); 
                Swal.fire({
                    title: 'Casos por visar.',
                    text: 'No se pudo obtener la infomación de casos pendietes de visar.',
                    icon: 'success',
                    type: 'error',
                    confirmButtonText: 'OK'
                })
                
            }
            
        }
    }); 
}

function columnaDia(casosDia) {
    let colDia = `<th id="colDia" colspan="3" style="background-color:#cde8ff; border: 1px solid #cbd3d6; text-align: center;border-bottom:none!important;" scope="colgroup">${casosDia.fecha}</th>`;
    $("#colspan").append(colDia);	
    
}

function columnasPorVisar(casosDia) {
    let columnas = `<th class="bordeBlanco" style="text-align: center;  border: 1px solid #fff;border-left: 1px solid #cbd3d6!important;">${casosDia.valores[0].columna}</th>
                    <th class="bordeBlanco" style="text-align: center;  border: 1px solid #fff;">${casosDia.valores[1].columna}</th>
                    <th class="bordeBlanco" style="text-align: center;  border: 1px solid #fff;border-right: 1px solid #cbd3d6!important;">${casosDia.valores[2].columna}</th>`;
    $("#columnasPorVisar").append(columnas);	
}
function cantidadDeCasos(casosDia) {
    let casos = `<td class="bordeGris" style="background-color: #f9f9f9; border: 1px solid #fff;text-align: center;border-left: 1px solid #cbd3d6!important;border-bottom: 1px solid #cbd3d6!important;"><a id="listarxregistroPorVisar" class="" href="#" onclick="listarxregistro('hoy','${casosDia.fecha_sin_format}','${casosDia.valores[0].columna}');">${(casosDia.valores[0].sumatoria == 0)? '-' : casosDia.valores[0].sumatoria}</a></td>
                <td class="bordeGris" style="background-color: #f9f9f9; border: 1px solid #fff;text-align: center;border-bottom: 1px solid #cbd3d6!important;"><a id="listarxregistroPorVisar" class="" href="#" onclick="listarxregistro('hoy','${casosDia.fecha_sin_format}','${casosDia.valores[1].columna}');">${(casosDia.valores[1].sumatoria == 0)? '-' : casosDia.valores[1].sumatoria}</a></td>
                <td class="bordeGris" style="background-color: #f9f9f9; border: 1px solid #fff;text-align: center;border-right: 1px solid #cbd3d6!important;border-bottom: 1px solid #cbd3d6!important;"><a id="listarxregistroPorVisar" class="" href="#" onclick="listarxregistro('hoy','${casosDia.fecha_sin_format}','${casosDia.valores[2].columna}');">${(casosDia.valores[2].sumatoria == 0)? '-' : casosDia.valores[2].sumatoria}</a></td>`;
    $("#cantidadDeCasos").append(casos);	
}


function camposTabla() {
    
    let dias = `<table id="CamposTabla"  style="display:table; width:100%">
                    <thead style="width:100%;background-color:#ddebf7; border: 1px solid #fff; ">
                        <tr id="colspan" style="width:100%;background-color:#ddebf7; border: 1px solid #fff; ">
                        </tr>    
                    
                        <tr id="columnasPorVisar" style="width:100%;background-color:#ddebf7;">
                        </tr>
                    </thead>
                    <tbody >
                        <tr id="cantidadDeCasos" style="background-color: #f9f9f9; border: 1px solid #fff;" style="display:table-cell;">
                        
                        </tr>
                    </tbody>
                </table>`;
    $("#porVisarTable").append(dias);

}
function totalesTabla(totales) 
{
    let totalesPendientes = `<table id="totalsTable" style="width:100%;display:table;">
                                <thead style="background-color:#f39c12; ">
                                    
                                    <tr style="background-color:#f39c12;">
                                        <th id="colDia" colspan="4" style="background-color:#f39c12; border: 1px solid #cbd3d6;border-bottom:none!important; text-align: center;" scope="colgroup">TOTALES</th>
                                    </tr>
                                    <tr style="width:100%;background-color:#f39c12" >
                                    
                                        <th class="col" style="width:25%!important;text-align: center; border: 0.5px solid #fff;border-left: 1px solid #cbd3d6!important;" scope="col">AUT</th>
                                        <th class="col" style="width:25%!important;text-align: center; border: 0.5px solid #fff;" scope="col">DEP</th>
                                        <th class="col" style="width:25%!important;text-align: center; border: 0.5px solid #fff;" scope="col">IND</th>
                                        <th class="col" style="width:25%!important;text-align: center; border: 0.5px solid #fff;border-right: 1px solid #cbd3d6!important;" scope="col">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td style="width:25%!important;background-color: #f9f9f9; border: 1px solid #fff;text-align: center;border-bottom: 1px solid #cbd3d6!important;border-left: 1px solid #cbd3d6!important;"><a id="listarxregistroPorVisar" href="#" class="" onclick="listarxregistro('hoy','total','AUT');">${(totales.autTotal == 0)? '-' : totales.autTotal}</a></td>
                                    <td style="width:25%!important;background-color: #f9f9f9; border: 1px solid #fff;text-align: center;border-bottom: 1px solid #cbd3d6!important;"><a id="listarxregistroPorVisar" href="#" class="" onclick="listarxregistro('hoy','total','DEP');">${(totales.depTotal == 0)? '-' : totales.depTotal}</a></td>
                                    <td style="width:25%!important;background-color: #f9f9f9; border: 1px solid #fff;text-align: center;border-bottom: 1px solid #cbd3d6!important;"><a id="listarxregistroPorVisar" href="#" class="" onclick="listarxregistro('hoy','total','IND');">${(totales.indTotal == 0)? '-': totales.indTotal}</a></td>
                                    <td style="width:25%!important;background-color: #f9f9f9; border: 1px solid #fff;text-align: center;border-right: 1px solid #cbd3d6!important;border-bottom: 1px solid #cbd3d6!important;"><a id="listarxregistroPorVisar" class="" href="#" onclick="listarxregistro('hoy','total','TOTAL');">${totales.total}</a></td>
                                    </tr>
                                </tbody>
                            </table>`;
    $("#porVisarTotalTable").append(totalesPendientes);
}



function transferenciaRechazada() {
    $("#section_search_solicitud #result").hide();
    $("#tabla_solicitudes").show();
    $("#solicitudPendientes").show();
    $("#tabla_desembolso").show();
    table = $('#tp_atencionCliente').DataTable();
    table.destroy();
    $('#tp_atencionCliente').DataTable({
        'iDisplayLength': 10,
        "responsive":true,
        //'dom': 'Bfrtip',
        "processing":true,
        "language": spanish_lang,
        'paging':true,
        'info':true,
        "searching": true,
        "ajax":
            $.fn.dataTable.pipeline({
                "url": base_url + 'api/solicitud/transferenciaRechazada',
                "method ": "POST"
            }),
        'columns': [
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return '<a href="#" class="btn btn-xs btn-primary solicitud" title="Consultar" id="icono" id_solicitud="' + data.id + '" onclick="consultar_solicitud(' + data.id + ',\'transRechazada\')"><i class="fa fa-cogs"></i></a>';
                }, "orderable": false,
            },
            { "data": "id" },
            { "data": "date_ultima_actividad" },
            { "data": "hours_ultima_actividad" },
            { "data": "documento" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return data.nombres + ' ' + data.apellidos;
                }

            },
            {
                "data": "nombre_situacion",
                // "render":function(data, type, row, meta){
                //     if(data.nombre_situacion != null)
                //     {
                //         return data.nombre_situacion.toUpperCase();

                //     }else{
                //         return '';
                //     }
                // }
            },
            { "data": "paso" },
            { "data": "tipo_solicitud" },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.respuesta_analisis != null) {
                        if (data.respuesta_analisis.toUpperCase() == "APROBADO") {
                            return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                        } else if (data.respuesta_analisis.toUpperCase() == "RECHAZADO") {
                            return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                },
                "orderable": false
            },
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    if (data.banco_resultado != null) {
                        if (data.banco_resultado.toUpperCase() == "ACEPTADA") {
                            return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                        } else if (data.banco_resultado.toUpperCase() == "RECHAZADA") {
                            return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                        } else {
                            return '';
                        }
                    } else {
                        return '';
                    }
                },
                "orderable": false
            },
            /*{"data":null,
                "render":function(data, type, row, meta)
                    {
                        if(data.resultado_ultimo_reto != null)
                        {
                            if(data.resultado_ultimo_reto.toUpperCase()=="CORRECTA")
                            {
                                return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                            }else if(data.resultado_ultimo_reto.toUpperCase()=="INCORRECTA")
                            {
                                return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                            }else{
                                return '';
                            }
                        }else{
                                return '';
                        }
                    },
                    "orderable": false
            },*/
            { "data": "estado" },
            { "data": "operador_nombre_pila" },
            { "data": "last_track", "orderable": false, }
        ],
        "columnDefs": [
            {
                "targets": 0,
                "orderable": false,
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 3%; text-align: center;');
                }

            },
            {
                "targets": 1,
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 7%; text-align: center;');
                }
            },
            {
                "targets": [1, 2, 3, 4],
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 7%;');
                }
            },
            {
                "targets": 5,
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'text-align: left; width: 10%;');
                }
            },
            {
                "targets": [7, 6, 8, 11, 12, 13],
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 7%;');
                }
            },
            {
                "targets": [9, 10],
                "createdCell": function (td, cellData, rowData, row, col) {
                    $(td).attr('style', 'width: 1%;');
                   
                }
            }
        ]
    });
}

function subirImagen() {
    var config = $('#new_doc')[0];
    let formData = new FormData(config);
    event.preventDefault();        
    $(this).find("input:file").each(function(index,elem){
        if($(elem).attr("name")!=="undefined")
        {
            formData.append($(elem).attr("name"), $(elem).val());
        }   
    });

    let base_url= $("#base_url").val();
    $.ajax({
        url: base_url+"api/galery/subir_imagen",
        type: "POST",
        dataType: "json",
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(response){
            if(response.status.ok)
            {
                addImageSlider(response.doc);
                    
            }else{
                    if(response.errors.image){
                    $("#field_files #new_doc #field_image").addClass('has-error');
                    $("#field_files #new_doc #field_image .help-block").text(response.errors.image).show();
            }
            if(response.errors.id_img_required){
                    $("#field_files #new_doc #field_id_img_required").addClass('has-error');
                    $("#field_files #new_doc #field_id_img_required .help-block").text(response.errors.id_img_required).show();
            }
            }
    }).fail(function(response) {
        //window.location.href = response.responseJSON.redirect;
    })
    .always(function() {
    });
}

function cargar_box_cargar_documento(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/Gestion/get_images_archivos/' + id_solicitud + '/documentos',
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".box_cargar_documento").html(response);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}

function cargar_box_slider_documentos(id_solicitud) {
    $.ajax({
        url: base_url + 'atencion_cliente/Gestion/get_images_documentos/' + id_solicitud + '/documentos',
        type: 'GET',
        dataType: 'html',
        data: {
            d : $("#client").data('number_doc')
        }
    })
        .done(function (response) {
            $(".box_slider_documentos").html(response);
        })
        .fail(function (response) {
            console.log('error');
        })
        .always(function () {

        });
}

function previewImg(patch, etiqueta) {
    var ruta = base_url + patch;
    //console.log(ruta);
    $('#imgTitle').empty();
    $('#imgDoc').empty();
    $('#imgTitle').text('Etiqueta: '+etiqueta);
    $('#imgDoc').prop('data',ruta);
    $('#imgDocumento').modal();

}

// Agrega un elemento a la galeria
function addImageSlider(image) {
    var item = document.getElementsByClassName(".item .active").length;
    var patch = image.uri.split('/public');
    patch = 'public/'+patch[1];
    if (item == 0) {
        $('.item').removeClass('active');
    }
    let date = new Date();
    html = '<div class="item active" style="margin-left:auto; margin-right:auto;">';
    html += '<div class="col-md-3"  style="position:absolute; width:243px; height:200px; top:96px"><a style="cursor:pointer" onclick="previewImg(\''+patch+'\',\''+image.descripcion+'\')" data-src="' + image.uri + '">Ver</a></div>';
    html += '<object type="application/pdf" data="' + image.uri +'" style="width: 100px; height: 150px;"> ERROR (no puede mostrarse el documento) </object>';
    html += '</div>';
    $(".carousel-inner").append(html);

}
    
