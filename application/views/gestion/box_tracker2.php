<div id="box_tracker" class="box box-info" style="height: 102%; padding:0px; ">
    <input id="base_url" type="hidden" name="base_url" value="<?php echo base_url()?>">
    <input id="op" type="hidden" value="<?php echo $this->session->userdata['tipo_operador']; ?>">
    <div class="box-header with-border"></div><!-- end box-header -->
    <div class="box-body"  style="overflow-y: auto; max-height: 500px">
        <div class="tab-pane active" id="timeline">
                <!-- The timeline -->
            <ul id="comment_timeline" class="timeline timeline-inverse">

              <!-- timeline time label -->
                <?php foreach($tracker['tracks'] as $date => $registers):?>
                    <li class="time-label">
                        <span class="<?php echo $registers['style']?>"><?php echo $date; ?></span>
                    </li>
                    <?php foreach($registers['tracks'] as $date => $track):?>
                        <li>
                            <i class="fa <?php echo 'fa '.$track['fa_icon'].' bg-'.$track['color'];?>"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> <?php echo $track['hora']; ?></span>

                                <h3 class="timeline-header"><?php echo $track['descripcion']; ?><a href="#"><?php echo ' - '.$track['operador'];?></a> </h3>

                                <div class="timeline-body">
                                    <?php echo $track['observaciones'];?>
                                </div>
                                <!-- <div class="timeline-footer">
                                    <a class="btn btn-primary btn-xs">Read more</a>
                                    <a class="btn btn-danger btn-xs">Delete</a>
                                </div> -->
                            </div>
                        </li>
                    <?php endforeach;?>
                <?php endforeach;?>
                <li><i class="fa fa-clock-o bg-gray"></i></li>
            </ul><!-- END timeline-inverse -->
        </div>
    </div><!-- end box-body -->
    <input type="hidden" id="id_credito">
    <div class="box-footer" style="height: 20%">
        <form id="save_comment" class="form-horizontal" action="#">
            <label>Comentario:</label>
            <textarea id="comment" class="textarea" style="width: 100%; height: 70px; font-size: 14px; line-height: 18px; border: 1px solid rgb(221, 221, 221); padding: 10px; resize: none;"></textarea>
            <div class="col-sm-5">
                <label class="">Forma de contacto:</label>
                <select id="type_contact"  class="form-control" required="required">
                    <option value="" selected data-info="">.:Seleccione una opción:.</option>
                    <?php foreach ($tracker['actions'] as $key => $action): ?>
                            <option value="<?php echo$action['id'] ?>" data-response="<?php echo $action['idgrupo_respuesta']?>" data-info="<?php echo '['.$action['etiqueta'].']'; ?>"><?php echo $action['etiqueta'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-sm-5">
                <label class="">Respuestas:</label>
                <select id="responses"  class="form-control" disabled required="required">
                    <option value="" disabled selected data-info="">.:Seleccione una opción:.</option>
                    <?php foreach ($tracker['actions'] as $key => $action): ?>
                        <?php if($action['idgrupo_respuesta'] !=0 ):?>
                            <?php foreach ($action['options'] as $key => $option): ?>
                                    <option value="<?php echo $action['id'] ?>" data-idRespuesta="<?=$option['iddetalle_respuesta']?>" data-response="<?php echo $action['idgrupo_respuesta']; ?>" data-info="<?php echo '['.$option['denominacion'].']'; ?>"><?php echo $option['denominacion'] ?></option>
                            <?php endforeach ?>
                        <?php endif; ?>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-sm-2" style="margin-top: 25px; margin-left: -15px;">
                <button id="btn_save_comment" class="btn btn-sm btn-success align-bottom">Comentar</button>
            </div>
            <input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>">
        </form>
    </div><!-- end box-footer -->
</div> <!-- end box-default -->

<script type="text/javascript">
    $('document').ready(function(){
        select_responses = $("#box_tracker #responses"); 
        select_type_contact = $("#box_tracker #type_contact");
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
            let id_operador = $(this).find("#id_operador").val();
            let type_contact = $("#type_contact").val();
            let id_campania = $("#id_campania").val();
            let response = (select_type_contact).find(':selected').data('info')+'-'+ $(select_responses).find(':selected').data('info') + comment; 
            // alert("AQUI COMENTARIOS:"+  id_solicitud+" "+id_credito);
            saveTrack2(response, type_contact, id_solicitud, id_operador, ()=>{get_box_track_stand_alone(id_solicitud,id_credito)});
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

    
    function saveTrack2(comment, typeContact, idSolicitude, idOperator, callback) {
	$('#btn_save_comment').addClass('disabled');
    console.log({'observaciones':comment, 'id_tipo_gestion':typeContact, 'id_solicitud':idSolicitude, 'id_operador':idOperator});
	$.ajax({
		url: base_url + 'api/track_gestion',
		type: 'POST',
		dataType: 'json',
		data: {
			'observaciones': comment,
			'id_tipo_gestion': typeContact,
			'id_solicitud': idSolicitude,
			'id_operador': idOperator
		}
	}).always(callback);
}


function guardarGestionOperador(id_operador, id_gestion, idDetalleRespuesta, id_campania) {
	let base_url = $("#base_url").val();
	var id_credito = $('#id_credito').val();
	var id_operador = id_operador;
	var id_gestion = id_gestion;
    console.log(id_credito,id_operador,id_gestion)
	$.ajax({
		url: base_url + 'api/campanias/consultar_asigando_operador',
		type: 'POST',
		data: {'id_operador': id_operador, 'id_credito': id_credito}
	}).done(function (response) {
		if (response.data != '') {
            get_box_track_stand_alone(id_solicitud,id_credito);
			$.ajax({
				url: base_url + 'api/campanias/guardarGestionOperador',
				type: 'POST',
				data: {
					'id_credito': id_credito,
					'id_operador': id_operador,
					'id_gestion': id_gestion,
					'idDetalleRespuesta': idDetalleRespuesta,
					'id_campania': id_campania
				}
			})
				.done(function (response) {})
				.fail(function (response) {})
				.always(function () {
					$('#btn_save_comment').removeClass('disabled');
				});
		}
	})
		.fail(function (response) {})
}

</script>
