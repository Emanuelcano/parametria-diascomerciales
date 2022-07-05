
$(document).ready(function () {
    let btn_call_action_id = "";
    render_tabla_agenda();
var acc_10 = document.getElementsByClassName("accordion_10");
var i10;
    for (i10 = 0; i10 < acc_10.length; i10++) {
  acc_10[i10].addEventListener("click", function() {
      this.classList.toggle("active");
      
      if ($(this).hasClass('active')) {
        $('.title_button_veragenda').text('DIRECTORIO TELEFÓNICO');
      } else {
        $('.title_button_veragenda').text('VER DIRECTORIO TELEFÓNICO');

      }
    var panel_10 = this.nextElementSibling;
    if (panel_10.style.display === "block") {
      panel_10.style.display = "none";
    } else {
      panel_10.style.display = "block";
    }
  });
}
    $('body').on('click', '#tabla_agenda2 i.copy', function(element) { 
    var text = $(this)
        .parent("td")
        .text();
    copiar(text);
   });
    
    // Change estado de servicio select
    $("body").on("change", "#tabla_agenda2 select.slc_estado_telefono", function (element) {
    let numero= $(this).parents('td').data('numero');
    let id_solicitud = $("#id_solicitud").val();
    let type_contact = 183;
    let id_operador = $("section").find("#id_operador").val();
    let estado = $(this).val();
    let id = $(this).data('id');
        
    if (estado == 1) {
        comment = "<b>[AGENDA]</b><br>Se activo el numero: " + numero;         

    } else {
        comment = "<b>[AGENDA]</b><br>Fuera de servicio el numero: " + numero;
    }
    let formData = new FormData();
    formData.append('estado', estado);
    formData.append('id', id);

    swal.fire({
    title: "¿Esta seguro?",
    text: "¿Estas seguro cambiar estado?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si"
    }).then(function (result) {
        if (result.value) {
            
            $.ajax({
            url: base_url + 'api/solicitud/actualizarAgendaEstado',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            crossDomain: true,
            
        })
                .done(function (response) {
                    
                swal.fire('Exito', 'Se cambio el estado de numero telefonico', 'success');
                render_tabla_agenda();
                saveTrack(comment, type_contact, id_solicitud, id_operador);


            })
            .fail(function () {
                swal.fire('Fail','Error al cambiar estado','error');

            })
            .always(function () {
            });

        }
        
    })

    });
    
    
    // change servicio btn
    $("body").on("click", "#tabla_agenda2 span.btn_estado_servicio", function (element) {
        let id = $(this).data('id');
        let llamada = $(this).data('llamada');
        let verificado_llamada = $(this).data('verificado_llamada');
        let whatsapp = $(this).data('whatsapp');
        let verificado_whatsapp = $(this).data('verificado_whatsapp');
        let sms = $(this).data('sms');
        let verificado_sms = $(this).data('verificado_sms');
        let numero = $(this).data('numero');
        if (llamada == 1 && verificado_llamada==1) {
            $("#new-estado-servicio .modal-body").html('<div class="row"><div class="form-group col-sm-12"><button type="button" class="btn btn-danger btn-lg btn-block" onclick="guardarEstadoServicio(\'' + id + '\', \'0\',\'verificado_llamada\',\'' + numero + '\')">No Validar Servicio</button></div><div class="form-group col-sm-12"><button type="button" class="btn btn-lg btn-block"  onclick="guardarEstadoServicio(\'' + id + '\', \'0\',\'llamada\',\'' + numero + '\')">Desactivar Servicio</button></div></div>');
                
        }
        if (llamada == 1 && verificado_llamada == 0) {
            $("#new-estado-servicio .modal-body").html('<div class="row"><div class="form-group col-sm-12"><button type="button" class="btn btn-success btn-lg btn-block" onclick="guardarEstadoServicio(\'' + id + '\', \'1\',\'verificado_llamada\',\'' + numero + '\')">Validar Servicio</button></div><div class="form-group col-sm-12"><button type="button" class="btn btn-lg btn-block"  onclick="guardarEstadoServicio(\'' + id + '\', \'0\',\'llamada\',\'' + numero + '\')">Desactivar Servicio</button></div></div>');       
        }
        if (llamada == 0) {
            $("#new-estado-servicio .modal-body").html('<div class="row"><div class="form-group col-sm-12"><button type="button" class="btn btn-success btn-lg btn-block" onclick="guardarEstadoServicio(\'' + id + '\', \'1\',\'llamada\',\'' + numero + '\')">Activar Servicio</button></div></div>');       
                
        }

        if (whatsapp == 1 && verificado_whatsapp==1) {
            $("#new-estado-servicio .modal-body").html('<div class="row"><div class="form-group col-sm-12"><button type="button" class="btn btn-danger btn-lg btn-block" onclick="guardarEstadoServicio(\'' + id + '\', \'0\',\'verificado_whatsapp\',\'' + numero + '\')">No Validar Servicio</button></div><div class="form-group col-sm-12"><button type="button" class="btn btn-lg btn-block"  onclick="guardarEstadoServicio(\'' + id + '\', \'0\',\'whatsapp\',\'' + numero + '\')">Desactivar Servicio</button></div></div>');
                
        }
        if (whatsapp == 1 && verificado_whatsapp == 0) {
            $("#new-estado-servicio .modal-body").html('<div class="row"><div class="form-group col-sm-12"><button type="button" class="btn btn-success btn-lg btn-block" onclick="guardarEstadoServicio(\'' + id + '\', \'1\',\'verificado_whatsapp\',\'' + numero + '\')">Validar Servicio</button></div><div class="form-group col-sm-12"><button type="button" class="btn btn-lg btn-block"  onclick="guardarEstadoServicio(\'' + id + '\', \'0\',\'whatsapp\',\'' + numero + '\')">Desactivar Servicio</button></div></div>');       
        }
        if (whatsapp == 0) {
            $("#new-estado-servicio .modal-body").html('<div class="row"><div class="form-group col-sm-12"><button type="button" class="btn btn-success btn-lg btn-block" onclick="guardarEstadoServicio(\'' + id + '\', \'1\',\'whatsapp\',\'' + numero + '\')">Activar Servicio</button></div>');
                
        }
    
        if (sms == 1 && verificado_sms==1) {
            $("#new-estado-servicio .modal-body").html('<div class="row"><div class="form-group col-sm-12"><button type="button" class="btn btn-danger btn-lg btn-block" onclick="guardarEstadoServicio(\'' + id + '\', \'0\',\'verificado_sms\',\'' + numero + '\')">No Validar Servicio</button></div><div class="form-group col-sm-12"><button type="button" class="btn btn-lg btn-block"  onclick="guardarEstadoServicio(\'' + id + '\', \'0\',\'sms\',\'' + numero + '\')">Desactivar Servicio</button></div></div>');
                
        }
        if (sms == 1 && verificado_sms == 0) {
            $("#new-estado-servicio .modal-body").html('<div class="row"><div class="form-group col-sm-12"><button type="button" class="btn btn-success btn-lg btn-block" onclick="guardarEstadoServicio(\'' + id + '\', \'1\',\'verificado_sms\',\'' + numero + '\')">Validar Servicio</button></div><div class="form-group col-sm-12"><button type="button" class="btn btn-lg btn-block"  onclick="guardarEstadoServicio(\'' + id + '\', \'0\',\'sms\',\'' + numero + '\')">Desactivar Servicio</button></div></div>');       
        }
        if (sms == 0) {
            $("#new-estado-servicio .modal-body").html('<div class="row"><div class="form-group col-sm-12"><button type="button" class="btn btn-success btn-lg btn-block" onclick="guardarEstadoServicio(\'' + id + '\', \'1\',\'sms\',\'' + numero + '\')">Activar Servicio</button></div>');
                
        }
    
        $("#new-estado-servicio .modal-footer").html('<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>');

        $('#new-estado-servicio').modal('show');
    });
    
    //show accordeon btn action
    $("body").on("click", "tbody button.__send_template_action", function (element) {
        var bandera_8,bandera_6,bandera_4 = 1
        if ($(this).next().find('.panel_8').hasClass('active_panel'))
        {
            bandera_8 = 2
        }
        if ($(this).next().find('.panel_6').hasClass('active_panel'))
        {
            bandera_6 = 2
        }
        if ($(this).next().find('.panel_4').hasClass('active_panel'))
        {
            bandera_4 = 2
        }
        
        let canal = $('#tipo_canal').val();
        let id_solicitud = $("#id_solicitud").val();
        let tipo_template = $(this).data('tipo_template'); 
        
        let boton_action = this;
        
        $.ajax({
            url: base_url + 'atencion_cliente/makeTemplateSend/'+id_solicitud+'/'+canal+'/'+tipo_template,
            type: 'GET',
            dataType:'json'          
        })
            .done(function (response) {
                var template_view = eval(response);

                if (tipo_template == 'IVR') {
                    html = '<div class="panel_8" style="box-shadow: rgb(0, 0, 0) 0px 3px 10px -3px; white-space: none !important;">';
                    $.each(template_view['dato_string_general'], function (j, template_view) {
                        if (template_view["por_defecto"] == 1 && template_view["tipo_template"]=="IVR") {
                            html += '<div class="" style="margin: 0px;display: flex;justify-content: center;align-items: center;padding-right: 0px" role="presentation" id="div_send_tmpl">';
                            html += '<div class="col-sm-11" style="text-align: justify;padding-right: 0px;padding-top: 15px;padding-bottom: 15px;">'+template_view["msg_string"]+'</div>';
                            html += '<div class="col-sm-1" id="btn_send_tmpl" style="float: right; vertical-align:middle">';
                            html += '<button data-proveedor="' + template_view['proveedor'] + '" data-tipo_template="' + template_view['tipo_template'] + '" data-id_template="' + template_view['id'] + '" data-template_description="' + template_view["msg_string"] + '"  class="btn btn-success btn-xs template_agenda_ivr"><i class="fa fa-send"></i></button>';
                            html += '</div></div><div role="separator" class="divider_div"></div>';
                        }                      
                    }); 
                    $.each(template_view['grupo_template'], function (k, grupo_template_view) {
                        if (grupo_template_view["template"] != null && grupo_template_view["template"].length> 0) {
                            html += '<button class="accordion_7" style="border: 0.1em solid white;">' + grupo_template_view["grupo"] + '</button>';
                            html += '<div class="panel_7">';
                            $.each(grupo_template_view["template"], function (l, g_template_view) {
                                if (g_template_view["tipo_template"] == "IVR") {   
                                    html += '<div class="" style="margin: 0px;display: flex;justify-content: center;align-items: center;padding-right: 0px" role="presentation" id="div_send_tmpl">';
                                    html += '<div class="col-sm-11" style="text-align: justify;padding-right: 0px;padding-top: 15px;padding-bottom: 15px;">' + g_template_view["msg_string"] + '</div>';
                                    html += '<div class="col-sm-1" id="btn_send_tmpl" style="float: right; vertical-align:middle">';
                                    html += '<button disabled  data-proveedor="' + g_template_view["proveedor"] + '" data-tipo_template="' + g_template_view['tipo_template'] + '" data-id_template="' + g_template_view['id'] + '" data-template_description="' + g_template_view['msg_string'] + '" data-twilio_template="' + g_template_view['id_template_twilio'] + '" class="btn btn-success btn-xs template_agenda_ivr"><i class="fa fa-send" style=""></i></button>';
                                    html += '</div></div><div role="separator" class="divider_div"></div>';
                                }
                            });
                            html += '</div>';
                        }                      
                    }); 
                    html += '</div>';
                    $(boton_action).next().html(html);
                    acordeon_list_ivr();
                   
                    if (bandera_8 > 1) {
                        $(boton_action).next().find('.panel_8').removeClass('active_panel');
                    }
                    else {
                        $('.panel_8').removeClass('active_panel');
                        $(boton_action).next().find('.panel_8').addClass('active_panel');
                        $('.panel_6').removeClass('active_panel');
                        $('.panel_4').removeClass('active_panel');

                    }
                   
                }
                
                if (tipo_template == 'WAPP') {
                    html_2 = '<div class="panel_4" style="box-shadow: rgb(0, 0, 0) 0px 3px 10px -3px; white-space: none !important;">';
                    $.each(template_view['dato_string_general'], function (a, template_view) {
                        if (template_view["por_defecto"] == 1 && template_view["tipo_template"] == "WAPP") {
                            html_2 += '<div class="" style="margin: 0px;display: flex;justify-content: center;align-items: center;padding-right: 0px" role="presentation" id="div_send_tmpl">';
                            html_2 += '<div class="col-sm-11" style="text-align: justify;padding-right: 0px;padding-top: 15px;padding-bottom: 15px;">'+template_view["msg_string"]+'</div>';
                            html_2 += '<div class="col-sm-1" id="btn_send_tmpl" style="float: right; vertical-align:middle">';
                            html_2 += '<button data-proveedor="' + template_view['proveedor'] + '" data-tipo_template="' + template_view['tipo_template'] + '" data-id_template="' + template_view['id'] + '" data-template_description="' + template_view["msg_string"] + '"  class="btn btn-success btn-xs template_agenda_ws"><i class="fa fa-send"></i></button>';
                            html_2 += '</div></div><div role="separator" class="divider_div"></div>';
                        }                      
                    }); 
                    $.each(template_view['grupo_template'], function (b, grupo_template_view) {
                        if (grupo_template_view["template"] != null && grupo_template_view["template"].length>0) {
                            html_2 += '<button class="accordion_3" style="border: 0.1em solid white;">' + grupo_template_view["grupo"] + '</button>';
                            html_2 += '<div class="panel_3">';
                            $.each(grupo_template_view["template"], function (l, g_template_view) {
                                if (g_template_view["tipo_template"] == "WAPP") {   
                                    html_2 += '<div class="" style="margin: 0px;display: flex;justify-content: center;align-items: center;padding-right: 0px" role="presentation" id="div_send_tmpl">';
                                    html_2 += '<div class="col-sm-11" style="text-align: justify;padding-right: 0px;padding-top: 15px;padding-bottom: 15px;">' + g_template_view["msg_string"] + '</div>';
                                    html_2 += '<div class="col-sm-1" id="btn_send_tmpl" style="float: right; vertical-align:middle">';
                                    html_2 += '<button disabled  data-proveedor="' + g_template_view["proveedor"] + '" data-tipo_template="' + g_template_view['tipo_template'] + '" data-id_template="' + g_template_view['id'] + '" data-template_description="' + g_template_view['msg_string'] + '" data-twilio_template="' + g_template_view['id_template_twilio'] + '" class="btn btn-success btn-xs template_agenda_ws"><i class="fa fa-send" style=""></i></button>';
                                    html_2 += '</div></div><div role="separator" class="divider_div"></div>';
                                }
                            });
                            html_2 += '</div>';
                        }                      
                    }); 
                    html_2 += '</div>';
                    $(boton_action).next().html(html_2);
                    acordeon_list_ws();
                    if (bandera_4 > 1) {
                        $(boton_action).next().find('.panel_4').removeClass('active_panel');
                    }
                    else {
                        $('.panel_4').removeClass('active_panel');
                        $(boton_action).next().find('.panel_4').addClass('active_panel');
                        $('.panel_6').removeClass('active_panel');
                        $('.panel_8').removeClass('active_panel');

                    }
                }
                
                if (tipo_template == 'SMS') {
                    html_3 = '<div class="panel_6" style="box-shadow: rgb(0, 0, 0) 0px 3px 10px -3px; white-space: none !important;">';
                    $.each(template_view['dato_string_general'], function (n, template_view) {
                        if (template_view["por_defecto"] == 1 && template_view["tipo_template"] == "SMS") {
                            html_3 += '<div class="" style="margin: 0px;display: flex;justify-content: center;align-items: center;padding-right: 0px" role="presentation" id="div_send_tmpl">';
                            html_3 += '<div class="col-sm-11" style="text-align: justify;padding-right: 0px;padding-top: 15px;padding-bottom: 15px;">'+template_view["msg_string"]+'</div>';
                            html_3 += '<div class="col-sm-1" id="btn_send_tmpl" style="float: right; vertical-align:middle">';
                            html_3 += '<button data-proveedor="' + template_view['proveedor'] + '" data-tipo_template="' + template_view['tipo_template'] + '" data-id_template="' + template_view['id'] + '" data-template_description="' + template_view["msg_string"] + '"  class="btn btn-success btn-xs template_agenda_sms"><i class="fa fa-send"></i></button>';
                            html_3 += '</div></div><div role="separator" class="divider_div"></div>';
                        }                      
                    }); 
                    $.each(template_view['grupo_template'], function (m, grupo_template_view) {
                        if (grupo_template_view["template"] != null &&  grupo_template_view["template"].length > 0) {
                            html_3 += '<button class="accordion_5" style="border: 0.1em solid white;">' + grupo_template_view["grupo"] + '</button>';
                            html_3 += '<div class="panel_5">';
                            $.each(grupo_template_view["template"], function (l, g_template_view) {
                                if (g_template_view["tipo_template"] == "SMS") {   
                                    html_3 += '<div class="" style="margin: 0px;display: flex;justify-content: center;align-items: center;padding-right: 0px" role="presentation" id="div_send_tmpl">';
                                    html_3 += '<div class="col-sm-11" style="text-align: justify;padding-right: 0px;padding-top: 15px;padding-bottom: 15px;">' + g_template_view["msg_string"] + '</div>';
                                    html_3 += '<div class="col-sm-1" id="btn_send_tmpl" style="float: right; vertical-align:middle">';
                                    html_3 += '<button disabled  data-proveedor="' + g_template_view["proveedor"] + '" data-tipo_template="' + g_template_view['tipo_template'] + '" data-id_template="' + g_template_view['id'] + '" data-template_description="' + g_template_view['msg_string'] + '" data-twilio_template="' + g_template_view['id_template_twilio'] + '" class="btn btn-success btn-xs template_agenda_sms"><i class="fa fa-send" style=""></i></button>';
                                    html_3 += '</div></div><div role="separator" class="divider_div"></div>';
                                }
                            });
                            html_3 += '</div>';

                        }                      
                    });
                     html_3 += '</div>';
                    $(boton_action).next().html(html_3);
                    acordeon_list_sms();
                    if (bandera_6 > 1) {
                        $(boton_action).next().find('.panel_6').removeClass('active_panel');
                    }
                    else {
                        $('.panel_6').removeClass('active_panel');
                        $(boton_action).next().find('.panel_6').addClass('active_panel');
                        $('.panel_4').removeClass('active_panel');
                        $('.panel_8').removeClass('active_panel');

                    }
                }
            })
            .fail(function (jqXHR) {
                // console.log(jqXHR);
            })
            .always(function () {
            });
    });
    
}); // fin del ready document





