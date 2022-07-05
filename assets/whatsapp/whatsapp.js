var id_operador = 0; 
var id_chat = 0;
var isInterval = false;
var interval2 = 0;
var interval5 = 0;
var altoEncabezadoMensajes = 0;
var filtro = '';
var strBuscar = null;

// pluguin para saber si el scroll esta presente
(function($) {
    $.fn.hasScrollBar = function() {
        return this.get(0).scrollHeight > this.height();
    }
})(jQuery);

$(function() {
    window.clearInterval();
    /*** Volver a refrescar los datos cada 2 Minutos ***/
    interval2 = window.setInterval(function() {
        //chats(id_operador, filtro); 
    }, 120000);
    /*** Volver a refrescar los datos cada 5 Minutos ***/
    interval5 = window.setInterval(function() {
        //operadores(isInterval = true); 
    }, 300000);

    altoEncabezadoMensajes = $("#panel_heading_mensajes").height();
    $("#panel_operadores").height(window.screen.availHeight-180);
    $("#panel_mensajes").height(window.screen.availHeight-180);
    $("#panel_chats").height(window.screen.availHeight-180);

    //$(".row.main-body").height($("#panel_mensajes").height()-110);
    $(".row.main-body").css('max-height',$("#panel_mensajes").height()-130);
    $(".row.main-body").css('height',$("#panel_mensajes").height()-130);


    $("#caja_scroll_operadores").height(window.screen.availHeight-220);
    $("#caja_scroll_chats").height(window.screen.availHeight-220);
    $("#panel_mensajes").height($("#panel_mensajes").height()-100);

    $("#h6_operador").hide();

    
});


// Operadores según canal seleccionado 
function operadores(isInterval) {
    if (!$("#selectCanal").val()) {
        $("#ul_operadores").html('');
        id_operador = 0;
        return null;
    }
    
    let tlf = $("#selectCanal").val();
    
    $.ajax({
        dataType: "JSON",
        data: {
            "canal": tlf, 
        },
        url: base_url + 'whatsapp/getOperadoresCanal',
        type: 'POST',
        beforeSend: function() {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#main").html(loading);
            if (!isInterval){
                $('#modalLoading').modal("show");
            }
        }
    })
    .done(function(respuesta){
        $('#modalLoading').modal("hide");
        if (respuesta.operadores.length > 0) {
            let ulOperadores = document.getElementById("ul_operadores");
            let html = '';
            respuesta.operadores.map((operador) => {
                html = html + `
                <div class="div_operador" data-id_operador="${operador.id_operador}">
                    ${operador.id_operador == id_operador
                        ? '<div class="list-group-item selection_operador" style="background-color: AliceBlue;">'
                        : '<div class="list-group-item" style="background-color: AliceBlue;">'
                    }
                        <strong>${operador.nombre_apellido}</strong><span class="badge">${operador.total}</span>
                        <div class="row">
                            <div class="col-md-2 text-center" title="Activo">
                                <h5><i class="fa fa-dot-circle-o"></i></h5>
                                <h4 class="text-success" id="h4_activo"><strong>${operador.activo}</strong></h4>
                            </div>
                            <div class="col-md-2 text-center" title="Sin leer">
                                <h5><i class="fa fa-eye-slash"></i></h5>
                                <h4 class="text-danger" id="h4_sin_leer"><strong>${operador.sin_leer}</strong></h4>
                            </div>
                            <div class="col-md-3 text-center" title="Sin responder">
                                <h5><i class="fa fa-comment" aria-hidden="true"></i></h5>
                                <h4 class="" style="color: orange" id="h4_sin_responder">${operador.ultimo}<strong></strong></h4>
                            </div>
                            <div class="col-md-2 text-center" title="Pendiente">
                                <h5><i class="fa fa-clock-o"></i></h5>
                                <h4 class="text-success" id="h4_pendiente"><strong>${operador.pendiente}</strong></h4>
                            </div>
                            <div class="col-md-2 text-center" title="Vencido">
                                <h5><i class="fa fa-ban"></i></h5>
                                <h4 class="text-primary" id="h4_vencido"><strong>${operador.vencido}</strong></h4>
                            </div>
                        </div>
                    </div>
                </div>`
            });
            ulOperadores.innerHTML = html;
        } else {
            $('#graphContainer').hide();
            Swal.fire("¡Información!",'Operador inactivo',"info");
        }

    })
    .fail(function(xhr) {
        $('#modalLoading').modal("hide");
        Swal.fire("¡Atención!", 
            `readyState: ${xhr.readyState}
                status: ${xhr.status}
                responseText: ${xhr.responseText}`,
            "error"
        );
    });
}

