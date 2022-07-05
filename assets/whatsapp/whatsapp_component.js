//Envio mensajes
function sendMessages(channel, canal, file = '') {
	let base_url = $("#base_url").val();
	const formData = new FormData();
	let mensaje = $("#"+channel+"-mensaje").val()
	let controlMessage = mensaje.replace(/[\n\r\s]+/gi, '');
    let status_chats = $('input[name="status_chats"]').val();
    if (controlMessage.length > 0  || file !== '') {

		formData.append('media', file);
		formData.append('message', mensaje);
		formData.append('chatID', channel);
		formData.append('operatorID', $("#hdd_id_operador").val());
        formData.append('cantidad', true);
        formData.append('status_chats', status_chats);
        
		if (canal == '188') {
			base_url += "comunicaciones/TwilioCobranzas/send_new_message"
        } else if (canal == '334' || canal == '049') {
            base_url += "comunicaciones/Twilio/send_new_message"
        }

		$("#chat-panel a.send-messages").addClass("disabled");
		
		$.ajax({
			url:  base_url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
		}).done(function (response) {

			if (typeof(response.error) == "undefined") {
				$("#"+channel+"-mensaje").val('');
			} else {
				Swal.fire('¡Atención!',response.description, 'error')
			}

			$("#"+channel+"-adjunto").val('');
			$("chat-panel a").removeClass("disabled");
			
		})
		.fail(function (response) {
            
            if(canal == null || canal == undefined || canal == 0){
                Swal.fire('¡Atención!','Debe seleccionar un canal', 'error');
            } else {
                Swal.fire('¡Atención!','No fue posible establecer la comunicacion', 'error');
            }
			
			$("#"+channel+"-adjunto").val('');
			$("#chat-panel a.send-messages").removeClass("disabled");

		})
		.always(function (response) {
		
		});  
	}
}

//Envio Templates
function send_template(mensaje, template, canal) {
	let solID = $("#id_solicitud").val();
	let base_url = $("#base_url").val();
    let numeroN = $("#template").data('numero');

	let formData = new FormData();

	formData.append('solID', solID);
	formData.append('phoneN', numeroN);
	formData.append('Template', mensaje);
	formData.append('id_template', template);
    formData.append('operatorID', $("#hdd_id_operador").val());
    
	if (canal == "1") {
		url_base = base_url + 'comunicaciones/twilio';
	} else {
		url_base =base_url + 'comunicaciones/TwilioCobranzas';
	}        

	swal.fire({
		title: "¿Esta seguro?",
		text: "¿Estas seguro de enviar el template seleccionado?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Si, Enviar"
	}).then(function (result) {
		$('#collapseTemplateWap').collapse("toggle");
		if (result.value) {
			 $.ajax({
					url: url_base + '/send_template_message_new',
					type: 'POST',
                    data: formData,
					processData: false,
					contentType: false,
				}).done(function (re) {
					if (re.template) {
						if (re.template === true) {
							swal.fire('Exito','Mensaje Enviado, a la espera del cliente','success');
						} else {
							swal.fire('Error','Ocurrió un error con la cookie asociada, trata nuevamente o prueba cerrar a iniciar sesión','error');
						}
					}

					if (re.chat) {
						if (re.operator) {
							swal.fire('Informacion','Existe un Chat activo con éste cliente. Operador: ' + re.operator,'info');
						} else {
							swal.fire('Informacion','Se ha levantado un nuevo Chat o el chat activo','info');
						}
					}

					if (re.self) {
						swal.fire('Informacion','Has reclamado ésta conversación. Se ha levantado un chat activo','info');
					}
				});
		}
	});
}

