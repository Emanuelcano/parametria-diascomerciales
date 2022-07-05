$(document).ready(function() {

	base_url = $("input#base_url").val();
	$("#cargando").css("display","none");

	$('#campoBuscar').keypress(function(e){
		if(e.which == 13){
			buscarCredito();
		}
	});

	/*
	if($("input#gestionando").val()=='no'){
		setInterval(cargarCreditos(''), 60000);
	}
	cargarCreditos('');
	*/

	//TablaPaginada('tp_atencionCliente',1,'desc',2,'desc');

});

function mayus(e) {
    e.value = e.value.toUpperCase();
}

function getFechasIntervaloInicial() {

	var now = moment();
	var diaSemana = now.format('E');
	var fechaDesde, fechaHasta;

	if (diaSemana == 1) {

		fechaDesde = moment().subtract(2, 'days').format('YYYY-MM-DD 13:00:00');

	} else if (diaSemana > 1 && diaSemana < 7) {

		fechaDesde = moment().subtract(1, 'days').format('YYYY-MM-DD 19:00:00');

	} else {

		fechaDesde = moment().subtract(1, 'days').format('YYYY-MM-DD 13:00:00');
	}

	fechaHasta = now.format('YYYY-MM-DD 23:59:59');

	return {fechaDesde: fechaDesde, fechaHasta: fechaHasta};

}

function cargarCreditos(buscar){
	//alert("entrooo "+$("input#gestionando").val());
	//if($("input#gestionando").val()=='no'){
	$("#cargando").css("display","block");

		base_url2 = $("input#base_url").val()+"ventas/Ventas/cargarCreditos";
		data = "buscar="+buscar;

		$.ajax({
			type: "POST",
			url: base_url2,
			data: data,
			success:function(response){
				validarSession(response);
				$("#tabla_solicitudes").html(response);
				//$("input#campoBuscar").val('');
				$("#cargando").css("display","none");
				//TablaPaginada('tablaCreditos',0,'asc');
				TablaPaginada('tablaCreditos',0,'asc',1,'asc');
			}
		})
	//}
}

function actualizarNosis(cuil,dni,ingreso,id_solicitud,sueldodeclarado,origen,periodo){

		base_url2 = $("input#base_url").val()+"ventas/Ventas/actualizarNosisAnalisis/";
		data = cuil+"/"+dni+"/"+ingreso+"/"+id_solicitud+"/"+sueldodeclarado+"/"+origen+"/"+periodo;
		base_url2 = base_url2 + data;

		$.ajax({
			type: "POST",
			url: base_url2,
			success:function(response){
				validarSession(response);
				$("#cargando").css("display","none");
			}
		})
	//}
}

function buscarCredito(){
	if ( $("input#gestionando").val()=='no' ) {
		$("#cargando").css("display", "block");
		buscar = $("input#campoBuscar").val();
		cargarCreditos(buscar);
	}
}

function exportarCreditos(){

	buscar = $("input#campoBuscar").val();
	if(buscar == ''){ buscar='vacio'; }

	base_url = $("input#base_url").val()+"ventas/Ventas/exportarCreditos/"+buscar;
	window.open(base_url);
}


function abrirModal(idCredito,idcreditoC,dniCredito,origen){

	$("#cargando").css("display","block");

	base_url2 = $("input#base_url").val()+"ventas/Ventas/verAnalista/"+idCredito+"/"+idcreditoC+"/"+origen;

	$.ajax({
		type: "POST",
		url: base_url2,
		//data: data,
		success:function(response){
			validarSession(response);

			if(response.trim() == 'SinDatos'){

				document.getElementById('mostrarModal').style.display = "block";
				document.getElementById('fadeMostrarModal').style.display = "block";

				$("#cargando").css("display","block");
				$("input#gestionando").val('si');

				if ( origen == 'LOCAL' ) {
					base_url2 = $("input#base_url").val()+"analisis_riesgo/AnalisisLocales/analizarCreditoLocal/"+idCredito;
				} else {
					base_url2 = $("input#base_url").val()+"ventas/Ventas/analizarCredito/"+idCredito+"/"+dniCredito+"/"+origen;
				}


				$.ajax({
					type: "POST",
					url: base_url2,

					success:function(response){
						validarSession(response);

						$("#modalInfo").html(response);
						$("#cargando").css("display","none");
						mostrarGrafico(idCredito,3);
					}
				})
			}else{
				alert("La solicitud esta siendo gestionada por: "+response)
				$("#cargando").css("display","none");
			}
		}
	})



}

function llamarZoiper(telefono){
	//$(location).attr('href','zoiper://90111521752171');
	window.location.href='zoiper://' + telefono;

	idCreditoC = $("input#IdConstancy").val();

	base_url2 = $("input#base_url").val()+"ventas/Ventas/registrarAudioValidacion/"+idCreditoC;

	data = "idCreditoC="+idCreditoC;


	//alert(base_url2);
	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
		}
	})


}

function  mostrarComentarios(idCredito,idCreditoC, origen){

	$("#cargando").css("display","block");

	base_url2 = $("input#base_url").val()+"ventas/Ventas/mostrarComentarios";

	data = "idCredito="+idCredito+
		"&idCreditoC="+idCreditoC+
		"&origen="+origen;

	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
			$("#comentariosValidacion"+idCredito).css("display","block");
			$("#comentarioValidacionV"+idCredito).val(response);
			$("#cargando").css("display","none");
		}
	})

}

function  recalificar(idCredito){

	$("#cargando").css("display","block");

	selectRecalificar     = $("#selectRecalificar option:selected").val();
	selectCapital         = $("#selectCapital option:selected").val();
	comentarioRecalificar = $("input#comentarioRecalificar").val();

	if(selectRecalificar=='APROBADO'){
		$("#filaEnviandoMail").css("display","block");
	}

	base_url2 = $("input#base_url").val()+"ventas/Ventas/recalificar";

	data = "idCredito="+idCredito+
		"&origen="+origen+
		"&selectRecalificar="+selectRecalificar+
		"&selectCapital="+selectCapital+
		"&comentarioRecalificar="+comentarioRecalificar;

	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
			$("#mostrarModal").css("display","none");
			$("#fadeMostrarModal").css("display","none");
			$('#modalInfo').html('');
			$('input#gestionando').val('no');

			cargarCreditos('');

			$("#cargando").css("display","none");

		}

	})

}

