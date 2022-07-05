<div class="box box-info">
    <div class="box-header with-border">
            <h6 class="box-title"><small><strong><?= $title?></strong></small>&nbsp;</h6>
    </div>
    <div class="box-body" >
        <form role="form" id ="imputacionAutomaticaBancolombia" enctype="multipart/form-data;charset=utf-8" data-banco = <?= $banco?>>

          <div class="form-group">
            	<label for="fileImputacionAutomaticaBancolombia">Imputación Automática</label>
            	<input type="file" id="fileImputacionAutomaticaBancolombia" required="true" name="fileImputacionAutomaticaBancolombia">
            	<p class="help-block">Formatos permitidos: .txt</p>
  	    	</div>
          
            <div class="form-group">
                <label for="rango_monto_imputacion" >Monto a Imputar</label>
                <select class="form-control" name="rango_monto_imputacion" id="rango_monto_imputacion" >
                    <option value="si">Mayor a 30000</option>
                    <option value="no">Menor a 30000</option>
                </select>
            </div> 

  	    	<div class="form-group">
        		<button type="button" class="btn btn-success" id="btnConfirmar">Cargar Archivo</button>
            <div id="detalle-archivo-imputacion-automatica" class="pull-right"></div>
  	    	</div>

        </form>
    </div>
    <?php if($banco == "bancolombia"):?>
      <?= $this->load->view('tesoreria/table_imputacion_automatica_bancolombia', null, true); ?>
        <!-- script de imputacion automatica -->
        <script src="<?php echo base_url() ?>assets/tesoreria/imputacion_automatica_bancolombia.js"></script>
    <?php endif;?>
</div>
