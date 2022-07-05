<style>
 .disabled-chat {
  width: 100%;
  height: 100%;
  background: black;
  opacity: 0.35;
  position: absolute;
  top: 0;
  left: 0;
  z-index: 1;
  display: block;
}
.active-chat .disabled-chat{
 display:none;
}
.panel{
    position: relative;
}
@media (max-width: 1500px) {
  #tabla-detalle-pago {
      font-size: 9px;
  }
}
</style>
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
<div class="container-fluid" style="margin-top: 4%;" id="body_chats">
<input id="id_operador" type="hidden"  value="<?= $this->session->userdata('idoperador')?>">
    <div class="row" >
        
      
        <div class="col-md-4 slot" id="slot-1">
               
        </div>
        <div class="col-md-4 slot" id="slot-2">
            
        </div>
        <div class="col-md-4 slot" id="slot-3">
            
        </div>
        
    </div>

   
</div>

<!-- componente de detalle de creditos -->
<div class="modal" id="detalle-proyeccion" tabindex="-1" role="dialog">
      <div class="modal-dialog" style="width:95%;" role="document">
        <div class="modal-content">
          <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Detalle de Pago</label></h3>
          </div>
          <div class="modal-body">
                <?php 
                    if($this->session->userdata['tipo_operador'] == 13){
                ?>
                    <div class="row" style="margin:0px;">
                      <div class="col-md-12 text-right">
                        <a class ="btn btn-success" id="enviarDetalle" style="margin-bottom:10px"><i class="fa fa-send"></i> ENVIAR DETALLE POR EMAIL</a><br>
                      </div>
                    </div>
                <?php 
                   }
                ?>
                <div class="row" style="margin:0px;">
                    <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4>INFORMACION DEL CREDITO</h4></div>
                    <div class="col-sm-12">
                        <table class="table text-center" id="detalle-credito-proyeccion">
                            <thead>
                                <th style='padding: 10px;'>OTORGAMIENTO</th>
                                <th style='padding: 10px;'>CAPITAL PRESTADO</th>
                                <th style='padding: 10px;'>PLAZO</th>
                                <th style='padding: 10px;'>PRIMER VENCIMIENTO</th>
                                <th style='padding: 10px;'>MONTO DEVOLVER</th>
                                <th style='padding: 10px; background: rgba(3, 169, 244, 0.23);'>DESCUENTOS</th>
                                <th style='padding: 10px;background: rgba(139, 195, 74, 0.2);'>DEUDA AL DIA</th>
                                <th style='padding: 10px;background: rgba(139, 195, 74, 0.2);'>TOTAL COBRADO</th>
                                <th style='padding: 10px;background: rgba(139, 195, 74, 0.2);'>ULTIMO COBRO</th>
                                <th style='padding: 10px;'>ESTADO</th>
                                <th style='padding: 10px;'>DIAS ATRASO</th>
                                <th style='padding: 10px;'>CUOTAS EN MORA</th>
                                
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4 >CUOTAS DEL CREDITxO</h4></div>
                    <div class="col-sm-12">
                        <table id="tabla-detalle-pago" class="table text-center table-bordered" style="font-size:9px">
                            <thead>
                                <th style='background: ;'>CUOTA</th>
                                <th style='background: ;'>VENCIMIENTO</th>
                                <th style='background: ;'>CAPITAL</th>
                                <th style='background: ;'>INTERÉS</th>
                                <th style='background: ;'>SEGURO</th>
                                <th style='background: ;'>ADMINISTRACIÓN</th>
                                <th style='background: ;'>TECNOLOGÍA</th>
                                <th style='background: ;'>IVA</th>
                                <th style='background: rgba(255, 235, 59, 0.09);'>MONTO POR CUOTA</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>DÍAS DE ATRASO</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>INTERÉS MORA</th>

                                <th style='background: rgba(244, 67, 54, 0.25);'>HONORARIO SMS-IVR-MAIL</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>HONORARIOS RASTREO</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>HONORARIOS PREJURIDICO</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>HONORARIOS BPO</th>

                                <th style='background: rgba(3, 169, 244, 0.23);'>DESCUENTO</th>
                                <th style='background: rgba(139, 195, 74, 0.4);'>MONTO A COBRAR</th>
                                <th style='background: rgba(139, 195, 74, 0.38);'>MONTO COBRADO</th>
                                <th style='background: rgba(139, 195, 74, 0.38);'>FECHA DE COBRO</th>
                                <th style='background: rgba(158, 158, 158, 0.18);'>ESTADO</th>
                                <th></th>
                            </thead>
                            <tbody class="principal">
                                
                            </tbody>
                        </table>
                    </div>
              </div>
                        
          </div>
        </div>
      </div>
    </div>
</div>

