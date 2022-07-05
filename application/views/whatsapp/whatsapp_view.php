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

<div class="container-fluid" style="margin-top: 4%;">
    <!-- /*** URL base ***/ -->
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <input type="hidden" name="status_chats" value="">
    <!-- Canal -->
    <div class="row">
        <div class="form-horizontal col-md-3">
            <div class="form-group">
                <label for="selectCanal" class="col-sm-2 control-label">Canal:</label>
                <div class="col-sm-10">
                    <select class="form-control" id="selectCanal" name="selectCanal">
                        <option value="0">Seleccione...</option>
                        <?php foreach ($canalChat as $value) {?>
                            <option value="<?= $value['tlf'] ?>"><?= $value['canal'] . ' - ' . $value['tlf'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <!-- Buscar -->
        <div class="form-horizontal col-md-8 col-md-offset-1">
            <div class="form-group">
                <label class="col-sm-2 control-label" for="select_buscar">Buscar por:</label>
                <div class="col-sm-3">
                    <select class="form-control" id="select_buscar" name="select_buscar">
                        <option value="">Seleccione...</option>
                        <option value="telefono">Número de teléfono</option>
                        <option value="texto">Texto</option>
                        <option value="documento">Documento</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inp_buscar" />
                </div>
                <div class="col-sm-3">
                    <select class="form-control" id="select_operador" name="select_operador">
                        <option value="todos">Operadores: *TODOS*</option>
                        <?php foreach ($operadores as $value) {?>
                            <option value="<?= $value['id_operador'] ?>"><?= ucwords(strtolower($value['nombre_apellido'])) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-1">
                    <button class="btn btn-success btn-block" id="btn_buscar">Buscar</button>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-2 text-right col-md-offset-1">
            <label for="myonoffswitch">Reacarga Automática:</label>
        </div>
        <div class="onoffswitch col-md-3">
            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" tabindex="0" checked>
            <label class="onoffswitch-label" for="myonoffswitch">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
        </div> -->
    </div>
    <!-- Operadores -->
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-primary" id="panel_operadores">
                <div class="panel-heading">
                    <h3 class="panel-title" data-toggle="tooltip" title="Hooray!">Operadores</h3>
                </div>
                <div style="overflow-y: auto;" id="caja_scroll_operadores">
                    <div class="panel-body">
                        <input type="hidden" name="chatList" value="0">
                        <ul id="ul_operadores" class="list-group"></ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Detalles de un Chat -->
        <div class="col-md-6">
            <div class="panel panel-warning " id="panel_mensajes">
                <div class="panel-heading" id="panel_heading_mensajes">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-muted">Cliente: <span class="text-danger" id="sp_nombre_cliente"></span></h6>
                            <h6 class="text-muted">Documento: <span class="text-danger" id="sp_documento"></span></h6>
                            <h6 class="text-muted">Teléfono: <span class="text-danger" id="sp_telefono"></span></h6>
                        </div>
                        <div class="col-md-4">
                            <!-- <h6 class="text-muted">Último mensaje: <em><span class="text-danger" id="sp_ultimo_mensaje"></span></em></h6> -->
                            <h6 class="text-muted">Primer recibido: <span class="text-danger" id="sp_primer_mensaje_recibido"></span></h6>
                            <h6 class="text-muted">Mensajes recibidos: <span class="text-danger" id="sp_mensajes_recibidos"></span></h6>
                            <h6 class="text-muted">Chat iniciado por: <span class="text-danger" id="sp_iniciado_por"></span></h6>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Primer enviado: <span class="text-danger" id="sp_primer_mensaje_enviado"></span></h6>
                            <h6 class="text-muted">Mensajes enviados: <span class="text-danger" id="sp_mensajes_enviados"></span></h6>
                            <h6 class="text-muted">Canal: <span class="text-danger" id="sp_canal"></span></h6>
                        </div>
                    
                    </div>
                    <div class="row">
                    </div>
                </div>

                            <div id="slot-1">

                            </div>

                <?php // $this->load->view('whatsapp/whatsapp_component.php',['id_chat'=>0]); ?>




            </div>




        </div>

    <!-- Modal -->

    <!-- Modal -->

            
        <!-- Todos los Chats de un operador -->
        <div class="col-md-3">
            <div class="panel panel-default" id="panel_chats">
                <div class="panel-heading" style="background-color: #cd547e; color: white">
                    <h3 class="panel-title">Chats del operador</h3>
                </div>
                <div style="height: 200px; overflow-y: auto;" id="caja_scroll_chats">
                    <div class="panel-body">
                        <div id=btn_operador_seleccionado></div>
                        <ul id="ul_chat" class="list-group"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal para el Loading -->
<div class="modal fade" id="modalLoading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <!-- Para el loading -->
    <div style="padding-top: 20%;">
        <div id="main" style="marging-top: 100px;"></div>
    </div>
</div>

<script src="<?php echo base_url('assets/whatsapp/whatsapp.js')?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script>
    
</script>
