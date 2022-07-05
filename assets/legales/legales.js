$(document).ready(function () {
    base_url = $("input#base_url").val();
    $("#cargando").css("display", "none");
});

function vistaBajaDatos() {
    $.ajax({
        type: "POST",
        url: base_url + "legales/Legales/vistaBajaDatos",
        success: function (response) {
            $("#main").html(response);
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
}

function Vistafallecido() {
$.ajax({
    type: "POST",
    url: base_url + "legales/Legales/Vistafallecido",
    success: function (response) {
        $("#main").html(response);
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
}

function VistaBloquear() {
    $.ajax({
        type: "POST",
        url: base_url + "legales/Legales/VistaBloquear",
        success: function (response) {
            $("#main").html(response);
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
    }

function VistaUsura() {
    $.ajax({
        type: "POST",
        url: base_url + "legales/Legales/VistaUsura",
        success: function (response) {
            $("#main").html(response);
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
    }