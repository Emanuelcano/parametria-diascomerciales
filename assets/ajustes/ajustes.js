
$('document').ready(function() {
    $("#result").hide();
    $("#buscar").on('click', function(event){
        let data = $('#search').val();
        if(data.length > 0){
            buscarSolicitud(data);
        } else {
            Swal.fire({
                title:'Clave de busqueda invalida',
                text: 'Ingrese un ID',
                icon: 'warning'
            });
        } 
    });

    $("#reset").on('click', function(event){
        $('#search').val('');
        $("#result").hide();
        $("#slt_banco").val("");
        $("#slt_tipo_cuenta").val("");
        $("#inp_numero_cta").val("");
        $("#inp_fecha_apertura").val("");
        $("#section_table_solicitud_ajustes").show();
        $("#table-solicitud-ajustes").DataTable().ajax.reload();
    });

    $("#actualizar-paso").on('click', function(event){
        actualizar_paso();
    });

    $("#actualizar-estado").on('click', function(event){
        actualizar_estado();
    });

    $("#actualizar-situacion").on('click', function(event){
        actualizar_situacion();
    });

    $("#actualizar-telefono").on('click', function(event){
        actualizar_telefono();
    });

    $("#anular-telefono").on('click', function(event){
        anular_telefono();
    });

    $("#actualizar-rechazo").on('click', function(event){
        actualizar_rechazo();
    });
    $("#resignar-solicitud").on('click', function(event){
        reasignar();
    });
    $("#resignar-datospers").on('click', function(event){
        reasignar_datospers();
    });
    $("#ampliar-cupo").on('click', function(event){
        actualizar_cupo();
    });

    $('#search').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $("#buscar").click();
        }
    });

    ajustes_var = []
    getTableAjustes();
    ajustes_var.myInt = setInterval(getTableAjustes, 60000);
        
});


