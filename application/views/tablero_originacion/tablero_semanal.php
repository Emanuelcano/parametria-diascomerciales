<style>
    #table_semanal{
		margin-top:-25px;
	}

    .cabeceras{
        font-size:17px;
    }

    .sorting_1{
        width:11%;
    }
</style>

<span class="hidden-xs">
	<?php
        $usuario     = $this->session->userdata("username");
        $tipoUsuario = $this->session->userdata("tipo");
    ?>
</span>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario;?>">
<div class="col-lg-12" id="table_semanal" style="display: block">
            <table data-page-length='25' align="center" id="table_originacion_semanal" class="table table-striped table=hover display cell-border" style="width:110%" width="100%">
                <thead>
                    <tr class="info">
                        <th class="consultor_mensual" style="height: 5px; padding: 0px; vertical-align: middle;" rowspan="3">
                            <br>
                            <h5 align="center"><strong>CONSULTOR</strong></h5>
                        </th>

                        <th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="6">
                            <h5 class="cabeceras" align="center"><small style="color:black;"><strong><?php echo 'Semana del '.$data['fechas'][3]['fechas4']['desde'].' hasta '.$data['fechas'][3]['fechas4']['hasta']?></strong></small></h5>
                        </th>
                        <th style="height: 5px; padding: 0px; background: #6dd9e4;" colspan="6">
                            <h5 class="cabeceras" align="center"><small style="color:black;"><strong><?php echo 'Semana del '.$data['fechas'][2]['fechas3']['desde'].' hasta '.$data['fechas'][2]['fechas3']['hasta']?></strong></small></h5>
                        </th>
                        <th style="height: 5px; padding: 0px; background-color: #bbbaba;" colspan="6">
                            <h5 class="cabeceras" align="center"><small style="color:black;"><strong><?php echo 'Semana del '.$data['fechas'][1]['fechas2']['desde'].' hasta '.$data['fechas'][1]['fechas2']['hasta']?></strong></small></h5>
                        </th>
                        <th style="height: 5px; padding: 0px; background: #ffda22;" colspan="6">
                            <h5 class="cabeceras" align="center"><small style="color:black;"><strong><?php echo 'Semana del '.$data['fechas'][0]['fechas1']['desde'].' hasta '.$data['fechas'][0]['fechas1']['hasta']?></strong></small></h5>
                        </th>
                    </tr>
                    <tr class="info">
                        <!-- SEMANA 1 -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
                        <!-- SEMANA 2 -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
                        <!-- SEMANA 3 -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
                        <!-- SEMANA 4 -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
                    </tr>
                    <tr class="info">
                        <!-- SEMANA 1 -->
                        <th style="width: 4%; padding: 0px; padding-left: 1px;background: #cccaeaf7;text-align: center; font-size:10px;">Apro / Asig</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #cccaeaf7;text-align: center; font-size:10px;">Obj</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #cccaeaf7;text-align: center; font-size:10px;">Rechaz</th>
                        <th style="width: 4%; padding: 0px; padding-left: 1px;background: #F9E79F;text-align: center; font-size:10px;">Apro / Asig</th>
                        <th style="width: 2%; padding: 0px; background: #F9E79F;text-align: center; font-size:10px;">Obj</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #F9E79F;text-align: center; font-size:10px;">Rechaz</th>

                        <!-- SEMANA 2 -->
                        <th style="width: 4%; padding: 0px; padding-left: 1px;background: #cccaeaf7;text-align: center; font-size:10px;">Apro / Asig</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #cccaeaf7;text-align: center; font-size:10px;">Obj</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #cccaeaf7;text-align: center; font-size:10px;">Rechaz</th>
                        <th style="width: 4%; padding: 0px; padding-left: 1px;background: #F9E79F;text-align: center; font-size:10px;">Apro / Asig</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #F9E79F;text-align: center; font-size:10px;">Obj</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #F9E79F;text-align: center; font-size:10px;">Rechaz</th>

                        <!-- SEMANA 3 -->
                        <th style="width: 4%; padding: 0px; padding-left: 1px;background: #cccaeaf7;text-align: center; font-size:10px;">Apro / Asig</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #cccaeaf7;text-align: center; font-size:10px;">Obj</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #cccaeaf7;text-align: center; font-size:10px;">Rechaz</th>
                        <th style="width: 4%; padding: 0px; padding-left: 1px;background: #F9E79F;text-align: center; font-size:10px;">Apro / Asig</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #F9E79F;text-align: center; font-size:10px;">Obj</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #F9E79F;text-align: center; font-size:10px;">Rechaz</th>

                        <!-- SEMANA 4 -->
                        <th style="width: 4%; padding: 0px; padding-left: 1px;background: #cccaeaf7;text-align: center; font-size:10px;">Apro / Asig</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #cccaeaf7;text-align: center; font-size:10px;">Obj</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #cccaeaf7;text-align: center; font-size:10px;">Rechaz</th>
                        <th style="width: 4%; padding: 0px; padding-left: 1px;background: #F9E79F;text-align: center; font-size:10px;">Apro / Asig</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #F9E79F;text-align: center; font-size:10px;">Obj</th>
                        <th style="width: 2%; padding: 0px; padding-left: 3px;background: #F9E79F;text-align: center; font-size:10px;">Rechaz</th>

                    </tr>
                </thead>
                <tbody>
