<style>
    table input{
        border-top: 0px !important;
        border-left: 0px !important;
        border-right: 0px !important;
        background: transparent !important;
    }
    #tbl_solicitud_devolucion_precarga_filter{
        margin-right: 0.2%;
    }
    #tbl_solicitud_devolucion_all_info {
        display: none;
    }
    .alinear-centro {
        margin-left: 50%;
    }
    #main > div.row > div:nth-child(3) {
        margin-left: 1.7%!important;
        margin-right: 1.8%!important;
    }
    #main > div.row > div:nth-child(4){
        margin-left: 1.7%!important;
        margin-right: 1.8%!important;
    }
    div.search
    {
        padding-left: 0px!important;
        padding-top: 1px;
        background-color: #ececec;
        width: 36px!important;
        height: 34px;
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;

    }
    #main > div.row > div:nth-child(3) > div.col-md-6
    {
        margin-top: 1%;
        padding-left: 0!important;
    }
    
    #tbl_solicitud_devolucion_all
    {
        font-size: 14px;
    }
    #tbl_solicitud_devolucion_cliente
    {
        font-size: 14px;
    }
    
    #buscar-cliente-documento
    {
        height: 100%;
    }

</style>
<div class="row">
    <div class="box-header with-border col-md-12">
        
        <div class="col-md-3 form-group" id="section_search_cliente">
                
            <label for="search-cliente">Buscar Cliente: </label>
            <input id="search-cliente" name="search-cliente" type="number" class="form-control" placeholder="Documento"> 
        </div>

        <button id="buscar-cliente" type="button" class="btn btn-info col-sm-1" title="Buscar" style="font-size: 12px;    margin-top: 25px;"><i class="fa fa-search"></i> Buscar</button>
        <button id="reset-cliente" type="button" class="btn btn-default col-sm-1" title="Limpiar" style="font-size: 12px;    margin-top: 25px;"><i class="fa fa- fa-remove"></i> Limpiar</button>
               
                
    </div>

    <div id="box_client" data-id_cliente="" class="box box-info client-results hide col-md-12">
       
        <br>
        <div id="tbl_solicitud_devolucion" style="padding-bottom:3em;">
            <table data-page-length='10' align="center" id="tbl_devolucion" class="table table-striped hover" width="100%">
                <thead>
                    <tr class="info">
                        <th style="width: 19%; padding: 0px; padding-left: 10px; text-align: center;"><h5><b><i class="fa fa-id-badge"></i> Cliente</b></h5></th>
                        <th style="width: 19%; padding: 0px; padding-left: 10px; text-align: center;"><h5><b><i class="fa fa-id-card"></i> Documento</b></h5></th>
                        <th style="width: 24%; padding: 0px; padding-left: 10px; text-align: center;"><h5><b><i class="fa fa-user"></i> Nombre Cliente</b></h5></th>
                        <th style="width: 19%; padding: 0px; padding-left: 10px; text-align: center;"><h5><b><i class="fa fa-upload"></i> Solicitar</b></h5></th>
                    </tr>
                </thead>
                <tbody class="text-center" id="tbl_body_devolucion">
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="">
        
        <h3 
        class="box-title col-md-6" 
        style="padding-left:0!important;"><small><strong>Solicitudes de Devolución</strong></small>&nbsp;</h3>
        <div class=" col-md-6">
        
            <div class="col-md-3 form-group" id="" style="margin-bottom: 0!important; margin-left: 34%;">
                <div class="input-group col-md-12">  
                    <input type="text" class="form-control col-md-12" id="buscarPorDNI" placeholder="Buscar por documento" aria-label="Buscar por documento" aria-describedby="basic-addon1" style="height: 32px;">
                </div>    
            </div>
            <div class="col-md-3 form-group" id="">
                <div class="input-group col-md-12">
                    <select class="form-control col-md-12" id="buscarEstado" style="height: 32px;">
                        <option value="" disabled selected>Buscar por estado</option>
                        <option value="0">Pendiente</option>
                        <option value="1">Procesado</option>
                        <option value="2">Procesando</option>
                        <option value="4">Rechazada</option>
                    </select>
                </div>
            </div> 
            <button 
            id="buscar-cliente-documento" 
            onclick="initTableSolicitudDevolucion();" 
            type="button" 
            class="btn btn-info" 
            title="Buscar"  
            style="font-size: 12px;width:40px;height: 32px; "><i class="fa fa-search"></i></button>
             
            <button 
                id="reset-buscar-cliente-documento" 
                type="button" 
                class="btn btn-default" 
                title="Ver todas" 
                onclick="tableInit();" 
                style="font-size: 12px;width:40px;height: 32px;"><i class="fa fa-remove" aria-hidden="true"></i></button>       
        </div>
        <table id="tbl_solicitud_devolucion_all" class="table table-striped table-bordered table-hover compact" style="display:none; " width="100%" >
            <thead style="" width="100%">
                <tr class="" style="background-color: #D8D5F9;" width="100%">
                    <th style="">Fecha</th>
                    <th style="">Hora</th>
                    <th style="">Solicitado Por</th>
                    <th style="">Documento</th>
                    <th style="">Nombre y Apellido</th>
                    <th style="">Monto Devolver</th>
                    <th style="">Fecha Proceso</th>
                    <th style="">Resultado</th>
                    <th style="">Monto Devuelto</th>
                    <th style="">Estado Solicitud</th>
                    <th style="">Acciones</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <table id="tbl_solicitud_devolucion_cliente" class="table table-bordered table-hover compact responsive" style="display:none;" width="100%">
        
            <thead style="" width="100%">
                <tr class="" style="background-color: #D8D5F9;" width="100%">
                    <th style="">Fecha</th>
                    <th style="">Hora</th>
                    <th style="">Solicitado Por</th>
                    <th style="">Documento</th>
                    <th style="">Nombre y Apellido</th>
                    <th style="">Monto Devolver</th>
                    <th style="">Fecha Proceso</th>
                    <th style="">Resultado</th>
                    <th style="">Monto Devuelto</th>
                    <th style="">Estado Solicitud</th>
                    <th style="">Acciones</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
    
    <div class="box-body">
        
        <h3 class="box-title"><small><strong>Precarga Solicitudes de Devolución</strong></small>&nbsp;</h3>
        
        <table id="tbl_solicitud_devolucion_precarga" class="table table-bordered table-hover compact responsive" >
            <thead>
                <tr class="" style="background-color: #D8D5F9;">
                    <th style="">Fecha</th>
                    <th style="">Hora</th>
                    <th style="">Solicitado Por</th>
                    <th style="">Documento</th>
                    <th style="">Nombre y Apellido</th>                    
                    <th style="">Comentario</th>
                    <th style="">Estado Solicitud</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="myModalDevolucion" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog" style="width: 90%;" role="document">
      <div class="modal-content">
        <div class="modal-header"><h4 class="modal-title">Solicitud de devolución</h4></div>
        <div class="modal-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-7" id='devolu-datos'>
                        
                        <input type="hidden" id="id_cliente">
                        <input type="hidden" id="id_devolucion">
                        <input type="hidden" id="precarga">
                        <div class="col-sm-12">
                            <p><b>Cliente</b></p>
                            <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                        </div>
                        <div class="col-sm-4">
                            <div class="col-sm-12 form-group">
                                <label for="documento">Documento</label>
                                <input type="text" class="form-control" id="documento" placeholder="Documento" readOnly>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label for="banco">Banco</label>
                                <input type="text" class="form-control" id="banco" placeholder="Banco" readOnly>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="col-sm-12 form-group">
                                <label for="nombres">Nombres y Apellidos</label>
                                <input type="text" class="form-control" id="nombres" placeholder="Nombres y Apellidos" readOnly>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="tipo">Tipo de cuenta</label>
                                <input type="text" class="form-control" id="tipo" placeholder="Tipo de cuenta" readOnly>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label for="cuenta">Cuenta</label>
                                <input type="text" class="form-control" id="cuenta" placeholder="Cuenta" readOnly>
                            </div>
                        </div>

                        <!-- Datos de Devolucion -->
                        <div class="col-sm-12">
                            <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                            <p><b>Devolver</b></p>
                            <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                        </div>

                        <div class="col-sm-4">
                            <div class="col-sm-12 form-group">
                                <label for="forma">Forma</label>
                                <select class="form-control" id="forma">
                                    <option value="PARCIAL">Parcial</option>
                                    <option value="TOTAL" selected>Total</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="col-sm-12 form-group">
                                <label for="monto">Monto a devolver $</label>
                                <input type="text" class="form-control text-red" style="font-weight: bold;" id="monto" value="0" placeholder="monto" readOnly>
                            </div>
                        </div>
                        <div class="col-sm-12 form-group">
                            <table class="table table-bordered compact" id="tabla-pagos" style="font-size: smaller;">
                                <thead class="bg-gray-active">
                                    <th>FECHA PAGO</th>
                                    <th>MONTO</th>
                                    <th>MEDIO</th>
                                    <th>REFERENCIA EXTERNA</th>
                                    <th>REFERENCIA INTERNA</th>
                                    <th>RESULTADO</th>
                                    <th></th>
                                </thead>
                                <tbody class="bg-gray">
                                    
                                </tbody>
                            </table>       
                        </div>

                        <!-- Datos de Comprobantes -->
                        <div class="col-sm-12">
                            <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                            <p><b>Comprobantes</b></p>
                            <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group cargaComprobante">
                                <label for="comprobante">Cargar comprobante</label>
                                <input type="file" id="comprobante">
                                <p class="help-block">Formatos permitidos jpg | png | jpeg | pdf </p>
                                <a class="btn btn-xs btn-warning" id="btn_loadcomprobante" onclick="subirComprobante()"> <i class="fa fa-arrow-right"></i> cargar comprobante </a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-bordered compact" id="tabla-comprobantes" style="font-size: smaller;">
                                <thead class="bg-gray-active">
                                    <th>COMPROBANTE</th>
                                    <th></th>
                                    <th></th>
                                </thead>
                                <tbody class="bg-gray">
                                    
                                </tbody>
                            </table>     
                        </div>

                        <div class="col-md-12" id="listdevoluciones">
                            
                            <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                            <p><b>Devoluciones Solicitadas</b></p>
                            <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                            <div>
                                <table class="table table-bordered compact" id="tabla-afterdev" style="font-size: smaller;">
                                    <thead class="bg-gray-active">
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th>Resultado</th>
                                        <th>Fecha Proceso</th>
                                    </thead>
                                    <tbody class="bg-gray">
                                    </tbody>
                                </table>       
                            </div>
                        </div>


                        <div class="procesado">
                            <!-- Proceso de devolucion -->
                            <div class="col-sm-12 bg-success" style="padding-top: 10px;">
                                <p><b>PROCESO DE DEVOLUCION</b></p>
                            </div>

                            <div class="col-sm-4">
                                <div class="col-sm-12 form-group">
                                    <label for="resultado">Resultado</label>
                                    <select class="form-control" id="resultado">
                                        <option value="DEVUELTO">Devuelto</option>
                                        <option value="NO DEVUELTO" >No Devuelto</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="col-sm-12 form-group">
                                    <label for="monto-devuelto">Monto devuelto $</label>
                                    <input type="text" class="form-control text-red" style="font-weight: bold;" id="monto-devuelto" value="0" placeholder="monto Devuelto" >
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-sm-12 form-group">
                                    <label for="comentario">Comentario</label>
                                    <input type="text" class="form-control" id="comentario"  placeholder="Comentario" >

                                </div>
                            </div>


                            <!-- Datos de Comprobantes Devolucion -->
                            <div class="col-sm-12">
                                <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                                <p><b>Comprobantes devolución</b></p>
                                <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                            </div>

                            <div class="col-sm-5">
                                <div class="form-group cargaComprobante">
                                    <label for="comprobante-devolucion">Cargar comprobante</label>
                                    <input type="file" id="comprobante-devolucion">
                                    <p class="help-block">Formatos permitidos jpg|png|jpeg|pdf</p>
                                    <a class="btn btn-xs btn-warning" onclick="subirComprobante()"> <i class="fa fa-arrow-right"></i> cargar comprobante </a>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <table class="table table-bordered" id="tabla-comprobantes-devolucion" style="font-size: smaller;">
                                    <thead class="bg-gray-active">
                                        <th>COMPROBANTE</th>
                                        <th></th>
                                        <th></th>
                                    </thead>
                                    <tbody class="bg-gray">
                                        
                                    </tbody>
                                </table>     
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5" id='devolu-images' style="text-align: center; padding-left: 0px;">
                        
                        <div id="whatsapp" class="col-md-12" style="padding: 0px">
                            <div id="box_whatsapp" class="box box-info __chats_list_container" style="height: 800px;">
                                <div class="box-header with-border" id="titulo">
                            
                                </div>
                                <div class="box-body" style="overflow-y: auto; height: 66%">
                                    <div class="tab-pane active" id="timeline" style="padding-top: 40%;">
                                        <div class="loader" id="loader-6">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style=" padding-bottom: 20px;">
            <div class="col-md-12">
                <button class="btn btn-info" id="enviar"><i class="fa fa-paper-plane"></i> Enviar </button>
                <button class="btn btn-warning" onclick="statusSolicitudDevolucion()" id="descartar"> Descartar </button>
                <button class="btn btn-danger" id="cancelar" >Cancelar</button>
            </div>
            
        </div>
    </div>
  </div>
