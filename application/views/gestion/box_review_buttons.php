 <style type="text/css">
    .button-gestion{
                    font-size: 18px;
                    margin: 5px 0px;
                    width: 100%;
                    }
    .button-gestion > i{
                    margin-right: 8px;
                    }
</style>
<?php
    $id_operador   = $this->session->userdata('tipo_operador');
    $tipo_operador = $this->Solicitud_m->get_nombre_operador($id_operador);
?>
<div id="box_botones_gestion" class="box box-info">
    <input id="tipo_operador" type="hidden" value="<?php echo $tipo_operador[0]["descripcion"];?>">
    <input id="base_url" type="hidden" value="<?php echo base_url();?>">
    <input id="analysis_buro" type="hidden" value="<?php echo isset($solicitude['respuesta_analisis'])?$solicitude['respuesta_analisis']:''; ?>">
    <input id="solicitud_status" type="hidden" value="<?php echo isset($solicitude['estado'])?$solicitude['estado']:''; ?>">
    <input id="id_operador" type="hidden" value="<?php echo $this->session->userdata('idoperador'); ?>">   
    <input id="step" type="hidden" value="<?php echo isset($solicitude['paso'])?$solicitude['paso']: 0; ?>">

    <div class="box-body">
        <div class="container-fluid">  
            <div class="row">
            <?php
            foreach ($btn_revision as $key => $btn): ?>
                <?php if($btn['etiqueta'] == 'VERIFICADO') : ?>
                        <div class="col-xs-2">
                            <button id="verified" class="btn btn-xs button-gestion bg-<?php echo $btn['color']; ?>" title="<?php echo $btn['descripcion']; ?>" data-reference="VERIFICADO" data-type_gestion="<?php echo $btn['id']; ?>">
                                <i class="fa <?php echo $btn['fa_icon']; ?>"></i><?php echo $btn['etiqueta']; ?>
                            </button>
                        </div>
                    <?php endif; 
             
                    if($btn['etiqueta'] == 'VALIDADO') : ?>
                        <div class="col-xs-2">
                            <button id="validated" class="btn btn-xs button-gestion bg-<?php echo $btn['color']; ?> " title="<?php echo $btn['descripcion']; ?>" data-reference="VALIDADO" data-type_gestion="<?php echo $btn['id']; ?>">
                                <i class="fa <?php echo $btn['fa_icon']; ?>"></i><?php echo $btn['etiqueta']; ?>
                            </button>
                        </div>
                    <?php endif; ?>
                <?php if($btn['etiqueta'] == 'ENVIAR PAGARÉ') :?>
                    <?php if(!$solicitude['pagare_firmado'] && !$pagare_descargado): ?>
                            <div class="col-xs-2">
                                <button id="promissory" class="btn btn-xs bg-<?php echo $btn['color']; ?>" title="<?php echo $btn['descripcion']; ?>" data-reference="PAGARE" data-type_gestion="<?php echo $btn['id']; ?>" style=" font-size: 18px;margin: 5px 0px;width: 100%;">
                                    <?php echo $btn['etiqueta']; ?>
                                </button>
                            </div>
                        <?php endif; ?>    
                    <?php endif; ?>

                <?php if($btn['etiqueta'] == 'REENVIAR PAGARÉ') :?>
                    <?php if($solicitude['pagare_firmado']): ?>
                            <div class="col-xs-2">
                                <button id="repromissory" class="btn btn-xs bg-<?php echo $btn['color']; ?>" title="<?php echo $btn['descripcion']; ?>" data-reference="PAGARE" data-type_gestion="<?php echo $btn['id']; ?>" style=" font-size: 18px;margin: 5px 0px;width: 100%;">
                                    <?php echo $btn['etiqueta']; ?>
                                </button>
                            </div>
                        <?php endif; ?>    
                    <?php endif; ?>
                
                <?php if($btn['etiqueta'] == 'PAGARÉ FIRMADO') :// Si el pagare ya esta firmado deshabilito el boton ?>
                    <?php if($solicitude['pagare_firmado']): ?>
                        <div class="col-xs-2">
                            <button disabled="disabled" class="btn btn-xs bg-<?php echo $btn['color']; ?>" title="<?php echo $btn['descripcion']; ?>" style=" font-size: 18px;margin: 5px 0px;width: 100%;">
                                <?php echo $btn['etiqueta']; ?>
                            </button>
                        </div>
                    <?php endif; ?>    
                <?php endif; ?>
                <?php if($btn['etiqueta'] == 'APROBADO') : ?>
                    <div class="col-xs-2">
                        <button id="approved" class="btn btn-xs btn-success button-gestion" title="<?php echo $btn['descripcion']; ?>" data-reference="APROBADO" data-type_gestion="<?php echo $btn['id']; ?>">
                            <i class="fa <?php echo $btn['fa_icon']; ?>"></i><?php echo $btn['etiqueta']; ?>
                        </button>
                    </div>
                <?php endif; ?>
                <?php if($btn['etiqueta'] == 'VISADO') : ?>
                    <?php if($tipo_operador[0]["descripcion"] == "FRAUDE"):?>
                   		<div class="col-xs-2">
                    		<button id="visado" class="btn btn-xs btn-info button-gestion" title="<?php echo $btn['descripcion']; ?>" data-reference="VISADO" data-type_gestion="<?php echo $btn['id']; ?>">
                                <i class="fa <?php echo $btn['fa_icon']; ?>"></i><?php echo $btn['etiqueta']; ?>
                            </button>
                    	</div>
                    <?php endif;?>
                <?php endif;?>
                <?php if($btn['etiqueta'] == 'ANALIZADO') : ?>
                    <?php if($tipo_operador[0]["descripcion"] == "AUDITOR VENTAS"):?>
                   		<div class="col-xs-2">
                    		<button id="analizado" class="btn btn-xs btn-info button-gestion" title="<?php echo $btn['descripcion']; ?>" data-reference="ESCALADO ANALIZADO" data-type_gestion="<?php echo $btn['id']; ?>">
                                <i class="fa <?php echo $btn['fa_icon']; ?>"></i><?php echo $btn['etiqueta']; ?>
                            </button>
                    	</div>
                    <?php endif;?>
                <?php endif;?>
                <?php if($btn['etiqueta'] == 'RECHAZADO') : ?>
                   	<div class="col-xs-2">
                        <button id="rejected" class="btn btn-xs btn-danger button-gestion" title="<?php echo $btn['descripcion']; ?>" data-reference="RECHAZADO" data-type_gestion="<?php echo $btn['id']; ?>">
                            <i class="fa <?php echo $btn['fa_icon']; ?>"></i><?php echo $btn['etiqueta']; ?>
                        </button>
                    </div>
                <?php endif;?>
            <?php endforeach; ?>
                <div class="col-xs-1"></div>
           </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    
    button_status();
    
    var base_url = $("#box_botones_gestion #base_url").val();
    // EVENTOS
    // Controla el accionar de los botones.
    $("#box_botones_gestion .button-gestion").on("click",function(){ 
        let id_solicitud = $("#id_solicitud").val();
        let id_operador = $("#box_botones_gestion #id_operador").val();
        let estado = $(this).data('reference');
        let type_contact = $(this).data('type_gestion');
        solicitudeUpdate( id_solicitud, id_operador, type_contact, estado);    
        $(this).attr('disabled', 'disabled');
        track_generado = true;

    });

    $("#box_botones_gestion #promissory").on("click", function ()
    {
        let id_solicitud = $("#id_solicitud").val();
        enviar_pagare(id_solicitud, $(this));
    });
    $("#box_botones_gestion #repromissory").on("click", function ()
    {
        let id_solicitud = $("#id_solicitud").val();
        reenviar_pagare(id_solicitud, $(this));
    });

}); 
</script>
           
