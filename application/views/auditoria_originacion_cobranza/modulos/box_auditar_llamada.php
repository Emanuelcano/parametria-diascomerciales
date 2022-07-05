<style>
    #tp_auditarLlamadas_next {
        display:none;
    }

    #tp_auditarLlamadas_info {
        display:none;
    }

    .fondo
    {
        background-color: #D8D5F9!important;
        border: none!important;
        color: #777!important;
        padding: 0%!important;
        
    }

    audio::-webkit-media-controls-panel 
    {
        background-color: rgb(216, 213, 249);
    }

    audio::-webkit-media-controls-timeline 
    {
        background-color: #B1D4E0;
        border-radius: 25px;
        margin-left: 10px;
        margin-right: 10px;
    }

    .form-check-label-uno
    {
        font-weight: 400!important;
        width: 24%!important;
    }
    .form-check-input {
        width: 2%;
    }
    .form-check-label
    {
        width: 2%;
    }
    
    div.alert-danger
    {
        border-radius: 25px;
        height:30px;
        text-align: center;
        padding-top: 4px;
        margin-top: 6px;
    }
    .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover 
    {
        background-color: #e9e9e9!important;
        
    }

    .modal-content 
    {
        box-shadow: 0 2px 6px rgb(0 0 0 / 50%)!important;
    }

    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button 
    {
        -webkit-appearance: none;
        margin: 0;
    }

    .select2-container--default .select2-selection--single 
    {
        background-color: #fff;
        border: 1px solid #aaa;
        border-radius: 4px;
        padding: 2.4px!important;
    }

    .loader {
		border: 16px solid #f3f3f3; /* Light grey */
		border-top: 16px solid #3498db; /* Blue */
		border-radius: 50%;
		width: 100px;
		height: 100px;
		animation: spin 2s linear infinite;
		display: none;
		align-items: center;
		justify-content: center;
		margin: 0 auto;
	}
	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
