<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<?php //echo "<pre>"; print_r($data); echo "</pre>"; ?>
<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
	<div class="box box-info"  style="background: #E8DAEF;">
      	<div class="box-header with-border" id="titulo">
        	<h6 class="box-title"><small><strong>Autorización de Gastos</strong></small></h6>
      	</div>
		<main role="main">
            <section id="formSection">
            	<div class="login-box-body" style="height: auto; text-align: left;  font-size: 12px; margin-bottom: -10px;">
                  	<fieldset>
                        <form class="form-horizontal" id="formDatosBasicos" action="<?php echo base_url()?>api/ApiGastos/registro_gastos" method="POST">
                          	<div class="box-body">
                                    <div class="form-group">
                                        <label for="id_empresa" class="col-sm-1 control-label">EMPRESA</label>
	                              	<div class="col-sm-6" id="dividEmpresa">
                                            <select class="form-control" id="id_empresa" name="id_empresa" required>
                                                <option disabled selected value="">Elija</option>
                                                <?php if ($data["id_empresa"]): foreach ($data["id_empresa"] as $empresa): ?>
                                                        <option value="<?php echo $empresa->id_empresa; ?>"><?php echo $empresa->denominacion; ?></option>
                                                <?php endforeach;
                                                endif;
                                                ?>
                                            </select>  
	                              	</div>
                                        <label for="id_empresa" class="col-sm-1 control-label">ESTADO		                               
	                              	</label>
	                              	<div class="col-sm-4" id="divestadogasto">
                                            <input  type="text" class="form-control" id="estado_gasto" value="INGRESANDO" disabled>
	                              	</div>
                                    </div>                                    
	                            <div class="form-group">                                        
	                            	<label for="tipoGasto" class="col-sm-1 control-label">Tipo
		                                <a onclick="abmTipoGasto()"
		                                  title="Agregar Tipo de Gasto">
		                                  <i class="fa fa-plus-square" ></i>
		                                </a>
	                              	</label>
	                              	<div class="col-sm-2" id="divTipoGasto">
                                            <select class="form-control" id="id_tipo" name="id_tipo" required>
                                                <option disabled selected>Elija</option>
                                                <?php if ($data["tipo_gasto"]): foreach ($data["tipo_gasto"] as $tipo): ?>
                                                        <option value="<?php echo $tipo->id_tipo_gasto; ?>"><?php echo $tipo->denominacion; ?></option>
                                                <?php endforeach;
                                                endif;
                                                ?>
                                            </select>  
	                              	</div>
                                        
	                              	<label for="Clase" class="col-sm-1 control-label">Clase
		                                <a onclick="abmClase()"
		                                  title="Agregar Clase">
		                                  <i class="fa fa-plus-square" ></i>
		                                </a>
	                              	</label>
	                              	<div class="col-sm-2" id="divClase">
                                            <select class="form-control" id="clase_gasto" name="clase_gasto" onchange="getDescripcion()" required>
                                                <option value="" disabled selected>Elija</option>
                                                <?php if ($data["clase_gasto"]): foreach ($data["clase_gasto"] as $clase): ?>
                                                        <option value="<?php echo $clase->id_clase_gasto; ?>"><?php echo $clase->denominacion; ?></option>
                                                <?php endforeach;
                                                endif;
                                                ?>
                                            </select>  
	                              	</div>
	                              	<label for="Descripcion" class="col-sm-2 control-label">Descripción
		                                <a onclick="abmDescripcion()"
		                                  title="Agregar Descripcion">
		                                  <i class="fa fa-plus-square" ></i>
		                                </a>
	                              	</label>
	                              	<div class="col-sm-4" id="divClase">
                                            <select class="form-control" id="descripcion" name="descripcion" required>
                                                <option value=""  selected>Elija</option>
                                                <?php if ($data["descripcion_gasto"]): foreach ($data["descripcion_gasto"] as $descripcion_gasto): ?>
                                                        <option style="display: none" value="<?php echo $descripcion_gasto->id_descripcion_gasto; ?>" id_clase="<?php echo $descripcion_gasto->id_clase_gasto; ?>"><?php echo $descripcion_gasto->denominacion; ?></option>
                                                <?php endforeach;
                                                endif;
                                                ?>
                                            </select>  
	                              	</div>
	                            </div>
	                            <div class="form-group">
	                              <label for="Beneficiario" class="col-sm-1 control-label">Beneficiario</label>
	                              <div class="col-sm-11" id="divBeneficiarios">
                                        <select class="form-control select2" id="id_beneficiario"  name="id_beneficiario" onchange="detalleBeneficiario()" required>
                                            <option value="" disabled selected>Elija Beneficiario</option>
                                            <?php if ($data["beneficiario"]): foreach ($data["beneficiario"] as $beneficiario): ?>
                                                    <option value="<?php echo $beneficiario['id_beneficiario']; ?>"><?php echo $beneficiario['denominacion']; ?></option>
                                            <?php endforeach;
                                            endif;
                                            ?>
                                        </select>  
	                              </div>
	                           
                                        <label for="detalle_beneficiario" class="col-sm-1 control-label"></label>
                                      
                                        <div class="col-sm-11">
                                          <input id="detalle_beneficiario" class="form-control" disabled>
                                        </div>
                                    </div>
	                            <div class="form-group">
	                              <label for="Concepto" class="col-sm-1 control-label">Concepto</label>
                                      
	                              <div class="col-sm-11">
	                                <input  type="text" class="form-control" id="concepto" name="concepto" 
	                                        placeholder="Concepto del gasto"
	                                        autocomplete="off">
	                              </div>
	                            </div>
	                            <div class="form-group">
                                        <label for="Factura" class="col-sm-1 control-label">Nro. Factura</label>

                                    <div class="col-sm-2">
                                        <input  type="text" class="form-control" id="nro_factura" onchange="existeFactura()" value="" name="nro_factura"
                                                placeholder="Nro de Factura" required
                                                autocomplete="off">
                                    </div>                                      
                                        <label for="moneda" class="col-sm-1 control-label" style="margin-left: -35px;">Moneda
                                            <a onclick="abmMoneda()"
                                               title="Agregar Moneda">
                                               <i class="fa fa-plus-square" ></i>
                                            </a>
                                        </label>
                                        <div class="col-sm-1" id="divmoneda">
                                            <select class="form-control" id="moneda" required name="id_tipo_moneda" style="margin-left: -27px;">
                                                <option value="" disabled selected>Elija</option>
                                                    <?php if ($data["moneda"]): foreach ($data["moneda"] as $moneda): ?>
                                                        <option value="<?php echo $moneda->id_moneda; ?>"><?php echo $moneda->denominacion; ?></option>
                                                    <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </select>
                                        </div>
	                              <label for="Emision" class="col-sm-1 control-label" style="margin-left: -64px;">Fecha de Emisión</label>

	                              <div class="col-sm-2">
                                          <input  type="_date" class="form-control datepicker" id="fecha_factura" required value="" name="fecha_factura" autocomplete="off">
	                              </div>
	                              <label for="Vto" class="col-sm-1 control-label" style="margin-left: -21px;">Fecha de Vencimiento</label>

	                              <div class="col-sm-2">
	                                <input  type="_date" class="form-control datepicker_to" id="fecha_vencimiento_factura" name="fecha_vencimiento_factura" autocomplete="off">
	                              </div>
                                      <label for="FormaPago" class="col-sm-2 control-label" style="margin-left: -10%;">Forma Pago</label>
                                        <div class="col-sm-1">
                                            <select class="form-control" id="forma_pago" name="forma_pago" required>
                                                <option value="" disabled selected>Elija</option>
                                                <?php if ($data["forma_pago"]): foreach ($data["forma_pago"] as $pago): ?>
                                                        <option value="<?php echo $pago->id_forma_pago; ?>"><?php echo $pago->denominacion; ?></option>
                                                <?php endforeach;
                                                endif;
                                                ?>
                                            </select>  
                                        </div>
                                    </div>
								<div class="col-sm-6">
		                            <div class="form-group">
		                                <label for="exento" class="col-sm-4 control-label" >Exento</label>

		                                <div class="col-sm-6">
		                                  <input onkeypress="return soloNumOnePoint(this)" pattern="^\d*(\.\d{0,2})?$" type="number" step="0.01" class="form-control calculaTotal" name="exento" id="exento" placeholder="Exento" style="text-align: right;">
		                                </div>
		                            </div>
		                            <div class="form-group">
		                                <label for="sub_total" class="col-sm-4 control-label">Sub-Total</label>

		                                <div class="col-sm-6">
		                                  <input onkeypress="return soloNumOnePoint(this)" pattern="^\d*(\.\d{0,2})?$" type="number" step="0.01" class="form-control calculaTotal" id="sub_total" name="sub_total" placeholder="Sub-Total" style="text-align: right;">
		                                </div>
		                            </div>
                                            <div class="form-group">
		                                <label for="impuesto" class="col-sm-4 control-label">Impuesto</label>

		                                <div class="col-sm-6">
		                                  <input onkeypress="return soloNumOnePoint(this)" pattern="^\d*(\.\d{0,2})?$" type="number" step="0.01" class="form-control calculaTotal" id="impuesto" name="impuesto" placeholder="Impuesto" style="text-align: right;">
		                                </div>
		                            </div>
                                    <div class="form-group">
		                                <label for="impuesto_consumo" class="col-sm-4 control-label">Impuesto Consumo</label>

		                                <div class="col-sm-6">
		                                  <input onkeypress="return soloNumOnePoint(this)" pattern="^\d*(\.\d{0,2})?$" type="number" step="0.01" class="form-control calculaTotal" id="impuesto_consumo" name="impuesto_consumo" placeholder="Impuesto Consumo" style="text-align: right;">
		                                </div>
		                            </div>
                                            <div class="form-group">
		                                <label for="impuesto" class="col-sm-4 control-label">Retefuente</label>

		                                <div class="col-sm-6">
		                                  <input onkeypress="return soloNumOnePoint(this)" pattern="^\d*(\.\d{0,2})?$" type="number" step="0.01" class="form-control calculaTotal" id="retefuente" name="retefuente" placeholder="Retefuente" style="text-align: right;">
		                                </div>
		                            </div>
                                            <div class="form-group">
		                                <label for="impuesto" class="col-sm-4 control-label">Reteica</label>

		                                <div class="col-sm-6">
		                                  <input onkeypress="return soloNumOnePoint(this)" pattern="^\d*(\.\d{0,2})?$" type="number" step="0.01" class="form-control calculaTotal" id="reteica" name="reteica" placeholder="Reteica" style="text-align: right;">
		                                </div>
		                            </div>
		                            <div class="form-group">
		                                <label for="descuento" class="col-sm-4 control-label">Descuento</label>

		                                <div class="col-sm-6">
		                                  <input onkeypress="return soloNumOnePoint(this)" pattern="^\d*(\.\d{0,2})?$" type="number" step="0.01" class="form-control calculaTotal" id="descuento" name="descuento" placeholder="Descuento" style="text-align: right;">
		                                </div>
		                            </div>		                            
		                            <div class="form-group">
		                                <label for="total_pagar" class="col-sm-4 control-label">Total a Pagar</label>

		                                <div class="col-sm-6">
                                                    <input readonly type="number" step="0.01" class="form-control" id="total_pagar"  name="total_pagar" placeholder="Total a pagar" style="text-align: right;">
		                                </div>
		                            </div>
                                    </div>								
                                    <div class="form-group col-sm-6">
                                       <label for="file" class="col-sm-2 control-label">Comprobante</label>
                                       <input type="hidden" name="filehidden" id="filehidden">
                                       <input name="file" required id="file" type="file" class="form-control file">
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
                              		<button type="button" class="btn btn-default col-sm-4" onclick="limpiarFormulario3();">Cancelar</button>
                            	</div>
                          	</div>
                          	<!-- /.box-footer -->
                        </form>
                  	</fieldset>
                    <div class="modal fade" id="mostrartipogasto" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>Agregar Tipo de Gasto</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
	                              <label for="tipo_gasto_agregado" class="col-sm-3 control-label">Tipo de Gasto:</label>

	                              <div class="col-sm-8">
	                                <input  type="text" class="form-control" id="den_tipo"
	                                        placeholder="Denominacion"
	                                        autocomplete="off">
	                              </div>
	                            </div>
                                </div>
                                <br>
                                <div class="modal-foote" style="text-align: center">
                                    <a href="#" data-dismiss="modal" class="btn btn-info" onclick="guardarTipoGasto()">Guardar</a>
                                    <a href="#" data-dismiss="modal" class="btn btn-default">Cerrar</a>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                     <div class="modal fade" id="mostrarclase" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>Agregar Clase de Gasto</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
	                              <label for="clase_gasto_agregado" class="col-sm-3 control-label">Clase de Gasto:</label>

	                              <div class="col-sm-8">
	                                <input  type="text" class="form-control" id="den_clase"
	                                        placeholder="Denominacion"
	                                        autocomplete="off">
	                              </div>
	                            </div>
                                </div>
                                <br>
                                <div class="modal-foote" style="text-align: center">
                                    <a href="#" data-dismiss="modal" class="btn btn-info" onclick="guardarClaseGasto()">Guardar</a>
                                    <a href="#" data-dismiss="modal" class="btn btn-default">Cerrar</a>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="mostrardescripcion" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3>Agregar Descripcion de Gasto</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                    <label for="clase_gasto_agregado" class="col-sm-4 control-label">Clase de Gasto:</label>

	                              <div class="col-sm-8">
	                                <select class="form-control" id="clase_gasto_agregar" required>
                                            <option value="" disabled selected>Elija</option>
                                            <?php if ($data["clase_gasto"]): foreach ($data["clase_gasto"] as $clase): ?>
                                                    <option value="<?php echo $clase->id_clase_gasto; ?>"><?php echo $clase->denominacion; ?></option>
                                            <?php endforeach;
                                            endif;
                                            ?>
                                        </select>  
	                              </div>
	                              <label for="descripcion_gasto_agregado" class="col-sm-4 control-label">Descripcion de Gasto:</label>

	                              <div class="col-sm-8">
	                                <input  type="text" class="form-control" id="den_descr"
	                                        placeholder="Denominacion"
	                                        autocomplete="off">
	                              </div>
	                            </div>
                                </div>
                                <br>
                                <div class="modal-foote" style="text-align: center;margin-top: 70px">
                                    <a href="#" data-dismiss="modal" class="btn btn-info" onclick="guardarDescripcionGasto()">Guardar</a>
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
                </div>
            </section>
        </main>
	</div>

    <div class="row">
    	<!-- Main content -->
        <div class="col-lg-12 text-right" style="display: block; padding-bottom:5px; padding-right: 25px;" id="nuevoGasto">
			<a class="btn btn-success" title="Registrar Gasto" onclick="nuevoGasto()"><i class="fa fa-money"></i> Nuevo Gasto</a>
		</div>
      	<section class="content">

            <div class="col-lg-12" id="cuerpoGastos" style="display: block">
                
                <?= $this->load->view('operaciones/tabla_gastos', null, true); ?>
              
            </div>

      	</section>
    	<!-- /.content -->
    </div>

