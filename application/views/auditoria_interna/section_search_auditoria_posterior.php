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

<?php 

/*echo '<pre>'; print_r($solicituds_status); echo '</pre>';

echo '<pre>'; print_r($solicituds_types); echo '</pre>';

echo '<pre>'; print_r($operators); echo '</pre>';*/

?>
<!-- resumen de las auditorias -->
<div>
    <div class="col-md-10" style="padding-top: 5px;padding-bottom: 5px;"><p class="text-muted" style="font-size: 16px;"><b>Prestamos por auditar <span id="fracha-hora-hoy" style="margin-right:20px;">19-06-2020 14:03:59</span></b> Total: <span id="total-auditorias"><b>10</b></span> | Auditados: <span id="auditados"><b>10</b></span> | Por auditar: <span id="por-auditar"><b>10</b></span></p></div>
    <div class="col-md-2 text-right" style="padding-top: 5px;padding-bottom: 5px;"><a class="btn bg-purple"> <i class="fa fa-archive"></i> CERRAR JORNADA</a></div>
</div>

<div style="margin-bottom: 10px">
    <form class="form-inline">
        <div class="form-group">
            <label for="fecha">Fecha llamada:</label>
            <input type="text" class="form-control" id="date_range" name="fecha">
        </div>
        <a class="btn btn-info" id="aEnvio"><i class="fa fa-search"></i> BUSCAR</a>
    </form>
</div>

<!-- tabla de solicitudes no auditadas -->
<div id="result_posterior">
    <table align="center" id="table_search_posterior" class="table table-responsive table-striped table=hover " width="100%" >
        <thead style="font-size: smaller; ">
            <tr class="info">
                <th></th>
                <th style="text-align: center;">N° Solicitud</th>
                <th style="text-align: center;">Fecha</th>
                <th style="text-align: center;">Documento</th>
                <th style="text-align: center;">Solicitante</th>
                <th style="text-align: center;">Vendedor</th>
                <th style="text-align: center;">Monto Prestar</th>
                <th style="text-align: center;">Score</th>
        </thead>
        <tbody style="font-size: 12px; text-align: center;">
            
        </tbody>
    </table>
</div>

