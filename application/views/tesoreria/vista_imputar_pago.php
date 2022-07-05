<div class="box box-info" id="box-imputacion-pago">
    <div class="box-header with-border">
            <h6 class="box-title"><small><strong>Imputación de pagos manual</strong></small>&nbsp;</h6>
			
    </div>
    <div class="box-body">
      <div class="input-group input-group-sm col-md-2">
          <input type="text" class="form-control" id="inpuBuscarCreditos" placeholder="cedula / nombres / apellidos">
          <span class="input-group-btn">
            <button type="button" class="btn btn-info btn-flat" id="buscarCreditoCliente">Buscar</button>
          </span>
      </div>
      <br>
      <br>
      <?= $this->load->view('tesoreria/table_creditos_clientes', null, true);?>
      <br>
      <div class="box-header with-border">
        <h6 class="box-title"><small><strong>Solicitudes de Imputación</strong></small>&nbsp;</h6>
      </div>
      <?= $this->load->view('tesoreria/table_solicitud_imputacion', null, true);?>
      <form action="">
        
      </form>
    </div>
</div>
<?= $this->load->view('tesoreria/modals/modal_imputar_pago', null, true);?>

<!-- script de prestamos -->
<script src="<?php echo base_url() ?>assets/tesoreria/imputacion_pago.js"></script>