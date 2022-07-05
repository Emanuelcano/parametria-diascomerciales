<span class="hidden-xs">
    <?php
    $usuario = $this->session->userdata("username");
    $tipoUsuario = $this->session->userdata("tipo");
    ?>
</span>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario ?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario; ?>">

<div id="dashboard_principal" style="display: block; background: #FFFFFF; padding-top:2.5%;">
    <div class="box-header with-border" class="col-lg-12">
		<div class="col-lg-12" id="btns_marketing" style="display: block">
			<div class="box-body pull-left">
				<a class="btn btn-app" id="btn_leads" onclick="tablero_leads();">
					<span class="badge bg-red" id="btn_table_lead"></span>
					<i class="fa fa-usd"></i> Tablero leads
				</a>
			</div>
        </div>

    </div>
</div>

<div>
    <section class="content" style="padding-top: -2%;">

        <div class="col-lg-12" id="contenido_marketing" style="display: block">

        </div>

    </section>
</div>

<!--  -->

<script>
$(document).ready(function(event){
    $('a#btn_leads').trigger('click');
})

function tablero_leads() {   
    $.ajax({
     type: "POST",
     url: base_url + 'gestiones_marketing/GestionesMarketing/tablero_leads_view',
     success: function (response) {
        $("#contenido_marketing").html(response);
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
        $("#contenido_marketing").html(loading);
    }
    });
}
</script>