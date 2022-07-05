//cambio estado del template 
$(document).one('click', 'a.btnChangeStatus', function(){
    var template_id = $(this).data('template');
    var  status = $(this).data('status') == 1 ? 0 : 1; 
    var template_type =  $('input[name="template_type"]').val();

    $.ajax({
        url: base_url + 'whatsapp/Templates/changeStatus/' + template_id + '/' + status,
        method:'GET',
        success: function(response){
            
            Swal.fire('', 'Se cambio el estado correctamente', 'success');
            templateList(template_type);
        }
    });
});

//abro la edicion
$(document).on('click', 'a.btnFormTemplate', function(){
    
    restart_modal();
    $("#test_by_documento").css({display:"block"});
    var template_type =  $('input[name="template_type"]').val();
    var template_id = $(this).data('template');

    $.ajax({
        type: "GET",
        url: base_url + 'whatsapp/Templates/getTemplate/' + template_id + '/'+ template_type,
        dataType: 'json',
        success: function (response) {
      
            var obj = jQuery.parseJSON(JSON.stringify(response));
    
            $('#modalTemplate').modal('show');
            
            $('#canal').val(obj.template[0].canal.split(','));
            $('#canal').select2({
                placeholder: '.: Selecciona los criterios :.',
                multiple : true
            });
          
            $("#tipo_template option[value='" + obj.template[0].tipo_template + "']").attr("selected", true);
            $("#grupo option[value='" + obj.template[0].grupo + "']").attr("selected", true);
            
            $("textarea#msg_string").val(obj.template[0].msg_string);
            $('input[name="template_id"]').val(obj.template[0].id);

            // CREO SELECTOR DE VARIABLES DINAMICO
            var select_variables = "";
            select_variables =     `<div id="variable_container">
                                        <label for="variable">Variables: </label>
                                        <select class="form-control` + (  obj.variables.length > 0 ? '' : 'disabled') + `" name="variable" id="variable" style="width: 100%" placeholder="Canal" ` + (  obj.variables.length > 0 ? '' : 'disabled="disabled"') + `>
                                        <option value="-1" disabled selected>Seleccione Variable</option>`;

            obj.variables.map(function(element,index){
                select_variables +=`<option value="`+element.variable+`">`+element.variable+`</option>`;
            });

            select_variables += `   </select>
                                </div>`;

            // CREO SELECTOR DE PROVEEDORES DINAMICO
            var select_proveedores = "";
            select_proveedores =     `<label for="variable">Proveedores: </label>
                                        <select class="form-control` + (  obj.proveedores.length > 0 ? '' : 'disabled') + `" name="proveedor" id="proveedor" style="width: 100%" placeholder="Canal" ` + (  obj.proveedores.length > 0 ? '' : 'disabled="disabled"') + `>
                                        <option value="-1" disabled selected>Seleccione Variable</option>`;

            obj.proveedores.map(function(element,index){
                select_proveedores +=`<option value="`+element.id_proveedor+`">`+element.proveedor+`</option>`;
            });

            select_proveedores += `</select>`;

            // CREO SELECTOR DE GRUPOS DINAMICO
            var select_grupo = "";
            select_grupo =     `<label for="grupo">Grupo: </label>
                                    <select class="form-control` + (  obj.grupos.length > 0 ? '' : 'disabled') + `" name="grupo" id="grupo" style="width: 100%" placeholder="grupo" ` + (  obj.grupos.length > 0 ? '' : 'disabled="disabled"') + `>
                                        <option value="-1" disabled selected>Seleccione Variable</option>
                                        <option value="OTRO">OTRO</option>`;

            obj.grupos.map(function(element,index){
                select_grupo +=`<option value="`+element.grupo+`">`+element.grupo+`</option>`;
            });
            
            select_grupo += `</select>`;

            //INSERTO SOBRE HTML LOS SELECTORES
            $('#grupo_container').html(select_grupo);
            $("#grupo_container option[value='" + obj.template[0].grupo + "']").attr("selected", true);
            $('#variables_select_container').html(select_variables);
            $('#proveedor_container').html(select_proveedores);
            $("#proveedor option[value='" + obj.template[0].proveedor + "']").attr("selected", true);

            $("a#create_variable").data("variable_exists", obj.variables.length);
        }
    });
});

