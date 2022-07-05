<div class="box box-info">

    <div class="box-header with-border">
            <h6 class="box-title"><small><strong><?= $title?></strong></small>&nbsp;</h6>
    </div>

    <div class="box-body" >
        <form role="form" id ="lectura" enctype="multipart/form-data;charset=utf-8" data-banco = <?= $banco?>>
          	<div class="form-group">
            	<label for="fileLectura">Cargar Archivos REC</label>
            	<input type="file" id="fileLectura" required="true" name="fileLectura">
            	<p class="help-block">Formatos permitidos: .txt</p>
  	    	</div>
  	    	<div class="form-group">
        		<button type="button" class="btn btn-success" id="btnConfirmar">Cargar Archivo</button>
            <div id="detalle-archivo-imputacion-automatica" class="pull-right"></div>
  	    	</div>
        </form>
    </div>

    <script src="<?php echo base_url() ?>assets/tesoreria/imputacion.js"></script>

</div>
