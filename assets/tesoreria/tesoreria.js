var timerDesembolso;
$(document).ready(function() {
    getCantImputacionesPendientes();
    cargarBancos();
    getCantValidarPendientes();

    /*** Volver a refrescar el Badge cada 5 Minutos ***/
    window.clearInterval();
    window.setInterval(function() {
        getCantValidarPendientes();
        cargarBancos();
    }, 300000);
    handlersModules();
    //Carga de modulo inicial de tesorería
    var moduloPrincipal = "tesoreria/Tesoreria/vistaPrestamos";
    initView(moduloPrincipal);
});

function handlersModules() {
    $("#buttonPrestamos").on("click", function(e) {
        var route = "tesoreria/Tesoreria/vistaPrestamos";
        initView(route);
    });

    $("#buttonDevolucion").on("click", function(e) {
        var route = "tesoreria/Tesoreria/vistaDevolucion";
        initView(route);
    });

    $("#buttonImputarPago").on("click", function(e) {
        var route = "tesoreria/Tesoreria/vistaImputarPago";
        initView(route);
    });

    $("#buttonImputarPagoArchivo").on("click", function(e) {
        var route = "tesoreria/Tesoreria/vistaImputarPagoEfecty";
        initView(route);
    });

    $("#buttonRespuestaBbva").on("click", function(e) {
        var route = "tesoreria/Tesoreria/vistaRespuestaBanco";
        initView(route);
    });

    $("#buttonRespuestaSantander").on("click", function(e) {
        var route = "tesoreria/Tesoreria/vistaRespuestaBancoSantanter";
        initView(route);
    });

    $("#buttonRespuestaBanColombia").on('click', function(e) {
        var route = "tesoreria/Tesoreria/vistaRespuestaBanColombia";
        initView(route);

    });
    $("#buttonRespuestaBancobogota").on('click', function(e) {
        var route = "tesoreria/Tesoreria/vistaRespuestaBancobogota";
        initView(route);

    });

    $("#buttonProcesarGasto").on("click", function(e) {
        var route = "tesoreria/Tesoreria/vistaRespuestaProcesarGasto";
        initView(route);
    });

    $("#buttonValidarDesembolsos").on("click", function() {
        validarDesembolso();
    });

    $("#buttonImputacionAutomatica").on("click", function() {
        var route = "tesoreria/Tesoreria/vistaImputacionAutomaticaBancolombia";
        initView(route);
    });

    $("#buttonImputacionRecaudo").on("click", function() {
        var route = "tesoreria/Tesoreria/vistaImputacionRecaudoBancolombia";
        initView(route);
    });

    $("#buttonGeneracionDebitoAutomatica").on("click", function() {
        var route = "tesoreria/Tesoreria/vistaDebitoAutomaticoBancolombia";
        initView(route);
    });

    $("#buttonLecturaEnvios").on("click", function() {
        var route = "tesoreria/envios/vistaDebitoAutomaticoBancolombiaRespuestaEnvios";
        initView(route);
    });

    $("#buttonGeneracionDebitos").on("click", function() {
        var route = "tesoreria/Tesoreria/vistaGeneracionDebitos";
        initView(route);
    });

    $("#buttonLecturaRCGA").on("click", function() {
        var route = "tesoreria/rcga/vistaDebitoAutomaticoBancolombiaRespuestaRcga";
        initView(route);
    });

    $("#buttonImputacion").on("click", function() {
        var route = "tesoreria/Imputacion/vistaImputacion";
        initView(route);
    });

    $("#buttonInformeEnvios").on("click", function() {
        var route = "tesoreria/envios/vistaDebitoAutomaticoBancolombiaInfomeEnvios";
        initView(route);
    });
}

function validarDesembolso() {
    var route = "tesoreria/Tesoreria/vistaValidarDesembolsos";
    initView(route);
}

