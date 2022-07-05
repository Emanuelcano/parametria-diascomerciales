function buscarCreditoCobranza(search, fecha = null , operador = null, criterio = null) {
    paginacion = 0;

    let base_url = $("#base_url").val();

    const formData = new FormData();
    formData.append("search", search);
    formData.append("fecha", fecha);
    formData.append("operador", operador);
    formData.append("criterio", criterio);

    table_search.processing(true);
    $.ajax({
        url: base_url + 'api/credito/buscar/',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
    })
        .done(function (response) {
            table_search.processing(false);
            table_search.clear();
            table_search.rows.add(response.creditos);
            table_search.draw();
            $("#section_search_credito #result").show();

        })
        .fail(function (response) {
            //console.log("error");
        })
        .always(function (response) {
            //console.log("complete");
        });
 
}
function formatNumber(numero) {
    let num = parseFloat(numero).toFixed(2);
    var num_parts = num.toString().split(".");
    num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return num_parts.join(",");
}
/***************************************************************************/
// Tracker
/***************************************************************************/

function get_box_track(id_solicitud, id_credito=0) {
    var documento= $("#box_client_title").data("documento");
    $.ajax({
        url: base_url + 'solicitud/gestion/track/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".tracker").html(response);
            $(".tracker #box_tracker .box-footer").css('min-height', '185px');
            $(".tracker #box_tracker .box-body").css('height', 'calc(100% - 185px)');
            get_box_whatsapp(documento);
            if(id_credito > 0){
                $('#result').addClass('hide');
                document.getElementById('id_credito').value = id_credito;
            }
            
        })
        .fail(function (response) {
        })
        .always(function () {

        });
}
/***************************************************************************/
// Chat Whatsapp
/***************************************************************************/

function get_box_whatsapp(documento) {
    if ($(".row-chat-track").find('.whatsapp').length > 0) {
        $.ajax({
            url: base_url + 'solicitud/gestion/whatsapp_paginado/' + documento + '/' + paginacion + '/2/',
            type: 'GET',
        })
            .done(function (response) {
                $(".whatsapp").html(response);
                $("#box_whatsapp").css("height", $(".row-chat-track").css("height"));
                $(".main-menu .__chat_history_container").css("max-height", ($(".row-chat-track").height() - 400) + 'px');
            })
            .fail(function () {
            })
            .always(function () {
            });
    }
}

/***************************************************************************/
// Track Marcacion
/***************************************************************************/

function get_box_marcacion(id_cliente) {
    $.ajax({
        url: base_url + 'api/credito/get_track_marcacion/' + id_cliente,
        type: 'GET',
    })
        .done(function (response) {
            if (response.status.ok) {
                let registros = response.data;
                let resultados = response.resultados;
                let aux_resultados = [];


                let colores = [];
                let colores_bordes = [];



                //array con los posibles resultados
                resultados.forEach(resultado => {
                    let slik = JSON.parse(skilResult);

                    aux_resultados.push(slik[resultado.skill_resultado]);

                    let color_r = Math.ceil(25 * (Math.random() * (10 - 1) + 1));
                    let color_g = Math.ceil(25 * (Math.random() * (10 - 1) + 1));
                    let color_b = Math.ceil(25 * (Math.random() * (10 - 1) + 1));

                    colores.push('rgba(' + color_r + ',' + color_g + ',' + color_b + ',0.2)');
                    colores_bordes.push('rgba(' + color_r + ',' + color_g + ',' + color_b + ',1)');
                });

                //array con la data de cada numero
                registros.forEach(resultado => {

                    let slik = JSON.parse(skilResult);

                    if (resultado["llamadas"].length > 0) {
                        //construimos la cantidad de lladas por respuesta
                        let cantidad_llamadas = [];

                        aux_resultados.forEach(tipo => {

                            let llamada = resultado["llamadas"].find(ll => slik[ll.skill_resultado] === tipo);

                            if (typeof (llamada) != 'undefined') {
                                cantidad_llamadas.push(llamada.cantidad_llamadas);
                            } else {
                                cantidad_llamadas.push(0);
                            }

                        });

                        $(".box_chart").prepend('<div class="col-sm-3"><canvas id="myChart-' + resultado.numero + '" width="400" height="300"></canvas></div>');

                        var ctx = document.getElementById('myChart-' + resultado.numero).getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: aux_resultados,
                                datasets: [{
                                    label: '',
                                    data: cantidad_llamadas,
                                    backgroundColor: colores,
                                    borderColor: colores_bordes,
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true,
                                            stepSize: 1
                                        }
                                    }]
                                },
                                title: {
                                    display: true,
                                    text: resultado.contacto + ' ' + resultado.numero + ' (' + resultado.fuente + ')'
                                },
                                legend: {
                                    display: false
                                }
                            }
                        });
                    }

                });

            }
        })
        .fail(function () {
        })
        .always(function () {
        });

}

