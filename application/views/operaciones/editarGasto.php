<?php
/*
  echo "<pre>";
  print_r($data);
  echo "</pre>";
  die();
*/

if ($data['vista']) {
    $disabled = 'disabled';
} else {
    $disabled = '';
}
?>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">

<?php //echo "<pre>"; print_r($data); echo "</pre>";  ?>
<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
    <div class="box box-info"  style="background: #E8DAEF;">
        <div class="box-header with-border" id="titulo">
            <h6 class="box-title"><small><strong>Autorización de Gastos</strong></small></h6>
        </div>
        <main role="main">
            <section id="formSection">
                <div class="login-box-body" style="height: auto; text-align: left;  font-size: 12px; margin-bottom: -10px;">
                    <fieldset>
                        <form class="form-horizontal" id="formDatosBasicos" action="<?php echo base_url()?>api/ApiGastos/actualizar_gasto" method="POST">
                            <input type="hidden" id="editar" value="true">
                            <input type="hidden" id="id_gasto" name="id_gasto" value="<?php echo $data['cargar_gasto'][0]['id_gasto']?>">
                            
                            <div class="box-body">
                                    <div class="form-group">
                                        <label for="id_empresa" class="col-sm-1 control-label">EMPRESA		    		                               
	                              	</label>
	                              	<div class="col-sm-6" id="dividEmpresa">
                                            <select <?php echo $disabled ?> class="form-control" id="id_empresa" name="id_empresa" required>
                                                <option disabled selected>Elija</option>
                                                <?php if ($data["id_empresa"]): foreach ($data["id_empresa"] as $empresa): 
                                                            if ($data['cargar_gasto'][0]['id_empresa'] == $empresa->id_empresa) {
                                                        ?>
                                                       <option selected value="<?php echo $empresa->id_empresa; ?>"><?php echo $empresa->denominacion; ?></option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <option value="<?php echo $empresa->id_empresa; ?>"><?php echo $empresa->denominacion; ?></option>
                                                        <?php
                                                    } 
                                                endforeach;
                                                endif;
                                                ?>
                                            </select>  
	                              	</div>  
                                        <label for="id_empresa" class="col-sm-1 control-label">ESTADO		                               
	                              	</label>
                                        <?php
                                        $estado_id = $data['cargar_gasto'][0]['estado'];
                                        $estado = "";
                                        if ($estado_id == 1){
                                            $estado = "PENDIENTE";
                                        } else if ($estado_id == 2){
                                            $estado = "ANULADO";
                                        } else if ($estado_id == 3){
                                            $estado = "APROBADO";
                                        } else if ($estado_id == 4){
                                            $estado = "RECHAZADO";
                                        } else if ($estado_id == 5){
                                            $estado = "PAGADO";
                                        } else if ($estado_id == 6){
                                            $estado = "NO PAGADO";
                                        }
                                        
                                        ?>
	                              	<div class="col-sm-4" id="divestadogasto">
                                            <input  type="text" class="form-control" id="estado_gasto" value="<?php echo $estado ?>" disabled>
	                              	</div>
                                    </div> 
                                <div class="form-group">
                                    <label for="tipoGasto" class="col-sm-1 control-label">Tipo</label>
                                    <div class="col-sm-2" id="divTipoGasto">
                                        <select <?php echo $disabled ?> class="form-control" id="tipo_gasto" name="id_tipo" required>
                                            <option value="" disabled selected>Elija</option>
                                            <?php
                                            if ($data["tipo_gasto"]): foreach ($data["tipo_gasto"] as $tipo):
                                                    if ($data['cargar_gasto'][0]['id_tipo'] == $tipo->id_tipo_gasto) {
                                                        ?>
                                                        <option selected value="<?php echo $tipo->id_tipo_gasto; ?>"><?php echo $tipo->denominacion; ?></option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <option value="<?php echo $tipo->id_tipo_gasto; ?>"><?php echo $tipo->denominacion; ?></option>
                                                        <?php
                                                    }
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>  
                                    </div>
                                    <label for="tipoDocumentoFiscal" class="col-sm-1 control-label">Clase</label>
                                    <div class="col-sm-2" id="divClase">
                                        <select <?php echo $disabled ?> class="form-control" onchange="getDescripcion();" id="clase_gasto" name="clase_gasto" required>
                                            <option value="0"  disabled selected>Elija</option>
                                            <?php
                                            if ($data["clase_gasto"]): foreach ($data["clase_gasto"] as $clase):
                                                    if ($data['cargar_gasto'][0]['id_clase'] == $clase->id_clase_gasto) {
                                                        ?>
                                                        <option selected value="<?php echo $clase->id_clase_gasto; ?>"><?php echo $clase->denominacion; ?></option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <option value="<?php echo $clase->id_clase_gasto; ?>"><?php echo $clase->denominacion; ?></option>
                                                        <?php
                                                    }
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>  
                                    </div>
                                    <label for="tipoDocumentoFiscal" class="col-sm-2 control-label">Descripción</label>
                                    <div class="col-sm-4" id="divClase">
                                        <select <?php echo $disabled ?> class="form-control" id="descripcion" name="descripcion" required>
                                            <?php
                                            if ($data["descripcion_gasto"]): foreach ($data["descripcion_gasto"] as $descripcion):
                                                    if ($data['cargar_gasto'][0]['id_descripcion'] == $descripcion->id_descripcion_gasto) {
                                                        ?>
                                                        <option selected value="<?php echo $descripcion->id_descripcion_gasto; ?>"><?php echo $descripcion->denominacion; ?></option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                         <option value="<?php echo $descripcion->id_descripcion_gasto; ?>"><?php echo $descripcion->denominacion; ?></option>
                                                        <?php
                                                    }
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>  
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="tipoDocumentoFiscal" class="col-sm-1 control-label">Beneficiario</label>
                                    <div class="col-sm-11" id="divBeneficiarios">
                                        <select <?php echo $disabled ?>  onchange="detalleBeneficiario()" name="id_beneficiario" class="form-control select2" id="id_beneficiario" required>
                                            <option value="" disabled selected>Elija Beneficiario</option>
                                            <?php
                                            if ($data["beneficiarios"]): foreach ($data["beneficiarios"] as $beneficiario):
                                                    if ($data['cargar_gasto'][0]['id_beneficiario'] == $beneficiario['id_beneficiario']) {
                                                        ?>
                                                        <option selected value="<?php echo $beneficiario['id_beneficiario']; ?>"><?php echo $beneficiario['denominacion']; ?></option>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <option value="<?php echo $beneficiario['id_beneficiario']; ?>"><?php echo $beneficiario['denominacion']; ?></option>

                                                        <?php
                                                    }
                                                endforeach;
                                            endif;
                                            ?>
                                        </select>  
                                    </div>
                                    <label for="detalle_beneficiario" class="col-sm-1 control-label"></label>   
                                    <div class="col-sm-11">
                                        <input id="detalle_beneficiario" class="form-control" disabled value="<?php  echo $data["tipo_documento"][0]['convencion_tipoDocumento']." - ".$beneficiario['nro_documento']." - ".$data["tipo_moneda"][0]['denominacion']; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="denominacion" class="col-sm-1 control-label">Concepto</label>

                                    <div class="col-sm-11">
                                        <input  value="<?php echo $data['cargar_gasto'][0]['concepto']; ?>" <?php echo $disabled ?> type="text" class="form-control" id="concepto"
                                                placeholder="Concepto del gasto" name="concepto"
                                                autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="denominacion" class="col-sm-1 control-label">Nro. Factura</label>

                                    <div class="col-sm-2">
                                        <input  value="<?php echo $data['cargar_gasto'][0]['nro_factura']; ?>" <?php echo $disabled ?> type="text" class="form-control" id="nro_factura"
                                                placeholder="Numero de Factura" name="nro_factura" required
                                                autocomplete="off">
                                    </div>
                                    <label for="moneda" class="col-sm-1 control-label" style="margin-left: -35px;">Moneda
                                    </label>
                                    <div class="col-sm-1" id="divmoneda">
                                        <select <?php echo $disabled ?> class="form-control" id="moneda" name="id_tipo_moneda"  style="margin-left: -27px;" required>
                                            <option value="" disabled selected>Elija</option>
                                                <?php if ($data["moneda"]): foreach ($data["moneda"] as $moneda): 
                                                        if ($data['cargar_gasto'][0]['id_tipo_moneda'] == $moneda->id_moneda) {?>
                                                    <option selected value="<?php echo $moneda->id_moneda; ?>"><?php echo $moneda->denominacion; ?></option>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <option value="<?php echo $moneda->id_moneda; ?>"><?php echo $moneda->denominacion; ?></option>
                                                 <?php
                                                } 
                                            endforeach;
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <label for="denominacion" class="col-sm-1 control-label" style="margin-left: -64px;">Fecha de Emisión</label>

                                    <div class="col-sm-2">
                                        <input  required value="<?php echo date('d-m-Y', strtotime($data['cargar_gasto'][0]['fecha_emision'])); ?>" <?php echo $disabled ?>  class="form-control datepicker" id="fecha_factura" name="fecha_factura" autocomplete="off">
                                    </div>
                                    <label for="denominacion" class="col-sm-1 control-label" style="margin-left: -21px;">Fecha de Vencimiento</label>

                                    <div class="col-sm-2">
                                        <input value="<?php echo date('d-m-Y', strtotime($data['cargar_gasto'][0]['fecha_vencimiento']));?>" <?php echo $disabled ?>  class="form-control datepicker_to" id="fecha_vencimiento_factura"  name="fecha_vencimiento_factura" autocomplete="off">
                                    </div>
                                        <label for="FormaPago" class="col-sm-2 control-label" style="margin-left: -10%;">Forma Pago</label>
                                        <div class="col-sm-1" id="divFormaPago">
                                            <select <?php echo $disabled ?> class="form-control" id="forma_pago" name="forma_pago" required>
                                                <option value="" disabled selected>Elija</option>
                                                <?php
                                                if ($data["forma_pago"]): foreach ($data["forma_pago"] as $pago):
                                                        if ($data['cargar_gasto'][0]['id_forma_pago'] == $pago->id_forma_pago) {
                                                            ?>
                                                            <option selected value="<?php echo $pago->id_forma_pago; ?>"><?php echo $pago->denominacion; ?></option>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <option value="<?php echo $pago->id_forma_pago; ?>"><?php echo $pago->denominacion; ?></option>
                                                            <?php
                                                        } endforeach;
                                                endif;
                                                ?>
                                            </select>  
                                        </div>   
                                </div>
                                <?php
                                if($disabled){
                                    $exento      = number_format($data['cargar_gasto'][0]['exento'], 2, ',', '.');
                                    $subtotal    = number_format($data['cargar_gasto'][0]['sub_total'], 2, ',', '.');
                                    $impuesto    = number_format($data['cargar_gasto'][0]['impuesto'], 2, ',', '.');
                                    $impuesto_consumo    = number_format($data['cargar_gasto'][0]['impuesto_consumo'], 2, ',', '.');
                                    $retefuente  = number_format($data['cargar_gasto'][0]['retefuente'], 2, ',', '.');
                                    $reteica     = number_format($data['cargar_gasto'][0]['reteica'], 2, ',', '.');
                                    $descuento   = number_format($data['cargar_gasto'][0]['descuento'], 2, ',', '.');
                                    $total_pagar = number_format($data['cargar_gasto'][0]['total_pagar'], 2, ',', '.');
                                }else{
                                    $exento      = $data['cargar_gasto'][0]['exento'];
                                    $subtotal    = $data['cargar_gasto'][0]['sub_total']; 
                                    $impuesto    = $data['cargar_gasto'][0]['impuesto'];
                                    $impuesto_consumo   = $data['cargar_gasto'][0]['impuesto_consumo'];
                                    $retefuente  = $data['cargar_gasto'][0]['retefuente'];
                                    $reteica     = $data['cargar_gasto'][0]['reteica'];
                                    $descuento   = $data['cargar_gasto'][0]['descuento']; 
                                    $total_pagar = $data['cargar_gasto'][0]['total_pagar'];
                                }
                                
                                ?>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="telefono" class="col-sm-4 control-label">Exento</label>

                                        <div class="col-sm-6">
                                            <input name="exento" value="<?php echo $exento; ?>"   <?php echo $disabled ?> class="form-control calculaTotal" id="exento" placeholder="Exento" style="text-align: right;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="sub_total" class="col-sm-4 control-label">Sub-Total</label>

                                        <div class="col-sm-6">
                                            <input name="sub_total" value="<?php echo $subtotal; ?>" <?php echo $disabled ?> class="form-control calculaTotal" id="sub_total" placeholder="Sub-Total" style="text-align: right;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="impuesto" class="col-sm-4 control-label">Impuesto</label>

                                        <div class="col-sm-6">
                                            <input name="impuesto" value="<?php echo $impuesto; ?>" <?php echo $disabled ?> class="form-control calculaTotal" id="impuesto" placeholder="Impuesto" style="text-align: right;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="impuesto_consumo" class="col-sm-4 control-label">Impuesto Consumo</label>

                                        <div class="col-sm-6">
                                            <input name="impuesto_consumo" value="<?php echo $impuesto_consumo; ?>" <?php echo $disabled ?> class="form-control calculaTotal" id="impuesto_consumo" placeholder="Impuesto Consumo" style="text-align: right;">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="retefuente" class="col-sm-4 control-label">Retefuente</label>

                                        <div class="col-sm-6">
                                          <input value="<?php echo $retefuente; ?>" <?php echo $disabled ?> class="form-control calculaTotal" id="retefuente" name="retefuente" placeholder="Retefuente" style="text-align: right;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reteica" class="col-sm-4 control-label">Reteica</label>

                                        <div class="col-sm-6">
                                          <input value="<?php echo $reteica; ?>" <?php echo $disabled ?>  class="form-control calculaTotal" id="reteica" name="reteica" placeholder="Reteica" style="text-align: right;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="descuento" class="col-sm-4 control-label">Descuento</label>

                                        <div class="col-sm-6">
                                            <input name="descuento" value="<?php echo $descuento; ?>" <?php echo $disabled ?> class="form-control calculaTotal" id="descuento" placeholder="Descuento" style="text-align: right;">
                                        </div>
                                    </div>		                            
                                    <div class="form-group">
                                        <label for="total_pagar" class="col-sm-4 control-label">Total a Pagar</label>
                                        <div class="col-sm-6">
                                            <input readonly class="form-control" id="total_pagar"  name="total_pagar" placeholder="Total a pagar" style="text-align: right;" value="<?php echo $total_pagar; ?>" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">                                                              
                                    <input type="hidden" name="url_archivo" id="url_archivo" value="<?php echo $data['cargar_gasto'][0]['url_archivo'] ?>">
                                        <input type="hidden" name="url_comprobante_pago" id="url_comprobante_pago" value="<?php echo $data['cargar_gasto'][0]['url_comprobante_pago'] ?>">
                                        <label for="file" class="col-sm-2 control-label">Comprobante</label>
                                       <input type="hidden" name="filehidden" id="filehidden">
                                       <input <?php echo $disabled ?> name="file" id="file" type="file" class="form-control file">
                                       <p style="font-size: 20px;">
                                            <a href="<?php echo base_url($data['cargar_gasto'][0]['url_archivo']) ?>" target="_blank">
                                                <i  class="fa fa-search-plus"></i> Ver Comprobante de Gasto
                                            </a>
                                        </p>
                                        <p style="font-size: 20px;" id="view_comprobante_pago" style="display:none;">
                                            <a href="<?php echo base_url($data['cargar_gasto'][0]['url_comprobante_pago']) ?>" target="_blank">
                                                <i  class="fa fa-search-plus"></i> Ver Comprobante de Pago
                                            </a>
                                        </p> 


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
        <!-- Main content -->
        <section class="content">

            <div class="col-lg-12" id="estadosGasto" style="display: block">

                <?= $this->load->view('operaciones/tabla_estados', null, true); ?>

            </div>

        </section>
        <!-- /.content -->
    </div>

    <div class="row">
        <!-- Main content -->
        <section class="content">

            <div class="col-lg-12" id="cuerpoGastos" style="display: block">

                <?= $this->load->view('operaciones/tabla_gastos', null, true); ?>

            </div>

        </section>
        <!-- /.content -->
    </div>

