$('#frm_datos_baja').keydown(function (e){
    var keyCode= e.which;
    if (keyCode == 13){
      event.preventDefault();
      $('#btn_buscar').trigger('click'); 
    }
  });

$('#frm_dar_fallecido').keydown(function (e){
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
var tipoBusqueda = $('#sl_buscar').val();
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
            url: base_url + 'legales/Legales/get_fallecidos',
            type:'POST',
            data:{
                tipoBusqueda,
                datoBuscar
            },
            success: function (res) {
                var response = eval(res);
                $('.cuerpo').empty();
                $('.tabla').removeAttr('style');
                $('#btn_descargar').removeAttr('style');
                if (response == 'El documento no existe') {
                    $('#adjuntar_fallecidos').css('display','none');                   
                    $('.cuerpo').append('<th colspan="4">No se encontraron registros con este documento</th>');
                }else{                       
                    var nombre = response[0]['nombres'];
                    var documento = response[0]['documento'];
                        
                        if(response[0]['tipo'] == 'baja' || response[0]['tipo'] == 'No Baja'){
                            $('#adjuntar_fallecidos').css('display','none');
                            var opciones = '<span style="color:red;">Cliente dado de baja<br> el dia '+response[0]['razon'][0]['fecha_hora_aplicacion']+'</span>';
                        }else{
                            $('#adjuntar_fallecidos').removeAttr('style');
                            var opciones = '<a id="btn_dar_baja" nombre="'+nombre+'" documento="'+documento+'" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>';
                            var adjunto = '<th id="bt_adjuntarFallecidos"><a id="btn_adjuntarFallecidos" nombre="'+nombre+'" documento="'+documento+'" class="btn btn-primary"><i class="fa fa-folder-open" aria-hidden="true"></i></a></th>';
                        }
                        $('.cuerpo').append('<tr>');
                        $('.cuerpo').append('<th>' +response[0]['documento']+'</th>');
                        $('.cuerpo').append('<th style="text-align:left">' +response[0]['nombres']+'</th>');
                        $('.cuerpo').append('<th style="text-align:left">' +response[0]['apellidos']+'</th>');
                        
                        if (response[0]['tipo'] == 'si') {    
                            $('#adjuntar_fallecidos').css('display','none');                        
                            $('.cuerpo').append('<th><span style="color:red;">Cliente no cuenta con creditos activos</span></th>');
                        }else{
                            $('.cuerpo').append(adjunto);
                            $('.cuerpo').append('<th>'+opciones+'</th>');
                        }
                }
                
            }
        });
    }
});

$('body').on('click','#clientes a[id="btn_dar_baja"]',function(event){
    var documento = $(this).attr('documento');
    var nombre = $(this).attr('nombre');
    var $loader = $('.loader');
    Swal.fire({
        title: '¿Desea dar de baja los datos de '+nombre+'?',
        showCancelButton: true,
        showCancelButton: true,
        confirmButtonText: `Si`,
        cancelButtonText: `Cancelar`,
    }).then((result) => {
        if (result.value) {
            var razon = "FALLECIDO";
                $.ajax({
                    url: base_url + "api/ApiLegales/dar_baja",
                    type:'POST',
                    data:{documento: documento, razon:razon},
                    beforeSend: function() {
                        // aquí puedes poner el código paraque te muestre el gif
                          $("#compose-modal-wait").modal({
                              show: true,
                              backdrop: 'static',
                              keyboard: false
                          });
                          $loader.show();
                      },
                    success:function(response){
                        setTimeout(function(){
                            $loader.hide();
                            $("#compose-modal-wait").modal('hide');
                            }, 100);
                        Swal.fire('Se han dado de baja los datos', response, 'success')
                        //$('.tabla').reload();
                        $(".cuerpo").load(" .cuerpo");
                        $('#btn_buscar').trigger('click'); 
                    }
                });
        } else {
            Swal.fire('Se ha cancelado la acción', '', 'info')
        }
})
});


