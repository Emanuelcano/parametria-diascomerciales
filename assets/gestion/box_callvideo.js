$(() => {
	gCallvideo.init();
});

gCallvideo = []
gCallvideo.links = []
gCallvideo.t = []
gCallvideo.box = {
	$btnload 			: $('#box_client_data #callvideo_btn_load'),
	$inicio_videoCall 	: $('button#inicio_llamada', this.$btnload),
	$send_videoCall_sms : $('button#send_link_sms', this.$btnload),
	$send_videoCall_ws 	: $('button#send_link_ws', this.$btnload),
	$callvideo_operator	: $('section.callvideo-operator'),
	$Box_videoLlamadas 	: $('div#box_callvideo', this.$callvideo_operator),
	$box_header 		: $('div#callvideo_buttons', this.$Box_videoLlamadas),
	$btn_minimize		: $('button#btn-callvideo-minimize', this.$box_header),
	$btn_restore		: $('button#btn-callvideo-restore', this.$box_header),
	// $btn_close			: $('button#btn-callvideo-close', this.$box_header),
	$box_body			: $('div.box-body', this.$Box_videoLlamadas),
	$div_root 			: $('div#root-iframe', this.$box_body),
}

gCallvideo.init = () => {
	dragElement(document.getElementById("box_callvideo"));
	gCallvideo.generatelink(false);

	gCallvideo.box.$inicio_videoCall.on('click',() => {
		if (gCallvideo.box.$inicio_videoCall.text() == 'Iniciar VideoLlamada')
			gCallvideo.generatelink();
		else
			gCallvideo.openvideocall()
	})

	// modal Button
	gCallvideo.box.$btn_minimize.on('click',() => {gCallvideo.minimize()})
	gCallvideo.box.$btn_restore.on('click',() => {gCallvideo.restore()})

	// panel Button
	gCallvideo.box.$send_videoCall_sms.on('click',() => {gCallvideo.send('sms')})
	gCallvideo.box.$send_videoCall_ws.on('click',() => {gCallvideo.send('ws')})
}

channelvideollamada.bind('close_room', function(data) {
	swal.fire('cerrada','Video Llamada Finalizada','success');
	gCallvideo.box.$Box_videoLlamadas.addClass('hidden')
	gCallvideo.box.$div_root.html('')
	gCallvideo.links = []
	delete var_videollamada.callWaiting[$("#id_solicitud").val()]
	gCallvideo.box.$send_videoCall_sms.prop('disabled', true)
	gCallvideo.box.$send_videoCall_ws.prop('disabled', true)
	gCallvideo.box.$inicio_videoCall.prop("disabled", false);
	gCallvideo.box.$inicio_videoCall.text('Iniciar VideoLlamada').addClass('btn-primary').removeClass('btn-success');
});

gCallvideo.generatelink = (newtoken = true) => {
	$.ajax({
		type: "POST",
		url: base_url + 'video_llamadas/get_token',
		data: {documento: $("#client").data("number_doc"), newtoken : newtoken , id_solicitud : $("#id_solicitud").val()},
		success: function(r) {
			if (r.status) {				
				gCallvideo.links = r.link
				gCallvideo.t = r.t
				gCallvideo.box.$send_videoCall_sms.prop('disabled', !gCallvideo.t.sms)
				gCallvideo.box.$send_videoCall_ws.prop('disabled', !gCallvideo.t.ws)
				gCallvideo.box.$inicio_videoCall.text('Abrir VideoLlamada').removeClass('btn-primary').addClass('btn-success');
				if (var_videollamada.callWaiting.length > 0)
					if (var_videollamada.callWaiting[$("#id_solicitud").val()].status)
						gCallvideo.openvideocall()
			}
		}
	});
}


gCallvideo.minimize = () => {
	gCallvideo.box.$Box_videoLlamadas.animate({width: '15%'}, 1000);
	gCallvideo.box.$box_header.removeClass('col-md-2 col-md-offset-4').addClass('col-md-3 col-md-offset-3');
	gCallvideo.box.$btn_minimize.addClass('hidden')
	gCallvideo.box.$btn_restore.removeClass('hidden')
}

