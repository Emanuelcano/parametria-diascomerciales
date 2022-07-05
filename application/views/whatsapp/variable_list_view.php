
<div class="variables_container">

<br><br>

    <div class="row">
            <div class="col-md-4 container_id" >
                <div class="col-md-12">
                    <label for="variables_id">id: </label>
                    <input type="text" name="variables_id[]" class="form-control variables_id" value="<?php echo $variable_exist?>">
                </div>
            </div>
            <div class="col-md-6">
                <label for="tipo_variable">Tipo: </label>
                <select class="form-control values_tipo_variable" data-variable_id="<?php echo $variable_exist?>" name="values_tipo_variable[]" style="width: 100%" placeholder="Grupo">
                    <option value="" disabled selected>Seleccione tipo:</option>
                    <option value="1">Dinamica</option>
                    <option value="2">Estatica</option>
                </select>
            </div>
            <div class="col-md-2" >
                <div class="col-md-12">
                    <a href="#" style="margin-top:22px;" class="btn btn-small btn-danger button_delete_variable" data-delete_variable_id="<?php echo $variable_exist?>"><i  class="fa fa-trash-o" aria-hidden="true"></i> </a>
                </div>
            </div>
    </div>

<br>

    <div class="row container_inputs">
        <div class="col-md-4" >
            <div class="col-md-12">
                <label for="campo">Campo: </label>
                <input type="text" name="values_campo[]" id="values_campo<?php echo $variable_exist?>" class="form-control values_campo" value="">
            </div>
        </div>
        <div class="col-md-4 container_condicion">
            <div class="col-md-12">
                <label for="condicion">Condición: </label>
                <input type="text" name="values_condicion[]" id="values_condicion<?php echo $variable_exist?>" class="form-control values_condicion" value="">
            </div>
        </div>
        <div class="col-md-4 container_formato">
            <div class="col-md-12">
                <label for="formato">Formato: </label>
                <input type="text" name="values_formato[]" id="values_formato<?php echo $variable_exist?>" class="form-control values_formato" value="">
            </div>
        </div>
    </div>

</div>