// Inicio de as funciones para el modulo de Auditoria Originacion & Cobranza

// Muestra la vista para auditoria originación.
function auditoriaOriginacion() {
    localStorage.removeItem('busqueda'); // Limpia el localStorage, solo el imtem que guarde en la busqueda.

    if ($("div[id='btnsAuditoria']")) {
        $("div[id='btnsAuditoria']").remove();
    }

    let end_point  = $("input#base_url").val() + "auditoria_originacion_cobranza/Auditoria/auditoriaOriginacion";
    
    $.ajax({
        type: "GET",
        url: end_point,
        success: function (response) {
            $("#main").html(response);
            $('#canal').addClass('originacion');
        }
    });
}

// function operadoresSelect() {

//     $.ajax({
//         url: $("input#base_url").val() + 'operadores_select',
//         type: 'GET',
//     })
//     .done(function (response) {
//         console.log(response);
//     })
// }


function auditoriaCobranzas() {
    if ($("div[id='btnsAuditoria']")) {
        $("div[id='btnsAuditoria']").remove();
    }

    let end_point  = $("input#base_url").val() + "auditoria_originacion_cobranza/Auditoria/auditoriaCobranzas";
    
    $.ajax({
        type: "GET",
        url: end_point,
        success: function (response) {
            $("#main").html(response);
        }
    });
}

function misAuditorias() {
    localStorage.removeItem('busqueda');
    if ($("section[id='box_detalle_auditoria']")) {
        $("section[id='box_detalle_auditoria']").remove();
    }
    if ($("div[id='btnsAuditoria']")) {
        $("div[id='btnsAuditoria']").remove();
    }

    if ($("div[id='casosAAuditarTabla']")) {
        $("div[id='casosAAuditarTabla']").remove();
    }

    if ($("div[id='box_auditoria']")) {
        $("div[id='box_auditoria']").remove();
    }

    let end_point  = $("input#base_url").val() + "auditoria_originacion_cobranza/Auditoria/misAuditorias";
    
    $.ajax({
        type: "GET",
        url: end_point,
        success: function (response) {
            $("#main").html(response);
        }
    });
}

// Muestra la vista de la tabla
function cargarTable(close = null) {
    if (close == null) {
        localStorage.removeItem('busqueda');
    }
    if ($("div[id='btnsAuditoria']")) {
        $("div[id='btnsAuditoria']").remove();
    }
    
    let end_point  = $("input#base_url").val() + "auditoria_originacion_cobranza/Auditoria/auditarLlamadas";
    $.ajax({
        type: "GET",
        url: end_point,
        success: function (response) {
            
            $("#main").html(response);
        }
    });
}


