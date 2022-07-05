<div class="box box-info" id="abmOperadores"  style="background: #E8DAEF;">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Registrar Operador</strong></small></h6>
    </div>
    <main role="main">
        <section id="formSection">
            <div class="login-box-body" style="height: auto; text-align: left;  font-size: 12px; margin-bottom: -10px;">
                <fieldset>
                    <form class="form-horizontal" id="formDatosBasicos">
                        <div class="box-body">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="tipoDocumentoFiscal" class="col-sm-1 control-label" >Documento</label>

                                    <div class="col-sm-3">
                                        <input  type="text" minlength="11" maxlength="11" class="form-control" id="nroDocumentoFiscal" 
                                            placeholder="Nro. Documento" required onkeypress="return numeros(event)">
                                    </div>

                                    <label for="nombre" class="col-sm-1 control-label">Nombres</label>

                                    <div class="col-sm-3">
                                        <input  type="text" class="form-control" id="nombre" 
                                            placeholder="Nombre del Operador " autocomplete="off"  >
                                        <div class="contenedor" style=" text-align: left;  margin-top: 5px; background: #DDDDDD; display: none;"></div>
                                    </div>

                                    <label for="apellido" class="col-sm-1 control-label">Apellidos</label>

                                    <div class="col-sm-3">
                                        <input  type="text" class="form-control" id="apellido" 
                                            placeholder="Apellido del Operador " autocomplete="off"  >
                                        <div class="contenedor" style=" text-align: left;  margin-top: 5px; background: #DDDDDD; display: none;"></div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-6">

                                <div class="form-group">

                                        <label for="slc_estado" class="col-sm-2 control-label">Estado</label>
                                        <div class="col-sm-10">
                                            <select class="form-control" id="slc_estado" name="slc_estado" >
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                </div>

                                <div class="form-group">
                                    <label for="pila" class="col-sm-2 control-label">Nombre de Pila</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="pila" placeholder="Nombre de pila" autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="telefono" class="col-sm-2 control-label">Tel&eacute;fono fijo</label>

                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="telefono"   placeholder="Tel&eacute;fono" autocomplete="off" onkeypress="return numeros(event)">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" id="telefono_ext"   placeholder="Extension" autocomplete="off" onkeypress="return numeros(event)">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="telefono_wapp" class="col-sm-2 control-label">Whatsapp</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="telefono_wapp"   placeholder="Whatsapp" autocomplete="off" onkeypress="return numeros(event)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-sm-2 control-label">Correo Electronico</label>

                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="email" placeholder="Correo Electronico" autocomplete="off">
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
                                        <select class="form-control" id="slc_tipoOperador" name="slc_tipoOperador" >
                                                <option value="">Elija</option>
                                                <?php foreach ($data['tipos_operador'] as $tipo): ?>
                                                        <option 
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
                                            <option value="ARGENTINA">Argentina</option>
                                            <option value="COLOMBIA">Colombia</option>
                                            <option value="GENERAL">GENERAL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="verificacion-token" class="col-sm-2 col-xs-12 control-label">Verificacion Login por token</label>

                                    <div class="col-sm-10">
                                        <select class="form-control" id="verificacion-token" name="verificacion-token" >
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                </div>

                                  
                                  <div class="form-group hide automaticas">
                                      <label for="automaticas" class="col-sm-2 col-xs-12 control-label">Gestion de solicitudes automaticas</label>

                                      <div class="col-sm-10">
                                        <select class="form-control" id="automaticas" name="automaticas" >
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                  </div> 

                                <div class="form-group">
                                    <label for="user" class="col-sm-2 control-label">Usuario</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="user"  placeholder="Usuario" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="col-sm-2 control-label">Clave</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="password" placeholder="Clave" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">

                                <div class="form-group">
                                    <label class="col-sm-12">Imagen</label>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input type="file" class="form-control" id="imagen" placeholder="Exento" accept=".jpg,.png,.jpeg" onchange="verificarExtensionImagen(this)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <img src='<?php  echo base_url().'public/operadores_imagenes/user.png'; ?>' 
                                    alt="imagen de usuario" id="preview" 
                                    style="margin: 0 auto; display: block;max-height: 200px;">
                                </div>
                            </div>

                            

                            <div class="col-sm-12">
                                <label>Asignación de modulos</label>
                                <div id="dualSelectExample" style="width:100%; height:200px;"></div><br> 								
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

                        <div class="col-sm-12" style="margin-top: -3px;">
                            <div class="box-footer col-sm-6" style="text-align: center;">
                               
                                <button type="button" class="btn btn-primary col-sm-4 pull-right" id="btnActualizarOperador"
                                     ="true" onclick="registrarOperador();">Registrar
                                </button>
                            </div>
                            <div class="box-footer col-sm-6">
                                <button type="" class="btn btn-default col-sm-4" onclick="listaOperadores();">Cancelar</button>
                            </div>
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </fieldset>
            </div>
        </section>
    </main>
</div>
