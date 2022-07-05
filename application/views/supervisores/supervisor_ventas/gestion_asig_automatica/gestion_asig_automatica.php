<!-- <style>
  div.fondo{
    background-color: #e8daef!important;
    border: none!important;
    color: #777!important;
    
  }
  li.active > a{
    background-color: #e8daef!important;
  }
  ul {
    margin-bottom: 10%;
  }
  
</style> -->
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>">
<div class="contenedor">
	<div class="row">
	<h6><strong>REGLAS ASIGNACIÓN AUTOMÁTICA</strong></h3>
	<hr>
	<table data-page-length='10' id="dt_reglas_asig_automatica" class="table table-striped table-bordered hover" width="100%">
    <thead>
    <tr class="info"  width="100%">
			<th style="vertical-align: middle;">ID</th>
	    <th style="vertical-align: middle;">SITUACION LABORAL</th>
			<th style="vertical-align: middle;">ANTIGUEDAD LABORAL</th>
			<th style="vertical-align: middle;">PREDICCION</th>
			<th style="vertical-align: middle;">ESTADO</th>
	    <th style="vertical-align: middle;"><i class="fa fa-cog col-lg-12" aria-hidden="true" style="color:#777;text-align: center!important;font-size: 18px;" title="Acciones"></i></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
	
  </div>
  
	<div class="row">
	<h6><strong>TRACK DE CAMBIOS</strong></h3>
  <hr>
	<table data-page-length='10' id="dt_track_asig_automatica" class="table table-striped table-bordered hover" width="100%">
    <thead>
    <tr class="info"  width="100%">
			<th style="vertical-align: middle;">Id</th>
			<th style="vertical-align: middle;">Fecha Hora</th>
			<th style="vertical-align: middle;">Operador</th>
	    <th style="vertical-align: middle;">Situacion Laboral</th>
			<th style="vertical-align: middle;">Antiguedad</th>
			<th style="vertical-align: middle;">Prediccion</th>
			<th style="vertical-align: middle;">Estado</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
  </div>
 
 
 
 
</div>
<script src="<?php echo base_url('assets/js/sweetalert2.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/ventas/ventas.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/supervisor_ventas/gestion_asig_automatica/gestion_asig_automatica.js'); ?>" defer></script>





