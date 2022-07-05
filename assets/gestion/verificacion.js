$(document).ready(function() {     
    mostrarDatos();
    $('.datos_verificacion').click();
    //Abrir verificacion titular
    $("#verified_titular").click(function(){
        $('.disabled_familiar').attr('disabled','disabled');
        $('.disabled_laboral').attr('disabled','disabled');        
        $('.disabled_personal').attr('disabled','disabled'); 
        var cantidad = $('#cantidad_titular').val();
        if(cantidad >= 1){
            $('.disabled_titular').attr('disabled','disabled'); 
        } else {
            $('.disabled_titular').attr('disabled',false);
        }        
        $("#form_titular").fadeIn('slow',function() {                   
            $(this).css("display","block");                     
        });           
        $('.datos_verificacion').click();
    });
        
    //Abrir verificacion familiar    
    $("#verified_familiar").click(function(){
        $('.disabled_titular').attr('disabled','disabled');
        $('.disabled_laboral').attr('disabled','disabled');
        $('.disabled_personal').attr('disabled','disabled'); 
        var cantidad = $('#cantidad_familiar').val();
        if(cantidad >= 3){
            $('.disabled_familiar').attr('disabled','disabled');
        } else {
             $('.disabled_familiar').attr('disabled',false);
        }
        
        $("#form_familiar").fadeIn('slow',function() {
            $(this).css("display","block");
        });           
        $('.datos_verificacion').click();
    });

    //Abrir verificacion personal
    $("#verified_personal").click(function(){
        $('.disabled_titular').attr('disabled','disabled');
        $('.disabled_laboral').attr('disabled','disabled');
        $('.disabled_familiar').attr('disabled','disabled');
        var cantidad = $('#cantidad_personal').val();
        if(cantidad >= 3){
            $('.disabled_personal').attr('disabled','disabled');
        } else {
         $  ('.disabled_personal').attr('disabled',false);       
        }
        $("#form_personal").fadeIn('slow',function() {
            $(this).css("display","block");
        });          
        $('.datos_verificacion').click();
    });
    
     //Abrir verificacion laboral
    $("#verified_laboral").click(function(){
        $('.disabled_titular').attr('disabled','disabled');
        $('.disabled_familiar').attr('disabled','disabled');
        $('.disabled_personal').attr('disabled','disabled'); 
        var cantidad = $('#cantidad_laboral').val();
        if(cantidad >= 3){
            $('.disabled_laboral').attr('disabled','disabled'); 
        }else{
            $('.disabled_laboral').attr('disabled',false);
        }           
        $("#form_laboral").fadeIn('slow',function() {
            $(this).css("display","block");
        });
        $('.datos_verificacion').click();
    });
    
     //Abrir verificacion titular independiente
    $("#verified_titular_independiente").click(function(){
        $('.disabled_prov2').attr('disabled','disabled');
        $('.disabled_provl').attr('disabled','disabled');
        var cantidad = $('#cantidad_titular_ind').val();
        if(cantidad >= 1){
            $('.disabled_tit_ind').attr('disabled','disabled'); 
        }else{
            $('.disabled_tit_ind').attr('disabled',false);
        }
        
        $("#form_titular_independiente").fadeIn('slow',function() {
            $(this).css("display","block");
        });           
        $('.datos_verificacion').click();
    });
    
     //Abrir verificacion proveedor1
    $("#verified_proveedor1").click(function(){
        $('.disabled_prov2').attr('disabled','disabled');
        $('.disabled_tit_ind').attr('disabled','disabled');
        var cantidad = $('#cantidad_prov1').val();
        if(cantidad >= 3){
            $('.disabled_provl').attr('disabled','disabled');
        }else{
            $('.disabled_provl').attr('disabled',false);
        }            
        $("#form_proveedor1").fadeIn('slow',function() {
            $(this).css("display","block");
        });            
        $('.datos_verificacion').click();
    });
    
     //Abrir verificacion proveedor2
    $("#verified_proveedor2").click(function(){
        $('.disabled_provl').attr('disabled','disabled');
        $('.disabled_tit_ind').attr('disabled','disabled');
        var cantidad = $('#cantidad_prov2').val();
        if(cantidad >= 1){
             $('.disabled_prov2').attr('disabled','disabled');
        }else{
            $('.disabled_prov2').attr('disabled',false);
        }        
        $("#form_proveedor2").fadeIn('slow',function() {
            $(this).css("display","block");
        });          
        $('.datos_verificacion').click();
    });
    
    //Guardar datos familiar
    $('#form_familiar').submit(function (event){        
        event.preventDefault();
        var formData= new FormData($("#form_familiar")[0]);
            $.ajax({
                url:$('#form_familiar').attr('action'),
                type:$('#form_familiar').attr('method'),
                data:formData,
                cache: false,
                contentType: false,
                processData:false,
                success:function(response){ 
                    if (response.errors){
                        toastr["error"](response.errors, "Verificación Familiar"); 
                    } else { 
                        var cantidad = parseInt(response.cantidad.cantidad)+1;
                        if (cantidad >2){
                            $('.disabled_familiar').attr('disabled','disabled');
                        }
                        $("#boton_familiar").before('<button type="button" class="btn btn-warning datos_verificacion" pregunta1="'+response.data.pregunta1+'" pregunta2="'+response.data.pregunta2+'" pregunta3="'+response.data.pregunta3+'" pregunta4="'+response.data.pregunta4+'" pregunta5="'+response.data.pregunta5+'" pregunta6="'+response.data.pregunta6+'">'+cantidad+'</button>');
                        toastr["success"](response.message, "Verificación Familiar");   
                        mostrarDatos();                        
                    }
                }
            });        
    });
    
    //Guardar datos titular
    $('#form_titular').submit(function (event){        
        event.preventDefault();
        var formData= new FormData($("#form_titular")[0]);
            $.ajax({
                url:$('#form_titular').attr('action'),
                type:$('#form_titular').attr('method'),
                data:formData,
                cache: false,
                contentType: false,
                processData:false,
                success:function(response){                    
                    if (response.errors){                       
                        toastr["error"](response.errors, "Verificación Titular");                           
                    } else {
                        var cantidad = parseInt(response.cantidad.cantidad)+1;
                        if (cantidad >0){
                            $('.disabled_titular').attr('disabled','disabled');
                        }
                        $("#boton_titular").before('<button type="button" class="btn  btn-info datos_verificacion" pregunta1="'+response.data.pregunta1+'" pregunta2="'+response.data.pregunta2+'" pregunta3="'+response.data.pregunta3+'" pregunta4="'+response.data.pregunta4+'" pregunta5="'+response.data.pregunta5+'" pregunta6="'+response.data.pregunta6+'">'+cantidad+'</button>');
                        toastr["success"](response.message, "Verificación Titular");            
                        mostrarDatos();           
                    }
                }
            });        
    });
    
     //Guardar datos personal
    $('#form_personal').submit(function (event){        
        event.preventDefault();
        var formData= new FormData($("#form_personal")[0]);
            $.ajax({
                url:$('#form_personal').attr('action'),
                type:$('#form_personal').attr('method'),
                data:formData,
                cache: false,
                contentType: false,
                processData:false,
                success:function(response){                    
                    if (response.errors){                       
                        toastr["error"](response.errors, "Verificación Personal");                           
                    } else {
                        var cantidad = parseInt(response.cantidad.cantidad)+1;
                        if (cantidad >2){
                            $('.disabled_personal').attr('disabled','disabled');
                        }
                        $("#boton_personal").before('<button type="button" class="btn btn-success datos_verificacion" pregunta1="'+response.data.pregunta1+'" pregunta2="'+response.data.pregunta2+'" pregunta3="'+response.data.pregunta3+'" pregunta4="'+response.data.pregunta4+'" pregunta5="'+response.data.pregunta5+'" pregunta6="'+response.data.pregunta6+'">'+cantidad+'</button>');
                        toastr["success"](response.message, "Verificación Personal");
                        mostrarDatos();
                    }
                }
            });        
    });
    
     //Guardar datos laboral
    $('#form_laboral').submit(function (event){        
        event.preventDefault();
        var formData= new FormData($("#form_laboral")[0]);
            $.ajax({
                url:$('#form_laboral').attr('action'),
                type:$('#form_laboral').attr('method'),
                data:formData,
                cache: false,
                contentType: false,
                processData:false,
                success:function(response){ 
                    if (response.errors){                       
                        toastr["error"](response.errors, "Verificación Laboral");                        
                    } else {     
                        var cantidad = parseInt(response.cantidad.cantidad)+1;
                        if (cantidad >2){
                            $('.disabled_laboral').attr('disabled','disabled');
                        }
                        $("#boton_laboral").before('<button type="button" class="btn  btn-success datos_verificacion" pregunta1="'+response.data.pregunta1+'" pregunta2="'+response.data.pregunta2+'" pregunta3="'+response.data.pregunta3+'" pregunta4="'+response.data.pregunta4+'" pregunta5="'+response.data.pregunta5+'" pregunta6="'+response.data.pregunta6+'">'+cantidad+'</button>');
                        toastr["success"](response.message, "Verificación Laboral");
                        mostrarDatos();                        
                    }
                }
            });        
    });
    
     //Guardar datos titular independiente
    $('#form_titular_independiente').submit(function (event){        
        event.preventDefault();
        var formData= new FormData($("#form_titular_independiente")[0]);
            $.ajax({
                url:$('#form_titular_independiente').attr('action'),
                type:$('#form_titular_independiente').attr('method'),
                data:formData,
                cache: false,
                contentType: false,
                processData:false,
                success:function(response){                    
                    if (response.errors){                        
                        toastr["error"](response.errors, "Verificación Titular Independiente"); 
                    } else { 
                        var cantidad = parseInt(response.cantidad.cantidad)+1;
                        if (cantidad >0){
                            $('.disabled_tit_ind').attr('disabled','disabled');
                        }
                        $("#boton_titular_ind").before('<button type="button" class="btn btn-info datos_verificacion" pregunta1="'+response.data.pregunta1+'" pregunta2="'+response.data.pregunta2+'" pregunta3="'+response.data.pregunta3+'" pregunta4="'+response.data.pregunta4+'" pregunta5="'+response.data.pregunta5+'" pregunta6="'+response.data.pregunta6+'">'+cantidad+'</button>');
                        toastr["success"](response.message, "Verificación Titular Independiente");
                        mostrarDatos();    
                    }
                }
            });        
    });
    
     //Guardar datos proveedor 1
    $('#form_proveedor1').submit(function (event){        
        event.preventDefault();
        var formData= new FormData($("#form_proveedor1")[0]);
            $.ajax({
                url:$('#form_proveedor1').attr('action'),
                type:$('#form_proveedor1').attr('method'),
                data:formData,
                cache: false,
                contentType: false,
                processData:false,
                success:function(response){                 
                    if (response.errors){                       
                        toastr["error"](response.errors, "Verificación Proveedor 1"); 
                    } else {
                        var cantidad = parseInt(response.cantidad.cantidad)+1;
                        if (cantidad >2){
                            $('.disabled_provl').attr('disabled','disabled');
                        }
                        $("#boton_prov1").before('<button type="button" class="btn btn-warning datos_verificacion" pregunta1="'+response.data.pregunta1+'" pregunta2="'+response.data.pregunta2+'" pregunta3="'+response.data.pregunta3+'" pregunta4="'+response.data.pregunta4+'" pregunta5="'+response.data.pregunta5+'" pregunta6="'+response.data.pregunta6+'">'+cantidad+'</button>');
                        toastr["success"](response.message, "Verificación Proveedor 1");
                        mostrarDatos();    
                    }
                }
            });        
    });
    
     //Guardar datos proveedor 2
    $('#form_proveedor2').submit(function (event){        
        event.preventDefault();
        var formData= new FormData($("#form_proveedor2")[0]);
            $.ajax({
                url:$('#form_proveedor2').attr('action'),
                type:$('#form_proveedor2').attr('method'),
                data:formData,
                cache: false,
                contentType: false,
                processData:false,
                success:function(response){                    
                    if (response.errors){                       
                        toastr["error"](response.errors, "Verificación Proveedor 2"); 
                    } else {  
                        var cantidad = parseInt(response.cantidad.cantidad)+1;
                        if (cantidad >2){
                            $('.disabled_prov2').attr('disabled','disabled');
                        }
                        $("#boton_prov2").before('<button type="button" class="btn btn-success datos_verificacion" pregunta1="'+response.data.pregunta1+'" pregunta2="'+response.data.pregunta2+'" pregunta3="'+response.data.pregunta3+'" pregunta4="'+response.data.pregunta4+'" pregunta5="'+response.data.pregunta5+'" pregunta6="'+response.data.pregunta6+'">'+cantidad+'</button>');
                        toastr["success"](response.message, "Verificación Proveedor 2 o Cliente");
                        mostrarDatos();    
                    }
                }
            });        
    });
    
    
});   


