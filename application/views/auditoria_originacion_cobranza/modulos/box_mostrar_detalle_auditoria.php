

<style>
    .icon_close {
        width: 26px;
        height: 26px;
        font-size: 14px;
        line-height: 26px;
        position: relative;
        color: red;
        background: #d2d6de;
        border-radius: 50%;
        text-align: center;
        left: 15px;
        top: 3px;
    }
    div.hover:hover{
        font-weight:bold;
    }
    div.hover{
        font-size: 14px;
    }
    .contenedor::-webkit-scrollbar {
        -webkit-appearance: none;
    }
    /* .contenedor {
        border-bottom: 1px solid #d2d6de;
    } */
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
    audio::-webkit-media-controls-panel 
    {
        background-color: #FFFEDE;
    }

    audio::-webkit-media-controls-timeline 
    {
        background-color: #f7e6a4;
        border-radius: 25px;
        margin-left: 10px;
        margin-right: 10px;
    }

    .sel_hover:hover{
        background-color:#F0F0F0;
    }

</style>
<section class="box_detalle_auditoria" style="    border-top: 2.5px solid #00c0ef;">

    <?php 
        foreach ($auditoria['detalle_auditoria']  as $detalle_auditoria) {
           echo "<input type='hidden' id='dataGrafico' value='".$detalle_auditoria['grupo_parametro']."' data-calificacion='".$detalle_auditoria['calificacion']."' data-parametro_evaluado='".$detalle_auditoria['parametro_evaluado']."'>";

        }
    ?>
    <a id="close_llamado" href="#" title="Cerrar auditoría" class="pull-right" onclick="misAuditorias();" style="position: absolute; right: 43px; z-index: 1000; top: 5px;">
            <i class="fa fa-close icon_close"></i>
    </a>
    

    <div class="box-body" style=" font-size: 12px;border-bottom: 1px solid #d2d6de">
        <div class="col-md-6" style="margin-top: 2%;">
            <div class="col-md-12" style="text-align: left;background-color: #E1D1F6;height: 30px;padding-top: 0.5%;">
                <?php 
                    echo '<strong style="">DETALLE DE LA AUDITORIA: </strong>';                                    
                ?>
                
            </div>
            <table class="table table-bordered table-responsive display">
                <thead>
                    <tr class="table-light">
                        <th>Fecha Auditoria</th>
                        <th>Teléfono</th>
                        <th>Contacto</th>
                        <th>Operador Asignado</th>
                        <th>Auditor</th>
                        

                    </tr>
                </thead>
                <tbody>
                    <tr style="height: 38px">
                        <td><?= $auditoria['auditoria']->fecha_auditoria ;?></td>    
                        <td class='hd_center'><?= $auditoria['auditoria']->numero_telefonico;?></td>
                        <td class='hd_center'><?= $auditoria['auditoria']->contacto;?></td>
                        <td class='hd_center'><?= $auditoria['auditoria']->operador_asignado;?></td>                       
                        <td class='hd_center'><?= $auditoria['auditoria']->operador_auditor;?></td>                       
                    </tr>

                </tbody>
                
            </table>
            <div id="div_observacion" style="text-align: left;height: auto;padding-top: 1%;overflow-y: auto; margin-top:-1%; background-color: #E1E5FF; border-bottom: 1px solid #E0E0E0;">
                <p id="observacion" style="margin-bottom:5px;"><strong style="font-size:14px; margin-left:2%;">Observaciones: </strong></p>                       
            </div>
            <div style="background-color: #f9f9f9; padding-top: 1%; padding-bottom: 1px; margin-bottom:2%;">
                <p id="cont_observacion" style="text-align: left; font-size:12px; margin-left:2%; "><?php echo $auditoria['auditoria']->observaciones;?></p>
            </div>
        
            
            <div class="col-md-12" style="text-align: left;background-color: #E1D1F6;height: 30px;padding-top: 0.7%;">
                <strong style="">Audios auditados - </strong>                                   
                <strong style="">Cantidad de audios auditados: <?php echo count($audios);?></strong>
                
            </div>            
            <div class="col-md-12 contenedor" style="padding:1%;overflow-y: auto; height: 100%; background-color:#f9f9f9;">
                <?php 
                    foreach ($audios  as $audio) {
                ?>
                    <div class="col-md-12" id="<?php echo $audio->id_track;?>" style="">
                        <h5 class="col-md-4">Fecha llamado</h5>
                        <h5 class="col-md-6">
                            <?php 
                                $mifecha= new DateTime($audio->fecha_audio); 
                                $mifecha->modify('-2 hours');
                                echo $mifecha->format('d-m-Y H:i:s');
                            ?>
                        </h5>
                        <audio class="col-md-12" id="audio_<?php echo $audio->id_track; ?>" src="<?php echo $audio->path_audio; ?>" controls="true"></audio>

                    
                    </div>
                    
                <?php 
                        
                    } ?>
        </div>
        
    </div>
    

        <div class="col-md-6" style="margin-top: 2%;">
            <div class="col-md-12" style="text-align: left;background-color: #E1D1F6;height: 30px;padding-top: 0.5%;">
                <strong style="">Detalle de la evaluación</strong>                                 
                
                
            </div>
            <?php 
            // var_dump($auditoria["detalle_auditoria"]);die;
            // $parametros_presentacion = '';
            // $calificacion_presentacion = '';
            // $parametros_negociacion = '';
            // $calificacion_negociacion = '';
            // $parametros_sondeo = '';
            // $calificacion_sondeo = '';
            // $parametros_profesionalismo = '';
            // $calificacion_profesionalismo = '';
            // $parametros_cierre = '';
            // $calificacion_cierre = '';    
            $grupos = [];
            $i = 0;
            foreach ($auditoria['detalle_auditoria'] as $detalle) {
                if (!in_array($detalle["grupo_parametro"],$grupos)) {
                    array_push($grupos, $detalle["grupo_parametro"]);
                }
                
                $grupos[$detalle["grupo_parametro"]][$detalle["parametro_evaluado"]][$i] = $detalle["calificacion"];
                // $grupos[$detalle["grupo_parametro"]][$detalle["parametro_evaluado"]]["calificacion"][$i] = $detalle["calificacion"].'<br>';
                $i++;
            } ?>

            <div>
                <table class="table table-bordered table-responsive" id="table_eval" style="background-color:#f9f9f9;">
                    <thead id='titulo_eval' style="background-color:#E1E5FF;">
                        <tr>
                            <th>Grupo de parametro</th>
                            <th>Parametro evaluado</th>
                            <th>Calificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($grupos as $key => $value) {
                            
                            if (!is_int($key)) { 
                                $parametros_evaluar = "";
                                $calificacion = ""; ?>
                                <tr class="sel_hover">
                                <th style='text-align:center; padding-top: 4%;'><?php echo $key ?></th>
                                <?php  
                                foreach ($value as $k => $val) {
                                    // var_dump($val);
                                    $parametros_evaluar .= $k."<br>";
                                    $calificacion .= $val[$i]."<br>";
                                    $i++; 
                                } ?>
                                <th><?php echo $parametros_evaluar ?></th>
                                <th><?php echo $calificacion ?></th>
                                </tr>   
                                
                            
                        <?php }} ?>
                            <!-- <th><?php echo $calificacion_presentacion ?></th>
                        <tr class="sel_hover">
                            <th style='text-align:center; padding-top: 6%;'>Negociación</th>
                            <th><?php echo $parametros_negociacion ?></th>
                            <th><?php echo $calificacion_negociacion ?></th>
                        </tr>
                        <tr class="sel_hover">
                            <th style='text-align:center; padding-top: 5%;'>Sondeo</th>
                            <th><?php echo $parametros_sondeo ?></th>
                            <th><?php echo $calificacion_sondeo ?></th>
                        </tr>
                        <tr class="sel_hover">
                            <th style='text-align:center; padding-top: 9%;'>Profesionalismo</th>
                            <th><?php echo $parametros_profesionalismo ?></th>
                            <th><?php echo $calificacion_profesionalismo ?></th>
                        </tr>
                        <tr class="sel_hover">
                            <th style='text-align:center; padding-top: 5%;'>Cierre</th>
                            <th><?php echo $parametros_cierre ?></th>
                            <th><?php echo $calificacion_cierre ?></th>
                        </tr> -->
                    </tbody>
                </table>
            </div>
            
            

        </div>
    </div>
</section>
<script src="<?php echo base_url('assets/js/Chart.min.js');?>"></script>
<script src="<?php echo base_url('assets/js/chartjs-plugin-datalabels.min.js');?>"></script>
<script>
    $(document).ready(function(){
        // Solo reproduce un audio a la vez. 
        var audios_tags = document.querySelectorAll('audio');
        var audios = [];
        audios_tags.forEach(element => {
            audios.push(element.id);
        });
        var currentAudio = '';
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
        // FIN - Solo reproduce un audio a la vez.
    });
    </script>