<style type="text/css">
  textarea {
        overflow-y: scroll;
        height: 100px;
        resize: none; /* Remove this if you want the user to resize the textarea */
    }

      #view_test_query {
        overflow-y: scroll;
        height: 200px;
        resize: none; /* Remove this if you want the user to resize the textarea */
    }
</style>
<?php $id_campania=0; ?>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="constante_url" value="<?php echo CONSTANTE_URL; ?>">
<input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/email.multiple.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap-tagsinput.css'); ?>"/>
<script type="text/javascript" src="<?php echo base_url('assets/fullcalendar/main.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/fullcalendar/locales/es.js'); ?>"></script>




<div id="section_search_solicitud" style="background: #FFFFFF;">
		<?php $prelanzamiento = $data['prelanzamiento']; ?>
		<?php if (!empty($prelanzamiento)) { ?>
		  <div class="row">
			  <div class="col-md-8 col-md-offset-2">
				  <div class="box box-primary">
					  <div class="box-header with-border"><h3 class="box-title">Campanias Listas para lanzarse</h3></div>
					  <table class="table table-condensed" id="table-events">
						  <thead>
						  <tr>
							  <th>ID</th>
							  <th>Campania</th>
							  <th>Tipo</th>
							  <th id="action-column">Acciones</th>
						  </tr>
						  </thead>
						  <tbody>
						  <?php foreach ($prelanzamiento as $item): ?>
							  <tr>
								  <td><?php echo $item['id_logica']; ?></td>
								  <td><?php echo $item['nombre_logica']; ?></td>
								  <td><?php echo $item['type_logic']; ?></td>
								  <td>
									  <a href="<?php echo base_url('cronograma_campanias/Cronogramas/previewLanzamiento/'.$item['id_logica'].'/'.$item['id_template'].'/'.$item['id_event'].'/'.$item['prelanzamiento_id']); ?>" class="btn btn-primary btn-xs">Envio preliminar</a>
								  </td>
							  </tr>
						  <?php endforeach; ?>
						  </tbody>
					  </table>
					  <div class="overlay" id="loadingListCronograma" style="display: none"><i
								  class="fa fa-refresh fa-spin"></i></div>
				  </div>
			  </div>
		  </div>
		<?php } ?>
        <H3><strong>Agendar Campañas</strong></H3>
            <div class="row">
                <div class="col-md-12 col-md-offset-9">
                    <button  type="button" id="btn_save_campaing" class="btn btn-primary hide" title="Nueva Campañia" style="font-size: 12px;"><i class="fa fa-save"></i> Guardar Campañia</button>
                    <button  type="button" id="btn_new_campaing" class="btn btn-success" title="Nueva Campañia" style="font-size: 12px;"><i class="fa fa-plus"></i> Nueva Campañia</button>
                    <button  type="button" id="btn_calendar" class="btn btn-info" title="Calendario" style="font-size: 12px;"><i class="fa fa-calendar"></i> Ver Calendario</button>
                </div>
            </div>
            <br>
            <div class="row">
              <div class="" id="result">
				  <table align="center" id="table_campania" class="table table-responsive table-striped table=hover display" width="100%" >
					  <thead style="font-size: smaller; ">
					  <tr class="info">
						  <th style="text-align: center;">ID</th>
						  <th style="text-align: center;">Campañia</th>
						  <th style="text-align: center;">Proveedor</th>
						  <th style="text-align: center;">Tipo</th>
						  <th style="text-align: center;">Estado</th>
						  <th style="text-align: center;">Accion</th>
					  </tr>
					  </thead>
					  <tbody style="font-size: 12px; text-align: center;" id="tb_body">
					  <?php foreach ($data['campanias'] as $k => $campania) { ?>
						  <tr>
							  <td><?=$campania->id_logica?></td>
							  <td><?=$campania->nombre_logica?></td>
							  <td><?=$campania->proveedor?></td>
							  <td><?=$campania->type_logic?></td>
							  <td><?=($campania->estado==1) ? "Activo" : "Inactivo"; ?></td>
							  <td>
								  <button type='button' id='btn_edit_campaing' onclick='edit_campaing("<?=$campania->id_logica?>")' class='btn btn-primary ajustar-credito' data-index='<?=$k?>' data-credito_detalle='<?=$campania->id_logica?>'><i class='fa fa-pencil'></i></button>
								  <a href="<?php echo base_url();?>cronograma_campanias/Cronogramas/edit/<?=$campania->id_logica?>" type='button' id='btn_edit_campaing' class='btn btn-warning ajustar-credito' data-index='<?=$k?>' data-credito_detalle='<?=$campania->id_logica?>'><i class='fa fa-pencil'></i></a>
						  </tr>
					  <?php } ?>
					  </tbody>
				  </table>
              </div>
            </div>

