
$(document).ready(function(){
    buscarBancoOrigen();
    buscarCuentaDestino();

	handlersImputacionPago();
    //handlersModalImputacionPago();
    buttonsTableHandler();
    initTablePrestamosPagar();
    initTableSolicitudImputar();
    modalConfirm();
})

function handlersImputacionPago(){
	$("#buscarCreditoCliente").on('click', function(e){
		$("#tp_creditosCliente").DataTable().ajax.reload();
	});
}

//function handlersModalImputacionPago(){
    
// $('#modalImputarPago').on('shown.bs.modal', function (e) {
//     buscarBancoOrigen();
//     buscarCuentaDestino();
// })
    
//}

$('#modalImputarPago').on('hidden.bs.modal', function (e) {
    $("#formImputacionPago").trigger("reset");
    $("#modalAlert").addClass('hidden');
})


//Busca los bancos de origen
function buscarBancoOrigen(){
    var route = $("input#base_url").val()+"tesoreria/tesoreria/buscarEntidadesBancarias"
    $.ajax({
        url: route,
        type: 'GET',
        dataType: 'JSON'
    })
    .done(function(response) {
        $("#bankEntidades").html("");
        $("#bankEntidades").append("<option selected value=''>Seleccione</option>");
        $.each(response.data, function(i,el){
            addOptionsSelect(el.id_Banco, el.Nombre_Banco, "bankEntidades");
        })
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
    
}
//Busca las cuentas bancarias 
function buscarCuentaDestino(){

    var route = $("input#base_url").val()+"tesoreria/tesoreria/buscarCuentaBancaria"
    $.ajax({
        url: route,
        type: 'GET',
        dataType: 'JSON'
    })
    .done(function(response) {
        $("#cuentaDestino").html("");
        $("#cuentaDestino").append("<option selected value=''>Seleccione</option>");
        $.each(response.data, function(i,el){
            addOptionsSelect(el.id, el.numero_cuenta, "cuentaDestino");
        })
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
    
}

//ADD OPTIONS TO SELECT
function addOptionsSelect(optionValue, optionText, idSelect){
    $("#"+idSelect).append(new Option(optionText, optionValue));
} 

//Inicializar la tabla de imputaciones
function initTablePrestamosPagar(paramBusqueda = null){
    let ajax = {
        'type' :"POST",
        'url' : $("input#base_url").val()+"tesoreria/tesoreria/buscarCreditosClientes",
        'data': function (d) {
            //SET DATA FOR POST in Datatable
	        d.paramBusqueda = $("#inpuBuscarCreditos").val();
	    }
    }
    let columns = [
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
            "data": "id_credito",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
            "data": "numero_cuota",
            "render": function(data, type, row, meta){
                return data
            }
        },
        {
        	"data" : "fecha_otorgamiento"
        },
        {
            "data" : "monto_cuota"
        },
        {
        	"data" : "estado_cuota",
            "render" : function(data, type, row, meta){
                data = (data === null) ? "vigente" : data;
                return data;
            }
        },
        {
            "data" : null,
            "render": function(data, type, row, meta ){
                var buttonUpDonw = "";
                //if(row.comprobante == null){
                    buttonUpDonw = "<button class='btn btn-xs btn-primary btnFormImputacion' title='Imputar Credito'>"+
                        "<i class='fa fa-upload' ></i></button>";
                //}else{
                //    buttonUpDonw = '<a href="'+row.comprobante+'" target="_blank"><i class="fa fa-download" ></i></a>'
                //}
                
                
                var botones = buttonUpDonw; 
                return botones;
            }
        }
        
    ]
    TablaPaginada('tp_creditosCliente',2,'asc','','', ajax, columns );
}

var buttonsTableHandler  = function(){
    
    $('#tp_creditosCliente tbody').off().on( 'click', 'button', function () {
        var data = $("#tp_creditosCliente").DataTable().row( $(this).parents('tr') ).data();

        if($(this).hasClass('btnFormImputacion')){
            $("#nombre_cliente").html(data.nombre + " - "+" CC:" +data.documento);
            clickImputandoPago(data);
        }

    } );
    
}

var clickImputandoPago = function(data){
    $("#modalImputarPago #id_credito").val(data.id_credito);
    $("#modalImputarPago #id_cliente").val(data.id_cliente);
    $("#modalImputarPago #id_detalle_credito").val(data.id_credito_detalle);
    $("#modalImputarPago #documento").val(data.documento);
    /*** Se verifica si el Cliente existe en Solicitud Imputación ***/
    if (data.id_solicitud_imputacion) {
        $("#modalImputarPago #btnNoProcesar").attr("disabled", true);
        $("#modalImputarPago #btnProcesar").attr("disabled", true);

        $("#modalImputarPago #id_solicitud_imputacion").val(data.id_solicitud_imputacion);
        $("#modalImputarPago #ruta_comprobante").val(data.comprobante);
        
        $("#modalImputarPago #viewcomprobanteActual").html('')
        datafile = '';
        let ext = (data.comprobante).split('.')
        if (ext[1] == 'pdf') {
            datafile = '<object class="viewcomprobanteActual"  type="application/pdf" data="'+$("#base_url").val() + data.comprobante+'" style="width: -webkit-fill-available; height: -webkit-fill-available;" ></object>';
        }else
            datafile = '<object class="viewcomprobanteActual" data="'+$("#base_url").val() + data.comprobante+'" style="max-height:100%; max-width:100%;" ></object>';

        $("#modalImputarPago #viewcomprobanteActual").html(datafile)            

        $("#modalImputarPago #medio_pago").val(data.medio_pago);
        $("#modalImputarPago #referencia").val(data.referencia);
        $("#modalImputarPago #fechaTransferencia").val(data.fecha_pago);
        $("#modalImputarPago #montoTransferencia").val(data.monto_pago);
        $('#modalImputarPago #bankEntidades option[value="' + data.banco_origen + '"]').prop("selected", true);
        $('#modalImputarPago #cuentaDestino option[value="' + data.banco_destino + '"]').prop("selected", true);

        /*** Si los medios de pago son Efecty y ePayco no lleva banco origen ***/
        if (data.medio_pago == 'Efecty' || data.medio_pago == 'ePayco') {
            $('#modalImputarPago #bankEntidades').attr('disabled', true);
        } else {
            $('#modalImputarPago #bankEntidades').attr('disabled', false);
        }

        $("#modalImputarPago #a_comprobante").attr('href', data.comprobante);
        let arrNombreArchivo = data.comprobante.split('/');
        $("#modalImputarPago #a_comprobante").text(arrNombreArchivo[arrNombreArchivo.length - 1]);
        $("#modalImputarPago comprobanteImputacionCredito").val($("input#base_url").val() + '/' + data.comprobante);
        $("#modalImputarPago #a_comprobante").show();
        $("#modalImputarPago #div_resultado").show();
        $("#modalImputarPago #btnNoProcesar").show();
    } else {
        $("#modalImputarPago #id_solicitud_imputacion").val("");
        $("#modalImputarPago #ruta_comprobante").val("");
        $("#modalImputarPago #a_comprobante").hide();
        $("#modalImputarPago #div_resultado").hide();
        $("#modalImputarPago #btnNoProcesar").hide();
        $("#modalImputarPago #btnProcesar").attr("disabled", false);
    }
	$("#modalImputarPago").modal({
        backdrop: 'static',
        keyboard: false
    })
}

var modalConfirm = function(){
    
    $("#btnProcesar").on('click', function(e){
        
        var form = $('#formImputacionPago');
        var formData = new FormData();
        var dataImputacionPago  = $('#formImputacionPago').serializeArray();
        formData.append('comprobante', "");

        $.each(form.find('input[type="file"]'), function(i, tag){
            $.each($(tag)[0].files, function(i, file){
                formData.set(tag.name, file);
            })
        })

        $.each(dataImputacionPago, function(i, val) {
            formData.set(val.name, val.value);
        });

        guardarImputacion(formData);
    });

    $("#btnCancelar").on('click', function(e){
        
        $("#modalImputarPago").modal('hide');
    });
}

function guardarImputacion(dataImputacionPago){
    let fecha_pago = $("#modalImputarPago #fechaTransferencia").val();

    let fechaAlt = new Date();
    let fecha = fecha_pago.split("-");
    fechaAlt.setFullYear(fecha[0],fecha[1]-1,fecha[2]);
    var today = new Date();

    if (fechaAlt > today) {
        return Swal.fire(
            "¡Atención!",
            "La Fecha no puede ser mayor al día de hoy",
            "error"
        );
    }

    var url = $("input#base_url").val()+"tesoreria/tesoreria/guardarImputacion"
    $.ajax({ 
         url: url,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: dataImputacionPago,
        success: function(response){
            if(response.response.respuesta ){
                $("#modalImputarPago").modal('hide');
                toastr["success"](response.response.mensaje, "Imputación de pago" );
                $("#tp_creditosCliente").DataTable().ajax.reload();
                $("#tbl_solicitud_imputacion").DataTable().ajax.reload();
                getCantImputacionesPendientes();
                if (dataImputacionPago.get('resultado') === 'Imputado') {
                    var formData = new FormData();
                    formData.append('doc', dataImputacionPago.get('documento'));
                    $.ajax({
                        url: $("input#base_url").val()+"tesoreria/tesoreria/getvalidacioncliente",
                        type: 'POST',
                        dataType: 'JSON',
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: (response) => {
                            console.log(response);
                        }
                    });
                }

            }else if(response.response.errors.length !== 0){
                modalOptions.body.alert.message = []
                $.each(response.response.errors, function(i, el){
                    modalOptions.body.alert.message.push(el+"<br>");
                })
                modalOptions.body.alert.type  = 'alert-danger';
                    modalAlertMessage(modalOptions);
            }
            /*** Se realiza el track de gestión ***/
            let monto_pago = $("#montoTransferencia").val();
            let referencia = $("#referencia").val();
			let id_solicitud = response.response.id_solicitud;
			let id_operador = response.response.id_operador;
            let type_contact = 171;
            if(response.response.comprobante){
                if(response.response.comprobante.length > 120){ 
                    comprobante = response.response.nombre_comprobante.slice(47, 86);
                }else{
                    comprobante = 'Comprobante';
                }
                var adjunto = '<a href="'+$("input#base_url").val()+response.response.comprobante+'" target="_blank">'+comprobante+'</a>';
            }else{
                var adjunto = 'NO';
            }
			let comment = "<b>[IMPUTACIÓN PAGO]</b>"+ 
				"<br> Fecha: " + response.response.fecha_operacion +
                "<br> Monto: " + monto_pago +
                "<br> Referencia: " + referencia +
				"<br> Cliente: " + $("#nombre_cliente").text() +
                "<br> Comprobante adjunto: " + adjunto;
			saveTrack(comment, type_contact, id_solicitud, id_operador);
        },
        error: function(){
            Swal.fire(
                "¡Atención!",
                "Se ha presentado una falla al imputar el pago. Le pedimos que solicite la revision del caso con un administrador",
                "error"
            );
        },
        beforeSend : function(){
            disabledButtons(true);
        },
        complete : function(){
            disabledButtons(false); 
        }
    });
    
}
/**********************************************************/
/*** Validación en cada cambio del Select de Respuestas ***/
/**********************************************************/
$('body').on('change', '#slt_resultado', function () {
	let respuesta = $("#slt_resultado").val();
	if(respuesta === "Imputado") {
        $("#btnProcesar").attr("disabled", false);
        $("#btnNoProcesar").attr("disabled", true);
	} else {
        $("#btnProcesar").attr("disabled", true);
        $("#btnNoProcesar").attr("disabled", false);
	};
});
/*************************/
/*** Botón No procesar ***/
/*************************/
$("#btnNoProcesar").on('click', function(e){
    let fecha_pago = $("#modalImputarPago #fechaTransferencia").val();

    let fechaAlt = new Date();
    let fecha = fecha_pago.split("-");
    fechaAlt.setFullYear(fecha[0],fecha[1]-1,fecha[2]);
    var today = new Date();

    if (fechaAlt > today) {
        return Swal.fire(
            "¡Atención!",
            "La Fecha no puede ser mayor al día de hoy",
            "error"
        );
    }

    var form = $('#formImputacionPago');
    var formData = new FormData();
    var dataImputacionPago  = $('#formImputacionPago').serializeArray();
    formData.append('comprobante', "");

    $.each(form.find('input[type="file"]'), function(i, tag){
        $.each($(tag)[0].files, function(i, file){
            formData.set(tag.name, file);
        })
    })

    $.each(dataImputacionPago, function(i, val) {
        formData.set(val.name, val.value);
    });
 
    var url = $("input#base_url").val()+"tesoreria/tesoreria/actualizarSolicitudImputacion"
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: formData,
        success: function(response){
            if(response.response.respuesta ){
                $("#modalImputarPago").modal('hide');
                toastr["success"](response.response.mensaje, "Imputación de pago" );
                $("#tp_creditosCliente").DataTable().ajax.reload();
                $("#tbl_solicitud_imputacion").DataTable().ajax.reload();
            }else if(response.response.errors.length !== 0){
                modalOptions.body.alert.message = []
                $.each(response.response.errors, function(i, el){
                    modalOptions.body.alert.message.push(el+"<br>");
                })
                modalOptions.body.alert.type  = 'alert-danger';
                    modalAlertMessage(modalOptions);
            }
            /*** Se realiza el track de gestión ***/
            let monto_pago = $("#montoTransferencia").val();
            let referencia = $("#referencia").val();
			let id_solicitud = response.response.id_solicitud;
			let id_operador = response.response.id_operador;
			let type_contact = 171;
            let comment = "<b>[IMPUTACIÓN PAGO NO PROCESADO]</b>"+ 
                "<br> Motivo: " + $("#slt_resultado").val() +
				"<br> Fecha: " + response.response.fecha_operacion +
                "<br> Monto: " + monto_pago +
                "<br> Referencia: " + referencia +
				"<br> Cliente: " + $("#nombre_cliente").text();
			saveTrack(comment, type_contact, id_solicitud, id_operador);
        },
        error: function(err){
            console.log(err);
        },
        beforeSend : function(){
            disabledButtons(true);
        },
        complete : function(){
            disabledButtons(false); 
        }
    });
});

$("#btnCancelar").on('click', function(e){
    
    $("#modalImputarPago").modal('hide');
});
/*****************************************************/
/*** Se inicializa la tabla Solicitud Imputaciones ***/
/*****************************************************/
function initTableSolicitudImputar(){
    let ajax = {
        'type' :"POST",
        'url' : $("input#base_url").val()+"tesoreria/tesoreria/buscarSolicitudImputacion",
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
            "data": "id_credito",
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
            "data" : "estado_credito",
            "render" : function(data, type, row, meta){
                data = (data === null) ? "vigente" : data;
                return data;
            }
        },
        {
            "data": null,
            "render": function(data, type, row, meta){
                if (data.por_procesar == 0) {
                    return 'Por procesar'
                } else {
                    return 'Procesado'
                }
            }
        },
        {
            "data" : null,
            "render": function(data, type, row, meta ){
                var buttonUp = "";
                    buttonUp = `<button class='btn btn-xs btn-primary btnFormImputacion' title='Imputar Solicitud'>
                                        <i class='fa fa-upload' ></i>
                                    </button>`;
                return buttonUp; 
            }
        }
        
    ]
    TablaPaginada('tbl_solicitud_imputacion', 2, 'asc', '', '', ajax, columns );
}
/******************************************************************/
/*** Acción de Imputar en la Tabla de Solicitudes de Imputación ***/
/******************************************************************/
$('#tbl_solicitud_imputacion tbody').off().on( 'click', 'button', function () {
    var data = $("#tbl_solicitud_imputacion").DataTable().row( $(this).parents('tr') ).data();
    if($(this).hasClass('btnFormImputacion')){
        //console.log(data);
        $("#nombre_cliente").html(data.nombre + " - "+" CC:" +data.documento);
        clickImputandoPago(data);
    }

} );
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
