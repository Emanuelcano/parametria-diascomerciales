<div class="row" style="padding-top: 2em; padding-bottom: 1em;">
	<div class="form-group col-lg-4">
		<label for="input_tipo_operador_buscar" class="col-sm-3 control-label" style="margin-right: -2em;">Tipo operador:</label>
		<div class="col-sm-6">
			<select name="_tipo_operador_buscar" id="input_tipo_operador_buscar" class="form-control" required="required">
				<option value="0" selected>Seleccione una opccion</option>
			</select>
		</div>
		<button type="button" class="btn btn-info btn-sm" id="button_tipo_operador_buscar">Buscar</button>
	</div>
</div>
<hr>

<table data-page-length='10' id="tp_operadoresVentas" class="table table-striped hover" width="100%">
    
    <thead width="100%">
        <tr class="info" width="100%">
            <th>Habilitar</th>
            <th>Operador</th>
			<th>Tipo Operador</th>
            <th>Equipo</th>
            <th>Gestion obligatoria</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="col-lg-11 text-center" style="display: block; padding-bottom:5px;margin-bottom:4%;">
		<a class="btn btn-success" style="width:400px" title="Registrar configuración" onClick="procesarOperadores();" disabled> PROCESAR </a>
</div>

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
    
    inicializar_select_tipo_operador(); 
    initTableOperadporesVentas_vacio(); 

});
</script>