<!-- Ultima Semana -->
				<?php 
                // var_dump($data);die;
                    $object = new stdClass();
                    $total_asignaciones_semana_4 = $total_asignaciones_semana_dependiente_4 =  $total_asignaciones_semana_independiente_4 = 0;
                    $total_aprobados_semana_4 = $total_aprobados_semana_dependiente_4 = $total_rechazado_semana_dependiente_4 = $total_aprobados_semana_independiente_4 = $total_rechazado_semana_independiente_4 = 0;
                    $objetivo_aprobados_semana_4 = 200;
                    $total_reto_asignados_4 = 0;
                    $total_reto_aprobados_4 = 0;
                    $total_operador_4 = $operador_total_4 = $operador_mora_4 = 0;

                    $total_asignaciones_semana_3 = $total_asignaciones_semana_dependiente_3 =  $total_asignaciones_semana_independiente_3 = 0;
                    $total_aprobados_semana_3 = $total_aprobados_semana_dependiente_3 = $total_rechazado_semana_dependiente_3 = $total_aprobados_semana_independiente_3 = $total_rechazado_semana_independiente_3 = 0;
                    $objetivo_aprobados_semana_3 = 200;
                    $total_reto_asignados_3 = 0;
                    $total_reto_aprobados_3 = 0;
                    $total_operador_3 = $operador_total_3 = $operador_mora_3 = 0;

                    $total_asignaciones_semana_2 = $total_asignaciones_semana_dependiente_2 =  $total_asignaciones_semana_independiente_2 = 0;
                    $total_aprobados_semana_2 = $total_aprobados_semana_dependiente_2 = $total_rechazado_semana_dependiente_2 = $total_aprobados_semana_independiente_2 = $total_rechazado_semana_independiente_2 = 0;
                    $objetivo_aprobados_semana_2 = 200;
                    $total_reto_asignados_2 = 0;
                    $total_reto_aprobados_2 = 0;
                    $total_operador_2 = $operador_total_2 = $operador_mora_2 = 0;

                    $total_asignaciones_semana_1 = $total_asignaciones_semana_dependiente_1 =  $total_asignaciones_semana_independiente_1 = 0;
                    $total_aprobados_semana_1 = $total_aprobados_semana_dependiente_1 = $total_rechazado_semana_dependiente_1 = $total_aprobados_semana_independiente_1 = $total_rechazado_semana_independiente_1 = 0;
                    $objetivo_aprobados_semana_1 = 200;
                    $total_reto_asignados_1 = 0;
                    $total_reto_aprobados_1 = 0;
                    $total_operador_1 = $operador_total_1 = $operador_mora_1 = 0;
                    foreach ($data as $key => $value):
                        if(isset($value["asignados"])) 
                        {
                            $total_asignaciones_semana_dependiente_4 += (isset($value["semana_4"][$key]["asignados_dependientes_4"]))? $value["semana_4"][$key]["asignados_dependientes_4"] : 0;
                            $total_asignaciones_semana_independiente_4 += (isset($value["semana_4"][$key]["asignados_independientes_4"]))? $value["semana_4"][$key]["asignados_independientes_4"] : 0;
                            
                            $total_aprobados_semana_dependiente_4    += (isset($value["semana_4"][$key]["aprobados_dependientes_4"]))? $value["semana_4"][$key]["aprobados_dependientes_4"] : 0;
                            $total_rechazado_semana_dependiente_4    += (isset($value["semana_4"][$key]["rechazado_dependientes_4"]))? $value["semana_4"][$key]["rechazado_dependientes_4"] : 0;
                            $total_aprobados_semana_independiente_4    += (isset($value["semana_4"][$key]["aprobados_independientes_4"]))? $value["semana_4"][$key]["aprobados_independientes_4"] : 0;
                            $total_rechazado_semana_independiente_4    += (isset($value["semana_4"][$key]["rechazado_independientes_4"]))? $value["semana_4"][$key]["rechazado_independientes_4"] : 0;
                        
                            $operador_total_4 += 1;         
				?>	
                <tr>                    
                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; height: 30px; padding-left:1%;"> <?= $value["nombre_apellido"] ?></td>     
                    <!-- Dependientes -->
                        <!-- Asig - Apro -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            
                            if (isset($value["semana_4"]) && isset($value["semana_4"][$key]["aprobados_dependientes_4"])) {
                                echo (isset($value["semana_4"][$key]["aprobados_dependientes_4"]) && ($value["semana_4"][$key]["aprobados_dependientes_4"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][3]['fechas4']['desde'].'/'.$data['fechas'][3]['fechas4']['hasta'].'/Dependiente/Aprobados_dependiente">'.$value["semana_4"][$key]["aprobados_dependientes_4"].'</a>': '-'; 
                                echo " / ";
                                echo (isset($value["semana_4"][$key]["asignados_dependientes_4"]) && ($value["semana_4"][$key]["asignados_dependientes_4"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][3]['fechas4']['desde'].'/'.$data['fechas'][3]['fechas4']['hasta'].'/Dependiente/Asignados_dependiente">'.$value["semana_4"][$key]["asignados_dependientes_4"].'</a>': '-' ;
                            }else{
                                echo '- / -';
                            }
                            ?>
                        </td>

                        <!-- Obj -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;"><?php
                            $objetivo_semana_aux_4 =0;
                            if (isset($value["semana_4"])) {                                
                                $asignados = intval($value["semana_4"][$key]["asignados_dependientes_4"]);
                                $aprobados = intval($value["semana_4"][$key]["aprobados_dependientes_4"]);
                                if ($asignados > 0){
                                    $objetivo_semana_aux_4 = ($aprobados / $asignados)*100;
                                    $objetivo_semana_4 = number_format(round($objetivo_semana_aux_4, '0')); //number_format(round(($objetivo_semana_aux * 100 / floatval($data['objetivo']) - 100 ), '0'));
                                }else{
                                    $objetivo_semana_4 = "0";
                                }
                                echo $objetivo_semana_4 . '% ';
                                    $objetivo_semana_aux_dep_4=0;
                                    $objetivo_semana_aux_ind_4 =0;
                                    $asignados_dep_4 = intval($value["semana_4"][$key]["asignados_dependientes_4"]);
                                    $aprobados_dep_4 = intval($value["semana_4"][$key]["aprobados_dependientes_4"]);
                                    $asignados_ind_4 = intval($value["semana_4"][$key]["asignados_independientes_4"]);
                                    $aprobados_ind_4 = intval($value["semana_4"][$key]["aprobados_independientes_4"]);
                                    if($asignados_dep_4 > 0 && $asignados_ind_4 >0){
                                        $objetivo_semana_aux_dep_4 = ($aprobados_dep_4/ $asignados_dep_4)*100;
                                        $objetivo_dependientes_4 = number_format(round($objetivo_semana_aux_dep_4, '0'));
                                        $objetivo_semana_aux_ind_4 = ($aprobados_ind_4 / $asignados_ind_4)*100;
                                        $objetivo_independientes_4 = number_format(round($objetivo_semana_aux_ind_4, '0'));
                                    }else{
                                        $objetivo_dependientes_4 =0;
                                        $objetivo_independientes_4 =0;
                                    }  
                            }else{
                                echo "0% ";
                            }                             
                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_dependientes_4) >= $data['porcentaje']['objetivos_dependientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                            ?>
                        </td>
                                    <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["semana_4"])) {
                                echo (isset($value["semana_4"][$key]["rechazado_dependientes_4"]) && ($value["semana_4"][$key]["rechazado_dependientes_4"] > 0))? $value["semana_4"][$key]["rechazado_dependientes_4"] : '-';
                            }else{
                                echo '-';
                            }
                            ?>
                        </td>     

                            <!-- Independiente -->

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["semana_4"])) {
                                echo (isset($value["semana_4"][$key]["aprobados_independientes_4"]) && ($value["semana_4"][$key]["aprobados_independientes_4"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][3]['fechas4']['desde'].'/'.$data['fechas'][3]['fechas4']['hasta'].'/Independiente/Aprobados_Independiente">'.$value["semana_4"][$key]["aprobados_independientes_4"].'</a>': '-' ;
                                echo " / ";
                                echo (isset($value["semana_4"][$key]["asignados_independientes_4"]) && ($value["semana_4"][$key]["asignados_independientes_4"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][3]['fechas4']['desde'].'/'.$data['fechas'][3]['fechas4']['hasta'].'/Independiente//Asignados_Independiente">'.$value["semana_4"][$key]["asignados_independientes_4"].'</a>': '-' ;
                            }else{
                                echo '- / -';
                            }
                            ?>
                        </td>
                        
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php
                                if (isset($value["semana_4"])) {     
                                $asignados = intval($value["semana_4"][$key]["asignados_independientes_4"]);
                                $aprobados = intval($value["semana_4"][$key]["aprobados_independientes_4"]);
                                    if ($asignados > 0){
                                        // $objetivo_semana_aux = ($aprobados / $asignados)*100;
                                        $objetivo_semana_4 = number_format(round((($aprobados / $asignados)*100), '0')); 
                                    }else 
                                        $objetivo_semana_4 = "0";
                                    
                                    echo $objetivo_semana_4 . '% ';
                                    $objetivo_semana_aux_dep_4=0;
                                    $objetivo_semana_aux_ind_4 =0;
                                    $asignados_dep_4 = intval($value["semana_4"][$key]["asignados_dependientes_4"]);
                                    $aprobados_dep_4 = intval($value["semana_4"][$key]["aprobados_dependientes_4"]);
                                    $asignados_ind_4 = intval($value["semana_4"][$key]["asignados_independientes_4"]);
                                    $aprobados_ind_4 = intval($value["semana_4"][$key]["aprobados_independientes_4"]);
                                    if($asignados_dep_4 > 0 && $asignados_ind_4 >0){
                                        $objetivo_semana_aux_dep_4 = ($aprobados_dep_4/ $asignados_dep_4)*100;
                                        $objetivo_dependientes_4 = number_format(round($objetivo_semana_aux_dep_4, '0'));
                                        $objetivo_semana_aux_ind_4 = ($aprobados_ind_4 / $asignados_ind_4)*100;
                                        $objetivo_independientes_4 = number_format(round($objetivo_semana_aux_ind_4, '0'));
                                    }else{
                                        $objetivo_dependientes_4 =0;
                                        $objetivo_independientes_4 =0;
                                    }                               
                                }else{
                                    echo '0% ';
                                }
                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_independientes_4) >= $data['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                            ?>
                        </td>
                                <!-- Rechazados -->
                                <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["semana_4"][$key]["rechazado_independientes_4"]) && ($value["semana_4"][$key]["rechazado_independientes_4"] > 0))? $value["semana_4"][$key]["rechazado_independientes_4"] : '-';
                            ?>
                        </td>
<!-- Fin ultima semana -->
<!-- Semana 3 -->               
                <?php

                    $total_asignaciones_semana_dependiente_3 += (isset($value["semana_3"][$key]["asignados_dependientes_3"]))? $value["semana_3"][$key]["asignados_dependientes_3"] : 0;
                    $total_asignaciones_semana_independiente_3 += (isset($value["semana_3"][$key]["asignados_independientes_3"]))? $value["semana_3"][$key]["asignados_independientes_3"] : 0;
                    
                    $total_aprobados_semana_dependiente_3 += (isset($value["semana_3"][$key]["aprobados_dependientes_3"]))? $value["semana_3"][$key]["aprobados_dependientes_3"] : 0;
                    $total_rechazado_semana_dependiente_3    += (isset($value["semana_3"][$key]["rechazado_dependientes_3"]))? $value["semana_3"][$key]["rechazado_dependientes_3"] : 0;
                    $total_aprobados_semana_independiente_3    += (isset($value["semana_3"][$key]["aprobados_independientes_3"]))? $value["semana_3"][$key]["aprobados_independientes_3"] : 0;
                    $total_rechazado_semana_independiente_3    += (isset($value["semana_3"][$key]["rechazado_independientes_3"]))? $value["semana_3"][$key]["rechazado_independientes_3"] : 0;
                
                    $operador_total_3 += 1;
                ?>

                    <!-- Dependientes -->
                        <!-- Asig - Apro -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            
                            if (isset($value["semana_3"]) && isset($value["semana_3"][$key]["aprobados_dependientes_3"])) {
                                echo (isset($value["semana_3"][$key]["aprobados_dependientes_3"]) && ($value["semana_3"][$key]["aprobados_dependientes_3"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][2]['fechas3']['desde'].'/'.$data['fechas'][2]['fechas3']['hasta'].'/Dependiente/Aprobados_dependiente">'.$value["semana_3"][$key]["aprobados_dependientes_3"].'</a>': '-'; 
                                echo " / ";
                                echo (isset($value["semana_3"][$key]["asignados_dependientes_3"]) && ($value["semana_3"][$key]["asignados_dependientes_3"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][2]['fechas3']['desde'].'/'.$data['fechas'][2]['fechas3']['hasta'].'/Dependiente/Asignados_dependiente">'.$value["semana_3"][$key]["asignados_dependientes_3"].'</a>': '-' ;
                            }else{
                                echo '- / -';
                            }
                            ?>
                        </td>

                        <!-- Obj -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;"><?php
                            $objetivo_semana_aux_3 =0;
                            if (isset($value["semana_3"])) {                                
                                $asignados = intval($value["semana_3"][$key]["asignados_dependientes_3"]);
                                $aprobados = intval($value["semana_3"][$key]["aprobados_dependientes_3"]);
                                if ($asignados > 0){
                                    $objetivo_semana_aux_3 = ($aprobados / $asignados)*100;
                                    $objetivo_semana_3 = number_format(round($objetivo_semana_aux_3, '0')); //number_format(round(($objetivo_semana_aux * 100 / floatval($data['objetivo']) - 100 ), '0'));
                                }else{
                                    $objetivo_semana_3 = "0";
                                }
                                echo $objetivo_semana_3 . '% ';
                                    $objetivo_semana_aux_dep_3=0;
                                    $objetivo_semana_aux_ind_3 =0;
                                    $asignados_dep_3 = intval($value["semana_3"][$key]["asignados_dependientes_3"]);
                                    $aprobados_dep_3 = intval($value["semana_3"][$key]["aprobados_dependientes_3"]);
                                    $asignados_ind_3 = intval($value["semana_3"][$key]["asignados_independientes_3"]);
                                    $aprobados_ind_3 = intval($value["semana_3"][$key]["aprobados_independientes_3"]);
                                    if($asignados_dep_3 > 0 && $asignados_ind_3 >0){
                                        $objetivo_semana_aux_dep_3 = ($aprobados_dep_3/ $asignados_dep_3)*100;
                                        $objetivo_dependientes_3 = number_format(round($objetivo_semana_aux_dep_3, '0'));
                                        $objetivo_semana_aux_ind_3 = ($aprobados_ind_3 / $asignados_ind_3)*100;
                                        $objetivo_independientes_3 = number_format(round($objetivo_semana_aux_ind_3, '0'));
                                    }else{
                                        $objetivo_dependientes_3 =0;
                                        $objetivo_independientes_3 =0;
                                    }  
                            }else{
                                echo "0% ";
                            }                             
                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_dependientes_3) >= $data['porcentaje']['objetivos_dependientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                            ?>
                        </td>
                                    <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["semana_3"])) {
                                echo (isset($value["semana_3"][$key]["rechazado_dependientes_3"]) && ($value["semana_3"][$key]["rechazado_dependientes_3"] > 0))? $value["semana_3"][$key]["rechazado_dependientes_3"] : '-';
                            }else{
                                echo '-';
                            }
                            ?>
                        </td>     

                            <!-- Independiente -->

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["semana_3"])) {
                                echo (isset($value["semana_3"][$key]["aprobados_independientes_3"]) && $value["semana_3"][$key]["aprobados_independientes_3"] > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][2]['fechas3']['desde'].'/'.$data['fechas'][2]['fechas3']['hasta'].'/Independiente/Aprobados_Independiente">'.$value["semana_3"][$key]["aprobados_independientes_3"].'</a>': '-' ;
                                echo " / ";
                                echo (isset($value["semana_3"][$key]["asignados_independientes_3"]) && ($value["semana_3"][$key]["asignados_independientes_3"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][2]['fechas3']['desde'].'/'.$data['fechas'][2]['fechas3']['hasta'].'/Independiente/Asignados_Independiente">'.$value["semana_3"][$key]["asignados_independientes_3"].'</a>': '-' ;
                            }else{
                                echo '- / -';
                            }
                            ?>
                        </td>
                        
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php
                                if (isset($value["semana_3"])) {     
                                $asignados = intval($value["semana_3"][$key]["asignados_independientes_3"]);
                                $aprobados = intval($value["semana_3"][$key]["aprobados_independientes_3"]);
                                    if ($asignados > 0){
                                        // $objetivo_semana_aux = ($aprobados / $asignados)*100;
                                        $objetivo_semana_3 = number_format(round((($aprobados / $asignados)*100), '0')); 
                                    }else 
                                        $objetivo_semana_3 = "0";
                                    
                                    echo $objetivo_semana_3 . '% ';
                                    $objetivo_semana_aux_dep_3=0;
                                    $objetivo_semana_aux_ind_3 =0;
                                    $asignados_dep_3 = intval($value["semana_3"][$key]["asignados_dependientes_3"]);
                                    $aprobados_dep_3 = intval($value["semana_3"][$key]["aprobados_dependientes_3"]);
                                    $asignados_ind_3 = intval($value["semana_3"][$key]["asignados_independientes_3"]);
                                    $aprobados_ind_3 = intval($value["semana_3"][$key]["aprobados_independientes_3"]);
                                    if($asignados_dep_3 > 0 && $asignados_ind_3 >0){
                                        $objetivo_semana_aux_dep_3 = ($aprobados_dep_3/ $asignados_dep_3)*100;
                                        $objetivo_dependientes_3 = number_format(round($objetivo_semana_aux_dep_3, '0'));
                                        $objetivo_semana_aux_ind_3 = ($aprobados_ind_3 / $asignados_ind_3)*100;
                                        $objetivo_independientes_3 = number_format(round($objetivo_semana_aux_ind_3, '0'));
                                    }else{
                                        $objetivo_dependientes_3 =0;
                                        $objetivo_independientes_3 =0;
                                    }                               
                                }else{
                                    echo '0% ';
                                }
                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_independientes_3) >= $data['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                            ?>
                        </td>
                                <!-- Rechazados -->
                                <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["semana_3"][$key]["rechazado_independientes_3"]) && ($value["semana_3"][$key]["rechazado_independientes_3"] > 0))? $value["semana_3"][$key]["rechazado_independientes_3"] : '-';
                            ?>
                        </td>
<!-- Fin Semana 3 -->
<!-- Semana 2 -->
                <?php
                    $total_asignaciones_semana_dependiente_2 += (isset($value["semana_2"][$key]["asignados_dependientes_2"]))? $value["semana_2"][$key]["asignados_dependientes_2"] : 0;
                    $total_asignaciones_semana_independiente_2 += (isset($value["semana_2"][$key]["asignados_independientes_2"]))? $value["semana_2"][$key]["asignados_independientes_2"] : 0;
                    
                    $total_aprobados_semana_dependiente_2    += (isset($value["semana_2"][$key]["aprobados_dependientes_2"]))? $value["semana_2"][$key]["aprobados_dependientes_2"] : 0;
                    $total_rechazado_semana_dependiente_2    += (isset($value["semana_2"][$key]["rechazado_dependientes_2"]))? $value["semana_2"][$key]["rechazado_dependientes_2"] : 0;
                    $total_aprobados_semana_independiente_2    += (isset($value["semana_2"][$key]["aprobados_independientes_2"]))? $value["semana_2"][$key]["aprobados_independientes_2"] : 0;
                    $total_rechazado_semana_independiente_2    += (isset($value["semana_2"][$key]["rechazado_independientes_2"]))? $value["semana_2"][$key]["rechazado_independientes_2"] : 0;
                
                    $operador_total_2 += 1;         
                ?>
                    <!-- Dependientes -->
                        <!-- Asig - Apro -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            
                            if (isset($value["semana_2"]) && isset($value["semana_2"][$key]["aprobados_dependientes_2"])) {
                                echo (isset($value["semana_2"][$key]["aprobados_dependientes_2"]) && ($value["semana_2"][$key]["aprobados_dependientes_2"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][1]['fechas2']['desde'].'/'.$data['fechas'][1]['fechas2']['hasta'].'/Dependiente/Aprobados_dependiente">'.$value["semana_2"][$key]["aprobados_dependientes_2"].'</a>': '-'; 
                                echo " / ";
                                echo (isset($value["semana_2"][$key]["asignados_dependientes_2"]) && ($value["semana_2"][$key]["asignados_dependientes_2"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][1]['fechas2']['desde'].'/'.$data['fechas'][1]['fechas2']['hasta'].'/Dependiente/Asignados_dependiente">'.$value["semana_2"][$key]["asignados_dependientes_2"].'</a>': '-' ;
                            }else{
                                echo '- / -';
                            }
                            ?>
                        </td>

                        <!-- Obj -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;"><?php
                            $objetivo_semana_aux_2 =0;
                            if (isset($value["semana_2"])) {                                
                                $asignados = intval($value["semana_2"][$key]["asignados_dependientes_2"]);
                                $aprobados = intval($value["semana_2"][$key]["aprobados_dependientes_2"]);
                                if ($asignados > 0){
                                    $objetivo_semana_aux_2 = ($aprobados / $asignados)*100;
                                    $objetivo_semana_2 = number_format(round($objetivo_semana_aux_2, '0')); //number_format(round(($objetivo_semana_aux * 100 / floatval($data['objetivo']) - 100 ), '0'));
                                }else{
                                    $objetivo_semana_2 = "0";
                                }
                                echo $objetivo_semana_2 . '% ';
                                    $objetivo_semana_aux_dep_2=0;
                                    $objetivo_semana_aux_ind_2 =0;
                                    $asignados_dep_2 = intval($value["semana_2"][$key]["asignados_dependientes_2"]);
                                    $aprobados_dep_2 = intval($value["semana_2"][$key]["aprobados_dependientes_2"]);
                                    $asignados_ind_2 = intval($value["semana_2"][$key]["asignados_independientes_2"]);
                                    $aprobados_ind_2 = intval($value["semana_2"][$key]["aprobados_independientes_2"]);
                                    if($asignados_dep_2 > 0 && $asignados_ind_2 >0){
                                        $objetivo_semana_aux_dep_2 = ($aprobados_dep_2/ $asignados_dep_2)*100;
                                        $objetivo_dependientes_2 = number_format(round($objetivo_semana_aux_dep_2, '0'));
                                        $objetivo_semana_aux_ind_2 = ($aprobados_ind_2 / $asignados_ind_2)*100;
                                        $objetivo_independientes_2 = number_format(round($objetivo_semana_aux_ind_2, '0'));
                                    }else{
                                        $objetivo_dependientes_2 =0;
                                        $objetivo_independientes_2 =0;
                                    }  
                            }else{
                                echo "0% ";
                            }                             
                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_dependientes_2) >= $data['porcentaje']['objetivos_dependientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                            ?>
                        </td>
                                    <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["semana_2"])) {
                                echo (isset($value["semana_2"][$key]["rechazado_dependientes_2"]) && ($value["semana_2"][$key]["rechazado_dependientes_2"] > 0))? $value["semana_2"][$key]["rechazado_dependientes_2"] : '-';
                            }else{
                                echo '-';
                            }
                            ?>
                        </td>     

                            <!-- Independiente -->

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["semana_2"])) {
                                echo (isset($value["semana_2"][$key]["aprobados_independientes_2"]) && ($value["semana_2"][$key]["aprobados_independientes_2"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][1]['fechas2']['desde'].'/'.$data['fechas'][1]['fechas2']['hasta'].'/Independiente/Aprobados_Independiente">'.$value["semana_2"][$key]["aprobados_independientes_2"].'</a>': '-' ;
                                echo " / ";
                                echo (isset($value["semana_2"][$key]["asignados_independientes_2"]) && ($value["semana_2"][$key]["asignados_independientes_2"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][1]['fechas2']['desde'].'/'.$data['fechas'][1]['fechas2']['hasta'].'/Independiente/Asignados_Independiente">'.$value["semana_2"][$key]["asignados_independientes_2"].'</a>': '-' ;
                            }else{
                                echo '- / -';
                            }
                            ?>
                        </td>
                        
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php
                                if (isset($value["semana_2"])) {     
                                $asignados = intval($value["semana_2"][$key]["asignados_independientes_2"]);
                                $aprobados = intval($value["semana_2"][$key]["aprobados_independientes_2"]);
                                    if ($asignados > 0){
                                        // $objetivo_semana_aux = ($aprobados / $asignados)*100;
                                        $objetivo_semana_2 = number_format(round((($aprobados / $asignados)*100), '0')); 
                                    }else 
                                        $objetivo_semana_2 = "0";
                                    
                                    echo $objetivo_semana_2 . '% ';
                                    $objetivo_semana_aux_dep_2=0;
                                    $objetivo_semana_aux_ind_2 =0;
                                    $asignados_dep_2 = intval($value["semana_2"][$key]["asignados_dependientes_2"]);
                                    $aprobados_dep_2 = intval($value["semana_2"][$key]["aprobados_dependientes_2"]);
                                    $asignados_ind_2 = intval($value["semana_2"][$key]["asignados_independientes_2"]);
                                    $aprobados_ind_2 = intval($value["semana_2"][$key]["aprobados_independientes_2"]);
                                    if($asignados_dep_2 > 0 && $asignados_ind_2 >0){
                                        $objetivo_semana_aux_dep_2 = ($aprobados_dep_2/ $asignados_dep_2)*100;
                                        $objetivo_dependientes_2 = number_format(round($objetivo_semana_aux_dep_2, '0'));
                                        $objetivo_semana_aux_ind_2 = ($aprobados_ind_2 / $asignados_ind_2)*100;
                                        $objetivo_independientes_2 = number_format(round($objetivo_semana_aux_ind_2, '0'));
                                    }else{
                                        $objetivo_dependientes_2 =0;
                                        $objetivo_independientes_2 =0;
                                    }                               
                                }else{
                                    echo '0% ';
                                }
                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_independientes_2) >= $data['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                            ?>
                        </td>
                                <!-- Rechazados -->
                                <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["semana_2"][$key]["rechazado_independientes_2"]) && ($value["semana_2"][$key]["rechazado_independientes_2"] > 0))? $value["semana_2"][$key]["rechazado_independientes_2"] : '-';
                            ?>
                        </td>
<!-- Fin Semana 2 -->
<!-- Semana Actual -->
                <?php
                    $total_asignaciones_semana_dependiente_1 += (isset($value["semana_1"][$key]["asignados_dependientes_1"]))? $value["semana_1"][$key]["asignados_dependientes_1"] : 0;
                    $total_asignaciones_semana_independiente_1 += (isset($value["semana_1"][$key]["asignados_independientes_1"]))? $value["semana_1"][$key]["asignados_independientes_1"] : 0;
                    
                    $total_aprobados_semana_dependiente_1    += (isset($value["semana_1"][$key]["aprobados_dependientes_1"]))? $value["semana_1"][$key]["aprobados_dependientes_1"] : 0;
                    $total_rechazado_semana_dependiente_1    += (isset($value["semana_1"][$key]["rechazado_dependientes_1"]))? $value["semana_1"][$key]["rechazado_dependientes_1"] : 0;
                    $total_aprobados_semana_independiente_1    += (isset($value["semana_1"][$key]["aprobados_independientes_1"]))? $value["semana_1"][$key]["aprobados_independientes_1"] : 0;
                    $total_rechazado_semana_independiente_1    += (isset($value["semana_1"][$key]["rechazado_independientes_1"]))? $value["semana_1"][$key]["rechazado_independientes_1"] : 0;
                
                    $operador_total_1+= 1;         
                ?>
                    <!-- Dependientes -->
                        <!-- Asig - Apro -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            
                            if (isset($value["semana_1"]) && isset($value["semana_1"][$key]["aprobados_dependientes_1"])) {
                                echo (isset($value["semana_1"][$key]["aprobados_dependientes_1"]) && ($value["semana_1"][$key]["aprobados_dependientes_1"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Dependiente/Aprobados_dependiente">'.$value["semana_1"][$key]["aprobados_dependientes_1"].'</a>': '-'; 
                                echo " / ";
                                echo (isset($value["semana_1"][$key]["asignados_dependientes_1"]) && ($value["semana_1"][$key]["asignados_dependientes_1"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Dependiente/Asignados_dependiente">'.$value["semana_1"][$key]["asignados_dependientes_1"].'</a>': '-' ;
                            }else{
                                echo '- / -';
                            }
                            ?>
                        </td>

                        <!-- Obj -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;"><?php
                            $objetivo_semana_aux_1 =0;
                            if (isset($value["semana_1"])) {                                
                                $asignados = intval($value["semana_1"][$key]["asignados_dependientes_1"]);
                                $aprobados = intval($value["semana_1"][$key]["aprobados_dependientes_1"]);
                                if ($asignados > 0){
                                    $objetivo_semana_aux_1 = ($aprobados / $asignados)*100;
                                    $objetivo_semana_1 = number_format(round($objetivo_semana_aux_1, '0')); //number_format(round(($objetivo_semana_aux * 100 / floatval($data['objetivo']) - 100 ), '0'));
                                }else{
                                    $objetivo_semana_1 = "0";
                                }
                                echo $objetivo_semana_1 . '% ';
                                    $objetivo_semana_aux_dep_1=0;
                                    $objetivo_semana_aux_ind_1 =0;
                                    $asignados_dep_1 = intval($value["semana_1"][$key]["asignados_dependientes_1"]);
                                    $aprobados_dep_1 = intval($value["semana_1"][$key]["aprobados_dependientes_1"]);
                                    $asignados_ind_1 = intval($value["semana_1"][$key]["asignados_independientes_1"]);
                                    $aprobados_ind_1 = intval($value["semana_1"][$key]["aprobados_independientes_1"]);
                                    if($asignados_dep_1 > 0 && $asignados_ind_1 >0){
                                        $objetivo_semana_aux_dep_1 = ($aprobados_dep_1/ $asignados_dep_1)*100;
                                        $objetivo_dependientes_1 = number_format(round($objetivo_semana_aux_dep_1, '0'));
                                        $objetivo_semana_aux_ind_1 = ($aprobados_ind_1 / $asignados_ind_1)*100;
                                        $objetivo_independientes_1 = number_format(round($objetivo_semana_aux_ind_1, '0'));
                                    }else{
                                        $objetivo_dependientes_1 =0;
                                        $objetivo_independientes_1 =0;
                                    }  
                            }else{
                                echo "0% ";
                            }                             
                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_dependientes_1) >= $data['porcentaje']['objetivos_dependientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                            ?>
                        </td>
                                    <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["semana_1"])) {
                                echo (isset($value["semana_1"][$key]["rechazado_dependientes_1"]) && ($value["semana_1"][$key]["rechazado_dependientes_1"] > 0))? $value["semana_1"][$key]["rechazado_dependientes_1"] : '-';
                            }else{
                                echo '-';
                            }
                            ?>
                        </td>     

                            <!-- Independiente -->

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["semana_1"])) {
                                echo (isset($value["semana_1"][$key]["aprobados_independientes_1"]) && ($value["semana_1"][$key]["aprobados_independientes_1"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Independiente/Aprobados_Independiente">'.$value["semana_1"][$key]["aprobados_independientes_1"].'</a>': '-' ;
                                echo " / ";
                                echo (isset($value["semana_1"][$key]["asignados_independientes_1"]) && ($value["semana_1"][$key]["asignados_independientes_1"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Independiente/Asignados_Independiente">'.$value["semana_1"][$key]["asignados_independientes_1"].'</a>': '-' ;
                            }else{
                                echo '- / -';
                            }
                            ?>
                        </td>
                        
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php
                                if (isset($value["semana_1"])) {     
                                $asignados = intval($value["semana_1"][$key]["asignados_independientes_1"]);
                                $aprobados = intval($value["semana_1"][$key]["aprobados_independientes_1"]);
                                    if ($asignados > 0){
                                        // $objetivo_semana_aux = ($aprobados / $asignados)*100;
                                        $objetivo_semana_1 = number_format(round((($aprobados / $asignados)*100), '0')); 
                                    }else 
                                        $objetivo_semana_1 = "0";
                                    
                                    echo $objetivo_semana_1 . '% ';
                                    $objetivo_semana_aux_dep_1=0;
                                    $objetivo_semana_aux_ind_1 =0;
                                    $asignados_dep_1 = intval($value["semana_1"][$key]["asignados_dependientes_1"]);
                                    $aprobados_dep_1 = intval($value["semana_1"][$key]["aprobados_dependientes_1"]);
                                    $asignados_ind_1 = intval($value["semana_1"][$key]["asignados_independientes_1"]);
                                    $aprobados_ind_1 = intval($value["semana_1"][$key]["aprobados_independientes_1"]);
                                    if($asignados_dep_1 > 0 && $asignados_ind_1 >0){
                                        $objetivo_semana_aux_dep_1 = ($aprobados_dep_1/ $asignados_dep_1)*100;
                                        $objetivo_dependientes_1 = number_format(round($objetivo_semana_aux_dep_1, '0'));
                                        $objetivo_semana_aux_ind_1 = ($aprobados_ind_1 / $asignados_ind_1)*100;
                                        $objetivo_independientes_1 = number_format(round($objetivo_semana_aux_ind_1, '0'));
                                    }else{
                                        $objetivo_dependientes_1 =0;
                                        $objetivo_independientes_1 =0;
                                    }                               
                                }else{
                                    echo '0% ';
                                }
                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_independientes_1) >= $data['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                            ?>
                        </td>
                                <!-- Rechazados -->
                                <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["semana_1"][$key]["rechazado_independientes_1"]) && ($value["semana_1"][$key]["rechazado_independientes_1"] > 0))? $value["semana_1"][$key]["rechazado_independientes_1"] : '-';
                            ?>
                        </td>
<!-- Fin Semana Actual -->
                </tr>
                

                <?php } endforeach; ?>
				</tbody>
				<tfoot style="font-size: 12px;font-weight: 600;">
                <tr class="info"> 
                            <td style="padding: 0px; font-size: 14px;text-align: center; vertical-align: middle;"> TOTAL</td>
<!-- Total Semana 4 -->
                            <!-- Hoy Dependiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_semana_dependiente_4 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][3]['fechas4']['desde'].'/'.$data['fechas'][3]['fechas4']['hasta'].'/Dependiente/Total_aprobados_Dependientes">'.$total_aprobados_semana_dependiente_4.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_semana_dependiente_4 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][3]['fechas4']['desde'].'/'.$data['fechas'][3]['fechas4']['hasta'].'/Dependiente/Total_asignados_Dependientes">'.$total_asignaciones_semana_dependiente_4.'</a>': '0'; ?>
                            </td>
                            <td style="width: 3%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_semana_tot_dep_4 = 0;
                                    if($total_aprobados_semana_dependiente_4 > 0 && $total_asignaciones_semana_dependiente_4 >0){
                                        $porc_semana_aux_dep_4 = ($total_aprobados_semana_dependiente_4/ $total_asignaciones_semana_dependiente_4)*100;
                                        $porc_semana_tot_dep_4 = number_format(round($porc_semana_aux_dep_4, '0'));
                                        echo $porc_semana_tot_dep_4 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_semana_tot_dep_4) >= $data['porcentaje']['objetivos_dependientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.22rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 2%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_semana_dependiente_4 > 0)? $total_rechazado_semana_dependiente_4 : '0'; ?>
                            </td> 
                            <!-- Hoy Independiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_semana_independiente_4 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][3]['fechas4']['desde'].'/'.$data['fechas'][3]['fechas4']['hasta'].'/Independiente/Total_aprobados_Independientes">'.$total_aprobados_semana_independiente_4.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_semana_independiente_4 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][3]['fechas4']['desde'].'/'.$data['fechas'][3]['fechas4']['hasta'].'/Independiente/Total_asignados_Independientes">'.$total_asignaciones_semana_independiente_4.'</a>': '0'; ?>
                            </td>
                            <td style="width: 3%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_semana_tot_ind_4 = 0;
                                    if($total_aprobados_semana_independiente_4 > 0 && $total_asignaciones_semana_independiente_4 >0){
                                        $porc_semana_aux_ind_4 = ($total_aprobados_semana_independiente_4/ $total_asignaciones_semana_independiente_4)*100;
                                        $porc_semana_tot_ind_4 = number_format(round($porc_semana_aux_ind_4, '0'));
                                        echo $porc_semana_tot_ind_4 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_semana_tot_ind_4) >= $data['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 2%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_semana_independiente_4 > 0)? $total_rechazado_semana_independiente_4 : '0'; ?>
                            </td> 

<!-- Semana 3 -->
                            <!-- Hoy Dependiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_semana_dependiente_3 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][2]['fechas3']['desde'].'/'.$data['fechas'][2]['fechas3']['hasta'].'/Dependiente/Total_aprobados_Dependientes">'.$total_aprobados_semana_dependiente_3.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_semana_dependiente_3 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][2]['fechas3']['desde'].'/'.$data['fechas'][2]['fechas3']['hasta'].'/Dependiente/Total_asignados_Dependientes">'.$total_asignaciones_semana_dependiente_3.'</a>': '0'; ?>
                            </td>
                            <td style="width: 3%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_semana_tot_dep_3 = 0;
                                    if($total_aprobados_semana_dependiente_3 > 0 && $total_asignaciones_semana_dependiente_3 >0){
                                        $porc_semana_aux_dep_3 = ($total_aprobados_semana_dependiente_3/ $total_asignaciones_semana_dependiente_3)*100;
                                        $porc_semana_tot_dep_3 = number_format(round($porc_semana_aux_dep_3, '0'));
                                        echo $porc_semana_tot_dep_3 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_semana_tot_dep_3) >= $data['porcentaje']['objetivos_dependientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 2%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_semana_dependiente_3 > 0)? $total_rechazado_semana_dependiente_3 : '0'; ?>
                            </td> 
                            <!-- Hoy Independiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_semana_independiente_3 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][2]['fechas3']['desde'].'/'.$data['fechas'][2]['fechas3']['hasta'].'/Independiente/Total_aprobados_Independientes">'.$total_aprobados_semana_independiente_3.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_semana_independiente_3 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][2]['fechas3']['desde'].'/'.$data['fechas'][2]['fechas3']['hasta'].'/Independiente/Total_asignados_Independientes">'.$total_asignaciones_semana_independiente_3.'</a>': '0'; ?>
                            </td>
                            <td style="width: 3%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_semana_tot_ind_3 = 0;
                                    if($total_aprobados_semana_independiente_3 > 0 && $total_asignaciones_semana_independiente_3 >0){
                                        $porc_semana_aux_ind_3 = ($total_aprobados_semana_independiente_3/ $total_asignaciones_semana_independiente_3)*100;
                                        $porc_semana_tot_ind_3 = number_format(round($porc_semana_aux_ind_3, '0'));
                                        echo $porc_semana_tot_ind_3 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_semana_tot_ind_3) >= $data['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 2%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_semana_independiente_3 > 0)? $total_rechazado_semana_independiente_3 : '0'; ?>
                            </td> 

<!-- Total Semana 2-->
                            <!-- Hoy Dependiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_semana_dependiente_2 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][1]['fechas2']['desde'].'/'.$data['fechas'][1]['fechas2']['hasta'].'/Dependiente/Total_aprobados_Dependientes">'.$total_aprobados_semana_dependiente_2.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_semana_dependiente_2 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][1]['fechas2']['desde'].'/'.$data['fechas'][1]['fechas2']['hasta'].'/Dependiente/Total_asignados_Dependientes">'.$total_asignaciones_semana_dependiente_2.'</a>': '0'; ?>
                            </td>
                            <td style="width: 3%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_semana_tot_dep_2 = 0;
                                    if($total_aprobados_semana_dependiente_2 > 0 && $total_asignaciones_semana_dependiente_2 >0){
                                        $porc_semana_aux_dep_2 = ($total_aprobados_semana_dependiente_2/ $total_asignaciones_semana_dependiente_2)*100;
                                        $porc_semana_tot_dep_2 = number_format(round($porc_semana_aux_dep_2, '0'));
                                        echo $porc_semana_tot_dep_2 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_semana_tot_dep_2) >= $data['porcentaje']['objetivos_dependientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 2%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_semana_dependiente_2 > 0)? $total_rechazado_semana_dependiente_2 : '0'; ?>
                            </td> 
                            <!-- Hoy Independiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_semana_independiente_2 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][1]['fechas2']['desde'].'/'.$data['fechas'][1]['fechas2']['hasta'].'/Independiente/Total_aprobados_Independientes">'.$total_aprobados_semana_independiente_2.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_semana_independiente_2 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][1]['fechas2']['desde'].'/'.$data['fechas'][1]['fechas2']['hasta'].'/Independiente/Total_asignados_Independientes">'.$total_asignaciones_semana_independiente_2.'</a>': '0'; ?>
                            </td>
                            <td style="width: 3%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_semana_tot_ind_2 = 0;
                                    if($total_aprobados_semana_independiente_2 > 0 && $total_asignaciones_semana_independiente_2 >0){
                                        $porc_semana_aux_ind_2 = ($total_aprobados_semana_independiente_2/ $total_asignaciones_semana_independiente_2)*100;
                                        $porc_semana_tot_ind_2 = number_format(round($porc_semana_aux_ind_2, '0'));
                                        echo $porc_semana_tot_ind_2 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_semana_tot_ind_2) >= $data['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 2%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_semana_independiente_2 > 0)? $total_rechazado_semana_independiente_2 : '0'; ?>
                            </td> 
<!-- Total Semana Actual -->
                            <!-- Hoy Dependiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_semana_dependiente_1 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Dependiente/Total_aprobados_Dependientes">'.$total_aprobados_semana_dependiente_1.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_semana_dependiente_1 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Dependiente/Total_asignados_Dependientes">'.$total_asignaciones_semana_dependiente_1.'</a>': '0'; ?>
                            </td>
                            <td style="width: 3%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_semana_tot_dep_1 = 0;
                                    if($total_aprobados_semana_dependiente_1 > 0 && $total_asignaciones_semana_dependiente_1 >0){
                                        $porc_semana_aux_dep_1 = ($total_aprobados_semana_dependiente_1/ $total_asignaciones_semana_dependiente_1)*100;
                                        $porc_semana_tot_dep_1 = number_format(round($porc_semana_aux_dep_1, '0'));
                                        echo $porc_semana_tot_dep_1 . "% "; 

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_semana_tot_dep_1) >= $data['porcentaje']['objetivos_dependientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 2%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_semana_dependiente_1 > 0)? $total_rechazado_semana_dependiente_1 : '0'; ?>
                            </td> 
                            <!-- Hoy Independiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_semana_independiente_1 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Independiente/Total_aprobados_Independientes">'.$total_aprobados_semana_independiente_1.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_semana_independiente_1 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Independiente/Total_asignados_Independientes">'.$total_asignaciones_semana_independiente_1.'</a>': '0'; ?>
                            </td>
                            <td style="width: 3%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_semana_tot_ind_1 = 0;
                                    if($total_aprobados_semana_independiente_1 > 0 && $total_asignaciones_semana_independiente_1 >0){
                                        $porc_semana_aux_ind_1 = ($total_aprobados_semana_independiente_1/ $total_asignaciones_semana_independiente_1)*100;
                                        $porc_semana_tot_ind_1 = number_format(round($porc_semana_aux_ind_1, '0'));
                                        echo $porc_semana_tot_ind_1 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_semana_tot_ind_1) >= $data['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 1.2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 1.2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 2%; padding: 0px; background: #C4D4E7; text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_semana_independiente_1 > 0)? $total_rechazado_semana_independiente_1 : '0'; ?>
                            </td> 
                    </tr>
                </tfoot>
			</table>
                
            </div>

<script>
	$(document).ready(function() {
		$('#table_originacion_semanal').DataTable();
        $('.consultor_mensual').css({"width":"11%"});
	})
</script>