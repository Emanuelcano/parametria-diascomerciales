$(document).ready(function() {
    cargarRespuestaBanco();
})
var tp_respuestaBanco;
var cargarRespuestaBanco = function() {
    let columnDefs = [{
            'targets': [1],
            'createdCell': function(td, cellData, rowData, row, col) {
                $(td).attr('style', 'padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;');
            }
        },
        {
            'targets': [6],
            "visible": false,
            "searchable": false
        }
    ];
    let columns = [{
            "data": "documento",
            "render": function(data, type, row, meta) {
                return data
            }
        },
        {
            "data": "monto"
        },
        {
            "data": "fecha_proceso"
        },
        {
            "data": "fecha_cobro"
        },
        {
            "data": "estado"
        },
        {
            "data": "causal"
        },
        {
            "data": "orden"
        }
    ]
    let options_dt = {
        "order": [
            [6, "asc"],
            [0, "asc"]
        ],
        createdRow: function(row, data, dataIndex) {
            if (data.respuesta == "solicitud_pagada") {
                $(row).addClass('success');
            } else if (data.respuesta == "solicitud_ya_pagados") {
                $(row).addClass('info');
            } else if (data.respuesta == "solicitud_no_pagadas") {
                $(row).addClass('danger');

            } else if (data.respuesta == "solicitud_pago_con_error") {
                $(row).addClass('warning');

            }
        }
    }
    tp_respuestaBanco = TablaPaginada('tp_respuestaBanco', 0, 'asc', '', '', null, columns, columnDefs, options_dt);

    $("#btnConfirmar").on('click', function(e) {

        if ($("#fileRespuestaBanco").val() !== "") {
            var form = $('#respuestaBanco');
            var formData = new FormData();
            var dataRespuestaBanco = $('#respuestaBanco').serializeArray();
            formData.append('fileRespuestaBanco', "");

            $.each(form.find('input[type="file"]'), function(i, tag) {
                $.each($(tag)[0].files, function(i, file) {
                    formData.set(tag.name, file);
                })
            })

            $.each(dataRespuestaBanco, function(i, val) {
                formData.set(val.name, val.value);
            });
            Swal.fire({
                title: 'Esta seguro?',
                text: "Procesamiento del archivo de Respuesta Banco Bogota : " + $("#fileRespuestaBanco").val().replace(/C:\\fakepath\\/i, ''),
                icon: 'warning',
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonColor: '#00a65a',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Confirmar'
            }).then((result) => {
                console.log(result);
                if (result.value) {
                    procesarRespuestaBanco(formData);
                }
            })
        } else {
            toastr["warning"]("Debe seleccionar un archivo valido.", "Respuesta de Banco Bogota");
        }
    });

}


function procesarRespuestaBanco(dataRespuestaBanco) {

    var url = $("input#base_url").val() + "tesoreria/tesoreria/procesarRespuestaBancobogota";
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'JSON',
        contentType: false,
        processData: false,
        data: dataRespuestaBanco,
        beforeSend: function() {
            disabledButtons(true);
            timerSweetAlert('Procesando Respuesta Bancobogota');
        },
        complete: function() {
            disabledButtons(false);
            Swal.close()
        },
        success: function(response) {
            if (response.response.respuesta) {
                $("#tp_respuestaBanco").dataTable().api().clear().draw();

                $("#tp_respuestaBanco").dataTable().api().rows.add(response.data); // Add new data
                $("#tp_respuestaBanco").dataTable().api().columns.adjust().draw(); // Redraw the DataTable
                toastr["success"](response.response.mensaje, "Respuesta de Bancogota");

            } else if (response.response.errors.length !== 0) {
                var message_errors = ""
                $.each(response.response.errors, function(i, el) {
                    message_errors += el + "<br>"
                })

                toastr["error"](message_errors, "Respuesta de Bancogota");

            } else {
                toastr["warning"](response.response.mensaje, "Respuesta de Bancogota");
            }

        },
        error(xhr, status, error) {
            //Ajax request failed.
            console.log(xhr);
            console.log(status);
            console.log(error);
            toastr["error"]("No se pudo completar la carga del archivo.", "Error");
        }
    })

}