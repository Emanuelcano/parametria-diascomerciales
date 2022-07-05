<style>
	#table_hoy{
		margin-top:-25px;
	}

    .sorting_1{
        width:15%;
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

            <div class="col-lg-12" id="table_hoy" style="display: block">
                <table data-page-length='25' align="center" id="table_originacion_hoy" class="table table-striped table=hover display cell-border" style="width:100%" width="100%">
                <thead>
                    <tr class="info">
                        <th class="consultor_hoy" style="height: 5px; padding: 0px; width: 13%!important; vertical-align: middle;" rowspan="3">
                            <br>
                            <h5 align="center"><strong>CONSULTOR</strong></h5>
                        </th>

                        <th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="6">
                            <h5 align="center"><small style="color:black;"><strong>HOY</strong></small></h5>
                        </th>
                    </tr>
                    <tr class="info">
                        <!-- Hoy -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
                    </tr>
                    <tr class="info">
                        <!-- Hoy -->
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Apro / Asig</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Obj</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Rechaz</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Apro / Asig</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Obj</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Rechaz</small></th>

                    </tr>
                </thead>
                <tbody>
				<?php 
                $object = new stdClass();
                $total_asignaciones_dia = $total_asignaciones_hoy_dependiente =  $total_asignaciones_hoy_independiente = 0;
                $total_aprobados_dia = $total_aprobados_hoy_dependiente = $total_rechazado_hoy_dependiente = $total_aprobados_hoy_independiente = $total_rechazado_hoy_independiente = 0;
                $objetivo_aprobados_dia = 200;
                $total_reto_asignados = 0;
                $total_reto_aprobados = 0;
				$total_operador = $operador_total = $operador_mora = 0;
                if (isset($data["indicadores"])) {
                    $valor = $data["indicadores"];
                }else{
                    $valor = $data;
                }
				foreach ($valor as $key => $value):
					if(isset($value["asignados"]) && $value["asignados"] > 0) 
					{
                        
                        $total_asignaciones_hoy_dependiente += (isset($value["hoy"]["asignados_dependientes"]))? $value["hoy"]["asignados_dependientes"] : 0;
						$total_asignaciones_hoy_independiente += (isset($value["hoy"]["asignados_independientes"]))? $value["hoy"]["asignados_independientes"] : 0;
						
						$total_aprobados_hoy_dependiente    += (isset($value["hoy"]["aprobados_dependientes"]))? $value["hoy"]["aprobados_dependientes"] : 0;
						$total_rechazado_hoy_dependiente    += (isset($value["hoy"]["rechazado_dependientes"]))? $value["hoy"]["rechazado_dependientes"] : 0;
						$total_aprobados_hoy_independiente    += (isset($value["hoy"]["aprobados_independientes"]))? $value["hoy"]["aprobados_independientes"] : 0;
						$total_rechazado_hoy_independiente    += (isset($value["hoy"]["rechazado_independientes"]))? $value["hoy"]["rechazado_independientes"] : 0;
						
						$operador_total += 1;
                        
                        
                        // var_dump($value["asignados"]);die;
                ?>	
					<tr>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; height: 35px; padding-left:1%;"> <?= $value["nombre_apellido"] ?></td>

                            <!-- Dependientes -->
                                <!-- Asig - Apro -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                        <?php  
                            echo (isset($value["hoy"]["aprobados_dependientes"]) && ($value["hoy"]["aprobados_dependientes"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_hoy_apro_csv/'.$value["idoperador"].'/Dependiente/Aprobados_Dependiente">'.$value["hoy"]["aprobados_dependientes"].'</a>': '-'; 
                            echo " / ";
                            echo (isset($value["hoy"]["asignados_dependientes"]) && ($value["hoy"]["asignados_dependientes"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_hoy_asig_csv/'.$value["idoperador"].'/Dependiente/Asignados_Dependiente">'.$value["hoy"]["asignados_dependientes"].'</a>': '-' ;
                        ?>
                        </td>
                                <!-- Obj -->
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;"><?php
                            $objetivo_hoy_aux =0;
                            $asignados = intval($value["hoy"]["asignados_dependientes"]);
                            $aprobados = intval($value["hoy"]["aprobados_dependientes"]);
                            if ($asignados > 0){
                                $objetivo_hoy_aux = ($aprobados / $asignados)*100;
                                $objetivo_hoy = number_format(round($objetivo_hoy_aux, '0')); //number_format(round(($objetivo_hoy_aux * 100 / floatval($data['objetivo']) _ 100 ), '0'));
                            }else{
                                $objetivo_hoy = "0";
                            }
                            echo $objetivo_hoy . '% ';
                                $objetivo_hoy_aux_dep=0;
                                $objetivo_hoy_aux_ind =0;
                                $asignados_dep = intval($value["hoy"]["asignados_dependientes"]);
                                $aprobados_dep = intval($value["hoy"]["aprobados_dependientes"]);
                                $asignados_ind = intval($value["hoy"]["asignados_independientes"]);
                                $aprobados_ind = intval($value["hoy"]["aprobados_independientes"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                    $objetivo_hoy_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                    $objetivo_dependientes = number_format(round($objetivo_hoy_aux_dep, '0'));
                                    $objetivo_hoy_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                    $objetivo_independientes = number_format(round($objetivo_hoy_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }                               

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_dependientes) >= $valor['porcentaje']['objetivos_dependientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>

                                <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["hoy"]["rechazado_dependientes"]) && ($value["hoy"]["rechazado_dependientes"] > 0))? $value["hoy"]["rechazado_dependientes"] : '-';
                            ?>
                        </td>

                            <!-- Independiente -->

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["hoy"]["aprobados_independientes"]) && ($value["hoy"]["aprobados_independientes"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_hoy_apro_csv/'.$value["idoperador"].'/Independiente/Aprobados_Independiente">'.$value["hoy"]["aprobados_independientes"].'</a>': '-' ;
                                echo " / ";
                                echo (isset($value["hoy"]["asignados_independientes"]) && ($value["hoy"]["asignados_independientes"] > 0))? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_hoy_asig_csv/'.$value["idoperador"].'/Independiente/Asignados_Independiente">'.$value["hoy"]["asignados_independientes"].'</a>': '-' ;
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;">
                            <?php
                                // $objetivo_hoy_aux =0;
                                $asignados = intval($value["hoy"]["asignados_independientes"]);
                                $aprobados = intval($value["hoy"]["aprobados_independientes"]);

                                if ($asignados > 0){
                                    // $objetivo_hoy_aux = ($aprobados / $asignados)*100;
                                    $objetivo_hoy = number_format(round((($aprobados / $asignados)*100), '0')); 
                                }else 
                                    $objetivo_hoy = "0";
                                
                                echo $objetivo_hoy . '% ';
                                $objetivo_hoy_aux_dep=0;
                                $objetivo_hoy_aux_ind =0;
                                $asignados_dep = intval($value["hoy"]["asignados_dependientes"]);
                                $aprobados_dep = intval($value["hoy"]["aprobados_dependientes"]);
                                $asignados_ind = intval($value["hoy"]["asignados_independientes"]);
                                $aprobados_ind = intval($value["hoy"]["aprobados_independientes"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                    $objetivo_hoy_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                    $objetivo_dependientes = number_format(round($objetivo_hoy_aux_dep, '0'));
                                    $objetivo_hoy_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                    $objetivo_independientes = number_format(round($objetivo_hoy_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }                               

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_independientes) >= $valor['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                                <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["hoy"]["rechazado_independientes"]) && ($value["hoy"]["rechazado_independientes"] > 0))? $value["hoy"]["rechazado_independientes"] : '-';
                            ?>
                        </td>
                    </tr>
                    <?php } endforeach ?>
				</tbody>
				<tfoot style="font-size: 15px;font-weight: 600;">
                    <tr class="info"> 
                            <td style="padding: 0px; font-size: 16px; vertical-align: middle;"> TOTAL</td>
                            <!-- Hoy Dependiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_hoy_dependiente > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.date('Y-m-d').'/'.date('Y-m-d').'/Dependiente/Total_aprobados_dependientes">'.$total_aprobados_hoy_dependiente.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_hoy_dependiente > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.date('Y-m-d').'/'.date('Y-m-d').'/Dependiente/Total_asignados_dependientes">'.$total_asignaciones_hoy_dependiente.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_hoy_tot_dep = 0;
                                    if($total_aprobados_hoy_dependiente > 0 && $total_asignaciones_hoy_dependiente >0){
                                        $porc_hoy_aux_dep = ($total_aprobados_hoy_dependiente/ $total_asignaciones_hoy_dependiente)*100;
                                        $porc_hoy_tot_dep = number_format(round($porc_hoy_aux_dep, '0'));
                                        echo $porc_hoy_tot_dep . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_hoy_tot_dep) >= $valor['porcentaje']['objetivos_dependientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_hoy_dependiente > 0)? $total_rechazado_hoy_dependiente : '0'; ?>
                            </td> 
                            <!-- Hoy Independiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_hoy_independiente > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_aprob_csv/'.date('Y-m-d').'/'.date('Y-m-d').'/Independiente/Total_aprobados_independientes">'.$total_aprobados_hoy_independiente.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_hoy_independiente > 0)? '<a href="'.base_url().'gestion/TableroOriginacion/originacion_total_asig_csv/'.date('Y-m-d').'/'.date('Y-m-d').'/Independiente/Total_asignados_independientes">'.$total_asignaciones_hoy_independiente.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_hoy_tot_ind = 0;
                                    if($total_aprobados_hoy_independiente > 0 && $total_asignaciones_hoy_independiente >0){
                                        $porc_hoy_aux_ind = ($total_aprobados_hoy_independiente/ $total_asignaciones_hoy_independiente)*100;
                                        $porc_hoy_tot_ind = number_format(round($porc_hoy_aux_ind, '0'));
                                        echo $porc_hoy_tot_ind . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_hoy_tot_ind) >= $valor['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_hoy_independiente > 0)? $total_rechazado_hoy_independiente : '0'; ?>
                            </td> 
			</table>
                
            </div>

<script>
	$(document).ready(function() {
		$('#table_originacion_hoy').DataTable();
        $('.consultor_hoy').css({"width":"15%"});
	})
</script>