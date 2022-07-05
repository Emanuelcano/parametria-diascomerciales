<style>
    .carousel-inner img {
        
        width: 100%;
        max-height: 460px;
        margin: 0px auto;
    }
</style>

<div class="box box-info">
    <div class="list-group well-gestion well-sm" style="margin-top: -20px; height:300px;">
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <?php $i=0; foreach ($docs['data'] as $key => $doc):?>   
                    <?php if(!$doc->is_image && $doc->extension != '.mp4' && $doc->extension != '.webm' ):?>
                            <?php if($i == 0):  ?>
                                <li data-target="#carousel-example-generic" data-slide-to="<?= $i ?>" class="active"></li>
                            <?php else:  ?>
                                <li data-target="#carousel-example-generic" data-slide-to="<?= $i ?>"></li>
                            <?php endif; ?>
                        <?php  $i++; ?>
                    <?php endif; ?>       
                <?php endforeach; ?>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <?php if(!empty($docs)):  ?>
                    <?php $i=0; foreach ($docs['data'] as $key => $doc):?>   
                        <?php if(!$doc->is_image && $doc->extension != '.mp4' && $doc->extension != '.webm' ):?>
                                <?php if($i == 0):  ?>
                                    <div class="item active" style="margin-left:auto; margin-right:auto;">
                                        <div class="col-md-3" style="position:absolute; width:243px; height:200px; top:96px;"><a style="cursor:pointer" onclick="previewImg(<?= '\''.$doc->patch_imagen.'\''?>,<?= '\''.$doc->etiqueta.'\'' ?>)">Ver</a></div>
                                            <object
                                                type="application/pdf"
                                                data="<?php echo base_url($doc->patch_imagen) ?>"
                                                style="width: 100px; height: 150px;">
                                                ERROR (no puede mostrarse el documento)
                                            </object>
                                    </div>
                                <?php else:  ?>
                                    <div class="item" style="margin-left:auto; margin-right:auto;">
                                        <div class="col-md-3"  style="position:absolute; width:243px; height:200px; top:96px"><a style="cursor:pointer" onclick="previewImg(<?= '\''.$doc->patch_imagen.'\''?>,<?= '\''.$doc->etiqueta.'\'' ?>)">Ver</a></div>
                                            <object
                                                type="application/pdf"
                                                data="<?php echo base_url($doc->patch_imagen) ?>"
                                                style="width: 100px; height: 150px;">
                                                ERROR (no puede mostrarse el documento)
                                            </object>
                                    </div>
                                <?php endif; ?>
                            <?php $i++; ?>
                        <?php endif; ?>       
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Controls -->
            <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</div>
