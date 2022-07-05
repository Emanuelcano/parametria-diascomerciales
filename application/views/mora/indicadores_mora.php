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
              <table data-page-length='50' align="center" id="tp_Indicadores" class="table table-striped table=hover display cell-border" style="width:100%" width="100%">
                <thead>
                	<tr class="info">
                        <th style="height: 5px; padding: 0px; width: 13%!important; vertical-align: middle;" rowspan="3">
                            <?php
                                if ($this->session->userdata('tipo_operador') == 9)
                                    $this->load->view('mora/configurar_mora');
                            ?>
                            <br>
		                	<h5 align="center"><small><strong>CONSULTOR</strong></small></h5>
		              	</th>

                        <th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="6">
		                	<h5 align="center"><small style="color:black;"><strong>HOY</strong></small></h5>
		              	</th>

		              	
		              	<th style="height: 5px; padding: 0px;background: #6dd9e4; color:black;" colspan="6">
                            <h5 align="center"><small style="color:black;"><strong>
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
		              	<th style="height: 5px; padding: 0px; background-color: #bbbaba;" colspan="6">
		                	<h5 align="center"><small style="color:black;"><strong>ESTE MES</strong></small></h5>
                        </th>
                        <!-- <th style="height: 5px; padding: 0px; background: #ffda22;" colspan="2">
		                	<h5 align="center"><small style="color:black;"><strong>MORA </strong><strong id="rango"></strong></small></h5>
                        </th>-->
                        <th style="height: 5px; padding: 0px; background: #6dd9e4;" colspan="1">
		            	    <h5 align="center"><small style="color:black;"><strong>PROXIMO VENC</strong><br><strong id="fecha_vencimiento_mostrar"></strong></small></h5>
		          	    </th> 
		            </tr>
                    <tr class="info">
                        
                        <!-- Hoy -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
	                    <!-- <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #C4D4E7;border-collapse: collapse;" colspan="1">small>Retanqueo</small</th> -->

                        <!-- whatsapp subtitulo
                        <th class="text-center" style="padding: 0px; padding-left: 10px;border-collapse: collapse;" colspan="2"><small>Hoy</small></th>
                        -->

                        <!-- llamadas -->
                        <!-- <th class="text-center" style="padding: 0px; padding-left: 10px;background: #C4D4E7;border-collapse: collapse;" colspan="2"><small>Hoy</small></th> -->

                        <!-- ayer -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
	                    <!-- <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #6dd9e4;border-collapse: collapse;" colspan="1">small>Retanqueo</small</th> -->
                        
                        <!-- mes -->
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="3"><small>Dependiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="3"><small>Independiente</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background-color: #ffda22;border-collapse: collapse;text-align: center;" colspan="1">
                            <h5 align="center"><small style="color:black;"><strong>MORA </strong></br><strong id="rango"></strong></small></h5>
                        </th>
                        <!-- mes mora -->
                        <!--th class="text-center" style="padding: 0px; padding-left: 10px;background: #cccaeaf7;border-collapse: collapse;" colspan="1"><small>Dependientes</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 10px;background: #F9E79F;border-collapse: collapse;" colspan="1"><small>Independientes</small></th!-->
                        <!-- <th class="text-center" style="padding: 0px;padding-left: 18px;padding-right: 16px;background: #ffda22;border-collapse: collapse;" colspan="2"><small>%</small></th> -->
                      <!-- Templates -->
                        <!-- <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #cccaeaf7;border-collapse: collapse;" colspan="1"><small>Templates</small></th>
                        <th class="text-center" style="padding: 0px; padding-left: 18px;padding-right: 16px;background: #F9E79F;border-collapse: collapse;" colspan="1"><small>Chats</small></th> -->
                    </tr>
                  	<tr class="info">
	                    <!-- Hoy -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Apro / Asig</small></th>
                        <!-- <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Apro</small></th> -->
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Obj</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Rechaz</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Apro / Asig</small></th>
	                    <!-- <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;display:none;"><small>Apro</small></th> -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Rechaz</small></th>
                       
                        <!-- ayer -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Apro / Asig</small></th>
	                    <!-- <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;display:none;"><small>Apro</small></th> -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Rechaz</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Apro / Asig</small></th>
	                    <!-- <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;display:none;"><small>Apro</small></th> -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Rechaz</small></th>
                        
                        <!-- mes -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Apro / Asig</small></th>
	                    <!-- <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;display:none;"><small>Apro</small></th> -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small>rechaz</small></th>
                        <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Apro / Asig</small></th>
	                    <!-- <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;display:none;"><small>Apro</small></th> -->
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #F9E79F;text-align: center;"><small>Rechaz</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background-color: #ffda22;text-align: center;">%</th>
                        <!-- mes Mora-->
                        <!--th style="width: 4%; padding: 0px; padding-left: 10px;background: #cccaeaf7;text-align: center;"><small></small><-->
                        <!-- <th style="width: 4%; padding: 0px; padding-left: 10px;background: #ffda22;text-align: center;"><small></small></th> -->
                        <!-- <th style="width: 4%; padding: 0px; padding-left: 10px;background: #ffda22;text-align: center;"><small></small></th> -->
                        <!-- Templates-->
                        <!-- <th style="width: 4%; padding: 0px; background: #cccaeaf7;text-align: center;"><small>Enviados</small></th>
                        <th style="width: 4%; padding: 0px; background: #F9E79F;text-align: center;"><small>Activos</small></th> -->

                  	</tr>
                </thead>
                <tbody>

                <?php 
                $object = new stdClass();
                $total_mora = 0;
                $total_asignaciones_dia = $total_asignaciones_hoy_dependiente =  $total_asignaciones_hoy_independiente = 0;
                $total_asignaciones_mes = $total_asignaciones_mes_dependiente =  $total_asignaciones_mes_independiente = 0;
                $total_asignaciones_ayer = $total_asignaciones_ayer_dependiente =  $total_asignaciones_ayer_independiente = 0;
                $total_aprobados_ayer = $total_aprobados_ayer_dependiente = $total_aprobados_ayer_independiente = $total_rechazado_ayer_dependiente = $total_rechazado_ayer_independiente = 0;
                $total_aprobados_dia = $total_aprobados_hoy_dependiente = $total_rechazado_hoy_dependiente = $total_aprobados_hoy_independiente = $total_rechazado_hoy_independiente = 0;
                $total_aprobados_mes = $total_aprobados_mes_dependiente = $total_aprobados_mes_independiente = $total_rechazado_mes_dependiente = $total_rechazado_mes_independiente = 0;
                $total_operador = $operador_total = $operador_mora = 0;
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
                        $total_rechazado_hoy_dependiente    += (isset($value["hoy"]["rechazado-dependientes"][0]))? $value["hoy"]["rechazado-dependientes"] : 0;
                        $total_aprobados_hoy_independiente    += (isset($value["hoy"]["aprobados-independientes"][0]))? $value["hoy"]["aprobados-independientes"] : 0;
                        $total_rechazado_hoy_independiente    += (isset($value["hoy"]["rechazado-independientes"][0]))? $value["hoy"]["rechazado-independientes"] : 0;

                        $total_asignaciones_ayer_dependiente += (isset($value["ayer"]["asignados-dependientes"][0]))? $value["ayer"]["asignados-dependientes"] : 0;
                        $total_asignaciones_ayer_independiente += (isset($value["ayer"]["asignados-independientes"][0]))? $value["ayer"]["asignados-independientes"] : 0;
                        
                        $total_aprobados_ayer_dependiente    += (isset($value["ayer"]["aprobados-dependientes"][0]))? $value["ayer"]["aprobados-dependientes"] : 0;
                        $total_aprobados_ayer_independiente    += (isset($value["ayer"]["aprobados-independientes"][0]))? $value["ayer"]["aprobados-independientes"] : 0;                        
                        $total_rechazado_ayer_dependiente    += (isset($value["ayer"]["rechazado-dependientes"][0]))? $value["ayer"]["rechazado-dependientes"] : 0;
                        $total_rechazado_ayer_independiente    += (isset($value["ayer"]["rechazado-independientes"][0]))? $value["ayer"]["rechazado-independientes"] : 0;

                        $total_asignaciones_mes_dependiente += (isset($value["mes"]["asignados-dependientes"][0]))? $value["mes"]["asignados-dependientes"] : 0;
                        $total_asignaciones_mes_independiente += (isset($value["mes"]["asignados-independientes"][0]))? $value["mes"]["asignados-independientes"] : 0;
                        
                        $total_aprobados_mes_dependiente    += (isset($value["mes"]["aprobados-dependientes"][0]))? $value["mes"]["aprobados-dependientes"] : 0;
                        $total_aprobados_mes_independiente    += (isset($value["mes"]["aprobados-independientes"][0]))? $value["mes"]["aprobados-independientes"] : 0;
                        $total_rechazado_mes_dependiente    += (isset($value["mes"]["rechazado-dependientes"][0]))? $value["mes"]["rechazado-dependientes"] : 0;
                        $total_rechazado_mes_independiente    += (isset($value["mes"]["rechazado-independientes"][0]))? $value["mes"]["rechazado-independientes"] : 0;
                        
                        $operador_total += 1;
                       
                        //continue; 
                     ?>
                     
                    <tr>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; height: 35px;"> <?= $value["nombre_apellido"] ?></td>

                        <!-- hoy -->
                        
                        <input type="hidden" value="<?php echo $value["fecha"]["rango"] ?>" id="fecha">
                        <input type="hidden" value="<?php echo $value["fecha"]["fecha_vencimiento"] ?>" id="fecha_vencimiento">

                            <!-- Dependientes -->
                                <!-- Asig - Apro -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                        <?php  
                            echo (isset($value["hoy"]["aprobados-dependientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_hoy/'.$value["idoperador"].'/dependiente/1">'.$value["hoy"]["aprobados-dependientes"].'</a>': '-'; 
                            echo " / ";
                            echo (isset($value["hoy"]["asignados-dependientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_hoy/'.$value["idoperador"].'/dependiente">'.$value["hoy"]["asignados-dependientes"].'</a>': '-' ;
                        ?>
                        </td>
                                <!-- Obj -->
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;"><?php
                            $objetivo_hoy_aux =0;
                            $asignados = intval($value["hoy"]["asignados-dependientes"]);
                            $aprobados = intval($value["hoy"]["aprobados-dependientes"]);
                            if ($asignados > 0){
                                $objetivo_hoy_aux = ($aprobados / $asignados)*100;
                                $objetivo_hoy = number_format(round($objetivo_hoy_aux, '0')); //number_format(round(($objetivo_hoy_aux * 100 / floatval($data['objetivo']) - 100 ), '0'));
                            }else{
                                $objetivo_hoy = "0";
                            }
                            echo $objetivo_hoy . '% ';
                                $objetivo_hoy_aux_dep=0;
                                $objetivo_hoy_aux_ind =0;
                                $asignados_dep = intval($value["hoy"]["asignados-dependientes"]);
                                $aprobados_dep = intval($value["hoy"]["aprobados-dependientes"]);
                                $asignados_ind = intval($value["hoy"]["asignados-independientes"]);
                                $aprobados_ind = intval($value["hoy"]["aprobados-independientes"]);
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
                                if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>

                                <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["hoy"]["rechazado-dependientes"][0]))? $value["hoy"]["rechazado-dependientes"] : '-';
                            ?>
                        </td>

                            <!-- Independiente -->

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["hoy"]["aprobados-independientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_hoy/'.$value["idoperador"].'/independiente/1">'.$value["hoy"]["aprobados-independientes"].'</a>': '-' ;
                                echo " / ";
                                echo (isset($value["hoy"]["asignados-independientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_hoy/'.$value["idoperador"].'/independiente">'.$value["hoy"]["asignados-independientes"].'</a>': '-' ;
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;">
                            <?php
                                // $objetivo_hoy_aux =0;
                                $asignados = intval($value["hoy"]["asignados-independientes"]);
                                $aprobados = intval($value["hoy"]["aprobados-independientes"]);

                                if ($asignados > 0){
                                    // $objetivo_hoy_aux = ($aprobados / $asignados)*100;
                                    $objetivo_hoy = number_format(round((($aprobados / $asignados)*100), '0')); 
                                }else 
                                    $objetivo_hoy = "0";
                                
                                echo $objetivo_hoy . '% ';
                                $objetivo_hoy_aux_dep=0;
                                $objetivo_hoy_aux_ind =0;
                                $asignados_dep = intval($value["hoy"]["asignados-dependientes"]);
                                $aprobados_dep = intval($value["hoy"]["aprobados-dependientes"]);
                                $asignados_ind = intval($value["hoy"]["asignados-independientes"]);
                                $aprobados_ind = intval($value["hoy"]["aprobados-independientes"]);
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
                                if(floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                                <!-- Rechazados -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["hoy"]["rechazado-independientes"][0]))? $value["hoy"]["rechazado-independientes"] : '-';
                            ?>
                        </td>
                       
                        
                        <!-- <td style="padding: 0px; font-size: 16px;width: 3%;vertical-align: middle; text-align: center; background-color:#d1daef;"> -->
                            <?php
                               
                                // $objetivo_hoy_aux_dep=0;
                                // $objetivo_hoy_aux_ind =0;
                                // $asignados_dep = intval($value["hoy"]["asignados-dependientes"]);
                                // $aprobados_dep = intval($value["hoy"]["aprobados-dependientes"]);
                                // $asignados_ind = intval($value["hoy"]["asignados-independientes"]);
                                // $aprobados_ind = intval($value["hoy"]["aprobados-independientes"]);
                                // if($asignados_dep > 0 && $asignados_ind >0){
                                //     $objetivo_hoy_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                //     $objetivo_dependientes = number_format(round($objetivo_hoy_aux_dep, '0'));
                                //     $objetivo_hoy_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                //     $objetivo_independientes = number_format(round($objetivo_hoy_aux_ind, '0'));
                                // }else{
                                //     $objetivo_dependientes =0;
                                //     $objetivo_independientes =0;
                                // }                               

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                //     echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                // else
                                //     echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        <!-- </td> -->

                        <!-- WHATSAPP
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center; background:#EAEDED;"> <? //= $value["whatsapp"]["sin_atender"] ?></td>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center; background:#EAEDED;"><? //= $value["whatsapp"]["iniciados"] ?></td> -->
                        <!-- LLAMADAS -->
                        <!-- <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;background: #EAEDED;">
                            <?php 
                        
                                /*$asignados_ayer = (isset($value["ayer"]["asignados"][0]))? $value["ayer"]["asignados"] : 0;
                                $porcentaje_llamadas = 0;
                                if($asignados_ayer > 0){
                                    $porcentaje_llamadas = round($value["llamadas"]["ayer"] / (3 * $asignados_ayer), '0')*100; 
                                } else{
                                    $porcentaje_llamadas = $value["llamadas"]["ayer"];
                                }

                                echo $porcentaje_llamadas.'%';
                                */
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;background: #EAEDED;">
                            <?php
                               /* $asignados_mes = (isset($value["mes"]["asignados"][0]))? $value["mes"]["asignados"] : 0;
                                $porcentaje_llamadas = 0;
                                if($asignados_mes > 0){
                                    $porcentaje_llamadas = round($value["llamadas"]["mes"] / (3 * $asignados_mes), '0')*100; 
                                } else{
                                    $porcentaje_llamadas = $value["llamadas"]["mes"];
                                }
                                echo $porcentaje_llamadas.'%';*/
                            ?>
                        </td> -->

                        <!-- AYER -->
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                        <?php 
                            echo (isset($value["ayer"]["aprobados-dependientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_ayer/'.$value["idoperador"].'/dependiente/1">'.$value["ayer"]["aprobados-dependientes"].'</a>': '--';
                            echo " / ";
                            echo (isset($value["ayer"]["asignados-dependientes"][0]))?'<a href="'.base_url().'tablero/Tablero/primarias_ayer/'.$value["idoperador"].'/dependiente">'.$value["ayer"]["asignados-dependientes"].'</a>':'--';                            
                        ?>
                        </td>
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;">
                            <?php
                                $objetivo_ayer_aux =0;
                                if(isset($value["ayer"]["asignados-dependientes"][0],$value["ayer"]["aprobados-dependientes"][0])){
                                    $asignados = intval($value["ayer"]["asignados-dependientes"]) ;
                                    $aprobados = intval($value["ayer"]["aprobados-dependientes"]) ;
                                }else{
                                    $asignados = '0';
                                    $aprobados = '0';
                                }
                                if ($asignados > 0){
                                    $objetivo_ayer_aux = ($aprobados / $asignados)*100;
                                    $objetivo_ayer = number_format(round($objetivo_ayer_aux, '0'));
                                }else{
                                    $objetivo_ayer = "0";
                                }                            
                                echo $objetivo_ayer . '% ';
                                $objetivo_ayer_aux_dep=0;
                                $objetivo_ayer_aux_ind =0;
                                $asignados_dep = intval($value["ayer"]["asignados-dependientes"]);
                                $aprobados_dep = intval($value["ayer"]["aprobados-dependientes"]);
                                $asignados_ind = intval($value["ayer"]["asignados-independientes"]);
                                $aprobados_ind = intval($value["ayer"]["aprobados-independientes"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                    $objetivo_ayer_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                    $objetivo_dependientes = number_format(round($objetivo_ayer_aux_dep, '0'));
                                    $objetivo_ayer_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                    $objetivo_independientes = number_format(round($objetivo_ayer_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }                               

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] )
                                echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["ayer"]["rechazado-dependientes"][0]))? $value["ayer"]["rechazado-dependientes"][0] : '-';
                            ?>
                        </td>
                        
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["ayer"]["aprobados-independientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_ayer/'.$value["idoperador"].'/independiente/1">'.$value["ayer"]["aprobados-independientes"].'</a>': '--';
                                echo " / ";
                                echo (isset($value["ayer"]["asignados-independientes"][0]))?'<a href="'.base_url().'tablero/Tablero/primarias_ayer/'.$value["idoperador"].'/independiente">'.$value["ayer"]["asignados-independientes"].'</a>':'--';
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;">
                            <?php 
                                $objetivo_ayer_aux =0;
                                if(isset($value["ayer"]["asignados-independientes"][0],$value["ayer"]["aprobados-independientes"][0])){
                                    $asignados = intval($value["ayer"]["asignados-independientes"]);
                                    $aprobados = intval($value["ayer"]["aprobados-independientes"]);
                                }else{
                                    $asignados = '0';
                                    $aprobados = '0';
                                } 
                                if ($asignados > 0){
                                    $objetivo_ayer_aux = ($aprobados / $asignados)*100;
                                    $objetivo_ayer = number_format(round($objetivo_ayer_aux, '0'));
                                }else{
                                    $objetivo_ayer = "0 ";
                                } 
                                echo $objetivo_ayer . '% '; 
                                $objetivo_ayer_aux_dep=0;
                                $objetivo_ayer_aux_ind =0;
                                $asignados_dep = intval($value["ayer"]["asignados-dependientes"]);
                                $aprobados_dep = intval($value["ayer"]["aprobados-dependientes"]);
                                $asignados_ind = intval($value["ayer"]["asignados-independientes"]);
                                $aprobados_ind = intval($value["ayer"]["aprobados-independientes"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                    $objetivo_ayer_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                    $objetivo_dependientes = number_format(round($objetivo_ayer_aux_dep, '0'));
                                    $objetivo_ayer_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                    $objetivo_independientes = number_format(round($objetivo_ayer_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }                               

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["ayer"]["rechazado-independientes"][0]))? $value["ayer"]["rechazado-independientes"] : '-';
                            ?>
                        </td>
                        <!-- <td style="padding: 0px; font-size: 16px; width: 6%;vertical-align: middle; text-align: center; background-color: #6dd9e4;"> -->
                            <?php
                            //    $objetivo_ayer_aux_dep=0;
                            //    $objetivo_ayer_aux_ind =0;
                            //    $asignados_dep = intval($value["ayer"]["asignados-dependientes"]);
                            //    $aprobados_dep = intval($value["ayer"]["aprobados-dependientes"]);
                            //    $asignados_ind = intval($value["ayer"]["asignados-independientes"]);
                            //    $aprobados_ind = intval($value["ayer"]["aprobados-independientes"]);
                            //    if($asignados_dep > 0 && $asignados_ind >0){
                            //        $objetivo_ayer_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                            //        $objetivo_dependientes = number_format(round($objetivo_ayer_aux_dep, '0'));
                            //        $objetivo_ayer_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                            //        $objetivo_independientes = number_format(round($objetivo_ayer_aux_ind, '0'));
                            //    }else{
                            //        $objetivo_dependientes =0;
                            //        $objetivo_independientes =0;
                            //    }                               

                            //    if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                            //    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                            //    else
                            //        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                           ?>
                        <!-- </td> -->

                        <!-- MES -->
                        <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;">
                            <?php 
                                echo (isset($value["mes"]["aprobados-dependientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_mes/'.$value["idoperador"].'/dependiente/1">'.$value["mes"]["aprobados-dependientes"].'</a>': '--';
                                echo " / ";
                                echo (isset($value["mes"]["asignados-dependientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_mes/'.$value["idoperador"].'/dependiente">'.$value["mes"]["asignados-dependientes"].'</a>': '--';
                            ?>
                        </td>

                        <td style="padding: 0px; font-size: 18px; text-align: center; vertical-align: middle;">
                            <?php
                                $objetivo_mes_aux =0;
                                if(isset($value["mes"]["asignados-dependientes"][0],$value["mes"]["aprobados-dependientes"][0])){
                                    $asignados = intval($value["mes"]["asignados-dependientes"]);
                                    $aprobados = intval($value["mes"]["aprobados-dependientes"]);
                                }else{
                                    $asignados = '0';
                                    $aprobados = '0';
                                } 
                                if ($asignados > 0){
                                    $objetivo_mes_aux = ($aprobados / $asignados)*100;
                                    $objetivo_mes = number_format(round($objetivo_mes_aux, '0'));
                                }else{
                                    $objetivo_mes = "0";
                                }
                                echo $objetivo_mes . '% ';
                                $objetivo_mes_aux_dep=0;
                                $objetivo_mes_aux_ind =0;
                                $asignados_dep = intval($value["mes"]["asignados-dependientes"]);
                                $aprobados_dep = intval($value["mes"]["aprobados-dependientes"]);
                                $asignados_ind = intval($value["mes"]["asignados-independientes"]);
                                $aprobados_ind = intval($value["mes"]["aprobados-independientes"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                        $objetivo_mes_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                        $objetivo_dependientes = number_format(round($objetivo_mes_aux_dep, '0'));
                                        $objetivo_mes_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                        $objetivo_independientes = number_format(round($objetivo_mes_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }                               

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] )
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["mes"]["rechazado-dependientes"][0]))? $value["mes"]["rechazado-dependientes"] : '-';
                            ?>
                        </td>
                        <td style="padding: 0px;font-size: 12px; text-align: center; vertical-align: middle;">
                        <?php 
                            echo (isset($value["mes"]["aprobados-independientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_mes/'.$value["idoperador"].'/independiente/1">'.$value["mes"]["aprobados-independientes"].'</a>': '--';
                            echo " / ";
                            echo (isset($value["mes"]["asignados-independientes"][0]))? '<a href="'.base_url().'tablero/Tablero/primarias_mes/'.$value["idoperador"].'/independiente">'.$value["mes"]["asignados-independientes"].'</a>': '--';                            
                            // echo (isset($value["mes"]["aprobados-independientes"][0]))? $value["mes"]["aprobados-independientes"] : '--';?>
                        </td>
                        <td style="padding: 0px;  font-size: 18px; text-align: center; vertical-align: middle;">
                            <?php  $objetivo_mes_aux =0;
                                if(isset($value["mes"]["asignados-independientes"][0],$value["mes"]["aprobados-independientes"][0])){
                                    $asignados = intval($value["mes"]["asignados-independientes"]);
                                    $aprobados = intval($value["mes"]["aprobados-independientes"]);
                                }else{
                                    $asignados = '0';
                                    $aprobados = '0';
                                } 
                                if ($asignados > 0){
                                    $objetivo_mes_aux = ($aprobados / $asignados)*100;
                                    $objetivo_mes = number_format(round($objetivo_mes_aux, '0'));
                                }else{
                                    $objetivo_mes = "0";
                                }
                                echo $objetivo_mes . '% ';
                                $objetivo_mes_aux_dep=0;
                                $objetivo_mes_aux_ind =0;
                                $asignados_dep = intval($value["mes"]["asignados-dependientes"]);
                                $aprobados_dep = intval($value["mes"]["aprobados-dependientes"]);
                                $asignados_ind = intval($value["mes"]["asignados-independientes"]);
                                $aprobados_ind = intval($value["mes"]["aprobados-independientes"]);
                                if($asignados_dep > 0 && $asignados_ind >0){
                                        $objetivo_mes_aux_dep = ($aprobados_dep/ $asignados_dep)*100;
                                        $objetivo_dependientes = number_format(round($objetivo_mes_aux_dep, '0'));
                                        $objetivo_mes_aux_ind = ($aprobados_ind / $asignados_ind)*100;
                                        $objetivo_independientes = number_format(round($objetivo_mes_aux_ind, '0'));
                                }else{
                                    $objetivo_dependientes =0;
                                    $objetivo_independientes =0;
                                }                               

                                // if(floatval($objetivo_dependientes) >= $value['porcentaje']['objetivos_dependientes'] && floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                if(floatval($objetivo_independientes) >= $value['porcentaje']['objetivos_independientes'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
                            <?php 
                                echo (isset($value["mes"]["rechazado-independientes"][0]))? $value["mes"]["rechazado-independientes"] : '-';
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; width: 6%;vertical-align: middle; text-align: center;"> 
                            <?php
                                if(isset($value["mora"]["valor_caso_total"],$value["mora"]["valor_mora_total"])){
                                    $caso_total = intval($value["mora"]["valor_caso_total"]);
                                    $mora_total= intval($value["mora"]["valor_mora_total"]);
                                }else{
                                    $mora_dependiente = "0";
                                    $mora_independiente = "0";
                                }
                                if ($mora_total > 0){
                                    $conv_mora_aux = round(($mora_total  * 100) / $caso_total);
                                    $total_mora+= $conv_mora_aux;
                                }else{
                                    $conv_mora_aux = "0";
                                }
                            // $categoria = $conv_mora_aux;
                                // echo $conv_mora_aux . '%'; 

                                echo ($conv_mora_aux > 0)? '<a href="'.base_url().'tablero/Tablero/mora_mes/'.$value["idoperador"].'">'.$conv_mora_aux . '% </a>': $conv_mora_aux . '%';

                                if($conv_mora_aux > 0)
                                    $operador_mora += 1; 
                                echo "     ";
                                if(floatval($conv_mora_aux) <= $value['porcentaje']['objetivo_mora'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                        
                        <!-- Mes Mora -->
                        <!--td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;"> 
                            <?php 
                               /* $conv_mora_aux =0;
                                if(isset($value["mora"]["mora_mes_dependiente"],$value["mora"]["asignados-dependientes_tipo_laboral"])){
                                    $mora_dependiente = intval($value["mora"]["mora_mes_dependiente"]);
                                    $mora_dependiente_laboral = intval($value["mora"]["asignados-dependientes_tipo_laboral"]);
                                }else{
                                    $mora_dependiente = "0";
                                    $mora_independiente = "0";
                                }
                                if ($mora_dependiente_laboral > 0){
                                    $conv_mora_aux = round(($mora_dependiente_laboral * 100) / $mora_dependiente);  
                                }else{
                                     $conv_mora_aux = "0";
                                }
                               
                                echo $conv_mora_aux . '%';*/
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php 
                                /*if(isset($value["mora"]["mora_mes_independiente"],$value["mora"]["asignados-independientes_tipo_laboral"])){
                                    $mora_dependiente = intval($value["mora"]["mora_mes_independiente"]);
                                    $mora_dependiente_laboral = intval($value["mora"]["asignados-independientes_tipo_laboral"]);
                                }else{
                                    $mora_dependiente = "0";
                                    $mora_independiente = "0";
                                }
                                if ($mora_dependiente_laboral > 0){
                                    $conv_mora_aux = round(($mora_dependiente_laboral  * 100) / $mora_dependiente);  
                                }else{
                                     $conv_mora_aux = "0";
                                }
                                
                                echo $conv_mora_aux . '%';
                                echo  $value["mora"]["valor_caso_total"];
                                echo $value["mora"]["valor_mora_total"];*/
                            ?>
                        </td-->
                        
                        <!-- <td style="padding: 11px; font-size: 16px; vertical-align: middle; text-align: center;">    
                            <?php
                            //     if(isset($value["mora"]["valor_caso_total"],$value["mora"]["valor_mora_total"])){
                            //         $caso_total = intval($value["mora"]["valor_caso_total"]);
                            //         $mora_total= intval($value["mora"]["valor_mora_total"]);
                            //     }else{
                            //         $mora_dependiente = "0";
                            //         $mora_independiente = "0";
                            //     }
                            //     if ($mora_total > 0){
                            //         $conv_mora_aux = round(($mora_total  * 100) / $caso_total);
                            //         $total_mora+= $conv_mora_aux;
                            //     }else{
                            //         $conv_mora_aux = "0";
                            //     }
                            //    // $categoria = $conv_mora_aux;
                            //     echo $conv_mora_aux . '%'; 
                            //     if($conv_mora_aux > 0)
                            //         $operador_mora += 1;     
                            ?>
                        </td> -->
                        <!-- <td style="padding: 10px; font-size: 16px; vertical-align: middle; text-align: center;">
                            <?php
                                // if(floatval($conv_mora_aux) <= $value['porcentaje']['objetivo_mora'])
                                //     echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                // else
                                    // echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?> 
                        </td> -->
                        <!--Nueva tabla-->
                        <!-- <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;">
                            <?php 
                                // if(isset($value["templates"]["templates_total"])){
                                //     $templates_activos = $value["templates"]["templates_total"];
                                // }else{
                                //     $templates_activos = 0;
                                // } 
                                // echo $templates_activos;
                            ?>
                        </td> -->
                        <!-- <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;">
                            <?php 
                                // if(isset($value["chats"]["chats_total"])){
                                //     $chats_activos = $value["chats"]["chats_total"];
                                // }else{
                                //     $chats_activos = 0;
                                // } 
                                // echo $chats_activos;
                            ?>
                        </td> -->
                    </tr>
                <?php }endforeach; ?>
                   
                </tbody>
                <tfoot style="font-size: 15px;font-weight: 600;">
                    <tr class="info"> 
                            <td style="padding: 0px; font-size: 16px; vertical-align: middle;"> TOTAL</td>
                            <!-- Hoy Dependiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_hoy_dependiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_hoy_total/dependiente/1">'.$total_aprobados_hoy_dependiente.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_hoy_dependiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_hoy_total/dependiente">'.$total_asignaciones_hoy_dependiente.'</a>': '0'; ?>
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
                                    if(floatval($porc_hoy_tot_dep) >= $value['porcentaje']['objetivos_dependientes'])
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
                                <?=($total_aprobados_hoy_independiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_hoy_total/independiente/1">'.$total_aprobados_hoy_independiente.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_hoy_independiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_hoy_total/independiente">'.$total_asignaciones_hoy_independiente.'</a>': '0'; ?>
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
                                    if(floatval($porc_hoy_tot_ind) >= $value['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_hoy_independiente > 0)? $total_rechazado_hoy_independiente : '0'; ?>
                            </td> 
                            <!-- ayer Dependiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_ayer_dependiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_ayer_totales/dependiente/1">'.$total_aprobados_ayer_dependiente.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_ayer_dependiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_ayer_totales/dependiente">'.$total_asignaciones_ayer_dependiente.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_ayer_tot_dep = 0;
                                    if($total_aprobados_ayer_dependiente > 0 && $total_asignaciones_ayer_dependiente >0){
                                        $porc_ayer_aux_dep = ($total_aprobados_ayer_dependiente/ $total_asignaciones_ayer_dependiente)*100;
                                        $porc_ayer_tot_dep = number_format(round($porc_ayer_aux_dep, '0'));
                                        echo $porc_ayer_tot_dep . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_ayer_tot_dep) >= $value['porcentaje']['objetivos_dependientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_ayer_dependiente > 0)? $total_rechazado_ayer_dependiente : '0'; ?>
                            </td> 
                            <!-- ayer Independiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_ayer_independiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_ayer_totales/independiente/1">'.$total_aprobados_ayer_independiente.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_ayer_independiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_ayer_totales/independiente">'.$total_asignaciones_ayer_independiente.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_ayer_tot_ind = 0;
                                    if($total_aprobados_ayer_independiente > 0 && $total_asignaciones_ayer_independiente >0){
                                        $porc_ayer_aux_ind = ($total_aprobados_ayer_independiente/ $total_asignaciones_ayer_independiente)*100;
                                        $porc_ayer_tot_ind = number_format(round($porc_ayer_aux_ind, '0'));
                                        echo $porc_ayer_tot_ind . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_ayer_tot_ind) >= $value['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_ayer_independiente > 0)? $total_rechazado_ayer_independiente : '0'; ?>
                            </td> 
                            <!-- mes Dependiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_mes_dependiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_mes_total/dependiente/1">'.$total_aprobados_mes_dependiente.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_mes_dependiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_mes_total/dependiente">'.$total_asignaciones_mes_dependiente.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_mes_tot_dep = 0;
                                    if($total_aprobados_mes_dependiente > 0 && $total_asignaciones_mes_dependiente >0){
                                        $porc_mes_aux_dep = ($total_aprobados_mes_dependiente/ $total_asignaciones_mes_dependiente)*100;
                                        $porc_mes_tot_dep = number_format(round($porc_mes_aux_dep, '0'));
                                        echo $porc_mes_tot_dep . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_mes_tot_dep) >= $value['porcentaje']['objetivos_dependientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_mes_dependiente > 0)? $total_rechazado_mes_dependiente : '0'; ?>
                            </td> 
                            <!-- mes Independiente-->
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "> 
                                <?=($total_aprobados_mes_independiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_mes_total/independiente/1">'.$total_aprobados_mes_independiente.'</a>' : '0'; ?>
                                <?= " / ";?>
                                <?=($total_asignaciones_mes_independiente > 0)? '<a href="'.base_url().'tablero/Tablero/primarias_mes_total/independiente">'.$total_asignaciones_mes_independiente.'</a>': '0'; ?>
                            </td>
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?php 
                                    $porc_mes_tot_ind = 0;
                                    if($total_aprobados_mes_independiente > 0 && $total_asignaciones_mes_independiente >0){
                                        $porc_mes_aux_ind = ($total_aprobados_mes_independiente/ $total_asignaciones_mes_independiente)*100;
                                        $porc_mes_tot_ind = number_format(round($porc_mes_aux_ind, '0')) ;
                                        echo $porc_mes_tot_ind . "% ";

                                    }else{
                                        echo 0 . "% ";
                                    }
                                    if(floatval($porc_mes_tot_ind) >= $value['porcentaje']['objetivos_independientes'])
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                    else
                                        echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                                ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; ">
                                <?=($total_rechazado_mes_independiente > 0)? $total_rechazado_mes_independiente : '0'; ?>
                            </td> 
                            <td style="width: 4%; padding: 0px; background: #C4D4E7;text-align: center; vertical-align: middle; "></td>
                    </tr>
                </tfoot>
            </table>
            </div>
            <!-- <div class="col-md-12">
                <div class="row col-md-1" >
                    <a id="modo-oscuro" onclick="oscuro()" class="btn bg-black">Modo Oscuro</a>
                </div>
                <div class="row col-md-2" >
                    <?php
                        // if ($this->session->userdata('tipo_operador') == 9)
                        //     $this->load->view('mora/configurar_mora');
                    ?>
                </div>
                
            </div> -->
            <div class="col-lg-12"  style="display: block">
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h3 class="panel-title text-center" style="font-size: 14px; color:black;"><strong>VENTAS DEL DIA</strong></h3>
                            </div>
                            <div class="panel-body" style="padding: 0px;">
                                <!-- <p class="text-center" style="font-size:80px;"> -->
                                <?php
                                    $total_ventas_dia = $total_aprobados_hoy_dependiente + $total_aprobados_hoy_independiente;
                                    $total_asig_por = $total_asignaciones_hoy_dependiente + $total_asignaciones_hoy_independiente;
                                    if($total_ventas_dia > 0 || $total_asig_por >0){
                                        $total_por_dia = ($total_aprobados_hoy_dependiente + $total_aprobados_hoy_independiente)/($total_asignaciones_hoy_dependiente + $total_asignaciones_hoy_independiente) *100;
                                    }else{
                                        $total_por_dia = '0';
                                    }
                                    echo '<div class="col-md-6 col-md-offset-1" style="color: #6f76d4;font-size: 25px; margin-top: 2%;">'.number_format($total_ventas_dia, 0,',','.').' / '.number_format($total_asig_por, 0,',','.').'</div>';
                                    echo '<div class="col-md-5" style="font-size: 35px; color: #6f76d4;"><strong>'.round($total_por_dia).'%</strong></div>';
                                    // echo '<div style="width: 63%;margin-left: 6%;float: left;color: red;font-size: 51px;">'.number_format($total_ventas_dia, 0,',','.').' / '.number_format($total_asig_por, 0,',','.').'</div>';
                                    // echo '<div style="float: left;font-size: 31px; color: black;margin-top: 4%;"><strong>'.round($total_por_dia).'%</strong></div>';
                                ?>
                                <!-- </p> -->
                            </div>
                        </div>
                    </div>
                    <style>
                        .panel-danger>.panel-heading {
                            color: #060606;
                            background-color: #bdc0ea;
                        }
                    </style>
                    <div class="col-md-3">
                    <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title text-center" style="font-size: 14px;color:black;"><strong>VENTAS DE AYER</strong>
                            </div>
                            <div class="panel-body" style="padding: 0px;">
                                <?php
                                    $total_ventas_ayer = $total_aprobados_ayer_dependiente + $total_aprobados_ayer_independiente;
                                    $total_asig_por_ayer = $total_asignaciones_ayer_dependiente + $total_asignaciones_ayer_independiente;
                                    if($total_ventas_ayer > 0 || $total_asig_por_ayer >0){
                                        $total_por_ayer = ($total_aprobados_ayer_dependiente + $total_aprobados_ayer_independiente)/($total_asignaciones_ayer_dependiente + $total_asignaciones_ayer_independiente) *100;
                                    }else{
                                        $total_por_ayer = '0';
                                    }
                                    echo '<div class="col-md-6 col-md-offset-1" style="color: #337ab7;font-size: 25px; margin-top: 2%;">'.number_format($total_ventas_ayer,0,',','.').' / '.number_format($total_asig_por_ayer,0,',','.').'</div>';
                                    echo '<div class="col-md-5" style="float: left;font-size: 35px; color: #337ab7;"><strong>'.round($total_por_ayer , '0').'%</strong></div>';
                                    // echo '<div style="width: 63%;margin-left: 6%;float: left;color: #337ab7;font-size: 51px;">'.number_format($total_ventas_ayer,0,',','.').' / '.number_format($total_asig_por_ayer,0,',','.').'</div>';
                                    // echo '<div style="float: left;font-size: 31px; color: #337ab7;margin-top: 4%;"><strong>'.round($total_por_ayer , '0').'%</strong></div>';
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-primary" style="border-color: #76e07a;">
                            <div class="panel-heading" style="background-color: #76e07a;border-color: #76e07a;">
                                <h3 class="panel-title text-center" style="font-size: 14px;color:black;"><strong>VENTAS DEL MES</strong></h3>
                            </div>
                            <div class="panel-body" style="padding: 0px;">
                                <?php
                                    $total_ventas_mes = $total_aprobados_mes_dependiente + $total_aprobados_mes_independiente;
                                    $total_asig_por_mes = $total_asignaciones_mes_dependiente + $total_asignaciones_mes_independiente;
                                    if($total_ventas_mes > 0 || $total_asig_por_mes >0){
                                        $total_por_mes = ($total_aprobados_mes_dependiente + $total_aprobados_mes_independiente)/($total_asignaciones_mes_dependiente + $total_asignaciones_mes_independiente) *100;
                                    }else{
                                        $total_por_mes = '0';
                                    }
                                    echo '<div class="col-md-6 col-md-offset-1" style="color: #61b764;;font-size: 25px;margin-top: 2%;">'.number_format($total_ventas_mes, 0,',','.').' / '.number_format($total_asig_por_mes,0,',','.').'</div>';
                                    echo '<div class="col-md-5" style="float: left;font-size: 35px; color: #61b764;"><strong>'.round($total_por_mes , '0').'%</strong></div>';
                                    // echo '<div class="col-md-6 col-md-offset-1" style="color: #76e07a;;font-size: 30px;">'.number_format($total_ventas_mes, 0,',','.').' / '.number_format($total_asig_por_mes,0,',','.').'</div>';
                                    // echo '<div style="float: left;font-size: 31px; color: #76e07a;"><strong>'.round($total_por_mes , '0').'%</strong></div>';
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-primary" style="border-color: #ff2525;">
                            <div class="panel-heading" style="background-color: #ff2525;border-color: #ff2525;">
                                <h3 class="panel-title text-center" style="font-size: 14px;color:black;"><strong>MORA ACUMULADA PERIODO</strong></h3>
                            </div>
                            <div class="panel-body" style="padding: 0px;">
                            <!-- <p class="text-center" style="font-size:80px;"> -->
                                <?php
                                    
                                    if($operador_mora > 0){
                                        $total = ($total_mora / $operador_mora);
                                    }else{
                                        $total = '0';
                                    }
                                echo '<div class="col-md-5 col-md-offset-5" style="font-size: 35px;color: #ff2525;"><strong>'.round($total, '0').'%</strong></div>'; 
                                ?>
                                <!-- </p> -->
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

      	</section>
    	<!-- /.content -->
    </div>
    
</div>
<script>
function oscuro(){	
    if ($("body").hasClass("modo-oscuro")) {
        $("body").removeClass("modo-oscuro");
        $("#modo-oscuro").html("Modo Oscuro");
        $("#modo-oscuro").removeClass("btn-default");
        $("#modo-oscuro").addClass("bg-black");
        $("#modo-oscuro").addClass("btn-default");
    } else {
        $("body").addClass("modo-oscuro");
        $("#modo-oscuro").html("Modo Luminoso");
        $("#modo-oscuro").removeClass("bg-black");
        $("#modo-oscuro").addClass("btn-default");
    }
}
</script>
