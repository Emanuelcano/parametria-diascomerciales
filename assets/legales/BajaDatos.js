$('#frm_datos_baja').keydown(function (e){
    var keyCode= e.which;
    if (keyCode == 13){
      event.preventDefault();
      $('#btn_buscar').trigger('click'); 
    }
  });

$('#frm_dar_baja').keydown(function (e){
var keyCode= e.which;
if (keyCode == 13){
    event.preventDefault();
    $('#btn_dar_baja').trigger('click'); 
}
});

$('#datos_cliente').click(function (event) {
    $(function(){

        $('#datos_cliente').keypress(function(e) {
            if(isNaN(this.value + String.fromCharCode(e.charCode))) 
            return false;
        })
        .on(function(e){
            e.preventDefault();
        });          
    });
});
$('button#btn_buscar').click(function (event) {
    var datoBuscar = $('#datos_cliente').val();
    if ($('#datos_cliente').val() == '') {
        Swal.fire({
            title: 'Atencion!',
            text: 'Debe ingresar un dato para la busqueda',
        });
        $('#datos_cliente').focus();
        
 
    }else if(isNaN($('#datos_cliente').val())){
        Swal.fire({
            title: 'Atencion!',
            text: 'Dato ingresado no valido',
        });
        $('#datos_cliente').val('');
    }else{
        event.preventDefault();
        $.ajax({
            url: base_url + $('#frm_datos_baja').attr('action'),
            type:$('#frm_datos_baja').attr('method'),
            data:{
                datoBuscar
            },
            success: function (res) {
                var response = eval(res);
                $('.cuerpo').empty();
                $('.tabla').removeAttr('style');
                $('#observaciones').removeAttr('style');
                $('#observaciones').removeAttr('style');
                if (response == 'El documento no existe') {         
                    $('#adjuntar_bajaDatos').css('display','none');          
                    $('.cuerpo').append('<th colspan="5">No se encontraron registros con este documento</th>');
                }else{                       
                    var nombre = response[0]['nombres'];
                    var documento = response[0]['documento'];
                    if (response[0]['estado'] == 'mora' || response[0]['estado'] =='vigente') {
                        var opciones = '<th colspan="2"><span style="color:red;">Cliente con credito activo</span></th>';
                        $('#adjuntar_bajaDatos').css('display','none');
                        $('#observaciones').css('display','none');
                    }else{
                        var opciones = '<th><a id="btn_dar_baja" nombre="'+nombre+'" documento="'+documento+'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a></th>';
                    }
                    $('.cuerpo').append('<tr>');
                    $('.cuerpo').append('<th>' +response[0]['documento']+'</th>');
                    $('.cuerpo').append('<th class="nombres" style="text-align:left">' +response[0]['nombres']+'</th>');
                    $('.cuerpo').append('<th id="txt_apellido" style="text-align:left">' +response[0]['apellidos']+'</th>');
                    if (response[0]['tipo'] == 'No Baja') {
                        $('#adjuntar_bajaDatos').removeAttr('style');
                        $('<th><input type="text" name="observaciones_txt" id="observaciones_txt" placeholder="Agregar una observaci&oacute;n (OBLIGATORIO)" class="form-control" aria-label="Username" aria-describedby="basic-addon1"></th>').insertAfter('#txt_apellido');
                        $('.cuerpo').append('<th id="bt_adjuntarBajadatos"><a id="btn_adjuntarBajaDatos" nombre="'+nombre+'" documento="'+documento+'" class="btn btn-primary"><i class="fa fa-folder-open" aria-hidden="true"></i></a></th>')
                        $('#observaciones_txt').css({'height':'30px'});
                    }
                    if(response[0]['estado'] == 'mora' || response[0]['estado'] =='vigente' || response[0]['tipo'] == 'No Baja'){
                        $('.cuerpo').append(opciones);
                    }
                    if(response[0]['tipo'] == 'baja'){
                                $('#adjuntar_bajaDatos').removeAttr('style');
                                $('<th id="inputs" rowspan="3"><span style="color:red;">Cliente ya dado de baja el dia '+response[0]['razon'][0]['fecha_hora_aplicacion']+'</span></th>').insertAfter('#txt_apellido');
                                $('#inputs').append('<tr><th id="campos"><input type="text" id="razon" disabled value="'+response[0]['razon'][0]['razon']+'" class="form-control" aria-label="Username" aria-describedby="basic-addon1"></th>');                  
                                $('#inputs').append('<th><input type="text" name="observaciones_txt" id="observaciones_txt" placeholder="Comentario para reactivar el cliente" class="form-control" aria-label="Username" aria-describedby="basic-addon1"></th></tr>');                  
                                $('#inputs').css({'padding-top':'1%'})
                                $('#campos').css({'width':'10%'})
                                $('#razon').css({'width':'92%', 'margin-left':'5%', 'height':'30px'});
                                $('#observaciones_txt').css({'width':'92%', 'margin-left':'5%', 'height':'30px'});
                                $('.cuerpo').append('<th id="bt_adjuntarBajadatos"><a id="btn_adjuntarBajaDatos" nombre="'+nombre+'" documento="'+documento+'" class="btn btn-primary"><i class="fa fa-folder-open" aria-hidden="true"></i></a></th>')
                                $('.cuerpo').append('<th><a id="btn_reactivar" nombre="'+nombre+'" documento="'+documento+'" class="btn btn-success"><i class="fa fa-check-square-o" aria-hidden="true"></i></a></th>')
                        }
                        
                    }
                
            }
        });
    }
});

