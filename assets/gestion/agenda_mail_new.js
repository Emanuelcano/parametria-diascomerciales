$(document).ready(function () {

    render_tabla_agenda_mail();
    $('body').on('click', '#tabla_agenda_3 i.copy', function (element) {
        var text = $(this)
            .parent("td")
            .text();
        copiar(text);
    });
    
    var acc_13 = document.getElementsByClassName("accordion_13");
var i13;
    for (i13 = 0; i13 < acc_13.length; i13++) {
  acc_13[i13].addEventListener("click", function() {
      this.classList.toggle("active");
      
      if ($(this).hasClass('active')) {
        $('.title_button_vermail').text('DIRECTORIO MAIL');
      } else {
        $('.title_button_vermail').text('VER DIRECTORIO MAIL');

      }
    var panel_13 = this.nextElementSibling;
    if (panel_13.style.display === "block") {
      panel_13.style.display = "none";
    } else {
      panel_13.style.display = "block";
    }
  });
}
    
});
    email_t = []

    get_data_template = () => {
        
        let id_cliente = $('#id_cliente').data('id_cliente');
        if (id_cliente != ''){
            const formData = new FormData();
            formData.append("id_cliente", id_cliente);
            $.ajax({
                url: base_url + 'api/solicitud/get_template_data',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
            });
        }
    }

function render_tabla_agenda_mail() {
    let documento = $("#client").data('number_doc');
    var hoy = new Date();
    var hoy_moment = moment(hoy, "YYYY-MM-DD");
    var btn_action_mail;

    $.ajax({
        url: base_url + 'atencion_cliente/agendaMail/' + documento,
        type: 'GET',
        dataType: 'json',beforeSend: function () {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#tabla_agenda_3").html(loading);
        },
        success: function (respuesta) {
            var registros = eval(respuesta['data']);
            html = '<a style="margin-bottom: 0.5em; float: right;" class="btn btn-success" id="agregarMailAgenda"><i class="fa fa-plus"></i> AGENDAR</a>';

            html += '<table class="table modificable" id="table_agenda_mail">';
            html += '<thead><th>Cuenta</th><th>Contacto</th><th>Fuente</th><th>Antiguedad</th><th>Estado</th><th></th></thead><tbody>';
            $.each(registros, function (j, valor) {
                let fuente_mail;
                switch (valor["fuente"]) {
                    case "PERSONAL":
                        fuente_mail = "Personal";
                        break;
                    case "REFERENCIA":
                        fuente_mail = "Referencia";
                        break;
                    case "LABORAL":
                        fuente_mail = "Laboral";
                        break;
                    case "BURO_T":
                        fuente_mail = "Buro - Mail - T";
                        break;
                    case "BURO_D":
                        fuente_mail = "Buro - Laboral - D";
                        break;
                    
                }
                if (valor["estado"] == 0) {
                    slc_activo = '';
                    slct_inactivo = 'selected';
                    btn_action_mail = '<button disabled style="background:grey; color: white;" class="btn btn-sm __send_template_mail accordion_11" ><i class="fa fa-envelope" aria-hidden="true"></i></button>';
                } else {
                    slc_activo = 'selected';
                    slct_inactivo = '';
                    btn_action_mail = '<button style="background:#00c0ef; color: white;" class="btn btn-sm __send_template_mail accordion_11" ><i class="fa fa-envelope" aria-hidden="true"></i></button><div class="print_acordeon"></div>';

                }
                
                if (valor["primer_reporte"] != null && valor["primer_reporte"] != "") {
                    var primer_reporte = valor["primer_reporte"]
                    var primer_reporte_moment = moment(primer_reporte, "YYYY-MM-DD");
                    antiguedad = hoy_moment.diff(primer_reporte_moment, 'days');

                } else {
                    antiguedad = 0;
                }
                
                html += "<tr>";
                html += '<td class="cuenta">' + valor['cuenta'] + '<i class="fa fa-clone pull-right copy" style="color: red; " title="Copiar Mail">&nbsp;&nbsp;</i></td>';
                html += '<td>'+valor['contacto']+'</td>';
                html += '<td>' + fuente_mail + '</td>';
                html += '<td style="text-align: center;">' + antiguedad + '</td>';
                html += '<td> <select class="form-control selectpicker slc_estado_mail" data-id="' + valor["id"] + '" data-documento="' + valor["documento"] + '" id="slc_estado_mail"><option value="1" ' + slc_activo + '>Activo</option><option value="0" ' + slct_inactivo + '>Fuera de servicio</option></select></td>';
                html += '<td  data-email="' + valor["cuenta"] + '" style="text-align: center;">'+btn_action_mail+'</td>';
                html += '</tr>';
            });
            html += '</tbody>';
            $('#tabla_agenda_3').html(html);
            TablaPaginada("table_agenda_mail", 0, "asc", "", "");
               
        }
    });
} 

