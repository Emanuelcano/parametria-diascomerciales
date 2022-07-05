$(document).ready(function(){
    cargarImputacionRecaudo();
})
var tp_imputacionRecaudo;
var cargarImputacionRecaudo = function(){
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
    tp_respuestaBanco = TablaPaginada('tp_imputacion_recaudo_bancolombia',0,'asc','','', null , columns, columnDefs, options_dt);

    $("#btnConfirmar").on('click', function(e){
        
        console.log('XXX');
        var form = $('#imputacionRecaudoBancolombia');
        var formData = new FormData();
        var dataImputacionAutomatica  = $('#imputacionRecaudoBancolombia').serializeArray();
        formData.append('imputacionRecaudoBancolombia', "");

        $.each(form.find('input[type="file"]'), function(i, tag){
            $.each($(tag)[0].files, function(i, file){
                formData.set(tag.name, file);
            })
        })

        $.each(dataImputacionAutomatica, function(i, val) {
            formData.set(val.name, val.value);
        });

        procesarRespuestaImputacionRecaudo(formData);
    });

}

function procesarRespuestaImputacionRecaudo(dataRespuestaImputacion){
    
    console.log('AAA');
    var url = $("input#base_url").val()+"tesoreria/tesoreria/procesarRespuestaImputacionRecaudo";
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
            console.log(response.response.respuesta);
            if(response.response.respuesta ){
                $("#detalle-archivo-imputacion-recaudo").html("");
                $("#detalle-archivo-imputacion-recaudo").append("<p>Archivo: "+response.detalle.archivo+
                " | Monto Total Debitado: "+response.detalle.monto_total+
                " | Total Registros: "+response.detalle.total_txt+
                " | Origen Pago: "+response.detalle.origen_pago+
                " | Fecha Subida "+response.detalle.fecha_subida+"</p>");
            	$("#tp_imputacion_recaudo_bancolombia").dataTable().api().clear().draw();
        	 	$("#tp_imputacion_recaudo_bancolombia").dataTable().api().rows.add(response.data); // Add new data
    			$("#tp_imputacion_recaudo_bancolombia").dataTable().api().columns.adjust().draw(); // Redraw the DataTable
                toastr["success"](response.response.mensaje, "Respuesta Imputación Recaudo" );

            }else if(response.response.errors.length !== 0){
            	var message_errors = ""
                $.each(response.response.errors, function(i, el){
                    message_errors += el+"<br>"
                })
                
                toastr["error"](message_errors, "Respuesta Imputación Recaudo" );
                console.log('FFF');

            }else{
                console.log('GGG');
            	toastr["warning"](response.response.mensaje, "Respuesta Imputación Recaudo" );
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