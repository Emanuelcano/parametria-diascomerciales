
<?php if(isset($datos_solicitud)){ ?>
<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
    <section class="content">

        <div class="col-lg-12" id="main" style="display: block;">

          <span class="hidden-xs">

</span>

<?php //echo base_url()."assets/template/dist/img/loading.gif"; ?>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">


<div id="dashboard_principal" style="display: block; background: #FFFFFF;">

<div class="row">
    <div class="col-md-4">

        <div class="box box-info">

            <div class="box-header with-border" id="titulo">
                <h6 class="box-title"><small><strong>Datos Cliente</strong></small>&nbsp;&nbsp;&nbsp;&nbsp;<small>Buros:</small>
                    <?php
                    if(!empty($datos_solicitud[0]['respuesta_analisis'])) {
                        if($datos_solicitud[0]['respuesta_analisis'] == 'APROBADO') {?>
                            <strong style="font-size: 16px; color: green;">
                        <?php }else{?>
                            <strong style="font-size: 16px; color: red;">
                        <?php }
                            echo "[".$datos_solicitud[0]['respuesta_analisis']."]"; }?></strong>&nbsp;&nbsp;<small>Estado:</small>
                    <?php
                    if(!empty($datos_solicitud[0]['estado'])) {
                        if($datos_solicitud[0]['estado'] != 'RECHAZADO') {?>
                            <strong style="font-size: 16px; color: blue;">
                        <?php }else{?>
                            <strong style="font-size: 16px; color: red;">
                        <?php }
                            echo "[".$datos_solicitud[0]['estado']."]"; }?></strong></h6>
            </div>

            <table width="100%" class="table table-striped table=hover display">
                <tbody>

                    <tr>
                        <td class="analisis_col_izq" width="30%" style="text-align: right;">Tipo de Documento</td>
                        <td class="analisis_col_der"><strong><?= $datos_solicitud[0]['nombre_tipoDocumento'] ?></strong></td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Numero Documento</td>
                        <td class="analisis_col_der"><strong><?= number_format($datos_solicitud[0]['documento'],0,',','.') ?></strong></td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Fecha Expedición</td>
                        <td class="analisis_col_der"><strong><?= date("d-m-Y", strtotime($datos_solicitud[0]['fecha_expedicion'])) ?></strong></td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Nombres:</td>
                        <td class="analisis_col_der">
                            <strong><?= $datos_solicitud[0]['nombres'] ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Apellidos</td>
                        <td class="analisis_col_der">
                            <strong><?= $datos_solicitud[0]['apellidos'] ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Telefono</td>
                        <td class="analisis_col_der"><strong><?= $datos_solicitud[0]['telefono'] ?></strong></td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Registro</td>
                        <td class="analisis_col_der">
                            <strong><?= $datos_solicitud[0]['canal'] ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Mail</td>
                        <td class="analisis_col_der">
                            <strong><?= $datos_solicitud[0]['email'] ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Situación Laboral</td>
                        <td class="analisis_col_der">
                            <strong><?= $datos_solicitud[0]['nombre_situacion'] ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Sueldo Declarado $</td>
                        <td class="analisis_col_der">
                            <strong><?= number_format($datos_solicitud[0]['ingreso_mensual'],2,",",".") ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Banco</td>
                        <td class="analisis_col_der">
                            <strong><?php if(isset($datos_bancarios[0])){ echo $datos_bancarios[0]['Nombre_Banco'];}  ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Tipo de cuenta</td>
                        <td class="analisis_col_der">
                            <strong><?php if(isset($datos_bancarios[0])){ echo $datos_bancarios[0]['Nombre_TipoCuenta'];} ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Numero de cuenta</td>
                        <td class="analisis_col_der">
                            <strong><?php if(isset($datos_bancarios[0])){
                                $long = strlen($datos_bancarios[0]['numero_cuenta']);
                                $repetir = $long-4;
                                echo substr($datos_bancarios[0]['numero_cuenta'],0,1).str_repeat('*', $repetir).substr($datos_bancarios[0]['numero_cuenta'],-3);
                            } ?></strong>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
    <?php
        $data['datos_track_hidden'] = [
            "id_solicitud" => $datos_solicitud[0]['id'],
            "operador_asignado" => $datos_solicitud[0]['operador_asignado']
        ];
    ?>
    <div class="col-md-4" style="max-height: 583px;">
        <?php $this->load->view('gestion/box_tracker',$data['datos_track_hidden']);?>
    </div>
    <div class="col-md-4">
        <div class="box box-info col-md-4">

            <div class="box-header with-border" id="titulo">
                <h6 class="box-title"><small><strong>Wathsapp</strong></small></h6>
            </div>
            <textarea id="comentarioValidacionV" cols="80" rows="25" style="width: 100%; font-size: 14px; background: #91D284; border-radius: 5px;">

            </textarea>
            <textarea cols="75" rows="1" id="comentarioValidacionR" placeholder="Track Gestion" style="font-size: 12px; width: 95%;">
            </textarea>&nbsp;<i style="font-size: 20px; margin-right: 8px;" class="fa fa-send-o"></i>

        </div>
    </div>
</div>
<?php $this->load->view('gestion/box_review_buttons',$indicadores[0]);?>


    <?php
        if(isset($indicadores[0])){
            $this->load->view('gestion/indicadores',$solicitude[0]);
        }
    ?>

<div class="row">

    <div class="col-md-4">
        <div class="box box-info">

            <?php if(isset($solicitud_condicion[0])): ?>
                <div class="col-md-6">
                    <div class="box-header with-border" id="titulo">
                        <h6 class="box-title"><small><strong>Solicitud Inicial</strong></small></h6>
                    </div>
                    <table width="50%" class="table table-striped table=hover display">
                        <tbody>
                            <tr>
                                <td class="analisis_col_izq" width="60%" style="text-align: right;">Solicitado</td>
                                <td class="analisis_col_der">
                                    <strong><?= number_format($solicitud_condicion[0]['capital_solicitado'],0,",",".") ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="analisis_col_izq" style="text-align: right;">Plazo</td>
                                <td class="analisis_col_der">
                                    <strong><?= $solicitud_condicion[0]['plazo'] ?></strong></td>
                            </tr>
                            <tr>
                                <?php 
                                $plazo_aux = ($solicitud_condicion[0]['plazo'] > 0)? $solicitud_condicion[0]['plazo'] : 1 ;
                                
                                $v_cuota = $solicitud_condicion[0]['total_devolver'] / $plazo_aux ;?>
                                <td class="analisis_col_izq" style="text-align: right;">Valor Cuota</td>
                                <td class="analisis_col_der">
                                    <strong><?= number_format($v_cuota,2,",",".") ?></strong></td>
                            </tr>
                            <tr>
                                <td class="analisis_col_izq" style="text-align: right;">Fecha Primer Vencimiento</td>
                                <td class="analisis_col_der">
                                    <strong><?= date("d-m-Y", strtotime($solicitud_condicion[0]['fecha_pago_inicial'])) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if(isset($solicitud_desembolso[0])):?>
                <div class="col-md-6">
                    <div class="box-header with-border" id="titulo">
                        <h6 class="box-title"><small><strong>Confirmación desembolso</strong></small></h6>
                    </div>
                    <table width="50%" class="table table-striped table=hover display">
                        <tbody>
                            <tr>
                                <td class="analisis_col_izq" width="60%" style="text-align: right;">Solicitado</td>
                                <td class="analisis_col_der">
                                    <strong><?= number_format($solicitud_desembolso[0]['capital_solicitado'],2,",",".") ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="analisis_col_izq" style="text-align: right;">Plazo</td>
                                <td class="analisis_col_der">
                                    <strong><?= $solicitud_desembolso[0]['plazo'] ?></strong></td>
                            </tr>
                            <tr>
                                <?php $v_cuota = $solicitud_desembolso[0]['total_devolver'] / $solicitud_desembolso[0]['plazo'] ?>
                                <td class="analisis_col_izq" style="text-align: right;">Valor Cuota</td>
                                <td class="analisis_col_der">
                                    <strong><?= number_format($v_cuota,2,",",".") ?></strong></td>
                            </tr>
                            <tr>
                                <td class="analisis_col_izq" style="text-align: right;">Fecha Primer Vencimiento</td>
                                <td class="analisis_col_der">
                                    <strong><?= date("d-m-Y", strtotime($solicitud_desembolso[0]['fecha_pago_inicial'])) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-4">

        <div class="box box-info">

            <div class="box-header with-border" id="titulo">
                <h6 class="box-title"><small><strong>Referencia familiar</strong></small></h6>
            </div>
            <?php if(isset($referencia_familiar)){ ?>
            <table class="table table-striped table=hover display" width="100%">
                <tbody>
                    <tr>
                        <td class="analisis_col_izq" width="30%" style="text-align: right;">Nombres y apellidos</td>
                        <td class="analisis_col_der">
                            <strong><?= $referencia_familiar['nombres_apellidos'] ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Telefono</td>
                        <td class="analisis_col_der"><strong><?= $referencia_familiar['telefono'] ?></strong></td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Parentesco</td>
                        <td class="analisis_col_der">
                            <strong><?= $referencia_familiar['Nombre_Parentesco'] ?></strong>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php } ?>
        </div>

    </div>

    <div class="col-md-4">

        <div class="box box-info">

            <div class="box-header with-border" id="titulo">
                <h6 class="box-title"><small><strong>Referencia personal</strong></small></h6>
            </div>
            <?php if(isset($referencia_personal)){ ?>
            <table class="table table-striped table=hover display" width="100%">
                <tbody>

                    <tr>
                        <td class="analisis_col_izq" width="30%" style="text-align: right;">Nombres y apellidos:</td>
                        <td class="analisis_col_der">
                            <strong><?= $referencia_personal['nombres_apellidos'] ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Telefono</td>
                        <td class="analisis_col_der">
                            <strong><?= $referencia_personal['telefono'] ?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="analisis_col_izq" style="text-align: right;">Parentesco</td>
                        <td class="analisis_col_der">
                            <strong><?= $referencia_personal['Nombre_Parentesco'] ?></strong>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="height:300px;">
        <?php $this->load->view('gestion/box_galery');?>
    </div>
</div>
<!--     <div class="col-md-12">

    <div class="box box-info">

        <div class="box-header with-border" id="titulo">
            <h6 class="box-title"><small><strong>Cuotas</strong></small></h6>
        </div>

        <table data-page-length='10' align="center" id="tp_Beneficiarios" class="table table-striped table=hover display" width="100%">
            <thead>
              <tr class="info">
                <th style="width: 10%; padding: 0px; padding-left: 10px;">Numero</th>
                <th style="width: 10%; padding: 0px; padding-left: 10px;">Fecha de Vencimiento</th>
                <th style="width: 10%; padding: 0px; padding-left: 10px;">Monto a Pagar</th>
                <th style="width: 10%; padding: 0px; padding-left: 10px;">Fecha de Pago</th>
                <th style="width: 10%; padding: 0px; padding-left: 10px;">Monto Pagado</th>
                <th style="width: 10%; padding: 0px; padding-left: 10px;">Descuento</th>
                <th style="width: 10%; padding: 0px; padding-left: 10px;">Pendiente Pago</th>
                <th style="width: 10%; padding: 0px; padding-left: 10px;">Estado</th>
                <th style="width: 10%; padding: 0px; padding-left: 10px;">Forma Pago</th>
                <th style="width: 10%; padding: 0px;">&nbsp;</th>
              </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 0px; font-size: 14px; text-align: center; vertical-align: middle;">1</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle;">08-11-2019</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">182.500</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">&nbsp;</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">&nbsp;</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">&nbsp;</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">182.500</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">VIGENTE</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">&nbsp;</td>

                    <td style="height: 5px; padding: 4px;" align="center">
                      <a class="btn btn-xs bg-navy" title="Pagar Cuota"
                        onclick='$("#main").css("display","block");'>
                        <i class="fa fa-money" ></i>
                      </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0px; font-size: 14px; text-align: center; vertical-align: middle;">2</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle;">08-12-2019</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">182.500</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">&nbsp;</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">&nbsp;</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">&nbsp;</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">182.500</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">VIGENTE</td>

                    <td style="padding: 0px; font-size: 14px; vertical-align: middle; text-align: center;">&nbsp;</td>

                    <td style="height: 5px; padding: 4px;" align="center">
                      <a class="btn btn-xs bg-navy" title="Pagar Cuota"
                        onclick='$("#main").css("display","block");'>
                        <i class="fa fa-money" ></i>
                      </a>
                    </td>
                </tr>

            </tbody>
        </table>

    </div>
</div> -->
</div>
<?php } ?>