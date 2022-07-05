<div class="box box-bancos">

    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Detalle Bancario</strong></small></h6>
    </div>
    <?php //print_r($solicitude); die();
    if (!empty($analisis)){
        $visible = '';
        $cuenta_habilitada = '';
    }else{
        $visible = 'hidden';
        $cuenta_habilitada = 'disabled';
    }
    ?>
    
    <input hidden id="operador_nombre" value="<?php echo isset($_SESSION['user']->first_name)?$_SESSION['user']->first_name:'' ;?>  <?php echo isset($_SESSION['user']->last_name)?$_SESSION['user']->last_name:'' ;?>">
    <input hidden id="id_solicitud" value="<?php echo isset($solicitude)?$solicitude:'' ;?>">
    <input hidden id="id_operador" value="<?= $this->session->userdata('idoperador')?>">
    <input hidden id="cuenta_antigua" value="<?php echo isset($datos_bancarios[0]['numero_cuenta'])?$datos_bancarios[0]['numero_cuenta']:'' ;?>">
    <input hidden id="tipo_cuenta_antigua" value="<?php echo isset($datos_bancarios[0]['Nombre_TipoCuenta'])?$datos_bancarios[0]['Nombre_TipoCuenta']:'' ;?>">
    <input hidden id="banco_antiguo" value="<?php echo isset($datos_bancarios[0]['Nombre_Banco'])?$datos_bancarios[0]['Nombre_Banco']:'' ;?>">
    <?//= var_dump($datos_bancarios);?>
    <table class="table table-striped table=hover display" width="100%" style="margin:0px;">
        <tbody>
            <tr>
                <td class="analisis_col_izq" width="30%">Banco: </td>
                <td class="analisis_col_der">
                   <select id="client_bank" class="form-control input-sm">
                        <option value="" >.:Seleccione una opción:.</option>
                        <?php foreach ($banks as $key => $entidad):?>
                            <option value="<?php echo $entidad['id_Banco']; ?>" 
                                <?php if(isset($datos_bancarios[0]['id_banco']))
                                        { 
                                            echo ($entidad['id_Banco']==$datos_bancarios[0]['id_banco'])?'selected style="background-color:blanchedalmond"':'' ;
                                        }
                                ?>
                            >
                                <?php echo $entidad['Nombre_Banco']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>                    
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq">Tipo de cuenta: </td>
                <td class="analisis_col_der">
                    <select id="client_account" class="form-control input-sm">
                        <option value="" >.:Seleccione una opción:.</option>
                        <?php foreach ($type_account as $key => $account):?>
                            <option value="<?php echo $account['id_TipoCuenta']; ?>" 
                                <?php if(isset($datos_bancarios[0]['id_banco']))
                                    { 
                                        echo ($account['id_TipoCuenta']==$datos_bancarios[0]['id_tipo_cuenta'])?'selected style="background-color:blanchedalmond"':'' ;
                                    }
                                ?>
                            >
                                <?php echo $account['Nombre_TipoCuenta'];?>    
                            </option>
                        <?php endforeach; ?>
                    </select>    
                </td>
            </tr>
            <tr>           
                <?php 
                    $fondo_cuenta ="";
                    $style_cuenta = "style='background-color: white;'";
                    $tamanio = "col-md-3";
                    if(!empty($datos_bancarios[0]['respuesta'])){
                        $tamanio ='col-md-3';
                        $style_cuenta = "";
                        if($datos_bancarios[0]['respuesta'] == "ACEPTADA"){
                            $fondo_cuenta = "cuenta_row bg-success";
                            $razon_rechazado = $datos_bancarios[0]['razon'];
                        } else {     
                            $fondo_cuenta = "cuenta_row bg-danger";
                            $razon_rechazado = $datos_bancarios[0]['razon'];
                        } 
                    }
                ?>
                <td class="analisis_col_izq fondo <?php echo $fondo_cuenta?>">                    
                <!-- Icono de mail validado o no validado-->    
                        <?php 
                            if (isset($datos_bancarios[0]['respuesta'])){
                                if($datos_bancarios[0]['respuesta'] == "ACEPTADA"){
                                ?>
                                <i style="font-size: 14px; margin-left: -6px;margin-top: 9px; color: green" id="icono_cuenta" class="fa fa-check"></i> 
                            <?php }else{?>
                                <i style="font-size: 14px; margin-left: -6px;margin-top: 9px; color: red" id="icono_cuenta" class="fa fa-times-circle"></i>
                            <?php } 
                            }
                        ?>
                        Número de cuenta:
                </td>
                <td class="analisis_col_der fondo <?php echo $fondo_cuenta?>">
                    <div  <?php echo $style_cuenta ?>>
                        <div class="col-md-12">
                            <strong id="account">
                                <input <?php echo $cuenta_habilitada ?> type="text" id="cuenta_mascara" autocomplete="off" onkeypress="ValidarNumeros(event)" style="width: 92%; margin-left: -15px;" value="<?php echo isset($datos_bancarios[0]['numero_cuenta'])? $datos_bancarios[0]['numero_cuenta']:'' ?>" /> 
                                <input hidden id="nro_cuenta_original" value="<?php echo isset($datos_bancarios[0]['numero_cuenta'])? $datos_bancarios[0]['numero_cuenta']:'' ?>" style="width: 150px" /> 
                            <span style="margin-left: 40px;"><?php echo isset($razon_rechazado)?$razon_rechazado:'' ?></span></strong>
                            <i id="icono_reenviar" <?php echo $visible ?> style="font-size: 20px; float: right; color: #4F9EB5;margin-top: -16px;cursor:pointer" title="Reenviar Validar Cuenta" class="fa fa-refresh" onclick="reenvioValidarCuenta();"></i>
                        </div>
                    </div>
                </td>    
            </tr>
            <tr>
                <td class="analisis_col_izq">Forma de desembolso: </td>
                <td class="analisis_col_der">
                    <div class="row">
                        <div class="col-sm-6">
                            <select id="desembolso" disabled class="form-control input-sm">
                                <option value="">.:Seleccione una opción:.</option>
                                <option value="" selected>TRANSFERENCIA</option>                     
                            </select> 
                        </div>
                        <?php if (isset($pagado_txt[0]['pagado'])){                
                                    if($pagado_txt[0]['pagado'] == 2){?>
                                        <div class="col-sm-6">
                                            <button id="reenvio_desembolso" value="<?php echo $solicitude['id'];?>" class="btn btn-xs btn-success button-gestion" title="Reenvio Desembolso">
                                                Reenviar a Desembolso
                                            </button> 
                                        </div>  
                              <?php } 
                            } ?>
                    </div>
                       
                </td>
            </tr>
            
            
        </tbody>
    </table> 