function initView(route, options = []) {
    base_url = $("input#base_url").val() + route;

    $.ajax({
        url: base_url,
        type: "GET",
        dataType: "html",
        success: function(response) {
            $("#main").html(response);
        },
        error: function() {
            console.log("error");
        },
        beforeSend: function() {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#main").html(loading);
        }
    });
}
/*************************************************************************/
/*** la etiqueta ancla para validar el desembolso escogido de la tabla ***/
/*************************************************************************/
$('body').on('click', '#tbl_validar_desembolsos a[id="aValidar"]', function() {
    let base_url = $("#base_url").val();
    /*** Obtengo los valores de los atributos ***/
    let id_desembolso = $(this).attr('id_desembolso');
    let id_solicitud = $(this).attr('id_solicitud');
    let fecha_alta = $(this).attr('fecha_alta');
    let documento = $(this).attr('documento');
    let nombre_apellido = $(this).attr('nombre_apellido');
    let fecha_hora_solicitud = $(this).attr('fecha_hora_solicitud');
    let fecha_procesado = $(this).attr('fecha_procesado');
    let nombre_apellido_operador = $(this).attr('nombre_apellido_operador');
    let respuesta = $(this).attr('respuesta');
    let comprobante = $(this).attr('comprobante');

    $('#id_desembolso').text(id_desembolso);
    $('#id_solicitud').text(id_solicitud);
    $('#fecha_alta').text(fecha_alta);
    $('#documento').text(documento);
    $('#nombre_apellido').text(nombre_apellido);
    $('#fecha_hora_solicitud').text(fecha_hora_solicitud);
    $('#fecha_procesado').text(fecha_procesado);
    $('#operador').text(nombre_apellido_operador);

    if (respuesta && respuesta != "null") {
        arrRespuesta = respuesta.split('-');
        $('#selectRespuesta option[value="' + arrRespuesta[0] + '"]').prop("selected", true);
        $('#aEnvio').hide();
        $("#selectRespuesta").attr("disabled", true);
        $("#selectMotivo").val("");
        if (arrRespuesta.length > 1) {
            $('#selectMotivo option[value="' + arrRespuesta[1] + '"]').prop("selected", true);
        }
        if (comprobante && comprobante != "null") {
            $('#subirComprobante').hide();
            $('#urlComprobante').attr('href', comprobante);
            let arrNombreArchivo = comprobante.split('/');
            $('#urlComprobante').text(arrNombreArchivo[arrNombreArchivo.length - 1]);
            $('#mostarComprobante').show();
        } else {
            $('#noComprobante').show();
            $('#mostarComprobante').hide();
            $('#subirComprobante').hide();
        }
    } else {
        $("#selectRespuesta").attr("disabled", false);
        $("#selectRespuesta").val("");
        $("#selectMotivo").val("");
        $('#aEnvio').show();
        $('#noComprobante').hide();
        $('#subirComprobante').show();
        $('#mostarComprobante').hide();
    }

    switch ($(this).attr('pagado')) {
        case "0":
            $('#pagado').text("Enviado al banco");
            break;
        case "1":
            $('#pagado').text("Transferencia realizada");
            break;
        case "2":
            $('#pagado').text("Transferencia rechazada");
            break;
        case "3":
            $('#pagado').text("Reenviado a transferir");
            break;
        default:
            $('#pagado').text("Sin definir");
            break;
    }

    $('#origen_pago').text($(this).attr('origen_pago'));
    let arrRutaArchivo = $(this).attr('ruta_txt').split("/");
    archivo = arrRutaArchivo[arrRutaArchivo.length - 1];
    $('#ruta_txt').text(archivo);

    $('#modalValidarDesembolso').modal("show");
});
/*******************************************************************/
/*** Obtiene la cantidad de desembolsos pendientes para el Badge ***/
/*******************************************************************/
function getCantValidarPendientes() {
    let base_url = $("#base_url").val();

    $.ajax({
            type: "GET",
            url: base_url + 'api/ApiGastos/cantValidarPendientes',
        })
        .done(function(response) {
            if (response.status.ok) {
                $('#cantDesembolsoValidar').text(response.cantPendiente);
            }
        })
        .fail(function(xhr) {
            Swal.fire("Atencion!",
                `readyState: ${xhr.readyState}
				status: ${xhr.status}
				responseText: ${xhr.responseText}`,
                "error"
            );
        });
}
/********************************************************************/
/*** Función que realiza el Track de la validación del desembolso ***/
/********************************************************************/
function saveTrack(comment, typeContact, idSolicitude, idOperator) {
    let base_url = $("#base_url").val();
    $.ajax({
            url: base_url + 'api/track_gestion',
            type: 'POST',
            dataType: 'json',
            data: {
                'observaciones': comment,
                'id_tipo_gestion': typeContact,
                'id_solicitud': idSolicitude,
                'id_operador': idOperator
            }
        })
        .done(function(response) {
            if (response.status.ok) {

            }
        })
        .fail(function(xhr) {
            Swal.fire("Atencion!",
                `readyState: ${xhr.readyState}
				status: ${xhr.status}
				responseText: ${xhr.responseText}`,
                "error"
            );
        });
}
/**********************************************************/
/*** Validación en cada cambio del Select de Respuestas ***/
/**********************************************************/
$('body').on('change', '#selectRespuesta', function() {
    let respuesta = $("#selectRespuesta").val();
    if (respuesta === "RECHAZADA") {
        $("#selectMotivo").attr("disabled", false);
    } else {
        $("#selectMotivo").attr("disabled", true);
        $("#selectMotivo").val("");
    };
});
/***************************************************************/
/*** Botón de envío en el modal para actualizar la respuesta ***/
/***************************************************************/
$('body').on('click', '#aEnvio', function() {
    let base_url = $("#base_url").val();
    let id_desembolso = $("#id_desembolso").text();
    let respuesta = $("#selectRespuesta").val();

    if (respuesta === "") {
        return $("#pError").text('Debe seleccionar una Respuesta');
    }

    if (respuesta === "RECHAZADA") {
        let motivo = $("#selectMotivo").val()
        if (motivo === "") {
            return $("#pError").text('Debe seleccionar un Motivo');
        } else {
            respuesta = "RECHAZADA" + "-" + $("#selectMotivo").val();
        }
    };
    let id_solicitud = $('#id_solicitud').text();
    $.ajax({
            dataType: "JSON",
            data: { "respuesta": respuesta, "id_solicitud": id_solicitud },
            method: "POST",
            url: base_url + 'api/ApiGastos/actualizaDesembolsoValidado/' + id_desembolso,
        })
        .done(function(response) {
            if (response.status.ok) {
                let file = document.getElementById('file');
                let adjunto = '';
                if (file.value) {
                    let form = new FormData();
                    form.append("file", file.files[0], file.value);
                    data = form;
                    let settings = {
                        "url": base_url + 'api/ApiGastos/uploadComprobanteValidado/' + id_desembolso,
                        "method": "POST",
                        "timeout": 0,
                        "processData": false,
                        "mimeType": "multipart/form-data",
                        "contentType": false,
                        "data": data
                    };
                    $.ajax(settings).done(function(responseComprobante) {
                        var fecha = new Date(),
                            month = '' + ("0" + (fecha.getMonth() + 1)).slice(-2);
                        year = fecha.getFullYear();
                        var nombre_comprobante = responseComprobante.slice(86, 115);
                        var archivo_ruta = $("#base_url").val() + 'public/tesoreria/comprobantes/' + year + '/' + month + '/' + nombre_comprobante;
                        adjunto = '<a href="' + archivo_ruta + '" target="_blank">' + nombre_comprobante + '</a>';
                        /*** Se realiza el track de gestión ***/
                        track_gestion($('#id_solicitud').text(), response.id_operador, response.fecha, respuesta, adjunto);

                        $('#modalValidarDesembolso').modal("hide");
                        Swal.fire("¡Éxito!",
                            'Archivo subido y actualizado con éxito',
                            "success"
                        ).then(() => {
                            validarDesembolso();
                        });
                    }).fail(function(xhr) {
                        Swal.fire("Atencion!",
                            `readyState: ${xhr.readyState}
							status: ${xhr.status}
							responseText: ${xhr.responseText}`,
                            "error"
                        )
                    });
                } else {
                    $('#modalValidarDesembolso').modal("hide");
                    Swal.fire("¡Exito!",
                        'Actualizado con éxito',
                        "success"
                    ).then(() => {
                        validarDesembolso();
                    });
                    /*** Se realiza el track de gestión ***/
                    track_gestion($('#id_solicitud').text(), response.id_operador, response.fecha, respuesta, 'No');
                }
            }
        })
        .fail(function(xhr) {
            Swal.fire("Atencion!",
                `readyState: ${xhr.readyState}
				status: ${xhr.status}
				responseText: ${xhr.responseText}`,
                "error"
            );
        });
});