// Envío el formulario con el resultado y las calificaciones del audio.
function envioFormAdutoriaLlamada(){
    // Captur btn class guardar-btn
    let btn = document.getElementsByClassName('guardar-btn');
    // Add class disabled to btn
    btn[0].disabled = true;

    let id_solicitud = $("#id_solicitud").val();

    let id_track = document.querySelectorAll('input[id="id_track"]:checked');
    var audios_auditados = new Array();
    
    id_track.forEach(element => {
        audios_auditados.push(element.value);
    });

    let evaluacion = [];
    $("input:radio:checked").each(function() {
        evaluacion.push($(this).val());
    });  
    let tipo_operador = $("#tipo_operador").val();
    let estado_solicitud = $("#estado_solicitud").val();

    let observacion = $("#result-cometario").val()

    $.ajax({
        type: "post",
        url: base_url + "api/ApiAuditoria/obtener_parametros",
        data: {"id_solicitud":id_solicitud, "ids_track":audios_auditados, "tipo_operador":tipo_operador, "estado":estado_solicitud, "evaluacion":evaluacion, "observacion":observacion},
        success: function (response) {
            let resp = JSON.parse(response);
            if (resp.status == 200) {
                btn[0].disabled = false;
                Swal.fire({
                    title: 'Llamada auditada.',
                    text: 'La auditoría de la llamada fue registrada correctamente.',
                    type: 'success',
                    confirmButtonText: 'OK'
                });
                audios_auditados.forEach(id => {
                    $("#"+id).remove();
                });

                let unchecked = document.querySelectorAll('input[type="radio"]:checked');
                    
                unchecked.forEach(element => {
                    element.checked = false;
                });
                
                document.querySelector('#result-cometario').value = '';
                
                $.ajax({
                    type: "post",
                    url: base_url + "api/ApiAuditoria/verificarAudios",
                    data: {"id_track":audios_auditados, "id_solicitud":id_solicitud},
                    success: function (respuesta) {
                        if (respuesta) {
                            $('#' +audios_auditados[0]).remove();
                            if ($(".audios_auditar").length == 0) {
                                $("#auditoriaForm").empty();
                                $("#auditoriaForm").css("text-align","center");
                                $("#auditoriaForm").css("height","190px");

                                $("#auditoriaForm").append("<div id='no_audios'><h1 style='padding-top: 4%;'>No posee audios para auditar</h1></div>");
                            }
                        }
                    }
                });
            }else {
                btn[0].disabled = false;
                Swal.fire({
                    title: 'Error al auditar.',
                    text: resp.mensaje,
                    type: 'error',
                    confirmButtonText: 'OK'
                })        
            }
        }
    });

    // obtener el value de la opcion selecionada del select id result-select
    // const select = document.getElementById('result-select');
    // const selected = select.options[select.selectedIndex].value;

    // obtengo el texto de la etiqueta textarea con id result-cometario
    // const comentario = document.getElementById('result-cometario').value;

    // // Todos los campos deben estar checkeados.
    // if(evaluacion.length > 0){

    //     let resultado_auditoria = {
    //         presentacion: presentacion,
    //         negociacion: negociacion,
    //         sondeo: sondeo,
    //         profesionalismo: profesionalismo,
    //         cierre: cierre,
    //         // resultado: selected,
    //         comentarios: comentario,
    //         id_solicitud: id_solicitud,
    //         ids_track: audios_auditados
    //     };

    //     $.ajax({
    //         type: "post",
    //         url: base_url + "api/ApiAuditoria/obtener_parametros",
    //         data: {"tipo_operador":tipo_operador, "estado":estado_solicitud, "evaluacion":evaluacion},
    //         success: function (response) {
                
    //             if(response.status.ok){
    //                 btn[0].disabled = false;
    //                 Swal.fire({
    //                     title: 'Llamada auditada.',
    //                     text: 'La auditoría de la llamada fue registrada correctamente.',
    //                     type: 'success',
    //                     confirmButtonText: 'OK'
    //                 });
    //                 audios_auditados.forEach(id => {
    //                     $("#"+id).remove();
    //                 });

    //                 let unchecked = document.querySelectorAll('input[type="radio"]:checked');
                    
    //                 unchecked.forEach(element => {
    //                     element.checked = false;
    //                 });
                    
    //                 document.querySelector('#result-cometario').value = '';
                    
    //                 $.ajax({
    //                     type: "post",
    //                     url: base_url + "api/ApiAuditoria/verificarAudios",
    //                     data: {"id_track":audios_auditados, "id_solicitud":id_solicitud},
    //                     success: function (respuesta) {
    //                         if (respuesta) {
    //                             $('#' +audios_auditados[0]).remove();
    //                             if ($(".audios_auditar").length == 0) {
    //                                 $("#auditoriaForm").empty();
    //                                 $("#auditoriaForm").css("text-align","center");
    //                                 $("#auditoriaForm").css("height","190px");
    
    //                                 $("#auditoriaForm").append("<div id='no_audios'><h1 style='padding-top: 4%;'>No posee audios para auditar</h1></div>");
    //                             }
    //                         }
    //                     }
    //                 });
    //                 } else {
    //                     btn[0].disabled = false;
    //                     Swal.fire({
    //                         title: 'Llamada auditada.',
    //                         text: 'La auditoría del llamada no pudo ser registrada correctamente.',
    //                         type: 'error',
    //                         confirmButtonText: 'OK'
    //                     });
    //                 }
    //             }  
    //         });
        
    // } else {
    //     btn[0].disabled = false;
    //     Swal.fire({
    //         title: 'Llamada auditada.',
    //         text: 'Todos los campos deben estar checkeados.',
    //         type: 'error',
    //         confirmButtonText: 'OK'
    //     })        
    // }
}

