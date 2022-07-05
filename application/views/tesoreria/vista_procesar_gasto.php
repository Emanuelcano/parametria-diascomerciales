<script src="<?php echo base_url() ?>node_modules/moment/min/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>
<div class="box-header with-border">
<h6 class="box-title"><small><strong>Consultar Gastos</strong></small>&nbsp;</h6>
</div>
<div id="section_search_gasto">
    <div class="box-header with-border" style="padding-bottom: 3em;">
        <div class="col-md-12" style="padding:0px; margin:0px;">
            <form id="form_search_gasto" class="form-inline col-md-10" method="POST" style="padding-left: 0px;">
                <input id="search_gasto" name="search" type="text" class="form-control" placeholder="Id gasto/Factura/Benefeciario">
                <!-- <input type="text" id="date_range" style="width: 16%;" name="date_range" class="form-control" autocomplete="off"> -->
                <button type="submit" class="btn btn-info" title="Buscar Gasto" style="font-size: 12px;"><i class="fa fa-search"></i>Buscar</button>
                <button type="reset" class="btn btn-default" title="Limpiar GAsto" style="font-size: 12px;"><i class="fa fa- fa-remove"></i>Limpiar</button>
            </form>
        </div>
    </div>
</div>
<div id="result_gasto" style="display:none; padding-bottom:3em;">
<table data-page-length='10' id="table_search_gasto" class="table table-striped hover" width="100%">
    <thead>
      <tr class="info">
        <th style="display: none">ID</th>
        <th style="width: 8%; padding: 0px; padding-left: 10px;">Solicitado</th>
        <th style="width: 8%; padding: 0px; padding-left: 10px;">Nro Factura</th>
        <th style="width: 50%; padding: 0px; padding-left: 10px;">Beneficiario</th>
        <th style="width: 8%; padding: 0px; padding-left: 10px;">Vencimiento</th>
        <th style="width: 10%; padding: 0px; padding-left: 10px;">Monto Pagar</th>
        <th style="width: 8%; padding: 0px; padding-left: 10px;">Estado</th>
        <th style="width: 8%; padding: 0px;">&nbsp;</th>
      </tr>
    </thead>
    <tbody>

    </tbody>
</table>
</div>
<div class="box box-info">
    <div class="box-header with-border">
            <h6 class="box-title"><small><strong><?= $title; ?></strong></small>&nbsp;</h6>
    </div>
    <!-- <a class="btn btn-xs bg-success" style="font-size:1em;"title="Generar CSV"><b>Generar CSV</b><i class="fa fa-table"></i></a> -->
    <div class="box-body" >
        <div class="row">
             <div class="col-md-6">
                <select class="form-control" name="select_banco" id="select_banco">
                    <option selected="true" disabled="disabled" data-cuenta="">Seleccione Cuenta Bancaria</option>
                    <?php foreach ($cuentasBancarias as $cuentaBancaria): ?>
                        <option value="<?= $cuentaBancaria->id_banco?>" data-cuenta="<?= $cuentaBancaria->numero_cuenta?>" data-tipo="<?= $cuentaBancaria->codigo_TipoCuenta?>" data-estado="<?= $cuentaBancaria->nombre_estado?>"><?= $cuentaBancaria->Nombre_Banco?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6" id="info_banco" class="caja">
            </div>

        </div>
    </div>
</div>

<div>
<?= $this->load->view('tesoreria/table_procesar_gasto', null, true); ?>
        
</div>
<?= $this->load->view('tesoreria/modals/modal_confirm', null, true);?>
<?= $this->load->view('tesoreria/modals/modal_info', null, true);?>

<script src="<?php echo base_url() ?>assets/tesoreria/procesar_gastos.js"></script>
<script src="<?php echo base_url() ?>assets/operaciones/gastos.js"></script>