//abro modal edicion de email 
$(document).on('click', 'a.btnFormEmailTemplate', function(){
    
    var template_id = $(this).data('template');
    var template_type =  $('input[name="template_type"]').val();

 
    $.ajax({
        type: "GET",
        url: base_url + 'whatsapp/Templates/getTemplate/' + template_id + '/'+ template_type,
        dataType: 'json',
        success: function (response) {
 
            var obj = jQuery.parseJSON(JSON.stringify(response));

            //agrego datos en modal email template cuando se trata de una edicion
            $('#canal_email').val(obj.template_email[0].canal.split(','));
            $('#canal_email').select2({
                placeholder: '.: Selecciona los criterios :.',
                multiple : true
            });
            
            $('input[name="template_id_email"]').val(obj.template_email[0].id);
            $('input[name="nombre_logica"]').val(obj.template_email[0].nombre_logica);
            $('input[name="nombre_template"]').val(obj.template_email[0].nombre_template);
            $("textarea#editor_html").next().children().next().find('#cke_1_contents').find('iframe').attr('id','iframe_editor');
            $("textarea#editor_html").val(obj.template_email[0].html_contenido);
            $("#iframe_editor").contents().find("body").children().html(obj.template_email[0].html_contenido);
            $("div#editor_html").next().children().find('p').text(obj.template_email[0].html_contenido);
            $('#query_contenido').val(obj.template_email[0].query_contenido);
            $("#arreglo_variables_rplc").val(obj.template_email[0].arreglo_variables_rplc);
            //abro modal
            $("#modalEmailTemplate").modal("show");
        }
    });
});

//probar popover 
$(document).on('click', 'a.btnpopover', function(){
    var template_id = $(this).data('template_id');
    var html = `<div class="row template_render_popover_container">
                    <div class="form-group">
                   
                        <div class="col-md-10" style="padding-right:25px;"> 
                            <input placeholder="Documento" style="width:100%;" type="text" name="documento_render_` + $(this).data('template_id') + `" class="form-control" value="" >
                        </div>
                        <div class="col-md-1" style="margin-left:-20px;">
                            <a class="btn btn-info btn_render_template" data-idTemplate="` + $(this).data('template_id') + `" ><i class="fa fa-send"></i></a> &nbsp;
                        </div>
                    </div>
                </div>`;

    $(".popover-content").css("background-color","white");
    $(".popover-content").css({
        color: "#000000",
        height: "70px",
        width: "250px",
        border: "solid 1px white"  
    });
    $('')

    $('[data-toggle="popover"]').popover({
        container: 'body',
        sanitize: false,
        html: true,
        content : html
    }); 
});

//renderizo email 
$(document).on('click', 'a.btn_render_template', function(){
    var id_template = $(this).data('idtemplate');
    var documento = $('input[name="documento_render_' + id_template + '"]').val();

    if(documento == '') {
        Swal.fire('', '<h4>Debe ingresar un numero de documento.</h4>', 'warning');
    } else {
        $.ajax({
                    type: "GET",
                    url: base_url + 'whatsapp/Templates/documentoExits/' + documento,
                    contentType: false,
                    processData:false,
                    dataType: 'json'
                }).done(function(exists){
                    obj = jQuery.parseJSON(JSON.stringify(exists));
                
                    if(obj.data.documento != 1){
                        Swal.fire('', '<h4>Debe ingresar un numero de documento valido.</h4>', 'warning');         
                    } else {
                
                        var formData = new FormData();
                        formData.append("id_template_mail", id_template);
                        formData.append("documento", documento);

                        $.ajax({
                            url: base_url + "api/solicitud/agendaMailTemplateHtml",
                            method:"POST",
                            data: formData,
                            contentType: false,
                            processData:false
                        }).done(function(response) {
                            $("#render_template_container").html(response.message);
                            $("#modalRenderEmailTemplate").modal('show');
                            $("div.wrapper").css("min-height","0%");
                        });
                    }
                });
    }
});

