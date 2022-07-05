<div class="box box-info">
    <?php //print_r($ref_personal);die(); ?>
    <div id="div_referencia_personal">
        <input hidden id="id_ref_personal" value="<?php echo $ref_personal[0]['id'] ?>">
    </div>
    <input hidden id="referencia_tipo_personal" value="REFERENCIA PERSONAL">
    <input hidden id="operador_nombre" value="<?php echo isset($_SESSION['user']->first_name)?$_SESSION['user']->first_name:'' ;?>  <?php echo isset($_SESSION['user']->last_name)?$_SESSION['user']->last_name:'' ;?>">
   
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Referencia personal</strong></small></h6>
        <i style="float: right; font-size: 20px; margin-right: 8px; color: green" onclick="agregar_referencia_personal(<?php echo $ref_personal[0]['id'] ?>,'personal')" title="Agregar Nueva Referencia" class="verif fa fa-plus-square"></i>
    </div>
    <?php if(isset($ref_personal[0])): ?>
    <table class="table table-striped table=hover display" id="tabla_vista_personal" width="100%">
        <tbody>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Nombres y apellidos</td>
                <td class="analisis_col_der">
                    <strong><?php echo isset($ref_personal[0]['nombres_apellidos'])?$ref_personal[0]['nombres_apellidos']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Telefono</td>
                <td class="analisis_col_der">
                    <strong><?php echo  isset($ref_personal[0]['telefono'])?$ref_personal[0]['telefono']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Parentesco</td>
                <td class="analisis_col_der">
                    <strong><?php echo  isset($ref_personal[0]['Nombre_Parentesco'])?$ref_personal[0]['Nombre_Parentesco']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Estado</td>
                <td class="analisis_col_der">
                    <strong id="estado_personal"><?php echo  isset($ref_personal[0]['verificacion'])?$ref_personal[0]['verificacion']:'' ?></strong>
                </td>
            </tr>
            <tr>
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
            </tr>
        </tbody>
    </table>
    <?php endif; ?>
    
    <table class="table table-striped table=hover display" id="tabla_agregar_personal" hidden width="100%">
        <tbody>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Nombres y apellidos</td>
                <td class="analisis_col_der">
                    <input type="text" id="nombre_apellido_personal" value="" class="agregar_referencia_personal" autocomplete="off">
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Telefono</td>
                <td class="analisis_col_der">
                    <input type="text" id="telefono_personal" onkeypress="ValidarNumeros(event)" class="agregar_referencia_personal" value="" autocomplete="off">
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Parentesco</td>
                <td class="analisis_col_der">                    
                    <select class="form-control col-md-12" id="parentesco_personal" name="parentesco">
                        <option value="" disabled selected>.: Seleccione :.</option>
                        <?php foreach ($parentesco as $key => $datos): ?>
                            <?php if ($datos['id_parentesco'] >= 5): ?>
                                <option value="<?php echo $datos['id_parentesco'] ?>"><?php echo $datos['Nombre_Parentesco'] ?></option>
                            <?php endif; ?>
                        <?php endforeach ?>                        
                    </select>                    
                </td>
            </tr> 
            <tr>
                <td class="analisis_col_izq" style="text-align: right;"></td>
                <td class="analisis_col_der"> 
                    <button class="btn btn-success pull-right" data-id-referencia="" referencia="personal" onclick="registrar_personal(this)" id="registrar_personal">REGISTRAR</button>                 
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
                    <strong>* <?php echo isset($ref_personal[$i]['nombres_apellidos'])?$ref_personal[$i]['nombres_apellidos']:'' ?></strong>
                </td>
                <?php if ($ref_personal[$i]['carga_consultor'] == 1){ ?>
                    <td class="analisis_col_der">
                        <i style="float:right;font-size: 20px;color: cornflowerblue; margin-left: 6px;" onclick="ver_referencia_personal(<?php echo $ref_personal[$i]['id'] ?>)" class="verif fa fa-eye"></i>
                        <i style="float:right;font-size: 20px;color: blue" data-id-referencia =" <?php echo $ref_personal[$i]['id'] ?>" onclick="editar_referencia_personal(this)" class="verif fa fa-edit"></i>    
                    </td>
                <?php } else { ?>
                    <td class="analisis_col_der">
                        <i style="float:right;font-size: 20px;color: cornflowerblue" onclick="ver_referencia_personal(<?php echo $ref_personal[$i]['id'] ?>)" class="verif fa fa-eye"></i>  
                    </td>
                <?php }?>                
            </tr>
            <?php } ?>
      </tbody>
    </table>
</div>