</style>
<div style="border-top: 2.5px solid #00c0ef;" id="casosAAuditarTabla">
    <form action="" style="margin-bottom:2%;">
        <div class="" >
            <div class="" role="document">
                <div class="modal-content">
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-1">
                                <label>Desde:</label>
                                <div >
                                    <input type="date" id="date_range-desde" style="height: 27px;border-radius: 4px;border-color:#888888;border-width: 1px;" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-1" style="margin-lef:2%;">
                                <label>Hasta: </label>
                                <div>
                                    <input type="date" id="date_range-hasta" style="height: 27px;border-radius: 4px;border-color:#888888;border-width: 1px;" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-2" style="padding-left:3%;">
                                <label>Telefono:</label>
                                <div>
                                    <input class="col-md-12" placeholder="Buscar por teléfono"type="number" id="date_telefono" style="height: 27px;border-radius: 4px;border-color:#888888;border-width: 1px;" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-1" id="tipoEquipo">
                            <label>Equipo:</label>
                                <div style="margin-top:-1px;">
                                    <select class="col-md-12 form-control" name="Seleccionar operador" style="height: 28px!important;  border-radius:5px; border: 1px solid #868686; padding-top:3px;" id="equipoSelected">
                                        <option value="seleccione_equipo" selected>Seleccione equipo</option>
                                        <option value="COLOMBIA">COLOMBIA</option>
                                        <option value="ARGENTINA">ARGENTINA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2" id="tipoOperadores">
                                <label>Tipo Operador:</label>
                                <div style="margin-top:-1px;">
                                    <select class="col-md-12 form-control" name="Seleccionar operador" style="height: 28px!important; border-radius:5px; border: 1px solid #868686; padding-top:3px;" id="tipoOperadorSelected">
                                        <option value="" selected>Tipo Operador</option>
                                        <option value="1">CONSULTOR</option>
                                        <!-- <option value="4">ATENCION AL CLIENTE</option> -->
                                        <!-- <option value="5">COBRANZA DIGITAL</option> -->
                                        <option value="6">COBRANZA TELEFONICA</option>
                                    </select>
                                </div>
                            </div>               

                            <div class="col-md-2" id="operadoresPorEquipo">
                                <label>Operadores:</label>
                                <div>
                                    <select class="col-md-12 form-control select2-multiple" name="Seleccionar operador" style="height: 30px!important;" id="operadorSelected">
                                        <option value="">Seleccione operador</option>
                                        <?php
                                        foreach($lista_operadores as $operador){
                                            echo '<option value="'.$operador->idoperador.'">'.$operador->nombre_apellido.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>               

                            <div class="col-md-1">
                                <label>Central:</label>
                                <div>
                                    <select class="col-md-12" name="Seleccione central" style="height: 27px;border-radius: 4px;border-color:#888888;" id="centralSelected">
                                        <option value="0">Seleccione central</option>
                                        <option value="activo_twilio">Twilio</option>
                                        <option value="activo_neotell">Neotell</option>
                                    </select>
                                </div>
                                
                            </div>             

                            <div class="col-md-1">
                                <label></label>
                                <div>
                                    <button 
                                        type="button" 
                                        class="btn btn-primary col-md-12" 
                                        title="Buscar" 
                                        style="font-size: 12px;"
                                        onclick="initTableAuditarLlamadas(busqueda = true);"
                                        id="buscar_llamada"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>
                
                </div> 
            </div>
        </div>
    </form>

    <table data-page-length='10' id="tp_auditarLlamadas" class="table table-striped table-bordered hover display" width="100%">
        <thead>
            <tr class="info" style="background-color: #D8D5F9;" width="100%">
                <th style="text-align: center;background-color: #D8D5F9;"><i class="fa fa-cogs"></i></th>
                <th style="text-align: center;background-color: #D8D5F9;"><i class="fa fa-play-circle" aria-hidden="true"></i></th>

                <th style="text-align: center;background-color: #D8D5F9;">Fecha</th>                        
                <th style="text-align: center;background-color: #D8D5F9;">Solicitud</th>
                <th style="text-align: center;background-color: #D8D5F9;">Número Telefónico</th>
                <th style="text-align: center;background-color: #D8D5F9;">Contacto</th>
                <th style="text-align: center;background-color: #D8D5F9;">Documento</th>

                <th style="text-align: center;background-color: #D8D5F9;">Operador Asignado</th>

            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal fade" id="modalLoadingAuditoria" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fa fa-time"></i> ESPERE MIENTRAS SE MIGRAN LOS AUDIOS</h4>
					<div class="col-md-12 hide" id="succes">
						<!-- Primary box -->
						<div class="box box-solid box-primary">
							<div class="box-header">
								<h3 class="box-title">MIGRACIÓN DE AUDIOS</h3>
								<div class="box-tools pull-right">
									<button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
									<button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body">
								<span id="respuesta"></span>
							</div><!-- /.box-body -->
						</div><!-- /.box -->
					</div><!-- /.col -->
				</div>
			<div class="modal-body">
				<div class="data"></div>
				<div class="loader"></div> 
			</div>
				<div class="modal-footer clearfix">
				</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/supervisores/select2Bootstrap.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script>
    var selectedRow = null;
    var selectedTr = null;

    $(document).ready(function(){
        $('#operadorSelected').select2();
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
                            }else {
                               
                                Swal.fire({
                                    title: 'Llamadas a auditar.',
                                    text: 'No se encntraron llamadas para el criterio de búsqueda seleccionado.',
                                    type: 'error',
                                    confirmButtonText: 'OK'
                                }).
                                then((result) => {
                                    let btn = document.getElementById('buscar_llamada');
                                    btn.disabled = false;
                                    $('#tp_auditarLlamadas_processing').css('display','none');
                                });
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
            return this.iterator( '#tp_auditarLlamadas', function ( settings ) {
                settings.clearCache = true;
            } );
        } );

        initTableAuditarLlamadas();
       
        
    });

    

    $('#equipoSelected').on('change', function(){
        
        let selected = $(this).val();
        
        $('#operadorSelected').remove();
        $.ajax({
            url: $("input#base_url").val() + 'operadores_select/' + selected,
            type: 'GET',
            })
            .done(function (response) {
                let select = `<select class="col-md-12 form-control select2-multiple" name="Seleccione operador" style="height: 36px!important;" id="operadorSelected">Seleccione un operador</option>`;
                
                select += `<option value="seleccione_operador">Seleccione operador</option>`;
                response.forEach(element =>{
                    select += `<option value="${element.idoperador}">${element.nombre_apellido}</option>`;
                });
                select += `</select>`;
                $('#operadoresPorEquipo').append(select);
                $('#operadorSelected').select2();
            })
    })

    $("#tipoOperadorSelected").on("change", function() {
        if($("#tipoOperadorSelected").val() == ""){
            var tipo_operador = "(1,4,5,6)";
            // var tipo_operador = "6";
        }else{
            var tipo_operador = $("#tipoOperadorSelected").val();
        }
        let equipo = $("#equipoSelected").val();
        $('#operadorSelected').remove();
        $.ajax({
            url: $("input#base_url").val() +'api/ApiAuditoria/tipo_operador',
            type: 'post',
            data: {"tipo_operador":tipo_operador, "equipo":equipo},
            })
            .done(function (response) {
                let select = `<select class="col-md-12 form-control select2-multiple" name="Seleccione operador" style="height: 36px!important;" id="operadorSelected">Seleccione un operador</option>`;
                
                select += `<option value="seleccione_operador">Seleccione operador</option>`;
                response.forEach(element =>{
                    select += `<option value="${element.idoperador}">${element.nombre_apellido}</option>`;
                });
                select += `</select>`;
                $('#operadoresPorEquipo').append(select);
                $('#operadorSelected').select2();
            })
    });
</script>


