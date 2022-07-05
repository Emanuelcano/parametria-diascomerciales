<?php $fechaProceso = new DateTime(); ?>
<div class="box box-info">

    <div class="box-header with-border">
            <h6 class="box-title"><small><strong><?= $title?></strong></small>&nbsp;</h6>
    </div>

    <div class="box-body" >

        <form role="form" id ="generacionDebitoAutomaticoBancolombia" enctype="multipart/form-data;charset=utf-8" >            

            <div class="form-group">
                <label for="fechaInicio" >Fecha Inicio Corrida</label>
                <input type="date" name="fechaInicio" required="true" class="form-control" id="fechaInicio" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
            </div>

            <div class="form-group">
                <label for="fechaFinalizacion" >Fecha Finalizacion Corrida</label>
                <input type="date" name="fechaFinalizacion" required="true" class="form-control" id="fechaFinalizacion" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
            </div>

            <div class="form-group " id="fechaVencimiento">
                <label for="fechaVencimiento" >Fecha Vencimiento </label>
                <input type="date" name="fechaVencimiento" class="form-control" id="fechaVencimiento" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
            </div>

<!--             <div class="form-group">
                <label for="conFechaVencimiento" >Con Filtro por Fecha Vencimiento</label>
                <select class="form-control" name="conFechaVencimiento" id="conFechaVencimiento" >
                    <option value="no">NO</option>
                    <option value="si">SI</option>
                </select>
            </div>

            <div class="form-group hide" id="filtroFechaVencimiento">
                <label for="filtroFechaVencimiento" >Filtro Fecha Vencimiento </label>
                <input type="date" name="filtroFechaVencimiento" class="form-control" id="filtroFechaVencimiento" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
            </div> -->

            <div class="form-group hide" id="fechaVencimientoInicio">
                <label for="fechaVencimientoInicio" >Fecha Vencimiento Inicio</label>
                <input type="date" name="fechaVencimientoInicio" class="form-control" id="fechaVencimientoInicio" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
            </div>

            <div class="form-group hide" id="fechaVencimientoFinal">
                <label for="fechaVencimientoFinal" >Fecha Vencimiento Final</label>
                <input type="date" name="fechaVencimientoFinal" class="form-control" id="fechaVencimientoFinal" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
            </div>

            <div class="form-group">
                <label for="auth" >Autorizacion</label>
                <select class="form-control" name="auth" id="auth" >
                    <option value="si">SI</option>
                    <option value="no">NO</option>
                </select>
            </div>

            <div class="form-group">
                <label for="estado" >Estado</label>
                <select class="form-control" name="estado" id="estado" >
                    <option value="mora">Morosos</option>
                    <option value="vigente">Vigentes</option>
                </select>
            </div>

            <div class="form-group">
                <label for="conDebitoMultiple" >Con Debitos Multiples</label>
                <select class="form-control" name="conDebitoMultiple" id="conDebitoMultiple" >
                    <option value="no">NO</option>
                    <option value="si">SI</option>
                    <option value="otro">OTRO</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="bancotipo" >Banco</label>
                <select class="form-control" name="bancotipo" id="bancotipo" >
                    <option value="bancolombia">Bancolombia</option>
                    <option value="otrosbancos">Otros bancos</option>
                </select>
            </div>

            <div class="form-group hide" id="tipoempleado" >
                <label for="tipoempleado" >Tipo empleado</label>
                <select class="form-control" name="tipoempleado" id="tipoempleado" >
                    <option value="dependientes">DEPENDIENTES</option>
                    <option value="independientes">INDEPENDIENTES</option>
                </select>
            </div>

            <div class="form-group" id="topeDiv">
                <label for="tope" >Tope</label>
                <input type="text" name="tope" class="form-control" id="tope" >
            </div>

            <div class="form-group" id="atrasoDiv">
                <label for="atraso" >Dias de Atraso > a</label>
                <input type="text" name="atraso" class="form-control" id="atraso" >
            </div>

            <div class="form-group" id="sqlClientes">
                <label for="sqlClientes" >Query Clientes</label>
                <textarea id="sqlClientes" name="sqlClientes" rows="30" cols="60"></textarea>
            </div>

            <div class="form-group" id="sqlMonto">
                <label for="sqlMonto" >Query Monto</label>
                <textarea id="sqlMonto" name="sqlMonto" rows="30" cols="60"></textarea>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-success" id="btnGenerarVista">Generar Vista</button>
                <div id="detalle-vista-debito-automatico" class="pull-right"></div>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-success" id="btnEnviarArchivos">Enviar Archivos</button>
                <div id="detalle-archivo-imputacion-automatica" class="pull-right"></div>
            </div>

        </form>

    </div>

    <?php if($banco == "bancolombia"):?>
        <?= $this->load->view('tesoreria/table_debito_automatico_bancolombia', null, true); ?>
        <!-- script de imputacion automatica -->
        <script src="<?php echo base_url() ?>assets/tesoreria/bancolombia_generacion_debito_automatico.js"></script>
    <?php endif;?>

</div>
