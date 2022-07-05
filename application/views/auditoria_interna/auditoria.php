<span class="hidden-xs">
    <?php
    $usuario = $this->session->userdata("username");
    $tipoUsuario = $this->session->userdata("tipo");
    ?>
</span>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario ?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario; ?>">

<div id="mostrarModal" class="modalAjuste" style="width:0%; height: 0%; margin-left: -15%; margin-top: 2%;">
    <div id="modalInfo"></div>
</div>

<div id="fadeMostrarModal" class="overlayAjuste" onclick = "limpiarAnalista();
        document.getElementById('mostrarModal').style.display = 'none';
        document.getElementById('fadeMostrarModal').style.display = 'none';
        $('#modalInfo').html('');
        $('input#gestionando').val('no');"></div>

<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
    <div class="col-md-12" align="center" id="divLiquidar" style="display: block; height: 100%;margin-top: 1%">
        <div class="box-header with-border" class="col-lg-12">
            <div class="col-lg-12" id="cuerpoCreditosBuscar" style="display: block">
               
                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="cargaviewAuditoriaOnline();">
                        <span class="badge bg-red" id="cantidad_beneficiario"><?php echo $data['solicitudes_on_pendientes']; ?></span>
                        <i class="fa fa-headphones"></i> Auditoria Online
                    </a>
                </div>
                <div class="box-body pull-left">
                    <a class="btn btn-app" onclick="cargaviewAuditoriaPosterior();">
                        <span class="badge bg-purple" id="solicitudes_pos_pendientes"><?php echo $data['solicitudes_pos_pendientes']; ?></span>
                        <i class="fa fa-podcast"></i> Auditoria Posterior
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dashboard_content">
    <section class="content">

        <div class="col-lg-12" id="main" style="display: block">
            <div id="auditoria_online" style="display: block; background: #FFFFFF;">
                <?php $this->load->view('auditoria_interna/gestion_auditoria_online'); ?>
            </div>
            <div id="section_search_solicitud" class="box box-info " style="background: #FFFFFF; margin-top:10px; display: none">
                <?php $this->load->view('auditoria_interna/section_search_auditoria_posterior', ['solicitudes' => $data['solicitudes_posterior']]); ?>
            </div>
        </div>

    </section>
</div>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script src="<?php echo base_url('assets/auditoria_interna/gestionar.js')?>"></script>
<script src="<?php echo base_url('assets/auditoria_interna/auditoria.js'); ?>"></script>
<script src="<?php echo base_url('assets/gestion/gestion.js'); ?>"></script>
<script>
    $(document).ready(function(){
        cargaviewAuditoriaOnline();
    });
</script>