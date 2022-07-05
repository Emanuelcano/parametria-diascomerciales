<style type="text/css">
    .grid-striped .row:nth-of-type(odd) { background-color: rgba(0,0,0,.05);}

    .css-table-row {
        padding: 10px;
        margin: 2px;
        
    }

    .css-table-row div {
        display: table-cell;
        padding: 0 6px;
    }
    
    .telefono_row {
        height: 32px;
        margin-left: -22px;
        margin-right: -23px;
    }   
    
    .cuenta_row {
        height: 40px;
        margin-left: -15px;
        margin-right: -15px;
    }
    
    .box>.overlay,.overlay-wrapper>.overlay,.box>.loading-img,.overlay-wrapper>.loading-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%
}

    .box .overlay_telefono,.overlay-wrapper .overlay_telefono {
        z-index: 50;
        background: rgba(255,255,255,0.7);
        border-radius: 3px
    }

    .box .overlay_telefono>.fa,.overlay-wrapper .overlay_telefono>.fa {
        position: absolute;
        top: 44%;
        left: 74%;
        margin-left: -15px;
        margin-top: -15px;
        color: #000;
        font-size: 20px
    }

    .box .overlay_telefono.dark,.overlay-wrapper .overlay_telefono.dark {
        background: rgba(0,0,0,0.5)
    }
    
    .box .overlay_email,.overlay-wrapper .overlay_email {
        z-index: 50;
        background: rgba(255,255,255,0.7);
        border-radius: 3px
    }

    .box .overlay_email>.fa,.overlay-wrapper .overlay_email>.fa {
        position: absolute;
        top: 87%;
        left: 87%;
        margin-left: -15px;
        margin-top: -15px;
        color: #000;
        font-size: 20px
    }

    .box .overlay_email.dark,.overlay-wrapper .overlay_email.dark {
        background: rgba(0,0,0,0.5)
    }


</style>
<?php
/* 
    if ($whatsapp_respuesta[0] == "L"){
        $whatsapp = "<strong style='color:blue'>LEIDO</strong>";
        $icono = '<i style="margin-right: 8px; color: green" class="fa fa-check"></i>';
        $bg = "bg-success";
    } else {
        $whatsapp = "NO VERIFICADO";
        $icono = "";
        $bg = "";
    }
    
    if (isset($whatsapp_respuesta[0]->sms_status)){
        switch ($whatsapp_respuesta[0]->sms_status) {
        case "read":
            $whatsapp = "<strong style='color:blue'>LEIDO</strong>";
            $icono = '<i style="font-size: 15px; color: green; margin-right: 8px;" class="fa fa-check"></i>';            
            $bg = "bg-success";
            break;
        case "sent":
            $whatsapp = "<strong style='color:black'>ENVIADO</strong>";
            $icono = '<i style="font-size: 15px; color: green; margin-right: 8px;" class="fa fa-check"></i>';
            $bg = "bg-success";
            break;
        case "delivered":
            $whatsapp = "<strong style='color:green'>ENTREGADO</strong>";
            $icono = '<i style="font-size: 15px; color: green; margin-right: 8px;" class="fa fa-check"></i>';
            $bg = "bg-success";
            break;
        case "faild":
        case "undelivered":
            $whatsapp = "<strong style='color:red'>NO ENTREGADO</strong>";
            $icono = '<i style="font-size: 15px; color: red; margin-right: 8px;" class="fa fa-times-circle"></i>';
            $bg = "bg-danger";
            break;
        case "queued":
            $whatsapp = "<strong style='color:red'>NO</strong>";
            $icono = '<i style="font-size: 15px; color: red; margin-right: 8px;" class="fa fa-times-circle"></i>';
            $bg = "bg-danger";
            break;
        }
    }
    */
?>
<script type="text/javascript">
    $(document).ready(function () {
        // $("#icontoClose").trigger("click");
        $("#txt_num_man").val('<?= PHONE_COD_COUNTRY .   $solicitude['telefono']?>');
    });
</script>


