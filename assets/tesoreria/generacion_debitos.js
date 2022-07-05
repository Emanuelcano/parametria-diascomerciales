$(document).ready(function(){
    generarDebitos();
})

var generarDebitos = function(){

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
            "data" : "estado"
        },
        {
            "data" : "monto_cobrar"
        },
        {
            "data" : "monto_cuota"
        },
        {
            "data" : "monto_deuda"
        },
        {
            "data" : "fecha_vencimiento"
        },
        {
            "data" : "dias_atraso"
        },
        {
            "data" : "fecha_debitar"
        }
    ];

    let options_dt = {
        "order": [[3,"asc"], [0, "asc"]],
		createdRow : function( row, data, dataIndex){
		
    	}
    };

    tp_respuestaBanco = TablaPaginada('tp_generacion_debitos',0,'asc','','', null , columns, columnDefs, options_dt);

    document.getElementById("tipo_debito").onchange=function(){

        if ($("#tipo_debito").val() == "1")
        {

            if ($("#banco_debitador").val() == "1")
            {
                document.getElementById("clientes_bancos").value = 1;
            }

            if ($("#banco_debitador").val() == "2")
            {
                 document.getElementById("clientes_bancos").value = 3;
            }
        }

        if ($("#tipo_debito").val() == "2")
        {
            document.getElementById("clientes_bancos").value = 2;
        }

    }

    document.getElementById("banco_debitador").onchange=function(){

        if ($("#tipo_debito").val() == "1")
        {

            if ($("#banco_debitador").val() == "1")
            {
                document.getElementById("clientes_bancos").value = 1;
            }

            if ($("#banco_debitador").val() == "2")
            {
                 document.getElementById("clientes_bancos").value = 3;
            }
        }

        if ($("#tipo_debito").val() == "2")
        {
            document.getElementById("clientes_bancos").value = 2;
        }

    }

    $("#tipo_archivo").change(function(){

        if ($("#tipo_archivo").val() == "1")
        {
            $("#estado_deuda").val("1");
            $("#rangoDiv").removeClass('hide');
            $("#atrasoDesdeDiv").removeClass('hide');
            $("#atrasoHastaDiv").removeClass('hide');
            $("#vencimientoDesdeDiv").removeClass('hide');
            $("#vencimientoHastaDiv").removeClass('hide');

            $("#atrasoDesdeDiv").removeClass('hide');
            $("#atrasoHastaDiv").removeClass('hide'); 
            $("#vencimientoDesdeDiv").addClass('hide');
            $("#vencimientoHastaDiv").addClass('hide');
        }

        if ($("#tipo_archivo").val() == "2")
        {
            $("#estado_deuda").val("2");
            $("#estado_deuda").trigger('chamge');
            $("#rangoDiv").addClass('hide');
            $("#atrasoMayorDiv").addClass('hide');
            $("#atrasoDesdeDiv").addClass('hide');
            $("#atrasoHastaDiv").addClass('hide');
            $("#vencimientoDesdeDiv").addClass('hide');
            $("#vencimientoHastaDiv").addClass('hide');
        }

    });

    $("#estado_deuda").change(function(){
        
        if ($("#estado_deuda").val() == "1")
        {
            $("#rangoDiv").removeClass('hide');
            $("#atrasoDesdeDiv").removeClass('hide');
            $("#atrasoHastaDiv").removeClass('hide');
            $("#vencimientoDesdeDiv").removeClass('hide');
            $("#vencimientoHastaDiv").removeClass('hide');

            $("#atrasoDesdeDiv").removeClass('hide');
            $("#atrasoHastaDiv").removeClass('hide'); 
            $("#vencimientoDesdeDiv").addClass('hide');
            $("#vencimientoHastaDiv").addClass('hide');

        } 

        if ($("#estado_deuda").val() == "2")
        {
            $("#rangoDiv").addClass('hide');
            $("#atrasoDesdeDiv").addClass('hide');
            $("#atrasoHastaDiv").addClass('hide');
            $("#vencimientoDesdeDiv").addClass('hide');
            $("#vencimientoHastaDiv").addClass('hide');
        } 

    });    

    $("#rango").change(function(){
        
        if ($("#rango").val() == "1")
        {
            $("#atrasoMayorDiv").addClass('hide');
            $("#atrasoDesdeDiv").removeClass('hide');
            $("#atrasoHastaDiv").removeClass('hide'); 
            $("#vencimientoDesdeDiv").addClass('hide');
            $("#vencimientoHastaDiv").addClass('hide');

        } 

        if ($("#rango").val() == "2")
        {
            $("#atrasoMayorDiv").addClass('hide');
            $("#atrasoDesdeDiv").addClass('hide');
            $("#atrasoHastaDiv").addClass('hide'); 
            $("#vencimientoDesdeDiv").removeClass('hide');
            $("#vencimientoHastaDiv").removeClass('hide');
        } 

        if ($("#rango").val() == "3")
        {
            $("#atrasoMayorDiv").removeClass('hide');
            $("#atrasoDesdeDiv").addClass('hide');
            $("#atrasoHastaDiv").addClass('hide'); 
            $("#vencimientoDesdeDiv").addClass('hide');
            $("#vencimientoHastaDiv").addClass('hide');
        } 

        if ($("#rango").val() == "4")
        {
            $("#atrasoMayorDiv").addClass('hide');
            $("#atrasoDesdeDiv").addClass('hide');
            $("#atrasoHastaDiv").addClass('hide'); 
            $("#vencimientoDesdeDiv").addClass('hide');
            $("#vencimientoHastaDiv").addClass('hide');
        } 

    });

    $("#tipo").change(function()
    {
        
        if ($("#tipo").val() == "1")
        {
            $("#aplicaTopeDiv").addClass('hide');
        } 

        if ($("#tipo").val() == "2")
        {
            $("#aplicaTopeDiv").removeClass('hide');

            if ($("#cantidad_debitos").val() == "1")
            {
                $("#aplicaTopeDiv").removeClass('hide');
            } 

            if ($("#cantidad_debitos").val() > "1")
            {
                $("#aplicaTopeDiv").addClass('hide');
            } 
        } 

    });

    $("#aplica_tope").change(function(){
        
        if ($("#aplica_tope").val() == "1"){
            $("#valorTopeDiv").removeClass('hide');
        } 

        if ($("#aplica_tope").val() == "0"){
            $("#valorTopeDiv").addClass('hide');
        } 

    });
    
    $("#cantidad_debitos").change(function()
    {
        
        if ($("#cantidad_debitos").val() == "1")
        {

            $("#aplicaTopeDiv").addClass('hide');

            if ($("#tipo").val() == "2")
            {
                $("#aplicaTopeDiv").removeClass('hide');
            }

            $("#tipoFacturaUnicaDivContent").addClass('hide'); 

            $("#tipoFactura1DivContent").addClass('hide'); 
            $("#tipoFactura2DivContent").addClass('hide'); 
            $("#tipoFactura3DivContent").addClass('hide'); 
            $("#tipoFactura4DivContent").addClass('hide'); 
            $("#tipoFactura5DivContent").addClass('hide'); 
        } 

        if ($("#cantidad_debitos").val() == "2")
        {

            $("#aplicaTopeDiv").addClass('hide');

            $("#tipoFacturaUnicaDivContent").addClass('hide'); 

            $("#tipoFactura1DivContent").removeClass('hide'); 
            $("#tipoFactura2DivContent").removeClass('hide'); 
            $("#tipoFactura3DivContent").addClass('hide'); 
            $("#tipoFactura4DivContent").addClass('hide'); 
            $("#tipoFactura5DivContent").addClass('hide'); 
        } 

        if ($("#cantidad_debitos").val() == "3")
        {

            $("#aplicaTopeDiv").addClass('hide');
            $("#tipoFacturaUnicaDivContent").addClass('hide'); 

            $("#tipoFactura1DivContent").removeClass('hide'); 
            $("#tipoFactura2DivContent").removeClass('hide'); 
            $("#tipoFactura3DivContent").removeClass('hide'); 
            $("#tipoFactura4DivContent").addClass('hide'); 
            $("#tipoFactura5DivContent").addClass('hide'); 
        } 

        if ($("#cantidad_debitos").val() == "4")
        {

            $("#aplicaTopeDiv").addClass('hide');
            $("#tipoFacturaUnicaDivContent").addClass('hide'); 

            $("#tipoFactura1DivContent").removeClass('hide'); 
            $("#tipoFactura2DivContent").removeClass('hide'); 
            $("#tipoFactura3DivContent").removeClass('hide'); 
            $("#tipoFactura4DivContent").removeClass('hide'); 
            $("#tipoFactura5DivContent").addClass('hide'); 
        } 

        if ($("#cantidad_debitos").val() == "5")
        {

            $("#aplicaTopeDiv").addClass('hide');
            $("#tipoFacturaUnicaDivContent").addClass('hide'); 

            $("#tipoFactura1DivContent").removeClass('hide'); 
            $("#tipoFactura2DivContent").removeClass('hide'); 
            $("#tipoFactura3DivContent").removeClass('hide'); 
            $("#tipoFactura4DivContent").removeClass('hide'); 
            $("#tipoFactura5DivContent").removeClass('hide'); 
        }   

    });    

    $("#banco_debitador").change(function(){

        if ($("#banco_debitador").val() == "1"){
        }

        if ($("#banco_debitador").val() == "2"){
        }

    });

    $("#btnGenerarVista").on('click', function(e){

        $('#btnGenerarVista').attr("disabled", true);
        $('#btnEnviarArchivos').attr("disabled", true);
        $('#btnDescargarArchivos').attr("disabled", true);
        $('#btnPreNotificacion').attr("disabled", true);

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

        formData.set('action', 'generar');

        generarVistaDebitos(formData);
    });

    $("#btnEnviarArchivos").on('click', function(e){
        
        $('#btnEnviarArchivos').attr("disabled", true);
        $('#btnGenerarVista').attr("disabled", true);
        $('#btnDescargarArchivos').attr("disabled", true);
        $('#btnPreNotificacion').attr("disabled", true);

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

        formData.set('action', 'enviar');

        enviarArchivoDebitos(formData);
    });

    $("#btnDescargarArchivos").on('click', function(e){
        
        $('#btnEnviarArchivos').attr("disabled", true);
        $('#btnGenerarVista').attr("disabled", true);
        $('#btnDescargarArchivos').attr("disabled", true);
        $('#btnPreNotificacion').attr("disabled", true);

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

        formData.set('action', 'descargar');

        descargarArchivosDebito(formData);
    });

    $("#btnPreNotificacion").on('click', function(e){
        
        //console.log("btnPreNotificacion");
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

        var url = $("input#base_url").val()+"tesoreria/generacion/generarPreNotificacionBancoBogota";

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            contentType: false,
            processData: false,
            data: formData,
            beforeSend : function(){

                $('#btnGenerarVista').attr("disabled", true);
                $('#btnEnviarArchivos').attr("disabled", true);
                $('#btnDescargarArchivos').attr("disabled", true);
                $('#btnPreNotificacion').attr("disabled", true);

                disabledButtons(true);

            },
            complete : function(){

                disabledButtons(false);  

                $('#btnGenerarVista').attr("disabled", false);
                $('#btnEnviarArchivos').attr("disabled", false);
                $('#btnDescargarArchivos').attr("disabled", false);
                $('#btnPreNotificacion').attr("disabled", false);

            },
            success : function(response){
    
                let base_url_medios_fac = response.url_facturacion;
                let base_url_medios_nov = response.url_novedad;
    
                window.open(base_url_medios_fac, '_blank');
                window.open(base_url_medios_nov, '_blank');
                
                $('#btnGenerarVista').attr("disabled", false);
                $('#btnEnviarArchivos').attr("disabled", false);
                $('#btnDescargarArchivos').attr("disabled", false);
                $('#btnPreNotificacion').attr("disabled", false);
    
                if(response){
    
                    toastr["success"]('Generacion de Debito', "Respuesta Descarga Archivos Debito" );
    
                }else{
                    toastr["warning"](response.response.mensaje, "Respuesta Descarga Archivos Debito" );
                }
    
            },
            error(xhr,status,error){
                //Ajax request failed.
                console.log(xhr);
                console.log(status);
                console.log(error);
                toastr["error"]("No se pudo completar el envio de los archivos .", "Error" );
            }
            
        }) 
    });

}