//obtengo los templates con las variables traducidas
function get_templates_wapp(status_chat, id_chat) {
	let id = $("#id_solicitud").val();
    let canal = $('#selectCanal').find(":selected").text();
    if(canal == '15185188' || typeof canal == 'undefined') {
        canal = '1'; 
    } else {
        canal = '2'; 
    }

    if(id == '' || id == null || id == undefined) {
        $('button#template-'+id_chat).on('click', function(){
            $('#my_modal_template_'+id_chat).modal('hide');
        })
        swal.fire('Error','Los templates no estan habilitados para visitantes','error');
    
    } else {
     
        $('button#template-'+id_chat).on('click', function(){
            $('#my_modal_template_'+id_chat).modal('show');
        })

        let base_url = $("#base_url").val();
    
        if ($("#collapseTemplateWap-"+id_chat+" .well").html() == "") {
            $.ajax({
                url: base_url + 'atencion_cliente/makeTemplateSend/'+id+'/'+canal+'/WAPP',
                type: 'GET',
            })
                .done(function (response) {
                    let defecto = elementos = '';
                    let grupos = JSON.parse(response).grupo_template;
                    
                    elementos += '<div class="panel-group" id="accordion-'+id_chat+'" role="tablist" aria-multiselectable="true">';
                    grupos.forEach(grupo => {
                            let templates = grupo.template;

                            elementos += '<div class="panel panel-success">';
                            elementos += '<div class="panel-heading" role="tab" id="heading-'+grupo.grupo+'">';
                            elementos += '<h4 class="panel-title"><a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse-'+grupo.grupo+'" aria-expanded="true" aria-controls="collapse-'+grupo.grupo+'">';
                            elementos += grupo.grupo + '</a></h4></div>'
                            elementos += '<div id="collapse-'+grupo.grupo+'" class="panel-collapse collapse " role="tabpanel" aria-labelledby="heading-'+grupo.grupo+'">';
                            
                            templates.forEach(template => {
                                if (template.por_defecto === "1") {
                                    defecto += '<div class="panel-body"><div class="col-sm-11 mensaje">'+template.msg_string+'</div>';
                                    defecto += '<div class="col-sm-1"><button data-proveedor="1" data-id_template="'+template.id+'" class="btn btn-success btn-sm" ' + ( status_chat != 'vencido' ? 'disabled' : '' ) + '><i class="fa fa-send"></i></button>';
                                    defecto += '</div></div>';            
                                }
                                
                                elementos += '<div class="panel-body">';
                                elementos += '<div class="col-sm-11 mensaje">'+template.msg_string+'</div>';
                                elementos += '<div class="col-sm-1">';
                                elementos += '<button data-proveedor="1" data-tipo_template="WAPP" data-id_template="'+template.id+'" class="btn btn-success btn-sm" ' + ( status_chat != 'vencido' ? 'disabled' : '' ) + '><i class="fa fa-send"></i></button>';
                                elementos += '</div></div>';
                            });
                            elementos += '</div></div>';
                            
                            
                        });
                        elementos += '</div>';
                        
                    $("#collapseTemplateWap-"+id_chat+" .well").html(defecto+' '+elementos);
                    
                    $("#collapseTemplateWap-"+id_chat+" .panel-body").on('click', function () 
                    {
                        $("#collapseTemplateWap-"+id_chat+" .panel-body").removeClass("habilitar_send_template");
                        $(this).addClass("habilitar_send_template");

                        $("#collapseTemplateWap-"+id_chat+" .panel-body").css('background-color', '#FFFFFF');
                        if($(this).hasClass('habilitar_send_template')){
                            $(this).css('background-color', '#CCCCCC');
                        } 
                        
                        $("#collapseTemplateWap-"+id_chat+" .panel-body button").prop("disabled", true);
                        $(this).find("button").prop("disabled", false);
                        $("#collapseTemplateWap-"+id_chat+" .panel-body button").prop("onclick", null);
                        
                        $(this).find("button").on("click", function () {
                            let mensaje = $(this).closest(".panel-body").find(".mensaje").text();
                            let template = $(this).data("id_template");
                            send_template(mensaje, template, canal);
                        });

                    });

                    let tope = parseInt($("ul#ul_detalle_chat").offset().top) - $("#collapseTemplateWap-"+id_chat).height() - 160;
                    
                    $("#collapseTemplateWap-"+id_chat).css("top", tope+"px");
                    $("#collapseTemplateWap-"+id_chat).css("left", "-280px");
                    $('#collapseTemplateWap-'+id_chat).collapse("toggle");
                })
                .fail(function () {

                });
        } else {
                    let tope = parseInt($("ul#ul_detalle_chat").offset().top) - $("#collapseTemplateWap").height() - 160;
                    $("#collapseTemplateWap-"+id_chat).css("top", tope+"px");
                    $("#collapseTemplateWap-"+id_chat).css("left", "-280px");
                    $('#collapseTemplateWap-'+id_chat).collapse("toggle");
                }
    }
}

