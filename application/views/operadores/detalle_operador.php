<div class="box box-info" id="abmOperadores"  style="background: #E8DAEF;">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Datos del Operador</strong></small></h6>
    </div>
    <main role="main">
        <section id="formSection">
            <div class="login-box-body" style="height: auto; text-align: left;  font-size: 12px; margin-bottom: -10px;">
                <fieldset>
                    <form class="form-horizontal" id="formDatosBasicos">
                        <div class="box-body">

                            <div class="form-group">
                                <label for="tipoDocumentoFiscal" class="col-sm-1 control-label">Documento</label>

                                <div class="col-sm-2">
                                    <input  type="text" minlength="11" maxlength="11" class="form-control" id="nroDocumentoFiscal" readOnly
                                        placeholder="Nro. Documento" required onkeypress="return numeros(event)">
                                </div>

                                <label for="denominacion" class="col-sm-1 control-label">Nombres y Apellidos</label>

                                <div class="col-sm-8">
                                    <input  type="text" class="form-control" id="denominacion" value="<?php echo $data['operador']->nombre_apellido; ?>"
                                        placeholder="Nombre del Operador " autocomplete="off" readOnly>
                                    <div class="contenedor" style=" text-align: left;  margin-top: 5px; background: #DDDDDD; display: none;"></div>
                                </div>

                            </div>
                            <div class="col-sm-6" style=";padding-left: 0;">

                                <div class="form-group">
                                    <label for="estado" class="col-sm-2 control-label">Estado</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="estado" placeholder="Nombre de pila" readOnly value="<?php 
                                            if($data['operador']->estado == '1'){echo "Activo"; }
                                            else { echo "Inactivo"; } 
                                        ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="nombre-pila" class="col-sm-2 control-label">Nombre de Pila</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="nombre-pila" placeholder="Nombre de pila" readOnly value="<?php echo $data['operador']->nombre_pila; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="telefono" class="col-sm-2 control-label">Tel&eacute;fono fijo</label>

                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="telefono" readOnly placeholder="Tel&eacute;fono" value="<?php echo $data['operador']->telefono_fijo; ?>">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" id="telefono_ext" readOnly placeholder="Extension" value="<?php echo $data['operador']->extension; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="telefono" class="col-sm-2 control-label">Tel&eacute;fono Celular</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="telefono_cel" readOnly placeholder="Tel&eacute;fono Celular" value="<?php echo $data['operador']->wathsapp; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="telefono" class="col-sm-2 control-label">Whatsapp</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="telefono_wapp" readOnly placeholder="Whatsapp" value="<?php echo $data['operador']->wathsapp; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="col-sm-2 control-label">Correo Electronico</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="email" readOnly placeholder="Correo Electronico" value="<?php echo $data['operador']->mail; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tipoDocumentoFiscal" class="col-sm-2 control-label">Tipo
                                        <a
                                            title="Agregar Tipo de Operador">
                                            <i class="fa fa-plus-square" ></i>
                                        </a>
                                        </label>
                                    <div class="col-sm-10" id="divTipoOperador">
                                        <select class="form-control" id="slc_tipoOperador" name="slc_tipoOperador" disabled>
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
                                        <select class="form-control" id="slc_equipo" name="slc_equipo" disabled>
                                            <option value="<?php echo $data['operador']->equipo;?>"><?php echo $data['operador']->equipo;?></option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="verificacion-token" class="col-sm-2 col-xs-12 control-label">Verificacion Login por token</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="verificacion-token" placeholder="verificacion por token" readOnly value="<?php 
                                            if($data['operador']->verificion_login == '1'){echo "Activo"; }
                                            else { echo "Inactivo"; } 
                                        ?>">
                                
                                    </div>
                                </div>
                                <?php if ($data['operador']->tipo_operador == '11') { ?>
                                  
                                    <div class="form-group">
                                        <label for="automaticas" class="col-sm-2 col-xs-12 control-label">Gestion de solicitudes automaticas</label>

                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="automaticas" placeholder="automaticas" readOnly value="<?php 
                                                if($data['operador']->automaticas == '1'){echo "Activo"; }
                                                else { echo "Inactivo"; } 
                                            ?>">
                                    
                                        </div>
                                    </div> 
                                <?php } ?>
                                <div class="form-group">
                                    <label for="user" class="col-sm-2 control-label">Usuario</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="user" readOnly placeholder="Usuario" value="<?php echo $data['user_auth']['user']; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="col-sm-2 control-label">Clave</label>

                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="password" placeholder="Clave" value="<?php echo $data['user_auth']['password']; ?>" readOnly>
                                        <a style="position: absolute;top: 10px;right: 25px;font-size: medium;color: #605ca8;"><i class="fa fa-eye" id="show" onclick="show()" ></i></a>
                                    </div>
                                </div> 
                                
                                
                            </div>
                            <div class="col-sm-6" style = "padding-right: 0;">
                                
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Imagen</label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control" id="imagen" readOnly disabled placeholder="Exento" accept=".jpg,.png,.jpeg" onchange="verificarExtensionImagen(this)">
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
                        </div>
                        <div class="col-sm-12" style="margin-top: -3px;">
                                <button type="button" class="btn btn-default" style="width: 30%; margin:0 auto; display:block;" onclick="listaOperadores();">Regresar</button>
                        </div>
                    </form>
                </fieldset>
            </div>
        </section>
    </main>
</div>