<div class="row">
    <div class="col-lg-12 hide" id="view-new_campain">
        <div class="content">
            <div class="card card-calendar">
                <div class="content">
                    <!-- INICIO CUERPO NUEVA CAMPAÑIA-->

                <form name="frm_campania" id="frm_campania" class="form-group" role="form" method="POST">
					<input type="hidden" name="txt_hd_id_camp" id="txt_hd_id_camp">
                
                  <div class="form-group">
                 
                    <label for="title" class="col-sm-1 control-label">Titulo</label>
                    <div class="col-sm-5">
                      <input type="text" name="nombre_logica" class="form-control" id="nombre_logica" placeholder="Titulo">
                    </div>
                    <label for="color" class="col-sm-1 control-label">Estado</label>
                    <div class="col-sm-2">
                      <select name="sl_estado_campain" class="form-control" id="sl_estado_campain">
                          <option value="1">Habilitado</option>
                          <option value="0">Deshabilitado</option>
                      </select>
                    </div>
                    <label for="color" class="col-sm-1 control-label">Color</label>
                    <div class="col-sm-2">
                      <select name="sl_color" class="form-control" id="sl_color">
                                      <option value="0">Seleccionar</option>
                                      <option style="color:#0071c5;" value="#0071c5">&#9724; Azul oscuro</option>
                                      <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquesa</option>
                                      <option style="color:#008000;" value="#008000">&#9724; Verde</option>                       
                                      <option style="color:#FFD700;" value="#FFD700">&#9724; Amarillo</option>
                                      <option style="color:#FF8C00;" value="#FF8C00" selected="selected">&#9724; Naranja</option>
                                      <option style="color:#FF0000;" value="#FF0000">&#9724; Rojo</option>
                                      <option style="color:#000;" value="#000">&#9724; Negro</option>
                          
                        </select>
                    </div>
                    
                  </div>
                  <hr>
                  <div class="form-group">
                    
                    <label for="color" class="col-sm-1 control-label">Proveedores</label>
                    <div class="col-sm-4">
                      <select name="sl_proveedor" class="form-control" id="sl_proveedor">
                                      <?php  foreach ($this->session->flashdata('proveedores_rs') as $value) {?>
                                        <option value="<?php echo $value['id_proveedor'];?>" selected="selected"><?php echo $value['nombre_proveedor'];?></option> 
                                      <?php }  ?>
                        </select>
                    </div>
                    <div class="col-sm-1"><a href="#" id="btn_nuevo_pro" onclick="$('#ModalAddPro').modal('show')" class="btn btn-success" title="Agregar Nuevo Proveedor" ><i class="fa fa-plus"></i></a>
                    </div>
                    <label for="color" class="col-sm-1 control-label">Servicio</label>
                    <div class="col-sm-2">
                      <select name="sl_tipo_servicio" class="form-control" id="sl_tipo_servicio" required="required">
                                      
                                      <option value="SMS" selected="selected">SMS</option>
                                      <option value="MAIL">EMAIL</option>                       
                                      <option value="WSP">WHATSAP</option>
                                      <option value="IVR">IVR</option>
                        </select>
                    </div>
                    <label for="color" class="col-sm-1 control-label">Modalidad</label>
                    <div class="col-sm-2">
                      <select name="sl_modalidad" class="form-control" id="sl_modalidad">
                                      <option value="ALEATORIO">Aleatorio</option>
                                      <option value="PREDETERMINADO">Predeterminado</option>
                          
                        </select>
                    </div>
                    
                  </div>
                  </form>

                  <div class="col-md-12" id="detalle_campania" style="margin-top: 20px; display: none">
					  <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
						  <div class="panel panel-default">
							  <div class="panel-heading" role="tab" id="headingOne">
								  <h4 class="panel-title" role="button" data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
									  ¿Que Enviar?
								  </h4>
							  </div>
							  <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
								  <div class="panel-body">
									  <?php $this->load->view('supervisores/box_campanias_que_enviar', ['id_campania' => $id_campania]); ?>
								  </div>
							  </div>
						  </div>
						  <div class="panel panel-default">
							  <div class="panel-heading" role="tab" id="headingTwo">
								  <h4 class="panel-title" role="button" data-toggle="collapse" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
									  ¿A quien enviar?
								  </h4>
							  </div>
							  <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
								  <div class="panel-body">
									  <?php $this->load->view('supervisores/box_campanias_a_quien_enviar', ['id_campania' => $id_campania]); ?>
								  </div>
							  </div>
						  </div>
						  <div class="panel panel-default">
							  <div class="panel-heading" role="tab" id="headingThree">
								  <h4 class="panel-title"  role="button" data-toggle="collapse"  href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
									  ¿Cuando enviar?
								  </h4>
							  </div>
							  <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
								  <div class="panel-body">
									  <?php $this->load->view('supervisores/box_campanias_cuando_enviar', ['id_campania' => $id_campania]); ?>
								  </div>
							  </div>
						  </div>
						  <div class="panel panel-default">
							  <div class="panel-heading" role="tab" id="headingFour">
								  <h4 class="panel-title" role="button" data-toggle="collapse" href="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
									  ¿Como enviar?
								  </h4>
							  </div>
							  <div id="collapseFour" class="panel-collapse collaps ine" role="tabpanel" aria-labelledby="headingFour">
								  <div class="panel-body">
									  <?php $this->load->view('supervisores/box_campanias_como_enviar', ['id_campania' => $id_campania]); ?>
								  </div>
							  </div>
						  </div>
					  </div>
				  </div>