function get_llamadas_detalle(id_cliente) {
    let document = $("#document").val();
    console.log(document);
    // alert(document);
    $.ajax({
        url: base_url + 'apiGetLlamadasNeotell',
        data:{documento:document},
        type: 'POST'
    })
        .done(function (response) {

            if (response.status.ok) {

                let tabla = [];
                llamadas = response.data;
                llamadas.forEach(item => {

                    var callType = JSON.parse(callTypes);
                    var actor = JSON.parse(actores);
                    console.log(item.path_audio);
                    tabla.push([
                        moment(item.fecha_audio).format('YYYY-MM-DD hh:mm:ss'),
                        item.contacto,
                        item.numero_solicitud.substr(-10),
                        item.fuente,
                        "NEOTELL",
                        (item.path_audio != null) ? ('<a class="btn btn-xs btn-success reproducir-audio" data-audio="' + item.path_audio + '" title="Reproducir"><i class="fa fa-play"></i></a><a class="btn btn-info btn-xs descargar-audio" data-audio="' + item.path_audio + '" ><i class="fa fa-download"></i></a>') : ' ',
                    ]);
                });
                //$("#table-track_marcacion").removeClass('hidden');
                $('#table-track_marcacion').DataTable({ "order": [[0, "desc"]], 'pageLength': 10 }).clear().rows.add(tabla).draw();
                $('a.reproducir-audio').click('on', function () { recuperarAudioNeotell(this); });
                $('a.descargar-audio').click('on', function () { recuperarAudioNeotell(this); });
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
    // $.ajax({
    //     url: base_url + 'api/credito/get_llamadas_detalle/' + id_cliente,
    //     type: 'GET'
    // })
    //     .done(function (response) {

    //         if (response.status.ok) {

    //             let tabla = [];
    //             ausencias = response.data;
    //             ausencias.forEach(item => {

    //                 var callType = JSON.parse(callTypes);
    //                 var actor = JSON.parse(actores);

    //                 tabla.push([
    //                     moment(item.fecha).format('YYYY-MM-DD hh:mm:ss'),
    //                     item.skill_result,
    //                     item.nombre,
    //                     item.name_agent,
    //                     item.telephone_number.substr(-10),
    //                     item.contacto,
    //                     item.fuente,
    //                     item.talk_time,
    //                     callType[item.tipo_llamada],
    //                     item.descri_typing_code,
    //                     item.descri_typing_code2,
    //                     actor[item.who_hangs_up],
    //                     item.central,
    //                     (item.path_audio != null) ? ('<a class="btn btn-xs btn-success reproducir-audio" data-audio="' + item.audio + '" title="Reproducir"><i class="fa fa-play"></i></a><a class="btn btn-info btn-xs descargar-audio" data-audio="' + item.audio + '" ><i class="fa fa-download"></i></a>') : ' ',
    //                 ]);
    //             });
    //             //$("#table-track_marcacion").removeClass('hidden');
    //             $('#table-track_marcacion').DataTable({ "order": [[0, "desc"]], 'pageLength': 10 }).clear().rows.add(tabla).draw();
    //             $('a.reproducir-audio').click('on', function () { recuperarAudio(this); });
    //             $('a.descargar-audio').click('on', function () { recuperarAudio(this); });
    //             $('#table-track_marcacion_paginate').click("on", function () {
    //                 $("a.reproducir-audio").attr("onclick", "").unbind("click");
    //                 $("a.descargar-audio").attr("onclick", "").unbind("click");
    //                 $('a.reproducir-audio').click('on', function () { recuperarAudio(this); });
    //                 $('a.descargar-audio').click('on', function () { recuperarAudio(this); });
    //             });

    //         } else {
    //             $('#table-track_marcacion').DataTable().clear().draw();
    //         }
    //     })
    //     .fail(function () {
    //     })
    //     .always(function () {
    //     });

}

function recuperarAudioNeotell(element) {
    let id_audio = $(element).data('audio');
    $(".reproduccion-audios .modal-content").html('<p>Para descargar el audio: Hacer click derecho sobre el audio y seleccionar la opcion "Guardar audio como"</p><audio src="'+id_audio+'" preload="auto" controls style="width: 100%;"><p>Tu navegador no implementa el elemento audio.</p></audio>');
    $(".reproduccion-audios").modal('toggle')
    $('.modal.reproduccion-audios').on('hidden.bs.modal', function (e) {
        $(".reproduccion-audios .modal-content audio").attr('src', '');
    });

}

function consultar_credito(id_credito, render_view = false) {
    if (render_view == false) {
        event.preventDefault();
    }
    
    let base_url = $("#base_url").val();
    //console.log(base_url+'solicitud/'+id_solicitud);
    $.ajax({
        url: base_url + 'credito/' + id_credito,
        type: 'GET',
    })
        .done(function (response) {
            paginacion = 0;
            if (response != "") {
                $("#tabla_creditos").hide();
                $("#section_search_credito #form_search").hide();
                $("#section_search_credito #result").hide();
                $(".desempenho").hide();
                $("#texto").text();
                $("#texto").html(response);
                $("#separador_cobranzas").hide();
                if ($(".row-chat-track").height() < $("#box_client_data").height())
                    $(".row-chat-track").css('height', $("#box_client_data").height());

                let id_solicitud = $("#id_solicitud").val();
                let id_cliente = $("#client").data('id_cliente');
                let documento = $("#client").data('number_doc');
                get_box_track(id_solicitud,id_credito);
               
                //get_box_marcacion(id_cliente);
                get_llamadas_detalle(id_cliente);
                //get_agenda_telefonica(documento,id_solicitud);

            } else {
                Swal.fire({
                    title: "ups!",
                    text: 'La solicitud del credito seleccionado ha sido cerrada',
                    icon: 'error'
                });
            }
        })
        .fail(function () {
        })
        .always(function () {
        });
}
/***************************************************************************/
// Agenda Telefonica
/***************************************************************************/

function generarPromesa(id_cliente) {
    const formData = new FormData();
    var cuotas = [];
    var detalle_plan = [];

    formData.append("medio", $("#medios").val());
    formData.append("id_cliente", id_cliente);

    //si el acuerdo viene por plan de pago
    if ($("#planes_pago").val() != "" && $("#planes_pago").val() != null) {
        $('.detalle-plan .table tbody tr').each(function () {
            var cuota_plan = $(this).data('id_detalle_plan');
            $(this).find("td input").each(function () {


                if (($(this).hasClass("hidden") && $(this).data("campo") == 'monto') || $(this).data("campo") != 'monto') {
                    cuota_plan += '&' + this.value;
                }
            });
            detalle_plan.push(cuota_plan);

        });
        formData.append("tipo", 'plan');
        formData.append("plan_detalle", detalle_plan);
        formData.append("fecha_calculo", $(".detalle-plan input[data-campo=fecha]").last().val());


        Swal.fire({
            title: '¡Atención!',
            text: 'Se generará el acuerdo de pago por $' + $("#monto").val() + '. Valido hasta el ' + $(".detalle-plan input[data-campo=fecha]").last().val(),
            icon: 'warning',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            showCancelButton: 'true'
        }).then((result) => {
            if (result.value) {

                $.ajax({
                    url: base_url + 'api/credito/crear_promesa',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function (response) {
                    Swal.fire({
                        title: "¡Perfecto!",
                        text: response.message,
                        icon: 'success'
                    });
                    consultarPromesas(id_cliente);

                    let id_solicitud = $("#id_solicitud").val();
                    let id_operador = $("section").find("#id_operador").val();
                    let type_contact = 10;
                    let comment = "<b>[ACUERDO DE PAGO]</b>" +
                        "<br> <b>Plan :" + $(".detalle-plan h5").data("plan") + "</b>" +
                        "<br> Medio: " + $("#medios").val();
                    "<br> Monto proyectoado: " + $("#monto").val();
                    detalle = response.data;
                    detalle.forEach(element => {
                        comment += "<br> <b>Cuota " + element.cuota + " para el " + moment(element.fecha).format('DD-MM-YYYY') + "</b>" +
                            "<br> Porcentaje: " + element.porcentaje +
                            "  Monto acuerdo: $" + formatNumber(element.monto);
                    });
                    saveTrack(comment, type_contact, id_solicitud, id_operador);
                })
                    .fail(function (response) {
                        //console.log("error");
                    })
                    .always(function (response) {
                        //console.log("complete");
                    });


            }
        });



    } else {

        $.each($("input[name='ch-creditos']:checked"), function () {
            cuotas.push($(this).data('id_cuota'));
        });

        if (cuotas.length > 0) {
            let monto = parseFloat($("#monto").val().split('.').join("").replace(',', '.'));
            if ($("#fechaAcuerdo").val() != "" && monto > 0 && $("#medio").val() != "") {
                let descuento = 0;
                let id_descuento = 0;
                if (typeof ($("#monto_descuento").val()) != 'undefined' && $("#plan_descuento").val() != '') {
                    descuento = $("#monto_descuento").val().split('.').join("").replace(',', '.');
                    id_descuento = $("#plan_descuento").val();
                }

                formData.append("fecha", $("#fechaAcuerdo").val());
                formData.append("cuotas", cuotas);
                formData.append("tipo", 'simple');
                formData.append("monto", monto);
                formData.append("monto_descuento", descuento);
                formData.append("id_plan", id_descuento);

                let comment = "<b>[ACUERDO DE PAGO]</b><br>Fecha: " + moment($("#fechaAcuerdo").val()).format('DD-MM-YYYY') + "<br> Medio: " + $("#medios").val() + "<br> Monto acuerdo: $" + formatNumber(monto);
                comment += (parseFloat(descuento) > 0) ? ("<br>Descuento: " + $("#plan_descuento option:selected").text()) : '';
                comment += (parseFloat(descuento) > 0) ? ("<br>Monto descuento: $" + formatNumber(descuento)) : '';
                comment += "<br>Monto proyectado: $" + formatNumber($("#monto").data('monto_original'));
                comment += "<br>";
                if ($("#ch-descuento-campania").is(':checked')) {
                    comment += "<b>Descuento de campaña aplicado</b>";
                    comment += "<br>";
                    comment += "Deuda Al Dia: $" + formatNumber($("#ch-descuento-campania").data('deuda'));
                    comment += "<br>";
                    comment += "Monto Descuento:$" + formatNumber($("#ch-descuento-campania").data('descuento'));
                    comment += "<br>";
                }

                Swal.fire({
                    title: '¡Atención!',
                    text: 'Se generará el acuerdo de pago por $' + $("#monto").val() + '. Valido hasta el ' + moment($("#fechaAcuerdo").val()).format('DD-MM-YYYY'),
                    icon: 'warning',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    showCancelButton: 'true'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            url: base_url + 'api/credito/crear_promesa',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                        }).done(function (response) {
                            Swal.fire({
                                title: "¡Perfecto!",
                                text: response.message,
                                icon: 'success'
                            });
                            consultarPromesas(id_cliente);

                            let id_solicitud = $("#id_solicitud").val();
                            let id_operador = $("section").find("#id_operador").val();
                            let type_contact = 10;
                            saveTrack(comment, type_contact, id_solicitud, id_operador);
                        })
                            .fail(function (response) {
                                //console.log("error");
                            })
                            .always(function (response) {
                                //console.log("complete");
                            });

                    }
                });

            } else {
                alert("los campos Fecha, Medio y Monto son obligatorios")
            }
        } else {
            alert("Debes seleccionar las cuotas para las cuales se generara la nueva promesa");
        }
    }
}

function consultarPromesas(id_cliente) {
    let epayco = $("#box_client_data #epayco");
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/credito/consultar_promesa/' + id_cliente,
        type: 'GET',
    })
        .done(function (response) {
            if (typeof (response.data) != "undefined" && response.data != null) {
                let acuerdos = response.data;
                let cumplidos = 0;
                let incumplidos = 0;
                var tabla = "";
                acuerdos.forEach(function (acuerdo, indice, array) {

                    tabla += '<tr>';
                    tabla += '<td class="">' + moment(acuerdo.fecha).format('DD-MM-YYYY') + '</td>';
                    //tabla +='<td class="">'+acuerdo.id_cliente+'</td>';
                    tabla += '<td class="">$' + formatNumber(acuerdo.monto) + '</td>';
                    tabla += '<td class="">' + acuerdo.medio + '</td>';
                    tabla += '<td class="">' + moment(acuerdo.fecha_hora).format('DD-MM-YYYY') + '</td>';
                    tabla += '<td class="">';
                    if (acuerdo.estado == 'pendiente')
                        tabla += '<i class="fa fa-spinner text-orange"></i>';
                    if (acuerdo.estado == 'cumplido') {
                        tabla += '<i class="fa fa-thumbs-up text-green"></i>';
                        cumplidos++;
                    }
                    if (acuerdo.estado == 'incumplido') {
                        tabla += '<i class="fa fa-thumbs-down text-red"></i>';
                        incumplidos++;
                    }
                    if (acuerdo.estado == 'anulado')
                        tabla += '<i class="fa fa-ban text-red"></i>';
                    tabla += '</td>';
                    tabla += '<td class="">';
                    tabla += '<a onclick="consultarPromesasDetalle(' + acuerdo.id + ')"><i class="fa fa-eye text-blue"></i></a>';
                    if (acuerdo.estado == 'pendiente' && acuerdo.editable) {
                        tabla += '<a style="margin-left: 5px;" onclick="ajustarPlanDescuento(' + acuerdo.id + ')"><i class="fa fa-edit text-blue"></i></a>';
                    }
                    tabla += '</td>';
                    tabla += '<td class="">';
                    if (acuerdo.estado == 'pendiente' && typeof (epayco.data('token')) != 'undefined') {
                        tabla += '<a data-action="cash" class="pay_it" data-credit="Acuerdo ' + acuerdo.id + '" data-quota="Pago" data-amount="' + acuerdo.monto + '" data-id_quota="A-' + acuerdo.id + '" data-key="' + epayco.data('token') + '" data-test="' + epayco.data("test") + '" >';
                        tabla += '<img src="' + base_url + 'assets/images/money.png" style="width:35%;""></a>';
                    }
                    tabla += '</td>';

                    tabla += '</tr>';

                });
                $("#cumplidos").html(cumplidos);
                $("#incumplidos").html(incumplidos);
                $("#tabla-promesa tbody").html(tabla);
                $('.row-chat-track').css('height', $('#box_client_data').height() + 'px');
            }
            $(".pay_it").on("click", function (event) {
                event.preventDefault();
                let test = $(this).data("test");
                let key = $(this).data("key");
                let client = $("#box_client_data #client");
                let epayco = $("#box_client_data #epayco");
                var handler = ePayco.checkout.configure({
                    key: key,
                    test: test
                });
                /**
                 * caso cuota: C-<id cuota>-<random> o <id cuota>-<random>
                 * caso acuerdo: A-<id cliente>-<random> | FIXME
                 * caso plan de pago: P-<id cliente>-<random> | TODO
                 */
                var data = {
                    //Parametros compra (obligatorio)
                    name: $(this).data("quota") + " - " + $(this).data("credit"),
                    description: $(this).data("quota") + " - " + $(this).data("credit"),
                    invoice: $(this).data("id_quota") + "-" + new Date().getTime(),
                    currency: "cop",
                    amount: Math.ceil($(this).data("amount")),
                    tax_base: "0",
                    tax: "0",
                    country: "co",
                    lang: "es",
                    //Onpage="false" - Standard="true"
                    external: "false",
                    //Atributos opcionales
                    extra1: "extra1",
                    extra2: "extra2",
                    extra3: "extra3",
                    confirmation: epayco.data("confirmation"),
                    response: epayco.data("response"),
                    //Atributos cliente
                    name_billing: client.data("name"),
                    type_doc_billing: "cc",
                    mobilephone_billing: client.data("mobilephone"),
                    number_doc_billing: client.data("number_doc"),
                    email_billing: client.data("email"),
                }
                
                handler.open(data);
            });
        })
        .fail(function () {
        })
        .always(function () {
        });
}

