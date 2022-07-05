<style>
    @media (min-width: 768px) and (max-width: 1024px)  {
        .slot-down{
            width: 353px;
        }
    }
    @media (min-width: 1025px) and (max-width: 1920)  {
        .slot-down{
            width: 523px;
        }
    }
    
</style>
<div class="row" >
    <input type="hidden" id="hdd_id_operador_<?=(!empty($canalChat[0]['id']))?$canalChat[0]['id']:0?>" value="<?= (!empty($canalChat[0]['documento']))?$canalChat[0]['documento']:0 ?>">
    <input type="hidden" id="hdd_id_solicitud_<?=(!empty($canalChat[0]['id']))?$canalChat[0]['id']:0?>" value="<?= (!empty($canalChat[0]['id_solicitud']))?$canalChat[0]['id_solicitud']:0 ?>">
    <?php (!empty($canalChat[0]['id']))?$idchat = $canalChat[0]['id']:$idchat = 0;?>
    <div class="col-md-12" id="colum-chat-<?php echo $idchat  ?>">
        <div class="panel panel-primary panel_operadores" id="panel_operadores_<?php echo $idchat; ?>" style="height:620px;margin-left: -13px;margin-right: -13px;margin-top: -11px;">
        <div class="disabled-chat"></div>
            <div class="panel-heading">
                <?php
                if (!empty($canalChat[0]['id'])) {

                     if ($canalChat[0]['estado_credito']=="mora")
                        {
                            $labelEstado = '<span class="label label-danger">'.$canalChat[0]['estado_credito'].'</span>';
                        }else if ($canalChat[0]['estado_credito']=="vigente"){
                            $labelEstado = '<span class="label label-success">'.$canalChat[0]['estado_credito'].'</span>';
                        }else if ($canalChat[0]['estado_credito']=="anulado"){
                            $labelEstado = '<span class="label label-warning">'.$canalChat[0]['estado_credito'].'</span>';
                        }else if ($canalChat[0]['estado_credito']=="cancelado"){
                            $labelEstado = '<span class="label label-default">'.$canalChat[0]['estado_credito'].'</span>';

                    } 
                }else{
                    $labelEstado ="";
                } 
                ?>
                <h4 class="panel-title" style="font-size: 12px;" data-toggle="tooltip" > <?php echo  (!empty($canalChat[0]['id']))? $canalChat[0]['documento']." ".$canalChat[0]['nombres']." ".$canalChat[0]['apellidos']." ".$labelEstado." ".$canalChat[0]['from']:  "Sin chats disponibles"; ?></h4>
                
            </div>
            <!-- progress bar  -->
            <div class="progress <?php echo (empty($canalChat[0]['id']))?'hide':'';?>" style="margin-bottom:0px;" id="timer-progress-bar-container">
                <div class="progress-bar progress-bar-striped progress-bar-success active " id="progress_bar_<?php echo $idchat; ?>" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                <a class="btn btn-xs btn-danger cronometro-button" id="cronometro-button-danger-<?php echo $idchat; ?>" data-animation="zoomOutDown"  role="button" style="margin:0; position:absolute; right:10px" ><i class="fa fa-ban"></i> Finalizar</a>
                <!-- <a class="btn btn-xs btn-warning cronometro-button disabled" id="cronometro-button-warning" role="button" style="margin:0;  position:absolute; right:30px" onclick = "solicitar_extension(0)"><i class="fa fa-refresh"></i></a> -->
                <!-- <a class="btn btn-xs btn-info cronometro-button disabled" id="cronometro-button-descanso" role="button" style="margin:0;  position:absolute; right:5px" onclick = "temporizador_control_descanso()"><i class="fa fa-pause"></i></a> -->
                
            </div> 
            <!-- progress bar  -->
            <div style=" overflow-y: auto;" id="caja_scroll_operadores_<?php echo $idchat; ?>">
            
                <div class="panel-body" style="height:588px; padding:0;">
                    <div class="col-xs-12" style="height:100%; padding-left:0;padding-right:0;">
                    <?php $this->load->view('whatsapp/whatsapp_component', ['id_chat' => $idchat, 'canal' => (!empty($canal))?$canal: 0]); ?>

                    </div>
                </div>
                <!-- aqui -->
            </div>
        </div>
    </div>
    <div class="col-md-12 colum-info slot-down" id="colum-info-<?php echo $idchat; ?>" style="height:620px;margin-left: -13px;margin-right: -13px;padding-left: 16px;padding-right: 0px;width: 652px;margin-top: -16px;">
                    <div class="box box-primary" height="700px">
                        <div class="disabled-chat"></div>
                            <div class="box-header with-border">
                                <h3 class="box-title">Informacion del credito <?php echo (!empty($canalChat[0]['id_credito']))?$canalChat[0]['id_credito']:0 ?>
                                    
                                </h3>

                                <div class="box-tools pull-right">
                                    <?php if(!empty($canalChat[0]['paso'])): ?>
                                     <i style="font-size: 13px; color: blue" class="fa fa-eye">&nbsp;<label style="font-family: arial;"> <?php echo $canalChat[0]['paso'];?></label></i>
                                    <?php endif ?> 
                                    <?php if(!empty($canalChat[0]['estado_solicitud'])): ?>
                                        <?php if($canalChat[0]['estado_solicitud'] == 'APROBADO'){ ?>
                                            <i style="font-size: 13px; color: green" class="fa fa-check">&nbsp;<label style="font-family: arial;"><?php echo $canalChat[0]['estado_solicitud'];?></label></i>
                                        <?php }else if($canalChat[0]['estado_solicitud'] == 'RECHAZADO' || $canalChat[0]['estado_solicitud'] == 'ANULADO'){ ?>
                                            <i style="font-size: 13px; color: red" class="fa fa-times-circle">&nbsp;<label style="font-family: arial;"><?php echo $canalChat[0]['estado_solicitud'];?></label></i>
                                        <?php }else if($canalChat[0]['estado_solicitud'] == 'ANALISIS'){ ?>
                                            <i style="font-size: 13px; color: teal" class="fa fa-cogs">&nbsp;<label style="font-family: arial;"><?php echo $canalChat[0]['estado_solicitud'];?></label></i>
                                        <?php }else if($canalChat[0]['estado_solicitud'] == 'VERIFICADO'){ ?>
                                            <i style="font-size: 13px; color: orange" class="fa fa-eye">&nbsp;<label style="font-family: arial;"><?php echo $canalChat[0]['estado_solicitud'];?></label></i>
                                        <?php }else if($canalChat[0]['estado_solicitud'] == 'VALIDADO'){ ?>
                                            <i style="font-size: 13px; color: brown" class="fa fa-check-square-o">&nbsp;<label style="font-family: arial;"><?php echo $canalChat[0]['estado_solicitud'];?></label></i>
                                        <?php }else if($canalChat[0]['estado_solicitud'] == 'TRANSFIRIENDO'){ ?>
                                            <i style="font-size: 13px; color: purple" class="fa fa-bank">&nbsp;<label style="font-family: arial;"><?php echo $canalChat[0]['estado_solicitud'];?></label></i>
                                        <?php }else if($canalChat[0]['estado_solicitud'] == 'PAGADO'){ ?>
                                            <i style="font-size: 13px; color: blue" class="fa fa-money">&nbsp;<label  style="font-family: arial;"><?php echo $canalChat[0]['estado_solicitud'];?></label></i>
                                        <?php } ?>
                                    <?php endif ?> 
                                </div>
                            </div>
                            
                            <div class="box-body" >
                                <iframe id="body_info_<?php echo $idchat; ?>" 
                                title="Inline Frame Example"
                                width="100%"
                                height="700px"
                                frameborder="0"
                                src="<?php base_url() ?>atencion_cobranzas/cobranzas/getChatButtom/<?php echo (!empty($canalChat[0]['id_credito']))?$canalChat[0]['id_credito']:0 ?>/<?php echo (!empty($canalChat[0]['documento']))?$canalChat[0]['documento']:0 ?>">
                                </iframe>
                            </div>
                    </div>


                        
        </div>
