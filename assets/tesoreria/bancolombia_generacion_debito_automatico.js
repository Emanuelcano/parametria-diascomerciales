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
            "data": "nombres",
        },
        {
        	"data" : "apellidos"
        },
        {
            "data" : "documento"
        },
        {
            "data" : "numero_cuenta"
        },
        {
            "data" : "id_cuota"
        },
        {
            "data" : "monto_cobrar"
        },
        {
            "data" : "fecha_vencimiento"
        }
    ]
    let options_dt = {
        "order": [[3,"asc"], [0, "asc"]],
		createdRow : function( row, data, dataIndex){
		
    	}
    }

    tp_respuestaBanco = TablaPaginada('tp_debito_automatico_bancolombia',0,'asc','','', null , columns, columnDefs, options_dt);

    $("#estado").change(function(){
        
        if ($("#estado").val() == "mora"){
            console.log("MORA");
            $("#fechaVencimientoInicio").addClass('hide');
            $("#fechaVencimientoFinal").addClass('hide');
            $("#fechaVencimiento").removeClass('hide');
            $("#topeDiv").removeClass('hide');
            $("#atrasoDiv").removeClass('hide');
        } 

        if ($("#estado").val() == "vigente"){
            console.log("VIGENTE");
            $("#fechaVencimientoInicio").removeClass('hide');
            $("#fechaVencimientoFinal").removeClass('hide');
            $("#fechaVencimiento").addClass('hide');
            $("#topeDiv").addClass('hide');
            $("#atrasoDiv").addClass('hide');
        } 

    });

    $("#bancotipo").change(function(){

        console.log("xxx");

        if ($("#bancotipo").val() == "bancolombia"){
            console.log("bancolombia");
            $("#tipoempleado").addClass('hide');
        }

        if ($("#bancotipo").val() == "otrosbancos"){
            console.log("otrosbancos");
            $("#tipoempleado").removeClass('hide');
        }

    });

    $("#btnGenerarVista").on('click', function(e){
        $('#btnGenerarVista').attr("disabled", true);
        $('#btnEnviarArchivos').attr("disabled", true);
        var form = $('#generacionDebitoAutomaticoBancolombia');
        var formData = new FormData();
        var dataImputacionAutomatica  = $('#generacionDebitoAutomaticoBancolombia').serializeArray();

         $.each(form.find('input[type="file"]'), function(i, tag){
            $.each($(tag)[0].files, function(i, file){
                formData.set(tag.name, file);
            })
        }) 

        $.each(dataImputacionAutomatica, function(i, val) {
            formData.set(val.name, val.value);
        });

        generarVistaDebitoAutomatico(formData);
    });

    $("#btnEnviarArchivos").on('click', function(e){
        $('#btnEnviarArchivos').attr("disabled", true);
        $('#btnGenerarVista').attr("disabled", true);
        var form = $('#generacionDebitoAutomaticoBancolombia');
        var formData = new FormData();
        var dataImputacionAutomatica  = $('#generacionDebitoAutomaticoBancolombia').serializeArray();

         $.each(form.find('input[type="file"]'), function(i, tag){
            $.each($(tag)[0].files, function(i, file){
                formData.set(tag.name, file);
            })
        }) 

        $.each(dataImputacionAutomatica, function(i, val) {
            formData.set(val.name, val.value);
        });
        
        enviarArchivosDebitoAutomatico(formData);
    });

}

function enviarArchivosDebitoAutomatico(dataRespuesta)
{
    console.log(dataRespuesta);

    var url = $("input#base_url").val()+"tesoreria/tesoreria/enviarArchivosDebitoAutomatico";

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: dataRespuesta,
        beforeSend : function(){
            disabledButtons(true);
            $('#btnEnviarArchivos').attr("disabled", true);
            $('#btnGenerarVista').attr("disabled", true);
        },
        complete : function(){
            disabledButtons(false);  
            $('#btnEnviarArchivos').attr("disabled", false);
            $('#btnGenerarVista').attr("disabled", false);
        },
        success : function(response){
            
            $('#btnEnviarArchivos').attr("disabled", false);
            $('#btnGenerarVista').attr("disabled", false);
            if(response){
                toastr["success"]('Generacion de Debito', "Envio realizado" );
            }else{
            	toastr["warning"](response.response.mensaje, "Respuesta Generacion vista Debito Automático" );
            }

        },
        error(xhr,status,error){
            //Ajax request failed.
            console.log(xhr);
            console.log(status);
            console.log(error);
            toastr["error"]("No se pudo completar con la generacion de la vista .", "Error" );
        }

    })

}

function generarVistaDebitoAutomatico(dataRespuesta)
{
    console.log("btnGenergenerarVistaDebitoAutomaticoarVista");
    var url = $("input#base_url").val()+"tesoreria/tesoreria/generarVistaDebitoAutomatico";

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: dataRespuesta,
        beforeSend : function(){
            console.log("beforeSend");
            $('#btnGenerarVista').attr("disabled", true);
            $('#btnEnviarArchivos').attr("disabled", true);
            disabledButtons(true);
        },
        complete : function(){
            disabledButtons(false);  
            $('#btnGenerarVista').attr("disabled", false);
            $('#btnEnviarArchivos').attr("disabled", false);
        },
        success : function(response){
            $('#btnGenerarVista').attr("disabled", false);
            $('#btnEnviarArchivos').attr("disabled", false);
            if(response){

                $("#detalle-vista-debito-automatico").html("");
                $("#tp_debito_automatico_bancolombia").dataTable().api().clear().draw();
        	 	$("#tp_debito_automatico_bancolombia").dataTable().api().rows.add(response.clientes); // Add new data
    			$("#tp_debito_automatico_bancolombia").dataTable().api().columns.adjust().draw(); // Redraw the DataTable
                toastr["success"]('Generacion de Debito', "Respuesta Generacion Vista Debito Automático" );

            }else{
            	toastr["warning"](response.response.mensaje, "Respuesta Generacion vista Debito Automático" );
            }

        },
        error(xhr,status,error){
            //Ajax request failed.
            console.log(xhr);
            console.log(status);
            console.log(error);
            toastr["error"]("No se pudo completar con la generacion de la vista .", "Error" );
        }
        
    })

}