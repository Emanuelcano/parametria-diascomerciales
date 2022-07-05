<style>
    table input{
        border-top: 0px !important;
        border-left: 0px !important;
        border-right: 0px !important;
        background: transparent !important;
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
        <div id="tbl_solicitud_devolucion_cliente" style="padding-bottom:3em;">
            <table data-page-length='10' align="center" id="tbl_devolucion" class="table table-striped hover" width="100%">
                <thead>
                    <tr class="" style="background-color: #D8D5F9;">
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
                <tbody class="text-center" id="tbl_body_devolucion_cliente">
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="box-body">
        <div id ="respuestaBanco" >
            <div class="form-group">
            	<label for="fileRespuestaDevolucion">Respuesta Devolución</label>
            	<input type="file" id="fileRespuestaDevolucion" required="true" name="fileRespuestaDevolucion">
            	<p class="help-block">Formatos permitidos: .xls</p>
  	    	</div>
  	    	<div class="form-group">
        		<button type="button" class="btn btn-success btn-xs" id="procesar-respuesta">Cargar Archivo</button>
  	    	</div>
        </div>

        <div class="col-sm-6">
            <h3 class="box-title"><small><strong>Solicitudes de Devolución</strong></small>&nbsp;</h3>
        </div>
        <div class="col-sm-6 text-right">
            <br>
            <a class="btn btn-sm btn-success" id="generar-archivo">Generar archivo</a>
            <a class="btn btn-sm btn-warning" data-estado = "0" id="estado-consulta">Procesando</a>
        </div>
        <table id="tbl_solicitud_devolucion_all" class="table table-bordered table-hover dataTable" >
            <thead>
                <tr class="" style="background-color: #D8D5F9;">
                    <th style="">Fecha</th>
                    <th style="">Hora</th>
                    <th style="">Solicitado Por</th>
                    <th style="">Documento</th>
                    <th style="">Nombre y Apellido</th>
                    <th style="">Banco</th>
                    <th style="">Cuenta</th>
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
</div>

<div class="modal fade bs-example-modal-lg" id="myModalDevolucion" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header"><h4 class="modal-title">Solicitud de devolución</h4></div>
        <div class="modal-body">
            <input type="hidden" id="id_cliente">
                <div class="row">
                    <!-- Dato del Cliente -->
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
                            <select class="form-control" id="forma" disabled>
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
                        <table class="table table-bordered" id="tabla-pagos" style="font-size: smaller;">
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
                        <p><b>Comprobantes Cliente</b></p>
                        <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                    </div>

                    <!-- <div class="col-sm-5">
                        <div class="form-group">
                            <label for="exampleInputFile">Cargar comprobante</label>
                            <input type="file" id="comprobante">
                            <p class="help-block">Formatos permitidos jpg|png|jpeg|pdf</p>
                            <a class="btn btn-xs btn-warning" onclick="subirComprobante()"> <i class="fa fa-arrow-right"></i> cargar comprobante </a>
                        </div>
                    </div> -->
                    <div class="col-sm-8 col-sm-offset-2">
                        <table class="table table-bordered" id="tabla-comprobantes" style="font-size: smaller;">
                            <thead class="bg-gray-active">
                                <th>COMPROBANTE</th>
                                <th></th>
                            </thead>
                            <tbody class="bg-gray">
                                
                            </tbody>
                        </table>     
                    </div>

                    <!-- Datos del solicitante -->
                    <div class="col-sm-12">
                        <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                        <p><b>Solicitado por: </b></p>
                        <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                    </div>

                    <div class="col-sm-12">
                        <div class="col-sm-6 form-group">
                            <label for="nombreApellido">Nombre y apellido del operador</label>
                            <input type="text" class="form-control" id="nombreApellido" placeholder="Nombre y Apellido" readOnly>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="fecha">fecha</label>
                            <input type="text" class="form-control" id="fecha" placeholder="Fecha" readOnly>
                        </div>
                    </div>

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
                        <div class="form-group">
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
                    

                    <!-- BOTONES -->
                    <div class="col-sm-12 text-right">
                        <hr style="border-top: 1px solid #eee; margin-top:10px; margin-bottom:10px;">
                            <button class="btn btn-danger accion" id="noProcesar"> No Procesar </button>
                            <button class="btn btn-info accion"   id="enviar"> Procesar </button>
                            <button class="btn btn-default" id="cancelar" > Cancear</button>
                    </div>

                </div>
        </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
        initTableSolicitudDevolucion();
        $("#noProcesar").prop("disabled", true);

        $("#buscar-cliente").click('on', function (){       
            buscarClienteDevolucion($("#search-cliente").val());            
        });

        $(".accion").on('click', function(){
            procesarSolicitudDevolucion();
        });

        $("#cancelar").on('click', function(){
           
            $("#myModalDevolucion #tabla-comprobantes-devolucion a.delete").each(function(){
                eliminarComprbante($(this),$(this).data('name'));
            });
            $("#tabla-pagos").dataTable().fnDestroy();            
            $('#myModalDevolucion #tabla-pagos tbody').html("");
            $('#myModalDevolucion #tabla-comprobantes-devolucion tbody').html("");
            $('#myModalDevolucion #tabla-comprobantes tbody').html("");
            $('#myModalDevolucion #comprobante-devolucion').val("");
            $('#myModalDevolucion').modal('hide');
                    
        });

        $("#resultado").on('change', function(){
           if($("#resultado").val() == 'DEVUELTO') {

                $("#noProcesar").prop("disabled", true);
                $("#enviar").prop("disabled", false);

           }else{
                $("#noProcesar").prop("disabled", false);
                $("#enviar").prop("disabled", true);
                $("#monto-devuelto").val(0);
           }
        });

        $("#monto-devuelto").on({
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

        $("#buscar-cliente").click('on', function (){       
            buscarClienteDevolucion($("#search-cliente").val());            
        });

        $("#generar-archivo").click('on', function (){ 
            if(auxTabla.data().count() > 0 ){
                generarArchivoDevolucion();
            } else {
                Swal.fire('','No existen registrs para generar el archivo','warning')
            }
        }); 
        
        $("#estado-consulta").click('on', function (){ 
            filtrarEstado();
        }); 

        $("#procesar-respuesta").click('on', function (){ 
            procesarRespuestaDevolucion();
        }); 
    });    

    function procesarRespuestaDevolucion(){
        if($("#fileRespuestaDevolucion").val() !== ""){

            Swal.fire({
                    title: 'Esta seguro?',
                    text: "Procesamiento del archivo de Respuesta Santander : "+ $("#fileRespuestaDevolucion").val().replace(/C:\\fakepath\\/i, ''),
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

                        let file = document.getElementById('fileRespuestaDevolucion');
                        let form = new FormData();
                
                        form.append("file", file.files[0], file.value);
                        form.append("fileName", $("#fileRespuestaDevolucion").val().replace(/C:\\fakepath\\/i, ''));
                        data = form;
                        let base_url = $("#base_url").val();
                        let settings = {
                            "url": base_url + 'api/solicitud/procesarRespuestaDevolucion',
                            "method": "POST",
                            "timeout": 0,
                            "processData": false,
                            "mimeType": "multipart/form-data",
                            "contentType": false,
                            "data": data,
                        };
                
                        $.ajax(settings).done(function (response) {
                            response = JSON.parse(response);
                            console.log(response);
                            if(response.status.ok){
                                Swal.fire('',response.message,'success');   
                            } else{
                                Swal.fire('',response.message,'error');   
                            }
                        
                        }).fail(function(xhr) {
                            Swal.fire("¡Atencion!", 
                                `readyState: ${xhr.readyState}
                                    status: ${xhr.status}
                                    responseText: ${xhr.responseText}`,
                                "error"
                            )
                        });
                    
                    }
                });
        }

    }
</script>