//obtengo los datos de la variable de un template
$(document).on('change', '#variable', function(){
    var variable = $(this).val();
    var template_id = $('input[name="template_id"]').val();
    restart_variables_edicion();
    $.ajax({
        type: "GET",
        url: base_url + 'whatsapp/Templates/getVariable/' + template_id + "/" + variable,
        dataType: 'json',
        success: function (response) {
            var obj = jQuery.parseJSON(JSON.stringify(response));
            $("#tipo_variable").parent().css({display:"block"});

            //falta ver porque no cambia la seleccion sobre la vista pero si sobre el codigo
            if(obj.variable[0].tipo == 1){
                $("#tipo_variable option[value='" + obj.variable[0].tipo + "']").attr("selected", true);
                $("#tipo_variable option[value='" + 2 + "']").attr("selected", false);
            } else {
                $("#tipo_variable option[value='" + obj.variable[0].tipo + "']").attr("selected", true);
                $("#tipo_variable option[value='" + 1 + "']").attr("selected", false);
            }
            //falta ver porque no cambia la seleccion sobre la vista pero si sobre el codigo
            $("#campo").parent().parent().css({display:"block"});
            $('input[name="campo"]').val(obj.variable[0].campo);

            if(obj.variable[0].tipo  == 1){
                $("#condicion").parent().parent().css({display:"block"});
                $('input[name="condicion"]').val(obj.variable[0].condicion);
                $("#formato").parent().parent().css({display:"block"});
                $('input[name="formato"]').val(obj.variable[0].formato);      
            } 
        }
    });
});

//Muestro los campos de acuerdo al tipo de variable 
$(document).on('change', '#tipo_variable', function(){
    var tipo = $(this).val();

    if(tipo == 1){
        $("#condicion").parent().parent().css({display:"block"});
        $("#formato").parent().parent().css({display:"block"});
    } else {
        $("#condicion").parent().parent().css({display:"none"});
        $("#formato").parent().parent().css({display:"none"});    
    }
});

// ---------------------------------------------------------------------------

//obtengo la data de los listados de los templates por tipo
function templateList(type) {
    
    var base_url = $('input#base_url').val();
    $('input[name="template_type"]').val(type);

    $.ajax({
        type: "GET",
        url:  base_url + 'whatsapp/Templates/templateList/' + type,
        success: function (response) {

            if(type != 'email'){
                $('input[name="test_number"]').attr('type','text');
                $('input[name="test_email"]').attr('type','hidden');
                
                //borro leyenda de numero 
                $("#ok_number").text("");
                $("#ok_number").parent().css("background-color", "#FFFFFF")
            } else {
                $('input[name="test_number"]').attr('type','hidden');
                $('input[name="test_email"]').attr('type','text');
            }

            $('#test_title_container').css({display:'block'});
            $('#test_inputs_container').css({display:'block'});
            $('#title_list').text('Templates ' + type);
            $('#button_create_template_container').css({display:'block'});
            
            $('#template_list_container').html(response);
        }
    });
}

// descheckeo todos los selectores del listado y dejo solamente el que seleccione
$(document).on('change', '.template_checkbox', function(){

    $('.template_checkbox').each(function(value, index) {
        if(!$(this).is(':checked')){
            $('.template_checkbox').prop( "checked", false );
        }
    });

    $(this).prop( "checked", true );
});

// abro modal para crear un nuevo template
$(document).on('click', 'a#modal_create_template', function(){
    var template_type =  $('input[name="template_type"]').val();
    restart_modal();
    if(template_type != 'email'){

        $("#test_by_documento").css({display:"none"});

        $('input[name="template_id"]').val("");
        $('input[name="template_email_id"]').val("");
        $('#modalTemplate').modal('show');
    
        $("textarea#msg_string").val("");
        $("#tipo_template").val("");
        $("#grupo").val("");
        $('#canal').val([]);
        $('#canal').select2({
            placeholder: 'Seleccione el canal',
            multiple : true
        });  
        
        $.ajax({
            type: "GET",
            url: base_url + 'whatsapp/Templates/getCreateData/' + template_type ,
            dataType: 'json',
            success: function (response) {
                var obj = jQuery.parseJSON(JSON.stringify(response));
                // CREO SELECTOR DE PROVEEDORES DINAMICO
                var select_proveedores = "";
                select_proveedores =     `<label for="variable">Proveedor: </label>
                                            <select class="form-control` + (  obj.proveedores.length > 0 ? '' : 'disabled') + `" name="proveedor" id="proveedor" style="width: 100%" placeholder="Canal" ` + (  obj.proveedores.length > 0 ? '' : 'disabled="disabled"') + `>
                                            <option value="-1" disabled selected>Seleccione Variable</option>`;

                obj.proveedores.map(function(element,index){
                    select_proveedores +=`<option value="`+element.id_proveedor+`">`+element.proveedor+`</option>`;
                });

                select_proveedores += `</select>`;

                // CREO SELECTOR DE GRUPOS DINAMICO
                var select_grupo = "";
                select_grupo =     `<label for="grupo">Grupo: </label>
                                        <select class="form-control` + (  obj.grupos.length > 0 ? '' : 'disabled') + `" name="grupo" id="grupo" style="width: 100%" placeholder="grupo" ` + (  obj.grupos.length > 0 ? '' : 'disabled="disabled"') + `>
                                            <option value="-1" disabled selected>Seleccione Variable</option>
                                            <option value="OTRO">OTRO</option>`;

                obj.grupos.map(function(element,index){
                    select_grupo +=`<option value="`+element.grupo+`">`+element.grupo+`</option>`;
                });
                
                select_grupo += `</select>`;

                //INSERTO SOBRE HTML LOS SELECTORES
                $('#grupo_container').html(select_grupo);
                $('#proveedor_container').html(select_proveedores);
            }
        });
        
    } else {
        
        $('input[name="template_id_email"]').val("");
        $('#canal_email').val([]);
        $('#canal_email').select2({
            placeholder: 'Seleccione el canal',
            multiple : true
        });
        $('#modalEmailTemplate').modal('show');
    }
  
});