function buscarSolicitud(solicitud) {
    
    let base_url = $("#base_url").val();
    $("#actualizar-situacion").addClass('disabled');
    $("#actualizar-paso").addClass('disabled');
    $("#actualizar-estado").addClass('disabled');
    $("#actualizar-telefono").addClass('disabled');
    $("#actualizar-rechazo").addClass('disabled');
    $("#resignar-solicitud").addClass('disabled');
    $("#id_solicitud").val('0');
    $.ajax({
        url: base_url+'api/ajustes/buscar/'+solicitud,
        type: 'GET',
        dataType: 'json'
    })
    .done(function(response) {

        
        if(response.status.ok) {

            let table_ajustes = "";            
            for (var i = 0; i < response.sol_ajustes.length; i++) {
                resp_aj = response.sol_ajustes[i]
                table_ajustes += "<tr id='sol_ajuste_"+resp_aj.id+"'>";
                table_ajustes += "  <td style='width : 10%;'>"+resp_aj.fecha_solicitud+"</td>";
                table_ajustes += "  <td style='width : 5%;'>"+resp_aj.id_solicitud+"</td>";
                table_ajustes += "  <td style='width : 10%;'>"+resp_aj.name_operador+"</td>";
                table_ajustes += "  <td style='width : 7%;'>"+resp_aj.descrip_tipo+"</td>";
                table_ajustes += "  <td style='width : 8%;'>"+resp_aj.descrip_clase+"</td>";
                table_ajustes += "  <td style='width : 8%;'>"+resp_aj.descripcion+"</td>";

                ajustes_var.save = (( resp_aj.id_operador_procesa == null ) ? false : true )
                // ajustes_var.hidden = ((ajustes_var.save) ? ' hidden ': ' ' )
                input_ajust_obserba = '<input type="text" name="ajust_obserba" id="ajust_obserba" class="form-control input-sm '+((ajustes_var.save) ? 'hidden': ' ' )+'"></input>';
                switch (resp_aj.estado) {
                    case '0': table_ajustes += '<td style="width:30%;"><span class="'+((!ajustes_var.save) ? 'hidden': ' ' )+'">POR PROCESAR</span>'+input_ajust_obserba+'</td>';   break;
                    case '1': table_ajustes += '<td style="width:30%;"><span class="'+((!ajustes_var.save) ? 'hidden': ' ' )+'">PROCESADO</span>'+input_ajust_obserba+'</td>';      break;
                    case '2': table_ajustes += '<td style="width:30%;"><span class="'+((!ajustes_var.save) ? 'hidden': ' ' )+'">ANULADO</span>'+input_ajust_obserba+'</td>';        break;
                    case '3': table_ajustes += '<td style="width:30%;"><span class="'+((!ajustes_var.save) ? 'hidden': ' ' )+'">NO VALIDA</span>'+input_ajust_obserba+'</td>';      break;
                }
                table_ajustes += '  <td style="width : 15%;"><span class="'+((!ajustes_var.save) ? 'hidden': ' ' )+'">'+ ((resp_aj.observaciones == null) ? '': resp_aj.observaciones) +'</span> '+
                                '    <select name="new_estado" id="new_estado" onchange="select_changed_ajustes(this)" class="form-control input-sm '+((ajustes_var.save) ? 'hidden': ' ' )+'">' +
                                '       <option value="none" selected disabled hidden>Seleccione</option>' +
                                '       <option value="PROCESADO">PROCESADO</option>' +
                                '       <option value="NO PROCESADO">NO PROCESADO</option>' +
                                '       <option value="SOLICITUD INCOMPLETA">SOLICITUD INCOMPLETA</option>' +
                                '       <option value="NO CORRESPONDE">NO CORRESPONDE</option>' +
                                '    </select></td>';

                table_ajustes += "  <td><button class='btn btn-success btn-sm hidden' id='btn_ajustes_procesar' data-id='"+resp_aj.id+"' onclick='btn_ajustes_procesar(this)'><i class='fa fa-thumbs-up'></i></button>";
                table_ajustes += "  <button class='btn btn-danger btn-sm hidden' id='btn_ajustes_noprocesar' data-id='"+resp_aj.id+"' onclick='btn_ajustes_noprocesar(this)'><i class='fa fa-thumbs-down'></i></button>";
                table_ajustes += "  <button class='btn btn-info btn-sm "+((!ajustes_var.save) ? 'hidden': ' ' )+"' id='btn_action_ajustes' data-open=0 data-id='"+resp_aj.id+"' onclick='btn_action_ajustes(this)'><i class='fa fa-gear'></i></button></td>";
                table_ajustes += "</tr>";
            }
            $("#datos-solicitud-ajustes tbody").html(table_ajustes);

            let table = "<tr><td>"+response.solicitud.id+"</td><td>";
                table += (typeof(response.solicitud.documento) != 'undefined' && response.solicitud.documento != null)? response.solicitud.documento:'';
                table += "</td><td>"+ response.solicitud.nombres+" ";
                table +=  (typeof(response.solicitud.apellidos) != 'undefined' && response.solicitud.apellidos != null)? response.solicitud.apellidos:'';
                table += "</td><td>";
                table += (typeof(response.solicitud.nombre_situacion) != 'undefined' && response.solicitud.nombre_situacion != null)? (response.solicitud.nombre_situacion):'';
                table += "</td><td>"+
                response.solicitud.paso+"</td><td>";
                table +=(typeof(response.solicitud.estado) != 'undefined' && response.solicitud.estado != null)? (response.solicitud.estado):'';
                table += "</td><td>"+
                response.solicitud.tipo_solicitud+"</td></tr>";

            $("#datos-solicitud tbody").html(table);
            $("#paso-actual").html(response.solicitud.paso);
            $("#estado-actual").html((typeof(response.solicitud.estado) != 'undefined' && response.solicitud.estado != null)? (response.solicitud.estado):'');
            $("#situacion-actual").html((typeof(response.solicitud.nombre_situacion) != 'undefined' && response.solicitud.nombre_situacion != null)? (response.solicitud.nombre_situacion):'');
            $("#telefono-actual").html((typeof(response.solicitud.telefono) != 'undefined' && response.solicitud.telefono != null)? (response.solicitud.telefono):'');
            $("#levantar-rechazo").html((typeof(response.solicitud.estado) != 'undefined' && response.solicitud.estado != null)? (response.solicitud.estado):'');
            $("#nombre-usuario").html((typeof(response.nombre_operador) != 'undefined' && response.nombre_operador != null)? (response.nombre_operador):'');
            
            $("#fix_sol_nombre").val((typeof(response.solicitud.nombres) != 'undefined' && response.solicitud.nombres != null)? (response.solicitud.nombres):'');
            $("#fix_sol_apellido").val((typeof(response.solicitud.apellidos) != 'undefined' && response.solicitud.apellidos != null)? (response.solicitud.apellidos):'');

            $('#anular-telefono').addClass('hidden')

            if (response.solicitud.paso == 2) 
                $('#anular-telefono').removeClass('hidden');

            if(typeof(response.beneficios) != 'undefined' && response.beneficios != null)
            {
                $("#cupo-actual").html(formatNumber(response.beneficios.disponible));
                let max = parseInt(response.beneficios.max);
                let incre = parseInt(response.beneficios.incremento);
                let ini = parseInt(response.beneficios.ini);
                
                    let html ='<option value="" selected disabled>Seleccione</option>';
                    for (let index = ini; index <= max; index+=incre) {
                        html +='<option value="'+index+'">'+formatNumber(index)+'</option>'
                    }
                    $("#cupos").html(html);
                    $("#cupos").prop('disabled', false);
                    if(parseInt(response.beneficios.plazo) > 0)
                        $("#plazos").prop('disabled', false);

                    $("#plazos").data('plazo-actual', response.beneficios.plazo);
                    $("#plazos").val(response.beneficios.plazo);
                
                

            } else {
                $("#cupos").prop('disabled', true);
                $("#cupo-actual").html('');
                $("#cupos").html('<option value="" selected disabled>Seleccione</option>');
            }

            $("#id_solicitud").val(response.solicitud.id);
            if(response.solicitud.estado ==='RECHAZADO'){
                $("#actualizar-rechazo").removeClass('disabled');
            }
            //resignar solicitud
            if(response.solicitud.estado !='TRANSFIRIENDO' && response.solicitud.estado !='PAGADO'){
                $("#resignar-solicitud").removeClass('disabled');
            }
            
            if(response.solicitud.paso != 2){ $("#actualizar-telefono").removeClass('disabled'); }
            //llenar select pasos
            let pasos = response.pasos; 
            $('#pasos_disponibles').html('<option value="">Seleccione</option>');      
            $.each(pasos,function( index, value ) {
                $('#pasos_disponibles').append('<option value="'+ value +'">'+ value +'</option>');
                $("#actualizar-paso").removeClass('disabled');
            });

            //llenar select operadores
            let operadores = response.operadores; 
            $('#operadores').html('<option value="0">Al azar</option>');      
            $.each(operadores,function( index, value ) {
                $('#operadores').append('<option value="'+ value.idoperador +'">'+ value.nombre_apellido +'</option>');
            });

            //llenar select estado
            let estados = response.estados;
            $('#estados_disponibles').html('<option value="">Seleccione</option>');        
            $.each(estados,function( index, value ) {
                $('#estados_disponibles').append('<option value="'+ value +'">'+ value +'</option>');
                $("#actualizar-estado").removeClass('disabled');
            });

            //llenar select situacion
            let situaciones = response.situaciones;
            $('#situaciones_disponibles').html('<option value="">Seleccione</option>');       
            $.each(situaciones,function( index, value ) {
                $('#situaciones_disponibles').append('<option value="'+ value.id_situacion +'">'+ value.nombre_situacion +'</option>');
                $("#actualizar-situacion").removeClass('disabled');
            });
            // ************************/
            // *** Validar Cuenta *****/
            // ************************/

            //*** Bancos ***/
            if (response.bancos.length > 0) {
                response.bancos.forEach(banco => {
                    $("#slt_banco").append(`<option value=${banco.id_Banco}>${banco.Nombre_Banco}</option>`);
                });
            }
            //*** Tipo Cuenta ***/
            response.tipos_cuenta.forEach(tipo_cuenta => {
                $("#slt_tipo_cuenta").append(`<option value=${tipo_cuenta.id_TipoCuenta}>${tipo_cuenta.Nombre_TipoCuenta}</option>`);
            });
            if (response.datos_bancarios.length > 0) {
                $("#slt_banco").val(response.datos_bancarios[0].id_Banco);
                $("#slt_tipo_cuenta").val(response.datos_bancarios[0].id_tipo_cuenta);
                $("#inp_numero_cta").val(response.datos_bancarios[0].numero_cuenta);
                $("#cuenta_antigua").val(response.datos_bancarios[0].numero_cuenta);
                $("#tipo_cuenta_antigua").val(response.datos_bancarios[0].Nombre_TipoCuenta);
                $("#banco_antiguo").val(response.datos_bancarios[0].Nombre_Banco);
            }
            
            if (response.solicitud_imagenes.length > 0) {
                let html = "";
                response.solicitud_imagenes.forEach((imagen, index) => {
                    html += `<button class="btn btn-default btn-block" onclick="showimage(this)" data-imgpatch="${imagen.patch_imagen}" >${imagen.etiqueta}</button>`                            
                });
                $("#Ajustes_files > div").html(html);
                
            }

            if(response.verificacion.length > 0 ) { 
                $(".boton-verificacion").html(response.verificacion);

                $("#verificacion").on("click", function (){
                    autorizarVerificacion(solicitud);
                });
            }


            if(response.boton.length > 0 ) { 
                $(".boton-pagare").html(response.boton);

                $("#pagare").on("click", function (){
                    gestionPagare(solicitud);
                });
            }

            if(response.solicitud.paso >= 8) { 
                $("#a_procesar").removeClass('disabled'); 
            } else {
                $("#a_procesar").addClass('disabled');
            }
            /*** Se verifica si al buscar las imágenes de certifiaciones bancarias trae más de una ***/
            if(response.imagenes !== "") {
                if (response.imagenes.length > 1) {
                    let html = "";
                    response.imagenes.forEach((imagen, index) => {
                        html += `
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="form-group has-primary has-feedback">
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #428bca; color: white;"><i class="fa fa-eye"></i></span>
                                            <a class="btn btn-default btn-block" href="${imagen.patch_imagen}" target="_blank">Certificación ${index + 1}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `                            
                    });
                    $("#imagenes").html(html);
                    $("#a_mostrar").removeAttr('href');
                } else {
                    $("#imagenes").html("");
                    $("#a_mostrar").attr('href', response.imagenes[0].patch_imagen);
                }
                $("#a_mostrar").show();
            } else {
                $("#a_mostrar").hide();
            }

            if (response.buro.length > 0) {
                $("#buro").val(response.buro[0].buro);
            }

            $("#section_table_solicitud_ajustes").hide()
            $("#result").show();
        } else {
            Swal.fire({
                title: 'Oops',
                text: response.mensaje,
                icon: 'warning'
            });
        }
        

    })
    .fail(function(response) {
    })
    .always(function(response) {
    });
    
}


