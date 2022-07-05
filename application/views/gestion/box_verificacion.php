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
    if (isset($solicitude['estado'])){
        if($solicitude['estado'] == 'APROBADO' || $solicitude['estado'] == 'TRANSFIRIENDO' || $solicitude['estado'] == 'PAGADO' || $solicitude['estado'] == 'RECHAZADO'){
            $disabled_laboral  = 'disabled';
            $disabled_provl    = 'disabled';
            $disabled_prov2    = 'disabled';
            $disabled_tit_ind  = 'disabled';
            $disabled_familiar = 'disabled';
            $disabled_personal = 'disabled';
            $disabled_titular  = 'disabled';
            $visible_form      = 'style="display:block"';
            $botones  = 'disabled';
        } else {
            $botones  = '';
            $visible_form      = 'style="display:none"';
            if (count($referencia_laboral_botones) >= 3){
                $disabled_laboral = 'disabled';
            } else {
                $disabled_laboral = '';
            }
            if (count($referencia_titular_prov_1) >= 3){
                $disabled_provl = 'disabled';
            } else {
                $disabled_provl = '';
            }
            if (count($referencia_titular_prov_2) >= 3){
                $disabled_prov2 = 'disabled';
            } else {
                $disabled_prov2 = '';
            }    
            if (count($referencia_titular_ind_botones) >= 1){
                $disabled_tit_ind = 'disabled';
            } else {
                $disabled_tit_ind = '';
            }
            if (count($referencia_familiar_botones) >= 3){
                $disabled_familiar = 'disabled';
            } else {
                $disabled_familiar = '';
            }
            if (count($referencia_personal_botones) >= 3){
                $disabled_personal = 'disabled';
            } else {
                $disabled_personal = '';
            }
            if (count($referencia_titular_botones) >= 1){
                $disabled_titular = 'disabled';
            } else {
                $disabled_titular = '';
            }  
        }
    } else {
        $botones  = '';
        $visible_form      = 'style="display:none"';
            if (count($referencia_laboral_botones) >= 3){
                $disabled_laboral = 'disabled';
            } else {
                $disabled_laboral = '';
            }
            if (count($referencia_titular_prov_1) >= 3){
                $disabled_provl = 'disabled';
            } else {
                $disabled_provl = '';
            }
            if (count($referencia_titular_prov_2) >= 3){
                $disabled_prov2 = 'disabled';
            } else {
                $disabled_prov2 = '';
            }    
            if (count($referencia_titular_ind_botones) >= 1){
                $disabled_tit_ind = 'disabled';
            } else {
                $disabled_tit_ind = '';
            }
            if (count($referencia_familiar_botones) >= 3){
                $disabled_familiar = 'disabled';
            } else {
                $disabled_familiar = '';
            }
            if (count($referencia_personal_botones) >= 3){
                $disabled_personal = 'disabled';
            } else {
                $disabled_personal = '';
            }
            if (count($referencia_titular_botones) >= 1){
                $disabled_titular = 'disabled';
            } else {
                $disabled_titular = '';
            }    
    }
    
