$(document).ready(function () {
	base_url = $("input#base_url").val();
	$("#cargando").css("display", "none");
	hoy = moment(new Date()).format("DD-MM-YYYY");
	semana = moment(new Date())
		.add(7, "d")
		.format("DD-MM-YYYY");
});

//Tabla Gastos  
function initTablaGastos() {
	let ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/ApiGastos/tabla_gastos"
	};

	let columnDefs = [
		{
			targets: [0],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr("style", "display:none");
			}
		},
		{
			targets: [1, 6, 7],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding-top: 3px!important;font-size: 12px;text-align: center;vertical-align: initial;line-height: 16px;"
				);
			}
		},
		{
			targets: [2, 3],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding-top: 3px!important;font-size: 12px;text-align: left; vertical-align: initial;line-height: 16px;"
				);
			}
		},
		{
			targets: [5],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding-top: 3px!important;font-size: 12px; text-align: right; vertical-align: middle;line-height: 16px;"
				);
			}
		},
		{
			targets: [4],
			createdCell: function (td, cellData, rowData, row, col) {
				alertaPagoProximoVencer = "";
				fecha_vence = moment(cellData).format("DD-MM-YYYY");
				// console.log("fecha_vence", fecha_vence);
				if (
					moment(fecha_vence, "DD-MM-YYYY") >= moment(hoy, "DD-MM-YYYY") &&
					moment(fecha_vence, "DD-MM-YYYY") <= moment(semana, "DD-MM-YYYY")
				) {
					$(td).addClass("alertProcesarPago");
					console.log("SI");
					$(td)
						.parent()
						.css("background", "#FFEBB0");
				}
				if (
					moment(fecha_vence, "DD-MM-YYYY") < moment(hoy, "DD-MM-YYYY")
				) {
					$(td)
						.parent()
						.css("background", "#FADBD8");
				}
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;"
				);
			}
		}
	];
	let columns = [
		{ data: "id_gasto" },
		{
			data: "fecha_ultima_modificacion",
			render: function (data, type, row, meta) {
				var fecha_ultima_modificacion = moment(data).format("DD/MM/YYYY");
				return fecha_ultima_modificacion;
			}
		},
		{ data: "nro_factura" },
		{ data: "denominacion" },
		{
			data: "fecha_vencimiento",
			render: function (data, type, row, meta) {
				var fecha_vencimiento = moment(data).format("DD/MM/YYYY");
				return fecha_vencimiento;
			}
		},
		{ data: "total_pagar", render: $.fn.dataTable.render.number(".", ",", 2) },
		{
			data: "estado",
			render: function (data, type, row, meta) {
				switch (data) {
					case "1":
						estado = "PENDIENTE";
						break;
					case "2":
						estado = "ANULADO";
						break;
					case "3":
						estado = "APROBADO";
						break;
					case "4":
						estado = "RECHAZADO";
						break;
					case "5":
						estado = "PAGADO";
						break;
					case "6":
						estado = "NO PAGADO";
						break;
					default:
						estado = data;
						break;
				}
				return estado;
			}
		},
		{
			data: null,
			render: function (data, type, row, meta) {
				if (row.estado != "1") {
					var disabled = "disabled";
				}
				var vista = true;
				var verGasto =
					'<a class="btn btn-xs bg-navy" title="Ver Datos del gasto" onclick="cargarGastos(' +
					row.id_gasto +
					"," +
					vista +
					');"><i class="fa fa-eye" ></i></a>';
				var editarGasto =
					"<button " +
					disabled +
					' class="btn btn-xs btn-success" title="Actualizar Datos del gasto" onclick="cargarGastos(' +
					row.id_gasto +
					');"> <i class="fa fa-pencil-square-o" ></i> </button>';
				var anularGasto =
					"<button " +
					disabled +
					' class="btn btn-xs btn-danger updateEstadoGasto" title="Anular autorización del gasto" data-estado ="2"  onclick="anularGasto(' +
					row.id_gasto +
					');"> <i class="fa fa-ban" ></i> </button>';
				var botones = verGasto + " " + editarGasto + " " + anularGasto;
				return botones;
			}
		}
	];
	let options_dt = {
		"order": [],
		
		createdRow : function( row, data, dataIndex){
			var date = new Date();
			var day = date.getDate();
			var month = date.getMonth() + 1;
			var year = date.getFullYear();
			var fecha_vencimiento = formatDate(data.fecha_vencimiento);
			var fecha_compara = fecha_vencimiento = formatDate(data.fecha_vencimiento);
			var fecha_compara_primero = fecha_compara.replace("/", "");
			var fecha_compara_final = fecha_compara_primero.replace("/", "");
			var year_vencido = fecha_compara_final.substring(4, 8);
			var month_vencido = fecha_compara_final.substring(2, 4);
			var day_vencido = fecha_compara_final.substring(0, 2);
			if(month < 10){
				month = '0'+month;
			}
			if(data.estado == 1){
				if(year_vencido < year){
					$(row).addClass('vencido');
				}else{
					if(month_vencido == month){
						if(day_vencido < day){
							$(row).addClass('vencido');	
						}	
					}else{
						if(month_vencido < month){
							$(row).addClass('vencido');

						}
					}
				}
			}
    	}
    } 
	TablaPaginada("tp_Gastos", 0, "asc", "", "", ajax, columns, columnDefs,options_dt);
}

