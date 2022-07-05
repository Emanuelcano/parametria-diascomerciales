    $('#btn_exportar_cobros').on("click", function (event){
        event.preventDefault();
        var $btn = $('#btn');
        var $data = $('.data');
        var $loader = $('.loader');
        
        if ($('#sl_desde_cobro').val() == '') {
            Swal.fire({
                icon: 'error',
                title: 'Atencion!',
                text: 'Debe ingresar fecha de inicio',
            })
            $('#sl_desde_cobro').focus();
        }
        if ($('#sl_hasta_cobro').val() == '') {
            Swal.fire({
                icon: 'error',
                title: 'Atencion!',
                text: 'Debe ingresar fecha de fin',
            })
            $('#sl_hasta_cobro').focus();
        }else if($('#sl_desde_cobro, #sl_hasta_cobro').val() != ''){
            $.ajax({
                url: base_url + 'reportes/Reportes/exportar_reporte_cobros',
                type:$('#frm_exportar_cobro').attr('method'),
                data:$('#frm_exportar_cobro').serialize(),
                beforeSend: function() {
                  // aquí puedes poner el código paraque te muestre el gif
                    $("#compose-modal-wait-cobros").modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    });
                    $loader.show();
                },
                success:function(respuesta){
                    setTimeout(function(){
                    $loader.hide();
                    $("#compose-modal-wait-cobros").modal('hide');
                    }, 1000)
                    
                    let url = base_url+"public/csv/"+respuesta;
                    window.open(url, '_self');
                }
            });
        }
    });