function autorizarVerificacion( solicitud){
    let base_url = $("#base_url").val();

    $.ajax({
        url: base_url+'ajustes/verificacion',
        type: 'POST',
        dataType: 'json',
        data:{'id_solicitud':solicitud}
    })
    .done(function(response) {
        if(response.status.ok) {
            Swal.fire('¡Exito!',"Verificacion autorizada", 'success');
            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[AUTORIZA NUEVA VERIFICACION DE IDENTIDAD]</b>";
            
            saveTrack(comment, type_contact, solicitud, id_operador);
        } else {
            Swal.fire('¡Lo sentimos!',"Verificacion no autorizada", 'error');
        }

    })
    .fail(function(response) {
        //console.log("error");
    })
    .always(function(response) {
        //console.log("complete");
    });
}


function gestionPagare( solicitud){
    let base_url = $("#base_url").val();
    let funcion = $("#pagare").data("fun");

    $.ajax({
        url: base_url+'ajustes/gestionPagare',
        type: 'POST',
        dataType: 'json',
        data:{'funcion': funcion, 'id_solicitud':solicitud}
    })
    .done(function(response) {
        if(response.status.ok) {
            Swal.fire('¡Exito!',"Solicitud procesada", 'success');
        } else {
            Swal.fire('Lo sentimos!',"Solicitud rechazada", 'error');
        }

    })
    .fail(function(response) {
        //console.log("error");
    })
    .always(function(response) {
        //console.log("complete");
    });
}

