<style>
    /* ALL LOADERS */

    .loader{
    width: 100px;
    height: 100px;
    border-radius: 100%;
    position: relative;
    margin: 0 auto;
    }

    /* LOADER 6 */

    #loader-6{
    top: 40px;
    left: -2.5px;
    }

    #loader-6 span{
        display: inline-block;
        width: 5px;
        height: 30px;
        /* background-color: #605ca8; */
        background-color: rgba(255, 206, 86, 0.75);
    }

    #loader-6 span:nth-child(1){
    animation: grow 1s ease-in-out infinite;
    }

    #loader-6 span:nth-child(2){
    animation: grow 1s ease-in-out 0.15s infinite;
    }

    #loader-6 span:nth-child(3){
    animation: grow 1s ease-in-out 0.30s infinite;
    }

    #loader-6 span:nth-child(4){
    animation: grow 1s ease-in-out 0.45s infinite;
    }

    @keyframes grow{
        0%, 100%{
            -webkit-transform: scaleY(1);
            -ms-transform: scaleY(1);
            -o-transform: scaleY(1);
            transform: scaleY(1);
        }

        50%{
            -webkit-transform: scaleY(1.8);
            -ms-transform: scaleY(1.8);
            -o-transform: scaleY(1.8);
            transform: scaleY(1.8);
        }
    }
</style>

<div class="container-fluid" style="margin-top: 3%;">
    <!-- /*** URL base ***/ -->
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">

    <!-- Button modal -->
    <div class="row text-right">
        <div class="col-md-1 col-md-offset-11" style="position: absolute; z-index: 1; padding-top: 8px;">
            <button type="button" 
                class="btn btn-primary text-center btn-sm img-circle" 
                id="modalBtn" 
                style="box-shadow: -3px 3px 3px 1px rgba(0, 0, 0, 0.2);"
            >
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>

    <!-- Contenedor de los tres gráficos -->
    <div id="graphContainer" style="position: relative;">
        <div class="row">
            <!-- /*** PIE ***/ -->
            <div class="col-md-4 text-center">
                <h5 class="bg-primary" style="padding: 6px 0px 6px;">
                    SOLICITUDES <span id="tipoPie"></span> TOTALES: 
                    <strong><span id="total">0</span></strong>
                    DEL PERÍODO: 
                    <span id="periodoPie"></span>
                </h5>
                <canvas id="myChartPie" width="3" height="2"></canvas>
            </div>
            <!-- /*** BAR ***/ -->
            <div class="col-md-8 text-center">
                <h5 class="bg-info" style="padding: 6px 0px 6px;">
                    <strong>
                        STATUS DEL FLUJO DE SOLICITUDES 
                        <span id="tipoBar"></span>
                        DEL PERÍODO: 
                        <span id="periodoBar"></span>
                    </strong>
                </h5>
                <canvas id="myChartBar" width="9" height="3"></canvas>
            </div>
        </div>
        <!-- /*** LINE ***/ -->
        <div class="row">
            <div class="col-md-12 text-center">
                <h5 class="bg-warning" style="padding: 6px 0px 6px;">
                    <strong>
                        SOLICITUDES  
                        <span id="tipoLine"></span>
                        POR HORA DEL PERÍODO: 
                        <span id="periodoLine"></span>
                    </strong>
                </h5>
                <canvas id="myChartLine" width="15" height="3"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- Modal Formulario búsqueda -->
<div class="modal fade" id="modalBuscar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Criterios de búsqueda</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="form-row">
                            <div class="form-group col-md-4 col-md-offset-2">
                                <label for="selectTipo">Tipo:</label>
                                <select class="form-control" id="selectTipo" name="selectTipo">
                                    <?php foreach ($tipoSolicitud as $value) {?>
                                        <option value="<?= $value['tipo_solicitud'] ?>"><?= $value['tipo_solicitud'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="fecha">Fechas:</label>
                                <input type="text" class="form-control" id="date_range" name="fecha">
                            </div>
                        </div>
                    </div>
                    <div class="row text-center">
                        <a class="btn btn-info" id="aEnvio"><i class="fa fa-search"></i> BUSCAR</a>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal para el Loading -->
<div class="modal fade" id="modalLoading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <!-- Para el loading -->
    <div style="padding-top: 20%;">
        <div id="main" style="marging-top: 100px;"></div>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>
<script src="<?php echo base_url('assets/tablero_flujo/tableroFlujo.js')?>"></script>
<script src="<?php echo base_url('assets/js/Chart.min.js');?>"></script>
<script src="<?php echo base_url('assets/js/chartjs-plugin-datalabels.min.js');?>"></script>
<script>
    $(document).ready(function() {
        let fecha = moment().format("DD-MM-YYYY") + " | " + moment().format("DD-MM-YYYY");
        $("#date_range").val(fecha);

        $('#graphContainer').hide();
        cargaInicial();
        /*** Volver a refrescar los datos cada 5 Minutos ***/
        window.clearInterval();
        window.setInterval(function() { cargaInicial(); }, 300000);

        $('#modalBtn').on('click', function(){
            $('#modalBuscar').modal("show");
        })
    });
</script>