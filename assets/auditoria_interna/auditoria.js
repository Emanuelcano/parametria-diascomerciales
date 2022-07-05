var _mSelect2 = $(".select2").select2();

$(document).ready(function () {
    base_url = $("input#base_url").val();

});

function limpiarFormulario() {
    $('.select2').val(null).trigger("change");
    document.getElementById("formDatosBasicos").reset();
}

function formatDate (input) {
    var datePart = input.match(/\d+/g),
    year = datePart[0],
    month = datePart[1], day = datePart[2];
    return day + '/' + month + '/' + year;
}

function cargaviewAuditoriaOnline() {
    $("#section_search_solicitud").hide()
    $('#auditoria_online').show();
    base_url = $("input#base_url").val() + "auditoria_interna/Auditoria/vistaGeneraAuditoria";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main #auditoria_online").html(response);

        }
    });
}


function cargaviewAuditoriaPosterior() {
        let fecha = $("#date_range").val();

        $('#auditoria_online').hide();
        $("section #texto").html("");
        $("section #texto").addClass('hide');
    
        var $loader = $('.loader');
        base_url = $("input#base_url").val() + "ApiAuditoriaInterna/solicitudes/noAuditadas";
        $.ajax({
            type: "POST",
            data: {
                "fecha": fecha
            },
            url: base_url,
            beforeSend: function() {
                $("#compose-modal-wait-post").modal({
                    show: true,
                    backdrop: 'static',
                    keyboard: false
                });

                $loader.show();
            },
            success: function (response) {
                $loader.hide();
                $("#compose-modal-wait-post").modal('hide');

                $("#table_search_posterior").dataTable().fnDestroy();

                let tabla = [];
                solicitudes = response.solicitudes;
                if (solicitudes.length > 0) {
                    solicitudes.forEach(item => {
                        tabla.push([
                            `<a href="#" 
                                class="btn btn-xs btn-primary solicitud-por-auditar" 
                                title="Consultar" 
                                data-solicitud="${item.id}"
                                data-audio=${item.audio}
                                data-cliente="${item.nombres} ${item.apellidos}"
                                data-documento=${item.documento}
                                data-tipo=${item.tipo_solicitud}
                                data-fecha_alta=${item.fecha_alta}
                                data-fecha_aprobado=${item.fecha_creacion}
                                data-monto=${item.capital_solicitado}
                                data-telefono=${item.telefono}
                                onclick="mostrarInformacion(this)"
                            >
                                <i class="fa fa-cogs"></i>
                            </a>`,
                            item.id,
                            moment(item.fecha_alta).format('DD/MM/YYYY hh:mm:ss'),
                            item.documento,
                            item.nombres +' '+item.apellidos,
                            item.name_agent,
                            item.capital_solicitado,
                            item.score,
                        ]);
                    });
                    //$("#table-track_marcacion").removeClass('hidden');
                    $('#table_search_posterior').DataTable({"order": [[ 0, "desc" ]], 'pageLength': 10}).clear().rows.add(tabla).draw();
                } else {
                    Swal.fire("Ups!", "No hay datos que mostrar", "info");
                }
            }
        });
        $("section#auditar-solicitud").hide();
        $("#section_search_solicitud").show();
        $("#result_posterior").show();
}

