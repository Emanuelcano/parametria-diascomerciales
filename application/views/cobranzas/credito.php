<?php 
if(isset($id_solicitud))
{
?>

	<?php if(!empty($automatico)) { ?>
        <script>
            var minutosGestion = <?=$timeConfig['minutos_gestion']?>;
            var minutosExtra = <?=$timeConfig['minutos_extra']?>;
            var cantidadExtensiones = <?=$timeConfig['cantidad_extensiones']?>;
            </script>
	<?php } ?>
    <?php if(!empty($automatico)) { ?>
	<?php } ?>
<section class="content-header">
    <div id="alerta"></div>
    
</section>
<section class="content">
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <input type="hidden" id="id_solicitud" data-paso="<?php echo $solicitude[0]['paso'] ?>" data-tipo="<?php echo $solicitude[0]['tipo_solicitud'] ?>" value="<?php echo $id_solicitud; ?>">
    <input type="hidden" id="credito" data-id_credito="<?php echo $solicitude[0]['id_credito'] ?>" data-status = "PAGADO" >
    <input type="hidden" id="id_cliente" data-id_cliente="<?php echo $solicitude[0]['id_cliente'] ?>" >
    <input type="hidden" id="id_campania" value="<?=(isset($campania['id']) ? $campania['id'] : 0) ?>" >
    
    <div class="row">
        <?php
            $this->load->view('cobranzas/box_title',['solicitude' => $solicitude[0]]);
        ?>
    </div>
    <div class="row row-chat-track" style="<?= ($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO)? 'height: 900px;':'height: 550px;'?> ">

        <div class="<?= ($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO)? 'col-md-5':'col-md-6' ?>" style="height: 95%; padding:0px">
            <?php
                $planes_pago = isset($planes_pago[0])? $planes_pago:[];
                $status_chat = isset($status_chat)?$status_chat:NULL;
                $canal_chat = isset($canal_chat)?$canal_chat:NULL;
                $this->load->view('cobranzas/box_client_info', ['proximo_monto' => $proximo_monto,'planes_descuentos' => $planes_descuentos,'mora_al_dia' => $mora_al_dia, 'acuerdos_pago' => $acuerdos_pago, 'solicitude' => $solicitude[0], 'cuota_mas_antigua' => $cuota_mas_antigua, 'planes_pago' => $planes_pago,  'status_chat' => $status_chat, 'canal_chat' => $canal_chat]);
            ?>
        </div>
        <div class="<?= ($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO)? 'col-md-4':'col-md-6' ?> tracker" style="height: 95%; padding-left:5px;padding-right:5px">
        
                    <div id="box_tracker" class="box box-info" style="height: 100%;">
                        <div class="box-header with-border"></div><!-- end box-header -->
                        <div class="box-body"  style="overflow-y: auto; height: 66%">
                            <div class="tab-pane active" id="timeline" style="padding-top: 35%;">
                                <div class="loader" id="loader-6">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div><!-- end box-body -->
                        <div class="box-footer" style="height: 31%">

                        </div><!-- end box-footer -->
                    </div> <!-- end box-default -->


            <?php //$this->load->view('gestion/box_tracker',['tracker'=>$tracker,'id_solicitud'=>$id_solicitud]);?>
        </div>

        <?php
            //si el operador es de tipo externo no mostramos el historial de chat 
           if($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO) { ?>

                <div class="col-md-3 whatsapp " style="height: 95%; padding:0px;">
                    <div id="box_whatsapp" class="box box-info __chats_list_container" style="height: 100%">
                        <div class="box-header with-border" id="titulo">
                    
                        </div>
                        <div class="box-body"  style="overflow-y: auto; height: 66%">
                            <div class="tab-pane active" id="timeline" style="padding-top: 40%;">
                                <div class="loader" id="loader-6">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div><!-- end box-body -->
                    </div>
                </div>

        <?php  } ?>
    </div>

    <div class="row" style="padding-top: 10px; display: none">

        <div class="dropdown <?= ($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO)? 'col-md-4':'col-md-6' ?>" style="padding:0;">
            <a style="width:100%" class="btn btn-info" id="dLabelMSN" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">MENSAJE<span style="float: right; margin-top: 10px;" class="caret"></span></a>
            <ul class="dropdown-menu" aria-labelledby="dLabelMSN" style="width:100%;box-shadow: rgb(0, 0, 0) 0px 3px 10px -3px;">
                <?php foreach ($template_mensajes as $key => $template) :?>
                    <li style="padding-left:10px" class="cortar">
                        <div class="row" style="margin:0px;">
                            <div class="col-sm-11 cortar" style="padding:0px;">
                                <?= $template['descripcion']; ?>
                            </div>
                            <div class="col-sm-1">
                                <a data-tipo="sms" data-id_template="<?= $template['id']; ?>" data-descripcion_template="<?= $template['descripcion']; ?>" data-id_cliente="<?= $solicitude[0]['id_cliente'] ?>" class="btn btn-success btn-xs template"><i class="fa fa-send"></i></a>
                            </div>
                        </div>
                    </li>
                    <li role="separator" class="divider"></li>
                <?php endforeach;?>
                
            </ul>
        </div>
        <?php if($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO) { ?>
            <div class="dropdown col-md-4" style="padding:0;">
                <a style="width:100%" class="btn btn-success disabled" id="dLabelWapp" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">WHATSAPP<span style="float: right; margin-top: 10px;" class="caret"></span></a>
                <ul class="dropdown-menu" aria-labelledby="dLabelWapp" style="width:100%;box-shadow: rgb(0, 0, 0) 0px 3px 10px -3px;">
                    <li style="padding-left:10px">Mensaje 1</li>
                    <li role="separator" class="divider"></li>
                    <li style="padding-left:10px">Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam sapiente nihil voluptate tenetur eius! Hic obcaecati tempora quia sed accusantium cum, alias ut maxime temporibus exercitationem eos, aut officia nobis!</li>
                    <li role="separator" class="divider"></li>
                    <li style="padding-left:10px" class="cortar">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Rerum libero veniam temporibus modi corporis mollitia officiis, iste tenetur asperiores? Soluta harum temporibus iusto debitis minima nemo deserunt cumque cum deleniti. 3</li>
                </ul>
            </div>
        <?php } ?>
        <div class="dropdown <?= ($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO)? 'col-md-4':'col-md-6' ?>" style="padding:0;">
            <a style="width:100%" class="btn btn-warning " id="dLabelMail" data-target="#" href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">MAIL<span style="float: right; margin-top: 10px;" class="caret"></span></a>
            <ul class="dropdown-menu" aria-labelledby="dLabelMSN" style="width:100%;box-shadow: rgb(0, 0, 0) 0px 3px 10px -3px;">
                <?php foreach ($template_mails as $key => $template) :?>
                    <li style="padding-left:10px" class="cortar">
                        <div class="row" style="margin:0px;">
                            <div class="col-sm-11 cortar" style="padding:0px;">
                                <?= $template['descripcion']; ?>
                            </div>
                            <div class="col-sm-1">
                                <a data-tipo="mail" data-id_template="<?= $template['template']; ?>" data-descripcion_template="<?= $template['descripcion']; ?>" data-id_cliente="<?= $solicitude[0]['id_cliente'] ?>" class="btn btn-success btn-xs template"><i class="fa fa-send"></i></a>
                            </div>
                        </div>
                    </li>
                    <li role="separator" class="divider"></li>
                <?php endforeach;
                    if($this->session->userdata['tipo_operador'] == 13){
                        foreach ($idsCreditos as $k => $cred) {
                ?>
                            <li style="padding-left:10px" class="cortar">
                                <div class="row" style="margin:0px;">
                                    <div class="col-sm-11 cortar" style="padding:0px;">
                                       Desglose del credito <?= $cred; ?>
                                    </div>
                                    <div class="col-sm-1">
                                        <a data-tipo="mail" data-descripcion_template="Desglose del credito <?= $cred; ?>" data-id_cliente="<?= $solicitude[0]['id_cliente'] ?>" data-id_credito="<?= $cred; ?>" class="btn btn-success btn-xs template-desglose"><i class="fa fa-send"></i></a>
                                    </div>
                                </div>
                            </li>
                            <li role="separator" class="divider"></li>
                <?php 
                        }
                    }
                
                ?>


            </ul>
        </div>
    </div>

    <?php
        //si el operador es de tipo externo no mostramos el historial de chat 
        if($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO) { ?>

            <div class="row" style="margin-top: 20px;">
                <?php $this->load->view('gestion/box_agenda_telefono_new',['id_solicitud'=>$id_solicitud,'documento'=>$documento,'tipo_canal'=>2]); ?>

                <?php $this->load->view('cobranzas/box_agenda_mail_new',['id_solicitud'=>$id_solicitud,'documento'=>$documento]);?>
            </div>
            <div class="row" style="margin-top: 20px;">
                    <?php $this->load->view('cobranzas/box_situacion_laboral'); ?>
            </div>

            <div class="row track-marcacion" style="margin-top: 20px;">
                    <?php $this->load->view('cobranzas/box_track_marcacion'); ?>
            </div>
    <?php  } ?>           
</section>

<!-- Modal detalle-->
<div class="modal fade" id="credito-detalle" tabindex="-1" role="dialog" aria-labelledby="creditoLabel">
  <div class="modal-dialog" role="document" style="width:70%">
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
                                        <label for="plan_descuent_ajusteo">Planes de descuento: </label>
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


<div class="modal fade" id="create-agenda" tabindex="-1" role="dialog" aria-labelledby="agendaLabel">
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
                    <label for="new-numero" class="col-form-label">Número:</label>
                    <input type="number" class="form-control" id="new-numero" onkeypress='return validaNumericos(event)'>
                </div>
                
                <div class="form-group col-sm-6">
                    <label for="new-contacto" class="col-form-label">Contacto:</label>
                    <input type="text" class="form-control" id="new-contacto">
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-fuente" class="col-form-label">Fuente:</label>
                    <select class="form-control" id="new-fuente">
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
                    <label for="new-tipo" class="col-form-label">Tipo:</label>
                    <select class="form-control" id="new-tipo">
                        <option value="MOVIL">Movil</option>
                        <option value="FIJO">Fijo</option>
                    </select>
                </div>
                
                <div class="form-group col-sm-6">
                    <label for="departamentos" class="col-form-label">Departamento:</label>
                    <select class="form-control" id="departamentos">
                        <option value="" disabled selected >Seleccione</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="ciudad" class="col-form-label">Ciudad:</label>
                    <select class="form-control" id="ciudad" disabled >
                        <option value="" disabled selected>Seleccione</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-parentesco" class="col-form-label">Parentesco:</label>
                    <select class="form-control" id="new-parentesco">
                        <option value=0>Sin parentesco</option>
                        <?php foreach ($lista_parentesco as $key => $value):?>
                            <option value="<?= $value["id_parentesco"]?>" ><?= $value["Nombre_Parentesco"]?></option>
                        <?php endforeach;?>                                        
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-estado" class="col-form-label">Estado:</label>
                    <select class="form-control" id="new-estado">
                        <option value="1">Activo</option>
                        <option value="0">Fuera de servicio</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12" id="form-mail">
                <div class="form-group col-sm-6">
                    <label for="new-mail" class="col-form-label">Cuenta:</label>
                    <input type="email" class="form-control" id="new-mail">
                </div>
                <div class="form-group col-sm-6">
                    <label for="new-contacto-2" class="col-form-label">Contacto:</label>
                    <input type="text" class="form-control" id="new-contacto-2">
                </div>
            </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" data-tipo_formulario="" id="agendar" onclick="agendarTelefonoMail(<?= $solicitude[0]['id_cliente'] ?>)"><i class="fa fa-plus"></i>AGREGAR</button>
    </div>
    </div>
  </div>
</div>

<div class="modal fade" id="localidad-modal" tabindex="-1" role="dialog" aria-labelledby="localidad-modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="creditoLabel">Agregar departamento y ciudad</label></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label for="departamentos-modal" class="col-form-label">Departamento:</label>
                        <select class="form-control" id="departamentos-modal">
                            <option value="" disabled selected >Seleccione</option>
                        </select>
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="ciudad-modal" class="col-form-label">Ciudad:</label>
                        <select class="form-control" id="ciudad-modal" disabled >
                            <option value="" disabled selected>Seleccione</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url('assets/gestion/agenda_telefonica_new.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/gestion/agenda_mail_new.js');?>"></script>

<script type="text/javascript">
	
    $(function () {
		
		startGestion();
		<?php if(!empty($autollamada) and $autollamadaNumero != '') { ?>
	  		//momentaneo hasta que wolkvox solucione su problema
	  		if (sessionStorage.switch_valor == 'activo_neotell') {
				llamar();		
			} else if (sessionStorage.switch_valor == 'activo_neotell_colombia') {
				llamar();		
			} else if (sessionStorage.switch_valor == 'activo_twilio') {
                startupClient(llamar());
			}
		
			function llamar() {
                setTimeout(function(){

				$("#icontoClose").trigger("click");
                if (sessionStorage.switch_valor == 'activo_neotell') {
					$("#txt_num_man").val('<?= str_replace('+','',PHONE_COD_COUNTRY) .  $autollamadaNumero?>');
                }else if (sessionStorage.switch_valor == 'activo_neotell_colombia') {
					$("#txt_num_man").val('<?= $autollamadaNumero?>');
                }else if (sessionStorage.switch_valor == 'activo_twilio') {
                    $("#txt_num_man").val('<?= PHONE_COD_COUNTRY .  $autollamadaNumero?>');
                }
				$("#btn_call").trigger("click");
            }, 2000);
			}
		<?php } ?>

		<?php if(!empty($templates)) { ?>
			templatesAutomaticos();
		<?php } ?>
		
		<?php if(!empty($automatico)) { ?>
			$("header .progress").removeClass( "hide");
			iniciarTimer();
		<?php } ?>
		
        cargarInfoLaboral();
        
        $("select#departamentos").change(function (event){
            $.ajax({
                url: base_url+'api/credito/get_municipios/'+this.value,
                type: 'GET'
                })
                .done(function(response) {
                    
                    if(typeof(response.municipios) != 'undefined'){
                        let municipios = response.municipios;
                        $("select#ciudad").html('');
                        municipios.forEach(function(municipio, indice, array){
                            $("select#ciudad").append('<option value="'+municipio.nombre_municipio+'" >'+municipio.nombre_municipio+'</option>')
                        });
                        $("select#ciudad").attr('disabled', false);
                    }
                })
                .fail(function() {
                })
                .always(function() {
                });
        });
        $("select#departamentos-modal").change(function (event){
            $.ajax({
                url: base_url+'api/credito/get_municipios/'+this.value,
                type: 'GET'
                })
                .done(function(response) {
                    
                    if(typeof(response.municipios) != 'undefined'){
                        let municipios = response.municipios;
                        $("select#ciudad-modal").html('');
                        municipios.forEach(function(municipio, indice, array){
                            $("select#ciudad-modal").append('<option value="'+municipio.nombre_municipio+'" >'+municipio.nombre_municipio+'</option>')
                        });
                        $("select#ciudad-modal").attr('disabled', false);
                    }
                })
                .fail(function() {
                })
                .always(function() {
                });
        });
        $("a.template").click(function (event) {
            const formData = new FormData();
            var send_to = [];
            let tipo = $(this).data('tipo');
            let id_solicitud = $("#id_solicitud").val();
            let id_operador = $("section").find("#id_operador").val();
            let type_contact;
            let mensaje = "";
            let comment = "";
            let alerta = "";
            let url = "";

            switch (tipo) {
                case 'sms':
                    $.each($("input[name='ch-telefonos']:checked"), function(){
                        if( $(this).data('estado') == 1)
                        send_to.push($(this).data('numero_telefono'));
                    });

                    formData.append("id_cliente", $(this).data('id_cliente'));
                    formData.append("id_mensaje", $(this).data('id_template'));
                    formData.append("telefonos", send_to);

                    type_contact = 13;
                    mensaje = $(this).data('descripcion_template');
                    comment = "<b>[SMS TEXTO]</b><br>Fecha: " + moment().format('DD-MM-YYYY') + "<br> Mensaje: " +  mensaje;
                    alerta = "¿Estas seguro de que quieres enviar el mensaje?";
                    url = base_url+'api/credito/enviarMensaje';
                    break;

                case 'mail':
                    $.each($("input[name='ch-mail']:checked"), function(){
                        if( $(this).data('estado') == 1)
                        send_to.push($(this).data('cuenta'));
                        //console.log($(this).data('cuenta'));
                        
                    });

                    formData.append("id_cliente", $(this).data('id_cliente'));
                    formData.append("id_mail", $(this).data('id_template'));
                    formData.append("cuentas", send_to);
                    
                    //console.log(send_to);
                    type_contact = 13;
                    mensaje = $(this).data('descripcion_template');
                    comment = "<b>[Mail]</b><br>Fecha: " + moment().format('DD-MM-YYYY') + "<br> Mensaje: " +  mensaje;
                    alerta = "¿Estas seguro de que quieres enviar el correo?";
                    url = base_url+'api/credito/enviarMail';
                    break;
            
                default:
                    break;
            }
            
            Swal.fire({
                title: '¡Atención!',
                text: alerta,
                icon: 'warning',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                showCancelButton: 'true'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data:formData,
                        processData: false,
                        contentType: false,
                    }).done(function(response) {
                        if(response.status == '200') {
                            
                            saveTrack(comment, type_contact, id_solicitud, id_operador);
                            Swal.fire({
                                title:"¡Perfecto!",
                                text: response.message,
                                icon: 'success'
                            });
                        } else {
                            Swal.fire({
                                title:"¡Ups!",
                                text: response.message,
                                icon: 'error'
                            });
                        }
                        
                    })
                    .fail(function(response) {
                        //console.log("error");
                    })
                    .always(function(response) {
                        //console.log("complete");
                    });
                    
                }
            });
        });

        $("a.template-desglose").click(function (event) {
            
            var send_to = [];
            
            
            $.each($("input[name='ch-mail']:checked"), function(){
                if( $(this).data('estado') == 1)
                send_to.push($(this).data('cuenta'));
            });
                    
            enviarDetalle($(this).data('id_credito'), $(this).data('id_cliente'), send_to);
        });

    });

	var segundos;
	var porcentajeBarra;
	var warningTime;
	var dangerTime;
	var renovacionesSolicitadas = 0;
	var tiempoSeleccionado;
	var cronometroTimer_id;

		/**
		 * Inicia el timmer de la barra de tiempo
		 */
		function iniciarTimer() {
			tiempoSeleccionado = minutosGestion;
			$("#cronometro-button-warning").remove();
			$("#cronometro-button-danger").hide();

			let botonRenovar = '<a title="Solicitar Renovacion" class="btn btn-xs btn-warning cronometro-button disabled" id="cronometro-button-warning" role="button" style="margin:0;  position:absolute; right:60px" onclick = "renovarTimer()"><i class="fa fa-refresh"></i></a>'
			let botonDescanso = '<a title="Descanso" class="btn btn-xs btn-info cronometro-button" id="cronometro-button-pause" role="button" style="margin:0; position:absolute; right:33px" onclick = "timerDescansoCampania()"><i class="fa fa-pause"></i></a>'
			let botonSalir = '<a title="Salir Campaña" class="btn btn-xs btn-danger cronometro-button" id="cronometro-button-salir" role="button" style="margin:0; position:absolute; right:5px" onclick = "timerSalirCampania()"><i class="fa fa-sign-out"></i></a>'
			$("#timer-progress-bar-container").append(botonRenovar);
			$("#timer-progress-bar-container").append(botonDescanso);
			$("#timer-progress-bar-container").append(botonSalir);

			if (cantidadExtensiones > 0) {
				$("#cronometro-button-warning").removeClass("disabled");
			}

			segundos = tiempoSeleccionado * 60;
			porcentajeBarra = 100;
			warningTime = (tiempoSeleccionado * 60) / 2;
			dangerTime = (tiempoSeleccionado * 60) / 4;
			$("header .progress .progress-bar").html(new Date(segundos * 1000).toISOString().substr(11, 8));
			$("header .progress .progress-bar").addClass("progress-bar-success");
			$("header .progress .progress-bar").removeClass("progress-bar-warning");
			$("header .progress .progress-bar").removeClass("progress-bar-danger");
			cronometrarTimer();
		}

		/**
		 * Sale de la campania manual automatica
		 */
		function timerSalirCampania() {
			pararCronometroTimer();
			salirCampaniaManual(function () {
				setTimeout(() => {
					console.log('1cronometro parado')
				}, 500);
				$("#timer-progress-bar-container").hide();
				location.reload();
			});
		}

		/**
		 * Pone al operador en descanso en las campanias manuales automaticas
		 */
		function timerDescansoCampania() {
			pararCronometroTimer();
			descanso_campania_manual(function () {
				setTimeout(() => {
					console.log('2cronometro parado')
				}, 500);
				$("#timer-progress-bar-container").hide();
				location.reload();
			});
		}

		/**
		 * Inicio cronometro barra de tiempo
		 */
		function cronometrarTimer() {
			cronometroTimer_id = setInterval(function () {
				escribirTimer();
			}, 1000);
			console.log("inicio cronometro: " + cronometroTimer_id);
		}

		/**
		 * Para el cronometro de la barra de tiempo
		 */
		function pararCronometroTimer() {
			cronometroTimer_id = clearInterval(cronometroTimer_id);
		}

		/**
		 * Renueva el tiempo de la barra de tiempo
		 */
		function renovarTimer() {
			renovacionesSolicitadas++;
			if (renovacionesSolicitadas >= cantidadExtensiones) {
				$("#cronometro-button-warning").hide();
			}
			segundos = minutosExtra * 60;
			tiempoSeleccionado = minutosExtra;
			porcentajeBarra = 100;
			warningTime = (tiempoSeleccionado * 60) / 2;
			dangerTime = (tiempoSeleccionado * 60) / 4;
			$("header .progress .progress-bar").addClass("progress-bar-success");
			$("header .progress .progress-bar").removeClass("progress-bar-warning");
			$("header .progress .progress-bar").removeClass("progress-bar-danger");
		}

		/**
		 * Corre el tiempo de la barra de tiempo
		 */
		function escribirTimer() {
			segundos--;

			porcentajeBarra = porcentajeBarra - 100 / (tiempoSeleccionado * 60);

			$("header .progress").removeClass("hide");
			$("header .progress .cronometro-button").removeClass("hide");
			$("header .progress .progress-bar").css("width", porcentajeBarra + "%");
			$("header .progress .progress-bar").html(new Date(segundos * 1000).toISOString().substr(11, 8));

			if (segundos <= warningTime && segundos > dangerTime) {
				$("header .progress .progress-bar").removeClass("progress-bar-success");
				$("header .progress .progress-bar").addClass("progress-bar-warning");
			} else {
				if (segundos <= dangerTime) {
					$("header .progress .progress-bar").removeClass("progress-bar-warning");
					$("header .progress .progress-bar").addClass("progress-bar-danger");
				}
			}

			if (segundos === 30 && renovacionesSolicitadas < cantidadExtensiones) {
				Swal.fire({
					title: 'Queda poco tiempo de gestion. Quieres una renovacion de tiempo?',
					showDenyButton: true,
					showCancelButton: true,
					confirmButtonText: 'Si',
					cancelButtonText: `No`,
				}).then((result) => {
					if (result.value) {
						renovarTimer();
					}
				})
			}

			if (segundos == 0) {
				endGestion(function () {
					let id_solicitud = $("#id_solicitud").val();
					let id_operador = $("#id_operador").val();
					let idCredito = <?php echo $solicitude[0]['id_credito'] ?>;

					$.ajax({
						url: base_url + 'api/solicitud/checkSolicitudHasTrackToday',
						type: 'POST',
						data: {"id_solicitud": $('#id_solicitud').val()},
					}).done(function (response) {
						if (response.data === false) {
							//no hay track, por tanto no realizo ninguna accion. Trackeo que cerro sin realizar accion
							saveTrackCredito('[CAMPAÑA MANUAL CERRADO SIN GESTION]', 182, id_solicitud, id_operador, function () {
								$.ajax({
									url: base_url + 'api/campanias/removeCreditoDeOperador',
									type: 'POST',
									data: {"idCredito": idCredito, "idOperador": id_operador},
								}).done(function (response) {
									window.location.href = base_url + 'atencion_cobranzas/cobranzas';
								})
							});
						} else {
							window.location.href = base_url + 'atencion_cobranzas/cobranzas';
						}
					});
				});
			}
		}

		/**
		 * Si no hubo gestion el dia de  hoy enviara los templates que esten cargados
		 */
		function templatesAutomaticos() {
			$.ajax({
				url: base_url + 'api/solicitud/checkSolicitudHasTrackToday',
				type: 'POST',
				data: {"id_solicitud": $("#id_solicitud").val()},
			}).done(function (response) {
				if (response.data == false) {
					let templates = <?=json_encode($templates)?>

					if (templates.whatsapp.mensaje != '') {
						sendTemplateWhatsapp(templates.whatsapp);
					}
					if (templates.sms != '') {
						sendTemplateSMS(templates.sms);
					}
					if (templates.email != '') {
						sendTemplateEmail(templates.email);
					}
				}
			});
		}

		/**
		 * Envio de Templates de Whatsapp
		 *
		 * @param data
		 */
		function sendTemplateWhatsapp(data) {
			if (data.canal == "1") {
				url_base = base_url + 'comunicaciones/twilio';
			} else {
				url_base = base_url + 'comunicaciones/TwilioCobranzas';
			}

			$.ajax({
				url: url_base + '/send_template_message_new',
				type: 'POST',
				data: {
					"solID": <?=$id_solicitud ?>,
					"phoneN": data.numero,
					"Template": data.mensaje,
					"id_template": data.templateId
				},
				dataType: 'json',
			}).done(function (re) {
				trackTemplateWhatsapp();
			});
		}

		/**
		 * Envio de Templates de SMS
		 *
		 * @param data
		 */
		function sendTemplateSMS(data) {
			if (data.numero != '' && data.mensaje != '') {
				$.ajax({
					url: base_url + 'api/ApiSolicitud/enviarSmsIvrAgendaTelefonica',
					type: 'POST',
					data: {
						"numero": data.numero, //consultar
						"text": data.mensaje,
						"servicio": 2,
						"tipo_envio": 2,
					},
					dataType: 'json',
					crossDomain: true,
					beforesend: function () {
						request.setRequestHeader("Access-Control-Allow-Origin", '*');
					},
				}).done(function () {
					trackTemplateSMS();
				})
			} else {
				console.log('template SMS no enviado. Datos:')
				console.log(data);
			}
		}

		/**
		 * Envio de templates de Emails
		 *
		 * @param data
		 */
		function sendTemplateEmail(data) {
			if (data.direccion != '' && data.template != '0' && data.logica != 0) {
				$.ajax({
					url: base_url + 'api/solicitud/enviarMailAgendaPepipost',
					type: 'POST',
					data: {
						"documento": <?=$documento ?>,
						"mail": data.direccion,
						"id_template": data.template,
						"id_logica": data.logica,
					},
					dataType: 'json',
					crossDomain: true,
					beforesend: function () {
						request.setRequestHeader("Access-Control-Allow-Origin", '*');
					},
				})
					.done(function (response) {
						trackTemplateEmail();
					})
					.fail(function () {

					});
			} else {
				console.log('template emails no enviado. Datos:');
				console.log(data);
			}

		}

		/**
		 * Guarda Trackeos
		 *
		 * @param comment
		 * @param typeContact
		 * @param idSolicitude
		 * @param idOperator
		 * @param callback
		 */
		function saveTrackCredito(comment, typeContact, idSolicitude, idOperator, callback) {
			$('#btn_save_comment').addClass('disabled');
			$.ajax({
				url: base_url + 'api/track_gestion',
				type: 'POST',
				dataType: 'json',
				data: {
					'observaciones': comment,
					'id_tipo_gestion': typeContact,
					'id_solicitud': idSolicitude,
					'id_operador': idOperator
				}
			}).always(callback);
		}

		/**
		 * Guarda el trakeo del template enviado
		 *
		 * @param templateType
		 * @param callback
		 */
		function trackTemplate(templateType, callback) {
			let id_solicitud = $("#id_solicitud").val();
			let id_operador = $("#id_operador").val();
			saveTrackCredito('Template ' + templateType + " enviado", 182, id_solicitud, id_operador, callback);
		}

		/**
		 * Guarda el trakeo del envio del template de SMS
		 *
		 * @param callback
		 */
		function trackTemplateSMS(callback) {
			trackTemplate('SMS', callback);
		}

		/**
		 * Guarda el trakeo del envio del template de Email
		 *
		 * @param callback
		 */
		function trackTemplateEmail(callback) {
			trackTemplate('Email', callback);
		}

		/**
		 * Guarda el trakeo del envio de template de Whatsapp
		 *
		 * @param callback
		 */
		function trackTemplateWhatsapp(callback) {
			trackTemplate(' Whatsapp ', callback);
		}

		/**
		 * Registra el inicio de la gestion del caso
		 */
		function startGestion() {
			let id_operador = $("#id_operador").val();
			let idCredito = <?php echo $solicitude[0]['id_credito'] ?>;
			$.ajax({
				url: base_url + 'api/campanias/startGestion',
				type: 'POST',
				data: {"idCampania": '<?=(isset($campania['id'])) ? $campania['id'] : 0 ?>', "idCredito": idCredito, "idOperador": id_operador},
			}).done()
		}

		/**
		 * Registra el fin de la gestion del caso
		 *
		 * @param callback
		 */
		function endGestion(callback) {
			let id_operador = $("#id_operador").val();
			let idCredito = <?php echo $solicitude[0]['id_credito'] ?>;
			$.ajax({
				url: base_url + 'api/campanias/endGestion',
				type: 'POST',
				data: {
					"idCampania": '<?=(isset($campania['id'])) ? $campania['id'] : 0 ?>',
					"idCredito": idCredito,
					"idOperador": id_operador
				},
			}).done(callback)
		}
</script>

<?php 
} 
?>