</div>

<script>
    


$(document).ready(function() {
    tableInit()
    initTablePrecargaSolicitudDevolucion();
    
    $("#enviar").on('click', function(){
        enviarSolicitudDevolucion();
    });
    $("#cancelar").on('click', function(){
        
        // swal.fire({
        //     title: "¿Esta seguro?",
        //     text: "Al cancelar la solictud perdera la información cargada hasta el momento",
        //     type: "warning",
        //     showCancelButton: true,
        //     confirmButtonColor: "#3085d6",
        //     cancelButtonColor: "#dedede",
        //     confirmButtonText: "Continuar",
        //     cancelButtonText: "Cancelar"
        //     }).then(function (result) {
        //         if (result.value) {
                    $("#myModalDevolucion #tabla-comprobantes a.delete").each(function(){
                        eliminarComprbante($(this),$(this).data('name'));
                    });
                    $("#tabla-pagos").dataTable().fnDestroy();            
                    $("#tabla-afterdev").dataTable().fnDestroy();            
                    $('#myModalDevolucion #tabla-pagos tbody').html("");
                    $('#myModalDevolucion #tabla-afterdev tbody').html("");
                    $('#myModalDevolucion #tabla-comprobantes tbody').html("");
                    $('#myModalDevolucion #comprobante').val("");
                    $('#myModalDevolucion #whatsapp #box_whatsapp').css('height',' 800px')
                    $('#myModalDevolucion #whatsapp #box_whatsapp').html(
                        '<div class="box-body" style="overflow-y: auto; height: 66%">'+
                        '    <div class="tab-pane active" id="timeline" style="padding-top: 40%;">'+
                        '        <div class="loader" id="loader-6"><span></span>\n<span></span>\n<span></span>\n<span></span>\n</div>'+
                        '    </div>'+
                        '</div>'
                    );
                    $('#myModalDevolucion').modal('hide');
            //     }
            // });
    });

    $("#forma").on('change', function(){
        SelectformaChange()
    });        

    $("#monto").on({
        "focus": function(event) {
            $(event.target).select();
        },
        "keyup": function(event) {
            $(event.target).val(function(index, value) {
            return value.replace(/\D/g, "")
                .replace(/([0-9])([0-9]{2})$/, '$1,$2')
                .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
            });
        }
        
    });
});    