//switch de select por input grupos
$(document).on('change', '#grupo', function() {
    if($(this).val() == 'OTRO'){
        $('#new_group_container').css('display','block');
        $('#grupo_container').css('display','none');
    }
});

//switch to select group
$(document).on('click', 'a#return_select_group', function(){
        $('#new_group_container').css('display','none');
        $('#grupo_container').css('display','block');
})

//reinicio los campos del modal 
function restart_variables_edicion(){
    $("#tipo_variable").parent().css({display:"none"});
    $("#campo").parent().parent().css({display:"none"});  
    $("#condicion").parent().parent().css({display:"none"});
    $("#formato").parent().parent().css({display:"none"});  
    var campo = $('input[name="campo"]').val("");
    var condicion = $('input[name="condicion"]').val("");
    var formato = $('input[name="formato"]').val("");
}

//reseteo inputs del modal cuando lo abro tanto para edicion como para creacion
function restart_modal(){
    var template_type =  $('input[name="template_type"]').val();

    if(template_type != 'email') {
        $("#tipo_variable").parent().css({display:"none"});
        $("#campo").parent().parent().css({display:"none"});  
        $("#condicion").parent().parent().css({display:"none"});
        $("#formato").parent().parent().css({display:"none"});  
        $("#variable_container").remove();
        $(".variables_container").remove();
        
        $("a#create_variable").data("variable_exists", 0);

        //reseteo los parametros del div e input que contiene la prueba del template en modal
        $('#test_render_template').parent().parent().css({display:"none"});
        $('input[name="documento"]').val("");

        var campo = $('input[name="campo"]').val("");
        var condicion = $('input[name="condicion"]').val("");
        var formato = $('input[name="formato"]').val("");
    } else {
        
        $( 'select[name="canal_email[]"]' ).val("");
        $("textarea#arreglo_variables_rplc").val("");
        $("textarea#query_contenido").val("");
        $('input[name="nombre_logica"]').val("");
        $('input[name="nombre_template"]').val("");
        $("div#editor_html").next().children().find('p').text("");
    }
    
}

//creo campos para crear una nueva variable
$(document).on("click", '#create_variable', function(){

    var variable_exists = $(this).data('variable_exists');
    variable_exists++;
    $(this).data("variable_exists",  variable_exists);

    var formData = new FormData();
    formData.append("variable_exist", variable_exists);

    $.ajax({
        type: "POST",
        url: base_url + 'whatsapp/Templates/createVariablesList',
        data: formData,
        contentType: false,
        processData:false
    }).done(function(response){
        $("#container_new_variables").append(response);
    });
});

$(document).on("change", 'select[name="values_tipo_variable[]"]', function(){
    var variable_id =  $(this).parent().parent().parent().parent().find("div.container_inputs");
    var value = $(this).val();

    if(value != 2){
        variable_id.find("div.container_condicion").css({display:"block"})
        variable_id.find("div.container_formato").css({display:"block"})
    } else {
        variable_id.find("div.container_condicion").css({display:"none"})
        variable_id.find("div.container_formato").css({display:"none"})
    }
});

