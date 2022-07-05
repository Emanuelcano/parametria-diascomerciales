// Ready page
$(() => {
	var_sol_ajustes.init();
});


var_sol_ajustes = [];
var_sol_ajustes.var = [];

var_sol_ajustes.init = () => {

	var_sol_ajustes.get_sol_ajustes();
	var_sol_ajustes.accordion_gest_sa();
	$('#box_sol_ajustes #ajus_tipo').on('change', function () {
		var_sol_ajustes.var.tipo = []
		ajus_tipo = $('#box_sol_ajustes #ajus_tipo').val()
		$('#box_sol_ajustes #ajus_clases').html()
		var_sol_ajustes.var.tipo = {
			id: ajus_tipo,
			descripcion: $('#box_sol_ajustes #ajus_tipo option:selected').text()
		}
		var_sol_ajustes.getClasebyid(ajus_tipo)
	});

	$('#box_sol_ajustes #ajus_clases').on('change', function () {
		var_sol_ajustes.saveclase();
		var_sol_ajustes.showrequisitos();
	});
	
	$("#box_sol_ajustes .accordion_gest_sa").on("click", () => {
		var_sol_ajustes.accordion_gest_sa();
	})
}

var_sol_ajustes.showrequisitos = () => {	
	key = $('#box_sol_ajustes #ajus_clases option:selected').data('key'); 
	requisitos = var_sol_ajustes.var.data[key].requisitos
	datarequisitos = "";
	if (requisitos.length > 0){
		datarequisitos = "<span>requisitos:<br></span>";
		for (let i = 0; i < requisitos.length; i++) {
			const element = requisitos[i];
			datarequisitos += '<span>&nbsp;-&nbsp;'+element.descripcion+'<br></span>'
		}
		$('#box_sol_ajustes #ajus_requisitos').removeClass('hidden')
	} else {
		$('#box_sol_ajustes #ajus_requisitos').addClass('hidden')
	}
	$('#box_sol_ajustes #ajus_requisitos').html(datarequisitos)
}

var_sol_ajustes.saveclase = () => {
	var_sol_ajustes.var.clase = []
	var_sol_ajustes.var.clase = {
		id: $('#box_sol_ajustes #ajus_clases').val(),
		descripcion: $('#box_sol_ajustes #ajus_clases option:selected').text()
	}
}

var_sol_ajustes.get_sol_ajustes = () => {
	var_sol_ajustes.var.documento = $("#client").data('number_doc');

	if ($.fn.DataTable.isDataTable('#table-solicitudes-ajustes'))
		$('#table-solicitudes-ajustes').DataTable().destroy();

	let ajax = {
		type: "GET",
		url: base_url + 'atencion_cliente/get_solAjustes/' + var_sol_ajustes.var.documento,
		dataType: 'json'
	}

	let columns = [];
	columns = [{
			data: "fecha_solicitud_ajuste"
		},
		{
			data: "id_solicitud_cliente"
		},
		{
			data: "operador_solicitante"
		},
		{
			data: "tipo"
		},
		{
			data: "clase"
		},
		{
			data: "comentario"
		},
		{
			data: "estado",
			render: (data) => {
				valor = ''
				switch (data) {
					case '0':
						valor = 'Por procesar';
						break;
					case '1':
						valor = 'procesado';
						break;
					case '2':
						valor = 'Anulado';
						break;
					case '3':
						valor = 'No Valida';
						break;
				}
				return valor
			}
		},
		{
			data: "fecha_procesado"
		},
		{
			data: "procesado_por_operador"
		},
		{
			data: "observaciones"
		},
		{
			data: "resultado"
		},
		{
			data: "estado",
			render: (data, type, row) => {
				if (data == '0') {
					return '<button id="btn_anul_ajuste" class="btn btn-danger align-bottom" data-id=' + row.id + ' onclick="var_sol_ajustes.anular_ajuste(this)"><i class="fa fa-thumbs-down" aria-hidden="true"></i></button>'
				} else
					return ''
			}
		}
	]
	TablaPaginada('table-solicitudes-ajustes', 0, 'desc', '', '', ajax, columns);
}

