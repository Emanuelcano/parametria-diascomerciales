<style>
    #label{
        text-align:center;
        border-bottom: solid #eee;
    }

    #tabla{
        padding-top:2%;
    }

    #columnas{
        background-color: #D8D5F9;
    }
</style>

<div class="col-lg-12">
    <div id="label">
        <h3>Recaudos sin Imputar</h3>
    </div>

    <div id="tabla">
    <table id="table_sin_imputar" class="table table-striped table-bordered" style="width:100%">
        <thead id="columnas">
            <tr>
                <th>documento</th>
                <th>Fecha Recaudo</th>
                <th>Monto Total</th>
                <th>Origen Pago</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="data">

        </tbody>
    </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_sin_imputar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="width:155%; right:30%;">
            <div class="modal-header">
                <button class="close btn_cerrar" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Imputación de Pago</h4>
            </div>
            <div class="modal-body" id="data_modal">
                <div class="container">
                    <div class="col-lg-12" style="display:flex;">
                        <div class="input-group col-lg-3" id="div_fecha">
                            <label> Fecha transferencia:</label>
                            <input type="text" class="form-control" id='fecha_rec' disabled>
                        </div>

                        <div class="input-group col-lg-3" style="padding-left:3%;" id="div_monto">
                            <label> Monto transferencia:</label>
                            <input type="text" class="form-control" id='monto_rec' disabled>
                        </div>
                        
                        <div class="input-group col-lg-3" id="div_origen" style="padding-left:3%;">
                            <label> Origen Pago:</label>
                            <input type="text" class="form-control" id='origen_rec' disabled>
                        </div>
                    </div>
                    <div class="col-lg-12" style="display:flex; padding-top:2%;">
                        
                        <div class="input-group col-lg-3" id="div_documento">
                            <label> Documento del Depositante:</label>
                            <input type="text" class="form-control" id='docDep_rec' disabled>
                        </div>

                        <div class="input-group col-lg-2" style="padding-left:3%;" id="div_documento_titular">
                            <label> Documento del titular:</label>
                            <input type="text" class="form-control sol_num" id='docTitular_rec'>
                        </div>

                        <div class="col-lg-1" id="div_btnVerificar" style="padding-top: 2.2%;">
                            <input type="button" id="btn_verificar_doc" value="Verificar" class="btn btn-info">
                        </div>
                        
                    </div>
                    <div class="col-lg-12" id="div_data_cliente" style="display:none; padding-top:2%;">
                        <hr style="border-top: 1px solid #dadada; width:75%; margin-right:31%;">
                        <div style="text-aling:center;">
                            <h3>Cliente:</h3>
                        </div>
                        <div class="col-lg-12" style="display:flex; padding-top:1%; background-color:#e8e8e8; width:75%; border-radius:5px;">
                            <div class="input-group col-lg-1" id="div_id_cliente">
                                <label> #</label>
                            </div>
                            <div class="input-group col-lg-3" id="div_nombres_cliente">
                                <label> Nombres:</label>
                            </div>
                            <div class="input-group col-lg-3" id="div_apellidos_cliente">
                                <label> Apellidos:</label>
                            </div>

                            <div class="input-group col-lg-3" id="div_documento_cliente">
                                <label> Documento:</label>
                            </div>

                            <div class="input-group col-lg-3" id="div_estado_cliente">
                                <label> Estado crédito:</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12" id="div_nodata"  style="display:none; padding-top:2%;">
                        <input type="hidden" id="doc_insert">
                        <input type="hidden" id="id_sin_impu">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn_cerrar" data-dismiss="modal">Cancelar</button>
                <input type="button" class="btn btn-success" id="btn_imputar" value="Imputar"></input>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/supervisores.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/recaudosSinImputar.js'); ?>"></script>