function cerrarCasoAuditar() {
    $('.box_auditoria').remove();
    cargarTable(true);
}

// Inicializo la tabla que muestra las solicitudes pendientes de auditar..
// Se ejecuta al cargar la pagina o al relizar una búsqueda, se guardan los paramentros de búsqueda en el local. 
function initTableAuditarLlamadas(busqueda = false) {
    let btn = document.getElementById('buscar_llamada');
    if ((localStorage.getItem('busqueda') == null) ||
        (
            JSON.parse(localStorage.getItem('busqueda')).desde == null &&
            JSON.parse(localStorage.getItem('busqueda')).hasta == null &&
            JSON.parse(localStorage.getItem('busqueda')).operador == null &&
            JSON.parse(localStorage.getItem('busqueda')).tipoOperador == null &&
            JSON.parse(localStorage.getItem('busqueda')).telefono == null &&
            JSON.parse(localStorage.getItem('busqueda')).pais == 'All' &&
            JSON.parse(localStorage.getItem('busqueda')).pais == null
        ) ||  (busqueda == true) ) {
            if($("#tipoOperadorSelected").val() == ""){
                var tipoOperador = "(1,4,5,6)";
            }else{
                var tipoOperador = $("#tipoOperadorSelected").val();
            }
                var desde    = ($("input#date_range-desde").val() != "") ? $("input#date_range-desde").val() : null;
                var hasta    = ($("input#date_range-hasta").val() != "") ? $("input#date_range-hasta").val() : null;
                var pais     = ($("#equipoSelected option:selected").val() != 'seleccione_equipo') ? $("#equipoSelected option:selected").val() : 'All';
                var operador = ($("#operadorSelected option:selected").val() != 'seleccione_operador' && $("#operadorSelected option:selected").val() != 'undefined') ? $("#operadorSelected option:selected").val() : null;
                var central = ($("#centralSelected option:selected").val() != 'seleccione_operador' && $("#centralSelected option:selected").val() != 'undefined') ? $("#centralSelected option:selected").val() : null;
                var tipoOperador = tipoOperador;
                var telefono = ($("input#date_telefono").val() != "") ? $("input#date_telefono").val() : null;
    } else {
        let localBusqueda = JSON.parse(localStorage.getItem('busqueda'));
        var desde    = localBusqueda.desde;
        var hasta    = localBusqueda.hasta;
        var pais     = localBusqueda.pais;
        var tipoOperador = localBusqueda.tipoOperador;
        var operador = localBusqueda.operador;
        var telefono = localBusqueda.telefono;
        var central = localBusqueda.central;

        $("input#date_range-desde").val(desde);
        $("input#date_range-hasta").val(hasta);
        $('select#tipoOperadorSelected option[value="'+tipoOperador+'"]').attr("selected", true);
        $("select#centralSelected option[value='"+central+"']").attr("selected", true);
        
        $('select#equipoSelected option[value="'+pais+'"]').attr("selected", true);
        
        $("input#date_telefono").val(telefono);

        $('#operadorSelected').remove();
        $.ajax({
            url: $("input#base_url").val() +'api/ApiAuditoria/tipo_operador',
            type: 'post',
            data: {"tipo_operador":tipoOperador, "equipo":pais},
            })
            .done(function (response) {
                let select = `<select class="col-md-12 form-control select2-multiple" name="Seleccione operador" style="height: 36px!important;" id="operadorSelected">Seleccione un operador</option>`;
                
                select += `<option value="seleccione_operador">Seleccione operador</option>`;
                response.forEach(element =>{
                    if (element.idoperador == operador) {
                        select += `<option value="${element.idoperador}" selected>${element.nombre_apellido}</option>`;
                    }
                    select += `<option value="${element.idoperador}">${element.nombre_apellido}</option>`;
                });
                select += `</select>`;
                $('#operadoresPorEquipo').append(select);
                $('#operadorSelected').select2();
            })
    }
    var busqueda = {
        desde: desde,
        hasta: hasta,
        pais: pais,
        operador: operador,
        tipoOperador: tipoOperador,
        telefono: telefono,
        central: central,
    };

    // Guardo los parámetros de búsqueda en el localStorage.
    localStorage.busqueda = JSON.stringify(busqueda);
 
    let end_point = $("#base_url").val() + 'api/auditoria/llamadas';

    // Se fecha desde es mayo a fecha hasta muestro mensaje de error y corto la función.
    if (desde > hasta) {
        Swal.fire({
            title: 'Error',
            text: 'La fecha de inicio debe ser menor a la fecha de fin.',
            type: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }else{

        $('#tp_auditarLlamadas').dataTable().fnDestroy()
        $('#tp_auditarLlamadas').dataTable( {
            "responsive":true,
            "processing":true,
            "language": spanish_lang,
            'iDisplayLength': 10,
            'paging':true,
            'info':true,
            "searching": false,
            "serverSide": true,
            "order": [[ 2, "desc" ]],
            "ajax":
                    $.fn.dataTable.pipeline( {
                    "url": end_point,
                    "type" : "GET",
                    "pages": 5,
                    "data": {
                        "fecha_desde" : desde,
                        "fecha_hasta" : hasta,
                        "pais" : pais,
                        "operador" : operador,
                        "tipoOperador" : tipoOperador,
                        "telefono" : telefono,
                        "central":central
                    }
                }),
                columns:[
                    {
                        'data': null,
                        "className": 'dt-control',
                        "orderable": false,
                        render: function (data, type, row, meta) 
                        {   
                            let info = `<button 
                                        id="info_auditarLlamadas" 
                                        class="col-xs-12 dt-control btn btn-primary" 
                                        style="padding:0!important;height:30px;"
                                        data-telefono="${data.numero_telefonico}"
                                        value="${data.solicitud}"
                                        data-a_auditar="${data.solicitud}"
                                        tipo_operador="${data.tipo_operador}"
                                        title="Auditar gestión">
                                            <i class="fa fa-cogs"></i>
                                    </button>`;
                            return info;                        
                        },
                    },
                    {
                        'data': null,
                        render: function (data, type, row, meta) 
                        {
                            let info = `<i 
                                            class="fa fa-clock-o" 
                                            style="font-size: 30px;padding:0!important;height:30px;color:#bb0a09;" 
                                            title="Llamado pendiente de auditar"></i>`;
                                return info;
                        }
                    },
                    { 'data': "fecha",
                        render: function (data, type, row, meta) 
                        {
                            
                            let fecha = moment(data).format('DD-MM-YYYY HH:mm:ss');
                            return data ? fecha : '-';
                        }
                    },
                    { 'data': "solicitud",
                        render: function (data, type, row, meta) 
                        {
                            return data ? data : '-';
                        }
                    },
                    { 'data': "numero_telefonico",
                        render: function (data, type, row, meta) 
                        {
                            
                            return (data != null) ? data : '-';
                        }
                    },
                    { 'data': "contacto",
                        render: function (data, type, row, meta) 
                        {
                            
                            return (data != null) ? data : '-';
                        }
                    },
                    { 'data': "documento",
                        render: function (data, type, row, meta) 
                        {
                            
                            return (data != null) ? data : '-';
                        }
                    },
                    { 'data': "operador_asignado",
                        render: function (data, type, row, meta) 
                        {
                            
                            return (data != null) ? data : '-';
                        }
                    }              
                ],
                "columnDefs": [
                    {
                        "targets": 0,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 3%; text-align: center;'); 
                                    }
                    },
                    {
                        "targets": 1,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 3%; text-align: center;'); 
                                    }
                    },
                    {
                        "targets": 2,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 7%; text-align: center;'); 
                                    }
                    }, 
                    {
                        "targets": 3,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                                      {
                                            $(td).attr('style', 'width: 9%; text-align: center;'); 
                                      }
                    },
                    {
                        "targets": 4,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                                        {
                                            $(td).attr('style', 'width: 9%; text-align: center;'); 
                                        }
                    },
                    {
                        "targets": 5,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                                        {
                                            $(td).attr('style', 'width: 9%; text-align: left;'); 
                                        }
                    },
                    {
                        "targets": 6,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                                        {
                                            $(td).attr('style', 'width: 8%; text-align: center;'); 
                                        }
                    },
                    {
                        "targets": 7,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 9%; text-align: left;'); 
                                    }
                    }               
                ],   
            });    
    }

}

