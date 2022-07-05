<div class="box box-info">
    <div class="box-header with-border">
            <h6 class="box-title"><small><strong><?= $title?></strong></small>&nbsp;</h6>
    </div>
    <div class="box-body" >
        <form role="form" id ="imputacionRecaudoBancolombia" enctype="multipart/form-data;charset=utf-8" data-banco = <?= $banco?>>
          	<div class="form-group">
            	<label for="fileImputacionRecaudoBancolombia">Imputación Recaudo</label>
            	<input type="file" id="fileImputacionRecaudoBancolombia" required="true" name="fileImputacionRecaudoBancolombia">
            	<p class="help-block">Formatos permitidos: .txt</p>
  	    	</div>
  	    	<div class="form-group">
        		<button type="button" class="btn btn-success" id="btnConfirmar">Cargar Archivo</button>
            <div id="detalle-archivo-imputacion-recaudo" class="pull-right"></div>
  	    	</div>
        </form>
    </div>
    <?php if($banco == "bancolombia"):?>
      <?= $this->load->view('tesoreria/table_imputacion_recaudo_bancolombia', null, true); ?>
        <!-- script de imputacion automatica -->
        <script src="<?php echo base_url() ?>assets/tesoreria/imputacion_recaudo_bancolombia.js"></script>
    <?php endif;?>
</div>