//
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
 
            return $.ajax( {
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
$.fn.dataTable.Api.register( 'clearPipeline()', function () {
    return this.iterator( '#tbl_solicitud_devolucion_all', function ( settings ) {
        settings.clearCache = true;
    } );
} );
 
 
function tableInit(){
  
    $('#tbl_solicitud_devolucion_all').css('display', 'table');
    $('#tbl_solicitud_devolucion_cliente').css('display', 'none');
    // $('#reset-buscar-cliente-documento').css('display', 'none');
    $('#tbl_solicitud_devolucion_cliente_wrapper').css('display', 'none');

    $('#tbl_solicitud_devolucion_all').dataTable().fnDestroy()
    $('#tbl_solicitud_devolucion_all').DataTable({
            "ajax":
                $.fn.dataTable.pipeline( {
                    "url": $("input#base_url").val()+"api/solicitud/getSolicitudesDevolucionPaginada?estado=0,1,2,4",
                "type" : "GET",
                "pages": 5,
                }),
            "lengthMenu": [
                [5, 10, 15, 25, 50],
                [5, 10, 15, 25, 50],
                
            ],
            "autoWidth": false,
            "language": {
                sProcessing: "Procesando...",
                sLengthMenu: "Mostrar _MENU_ registros",
                sZeroRecords: "No se encontraron resultados",
                sEmptyTable: "Ningún dato disponible en esta tabla",
                sInfo: "Del _START_ al _END_ de un total de _TOTAL_ reg.",
                sInfoEmpty: "0 registros",
                sInfoFiltered: "(filtrado de _MAX_ reg.)",
                sInfoPostFix: "",
                sSearch: "Buscar:",
                sUrl: "",
                sInfoThousands: ",",
                sLoadingRecords: "Cargando...",
                //sDom: "tlip",
                oPaginate: {
                    sNext: "Sig",
                    sPrevious: "Ant"
                }
            },
            
            'iDisplayLength': 5,
            'paging':true,
            'info':true,
            "searching": false, 
            "processing":true,
            "serverSide": true,
            "order": [ 0, "desc" ],
            'columns':[
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        return moment(data.fecha).format('DD/MM/YYYY');
                    }
                },
                {
                    "data": "hora",
                    "render": function(data, type, row, meta){
                        return data;
                    }
                },
                {
                    "data": "solicitado",
                    "render": function(data, type, row, meta){
                        return data;
                    }
                },
                {
                    "data": "documento",
                    "render": function(data, type, row, meta){
                        return data;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        return (data.nombres+' '+data.apellidos);
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        return formatNumber(data.monto_devolver);
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){ 
                        return (data.fecha_proceso != null)? moment(data.fecha_proceso).format('DD/MM/YYYY hh:mm:ss'):'';
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){ 
                        return (data.resultado != null)? data.resultado:'';
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        return (data.monto_devuelto != null) ? formatNumber(data.monto_devuelto) : '';
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        if(data.estado == 1){
                            return '<p class="text-success">PROCESADO</p>';
                        //}else if(data.estado == 0){
                            //return '<p class="text-info">PENDIENTE</p>';
                        }else if(data.estado == 2){
                            return '<p class="text-warning">PROCESANDO</p>';
                        }else if(data.estado == 4){
                            return '<p class="text-warning">DESCARTADA</p>';
                        } else if(data.estado == 0 || data.estado == undefined) {
                            //return '<p class="text-warning"></p>';
                            return '<p class="text-info">PENDIENTE</p>';
                        }             
                    }
                },
                {
                    "data" : null,
                    "render": function(data, type, row, meta ){
                        var buttonUp = "<div>"+ 
                                            "<button class='btn btn-xs btn-primary' type='button' id='"+data.id+"' onclick='cargarInfoDevolucion("+data.id_cliente+","+data.id+" )' title='Ver Solicitud'><i class='fa fa-eye'></i></button></div>";
                                            
                        return buttonUp; 
                    }
                }            
            ],
           
                
        });
    }