/************************************************/
/*** Muestra el detalle del audio por auditar ***/
/************************************************/
function mostrarInformacion(element) {
    $("#result_posterior").hide();
    $('#container-no-auditados').hide();

    let id_solicitud = $(element).data('solicitud');
    $("#txt_hd_solicitud_post").val(id_solicitud);
    $("#txt_hd_telefono_post").val($(element).data('telefono'));
    let id_audio = $(element).data('audio');
    $("#txt_hd_audio_post").val(id_audio);
    $("#lbl_solicitante_post").text($(element).data('cliente'));
    $("#lbl_documento_post").text($(element).data('documento'));
    $("#lbl_tipo_solicitud_post").text($(element).data('tipo'));
    let fecha_alta = moment($(element).data('fecha_alta')).format('DD/MM/YYYY');
    $("#lbl_fecha_alta_post").text(fecha_alta);
    let fecha_aprobado = moment($(element).data('fecha_aprobado')).format('DD/MM/YYYY');
    $("#lbl_fecha_aprobado_post").text(fecha_aprobado);
    $("#lbl_monto_post").text(formatNumber($(element).data('monto')));
    base_url = $("input#base_url").val() + "ApiAuditoriaInterna/getLlamadasPorAuditar/" + id_audio;
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
            //$("#main #auditoria_online").html(response);
            $("#table-audios-no-auditados").dataTable().fnDestroy();
            let tabla = [];
            audios = response.audios;
            audios.forEach(item => {
                tabla.push([
                    moment(item.fecha_hora_llamada).format('DD/MM/YYYY hh:mm:ss'),
                    item.name_agent,
                    item.telefono,
                    'Contacto',
                    'Fuente',
                    item.talk_time,
                    item.tipo_llamada,
                    item.who_hangs_up,
                    item.central,
                    `<a href="#" 
                        class="btn btn-xs btn-primary solicitud-por-auditar" 
                        title="Consultar" 
                        onclick="mosrtrarAudio(this)"
                        data-audio_path=${item.path_audio}
                        data-id_audio=${item.id}
                        data-id_solicitud=${id_solicitud}
                    >
                        <i class="fa fa-eye"></i>
                    </a>`,
                ]);
            });
            //$("#table-track_marcacion").removeClass('hidden');
            $('#table-audios-no-auditados').DataTable({"order": [[ 0, "desc" ]], 'pageLength': 10}).clear().rows.add(tabla).draw();
        }
    });
    $("section#auditar-solicitud").show();
}

function formatNumber(numero)
{
    let num = parseFloat(numero).toFixed(2);
    var num_parts = num.toString().split(".");
    num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return num_parts.join(",");
}

/********************************************************/
/*** Envío por AJAX del formulario de la calificación ***/
/********************************************************/
$('#frm_califica_post #btn_guardar_post').on('click', function (event) {
    event.preventDefault();

    let base_url = $("#base_url").val();
    /*** Se verifica si es un create o un update para la ruta ***/
    let url = $('#txt_hd_id_auditoria_post').val() 
        ? 'ApiAuditoriaInterna/actualizarAuditoria/' + $('#txt_hd_id_auditoria_post').val() 
        : base_url + 'api/ApiAuditoriaInterna/GuardarAuditoria';

    $.ajax({
        data: {
            "sl_tlfcliente": $("#txt_hd_telefono_post").val(),
            "txt_observaciones": $("#txt_observaciones_post").val(),
            "rd_califica": $('input:radio[name=rd_califica_post]:checked').val(),
            "id_audio": $("#txt_hd_audio_post").val(),
            "tipo_auditoria": "POSTERIOR",
            "txt_hd_operacion": $("#txt_hd_operacion_post").val(),
            "txt_hd_solicitud": $("#txt_hd_solicitud_post").val()
        },
        url:   url,
        type: 'POST',
    })
    .done(function(respuesta){
        if (respuesta.status.ok) {
            if (respuesta.auditoria.length > 0) {
                let fila = {
                    id: respuesta.auditoria[0].id,
                    fecha_auditado: respuesta.auditoria[0].fecha_auditado,
                    id_solicitud: respuesta.auditoria[0].id_solicitud,
                    tlf_cliente: respuesta.auditoria[0].tlf_cliente,
                    observaciones: respuesta.auditoria[0].observaciones,
                    gestion: respuesta.auditoria[0].gestion,
                    proceso: respuesta.auditoria[0].proceso
                };
                $('#resumen-auditoria_post').dataTable().api().row.add(fila).draw();
                
                $('input[type=radio]').removeAttr( "checked" );
                $('#txt_hd_id_auditoria_post').val("");
                $('#txt_observaciones_post').val("");
                rowData = '';
                $('#rd_califica1_post').prop( "checked", true );

                Swal.fire("Listo!",'',"success");
            }
        } else{
            Swal.fire("Ups!",respuesta.message,"error");
        }
    })
    .fail(function(xhr,err){
        Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
    });
});
async function verSolicitud (id_solicitud) {    
    x = await Promise.resolve(consultar_solicitud(id_solicitud));
}


