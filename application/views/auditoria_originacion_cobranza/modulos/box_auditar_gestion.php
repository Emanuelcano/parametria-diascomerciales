<link rel="stylesheet" href="../assets/css/custom-gestion.css">
<style type="text/css">
    .box {
        margin-bottom: 4px;        
    }    
    section.box_auditoria {
        padding: 5px;
        max-height:150px!important
    }
    #titulo {
        background-color: #e0dff5;
        box-shadow: 0px 3px 3px -9px #888888;
        /* z-index: 1; */
        height: 26px;
        padding-top: 0px;
        /* border-top: 3px solid #00c0ef; */
        /* margin-left: 2%; */
        /* width: 96%; */
    }
    #form_auditoria {
        margin-bottom: 2%;
        
    }

    .contenedor::-webkit-scrollbar {
        -webkit-appearance: none;
    }
    .contenedor {
        border-bottom: 1px solid #d2d6de;
    }
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
</style>
<input id="client" type="hidden" data-number_doc = "<?php echo $solicitude[0]['documento']; ?>">
<input id="solicitud" type="hidden" data-id_solicitud= "<?php echo $solicitude[0]['id']; ?>">

<input type="hidden" id="id_solicitud" data-tipo="<?php echo $solicitude[0]['tipo_solicitud'] ?>" data-status = "<?php echo $solicitude[0]['estado'] ?>" value="<?php echo $solicitude[0]['id']; ?>">
<input type="hidden" id="box_client_title" data-id_cliente="<?php echo $solicitude[0]['id_cliente'] ?>" data-status = "<?php echo $solicitude[0]['estado'] ?>" value="<?php echo $solicitude[0]['id']; ?>">
<div id="content_auditar">
    <section class="box_auditoria">
        <a id="close_llamado" href="#" title="Cerrar y continuar auditando otro llamado" class="pull-right" onclick="cerrarCasoAuditar();" style="position: absolute; right: 43px; z-index: 1000; top: 20px;">
                <i class="fa fa-close icon_close"></i>
        </a>
        <div class="row row-chat-track" style="height: 600px;"> 
        <!-- <div id="box_gestion_header" class="col-md-4" >
            
            
        </div> -->

        <div class="col-md-4 contenedor" style="padding-right: 5px; padding-left: 5px; overflow-x: hidden; overflow-y: auto; max-height: 730px;">
                <div id="box_client_data" class="box box-info">

                    <div class="box-body" style=" font-size: 12px;">
                        <div class="container-fluid grid-striped" style="margin-top: 5%;">
                            <div class="col-md-12" style="text-align: left;background-color: #e0dff5;height: 26px;padding-top:1%">
                                <?php 
                                    if ($solicitude[0]['id_credito']) {
                                        echo '<strong>INFO CLIENTE</strong>';
                                        } else {
                                            echo '<strong>INFO SOLICITANTE</strong>';
                                        }                                     
                                ?>
                                
                            </div>
                            <table class="table table-bordered table-responsive display">
                                <thead>
                                    <tr class="table-light">
                                        <th>SOLICITANTE</th>
                                        <th>DNI</th>
                                        <th>TELEFONO</th>
                                        <th>SITUACION LABORAL</th>
                                        

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="height: 38px">
                                        <td><?=$solicitude[0]['nombres'].' '.$solicitude[0]['apellidos'];?></td>    
                                        <td class='hd_center'><?=$solicitude[0]['documento'];?></td>
                                        <td class='hd_center'><?=$solicitude[0]['telefono'];?></td>
                                        <td class='hd_center'><?=$solicitude[0]['nombre_situacion'];?></td>

                                    </tr>
                                </tbody>
                            </table>

                            <div class="col-md-12" style="text-align: left;background-color: #e0dff5;height: 26px;padding-top:1%">
                                <strong>INFO SOLICITUD</strong>
                            </div>

                            <table class="table table-bordered table-responsive display">
                                <thead>
                                    <tr class="table-light">
                                        <th>NUMERO</th>
                                        <th>ALTA</th>
                                        <th>TIPO</th>
                                        <th>PASO</th>
                                        <th>MONTO SOLICITADO</th>
                                    
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="height: 38px">
                                        <td class='hd_center'id="id_solicitud" value="<?=$solicitude[0]['id'];?>"><?=$solicitude[0]['id'];?></td>
                                        <td class='hd_center'>
                                            <?php
                                                if (isset($solicitude[0]['fecha_alta'])) {
                                                    $date = new DateTime($solicitude[0]['fecha_alta']);
                                                    echo $date->format('d-m-Y H:i');
                                                } else 
                                                    echo '';
                                            ?>
                                        </td>
                                        <td><?=isset($solicitude[0]['tipo_solicitud'])?$solicitude[0]['tipo_solicitud']:''; ?>
                                        </td>
                                        
                                        
                                        <td class='hd_center'>
                                            <?=isset($solicitude[0]['paso'])?$solicitude[0]['paso'] . '' :''; ?>
                                            
                                        </td>
                                        <td class='hd_center'>$ <?=$solicitude[0]['valor_transaccion'];?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col-md-12" style="text-align: left;background-color: #e0dff5;height: 26px;padding-top:1%">
                                <strong>INFO CREDITO</strong>
                            </div>
                            <?php 
                            if (count($credito) <= 1) {
                                $tamano ="auto";
                            }else{
                                $tamano ="180px";
                            } ?>
                            <div style="width: 100%; height: <?php echo $tamano ?>; overflow: auto;">                                
                                <table class="table table-bordered table-responsive display">
                                    <thead >
                                        <tr class="table-light">
                                            <th>CREDITO</th>
                                            <th>FECHA OTORGAMIENTO</th>
                                            <th>MONTO OTORGADO</th>
                                            <th>ESTADO</th>
                                            <th>DIAS DE ATRASO</th>
    
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($credito as $key => $creditos) {?>
                                        <tr style="height: 38px">
                                            <td class='hd_center'><?=$creditos['id'] ? $creditos['id']  : '--';?></td>
                                            <td class='hd_center'>
                                                <?php
                                                
                                                    if (isset($creditos['id'])) {
                                                        $date = new DateTime($creditos['fecha_otorgamiento']);
                                                        echo $date->format('d-m-Y H:i');
                                                    } else 
                                                        echo '--';
                                                ?>
                                            <td class='hd_center'>$ <?php echo $creditos['id'] ? $creditos['monto_prestado'] : '--';?> </td>
                                            <td class='hd_center'> <?php 
                                                if ($creditos['id']) {
                                                    if ($creditos['estado'] == 'cancelado') {
                                                        echo '<i class="fa fa-ban" aria-hidden="true" title="Cancelado" style="color:red;font-size: 16px;text-align: center;padding-left: 22%;"></i>';
                                                    } else if ($creditos['estado'] == 'vigente') {
                                                        echo '<i class="fa  fa-thumbs-up" aria-hidden="true" title="Vigente" style="color:#1d9e74;font-size: 16px;text-align: center;padding-left: 22%;"></i>';
                                                    } else if ($creditos['estado'] == 'mora') {
                                                        echo '<i class="fa fa-thumbs-down" aria-hidden="true" title="Mora" style="color:red;font-size: 16px;text-align: center;padding-left: 22%;"></i>';
                                                    }  else if ($creditos['estado'] == 'anulado') {
                                                        echo '<i class="fa fa-times" aria-hidden="true" title="Anulado" style="color:red;font-size: 16px;text-align: center;padding-left: 22%;"></i>';
                                                    }
                                                } else {
                                                    echo '--';
                                                }
                                                
                                            
                                            ?> </td>
                                            <td class='hd_center' style="text-align: center border: 1px solid #000; background-color: #FFF;"> 
                                                <?php 
                                                    if ($creditos['id']) {
                                                        if ($creditos['dias_atraso'] == '') {
                                                            echo '--';
                                                        } else {
                                                            echo $creditos['dias_atraso'];
                                                        }
                                                    }
                                                ?> 
                                            </td>
                                            
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-md-12" style="text-align: left;background-color: #e0dff5;height: 26px;padding-top:1%">
                                <strong>INFO ACUERDOS</strong>
                            </div>
                            <div style="width: 100%; height: 160px; overflow: auto;">
                                <table class="table table-bordered table-responsive display">
                                    <thead>
                                        <tr class="table-light">
                                            <th>FECHA ACUERDO</th>
                                            <th>FECHA PAGO</th>
                                            <th>MONTO</th>
                                            <th>ESTADO</th>

                                        </tr>
                                    </thead>
                                    <?php if ($acuerdos_pago == 'Sin acuerdos') { ?>
                                        <tbody>
                                                <td class='hd_center' >Sin acuerdo</td>
                                                <td class='hd_center' >--</td>
                                                <td class='hd_center' >--</td>
                                                <td class='hd_center' >--</td>
                                                
                                        </tbody>
                                    <?php } else { ?>
                                        <?php foreach ($acuerdos_pago as $acuerdo) { ?>
                                            <tbody>
                                                
                                            
                                                    <td class='hd_center'><?=$acuerdo['fecha_hora'];?></td>
                                                    <td class='hd_center'><?=$acuerdo['fecha'];?></td>
                                                    <td class='hd_center'>$ <?=$acuerdo['monto'];?></td>
                                                    
                                                    <td class='hd_center'> <?php 
                                                        
                                                        if ($acuerdo['estado'] == 'anulado') {
                                                            echo '<i class="fa fa-times" aria-hidden="true" title="Anulado" style="color:red;font-size: 16px;text-align: center;padding-left: 22%;"></i>';
                                                        } else if ($acuerdo['estado'] == 'incumplido') {
                                                            echo '<i class="fa fa-thumbs-down" aria-hidden="true" title="Incumplido" style="color:red;font-size: 16px;text-align: center;padding-left: 22%;"></i>';
                                                        } else if ($acuerdo['estado'] == 'cumplido') {
                                                            echo '<i class="fa fa-thumbs-up" aria-hidden="true" title="Cumplido" style="color:#1d9e74;font-size: 16px;text-align: center;padding-left: 22%;"></i>';
                                                        }  else if ($acuerdo['estado'] == 'pendiente') {
                                                            echo '<i class="fa fa-spinner" aria-hidden="true" title="Pendiente" style="color:#1d9e74;font-size: 16px;text-align: center;padding-left: 22%;"></i>';
                                                        }
                                                    
                                                        
                                                    
                                                    ?> </td>
                                                    
                                                    
                                            </tbody>
                                        <?php } ?>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tracker" class="col-md-4 contenedor" style="height: 114%;padding-right: 5px; padding-left: 5px; overflow-x: hidden; overflow-y: auto;">
                <div id="box_tracker" class="box box-info" style="height: 90%;">
                    <div class="box-header with-border"></div><!-- end box-header -->
                    <div class="box-body contenedor"  style="overflow-y: auto; height: 66%">
                        <div class="tab-pane active" id="timeline" style="padding-top: 35%;">
                            <div class="loader" id="loader-6">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                            </div>
                        </div>
                    </div><!-- end box-body -->
                    <div class="box-footer" style="height: 31%">

                    </div><!-- end box-footer -->
                </div> <!-- end box-default -->
            </div>
            <div id="whatsapp" class="col-md-4" style="height: 114%;padding:0px; ">
                
                <div id="box_whatsapp" class="box box-info __chats_list_container" style="height: 600px;">
                    <div class="box-header with-border" id="titulo">
                
                    </div>
                    <div class="box-body"  style="overflow-y: auto; height: 66%">
                        <div class="tab-pane active" id="timeline" style="padding-top: 40%;">
                            <div class="loader" id="loader-6">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </div><!-- end box-body -->
                </div>

            </div>

            <div id="form_auditoria" class="col-md-12" style="background-color:#e9e9e9; border-top: 3px solid #00c0ef;border-bottom: 1px solid #d2d6de;">
                <?php
                        $this->load->view('auditoria_originacion_cobranza/modulos/box_form_auditar_llamado', [$parametros, $calificaciones, $solicitude, $tipo_operador]);
                    ?> 
            </div>

        </div>       
    </section>
</div>