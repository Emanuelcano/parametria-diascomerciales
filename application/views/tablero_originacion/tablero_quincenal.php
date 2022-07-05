<style>
	#table_quincenal{
		margin-top:-25px;
	}

    .sorting_1{
        width:11%;
    }

    .cabeceras{
        font-size:17px;
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

            <div class="col-lg-12" id="table_quincenal" style="display: block">
            <table data-page-length='25' align="center" id="table_originacion_quincenal" class="table table-striped table=hover display cell-border" style="width:100%" width="100%">
                <thead>
                    <tr class="info">
                        <th class="consultor_quincenal" style="height: 5px; padding: 0px; width: 13%!important; vertical-align: middle;" rowspan="3">
                            <br>
                            <h5 align="center"><strong>CONSULTOR</strong></h5>
                        </th>

                        <th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="6">
                            <h5 class="cabeceras" align="center"><small style="color:black;"><strong><?php echo 'Quincena del '.$data['fechas'][0]['fechas1']["desde"].' hasta '.$data['fechas'][0]['fechas1']["hasta"]?></strong></small></h5>
                        </th>
                        <th style="height: 5px; padding: 0px; background: #6dd9e4;" colspan="6">
                            <h5 class="cabeceras" align="center"><small style="color:black;"><strong><?php echo 'Quincena del '.$data['fechas'][1]['fechas2']["desde"].' hasta '.$data['fechas'][1]['fechas2']["hasta"]?></strong></small></h5>
                        </th>
                    </tr>
                    <tr class="info">
                        <!-- QUINCENA 1 -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
                        <!-- QUINCENA 2 -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
                    </tr>
                    <tr class="info">
                        <!-- QUINCENA 1 -->
                        <th style="width: 5%; padding: 0px; padding-left: 5px;background: #cccaeaf7;text-align: center;"><small>Apro / Asig</small></th>
                        <th style="width: 3%; padding: 0px; padding-left: 5px;background: #cccaeaf7;text-align: center;"><small>Obj</small></th>
                        <th style="width: 3%; padding: 0px; padding-left: 5px;background: #cccaeaf7;text-align: center;"><small>Rechaz</small></th>
                        <th style="width: 5%; padding: 0px; padding-left: 5px;background: #F9E79F;text-align: center;"><small>Apro / Asig</small></th>
                        <th style="width: 3%; padding: 0px; padding-left: 5px;background: #F9E79F;text-align: center;"><small>Obj</small></th>
                        <th style="width: 3%; padding: 0px; padding-left: 5px;background: #F9E79F;text-align: center;"><small>Rechaz</small></th>

                        <!-- QUINCENA 2 -->
                        <th style="width: 5%; padding: 0px; padding-left: 5px;background: #cccaeaf7;text-align: center;"><small>Apro / Asig</small></th>
                        <th style="width: 3%; padding: 0px; padding-left: 5px;background: #cccaeaf7;text-align: center;"><small>Obj</small></th>
                        <th style="width: 3%; padding: 0px; padding-left: 5px;background: #cccaeaf7;text-align: center;"><small>Rechaz</small></th>
                        <th style="width: 5%; padding: 0px; padding-left: 5px;background: #F9E79F;text-align: center;"><small>Apro / Asig</small></th>
                        <th style="width: 3%; padding: 0px; padding-left: 5px;background: #F9E79F;text-align: center;"><small>Obj</small></th>
                        <th style="width: 3%; padding: 0px; padding-left: 5px;background: #F9E79F;text-align: center;"><small>Rechaz</small></th>


                    </tr>
                </thead>
                <tbody>
				<?php                     
                    $object1 = new stdClass();
                    $total_asignaciones_quincena_1 = $total_asignaciones_quincena_dependiente_1 =  $total_asignaciones_quincena_independiente_1 = 0;
                    $total_aprobados_quincena_1 = $total_aprobados_quincena_dependiente_1 = $total_rechazado_quincena_dependiente_1 = $total_aprobados_quincena_independiente_1 = $total_rechazado_quincena_independiente_1 = 0;
                    
                    $objetivo_aprobados_quincena_1 = 200;
                    $total_reto_asignados_1 = 0;
                    $total_reto_aprobados_1 = 0;
                    $total_operador_1 = $operador_total_1 = $operador_mora_1 = 0;

                    $total_asignaciones_quincena_2 = $total_asignaciones_quincena_dependiente_2 =  $total_asignaciones_quincena_independiente_2 = 0;
                    $total_aprobados_quincena_2 = $total_aprobados_quincena_dependiente_2 = $total_rechazado_quincena_dependiente_2 = $total_aprobados_quincena_independiente_2 = $total_rechazado_quincena_independiente_2 = 0;
                    
                    $objetivo_aprobados_quincena_2 = 200;
                    $total_reto_asignados_2 = 0;
                    $total_reto_aprobados_2 = 0;
                    $total_operador_2 = $operador_total_2 = $operador_mora_2 = 0;
                        foreach ($data as $key => $value):
                        // var_dump($value);
                        if(isset($value["asignados"])) 
                        {
                            $total_asignaciones_quincena_dependiente_1 += (isset($value["quincena_1"][$key]["asignados_dependientes_1"]))? $value["quincena_1"][$key]["asignados_dependientes_1"] : 0;
                            $total_asignaciones_quincena_independiente_1 += (isset($value["quincena_1"][$key]["asignados_independientes_1"]))? $value["quincena_1"][$key]["asignados_independientes_1"] : 0;
                            
                            $total_aprobados_quincena_dependiente_1    += (isset($value["quincena_1"][$key]["aprobados_dependientes_1"]))? $value["quincena_1"][$key]["aprobados_dependientes_1"] : 0;
                            $total_rechazado_quincena_dependiente_1    += (isset($value["quincena_1"][$key]["rechazado_dependientes_1"]))? $value["quincena_1"][$key]["rechazado_dependientes_1"] : 0;
                            $total_aprobados_quincena_independiente_1    += (isset($value["quincena_1"][$key]["aprobados_independientes_1"]))? $value["quincena_1"][$key]["aprobados_independientes_1"] : 0;
                            $total_rechazado_quincena_independiente_1    += (isset($value["quincena_1"][$key]["rechazado_independientes_1"]))? $value["quincena_1"][$key]["rechazado_independientes_1"] : 0;
                        
                            $operador_total_1 += 1;
				?>	
                <tr>
                    <td style="padding: 0px; font-size: 16px; vertical-align: middle; height: 35px; padding-left:1%;"> <?= $data[$key]["nombre_apellido"] ?></td>
                    <!-- Dependientes -->
                                <!-- Asig - Apro -->
                                <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                        <?php  
                            echo (isset($value["quincena_1"][$key]["aprobados_dependientes_1"]) && ($value["quincena_1"][$key]["aprobados_dependientes_1"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][0]['fechas1']["desde"].'/'.$data['fechas'][0]['fechas1']["hasta"].'/Dependiente/Aprobados_dependiente">'.$value["quincena_1"][$key]["aprobados_dependientes_1"].'</a>': '-'; 
                            echo " / ";
                            echo (isset($value["quincena_1"][$key]["asignados_dependientes_1"]) && ($value["quincena_1"][$key]["asignados_dependientes_1"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][0]['fechas1']["desde"].'/'.$data['fechas'][0]['fechas1']["hasta"].'/Dependiente/Asignados_dependiente">'.$value["quincena_1"][$key]["asignados_dependientes_1"].'</a>': '-' ;
                        ?>
                        </td>
                                <!-- Obj -->
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;"><?php
                            $objetivo_quincena_aux =0;
                            $asignados = intval($value["quincena_1"][$key]["asignados_dependientes_1"]);
                            $aprobados = intval($value["quincena_1"][$key]["aprobados_dependientes_1"]);
                            if ($asignados > 0){
                                $objetivo_quincena_aux = ($aprobados / $asignados)*100;
                                $objetivo_quincena = number_format(round($objetivo_quincena_aux, '0')); //number_format(round(($objetivo_quincena_aux * 100 / floatval($data['objetivo']) - 100 ), '0'));
                            }else{
                                $objetivo_quincena = "0";
                            }
                            echo $objetivo_quincena . '% ';
                                $objetivo_quincena_aux_dep=0;
                                $objetivo_quincena_aux_ind =0;
                                $asignados_dep = intval($value["quincena_1"][$key]["asignados_dependientes_1"]);
                                $aprobados_dep = intval($value["quincena_1"][$key]["aprobados_dependientes_1"]);
                                $asignados_ind = intval($value["quincena_1"][$key]["asignados_independientes_1"]);
                                $aprobados_ind = intval($value["quincena_1"][$key]["aprobados_independientes_1"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                    $objetivo_quincena_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                    $objetivo_dependientes = number_format(round($objetivo_quincena_aux_dep, '0'));
                                    $objetivo_quincena_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                    $objetivo_independientes = number_format(round($objetivo_quincena_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }                               

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_dependientes) >= $data['porcentaje']['objetivos_dependientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>

                                <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["quincena_1"][$key]["rechazado_dependientes_1"]) && ($value["quincena_1"][$key]["rechazado_dependientes_1"] > 0))? $value["quincena_1"][$key]["rechazado_dependientes_1"] : '-';
                            ?>
                        </td>

                            <!-- Independiente -->

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["quincena_1"][$key]["aprobados_independientes_1"]) && ($value["quincena_1"][$key]["aprobados_independientes_1"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][0]['fechas1']["desde"].'/'.$data['fechas'][0]['fechas1']["hasta"].'/Independiente/Aprobados_Independiente">'.$value["quincena_1"][$key]["aprobados_independientes_1"].'</a>': '-' ;
                                echo " / ";
                                echo (isset($value["quincena_1"][$key]["asignados_independientes_1"]) && ($value["quincena_1"][$key]["asignados_independientes_1"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][0]['fechas1']["desde"].'/'.$data['fechas'][0]['fechas1']["hasta"].'/Independiente/Asignados_Independiente">'.$value["quincena_1"][$key]["asignados_independientes_1"].'</a>': '-' ;
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;">
                            <?php
                                // $objetivo_quincena_aux =0;
                                $asignados = intval($value["quincena_1"][$key]["asignados_independientes_1"]);
                                $aprobados = intval($value["quincena_1"][$key]["aprobados_independientes_1"]);

                                if ($asignados > 0){
                                    // $objetivo_quincena_aux = ($aprobados / $asignados)*100;
                                    $objetivo_quincena = number_format(round((($aprobados / $asignados)*100), '0')); 
                                }else 
                                    $objetivo_quincena = "0";
                                
                                echo $objetivo_quincena . '% ';
                                $objetivo_quincena_aux_dep=0;
                                $objetivo_quincena_aux_ind =0;
                                $asignados_dep = intval($value["quincena_1"][$key]["asignados_dependientes_1"]);
                                $aprobados_dep = intval($value["quincena_1"][$key]["aprobados_dependientes_1"]);
                                $asignados_ind = intval($value["quincena_1"][$key]["asignados_independientes_1"]);
                                $aprobados_ind = intval($value["quincena_1"][$key]["aprobados_independientes_1"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                    $objetivo_quincena_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                    $objetivo_dependientes = number_format(round($objetivo_quincena_aux_dep, '0'));
                                    $objetivo_quincena_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                    $objetivo_independientes = number_format(round($objetivo_quincena_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }                               

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_independientes) >= $data['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                                <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["quincena_1"][$key]["rechazado_independientes_1"]) && ($value["quincena_1"][$key]["rechazado_independientes_1"] > 0))? $value["quincena_1"][$key]["rechazado_independientes_1"] : '-';
                            ?>
                        </td>
<!-- Quincena 2 -->
                        <?php
                            $total_asignaciones_quincena_dependiente_2 += (isset($value["quincena_2"][$key]["asignados_dependientes_2"]))? $value["quincena_2"][$key]["asignados_dependientes_2"] : 0;
                            $total_asignaciones_quincena_independiente_2 += (isset($value["quincena_2"][$key]["asignados_independientes_2"]))? $value["quincena_2"][$key]["asignados_independientes_2"] : 0;
                            
                            $total_aprobados_quincena_dependiente_2    += (isset($value["quincena_2"][$key]["aprobados_dependientes_2"]))? $value["quincena_2"][$key]["aprobados_dependientes_2"] : 0;
                            $total_rechazado_quincena_dependiente_2    += (isset($value["quincena_2"][$key]["rechazado_dependientes_2"]))? $value["quincena_2"][$key]["rechazado_dependientes_2"] : 0;
                            $total_aprobados_quincena_independiente_2    += (isset($value["quincena_2"][$key]["aprobados_independientes_2"]))? $value["quincena_2"][$key]["aprobados_independientes_2"] : 0;
                            $total_rechazado_quincena_independiente_2    += (isset($value["quincena_2"][$key]["rechazado_independientes_2"]))? $value["quincena_2"][$key]["rechazado_independientes_2"] : 0;
                            $operador_total_2 += 1;

                        ?>
                    <!-- Asig - Apro -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                        <?php  
                            echo (isset($value["quincena_2"][$key]["aprobados_dependientes_2"]) && ($value["quincena_2"][$key]["aprobados_dependientes_2"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][1]['fechas2']["desde"].'/'.$data['fechas'][1]['fechas2']["hasta"].'/Dependiente/Aprobados_dependiente">'.$value["quincena_2"][$key]["aprobados_dependientes_2"].'</a>': '-'; 
                            echo " / ";
                            echo (isset($value["quincena_2"][$key]["asignados_dependientes_2"]) && ($value["quincena_2"][$key]["asignados_dependientes_2"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][1]['fechas2']["desde"].'/'.$data['fechas'][1]['fechas2']["hasta"].'/Dependiente/Asignados_dependiente">'.$value["quincena_2"][$key]["asignados_dependientes_2"].'</a>': '-' ;
                        ?>
                        </td>
                                <!-- Obj -->
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;"><?php
                            $objetivo_quincena_aux =0;
                            if (isset($value["quincena_2"])) {    
                            $asignados = intval($value["quincena_2"][$key]["asignados_dependientes_2"]);
                            $aprobados = intval($value["quincena_2"][$key]["aprobados_dependientes_2"]);
                            if ($asignados > 0){
                                $objetivo_quincena_aux = ($aprobados / $asignados)*100;
                                $objetivo_quincena = number_format(round($objetivo_quincena_aux, '0')); //number_format(round(($objetivo_quincena_aux * 100 / floatval($data['objetivo']) - 100 ), '0'));
                            }else{
                                $objetivo_quincena = "0";
                            }
                            echo $objetivo_quincena . '% ';
                                $objetivo_quincena_aux_dep=0;
                                $objetivo_quincena_aux_ind =0;
                                $asignados_dep = intval($value["quincena_2"][$key]["asignados_dependientes_2"]);
                                $aprobados_dep = intval($value["quincena_2"][$key]["aprobados_dependientes_2"]);
                                $asignados_ind = intval($value["quincena_2"][$key]["asignados_independientes_2"]);
                                $aprobados_ind = intval($value["quincena_2"][$key]["aprobados_independientes_2"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                    $objetivo_quincena_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                    $objetivo_dependientes = number_format(round($objetivo_quincena_aux_dep, '0'));
                                    $objetivo_quincena_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                    $objetivo_independientes = number_format(round($objetivo_quincena_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }
                            }else {
                                echo "0%";
                            }                        

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_dependientes) >= $data['porcentaje']['objetivos_dependientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>

                                <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["quincena_2"])) {
                                echo (isset($value["quincena_2"][$key]["rechazado_dependientes_2"]) && ($value["quincena_2"][$key]["rechazado_dependientes_2"] > 0))? $value["quincena_2"][$key]["rechazado_dependientes_2"] : '-';
                            }else{
                                echo "-";
                            }
                            ?>
                        </td>

                            <!-- Independiente -->

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php
                            if (isset($value["quincena_2"])) {
                                echo (isset($value["quincena_2"][$key]["aprobados_independientes_2"]) && ($value["quincena_2"][$key]["aprobados_independientes_2"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionApro_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][1]['fechas2']["desde"].'/'.$data['fechas'][1]['fechas2']["hasta"].'/Independiente/Aprobados_Independiente">'.$value["quincena_2"][$key]["aprobados_independientes_2"].'</a>': '-' ;
                                echo " / ";
                                echo (isset($value["quincena_2"][$key]["asignados_independientes_2"]) && ($value["quincena_2"][$key]["asignados_independientes_2"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacionAsig_entre_csv/'.$value["idoperador"].'/'.$data['fechas'][1]['fechas2']["desde"].'/'.$data['fechas'][1]['fechas2']["hasta"].'/Independiente/Asignados_Independiente">'.$value["quincena_2"][$key]["asignados_independientes_2"].'</a>': '-' ;
                            }else{
                                echo "- / -";
                            }?>
                        </td>
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;">
                            <?php
                            if (isset($value["quincena_2"])) {
                                // $objetivo_quincena_aux =0;
                                $asignados = intval($value["quincena_2"][$key]["asignados_independientes_2"]);
                                $aprobados = intval($value["quincena_2"][$key]["aprobados_independientes_2"]);
                                
                                if ($asignados > 0){
                                    // $objetivo_quincena_aux = ($aprobados / $asignados)*100;
                                    $objetivo_quincena = number_format(round((($aprobados / $asignados)*100), '0')); 
                                }else 
                                    $objetivo_quincena = "0";
                                
                                echo $objetivo_quincena . '% ';
                                $objetivo_quincena_aux_dep=0;
                                $objetivo_quincena_aux_ind =0;
                                $asignados_dep = intval($value["quincena_2"][$key]["asignados_dependientes_2"]);
                                $aprobados_dep = intval($value["quincena_2"][$key]["aprobados_dependientes_2"]);
                                $asignados_ind = intval($value["quincena_2"][$key]["asignados_independientes_2"]);
                                $aprobados_ind = intval($value["quincena_2"][$key]["aprobados_independientes_2"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                    $objetivo_quincena_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                    $objetivo_dependientes = number_format(round($objetivo_quincena_aux_dep, '0'));
                                    $objetivo_quincena_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                    $objetivo_independientes = number_format(round($objetivo_quincena_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }                          
                            }else{
                                echo "0%";
                            }     

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_independientes) >= $data['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                                <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                            if (isset($value["quincena_2"])) {
                                echo (isset($value["quincena_2"][$key]["rechazado_independientes_2"]) && ($value["quincena_2"][$key]["rechazado_independientes_2"] > 0))? $value["quincena_2"][$key]["rechazado_independientes_2"] : '-';
                            }else {
                                echo "-";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php } endforeach ?>
				</tbody>
				<tfoot style="font-size: 15px;font-weight: 600;">
                <tr class="info"> 
                            <td style="padding: 0px; font-size: 16px; vertical-align: middle;"> TOTAL</td>
                            <!-- Quincena 2 Dependiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_quincena_dependiente_2 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Dependiente/Total_aprobados_Dependientes">'.$total_aprobados_quincena_dependiente_1.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_quincena_dependiente_2 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Dependiente/Total_asignados_Dependientes">'.$total_asignaciones_quincena_dependiente_1.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_quincena_tot_dep_2 = 0;
                                    if($total_aprobados_quincena_dependiente_2 > 0 && $total_asignaciones_quincena_dependiente_2 >0){
                                        $porc_quincena_aux_dep_2 = ($total_aprobados_quincena_dependiente_2/ $total_asignaciones_quincena_dependiente_2)*100;
                                        $porc_quincena_tot_dep_2 = number_format(round($porc_quincena_aux_dep_2, '0'));
                                        echo $porc_quincena_tot_dep_2 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_quincena_tot_dep_2) >= $data['porcentaje']['objetivos_dependientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_quincena_dependiente_2 > 0)? $total_rechazado_quincena_dependiente_2 : '0'; ?>
                            </td> 
                            <!-- Hoy Independiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_quincena_independiente_2 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Independiente/Total_aprobados_Independientes">'.$total_aprobados_quincena_independiente_1.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_quincena_independiente_2 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][0]['fechas1']['desde'].'/'.$data['fechas'][0]['fechas1']['hasta'].'/Independiente/Total_asignados_Independientes">'.$total_asignaciones_quincena_independiente_1.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_quincena_tot_ind_2 = 0;
                                    if($total_aprobados_quincena_independiente_2 > 0 && $total_asignaciones_quincena_independiente_2 >0){
                                        $porc_quincena_aux_ind_2 = ($total_aprobados_quincena_independiente_2/ $total_asignaciones_quincena_independiente_2)*100;
                                        $porc_quincena_tot_ind_2 = number_format(round($porc_quincena_aux_ind_2, '0'));
                                        echo $porc_quincena_tot_ind_2 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_quincena_tot_ind_2) >= $data['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_quincena_independiente_2 > 0)? $total_rechazado_quincena_independiente_2 : '0'; ?>
                            </td> 

                             <!-- Quincena 1 Dependiente-->
                             <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_quincena_dependiente_1 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][1]['fechas2']["desde"].'/'.$data['fechas'][1]['fechas2']["hasta"].'/Dependiente/Total_aprobados_Dependientes">'.$total_aprobados_quincena_dependiente_2.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_quincena_dependiente_1 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][1]['fechas2']["desde"].'/'.$data['fechas'][1]['fechas2']["hasta"].'/Dependiente/Total_asignados_Dependientes">'.$total_asignaciones_quincena_dependiente_2.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_quincena_tot_dep_1 = 0;
                                    if($total_aprobados_quincena_dependiente_1 > 0 && $total_asignaciones_quincena_dependiente_1 >0){
                                        $porc_quincena_aux_dep_1 = ($total_aprobados_quincena_dependiente_1/ $total_asignaciones_quincena_dependiente_1)*100;
                                        $porc_quincena_tot_dep_1 = number_format(round($porc_quincena_aux_dep_1, '0'));
                                        echo $porc_quincena_tot_dep_1 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_quincena_tot_dep_1) >= $data['porcentaje']['objetivos_dependientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_quincena_dependiente_1 > 0)? $total_rechazado_quincena_dependiente_1 : '0'; ?>
                            </td> 
                            <!-- Hoy Independiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_quincena_independiente_1 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.$data['fechas'][1]['fechas2']["desde"].'/'.$data['fechas'][1]['fechas2']["hasta"].'/Independiente/Total_aprobados_Independientes">'.$total_aprobados_quincena_independiente_2.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_quincena_independiente_1 > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.$data['fechas'][1]['fechas2']["desde"].'/'.$data['fechas'][1]['fechas2']["hasta"].'/Independiente/Total_asignados_Independientes">'.$total_asignaciones_quincena_independiente_2.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_quincena_tot_ind_1 = 0;
                                    if($total_aprobados_quincena_independiente_1 > 0 && $total_asignaciones_quincena_independiente_1 >0){
                                        $porc_quincena_aux_ind_1 = ($total_aprobados_quincena_independiente_1/ $total_asignaciones_quincena_independiente_1)*100;
                                        $porc_quincena_tot_ind_1 = number_format(round($porc_quincena_aux_ind_1, '0'));
                                        echo $porc_quincena_tot_ind_1 . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_quincena_tot_ind_1) >= $data['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_quincena_independiente_1 > 0)? $total_rechazado_quincena_independiente_1 : '0'; ?>
                            </td> 

                    </tr>   
                </tfoot>
			</table>
                
            </div>

<script>
	$(document).ready(function() {
		$('#table_originacion_quincenal').DataTable();
        $('.consultor_quincenal').css({"width":"11%"});
	})
</script>