/************************************/
/*** Muestra el audio a Calificar ***/
/************************************/
function mosrtrarAudio(element) {
    base_url = $("input#base_url").val();
    verSolicitud($(element).data('id_solicitud'));

    $('#container-no-auditados').show();
    getAuditoriaAudioPosterior($(element).data('id_audio'));
    $("#audio_audio").attr("src", $(element).data('audio_path'));

    /*** Hay que buscar de hacer esto con promesas 
         o de una forma más eficiente que esta ***/
    setTimeout(() => {
        $("#texto #close_solicitude").css('display', 'none');
        $("#texto .content-header").css('display', 'none');
        $("#texto .title").css('display', 'none');
        $("#texto .container .dropdown").css('display', 'none');
        $("#texto #edit_mail").css('display', 'none');
        $("#texto #icono_reenviar").css('display', 'none');
        $("#texto #icono_reenviar_datos").css('display', 'none');
        $("#texto #btn_save_comment").css('display', 'none');
        $("#texto #validar-desembolso").css('display', 'none');
        $("#texto #box_botones_gestion").css('display', 'none');
        $("#texto .form-group button").css('display', 'none');
        $("#texto #btnRecalcular").css('display', 'none');
        $("#texto #titulo i").css('display', 'none');
        $("#texto .a").css('display', 'none');
        $("#texto #box_galery button").css('display', 'none');
        $("#texto #label_archivo").css('display', 'none');
        $("#texto").removeClass('hide');
    }, 3000);
}
/*******************************************/
/*** Cierra la sección de la información ***/
/*******************************************/
$('section #a_close').on('click', function () {
    $("section #auditar-solicitud").hide();
    $("#table-audios-no-auditados").html("");
    $("section #texto").html("");
    $("section #texto").addClass('hide');
    $("#result_posterior").show();
});
/***************************************************************/
/*** Se obtiene la auditoría realizada de un audio POSTERIOR ***/
/***************************************************************/
function getAuditoriaAudioPosterior(id_audio) {
    /*** Se instancia el dataTable ***/
    $("#resumen-auditoria_post").dataTable().fnDestroy();
    $('#resumen-auditoria_post').DataTable({
        // rowId: data.id,
        order: [[ 1, "asc" ]],
        // data: tabla,
        columns: [
            { data: 'id' },
            { data: null,
                render: (data) => {
                    return moment(data.fecha_auditado).format('DD-MM-YYYY HH:mm:ss') 
                }
            },
            { data: 'id_solicitud' },
            { data: 'tlf_cliente' },
            { data: 'observaciones' },
            { data: 'gestion' },
            { data: 'proceso' },
            { data: null,
                render: (data) => {
                    return `<a class="btn btn-xs btn-success" 
                                title="Actualizar Auditoría"
                                onclick="cargarAuditoriaFormPosterior(${data.id}, '${data.observaciones}', '${data.gestion}')">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>`
                }
            }
        ],
        language: {
            sProcessing:     'Procesando...',
            sLengthMenu:     'Mostrar _MENU_ registros',
            sZeroRecords:    'No se encontraron resultados',
            sEmptyTable:     'Ningún dato disponible en esta tabla',
            sInfo:           'Del _START_ al _END_ de un total de _TOTAL_ reg.',
            sInfoEmpty:      '0 registros',
            sInfoFiltered:   '(filtrado de _MAX_ reg.)',
            sInfoPostFix:    '',
            sSearch:         'Buscar:',
            sUrl:            '',
            sInfoThousands:  ',',
            sLoadingRecords: 'Cargando...',
            oPaginate: {
                    sFirst:    'Primero',
                    sLast:     'Último',
                    sNext:     'Sig',
                    sPrevious: 'Ant'
            },
            oAria: {
                sSortAscending:  ": Activar para ordenar la columna de manera ascendente",
                sSortDescending: ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
    $('#resumen-auditoria_post').DataTable().column( 0 ).visible( false );

    let base_url = $("#base_url").val();

    $.ajax({
        type: "GET",
        url: base_url + 'ApiAuditoriaInterna/getAuditadasPosterior/' + id_audio,
    })
    .done(function(respuesta) {
        if (respuesta.status.ok) {
            $('#resumen-auditoria').dataTable().api().rows.add(respuesta.auditorias).draw();
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
/*** Se carga la auditoría POSTERIOR a modificar en el formulario ***/
/********************************************************************/
var rowData = '';
function cargarAuditoriaFormPosterior(id_auditoria, observacion, gestion) {
    if(rowData) {
        $('#resumen-auditoria_post').dataTable().api().row.add(rowData).draw();
    };

    $('#txt_hd_id_auditoria_post').val(id_auditoria);

    $('#txt_observaciones_post').val(observacion);

    $('input[type=radio]').removeAttr( "checked" );
    switch (gestion) {
        case "BUENA":
            $('#rd_califica1_post').prop( "checked", true );
        break;
        case "REVISAR":
            $('#rd_califica2_post').prop( "checked", true );
        break;
        case "MALA":
            $('#rd_califica3_post').prop( "checked", true );
        break;
        default: console.log('default:', gestion);
        break;
    }

    gestion2 = $('input:radio[name=rd_califica_post]:checked').val();
    
    /*** Se busca la fila a editar en el dataTable para removerla ***/
    var table = $('#resumen-auditoria_post').DataTable();
    var indexes = table.rows().eq( 0 ).filter( function (rowIdx) {
        return table.cell( rowIdx, 0 ).data() == id_auditoria ? true : false;
    });
    rowData = $('#resumen-auditoria_post').dataTable().api().row(indexes[0]).data();
    $('#resumen-auditoria_post').dataTable().api().row(indexes[0]).remove().draw();
}
/********************/
/*** Botón buscar ***/
/********************/
$('#aEnvio').on('click', function (event){
    cargaviewAuditoriaPosterior();
});

/***********************/
/*** daterangepicker ***/
/***********************/
$("#date_range").daterangepicker(
    {
        autoUpdateInput: false,
        ranges: {
            Hoy: [moment(), moment()],
            Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "Últimos 7 Días": [moment().subtract(6, "days"), moment()],
            "Últimos 30 Días": [moment().subtract(29, "days"), moment()],
            "Mes Actual": [moment().startOf("month"), moment().endOf("month")],
            "Mes Anterior": [
                moment()
                    .subtract(1, "month")
                    .startOf("month"),
                moment()
                    .subtract(1, "month")
                    .endOf("month")
            ]
        },
        locale: {
            format: "DD-MM-YYYY",
            separator: " | ",
            applyLabel: "Guardar",
            cancelLabel: "Cancelar",
            fromLabel: "Desde",
            toLabel: "Hasta",
            customRangeLabel: "Personalizar",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            monthNames: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            firstDay: 1
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
        timePicker: false
    },
    function(start, end) {
        $("#daterange-btn span").html(
            start.format("DD-MM-YYYY") + " - " + end.format("DD-MM-YYYY")
        );
    }
);

$("#date_range").on("apply.daterangepicker", function(ev, picker) {
    $(this).val(
        picker.startDate.format("DD-MM-YYYY")
        + " | " +
        picker.endDate.format("DD-MM-YYYY")
    );
});

$("#date_range").on("cancel.daterangepicker", function(ev, picker) {
    $(this).val("");
});
