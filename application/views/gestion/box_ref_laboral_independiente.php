<div class="box box-info">
    <?php //print_r($ref_personal);die(); ?>
    <div id="div_referencia_personal">
        <input hidden id="id_ref_personal" value="<?php echo $ref_personal[0]['id'] ?>">
    </div>
     <input hidden id="referencia_tipo_personal" value="REFERENCIA LABORAL">
    <input hidden id="operador_nombre" value="<?php echo isset($_SESSION['user']->first_name)?$_SESSION['user']->first_name:'' ;?>  <?php echo isset($_SESSION['user']->last_name)?$_SESSION['user']->last_name:'' ;?>">
   
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Referencia laboral</strong></small></h6>
        <i style="float: right; font-size: 20px; margin-right: 8px; color: green" onclick="agregar_referencia_personal(<?php echo $ref_personal[0]['id'] ?>,'laboralI')" title="Agregar Nueva Referencia" class="verif fa fa-plus-square"></i>
    </div>
    <?php if(isset($ref_personal[0])): ?>
    <table class="table table-striped table=hover display" id="tabla_vista_personal" width="100%">
        <tbody>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Actividad</td>
                <td class="analisis_col_der">
                    <strong><?php echo isset($ref_personal[0]['actividad'])?$ref_personal[0]['actividad']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Dirección</td>
                <td class="analisis_col_der">
                    <strong><?php echo  isset($ref_personal[0]['actividad_direccion'])?$ref_personal[0]['actividad_direccion']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Facebook</td>
                <td class="analisis_col_der">
                    <strong><?php echo  isset($ref_personal[0]['facebook'])?$ref_personal[0]['facebook']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Instagram</td>
                <td class="analisis_col_der">
                    <strong><?php echo  isset($ref_personal[0]['instagram'])?$ref_personal[0]['instagram']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Tipo empresa</td>
                <td class="analisis_col_der">
                    <strong><?php echo  isset($ref_personal[0]['tipo_empresa'])?$ref_personal[0]['tipo_empresa']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Tipo contrato</td>
                <td class="analisis_col_der">
                    <strong id="estado_personal"><?php echo  isset($ref_personal[0]['tipo_contrato'])?$ref_personal[0]['tipo_contrato']:'' ?></strong>
                </td>
            </tr>
            <!-- <tr>
                <td class="analisis_col_izq" style="text-align: right;">Verificaci&oacute;n</td>
                <td class="analisis_col_der">
                    <select class="form-control col-md-12" name="verificacion" id="personal">
                        <option value="" selected>.: Seleccione :.</option>
                        <option value="CONTACTADO">CONTACTADO</option>
                        <option value="NO CONTACTADO">NO CONTACTADO</option>
                        <option value="NO REFERENCIA">NO REFERENCIA</option>
                    </select>                                      
                </td>
                <td class="col-md-1">
                    <i style="font-size: 20px; margin-right: 8px; color: cadetblue" onclick="verificar_personal()" class="fa fa-check-square"></i>  
                </td>
            </tr> -->
        </tbody>
    </table>
    <?php endif; ?>
    <table class="table table-striped table=hover display" id="tabla_agregar_personal" hidden width="100%">
        <tbody>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Actividad</td>
                <td class="analisis_col_der">
                    <input type="text" id="actividad" value="" class="agregar_referencia_personal" autocomplete="off">
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Dirección</td>
                <td class="analisis_col_der">
                    <input type="text" id="actividad_direccion" value="" class="agregar_referencia_personal" autocomplete="off">
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Facebook</td>
                <td class="analisis_col_der">
                    <input type="text" id="facebook" value="" class="agregar_referencia_personal" autocomplete="off">
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Instagram</td>
                <td class="analisis_col_der">
                    <input type="text" id="instagram" value="" class="agregar_referencia_personal" autocomplete="off">
                </td>
            </tr>
            <!-- <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Tipo empresa</td>
                <td class="analisis_col_der">
                    <input type="text" id="tipo_empresa" value="" class="agregar_referencia_personal" autocomplete="off">
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Tipo contrato</td>
                <td class="analisis_col_der">
                    <input type="text" id="tipo_contrato" value="" class="agregar_referencia_personal" autocomplete="off">
                </td>
            </tr> -->
                        
            <tr>
                <td class="analisis_col_izq" style="text-align: right;"></td>
                <td class="analisis_col_der"> 
                    <button class="btn btn-success pull-right" data-id-referencia="" referencia="laboralI" onclick="registrar_personal(this)" id="registrar_personal">REGISTRAR</button>                 
                </td>
            </tr>           
        </tbody>
    </table>
    <!-- Tabla de lista de relaciones -->
    <table class="table table-striped table=hover display" id="tabla_lista_referencias_personal" width="100%">
        <tbody>
    <?php for ($i = 0; $i < count($ref_personal); $i++) {?>
            <tr>
                <td class="analisis_col_izq" style="text-align: left;">
                    <strong>* <?php echo isset($ref_personal[$i]['actividad'])?$ref_personal[$i]['actividad']:'' ?></strong>
                </td>
                
                <td class="analisis_col_der">
                    <i style="float:right;font-size: 20px;color: cornflowerblue" onclick="ver_referencia_personal(<?php echo $ref_personal[$i]['id'] ?>)" class="verif fa fa-eye"></i>  
                </td>
            </tr>
            <?php } ?>
      </tbody>
    </table>
</div>