function actualizar_cupo(){
    let base_url = $("#base_url").val();
    let id = $("#id_solicitud").val();
    let plazo = 0;

    let monto = $("#cupos").val();
    if ((monto == '' ||  monto == null)  && ($("#plazos").val() == '' ||  $("#plazos").val() == null)) {
        return false;
    }

    if(monto == '' ||  monto == null){
        monto = $("#cupo-actual").html().replace(/\./g,'');
        monto = monto.replace(/\,/g,'.');
    }

    if ($("#plazos").data('plazo-actual') > 0) {
        plazo = $("#plazos").val();
    }

    $.ajax({
        url: base_url+'api/ajustes/actualizar_cupo',
        type: 'POST',
        dataType: 'json',
        data:{'monto': monto, 'id_solicitud':id, 'plazo':plazo}
    })
    .done(function(response) {

        if(response.status.ok) {
           /* track */
            Swal.fire('¡Exito!', response.mensaje, 'success');
            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[AJUSTE MONTO OFRECIDO]</b>"+ 
            "<br> Monto anterior: " + $("#cupo-actual").html()+
            "<br> Monto nuevo: " +  formatNumber(monto);
            if ($("#plazos").data('plazo-actual') > 0) {
                comment += "<br> Plazo anterior: " +  $("#plazos").data('plazo-actual')+
                            "<br> Plazo nuevo: " +  $("#plazos").val();
            }
            
            saveTrack(comment, type_contact, id, id_operador);
            buscarSolicitud(id);
        } else {
            Swal.fire('Oops', response.mensaje, 'error');
        }
        

    })
    .fail(function(response) {
        //console.log("error");
    })
    .always(function(response) {
        //console.log("complete");
    });
}


