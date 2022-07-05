
<?php $estilo_div = "style='padding-right: 1px; padding-left: 7px;'";
if(!empty($condition)): ?>
    <div class="col-md-2 col-sm-6" <?php echo $estilo_div ?>>
        <div id="" class="box box-info small">
            <div class="box-header with-border">
                <h6 class="box-title text-center"><small><strong>Solicitud Inicial</strong></small></h6>
            </div>
            <div class="box-body grid-striped">
                <div class="row css-table-row"> 
                    <div class="col-md-6">
                            <div class="pull-right" style=""> Solicitado</div>
                    </div>
                    <div class="col-md-6">
                        <div class="analisis_col_der">
                            <strong><?php echo isset($condition['capital_solicitado'])?"$".number_format($condition['capital_solicitado'],0,",","."):'' ?></strong>
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-6">
                            <div class="pull-right" style=""> Plazo</div>
                    </div>
                    <div class="col-md-6">
                        <div class="analisis_col_der">
                            <strong><?php echo isset($condition['plazo'])?$condition['plazo']:'' ?></strong>
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-6">
                        <?php 
                        $plazo_aux = ($condition['plazo'] > 0)? $condition['plazo'] : 1 ;
                        $v_cuota = $condition['total_devolver'] / $plazo_aux ?>
                        <div class="pull-right" style=""> Valor Cuota</div>
                    </div>
                    <div class="col-md-6">
                        <div class="analisis_col_der">
                            <strong><?php echo isset($v_cuota)?"$".number_format($v_cuota,0,",","."):'' ?></strong>
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-6">
                        <div class="pull-right" style="">1° Vencimiento</div>
                    </div>
                    <div class="col-md-6">
                        <div class="analisis_col_der">
                            <strong><?php echo isset($condition['fecha_pago_inicial'])?date("d-m-Y", strtotime($condition['fecha_pago_inicial'])):'' ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if(!empty($offer)): ?>
    <div class="col-md-2 col-sm-6" <?php echo $estilo_div ?>> 
        <div id="" class="box box-info small">
            <div class="box-header with-border">
                <h6 class="box-title"><small><strong>Ofrecido</strong></small></h6>
            </div>
            <div class="box-body grid-striped">
                <div class="row css-table-row">
                    <div class="col-md-6">
                        <div class="pull-right" style="">Monto</div>
                    </div>
                    <div class="col-md-6">
                        <div class="analisis_col_der">
                            <strong><?php echo isset($offer['monto_maximo'])?"$".number_format($offer['monto_maximo'],0,",","."):'' ?></strong>
                        </div>
                    </div>
                </div>
                <div class="row css-table-row"> 
                    <div class="col-md-6">
                        <div class="pull-right" style="">Plazo</div>
                    </div>
                    <div class="col-md-6">
                        <div class="analisis_col_der">
                            <strong><?php echo isset($offer['plazo_maximo'])?$offer['plazo_maximo']:'' ?></strong>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if(!empty($desembolso)):
    /*$fecha_actual = getdate();
    $dia          = $fecha_actual['mday'];
    $mes          = str_pad($fecha_actual['mon'], 2, '0', STR_PAD_LEFT);
    $anio         = $fecha_actual['year'];
    $dia_semana   = $fecha_actual['weekday'];
    $lastDateOfMonth = date("Y-m-t", strtotime("$dia-$mes-$anio"));
    $lastDateOfMonth = intval(date("d", strtotime($lastDateOfMonth)));
    if ($mes == 12){
         $mes_siguiente =  str_pad(1, 2, '0', STR_PAD_LEFT);
         $anio_siguiente = $anio + 1;
         $lastDateOfNextMonth = date("Y-m-t", strtotime("$dia-$mes_siguiente-$anio_siguiente"));
         $lastDateOfNextMonth = intval(date("d", strtotime($lastDateOfNextMonth)));
    } else {
        $mes_siguiente_calculo = $mes + 1;
        $mes_siguiente = str_pad($mes_siguiente_calculo, 2, '0', STR_PAD_LEFT);
        $lastDateOfNextMonth = date("Y-m-t", strtotime("01-$mes_siguiente-$anio"));
        $lastDateOfNextMonth = intval(date("d", strtotime($lastDateOfNextMonth)));
    }
    $mes_siguiente_siguiente = $mes + 2;
    $mes_siguiente_siguiente = str_pad($mes_siguiente_siguiente, 2, '0', STR_PAD_LEFT);
    $fecha_primera = "";
    $fecha_segunda = "";
    // Si la fecha es del 1 al 9 
    // Si la fecha es del 10 al 19
    // Si la fecha es del 20 al lastDateOfMonth  
    if($dia >= 1 && $dia <= 5){    
        $fecha_primera = "15-".$mes."-".$anio;
        $fecha_segunda = "01-".$mes_siguiente."-".(($mes == 12)? $anio_siguiente:$anio);  

        $dia_fecha_primera = saber_dia(date("Y-m-d", strtotime($fecha_primera)));
        $dia_fecha_segunda = saber_dia(date("Y-m-d", strtotime($fecha_segunda)));
        if($dia_fecha_primera == "Domingo"){
            $fecha_primera = "16-".$mes."-".$anio;
        } else if($dia_fecha_primera == "Sábado"){   
            $fecha_primera = '14-'.$mes."-".$anio;            
        } 
        
        if($dia_fecha_segunda == "Domingo"){
            $fecha_segunda = "02-".$mes_siguiente."-".(($mes == 12)? $anio_siguiente:$anio); 
        } else if($dia_fecha_segunda == "Sábado"){   
            $fecha_segunda = ($lastDateOfMonth-1)."-".$mes."-".$anio;          
        } 
    } else if ($dia >= 6 && $dia <= 19){
        $fecha_primera = "01-".$mes_siguiente."-".(($mes == 12)? $anio_siguiente:$anio); 
        $fecha_segunda = "15-".$mes_siguiente."-".(($mes == 12)? $anio_siguiente:$anio); 
        $dia_fecha_primera = saber_dia(date("Y-m-d", strtotime($fecha_primera)));
        $dia_fecha_segunda = saber_dia(date("Y-m-d", strtotime($fecha_segunda)));
        if($dia_fecha_primera == "Domingo"){
            $fecha_primera = "02-".$mes_siguiente."-".$anio;
        } else if($dia_fecha_primera == "Sábado"){   
            $fecha_primera = ($lastDateOfMonth-1)."-".$mes."-".$anio;           
        } 
        
        if($dia_fecha_segunda == "Domingo"){
            $fecha_segunda = "16-".$mes_siguiente."-".(($mes == 12)? $anio_siguiente:$anio);
        } else if($dia_fecha_segunda == "Sábado"){   
            $fecha_segunda = "14-".$mes_siguiente."-".(($mes == 12)? $anio_siguiente:$anio);            
        } 
       
    } else if ($dia >= 20 && $dia <= $lastDateOfMonth){
        /*if ($mes == 12){
            $mes_siguiente =  str_pad(1, 2, '0', STR_PAD_LEFT);
            $anio = $anio + 1;
            $lastDateOfNextMonth = date("Y-m-t", strtotime("$dia-$mes_siguiente-$anio"));
            $lastDateOfNextMonth = intval(date("d", strtotime($lastDateOfNextMonth)));
        } else {
            $mes_siguiente_calculo = $mes + 1;
            $mes_siguiente = str_pad($mes_siguiente_calculo, 2, '0', STR_PAD_LEFT);
            $lastDateOfNextMonth = date("Y-m-t", strtotime("01-$mes_siguiente-$anio"));
            $lastDateOfNextMonth = intval(date("d", strtotime($lastDateOfNextMonth)));
        }*/
        /*
        $fecha_primera = "15-".$mes_siguiente."-".(($mes == 12)? $anio_siguiente:$anio);
        $dia_fecha_primera = saber_dia(date("Y-m-d", strtotime($fecha_primera)));
        if($dia_fecha_primera == "Domingo"){
            $fecha_primera = "16-".$mes_siguiente."-".(($mes == 12)? $anio_siguiente:$anio);
        } else if($dia_fecha_primera == "Sábado"){   
            $fecha_primera = "14-".$mes_siguiente."-".(($mes == 12)? $anio_siguiente:$anio);            
        } 
        switch ($mes) {
        
            case 11:
                $mes_siguiente_siguiente = '01';
                $anio = $anio +1;
                break;
            case 12:
                $mes_siguiente_siguiente = '02';
                $anio = $anio +1;
                break;
        }
        $fecha_segunda = "01-".$mes_siguiente_siguiente."-".$anio;
        $dia_fecha_segunda = saber_dia(date("Y-m-d", strtotime($fecha_segunda)));
        if($dia_fecha_segunda == "Domingo"){
            $fecha_segunda = "02-".$mes_siguiente_siguiente."-".$anio;
        } else if($dia_fecha_segunda == "Sábado"){   
            $fecha_segunda = ($lastDateOfNextMonth-1)."-".$mes_siguiente."-".$anio;            
        } 
    }    */
    $fecha_primera= $fechas_venciento['primer_vencimiento'];
    $fecha_segunda= $fechas_venciento['segundo_vencimiento'];
    $disabled = ""; 
    //$diasReales = date_diff_days(date('Y-m-d'), $desembolso['fecha_pago_inicial']);