</div>
<style>
.vencido{
    background-color:#F7DBDB!important;
}
.vencido:hover {
    background-color:#F4C4C4!important;
}

</style>
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
                        for (var i in response.errors) {
                             alert(response.errors[i]);
                             break;
                        }
                    } else {
                        var cantidad = $('#cantidad_gasto').text();
                        var nueva_cantidad = parseInt(cantidad) + 1;   
                        alert(response.message);
                        limpiarFormulario3();
                        $('#tp_Gastos').DataTable().ajax.reload();
                        $('#cantidad_gasto').text(nueva_cantidad);
                    }
                }
            });        
    });
    
    $(".file").fileinput({
        dropZoneEnabled : false,
        showUpload : false,
        uploadUrl: "<?php base_url();?>", // Please, triggered upload
        //uploadUrl: '<?php base_url();?>Seg_configuraciones/', // Please, triggered upload
        uploadAsync: false,
        maxFileCount: 10
    });
  
    $('.select2').select2();       
    
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
    $('#formSection').css("display","none");
function nuevoGasto(){
    $('#nuevoGasto').css("display","none")

    $('#cuerpoGastos').css("display","none");
    $('#formSection').css("display","block");
	document.getElementById("formDatosBasicos").reset();

}

function limpiarFormulario3(){
    $('#cuerpoGastos').css("display","block");
    $('#formSection').css("display","none");
    $('#nuevoGasto').css("display","block")
}
function soloNumOnePoint(txt)
{
    if(event.keyCode > 47 && event.keyCode < 58 || event.keyCode == 46)
    {
        var amount = $(txt).val();
        var present=0;
        var count=0;
    
        do
        {
        present=amount.indexOf(".",present);
        if(present!=-1)
        {
            count++;
            present++;
        }
        }
        while(present!=-1);
        if(present==-1 && amount.length==0 && event.keyCode == 46)
        {
            event.keyCode=0;
            return false;
        }
        if(count>=1 && event.keyCode == 46)
        {
            event.keyCode=0;
            return false;
        }
        if(count==1)
        {
            var lastdigits=amount.substring(amount.indexOf(".")+1,amount.length);
            if(lastdigits.length>=2)
                {
                    event.keyCode=0;
                    return false;
                }
        }
        return true;
    }
    else
    {
        event.keyCode=0;
        return false;
    }
}

$(document).on('keydown', 'input[pattern]', function(e){
  var input = $(this);
  var oldVal = input.val();
  var regex = new RegExp(input.attr('pattern'), 'g');

  setTimeout(function(){
    var newVal = input.val();
    if(!regex.test(newVal)){
      input.val(oldVal); 
    }
  }, 0);
});



</script>

