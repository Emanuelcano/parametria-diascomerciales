<div class="box box-info">

    <div class="box-header with-border">
            <h6 class="box-title"><small><strong><?= $title?></strong></small>&nbsp;</h6>
    </div>

    <div class="box-body" >

        <form role="form" id ="lecturarcga" enctype="multipart/form-data;charset=utf-8" data-banco = <?= $banco?>>

          	<div class="form-group">
            	<label for="fileLecturarcga">Cargar Rspuesta RCGA</label>
            	<input type="file" id="fileLecturarcga" required="true" name="fileLecturarcga">
            	<p class="help-block">Formatos permitidos: .txt</p>
  	    	</div>
          
  	    	<div class="form-group">
        		<button type="button" class="btn btn-success" id="btnConfirmar">Cargar Archivo</button>
            <div id="detalle-archivo-imputacion-automatica" class="pull-right"></div>
  	    	</div>

        </form>

    </div>

    <!-- script de imputacion automatica -->
    <script src="<?php echo base_url() ?>assets/tesoreria/debito_automatico_respuesta_envios.js"></script>
  
</div>