</div>
<script type="text/javascript">   
    
    $(function () {
        $(".datepicker").datepicker({
        onClose: function (selectedDate) {
        $(".datepicker_to").datepicker("option", "minDate", selectedDate);
        }
        });
        $(".datepicker_to").datepicker({
        onClose: function (selectedDate) {
        $(".datepicker").datepicker("option", "maxDate", selectedDate);
        }
        });
    });
    
    $('#formDatosBasicos').submit(function (event){        
        event.preventDefault();
        var id_gasto = $('#id_gasto').val();
        var formData= new FormData($("#formDatosBasicos")[0]);
            $.ajax({
                url:$('#formDatosBasicos').attr('action'),
                type:$('#formDatosBasicos').attr('method'),
                data:formData,
                cache: false,
                contentType: false,
                processData:false,
                success:function(response){
                     if (response.errors){
                           for (var i in response.errors) {
                                alert(response.errors[i]);
                                break;
                            }
                    } else {
                        if (editar){
                            cargarGastos(id_gasto);
                        }                        
                        alert(response.message);
                        limpiarFormulario();
                        $('#tp_Gastos').DataTable().ajax.reload();
                    }
                }
            });        
    });
    
    $('.select2').select2();
    
    var url_archivo = $('#url_archivo').val();
    var base_url = $("input#base_url").val();
    var url_comprobante_pago=$('#url_comprobante_pago').val();
    var extension = url_archivo.substr(-3);
    var extension2 = url_comprobante_pago.substr(-3);
    var estado_gasto =  $('#estado_gasto').val();
    $(".file").fileinput('destroy');

