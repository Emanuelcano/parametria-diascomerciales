
<div class="row box box-info mx-0">

    <div class="box-header with-border" id="titulo">
		<h6 class="box-title"><small><strong>Asignaciones</strong></small></h6>
	</div>

	<div class="col-xs-12">
        <ul class="nav nav-tabs nav-justified">
                <li class="active"> <a  href="#1" data-toggle="tab" onClick="reset()">Operador a Operador</a> </li>
                <li><a href="#2" data-toggle="tab" onClick="reset()">Puntual</a> </li>
                <li><a href="#3" data-toggle="tab" onClick="reset()">Equitativa</a> </li>
                <li><a href="#4" data-toggle="tab" onClick="reset()">Chats</a> </li> 
        </ul>

        <div class="tab-content login-box-body">
            <div class="tab-pane active register-box-body p-0" id="1">
               <form action="" class="form-horizontal">
                    <div class="row mx-0">
                        <div class="col-sm-6">
                            <div class="row mx-0">
                                <h5><strong>Fecha de las asignaciones a reasignar</strong></h5>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="desde-1">Desde</label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" id="desde-1" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" onChange="actualizarAsignaciones(this)">
                                    </div>
                                    
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="hasta-1">Hasta</label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" id="hasta-1" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>"  onChange="actualizarAsignaciones(this)">
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-0">
                                <h5><strong>Selección de operadores</strong></h5>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="desde">De</label>
                                    <div class="col-sm-10">
                                        <select id="operador-1" class="slc_operadores form-control designado" onChange="cargarAsignaciones(this)">
                                                <option disabled value="" selected>Seleccione...</option>
                                                <?php foreach ($data['operadores_designados'] as $tipo): 
                                                ?>
                                                        <option 
                                                        value="<?php echo $tipo['idoperador']?>"><?php echo $tipo['nombre_apellido']?></option>
                                                <?php  endforeach; ?>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="hasta">Para</label>
                                    <div class="col-sm-10">
                                        <select id="operador-2" class="slc_operadores form-control" disabled = "true" onChange="cargarAsignaciones(this)">
                                                <option disabled value="" selected>Seleccione...</option>
                                                <?php foreach ($data['operadores_receptares'] as $tipo): 
                                                ?>
                                                        <option 
                                                        value="<?php echo $tipo['idoperador']?>" class="receptor op-<?php echo $tipo['idoperador']?>"><?php echo $tipo['nombre_apellido']?></option>
                                                <?php  endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-0">
                                <button type="button" id="asig-1" onClick="asignarSolicitudes(this)" class=" btn center-block btn-success">Asignar</button>
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="row mx-0">
                                <div class="col-sm-6">
                                    <h5><strong>Solicitudes que se asignarán: </strong><span class="total" id="cant-operador-1">0</span></h5>
                                    <h5><strong>Chats que se asignarán: </strong><span class="total" id="cant-chats-operador-1">0</span></h5>
                                    <div class="cont">
                                        <table id = "asig-operador-1" class="table">
                                            <thead>
                                                <th>Solicitud</th>
                                                <th class="text-center">Chat</th>
                                                <th>Fecha</th>
                                            </thead>
                                            <tbody>
                                                    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <h5><strong> Solicitudes ya asignadas: </strong><span class="total" id="cant-operador-2">0</span></h5>
                                    <h5 ><strong>Chats ya asignados: </strong><span class="total" id="cant-chats-operador-2">0</span></h5>
                                    <div class="cont">
                                        <table id = "asig-operador-2" class="table">
                                            <thead>
                                                <th>Solicitud</th>
                                                <th>Chat</th>
                                                <th>Fecha</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               </form>
            </div>

            <div class="tab-pane register-box-body p-0" id="2">
                <form action="" class="form-horizontal">
                    <div class="row mx-0">
                        <div class="col-sm-5">
                            <div class="row mx-0">
                                <h5><strong>Buscar Solicitud</strong></h5>
                                <div class="form-group col-sm-12">
                                    <label class="col-sm-3 control-label" for="documento">Documento</label>
                                    <div class="col-sm-7">
                                        <input type="number" class="form-control" id="documento">
                                    </div>
                                    <a class="btn btn-info" onClick="consultarSolicitud('documento')"><i class="fa fa-search col-sm-2"></i></a>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="col-sm-3 control-label" for="solicitud">Nro solicitud</label>
                                    <div class="col-sm-7">
                                        <input type="number" class="form-control" id="solicitud" >
                                    </div>
                                    <a class="btn btn-info" onClick="consultarSolicitud('solicitud')"><i class="fa fa-search col-sm-2"></i></a>
                                </div>
                            </div>

                            <div class="row mx-0">
                                <h5><strong>Selección de operador</strong></h5>
                                <div class="form-group col-sm-12">
                                    <label class="col-sm-3 control-label" for="hasta">Operador</label>
                                    <div class="col-sm-9">
                                        <select id="operador-3" class="slc_operadores form-control designado" onChange="consultarOperador(this)">
                                                <option disabled value="" selected>Seleccione...</option>
                                                <?php foreach ($data['operadores_designados'] as $tipo): 
                                                ?>
                                                        <option 
                                                        value="<?php echo $tipo['idoperador']?>"><?php echo $tipo['nombre_apellido']?></option>
                                                <?php  endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-0">
                                <button type="button" id="asig-2" onClick="asignarSolicitudes(this)" class=" btn center-block btn-success">Asignar</button>
                            </div>
                            
                        </div>
                        <div class="col-sm-7">
                            <div class="row mx-0">
                                <div class="col-sm-12">
                                    <h5><strong>Solicitud(es) que se reasignara(n): </strong> <span class="total" id="cant-solicitud-seleccionada">0</span></h5>
                                    <div class="cont">
                                        <table id = "solicitud-seleccionada" class="table">
                                            <thead>
                                                <th></th>
                                                <th>Solicitud</th>
                                                <th class="text-center">Chat</th>
                                                <th>Asignado a</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <h5><strong> Operador seleccionado</strong></h5>
                                    <div class="cont">
                                        <table id = "operador-seleccionado" class="table">
                                            <thead>
                                                <th>Id</th>
                                                <th>Nombre</th>
                                                <th>Tipo operador</th>
                                                <!--<th>asignaciones</th> -->
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               </form>                            
            </div>

            <div class="tab-pane register-box-body p-0" id="3">
                <form action="" class="form-horizontal">
                    <div class="row mx-0">
                        <div class="col-sm-6">
                            <div class="row mx-0">
                                <h5><strong>Fecha de las asignaciones a reasignar</strong></h5>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="desde-3">Desde</label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" id="desde-3" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" onChange="actualizarAsignaciones(this)">
                                    </div>
                                    
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="hasta-3">Hasta</label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" id="hasta-3" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" onChange="actualizarAsignaciones(this)">
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-0">
                                <h5><strong>Selección de operador</strong></h5>
                                <div class="form-group col-sm-12">
                                    <label class="col-sm-2 control-label" for="operador-4">Operador</label>
                                    <div class="col-sm-8">
                                        <select id="operador-4" class="slc_operadores form-control designado" onChange="cargarAsignaciones(this)">
                                                <option disabled value="" selected>Seleccione...</option>
                                                <?php foreach ($data['operadores_designados'] as $tipo): 
                                                ?>
                                                        <option 
                                                        value="<?php echo $tipo['idoperador']?>"><?php echo $tipo['nombre_apellido']?></option>
                                                <?php  endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" id="asig-3" onClick="asignarSolicitudes(this)" class=" btn center-block btn-success">Asignar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mx-0">
                                
                                <div class="col-sm-6">
                                    <h5 style="margin-left: -15px;"><strong>Solicitudes que se asignarán</strong></h5>
                                    <h5 style="margin-left: -15px;">Total: <span class="total" id="cant-operador-4">0</span>  |  Por operador: <span class="total" id="cant-por-operador">0</span></h5>
                                </div>
                                <div class="col-sm-6">
                                    <h5 style="margin-left: -15px;"><strong>Chats que se asignarán</strong></h5>
                                    <h5 style="margin-left: -15px;">Total: <span class="total" id="cant-chats-operador-4">0</span>  |  Por operador: <span class="total" id="cant-chats-por-operador">0</span></h5>
                                </div>
                                <div class="col-sm-12">
                                    <div class="cont">
                                        <table id = "asig-operador-4" class="table">
                                            <thead>
                                                <th>Id</th>
                                                <th class="text-center">Chat</th>
                                                <th>Fecha</th>
                                            </thead>
                                            <tbody>
                                                    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row mx-0">
                                <div class="col-sm-12">
                                    <h5><strong>Operador(es) receptores</strong></h5>
                                    <div class="cont">
                                        <table id = "receptores" class="table">
                                            <thead>
                                                <th></th>
                                                <th>Id</th>
                                                <th>Nombre</th>
                                                <th>Tipo operador</th>
                                                <!--<th>asignaciones</th> -->
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['operadores_receptares'] as $operador): 
                                                ?>
                                                    <tr >
                                                        <td><div class="form-check">
                                                                <input type="checkbox" class="form-check-input" id="ch-op-<?php echo $operador['idoperador']?>" value="<?php echo $operador['idoperador']?>" name ="ch-operadores" onChange = "calcularTotalPorOperador(this, 'cant-por-operador', 2)">
                                                                <label class="form-check-label" for="ch-op-<?php echo $operador['idoperador']?>"></label>
                                                        </div></td>
                                                        <td><?php echo $operador['idoperador']?></td>
                                                        <td><?php echo $operador['nombre_apellido']?></td>
                                                        <td><?php echo $operador['descripcion']?></td>
                                                    </tr>
                                                <?php  endforeach; ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               </form>
            </div>

            <div class="tab-pane  register-box-body " id="4">
            <form action="" class="form-horizontal">
                    <div class="row mx-0">
                        <div class="col-sm-6">

                            <div class="row mx-0" style="padding-right: 60px;">
                                <h5 style="border-bottom: 1px solid #00c0ef;"><strong>Reasignación por lote</strong></h5>
                            </div>
                            <div class="row mx-0">
                                <h5><strong>Fecha de las asignaciones a reasignar</strong></h5>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="desde-4">Desde</label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" id="desde-4" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" onChange="actualizarAsignaciones(this)">
                                    </div>
                                    
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="hasta-4">Hasta</label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" id="hasta-4" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>"  onChange="actualizarAsignaciones(this)">
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-0">
                                <h5><strong>Selección de operadores</strong></h5>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="operador-5">De</label>
                                    <div class="col-sm-10">
                                        <select id="operador-5" class="slc_operadores form-control designado" onChange="cargarAsignaciones(this)">
                                                <option disabled value="" selected>Seleccione...</option>
                                                <?php foreach ($data['operadores_designados'] as $tipo): 
                                                ?>
                                                        <option 
                                                        value="<?php echo $tipo['idoperador']?>"><?php echo $tipo['nombre_apellido']?></option>
                                                <?php  endforeach; ?>
                                            
                                        </select>
                                    </div>
                                    <div class="col-sm-12">
                                        <h5><strong>Chats que se asignarán: </strong><span class="total" id="cant-chats-operador-5">0</span></h5>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="col-sm-2 control-label" for="operador-6">Para</label>
                                    <div class="col-sm-10">
                                        <select id="operador-6" class="slc_operadores form-control" disabled = "true" onChange="cargarAsignaciones(this)">
                                                <option disabled value="" selected>Seleccione...</option>
                                                <?php foreach ($data['operadores_receptares'] as $tipo): 
                                                ?>
                                                        <option 
                                                        value="<?php echo $tipo['idoperador']?>" class="receptor op-<?php echo $tipo['idoperador']?>"><?php echo $tipo['nombre_apellido']?></option>
                                                <?php  endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-12">
                                        <h5 ><strong>Chats ya asignados: </strong><span class="total" id="cant-chats-operador-6">0</span></h5>
                                    </div>
                                </div>
                            </div>

                            <div class="row mx-0">
                                <button type="button" id="asig-4" onClick="asignarSolicitudes(this)" class=" btn center-block btn-success">Asignar</button>
                            </div>
                            
                        </div>
                        <div class="col-sm-6">
                            <div class="row mx-0" style="padding-right: 60px;">
                                <h5 style="border-bottom: 1px solid #00c0ef;"><strong>Reasignación puntual</strong></h5>
                            </div>
                            <div class="row mx-0">
                                    <h5><strong>Buscar Chat</strong></h5>
                                    <div class="form-group col-sm-12">
                                        <label class="col-sm-3 control-label" for="telefono">Teléfono</label>
                                        <div class="col-sm-7">
                                            <input type="number" class="form-control" id="telefono">
                                        </div>
                                        <a class="btn btn-info" onClick="consultarChats()"><i class="fa fa-search col-sm-2"></i></a>
                                    </div>
                                    
                            </div>
                            <div class="row mx-0">
                                <h5><strong>Selección de operador</strong></h5>
                                <div class="form-group col-sm-12">
                                    <label class="col-sm-3 control-label" for="operador-7">Operador</label>
                                    <div class="col-sm-9">
                                        <select id="operador-7" class="slc_operadores form-control">
                                                <option disabled value="" selected>Seleccione...</option>
                                                <?php foreach ($data['operadores_receptares'] as $tipo): 
                                                ?>
                                                        <option 
                                                        value="<?php echo $tipo['idoperador']?>"><?php echo $tipo['nombre_apellido']?></option>
                                                <?php  endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                    <h5><strong>Chat(s) que se reasignara(n): </strong> <span class="total" id="cant-chats-operador-7">0</span></h5>
                                    <div class="cont">
                                        <table id = "chats-seleccionados" class="table">
                                            <thead>
                                                <th></th>
                                                <th>Chat</th>
                                                <th>Asignado a</th>
                                                <th>Último mensaje</th>
                                                <th>Estado</th>
                                                <th>Tipo</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>



                            <div class="row mx-0">
                                <button type="button" id="asig-5" onClick="asignarSolicitudes(this)" class=" btn center-block btn-success">Asignar</button>
                            </div>
                        </div>
                    </div>
               </form>
            </div>
            
        </div>
    </div>

</div>

