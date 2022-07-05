<div class="box-header with-border">
    <h6 class="box-title"><small><strong>Consultar Solicitud</strong></small>&nbsp;</h6>
</div>
<div id="section_search_solicitud">
    <div class="box-header with-border" style="padding-bottom: 3em;">
        <div class="col-md-12" style="padding:0px; margin:0px;">
            <form id="form_search_solicitud" class="form-inline col-md-10" style="padding-left: 0px;" method="get">
                <input id="search_solicitud" name="search" type="text" class="form-control" placeholder="N° Solicitud">
                <a class="btn btn-info" id="aBuscar" title="Buscar Solicitud" style="font-size: 12px;"><i class="fa fa-search"></i> Buscar</a>
                <button type="reset" class="btn btn-default" title="Limpiar Solicitud" style="font-size: 12px;"><i class="fa fa- fa-remove"></i> Limpiar</button>
            </form>
        </div>
    </div>
</div>

<div class="box box-info">
    <div class="box-header with-border">
        <h6 class="box-title"><small><strong>Validar desembolso</strong></small>&nbsp;</h6>
    </div>
    <div class="box-body" >
        <div class="row">
        </div>
    </div>
</div> 
<div id="tbl_validar_desembolsos" style="padding-bottom:3em;">
    <table data-page-length='10' id="tp_validar_desembolsos" class="table table-striped hover table-responsive" width="100%">
        <thead>
            <tr class="info">
                <th style="width: 16%; padding: 0px; padding-left: 10px; text-align: center;">No Solicitud</th>
                <th style="width: 16%; padding: 0px; padding-left: 10px; text-align: center;">Estado Solicitud</th>
                <th style="width: 16%; padding: 0px; padding-left: 10px; text-align: center;">Tipo Solicitud</th>
                <th style="width: 20%; padding: 0px; padding-left: 10px; text-align: center;">Nombre Cliente</th>
                <th style="width: 16%; padding: 0px; padding-left: 10px; text-align: center;">Operador</th>
                <th style="width: 16%; padding: 0px; padding-left: 10px; text-align: center;">Fecha Solicitud Desembolso</th>
                <th style="width: 16%; padding: 0px; padding-left: 10px; text-align: center;">Validar</th>
            </tr>
        </thead>
        <tbody class="text-center" id="tbl_body_validar_desembolsos">
            <?php foreach ($desembolsos as $desembolso) {?>
                <tr>
                    <td><?= $desembolso['id_solicitud'] ?></td>
                    <td><?= $desembolso['estado'] ?></td>
                    <td><?= $desembolso['tipo_solicitud'] ?></td>
                    <td class="text-left"><?= $desembolso['nombre_apellido'] ?></td>
                    <td class="text-left"><?= $desembolso['nombre_apellido_operador'] ?></td>
                    <td><?= $desembolso['fecha_hora_solicitud'] ?></td>
                    <td>
                        <a id="aValidar" 
                            class="btn btn-primary btn-xs"
                            title="validar"
                            id_desembolso="<?= $desembolso['id'] ?>"
                            id_solicitud="<?= $desembolso['id_solicitud'] ?>"
                            fecha_hora_solicitud="<?= $desembolso['fecha_hora_solicitud'] ?>"
                            fecha_alta="<?= $desembolso['fecha_alta'] ?>"
                            documento="<?= $desembolso['documento'] ?>"
                            nombre_apellido="<?= $desembolso['nombre_apellido'] ?>"
                            origen_pago="<?= $desembolso['origen_pago'] ?>"
                            fecha_procesado="<?= $desembolso['fecha_procesado'] ?>"
                            pagado="<?= $desembolso['pagado'] ?>"
                            ruta_txt="<?= $desembolso['ruta_txt'] ?>"
                            nombre_apellido_operador="<?= $desembolso['nombre_apellido_operador'] ?>"
                            respuesta="<?= $desembolso['respuesta'] ?>"
                            comprobante="<?= $desembolso['comprobante'] ?>"
                        >
                            <i class="fa fa-check"></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- Modal Formulario Validar Desembolso -->
<div class="modal fade" id="modalValidarDesembolso" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" style="width: 80%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="myModalLabel">Validar desembolso</h3>
            </div>
            <div class="modal-body">
                <div style="background-color: #ECE7E7;">
                    <h4>Cliente:</h4>
                </div>
                <div class="row">
                    <label id="id_desembolso" style="display:none;"></label>
                    <div class="col-md-3">
                        <h5>Documento: <strong><label id="documento"></label></h5></strong>
                    </div>
                    <div class="col-md-3">
                        <h5>Nombre: <strong><label id="nombre_apellido"></label></h5></strong>
                    </div>
                    <div class="col-md-3">
                        <h5>Solicitud N°: <strong><label id="id_solicitud"></label></h5></strong>
                    </div>
                    <div class="col-md-3">
                        <h5>Fecha alta: <strong><label id="fecha_alta"></label></h5></strong>
                    </div>
                </div>
                <div style="background-color: #ECE7E7;">
                    <h4>Validación solicitada por:</h4>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <h5>Operador: <strong><label id="operador"></label></h5></strong>
                    </div>
                    <div class="col-md-4">
                        <h5>Fecha solicitud: <strong><label id="fecha_hora_solicitud"></label></h5></strong>
                    </div>
                </div>
                <div style="background-color: #ECE7E7;">
                    <h4>Datos Desembolso:</h4>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h5>Estado: <strong><label id="pagado"></label></h5></strong>
                    </div>
                    <div class="col-md-3">
                        <h5>Banco desembolso: <strong><label id="origen_pago"></label></h5></strong>
                    </div>
                    <div class="col-md-3">
                        <h5>Fecha procesado: <strong><label id="fecha_procesado"></label></h5></strong>
                    </div>
                    <div class="col-md-3">
                        <h5>TXT: <strong><small><label id="ruta_txt"></label></small></h5></strong>
                    </div>
                </div>
                <div class="bg-success">
                    <h4>Resultado:</h4>
                </div>
                <form method="post" name="frmValidar" id="frmValidar">
                    <div class="row">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="selectRespuesta">Respuesta:</label>
                                <select class="form-control" id="selectRespuesta" name="selectRespuesta" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="EN PROCESO">EN PROCESO</option>
                                    <option value="CONFIRMADA">CONFIRMADA</option>
                                    <option value="RECHAZADA">RECHAZADA</option>
                                    <option value="NO PROCESADO">NO PROCESADO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="selectMotivo">Motivo:</label>
                                <select class="form-control" id="selectMotivo" name="selectRespuesta">
                                    <option value="">Seleccionar...</option>
                                    <option value="Cuenta inválida">Cuenta inválida</option>
                                    <option value="Cuenta no existe">Cuenta no existe</option>
                                    <option value="Cuenta cancelada/cerrada">Cuenta cancelada/cerrada</option>
                                    <option value="Identificación incorrecta">Identificación incorrecta</option>
                                    <option value="Cuenta no habilitada">Cuenta no habilitada</option>
                                    <option value="Cuenta inactiva o bloqueada">Cuenta inactiva o bloqueada</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <div id="subirComprobante">
                                    <label for="file">Comprobante Respuesta</label>
                                    <input type="file" id="file" name="file" required="false" accept="image/*,.pdf">
                                    <p class="help-block">Formatos permitidos: jpg | png | jpeg | pdf</p>
                                </div>
                                <div id="mostarComprobante">
                                    <label for="">Comprobante Respuesta</label><br>
                                    <a class="" target="_blank" id="urlComprobante"></a>
                                </div>
                                <div id="noComprobante">
                                    <label for="">Comprobante Respuesta</label><br>
                                    No tiene
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-danger text-center">
                        <b><p id="pError"></p></b>
                    </div>
                    <div class="row text-center">
                        <a class="btn btn-info" id="aEnvio"><i class="fa fa-send"></i> ENVIAR</a>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script>
	$(document).ready(function(){
        getCantValidarPendientes();
        $("#selectMotivo").attr("disabled", true);
        $('#mostarComprobante').hide();
        $('#noComprobante').hide();

        $("#tp_validar_desembolsos").DataTable();
    });
</script>