//Tabla estados
function initTablaEstados(data) {
	let ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/ApiGastos/tabla_estados",
		data: data
	};
	let columnDefs = [
		{
			targets: [0],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr("style", "display:none");
			}
		},
		{
			targets: [2, 5, 6, 7],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; text-align: center; vertical-align: middle"
				
				);
			}
		},
		{
			targets: [1],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; text-align: left; vertical-align: middle"
				);
			}
		},
		{
			targets: [4],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px;vertical-align: middle text-align: right;"
				);
			}
		},
		{
			targets: [3],
			createdCell: function (td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"padding: 0px; font-size: 12px; text-align: right; vertical-align: middle;"
				);
			}
		}
	];
	let columns = [
		{ data: "id_gasto" },
		{ data: "nro_factura" },
		{ data: "denominacion" },
		{
			data: "fecha_vencimiento",
			render: function (data, type, row, meta) {
				var fecha_vencimiento = moment(data).format("DD/MM/YYYY");
				return fecha_vencimiento;
			}
		},
		{ data: "total_pagar", render: $.fn.dataTable.render.number(".", ",", 2) },
		{
			data: "fecha_ultima_modificacion",
			render: function (data, type, row, meta) {
				var fecha_ultima_modificacion = moment(data).format("DD/MM/YYYY");
				return fecha_ultima_modificacion;
			}
		},
		{ data: "nombre_apellido" },
		{
			data: "estado",
			render: function (data, type, row, meta) {
				switch (data) {
					case "1":
						estado = "PENDIENTE";
						break;
					case "2":
						estado = "ANULADO";
						break;
					case "3":
						estado = "APROBADO";
						break;
					case "4":
						estado = "RECHAZADO";
						break;
					case "5":
						estado = "PAGADO";
						break;
					case "6":
						estado = "NO PAGADO";
						break;
					default:
						estado = data;
						break;
				}
				return estado;
			}
		}
	];
	TablaPaginada("tp_Estados", 0, "asc", "", "", ajax, columns, columnDefs);
}

//Carga un Gasto para ver o editar
function cargarGastos(id, vista = "") {
	var data = {
		id_gasto: id,
		vista: vista
	};
	var base_url = $("input#base_url").val() + "api/ApiGastos/cargar_gasto";
	$.ajax({
		type: "POST",
		url: base_url,
		data: data,
		success: function (response) {
			$("#main").html(response);
			initTablaEstados(data);
			initTablaGastos();
		}
	});
}
function cargarGastosTesoreria(id, vista = "") {
	var data = {
		id_gasto: id,
		vista: vista
	};
	var base_url = $("input#base_url").val() + "api/ApiGastos/cargar_gasto";
	$.ajax({
		type: "POST",
		url: base_url,
		data: data,
		success: function (response) {
			$("#main").html(response);
			$('.content').css("display", "none");
		}
	});
}

//verifica si la factura ingresada ya existe para el beneficiario seleccionado
function existeFactura() {
	var nro_factura = $("#nro_factura").val();
	var id_beneficiario = $("#id_beneficiario").val();
	var data = {
		nro_factura: nro_factura,
		id_beneficiario: id_beneficiario
	};
	var base_url = $("input#base_url").val() + "api/ApiGastos/verificar_factura";
	$.ajax({
		type: "POST",
		url: base_url,
		data: data,
		success: function (response) {
			if (response.errors) {
				alert(response.errors);
				$("#nro_factura").val("");
			}
		}
	});
}

