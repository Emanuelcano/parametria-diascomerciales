<style>
.testimonial-group > .row {
  overflow-x: auto;
  white-space: nowrap;
}
.testimonial-group > .row > .col-md-2 {
  display: inline-block;
  float: none;
}

input[type=”file”]#file {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
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

.container-video-images,    
.container-video-ws{    
    display:table;
    width:auto;
    position:relative;
    width:95%;
}
.container-video-images>.player-buttons,
.container-video-ws>.player-buttons{    
    background-image:url('data:image/svg+xml;base64,PHN2ZyBmaWxsPSJXaW5kb3ciIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgd2lkdGg9IjI0IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogICAgPHBhdGggZD0iTTggNXYxNGwxMS03eiIvPgogICAgPHBhdGggZD0iTTAgMGgyNHYyNEgweiIgZmlsbD0ibm9uZSIvPgo8L3N2Zz4K');
    background-repeat:no-repeat;
    width:50%;
    height:50%;
    position:absolute;
    left:0%;
    right:0%;
    top:0%;
    bottom:0%;
    margin:auto;
    background-size:contain;
    background-position: center;
    cursor: pointer;
}

.box_galery #compare #compare-title {
    margin: 10px 0px;
    display: flex;
    justify-content: space-evenly;
}

.box_galery #compare #compare-title button {
    margin: 0.5em 0.5em;
}
.box_galery #compare #compare-title i {
    left: 0px;
}

.box_galery #veriff .veriff_thumbnail,
.box_galery #fotos .fotos_thumbnail,
.box_galery #whatsapp_veriff .whatsapp_veriff_thumbnail {
    position: relative;
    overflow: hidden;
    /* border: 1px solid; */
    width: 200px;
    height: 250px; 
}
.box_galery #whatsapp_veriff .whatsapp_veriff_thumbnail::before {
    content: "";
    position: absolute;
    height: 100%;
    z-index: 10;
    background: var(--whatsapp_veriff_thumbnail);
    background-size : contain;
    transform: var(--whatsapp_veriff_thumbnail_transform);
    left: var(--whatsapp_veriff_thumbnail_left);
    width: var(--whatsapp_veriff_thumbnail_width);
}
.box_galery #veriff .veriff_thumbnail::before {
    content: "";
    position: absolute;
    height: 100%;
    z-index: 10;
    background: var(--veriff_thumbnail);
    background-size : contain;
    transform: var(--veriff_thumbnail_transform);
    left: var(--veriff_thumbnail_left);
    width: var(--veriff_thumbnail_width);
}
.box_galery #fotos .fotos_thumbnail::before {
    content: "";
    position: absolute;
    height: 100%;
    z-index: 10;
    background: var(--fotos_thumbnail);
    background-size : contain;
    transform: var(--fotos_thumbnail_transform);
    left: var(--fotos_thumbnail_left);
    width: var(--fotos_thumbnail_width);
}