$("body").on("change","#tabla_agenda_3 select.slc_estado_mail",function (event) {
        let estado = $(this).val();
        let id = $(this).data('id');
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
                url: base_url + 'api/solicitud/actualizarMailEstado',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                crossDomain: true,
                
            })
                .done(function (response) {
                    swal.fire('Exito', 'Se cambio el estado del mail', 'success');
                    render_tabla_agenda_mail();
                })
                .fail(function () {
                    swal.fire('Fail','Error al cambiar estado','error');

                })
                .always(function () {
                });

            }
            
        });
        
    });

$("body").on("click", "#tabla_agenda_3 a#agregarMailAgenda", function (event) {
    $('#add_mail_new input[type=text]').val('');
    $('#add_mail_new input[type=number]').val('');
    $('#add_mail_new').modal('show');

});

function agendarMail(documento){
    let id_solicitud = $('#agendar_tlf_new').data('id_solicitud');
    let comment = '<b>[AGENDA]</b><br> Se Agrego a la Agenda nuevo  <b>Mail:</b> ' + $("#new-cuenta-mail").val() + ' <b>NOMBRE:</b> ' + $("#new-contacto-mail").val();
    let type_contact = 183;
    let id_operador = $("section").find("#id_operador").val();

    const formData = new FormData();
    formData.append("documento", documento);
    formData.append("cuenta", $("#new-cuenta-mail").val());
    formData.append("fuente", $("#new-fuente-mail").val());
    formData.append("contacto", $("#new-contacto-mail").val());
    formData.append("estado", $("#new-estado").val());


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
                saveTrack(comment, type_contact, id_solicitud, id_operador);


            } else {
                Swal.fire("¡Ups!", response.message, 'error');
            }
                
        })
            .fail(function (response) {
            })
            .always(function (response) {
            });
}


$("body").on("click", "#tabla_agenda_3 button.__send_template_mail", function (event) {
    let html2;
    let boton_action_mail = this;
    let canal = $('#tipo_canal').val();
    let documento = $("#client").data('number_doc');
    var bandera_11 = 1;
    if ($(this).next().find('.panel_11').hasClass('active_panel_2'))
    {
        bandera_11 = 2
    }
    const formData = new FormData();
    formData.append("documento", documento);
    formData.append("canal", canal);

    $.ajax({
        url: base_url + 'atencion_cliente/agendaMailTemplate',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
    })
        .done(function (response) {
            var template_mail_view = eval(response);
            email_t.listtemplate = []
            html2 = '<div class="panel_11" style="box-shadow: rgb(0, 0, 0) 0px 3px 10px -3px; white-space: none !important;">';

                    $.each(template_mail_view, function (j, template_mail_view) {
                            email_t.listtemplate[template_mail_view['id']] = template_mail_view
                            html2 += '<div class="" style="margin: 0px;display: flex;justify-content: center;align-items: center;padding-right: 0px" role="presentation">';
                            html2 += '<div class="col-sm-10" style="text-align: justify;padding-right: 0px;padding-top: 15px;padding-bottom: 15px;">Asunto: <strong>'+template_mail_view["nombre_logica"]+'</strong> - Template: <strong>'+template_mail_view["nombre_template"]+'</strong> </div>';
                            html2 += '<div class="col-sm-2" id="btn_send_mail" style="float: right; vertical-align:middle">';
                            // html2 += '<button data-id_template="' + template_mail_view['id'] + '" data-id_logica="' + template_mail_view['id_logica'] + '" class="btn btn-success btn-xs send_template_mail"><i class="fa fa-send"></i></button>';
                            html2 += '<button style="color:white;background:black; margin-left:0.5em;" data-id_template="' + template_mail_view['id'] + '" data-id_logica="' + template_mail_view['id_logica'] + '" class="btn-xs preview_html"><i class="fa fa-eye"></i></button>';
                            html2 += '</div></div><div role="separator" class="divider_div"></div>';                                            
                    });
            html2 += '</div>';
            $(boton_action_mail).next().html(html2);
            if (bandera_11 > 1) {
                $(boton_action_mail).next().find('.panel_11').removeClass('active_panel_2');
            }
            else {
                $('.panel_11').removeClass('active_panel_2');
                $(boton_action_mail).next().find('.panel_11').addClass('active_panel_2');
               
            }
            get_data_template()

        })
        .fail(function (jqXHR) {
            console.log(jqXHR);
        })
        .always(function () {
        });

});

