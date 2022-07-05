function cargaInicial() {
    let base_url = $("#base_url").val();
    let selectTipo = $("#selectTipo").val();
    let fecha = $("#date_range").val();


    // $('#graphContainer').hide();

    $.ajax({
        dataType: "JSON",
        data: {
            "selectTipo": selectTipo, 
            "fecha": fecha
        },
        url: base_url + 'tableroFlujo/graficas',
        type: 'POST',
        beforeSend: function() {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#main").html(loading);
            // $('#main').show();
            $('#modalLoading').modal("show");
        }
    })
    .done(function(respuesta){
        // $('#main').hide();
        $('#modalLoading').modal("hide");
        for (let id in Chart.instances) {
            Chart.instances[id].destroy();
        }
    
        if (respuesta.pie.length > 0) {
            $('#graphContainer').show();
            let totalSolicitudes = 0;
            let labelsPie = [];
            let dataPie = [];
            let backgroundColorPie = [];
            let labelsBar = [];
            let dataBar = [];
            let backgroundColorBar = [];
            let labelsLine = [];
            let dataLineOne = [];
            let dataLineTwo = [];
            let dataLineThree = [];
            let datasets = [];
            /*** Se obtiene primero el total de Solicitudes ***/
            respuesta.pie.map(function (dato) {
                totalSolicitudes += parseInt(dato.cantidad);
            });
            /*** PIE ***/
            $('#total').text(totalSolicitudes);
            $('#periodoPie').text($('#date_range').val());
            $('#tipoPie').text($('#selectTipo').val());            
            respuesta.pie.map(function (dato, index) {
                labelsPie[index] = dato.respuesta_analisis + ': ' + dato.cantidad;
                dataPie[index] = ((parseInt(dato.cantidad) * 100)/totalSolicitudes).toFixed(2);
            });
            backgroundColorPie = [
                'rgba(255, 206, 86, 0.75)', // yellow
                'rgba(75, 192, 192, 0.75)', // green
                'rgba(255, 99, 132, 0.75)', // red
            ];
            datasets = [
                {
                    data: dataPie,
                    backgroundColor: backgroundColorPie,
                    borderWidth: 1,
                    datalabels: {
                        align: 'center',
                        labels: {
                            value: {
                                font: {
                                    weight: 'bold',
                                }
                            }
                        },
                        formatter: function(value, context) {
                            return value + ' %';
                        }
                    },    
                }
            ];
            renderGraph('myChartPie', 'pie', datasets, labelsPie);
            /*** BAR ***/
            $('#periodoBar').text($('#date_range').val());
            $('#tipoBar').text($('#selectTipo').val());            
            respuesta.bar.map(function (dato, index) {
                labelsBar[index] = dato.descripcion_paso + ' ' + dato.cantidad;
                dataBar[index] = ((parseInt(dato.cantidad) * 100)/totalSolicitudes).toFixed(2);
            });
            backgroundColorBar = [
                'rgba(255, 99, 132, 0.75)', // red
                'rgba(139, 195, 74, 0.75)', // green
                'rgba(54, 162, 235, 0.75)', // blue
                'rgba(75, 192, 192, 0.75)', // green
                'rgba(255, 206, 86, 0.75)', // yellow
                'rgba(153, 102, 255, 0.75)', // purple
                'rgba(255, 159, 64, 0.75)', // orange
                'purple',
                'blue',
                'yellow',
                'rgba(191, 0, 0, 0.75)', // otro rojo
                'red',
                'orange'
            ];
            datasets = [
                {
                    label: '% Status flujo',
                    data: dataBar,
                    backgroundColor: backgroundColorBar,
                    borderWidth: 1,
                    datalabels: {
                        align: 'top',
                        labels: {
                            value: {
                                font: {
                                    weight: 'bold',
                                }
                            }
                        },
                        formatter: function(value, context) {
                            return value + ' %';
                        }
                    },    
                }
            ];
            renderGraph('myChartBar', 'bar', datasets, labelsBar);
            /*** LINE Solicitudes ***/
            $('#periodoLine').text($('#date_range').val());
            $('#tipoLine').text($('#selectTipo').val());            
            let k = 0;
            for (let i = 0; i < 24; i++) {
                labelsLine[i] = i;
                if (respuesta.lineOne[k]) {
                    if (respuesta.lineOne[k].hora == i) {
                        dataLineOne[i] = respuesta.lineOne[k].cantidad;
                        k++;
                    } else {
                        dataLineOne[i] = "0";
                    }
                } else {
                    dataLineOne[i] = "0";
                }
            }
            /*** LINE Solicitudes Aprobadas ***/
            k = 0;
            for (let i = 0; i < 24; i++) {
                if (respuesta.lineTwo[k]) {
                    if (respuesta.lineTwo[k].hora == i) {
                        dataLineTwo[i] = respuesta.lineTwo[k].cantidad;
                        k++;
                    } else {
                        dataLineTwo[i] = "0";
                    }
                } else {
                    dataLineTwo[i] = "0";
                }
            }
            /*** LINE Solicitudes Rechazadas ***/
            k = 0;
            for (let i = 0; i < 24; i++) {
                if (respuesta.lineThree[k]) {
                    if (respuesta.lineThree[k].hora == i) {
                        dataLineThree[i] = respuesta.lineThree[k].cantidad;
                        k++;
                    } else {
                        dataLineThree[i] = "0";
                    }
                } else {
                    dataLineThree[i] = "0";
                }
            }

            datasets = [
                {
                    label: 'Solicitudes por hora',
                    data: dataLineOne,
                    backgroundColor: 'rgba(255, 99, 132, 0)',
                    borderColor: 'rgba(54, 162, 235, 1)', // blue
                    borderWidth: 1,
                    datalabels: {
                        labels: {
                            value: {
                                font: {
                                    weight: 'bold',
                                    size: 9
                                }
                            }
                        },
                    },        
                },
                {
                    label: 'Aprobadas por hora',
                    data: dataLineTwo,
                    backgroundColor: 'rgba(255, 99, 132, 0)',
                    borderColor: 'rgba(75, 192, 192, 1)', // green,
                    borderWidth: 1,
                    datalabels: {
                        labels: {
                            value: {
                                font: {
                                    weight: 'bold',
                                    size: 9
                                }
                            }
                        },
                    },        
                },
                {
                    label: 'Rechazadas por hora',
                    data: dataLineThree,
                    backgroundColor: 'rgba(255, 99, 132, 0)',
                    borderColor: 'rgba(255, 99, 132, 1)', // red
                    borderWidth: 1,
                    datalabels: {
                        labels: {
                            value: {
                                font: {
                                    weight: 'bold',
                                    size: 9
                                }
                            }
                        },
                    },        
                }
        ];
            renderGraph('myChartLine', 'line', datasets, labelsLine);
        } else {
            $('#graphContainer').hide();
            Swal.fire("¡Información!",'Los criterios seleccionados no arrojan ningún dato para graficar',"info");
        }
    })
    .fail(function(xhr) {
        $('#modalLoading').modal("hide");
        Swal.fire("¡Atención!", 
            `readyState: ${xhr.readyState}
                status: ${xhr.status}
                responseText: ${xhr.responseText}`,
            "error"
        );
    });
}
/*********************************/
/*** Función para los gráficos ***/
/*********************************/
function renderGraph(el, type, datasets, labelData) {
    let ctx = document.getElementById(el).getContext('2d');
    x = new Chart(ctx, {
        type: type,
        data: {
            labels: labelData,
            datasets: datasets
        },
        options: {
            plugins: {
                datalabels: {
                    color: '#000000',
                    align: 'end',
                }
            }
        }
    });
}
/*******************/
/*** Botón envío ***/
/*******************/
$('#aEnvio').on('click', function (event){
    $('#modalBuscar').modal("hide");
    // event.preventDefault();
    cargaInicial();
});
/***********************/
/*** daterangepicker ***/
/***********************/
$("#date_range").daterangepicker(
    {
        autoUpdateInput: false,
        ranges: {
            Hoy: [moment(), moment()],
            Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "Últimos 7 Días": [moment().subtract(6, "days"), moment()],
            "Últimos 30 Días": [moment().subtract(29, "days"), moment()],
            "Mes Actual": [moment().startOf("month"), moment().endOf("month")],
            "Mes Anterior": [
                moment()
                    .subtract(1, "month")
                    .startOf("month"),
                moment()
                    .subtract(1, "month")
                    .endOf("month")
            ]
        },
        locale: {
            format: "DD-MM-YYYY",
            separator: " | ",
            applyLabel: "Guardar",
            cancelLabel: "Cancelar",
            fromLabel: "Desde",
            toLabel: "Hasta",
            customRangeLabel: "Personalizar",
            daysOfWeek: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
            monthNames: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            firstDay: 1
        },
        startDate: moment().subtract(29, "days"),
        endDate: moment(),
        timePicker: false
    },
    function(start, end) {
        $("#daterange-btn span").html(
            start.format("DD-MM-YYYY") + " - " + end.format("DD-MM-YYYY")
        );
    }
);

$("#date_range").on("apply.daterangepicker", function(ev, picker) {
    $(this).val(
        picker.startDate.format("DD-MM-YYYY")
        + " | " +
        picker.endDate.format("DD-MM-YYYY")
    );
});

$("#date_range").on("cancel.daterangepicker", function(ev, picker) {
    $(this).val("");
});