</style>
<div id="box_galery" class="box box-info"  style="position: relative;">      
    <input id="base_url" type="hidden" name="base_url" value="<?php echo base_url()?>">
    <input id="id_operador" type="hidden" value="<?php echo $this->session->userdata('idoperador'); ?>">

    <?php 

        function statusWhatsapp($data, $intentos, $pagare): stdClass {
            $status = new stdClass;        
            $rejected = '';
            for ($i=1; $i < $intentos ; $i++) 
                $rejected .= '&nbsp;<img src="'. base_url('assets/images/rejected.svg') . '">';
                
            if ($data == '1') {
                $status->msj = '<label>'.$rejected.'&nbsp;<img src="'. base_url('assets/images/check-circle-done.svg') . '"> Recibido </label>';
                $status->disabled = (bool) false;
            } elseif ($data == '2') {
                $status->msj = '<label>'.$rejected.'&nbsp;<img src="'. base_url('assets/images/pendiente.svg') . '"> Pendiente </label>';
                $status->disabled = (bool) true; 
            }
            switch ($pagare) {
                case 0:
                case 1:
                    $status->disabled_check = (bool) false;
                    break;
                case 2:
                    $status->disabled_check = (bool) true;
                    break;
            }
            return $status;
        }

        function statusPagare($pagares): int {
            if (isset($pagares[1])){
                if ($pagares[0]->firma == '1' && $pagares[1]->firma == '1')
                    return 1;
                else
                    return 2;
            } else {
                return 0;
            }
        }

        function get_datetime_format($dateSrc) {
            $date = new DateTime($dateSrc);
            $date->setTimezone(new DateTimeZone('America/Bogota'));
            return '<i class="fa fa-calendar-check-o" aria-hidden="true"></i> '.$date->format('d/m/Y H:i:s');
        }

        if (isset($docs['ws']['data'])):
            $data = $docs['ws']['data'];
            $intentos = json_decode($data->intentos);

            $status_pagare = statusPagare($docs['ws']['pagare']);
            $status_front = statusWhatsapp($data->front, $intentos->front, $status_pagare);
            $status_back  = statusWhatsapp($data->back , $intentos->back, $status_pagare);
            $status_video = statusWhatsapp($data->video, $intentos->video, $status_pagare);

            $textImagen = [
                1 => '<label>Selfie</label>',
                2 => '<label>Cedula - Frente</label>',
                3 => '<label>Cedula - Dorso</label>',
                4 => '<label>Video - Selfie</label>'
            ];

    ?>
    
    <div class="text-center Whatsapp-veriff-group col-md-8">
        <input type="hidden" name="id_veriff_whatsapp" id="id_veriff_whatsapp" value="<?=$docs['ws']['data']->id ?>">
        <div class="row" id="whatsapp_veriff" style="height: auto; padding: 10px; white-space: nowrap; overflow-x: auto; ">
            <?php 
                foreach (array_reverse($docs['ws']['img']) as $key => $doc):
                    if($doc['is_image']):?>
                        <div class="col-md-2 item-galery" id="whatsapp_veriff_<?=$doc['sid']?>" style=" display: inline-block; float: none; margin-left: 35px;">
                            <div class="whatsapp_veriff_thumbnail" data-grade="<?=$doc['rotation']?>" data-src="<?=base_url($doc['patch_imagen'])?>"
                                style="--whatsapp_veriff_thumbnail: url(<?=base_url($doc['patch_imagen'])."?".time(); ?>) no-repeat center center; --whatsapp_veriff_thumbnail_transform: rotate(<?=$doc['rotation']?>deg);">
                            </div>
                            <div>
                                <div><?php echo $textImagen[$doc['id_imagen_requerida']];?></div>
                                <div style="font-size: 11px"><?php echo get_datetime_format($doc['fecha_carga']);?></div>
                            </div>
                            <div class="caption">
                                <p style="font-size: smaller"></p>
                                <button class="screen_1 btn btn-default" onclick="box_galery.compareImages(this)" data-title="<?=$textImagen[$doc['id_imagen_requerida']]?>" data-root_div="whatsapp_veriff" data-sid="<?=$doc['sid']?>" data-grade="<?=$doc['rotation']?>" data-view="screen_1" data-src="<?php echo base_url($doc['patch_imagen'])?>" >1</button>
                                <button class="screen_2 btn btn-default" onclick="box_galery.compareImages(this)" data-title="<?=$textImagen[$doc['id_imagen_requerida']]?>" data-root_div="whatsapp_veriff" data-sid="<?=$doc['sid']?>" data-grade="<?=$doc['rotation']?>" data-view="screen_2" data-src="<?php echo base_url($doc['patch_imagen'])?>" >2</button>
                            </div>
                        </div>
            <?php   endif;
                    if($doc['extension'] == '.mp4' || $doc['extension'] == '.webm'):    ?> 
                        <div class="col-md-2 item-galery-video" style=" display: inline-block; float: none; margin-left: 35px; vertical-align: top">
                            <section  class="container-video-ws">
                                <video height="250px" preload="metadata" >
                                    <source src="<?php echo base_url($doc['patch_imagen'])?>" type="video/mp4">
                                </video>
                                <div class="player-buttons" onclick="show_verrif_whatsapp('<?=base_url($doc['patch_imagen'])?>')"></div>
                            </section>
                            <div style="margin-top: -5px;"><?php echo $textImagen[$doc['id_imagen_requerida']];?></div>
                            <div style="font-size: 11px"><?php echo get_datetime_format($doc['fecha_carga']);?></div>
                        </div>
            <?php   endif;
                endforeach; ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row" id="box_verif_whatsapp"  style="height: 100%; padding: 10px; display: flex; flex-direction: column;">
            <label style="padding: 5px 5px;border-bottom: 1px solid;">Verificación: Por W</label>
            <div style="padding: 5px 5px;">
                <div class="row col-md-5" >
                    <label >Solicitado:</label>
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px; display: none;">
                        <!-- <label><input type="checkbox" name="analisis_13_face" id="analisis_13_face" <?php // ($status_video->disabled) ? 'disabled' : '';  ?> >&nbsp; Foto Perfil </label> -->
                    </div>
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px;">
                        <label><input type="checkbox" name="analisis_13_front" data-intentos_ws="<?=$intentos->front;?>"  data-estadoverifws="<?php echo (!$status_front->disabled_check) ? "true" : "false";  ?>" id="analisis_13_front" <?php echo ($status_front->disabled_check) ? 'disabled' : '';  ?> >&nbsp; Documento Frente </label>
                    </div>
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px;">
                        <label><input type="checkbox" name="analisis_13_back" data-intentos_ws="<?=$intentos->back;?>" data-estadoverifws="<?php echo (!$status_back->disabled_check) ? "true" : "false";  ?>" id="analisis_13_back" <?php echo ($status_back->disabled_check) ? 'disabled' : '';  ?>>&nbsp; Documento Reverso </label>
                    </div>
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px;">
                        <label><input type="checkbox" name="analisis_13_video" data-intentos_ws="<?=$intentos->video;?>" data-estadoverifws="<?php echo (!$status_video->disabled_check) ? "true" : "false";  ?>" id="analisis_13_video" <?php echo ($status_video->disabled_check) ? 'disabled' : '';  ?>>&nbsp; Video Verificacion </label>
                    </div>
                    <div class="col-md-12" style="padding-top: 25px">
                        <button class="btn btn-success" id="btn_chatbot_get_requeriments" disabled >VOLVER A SOLICITAR</button>
                    </div>			
                </div>
                <div class="row col-md-5" id="estado_">
                    <label >Estado de Solicitud:</label>
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px; display: none;" data-estadoverifws="<?php //= $status->face; ?>" > <?php //$status->face; ?></div>
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px;" ><?=$status_front->msj; ?></div>
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px;" ><?=$status_back->msj; ?></div>
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px;" ><?=$status_video->msj; ?></div>
                   
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px; padding-top: 28px">
                        <div class="row" style="height: 36px; display: flex; align-items: center; font-weight: 600" >
                            <?php
                                if ($intentos->front == 1 && $intentos->back == 1 && $intentos->video == 1):
                                    if(!$status_front->disabled && !$status_back->disabled && !$status_video->disabled):
                                        echo '<div class="col-md-12" style="vertical-align: middle; margin: 0px;">COMPLETADO</div>';
                                    else:
                                        echo '<div class="col-md-12" style="vertical-align: middle; margin: 0px;">PENDIENTE</div>';
                                    endif;
                                else:
                                    if(!$status_front->disabled && !$status_back->disabled && !$status_video->disabled):
                                        echo '<div class="col-md-12" style="vertical-align: middle; margin: 0px;">COMPLETADO</div>';
                                    else:
                                        echo '<div class="col-md-12" style="vertical-align: middle; margin: 0px;">REENVIO</div>';
                                    endif;
                                endif;
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row col-md-2" id="estado_veriff">
                    <label >Biometria:</label>
                    <div class="col-md-12" style="vertical-align: middle; margin: 0px; ">
                    <?php 
                        switch ($data->validation) {
                            case 'SUCCCESS':
                                echo '<i class="fa fa-lg fa-check" style="color:green" aria-hidden="true"></i>';
                                break;
                            case 'NOT_MATCH':
                                echo '<i class="fa fa-lg fa-exclamation-triangle" style="color:red" aria-hidden="true"></i>';
                                break;
                            default:
                                echo '';
                                break;
                        }
                    ?>
                    </div>
                </div>
            </div>
			<hr width=100% text-color="#e3e3e3" size=5 style=" margin-top: 10px; margin-bottom: 10px ">
            <div style="display: flex; flex-direction: row; justify-content: flex-start; height: 30px">
            	<label for="exampleFormControlTextarea1">Resultado:</label>
            	<div class="form-group " style="display: flex;" >
            		<div class="icheck-success d-inline" style="padding-left: 50px;">
            			<label for="rd_califica1">Coincide</label>
            			<input type="radio" id="rd_califica1" value=0 name="resul_val_ws" <?=($docs['ws']['data']->resultado == 0 && isset($docs['ws']['data']->resultado))? 'checked' : '';?> >
            		</div>
            		<div class="icheck-warning d-inline" style="padding-left: 20px;">
            			<label for="rd_califica2">No Coincide</label>
            			<input type="radio" id="rd_califica2" value=1 name="resul_val_ws" <?=($docs['ws']['data']->resultado == 1 && isset($docs['ws']['data']->resultado))? 'checked' : '';?>>
            		</div>
            	</div>
            </div>

			<hr width=100% text-color="#e3e3e3" size=5 style=" margin-top: 10px; margin-bottom: 10px ">
            <div class="text-center" style="padding-top: 5px; display: flex; flex-wrap: wrap;">
                <?php
                    $status_textarea = (bool) false;
                    if (!$status_front->disabled && !$status_back->disabled && !$status_video->disabled) {
                        $status_textarea = (bool) true;
                    }
                ?>
                <label >Observaciones: </label>
                <textarea id="textarea_chatbot_get_requeriments" style="width: 90%; height: 70px; marginn: 10px 0px";  <?php echo (!$status_textarea) ? 'disabled' : '';  ?> ><?=($docs['ws']['data']->respuesta_identificacion != null || $docs['ws']['data']->respuesta_identificacion != '')? $docs['ws']['data']->respuesta_identificacion : ''; ?></textarea>
            </div>
            <div class="text-center" style="margin: 10px 0px">
                <button class="btn btn-default btn-info" id="btn_validacion_biometria_whatsapp" <?php echo (!$status_textarea) ? 'disabled' : '';?> style="color: white; width: 50%" ><i class="fa fa-user" aria-hidden="true"></i> CONFIRMAR VERIFICACIÓN</button>
            </div>		
        </div>
    </div>

    <hr width=100% text-color="#FF0000" size=10>
        <?php endif; ?>

    <div class="text-center testimonial-group col-md-9">
        <div id="fotos" class="row" style="height: auto; padding: 10px; white-space: nowrap; overflow-x: auto; ">
            <?php foreach ($docs['data'] as $key => $doc):?>   
                <?php if(!isset($doc['scan_reference'])):?>
                    <?php                
                        //if($doc['scan_reference'] == ""){
                        if($doc['is_image']):
                                ?>
                                <div class="col-md-2 item-galery" id="fotos_<?=$doc['sid']?>"  style=" display: inline-block; float: none; margin-left: 35px;">
                                    
                                    <div class="fotos_thumbnail" data-grade="<?=$doc['rotation']?>" data-src="<?=base_url($doc['patch_imagen'])?>"
                                        style="--fotos_thumbnail: url(<?=base_url($doc['patch_imagen'])."?".time(); ?>) no-repeat center center; --fotos_thumbnail_transform: rotate(<?=$doc['rotation']?>deg);">
                                    </div>
                                    <div>
                                        <div style="font-size: 11px; padding: 5px 0px"><?php echo get_datetime_format($doc['fecha_carga']);?></div>
                                    </div>
                                    <div class="caption">
                                        <button class="screen_1 btn btn-default" onclick="box_galery.compareImages(this)" data-root_div="fotos" data-sid="<?=$doc['sid']?>" data-grade="<?=$doc['rotation']?>" data-view="screen_1" data-src="<?php echo base_url($doc['patch_imagen'])?>" >1</button>
                                        <button class="screen_2 btn btn-default" onclick="box_galery.compareImages(this)" data-root_div="fotos" data-sid="<?=$doc['sid']?>" data-grade="<?=$doc['rotation']?>" data-view="screen_2" data-src="<?php echo base_url($doc['patch_imagen'])?>" >2</button>
                                    </div>
                                </div>
                        <?php endif; 
                        
                        if($doc['extension'] == '.mp4' || $doc['extension'] == '.webm'):
                        
                        ?> 
                        <div class="col-md-2 item-galery-video" style="margin-left:100px; width: 200px;">
                            <a href="<?php echo base_url($doc['patch_imagen'])?>" target="_blank" class="btn btn-info" style="font-size:smaller; width:100%"><?php echo $doc['etiqueta'];?></a>
                        </div>
                                    
                        <?php endif; ?>



                    <?php else: 
                        if($doc['extension'] == '.mp4'  && $doc['is_image'] == 0 && $doc['id_imagen_requerida'] == 26):
                            ?>
                            <div class="col-md-2 item-galery-video" style=" display: inline-block; float: none; padding-top: 10px; margin-left: 35px; vertical-align: top; width: 200px;">
                                <figure  class="container-video-ws">
                                    <video height="150px" preload="metadata" >
                                        <source src="<?php echo base_url($doc['patch_imagen'])?>#t=1" type="video/mp4">
                                    </video>
                                    <div class="player-buttons" onclick="show_verrif_whatsapp('<?=base_url($doc['patch_imagen'])?>')"></div>
                                </figure>
                                <div style="padding: 5px 0px;"><?php echo $doc['etiqueta'];?></div>
                                <div style="font-size: 11px; padding-bottom: 5px"><?php echo get_datetime_format($doc['fecha_carga']);?></div>
                            </div>
                            <?php
                        endif;
                        ?>

                    <?php endif; ?>       
            <?php endforeach; ?>
        </div>
    </div>


    <?php if(in_array(strtoupper($solicitude['estado']),['ANALISIS','VERIFICADO','VALIDADO','APROBADO','TRANSFIRIENDO'])) : 
        //Si viene alguna foto con jumio mostrar botones
    ?>               
            <div class="col-md-3" style="position: relative;overflow-y: scroll; margin-bottom: 12px; margin-top: 12px;">
                <div class="text-center" style="display: flex; flex-direction: column;">                
                    <button type="button" style="margin: 5px 0px" class=" btn btn-sm btn-warning validate_identity" data-reference="VERIFICACION DE IDENTIDAD" data-type_gestion="8">Reenviar a verificación</button>
                    <button type="button" style="margin: 5px 0px" class=" btn btn-sm btn-info biometria" data-reference="BIOMETRIA" onclick="send_biometria()">Enviar por Whatsapp</button>
                    <button type="button" style="margin: 5px 0px" class=" btn btn-sm btn-success validate_image" data-type_gestion="8">Actualizar imagenes</button>                
                    <button type="button" style="margin: 5px 0px" class=" btn btn-sm btn-success " id="btn_update_videocall" style="display : none;" >Actualizar Videollamadas</button>                
                </div>
            </div>        
        <?php endif; ?>    
