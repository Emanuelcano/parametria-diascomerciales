<?php //print_r($data['operador']->idoperador)?>
<div class="box box-info" id="abmOperadores"  style="background: #E8DAEF;">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Modificar Datos del Operador</strong></small></h6>
    </div>
    <main role="main">
        <section id="formSection">
            <div class="login-box-body" style="height: auto; text-align: left;  font-size: 12px; margin-bottom: -10px;">
                <fieldset>
                    <form class="form-horizontal" id="formDatosBasicos">
                        <div class="box-body">

                            <div class="form-group" style=" padding-left: 0; padding-right: 1em;">
                                <input type="hidden" id="id_operador" value="<?php echo $data['operador']->idoperador; ?>">
                                <input type="hidden" id="id_usuario" value="<?php echo $data['operador']->id_usuario; ?>">
                                
                                <label for="tipoDocumentoFiscal" class="col-sm-1 control-label" >Documento</label>

                                <div class="col-sm-3">
                                    <input  type="text" minlength="11" maxlength="11" class="form-control" id="nroDocumentoFiscal" 
                                        placeholder="Nro. Documento" required onkeypress="return numeros(event)" readOnly value="<?php echo $data['operador']->documento;?>">
                                </div>

                                <label for="nombre" class="col-sm-1 control-label" >Nombres</label>

                                <div class="col-sm-3">
                                    <input  type="text" class="form-control" id="nombre" value="<?php echo $data['user_auth']['name']; ?>"
                                        placeholder="Nombre del Operador " autocomplete="off"  readOnly>
                                    <div class="contenedor" style=" text-align: left;  margin-top: 5px; background: #DDDDDD; display: none;"></div>
                                </div>
                                <label for="apellido" class="col-sm-1 control-label"> Apellidos</label>

                                <div class="col-sm-3">
                                    <input  type="text" class="form-control" id="apellido" value="<?php echo $data['user_auth']['lastname']; ?>"
                                        placeholder="Apellido del Operador " autocomplete="off" readOnly >
                                    <div class="contenedor" style=" text-align: left;  margin-top: 5px; background: #DDDDDD; display: none;"></div>
                                </div>

                            </div>
                            <div class="col-sm-6" style=" padding-left: 0; padding-right: 1em;">

                            <div class="form-group">

                                <label for="slc_estado" class="col-sm-2 control-label">Estado</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="slc_estado" name="slc_estado" >
                                    
                                            <option value="1" <?php if($data['operador']->estado == '1'){echo "selected"; }?>>Activo</option>
                                            <option value="0" <?php if($data['operador']->estado == '0'){echo "selected"; }?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>

                                <div class="form-group">
                                    <label for="pila" class="col-sm-2 control-label">Nombre de Pila</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="pila" placeholder="Nombre de pila"   value="<?php echo $data['operador']->nombre_pila; ?>" readOnly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="telefono" class="col-sm-2 control-label">Tel&eacute;fono fijo</label>

                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="telefono"   placeholder="Tel&eacute;fono" value="<?php echo $data['operador']->telefono_fijo; ?>" readOnly>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" id="telefono_ext"   placeholder="Extension" value="<?php echo $data['operador']->extension; ?>" readOnly>
                                    </div>
                                </div>
                        
                                <div class="form-group">
                                    <label for="telefono_wapp" class="col-sm-2 control-label">Whatsapp</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="telefono_wapp"   placeholder="Whatsapp" value="<?php echo $data['operador']->wathsapp; ?>" readOnly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-sm-2 control-label">Correo Electronico</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="email"   placeholder="Correo Electronico" value="<?php echo $data['operador']->mail; ?>" readOnly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="slc_tipoOperador" class="col-sm-2 control-label">Tipo
                                        <a
                                            title="Agregar Tipo de Operador" onClick="nuevoTipo()">
                                            <i class="fa fa-plus-square" ></i>
                                        </a>
                                        </label>
                                    <div class="col-sm-10" id="divTipoOperador">
                                        <select class="form-control" id="slc_tipoOperador" name="slc_tipoOperador"  >
                                                <option value="">Elija</option>
                                                <?php foreach ($data['tipos_operador'] as $tipo): ?>
                                                        <option <?php if ($data['operador']->tipo_operador == $tipo['idtipo_operador']) echo 'selected'?>
                                                        value="<?php echo $tipo['idtipo_operador']?>"><?php echo $tipo['descripcion']?></option>
                                                <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="slc_equipo" class="col-sm-2 control-label">Equipo</label>
                                    <div class="col-sm-10" id="divEquipo">
                                        <select class="form-control" id="slc_equipo" name="slc_equipo" >
                                            <option value="">Elija</option>
                                            <option value="ARGENTINA" <?php if ($data['operador']->equipo == "ARGENTINA") echo 'selected'?>>Argentina</option>
                                            <option value="COLOMBIA" <?php if ($data['operador']->equipo == "COLOMBIA") echo 'selected'?>>Colombia</option>
                                            <option value="GENERAL" <?php if ($data['operador']->equipo == "GENERAL") echo 'selected'?>>General</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="verificacion-token" class="col-sm-2 col-xs-12 control-label">Verificacion Login por token</label>

                                    <div class="col-sm-10">
                                        <select class="form-control" id="verificacion-token" name="verificacion-token" >
                                                <option value="1" <?php if($data['operador']->verificion_login == '1'){echo "selected"; }?>>Activo</option>
                                                <option value="0" <?php if($data['operador']->verificion_login == '0'){echo "selected"; }?>>Inactivo</option>
                                        </select>
                                    </div>
                                </div>

                                


                                <div class="form-group">
                                    <label for="user" class="col-sm-2 control-label">Usuario</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="user" readOnly placeholder="Usuario" value="<?php echo $data['user_auth']['user']; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password" class="col-sm-2 col-xs-12 control-label">Clave</label>
                                    <div class="col-sm-8 col-xs-10">
                                        <input type="password" class="form-control" id="password" placeholder="Clave" value="<?php echo $data['user_auth']['password']; ?>">
                                      
                                    </div>
                                    <div class="col-sm-1 col-xs-1" style="margin-left:3% ; padding: 0;">
                                        <a class="btn btn-success" onClick="cambiarClave()" style="border: none;"><i class="fa fa-exchange"></i></a>
                                    </div>
                                    <div class="col-sm-1 col-xs-1" style="padding: 0;margin-left:-2%!important;width: 7%!important;">
                                        <a class="btn btn-success " style="background: orange; border: none;" onClick="habilitarCambiarClave()"><i class="fa fa-unlock"></i></a>
                                    </div>
                                </div> 

                            </div>

                            <div class="col-sm-6" style=" padding-left: 0; padding-right: 1em;">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Imagen</label>
                                    <div class="col-sm-10">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <img src='<?php 
                                        if($data['operador']->avatar != '')
                                        {
                                            echo base_url().$data['operador']->avatar;

                                        } else
                                        {
                                            echo base_url().'public/operadores_imagenes/user.png';
                                        }
                                    ?>' alt="imagen de usuario" id="preview" style="margin: 0 auto; display: block;max-height: 200px;">
                                </div>
                            </div>


                            <div class="col-sm-6" style=" padding: 0;" >
                                <label>Modulos asignados</label>
                                
                                <div id="modulosBoxOperadores" style="width:100%; height:200px;overflow: auto;" >
                                     
                                </div><br>								
                            </div>
            
                        </div>
                        <!-- /.box-body -->
                        <div class="col-sm-12 pull-right" id="divMensajes" style="display: none;">
                            <div class="alert alert-danger" id="divError" style="display: none;">
                                <span id="error"></span>
                            </div>
                            <div class="alert alert-warning" id="divAlerta" style="display: none;">
                                <span id="alerta"></span>
                            </div>
                        </div>

                        <div class="col-sm-12 " style="margin-top: -3px;">
                            <div class="box-footer col-sm-6" style="text-align: center;">
                                <button type="button" class="btn btn-primary col-sm-4 pull-right" id="btnRegistoOperador"
                                     onclick="actualizarOperador();">Actualizar
                                </button>
                            </div>
                            <div class="box-footer col-sm-6">
                                <button type="button" class="btn btn-default col-sm-4" onclick="listaOperadores();">Cancelar</button>
                            </div>
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </fieldset>
            </div>
        </section>
    </main>
</div>