function consultarPromesasDetalle(id_promesa) {
    let base_url = $("#base_url").val();
    $(".ajuste-descuento").hide();

    $.ajax({
        url: base_url + 'api/credito/consultar_promesa_detalle/' + id_promesa,
        type: 'GET',
    })
        .done(function (response) {
            if (typeof (response.data) != "undefined" && response.data != null) {
                let detalle = response.data;
                let cuota = "";

                cuota = "<th style='padding: 10px;'>ACUERDO</th><th style='padding: 10px;'>FECHA DE PAGO</th><th style='padding: 10px;'>MONTO DEL ACUERDO</th><th style='padding: 10px;'>DESCUENTO</th><th style='padding: 10px;'>PLAN</th><th style='padding: 10px;'>MEDIO</th><th style='padding: 10px;'>ESTADO</th><th style='padding: 10px;'>OPERADOR</th><th style='padding: 10px;'>FECHA REGISTRO</th>";

                $("#credito-detalle #creditoLabel").html('DETALLE DEL ACUERDO ' + detalle[0].id);
                $("#credito-detalle #titulo-1").html('INFORMACION DEL ACUERDO');
                $("#credito-detalle #titulo-2").html('DETALLE DEL ACUERDO');
                $("#credito-detalle #detalle-credito-modal thead").html(cuota);

                cuota = "<th style='padding: 10px;'>CREDITO</th><th style='padding: 10px;'>CUOTA</th><th style='padding: 10px;'>VENCIMIENTO</th><th style='padding: 10px;'>ESTADO</th><th style='padding: 10px;'>A COBRAR</th>";
                $("#credito-detalle #detalle-cuota-modal thead").html(cuota);
                cuota = "";



                detalle.forEach(function (info, indice, array) {


                    if (info.fecha_cobro != null)
                        fecha_cobro = moment(info.fecha_cobro).format('DD-MM-YYYY');

                    cuota += "<tr>" +
                        "<td>" + info.id_credito + "</td>" +
                        "<td>" + info.numero_cuota + "</td>" +
                        "<td>" + moment(info.fecha_vencimiento.substr(0, 10)).format('DD-MM-YYYY') + "</td>" +
                        "<td>" + info.estado_cuota + "</td>" +
                        "<td>" + formatNumber(info.monto_acuerdo) + "</td>";
                });

                $("#credito-detalle #detalle-cuota-modal tbody").html(cuota);
                if (typeof (detalle[0].nombre_apellido) == 'undefined' || detalle[0].nombre_apellido == null) {
                    detalle[0].nombre_apellido = " ";
                }
                if (detalle[0].descripcion == 0 || detalle[0].descripcion == null) { detalle[0].descripcion = ' '; }
                $("#credito-detalle #detalle-credito-modal tbody").html("<tr>" +
                    "<td>" + detalle[0].id + "</td>" +
                    "<td>" + moment(detalle[0].fecha.substr(0, 10)).format('DD-MM-YYYY') + "</td>" +
                    "<td>" + formatNumber(detalle[0].monto) + "</td>" +
                    "<td>" + formatNumber(detalle[0].monto_descuento) + "</td>" +
                    "<td>" + detalle[0].descripcion + "</td>" +
                    "<td>" + detalle[0].medio + "</td>" +
                    "<td>" + detalle[0].estado + "</td>" +
                    "<td>" + detalle[0].nombre_apellido + "</td>" +
                    "<td>" + moment(detalle[0].fecha_hora.substr(0, 10)).format('DD-MM-YYYY') + "</td>");
                $('#credito-detalle').modal('show');

            }
        })
        .fail(function () {
        })
        .always(function () {
        });
}


function ajustarPlanDescuento(id_promesa) {
    let base_url = $("#base_url").val();
    $(".ajuste-descuento").show();
    $.ajax({
        url: base_url + 'api/credito/consultar_promesa_detalle/' + id_promesa,
        type: 'GET',
    })
        .done(function (response) {
            if (typeof (response.data) != "undefined" && response.data != null) {
                let detalle = response.data;
                let cuota = "";

                cuota = "<th style='padding: 10px;'>ACUERDO</th><th style='padding: 10px;'>FECHA DE PAGO</th><th style='padding: 10px;'>MONTO DEL ACUERDO</th><th style='padding: 10px;'>DESCUENTO</th><th style='padding: 10px;'>PLAN DESCUENTO</th><th style='padding: 10px;'>MEDIO</th><th style='padding: 10px;'>ESTADO</th><th style='padding: 10px;'>OPERADOR</th><th style='padding: 10px;'>FECHA REGISTRO</th>";

                $("#credito-detalle #creditoLabel").html('DETALLE DEL ACUERDO ' + detalle[0].id);
                $("#credito-detalle #titulo-1").html('INFORMACION DEL ACUERDO');
                $("#credito-detalle #titulo-2").html('AJUSTE DE PLAN DE DESCUENTO');
                $("#credito-detalle #detalle-credito-modal thead").html(cuota);
                $("#credito-detalle #detalle-cuota-modal thead").html('');
                $("#credito-detalle #detalle-cuota-modal tbody").html('');

                cuota = "";


                if (typeof (detalle[0].nombre_apellido) == 'undefined' || detalle[0].nombre_apellido == null) { detalle[0].nombre_apellido = " "; }
                if (detalle[0].descripcion == 0 || detalle[0].descripcion == null) { detalle[0].descripcion = ' '; }

                $("#credito-detalle #detalle-credito-modal tbody").html("<tr>" +
                    "<td>" + detalle[0].id + "</td>" +
                    "<td>" + moment(detalle[0].fecha.substr(0, 10)).format('DD-MM-YYYY') + "</td>" +
                    "<td>" + formatNumber(detalle[0].monto) + "</td>" +
                    "<td>" + formatNumber(detalle[0].monto_descuento) + "</td>" +
                    "<td>" + detalle[0].descripcion + "</td>" +
                    "<td>" + detalle[0].medio + "</td>" +
                    "<td>" + detalle[0].estado + "</td>" +
                    "<td>" + detalle[0].nombre_apellido + "</td>" +
                    "<td>" + moment(detalle[0].fecha_hora.substr(0, 10)).format('DD-MM-YYYY') + "</td>");
                $('#credito-detalle').modal('show');

                $('#plan_descuento_ajuste').html($("#plan_descuento").html());
                $('#plan_descuento_ajuste').val(detalle[0].id_planes_descuentos);

                $(".ajuste-descuento #monto_descuento_ajuste").val(formatNumber(detalle[0].monto_descuento));
                $(".ajuste-descuento #monto_acuerdo_ajuste").val(formatNumber(detalle[0].monto));
                $(".ajuste-descuento #old_monto_ajuste").show();
                $(".ajuste-descuento #old_monto_ajuste strong").html(formatNumber(parseFloat(detalle[0].monto) + parseFloat(detalle[0].monto_descuento)));

                $('#plan_descuento_ajuste').change('on', function (event) { aplicarDescuentoAjuste(detalle[0].fecha.substr(0, 10)) });
                $('.ajuste-descuento .btn').click('on', function (event) { guardarAjusteDescuento(id_promesa) });


            }
        })
        .fail(function () {
        })
        .always(function () {
        });
}

