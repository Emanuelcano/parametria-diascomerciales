<div class="login-box-body" style="height: auto; text-align: left; font-size: 14px;">
  <strong>Crear nuevo Dia Comercial</strong>
  <br>
  <section id="formSection">
    <fieldset>
    <div class="alert alert-danger" id="msg-error" style="display: none"><strong>Importante!  </strong>Corregir los siguientes errores. 
      <div class="list-errors"></div>
      </div>
      <form class="form-horizontal" id="formDatosBasicos">
        <div class="box-body">
          <div class="form-group">
            <input  type="text" id="id" style="display:none " value="<?php if(isset($id)) echo $id ;?>">
         
            <label for="fecha" class="col-sm-1 control-label">Fecha</label>

            <div class="col-sm-5" id="divfecha">
              <input  type="date" style= "width: 150px;" min="<?php $hoy=date("Y-m-d"); echo $hoy;?>"class="form-control" id="fecha"
                       required  value="<?php if(isset($fecha)) echo $fecha?>"
                      >
            </div>
          </div>
          <div class="form-group">
            <label for="descripcion"  class="col-sm-1 control-label">Descripcion</label>

            <div class="col-sm-1">
              <input  type="text" style= "width: 200px;" minlength="1" maxlength="50" class="form-control" id="descripcion"
                      placeholder="Descripcion del evento" required value="<?php if(isset($descripcion)) echo $descripcion?>"
                      >
            </div>

          </div>  
        </div>
        <div class="col-sm-12" style="margin-top: -3px;">
          <div class="box-footer col-sm-6" style="text-align: center;">
            <button type="button" class="btn btn-primary col-sm-6 pull-right" id="btnRegistrarDia"
               onclick="registrarDiaComercial();" style="display: block;">Registrar
            </button>
            <button type="button" class="btn btn-primary col-sm-6 pull-right" id="btnActualizarDia"
               disabled="true" onclick="actualizarDiaComercial();" style="display: none;">Actualizar
            </button>
          </div>
          <div class="box-footer col-sm-6">
            <button type="" class="btn btn-default col-sm-6" onclick="listaDiasComerciales();">Cancelar</button>
          </div>
        </div>
        <!-- /.box-footer -->
      </form>
    </fieldset>
  </section>
</div>