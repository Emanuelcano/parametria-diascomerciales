    // chequeo si hubo algun cambio en los select     
    
    $('.fechas').change(function (event){
        $.ajax({
            type: "POST",
            url: base_url + "reportes/reportes/datosOperadores",
            dataType: 'json',
            data:{
                datoInicio: $('#date_inicio').val(),
                datoFin: $('#date_fin').val()        
            },
            success:function(resultado){
                if (resultado == '') {
                   
                    $('#sl_operador').empty();
                    $('#sl_operador').append('<option value="0" selected="selected"> TODOS </option>');
                    
                }else{
                    i=0;
                    while (i < resultado.length) {
                        $('#sl_operador').append('<option value="'+resultado[i].idoperador+'"> '+resultado[i].nombre_apellido+' </option>');
                        //console.log(resultado[i].idoperador, resultado[i].nombre_apellido);
                        i++;
                    }    
            }
            }
            });

    });

    $('button#btnExportar-xls').click(function (event) {
        //event.preventDefault();
        var $loader = $('.loader');
        
        if ($('#date_inicio').val() == '') {
            Swal.fire({
                title: 'Atencion!',
                text: 'Debe ingresar fecha de inicio',
            })
            $('#date_inicio').focus();
        }
        if ($('#date_fin').val() == '') {
            Swal.fire({
                title: 'Atencion!',
                text: 'Debe ingresar fecha de fin',
            })
            $('#date_fin').focus();
        }else if($('#date_inicio, #date_fin').val() != ''){
            $.ajax({
                url: base_url + $('#frm-exportar_asignacion').attr('action'),
                type:$('#frm-exportar_asignacion').attr('method'),
                data:$('#frm-exportar_asignacion').serialize(),
        
                beforeSend: function() {
                  // aquí puedes poner el código paraque te muestre el gif
                    $("#compose-modal-wait").modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    });
                    $loader.show();
                },
                success:function(respuesta){
                    setTimeout(function(){
                    $loader.hide();
                    $("#compose-modal-wait").modal('hide');
                    }, 1000);                    
                    let url = base_url+"public/csv/"+respuesta;
                    //console.log(respuesta);
                    window.open(url, '_self');
                }
            });
        }
    })