//Selecciono canal 
$('body').on('change', '#selectCanal', function () {
    
    limpiaCampo();
    $("#ul_chat").html('');
    $("#btn_operador_seleccionado").html('');
    $("#select_buscar").val('');
    $("#select_operador").val('todos');
    $("#inp_buscar").val('');
    $("#h6_operador").hide();
    id_chat = 0;
    id_operador = 0;
    filtro = '';
    operadores(false);
});

// Chats de un Operador 
function chats(id_operador, filtro) {
    
    if(id_operador > 0) {
        let tlf = $("#selectCanal").val();
        
        var status = 'activo';
        if(filtro){
            var status = filtro;
        }
        
        $.ajax({
            dataType: "JSON",
            data: {
                "canal": tlf, 
                "id_operador": id_operador,
                "filtro": filtro,
                "status": status
            },
            url: base_url + 'whatsapp/getOperadorCliente',

            type: 'POST',
            beforeSend: function() {
                var loading =
                    '<div class="loader" id="loader-6">' +
                    "<span></span>" +
                    "<span></span>" +
                    "<span></span>" +
                    "<span></span>" +
                    "</div>";
                // $("#main").html(loading);
                // $('#modalLoading').modal("show");
            }
        })
        .done(function(respuesta){
            
            render_chat_list(respuesta, filtro);
          
            })
        .fail(function(xhr) {
            $('#modalLoading').modal("hide");
            Swal.fire("¡Atención!", 
                `readyState: ${xhr.readyState}
                    status: ${xhr.status}
                    responseText: ${xhr.responseText}`,
                "error"
            );
        });
    }
}

//renderizo lista de chats por operador
function render_chat_list(chats, filtro){
    $('#modalLoading').modal("hide");
    
    if (chats.chatOperador.length > 0) {
        
        let ulChat = document.getElementById("ul_chat");
        let html = '';
        
        chats.chatOperador.map((chat) => {
            html = html + `
            <div class="div_chat" data-canal_chat="${chat.canal.substr(-3)}" data-id_chat="${chat.id}" data-id_operador="${chat.id_operador}" data-nombre_operador="${chat.nombre_operador}">
                ${chat.id == id_chat
                    ? '<button class="list-group-item selection_chat" style="background-color: #f4f3f3; color: #cd547e; font-weight: bold; font-family: Comic Sans MS;">'
                    : '<button class="list-group-item" style="background-color: #f4f3f3; color: #cd547e; font-weight: bold; font-family: Comic Sans MS;">'
                }
                    ${(chat.sin_leer == 1 && chat.status_chat == 'activo') ? '<span class="badge" style="background-color: #cd547e"><i class="fa fa-comment"></i></span>' : ''}
                    ${chat.nombres} ${chat.apellidos} <span class="badge" style="background-color: #f4f3f3; color: #cd547e;">${filtro}</span>
                    <div class="row">
                        <div class="col-md-8 text-muted" style="font-family: Arial; font-size: 12px;">
                            ${chat.ultimo_mensaje.substr(0, 30)}...
                        </div>
                        <div class="col-md-4 text-muted text-right" style="font-family: Arial; font-size: 12px; margin-top: 5px">
                            ${chat.ultima_hora}
                        </div>
                    </div>
                </button>
            </div>`
        });
        ulChat.innerHTML = html; 
        

    } else {
        $('#graphContainer').hide();
        limpiaCampo();
        let html = `
            <div class="text-center">
                <h4 style="font-weight: bold; font-family: Comic Sans MS;">Operador sin chats asignados ${filtro}...</h4>
            </div>
        `
        $("#ul_chat").html(html);
        id_operador = 0;
        id_chat = 0;
        filtro = '';
    }
}