function guardarAjusteDescuento(id_acuerdo) {
    const formData = new FormData();
    let descuento = 0;
    let id_descuento = 0;
    let monto = parseFloat($("#monto_acuerdo_ajuste").val().split('.').join("").replace(',', '.'));

    if ($("#plan_descuento_ajuste").val() != '') {
        descuento = $("#monto_descuento_ajuste").val().split('.').join("").replace(',', '.');
        id_descuento = $("#plan_descuento_ajuste").val();
    }

    formData.append("acuerdo", id_acuerdo);
    formData.append("monto", monto);
    formData.append("monto_descuento", descuento);
    formData.append("id_plan", id_descuento);

    let comment = "<b>[AJUSTE ACUERDO DE PAGO]</b><br>Acuerdo ajustado: " + id_acuerdo + " <br>Monto: $" + formatNumber(monto);
    comment += "<br>Descuento: " + $("#plan_descuento_ajuste option:selected").text();
    comment += "<br>Monto descuento: $" + formatNumber(descuento);
    comment += "<br>Monto proyectado: $" + formatNumber($("#monto").data('monto_original'));
    comment += "<br>";


    $.ajax({
        url: base_url + 'api/credito/ajustar_descuento_promesa',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (response) {
        Swal.fire({
            title: "¡Perfecto!",
            text: response.message,
            icon: 'success'
        });
        let id_cliente = $("#planes_pago").data("id_cliente");
        consultarPromesas(id_cliente);

        let id_solicitud = $("#id_solicitud").val();
        let id_operador = $("section").find("#id_operador").val();
        let type_contact = 10;
        saveTrack(comment, type_contact, id_solicitud, id_operador);
    })
        .fail(function (response) {
            //console.log("error");
        })
        .always(function (response) {
            //console.log("complete");
        });


}

function campaniaDescuento() {
    const formData = new FormData();
    let id_cliente = $("#client").data('id_cliente');

    formData.append("fecha", $("#fechaAcuerdo").val());
    formData.append("id_cliente", id_cliente);

    $.ajax({
        url: base_url + 'api/credito/descuento_campania',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (response) {
        //console.log(response.status.ok);
        if (response.status.ok) {
            let check = "";

            if ($("#ch-descuento-campania").is(':checked')) { check = "checked"; $("#monto").val($("#ch-descuento-campania").val()); }

            $("div.descuento-campania").html('<p class="text-red">Deuda: <b>$' + formatNumber(response.data.deuda) + '</b> | Descuento Campaña: <b>$' + formatNumber(response.data.descuento) + '</b> | A Pagar por Campaña: <b>$' + formatNumber(response.data.total) + '</b>  <input type="checkbox" ' + check + ' class="form-check-input" id="ch-descuento-campania" data-deuda="' + formatNumber(response.data.deuda) + '" data-descuento="' + formatNumber(response.data.descuento) + '" value ="' + formatNumber(response.data.total) + '" name="ch-descuento-campania"></p>');

            if ($("#ch-descuento-campania").is(':checked')) { $("#monto").val($("#ch-descuento-campania").val()); }


            $("div.descuento-campania #ch-descuento-campania").click("on", function () {
                if ($(this).is(':checked')) {
                    $("#planes_pago").prop('disabled', true);
                    $("#plan_descuento").prop('disabled', true);
                    $("#monto").prop('readonly', true);
                    $("#monto_descuento").val('0');
                    $("#planes_pago").val('');
                    $("#plan_descuento").val('');
                    $("#monto").val($(this).val());

                    $('#fechaAcuerdo').daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        locale: { "format": "YYYY-MM-DD" },
                        minYear: parseInt(moment().format('YYYY'), 10),
                        maxYear: parseInt(moment().format('YYYY'), 10),
                        minDate: moment().format('YYYY-MM-DD'),
                        maxDate: moment(response.data.valido, "YYYY-MM-DD")
                    });
                    $('#fechaAcuerdo').on('apply.daterangepicker', function (ev, picker) {
                        calcularMora();
                        campaniaDescuento();
                    });
                } else {
                    $("#planes_pago").prop('disabled', false);
                    $("#plan_descuento").prop('disabled', false);
                    $("#monto").prop('readonly', false);
                    $('#fechaAcuerdo').daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        locale: { "format": "YYYY-MM-DD" },
                        minYear: parseInt(moment().format('YYYY'), 10),
                        maxYear: parseInt(moment().format('YYYY'), 10),
                        minDate: moment().format('YYYY-MM-DD'),
                        maxDate: moment(moment().format('YYYY-MM-DD'), "YYYY-MM-DD").add(3, 'days')
                    });
                    calcularMora();
                    $('#fechaAcuerdo').on('apply.daterangepicker', function (ev, picker) {
                        calcularMora();
                        campaniaDescuento();
                    });
                }
            });
            $('.row-chat-track').css('height', $('#box_client_data').height() + 'px');

        }
    })
        .fail(function (response) {
            //console.log("error");
        })
        .always(function (response) {
            //console.log("complete");
        });

}

function mostrarFormulario(tipo) {
    $('#create-agenda input[type=text]').val('');
    $('#create-agenda input[type=number]').val('');
    $('#create-agenda input[type=email]').val('');

    if (tipo == 'tel') {
        

        $.ajax({
            url: base_url + 'api/credito/get_departamentos',
            type: 'GET'
        })
            .done(function (response) {
                $('button#agendar').attr('data-tipo_formulario', tipo);
                $("#create-agenda #new-titulo").html('AGENDAR NÚMERO');
                $('#form-mail').addClass('hidden');
                $('#form-tel').removeClass('hidden');
                $('#create-agenda').modal('show');
                $("select#departamentos").html('');
                if (typeof (response.departamentos) != 'undefined') {
                    let departamentos = response.departamentos;
                    departamentos.forEach(function (departamento, indice, array) {
                        $("select#departamentos").append('<option data-id_departamento="' + departamento.nombre_departamento + '" value="' + departamento.Codigo + '" >' + departamento.nombre_departamento + '</option>')
                    });
                }
            })
            .fail(function () {
            })
            .always(function () {
            });


    } else {
        $("#create-agenda #new-titulo").html('AGENDAR CORREO');
        $('#form-mail').removeClass('hidden');
        $('#form-tel').addClass('hidden');
        $('button#agendar').attr('data-tipo_formulario', tipo);
        $('#create-agenda').modal('show');
    }

}

function mostrarFormularios(params) {
    
    $('#new-create-agenda-alone input[type=text]').val('');
    $('#new-create-agenda-alone input[type=number]').val('');
    $('#new-create-agenda-alone input[type=email]').val('');
// console.log(params.tipo)

        $.ajax({
            url: base_url + 'api/credito/get_departamentos',
            type: 'GET'
        })
            .done(function (response) {
                $('button#agendar').attr('data-tipo_formulario', params.tipo);
                $("#new-create-agenda-alone #new-titulo").html('AGENDAR NÚMERO');
                $('#form-mail').addClass('hidden');
                $('#form-tel').removeClass('hidden');
                $('#new-create-agenda-alone').modal('show');
                $("select#departamentos-new").html('');
                
                $('button#agendar_tlf_new').attr('data-id_solicitud', params.id_solicitud);
                $('button#agendar_tlf_new').attr('data-documento', params.documento);

                $('#table-agenda-telefono').DataTable().clear();
                $('#table-agenda-telefono').DataTable().destroy();
                render_tabla_agendaTlf(params.documento)
                
                if (typeof (response.departamentos) != 'undefined') {
                    let departamentos = response.departamentos;
                    departamentos.forEach(function (departamento, indice, array) {
                        $("select#departamentos-new").append('<option data-id_departamento="' + departamento.nombre_departamento + '" value="' + departamento.Codigo + '" >' + departamento.nombre_departamento + '</option>')
                    });
                }
            })
            .fail(function () {
            })
            .always(function () {
            });


   

}
function mostrarFormulariosMail(params) {
            
        $('button#agendar').attr('data-tipo_formulario', params.tipo);
        $("#new-create-agenda-mail-alone #new-titulo").html('AGENDAR CORREO');
        $('#form-mail').addClass('hidden');
        $('#form-tel').removeClass('hidden');
        $('#new-create-agenda-mail-alone').modal('show');
        $("select#departamentos-new").html('');
        
        $('button#btn_agendar_mail_new').attr('data-id_solicitud', params.id_solicitud);
        $('button#btn_agendar_mail_new').attr('data-documento', params.documento);

        $('#table-agenda-telefono').DataTable().clear();
        $('#table-agenda-telefono').DataTable().destroy();
        render_tabla_agenda_mail(params.documento)

}

