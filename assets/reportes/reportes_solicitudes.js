$(document).ready(($) =>
{
     //Date range as a button
    $('.date_range').daterangepicker(
    {
        autoUpdateInput: false,
        ranges   : 
        {
          'Hoy'       : [moment(), moment()],
          'Ayer'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Últimos 7 Días' : [moment().subtract(6, 'days'), moment()],
          'Últimos 30 Días': [moment().subtract(29, 'days'), moment()],
          'Mes Anterior'  : [moment().startOf('month'), moment().endOf('month')],
          'Últimos Mes'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "locale": 
        {
            "format": "DD-MM-YYYY",
            "separator": " | ",
            "applyLabel": "Guardar",
            "cancelLabel": "Cancelar",
            "fromLabel": "Desde",
            "toLabel": "Hasta",
            "customRangeLabel": "Personalizar",
            "daysOfWeek": ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
            "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
            "firstDay": 1
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment(),
        timePicker: false,
    },

        function (start, end) 
        {
            $('#daterange-btn span').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'))
        }
    );

    $('.date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' | ' + picker.endDate.format('DD-MM-YYYY'));
        });

    $('.date_range').on('cancel.daterangepicker', function(ev, picker)
    {
      $(this).val('');
    })
    var modulo_reportes = $("#reportes_dashboard_principal");
		
    // EVENTOS
    modulo_reportes.find("#btn_solicitudes").on('click',(elem) =>{
        modulo_reportes.find("#solicitudes_sub_menu").toggle();
    });
    
    modulo_reportes.find("#btn_solicitudes_indicadores").on('click', function(){
        
        modulo_reportes.find("#box_solicitud_indicadores_filtro").show();
    });
})    