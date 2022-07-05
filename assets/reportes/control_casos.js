    // chequeo si hubo algun cambio en los select     
    
    $( document ).ready(function() {
        var casos ="si";
        $.ajax({
            type: "POST",
            url: base_url + "reportes/reportes/datosOperadoresFraude",
            dataType: 'json',
            data:{
                casos: casos       
            },
            success:function(resultado){          
                    for (var i=0; i<resultado.length; i++) {
                        $('#slc_operador').append('<option value="'+resultado[i].idoperador+'"> '+resultado[i].nombre_apellido+' </option>');  
                    }
            }
            });

    });

    $('button#btnExportar_casos').click(function (event) {
        event.preventDefault();
        var $loader = $('.loader');        
        if ($('#dato_inicio').val() == '') {
            Swal.fire({
                title: 'Atencion!',
                text: 'Debe ingresar fecha de inicio',
            })
            $('#dato_inicio').focus();
        }
        if ($('#dato_fin').val() == '') {
            Swal.fire({
                title: 'Atencion!',
                text: 'Debe ingresar fecha de fin',
            })
            $('#dato_fin').focus();
        }else if($('#dato_inicio, #dato_fin').val() != ''){
            $.ajax({
                url: base_url + $('#frm-casos_devueltos').attr('action'),
                type:$('#frm-casos_devueltos').attr('method'),
                data:$('#frm-casos_devueltos').serialize(),
                beforeSend: function() {
                  // aquí puedes poner el código paraque te muestre el gif
                    $("#compose-modal").modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    });
                    $loader.show();
                },
                success:function(respuesta){
                    setTimeout(function(){
                    $loader.hide();
                    $("#compose-modal").modal('hide');
                    }, 1000);
                    let url = base_url+"public/csv/"+respuesta;
                    window.open(url, '_self');
                }
            });
        }
    })



