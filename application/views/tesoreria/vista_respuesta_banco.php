<div class="box box-info">
    <div class="box-header with-border">
            <h6 class="box-title"><small><strong><?= $title?></strong></small>&nbsp;</h6>
    </div>
    <div class="box-body" >
        <form role="form" id ="respuestaBanco" enctype="multipart/form-data" data-banco = <?= $banco?>>
          	<div class="form-group">
            	<label for="fileRespuestaBanco">Respuesta Banco</label>
            	<input type="file" id="fileRespuestaBanco" required="true" name="fileRespuestaBanco">
            	<p class="help-block">Formatos permitidos: .xls</p>
  	    	</div>
  	    	<div class="form-group">
        		<button type="button" class="btn btn-success" id="btnConfirmar">Cargar Archivo</button>
  	    	</div>
 	 	</form>
    </div>
    <?php if($banco == "bbva"):?>
      <?= $this->load->view('tesoreria/table_respuesta_banco', null, true); ?>
        <!-- script de respuesta banco -->
        <script src="<?php echo base_url() ?>assets/tesoreria/respuesta_banco.js"></script>
      <?php elseif($banco == "santander"): ?>
        <?= $this->load->view('tesoreria/table_respuesta_santander', null, true); ?>
        <!-- script de respuesta banco Santander -->
        <script src="<?php echo base_url() ?>assets/tesoreria/respuesta_banco_santander.js"></script>
      <?php elseif($banco == "bancolombia"): ?>
        <?= $this->load->view('tesoreria/table_respuesta_bancolombia', null, true); ?>
        <!-- script de respuesta banco Colombia -->
        <script src="<?php echo base_url() ?>assets/tesoreria/respuesta_banco_colombia.js"></script>
        <?php elseif($banco == "Bancobogota"): ?>
        <?= $this->load->view('tesoreria/table_respuesta_bancobogota', null, true); ?>
        
        <!-- script de respuesta banco Bogota -->
        <script src="<?php echo base_url() ?>assets/tesoreria/respuesta_banco_bogota.js"></script>
    <?php endif;?>
</div>