$('body').on('click','#tp_auditarLlamadas button',function(event){
    event.preventDefault();
    let telefono = $(this).attr('data-telefono');
    let id_solicitud = $(this).attr('value');
    let tipo_operador = $(this).attr("tipo_operador");

    let central = $('#centralSelected').val();
    if ($("div[id='btnsAuditoria']")) {
        $("div[id='btnsAuditoria']").remove();
    }
    if ($("section[class='box_auditoria']")) {
        $("section[class='box_auditoria']").remove();
    }
    
    let end_point  = $("input#base_url").val() + "auditoria_gestion_operador";
    let $loader = $('.loader');  
    $.ajax({
        type: "POST",
        data: {"id_solicitud":id_solicitud, "telefono":telefono, "central":central, "tipo_operador":tipo_operador}, 
        url: end_point,
        beforeSend: function () {
            $("#modalLoadingAuditoria").modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
            $loader.show();
        },
        success: function (response) {
            setTimeout(function(){
                $loader.hide();
                $("#modalLoadingAuditoria").modal('hide');
                $(".modal-backdrop").fadeOut();
                $(".skin-purple").removeClass('modal-open');
                $(".skin-purple").css('padding-right','0px');
                if (response != 400) {
                    $("#casosAAuditarTabla").remove();
                    $("#main").html(response);
                    $('#canal').addClass('originacion');
                    
                    get_track(id_solicitud);
                    
                }else{
                    Swal.fire('No se han encontrado audios', "El registro no posee audios para auditar", 'warning');
                }
            }, 500);
        },
        error: function (error) {
            $loader.hide();
                $("#modalLoadingAuditoria").modal('hide');
            $(".modal-backdrop").fadeOut();
            $(".skin-purple").removeClass('modal-open');
        }
    });
})

