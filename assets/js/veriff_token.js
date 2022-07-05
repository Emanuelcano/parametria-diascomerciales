$(function() {

    setTimeout(function(){ $("#resend").removeClass('disabled') }, 30000);
    $("#resend").on('click', function(){
        if($("#resend").hasClass('disabled')){
            Swal.fire("Debe esperar 30seg para volver a enviar el código","","info");
        }else{
            reenviar_token();
        }
    });   
    
    $("#token").on("keyup",function() {
        if($("#token").val().length >= 8){
            $("#validate").removeClass('disabled');
        }else{
            $("#validate").addClass('disabled');
        }
    });

    $("#validate").on('click', function(){validar_token();});
});

function validar_token(){
    let token = $("#token").val();
    let base_url = $("#base_url").val();
    if (token.length > 0) {
        
        $.ajax({
            url: base_url + 'login/verificacion/'+token,
            type: 'GET',
            dataType: 'json',
        })
        .done(function (response) {
            if(response.ok && typeof(response.URL) != "undefined"){
                window.location.href = response.URL;
            }else{
                    Swal.fire(response.message,"",  "error");
    
            }
        });
    }
}

function reenviar_token() {
    let base_url = $("#base_url").val();
    $.ajax({
        url: base_url + 'login/reenviar',
        type: 'GET',
        dataType: 'json',
    })
    .done(function (response) {
        console.log(response)
        if(response.ok){
            Swal.fire(response.message,"", "success");
            $("#resend").addClass('disabled');
            setTimeout(function(){ $("#resend").removeClass('disabled') }, 30000);
        }else{
            Swal.fire(response.message,"",  "error");

        }
    });
}