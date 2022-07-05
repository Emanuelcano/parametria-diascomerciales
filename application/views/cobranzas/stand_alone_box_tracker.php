<div class="tracker" >
    
</div> 

<script type="text/javascript">
    $('document').ready(function(){
		get_box_track_stand_alone(id_solicitud,id_credito);	
        var select_responses = $("#box_tracker #responses"); 
        var select_type_contact = $("#box_tracker #type_contact");
        var base_url= $("#base_url").val();
        if($('#op').val() == 6 || $('#op').val() == 5){
            $("#result_table").addClass('hide');

        }
        // CONTROL DE EVENTOS
        // 
        // Guarda un comentario.
        $("#box_tracker #save_comment").on('submit',function(event){
            event.preventDefault();
            
            let comment = $(this).find("#comment").val();
            // let id_solicitud = $("#id_solicitud").val();
            let id_operador = $(this).find("#id_operador").val();
            let type_contact = $("#type_contact").val();
            let id_campania = $("#id_campania").val();
            let response = (select_type_contact).find(':selected').data('info')+'-'+ $(select_responses).find(':selected').data('info') + comment;
               
            console.log(response, type_contact, id_solicitud, id_operador);         
            saveTrack(response, type_contact, id_solicitud, id_operador);
            if($('#op').val() == 6 || $('#op').val() == 5){
                guardarGestionOperador(id_operador, type_contact, $(select_responses).find(':selected').data('idrespuesta'), id_campania);
            }
            $("#save_comment #comment").val('');
            select_responses.val("");
            select_type_contact.val("");
        })

        // Cambios en el tipo de contacto.
        $(select_type_contact).on("change",function()
        {
            contact_id_response = $(this).find(':selected').data('response');
            if(contact_id_response !=0)
            {
                $(select_responses).find("option").each(function(index,elem) {
                $(select_responses).prop('disabled',false);
                $(select_responses).prop('required',true);
                    if( contact_id_response == $(elem).data('response'))
                    {
                       $(elem).show()
                    }else{
                      $(elem).hide()
                    }
                });
            }else{
                $(select_responses).prop('disabled',true);
                $(select_responses).prop('required',false);
            }
        });

        var channel_sol = pusher.subscribe('channel-track-'+$("#id_solicitud").val());
        channels.push(channel_sol);
        channel_sol.bind('received-track-component', function(data) {
            addElemTimeLine(data)
        });

    });

    // Agrega elementos al timeline
    function addElemTimeLine(response) {
        label_date = $("#comment_timeline li").first().text().trim();
        first_li = $("#comment_timeline li").first();
        if (response.fecha_string != label_date) {
            label = '<li class="time-label"><span class="bg-maroon">' + response.fecha_string + '</span></li>';
            first_li.before(label);
            // Sobreescribo el valor del primer elemento para insertar el comentario.
            first_li = $("#comment_timeline li").first();
        }
        first_li.after()
        text = '<li>';
        text += '<i class="fa fa ' + response.fa_icon + ' bg-' + response.color + '"></i>';
        text += '<div class="timeline-item">';
        text += '<span class="time"><i class="fa fa-clock-o"></i>' + response.hora + '</span>';
        text += '<h3 class="timeline-header">' + response.descripcion + '<a href="#"> - ' + response.operador + '</a></h3>';
        text += '<div class="timeline-body">' + response.observaciones + '</div>';
        text += '</div>';
        text += '</li>';

        first_li.after(text);
    }

</script>


<script type="text/javascript" src="<?php echo asset('assets/js/cobranzas/cobranzas.js'); ?>"></script>

<script>
  	let id_solicitud = <?=$idSolicitud?>;
	let id_credito = <?=$idCredito?>;
  
$(document).ready(function() {

$("#id_solicitud").val(id_solicitud);



// CONTROL DE EVENTOS
// 
// Guarda un comentario.
		$("#box_tracker #save_comment").on('submit',function(event){
		event.preventDefault();
		let comment = $(this).find("#comment").val();
		// let id_solicitud = $("#id_solicitud").val();
		let id_operador = $(this).find("#id_operador").val();
		let type_contact = $("#type_contact").val();
		let id_campania = $("#id_campania").val();
		let response = (select_type_contact).find(':selected').data('info')+'-'+ $(select_responses).find(':selected').data('info') + comment;            
		saveTrack(response, type_contact, id_solicitud, id_operador);
		if($('#op').val() == 6 || $('#op').val() == 5){
		guardarGestionOperador(id_operador, type_contact, $(select_responses).find(':selected').data('idrespuesta'), id_campania);
		}
		$("#save_comment #comment").val('');
		select_responses.val("");
		select_type_contact.val("");
	})
});

	/**
		 * Guarda Trackeos
		 *
		 * @param comment
		 * @param typeContact
		 * @param idSolicitude
		 * @param idOperator
		 * @param callback
		 */


		
function get_box_track_stand_alone(id_solicitud, id_credito=0) {
    var documento= $("#box_client_title").data("documento");
    $.ajax({
        url: base_url + 'gestion/Tracker/track_stand_alone/' + id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
        .done(function (response) {
            $(".tracker").html(response);
            $(".tracker #box_tracker .box-footer").css('min-height', '185px');
            $(".tracker #box_tracker .box-body").css('height', 'calc(100% - 185px)');
            get_box_whatsapp(documento);
            if(id_credito > 0){
                $('#result').addClass('hide');
                document.getElementById('id_credito').value = id_credito;
            }
            
        })
        .fail(function (response) {
        })
        .always(function () {

        });
}

</script>

