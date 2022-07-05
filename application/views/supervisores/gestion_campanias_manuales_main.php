<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>">
<!-- Esto es para que el header no superponga el buscador -->
<?php //echo $desembolso; die();?>
<div class="box-header with-border" class="col-lg-12"><div class="col-md-12">&nbsp;</div></div>
<!--  -->
	<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
		<div class="box-header with-border" class="col-lg-12">
			<div class="col-md-12">
		    	<?php $this->load->view('supervisores/section_form_campanias'); ?>
			</div>
		</div>
    	<br>        
        <br>
               
	    <div id="texto" class="col-lg-12" style="display: block; background: #EBEDEF;"></div>
	    <div id="texto_agenda" class="col-lg-1"></div>
	</div>

<script src="<?php echo base_url('assets/supervisores/gestionar.js?'.microtime());?>"></script>