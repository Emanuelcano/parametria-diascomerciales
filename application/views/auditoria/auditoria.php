<span class="hidden-xs">
	<?php
	
	$usuario     = $this->session->userdata("username");
	$tipoUsuario = $this->session->userdata("tipo");
	?>
</span>

<?php //echo base_url()."assets/template/dist/img/loading.gif"; ?>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">

<span class="hidden-xs">
	<?php
	/*if (empty($this->session->userdata("sesion"))) {
		redirect(base_url() . "auth/logout");
    }*/
    

	$usuario     = $this->session->userdata("username");
    $tipoUsuario = $this->session->userdata("tipo");
?>
</span>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario;?>">

<div id="dashboard_principal" style="display: block; background: #FFFFFF; margin-top: 7rem;">

    <div class="row">
    	<!-- Main content -->
      	<section class="content">

            <div class="col-lg-12" id="cuerpoGastos" style="display: block">
                <div class="form-group">
                    <label for="tipoDocumentoFiscal" class="col-sm-1 control-label">Tipo</label>
                      <div class="col-sm-1" id="divBeneficiarios">
                        
                            <select class="form-control select2" name="slc_tiporepo" id="slc_tiporepo"  >
                        
                              <option value="1">Ingreso</option>
                              <option value="2">Egreso</option>
                              
                        </select>
                      </div>
                      
                      <label for="denominacion" class="col-sm-1 control-label">Fecha desde</label>

                      <div class="col-sm-2">
                        <input  type="date" class="form-control" id="fecha_Desde" name="fecha_Desde" 
                                placeholder="Fecha desde "
                                autocomplete="off">
                      </div>
                      <label for="denominacion" class="col-sm-1 control-label">Fecha hasta</label>

                      <div class="col-sm-2">
                        <input  type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                                placeholder="Fecha de hasta"
                                autocomplete="off">
                      </div>
                    <button type="button" class="btn btn-success col-sm-1 pull-right" id="btnExportarLista" style="display: block;"><i class="fa fa-file-excel-o"></i> Exportar</button>
                    <button type="button" class="btn btn-primary col-sm-1 pull-right" id="btnBuscar" style="display: block;"><i class="fa fa-search"></i> Buscar</button>

                </div>


	
				<div >

						<div class="col-lg-12" id="main" style="display: block; background: #FFFFFF;">

						</div>

				</div>

			</div>

      	</section>
    	<!-- /.content -->
    </div>

</div>



<script src="<?php echo base_url('assets/js/jquery.js') ?>"></script>
<script src="<?php echo base_url('assets/js/Auditoria/auditoria.js') ?>"></script>
<script src="<?php echo base_url('assets/function.js'); ?>"></script>