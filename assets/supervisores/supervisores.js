
var cuotas;
var index = -1;
var tablaPagos;
var modulos = [];
var user_modulos = [];
$(document).ready(function () {
    base_url = $("input#base_url").val();
    $("#cargando").css("display", "none");
    getCantImputacionesRealizadas();
    
});
$('#slc-ausencia').change('on', function (params) {
    getAusencias();
});

var _mSelect2 = $(".select2").select2();

function limpiarFormulario() {
    $(".select2")
        .val(null)
        .trigger("change");
    document.getElementById("formDatosBasicos").reset();
}

function formatDate(input) {
    var datePart = input.match(/\d+/g),
        year = datePart[0],
        month = datePart[1],
        day = datePart[2];
    return day + "/" + month + "/" + year;
}
function vistaGeneraCampaniaManual() {
    $("#main-ausencias").hide();
    base_url =
        $("input#base_url").val() + "supervisores/Supervisores/vistaGeneraCampaniaManual";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}
function vistaGeneraCampania() {
    $("#main-ausencias").hide();
    $("#tp_Beneficiarios")
        .DataTable()
        .ajax.reload();
    base_url =
        $("input#base_url").val() + "supervisores/Supervisores/vistaGeneraCampania";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}

function VistaConfigCentrales() {
    $("#main-ausencias").hide();
    base_url =
        $("input#base_url").val() + "supervisores/Supervisores/VistaConfigCentrales";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}
/*funcion vieja que debe ser modificada porque retorna la vista completa*/

function VistaFixPayment() {
    $("#main-ausencias").hide();
    base_url =
        $("input#base_url").val() + "supervisores/Supervisores/VistaFixPayment";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}

function VistaCampaniasSMS() {
    $("#main-ausencias").hide();
	
    base_url = $("input#base_url").val() + "supervisores/Supervisores/VistaCampaniasSMS";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}

function vistaGastos() {
    $("#main-ausencias").hide();
    $("#tp_Gastos")
        .DataTable()
        .ajax.reload();
    base_url = $("input#base_url").val() + "operaciones/Operaciones/vistaGastos";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            initTablaGastos();
            $("#cargando").css("display", "none");
        }
    });
}




/**
 * SOLICITAR IMPUTACION
 */
function vistaSolicitarImputacion(){
    $("#main-ausencias").hide();
    let base_url = $("input#base_url").val() + "supervisores/Supervisores/vistaSolicitarImputacion";
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
                $("#main").html(response);
                $("#reset-cliente").click('on', function (){        $("#search-cliente").val('');                         });
                $("#buscar-cliente").click('on', function (){       buscarCliente($("#search-cliente").val());            });
                $('.daterangepicker').css("display", "none");
        }
    });
}

/**
 * SOLICITAR DEVOLUCION
 */
function vistaSolicitarDevolucion(){
    $("#main-ausencias").hide();
    let base_url = $("input#base_url").val() + "supervisores/Supervisores/vistaSolicitarDevolucion";
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
                $("#main").html(response);
                $("#reset-cliente").click('on', function (){        $("#search-cliente").val('');                         });
                $("#buscar-cliente").click('on', function (){       buscarClienteDevolucion($("#search-cliente").val());            });
                $('.daterangepicker').css("display", "none");
        }
    });
}



function initTablePrecargaSolicitudDevolucion(){
    let ajax = {
        'type' :"GET",
        'url' : $("input#base_url").val()+"api/solicitud/getSolicitudesDevolucion?estado=3",
    }
    let columns = [
        {
            "data": null,
            "render": function(data, type, row, meta){
                return moment(data.fecha).format('DD/MM/YYYY')
            }
        },
        {
            "data": "hora",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "solicitado",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "documento",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": null,
            "render": function(data, type, row, meta){
                return (data.nombres+' '+data.apellidos);
            }
        },
        {
            "data" : "comentario"
        },
        {
            "data": null,
            "render": function(data, type, row, meta){
                if(data.estado == 1){
					return '<p class="text-success">PROCESADO</p>';
				}else if(data.estado == 3){
					return '<p class="text-warning">PRECARGA</p>';
				}else if(data.estado == 0 || data.estado == undefined) {
                    return '<p class="text-info">PENDIENTE</p>';
                }
               
            }
        },
        {
            "data" : null,
            "render": function(data, type, row, meta ){
                var buttonUp = "<div>"+ 
                                    "<button  class='btn btn-xs btn-primary alinear-centro' type='button' id='"+data.id+"' onclick='cargarInfoDevolucion("+data.id_cliente+","+data.id+", true )' title='Ver Solicitud'><i class='fa fa-eye'></i></button></div>";
                                    
                return buttonUp; 
            }
        }
    ];
    TablaPaginada('tbl_solicitud_devolucion_precarga', 0, 'asc', 1, 'asc', ajax, columns );
    
}

function buscarClienteDevolucion(documento){
    if (documento.trim().length > 0) {
        
        let base_url = $("input#base_url").val() + "api/solicitud/consultarCliente/"+documento;
        $.ajax({
            type: "GET",
            url: base_url,
            success: function (response) {
                if (response.status.ok){
                    $("#box_client").removeClass('hide');
                    $('#inp_id_cliente').val(response.data.id_cliente);
                    $('#nombre_cliente').text(response.data.nombre + '-CC:' + response.data.documento);
                    $('#inp_id_solicitud').val(response.data.id_solicitud);
                    let tblBodyImputacion = document.getElementById("tbl_body_devolucion");
                    let html = '';
                    html = html + `
                    <tr>
                        <td>${response.data.id}</td>
                        <td>${response.data.documento}</td>
                        <td>${response.data.nombres} ${response.data.apellidos}</td>
                        <td>
                            <a id="aSolicitar" 
                                class="btn btn-primary btn-xs"
                                title="Solicitar Imputación"
                                id_cliente="${response.data.id}"
                                cliente="${response.data.nombres} ${response.data.apellidos}"
                                onclick="gestionarDevolucion(${response.data.id})"
                            >
                                <i class="fa fa-upload"></i>
                            </a>
                        </td>
                    </tr>`
                    tblBodyImputacion.innerHTML = html;
                }else{
                    Swal.fire({
                        title: "Ups!",
                        text: response.message,
                        icon: 'error'
                    });
                }
    
                
            }
        });
    }else{
        Swal.fire({
            title: "Documento invalido",
            text: 'Ingrese un número de documento valido',
            icon: 'error'
        });
    }
}

function gestionarDevolucion(id_cliente){
    // Desabilitar el elmento con el id aSolicitar
    $("#aSolicitar").attr('disabled', true);
    let base_url = $("input#base_url").val() + "api/solicitud/consultarDevolucion/"+id_cliente;
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
            if (response.status.ok){
                if(!response.devolucion){
                    swal.fire({
                        title: "¡Cliente sin saldo a devolver!",
                        text: "¿Desea continuar?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si, continuar",
                        cancelButtonText: "No, cancelar"
                    })
                    .then(function(result) {
                        if (result.value) {
                            $("#aSolicitar").attr('disabled', false);
                            cargarInfoDevolucion(id_cliente);
                        }
                    });
                } else{
                    $("#aSolicitar").attr('disabled', false);
                    cargarInfoDevolucion(id_cliente, null, false, true);
                }
            }else{
                $("#aSolicitar").attr('disabled', false);
                Swal.fire({
                    title: "Ups!",
                    text: response.message,
                    icon: 'error'
                });
            }

            
        }
    });
    
}

paginacion = 0
function cargarInfoDevolucion(id_cliente, id_devolucion = null, precarga = false, is_disabled = null){

    // deshabilitar el elemento con el id que es igual a id_devolucion
    if (id_devolucion != null){
        document.getElementById(id_devolucion).disabled = true;   
    }
    
    let data={};
    $(".procesado").hide();
    $("#myModalDevolucion #enviar").show();
    $("#myModalDevolucion #descartar").hide();
    $("#myModalDevolucion #listdevoluciones").hide();
    $("#myModalDevolucion .cargaComprobante a.btn").removeClass('disabled');
    $("#myModalDevolucion .cargaComprobante input").prop('disabled', false);
    $("#myModalDevolucion select").prop('disabled', false);
    $("#myModalDevolucion #precarga").val(precarga);
    $("#myModalDevolucion #id_devolucion").val(id_devolucion);

    let base_url = $("input#base_url").val() + "api/solicitud/consultarDatosDevolucion/"+id_cliente;
    if(id_devolucion != null && id_devolucion > 0){
        data = {'id_devolucion':id_devolucion, 'precarga': precarga};
    }
    $.ajax({
        type: "GET",
        url: base_url,
        data: data,
        success: function (response) {
            if (response.status.ok) {
                
                if (id_devolucion != null){
                    document.getElementById(id_devolucion).disabled = false;
                }
                $('#myModalDevolucion #documento').val(response.data.cliente[0].documento);
                $('#myModalDevolucion #nombres').val(response.data.cliente[0].nombres+' '+response.data.cliente[0].apellidos);
                $('#myModalDevolucion #banco').val(response.data.cliente[0].Nombre_Banco);
                $('#myModalDevolucion #tipo').val(response.data.cliente[0].Nombre_TipoCuenta);
                $('#myModalDevolucion #cuenta').val(response.data.cliente[0].numero_cuenta);
                $('#myModalDevolucion #id_cliente').val(id_cliente);

                $("#tabla-pagos").dataTable().fnDestroy();          
                $("#tabla-afterdev").dataTable().fnDestroy();          
                $('#myModalDevolucion #tabla-pagos tbody').html("");
                $('#myModalDevolucion #tabla-afterdev tbody').html("");
                $('#myModalDevolucion #tabla-comprobantes tbody').html("");
                $('#myModalDevolucion #comprobante').val("");
                $('#myModalDevolucion').modal('hide');

                let pagos = response.data.pagos;
                $.each( pagos, function( index, value ) {
                    $('#myModalDevolucion #tabla-pagos tbody').append(
                        '<tr><td>'+
                        value.fecha_pago+'</td> <td>'+
                        value.monto+'</td> <td>'+
                        value.medio_pago+'</td> <td>'+
                        ((value.referencia_externa != null)? value.referencia_externa:'')+'</td> <td>'+
                        ((value.referencia_interna != null)? value.referencia_interna:'')+'</td> <td>'+
                        ((value.estado ==1)? 'Cobro realizado':'No cobrado')+
                        '</td> <td class="text-center"> '+((value.estado ==1)? '<div class="checkbox" style="margin:0px;"> <label><input type="checkbox" id="'+value.id+'" data-id_pago="'+value.id+'" value="'+value.monto+'"></label> </div>':'')+'</td> </tr>');
                });
                
               
                // escuchar el evento on click del boton .swal2-cancel

                $(document).ready(function(){
                    $("#myModalDevolucion .swal2-cancel").click(function(){
                        document.querySelector('#enviar').disabled = false;
                    });
                });

                

                if($('#myModalDevolucion #tabla-pagos input[type="checkbox"]:checked').length == 0){
                    document.querySelector('#enviar').disabled = true;
                }                
                
                // ver si se selecciona algun imput checkbox y volver a habilitar el boton enviar
                $('#myModalDevolucion #tabla-pagos input[type="checkbox"]').change(function(){
                    if(($('#myModalDevolucion #tabla-pagos input[type="checkbox"]:checked').length != 0)){
                        document.querySelector('#enviar').disabled = false;
                        document.getElementById("monto").addEventListener("input", function(event){
                            if($('#myModalDevolucion #monto').val() != '0')       
                            {
                                document.querySelector('#enviar').disabled = false;
                            }else {
                                document.querySelector('#enviar').disabled = true;
                            }
        
                        });
                    } else {
                        document.querySelector('#enviar').disabled = true;  
                    }
                });
                
                
                $('#myModalDevolucion input:checkbox').prop("checked", true );
                $('#myModalDevolucion input:checkbox').prop("disabled", true);
                
                is_disabled ? document.querySelector('#enviar').disabled = false : document.querySelector('#enviar').disabled = true;
                $('#myModalDevolucion #forma').val("TOTAL");
                
                tablaPagos = $('#myModalDevolucion #tabla-pagos').DataTable({"pageLength": 4, sDom: 'rt'});
                $("#monto").val(setTotalMontos());
                tablaPagos.rows().nodes().to$().find('input:checkbox').each(function(){ 
                    $(this).on("change",function (){
                        $("#monto").val(setTotalMontos());
                    });
                });
                $('#myModalDevolucion #monto').prop("readOnly", true);

                
                if(typeof(response.data.devolucion) != 'undefined'){
                    $("#myModalDevolucion .procesado").show();
                    $("#myModalDevolucion input").prop('readOnly', true);
                    $("#myModalDevolucion select").prop('disabled', true);
                    $("#myModalDevolucion .cargaComprobante a.btn").addClass('disabled', true);
                    $("#myModalDevolucion .cargaComprobante input").prop('disabled', true);
                    $("#myModalDevolucion #enviar").hide();

                    $('#myModalDevolucion #tabla-comprobantes-devolucion tbody').html('');
				    $('#myModalDevolucion #monto-devuelto').val(formatNumber(response.data.devolucion[0].monto_devolver,2));
                    $('#myModalDevolucion #nombreApellido').val(response.data.devolucion[0].solicitado);
                    $('#myModalDevolucion #fecha').val((response.data.devolucion[0].fecha != null)? moment(response.data.devolucion[0].fecha).format('DD/MM/YYYY'):'');+
                    $('#myModalDevolucion #monto-devuelto').val(formatNumber(response.data.devolucion[0].monto_devuelto,2));
					$('#myModalDevolucion #comentario').val(response.data.devolucion[0].comentario);
					$('#myModalDevolucion #resultado').val(response.data.devolucion[0].resultado);
                    $('#myModalDevolucion input:checkbox').prop("checked", false);

                    $.each( response.data.pagosDevolucion, function( index, pago ) {
                        $('#myModalDevolucion input:checkbox#'+pago.id_pago).prop("checked", true);
                    });
                    $("#monto").val(setTotalMontos());

                    let comprobantes = response.data.comprobantes;
                    $.each( comprobantes, function( index, comprobante ) {
                        comp =comprobante.comprobante; 
                        $("#myModalDevolucion #tabla-comprobantes tbody").append(
                            '<tr><td>'+(comp.substring(comp.lastIndexOf('/') + 1))+'</td>' + 
                            '<td class="text-center"><a href="'+$("input#base_url").val()+comp.substring(1)+'" target="_blank"  class="btn btn-primary btn-xs view"><i class="fa fa-download"></i></a></td>' + 
                            '<td class="text-center"><button typo="button" onclick="showComprobante(\''+comp.substring(1)+'\')" target="_blank"  class="btn btn-primary btn-xs view"><i class="fa fa-eye"></i></button></td></tr>');
                    });

                    let comprobantesDevolucion = response.data.comprobantesDevolucion;
                    $.each( comprobantesDevolucion, function( index, comprobanteDevolucion ) {
                        comp = comprobanteDevolucion.comprobante;
                        $("#myModalDevolucion #tabla-comprobantes-devolucion tbody").append(
                            '<tr><td>'+(comp.substring(comp.lastIndexOf('/') + 1))+'</td>' + 
                            '<td class="text-center"><a href="'+$("input#base_url").val()+comp.substring(1)+'" target="_blank"  class="btn btn-primary btn-xs view"><i class="fa fa-download"></i></a></td>' + 
                            '<td class="text-center"><button typo="button" onclick="showComprobante(\''+comp.substring(1)+'\')" target="_blank"  class="btn btn-primary btn-xs view"><i class="fa fa-eye"></i></button></td></tr>');
                    });
                }
                if (precarga) {
                    $('#myModalDevolucion #forma').val(response.data.devolucion[0].forma_devolucion);
                    let comprobantes = response.data.comprobantesDevolucionPrecarga;
                    $.each( comprobantes, function( index, comprobante ) {
                        comp =comprobante.comprobante;
                        $("#myModalDevolucion #tabla-comprobantes tbody").append(
                            '<tr><td>'+(comp.substring(comp.lastIndexOf('/') + 1))+'</td>' + 
                            '<td class="text-center"><button typo="button" id="DescargarComprobante" href="'+$("input#base_url").val()+comp.substring(1)+'" target="_blank"  class="btn btn-primary btn-xs view"><i class="fa fa-download"></i></button></td>' + 
                            '<td class="text-center"><button type="button" id="VerComprobante" onclick="showComprobante(\''+comp.substring(1)+'\')" target="_blank"  class="btn btn-primary btn-xs view"><i class="fa fa-eye"></i></button></td></tr>');
                    });
                    $("#myModalDevolucion #listdevoluciones").show();
                    $('#myModalDevolucion .procesado').hide()
                    $("#myModalDevolucion #enviar").show();
                    $("#myModalDevolucion #descartar").show();
                    $('#myModalDevolucion #btn_loadcomprobante').hide()
                    $("#myModalDevolucion #monto").prop("readOnly", false);
                    $("#myModalDevolucion select").prop('disabled', false);
                    SelectformaChange()
                    tablaPagos.rows().nodes().to$().each(function(){ 
                        $(this).each(function(index, elem){
                            if ($(elem).find('td:eq(2)').html() == 'devolucion') {
                                $(elem).find('td:eq(6) input').prop('disabled', true);
                            }
                        })
                    });
                    estatus = {0 : 'Pendiente', 1 : 'Procesado', 2 : 'Procesando', 3 : 'Precarga', 4 : 'rechazada'}
                    if (response.data.devoluciones.length > 0) {
                        $.each( response.data.devoluciones, function( index, value ) {

                            
                            $('#myModalDevolucion #tabla-afterdev tbody').append(
                                '<tr><td>'+
                                value.fecha+'</td> <td>'+
                                value.hora+'</td> <td>'+
                                value.monto_devuelto+'</td> <td>'+
                                estatus[parseInt(value.estado)]+'</td> <td>'+
                                value.resultado+'</td> <td>'+
                                value.fecha_proceso +
                                '</td></tr>');
                        });
                    }
                    tablaafterdev = $('#myModalDevolucion #tabla-afterdev').DataTable({"pageLength": 2, sDom: 'rt'});
                }

                    
                    $('#myModalDevolucion #whatsapp #box_whatsapp').css('height',' 800px')
                    $('#myModalDevolucion #whatsapp #box_whatsapp').html(
                        '<div class="box-body" style="overflow-y: auto; height: 66%">'+
                        '    <div class="tab-pane active" id="timeline" style="padding-top: 40%;">'+
                        '        <div class="loader" id="loader-6"><span></span>\n<span></span>\n<span></span>\n<span></span>\n</div>'+
                        '    </div>'+
                        '</div>'
                    );      

                    $.ajax({
                        url: $("input#base_url").val() + 'solicitud/gestion/whatsapp_paginado/' + response.data.solicitud[0].id + '/0/1/',
                        type: 'GET',
                    })
                    .done(function (response) {
                        $("#whatsapp").html(response);
                        $("#whatsapp #box_whatsapp").css('height', '');
                        $("#whatsapp #box_whatsapp").css('box-shadow', 'none');
                        $("#whatsapp #box_whatsapp").css('margin-bottom', '1px');
                        $("#whatsapp #box_whatsapp .panel-group").css('margin-bottom', '1px');
                        $("#whatsapp #box_whatsapp .panel").css('height', '');
                        $("#whatsapp #box_whatsapp .panel").css('box-shadow', 'none');
                        $("#whatsapp #box_whatsapp .panel").css('margin-bottom', '1px');
                        $("#whatsapp #box_whatsapp > .container .nav").append('<li role="comprobante" class=""><a href="#190" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-picture-o"></i></a></li>')
                        $("#whatsapp #box_whatsapp > .panel .tab-content").append('<div role="tabpanel" class="tab-pane" id="190"><div id="viewcomprobanteActual" style="text-align: center; font-size: 20px;">Seleccione un Comprobante</div></div>')
                    });

                $('#myModalDevolucion').modal({
                    backdrop: 'static',
                    keyboard:false,
                    show: true
                });
            }else{
                Swal.fire({
                    title: "Ups!",
                    text: response.message,
                    icon: 'error'
                });
            }

            
        }
    });
}


