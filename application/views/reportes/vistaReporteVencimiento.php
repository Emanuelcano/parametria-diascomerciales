<style type="text/css">
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
<div>
    <h1 align="center">Reportes Vencimiento</h1>
</div>
<form action="<?php base_url();?>reportes/Reportes/exportar_csv_vencimiento" method="POST" id="frm-exportar-vencimiento" name="frm-exportar-vencimiento">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Tipo Solicitud:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                                <select class="form-control" name="sl_tipo_solicitud" id="sl_tipo_solicitud">
                                    <option value="TODOS" selected="selected"> TODOS </option>
                                    <option value="PRIMARIA"> PRIMARIA </option>
                                    <option value="RETANQUEO"> RETANQUEO</option>
                                </select>
                        </div>
                </div>
            </div>
            <div class="col-lg-3">
            <div class="form-group">    
                <label>Estado Solicitud:</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-address-book"></i></span>
                            <select class="form-control" name="sl_estado" id="sl_estado">
                                    <option value="TODOS" selected="selected"> TODOS </option>
                                    <option value="vigente"> VIGENTE </option>
                                    <option value="mora"> MORA</option>
                                    <option value="pagado"> PAGADO</option>
                            </select>    
                        </div>              
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label>Fecha Inicio:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="input" autocomplete="off" class="form-control datetimepicker" name="sl_desde" id="sl_desde">
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group">
                    <label>Fecha Fin:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="input" autocomplete="off" class="form-control datetimepicker" name="sl_hasta" id="sl_hasta">
                        </div>
                </div>
            </div>
            <div class="col-lg-2 pt-2">
                <div class="form-group">
                    <label>Exportar</label>
                        <div class="input-group">
                            <button type="button" class="btn btn-success" id="btn-exportar"><i class="fa fa-file-excel-o"> CSV</i></button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</form>
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
<script type="text/javascript" src="<?php echo base_url('assets/reportes/control_reportes.js');?>" ></script>


