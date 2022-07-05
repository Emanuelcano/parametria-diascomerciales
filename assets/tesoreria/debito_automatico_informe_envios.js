$(document).ready(function(){
    cargarInformeEnvios();
})

var tp_imputacionAutomatica;

var cargarInformeEnvios = function(){

    columnDefs = [
        {
            'targets': [1],
            'createdCell':  function (td, cellData, rowData, row, col) {
                $(td).attr('style', 'padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;'); 
            }
        }
    ];

    options = {
        "order": [[3,"asc"], [0, "asc"]],
        createdRow : function( row, data, dataIndex){

        }
    }

    columns = [
        {
            "data" : "FactAchivo"
        },
        {
            "data" : "id_detalle_credito"
        },
        {
            "data" : "fecha_subida"
        },
        {
            "data" : "Fact"
        },
        {
            "data" : "Debita"
        }
    ];

    TablaPaginada('tp_lectura_bancolombia_envio_rcga',0,'asc','','', null , columns, columnDefs, options);

    columns = [
        {
            "data": "convenio",
        },
        {
            "data" : "NovAchivo"
        },
        {
            "data" : "id_detalle_credito"
        },
        {
            "data" : "fecha_subida"
        },
        {
            "data" : "Nov"
        },
        {
            "data" : "Inicia"
        },
        {
            "data" : "Finaliza"
        }
    ];

    TablaPaginada('tp_lectura_bancolombia_envio_rnov',0,'asc','','', null , columns, columnDefs, options);

    $("#btnConfirmar").on('click', function(e){
        
        $('#btnConfirmar').attr("disabled", true);

        var form = $('#lecturarcga');
        var formData = new FormData();
        var dataLecturaRcga  = $('#lecturarcga').serializeArray();

        $.each(form.find('input[type="file"]'), function(i, tag){
            $.each($(tag)[0].files, function(i, file){
                formData.set(tag.name, file);
            })
        })

        $.each(dataLecturaRcga, function(i, val) {
            formData.set(val.name, val.value);
        });

        procesarInformeEnvios(formData);
    });

}

function procesarInformeEnvios(dataRespuestaInformeEnvios){

    var url = $("input#base_url").val()+"tesoreria/Envios/procesarInformeEnvios";

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: dataRespuestaInformeEnvios,
        beforeSend : function(){
            $('#btnConfirmar').attr("disabled", true);
        },
        complete : function(){ 
            $('#btnConfirmar').attr("disabled", false);
        },
        success : function(response){
            $('#btnConfirmar').attr("disabled", false);
            
            //console.log("AAA" + response.enviosRcga);
            if(response){
                
                $("#detalle-vista-debito-automatico").html("");

                $("#nombreArchivos").html(response.nombreArchivos);
                $("#cantidadCasosRcga").html(response.cantidadCasosRcga);
                $("#cantidadCasosRnov").html(response.cantidadCasosRnov);
                $("#totalXCodRespRcga").html(response.totalXCodRespRcga);
                $("#totalXCodRespRnov").html(response.totalXCodRespRnov);

                $("#tp_lectura_bancolombia_envio_rcga").dataTable().api().clear().draw();
                $("#tp_lectura_bancolombia_envio_rnov").dataTable().api().clear().draw();

        	 	$("#tp_lectura_bancolombia_envio_rcga").dataTable().api().rows.add(response.enviosRcga); // Add new data
    			$("#tp_lectura_bancolombia_envio_rcga").dataTable().api().columns.adjust().draw(); // Redraw the DataTable

                $("#tp_lectura_bancolombia_envio_rnov").dataTable().api().rows.add(response.enviosRnov); // Add new data
                $("#tp_lectura_bancolombia_envio_rnov").dataTable().api().columns.adjust().draw(); // Redraw the DataTable  

                toastr["success"]('Informes', "Informe de Envios" );

            }else{
            	toastr["warning"](response.response.mensaje, "Informe de Envios" );
            }
            
        },
        error(xhr,status,error){
            //Ajax request failed.
            $('#btnConfirmar').attr("disabled", false);
            console.log(xhr);
            console.log(status);
            console.log(error);
            toastr["error"]("No se pudo completar la carga del archivo.", "Error" );
        }
    })
    
}