var_sol_ajustes.accordion_gest_sa = () => {
	$('#box_sol_ajustes .accordion_gest_sa').toggleClass("active");
	$('#box_sol_ajustes .accordion_gest_sa').hasClass('active') ? $('.title_button_sol_ajuste').text('AJUSTES') : $('.title_button_sol_ajuste').text('VER AJUSTES');
	$('#box_sol_ajustes .body_sol_ajuste').css('display') === 'block' ? $('.body_sol_ajuste').css('display', 'none') : $('.body_sol_ajuste').css('display', 'block');
}

var_sol_ajustes.getClasebyid = (id) => {

	var_sol_ajustes.var.clase = []

	$.ajax({
			url: base_url + 'atencion_cliente/get_tipoajustes/' + id,
			type: 'GET',
			dataType: 'json'
		})
		.done(function (response) {
			data = response.data
			var_sol_ajustes.var.data = []
			var_sol_ajustes.var.data = data
			dataoption = ""
			for (var i = 0; i < data.length; i += 1) {
				dataoption += '<option data-key='+i+' value="' + data[i]['id'] + '" >' + data[i]['descripcion'] + '</option>';
			}
			$('#box_sol_ajustes #ajus_clases').html(dataoption)
			var_sol_ajustes.saveclase()
			var_sol_ajustes.showrequisitos()
		})
		.fail(function (response) {})
		.always(function (response) {});
}

var_sol_ajustes.procesar = () => {

	id_clase_ajuste = $('#box_sol_ajustes #ajus_clases').val()
	descripcion = $('#box_sol_ajustes #ajus_descripcion').val()

	if (id_clase_ajuste === null || descripcion === '') {
		Swal.fire('¡Advertencia!', "Campos incompletos", 'error');
	} else {
		swal
			.fire({
				title: "Esta seguro?",
				text: "desea agregar la solicitud de ajustes?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Si, Confirmar"
			})
			.then(function (result) {
				if (result.value) {
					var data = {
						id_solicitud: $('#id_solicitud').val(),
						documento: $("#client").data('number_doc'),
						id_tipo_ajuste: $('#box_sol_ajustes #ajus_tipo').val(),
						id_clase_ajuste: id_clase_ajuste,
						descripcion: descripcion,
					};

					var base_url =
						$("input#base_url").val() + "atencion_cliente/saveSolajustes";
					$.ajax({
						type: "POST",
						url: base_url,
						data: data,
						success: function (response) {

							Swal.fire('¡Exito!', "Registro Guardado", 'success');
							$("#table-solicitudes-ajustes").DataTable().ajax.reload();
							let id_operador = $("#id_operador").val();
							let solicitud = $("#id_solicitud").val();
							let t_contact = 170;
							let comment = '<b>[SOLICITUD DE AJUSTE]</b>' +
								'<br>[TIPO] = ' + var_sol_ajustes.var.tipo.descripcion +
								'<br>[CLASE] = ' + var_sol_ajustes.var.clase.descripcion +
								'<br>' + descripcion

							saveTrack(comment, t_contact, solicitud, id_operador);
						}
					});
				}
			});
	}
}

var_sol_ajustes.anular_ajuste = (elem) => {
	let id = $(elem).data('id');
	swal
		.fire({
			title: "Esta seguro?",
			text: "desea agregar la solicitud de ajustes?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Si, Confirmar"
		})
		.then(function (result) {
			if (result.value) {
				var data = {
					id: id,
					estado: 2,
        			resultado: 'NO PROCESADO',
                    fecha_proceso: moment().format('YYYY-MM-DD h:mm:ss a'),
                    id_operador_procesa: $("#id_operador").val()
				};
				var base_url = $("input#base_url").val() + "atencion_cliente/updateSolajustes";
				$.ajax({
					type: "POST",
					url: base_url,
					data: data,
					success: function (response) {
						Swal.fire('¡Exito!', "Registro Anulado con exito", 'success');
						$("#table-solicitudes-ajustes").DataTable().ajax.reload();
					}
				});
			}
		});
}
