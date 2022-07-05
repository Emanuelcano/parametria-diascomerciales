<style type="text/css">
    hr{
        border: 0 none #e8e1e0;
        border-top: 1px solid #e8e1e0;
        height: 1px;
        margin: 5px 0;
        display: block;
        clear: both;
    }

    .loader {
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
</style>
<div align="center">
    <h2>Reportes Asignaci&oacute;n</h2>
</div>
<form action="<?php base_url();?>reportes/Reportes/exportar_reporte_asignacion" method="POST" id="frm-exportar_asignacion" name="frm-exportar_asignacion">
    <br>
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Fecha inicio:</label>
                    <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="input" autocomplete="off" class="fechas form-control datetimepicker" name="date_inicio" id="date_inicio">
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Fecha fin:</label>
                    <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="input" autocomplete="off" class="fechas form-control datetimepicker" name="date_fin" id="date_fin">
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <label>Operador</label>
                    <div class="input-group" id="selector">
                        <span class="input-group-addon"><i class="fa fa-address-book"></i></span>
                            <select class="form-control" name="sl_operador" id="sl_operador">
                                    <option value="0" selected="selected"> TODOS </option>
                            </select> 
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label>Exportar</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-success" id="btnExportar-xls"><i class="fa fa-file-excel-o"> XLS</i></button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</form>
<br>
<hr class="margin-top:10px;">
<div class="modal fade" id="compose-modal-wait" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS SE GENERA SU BUSQUEDA </h4>
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
            <div class="loader"></div> 
        </div>
            <div class="modal-footer clearfix">
            </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/supervisores/select2Bootstrap.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url('assets/reportes/control_asignacion.js');?>" ></script>