<div id="box_datos_contacto" class="box box-info">
    <div class="box-header with-border" id="titulo">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4">
                    <small><strong>Datos de Contacto</strong></small>
                </div>                
            </div>
        </div>
    </div><!-- end box-header -->
    <?php
        $style_mail = '';
        $fondo_mail = '';
        $style_telefono = '';
        $fondo_telefono = '';  

        if (isset($solicitude['validacion_mail'])){
            if($solicitude['validacion_mail'] == 1){
                $style_mail = 'style="display:none"';
                $fondo_mail = "bg-success";
            } else {     
                $style_mail = 'style="display:inline;float:rigth"';
                $fondo_mail = "bg-danger";
            }         
        }

        if (isset($solicitude['validacion_telefono'])){
            if($solicitude['validacion_telefono'] == 1){ 
                $style_telefono = 'style="display:none"';
                $fondo_telefono = "bg-success";
            } else {
                $style_telefono = 'style="display:block"';
                $fondo_telefono = "bg-danger";                
            }  
        }

    ?>
    <div class="box-body" style="font-size: 12px;"> 
        <div class="overlay_telefono" hidden>
            <i class="fa fa-refresh fa-spin"></i>
        </div>
        <div class="overlay_email" hidden>
            <i class="fa fa-refresh fa-spin"></i>
        </div>
        <input id="documento" type="hidden" value="<?php echo $solicitude['documento'];?>">

        <div class="container-fluid grid-striped">
            <div class="css-table">
                 
                <div class="telefono_row css-table-row <?php echo $fondo_telefono ?>" id="dato_telefono">                
                  
                    <div class="col-md-2">
                        <?php if($solicitude['validacion_telefono'] == 1){ ?>
                                <i style="font-size: 15px; color: green;" class="fa fa-check"></i>
                        <?php }else{?>
                            <i style="font-size: 15px; color: red;" class="fa fa-times-circle" id="icon_tlf"></i>
                        <?php } ?>
                        <small>Teléfono:</small>
                    </div>
                    <div class="col-md-4" id="telefono_llamar" style="margin-left: 5px;"><strong id="phone"><?php echo $solicitude['telefono']; ?></strong></div>
                    <?php if($solicitude['tipo_solicitud'] == 'RETANQUEO' && $solicitude['pagare_enviado']==1 && $solicitude['pagare_firmado'] == 0 && $solicitude['validacion_telefono'] == 1 ){
                            echo '<div class="col-md-3 text-right"><small>CODIGO FIRMA:</small></div>
                            <div class="col-md-2" style="margin-left: 0px;"><strong>'.$solicitude['codigo_firma'].'</strong><i class="fa fa-clone pull-right copy" style="color: red; " title="Copiar Numero de telefono">&nbsp;&nbsp;</i></div>';
                        }
                    ?>                   
                    <!--
                    <div class="col-md-1" style="margin-left: 28px;">                    
                        <button id="llamar" style="font-size: 15px; color: green; cursor: pointer; display: block;" class="fa fa-phone"></button>
                    </div>
                    -->
                    <div class="col-md-5" <?php echo $style_telefono?> >
                            <?php if($solicitude['validacion_telefono'] != 1){ ?>
                            <button type="button" style="font-size: 15px; float:right;margin-left: 5px; border-width: 1px; background-color: #00a65a;" class="fa fa-check" id="btnValTelefono" onclick="val_telefono_client(<?php echo $solicitude['id']; ?>)"></button>
                            <button style="font-size: 15px; cursor: pointer;float:right;padding: 0px" class="fa fa-refresh" id="celdaValTelefono" onclick="resetTelefono(<?php echo $solicitude['id']; ?>)">&nbsp;-&nbsp;<?php echo isset($solicitude['cantidad_sms'])? $solicitude['cantidad_sms']:0 ?></button>
                            <?php } ?> 
                            <?php if(!$solicitude['validacion_telefono']): ?>
                                    <button id="btn_send_sms"  data-id_solicitud="<?php echo $solicitude['id'];?>" data-documento="<?php echo $solicitude['documento'] ?>" style="font-size: 15px; color: green;float: right;margin-right: 10px;" class="fa fa-share-square-o"></button>
                                        <div style="display:inline" id="msg_send_sms"></div>
                                    <button id="btn_send_ivr"  data-id_solicitud="<?php echo $solicitude['id'];?>" data-documento="<?php echo $solicitude['documento'] ?>" style="font-size: 15px; color: green;float: right;margin-right: 10px;" class="fa fa-share-square-o"> <span style="font-size: 10px;color: black;font-weight: bold;"> IVR </span></button>

                            <?php endif; ?>                                                             
                    </div>              
                </div>
                <!--<div class="telefono_row css-table-row <?php // echo $bg; ?>">
                    <div class="col-md-1" style="margin-left: -10px;"><?php //echo $icono; ?></div>
                    
                    <div class="col-md-1 text-right"><small>WhatsApp:</small></div>
                    <div class="col-md-6" id="whatsapp_respuesta" style="margin-left: 33px;"><strong id="whatsapp_respuesta"><?php //echo $whatsapp; ?></strong></div> 
                    
                </div> -->
                <div class="telefono_row css-table-row <?php echo $fondo_mail ?>" >

                    <div class="col-md-2">
                        <!-- Icono de mail validado o no validado-->    
                        <?php if(!empty($solicitude['email'])){
                                    if($solicitude['validacion_mail'] == 1){
                                ?>
                                    <i style="font-size: 15px; color: green" class="fa fa-check"></i> 
                             <?php }else{?>
                            <i style="font-size: 15px; color: red" class="fa fa-times-circle"></i>
                        <?php } }?>
                        <small>Email:</small>
                    </div>                
                    <!-- Div de iconos -->
                    <div class="col-md-10" style="padding: 0px;margin:0px">
                        <!-- Icono de editar mail-->
                        <div class="col-md-5">
                            <!-- Dato de mail que viene desde la solicitud -->
                            <strong id="email" style="float:left"><?php echo $solicitude['email'] ?></strong>
                            <input id="new_email" style="display: none;float:left" type="text" name="new_email" value="<?php echo $solicitude['email']; ?>">
                        </div>
                        <div class="col-md-2 col-md-offset-2" style="margin-left: -10px;">
                            <button id="edit_mail" style="font-size: 15px; color: #337ab7; cursor: pointer; display: block;float: right;" class="fa fa-pencil"></button>
                            <button id="edit_mail_save" style="font-size: 15px; margin-right: 8px; color: #5cb85c; cursor: pointer; display: none;" class="fa fa-save"></button>
                        </div>
                        <div class="col-md-1">
                            <button id="edit_mail_cancel" style="font-size: 15px; margin-right: 8px; color: #d9534f; cursor: pointer; display: none;" class="fa fa-undo"></button>
                        </div> 
                        <div class="col-md-1">
                            <?php if(!empty($email_log) && count($email_log) > 4){?>
                                <button id="desbloquear" style="font-size: 15px; color: blue; cursor: pointer; display: block;" title="Desbloquear usuario" class="fa fa-lock"></button>
                        <?php }   ?>                    
                        </div>                    
                        <div  class="col-md-3" <?php echo $style_mail?>>  
                            <!-- Cantidad de veces que se reenvio el mail-->
                            <?php if(!empty($solicitude['email'])){
                            if($solicitude['validacion_mail'] != 1){ ?>
                                <button  style="font-size: 15px; cursor: pointer;float:right;" id="celdaValEmail" class="fa fa-refresh dropdown-item" onclick="resetEmail(<?php echo $solicitude['id']; ?>)">&nbsp;-&nbsp; <?php echo isset($solicitude['cantidad_mail'])? $solicitude['cantidad_mail']:0 ?></button>
                            <?php } } ?> 
                            <?php if(!$solicitude['validacion_mail']): ?>
                            <button id="btn_send_email" data-id_solicitud="<?php echo $solicitude['id'];?>" data-documento="<?php echo $solicitude['documento'] ?>" style="font-size: 15px; color: green;margin-left: -13px" class="fa fa-share-square-o dropdown-item"></button>
                            <?php endif; ?>
                        </div> 
                    </div>
                    
                </div>    
            </div>
        </div>

    </div>

       <!--  -->
