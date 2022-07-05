// const { isNull } = require("lodash");

var modulos = [];
var user_modulos = []
var calendar;

$(document).ready(function () {
    base_url = $("input#base_url").val();

});

function editarGrupo(id) {
    let id_medio= "";
    $("#registrar-grupo").css("display", "none");
    $("#actualizar-agente").css("display", "block");
    if (id != null) {
        base_url = $("input#base_url").val() + "api/ApiNotificaciones/get_grupo_update";
        $.ajax({
            type: "POST",
            url: base_url,
            data:{id:id},
            success: function (response) {
                //  console.log(response.data)
                 var registros = eval(response.data);
                //  console.log(registros)
                
                if (typeof (registros) != 'undefined') {
                    // console.log(registros)
                        
                            
                            $("#id_grupo_hd").val(registros.id_grupo_notificacion );
                            $("#name_group").val(registros.nombre_grupo );
                            // $("#sl_medio").val(registros.medios_notificacion );

                            // if (registros.medios_notificacion =="SLACK") {
                            //     id_medio = registros.notificados_slack 
                            // }else if (registros.medios_notificacion =="EMAIL") {
                            //     id_medio = registros.notificados_correos 
                            // }else if (registros.medios_notificacion =="SMS") {
                            //     id_medio = registros.notificados_sms 
                            // }
                            // var medio_enpartes= id_medio.split(",")
                            // console.log(medio_enpartes)
                            // $('#id_medio').select2('data', {id: 1, text: 'Lorem Ipsum'});
                            // $("#id_medio").select2('data',{medio_enpartes});
                            $("#txt_mensaje_enviar").val(registros.mensaje_notificar );
                            $("#sl_metodo").val(registros.metodo_notificacion );
                            $("#sl_action").val(registros.action );

                            
                            // $("#actualizar-grupo").css("display", "none");
                            $("#actualizar-grupo").css("display", "block");

                }
            }
        });
    }

}