//ver comprobante de pago
if (url_comprobante_pago =="" || url_comprobante_pago == null){
    $('#view_comprobante_pago').css("display","none");
}else{
    $('#view_comprobante_pago').css("display","block");
}


if (estado_gasto == 'APROBADO' || estado_gasto == 'PAGADO' ){
    if (url_archivo=="") {
        fotoparaplug="<img src='"+base_url+"/gastos/no-image-icon.png' class='file-preview-image'>";
    }else{        
        if (extension == "pdf"){
            fotoparaplug="<embed src='"+base_url+url_archivo+"' class='file-preview-image' />";          
            // fotoparaplug="<embed src='"+base_url+url_comprobante_pago+"' class='file-preview-image' />";

        } else {
            fotoparaplug="<img src='"+base_url+url_archivo+"' class='file-preview-image'>";
            // fotoparaplug="<img src='"+base_url+url_comprobante_pago+"' class='file-preview-image' />";
        }  
    }
    if (url_comprobante_pago !="") {  
        if (extension2 == "pdf"){
            fotoparaplug2="<embed src='"+base_url+url_comprobante_pago+"' class='file-preview-image' />";          
            // fotoparaplug="<embed src='"+base_url+url_comprobante_pago+"' class='file-preview-image' />";

        } else {
            fotoparaplug2="<img src='"+base_url+url_comprobante_pago+"' class='file-preview-image'>";
            // fotoparaplug="<img src='"+base_url+url_comprobante_pago+"' class='file-preview-image' />";
        }  
    }

    $(".file").fileinput({
            dropZoneEnabled : false,
            showUpload : false,
            uploadUrl: "<?php base_url();?>",
            uploadAsync: false,
            maxFileCount: 10,
            initialPreview: [
                            fotoparaplug,fotoparaplug2
                            ]   
    });
}else{
    //Carga imagen o pdf o imagen vacia
    if (url_archivo=="") {
        fotoparaplug="<img src='"+base_url+"/gastos/no-image-icon.png' class='file-preview-image'>";
    }else{        
        if (extension == "pdf"){
            fotoparaplug="<embed src='"+base_url+url_archivo+"' class='file-preview-image' />";          
            // fotoparaplug="<embed src='"+base_url+url_comprobante_pago+"' class='file-preview-image' />";

        } else {
            fotoparaplug="<img src='"+base_url+url_archivo+"' class='file-preview-image'>";
            // fotoparaplug="<img src='"+base_url+url_comprobante_pago+"' class='file-preview-image' />";
        }  
    }
    $(".file").fileinput({
            dropZoneEnabled : false,
            showUpload : false,
            uploadUrl: "<?php base_url();?>",
            uploadAsync: false,
            maxFileCount: 10,
            initialPreview: [
                            fotoparaplug
                            ]   
    });
}

    $(".calculaTotal").blur(function(){
        if($("#descuento").val()){
            var descuento = parseFloat($("#descuento").val()); 
        } else {
            var descuento = 0;
        }
        if ($("#impuesto").val()) {
            var impuesto = parseFloat($("#impuesto").val());
        } else {
            var impuesto = 0;
        }
        if ($("#impuesto_consumo").val()) {
            var impuesto_consumo = parseFloat($("#impuesto_consumo").val());
        } else {
            var impuesto_consumo = 0;
        }

        if ($("#reteica").val()) {
            var reteica = parseFloat($("#reteica").val());
        } else {
            var reteica = 0;
        }
        if ($("#retefuente").val()) {
            var retefuente = parseFloat($("#retefuente").val());
        } else {
            var retefuente = 0;
        }
        if ($("#exento").val()) {
            var exento = parseFloat($("#exento").val());
        } else {
            var exento = 0;
        }
        if ($("#sub_total").val()) {
            var sub_total = parseFloat($("#sub_total").val());
        } else {
            var sub_total = 0;
        }
        
        
        var total = exento+sub_total-retefuente-reteica+impuesto+impuesto_consumo-descuento;
        document.getElementById("total_pagar").value = parseFloat(total);
    }); 
    

</script>


