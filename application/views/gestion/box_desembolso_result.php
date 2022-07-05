<div class="box box-bancos box-info">
    <div class="box-header with-border col-md-6" id="titulo">
        <h6 class="box-title"><small><strong>Estado Desembolso</strong></small></h6>
    </div>
    <div class="box-header with-border col-md-6" id="titulo">
        <h6 class="box-title col-md-12" ><small class="col-md-12"><strong class="col-md-12 text-right" style="color:red;">
        <?php
                if(($pagado_txt[0]['origen_pago'] == 'BBVA') 
                || ($pagado_txt[0]['origen_pago'] == 'Bancolombia') 
                || ($pagado_txt[0]['origen_pago'] == 'Santander')){
                  echo 'TRANSFERENCIA BANCARIA';   
                } 
                if(($pagado_txt[0]['origen_pago'] == 'Efecty') 
                && (($pagado_txt[0]['pagado'] == 0) 
                || ($pagado_txt[0]['pagado'] == 1))) {
                    echo 'EFECTY';   
                }
        ?>


        </strong></small></h6>
    </div>
    <div class="box-body">
        <?php // var_dump($pagado_txt);?>
        <div class="row">
            <?php
                
                if(!empty($pagado_txt)){
                    $hoy = date("Y-m-d H:i:s");
                    $fecha = (!empty($verificacion_desembolso))? $verificacion_desembolso[0]->fecha_hora_solicitud:$pagado_txt[0]['fecha_procesado'];
                    $horas = round((strtotime($hoy) - strtotime($fecha))/(60*60));
                    
                    ?>

            <div class="<?= ($horas >= 48 && (!isset($verificacion_desembolso[0]) || (isset($verificacion_desembolso[0]) && !is_null($verificacion_desembolso[0]->respuesta))))? 'col-md-10':'col-md-12' ?>">
                <h5 style="margin:0px;"><?php if($pagado_txt[0]['origen_pago'] != 'Efecty') {echo '<b>Banco: </b>' ;} ?><?= $pagado_txt[0]['origen_pago'] ?> | <b>Fecha: </b> <?= date("d-m-Y", strtotime($pagado_txt[0]['fecha_procesado'])); ?>  | <b>Hora: </b> <?= date("H:i", strtotime($pagado_txt[0]['fecha_procesado'])); ?> | <b>Respuesta: </b> 
                <?php if(($pagado_txt[0]['pagado'] == 0) && ($pagado_txt[0]['origen_pago'] == 'Efecty')) {echo 'DISPONIBLE EN OFICINAS Efecty';} ?> 
                <?php if(($pagado_txt[0]['pagado'] == 1) && ($pagado_txt[0]['origen_pago'] == 'Efecty')) {echo 'CLIENTE RETIRO EL DINERO';} ?> 
                <?php if(($pagado_txt[0]['pagado'] == 0) && ($pagado_txt[0]['origen_pago'] != 'Efecty')) {echo 'ENVIADO AL BANCO';} ?>
                <?php if((($pagado_txt[0]['pagado'] == 1) || ($pagado_txt[0]['pagado'] == 2)) && ($pagado_txt[0]['origen_pago'] != 'Efecty')) {echo 'TRANSFERENCIA REALIZADA';} ?>
                <?php if(($pagado_txt[0]['pagado'] == 3) && ($pagado_txt[0]['origen_pago'] != 'Efecty')) {echo 'REENVIADO A TRANSFERIR';} ?>
                </h5>
            </div>
            
            
            <?php

                if( ($horas >= 48 || $solicitude['estado'] =="PAGADO") 
                && (!isset($verificacion_desembolso[0]) 
                || (isset($verificacion_desembolso[0]) 
                && !is_null($verificacion_desembolso[0]->respuesta)))
                && ($pagado_txt[0]['origen_pago'] != 'Efecty')){ ?>
                    <div class="col-md-2">
                        <a class="btn btn-success btn-xs" id="validar-desembolso" onclick="validarDesembolso(<?= $pagado_txt[0]['id_solicitud']?>,<?= $this->session->userdata('idoperador')?> );">VALIDAR</a>
                    </div>
                    
                    <?php } else if(isset($verificacion_desembolso[0])){ ?>
                        <div class="col-md-12">
                            <p style="background:#fff7e1;">Solicitud de validacion enviada. En espera de respuesta</p>
                        </div>
                        <?php } 
                     
                 }
                ?>

            <div class="col-md-12">
                <?php
                    if(isset($verificacion_desembolso[0]) && !is_null($verificacion_desembolso[0]->respuesta)){
                        echo '<h5 style="margin:0px;background: #dff0d8"><b>Respuesta: </b> '.$verificacion_desembolso[0]->respuesta.' '.((is_null($verificacion_desembolso[0]->comprobante))? '':' <b> | Comprobante: </b> <a href="'.base_url($verificacion_desembolso[0]->comprobante).'" target="_blank"> comprobante</a>').'</h5>'; 
                    } 
                ?>
            </div>
           
        </div>
        
    </div>
    
    
</div>