</div>
    <hr width=100% text-color="#FF0000" size=20>
            <!-- contar si tiene imagen en data, si tiene hacer lo anterior -->
<?php $existen_imagenes = 0;
foreach ($docs['origin'] as $key => $jumio):
        foreach ($docs['data'] as $key => $doc):            
            if($doc['scan_reference'] == $jumio['scanReference']): 
                if($doc['is_image']): 
                    $existen_imagenes = $existen_imagenes + 1;
                endif;
            endif;
        endforeach;         
endforeach;?>    
            
            
            
<?php if($existen_imagenes != 0): ?>                
            
<?php 
    $textImagen_images = [
        1 => '<label>Selfie</label>',
        2 => '<label>Cedula - Frente</label>',
        3 => '<label>Cedula - Dorso</label>',
        18 => '<label>Video - Selfie</label>'
    ];
    
    foreach ($docs['origin'] as $key => $jumio):?>
    <div class="text-center testimonial-group verificadas col-md-9"> 
        <div class="row images" id="veriff" style="height: auto; padding: 10px; white-space: nowrap; overflow-x: auto; ">
        <?php foreach ($docs['data'] as $key => $doc):?>
            
         <?php if($doc['scan_reference'] == $jumio['scanReference']): ?>                  
                    <?php if($doc['is_image']): ?>
                        
                            <div class="col-md-2 item-galery" id="veriff_<?=$doc['sid']?>" style="display: inline-block; float: none; margin-left: 35px;">
                                <div class="veriff_thumbnail" data-grade="<?=$doc['rotation']?>" data-src="<?=base_url($doc['patch_imagen'])?>"
                                    style="--veriff_thumbnail: url(<?=base_url($doc['patch_imagen'])."?".time(); ?>) no-repeat center center; --veriff_thumbnail_transform: rotate(<?=$doc['rotation']?>deg);">
                                </div>
                                <div style="padding: 5px 0px;">
                                    <div><?=$textImagen_images[$doc['id_imagen_requerida']];?></div>
                                    <div style="font-size: 11px; padding-bottom: 5px"><?php echo get_datetime_format($doc['fecha_carga']);?></div>
                                </div>
                                <div class="caption">
                                    <button class="screen_1 btn btn-default" onclick="box_galery.compareImages(this)" data-root_div="veriff" data-sid="<?=$doc['sid']?>" data-grade="<?=$doc['rotation']?>" data-view="screen_1" data-src="<?php echo base_url($doc['patch_imagen'])?>" >1</button>
                                    <button class="screen_2 btn btn-default" onclick="box_galery.compareImages(this)" data-root_div="veriff" data-sid="<?=$doc['sid']?>" data-grade="<?=$doc['rotation']?>" data-view="screen_2" data-src="<?php echo base_url($doc['patch_imagen'])?>" >2</button>
                                </div>
                            </div>
                    <?php endif;
                    if($doc['extension'] == '.mp4' || $doc['extension'] == '.webm'):
                    ?>
                        <div class="col-md-2 item-galery-video" style=" display: inline-block; float: none; margin-left: 35px; vertical-align: top">
                            <figure  class="container-video-images">
                                <video height="250px" preload="metadata" >
                                    <source src="<?php echo base_url($doc['patch_imagen'])?>" type="video/mp4">
                                </video>
                                <div class="player-buttons" onclick="show_verrif_whatsapp('<?=base_url($doc['patch_imagen'])?>')"></div>
                            </figure>
                            <div style="padding: 5px 0px;"><?php echo $textImagen_images[$doc['id_imagen_requerida']];?></div>
                            <div style="font-size: 11px"><?php echo get_datetime_format($doc['fecha_carga']);?></div>
                        </div>
                                    
                    <?php endif; ?>
            <?php endif;?>
        <?php endforeach;?>
        </div>       
    </div>
    <?php foreach ($docs['data'] as $key => $doc):?>            
         <?php if($doc['scan_reference'] == $jumio['scanReference'] && $doc['is_image']):?>            
            <div class="col-md-3" style="position: relative;overflow-y: scroll;">
                <label>Verificación: <?php 
                
                        if(isset($jumio['source']))
                        {
                            echo 'Por Fotos';
                        } else if(isset($jumio['validador']) && $jumio['validador'] == 'eid'){
                            echo 'Por Video';
                        } else if(isset($jumio['validador']) && $jumio['validador'] == 'veriff') {
                            echo 'Por V';
                        } else if(isset($jumio['validador']) && $jumio['validador'] == 'meta') {
                            echo 'Por M';
                        } else{
                            echo '';
                        }
                        
                        ?></label>
                <div class="grid-striped">
                    <div class="row css-table-row" >
                        <div class="col-md-4 css-table-row text-left" style=" margin: 0px; padding: 0px;"><small>Fecha:</small></div>
                        <div class="col-md-8 css-table-row" style="vertical-align: middle; margin: 0px;"><strong style="font-size: 12px;"><?php echo (isset($jumio['fecha']))? date_format(date_create($jumio['fecha']),'d/m/Y H:i:s'):''?></strong></div>
                    </div>
                    <div class="row css-table-row" >
                        <div class="col-md-4 css-table-row text-left" style=" margin: 0px; padding: 0px;"><small>Resultado:</small></div>
                        <div class="col-md-8 css-table-row" style="vertical-align: middle; margin: 0px; <?php  
                        
                            
                            if(isset($jumio['respuesta_identificacion']) && !empty($jumio['respuesta_identificacion']))
                            {
                                echo strtoupper($jumio['respuesta_identificacion'])=='FRAUDE' ? 'color:red;':'';                 
                            }else{
                                echo '';	
                            }
                            ?>"><strong style="font-size: 12px;"><?php 
                                if(isset($jumio['validador']) && $jumio['validador'] == 'veriff'){
                                    echo (isset($jumio['respuesta_match']))? $jumio['respuesta_match']:'';
                                    //echo ($jumio['response_code'] == 'PENDIENTE' && ($jumio['respuesta_match'] == 'APROBADO' || $jumio['respuesta_match'] == 'VERIFICACION FALLIDA') )? $jumio['respuesta_match']:$jumio['response_code'];

                                } else {
                                    echo isset($jumio['respuesta_identificacion'])? $jumio['respuesta_identificacion']:'';
                                }
                                ?></strong>
                        </div>
                    </div><?php 
                        if (isset($jumio['respuesta_supervivencia']) && $jumio['respuesta_supervivencia'] != '' ) { ?>
                        <div class="row css-table-row" >
                            <div class="col-md-4 css-table-row text-left" style="margin: 0px; padding: 0px;"><small>Prueba Vida:</small></div>
                            <div class="col-md-8 css-table-row" style="vertical-align: middle; margin: 0px;"><strong style="font-size: 12px;">
                                <?php 
                                    if(isset($jumio['validador']) && $jumio['validador'] == 'meta'){
                                        if ($jumio['respuesta_supervivencia'] == 200) 
                                            echo 'OK';
                                        else
                                            echo 'Error';
                                    } else {
                                        echo (isset($jumio['respuesta_supervivencia']))? $jumio['respuesta_supervivencia']:'';
                                    }
                                  ?></strong></div>
                        </div>
                        <div class="row css-table-row" >
                            <div class="col-md-4 css-table-row text-left" style=" margin: 0px; padding: 0px;"><small>Coincidencia:</small></div>
                            <div class="col-md-8 css-table-row" style="vertical-align: middle; margin: 0px;"><strong style="font-size: 12px;"><?php echo (isset($jumio['respuesta_match']) && (!isset($jumio['validador']) || ( isset($jumio['validador']) && $jumio['validador'] != 'veriff')))? $jumio['respuesta_match']:''?></strong></div>
                        </div>
                    <?php } 
                    
                        if ($jumio['respuesta_match'] == 'APROBADO' ) { ?>
                            <div class="row css-table-row" >
                                <div class="col-md-4 css-table-row text-left" style=" margin: 0px; padding: 0px;"><small>Observaciones:</small></div>
                                <div class="col-md-8 css-table-row" style="vertical-align: middle; margin: 0px;">
                                    <textarea id="observacion_valid_galery" style="width:100%" rows="3" ><?=(!is_null($jumio['respuesta_identificacion']))? $jumio['respuesta_identificacion']:'';?></textarea>
                                    <button type="button" onclick="btn_procesar_galery(this)" id="btn_galery_register" data-id-verify="<?=$jumio['id'] ?>" class=" btn btn-sm btn-success" style="float: right;">Registrar</button>                
                                </div>
                            </div>
                    <?php } ?>
                </div>
            </div>
        <hr width=100% text-color="#FF0000" size=20>
        <?php break; endif;?>
    <?php endforeach;?>