$('body').on('click','#clientes a[id="btn_dar_baja"]',function(event){
    if ($('#observaciones_txt').val() == '') {
        Swal.fire({
            title: 'Atencion!',
            text: 'Es requerido agregar una observaci??n',
        });
        $('#datos_cliente').focus();
    }else{

    let observaciones = $('#observaciones_txt').val();
    let documento = $(this).attr('documento');
    let nombre = $(this).attr('nombre');
    Swal.fire({
        title: '??Desea dar de baja los datos de '+nombre+'?',
        showCancelButton: true,
        showCancelButton: true,
        confirmButtonText: `Si`,
        cancelButtonText: `Cancelar`,
    }).then((result) => {
        if (result.value) {
            let razon = '[BAJA DATOS]';
            $.ajax({
                url: base_url + "api/ApiLegales/dar_baja",
                type:'POST',
                data:{documento: documento, razon:razon, observaciones:observaciones},
                success:function(response){
                    Swal.fire('Se han dado de baja los datos', response, 'success')
                    //$('.tabla').reload();
                        $(".cuerpo").load(" .cuerpo");
                    $('#btn_buscar').trigger('click'); 
                    }
                });
        } else {
            Swal.fire('Se ha cancelado la acci??n', '', 'info')
        }
    })
}
});

$('body').on('click','#clientes a[id="btn_reactivar"]',function(event){
    if ($('#observaciones_txt').val() == '') {
        Swal.fire('Atencion!', 'Es requerido agregar una observaci??n','warning');
        $('#datos_cliente').focus();
    }else{

    let observaciones = $('#observaciones_txt').val();
    let documento = $(this).attr('documento');
    let nombre = $(this).attr('nombre');
    Swal.fire({
        title: '??Desea REACTIVAR a '+nombre+'?',
        showCancelButton: true,
        showCancelButton: true,
        confirmButtonText: `Si`,
        cancelButtonText: `Cancelar`,
    }).then((result) => {
        if (result.value) {
            let razon = '[REVERSO BAJA DATOS]';
                $.ajax({
                    url: base_url + "api/ApiLegales/reactivar",
                    type:'POST',
                    data:{documento: documento, razon:razon, observaciones:observaciones},
                    success:function(response){
                        Swal.fire('Se ha reactivado a este cliente', response, 'success')
                        //$('.tabla').reload();
                        $(".cuerpo").load(" .cuerpo");
                        $('#btn_buscar').trigger('click'); 
                    }
                });
        } else {
            Swal.fire('Se ha cancelado la acci??n', '', 'info')
        }
    })
}
});

$('body').on('click', '#titulo a[id="btn_descargar"]', function(event){
    var $loader = $('.loader');
    var tipo = 'baja';
    $.ajax({
        type: "post",
        data: {tipo:tipo},
        url: base_url + "legales/Legales/descargar_datos",
        beforeSend: function() {
            $("#compose-modal-wait").modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
            $loader.show();
        },
        success: function (response) {
            setTimeout(function(){
                $loader.hide();
                $("#compose-modal-wait").modal('hide');
                }, 100);
            let url = base_url+"public/csv/"+response;
                    //console.log(response);  
                    window.open(url, '_self');
            
        }
    });
})