function initTableSolicitudDevolucion(){
    var documento = '';
    var estado = '';
    $('#tbl_solicitud_devolucion_all').css('display', 'none');
    $('#tbl_solicitud_devolucion_all_wrapper').css('display', 'none');
    $('#tbl_solicitud_devolucion_cliente').css('display', 'table');
    console.log($('#buscarPorDNI').val());
    documento = $('#buscarPorDNI').val() != '' ? $('#buscarPorDNI').val() : 'false';
    estado = $('#buscarEstado').val()!= '' ? $('#buscarEstado').val() : 'false';
    
    $('#tbl_solicitud_devolucion_cliente').dataTable().fnDestroy()
    $('#tbl_solicitud_devolucion_cliente').DataTable({
            "ajax":
                $.fn.dataTable.pipeline( {
                "url": $("input#base_url").val()+"api/solicitud/solicitudes_devolucion_paginada",
                "type" : "GET",
                "pages": 5,
                "data": {
                    documento: documento,
                    estado: estado,
                }
                }),
            "lengthMenu": [
                [5, 10, 15, 25, 50],
                [5, 10, 15, 25, 50],
                
            ],
            "autoWidth": false,
            "language": {
                sProcessing: "Procesando...",
                sLengthMenu: "Mostrar _MENU_ registros",
                sZeroRecords: "No se encontraron resultados",
                sEmptyTable: "Ningún dato disponible en esta tabla",
                sInfo: "Del _START_ al _END_ de un total de _TOTAL_ reg.",
                sInfoEmpty: "0 registros",
                sInfoFiltered: "(filtrado de _MAX_ reg.)",
                sInfoPostFix: "",
                sSearch: "Buscar:",
                sUrl: "",
                sInfoThousands: ",",
                sLoadingRecords: "Cargando...",
                //sDom: "tlip",
                oPaginate: {
                   
                    sNext: "Sig",
                    sPrevious: "Ant"
                }
            },
            
            'iDisplayLength': 5,
            'paging':true,
            'info':true,
            "searching": false, 
            "processing":true,
            "order": [[ 2, "desc" ]],
            'columns':[
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        return moment(data.fecha).format('DD/MM/YYYY')
                    }
                },
                {
                    "data": "hora",
                    "render": function(data, type, row, meta){
                        return data
                    }
                },
                {
                    "data": "solicitado",
                    "render": function(data, type, row, meta){
                        return data
                    }
                },
                {
                    "data": "documento",
                    "render": function(data, type, row, meta){
                        return data
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        return (data.nombres+' '+data.apellidos);
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        return formatNumber(data.monto_devolver);
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){ 
                        return (data.fecha_proceso != null)? moment(data.fecha_proceso).format('DD/MM/YYYY hh:mm:ss'):'';
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){ 
                        return (data.resultado != null) ? data.resultado :'';
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        return (data.monto_devuelto != null) ? formatNumber(data.monto_devuelto) : '';
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta){
                        if(data.estado == 1){
                            return '<p class="text-success">PROCESADO</p>';
                        //}else if(data.estado == 0){
                            //return '<p class="text-info">PENDIENTE</p>';
                        }else if(data.estado == 2){
                            return '<p class="text-warning">PROCESANDO</p>';
                        }else if(data.estado == 4){
                            return '<p class="text-warning">DESCARTADA</p>';
                        } else if(data.estado == 0 || data.estado == undefined) {
                            //return '<p class="text-warning"></p>';
                            return '<p class="text-info">PENDIENTE</p>';
                        }             
                    }
                },
                {
                    "data" : null,
                    "render": function(data, type, row, meta ){
                        var buttonUp = "<div>"+ 
                                        "<button class='btn btn-xs btn-primary' type='button' id='"+data.id+"' onclick='cargarInfoDevolucion("+data.id_cliente+","+data.id+" )' title='Ver Solicitud'><i class='fa fa-eye'></i></button></div>";
                                            
                        return buttonUp; 
                    }
                }            
            ],
           
                
        });
}
</script>