function descargarArchivosDebito(dataRespuesta)
{
    var url = $("input#base_url").val()+"tesoreria/generacion/descargarArchivos";
    console.log("descargarArchivosDebito");
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: dataRespuesta,
        beforeSend : function(){
            $('#btnGenerarVista').attr("disabled", true);
            $('#btnEnviarArchivos').attr("disabled", true);
            $('#btnDescargarArchivos').attr("disabled", true);
            $('#btnPreNotificacion').attr("disabled", true);
            disabledButtons(true);
        },
        complete : function(){
            disabledButtons(false);  
            $('#btnGenerarVista').attr("disabled", false);
            $('#btnEnviarArchivos').attr("disabled", false);
            $('#btnDescargarArchivos').attr("disabled", false);
            $('#btnPreNotificacion').attr("disabled", false);
        },
        success : function(response){

            //console.log(response.url_facturacion);
            //console.log(response.url_novedad);

            let base_url_medios_fac = response.url_facturacion;
            let base_url_medios_nov = response.url_novedad;

            window.open(base_url_medios_fac, '_blank');
            window.open(base_url_medios_nov, '_blank');

            $('#btnGenerarVista').attr("disabled", false);
            $('#btnEnviarArchivos').attr("disabled", false);
            $('#btnDescargarArchivos').attr("disabled", false);
            $('#btnPreNotificacion').attr("disabled", false);

            if(response){

                toastr["success"]('Generacion de Debito', "Respuesta Descarga Archivos Debito" );

            }else{
            	toastr["warning"](response.response.mensaje, "Respuesta Descarga Archivos Debito" );
            }

        },
        error(xhr,status,error){
            //Ajax request failed.
            console.log(xhr);
            console.log(status);
            console.log(error);
            toastr["error"]("No se pudo completar el envio de los archivos .", "Error" );
        }
        
    })
}