function track_gestion(id_solicitud, id_operador, fecha, respuesta, adjunto) {
    let type_contact = 170;
    let comment = "<b>[VALIDAR DESEMBOLSO]</b>" +
        "<br> Fecha: " + fecha +
        "<br> Respuesta: " + respuesta +
        "<br> Comprobante adjunto: " + adjunto
    saveTrack(comment, type_contact, id_solicitud, id_operador);

}
/*******************************************************************/
/*** Botón de bíusqueda de las solicitudes validadas o a validar ***/
/*******************************************************************/
$('body').on('click', '#aBuscar', function() {
    let base_url = $("#base_url").val();
    $.ajax({
            url: base_url + "api/ApiGastos/searchSolicitud/" + $("#search_solicitud").val(),
            type: "GET",
        })
        .done(function(response) {
            if (response.data.length > 0) {
                let tblBodyDesembolsos = document.getElementById("tbl_body_validar_desembolsos");
                let html = '';

                response.data.forEach(solicitud => {
                    html = html + `
                <tr>
                    <td>${solicitud.id_solicitud}</td>
                    <td>${solicitud.estado}</td>
                    <td>${solicitud.tipo_solicitud}</td>
                    <td>${solicitud.nombre_apellido}</td>
                    <td>${solicitud.fecha_hora_solicitud}</td>
                    <td>
                        <a id="aValidar" 
                            class="btn btn-primary btn-xs"
                            title="validar"
                            id_desembolso="${solicitud.id}"
                            id_solicitud="${solicitud.id_solicitud}"
                            fecha_hora_solicitud="${solicitud.fecha_hora_solicitud}"
                            fecha_alta="${solicitud.fecha_alta}"
                            documento="${solicitud.documento}"
                            nombre_apellido="${solicitud.nombre_apellido}"
                            origen_pago="${solicitud.origen_pago}"
                            fecha_procesado=${solicitud.fecha_procesado}"
                            pagado="${solicitud.pagado}"
                            ruta_txt="${solicitud.ruta_txt}"
							nombre_apellido_operador="${solicitud.nombre_apellido_operador}"
							respuesta="${solicitud.respuesta}"
                            comprobante="${solicitud.comprobante}"
                        >
                            <i class="fa fa-check"></i>
                        </a>
                    </td>
                </tr>`
                });
                tblBodyDesembolsos.innerHTML = html;
            } else {
                Swal.fire("¡Informacion!",
                    'La solicitud no se encuentra',
                    "info"
                )

            }
        })
        .fail(function(xhr) {
            Swal.fire("Atencion!",
                `readyState: ${xhr.readyState}
				status: ${xhr.status}
				responseText: ${xhr.responseText}`,
                "error"
            );
        });
});
/*******************************************************************************/
/*** Botón reset que recarga todas las solicitudes pendientes por validación ***/
/*******************************************************************************/
$("body").on("reset", '#form_search_solicitud', function() {
    validarDesembolso();
});
/********************************************************************/
/*** Obtiene la cantidad de Imputaciones pendientes para el Badge ***/
/********************************************************************/
function getCantImputacionesPendientes() {
    let base_url = $("#base_url").val();

    $.ajax({
            type: "GET",
            url: base_url + 'api/ApiGastos/cantImputacionesPendientes',
        })
        .done(function(response) {
            if (response.status.ok) {
                $("#cantImputarPendiente").text(response.cantPendiente);
            }
        })
        .fail(function(xhr) {
            Swal.fire("Atencion!",
                `readyState: ${xhr.readyState}
				status: ${xhr.status}
				responseText: ${xhr.responseText}`,
                "error"
            );
        });
}


