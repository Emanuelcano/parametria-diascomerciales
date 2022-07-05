<div class="box box-info" id="box_documento">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Documentos <?=(count($docs['pagare_revolvente']) > 0)? "<span class='text-blue'>PAGARE REVOLVENTE SOLICITUD: ".$docs['pagare_revolvente'][0]->id_solicitud."</span>": '' ;?></strong></small></h6>
    </div>
    <div id="field_files" class="list-group well-gestion well-sm">
                
            <?php
            if(isset($docs['data']) && !empty($docs['data'])):?>
                <?php foreach ($docs['data'] as $key => $doc):?>
                        
                            <?php if(!$doc->is_image && $doc->extension != '.mp4' && $doc->extension != '.webm' ): ?>
                                <?php if($firma == 1 && $doc->etiqueta == 'Pagare original' && $evaluarFirma ){ ?>
                                        <div class="list-group-item" style="font-size:smaller;"><a onclick="event.preventDefault();window.open('<?php echo base_url($doc->patch_imagen)?>', '_self');return false;" ><?php echo $doc->etiqueta;?></a> <a onclick = 'actualizarPagare(<?php echo $id_solicitud ?>)' class="btn-warning btn-xs" style="float: right;"><i class="fa fa-refresh"></i></a></div>
                                <?php }else{ ?>
                                        <a onclick="event.preventDefault();window.open('<?php echo base_url($doc->patch_imagen)?>', '_blank');return false;" class="list-group-item" style="font-size:smaller;"><?php echo $doc->etiqueta;?></a>
                                <?php }?>

                    <?php endif; ?>
                            
            <?php endforeach; ?>
            <?php endif; ?>
    </div>
</div>