function  cambiarProducto(idCredito,idCreditoC,origen){

  $("#cargando").css("display","block");

  sctProducto = $("#slctCambiarProducto option:selected").val();

  arreglo = sctProducto.split(".-.");

  selectProducto=arreglo[0];
  monto=arreglo[1];
  plazo=arreglo[2];
  vcuota=arreglo[3];
  anterior=arreglo[4];

  base_url2 = $("input#base_url").val()+"ventas/Ventas/cambiarProducto";

  data = "idCredito="+idCredito+
    "&origen="+origen+
    "&selectProducto="+selectProducto+
    "&anterior="+anterior+
    "&monto="+monto+
    "&idCreditoC="+idCreditoC;

  $.ajax({
    type: "POST",
    url: base_url2,
    data: data,
    success:function(response){
      validarSession(response);

      $('#monto_producto').html(monto);
      $('#plazo_producto').html(plazo);
      $('#cuota_producto').html(vcuota);

      mostrarComentarios(idCredito,idCreditoC,origen);

      $("#cargando").css("display","none");



    }

  })

}

function mostrarGrafico(idCredito,origen){

	if(origen == 1){
		base_url2 = $("input#base_url").val()+"ventas/Ventas/actualizaAnalista/"+idCredito;
		$.ajax({
			type: "POST",
			url: base_url2,
			//data: data,
			success:function(response){
				validarSession(response);

				$("#cargando").css("display","none");
			}
		})
	}

	if(origen == 3){
		divgrafico = 'graficaBuro'+idCredito;
		tipo = 'line';
	}else{
		divgrafico = 'grafica'+idCredito;
		tipo = 'column';
	}

	dniCredito = $("input#dniOculto"+idCredito).val();
	base_url2 = $("input#base_url").val()+"ventas/Ventas/datosGrafico/"+dniCredito;

	$.ajax({
		type: "POST",
		url: base_url2,
		success:function(response2){
			validarSession(response2);

			if(response2 != 'vacio'){

				var dataCategoria  = [];
				var dataConsultas  = [];

				respuesta = response2.split("|.|");

				for (c=0; c<24; c++){
					fila = respuesta[c].split(" => ");
					dataCategoria[c]  = fila[0];
					//dataCategoria[c]  = c+1;
					dataConsultas[c]  = parseInt(fila[1]);

					Highcharts.chart(divgrafico, {
						chart: {
							type: tipo,
							width: null,
							height: 240
						},
						title: {
							text: 'Proyeccion Deuda 24 Meses',
							style: {
								font: '12px'
							}
						},
						subtitle: {
							text: ''
						},
						xAxis: {
							categories:
							dataCategoria
							,
							crosshair: true
						},
						yAxis: {
							min: 0,
							title: {
								text: 'Miles de $'
							}
						},
						tooltip: {
							headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
							pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
								'<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
							footerFormat: '</table>',
							shared: true,
							useHTML: true
						},
						plotOptions: {
							column: {
								pointPadding: 0.2,
								borderWidth: 0
							}
						},
						series: [{
							showInLegend: false,
							name: 'Meses',
							data: dataConsultas

						}]
					});
				}
			}
		}
	})

}

function rotation90d(campo,idCredito){
	$("#"+campo+idCredito).rotate({
		angle:0,
		animateTo:90,

	});
}

function rotation90i(campo,idCredito){
	$("#"+campo+idCredito).rotate({
		angle:0,
		animateTo:-90,

	});
}

function rotation180(campo,idCredito){
	$("#"+campo+idCredito).rotate({
		angle:0,
		animateTo:180,

	});
}

function rotation270(campo,idCredito){
	$("#"+campo+idCredito).rotate({
		angle:0,
		animateTo:270,

	});
}


function mostrarImagen(fotoCliente,dniFrente,dniReverso,dniCredito,nombreCliente) {
	document.getElementById('fadeMostrarImagenes').style.display = "block";
	$("#mostrarImagenes").modal('show');

	document.getElementById('dni').value = dniCredito;
	document.getElementById('nombreCliente').value = nombreCliente;
	document.getElementById('imagen1').src = fotoCliente;
	document.getElementById('imagen2').src = dniFrente;
	document.getElementById('imagen3').src = dniReverso;
}

