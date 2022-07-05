<div class="box box-info" id="box_familiar">
    <div id="div_referencia">
        <input hidden id="id_ref_family" value="<?php echo $ref_family[0]['id'] ?>">
    </div>
    <input hidden id="referencia_tipo" value="REFERENCIA FAMILIAR">
    <input hidden id="operador_nombre" value="<?php echo isset($_SESSION['user']->first_name)?$_SESSION['user']->first_name:'' ;?>  <?php echo isset($_SESSION['user']->last_name)?$_SESSION['user']->last_name:'' ;?>">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Referencia familiar</strong></small></h6>        
        <i style="float: right; font-size: 20px; margin-right: 8px; color: green" onclick="agregar_referencia_familiar()" title="Agregar Nuevo Familiar" class="verif fa fa-plus-square"></i>
    </div>
    <?php
    if(isset($ref_family[0])){ ?>
    <table class="table table-striped table=hover display" id="tabla_vista" width="100%">
        <tbody>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Nombres y apellidos</td>
                <td class="analisis_col_der">
                    <strong><?php echo isset($ref_family[0]['nombres_apellidos'])?$ref_family[0]['nombres_apellidos']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Telefono</td>
                <td class="analisis_col_der"><strong><?php echo isset($ref_family[0]['telefono'])?$ref_family[0]['telefono']:'' ?></strong></td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Parentesco</td>
                <td class="analisis_col_der">
                    <strong><?php echo isset($ref_family[0]['Nombre_Parentesco'])?$ref_family[0]['Nombre_Parentesco']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Estado</td>
                <td class="analisis_col_der">
                    <strong id="estado_familiar"><?php echo  isset($ref_family[0]['verificacion'])?$ref_family[0]['verificacion']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Verificaci&oacute;n</td>
                <td class="analisis_col_der">
                    <select class="form-control col-md-12" id="familiar" name="verificacion">
                        <option value="" selected>.: Seleccione :.</option>
                        <option value="CONTACTADO">CONTACTADO</option>
                        <option value="NO CONTACTADO">NO CONTACTADO</option>
                        <option value="NO REFERENCIA">NO REFERENCIA</option>
                    </select>                    
                </td>
                <td class="col-md-1">
                    <i style="font-size: 20px; margin-right: 8px; color: cadetblue" onclick="verificar_familiar()" class="verif fa fa-check-square"></i>  
                </td>
            </tr>            
        </tbody>
    </table>
    <?php } ?>  
    
    <table class="table table-striped table=hover display" id="tabla_agregar_familiar" hidden width="100%">
        <tbody>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Nombres y apellidos</td>
                <td class="analisis_col_der">
                    <input type="text" id="nombre_apellido_agregar" value="" class="agregar_referencia_familiar" autocomplete="off">
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Telefono</td>
                <td class="analisis_col_der">
                    <input type="text" id="telefono_agregar" onkeypress="ValidarNumeros(event)" class="agregar_referencia_familiar" value="" autocomplete="off">
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Parentesco</td>
                <td class="analisis_col_der">                    
                    <select class="form-control col-md-12" id="parentesco_agregar" name="parentesco">
                        <option value="" disabled selected>.: Seleccione :.</option>
                        <?php foreach ($parentesco as $key => $datos): ?>
                            <?php if ($datos['id_parentesco'] >= 1 && $datos['id_parentesco'] <= 4): ?>
                                <option value="<?php echo $datos['id_parentesco'] ?>"><?php echo $datos['Nombre_Parentesco'] ?></option>
                            <?php endif; ?>
                        <?php endforeach ?>                        
                    </select>                    
                </td>
            </tr> 
            <tr>
                <td class="analisis_col_izq" style="text-align: right;"></td>
                <td class="analisis_col_der"> 
                    <button class="btn btn-success pull-right" data-id-referencia="" onclick="registrar_familiar(this)" id="registrar_familiar">REGISTRAR</button>                 
                </td>
            </tr>           
        </tbody>
    </table>
    <!-- Tabla de lista de relaciones -->
    <table class="table table-striped table=hover display" id="tabla_lista_referencias" width="100%">
        <tbody>
    <?php for ($i = 0; $i < count($ref_family); $i++) {?>
            <tr>
                <td class="analisis_col_izq" style="text-align: left;">
                    <strong>* <?php echo isset($ref_family[$i]['nombres_apellidos'])?$ref_family[$i]['nombres_apellidos']:'' ?></strong>
                </td>
                <?php if ($ref_family[$i]['carga_consultor'] == 1){ ?>
                    <td class="analisis_col_der">
                        <i style="float:right;font-size: 20px;color: cornflowerblue; margin-left: 6px;" onclick="ver_referencia_familiar(<?php echo $ref_family[$i]['id'] ?>)" class="verif fa fa-eye"></i>
                        <i style="float:right;font-size: 20px;color: blue" data-id-referencia =" <?php echo $ref_family[$i]['id'] ?>" onclick="editar_referencia_familiar(this)" class="verif fa fa-edit"></i>  
                    </td>
                <?php } else { ?>
                    <td class="analisis_col_der">
                        <i style="float:right;font-size: 20px;color: cornflowerblue" onclick="ver_referencia_familiar(<?php echo $ref_family[$i]['id'] ?>)" class="verif fa fa-eye"></i>  
                    </td>
                <?php }?>
                
            </tr>
            <?php } ?>
      </tbody>
    </table>
</div>