$('body').on('click', '#clientes a[id="btn_adjuntarFallecidos"]', function (event) {
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
                            $('.mostrarAdj').append('<div class="col-lg-4 list_comprobantes_bloqueos"></div>')
                            $('.list_comprobantes_bloqueos').append('<div id="mostrar_adjuntos_bloqueo"><label>Adjuntos Bloqueados:</label></div>')
                            bloqueados++
                        }
                        $('#mostrar_adjuntos_bloqueo').append('<p><a class="abrir_adjuntos_fallecido"id="abrir_comprobante'+[i]+'" comprobante="'+response[i].comprobante+'" href="#">'+response[i].nombre_comprobante+'</a></p>')                    
                    }else if (response[i].origen_comprobante == "legales/BajaDatos") {
                        if (baja_datos == 0) {
                            $('.mostrarAdj').append('<div class="col-lg-4 list_comprobantes_bajaDatos"></div>')
                            $('.list_comprobantes_bajaDatos').append('<div id="mostrar_adjuntos_Baja"><label>Adjuntos Baja datos:</label></div>')
                            baja_datos++
                        }
                        $('#mostrar_adjuntos_Baja').append('<p><a class="abrir_adjuntos_fallecido"id="abrir_comprobante'+[i]+'" comprobante="'+response[i].comprobante+'" href="#">'+response[i].nombre_comprobante+'</a></p>')                    
                    } else {
                        if (fallecidos == 0) {
                            $('.mostrarAdj').append('<div class="col-lg-4 list_comprobantes_fallecidos"></div>')
                            $('.list_comprobantes_fallecidos').append('<div id="mostrar_adjuntos_fallecidos"><label>Adjuntos Fallecidos:</label></div>')
                            fallecidos++
                        }
                        $('#mostrar_adjuntos_fallecidos').append('<p><a class="abrir_adjuntos_fallecido"id="abrir_comprobante'+[i]+'" comprobante="'+response[i].comprobante+'" href="#">'+response[i].nombre_comprobante+'</a></p>')                    
                    }                    
                    $('#abrir_comprobante'+[i]+'').css({'display':'flex'});
                    $('#abrir_comprobante'+[i]+'').append('<div id="descargarFallecido'+[i]+'"><a href="#" id="descargarAdjFallecido" nombre_comp="'+response[i].nombre_comprobante+'" comprobante="'+response[i].comprobante+'" ><i class="fa fa-download" aria-hidden="true"></i></a></div>')                    
                    $('#descargarFallecido'+[i]+'').css({'padding-left':'15px'});
                }
                
            }
        }
    });
    $('.modal-title').remove();
    $("#myModalAdjuntarFallecido").modal("show");
    $('.modalCabeceraFallecidos').append('<h3 class="modal-title" id="tituloModalfallecido">Adjuntar comprobante para '+nombre+'</h3>')
})

$("#myModalAdjuntarFallecido").on("hidden.bs.modal", function () {
    $('.inp_adjuntarFallecidos').val('');
});


$("#btn_agregar_comprobante").on("click", function (event) {
    if ($('#file').val() != '') {
        var documentoCliente = $('#datos_cliente').val();
        
        var formdata= new FormData($("#formArchivo")[0]);
        formdata.append('documento', documentoCliente)
        formdata.append('emitido', 'legales/Fallecidos')

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
                    $('#myModalAdjuntarFallecido').modal('toggle');
                }else{
                    Swal.fire('Atencion!',response,'warning');
                }
            }
        });
    }else{
        Swal.fire('Atencion!','Debe ingresar un comprobante para poder guardar','warning');
    }
})

$('body').on('click', '.contenido_modal a[class="abrir_adjuntos_fallecido"]', function (event) {
    let comprobante = $(this).attr('comprobante');
    let url = base_url+'/'+comprobante;
    console.log('aqui');
    window.open(url, '_blank');
})

$('body').on('click', '.contenido_modal a[id="descargarAdjFallecido"]', function (event) {
    let comprobante = $(this).attr('comprobante');
    let nombre_comp = $(this).attr('nombre_comp');
    let url = base_url+'/'+comprobante;
    
    var a = document.createElement('a');
    a.download = nombre_comp;
    a.target = '_blank';
    a.href= url;
    a.click();
})