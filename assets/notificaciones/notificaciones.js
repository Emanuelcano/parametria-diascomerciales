$(document).ready(function () {
    base_url = $("input#base_url").val();
    $("#cargando").css("display", "none");
});
function vistaGrupos() {
    $("#main-ausencias").hide();
    base_url =
        $("input#base_url").val() + "notificaciones/Notificaciones/vistaGrupos";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}
function vistaPalabras() {
    $("#main-ausencias").hide();
    $("#tp_Beneficiarios")
        .DataTable()
        .ajax.reload();
    base_url =
        $("input#base_url").val() + "notificaciones/Notificaciones/vistaPalabras";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}

function VistaConfigCentrales() {
    $("#main-ausencias").hide();
    base_url =
        $("input#base_url").val() + "supervisores/Supervisores/VistaConfigCentrales";
    $.ajax({
        type: "POST",
        url: base_url,
        success: function (response) {
            $("#main").html(response);
            $("#cargando").css("display", "none");
        }
    });
}
