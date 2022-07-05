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

    <div id="box_client" data-id_cliente="" class="box box-info client-results hide col-md-12" style="background: #efefef;">
        <!-- <div class="box-header row" id="titulo" style="background-color: #fffdfa;box-shadow: 0px 9px 10px -9px #888888;">
            <div class="col-md-3 text-center">
                <h4 id="id-cliente"><i class="fa fa-id-badge"></i>
                    <strong><label id="lbl_id_cliente" ></label></strong>
                </h4>
            </div>
            <div class="col-md-3 text-center">
                <h4 id="documento-cliente"><i class="fa fa-id-card"></i>
                    <strong><label id="lbl_documento_cliente"></label></strong>
                </h4>
            </div>
            <div class="col-md-3 text-center">
                <h4 id="nombre-cliente"><i class="fa fa-user"></i>
                    <strong><label id="lbl_nombre_cliente" ></label></strong>
                </h4>
            </div>
            <div class="col-md-3 text-center">
                <h4 id="deuda-cliente"><i class="fa fa-money"></i>
                    <strong>$<label id="lbl_deuda_cliente" class="text-danger"></label></strong>
                </h4>
            </div>
        </div> -->
        <!-- end box-header -->
        <br>
        <div id="tbl_solicitud_imputacion" style="padding-bottom:3em;">
            <table data-page-length='10' align="center" id="tbl_imputacion" class="table table-striped hover" width="100%">
                <thead>
                    <tr class="info">
                        <th style="width: 19%; padding: 0px; padding-left: 10px; text-align: center;"><h5><b><i class="fa fa-id-badge"></i> Cliente</b></h5></th>
                        <th style="width: 19%; padding: 0px; padding-left: 10px; text-align: center;"><h5><b><i class="fa fa-id-card"></i> Documento</b></h5></th>
                        <th style="width: 24%; padding: 0px; padding-left: 10px; text-align: center;"><h5><b><i class="fa fa-user"></i> Nombre Cliente</b></h5></th>
                        <th style="width: 19%; padding: 0px; padding-left: 10px; text-align: center;"><h5><b><i class="fa fa-money"></i> Deuda</b></h5></th>
                        <th style="width: 19%; padding: 0px; padding-left: 10px; text-align: center;"><h5><b><i class="fa fa-upload"></i> Solicitar</b></h5></th>
                    </tr>
                </thead>
                <tbody class="text-center" id="tbl_body_imputacion">
                </tbody>
            </table>
            <h3 class="box-title" ><small><strong>Solicitudes de imputación del cliente</strong></small></h3>
            <table id="tbl_solicitud_imputacion_aux" class="table table-bordered table-hover dataTable">
                <thead>
                    <tr class="" style="background-color: #D8D5F9;">
                        <th style="width: 100px">Fecha Solicitud</th>
                        <th style="">Solicitante</th>
                        <th style="">Fecha Imputación</th>
                        <th style="">Resultado</th>
                        <th style="">Documento</th>
                        <th style="">Nombre y Apellido</th>
                        <th style="">Fecha Pago</th>
                        <th style="">Monto Pago</th>
                        <th style="">Estado Solicitud</th>
                        <th style="width: 400px">Comentario</th>
                        <th style="">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div><!-- end box-info -->
    
    <div class="box-body">
        
        <h3 class="box-title"><small><strong>Solicitudes de Imputación Ultimos 10 Dias</strong></small>&nbsp;</h3>
        
        <table id="tbl_solicitud_imputacion_all" class="table table-bordered table-hover dataTable">
            <thead>
                <tr class="" style="background-color: #D8D5F9;">
                    <th style="width: 100px">Fecha Solicitud</th>
                    <th style="">Solicitante</th>
                    <th style="">Fecha Imputación</th>
                    <th style="">Resultado</th>
                    <th style="">Documento</th>
                    <th style="">Nombre y Apellido</th>
                    <th style="">Fecha Pago</th>
                    <th style="">Monto Pago</th>
                    <!-- <th style="">Crédito</th>
                    <th style="">Fecha Otorgamiento</th>
                    <th style="">Monto de Crédito</th>
                    <th style="">Estado Crédito</th> -->
                    <th style="">Estado Solicitud</th>
                    <th style="width: 400px">Comentario</th>
                    <th style="">Acciones</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>

    <div class="box-body ">

        <h3 class="box-title"><small><strong>Precargas de Imputación</strong></small>&nbsp;</h3>

        <table id="tbl_precarga_imputacion" class="table table-bordered table-hover dataTable">
            <thead>
                <tr class="" style="background-color: #59a3d8;">
                    <th style="">Precarga</th>
                    <th style="">Solicitante</th>
                    <th style="">Documento</th>
                    <th style="">Nombre y Apellido</th>
                    <th style="">Metodo de Pago</th>
                    <th style="">Comprobante</th>
                    <th style="">Acciones</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<!-- Modal Formulario Solicitar Imputacion -->
