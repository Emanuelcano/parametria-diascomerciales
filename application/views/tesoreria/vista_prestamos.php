<!-- <div class="box box-info">
    <div class="box-header with-border" id="titulo"  style="background: #E8DAEF;">
            <h6 class="box-title"><small><strong>Cuentas para pago de prestamos</strong></small>&nbsp;</h6>
    </div>
    <?php //echo $this->load->view('tesoreria/table_cuentas_bancarias', $tesoreria, true); ?>
</div> -->
<div class="box box-info">
    <div class="box-header with-border" id="titulo" style="background: #BB8FCE;">
        <h6 class="box-title"><small><strong>Prestamos por pagar</strong></small>&nbsp;</h6>
    </div>
    <?= $this->load->view('tesoreria/table_prestamos', null, true); ?>
</div>
<!-- 
<div class="box box-info">
    <div class="box-header with-border" id="titulo"  style="background: #e0584e;">
            <h6 class="box-title"><small style="color: #FFF;"><strong>Pagos Rechazados</strong></small>&nbsp;</h6>
    </div>
    <?php //$this->load->view('gestion/table_desembolso_ajax', $tesoreria, true); ?>
</div> -->

<?= $this->load->view('tesoreria/modals/modal_confirm', null, true);?>

<!-- script de prestamos -->
<script src="<?php echo base_url() ?>assets/tesoreria/prestamos.js"></script>