<!-- seccion de auditoria -->
<section id="auditar-solicitud" style="display:none;">
    <div id="box_client_title" class="box box-info">
        <div class="box-header" id="titulo" style="background-color: #fffdfa;box-shadow: 0px 9px 10px -9px #888888;">
            <div class="row">
                    
                    <div class="col-md-2 text-center">
                        <h5 class=""><i class="fa fa-user"></i>
                        <strong><label id="lbl_solicitante_post" ></label></strong>
                        
                        </h5>
                    </div>
                    <div class="col-md-2 text-center">
                        <h5 class=""><i class="fa fa-id-card"></i>
                        <strong><label id="lbl_documento_post" ></label></strong>
                        </h5>
                    </div>
                    <div class="col-md-2 text-center">

                        <h5 class="">
                            Tipo: <strong><label id="lbl_tipo_solicitud_post" ></label></strong>
                        </h5>
                    </div>
                    <div class="col-md-2 text-center">
                        <h5 class="">
                            Fecha alta: <strong><label id="lbl_fecha_alta_post" ></label></strong>
                        </h5>
                    </div>

                    <div class="col-md-2 text-center">
                        <h5 class="">
                            Fecha aprobado: <strong><label id="lbl_fecha_aprobado_post" ></label></strong>
                        </h5>
                    </div>
                    <div class="col-md-2 text-center">
                    <a class="btn btn-md img-circle" style="float: right; color: red; background: #d2d6de;" id="a_close"><i class="fa fa-close"></i></a>
                        <h5 class="">
                            Monto solicitado: <strong>$<label id="lbl_monto_post" ></label></strong>
                        </h5>
                    </div>
            </div>
        </div><!-- end box-header -->
    </div><!-- end box-info -->   

    <!-- tabla de audios para las solicitud seleccionada -->
    <div class="row" id="audios-no-auditados">
        <div class="col-md-12">
            <table align="center" id="table-audios-no-auditados" class="table table-responsive table-striped table=hover " width="100%" >
                <thead style="font-size: smaller; ">
                    <tr class="info">
                        <th style="text-align: center;">Fecha</th>
                        <th style="text-align: center;">Vendedor</th>
                        <th style="text-align: center;">Teléfono</th>
                        <th style="text-align: center;">Contacto</th>
                        <th style="text-align: center;">Fuente</th>
                        <th style="text-align: center;">Duración</th>
                        <th style="text-align: center;">Tipo Llamada</th>
                        <th style="text-align: center;">Finalizado Por</th>
                        <th style="text-align: center;">Central</th>
                        <th style="text-align: center;">Audio</th>
                </thead>
                <tbody style="font-size: 12px; text-align: center;">
                
                </tbody>
            </table>
        </div>
    </div>

    <div class="row" id="container-no-auditados">
        <div class="col-md-12">
            <audio controls id="audio_audio" style="width: 100%;">
                <p>Tu navegador no implementa el elemento audio.</p>
            </audio>
        </div>
        <div class="col-md-12">
            <form name="frm_califica" method="POST" id="frm_califica_post">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title text-center">Calificación de servicio</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="txt_hd_solicitud" id="txt_hd_solicitud_post">
                            <input type="hidden" name="txt_hd_audio" id="txt_hd_audio_post">
                            <input type="hidden" name="txt_hd_operacion" id="txt_hd_operacion_post">
                            <input type="hidden" name="txt_hd_id_auditoria" id="txt_hd_id_auditoria_post">
                            <input type="hidden" name="txt_hd_telefono" id="txt_hd_telefono_post">
                            <div class="col-md-10 text-center">
                                <div class="form-group">
                                <label for="exampleFormControlTextarea1">Observaciones</label>
                                <textarea class="form-control" name="txt_observaciones" id="txt_observaciones_post" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-2 text-left">
                                <label for="exampleFormControlTextarea1">Calificación</label>
                                <!-- radio -->
                                <div class="form-group clearfix">
                                    <div class="icheck-success d-inline">
                                        <input type="radio" id="rd_califica1_post" value="BUENA" name="rd_califica_post" checked>
                                        <label for="rd_califica1_post">
                                            BUENA
                                        </label>
                                    </div>
                                    <div class="icheck-warning d-inline">
                                        <input type="radio" id="rd_califica2_post" value="REVISAR" name="rd_califica_post">
                                        <label for="rd_califica2_post">
                                            REVISAR
                                        </label>
                                    </div>
                                    <div class="icheck-danger d-inline">
                                        <input type="radio" id="rd_califica3_post" value="MALA" name="rd_califica_post">
                                        <label for="rd_califica3_post">
                                            MALA
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>       
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table=hover display table-striped" id="resumen-auditoria_post" style="width: 100%">
                            <thead style="background: rgba(103, 58, 183, 0.19);">
                                <th>Id</th>
                                <th>Fecha</th>
                                <th>Solicitud</th>
                                <th>Número</th>
                                <th>Observación</th>
                                <th>Calificación</th>
                                <th>Proceso</th>
                                <th>Acción</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer clearfix">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-lg btn-success" id="btn_guardar_post">
                                <i class="fa fa-save"></i> Guardar
                            </button>
                        </div>
                        <!-- <div class="col-md-6 text-left">
                            <button class="btn btn-lg btn-default" type="button" data-widget="remove" id="clouse_modal">
                                <i class="fa fa-times"></i> Cancelar
                            </button>
                        </div> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<section>
    <div id="texto" 
        class="col-lg-12 hide" 
        style="display: block; background: #EBEDEF;">
    </div>
</section>
<!-- /*** Modal del spinner ***/ -->
<div class="modal fade" id="compose-modal-wait-post" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS SE GENERA SU BUSQUEDA </h4>
                </div>
            <div class="modal-body">
                <div class="loader"></div> 
            </div>
            <div class="modal-footer clearfix">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>

<script>
    let fecha = moment(moment().subtract(7, 'days').calendar()).format("DD-MM-YYYY") + " | " + moment().format("DD-MM-YYYY");
    $("#date_range").val(fecha);
</script>