//agrego leyenda para que el usuario sepa el fomrato del numero que debe ingresar
function ok_number(supplier){
    if(supplier == 10 || supplier == 7){
        $("#ok_number").text("numero sin codigo pais. Ejemplo: 3024455997");
    } else{
        $("#ok_number").text("numero completo. Ejemplo: +5491187568745");
    }
    $("#ok_number").parent().css("background-color", "#FFF87F")
}

//testeo un template con un numero en particular
$(document).on('click', 'a#test_template_with_number', function(){
    
    var id_template = "";
    var template = "";
    var number = $('#test_number').val();
    var documento = $('input[name="test_documento"').val();
    var template_type = $('input[name="template_type"]').val();
    var email = $('input[name="test_email"]').val();


   
    //obtengo el documento del usuario con el el numero telefonico seteado
    param_number = encodeURIComponent(number);
    
    if(documento == "") {
        Swal.fire('', '<h4>Debe ingresar un numero de documento.</h4>', 'warning');
    }
    if(template_type != 'email') {
        
        if(number == "") {
            Swal.fire('', '<h4>Debe ingresar un numero de Telefono.</h4>', 'warning');
        }
        var obj;

        var status = $('input[name="template_checkbox[]"]:checked').data('status');
        if(status == 0){
            Swal.fire('', '<h4>El templates esta inactivo</h4>', 'warning');
        } else {

            $.ajax({
                type: "GET",
                url: base_url + 'whatsapp/Templates/documentoExits/' + documento,
                contentType: false,
                processData:false,
                dataType: 'json'
            }).done(function(exists){
                    obj = jQuery.parseJSON(JSON.stringify(exists));
                
                    if(obj.data.documento != 1){
                        Swal.fire('', '<h4>Debe ingresar un numero de documento valido.</h4>','warning');         
                    } else {

                        var arr = $('input[name="template_checkbox[]"]:checked').map(function(){
                            return 'id|' + this.value + '|template|' + $(this).data('template-message') + '|canal|' + $(this).data('canal') + '|proveedor|' + $(this).data('proveedor') 
                        }).get();

                        if(arr.length < 1){
                            Swal.fire('', '<h4>Debe seleccionar un template.</h4>', 'warning');   
                        } 

                        var templates_data = arr.join('|').split('|');
                        templates_data.map(function(element,index){

                        if(element == 'id'){
                            id_template = templates_data[index+1];    
                        }

                        if(element == 'template'){
                            //traduzco variables del template
                            $.ajax({
                                    type: "GET",
                                    url: base_url + 'whatsapp/Templates/testUnitTemplate/' + documento,
                                    contentType: false,
                                    processData:false,
                                }).done(function(response){

                                    $.ajax({
                                            type: "GET",
                                            url: base_url + 'mensaje/maker/' + id_template + '/' + response,
                                            contentType: false,
                                            processData:false,
                                    }).done(function(mensaje){
                                        
                                        template = mensaje.message; 

                                        if(templates_data[index+2] == 'canal')
                                        {   
                                            var supplier  = templates_data[index+5].split(',');
                                            ok_number(supplier);
                                            var url = "";

                                            if(templates_data[index+3].split(',').length == 2) {

                                                var canales = templates_data[index+3].split(',');
                                            
                                                canales.map(function(value, index){
                                                    makeUrlAndSendTemplate(canales[index], id_template, number, template, template_type, supplier);
                                                });

                                            } else {
                                                makeUrlAndSendTemplate(templates_data[index+3], id_template, number, template, template_type, supplier);
                                            }
                                        }
                                    });
                                });
                            }
                        });
                    }
                });
            }
        } else {
            if(email == "") {
                Swal.fire('', '<h4>Debe ingresar un email</h4>', 'warning');
            } else {
                var obj;
                var id_logica = $('.template_checkbox').data('id_logica');
                id_template = $('input:checkbox[name="template_checkbox[]"]:checked').val();
  
                $.ajax({
                    type: "GET",
                    url: base_url + 'whatsapp/Templates/documentoExits/' + documento,
                    contentType: false,
                    processData:false,
                    dataType: 'json'
                }).done(function(exists){
                        obj = jQuery.parseJSON(JSON.stringify(exists));
                    
                        if(obj.data.documento != 1) {
                            Swal.fire('', '<h4>Debe ingresar un numero de documento valido.</h4>', 'warning');         
                        } else {
                            makeUrlAndSendEmailTemplate(id_template, documento, email, id_logica);
                        }
                });
            }
        }
});

