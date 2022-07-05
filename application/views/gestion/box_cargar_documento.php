<div class="box box-info">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Documentos</strong></small></h6>
    </div>
    <div id="field_files" class="list-group well-gestion well-sm" style="">
           <form id="new_doc" class="form-inline" method="POST" >
                        <input type="hidden" name="id_solicitud" value="<?php echo isset($solicitude['id'])?$solicitude['id']:''?>">
                        <input type="hidden" name="documento" value="<?php echo isset($solicitude['documento'])?$solicitude['documento']:''?>">
                        <input type="hidden" name="action" value="Subir imagen o documento">
                        <div id="field_file" class="form-group">
                            <label class="control-label" for="tipo">Archivo:</label>
                            <span class="file">
                                <input id="file" type="file" name="file" style="width:100%; display: none">
                            </span>
                            <label class="control-label" id="label_archivo" for="file">
                                <span> Seleccionar archivo </span>
                            </label>
                        </div>
                        <div id="field_id_img_required" class="form-group">
                            <label class="control-label" for="tipo">Tipo:</label>
                            <br>
                            <select  class="form-control input-sm" name="id_img_required" style="width:100%;">
                                    <option disabled="disabled" selected>.:Seleccione una opción:.</option>
                                    <?php foreach ($images['options'] as $key => $value): ?>
                                            <option value="<?php echo $value['id']?>"><?php echo $value['etiqueta']?></option>
                                    <?php endforeach; ?>
                            </select>
                            <span class="help-block" style="display: none;"></span>
                        </div>
                        <div class="a" style="text-align: center; margin-top: 5px">
                            <button  onclick="subirImagen()" class=" btn btn-sm btn-primary">Subir</button>
                        </div>
	        </form>
    </div>
</div>

<script type="text/javascript">
	$("document").ready(function(){			
            //Cambio el file para que aparezca el nombre del archivo en el boton cuando se selecciona uno
            jQuery('input[type=file]').change(function(){
                var filename = jQuery(this).val().split('\\').pop();
                var idname = jQuery(this).attr('id');
                if(!filename){
                    jQuery('span.'+idname).next().find('span').html("SELECCIONAR ARCHIVO");
                } else {
                    jQuery('span.'+idname).next().find('span').html(filename);
                    if(!filename){
                        $("#label_archivo").html("SELECCIONAR ARCHIVO");
                    }
                }
            });

    });
</script>