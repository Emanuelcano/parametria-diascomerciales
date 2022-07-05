<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<?php //echo "<pre>"; print_r($data); echo "</pre>"; ?>
<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
    <div class="box box-info"  style="background: #E8DAEF;">
        <div class="box-header with-border" id="titulo">
                <h6 class="box-title"><small><strong>Autorización de Gastos</strong></small></h6>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12" id="cuerpoGastos" style="display: block">    
            <?= $this->load->view('administracion/tabla_administracion', null, true); ?>
        </div>
    </div>
</div>