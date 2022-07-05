<link rel="stylesheet" href="<?php echo base_url('assets/css/dualSelectList.css');?>"/>
<link rel="stylesheet" href="../assets/css/custom-gestion.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/supervisores/select2Bootstrap.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/dualSelectListSkill.jquery.js');?>" ></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<link rel="stylesheet" href="<?php echo base_url('assets/css/custom-gestion.css');?>">
<script type="text/javascript" src="<?php echo base_url('assets/jquery-validate/jquery.validate.min.js');?>" ></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/tablero_originacion/tablero_originacion.js');?>" ></script>
<style type="text/css">
    #botones {
        padding-top:3%;
    }

	.box-body{
		padding-right:0px;
		padding-left:5px;
	}

	.loading {
    border: 16px solid #f3f3f3; /* Light grey */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    display: none;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}

    #slc_semana{
        width: 15%;
        left: 34%;
    }
</style>
<div>
	<div id="botones" style="display: block; background: #FFFFFF;">
		<div class="box-header with-border" class="col-lg-12">
			<div class="col-lg-6" style="display: block">
				<div class="box-body pull-left">
					<input type="button" id="btn_diario" onclick="cambioSeccion('btn_diario', 'tablero_hoy')" class="button btn btn-info active" value="DIARIO"></input>
				</div>
				<div class="box-body pull-left">
					<input type="button" id="btn_semanal" onclick="cambioSeccion('btn_semanal', 'tablero_semanal')" class="button btn btn-info" value="SEMANAL"></input>
				</div>
				<div class="box-body pull-left">
					<input type="button" id="btn_quincenal" onclick="cambioSeccion('btn_quincenal', 'tablero_quincenal')" class="button btn btn-info" value="QUINCENAL"></input>
				</div>
				<div class="box-body pull-left">
					<input type="button" id="btn_mensual" onclick="cambioSeccion('btn_mensual', 'tablero_mensual')" class="button btn btn-info" value="MENSUAL"></input>
				</div>
			</div>
            <div>
                <div id="slc_semana" style="display: none" class="col-lg-6 form-group">
                    <label>Comienzo de semana</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <select class="form-control" name="sl_diaCorte" id="sl_diaCorte">
                                    
                            </select> 
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>
<div>
    <section class="content">
        <div class="col-lg-12" id="contenido" style="display: block">
			
        </div>
    </section>
</div>
<div class="modal fade" id="compose-modal-wait-loading" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS CARGA EL TABLERO </h4>
                <div class="col-md-12 hide" id="succes">
                    <!-- Primary box -->
                    <div class="box box-solid box-primary">
                        <div class="box-header">
                            <h3 class="box-title">BUSQUEDA DE PLANTILLAS</h3>
                            <div class="box-tools pull-right">
                                <button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <span id="respuesta"></span>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.col -->
            </div>
        <div class="modal-body">
            <div class="data"></div>
            <div class="loading"></div> 
        </div>
            <div class="modal-footer clearfix">
            </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->




	