function updateGrupo() {
   
}
function initTableAgenteCentral() {
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'api/ApiNotificaciones/tableGrupos',
        type: 'GET',

    })
        .done(function (response) {
            //  console.log(response.data)
            $('#tp_agenteCentral').dataTable().fnDestroy();
            if (response.ok) {
                $('#tp_agenteCentral').DataTable({
                    rowId: response.data.id_grupo_notificacion,
                    order: [[2, "asc"]],
                    data: response.data,
                    columns: [
                        { data: "id_grupo_notificacion" },
                        { data: "nombre_grupo" },
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                central_str = (data['medios_notificacion']).toUpperCase();
                                return central_str;
                            }
                        },
                        {
                            data: null,
                            
                            render: function (data, type, row, meta) {
                                // console.log(data['estado_agente']);
                                switch (data['metodo_notificacion']) {
                                    case "1":
                                        estado = "ENVIO";
                                        break;
                                    case "2":
                                        estado = "RECEPCION";
                                        break;
                                    case "1,2":
                                        estado = "ENVIO/RECEPCION";
                                        break;
                                    default:
                                        estado = data['metodo_notificacion'];
                                        break;
                                }
                                return estado;
                            }
                        },
                         
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                // console.log(data['estado_agente']);
                                switch (data['action']) {
                                    case "send":
                                        estado = "ENVIAR";
                                        break;
                                    case "block":
                                        estado = "BLOQUEAR";
                                        break;
                                    case "no_action":
                                        estado = "SIN ACCION";
                                        break;
                                    default:
                                        estado = data['action'];
                                        break;
                                }
                                return estado;
                            }
                        },
                        { data: "origen" },
                        {
                            data: null,
                            
                            render: function (data, type, row, meta) {
                                // console.log(data['estado_agente']);
                                switch (data['estatus']) {
                                    case "1":
                                        estado = "ACTIVO";
                                        break;
                                    case "0":
                                        estado = "INCATIVO";
                                        break;
                                    default:
                                        estado = data['estatus'];
                                        break;
                                }
                                return estado;
                            }
                        },
                        {
                            data: null,
                            render: function (data, type, row, meta) {
                                var Palabras =
                                    '<a class="btn btn-xs btn-success" title="Palabras" id="Palabras'+
                                    row.id_grupo_notificacion+'" data-origen="'+
                                    row.origen+'" data-id_grupo="'+
                                    row.id_grupo_notificacion+'" data-nombre_grupo="'+ row.nombre_grupo+'" onclick="mostrarPalabras(' +
                                    row.id_grupo_notificacion + ');" ><i class="fa fa-address-book" ></i></a>';
                                var EditarGrupo =
                                    '<a class="btn btn-xs btn-primary" title="EditarGrupo" onclick="editarGrupo(' +
                                    row.id_grupo_notificacion + ');"><i class="fa fa-pencil-square-o" ></i></a>';
                                var HabilitarAgente =
                                    '<a class="btn btn-xs btn-success" id="btnHabilitarAgente" data-estado="1" data-id_consultor="' + row.id_grupo_notificacion + '" title="Habilitar Agente", onclick="altaAgente(' +
                                    row.id_grupo_notificacion + ');"><i class="fa fa-check-square-o" ></i></a>';
                                var DeshabilitarAgente =
                                    '<a class="btn btn-xs btn-danger" id="btnDeshabilitarAgente" data-estado="0" data-id_consultor="' + row.id_grupo_notificacion + '" title="Deshabilitar Agente" onclick="bajaAgente(' +
                                    row.id_grupo_notificacion + ');"><i class="fa fa-ban" ></i></a>';
                                if (data['estatus'] == 1) {
                                    return Palabras + " " +EditarGrupo + " " + DeshabilitarAgente;

                                } else {
                                    return EditarGrupo + " " + HabilitarAgente;
                                }
                            }
                        }
                    ],
                    columnDefs: [
                        {
                            targets: [7],
                            createdCell: function (td, cellData, rowData, row, col) {
                                $(td).attr("style", "text-align: center; width: 10%;");
                            }
                        },]
                });

            }

        })
        .fail(function (response) {
            console.log(response.error);
        })
        .always(function (response) {
            //console.log("complete");
        });

        
    }
    
    function BuscartracksFiltradas() {
        let base_url = $("#base_url").val();
        $.ajax({
            url: base_url + 'api/ApiNotificaciones/buscarTracksFiltradas',
            type: 'GET',
    
        })
            .done(function (response) {
                //  console.log(response.data)
                $('#tp_agenteCentral').dataTable().fnDestroy();
                if (response.ok) {
                    $('#tp_agenteCentral').DataTable({
                        rowId: response.data.id_grupo_notificacion,
                        order: [[2, "asc"]],
                        data: response.data,
                        columns: [
                            { data: "id_grupo_notificacion" },
                            { data: "nombre_grupo" },
                            {
                                data: null,
                                render: function (data, type, row, meta) {
                                    central_str = (data['medios_notificacion']).toUpperCase();
                                    return central_str;
                                }
                            },
                            {
                                data: null,
                                
                                render: function (data, type, row, meta) {
                                    // console.log(data['estado_agente']);
                                    switch (data['metodo_notificacion']) {
                                        case "1":
                                            estado = "ENVIO";
                                            break;
                                        case "2":
                                            estado = "RECEPCION";
                                            break;
                                        case "1,2":
                                            estado = "ENVIO/RECEPCION";
                                            break;
                                        default:
                                            estado = data['metodo_notificacion'];
                                            break;
                                    }
                                    return estado;
                                }
                            },
                             
                            {
                                data: null,
                                render: function (data, type, row, meta) {
                                    // console.log(data['estado_agente']);
                                    switch (data['action']) {
                                        case "send":
                                            estado = "ENVIAR";
                                            break;
                                        case "block":
                                            estado = "BLOQUEAR";
                                            break;
                                        case "no_action":
                                            estado = "SIN ACCION";
                                            break;
                                        default:
                                            estado = data['action'];
                                            break;
                                    }
                                    return estado;
                                }
                            },
                            { data: "origen" },
                            {
                                data: null,
                                
                                render: function (data, type, row, meta) {
                                    // console.log(data['estado_agente']);
                                    switch (data['estatus']) {
                                        case "1":
                                            estado = "ACTIVO";
                                            break;
                                        case "0":
                                            estado = "INCATIVO";
                                            break;
                                        default:
                                            estado = data['estatus'];
                                            break;
                                    }
                                    return estado;
                                }
                            },
                            {
                                data: null,
                                render: function (data, type, row, meta) {
                                    var Palabras =
                                        '<a class="btn btn-xs btn-success" title="Palabras" id="Palabras'+
                                        row.id_grupo_notificacion+'" data-origen="'+
                                        row.origen+'" data-id_grupo="'+
                                        row.id_grupo_notificacion+'" data-nombre_grupo="'+ row.nombre_grupo+'" onclick="mostrarPalabras(' +
                                        row.id_grupo_notificacion + ');" ><i class="fa fa-address-book" ></i></a>';
                                    var EditarGrupo =
                                        '<a class="btn btn-xs btn-primary" title="EditarGrupo" onclick="editarGrupo(' +
                                        row.id_grupo_notificacion + ');"><i class="fa fa-pencil-square-o" ></i></a>';
                                    var HabilitarAgente =
                                        '<a class="btn btn-xs btn-success" id="btnHabilitarAgente" data-estado="1" data-id_consultor="' + row.id_grupo_notificacion + '" title="Habilitar Agente", onclick="altaAgente(' +
                                        row.id_grupo_notificacion + ');"><i class="fa fa-check-square-o" ></i></a>';
                                    var DeshabilitarAgente =
                                        '<a class="btn btn-xs btn-danger" id="btnDeshabilitarAgente" data-estado="0" data-id_consultor="' + row.id_grupo_notificacion + '" title="Deshabilitar Agente" onclick="bajaAgente(' +
                                        row.id_grupo_notificacion + ');"><i class="fa fa-ban" ></i></a>';
                                    if (data['estatus'] == 1) {
                                        return Palabras + " " +EditarGrupo + " " + DeshabilitarAgente;
    
                                    } else {
                                        return EditarGrupo + " " + HabilitarAgente;
                                    }
                                }
                            }
                        ],
                        columnDefs: [
                            {
                                targets: [7],
                                createdCell: function (td, cellData, rowData, row, col) {
                                    $(td).attr("style", "text-align: center; width: 10%;");
                                }
                            },]
                    });
    
                }
    
            })
            .fail(function (response) {
                console.log(response.error);
            })
            .always(function (response) {
                //console.log("complete");
            });
    
            
        }


    function mostrarPalabras(id)
    {   
        name_grupo = $("#Palabras"+id).attr("data-nombre_grupo")
        origen = $("#Palabras"+id).attr("data-origen")
        // console.log(id,name_grupo)
        $('#lbl_grupo').text(name_grupo);
        $('#hd_group').val(id);
        $('#txt_group').val(name_grupo);
        $('#lbl_origen').text(origen);
        $('#txt_origen').val(origen);
        

        $('#modalPalabras').modal('show');
        let base_url = $("#base_url").val();
        $.ajax({
            url: base_url + 'api/ApiNotificaciones/mostrarPalabras',
            data:{id:id,origen:origen},
            type: 'POST',
            
        })
        .done(function (response) {
    
    
    $('#modalPalabras').modal('show');
    // $('#table_words').text(response);
    // console.log(response);

   
            //alert(respuesta);
            var registros = eval(response);
            
            html ="<table id='palabras_table' class='table table-responsive table-bordered'><thead>";
            html +="<tr>";
            html +="<th>#</th><th> Palabra </th>";
            html +="<th></th>";
            html +="</tr>";
            html +="</thead><tbody>";
            
            for (var i = 0; i < registros.length; i++) {
                html +=" <tr>";
                html +=" <td>"+i+"</td>";
                html +=" <td>"+registros[i]['palabra']+"</td>";
                html +=' <td><a class="btn btn-xs btn-primary" id="btn_editar_palabra" data-estado="0" data-palabra="' + registros[i]['palabra'] + '"  ><i class="fa fa-pencil-square-o" ></i></a>';
                html +=' <a class="btn btn-xs btn-danger" id="btn_eliminar_palabra" data-estado="0" data-palabra="' + registros[i]['palabra'] + '"  ><i class="fa fa-trash" ></i></a></td>';
                html +=" </tr>";
                
            }
            
            html +="</tbody></table>";
            $('#table_words').html(html);
            let table = $('#palabras_table').dataTable( {
                            searching: true,
                            autoFill: true,
                            pageLength: 5,
                            bFilter: false,
                            bSort: true,
                            responsive: true,
                            colReorder: true,
                            select: true,
                            'language': {
                                'decimal': ',',
                                'thousands': '.',
                                'lengthMenu': 'Mostrando _MENU_ registros por pagina',
                                'zeroRecords': 'Nothing found - sorry',
                                'info': 'Mostrando pagina _PAGE_ de _PAGES_',
                                'infoEmpty': 'No records available',
                                'infoFiltered': '(filtered from _MAX_ total records)'
                            },
                            'pagingType': 'full_numbers'
                            
            });
           
            // alert(table)
            // $('#name_group').on( 'keyup', function () {
            //     table.search( this.value ).draw();
            // } );


    
})
.fail(function (response) {
    console.log(response.error);
})
.always(function (response) {
    //console.log("complete");
});


}




function solo_numeros(event) {
    let keycode = event.keyCode;
    if (keycode >= 48 && keycode <= 57) {
        return true;
    }
    return false;
}

function solo_palabras(event) {
    var key = event.keyCode || e.which,
      tecla = String.fromCharCode(key).toLowerCase(),
      letras = " áéíóúabcdefghijklmnñopqrstuvwxyz",
      especiales = [8, 37, 39, 46],
      tecla_especial = false;

    for (var i in especiales) {
      if (key == especiales[i]) {
        tecla_especial = true;
        break;
      }
    }

    if (letras.indexOf(tecla) == -1 && !tecla_especial) {
      return false;
    }
  }



