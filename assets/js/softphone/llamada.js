var neotellupcasos = null;
let USUARIO = $("input#hdd_id_agente_n").val();
let TIPO_OPERADOR = $("input#hdd_tipo_operador").val();
let actividad_caso = $("input#hdd_leyendo_caso").val();
let centrales_idoperador = (USUARIO != undefined && USUARIO != "") ? USUARIO : datos_centrales.id_agente_neotell
let centrales_tipoopeador = (TIPO_OPERADOR != undefined && TIPO_OPERADOR != "") ? TIPO_OPERADOR : datos_centrales.tipo_operador

/* 
	Inicio de bloque de levatamiento de casos neotell
 */
function iniciarLoopLLamada() {

	// console.log(sessionStorage.switch_valor)
	neotellupcasos = setInterval(function () {

		$.ajax({
			type: "POST",
			//data:{USUARIO:centrales_idoperador,bandera:1},//PRUEBA CASO RESPONSE CONSTRUIDA
			data: { USUARIO: centrales_idoperador,server: sessionStorage.switch_valor},
			url: base_url + "api/ApiSupervisores/up_casos_neotell",
			success: function (res) {
				var respuesta = JSON.parse(res);
				//  console.log(respuesta.respuesta_cadena);

				if (respuesta.respuesta_cadena == "Operador asignado a campaña de chat no disponible para llamadas") {
					console.log(respuesta.respuesta_cadena);
					$("#span_estado").text("DESCONECTADO");
				}else if (respuesta.respuesta_cadena == "Agente no logeado en neotell") {
					console.log(respuesta.respuesta_cadena);
					$("#span_estado").text("DESCONECTADO");
				}else if (respuesta.respuesta_cadena == "Posibles causas del problema: Sin licencias activas ,sin conexion a internet o Sin usuario en la tabla de track_operadores de telefoniaAgente no logeado en neotell") {
					console.log(respuesta.respuesta_cadena);
					$("#span_estado").text("DESCONECTADO");
				}else if (typeof respuesta.redirect_url !== "undefined") {
					$("#span_estado").text("MOSTRANDO");
					console.log(respuesta.redirect_url)					
					console.log(respuesta.respuesta_cadena)					
					window.location.replace(base_url + "atencion_cobranzas/renderCobranzas");
				} else {
					$("#span_estado").text("DISPONIBLE");

					//console.log(res+" ejecuto 2")
					// console.log(respuesta.respuesta_cadena);

				}
			}, error: function (res) {
				console.log(res.responseText);
				console.log("no perteneces al tramo de red para comunicarte con neotell");
			}

		});

	}, 1000);
}


function detenerLoopLLamada() {
	clearInterval(neotellupcasos);

	$.ajax({

		type: "POST",
		crossDomain: true,
		//async:true,
		//dataType: 'jsonp',
		url: $("input#base_url").val() + 'api/ApiSupervisores/cerrar_caso',
		data: { server : sessionStorage.switch_valor },
		beforesend: function () {

		},
		success: function (res) {

			if (res == "CASO CERRADO") {
				if (neotellupcasos != null) {


				}
				//iniciarLoopLLamada();

			}

		}
	});
}