// Al escoger un Operador del panel izquierdo 
$('body').on('click', '#panel_operadores .div_operador', function () {
    id_operador = $(this).attr('data-id_operador');
    $("#inp_pagina").val(0);
    limpiaCampo();
    id_chat = 0;
    filtro = '';

    
    /*** Demarca el operador seleccionado ***/
    $('div.list-group-item').removeClass("selection_operador");
    $(this).children('div.list-group-item').addClass("selection_operador");

    $('input[name="status_chats"]').val('');
	//remuevo el contenedor para que permite redactar un mensaje
	$('div#comunication_container').remove();

    selection = $(this).clone();
    selection.prop("id", "clone");
    $("#btn_operador_seleccionado").html(selection);

    valorActivo = $("#clone").find("#h4_activo").text();
    $("#clone").find("#h4_activo").html(`<strong><a class="text-success chat_by_status" onclick="chats(`+id_operador+`,'activo')" data-a="activo">${valorActivo}</a></strong>`);

    valorSinLeer = $("#clone").find("#h4_sin_leer").text();
    $("#clone").find("#h4_sin_leer").html(`<strong><a class="text-danger chat_by_status" onclick="chats(`+id_operador+`,'sin_leer')" data-a="sin_leer">${valorSinLeer}</a></strong>`);

    valorSinResponder = $("#clone").find("#h4_sin_responder").text();
    $("#clone").find("#h4_sin_responder").html(`<strong><a class="chat_by_status" style="color: orange" onclick="chats(`+id_operador+`,'sin_responder')" data-a="sin_responder">${valorSinResponder}</a></strong>`);

    valorPendiente = $("#clone").find("#h4_pendiente").text();
    $("#clone").find("#h4_pendiente").html(`<strong><a class="text-success chat_by_status" onclick="chats(`+id_operador+`,'pendiente')" data-a="pendiente">${valorPendiente}</a></strong>`);

    vencido = $("#clone").find("#h4_vencido").text();
    $("#clone").find("#h4_vencido").html(`<strong><a class="text-primary chat_by_status" onclick="chats(`+id_operador+`,'vencido')" data-a="vencido">${vencido}</a></strong>`);

    
    // Filtrar por indicadores Activo, Sin leer, Sin responder y Vencido 
    
    chats(id_operador, filtro);    
});





function render_header_data(data, type_message = "") {

    //primer columna
    $('#sp_nombre_cliente').text(data.chatCliente[0].nombre_apellido);
    $('#sp_documento').text(data.chatCliente[0].documento);
    //$('#documento_hdd').html('<input type="hidden" value="'+data.chatCliente[0].documento+'">');
    $('#sp_telefono').text(data.chatCliente[0].tlf_cliente);

    //segunda columna
    $('#sp_primer_mensaje_recibido').text(data.totalRecibidosEnviados[1].primero);
    $('#sp_mensajes_recibidos').text(data.totalRecibidosEnviados[1].cantidad);

    //Tercer columna
    $('#sp_primer_mensaje_enviado').text(data.totalRecibidosEnviados[0].primero);
    $('#sp_mensajes_enviados').text(data.totalRecibidosEnviados[0].cantidad);

    //Cuarta Columna
    $('#sp_iniciado_por').text((data.chatCliente[0].iniciado_por == 'visitor')? 'Cliente':'Operador');
    $('#sp_canal').text((data.chatCliente[0].canal == '334' ? 'cobranzas' : 'ventas')+" - "+$(".list-group-item.selection_chat").closest('.div_chat').attr('data-nombre_operador'));

}

$(document).on('change','select[name="selectCanal"]', function(){
	$('div#comunication_container').remove();
});

