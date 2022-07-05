<div class="box box-info" id="box_familiar">
    <div id="div_referencia">
        <input hidden id="id_ref_family" value="<?php echo isset($datos_personales[0])?$datos_personales[0]['id']:'' ?>">
    </div>
    <input hidden id="referencia_tipo" value="REFERENCIA FAMILIAR">
    <input hidden id="operador_nombre" value="<?php echo isset($_SESSION['user']->first_name)?$_SESSION['user']->first_name:'' ;?>  <?php echo isset($_SESSION['user']->last_name)?$_SESSION['user']->last_name:'' ;?>">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Datos Personales</strong></small></h6>  
    </div>
    <?php
    if(isset($datos_personales[0])){ ?>
    <table class="table table-striped table=hover display" id="tabla_vista" width="100%">
        <tbody>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Tipo Plan: </td>
                <td class="analisis_col_der">
                    <strong><?php echo isset($datos_personales[0]['tipo_plan'])?$datos_personales[0]['tipo_plan']:'' ?></strong>                    
                </td>
                <td class="analisis_col_izq" style="text-align: right;">Departamento: </td>
                <td class="analisis_col_der">
                    <strong id="departamento"><?php echo  isset($datos_personales[0]['departamento'])?$datos_personales[0]['departamento']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" width="30%" style="text-align: right;">Operador Móvil: </td>
                <td class="analisis_col_der">
                    <strong><?php echo isset($datos_personales[0]['nombre_operador'])?$datos_personales[0]['nombre_operador']:'' ?></strong>
                </td>
                 <td class="analisis_col_izq" style="text-align: right;">Dirección de Residencia: </td>
                <td class="analisis_col_der">
                    <strong id="direccion"><?php echo  isset($datos_personales[0]['direccion_residencia'])?$datos_personales[0]['direccion_residencia']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Telefono Fijo</td>
                <td class="analisis_col_der"><strong><?php echo isset($datos_personales[0]['telefono_fijo'])?$datos_personales[0]['telefono_fijo']:'' ?></strong></td>
                <td class="analisis_col_izq" style="text-align: right;">Tiempo de Residencia: </td>
                <td class="analisis_col_der">
                    <strong id="tiempo"><?php echo  isset($datos_personales[0]['tiempo_de_residencia'])?$datos_personales[0]['tiempo_de_residencia']:'' ?></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Vehículo: </td>
                <td class="analisis_col_der">
                    <strong><?php echo isset($datos_personales[0]['vehiculo'])?$datos_personales[0]['vehiculo']:'' ?></strong>
                </td>
                <td class="analisis_col_izq" style="text-align: right;">Personas Conviviendo: </td>
                <td class="analisis_col_der">
                    <?php $nombre_conviviente = "";
                    $array_convivientes = explode("-",$datos_personales[0]['personas_conviviendo']);
                    for($i=0; $i<count($array_convivientes); $i++){
                        //ver si esta parte se puede cambiar para que lo traiga por base
                        if($array_convivientes[$i] == 1){
                            $nombre = "Solo";
                        } else if($array_convivientes[$i] == 2){
                            $nombre = "Padres";                            
                        } else if($array_convivientes[$i] == 3){
                            $nombre = "Pareja";                            
                        } else if($array_convivientes[$i] == 4){
                            $nombre = "Amigos";                            
                        } else if($array_convivientes[$i] == 5){
                            $nombre = "Hijos";                            
                        } else if($array_convivientes[$i] == 6){
                            $nombre = "Otros";                            
                        }
                        if($i < count($array_convivientes)-1){
                            $nombre_conviviente .= $nombre." - ";
                        } else {
                            $nombre_conviviente .= $nombre;
                        }                       
                    }?>
                    <strong id="personas"><?php echo  isset($nombre_conviviente)?$nombre_conviviente:'' ?></strong>                    
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Vehículo Placa: </td>
                <td class="analisis_col_der">
                    <strong><?php echo isset($datos_personales[0]['vehiculo_placa'])?$datos_personales[0]['vehiculo_placa']:'' ?></strong>
                </td>
                <td class="analisis_col_izq" style="text-align: right;"></td>
                <td class="analisis_col_der">
                    <strong></strong>
                </td>
            </tr>
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Nivel Educativo: </td>
                <td class="analisis_col_der">
                    <strong id="nivel"><?php echo  isset($datos_personales[0]['nombre_nivel_estudio'])?$datos_personales[0]['nombre_nivel_estudio']:'' ?></strong>
                </td>
                <td class="analisis_col_izq" style="text-align: right;">Personas a Cargo: </td>
                <td class="analisis_col_der">
                    <strong id="acargo"><?php echo  isset($datos_personales[0]['personas_manteniendo'])?$datos_personales[0]['personas_manteniendo']:'' ?></strong>
                </td>
            </tr> 
            <tr>
                <td class="analisis_col_izq" style="text-align: right;">Motivo de Solicitud: </td>
                <td class="analisis_col_der">
                    <strong id="motivo"><?php echo  isset($datos_personales[0]['nombre_motivo'])?$datos_personales[0]['nombre_motivo']:'' ?></strong>
                </td>
                <td class="analisis_col_izq" style="text-align: right;">Cantidad de Hijos: </td>
                <td class="analisis_col_der">
                    <strong id="cantidadhijos"><?php echo  isset($datos_personales[0]['cantidad'])?$datos_personales[0]['cantidad']:'' ?></strong>
                </td>
            </tr> 
        </tbody>
    </table>
    <?php } ?>        
</div>