function closeModal() {
	document.getElementById('fadeMostrarImagenes').style.display = "none";
	document.getElementById('mostrarImagenes').style.display = "none";
}


	function validarMail(idCredito,estado){

		$("#cargando").css("display","block");

		base_url2 = $("input#base_url").val()+"ventas/Ventas/validarMail/"+idCredito+"/"+estado;

		$.ajax({
			type: "POST",
			url: base_url2,
			//data: data,
			success:function(response){
				validarSession(response);
				cargarCreditos('');
				$("#cargando").css("display","none");
			}
		})

	}


	function limpiarAnalista(){

		$("#cargando").css("display","block");
		idCreditoC = $("input#IdConstancy").val();
		origen = $("input#origen").val();

		base_url2 = $("input#base_url").val()+"ventas/Ventas/limpiarAnalista";

		data = "idCreditoC="+idCreditoC+
				"&origen="+origen;

		$.ajax({
			type: "POST",
			url: base_url2,
			data: data,
			success:function(response){
				validarSession(response);

				$("#cargando").css("display","none");

			}
		})

	}

	// TRaido de Analisis Riesgo Nuevo JS


	function guardarRespuesta(numero,pregunta,idCreditoC,idCredito){

		if(pregunta == 'EDAD'){
			valorVerdadedo = $("input#EDADH"+idCredito).val();
			valorEnviado = $("input#EDAD"+idCredito).val();
		}

		if(pregunta == 'DNI'){
			valorVerdadedo = $("input#DNIH"+idCredito).val();
			valorEnviado = $("input#DNI"+idCredito).val();
		}

		if(pregunta == 'ANTIGUEDAD'){
			valorVerdadedo = $("input#ANTIGUEDADH"+idCredito).val();
			valorEnviado = $("input#ANTIGUEDAD"+idCredito).val();
		}

		if(pregunta == 'EMPRESA'){
			valorVerdadedo = $("input#EMPRESAH"+idCredito).val();
			valorEnviado = $("#EMPRESA"+idCredito+" option:selected").val();
		}

		if(pregunta == 'BANCO'){
			valorVerdadedo = $("input#BANCOH"+idCredito).val();
			valorEnviado = $("#BANCO"+idCredito+" option:selected").val();
		}

		if(pregunta == 'DIACOBRO'){
			valorVerdadedo = '';
			valorEnviado = $("#diaCobro"+idCredito+" option:selected").val();
		}

		if(pregunta == 'CUANTOGANA'){
			valorVerdadedo = $("input#CUANTOGANAH"+idCredito).val();;
			valorEnviado = $("input#CUANTOGANA"+idCredito).val();
		}

		if(pregunta == 'RAZON'){
			valorVerdadedo = '';
			valorEnviado = $("#razon"+idCredito+" option:selected").val();
		}

		base_url2 = $("input#base_url").val()+"ventas/Ventas/guardarRespuesta";

		data = "pregunta="+pregunta+
			"&idCredito="+idCredito+
			"&idCreditoC="+idCreditoC+
			"&valorVerdadedo="+valorVerdadedo+
			"&valorEnviado="+valorEnviado+
			"&numero="+numero;

		$.ajax({
			type: "POST",
			url: base_url2,
			data: data,
			success:function(response){
				validarSession(response);
				}
		})


	}

	function aprobarExtra(idPregunta,idCreditoC,idCredito,origen){
		estado="TRUE";
		guardarRespuestaExtra(idPregunta,idCreditoC,idCredito,estado,origen);
	}

	function rechazarExtra(idPregunta,idCreditoC,idCredito,origen){
		estado="FALSE";
		guardarRespuestaExtra(idPregunta,idCreditoC,idCredito,estado,origen);
	}


	function guardarRespuestaExtra(idpregunta,idCreditoC,idCredito,estado,origen){

		var estadof = estado;
		numero = parseInt($('input#pregsave').val());

		if(idpregunta == '1'){
			valorVerdadero = $("input#fechaNac").val();
			valorEnviado   = $("input#fechaNacRes").val();
			
			idPregunta = 1;
			numero     = 1;
			data = "idpregunta="+idpregunta+
				"&idCredito="+idCredito+
				"&idCreditoC="+idCreditoC+
				"&valorVerdadero="+valorVerdadero+
				"&valorEnviado="+valorEnviado+
				"&estado="+estadof+
				"&numero="+numero;

			if (valorEnviado!=""){
				enviarPreguntaExtra(data);
			}

		}
		else{
			if(idpregunta == 2){

				nLabRes = parseInt($('input#nlab').val());
				if (nLabRes>3){
					nLabRes = 3;
				}


				if(nLabRes != 0){

					$('input#laburo*').each(function(){
						n++;
					});
					n = 0;
					$('#cLaburo:checked').each(function(){
						n++;
					});
					
					n = 1;
					m = 0;
					//$('#cLaburo:checked').each(function(){
					for (m = 1; m <= nLabRes; m++) {
						n = n + 1;
						
					  	nLab = $("[nLab="+m+"]").val();
					  	if (nLab !="1" && nLab !="2" && nLab !="3" && nLab !="4" && nLab !="5" && nLab !="6" ){
							valorVerdadero="";
							estadof = "FALSE";
					  	}
						else{
					  		valorVerdadero = $("input#laburo"+nLab+"res").val();
						}

					  	valorEnviado   = $("input#laburo"+m).val();
					  	idPregunta = 2;
					  	numero = numero+1
					  	$("#pregsave").val(numero);

						data = "idpregunta="+idpregunta+
							"&idCredito="+idCredito+
							"&idCreditoC="+idCreditoC+
							"&valorVerdadero="+valorVerdadero+
							"&valorEnviado="+valorEnviado+
							"&estado="+estadof+
							"&numero="+numero;

						if (valorEnviado!=""){
							enviarPreguntaExtra(data);
						}

					}
				//});
				}
				else{
					//numero=2;
					numero = numero+1
					$("#pregsave").val(numero);
					valorEnviado = $("input#laburo1").val();
					if (valorEnviado==""){
						alert("Debe indicar el Empleo Actual");
						return;
					}
					data = "idpregunta="+idpregunta+
						"&idCredito="+idCredito+
						"&idCreditoC="+idCreditoC+
						"&valorVerdadero="+""+
						"&valorEnviado="+valorEnviado+
						"&estado="+estadof+
						"&numero="+numero;

						enviarPreguntaExtra(data);
				}

			}
			if(idpregunta == 3){
				//n=0;
				nParRes = parseInt($('input#npar').val());
				if (nParRes>3){
					nParRes = 3;
				}
				if(nParRes != 0){

					m = 0;
					for (m = 1; m <= nParRes; m++) {

					  	nPar = $("[nPar="+m+"]").val();
					  	if (nPar !="1" && nPar !="2" && nPar !="3" && nPar !="4" && nPar !="5" && nPar !="6" ){
							valorVerdadero="";
							estadof = "FALSE";
					  	}
						else{
					  		valorVerdadero = $("input#pariente"+nPar+"res").val();
						}

					  	valorEnviado   = $("input#pariente"+m).val();
					  	idPregunta = 3;
					  	numero = numero+1
					  	$("#pregsave").val(numero);

						data = "idpregunta="+idpregunta+
							"&idCredito="+idCredito+
							"&idCreditoC="+idCreditoC+
							"&valorVerdadero="+valorVerdadero+
							"&valorEnviado="+valorEnviado+
							"&estado="+estadof+
							"&numero="+numero;

						if (valorEnviado!=""){
							enviarPreguntaExtra(data);
						}

					}
				}
				else{
					numero = numero+1
					$("#pregsave").val(numero);
					valorEnviado = $("input#laburo1").val();
					if (valorEnviado==""){
						alert("Debe indicar el Empleo Actual");
						return;
					}
					data = "idpregunta="+idpregunta+
						"&idCredito="+idCredito+
						"&idCreditoC="+idCreditoC+
						"&valorVerdadero="+""+
						"&valorEnviado="+valorEnviado+
						"&estado="+estadof+
						"&numero="+numero;

						enviarPreguntaExtra(data);
				}

			}
			if(idpregunta == 4){
				nDomRes = parseInt($('input#ndom').val());
				if (nDomRes>3){
					nDomRes = 3;
				}
				if(nDomRes != 0){

					m = 0;
					for (m = 1; m <= nDomRes; m++) {

					  	nDom = $("[nDom="+m+"]").val();
					  	if (nDom !="1" && nDom !="2" && nDom !="3" && nDom !="4" && nDom !="5" && nDom !="6" ){
							valorVerdadero="";
							estadof = "FALSE";
					  	}
						else{
					  		valorVerdadero = $("input#domdir"+nDom+"res").val() + " / " + $("input#domloc"+nDom+"res").val() ;
						}

					  	valorEnviado   = $("input#domicilio"+m).val();
					  	idPregunta = 4;
					  	numero = numero+1
					  	$("#pregsave").val(numero);

						data = "idpregunta="+idpregunta+
							"&idCredito="+idCredito+
							"&idCreditoC="+idCreditoC+
							"&valorVerdadero="+valorVerdadero+
							"&valorEnviado="+valorEnviado+
							"&estado="+estadof+
							"&numero="+numero;

						if (valorEnviado!=""){
							enviarPreguntaExtra(data);
						}

					}
				}
				else{
					m = 0;
					for (m = 1; m <= 3; m++) {
					  	valorEnviado   = $("input#domicilios"+m).val();
					  	valorVerdadero="";

					  	idPregunta = 5;
					  	numero = numero+1
					  	$("#pregsave").val(numero);

						data = "idpregunta="+idpregunta+
							"&idCredito="+idCredito+
							"&idCreditoC="+idCreditoC+
							"&valorVerdadero="+valorVerdadero+
							"&valorEnviado="+valorEnviado+
							"&estado="+estadof+
							"&numero="+numero;

						if (valorEnviado!=""){
							enviarPreguntaExtra(data);
						}

					}

				}
			}


			if(idpregunta == 5){
				//n=0;
				nEntRes = parseInt($('input#nent').val());
				if (nEntRes>3){
					nEntRes = 3;
				}
				if(nEntRes != 0){

					m = 0;
					for (m = 1; m <= nEntRes; m++) {

					  	nEnt = $("[nEnt="+m+"]").val();
					  	if (nEnt !="1" && nEnt !="2" && nEnt !="3" && nEnt !="4" && nEnt !="5" && nEnt !="6" ){
							valorVerdadero="";
							estadof = "FALSE";
					  	}
						else{
					  		valorVerdadero = $("input#entidad"+nEnt+"res").val();
						}


					  	valorEnviado   = $("input#entidad"+m).val();
					  	idPregunta = 5;
					  	numero = numero+1
					  	$("#pregsave").val(numero);

						data = "idpregunta="+idpregunta+
							"&idCredito="+idCredito+
							"&idCreditoC="+idCreditoC+
							"&valorVerdadero="+valorVerdadero+
							"&valorEnviado="+valorEnviado+
							"&estado="+estadof+
							"&numero="+numero;

						if (valorEnviado!=""){
							enviarPreguntaExtra(data);
						}

					}
				}
				else{
					m = 0;
					for (m = 1; m <= 3; m++) {
					  	valorEnviado   = $("input#entidad"+m).val();
					  	valorVerdadero="";

					  	idPregunta = 5;
					  	numero = numero+1
					  	$("#pregsave").val(numero);

						data = "idpregunta="+idpregunta+
							"&idCredito="+idCredito+
							"&idCreditoC="+idCreditoC+
							"&valorVerdadero="+valorVerdadero+
							"&valorEnviado="+valorEnviado+
							"&estado="+estadof+
							"&numero="+numero;

						if (valorEnviado!=""){
							enviarPreguntaExtra(data);
						}

					}

				}
				$("#dEntidades").css("display","none");
				$("#dEntidadesres").css("display","none");
				//Comentario para seguir en el proximo paso
				$("input#cr_valido_extra").val(1);

				setInterval(6000);
				iniciarProceso(idCreditoC,idCredito,1,origen);
			}
		}

	}

	function enviarPreguntaExtra(data){

		base_url2 = $("input#base_url").val()+"ventas/Ventas/guardarRespuestaExtra";

		$.ajax({
			type: "POST",
			url: base_url2,
			data: data,
			success:function(response){
				validarSession(response);
				}
		})
	}

	function iniciarProceso(idCredito,idCreditoC,viene,origen){

		if (validoExtras()==1){

			base_url2 = $("input#base_url").val()+"ventas/Ventas/iniciarProceso";
			data =  "idCredito="+idCredito+
					"&idCreditoC="+idCreditoC+
					"&origen="+origen;

			$.ajax({
				type: "POST",
				url: base_url2,
				data: data,
				success:function(response){
					validarSession(response);
					$("#cargando").css("display","none");
				}
			})

			$("input#gestionando").val('si');
			$("#celdaRellamado"+idCredito).css("display","none");
			$("#botonIniciarProceso"+idCredito).css("display","none");
			$("#divTituloValidacion").css("display","block");
			$("#botonrechazarProceso"+idCredito).css("display","block");
			$("#botonRechazarImagen"+idCredito).css("display","none");
			$("#celdaObservacionesV"+idCredito).css("display","none");
			$("#comentariosValidacion"+idCredito).css("display","none");
			$("#siguientePregunta"+idCredito).css("display","block");

			$("input#vieneSeguir").val(viene);
			preguntaCargar = "pregunta"+viene;
			if(viene >= 1){
				eval(preguntaCargar+'('+idCredito+','+1+','+idCreditoC+')');
			}else if(viene >= 1 && viene < 1){
				eval(preguntaCargar+'('+idCredito+','+0+','+idCreditoC+')');
			}else{
				mostrarValidar(idCredito,idCreditoC);
			}

		}
		else{
			$("input#gestionando").val('si');
			$("#celdaRellamado"+idCredito).css("display","none");
			$("#divTituloValidacion").css("display","block");
			$("#botonDevolverProceso"+idCredito).css("display","none");
			$("#botonIniciarProceso"+idCredito).css("display","none");
			$("#botonrechazarProceso"+idCredito).css("display","block");
			$("#botonRechazarImagen"+idCredito).css("display","none");
			$("#celdaObservacionesV"+idCredito).css("display","none");
			$("#comentariosValidacion"+idCredito).css("display","none");

			nroExtras = $("input#cr_nro_extra").val();
			if (nroExtras == 0){
				$("#dFechaNac").css("display","block");
			}

		}

	}

	function siguientePregunta(idCredito,idCreditoC){

		var seguir=$("input#vieneSeguir").val();

		preguntaCargar = "pregunta"+seguir;
		eval(preguntaCargar+'('+idCredito+','+0+','+idCreditoC+')');

	}

	function pregunta1(idCredito,origen,idCreditoC){

		$("#CuentaAtras"+idCredito).html('1');
		pregunta = $("input#pregunta1"+idCredito).val();
		$("#"+pregunta+idCredito).css("display","block");
		$("input#vieneSeguir").val(2);

	}

	function pregunta2(idCredito,origen,idCreditoC){

		idCreditoC = $("input#idCreditoC"+idCredito).val();
		preguntaa  = $("input#pregunta1"+idCredito).val();
		if(origen<1){
			guardarRespuesta(1,preguntaa,idCreditoC,idCredito);
		}
		$("#"+preguntaa+idCredito).css("display","none");
		$("#CuentaAtras"+idCredito).html('2');
		pregunta = $("input#pregunta2"+idCredito).val();
		$("#"+pregunta+idCredito).css("display","block");
		$("input#vieneSeguir").val(3);

	}

	function pregunta3(idCredito,origen,idCreditoC){

		idCreditoC = $("input#idCreditoC"+idCredito).val();
		preguntaa  = $("input#pregunta2"+idCredito).val();
		if(origen<1){
			guardarRespuesta(2,preguntaa,idCreditoC,idCredito);
		}
		$("#"+preguntaa+idCredito).css("display","none");
		$("#CuentaAtras"+idCredito).html('3');
		pregunta = $("input#pregunta3"+idCredito).val();
		$("#"+pregunta+idCredito).css("display","block");
		$("input#vieneSeguir").val(4);

	}

	function pregunta4(idCredito,origen,idCreditoC){

		idCreditoC = $("input#idCreditoC"+idCredito).val();
		preguntaa  = $("input#pregunta3"+idCredito).val();
		if(origen<1){
			guardarRespuesta(3,preguntaa,idCreditoC,idCredito);
		}
		$("#"+preguntaa+idCredito).css("display","none");
		$("#CuentaAtras"+idCredito).html('4');
		pregunta = $("input#pregunta4"+idCredito).val();
		$("#"+pregunta+idCredito).css("display","block");
		$("input#vieneSeguir").val(5);

	}

	function pregunta5(idCredito,origen,idCreditoC){

		idCreditoC = $("input#idCreditoC"+idCredito).val();
		preguntaa  = $("input#pregunta4"+idCredito).val();
		if(origen<1){
			guardarRespuesta(4,preguntaa,idCreditoC,idCredito);
		}
		$("#"+preguntaa+idCredito).css("display","none");
		$("#CuentaAtras"+idCredito).html('5');
		pregunta = $("input#pregunta5"+idCredito).val();
		$("#"+pregunta+idCredito).css("display","block");
		$("input#vieneSeguir").val(6);

	}

	function pregunta6(idCredito,origen,idCreditoC){

		if(origen<1){
			idCreditoC = $("input#idCreditoC"+idCredito).val();
			preguntaa  = $("input#pregunta5"+idCredito).val();
			guardarRespuesta(5,preguntaa,idCreditoC,idCredito);
			$("#"+preguntaa+idCredito).css("display","none");
		}

		$("#CuentaAtras"+idCredito).html('6');
		pregunta = $("input#pregunta6"+idCredito).val();
		$("#"+pregunta+idCredito).css("display","block");
		$("input#vieneSeguir").val(0);
		$("#siguientePregunta"+idCredito).css("display","none");

	}

	function activarvalidar(idCredito,idCreditoC){

		idCreditoC = $("input#idCreditoC"+idCredito).val();
		preguntaa  = $("input#pregunta6"+idCredito).val();

		guardarRespuesta(6,preguntaa,idCreditoC,idCredito);
		$("#siguientePregunta"+idCredito).css("display","none");
		$("#siguienteValidando"+idCredito).css("display","block");

	}

    function validoExtras(){
    	validoExtra = $("input#cr_valido_extra").val();
    	//alert(validoExtra);
    	return validoExtra;
    }

	function mostrarValidar(idCredito,idCreditoC){

			$("#VALIDANDO"+idCredito).css("display","block");
			$("#CuentaAtras"+idCredito).css("display","none");
			pregunta = $("input#pregunta6"+idCredito).val();
			$("#"+pregunta+idCredito).css("display","none");
			$("#botonesIniciarValidacion"+idCredito).css("display","none");
			$("#botonesIniciarValidacion2"+idCredito).css("display","none");
			$("#celdaRellamado"+idCredito).css("display","none");
			$("#comentariosValidacion"+idCredito).css("display","none");
			//$("#comentarioValidacionR"+idCredito).css("display","none");
			$("#siguienteValidando"+idCredito).css("display","none");
			//$("#celdaObservacionesR"+idCredito).css("display","none");
			$("#celdaObservacionesVi"+idCredito).css("display","block");

			base_url2 = $("input#base_url").val()+"ventas/Ventas/mostrarValidar";

			data =  "idCreditoC="+idCreditoC;

			$.ajax({
				type: "POST",
				url: base_url2,
				data: data,
				success:function(response){
					validarSession(response);

					$("#resultadoValidacion"+idCredito).html(response);
					$("#resultadoValidacion"+idCredito).css("display","block");
					$("#botonesValidacion"+idCredito).css("display","block");
					$("#botonesIniciarValidacion"+idCredito).css("display","none");

					$("#celdaAprobar"+idCredito).css("display","block");
					$("#celdaResetear"+idCredito).css("display","block");
					$("#celdaRechazar"+idCredito).css("display","block");

					$("#cargando").css("display","none");


					//$("#celdaObservacionesR"+idCredito).css("display","none");
					//$("#celdaObservacionesVi"+idCredito).css("display","block");

				}
			})

			$("#celdaObservacionesVi"+idCredito).css("display","block");

			$("#VALIDANDO"+idCredito).css("display","none");
	}


	function mostrarValidarExtra(idCredito,idCreditoC){

			base_url2 = $("input#base_url").val()+"ventas/Ventas/mostrarValidarExtra";

			data =  "idCreditoC="+idCreditoC;

			$.ajax({
				type: "POST",
				url: base_url2,
				data: data,
				success:function(response){
					validarSession(response);
					$("#divResultadoValidacion").css("display","block");
					$("#resultadoValidacionExtra").html(response);
					$("#resultadoValidacionExtra").css("display","block");
				}
			})

	}

	function guardarComentario(idCredito){

		observaciones  = $("#comentarioValidacionR"+idCredito).val();
		idCreditoC     = $("input#idCreditoC"+idCredito).val();
		origen         = $("input#origen").val();

		idTipoRespuesta     = $("#slcTipoRespuesta option:selected").val();
		idGrupoRespuesta    = $("#slcGrupoRespuesta option:selected").val();
		idSubGrupoRespuesta = $("#slcSubGrupoRespuesta option:selected").val();
		idDetalleRespuesta  = $("#slcDetalleRespuesta option:selected").val();

		txtTipo     = $('#slcTipoRespuesta option:selected').text();
		txtGrupo    = $('#slcGrupoRespuesta option:selected').text();
		txtSubGrupo = $('#slcSubGrupoRespuesta option:selected').text();
		txtDetalle  = $('#slcDetalleRespuesta option:selected').text();

		base_url2 = $("input#base_url").val()+"ventas/Ventas/guardarComentario";

		data = "observaciones="+observaciones+
			"&idCredito="+idCredito+
			"&origen="+origen+
			"&idCreditoC="+idCreditoC+
			"&idTipoRespuesta="+idTipoRespuesta+
			"&idGrupoRespuesta="+idGrupoRespuesta+
			"&idSubGrupoRespuesta="+idSubGrupoRespuesta+
			"&idDetalleRespuesta="+idDetalleRespuesta+
			"&txtTipo="+txtTipo+
			"&txtGrupo="+txtGrupo+
			"&txtSubGrupo="+txtSubGrupo+
			"&txtDetalle="+txtDetalle;

		$.ajax({
			type: "POST",
			url: base_url2,
			data: data,
			success:function(response){
				validarSession(response);

				$('#slcTipoRespuesta').prop('selectedIndex',0);
				$('#slcGrupoRespuesta').prop('selectedIndex',0);
				$('#slcSubGrupoRespuesta').prop('selectedIndex',0);
				$('#slcDetalleRespuesta').prop('selectedIndex',0);

				$("#comentarioValidacionR"+idCredito).val('');
				mostrarComentarios(idCredito,idCreditoC,origen);
				$("#cargando").css("display","none");
			}
		})

	}