<div class="modal fade" id="modalSolicitudImputacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="width: 65%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="myModalLabel">Solicitud de imputación</h3>
            </div>
            <div class="modal-body">
            	<div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="referencia"><h5 id="nombre_cliente"></h5></label>
                        </div>
                        <input type="hidden" id="solicitudImputacion">
                        <input type="hidden" id="documento">
                        <input type="hidden" id="id_solicitud_imputacion">
                    </div>
            		<div class="row">
            			<div class="col-md-6">
                            <div class="col-md-12">
                                <label for="referencia">Referencia: </label>
                                <input type="text" class="form-control" id="referencia" autocomplete="off">
                            </div>
                            <div class="col-md-12">
                                <label for="fechaPago">Fecha del Pago: </label>
                                <input type="date" class="form-control" id="fechaPago" autocomplete="off">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="monto">Monto del Pago: </label>
                                <input type="number" 
                                    onClick="this.select();" 
                                    class="form-control" 
                                    id="monto"
                                    placeholder="0.0"
                                >
                            </div>
                            <div class="form-group col-md-12">
                                <label for="medios">Medio de Pago: </label>
                                <select name="medios" id="medios" class="form-control">
                                    <option value="">Seleccione...</option>
                                    <option value="Depósito">Depósito</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="banco-cliente">Banco desde donde realizó la transferencia/depósito: </label>
                                <select name="banco-cliente" id="banco-cliente" class="form-control">
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($bancosOrigen as $bancoOrigen) {?>
                                        <option value=<?= $bancoOrigen['id_Banco'] ?>><?= $bancoOrigen['Nombre_Banco'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="banco-solventa">Banco de solventa a donde transfirió/depositó: </label>
                                <select name="banco-solventa" id="banco-solventa" class="form-control">
                                <option value="">Seleccione...</option>
                                    <?php foreach ($bancosDestino as $bancoDestino) {?>
                                        <option value=<?= $bancoDestino['id_Banco'] ?>><?= $bancoDestino['Nombre_Banco'] . ' - ' . $bancoDestino['numero_cuenta'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-12 file-input">
                                <label for="file">Comprobante</label>
                                <input type="file" id="file" name="file" required="true" accept="image/*,.pdf">
                                <p class="help-block">Formatos permitidos: jpg | png | jpeg | pdf</p>
                                <p>Comprobante cargado: <a class="comprobanteActual" comprobante="" target="_blank"></a><p>
                            </div>
                            <div class="form-group col-md-12 resumen">
                                
                            </div>

                        </div>
            			<div class="col-md-6" id="viewcomprobanteActual" style="height:500px; text-align: center;"></div>
                        <input type="hidden" id="inp_id_cliente">
                        <input type="hidden" id="inp_id_solicitud">
                        <input type="hidden" id="id_solicitud">
                        <input type="hidden" id="medio_pago">
                        <input type="hidden" id="cliente">

            		</div>
                    <div class="row">
                        <table id="tbl_pagos_imputacion" class="table table-bordered table-hover dataTable stripe" width="99%">
                            <thead>
                                <tr class="bg-gray-active">
                                    <th>Fecha y hora</th>
                                    <th>Medio</th>
                                    <th>Referencia Externa</th>
                                    <th>Referencia interna</th>
                                    <th>Fecha Pago</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
            	</div>
            </div>
            <div class="modal-footer" style=" padding-bottom: 20px;">
                <div class="col-md-6">
                    <select name="comentario" id="comentario" class="form-control col-md-9" style="width: 70%; margin-right: 15px"></select>
                    <button class='btn btn-xs btn-danger form-control col-md-3' id="m_btnanular" title='Cancelar Solicitud' onClick='AnularSol()'><i class='fa fa-times'></i> Anular Solicitud</button>
                </div>
                <div class="col-md-6 ">
                    <a class="btn btn-info" id="aEnviar"><i class="fa fa-send"></i> ENVIAR</a> &nbsp;
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
                
            </div>
        </div>
    </div>
</div>
<!-- Modal Anular Imputacion -->
<div class="modal fade" id="modalAnularSolicitudImputacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 20%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">¿Anular solicitud de imputación?:</h4>
            </div>
            <div class="modal-body">
                <div>
                    <label>Fecha solicitud: <h5 id="fecha_solicitud" style="display: inline-block;"></h5></label>
                </div>
                <div>
                    <label>Referencia: <h5 id="referencia" style="display: inline-block;"></h5></label>
                </div>
                <div>
                    <label>Fecha pago: <h5 id="fecha_pago" style="display: inline-block;"></h5></label>
                </div>
                <div>
                    <label>Monto pago: <h5 id="monto_pago" style="display: inline-block;"></h5></label>
                </div>
                <div>
                    <label>Cliente: <h5 id="cliente" style="display: inline-block;"></h5></label>
                </div>
            </div>
            <input type="hidden" id="id_solicitud_imputacion">
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnAnular">Anular</button>
                <button type="button" class="btn btn-default" id="modalCancelarbtn" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Deuda < 0 -->
<div class="modal fade" id="modalDeuda" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 20%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">¡Cliente sin deuda!</h4>
            </div>
            <div class="modal-body">
                <div>
                    <p>¿Desea continuar?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnSi">Si</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#banco-cliente").attr("disabled", true);
        $("#banco-solventa").attr("disabled", true);
        initTableSolicitudImputar();
        initTablePrecargaImputar();
    });    
    /*** Coloca el monto en formato ##.###,## ***/
    // $("#monto").on({
    //     "focus": function(event) {
    //         $(event.target).select();
    //     },
    //     "keyup": function(event) {
    //         $(event.target).val(function(index, value) {
    //         return value.replace(/\D/g, "")
    //             .replace(/([0-9])([0-9]{2})$/, '$1,$2')
    //             .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ".");
    //         });
    //     }        
    // });
</script>