?>

    
    <?php if(!empty($credito)){
        $disabled = "disabled";
    }
    ?>
    <div class="col-md-4 col-sm-6" <?php echo $estilo_div ?>>
        <div id="" class="box box-info small">
            <div class="box-header with-border">
                <h6 class="box-title pull-center"><small><strong>Confirmación desembolso</strong></small></h6>
            </div>
            <div class="box-body grid-striped">
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <div class="pull-right" style=""> Solicitado</div>
                    </div>
                    <div class="col-md-4">
                        <div class="">
                            <strong><?php echo isset($desembolso['capital_solicitado'])?"$".number_format($desembolso['capital_solicitado'],0,",","."):'' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control input-sm" <?= $disabled ?> id="solicitado_nuevo">
                                <option value="">Seleccione</option>
                                <?php if(!empty($offer)): ?>
                                    <?php foreach($offer['montos_parciales'] as $key => $monto_parcial): ?>
                                        <option value="<?=$monto_parcial?>"><?= number_format($monto_parcial, 0,",",".") ?></option>
                                    <?php endforeach; ?>
                                <?php endif;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <div class="pull-right" style=""> Plazo</div>
                    </div>
                    <div class="col-md-4">
                        <div class="">
                            <strong id ="plazo_anterior" data-plazo-anterior = "<?= isset($desembolso['plazo'])?$desembolso['plazo']:''?>"><?php echo isset($desembolso['plazo'])?$desembolso['plazo']:'' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control input-sm" <?= $disabled ?> id="plazo_nuevo">
                                <option value="">Seleccione</option>
                                <?php if(!empty($offer)): ?>
                                    <?php foreach($offer['plazos'] as $key => $plazo): ?>
                                        <option value="<?= $plazo ?>" ><?=$plazo?></option>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <?php $v_cuota = ($desembolso['plazo']!= 0) ? $desembolso['total_devolver'] / $desembolso['plazo'] : 0?>
                        <div class="pull-right" style=""> Valor Cuota</div>
                    </div>
                    <div class="col-md-4">
                        <div class="">
                            <strong><?php echo isset($v_cuota)?"$".number_format($v_cuota,0,",","."):'' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4"> 
                        <div class="">
                            <strong><?php echo isset($v_cuota)?"$".number_format($v_cuota,0,",","."):'' ?></strong>  
                        </div>  
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <div class="pull-right" style="">1° Vencimiento</div>
                    </div>
                    <div class="col-md-4">
                        <div class="analisis_col_der">
                            <strong><?php echo isset($desembolso['fecha_pago_inicial'])?date("d-m-Y", strtotime($desembolso['fecha_pago_inicial'])):'' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">                            
                            <select class="form-control input-sm" <?= $disabled ?> id="fecha_nueva" style="padding:0px">
                                <option value="">Seleccione</option>
                                <option value="<?php echo date("Y-m-d H:i:s", strtotime($fecha_primera))?>"> <?php echo isset($fecha_primera)?$fecha_primera : "" ?></option>
                                <option value="<?php echo date("Y-m-d H:i:s", strtotime($fecha_segunda)) ?>"> <?php echo isset($fecha_segunda)?$fecha_segunda : "" ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <div class="pull-right" style="">Días</div>
                    </div>
                    <div class="col-md-4">
                        <div class="analisis_col_der">
                            <strong><?= $desembolso['dias'] ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4" id ="calculo_dias">
                        
                    </div>
                </div>
                <?php if(empty($credito)):?>
                    <div class="col-md-6">
                        <div class="pull-left" id="recalcular_message" ><span class="text-success" ></span></div>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-success pull-right" id = "btnRecalcular" data-id_solicitud ='<?= isset($condition['id_solicitud'])?$condition['id_solicitud'] : "" ?>'>RECALCULAR</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php 