function cargarBancos() {
    base_url = $("input#base_url").val() + "api/ApiPrestamo/consultarBancos";

    $.ajax({
        type: "GET",
        url: base_url,
        dataType: "json",
        beforeSend: function() {},
        complete: function() {},
        success: function(response) {
            console.log(response);
            $(".calculo-desembolso .box-body").html('');
            if (response.status.ok) {
                let bancos = response.data;
                $.each(bancos, function(index, value) {
                    let html = "";

                    html += '<div class="col-sm-3"><div class="panel panel-info" style="margin-bottom:0px !important; font-size: 12px;"><div class="panel-heading">';
                    html += '<h3 class="panel-title">' + value.Nombre_Banco + '</h3>';
                    html += '</div><div class="panel-body"><div class="row"><div class="col-sm-3 text-center">';
                    html += '<p>UNIDADES</p><p id="unidades-' + value.id_Banco + '">' + value.valores.unidades + '</p>';
                    html += '</div><div class="col-sm-6 text-center" style="border-left: 1px solid #bce8f1;border-right: 1px solid #bce8f1;">';
                    html += '<p>MONTO $</p><p id="monto-total-' + value.id_Banco + '">' + number_format(value.valores.monto, 2); + '</p>';
                    html += '</div><div class="col-sm-3 text-center">';

                    html += '<a class="btn btn-warning btn-sm update-' + value.id_Banco + '"><i class="fa fa-spinner"></i></a>';
                    html += '<a class="descargas btn btn-success btn-sm download-' + value.id_Banco + '"><i class="fa fa-download"></i></a>';

                    html += '</div></div><hr style="border-top-color: #bce8f1;margin-top: 5px;margin-bottom: 5px;"><div class="row"><div class="col-sm-3 text-center">';
                    html += '<p style="margin-bottom:5px;">CALCULAR</p><div class="input-group">';
                    html += '<input type="number" id="limit-' + value.id_Banco + '" value="0" class="form-control" min="0" step="1" style="height: 25px;" >';
                    html += '</div></div><div class="col-sm-6 text-center" style="border-left: 1px solid #bce8f1;border-right: 1px solid #bce8f1;">';
                    html += '<p>MONTO $</p><p id="monto-' + value.id_Banco + '">0</p></div><div class="col-sm-3 text-center">';

                    html += '<a class="btn btn-info btn-sm exc-' + value.id_Banco + '"><i class="fa fa-gears"></i></a>';
                    html += '<a class="descargas btn bg-blue btn-sm resumen-' + value.id_Banco + '"><i class="fa fa-download"></i></a>';

                    html += '</div></div></div></div></div>';

                    $(".calculo-desembolso .box-body").append(html);

                    $(".update-" + value.id_Banco).on('click', function() {
                        $("#limit-" + value.id_Banco).val(0);
                        actualizarCalculosArchivos(value.id_Banco, -1)
                    });
                    $(".download-" + value.id_Banco).on('click', function() {

                        descargarCalculosArchivos(value.endpoint, value.id_Banco, -1);

                    });
                    $(".exc-" + value.id_Banco).on('click', function() {
                        actualizarCalculosArchivos(value.id_Banco, 1)
                    });
                    $(".resumen-" + value.id_Banco).on('click', function() {
                        descargarCalculosArchivos(value.endpoint, value.id_Banco, 1);

                    });
                });

                setTimeout(function() {
                    $(".descargas").addClass('disabled');
                }, 10000);

            } else {
                Swal.fire(response.message, "", "error")
            }

        },
        error: function(jqXHR, textStatus, errorThrown) {

        }
    });
}