<!--                  <div class="col-md-12">-->
<!--                    --><?php //$this->load->view('supervisores/box_campanias_que_enviar', ['id_campania' => $id_campania]); ?>
<!--                  </div>-->
<!--                  <div class="col-md-12">-->
<!--                    --><?php //$this->load->view('supervisores/box_campanias_a_quien_enviar', ['id_campania' => $id_campania]); ?>
<!--                  </div>-->
<!--                  <div class="col-md-12">-->
<!--                    --><?php //$this->load->view('supervisores/box_campanias_cuando_enviar', ['id_campania' => $id_campania]); ?>
<!--                  </div>-->
<!--                  <div class="col-md-12">-->
<!--                    --><?php //$this->load->view('supervisores/box_campanias_como_enviar', ['id_campania' => $id_campania]); ?>
                  </div>
                    <!-- INICIO CUERPO NUEVA CAMPAÑIA-->
                </div>
            </div>
        </div>        
    </div>
</div>
<div class="row">
    <div class="col-lg-12" id="view-calendar">
            <div class="content">
                <div class="card card-calendar">
                    <div class="content">
                        <div id="fullCalendar"></div>
                    </div>
                </div>
            </div>        
    </div>
</div>
<div id="ModalView" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Detalle del Mensaje</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4 text-right"><strong>Tipo de Campaña</strong></div>
					<div class="col-md-8"><p id="modal-campain-type"></p></div>
					<div class="col-md-4 text-right"><strong>Fecha y hora programada</strong></div>
					<div class="col-md-8"><p id="modal-campain-date"></p></div>
					<div class="col-md-4 text-right"><strong>Mensaje enviado</strong></div>
					<div class="col-md-8"><p id="modal-campain-msg"></p></div>
					<input type="hidden" id="modal-id-msg-prog-date">
					<input type="hidden" id="modal-id-msg-prog">
					<input type="hidden" id="modal-id-campain">
					<input type="hidden" id="modal-canceled">
				</div>
			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-md-6 text-left">
						<button class="btn btn-warning" id="modal-disable">Deshabilitar</button>
						<button class="btn btn-success" id="modal-enable">Habilitar</button>
					</div>
					<div class="col-md-6">
						<button type="button" class="btn btn-info" data-dismiss="modal" onclick="edit_campaing_modal($('#modal-id-campain').val())">Ver mas detalles</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>



