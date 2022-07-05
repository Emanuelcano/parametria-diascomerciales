<span class="hidden-xs">
	<?php
        $usuario     = $this->session->userdata("username");
        $tipoUsuario = $this->session->userdata("tipo");
    ?>
</span>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario;?>">

<div id="dashboard_principal" style="display: block;  margin-top:1rem;">

    <div class="row">
    	<!-- Main content -->
      	<section class="content">

            <div class="col-lg-12" id="cuerpoGastos" style="display: block">

              <table data-page-length='25' align="center" id="tp_Indicadores" class="table table-striped table=hover display" width="100%">
                <thead>
                	<tr class="info">
		              	<th style="height: 5px; padding: 0px; width: 15%" rowspan="3">
		                	<h5 align="center"><small><strong>VENDEDOR</strong></small></h5>
		              	</th>

                        <th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="8">
		                	<h5 align="center"><small><strong>HOY</strong></small></h5>
		              	</th>
                        <th style="height: 5px; padding: 0px;" colspan="2">
		                	<h5 align="center"><small><strong>WHATSAPP</strong></small></h5>
		              	</th>
                        <!-- <th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="2">
		                	<h5 align="center"><small><strong>Llamadas</strong></small></h5>
		              	</th> -->

		              	
		              	<th style="height: 5px; padding: 0px;background: #C4D4E7;" colspan="7">
                            <h5 align="center"><small><strong>
                            <?php 
                                $hoy=date('d-m-Y');
                                $ayer = date('d-m-Y', strtotime('-1 day', strtotime($hoy)));
                                $dia = date("D",strtotime($ayer));
                        
                                if ($dia == 'Sun'){
                                    echo 'ÚLTIMO VIERNES';
                                } else {
                                    echo 'AYER';
                                }
                            ?></strong></small></h5>
		              	</th>
		              	<th style="height: 5px; padding: 0px;" colspan="7">
		                	<h5 align="center"><small><strong>ESTE MES</strong></small></h5>
		              	</th>
		            </tr>
                    <tr class="info">
                        
	                    <!-- Hoy -->
	                    <th class="text-center" style="padding: 0px; padding-left: 10px;background: #C4D4E7;border-collapse: collapse;" colspan="2"><small>Dependiente</small></th>
	                    <th class="text-center" style="padding: 0px; padding-left: 10px;background: #C4D4E7;border-collapse: collapse;" colspan="2"><small>Independiente</small></th>
	                    <th class="text-center" style="padding: 0px; padding-left: 10px;background: #C4D4E7;border-collapse: collapse;" colspan="4"><small>Retanqueo</small></th>

                        <!-- whatsapp subtitulo -->
                        <th class="text-center" style="padding: 0px; padding-left: 10px;border-collapse: collapse;" colspan="2"><small>Hoy</small></th>


                        <!-- llamadas -->
                        <!-- <th class="text-center" style="padding: 0px; padding-left: 10px;background: #C4D4E7;border-collapse: collapse;" colspan="2"><small>Hoy</small></th> -->

                        <!-- ayer -->
                        <th class="text-center" style="padding: 0px; padding-left: 10px;background: #C4D4E7;border-collapse: collapse;text-align: center;" colspan="3"><small>Primario</small></th>
	                    <th class="text-center" style="padding: 0px; padding-left: 10px;background: #C4D4E7;border-collapse: collapse;text-align: center;" colspan="4"><small>Retanqueo</small></th>
                        
                        <!-- mes -->
	                    <th class="text-center" style="padding: 0px; padding-left: 10px;border-collapse: collapse;text-align: center;" colspan="3"><small>Primario</small></th>
	                    <th class="text-center" style="padding: 0px; padding-left: 10px;border-collapse: collapse;text-align: center;" colspan="4"><small>Retanqueo</small></th>

                  	</tr>
                  	<tr class="info">
	                    <!-- Hoy -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Asig</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Apro</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Asig</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Apro</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Obj</small></th>
	                    <th style="width: 2%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Ret Asig</small></th>
	                    <th style="width: 2%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Ret Apro</small></th>
	                    <th style="width: 2%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"></th>
                        <!-- whatsapp subtitulo -->
                        <th style="width: 4%; padding: 0px; padding-left: 10px;"><small>Sin Atender</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;"><small>Iniciadas</small></th>

                        <!-- llamadas -->
                        <!-- <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;"><small>% Ayer</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;"><small>% Mes</small></th> -->

                        <!-- ayer -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Asig</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Apro</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Ret Asig</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Ret Apro</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>ret Obj</small></th>
	                    <th style="width: 2%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"></th>
                        
                        <!-- mes -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;text-align: center;"><small>Asig</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;text-align: center;"><small>Apro</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;text-align: center;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;text-align: center;"><small>Ret Asig</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;text-align: center;"><small>Ret Apro</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;text-align: center;"><small>ret Obj</small></th>
	                    <th style="width: 2%; padding: 0px; padding-left: 10px;text-align: center;"></th>

                  	</tr>
                </thead>
                <tbody>

                <?php 
                $object = new stdClass();
                $total_asignaciones_dia = $total_asignaciones_hoy_dependiente =  $total_asignaciones_hoy_independiente = 0;
                $total_asignaciones_mes = 0;
                $total_asignaciones_ayer = 0;
                $total_aprobados_ayer = 0;
                $total_aprobados_dia = $total_aprobados_hoy_dependiente = $total_aprobados_hoy_independiente = 0;
                $total_aprobados_mes = 0;
                $objetivo_aprobados_dia = 200;
                $objetivo_aprobados_mes = 5000;
                $total_reto_asignados = 0;
                $total_reto_aprobados = 0;
                $wapp_sin_atender = 0;
                $wapp_iniciados = 0;
                $total_reto_asignados_ayer = 0;
                $total_reto_aprobados_ayer = 0;
                $total_reto_aprobados_mes = 0;
                $total_reto_asignados_mes = 0;
                foreach ($data['indicadores'] as $key => $value): 
                    if($value["mes"]["asignados"] > 0) 
                    {
                        $total_asignaciones_hoy_dependiente += (isset($value["hoy"]["asignados-dependientes"][0]))? $value["hoy"]["asignados-dependientes"] : 0;
                        $total_asignaciones_hoy_independiente += (isset($value["hoy"]["asignados-independientes"][0]))? $value["hoy"]["asignados-independientes"] : 0;
                        
                        $total_aprobados_hoy_dependiente    += (isset($value["hoy"]["aprobados-dependientes"][0]))? $value["hoy"]["aprobados-dependientes"] : 0;
                        $total_aprobados_hoy_independiente    += (isset($value["hoy"]["aprobados-independientes"][0]))? $value["hoy"]["aprobados-independientes"] : 0;

                        

                        $total_asignaciones_ayer += (isset($value["ayer"]["asignados"][0]))? $value["ayer"]["asignados"] : 0;
                        $total_aprobados_ayer    += (isset($value["ayer"]["aprobados"][0]))? $value["ayer"]["aprobados"] : 0;

                        $total_asignaciones_mes += (isset($value["mes"]["asignados"][0]))? $value["mes"]["asignados"] : 0;
                        $total_aprobados_mes    += (isset($value["mes"]["aprobados"][0]))? $value["mes"]["aprobados"] : 0;

                    

                        $wapp_sin_atender       += $value["whatsapp"]["sin_atender"];
                        $wapp_iniciados         += $value["whatsapp"]["iniciados"];

                        

                        $total_reto_asignados   += (isset($value["hoy"]["reto_asignados"][0]))? $value["hoy"]["reto_asignados"] : 0;
                        $total_reto_aprobados   += (isset($value["hoy"]["reto_aprobados"][0]))? $value["hoy"]["reto_aprobados"] : 0;
                        
                        $total_reto_asignados_ayer += (isset($value["ayer"]["reto_asignados"][0]))? $value["ayer"]["reto_asignados"] : 0;
                        $total_reto_aprobados_ayer += (isset($value["ayer"]["reto_aprobados"][0]))? $value["ayer"]["reto_aprobados"] : 0;

                        $total_reto_asignados_mes += (isset($value["mes"]["reto_asignados"][0]))? $value["mes"]["reto_asignados"] : 0;
                        $total_reto_aprobados_mes += (isset($value["mes"]["reto_aprobados"][0]))? $value["mes"]["reto_aprobados"] : 0;
                        
                        //continue; 
                     ?>
                    
                    <tr>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; height: 35px;"> <?= $value["nombre_apellido"] ?></td>

                        <!-- hoy -->
                        
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                        <?php echo (isset($value["hoy"]["asignados-dependientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_hoy/'.$value["idoperador"].'">'.$value["hoy"]["asignados-dependientes"].'</a>': '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                        <?php echo (isset($value["hoy"]["aprobados-dependientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_hoy/'.$value["idoperador"].'">'.$value["hoy"]["aprobados-dependientes"].'</a>': '--';?>
                        </td>

                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php echo (isset($value["hoy"]["asignados-independientes"][0]))? $value["hoy"]["asignados-independientes"] : '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php echo (isset($value["hoy"]["aprobados-independientes"][0]))? $value["hoy"]["aprobados-independientes"] : '--';?>
                        </td>


                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #F9E79F;"><?php
                            $objetivo_hoy_aux =0;
                            $asignados = intval($value["hoy"]["asignados-dependientes"]) + intval($value["hoy"]["asignados-independientes"]);
                            $aprobados = intval($value["hoy"]["aprobados-dependientes"]) + intval($value["hoy"]["aprobados-independientes"]);
                            if ($asignados > 0){
                                $objetivo_hoy_aux = $aprobados * 100 / $asignados;
                                $objetivo_hoy = number_format(round($objetivo_hoy_aux, '0')); //number_format(round(($objetivo_hoy_aux * 100 / floatval($data['objetivo']) - 100 ), '0'));
                            }else 
                                $objetivo_hoy = "0";
                            
                            echo $objetivo_hoy . '%';
                        ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                        <?php echo (isset($value["hoy"]["reto_asignados"][0]))? '<a href="'.base_url().'tablero/Tablero/retanqueo_hoy/'.$value["idoperador"].'">'.$value["hoy"]["reto_asignados"].'</a>': '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php echo (isset($value["hoy"]["reto_aprobados"][0]))? $value["hoy"]["reto_aprobados"] : '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;">
                            <?php
                                if(floatval($objetivo_hoy) >= 50)
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>

                        <!-- WHATSAPP -->
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center; background:#EAEDED;"> <?= $value["whatsapp"]["sin_atender"] ?></td>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center; background:#EAEDED;"><?= $value["whatsapp"]["iniciados"] ?></td>
                        <!-- LLAMADAS -->
                        <!-- <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;background: #EAEDED;">
                            <?php 
                                $asignados_ayer = (isset($value["ayer"]["asignados"][0]))? $value["ayer"]["asignados"] : 0;
                                $porcentaje_llamadas = 0;
                                if($asignados_ayer > 0){
                                    $porcentaje_llamadas = round($value["llamadas"]["ayer"] / (3 * $asignados_ayer), '0')*100; 
                                } else{
                                    $porcentaje_llamadas = $value["llamadas"]["ayer"];
                                }

                                echo $porcentaje_llamadas.'%';
                                
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;background: #EAEDED;">
                            <?php
                                $asignados_mes = (isset($value["mes"]["asignados"][0]))? $value["mes"]["asignados"] : 0;
                                $porcentaje_llamadas = 0;
                                if($asignados_mes > 0){
                                    $porcentaje_llamadas = round($value["llamadas"]["mes"] / (3 * $asignados_mes), '0')*100; 
                                } else{
                                    $porcentaje_llamadas = $value["llamadas"]["mes"];
                                }

                                echo $porcentaje_llamadas.'%';
                            ?>
                        </td> -->

                        <!-- AYER -->
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                        <?php echo (isset($value["ayer"]["asignados"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_ayer/'.$value["idoperador"].'">'.$value["ayer"]["asignados"].'</a>': '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php  echo (isset($value["ayer"]["aprobados"][0]))? $value["ayer"]["aprobados"] : '--'; ?>                            
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                        <?php 
                            $objetivo_ayer_aux = 0;
                               if (isset($value["ayer"]["asignados"][0]) && $value["ayer"]["asignados"] != 0){
                                    $objetivo_ayer_aux = $value["ayer"]["aprobados"] * 100 / $value["ayer"]["asignados"];
                                    $objetivo_ayer = number_format(round( ($objetivo_ayer_aux * 100 / floatval($data['objetivo']) - 100 ), '0'));
                                } else
                                    $objetivo_ayer = "-100";
                                
                                echo $objetivo_ayer. '%';
                        ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                        <?php echo (isset($value["ayer"]["reto_asignados"][0]))? '<a href="'.base_url().'tablero/Tablero/retanqueo_ayer/'.$value["idoperador"].'">'.$value["ayer"]["reto_asignados"].'</a>': '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php echo (isset($value["ayer"]["reto_aprobados"][0]))? $value["ayer"]["reto_aprobados"] : '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php  //echo (isset($value["ayer"]["reto_aprobados"][0]))? $value["ayer"]["reto_aprobados"] : '--'; 
                                $objetivo_reto = 0;
                                if (isset($value["ayer"]["reto_asignados"][0]) && $value["ayer"]["reto_asignados"] != 0){
                                    $objetivo_reto = $value["ayer"]["reto_aprobados"] * 100 / $value["ayer"]["reto_asignados"];
                                    $objetivo_reto = number_format(round($objetivo_reto - 100 ), '0');
                                } else
                                    $objetivo_reto = "-100";
                                
                                echo $objetivo_reto. '%';
                            
                            ?>
                        </td>

                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;">
                            <?php
                                if(floatval($objetivo_ayer) >= 0)
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>


                        <!-- MES -->
                       
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                        <?php echo (isset($value["mes"]["asignados"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_mes/'.$value["idoperador"].'">'.$value["mes"]["asignados"].'</a>': '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #D5DBDB;"><?= $value["mes"]["aprobados"] ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #F9E79F;"><?php
                                $objetivo_mes_aux = $value["mes"]["aprobados"]* 100 / $value["mes"]["asignados"];
                                $objetivo_mes = number_format(round(($objetivo_mes_aux * 100 / floatval($data['objetivo']) - 100), '0')) ;
                                echo $objetivo_mes. '%' ;
                                ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                        <?php echo (isset($value["mes"]["reto_asignados"][0]))? '<a href="'.base_url().'tablero/Tablero/retanqueo_mes/'.$value["idoperador"].'">'.$value["mes"]["reto_asignados"].'</a>': '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;">
                            <?php echo (isset($value["mes"]["reto_aprobados"][0]))? $value["mes"]["reto_aprobados"] : '--';?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;">
                            <?php  //echo (isset($value["mes"]["reto_aprobados"][0]))? $value["mes"]["reto_aprobados"] : 0 
                                $objetivo_reto = 0;
                                if (isset($value["mes"]["reto_asignados"][0]) && $value["mes"]["reto_asignados"] != 0){
                                    $objetivo_reto = $value["mes"]["reto_aprobados"] * 100 / $value["mes"]["reto_asignados"];
                                    $objetivo_reto = number_format(round($objetivo_reto - 100 ), '0');
                                } else
                                    $objetivo_reto = "-100";
                                
                                echo $objetivo_reto. '%';
                            ?>
                        </td>
                    

                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;background: #EAEDED;"> 
                            <?php
                                if(floatval($objetivo_mes) >= 0)
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                    </tr>
                <?php }endforeach; ?>
                   
                </tbody>
                <tfoot style="font-size: 15px;font-weight: 600;">
                    <tr class="info">
                            <td style="padding: 0px; font-size: 16px; vertical-align: middle;"> TOTAL</td>
                            <!-- Hoy -->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;">
                            <?php if($total_asignaciones_hoy_dependiente > 0): echo '<a href="'.base_url().'tablero/Tablero/primarias_hoy_total">'.$total_asignaciones_hoy_dependiente.'</a>'; else: echo '0'; endif; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;">
                            <?php if($total_aprobados_hoy_dependiente > 0): echo '<a href="'.base_url().'tablero/Tablero/primarias_hoy_total">'.$total_aprobados_hoy_dependiente.'</a>'; else: echo '0'; endif; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;"><?=$total_asignaciones_hoy_independiente?></td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;"><?=$total_aprobados_hoy_independiente?></td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;"></td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;">
                            <?php if($total_reto_asignados>0): echo '<a href="'.base_url().'tablero/Tablero/retanqueo_hoy_total">'.$total_reto_asignados.'</a>'; else: echo '0'; endif; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;"><?= $total_reto_aprobados?></td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;"></td>
                            <!-- whatsapp subtitulo -->
                            <td style="width: 4%; padding: 0px; text-align: center;"><?= $wapp_sin_atender ?></td>
                            <td style="width: 4%; padding: 0px; text-align: center;"><?= $wapp_iniciados ?></td>

                            <!-- llamadas -->
                            <!-- <tr style="width: 4%; padding: 0px; background: #C4D4E7;"><small>% Ayer</small></tr>
                            <tr style="width: 4%; padding: 0px; background: #C4D4E7;"><small>% Mes</small></tr> -->
 
                            <!-- ayer -->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;">
                            <?php if($total_asignaciones_ayer>0): echo '<a href="'.base_url().'tablero/Tablero/primarias_ayer_totales">'.$total_asignaciones_ayer.'</a>'; else: echo '0'; endif; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;"><?= $total_aprobados_ayer ?></td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;"></td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;">
                            <?php if($total_reto_asignados_ayer>0): echo '<a href="'.base_url().'tablero/Tablero/retanqueo_ayer_total">'.$total_reto_asignados_ayer.'</a>'; else: echo '0'; endif; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;"><?= $total_reto_aprobados_ayer?></td></td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;"></td>
                            <td style="width: 2%; padding: 0px; background: #C4D4E7;text-align: center;"></td>
                            
                            <!-- mes -->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;">
                            <?php if($total_asignaciones_mes>0): echo '<a href="'.base_url().'tablero/Tablero/primarias_mes_total">'.$total_asignaciones_mes.'</a>'; else: echo '0'; endif; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; text-align: center;"><?=$total_aprobados_mes?></td>
                            <td style="width: 4%; padding: 0px; text-align: center;"></td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center;">
                            <?php if($total_reto_asignados_mes>0): echo '<a href="'.base_url().'tablero/Tablero/retanqueo_mes_total">'.$total_reto_asignados_mes.'</a>'; else: echo '0'; endif; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; text-align: center;"><?= $total_reto_aprobados_mes?></td></td>
                            <td style="width: 4%; padding: 0px; text-align: center;"></td>
                            <td style="width: 2%; padding: 0px; text-align: center;"></td>
                        </tr>
                </tfoot>
                </table>
            </div>

            <div class="col-lg-12"  style="display: block">
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h3 class="panel-title text-center" style="font-size: x-large;">VENTAS PERDIDAS HOY</h3>
                            </div>
                            <div class="panel-body" style="padding: 0px;">
                                <p class="text-center" style="font-size:80px;">
                                <?php
                                    $total_asignaciones_dia = $total_asignaciones_hoy_dependiente + $total_asignaciones_hoy_independiente;
                                    $total_aprobados_dia = $total_aprobados_hoy_dependiente + $total_aprobados_hoy_independiente;
                                
                                    echo $total_asignaciones_dia - $total_aprobados_dia;
                                ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                    <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title text-center" style="font-size: x-large;">VENTAS AL DIA</h3>
                            </div>
                            <div class="panel-body" style="padding: 0px;">
                                <?php 
                                    $porcent = 0;
                                    if($objetivo_aprobados_dia > 0){
                                        $porcent = $total_aprobados_dia / $objetivo_aprobados_dia *100;
                                    } 
                                ?>
                                <p class="text-center" style="color: <?php echo ($porcent < 50)? 'red':'#10e810'?>; font-size:80px;"><?= round($porcent, '0')-100 ?>%</p>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title text-center" style="font-size: x-large;">RETANQUEOS HOY</h3>
                            </div>
                            <div class="panel-body" style="padding: 0px;">
                                <?php 
                                    $porcent_reto = 0;
                                    if($total_reto_asignados > 0){
                                        $porcent_reto = $total_reto_aprobados / $total_reto_asignados *100;
                                    } 
                                ?>
                                <p class="text-center" style="color: <?php echo ( $porcent_reto < 50)? 'red':'#10e810'?>; font-size:80px;"><?= round($porcent_reto , '0') - 100?>%</p>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title text-center " style="font-size: x-large;">VENTAS AL MES</h3>
                            </div>
                            <div class="panel-body" style="padding: 0px;">
                                <?php 
                                    $porcent = 0;
                                    if($objetivo_aprobados_mes > 0){
                                        $porcent = $total_aprobados_mes / $objetivo_aprobados_mes *100;
                                    } 
                                ?>
                                <p class="text-center" style="color: <?php echo ($porcent < 50)? 'red':'#10e810'?>; font-size:80px;"><?= round($porcent, '0')-100?>%</p>

                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

      	</section>
    	<!-- /.content -->
    </div>

</div>