function render_tabla_agenda() {
    let documento = $("#client").data('number_doc');
    var hoy = new Date();
    var hoy_moment = moment(hoy, "YYYY-MM-DD");
    
    $.ajax({
        url: base_url + 'atencion_cliente/agendaTelefonica/' + documento,
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#tabla_agenda2").html(loading);
        },
        success: function (respuesta) {
            var registros = eval(respuesta);
            html = '<a style="margin-bottom: 0.5em; float: right;" class="btn btn-success" id="agregarNumeroAgenda"><i class="fa fa-plus"></i> AGENDAR</a>';

            html += '<table class="table modificable" id="table_agenda">';
            html += '<thead><th>Número</th><th>Ciudad/Departamento</th><th>Contacto</th><th>Fuente</th><th>Tipo</th><th>Servicios</th><th>Antiguedad</th><th>Estado</th><th>Action</th><th>Editar</th></thead><tbody>';
            $.each(registros, function (j, valor) {

                let botton, fuente_tlf = "";
                let class_icono = "";
                var btn_servicio_ws, btn_servicio_sms = "";

                if (valor["departamento"] != "" && valor["departamento"] != null) {
                    botton = valor["ciudad"] + " / " + valor["departamento"];
                } else {
                    botton = '<i class="fa fa-map-marker text-red"> Agregar </i>';
                }
                
                if (valor["tipo"] == 'MOVIL') {
                    class_icono = '<i class="fa fa-mobile" aria-hidden="true"></i>';
                } else {
                    class_icono = '<i class="fa fa-phone" aria-hidden="true"></i>';
                }
                switch (valor["fuente"]) {
                    case "PERSONAL":
                        fuente_tlf = "Personal";
                        break;
                     case "PERSONAL DECLARADO":
                        fuente_tlf = "Personal Declarado";
                        break;
                     case "PERSONAL LLAMADA":
                        fuente_tlf = "Personal Llamada";
                        break;
                    case "PERSONAL WHATSAPP":
                        fuente_tlf = "Personal Whatsapp";
                        break;
                     case "PERSONAL ANTERIOR":
                        fuente_tlf = "Personal Anterior";
                        break;
                    case "REFERENCIA":
                        fuente_tlf = "Referencia";
                        break;
                    case "LABORAL":
                        fuente_tlf = "Laboral";
                        break;
                    case "BURO_CELULAR":
                        fuente_tlf = "Buro - Celular - D";
                        break;
                    case "BURO_CELULAR_T":
                        fuente_tlf = "Buro - Celular - T";
                        break;
                    case "BURO_LABORAL":
                        fuente_tlf = "Buro - Laboral - D";
                        break;
                    case "BURO_RESIDENCIAL":
                        fuente_tlf = "Buro - Residencial - D";
                        break;
                }
                if (valor["primer_reporte"] != null && valor["primer_reporte"] != "") {
                    var primer_reporte = valor["primer_reporte"]
                    var primer_reporte_moment = moment(primer_reporte, "YYYY-MM-DD");
                    antiguedad = hoy_moment.diff(primer_reporte_moment, 'months');

                } else {
                    antiguedad = 0;
                }
                if (valor["verificado_llamada"] == null) {
                    valor["verificado_llamada"] = 0;
                }
                if (valor["verificado_sms"] == null) {
                    valor["verificado_sms"] = 0;
                }
                if (valor["verificado_whatsapp"] == null) {
                    valor["verificado_whatsapp"] = 0;
                }
                if (valor["fuente"] != "PERSONAL" && valor["fuente"] != "PERSONAL WHATSAPP" &&valor["fuente"] != "PERSONAL LLAMADA" ) {
                    btn_estado_servicio = 'btn_estado_servicio';
                } else {
                    btn_estado_servicio = 'no-drop';
                }
                if (valor["llamada"] == 1 && valor["estado"] == 1 && valor["verificado_llamada"] == 1) {
                    btn_servicio_call = '<span class="'+btn_estado_servicio+'" data-numero="' + valor["numero"] + '" data-id="' + valor["id"] + '" data-llamada="' + valor["llamada"] + '" data-verificado_llamada="' + valor["verificado_llamada"] + '"><i class="fa fa-volume-control-phone texto-success"></i></span>';
                }
                if (valor["llamada"] == 1 && valor["estado"] == 1 && valor["verificado_llamada"] == 0) {
                    btn_servicio_call = '<span class="'+btn_estado_servicio+'" data-numero="' + valor["numero"] + '" data-id="' + valor["id"] + '" data-llamada="' + valor["llamada"] + '" data-verificado_llamada="' + valor["verificado_llamada"] + '"><i class="fa fa-volume-control-phone texto-warning"></i></span>';
                }
                if (valor["llamada"] == 1 && valor["estado"] == 1) {
                    if (valor['contacto'] == null || valor['contacto'] == "") {
                        btn_action_call = '<button  data-numero="' + valor['numero'] + '" data-contacto="No posee contacto" style="background:#f39c12; color:white;" class="btn btn-sm btn_call_action" id="btn_call_action_'+valor['numero']+'"><i class="fa fa-headphones" aria-hidden="true"></i></button><div class="print_acordeon"></div>';
                        btn_action_ivr = '<button style="background:#9535ab;color:white;" class="btn btn-sm __send_template_action accordion_8"  data-tipo_template="IVR">IVR</i></button><div class="print_acordeon"></div>';
                    } else {
                        btn_action_call = '<button  data-numero="' + valor['numero'] + '" data-contacto="' + valor['contacto'] + '" style="background:#f39c12; color:white;" class="btn btn-sm btn_call_action" id="btn_call_action_'+valor['numero']+'"><i class="fa fa-headphones" aria-hidden="true"></i></button><div class="print_acordeon"></div>';
                        btn_action_ivr = '<button style="background:#9535ab;color:white;" class="btn btn-sm __send_template_action accordion_8"  data-tipo_template="IVR">IVR</i></button><div class="print_acordeon"></div>';
                    }
                }
                if (valor["llamada"] == 0 || valor["estado"] == 0) {
                    btn_servicio_call = '<span class="'+btn_estado_servicio+'" data-numero="' + valor["numero"] + '" data-id="' + valor["id"] + '" data-llamada="' + valor["llamada"] + '" data-verificado_llamada="' + valor["verificado_llamada"] + '"><i class="fa fa-volume-control-phone texto-danger"></i></span>';
                    btn_action_call = '<button disabled="true" data-numero="' + valor["numero"] + '" style="background: grey; color: white;" class="btn btn-sm btn_call_action" id="btn_call_action_'+valor['numero']+'"><i class="fa fa-headphones" aria-hidden="true"></i></button>';
                    btn_action_ivr = '<button disabled="true" style="background:grey;color:white;" class="btn btn-sm">IVR</i></button>';
                }
                if (valor["whatsapp"] == 1 && valor["estado"] == 1 && valor["verificado_whatsapp"] == 1) {
                    btn_servicio_ws = '<span class="'+btn_estado_servicio+'" data-numero="' + valor["numero"] + '" data-id="' + valor["id"] + '" data-whatsapp="' + valor["whatsapp"] + '" data-verificado_whatsapp="' + valor["verificado_whatsapp"] + '"><i class="fa fa-whatsapp texto-success"></i></span>';
                }
                if (valor["whatsapp"] == 1 && valor["estado"] == 1 && valor["verificado_whatsapp"] == 0) {
                    btn_servicio_ws = '<span class="'+btn_estado_servicio+'" data-numero="' + valor["numero"] + '" data-id="' + valor["id"] + '" data-whatsapp="' + valor["whatsapp"] + '" data-verificado_whatsapp="' + valor["verificado_whatsapp"] + '"><i class="fa fa-whatsapp texto-warning"></i></span>';
                }
                if (valor["whatsapp"] == 0 || valor["estado"] == 0) {
                    btn_servicio_ws = '<span class="'+btn_estado_servicio+'" data-numero="' + valor["numero"] + '" data-id="' + valor["id"] + '" data-whatsapp="' + valor["whatsapp"] + '" data-verificado_whatsapp="' + valor["verificado_whatsapp"] + '" data-estado="' + valor["estado"] + '"><i class="fa fa-whatsapp texto-danger"></i></span>';
                    btn_action_ws = '<button disabled="true" style="background:grey;color:white;" class="btn btn-sm"><i class="fa fa-whatsapp" aria-hidden="true"></i></button>';
                }
                if (valor["whatsapp"] == 1 && valor["estado"] == 1) {
                    btn_action_ws = '<button style="background: green; color: white;" class="btn btn-sm __send_template_action accordion_4"  data-tipo_template="WAPP"><i class="fa fa-whatsapp" aria-hidden="true"></i></button><div class="print_acordeon"></div>';
                }
                if (valor["sms"] == 1 && valor["estado"] == 1 && valor["verificado_sms"] == 1) {
                    btn_servicio_sms = '<span class="'+btn_estado_servicio+'" data-numero="' + valor["numero"] + '" data-id="' + valor["id"] + '" data-sms="' + valor["sms"] + '" data-verificado_sms="' + valor["verificado_sms"] + '"><i class="fa fa-comments texto-success"></i></span>';
                }
                if (valor["sms"] == 1 && valor["estado"] == 1 && valor["verificado_sms"] == 0) {
                    btn_servicio_sms = '<span class="'+btn_estado_servicio+'" data-numero="' + valor["numero"] + '" data-id="' + valor["id"] + '" data-sms="' + valor["sms"] + '" data-verificado_sms="' + valor["verificado_sms"] + '"><i class="fa fa-comments texto-warning"></i></span>';
                }
                if (valor["sms"] == 0 || valor["estado"] == 0) {
                    btn_servicio_sms = '<span class="'+btn_estado_servicio+'" data-numero="' + valor["numero"] + '" data-id="' + valor["id"] + '" data-sms="' + valor["sms"] + '" data-verificado_sms="' + valor["verificado_sms"] + '" data-estado="' + valor["estado"] + '"><i class="fa fa-comments texto-danger"></i></span>';
                    btn_action_sms = '<button disabled="true" style="background:grey;color:white;" class="btn btn-sm"><i class="fa fa-comments" aria-hidden="true"></i></button>';
                }
                if (valor["sms"] == 1 && valor["estado"] == 1) {
                    btn_action_sms = '<button style="background:#00c0ef ;color:white;" class="btn btn-sm __send_template_action accordion_6"  data-tipo_template="SMS"><i class="fa fa-comments" aria-hidden="true"></i></button><div class="print_acordeon"></div>';
                }
                if (valor["estado"] == 0) {
                    slc_activo = '';
                    slct_inactivo = 'selected';
                } else {
                    slc_activo = 'selected';
                    slct_inactivo = '';
                }

                let operador_tipo = $("#hdd_tipo_operador").val();
                let perfiles = [1,5,7,8,6,14,18];
                var permitido = perfiles.find(perfil => perfil == operador_tipo);      
                
                let btn_audios = "";
                if(permitido == undefined){
                    btn_audios = `<button 
                    class="btn btn-sm  dt-control" 
                    id="${valor['id']}"
                    style="background:#ba0000;color:white;"
                    data-telefono="${valor['numero']}"
                    title="Escuchar llamadas"
                    onclick="mostrarLlamados(${valor['numero']})">
                        <i class="fa fa-microphone" aria-hidden="true"></i>
                    
                    </button>`;
                }
          
                if ( ((valor["fuente"] == 'PERSONAL DECLARADO') 
                || (valor["fuente"] == 'PERSONAL')
                || (valor["fuente"] == 'REFERENCIA')
                )
                && (permitido == undefined)) {

                    btn_audios = `<button 
                                    class="btn btn-sm  dt-control" 
                                    id="${valor['id']}"
                                    style="background:#ba0000;color:white;"
                                    data-telefono="${valor['numero']}"
                                    title="Escuchar llamadas"
                                    onclick="mostrarLlamados(${valor['numero']})">
                                        <i class="fa fa-microphone" aria-hidden="true"></i>
                                    
                                    </button>`;
                } 
                if (valor["fuente"] != 'PERSONAL DECLARADO') {
                    btn_edit = '<button class="btn btn-sm  btn_edit_num" style="background:#0073b7;color:white;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
                } else {
                    btn_edit = '<button disabled="true" class="btn btn-sm  btn_edit_num" style="background:gris;color:white;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
                }
               
                html += "<tr>";
                html += '<td class="numero" data-id="'+valor["id"]+'">' + valor['numero'] + '<i class="fa fa-clone pull-right copy" style="color: red; " title="Copiar Numero de telefono">&nbsp;&nbsp;</i></td>';
                html += '<td><a class="codigo" style="font-size: 12px;" data-registro=' + valor["id"] + ' data-numero=' + valor["numero"] + ' onclick= "agregarLocalidadModal_New(this)">' + botton + '</a></td>';
                html += '<td><div class="form-group" style="margin-bottom: 0px;"><span>' + valor["contacto"] + '</span></div></td>';
                html += '<td>' + fuente_tlf + '</td>';
                html += '<td style="text-align: center; font-size: 2em;">' + class_icono + '</td>';
                html += '<td style="text-align: center; font-size: 1.5em;">' + btn_servicio_call + " " + btn_servicio_ws + " " + btn_servicio_sms +'</td>';
                html += '<td style="text-align: center;">' + antiguedad + '</td>';
                html += '<td data-numero="'+valor['numero']+'"> <select class="form-control selectpicker slc_estado_telefono" data-id="' + valor["id"] + '" data-documento="' + valor["documento"] + '" id="slc_estado_telefono"><option value="1" ' + slc_activo + '>Activo</option><option value="0" ' + slct_inactivo + '>Fuera de servicio</option></select></td>';
                html += '<td style="display:flex" data-numero_ws="' + valor['numero'] + '" data-numero="+57' + valor['numero'] + '" data-llamada="' + valor["llamada"] + '" data-whatsapp="' + valor["whatsapp"] + '" data-estado="' + valor["estado"] + '" data-sms="' + valor["sms"] + '">' + btn_action_call + btn_action_ivr + btn_action_ws + btn_action_sms + btn_audios + '</td>';
                html += '<td data-id="'+valor["id"]+'">'+btn_edit+'</td>'
                html += '</tr>';
            });
            html += '</tbody>';
            $('#tabla_agenda2').html(html);
            TablaPaginada("table_agenda", 0, "asc", "", "");
               
        }
    });
}


