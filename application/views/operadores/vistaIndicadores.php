<span class="hidden-xs">
	<?php
	$usuario     = $this->session->userdata("username");
    $tipoUsuario = $this->session->userdata("tipo");
?>
</span>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario;?>">

<div id="dashboard_principal" style="display: block; background: #FFFFFF;">

    <div class="row">
    	<!-- Main content -->
      	<section class="content">

            <div class="col-lg-12" id="cuerpoGastos" style="display: block">

              <table data-page-length='15' align="center" id="tp_Indicadores" class="table table-striped table=hover display" width="100%">
                <thead>
                	<tr class="info">
		              	<th style="height: 5px; padding: 0px; width: 15%" rowspan="2">
		                	<h5 align="center"><small><strong>CONSULTOR</strong></small></h5>
		              	</th>

		              	<th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="7">
		                	<h5 align="center"><small><strong>ESTE MES</strong></small></h5>
		              	</th>
		              	<th style="height: 5px; padding: 0px;" colspan="7">
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
		              	<th style="height: 5px; padding: 0px; background: #C4D4E7;" colspan="7">
		                	<h5 align="center"><small><strong>HOY</strong></small></h5>
		              	</th>
		            </tr>
                  	<tr class="info">
	                    
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Asig</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Veri</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Vali</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Apro</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"><small>Ret</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;text-align: center;"></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;"><small>Asig</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;"><small>Veri</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;"><small>Vali</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;"><small>Apro</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;"><small>Ret</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;"></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;"><small>Asig</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;"><small>Veri</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;"><small>Vali</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;"><small>Apro</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;"><small>Obj</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;"><small>Ret</small></th>
	                    <th style="width: 4%; padding: 0px; padding-left: 10px;background: #C4D4E7;"></th>
                  	</tr>
                </thead>
                <tbody>

                <?php foreach ($data['indicadores'] as $key => $value):
                        if( isset($value["mes"]["control"][0]->asignados) && $value["mes"]["control"][0]->asignados > 0) { ?>
                    
                    <tr>
                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; height: 35px;"> <?= $value["nombre_apellido"] ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;"><?= $value["mes"]["control"][0]->asignados ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;"><?= $value["mes"]["control"][0]->verificados ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;"><?= $value["mes"]["control"][0]->validados ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #D5DBDB;">
                            <?php echo (isset($value["mes"]["aprobados"][0]))? $value["mes"]["aprobados"][0]["cantidad"] : 0 ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #F9E79F;">
                            <?php
                                
                                    $objetivo_mes = number_format(round($value["mes"]["aprobados"][0]["cantidad"] * 100 / $value["mes"]["control"][0]->asignados, '2'), 2) ;
                                    echo $objetivo_mes. '%' ;
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #D5DBDB;">
                            <?php echo (isset($value["mes"]["reto"][0]))? $value["mes"]["reto"][0]["cantidad"] : 0 ?>
                        </td>

                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;background: #EAEDED;"> 
                            <?php
                                if($objetivo_mes >= $data['objetivo'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php echo (isset($value["ayer"]["control"][0]))?  $value["ayer"]["control"][0]->asignados: 0 ?> 
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php echo (isset($value["ayer"]["control"][0]))? $value["ayer"]["control"][0]->verificados : 0 ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php echo (isset($value["ayer"]["control"][0]))? $value["ayer"]["control"][0]->validados : 0 ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php echo (isset($value["ayer"]["aprobados"][0]))?  $value["ayer"]["aprobados"][0]["cantidad"] : 0 ?>
                        </td>

                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;"><?php
                                if (isset($value["ayer"]["control"][0]) && $value["ayer"]["control"][0]->asignados > 0)
                                    $objetivo_ayer = number_format(round($value["ayer"]["aprobados"][0]["cantidad"] * 100 / $value["ayer"]["control"][0]->asignados, '2'), 2);
                                else
                                    $objetivo_ayer = "0.00";
                                
                                echo  $objetivo_ayer. '%';
                                ?></td>

                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;">
                            <?php echo (isset($value["ayer"]["reto"][0]))? $value["ayer"]["reto"][0]["cantidad"] : 0 ?>
                        </td>

                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;">
                            <?php
                                if($objetivo_ayer >= $data['objetivo'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;">
                            <?php echo (isset($value["hoy"]["control"][0]))? $value["hoy"]["control"][0]->asignados : 0; ?>
                        </td>

                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;">
                            <?php echo (isset($value["hoy"]["control"][0]))? $value["hoy"]["control"][0]->verificados : 0 ?>
                        </td>

                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;">
                            <?php echo (isset($value["hoy"]["control"][0]))? $value["hoy"]["control"][0]->validados : 0 ?>
                        </td>
                
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #D5DBDB;">
                            <?php echo (isset($value["hoy"]["aprobados"][0]))? $value["hoy"]["aprobados"][0]["cantidad"] : 0 ?>
                        </td>

                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #D5DBDB;"><?php
                            if (isset($value["hoy"]["control"][0]) && $value["hoy"]["control"][0]->asignados > 0)
                                $objetivo_hoy = number_format(round($value["hoy"]["aprobados"][0]["cantidad"] * 100 / $value["hoy"]["control"][0]->asignados, '2'), 2);
                            else 
                                $objetivo_hoy = "0.00";
                            
                            echo $objetivo_hoy . '%';
                        ?></td>
                        <td style="padding: 0px; font-size: 16px; text-align: center; vertical-align: middle;background: #EAEDED;">
                            <?php echo (isset($value["hoy"]["reto"][0]))? $value["hoy"]["reto"][0]["cantidad"] : 0 ?>
                        </td>

                        <td style="padding: 0px; font-size: 16px; vertical-align: middle; text-align: center;background: #EAEDED;">
                            <?php
                                if($objetivo_hoy >= $data['objetivo'])
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-up " style="color:#00a65a; font-size: 2rem"></i>';
                                else
                                    echo '<i title="Entrada" class="fa fa-chevron-circle-down" style="color:#dd4b39; font-size: 2rem"></i>';
                            ?>
                        </td>
                    </tr>
                <?php }endforeach; ?>

                </tbody>
              </table>
            </div>

      	</section>
    	<!-- /.content -->
    </div>

</div>


