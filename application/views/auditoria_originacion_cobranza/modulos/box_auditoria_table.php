<style>
    #table_container {
        border-top: 2.5px solid #00c0ef;
        
    }
    #tp_misAuditorias_wrapper {
        padding-top: 1%;
    }
    .contenedor::-webkit-scrollbar {
        -webkit-appearance: none;
    }
    /* .contenedor {
        border-bottom: 1px solid #d2d6de;
    } */
    .contenedor::-webkit-scrollbar:vertical {
        width:4px;
    }
    .contenedor::-webkit-scrollbar-thumb {
        background-color: #797979;
        border-radius: 20px;
        border: 2px solid #888888;
    }

    .contenedor::-webkit-scrollbar-track {
        border-radius: 10px;  
    }
</style>
<div class="row col-lg-12" style="display:flex; padding-top:1%;">
    <div class="col-lg-2">
        <label for="">Documento</label>
        <div>
            <input type="text" name="" class="form-control" placeholder="Buscar por documento" id="search_documento" style="height: 27px;border-radius: 4px;border-color:#888888;border-width: 1px;" autocomplete="off">
        </div>
    </div>
    
    <div class="col-lg-2">
        <label for="">Telefono</label>
        <div>
            <input type="text" name="" placeholder="Buscar por teléfono" class="form-control" id="search_telefono" style="height: 27px;border-radius: 4px;border-color:#888888;border-width: 1px;" autocomplete="off">
        </div>
    </div>

    <div class="col-lg-2">
        <div style="padding-top:10%;">
            <input type="buttom" name="" class="btn btn-primary" id="btn_buscar" value="Buscar">
        </div>
    </div>
</div>
<div id="table_container">
    <table data-page-length='10' id="tp_misAuditorias" class="table table-striped table-bordered hover display" width="100%">
            <thead>
                <tr class="info" style="background-color: #D8D5F9;" width="100%">
                    <th style="text-align: center;background-color: #D8D5F9;"><i class="fa fa-eye" aria-hidden="true"></i></th>
                    <th style="text-align: center;background-color: #D8D5F9;">Fecha auditoria</th>                        
                    <th style="text-align: center;background-color: #D8D5F9;">Solicitud</th>
                    <th style="text-align: center;background-color: #D8D5F9;">Número Telefónico</th>
                    <th style="text-align: center;background-color: #D8D5F9;">Contacto</th>
                    <th style="text-align: center;background-color: #D8D5F9;">Documento</th>
                    <th style="text-align: center;background-color: #D8D5F9;">Operador Auditor</th>
                    <th style="text-align: center;background-color: #D8D5F9;">Observaciones</i></th>
                </tr>
            </thead>
            <tbody id="cont_body">
                
            </tbody>
    </table>
</div>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/supervisores/select2Bootstrap.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/supervisores.js'); ?>"></script>
<script>

    $(document).ready(function(){

        // Pipelining function for DataTables. To be used to the `ajax` option of DataTables
        //
        $.fn.dataTable.pipeline = function ( opts ) {
            // Configuration options
            var conf = $.extend( {
                pages: 5,     // number of pages to cache
                url: '',      // script url
                data: null,   // function or object with parameters to send to the server
                              // matching how `ajax.data` works in DataTables
                method: 'GET' // Ajax HTTP method
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
                            if (json.data) {
                                cacheLastJson = $.extend(true, {}, json);
                                
                                if ( cacheLower != drawStart ) {
                                    json.data.splice( 0, drawStart-cacheLower );
                                }
                                if ( requestLength >= -1 ) {
                                    json.data.splice( requestLength, json.data.length );
                                }
                                
                                drawCallback( json );
                            }
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
            return this.iterator( '#tp_misAuditorias', function ( settings ) {
                settings.clearCache = true;
            } );
        } );

        initTableMisAuditorias();
       
        
    });

$('#search_documento').keydown(function (e){
    $(function(){
        $('#search_documento').keypress(function(e) {
            if(isNaN(this.value + String.fromCharCode(e.charCode))) 
            return false;
        })
        .on(function(e){
            e.preventDefault();
        });          
    });
});    


$('#search_telefono').keydown(function (e){
    $(function(){
        $('#search_telefono').keypress(function(e) {
            if(isNaN(this.value + String.fromCharCode(e.charCode))) 
            return false;
        })
        .on(function(e){
            e.preventDefault();
        });          
    });
});


$("#btn_buscar").on("click", function () {

        let documento = $("#search_documento").val();
        let telefono = $("#search_telefono").val();

        $.fn.dataTable.pipeline = function ( opts ) {
            // Configuration options
            var conf = $.extend( {
                pages: 5,     // number of pages to cache
                url: $("#base_url").val() + 'api/ApiAuditoria/searchMisAuditorias',      // script url
                data: {"documento":documento, "telefono":telefono},   // function or object with parameters to send to the server
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
                        "type":     "POST",
                        "url":      $("#base_url").val() + 'api/ApiAuditoria/searchMisAuditorias',
                        "data":     {"documento":documento, "telefono":telefono},
                        "dataType": "json",
                        "cache":    false,
                        "success":  function ( json ) {
                            if (json.data) {
                                cacheLastJson = $.extend(true, {}, json);
                                
                                if ( cacheLower != drawStart ) {
                                    json.data.splice( 0, drawStart-cacheLower );
                                }
                                if ( requestLength >= -1 ) {
                                    json.data.splice( requestLength, json.data.length );
                                }
                                
                                drawCallback( json );
                            }
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
            return this.iterator( '#tp_misAuditorias', function ( settings ) {
                settings.clearCache = true;
            } );
        } );

        initTableMisAuditoriasSearch();
    
});

</script>