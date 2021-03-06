<style>
	 ayudaForm {
		margin-bottom: 1em!important;
        font-size: 14px!important;
        font-weight: 400;
        color: #777!important;
        
    }
    div.ayuda {
        margin-top: 1%;
        color: #777!important;
    }

    .ayudaForm:last-child {
        margin-bottom: 0%!important;
    }

    #formDatosBasicos > div.box-body > div:nth-child(1) > div:nth-child(12) > label {
        text-align: left!important;

    }

    #formDatosBasicos > div.box-body > div:nth-child(1) > div:nth-child(6) > label {
        padding-top: 0!important;
        margin-bottom: 0;
        text-align: left!important;
    }
    .form-horizontal .control-label {
        text-align: left!important;
    }
	.card-guia-ayuda{
		padding: 0;
		margin-bottom: 1em;
		margin-top: 1em;
		background-color: #fbff001f!important;
        height: auto;
        border-radius: 0.35em;
    
    }
    
    #collapseOne > div 
    {
        padding: 15px;
    }
    
    #editConfiguracion {
        box-shadow: none!important;
    }
    

    .box-body {
        padding-top: 16px;
    }
    #headingOne > button {
        color: #777!important;
        text-decoration: none!important;
        background-color: transparent;
        font-weight: 600;
        font-size: 18px!important;
    }
    #headingOne > button:hover {
        font-weight: 700;
    }

    
	.for-uni{
         display: -webkit-box;
         display: -ms-flexbox;
         display: flex;
         -webkit-box-align: center;
         -ms-flex-align: center;
         align-items: center;
         padding: 0.375rem 0.75rem;
         margin-bottom: 0;
         font-size: 1rem;
         font-weight: 400;
         line-height: 1.5;
         color: #495057;
         text-align: center;
         white-space: nowrap;
         background-color: #e9ecef;
         border: 1px solid #ced4da;
         /* border-radius: 0.25rem; */
         border-top-left-radius: 0.25rem;
         border-bottom-left-radius: 0.25rem;
    }
</style>
<input type="hidden" id="idConfig" value="<?php echo $config[0]->id; ?>">