function programarLlamado(idCredito,origen){

  observaciones = $("#comentarioValidacionR"+idCredito).val();
  fechaLlamado = $("#dProgramarLlamado"+idCredito).val();
  horaLLamado = $("#tProgramarLlamado"+idCredito).val();

  idCreditoC    = $("input#idCreditoC"+idCredito).val();

  base_url2 = $("input#base_url").val()+"ventas/Ventas/programarLlamado";

  data = "observaciones="+observaciones+
         "&idCredito="+idCredito+
         "&idCreditoC="+idCreditoC+
         "&fechaLlamado="+fechaLlamado+
         "&horaLLamado="+horaLLamado+
         "&origen="+origen;


  $.ajax({
    type: "POST",
    url: base_url2,
    data: data,
    success:function(response){
      validarSession(response);
      $("#comentarioValidacionR"+idCredito).val('');
      $("#dProgramarLlamado"+idCredito).val('');
      $("#tProgramarLlamado"+idCredito).val('');
      mostrarComentarios(idCredito,idCreditoC,origen);
	    $("input#gestionando").val('no');
      $("#cargando").css("display","none");
    }
  })

}


function  resetearValidacion(idCredito,idCreditoC){
  if(confirm("Esta seguro de BORRAR LA VALIDACION para REALIZARLA DE NUEVO")){
    $("#cargando").css("display","block");
    observaciones = $("comentarioValidacionR"+idCredito).val();

    base_url2 = $("input#base_url").val()+"ventas/Ventas/resetearValidacion";

    data = "idCredito="+idCredito+
           "&idCreditoC="+idCreditoC+
           "&observaciones="+observaciones;

    $.ajax({
      type: "POST",
      url: base_url2,
      data: data,
      success:function(response){
        validarSession(response);
        location.href=$("input#base_url").val()+"analisis_riesgo/Analisis";
	      $("input#gestionando").val('no');
        $("#cargando").css("display","none");

      }
    })
  }
}