<!-- Modal asignar Skill -->
<div class="modal fade" id="modalA_asignarSkill" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" style="width: 80%;" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="myModalLabel">Asignar Skill</h3>
      </div>
      <div class="modal-body">
      <div class="box-body">
        <div id="asignacion_skill">
          <div class="col-sm-12">
            <div id="dualSelectExample" style="width:100%; height:200px;"></div><br> 								
          </div>
          <div class="row text-right">
            <div class="col-sm-2">
                <button  type="button" class="btn btn-success" title="asignar-skill" style="font-size: 12px; width: 100%;" id="asignar-skill" ><i class="fa fa-check"></i> ASIGNAR SKILL </button>
            </div>
          </div>
        </div>
      </div>
            
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content" style="width:850px; margin-left: -100px;">
            <form class="form-horizontal" name="frm_modalAdd" id="frm_modalAdd" method="POST" action="<?php echo base_url();?>api/ApiCampanias/guardarEvento">
            
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Agendar Campañia</h4>
              </div>
              <div class="modal-body">
                
                  <div class="form-group">
                 
                    <label for="title" class="col-sm-2 control-label">Titulo</label>
                    <div class="col-sm-10">
                      <input type="text" name="title" class="form-control" id="title" placeholder="Titulo">
                    </div>
                    
                  </div>
                  <div class="form-group">
                    
                    <label for="color" class="col-sm-2 control-label">Proveedores</label>
                    <div class="col-sm-8">
                      <select name="id_proveedor" class="form-control" id="id_pro">
                                      <?php  foreach ($this->session->flashdata('proveedores_rs') as $value) {?>
                                        <option value="<?php echo $value['id_proveedor'];?>" selected="selected"><?php echo $value['nombre_proveedor'];?></option> 
                                      <?php }  ?>
                        </select>
                    </div>
                    <div class="col-sm-2"><a href="#" id="btn_nuevo_pro" onclick="$('#ModalAddPro').modal('show')" class="btn btn-success" title="Agregar Nuevo Proveedor" ><i class="fa fa-plus"></i></a></div>
                    
                  </div>

            <div class="form-group">
              
                      <label for="color" class="col-sm-2 control-label">Logicas (Querys)</label>
                    <div class="col-sm-8">
                      <select name="id_logica" class="form-control" id="id_log">
                                      <?php  foreach ($this->session->flashdata('logicas_rs') as $value) {?>
                                        <option value="<?php echo $value['id_logica'];?>" selected="selected"><?php echo $value['id_logica'].".-".$value['nombre_logica'];?></option> 
                                      <?php }  ?>
                        </select>
                    </div>
                <div class="col-sm-1"><a href="#" id="btn_viewlogic" class="btn btn-primary" title="Ver/Actualizar Logica" ><i class="fa fa-eye"></i></a></div>
                <div class="col-sm-1"><a href="#" id="btn_nuevalogica" onclick="nuevaLogica();" class="btn btn-success" title="Agregar Nueva Logica" ><i class="fa fa-plus"></i></a></div>
                
            </div>
                  
                  <div class="form-group">
                    
                    <label for="color" class="col-sm-2 control-label">Color</label>
                    <div class="col-sm-10">
                      <select name="color" class="form-control" id="color">
                                      <option value="">Seleccionar</option>
                                      <option style="color:#0071c5;" value="#0071c5">&#9724; Azul oscuro</option>
                                      <option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquesa</option>
                                      <option style="color:#008000;" value="#008000">&#9724; Verde</option>                       
                                      <option style="color:#FFD700;" value="#FFD700">&#9724; Amarillo</option>
                                      <option style="color:#FF8C00;" value="#FF8C00">&#9724; Naranja</option>
                                      <option style="color:#FF0000;" value="#FF0000">&#9724; Rojo</option>
                                      <option style="color:#000;" value="#000">&#9724; Negro</option>
                          
                        </select>
                    </div>
                    
                  </div>
                  <div class="form-group">
                   
                      <label for="start" class="col-sm-2 control-label">Fecha Inicial</label>
                      <div class="col-sm-8">
                        <input type="date" name="start" class="form-control" id="start">
                      </div>
                      <div class="form-group col-sm-2" id="div_hora_ini_campania">
                        
                        <span class='input-group timepicker' >
                          <input type='text' class="form-control" name='hora_ini_campania' id='hora_ini_campania' placeholder="HORA INICIO"/>
                          <span class="input-group-addon">
                            <span class="fa fa-clock-o"></span>
                          </span>
                        </span>
                        <!-- <input type="text" maxlength="4" class="form-control" id="hora_ini_campania" placeholder="HORA INICIO" onkeypress="return solo_numeros(event)"></input> -->
                      </div>
                    
                  </div>
                  <div class="form-group">
                   
                      <label for="start" class="col-sm-2 control-label">Fecha final</label>
                      <div class="col-sm-8">
                        <input type="date" name="end" class="form-control datepicker" id="end">
                      </div>
                      <div class="form-group col-sm-2" id="div_hora_fin_campania">
                        
                        <span class='input-group timepicker' >
                          <input type='text' class="form-control" name='hora_fin_campania' id='hora_fin_campania' placeholder="HORA FINAL"/>
                          <span class="input-group-addon">
                            <span class="fa fa-clock-o"></span>
                          </span>
                        </span>
                        <!-- <input type="text" maxlength="4" class="form-control" id="hora_ini_campania" placeholder="HORA INICIO" onkeypress="return solo_numeros(event)"></input> -->
                      </div>
                    
                  </div>
                
              <div id="view_test_camp"></div>
              </div>
              <div class="modal-footer">
                <button type="button" id="" class="btn btn-info">Test template</button>
                <button type="button" id="btn_test_mensaje" class="btn btn-warning">Test Campañia</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
              </div>
            </form>
            </div>
          </div>
        </div>

