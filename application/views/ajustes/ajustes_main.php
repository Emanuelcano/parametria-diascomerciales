
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>">
<input type="hidden" id="id_solicitud" name="id_solicitud" value="0">
<input type="hidden" id="operador_nombre" value="<?php echo isset($_SESSION['user']->first_name) ? $_SESSION['user']->first_name : '';?> <?php echo isset($_SESSION['user']->last_name) ? $_SESSION['user']->last_name :'';?>">
<input type="hidden" id="cuenta_antigua" value="">
<input type="hidden" id="tipo_cuenta_antigua" value="">
<input type="hidden" id="banco_antiguo" value="">
<input type="hidden" id="buro" value="">

<!-- Esto es para que el header no superponga el buscador -->
<div class="box-header with-border" class="col-lg-12"><div class="col-md-12">&nbsp;</div></div>

<div id="dashboard_principal" style="display: block; background: #FFFFFF; padding:10px;">
    <div class="box-header with-border row">
        
        <div class="col-md-3 form-group" id="section_search_solicitud">
                
            <label for="search">Buscar solicitud por ID: </label>
            <input id="search" name="search" type="text" class="form-control" placeholder="ID"> 
        </div>

        <button id="buscar" type="button" class="btn btn-info col-sm-1" title="Buscar" style="font-size: 12px;    margin-top: 25px;"><i class="fa fa-search"></i> Buscar</button>
        <button id="reset" type="button" class="btn btn-default col-sm-1" title="Limpiar" style="font-size: 12px;    margin-top: 25px;"><i class="fa fa- fa-remove"></i> Limpiar</button>
               
                
    </div>
    <div class="col-sm-12" id="section_table_solicitud_ajustes" >       
        <table class="table table-striped table-bordered text-center" id="table-solicitud-ajustes" style="width: 100%">
            <thead>
                <th>Fecha Solicitud Ajuste</th>
                <th>id solicitud cliente</th>
                <th>Operador Solicitante</th>
                <th>Tipo</th>
                <th>Clase</th>
                <th>Comentario</th>
                <th>Estado</th>
                <th>Fecha Procesado</th>
                <th>Procesado por Operador</th>
                <th>Observaciones</th>
                <th>Resultado</th>
                <th></th>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
        
    <div class="row" id="result" style="display: none; margin:0px;">
    
        <div class="col-sm-12">
       
            <table class="table table-striped table-bordered text-center" id="datos-solicitud">
                <thead>
                    <th>ID</th>
                    <th>DOCUMENTO</th>
                    <th>SOLICITANTE</th>
                    <th>SITUACIÓN LABORAL</th>
                    <th>PASO</th>
                    <th>ESTADO</th>
                    <th>TIPO</th>
                </thead>
                <tbody>
                    <tr></tr>
                </tbody>
            </table>
            
        </div>
        <div class="col-sm-12">
       
            <table class="table table-striped table-bordered text-center table-condensed" id="datos-solicitud-ajustes">
                <tbody>
                </tbody>
            </table>
            
        </div>


        <div class="col-sm-3" style="padding: 30px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>PASO</b></h4></div>
                <div class="panel-body">
                   <div class="row" style="padding:10px;">
                       <div class="col-sm-6 text-right"><h5><b>Actual:</b></h5></div>
                       <div class="col-sm-6 text-left" id="paso-actual" style="padding-top:9px"></div>
                   </div>
                   <div class="row" style="background: #ececec;padding:10px;">
                       <div class="col-sm-6 text-right"><h5><b>Cambiar a:</b></h5></div>
                       <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <select class="form-control" id="pasos_disponibles">
                                    
                                </select>
                            </div>
                       </div>
                   </div>
                   <div class="row" style="padding:10px;">
                       <div class="col-sm-12 text-center">
                           <a class="btn btn-success " id="actualizar-paso"> PROCESAR </a>
                       </div>
                   </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3" style="padding: 30px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>ESTADO</b></h4></div>
                <div class="panel-body">
                   <div class="row" style="padding:10px;">
                       <div class="col-sm-6 text-right"><h5><b>Actual:</b></h5></div>
                       <div class="col-sm-6 text-left" id="estado-actual" style="padding-top:9px"></div>
                   </div>
                   <div class="row" style="background: #ececec;padding:10px;">
                       <div class="col-sm-6 text-right"><h5><b>Cambiar a:</b></h5></div>
                       <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <select class="form-control" id="estados_disponibles">
                                    
                                </select>
                            </div>
                       </div>
                   </div>
                   <div class="row" style="padding:10px;">
                       <div class="col-sm-12 text-center">
                           <a class="btn btn-success " id="actualizar-estado"> PROCESAR </a>
                       </div>
                   </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3" style="padding: 30px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>SITUACIÓN LABORAL</b></h4></div>
                <div class="panel-body">
                   <div class="row" style="padding:10px;">
                       <div class="col-sm-6 text-right"><h5><b>Actual:</b></h5></div>
                       <div class="col-sm-6 text-left" id="situacion-actual" style="padding-top:9px"></div>
                   </div>
                   <div class="row" style="background: #ececec;padding:10px;">
                       <div class="col-sm-6 text-right"><h5><b>Cambiar a:</b></h5></div>
                       <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <select class="form-control" id="situaciones_disponibles">
                                    
                                </select>
                            </div>
                       </div>
                   </div>
                   <div class="row" style="padding:10px;">
                       <div class="col-sm-12 text-center">
                           <a class="btn btn-success " id="actualizar-situacion"> PROCESAR </a>
                       </div>
                   </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3" style="padding: 30px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>TELEFONO</b></h4></div>
                <div class="panel-body">
                   <div class="row" style="padding:10px;">
                       <div class="col-sm-6 text-right"><h5><b>Actual:</b></h5></div>
                       <div class="col-sm-6 text-left" id="telefono-actual" style="padding-top:9px"></div>
                   </div>
                   <div class="row" style="background: #ececec;padding:10px;">
                       <div class="col-sm-6 text-right"><h5><b>Cambiar a:</b></h5></div>
                       <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <input type="text" onkeypress="return isNumberKey(event)" class="form-control" id="new-telefono" placeholder="Telefono">
                            </div>
                       </div>
                   </div>
                   <div class="row" style="padding:10px;">
                       <div class="col-sm-12 text-center">
                           <a class="btn btn-success " id="actualizar-telefono"> PROCESAR </a>
                           <a class="btn btn-warning " id="anular-telefono"> ANULAR </a>
                       </div>
                   </div>
                </div>
            </div>
        </div>
        <!--Reasignar solicitud-->
        <div class="col-sm-3" style="padding: 30px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>REASIGNAR SOLICITUD</b></h4></div>
                <div class="panel-body">
                    <div class="row" style="padding:10px;">
                        <div class="col-sm-6 text-right"><h5><b>Actual:</b></h5></div>
                        <div class="col-sm-6 text-left" id="nombre-usuario" style="padding-top:9px"></div>
                    </div>
                    <div class="row" style="background: #ececec;padding:10px;">
                       <div class="col-sm-6 text-right"><h5><b>Cambiar a:</b></h5></div>
                       <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <select class="form-control" id="operadores">
                                </select>
                            </div>
                       </div>
                   </div>
                    
                    <div class="row" style="padding:10px;">
                        <div class="col-sm-12 text-center">
                            <a class="btn btn-success " id="resignar-solicitud"> PROCESAR </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3" style="padding: 30px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>AMPLIAR CUPO</b></h4></div>
                <div class="panel-body">
                    <div class="row" style="padding:10px;">
                        <div class="col-sm-6 text-right"><h5><b>Disponible:</b></h5></div>
                        <div class="col-sm-6 text-left" id="cupo-actual" style="padding-top:9px"></div>
                    </div>
                    <div class="row" style="background: #ececec;padding:10px;">
                        <div class="col-sm-6 text-right"><h5><b>Plazo:</b></h5></div>
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <select class="form-control" id="plazos" disabled>
                                    <option value="0" disabled selected>-plazo-</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="padding:10px;">
                        <div class="col-sm-6 text-right"><h5><b>Nuevo monto:</b></h5></div>
                        <div class="col-sm-6">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <select class="form-control" id="cupos" disabled>
                                    <option value="" selected disabled>Seleccione</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" style="background: #ececec;padding:10px;">
                        <div class="col-sm-12 text-center">
                            <a class="btn btn-success " id="ampliar-cupo"> PROCESAR </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Rechazo -->
        <div class="col-sm-3" style="padding: 30px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>LEVANTAR RECHAZO</b></h4></div>
                <div class="panel-body">
                    <div class="row" style="padding:10px;">
                        <div class="col-sm-6 text-right"><h5><b>Actual:</b></h5></div>
                        <div class="col-sm-6 text-left" id="levantar-rechazo" style="padding-top:9px"></div>
                    </div>
                    <div class="row" style="background: #ececec;padding:10px;">
                        <div class="col-sm-12 text-right" style="padding:17px"></div>
                        
                    </div>
                    
                    <div class="row" style="padding:10px;">
                        <div class="col-sm-12 text-center">
                            <a class="btn btn-success " id="actualizar-rechazo"> PROCESAR </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="col-sm-3" style="padding: 30px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>PAGARE</b></h4></div>
                <div class="panel-body">
                    <div class="row" >
                        <div class="col-sm-12 text-right" style="padding:30px"></div>  
                    </div>
                    <div class="row" style="background: #ececec;padding:10px;">
                        <div class="col-sm-12 text-right" style="padding:17px"></div>  
                    </div>
                    
                    <div class="row" style="padding:10px;">
                        <div class="col-sm-12 text-center boton-pagare">
                            <div class="col-sm-12 text-right" style="padding:15px"></div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="col-sm-3" style="padding: 30px;">
            <div class="panel panel-info">
                <div class="panel-heading text-center"><h4><b>AUTORIZAR VERIFICACION</b></h4></div>
                <div class="panel-body">
                    <div class="row" >
                        <div class="col-sm-12 text-right" style="padding:15px"></div>  
                    </div>
                    <div class="row" style="background: #ececec;padding:10px;">
                        <div class="col-sm-12 text-right" style="padding:17px"></div>  
                    </div>
                    <div class="row" style="padding:10px;">
                        <div class="col-sm-12 text-center boton-verificacion">
                            <div class="col-sm-12 text-right" style="padding:15px"></div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3" style="padding: 30px;">
            	<div class="panel panel-info">
            		<div class="panel-heading text-center">
            			<h4><b>NOMBRE Y APELLIDO</b></h4>
            		</div>
            		<div class="panel-body">
            			<div class="row" style="padding:10px;">
            				<div class="col-sm-5 text-right">
            					<h5><b>Nombre:</b></h5>
            				</div>
            				<div class="col-sm-7 text-left" id="nombre-usuario" style="padding-top:9px">
                                <input type="text" class="form-control" id="fix_sol_nombre" />
                            </div>
            			</div>
            			<div class="row" style="background: #ececec;padding:10px;">
            				<div class="col-sm-5 text-right">
            					<h5><b>Apellido:</b></h5>
            				</div>
            				<div class="col-sm-7">
            					<div class="form-group" style="margin-bottom: 0px;">
                                    <input type="text" class="form-control" id="fix_sol_apellido" />
            					</div>
            				</div>
            			</div>

            			<div class="row" style="padding:10px;">
            				<div class="col-sm-12 text-center">
            					<a class="btn btn-success " id="resignar-datospers"> PROCESAR </a>
            				</div>
            			</div>
            		</div>
            	</div>
            </div>
            
        <!-- *** Validar Cuenta *** -->
        <div class="col-sm-9" style="width: 100%;">
            <div class="row" style="margin:0px;">
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading text-center"><h4><b>VALIDAR CUENTA</b></h4></div>
                        <div class="panel-body">
                        <div class="row" style="padding:10px;">
                            <div class="col-sm-3"><h5><b>Banco:</b></h5></div>
                            <div class="col-sm-3"><h5><b>Tipo de Cuenta:</b></h5></div>
                            <div class="col-sm-3"><h5><b>Número:</b></h5></div>
                            <div class="col-sm-3"><h5><b>Fecha Apertura:</b></h5></div>
                        </div>
                        <div class="row" style="background: #ececec; padding:10px;">
                            <div class="col-sm-3">
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <select class="form-control" id="slt_banco">
                                        <option value="">Seleccione una opción:</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <select class="form-control" id="slt_tipo_cuenta">
                                        <option value="">Seleccione una opción:</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <input type="text" class="form-control" id="inp_numero_cta" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" style="margin-bottom: 0px;">
                                    <input type="date" class="form-control" id="inp_fecha_apertura" />
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding:10px;">                            
                            <div class="col-sm-4 ">                                
                                <div id="Ajustes_files">
                                    <div class="list-group-item" style="padding:0px;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-offset-5 text-center">
                                <a class="btn btn-primary btn-sm" id="a_mostrar" style="float: left;" target="_blank"><i class="fa fa-eye"></i>
                                <a class="btn btn-success" id="a_procesar"> PROCESAR </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-sm-12"  id="AjustesImagen"></div>
    </div>
</div>

<!-- Modal imágenes -->
<div class="modal fade" id="modalImagen" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Certificación bancaria</h4>
            </div>
            <div class="modal-body">
                <div id=imagenes></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        
    </div>
</div>

<script src="<?php echo base_url('assets/moment/moment.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/ajustes/ajustes.js'); ?>"></script>