showComprobante = (comp) => {
  
    $("#myModalDevolucion #viewcomprobanteActual").html('no images')
    let ext = (comp).split('.')
    if (ext[1] == 'pdf') {
        datafile = '<object class="viewcomprobanteActual" type="application/pdf" data="'+$("#base_url").val() + comp+'" style=" height: -webkit-fill-available; min-height: 70vh; width: 100%;" ></object>';
    }else
        datafile = '<object class="viewcomprobanteActual" data="'+$("#base_url").val() + comp+'" style="max-height:100%; max-width:100%;" ></object>';
    $("#myModalDevolucion #viewcomprobanteActual").html(datafile)
    $('.nav-tabs a[href="#190"]').tab('show');

}

function cargar(posicion,id_solicitud,numero){
    
    $('.accordion-numeros').click(function(e){
        e.preventDefault();
        $(this).addClass('active');
        $(this).siblings().removeClass('active');
    });
   
    $("#"+numero+"188"+".main-menu").scroll(function () {
        if ($(this).scrollTop() === 0) {
            $('.loader').removeClass('hide');
            var paginacion = document.getElementsByClassName('paginacion-188')[posicion]['value'];
            get_mensajes_chat_chat(id_solicitud,numero,paginacion,188);
        }
    });
   
    $("#"+numero+"334"+".main-menu").scroll(function () {        
        if ($(this).scrollTop() === 0) {
            $('.loader').removeClass('hide');
            var paginacion = document.getElementsByClassName('paginacion-334')[posicion]['value'];
            get_mensajes_chat_chat(id_solicitud,numero,paginacion,334);
        }
    });
}