<div class="modal fade" id="ModalAddPro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
            <form class="form-horizontal" name="frm_modalAddPro" id="frm_modalAddPro" method="POST" action="<?php echo base_url();?>api/ApiCampanias/guardarProveedor">
            
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Agregar Proveedor</h4>
              </div>
              <div class="modal-body">
                
                  <div class="form-group">
                    <label for="title" class="col-sm-2 control-label">Nombre Proveedor</label>
                    <div class="col-sm-10">
                      <input type="text" name="nombre_proveedor" class="form-control" required="required" id="nombre_proveedor" placeholder="Nombre Proveedor">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="color" class="col-sm-2 control-label">Tipo Servicio</label>
                    <div class="col-sm-8">
                      <select name="tipo_servicio" class="form-control" id="tipo_servicio" required="required">
                                      
                                      <option value="SMS" selected="selected">SMS</option>
                                      <option value="EMAIL">EMAIL</option>                       
                                      <option value="WHATSAP">WHATSAP</option>
                                      <option value="TELEFONIA">TELEFONIA</option>
                        </select>
                    </div>
                    
                  </div>
                  <div class="form-group">
                    <label for="end" class="col-sm-2 control-label">Monto Cancelado</label>
                    <div class="col-sm-10">
                      <input type="text" name="monto_pago" class="form-control" id="monto_pago">
                    </div>
                  </div>
                  
                  
                  <div class="form-group">
                    <label for="start" class="col-sm-2 control-label">Fecha Pago</label>
                    <div class="col-sm-10">
                      <input type="text" name="fecha_vencimiento_pago" class="form-control" id="fecha_vencimiento_pago">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="color" class="col-sm-2 control-label">Estado</label>
                    <div class="col-sm-8">
                      <select name="estado" class="form-control" id="estado">
                                      
                                      <option value="1" selected="selected">ACTIVO</option>
                                      <option value="0">INACTIVO</option>                       
                        </select>
                    </div>
                    
                  </div>
                
              </div>
              <div class="modal-footer">
                <button type="submit" id="btn_guardar_pro" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
              </div>
            </form>
            </div>
          </div>
        </div>


