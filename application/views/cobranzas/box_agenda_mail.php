<div id="box_agenda_mail" class="box box-info">
    <div class="box-header with-border" id="titulo">
    </div><!-- end box-header -->
    <div class="box-body" style="font-size: 12px;">
        
        <div class="container-fluid">
            <div class="row">
        
                <div class ="col-sm-12 text-center" style="background-color: #d8d5f9;box-shadow: 0px 9px 10px -9px #888888; z-index: 1;">
                    <h4>DIRECTORIO CORREO</h4>
                </div>
                <div class="col-sm-12 cont">
                    <table class="table modificable" id="table-agenda-mail">
                        <thead>
                            <th></th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th><a class="btn btn-success btn-sm" onclick="mostrarFormulario('mail')"><i class="fa fa-plus"></i></a></th>
                        </thead>
                        <tbody>

                        <?php foreach ($agenda_mail as $key => $value) : ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="ch-mail-<?= $value["id"]?>" value="<?= $value["id"]?>" name="ch-mail" data-cuenta="<?= $value["cuenta"] ?>" data-estado="<?= $value["estado"] ?>" >
                                        <label class="form-check-label" for="ch-mail-<?= $value["id"]?>"></label>
                                    </div>
                                </td>
                                <td><?= $value["cuenta"]?></td>
                                <td>
                                    <select class="form-control" id="estado-mail-<?= $value["id"] ?>">
                                        <option value="1" <?php echo ($value["estado"] == 1)? 'selected':'' ?> >Activo</option>
                                        <option value="0" <?php echo ($value["estado"] == 0)? 'selected':'' ?> >Fuera de servicio</option>
                                    </select>
                                </td>
                                <td><a class="btn btn-info btn-sm" onclick="guardarCambio(<?= $value['id'] ?>, 'mail')"><i class="fa fa-save"></i></a></td>
                            </tr>
                        <?php endforeach;?>
                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
