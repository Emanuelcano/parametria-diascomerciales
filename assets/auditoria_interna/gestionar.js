var paginacion = 0;


function AuditarOperadorOnline(){
    let base_url = $("#base_url").val();
    var $btn = $('#btn');
    var $data = $('.data');
    var $loader = $('.loader');

    $.ajax({
          dataType: "JSON",
          data:$('#form_search').serialize(),
          url:   base_url+'api/ApiAuditoriaInterna/AuditarOperador',
          type: 'POST',
          beforeSend: function(request) {

              $("#compose-modal-wait").modal({
                  show: true,
                  backdrop: 'static',
                  keyboard: false
              });

              $loader.show();
          }
          }).done(function(respuesta){

              setTimeout(function(){
                  $loader.hide();
                  $("#compose-modal-wait").modal('hide');
              }, 1000);

              if (respuesta!="") {

                    html ="";
                    var registros = eval(respuesta);
                    
                    for (var i = 0; i < registros.length; i++) {

                        if(registros[i]['buro'].toUpperCase()=="APROBADO")
                        {
                              $boxBuro= '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                        }else if(registros[i]['buro'].toUpperCase()=="RECHAZADO")
                        {
                              $boxBuro= '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                        }else{
                              $boxBuro= '';
                        }

                        html +="<tr>";
                        html +="<td><a href='#' class='btn btn-xs btn-primary solicitud' title='Calificar' operacion='"+registros[i]['tipo_operacion']+"' monto='"+registros[i]['monto_aprobado']+"' documento='"+registros[i]['documento']+"' tipo='"+registros[i]['tipo']+"' fech_aprobado='"+registros[i]['fech_aprobado'] +"' id='btn_calificar' solicitante='"+registros[i]['solicitante']+"' fecha_alta='"+registros[i]['fech_alta']+"' id_cliente='"+registros[i]['id_cliente']+"' id_soli='"+ registros[i]['id_solicitud'] +"'  id_auditoria='"+registros[i]['id_auditoria']+"' ><i class='fa fa-cogs'></i></a></td>";
                        html +="<td>"+ registros[i]['id_solicitud'] +"</td>";
          //            html +="<td>"+ registros[i]['id_cliente'] +"</td>";
                        html +="<td>"+ registros[i]['fecha'] +"</td>";
                        html +="<td>"+ registros[i]['hora'] +"</td>";
                        html +="<td>"+ registros[i]['documento'] +"</td>";
                        html +="<td>"+ registros[i]['solicitante'] +"</td>";
                        html +="<td>"+ registros[i]['tipo'] +"</td>";
                        html +="<td>"+ $boxBuro +"</td>";
                        html +="<td>"+ registros[i]['estado'] +"</td>";
                        html +="<td>"+ registros[i]['tipo_operacion'] +"</td>";
                        html +="</tr>";
                    }

                    $('#table_search_auditoria').dataTable().fnDestroy();
                    $('#tb_result').html(html);
                    $('#table_search_auditoria').dataTable( {
                        "language": {
                                    "sProcessing":     "Procesando...",
                                    "sLengthMenu":     "Mostrar _MENU_ registros",
                                    "sZeroRecords":    "No se encontraron resultados",
                                    "sEmptyTable":     "Ningún dato disponible en esta tabla",
                                    "sInfo":           "Del _START_ al _END_ de un total de _TOTAL_ reg.",
                                    "sInfoEmpty":      "0 registros",
                                    "sInfoFiltered":   "(filtrado de _MAX_ reg.)",
                                    "sInfoPostFix":    "",
                                    "sSearch":         "Buscar:",
                                    "sUrl":            "",
                                    "sInfoThousands":  ",",
                                    "sLoadingRecords": "Cargando...",
                                    "oPaginate": {
                                            "sFirst":    "Primero",
                                            "sLast":     "Último",
                                            "sNext":     "Sig",
                                            "sPrevious": "Ant"
                                    },
                                    "oAria": {
                                        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                                    }
                        },
                        'pageLength': 10,
                        'order': [[ 0, "asc" ]],
                            "columnDefs": [ 
                                {
                                    "targets": 0,
                                    "orderable": false,
                                    "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 2%; text-align: center;'); 
                                    }

                                },
                                
                                {
                                    "targets": [1,2,3,4],
                                    "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'width: 10%; text-align: center;'); 
                                        
                                    }
                                },
                                {
                                    "targets": 5,
                                    "createdCell": function(td, cellData, rowData, row, col)
                                    {
                                        $(td).attr('style', 'text-align: center; width: 10%;'); 
                                    }
                                },
                                {
                                    "targets": [6,7,8,9],
                                    "createdCell": function(td, cellData, rowData, row, col)
                                    {     
                                        $(td).attr('style', 'width: 7%;'); 
                                    }
                                }
                                        
                            ],
                          });
              }else{
                  swal.fire("Atencion!","El Operador Seleccionado no esta activo en este momento en ninguna gestion!","warning");
              }

          }).fail(function(xhr,err){
              $loader.hide();
              Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
    });

    /*** Se obtiene el id del operador escogido  ***/
    let id_operador = $("#sl_operadores").val();
    /*** Se obtienen todas las auditorias hechas al operador escogido ***/
    getAuditoriasRealizadas(id_operador);
}

