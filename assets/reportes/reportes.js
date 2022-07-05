function control_solicitudes() {

    $.ajax({
        type: "POST",
        url: base_url + "reportes/reportes/vistaReporteSolicitud",
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

function control_reporte() {
    $("#tp_Beneficiarios")
        .DataTable()
        .ajax.reload();    
    $.ajax({
        type: "POST",
        url: base_url + "reportes/reportes/vistaReporteVencimiento",
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

function control_originacion() {   
    //console.log('aqui estoy');
    
    $.ajax({
     type: "POST",
     url: base_url + "reportes/reportes/vistaReporteOriginacion",
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

function contable_reporte() {
    $.ajax({
        type: "POST",
        url: base_url + "reportes/reportes/vistaReporteContable",
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