<?php endforeach;?>
<?php endif;?>
    <div id="compare" class="box-footer" style="display: none;">
        <div id="compare-title" >
            <div id="compare-rotate-screen_1">
                <div>
                    <button class="btn btn-primary" data-grados="0" data-rotate="left" id="screen_1" data-view="screen_1" onClick="box_galery.rotate(this)" ><i class="fa fa-undo"></i></button>
                    <button class="btn btn-primary" data-grados="0" data-rotate="right" id="screen_1" data-view="screen_1" onClick="box_galery.rotate(this)" ><i class="fa fa-repeat"></i></button>
                </div>
            </div>
            <div style="display: flex; flex-direction: column;">
                <button id="icon_close" class="btn btn-google"><i class="fa fa-close"></i></button>
            </div>
            <div id="compare-rotate-screen_2">
                <button class="btn btn-primary" data-grados="0" data-rotate="left" id="screen_2" data-view="screen_2" onClick="box_galery.rotate(this)" ><i class="fa fa-undo"></i></button>
                <button class="btn btn-primary" data-grados="0" data-rotate="right" id="screen_2" data-view="screen_2" onClick="box_galery.rotate(this)" ><i class="fa fa-repeat"></i></button>
            </div>
        </div>
        <div id="change_img" style="display: flex;justify-content: space-evenly;">
            <div id="title_1"></div>
            <div id="title_button" >
                <button id="icon_change" onClick="box_galery.changedImage()" class="btn btn-success"><i class="fa fa-arrows-h"></i></button></div>
            <div id="title_2"></div>
        </div>
        <div class="row">
            <canvas id="screen_1" class="col-md-6" data-src=""></canvas>
            <canvas id="screen_2" class="col-md-6" data-src=""></canvas>
        </div>
    </div>
    