function  devolverProceso(idCredito){
  if(confirm("Esta seguro de devolver la solicitud ")){
    $("#cargando").css("display","block");
    observaciones = $("comentarioValidacionR"+idCredito).val();

    base_url2 = $("input#base_url").val()+"ventas/Ventas/devolverProceso";

    data = "idCredito="+idCredito+
           "&observaciones="+observaciones;

    $.ajax({
      type: "POST",
      url: base_url2,
      data: data,
      success:function(response){
        validarSession(response);
        location.href=$("input#base_url").val()+"analisis_riesgo/Analisis";
        $("#cargando").css("display","none");

      }
    })
  }
}


	function  rechazarProceso(idCredito,idCreditoC,origen){

		if(confirm("Esta seguro que desea RECHAZAR la solicitud")) {

			$("#cargando").css("display","block");
			observaciones	= $("comentarioValidacionR"+idCredito).val();

			idTipoRespuesta     = $("#slcTipoRespuesta option:selected").val();
			idGrupoRespuesta    = $("#slcGrupoRespuesta option:selected").val();
			idSubGrupoRespuesta = $("#slcSubGrupoRespuesta option:selected").val();
			idDetalleRespuesta  = $("#slcDetalleRespuesta option:selected").val();

			txtTipo     = $('#slcTipoRespuesta option:selected').text();
			txtGrupo    = $('#slcGrupoRespuesta option:selected').text();
			txtSubGrupo = $('#slcSubGrupoRespuesta option:selected').text();
			txtDetalle  = $('#slcDetalleRespuesta option:selected').text();

			base_url2 		= $("input#base_url").val()+"ventas/Ventas/rechazarProceso";

			data = "idCredito="+idCredito+
				"&observaciones="+observaciones+
				"&origen="+origen+
				"&idCreditoC="+idCreditoC+
				"&idTipoRespuesta="+idTipoRespuesta+
				"&idGrupoRespuesta="+idGrupoRespuesta+
				"&idSubGrupoRespuesta="+idSubGrupoRespuesta+
				"&idDetalleRespuesta="+idDetalleRespuesta+
				"&txtTipo="+txtTipo+
				"&txtGrupo="+txtGrupo+
				"&txtSubGrupo="+txtSubGrupo+
				"&txtDetalle="+txtDetalle;

			$.ajax({
				type: "POST",
				url: base_url2,
				data: data,
				success:function(response){
					validarSession(response);
					location.href=$("input#base_url").val()+"analisis_riesgo/Analisis";
					$("input#gestionando").val('no');

					$("#cargando").css("display","none");
				}
			})

		}

	}

	function  aprobarValidacion(idCredito,idCreditoC,origen,dniCredito){

		if(confirm("Esta seguro que desea APROBAR la validacion")){

			$("#cargando").css("display","block");
			$("input#gestionando").val('si');

			observaciones = $("#comentarioValidacionR"+idCredito).val();
			base_url2 = $("input#base_url").val()+"ventas/Ventas/aprobarValidacion";
			data =  "idCredito="+idCredito+
				"&idCreditoC="+idCreditoC+
				"&observaciones="+observaciones+
				"&origen="+origen;

			$.ajax({
				type: "POST",
				url: base_url2,
				data: data,
				success:function(response){

					validarSession(response);
					$("#cargando").css("display","none");

					$("#botonesIniciarValidacion"+idCredito).css("display","none");
					$("#botonesValidacion"+idCredito).css("display","none");
					$(".tituloValidacion").css("display","none");
					$("#tablaValidacion"+idCredito).css("display","none");
					$("#resultadoValidacionExtra").css("display","none");

					$("#botonesAprobar"+idCredito).css("display","block");

					}
			})
		}
	}


	function rechazarValidacion(idCredito,idCreditoC,origen){

	  if(confirm("Esta seguro que desea RECHAZAR la validacion")){

	    $("#cargando").css("display","block");

	    observaciones = $("#comentarioValidacionVi"+idCredito).val();

	    idTipoRespuesta     = $("#slcTipoRespuesta option:selected").val();
		idGrupoRespuesta    = $("#slcGrupoRespuesta option:selected").val();
		idSubGrupoRespuesta = $("#slcSubGrupoRespuesta option:selected").val();
		idDetalleRespuesta  = $("#slcDetalleRespuesta option:selected").val();

		txtTipo     = $('#slcTipoRespuesta option:selected').text();
		txtGrupo    = $('#slcGrupoRespuesta option:selected').text();
		txtSubGrupo = $('#slcSubGrupoRespuesta option:selected').text();
		txtDetalle  = $('#slcDetalleRespuesta option:selected').text();

	    base_url2 = $("input#base_url").val()+"ventas/Ventas/rechazarValidacion";

	    data = "idCredito="+idCredito+
	           "&idCreditoC="+idCreditoC+
	           "&origen="+origen+
	           "&observaciones="+observaciones+
				"&idTipoRespuesta="+idTipoRespuesta+
				"&idGrupoRespuesta="+idGrupoRespuesta+
				"&idSubGrupoRespuesta="+idSubGrupoRespuesta+
				"&idDetalleRespuesta="+idDetalleRespuesta+
				"&txtTipo="+txtTipo+
				"&txtGrupo="+txtGrupo+
				"&txtSubGrupo="+txtSubGrupo+
				"&txtDetalle="+txtDetalle;

	    $.ajax({
	      type: "POST",
	      url: base_url2,
	      data: data,
	      success:function(response){
	        validarSession(response);
	        location.href=$("input#base_url").val()+"analisis_riesgo/Analisis";
	        $("input#gestionando").val('no');
	        $("#cargando").css("display","none");
	      }
	    })

	  }


	}

	function noResponde(idCredito,idCreditoC,origen){

		$("#cargando").css("display","block");

		observaciones = $("comentarioValidacionR"+idCredito).val();

		base_url2 = $("input#base_url").val()+"ventas/Ventas/noResponde";

		data =  "idCredito="+idCredito+
			"&idCreditoC="+idCreditoC+
			"&observaciones="+observaciones+
			"&origen="+origen;

		$.ajax({
			type: "POST",
			url: base_url2,
			data: data,
			success:function(response){
				validarSession(response);
				limpiarAnalista()

				document.getElementById('mostrarModal').style.display = "none";
				document.getElementById('fadeMostrarModal').style.display = "none";
				$("input#gestionando").val('no');
				$("#cargando").css("display","none");
				mostrarComentarios(idCredito,idCreditoC,origen);
			}
		})

	}


