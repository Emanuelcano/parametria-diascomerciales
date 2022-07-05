<style>
    .ayudaForm {
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

    #nuevaConfig {
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
<div class="box box-info" id="nuevaConfig"  style="background: #E8DAEF;">
    <div class="box-header with-border" id="titulo">
        <h6 class="box-title"><small><strong>Registrar configuración - Gestión Obligatoria</strong></small></h6>
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
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Si la parametrización está activa o no.">Estado de la campaña</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="estado" name="estado">
                                                <option value="1">Activo</option>
                                                <option value="0" selected>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            
								<div class="form-group">
									<label  class="col-sm-4 control-label has-tip" data-tip="Operadores que serán afectado por la parametrización.">Operadores en gestión</label>
                                    <div class="col-lg-8 col-sm-8">
                                        <select class="form-control" id="tipoOperador" name="tipoOperador">
											<option value='' selected>Selecciona el tipo de operador</option>;
											<?php 
												 $tipo_operadores_permitidos = array(1,4);
												 foreach ($tipo_operador_list as $key => $value) {
													//  if(in_array($key, $tipo_operadores_permitidos)){
														 echo "<option value='{$value['idtipo_operador']}'>{$value['descripcion']}</option>";
													//  }
												 }	
											?>
                                            </select>
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Periodo en el cual se gestionarán las solicitudes obligatorias">Duración de campaña automática</label>
									<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="minProcesoObligatorio" placeholder="Ingrese en minutos la duración. Ej: 30" autocomplete="off">
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Intervalo de tiempo en dias para filtrar solicitudes obligatorias.">Días de búsqueda</label>

                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">Días</div>
                                        </div>
                                        <input type="number" class="form-control" id="diasBusqueda" placeholder="Ingrese los días para buscar solicitudes. Ej. 4" autocomplete="off">
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Tiempo que dura la solicitud abierta.">Tiempo de gestión</label>
                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="minGestion" placeholder="Ingrese en minutos el tiempo de gestión. Ej 30" autocomplete="off">
                                    </div>
                                   
                                </div>
                                
								<div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Si permite extensión: mas tiempo para gestionar una solicitud.">Extensiones consecutivas</label>

									<div class="col-sm-8">
                                        <select class="form-control" id="extensionesConsecutivas" name="extensionesConsecutivas" >
                                                    <option value="" selected>Seleccione si desea activar las extensiones</option>
                                                    <option value="1">Activas</option>
                                                    <option value="0">Inactivas</option>
                                            </select>
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label  class="col-sm-4 control-label has-tip" data-tip="Este campo se requiere filtrar los distintos tipos de origenes para realizar la busqueda!">Tiempo de extensión de solicitud</label>

                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="minutosExtension" placeholder="Ingrese cuantos minutos quiere extender. Ej. 2" autocomplete="off">
                                    </div>
                                </div>
                                
								<div class="form-group">
								<label for="estado" class="col-sm-4 control-label has-tip" data-tip="Tiempo de espera para actualizar lista de solicitudes en proceso actual, para mantener la prioridad actualizada.">Periodo actualización de solicitudes</label>
								<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="minSolicitud" placeholder="Ingrese cada cuantos minutos se actualiza. Ej. 30." autocomplete="off">
                                    </div>
                                   
                                </div>

                                
							</div>
							<div class="col-md-6 col-lg-6">
    
							<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Intervalo de tiempo en horas para seguimiento(track) y recuperar las solicitudes.">Periodo últimas gestionadas</label>
                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Hrs&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="horaUltimaGestion" placeholder="Ingrese cada cuantas horas se consulta. Ej 5" autocomplete="off">
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Tiempo para gestionar los chats; usado también, para dar descanso al operador">Tiempo de gestión de chats</label>

                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="minGestionChats" placeholder="Ingrese cuantos minutos para gestionar chats. Ej. 10" autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="col-sm-4 control-label has-tip" data-tip="Intervalo de tiempo en minutos para filtrar chats con solicitudes obligatorias.">Periodo de actualización documentos</label>

                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Min&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="minDocChats" placeholder="Ingrese cada cuantos minutos se evaluan chats con doc. Ej. 5" autocomplete="off">
                                    </div>
                                </div>
                                
								<div class="form-group">
								<label for="estado" class="col-sm-4 control-label has-tip" data-tip="Tiempo de espera para que el operador empiece a procesar las solicitudes obligatorias">Tiempo de preparación</label>
								<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Seg&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="segEjecucion" value="15" placeholder="Tiempo de preparación" autocomplete="off" disabled>
                                    </div>
                                </div>

								<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Porcentaje en el que se torna amarilla la barra de progreso de tiempo.">Tiempo estado alerta</label>
                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;&nbsp;&nbsp;%&nbsp;&nbsp;&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="porcentajeAlerta" value="30" placeholder="Tiempo estado alerta" autocomplete="off" disabled>
                                    </div>
                                </div>
                                
								<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="porcentaje en el que se torna roja la barra de progreso de tiempo.">Tiempo estado preventivo</label>
                                    <div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;&nbsp;&nbsp;%&nbsp;&nbsp;&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="porcentajePreventivo" value="10" placeholder="Tiempo estado preventivo" autocomplete="off" disabled>
                                    </div>
                                </div>
                                  
								<div class="form-group">
                                    <label for="estado" class="col-sm-4 control-label has-tip" data-tip="Duración de mostrar alerta">Tiempo ventana de alerta</label>
									<div class="col-sm-8" style="display: flex;">
                                         <div class="input-group-prepend" style="display: flex;">
                                             <div class="input-group-text for-uni">&nbsp;Seg&nbsp;</div>
                                        </div>
                                        <input type="number" class="form-control" id="segundosAlerta" value="30" placeholder="Tiempo ventana de alerta" autocomplete="off" disabled>
                                    </div>
                                </div>                             
                              
							</div>
                        </div>
                            
                           
                            
                        <!-- /.box-body -->

                        <div class="col-sm-12" style="margin-top: -3px;">
                            <div class="box-footer col-sm-6" style="text-align: center;">
                               
                                <button type="button" class="btn btn-primary col-sm-4 pull-right" id=""
                                    onclick="registrarConfiguracion();">Registrar
                                </button>
                            </div>
                            <div class="box-footer col-sm-6">
                                <button type="button" class="btn btn-default col-sm-4" onclick="cancelar();" >Cancelar</button>
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
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Estado de la campaña:</strong> Condición del estado de la campaña obligatoria.</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Operadores en gestión:</strong> Grupo de operadores a los que se aplica la gestión automática.</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Duración de campaña automática:</strong> Periodo de tiempo durante el cual se mostrarán las solicitudes obligatorias continuamente. (Min)</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Días de búsqueda:</strong> Días de antiguedad en el que se buscan las solicitudes para la gestión. (Días)</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de gestión:</strong> Tiempo máximo que se le asigna a un operador para gestionar un caso. (Min)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Extensiones consecutivas:</strong> Condición para permitirle al operador extender el tiempo de la solicitud, más de una vez.(Activa)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de extensión de solicitud:</strong> Tiempo establecido para cuando un operador solicita más tiempo en una solicitud.</p>
                                        <p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Periodo actualización de solicitudes:</strong> Periodo de tiempo en el que se evalúan las prioridades y actualizan las solicitudes. (Min)</p>
                                        
                                    </div>
                                    <div class="card-body col-md-6">
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Periodo últimas gestionadas:</strong> Periodo de tiempo en el que se vuelve a consultar una solicitud ya gestionada.</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de gestión de chats:</strong> Tiempo en que los operadores están desconectados de la campaña para gestionar los chats. (Min)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Periodo de actualización documentos:</strong> Periodo de tiempo en el que se evaluan los chats que disponen de documentos para priorizar. (Min)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo de preparación:</strong> Tiempo previo a iniciar las solicitudes automáticas. (Seg)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo estado alerta:</strong> Tiempo establecido para que la barra superior de estado cambie a color amarillo. (Porcentaje)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo estado preventivo:</strong> Tiempo establecido para que la barra superior de estado cambie a color rojo. (Porcentaje)</p>
										<p class="ayudaForm"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<strong> Tiempo ventana de alerta:</strong> Tiempo que permanecerá la ventana de solicitud de extensión abierta (Seg)</p>
                                    </div>
                                    
                                <!-- </div> -->
                            </div>
                        </div>
                            
                    </div>
                
                
            </div>
        </section>
    </main>

</div>