$('body').on('click', '#clouse_modal', function() {
    if(rowData) {
        $('#resumen-auditoria').dataTable().api().row.add(rowData).draw();
    };
    $('input[type=radio]').removeAttr( "checked" );
    $('#txt_hd_id_auditoria').val("");
    $('#txt_observaciones').val("");
    $('#rd_califica1').attr( "checked", true );
    rowData = '';
    $("#compose-modal-calificar").modal("hide");
});

$('body').on('click','#tb_result a[id="btn_calificar"]',function(event){
        let base_url = $("#base_url").val();
        let id_soli = $(this).attr('id_soli');
        let id_auditoria = $(this).attr('id_auditoria');
        let id_cliente = $(this).attr('id_cliente');
        let fecha_alta = $(this).attr('fecha_alta');
        let solicitante = $(this).attr('solicitante');
        let fech_aprobado = $(this).attr('fech_aprobado');
        let tipo = $(this).attr('tipo');
        let documento = $(this).attr('documento');
        let monto = $(this).attr('monto');
        let operacion = $(this).attr('operacion');
        $("#txt_hd_solicitud").val(id_soli);
        $("#txt_hd_track").val(id_auditoria);
        $("#txt_hd_operacion").val(operacion);

        $("#lbl_solicitante").text(solicitante);
        $("#lbl_fecha_alta").text(fecha_alta);
        (fech_aprobado != 'null') 
            ? $("#lbl_fecha_aprobado").text(fech_aprobado)
            : $("#lbl_fecha_aprobado").text("");
        $("#lbl_tipo_solicitud").text(tipo);
        $("#lbl_documento").text(documento);
        (monto != 'null') 
            ? $("#lbl_monto").text(formatNumber(monto))
            : $("#lbl_monto").text("");
        
        $("#compose-modal-calificar").modal("show");

        $.ajax({
         dataType: "JSON",
         data: {"id_cliente" : id_cliente,"operacion": operacion, "id_solicitud": id_soli},
         url:   base_url+'api/ApiAuditoriaInterna/BuscarNumerosCliente',
         type: 'POST',
         beforeSend: function(request) {
         }
        }).done(function(respuesta){
          html="";
          var registros = eval(respuesta);
          for (var i = 0; i < registros.length; i++) {
          
              html +="<option value='"+registros[i]['telefono']+"'>"+ registros[i]['fuente'] + " - " + registros[i]['telefono'] +"</td>";
          }

          $("#sl_tlfcliente").html(html);
          $("#sl_tlfcliente").select2();

        }).fail(function(xhr,err){
              
              Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
        });


        
});
/*****************************************************************/
/*** Se obtienen todas las auditorías realizadas a un operador ***/
/*****************************************************************/
function getAuditoriasRealizadas(id_operador) {
    /*** Se instancia el dataTable ***/
    $('#resumen-auditoria').dataTable().fnDestroy();
    $('#resumen-auditoria').DataTable({
        // rowId: data.id,
        order: [[ 1, "asc" ]],
        // data: tabla,
        columns: [
            { data: 'id' },
            { data: null,
                render: (data) => {
                    return moment(data.fecha_auditado).format('DD-MM-YYYY HH:mm:ss') 
                }
            },
            { data: 'id_solicitud' },
            { data: 'tlf_cliente' },
            { data: 'observaciones' },
            { data: 'gestion' },
            { data: 'proceso' },
            { data: null,
                render: (data) => {
                    return `<a class="btn btn-xs btn-success" 
                                title="Actualizar Auditoría"
                                onclick="cargarAuditoriaForm(${data.id}, ${data.tlf_cliente}, '${data.observaciones}', '${data.gestion}')">
                                <i class="fa fa-pencil-square-o"></i>
                            </a>`
                    
                }
            }
        ],
        language: {
            sProcessing:     'Procesando...',
            sLengthMenu:     'Mostrar _MENU_ registros',
            sZeroRecords:    'No se encontraron resultados',
            sEmptyTable:     'Ningún dato disponible en esta tabla',
            sInfo:           'Del _START_ al _END_ de un total de _TOTAL_ reg.',
            sInfoEmpty:      '0 registros',
            sInfoFiltered:   '(filtrado de _MAX_ reg.)',
            sInfoPostFix:    '',
            sSearch:         'Buscar:',
            sUrl:            '',
            sInfoThousands:  ',',
            sLoadingRecords: 'Cargando...',
            oPaginate: {
                    sFirst:    'Primero',
                    sLast:     'Último',
                    sNext:     'Sig',
                    sPrevious: 'Ant'
            },
            oAria: {
                sSortAscending:  ": Activar para ordenar la columna de manera ascendente",
                sSortDescending: ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
    $('#resumen-auditoria').DataTable().column( 0 ).visible( false );

    let base_url = $("#base_url").val();

    $.ajax({
        type: "GET",
        url: base_url + 'api/ApiAuditoriaInterna/getAuditoriasRealizadas/' + id_operador,
    })
    .done(function(respuesta) {
        if (respuesta.status.ok) {
            $('#resumen-auditoria').dataTable().api().rows.add(respuesta.auditorias).draw();
        }
    })
    .fail(function(xhr) {
        Swal.fire("Atencion!", 
            `readyState: ${xhr.readyState}
                status: ${xhr.status}
                responseText: ${xhr.responseText}`,
            "error"
        );
    });
}
/**********************************************************/
/*** Se carga la auditoría a modificar en el formulario ***/
/**********************************************************/
var rowData = '';
function cargarAuditoriaForm(id_auditoria, tlf, observacion, gestion) {
    if(rowData) {
        $('#resumen-auditoria').dataTable().api().row.add(rowData).draw();
    };

    $('#txt_hd_id_auditoria').val(id_auditoria);

    $('#txt_observaciones').val(observacion);

    $('input[type=radio]').removeAttr( "checked" );
    switch (gestion) {
        case "BUENA":
            $('#rd_califica1').prop( "checked", true );
        break;
        case "REVISAR":
            $('#rd_califica2').prop( "checked", true );
        break;
        case "MALA":
            $('#rd_califica3').prop( "checked", true );
        break;
        default: console.log('default:', gestion);
        break;
    }

    gestion2 = $('input:radio[name=rd_califica]:checked').val();
    
    $('select option').removeAttr( "selected" );
    $('#sl_tlfcliente option[value="' + tlf + '"]').prop("selected", true);

    /*** Se busca la fila a editar en el dataTable para removerla ***/
    var table = $('#resumen-auditoria').DataTable();
    var indexes = table.rows().eq( 0 ).filter( function (rowIdx) {
        return table.cell( rowIdx, 0 ).data() == id_auditoria ? true : false;
    });
    rowData = $('#resumen-auditoria').dataTable().api().row(indexes[0]).data();
    $('#resumen-auditoria').dataTable().api().row(indexes[0]).remove().draw();
}