function aprobarCredito(idCredito,idCreditoC,origen) {

	if ( confirm("Esta seguro que desea APROBAR EL CREDITO") ) {

		$("#cargando").css("display","block");
		$("input#gestionando").val('no');

		observaciones = $("#comentarioValidacionR"+idCredito).val();
		base_url2 = $("input#base_url").val()+"ventas/Ventas/aprobarCredito";

		data = "idCredito="+idCredito+
				"&idCreditoC="+idCreditoC+
				"&origen="+origen+
				"&observaciones="+observaciones;

		$.ajax({
			type: "POST",
			url: base_url2,
			data: data,
			success:function(response){
				validarSession(response);
				location.href=$("input#base_url").val()+"analisis_riesgo/Analisis";
				$("input#gestionando").val('no');
				$("#cargando").css("display","none");
			}
		})

	}

}

function rechazarCredito(idCredito,idCreditoC,origen){

	if(confirm("Esta seguro que desea RECHAZAR EL CREDITO")){

		$("#cargando").css("display","block");

		observaciones   = $("#comentarioValidacionR"+idCredito).val();

		idTipoRespuesta     = $("#slcTipoRespuesta option:selected").val();
		idGrupoRespuesta    = $("#slcGrupoRespuesta option:selected").val();
		idSubGrupoRespuesta = $("#slcSubGrupoRespuesta option:selected").val();
		idDetalleRespuesta  = $("#slcDetalleRespuesta option:selected").val();

		txtTipo     = $('#slcTipoRespuesta option:selected').text();
		txtGrupo    = $('#slcGrupoRespuesta option:selected').text();
		txtSubGrupo = $('#slcSubGrupoRespuesta option:selected').text();
		txtDetalle  = $('#slcDetalleRespuesta option:selected').text();

		base_url2       = $("input#base_url").val()+"ventas/Ventas/rechazarCredito";

		data = "idCredito="+idCredito+
				"&idCreditoC="+idCreditoC+
				"&origen="+origen+
				"&observaciones="+observaciones+
				"&idTipoRespuesta="+idTipoRespuesta+
				"&idGrupoRespuesta="+idGrupoRespuesta+
				"&idSubGrupoRespuesta="+idSubGrupoRespuesta+
				"&idDetalleRespuesta="+idDetalleRespuesta+
				"&txtTipo="+txtTipo+
				"&txtGrupo="+txtGrupo+
				"&txtSubGrupo="+txtSubGrupo+
				"&txtDetalle="+txtDetalle;

		$.ajax({
			type: "POST",
			url: base_url2,
			data: data,
			success:function(response) {
				validarSession(response);
				$("#cargando").css("display","none");
				$("input#gestionando").val('no');
				location.href=$("input#base_url").val()+"analisis_riesgo/Analisis";
			}
		})

	}
}


