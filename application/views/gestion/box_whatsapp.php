<style>

    .__chat_history_container {
        max-height: 452px;
        overflow-y: revert;
    }

    .main-body {
    line-height: 1;
    height: auto;
    max-height: 550px!important;
    overflow-x: hidden;
    overflow-y: auto;
    background: #fff;
    border-right: 1px solid #d8d8d8;
    border-left: 1px solid #d8d8d8;
    background-size: 11px;
    display: flex;
    flex-direction: column;
    height: 100vh;
    margin:0 ;
}

    .__chat_history_container::-webkit-scrollbar {
        width:5px;
        overflow-y: hidden;
    }
    .__chat_history_container::-webkit-scrollbar-track {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
        border-radius:10px; 
        overflow-y: hidden;
 
    }
    .__chat_history_container::-webkit-scrollbar-thumb {
        -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5);
        border-radius:10px;
        overflow-y: hidden;
  
    }
    .accordion {
        background-color: #afd879 ;
        color: #FFF;
    }
    .numeros{background-color: #cacacad9;color: #FFF;font-size: 1.1em!important;}
    .accordion-numeros:hover {color: white;background-color: #7e009d; }
    button.accordion-numeros.active {background-color: #8b0e8b!important;color:#FFF;}
    .panel-numeros {
        display: none;
        background-color: white;
        overflow: hidden;
        padding: 0px;
    }
    .accordiones{
        cursor: pointer;
        padding: 10px;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        font-size: 1em;
        transition: 0.4s;
        letter-spacing: 0.2em;
    }
    .accordion-numeros{
        background-color: #ededed;
        color: #7e009d;
        font-weight: bold;
    }
    .active.accordion-numeros:after{
        content: "🢁";
        color: white;
        font-weight: bold;
        float: right;
        margin-left:5px;
        margin-top: -2px;
    }
    .accordion-numeros:after{
        content: "🢃";
        color: white;
        font-weight: bold;
        float: right;
        margin-left:5px;
    }
    .divider_div {	
        height: 1px;
        width: 100%;
        display: block;
        margin-top: 0.5em; 
        margin-bottom: 0.5em;
        overflow: hidden;
        background-color: #E5E5E5;	
    }
    .accordion .active, .accordion:hover {
        background-color: #668c31; 
    }
    .accordion:after{
        content: "\2B9F";
        color: white;
        font-weight: bold;
        float: right;
        margin-left:5px;
    }
   
    .panel_2 >.active:after {
        content: "\2B9E";
    }
    .panel {
        padding: 0px;
        display: block;
        background-color: white;
        overflow: hidden;
    }
    .panel_2 {
        padding: 0px;
        display: none;
        background-color: white;
        overflow: hidden;
        position: absolute !important;
        z-index:1000 !important;
        max-height: 60em;
    }
    .panel_3{
        padding: 0px;
        display: none;
        background-color: white;
        overflow: hidden;
    }

        #div_send_tmpl a{
            text-decoration: none;
            color: black;
        }
        #div_send_tmpl i{
            text-decoration: none;
            color: white;
        }
        
        .habilitar_send_template{
            background:#D8D5F9;
        }
        .row.chat-message-user-h {
            text-align: right;
        }
        .panel-numeros .popover {
            border: 0px;
            max-width:600px;
        }
        .panel-numeros .popover-title {
            background-color: #f7f7f7;
            font-size: 14px;
            color: inherit;
        }

        .panel-numeros .popover-content {
            background-color: inherit;
            color: #333;
            padding: 10px;
            padding-left: 3px;
        }




/**
    ESTILO HISTORICHO CHAT
*/

    .collapse#collapseTemplateWap {
        position: absolute;
        width: 800px;
        max-height: 60%;
        border: none;
        background-color: #fff;
        overflow-y: scroll;
        box-shadow: 0px 5px 10px 0px #888888;
    }
    .collapse#collapseTemplateWap .well{
        padding: 0;
        border: none;
    }
    .collapse#collapseTemplateWap a{
        border: none;
    }
    .collapse#collapseTemplateWap .panel-group{
        margin:0px;
    }
    .collapse#collapseTemplateWap .panel-body{
        align-items: center;
        display: flex;    
    }
    .collapse#collapseTemplateWap .panel-heading{
        padding: 5px;    
    }
    

.dropbtnVent, .dropbtnCbr {
    color: white;
    font-size: 16px;
    border: none;
    cursor: pointer;
    background-color: transparent !important;
    margin-top:5%;
    display: none;
}

.dropdown {
    display: -webkit-inline-box;
    position:absolute;
}

