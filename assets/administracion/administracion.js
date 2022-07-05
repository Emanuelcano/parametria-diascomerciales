const { reduce } = require("lodash");

$(document).ready(function() {
	base_url = $("input#base_url").val();
	$("#cargando").css("display", "none");
});


function administrarGastos() {
	$("#tp_GastosPendientes")
		.DataTable()
		.ajax.reload();
	base_url =
		$("input#base_url").val() +
		"administracion/administracion/vistaGastosPendientes";
	$.ajax({
		type: "POST",
		url: base_url,
		success: function(response) {
			$("#main").html(response);
			initTablaGastosPendientes();
			$("#cargando").css("display", "none");
		}
	});
}

function initTablaGastosPendientes() {
	let ajax = {
		type: "POST",
		url: $("input#base_url").val() + "api/ApiGastos/tabla_gastos_pendientes"
	};

	let columnDefs = [
		{
			targets: [0],
			createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr("style", "display:none");

			}	
		},
		{
			targets: [1, 4, 6, 7],
			createdCell: function(td, cellData, rowData, row, col) {
				
				$(td).attr(
					"style",
					"font-size: 12px; text-align: center; vertical-align: middle;padding: 4px;"
				);
				
			}
		},
		
		{
			targets: [2],
			createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"font-size: 12px; text-align: left; vertical-align: middle;padding: 4px;"
				);
			}
		},
		{
			targets: [5],
			createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"font-size: 12px; text-align: right; vertical-align: middle;padding: 4px;"
				);
			}
		},
		{
			targets: [3],
			createdCell: function(td, cellData, rowData, row, col) {
				$(td).attr(
					"style",
					"font-size: 12px; vertical-align: middle;padding: 4px;"
				);

			}
		}
	];
	;
	let columns = [
		{ data: "id_gasto" },
		{
			data: "fecha_ultima_modificacion",
			render: function(data, type, row, meta) {
				var fecha_ultima_modificacion = formatDate(data);
				return fecha_ultima_modificacion;
			} 
		},
		{ data: "nro_factura" },
		{ data: "denominacion" },
		{
			data: "fecha_vencimiento",
			render: function(data, type, row, meta) {		
				var fecha_vencimiento = formatDate(data);
				return fecha_vencimiento;
		},	
	},		
		{ data: "total_pagar", render: $.fn.dataTable.render.number(".", ",", 2) },
		{
			data: "estado",
			render: function(data, type, row, meta) {
				var estado = data == 1 ? "PENDIENTE" : "ANULADO";
				return estado;
			}
		},
		{
			data: null,
			render: function(data, type, row, meta) {
				if (row.estado != "1") {
					var disabled = "disabled";
				}
				var vista = true;
				var verGasto =
					'<a class="btn btn-xs bg-navy" title="Ver Datos del gasto" onclick="cargarGastosPendiente(' +
					row.id_gasto +
					"," +
					vista +
					');"><i class="fa fa-eye" ></i></a>';
				var botones = verGasto;
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
	TablaPaginada(
		"tp_GastosPendientes",
		0,
		"asc",
		"",
		"",
		ajax,
		columns,
		columnDefs,
		options_dt,
		
		
		
	);
}

//Carga un Gasto para ver o editar
function cargarGastosPendiente(id, vista = "") {
	var data = {
		id_gasto: id,
		vista: vista
	};
	var base_url =
		$("input#base_url").val() + "api/ApiGastos/cargar_gasto_pendiente";
	$.ajax({
		type: "POST",
		url: base_url,
		data: data,
		success: function(response) {
			$("#main").html(response);
			initTablaEstados(data);
		}
	});
}

//Rechaza el gasto
function updateEstadoGasto(element) {
	estado = $(element).attr("data-estado");
	id_gasto = $(element).attr("data-id");
	var data = {
		id_gasto: id_gasto,
		id_estado: estado
	}; 
	
	if (estado == 3) {
		swal
			.fire({
				title: "Esta seguro?",
				text: "Quiere Aprobar el gasto permanentemente!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Si, Aprobar"
			})
			.then(function(result) {
				if (result.value) {
					var base_url =
						$("input#base_url").val() + "api/ApiGastos/actualiza_estado_gasto";
					$.ajax({
						type: "POST",
						url: base_url,
						data: data,
						success: function(response) {
							if (response["errors"]) {
								toastr["error"]("No se pudo actualizar el gasto", "ERROR");
							} else {
								var cantidad = $("#cantidad_gastos_pendientes").text();
								var nueva_cantidad = parseInt(cantidad) - 1;
								$("#cantidad_gastos_pendientes").text(nueva_cantidad);
								toastr["success"]("Se actualizo correctamente", "Actualizado");
								administrarGastos();
							}
						}
					});
				}
			});
	} else {
		swal
			.fire({
				title: "Esta seguro?",
				text: "Quiere Rechazar el gasto permanentemente!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Si, Rechazar"
			})
			.then(function(result) {
				if (result.value) {
					var base_url =
						$("input#base_url").val() + "api/ApiGastos/actualiza_estado_gasto";
					$.ajax({
						type: "POST",
						url: base_url,
						data: data,
						success: function(response) {
							if (response["errors"]) {
								toastr["error"]("No se pudo actualizar el gasto", "ERROR");
							} else {
								var cantidad = $("#cantidad_gastos_pendientes").text();
								var nueva_cantidad = parseInt(cantidad) - 1;
								$("#cantidad_gastos_pendientes").text(nueva_cantidad);
								toastr["success"]("Se actualizo correctamente", "Actualizado");
								administrarGastos();
							}
						}
					});
				}
			});
	}
}
