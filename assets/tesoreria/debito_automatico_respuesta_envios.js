$(document).ready(function(){
    cargarRespuestaEnvios();
})

var tp_imputacionAutomatica;

var cargarRespuestaEnvios = function(){
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
            console.log("data");
            console.log(data);
    	}
    }
    tp_respuestaBanco = TablaPaginada('tp_lectura_bancolombia_envio',0,'asc','','', null , columns, columnDefs, options_dt);

    $("#btnConfirmar").on('click', function(e){
        
        var form = $('#lecturarcga');
        var formData = new FormData();
        var dataLecturaRcga  = $('#lecturarcga').serializeArray();
        formData.append('fileLecturarcga', "");

        $.each(form.find('input[type="file"]'), function(i, tag){
            $.each($(tag)[0].files, function(i, file){
                formData.set(tag.name, file);
            })
        })

        $.each(dataLecturaRcga, function(i, val) {
            formData.set(val.name, val.value);
        });

        procesarRespuestaImputacionEnvios(formData);
    });

}

function procesarRespuestaImputacionEnvios(dataRespuestaImputacion){

    var url = $("input#base_url").val()+"tesoreria/envios/procesarRespuestaEnvio";

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
            console.log("Y");
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