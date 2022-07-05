<style>
    body table{
        font-size:11px !important;
    }
    table input{
        border-top: 0px !important;
        border-left: 0px !important;
        border-right: 0px !important;
        background: transparent !important;
        font-size: 11px !important;
        height: 15px !important;
    }
</style>
<div class="row">
    <div class="col-md-12 search-section">
        <?php $this->load->view('cobranzas/section_search_creditos'); ?>
    </div>

    <div class="col-md-12 ajustes" style="display:none;">
       
    <!-- informacion del cliente -->
        <div id="box_client_title" data-id_cliente="" class="box box-info">
            <div class="box-header" id="titulo" style="padding:0;background-color: #fffdfa;box-shadow: 0px 9px 10px -9px #888888;">
                <div class="row">
                
                    <div class="col-md-4 text-center">
                        <h4><i class="fa fa-user"></i>
                        <span id="nombre-cliente"> </span>
                        </h4>
                    </div>

                
                    <div class="col-md-2 text-center">
                        <h4><i class="fa fa-id-card"></i>
                        <span id="documento-cliente"> </span>
                        </h4>
                    </div>
                    <div class="col-md-2 text-center">

                        <h4><i class="fa fa-phone"></i>
                        <span id="telefono-cliente"> </span>
                        </h4>
                    </div>
                    <div class="col-md-3 text-center">
                        <h4><i class="fa fa-envelope"></i>
                        <span id="mail-cliente"> </span>
                        </h4>
                    </div>

                    <div class="col-md-1 text-center">
                        <a id="close_credito" class="btn btn-danger btn-xs" title="Cerrar y continuar gestionando otro crédito" style="border-radius:50%;margin-top: 7px;"><i class="fa fa-close"></i></a>
                    </div>
                </div>
            </div><!-- end box-header -->
        </div>

    <!-- informacion de los creditos -->
        <div class="col-sm-12 creditos">
        
            <table class="table table-striped table-bordered text-center" id="creditos-cliente">
                <thead  style="background: #cccaf4" style="background: #cccaf4">
                    <th>CREDITO</th>
                    <th>CUOTA</th>
                    <th>VENCIMIENTO</th>
                    <th>CAPITAL</th>
                    <th>INTERES</th>
                    <th>SEGURO</th>
                    <th>ADMINISTRACION</th>
                    <th>TECNOLOGIA</th>
                    <th>IVA</th>
                    <th>MONTO POR CUOTA</th>
                    <th>DIAS DE ATRASO</th>
                    <th>INTERES MORA</th>
                    <th>TECNOLOGIA MORA</th>
                    <th>MULTA MORA</th>
                    <th>DESCUENTO</th>
                    <th>MONTO A COBRAR</th>
                    <th>MONTO COBRADO</th>
                    <th>FECHA DE COBRO</th>
                    <th>ESTADO CUOTA</th>
                    <th>ESTADO CREDITO</th>
                    <th></th>
                </thead>
                <tbody>
                </tbody>
            </table>
            
        </div>

    <!-- Seccion de ajuste de cuotas -->
        <!-- DESCUENTOS  -->
        <div class="col-sm-4 col-md-4 hide " id="seccion-descuento" style="min-width: 300px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>Descuento de cuota:  <span class="id_cuota"></span></b> </h4></div>
                <div class="panel-body">
                    <div class="row">
                        <!--<div class="col-md-5">
                            <label for="descuento-solventa">Descuentos porcentuales: </label>
                        </div>
                        <div class="col-md-7">
                            <select name="descuento-solventa" id="descuento-solventa" class="form-control">
                                
                            </select>
                        </div> -->

                        <div class="col-md-5">
                            <label for="descuento">Descuento monto fijo: </label>
                        </div>
                        <div class="col-md-7">
                            <input name="descuento" type="number" id="descuento" min="0" value = "0" class="form-control" readonly="true">
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                                <h5> Monto antes del descuento</h5>                            
                                <h5 class="text-warning" id="old-monto">0</h5>                            
                        </div>
                        <div class="col-md-6">
                                <h5>Monto a cobrar con descuento</h5>                            
                                <h5 class="text-success" id="new-monto">0</h5>                            
                        </div>
                        <div class="col-md-12 text-center">
                                <a class="btn btn-success" id="aplicar-descuento">APLICAR</a> 
                        </div>
                        <div class="col-md-12 text-center nota-descuento text-center hide" style="background-color: #ffc10736;color: ">
                                  <h5>El monto ingresado no puede superar el monto a pagar</h5>
                                 
                        </div>
                        
                    </div>

                  
                </div>
            </div>
        </div>

        <!-- PAGOS -->
        <div class="col-sm-8 col-md-8 hide" id="seccion-pagos" style="min-width: 300px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>Pagos de la cuota: <span class="id_cuota"></span></b><!-- <a class="btn btn-success btn-xs agregar-detalle-pago" style="float:right"><i class="fa fa-spinner"></i></a> --></h4></div>
                <div class="panel-body">
                    

                    <table class="table table-striped table=hover display" id="tabla-pagos">
                        <thead>
                            <th>ID PAGO</th>
                            <th>TIPO PAGO</th>
                            <th>FECHA PAGO</th>
                            <th>MONTO TOTAL</th>
                            <th>CUOTA</th>
                            <th>MONTO CUOTA</th>
                            <th>REFERENCIA</th>
                            <th>ESTADO</th>
                            <th>EXCEDENTE</th>
                            <th></th>

                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="loader" id="loader-6" style="top: 10px;height: 17px;">
                        <span style="width: 3px; height: 10px"></span>
                        <span style="width: 3px; height: 10px"></span>
                        <span style="width: 3px; height: 10px"></span>
                        <span style="width: 3px; height: 10px"></span>
                    </div>
                    <br>
                    <div class="row nuevo-detalle-pago hide"><hr>
                        <div class="col-md-3">
                            
                            <div class="form-group">
                                <label for="new-idpago">ID pago</label>
                                <select class="form-control" id="new-idpago">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="new-idcuota">Cuota a pagar</label>
                                <input type="number" min="1" class="form-control" id="new-idcuota" placeholder="ID cuota">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="new-montocuota">Monto de la cuota</label>
                                <input type="number" min="0" value = "0" class="form-control" id="new-montocuota" placeholder="Monto Cuota">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="new-excedente">Excedente</label>
                                <input type="number" min="0" class="form-control" id="new-excedente" value="0" placeholder="Excedente">
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                        <a class="btn btn-success " id="agregar-detalle">AREGAR DETALLE</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
</div>