?>
<div id="box_botones" class="box box-info">
    <input id="tipo_operador" type="hidden" value="<?php echo $tipo_operador[0]["descripcion"];?>">
    <input id="base_url" type="hidden" value="<?php echo base_url();?>">
    <input id="analysis_buro" type="hidden" value="<?php echo isset($solicitude['respuesta_analisis'])?$solicitude['respuesta_analisis']:''; ?>">
    <input id="solicitud_status" type="hidden" value="<?php echo isset($solicitude['estado'])?$solicitude['estado']:''; ?>">
    <input id="id_operador" type="hidden" value="<?php echo $this->session->userdata('idoperador'); ?>">   
    <input id="step" type="hidden" value="<?php echo isset($solicitude['paso'])?$solicitude['paso']: 0; ?>">    
    <div class="box-body">
        <div class="container-fluid">  
           <div class="row">
            <?php if ($solicitude['id_situacion'] == 3){ ?>  
               <div class="col-md-12">
                   <p style="font-weight: bold;">Recordar solicitar: </p>
                    <ul> 
                        <li> Factura principal proveedor (la de mayor monto)</li>
                        <li> Extracto bancario o pantalla de ultimos movimientos o resumen de tarjeta de credito</li>
                        <li> Dos referentes proveedor  o  un referente proveedor y un referente cliente</li>               
                    </ul>                  
               </div>  
                <div class="col-xs-4">
                    <button id="verified_proveedor1" <?php echo $botones ?> class="btn btn-xs btn-warning button-gestion" title="Verificación de proveedor">
                        <i class="fa fa-check-square-o"></i>VERIFICAR PROVEEDOR 1
                    </button>
                    <form id="form_proveedor1" <?php echo $visible_form ?>  action="<?php echo base_url()?>api/ApiVerificacion/set_referencia_proveedor1" method="POST">
                        <input name="id_solicitud" type="hidden" value="<?php echo isset($solicitude['id'])?$solicitude['id']:''; ?>">
                        <div class="form-group"> 
                            <label for="ocupacion_proveedor1" class="control-label">A qué se dedica? : </label>
                            <input type="text" class="form-control pregunta1 disabled_provl" <?php echo $disabled_provl ?> id="ocupacion_proveedor1" name="ocupacion_proveedor1" placeholder="Ingrese Ocupación">
                        </div> 
                        <div class="form-group"> 
                            <label for="domicilio_negocio_prov1" class="control-label">Domicilio del negocio: </label>
                            <input type="text" class="form-control pregunta2 disabled_provl" <?php echo $disabled_provl ?> id="domicilio_negocio_prov1" name="domicilio_negocio_prov1" placeholder="Ingrese Dirección">
                        </div> 
                        <div class="form-group"> 
                            <label for="barrio_negocio_prov1" class="control-label">Barrio: </label>
                            <input type="text" class="form-control pregunta3 disabled_provl" <?php echo $disabled_provl ?> id="barrio_negocio_prov1" name="barrio_negocio_prov1" placeholder="Ingrese Barrio">      
                        </div> 
                        <div class="form-group"> 
                            <label for="servicios_prov1" class="control-label">Descripción de servicios prestados: </label>
                            <input type="text" class="form-control pregunta4 disabled_provl" <?php echo $disabled_provl ?> id="servicios_prov1" name="servicios_prov1" placeholder="Ingrese Detalle">      
                        </div> 
                        <div class="form-group"> 
                            <label for="tiempo_trabajo_prov1" class="control-label">Hace cuantos años lo conoce? : </label>
                            <input type="text" class="form-control pregunta5 disabled_provl" <?php echo $disabled_provl ?> id="tiempo_trabajo_prov1" name="tiempo_trabajo_prov1" placeholder="Ingrese Tiempo">
                        </div> 
                        <div class="form-group"> 
                            <label for="producto_prov1" class="control-label">Producto : </label>
                            <input type="text" class="form-control pregunta6 disabled_provl" <?php echo $disabled_provl ?> id="producto_prov1" name="producto_prov1" placeholder="Ingrese Producto que le provee">
                        </div> 
                        <div class="form-group"> <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary disabled_provl" <?php echo $disabled_provl ?>>Guardar</button>
                            <button type="button" class="btn btn-warning disabled_provl" <?php echo $disabled_provl ?> id="nueva" onclick="limpiarFormularioProv1()">Nueva Verificación</button>
                        </div>  
                        <input hidden id="cantidad_prov1" value="<?php echo count($referencia_titular_prov_1)?>">
                        <!-- Listar las referencias en botones para mostrar cada una -->
                        <?php for ($i = 0; $i < count($referencia_titular_prov_1) ; $i++) {?>
                            <button type="button" class="btn btn-warning datos_verificacion" 
                                    pregunta1="<?php echo  isset($referencia_titular_prov_1[$i]['pregunta1'])?$referencia_titular_prov_1[$i]['pregunta1']:''; ?>" 
                                    pregunta2="<?php echo  isset($referencia_titular_prov_1[$i]['pregunta2'])?$referencia_titular_prov_1[$i]['pregunta2']:''; ?>"  
                                    pregunta3="<?php echo  isset($referencia_titular_prov_1[$i]['pregunta3'])?$referencia_titular_prov_1[$i]['pregunta3']:''; ?>" 
                                    pregunta4="<?php echo  isset($referencia_titular_prov_1[$i]['pregunta4'])?$referencia_titular_prov_1[$i]['pregunta4']:''; ?>"
                                    pregunta5="<?php echo  isset($referencia_titular_prov_1[$i]['pregunta5'])?$referencia_titular_prov_1[$i]['pregunta5']:''; ?>" 
                                    pregunta6="<?php echo  isset($referencia_titular_prov_1[$i]['pregunta6'])?$referencia_titular_prov_1[$i]['pregunta6']:''; ?>">
                                        <?php echo $i +1?>
                            </button>
                        <?php } ?>
                        <div id="boton_prov1"></div>                        
                    </form>                                       
                </div>
               <div class="col-xs-4">
                    <button id="verified_proveedor2" <?php echo $botones ?> class="btn btn-xs btn-success button-gestion" title="Verificación de proveedor">
                        <i class="fa fa-check-square-o"></i>VERIFICAR PROVEEDOR 2
                    </button>
                    <form id="form_proveedor2" <?php echo $visible_form ?>  action="<?php echo base_url()?>api/ApiVerificacion/set_referencia_proveedor2" method="POST">
                        <input name="id_solicitud" type="hidden" value="<?php echo isset($solicitude['id'])?$solicitude['id']:''; ?>">
                        <div class="form-group"> 
                            <label for="ocupacion_proveedor2" class="control-label">A qué se dedica? : </label>
                            <input type="text" <?php echo $disabled_prov2 ?> class="form-control pregunta1 disabled_prov2" id="ocupacion_proveedor2" name="ocupacion_proveedor2" placeholder="Ingrese Ocupación">
                        </div> 
                        <div class="form-group"> 
                            <label for="domicilio_negocio_prov2" class="control-label">Domicilio del negocio: </label>
                            <input type="text" <?php echo $disabled_prov2 ?> class="form-control pregunta2 disabled_prov2" id="domicilio_negocio_prov2" name="domicilio_negocio_prov2" placeholder="Ingrese Dirección">
                        </div> 
                        <div class="form-group"> 
                            <label for="barrio_negocio_prov2" class="control-label">Barrio: </label>
                            <input type="text" <?php echo $disabled_prov2 ?> class="form-control pregunta3 disabled_prov2" id="barrio_negocio_prov2" name="barrio_negocio_prov2" placeholder="Ingrese Barrio">      
                        </div> 
                        <div class="form-group"> 
                            <label for="servicios_prov2" class="control-label">Descripción de servicios prestados: </label>
                            <input type="text" <?php echo $disabled_prov2 ?> class="form-control pregunta4 disabled_prov2" id="servicios_prov2" name="servicios_prov2" placeholder="Ingrese Detalle">      
                        </div> 
                        <div class="form-group"> 
                            <label for="tiempo_trabajo_prov2" class="control-label">Hace cuantos años lo conoce? : </label>
                            <input type="text" <?php echo $disabled_prov2 ?> class="form-control pregunta5 disabled_prov2" id="tiempo_trabajo_prov2" name="tiempo_trabajo_prov2" placeholder="Ingrese Tiempo">
                        </div> 
                        <div class="form-group"> 
                            <label for="producto_prov2" class="control-label">Producto : </label>
                            <input type="text" <?php echo $disabled_prov2 ?> class="form-control pregunta6 disabled_prov2" id="producto_prov2" name="producto_prov2" placeholder="Ingrese Producto que le provee">
                        </div> 
                        <div class="form-group"> <!-- Submit Button -->
                            <button type="submit" <?php echo $disabled_prov2 ?> class="btn btn-primary disabled_prov2">Guardar</button>
                            <button type="button" <?php echo $disabled_prov2 ?> class="btn btn-success disabled_prov2" id="nueva" onclick="limpiarFormularioProv2()">Nueva Verificación</button>
                        </div> 
                        <input hidden id="cantidad_prov2" value="<?php echo count($referencia_titular_prov_2)?>">
                        <!-- Listar las referencias en botones para mostrar cada una -->
                        <?php for ($i = 0; $i < count($referencia_titular_prov_2) ; $i++) {?>
                            <button type="button" class="btn btn-success datos_verificacion" 
                                    pregunta1="<?php echo  isset($referencia_titular_prov_2[$i]['pregunta1'])?$referencia_titular_prov_2[$i]['pregunta1']:''; ?>" 
                                    pregunta2="<?php echo  isset($referencia_titular_prov_2[$i]['pregunta2'])?$referencia_titular_prov_2[$i]['pregunta2']:''; ?>"
                                    pregunta3="<?php echo  isset($referencia_titular_prov_2[$i]['pregunta3'])?$referencia_titular_prov_2[$i]['pregunta3']:''; ?>" 
                                    pregunta4="<?php echo  isset($referencia_titular_prov_2[$i]['pregunta4'])?$referencia_titular_prov_2[$i]['pregunta4']:''; ?>"
                                    pregunta5="<?php echo  isset($referencia_titular_prov_2[$i]['pregunta5'])?$referencia_titular_prov_2[$i]['pregunta5']:''; ?>" 
                                    pregunta6="<?php echo  isset($referencia_titular_prov_2[$i]['pregunta6'])?$referencia_titular_prov_2[$i]['pregunta6']:''; ?>">
                                        <?php echo $i +1?>
                            </button>
                        <?php } ?>
                        <div id="boton_prov2"></div>                        
                    </form>                                       
                </div>
                <div class="col-xs-4">
                    <button id="verified_titular_independiente" <?php echo $botones ?> class="btn btn-xs btn-info button-gestion" title="Verificación de titular">
                        <i class="fa fa-check-square-o"></i>VERIFICACION TITULAR
                    </button>
                    <form id="form_titular_independiente" <?php echo $visible_form ?> action="<?php echo base_url()?>api/ApiVerificacion/set_referencia_titular_ind" method="POST">
                        <input name="id_solicitud" type="hidden" value="<?php echo isset($solicitude['id'])?$solicitude['id']:''; ?>">
                        <div class="form-group"> 
                            <label for="telefono_titular_ind" class="control-label">Teléfono: </label>
                            <input type="text" class="form-control" id="telefono_titular_ind" value="<?php echo isset($solicitude['telefono'])?$solicitude['telefono']:''; ?>" disabled="disabled">
                        </div> 
                        <div class="form-group"> 
                            <label for="ocupacion" class="control-label">A qué se dedica? : </label>
                            <input type="text" <?php echo $disabled_tit_ind ?> class="form-control pregunta1 disabled_tit_ind" id="ocupacion" name="ocupacion" placeholder="Ingrese Ocupación">
                        </div> 
                        <div class="form-group"> 
                            <label for="domicilio_negocio" class="control-label">Domicilio del negocio: </label>
                            <input type="text" <?php echo $disabled_tit_ind ?> class="form-control pregunta2 disabled_tit_ind" id="domicilio_negocio" name="domicilio_negocio" placeholder="Ingrese Dirección">
                        </div> 
                        <div class="form-group"> 
                            <label for="barrio_negocio" class="control-label">Barrio: </label>
                            <input type="text" <?php echo $disabled_tit_ind ?> class="form-control pregunta3 disabled_tit_ind" id="barrio_negocio" name="barrio_negocio" placeholder="Ingrese Barrio">      
                        </div> 
                        <div class="form-group"> 
                            <label for="servicios" class="control-label">Descripción de servicios prestados: </label>
                            <input type="text" <?php echo $disabled_tit_ind ?> class="form-control pregunta4 disabled_tit_ind" id="servicios" name="servicios" placeholder="Ingrese Detalle">      
                        </div> 
                        <div class="form-group"> 
                            <label for="tiempo_trabajo_ind" class="control-label">Hace cuantos años lo conoce? : </label>
                            <input type="text" <?php echo $disabled_tit_ind ?> class="form-control pregunta5 disabled_tit_ind" id="tiempo_trabajo_ind" name="tiempo_trabajo_ind" placeholder="Ingrese Tiempo">
                        </div> 
                        <div class="form-group"> 
                            <label for="nit" class="control-label">NIT : </label>
                            <input type="text" <?php echo $disabled_tit_ind ?> class="form-control pregunta6 disabled_tit_ind" id="nit" name="nit" placeholder="Ingrese NIT">
                        </div> 
                        <div class="form-group"> <!-- Submit Button -->
                            <button type="submit" <?php echo $disabled_tit_ind ?> class="btn btn-primary disabled_tit_ind">Guardar</button>
                            <button type="button" <?php echo $disabled_tit_ind ?> class="btn btn-info disabled_tit_ind" id="nueva" onclick="limpiarFormularioTitularInd()">Nueva Verificación</button>
                        </div>  
                        <!-- Listar las referencias en botones para mostrar cada una -->
                        <input hidden id="cantidad_titular_ind" value="<?php echo count($referencia_titular_ind_botones)?>">
                        <?php for ($i = 0; $i < count($referencia_titular_ind_botones) ; $i++) {?>
                            <button type="button" class="btn btn-info datos_verificacion" 
                                    pregunta1="<?php echo  isset($referencia_titular_ind_botones[$i]['pregunta1'])?$referencia_titular_ind_botones[$i]['pregunta1']:''; ?>" 
                                    pregunta2="<?php echo  isset($referencia_titular_ind_botones[$i]['pregunta2'])?$referencia_titular_ind_botones[$i]['pregunta2']:''; ?>"
                                    pregunta3="<?php echo  isset($referencia_titular_ind_botones[$i]['pregunta3'])?$referencia_titular_ind_botones[$i]['pregunta3']:''; ?>" 
                                    pregunta4="<?php echo  isset($referencia_titular_ind_botones[$i]['pregunta4'])?$referencia_titular_ind_botones[$i]['pregunta4']:''; ?>"
                                    pregunta5="<?php echo  isset($referencia_titular_ind_botones[$i]['pregunta5'])?$referencia_titular_ind_botones[$i]['pregunta5']:''; ?>" 
                                    pregunta6="<?php echo  isset($referencia_titular_ind_botones[$i]['pregunta6'])?$referencia_titular_ind_botones[$i]['pregunta6']:''; ?>">
                                        <?php echo $i +1?>
                            </button>
                        <?php } ?>
                        <div id="boton_titular_ind"></div>
                    </form>                                       
                </div>

            <?php } else { ?>        
                <!-- Si es fuerzas armadas agrego documentacion necesaria -->
                <?php if ($solicitude['id_situacion'] == 7){ ?>
                <div class="col-md-12">
                    <p style="font-weight: bold;">Recordar solicitar: </p>
                     <ul> 
                         <li> Foto del carné de servicios de salud militar (El número debe coincidir con el número de cédula):   
                             <a href="#" onclick="$('#fotoCarnet').modal();"><i style="font-size: 20px;" class="fa fa-photo"></i></a>
                         </li>
                         <li> Desprendible de nómina de la quincena anterior:   
                             <a href="#" onclick="$('#desprendible').modal();"><i style="font-size: 20px;" class="fa fa-photo"></i></a>
                         </li>
                         <li> Preguntar algunas cosas que deberían saber y podemos contrastar con los documentos que nos dan:   
                             <a href="#" onclick="$('#rango').modal();"><i style="font-size: 20px;" class="fa fa-photo"></i><br></a>
                             <p style="margin-left:10px">a. ¿Cuál es su rango? (Lo podemos deducir del desprendible de nómina):</p>
                             <p style="margin-left:10px">b. ¿A qué batallón pertenece? (Sale en el desprenidble)</p>
                             <p style="margin-left:10px">c. ¿Cuánto tiempo lleva en el ejercito? (Sale en el carné de servicios de salud)</p>
                         </li>    
                         <li> Pedir fotos con el uniforme, donde se vea la insignia (que depende del rango y debería coincidir con el desprendible)</li>
                     </ul>                  
                </div> 
                <?php } ?>     
                <div class="col-xs-4">
                    <button id="verified_familiar" <?php echo $botones ?> class="btn btn-xs btn-warning button-gestion" title="Verificación familiar">
                        <i class="fa fa-check-square-o"></i>VERIFICACION FAMILIAR
                    </button>
                    <form id="form_familiar" <?php echo $visible_form ?>  <?php echo $botones ?>action="<?php echo base_url()?>api/ApiVerificacion/set_referencia_familiar" method="POST">
                        <input name="id_solicitud" type="hidden" value="<?php echo isset($solicitude['id'])?$solicitude['id']:''; ?>">
                        <div class="form-group"> 
                            <label for="nombre_familiar" class="control-label">Nombre de referencia: </label>
                            <input type="text" <?php echo $disabled_familiar ?> class="form-control pregunta1 disabled_familiar" value="<?php echo isset($ref_family['nombres_apellidos'])?$ref_family['nombres_apellidos']:''; ?>" id="nombre_familiar" name="nombre_familiar" placeholder="Ingrese Nombre de Familiar">
                        </div> 
                        <?php if ($solicitude['id_situacion'] != 4 && $solicitude['id_situacion'] != 5){ ?>  
                        <div class="form-group"> 
                            <label for="empresa_familiar" class="control-label">Empresa en la que trabaja: </label>
                            <input type="text" <?php echo $disabled_familiar ?> class="form-control pregunta2 disabled_familiar" id="empresa_familiar" name="empresa_familiar" placeholder="Ingrese Empresa">
                        </div> 
                        <?php } ?>  
                        <div class="form-group"> 
                            <label for="domicilio_familiar" class="control-label">Domicilio particular: </label>
                            <input type="text" <?php echo $disabled_familiar ?> class="form-control pregunta3 disabled_familiar" id="domicilio_familiar" name="domicilio_familiar" placeholder="Ingrese Dirección">
                        </div> 
                        <div class="form-group"> 
                            <label for="barrio_familiar" class="control-label">Barrio: </label>
                            <input type="text" <?php echo $disabled_familiar ?> class="form-control pregunta4 disabled_familiar" id="barrio_familiar" name="barrio_familiar" placeholder="Ingrese Barrio">   
                        </div> 
                        <?php if ($solicitude['id_situacion'] != 4 && $solicitude['id_situacion'] != 5){ ?>  
                        <div class="form-group"> 
                            <label for="tipo_trabajo_familiar" class="control-label">Hace cuanto trabaja en la empresa? : </label>
                            <input type="text" <?php echo $disabled_familiar ?> class="form-control pregunta5 disabled_familiar" id="tipo_trabajo_familiar" name="tipo_trabajo_familiar" placeholder="Ingrese Tiempo">
                        </div> 
                        <?php } ?>  
                        <div class="form-group"> <!-- Submit Button -->                            
                            <button type="submit" <?php echo $disabled_familiar ?> class="btn btn-primary disabled_familiar">Guardar</button>
                            <button type="button" <?php echo $disabled_familiar ?> class="btn btn-warning disabled_familiar" onclick="limpiarFormularioFamiliar()">Nueva Verificación</button>
                        </div> 
                        <input hidden id="cantidad_familiar" value="<?php echo count($referencia_familiar_botones)?>">
                        <!-- Listar las referencias en botones para mostrar cada una -->
                        <?php for ($i = 0; $i < count($referencia_familiar_botones) ; $i++) {?>
                            <button type="button" class="btn btn-warning datos_verificacion"
                                    pregunta1="<?php echo  isset($referencia_familiar_botones[$i]['pregunta1'])?$referencia_familiar_botones[$i]['pregunta1']:''; ?>" 
                                    pregunta2="<?php echo  isset($referencia_familiar_botones[$i]['pregunta2'])?$referencia_familiar_botones[$i]['pregunta2']:''; ?>"
                                    pregunta3="<?php echo  isset($referencia_familiar_botones[$i]['pregunta3'])?$referencia_familiar_botones[$i]['pregunta3']:''; ?>" 
                                    pregunta4="<?php echo  isset($referencia_familiar_botones[$i]['pregunta4'])?$referencia_familiar_botones[$i]['pregunta4']:''; ?>"
                                    pregunta5="<?php echo  isset($referencia_familiar_botones[$i]['pregunta5'])?$referencia_familiar_botones[$i]['pregunta5']:''; ?>" 
                                    pregunta6="<?php echo  isset($referencia_familiar_botones[$i]['pregunta6'])?$referencia_familiar_botones[$i]['pregunta6']:''; ?>">
                                        <?php echo $i +1?>
                            </button>
                        <?php } ?>  
                        <div id="boton_familiar"></div>
                    </form> 
                </div> 
           <!-- Si es empleado se piden los datos laborales -->
            <?php if ($solicitude['id_situacion'] == 1 || $solicitude['id_situacion'] == 7){ ?>               
               <div class="col-xs-4">
                     <button id="verified_laboral" <?php echo $botones ?> class="btn btn-xs btn-success button-gestion" title="Verificación laboral">
                         <i class="fa fa-check-square-o"></i>VERIFICACION LABORAL
                     </button>
                    <form id="form_laboral" <?php echo $visible_form ?> action="<?php echo base_url()?>api/ApiVerificacion/set_referencia_laboral" method="POST">
                        <input name="id_solicitud" <?php echo $disabled_laboral ?> type="hidden" value="<?php echo isset($solicitude['id'])?$solicitude['id']:''; ?>">
                        <div class="form-group"> 
                            <label for="empresa_laboral" class="control-label">Empresa en la que trabaja: </label>
                            <input type="text"  <?php echo $disabled_laboral ?> class="form-control pregunta1 disabled_laboral" id="empresa_laboral" name="empresa_laboral" placeholder="Ingrese Empresa">
                        </div>  
                        <div class="form-group"> 
                            <label for="tipo_trabajo_laboral" class="control-label">Hace cuanto trabaja en la empresa? : </label>
                            <input type="text"  <?php echo $disabled_laboral ?> class="form-control pregunta2 disabled_laboral" id="tipo_trabajo_laboral" name="tipo_trabajo_laboral" placeholder="Ingrese Tiempo">
                        </div> 
                        <div class="form-group"> 
                            <label for="puesto" class="control-label">Puesto / Cargo : </label>
                            <input type="text"  <?php echo $disabled_laboral ?> class="form-control pregunta3 disabled_laboral" id="puesto" name="puesto" placeholder="Ingrese Puesto o Cargo">
                        </div> 
                        <div class="form-group"> 
                            <label for="caja" class="control-label">Caja de Compensación : </label>
                            <input type="text"  <?php echo $disabled_laboral ?> class="form-control pregunta4 disabled_laboral" id="caja" name="caja" placeholder="Ingrese Caja de Compensación">
                        </div> 
                        <div class="form-group"> <!-- Submit Button -->
                            <button type="submit"  <?php echo $disabled_laboral ?> class="btn btn-primary disabled_laboral">Guardar</button>
                            <button type="button"  <?php echo $disabled_laboral ?> class="btn btn-success disabled_laboral" onclick="limpiarFormularioLaboral()">Nueva Verificación</button>
                        </div>
                        <input hidden id="cantidad_laboral" value="<?php echo count($referencia_laboral_botones)?>">
                        <!-- Listar las referencias en botones para mostrar cada una -->
                        <?php for ($i = 0; $i < count($referencia_laboral_botones) ; $i++) {?>
                            <button type="button" class="btn btn-success datos_verificacion" 
                                    pregunta1="<?php echo  isset($referencia_laboral_botones[$i]['pregunta1'])?$referencia_laboral_botones[$i]['pregunta1']:''; ?>" 
                                    pregunta2="<?php echo  isset($referencia_laboral_botones[$i]['pregunta2'])?$referencia_laboral_botones[$i]['pregunta2']:''; ?>"
                                    pregunta3="<?php echo  isset($referencia_laboral_botones[$i]['pregunta3'])?$referencia_laboral_botones[$i]['pregunta3']:''; ?>" 
                                    pregunta4="<?php echo  isset($referencia_laboral_botones[$i]['pregunta4'])?$referencia_laboral_botones[$i]['pregunta4']:''; ?>"
                                    pregunta5="<?php echo  isset($referencia_laboral_botones[$i]['pregunta5'])?$referencia_laboral_botones[$i]['pregunta5']:''; ?>" 
                                    pregunta6="<?php echo  isset($referencia_laboral_botones[$i]['pregunta6'])?$referencia_laboral_botones[$i]['pregunta6']:''; ?>">
                                        <?php echo $i +1?>
                            </button>
                        <?php } ?>
                        <div id="boton_laboral"></div>
                    </form>
                 </div>                
            <?php } else{ ?>
                <div class="col-xs-4">
                    <button id="verified_personal" <?php echo $botones ?> class="btn btn-xs btn-success button-gestion" title="Verificación personal">
                        <i class="fa fa-check-square-o"></i>VERIFICACION PERSONAL
                    </button>
                    <form id="form_personal" <?php echo $visible_form ?> action="<?php echo base_url()?>api/ApiVerificacion/set_referencia_personal" method="POST">
                        <input name="id_solicitud" type="hidden" value="<?php echo isset($solicitude['id'])?$solicitude['id']:''; ?>">
                        <div class="form-group"> 
                            <label for="nombre_personal" class="control-label">Nombre de referencia: </label>
                            <input type="text" class="form-control pregunta1 disabled_personal" <?php echo $disabled_personal ?> value="<?php echo isset($ref_personal['nombres_apellidos'])?$ref_personal['nombres_apellidos']:''; ?>" id="nombre_personal" name="nombre_personal" placeholder="Ingrese Nombre de Referencia Personal">
                        </div> 
                        <?php if ($solicitude['id_situacion'] != 4 && $solicitude['id_situacion'] != 5){ ?>  
                        <div class="form-group"> 
                            <label for="empresa_personal" class="control-label">Empresa en la que trabaja: </label>
                            <input type="text" class="form-control pregunta2 disabled_personal" <?php echo $disabled_personal ?> id="empresa_personal" name="empresa_personal" placeholder="Ingrese Empresa">
                        </div> 
                        <?php } ?>  
                        <div class="form-group"> 
                            <label for="domicilio_personal" class="control-label">Domicilio particular: </label>
                            <input type="text" class="form-control pregunta3 disabled_personal" <?php echo $disabled_personal ?> id="domicilio_personal" name="domicilio_personal" placeholder="Ingrese Dirección">
                        </div> 
                        <div class="form-group"> 
                            <label for="barrio_personal" class="control-label">Barrio: </label>
                            <input type="text" class="form-control pregunta4 disabled_personal" <?php echo $disabled_personal ?> id="barrio_personal" name="barrio_personal" placeholder="Ingrese Barrio">   
                        </div> 
                        <?php if ($solicitude['id_situacion'] != 4 && $solicitude['id_situacion'] != 5){ ?>  
                        <div class="form-group"> 
                            <label for="tipo_trabajo_personal" class="control-label">Hace cuanto trabaja en la empresa? : </label>
                            <input type="text" class="form-control pregunta5 disabled_personal" <?php echo $disabled_personal ?> id="tipo_trabajo_personal" name="tipo_trabajo_personal" placeholder="Ingrese Tiempo">
                        </div> 
                        <?php } ?>  
                        <div class="form-group"> <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary disabled_personal" <?php echo $disabled_personal ?>>Guardar</button>
                            <button type="button" class="btn btn-success disabled_personal" <?php echo $disabled_personal ?> onclick="limpiarFormularioPersonal()">Nueva Verificación</button>
                        </div>
                        <input hidden id="cantidad_personal" value="<?php echo count($referencia_personal_botones)?>">
                        <!-- Listar las referencias en botones para mostrar cada una -->
                        <?php for ($i = 0; $i < count($referencia_personal_botones) ; $i++) {?>
                            <button type="button" class="btn btn-success datos_verificacion" 
                                    pregunta1="<?php echo  isset($referencia_personal_botones[$i]['pregunta1'])?$referencia_personal_botones[$i]['pregunta1']:''; ?>" 
                                    pregunta2="<?php echo  isset($referencia_personal_botones[$i]['pregunta2'])?$referencia_personal_botones[$i]['pregunta2']:''; ?>"
                                    pregunta3="<?php echo  isset($referencia_personal_botones[$i]['pregunta3'])?$referencia_personal_botones[$i]['pregunta3']:''; ?>" 
                                    pregunta4="<?php echo  isset($referencia_personal_botones[$i]['pregunta4'])?$referencia_personal_botones[$i]['pregunta4']:''; ?>"
                                    pregunta5="<?php echo  isset($referencia_personal_botones[$i]['pregunta5'])?$referencia_personal_botones[$i]['pregunta5']:''; ?>" 
                                    pregunta6="<?php echo  isset($referencia_personal_botones[$i]['pregunta6'])?$referencia_personal_botones[$i]['pregunta6']:''; ?>">
                                        <?php echo $i +1?>
                            </button>
                        <?php } ?>
                       <div id="boton_personal"></div>
                    </form>
                 </div>              
                <?php } ?>                
                    <div class="col-xs-4">
                    <button id="verified_titular" <?php echo $botones ?> class="btn btn-xs btn-info button-gestion" title="Verificación de titular">
                        <i class="fa fa-check-square-o"></i>VERIFICACION TITULAR
                    </button>
                    <form id="form_titular" <?php echo $visible_form ?> action="<?php echo base_url()?>api/ApiVerificacion/set_referencia_titular" method="POST">
                        <input name="id_solicitud" type="hidden" value="<?php echo isset($solicitude['id'])?$solicitude['id']:''; ?>">
                        <div class="form-group"> 
                            <label for="telefono_titular" class="control-label">Teléfono: </label>
                            <input type="text" class="form-control" id="telefono_titular" value="<?php echo isset($solicitude['telefono'])?$solicitude['telefono']:''; ?>" disabled="disabled">
                        </div> 
                        <?php if ($solicitude['id_situacion'] != 4 && $solicitude['id_situacion'] != 5){ ?>  
                        <div class="form-group"> 
                            <label for="empresa" class="control-label">Empresa en la que trabaja: </label>
                            <input type="text" class="form-control pregunta1 disabled_titular" <?php echo $disabled_titular ?> id="empresa" name="empresa" placeholder="Ingrese Empresa">
                        </div> 
                        <?php } ?>  
                        <div class="form-group"> 
                            <label for="domicilio" class="control-label">Domicilio particular: </label>
                            <input type="text" class="form-control pregunta2 disabled_titular" <?php echo $disabled_titular ?> id="domicilio" name="domicilio" placeholder="Ingrese Dirección">
                        </div> 
                        <div class="form-group"> 
                            <label for="barrio" class="control-label">Barrio: </label>
                            <input type="text" class="form-control pregunta3 disabled_titular" <?php echo $disabled_titular ?> id="barrio" name="barrio" placeholder="Ingrese Barrio">      
                        </div> 
                        <?php if ($solicitude['id_situacion'] != 4 && $solicitude['id_situacion'] != 5){ ?>  
                        <div class="form-group"> 
                            <label for="tipo_trabajo" class="control-label">Hace cuanto trabaja en la empresa? : </label>
                            <input type="text" class="form-control pregunta4 disabled_titular" <?php echo $disabled_titular ?> id="tipo_trabajo" name="tipo_trabajo" placeholder="Ingrese Tiempo">
                        </div> 
                        <?php } ?>  
                        <div class="form-group"> 
                            <label for="signo" class="control-label">De qué signo es? : </label>
                            <input type="text" class="form-control pregunta5 disabled_titular" <?php echo $disabled_titular ?> id="signo" name="signo" placeholder="Ingrese Signo">
                        </div> 
                        <div class="form-group"> <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary disabled_titular" <?php echo $disabled_titular ?>>Guardar</button>
                            <button type="button" class="btn btn-info disabled_titular" <?php echo $disabled_titular ?> id="nueva" onclick="limpiarFormularioTitular()">Nueva Verificación</button>
                        </div>  
                        <input hidden id="cantidad_titular" value="<?php echo count($referencia_titular_botones)?>">
                        <!-- Listar las referencias en botones para mostrar cada una -->
                        <?php for ($i = 0; $i < count($referencia_titular_botones) ; $i++) {?>
                        <button type="button" class="btn btn-info datos_verificacion" 
                                pregunta1="<?php echo  isset($referencia_titular_botones[$i]['pregunta1'])?$referencia_titular_botones[$i]['pregunta1']:''; ?>" 
                                pregunta2="<?php echo  isset($referencia_titular_botones[$i]['pregunta2'])?$referencia_titular_botones[$i]['pregunta2']:''; ?>"
                                pregunta3="<?php echo  isset($referencia_titular_botones[$i]['pregunta3'])?$referencia_titular_botones[$i]['pregunta3']:''; ?>" 
                                pregunta4="<?php echo  isset($referencia_titular_botones[$i]['pregunta4'])?$referencia_titular_botones[$i]['pregunta4']:''; ?>"
                                pregunta5="<?php echo  isset($referencia_titular_botones[$i]['pregunta5'])?$referencia_titular_botones[$i]['pregunta5']:''; ?>" 
                                pregunta6="<?php echo  isset($referencia_titular_botones[$i]['pregunta6'])?$referencia_titular_botones[$i]['pregunta6']:''; ?>">
                                    <?php echo $i +1?>
                        </button>
                        <?php } ?>
                        <div id="boton_titular"></div>
                    </form>                                       
                </div>
                <div id="boton_titular"></div>  
                <div class="col-xs-1"></div>
           </div>
           <?php } ?>   
        </div>
    </div>
