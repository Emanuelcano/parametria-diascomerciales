$(document).ready(function(){
    cargarImputacion();
})

var tp_imputacionAutomatica;

var cargarImputacion = function()
{

    

    $("#btnConfirmar").on('click', function(e){
        
        if ($('#fileLectura')[0].files.length === 0) 
        {
            return;
        }

        var form = $('#lectura');
        var formData = new FormData();
        var dataLecturaRcga  = $('#lectura').serializeArray();
        formData.append('fileLectura', "");

        $.each(form.find('input[type="file"]'), function(i, tag){
            $.each($(tag)[0].files, function(i, file){
                formData.set(tag.name, file);
            })
        })

        $.each(dataLecturaRcga, function(i, val) {
            formData.set(val.name, val.value);
        });

        subirArchivoImputacion(formData);
    });

}

function subirArchivoImputacion(dataRespuestaImputacion){

    var url = $("input#base_url").val()+"tesoreria/imputacion/subirArchivoImputacion";

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
                
            	var message_errors = "";
                $.each(response.response.errors, function(i, el){
                    message_errors += el+"<br>";
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