$("body").on("click", "#tabla_agenda_3 button.preview_html ", function (event) {
    let id_template_mail = $(this).data('id_template');
    let documento = $("#client").data('number_doc');
    let id_logica = $(this).data('id_logica');
    let mail = $(this).parents('td').data('email');
    $('#preview_mail_html .modal-footer .sendTemplateMail').data('mail', mail);
    $('#preview_mail_html .modal-footer .sendTemplateMail').data('id_template', id_template_mail);
    $('#preview_mail_html .modal-footer .sendTemplateMail').data('id_logica', id_logica);

    email_t.sel_t = []
    email_t.sel_t = email_t.listtemplate[id_template_mail]
    email_t.sel_t.email = mail
    
    const formData = new FormData();
    formData.append("documento", documento);
    formData.append("id_template_mail", id_template_mail);
    $.ajax({
        url: base_url + 'api/solicitud/agendaMailTemplateHtml',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
    })
        .done(function (response) {
            var template_mail_preview = response;
            
            $('#preview_mail_html .modal-body').html(template_mail_preview.message);
            if (template_mail_preview.deshabilitar == true) {
                $('.sendTemplateMail').prop('disabled', true);
            } else {
                $('.sendTemplateMail').prop('disabled', false);
            }
            $('#preview_mail_html').modal('show');
            
        });
});

$("body").on("click", "#preview_mail_html .modal-footer .sendTemplateMail", function (event) {
    let documento = $("#client").data('number_doc');
    let mail = $(this).data('mail');
    let id_template = $(this).data('id_template');
    let id_logica = $(this).data('id_logica');
    let formData = new FormData();
    formData.append('documento', documento);
    formData.append('mail', mail);
    formData.append('id_template', id_template);
    formData.append('id_logica', id_logica);
        swal.fire({
        title: "¿Esta seguro?",
        text: "¿Estas seguro de enviar Mail seleccionado?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, Enviar"
    }).then(function (result) {
        if (result.value) {
            $.ajax({
            url: base_url + 'api/solicitud/enviarMailAgendaPepipost',
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
                // render_tabla_agenda_mail();
                let id_solicitud = $('#agendar_tlf_new').data('id_solicitud');
                let comment = 
                '<b>[ENVIO EMAIL]</b><br><b>Mail: </b>' + email_t.sel_t.email + 
                '</b><br><b>Asunto: </b>' + email_t.sel_t.nombre_logica + 
                '</b><br><b>Template: </b>' + email_t.sel_t.nombre_template;
                let type_contact = 6;
                let id_operador = $("section").find("#id_operador").val();
                saveTrack(comment, type_contact, id_solicitud, id_operador);
                get_box_track(id_solicitud);
                swal.fire('Exito', 'Se envio Mensaje Mail', 'success');
                $("#preview_mail_html").modal('hide');
            })
            .fail(function () {
                swal.fire('Fail','Mail no pudo ser enviado al cliente','error');
            })
            .always(function () {
            });

        }
        
    })
    
});

function validateReEmail(mail) {
  const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(mail);
}

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

$("#new-cuenta-mail").on('change', () => {
    validateEmail();
})