</div>
<script type="text/javascript">
    $('document').ready(function(){
         $("#cuenta_mascara").blur(function(){            
            var new_cuenta = $("#cuenta_mascara").val();
            var old_cuenta = $("#nro_cuenta_original").val();
            if($.isNumeric(new_cuenta) && new_cuenta != old_cuenta)
                $("#nro_cuenta_original").attr("value",new_cuenta);
        });    
        
        
    //Reenviar desembolso
    $("#reenvio_desembolso").click(function(){
        //Actualizar el campo Pagado
        var data = {
            "id_solicitud" : $("#reenvio_desembolso").val(),
        }
        var base_url = $("input#base_url").val() + "gestion/Galery/actualizarPagado";
        $.ajax({
        type: "POST",
            url: base_url,
            data: data,
            success:function(response){
                if(response){
                    toastr["success"]("Enviado a Deseembolso", "Reenvio de desembolso");
                    $("#reenvio_desembolso").hide();
                }else{
                    toastr["error"]("Fallo envio a Desembolso", "Reenvio de desembolso");
                }                
            }
        });
    });   
  });

  function reenvioValidarCuenta() {
    //Guardar lo que esta solicitud_datos_bacarios a solicitud_datos_bacarios_intentos    
    var id_solicitud = $("#id_solicitud").val();
    // alert(id_solicitud)
    var numero_cuenta = $("#nro_cuenta_original").val();
    var id_banco = $("#client_bank").val();
    var id_tipo_cuenta = $("#client_account").val();
    var id_operador = $("#id_operador").val();
    var numero_cuenta_ant = $("#cuenta_antigua").val();
    var banco_ant = $("#banco_antiguo").val();
    var tipo_cuenta_ant = $("#tipo_cuenta_antigua").val();
    var nombre_operador = $("#operador_nombre").val();
    var nombre_banco = $("#client_bank option:selected").html();
    var nombre_tipo_cuenta = $("#client_account option:selected").html();
    var buro = $("#buro").val();

    if (!numero_cuenta || !id_banco || !id_tipo_cuenta) {
        toastr["warning"]("Debe ingresar un número de cuenta, banco y tipo de cuenta", "ACTUALIZACION DE CUENTA");
    } else {
        var base_url = $("#base_url").val();
        var data = {
            "id_solicitud": id_solicitud,
            "numero_cuenta": numero_cuenta,
            "id_banco": id_banco,
            "id_tipo_cuenta": id_tipo_cuenta,
            "numero_cuenta_ant": numero_cuenta_ant,
            "banco_ant": banco_ant,
            "tipo_cuenta_ant": tipo_cuenta_ant,
            "id_operador": id_operador,
            "nombre_operador": nombre_operador,
            "nombre_banco": nombre_banco,
            "nombre_tipo_cuenta": nombre_tipo_cuenta,
            "buro": buro
        }
        var base_url = $("input#base_url").val() + "gestion/Galery/actualizarNumeroCuenta";
        $.ajax({
            type: "POST",
            url: base_url,
            data: data,
            dataType: 'json',
            success: function (response) {
                //actualizar espacio de trackeo
                //addElemTimeLine(response.track);
                $('#banco_antiguo').val(nombre_banco);
                $('#tipo_cuenta_antigua').val(nombre_tipo_cuenta);
                $('#cuenta_antigua').val(numero_cuenta);
                if (response.update_cbu == "APROBADO") {
                    toastr["success"](response.update_cbu, "CUENTA ACTUALIZADA");
                    $('.fondo').removeClass("bg-danger").addClass("bg-success");
                    $('#icono_cuenta').removeClass("fa-times-circle").addClass("fa-times-check");
                    $('#icono_cuenta').css('color', 'green');

                } else {
                    toastr["error"](response.update_cbu, "CUENTA ACTUALIZADA");
                    $('.fondo').removeClass("bg-success").addClass("bg-danger");
                    $('#icono_cuenta').removeClass("fa-times-check").addClass("fa-times-circle");
                    $('#icono_cuenta').css('color', 'red');
                }

                get_box_track_stand_alone(id_solicitud)
            }
        });
    }
}

function get_box_track_stand_alone(id_solicitud, id_credito=0) {
    let client = $("#box_client_data #client");
    var documento = client[0]["attributes"][6]["value"];;
    $.ajax({
        url: base_url + 'gestion/Tracker/track_stand_alone/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".tracker").html(response);
            $(".tracker #box_tracker .box-footer").css('min-height', '185px');
            $(".tracker #box_tracker .box-body").css('height', 'calc(100% - 185px)');
            get_box_whatsapp(documento);
            if(id_credito > 0){
                $('#result').addClass('hide');
                document.getElementById('id_credito').value = id_credito;
            }
            
        })
        .fail(function (response) {
        })
        .always(function () {

        });
}
</script>