<style>
    input.form-check-input{
        margin-left: 1%;
        margin-right: 1%;
        cursor: pointer;
        width: 15px;
        height: 15px;
    }
    .form-check-label {
        margin-left: 1%;
        /* margin-right: 1%; */
    }
    #result-cometario{
        height: 92px;
    }
    .contenedor::-webkit-scrollbar {
        -webkit-appearance: none;
    }
    .contenedor {
        /* border-bottom: 1px solid #d2d6de; */
    }
    .contenedor::-webkit-scrollbar:vertical {
        width:4px;
    }
    .contenedor::-webkit-scrollbar-thumb {
        background-color: #797979;
        border-radius: 20px;
        border: 2px solid #888888;
    }
    .contenedor::-webkit-scrollbar-track {
        border-radius: 10px;  
    }
    input[type="checkbox"] {
        background-color: #66bb6a;
        border-color: #66bb6a;
    }
</style>
<div id="auditoriaForm" style="">
                <input type="text" id="id_solicitud" value="" hidden>
                <input type="number" id="id_track" value="" hidden>
                <input type="number" id="tipo_operador" value="<?= $tipo_operador ?>" hidden>
                <input type="text" id="estado_solicitud" value="<?= $solicitude[0]["estado"]; ?>" hidden>
                <?php if(isset($audios[0]->id_track)){ ?>
                <div class="col-md-4 contenedor" id="contenedor_audio" style="padding:1%;overflow-y: auto; height: 270px;">
                    
                    <?php
                            foreach ($audios  as $audio) {
                            
                    ?>
                        <div class="col-md-12 audios_auditar" id="<?php echo $audio->id_track;?>" style="">
                            <h5 class="col-md-4">Fecha llamado</h5>
                            <h5 class="col-md-5">
                                <?php 
                                    $mifecha= new DateTime($audio->fecha_audio); 
                                    $mifecha->modify('-2 hours');
                                    echo $mifecha->format('d-m-Y H:i:s');
                                ?></h5>
                            <h5 class="col-md-3"><?php echo $audio->origen;?></h5>
                            <audio class="col-md-7" controls>
                                <source id="audio_<?php echo $audio->id_track; ?>" src="<?php echo $audio->path_audio; ?>" type="audio/ogg"> 
                            </audio>

                            <div class="form-check col-md-5 round">
                                
                                <label class="form-check-label-uno col-md-7" for="checkbox" style="padding: 0!important;font-weight: 400;font-size: 13px;margin-top: 4%;" >Corresponde a gestión: </label>
                                
                                <input type="checkbox" class="form-check-input col-md-2 check_audio" data-id_track="<?php echo $audio->id_track; ?>" data-numero_solicitud="<?php echo $audio->numero_solicitud; ?>" id="id_track" value="<?php echo $audio->id_track; ?>" name="" style="margin-top: 5%;margin-right: 10%;" >
                                
                                <button 
                                    id="reportar_audio" 
                                    class="col-md-2 btn btn-xs bg-light" 
                                    style="font-size: 18px;padding:0!important;height:30px;background-color: #dfdfdf;color:#bb0a09; margin-top: 3%;"
                                    onclick="reportarAudio(this);"
                                    data-telefono="<?php echo $audio->numero_solicitud;?>"
                                    data-id_track="<?php echo $audio->id_track;?>"
                                    data-fecha_audio="<?php $mifecha= new DateTime($audio->fecha_audio); 
                                                            $mifecha->modify('-2 hours');
                                                            echo $mifecha->format('d-m-Y H:i:s');?>"
                                    title="Reporatar problema con el audio.">
                                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        
					<?php 
                            
                        }?>
                </div>

                <div class="col-md-8" style="">
                    <ul class="nav nav-tabs col-md-12" style="padding: 0!important; border-radius: 1px!important;">
                   
                        <?php foreach ($parametros as $key => $value) { 
                            if($tipo_operador == 1 || $tipo_operador == 4){
                                if ($solicitude[0]["estado"] == "APROBADO" ||
                                    $solicitude[0]["estado"] == "TRANSFIRIENDO" ||
                                    $solicitude[0]["estado"] == "PAGADO" ||
                                    $solicitude[0]["estado"] == "RECHAZADO" || 
                                    $solicitude[0]["estado"] == "ANULADO") {
                                    echo '<li class="fondo col-md-2 '.($key == "Presentación" ? "active" : " ").'" id="mostrarPresentacion"><a class="text-center" href="#'.$value[0]["tag_id"].'" data-toggle="tab">'.$key.'</a></li>';
                                }else{
                                    echo '<li class="fondo col-md-2 '.($key == "Gestión" ? "active" : " ").'" id="mostrarPresentacion"><a class="text-center" href="#'.$value[0]["tag_id"].'" data-toggle="tab">'.$key.'</a></li>';
                                }
                            }else{
                                echo '<li class="fondo col-md-2 '.($key == "Presentación" ? "active" : " ").'" id="mostrarPresentacion"><a class="text-center" href="#'.$value[0]["tag_id"].'" data-toggle="tab">'.$key.'</a></li>';
                            }
                        }?>

                        <li class="fondo col-md-2" id="mostrarResultado"><a class="text-center" href="#resultado" id="configA" data-toggle="tab">Resultado</a></li>
                    </ul>

                    <div class="tab-content row">

                    <?php foreach ($parametros as $key => $value) { 
                        if($tipo_operador == 1 || $tipo_operador == 4){
                            if ($solicitude[0]["estado"] == "APROBADO" ||
                            $solicitude[0]["estado"] == "TRANSFIRIENDO" ||
                            $solicitude[0]["estado"] == "PAGADO" ||
                            $solicitude[0]["estado"] == "RECHAZADO" || 
                            $solicitude[0]["estado"] == "ANULADO") {?>
                                <div class="col-lg-12 tab-pane fade in <?= ($key == 'Presentación') ? 'active' : ' ' ?>" id="<?php echo $value[0]["tag_id"] ?>"  style="padding: 0.5%;">
                            <?php }else{ ?>
                                <div class="col-lg-12 tab-pane fade in <?= ($key == 'Gestión') ? 'active' : ' ' ?>" id="<?php echo $value[0]["tag_id"] ?>"  style="padding: 0.5%;">
                            
                            <?php } }else{?>
                                <div class="col-lg-12 tab-pane fade in <?= ($key == 'Presentación') ? 'active' : ' ' ?>" id="<?php echo $value[0]["tag_id"] ?>"  style="padding: 0.5%;">
                            <?php } ?>
                            <div id="" class="col-md-12">
                            <?php foreach ($value as $parametro) {  ?>
                                <div class="form-check col-md-12">
                                    
                                    <label class="form-check-label-uno col-md-4" ><?= $parametro['descripcion']?> </label>
                                    <?php foreach ($calificaciones as $calificacion) {  ?>
                                        <label class="form-check-label " ><?= $calificacion['nombre']?></label>
                                        <input type="radio" class="form-check-input inp_check_audio" id="" value="<?= $calificacion['etiqueta']?>" name="<?= $parametro['name']?>">
                                    <?php }?>
                                    
                                </div>
                                
                            <?php }?>    
                            </div>
                    
                        </div>
                    
                    <?php }?>
                        
                        <div class="col-lg-12 tab-pane fade" id="resultado"  style="padding: 0.5%;">
                            <!-- style="overflow: auto;" -->
                            <div id="" >
                                <div class="form-check col-lg-12">
                                    <!-- <select class="col-md-2 result-select" id="result-select"> 
                                        <option value="">Selecciona resultado</option>
                                        <option value="Capacitación">Capacitación</option>
                                        <option value="Apercibimiento">Apercibimiento</option>
                                        <option value="Alerta">Alerta</option>
                                        <option value="Suspensión">Suspensión</option>
                                        <option value="Sin alerta">Sin alerta</option>    
                                    </select>     -->
                                    <label class="form-check-label-uno col-md-12" style="" >Observaciones</label>
                                    <textarea class="col-md-11 result-cometario" id="result-cometario"></textarea>
                                    
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary guardar-btn col-md-2" onclick="envioFormAdutoriaLlamada();">Guardar</button>
                                    <!-- <?php if($tipo_operador == 1 || $tipo_operador == 4 ){
                                        if ($solicitude[0]["estado"] == "RECHAZADO" || $solicitude[0]["estado"] == "PAGADO") {?>
                                            <button class="btn btn-primary guardar-btn col-md-2 disabled" disabled="disabled" onclick="envioFormAdutoriaLlamada();">Guardar y Alertar</button>                                        
                                        <?php }
                                    }else{?>
                                                <button class="btn btn-primary guardar-btn col-md-2 disabled" disabled="disabled" onclick="envioFormAdutoriaLlamada();">Guardar y Alertar</button>
                                        <?php } ?> -->
                                </div>
                            </div>
                    
                        </div>
                        
                    </div>
                    <?php }else{?>
                        <div id="no_audios" style="text-align:center; height:190px;">
                            <input id="sin_audio" type="hidden" value="true"></h1>
                            <h1 style="padding-top: 4%;">No posee audios para auditar</h1>
                        </div>
                    <?php }?>
                </div>
            </div>

            <div class="modal fade" id="reportarAudioModal" tabindex="-1" role="dialog" >
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                

                    </div>
                </div>
            </div>
<script>
    $(document).ready(function(){
        var audios_tags = document.querySelectorAll('audio');
        var audios = [];
        audios_tags.forEach(element => {
            audios.push(element.id);
        });
        var currentAudio = '';
        // console.log(audios);
        $('audio').each(function(){
            this.addEventListener('play', function(){
                if(currentAudio != ''){
                    document.getElementById(currentAudio).pause();
                    this.play();
                    currentAudio = this.id;
                } else {
                    currentAudio = this.id;
                }
                
            });

            this.addEventListener('ended', function(){
                currentAudio = '';
            });
        });  
        let data = $("#sin_audio").val();
        if (data == 'true') {
            
        }
    });

    $(".inp_check_audio").on("click", function (e) {
        if(!$("input.check_audio").is(":checked")){
            Swal.fire("Ups!","Debe seleccionar un audio para evaluar","info");
            $(".inp_check_audio").prop('checked', false);
        }
    });
</script>