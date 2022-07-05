<?php $fechaProceso = new DateTime(); ?>

<style>

.bg-grey { background-color: #dfdfdf!important}
.bg-yellow { color: black!important; background-color: #e5e3ae!important}
.btn-width { width: 120px ; display: inline-block}
.btn-box { padding-left: 70%}

</style>

<div class="box box-info">

    <div class="box-header with-border">
            <h6 class="box-title"><small><strong><?= $title?></strong></small>&nbsp;</h6>
    </div>

    <div class="box-body" >

        <form role="form" id ="generacionDebitoAutomaticoBancolombia" enctype="multipart/form-data;charset=utf-8" >            

            <div class="form-group col-md-6">

                <div class="alert bg-grey col-md-12" >
                  BANCO DEBITADOR DEBITOS
                </div>

                <div class="form-group col-md-4">

                    <label for="tipo_archivo" >Tipo Archivo</label>
                    <select class="form-control" name="tipo_archivo" id="tipo_archivo" >
                        <option value="1">Debito Automatico</option>
                        <option value="2">Recaudo por Convenio</option>
                    </select>

                </div>

                <div class="form-group col-md-2">

                    <label for="banco_debitador" >Banco Debitador</label>
                    <select class="form-control" name="banco_debitador" id="banco_debitador" >
                        <option value="1" data-sync="1">Bancolombia</option>
                        <option value="2" data-sync="3">Banco Bogota</option>
                    </select>

                </div>

                <div class="form-group col-md-2">

                    <label for="tipo_debito" >Tipo Debito</label>
                    <select class="form-control" name="tipo_debito" id="tipo_debito" >
                        <option value="1" data-sync="1">Propio</option>
                        <option value="2" data-sync="2">ACH</option>
                    </select>

                </div>

                <div class="form-group col-md-2">

                    <label for="clientes_bancos" >Clientes de Bancos</label>
                    <select class="form-control" name="clientes_bancos" id="clientes_bancos" >
                        <option value="1">Bancolombia</option>
                        <option value="3">Banco Bogota</option>
                        <option value="4">AV Villas</option>
                        <option value="5">ITAU</option>
                        <option value="6">COLPATRIA</option>
                        <option value="2">Otros Bancos</option>
                    </select>

                </div>

                <div class="form-group col-md-2">

                    <label for="autorizacion" >Autorizacion</label>
                    <select class="form-control" name="autorizacion" id="autorizacion" >
                        <option value="0">NO</option>
                        <option value="1">SI</option>
                    </select>

                </div>

            </div>

            <div class="form-group col-md-6">

                <div class="alert bg-yellow col-md-12" >
                  FECHAS DE EJECUCION
                </div>

                <div class="form-group col-md-3">
                    <label for="fechaInicio" >Fecha Inicio Corrida</label>
                    <input type="date" name="fechaInicio" required="true" class="form-control" id="fechaInicio" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
                </div>

                <div class="form-group col-md-4">
                    <label for="fechaFinalizacion" >Fecha Finalizacion Corrida</label>
                    <input type="date" name="fechaFinalizacion" required="true" class="form-control" id="fechaFinalizacion" value="<?php $fechaProceso->modify('+1 day'); echo $fechaProceso->format('Y-m-d');?>" />
                </div>

                <div class="form-group col-md-3" id="fechaVencimiento">
                    <label for="fechaVencimiento" >Fecha A Debitar</label>
                    <input type="date" name="fechaVencimiento" class="form-control" id="fechaVencimiento" value="<?php $fechaProceso->modify('-1 day'); echo $fechaProceso->format('Y-m-d');?>" />
                </div>

            </div>

            <div class="form-group col-md-6">

                <div class="alert bg-grey col-md-12">
                  CLIENTES A DEBITAR
                </div>

                <div class="form-group col-md-12">

                    <div class="form-group col-md-2">

                        <label for="grupo" >Grupo</label>

                        <select class="form-control" name="grupo" id="grupo" >
                            <option value="1">TODOS</option>
                            <option value="2">PRIMARIA</option>
                            <option value="3">RETANQUEO</option>
                        </select>

                    </div>

                    <div class="form-group col-md-2">

                        <label for="estado_deuda" >Estado</label>

                        <select class="form-control" name="estado_deuda" id="estado_deuda" >
                            <option value="1">MORA</option>
                            <option value="2">VIGENTE</option>
                        </select>

                    </div>

                    <div class="form-group col-md-2" id="rangoDiv">

                        <label for="rango" >Rango</label>

                        <select class="form-control" name="rango" id="rango" >
                            <option value="1">DIAS ATRASO RANGO</option>
                            <option value="3">DIAS ATRASO MAYOR A</option>
                            <option value="4">MORA COMPLETA</option>
                            <option value="2">FECHA VENCIMIENTO</option>
                        </select>

                    </div>

                    <div class="form-group col-md-3 hide" id="atrasoMayorDiv">

                        <label for="atraso_desde" >Dias atraso mayor a</label>
                        <input type="text" name="atraso_mayor" class="form-control" id="atraso_mayor" value="15" >

                    </div>

                    <div class="form-group col-md-3" id="atrasoDesdeDiv">

                        <label for="atraso_desde" >Dias atraso desde</label>
                        <input type="text" name="atraso_desde" class="form-control" id="atraso_desde" value="1" >

                    </div>

                    <div class="form-group col-md-3" id="atrasoHastaDiv">

                        <label for="atraso_hasta" >Dias atraso hasta</label>
                        <input type="text" name="atraso_hasta" class="form-control" id="atraso_hasta" value="15" >

                    </div>

                    <div class="form-group hide col-md-3" id="vencimientoDesdeDiv" >

                        <label for="vencimiento_desde" >Vencimiento desde</label>
                        <input type="date" name="vencimiento_desde" required="true" class="form-control" id="vencimiento_desde" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
                    
                    </div>

                    <div class="form-group hide col-md-3" id="vencimientoHastaDiv"  >

                        <label for="vencimiento_hasta" >Vencimiento hasta</label>
                        <input type="date" name="vencimiento_hasta" required="true" class="form-control" id="vencimiento_hasta" value="<?php echo $fechaProceso->format('Y-m-d');?>" />
                    
                    </div>

                </div>

                <div class="form-group col-md-12">

                    <div class="form-group col-md-3">

                        <label for="tope_renovadores" >Tope renovaciones</label>

                        <select class="form-control" name="tope_renovadores" id="tope_renovadores" >

                            <option value="1">SIN TOPE</option>
                            <option value="2">1</option>
                            <option value="3">2</option>
                            <option value="4">3</option>
                            <option value="5">4</option>
                            <option value="6">5</option>
                            <option value="7">6</option>
                            <option value="8">7</option>
                            <option value="9">8</option>
                            
                        </select>

                    </div>

                </div>

            </div>

            <div class="form-group col-md-6">

                <div class="alert bg-grey col-md-12">
                  FORMA DE DEBITAR
                </div>

                <div class="form-group col-md-3">

                    <label for="cantidad_debitos" >Cantidad Debitos</label>

                    <select class="form-control" name="cantidad_debitos" id="cantidad_debitos" >
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>

                </div>

                <div class="form-group col-md-2">

                    <label for="sobre" >Sobre</label>

                    <select class="form-control" name="sobre" id="sobre" >
                        <option value="2">Valor Deuda</option>
                        <option value="1">Valor Cuota</option>
                    </select>

                </div>

                <div class="form-group col-md-2">

                    <label for="tipo" >Tipo</label>

                    <select class="form-control" name="tipo" id="tipo" >
                        <option value="1">Debito Parcial</option>
                        <option value="2">Debito Total</option>
                    </select>

                </div>

                <div class="form-group hide col-md-5" id="aplicaTopeDiv" >

                    <div class="form-group col-md-6">

                        <label for="aplica_tope" >Aplica monto tope</label>

                        <select class="form-control" name="aplica_tope" id="aplica_tope" >
                            <option value="0">NO</option>
                            <option value="1">SI</option>
                        </select>

                    </div>

                    <div class="form-group hide col-md-6" id="valorTopeDiv">
                        <label for="valor_tope" >Valor tope</label>
                        <input type="text" name="valor_tope" class="form-control" id="valor_tope" >
                    </div>

                </div>

                <!-- Factura 1 -->
                <div class="form-group hide col-md-9" id="tipoFactura1DivContent" >

                    <div class="form-group col-md-3" id="valorFactura1Div">
                        <label for="valorFactura1" >Porcentaje</label>
                        <input type="text" name="valorFactura1" class="form-control" id="valorFactura1" >
                    </div>

                </div>

                <!-- Factura 2 -->
                <div class="form-group hide col-md-9" id="tipoFactura2DivContent" >

                    <div class="form-group col-md-3" id="valorFactura2Div">
                        <input type="text" name="valorFactura2" class="form-control" id="valorFactura2" >
                    </div>

                </div>

                <!-- Factura 3 -->
                <div class="form-group hide col-md-9" id="tipoFactura3DivContent" >

                    <div class="form-group col-md-3" id="valorFactura3Div">
                        <input type="text" name="valorFactura3" class="form-control" id="valorFactura3" >
                    </div>

                </div>
            
                <!-- Factura 4 -->
                <div class="form-group hide col-md-9" id="tipoFactura4DivContent" >

                    <div class="form-group col-md-3" id="valorFactura4Div">
                        <input type="text" name="valorFactura4" class="form-control" id="valorFactura4" >
                    </div>

                </div>

                <!-- Factura 5 -->
                <div class="form-group hide col-md-9" id="tipoFactura5DivContent" >

                    <div class="form-group col-md-3" id="valorFactura5Div">
                        <input type="text" name="valorFactura5" class="form-control" id="valorFactura5" >
                    </div>

                </div>

            </div>
            
            <div class="row col-md-12 btn-box" >

                <button type="button" class="btn btn-success btn-width" id="btnGenerarVista">Generar Vista</button>

                <button type="button" class="btn btn-success btn-width" id="btnEnviarArchivos">Enviar</button>

                <button type="button" class="btn btn-success btn-width" id="btnDescargarArchivos">Descargar</button>

                <button type="button" class="btn btn-success btn-width" id="btnPreNotificacion">Pre Notificacion</button>

            </div>

        </form>

    </div>

    <?php if($banco == "bancolombia"):?>
        <?= $this->load->view('tesoreria/table_generacion_debitos', null, true); ?>
        <script src="<?php echo base_url() ?>assets/tesoreria/generacion_debitos.js"></script>
    <?php endif;?>

</div>