function actualizarCalculosArchivos(banco, limit) {
    let cantidad = 0;
    //si seactualizara la seccion de recalculo
    if (limit > 0) {
        cantidad = $("#limit-" + banco).val();
    }

    base_url = $("input#base_url").val() + "api/ApiPrestamo/consultarBancos/" + banco + "/" + cantidad;

    $.ajax({
        type: "GET",
        url: base_url,
        dataType: "json",
        beforeSend: function() {
            disabledButtons(true);
        },
        complete: function() {
            disabledButtons(false);
        },
        success: function(response) {
            console.log(response);

            if (response.status.ok) {
                let bancos = response.data[0];

                if (limit > 0) {
                    $(".resumen-" + bancos.id_Banco).removeClass('disabled');
                    $("#monto-" + bancos.id_Banco).html(number_format(bancos.valores.monto, 2));
                    setTimeout(function() {
                        $(".resumen-" + bancos.id_Banco).addClass('disabled');
                    }, 10000);
                } else {
                    $(".download-" + bancos.id_Banco).removeClass('disabled');
                    $("#unidades-" + bancos.id_Banco).html(bancos.valores.unidades);
                    $("#monto-total-" + bancos.id_Banco).html(number_format(bancos.valores.monto, 2));
                    setTimeout(function() {
                        $(".download-" + bancos.id_Banco).addClass('disabled');
                    }, 10000);
                }

            } else {
                Swal.fire(response.message, "", "error")
            }

        },
        error: function(jqXHR, textStatus, errorThrown) {

        }
    });


}