const get_track = (id_solicitud) => {
    var documento = $("#client").data("number_doc");
    
    $.ajax({
        url: $("input#base_url").val() + 'solicitud/gestion/track/' + id_solicitud,
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
    paginacion = 0;
    
    if ($(".row-chat-track").find('#whatsapp').length > 0) {
        $.ajax({
            url: $("input#base_url").val() + 'solicitud/gestion/whatsapp_paginado/' + documento + '/' + paginacion + '/1/',
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
function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

function reportarAudio(element) {

    $('#reportarAudioModal').css('display', 'block');

    if ($('#reporteForm')) {
        $('#reporteForm').remove();
    }

    let id_track = $(element).data('id_track');
    let fecha_audio = $(element).data('fecha_audio');
    let telefono = $(element).data('telefono');
    let id_solicitud = $('#solicitud').data('id_solicitud');

    let reporte = `<div class="col-md-3" id="reporteForm" style="background-color: #fff; margin-top: 14%; margin-left: 36%; height: 230px; padding-top: 1%; width: 431px;border-radius:2%;">

                    <h4 style="text-align: center;">Seleccione el motivo por el que se reporta el audio</h4>
                    <div class="col-md-12" style=" z-index: 1;height: 44px;padding-top: 0px; border-top: 3px solid #00c0ef;    padding-left: 10%;">
                        <h6 class="col-md-6" >Reportando audio de fecha:</h6>
                        <h6 class="col-md-6">${fecha_audio}</h6>
                    </div>
                    
                    <select id="tipo_incidente" class="col-md-10" style="margin-top:2%;margin-left:8%;height:30px;margin-bottom:2%">
                        <option value="1">Seleccione</option>
                        <option value="Reportado">No se puede reprodcir audio</option>
                        <option value="No corresponde">Audio no corresponde a la solicitud</option>
                    </select>

                    <button 
                        id="reporteAudio" 
                        class="btn btn-primary bg-light col-md-5" 
                        style="font-size: 18px;padding:0!important;height:30px;margin-left:7%; margin-top: 3%;"
                        onclick="enviarReporte(this);"
                        data-id_solicitud="${id_solicitud}"
                        data-id_track="${id_track}"
                        data-telefono="${telefono}"
                        title="Reporatar problema con el audio.">
                            Reportar audio
                    </button>
                    <button 
                        id="cancelar" 
                        class="btn btn-warning bg-light col-md-5" 
                        style="font-size: 18px;padding:0!important;height:30px;margin-left:2%; margin-top: 3%;"
                        onclick=""
                        title="Reporatar problema con el audio." data-dismiss="modal">
                            Cancelar
                    </button>
                </div>`;
            $('#reportarAudioModal').append(reporte);
            $('#reportarAudioModal').modal('show');
            $('#reporteAudio').attr('disabled', true);

            $('#tipo_incidente').on('change', function () {
                if($('#tipo_incidente').val() != '1'){
                    $('#reporteAudio').attr('disabled', false);
                }else {
                    $('#reporteAudio').attr('disabled', true);
                }
                
            })
}

function enviarReporte(element) {
    
    let selection = document.getElementById("tipo_incidente");
    // selection.options[selection.selectedIndex].value;
    let operador = document.getElementById('hdd_id_operador').value;
    let id_track = $(element).data('id_track');
    let telefono = $(element).data('telefono');
    let id_solicitud = document.getElementById('id_solicitud').value

    $.ajax({
        url:   base_url +'auditoria_audio_reportar',
        type:  'POST',
        data:  {
            id_track: id_track,
            fecha_audio: $(element).data('fecha_audio'),
            tipo_incidente: selection.options[selection.selectedIndex].value,
            operador: operador,
            id_solicitud: id_solicitud,
        },
        success: function(response) {
            
            if (response.status.code == 200) {
                Swal.fire(
                    'Reportado!',
                    'El audio ha sido reportado.',
                    'success'
                )
                $('#reporteForm').remove();
                $('#reportarAudioModal').css('display', 'none');
                $('.modal-backdrop').css('display', 'none');
                $(".skin-purple").removeClass('modal-open');

                $.ajax({
                    type: "post",
                    url: base_url + "api/ApiAuditoria/verificarAudios",
                    data: {"id_track":id_track, "id_solicitud":id_solicitud},
                    success: function (respuesta) {
                        if (respuesta) {
                            $('#' + id_track).remove();
                            if ($(".audios_auditar").length == 0) {
                                $("#auditoriaForm").empty();
                                $("#auditoriaForm").css("text-align","center");
                                $("#auditoriaForm").css("height","190px");

                                $("#auditoriaForm").append("<div id='no_audios'><h1 style='padding-top: 4%;'>No posee audios para auditar</h1></div>");
                            }
                        }
                    }
                });
                // // cerrarCasoAuditar();
                // // auditarLlamada(telefono, id_solicitud);
                // $('#' + id_track).remove();
                
            }
        }
    })

}


function initTableMisAuditorias(busqueda = false) {

    
    
    let end_point = $("#base_url").val() + 'api/auditoria/mis_auditorias';

    $('#tp_misAuditorias').dataTable().fnDestroy()
    $('#tp_misAuditorias').dataTable( {
        "responsive":true,
        "processing":true,
        "language": spanish_lang,
        'iDisplayLength': 10,
        'paging':true,
        'info':true,
        "searching": true,
        // "serverSide": true,
        "order": false,
        "ajax":
                $.fn.dataTable.pipeline( {
                "url": end_point,
                "type" : "GET",
                "pages": 5,
            } ),
            columns:[
                {
                    'data': null,
                    "className": 'dt-control',
                    "orderable": false,
                    render: function (data, type, row, meta) 
                    {   
                        let info = `<button 
                                    id="info_auditarLlamadas" 
                                    class="col-xs-12 btn dt-control btn-primary" 
                                    style="padding:0!important;height:30px;background-color: #7fdede;font-size: 18px;border: none;color:#666;"
                                    onclick="detalleAuditoria(${data.id});"
                                    value=""
                                    data-a_auditar=""
                                    title="Ver detalle auditoria">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>`;
                        return info;
                    },
                },
                { 'data': "fecha_auditoria",
                    render: function (data, type, row, meta) 
                    {
                        
                        let fecha = moment(data).format('DD-MM-YYYY HH:mm:ss');
                        return data ? fecha : '-';
                    }
                },
                { 'data': "id_solicitud",
                    render: function (data, type, row, meta) 
                    {
                        return data ? data : '-';
                    }
                },
                { 'data': "numero_telefonico",
                    render: function (data, type, row, meta) 
                    {
                        
                        return (data != null) ? data : '-';
                    }
                },
                { 'data': "contacto",
                    render: function (data, type, row, meta) 
                    {
                        
                        return (data != null) ? data : '-';
                    }
                },
                { 'data': "documento",
                    render: function (data, type, row, meta) 
                    {
                        
                        return (data != null) ? data : '-';
                    }
                },
                { 'data': "operador_auditor",
                    render: function (data, type, row, meta) 
                    {
                        
                        return (data != null) ? data : '-';
                    }
                },      
                {
                    'data': "observaciones" ,
                    render: function (data, type, row, meta)
                    {
                        return `<p style="height: 20px;overflow-y:auto;">${data ? data : '-'}</p>`;
                    }
                }        
            ],
            "columnDefs": [
                {
                    "targets": 0,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                {
                                    $(td).attr('style', 'width: 2%; text-align: center;'); 
                                }
                },
                {
                    "targets": 1,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                {
                                    $(td).attr('style', 'width: 8%; text-align: center;'); 
                                }
                },
                {
                    "targets": 2,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                {
                                    $(td).attr('style', 'width: 3%; text-align: center;'); 
                                }
                }, 
                {
                    "targets": 3,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                  {
                                        $(td).attr('style', 'width: 6%; text-align: center;'); 
                                  }
                },
                {
                    "targets": 4,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 9%; text-align: left;'); 
                                    }
                },
                {
                    "targets": 5,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 6%; text-align: center;'); 
                                    }
                },
                {
                    "targets": 6,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 8%; text-align: left;'); 
                                    }
                },
                {
                    "targets": 7,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                {
                                    $(td).attr('style', 'width: 16%; text-align: center;'); 
                                }
                }               
            ],   
        });    
}

function detalleAuditoria(id_auditoria) {
    if ($("div[id='table_container']")) {
        $("div[id='table_container']").remove();
    }
    
    
    let end_point  = $("input#base_url").val() + "api/ApiAuditoria/detalleAuditoria";
    
    $.ajax({
        type: "POST",
        data: {"id_auditoria":id_auditoria},
        url: end_point,
        success: function (response) {
            
            $("#main").html(response);
           
        }
    });
}

function initTableMisAuditoriasSearch(documento, telefono) {
    let end_point = $("#base_url").val() + 'api/ApiAuditoria/searchMisAuditorias';
    $('#tp_misAuditorias').dataTable().fnDestroy()
    $('#tp_misAuditorias').dataTable( {
        "responsive":true,
        "processing":true,
        "language": spanish_lang,
        'iDisplayLength': 10,
        'paging':true,
        'info':true,
        "searching": true,
        // "serverSide": true,
        "order": false,
        "ajax":
                $.fn.dataTable.pipeline( {
                "url": end_point,
                "type" : "POST",
                "pages": 5,
                "data": {
                    "documento" : documento,
                    "telefono" : telefono
                }
            }),
            columns:[
                {
                    'data': null,
                    "className": 'dt-control',
                    "orderable": false,
                    render: function (data, type, row, meta) 
                    {   
                        let info = `<button 
                                    id="info_auditarLlamadas" 
                                    class="col-xs-12 btn dt-control btn-primary" 
                                    style="padding:0!important;height:30px;background-color: #7fdede;font-size: 18px;border: none;color:#666;"
                                    onclick="detalleAuditoria(${data.id});"
                                    value=""
                                    data-a_auditar=""
                                    title="Ver detalle auditoria">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>`;
                        return info;
                    },
                },
                { 'data': "fecha_auditoria",
                    render: function (data, type, row, meta) 
                    {
                        console.log(data)
                        let fecha = moment(data).format('DD-MM-YYYY HH:mm:ss');
                        return data ? fecha : '-';
                    }
                },
                { 'data': "id_solicitud",
                    render: function (data, type, row, meta) 
                    {
                        return data ? data : '-';
                    }
                },
                { 'data': "numero_telefonico",
                    render: function (data, type, row, meta) 
                    {
                        
                        return (data != null) ? data : '-';
                    }
                },
                { 'data': "contacto",
                    render: function (data, type, row, meta) 
                    {
                        
                        return (data != null) ? data : '-';
                    }
                },
                { 'data': "documento",
                    render: function (data, type, row, meta) 
                    {
                        
                        return (data != null) ? data : '-';
                    }
                },
                { 'data': "operador_auditor",
                    render: function (data, type, row, meta) 
                    {
                        
                        return (data != null) ? data : '-';
                    }
                },      
                {
                    'data': "observaciones" ,
                    render: function (data, type, row, meta)
                    {
                        return `<p style="height: 20px;overflow-y:auto;">${data ? data : '-'}</p>`;
                    }
                }        
            ],
            "columnDefs": [
                {
                    "targets": 0,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                {
                                    $(td).attr('style', 'width: 2%; text-align: center;'); 
                                }
                },
                {
                    "targets": 1,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                {
                                    $(td).attr('style', 'width: 8%; text-align: center;'); 
                                }
                },
                {
                    "targets": 2,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                {
                                    $(td).attr('style', 'width: 3%; text-align: center;'); 
                                }
                }, 
                {
                    "targets": 3,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                  {
                                        $(td).attr('style', 'width: 6%; text-align: center;'); 
                                  }
                },
                {
                    "targets": 4,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 9%; text-align: left;'); 
                                    }
                },
                {
                    "targets": 5,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 6%; text-align: center;'); 
                                    }
                },
                {
                    "targets": 6,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 8%; text-align: left;'); 
                                    }
                },
                {
                    "targets": 7,
                    "orderable": false,
                    "createdCell": function(td, cellData, rowData, row, col)
                                {
                                    $(td).attr('style', 'width: 16%; text-align: center;'); 
                                }
                }               
            ],   
        });    
        
}