function mostrarLlamados(telefono) {

    $('#audios_visado').remove();
    var params = {
        "telefono" : telefono,
       "central" : (typeof sessionStorage.switch_valor == 'undefined') ? '0' : sessionStorage.switch_valor
    }
    console.log(params)
    $.ajax({
        url: base_url + 'getInfoAudio',
        data:params,
        type:'POST',
        dataType:'JSON',
        success:function(response){
            
            if( response.ok == false){
                swal('Número telefonico sin registros de llamada', '', 'error').then(function(){
                    $('#audios_escuchar').css('display', 'none');
                });
            } else {
                
                let audios = JSON.parse(response.resp);
                
                let contenedor = `<div class="" width="auto">
                                    <div id="audios_visado" style="overflow-y: scroll;max-height: 300px">
                                                            
                                        <div class="col-md-12" style="background-color: #e0dff5 ;box-shadow: 0px 9px 10px -9px #888888; z-index: 1;height: 44px;padding-top: 0px; border-top: 3px solid #00c0ef;width: 100%;">
                                            <h4 class="col-md-10" style=";">Audios correspondientes al nro telefónico ${telefono}</h4>
                                            <button onclick="closeAudios()" style="margin-left: 14%; border:none;font-size:18px;background:#dad7f9;border-radius:40%;margin-top: 5px;">
                                                <i class="fa  fa-times" style="color:#ba0000;" aria-hidden="true"></i>
                                            
                                            </button>
                                        </div>
                                    </div>
                                    <div id="reportar_audio">
                                    
                                    </div>
                                </div>`;
                
            
                const even = (audio) => {
                    if(audio.reportado == true) { return true; };
                };

                if(audios.every(even) == false) {
                    $('#audios_escuchar').css('display', 'block');   
                    $('#audios_escuchar').html(contenedor);

                    audios.forEach( function(audio){
                        // console.log(audio.fecha_audio);
                        // console.log(fecha_audio);
                        // audio.fecha_audio > '2021-12-29 00:00:00' &&
                        if ( audio.reportado != true) {
                            let id_track = audio.id_track;
                            // let fecha_audio = audio.fecha_audio;
                            var fecha_audio = moment(audio.fecha_audio).subtract(2, 'hours').format('DD/MM/YYYY HH:mm:ss');
                            var html =  `<div class="col-md-3"  id="audios_escuchar" style="margin-top:1%;">
                                            <div class="col-md-12" style="">
                                                <div class="col-md-4" >Fecha llamado</div>
                                                <div class="col-md-6">${fecha_audio}</div>
                                            </div>
                                            <audio class="col-md-11" id="audio_${audio.id_track}"  src="${audio.path_audio}" controls="true"></audio>
                                            <button 
                                                    id="reportar_audio" 
                                                    class="col-xs-1 btn btn-xs bg-light" 
                                                    style="font-size: 18px;padding:0!important;height:30px;background-color: #dfdfdf;color:#bb0a09; margin-top: 3%;"
                                                    onclick="reportarAudio(this);"
                                                    data-telefono="${audio.numero_solicitud}"
                                                    data-id_track="${id_track}"
                                                    data-fecha_audio="${fecha_audio}"
                                                    title="Reporatar problema con el audio.">
                                                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                                </button>
                                        </div>`;
                            $('#audios_visado').append(html);
                        }
                       
                    })
                } else {
                    swal('Número telefonico sin registros de llamada', '', 'error').then(function(){
                        $('#audios_escuchar').css('display', 'none');
                    });
                }

                
                
            }
        }
    })
    
}