</div>

<input type="hidden" id="hdd_tiempo_res_chat_<?php echo $idchat;?>" value="<?php echo (!empty($canalChat[0]['tiempo_respuesta']) )? $canalChat[0]['tiempo_respuesta'] : 0 ?>">
<input type="hidden" id="hdd_tiempo_wait_chat_<?php echo $idchat;?>" value="<?php echo (!empty($canalChat[0]['tiempo_espera']) )? $canalChat[0]['tiempo_espera'] : 0 ?>">

<?php 
if (!empty($canalChat[0]['to']))
{
    $canal = substr($canalChat[0]['to'], -3); 

}
?>
<!-- <input type="hidden" id="id_chat" value=""> -->
<script>
    var btn_finalizar_chat = false;
    $(document).ready(function(){
        
        $variables = {
            'bandera' : true,
            'minutosGestion': $("#hdd_tiempo_res_chat_"+<?php echo $idchat;?>).val() / 60,
            'minutosRespuesta': $("#hdd_tiempo_wait_chat_"+<?php echo $idchat;?>).val() / 60
        };

        controladores[<?php echo $idchat;?>]  = $variables;
       
       <?php 
       if($idchat > 0 ){
       ?>
       bandera = true;
        cargar(<?php echo $idchat;?>, <?php echo $canal;?>, bandera);
       <?php } ?> 
        
        
        iniciarTimer(<?php echo $idchat;?>);
        $('#cronometro-button-danger-'+<?php echo $idchat;?>).on('click',function(event){
            //event.preventDefault();
            cerrar_chat(<?php echo $idchat;?>,true);
        });
       
    });
    
    /**
     * Inicio cronometro barra de tiempo
     */
    function iniciarTimer(id_chat) {
        
        let tiempoSeleccionado = (controladores[id_chat].bandera)? controladores[id_chat].minutosGestion : controladores[id_chat].minutosRespuesta;
        Object.assign(controladores[id_chat],{'tiempoSeleccionado': tiempoSeleccionado });
        Object.assign(controladores[id_chat],{'segundos': (tiempoSeleccionado * 60) });
        Object.assign(controladores[id_chat],{'warningTime':(tiempoSeleccionado * 60) / 2 });
        Object.assign(controladores[id_chat],{'dangerTime': (tiempoSeleccionado * 60) / 4 });
        Object.assign(controladores[id_chat],{'porcentajeBarra': 100 });

       

        $("#progress_bar_"+id_chat).html(new Date( controladores[id_chat].segundos * 1000).toISOString().substr(11, 8));
        $("#progress_bar_"+id_chat).addClass("progress-bar-success");
        $("#progress_bar_"+id_chat).removeClass("progress-bar-warning");
        $("#progress_bar_"+id_chat).removeClass("progress-bar-danger");
        
        cronometroTimer_id = setInterval(function () {
            escribirTimer(id_chat);
        }, 1000);
        Object.assign(controladores[id_chat],{'cronometroTimer_id': cronometroTimer_id});
        
        
        $('.slot').on('click',function(event){
            $('.slot').find(".panel_operadores, .colum-info").removeClass("active-chat");
            $(this).find(".panel_operadores, .colum-info").addClass("active-chat");
        });
    }

    function escribirTimer(id_chat) {
        controladores[id_chat].segundos =  controladores[id_chat].segundos - 1;

        Object.assign(controladores[id_chat],{'porcentajeBarra': (controladores[id_chat].porcentajeBarra - 100 / (controladores[id_chat].tiempoSeleccionado * 60)) });

        $("#progress_bar_"+id_chat).css("width", controladores[id_chat].porcentajeBarra + "%");
        $("#progress_bar_"+id_chat).html(new Date(controladores[id_chat].segundos * 1000).toISOString().substr(11, 8));

        if (controladores[id_chat].segundos <= controladores[id_chat].warningTime && controladores[id_chat].segundos > controladores[id_chat].dangerTime) {
            $("#progress_bar_"+id_chat).removeClass("progress-bar-success");
            $("#progress_bar_"+id_chat).addClass("progress-bar-warning");
        } else {
            if (controladores[id_chat].segundos <= controladores[id_chat].dangerTime) {
                $("#progress_bar_"+id_chat).removeClass("progress-bar-warning");
                $("#progress_bar_"+id_chat).addClass("progress-bar-danger");
            }
        }
 
        if (controladores[id_chat].segundos <= 0 && id_chat != 0 ) {
            cerrar_chat (id_chat)
            
        }
    }


    function cerrar_chat (id_chat,btn_finalizar_chat = false)
    {
        var arrayData = {
            'id_chat' : id_chat,
            
        };
        if (btn_finalizar_chat)
        {
            arrayData['btn_finalizar_chat'] = true;
        }
        //pararCronometroTimer();
        let slotVacio = $("#colum-chat-"+id_chat).closest(".slot").attr("id");
        controladores[id_chat].cronometroTimer_id = clearInterval(controladores[id_chat].cronometroTimer_id);
        $("#"+slotVacio).addClass("animate__animated animate__zoomOutDown animate__delay-.5s");
        $("#"+slotVacio).html("");
        // if (typeof(id_chat) == "undefined" && idchat < 0 )
        // {
        //     sin_casos_gestionados = true
        // }
        
        //CERRAR CHATS DESAPARECER DE LA TABLA TRACKING
        $.ajax({
			url:  base_url + 'chat_uac/ChatUAC/borrar_chat',
			type: 'POST',
            data:arrayData,

		}).done(function (response) {
			// console.log(response)

            if(typeof(pusher) != "undefined"){
                
                $.each(channels, function( index, value ) {
                    // console.log("AQUI: "+value.name)
                    if(value.name == "channel-chat-"+id_chat){
                        // console.log("AQUI: "+value.name)
                        pusher.unsubscribe(value.name);
                        value.unbind();
                    }
                    
                    
                });

                    // channels = [];
               
            }
		})
		.fail(function (response) {
       

		})
		.always(function (response) {
		
		});  

        cargarNuevoChat(slotVacio, 1);

       

    }

    function waitResponse(channel, canal)
    {
        // console.log(channel, canal);
        // panel_operadores_194577
        if (controladores[channel].bandera == true)
        {
            controladores[channel].bandera = false;
            // toastr["info"]("Tiempo Cambiado", "lapso de respuesta del cliente");
        }else{
            controladores[channel].bandera = true;
            // toastr["info"]("Tiempo Cambiado", "lapso de respuesta del operador");

        }
        clearInterval(controladores[channel].cronometroTimer_id);
        iniciarTimer(channel)
        
    }

    function formatNumber(numero) {
        let num = parseFloat(numero).toFixed(2);
        var num_parts = num.toString().split(".");
        num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return num_parts.join(",");
    }

    function send_pagare(e){
        
        var id_chat = e.getAttribute('id_chat');
        var telefono = e.getAttribute('telefono');
        var path_doc = e.getAttribute('path_doc');
        var canal = e.getAttribute('canal');

        var arrayData = 
        {
            'id_chat' : id_chat,
            'telefono' : telefono,
            'path_doc' : path_doc,
            'canal' : canal,
        };
        swal.fire({
            title: "¿Esta seguro?",
            text: "¿Estas seguro de enviar el pagare seleccionado?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, Enviar"
        }).then(function (result) {
                if (result.value) {
                    $.ajax({
                    url:  base_url + 'chat_uac/ChatUAC/send_pagare_whatsapp',
                    type: 'POST',
                    data:arrayData,

                    }).done(function (response) {
                        console.log(response)

                        
                    })
                    .fail(function (response) {
                

                    })
                    .always(function (response) {
                    
                    }); 
                        
                }
        });
         



    }
   
   
</script>