function supervisorCredito(idCredito,idCreditoC,origen){

	if(confirm("Esta seguro que desea enviar la solicitud a revision del SUPERVISOR")){

		$("#cargando").css("display","block");
		$("input#gestionando").val('si');

		observaciones = $("comentarioValidacionR"+idCredito).val();

		base_url2 = $("input#base_url").val()+"ventas/Ventas/supervisorCredito";

		data = "idCredito="+idCredito+
				"&idCreditoC="+idCreditoC+
				"&origen="+origen+
				"&observaciones="+observaciones;

		$.ajax({
			type: "POST",
			url: base_url2,
			data: data,
			success:function(response){
				validarSession(response);
				$("input#gestionando").val('no');

				location.href=$("input#base_url").val()+"analisis_riesgo/Analisis";
				$("#cargando").css("display","none");

			}
		})

	}

}


function  actualizaCheck(tipo,valor_actual,idCredito, origen, idCreditoC){

	$("#cargando").css("display","block");

	if(tipo == 'qr'){valor_actual =$("input#vqr").val();}
	if(tipo == 'rt'){valor_actual =$("input#vrt").val();}
	if(tipo == 'bcra'){valor_actual =$("input#vbcra").val();}
	if(tipo == 'siisa'){valor_actual =$("input#vsiisa").val();}
	if(tipo == 'nosis'){valor_actual =$("input#vnosis").val();}
	if(tipo == 'imagenes'){valor_actual =$("input#vimagenes").val();}


	if(valor_actual ==0){vactual = 1;}else{vactual =0;}

	if(tipo == 'qr'){$("input#vqr").val(vactual);}
	if(tipo == 'rt'){$("input#vrt").val(vactual);}
	if(tipo == 'bcra'){$("input#vbcra").val(vactual);}
	if(tipo == 'siisa'){$("input#vsiisa").val(vactual);}
	if(tipo == 'nosis'){$("input#vnosis").val(vactual);}
	if(tipo == 'imagenes'){$("input#vimagenes").val(vactual);}

	base_url2 = $("input#base_url").val()+"ventas/Ventas/actualizaCheck";

	data = "idCredito="+idCredito+
		"&tipo="+tipo+
		"&origen="+origen+
		"&valor_actual="+valor_actual;

	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
			mostrarComentarios(idCredito,idCreditoC,origen);
			$("#cargando").css("display","none");
		}
	})

}