function closeAudios(){
    $('#audios_visado').remove();
    $('#audios_escuchar').css('display', 'none');  
}

function reportarAudio(element) {

    $('#reportarAudioModal').css('display', 'block');
    // data fecha_audio
    // data id_track
    if ($('#reporteForm')) {
        $('#reporteForm').remove();
    }
    let id_track = $(element).data('id_track');
    let fecha_audio = $(element).data('fecha_audio');
    let telefono = $(element).data('telefono');
    
    

    let reporte = `<div class="col-md-3" id="reporteForm" style="background-color: #fff; margin-top: 14%; margin-left: 36%; height: 230px; padding-top: 1%; width: 431px;border-radius:2%;">

                    <h4 style="text-align: center;">Seleccione el motivo por el que se reporta el audio</h4>
                    <div class="col-md-12" style=" z-index: 1;height: 44px;padding-top: 0px; border-top: 3px solid #00c0ef;    padding-left: 10%;">
                        <h6 class="col-md-6" >Reportando audio de fecha:</h6>
                        <h6 class="col-md-6">${fecha_audio}</h6>
                    </div>
                    
                    <select id="tipo_incidente" class="col-md-10" style="margin-top:2%;margin-left:8%;height:30px;margin-bottom:2%">
                        <option value="1">Seleccione</option>
                        <option value="Audio vacío">Atiende contestador</option>
                        <option value="No se puede reprodcir audio">No se puede reprodcir audio</option>
                    </select>

                    <button 
                        id="reporteAudio" 
                        class="btn btn-primary bg-light col-md-5" 
                        style="font-size: 18px;padding:0!important;height:30px;margin-left:7%; margin-top: 3%;"
                        onclick="enviarReporte(this);"
                        data-id_track="${id_track}"
                        data-fecha_audio="${fecha_audio}"
                        data-telefono="${telefono}"
                        title="Reporatar problema con el audio.">
                            Reportar audio
                    </button>
                    <button 
                        id="cancelar" 
                        class="btn btn-warning bg-light col-md-5" 
                        style="font-size: 18px;padding:0!important;height:30px;margin-left:2%; margin-top: 3%;"
                        onclick=""
                        title="Reporatar problema con el audio." data-dismiss="modal">
                            Cancelar
                    </button>
                </div>`;
            $('#reportarAudioModal').append(reporte);
            $('#reportarAudioModal').modal('show');
            $('#reporteAudio').attr('disabled', true);

            $('#tipo_incidente').on('change', function () {
                if($('#tipo_incidente').val() != '1'){
                    $('#reporteAudio').attr('disabled', false);
                }else {
                    $('#reporteAudio').attr('disabled', true);
                }
                
            })
}


function enviarReporte(element) {
    
    let selection = document.getElementById("tipo_incidente");
    // selection.options[selection.selectedIndex].value;
    let operador = document.getElementById('hdd_id_operador').value;
    
    let telefono = $(element).data('telefono');

    $.ajax({
        url:   base_url +'audio_reportar',
        type:  'POST',
        data:  {
            id_track: $(element).data('id_track'),
            fecha_audio: $(element).data('fecha_audio'),
            tipo_incidente: selection.options[selection.selectedIndex].value,
            operador: operador
        },
        success: function(response) {
            
            if (response.status.code == 200) {
                Swal.fire(
                    'Reportado!',
                    'El audio ha sido reportado.',
                    'success'
                )
                $('#reporteForm').remove();
                $('#reportarAudioModal').css('display', 'none');
                $('.modal-backdrop').css('display', 'none');
                mostrarLlamados(telefono);
                
            }
        }
    })

}

function acordeon_list_ivr() {
    /**********       IVR          *******/
    var acc7 = $("body tbody button.accordion_7");
    var h7;
    for (h7 = 0; h7 < acc7.length; h7++) {
        acc7[h7].addEventListener("click", function () {
            this.classList.toggle("active");
            var panel_7 = this.nextElementSibling;
            
            if ($(panel_7).css('display') === "block") {
                $(panel_7).hide();
            } else {
                $(panel_7).show();
            }
        });
    }
    
        
}
function acordeon_list_ws() {
    /**********       WAPP          *******/
    var acc3 = $("body tbody button.accordion_3");
    for (var h3 = 0; h3 < acc3.length; h3++) {
        acc3[h3].addEventListener("click", function () {
            this.classList.toggle("active");
            var panel_3 = this.nextElementSibling;
            
            if ($(panel_3).css('display') === "block") {
                $(panel_3).hide();
            } else {
                $(panel_3).show();
            }
        });
    }
    
}
function acordeon_list_sms() {
    var h5
    /**********       SMS          *******/
    var acc5 = $("body tbody button.accordion_5");
    for (h5 = 0; h5 < acc5.length; h5++) {
        acc5[h5].addEventListener("click", function () {
            this.classList.toggle("active");
            var panel_5 = this.nextElementSibling;
            
            if ($(panel_5).css('display') === "block") {
                $(panel_5).hide();
            } else {
                $(panel_5).show();
            }
        });
    }
    
}
function TablaPaginada(
	nombreTabla,
	colOrdenar,
	fOrdenar,
	colOrdenar2 = "",
	fOrdenar2 = "",
	ajax = null,
	columns = null,
	columnDefs = null,
	options_dt = null,
	createdRow = null,
	pageLength = null,
	footerCallback = null,
	extras = null
	
) {
	var tabla = "#" + nombreTabla;
	var columnaOrdenar = colOrdenar;
	var formaOrdenar = fOrdenar;

	if (colOrdenar2 == "") {
		var columnaOrdenar2 = colOrdenar;
		var formaOrdenar2 = fOrdenar;
	} else {
		var columnaOrdenar2 = colOrdenar2;
		var formaOrdenar2 = fOrdenar2;
	}
	
	//alert(columnaOrdenar2+formaOrdenar2)

	let options = {
		
		lengthMenu: [
			[5, 10, 15, 25, 50],
			[5, 10, 15, 25, 50],
			
		],
	
		//"aaSorting": [[columnaOrdenar,formaOrdenar], [columnaOrdenar2,formaOrdenar2]],
		order: [],
		language: {
			
			sProcessing: "Procesando...",
			sLengthMenu: "Mostrar _MENU_ registros",
			sZeroRecords: "No se encontraron resultados",
			sEmptyTable: "Ningún dato disponible en esta tabla",
			sInfo: "Del _START_ al _END_ de un total de _TOTAL_ reg.",
			sInfoEmpty: "0 registros",
			sInfoFiltered: "(filtrado de _MAX_ reg.)",
			sInfoPostFix: "",
			sSearch: "Buscar:",
			sUrl: "",
			sInfoThousands: ",",
			sLoadingRecords: "Cargando...",
			oPaginate: {
				sFirst: "Primero",
				sLast: "Último",
				sNext: "Sig",
				sPrevious: "Ant"
			},
			oAria: {
				sSortAscending:
					": Activar para ordenar la columna de manera ascendente",
				sSortDescending:
					": Activar para ordenar la columna de manera descendente"
			}
		}
	};
	if (ajax !== null) {
		options.ajax = ajax;
	}
	if (columns !== null) {
		options.columns = columns;
	}
	if (columnDefs !== null) {
		options.columnDefs = columnDefs;
	}

	if (options_dt !== null) {
		options.order = options_dt.order;
		options.createdRow = options_dt.createdRow;
	}
	if (createdRow !== null) {
		options.createdRow = createdRow;
	}

	if(pageLength !== null){
		options.displayLength = pageLength;
	}

	if(footerCallback !== null){
		options.footerCallback = footerCallback;
	}
	if(extras !== null){
		$.each(extras, function(i,el){
			options[i] = el
		})
	}

	$(tabla).DataTable(options);
}
 $("select#new-fuente-new").change(function (event) {
     if ($(this).find(':selected').val() == 'PERSONAL' || $(this).find(':selected').val() == 'PERSONAL WHATSAPP' || $(this).find(':selected').val() == 'PERSONAL LLAMADA') {
         $('#llamada-new').attr('disabled', true);
         $('#sms-new').attr('disabled', true);
         $('#wts-new').attr('disabled', true);
     } else {
        $('#llamada-new').attr('disabled', false);
         $('#sms-new').attr('disabled', false);
         $('#wts-new').attr('disabled', false);
     }
     if ($(this).find(':selected').val() == 'PERSONAL') {
        $('#llamada-new').val(1);
         $('#sms-new').val(1);
         $('#wts-new').val(1);
     }
     if ($(this).find(':selected').val() == 'PERSONAL WHATSAPP') {
        $('#llamada-new').val(0);
         $('#sms-new').val(0);
         $('#wts-new').val(1);
     }
     if ($(this).find(':selected').val() == 'PERSONAL LLAMADA') {
        $('#llamada-new').val(1);

         $('#sms-new').val(1);

         $('#wts-new').val(0);
     }
     
     if ($(this).find(':selected').val() == 'REFERENCIA') {
         $('#div-parentesco-new').show();
     } else {
         $('#div-parentesco-new').hide()
     }

 });

$("select#new-tipo-new").change(function (event) {
    if ($("select#new-tipo-new").find(':selected').val() == 'FIJO') {
        $('#div-departamentos-new').show();
        $('#div-ciudad-new').show();

    } else {
        $('#div-departamentos-new').hide();
        $('#div-ciudad-new').hide();
        $('#departamentos-new').val('');
        $('#ciudad-new').val('');
    }
});
function copiar(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(element).select();
    document.execCommand("copy");
    $temp.remove();
}

