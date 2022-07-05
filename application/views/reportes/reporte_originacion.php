    <!-- INICIO DE PESTAÑAS PARA ORIGINACION -->
<div id="originacion_sub_menu" class="btn-group row col-md-12">      
    <ul class="nav nav-tabs">
        <li class="active"><a href="#pestaña_asignacion" data-toggle="tab">Asignaci&oacute;n</a></li>
        <li><a href="#pestaña_casos_devuelvotos" data-toggle="tab">Devueltos</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="pestaña_asignacion">
            <?php $this->load->view('reportes/reporte_asignacion');?>
        </div>           
        <div class="tab-pane" id="pestaña_casos_devuelvotos">
            <?php $this->load->view('reportes/casos_devueltos');?>
        </div>           
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/supervisores/select2Bootstrap.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url('assets/reportes/control_reportes.js');?>" ></script>