if(!empty($credito) && isset($credito['fecha_otorgamiento'])):
    $diffReal = date_diff_days($credito['fecha_otorgamiento'], $desembolso['fecha_pago_inicial']);
?>
    <div class="col-md-4 col-sm-6" id = "credito-box" <?php echo $estilo_div ?>>
        <div id="" class="box box-info small">
            <div class="box-header with-border">
                <h6 class="box-title pull-center"><small><strong>Credito desembolso</strong></small></h6>
            </div>
            <div class="box-body grid-striped">
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <div class="pull-right" style=""> Solicitado</div>
                    </div>
                    <div class="col-md-4">
                        <div class="analisis_col_der">
                            <strong><?php echo isset($desembolso['capital_solicitado'])?"$".number_format($desembolso['capital_solicitado'],0,",","."):'' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control input-sm" id="dc_solicitado_nuevo">
                                <option value="">Seleccione</option>
                                <?php if(!empty($offer)): ?>
                                    <?php foreach($offer['montos_parciales'] as $key => $monto_parcial): ?>
                                        <option value="<?=$monto_parcial?>"><?= number_format($monto_parcial, 0,",",".") ?></option>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <div class="pull-right" style=""> Plazo</div>
                    </div>
                    <div class="col-md-4">
                        <div class="analisis_col_der">
                            <strong id ="dc_plazo_anterior" data-dc-plazo-anterior = "<?= isset($desembolso['plazo'])?$desembolso['plazo']:''?>"><?php echo isset($desembolso['plazo'])?$desembolso['plazo']:'' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-control input-sm" id="dc_plazo_nuevo">
                                <option value="">Seleccione</option>
                                <?php if(!empty($offer)): ?>
                                    <?php foreach($offer['plazos'] as $key => $plazo): ?>
                                        <option value="<?= $plazo ?>" ><?=$plazo?></option>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <?php $v_cuota = ($desembolso['plazo'] != 0)?$desembolso['total_devolver'] / $desembolso['plazo'] : 0 ?>
                        <div class="pull-right" style=""> Valor Cuota</div>
                    </div>
                    <div class="col-md-4">
                        <div class="analisis_col_der">
                            <strong><?php echo isset($v_cuota)?"$".number_format($v_cuota,0,",","."):'' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4"> 
                        <strong><?php echo isset($v_cuota)?"$".number_format($v_cuota,0,",","."):'' ?></strong>    
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <div class="pull-right" style="">1° Vencimiento</div>
                    </div>
                    <div class="col-md-4">
                        <div class="analisis_col_der" data-fecha-inicial-actual="<?=$desembolso['fecha_pago_inicial']?>" id="data_fecha_inicial">
                            <strong><?php echo isset($desembolso['fecha_pago_inicial'])?date("d-m-Y", strtotime($desembolso['fecha_pago_inicial'])):'' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="date" class="form-control input-sm col-md-10" id="dc_fecha_nueva" style="padding:0px">
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <div class="pull-right" style="">Otorgamiento</div>
                    </div>
                    <div class="col-md-4">
                        <div class="analisis_col_der" data-fecha-otorg-actual="<?=$credito['fecha_otorgamiento']?>" id="data_fecha_otorgamiento">
                            <strong><?php echo isset($credito['fecha_otorgamiento'])?date("d-m-Y", strtotime($credito['fecha_otorgamiento'])):'' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="date" class="form-control input-sm" id="dc_fecha_otorgamiento">
                        </div>
                    </div>
                </div>
                <div class="row css-table-row">
                    <div class="col-md-4">
                        <div class="pull-right" style="">Días</div>
                    </div>
                    <div class="col-md-4">
                        <div class="analisis_col_der">
                            <strong class="hide"><?= $desembolso['dias'] ?></strong>
                            <strong><?= $diffReal ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4" id="dc_calculo_dias">
                        
                    </div>
                </div>
                <br>
                <div class="col-md-6">
                    <div class="pull-left" id="recalcular_message" ><span class="text-success" ></span></div>
                </div>
                <div class="col-md-6">
                   <?php if($credito['recalculable']): ?>
                    <button class="btn btn-success pull-right" id = "btnRecalcularDetalle" data-id_solicitud ='<?= isset($condition['id_solicitud'])?$condition['id_solicitud'] : "" ?>'>RECALCULAR</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>