function getGrupoRespuesta(){

	idTipo = $("#slcTipoRespuesta option:selected").val();

	base_url2 = $("input#base_url").val()+"ventas/Ventas/getGrupoRespuesta";
	data = "idTipo="+idTipo;

	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
			$("#slcGrupoRespuesta").html(response);

		}
	})

}

function getGrupoRespuesta(){

	idTipo = $("#slcTipoRespuesta option:selected").val();

	base_url2 = $("input#base_url").val()+"ventas/Ventas/getGrupoRespuesta";
	data = "idTipo="+idTipo;

	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
			$("#slcGrupoRespuesta").html(response);

		}
	})

}

function getSubGrupoRespuesta(){

	idTipo = $("#slcGrupoRespuesta option:selected").val();

	base_url2 = $("input#base_url").val()+"ventas/Ventas/getSubGrupoRespuesta";
	data = "idTipo="+idTipo;

	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
			$("#slcSubGrupoRespuesta").html(response);
		}
	})

}

function getDetalleRespuesta(){

	idTipo = $("#slcSubGrupoRespuesta option:selected").val();

	base_url2 = $("input#base_url").val()+"ventas/Ventas/getDetalleRespuesta";
	data = "idTipo="+idTipo;

	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
			$("#slcDetalleRespuesta").html(response);
		}
	})


}


function cambiarSexo(dni){

	sexo = $("#sexo option:selected").val();
	idCreditoC = $("input#IdConstancy").val();
	origen = $("input#origen").val();


	base_url2 = $("input#base_url").val()+"ventas/Ventas/cambiarSexo";
	data = "sexo="+sexo+"&idCreditoC="+idCreditoC+
			"&origen="+origen+"&dni="+dni;

	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
		}
	})
}

function cambiarEntidadDeclarada(idCredito){

	entidad = $("#entidadDeclarada option:selected").val();

	base_url2 = $("input#base_url").val()+"ventas/Ventas/cambiarEntidadDeclarada";
	data = "idCredito="+idCredito+"&entidadDeclarada="+entidad;

	$.ajax({
		type: "POST",
		url: base_url2,
		data: data,
		success:function(response){
			validarSession(response);
		}
	})


}
