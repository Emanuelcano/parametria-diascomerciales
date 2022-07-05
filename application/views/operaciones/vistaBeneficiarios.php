<?php //echo base_url()."assets/template/dist/img/loading.gif";   ?>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">

<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
    <div class="box box-info"  style="background: #E8DAEF;">
        <div class="box-header with-border" id="titulo">
            <h6 class="box-title"><small><strong>Beneficiarios</strong></small></h6>
        </div>
        <main role="main">
            <section id="formSection">
                <div class="login-box-body" style="height: auto; text-align: left;  font-size: 12px; margin-bottom: -10px;" id="form_new_beneficiario">
                    <fieldset>
                        <form class="form-horizontal" id="formDatosBasicos" action="<?php echo base_url()?>api/ApiBeneficiario/registro_beneficiario" method="POST">
                            <div class="box-body">
                               <div class="form-group">
                                    <label for="tipoBeneficiario" class="col-sm-1 control-label">Tipo
                                        <a onclick="abmTipoBeneficiario()"
                                           title="Agregar Tipo de Beneficiario">
                                            <i class="fa fa-plus-square" ></i>
                                        </a>
                                    </label>
                                    <div class="col-sm-1" id="divTipoBeneficiario">
                                        <select class="form-control" id="tipoBeneficiario" name="tipoBeneficiario" required>
                                            <option disabled  selected value="">Elija</option>                                                    
                                            <?php if ($data["tipo_benef"]): foreach ($data["tipo_benef"] as $tipo): ?>
                                                    <option value="<?php echo $tipo->id_tipo_beneficiario; ?>"><?php echo $tipo->denominacion; ?></option>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <label for="R" class="col-sm-1 control-label">Rubro
                                        <a onclick="abmRubroBeneficiario()"
                                           title="Agregar Rubro">
                                            <i class="fa fa-plus-square" ></i>
                                        </a>
                                    </label>
                                    <div class="col-sm-2" id="divTipoRubro">
                                        <select class="form-control" id="rubroBeneficiario" name="rubroBeneficiario" required>
                                            <option disabled value="" selected>Elija</option>
                                            <?php if ($data["lista_rubro"]): foreach ($data["lista_rubro"] as $rubro): ?>
                                                    <option value="<?php echo $rubro->id_rubro_beneficiario; ?>"><?php echo $rubro->denominacion; ?></option>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <label for="formaPago" class="col-sm-1 control-label">Forma Pago
                                        <a onclick="abmFormaPagoBeneficiario()"
                                           title="Agregar Forma de Pago">
                                            <i class="fa fa-plus-square" ></i>
                                        </a>
                                    </label>
                                    <div class="col-sm-2" id="divFormaPago">
                                        <select class="form-control" id="formaPago" name="formaPago"  required>
                                            <option disabled value="" selected>Elija</option>
                                            <?php if ($data["forma_pago"]): foreach ($data["forma_pago"] as $pago): ?>
                                                    <option value="<?php echo $pago->id_forma_pago; ?>"><?php echo $pago->denominacion; ?></option>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <label for="moneda" class="col-sm-1 control-label">Moneda
                                        <a onclick="abmMoneda()"
                                           title="Agregar Moneda">
                                            <i class="fa fa-plus-square" ></i>
                                        </a>
                                    </label>
                                    <div class="col-sm-2" id="divmoneda">
                                        <select class="form-control" id="moneda" required name="moneda">
                                            <option disabled value="" selected>Elija</option>
                                            <?php if ($data["moneda"]): foreach ($data["moneda"] as $moneda): ?>
                                                    <option value="<?php echo $moneda->id_moneda; ?>"><?php echo $moneda->denominacion; ?></option>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tipoDocumentoFiscal" class="col-sm-1 control-label" >Documento
                                        <a onclick="abmDocumentoBeneficiario()"
                                           title="Agregar Tipo de Documento Fiscal">
                                            <i class="fa fa-plus-square" ></i>
                                        </a>
                                    </label>
                                    <div class="col-sm-1" id="divTipoDocumentoFiscal">
                                        <select class="form-control" id="tipoDocumento" name="tipoDocumento" required>
                                            <option disabled value="" selected>Elija</option>
                                            <?php if ($data["tipo_documento"]): foreach ($data["tipo_documento"] as $doc): ?>
                                                    <option value="<?php echo $doc->id_tipoDocumento; ?>"><?php echo $doc->convencion_tipoDocumento; ?></option>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>

                                    <label for="nroDocumentoFiscal" class="col-sm-1 control-label">N&uacute;mero</label>

                                    <div class="col-sm-2">
                                        <input  type="number" class="form-control" id="nroDocumento" name="nroDocumento"
                                                placeholder="Nro. Doc. Fiscal" required >   
                                    </div>

                                </div>	                            
                                <div class="form-group">
                                    <label for="denominacion" class="col-sm-1 control-label">Denominaci&oacute;n</label>

                                    <div class="col-sm-10">
                                        <input  type="text" class="form-control" id="denominacion" name="denominacion"
                                                placeholder="Nombre del Beneficiario o Razon Social de la empresa proveedora"
                                                autocomplete="off" required>
                                        <div class="contenedor" style=" text-align: left;
                                             margin-top: 5px;
                                             background: #DDDDDD; display: none;"></div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="direccion" class="col-sm-1 control-label">Direcci&oacute;n</label>

                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Direcci&oacute;n Fiscal"  id="direccion" name="direccion">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="direccion" class="col-sm-1 control-label"></label>

                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="Localidad"  id="localidad" name="localidad">
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="text" class="form-control" placeholder="C.P." id="cp" name="cp">
                                    </div>

                                    <div class="col-sm-4">
                                        <select class="form-control select2"  id="id_provincia" name="id_provincia">
                                            <option disabled selected>Elija</option>s
                                            <?php if ($data["provincia"]): foreach ($data["provincia"] as $provincia): ?>
                                                    <option value="<?php echo $provincia->id_departamento; ?>"><?php echo $provincia->nombre_departamento; ?></option>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="telefono" class="col-sm-1 control-label">Tel&eacute;fono(s)</label>

                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" placeholder="Tel&eacute;fono"  id="telefono" name="telefono">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" placeholder="Tel&eacute;fono Alternativo"  id="telefonoAlt" name="telefonoAlt">
                                    </div>
                                    <label for="email" class="col-sm-2 control-label">Correo Electronico</label>

                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Correo Electronico"  id="email" name="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="banco" class="col-sm-1 control-label">Banco
                                    </label>
                                    <div class="col-sm-3" id="divBanco">
                                        <select class="form-control select2" id="id_banco" name="id_banco">
                                            <option disabled selected>Elija Banco</option>
                                            <?php if ($data["banco"]): foreach ($data["banco"] as $banco): ?>
                                                    <option value="<?php echo $banco->id_Banco; ?>"><?php echo $banco->Nombre_Banco; ?></option>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <label for="tipoCuenta" class="col-sm-1 control-label">Tipo Cuenta
                                    </label>
                                    <div class="col-sm-2" id="divTipoBanco">
                                        <select class="form-control"  id="tipoCuenta" name="tipoCuenta">
                                            <option disabled selected>Elija Tipo Cuenta</option>
                                            <?php if ($data["tipo_cuenta"]): foreach ($data["tipo_cuenta"] as $tipo_cuenta): ?>
                                                    <option value="<?php echo $tipo_cuenta->id_TipoCuenta; ?>"><?php echo $tipo_cuenta->Nombre_TipoCuenta; ?></option>
                                                    <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cbu" class="col-sm-1 control-label">Nro. de Cuenta</label>
                                    <div class="col-sm-4">
                                        <input  type="number" class="form-control" id="nro_cuenta1" name="nro_cuenta1" placeholder="Nro. de Cuenta 1"  maxlength="20">
                                    </div>
                                    <label for="cuenta" class="col-sm-2 control-label" hidden>Nro. Cuenta</label>

                                    <div class="col-sm-4" hidden>
                                        <input  type="number" class="form-control" id="nro_cuenta2" name="nro_cuenta2" placeholder="Nro de Cuenta 2" name="nroCuenta">
                                    </div>
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
                              		<button type="submit" class="btn btn-primary col-sm-4 pull-right" id="btnConfirmar"
                                 		style="display: block;">Registrar
                              		</button>
                            	</div>
                            	<div class="box-footer col-sm-6">
                              		<button type="button" class="btn btn-default col-sm-4" onclick="limpiarFormulario2();">Cancelar</button>
                            	</div>
                          	</div>
                            <!-- /.box-footer -->
                        </form>
                    </fieldset>
                    <div class="modal fade" id="mostrartipo" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>Agregar Tipo de Beneficiario</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="agregado_tipo" class="col-sm-5 control-label">Descripcion de Beneficiario:</label>

                                        <div class="col-sm-7">
                                            <input  type="text" class="form-control" id="den_ben"
                                                    placeholder="Denominacion"
                                                    autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="modal-foote" style="text-align: center">
                                    <a href="#" data-dismiss="modal" class="btn btn-info" onclick="guardarTipoBeneficiario()">Guardar</a>
                                    <a href="#" data-dismiss="modal" class="btn btn-default">Cerrar</a>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="mostrarrubro" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>Agregar Rubro de Beneficiario</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="rubro_agregado" class="col-sm-5 control-label">Descripcion de Rubro:</label>

                                        <div class="col-sm-7">
                                            <input  type="text" class="form-control" id="den_rub"
                                                    placeholder="Denominacion"
                                                    autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="modal-foote" style="text-align: center">
                                    <a href="#" data-dismiss="modal" class="btn btn-info" onclick="guardarRubroBeneficiario()">Guardar</a>
                                    <a href="#" data-dismiss="modal" class="btn btn-default">Cerrar</a>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="mostrarfomapago" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>Agregar Forma de Pago</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="rubro_agregado" class="col-sm-5 control-label">Descripcion:</label>

                                        <div class="col-sm-7">
                                            <input  type="text" class="form-control" id="den_fp"
                                                    placeholder="Denominacion"
                                                    autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="modal-foote" style="text-align: center">
                                    <a href="#" data-dismiss="modal" class="btn btn-info" onclick="guardarFormaPago()">Guardar</a>
                                    <a href="#" data-dismiss="modal" class="btn btn-default">Cerrar</a>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>     
                    <div class="modal fade" id="mostrarmoneda" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>Agregar Tipo de Moneda</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="moneda" class="col-sm-5 control-label">Descripcion:</label>

                                        <div class="col-sm-7">
                                            <input  type="text" class="form-control" id="den_moneda"
                                                    placeholder="Descripcion"
                                                    autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="modal-foote" style="text-align: center">
                                    <a href="#" data-dismiss="modal" class="btn btn-info" onclick="guardarMoneda()">Guardar</a>
                                    <a href="#" data-dismiss="modal" class="btn btn-default">Cerrar</a>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>     
                    <div class="modal fade" id="mostrardocumento" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>Agregar Tipo de Documento</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="tipo_agregado" class="col-sm-5 control-label">Nombre:</label>

                                        <div class="col-sm-7">
                                            <input  type="text" class="form-control" id="nombre"
                                                    placeholder="Nombre de Documento"
                                                    autocomplete="off">
                                        </div>
                                        <label for="tipo_agregado" class="col-sm-5 control-label">Convencion:</label>

                                        <div class="col-sm-7">
                                            <input  type="text" class="form-control" id="convencion"
                                                    placeholder="Convencion"
                                                    autocomplete="off">
                                        </div>
                                        <label for="tipo_agregado" class="col-sm-5 control-label">Codigo:</label>

                                        <div class="col-sm-7">
                                            <input  type="text" class="form-control" id="cod"
                                                    placeholder="Codigo de Docuemento"
                                                    autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="modal-foote" style="text-align: center;margin-top: 70px;">
                                    <a href="#" data-dismiss="modal" class="btn btn-info" onclick="guardarTipoDocumento()">Guardar</a>
                                    <a href="#" data-dismiss="modal" class="btn btn-default">Cerrar</a>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <div class="row" id="table_beneficiario">
        <section class="content">
                <div class="col-lg-12 text-right" style="display: block; padding-bottom:0px;">
				    <a class="btn btn-success" title="Registrar Beneficiario" onclick="nuevoBeneficiario()"><i class="fa fa-user-plus"></i> Nuevo Beneficiario</a>
				</div>            
                <div class="col-md-12" align="center">
                <div class="login-box-body" style="text-align: left; font-size: 12px;">

                    <div class="box-body" style="margin-top: -17px;">
                        <div class="row">
                            <div class="col-lg-12" id="cuerpoBeneficiarios" style="display: block">
                                <?= $this->load->view('operaciones/tabla_beneficiarios', null, true); ?>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>
    </div>