.dropdown-content {
    position:absolute;
    display: none;
    min-width: 136px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index:9;
    background:#fff;
    width: 8%;
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

.btnSub{
    width:100%;
}
</style>
    <?php 
        /**
         * evaluamos el estado del chat princi,pal para habilitar boton enviar por wapp
         * si la variable es 0 = inactivo 
         * si la variable es 1 = activo
         */
        $status_chat_principal = 0;
    ?>
    <div id="box_whatsapp" class="box box-info __chats_list_container" style="height: 100%; overflow: hidden;scroll-behavior: smooth;overflow-y: auto;">    
 
        <div class="panel-group" id="accordion accordiones" role="tablist" aria-multiselectable="true" style="background-color:red!important;"></div>
        <div class="container">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active" data-chat="334"><a href="#334" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-headphones"></i></a></li>
                <li role="presentation" class="" data-chat="188"><a href="#188" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-dollar"></i></a></li>
            </ul>
        </div>
        <!-- Chats whatsapp -->
        <div class="panel">
            <div class="container" style="margin: 0px;display: flex;justify-content: center;align-items: center;padding: 0px;background-color:white!important;" role="presentation" id="div_send_tmpl">
                <div class="col-sm-12" style="text-align: justify; padding-right: 10px;padding-left: 10px;padding-top: 15px;padding-bottom: 0px;">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="334">
                            <!-- Listar numeros -->
                            <?php for($i=0; $i < count($datos['chats_334']); $i++){?>
                                    <?php //if(count($items) == 1){echo '<script> $(".accordion-numeros").addClass("active"); $(".panel-numeros").css("display", "block"); $("#'.$i.'")[0].click();  $("#'.$items[$i]['numero'].'334")[0].scrollTop = $("#'.$items[$i]['numero'].'334")[0].scrollHeight;</script>';}?>

                                    <button onClick="cargar(<?php echo $datos['chats_334'][$i]['id']?>)" id="<?php echo $datos['chats_334'][$i]['id'] ?>" class="accordion-numeros accordiones" style="border: 0.1em solid white;">
                                        <?php  
                                            if($datos['chats_334'][$i]['fuente'] == 'PERSONAL DECLARADO' || $datos['chats_334'][$i]['fuente'] == 'PERSONAL') { 
                                                if($datos['chats_334'][$i]['status_chat'] == "activo"){
                                                    $status_chat_principal = 1;
                                                }
                                                echo '<i class="fa fa-star" data-status="'.$datos['chats_334'][$i]["status_chat"].'" style="color: #efff03; position: absolute;"></i>'; 
                                            }
                                        ?>
                                        <div style="float: left;padding-left: 5%;">
                                        <?= $datos['chats_334'][$i]['from']."-".$datos['chats_334'][$i]['contacto'].((!is_null($datos['chats_334'][$i]['Nombre_Parentesco']))? " - ".$datos['chats_334'][$i]['Nombre_Parentesco']:'');?></div>
                                    </button>
                                    <div class="panel-numeros" data-canal="334" id="<?php echo $datos['chats_334'][$i]['id']."-panel";?>" data-numero="<?php echo $datos['chats_334'][$i]['from'];?>">
                                        <div class="__chat_history_container" style="max-height: 100%;" >
                                            <div class="container">
                                                <input type="hidden" id="<?php echo $datos['chats_334'][$i]["id"]."-paginacion"; ?>" value = 0 class="paginacion">
                                                <div class="row" style="width:100%">
                                                    <div class="col-xs-12">
                                                        <div class="data-chat-show" style="margin-bottom: 10px;">
                                                            <div class="row main-body main-menu main-menu-h" ref="scroller">
                                                                <div class="col-md-12 col-lg-12 col-sm-12">
                                                                    <div class="loader" id="loader-6" style="top: 10px;height: 17px;">
                                                                        <span style="width: 3px; height: 10px"></span>
                                                                        <span style="width: 3px; height: 10px"></span>
                                                                        <span style="width: 3px; height: 10px"></span>
                                                                        <span style="width: 3px; height: 10px"></span>
                                                                    </div>
                                                                    <div class="welcome" id="<?php echo $datos['chats_334'][$i]["id"].'welcome';?>">                                                                    
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-11" style="padding: 0px;margin-left: -15px;">
                                                            <textarea id="<?php echo $datos['chats_334'][$i]["id"]."-mensaje"; ?>" class="form-control" rows="2" style="width: 100%; resize: none;" <?php echo ($datos['chats_334'][$i]["status_chat"] != "activo")? "disabled" : "";?> ></textarea>
                                                        </div>
                                                        <div class="col-xs-1" style="margin-right: -15px;padding-top: 10px;">
                                                            <a class="btn btn-success send-messages  <?php echo ($datos['chats_334'][$i]["status_chat"] != "activo")? "disabled" : "";?>" onclick='<?php echo "sendMessages(".$datos['chats_334'][$i]['id'].", 334)" ?>' ><i class="fa fa-send"></i></a>
                                                        </div>
                                                        <div class="col-xs-12" style="padding: 0px;margin-left: -15px;padding-top: 10px;">
                                                            <a class="btn templates <?php echo ($datos['chats_334'][$i]["status_chat"] == "activo")? "disabled" : "";?>" role="button" data-toggle="collapse" style="padding:0px;" aria-expanded="false" aria-controls="collapseTemplateWap" onclick="get_templates_wapp(1, '<?php echo $datos['chats_334'][$i]['from'];?>')"> <img src="../public/chat_files/img/templates_aprobados.png" style="width:30px" /></a>
                                                            
                                                            <a class="btn adjuntos <?php echo ($datos['chats_334'][$i]["status_chat"] != "activo")? "disabled" : "";?>" href="javascript:void(0)" title="Agregar imagen" style="padding:0px;">
                                                                    <label  title="Subir imagen" style="padding: 0px 5px;font-size: x-large;margin-bottom:0px">
                                                                        <img src="../public/chat_files/img/adjunto.png" style="width:30px" /></i>
                                                                        <?php //if ($datos['chats_334'][$i]["status_chat"] == "activo"){?>
                                                                            <input id="<?php echo $datos['chats_334'][$i]["id"]."-adjunto"; ?>" data-channel="<?php echo $datos['chats_334'][$i]["id"]; ?>" data-canal="334" type="file" name="media" accept=".gif,.pdf,.jpeg,.jpg,.png,.pdf" style="display: none;">
                                                                        <?php //}?>
                                                                    </label>
                                                            </a>

                                                            <button 
                                                                id="<?=$datos['chats_334'][$i]["id"]."-popover-ws"; ?>" 
                                                                class="btn selfie <?=($datos['chats_334'][$i]["status_chat"] != "activo")? "disabled" : "";?>" 
                                                                style="padding:0px" 
                                                                onclick="box_whatsapp.popover(this)" data-from_veriff_ws="<?=$datos['chats_334'][$i]['from']?>" 
                                                                data-id_veriff_ws="<?=$datos['chats_334'][$i]['id']?>" 
                                                                data-biometriaws="<?=(isset($datos['permisos']['operador']->biometriaws))? $datos['permisos']['operador']->biometriaws : 0 ?>" > <img src="<?=base_url('public/chat_files/img/selfie.png'); ?>" style="width:30px" /></button>

                                                            <a class="btn hide linkPago   <?php echo ($datos['chats_334'][$i]["status_chat"] != "activo")? "disabled" : "";?>" role="button" data-canal-chat="ventas" data-mobilephone = "<?php echo $datos['chats_334'][$i]['from'];?>" data-medio-pago="efectivo" style="padding:0px" aria-expanded="false" onclick="envioLinkDePago(this)" title = "Enviar link Efectivo Baloto, Gana, Daviplata, otros" > <img src="../public/chat_files/img/efectivo.png" style="width:30px" /></a>
                                                            <a class="btn hide linkPago  <?php echo ($datos['chats_334'][$i]["status_chat"] != "activo")? "disabled" : "";?>" role="button" data-canal-chat="ventas" data-mobilephone = "<?php echo $datos['chats_334'][$i]['from'];?>" data-medio-pago="PSE" style="padding:0px" aria-expanded="false" onclick="envioLinkDePago(this)" title = "Enviar link PSE"> <img src="../public/chat_files/img/pse.png" style="width:30px" /></a>
                        
                                                            <!-- VENTAS -->
                                                            <?php if($datos['chats_334'][$i]['fuente'] == 'PERSONAL DECLARADO' || $datos['chats_334'][$i]['fuente'] == 'PERSONAL') { ?>
                                                                <input type="hidden" id="cl_documento" value="<?php echo ($datos['chats_334'][$i]['documento']);?>"></input>
                                                            <?php } ?>
                                                            <div class="dropdown">
                                                                <button id="btnVent" class="dropbtnVent" estado="oculto"><i class="fa fa-share-alt-square fa-2x" aria-hidden="true" id="iconoBoleta" style="color:#3EC79B;"></i></button>
                                                                <div class="dropdown-content" id="contenido_ventas">
                                                                    <div id="botones">
                                                                    <div class="envioWhatsapp">
                                                                            <a class="btnSub" id="btnEfecty<?php echo $i ?>" canal="1" seleccionado="btnEfecty<?php echo $i ?>" codConv="111694" sbmenu="botonesEnv" submenu="bontonesEfecty<?php echo $i ?>" metodo="'efecty'" telefono="<?php echo ($datos['chats_334'][$i]['from']); ?>" documento="<?php echo ($datos['chats_334'][$i]['documento']); ?>">Efecty</a>
                                                                            <div id="bontonesEfecty<?php echo $i ?>" class="botonesEnv"></div>
                                                                        </div>
                                                                        <!-- <div class="envioWhatsapp">
                                                                            <a class="btnSub" canal="1" id="btnBaloto<?php echo $i ?>" submenu="bontonesBaloto<?php echo $i ?>" codConv="952784" sbmenu="botonesEnv" metodo="'baloto'" seleccionado="btnBaloto<?php echo $i ?>" telefono="<?php echo ($datos['chats_334'][$i]['from']); ?>" documento="<?php echo ($datos['chats_334'][$i]['documento']); ?>">Baloto</a>
                                                                            <div id="bontonesBaloto<?php echo $i ?>" class="botonesEnv"></div>
                                                                        </div> -->
                                                                        <div class="envioWhatsapp">
                                                                            <a class="btnSub" submenu="bontonesCorres<?php echo $i ?>" id="btnCorres<?php echo $i ?>" seleccionado="btnCorres<?php echo $i ?>" codConv="90652" sbmenu="botonesEnv" canal="1" metodo="'corresponsal'" telefono="<?php echo ($datos['chats_334'][$i]['from']); ?>" documento="<?php echo ($datos['chats_334'][$i]['documento']); ?>">Corresponsal</a>
                                                                            <div id="bontonesCorres<?php echo $i ?>" class="botonesEnv"></div>
                                                                        </div>
                                                                        <div class="envioWhatsapp">
                                                                            <a class="btnSub" submenu="bontonesDepos<?php echo $i ?>" id="btnDespo<?php echo $i ?>" seleccionado="btnDespo<?php echo $i ?>" codConv="90652" sbmenu="botonesEnv" style="color:black;" canal="1" metodo="'deposito'" telefono="<?php echo ($datos['chats_334'][$i]['from']); ?>" documento="<?php echo ($datos['chats_334'][$i]['documento']); ?>">Deposito</a>
                                                                            <div id="bontonesDepos<?php echo $i ?>" class="botonesEnv"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>                    
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                if ($i < count($datos['chats_334'])-1 ) {
                                    echo  '<div role="separator" class="divider_div"></div>';
                                }
                            } ?>
                        </div>
                        
                        <div role="tabpanel" class="tab-pane" id="188">
                            <!-- Listar numeros -->
                            <?php for($i=0; $i < count($datos['chats_188']); $i++){?>
                                    <?php //if(count($items) == 1){echo '<script> $(".accordion-numeros").addClass("active"); $(".panel-numeros").css("display", "block"); $("#'.$i.'")[0].click();  $("#'.$items[$i]['numero'].'188")[0].scrollTop = $("#'.$items[$i]['numero'].'188")[0].scrollHeight;</script>';}?>
                                    <button onClick="cargar(<?php echo $datos['chats_188'][$i]['id']?>)" id="<?php echo $datos['chats_188'][$i]['id'] ?>" class="accordion-numeros accordiones" style="border: 0.1em solid white;">
                                        <?php  
                                            if($datos['chats_188'][$i]['fuente'] == 'PERSONAL DECLARADO') { 
                                                echo '<i class="fa fa-star" style="color: #efff03; position: absolute;"></i>'; 
                                            }
                                        ?>
                                        <div style="float: left;padding-left: 5%;">
                                        <?= $datos['chats_188'][$i]['from']."-".$datos['chats_188'][$i]['contacto'].((!is_null($datos['chats_188'][$i]['Nombre_Parentesco']))? " - ".$datos['chats_188'][$i]['Nombre_Parentesco']:'');?></div>
                                    </button>
                                    <div class="panel-numeros" data-canal="188" id="<?php echo $datos['chats_188'][$i]['id']."-panel";?>" data-numero="<?php echo $datos['chats_188'][$i]['from'];?>">
                                        <div class="__chat_history_container" style="max-height: 100%;" >
                                            <div class="container">
                                                <input type="hidden" id="<?php echo $datos['chats_188'][$i]["id"]."-paginacion"; ?>" value = 0 class="paginacion">
                                                <div class="row" style="width:100%">
                                                    <div class="col-xs-12">
                                                        <div class="data-chat-show" style="margin-bottom: 10px;">
                                                            <div class="row main-body main-menu main-menu-h" ref="scroller">
                                                                <div class="col-md-12 col-lg-12 col-sm-12">
                                                                    <div class="loader" id="loader-6" style="top: 10px;height: 17px;">
                                                                        <span style="width: 3px; height: 10px"></span>
                                                                        <span style="width: 3px; height: 10px"></span>
                                                                        <span style="width: 3px; height: 10px"></span>
                                                                        <span style="width: 3px; height: 10px"></span>
                                                                    </div>
                                                                    <div class="welcome" id="<?php echo $datos['chats_188'][$i]["id"].'welcome';?>">                                                                    
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-11" style="padding: 0px;margin-left: -15px;">
                                                            <textarea id="<?php echo $datos['chats_188'][$i]["id"]."-mensaje"; ?>" class="form-control" rows="2" style="width: 100%; resize: none;" <?php echo ($datos['chats_188'][$i]["status_chat"] != "activo")? "disabled" : "";?> ></textarea>
                                                        </div>
                                                        <div class="col-xs-1" style="margin-right: -15px;padding-top: 10px;">
                                                            <a class="btn btn-success send-messages <?php echo ($datos['chats_188'][$i]["status_chat"] != "activo")? "disabled" : "";?>" onclick='<?php echo "sendMessages(".$datos['chats_188'][$i]['id'].", 188)" ?>' ><i class="fa fa-send"></i></a>
                                                        </div>
                                                        <div class="col-xs-12" style="padding: 0px;margin-left: -15px;padding-top: 10px;">
                                                            <a class="btn templates <?php echo ($datos['chats_188'][$i]["status_chat"] == "activo")? "disabled" : "";?>" role="button" data-toggle="collapse" style="padding:0px;" aria-expanded="false" aria-controls="collapseTemplateWap" onclick="get_templates_wapp(2, '<?php echo $datos['chats_188'][$i]['from'];?>')"> <img src="../public/chat_files/img/templates_aprobados.png" style="width:30px" /></a>
                                                            
                                                            <a class="btn adjuntos <?php echo ($datos['chats_188'][$i]["status_chat"] != "activo")? "disabled" : "";?>" href="javascript:void(0)" title="Agregar imagen" style="padding:0px;">
                                                                    <label  title="Subir imagen" style="padding: 0px 5px;font-size: x-large;margin-bottom:0px">
                                                                        <img src="../public/chat_files/img/adjunto.png" style="width:30px" /></i>
                                                                        <?php //if ($datos['chats_188'][$i]["status_chat"] == "activo"){?>
                                                                            <input id="<?php echo $datos['chats_188'][$i]["id"]."-adjunto"; ?>" data-channel="<?php echo $datos['chats_188'][$i]["id"]; ?>" data-canal="188" type="file" name="media" accept=".gif,.pdf,.jpeg,.jpg,.png,.pdf" style="display: none;">
                                                                        <?php //}?>
                                                                    </label>
                                                            </a>

                                                            <a class="btn hide linkPago  <?php echo ($datos['chats_188'][$i]["status_chat"] != "activo")? "disabled" : "";?>" role="button" data-canal-chat="cobranzas" data-mobilephone = "<?php echo $datos['chats_188'][$i]['from'];?>" data-medio-pago="efectivo" style="padding:0px" aria-expanded="false" onclick="envioLinkDePago(this)" title = "Enviar link Efectivo Baloto, Gana, Daviplata, otros" > <img src="../public/chat_files/img/efectivo.png" style="width:30px" /></a>
                                                            <a class="btn hide linkPago  <?php echo ($datos['chats_188'][$i]["status_chat"] != "activo")? "disabled" : "";?>" role="button" data-canal-chat="cobranzas" data-mobilephone = "<?php echo $datos['chats_188'][$i]['from'];?>" data-medio-pago="PSE" style="padding:0px" aria-expanded="false" onclick="envioLinkDePago(this)" title = "Enviar link PSE"> <img src="../public/chat_files/img/pse.png" style="width:30px" /></a>
                        
                                                            <!-- COBRANZAS -->
                                                            <div class="dropdown" >
                                                                <button id="btnCbr" class="dropbtnCbr" estado="oculto"><i class="fa fa-share-alt-square fa-2x" aria-hidden="true" id="iconoBoleta" style="color:#3EC79B;"></i></button>
                                                                <div class="dropdown-content" id="contenido_cobranza">
                                                                    <div id="botones">
                                                                    <div class="envioWhatsapp">
                                                                            <a class="btnSub" id="btnEfectyCbr<?php echo $i ?>" canal="2" seleccionado="btnEfectyCbr<?php echo $i ?>" codConv="111694" submenu="bontonesEfectyCobr<?php echo $i ?>" sbmenu="botonesEnvCbr" metodo="'efecty'" telefono="<?php echo ($datos['chats_334'][$i]['from']); ?>" documento="<?php echo ($datos['chats_334'][$i]['documento']); ?>">Efecty</a>
                                                                            <div id="bontonesEfectyCobr<?php echo $i ?>" class="botonesEnvCbr"></div>
                                                                        </div>
                                                                        <!-- <div class="envioWhatsapp">
                                                                            <a class="btnSub" canal="2" id="btnBalotoCbr<?php echo $i ?>" submenu="bontonesBalotoCbr<?php echo $i ?>" codConv="952784" sbmenu="botonesEnvCbr" metodo="'baloto'" seleccionado="btnBalotoCbr<?php echo $i ?>" telefono="<?php echo ($datos['chats_334'][$i]['from']); ?>" documento="<?php echo ($datos['chats_334'][$i]['documento']); ?>">Baloto</a>
                                                                            <div id="bontonesBalotoCbr<?php echo $i ?>" class="botonesEnvCbr"></div>
                                                                        </div> -->
                                                                        <div class="envioWhatsapp">
                                                                            <a class="btnSub" submenu="bontonesCorresCbr<?php echo $i ?>" sbmenu="botonesEnvCbr" id="btnCorresCbr<?php echo $i ?>" codConv="90652" seleccionado="btnCorresCbr<?php echo $i ?>" canal="2" metodo="'corresponsal'" telefono="<?php echo ($datos['chats_334'][$i]['from']); ?>" documento="<?php echo ($datos['chats_334'][$i]['documento']); ?>">Corresponsal</a>
                                                                            <div id="bontonesCorresCbr<?php echo $i ?>" class="botonesEnvCbr"></div>
                                                                        </div>
                                                                        <div class="envioWhatsapp">
                                                                            <a class="btnSub" submenu="bontonesDeposCbr<?php echo $i ?>" sbmenu="botonesEnvCbr" id="btnDespoCbr<?php echo $i ?>" codConv="90652" seleccionado="btnDespoCbr<?php echo $i ?>" style="color:black;" canal="2" metodo="'deposito'" telefono="<?php echo ($datos['chats_334'][$i]['from']); ?>" documento="<?php echo ($datos['chats_334'][$i]['documento']); ?>">Deposito</a>
                                                                            <div id="bontonesDeposCbr<?php echo $i ?>" class="botonesEnvCbr"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>  
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   
                                
                            <?php 
                                if ($i < count($datos['chats_188'])-1 ) {
                                    echo  '<div role="separator" class="divider_div"></div>';
                                }
                            } ?>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="status_chat_principal"  value="<?php echo $status_chat_principal; ?>">

    <div class="collapse" id="collapseTemplateWap">
        <div class="well"></div>
    </div>

    <script type="text/javascript">

    jQuery(document).ready(function() {
       
        $("ul.nav.nav-tabs").on("click",function () {
            $(".well").html("");
        });
        $('#box_whatsapp textarea').keypress(function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                $(this).closest('.col-xs-12').find('.send-messages').click();
            }
        });
        var acc = document.getElementsByClassName("accordion");
        var h;

        for (h = 0; h < acc.length; h++) {
            acc[h].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                }
            });
        }
        var acc2 = document.getElementsByClassName("accordion_2");
        var h2;

        for (h2 = 0; h2 < acc2.length; h2++) {
            acc2[h2].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel2 = this.nextElementSibling;
                if (panel2.style.display === "block") {
                    panel2.style.display = "none";
                } else {
                    panel2.style.display = "block";
                }
            });
        }
        
        var acc3 = document.getElementsByClassName("accordion-numeros");
        for (var i = 0; i < acc3.length; i++) {
            acc3[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    $('.panel-numeros').css('display', 'none');
                    panel.style.display = "block";
                    let id = panel.id;
                    var element = $("#"+id+" .main-menu")[0];
                    element.scrollTop = element.scrollHeight;
                }

            });

        }


        $(".adjuntos input").on("change", function (element) {
            Swal.fire({
                title: 'Envio de archivo',
                text: 'Enviar el archivo seleccionado?',
                icon: 'warning',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                showCancelButton: 'true'
            }).then((result) => {
                if (result.value) {
                    sendMessages($(this).data("channel"), $(this).data("canal"), $(this)[0].files[0]);
                }
            });
        });
        
        if($("#id_solicitud").data('tipo') != "PRIMARIA"){
            $("a.selfie").addClass('disabled');
            $("a.biometria").addClass('disabled');
        } else {
            let estados = ["APROBADO", "TRANSFIRIENDO", "RECHAZADO", "PAGADO","ANULADO"];

            if (estados.includes($("#id_solicitud").data('status')) || ($("#id_solicitud").data('paso') != 13 && $("#id_solicitud").data('paso') !=16)) {
                $("a.selfie").addClass('disabled');
                $("a.biometria").addClass('disabled');
            } else{
                if( $("#status_chat_principal").val() == "1"){
                    $("a.biometria").removeClass('disabled');
                }
            }
        }

        if($('input#credito').data("status").toUpperCase() == "VIGENTE" || $('input#credito').data("status").toUpperCase() == "MORA" || $('.estado-credito').html().toUpperCase() == "[VIGENTE]" || $('.estado-credito').html().toUpperCase() == "[MORA]"){
            $("a.linkPago").removeClass("hide");
        }
    });

    
    function cargar(id_chat){
        $('.accordion-numeros').click(function(e){
            e.preventDefault();
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
        });
        

        if ($("#"+id_chat+'-paginacion').val() == 0) {
            get_mensajes_chat(id_chat, paginacion);
                        
            var channel = pusher.subscribe('channel-chat-'+id_chat);
            channels.push(channel);

            channel.bind('received-message-component', function(data) {
                displayMessage(data);
            });
            channel.bind('sent-message-component', function(data) {
                displayMessage(data);
            });
            channel.bind('message-status', function(data) {
                status_sms(data.status, data.messageID);
            });
        }
        
        $("#"+id_chat+"-panel .main-menu").scroll(function () {
            if ($(this).scrollTop() === 0) {
                $("#"+id_chat+"-panel .loader").removeClass('hide');
                get_mensajes_chat(id_chat, $("#"+id_chat+'-paginacion').val());
            }
        });

        if(parseInt($("#paso-solicitud").html()) < 13){
            $("a.selfie").addClass('disabled');
        }

        let documento = $("#cl_documento").val();
        $.ajax({
            type: "post",
            url: base_url + "api/ApiSolicitud/buscarCredito",
            data: {"documento":documento},
            success: function (respuesta) {
                response = JSON.parse(respuesta);
                if (response == true) {
                    $(".dropbtnVent").css("display", "block");
                    $(".dropbtnCbr").css("display", "block");
                }
            }
        });
        
        let botones = $("#botones");
        botones.attr('unselectable', 'on').css('user-select', 'none').on('selectstart dragstart', false);
    }

    function get_mensajes_chat(id_chat, paginacion) {
        let base_url = $("#base_url").val();
        const formData = new FormData();
        formData.append("chat", id_chat);
        formData.append("pagina", paginacion);

        let objDiv = $("#"+id_chat+"-panel .main-menu");
        if (objDiv.length > 0) {
            objDiv = $("#"+id_chat+"-panel .main-menu")[0];
        } else {
            objDiv = null;
        }
        let scrollHeightOld = objDiv.scrollHeight;

        if (paginacion > -1) {
            $.ajax({
                url: base_url + 'solicitud/gestion/api/whatsapp_paginado',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            })
                .done(function (response) {
                    let elementos = '';
                    if (response.status.ok && typeof (response.chat) != 'undefined') {

                        let chats = response.chat;

                        chats.forEach(chat => {
                            let mensajes = chat.messages;

                            mensajes.forEach(msg => {
                                elementos += '<div class="row chat-message-bot mb-4';
                                elementos += (msg.received == 1) ? ' chat-message-bot" style="padding-left: 5px;' : '';
                                elementos += (msg.sent == 1) ? ' chat-message-user-h' : '';
                                elementos += '">';

                                elementos += (msg.sent == 1) ? '<div style="display:flex;width:100%;justify-content:flex-end;"></div>' : '';

                                elementos += '<table class="';
                                elementos += (msg.received == 1) ? ' chat-entry-bot' : '';
                                elementos += (msg.sent == 1) ? ' chat-entry-user' : '';
                                elementos += '">';
                                elementos += '<tbody><tr>';
                                //elementos += (msg.received == 1) ? ('<td class="davi-icon"></td>') : '';
                                elementos += '<td><div class="bubble"><p class="__msg_body">' + nl2br(msg.body ,false) + '</p>';

                                elementos += (msg.media_url0 && (msg.media_content_type0 == 'image/jpeg' || msg.media_content_type0 == 'image/gif' || msg.media_content_type0 == 'image/png')) ? '<img src="' + msg.media_url0 + '" alt="Mensaje Multimedia" class="message_image mt-4 mb-3 d-block"  style="width: 100%!important;">' : '';
                                elementos += (msg.media_url0 && msg.media_content_type0 == 'application/pdf') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= "' + base_url + 'assets/images/icons/pdf-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
                                elementos += (msg.media_url0 && msg.media_content_type0 == 'text/csv') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= "' + base_url + 'assets/images/icons/excel-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
                                elementos += (msg.media_url0 && (msg.media_content_type0 == 'audio/amr' || msg.media_content_type0 == 'audio/mp4' || msg.media_content_type0 == 'audio/mpeg' || msg.media_content_type0 == 'audio/ogg')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-headphones" aria-hidden="true" style="margin-right:.5rem;"></i> Escuchar audio</a>') : '';
                                elementos += (msg.media_url0 && (msg.media_content_type0 == 'video/3gpp' || msg.media_content_type0 == 'video/mp4')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-eye " aria-hidden="true" style="margin-right:.5rem;"></i> Ver Video</a>') : '';

                                elementos += '</div><div class="message-date">' + moment(msg.fecha_creacion).format('DD-MM-YYYY h:mm:ss') + '<br>';
                                elementos += (msg.received == 0 && typeof (msg.nombre_apellido_operador) != 'undefined' && msg.nombre_apellido_operador != null) ? msg.nombre_apellido_operador : '';
                                elementos += (msg.received == 0 && typeof (msg.nombre_apellido_operador) == 'undefined' && typeof (chat.operadores.nombre_apellido) != 'undefined') ? chat.operadores.nombre_apellido : '';
                                elementos += '</div></td>';
                                if (msg.sent == 1) {
                                    elementos += '<td class="davi-icon" id="sent-'+msg.sms_message_sid+'">';
                                    if (msg.sms_status === 'queued' || msg.sms_status === 'sent') {
                                        elementos += '<img src="' + base_url + 'assets/images/icons/single-grey.svg" alt="status icon" class="__status_icon">';
                                    }
                                    if (msg.sms_status === 'delivered') {
                                        elementos += '<img src="' + base_url + 'assets/images/icons/double-grey.svg" alt="status icon" class="__status_icon">';
                                    }
                                    if (msg.sms_status === 'read') {
                                        elementos += '<img src="' + base_url + 'assets/images/icons/double-blue.svg" alt="status icon" class="__status_icon">';
                                    }
                                    if (msg.sms_status === 'failed') {
                                        elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
                                    }
                                    if (msg.sms_status === 'failed') {
                                        elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
                                    }

                                    elementos += '</td>';
                                }
                                elementos += '</tr></tbody></table>';

                                elementos += (msg.sent == 1) ? '</div>' : '';

                                elementos += '</div>';

                            });
                        });
                    
                        $('.loader').addClass('hide');

                            
                        $('#'+id_chat+ 'welcome').prepend(elementos);
                        $('#'+id_chat+'-paginacion').val(response.paginacion);
                                
                    
                        let element = $("#"+id_chat+"-panel .main-menu")[0];
                        element.scrollTop = (element.scrollHeight - scrollHeightOld);


                    }
                    $('.loader').addClass('hide');

                })
                .fail(function () {
                })
                .always(function () {
                });
        } else {
            $('.loader').addClass('hide');


        }

    }

    function displayMessage(msg) {
        elementos='';
        elementos += '<div class="row chat-message-bot mb-4';
        elementos += (msg.received == 1) ? ' chat-message-bot style="padding-left: 5px;' : '';
        elementos += (msg.sent == 1) ? ' chat-message-user-h' : '';
        elementos += '">';

        elementos += (msg.sent == 1) ? '<div style="display:flex;width:100%;justify-content:flex-end;"></div>' : '';

        elementos += '<table class="';
        elementos += (msg.received == 1) ? ' chat-entry-bot' : '';
        elementos += (msg.sent == 1) ? ' chat-entry-user' : '';
        elementos += '">';
        elementos += '<tbody><tr>';
        //elementos += (msg.received == 1) ? ('<td class="davi-icon"></td>') : '';
        elementos += '<td><div class="bubble"><p class="__msg_body">' + nl2br(msg.body ,false) + '</p>';


        elementos += (msg.media_url0 && (msg.media_content_type0 == 'image/jpeg' || msg.media_content_type0 == 'image/gif' || msg.media_content_type0 == 'image/png')) ? '<img src="' + msg.media_url0 + '" alt="Mensaje Multimedia" class="message_image mt-4 mb-3 d-block"  style="width: 100%!important;">' : '';
        elementos += (msg.media_url0 && msg.media_content_type0 == 'application/pdf') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= ' + base_url + '"assets/images/icons/pdf-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
        elementos += (msg.media_url0 && msg.media_content_type0 == 'text/csv') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= ' + base_url + '"assets/images/icons/excel-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
        elementos += (msg.media_url0 && (msg.media_content_type0 == 'audio/amr' || msg.media_content_type0 == 'audio/mp4' || msg.media_content_type0 == 'audio/mpeg' || msg.media_content_type0 == 'audio/ogg')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-headphones" aria-hidden="true" style="margin-right:.5rem;"></i> Escuchar audio</a>') : '';
        elementos += (msg.media_url0 && (msg.media_content_type0 == 'video/3gpp' || msg.media_content_type0 == 'video/mp4')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-eye " aria-hidden="true" style="margin-right:.5rem;"></i> Ver Video</a>') : '';


        elementos += '</div><div class="message-date">' + moment(msg.fecha_creacion).format('DD-MM-YYYY h:mm:ss') + '<br>';
        elementos += (msg.received == 0 && typeof (msg.nombre_apellido_operador) != 'undefined' && msg.nombre_apellido_operador != null) ? msg.nombre_apellido_operador : '';
        elementos += (msg.received == 0 && typeof (msg.nombre_apellido_operador) == 'undefined' && typeof (chat.operadores.nombre_apellido) != 'undefined') ? chat.operadores.nombre_apellido : '';
        elementos += '</div></td>';
        if (msg.sent == 1) {
            elementos += '<td class="davi-icon" id="sent-'+msg.sms_sid+'">';
            if (msg.sms_status === 'queued' || msg.sms_status === 'sent') {
                elementos += '<img src="' + base_url + 'assets/images/icons/single-grey.svg" alt="status icon" class="__status_icon">';
            }
            if (msg.sms_status === 'delivered') {
                elementos += '<img src="' + base_url + 'assets/images/icons/double-grey.svg" alt="status icon" class="__status_icon">';
            }
            if (msg.sms_status === 'read') {
                elementos += '<img src="' + base_url + 'assets/images/icons/double-blue.svg" alt="status icon" class="__status_icon">';
            }
            if (msg.sms_status === 'failed') {
                elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
            }
            if (msg.sms_status === 'failed') {
                elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
            }

            elementos += '</td>';
        }
        elementos += '</tr></tbody></table>';

        elementos += (msg.sent == 1) ? '</div>' : '';

        elementos += '</div>';

        $('#'+msg.id_chat+ 'welcome').append(elementos);

        let element = $("#"+msg.id_chat+"-panel .main-menu")[0];
        element.scrollTop = element.scrollHeight;

        //habilitamos todos los campos del chat
        //send-messages 
        if(msg.received == 1){
             
            $("#"+msg.id_chat+"-panel a.send-messages").removeClass("disabled");
            $("#"+msg.id_chat+"-panel a.adjuntos").removeClass("disabled");
            $("#"+msg.id_chat+"-panel textarea").prop("disabled", false);
            $("#"+msg.id_chat+"-panel a.templates").addClass("disabled");
            if($("#id_solicitud").data('tipo') == "PRIMARIA"){
                $("#"+msg.id_chat+"-panel a.selfie").removeClass('disabled');
            }
        }
    }

    function sendMessages(channel, canal, file = '') {
        let base_url = $("#base_url").val();
        const formData = new FormData();
        let mensaje = $("#"+channel+"-mensaje").val()
        let controlMessage = mensaje.replace(/[\n\r\s]+/gi, '');
        
        
        if (controlMessage.length > 0  || file !== '') {

            formData.append('media', file);
            formData.append('message', mensaje);
            formData.append('chatID', channel);
            formData.append('operatorID', $("#id_operador").val());
            if (canal == 188) {
                base_url += "comunicaciones/TwilioCobranzas/send_new_message"
            }
            if (canal == 334) {
                base_url += "comunicaciones/Twilio/send_new_message"
            }
            $("#"+channel+"-panel a.send-messages").addClass("disabled");
            
            $.ajax({
                url:  base_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {

                if (typeof(response.error) == "undefined") {
                    let cant = response.messages.messages.length - 1;
                    //displayMessage(response.messages.messages[cant]);
                    $("#"+channel+"-mensaje").val('');
                    
                } else {
                    Swal.fire('¡Atención!',response.description, 'error')
                }
                $("#"+channel+"-adjunto").val('');
                $("#"+channel+"-panel a").removeClass("disabled");

            })
            .fail(function (response) {
                Swal.fire('¡Atención!','No fue posible establecer la comunicacion', 'error');
                $("#"+channel+"-adjunto").val('');
                $("#"+channel+"-panel a.send-messages").removeClass("disabled");

            })
            .always(function (response) {
                //console.log("complete");
            });  
        }

    }

    function status_sms(status, mensaje) {
        let elementos = '';
        if (status === 'queued' || status === 'sent') {
            elementos += '<img src="' + base_url + 'assets/images/icons/single-grey.svg" alt="status icon" class="__status_icon">';
        }
        if (status === 'delivered') {
            elementos += '<img src="' + base_url + 'assets/images/icons/double-grey.svg" alt="status icon" class="__status_icon">';
        }
        if (status === 'read') {
            elementos += '<img src="' + base_url + 'assets/images/icons/double-blue.svg" alt="status icon" class="__status_icon">';
        }
        if (status === 'failed') {
            elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
        }
        if (status === 'failed') {
            elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
        }

        $("#sent-"+mensaje).html(elementos);
    }

    function get_templates_wapp(canal, numero) {
        let id = $("#id_solicitud").val();
        let base_url = $("#base_url").val();
        
        if ($("#collapseTemplateWap .well").html() == "") {
            $.ajax({
                url: base_url + 'atencion_cliente/makeTemplateSend/'+id+'/'+canal+'/WAPP',
                type: 'GET',
            })
                .done(function (response) {
                    let defecto = elementos = '';
                    let grupos = JSON.parse(response).grupo_template;

                    elementos += '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';
                    grupos.forEach(grupo => {
                            let templates = grupo.template;

                            elementos += '<div class="panel panel-success">';
                            elementos += '<div class="panel-heading" role="tab" id="heading-'+grupo.grupo+'">';
                            elementos += '<h4 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-'+grupo.grupo+'" aria-expanded="true" aria-controls="collapse-'+grupo.grupo+'">';
                            elementos += grupo.grupo + '</a></h4></div>'
                            elementos += '<div id="collapse-'+grupo.grupo+'" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading-'+grupo.grupo+'">';
                            
                            templates.forEach(template => {
                                if (template.por_defecto === "1") {
                                    defecto += '<div class="panel-body"><div class="col-sm-11 mensaje">'+template.msg_string+'</div>';
                                    defecto += '<div class="col-sm-1"><button data-proveedor="1" data-id_template="'+template.id+'" class="btn btn-success btn-sm" disabled><i class="fa fa-send"></i></button>';
                                    defecto += '</div></div>';            
                                }
                                
                                elementos += '<div class="panel-body">';
                                elementos += '<div class="col-sm-11 mensaje">'+template.msg_string+'</div>';
                                elementos += '<div class="col-sm-1">';
                                elementos += '<button data-proveedor="1" data-tipo_template="WAPP" data-id_template="'+template.id+'" class="btn btn-success btn-sm" disabled><i class="fa fa-send"></i></button>';
                                elementos += '</div></div>';
                            });
                            elementos += '</div></div>';
                            
                            
                        });
                        elementos += '</div>';
                        
                    $("#collapseTemplateWap .well").html(defecto+' '+elementos);

                    $("#collapseTemplateWap .panel-body").on('click', function () {
                        $("#collapseTemplateWap .panel-body").removeClass("habilitar_send_template");
                        $(this).addClass("habilitar_send_template");

                        $("#collapseTemplateWap .panel-body button").prop("disabled", true);
                        $(this).find("button").prop("disabled", false);

                        $("#collapseTemplateWap .panel-body button").prop("onclick", null);
                        
                        $(this).find("button").on("click", function () {
                            let mensaje = $(this).closest(".panel-body").find(".mensaje").text();
                            let template = $(this).data("id_template");
                            send_template(numero, mensaje, template, canal);
                        });

                    });
                    let ele = $(".accordion-numeros.accordiones.active").prop("id");
                    let tope = parseInt($("#"+ele+"-panel a.templates").offset().top) - $("#collapseTemplateWap").height() - 160;
                    if(tope < 0)
                        tope = parseInt($("#"+ele+"-panel a.templates").offset().top) + $("#collapseTemplateWap").height() + 20;
                    
                    $("#collapseTemplateWap").css("top", tope+"px");
                    $("#collapseTemplateWap").css("left", "-280px");
                    $('#collapseTemplateWap').collapse("toggle");
                })
                .fail(function () {

                });
        } else {
                    let ele = $(".accordion-numeros.accordiones.active").prop("id");
                    let tope = parseInt($("#"+ele+"-panel a.templates").offset().top) - $("#collapseTemplateWap").height() - 160;
                    if(tope < 0)
                        tope = parseInt($("#"+ele+"-panel a.templates").offset().top) + $("#collapseTemplateWap").height() + 20;
                    
                    $("#collapseTemplateWap").css("top", tope+"px");
                    $("#collapseTemplateWap").css("left", "-280px");
                    $('#collapseTemplateWap').collapse("toggle");
                }
    }

    function send_template(numero, mensaje, template, canal){
        let solID = $("#id_solicitud").val();
        let base_url = $("#base_url").val();
        let formData = new FormData();

        formData.append('solID', solID);
        formData.append('phoneN', numero);
        formData.append('Template', mensaje);
        formData.append('id_template', template);

        if (canal == "1") {
            url_base = base_url + 'comunicaciones/twilio';  
        } else {
            url_base =base_url + 'comunicaciones/TwilioCobranzas';
        }        
                
        swal.fire({
            title: "¿Esta seguro?",
            text: "¿Estas seguro de enviar el template seleccionado?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, Enviar"
        }).then(function (result) {
            $('#collapseTemplateWap').collapse("toggle");;
            if (result.value) {
                 $.ajax({
                        url: url_base + '/send_template_message_new',
                        type: 'POST',
                         data: formData,
                        processData: false,
                        contentType: false,
                    }).done(function (re) {
                        //$('#collapseTemplateWap').collapse("toggle");;
                        if (re.template) {
                            if (re.template === true) {
                                swal.fire('Exito','Mensaje Enviado, a la espera del cliente','success');
                            } else {
                                swal.fire('Error','Ocurrió un error con la cookie asociada, trata nuevamente o prueba cerrar a iniciar sesión','error');
                            }
                        }

                        if (re.chat) {
                            if (re.operator) {
                                swal.fire('Informacion','Existe un Chat activo con éste cliente. Operador: ' + re.operator,'info');
                            } else {
                                swal.fire('Informacion','Se ha levantado un nuevo Chat o el chat activo','info');
                            }
                        }

                        if (re.self) {
                            swal.fire('Informacion','Has reclamado ésta conversación. Se ha levantado un chat activo','info');
                        }
                    });
            }
        });
            

    }

    function send_biometria(numero = "") {
        let solID = $("#id_solicitud").val();
        let base_url = $("#base_url").val();
        let formData = new FormData();
        let cadena = "";

        formData.append('numero', numero);
        formData.append('solicitud', solID);


        if($("#id_solicitud").data('paso') >= 13){
            if(numero != "")
            cadena = "Telefono: "+numero;

            $.ajax({
                url:  base_url + 'solicitud/send_biometria',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {
                
                if (typeof(response.success) != "undefined" && response.success) {
                    Swal.fire('',response.title_response, 'success');
                    let id_operador = $("#id_operador").val();
                    let type_contact = 8;
                    let comment = "<b>[VERIFICACION DE IDENTIDAD]</b><br><b>Envio de link por whatsapp</b><br> "+cadena;
                    
                    saveTrack(comment, type_contact, solID, id_operador);
                } else {
                    Swal.fire('¡Atención!',response.title_response, 'error')
                }
               
            })
            .fail(function (response) {
                Swal.fire('¡Atención!','No fue posible establecer la comunicacion', 'error');
            })
            .always(function (response) {
                //console.log("complete");
            });  
        } else {
            Swal.fire('¡Atención!','El paso de la solicitud no permite el envio del link de verificación', 'error');
        }
        
        
    }

    box_whatsapp = [];
    box_whatsapp.popover = (elemn) => {
        from                = $(elemn).data("from_veriff_ws");
        id                  = $(elemn).data("id_veriff_ws");
        visible_operador    = $(elemn).data("biometriaws");
        // Sts_veriff          = $(elemn).data("status_veriff_ws");

        $.getJSON( base_url + 'api/getwhatsapp_scan/' + $("#id_solicitud").val() , function( data ) {

            if (data.status) {
                if (data.data.status == 'activo') 
                    Sts_veriff = 1;
                else
                    Sts_veriff = 0;
            } else {
                Sts_veriff = 0;
            }

            paso = $("#paso-solicitud").text();        
            html = '';
            html += '<div class="div-table" style="border-top: 1px solid #ccc;" >';
            html += '<div class="div-table-row">' + 
                        '<button type="button" class="div-table-col btn btn-link" style="text-decoration: none; color: #333" onclick="send_biometria(\''+ from +'\')">' + 
                            '<strong>ENVIAR LINK DE VERIFICACIÓN DE IDENTIDAD</strong>'+
                        '</button>' + 
                    '</div>';
            html += '<div class="div-table-row" style="'+((visible_operador == 1)? '' : 'display: none')+ '">' + 
                        '<button type="button" id="send_biometria_whatsapp_'+id+'" class="div-table-col btn btn-link" '+((Sts_veriff == 1)? 'disabled': '')+' '+((paso == 13 || paso == 16 )? '': 'disabled')+' style="text-decoration: none; color: #333" onclick="box_whatsapp.send_biometria_whatsapp(\''+ from +'\',\''+ id +'\' )"> ' + 
                            '<strong>INICIAR VERIFICACIÓN POR WHATSAPP</strong>' + 
                        '</button>' + 
                    '</div>';
            html += '</div>';
            
            $('#'+ id +'-popover-ws').popover({
                placement: 'right',
                trigger: 'focus',
                delay: { "show": 100, "hide": 500 },
                html: true,
                sanitize: false,
                title: "",
                content: () => {
                    return html;
                }
            })
            $('#'+ id +'-popover-ws').popover('show');
        });


    };
    box_whatsapp.send_biometria_whatsapp = (dato_telefono, id) => {
        flowdata = {
                'analisis_13_face' : 0,
                'analisis_13_front' : 1,
                'analisis_13_back'  : 1,
                'analisis_13_video' : 1,
            }
            
            Swal.fire({
                title: 'Confirmar Envio',
                text: '¿Deseas solicitar la verificación por WhatsApp?',
                type: 'warning',
                confirmButtonText: 'Solicitar',
                cancelButtonText: 'Cancelar',
                showCancelButton: 'true'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url:  base_url + 'atencion_cliente/getverifywhatsapp',
                        type: 'POST',
                        data: {
                            id_solicitud: $("#id_solicitud").val(),
                            documento: $("#client").data("number_doc"),
                            telefono: dato_telefono,
                            flow : flowdata,
                            action : 'btinit_biometria_ws'
                        },
                        success: (resp) => {
                            data = JSON.parse(resp);   
                            let id_operador = $("#id_operador").val();
                            let id_solicitud = $("#id_solicitud").val();
                            let type_contact = 193;
                            let comment = "<b>[INICIADO]</b>" + 
                            "<br><b>Inicio de verificación automática por whatsapp.</b>" + 
                            "<br>Telefono: " + data.telf + ".";
                            // $("button#send_biometria_whatsapp_"+id).prop('disabled',true)
                            saveTrack(comment, type_contact, id_solicitud, id_operador);
                            cargar_box_galery($("#id_solicitud").val())
                        }
                    })
                }
            });
    }
    
    $(".btnSub").on("click", function () {
        let boton = $(this).attr('submenu');
        let metodo = $(this).attr('metodo');
        let canal = $(this).attr('canal');
        let documento = $(this).attr('documento');
        let telefono = $(this).attr('telefono');
        let seleccion = $(this).attr("seleccionado");
        let btn=$(this).attr("sbmenu");
        let convenio=$(this).attr("codConv");
        $(".btnSub").css("background-color", "#f5f5f5");
        if($("#"+boton).html() == ""){
            // $(".btnSub").css("background-color", "#f1f1f1");
            // $("#"+seleccion).css("background-color", "#3EC79B");
            $("."+btn).empty();
            $("#"+seleccion).css("background-color", "#3EC79B");
            $("#"+seleccion).css("display", "inline-block");
            $("#"+seleccion).css("width", "100%");
            agregarSubBtn(btn,boton,metodo,canal,documento,telefono,convenio);
        }else{
            $("#"+boton).empty();
        }
        
    });

    $(".dropbtnVent").on("click", function () { 
        let estado = $(this).attr('estado');
        if (estado == "oculto") {
            $(".botonesEnv").empty();
            $(".btnSub").css("background-color", "#f5f5f5");
            $(".btnSub").css("display", "inline-block");
            $("#envioBoleta").remove();

            $(".dropdown-content").css("display", "inline-block");
            $(".dropbtnVent").attr("estado", "visible");
        }else{
            $(".dropdown-content").css("display", "none");
            $(".dropbtnVent").attr("estado", "oculto");
        }        
    });

    
    $(".dropbtnCbr").on("click", function () { 
        let estado = $(this).attr('estado');
        if (estado == "oculto") {
            $(".botonesEnv").empty();
            $(".btnSub").css("background-color", "#f5f5f5");
            $(".btnSub").css("display", "inline-block");
            $("#envioBoleta").remove();
            
            $(".dropdown-content").css("display", "inline-block")
            $(".dropbtnCbr").attr("estado", "visible")
        }else{
            $(".dropdown-content").css("display", "none")
            $(".dropbtnCbr").attr("estado", "oculto")
        }        
    });

    $(document).on('click',function (e){
        let estado = $(".dropbtnVent").attr("estado");
        let btn = $(".dropbtnVent");
        let contenido = $("#contenido_ventas");
        if (!btn.is(e.target) && $(e.target).closest(btn).length == 0 && !contenido.is(e.target) && $(e.target).closest(contenido).length == 0) {
            $("#contenido_ventas").css("display", "none");
            $(".dropbtnVent").attr("estado", "oculto");
        }
    }); 

    $(document).on('click',function (e){
        let estado = $(".dropbtnCbr").attr("estado");
        let btn = $(".dropbtnCbr");
        let contenido = $("#contenido_cobranza");
        if (!btn.is(e.target) && $(e.target).closest(btn).length == 0 && !contenido.is(e.target) && $(e.target).closest(contenido).length == 0) {
            $("#contenido_cobranza").css("display", "none");
            $(".dropbtnCbr").attr("estado", "oculto");
        }
    });

    function agregarSubBtn(btn,boton,metodo,canal,documento,telefono, convenio) {
        $("."+btn).empty();
        $('#'+boton).append('<a id="envioBoleta" style="display: table-cell;" onclick="enviarBoletas('+canal+','+telefono+','+documento+', '+metodo+','+2+','+convenio+')"><i class="fa fa-commenting-o" aria-hidden="true" style="color:black;"></i></a>');
        $('#'+boton).append('<a id="envioBoleta" style="display: table-cell;" onclick="enviarBoletas('+canal+','+telefono+','+documento+', '+metodo+','+1+','+convenio+')"><i class="fa fa-envelope-o" aria-hidden="true" style="color:black;"></i></a>');
        $('#'+boton).append('<a id="envioBoleta" style="display: table-cell;" onclick="enviarBoletas('+canal+','+telefono+','+documento+', '+metodo+','+0+','+convenio+')"><i class="fa fa-whatsapp" aria-hidden="true" style="color:black;"></i></a>');
    
    }

    function enviarBoletas(canal, telefono, documento, medio_pago, fuente,convenio) { 
        $.ajax({
            data: {
                "canal": canal, 
                "documento": documento, 
                "telefono": telefono, 
                "medio_pago": medio_pago,
                "fuente": fuente,
                "cod_convenio": convenio
            },
            url: base_url + "api/ApiSolicitud/data_enviar",
            type: "POST",
            success: function (respuesta) {
                response = JSON.parse(respuesta);
                if(response.status == 200 || response.status == "200"){
                    Swal.fire('Se ha enviado correctamente', response.mensaje, 'success');
                }else{
                    Swal.fire('No se ha realizado el envio', response.mensaje, 'warning');
                }
            }
        });
    }

</script>