function agendarTelefonoMail(id_cliente) {
    const formData = new FormData();

    if ($("#new-mail").val() == '' && $("#new-numero").val() != '') {
        formData.append("id_cliente", id_cliente);
        formData.append("numero", $("#new-numero").val());
        formData.append("fuente", $("#new-fuente").val());
        formData.append("contacto", $("#new-contacto").val());
        formData.append("id_parentesco", $("#new-parentesco").val());
        formData.append("estado", $("#new-estado").val());
        formData.append("tipo", $("#new-tipo").val());
        formData.append("municipio", $("#ciudad").val());
        formData.append("departamento", $("select#departamentos").find(':selected').data('id_departamento'));
    }
    if ($("#new-mail").val() != '' && $("#new-numero").val() == '') {
        formData.append("id_cliente", id_cliente);
        formData.append("cuenta", $("#new-mail").val());
        formData.append("contacto", $("#new-contacto-2").val());
    }

    $.ajax({
        url: base_url + 'api/credito/agendar',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
    }).done(function (response) {
        let elementos = "";
        if (response.status == '200') {
            Swal.fire({
                title: "¡Perfecto!",
                text: response.message,
                icon: 'success'
            });

            if (typeof (response.agenda_telefonica) != 'undefined' && response.agenda_telefonica != null) {
                var data = response.agenda_telefonica;
                data.forEach(function (telefono, indice, array) {
                    elementos += '<tr><td>';
                    elementos += '<div class="form-check">';
                    elementos += '<input type="checkbox" class="form-check-input" id="ch-tel-' + telefono.id + '" value ="' + telefono.id + '" name="ch-telefonos" data-numero_telefono="' + telefono.numero + '" data-estado="' + telefono.estado + '">';
                    elementos += '</div></td>';
                    elementos += '<td class="numero">';
                    if (telefono.estado_codigo == 1) {
                        elementos += telefono.codigo + '/' + telefono.numero;
                    } else {
                        elementos += telefono.numero;
                    }
                    elementos += '</td><td>';


                    if (telefono.codigo != "" && telefono.departamento != "") {
                        elementos += '<a data-registro="' + telefono.id + '" class="codigo" style="font-size: 12px;"';
                        if (telefono.estado_codigo != 1) {
                            elementos += 'onclick= "agregarLocalidadModal(this)"';
                        }

                        elementos += '>' + telefono.codigo + '/' + telefono.departamento + '</a>';

                        if (telefono.estado_codigo != 1) {
                            elementos += '<a data-title="Verificar código" data-registro="' + telefono.id + '" class="verificacion"><i class="fa fa-check-circle text-green" ></i></a>';
                        }
                    } else {
                        elementos += '<a data-registro="' + telefono.id + '" onclick= "agregarLocalidadModal(this)"><i class="fa fa-map-marker text-red"></i> Agregar</a>';

                    }
                    elementos += '</td><td>';
                    elementos += '<div class="form-group" style="margin:0px;">';
                    elementos += '<input type="text" class="form-control" id="contacto-tel-' + telefono.id + '" value="' + telefono.contacto + '">';
                    elementos += '</div></td><td>';
                    elementos += '<select class="form-control" id="fuente-tel-' + telefono.id + '">';

                    if (telefono.fuente == "PERSONAL") {
                        elementos += '<option value="PERSONAL" selected>Personal</option>';
                    } else {
                        elementos += '<option value="PERSONAL" >Personal</option>';
                    }
                    if (telefono.fuente == "REFERENCIA") {
                        elementos += '<option value="REFERENCIA" selected>Referencia</option>';
                    } else {
                        elementos += '<option value="REFERENCIA" >Referencia</option>';
                    }
                    if (telefono.fuente == "LABORAL") {
                        elementos += '<option value="LABORAL" selected>Laboral</option>';
                    } else {
                        elementos += '<option value="LABORAL" >Laboral</option>';
                    }
                    if (telefono.fuente == "BURO_CELULAR") {
                        elementos += '<option value="BURO_CELULAR" selected>Buro - Celular - D</option>';
                    } else {
                        elementos += '<option value="BURO_CELULAR" >Buro - Celular - D</option>';
                    }
                    if (telefono.fuente == "BURO_CELULAR_T") {
                        elementos += '<option value="BURO_CELULAR_T" selected>Buro - Celular - T</option>';
                    } else {
                        elementos += '<option value="BURO_CELULAR_T" >Buro - Celular - T</option>';
                    }
                    if (telefono.fuente == "BURO_LABORAL") {
                        elementos += '<option value="BURO_LABORAL" selected>Buro - Laboral - D</option>';
                    } else {
                        elementos += '<option value="BURO_LABORAL" >Buro - Laboral - D</option>';
                    }
                    if (telefono.fuente == "BURO_RESIDENCIAL") {
                        elementos += '<option value="BURO_RESIDENCIAL" selected>Buro - Laboral - D</option>';
                    } else {
                        elementos += '<option value="BURO_RESIDENCIAL" >Buro - Residencial - D</option>';
                    }

                    elementos += '</select></td><td>';
                    elementos += '<select class="form-control" id="tipo-tel-' + telefono.id + '">';
                    elementos += '<option value="" > </option>';



                    if (telefono.tipo == "MOVIL") {
                        elementos += '<option value="MOVIL" selected>Movil</option>';
                    } else {
                        elementos += '<option value="MOVIL" >Movil</option>';
                    }
                    if (telefono.tipo == "FIJO") {
                        elementos += '<option value="FIJO" selected>Fijo</option>';
                    } else {
                        elementos += '<option value="FIJO" >Fijo</option>';
                    }


                    elementos += '</select></td><td>';
                    elementos += '<select class="form-control" id="parentesco-tel-' + telefono.id + '">';
                    elementos += '<option value="0" > </option>';

                    let parentesco = response.lista_parentesco;
                    parentesco.forEach(function (paren, indice, array) {

                        if (telefono.parentesco == paren.Nombre_Parentesco) {
                            elementos += '<option value ="' + paren.id_parentesco + '" selected >' + paren.Nombre_Parentesco + '</option>';
                        } else {
                            elementos += '<option value ="' + paren.id_parentesco + '">' + paren.Nombre_Parentesco + '</option>';
                        }

                    });
                    elementos += '</select></td><td>';
                    elementos += '<select class="form-control" id="estado-tel-' + telefono.id + '">';


                    if (telefono.estado == "1") {
                        elementos += '<option value="1" selected>Activo</option>';
                    } else {
                        elementos += '<option value="1" >Activo</option>';
                    }
                    if (telefono.estado == "0") {
                        elementos += '<option value="0" selected>Fuera de servicio</option>';
                    } else {
                        elementos += '<option value="0" >Fuera de servicio</option>';
                    }

                    elementos += ' </select></td>';
                    elementos += '<td><a class="btn btn-info btn-sm" onclick="guardarCambio(' + telefono.id + ', \'tel\')"><i class="fa fa-save"></i></a></td></tr>';

                });
                $("#table-agenda-telefono tbody").html(elementos);
                $("a.verificacion").click(function (event) {
                    verificarCodigo($(this));
                });
            }
            if (typeof (response.agenda_mail) != 'undefined' && response.agenda_mail != null) {
                var data = response.agenda_mail;
                data.forEach(function (mail, indice, array) {
                    elementos += '<tr><td><div class="form-check">';
                    elementos += '<input type="checkbox" class="form-check-input" id="ch-mail-' + mail.id + ' value="' + mail.id + '">';
                    elementos += '<label class="form-check-label" for="ch-mail-' + mail.id + '"></label></div></td>';
                    elementos += '<td>' + mail.cuenta + '</td><td>';
                    elementos += '<select class="form-control" id="estado-mail-' + mail.id + '">';
                    if (mail.estado == 1) {
                        elementos += '<option value="1" selected >Activo</option>';
                    } else {
                        elementos += '<option value="1" >Activo</option>';
                    }
                    if (mail.estado == 0) {
                        elementos += '<option value="0" selected >Fuera de servicio</option>';
                    } else {
                        elementos += '<option value="0" >Fuera de servicio</option>';
                    }
                    elementos += '</select></td><td><a class="btn btn-info btn-sm" onclick="guardarCambio(' + mail.id + ', \'mail\')"><i class="fa fa-save"></i></a></td></tr>';
                });
                $("#table-agenda-mail tbody").html(elementos);
            }


        } else {
            Swal.fire("¡Ups!",response.message,'error');
        }
        $('#create-agenda').modal('hide');
    })
        .fail(function (response) {
            //console.log("error");
        })
        .always(function (response) {
            //console.log("complete");
        });
}

function guardarCambio(id, agenda) {
    const formData = new FormData();

    if (agenda == 'tel') {
        formData.append("id", id);
        formData.append("fuente", $("#fuente-tel-" + id).val());
        formData.append("contacto", $("#contacto-tel-" + id).val());
        formData.append("id_parentesco", $("#parentesco-tel-" + id).val());
        formData.append("estado", $("#estado-tel-" + id).val());
        formData.append("tipo", $("#tipo-tel-" + id).val());
        if ($("select#departamentos-modal").val() != null) {
            formData.append("departamento", $("select#departamentos-modal").find(':selected').data('id_departamento'));
            formData.append("ciudad", $("select#ciudad-modal").val());
            $("select#departamentos-modal").html('');
        }
    }
    if (agenda == 'mail') {
        formData.append("estado", $("#estado-mail-" + id).val());
        formData.append("id", id);
    }
    formData.append("agenda", agenda);
    Swal.fire({
        title: '¡Atención!',
        text: '¿Estas seguro de que quieres modificar la agenda?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {

            $.ajax({
                url: base_url + 'api/credito/actualizarAgenda',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {
                if (response.status == '200') {
                    Swal.fire({
                        title: "¡Perfecto!",
                        text: response.message,
                        icon: 'success'
                    });
                    if (response.departamento != "" && response.codigo != "") {
                        let cadena = '<a data-registro="' + id + '" class="codigo" style="font-size: 12px;" onclick= "agregarLocalidadModal(this)">' + response.codigo + '/' + response.departamento + '</a><a data-title="Verificar código" data-registro="' + id + '" class="verificacion"><i class="fa fa-check-circle text-green" ></i></a>'
                        $("a[data-registro=" + id + "]").closest('td').html(cadena);

                        $("a.verificacion").click(function (event) {
                            verificarCodigo($(this));
                        });
                    }
                } else {
                    Swal.fire({
                        title: "¡Ups!",
                        text: response.message.contacto,
                        icon: 'error'
                    });
                }

            })
                .fail(function (response) {
                    //console.log(response);
                })
                .always(function (response) {
                    //console.log("complete");
                });

        }
    });

}

function cargarPlan(elemento) {
    if ($(elemento).val() != "") {
        //removemos los descuentos aplicables
        $("#plan_descuento").val('');
        $("#plan_descuento").prop('disabled', 'disabled');
        $("#monto_descuento").val(formatNumber(0));
        $("#old_monto").hide();


        $("#monto").attr('disabled', true);
        $("#fechaAcuerdo").attr("disabled", true);

        let base_url = $("#base_url").val();
        let id_cliente = $("#planes_pago").data("id_cliente");
        let startdate = moment().format('DD-MM-YYYY');

        const formData = new FormData();

        formData.append("id_cliente", id_cliente);
        formData.append("plan", $("#planes_pago").val());
        if ($(elemento).data('fecha') == true) {
            startdate = $(elemento).val();
            //console.log(startdate);
            formData.append("fecha", startdate);
        } else {
            $(".detalle-plan").html("");
        }

        $.ajax({
            url: base_url + 'api/credito/detallePlanPago',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false
        })
            .done(function (response) {
                if (typeof (response.data.plan_detalle) != 'undefined') {
                    let detalle = response.data.plan_detalle
                    $("#monto").val(formatNumber(response.data.deuda));
                    if ($(elemento).data('fecha') != true) {
                        $(".detalle-plan").append('<hr style="border-top: 1px solid #d2d6de;"></hr><h5 data-plan="' + response.data.plan_detalle[0].plan + '"><b>Detalle Plan ' + response.data.plan_detalle[0].plan + '</b></h5><table class="table modificable"><thead> <tr><th scope="col">Cuota</th><th scope="col">Porcentaje</th><th scope="col">Fecha</th><th scope="col">Monto</th></tr></thead><tbody></tbody></table>');
                    }

                    detalle.forEach(function (cuota, indice, array) {
                        if ($(elemento).data('fecha') != true) {
                            $(".detalle-plan tbody").append('<tr data-id_detalle_plan="' + cuota.id + '"><td>' + cuota.numero_cuota + '</td><td><input type="text" class="form-control" data-id_detalle_plan="' + cuota.id + '" id="porcentaje-' + cuota.id + '" readonly value="' + cuota.porcentaje + '%"></td><td><input type="text" data-campo="fecha" data-fecha ="true" data-id_detalle_plan="' + cuota.id + '" class="form-control" id="fecha-acuerdo-' + cuota.id + '"></td><td><input type="text" class="form-control moneda" data-campo="monto" data-pasar=true data-id_detalle_plan="' + cuota.id + '" id="monto-cuota-' + cuota.id + '" readonly value="' + formatNumber(response.data.deuda * cuota.porcentaje / 100) + '"><input type="text" class="form-control hidden" data-campo="monto" data-pasar=true data-id_detalle_plan="' + cuota.id + '" id="h-monto-cuota-' + cuota.id + '" readonly value="' + response.data.deuda * cuota.porcentaje / 100 + '"></td></tr>');

                            let maxdate = moment(startdate, "DD-MM-YYYY").add(cuota.extension_dias, 'days');
                            $('#fecha-acuerdo-' + cuota.id).daterangepicker({
                                singleDatePicker: true,
                                showDropdowns: true,
                                locale: { "format": "DD-MM-YYYY" },
                                minYear: parseInt(moment().format('YYYY'), 10),
                                maxYear: parseInt(moment().format('YYYY'), 10),
                                minDate: moment().format('DD-MM-YYYY'),
                                maxDate: maxdate,
                                startDate: maxdate
                            });

                            if (indice == detalle.length - 1)
                                $("#fecha-acuerdo-" + cuota.id).attr("onchange", "cargarPlan(this)");
                        } else {
                            $("#monto-cuota-" + cuota.id).val(formatNumber(response.data.deuda * cuota.porcentaje / 100));
                            $("#h-monto-cuota-" + cuota.id).val(response.data.deuda * cuota.porcentaje / 100);
                        }
                    });

                    $('.row-chat-track').css('height', $('#box_client_data').height() + 'px');
                    //$('#box_tracker').css('height', $('#box_client_data').height()-20+'px');

                    $('.moneda').autoNumeric('init', { aSep: '.', aDec: ',', aSign: '' });
                } else {
                    Swal.fire({
                        title: "Ups!",
                        text: response.message,
                        icon: 'success'
                    });
                }
            })
            .fail(function (response) {
                console.log(response);

            })
            .always(function () {
            });

    } else {

        $("#plan_descuento").prop('disabled', false);

        calcularMora();

        $("#monto").attr('disabled', false);
        $("#fechaAcuerdo").attr("disabled", false);
        $(".detalle-plan").html("");
        $('.row-chat-track').css('height', $('#box_client_data').height() + 'px');
        //$('#box_tracker').css('height', $('#box_client_data').height()-20+'px');

    }


}