</div>
<script type="text/javascript">
    
      $('#formDatosBasicos').submit(function (event){
        
        event.preventDefault();
        var formData= new FormData($("#formDatosBasicos")[0]);
            $.ajax({
                url:$('#formDatosBasicos').attr('action'),
                type:$('#formDatosBasicos').attr('method'),
                data:formData,
                cache: false,
                contentType: false,
                processData:false,
                beforeSend : function(){
                    disabledButtons(true);
                },
                complete : function(){
                    disabledButtons(false);  
                },
                success:function(response){                    
                    if (response.errors){
                        Swal.fire({
                            title: "ups!",
                            text: response.errors,
                            icon: 'error'
                        });
                        // for (var i in response.errors) {
                        //      alert(response.errors[i]);
                        //      break;
                        // }
                    } else {
                        var cantidad = $('#cantidad_beneficiario').text();
                        var nueva_cantidad = parseInt(cantidad) + 1;
                        Swal.fire({
                            title: "Exito!",
                            text: response.message,
                            icon: 'success' 
                        });                      
                        // alert(response.message);
                        limpiarFormulario2();
                        $('#tp_Beneficiarios').DataTable().ajax.reload();
                        $('#cantidad_beneficiario').text(nueva_cantidad);
                    }
                }
            });

        
    });
    
    function limpiarFormulario2() {
        $("#formSection").css("display", "none");
        $("#table_beneficiario").css("display", "block");
    }
    
    $('.select2').select2();
    $('#formSection').css("display", "none");
</script>


