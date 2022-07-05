$(document).ready(function(){
    cargarImputacionAutomatica();
})
var tp_imputacionAutomatica;
var cargarImputacionAutomatica = function(){
	let columnDefs = [
        {
            'targets': [1],
            'createdCell':  function (td, cellData, rowData, row, col) {
                $(td).attr('style', 'padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;'); 
            }
        }
    ];
    let columns = [
        {
            "data": "nombre_completo_pagador",
        },
        {
        	"data" : "cuenta_pagador"
        },
        {
            "data" : "monto"
        },
        {
            "data" : "id_credito_detalle"
        },
        {
            "data" : "status"
        },
        {
            "data" : "fecha_recaudo"
        }
    ]
    let options_dt = {
        "order": [[3,"asc"], [0, "asc"]],
		createdRow : function( row, data, dataIndex){
			
            console.log(data);
    	}
    }
    tp_respuestaBanco = TablaPaginada('tp_imputacion_automatica_bancolombia',0,'asc','','', null , columns, columnDefs, options_dt);

    $("#btnConfirmar").on('click', function(e){
        
        var form = $('#imputacionAutomaticaBancolombia');
        var formData = new FormData();
        var dataImputacionAutomatica  = $('#imputacionAutomaticaBancolombia').serializeArray();
        formData.append('fileImputacionAutomaticaBancolombia', "");

        $.each(form.find('input[type="file"]'), function(i, tag){
            $.each($(tag)[0].files, function(i, file){
                formData.set(tag.name, file);
            })
        })

        $.each(dataImputacionAutomatica, function(i, val) {
            formData.set(val.name, val.value);
        });

        procesarRespuestaImputacionAuto(formData);
    });

}

function procesarRespuestaImputacionAuto(dataRespuestaImputacion){
    
    var url = $("input#base_url").val()+"tesoreria/tesoreria/procesarRespuestaImputacionAutomatica";
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: dataRespuestaImputacion,
        beforeSend : function(){
            disabledButtons(true);
        },
        complete : function(){
            disabledButtons(false);  
        },
        success : function(response){
            if(response.response.respuesta ){
                $("#detalle-archivo-imputacion-automatica").html("");
                $("#detalle-archivo-imputacion-automatica").append("<p>Archivo: "+response.detalle.archivo+
                " | Monto Total Debitado: "+response.detalle.monto_total+
                " | Total Registros: "+response.detalle.total_txt+
                " | Origen Pago: "+response.detalle.origen_pago+
                " | Fecha Subida "+response.detalle.fecha_subida+"</p>");
            	$("#tp_imputacion_automatica_bancolombia").dataTable().api().clear().draw();
        	 	$("#tp_imputacion_automatica_bancolombia").dataTable().api().rows.add(response.data); // Add new data
    			$("#tp_imputacion_automatica_bancolombia").dataTable().api().columns.adjust().draw(); // Redraw the DataTable
                toastr["success"](response.response.mensaje, "Respuesta Imputación Automática" );

            }else if(response.response.errors.length !== 0){
            	var message_errors = ""
                $.each(response.response.errors, function(i, el){
                    message_errors += el+"<br>"
                })
                
                toastr["error"](message_errors, "Respuesta Imputación Automática" );

            }else{
            	toastr["warning"](response.response.mensaje, "Respuesta Imputación Automática" );
            }

        },
        error(xhr,status,error){
            //Ajax request failed.
            console.log(xhr);
            console.log(status);
            console.log(error);
            toastr["error"]("No se pudo completar la carga del archivo.", "Error" );
        }
    })
    
}