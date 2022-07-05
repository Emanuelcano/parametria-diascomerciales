$(document).ready(function() {
    var $loader = $('.loading');  
    let tipo = 'btn_diario';
    $.ajax({
        type: "post",
        url: base_url + "gestion/TableroOriginacion/originacion_data",
        data: {tipo:tipo},
        beforeSend: function() {
            $("#compose-modal-wait-loading").modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
            $loader.show();
        },
        success: function (response) {  
            let datos = JSON.parse(response);
            if (datos['status'][0] == '400'){
                $("#compose-modal-wait-loading").modal('hide');
                Swal.fire('Atencion!', datos['status'][1],'warning');
            }else{
                datos['ruta'] = "tablero_originacion/tablero_hoy";
                $("#contenido").load("TableroOriginacion/obtener_tableros_originacion", {"datos":datos});
                setTimeout(function(){
                    $loader.hide();
                    $("#compose-modal-wait-loading").modal('hide');
                }, 800);
            }
        }
    });
    $('#sl_diaCorte').on('change', function (){
        let dia = $('#sl_diaCorte').val();
        cambioSeccion('btn_semanal', 'tablero_semanal', dia);
    });
});


function cambioSeccion(tipo, ruta, dia = null) {
    $('.button').removeClass('active');
    $('#'+tipo+'').addClass('active');
    var $loader = $('.loading');   
    $.ajax({
        type: "post",
        url: base_url + "gestion/TableroOriginacion/originacion_data",
        data: {tipo:tipo, dia:dia},
        beforeSend: function() {
            $("#compose-modal-wait-loading").modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
            $loader.show();
        },
        success: function (response) {
            let datos = JSON.parse(response);
            if (datos['status'][0] == '400'){
                $("#compose-modal-wait-loading").modal('hide');
                Swal.fire('Atencion!', datos['status'][1],'warning');
            }else{
                setTimeout(function(){
                    $loader.hide();
                    $("#compose-modal-wait-loading").modal('hide');
                }, 800);
                if (tipo == "btn_semanal") {
                    $('#sl_diaCorte').empty();
                    var inicio = new Date().getDay();
                    let day = dia        
                    let menor = inicio;
                    let mayor = 1; 
                    
                        if (inicio > 0) {                    
                            for (let i = 0; i <= 6; i++) {
                                let dateString = convertirFecha(i);
                                if (inicio === i) {
                                    if (day === null || day === '') {
                                        $('#sl_diaCorte').append('<option value="'+inicio+'" selected> '+dateString+' </option>');
                                    }else if(day == inicio){
                                        $('#sl_diaCorte').append('<option value="'+inicio+'" selected> '+dateString+' </option>');
                                    }else{
                                        $('#sl_diaCorte').append('<option value="'+inicio+'"> '+dateString+' </option>');
                                    }
                                    
                                }else{
                                    
                                        if (i < inicio) {
                                            let diaMenor = '-'+menor
                                            if (day == diaMenor) {
                                                $('#sl_diaCorte').append('<option value="'+day+'" selected> '+dateString+' </option>');
                                            }else{
                                                $('#sl_diaCorte').append('<option value="'+diaMenor+'"> '+dateString+' </option>');
                                            }
                                            menor--;
                                        }else{
                                            let diaMayor = '+'+mayor
                                            if (day == diaMayor) {
                                                $('#sl_diaCorte').append('<option value="'+day+'" selected> '+dateString+' </option>');
                                            }else{
                                                $('#sl_diaCorte').append('<option value="'+diaMayor+'"> '+dateString+' </option>');
                                            }
                                            mayor++;
                                        }
                                    
                                }                   
                            }
                        }
                    
                    $('#slc_semana').removeAttr('style');
                }else{
                    $('#slc_semana').css({'display':'none'});
                }
                let data ={"indicadores": datos, "ruta": "tablero_originacion/"+ruta};
                $("#contenido").load("TableroOriginacion/obtener_tableros_originacion", {"datos":data});
            }
        }
    });
}

function convertirFecha(dia) { 
    switch (dia) {
        case 0:
            return "DOMINGO";
        break;
        case 1:
            return "LUNES";
        break;
        case 2:
            return "MARTES";
        break;
        case 3:
            return "MIERCOLES";
        break;
        case 4:
            return "JUEVES";
        break;
        case 5:
            return "VIERNES";
        break;    
        default:
            return "SABADO"
        break;
    }
}