function mostrarDatos(){
    $(".datos_verificacion").click(function(){
        var pregunta1 = $(this).attr('pregunta1');
        var pregunta2 = $(this).attr('pregunta2');
        var pregunta3 = $(this).attr('pregunta3');
        var pregunta4 = $(this).attr('pregunta4');
        var pregunta5 = $(this).attr('pregunta5');
        var pregunta6 = $(this).attr('pregunta6');
        $(this).parent().find('.pregunta1').val(pregunta1);
        $(this).parent().find('.pregunta2').val(pregunta2);
        $(this).parent().find('.pregunta3').val(pregunta3);
        $(this).parent().find('.pregunta4').val(pregunta4);
        $(this).parent().find('.pregunta5').val(pregunta5);
        $(this).parent().find('.pregunta6').val(pregunta6);
    });  
}

function limpiarFormularioFamiliar() {
    document.getElementById("form_familiar").reset();
} 

function limpiarFormularioTitular() {
    document.getElementById("form_titular").reset();
} 

function limpiarFormularioPersonal() {
    document.getElementById("form_personal").reset();
} 

function limpiarFormularioLaboral() {
    document.getElementById("form_laboral").reset();
} 

function limpiarFormularioTitularInd() {
    document.getElementById("form_titular_independiente").reset();
} 

function limpiarFormularioProv1() {
    document.getElementById("form_proveedor1").reset();
} 

function limpiarFormularioProv2() {
    document.getElementById("form_proveedor2").reset();
}