$(document).ready(function () {
	if (sessionStorage.switch_valor == "activo_neotell") {
		console.log(actividad_caso == "0")
		console.log(centrales_tipoopeador == "6")
		console.log(centrales_tipoopeador == "9")

		if (actividad_caso == "0" && (centrales_tipoopeador == "6" || centrales_tipoopeador == "9" || centrales_tipoopeador == "18")) {

			iniciarLoopLLamada();

		}
	}else if (sessionStorage.switch_valor == "activo_neotell_colombia") {
		if (actividad_caso == "0" && (centrales_tipoopeador == "6" || centrales_tipoopeador == "9" || centrales_tipoopeador == "18")) {

			iniciarLoopLLamada();

		}
	}else if (sessionStorage.switch_valor == "activo_twilio") {
		// alert("AQAWE2")
		// startupClient()
	}


	/*
	|--------------------------------------------------------------------------
	| Libreria wolkvox Area de Componente llamadas Ing. Esthiven Garcia
	|--------------------------------------------------------------------------
	*/

	/*
	|-------------------------------------------------------------------------------------------------------------------
	| Area de Reportes Ing. Esthiven Garcia
	| esta seccion consume las acciones de integracion en componente de llamadas como llamar colgar mute etc.
	| Api documentacion link: https://www.wolkvox.com/apis.php seccion API Agentes.
	|-------------------------------------------------------------------------------------------------------------------
	*/


	/*
	|-----------------------------------------------------------------------------------------
	| Metodo que capta el presionar los botones numericos para establecer comandos en llamada
	|-----------------------------------------------------------------------------------------
	*/

	$('body').on('click', '.num_press', function (event) {
		event.preventDefault();
		var num_press = $(this).attr('value');
		$.ajax({
			type: "GET",
			crossDomain: true,
			dataType: 'jsonp',
			url: 'http://localhost:8084/apiagentbox?action=keyp&key=' + num_press,
			beforesend: function () {
				request.setRequestHeader("Access-Control-Allow-Origin", '*');
			},
			success: function (res) {

				console.log(res.status);


			}
		});

	});

	/*
	$('body').on('click','.num_press',function(event){
		event.preventDefault();
		var num_press = $(this).attr('value');
	   $.getJSON('http://localhost:8084/apiagentbox?action=keyp&key='+num_press, function (res)
	   {
			 res=JSON.stringify(res);
			 var status = JSON.parse(res);
		   
		   Swal.fire("Llamada!", status, "info");
		   
		   if(status.ok != ""){
		   	
				  Swal.fire("Llamada!", "Numero digitado "+status+" al agente: "+num_press, "success");
			 
		   }
		 });
	 });*/



	/*$('body').on('click','.num_press',function(event){
			event.preventDefault();
			var num_press = $(this).attr('value');
			console.log(num_press);
		$.ajax({
			url: $("input#base_url").val()+'softphone/Llamada/marcar_numero',
			type:'POST',
			data:{ "num_press" : num_press },
			success:function(respuesta){
			  
			  //swal("info!", "Numero comando marcado:"+num_press + " " +respuesta, "info");
	
			}
		});
	});*/

	$("#btn_transferirori").click(function (event) {
		event.preventDefault();

		let num_trans = $("#txt_num_trans").val();
		console.log(num_trans);


		if (sessionStorage.switch_valor == "activo_wolkvox") {

			$.ajax({
				url: $("input#base_url").val() + 'softphone/Llamada/transferir_llamada',
				type: 'POST',
				data: { "num_trans": num_trans },
				success: function (respuesta) {

					swal("info!", "Llamada transferida a operador:" + num_trans + " " + respuesta, "info");

				}
			});
		} else if (sessionStorage.switch_valor == "activo_neotell") {

			Swal.fire("Accion inhabilitada con neotell!", "Solo se pueden trasferir llamadas en el softphone opcion *12.", "info");

		}
	});

	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de trasnferencia de llamada optiene el numero en el campo txt_num_trans para realizar la redireccion de llamada
	|-----------------------------------------------------------------------------------------------------------------------
	*/

	$("#btn_transferir").click(function (event) {
		event.preventDefault();
		let num_trans = $("#txt_num_trans").val();

		if (num_trans == "") {
			Swal.fire("Verifique!", "Debe indicar un numero telefonico para realizar la transferencia de la llamada", "error");
		} else {

			if (sessionStorage.switch_valor == "activo_wolkvox") {

				$.ajax({
					type: "GET",
					crossDomain: true,
					dataType: 'jsonp',
					url: 'http://localhost:8084/apiagentbox?action=tran&phone=' + num_trans,
					beforesend: function () {
						request.setRequestHeader("Access-Control-Allow-Origin", '*');
					},
					success: function (res) {


						if (res.status === "ok") {

							Swal.fire("Llamada!", "Su llamada fue trasnferida " + res.status + " al operador: " + num_trans, "success");

						}
					}
				});
			} else if (sessionStorage.switch_valor == "activo_neotell") {

				Swal.fire("Accion inhabilitada con neotell!", "Solo se pueden trasferir llamadas en el softphone opcion *12.", "info");

			}
		}
	});

	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de llamada optiene el numero en el campo txt_num_man para realizar la redireccion de llamada manual
	|-----------------------------------------------------------------------------------------------------------------------
	*/

	$("#btn_call").click(function (event) {
		event.preventDefault();


		var txtTelefono = $('#txt_num_man').val();
		var id_customer = "12700";
		if (typeof sessionStorage.switch_valor  === 'undefined')
		{
			Swal.fire("Verifique!", "Debe indicar una Central telefonica para realizar la llamada", "error");
			return;
		}
		if (txtTelefono == "") {
			Swal.fire("Verifique!", "Debe indicar un numero telefonico para realizar la llamada", "error");
		} else {
			if (sessionStorage.switch_valor == "activo_wolkvox") {

				$.ajax({
					type: "GET",
					crossDomain: true,
					dataType: 'jsonp',
					url: 'http://localhost:8084/apiagentbox?action=dial&phone=' + txtTelefono + '&id_customer=' + id_customer + '&callback=?',
					beforesend: function () {
						request.setRequestHeader("Access-Control-Allow-Origin", '*');
					},
					success: function (res) {
						res = JSON.stringify(res);
						var status = JSON.parse(res);

						Swal.fire("Llamada!", status.status, "info");

						if (status.id_call != "") {

							Swal.fire("Llamada!", "Su ID de llamada es" + status.id_call, "success");

						}
					}
				});
			} else if (sessionStorage.switch_valor == "activo_neotell") {



				$.ajax({
					type: "POST",
					//dataType: 'jsonp',
					data: { USUARIO: centrales_idoperador, TELEFONO: txtTelefono , server: sessionStorage.switch_valor},
					url: base_url + "api/ApiSupervisores/LLAMADA_NEOTELL",
					success: function (res) {
						console.log(res)
					}
				});
				Swal.fire("Llamada en curso!", "llamada al numero: " + txtTelefono, "info");
				$("#"+btn_call_action_id).css({'background-color': '#ba0000', 'color': 'white'});
				// $("#"+btn_call_action_id).css({'background-color': '#ba0000', 'color': 'white'});
				toastr["success"](`Realizando llamada al numero: ${params.To}`, "Llamando");
				sessionStorage.llamada_saliente = 1;

			} else if (sessionStorage.switch_valor == "activo_neotell_colombia") {



				$.ajax({
					type: "POST",
					//dataType: 'jsonp',
					data: { USUARIO: centrales_idoperador, TELEFONO: txtTelefono , server: sessionStorage.switch_valor },
					url: base_url + "api/ApiSupervisores/LLAMADA_NEOTELL",
					success: function (res) {
						console.log(res)
					}
				});
				Swal.fire("Llamada en curso!", "llamada al numero: " + txtTelefono, "info");
				$("#"+btn_call_action_id).css({'background-color': '#ba0000', 'color': 'white'});
				// $("#"+btn_call_action_id).css({'background-color': '#ba0000', 'color': 'white'});
				toastr["success"](`Realizando llamada al numero: ${params.To}`, "Llamando");
				sessionStorage.llamada_saliente = 1;


			}

		}
	});


	$("#btn_callsecond").click(function (event) {
		event.preventDefault();
		var txtTelefono = $('#txt_num_man').val();
		var id_customer = "12700";

		$.getJSON('http://localhost:8084/apiagentbox?action=dial&phone=' + txtTelefono + '&id_customer=' + id_customer + '&callback=?', function (res) {
			res = JSON.stringify(res);
			var status = JSON.parse(res);

			Swal.fire("Llamada!", status.status, "info");

			if (status.id_call != "") {

				Swal.fire("Llamada!", "Su ID de llamada es" + status.id_call, "success");

			}
		});
	});

	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de reprogración de la llamada discada por predictivo o campaña este metodo genera dos campos de texto para 
	| obtener los datos requeridos para la reprogramación tambien se validan los datos bien antes de repregramar.
	|-----------------------------------------------------------------------------------------------------------------------
	*/

	$("#btn_reprograming").click(function (event) {
		//$('body').on('click','#myModal button[id="btn_call"]',function(event){
		event.preventDefault();
		//var slc_tiporepo = $("#slc_tiporepo").val();


		var txt_num_man = $("#txt_num_man").val();


		if (txt_num_man == "") {
			Swal.fire("Verifique!", "Debe indicar un numero telefonico para realizar la llamada", "error");
		} else {

			if (sessionStorage.switch_valor == "activo_wolkvox") {
				Swal.fire({

					title: 'Reprogramación de Llamada',
					type: 'info',
					html: '<div class="row"><div class="col-md-6"><p><label class="control-label">Fecha <star>*</star></label><input type="text" name="input-field" id="input-field"  class="form-control datetimepicker" placeholder="Fecha ..."/></p></div><div class="col-md-6"><p><label class="control-label">Hora <star>*</star></label><input type="text" name="input-field2" id="input-field2"  class="form-control timepicker" placeholder="Fecha ..."/></p></div></div>',
					showCloseButton: true,
					focusConfirm: false,
					showCancelButton: true,
					closeOnConfirm: false,
					allowOutsideClick: false,
					customClass: 'swal2-overflow',
					onOpen: function () {
						var dateToday = new Date();
						$('.datetimepicker').datepicker({
							minDate: dateToday,
							tooltips: {
								today: 'Ve a la fecha actual',
								clear: 'Limpiar Selecion',
								close: 'Cerrar Caledario',
								selectMonth: 'Seleccione Mes',
								prevMonth: 'Mes Anterior',
								nextMonth: 'Mes Siguiente',
								selectYear: 'Selecione un Año',
								prevYear: 'Año Anterior',
								nextYear: 'Año Siguiente',
								selectDecade: 'Seleccione Decada',
								prevDecade: 'Decada Anterior',
								nextDecade: 'Siguiente Decada',
								prevCentury: 'Previous Century',
								nextCentury: 'Next Century'
							},
							icons: {

								time: "fa fa-clock-o",
								date: "fa fa-calendar",
								up: "fa fa-chevron-up",
								down: "fa fa-chevron-down",
								previous: 'fa fa-chevron-left',
								next: 'fa fa-chevron-right',
								today: 'fa fa-screenshot',
								clear: 'fa fa-trash',
								close: 'fa fa-remove'
							},
							dateFormat: 'yy-mm-dd'
						});

						$('.timepicker').timepicker({
							minDate: dateToday,
							showMeridian: false
						});
					},
				}).then((result) => {




					var fecha = $('#input-field').val();
					var hora = $('#input-field2').val();
					var fecx = fecha.split('-');
					var horx = hora.split(':');
					var fechacomp = fecx[0] + fecx[1] + fecx[2] + horx[0] + horx[1];

					console.log(fechacomp);


					if (txt_num_man == "") {
						Swal.fire("Verifique!", "Debe indicar un numero telefonico para realizar la llamada", "error");
					} else {
						$.ajax({
							type: "GET",
							crossDomain: true,
							dataType: 'jsonp',
							url: 'http://localhost:8084/apiagentbox?action=rcal&date=' + fechacomp + '&dial=' + txt_num_man + '&type_recall=auto',
							beforesend: function () {
								request.setRequestHeader("Access-Control-Allow-Origin", '*');
							},
							success: function (res) {

								if (res.status === "ok") {

									Swal.fire("Llamada!", "Su llamada ha sido reprogramada " + res.status + " en fecha y hora señalada", "success");

								}
							}
						});
					}

				})

			} else if (sessionStorage.switch_valor == "activo_neotell") {

				Swal.fire("Accion inhabilitada con neotell!", "Solo se pueden reprogramar llamadas en el softphone opcion *12.", "info");

			}
		}


	});



	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de colgar la llamada 
	|-----------------------------------------------------------------------------------------------------------------------
	*/
	$("#btn_hang").click(function (event) {
		event.preventDefault();
		if (sessionStorage.switch_valor == "activo_wolkvox") {

			$.ajax({
				type: "GET",
				dataType: 'jsonp',
				url: 'http://localhost:8084/apiagentbox?action=haup',
				beforesend: function () {
					request.setRequestHeader("Access-Control-Allow-Origin", '*');
				},
				success: function (res) {
					console.log(res);

					if (res.status === "ok") {

						Swal.fire("Llamada!", "Su llamada ha sido colgada", "success");

					}
				}
			});
		} else if (sessionStorage.switch_valor == "activo_neotell") {


			$.ajax({
				type: "POST",
				data: { USUARIO: centrales_idoperador ,server: sessionStorage.switch_valor},
				url: base_url + "api/ApiSupervisores/COLGAR_NEOTELL",
				success: function (res) {
					console.log(res)

					$.ajax({
						type: "POST",
						data: { USUARIO: centrales_idoperador , server : sessionStorage.switch_valor},
						url: base_url + "api/ApiSupervisores/DISPONIBLE_NEOTELL",
						success: function (res) {
							console.log(res)
							Swal.fire("Llamada!", "Su llamada ha sido colgada", "success");
							$("#body_status_call").text("   Colgando...");
							sessionStorage.llamada_saliente = 0;

						}
					});

					
				}
			});

			Swal.fire("Llamada!", "Su llamada ha sido colgada", "success");

		} else if (sessionStorage.switch_valor == "activo_neotell_colombia") {


			$.ajax({
				type: "POST",
				data: { USUARIO: centrales_idoperador , server: sessionStorage.switch_valor},
				url: base_url + "api/ApiSupervisores/COLGAR_NEOTELL",
				success: function (res) {
					console.log(res)

					$.ajax({
						type: "POST",
						data: { USUARIO: centrales_idoperador , server : sessionStorage.switch_valor},
						url: base_url + "api/ApiSupervisores/DISPONIBLE_NEOTELL",
						success: function (res) {
							console.log(res)
							Swal.fire("Llamada!", "Su llamada ha sido colgada", "success");
							$("#body_status_call").text("   Colgando...");
							sessionStorage.llamada_saliente = 0;

						}
					});

					
				}
			});

			Swal.fire("Llamada!", "Su llamada ha sido colgada", "success");

		}else if (sessionStorage.switch_valor == "activo_twilio"){
			//  alert("prueba")
			
		}






	});

	$("#btn_hangori").click(function (event) {

		event.preventDefault();


		$.ajax({
			type: "POST",
			url: $("input#base_url").val() + 'softphone/Llamada/colgar_llamada',

			success: function (response) {
				if (response == "error al colgar") {
					Swal.fire("Verifique!", "La llamada ha sido colgada", "success");

				} else {
					Swal.fire("Verifique!", response, "error");

				}
			}
		});



	});

	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de mutear la llamada 
	|-----------------------------------------------------------------------------------------------------------------------
	*/

	$("#btn_mute").click(function (event) {
		event.preventDefault();

		if (sessionStorage.switch_valor == "activo_wolkvox") {

			$.ajax({
				type: "GET",
				dataType: 'jsonp',
				url: 'http://localhost:8084/apiagentbox?action=mute',
				beforesend: function () {
					request.setRequestHeader("Access-Control-Allow-Origin", '*');
				},
				success: function (res) {
					console.log(res);

					if (res.status === "ok") {

						Swal.fire("Llamada!", "Su llamada ha sido muteada", "success");

					}
				}
			});
		} else if (sessionStorage.switch_valor == "activo_neotell") {

			Swal.fire("Accion inhabilitada con neotell!", "Solo con el control de mute de la vincha es posible mutear llamadas en neotell o directamente en el softphone", "info");

		}



	});

	$("#btn_muteori").click(function (event) {

		event.preventDefault();


		$.ajax({
			type: "POST",
			url: $("input#base_url").val() + 'softphone/Llamada/mutear_llamada',

			success: function (response) {
				if (response == "Muteado") {
					Swal.fire("Verifique!", "La llamada ha sido muteada", "success");

				} else {
					Swal.fire("Verifique!", response, "error");

				}
			}
		});
	});

	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de desmutear la llamada 
	|-----------------------------------------------------------------------------------------------------------------------
	*/
	$("#btn_desmute").click(function (event) {
		event.preventDefault();

		if (sessionStorage.switch_valor == "activo_wolkvox") {

			$.ajax({
				type: "GET",
				dataType: 'jsonp',
				url: 'http://localhost:8084/apiagentbox?action=mute',
				beforesend: function () {
					request.setRequestHeader("Access-Control-Allow-Origin", '*');
				},
				success: function (res) {
					console.log(res);

					if (res.status === "ok") {

						Swal.fire("Llamada!", "Su llamada ha sido desmuteada", "success");

					}
				}
			});
		} else if (sessionStorage.switch_valor == "activo_neotell") {

			Swal.fire("Accion inhabilitada con neotell!", "Solo con el control de desmute de la vincha es posible mutear llamadas en neotell o directamente en el softphone", "info");

		}



	});

	$("#btn_desmuteori").click(function (event) {

		event.preventDefault();


		$.ajax({
			type: "POST",
			url: $("input#base_url").val() + 'softphone/Llamada/desmutear_llamada',

			success: function (response) {
				if (response == "Desmuteado") {
					Swal.fire("Verifique!", "La llamada ha sido Desmuteada", "success");

				} else {
					Swal.fire("Verifique!", response, "error");

				}
			}
		});
	});


	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de colocar la llamada en espera
	|-----------------------------------------------------------------------------------------------------------------------
	*/

	$("#llamada_en_espera").click(function (event) {
		event.preventDefault();
		$.ajax({
			type: "GET",
			dataType: 'jsonp',
			url: 'http://localhost:8084/apiagentbox?action=hold',
			beforesend: function () {
				request.setRequestHeader("Access-Control-Allow-Origin", '*');
			},
			success: function (res) {
				console.log(res);

				if (res.status === "ok") {

					Swal.fire("Llamada!", "Su llamada esta en espera", "success");

				}
			}
		});



	});

	$("#llamada_en_esperaori").click(function (event) {

		event.preventDefault();


		$.ajax({
			type: "POST",
			url: $("input#base_url").val() + 'softphone/Llamada/llamada_en_espera',

			success: function (response) {
				if (response == "llamada en espera") {
					Swal.fire("Verifique!", "La llamada ha sido Desmuteada", "success");

				} else {
					Swal.fire("Verifique!", response, "error");

				}
			}
		});
	});

	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de codificar la llamada la cuelga  y la coloca el status del operador en ready
	|-----------------------------------------------------------------------------------------------------------------------
	*/

	$("#btn_codready").click(function (event) {

		event.preventDefault();
		let base_url = $("input#base_url").val();
		var cod1 = $("#exampleFormControlSelect1").val();
		var commets = $("#exampleFormControlTextarea1").val().toUpperCase();
		let id_solicitud = $("#id_solicitud").val();
		let id_operador = $("section").find("#id_operador").val();
		let type_contact = 3; // base gestion -> botones operador 
		let comment = "<b>[" + commets + "]</b>";


		if (commets == "") {
			Swal.fire("Verifique!", "Debe indicar un comentario para codificar la llamada", "error");
		} else if (id_solicitud) {

			let base_url = $("input#base_url").val();
			saveTrack(comment, type_contact, id_solicitud, id_operador);

			
			if (sessionStorage.switch_valor == "activo_wolkvox") {

				$.ajax({
					type: "GET",
					dataType: 'jsonp',
					url: 'http://localhost:8084/apiagentbox?action=chur&cod=' + cod1 + '&cod2=1&comm=' + commets,
					beforesend: function () {
						request.setRequestHeader("Access-Control-Allow-Origin", '*');
					},
					success: function (res) {
						console.log(res);
	
						if (res.status === "ok") {
	
							Swal.fire("Llamada!", "Su llamada ha sido codificada", "success");
							window.open(base_url + "dashboard", "_self", "");
							window.close();
	
						} else {
							Swal.fire("Verifique!", res, "error");
	
						}
					}
				});

			} else if (sessionStorage.switch_valor == "activo_neotell") {


				$.ajax({
					type: "POST",
					data: { USUARIO: centrales_idoperador , server : sessionStorage.switch_valor},
					url: base_url + "api/ApiSupervisores/DISPONIBLE_NEOTELL",
					success: function (res) {
						console.log(res)
					}
				});

				Swal.fire("Llamada!", "Su llamada ha sido codificada", "success");
				window.location.replace(base_url + "dashboard");
				window.close();

			} else if (sessionStorage.switch_valor == "activo_neotell_colombia") {


				$.ajax({
					type: "POST",
					data: { USUARIO: centrales_idoperador , server : sessionStorage.switch_valor},
					url: base_url + "api/ApiSupervisores/DISPONIBLE_NEOTELL",
					success: function (res) {
						console.log(res)
					}
				});

				Swal.fire("Llamada!", "Su llamada ha sido codificada", "success");
				window.location.replace(base_url + "dashboard");
				window.close();

			}
		} else {
			console.log("estoy aqui");


			$.ajax({
				type: "GET",
				dataType: 'jsonp',
				url: 'http://localhost:8084/apiagentbox?action=chur&cod=' + cod1 + '&cod2=1&comm=' + commets,
				beforesend: function () {
					request.setRequestHeader("Access-Control-Allow-Origin", '*');
				},
				success: function (res) {
					console.log(res);

					if (res.status === "ok") {

						Swal.fire("Llamada!", "Su llamada ha sido codificada", "success");
						window.open(base_url + "dashboard", "_self", "");
						window.close();

					} else {
						Swal.fire("Verifique!", res, "error");

					}
				}
			});




		}


	});

	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de codificar la llamada la cuelga pero permite una llamada auxiliar
	|-----------------------------------------------------------------------------------------------------------------------
	*/

	$("#btn_cod").click(function (event) {

		event.preventDefault();
		let base_url = $("input#base_url").val();
		var cod1 = $("#exampleFormControlSelect1").val();
		var commets = $("#exampleFormControlTextarea1").val().toUpperCase();
		let id_solicitud = $("#id_solicitud").val();
		let id_operador = $("section").find("#id_operador").val();
		let type_contact = 3; // base gestion -> botones operador 
		let comment = "<b>[" + commets + "]</b>";

		if (commets == "") {
			Swal.fire("Verifique!", "Debe indicar un comentario para codificar la llamada", "error");
		} else if (id_solicitud) {

			let base_url = $("input#base_url").val();
			saveTrack(comment, type_contact, id_solicitud, id_operador);
				if (sessionStorage.switch_valor == "activo_wolkvox") {
				
						$.ajax({
							type: "GET",
							dataType: 'jsonp',
							url: 'http://localhost:8084/apiagentbox?action=codd&cod=' + cod1 + '&cod2=1&comm=' + commets,
							beforesend: function () {
								request.setRequestHeader("Access-Control-Allow-Origin", '*');
							},
							success: function (res) {
								console.log(res);
								
								if (res.status === "ok") {
									
									Swal.fire("Llamada!", "Su llamada ha sido codificada", "success");
									
								} else {
									Swal.fire("Verifique!", res, "error");
									
								}
							}
						});
				} else if (sessionStorage.switch_valor == "activo_neotell") {
					$.ajax({
						type: "POST",
						data: { USUARIO: centrales_idoperador , server : sessionStorage.switch_valor},
						url: base_url + "api/ApiSupervisores/DISPONIBLE_NEOTELL",
						success: function (res) {
							console.log(res)
						}
					});
	
					Swal.fire("Llamada!", "Su llamada ha sido codificada", "success");
				
				} else if (sessionStorage.switch_valor == "activo_neotell_colombia") {
					$.ajax({
						type: "POST",
						data: { USUARIO: centrales_idoperador , server : sessionStorage.switch_valor},
						url: base_url + "api/ApiSupervisores/DISPONIBLE_NEOTELL",
						success: function (res) {
							console.log(res)
						}
					});
	
					Swal.fire("Llamada!", "Su llamada ha sido codificada", "success");
					
	
				
				}
			} else {
			console.log("estoy aqui");


			$.ajax({
				type: "GET",
				dataType: 'jsonp',
				url: 'http://localhost:8084/apiagentbox?action=chur&cod=' + cod1 + '&cod2=1&comm=' + commets,
				beforesend: function () {
					request.setRequestHeader("Access-Control-Allow-Origin", '*');
				},
				success: function (res) {
					console.log(res);

					if (res.status === "ok") {

						Swal.fire("Llamada!", "Su llamada ha sido codificada", "success");

					} else {
						Swal.fire("Verifique!", res, "error");

					}
				}
			});




		}


	});


	$("#btn_codori").click(function (event) {

		event.preventDefault();
		var cod1 = $("#exampleFormControlSelect1").val();
		//var cod2= $("#exampleFormControlSelect2").val();
		var commets = $("#exampleFormControlTextarea1").val();

		if (commets == "") {
			Swal.fire("Verifique!", "Debe indicar un comentario para codificar la llamada", "error");
		} else {

			var parametros = { "cod1": cod1, "commets": commets };


			$.ajax({
				type: "POST",
				url: $("input#base_url").val() + 'softphone/Llamada/codificar_only',
				data: parametros,
				success: function (response) {
					//if (response=="Llamada colgada") {
					Swal.fire("Llamada!", "La llamada ha sido codificada solamente" + " " + response, "info");

					//}else{
					//Swal.fire("Verifique!", response, "error");

					//}
				}
			});

		}


	});

	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| Metodo de llamada auxiliar
	|-----------------------------------------------------------------------------------------------------------------------
	*/

	$("#btn_aux").click(function (event) {
		event.preventDefault();
		var txt_num_man = $("#txt_num_man").val();

		if (txt_num_man == "") {
			Swal.fire("Verifique!", "Debe indicar un numero telefonico para realizar la llamada auxiliar", "error");
		} else {
			$.ajax({
				type: "GET",
				crossDomain: true,
				dataType: 'jsonp',
				url: 'http://localhost:8084/apiagentbox?action=diax&phone=' + txt_num_man,
				beforesend: function () {
					request.setRequestHeader("Access-Control-Allow-Origin", '*');
				},
				success: function (res) {


					if (res.status === "ok") {

						Swal.fire("Llamada!", "Su llamada auxiliar esta en curso " + res.status + " al numero: " + txt_num_man, "success");

					}
				}
			});
		}
	});

	$("#btn_auxori").click(function (event) {
		//$('body').on('click','#myModal button[id="btn_call"]',function(event){
		event.preventDefault();
		//var slc_tiporepo = $("#slc_tiporepo").val();


		var txt_num_man = $("#txt_num_man").val();
		var parametros = { "txt_num_man": txt_num_man };

		if (txt_num_man == "") {
			Swal.fire("Verifique!", "Debe indicar un numero telefonico para realizar la llamada", "error");
		} else {
			$.ajax({
				type: "POST",
				url: $("input#base_url").val() + 'softphone/Llamada/llamada_auxiliar',
				data: parametros,
				success: function (response) {
					//if (response=="Llamada conectada") {
					Swal.fire("Llamada!", "La llamada  al numero :" + txt_num_man + " " + response, "info");

					//}else{
					//	Swal.fire("Verifique!", response+" al numero :"+txt_num_man, "error");

					//}
				}
			});
		}


	});

	$("#btn_cambio_estado").click(function (event) {
		$.ajax({
			type: "POST",
			data: { USUARIO: centrales_idoperador , server : sessionStorage.switch_valor },
			url: base_url + "api/ApiSupervisores/DISPONIBLE_NEOTELL",
			success: function (res) {
				console.log(res)
			}
		});
	});

	/*
	|-----------------------------------------------------------------------------------------------------------------------
	| 
	|-----------------------------------------------------------------------------------------------------------------------
	*/




});



