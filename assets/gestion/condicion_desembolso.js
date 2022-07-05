$(document).ready(function(){
    let solicitado_nuevo;
    let plazo_nuevo;
    let fecha_nueva;
    let fecha_otorgamiento;
    let id_solicitud;
    
    $("#btnRecalcular").on('click',function(e){ 

        solicitado_nuevo = $("#solicitado_nuevo option:selected").val();
        plazo_nuevo = $("#plazo_nuevo option:selected").val();
        fecha_nueva = $("#fecha_nueva").val();
        id_solicitud = $("#btnRecalcular").data('id_solicitud');

        recalcular_desembolso(id_solicitud, solicitado_nuevo, plazo_nuevo, fecha_nueva);
    });

    $("#btnRecalcularDetalle").on('click', function(e){
        solicitado_nuevo = $("#dc_solicitado_nuevo option:selected").val();
        plazo_nuevo = $("#dc_plazo_nuevo option:selected").val();
        fecha_nueva = $("#dc_fecha_nueva").val();
        fecha_otorgamiento = $("#dc_fecha_otorgamiento").val();
        id_solicitud = $("#btnRecalcularDetalle").data('id_solicitud');
        recalcular_desembolso(id_solicitud, solicitado_nuevo, plazo_nuevo, fecha_nueva, fecha_otorgamiento);
        
    });

    let messageDias = '<p>Súpero el numero de días.</p>';

    $("#plazo_nuevo").on('change', function(e){
        plazo_nuevo = $(this).val();

        let date1 = new Date();
        let date2 = new Date($("#fecha_nueva").val());
        var diffInTime = date2.getTime() - date1.getTime();
        var diffInDays = diffInTime / (1000 * 3600 * 24);

        if($(e.currentTarget).val() == 1 && Math.round(diffInDays) > 50){
            $("#fecha_nueva").val("");
            $("#recalcular_message span").addClass('text-danger').removeClass('text-success').html(messageDias);
            $("#calculo_dias").html("");
        }
    });

    $("#fecha_nueva").on('change', function(e){
        
        let d1 = new Date();
        let d2 = new Date($(e.target).val());
        var diffInTime = d2.getTime() - d1.getTime()
        var diffInDays = diffInTime / (1000 * 3600 * 24);

        var plazonuevo = $("#plazo_nuevo option:selected").val();
        var plazoAnterior = $("#plazo_anterior").data('plazoAnterior');

        var maxDaysAccept = 50;
        
        if( (Math.round(diffInDays) > 1 && Math.round(diffInDays)) <= maxDaysAccept){
            $("#calculo_dias").html("<strong>"+Math.round(diffInDays)+"<strong>");
            $("#recalcular_message span p").remove();
        }else{
            $("#recalcular_message span").addClass('text-danger').removeClass('text-success').html(messageDias);
            $(e.target).val("");
            $("#calculo_dias").html("");
        }
        
    });

    $("#dc_fecha_nueva").on('change', function(e){

        let currentFechaOtorg = ($("#dc_fecha_otorgamiento").val() === "") ? $("#data_fecha_otorgamiento").data('fecha-otorg-actual') : $("#dc_fecha_otorgamiento").val()

        let dc_d1 = new Date(currentFechaOtorg);
        let dc_d2 = new Date($(e.target).val());
        var dc_diffInTime = dc_d2.getTime() - dc_d1.getTime()
        var dc_diffInDays = dc_diffInTime / (1000 * 3600 * 24);

        var plazonuevo = $("#dc_plazo_nuevo option:selected").val();
        var plazoAnterior = $("#dc_plazo_anterior").data('dcPlazoAnterior');

        var maxDaysAccept = 50;

        if( ( Math.round(dc_diffInDays) > 0 && Math.round(dc_diffInDays) <= maxDaysAccept)  ) {
            
            $("#dc_calculo_dias").html("<strong>"+Math.round(dc_diffInDays)+"<strong>");
            $("#recalcular_message span p").remove();

        }else{
            $("#recalcular_message span").addClass('text-danger').removeClass('text-success').html(messageDias);
            $(e.target).val("");
            $("#dc_calculo_dias").html("");
        }
        
    });

    $("#dc_fecha_otorgamiento").on('change', function(e){
        
        let currentFechaInicial = ($("#dc_fecha_nueva").val() === "") ? $("#data_fecha_inicial").data('fecha-inicial-actual') : $("#dc_fecha_nueva").val()

        let dc_d1 = new Date($(e.target).val());
        let dc_d2 = new Date(currentFechaInicial);
        var dc_diffInTime = dc_d2.getTime() - dc_d1.getTime()
        var dc_diffInDays = dc_diffInTime / (1000 * 3600 * 24);

        var plazonuevo = $("#dc_plazo_nuevo option:selected").val();
        var plazoAnterior = $("#dc_plazo_anterior").data('dcPlazoAnterior');

        var maxDaysAccept = 50;

        if( Math.round(dc_diffInDays) > 0 && Math.round(dc_diffInDays) <= maxDaysAccept){
            $("#dc_calculo_dias").html("<strong>"+Math.round(dc_diffInDays)+"<strong>");
            $("#recalcular_message span p").remove();
        }else{
            $("#recalcular_message span").addClass('text-danger').removeClass('text-success').html(messageDias);
            $(e.target).val("");
            $("#dc_calculo_dias").html("");
        }
        
    });
    
})

function recalcular_desembolso(id_solicitud, solicitado_nuevo, plazo_nuevo, fecha_nueva, fecha_otorgamiento = ""){
    $.ajax({
        url: base_url+'api/condicion_desembolso/recalcular',
        type: 'POST',
        dataType: 'json',
        data: {id_solicitud: id_solicitud, solicitado_nuevo : solicitado_nuevo, plazo_nuevo:plazo_nuevo, fecha_nueva :fecha_nueva, fecha_otorgamiento : fecha_otorgamiento},
    })
    .done(function(response) {
        consultar_solicitud(id_solicitud);
        $("#recalcular_message span").addClass('text-success').removeClass('text-danger').html('<p>'+response.message+'</p>');
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });
    
}
