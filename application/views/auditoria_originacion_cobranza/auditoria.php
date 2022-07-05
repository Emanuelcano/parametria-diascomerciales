<style>
    .result-select {
        margin-left: 1%;
        margin-bottom: 0%;
        margin-right: 1%;
    }
    .result-cometario {
        margin: 1%;
    }
    .guardar-btn {
        margin-left: 1%;
        margin-bottom: 0%;
        margin-top: 1%;
        margin-right: 1%;
        border: none;
    }
    .form-check-label {
        font-weight: 300!important;
    }
    .tab-pane{
        margin-top: 1%;
    }
</style>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">

<div id="" style="display: block; background: #FFFFFF;margin-top=3%">
    <div class="col-md-12" align="center" id="" style="display: block; height: 100%;margin-top: 3%;padding-left:0!important">
        <div class="box-header" class="col-lg-12" style="padding:0!important">
            <div class="col-lg-12" id="" style="display: block; margin-top:1%">

                <!-- <div class="box-body pull-left" style="padding:0!important;">
                    <a 
                    class="btn btn-app " 
                    style="" 
                    onclick="auditoriaOriginacion();"
                    >
                        <i class="fa fa-users col-md-12"></i> 
                        <p class="col-md-12">Auditoría Originación</p>
                    </a>
                </div> -->

                <div class="box-body pull-left" style="padding:0!important;">
                    <a 
                    class="btn btn-app " 
                    id="llamadas_auditoria"
                    style="" 
                    onclick="cargarTable();"
                    >
                        <i class="fa fa-money col-md-12"></i> 
                        <p class="col-md-12">Auditoria llamadas</p>
                    </a>
                </div>

                <div class="box-body pull-left" style="padding:0!important;">
                    <a 
                    class="btn btn-app " 
                    style="" 
                    onclick="misAuditorias();"
                    >
                        <i class="fa  fa-calendar col-md-12"></i> 
                        <p class="col-md-12">Mis Auditorías</p>
                    </a>
                </div>
                
                

            </div>
        </div>
    </div>
</div>

<div id="dashboard_principal">
    <section class="content">

        <div class="col-lg-12" id="main" style="display: block;padding:0!important">
            <!-- Acá se insertan los modulos en la vista -->
        </div>

    </section>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/auditoria_originacion_cobranza/auditoria.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script>
    $("#llamadas_auditoria").trigger('click');
</script>