</div> <!-- end box-body -->


<script type="text/javascript">
    $('document').ready(function(){

        // EVENTOS
        
        // Reenvio de SMS
        $("#btn_send_sms").click(function(e) {  
            $("#box_datos_contacto .loader").show();
            $(this).addClass('hidden');
            let id_solicitud = $(this).data('id_solicitud');            
            if(send_sms(id_solicitud))
            {
                toastr["success"]("Mensaje enviado", "Envio de SMS");
            }else{
                toastr["error"]("Error en el envio del SMS", "Envio de SMS");
            }
        });

        //Reenvio de IVR
        $("#btn_send_ivr").click(function(e) {  
            $("#box_datos_contacto .loader").show();
            $(this).addClass('hidden');
            let id_solicitud = $(this).data('id_solicitud');    
            let response = send_ivr(id_solicitud); 
            if(response != -1 && typeof(response.status.ok) != 'undefined' && response.status.ok)
            {
                toastr["success"]("IVR enviado", "Envio de IVR");
            }else{
                if(response.status.code == '402'){
                    toastr["error"]("Se ha superado el máximo de envíos", "Envio de IVR");
                } else{
                    toastr["error"]("Error en el envio del IVR", "Envio de IVR");
                }
            }
        });






        // Reenvio de Email
        $("#btn_send_email").click(function(e) {  
            $(this).addClass('hidden');
            let id_solicitud = $(this).data('id_solicitud');
            if(send_email_validation(id_solicitud))
            {
                toastr["success"]("Mensaje enviado", "Envio de Email");
            }else{  
                toastr["error"]("Error en el envio del email", "Envio de Email");
            }
        });

        
        
        // Modificar Mail
        $("#edit_mail").click(function(e) {  
            // Oculto el boton pencil
            $(this).hide();
            // Oculto el mail actual
            $("#email").hide();
            // Muestro el input para el mail nuevo
            $("#new_email").show();
            // Oculto los botones de acciones sobre el mail
            $("#box_client_data .email_action").css('visibility', 'hidden');
            // Muestro el boton de guardar
            $("#edit_mail_save").show();
            // Muestro el boton de cancelar
            $("#edit_mail_cancel").show();
        });
        //Verificando si no tiene email nomuestra el btn modificar email
        //     console.log("$(#email).text()",$("#email").text());
        //     console.log("$(#email).val()",$("#email").val());

        if($("#email").text()==''){

            $("#edit_mail").css('visibility', 'hidden');
        }else{
            $("#edit_mail").css('visibility', 'visible');
        }

        // Guardar nuevo Mail
        $("#edit_mail_save").on('click', function(event){

            let id_solicitud = $("#id_solicitud").val();
            let documento = $("#documento").val();
            let params= { "email": $("#new_email").val(),"mail2": $("#new_email").val(), "documento": documento};
            let save_solicitude = edit_solicitud_data_client(id_solicitud, params);
            if(save_solicitude)
            {
                let comment = $("#email").text()+" -> "+$("#new_email").val();
                let typeContact = 6;
                let idOperator = $("#id_operador").val();
                saveTrack(comment, typeContact, id_solicitud, idOperator);
            // Oculto el boton guardar
            $(this).hide();
            // Oculto el boton de cancelar
            $("#edit_mail_cancel").hide();
            // Oculto el input para el mail nuevo
            $("#new_email").hide();
            // Actualizo el campo con el nuevo mail
            $("#email").text($("#new_email").val());
            // Muestro el campo con el nuevo mail
            $("#email").show();
            // Muestro los botones de acciones sobre el mail
            $("#box_client_data .email_action").css('visibility', 'visible');
            // Muestro el boton pencil
            $("#edit_mail").show();
            // Muestro el boton reenviar mensaje
            $("#flecha_mail").show();
            }else{
                Swal.fire({
                            title:"¡Ups!",
                            text: "Este email no esta disponible",
                            icon: 'error'
                        });
                $("#edit_mail_cancel").click();
                $("#new_email").val($("#email").text());
            }
        });

        // Cancelar Modificar Mail
        $("#edit_mail_cancel").on('click', function(event){
            // Oculto el boton cancelar
            $(this).hide();
            // Oculto el boton guardar
            $("#edit_mail_save").hide();
            // Muestro los botones de acciones sobre el mail
            $("#box_client_data .email_action").css('visibility', 'visible');
            // Muestro el boton pencil
            $("#edit_mail").show();
            // Muestro el boton reenviar mensaje
            $("#flecha_mail").show();
            // Oculto el input para el mail nuevo
            $("#new_email").hide();
            // Muestro el campo con el mail
            $("#email").show();
        });
        
        // Llamada automatica
        $('#llamar').click(function(e) {            
            var inputvalue = $("#telefono_llamar").text(); //numero de telefono
            window.location.replace(" zoiper://57"+inputvalue);
        });
        
        $('#desbloquear').click(function(e) {            
            var email = $("#email").text(); 
            if (email = ""){
                toastr["info"]("No se encuentra Email para desbloquear", "Desbloqueo de Email");
            } else {
                desbloquear_usuario();               
            }            
        });
        
    });
</script>