gCallvideo.restore = () => {
	gCallvideo.box.$Box_videoLlamadas.animate({width: '33%'}, 1000);
	gCallvideo.box.$box_header.addClass('col-md-2 col-md-offset-4').removeClass('col-md-3 col-md-offset-3');
	gCallvideo.box.$btn_minimize.removeClass('hidden')
	gCallvideo.box.$btn_restore.addClass('hidden')
}

gCallvideo.send = (action) => {
		formData = new FormData();
		formData.append("documento", $("#client").data("number_doc"));
		formData.append("id_solicitud", $("#id_solicitud").val());
		formData.append("l", gCallvideo.links.cliente)
		formData.append("action", action)
		$.ajax({
			url: base_url + 'video_llamadas/enviar_link',
			type: 'POST',
			data : formData,
			processData: false,
			contentType: false,
		}).done(function(response) {
			if (response.status.code == 200) {
				if (typeof response.sms != 'undefined') {
					if (response.sms.success == true)
						swal.fire('Enviada',response.sms.msj,'success');
				}
				if (typeof response.ws != 'undefined') {
					swal.fire('Enviada',response.ws.msj,'success');
				}
			} else 
				swal.fire('Error',"error en el envio",'warning');
		});
	}

	gCallvideo.close_solicitud = () => {
		$.ajax({
			url: base_url + 'video_llamadas/get_status_videoCall',
			type: 'POST',
			data: {documento: $("#client").data("number_doc")},
			success: (data) => {
				if (data.activeRoom == 1) {
					swal.fire({		
						title: "ALTO",
						text: "Esta Saliendo de una llamada Activa, Esta Seguro?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#3085d6",
						cancelButtonColor: "#d33",
						confirmButtonText: "Si"
					}).then(function (result) {
						if (result.value) {
							$.ajax({
								url: base_url + 'video_llamadas/cierre', type: 'POST', data: data,
								success: (response) => {
									delete var_videollamada.callWaiting[$("#id_solicitud").val()]
									close_solicitude()
								}
							})
						}
					})
				} else {
					gCallvideo.box.$Box_videoLlamadas.addClass('hidden')
					gCallvideo.box.$div_root.html('')
				}
	
			}
		})
	}

gCallvideo.closeSolicitud = () => {
	swal.fire('Aviso','Validando que no tenga llamada activa','warning');
}

gCallvideo.openvideocall = () => {
	gCallvideo.box.$inicio_videoCall.prop("disabled", true);
	gCallvideo.box.$Box_videoLlamadas.removeClass('hidden')
	gCallvideo.box.$div_root.html('').append('<iframe src="' + gCallvideo.links.operador + '" frameborder="0" style="height: 600px;" class="responsive-iframe" allow="camera;microphone"></iframe>')
}

function dragElement(elmnt) {
	var pos1 = 0,
		pos2 = 0,
		pos3 = 0,
		pos4 = 0;
	if (document.getElementById("titulo " + elmnt.id + "_header")) {
		document.getElementById("titulo " + elmnt.id + "_header").onmousedown = dragMouseDown;
	} else {
		elmnt.onmousedown = dragMouseDown;
	}

	function dragMouseDown(e) {
		e = e || window.event;
		e.preventDefault();
		pos3 = e.clientX;
		pos4 = e.clientY;
		document.onmouseup = closeDragElement;
		document.onmousemove = elementDrag;
	}

	function elementDrag(e) {
		e = e || window.event;
		e.preventDefault();
		pos1 = pos3 - e.clientX;
		pos2 = pos4 - e.clientY;
		pos3 = e.clientX;
		pos4 = e.clientY;
		elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
		elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
	}

	function closeDragElement() {
		document.onmouseup = null;
		document.onmousemove = null;
	}
}