<div class="box box-info" id="editConfiguracion"  style="background: #E8DAEF;">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Registrar configuraci??n - Gesti??n Obligatoria</strong></small></h6>
    </div>
    <main role="main">
        <section id="formSection">
            <div class="login-box-body" style="height: auto; text-align: left;  font-size: 12px; margin-bottom: -10px;">
                <fieldset>
                    <form class="form-horizontal" id="formDatosBasicos">
                        <div class="box-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                            
								<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Si la parametrizaci??n est?? activa o no.">Estado de la campa??a</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="estado" name="estado" >
                                                <option value="1" <?php echo (strtoupper($config[0]->estado) == "1")? 'selected':'' ?>>Activo</option>
                                                <option value="0" <?php echo (strtoupper($config[0]->estado) == "0")? 'selected':'' ?>>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="form-group">
									<label  class="col-sm-4 control-label has-tip" data-tip="Operadores que ser??n afectado por la parametrizaci??n.">Operadores en gesti??n</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="tipoOperador" name="tipoOperador" >
											<option value=''></option>";
											<?php 
												 $tipo_operadores_permitidos = array(1,4);
												 foreach ($tipo_operador_list as $key => $value) {
													//  if(in_array($key, $tipo_operadores_permitidos)){
														if ($config[0]->tipo_operador == $value['idtipo_operador']) {
															echo "<option value='{$value['idtipo_operador']}' selected>{$value['descripcion']}</option>";
														} else {
															echo "<option value='{$value['idtipo_operador']}'>{$value['descripcion']}</option>";
														}
															
													//  }
												 }	
											?>
                                            </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Periodo en el cual se gestionar??n las solicitudes obligatorias">Duraci??n de campa??a autom??tica</label>
                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->min_proceso_obligatorio)) ?>" class="form-control" id="minProcesoObligatorio" placeholder="Ingrese en minutos la duraci??n. Ej: 30" autocomplete="off">
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Intervalo de tiempo en dias para filtrar solicitudes obligatorias.">D??as de b??squeda</label>
                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">D??as</div>
                                        </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->dias_busqueda)) ?>" class="form-control" id="diasBusqueda" placeholder="Ingrese los d??as para buscar solicitudes. Ej. 4" autocomplete="off">
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Tiempo que dura la solicitud abierta.">Tiempo de gesti??n</label>
                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="text" value="<?php echo (strtoupper($config[0]->min_gestion)) ?>" class="form-control" id="minGestion" placeholder="Ingrese en minutos el tiempo de gesti??n. Ej 30" autocomplete="off">
                                    </div>
                                   
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Si permite extensi??n: mas tiempo para gestionar una solicitud.">Extensiones consecutivas</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="extensionesConsecutivas" name="extensionesConsecutivas">
                                                    <option value="1" <?php echo (strtoupper($config[0]->extensiones_consecutivas) == "1")? 'selected':'' ?>>Activas</option>
                                                    <option value="0" <?php echo (strtoupper($config[0]->extensiones_consecutivas) == "0")? 'selected':'' ?> >Inactivas</option>
                                            </select>
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label  class="col-sm-4 control-label has-tip" data-tip="Este campo se requiere filtrar los distintos tipos de origenes para realizar la busqueda!">Tiempo de extensi??n de solicitud</label>
									<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->min_extension)) ?>" class="form-control" id="minutosExtension" placeholder="Ingrese cuantos minutos quiere extender. Ej. 2" autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="form-group">
								<label for="estado" class="col-sm-4 control-label has-tip" data-tip="Tiempo de espera para actualizar lista de solicitudes en proceso actual, para mantener la prioridad actualizada.">Periodo actualizaci??n de solicitudes</label>
									<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="text" value="<?php echo (strtoupper($config[0]->min_get_solicitudes)) ?>" class="form-control" id="minSolicitud" placeholder=" Ingrese cada cuantos minutos se actualiza. Ej. 30." autocomplete="off">
                                    </div>
                                   
                                </div>
							
                                
                            </div>
                            <div class="col-md-6 col-lg-6">                                
								<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Intervalo de tiempo en horas para seguimiento(track) y recuperar las solicitudes.">Periodo ??ltimas gestionadas</label>
									<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Hrs&nbsp;</div>
                                        </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->horas_ultima_gestion)) ?>" class="form-control" id="horaUltimaGestion" placeholder="Ingrese cada cuantas horas se consulta. Ej 5" autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Tiempo para gestionar los chats; usado tambi??n, para dar descanso al operador">Tiempo de gesti??n de chats</label>
									<div class="col-sm-8" style="display: flex;">
									<div class="input-group-prepend" style="display: flex;">
                                        <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                    </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->min_gestion_chats)) ?>" class="form-control" id="minGestionChats" placeholder="Ingrese cuantos minutos para gestionar chats. Ej. 10" autocomplete="off">
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Intervalo de tiempo en minutos para filtrar chats con solicitudes obligatorias.">Periodo de actualizaci??n documentos</label>
									<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->min_chat_documentos)) ?>" class="form-control" id="minDocChats" placeholder="Ingrese cada cuantos minutos se evaluan chats con doc. Ej. 5" autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="form-group">
								<label for="estado" class="col-sm-4 control-label has-tip" data-tip="Tiempo de espera para que el operador empiece a procesar las solicitudes obligatorias">Tiempo de preparaci??n</label>
									<div class="col-sm-8" style="display: flex;">
	                                         <div class="input-group-prepend" style="display: flex;">
	                                             <div class="input-group-text for-uni">&nbsp;Seg&nbsp;</div>
	                                        </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->seg_ejecucion)) ?>" class="form-control" id="segEjecucion" placeholder="Segundos de preparaci??n" autocomplete="off" disabled>               
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Porcentaje en el que se torna amarilla la barra de progreso de tiempo.">Tiempo estado alerta</label>
									<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;&nbsp;&nbsp;%&nbsp;&nbsp;&nbsp;</div>
                                        </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->porcentaje_warning)) ?>" class="form-control" id="porcentajeAlerta" placeholder="Porcentaje de tiempo alerta" autocomplete="off" disabled>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="porcentaje en el que se torna roja la barra de progreso de tiempo.">Tiempo estado preventivo</label>
									<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;&nbsp;&nbsp;%&nbsp;&nbsp;&nbsp;</div>
                                        </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->porcentaje_alerta_extension)) ?>" class="form-control" id="porcentajePreventivo" placeholder="Porcentaje de tiempo preventivo" autocomplete="off" disabled>
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Duraci??n de mostrar alerta">Tiempo ventana de alerta</label>
                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Seg&nbsp;</div>
                                        </div>
                                        <input type="number" value="<?php echo (strtoupper($config[0]->segundos_alert_ext)) ?>" class="form-control" id="segundosAlerta" placeholder="Segundos de alerta y extensi??n" autocomplete="off" disabled>
                                    </div>
                                </div>
                            
                               
                            </div>
						</div>
                            
                        <!-- /.box-body -->
                 

                        <div class="col-sm-12" style="margin-top: -3px;">
                            <div class="box-footer col-sm-6" style="text-align: center;">
                               
                                <button type="button" class="btn btn-primary col-sm-4 pull-right" id="" onclick="editConfig();">Actualizar
                                </button>
                            </div>
                            <div class="box-footer col-sm-6">
                                <button type="button" class="btn btn-default col-sm-4" onclick="cancelar();"> Cancelar</button>
                            </div>
                        </div>
                        <!-- /.box-footer -->
                    </form>
                </fieldset>
				<div class="col-sm-12 card-guia-ayuda">
                        <div id="accordion" class="alert ayuda">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <i class="fa fa-folder-open-o" aria-hidden="true" style="font-size:14px!important"></i>  Gu&iacute;a de ayuda</i>
                                    </button> 
                                </div>
                
                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                               <!-- <div class="card-body" > -->
							   <div class="card-body col-md-6">
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Estado de la campa??a:</strong> Condici??n del estado de la campa??a obligatoria.</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Operadores en gesti??n:</strong> Grupo de operadores a los que se aplica la gesti??n autom??tica.</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Duraci??n de campa??a autom??tica:</strong> Periodo de tiempo durante el cual se mostrar??n las solicitudes obligatorias continuamente. (Min)</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> D??as de b??squeda:</strong> D??as de antiguedad en el que se buscan las solicitudes para la gesti??n. (D??as)</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de gesti??n:</strong> Tiempo m??ximo que se le asigna a un operador para gestionar un caso. (Min)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Extensiones consecutivas:</strong> Condici??n para permitirle al operador extender el tiempo de la solicitud, m??s de una vez.(Activa)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de extensi??n de solicitud:</strong> Tiempo establecido para cuando un operador solicita m??s tiempo en una solicitud.</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Periodo actualizaci??n de solicitudes:</strong> Periodo de tiempo en el que se eval??an las prioridades y actualizan las solicitudes. (Min)</p>
                                        
                                    </div>
                                    <div class="card-body col-md-6">
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Periodo ??ltimas gestionadas:</strong> Periodo de tiempo en el que se vuelve a consultar una solicitud ya gestionada.</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de gesti??n de chats:</strong> Tiempo en que los operadores est??n desconectados de la campa??a para gestionar los chats. (Min)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Periodo de actualizaci??n documentos:</strong> Periodo de tiempo en el que se evaluan los chats que disponen de documentos para priorizar. (Min)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de preparaci??n:</strong> Tiempo previo a iniciar las solicitudes autom??ticas. (Seg)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo estado alerta:</strong> Tiempo establecido para que la barra superior de estado cambie a color amarillo. (Porcentaje)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo estado preventivo:</strong> Tiempo establecido para que la barra superior de estado cambie a color rojo. (Porcentaje)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo ventana de alerta:</strong> Tiempo que permanecer?? la ventana de solicitud de extensi??n abierta (Seg)</p>
                                    </div>
                                    
                                <!-- </div> -->
                            </div>
                        </div>
                            
                    </div>
                
                
            </div>
        </section>
    </main>
</div>