<!-- Modal detalle-->
<div class="modal fade" id="credito-detalle" tabindex="-1" role="dialog" aria-labelledby="creditoLabel">
  <div class="modal-dialog" role="document" style="width:100%">
    <div class="modal-content">
      <div class="modal-header text-center">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="creditoLabel"></label></h3>
      </div>
      <div class="modal-body">
        <div class="row" style="margin:0px;">
            <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4 id="titulo-1"></h4></div>
                <div class="col-sm-12">
                    <table class="table text-center" id="detalle-credito-modal">
                        <thead>
                        
                            
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4 id="titulo-2"></h4></div>
                <div class="col-sm-12 ajuste-descuento" style="display: block;">
                                    <br>
                                    
                                    <div class="col-md-4">
                                        <label for="plan_descuent_ajusteo">Planes de descuentox: </label>
                                        <select name="plan_descuento_ajuste" class="form-control" id="plan_descuento_ajuste">
                                            
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="monto_descuento_ajuste">Monto del descuento: </label>
                                        <input type="text" class="form-control" id="monto_descuento_ajuste" readOnly value="0">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="monto_descuento_ajuste">Monto del acuerdo: </label>
                                        <input type="text" class="form-control" id="monto_acuerdo_ajuste" readOnly value="0">
                                    </div>
                                    <div class="col-md-12" style=" padding-top: 5px; color: darkorange;">
                                        <span id="old_monto_ajuste" style="display: none;">Monto del acuerdo antes del descuento:$ <strong></strong></span>
                                    </div>
                                    <div class="col-md-12 text-center" >
                                        <a class="btn btn-primary btn-lg">Guardar Cambios</a>
                                    </div>


                </div>
                <div class="col-sm-12">
                    <table class="table text-center" id="detalle-cuota-modal">
                        <thead>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- DIRECTORIO TELEFONICO MODALS -->
<div class="modal fade" id="new-create-agenda-alone" tabindex="-1" role="dialog" aria-labelledby="agendaLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="agendaLabel"></label></h3>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4 id="new-titulo"></h4></div>
            <div class="col-sm-12"><br></div>
            <div class="col-sm-12" id="form-tel">
                <div class="form-group col-sm-6">
                    <label for="new-numero-new" class="col-form-label">Número:</label>
                    <input type="number" class="form-control" id="new-numero-new" onkeypress='return validaNumericos(event)'>
                </div>
                
                <div class="form-group col-sm-6">
                    <label for="new-contacto" class="col-form-label">Contacto:</label>
                    <input type="text" class="form-control" id="new-contacto-new">
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-fuente" class="col-form-label">Fuente:</label>
                    <select class="form-control" id="new-fuente-new">
                        <option value="" disabled selected >Seleccione</option>
                        <option value="PERSONAL">Personal</option>
                        <option value="PERSONAL LLAMADA">Personal llamada</option>
                        <option value="PERSONAL WHATSAPP">Personal whatsapp</option>
                        <option value="REFERENCIA">Referencia</option>
                        <option value="LABORAL">Laboral</option>
                        <option value="BURO_CELULAR">Buro - Celular - D</option>
                        <option value="BURO_CELULAR_T">Buro - Celular - T</option>
                        <option value="BURO_LABORAL">Buro - Laboral - D</option>
                        <option value="BURO_REFERENCIA">Buro - Referencia - D</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-tipo" class="col-form-label">Tipo:</label>
                    <select class="form-control" id="new-tipo-new">
                        <option value="MOVIL">Movil</option>
                        <option value="FIJO">Fijo</option>
                    </select>
                </div>
                
                <div class="form-group col-sm-6" id="div-departamentos-new">
                    <label for="departamentos" class="col-form-label">Departamento:</label>
                    <select class="form-control" id="departamentos-new">
                        <option value="" disabled selected >Seleccione</option>
                    </select>
                </div>
                <div class="form-group col-sm-6" id="div-ciudad-new">
                    <label for="ciudad" class="col-form-label">Ciudad:</label>
                    <select class="form-control" id="ciudad-new" disabled >
                        <option value="" disabled selected>Seleccione</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Estado:</label>
                    <select class="form-control" id="new-estado-new">
                        <option value="1">Activo</option>
                        <option value="0">Fuera de servicio</option>
                    </select>
                </div>
                <div style="display:none;" class="form-group col-sm-6" id="div-parentesco-new">
                    <label class="col-form-label">Parentesco</label>
                    <select class="form-control" id="parentesco-new">
                        <option value="" disabled selected>Seleccione</option>
                        <option value="0">Sin parentesco</option>
                        <option value="1">Madre</option>
                        <option value="2">Padre</option>
                        <option value="3">Hijo/a</option>
                        <option value="4">Hermano/a</option>
                        <option value="5">Amigo/a</option>
                        <option value="6">Compañero</option>
                        <option value="7">Otro</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Verificacion de Llamada</label>
                    <select class="form-control" id="llamada-verificada-new">
                        <option value="1">Verificado</option>
                        <option value="0">No Verificado</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Verificacion de SMS</label>
                    <select class="form-control" id="sms-verificada-new">
                        <option value="1">Verificado</option>
                        <option value="0">No Verificado</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Verificacion de Whatsapp</label>
                    <select class="form-control" id="wts-verificada-new">
                        <option value="1">Verificado</option>
                        <option value="0">No Verificado</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Llamada</label>
                    <select class="form-control" id="llamada-new">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">SMS</label>
                    <select class="form-control" id="sms-new">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Whatsapp</label>
                    <select class="form-control" id="wts-new">
                        <option value="1">Si</option>
                        <option value="0">No</option>
                    </select>
                </div>
                
            </div>
        </div>

      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success"  id="agendar_tlf_new"><i class="fa fa-plus"></i> AGREGAR</button>
          <button type="button" style="display:none;" class="btn btn-info" id="update_tlf_new"><i class="fa fa-refresh"></i> ACTUALIZAR</button>
          
        </div>
    </div>