<div class="modal fade" id="ModalAddLog" tabindex="-1"  role="dialog" aria-labelledby="myModalLabel">

          <div class="modal-dialog" role="document">
            <div class="modal-content" style="width:850px; margin-left: -100px;">
            <form class="form-horizontal" name="frm_modalAddLog" id="frm_modalAddLog" method="POST" action="<?php echo base_url();?>api/ApiCampanias/guardarLogicas">
            
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Agregar Nueva logica</h4>
              </div>
              <div class="modal-body">
                <input type="hidden" name="txt_type_submit" id="txt_type_submit">
                <input type="hidden" name="id_logica" id="id_logica">
                  <div class="form-group">
                    <label for="title" class="col-sm-2 control-label">Nombre Logica</label>
                    <div class="col-sm-4">
                      <input type="text" name="nombre_logica" class="form-control" required="required" id="nombre_logica" placeholder="Nombre Logica">
                    </div>
                  
                    
                    <label for="color" class="col-sm-2 control-label">Proveedores</label>
                    <div class="col-sm-4">
                      <select name="id_proveedor" class="form-control" id="id_proveedor">
                            <?php  foreach ($this->session->flashdata('proveedores_rs') as $value) {?>
                              <option value="<?php echo $value['id_proveedor'];?>" selected="selected"><?php echo $value['nombre_proveedor'];?></option> 
                            <?php }  ?>
                        </select>
                    </div>
                    
                    
                  </div>
                  <div class="form-group">
                    <label for="color" class="col-sm-2 control-label">Tipo Logica</label>
                    <div class="col-sm-10">
                      <select name="type_logic" class="form-control" id="type_logic">
                                      
                                      <option value="SMS" selected="selected">SMS</option>
                                      <option value="IVR">IVR</option>                       
                                      <option value="MAIL">MAIL</option>                       
                                      <option value="WSP">WHATSAPP</option>                       
                        </select>
                    </div>
                    
                  </div>

                  <div class="form-group">
                    <label for="end" class="col-sm-2 control-label">Objetivos a Buscar</label>
                    <div class="col-sm-10">
                      <select class="js-example-basic-multiple" name="criterios[]" id="sl_criterios" multiple="multiple" style="width: 100%" placeholder="Objetivo">
                          <option value=0>.:Objetivos:.</option>
                          
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="end" class="col-sm-2 control-label">Antiguedad</label>
                        <div class="col-sm-4">
                          <select class="form-control enable-control" name="sl_antiguedad" id="sl_antiguedad" disabled>
                              <option value="0"> .:ANTIGUEDAD:.</option>
                              <option value="credito_detalle.dias_atraso"> DIAS ATRASO</option>
                              <option value="credito_detalle.fecha_vencimiento"> RANGO DE VENCIMIENTO</option>
                          </select>
                        </div>
                 
                    <label for="end" class="col-sm-2 control-label">Rango de Mora</label>
                    <div class="col-sm-4">
                      <select class="form-control enable-control" name="sl_logica" id="sl_logica" disabled>
                        <option value="0">.:LOGICO:.</option>
                        <option value="="> IGUAL A </option>
                        <option value=">"> MAYOR A </option>
                        <option value="<"> MENOR A </option>
                        <option value="!="> DISTINTO A </option>
                        <option value="BETWEEN"> ENTRE </option>
                        <option value="IN"> EN </option>
                      
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="end" class="col-sm-2 control-label">Valores</label>
                    <div class="col-sm-5">
                        <input class="form-control entero hide" type="text" id="dias_atrasoA" name="dias_atrasoA"  autocomplete="off">
                        <input class="form-control moneda hide" type="text" id="currency_rangeA" name="currency_rangeA"  autocomplete="off">
                        <input class="form-control hide" type="text" id="date_rangeA" name="date_rangeA"  autocomplete="off">
                        <input class="form-control hide" type="text" id="date_range" name="date_range" style="min-width: 50px;"  autocomplete="off">
                    </div>
                    <div class="col-sm-5">
                        <input class="form-control entero hide" type="text" id="dias_atrasoB" name="dia_atrasosB"  autocomplete="off">
                        <input class="form-control moneda hide" type="text" id="currency_rangeB" name="currency_rangeB"  autocomplete="off">
                        <input class="form-control hide" type="text" id="date_rangeB" name="date_rangeB"  autocomplete="off">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="end" class="col-sm-2 control-label">Estado de Mora</label>
                    <div class="col-sm-4">
                      <select class="form-control enable-control" name="sl_estado_mora" id="sl_estado_mora" disabled>
                        <option value="0">.:ESTADO:.</option>
                        <option value="AND creditos.estado = 'mora'"> MORA </option>
                        <option value="AND creditos.estado = 'vigente'"> VIGENTE </option>
                      
                      </select>
                    </div>
                    <label for="end" class="col-sm-2 control-label">Tipo de Fuente</label>
                    <div class="col-sm-4">
                      <select class="form-control enable-control" name="sl_fuente" id="sl_fuente" disabled>
                        <option value="0">.:Fuente:.</option>
                        <option value="AND agenda_telefonica.fuente = 'BURO_CELULAR'"> BURO_CELULAR </option>
                        <option value="AND agenda_telefonica.fuente = 'BURO_CELULAR_T'"> BURO_CELULAR_T </option>
                        <option value="AND agenda_telefonica.fuente = 'BURO_LABORAL'"> BURO_LABORAL </option>
                        <option value="AND agenda_telefonica.fuente = 'BURO_RESIDENCIAL'"> BURO_RESIDENCIAL </option>
                        <option value="AND agenda_telefonica.fuente = 'LABORAL'"> LABORAL </option>
                        <option value="AND agenda_telefonica.fuente = 'PERSONAL'"> PERSONAL </option>
                        <option value="AND agenda_telefonica.fuente = 'REFERENCIA'"> REFERENCIA </option>              
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="end" class="col-sm-2 control-label">Limite Registros</label>
                    <div class="col-sm-4">
                      <select class="form-control enable-control" name="sl_limit" id="sl_limit" disabled>
                        <option value="0">.:LIMITE:.</option>
                        <option value="1"> 1 </option>
                        <option value="5"> 5 </option>
                        <option value="10"> 10 </option>
                        <option value="20"> 20 </option>
                        <option value="100"> 100 </option>
                        <option value="ALL"> TODOS </option>
                      
                      </select>
                    </div>

                    <label for="end" class="col-sm-2 control-label">Agrupar</label>
                    <div class="col-sm-4">
                      <select class="form-control enable-control" name="sl_groupBy" id="sl_groupBy" disabled>
                        <option value="0">.:Group by:.</option>
                        <option value="GROUP BY creditos.id_cliente"> id_cliente </option>            
                      </select>
                    </div>
                  </div>
                  
                  
                  
                  
                  <div class="form-group">
                    <label for="start" class="col-sm-2 control-label">Mensaje</label>
                    <div class="col-sm-10">
                      <textarea name="mensaje" class="form-control" id="mensaje" rows="5" required="required" ></textarea>

                    </div>
                  </div>

                  <div class="form-group">
                    <label for="end" class="col-sm-2 control-label">Query (Logica)</label>
                    <div class="col-sm-10">
                      <textarea name="query_contenido" class="form-control" id="query_contenido" rows="5" required="required"></textarea>
                      
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="color" class="col-sm-2 control-label">Estado</label>
                    <div class="col-sm-8">
                      <select name="estado" class="form-control" id="estado">
                                      
                                      <option value="1" selected="selected">ACTIVO</option>
                                      <option value="0">INACTIVO</option>                       
                        </select>
                    </div>
                    
                  </div>
                  <div class="hide" id="view_test_query"></div>
              </div>
              <div class="modal-footer">
                <a type="button" href="" id="btn_csv_descarga" class="btn btn-primary">Descargar CSV</a>
                <button type="button" id="btn_csv_query" class="btn btn-success">Generar CSV</button>
                <button type="reset" id="btn_clean_query" class="btn btn-default">Limpiar Query</button>
                <button type="button" id="btn_test_query" class="btn btn-info">Test Query</button>
                <button type="submit" id="btn_guardar_log" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
              </div>
            </form>
            </div>
          </div>
        </div>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/configuracion_centrales.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/gestionar_campanias.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/caret/jquery.caret.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/clock-timepicker/jquery-clock-timepicker.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/ddslick/jquery.ddslick.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/taginput/bootstrap-tagsinput.js'); ?>"></script>