//renderizo mensajes
function get_mensajes_chat(id_chat, paginacion, canal) {
//    debugger;
    let base_url = $("#base_url").val();
    // alert($("#hdd_id_operador_"+id_chat).val());
    const formData = new FormData();
    formData.append("id_chat", id_chat);
    formData.append("pagina", paginacion);
    formData.append("documento", $("#hdd_id_operador_"+id_chat).val());
    formData.append("solicitud", $("#hdd_id_solicitud_"+id_chat).val());

    let objDiv = $("#chat-panel-"+id_chat+" .main-body");
        if (objDiv.length > 0) {
            objDiv = $("#chat-panel-"+id_chat+" .main-body")[0];
        } else {
            objDiv = null;
        }
    let scrollHeightOld = objDiv.scrollHeight;

    
    if (paginacion > -1) {
        $.ajax({
            url: base_url + 'whatsapp/getOperadorChat',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        })
            .done(function (response) {
                
                let elementos = '';
                if (response.status.ok && typeof (response.chatCliente) != 'undefined') {
                    
                response.chatCliente.forEach(msg => {

                    elementos += '<div class="row chat-message-bot mb-4';
                    elementos += (msg.recibido == 1) ? ' chat-message-bot" style="padding-left: 10px;' : '';
                    elementos += (msg.recibido == 0) ? ' chat-message-user-h' : '';
                    elementos += '">';

                    elementos += (msg.recibido == 0) ? '<div style="display:flex;width:100%;justify-content:flex-end;"></div>' : '';

                    elementos += '<table class="';
                    elementos += (msg.recibido == 1) ? ' chat-entry-bot' : '';
                    elementos += (msg.recibido == 0) ? ' chat-entry-user' : '';
                    elementos +=  '">';
                    elementos += '<tbody><tr>';
                    elementos += '<td><div class="bubble" ><p class="__msg_body">' + nl2br(msg.body ,false) + '</p>';

                    elementos += (msg.media_url0 && (msg.media_content_type0 == 'image/jpeg' || msg.media_content_type0 == 'image/gif' || msg.media_content_type0 == 'image/png')) ? '<img src="' + msg.media_url0 + '" alt="Mensaje Multimedia" class="message_image mt-4 mb-3 d-block"  style="width: 100%!important;">' : '';
                    elementos += (msg.media_url0 && msg.media_content_type0 == 'application/pdf') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= ' + base_url + '"assets/images/icons/pdf-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
                    elementos += (msg.media_url0 && msg.media_content_type0 == 'text/csv') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= ' + base_url + '"assets/images/icons/excel-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
                    elementos += (msg.media_url0 && (msg.media_content_type0 == 'audio/amr' || msg.media_content_type0 == 'audio/mp4' || msg.media_content_type0 == 'audio/mpeg' || msg.media_content_type0 == 'audio/ogg')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-headphones" aria-hidden="true" style="margin-right:.5rem;"></i> Escuchar audio</a>') : '';
                    elementos += (msg.media_url0 && (msg.media_content_type0 == 'video/3gpp' || msg.media_content_type0 == 'video/mp4')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-eye " aria-hidden="true" style="margin-right:.5rem;"></i> Ver Video</a>') : '';
                    
                    elementos += '</div><div class="message-date">' + msg.fecha_creacion + '<br>';
                    elementos += (msg.recibido == 0 && typeof (msg.nombre_operador) != 'undefined' && msg.nombre_operador != null) ? msg.nombre_operador : '';
                    elementos += '</div></td>';
                    
                    if (msg.recibido == 0) {
                        elementos += '<td class="davi-icon" id="sent-' + msg.sms_message_sid + '">';
                        if (msg.sms_status === 'queued' || msg.sms_status === 'sent') {
                            elementos += '<img src="' + base_url + 'assets/images/icons/single-grey.svg" alt="status icon" class="__status_icon">';
                        }
                        if (msg.sms_status === 'delivered') {
                            elementos += '<img src="' + base_url + 'assets/images/icons/double-grey.svg" alt="status icon" class="__status_icon">';
                        }
                        if (msg.sms_status === 'read') {
                            elementos += '<img src="' + base_url + 'assets/images/icons/double-blue.svg" alt="status icon" class="__status_icon">';
                        }
                        if (msg.sms_status === 'failed') {
                            elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
                        }
                        if (msg.sms_status === 'failed') {
                            elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
                        }

                        elementos += '</td>';
                    }

                    elementos += '</tr></tbody></table>';
                    elementos += (msg.sent == 1) ? '</div>' : '';
                    elementos += '</div>';
                });
                
                $('.loader-'+id_chat).addClass('hide');
                $('.welcome#mensajes-'+id_chat).prepend(elementos);
                
                $('#paginacion-'+id_chat).val(response.pagina);
              

                    let element = $("#chat-panel-"+id_chat+" .main-body")[0];
                        element.scrollTop = (element.scrollHeight - scrollHeightOld);
                    if(response.chatCliente.length > 0){
                        let status_chat = ((response.chatCliente[0].status_chat != 'activo')? false:true);

                        //help-links
                        let help_links = `
                                    <div class="col-xs-11" style="padding-right: 0;">
                                        <textarea id="` + id_chat + `-mensaje" class="form-control" rows="3" style="width: 100%; resize: none;" `+ ((!status_chat)? "disabled" : "") +` ></textarea>
                                    </div>
                                    <div class="col-xs-1" style="padding-left: 10px;padding-right: 0px;" >
                                        <a class=" btn btn-lg btn-success send-messages `+ ((!status_chat)? "disabled" : "") +`" onclick="sendMessages( '` + id_chat + `',` + canal + `);waitResponse('` + id_chat + `',` + canal + `)" style=" width: 100%;padding-left: 0px; padding-right: 0px;" ><i class="fa fa-send"></i></a>
                                    </div>

                                    <div class="col-xs-12">

                                            <a class="btn adjuntos  `+ ((status_chat)? "" : "disabled") +`" id="adjunto-`+id_chat+`" href="javascript:void(0)" title="Agregar imagen" style="padding:0px" > 
                                                <label  title="Subir imagen" style="padding: 0px 5px;font-size: x-large;margin-bottom:0px">
                                                    <img src="public/chat_files/img/adjunto.png" style="width:30px" /></i>
                                                    <input id="` + id_chat + `-adjunto" data-channel="` + id_chat + `" data-canal="` + canal + `" type="file" name="media" accept=".gif,.pdf,.jpeg,.jpg,.png,.pdf" style="display: none;">
                                            
                                                </label>
                                            </a>
                                `;
                                //si el chat esta asociado a un cliente
                                if(response.chatCliente[0].documento != null/* true*/){
                                    help_links += `
                                                    <a data-toggle="modal" role="button" href="#my_modal_template_`+id_chat+`"  class="btn templates `+ ((status_chat)? "disabled" : "") +`" id="template-`+id_chat+`" onclick="get_templates_wapp('` + response.chatCliente[0].tlf_cliente + `', `+id_chat+`)"><img src="public/chat_files/img/templates_aprobados.png" style="width:30px" /></a>
                                                    <input id="id_solicitud" type="hidden" value="` + response.chatCliente[0].id_solicitud + `"/>
                                            
                                                            
                                                    <a class="btn  linkPago `+ ((status_chat)? "" : "disabled") +`" role="button" data-canal-chat="`+((canal == '188')? 'cobranzas':'ventas')+`" data-mobilephone = "` + response.chatCliente[0].tlf_cliente + `" data-medio-pago="efectivo" style="padding:0px" aria-expanded="false" onclick="envioLinkDePago(this)" title = "Enviar link Efectivo Baloto, Gana, Daviplata, otros" > <img src="public/chat_files/img/efectivo.png" style="width:30px" /></a>
                                                    <a class="btn  linkPago `+ ((status_chat)? "" : "disabled") +`" role="button" data-canal-chat="`+((canal == '188')? 'cobranzas':'ventas')+`" data-mobilephone = "` + response.chatCliente[0].tlf_cliente + `" data-medio-pago="PSE" style="padding:0px" aria-expanded="false" onclick="envioLinkDePago(this)" title = "Enviar link PSE"> <img src="public/chat_files/img/pse.png" style="width:30px" /></a>

                                                    
                                                        
                                                        <div class="dropdown" id="dropdown-pagares" style="display: inline-block!important;margin-top: 7px;position:relative;">
                                                        <button class="btn  dropdown-toggle" style="background-color: transparent !important; border-color:;" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                         <i class="fa fa-file-pdf-o fa-2x" aria-hidden="true" id="iconoBoleta" style="color:#F7C327;"></i>
                                                        
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">`;
                                                            for (i = 0; i < response.pagares.length; i++) {
                                                                if (response.pagares[i].extension == '.pdf' && response.pagares[i].etiqueta != "Certificado Laboral Operador" && response.pagares[i].etiqueta != "Certificacion Laboral Operador" && response.pagares[i].etiqueta != "Transferencia Desembolso") {
                                                                    help_links += `<li><a onclick="send_pagare(this)" class="dropdown-item" href="#" canal="${canal}" telefono="${response.chatCliente[0].tlf_cliente}" id_chat="${id_chat}" id="send_pagare_`+id_chat+"_"+response.pagares[i].sufijo+`" path_doc="` + response.pagares[i].patch_imagen + `">` + response.pagares[i].etiqueta + `</a></li>`;
                                                                }
                                                            }  
                                                         help_links += `</ul>
                                                         </div>
                                                     

                                                    
                                                        
                                                            
                                                           
                                                        
                                                        



                                                        <div class="dropdown" style=" margin-top:11px;display: -webkit-inline-box; position:absolute;">
                                                         <button id="dropbtn-`+id_chat+`" class="btn_cobranza" estado="oculto" style="color: white; font-size: 16px; border: none; cursor: pointer; background-color: transparent !important; margin-top:5%; display: none;"><i class="fa fa-share-alt-square fa-2x" aria-hidden="true" id="iconoBoleta" style="color:#3EC79B;"></i></button>
                                                                <div class="dropdown-content" id="dropdown-content-`+id_chat+`" style="position:absolute; display: none; min-width: 136px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index:9; background:#fff; width: 8%; bottom:45%;">
                                                                    <div id="botones-`+id_chat+`" style="display: block;">
                                                                        <div class="envioWhatsapp">
                                                                            <a class="btnSub`+id_chat+`" style="width:100%;" id="bontonesEfecty`+id_chat+`" canal="`+((canal == '188')? '2':'1')+`" seleccionado="btnEfecty`+id_chat+`" codConv="111694" sbmenu="botonesEnv" submenu="bontonesEfecty`+id_chat+`" metodo="'efecty'" telefono="` + response.chatCliente[0].tlf_cliente + `" documento="` + response.chatCliente[0].documento + `">Efecty</a>
                                                                            <div id="btnEfecty`+id_chat+`" class="botonesEnv"></div>
                                                                        </div>
                                                                        <div class="envioWhatsapp">
                                                                            <a class="btnSub`+id_chat+`" style="width:100%;" submenu="bontonesCorres`+id_chat+`" sbmenu="botonesEnvCbr"`+id_chat+`" id="bontonesCorres`+id_chat+`" seleccionado="btnCorres`+id_chat+`" codConv="90652" sbmenu="botonesEnv" canal="`+((canal == '188')? '2':'1')+`" metodo="'corresponsal'" telefono="` + response.chatCliente[0].tlf_cliente + `"documento="` + response.chatCliente[0].documento + `">Corresponsal</a>
                                                                            <div id="btnCorres`+id_chat+`" class="botonesEnv"></div>
                                                                        </div>
                                                                        <div class="envioWhatsapp">
                                                                            <a class="btnSub`+id_chat+`" style="width:100%;" submenu="bontonesDepos`+id_chat+`" id="bontonesDepos`+id_chat+`" seleccionado="btnDespo`+id_chat+`" codConv="90652" sbmenu="botonesEnv" style="color:black;" canal="`+((canal == '188')? '2':'1')+`" metodo="'deposito'" telefono="` + response.chatCliente[0].tlf_cliente + `"documento="` + response.chatCliente[0].documento + `">Deposito</a>
                                                                            <div id="btnDespo`+id_chat+`" class="botonesEnv"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                        </div>
                                                       
                                                        
                                                        
                                                        
                                                      

                                                `;

                                                $.ajax({
                                                    type: "post",
                                                    url: base_url + "api/ApiSolicitud/buscarCredito",
                                                    data: {"documento":response.chatCliente[0].documento},
                                                    success: function (respuesta) {
                                                        response = JSON.parse(respuesta);
                                                        if (response == true) {
                                                            $("#dropbtn-"+id_chat).css("display", "block");
                                                        }
                                                    }
                                                });

                                }
                                help_links += `
                                    </div>
                                `;

                                $('.help-links-'+id_chat).html(help_links);
                                $('.help-links-'+id_chat+' textarea').on('keypress',function(event){
                                    var keycode = (event.keyCode ? event.keyCode : event.which);
                                    if(keycode == '13'){
                                        $('.help-links-'+id_chat+' .send-messages').click();
                                    }
                                });
                            
                    }


                }
                carga_boletas_pago(id_chat);

                //renderizo datos de la cabecera del chat
               
                if (typeof $('#sp_primer_mensaje_recibido').html() != 'undefined')
                    render_header_data(response);
    
                $('.loader-'+id_chat).addClass('hide');

                $("#chat-panel-"+id_chat+" .main-body").scroll(function () {
                    if ($(this).scrollTop() === 0 && $('#chat-panel-'+id_chat+' .main-body').hasScrollBar()) {
                   
            
                        let canal = $(".list-group-item.selection_chat").closest(".div_chat").data('canal_chat');
                        $("#chat-panel .loader").removeClass('hide');
                        get_mensajes_chat(id_chat, $("#paginacion-"+id_chat).val(), canal);
                    }
                });
                $(".row.main-body").css('max-height',$("#panel_mensajes_"+id_chat).height()-130);
                $(".row.main-body").css('height',$("#panel_mensajes_"+id_chat).height()-130);

            })
            .fail(function () {
            })
            .always(function () {
            });
    } else {
        $('.loader-'+id_chat).addClass('hide');
    }
}

function status_sms(status, mensaje) {
    let elementos = '';
    if (status === 'queued' || status === 'sent') {
        elementos += '<img src="' + base_url + 'assets/images/icons/single-grey.svg" alt="status icon" class="__status_icon">';
    }
    if (status === 'delivered') {
        elementos += '<img src="' + base_url + 'assets/images/icons/double-grey.svg" alt="status icon" class="__status_icon">';
    }
    if (status === 'read') {
        elementos += '<img src="' + base_url + 'assets/images/icons/double-blue.svg" alt="status icon" class="__status_icon">';
    }
    if (status === 'failed') {
        elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
    }
    if (status === 'undelivered') {
        elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
    }

    $("#sent-"+mensaje).html(elementos);
}

//Carga el chat seleccionado
function cargar(id_chat, canal,bandera=null){
    // debugger;
    //console.log("entro cargar", id_chat);
    if(typeof(pusher) != "undefined"){
        if (bandera == null){
        $.each(channels, function( index, value ) {
            //console.log("AQUI: "+value.name)
            pusher.unsubscribe(value.name);
            value.unbind();
        });

            channels = [];
        }
    }
    //Pusher.logToConsole = true;
    $(".welcome#welcome-"+id_chat).html('');
    // debugger;
    get_mensajes_chat(id_chat, $('#paginacion-'+id_chat).val(), canal);
    
    var channel = pusher.subscribe('channel-chat-'+id_chat);
    channels.push(channel);

    channel.bind('received-message-component', function(data) {
        displayMessage(data, id_chat);
    });
    channel.bind('sent-message-component', function(data) {
        displayMessage(data, id_chat);
    });
    channel.bind('message-status', function(data) {
        status_sms(data.status, data.messageID);
    });

}

// renderiza mensaje
function displayMessage(msg, id_chat = 0) {

    elementos='';
    elementos += '<div class="row chat-message-bot mb-4';
    elementos += (msg.received == 1) ? ' chat-message-bot" style="padding-left: 10px;' : '';
    elementos += (msg.sent == 1) ? ' chat-message-user-h' : '';
    elementos += '">';

    elementos += (msg.sent == 1) ? '<div style="display:flex;width:100%;justify-content:flex-end;"></div>' : '';

    elementos += '<table class="';
    elementos += (msg.received == 1) ? ' chat-entry-bot' : '';
    elementos += (msg.sent == 1) ? ' chat-entry-user' : '';
    elementos += '">';
    elementos += '<tbody><tr>';
    //elementos += (msg.received == 1) ? ('<td class="davi-icon"></td>') : '';
    elementos += '<td><div class="bubble"><p class="__msg_body">' + nl2br(msg.body ,false) + '</p>';


    elementos += (msg.media_url0 && (msg.media_content_type0 == 'image/jpeg' || msg.media_content_type0 == 'image/gif' || msg.media_content_type0 == 'image/png')) ? '<img src="' + msg.media_url0 + '" alt="Mensaje Multimedia" class="message_image mt-4 mb-3 d-block"  style="width: 100%!important;">' : '';
    elementos += (msg.media_url0 && msg.media_content_type0 == 'application/pdf') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= ' + base_url + '"assets/images/icons/pdf-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
    elementos += (msg.media_url0 && msg.media_content_type0 == 'text/csv') ? ('<a href="' + msg.media_url0 + '" download="" target="_blank"><img src= ' + base_url + '"assets/images/icons/excel-icon.svg" alt="PDF icon" width="150px" height="150px"> </a>') : '';
    elementos += (msg.media_url0 && (msg.media_content_type0 == 'audio/amr' || msg.media_content_type0 == 'audio/mp4' || msg.media_content_type0 == 'audio/mpeg' || msg.media_content_type0 == 'audio/ogg')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-headphones" aria-hidden="true" style="margin-right:.5rem;"></i> Escuchar audio</a>') : '';
    elementos += (msg.media_url0 && (msg.media_content_type0 == 'video/3gpp' || msg.media_content_type0 == 'video/mp4')) ? ('<a href="' + msg.media_url0 + '" target="_blank"><i class="fa fa-eye " aria-hidden="true" style="margin-right:.5rem;"></i> Ver Video</a>') : '';


    elementos += '</div><div class="message-date">' + moment(msg.fecha_creacion).format('DD-MM-YYYY h:mm:ss') + '<br>';
    elementos += (msg.received == 0 && typeof (msg.nombre_apellido_operador) != 'undefined' && msg.nombre_apellido_operador != null) ? msg.nombre_apellido_operador : '';
    elementos += (msg.received == 0 && typeof (msg.nombre_apellido_operador) == 'undefined' && typeof (chat.operadores.nombre_apellido) != 'undefined') ? chat.operadores.nombre_apellido : '';
    elementos += '</div></td>';
    if (msg.sent == 1) {
        elementos += '<td class="davi-icon" id="sent-'+msg.sms_sid+'" style="padding:0px 5px 5px 0px">';
        if (msg.sms_status === 'queued' || msg.sms_status === 'sent') {
            elementos += '<img src="' + base_url + 'assets/images/icons/single-grey.svg" alt="status icon" class="__status_icon">';
        }
        if (msg.sms_status === 'delivered') {
            elementos += '<img src="' + base_url + 'assets/images/icons/double-grey.svg" alt="status icon" class="__status_icon">';
        }
        if (msg.sms_status === 'read') {
            elementos += '<img src="' + base_url + 'assets/images/icons/double-blue.svg" alt="status icon" class="__status_icon">';
        }
        if (msg.sms_status === 'failed') {
            elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
        }
        if (msg.sms_status === 'undelivered') {
            elementos += '<img src="' + base_url + 'assets/images/icons/failed.svg" alt="status icon" class="__status_icon">';
        }

        elementos += '</td>';
    }
    elementos += '</tr></tbody></table>';

    elementos += (msg.sent == 1) ? '</div>' : '';

    elementos += '</div>';

    $('.welcome#mensajes-'+id_chat).append(elementos);
    
    let element = $("#chat-panel-"+id_chat+" .main-body")[0];
    element.scrollTop = element.scrollHeight;
    if(msg.received == 1) {
        //habilito adjunto y mensaje texarea
        //$("#chat-panel-"++" a#send-messages").removeClass("disabled");
        $("#chat-panel a#adjunto").removeClass("disabled");
        $("#chat-panel #"+msg.receivedtextarea+"-mensaje").prop("disabled", false);
        $("#chat-panel a#templates").addClass("disabled");
        if($("#id_solicitud").data('tipo') == "PRIMARIA"){
            $("#id_chat-panel a.selfie").removeClass('disabled');
        }
    } 
}

function carga_boletas_pago(id_chat) {

    $(".btnSub"+id_chat).on("click", function () {
        let boton = $(this).attr('submenu');
        let metodo = $(this).attr('metodo');
        let canal = $(this).attr('canal');
        let documento = $(this).attr('documento');
        let telefono = $(this).attr('telefono');
        let seleccion = $(this).attr("seleccionado");
        let convenio=$(this).attr("codConv");
        $("#"+boton).css("background-color", "#f5f5f5");
        if($("#"+seleccion).html() == ""){
            // $(".btnSub").css("background-color", "#f1f1f1");
            // $("#"+seleccion).css("background-color", "#3EC79B");
            $("#"+seleccion).empty();
            $("#"+boton).css("background-color", "#3EC79B");
            $("#btnBotones"+id_chat).css("display", "inline-block");
            $("#btnBotones"+id_chat).css("width", "100%");
            agregarSubBtn(seleccion,boton,metodo,canal,documento,telefono,convenio, id_chat);
        }else{
            $("#"+seleccion).empty();
        }
        
    });

    $("#dropbtn-"+id_chat).on("click", function () { 
    let estado = $(this).attr('estado');
    if (estado == "oculto") {
        $(".botonesEnv").empty();
        $("#btnBotones"+id_chat).css("background-color", "#f5f5f5");
        $("#btnBotones"+id_chat).css("display", "inline-block");
        $("#envioBoleta"+id_chat).remove();
        
        $("#dropdown-content-"+id_chat).css("display", "inline-block")
        $("#dropbtn-"+id_chat).attr("estado", "visible")
    }else{
        $("#dropdown-content-"+id_chat).css("display", "none")
        $("#dropbtn-"+id_chat).attr("estado", "oculto")
    }        
});
    
    $(document).on('touchstart click',function (e){
        let btn = $("#dropbtn-"+id_chat);
        let contenido = $("#dropdown-content-"+id_chat);
        if (!btn.is(e.target) && $(e.target).closest(btn).length == 0 && !contenido.is(e.target) && $(e.target).closest(contenido).length == 0) {
            $("#dropdown-content-"+id_chat).css("display", "none");
            $("#dropbtn-"+id_chat).attr("estado", "oculto");
        }
    });

    $(".adjuntos input").on("change", function (element) {
        Swal.fire({
            title: 'Envio de archivo',
            text: 'Enviar el archivo seleccionado?',
            icon: 'warning',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            showCancelButton: 'true'
        }).then((result) => {
            if (result.value) {
                sendMessages($(this).data("channel"), $(this).data("canal"), $(this)[0].files[0]);
            }
        });
    });
    
}

function agregarSubBtn(seleccion,boton,metodo,canal,documento,telefono, convenio, id_chat) {
    $("#"+seleccion).empty();
    $('#'+seleccion).append('<a id="envioBoleta'+id_chat+'" style="display: table-cell;" onclick="enviarBoletas('+canal+','+telefono+','+documento+', '+metodo+','+2+','+convenio+')"><i class="fa fa-commenting-o" aria-hidden="true" style="color:black;"></i></a>');
    $('#'+seleccion).append('<a id="envioBoleta'+id_chat+'" style="display: table-cell;" onclick="enviarBoletas('+canal+','+telefono+','+documento+', '+metodo+','+1+','+convenio+')"><i class="fa fa-envelope-o" aria-hidden="true" style="color:black;"></i></a>');
    $('#'+seleccion).append('<a id="envioBoleta'+id_chat+'" style="display: table-cell;" onclick="enviarBoletas('+canal+','+telefono+','+documento+', '+metodo+','+0+','+convenio+')"><i class="fa fa-whatsapp" aria-hidden="true" style="color:black;"></i></a>');

}

function enviarBoletas(canal, telefono, documento, medio_pago, fuente,convenio) { 
    $.ajax({
        data: {
            "canal": canal, 
            "documento": documento, 
            "telefono": telefono, 
            "medio_pago": medio_pago,
            "fuente": fuente,
            "cod_convenio": convenio
        },
        url: base_url + "api/ApiSolicitud/data_enviar",
        type: "POST",
        success: function (respuesta) {
            response = JSON.parse(respuesta);
            if(response.status == 200 || response.status == "200"){
                Swal.fire('Se ha enviado correctamente', response.mensaje, 'success');
            }else{
                Swal.fire('No se ha realizado el envio', response.mensaje, 'warning');
            }
        }
    });
}

function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