function getCookie(cname) {
    let name = cname + "=",
        decodedCookie = decodeURIComponent(document.cookie),
        ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

$("select#departamentos-new").change(function (event){
    $.ajax({
        url: base_url+'api/credito/get_municipios/'+this.value,
        type: 'GET'
        })
        .done(function(response) {
            
            if(typeof(response.municipios) != 'undefined'){
                let municipios = response.municipios;
                $("select#ciudad-new").html('');
                municipios.forEach(function(municipio, indice, array){
                    $("select#ciudad-new").append('<option value="'+municipio.nombre_municipio+'" >'+municipio.nombre_municipio+'</option>')
                });
                $("select#ciudad-new").attr('disabled', false);
            }
        })
        .fail(function() {
        })
        .always(function() {
        });
});

$("select#departamentos-modal-new").change(function (event){
    $.ajax({
        url: base_url+'api/credito/get_municipios/'+this.value,
        type: 'GET'
        })
        .done(function(response) {
            
            if(typeof(response.municipios) != 'undefined'){
                let municipios = response.municipios;
                $("select#ciudad-modal-new").html('');
                municipios.forEach(function(municipio, indice, array){
                    $("select#ciudad-modal-new").append('<option value="'+municipio.nombre_municipio+'" >'+municipio.nombre_municipio+'</option>')
                });
                $("select#ciudad-modal-new").attr('disabled', false);
            }
        })
        .fail(function() {
        })
        .always(function() {
        });
});


function guardarEstadoServicio(id,valor,variable,numero) {
    let id_solicitud = $("#id_solicitud").val();
    let formData = new FormData();
    let type_contact = 183;
    let id_operador = $("section").find("#id_operador").val();
    let comment = '';

    formData.append('id', id);
    formData.append('valor', valor);
    formData.append('variable', variable);

    if (variable == 'llamada') {
        if (valor == 0 || valor == '0') {
            comment = '<b>[AGENDA]</b><br>DESACTIVO servicio LLAMADA del <b>TELEFONO:</b> ' + numero;
        } else {
            comment = '<b>[AGENDA]</b><br>ACTIVO servicio LLAMADA del <b>TELEFONO:</b> ' + numero;
        }
    }
    if (variable == 'verificado_llamada') {
        if (valor == 0 || valor == '0') {
            comment = '<b>[AGENDA]</b><br>DESVALIDO servicio LLAMADA del <b>TELEFONO:</b> ' + numero;
        } else {
            comment = '<b>[AGENDA]</b><br>VALIDO servicio LLAMADA del <b>TELEFONO:</b> ' + numero;
        }
    }
    
    if (variable == 'whatsapp') {
        if (valor == 0 || valor == '0') {
            comment = '<b>[AGENDA]</b><br>DESACTIVO servicio WHATSAPP del <b>TELEFONO:</b> ' + numero;
        } else {
            comment = '<b>[AGENDA]</b><br>ACTIVO servicio WHATSAPP del <b>TELEFONO:</b> ' + numero;
        }
    }
    if (variable == 'verificado_whatsapp') {
        if (valor == 0 || valor == '0') {
            comment = '<b>[AGENDA]</b><br>DESVALIDO servicio WHATSAPP del <b>TELEFONO:</b> ' + numero;
        } else {
            comment = '<b>[AGENDA]</b><br>VALIDO servicio WHATSAPP del <b>TELEFONO:</b> ' + numero;
        }
    }
    
    if (variable == 'sms') {
        if (valor == 0 || valor == '0') {
            comment = '<b>[AGENDA]</b><br>DESACTIVO servicio SMS del <b>TELEFONO:</b> ' + numero;
        } else {
            comment = '<b>[AGENDA]</b><br>ACTIVO servicio SMS del <b>TELEFONO:</b> ' + numero;
        }
    }
    if (variable == 'verificado_sms') {
        if (valor == 0 || valor == '0') {
            comment = '<b>[AGENDA]</b><br>DESVALIDO servicio SMS del <b>TELEFONO:</b> ' + numero;
        } else {
            comment = '<b>[AGENDA]</b><br>ºVALIDO servicio SMS del <b>TELEFONO:</b> ' + numero;
        }
    }

    swal.fire({
        title: "¿Esta seguro?",
        text: "¿Estas seguro de cambiar Estado Servicio?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si"
    }).then(function (result) {
        if (result.value) {
            
            $.ajax({
            url: base_url + 'api/solicitud/CambioEstadoServicioAgenda',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            crossDomain: true
            
        })
                .done(function (response) {
                    $('#new-estado-servicio').modal('hide');
                    $('.modal-backdrop').hide();
                    swal.fire('Exito', 'Se actualizo estado del servicio del numero: ' + numero, 'success');
                    render_tabla_agenda();
                    saveTrack(comment, type_contact, id_solicitud, id_operador);
            })
            .fail(function () {
                swal.fire('Fail','No se actualizo estado del servicio del numero: '+ numero,'error');

            })
            .always(function () {
            });

        }
        
    }) 
}

function agregarLocalidadModal_New(element) {
    $("#new-localidad-modal .modal-footer").html('<button type="button" class="btn btn-default" data-dismiss="modal" onclick="$(\'#departamentos-modal\').html(\'\')">Cancelar</button><button type="button" class="btn btn-success" data-dismiss="modal" onclick="guardarCambioNew(\'' + $(element).data('registro')+ '\', \'tel\',\'' + $(element).data('numero')+ '\')">Aceptar</button>');
    $.ajax({
        url: base_url + 'api/credito/get_departamentos',
        type: 'GET'
    })
        .done(function (response) {
            $('#new-localidad-modal').modal('show');
            $("select#departamentos-modal-new").html('');
            if (typeof (response.departamentos) != 'undefined') {
                let departamentos = response.departamentos;
                departamentos.forEach(function (departamento, indice, array) {
                    $("select#departamentos-modal-new").append('<option data-id_departamento="' + departamento.nombre_departamento + '" value="' + departamento.Codigo + '" >' + departamento.nombre_departamento + '</option>')
                });
            }

        })
        .fail(function () {
        })
        .always(function () {
        });
}
function guardarCambioNew(id, agenda, numero) {
    const formData = new FormData();
    let comment = "<b>[AGENDA]</b><br>Se actualizo la Ciudad y Departamento del numero: " + numero;
    let type_contact = 183;
    let id_solicitud = $("#id_solicitud").val();
    let id_operador = $("section").find("#id_operador").val();

    if (agenda == 'tel') {
        formData.append("id", id);
        if ($("select#departamentos-modal-new").val() != null) {
            formData.append("departamento", $("select#departamentos-modal-new").find(':selected').text());
            formData.append("ciudad", $("select#ciudad-modal-new").find(':selected').text());
            $("select#departamentos-modal-new").html('');
        }
    }
    formData.append("agenda", agenda);
    Swal.fire({
        title: '¡Atención!',
        text: '¿Estas seguro de que quieres modificar la agenda?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {

            $.ajax({
                url: base_url + 'api/solicitud/actualizarAgendaLocalidad',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {
                if (response.status == '200') {
                    saveTrack(comment, type_contact, id_solicitud, id_operador);
                    Swal.fire("¡Perfecto!", response.message,'success');
                    if (response.departamento != "" && response.codigo != "") {
                        render_tabla_agenda();
                    }
                } else {
                    Swal.fire("¡Ups!",response.message.contacto,'error');
                }

            })
                .fail(function (response) {
                    //console.log(response);
                })
                .always(function (response) {
                    //console.log("complete");
                });

        }
    });

}

function guardarCambioTlf(id,documento) {
    const formData = new FormData();
    var agenda = "tel";
    if (agenda == 'tel') {
        formData.append("id", id);
        formData.append("fuente", $("#fuente-tel-" + id).val());
        formData.append("contacto", $("#contacto-tel-" + id).val());
        formData.append("id_parentesco", $("#parentesco-tel-" + id).val());
        formData.append("estado", $("#estado-tel-" + id).val());
        formData.append("tipo", $("#tipo-tel-" + id).val());
        if ($("select#departamentos-modal").val() != null) {
            formData.append("departamento", $("select#departamentos-modal").find(':selected').data('id_departamento'));
            formData.append("ciudad", $("select#ciudad-modal").val());
            $("select#departamentos-modal").html('');
        }
    }
   
    formData.append("agenda", agenda);
    Swal.fire({
        title: '¡Atención!',
        text: '¿Estas seguro de que quieres modificar la agenda?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {

            $.ajax({
                url: base_url + 'api/credito/actualizarAgendaSolicitudes',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {
                if (response.status == '200') {
                    Swal.fire({
                        title: "¡Perfecto!",
                        text: response.message,
                        icon: 'success'
                    });
                    $('#table-agenda-telefono').DataTable().clear();
                    $('#table-agenda-telefono').DataTable().destroy();
                    render_tabla_agendaTlf(documento)
                    if (response.departamento != "" && response.codigo != "") {
                        let cadena = '<a data-registro="' + id + '" class="codigo" style="font-size: 12px;" onclick= "agregarLocalidadModal(this)">' + response.codigo + '/' + response.departamento + '</a><a data-title="Verificar código" data-registro="' + id + '" class="verificacion"><i class="fa fa-check-circle text-green" ></i></a>'
                        $("a[data-registro=" + id + "]").closest('td').html(cadena);

                        $("a.verificacion").click(function (event) {
                            verificarCodigo($(this));
                        });
                    }
                } else {
                    Swal.fire({
                        title: "¡Ups!",
                        text: response.message.contacto,
                        icon: 'error'
                    });
                }

            })
                .fail(function (response) {
                    //console.log(response);
                })
                .always(function (response) {
                    //console.log("complete");
                });

        }
    });

}
function guardarCambioMail(id,documento) {
    const formData = new FormData();
    var agenda = "mail";
   
    if (agenda == 'mail') {
        formData.append("contacto", $("#contacto-agen-" + id).val());
        formData.append("fuente", $("#fuente-mail-" + id).val());
        formData.append("estado", $("#estado-mail-" + id).val());
        formData.append("id", id);
    }
    formData.append("agenda", agenda);
    Swal.fire({
        title: '¡Atención!',
        text: '¿Estas seguro de que quieres modificar la agenda?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {

            $.ajax({
                url: base_url + 'api/credito/actualizarAgendaSolicitudes',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {
                if (response.status == '200') {
                    Swal.fire({
                        title: "¡Perfecto!",
                        text: response.message,
                        icon: 'success'
                    });
                    $('#table_agenda_mail').DataTable().clear();
                    $('#table_agenda_mail').DataTable().destroy();
                    render_tabla_agenda_mail(documento)
                   
                } else {
                    Swal.fire({
                        title: "¡Ups!",
                        text: response.message.contacto,
                        icon: 'error'
                    });
                }

            })
                .fail(function (response) {
                    //console.log(response);
                })
                .always(function (response) {
                    //console.log("complete");
                });

        }
    });

}
/********    Button edit num call modal      *****/
$("body").on("click", "#tabla_agenda2 button.btn_edit_num", function (element) {
    let id = $(this).parents('td').data('id');
    $("#new-create-agenda #update_tlf_new").data('id', id);
  
    $.ajax({
        url: base_url + 'api/solicitud/get_update_numero/'+id,
        type: 'GET'
    })
        .done(function (response) {
            let valor = response.data[0];
            if (valor["verificado_llamada"] == null) {
                valor["verificado_llamada"] = 0;
            }
            if (valor["verificado_sms"] == null) {
                valor["verificado_sms"] = 0;
            }
            if (valor["verificado_whatsapp"] == null) {
                valor["verificado_whatsapp"] = 0;
            }
            $("#new-create-agenda #new-titulo").html('ACTUALIZAR NÚMERO');
            $("#new-create-agenda #agendar_tlf_new").hide();
            $("#new-create-agenda #update_tlf_new").show();
            $("#new-create-agenda #new-numero-new").val(valor["numero"]);
            $("#new-create-agenda #new-contacto-new").val(valor["contacto"]);
            $("#new-create-agenda #new-fuente-new").val(valor["fuente"]);
            $("#new-create-agenda #new-tipo-new").val(valor["tipo"]);
            $("#new-create-agenda #departamentos-new").val(valor["departamento"]);
            $("#new-create-agenda #ciudad-new").val(valor["ciudad"]);
            $("#new-create-agenda #new-estado-new").val(valor["estado"]);
            $("#new-create-agenda #parentesco-new").val(valor["id_parentesco"]);
            $("#new-create-agenda #ciudad-new").val(valor["ciudad"]);
            $("#new-create-agenda #llamada-verificada-new").val(valor["verificado_llamada"]);
            $("#new-create-agenda #sms-verificada-new").val(valor["verificado_sms"]);
            $("#new-create-agenda #wts-verificada-new").val(valor["verificado_whatsapp"]);
            $("#new-create-agenda #llamada-new").val(valor["llamada"]);
            $("#new-create-agenda #sms-new").val(valor["sms"]);
            $("#new-create-agenda #wts-new").val(valor["whatsapp"]);
            if ($("select#new-tipo-new").find(':selected').val() == 'FIJO') {
                $('#div-departamentos-new').show();
                $('#div-ciudad-new').show();

            } else {
                $('#div-departamentos-new').hide();
                $('#div-ciudad-new').hide();
                $('#departamentos-new').val('');
                $('#ciudad-new').val('');
            }
            $('#new-create-agenda').modal('show');

        })
        .fail(function () {
        })
        .always(function () {
        });
    
});

/********    Button edit update num     *****/
$("body").on("click", "#new-create-agenda #update_tlf_new", function (element) {
    let id = $(this).data('id');
    formData = new FormData();
    formData.append("id", id);
    formData.append("numero", $("#new-numero-new").val());
    formData.append("fuente", $("#new-fuente-new").val());
    formData.append("contacto", $("#new-contacto-new").val());
    formData.append("estado", $("#new-estado-new").val());
    formData.append("tipo", $("#new-tipo-new").val());
    formData.append("departamento", $("select#departamentos-new").find(':selected').text());
    formData.append("ciudad", $("select#ciudad-new").find(':selected').text());
    formData.append("id_parentesco", $("#parentesco-new").val());
    formData.append("verificado_llamada", $("#llamada-verificada-new").val());
    formData.append("verificado_sms", $("#sms-verificada-new").val());
    formData.append("verificado_whatsapp", $("#wts-verificada-new").val());
    formData.append("llamada", $("#llamada-new").val());
    formData.append("sms", $("#sms-new").val());
    formData.append("whatsapp", $("#wts-new").val());
    Swal.fire({
        title: '¡Atención!',
        text: '¿Estas seguro de que quieres modificar los datos del contacto de la agenda?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {

            $.ajax({
                url: base_url + 'api/solicitud/actualizarEditAgenda',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            }).done(function (response) {
                $("#new-create-agenda").modal("hide");
                render_tabla_agenda();
                if (response.status == 200) {
                    swal.fire('Exito', response.message, 'success')
                } else {
                 swal.fire('Upps', response.message, 'error')
                }
            })
                .fail(function (response) {
                })
                .always(function (response) {
                });

        }
    });

});

