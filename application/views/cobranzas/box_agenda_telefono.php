<style>
table.modificable input[type=text], table.modificable select {
    border: 0px !important;
    border-bottom: 1px solid #ccc !important;
    height: 20px !important;
    padding-bottom: 0px !important;
    padding-top: 0px !important;
}
table.modificable .btn-group-sm>.btn, table.modificable .btn-sm {
    padding: 1px 10px !important;
}

a[data-title]:hover:after {
    opacity: 1;
    transition: all 0.1s ease 0.5s;
    visibility: visible;
}
a[data-title]:after {
    content: attr(data-title);
    background-color: #000000c9;
    color: #f4f4f4;
    position: absolute;
    padding: 7px;
    white-space: nowrap;
    box-shadow: 1px 1px 3px #222222;
    opacity: 0;
    z-index: 1;
    height: 30px;
    visibility: hidden;
    left: 20px;
    bottom: -6px
}
a[data-title] {
    position: relative;
    float: right;
}
</style>
<div id="box_agenda_telf" class="box box-info">
    <div class="box-header with-border" id="titulo">
    </div><!-- end box-header -->
    <div class="box-body" style="font-size: 12px;">
      
        <div class="container-fluid">
            <div class="row">
        
                <div class ="col-sm-12 text-center" style="background-color: #d8d5f9;box-shadow: 0px 9px 10px -9px #888888; z-index: 1;">
                    <h4>DIRECTORIO TELEFÓNICO</h4>
                </div>
                <div class="col-sm-12 cont">
                    <table class="table modificable " id="table-agenda-telefono">
                        <thead>
                            <th></th>
                            <th>Número</th>
                            <th>Codigo/departamento</th>
                            <th>Contacto</th>
                            <th>Fuente</th>
                            <th>Tipo</th>
                            <th>Parentesco</th>
                            <th>Estado</th>
                            <th><a class="btn btn-success btn-sm" onclick="mostrarFormulario('tel')"><i class="fa fa-plus"></i></a></th>
                        </thead>
                        <tbody>

                        

                        <?php foreach ($agenda_telefonica as $key => $value) : ?>
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="ch-tel-<?= $value["id"] ?>" value ="<?= $value["id"] ?>" name="ch-telefonos" data-numero_telefono="<?= $value["numero"] ?>" data-estado="<?= $value["estado"] ?>">
                                    </div>
                                </td>
                                <td class="numero"> <?php echo($value["estado_codigo"] == 1)? $value["codigo"].'/'.$value["numero"] : $value["numero"]?></td>
                                <td> 
                                
                                
                                <a class="codigo" style="font-size: 12px;" data-registro='<?= $value["id"] ?>' <?php echo($value["estado_codigo"] != 1 )? "onclick= 'agregarLocalidadModal(this)'":""?>>
                                    <?php echo($value["codigo"] != "" && $value["departamento"] != "")? $value["codigo"].'/'.$value["departamento"]:"<i class='fa fa-map-marker text-red'></i> Agregar" ?>
                                </a>
                                <?php echo ($value["estado_codigo"] != 1 && $value["codigo"] != "" && $value["departamento"] != "")? '<a data-title="Verificar código" data-registro="'.$value["id"].'" class="verificacion"><i class="fa fa-check-circle text-green" ></i></a>': '' ?>
                                
                                
                                
                                </td>
                                <td>
                                    <div class="form-group" style="margin-bottom: 0px;" >
                                        <input type="text" class="form-control" id="contacto-tel-<?= $value["id"] ?>" value="<?= $value["contacto"]?>">
                                    </div>
                                </td>
                                <td>
                                    <select class="form-control" id="fuente-tel-<?= $value["id"] ?>">
                                        <option value="PERSONAL" <?php echo (strtoupper($value["fuente"]) == "PERSONAL")? 'selected':'' ?>>Personal</option>
                                        <option value="REFERENCIA" <?php echo (strtoupper($value["fuente"]) == "REFERENCIA")? 'selected':'' ?>>Referencia</option>
                                        <option value="LABORAL" <?php echo (strtoupper($value["fuente"]) == "LABORAL")? 'selected':'' ?>>Laboral</option>
                                        <option value="BURO_CELULAR" <?php echo (strtoupper($value["fuente"]) == "BURO_CELULAR")? 'selected':'' ?>>Buro - Celular - D</option>
                                        <option value="BURO_CELULAR_T" <?php echo (strtoupper($value["fuente"]) == "BURO_CELULAR_T")? 'selected':'' ?>>Buro - Celular - T</option>
                                        <option value="BURO_LABORAL" <?php echo (strtoupper($value["fuente"]) == "BURO_LABORAL")? 'selected':'' ?>>Buro - Laboral - D</option>
                                        <option value="BURO_RESIDENCIAL" <?php echo (strtoupper($value["fuente"]) == "BURO_RESIDENCIAL")? 'selected':'' ?>>Buro - Residencial - D</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" id="tipo-tel-<?= $value["id"] ?>">
                                        <option value="" > </option>
                                        <option value="MOVIL" <?php echo (strtoupper($value["tipo"]) == "MOVIL")? 'selected':'' ?> >Movil</option>
                                        <option value="FIJO" <?php echo (strtoupper($value["tipo"]) == "FIJO")? 'selected':'' ?> >Fijo</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" id="parentesco-tel-<?= $value["id"] ?>">
                                        <option value="0" > </option>
                                        <?php foreach ($lista_parentesco as $key2 => $value2):?>
                                            <option value="<?= $value2["id_parentesco"]?>" <?php echo (strtoupper($value["parentesco"]) == strtoupper($value2["Nombre_Parentesco"]))? 'selected':'' ?> ><?= $value2["Nombre_Parentesco"]?></option>
                                        <?php endforeach;?>                                        
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control" id="estado-tel-<?= $value["id"] ?>">
                                        <option value="1" <?php echo ($value["estado"] == 1)? 'selected':'' ?> >Activo</option>
                                        <option value="0" <?php echo ($value["estado"] == 0)? 'selected':'' ?> >Fuera de servicio</option>
                                    </select>
                                </td>
                                <td><a class="btn btn-info btn-sm" onclick="guardarCambio(<?= $value['id'] ?>, 'tel')"><i class="fa fa-save"></i></a></td>
                            </tr>
                        <?php endforeach;?>
                        

                        </tbody>
                    </table>
                        
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("a.verificacion").click(function (event){
            verificarCodigo($(this));
        });
    });
</script>