function verificarCodigo(element) {
    const formData = new FormData();
    formData.append("id", $(element).data('registro'));
    formData.append("verificar", '1');
    Swal.fire({
        title: '¡Atención!',
        text: '¿Estas seguro de que el codigo especificado es el correcto?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {

            $.ajax({
                url: base_url + 'api/credito/actualizarAgenda',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {
                if (response.status == '200') {
                    Swal.fire({
                        title: "¡Perfecto!",
                        text: response.message,
                        icon: 'success'
                    });

                    let cadena = $(element).closest('tr').find('.codigo').text();
                    let codigo = cadena.split('/')[0].trim();
                    let numero = $(element).closest('tr').find('.numero').text().trim();
                    $(element).closest('tr').find('.numero').html(codigo + '/' + numero);



                    $(element).closest('tr').find('.codigo').prop("onclick", null);

                    $(element).remove();


                } else {
                    Swal.fire({
                        title: "¡Ups!",
                        text: response.message,
                        icon: 'error'
                    });
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
function agregarLocalidadModal(element) {
    $("#localidad-modal .modal-footer").html('<button type="button" class="btn btn-default" data-dismiss="modal" onclick="$(\'#departamentos-modal\').html(\'\')">Cancelar</button><button type="button" class="btn btn-success" data-dismiss="modal" onclick="guardarCambio(\'' + $(element).data('registro') + '\', \'tel\')">Aceptar</button>');
    $.ajax({
        url: base_url + 'api/credito/get_departamentos',
        type: 'GET'
    })
        .done(function (response) {
            $("select#departamentos-modal").html('');
            if (typeof (response.departamentos) != 'undefined') {
                let departamentos = response.departamentos;
                departamentos.forEach(function (departamento, indice, array) {
                    $("select#departamentos-modal").append('<option data-id_departamento="' + departamento.nombre_departamento + '" value="' + departamento.Codigo + '" >' + departamento.nombre_departamento + '</option>')
                });
            }
            $('#localidad-modal').modal('show');

        })
        .fail(function () {
        })
        .always(function () {
        });
}

function cargarInfoLaboral() {
    $("#table-info_laboral tbody").html('');
    let cadena = '<tr><td colspan="8" style="text-align:center"><b>No se encontraron los datos del cliente</b></td></tr>';
    $.ajax({
        url: base_url + 'api/credito/situacion_laboral/' + $("#box_client_title").data("id_cliente"),
        type: 'GET'
    })
        .done(function (response) {
            if (typeof (response.data) != 'undefined') {
                info = response.data;

                if (info.length > 0) {
                    cadena = "";
                    var fecha1 = moment(info[0].fecha_registro);
                    var fecha2 = moment();
                    var diff = fecha2.diff(fecha1, 'days');
                    if (diff < 31) {
                        $("a.refresh").remove();
                    }
                }

                info.forEach(element => {

                    cadena += '<tr>';
                    cadena += '<td>' + element.tipoIdentificacionAportanteId + '-' + element.numeroIdentificacionAportante + '-' + element.tipoCotizantePersonaNatural + '</td>';
                    cadena += '<td>' + element.razonSocialAportante + '</td>';
                    cadena += '<td>' + (element.tiene_salario_integral_actualmente == 1 ? 'Si' : 'No') + '</td>';
                    cadena += '<td>' + element.mesPeriodoValidado + '/' + element.anoPeriodoValidado + '</td>';
                    cadena += '<td>' + (element.realizoPago == 1 ? 'Si' : 'No') + '</td>';
                    cadena += '<td>$' + formatNumber(element.ingresos) + '</td>';
                    cadena += '<td>$' + formatNumber(element.promedioIngreso) + '</td>';
                    cadena += '<td>$' + formatNumber(element.mediasIngreso) + '</td>';
                    cadena += '<td>' + moment(element.fecha_registro).format('DD-MM-YYYY') + '</td>';
                    cadena += '</tr>';
                });

            }
            $("#table-info_laboral tbody").html(cadena);

        })
        .fail(function () {
        })
        .always(function () {
        });
}

function refreshInfoLaboral() {

    $.ajax({
        url: base_url + 'api/credito/actualizar_situacion_laboral/' + $("#id_solicitud").val(), 
        type: 'GET'
    })
        .done(function (response) {
            //console.log(response);
            if (response.status.ok) {
                Swal.fire("¡Excelente!",response.mensaje,'success');
                cargarInfoLaboral();
                $("a.refresh").remove();
            } else {
                Swal.fire({
                    title: "¡Ups!",
                    text: response.message,
                    icon: 'error'
                });
            }

        })
        .fail(function () {
        })
        .always(function () {
        });
}

function getGestionesDesempeño() {
    let base_url = $("#base_url").val();
    let fecha = $('#date_range').val();

    if (fecha != '' && typeof (fecha) != 'undefined') {
        $.ajax({
            url: base_url + 'api/credito/get_desempenho_operador/' + fecha,
            type: 'GET',
        })
            .done(function (response) {

                if (response.status.ok) {
                    $('#gestiones').html(response.data.gestion);

                    if (response.data.acuerdos.cantidad_acuerdos == 0) {
                        $('#acuerdos').html(response.data.acuerdos.cantidad_acuerdos + ' <br><i class="fa fa-arrow-right"></i> $0');
                    } else {
                        $('#acuerdos').html(response.data.acuerdos.cantidad_acuerdos + ' <br><i class="fa fa-arrow-right"></i> ' + formatNumber(response.data.acuerdos.suma_acuerdos));
                    }

                    if (response.data.acuerdos_cumplidos.cantidad_acuerdos == 0) {
                        $('#cumplidos').html(response.data.acuerdos_cumplidos.cantidad_acuerdos + ' <br><i class="fa fa-arrow-right"></i> $0');
                    } else {
                        $('#cumplidos').html(response.data.acuerdos_cumplidos.cantidad_acuerdos + ' <br><i class="fa fa-arrow-right"></i> ' + formatNumber(response.data.acuerdos_cumplidos.suma_acuerdos));
                    }

                    if (response.data.acuerdos_incumplidos.cantidad_acuerdos == 0) {
                        $('#incumplidos').html(response.data.acuerdos_incumplidos.cantidad_acuerdos + ' <br><i class="fa fa-arrow-right"></i> $0');
                    } else {
                        $('#incumplidos').html(response.data.acuerdos_incumplidos.cantidad_acuerdos + ' <br><i class="fa fa-arrow-right"></i> ' + formatNumber(response.data.acuerdos_incumplidos.suma_acuerdos));
                    }
                }

            })
            .fail(function (response) {
                //console.log("error");
            })
            .always(function (response) {
                //console.log("complete");
            });
    }


}


function aplicarDescuentoAjuste(fecha) {
    //seleccionamos todos los creditos y cuotas     
    $('#tabla-datos-mora input[name=ch-creditos]').not("[disabled]").prop('checked', true);

    const formData = new FormData();
    var monto = 0;
    var old_monto = 0;
    var descuento_monto = 0;
    var creditos = [];

    $.each($("input[name='ch-creditos']:checked"), function () {
        creditos.push($(this).data('id_cuota'));
    });

    var aux = removeDuplicates(creditos);
    $.each(aux, function (index, value) {
        if (creditos.length > 0) {
            formData.append("fecha", moment(fecha).format('YYYY-MM-DD'));
            formData.append("cuota", value);
            formData.append("id_descuento", $("#plan_descuento_ajuste").val());

            $.ajax({
                url: base_url + 'api/credito/recalculardeuda',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {

                monto += parseFloat(response.total_a_pagar);

                if (typeof (response.descuento) != 'undefined') {
                    descuento_monto += parseFloat(response.descuento.monto_descuento);
                    $("#monto_descuento_ajuste").val(formatNumber(descuento_monto));
                    $("#old_monto_ajuste").show();
                    $("#old_monto_ajuste strong").html(formatNumber(monto));

                } else {
                    $("#monto_descuento_ajuste").val(formatNumber(0));
                    $("#old_monto_ajuste").hide();
                }


                $("#monto_acuerdo_ajuste").val(formatNumber(monto - descuento_monto));
                $("#monto").data('monto_original', monto);

            })
                .fail(function (response) {
                    //console.log("error");
                })
                .always(function (response) {
                    //console.log("complete");
                });
        } else {
            alert("Debes seleccionar el credito para el cual se generara la nueva promesa");
        }
    });

}

function aplicarDescuento() {
    if ($("#plan_descuento").val() != '') {
        //seleccionamos todos los creditos y cuotas     
        $('#tabla-datos-mora input[name=ch-creditos]').not("[disabled]").prop('checked', true);
        // solo lectura el campo monto
        $("#monto").attr('readonly', true);
        $("#planes_pago").val('');
        cargarPlan($("#planes_pago"));
        $("#planes_pago").prop('disabled', 'disabled');

    } else {
        $("#planes_pago").prop('disabled', false);
        $("#monto_descuento").val(formatNumber(0));
        $("#old_monto").hide();
        //liberamos el campo monto
        $("#monto").attr('readonly', false);
        calcularMora();
    }

}

function calcularMora() {
    const formData = new FormData();
    var monto = 0;
    var descuento = 0;
    var creditos = [];
    if (!$("#ch-descuento-campania").is(':checked')) {
        $("#monto").val(0);
        $("#monto").data('creditos', '');
        $("#monto").data('monto_original', 0);
        $("#tabla-detalle-pago tbody").html('');
        $.each($("input[name='ch-creditos']:checked"), function () {
            creditos.push($(this).data('id_cuota'));
        });

        var aux = removeDuplicates(creditos);
        $.each(aux, function (index, value) {
            if (creditos.length > 0) {
                formData.append("fecha", $("#fechaAcuerdo").val());
                formData.append("cuota", value);
                formData.append("id_descuento", $("#plan_descuento").val());
                $.ajax({
                    url: base_url + 'api/credito/recalculardeuda',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                }).done(function (response) {

                    monto += parseFloat(response.total_a_pagar);

                    if (typeof (response.descuento) != 'undefined') {
                        descuento += parseFloat(response.descuento.monto_descuento);

                        
                        $("#monto_descuento").val(formatNumber(descuento));
                        $("#old_monto").show();
                        $("#old_monto strong").html(formatNumber(monto));

                    }

                    $("#monto").val(formatNumber(monto - descuento));
                    $("#monto").data('monto_original', monto);
                    $("#monto").data('creditos', creditos.toString());


                })
                    .fail(function (response) {
                        //console.log("error");
                    })
                    .always(function (response) {
                        //console.log("complete");
                    });
            } else {
                alert("Debes seleccionar el credito para el cual se generara la nueva promesa");
            }
        });
    }
}

function getGestionesConsultor(){
    let fecha = $("#slc-periodo").val();
    let consultor = $("#slc-operadores").val();

    $("#slc-periodo").prop('disabled',true);
    $("#consultar-gestion").addClass('disabled');
    $("#consultar-gestion").html('Consultando...');
    if (fecha != '' && typeof (fecha) != 'undefined') {
        $.ajax({
            url: base_url + 'api/credito/get_gestiones_consultor/' + fecha +'/'+consultor,
            type: 'GET',
        })
            .done(function (response) {

                if (response.status.ok) {
                    $('p.vendidos-empleados').html(response.vendidos_empleados);
                    $('p.mora-empleados').html(response.mora_empleados);
                    $('p.porcent-empleados').html(response.porcent_empleados);
                    $('p.vendidos-independientes').html(response.vendidos_independientes);
                    $('p.mora-independientes').html(response.mora_independientes);
                    $('p.porcent-independientes').html(response.porcent_independientes);
                    $('p.mora-periodo').html(response.mora_periodo+'%');

                    
                }
                $("#consultar-gestion").removeClass('disabled');
                $("#consultar-gestion").html('APLICAR');
                $("#slc-periodo").prop('disabled',false);


            })
            .fail(function (response) {
                $("#consultar-gestion").removeClass('disabled');
                $("#consultar-gestion").html('APLICAR');
                $("#slc-periodo").prop('disabled',false);

            });
    }
}

function validaNumericos(event) {
    if ((event.charCode >= 48 && event.charCode <= 57) || event.charCode == 44) {
        return true;
    }
    return false;
}

function removeDuplicates(array) {
    return array.filter((a, b) => array.indexOf(a) === b)
}


function enviar_salvo(id_credito){
    const formData = new FormData();
    formData.append("id_credito", id_credito);
    $.ajax({
        url: URL_WEBAPP+'Maestro/getPazYsalvo',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function(xhr) {
            xhr.setRequestHeader ("X-Requested-With", 'XMLHttpRequest');
            xhr.setRequestHeader ("Authorization", '14e2d009229757497dcba61f08344fdeef5b6f02');
        }
    })
    .done(function (response) {
            Swal.fire("Exito!", 'Se envio Paz y Salvo a correo', 'success');
            
            let id_solicitud = $("#id_solicitud").val();
            let id_operador = $("section").find("#id_operador").val();
            let type_contact = 6;
            let comment = "<b>[ENVIO MAIL]</b>" + "<br><b>Se envio mail Paz y Salvo  </b>";
            saveTrack(comment, type_contact, id_solicitud, id_operador);
        })
        .fail(function (response) {
        Swal.fire("Ups!",'No se pudo enviar Paz y Salvo','error');

        })
        .always(function () {

        });
}

function descargar_salvo(id_credito){

    const formData = new FormData();
    formData.append("id_credito", id_credito);

    $.ajax({
        url: URL_WEBAPP+'Maestro/generarPazYsalvo',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function(xhr) {
            xhr.setRequestHeader ("X-Requested-With", 'XMLHttpRequest');
            xhr.setRequestHeader ("Authorization", '14e2d009229757497dcba61f08344fdeef5b6f02');
        }
    })
        .done(function (response) {
            if(response.data.status=='success'){
            window.open(response.data.url, "Paz y Salvo")
            }else{
            Swal.fire("Upps!",response.data.status,'error');
            }
            if(response.status==400){
            Swal.fire("Upps!",response.data,'error');
            }
        })
        .fail(function (response) {
        Swal.fire("Ups!",'No se ha podido generar Paz y Salvo','error');

        })
        .always(function () {

        });
}

function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

/**
 * ======================================
 * FUNCTION activar_campania_manual
 * DEPRECADA EL 05/11/2021 USAR
 * @see activarCampaniaManual
 * en su lugar
 * ======================================
 **/

/**
 * Obtiene el detalle de los casos asignados para mostrarlos en el datatable
 */
function getCasosAsignadosDetallados() {
	let base_url = $("#base_url").val();
	$.ajax({
		url: base_url + 'api/campanias/getCasosAsignadosDetallados',
		type: 'POST',
	}).done(function(response) {
		$('#asignados').dataTable().fnDestroy();
		$('#asignados').DataTable({
			data: response.data,
			columnDefs: [
				{ targets: '_all', className: "text-left"},
				{ targets: 4, width: "20%"},
				{ targets: 4, width: "5%"}
			],
			columns: [
				{
					data: null,
					render: function (data, type, row, meta) {
						return '<a href="#" class="btn btn-xs btn-primary credito" title="Consultar" onclick="abrirCaso('+data.id+')"><i class="fa fa-cogs"></i></a>';
					}
				},{
					data: "ultima_actividad",
					render: function(data, type, row, meta){
						return data
					}
				},{
					data: "id",
					render: function(data, type, row, meta){
						return data
					}
				},
				{
					data: "documento",
					render: function(data, type, row, meta){
						return data
					}
				},
				{
					data: null,
					render: function (data, type, row, meta) {
						return data.apellidos +' '+data.nombres;

					}
				},
				{
					data: "monto_prestado",
					render: function(data, type, row, meta){
						return data
					}
				},
				{
					data: "fecha_vencimiento",
					render: function(data, type, row, meta){
						return data
					}
				},
				{
					data: "deuda",
					render: function(data, type, row, meta){
						return data
					}
				},
				{
					data: "estado",
					render: function(data, type, row, meta){
						return data
					}
				},
				{
					data: "last_track",
					render: function(data, type, row, meta){
						return data
					}
				},
			]
		});
	});
}

/**
 * Saca al operador de la campania. El proceso incluye:
 * - Desasignar la campania
 * - Quitar los casos ( creditos ) asignados 
 * - Tracker el cambio de estado a inactivo
 * 
 * @param callback
 */
function salirCampaniaManual(callback) {
	let base_url = $("#base_url").val();
	$.ajax({
		url: base_url + 'api/campanias/salirCampania',
		type: 'POST',
		data: {"id_operador": $('#id_operador').val()},
	})
		.done(function (response) {
			activarServerSideEvent();
			callback();
		})
		.fail(function (response) {
			Swal.fire({
				title: "¡Ups!",
				text: 'Ocurrio un error',
				icon: 'error'
			});
		})
}
function descanso_campania_manual(callback){
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/campanias/cambiarOperadorADescanso',
        type: 'POST',
        data: { 'id_operador': $('#id_operador').val() },
    })
        .done(function (response) {
            if (response.status.ok) {
                $('#gestionar_operador').addClass('hide');
                $('#buscar_acuerdos_periodos').prop('disabled', false);
                $('#buscar_acuerdos').addClass("disabled");
                var table_search = $('#table_search').DataTable();
                table_search.clear().draw();
                table_search.destroy();
                var table = $('#asignados').DataTable();
                table.clear().draw();
                table.destroy();
                $('#descanso_campania').addClass('hide');
                $('#reactivar_campania').removeClass('hide');
                $('#result_table').addClass('hide');
								callback();
            }
        })
        .fail(function (response) {
            Swal.fire({
                title: "¡Ups!",
                text: 'Ocurrio un error',
                icon: 'error'
            });
        })
        .always(function () {

        });
}

function reactivarCampaniaManual() {
	var table_search = $('#table_search').DataTable();
	table_search.clear().draw();
	table_search.destroy();
	var table = $('#asignados').DataTable();
	table.clear().draw();
	table.destroy();
	let base_url = $("#base_url").val();

	$.ajax({
		url: base_url + 'api/campanias/reactivarOperador',
		type: 'POST',
		data: {"id_operador": $('#id_operador').val()},
	})
		.done(function (response) {
			location.reload();
		})
		.fail(function (response) {
			Swal.fire({
				title: "¡Ups!",
				text: 'Ocurrio un error',
				icon: 'error'
			});
		})
}

function buscar_acuerdos_crm(id=0){
    if(id == 0){
        var id_operador = $('#id_operador').val()
    }else{
        var id_operador = id;
    }
    var acuerdos = $('#buscar_acuerdos_periodos').val();
    let base_url = $("#base_url").val();

    $.ajax({
        url: base_url + 'api/credito/buscar_acuerdo',
        type: 'POST',
        data: {'id_operador': id_operador, 'acuerdo':acuerdos},
    }).done(function (response) {
        $('#table_search').dataTable().fnDestroy();
        $('#table_search').DataTable({
            data: response.data,
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return '<a href="#" class="btn btn-xs btn-primary credito" title="Gestionar" onclick="consultar_credito('+data.id+')"><i class="fa fa-cogs"></i></a>';    
                    }
                },
                {
                    data: "ultima_actividad",
                    render: function(data, type, row, meta){
                        return data
                    }
                },{
                    data: "id",
                    render: function(data, type, row, meta){
                        return data
                    }
                },
                {
                    data: "documento",
                    render: function(data, type, row, meta){
                        return data
                    }
                },
                {
                    data: null,
                        render: function (data, type, row, meta) {
                            return data.apellidos +' '+data.nombres;
                        
                        }
                },
                {
                    data: "monto_prestado",
                        render: function(data, type, row, meta){
                        return data
                    }
                },
                {
                    data: "fecha_acuerdo",
                    render: function(data, type, row, meta){
                        return data
                    }
                },
                {
                    data: "monto_acuerdo",
                    render: function(data, type, row, meta){
                        return data
                    }
                },
                {
                    data: "estado",
                    render: function(data, type, row, meta){
                        return data
                    }
                },
                {
                    data: "last_track",
                    render: function(data, type, row, meta){
                        return data
                    }
                },
                
            ]
        });  
    })
    .fail(function () {
    })
    .always(function () {
    });
}

function separador_miles(num){
    num += '';
    var x = num.split('.');
    var x1 = x[0];
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1
}
function consultar_tablero_acuerdos(id_operador, tipo){
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/gestiones_acuerdos',
        type: 'POST',
        data: {'id_operador': id_operador, 'tipo':tipo},
    }).done(function (response) {
        if(response.status.ok){ 
            document.getElementById('url_excel').setAttribute('href', base_url +'api/excel/'+response.desde+'/'+response.hasta+'/'+id_operador);
            document.getElementById('acuerdos_alcanzados_anterior').innerHTML = ( response.data[0]['acuerdos_alcanzados_anterior'] == null)? '':response.data[0]['acuerdos_alcanzados_anterior'];
            document.getElementById('acuerdos_cumplidos_anterior').innerHTML =  ( response.data[0]['acuerdos_cumplidos_anterior']== null)? '':response.data[0]['acuerdos_cumplidos_anterior'];
            document.getElementById('suma_acuerdos_quincena_anterior_0_40').innerHTML = (response.data[0]['suma_acuerdos_quincena_anterior_0_40'] == null)? '':separador_miles(response.data[0]['suma_acuerdos_quincena_anterior_0_40']);
            document.getElementById('suma_acuerdos_quincena_anterior_41_90').innerHTML = (response.data[0]['suma_acuerdos_quincena_anterior_41_90'] == null)? '':separador_miles(response.data[0]['suma_acuerdos_quincena_anterior_41_90']);
            document.getElementById('suma_acuerdos_quincena_anterior_91_120').innerHTML = (response.data[0]['suma_acuerdos_quincena_anterior_91_120'] == null)? '':separador_miles(response.data[0]['suma_acuerdos_quincena_anterior_91_120']);
            //document.getElementById('suma_acuerdos_quincena_anterior_120').innerHTML = separador_miles(response.data[0]['suma_acuerdos_quincena_anterior_120']);
            
            if(response.data[0]['suma_acuerdos_quincena_anterior_0_40']  < 1){
                document.getElementById('suma_acuerdos_quincena_anterior_0_40').innerHTML = '&nbsp;';
            } 
            if(response.data[0]['suma_acuerdos_quincena_anterior_41_90']  < 1){
                document.getElementById('suma_acuerdos_quincena_anterior_41_90').innerHTML = '&nbsp;';
            }
            if(response.data[0]['suma_acuerdos_quincena_anterior_91_120']  < 1){
                document.getElementById('suma_acuerdos_quincena_anterior_91_120').innerHTML = '&nbsp;';
            }
            /*if(response.data[0]['suma_acuerdos_quincena_anterior_120']  < 1){
                document.getElementById('suma_acuerdos_quincena_anterior_120').innerHTML = '&nbsp;';
            }*/
            var acuerdos_0_40 = response.data[0]['suma_acuerdos_quincena_anterior_0_40'] * 1 / 100;
            var mora_41_90 = response.data[0]['suma_acuerdos_quincena_anterior_41_90'] * 3 / 100;
            var mora_91_120 = response.data[0]['suma_acuerdos_quincena_anterior_91_120'] * 8 / 100;
            //var mora_120 = response.data[0]['suma_acuerdos_quincena_anterior_120'] * 7 / 100;

            var anterior_2_45 = 7000000;
            var anterior_46_120 = 3000000;
            var anterior_mayor_120 = 2000000;
            if($('#tipo_equipo').val() == 'ARGENTINA'){
                var acuerdos_0_40 = acuerdos_0_40 / 3780 * 195;
                var mora_41_90 = mora_41_90 / 3780 * 195;
                var mora_91_120 = mora_91_120 / 3780 * 195;
                //var mora_120 = mora_120 / 3780 * 195;
                anterior_2_45 = 5500000;
                anterior_46_120 = 2250000;
                anterior_mayor_120 = 1500000;
            }
            
            if(response.data[0]['suma_acuerdos_quincena_anterior_0_40'] > anterior_2_45){
                $('#mora-13-60').removeClass('panel-red');
                $('#mora-13-60').addClass('panel-green');
                document.getElementById('acuerdos_0_40').innerHTML =  separador_miles(acuerdos_0_40)
                validacion_0_40 = true
            }else{
                $('#mora-13-60').removeClass('panel-green');
                $('#mora-13-60').addClass('panel-red');
                document.getElementById('acuerdos_0_40').innerHTML = '&nbsp;';
                validacion_0_40 = false
            }
            if(response.data[0]['suma_acuerdos_quincena_anterior_41_90'] > anterior_46_120){
                $('#mora-61-120').removeClass('panel-red');
                $('#mora-61-120').addClass('panel-green');
                document.getElementById('acuerdos_61_120').innerHTML = separador_miles(mora_41_90);
                validacion_41_90 = true
            }else{
                $('#mora-61-120').removeClass('panel-green');
                $('#mora-61-120').addClass('panel-red');
                document.getElementById('acuerdos_61_120').innerHTML = '&nbsp;';
                validacion_41_90 = false
            }
            console.log(response.data[0]['suma_acuerdos_quincena_anterior_91_120'])
            if(response.data[0]['suma_acuerdos_quincena_anterior_91_120'] >  anterior_mayor_120){
                $('#mora-121-180').removeClass('panel-red');
                $('#mora-121-180').addClass('panel-green');
                document.getElementById('acuerdos_121_180').innerHTML = separador_miles(mora_91_120);
                validacion_91_120 = true
            }else{
                $('#mora-121-180').removeClass('panel-green');
                $('#mora-121-180').addClass('panel-red');
                document.getElementById('acuerdos_121_180').innerHTML = '&nbsp;';
                validacion_91_120 = false
            }
            /*if(response.data[0]['suma_acuerdos_quincena_anterior_120'] > 1500000){
                $('#mora-180').removeClass('panel-red');
                $('#mora-180').addClass('panel-green');
                document.getElementById('acuerdos_180').innerHTML = separador_miles(mora_120);
                validacion_120 = true

            }else{
                $('#mora-180').removeClass('panel-green');
                $('#mora-180').addClass('panel-red');
                document.getElementById('acuerdos_180').innerHTML = '&nbsp;';
                validacion_120 = false
            }*/
            if(validacion_0_40 == true && validacion_41_90 == true && validacion_91_120 == true /*&& validacion_120 == true*/){
                var suma = separador_miles(acuerdos_0_40 + mora_41_90 + mora_91_120 /*+mora_120*/);

                document.getElementById('suma_comision').innerHTML = suma;
            }else{
                document.getElementById('suma_comision').innerHTML = '';
            }

        }
        
    })
    .fail(function () {
    })
    .always(function () {
    });
}

function actualizar_fecha_vencimiento(id_credito, btn){
    Swal.fire({
        title: '¡Atención!',
        text: 'Se Actualizara la fecha de vencimiento de la solicitud actual.',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: base_url+'api/credito/updatefechavencimiento',
                type: 'POST',
                dataType: 'json',
                data: {id_credito: id_credito, newfecha : $(btn).parent().parent().find('input').val()},
            })
            .done(function(response) {
                Swal.fire({
                    title: "¡Perfecto!",
                    text: "Actualizado Correctamente",
                    icon: 'success'
                });
                let id_solicitud = $("#id_solicitud").val();
                let id_operador = $("section").find("#id_operador").val();
                let type_contact = 10;
                let comment = "<b>[AJUSTE VENCIMIENTO CREDITO]</b>" +
                    "<br> Fecha Anterior : " + $(btn).parent().parent().find('button').data('fecha_a') +
                    "<br> Fecha Actual   : " + $(btn).parent().parent().find('input').val();
                saveTrack(comment, type_contact, id_solicitud, id_operador);
                consultar_credito(id_credito)
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
        }
    });
}

function activarServerSideEvent() {
	var channel = pusher.subscribe('channel-operador-'+$('#id_operador').val());
	channels.push(channel);

	channel.bind('activacion-campania-component', function(data) {
		location.reload();
	});
}

