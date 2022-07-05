<style>
    #editConfig {
        /* margin-top: 6%; */
        width: 24px;
    }
    #configuraciones > div > div.col-lg-12.text-right
	{
		margin-left: 1.2%;
        padding-bottom: 0px!important;
        margin-top: 1%!important;
	}

    #main > div 
    {
        border-top-color: #00c0ef!important;
    }
	.ayudaForm {
        margin-bottom: 1em!important;
        font-size: 14px!important;
        font-weight: 400;
        color: #777!important;
        
    }
    div.ayuda {
        margin-top: 1%;
        color: #777!important;
     
    }

    .ayudaForm:last-child {
        margin-bottom: 0%!important;
    }

    #formDatosBasicos > div.box-body > div:nth-child(1) > div:nth-child(12) > label {
        text-align: left!important;

    }

    #formDatosBasicos > div.box-body > div:nth-child(1) > div:nth-child(6) > label {
        padding-top: 0!important;
        margin-bottom: 0;
        text-align: left!important;
    }
    .form-horizontal .control-label {
        text-align: left!important;
    }
    
    .card-guia-ayuda{
		padding: 0;
		margin-bottom: 1em;
		margin-top: 1em;
		background-color: #fbff001f!important;
        height: auto;
        border-radius: 0.35em;
    
    }
    #collapseOne > div 
    {
        padding: 15px;
        
    }
  
    #editConfiguracion {
        box-shadow: none!important;
    }
    

    .box-body {
        padding-top: 16px;
    }
    #headingOne > button {
        color: #777!important;
        text-decoration: none!important;
        background-color: transparent;
        font-weight: 600;
        font-size: 18px!important;
    }
    #headingOne > button:hover {
        font-weight: 700;
    }
</style>

<div class="col-lg-12 text-right" style="display: block; padding-bottom:5px;margin-top:2%;margin-bottom:1%;">
    <a class="btn btn-success" title="Registrar configuración" onClick="nuevaConfigurcion()"><i class="fa fa-user-plus"></i> Nueva configuración</a>
</div>
<table data-page-length='10' id="tp_configuraciones" class="table table-striped table-bordered hover" width="100%">
    <thead>
    <tr class="info"  width="100%">
        
			<th style="vertical-align: middle;">Usuario Administrador</th>
	        <th style="vertical-align: middle;">Estado de la campaña</th>
			<th style="vertical-align: middle;">Operadores en gestión</th>
	        <th style="vertical-align: middle;">Duración de campaña automática</th>
	        <th style="vertical-align: middle;">Días de búsqueda</th>
	        <th style="vertical-align: middle;">Tiempo de gestión</th>
	        <th style="vertical-align: middle;">Extensiones consecutivas</th>
	        <th style="vertical-align: middle;">Tiempo de extensión de solicitud</th>
	        <th style="vertical-align: middle;">Periodo actualización de solicitudes</th>
	        <th style="vertical-align: middle;">Periodo últimas gestionadas</th>
	        <th style="vertical-align: middle;">Tiempo de gestión de chats</th>
	        <th style="vertical-align: middle;">Periodo de actualización documentos</th>
	        <th style="vertical-align: middle;">Tiempo de preparación</th>
	        <th style="vertical-align: middle;">Tiempo estado alerta</th>
	        <th style="vertical-align: middle;">Tiempo estado preventivo</th>
	        <th style="vertical-align: middle;">Tiempo ventana de alerta</th>
	        <th style="vertical-align: middle;"><i class="fa fa-cog col-lg-12" aria-hidden="true" style="color:#777;text-align: center!important;font-size: 18px;" title="Acciones"></i></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<div class="col-sm-12 card-guia-ayuda">
                        <div id="accordion" class="alert ayuda">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <i class="fa fa-folder-open-o" aria-hidden="true" style="font-size:14px!important"></i> Gu&iacute;a de ayuda</i>
                                    </button> 

                                </div>
                
                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
							<!-- <div class="card-body" > -->
                                    <div class="card-body col-md-6">
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Estado de la campaña:</strong> Condición del estado de la campaña obligatoria.</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Operadores en gestión:</strong> Grupo de operadores a los que se aplica la gestión automática.</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Duración de campaña automática:</strong> Periodo de tiempo durante el cual se mostrarán las solicitudes obligatorias continuamente. (Min)</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Días de búsqueda:</strong> Días de antiguedad en el que se buscan las solicitudes para la gestión. (Días)</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de gestión:</strong> Tiempo máximo que se le asigna a un operador para gestionar un caso. (Min)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Extensiones consecutivas:</strong> Condición para permitirle al operador extender el tiempo de la solicitud, más de una vez.(Activa)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de extensión de solicitud:</strong> Tiempo establecido para cuando un operador solicita más tiempo en una solicitud.</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Periodo actualización de solicitudes:</strong> Periodo de tiempo en el que se evalúan las prioridades y actualizan las solicitudes. (Min)</p>
                                        
                                    </div>
                                    <div class="card-body col-md-6">
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Periodo últimas gestionadas:</strong> Periodo de tiempo en el que se vuelve a consultar una solicitud ya gestionada.</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de gestión de chats:</strong> Tiempo en que los operadores están desconectados de la campaña para gestionar los chats. (Min)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Periodo de actualización documentos:</strong> Periodo de tiempo en el que se evaluan los chats que disponen de documentos para priorizar. (Min)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de preparación:</strong> Tiempo previo a iniciar las solicitudes automáticas. (Seg)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo estado alerta:</strong> Tiempo establecido para que la barra superior de estado cambie a color amarillo. (Porcentaje)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo estado preventivo:</strong> Tiempo establecido para que la barra superior de estado cambie a color rojo. (Porcentaje)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo ventana de alerta:</strong> Tiempo que permanecerá la ventana de solicitud de extensión abierta (Seg)</p>
                                    </div>
                                    
                                <!-- </div> -->
                            </div>
                        </div>
                            
                    </div>
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
    // initTableConfiguraciones();
});
</script>