</div> <!-- end box-default -->



<script type="text/javascript">
	$("document").ready(function(){
	
		$("#box_galery .validate_identity").on('click', function(event){
                    $(this).attr('disabled','disabled');
                    let id_solicitud = $("#id_solicitud").val();
                    let id_operador = $("#box_galery #id_operador").val();
                    let estado = $(this).data('reference');
                    let type_contact = $(this).data('type_gestion');
                    solicitudeUpdateStep(id_solicitud, id_operador, 13, type_contact, estado);
		});    
                $("#box_galery .validate_image").on('click', function(event){
                    $(this).attr('disabled','disabled');
                    let id_solicitud = $("#id_solicitud").val();                   
                    solicitudeUpdateImage(id_solicitud);
		}); 
                // Cierra la pantalla de comparacion
		$(".box_galery #compare #icon_close").on("click",function(){
            $("canvas").get(0).getContext("2d").reset()
            $("canvas").eq(0).removeData()
            $("canvas").get(1).getContext("2d").reset()
            $("canvas").eq(1).removeData()
            $("#compare").hide();
        }); 
        
        $('.box_galery .testimonial-group.verificadas').each(function () {
                 
                 if ($(this).find(".item-galery").length < 1) {
                     $(this).remove();
                 }
             });
        
		$("button#btn_update_videocall").on("click", function () {
            $.ajax({
                url: base_url + 'video_llamadas/update_video',
				type: 'POST',
				data: {
                    id_solicitud: $("#id_solicitud").val(),
                    documento: $("#client").data("number_doc")
				},
				success: (data) => {
                    if (data.success) {
                        $(".box_galery").html('<br><br><div class="text-center"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
                        setTimeout(() => {
                            cargar_box_galery($("#id_solicitud").val())
                        }, 2000);
                    }
				}
			})
		});
        
		$("button#btn_chatbot_get_requeriments").on("click", function () {
            if (!veriff_ws_reenvio.box.$box_body.parent().hasClass('hidden'))
                veriff_ws_reenvio.close()
            
            flowdata = {
                'analisis_13_front' : 1,
                'analisis_13_back'  : 1,
                'analisis_13_video' : 1,
            }
            $("#box_verif_whatsapp input:checkbox").each(function( index , elmn) {
                intentos = $(elmn).data('intentos_ws')
                estados = $(elmn).data('estadoverifws')
                if (intentos == 1 && estados != 1) {
                    flowdata[$(elmn).attr('name')] = $(elmn).is(':checked')? 1:2;
                } else {
                    flowdata[$(elmn).attr('name')] = $(elmn).is(':checked')? 1:0;
                }
                if ($(elmn).is(':checked')) 
                    $('fieldset#'+$(elmn).attr('name')).addClass('activo');
                else
                    $('fieldset#'+$(elmn).attr('name')).removeClass('activo');
            });
            
		    veriff_ws_reenvio.init(flowdata);
            
		});
        
		$("button#btn_validacion_biometria_whatsapp").on("click", function () {

            if ($("#textarea_chatbot_get_requeriments").val() !== '' && $("input[name='resul_val_ws']:checked").length > 0) {
                Swal.fire({
                    title: '¡Atención!',
                    text: '¿Quieres confirmar la verificación?',
                    type: 'warning',
                    confirmButtonText: 'Verificar',
                    cancelButtonText: 'Cancelar',
                    showCancelButton: 'true'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url:  base_url + 'atencion_cliente/validacion_biometria_whatsapp',
                            type: 'POST',
                            data: {
                                id_solicitud: $("#id_solicitud").val(),
                                texto: $("#textarea_chatbot_get_requeriments").val(),
                                resultado : $("input[name='resul_val_ws']:checked").val(),
                                id_whatsapp_scan :$("#id_veriff_whatsapp").val()
                            },
                            success: (resp) => {
                                data = JSON.parse(resp);
                                if (data.success) {
                                    let id_operador = $("#id_operador").val();
                                    let id_solicitud = $("#id_solicitud").val();
                                    let type_contact = 193;
                                    let comment =   "<b>[VERIFICACIÓN COMPLETADA]</b>" + 
                                                    "<br>Resultado: " + $("input[name='resul_val_ws']:checked").parent().find('label').html() + "." +
                                                    "<br>" + $("#textarea_chatbot_get_requeriments").val() + ".";
                                    saveTrack(comment, type_contact, id_solicitud, id_operador);
                                    cargar_box_galery(id_solicitud);
                                }
                            }
                        })
                    }
                });
            } else {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debes dejar una observación para completar la verificación',
                    type: 'error',
                    confirmButtonText: 'Ok'
                });
            }
            
		});

        $("#box_verif_whatsapp input:checkbox").on('change', function () {
            if ($(this).is(':checked')) {
                $("button#btn_chatbot_get_requeriments").prop('disabled', false);
            } else {
                if ($("#box_verif_whatsapp input:checkbox:checked").length == 0)
                    $("button#btn_chatbot_get_requeriments").prop('disabled', true);
            }
        });

        $.ajax({
            url:  base_url + 'video_llamadas/get_video_status/' + $("#id_solicitud").val(),
            type: 'GET',
            success: (data) => {
                if (data.success)
                    $('#btn_update_videocall').show()
                else
                    $('#btn_update_videocall').hide()
            }
        })

        show_verrif_whatsapp = (videoFile) => {
            modalws     = $('#veriff-ws-video');
            myRange_ws  = $('#myRange-ws', modalws);
            Volumen_ws  = $('#volumeslider', modalws);
            lbl_ws_begin= $('#lbl_verifWs_timebegin', modalws);
            lbl_ws_end  = $('#lbl_verifWs_timeend', modalws);
            video_Verws = $('#video-ws', modalws)[0];
            spin_Verws = $('#video-ws-spin', modalws);
            source_Verws= $('source', $('#video-ws', modalws));
            source_Verws.attr('src', videoFile + "#t=0");
            video_Verws.load();
            Volumen_ws.val(75);
            video_Verws.volume = 0.75;
			$(video_Verws).css('display', 'none');
            spin_Verws.show();
            modalws.modal({backdrop: true})

            $(video_Verws).on('loadedmetadata',function(){
				if (video_Verws.videoWidth > video_Verws.videoHeight) {
                    $(video_Verws).css('width', '100%');
					$(video_Verws).css('height', 'auto');
				} else {
                    if(video_Verws.videoHeight > 650 ) {
                        $(video_Verws).css('width', 'auto');
						$(video_Verws).css('height', '650px');
					} else {
                        $(video_Verws).css('width', 'auto');
						$(video_Verws).css('height', '100%');
					}
                }
                spin_Verws.hide();
                $(video_Verws).css('display', 'block');
            });
            $('#video-ws', modalws).on('timeupdate', seektimeupdate )
            myRange_ws.on('change', (event) => {
                seekto = video_Verws.duration * (myRange_ws.val() / 100);
                video_Verws.currentTime = seekto;
            })
            Volumen_ws.on('mousemove', (event) => {
                video_Verws.volume = Volumen_ws.val() / 100;
            })
            modalws.on('hidden.bs.modal', function (e) {
                source_Verws.attr('src', '');
                $(video_Verws).attr('style', '');
                video_Verws.load();
                modalws.off();
                $(video_Verws).off();
            })
            playPause = () => { 
                if (video_Verws.paused) {
                    video_Verws.play(); 
                } else 
                    video_Verws.pause(); 
            } 
        }

        function seektimeupdate() {
            var newtime = video_Verws.currentTime * (100 / video_Verws.duration);
            if (isNaN(newtime)) {
                myRange_ws[0].value = 0;
                lbl_ws_begin.html("00:00")
                lbl_ws_end.html("00:00")
            } else {
                myRange_ws[0].value = newtime;
                var curmins = Math.floor(video_Verws.currentTime / 60),
                    cursecs = Math.floor(video_Verws.currentTime - curmins * 60),
                    durmins = Math.floor(video_Verws.duration / 60),
                    dursecs = Math.floor(video_Verws.duration - durmins * 60);
                if (cursecs < 10) { cursecs = "0" + cursecs; }
                if (dursecs < 10) { dursecs = "0" + dursecs; }
                if (curmins < 10) { curmins = "0" + curmins; }
                if (durmins < 10) { durmins = "0" + durmins; }
                lbl_ws_begin.html(curmins + ":" + cursecs)
                lbl_ws_end.html(durmins + ":" + dursecs)
            }
        }       
        box_galery = {}
        box_galery.var = {}
        box_galery.var = {
            screen_1 : {sid : null},
            screen_2 : {sid : null},
            root_div : null
        }
        box_galery.rotate = (elem) => {
            
            left    = [0, 270, 180, 90];
            right   = [0, 90, 180, 270];
            grados  = $(elem).data('grados');
            rotate  = $(elem).data('rotate');
            view    = $(elem).data('view');
            indexActual = null;
            newgrados = null;
            if (rotate == 'left') {
                indexActual = left.indexOf(grados);
                if (indexActual < (left.length - 1)) 
                    newgrados = left[indexActual + 1];
                else
                    newgrados = left[0];       
            } 
            if (rotate == 'right') {
                indexActual = right.indexOf(grados);
                if (indexActual < (right.length - 1))
                    newgrados = right[indexActual + 1];
                else
                    newgrados = right[0];
            } 
            $("div#compare-rotate-"+view+" button").data('grados', newgrados);
            $("#"+box_galery.var.root_div+"_"+box_galery.var[view].sid+" button").eq(0).data('grade', newgrados)
            $("#"+box_galery.var.root_div+"_"+box_galery.var[view].sid+" button").eq(1).data('grade', newgrados)
            
            data = {
                grados: newgrados,
                view: view,
                dataSrc : $("canvas#"+view).data('src'),
                canvas : $("canvas#"+view).get(0),
                ctx : $("canvas#"+view).get(0).getContext("2d")
            }
            box_galery.set_rotateImages(data)
            box_galery.LoadImages(data)
        }

        box_galery.compareImages = (elm) => {
            $(".box_galery #compare").show();
            view = $(elm).data('view');
            sid = $(elm).data('sid');
            box_galery.var[view].sid = sid;
            box_galery.var.root_div  = $(elm).data('root_div');
            data = {
                grados: $(elm).data('grade'),
                view: view,
                dataSrc : $(elm).data('src'),
                canvas : $("canvas#"+view).get(0),
                ctx : $("canvas#"+view).get(0).getContext("2d")
            }
            
            $("canvas#"+view).data('src', $(elm).data('src'));
            $("canvas#"+view).data('root_div', $(elm).data('root_div'));
            $("canvas#"+view).data('id_'+view.split('_')[1], $(elm).data('sid'));
            $('div#compare div#title_'+ view.split('_')[1]).html($(elm).data('title'))
            $("#compare-rotate-"+view+" button").each(function(index, el) {
                $(el).data('grados', $(elm).data('grade'));
            });
            if ($(elm).data('root_div') == 'whatsapp_veriff') {
                if($('div#compare canvas').eq(0).data('root_div') != 'whatsapp_veriff' || $('div#compare canvas').eq(1).data('root_div') != 'whatsapp_veriff') 
                    $('div#compare #change_img').hide()
                else 
                    $('div#compare #change_img').show()
            }
            else 
                $('div#compare #change_img').hide()

            box_galery.LoadImages(data)
        }

        box_galery.LoadImages = (data) => {
            var img = new Image();
            img.onload = function() {
                data.ctx.reset()
                if (img.width < img.height){
                    data.canvas.width=img.width ;
                    data.canvas.height=img.height + 20 ;
                    orientacion = 'Vertical';
                } else {
                    data.canvas.height=img.width + 20 ;
                    data.canvas.width=img.height;
                    orientacion = 'Horizontal';
                }
                
                var MAX_WIDTH = data.canvas.width;
                var MAX_HEIGHT = data.canvas.width;

                var width = img.width;
                var height = img.height;

                if (width > height) {
                    if (width > MAX_WIDTH) {
                        height = height * (MAX_WIDTH / width);
                        T90     = MAX_WIDTH
                        T180    = height
                        T270    = img.width
                        width   = MAX_WIDTH;
                    }
                } else {
                    if (height > MAX_HEIGHT) {
                        width = width * (MAX_HEIGHT / height);
                        T90     = MAX_HEIGHT
                        T180    = height
                        T270    = width
                        height = MAX_HEIGHT;
                    }
                }  
                
                data.ctx.save();
                switch (data.grados) {
                    case 0:
                        data.ctx.translate(0, 10);
                        data.ctx.rotate(data.grados * Math.PI / 180);
                        (orientacion == 'Horizontal') ? data.ctx.drawImage(img, 0, 0, width, height) : data.ctx.drawImage(img, 0, 0);
                        (orientacion == 'Horizontal') ? thumb  = {left : '0%', width: '100%'} : thumb  = {left : '-15%', width: '250px'} ;
                        break;
                    case 90:
                        data.ctx.translate(T90 , 10);
                        data.ctx.rotate(data.grados * Math.PI / 180);
                        (orientacion == 'Horizontal') ? data.ctx.drawImage(img, 0, 0) : data.ctx.drawImage(img, 0, 0,width, height);
                        (orientacion == 'Horizontal') ? thumb  =  {left : '-15%', width: '250px'} : thumb  = {left : '-15%', width: '250px'};
                        break;
                    case 180:
                        data.ctx.translate(MAX_WIDTH,T180 + 10 ); 
                        data.ctx.rotate(data.grados * Math.PI / 180);
                        (orientacion == 'Horizontal') ? data.ctx.drawImage(img, 0, 0, width, height) : data.ctx.drawImage(img, 0, 0);
                        (orientacion == 'Horizontal') ? thumb  = {left : '0%', width: '100%'} : thumb  = {left : '-15%', width: '250px'} ;
                        break;
                    case 270:
                        data.ctx.translate(0,T270 + 10);
                        data.ctx.rotate(data.grados * Math.PI / 180);
                        (orientacion == 'Horizontal') ? data.ctx.drawImage(img, 0, 0) : data.ctx.drawImage(img, 0, 0, width, height);
                        (orientacion == 'Horizontal') ? thumb  =  {left : '-15%', width: '250px'} : thumb  = {left : '0%', width: '100%'};
                        break;
                }
                data.ctx.restore();
                data_div = {}
                data_div['--'+box_galery.var.root_div+'_thumbnail_transform'] = 'rotate('+data.grados+'deg)'
                data_div['--'+box_galery.var.root_div+'_thumbnail_width'] = thumb.width
                data_div['--'+box_galery.var.root_div+'_thumbnail_left'] = thumb.left
                $("#"+box_galery.var.root_div+"_"+box_galery.var[view].sid+" ."+box_galery.var.root_div+"_thumbnail").css(data_div)
            };
            img.src = data.dataSrc
        }
        
        box_galery.LoadImages_thumb = (root_div) => {
            $("#"+root_div+" ."+root_div+"_thumbnail").each(function(index, el) {
                var img     = new Image();
                var $img    = $(img);
                $img.on('load', function() {
                    grados = $(el).data('grade');
                    if (img.width < img.height){
                        orientacion = 'Vertical';
                    } else {
                        orientacion = 'Horizontal';
                    }
                    switch (grados) {
                        case 0:   (orientacion == 'Horizontal') ? thumb  = {left : '0%', width: '100%'} : thumb  = {left : '-15%', width: '250px'} ; break;
                        case 90:  (orientacion == 'Horizontal') ? thumb  = {left : '-15%', width: '250px'} : thumb  = {left : '-15%', width: '250px'}; break;
                        case 180: (orientacion == 'Horizontal') ? thumb  = {left : '0%', width: '100%'} : thumb  = {left : '-15%', width: '250px'} ; break;
                        case 270: (orientacion == 'Horizontal') ? thumb  = {left : '-15%', width: '250px'} : thumb  = {left : '0%', width: '100%'}; break;
                    }
                    
                    data_div = {}
                    data_div['--'+root_div+'_thumbnail_transform'] = 'rotate('+grados+'deg)'
                    data_div['--'+root_div+'_thumbnail_width'] = thumb.width
                    data_div['--'+root_div+'_thumbnail_left'] = thumb.left
                    $(el).css(data_div)
                })
                .attr('src', $(el).data('src'))
            });
        }  
        box_galery.LoadImages_thumb('whatsapp_veriff')
        box_galery.LoadImages_thumb('fotos')
        box_galery.LoadImages_thumb('veriff')
        
        box_galery.set_rotateImages = (data) => {
            load = $.ajax({url:base_url+'gestion/Galery/setRotation',type:'POST',data:{grados:data.grados,sid:box_galery.var[data.view].sid}})
        }

        box_galery.changedImage = () => {            
            if ($('div#compare canvas').eq(0).data('src') != '' && $('div#compare canvas').eq(1).data('src') != '') {
                if($('div#compare canvas').eq(0).data('root_div') != 'whatsapp_veriff' || $('div#compare canvas').eq(1).data('root_div') != 'whatsapp_veriff') {
                    Swal.fire({
                            title: '¡Error!',
                            text: 'No puede intercambiar imágenes entre servicios',
                            type: 'error',
                            confirmButtonText: 'Ok'
                        });
                } else {
                    if ($('div#compare canvas#screen_1').data('id_1') == $('div#compare canvas#screen_2').data('id_2')) {
                        Swal.fire({
                            title: '¡Error!',
                            text: 'No puede intercambiar dos imágenes iguales',
                            type: 'error',
                            confirmButtonText: 'Ok'
                        });
                    } else {
                        Swal.fire({
                            title: '¡Atención!',
                            text: '¿Quieres intercambiar estas imágenes?',
                            type: 'warning',
                            confirmButtonText: 'Confirmar',
                            cancelButtonText: 'Cancelar',
                            showCancelButton: 'true'
                        }).then((result) => {
                            if (result.value) {
                                $.ajax({
                                    url:  base_url + 'atencion_cliente/change_type_image',
                                    type: 'POST',
                                    data: {
                                        id_solicitud: $("#id_solicitud").val(),
                                        img_1: $('div#compare canvas#screen_1').data('id_1'),
                                        img_2: $('div#compare canvas#screen_2').data('id_2')
                                    },
                                    success: (resp) => {
                                        data = JSON.parse(resp);
                                        if (data.success) {
                                            Swal.fire("Se han intercambiado las imagenes correctamente","",  "success");
                                            cargar_box_galery($("#id_solicitud").val());
                                        }
                                    }
                                })
                            }
                        });
                    }
                }
            } else {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debes seleccionar ambas imagenes para intercambiar',
                    type: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        }
        $('div#compare #change_img').hide();

    });  
</script>
