<style>
	#box_veriff_ws_reenvio {
		position: fixed;
		z-index: 999;
		background-color: #fff;
		text-align: center;
		border: 1px solid #eee;
		box-shadow: 0px 0px 22px -10px rgb(0 0 0 / 50%);
		transition: background 0.3s, border 0.3s, border-radius 0.3s, box-shadow 0.3s;
		top: 160px;
		left: 120px;
		width: 35%;
		padding: 10px;
		border-radius: 7px;
	}

	#box_veriff_ws_reenvio div.box-header {
		padding: 7px 10px;
		border-bottom: 1px solid #ccc;
	}

	#box_veriff_ws_reenvio div.box-header button {
		padding: 0px;
	}

	#box_veriff_ws_reenvio div.box-header div#veriff-ws-buttons {
		display: flex;
		flex-direction: row-reverse;
		padding-right: 10px;
	}

	#box_veriff_ws_reenvio div.box-header button:hover {
		box-shadow: 0px 9px 10px -9px #eee;
	}

	#box_veriff_ws_reenvio div.box-body {
		background-color: #fff;
		height: 300px;
		padding: 5px 10px;
	}

	#box_veriff_ws_reenvio #msform {
		text-align: center;
		position: relative;
		padding: 0px 10px;
	}

	#box_veriff_ws_reenvio #msform #fs-title {
		text-align: center;
    	font-size: 16px;
	}

	#box_veriff_ws_reenvio #msform #lbl_title {
		font-size: 18px;
    	line-height: 0.5;
		text-align: center;
	}

	#box_veriff_ws_reenvio #msform fieldset .form-card {
		background: white;
		border: 0 none;
		border-radius: 0px;
		padding: 10px 0px;
		box-sizing: border-box;
		margin: 0 0 20px 0;
		position: relative;
		height: auto;
		min-height: 220px;
		text-align: left;
		color: #000
	}

	#box_veriff_ws_reenvio #msform fieldset {
		background: white;
		border: 0 none;
		border-radius: 0.5rem;
		box-sizing: border-box;
		width: 100%;
		margin: 0;
		/* padding-bottom: 20px; */
		padding: 0px;
		position: relative
	}

	#box_veriff_ws_reenvio #msform fieldset .fs-title {
		font-weight: 700;
	}

	#box_veriff_ws_reenvio #msform fieldset:not(:first-of-type) {
		display: none
	}

	#box_veriff_ws_reenvio .btn-outline-success {
		color: #28a745;
		border-color: #28a745;
		background-color: #fff;
	}
	#box_veriff_ws_reenvio .btn-outline-success:hover {
		color: #fff;
		background-color: #28a745;
		border-color: #28a745;
	}
	#box_veriff_ws_reenvio .btn-outline-success:not(:disabled):not(.disabled).active, .btn-outline-success:not(:disabled):not(.disabled):active, .show>.btn-outline-success.dropdown-toggle {
		color: #fff;
		background-color: #28a745;
		border-color: #28a745;
	}

	#box_veriff_ws_reenvio .select2-search__field {
		width: auto !important;
		padding: 0px 10px;
		margin-bottom: 5px;
	} 

	#box_veriff_ws_reenvio .select2-container--default .select2-selection--multiple {
		border: 0px solid #fff;
		background-color: white;
		border-radius: 4px;
		cursor: text;
	}

	#box_veriff_ws_reenvio .select2-container--default .select2-selection--multiple .select2-selection__choice {
		background-color: white;
    	border-color: white;
    	color: black;
		padding: 1px 2px;
		margin-top: 0px;
	}

	#box_veriff_ws_reenvio .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
		margin-right: 5px;
		color: rgba(255,255,255,0.7);
	}

	#box_veriff_ws_reenvio .select2-container--default .select2-selection--multiple .select2-selection__rendered {
		display: flex;
    	flex-direction: column;
	}
	#box_veriff_ws_reenvio .select2-container--default .select2-selection--multiple .select2-selection__rendered li span {
		color: red;
		font-size: 18px;
	}
	#box_veriff_ws_reenvio .select2-container--default .select2-selection--multiple li.select2-search.select2-search--inline  {
    	border: 1px solid #ccc;
		margin-top: 20px;
	}
	
