<link rel="stylesheet" href="../assets/css/custom-gestion.css">
<style type="text/css">
    .box {
        margin-bottom: 4px;        
    }    
    span.PlayPause {
        background: url(<?=base_url('assets/images/play_pause.svg')?>) no-repeat top left;
        background-size: contain;
        cursor: pointer;
        display: inline-block;
        height: 48px;
        width: 56px;
    }
    label[for="file"] {
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        background-color: #17a2b8;
        display: inline-block;
        transition: all .5s;
        cursor: pointer;
        padding: 5px 5px !important;
        text-transform: uppercase;
        width: 100%;
        text-align: center;
    }

    #imgDoc {
        
        width: 100%;
        max-height: 460px;
        margin: 0px auto;
    }
</style>
<?php $estilo_div = "padding-right: 1px; padding-left: 7px;";
        $analisis = isset($analisis[0])? $analisis[0]:[]; 
        #echo '<pre>'; print_r($solicitude); echo '</pre>'; exit;
 ?>
<input id="client" type="hidden" data-number_doc = "<?php echo $solicitude[0]['documento']; ?>">
<input type='hidden' id='validaciones' value="<?= $validaciones ?>">
<input type="hidden" id="id_solicitud" data-tipo="<?php echo $solicitude[0]['tipo_solicitud'] ?>" data-paso="<?php echo $solicitude[0]['paso'] ?>" data-status = "<?php echo $solicitude[0]['estado'] ?>" value="<?php echo $solicitude[0]['id']; ?>">
<input type="hidden" id="box_client_title" data-id_cliente="<?php echo $solicitude[0]['id_cliente'] ?>" data-status = "<?php echo $solicitude[0]['estado'] ?>" value="<?php echo $solicitude[0]['id']; ?>">
<input type="hidden" id="credito" data-id_credito="<?php echo $solicitude[0]['id_credito'] ?>" data-status = "<?php echo isset($solicitude[0]['status_credit']) ?>" >

<section class="content-header" style="padding: 5px;">
    <?php
        $bank = isset($datos_bancarios[0])? $datos_bancarios[0]:[];
        $this->load->view('gestion/box_header',['solicitud'=>$solicitude[0], 'bank'=>$bank, 'analisis'=>$analisis]);
    ?>
</section>
<section class="content" style="padding: 5px;">
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <div class="row">
        <div class="col-md-12 title">
        <?php
            //$this->load->view('gestion/box_title',['solicitude' => $solicitude[0], 'bank'=>$bank, 'whatsapp_respuesta'=>$whatsapp_respuesta]);
        ?>
        </div>
    </div>
    <div class="row row-chat-track" style="height: 920px; margin:0px;">
        <div class="col-md-4" style="height: 105%; padding:0px;" style="<?php echo $estilo_div ?>">

        
            <?php if (isset($bank['numero_cuenta'])){?>
            <?php
                $this->load->view('gestion/box_bancos',['solicitude'=>$solicitude[0], 'bank'=>$bank, 'pagado_txt'=>$pagado_txt, 'analisis'=>$analisis, 'banks'=>$banks], 'type_account', $type_account);
            ?>
        <?php } ?>    

        <?php if ($solicitude[0]['estado'] == 'PAGADO' || $solicitude[0]['estado'] == 'TRANSFIRIENDO' && isset($pagado_txt)){?>
            <?php
                $this->load->view('gestion/box_desembolso_result',['pagado_txt'=>$pagado_txt, 'verificacion_desembolso'=> $solicitud_verificacion, 'solicitude'=>$solicitude[0]]);
            ?>
        <?php } ?> 
        <div class="box_cargar_documento"></div>   
        <div class="box_slider_documentos"> </div>
        </div>        
        <div id="tracker"class="col-md-4" style="height: 95%;padding-right: 5px; padding-left: 5px;">
            <div id="box_tracker" class="box box-info" style="height: 102%;">
                <div class="box-header with-border"></div><!-- end box-header -->
                <div class="box-body"  style="overflow-y: auto; height: 66%">
                    <div class="tab-pane active" id="timeline" style="padding-top: 35%;">
                        <div class="loader" id="loader-6">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        </div>
                    </div>
                </div><!-- end box-body -->
                <div class="box-footer" style="height: 31%">

                </div><!-- end box-footer -->
            </div> <!-- end box-default -->
        </div>
        <div id="whatsapp" class="col-md-4" style="height: 95%;padding:0px; ">
            <div id="box_whatsapp" class="box box-info __chats_list_container" style="height: 875px;  <?php echo $estilo_div ?>">
                <div class="box-header with-border" id="titulo">
            
                </div>
                <div class="box-body"  style="overflow-y: auto; height: 66%">
                    <div class="tab-pane active" id="timeline" style="padding-top: 40%;">
                        <div class="loader" id="loader-6">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div><!-- end box-body -->
            </div>
            <?php
                //$solicitud_desembolso = isset($solicitud_desembolso)?$solicitud_desembolso:[]; 
                //$this->load->view('gestion/box_whatsapp',['chats' => $chats, 'solicitude' => $solicitude[0], 'solicitud_desembolso' => $solicitud_desembolso]);
            ?>
        </div>