//Anula el gasto
function anularGasto(id_gasto) {
	swal
		.fire({
			title: "Esta seguro?",
			text: "Quiere Anular el gasto permanentemente!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Si, Anular"
		})
		.then(function (result) {
			if (result.value) {
				var estado = $(".updateEstadoGasto").attr("data-estado");
				var data = {
					id_gasto: id_gasto,
					id_estado: estado
				};
				var base_url =
					$("input#base_url").val() + "api/ApiGastos/actualiza_estado_gasto";
				$.ajax({
					type: "POST",
					url: base_url,
					data: data,
					success: function (response) {
						if (response["errors"]) {
							toastr["error"]("No se pudo actualizar el gasto", "ERROR");
						} else {
							toastr["success"]("Se actualizo correctamente", "Actualizado");
							$("#tp_Gastos")
								.DataTable()
								.ajax.reload();
							$("#tp_Estados")
								.DataTable()
								.ajax.reload();
						}
					}
				});
			}
		});
}

//ABM GASTOS

function abmTipoGasto() {
	$("#mostrartipogasto").modal("show");
}
function abmClase() {
	$("#mostrarclase").modal("show");
}
function abmDescripcion() {
	$("#mostrardescripcion").modal("show");
}

//Guardar un tipo específico de gasto
function guardarTipoGasto() {
	var data = {
		denominacion: $("#den_tipo").val()
	};
	var base_url = $("input#base_url").val() + "api/ApiGastos/guardar_gasto";
	if (data["denominacion"] == "") {
		alert("Debe ingresar una denominacion de tipo de gasto");
	} else {
		$.ajax({
			type: "POST",
			url: base_url,
			data: data,
			success: function (response) {
				if (response.errors) {
					alert(response.errors);
				} else {
					alert(response.message);
					$("#id_tipo").append(
						"<option value= " +
						response.id +
						">" +
						$("#den_tipo").val() +
						"</option>"
					);
				}
			}
		});
	}
}

//Guardar una clase específica de gasto
function guardarClaseGasto() {
	var data = {
		denominacion: $("#den_clase").val()
	};
	var base_url =
		$("input#base_url").val() + "api/ApiGastos/guardar_clase_gasto";
	if (data["denominacion"] == "") {
		alert("Debe ingresar una denominacion de clase de gasto");
	} else {
		$.ajax({
			type: "POST",
			url: base_url,
			data: data,
			success: function (response) {
				if (response.errors) {
					alert(response.errors);
				} else {
					alert(response.message);
					$("#clase_gasto").append(
						"<option value= " +
						response.id +
						">" +
						$("#den_clase").val() +
						"</option>"
					);
				}
			}
		});
	}
}

//Trae las descripciones segun la clase seleccionada
function getDescripcion() {
	var id_clase = $("#clase_gasto").val();
	var sel = document.getElementById("descripcion");
	$("#descripcion").val("0");
	for (var i = 0; i < sel.length; i++) {
		var opt = sel[i];
		var id_clase_sel = opt.getAttribute("id_clase");
		if (id_clase == id_clase_sel) {
			opt.style.display = "inline";
		} else {
			opt.style.display = "none";
		}
	}
}

//Guardar una nueva Descripcion específica
function guardarDescripcionGasto() {
	var data = {
		denominacion: $("#den_descr").val(),
		id_clase_gasto: $("#clase_gasto_agregar").val()
	};
	var base_url =
		$("input#base_url").val() + "api/ApiGastos/guardar_descripcion_gasto";
	if (data["denominacion"] == "") {
		alert("Debe ingresar una denominacion de descripcion de gasto");
	} else {
		$.ajax({
			type: "POST",
			url: base_url,
			data: data,
			success: function (response) {
				if (response.errors) {
					alert(response.errors);
				} else {
					alert(response.message);
					$("#descripcion").append(
						"<option value= " +
						$("#clase_gasto_agregar").val() +
						">" +
						$("#den_descr").val() +
						"</option>"
					);
					$("#clase_gasto").val("");
				}
			}
		});
	}
}

//Rellena el input con el detalle de cada Beneficiario seleccionado
function detalleBeneficiario() {
	$("#detalle_beneficiario").text("");
	var data = {
		id_beneficiario: $("#id_beneficiario").val()
	};
	var base_url =
		$("input#base_url").val() + "api/ApiGastos/detalle_beneficiario";
	$.ajax({
		type: "POST",
		url: base_url,
		data: data,
		success: function (response) {
			var obj_response = JSON.parse(response);
			for (var i in obj_response) {
				//Agrego descripcion abajo
				var datos =
					obj_response[i].convencion_tipoDocumento +
					" - " +
					obj_response[i].nro_documento +
					" - " +
					obj_response[i].denominacion;
				$("#detalle_beneficiario").val(datos);
				//Cambio el tipo de moneda
				$("#moneda").val(obj_response[i].id_moneda);
			}
		}
	});
}