function generarVistaDebitos(dataRespuesta)
{

    var url = $("input#base_url").val()+"tesoreria/generacion/generarDebitos";

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: dataRespuesta,
        beforeSend : function(){
            $('#btnGenerarVista').attr("disabled", true);
            $('#btnEnviarArchivos').attr("disabled", true);
            $('#btnDescargarArchivos').attr("disabled", true);
            $('#btnPreNotificacion').attr("disabled", true);
            disabledButtons(true);
        },
        complete : function(){
            disabledButtons(false);  
            $('#btnGenerarVista').attr("disabled", false);
            $('#btnEnviarArchivos').attr("disabled", false);
            $('#btnDescargarArchivos').attr("disabled", false);
            $('#btnPreNotificacion').attr("disabled", false);
        },
        success : function(response){
            $('#btnGenerarVista').attr("disabled", false);
            $('#btnEnviarArchivos').attr("disabled", false);
            $('#btnDescargarArchivos').attr("disabled", false);
            $('#btnPreNotificacion').attr("disabled", false);
            if(response){

                $("#detalle-vista-debito-automatico").html("");
                $("#tp_generacion_debitos").dataTable().api().clear().draw();
        	 	$("#tp_generacion_debitos").dataTable().api().rows.add(response.clientes); // Add new data
    			$("#tp_generacion_debitos").dataTable().api().columns.adjust().draw(); // Redraw the DataTable
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

function enviarArchivoDebitos(dataRespuesta)
{

    var url = $("input#base_url").val()+"tesoreria/generacion/generarDebitos";

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
            $('#btnDescargarArchivos').attr("disabled", true);
            $('#btnPreNotificacion').attr("disabled", true);
        },
        complete : function(){
            disabledButtons(false);  
            $('#btnEnviarArchivos').attr("disabled", false);
            $('#btnGenerarVista').attr("disabled", false);
            $('#btnDescargarArchivos').attr("disabled", false);
            $('#btnPreNotificacion').attr("disabled", false);
        },
        success : function(response){
            $('#btnEnviarArchivos').attr("disabled", false);
            $('#btnGenerarVista').attr("disabled", false);
            $('#btnDescargarArchivos').attr("disabled", false);
            $('#btnPreNotificacion').attr("disabled", false);
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