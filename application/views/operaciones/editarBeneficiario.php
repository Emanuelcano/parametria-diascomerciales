<?php   
    if ($data['vista']){
        $disabled = 'disabled';
    } else {
        $disabled = '';
    } 
 /*
    echo "<pre>";
    print_r($data);
    echo "</pre>";
 */   
?>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="editar" value="true">
<div id="dashboard_principal" style="display: block; background: #FFFFFF; height: 4000px">
    <div class="box box-info"  style="background: #E8DAEF;">
        <div class="box-header with-border" id="titulo">
            <h6 class="box-title"><small><strong>Beneficiarios</strong></small></h6>
        </div>
        <main role="main">
            <section id="formSection">
                <div class="login-box-body" style="height: auto; text-align: left;  font-size: 12px; margin-bottom: -10px;">
                    <fieldset>
                        <form class="form-horizontal" id="formDatosBasicos" action="<?php echo base_url()?>api/ApiBeneficiario/actualizarBeneficiario" method="POST">
                            <div class="box-body">

                                <div class="form-group">
                                    <label for="tipoDocumentoFiscal" class="col-sm-1 control-label">Tipo</label>
                                    <div class="col-sm-1" id="divTipoBeneficiario">
                                        <select <?php echo $disabled ?> class="form-control" id="tipoBeneficiario" name="tipoBeneficiario" required>                                               
                                            <?php 
                                            
                                            if ($data["tipo_benef"]): foreach ($data["tipo_benef"] as $tipo): 
                                                if ($data['datos_beneficiario'][0]['id_tipo'] == $tipo->id_tipo_beneficiario){
                                            ?>
                                                     <option selected value="<?php echo $tipo->id_tipo_beneficiario; ?>"><?php echo $tipo->denominacion; ?></option>
                                            <?php          
                                                } else {
                                            ?>
                                                     <option value="<?php echo $tipo->id_tipo_beneficiario; ?>"><?php echo $tipo->denominacion; ?></option>
                                             <?php   } 
                                            endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <label for="tipoDocumentoFiscal" class="col-sm-1 control-label">Rubro</label>
                                    <div class="col-sm-2" id="divTipoRubro">
                                        <select <?php echo $disabled ?> class="form-control" id="rubroBeneficiario" name="rubroBeneficiario" required>
                                            <?php 
                                            if ($data["lista_rubro"]): foreach ($data["lista_rubro"] as $rubro): 
                                                if ($data['datos_beneficiario'][0]['id_rubro'] == $rubro->id_rubro_beneficiario){?>
                                                    <option selected value="<?php echo $rubro->id_rubro_beneficiario; ?>"><?php echo $rubro->denominacion; ?></option>
                                                <?php          
                                                } else {
                                                ?>
                                                    <option value="<?php echo $rubro->id_rubro_beneficiario; ?>"><?php echo $rubro->denominacion; ?></option>
                                            <?php   } 
                                            endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <label for="tipoDocumentoFiscal" class="col-sm-1 control-label">Forma Pago</label>
                                    <div class="col-sm-2" id="divFormaPago">
                                        <select <?php echo $disabled ?> class="form-control" id="formaPago" name="formaPago" required>
                                            <?php 
                                            if ($data["forma_pago"]): foreach ($data["forma_pago"] as $pago):
                                                if ($data['datos_beneficiario'][0]['id_forma_pago'] == $pago->id_forma_pago){?>
                                                    <option selected value="<?php echo $pago->id_forma_pago; ?>"><?php echo $pago->denominacion; ?></option>
                                                <?php          
                                                } else {
                                                ?>
                                                    <option value="<?php echo $pago->id_forma_pago; ?>"><?php echo $pago->denominacion; ?></option>
                                            <?php   } 
                                            endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <label for="moneda" class="col-sm-1 control-label">Moneda
                                    </label>
                                    <div class="col-sm-2" id="divMoneda">
                                        <select <?php echo $disabled ?> class="form-control" id="moneda" name="moneda" required>
                                            <?php if ($data["moneda"]): foreach ($data["moneda"] as $moneda): 
                                                if ($data['datos_beneficiario'][0]['id_tipo_moneda'] == $moneda->id_moneda){?>
                                                    <option selected value="<?php echo $moneda->id_moneda; ?>"><?php echo $moneda->denominacion; ?></option>
                                                   <?php          
                                                } else {
                                                ?>
                                                    <option value="<?php echo $moneda->id_moneda; ?>"><?php echo $moneda->denominacion; ?></option>
                                            <?php   } 
                                            endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tipoDocumentoFiscal" class="col-sm-1 control-label">Documento</label>
                                    <div class="col-sm-1" id="divTipoDocumentoFiscal">
                                        <select <?php echo $disabled ?> class="form-control" id="tipoDocumento" name="tipoDocumento" required>
                                            <?php 
                                            if ($data["tipo_documento"]): foreach ($data["tipo_documento"] as $doc): 
                                                if ($data['datos_beneficiario'][0]['id_tipo_documento'] == $doc->id_tipoDocumento){?>
                                                    <option selected value="<?php echo $doc->id_tipoDocumento; ?>"><?php echo $doc->convencion_tipoDocumento; ?></option>
                                                <?php          
                                                } else {
                                                ?>
                                                    <option value="<?php echo $doc->id_tipoDocumento; ?>"><?php echo $doc->convencion_tipoDocumento; ?></option>
                                                <?php   } 
                                                endforeach;
                                                endif;
                                                ?>
                                        </select>
                                    </div>

                                    <label for="nroDocumentoFiscal" class="col-sm-1 control-label">N&uacute;mero</label>

                                    <div class="col-sm-2">
                                        <input  <?php echo $disabled ?> type="number" class="form-control" id="nroDocumento" name="nroDocumento"
                                                placeholder="Numero de Docuemnto" value="<?php echo $data['datos_beneficiario'][0]['nro_documento']; ?>" required >   
                                    </div>

                                </div>	                            
                                <div class="form-group">
                                    <label for="denominacion" class="col-sm-1 control-label">Denominaci&oacute;n</label>

                                    <div class="col-sm-10">
                                        <input  <?php echo $disabled ?> type="text" class="form-control" id="denominacion" name="denominacion"
                                                placeholder="Nombre del Beneficiario o Razon Social de la empresa proveedora"
                                                autocomplete="off" value="<?php echo $data['datos_beneficiario'][0]['denominacion']; ?>" required>
                                        <div class="contenedor" style=" text-align: left;
                                             margin-top: 5px;
                                             background: #DDDDDD; display: none;"></div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="direccion" class="col-sm-1 control-label">Direcci&oacute;n</label>

                                    <div class="col-sm-10">
                                        <input <?php echo $disabled ?> type="text" class="form-control" placeholder="Direcci&oacute;n Fiscal"  value="<?php echo $data['datos_beneficiario'][0]['direccion']; ?>" id="direccion" name="direccion">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="direccion" class="col-sm-1 control-label"></label>

                                    <div class="col-sm-5">
                                        <input <?php echo $disabled ?> type="text" class="form-control" placeholder="Localidad"  value="<?php echo $data['datos_beneficiario'][0]['localidad']; ?>" id="localidad" name="localidad">
                                    </div>
                                    <div class="col-sm-1">
                                        <input <?php echo $disabled ?> type="text" class="form-control" placeholder="C.P." id="cp" value="<?php echo $data['datos_beneficiario'][0]['cp']; ?>" name="cp">
                                    </div>

                                    <div class="col-sm-4">
                                        <select <?php echo $disabled ?> class="form-control select2"  id="id_provincia" name="id_provincia">
                                            <?php 
                                                if ($data["provincia"]): foreach ($data["provincia"] as $provincia): 
                                                    if ($data['datos_beneficiario'][0]['id_provincia'] == $provincia->id_departamento){?>
                                                        <option selected value="<?php echo $provincia->id_departamento; ?>"><?php echo $provincia->nombre_departamento; ?></option>
                                                    <?php          
                                                    } else {
                                                    ?>
                                                        <option value="<?php echo $provincia->id_departamento; ?>"><?php echo $provincia->nombre_departamento; ?></option>
                                            <?php   } 
                                            endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="telefono" class="col-sm-1 control-label">Tel&eacute;fono(s)</label>

                                    <div class="col-sm-2">
                                        <input <?php echo $disabled ?> type="text" class="form-control" placeholder="Tel&eacute;fono"  value="<?php echo $data['datos_beneficiario'][0]['telefono']; ?>" id="telefono" name="telefono">
                                    </div>
                                    <div class="col-sm-2">
                                        <input <?php echo $disabled ?> type="text" class="form-control" placeholder="Tel&eacute;fono" value="<?php echo $data['datos_beneficiario'][0]['telefono_alt']; ?>" id="telefonoAlt" name="telefonoAlt">
                                    </div>
                                    <label for="email" class="col-sm-2 control-label">Correo Electronico</label>

                                    <div class="col-sm-4">
                                        <input <?php echo $disabled ?> type="text" class="form-control" placeholder="Correo Electronico"  value="<?php echo $data['datos_beneficiario'][0]['email']; ?>" id="email" name="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="banco" class="col-sm-1 control-label">Banco
                                    </label>
                                    <div class="col-sm-3" id="divBanco">
                                        <select <?php echo $disabled ?> class="form-control select2" id="id_banco" name="id_banco">
                                            <?php 
                                            if ($data["banco"]): foreach ($data["banco"] as $banco): 
                                                if ($data['datos_beneficiario'][0]['id_banco'] == $banco->id_Banco){?>
                                                    <option selected value="<?php echo $banco->id_Banco; ?>"><?php echo $banco->Nombre_Banco; ?></option>
                                                <?php          
                                                } else {
                                                ?>
                                                    <option value="<?php echo $banco->id_Banco; ?>"><?php echo $banco->Nombre_Banco; ?></option>
                                                <?php   } 
                                            endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <label for="tipoCuenta" class="col-sm-1 control-label">Tipo Cuenta
                                    </label>
                                    <div class="col-sm-2" id="divTipoBanco">
                                        <select <?php echo $disabled ?> class="form-control"  id="tipoCuenta" name="tipoCuenta">
                                            <?php                                            
                                            if ($data["tipo_cuenta"]): foreach ($data["tipo_cuenta"] as $tipo_cuenta): 
                                                if ($data['datos_beneficiario'][0]['id_tipo_cuenta'] == $tipo_cuenta->id_TipoCuenta){?>
                                                <option selected value="<?php echo $tipo_cuenta->id_TipoCuenta; ?>"><?php echo $tipo_cuenta->Nombre_TipoCuenta; ?></option>
                                                <?php          
                                                } else {
                                                ?>
                                                <option value="<?php echo $tipo_cuenta->id_TipoCuenta; ?>"><?php echo $tipo_cuenta->Nombre_TipoCuenta; ?></option>
                                              <?php   } 
                                            endforeach;
                                            endif;
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cbu" class="col-sm-1 control-label">Nro. de Cuenta</label>

                                    <div class="col-sm-4">
                                        <input  <?php echo $disabled ?> type="number" class="form-control" id="nro_cuenta1" name="nro_cuenta1" placeholder="Nro. de Cuenta" size="24" value="<?php echo $data['datos_beneficiario'][0]['nro_cuenta1']; ?>" >
                                    </div>
                                    <label hidden for="cuenta" class="col-sm-2 control-label">Nro. Cuenta</label>

                                    <div class="col-sm-4">
                                        <input  hidden <?php echo $disabled ?> type="number" class="form-control" id="nro_cuenta2" name="nro_cuenta2" placeholder="Nro. de Cuenta" value="<?php echo $data['datos_beneficiario'][0]['nro_cuenta2']; ?>" >
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
                            <input hidden name="id_beneficiario" id="id_beneficiario" value="<?php echo $data['datos_beneficiario'][0]['id_beneficiario']; ?>">
                            <div class="col-sm-12" style="margin-top: -3px;">
                                <div class="box-footer col-sm-7" style="text-align: center;">
                                    <button <?php echo $disabled ?> type="submit" class="btn btn-primary col-sm-4 pull-right" id="btnConfirmar">
                                        Actualizar
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-footer -->
                        </form>
                    </fieldset>
                </div>
            </section>
        </main>
    </div>
    <div class="row">
        <section class="content">
            <div class="col-md-12" align="center">
                <div class="login-box-body" style="text-align: left; font-size: 12px;">

                    <div class="box-body" style="margin-top: -10px;">
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
                    Swal.fire({
                    title: "Exito!",
                    text: response.message,
                    icon: 'success' });
                        // alert(response.message);
                        cargarBeneficiario(id_beneficiario);
                        $('#tp_Beneficiarios').DataTable().ajax.reload();
                    }

                }
            });        
    });    
    
    
    $('.select2').select2();
</script>


