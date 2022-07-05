
<div id="box_client_data" class="box box-info" style="height: 20px">
    <div class="box-header" id="titulo" style="background-color: #D7BDE2;height: 30px;">
        <div class="row">
            <div class="col-md-11" style="margin-top: -10px;">
                <div class="col-md-1" style="padding: 0px;">
                    <h4 class="" style="font-size: 10px;">
                        <?php if(!empty($solicitude['respuesta_analisis'])): ?>
                            <?php if($solicitude['respuesta_analisis'] == 'APROBADO'): ?>
                                <i style="margin-right: 8px; color: green" class="fa fa-check"></i>
                            <?php elseif($solicitude['respuesta_analisis'] == 'RECHAZADO'): ?>
                                <i style="margin-right: 8px; color: red" class="fa fa-times-circle"></i>
                            <?php endif ?>
                        <?php endif ?>
                        Buro
                    </h4>
                </div>
                <div class="col-md-2" style="padding: 0px;">
                    <h4 class="" style="font-size: 10px;">
                        <?php if(!empty($solicitude['validacion_telefono'])): ?>
                            <?php if($solicitude['validacion_telefono'] == 1): ?>
                                <i style="margin-right: 8px; color: green" class="fa fa-check"></i>
                            <?php else: ?>
                                <i style="margin-right: 8px; color: red" class="fa fa-times-circle"></i>
                            <?php endif ?>
                        <?php endif ?>
                        Teléfono
                    </h4>
                </div>
                <div class="col-md-2" style="margin-left: -5%;padding: 0px;">
                    <h4 class="" style="font-size: 10px;" >
                        <?php if(!empty($solicitude['validacion_mail'])): ?>
                            <?php if($solicitude['validacion_mail'] == 1): ?>
                                <i style="margin-right: 8px; color: green" class="fa fa-check"></i>
                            <?php else: ?>
                                <i style="margin-right: 8px; color: red" class="fa fa-times-circle"></i>
                            <?php endif ?>
                        <?php endif ?>
                        Email
                    </h4>
                </div>
                <div class="col-md-1" style="padding: 0px;margin-left: -5%;">
                    <h4 class="" style="font-size: 10px;">
                        <?php if(isset($bank['respuesta']) && !empty($bank['respuesta'])): ?>
                            <?php if($bank['respuesta']=='ACEPTADA'): ?>
                                <i style="margin-right: 8px; color: green" class="fa fa-check"></i>
                            <?php else: ?>
                                <i style="margin-right: 8px; color: red" class="fa fa-times-circle"></i>
                            <?php endif ?>
                        <?php endif ?>
                        Cuenta
                    </h4>
                </div>
                <div class="col-md-1" style="padding: 0px;">
                    <h4 class="" style="font-size: 10px;">
                        <?php if(isset($solicitude['reto_resultado']) && !empty($solicitude['reto_resultado'])): ?>
                            <?php if($solicitude['reto_resultado'] == 'CORRECTA'): ?>
                                <i style="margin-right: 8px; color: green" class="fa fa-check"></i>
                            <?php else: ?>
                                <i style="margin-right: 8px; color: red" class="fa fa-times-circle"></i>
                            <?php endif ?>
                        <?php endif ?>
                        Reto
                    </h4>
                </div>
                <div class="col-md-6" style="padding: 0px;">
                    <span class="" style="font-size: 1.5em;color: red" >
                        <?php echo $error_jumio?>
                    </span>
                </div>

                <!-- <div class="col-md-2" style="padding: 0px;">
                    <h4 class="" style="font-size: 10px;">
                        <?php //echo $icono; ?> WhatsApp: <?php // echo $whatsapp ?>
                    </h4>
                </div> -->
            </div>
            <div class="col-md-2">
                <table>
                    <tr>
                        <!-- <td width="68%" align="right">
                            <input type="date" id="fecha_agenda" value="<?= date('Y-m-d');?>">
                            <input type="time" id="hora_agenda" value="">
                            &nbsp;
                        </td>
                        <td width="6%">
                            <a href="#" class="btn btn-xs btn-primary" title="Agendar solicitud para llamada posterior" style="font-size: 16px;" onclick="agendar(<?= $solicitude['id']; ?>,<?= $this->session->userdata('idoperador'); ?>,'<?=$solicitude['nombres'];?>','<?=$solicitude['apellidos'];?>')"><i class="fa fa-calendar-check-o"></i>

                            </a>
                            </td> -->
                    </tr>
                </table>
            </div>
           
        </div>
    </div><!-- end box-header -->
</div><!-- end box-info -->