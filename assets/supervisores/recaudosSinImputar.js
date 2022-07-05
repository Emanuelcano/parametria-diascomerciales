crearDataTable();

function crearDataTable() {
    let ajax = {
        'type': "POST",
        'url': base_url + 'supervisores/Supervisores/obtenerRecuadosSImputar'
    };

    let columns = [
            { "data": "documento" },
            { "data": "fecha_recaudo" },
            { "data": "monto_total" },
            { "data": "origen_pago" },
            { "data": null,
                "render": function(data, type, row, meta ){
                var btnSinImputar = "";
                btnSinImputar = "<button class='btn btn-xs btn-primary' onclick='sinImputar("+data["id"]+");' title='Imputar Credito'>"+
                    "<i class='fa fa-upload'></i></button>";
                return btnSinImputar;
            }}
    ];
    TablaPaginada("table_sin_imputar", 1, "asc", '', '', ajax, columns);
}

function sinImputar(id) {
    $(".sol_num").val('');
    $("#div_data_cliente").css("display", "none");
    $("#div_nodata").css("display", "none");
    $(".data_cliente").remove();
    $('#btn_imputar').attr('disabled','disabled');
    $.ajax({
        type: "POST",
        url: base_url + "supervisores/Supervisores/obtenerRecuadosSImputar",
        data: {"id": id},
        success: function (respuesta) {
            var response = eval(respuesta);
            $("#fecha_rec").val(response[0].fecha_recaudo);
            $("#monto_rec").val(response[0].monto_total);
            $("#origen_rec").val(response[0].origen_pago);
            $("#docDep_rec").val(response[0].documento);
            $("#id_sin_impu").val(id);
            $("#modal_sin_imputar").modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
            $("#modal_sin_imputar").show();
        }
    });
}

$('.sol_num').click(function (event) {
    $(function(){
        $('.sol_num').keypress(function(e) {
            if(isNaN(this.value + String.fromCharCode(e.charCode))) 
            return false;
        }).on(function(e){
            e.preventDefault();
        });          
    });
});

$('#btn_verificar_doc').click(function (event) {
    $("#div_data_cliente").css("display", "none");
    $("#div_nodata").css("display", "none");
    let doc = $('#docTitular_rec').val();
    $("#doc_insert").val(doc);
    $(".data_cliente").remove();
    if ($('#docTitular_rec').val() == '') {
        Swal.fire('¡Atención!', 'Debe ingresar un documento para la verificación', 'info')
        $('#docTitular_rec').focus();
    }else{
        var docValidar = $('#docTitular_rec').val();
        $.ajax({
            type: "POST",
            url: base_url + "supervisores/Supervisores/validarDocumento",
            data: {"documentoVal":docValidar},
            success: function (res) {
                let data = eval(res);
                if(data.length > 0){
                    $("#div_id_cliente").append("<p class='data_cliente' style='color:#f93232;'><b>"+data[0].id+"</b></p>");
                    $("#div_nombres_cliente").append("<p class='data_cliente' style='color:#f93232;'><b>"+data[0].nombres+"</b></p>");
                    $("#div_apellidos_cliente").append("<p class='data_cliente' style='color:#f93232;'><b>"+data[0].apellidos+"</b></p>");
                    $("#div_documento_cliente").append("<p class='data_cliente' style='color:#f93232;'><b>"+data[0].documento+"</b></p>");
                    $("#div_estado_cliente").append("<p class='data_cliente' style='color:#f93232;'><b>"+data[0].estado+"</b></p>");
                    $("#div_data_cliente").css("display", "block");
                    $("#btn_imputar").removeAttr('disabled');
                }else{
                    $("#div_nodata").append("<p class='data_cliente' style='color:#f93232;'><b>Cliente no posee crédito</b></p>");
                    $("#div_nodata").css("display", "block");
                }
            }
        });
    }
});

$("#btn_imputar").on("click", function () {
    let documento = $('#doc_insert').val();
    let id = $('#id_sin_impu').val();
    Swal.fire({
        title: '¿Esta seguro que deseea realizar esta imputación?',
        showCancelButton: true,
        showCancelButton: true,
        confirmButtonText: `Si`,
        cancelButtonText: `Cancelar`,
    }).then((result) => {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: base_url + "supervisores/Supervisores/imputarPago",
                data: {"documento":documento, "id":id},
                success: function (response) { 
                    if (response == "true") {
                        $("#table_sin_imputar").dataTable().fnDestroy();
                        Swal.fire('Imputación realizada con exitó', '', 'success')
                        $("#data").empty()
                        crearDataTable();
                        $('.btn_cerrar').trigger('click'); 
                    }else{
                        Swal.fire('Error al realizar la imputación', '', 'warning')
                        $('.btn_cerrar').trigger('click'); 
                    }                    
                }
            });
            
        } else {
            Swal.fire('Se ha cancelado la acción', '', 'info')
        }
    })
})