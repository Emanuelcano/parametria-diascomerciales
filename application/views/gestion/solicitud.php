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
</style>
<?php $estilo_div = "padding-right: 1px; padding-left: 7px;";
        $analisis = isset($analisis[0])? $analisis[0]:[]; 
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
            <?php
                
                $this->load->view('gestion/box_client_info',['solicitude'=>$solicitude[0], 'bank'=>$bank, 'analisis'=>$analisis, 'user_videocall' => $user_videocall]);
            ?> 
            <div class="datos-contacto">
                <?php
                    //$this->load->view('gestion/box_datos_contacto',['solicitude'=>$solicitude[0]]);
                ?>
            </div>
            
            <?php
                $this->load->view('gestion/box_datos_laboral',['solicitude'=>$solicitude[0], 'bank'=>$bank, 'analisis'=>$analisis, 'banks'=>$banks], 'type_account', $type_account);
            ?>
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
	<div class="row">
		<div class="col-md-12">
			<?php $this->load->view('gestion/box_referencias_cruzadas',['id_solicitud'=>$id_solicitud,'documento'=>$solicitude[0]['documento'],'tipo_canal'=>1]);?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php
			$this->load->view('gestion/box_agenda_email',['id_solicitud'=>$id_solicitud,'documento'=>$solicitude[0]['documento'],'tipo_canal'=>1]);
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php $this->load->view('gestion/box_referencias_cruzadas_email',['id_solicitud'=>$id_solicitud,'documento'=>$solicitude[0]['documento'],'tipo_canal'=>1]);?>
		</div>
	</div>
    <div class="row">
        <div class="col-md-12">
            <?php 
                $var_box_situacion_laboral = [
                    'solicitude'    =>$solicitude[0],
                    'analisis'      =>$analisis,
                    'ref_personal'  =>$referencia_personal
                ];
                $this->load->view('gestion/box_situacion_laboral',$var_box_situacion_laboral);
            ?>
        </div>
    </div>
	<div class="row">
		<div class="col-md-12">
			<?php
			$var_box_data = [];
			$this->load->view('gestion/box_info_endeudamiento',$var_box_data);
			?>
		</div>
	</div>
	<?php
	if ($this->session->userdata['tipo_operador'] != 1 and $this->session->userdata['tipo_operador'] != 4) { ?>
		<div class="row">
			<div class="col-md-12">
				<?php $this->load->view('gestion/box_listas_restrictivas',['id_solicitud'=>$id_solicitud,'documento'=>$solicitude[0]['documento'],'tipo_canal'=>1]);?>
			</div>
		</div>
	<?php } ?>
    <!-- <div class="row">
        <div class="col-md-12">
        <?php //$this->load->view('gestion/box_review_buttons',['solicitude' => $solicitude[0],'btn_revision' => $btn_revision, 'pagare_descargado' =>$pagare_descargado]); ?>
        </div>
    </div> -->
    <div class="row">
        <div class="col-md-12">
        <?php 
            $ref_familiar = isset($referencia_familiar[0])?$referencia_familiar[0]:''; 
            $ref_personal = isset($referencia_personal[0])?$referencia_personal[0]:'';
            $familiar_botones = isset($referencia_familiar[0])?$referencia_familiar[0]:''; 
            $laboral_botones = isset($referencia_familiar[0])?$referencia_familiar[0]:''; 
            $personal_botones = isset($referencia_familiar[0])?$referencia_familiar[0]:''; 
            $titular_botones = isset($referencia_familiar[0])?$referencia_familiar[0]:''; 
            $this->load->view('gestion/box_verificacion',['solicitude' => $solicitude[0], 'ref_family'=>$ref_familiar, 'ref_personal'=>$ref_personal,'familiar_botones'=>$familiar_botones , 'laboral_botones' => $laboral_botones,'personal_botones'=>$personal_botones,'titular_botones'=>$titular_botones]); 
        ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 metricas">
            <?php if(isset($indicadores[0])){ $this->load->view('gestion/box_metrics',['indicadores'=>$indicadores[0], 'ranges'=>$ranges]); } ?>
        </div>
    </div>
    <div class="row">
            <?php
                $fechas_vencimientos = json_decode(json_encode($vencimientos), true);
                $condition = !empty($solicitud_condicion[0])?$solicitud_condicion[0]:[];
                $fechas_venciento = !empty($fechas_vencimientos[0])?$fechas_vencimientos[0]:[];
                $desembolso = !empty($solicitud_desembolso[0])?$solicitud_desembolso[0]:[];
                $offer = !empty($solicitud_oferta[0])?$solicitud_oferta[0]:[];
                $credito_general = !empty($credito_general[0])?$credito_general[0]:[];
                $this->load->view('gestion/box_condition',['condition'=>$condition, 'desembolso'=>$desembolso, 'offer'=>$offer, 'credito'=>$credito_general, 'fechas_venciento'=>$fechas_venciento]);
            ?>
    </div>
    
    <div class="row">
        <!-- <div class="col-md-6 col-lg-6" style="padding-right: 1px; padding-left: 7px;">
            <?php //if(isset($datos_personales)){ $this->load->view('gestion/box_datos_personales',['datos_personales'=>$datos_personales]); } ?>
        </div>  --> 
        <?php  if(isset($referencia_familiar[0])){ ?>
        <div class="col-md-3 col-lg-3" style="padding-right: 1px; padding-left: 7px;">
            <?php if(isset($referencia_familiar)){ $this->load->view('gestion/box_ref_family',['ref_family'=>$referencia_familiar, 'parentesco'=>$parentesco]); } ?>
        </div>       
    <?php  if ($solicitude[0]['id_situacion_laboral'] == 1){?>
            <div class="col-md-3 col-lg-3" style="<?php echo $estilo_div ?>">
                <?php if(isset($referencia_personal[0])){ $this->load->view('gestion/box_ref_laboral',['ref_personal'=>$referencia_personal, 'parentesco'=>$parentesco]); } ?>
            </div>            
        <?php } else if($solicitude[0]['id_situacion_laboral'] == 7) { ?>
            <div class="col-md-3 col-lg-3" style="<?php echo $estilo_div ?>">
                <?php if(isset($referencia_personal[0])){ $this->load->view('gestion/box_ref_fuerzas',['ref_personal'=>$referencia_personal, 'parentesco'=>$parentesco]); } ?>
            </div> 
        <?php } else if($solicitude[0]['id_situacion_laboral'] == 3) { ?>
            <div class="col-md-3 col-lg-3" style="<?php echo $estilo_div ?>">
                <input type="hidden" id="situacion_laboral" value="3">
                <?php if(isset($referencia_personal[0])){ $this->load->view('gestion/box_ref_laboral_independiente',['ref_personal'=>$referencia_personal, 'parentesco'=>$parentesco]); } ?>
            </div> 
    <?php }else{?>
            <div class="col-md-3 col-lg-3" style="<?php echo $estilo_div ?>">
                <?php if(isset($referencia_personal[0])){ $this->load->view('gestion/box_ref_personal',['ref_personal'=>$referencia_personal, 'parentesco'=>$parentesco]); } ?>
            </div>
        <?php } ?> 
    <?php  } ?>

   
    </div>

    <div class="row">
        <div class="col-md-3 col-lg-3 box_ref_documentos" style="overflow:scroll; height:265px; <?php echo $estilo_div ?>">
            <?php //$this->load->view('gestion/box_ref_documentos', ['docs'=>$images,'solicitude' => $solicitude[0]]);?>
        </div>
        <div class="col-md-3 col-lg-3 box_ref_archivos" style="height:224px; <?php echo $estilo_div ?>">
            <?php //$this->load->view('gestion/box_ref_archivos', ['docs'=>$images,'solicitude' => $solicitude[0]]);?>
        </div>           
        <div class="col-md-3 col-lg-3" style="overflow:scroll; height:264px; <?php echo $estilo_div ?>">
            <?php if(isset($referencia_familiar[0])){ $this->load->view('gestion/box_ref_telefono',['ref_family'=>$referencia_familiar[0]]); } ?>
        </div>
        <div class="col-md-3 col-lg-3" style="overflow:scroll; height:264px; <?php echo $estilo_div ?>">
            <?php if(isset($referencia_familiar[0])){ $this->load->view('gestion/box_ref_celular',['ref_family'=>$referencia_familiar[0]]); } ?>
        </div> 
    </div>
   
  
    <div class="row">
        <div class="col-md-12 box_galery">            
            <?php //Si tiene scanfer mandar los datos doc de jumio, sino el que estaba
            //$this->load->view('gestion/box_galery', ['docs'=>$images,'solicitude' => $solicitude[0]]);?>
        </div>             
    </div>



    <div class="row">
        <?php // if(isset($credits) && !empty($credits)): ?>
        <div class="col-md-12 creditos">
            <?php //$this->load->view('gestion/box_list_credits', ['credits'=>$credits, 'solicitude' => $solicitude[0]]);?>
        </div>
        <?php //endif; ?>
    </div>



    <div class="row" style="margin-top: 20px;">
        <div class="col-md-12 track-marcacion">
            <?php //$this->load->view('cobranzas/box_track_marcacion'); ?>
        </div>
    </div>
    <button type="button" onclick="btn_flotante_solicitud()" id="btn-flotante-solicitud" class="btn-flotante-solicitud"><i class="fa fa-angle-double-down" aria-hidden="true"></i></button>
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
<script type="text/javascript" src="<?php echo base_url('assets/gestion/box_situacion_laboral.js');?>"></script>
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

        $(window).scroll(function () {
            if(Math.round(($(window).scrollTop() * 100) / $('section.content').height()) < 30)
                $('button.btn-flotante-solicitud').html('<i class="fa fa-angle-double-down" aria-hidden="true"></i>')
            else
                $('button.btn-flotante-solicitud').html('<i class="fa fa-angle-double-up" aria-hidden="true"></i>')
        })

        btn_flotante_solicitud = () => {
            if(Math.round(($(window).scrollTop() * 100) / $('section.content').height()) < 30)
                $("html, body").stop().animate({scrollTop : $(document).height()}, 1000, 'swing');
            else
                $("html, body").stop().animate({scrollTop : 0}, 1000, 'swing');
        }
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
        $("#slc_bancos").show();
    }
        
    });
    
</script>