$('body').on('click', '#clientes a[id="btn_adjuntarBajaDatos"]', function (event) {
    let nombre = $(this).attr('nombre');
    let documento = $(this).attr('documento');
    $.ajax({
        type: "post",
        url: base_url + "legales/Legales/mostrar_adjunto",
        data: {documento:documento},
        success: function (respuesta) {
            var response = eval(respuesta);
            $('#info_archivo').remove();
            if (response == 'No existen archivos adjuntos') {
                $('.contenido_modal').append('<p id="info_archivo" style="color:red; padding-top:10px;"><strong>'+response+'</strong></p>')
            }else{
                $('.mostrarAdj').remove();
                $('.contenido_modal').append('<div class="row mostrarAdj"></div>')
                var bloqueados = 0;
                var baja_datos = 0;
                var fallecidos = 0;
                for (let i = 0; i < response.length; i++) {
                    if (response[i].origen_comprobante == "legales/Bloquear") {
                        if (bloqueados == 0) {
                            $('.mostrarAdj').append('<div class="col-lg-4 list_comprobantes_bloquear"></div>')
                            $('.list_comprobantes_bloquear').append('<div id="mostrar_adjuntos_bloqueo"><label>Adjuntos Bloqueados:</label></div>')
                            bloqueados++
                        }
                        $('#mostrar_adjuntos_bloqueo').append('<p><a class="abrir_adjuntos_baja"id="abrir_comprobante'+[i]+'" comprobante="'+response[i].comprobante+'" href="#">'+response[i].nombre_comprobante+'</a></p>')                    
                    }else if (response[i].origen_comprobante == "legales/BajaDatos") {
                        if (baja_datos == 0) {
                            $('.mostrarAdj').append('<div class="col-lg-4 list_comprobantes_baja_datos"></div>')
                            $('.list_comprobantes_baja_datos').append('<div id="mostrar_adjuntos_baja_datos"><label>Adjuntos Baja datos:</label></div>')
                            baja_datos++
                        }
                        $('#mostrar_adjuntos_baja_datos').append('<p><a class="abrir_adjuntos_baja"id="abrir_comprobante'+[i]+'" comprobante="'+response[i].comprobante+'" href="#">'+response[i].nombre_comprobante+'</a></p>')                    
                    } else {
                        if (fallecidos == 0) {
                            $('.mostrarAdj').append('<div class="col-lg-4 list_comprobantes_fallecidos"></div>')
                            $('.list_comprobantes_fallecidos').append('<div id="mostrar_adjuntos_fallecidos"><label>Adjuntos Fallecidos:</label></div>')
                            fallecidos++
                        }
                        $('#mostrar_adjuntos_fallecidos').append('<p><a class="abrir_adjuntos_baja"id="abrir_comprobante'+[i]+'" comprobante="'+response[i].comprobante+'" href="#">'+response[i].nombre_comprobante+'</a></p>')                    
                    }
                $('#abrir_comprobante'+[i]+'').css({'display':'flex'});
                    $('#abrir_comprobante'+[i]+'').append('<div id="descargarBajaDatos'+[i]+'"><a href="#" id="descargarAdjBaja" nombre_comp="'+response[i].nombre_comprobante+'" comprobante="'+response[i].comprobante+'" ><i class="fa fa-download" aria-hidden="true"></i></a></div>')                    
                    $('#descargarBajaDatos'+[i]+'').css({'padding-left':'15px'});
                }
                
            }
        }
    });
    $('.modal-title').remove();
    $("#myModalAdjuntarBajaDatos").modal("show");
    $('.modalCabeceraBajaDatos').append('<h3 class="modal-title" id="tituloModalBajaDatos">Adjuntar comprobante para '+nombre+'</h3>')
})

$("#myModalAdjuntarBajaDatos").on("hidden.bs.modal", function () {
    $('.inp_adjuntarBajaDatos').val('');
});


$("#btn_agregar_comprobante").on("click", function (event) {
    if ($('#file').val() != '') {
        var documentoCliente = $('#datos_cliente').val();
        
        var formdata= new FormData($("#formArchivo")[0]);
        formdata.append('documento', documentoCliente)
        formdata.append('emitido', 'legales/BajaDatos')
        $.ajax({
            type: "post",
            enctype: 'multipart/form-data',
            url: base_url + "legales/Legales/adjuntar_archivos",
            data: formdata,
            cache:false,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response == true) {
                    Swal.fire("Registro exitoso",'Guardado de manera exito','success')
                    $('#myModalAdjuntarBajaDatos').modal('toggle');
                }else{
                    Swal.fire('Atencion!',response,'warning');
                }
            }
        });
    }else{
        Swal.fire('Atencion!','Debe ingresar un comprobante para poder guardar','warning');
    }
})

$('body').on('click', '.contenido_modal a[class="abrir_adjuntos_baja"]', function (event) {
    let comprobante = $(this).attr('comprobante');
    let url = base_url+'/'+comprobante;
    
    window.open(url, '_blank');
})

$('body').on('click', '.contenido_modal a[id="descargarAdjBaja"]', function (event) {
    let comprobante = $(this).attr('comprobante');
    let nombre_comp = $(this).attr('nombre_comp');
    let url = base_url+'/'+comprobante;
    
    var a = document.createElement('a');
    a.download = nombre_comp;
    a.target = '_blank';
    a.href= url;
    a.click();
})