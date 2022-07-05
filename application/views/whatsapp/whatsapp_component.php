<style>
    /* ALL LOADERS */

    .loader{
    width: 100px;
    height: 100px;
    border-radius: 100%;
    position: relative;
    margin: 0 auto;
    }

    /* LOADER 6 */

    #loader-6{
    top: 40px;
    left: -2.5px;
    }

    #loader-6 span{
        display: inline-block;
        width: 5px;
        height: 30px;
        /* background-color: #605ca8; */
        background-color: rgba(255, 206, 86, 0.75);
    }

    #loader-6 span:nth-child(1){
    animation: grow 1s ease-in-out infinite;
    }

    #loader-6 span:nth-child(2){
    animation: grow 1s ease-in-out 0.15s infinite;
    }

    #loader-6 span:nth-child(3){
    animation: grow 1s ease-in-out 0.30s infinite;
    }

    #loader-6 span:nth-child(4){
    animation: grow 1s ease-in-out 0.45s infinite;
    }

    @keyframes grow{
        0%, 100%{
            -webkit-transform: scaleY(1);
            -ms-transform: scaleY(1);
            -o-transform: scaleY(1);
            transform: scaleY(1);
        }

        50%{
            -webkit-transform: scaleY(1.8);
            -ms-transform: scaleY(1.8);
            -o-transform: scaleY(1.8);
            transform: scaleY(1.8);
        }
    }

    .selection_operador {
        background-color: PALETURQUOISE !important;
    }

    .selection_chat {
        background-color: #FADBD8 !important;
    }
    .row.chat-message-user-h {
        text-align: right;
    }

    .whatsapp_textarea{
        background-color: #FFFFFF;
        margin-left: 5%;
        width: 94%;
        resize: none;
        border: #CCCCCC 1px solid;
        border-radius: 5px;
    }
    .whatsapp_textarea_disabled{
        background-color: #CCCCCC;
        margin-left: 5%;
        width: 94%;
        resize: none;
        border: #999999 1px solid;
        border-radius: 5px;
    }


    .onoffswitch {
        position: relative; width: 90px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    .onoffswitch-checkbox {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .onoffswitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #999999; border-radius: 20px;
    }
    .onoffswitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .onoffswitch-inner:before, .onoffswitch-inner:after {
        display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
        font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: "ON";
        padding-left: 10px;
        background-color: #34A7C1; color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "OFF";
        padding-right: 10px;
        background-color: #EEEEEE; color: #999999;
        text-align: right;
    }
    .onoffswitch-switch {
        display: block; width: 18px; margin: 6px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 56px;
        border: 2px solid #999999; border-radius: 20px;
        transition: all 0.3s ease-in 0s; 
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px; 
    }


    /* dropdown boleta pago*/ 
    #dropbtn {
    color: white;
    font-size: 16px;
    border: none;
    cursor: pointer;
    background-color: transparent !important;
    margin-top:5%;
    }

    .dropdown {
        display: -webkit-inline-box;
        position:absolute;
    }

    .dropdown-content {
        position:absolute;
        display: none;
        /* background-color: #f9f9f9; */
        min-width: 136px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index:9;
        background:#fff;
        width: 8%;
        /* top: 32%; */
        bottom:45%;
    }

    .dropdown-content a {
        color: black;
        padding: 6px 16px;
        text-decoration: none;
        display: block;
    }

    #botones {
        display: block;
    }

    .dropdown-content a:hover {background-color: #f1f1f1}    

    /* .dropdown:hover .dropdown-content {
        display: inline-block;
    } */

    #bontonesDepos{
        min-width: 136px;
        z-index:9;
        background:#fff;
        width: 8%;
    }

    #bontonesDepos a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    #deposito:hover #bontonesDepos{
        display: inline-block;
    }

    .bontonesDepos a:hover {background-color: #f9f9f9;}    

    #envioBoleta{
        background-color:#f7f7f7;
    }

    #envioBoleta:hover{
        background-color:#e5e4e4;
    }

</style>
<input type="hidden" id="hdd_id_operador_<?=(!empty($id_chat))?$id_chat:0?>" value="<?= (!empty($documento))?$documento:0 ?>">
<input type="hidden" id="hdd_id_solicitud_<?=(!empty($id_chat))?$id_chat:0?>" value="<?= (!empty($id_solicitud))?$id_solicitud:0 ?>">
    
<div class="panel panel-warning  main-menu-h" id="panel_mensajes_<?php echo $id_chat;?>" style="height: calc(100% - 30px);">
    <div class="panel-numeros" data-canal="188" id="chat-panel-<?php echo $id_chat;?>" >
            <div class="__chat_history_container" style="max-height: 100%;" >
                        <input type="hidden" id="paginacion-<?php echo $id_chat;?>" value = 0 class="paginacion">
                        <div class="row data-chat-show" style="margin: 0;padding-bottom: 0px;">
                            <div class="col-xs-12">
                                    <div class="row main-body " style="background: transparent !important;" ref="scroller">
                                        <div class="col-12">
                                            <div class="loader hide loader-<?php echo $id_chat;?>" id="loader-6" style="top: 10px;height: 17px;">
                                                <span style="width: 3px; height: 10px"></span>
                                                <span style="width: 3px; height: 10px"></span>
                                                <span style="width: 3px; height: 10px"></span>
                                                <span style="width: 3px; height: 10px"></span>
                                            </div>
                                            <div class="welcome" id="mensajes-<?php echo $id_chat;?>" style="padding-right: 20px; padding-left: 20px;" >                                                                    
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row help-links-<?php echo $id_chat;?>" style="padding-left: 5px;padding-right: 20px; background-color: #fff; padding-top: 10px;">
                                        
                                        
                                    </div>

                            </div>
                        </div>
            </div>
    </div>
</div>
<!-- Modal-->
<div class="modal modal-template" id="my_modal_template_<?php echo $id_chat;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
                </button>
                <h4 class="modal-title" id="myModalLabel-<?php echo $id_chat;?>">Templates</h4>
            </div>
            <div class="modal-body" style="margin: 0;padding: 0px 15px; max-height: calc(100vh - 212px); overflow-y: auto;">
                <div class="row" style="padding:0px 0px; margin-bottom: -21px;">
                    <div class="collapse" id="collapseTemplateWap-<?php echo $id_chat;?>">
                        <div class="well"></div>
                    </div>               
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/whatsapp/whatsapp_component.js')?>"></script>
<input type="hidden" id="hdd_id_chat" value="<?php echo $id_chat;?>">

<script>
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