    
    <!-- FIN FORMULARIO SOLICITUDES INDICADORES -->
<div id="solicitudes_sub_menu" class="btn-group row col-md-12">
        <!-- <button id="btn_solicitudes_indicadores" type="button" class="btn btn-default">Indicadores</button>
        <button id="btn_solicitudes_gestion" type="button" class="btn btn-default">Gestión</button>
        <button id="btn_solicitudes_tracks" type="button" class="btn btn-default">Tracks</button> -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#solicitudes_indicadores" data-toggle="tab">Indicadores</a></li>
            <li><a href="#solicitudes_gestion" data-toggle="tab">Gestión</a></li>
            <li><a href="#solicitudes_tracks" data-toggle="tab">Tracks</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="solicitudes_indicadores">
                <?php $this->load->view('reportes/reporte_indicadores'); ?>
            </div>
            <div class="tab-pane" id="solicitudes_gestion">
                <?php $this->load->view('reportes/reporte_gestion'); ?>
            </div>
            <div class="tab-pane" id="solicitudes_tracks">
                <?php $this->load->view('reportes/reporte_tracks'); ?>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="<?php echo base_url('assets/reportes/reportes_solicitudes.js');?>" ></script>

