<div id="tabla_solicitudes" style="display: block; margin-left: 1%; margin-right: 1%;">
    <table data-page-length='10' align="center" id="tp_atencionCliente" class="table table-striped table=hover display" width="100%">
        <thead class="info" style="font-size: smaller;margin-left: 1%; margin-right: 1%;">
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
        </thead>
        <tbody style="font-size: 11px; text-align: center;">
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $('document').ready(function(){

        $("#tabla_solicitudes a.solicitud").on('click', function(event){
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
        if(typeof(EXTENSION_TIME) =='undefined'){
            tableInit();
            getSolicitudesPendientes();
            let op = [1, 3, 4];
            if(op.includes($("#id_operador").data('tipo_operador'))){
                var tabla1 = setInterval(function(){ tableInit(); getSolicitudesPendientes();}, 60000);
            } else if($("#id_operador").data('tipo_operador') == 11){
                var tabla1 = setInterval(function(){ tableInit(); getSolicitudesPendientes();}, 300000);
            }
        } else{
            $("#tabla_solicitudes").hide();
        }
        

    });
    function listarSolicitudes(search) {
        $("#tabla_desembolso").hide();
        $("#section_search_solicitud #result").hide();
        $("#tabla_solicitudes").show();
        tableInit();
    }

    function tableInit(ocultar = false) {
        let linkA = document.querySelectorAll('#listarxregistroPorVisar');
        linkA.forEach(function (link) {
            
            link.classList.add('disabled');
        });
        ocultar ? $('#porVisarTotal').hide() : " ";
        if ($("table[id='CamposTabla']")) {
            
            $("table[id='CamposTabla']").remove();
            $('#tituloCasosVisar').hide();
        }
        if ($("p[id='mensajeSinCasos']")) {
            $("p[id='mensajeSinCasos']").remove();
        }
        if ($("table[id='totalsTable']")) {
            $("table[id='totalsTable']").remove();
        }
        $('#mostratTodasLasSolicitudes').addClass('disabled');

        $('table#tp_atencionCliente').dataTable().fnDestroy()
        $('#tp_atencionCliente').DataTable( {
            /*"responsive":true,*/
            "processing":true,
            "language": spanish_lang,
            'iDisplayLength': 10,
            'paging':true,
            'info':true,
            "searching": true, 
            "serverSide": true,
            "order": [[ 2, "desc" ]],
            "ajax":
                    $.fn.dataTable.pipeline( {"url": base_url+"api/solicitud/listar",
                    "type" : "POST",
                    "pages": 5,
                    "data": {'banco': $("#select-bancos").val()} 
                } ),
            'columns':[
                        {"data":null, 
                            "render": function(data, type, row, meta)
                            {
                                return '<a href="#" class="btn btn-xs btn-primary solicitud" title="Consultar" id="icono" id_solicitud="'+data.id+'" onclick="consultar_solicitud('+data.id+')"><i class="fa fa-cogs"></i></a>';
                            },"orderable": false,
                        },
                        {"data":"id"},
                        {"data":"date_ultima_actividad", },
                        {"data":"hours_ultima_actividad",},
                        {"data":"documento", },
                        {"data":null,
                            "render":function(data, type, row, meta)
                            {
                                return data.nombres+' '+data.apellidos;
                            }
                        },
                        {"data":"nombre_situacion",
                            /*"render":function(data, type, row, meta){
                                if(data.nombre_situacion != null)
                                {
                                    return data.nombre_situacion.toUpperCase().trim();
                                }else{
                                    return '';
                                }
                            },*/
                        },
                        {"data":"paso", },
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
                        /*{"data":null,
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
                        {"data":"estado", },
                        {"data":"operador_nombre_pila"},
                        {"data":"last_track", "orderable":false}
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
                                $('#mostratTodasLasSolicitudes').removeClass('disabled');
                                linkA.forEach(function (link) {
                                    link.classList.remove('disabled');
                                });
                                $(td).attr('style', 'width: 1%;'); 
                            }
                        }       
                ],
                
        });
    }

</script>