<!--         <div class="col-md-3">
            <?php
                //$solicitud_desembolso = isset($solicitud_desembolso)?$solicitud_desembolso:[]; 
               // $this->load->view('gestion/box_whatsapp',['chats' => $chats, 'solicitude' => $solicitude[0], 'solicitud_desembolso' => $solicitud_desembolso]);
            ?>
        </div> -->
    </div>
    
    
	<div class="row">
		<div class="col-md-12">
			<?php $this->load->view('gestion/box_review_buttons',['solicitude' => $solicitude[0],'btn_revision' => $btn_revision, 'pagare_descargado' =>$pagare_descargado]); ?>
		</div>
	</div>	
    <div class="row">
        <div class="col-md-12">
            <?php 
                $var_box_Ajustes = [];
                $this->load->view('gestion/box_sol_ajustes',$var_box_Ajustes);
            ?>
        </div>
    </div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php $this->load->view('gestion/box_agenda_telefono_new',['id_solicitud'=>$id_solicitud,'documento'=>$solicitude[0]['documento'],'tipo_canal'=>1]);?>
		</div>
	</div>
</section>
<section class="modal-content">
    <!-- Modal de Pasos -->
    <div class="modal fade" id="detallePaso" tabindex="-1" role="dialog" aria-labelledby="detallePaso" aria-hidden="true">
      <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><strong>PASO: </strong><?php echo isset($solicitude[0]['paso'])?$solicitude[0]['paso']:''; ?> - <?php echo isset($pasos[0]['titulo'])?$pasos[0]['titulo']:''; ?></h5>          
          </div>
          <div class="modal-body">
              <h5 class="modal-title" id="exampleModalLabel"><strong>DETALLE: </strong><?php echo isset($pasos[0]['descripcion'])?$pasos[0]['descripcion']:''; ?></h5>
            <br>
            <img src="<?php echo isset($pasos[0]['path'])?base_url($pasos[0]['path']):''; ?>"   style="height: unset;width:100%" >
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</section>
<section >
    <div class="modal fade " id="veriff-ws-video" tabindex="-1" role="dialog" aria-labelledby="veriff-ws-video" aria-hidden="true">
      <div class="modal-dialog " role="document">
        <div class="modal-content">
          <div class="modal-header">            
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
                <div style="display: flex;justify-content: space-around;">
                    <i id="video-ws-spin" class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
                    <video id="video-ws" preload="metadata" >
                        <source src="" type="video/mp4">
                    </video>
                </div>
                <div>
                    <div style="padding-top: 15px; width: 100%; display: flex; flex-direction: row" >
                        <label id="lbl_verifWs_timebegin" style="padding-right: 10px" >00:00</label>
                        <input type="range" min="0" max="100" value="0" class="slider" id="myRange-ws" data-slider-id="green">
                        <label id="lbl_verifWs_timeend" style="padding-left: 10px" >00:00</label>
                    </div>
                    <div style=" width: 100%; display: flex; flex-direction: row; justify-content: center" >
                        <label style="padding-right: 5px" >-</label>
                        <input id="volumeslider" type="range" min="0" max="100" value="100" step="1" style="width: 30%;" data-slider-id="green">
                        <label style="padding-left: 5px" >+</label>
                    </div>
                </div>
          </div>
          <div class="modal-footer" style="padding: 5px 15px;">
              <div style="display: flex; justify-content: space-around; ">                  
                <button type="button" class="btn btn-secondary" onclick="playPause()" style="background-color: white; padding: 0px;"><span class="PlayPause"></span></button>
              </div>
          </div>
        </div>
      </div>
    </div>