/********    Button llamada action agenda      *****/
$("body").on("click", "#tabla_agenda2 button.btn_call_action", function (element) {
    event.preventDefault();
    // sessionStorage.llamada_saliente=0;
     btn_call_action_id = $(this).attr('id');
    if (sessionStorage.switch_valor == "activo_wolkvox") {
        var cabecera = "99";
    }else if (sessionStorage.switch_valor == "activo_neotell") {

        cabecera ="57";
    }else if (sessionStorage.switch_valor == "activo_twilio") {
        
        cabecera ="+57";
    }else{
        cabecera ="";
    }
if (typeof sessionStorage.switch_valor != "undefined" && sessionStorage.switch_valor != "activo_ninguno") {

        if (sessionStorage.llamada_saliente==1)
        {
            $("#btn_hang").trigger("click");
             $("#"+btn_call_action_id).css({'background-color': '#f39c12', 'color': 'white'});
            sessionStorage.llamada_saliente = 0;
            toastr["error"]("Colgando llamada", "Colgado");
        }else{


            var txtTelefono = cabecera + $(this).data("numero");
            var txtContacto=$(this).data("contacto");
            
            var id_customer = "12700";

            if (txtTelefono == "") {
                Swal.fire("Verifique!", "Debe indicar un numero telefonico para realizar la llamada", "error");
            } else {
                swal.fire({
                    title: "¿Esta seguro?",
                    text: "¿Estas seguro de Llamar al numero: "+ $(this).data("numero") +" perteneciente a contacto: " +txtContacto+"?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Si, LLamar"
                }).then(function (result) {
                    if (result.value) {
                        $("#txt_num_man").val(txtTelefono);
                        $("#btn_call").trigger("click");
                        
                        
                    }
                });
            }
        }
}else{
    Swal.fire("Verifique!", "Debe seleccionar una central telefonica para realizar la llamada", "error");
}
});


function agendarTelefonoSolicitante(documento) {
    // alert(documento);
    let id_solicitud = $('#agendar_tlf_new').data('id_solicitud');
    let select_departamentos, select_ciudad;
    if ($("select#ciudad-new").find(':selected').text() != 'Seleccione') {
        select_departamentos = $("select#departamentos-new").find(':selected').text();
        select_ciudad = $("select#ciudad-new").find(':selected').text();
    } else {
        select_departamentos = '';
        select_ciudad = '';
    }
    const formData = new FormData();
    formData.append("documento", documento);
    formData.append("numero", $("#new-numero-new").val());
    formData.append("fuente", $("#new-fuente-new").val());
    formData.append("contacto", $("#new-contacto-new").val());
    formData.append("estado", $("#new-estado-new").val());
    formData.append("tipo", $("#new-tipo-new").val());
    formData.append("departamento", select_departamentos);
    formData.append("ciudad",select_ciudad );
    formData.append("id_solicitud",id_solicitud );
    formData.append("id_parentesco", $("#parentesco-new").val());
    formData.append("verificado_llamada", $("#llamada-verificada-new").val());
    formData.append("verificado_sms", $("#sms-verificada-new").val());
    formData.append("verificado_whatsapp", $("#wts-verificada-new").val());
    formData.append("llamada", $("#llamada-new").val());
    formData.append("sms", $("#sms-new").val());
    formData.append("whatsapp", $("#wts-new").val());
    
    let comment = '<b>[AGENDA]</b><br> Se Agrego a la Agenda nuevo número de <b>TELEFONO:</b> ' + $("#new-numero-new").val() + ' <b>NOMBRE:</b> ' + $("#new-contacto-new").val();
    let type_contact = 183;
    let id_operador = $("section").find("#id_operador").val();
    if (($("#new-fuente-new").val() == 'REFERENCIA' && $("#new-contacto-new").val() == "" && (($("#parentesco-new").val() == null) || ($("#new-tipo-new").val()== "FIJO" &&($("select#ciudad-new").find(':selected').text() =='Seleccione'))))) {

        Swal.fire("¡Ups!", 'Seleccione los campos correspondiente para crear un contacto referencia', 'error');

    }else {
        $.ajax({
            url: base_url + 'api/solicitud/agendarTelefono',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        }).done(function (response) {
            if (response.status == '200') {
                // get_agenda_telefonica(documento, id_solicitud);
                cargar_box_datos_contacto(id_solicitud);
                // cargar_box_title(id_solicitud);
                render_tabla_agenda();
                Swal.fire("¡Perfecto!", response.message, 'success');
                
                saveTrack2(comment, type_contact, id_solicitud, id_operador, ()=>{get_box_track_stand_alone(id_solicitud,0) });
                if (response.update_principal) {
                    div_number = $("#box_client_title .box-header .row").children()[2];
                    $(div_number).children().html('<i class="fa fa-phone"></i> '+ $("#new-numero-new").val());
                }

            } else {
                Swal.fire("¡Ups!", response.message, 'error');
            }
            $('#new-create-agenda').modal('hide');
        })
            .fail(function (response) {
                //console.log("error");
            })
            .always(function (response) {
                //console.log("complete");
            });
    }
}
$('body').on('click', '#tabla_agenda2 a[id="agregarNumeroAgenda"]', function (event) {

    $('#new-create-agenda input[type=text]').val('');
    $('#new-create-agenda input[type=number]').val('');
 
    $.ajax({
        url: base_url + 'api/credito/get_departamentos',
        type: 'GET'
    })
        .done(function (response) {
            $("#new-create-agenda #new-titulo").html('AGENDAR NÚMERO');
            $("#new-create-agenda #agendar_tlf_new").show();
            $("#new-create-agenda #update_tlf_new").hide();
            if ($("select#new-tipo-new").find(':selected').val() == 'FIJO') {
                $('#div-departamentos-new').show();
                $('#div-ciudad-new').show();

            } else {
                $('#div-departamentos-new').hide();
                $('#div-ciudad-new').hide();
                $('#departamentos-new').val('');
                $('#ciudad-new').val('');
            }
            $('#new-create-agenda').modal('show');
            $("select#departamentos-new").html('');
            if (typeof (response.departamentos) != 'undefined') {
                let departamentos = response.departamentos;
                departamentos.forEach(function (departamento, indice, array) {
                    $("select#departamentos-new").append('<option data-id_departamento="' + departamento.nombre_departamento + '" value="' + departamento.Codigo + '" >' + departamento.nombre_departamento + '</option>')
                });
            }
        })
        .fail(function () {
        })
        .always(function () {
        });

});

function agendarTelefonoSolicitanteAlone(documento) {
    // alert(documento);
    let id_solicitud = $('#agendar_tlf_new').data('id_solicitud');
    // alert(id_solicitud);
    let select_departamentos, select_ciudad;
    if ($("select#ciudad-new").find(':selected').text() != 'Seleccione') {
        select_departamentos = $("select#departamentos-new").find(':selected').text();
        select_ciudad = $("select#ciudad-new").find(':selected').text();
    } else {
        select_departamentos = '';
        select_ciudad = '';
    }
    const formData = new FormData();
    formData.append("documento", documento);
    formData.append("numero", $("#new-numero-new").val());
    formData.append("fuente", $("#new-fuente-new").val());
    formData.append("contacto", $("#new-contacto-new").val());
    formData.append("estado", $("#new-estado-new").val());
    formData.append("tipo", $("#new-tipo-new").val());
    formData.append("departamento", select_departamentos);
    formData.append("ciudad",select_ciudad );
    formData.append("id_solicitud",id_solicitud );
    formData.append("id_parentesco", $("#parentesco-new").val());
    formData.append("verificado_llamada", $("#llamada-verificada-new").val());
    formData.append("verificado_sms", $("#sms-verificada-new").val());
    formData.append("verificado_whatsapp", $("#wts-verificada-new").val());
    formData.append("llamada", $("#llamada-new").val());
    formData.append("sms", $("#sms-new").val());
    formData.append("whatsapp", $("#wts-new").val());
    
    let comment = '<b>[AGENDA]</b><br> Se Agrego a la Agenda nuevo número de <b>TELEFONO:</b> ' + $("#new-numero-new").val() + ' <b>NOMBRE:</b> ' + $("#new-contacto-new").val();
    let type_contact = 183;
    let id_operador = $("#id_operador").val();
    if (($("#new-fuente-new").val() == 'REFERENCIA' && $("#new-contacto-new").val() == "" && (($("#parentesco-new").val() == null) || ($("#new-tipo-new").val()== "FIJO" &&($("select#ciudad-new").find(':selected').text() =='Seleccione'))))) {

        Swal.fire("¡Ups!", 'Seleccione los campos correspondiente para crear un contacto referencia', 'error');

    }else {
        $.ajax({
            url: base_url + 'api/solicitud/agendarTelefono',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        }).done(function (response) {
            if (response.status == '200') {
                // get_agenda_telefonica(documento, id_solicitud);
                cargar_box_datos_contacto(id_solicitud);
                // cargar_box_title(id_solicitud);
                render_tabla_agendaTlf(documento);
                Swal.fire("¡Perfecto!", response.message, 'success');
                
                saveTrack2(comment, type_contact, id_solicitud, id_operador, ()=>{get_box_track_stand_alone(id_solicitud,0) });
                if (response.update_principal) {
                    div_number = $("#box_client_title .box-header .row").children()[2];
                    $(div_number).children().html('<i class="fa fa-phone"></i> '+ $("#new-numero-new").val());
                }

            } else {
                Swal.fire("¡Ups!", response.message, 'error');
            }
            $('#new-create-agenda-alone').modal('hide');
        })
            .fail(function (response) {
                //console.log("error");
            })
            .always(function (response) {
                //console.log("complete");
            });
    }
}

