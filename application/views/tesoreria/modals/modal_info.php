<!-- Modal -->
<div id="modalInfo" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:700px !important">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
          <div id="modalAlert" class="hidden">
              <div class="alert alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <div id="alertMessage">
                </div>
              </div>
          </div>
          <p></p>
      </div>
      <div class="modal-footer">
        <button style="height: 2.7em;margin-left:5px;" class="btn btn-xs bg-navy btnSubirComprobante" title="Subir Comprobante"><span><i class="fa fa-upload"></i></span>  Subir Comprobante</button><input type="file" class="hidden">
        <button style="height: 2.7em;margin-left:5px;"class="btn btn-xs btn-success btnPagar" data-estado="5" title="Procesar Pago"><i class="fa fa-check-square-o"></i>  Pagar Gasto</button>
        <button type="button" class="btn btn-default" id="btnCerrarInfo">Cerrar</button>
      </div>
    </div>

  </div>
</div>