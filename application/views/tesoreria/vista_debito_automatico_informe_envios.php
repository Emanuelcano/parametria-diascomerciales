<?php $fechaProceso = new DateTime(); ?>

<div class="box box-info">

    <div class="box-header with-border">
            <h6 class="box-title"><small><strong><?= $title?></strong></small>&nbsp;</h6>
    </div>

    <div class="box-body" >

        <form role="form" id ="lecturarcga" enctype="multipart/form-data;charset=utf-8" >

            <div class="form-group" id="fechaEnvio">
                <label for="fechaEnvio" >Fecha de Envio</label>
                <input type="date" name="fechaEnvio" class="form-control" id="fechaEnvio" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
            </div>

  	    	<div class="form-group">
        		<button type="button" class="btn btn-success" id="btnConfirmar">Mostrar Informe</button>
                <div id="detalle-archivo-imputacion-automatica" class="pull-right"></div>
  	    	</div>

        </form>

    </div>

    <?= $this->load->view('tesoreria/table_debito_automatico_bancolombia_informe_envios', null, true); ?>
    <!-- script de imputacion automatica -->
    <script src="<?php echo base_url() ?>assets/tesoreria/debito_automatico_informe_envios.js"></script>

</div>
