$(document).ready(function () {
    $('a#btn_gestion_variables').trigger('click');
});

$("a#btn_gestion_variables").on("click", function () {

    $.ajax({
        type: "POST",
        url: base_url + 'admin_sistemas/tableVariables',
        success: function (response) {
            $("#contenido").html(response);
            $("#cargando").css("display", "none");
        },
        beforeSend: function() {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#main").html(loading);
        }
        
    });
});