function get_mensajes_chat_chat(id_solicitud,numero,paginacion,gestion) {
    const formData = new FormData();
    formData.append("chat", gestion);
    formData.append("numero", numero);
    formData.append("pagina", paginacion);
    formData.append("id_solicitud", id_solicitud);
    let objDiv = $("#"+numero+[gestion]+".main-menu");
    if (objDiv.length > 0) {
        objDiv = $("#"+numero+[gestion]+".main-menu")[0];
    } else {
        objDiv = null;
    }
    let scrollHeightOld = objDiv.scrollHeight;
    if (paginacion > -1) {
        $.ajax({
            url: base_url + 'solicitud/gestion/api/whatsapp_paginado',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        })
            .done(function (response) {
                let elementos = '';
                if (response.status.ok && typeof (response.chat) != 'undefined') {

                    let chats = response.chat;

                    chats.forEach(chat => {
                        let mensajes = chat.messages;

                        mensajes.forEach(msg => {
                            elementos += '<div class="row chat-message-bot mb-4';
                            elementos += (msg.received == 1) ? ' chat-message-bot' : '';
                            elementos += (msg.sent == 1) ? ' chat-message-user' : '';
                            elementos += '">';

                            elementos += (msg.sent == 1) ? '<div style="display:flex;width:100%;justify-content:flex-end;"></div>' : '';

                            elementos += '<table class="';
                            elementos += (msg.received == 1) ? ' chat-entry-bot' : '';
                            elementos += (msg.sent == 1) ? ' chat-entry-user' : '';
                            elementos += '">';
                            elementos += '<tbody><tr>';
                            elementos += (msg.received == 1) ? ('<td class="davi-icon"><img width="28px" height="28px" src="' + base_url + 'assets/images/icons/customer-problem.svg"></td>') : '';
                            elementos += '<td><div class="bubble"><p class="__msg_body">' + nl2br(msg.body ,false) + '</p>';


                            elementos += (msg.media_url0 && (msg.media_content_type0 == 'image/jpeg' || msg.media_content_type0 == 'image/gif' || msg.media_content_type0 == 'image/png')) ? '<img src="' + msg.media_url0 + '" alt="Mensaje Multimedia" class="message_image mt-4 mb-3 d-block"  style="width: 100%!important;">' : '';
                            elementos += (msg.media_url0 && msg.media_content_type0 == 'application/pdf') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= ' + base_url + '"assets/images/icons/pdf-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
                            elementos += (msg.media_url0 && msg.media_content_type0 == 'text/csv') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= ' + base_url + '"assets/images/icons/excel-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
                            elementos += (msg.media_url0 && (msg.media_content_type0 == 'audio/amr' || msg.media_content_type0 == 'audio/mp4' || msg.media_content_type0 == 'audio/mpeg' || msg.media_content_type0 == 'audio/ogg')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-headphones" aria-hidden="true" style="margin-right:.5rem;"></i> Escuchar audio</a>') : '';
                            elementos += (msg.media_url0 && (msg.media_content_type0 == 'video/3gpp' || msg.media_content_type0 == 'video/mp4')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-eye " aria-hidden="true" style="margin-right:.5rem;"></i> Ver Video</a>') : '';


                            elementos += '</div><div class="message-date">' + moment(msg.fecha_creacion).format('DD-MM-YYYY') + '<br>';
                            elementos += (msg.received == 0 && typeof (msg.nombre_apellido_operador) != 'undefined' && msg.nombre_apellido_operador != null) ? msg.nombre_apellido_operador : '';
                            elementos += (msg.received == 0 && typeof (msg.nombre_apellido_operador) == 'undefined' && typeof (chat.operadores.nombre_apellido) != 'undefined') ? chat.operadores.nombre_apellido : '';
                            elementos += '</div></td>';
                            if (msg.sent == 1) {
                                elementos += '<td class="davi-icon">';
                                if (msg.sms_status === 'queued' || msg.sms_status === 'sent') {
                                    elementos += '<img src="' + base_url + 'assets/images/icons/single-grey.svg" alt="status icon" class="__status_icon">';
                                }
                                if (msg.sms_status === 'delivered') {
                                    elementos += '<img src="' + base_url + 'assets/images/icons/double-grey.svg" alt="status icon" class="__status_icon">';
                                }
                                if (msg.sms_status === 'read') {
                                    elementos += '<img src="' + base_url + 'assets/images/icons/double-blue.svg" alt="status icon" class="__status_icon">';
                                }
                                if (msg.sms_status === 'failed') {
                                    elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
                                }
                                if (msg.sms_status === 'failed') {
                                    elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
                                }

                                elementos += '<img width="28px" height="28px" src="' + base_url + 'assets/images/icons/operator-avatar.svg"></td>';
                            }
                            elementos += '</tr></tbody></table>';

                            elementos += (msg.sent == 1) ? '</div>' : '';

                            elementos += '</div>';

                        });
                    });
                   
                    $('.loader').addClass('hide');

                    if(gestion == '334'){
                        let objDiv = $("#334 .__chat_history_container")
                        for(var i=0;i<objDiv.length;i++){
                            if(objDiv[i]['id'] == numero){
                                $('.loader').addClass('hide');
                                $('#'+numero+ 'welcome-334').prepend(elementos);
                                document.getElementById(numero+'-numero-334').value = response.paginacion;
                            }
                        }
                    }
                    if(gestion =='188'){
                        let objDiv = $("#188 .__chat_history_container")
                        for(var i=0;i<objDiv.length;i++){
                            if(objDiv[i]['id'] == numero){
                                $('.loader').addClass('hide');
                                $('#'+numero+'welcome-188').prepend(elementos);
                                document.getElementById(numero+'-numero-188').value = response.paginacion;
                            }
                        }

                    }
                    let element = $("#"+numero+[gestion]+".main-menu")[0]
                    element.scrollTop = (element.scrollHeight - scrollHeightOld);


                }
                $('.loader').addClass('hide');

            })
            .fail(function () {
            })
            .always(function () {
            });
    } else {
        $('.loader').addClass('hide');
    }

}

function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

function SelectformaChange(){
    if($("#forma").val() == 'TOTAL') {
        $("#monto").prop("readOnly", true);
        tablaPagos.rows().nodes().to$().find('input:checkbox').each(function(){ 
            $(this).prop("disabled", true);
            $(this).prop("checked", true);
        });
        $("#monto").val(setTotalMontos());
   }else{
        $("#monto").prop("readOnly", false);
        tablaPagos.rows().nodes().to$().find('input:checkbox').each(function(){
            $(this).prop("disabled", false);
            $(this).prop("checked", false);
        });
        $("#monto").val(0);
   }
}

function subirComprobante() {
    let file = document.getElementById('comprobante');
    let form = new FormData();

    form.append("file", file.files[0], file.value);
    data = form;

    let settings = {
        "url": base_url + 'api/solicitud/uploadComprobanteDevolucion',
        "method": "POST",
        "timeout": 0,
        "processData": false,
        "mimeType": "multipart/form-data",
        "contentType": false,
        "data": data,
    };

    $.ajax(settings).done(function (response) {
        response = JSON.parse(response);
       if(response.status.ok){
           $("#myModalDevolucion #tabla-comprobantes tbody").append(
               '<tr><td>'+response.nombre+'</td><td class="text-center"><a href="'+base_url+response.url+
               '" target="_blank" data-name="'+response.nombre+'" class="btn btn-primary btn-xs view"><i class="fa fa-eye"></i></a></td><td class="text-center"><a onclick="eliminarComprbante(this,\''+response.nombre+'\')" class="btn btn-danger btn-xs delete" data-name="'+response.nombre+'"><i class="fa fa-times"></i></a></td></tr>');
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
function eliminarComprbante(elemento,nombre) {
    let form = new FormData();

    form.append("file", nombre);
    data = form;

    let settings = {
        "url": base_url + 'api/solicitud/deleteComprobanteDevolucion',
        "method": "POST",
        "timeout": 0,
        "processData": false,
        "mimeType": "multipart/form-data",
        "contentType": false,
        "data": data,
    };

    $.ajax(settings).done(function (response) {
        response = JSON.parse(response);
       if(response.status.ok){
            $(elemento).closest('tr').remove();
       } else{
            Swal.fire("¡Atencion!", "no pudimos remover el comprobante", "error");
       }
    }).fail(function(xhr) {
        Swal.fire("¡Atencion!", "no pudimos remover el comprobante", "error");
    });
}


function enviarSolicitudDevolucion(){
    
    // desactivar el boton con el id enviar
    $("#enviar").attr("disabled", true);
    


    let monto = parseFloat($("#monto").val().split('.').join("").replace(',', '.'));
    let max = parseFloat(setTotalMontos().split('.').join("").replace(',', '.'));
    var bool_value = $("#myModalDevolucion #precarga").val() == "true" ? true : false
    if(monto <= max ){
        swal.fire({
            title: "¿Esta seguro?",
            text: "Si se realiza la devolución el credito cambiará de estado a 'mora' o 'vigente' según corresponda",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#dedede",
            confirmButtonText: "Enviar",
            cancelButtonText: "Cancelar",
            allowOutsideClick: false
            }).then(function (result) {
                if(result.dismiss == 'cancel'){
                        document.querySelector('#enviar').disabled = false;
                }
                if (result.value) {
                    

                    const formData = new FormData();
                
                    let pagos=[];
                    let comprobantes=[];
                    tablaPagos.rows().nodes().to$().find('input:checkbox:checked').each(function(){ pagos.push($(this).data("id_pago"));});

                    formData.append("forma", $("#forma").val());
                    formData.append("id_cliente", $("#id_cliente").val());
                    formData.append("monto", parseFloat($("#monto").val().split('.').join("").replace(',', '.')));
                    formData.append("cuenta", $("#cuenta").val());
                    formData.append("banco", $("#banco").val());
                    formData.append("tipo", $("#myModalDevolucion #tipo").val());
                    formData.append("pagos", pagos);
                    formData.append("comprobantes", comprobantes);
                    formData.append("precarga", bool_value);
    
                    if (bool_value) { 
                        formData.append("id_devolucion", $("#myModalDevolucion #id_devolucion").val());
                        formData.append("estado", 0);
                        $.ajax({
                            url: base_url+'api/solicitud/UpdateSolicitudDevolucion',
                            type: 'POST',
                            data:formData,
                            processData: false,
                            contentType: false,
                        }).done(function(response) {
                            // Vuelvo a activar el boton enviar
                            $("#enviar").attr("disabled", false);
                            if(response.status.ok){
                                Swal.fire('Solicitud de devolución', response.message,'success');
                                $('#myModalDevolucion').modal('hide');
                               
                            }else{
                                $("#enviar").attr("disabled", false);
                                Swal.fire('Solicitud de devolución','No fue posible enviar la solicitud','error');
                            }
                                
                        }).fail(function(xhr) {
                            $("#enviar").attr("disabled", false);
                            Swal.fire('Solicitud de devolución','No fue posible enviar la solicitud','error');
                        });

                    } else {
                        
                        $("#myModalDevolucion #tabla-comprobantes a.view").each(function(){
                            comprobantes.push($(this).data('name'));
                        });    
                        formData.append("comprobantes", comprobantes);
                        $.ajax({
                            url: base_url+'api/solicitud/generarSolicitudDevolucion',
                            type: 'POST',
                            data:formData,
                            processData: false,
                            contentType: false,
                        }).done(function(response) {
                            
                            if(response.status.ok){
                                Swal.fire('Solicitud de devolución', response.message,'success');
                                $('#myModalDevolucion').modal('hide');
                               
                            }else{
                                Swal.fire('Solicitud de devolución','No fue posible enviar la solicitud','error');
                            }
                                
                        }).fail(function(xhr) {
                            Swal.fire('Solicitud de devolución','No fue posible enviar la solicitud','error');
                        });

                    }
                    $("#tbl_solicitud_devolucion_all").DataTable().ajax.reload();
                    $("#tbl_solicitud_devolucion_precarga").DataTable().ajax.reload();
    
                    auxTabla.ajax.reload( function () {
                        $("#buttonDevolucion .badge").html(auxTabla.data().count());
                    });
    
                }
                
            });

    }else{
        swal.fire('Monto incorrecto', 'El monto a devolver puede superar el monto de los pagos seleccionados', 'error')
    }

}


function statusSolicitudDevolucion(){
    // desabilitar el boton con el id descartar
    $("#descartar").attr("disabled", true);
    swal.fire({
        title: "¿Esta seguro?",
        text: "Desea descartar la devolución del credito?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#dedede",
        confirmButtonText: "Enviar",
        cancelButtonText: "Cancelar",
        allowOutsideClick: false

        })
        .then(function (result) {
           
            if(result.dismiss == 'cancel'){
                $("#descartar").attr("disabled", false);
            }
            
            if (result.value) {
                const formData = new FormData();
				
                let base_url = $("input#base_url").val();
                formData.append("estado", 4);
                formData.append("id", $("#myModalDevolucion #id_devolucion").val());
                formData.append("precarga", $("#myModalDevolucion #precarga").val());
                
                $.ajax({
                    url: base_url+'api/solicitud/cambiarEstado',
                    type: 'POST',
                    data:formData,
                    processData: false,
                    contentType: false,
                }).done(function(response) {
                    $("#descartar").attr("disabled", false);
                    if(response.status.ok){
                        Swal.fire('Solicitud de devolución', response.message,'success');
                        $('#myModalDevolucion').modal('hide');
                    }else{
                        Swal.fire('Solicitud de devolución','No fue posible actualizar la solicitud','error');
                    }
                    auxTabla.ajax.reload( function () {
                        $("#buttonDevolucion .badge").html(auxTabla.data().count());
                    });

                }).fail(function(xhr) {
                    $("#descartar").attr("disabled", false);
                    Swal.fire('Solicitud de devolución','No fue posible actualizar la solicitud','error');
                });
                $("#tbl_solicitud_devolucion_all").DataTable().ajax.reload();
                $("#tbl_solicitud_devolucion_precarga").DataTable().ajax.reload();

            }
            
        });
}

function setTotalMontos(){
    var total = 0;
    tablaPagos.rows().nodes().to$().find('input:checkbox:checked').each(function(){ 
        total += isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val());
    });
    
    return formatNumber(total);
}
function buscarCliente(documento){
    if (documento.trim().length > 0) {
        
        let base_url = $("input#base_url").val() + "api/credito/deuda_actual/"+documento;
        $.ajax({
            type: "GET",
            url: base_url,
            success: function (response) {
                if (response.status.ok){
                    $("#box_client").removeClass('hide');
                    // $('#fechaPago').datepicker({
                    //     dateFormat: 'dd/mm/yy'
                    // });
                    $('#inp_id_cliente').val(response.data.id_cliente);
                    $('#nombre_cliente').text(response.data.nombre + '-CC:' + response.data.documento);
                    $('#inp_id_solicitud').val(response.data.id_solicitud);
                    let tblBodyImputacion = document.getElementById("tbl_body_imputacion");
                    let html = '';
                    html = html + `
                    <tr>
                        <td>${response.data.id_cliente}</td>
                        <td>${response.data.documento}</td>
                        <td>${response.data.nombre}</td>
                        <td class="text-danger"><b><span id="deuda">$${formatNumber(response.data.deuda)}</span></b></td>
                        <td>
                            <a id="aSolicitar" 
                                class="btn btn-primary btn-xs"
                                title="Solicitar Imputación"
                                id_cliente="${response.data.id_cliente}"
                                cliente="${response.data.nombre}"
                                id_solicitud="${response.data.id_solicitud}"
                            >
                                <i class="fa fa-upload"></i>
                            </a>
                        </td>
                    </tr>`
                    tblBodyImputacion.innerHTML = html;


                    $("#tbl_solicitud_imputacion_aux tbody").html();
                    let solicitudes = response.data.solicitudes_imputacion;
                    $.each( solicitudes, function( index, value ) {
                        let estado = 0;
                        switch (value.por_procesar) {
                            case '0': estado = 'Por procesar';break;
                            case '1': estado =  'Procesado';break;
                            case '2': estado =  'Anulada';break;
                        }
                        $('#tbl_solicitud_imputacion_aux tbody').append('<tr class="" >'+
                                '<td >'+value.fecha_solicitud+'</td>'+
                                '<td style="">'+value.solicitante+'</td>'+
                                '<td style="">'+((value.fecha_proceso != null )?  value.fecha_proceso : '')+'</td>'+
                                '<td style="">'+((value.resultado != null)? value.resultado : '')+'</td>'+
                                '<td style="">'+value.documento+'</td>'+
                                '<td style="">'+value.nombre+'</td>'+
                                '<td style="">'+moment(value.fecha_pago).format('DD/MM/YYYY')+'</td>'+
                                '<td style="">'+value.monto_pago+'</td>'+
                                '<td style="">'+estado+'</td>'+
                                '<td style="">'+((value.comentario != null)? value.comentario: '')+'</td>'+
                                '<td style=""><div><button type="button" class="btn btn-xs btn-primary btnFormImputacion" id="'+value.id_solicitud_imputacion+'"  title="Ver Comprobante"><i class="fa fa-eye"></i></button></div></td>'+
                            '</tr>');
                            
                            $('#tbl_solicitud_imputacion_aux tbody #'+value.id_solicitud_imputacion).off().on( 'click', function () {
                                // deshavilitar el boton con id value.id_solicitud_imputacion
                                $('#tbl_solicitud_imputacion_aux tbody #'+value.id_solicitud_imputacion).attr('disabled',true);
                                if($(this).hasClass('btnFormImputacion')){
                                    $("#nombre_cliente").html(value.nombre + " - "+" CC:" +value.documento);
                                    clickImputandoPago(value);
                                    $('#tbl_solicitud_imputacion_aux tbody #'+value.id_solicitud_imputacion).attr('disabled',false);
                                }
                            
                            } );
                    });
                    
                    

                }else{
                    Swal.fire({
                        title: "Ups!",
                        text: response.message,
                        icon: 'error'
                    });
                }
    
                
            }
        });
    }else{
        Swal.fire({
            title: "Documento invalido",
            text: 'Ingrese un número de documento valido',
            icon: 'error'
        });
    }
}

/**
 * AJUSTE DE CUENTAS
 */
function vistaAjustarCuenta() {
    $("#main-ausencias").hide();
    let base_url = $("input#base_url").val() + "supervisores/Supervisores/vistaAjustarCuentas";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $(".search-section").show();
        }
    });
}

//busca los creditos que correspondan con el criterio de busqueda
function buscarCreditoCobranza(search, fecha = null , operador = null, criterio=null) {
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

//consuta la informacion del credito seleccionado
function consultar_credito(id_credito) {
    event.preventDefault();

    let base_url = $("#base_url").val();
    var id_cliente = 0;
    var cuota = 0;

    $.ajax({
        url: base_url + 'api/credito/consultar_credito/' + id_credito,
        type: 'GET',
    })
        .done(function (response) {
            if (response != "") {
                if (typeof (response.data[0]) != id_cliente) {
                    id_cliente = response.data[0].id_cliente;
                    cuota = response.data[0].id;
                    
                    consultar_cliente(id_cliente)
                    consultar_creditos(id_cliente);
                    
                    $(".search-section").hide();
                    $(".ajustes").show();

                    //ajustar cuentas
                    $("#close_credito").on('click', function () {
                        $(".ajustes").hide();
                        $(".search-section").show();
                        $("#seccion-descuento").addClass('hide');
                        $("#seccion-pagos").addClass('hide');
                    });
                    /*$(".agregar-detalle-pago").prop("onclick", null).off("click");
                    $(".agregar-detalle-pago").on('click', function (){
                        //$(".nuevo-detalle-pago").removeClass('hide');
                        reprocear(id_credito);

                    });
                    $("#agregar-detalle").prop("onclick", null).off("click");
                    $('#agregar-detalle').click('on', function () {
                        agregarDetallPeago();
                    });*/
                }

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

function cargar_cuota(cuota, key) {
    index = key;
    consultar_pagos(cuota, index);
   
    let montoPendiente = parseFloat(cuotas[index].descuento) + parseFloat(cuotas[index].monto_cobrar);
    if(cuotas[index].monto_cobrar <= 0 )
           montoPendiente = 0;

    $("input#descuento").val(montoPendiente);
    $("#seccion-pagos").removeClass('hide');
    $("#seccion-descuento").removeClass('hide');
    $(".id_cuota").html(cuota);

    $("input#descuento").keyup(function(){
        if($("input#descuento").val().length > 0 && $("input#descuento").val() <= montoPendiente){
            if(!$(".nota-descuento").hasClass('hide'))
                $(".nota-descuento").addClass('hide');
            
            $("#aplicar-descuento").removeClass('disabled');
            $("#old-monto").html(cuotas[index].monto_cobrar);
            $("#new-monto").html(montoPendiente-$("input#descuento").val());
        }else{
            $("#aplicar-descuento").addClass('disabled');
            $(".nota-descuento").removeClass('hide');
        }
    });
    $("#aplicar-descuento").prop("onclick", null).off("click");
    $("#aplicar-descuento").click('on', function (){
        let descuento = $("input#descuento").val();
        aplicarDescuento(descuento, cuota);
    });


    $("#old-monto").html(cuotas[index].monto_cobrar);
    $("#new-monto").html(montoPendiente-$("input#descuento").val());

}
/**
 * aplica el descuent y recalcula los valores del credito correspondiente
 */
function aplicarDescuento( descuento, id_cuota){
    const formData = new FormData();

    formData.append("descuento", descuento);
    formData.append("id_cuota", id_cuota);

    $.ajax({
        url: base_url+'api/ajustes/aplicar_descuento',
        type: 'POST',
        data:formData,
        processData: false,
        contentType: false,
    }).done(function(response) {
        if(response.status.ok){
            
            consultar_creditos(cuotas[0].id_cliente);
            let id_solicitud = $('#inp_id_solicitud').val();
			let id_operador = response.operador;
			let type_contact = 170;
			let comment = "<b>[DESCUENTO APLICADO]</b>"+ 
				"<br> Fecha: " + response.fecha +
                "<br> Monto a pagar anterior: " + response.monto_anterior +
                "<br> Monto descuento: " + response.descuento +
                "<br> Monto con descuento: " + response.total
                
			saveTrack(comment, type_contact, response.solicitud, id_operador);
            consultar_pagos(id_cuota, index);
        }else{
            Swal.fire({
                'title': 'Datos Invalidos',
                'text': '',
                'icon': 'error'
            })
        }
            
    })
    .fail(function(response) {
        //console.log("error");
    })
    .always(function(response) {
        //console.log("complete");
    });
}
/**
 * consulta los descuentos disponibles en la base de datos
 */
function consultar_descuentos(){
    let base_url = $("input#base_url").val() + "api/cobranzas/get_descuentos";

    //descuentos disponibles en la base de datos
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
            if(response.status.ok) {
                let descuentos = response.descuentos;
                let opciones = '<option value="">Seleccione</option>';
                descuentos.forEach(descuento => {
                    opciones +='<option value="'+descuento.id+'">'+descuento.descripcion+'</option>';
                });
                $("#descuento-solventa").html(opciones);
                
            } else {
                Swal.fire({
                    title: "Cliente invalido",
                    text: response.message,
                    icon: error
                })
            }
        }
    });
}
/**
 * consulta los datos de una solicitud
 */
function consultar_cliente(id_cliente){
    let base_url = $("input#base_url").val() + "api/solicitud/consultar_solicitud_cliente/"+id_cliente;
    $.ajax({
        type: "GET",
        url: base_url,
        success: function (response) {
            if(response.status.ok){
                $("#nombre-cliente").html(response.data.nombres + ' ' + response.data.apellidos);
                $("#documento-cliente").html(((response.data.documento!=null)? response.data.documento:''));
                $("#telefono-cliente").html(((response.data.telefono!=null)? response.data.telefono:''));
                $("#mail-cliente").html(((response.data.email!=null)? response.data.email:''));
            } else {
                Swal.fire({
                    title: "Cliente invalido",
                    text: response.message,
                    icon: error
                })
            }
        }
    });
}
/*
 * consulta la lista de todos los creditos asociados a un clinete
 */
function consultar_creditos(id_cliente) {
    let base_url = $("#base_url").val();

    
    $.ajax({
        url: base_url + 'api/credito/get_creditos_cliente/' + id_cliente,
        type: 'GET',
    })
    .done(function (response) {
        $(".creditos table#creditos-cliente tbody").html('');
        if (response.status.ok) {
                let creditos = response.creditos;
                cuotas = creditos;
                creditos.forEach(function (val, key) {

                    $(".creditos table#creditos-cliente tbody").append(
                        "<tr><td>" + val.id_credito + "</td>" +
                        "<td>" + val.id + "</td>" +
                        "<td>" + val.fecha_vencimiento + "</td>" +
                        "<td>" + val.capital + "</td>" +
                        "<td>" + val.interes + "</td>" +
                        "<td>" + val.seguro + "</td>" +
                        "<td>" + val.administracion + "</td>" +
                        "<td>" + val.tecnologia + "</td>" +
                        "<td>" + val.iva + "</td>" +
                        "<td>" + val.monto_cuota + "</td>" +
                        "<td>" + val.dias_atraso + "</td>" +
                        "<td>" + val.interes_mora + "</td>" +
                        "<td>" + val.tecnologia_mora + "</td>" +
                        "<td>" + val.multa_mora + "</td>" +
                        "<td>" + val.descuento + "</td>" +
                        "<td>" + val.monto_cobrar + "</td>" +
                        "<td>" + ((val.monto_cobrado !=null)? val.monto_cobrado:'0')+ "</td>" +
                        "<td>" + ((val.fecha_cobro != null)? val.fecha_cobro:'') + "</td>" +
                        "<td>" + val.estado + "</td>" +
                        "<td>" + val.estado_credito + "</td>" +
                        "<td><a href='#' class='btn btn-primary ajustar-credito' data-index='" + key + "' data-credito_detalle='" + val.id + "'><i class='fa fa-wrench'></i></a>" +
                        "<a href='#' class='btn btn-primary reprocesar-credito' data-index='" + key + "' data-credito='" + val.id_credito + "'><i class='fa fa-spinner'></i></a></td></tr>");
                });

                $(".ajustar-credito").click('on', function () {
                    let cuota = $(this).data('credito_detalle');
                    cargar_cuota(cuota, $(this).data('index'));
                });
                $(".reprocesar-credito").click('on', function () {
                    let credito = $(this).data('credito');
                    reprocesarCredito(credito, $(this).data('index'));
                });

                if(index > -1){
                    $("#old-monto").html(cuotas[index].monto_cobrar);
                    $("#new-monto").html(cuotas[index].monto_cobrar);
                }else{
                    $("#seccion-descuento").addClass('hide');
                }


            } else {
                Swal.fire({
                    title: "ups!",
                    text: 'No fue posible realizar la consulta',
                    icon: 'error'
                });
            }
        })
        .fail(function () {
        })
        .always(function () {
        });
}
function reprocesarCredito(credito, index){
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/ajustes/reprocesar_credito/' + credito,
        type: 'GET',
        dataType: 'json',
    })
    .done(function (response) {
        if(response.status.ok){
            Swal.fire({
                'title': response.message,
                'text': "",
                'icon': 'success'
            });
            consultar_creditos(response.cliente);
             /*** Se realiza el track de gestión ***/
                let id_solicitud = response.solicitud;
                let id_operador = response.operador;
                let type_contact = 170;
                let comment = "<b>[REPROCESAMIENTO CREDITO]</b>"+ 
                    "<br> Fecha: " + response.fecha +
                    "<br> Credito: " + response.credito;
                saveTrack(comment, type_contact, id_solicitud, id_operador);

        } else{
            Swal.fire({
                'title': response.message,
                'text': "",
                'icon': 'error'
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
function consultar_pagos(id_cuota, index) {
    $("#loader-6").removeClass("hide");
    $("#tabla-pagos tbody").html("");

    $(".nuevo-detalle-pago").addClass('hide');
    let base_url = $("#base_url").val();
    
    $.ajax({
        url: base_url + 'api/credito/buscar/pagos/' + id_cuota,
        type: 'GET',
        dataType: 'json',
    })
    .done(function (response) {
        $("#loader-6").addClass("hide");
        
        let pagos = response.pagos;
        let cadena = '<option value="">Seleccione</option>';
        //$("#seccion-pagos").removeClass('hide');
        $("table#tabla-pagos tbody").html('');
        
        pagos.forEach(function (val, key) {
                let readOnly = true;
                if(val.tipo_pago =='transferencia' || val.tipo_pago == 'deposito')
                    readOnly = false;
                cadena += '<option value="'+ val.id + '">'+ val.id + '</option>';
                $("table#tabla-pagos tbody").append(
                    "<tr>" +
                    "<td>" + val.id + "</td>" +
                    "<td>" + ((val.tipo_pago == null)? " ": val.tipo_pago ) + "</td>" +
                    "<td><div class='form-group' style='margin:0px;'><input type='text' class='form-control datepicker ' id='fecha-pago-" + val.id + "' value='" + moment(val.fecha_pago).format('DD-MM-YYYY') + "' readOnly ="+readOnly+" ></div></td>" +
                    "<td><div class='form-group' style='margin:0px;'><input type='number' class='form-control' id='monto-pago-" + val.id + "' value='" + val.monto + "' readOnly ="+readOnly+"></div></td>" +
                    
                    //si la cuota existe en el desglose de pago es editrable, de lo contrario no es editable
                    ((val.desglose != null)? ("<td><div class='form-group' style='margin:0px;'>" + val.id_detalle_credito + "</div></td>"):("<td>" + val.id_detalle_credito + "</td>"))
                    +
                    //monto cuota si la cuota existe en el desglose de pago
                    ((val.desglose != null)? ("<td><div class='form-group' style='margin:0px;'>" + val.monto_cuota + "</div></td>"):("<td class='text-center'>--</td>"))
                    +
                    
                    "<td>" + ((val.referencia != null) ? val.referencia : '') + "</td>" +
                    "<td>" + val.estado_razon + "</td>" +
                    ((val.desglose != null) ? ("<td><div class='form-group' style='margin:0px;'>" + val.tipo + "</div></td>"):("<td class='text-center'>--</td>")) +
                    "<td><a class='btn btn-primary btn-sm' id='save-"+key+"' data-pago='" + val.id + "' data-desglose='" + val.desglose + "' data-cuota-index='" + index + "' ><i class='fa fa-save'></i></a></td>" +
                    "</tr>");



                $('#fecha-pago-' + val.id).datepicker({
                    dateFormat: 'dd-mm-yy',
                    startDate: moment(val.fecha_pago).format('DD-MM-YYYY'),
                    maxDate: new Date()
                });
                $('#save-'+key).click('on', function (){   
                    reImputarPagos(this);
                });


            });
            $('#new-idpago').html(cadena);
        })
        .fail(function (response) {
            //console.log("error");
            $("#loader-6").addClass("hide");

        })
        .always(function (response) {
            //console.log("complete");
        });
}
function reImputarPagos(elemeto){
    let id_pago = $(elemeto).data('pago');
    let id_desglose = $(elemeto).data('desglose');
    let index_cuota = $(elemeto).data('cuota-index');
    
    let fecha = moment($('#fecha-pago-'+id_pago).val(),'DD-MM-YYYY').format('YYYY-MM-DD');
    let monto = $('#monto-pago-'+id_pago).val();
    if(fecha == "Invalid date"){
        fecha="";
    }
    const formData = new FormData();

    /*if(id_desglose != null){
        formData.append("id_cuota", $('#cuota-'+id_desglose).val());
        formData.append("monto_cuota", $('#monto-'+id_desglose).val());
        formData.append("tipo", $('#tipo-'+id_desglose).val());
        formData.append("desglose", id_desglose);
    } */
    formData.append("id_credito", cuotas[index_cuota].id_credito);
    formData.append("id_pago", id_pago);
    formData.append("fecha_pago", fecha);
    formData.append("monto_pago", monto);
    
    $.ajax({
        url: base_url+'api/ajustes/reImputar_pagos',
        type: 'POST',
        data:formData,
        processData: false,
        contentType: false,
    }).done(function(response) {
        if(response.status.ok){
            Swal.fire({
                'title': 'ReImputación',
                'text': response.message,
                'icon': 'success'
            })
            consultar_creditos(cuotas[0].id_cliente);
        }else{
            Swal.fire({
                'title': 'Datos Invalidos',
                'text': response.message,
                'icon': 'error'
            });
        }
            
    })
    .fail(function(response) {
        //console.log("error");
    })
    .always(function(response) {
        //console.log("complete");
    });
}
function mostrarDetallePagos(elementoId) {
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
function formatNumber(numero)
{
    let num = parseFloat(numero).toFixed(2);
    var num_parts = num.toString().split(".");
    num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return num_parts.join(",");
}
function agregarDetallPeago(){
    
    const formData = new FormData();

    formData.append("pago", $("#new-idpago").val());
    formData.append("cuota", $("#new-idcuota").val());
    formData.append("monto", $("#new-montocuota").val());
    formData.append("tipo", $("#new-excedente").val());
    formData.append("id_credito", cuotas[index].id_credito);

    $.ajax({
        url: base_url+'api/ajustes/agregar_detalle_pago',
        type: 'POST',
        data:formData,
        processData: false,
        contentType: false,
    }).done(function(response) {
        if(response.status.ok){
            
            consultar_creditos(cuotas[0].id_cliente);
        }else{
            Swal.fire({
                'title': 'Datos Invalidos',
                'text': '',
                'icon': 'error'
            })
        }
            
    })
    .fail(function(response) {
        //console.log("error");
    })
    .always(function(response) {
        //console.log("complete");
    });
    
    $(".nuevo-detalle-pago select").val('');
    $(".nuevo-detalle-pago input").val(0);
    $(".nuevo-detalle-pago").addClass('hide');
}
/*****************************/
/*** Envío del formulario  ***/
/*****************************/
$('body').on('click', '#aEnviar', function() {
    // capturar y desabilitar el elemento con el id aEnviar
    $('#aEnviar').addClass('disabled');

    let base_url = $("#base_url").val();
    let id_cliente = $("#inp_id_cliente").val();
    let documento = $("#documento").val();
    let referencia = $("#referencia").val();
    let fecha_pago = $("#fechaPago").val();
    let monto_pago = $("#monto").val();
    let medio_pago = $("#medios").val();
    let banco_origen = $("#banco-cliente").val();
    let banco_destino = $("#banco-solventa").val();
    let comprobante = (($("#file").val() == "")? $(".comprobanteActual").data('comprobante'):$("#file").val());
    let precarga = (($(".comprobanteActual").data('comprobante') != "")? 3:0);
    let solicitud = $("#modalSolicitudImputacion #inp_id_solicitud").val();

    let fechaAlt = new Date();
    let fecha = fecha_pago.split("-");
    fechaAlt.setFullYear(fecha[0],fecha[1]-1,fecha[2]);
    var today = new Date();

    if (fechaAlt > today) {
        $('#aEnviar').removeClass('disabled');
        return Swal.fire(
            "¡Atención!",
            "La Fecha no puede ser mayor al día de hoy",
            "error"
        );
    }

    if (!comprobante || comprobante == "") {
        $('#aEnviar').removeClass('disabled');
        return Swal.fire(
            "¡Atención!",
            "Debe seleccionar el Comprobante",
            "error"
        );
    } else if (monto_pago == "0") {
        $('#aEnviar').removeClass('disabled');
        return Swal.fire(
            "¡Atención!",
            "El monto debe ser una cantidad válida",
            "error"
        );
    } else if (medio_pago == "Depósito" || medio_pago == "Transferencia") {
        if (!banco_origen || !banco_destino) {
            $('#aEnviar').removeClass('disabled');
            return Swal.fire(
                "¡Atención!",
                "Debe seleccionar un Banco",
                "error"
            );
        }
    }

	$.ajax({
		dataType: "JSON",
		data: {
            "id_cliente": id_cliente,
            "referencia": referencia,
            "fecha_pago": fecha_pago,
            "monto_pago": monto_pago,
            "medio_pago": medio_pago,
            "banco_origen": banco_origen,
            "banco_destino": banco_destino,
            "comprobante": comprobante,
            "precarga": precarga,
            "por_procesar" : 0,
            "solicitud": solicitud
        },
		method: "POST",
		url: base_url + 'api/credito/enviarSolicitudImputacion',
        
	})
	.done(function(response) {
		if (response.status.ok) {

            let id_solicitud_imputacion = response.id_solicitud_imputacion;
            var comprobantesplit = comprobante.split('/');
            var nombre_comprobante = comprobantesplit[comprobantesplit.length -1];
            var url = $("#base_url").val() + comprobante

            if(precarga != 3 && $("#file").val() != ""){
                let file = document.getElementById('file');
                let form = new FormData();
                
                form.append("file", file.files[0], file.value);
                data = form;

                let settings = {
                    "url": base_url + 'api/credito/uploadComprobante/' + id_solicitud_imputacion,
                    "method": "POST",
                    "timeout": 0,
                    "processData": false,
                    "mimeType": "multipart/form-data",
                    "contentType": false,
                    "data": data
                };

                $.ajax(settings).done(function (response_imputacion) {
                    comprobantesplit =  [];
                    comprobantesplit = response_imputacion.split('/');
                    nombre_comprobante = comprobantesplit[comprobantesplit.length -1];
                    var fecha = new Date(),
                    mes = '' + ("0" + (fecha.getMonth() + 1)).slice(-2);
                    year = fecha.getFullYear();
                    url = $("#base_url").val()+'public/supervisores/comprobantes/'+year+'/'+mes+'/'+nombre_comprobante
                }).fail(function(xhr) {
                    Swal.fire("¡Atencion!", 
                        `readyState: ${xhr.readyState}
                            status: ${xhr.status}
                            responseText: ${xhr.responseText}`,
                        "error"
                    )
                });
            }            
            if (precarga == 3) {       
                sendSmsImp(documento, 1);
            }
            /*** Se realiza el track de gestión ***/
            let id_solicitud = $('#id_solicitud').val();
            let id_operador = response.id_operador;
            let type_contact = 171;
            let comment = "<b>[SOLICITUD IMPUTACIÓN]</b>"+ 
                "<br> Fecha: " + response.fecha_operacion +
                "<br> Monto: " + monto_pago +
                "<br> Referencia: " + referencia +
                "<br> Cliente: " + $("#nombre_cliente").text() +
                "<br> Comprobante: <a href='"+url+"'  target='_blan' >" + nombre_comprobante +'</a>'
            saveTrack(comment, type_contact, id_solicitud, id_operador);
            /*** Se limpian los campos del modal ***/
            $("#referencia").val('');
            $("#inp_id_cliente").val(0);
            $("#fechaPago").val('');
            $("#monto").val(0);
            $("#medios").val('');
            $("#banco-cliente").val('');
            $("#banco-solventa").val('');
            $("#file").val('');
            $("#banco-cliente").attr("disabled", true);
            $("#banco-solventa").attr("disabled", true);
            /*** Se limpia la tabla ***/
            let tblBodyImputacion = document.getElementById("tbl_body_imputacion");
            tblBodyImputacion.innerHTML = '<tr></tr>';
            /*** Se oculta el modal ***/
            $('#modalSolicitudImputacion').modal("hide");
            /*** Se recarga la tabla de solicitudes de imputación ***/
            $("#tbl_solicitud_imputacion_all").DataTable().ajax.reload();
            $("#tbl_precarga_imputacion").DataTable().ajax.reload();
            // Habilitr el botón con el id aEnviar
            Swal.fire("¡Éxito!", 
            '¡Registro y comprobante guardados con éxito!',
            "success"
            );
            $('#aEnviar').removeClass('disabled');

			
		} else {
            let { 
                referencia,
                fecha_pago, 
                monto_pago, 
                medio_pago, 
                id_cliente, 
                comprobante,
                comprobante_exist, 
            } = response.errors;
            Swal.fire("¡Atención!", 
                referencia ||
                fecha_pago || 
                monto_pago || 
                medio_pago || 
                id_cliente || 
                comprobante ||
                comprobante_exist,
                "error"
            );
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
/******************************************************************/
/*** Función que realiza el Track de la solicitud de Imputación ***/
/******************************************************************/
function saveTrack(comment, typeContact, idSolicitude, idOperator)
{
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
        if(response.status.ok)
        {
            
        }
    })
    .fail(function(xhr)
    {
		Swal.fire("Atencion!", 
			`readyState: ${xhr.readyState}
				status: ${xhr.status}
				responseText: ${xhr.responseText}`,
			"error"
		);
	});
}
/*************************************************************************/
/*** la etiqueta ancla para validar el desembolso escogido de la tabla ***/
/*************************************************************************/
$('body').on('click','#tbl_solicitud_imputacion a[id="aSolicitar"]', function() {
    $("#buscar-cliente").click();
    $("#modalSolicitudImputacion .resumen").hide();
    $("#modalSolicitudImputacion #aEnviar").show();
    $("#modalSolicitudImputacion .file-input").show();
    $("#modalSolicitudImputacion .modal-body :input").prop("disabled", false);
    $("#modalSolicitudImputacion .modal-body :input").val("");
    $("#modalSolicitudImputacion .comprobanteActual").attr('href',"");
    $("#modalSolicitudImputacion .comprobanteActual").html("");
    $("#modalSolicitudImputacion .comprobanteActual").data("comprobante","");
    if (parseFloat($("#deuda").text().slice(1)) <= 0) {
        $('#modalDeuda').modal("show");
    } else {
        $('#modalSolicitudImputacion').modal("show");
    }
});
/*****************************************/
/*** Opción SI del modal de Deuda Cero ***/
/*****************************************/
$('body').on('click','#modalDeuda #btnSi', function() {
    $('#modalSolicitudImputacion').modal("show");
});
/*************************************************************/
/*** Validación en cada cambio del Select de Medio de pago ***/
/*************************************************************/
$('body').on('change', '#medios', function () {
	let medio = $("#medios").val();
	if(medio === "Depósito" || medio === "Transferencia") {
        $("#banco-cliente").attr("disabled", false);
        $("#banco-solventa").attr("disabled", false);
	} else {
        $("#banco-cliente").attr("disabled", true);
        $("#banco-solventa").attr("disabled", true);
	};
});
/**********************************************************************/
/*** Obtiene la cantidad de Imputaciones ya realizadas en Tesorería ***/
/**********************************************************************/
function getCantImputacionesRealizadas() {
	let base_url = $("#base_url").val();

	$.ajax({
		type: "GET",
		url: base_url + 'api/credito/cantImputadas',
	})
	.done(function(response) {
		if (response.status.ok) {
			$('#ciario').text(response.cantImputadas);
			$('#comentarios').text(response.cantComentarios);
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
/*****************************************************/
/*** Se inicializa la tabla Solicitud Imputaciones ***/
/*****************************************************/
function initTableSolicitudImputar(){
    let ajax = {
        'type' :"POST",
        'url' : $("input#base_url").val()+"supervisores/Supervisores/buscarSolicitudImputacion",
    }
    let columns = [
        {
            "data": "fecha_solicitud",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "solicitante",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "fecha_proceso",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "resultado",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "documento",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "nombre",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": null,
            "render": function(data, type, row, meta){
                return moment(data.fecha_pago).format('DD/MM/YYYY')
            }
        },
        {
            "data" : "monto_pago"
        },
        {
            "data": null,
            "render": function(data, type, row, meta){
                switch (data.por_procesar) {
                    case '0': return 'Por procesar';
                    case '1': return 'Procesado';
                    case '2': return 'Anulada';
                }
            }
        },
        {
            "data": "comentario"
        },
        {
            "data" : null,
            "render": function(data, type, row, meta ){
                url = $("input#base_url").val() + data
                var buttonUp = "";
                    buttonUp = "<div>"+ 
                                    "<a class='btn btn-xs btn-primary btnFormImputacion' title='Ver Comprobante'>"+
                                        "<i class='fa fa-eye'></i>"+
                                    "</a>";
                                    if (row.por_procesar == 0) {
                                        buttonUp = buttonUp +
                                        "<button class='btn btn-xs btn-danger' id='anularSolicitud' title='Anular Solicitud' onClick='ftnAnunlar()'>"+
                                            "<i class='fa fa-eraser'></i>"+
                                        "</button>";
                                    } 
                    buttonUp = buttonUp + '</div>';
                return buttonUp; 
            }
        }
    ]
    TablaPaginada('tbl_solicitud_imputacion_all', 2, 'asc', '', '', ajax, columns );
    
    /******************************************************************/
    /*** Acción de Imputar en la Tabla de Solicitudes de Imputación ***/
    /******************************************************************/
    $('#tbl_solicitud_imputacion_all tbody').off().on( 'click', 'a', function () {
        var data = $("#tbl_solicitud_imputacion_all").DataTable().row( $(this).parents('tr') ).data();
        if($(this).hasClass('btnFormImputacion')){
            $("#nombre_cliente").html(data.nombre + " - "+" CC:" +data.documento);
            clickImputandoPago(data);
        }
    
    } );
}

function initTablePrecargaImputar(){
    let ajax = {
        'type' :"POST",
        'url' : $("input#base_url").val()+"supervisores/Supervisores/buscarPrecargaImputacion",
    }
    let columns = [
        {
            "data": "fecha_solicitud",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "solicitante",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "documento",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "nombre",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "medio_pago",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": null,
            "render":function(data, type, row, meta ){
                if(data.comprobante != null){
                    let arrNombreArchivo = data.comprobante.split('/');
                    return "<a href='"+$("#base_url").val()+data.comprobante+"' target='_blank'>"+arrNombreArchivo[arrNombreArchivo.length - 1]+"</a>";
                } else{
                    return "";
                }
            }
        },
        {
            "data" : null,
            "render": function(data, type, row, meta ){
                url = $("input#base_url").val() + data
                var buttonUp = "";
                    buttonUp += "<div>" +
                                    "<button typo='button' class='btn btn-xs btn-primary btnFormImputacion' id='"+ data.id_cliente +"' solicitud='" + data.id_solicitud_imputacion + "' title='Ver Comprobante'>" +
                                    "<i class='fa fa-gears'></i></button>";

                    buttonUp += '</div>';
                return buttonUp; 
            }
        }
    ]
    TablaPaginada('tbl_precarga_imputacion', 2, 'asc', '', '', ajax, columns );

    /******************************************************************/
    /*** Acción de Imputar en la Tabla de Solicitudes de Imputación ***/
    /******************************************************************/
    $('#tbl_precarga_imputacion tbody').off().on( 'click', 'button', function () {

        var data = $("#tbl_precarga_imputacion").DataTable().row($(this).parents('tr')).data();
        if($(this).hasClass('btnFormImputacion')){
            $("#nombre_cliente").html(data.nombre + " - "+" CC:" +data.documento);
            $("#modalSolicitudImputacion #cliente").val(data.nombre);

            $("#modalSolicitudImputacion .modal-body :input").val("");
            $("#inp_id_cliente").val(data.id_cliente);
            $("#inp_id_solicitud").val(data.id_solicitud_imputacion);
            $("#id_solicitud_imputacion").val(data.id_solicitud_imputacion);
            $("#id_solicitud").val(data.id_solicitud);
            clickImputandoPago(data);
            GetpagosImputados(data.id_cliente);
            $("#modalSolicitudImputacion #comentario").find("option").remove()        
            $("#modalSolicitudImputacion #comentario").append(new Option("Seleccione...", "", true));
            $("#modalSolicitudImputacion #comentario").append(new Option("Datos Ilegibles", "01"));
            $("#modalSolicitudImputacion #comentario").append(new Option("No corresponden a un abono a nuestras cuentas", "02"));
            $("#modalSolicitudImputacion #comentario").append(new Option("No corresponde a un medio de pago manejado por la empresa", "03"));
            $("#modalSolicitudImputacion #comentario").append(new Option("Cobro de imputación automatica", "04"));
            $("#modalSolicitudImputacion .modal-body :input").prop("disabled", false);            
            $("#modalSolicitudImputacion .comprobanteActual").data('comprobante', data.comprobante);
            $("#modalSolicitudImputacion .comprobanteActual").attr('href', $("#base_url").val() + data.comprobante);
            
            let arrNombreArchivo = data.comprobante.split('/');
            $("#modalSolicitudImputacion .comprobanteActual").html(arrNombreArchivo[arrNombreArchivo.length - 1]);
            $("#modalSolicitudImputacion .resumen").hide();
            $("#modalSolicitudImputacion #aEnviar").show();
            $("#modalSolicitudImputacion #comentario").show();
            $("#modalSolicitudImputacion #m_btnanular").show();
            $("#modalSolicitudImputacion #tbl_pagos_imputacion").show();
            $("#modalSolicitudImputacion .file-input").show();
        }
    } );
}

var clickImputandoPago = function(data){
    // habilitar el elemento con el id #aEnviar
    $('#aEnviar').removeClass('disabled');
    
    $("#modalSolicitudImputacion #id_credito").val(data.id_credito);
    $("#modalSolicitudImputacion #id_cliente").val(data.id_cliente);
    $("#modalSolicitudImputacion #id_detalle_credito").val(data.id_credito_detalle);
    $("#modalSolicitudImputacion #documento").val(data.documento);

        $("#modalSolicitudImputacion #ruta_comprobante").val(data.comprobante);
        $("#modalSolicitudImputacion #viewcomprobanteActual").html('')
        datafile = '';
        let ext = (data.comprobante).split('.')
        if (ext[1] == 'pdf') {
            datafile = '<object class="viewcomprobanteActual"  type="application/pdf" data="'+$("#base_url").val() + data.comprobante+'" style="width: -webkit-fill-available; height: -webkit-fill-available;" ></object>';
        }else
            datafile = '<object class="viewcomprobanteActual" data="'+$("#base_url").val() + data.comprobante+'" style="max-height:100%; max-width:100%;" ></object>';

        $("#modalSolicitudImputacion #viewcomprobanteActual").html(datafile)
        $("#modalSolicitudImputacion #medios").val(data.medio_pago);
        $("#modalSolicitudImputacion #referencia").val(data.referencia);
        $("#modalSolicitudImputacion #fechaPago").val(moment(data.fecha_pago).format("YYYY-MM-DD"));
        $("#modalSolicitudImputacion #monto").val(data.monto_pago);
        $('#modalSolicitudImputacion #banco-cliente option[value="' + data.banco_origen + '"]').prop("selected", true);
        $('#modalSolicitudImputacion #banco-solventa option[value="' + data.banco_destino + '"]').prop("selected", true);

        $("#modalSolicitudImputacion #a_comprobante").attr('href', data.comprobante);
        let arrNombreArchivo = data.comprobante.split('/');

        $("#modalSolicitudImputacion .resumen").html("<p><b>Comprobante: </b><a href='"+$("#base_url").val()+data.comprobante+"'>"+arrNombreArchivo[arrNombreArchivo.length - 1]+"</a></p><p><b>Resultado: </b>"+data.resultado+"</p><p><b>Comentario: </b>"+(data.comentario? data.comentario:"")+"</p>").show();
        $("#modalSolicitudImputacion #aEnviar").hide();
        $("#modalSolicitudImputacion #comentario").hide();
        $("#modalSolicitudImputacion #tbl_pagos_imputacion").hide();
        $("#modalSolicitudImputacion #m_btnanular").hide();
        $("#modalSolicitudImputacion .file-input").hide();
    
    $("#modalSolicitudImputacion .modal-body :input").prop("disabled", true);

	$("#modalSolicitudImputacion").modal({
        backdrop: 'static',
        keyboard: false
    })
}

function GetpagosImputados(id_cliente){
    // Capturar el button con el id
    var btn = document.getElementById(id_cliente);
    // Desabilitar el elemento btn
    btn.disabled = true;
    let ajax = {
        'type' :"POST",
        'url' : $("input#base_url").val()+"supervisores/Supervisores/getPagosImputados",
        dataType : 'json',
        data : {'id_cliente': id_cliente}
    }
    
    let columns = [
        {
            "data": "fecha",
            "render": function(data, type, row, meta){
                return moment(data).format('DD-MM-YYYY HH:mm')
            }
        },
        {
            "data": "medio_pago",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "referencia_externa",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "referencia_interna",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "fecha_pago",
            "render": function(data, type, row, meta){
                return moment(data).format('DD-MM-YYYY HH:mm:ss')
            }
        },
        {
            "data": "monto",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data" : "estado",
            "render": function(data, type, row, meta){
                estado = {1 : 'Aceptada', 2 : 'Rechazada', 3 : 'Pendiente', 4 : 'Fallida', 6 : 'Reversada', 7 : 'Retenida', 8 : 'Iniciada', 9 : 'Expirada', 10 : 'Abandonada', 11 : 'Cancelada', 12 : 'Antifraude'}
                return estado[data];
            }
        },
        {
            "data": "estado_razon"
        }
    ]
    if ( $.fn.DataTable.isDataTable( '#tbl_pagos_imputacion' ) ) 
        $('#tbl_pagos_imputacion').DataTable().destroy();
    TablaPaginada('tbl_pagos_imputacion', 2, 'asc', '', '', ajax, columns, null, null, null, null,null, { sDom: "tr"} );
    // Habilitar el elemento btn
    btn.disabled = false;
}

/***********************************************************/
/*** Llena el modal para la confirmación de la anulación ***/
/***********************************************************/
function ftnAnunlar() {
    $('#tbl_solicitud_imputacion_all tbody').on( 'click', 'button', function () {
        $("#anularSolicitud").attr('disabled', true);
        var data = $("#tbl_solicitud_imputacion_all").DataTable().row( $(this).parents('tr')).data();

        $("#modalAnularSolicitudImputacion #id_solicitud_imputacion").val(data.id_solicitud_imputacion);
        $("#modalAnularSolicitudImputacion #referencia").text(data.referencia);
        $("#modalAnularSolicitudImputacion #fecha_solicitud").text(data.fecha_solicitud);
        $("#modalAnularSolicitudImputacion #fecha_pago").text(data.fecha_pago);
        $("#modalAnularSolicitudImputacion #monto_pago").text(data.monto_pago);
        $("#modalAnularSolicitudImputacion #cliente").text(data.nombre);
        $("#modalAnularSolicitudImputacion").modal('show');
    });
    // Si se hace click en el boton con el id modalCancelarbtn
    $("#modalCancelarbtn").click(function(){
        $("#anularSolicitud").attr('disabled', false);
    });
}

/***********************************************************/
/*** Llena el modal para la confirmación de la anulación whatsapp***/
/***********************************************************/
function AnularSol() {
    // Desabilitar el boton con el id m_btnanular
    $("#m_btnanular").attr('disabled', true);
    let base_url = $("#base_url").val();
    id_solicitud_imputacion = $("#modalSolicitudImputacion #id_solicitud_imputacion").val();
    id_solicitud            = $("#modalSolicitudImputacion #id_solicitud").val();
    medio_pago              = $("#modalSolicitudImputacion #medio_pago").val();
    documento               = $("#modalSolicitudImputacion #documento").val();
    optionAnulacion         = $("#modalSolicitudImputacion #comentario option:selected").val();
    commentAnulacion        = $("#modalSolicitudImputacion #comentario option:selected").text().trim();

    
    if (optionAnulacion == "") {
        $("#m_btnanular").attr('disabled', false);
        return Swal.fire(
            "¡Atención!",
            "Seleccione una Opcion correcta para anular",
            "error"
        );
    }
    
    $.ajax({
		url: base_url + 'api/credito/anularSolicitud/' + id_solicitud_imputacion,
        type: 'POST',
        dataType: 'json',
        data: {
			'comentario': commentAnulacion
		}
    })
    .done(function(response) {
        if(response.status.ok)
        {
            $("#m_btnanular").attr('disabled', false);
            // Habilitar el boton con el id anularSolicitud
            $("#anularSolicitud").attr('disabled', false);

            $("#tbl_solicitud_imputacion_all").DataTable().ajax.reload();
            $("#tbl_precarga_imputacion").DataTable().ajax.reload();
            Swal.fire("¡Información!",
                "¡Solicitud de imputación anulada con éxito!",
                "info");
            
			// /*** Se realiza el track de gestión ***/
			let id_operador = response.id_operador;
			let type_contact = 171;
            let text_track = "";
            switch (medio_pago) {
                case "PSE":
                case "Efecty":
                case "Baloto":
                    text_track = "<b>[COMPROBANTE DE IMPUTACION AUTOMATICA] Proceso automático</b>"+ 
                        "<br> Fecha: " + response.fecha_operacion +
                        "<br> Cliente: " + $("#modalSolicitudImputacion #cliente").text();
                    break;
                default:
                    if (optionAnulacion == '01') {
                        sendSmsImp(documento, 0)
                    }
                    text_track = "<b>[COMPROBANTE NO CUMPLE LOS REQUISITOS] Proceso automático</b>"+ 
                        "<br> Fecha: " + response.fecha_operacion +
                        "<br> Cliente: " + $("#modalSolicitudImputacion #cliente").text();
                    break;
            }
            if (optionAnulacion == '04') {
                sendSmsImp(documento, 2)
            }
			saveTrack(text_track, type_contact, id_solicitud, id_operador);
            $('#modalSolicitudImputacion').modal("hide");
        }
    })
    .fail(function(xhr)
    {
        $("#m_btnanular").attr('disabled', false);
		Swal.fire("Atencion!", 
			`readyState: ${xhr.readyState}
				status: ${xhr.status}
				responseText: ${xhr.responseText}`,
			"error"
		);
	});
   
}

/*********************************************************/
/*** Botón anular del modal que confrima la anulación  ***/
/*********************************************************/
$('body').on('click', "#modalAnularSolicitudImputacion #btnAnular", function () {
    let base_url = $("#base_url").val();
    id_solicitud_imputacion = $("#modalAnularSolicitudImputacion #id_solicitud_imputacion").val();
    $.ajax({
		url: base_url + 'api/credito/anularSolicitud/' + id_solicitud_imputacion,
        type: 'POST',
    })
    .done(function(response) {
        if(response.status.ok)
        {
            $("#tbl_solicitud_imputacion_all").DataTable().ajax.reload();
            Swal.fire("¡Información!",
                "¡Solicitud de imputación anulada con éxito!",
                "info");
        }
    })
    .fail(function(xhr)
    {
		Swal.fire("Atencion!", 
			`readyState: ${xhr.readyState}
				status: ${xhr.status}
				responseText: ${xhr.responseText}`,
			"error"
		);
	});
})

var sendSmsImp = (doc, act) => {
    let formData = new FormData();         
    formData.append("doc", doc);
    formData.append("action",act);
    res = {};
    res.status = false;
    res.data = {};
    $.ajax({
        url     : $("#base_url").val()+'supervisor/msjimputacion',
        type    : "POST",
        data    : formData,
        processData: false,
        contentType: false,
        crossDomain: true,
    }).done((res) => {
        res.status = true;
        res.data = res;
        return res
    }).fail((err) => {
        return err
    })
}

function cargar_select(){
    let base_url = $("#base_url").val();
    $.ajax({
       url:  base_url+'api/ApiSupervisores/BuscarBotonesOperador',
       type: 'POST',
       success:function(respuesta){
           //alert(respuesta);
           var registros = eval(respuesta);
           html ="";
           for (var i = 0; i < registros.length; i++) {
               html +="<option value='"+registros[i]['id']+"''>"+registros[i]['id']+".- "+registros[i]['etiqueta']+"</option>";
           }
           $('#exclusiones').html(html);
       }
   });
}

//Lista campanias crm
function listar_campanias_crm(){
    let ajax = {
        'type' :"GET",
        'url' : $("input#base_url").val()+"supervisores/Supervisores/listar_campanias_get",
    }
    let columnDefs = [
        {
            'targets': [0],
            'createdCell':  function (td, cellData, rowData, row, col) {
                $(td).attr('style', 'width: 3%; text-align:center ;'); 
            }
        },
        {
            'targets': [1,2,3,6,9],
            'createdCell':  function (td, cellData, rowData, row, col) {
                $(td).attr('style', 'text-align: left;'); 
            }
        }
    ];
    let columns = [
        {
            "data": "id",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "descripcion",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "tipo",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "grupo",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "desde",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "hasta",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "accion",
            "render": function(data, type, row, meta){
                if(data == 'IN'){
                    return 'INCLUIR';
                }
                if(data == 'NOT IN'){
                    return 'EXCLUIR';
                }
                if(data == ''){
                    return '';
                }
            }
        },
        {
            "data": "credito_cliente",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "id_exclusion",
            "render": function(data, type, row, meta){ 
                if(data.length > 0){
                    if(data.length > 2){
                        
                        return data;
                       
                    }else{
                        return data;
                    }
                }else{
                    return data;
                }
            }
        },
        {
            "data": "orden",
            "render": function(data, type, row, meta){
                if(data == 1){
                    data ='MENOR A MAYOR';
                }else{
                    data ='MAYOR A MENOR';
                }
                return data
            }
        },
        {
            "data": "asignar",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "re_gestionar",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "estado",
            "render": function(data, type, row, meta){
                if(data == 1){
                    data = 'ACTIVA';
                }else{
                    data = 'INACTIVA';
                }
                return data
            }
        },
        {
        "data": "fecha_hora",
        "render": function(data, type, row, meta){
            return data
            }
        },
        {
            "data" : null,
            "render": function(data, type, row, meta ){
                url = $("input#base_url").val() + data
                var Editar='';
                var Ver ='';
                if(row.estado != 1){
                    var Editar=
					'<a class="btn btn-xs btn-primary" title="Editar campaña" onclick="editar_campania_crm(' +row.id + ');"><i class="fa fa-pencil-square-o" ></i></a>';
                }else{
                    var Ver =
					'<span class="btn btn-xs bg-navy" data-estado="1" title="Visualizar campaña" id="verCampania-'+row.id+'" onclick="loadCampania('+row.id+')"><i class="fa fa-eye"></i></span>';
                }
				var Estado =
					'<a class="btn btn-xs bg-yellow" id="estado" data-estado="0" title="Estado campaña" onclick="CambioEstadoCampania_crm('+row.id+');"><i class="fa fa-exchange" ></i></a>';
				
                    return Ver+" "+Editar+" "+Estado+" ";
            }

        }
        
    ]
    TablaPaginada('table_crm', 2, 'asc', '', '', ajax, columns, columnDefs );
}

function loadCampania(id) {
	document.getElementById('verCampania-'+id).innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
	ver_campania(id);
}
/**
 * Obtiene los campos de de la campania y su configuracion
 * 
 * @returns {Object}
 */
function getCamposCampania() {
	let fields = {};

	fields.exclusiones = $('#exclusiones').val();
	fields.tipo = $('#tipoo').val();
	fields.credito_cliente = $('#credito_cliente').val();
	fields.accion = $('#accion').val();
	fields.grupo = $('#grupo').val();
	fields.orden = $('#orden').val();
	fields.desde = $('#desde').val();
	fields.hasta = $('#hasta').val();
	fields.re_gestionar = $('#re_gestionar').val();
	fields.descripcion = $('#descripcion').val();
	fields.asignar = $('#asignar').val();
	fields.estado = $('#estado').val();
	fields.id = $('#id_campania').val();

	fields.operadores = $("#operadores").val();
	fields.equipo = $("#equipo").val();

	fields.autollamada = $("#autollamada").val();
	fields.templateWhatsapp = $("#templateWhatsapp").val();
	fields.templateSMS = $("#templateSMS").val();
	fields.templateEmail = $("#templateEmail").val();
	fields.canalWhatsapp = $("#canal").val();

	fields.automatico = $("#automatico").is(":checked");
	fields.minutos_gestion = $("#min1").val();
	fields.minutos_extra = $("#minExtra").val();
	fields.cantidad_extensiones = $("#extensiones").val();

	fields.grupoVentas  = $('#grupoVentas').val();
	fields.ventas_alta_cliente_input  = $('#ventas_alta_cliente_input').val();
	fields.ventas_ultimo_otorgamiento_input  = $('#ventas_ultimo_otorgamiento_input').val();
	fields.ventas_cantidad_creditos_input  = $('#ventas_cantidad_creditos_input').val();
	fields.ventas_mayor_atraso_input  = $('#ventas_mayor_atraso_input').val();
	
	fields.equipoQuery = $("#equipoQuery").val();
	
	return fields;
}

/**
 * Valida los campos de la campania y su configuracion
 * 
 * @returns {boolean}
 */
function validacion_campania() {
	let fields = getCamposCampania();

	let ok = true;
	let msj = '';

	if (fields.operadores.length == 0) msj = 'Operadores';

	if (fields.automatico) {
		if (fields.minutos_gestion == 0) msj = 'Minutos 1era Gestion';
		if (fields.minutos_extra == 0) msj = 'Minutos Extra';
		if (fields.cantidad_extensiones == 0) msj = 'Cantidad Extensiones';
	}

	if (fields.templateWhatsapp != 0 && fields.canalWhatsapp == 0) msj = 'Canal Whatsapp';
	if (fields.re_gestionar == '') msj = 'Regestionar'
	if (fields.tipo == '') msj = 'Tipo'
	if (fields.grupo == '') msj = 'Grupo'

	if (fields.accion != '') {
		if (fields.credito_cliente == '') msj = 'Creditos por cliente'
	}

	if (fields.orden == '') msj = 'Orden'

	if (fields.tipo != 'PREVENTIVA') {
		if (fields.desde == '') msj = 'Desde'
		if (fields.hasta == '') msj = 'Hasta'
	}

	if (fields.tipo == 'VENTAS') {
		if (fields.grupoVentas == '') {
			msj = 'Grupo'
		} else {
			if (fields.grupoVentas == 'ALTA_CLIENTE' && fields.ventas_alta_cliente_input == '') {
					msj = 'Mayor o igual a';
			}
			if (fields.grupoVentas == 'ULTIMO_OTORGAMIENTO' && fields.ventas_ultimo_otorgamiento_input == '') {
					msj = 'Mayor o igual a';
			}
			if (fields.grupoVentas == 'CANTIDAD_CREDITOS' && fields.ventas_cantidad_creditos_input == '') {
					msj = 'Mayor o igual a';
			}
			if (fields.grupoVentas == 'MAYOR_ATRASO' && fields.ventas_mayor_atraso_input == '') {
					msj = 'Mayor o igual a';
			}
		}
	}
	
	if (msj !== '') {
		ok = false;
		swal.fire('Error', 'Debe seleccionar un valor en el campo ' + msj, 'error');
	}

	return ok;
}

/**
 * Realiza una prueba de la campania segun las opciones seleccionadas
 */
function probar_campania() {
	let validation = validacion_campania();

	if (validation) {
		let fields = getCamposCampania();
		
		if (fields.desde === '') {
			fields.desde = '0';
		}
		
		if (fields.hasta === '') {
			fields.hasta = '0';
		}
		
		let param = {
			"desde": fields.desde,
			"hasta": fields.hasta,
			"grupo": fields.grupo,
			"orden": fields.orden,
			"tipo": fields.tipo,
			'exclusiones': fields.exclusiones,
			'credito_cliente': fields.credito_cliente,
			'accion': fields.accion,
			'grupoVentas': fields.grupoVentas,
			'ventas_alta_cliente_input': fields.ventas_alta_cliente_input,
			'ventas_ultimo_otorgamiento_input': fields.ventas_ultimo_otorgamiento_input,
			'ventas_cantidad_creditos_input': fields.ventas_cantidad_creditos_input,
			'ventas_mayor_atraso_input': fields.ventas_mayor_atraso_input,
			'equipoQuery': fields.equipoQuery
		};
		
		prueba_campanias_crm(param);
	}
}

/**
 * Crea una campania
 */
function createCampania() {
	let validation = validacion_campania();

	if (validation) {
		let fields = getCamposCampania();

		if (fields.desde === '') {
			fields.desde = '0';
		}

		if (fields.hasta === '') {
			fields.hasta = '0';
		}
		
		if (fields.asignar === '') {
			fields.asignar = '5';
		}

		delete fields.id;
		
		guardarCampania(fields);
	}
}

/**
 * Actualiza una campania
 */
function updateCampania() {
	let validation = validacion_campania();

	if (validation) {
		let fields = getCamposCampania();
		
		fields.canal_whatsapp = fields.canalWhatsapp;
		fields.whatsapp = fields.templateWhatsapp;
		fields.sms = fields.templateSMS;
		fields.mail = fields.templateEmail;
		
		update_campania_crm(fields);
	}
}

/**
 * Crea una campania
 * 
 * @param fields
 */
function guardarCampania(fields) {
	document.getElementById('tipo_btn').value = '';
	$.ajax({
		url: $("input#base_url").val() + 'api/campanias/campania_crm',
		type: 'POST',
		dataType: 'json',
		data: fields
	})
		.done(function (response) {
			if (response.status.ok) {
				swal.fire('Exito', 'Se genero la camapaña', 'success');

				$("#prueba_crm").removeClass("hide");
				$("#result").removeClass("hide");
				$("#result_busqueda").addClass("hide");

				vistaGeneraCampaniaManual();

				document.getElementById('tipo_btn').value = 'cargar';
			}
		})
		.fail(function (xhr) {
			swal.fire('Error', 'Error al generar campaña', 'error');
		});
}

/**
 * Trae casos correspondientes a la configuracion seleccionada de la campania
 * 
 * @param param
 */
function prueba_campanias_crm(param) {
	
	if ($.fn.DataTable.isDataTable("#busqueda_crm")) {
		$("#busqueda_crm").DataTable().clear().draw();
		$("#busqueda_crm").DataTable().destroy();
	}

	let ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/campanias/getCasosCampania/",
        data: param,
	};
	
	let columnDefs = [
		{ 'targets': [0],
			'createdCell': function (td, cellData, rowData, row, col) {
				$(td).addClass('tr');
				$(td).attr('style', 'text-align: left;width: 8%;');
			}
		},
		{ 'targets': [3, 9],
			'createdCell': function (td, cellData, rowData, row, col) {
				$(td).attr('style', 'text-align: left;');
			}
		},
	];

	let columns = [
		{
			"data": "ultima_actividad",
			"render": function (data, type, row, meta) {
				return data
			}
		},
		{
			"data": "id",
			"render": function (data, type, row, meta) {
				return data
			}
		},
		{
			"data": "documento",
			"render": function (data, type, row, meta) {
				return data
			}
		},
		{
			"data": "nombres",
			"render": function (data, type, row, meta) {
				return data
			}
		},
		{
			"data": "monto_prestado",
			"render": function (data, type, row, meta) {
				return data
			}
		},
		{
			"data": "fecha_vencimiento",
			"render": function (data, type, row, meta) {
				return data
			}
		},
		{
			"data": "deuda",
			"render": function (data, type, row, meta) {
				return data
			}
		},
		{
			"data": "dias_atraso",
			"render": function (data, type, row, meta) {
				return data
			}
		},
		{
			"data": "estado",
			"render": function (data, type, row, meta) {
				return data
			}
		},
		{
			"data": "last_track",
			"render": function (data, type, row, meta) {
				return data
			}
		},
	];
	
	$("#result").addClass("hide");
	$("#result_busqueda").removeClass("hide");
	TablaPaginada("busqueda_crm", 0, "asc", "", "", ajax, columns, columnDefs);

	$('#busqueda_crm').DataTable().on("draw", function () {
		if ($(".tr").length > 0) {
			if (document.getElementById('tipo_btn').value == 'editar') {
				$("#update_crm").showInlineBlock();
			} else {
				$("#crear_crm").showInlineBlock();
			}
		} else {
			$("#update_crm").hide();
			$("#crear_crm").hide();
		}
		
		let cantCasos = $('#busqueda_crm').DataTable().data().count();
		$("#cantidadCasos").val(cantCasos);
		calcularCampaniaTiempoEstimado();
		
	})
      
}

/**
 * Calcula y renderiza los tiempos de la campania
 */
function calcularCampaniaTiempoEstimado() {
	let cantCasos = Number($("#cantidadCasos").val());
	let cantOperadores = Number($('#cantOperadores').val());
	let cantOperdoresMaximos = Number($("#cantidadOperadores").val());
	let minutosGestion = Number($('#min1').val());
	let minutosExtra = Number($('#minExtra').val());
	let extensiones = Number($('#extensiones').val());
	let horasLaborales = Number($("#horasLaborales").val());

	let alertCantOperadores = $('#alertCantOperadores');
	if (cantOperadores > cantCasos) {
		alertCantOperadores.show();
		//esto se hace para evitar que calcule tiempos cuando los operadores son mas que los casos
		// Al haber 10 casos de 1 min cada uno. Si selecciono cualquier cantidad de operadores
		// mayor a 10 debe seguir dando 1 minuto.
		cantOperadores = cantCasos;
	} else {
		alertCantOperadores.hide();
	}
	
	let tiempoGestion = minutosAHorasMinutos(minutosGestion * cantCasos, cantOperadores);
	let tiempoExtra = minutosAHorasMinutos(minutosExtra * cantCasos, cantOperadores);
	let totalExtra = minutosAHorasMinutos((minutosGestion + minutosExtra) * cantCasos, cantOperadores);
	let tiempoExtensiones = minutosAHorasMinutos((minutosExtra * extensiones) * cantCasos, cantOperadores);
	
	let auxTotalConExtenciones = (minutosGestion + (minutosExtra * extensiones)) * cantCasos;
	let totalExtensiones = minutosAHorasMinutos(auxTotalConExtenciones, cantOperadores);
	let totalHorasLaborales = minutosAHorasMinutos(auxTotalConExtenciones, cantOperadores, horasLaborales);
	
	let renderGestion = renderDaysHoursMinutes(tiempoGestion);
	$("#calculoTiempoGestion").html(renderGestion);
	$("#TotalTiempoGestion").html(renderGestion)

	$("#calculoTiempoExtra").html(renderDaysHoursMinutes(tiempoExtra));
	$("#TotalTiempoExtra").html(renderDaysHoursMinutes(totalExtra));

	$("#calculoTiempoExtensiones").html(renderDaysHoursMinutes(tiempoExtensiones));
	$("#TotalTiempoExtensiones").html(renderDaysHoursMinutes(totalExtensiones));
	
	$("#totalHorasLaborales").html(renderDaysHoursMinutes(totalHorasLaborales));

	let alertCampania = $(".alert-campania");
	if (totalHorasLaborales.days > 0) {
		alertCampania.show();
	} else {
		alertCampania.hide();
	}
	
}

/**
 * Devuele en formato HH:MM los minutos proporcionados
 *
 * @param minutos
 * @param operadores
 * @param horasPorDia
 * @returns {{}}
 */
function minutosAHorasMinutos(minutos, operadores = 1, horasPorDia = 24) {
	let totalMinutes = Math.floor(minutos / operadores) ;

	let days = Math.floor(totalMinutes / 60 / horasPorDia);
	let hours = Math.floor(totalMinutes / 60) - (days * horasPorDia);
	let minutes = totalMinutes % 60;

	return {
		days: days,
		hours: hours,
		minutes: minutes
	};
}

/**
 * Renderiza los valores obtenidos de minutosAHorasMinutos
 * 
 * @param times
 * @returns {string}
 */
function renderDaysHoursMinutes(times) {
	let render = '';
	if (times.days > 0) {
		render = times.days + " Dias " + ('0' + times.hours).slice(-2) + ":" + ('0' + times.minutes).slice(-2) + " Horas";
	} else {
		render =  ('0' + times.hours).slice(-2) + ":" + ('0' + times.minutes).slice(-2) + " Horas";
	}
	
	return render;
}

function getCantidadOperadores(callback) {
	let base_url = $("input#base_url").val();
	$.ajax({
		url: base_url + 'api/campanias/getOperadoresPorTipoYEquipo',
		type: 'POST',
		data: {'tipoOperadores': $("#operadores").val(), 'equipo': $("#equipo").val()},
	})
		.done(function (data) {
			let maxOperadores = data.length;
			$("#cantidadOperadores").val(maxOperadores);
			$("#cantOperadores").val(1);
			$("#cantOperadoresNumero").html(1 +"/"+ maxOperadores);
			$("#cantOperadores").attr('max', maxOperadores);
			callback();
		});
}

$.fn.showInlineBlock = function () {
	return this.css('display', 'inline-block');
};

//Cambia estado campania crm
function CambioEstadoCampania_crm(id_reg){
    let base_url = $("input#base_url").val();
    $.ajax({
        url: base_url + 'api/campanias/getEstadoCampania',
        type: 'POST',
        dataType: 'json',
        data: { "id_campania": id_reg}

    })
    .done(function(response) {
        if(response.data === '1') {
							swal.fire({
									title: "Existen operadores activos en la campaña.",
									text: "Si le cambia el estado los eliminara a todos de la campaña y deberán volver a activarse.",
									type: "warning",
									showCancelButton: true,
									confirmButtonColor: "#3085d6",
									cancelButtonColor: "#d33",
									cancelButtonText: "No",
									confirmButtonText: "Si"
							}).then(function (result) {
									if (result.value) {
											$.ajax({
											url: base_url + 'api/campanias/desactivarCampania',
											type: 'POST',
											dataType: 'json',
											data: { "id_reg": id_reg}
											
									})
											.done(function (response) {
													swal.fire('Exito','El estado fue actualizado', 'success');
													vistaGeneraCampaniaManual();
											})
											.fail(function (response) {
													swal.fire('Ups..','Error inserperado','error');
					
											})
									}
							}) 
        } else {
					$.ajax({
						url: base_url + 'api/campanias/activarCampaniaManual',
						type: 'POST',
						dataType: 'json',
						data: { "id_reg": id_reg}
					})
						.done(function (response) {
							swal.fire('Exito','El estado fue actualizado', 'success');
							vistaGeneraCampaniaManual();
						})
				}
				
        
    }).fail(function(xhr){
        swal.fire('Upss..','Error inesperado','error');   
    });


}
//Edita campania crm
function editar_campania_crm(id_reg){
    document.getElementById('tipo_btn').value ='';
    $('#estado').attr("disabled", "true");
    let base_url = $("input#base_url").val();
    $.ajax({
        type: "POST",
        url: base_url + 'api/campanias/campania_campos',
        data : {'id_reg':id_reg}
    })
    .done(function(response) {
        if (response.status.ok) {
            $('#exclusiones').empty()
            document.getElementById('tipo_btn').value ='editar';
            $("#crear_crm").hide();
            $("#update_crm").showInlineBlock();
            $("#campaniaContainer").show();
						$("#nuevaCampania").hide();
						$("#cerrarCampania").show();
            document.getElementById('id_campania').value = response.result[0]['id'];
            document.getElementById('descripcion').value = response.result[0]['descripcion'];
            document.getElementById('tipoo').value = response.result[0]['tipo'];
            document.getElementById('grupo').value = response.result[0]['grupo'];
            document.getElementById('orden').value = response.result[0]['orden'];
            document.getElementById('desde').value = response.result[0]['desde'];
            document.getElementById('hasta').value = response.result[0]['hasta'];
            document.getElementById('asignar').value = response.result[0]['asignar'];
            document.getElementById('re_gestionar').value = response.result[0]['re_gestionar'];
            document.getElementById('autollamada').value = response.result[0]['autollamada'];
            document.getElementById('estado').value = response.result[0]['estado'];
            document.getElementById('exclusiones').value = response.result[0]['id_exclusion'];
						
						if (response.result[0]['automatico'] == 1) {
							$("#opcionesAutomatico").show();
							$("#automatico").prop('checked', true);
						} else {
							$("#opcionesAutomatico").hide();
							$("#automatico").prop('checked', false);
						}

						if (response.result[0]['tipo'] == 'VENTAS') {
							$("#grupoVentas").show();
							$("#grupo").hide();
							$("#input_ventas").show();
							$("#input").hide();
							$("#input_clientes").hide();

							$("#grupoVentas").val(response.result[0]['grupo_ventas']);

							$("#ventas_alta_cliente_input").hide();
							$("#ventas_ultimo_otorgamiento_input").hide();
							$("#ventas_cantidad_creditos_input").hide();
							$("#ventas_mayor_atraso_input").hide();
							
							if (response.result[0]['grupo_ventas'] === 'MAYOR_ATRASO') {
								$("#ventas_menor").show();
								$("#ventas_mayor").hide();
								$("#ventas_mayor_atraso_input").val(response.result[0]['grupo_ventas_value'])
								$("#ventas_mayor_atraso_input").show();
							} else {
								if (response.result[0]['grupo_ventas'] === 'TODOS') {
									$("#ventas_menor").hide();
									$("#ventas_mayor").hide();
								} else {
									$("#ventas_menor").hide();
									$("#ventas_mayor").show();
								}

								if (response.result[0]['grupo_ventas'] === 'CANTIDAD_CREDITOS') {
									$("#ventas_cantidad_creditos_input").val(response.result[0]['grupo_ventas_value'])
									$("#ventas_cantidad_creditos_input").show();
								}
								if (response.result[0]['grupo_ventas'] === 'ULTIMO_OTORGAMIENTO') {
									$("#ventas_ultimo_otorgamiento_input").val(response.result[0]['grupo_ventas_value'])
									$("#ventas_ultimo_otorgamiento_input").show();
								}
								if (response.result[0]['grupo_ventas'] === 'ALTA_CLIENTE') {
									$("#ventas_alta_cliente_input").val(response.result[0]['grupo_ventas_value'])
									$("#ventas_alta_cliente_input").show();
								}
							}
						}
						
						$("#min1").val(response.result[0]['minutos_gestion']);
						$("#minExtra").val(response.result[0]['minutos_extra']);
						$("#extensiones").val(response.result[0]['cantidad_extensiones']);
						$("#equipoQuery").val(response.result[0]['equipoQuery']);
					
						$("#operadores").val(response.result[0]['operadores'].split(','));	
						$("#operadores").trigger('change')
					
						$("#templateWhatsapp").val(response.result[0]['whatsapp']);
						$('#templateWhatsapp').trigger('change');

						$("#canal").val(response.result[0]['canal_whatsapp']);
						$('#canal').trigger('change');
						
						$("#templateSMS").val(response.result[0]['sms']);
						$('#templateSMS').trigger('change');
	
						$("#templateEmail").val(response.result[0]['mail']);
						$('#templateEmail').trigger('change');

						if (response.result[0]['tipo'] != 'VENTAS') {
							if(response.result[0]['grupo'] != 'PRIMARIA'){
								if(response.result[0]['accion']){
									$("#input_clientes").show();
									document.getElementById('accion').value = response.result[0]['accion'];
									document.getElementById('credito_cliente').value = response.result[0]['credito_cliente'];
								}
							}
						}
            if(response.result[0]['id_exclusion'] != null){
                var array_exclusion = response.result[0]['id_exclusion'].split(",");
                $.ajax({
                    url:  base_url+'api/ApiSupervisores/BuscarBotonesOperador',
                    type: 'POST',
                    success:function(respuesta){
                        var registros = eval(respuesta);
                        html ="";
                        for (var i = 0; i < registros.length; i++) {
                            for(var h = 0; h < array_exclusion.length; h++){
                                if(array_exclusion[h] == registros[i]['id']){
                                    html +="<option value='"+registros[i]['id']+"'' selected>"+registros[i]['id']+".- "+registros[i]['etiqueta']+"</option>";
                                }
                            }
                            html +="<option value='"+registros[i]['id']+"''>"+registros[i]['id']+".- "+registros[i]['etiqueta']+"</option>";
    
                        }
                        $('#exclusiones').html(html);
                    }
                });
            }else{
                cargar_select();
            }

            if(response.result[0]['tipo'] == 'MORA'){
                $("#input").show();
            }
        }else{
            swal.fire('Upss..','Error inserperado','error');
        }
    })
    .fail(function(xhr) {
        swal.fire('Upss..','Error inserperado','error');
         
    });
}

/**
 * Actualiza una campania
 * 
 * @param fields
 */
function update_campania_crm(fields) {
	let base_url = $("input#base_url").val();

	$.ajax({
		url: base_url + 'api/campanias/update_campania_crm',
		type: 'POST',
		dataType: 'json',
		data: fields,
	}).done(function (response) {
		if (response.status.ok) {
			swal.fire('Exito', 'La campaña se actualizo con exito', 'success');
		} else {
			swal.fire('Error', 'No se modificaron campos en la campaña', 'error');
		}
	}).fail(function (xhr) {
		swal.fire('Error', 'Error al generar campaña', 'error');
	});
	
	vistaGeneraCampaniaManual();
}
//Boton ver campania crm
function ver_campania(id, fecha = 0){
    document.getElementById('ver_campania').value = id;
    document.getElementById('cantidad_campania').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    document.getElementById('sin_gestion').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    document.getElementById('porcen_sin_gestion').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    document.getElementById('gestionados_hoy').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    document.getElementById('nombre_campania').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
    document.getElementById('gestion_fecha').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
		document.getElementById('gestionando').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
		document.getElementById('cantidad_mayor_gestion').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
		document.getElementById('cantidad_menor_gestion').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
		document.getElementById('tiempo_promedio').innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
		$("#tiempo_restante").html('<i class="fa fa-spinner fa-spin"></i>');
    var hoy = new Date();
    var dia =  hoy.getDate();
    var mes =  hoy.getMonth()+ 1;
    var anio =  hoy.getFullYear();
    if(dia < 10){dia = '0'+dia;}
    if(mes < 10){mes = '0'+mes;}
    fecha_hoy = anio+'-'+mes+'-'+dia;
    let base_url = $("input#base_url").val();
    let ajax = {
        url: base_url + 'api/campanias/operadores_activos',
        type: 'POST',
        dataType: 'json',
        data: {'id_campania': id},
    }
    let columnDefs = [
        {
            'targets': [0],
            'createdCell':  function (td, cellData, rowData, row, col) {
                $(td).attr('style', 'width: 50%; text-align: left;'); 
            }
        },
        {
            'targets': [1],
            'createdCell':  function (td, cellData, rowData, row, col) {
                $(td).attr('style', 'text-align: left;'); 
            }
        }
    ];
    let columns = [
        {
            "data": "nombre_apellido",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "estado",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data" : null,
            "render": function(data, type, row, meta ){         
                var Salir= '<a class="btn btn-xs bg-red" title="Sacar operador de campaña"  onClick="eliminar_operador_campania('+row.id_operador+','+row.id_campania+')"><i class="fa fa-ban" ></i></a>';                
                if(row.estado != 'activo'){
                    var Estado = '<a class="btn btn-xs bg-yellow" id="estado" data-estado="0" title="Cambiar estado del operador" onClick="activar_estado_operador('+row.id_operador+','+row.id_campania+')"><i class="fa fa-exchange" ></i></a>';

                }else{
                    var Estado = '<a class="btn btn-xs bg-yellow" id="estado" data-estado="0" title="Cambiar estado del operador" onClick="desactivar_estado_operador('+row.id_operador+','+row.id_campania+')"><i class="fa fa-exchange" ></i></a>';
                }

                return Salir+" "+Estado+" ";
            }
    
        }
    ]
    TablaPaginada('operadores_activos', 2, 'asc', '', '', ajax, columns,columnDefs);

    $.ajax({
        url: base_url + 'api/campanias/getCasosCampania',
        type: 'POST',
        dataType: 'json',
        data: {'id':id, 'fecha': fecha},
    }).done(function(response) {
				if (fecha !== 0) {
					$.ajax({
						url: base_url + 'api/campanias/getCantidadCasosEnFecha',
						type: 'POST',
						dataType: 'json',
						data: {'idCampania':id, 'desde': fecha, 'hasta': fecha},
					}).done(function(response) {
						document.getElementById('cantidad_campania').innerHTML = response.cantidad;
					});
				}  
			
        if(response.data)
        {
            document.getElementById('cantidad_campania').innerHTML = response.cantidad;
  
        }else{
            document.getElementById('cantidad_campania').innerHTML = '0';
        }
        $.ajax({
            url: base_url + 'api/campanias/casos_gestion',
            type: 'POST',
            dataType: 'json',
            data: {'id':id,'fecha': fecha},
        }).done(function(respuesta) {
            if(respuesta.status.ok){
								document.getElementById('nombre_campania').innerHTML = respuesta.nombre_campania;
                var casos_totales = document.getElementById('cantidad_campania').innerHTML;
                var sin_gestion = casos_totales - respuesta.data; 
                if(sin_gestion < 1){
                    document.getElementById('porcen_sin_gestion').innerHTML = '0%';
                    if(sin_gestion < 1 && fecha == 0){
                        Swal.fire("¡Información!","¡No quedan casos por gestionar!","info");
                    }
                }else{
                    document.getElementById('porcen_sin_gestion').innerHTML = Math.round((sin_gestion / casos_totales) * 100)+'%';
                }
                document.getElementById('sin_gestion').innerHTML = sin_gestion;
                
                if(fecha != 0 && fecha != fecha_hoy){
                    document.getElementById('gestionando').innerHTML = '--';
                }else{
                    document.getElementById('gestionando').innerHTML = respuesta.gestionando;
                }
                document.getElementById('gestionados_hoy').innerHTML = respuesta.gestionados_hoy;
                if(respuesta.mayor_gestion){
                    document.getElementById('nombre_mayor_gestion').innerHTML = respuesta.mayor_gestion['nombre_apellido'];
                    document.getElementById('cantidad_mayor_gestion').innerHTML = respuesta.mayor_gestion['total'];
                }else{
                    document.getElementById('nombre_mayor_gestion').innerHTML = '- - - -';
                    document.getElementById('cantidad_mayor_gestion').innerHTML = ' -- ';
                    
                }
                if(respuesta.menor_gestion){
                    document.getElementById('nombre_menor_gestion').innerHTML = respuesta.menor_gestion['nombre_apellido'];
                    document.getElementById('cantidad_menor_gestion').innerHTML = respuesta.menor_gestion['total'];
                    
                }else{
                    document.getElementById('nombre_menor_gestion').innerHTML = '- - - -';
                    document.getElementById('cantidad_menor_gestion').innerHTML = ' -- ';
                }
                    tiempo_promedio(id,fecha);
            }

						$.ajax({
							type: "POST",
							url: base_url + 'api/campanias/calcularTiempoRestanteCampania',
							data: {'idCampania': $("#ver_campania").val()}
						}).done(function (response) {
								$("#tiempo_restante").html(response.totalRestante);	
						});
        });
    }).always(function() {
			document.getElementById('verCampania-'+id).innerHTML = '<i class="fa fa-eye"></i>';
		});
    
    let operadores = [];
	let opciones = '<option disabled value="" selected>Seleccione un operador</option>';
	$.ajax({
		url: base_url + 'api/campanias/get_operadores_campania',
        type: 'POST',
        dataType: 'json',
				data: {'id':id},
		success: function (response) {
			response.data.forEach(item => {
					opciones += '<option value="' + item.idoperador + '">' + item.nombre_apellido + '</option>';
				});
				$('#operadoresCampania').html(opciones);
				$('#operadoresCampania').select2({
					placeholder: '.: Selecciona Operador :.',
					multiple: false
				});
		}
	});


}
//Tiempo promedio de gestion campaña manuales
function tiempo_promedio(id,fecha = 0){

    document.getElementById('tiempo_promedio').innerHTML = '';
    let base_url = $("input#base_url").val();
    $.ajax({
        url: base_url + 'api/campanias/tiempo_promedio',
        type: 'POST', 
        data: {'id_campania': id, 'fecha': fecha},
    })
        .done(function (data) {
            if(data.status.ok){
                document.getElementById('tiempo_promedio').innerHTML = data.data;
            }else{
                document.getElementById('tiempo_promedio').innerHTML = '--';
            }
            
            
        })
        .fail(function (data) {
            Swal.fire({
                title: "¡Ups!",
                text: 'Ocurrio un error',
                icon: 'error'
            });
        })
        .always(function () {

    });
    $('#result').addClass('hide');
    $('#view').removeClass('hide');
    $('#form_crm').addClass('hide');
    cargagrafico(id,fecha)
    cargagrafico_tipificacion(id,fecha);
    var table = $('#gestionados_crm').DataTable({
			"pageLength": 15
		});
    table.clear().draw();
    table.destroy();
    var table = $('#tipificaciones_crm').DataTable();
    table.clear().draw();
    table.destroy();
    tabla_gestiones(id,fecha);
    tabla_tipificaciones_crm(id,fecha)


}
function cerrar(){
    var table = $('#operadores_activos').DataTable();
    table.clear().draw();
    table.destroy();
    $('#form_crm').removeClass('hide');
    $('#result').removeClass('hide');
    $('#view').addClass('hide');
    
}
//Actualiza ver campania crm
function actualizar(){
    var table = $('#operadores_activos').DataTable();
    table.clear().draw();
    table.destroy();
    id = document.getElementById('ver_campania').value;
    ver_campania(id);
}
//Cambiar operador de campania
function operador_campania(){
    
    let base_url = $("input#base_url").val();
    id = document.getElementById('ver_campania').value;
    idoperador = $("#operadoresCampania").val();

    $.ajax({
        url: base_url + 'api/campanias/campania_activa_operador',
        type: 'POST',
        data: { "id": idoperador},
    })
        .done(function (responseop) {
            
            if (responseop.status.ok) { 
                swal.fire({
                    title: "El Operador ya se encuentra trabajando en una campaña.",
                    text: "Si cambia de campaña se eliminaran los casos asignados del operador y seran asignados nuevos.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    cancelButtonText: "No",
                    confirmButtonText: "Si"
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: base_url + 'api/campanias/salirCampania',
                            type: 'POST',
                            data: { "id_operador": idoperador},
                        })
                            .done(function (elimina_response) {
                                if (elimina_response.status.ok) {
																	asignarCampaniaAOperador(id,idoperador);
                                }
                            })
                            .fail(function (elimina_response) {
                                Swal.fire({
                                    title: "¡Ups!",
                                    text: 'Ocurrio un error',
                                    icon: 'error'
                                });
                            })
                            .always(function () {
                    
                        });
                    }
                    
                }) 

            }else{
							asignarCampaniaAOperador(id,idoperador);
            }
        })
        .fail(function () {
            Swal.fire({
                title: "¡Ups!",
                text: 'Error inesperado',
                icon: 'error'
            });
        })
        .always(function () {

    });
}

/**
 * Asigna operador en campania crm
 * 
 * @param id
 * @param idoperador
 */
function asignarCampaniaAOperador(id, idoperador) {
	let base_url = $("input#base_url").val();

	if (id != '' && idoperador != '') {
		$.ajax({
			url: base_url + 'api/campanias/activarCampania',
			type: 'POST',
			data: {"id_campania": id, "id_operador": idoperador},
		}).done(function (response) {
			if (response.status.ok) {
				var table = $('#operadores_activos').DataTable();
				table.clear().draw();
				table.destroy();
				ver_campania(id);
			}
		}).fail(function (xhr) {
			Swal.fire({title: "¡Ups!", text: 'Error inesperado', icon: 'error'});
		});
	} else {
		swal.fire('Upss..', 'Seleccione un operador', 'error');
	}
}

function eliminar_operador_campania(idoperador,id_campania){
    
    swal.fire({
        title: "El Operador sera eliminado de la campaña.",
        text: "Se eliminaran los casos asignados en los que se encuentra trabajando el operador.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "No",
        confirmButtonText: "Si"
    }).then(function (result) {
        if (result.value) {
            id = document.getElementById('ver_campania').value;
            let base_url = $("#base_url").val();
            $.ajax({
                url: base_url + 'api/campanias/salirCampania',
                type: 'POST',
                data: { "id_operador": idoperador },
            })
            .done(function (response) {
                if (response.status.ok) {
                    var table = $('#operadores_activos').DataTable();
                    table.clear().draw();
                    table.destroy();
                    ver_campania(id);

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
        
    })  
    


}
function desactivar_estado_operador(idoperador,id_campania){
    id = document.getElementById('ver_campania').value;
    let base_url = $("input#base_url").val();
    $.ajax({
        url: base_url + 'api/campanias/update_estado',
        type: 'POST',
        data: { "id_reg": id_campania,'id_operador': idoperador },
    })
        .done(function (response) {
            if (response.status.ok) {
                var table = $('#operadores_activos').DataTable();
                table.clear().draw();
                table.destroy();
                actualizar();

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
function activar_estado_operador(idoperador,id_campania){

    id = document.getElementById('ver_campania').value;
    let base_url = $("input#base_url").val();
    $.ajax({
        url: base_url + 'api/campanias/reactivarOperador',
        type: 'POST',
        data: { "id_reg": id_campania, "id_operador": idoperador },
    })
        .done(function (response) {
							var table = $('#operadores_activos').DataTable();
							table.clear().draw();
							table.destroy();
							actualizar();
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
function cargagrafico(id, fecha = 0) {

    let base_url = $("#base_url").val();
    $.ajax({
        dataType: "JSON",
        url: base_url + 'api/campanias/tipo_por_operador',
        data: { "id_campania": id, "fecha_consulta": fecha},
        type: 'POST',
        
    })
    .done(function(respuesta){
        if(respuesta.status.ok){
            if (window.x) {
                window.x.clear();
                window.x.destroy();
            }
            let labelsLine = [];
            labelsLine = ["07", "08", "09", "10", "11", "12","13", "14", "15", "16", "17", "18","19", "20"]
            var datasets = JSON.parse(respuesta.data);
            let ctx = document.getElementById('myChartLine_gestiones').getContext('2d');
            x = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labelsLine,
                    datasets: datasets
                },
                options: {
                    plugins: {
                        datalabels: {
                            color: '#000000',
                            align: 'start',
                            offset: -1,
                        },
                    },
                    
                }
            });
            
        }
    })
    .fail(function(xhr) {
       
    })
    
}
function consultas_gestiones_crm(tipo){
    $('#busqueda_por_fecha').addClass('hide');
    if(tipo == 'fecha'){
        document.getElementById('title_consulta').innerHTML = 'Busqueda por fecha';
        $('#busqueda_por_operador').addClass('hide');
        $('#busqueda_por_fecha').removeClass('hide');
    }
    $('#consulta_por_fecha').modal("show");
}
function consultar_gestion_fecha(){
    if($('#gestion_fecha').val() == ''){
        var fecha = 0;
    }else{
        var fecha = $('#gestion_fecha').val();
    }
    //tiempo_promedio_operador
    var table = $('#operadores_activos').DataTable();
    table.clear().draw();
    table.destroy();
    ver_campania($('#ver_campania').val(),fecha);
}
function cargagrafico_tipificacion(id,fecha=0){
    let base_url = $("#base_url").val();
    $.ajax({
        dataType: "JSON",
        url: base_url + 'api/campanias/tipificacion',
        data: { "id_campania": id, "fecha_consulta": fecha},
        type: 'POST',
        
    })
    .done(function(respuesta){
        
        if(respuesta.status.ok){
            if (window.tipificacion) {
                window.tipificacion.clear();
                window.tipificacion.destroy();
            }
            let labelsLine = [];
            labelsLine = ["07", "08", "09", "10", "11", "12","13", "14", "15", "16", "17", "18","19", "20"]
            var datasets = JSON.parse(respuesta.data);
            let tipificacion_ctx = document.getElementById('myChartLine_tipificaciones').getContext('2d');
            tipificacion = new Chart(tipificacion_ctx, {
                type: 'line',
                data: {
                    labels: labelsLine,
                    datasets: datasets
                },
                options: {
                    plugins: {
                        datalabels: {
                            color: '#000000',
                            align: 'start',
                            offset: -1,
                        },
                    },
                    
                }
            });
            

        }
    })
    .fail(function(xhr) {
       
    })

}
function tabla_gestiones(id,fecha=0){
		let base_url = $("input#base_url").val();
		
    if ($.fn.DataTable.isDataTable("#gestionados_crm")) {
        $("#gestionados_crm").DataTable().clear().draw();
        $("#gestionados_crm").DataTable().destroy();
    }
    
    let ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/campanias/tabla_gestion",
        data: {'id_campania': id, 'fecha':fecha},
	};
    let columnDefs = [
        {
            'targets': [0,1,2,3,4,5,6,7],
            'createdCell':  function (td, cellData, rowData, row, col) {
                $(td).attr('style', 'text-align: left;'); 
            }
        }
    ];
    let columns = [
        {
            "data": "nombre_apellido",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "asignados",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "gestiones",
            "render": function(data, type, row, meta){
							return '<a target="_self" href="' + base_url + 'api/campanias/downlaodCSVCasosGestionadosPorOperador/' + id + '/' + row.id_operador + '">' + data + '</a>'
            }
        },
        {
            "data": "menor_tiempo",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "mayor_tiempo",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "tiempo_descanso_gestion",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "tiempo_total_gestion",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "tiempo_inactivo",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "tiempo_promedio",
            "render": function(data, type, row, meta){
                return data
            }
        },
        
    ];
		
		let footer = function ( row, data, start, end, display ) {
			var api = this.api(), data;

			// converting to interger to find total
			var intVal = function ( i ) {
				return typeof i === 'string' ?
					i.replace(/[\$,]/g, '')*1 :
					typeof i === 'number' ?
						i : 0;
			};

			var gestionados = api
				.column( 2 )
				.data()
				.reduce( function (a, b) {
					return intVal(a) + intVal(b);
				}, 0 );

			// Update footer by showing the total with the reference of the column index 
			$( api.column( 0 ).footer() ).html('Total');
			$( api.column( 2 ).footer() ).html('<a target="_self" href="' + base_url + 'api/campanias/downlaodCSVTotalCasosGestionados/' + id + '">' + gestionados + '</a>');
			
		}

    TablaPaginada("gestionados_crm", 0, "asc", "", "", ajax, columns, columnDefs,null,null,15, footer );
}
function tabla_tipificaciones_crm(id,fecha=0){

    if ($.fn.DataTable.isDataTable("#tipificaciones_crm")) {
        $("#tipificaciones_crm").DataTable().clear().draw();
        $("#tipificaciones_crm").DataTable().destroy();
    }
    let base_url = $("input#base_url").val();
		
    let ajax = {
		type: "POST",
		url: base_url + "api/campanias/tablaTotalTipificaciones",
        data: {'id_campania': id, 'fecha':fecha},
	};
    let columnDefs = [
        {
            'targets': [0,1,2],
            'createdCell':  function (td, cellData, rowData, row, col) {
                $(td).attr('style', 'text-align: left;'); 
            }
        }
    ];
    let columns = [
        {
            "data": "etiqueta",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "denominacion",
            "render": function(data, type, row, meta){
                return data
            }
        },
				{
					"data": "cantidad",
					"render": function(data, type, row, meta){
						if (row.denominacion === null) {
							return '<a target="_self" href="' + base_url + 'api/campanias/downloadCSVTipificacionPorTipo/' + id + '/' + row.idTipo + '">' + data + '</a>';
						} else {
							return '<a target="_self" href="' + base_url + 'api/campanias/downloadCSVTipificacionPorDetalle/' + id + '/' + row.idDetalle + '">' + data + '</a>';
						}
					}
				},

	
    ]
    
    TablaPaginada("tipificaciones_crm", 0, "asc", "", "", ajax, columns, columnDefs);
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
                    if(item.idtipo_operador == 5 || item.idtipo_operador == 6 || item.idtipo_operador == 13){

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

$("#vistaRecaudosSImputar").on("click", function () {
	verRecaudosSinImputar();
})

function verRecaudosSinImputar() {
	$.ajax({
		type: "POST",
		url: base_url + "supervisores/Supervisores/vistaRecuadosSinImputar",
		success: function (response) {
			$("#main").html(response);
			$("#cargando").css("display", "none");
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
