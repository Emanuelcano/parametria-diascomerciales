<!-- Modal -->
<div id="modalImputarPago" class="modal fade" role="dialog" data-id_credito = ""  data-id_cliente = ""  data-id_detalle_credito = "">
  <div class="modal-dialog" style="width: 60%;" role="document">
  <!-- <div class="modal-dialog"> -->
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Imputación de Pago</h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <!-- <div class="box-body"> -->
            <div class="row">
              <h5 id="nombre_cliente"></h5>
              <div id="modalAlert" class="hidden">
                  <div class="alert alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <div id="alertMessage">

                        </div>
                  </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">                              
                <form role="form" id ="formImputacionPago" enctype="multipart/form-data">
                    <input type="hidden" class="" id="id_credito" name="id_credito">
                    <input type="hidden" class="" id="id_cliente" name="id_cliente">
                    <input type="hidden" class="" id="id_detalle_credito" name="id_creditos_detalle">
                    <input type="hidden" class="" id="documento" name="documento">
                    <input type="hidden" class="" id="id_solicitud_imputacion" name="id_solicitud_imputacion">
                    <input type="hidden" class="" id="ruta_comprobante" name="ruta_comprobante">
                    <input type="hidden" class="" id="medio_pago" name="medio_pago">
                    <div class="form-group">
                      <label for="referencia" >Referencia</label>
                      <input type="text" name="referencia" required="true" class="form-control" id="referencia">
                    </div>
                    <div class="form-group">
                      <label for="fechaTransferencia" >Fecha Transferencia</label>
                      <input type="date" name="fecha_transferencia" required="true" class="form-control" id="fechaTransferencia">
                    </div>
                    <div class="form-group">
                      <label for="montoTransferencia">Monto transferencia</label>
                      <input type="number" name="monto_transferencia" required="true" class="form-control" id="montoTransferencia" placeholder="0.0">
                    </div>
                    <div class="form-group">
                      <label>Banco Origen Transferencia</label>
                      <select class="form-control" id="bankEntidades" required="true" name="id_banco_origen">
                        <option value="">Seleccione</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label>Cuenta Destino</label>
                      <select class="form-control" id="cuentaDestino" required="true" name="id_cuenta_destino">
                        <option value="">Seleccione</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="comprobante">Comprobante</label>
                      <input type="file" id="comprobanteImputacionCredito" required="true" name="comprobante" id="input_file">
                      <br>
                      <a id='a_comprobante' target="_blank"></a>
                    </div>
                    <div class="form-group" id="div_resultado">
                      <label>Resultado</label>
                      <select class="form-control" id="slt_resultado" required="true" name="resultado">
                        <option value="">Seleccione</option>
                        <option value="Imputado">Imputado</option>
                        <option value="Transferencia no encontrada">Transferencia no encontrada</option>
                        <option value="Depósito no encontrado">Depósito no encontrado</option>
                        <option value="Monto no coincide">Monto no coincide</option>
                        <option value="Comprobante ya procesado">Comprobante ya procesado</option>
                        <option value="Pago ya imputado">Pago ya imputado</option>
                        <option value="Imputación mal cargada">Imputación mal cargada</option>
                        <option value="Imputación sin comprobante">Imputación sin comprobante</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="comentario" >Comentario</label>
                      <input type="text" name="comentario" required="true" class="form-control" id="comentario">
                    </div>
                </form>
              </div>
                <div class="col-md-6" id="viewcomprobanteActual"  style="height:600px; text-align: center;"></div>
            </div>
              <!-- /.box-body -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="btnNoProcesar">No Procesar</button>
        <button type="button" class="btn btn-primary" id="btnProcesar">Procesar</button>
        <button type="button" class="btn btn-default" id="btnCancelar">Cancelar</button>
      </div>
    </div>

  </div>
</div>