<?php 
    if(isset($tesoreria))
    {
        echo '<input type="hidden" id="paginado-tabla" value="10">';
        echo '<input type="hidden" id="gestion-solicitud" value="">';
    } else {
        echo '<input type="hidden" id="paginado-tabla" value="5">';
    } 
?>
<div id="tabla_desembolso" style="display: block">
    <strong>SOLICITUDES CON ERROR DE DESEMBOLSO</strong>
            <br>
    <table  align="center" id="tp_desembolso" class="table table-striped table=hover display" width="100%">
        <thead style="font-size: smaller; ">
            <tr class="info">
                <th></th>
                <th style="text-align: center;">N°</th>
                <th style="text-align: center;">Fecha</th>
                <th style="text-align: center;">Hora</th>
                <th style="text-align: center;">Documento</th>
                <th style="text-align: center;">Solicitante</th>
                <th style="text-align: center;">Situción Laboral</th>
                <th style="text-align: center;">Paso</th>
                <th style="text-align: center;">Tipo</th>
                <th style="text-align: center;">Buro</th>
                <th style="text-align: center;">Cuenta</th>
                <!-- <th style="text-align: center;">Reto</th> -->
                <th style="text-align: center;">Estado</th>
                <th style="text-align: center;">Operador</th>
                <th style="text-align: center;">Última Gestion</th>
            </tr>
        </thead>
        <tbody style="font-size: 11px; text-align: center;">
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $('document').ready(function(){

    $("#tabla_desembolso a.solicitud").on('click', function(event){
       let id_solicitud = $(this).data("id_solicitud");
       consultar_solicitud(id_solicitud);
    });
        var base_url= $("#base_url").val();

// Pipelining function for DataTables. To be used to the `ajax` option of DataTables
//
$.fn.dataTable.pipeline = function ( opts ) {
    // Configuration options
    var conf = $.extend( {
        pages: 5,     // number of pages to cache
        url: '',      // script url
        data: null,   // function or object with parameters to send to the server
                      // matching how `ajax.data` works in DataTables
        method: 'POST' // Ajax HTTP method
    }, opts );
 
    // Private variables for storing the cache
    var cacheLower = -1;
    var cacheUpper = null;
    var cacheLastRequest = null;
    var cacheLastJson = null;
 
    return function ( request, drawCallback, settings ) {
        var ajax          = false;
        var requestStart  = request.start;
        var drawStart     = request.start;
        var requestLength = request.length;
        var requestEnd    = requestStart + requestLength;
         
        if ( settings.clearCache ) {
            // API requested that the cache be cleared
            ajax = true;
            settings.clearCache = false;
        }
        else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
            // outside cached data - need to make a request
            ajax = true;
        }
        else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                  JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                  JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
        ) {
            // properties changed (ordering, columns, searching)
            ajax = true;
        }
         
        // Store the request for checking next time around
        cacheLastRequest = $.extend( true, {}, request );
 
        if ( ajax ) {
            // Need data from the server
            if ( requestStart < cacheLower ) {
                requestStart = requestStart - (requestLength*(conf.pages-1));
 
                if ( requestStart < 0 ) {
                    requestStart = 0;
                }
            }
             
            cacheLower = requestStart;
            cacheUpper = requestStart + (requestLength * conf.pages);
 
            request.start = requestStart;
            request.length = requestLength*conf.pages;
 
            // Provide the same `data` options as DataTables.
            if ( typeof conf.data === 'function' ) {
                // As a function it is executed with the data object as an arg
                // for manipulation. If an object is returned, it is used as the
                // data object to submit
                var d = conf.data( request );
                if ( d ) {
                    $.extend( request, d );
                }
            }
            else if ( $.isPlainObject( conf.data ) ) {
                // As an object, the data given extends the default
                $.extend( request, conf.data );
            }
 
            settings.jqXHR = $.ajax( {
                "type":     conf.method,
                "url":      conf.url,
                "data":     request,
                "dataType": "json",
                "cache":    false,
                "success":  function ( json ) {
                    cacheLastJson = $.extend(true, {}, json);
 
                    if ( cacheLower != drawStart ) {
                        json.data.splice( 0, drawStart-cacheLower );
                    }
                    if ( requestLength >= -1 ) {
                        json.data.splice( requestLength, json.data.length );
                    }
                     
                    drawCallback( json );
                }
            } );
        }
        else {
            json = $.extend( true, {}, cacheLastJson );
            json.draw = request.draw; // Update the echo for each response
            json.data.splice( 0, requestStart-cacheLower );
            json.data.splice( requestLength, json.data.length );
 
            drawCallback(json);
        }
    }
};
 
// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
$.fn.dataTable.Api.register( 'processing()', function () {
    return this.iterator( 'table', function ( settings ) {
        settings.clearCache = true;
    } );
} );
// DataTables initialisation
    $('#tp_desembolso').DataTable( {
        /*"responsive":true,*/
        "processing":true,
        "language": spanish_lang,
        'iDisplayLength':$("#paginado-tabla").val(),
        'paging':true,
        'info':true,
        "searching": true,
        "serverSide": true,
        "ajax":
                $.fn.dataTable.pipeline( {"url": base_url+"api/solicitud/listar_desembolso",
                "type" : "POST",
                "pages": $("#paginado-tabla").val()} ),
        'columns':[
                    {"data":null, 
                        "render": function(data, type, row, meta)
                        {
                            if($("#paginado-tabla").val() == 10)
                            { 
                                return '';
                            } else {
                                return '<a href="#" class="btn btn-xs btn-primary solicitud" title="Consultar" id="icono" id_solicitud="'+data.id+'" onclick="consultar_solicitud('+data.id+')"><i class="fa fa-cogs"></i></a>';
                            }

                        },"orderable": false,
                    },
                    {"data":"id"},
                    {"data":"date_ultima_actividad"},
                    {"data":"hours_ultima_actividad"},
                    {"data":"documento"},
                    {"data":null,
                     "render":function(data, type, row, meta)
                     {
                        return data.nombres+' '+data.apellidos;
                     }
                     
                    },
                    {"data":"nombre_situacion",
                        // "render":function(data, type, row, meta){
                        //     if(data.nombre_situacion != null)
                        //     {
                        //         return data.nombre_situacion.toUpperCase().toUpperCase();

                        //     }else{
                        //         return '';
                        //     }
                        // }
                    },
                    {"data":"paso"},
                    {"data":"tipo_solicitud"},
                    {"data":null,
                            "render":function(data, type, row, meta)
                            {
                                if(data.respuesta_analisis != null)
                                {
                                    if(data.respuesta_analisis.toUpperCase()=="APROBADO")
                                    {
                                        return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                                    }else if(data.respuesta_analisis.toUpperCase()=="RECHAZADO")
                                    {
                                        return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                                    }else{
                                        return '';
                                    }
                                }else{
                                    return '';
                                }
                            },
                            "orderable": false
                    },
                    {"data":null,
                            "render":function(data, type, row, meta)
                            {
                                if(data.banco_resultado != null)
                                {
                                    if(data.banco_resultado.toUpperCase()=="ACEPTADA")
                                    {
                                        return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                                    }else if(data.banco_resultado.toUpperCase()=="RECHAZADA")
                                    {
                                        return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                                    }else{
                                        return '';
                                    }
                                }else{
                                    return '';
                                }
                            },
                            "orderable": false
                    },
                   /* {"data":null,
                        "render":function(data, type, row, meta)
                            {
                                if(data.resultado_ultimo_reto != null)
                                {
                                    if(data.resultado_ultimo_reto.toUpperCase()=="CORRECTA")
                                    {
                                        return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                                    }else if(data.resultado_ultimo_reto.toUpperCase()=="INCORRECTA")
                                    {
                                        return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                                    }else{
                                        return '';
                                    }
                                }else{
                                        return '';
                                }
                            },
                            "orderable": false
                    },*/
                    {"data":"estado"},
                    {"data":"operador_nombre_pila"},
                    {"data":"last_track","orderable": false,
                            },
            ],
        "columnDefs": [ 
                {
                        "targets": 0,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                        {
                            $(td).attr('style', 'width: 3%; text-align: center;'); 
                        }

                    },
                    {
                        "targets": 1,
                        "createdCell": function(td, cellData, rowData, row, col)
                        {
                            $(td).attr('style', 'width: 7%; text-align: center;'); 
                        }
                    },
                    {
                        "targets": [1,2,3,4],
                        "createdCell": function(td, cellData, rowData, row, col)
                        {
                            $(td).attr('style', 'width: 7%;'); 
                        }
                    },
                    {
                        "targets": 5,
                        "createdCell": function(td, cellData, rowData, row, col)
                        {
                            $(td).attr('style', 'text-align: left; width: 10%;'); 
                        }
                    },
                    {
                        "targets": [7,6,8,11,12,13],
                        "createdCell": function(td, cellData, rowData, row, col)
                        {     
                            $(td).attr('style', 'width: 7%;'); 
                        }
                    },
                    {
                        "targets": [9,10],
                        "createdCell": function(td, cellData, rowData, row, col)
                        {     
                            $(td).attr('style', 'width: 1%;'); 
                        }
                    }          
            ]
        });
    });
</script>