</div>
</div>

<!-- DIRECTORIO MAIL MODALS -->
<div class="modal fade" id="new-create-agenda-mail-alone" tabindex="-1" role="dialog" aria-labelledby="agendaLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
				<h3 class="modal-title" id="agendaLabel"></label></h3>
			</div>
			<div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center"
                    style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;">
                    <h4>AGENDAR CORREO</h4>
                </div>
                <div class="col-sm-12"><br></div>
                <div class="col-sm-12" id="form-tel">
                    <div class="form-group col-sm-12">
                        <label for="new-cuenta" class="col-form-label">Cuenta:</label>
                        <input type="email" class="form-control" placeholder="example@dominio.com" id="new-cuenta-mail">
                    </div>
                    
                    <div class="form-group col-sm-12">
                        <label for="new-contacto" class="col-form-label">Contacto:</label>
                        <input type="text" class="form-control" id="new-contacto-mail">
						</div>
						<div class="form-group col-sm-6">
							<label for="new-fuente-mail" class="col-form-label">Fuente:</label>
							<select class="form-control" id="new-fuente-mail">
								<option value="PERSONAL">Personal</option>
								<option value="REFERENCIA">Referencia</option>
								<option value="LABORAL">Laboral</option>
								<option value="BURO_CELULAR">Buro - Celular - D</option>
								<option value="BURO_CELULAR_T">Buro - Celular - T</option>
								<option value="BURO_LABORAL">Buro - Laboral - D</option>
								<option value="BURO_REFERENCIA">Buro - Referencia - D</option>
							</select>
						</div>
						<div class="form-group col-sm-6">
							<label for="new-estado-mail" class="col-form-label">Estado:</label>
							<select class="form-control" id="new-estado">
								<option value="1">Activo</option>
								<option value="0">Fuera de servicio</option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-success" id="btn_agendar_mail_new" ><i class="fa fa-plus"></i> AGREGAR</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo asset('assets/gestion/gestion.js'); ?>"></script>
<script type="text/javascript" src="<?php echo asset('assets/js/cobranzas/cobranzas.js'); ?>"></script>
<script src="<?php echo base_url('assets/gestion/agenda_telefonica_new.js');?>" ></script>


<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script>
    var controladores = [];
    var sin_casos_gestionados = false;
   

    $(document).ready(function(){
        cargarNuevoChat('slot-1', 1);
        cargarNuevoChat('slot-2', 1);
        cargarNuevoChat('slot-3', 1);
        
      
       });

       $('body').on('click','button[id="agendar_tlf_new"]',function(event){
            event.preventDefault();
            id_solicitud = $(this).attr('data-id_solicitud');
            documento = $(this).attr('data-documento');
            agendarTelefonoSolicitanteAlone(documento);

        });
       $('body').on('click','button[id="btn_agendar_mail_new"]',function(event){
            event.preventDefault();
            id_solicitud = $(this).attr('data-id_solicitud');
            documento = $(this).attr('data-documento');
            agendarMailSolicitanteAlone(documento);

        });

   
    function cargarNuevoChat(slot, canal){
        $.ajax({
			url:  base_url + 'chat_uac/ChatUAC/render_new_chat?canal='+canal,
			type: 'GET',

		}).done(function (response) {
           
                $('#'+slot).html(response);
                $('#'+slot).removeClass("animate__animated animate__zoomOutDown animate__delay-.5s");
                $('#'+slot).addClass("animate__animated animate__zoomInUp animate__delay-.5s");
            
            
			
		})
		.fail(function (response) {
       

		})
		.always(function (response) {
		
		});  
    }


     window.addEventListener("message", (event)=>{// recibir mensaje del iframe
        // console.log(event)
        var item = JSON.parse(event.data);
        // console.log(item)
        if (item.function == "consultarCredito") {
            consultarCredito(item.data);
        }

        if (item.function == "consultarPromesasDetalle") {
            consultarPromesasDetalle(item.data);
        }
        if (item.function == "agendarTelefono") {
            
            mostrarFormularios(item.data);
        }
        if (item.function == "agendarMail") {
            
            mostrarFormulariosMail(item.data);
        }
        
      
    });


</script>