function actualizar_paso(){
    

    let base_url = $("#base_url").val();
    let id = $("#id_solicitud").val();
    let paso = $("#pasos_disponibles").val();
    let data = {'id_solicitud':id, 'new_paso': paso};
    $.ajax({
        url: base_url+'api/ajustes/actualizar_paso',
        type: 'POST',
        dataType: 'json',
        data:data
    })
    .done(function(response) {

        if(response.status.ok) {
           /* track */
            Swal.fire({
                title: '¡Exito!',
                text: response.mensaje,
                icon: 'success'
            });
            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[AJUSTE PASO SOLICITUD]</b>"+ 
            "<br> <b>Solicitud :"+id+"</b>"+
            "<br> Campo: Paso"+
            "<br> Valor anterior: " + $("#paso-actual").html()+
            "<br> Valor Nuevo: " +  $("#pasos_disponibles option:selected").text();
            
            saveTrack(comment, type_contact, id, id_operador);
            buscarSolicitud(id);
        } else {
            Swal.fire({
                title: 'Oops',
                text: response.mensaje,
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

function actualizar_estado(){
    

    let base_url = $("#base_url").val();
    let id = $("#id_solicitud").val();
    let estado = $("#estados_disponibles").val();
    let data = {'id_solicitud':id, 'new_estado': estado};
    $.ajax({
        url: base_url+'api/ajustes/actualizar_estado',
        type: 'POST',
        dataType: 'json',
        data:data
    })
    .done(function(response) {

        if(response.status.ok) {
           /* track */
            Swal.fire({
                title: '¡Exito!',
                text: response.mensaje,
                icon: 'success'
            });
            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[AJUSTE ESTADO SOLICITUD]</b>"+ 
            "<br> <b>Solicitud :"+id+"</b>"+
            "<br> Campo: Estado"+
            "<br> Valor anterior: " + $("#estado-actual").html()+
            "<br> Valor Nuevo: " +  $("#estados_disponibles option:selected").text();
            
            saveTrack(comment, type_contact, id, id_operador);
            buscarSolicitud(id);
        } else {
            Swal.fire({
                title: 'Oops',
                text: response.mensaje,
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

function actualizar_situacion(){
    

    let base_url = $("#base_url").val();
    let id = $("#id_solicitud").val();
    let situacion = $("#situaciones_disponibles").val();
    let data = {'id_solicitud':id, 'new_situacion': situacion};
    $.ajax({
        url: base_url+'api/ajustes/actualizar_situacion',
        type: 'POST',
        dataType: 'json',
        data:data
    })
    .done(function(response) {

        if(response.status.ok) {
           /* track */
            Swal.fire({
                title: '¡Exito!',
                text: response.mensaje,
                icon: 'success'
            });
           
            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[AJUSTE SITUACION SOLICITUD]</b>"+ 
            "<br> <b>Solicitud :"+id+"</b>"+
            "<br> Campo: Situación laboral"+
            "<br> Valor anterior: " + $("#situacion-actual").html()+
            "<br> Valor Nuevo: " +  $("#situaciones_disponibles option:selected").text();
            
            saveTrack(comment, type_contact, id, id_operador);
            buscarSolicitud(id);

        } else {
            Swal.fire({
                title: 'Oops',
                text: response.mensaje,
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

function actualizar_telefono(){
    

    let base_url = $("#base_url").val();
    let id = $("#id_solicitud").val();
    let telefono = $("#new-telefono").val();
    let data = {'id_solicitud':id, 'new-telefono': telefono};
    $.ajax({
        url: base_url+'api/ajustes/actualizar_telefono',
        type: 'POST',
        dataType: 'json',
        data:data
    })
    .done(function(response) {

        if(response.status.ok) {
           /* track */
            Swal.fire({
                title: '¡Exito!',
                text: response.mensaje,
                icon: 'success'
            });

            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[AJUSTE TELEFONO SOLICITUD]</b>"+ 
            "<br> <b>Solicitud :"+id+"</b>"+
            "<br> Campo: Teléfono"+
            "<br> Valor anterior: " + $("#telefono-actual").html()+
            "<br> Valor Nuevo: " + telefono;
            
            saveTrack(comment, type_contact, id, id_operador);
            buscarSolicitud(id);
        } else {
            Swal.fire({
                title: 'Oops',
                text: response.mensaje,
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


function anular_telefono(){
    

    let base_url = $("#base_url").val();
    let id = $("#id_solicitud").val();
    let data = {'id_solicitud':id};
    $.ajax({
        url: base_url+'api/ajustes/anular_telefono',
        type: 'POST',
        dataType: 'json',
        data:data
    })
    .done(function(response) {

        if(response.status.ok) {
           /* track */
            Swal.fire({
                title: '¡Exito!',
                text: response.mensaje,
                icon: 'success'
            });

            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[ANULACION TELEFONO]</b>";
            
            saveTrack(comment, type_contact, id, id_operador);
            buscarSolicitud(id);
        } else {
            Swal.fire({
                title: 'Oops',
                text: response.mensaje,
                icon: 'error'
            });
        }

    });
    
}

function actualizar_rechazo(){

    let base_url = $("#base_url").val();
    let id = $("#id_solicitud").val();
    let data = {'id_solicitud':id};
    $.ajax({
        url: base_url+'api/ajustes/levantar_rechazado',
        type: 'POST',
        dataType: 'json',
        data:data
    })
    .done(function(response) {

        if(response.status.ok) {
           /* track */
            Swal.fire({
                title: '¡Exito!',
                text: response.mensaje,
                icon: 'success'
            });
            var hoy = new Date();
            fecha = hoy.getDate()+"-"+(hoy.getMonth()+1)+"-"+hoy.getFullYear()+" "+hoy.getHours()+":"+hoy.getMinutes()+":"+hoy.getSeconds();
            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[RECHAZO LEVANTADO]</b>"+ 
            "<br> <b>Solicitud :"+id+"</b>"+
            "<br> "+
            "<br> fecha y hora: " + fecha;
             
            saveTrack(comment, type_contact, id, id_operador);
            buscarSolicitud(id);
        } else {
            Swal.fire({
                title: 'Oops',
                text: response.mensaje,
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
function reasignar(){
    let base_url = $("#base_url").val();
    let id = $("#id_solicitud").val();
    let operador = $("#operadores").val();
    let data = {'id_solicitud':id, 'operador': operador};
    $.ajax({
        url: base_url+'api/ajustes/reasignar_solicitud',
        type: 'POST',
        dataType: 'json',
        data:data
    })
    .done(function(response) {
        if(response.status.ok) {
           /* track */
            Swal.fire({
                title: '¡Exito!',
                text: response.mensaje,
                icon: 'success'
            });
            var hoy = new Date();
            fecha = hoy.getDate()+"-"+(hoy.getMonth()+1)+"-"+hoy.getFullYear()+" "+hoy.getHours()+":"+hoy.getMinutes()+":"+hoy.getSeconds();
            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[REASIGNAR SOLICITUD]</b>"+ 
            "<br> <b>Solicitud :"+id+"</b>"+
            "<br> "+
            "<br> fecha y hora: " + fecha+
            "<br> Operador anterior: " + response.operador_anterior+
            "<br> Operador asignado: " + response.operador_asignado;
            saveTrack(comment, type_contact, id, id_operador);
            buscarSolicitud(id);
        } else {
            Swal.fire({
                title: 'Oops',
                text: response.mensaje,
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

function saveTrack(comment, typeContact, idSolicitude, idOperator)
{

    $.ajax({
        url: base_url+'api/track_gestion',
        type: 'POST',
        dataType: 'json',
        data: {'observaciones':comment, 'id_tipo_gestion':typeContact, 'id_solicitud':idSolicitude, 'id_operador':idOperator}
    })
    .done(function(response) {
    })
    .fail(function(response)
    {
    })
    .always(function() {
    });
}
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
/*************************************************/
/*** Botón Procesar la validación de la cuenta ***/
/*************************************************/
$("#a_procesar").on('click', function() {
    let numero_cuenta = $("#inp_numero_cta").val();
    let id_banco = $("#slt_banco").val();
    let id_tipo_cuenta = $("#slt_tipo_cuenta").val();
    let fecha_apertura = $("#inp_fecha_apertura").val();
    let id_operador = $("#id_operador").val();
    let nombre_operador = $("#operador_nombre").val();
    let numero_cuenta_ant = $("#cuenta_antigua").val();
    let banco_ant = $("#banco_antiguo").val();
    let tipo_cuenta_ant = $("#tipo_cuenta_antigua").val();
    let nombre_banco = $("#slt_banco option:selected").html();
    let nombre_tipo_cuenta = $("#slt_tipo_cuenta option:selected").html();
    let buro = $("#buro").val();
    let id_solicitud = $("#id_solicitud").val();
    let verificacion_manual = true;

    /*** Se verifica que la fecha de apertura no sea mayor al día actual ***/
    let fechaAlt = new Date();
    let fecha = fecha_apertura.split("-");
    fechaAlt.setFullYear(fecha[0], fecha[1]-1, fecha[2]);
    var today = new Date();

    if (fechaAlt > today) {
        toastr["warning"]("La Fecha no puede ser mayor al día de hoy", "VALIDACIÓN DE CUENTA");
        return null;
    }
    
    /*** Validaciones ***/
    if (id_banco == "") {
        toastr["warning"]("Debe escoger un Banco", "VALIDACIÓN DE CUENTA");
        return null;
    }
    if (id_tipo_cuenta == "") {
        toastr["warning"]("Debe escoger un Tipo de Cuenta", "VALIDACIÓN DE CUENTA");
        return null;
    }
    if (numero_cuenta.length == 0) {
        toastr["warning"]("Debe indicar un Número de Cuenta", "VALIDACIÓN DE CUENTA");
        return null;
    }
    if (id_banco == 4) {
        if (numero_cuenta.length !== 11) {
            toastr["warning"]("El número de cuenta debe tener 11 dígitos", "VALIDACIÓN DE CUENTA");
            return null;
        }
    }
    if (id_banco == 28) {
        if (numero_cuenta.length !== 9) {
            toastr["warning"]("El número de cuenta debe tener 9 dígitos", "VALIDACIÓN DE CUENTA");
            return null;
        }
    }
    if (fecha_apertura.length == 0) {
        toastr["warning"]("Debe indicar una Fecha de Apertura", "VALIDACIÓN DE CUENTA");
        return null;
    }
    /*** Fin Validaciones ***/

    var base_url = $("#base_url").val();
    var data = {
        "id_solicitud": id_solicitud,
        "numero_cuenta": numero_cuenta,
        "id_banco": id_banco,
        "id_tipo_cuenta": id_tipo_cuenta,
        "numero_cuenta_ant": numero_cuenta_ant,
        "banco_ant": banco_ant,
        "tipo_cuenta_ant": tipo_cuenta_ant,
        "id_operador": id_operador,
        "nombre_operador": nombre_operador,
        "nombre_banco": nombre_banco,
        "nombre_tipo_cuenta": nombre_tipo_cuenta,
        "buro": buro,
        "verificacion_manual": verificacion_manual,
        "fecha_apertura": fecha_apertura,
    }
    $.ajax({
        type: "POST",
        url: base_url + "gestion/Galery/actualizarNumeroCuenta",
        data: data,
        dataType: 'json',
        success: function () {
            toastr["success"]("APROBADO", "VALIDACIÓN DE CUENTA"); 
        }
    });
});

btn_action_ajustes = (elem) => {
    raizTr = $(elem).parent().parent();
    if ($(elem).data('open') == 1 ) {
        raizTr.find('td:eq(6) span').removeClass('hidden');
        raizTr.find('td:eq(7) span').removeClass('hidden');
        raizTr.find('#new_estado').addClass('hidden').val('none'); 
        raizTr.find('#ajust_obserba').addClass('hidden').val('');
        raizTr.find("#btn_ajustes_noprocesar").addClass('hidden')
        raizTr.find("#btn_ajustes_procesar").addClass('hidden')
        $(elem).data('open', 0)
    } else if($(elem).data('open') == 0){
        raizTr.find('td:eq(6) span').addClass('hidden');
        raizTr.find('td:eq(7) span').addClass('hidden');
        raizTr.find('#new_estado').removeClass('hidden');
        raizTr.find('#ajust_obserba').removeClass('hidden');
        $(elem).data('open', 1)
    }
}

select_changed_ajustes = (elem) => {
    action = $(elem).find('option:selected').text();
    raizTr = $(elem).parent().parent();
    if (action == 'PROCESADO') {
        raizTr.find("#btn_ajustes_procesar").removeClass('hidden')
        raizTr.find("#btn_ajustes_noprocesar").addClass('hidden')
        raizTr.find("#btn_action_ajustes").addClass('hidden')
        if (ajustes_var.save)
            raizTr.find("#btn_action_ajustes").removeClass('hidden')
    } else {
        raizTr.find("#btn_ajustes_noprocesar").removeClass('hidden')
        raizTr.find("#btn_ajustes_procesar").addClass('hidden')
        raizTr.find("#btn_action_ajustes").addClass('hidden')
        if (ajustes_var.save)
            raizTr.find("#btn_action_ajustes").removeClass('hidden')
    }
    
}

btn_ajustes_procesar = (elem) => {
    let id = $(elem).data('id');   
    raizTr = $(elem).parent().parent();
	swal
		.fire({
			title: "Esta seguro?",
			text: "desea procesar la solicitud de ajustes?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Si, Confirmar"
		})
		.then(function (result) {
			if (result.value) {
				var data = {
					id: id,
                    estado: 1,
                    observaciones: raizTr.find('#ajust_obserba').val(),
                    resultado : raizTr.find('option:selected').text(),
                    fecha_proceso: moment().format('YYYY-MM-DD h:mm:ss a'),
                    id_operador_procesa: $("#id_operador").val()
				};
				var base_url = $("input#base_url").val() + "atencion_cliente/updateSolajustes";
				$.ajax({
					type: "POST",
					url: base_url,
					data: data,
					success: function (response) {
                        Swal.fire('¡Exito!', "Registro Guardado", 'success');
                        let id_operador = $("#id_operador").val();
                        let solicitud = raizTr.find('td:eq(1)').html();
                        let t_contact = 170;
                        let comment = '<b>[AJUSTE PROCESADO]</b>' +
                            '<br>[TIPO] = ' + raizTr.find('td:eq(3)').html() +
                            '<br>[CLASE] = ' + raizTr.find('td:eq(4)').html() +
                            '<br>' + raizTr.find('option:selected').text()

                        saveTrack(comment, t_contact, solicitud, id_operador);
                        buscarSolicitud(raizTr.find('td:eq(1)').html())
					}
				});
			}
		});
}

btn_ajustes_noprocesar = (elem) => {
    
    let id = $(elem).data('id');   
    raizTr = $(elem).parent().parent();

    if (raizTr.find('#ajust_obserba').val() === '') {
		Swal.fire('¡Advertencia!', "Campos incompletos", 'error');
	} else {

        swal
            .fire({
                title: "Esta seguro?",
                text: "desea cancelar la solicitud de ajustes?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, Confirmar"
            })
            .then(function (result) {
                if (result.value) {
                    var data = {
                        id: id,
                        estado: 3,
                        resultado : raizTr.find('option:selected').text(),
                        observaciones: raizTr.find('#ajust_obserba').val(),
                        fecha_proceso: moment().format('YYYY-MM-DD h:mm:ss a'),
                        id_operador_procesa: $("#id_operador").val()
                    };
                    var base_url = $("input#base_url").val() + "atencion_cliente/updateSolajustes";
                    $.ajax({
                        type: "POST",
                        url: base_url,
                        data: data,
                        success: function (response) {
                            Swal.fire('¡Exito!', "Registro Guardado", 'success');
                            let id_operador = $("#id_operador").val();
                            let solicitud = raizTr.find('td:eq(1)').html();
                            let t_contact = 170;
                            let comment = '<b>[AJUSTE NO PROCESADO]</b>' +
                                '<br>[TIPO] = ' + raizTr.find('td:eq(3)').html() +
                                '<br>[CLASE] = ' + raizTr.find('td:eq(4)').html() +
                                '<br>' + raizTr.find('#ajust_obserba').val()

                            saveTrack(comment, t_contact, solicitud, id_operador);
                            buscarSolicitud(raizTr.find('td:eq(1)').html())
                        }
                    });
                }
            });
    }

}

btn_show_ajustes = (elem) => {    
    let id = $(elem).data('id'); 
    buscarSolicitud(id)
}

getTableAjustes = () => {
    
	let ajax = {
		'type': "GET",
		'url': base_url + 'ajustes/get_ajustes',
		dataType: 'json'
	}
	let columns = [
        {
			"data": "fecha_solicitud"
		},
		{
			"data": "id_solicitud"
		},
		{
			"data": "name_operador"
		},
		{
			"data": "descrip_tipo"
		},
		{
			"data": "descrip_clase"
		},
        {
            "data": "descripcion"
        },
		{
			"data": "estado",
            render: (data) => {

				switch (data) {
					case '0': estado = 'POR PROCESAR';   break;
					case '1': estado = 'PROCESADO';      break;
					case '2': estado = 'ANULADO';        break;
					case '3': estado = 'NO VALIDA';      break;
				}
                return estado
            }
		},
		{
			"data": "fecha_proceso"
		},
		{
			"data": "name_operador_procesa"
		},
		{
			"data": "observaciones"
		},
		{
			"data": "resultado"
		},
		{
			"data": null,
            render: (data, type, row) => {
                return "<button class='btn btn-info btn-sm ' id='btn_show_ajustes' data-id='"+row['id_solicitud']+"' onclick='btn_show_ajustes(this)'><i class='fa fa-gear'></i></button></td>"
            }
		},
	]
	TablaPaginada('table-solicitud-ajustes', 2, 'asc', '', '', ajax, columns);
}

reasignar_datospers = () => {
    
    let base_url = $("#base_url").val();
    let id = $("#id_solicitud").val();
    let operador = $("#operadores").val();
    let nombres = $("#fix_sol_nombre").val();
    let apellidos = $("#fix_sol_apellido").val();
    let data = {'id':id, 'nombres': nombres, 'apellidos': apellidos};
    $.ajax({
        url: base_url+'api/ajustes/actualizar_datospers',
        type: 'POST',
        dataType: 'json',
        data:data
    })
    .done(function(response) {
        if(response.status.ok) {
           /* track */
            Swal.fire({
                title: '¡Exito!',
                text: response.mensaje,
                icon: 'success'
            });
            let id_operador = $("#id_operador").val();
            let type_contact = 170;
            let comment = "<b>[AJUSTE NOMBRES APELLIDOS]</b>"+ 
            "<br>Nombre anterior  : "+response.datos.old.nombre +
            "<br>Nuevo nombre     : "+response.datos.new.nombre +
            "<br>Apellido anterior: "+response.datos.old.apellido +
            "<br>Nuevo apellido   : "+response.datos.new.apellido ;
            saveTrack(comment, type_contact, id, id_operador);
            buscarSolicitud(id);
        } else {
            Swal.fire({
                title: 'Oops',
                text: response.mensaje,
                icon: 'error'
            });
        }

    });
}

showimage = (elem) => {
    img = $(elem).data('imgpatch'); 
    $("#AjustesImagen").html('')
    datafile = '';
    let ext = (img).split('.')
    if (ext[1] == 'pdf') {
        datafile = '<object class="AjustesImagen" type="application/pdf" data="'+$("#base_url").val() + img +'" style="width: -webkit-fill-available; height: -webkit-fill-available;" ></object>';
    }else
        datafile = '<object class="AjustesImagen" data="'+$("#base_url").val() + img +'" style="max-height:100%; max-width:100%;" ></object>';

    $("#AjustesImagen").html(datafile) 
}

/*********************************************************************/
/*** Muestra el modal de imágenes si la solicitud tiene más de una ***/
/*********************************************************************/
$("#a_mostrar").on('click', function() {
    if ($("#imagenes").html() !== "") {
        $("#modalImagen").modal('show');
    }
});
function formatNumber(numero) {
    let num = parseFloat(numero).toFixed(2);
    var num_parts = num.toString().split(".");
    num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return num_parts.join(",");
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
    if ( $.fn.DataTable.isDataTable(tabla) ) 
        $(tabla).DataTable().destroy();
	auxTabla = $(tabla).DataTable(options);
}