// crea URL y envia los datos al controller correspondiente
function makeUrlAndSendTemplate(canal = false, id_template = false, number = false, template = false, template_type = false, supplier = false) {
    
    var url = "";
    formData = new FormData();

    switch (template_type) {
        case 'WAPP':
            if(canal ==  '1'){
                url =  base_url + 'comunicaciones/Twilio/send_template_message_new/';
            } else {
                url =  base_url + 'comunicaciones/TwilioCobranzas/send_template_message_new/';
            }
            formData.append("test_template", true);
        break;
        case 'sms':
            url =  base_url + 'sendSms';
        break;
        case 'ivr':
            url =  base_url + 'sendIvr';
        break;
        default:
        break;
    }
    
    formData.append("id_template",id_template);
    formData.append("phoneN", number);
    formData.append("Template",template);
    formData.append("supplier",supplier);
  
    $.ajax({
            type: "POST",
            url:  url,
            data: formData,
            contentType: false,
            processData:false,
            dataType:"json"
        }).done(function(response){
                    switch (template_type) {
                        case 'WAPP':
                            if(response.template == true) {
                                Swal.fire('', '<h4>EL template: ' + id_template + ' fue enviado correctamente</h4>', 'success');
                            
                            } else {
                                Swal.fire('', '<h4>Fallo el envio del template: ' + id_template + ' </h4>', 'warning');
                            }
                        break;
                        default:
                            if(response == 200) {
                                Swal.fire('', '<h4>EL template: ' + id_template + ' fue enviado correctamente</h4>', 'success');
                            
                            } else {
                                Swal.fire('', '<h4>Fallo el envio del template: ' + id_template + ' </h4>', 'warning');
                            }
                        break;
                    }
            });
}

// crea URL y envia los datos a ApiSolicitud
function makeUrlAndSendEmailTemplate(id_template = false, documento = false, mail = false, id_logica = false) {
    
    var url = base_url + "api/solicitud/enviarMailAgendaPepipost";

    $.ajax({
            type: "POST",
            url:  url,
            data:{ id_template: id_template, documento: documento, mail: mail, id_logica: id_logica },
            success: function (response) {
                var obj = jQuery.parseJSON(JSON.stringify(response)); 
                
                    if(obj.status == 200 && $.trim(obj.data) == 'Email Enviado') {
                        Swal.fire('', '<h4>EL template: ' + id_template + ' fue enviado correctamente a ' + mail + '</h4>','success');
                    } else {
                        Swal.fire('', '<h4>Fallo el envio del template: ' + id_template + ' a ' + mail + ' </h4>', 'warning');
                    }
                }
        });
}