// Buscar y filtrar según la opción del Select 
$('body').on('click', '#btn_buscar', function () {
    let seleccion = $("#select_buscar").val();
    let txtBuscar = $("#inp_buscar").val();
    let operador = $("#select_operador").val();
    if (seleccion == '') {
        Swal.fire("¡Atención!", "Debe seleccionar el criterio de búsqueda", "error");
        return null;
    }
    if (txtBuscar == '') {
        Swal.fire("¡Atención!", "Debe indicar lo que desea buscar", "error");
        return null;
    }
    
    if($('#comunication_container').length > 0){
        $('#comunication_container').remove();
    }

    strBuscar = txtBuscar;
    limpiaCampo();
    $("#ul_chat").html('');
    $("#btn_operador_seleccionado").html('');
    $("#ul_operadores").html('');
    $("#selectCanal").val('');
    id_chat = 0;
    id_operador = 0;
    filtro = '';

    $.ajax({
        dataType: "JSON",
        data: {
            "filtro": seleccion,
            "txtBuscar": txtBuscar,
            'operador': operador,
        },
        url: base_url + 'whatsapp/getTelTxtDoc',
        type: 'POST',
        beforeSend: function() {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#main").html(loading);
            $('#modalLoading').modal("show");
        }
    })
    .done(function(respuesta){
        render_chat_list(respuesta, filtro);
        /*$('#modalLoading').modal("hide");
    
        if (respuesta.chatOperador.length > 0) {
            let ulChat = document.getElementById("ul_chat");
            let html = '';
            respuesta.chatOperador.map((chat) => {
                html = html + `
                <div class="div_chat" data-canal_chat="${chat.canal.substr(-3)}" data-id_chat="${chat.id}" data-id_operador="${chat.id_operador}" data-nombre_operador="${chat.nombre_apellido}">
                    ${chat.id == id_chat
                        ? '<button class="list-group-item selection_chat" style="background-color: #f4f3f3; color: #cd547e; font-weight: bold; font-family: Comic Sans MS;">'
                        : '<button class="list-group-item" style="background-color: #f4f3f3; color: #cd547e; font-weight: bold; font-family: Comic Sans MS;">'
                    }
                        ${(chat.sin_leer == 1 && chat.status_chat == 'activo') ? '<span class="badge" style="background-color: #cd547e"><i class="fa fa-comment"></i></span>' : ''}
                        ${chat.nombres} ${chat.apellidos} <span class="badge status_span" style="background-color: #f4f3f3; color: #cd547e;">${chat.status_chat}</span>
                        <div class="row">
                            <div class="col-md-8 text-muted" style="font-family: Arial; font-size: 12px;">
                                ${chat.ultimo_mensaje.substr(0, 30)}...
                            </div>
                            <div class="col-md-4 text-muted text-right" style="font-family: Arial; font-size: 12px; margin-top: 5px">
                                ${chat.ultima_hora}
                            </div>
                        </div>
                    </button>
                </div>`
            });
            ulChat.innerHTML = html;
        } else {
            $('#graphContainer').hide();
            limpiaCampo();
            let html = `
                <div class="text-center">
                    <h4 style="font-weight: bold; font-family: Comic Sans MS;">Operador sin chats asignados...</h4>
                </div>
            `
            $("#ul_chat").html(html);
            id_operador = 0;
            id_chat = 0;
            filtro = '';
        }*/
    })
    .fail(function(xhr) {
        $('#modalLoading').modal("hide");
        Swal.fire("¡Atención!", 
            `readyState: ${xhr.readyState}
                status: ${xhr.status}
                responseText: ${xhr.responseText}`,
            "error"
        );
    });
});






/********************************************/
/*** Al escoger un Chat del panel derecho ***/
/********************************************/
$('body').on('click', '.div_chat', function () {
    
    $('#paginacion').val(0); 
    
    id_chat = $(this).attr('data-id_chat');
    let canal  = $(this).data('canal_chat');

    
    $.ajax({
        url:  base_url + 'cargarChatComponent/'+id_chat,
        type: 'GET',

    }).done(function (response) {
        
            $('#slot-1').html(response);

            //$("div.main-menu.main-menu-h, div.main-menu.main-menu-h  .main-body").css('height',$("#panel_mensajes").height()+'px');
            $(".row.main-body").css('max-height',$("#panel_mensajes").height()-130);           
            $(".row.main-body").css('height',$("#panel_mensajes").height()-130);           
            //  debugger;
            cargar(id_chat, canal);
            
        
    })
     
   
    /*** Demarca el chat seleccionado ***/
    $('button').removeClass("selection_chat");
    $(this).children('button').addClass("selection_chat");
    limpiaCampo();
    
    
    
});


// Limpia los campos del panel central 
const limpiaCampo = () => {
    $("#ul_detalle_chat").html('');
    $("#sp_nombre_cliente").text('');
    $("#sp_documento").text('');
    $("#sp_telefono").text('');
    $("#sp_iniciado_por").text('');
    $("#sp_ultimo_mensaje").text('');
    $("#sp_mensajes_recibidos").text('');
    $("#sp_mensajes_enviados").text('');
    $("#sp_primer_mensaje_recibido").text('');
    //$("#sp_ultimo_mensaje_recibido").text('');
    $("#sp_primer_mensaje_enviado").text('');
    //$("#sp_ultimo_mensaje_enviado").text('');
}

// Switch que activa o desactiva la recarga automática o no de los panel izquierda y derecha 
$('body').on('click', '#myonoffswitch', function () {
    if($(this).prop('checked')) {
        /*** Volver a refrescar los datos cada 2 Minutos ***/
        interval2 = window.setInterval(function() {
            chats(id_operador, filtro); 
        }, 120000);
        /*** Volver a refrescar los datos cada 5 Minutos ***/
        interval5 = window.setInterval(function() {
            operadores(isInterval = true); 
        }, 300000);
    } else {
        window.clearInterval(interval2);
        window.clearInterval(interval5);
    }
});