</div>
<!-- Modal de Foto Carnet -->
<section class="modal-content">    
    <div class="modal fade" id="fotoCarnet" tabindex="-1" role="dialog" aria-labelledby="fotoCarnet" aria-hidden="true">
      <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><strong>DETALLE: </strong> Solicitar foto del carné de servicios de salud militar (El número debe coincidir con el número de cédula):</h5>          
          </div>
          <div class="modal-body">              
            <img src="<?php echo base_url()?>public/IMAGENES_FUERZA/foto_carnet.jpg"   style="height: unset;width:100%" >
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</section>
<!-- Modal desprendible -->
<section class="modal-content">    
    <div class="modal fade" id="desprendible" tabindex="-1" role="dialog" aria-labelledby="desprendible" aria-hidden="true">
      <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><strong>DETALLE: </strong> Desprendible de nómina de la quincena anterior:</h5>          
          </div>
          <div class="modal-body">              
            <img src="<?php echo base_url()?>public/IMAGENES_FUERZA/desprendible.png"   style="height: unset;width:100%" >
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</section>
<!-- Modal rango -->
<section class="modal-content">    
    <div class="modal fade" id="rango" tabindex="-1" role="dialog" aria-labelledby="rango" aria-hidden="true">
      <div class="modal-dialog" role="document" style="width: 70%">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><strong>DETALLE: </strong> Rango</h5>          
          </div>
          <div class="modal-body">              
            <img src="<?php echo base_url()?>public/IMAGENES_FUERZA/rango.png"   style="height: unset;width:100%" >
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
</section>

<script src="<?php echo base_url('assets/gestion/verificacion.js'); ?>"></script>
           