</style>
<div id="box_veriff_ws_reenvio" class="box box-info hidden">
	<input type="hidden" name="flowdata">
	<div class="box-header with-border" id="box_veriff_ws_reenvio_header">
		<div class="row">
			<div class="col-md-12">
				<div id="veriff-ws-buttons" class="btn-group col-md-12">
					<button id="btn-veriff-ws-reenvio-close" class="btn " style="background-color: #fff;"><i class="fa fa-window-close" style="color: red"></i></button>
				</div>
			</div>
		</div>
	</div>
	<div class="box-body">
		<div class="container-fluid col-md-12" id="msform">
			<fieldset id="analisis_13_front" data-analisis_13_front="">
				<div class="form-card">
					<div id="fs-title"><span class="fs-title">Selecciona porqué vuelves a solicitar la foto de la <span style="color: #00a65a">Cédula del Frente</span></span></div>
					<br>
					<div class="form-group" style="padding-top: 15px">
						<div id="lbl_title" ><label><small><?=$solicitud['nombres']; ?></label>, la foto de la cédula del frente que nos enviastes no lo podemos validar porque:</small></div>
						<div style="padding-top: 10px">
							<select class="form-control select2" multiple="multiple" data-placeholder="Buscar.." style="width: 100%;">
								<?php 
								foreach ($biometria_items[1] as $key => $value) {
									echo '<option name="id_biometria_'. $value['id'] .'" data-id_biometria="'. $value['id'] .'" value="'.$value['mensaje'].'">'.$value['mensaje'].'</option>';
								}
								?>
							</select>
						</div>
						<span id="err" style="display: none; color: red; padding-top: 10px">Debe seleccionar una opcion: </span>
						<div class="row" style="padding-top: 10px">
							<ul id="list_select"></ul>
						</div>
					</div>
				</div>
				<input type="button" name="next" id="btn_next" class="next btn btn-outline-success action-button" value="Siguiente" />
				<input type="button" name="confirm_send" id="confirm_send" class="btn btn-success action-button" value="Confirmar" />
			</fieldset>
			<fieldset id="analisis_13_back" data-analisis_13_back="">
				<div class="form-card">
					<div id="fs-title"><span class="fs-title">Selecciona porqué vuelves a solicitar la foto del <span style="color: #00a65a">Reverso de la cédula</span></span></div>
					<br>
					<div class="form-group" style="padding-top: 15px">
						<div id="lbl_title" ><label><small><?=$solicitud['nombres']; ?></label>, la foto del reverso de la cédula que nos enviastes no lo podemos validar porque:</small></div>
						<div style="padding-top: 10px">
							<select class="form-control select2" multiple="multiple" data-placeholder="Buscar.." style="width: 100%;">
								<?php 
								foreach ($biometria_items[1] as $key => $value) {
									echo '<option name="id_biometria_'. $value['id'] .'" data-id_biometria="'. $value['id'] .'" value="'.$value['mensaje'].'">'.$value['mensaje'].'</option>';
								}
								?>
							</select>
						</div>
						<span id="err" style="display: none; color: red; padding-top: 10px">Debe seleccionar una opcion: </span>
						<div class="row" style="padding-top: 10px">
							<ul id="list_select"></ul>
						</div>
					</div>
				</div>
				<input type="button" name="previous" id="btn_prev" class="previous btn btn-outline-success action-button-previous" value="Atras" />
				<input type="button" name="next" id="btn_next" class="next btn btn-outline-success action-button" value="Siguiente" />
				<input type="button" name="confirm_send" id="confirm_send" class="btn btn-success action-button" value="Confirmar" />
			</fieldset>

			<fieldset id="analisis_13_video" data-analisis_13_video="">
				<div class="form-card">
					<div id="fs-title"><span class="fs-title">Selecciona porqué vuelves a solicitar el <span style="color: #00a65a">Vídeo de verificación</span></span></div>
					<br>
					<div class="form-group" style="padding-top: 15px">
						<div id="lbl_title" ><label><small><?=$solicitud['nombres']; ?></label>, el vídeo que nos enviastes no lo podemos validar porque:</small></div>
						<div style="padding-top: 10px">
							<select class="form-control select2" multiple="multiple" data-placeholder="Buscar.."
								style="width: 100%;">
								<?php 
								foreach ($biometria_items[2] as $key => $value) {
									echo '<option name="id_biometria_'. $value['id'] .'" data-id_biometria="'. $value['id'] .'" value="'.$value['mensaje'].'">'.$value['mensaje'].'</option>';
								}
								?>
							</select>
						</div>
						<span id="err" style="display: none; color: red; padding-top: 10px">Debe seleccionar una opcion: </span>
						<div class="row" style="padding-top: 10px">
							<ul id="list_select"></ul>
						</div>
					</div>
				</div>
				<input type="button" name="previous" id="btn_prev" class="previous btn btn-outline-success action-button-previous" value="Atras" />
				<input type="button" name="confirm_send" id="confirm_send" class="btn btn-success action-button" value="Confirmar" />
			</fieldset>
		</div>
	</div>