</section>
<section class="modal-content">
    <!-- Modal de preview documento -->
    <div class="modal fade" id="imgDocumento" tabindex="-1" role="dialog" aria-labelledby="imgDocumento" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="imgTitle"></h4>          
          </div>
          <div class="modal-body">
                <object
                    id="imgDoc"
                    type="application/pdf"
                    data=""
                    style="width: 500px; height: 550px;">
                    ERROR (no puede mostrarse el documento)
                </object>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</section>
<section class="callvideo-operator">
    <?php $this->load->view('gestion/box_callvideo');?>
</section>
<section class="veriff-ws-reenvio">
    <?php $this->load->view('gestion/veriff-ws-reenvio', [ 'biometria_items' => $biometria_items, 'solicitud' => $solicitude[0] ]);?>
</section>

<script type="text/javascript" src="<?php echo base_url('assets/gestion/box_referencias_cruzadas_email.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/gestion/box_referencias_cruzadas.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/gestion/box_listas_restrictivas.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/gestion/box_info_endeudamiento.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/gestion/agenda_telefonica_new.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/gestion/box_sol_ajustes.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/gestion/box_agenda_email.js');?>"></script>
<script src="<?php echo base_url('assets/gestion/condicion_desembolso.js');?>"></script>
<script type="text/javascript">
    $(() => {
        var channel_chatbot = pusher.subscribe('chatbot_'+$('#id_solicitud').val());
        channels.push(channel_chatbot);

        channel_chatbot.bind('biometriaChatbot', function(data) {
            resp = JSON.parse(data);
            cargar_box_galery(resp.id_solicitud)
        });
    });

    $("#close_solicitude").on('click', function(){
        if ($('section.callvideo-operator > div#box_callvideo.hidden').length == 0) {
            gCallvideo.close_solicitud();
        } else {
            $.ajax({
            type: "GET",
            url: base_url + "atencion_cliente/getverifygalery/" + $("#id_solicitud").val(),
            dataType: "json",
            success: function (response) {
                    action = response.status
                    if (action){
                        Swal.fire({
                            title: '¡Atención!',
                            text: 'SOLICITUD CON VIDEO DE VERIFICACION SIN OBSERVACIONES',
                            icon: 'info',
                            confirmButtonText: 'Continuar',
                            cancelButtonText: 'Volver',
                            showCancelButton: 'true'
                        }).then((result) => {
                            if (result.value) {
                                close_solicitude()
                            }
                        });

                    } else {
                        close_solicitude()
                    }
                }
            }); 
        }


    close_solicitude = () => {

        //si la solicitud es obligatoria
        if(typeof(tiempo_timer) != 'undefined'){
            //cerrar la solicitud sin haberla gestionado
            if ((min > 0 || seg > 0) && !track_generado ) {
                Swal.fire({
                    title: '¡Solicitud sin gestión!',
                    text: 'Estas cerrando una solicitus OBLIGATORIA sin haberla gestionado',
                    icon: 'warning',
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    showCancelButton: 'true',
                    timer: 10000
                }).then((result) => {
                    if (result.value) {
                        cerrar_solicitud_obligatoria();
                    }
                });
            }else{
                cerrar_solicitud_obligatoria();
            }


        } else{

            if(typeof(pusher) != "undefined"){
                $.each(channels, function( index, value ) {
                    pusher.unsubscribe(value.name);
                    value.unbind();
                });
                channels = [];
            }
            $("#id_operador").data('validaciones', $("#validaciones").val());
            $("#dashboard_principal #texto").html("");
            $("#section_search_solicitud #form_search").show();
            $("#tabla_solicitudes").show();
            $("#solicitudPendientes").show();
            $("#tabla_desembolso").show();
            $("#botones_filtro").show();
            $("#texto_agenda").show();
            $("#texto_sol_ajustes").show();
            var_agendarcita.getCasosAgendados();
            var_agendarcita.getSolicitudAjustes();
            $("#sol_validaciones .badge").html($("#id_operador").data('validaciones'));
        }
    }
        
        
    });
    
</script>