function descargarCalculosArchivos(url, banco, limit) {
    let mensaje = "¿Seguro que desea generar y enviar el archivo de desembolso con " + $("#unidades-" + banco).val() + " registros para un total de $" + $("#monto-total-" + banco).text() + " ?";
    if (limit > 0) {
        url += "/" + $("#limit-" + banco).val();
        mensaje = "¿Seguro que desea generar y enviar el archivo de desembolso con " + $("#limit-" + banco).val() + " registros para un total de $" + $("#monto-" + banco).text() + " ?";
    }

    if (limit > 0 && $("#limit-" + banco).val() < 1) {
        Swal.fire("no hay registros para generar el archivo", "", "warning");
        return false;
    }
    let base_url = $("input#base_url").val();
    swal.fire({
        title: "Generacion de archivo",
        text: mensaje,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#dedede",
        confirmButtonText: "Descargar y Enviar",
        cancelButtonText: "Cancelar"
    }).then(function(result) {
        if (result.value) {
            let form = new FormData();
            form.append("url", url);
            $.ajax({
                url: base_url + 'api/ApiPrestamo/generarDesembolso',
                type: "POST",
                processData: false,
                contentType: false,
                data: form,
                beforeSend: function() {
                    disabledButtons(true);
                },
                complete: function() {
                    disabledButtons(false);
                },
                success: function(response) {

                    if (response.success) {
                        let blob = response.url
                        const url2 = base_url + blob;
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url2;
                        // the filename you want
                        a.download = response.nombre_archivo;
                        document.body.appendChild(a);
                        a.click();
                        swal.fire('', 'Archivo Generado y enviado con exito', 'success');


                    } else {
                        Swal.fire("", response.title_response, "error");
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {

                }
            });

        }
    });
}


function formatNumber(numero) {
    let num = parseFloat(numero).toFixed(2);
    var num_parts = num.toString().split(".");
    num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return num_parts.join(",");
}

function buscarClienteDevolucion(documento) {
    if (documento.trim().length > 0) {

        let base_url = $("input#base_url").val() + "api/solicitud/getSolicitudesDevolucion";
        $.ajax({
            type: "GET",
            url: base_url,
            data: { 'documento': documento, 'estado': 1 },
            success: function(response) {

                let datos = response.data
                if (datos.length > 0) {

                    $("#box_client").removeClass('hide');

                    let tblBodyImputacion = document.getElementById("tbl_body_devolucion_cliente");
                    let html = '';
                    $.each(datos, function(index, value) {
                        html += `
						<tr>
							<td>${moment(value.fecha).format('DD/MM/YYYY')}</td>
							<td>${value.hora}</td>
							<td>${value.solicitado}</td>
							<td>${value.documento}</td>
							<td>${value.nombres} ${value.apellidos}</td>
							<td>${formatNumber(value.monto_devolver)}</td>
							<td>${((value.fecha_proceso != null)? moment(value.fecha_proceso).format('DD/MM/YYYY hh:mm:ss'):'')}</td>
							<td>${((value.resultado != null)? value.resultado:'')}</td>
							<td>${formatNumber(value.monto_devuelto)}</td>
							<td>${((value.estado == 1)? 'Procesado':'Pendiente')}</td>
							<td>
								<div><a class='btn btn-xs btn-primary ' onclick='cargarInfoDevolucion(${value.id_cliente}, ${value.id})' title='Ver Solicitud'><i class='fa fa-eye'></i></a></div>
							</td>
						</tr>`
                    });

                    tblBodyImputacion.innerHTML = html;
                } else {
                    Swal.fire('', 'El cliente no tiene ninguna devolucion procesada', 'error');
                }


            }
        });
    } else {
        Swal.fire({
            title: "Documento invalido",
            text: 'Ingrese un número de documento valido',
            icon: 'error'
        });
    }
}


function initTableSolicitudDevolucion(estado = 0) {
    let ajax = {
        'type': "GET",
        'url': $("input#base_url").val() + "api/solicitud/getSolicitudesDevolucion",
        'data': { estado: estado }
        //'data': {estado:0}
    }
    let columns = [{
            "data": null,
            "render": function(data, type, row, meta) {
                return moment(data.fecha).format('DD/MM/YYYY')
            }
        },
        {
            "data": "hora",
            "render": function(data, type, row, meta) {
                return data
            }
        },
        {
            "data": "solicitado",
            "render": function(data, type, row, meta) {
                return data
            }
        },
        {
            "data": "documento",
            "render": function(data, type, row, meta) {
                return data
            }
        },
        {
            "data": null,
            "render": function(data, type, row, meta) {
                return (data.nombres + ' ' + data.apellidos);
            }
        },
        {
            "data": "banco",
        },
        {
            "data": "cuenta",
        },
        {
            "data": null,
            "render": function(data, type, row, meta) {
                return formatNumber(data.monto_devolver);
            }
        },
        {
            "data": null,
            "render": function(data, type, row, meta) {
                return (data.fecha_proceso != null) ? moment(data.fecha_proceso).format('DD/MM/YYYY hh:mm:ss') : '';
            }
        },
        {
            "data": "resultado"
        },
        {
            "data": null,
            "render": function(data, type, row, meta) {
                return formatNumber(data.monto_devuelto);
            }
        },
        {
            "data": null,
            "render": function(data, type, row, meta) {
                if (data.estado == 1) {
                    return '<p class="text-success">PROCESADO</p>';
                }
                if (data.estado == 0) {
                    return '<p class="text-info">PENDIENTE</p>';
                }
                if (data.estado == 2) {
                    return '<p class="text-warning">PROCESANDO</p>';
                }
            }
        },
        {
            "data": null,
            "render": function(data, type, row, meta) {
                var buttonUp = "<div>" +
                    "<a class='btn btn-xs btn-primary' onclick='cargarInfoDevolucion(" + data.id_cliente + ", " + data.id + ")' title='Ver Solicitud'>" +
                    "<i class='fa fa-upload'></i>" +
                    "</a>" + ((data.estado == 0) ?
                        "<a class='btn btn-xs btn-warning' onclick='cambiarEstado(" + data.estado + ", " + data.id + ")' title='cambiar estado'>" +
                        "<i class='fa fa-exchange'></i>" +
                        "</a>" : "") + "</div>";

                return buttonUp;
            }
        }
    ];
    let columnDefs = [{
        targets: [7, 10],
        className: 'dt-body-right'
    }]
    TablaPaginada('tbl_solicitud_devolucion_all', 1, 'asc', '', '', ajax, columns, columnDefs);

}


function cambiarEstado(estado, id) {

    if (estado == 0) {
        swal.fire({
            title: "Cambio de estado",
            text: "¿Cambiar estado de la devolucion a PROCESANDO?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#dedede",
            confirmButtonText: "Enviar",
            cancelButtonText: "Cancelar"
        }).then(function(result) {
            if (result.value) {

                const formData = new FormData();

                let base_url = $("input#base_url").val();
                formData.append("estado", 2);
                formData.append("id", id);

                $.ajax({
                    url: base_url + 'api/solicitud/cambiarEstado',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function(response) {

                    if (response.status.ok) {
                        Swal.fire('Solicitud de devolución', response.message, 'success');
                    } else {
                        Swal.fire('Solicitud de devolución', 'No fue posible actualizar la solicitud', 'error');
                    }
                    auxTabla.ajax.reload(function() {
                        $("#buttonDevolucion .badge").html(auxTabla.data().count());
                    });

                }).fail(function(xhr) {
                    Swal.fire('Solicitud de devolución', 'No fue posible actualizar la solicitud', 'error');
                });

            }
        });
    }
}

function cargarInfoDevolucion(id_cliente, id_devolucion) {
    $("#tabla-pagos").dataTable().fnDestroy();
    $('#myModalDevolucion #tabla-pagos tbody').html("");
    $('#myModalDevolucion #tabla-comprobantes-devolucion tbody').html("");
    $('#myModalDevolucion #comprobante-devolucion').val("");
    $('#myModalDevolucion #resultado').prop("disabled", false);
    $('#myModalDevolucion #monto-devuelto').prop("readOnly", false);
    $('#myModalDevolucion #comentario').prop("readOnly", false)
    $('#myModalDevolucion #comentario').val("");

    let base_url = $("input#base_url").val() + "api/solicitud/consultarDatosDevolucion/" + id_cliente;
    $.ajax({
        type: "GET",
        url: base_url,
        data: { 'id_devolucion': id_devolucion },
        success: function(response) {
            if (response.status.ok) {
                $('#myModalDevolucion .accion').removeClass("hide");
                $('#myModalDevolucion #documento').val(response.data.cliente[0].documento);
                $('#myModalDevolucion #nombres').val(response.data.cliente[0].nombres + ' ' + response.data.cliente[0].apellidos);
                $('#myModalDevolucion #banco').val(response.data.cliente[0].Nombre_Banco);
                $('#myModalDevolucion #tipo').val(response.data.cliente[0].Nombre_TipoCuenta);
                $('#myModalDevolucion #cuenta').val(response.data.cliente[0].numero_cuenta);
                $('#myModalDevolucion #id_cliente').val(id_cliente);
                $('#myModalDevolucion #forma').val(response.data.devolucion[0].forma_devolucion);
                $('#myModalDevolucion #monto').val(formatNumber(response.data.devolucion[0].monto_devolver, 2));
                $('#myModalDevolucion #nombreApellido').val(response.data.devolucion[0].solicitado);
                $('#myModalDevolucion #fecha').val((response.data.devolucion[0].fecha != null) ? moment(response.data.devolucion[0].fecha).format('DD/MM/YYYY') : '');
                $('#myModalDevolucion #tabla-pagos tbody').html('');
                $('#myModalDevolucion #tabla-comprobantes tbody').html('');
                $('#myModalDevolucion #tabla-comprobantes-devolucion tbody').html('');
                $('#myModalDevolucion #monto-devuelto').val(formatNumber(response.data.devolucion[0].monto_devolver, 2));

                $('#myModalDevolucion #enviar').data('id-devolucion', id_devolucion);
                $('#myModalDevolucion #enviar').data('id-credito', response.data.pagosDevolucion[0].id_credito);

                if (response.data.devolucion[0].estado != 0) {
                    $('#myModalDevolucion #monto-devuelto').val(response.data.devolucion[0].monto_devuelto);
                    $('#myModalDevolucion #comentario').val(response.data.devolucion[0].comentario);
                    $('#myModalDevolucion #resultado').val(response.data.devolucion[0].resultado);
                    $('#myModalDevolucion #resultado').prop("disabled", true);
                    $('#myModalDevolucion #monto-devuelto').prop("readOnly", true);
                    $('#myModalDevolucion #comentario').prop("readOnly", true);
                    $('#myModalDevolucion .accion').addClass("hide");

                }


                let pagos = response.data.pagos;
                $.each(pagos, function(index, value) {
                    $('#myModalDevolucion #tabla-pagos tbody').append(
                        '<tr><td>' +
                        value.fecha_pago + '</td> <td>' +
                        value.monto + '</td> <td>' +
                        value.medio_pago + '</td> <td>' +
                        ((value.referencia_externa != null) ? value.referencia_externa : '') + '</td> <td>' +
                        ((value.referencia_interna != null) ? value.referencia_interna : '') + '</td> <td>' +
                        ((value.estado == 1) ? 'Cobro realizado' : 'No cobrado') +
                        '</td> <td class="text-center"> ' + ((value.estado == 1) ? '<div class="checkbox" style="margin:0px;"> <label><input type="checkbox" id="' + value.id + '" data-id_pago="' + value.id + '" value="' + value.monto + '"></label> </div>' : '') + '</td> </tr>');
                });

                $.each(response.data.pagosDevolucion, function(index, pago) {
                    $('#myModalDevolucion input:checkbox#' + pago.id_pago).prop("checked", true);
                });

                let comprobantes = response.data.comprobantes;
                $.each(comprobantes, function(index, comprobante) {
                    comp = comprobante.comprobante;
                    $("#myModalDevolucion #tabla-comprobantes tbody").append(
                        '<tr><td>' + (comp.substring(comp.lastIndexOf('/') + 1)) + '</td><td class="text-center"><a href="' + $("input#base_url").val() + comp.substring(1) +
                        '" target="_blank"  class="btn btn-primary btn-xs view"><i class="fa fa-eye"></i></a></td></tr>');
                });

                let comprobantesDevolucion = response.data.comprobantesDevolucion;
                $.each(comprobantesDevolucion, function(index, comprobanteDevolucion) {
                    comp = comprobanteDevolucion.comprobante;
                    $("#myModalDevolucion #tabla-comprobantes-devolucion tbody").append(
                        '<tr><td>' + (comp.substring(comp.lastIndexOf('/') + 1)) + '</td><td class="text-center"><a href="' + $("input#base_url").val() + comp.substring(1) +
                        '" target="_blank"  class="btn btn-primary btn-xs view"><i class="fa fa-eye"></i></a></td><td></td></tr>');
                });

                $('#myModalDevolucion input:checkbox').prop("disabled", true);


                tablaPagos = $('#myModalDevolucion #tabla-pagos').DataTable({ "pageLength": 3 });


                $('#myModalDevolucion').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            } else {
                Swal.fire({
                    title: "Ups!",
                    text: response.message,
                    icon: 'error'
                });
            }


        }
    });
}

function subirComprobante() {
    let file = document.getElementById('comprobante-devolucion');
    let form = new FormData();

    form.append("file", file.files[0], file.value);
    data = form;
    let base_url = $("#base_url").val();
    let settings = {
        "url": base_url + 'api/solicitud/uploadComprobanteDevolucion',
        "method": "POST",
        "timeout": 0,
        "processData": false,
        "mimeType": "multipart/form-data",
        "contentType": false,
        "data": data,
    };

    $.ajax(settings).done(function(response) {
        response = JSON.parse(response);
        if (response.status.ok) {
            $("#myModalDevolucion #tabla-comprobantes-devolucion tbody").append(
                '<tr><td>' + response.nombre + '</td><td class="text-center"><a href="' + base_url + response.url +
                '" target="_blank" data-name="' + response.nombre + '" class="btn btn-primary btn-xs view"><i class="fa fa-eye"></i></a></td><td class="text-center"><a onclick="eliminarComprbante(this,\'' + response.nombre + '\')" class="btn btn-danger btn-xs delete" data-name="' + response.nombre + '" ><i class="fa fa-times"></i></a></td></tr>');
        }
    }).fail(function(xhr) {
        Swal.fire("¡Atencion!",
            `readyState: ${xhr.readyState}
                status: ${xhr.status}
                responseText: ${xhr.responseText}`,
            "error"
        )
    });
}

function eliminarComprbante(elemento, nombre) {
    let form = new FormData();

    form.append("file", nombre);
    data = form;
    let base_url = $("#base_url").val();

    let settings = {
        "url": base_url + 'api/solicitud/deleteComprobanteDevolucion',
        "method": "POST",
        "timeout": 0,
        "processData": false,
        "mimeType": "multipart/form-data",
        "contentType": false,
        "data": data,
    };

    $.ajax(settings).done(function(response) {
        response = JSON.parse(response);
        if (response.status.ok) {
            $(elemento).closest('tr').remove();
        } else {
            Swal.fire("¡Atencion!", "no pudimos remover el comprobante", "error");
        }
    }).fail(function(xhr) {
        Swal.fire("¡Atencion!", "no pudimos remover el comprobante", "error");
    });
}

function procesarSolicitudDevolucion() {
    swal.fire({
        title: "¿Esta seguro?",
        text: "Si se realiza la devolución el credito cambiará de estado a 'mora' o 'vigente' según corresponda",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#dedede",
        confirmButtonText: "Enviar",
        cancelButtonText: "Cancelar"
    }).then(function(result) {
        if (result.value) {


            const formData = new FormData();


            let comprobantes = [];
            $("#myModalDevolucion #tabla-comprobantes-devolucion a.view").each(function() {
                comprobantes.push($(this).data('name'));
            });

            let base_url = $("input#base_url").val();
            formData.append("respuesta", $("#resultado").val());
            formData.append("monto", parseFloat($("#monto-devuelto").val().split('.').join("").replace(',', '.')));
            formData.append("comentario", $("#comentario").val());
            formData.append("comprobantes", comprobantes);
            formData.append("id_devolucion", $("#myModalDevolucion #enviar").data('id-devolucion'));
            formData.append("id_credito", $("#myModalDevolucion  #enviar").data('id-credito'));

            $.ajax({
                url: base_url + 'api/solicitud/procesarSolicitudDevolucion',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function(response) {

                if (response.status.ok) {
                    Swal.fire('Solicitud de devolución', response.message, 'success');
                    $('#myModalDevolucion').modal('hide');

                } else {
                    Swal.fire('Solicitud de devolución', 'No fue posible enviar la solicitud', 'error');
                }
                auxTabla.ajax.reload(function() {
                    $("#buttonDevolucion .badge").html(auxTabla.data().count());
                });

            }).fail(function(xhr) {
                Swal.fire('Solicitud de devolución', 'No fue posible enviar la solicitud', 'error');
            });


        }

    });

}


function generarArchivoDevolucion() {
    let base_url = $("input#base_url").val();

    $.ajax({
        url: base_url + 'api/solicitud/generarArchivoDevolucion',
        type: 'GET',
    }).done(function(response) {
        console.log("response");

        if (response.status.ok) {
            let blob = response.url
            const url2 = base_url + blob;
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url2;
            // the filename you want
            a.download = response.nombre_archivo;
            document.body.appendChild(a);
            a.click();
            swal.fire('', 'your file has downloaded!', 'success');
            auxTabla.ajax.reload(function() {
                $("#buttonDevolucion .badge").html(auxTabla.data().count());
            });

        } else {
            Swal.fire('Solicitud de devolución', 'No fue posible enviar la solicitud', 'error');
        }


    }).fail(function(xhr) {
        Swal.fire('Solicitud de devolución', 'No fue posible enviar la solicitud', 'error');
    });
}

function filtrarEstado() {
    let estado = $("#estado-consulta").data('estado');
    let next;
    switch (estado) {
        case 0:
            $("#estado-consulta").data('estado', 2);
            $("#estado-consulta").html('PENDIENTES');
            $("#estado-consulta").removeClass('btn-warning');
            $("#estado-consulta").addClass('btn-info');
            next = 2;
            break;
        case 2:
            $("#estado-consulta").data('estado', 0);
            $("#estado-consulta").html('PROCESANDO');
            $("#estado-consulta").addClass('btn-warning');
            $("#estado-consulta").removeClass('btn-info');
            next = 0;

            break;
    }


    $("#tbl_solicitud_devolucion_all").dataTable().fnDestroy();
    initTableSolicitudDevolucion(next)
    auxTabla.ajax.reload(function() {
        $("#buttonDevolucion .badge").html(auxTabla.data().count());
    });
}