</div>


<script type="text/javascript">
	veriff_ws_reenvio = []
	veriff_ws_reenvio.box = {
		$btnload: $('div#box_veriff_ws_reenvio'),
		$box_header: $('div#box_veriff_ws_reenvio_header', this.$btnload),
		$btn_close: $('button#btn-veriff-ws-reenvio-close', this.$box_header),
		$box_body: $('div#box_veriff_ws_reenvio div.box-body'),
		$btn_next: $('input.next', this.$box_body),
		$btn_previous: $('input.previous', this.$box_body),
		$btn_confirm_send: $('input#confirm_send', this.$box_body),
		$select2: $('select.select2', this.$box_body),
	}

	veriff_ws_reenvio.init = (flow) => {
		var current_fs, next_fs, previous_fs, opacity;
		veriff_ws_reenvio.function.dragElement(document.getElementById("box_veriff_ws_reenvio"))

		veriff_ws_reenvio.var = {
			$flow: 0,
			$message: '',
		}

		veriff_ws_reenvio.box.$btn_close.on('click', () => {
			veriff_ws_reenvio.close()
		})
		veriff_ws_reenvio.box.$btn_next.on('click', (elm) => {
			veriff_ws_reenvio.next(elm.target)
		})
		veriff_ws_reenvio.box.$btn_previous.on('click', (elm) => {
			veriff_ws_reenvio.previous(elm.target)
		})
		veriff_ws_reenvio.box.$btn_confirm_send.on('click', (elm) => {
			veriff_ws_reenvio.Send(elm.target)
		})

		veriff_ws_reenvio.box.$select2.select2({
			language: { noResults: function () { return "No se encontraron resultados"}}
		}).trigger("change");

		veriff_ws_reenvio.box.$select2.on('select2:select select2:unselect', (elm) => {
			veriff_ws_reenvio.loadSelect(elm)
		})

		veriff_ws_reenvio.loadview(flow)
		veriff_ws_reenvio.box.$btnload.removeClass('hidden');
	}

	veriff_ws_reenvio.close = () => {
		veriff_ws_reenvio.box.$btnload.addClass('hidden')
		veriff_ws_reenvio.box.$btn_close.off();
		veriff_ws_reenvio.box.$btn_next.off();
		veriff_ws_reenvio.box.$btn_previous.off();
		veriff_ws_reenvio.box.$btn_confirm_send.off();
		veriff_ws_reenvio.box.$select2.off();
		veriff_ws_reenvio.box.$select2.val(null).trigger("change");

		$("fieldset[id^='analisis_13_']").each(function (index) {
			id = $(this).attr('id')
			$(this).data(id, '')
			$(this).removeClass('activo').removeAttr('style')
			$(this).find('input').show();
		});

	}

	veriff_ws_reenvio.loadview = (flow) => {
		veriff_ws_reenvio.var.$flow = veriff_ws_reenvio.function.count_flow_active(flow)
		count = 0;
		$.each(flow, function (key, value) {
			if (value == 0) {
				$('fieldset#' + key).css({
					'display': 'none',
					'position': 'relative'
				});
			} else {
				count++;
				$('fieldset#' + key).data(key, count);
				if (count == 1) {
					$('fieldset#' + key).show();
					$('fieldset#' + key + ' input#btn_prev').hide();
				}
				if (veriff_ws_reenvio.var.$flow == 1) {
					$('fieldset#' + key + ' input#btn_next').hide();
					$('fieldset#' + key + ' input#confirm_send').show();
				} else {
					$('fieldset#' + key + ' input#confirm_send').hide();
				}
			}
		});
	}

	veriff_ws_reenvio.loadSelect = (elem) => {
		lista = "";
		id = $(elem.target).parent().parent().parent().parent().attr('id')
	}


	veriff_ws_reenvio.next = (elm) => {
		current_fs = $(elm).parent();
		if (current_fs.find(':selected').length == 0) {
			current_fs.find('span.select2-selection').animate({
				"border-color": "red"
			}, 500).delay(3000).animate({
				"border-color": "#aaa"
			}, 500);
			current_fs.find('#err').slideDown(500).delay(3000).slideUp(500);
		} else {
			next_fs = $(elm).parent().nextAll('.activo').first();
			varcount = next_fs.data(next_fs.attr('id'))

			if (veriff_ws_reenvio.var.$flow == varcount) {
				$('fieldset#' + next_fs.attr('id') + ' input#btn_next').hide();
				$('fieldset#' + next_fs.attr('id') + ' input#confirm_send').show();
			}
			next_fs.show();
			current_fs.animate({
				opacity: 0
			}, {
				step: function (now) {
					opacity = 1 - now;
					current_fs.css({
						'display': 'none',
						'position': 'relative'
					});
					next_fs.css({
						'opacity': opacity
					});
				},
				duration: 600
			});
		}
	}

	veriff_ws_reenvio.Send = (elm) => {
		current_fs = $(elm).parent();
		if (current_fs.find(':selected').length == 0) {
			current_fs.find('span.select2-selection').animate({
				"border-color": "red"
			}, 500).delay(3000).animate({
				"border-color": "#aaa"
			}, 500);
			current_fs.find('#err').slideDown(500).delay(3000).slideUp(500);
		} else {
			flowdata = {
				'analisis_13_face': 0,
				'analisis_13_front': 1,
				'analisis_13_back': 1,
				'analisis_13_video': 1,
			}

			$("#box_verif_whatsapp input:checkbox").each(function (index, elmn) {
				intentos = $(elmn).data('intentos_ws')
				estados = $(elmn).data('estadoverifws')
				if (intentos == 1 && estados != 1) {
					flowdata[$(elmn).attr('name')] = $(elmn).is(':checked') ? 1 : 2;
				} else {
					flowdata[$(elmn).attr('name')] = $(elmn).is(':checked') ? 1 : 0;
				}
			});
			countselect = 0;
			veriff_ws_reenvio.var.$message = '';
			select2 = veriff_ws_reenvio.function.unique(veriff_ws_reenvio.box.$select2.find(':selected'));
			totalselect = select2.length;
			$.each(select2, function(i, e) {
				countselect += 1;
				veriff_ws_reenvio.var.$message += e;
				if (countselect < totalselect)
					veriff_ws_reenvio.var.$message += '|';
			});

			$.ajax({
				url: base_url + 'atencion_cliente/getverifywhatsapp',
				type: 'POST',
				data: {
					id_solicitud: $("#id_solicitud").val(),
					documento: $("#client").data("number_doc"),
					telefono: $("#dato_telefono #phone").html(),
					flow: flowdata,
					action: 'reenvio_biometria_ws',
					mensaje: veriff_ws_reenvio.var.$message
				},
				success: (data) => {
					veriff_ws_reenvio.close();
					let id_operador = $("#id_operador").val();
					let id_solicitud = $("#id_solicitud").val();
					let type_contact = 193;
					let comment = "<b>[REENVIO]</b><br><b>Se vuelven a solicitar lo siguientes archivos:</b>" +
						(flowdata['analisis_13_front'] == 1 ? "<br> - Documento - Frente." : "") +
						(flowdata['analisis_13_back'] == 1 ? "<br> - Documento - Dorso." : "") +
						(flowdata['analisis_13_video'] == 1 ? "<br> - Video - selfie." : "");

					saveTrack(comment, type_contact, id_solicitud, id_operador);
					Swal.fire({
						title: 'Solicitud enviada',
						text: 'Se ha enviado la solicitud de reenvio de documentos.',
						type: 'success',
						confirmButtonText: 'Aceptar'
					});
					cargar_box_galery($("#id_solicitud").val())
				}
			})
		}
	}

	veriff_ws_reenvio.previous = (elm) => {
		current_fs = $(elm).parent();
		previous_fs = $(elm).parent().prev();
		previous_fs.show();
		current_fs.animate({
			opacity: 0
		}, {
			step: function (now) {
				opacity = 1 - now;
				current_fs.css({
					'display': 'none',
					'position': 'relative'
				});
				previous_fs.css({
					'opacity': opacity
				});
			},
			duration: 600
		});
	}

	veriff_ws_reenvio.function = {
		count_flow_active: (myObject) => {
			return Object.keys(myObject).filter(function (el) {
				return myObject[el] == 1
			}).length;
		},
		dragElement: (elemn) => {
			var pos1 = 0,
				pos2 = 0,
				pos3 = 0,
				pos4 = 0;
			document.getElementById(elemn.id + "_header").onmousedown = dragMouseDown;

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
				elemn.style.top = (elemn.offsetTop - pos2) + "px";
				elemn.style.left = (elemn.offsetLeft - pos1) + "px";
			}

			function closeDragElement() {
				document.onmouseup = null;
				document.onmousemove = null;
			}
		},
		unique: (list) => {
			var result = [];
			$.each(list, function(i, e) {
				if ($.inArray($(e).text(), result) == -1) result.push($(e).text());
			});
			return result;
		}
	}

</script>