//guardo los datos del template y variables
$(document).on('click', 'a#guardar_template, a#guardar_email_template', function() {

    var modal = $(this).attr('id');
    var formData = new FormData();
    
    var template_type = $('input[name="template_type"]').val();


    formData.append("template_type", template_type);

    if(modal != 'guardar_email_template') {

        //datos del template
        var template_id = $('input[name="template_id"]').val();
        var tipo_template = $( 'select[name="tipo_template"]' ).val();
        var proveedor = $( 'select[name="proveedor"]' ).val();
        var msg_string =  $("textarea#msg_string").val();

        var canal = $( 'select[name="canal[]"]' ).val();
        canal = canal.toString();

        var grupo = $('select[name="grupo"]' ).val();
        if(grupo == 'OTRO'){
            grupo = $('input[name="new_grupo"]').val();
        }



        formData.append("template_id", template_id);
        formData.append("canal", canal); 
        formData.append("tipo_template", tipo_template);
        formData.append("proveedor", proveedor);
        formData.append("grupo", grupo);
        formData.append("msg_string", msg_string);

        
        //datos de la variable
        var variable =  $( 'select[name="variable"]' ).val();
        var tipo_variable = $( 'select[name="tipo_variable"]' ).val();
        var campo = $('input[name="campo"]').val();
        var condicion = $('input[name="condicion"]').val();
        var formato = $('input[name="formato"]').val();

        formData.append("variable", variable);
        formData.append("tipo_variable", tipo_variable);
        formData.append("campo", campo);
        formData.append("condicion", condicion);
        formData.append("formato", formato);

        //datos de las nuevas variables
        var variables_id = [];
        $('.variables_id').each(function(){ 
            variables_id.push($(this).val());
        });
        
        var values_tipo_variable = [];
        $('.values_tipo_variable').each(function(){ 
            values_tipo_variable.push($(this).val());
        });

        var values_campo = [];
        $('.values_campo').each(function(){ 
            values_campo.push($(this).val());
        });

        var values_condicion = [];
        $('.values_condicion').each(function(){ 
            values_condicion.push($(this).val());
        });

        var values_formato = [];
        $('.values_formato').each(function(){ 
            values_formato.push($(this).val());
        });

        formData.append("variables_id", variables_id);
        formData.append("values_tipo_variable", values_tipo_variable);
        formData.append("values_campo", values_campo);
        formData.append("values_condicion", values_condicion);
        formData.append("values_formato", values_formato);
    } else {
        var template_id_email = $('input[name="template_id_email"]').val();
        var canal = $( 'select[name="canal_email[]"]' ).val();
        var arreglo_variables_rplc =  $("textarea#arreglo_variables_rplc").val();
        var query_contenido = $("textarea#query_contenido").val();
        var nombre_logica = $('input[name="nombre_logica"]').val();
        var nombre_template = $('input[name="nombre_template"]').val();
        var html_contenido = "";
        if(template_id != '' || template_id != null) {
            html_contenido  = CKEDITOR.instances['editor_html'].getData();
        } else {
            var html_contenido =  $("textarea#editor_html").val();
        }

        formData.append("template_id", template_id_email);
        formData.append("canal_email", canal); 
        formData.append("arreglo_variables_rplc", arreglo_variables_rplc); 
        formData.append("query_contenido", query_contenido); 
        formData.append("nombre_logica", nombre_logica); 
        formData.append("nombre_template", nombre_template);
        formData.append("html_contenido", html_contenido);
    }

    $.ajax({
        type: "POST",
        url: base_url + 'saveTemplate',
        data: formData,
        contentType: false,
        processData:false,
        }).done(function(response){
            if(template_type != 'email') {
                //cierro modal WAPP, SMS o IVR
                $("div#modalTemplate").modal("hide");
                //refresco listado  WAPP, SMS o IVR
                templateList(template_type);
            } else {
                //cierro modal email
                $("div#modalEmailTemplate").modal("hide");
                //refresco listado email
                Swal.fire('', '<h4>' + (response == '200') ? 'El Template fue cargado correctamente' : response + '</h4>', 'warning');
                templateList(template_type); 
            }
        });
});

//eliminacion de variable sobre edicion
$(document).on("click", '.button_delete_variable', function(){
    $(this).parent().parent().parent().parent().remove();
});

// testeo de template/variables sin envio
$(document).on('click', 'a#test_template_by_documento', function(){
    var documento = $('input[name="documento"]').val();
    var template_id = $('input[name="template_id"]').val();

    $.ajax({
        type: "POST",
        url: base_url + 'whatsapp/Templates/testUnitTemplate/' + documento,
        contentType: false,
        processData:false,
        }).done(function(response){
            if(response == 'false'){
                $('#test_render_template').parent().parent().css({display:"block"});
                $('#test_render_template').html(`<p class="card-text">Debe ingresar un documento de un usuario que tenga una solicitud.</p>`);
            }
            $.ajax({
                        type: "GET",
                        url: base_url + 'mensaje/maker/' + template_id + '/' + response,
                        contentType: false,
                        processData:false,
            }).done(function(mensaje){
                $('#test_render_template').parent().parent().css({display:"block"});
                $('#test_render_template').html(`<p class="card-text">`+ mensaje.message +`<p>`);
            });
        });
});

//collaps editor htmly query en modal de edicion y creacion de template email
$(document).on('click', 'a#collapseEditor', function(){
    if($('#editor_container').hasClass('collapse_editor')){
        $('#editor_container').slideDown('slow');
        $('#editor_container').removeClass('collapse_editor');

    } else {
        $('#editor_container').slideUp('slow');
        $('#editor_container').addClass('collapse_editor');
    }
});
