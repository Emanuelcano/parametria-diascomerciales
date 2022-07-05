var dateToday = new Date();
$('.datetimepicker').datepicker({
    tooltips: {
        today: 'Ve a la fecha actual',
        clear: 'Limpiar Selecion',
        close: 'Cerrar Caledario',
        selectMonth: 'Seleccione Mes',
        prevMonth: 'Mes Anterior',
        nextMonth: 'Mes Siguiente',
        selectYear: 'Selecione un Año',
        prevYear: 'Año Anterior',
        nextYear: 'Año Siguiente',
        selectDecade: 'Seleccione Decada',
        prevDecade: 'Decada Anterior',
        nextDecade: 'Siguiente Decada',
        prevCentury: 'Previous Century',
        nextCentury: 'Next Century'
    },
    icons: {
        time: "fa fa-clock-o",
        date: "fa fa-calendar",
        up: "fa fa-chevron-up",
        down: "fa fa-chevron-down",
        previous: 'fa fa-chevron-left',
        next: 'fa fa-chevron-right',
        today: 'fa fa-screenshot',
        clear: 'fa fa-trash',
        close: 'fa fa-remove'
    },
    dateFormat: 'dd-mm-yy'
    });
    

    $('#btn_exportar_gastos').on("click", function (event){
        event.preventDefault();
        var $btn = $('#btn');
        var $data = $('.data');
        var $loader = $('.loader');
        
        if ($('#sl_desde').val() == '') {
            Swal.fire({
                icon: 'error',
                title: 'Atencion!',
                text: 'Debe ingresar fecha de inicio',
            })
            $('#sl_desde').focus();
        }
        if ($('#sl_hasta').val() == '') {
            Swal.fire({
                icon: 'error',
                title: 'Atencion!',
                text: 'Debe ingresar fecha de fin',
            })
            $('#sl_hasta').focus();
        }else if($('#sl_desde, #sl_hasta').val() != ''){
            $.ajax({
                url: base_url + 'reportes/Reportes/exportar_reporte_gastos',
                type:$('#frm_exportar_gastos').attr('method'),
                data:$('#frm_exportar_gastos').serialize(),
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
                    }, 1000)
                    
                    let url = base_url+"public/csv/"+respuesta;
                    window.open(url, '_self');
                }
            });
        }
    });