function agendarMailSolicitanteAlone(documento) {
    // alert(documento);
    let id_solicitud = $('#btn_agendar_mail_new').data('id_solicitud');
    // alert(id_solicitud);

    const formData = new FormData();
    formData.append("documento", documento);
    formData.append("cuenta", $("#new-cuenta-mail").val());
    formData.append("contacto", $("#new-contacto-mail").val());
    formData.append("fuente", $("#new-fuente-mail").val());
    formData.append("estado", $("#new-estado").val());
    formData.append("id_solicitud",id_solicitud );
    
    let comment = '<b>[AGENDA]</b><br> Se Agrego a la Agenda nuevo  <b>Mail:</b> ' + $("#new-cuenta-mail").val() + ' <b>NOMBRE:</b> ' + $("#new-contacto-mail").val();
    let type_contact = 183;
    let id_operador = $("#id_operador").val();
    if (($("#new-cuenta-mail").val() == '' && $("#new-contacto-mail").val() == "" && $("#new-cuenta-mail").val() == '' && $("#new-fuente-mail").val() == '')) {

        Swal.fire("¡Ups!", 'Seleccione los campos correspondiente para crear un contacto mail referencia', 'error');

    }else {
        $.ajax({
            url: base_url + 'api/solicitud/agendarMail',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        }).done(function (response) {
            if (response.ok) {
                render_tabla_agenda_mail();            
                Swal.fire("¡Perfecto!", response.message, 'success');
                $("#add_mail_new").modal("hide");
                saveTrack2(comment, type_contact, id_solicitud, id_operador, ()=>{get_box_track_stand_alone(id_solicitud,0) });


            } else {
                Swal.fire("¡Ups!", response.message, 'error');
            }


            
            $('#new-create-agenda-mail-alone').modal('hide');
        })
            .fail(function (response) {
                //console.log("error");
            })
            .always(function (response) {
                //console.log("complete");
            });
    }
}

function render_tabla_agendaTlf(documento =null) {
    if (documento== null){
        let documento = $("#client").data('number_doc');
    }
    // alert (documento)
    var hoy = new Date();
    var hoy_moment = moment(hoy, "YYYY-MM-DD");
    
    $.ajax({
        url: base_url + 'atencion_cliente/agendaTelefonica/' + documento,
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#table-agenda-telefono").html(loading);
        },
        success: function (respuesta) {
            var registros = eval(respuesta);
			var id_soli = $("#solicitud").data('number_soli');
			var docu = $("#client").data('number_doc');
            

            html = '<table class="table modificable" id="table-agenda-telefono">';
            html += '<thead>';
				html += '<th>Número</th>';
				html += '<th>Contacto</th>';
				html += '<th>Fuente</th>';
				html += '<th>Estado</th>';
				html += '<th>Editar</th>';
			html += '</thead>';
			html += '<tbody>';
            $.each(registros, function (j, valor) {

                let botton, fuente_tlf = "";
                let class_icono = "";
                var btn_servicio_ws, btn_servicio_sms = "";

                if (valor["departamento"] != "" && valor["departamento"] != null) {
                    botton = valor["ciudad"] + " / " + valor["departamento"];
                } else {
                    botton = '<i class="fa fa-map-marker text-red"> Agregar </i>';
                }
                
                if (valor["tipo"] == 'MOVIL') {
                    class_icono = '<i class="fa fa-mobile" aria-hidden="true"></i>';
                } else {
                    class_icono = '<i class="fa fa-phone" aria-hidden="true"></i>';
                }
                switch (valor["fuente"]) {
                    case "PERSONAL":
                        fuente_tlf = "Personal";
                        break;
                     case "PERSONAL DECLARADO":
                        fuente_tlf = "Personal Declarado";
                        break;
                     case "PERSONAL LLAMADA":
                        fuente_tlf = "Personal Llamada";
                        break;
                    case "PERSONAL WHATSAPP":
                        fuente_tlf = "Personal Whatsapp";
                        break;
                     case "PERSONAL ANTERIOR":
                        fuente_tlf = "Personal Anterior";
                        break;
                    case "REFERENCIA":
                        fuente_tlf = "Referencia";
                        break;
                    case "LABORAL":
                        fuente_tlf = "Laboral";
                        break;
                    case "BURO_CELULAR":
                        fuente_tlf = "Buro - Celular - D";
                        break;
                    case "BURO_CELULAR_T":
                        fuente_tlf = "Buro - Celular - T";
                        break;
                    case "BURO_LABORAL":
                        fuente_tlf = "Buro - Laboral - D";
                        break;
                    case "BURO_RESIDENCIAL":
                        fuente_tlf = "Buro - Residencial - D";
                        break;
                }
                if (valor["primer_reporte"] != null && valor["primer_reporte"] != "") {
                    var primer_reporte = valor["primer_reporte"]
                    var primer_reporte_moment = moment(primer_reporte, "YYYY-MM-DD");
                    antiguedad = hoy_moment.diff(primer_reporte_moment, 'months');

                } else {
                    antiguedad = 0;
                }
                if (valor["verificado_llamada"] == null) {
                    valor["verificado_llamada"] = 0;
                }
                if (valor["verificado_sms"] == null) {
                    valor["verificado_sms"] = 0;
                }
                if (valor["verificado_whatsapp"] == null) {
                    valor["verificado_whatsapp"] = 0;
                }
                
                if (valor["fuente"] != 'PERSONAL DECLARADO') {
                    btn_edit = '<button class="btn btn-sm  btn_edit_num" style="background:#0073b7;color:white;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
                } else {
                    btn_edit = '<button disabled="true" class="btn btn-sm  btn_edit_num" style="background:gris;color:white;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
                }
                if (valor["estado"] == 0) {
                    slc_activo = '';
                    slct_inactivo = 'selected';
                } else {
                    slc_activo = 'selected';
                    slct_inactivo = '';
                }

                if (valor["fuente"] == "PERSONAL" || 
				    valor["fuente"] == "PERSONAL DECLARADO" ||  
					valor["fuente"] == "PERSONAL LLAMADA" ||
					valor["fuente"] == "PERSONAL WHATSAPP"||
					valor["fuente"] == "PERSONAL ANTERIOR"||
					valor["fuente"] == "REFERENCIA")
				{


                        html += "<tr>";
                        html +=' <td><div class="form-check"><input type="checkbox" class="form-check-input" id="ch-tel-'+valor["id"]+'" value ="'+valor["id"]+'" name="ch-telefonos" data-numero_telefono="'+valor["numero"]+'" data-estado="'+valor["estado"]+'"></div></td>';
                        html +=' <td class="numero">'+  valor["numero"] +'</td>';
                                            
                        html +='<td><div class="form-group" style="margin-bottom: 0px;" ><input type="text" class="form-control" id="contacto-tel-'+valor["id"]+'" value="'+valor["contacto"]+'"></div></td>';
                        html +='<td>';
                        
                        html +=' <select class="form-control" id="fuente-tel-'+valor["id"]+'">';
                        html +=' <option value="PERSONAL" '+ (valor["fuente"].toUpperCase() == "PERSONAL"?'selected':'') +'>Personal</option>';
                        html +=' <option value="PERSONAL DECLARADO" '+ (valor["fuente"].toUpperCase() == "PERSONAL DECLARADO"?'selected':'') +'>Personal Declarado</option>';
                        html +=' <option value="PERSONAL ANTERIOR" '+ (valor["fuente"].toUpperCase() == "PERSONAL ANTERIOR"?'selected':'') +'>Personal Anterior</option>';
                        html +=' <option value="PERSONAL LLAMADA" '+ (valor["fuente"].toUpperCase() == "PERSONAL LLAMADA"?'selected':'') +'>Personal Llamada</option>';
                        html +=' <option value="PERSONAL WHATSAPP" '+ (valor["fuente"].toUpperCase() == "PERSONAL WHATSAPP"?'selected':'') +'>Personal WhatsApp</option>';
                        html +=' <option value="REFERENCIA" '+ (valor["fuente"].toUpperCase() == "REFERENCIA"?'selected':'') +'>Referencia</option>';
                        html +='</select>';
                        html +='<input type="hidden" id="tipo-tel-'+valor["id"]+'" value="'+valor["tipo"].toUpperCase()+'" ><input type="hidden" id="parentesco-tel-'+valor["id"]+'" value="'+valor["id_parentesco"].toUpperCase()+'" >';
                        html +='</td>';
                        html +='<td>';
                        html +='<select class="form-control" id="estado-tel-'+valor["id"]+'">';
                        html +='<option value="1" '+ (valor["estado"] == 1 ? 'selected': '' ) +'>Activo</option>';
                        html +='<option value="0" '+ (valor["estado"] == 0 ? 'selected': '' ) +'>Fuera de servicio</option>';
                        html +='</select>';
                        html +='</td>';
                        html +='<td><a class="btn btn-info btn-sm" onclick="guardarCambioTlf('+valor["id"]+','+documento+')"><i class="fa fa-save"></i></a></td>';



                        // html += '<td class="numero" data-id="'+valor["id"]+'">' + valor['numero'] + '</td>';
                        // html += '<td><div class="form-group" style="margin-bottom: 0px;"><span>' + valor["contacto"] + '</span></div></td>';
                        // html += '<td>' + fuente_tlf + '</td>';
                        // html += '<td data-numero="'+valor['numero']+'"> <select class="form-control selectpicker slc_estado_telefono" data-id="' + valor["id"] + '" data-documento="' + valor["documento"] + '" id="slc_estado_telefono"><option value="1" ' + slc_activo + '>Activo</option><option value="0" ' + slct_inactivo + '>Fuera de servicio</option></select></td>';
                        // html += '<td data-id="'+valor["id"]+'">'+btn_edit+'</td>'
                        html += '</tr>';
                }

            });
            html += '</tbody>';
            $('#table-agenda-telefono').html(html);
            TablaPaginada("table-agenda-telefono", 0, "asc", "", "");
               
        }
    });
}
function render_tabla_agenda_mail(documento =null) {
	if (documento== null){
		let documento = $("#client").data('number_doc');
    }
	var id_soli = $("#solicitud").data('number_soli');
	var docu = $("#client").data('number_doc');

    
    var hoy = new Date();
    var hoy_moment = moment(hoy, "YYYY-MM-DD");
    
    $.ajax({
        url: base_url + 'atencion_cliente/agendaMail/' + documento,
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#render_tabla_agenda_mail").html(loading);
        },
        success: function (respuesta) {
            var registros = eval(respuesta);
            

            html = '<table class="table modificable" id="table_agenda_mail">';
            html += '<thead>';
				html += '<th></th>';
				html += '<th>Cuenta</th>';
				html += '<th>Contacto</th>';
				html += '<th>Fuente</th>';
				html += '<th>Estado</th>';
				html += '<th>Editar</th>';
			html += '</thead>';
			html += '<tbody>';
			// console.log(registros.data)
            $.each(registros.data, function (j, valor) {
				// console.log(valor["cuenta"] )
                let botton, fuente_tlf = "";
                let class_icono = "";
                var btn_servicio_ws, btn_servicio_sms = "";
                
				if (valor["fuente"] == "PERSONAL" || 
				    valor["fuente"] == "PERSONAL DECLARADO" ||  
					valor["fuente"] == "PERSONAL LLAMADA" ||
					valor["fuente"] == "PERSONAL WHATSAPP"||
					valor["fuente"] == "PERSONAL ANTERIOR"||
					valor["fuente"] == "REFERENCIA")
				{


					html += "<tr>";
                        html +=' <td><div class="form-check"><input type="checkbox" class="form-check-input" id="ch-agen-'+valor["id"]+'" value ="'+valor["id"]+'" name="ch-telefonos" data-cuenta="'+valor["cuenta"]+'" data-estado="'+valor["estado"]+'"></div></td>';
                        html +=' <td class="numero">'+ valor["cuenta"] +'</td>';
                                            
                        html +='<td><input type="text" class="form-control" id="contacto-agen-'+valor["id"]+'" value="'+valor["contacto"]+'"></td>';
                        html +='<td>';
                        html +=' <select class="form-control" id="fuente-mail-'+valor["id"]+'">';
                        html +=' <option value="PERSONAL" '+ (valor["fuente"].toUpperCase() == "PERSONAL"?'selected':'') +'>Personal</option>';
                        html +=' <option value="PERSONAL DECLARADO" '+ (valor["fuente"].toUpperCase() == "PERSONAL DECLARADO"?'selected':'') +'>Personal Declarado</option>';
                        html +=' <option value="PERSONAL ANTERIOR" '+ (valor["fuente"].toUpperCase() == "PERSONAL ANTERIOR"?'selected':'') +'>Personal Anterior</option>';
                        html +=' <option value="PERSONAL LLAMADA" '+ (valor["fuente"].toUpperCase() == "PERSONAL LLAMADA"?'selected':'') +'>Personal Llamada</option>';
                        html +=' <option value="PERSONAL WHATSAPP" '+ (valor["fuente"].toUpperCase() == "PERSONAL WHATSAPP"?'selected':'') +'>Personal WhatsApp</option>';
                        html +=' <option value="REFERENCIA" '+ (valor["fuente"].toUpperCase() == "REFERENCIA"?'selected':'') +'>Referencia</option>';
                        html +='</select>';
                        html +='</td>';
                        html +='<td>';
                        html +='<select class="form-control" id="estado-mail-'+valor["id"]+'">';
                        html +='<option value="1" '+ (valor["estado"] == 1 ? 'selected': '' ) +'>Activo</option>';
                        html +='<option value="0" '+ (valor["estado"] == 0 ? 'selected': '' ) +'>Fuera de servicio</option>';
                        html +='</select>';
                        html +='</td>';
                        html +='<td><a class="btn btn-info btn-sm" onclick="guardarCambioMail('+valor["id"]+','+documento+')"><i class="fa fa-save"></i></a></td>';



                        // html += '<td class="numero" data-id="'+valor["id"]+'">' + valor['numero'] + '</td>';
                        // html += '<td><div class="form-group" style="margin-bottom: 0px;"><span>' + valor["contacto"] + '</span></div></td>';
                        // html += '<td>' + fuente_tlf + '</td>';
                        // html += '<td data-numero="'+valor['numero']+'"> <select class="form-control selectpicker slc_estado_telefono" data-id="' + valor["id"] + '" data-documento="' + valor["documento"] + '" id="slc_estado_telefono"><option value="1" ' + slc_activo + '>Activo</option><option value="0" ' + slct_inactivo + '>Fuera de servicio</option></select></td>';
                        // html += '<td data-id="'+valor["id"]+'">'+btn_edit+'</td>'
                        html += '</tr>';

				}
            });
            html += '</tbody>';
			// console.log(html)
            $('#table_agenda_mail').html(html);
            TablaPaginada("table_agenda_mail", 0, "asc", "", "");
               
        }
    });
}
 $(document).on('click', '.template_agenda_sms', function (e) {
        let phoneNum = $(this).parents('td').data('numero');
        let phoneNumMs = $(this).parents('td').data('numero_ws');
        // let phoneNum = '+541173666991';
        let tipo_envio_variable = $(this).data('tipo_envio');
        let message_traslate = $(this).data('template_description');
        let tipo_envio = $(this).data('tipo_template');
        let proveedor = $(this).data('proveedor');
        let id_template = $(this).data('id_template');

        let formData = new FormData();
        formData.append('numero', phoneNum);
        formData.append('numero_ms', phoneNumMs);
        formData.append('text', message_traslate);
        formData.append('servicio', proveedor);
        formData.append('tipo_envio', tipo_envio);

        swal.fire({
            title: "¿Esta seguro?",
            text: "¿Estas seguro de enviar SMS seleccionado?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, Enviar"
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                url: base_url + 'api/ApiSolicitud/enviarSmsIvrAgendaTelefonica',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false, 
                crossDomain: true,
                beforesend: function () {
                request.setRequestHeader("Access-Control-Allow-Origin", '*');
                }  ,             
            })
                .done(function (response) {
                    // get_agenda_telefonica(documento, id_solicitud);
                    render_tabla_agenda();
                    swal.fire('Exito', 'Se envio Mensaje SMS', 'success');
                    
                    if (tipo_envio_variable == 'MIXTO') {
                        updateAgendaProveedor(id_template, proveedor);
                    }
                    
                })
                .fail(function () {
                    swal.fire('Fail','Mensaje SMS no pudo ser enviado al cliente','error');
                })
                .always(function () {
                });

            }
            
        })

    });

    function updateAgendaProveedor(id_template, proveedor) { 
        let formData = new FormData();
        formData.append('id_template', id_template);
        formData.append('proveedor', proveedor);

        $.ajax({
            url: base_url + 'api/solicitud/updateAgendaProveedor',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false, 
            crossDomain: true,
        })
        .done(function (response) {
            toastr["success"]("Ok Se actualizo proveedor", "Actualizado")
        })
        .fail(function () {
            toastr["error"]("No se actualizo proveedor", "Error")

        })
        .always(function () {
        });
    }


    $(document).on('click', '.template_agenda_ivr', function (e) {
        let phoneNum = $(this).parents('td').data('numero');
        let phoneNumMs = $(this).parents('td').data('numero_ws');
        let message_traslate = $(this).data('template_description');
        let tipo_envio = $(this).data('tipo_template');
        let proveedor = $(this).data('proveedor');
        
        let formData = new FormData();
        formData.append('numero', phoneNum);
        formData.append('numero_ms', phoneNumMs);
        formData.append('text', message_traslate);
        formData.append('servicio', proveedor);
        formData.append('tipo_envio', tipo_envio);

        swal.fire({
            title: "¿Esta seguro?",
            text: "¿Estas seguro de enviar IVR seleccionado?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, Enviar"
        }).then(function (result) {
            if (result.value) {
                
                $.ajax({
                url: base_url + 'api/ApiSolicitud/enviarSmsIvrAgendaTelefonica',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                crossDomain: true,
                beforesend: function () {
                    request.setRequestHeader("Access-Control-Allow-Origin", '*');
                },
                
            })
                .done(function (response) {
                 swal.fire('Exito', 'Mensaje IVR Enviado, a la espera del cliente', 'success');
                })
                .fail(function () {
                    swal.fire('Fail','Mensaje IVR no pudo ser enviado al cliente','error');
                })
                .always(function () {
                });

            }
            
        })

    });

    $(document).on('click', '#div_send_tmpl', function (e) {
        $("div").removeClass("habilitar_send_template");
        $("#btn_send_tmpl button").prop("disabled", true);
        $(this).addClass("habilitar_send_template");
        $(this).children("#btn_send_tmpl").children("button").prop("disabled", false);
    });

    $(document).on('click', '.template_agenda_ws', function () {
        let solID = $(this).data('solicitude');
        let phoneNum = $(this).parents('td').data('numero_ws');
        let id_template = $(this).data('id_template');
        let cookies = getCookie('__data_operator');
        cookies = cookies.split(',');
        let user_type = parseInt(cookies[1]);

        // let phoneNum = '+541173666991';
        let message_traslate = $(this).data('template_description');
        let prefix = '/backend/';
        let formData = new FormData();
        formData.append('solID', solID);
        formData.append('phoneN', phoneNum);
        formData.append('Template', message_traslate);
        formData.append('id_template', id_template);

        switch (user_type) {
            case 1:
                url_base = window.location.origin + prefix + 'comunicaciones/twilio';
                message_traslate;
                // template_id
                break;
            case 4:
                url_base = window.location.origin + prefix + 'comunicaciones/twilio';
                message_traslate;
                // template_id;
                break;
            case 5:
                url_base = window.location.origin + prefix + 'comunicaciones/TwilioCobranzas';
                message_traslate;
                // template_id;
                break;
            case 6:
                url_base = window.location.origin + prefix + 'comunicaciones/TwilioCobranzas';
                message_traslate;
                // template_id;
                break;
            default:
                url_base = window.location.origin + prefix + 'comunicaciones/twilio';
                message_traslate;
                // template_id;
                break;
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
            if (result.value) {
                 $.ajax(
                    {
                        url: url_base + '/send_template_message_new',
                        type: 'POST',
                         data: formData,
                        processData: false,
                        contentType: false,
                    }
                 ).done(function (re) {
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
        })

    });

function validaNumericos(event) {
    if ((event.charCode >= 48 && event.charCode <= 57) || event.charCode == 44) {
        return true;
    }
    return false;
}

$("#new-numero-new").on('keyup', (e)=> {
    var numero_tlf = e.target.value;
    if (numero_tlf.length > 0 && numero_tlf.length <= 7) {
        $("#new-tipo-new").val("FIJO");
        $('#div-departamentos-new').show();
        $('#div-ciudad-new').show();
    } else{
        $("#new-tipo-new").val("MOVIL");
        $('#div-departamentos-new').hide();
        $('#div-ciudad-new').hide();
        $('#departamentos-new').val('');
        $('#ciudad-new').val('');
    }
});

function validateEmail() {
    const $result = $("#new-cuenta-mail");
    const mail = $("#new-cuenta-mail").val();
    $result.text("");
  
    if (validateReEmail(mail)) {
        $result.css("border-color", "lightgreen");
          $('#agendar_mail').prop('disabled', false);
  
      } else {
        $result.css("border-color", "red");
          $('#agendar_mail').prop('disabled', true);
      }
    return true;
  }

  function saveTrack2(comment, typeContact, idSolicitude, idOperator, callback) {
	$('#btn_save_comment').addClass('disabled');
    console.log({'observaciones':comment, 'id_tipo_gestion':typeContact, 'id_solicitud':idSolicitude, 'id_operador':idOperator});
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

function get_box_track_stand_alone(id_solicitud, id_credito=0) {
    var documento= $("#box_client_title").data("documento");
    $.ajax({
        url: base_url + 'gestion/Tracker/track_stand_alone/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".tracker").html(response);
            $(".tracker #box_tracker .box-footer").css('min-height', '185px');
            $(".tracker #box_tracker .box-body").css('height', 'calc(100% - 185px)');
            get_box_whatsapp(documento);
            if(id_credito > 0){
                $('#result').addClass('hide');
                document.getElementById('id_credito').value = id_credito;
            }
            
        })
        .fail(function (response) {
        })
        .always(function () {

        });
}

