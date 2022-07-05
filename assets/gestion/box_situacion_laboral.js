$(document).ready(function () {
	var acc_10 = $(".accordion_gest_st");
	var i10;
	for (i10 = 0; i10 < acc_10.length; i10++) {
		acc_10[i10].addEventListener("click", function () {
			
			this.classList.toggle("active");

			if ($(this).hasClass('active')) {
				$('.title_button_versitlab').text('LABORAL');
			} else {
				$('.title_button_versitlab').text('VER LABORAL');

			}
			var panel_10 = this.nextElementSibling;
			if (panel_10.style.display === "block") {
				panel_10.style.display = "none";
			} else {
				panel_10.style.display = "block";
			}
		});
	}

	var_sit_lab.accordion_gest_st('.accordion_gest_st_ilt', '.title_button_versitlab_ilt', '.body_versitlab_ilt');
	var_sit_lab.accordion_gest_st('.accordion_gest_st_ila', '.title_button_versitlab_ila', '.body_versitlab_ila');
	var_sit_lab.accordion_gest_st('.accordion_gest_st_ilarus', '.title_button_versitlab_ilarus', '.body_versitlab_ilarus');
	var_sit_lab.accordion_gest_st('.accordion_gest_st_ilMareigua', '.title_button_versitlab_ilMareigua', '.body_versitlab_ilMareigua');
	var_sit_lab.get_inf_laboral();
	var_sit_lab.get_inf_laboralE();
	var_sit_lab.get_inf_laboralArus();
	var_sit_lab.get_inf_laboralMareigua();
	
	$(".accordion_gest_st_ilt").on("click", () => {
		var_sit_lab.accordion_gest_st('.accordion_gest_st_ilt', '.title_button_versitlab_ilt', '.body_versitlab_ilt');
	})
	
	$(".accordion_gest_st_ila").on("click", () => {
		var_sit_lab.accordion_gest_st('.accordion_gest_st_ila', '.title_button_versitlab_ila', '.body_versitlab_ila');
	})
	
	$(".accordion_gest_st_ilarus").on("click", () => {
		var_sit_lab.accordion_gest_st('.accordion_gest_st_ilarus', '.title_button_versitlab_ilarus', '.body_versitlab_ilarus');
	})	
	
	$(".accordion_gest_st_ilMareigua").on("click", () => {
		var_sit_lab.accordion_gest_st('.accordion_gest_st_ilMareigua', '.title_button_versitlab_ilMareigua', '.body_versitlab_ilMareigua');
	})	

}); // fin del ready document

	var_sit_lab = [];
	var_sit_lab.format = [];
	var_sit_lab.data = [];
	var_sit_lab.data.documento = "";
	
	var_sit_lab.accordion_gest_st = (v1, v2, v3) => {
		$(v1).toggleClass("active");
		$(v1).hasClass('active') ? $(v2).text('APORTES') : $(v2).text('VER APORTES');
		$(v3).css('display') === 'block' ? $(v3).css('display', 'none') : $(v3).css('display', 'block');
	}
	var_sit_lab.get_inf_laboral = () => {
		var_sit_lab.data.documento = $("#inp_sl_documento").val();
		$.ajax({
			dataType: "json",
			url		:  base_url + 'atencion_cliente/get_inflaboral/' + var_sit_lab.data.documento,
			type	: 'GET'
		}).done( (response) => {

			if ( $.fn.DataTable.isDataTable( '#table_sl_ILT_aportes' ) ) 
				$('#table_sl_ILT_aportes').DataTable().destroy();

			if ( $.fn.DataTable.isDataTable( '#table_sl_ILT' ) ) 
				$('#table_sl_ILT').DataTable().destroy();

			let columns = [];
			columns = [
				{
					data: (row, type) => {
						if (type === 'display') {
							row.periodo = ('0' + row.mesPeriodoValidado).slice(-2) + '-' + row.anoPeriodoValidado;
							return row.periodo;		
						}
						return row.position;
					},
					className: 'dt-body-center'
				},
				{
					data: "NIT",
					className: 'dt-body-center',
				},
				{
					data: "empresa"
				},
				{
					data: "salario",
					className: 'dt-body-right',
					render: (data) => {
						return var_sit_lab.format.moneda.format(data)
					}
				},
			]

			TablaPaginada('table_sl_ILT_aportes', 0, 'desc', '', '', null, columns , null , null, null, 15, null, { data: response['ILT']['APORTES'], order: [[ 0, 'desc' ]]  } );

			columns = [];
			columns =[
					{
						data: "EPS",
						render: (data) => {
							return data? data: '-';
						}
					},
					{
						data: "AFP",
						render: (data) => {
							return data? data: '-';
						}
					},
					{
						data: "ocupacion",
						className: 'dt-body-center',
						render: (data) => {
							return var_sit_lab.format.numero.format(data) + " %"
						}
					},
					{
						data: "rotacion",
						className: 'dt-body-center',
						render: (data) => {
							return var_sit_lab.format.numero.format(data)
						}
					},
					{
						data: "menor_salario",
						className: 'dt-body-right',
						render: (data) => {
							return var_sit_lab.format.moneda.format(data.replace(',','.'))
						}
					},
					{
						data: "mayor_salario",
						className: 'dt-body-right',
						render: (data) => {
							return var_sit_lab.format.moneda.format(data.replace(',','.'))
						}
					},
					{
						data: "salario_promedio",
						className: 'dt-body-right',
						render: (data) => {
							return (data != 'nan') ? var_sit_lab.format.moneda.format(data.replace(',','.')) : var_sit_lab.format.moneda.format(0)
						}
					},
					{
						data: "fecha_consulta",
						className: 'dt-body-center',
						render: (data) => {
							return data? data: '-';
						}
					},
				]
			
			TablaPaginada('table_sl_ILT', 0, 'asc', '', '', null, columns , null , null, null, 15, null, { data: [response['ILT']['IL']] } );
			
		});
	}
	
	var_sit_lab.get_inf_laboralE = () => {
		
		$.ajax({
			url		:  base_url + 'atencion_cliente/get_inflaboralE',
			type	: 'POST',
			dataType: 'json',
			data	: {'documento':$("#inp_sl_documento").val(), 'id_solicitud' : $("#id_solicitud").val()}
		}).done( (response) => {
			
			if ( $.fn.DataTable.isDataTable( '#table_sl_ILE_aportes' ) ) 
				$('#table_sl_ILE_aportes').DataTable().destroy();

			if ( $.fn.DataTable.isDataTable( '#table_sl_ILE' ) ) 
				$('#table_sl_ILE').DataTable().destroy();
			let columns = [];
			columns = [
				{
					data: "periodo",
					className: 'dt-body-center'
				},
				{
					data: "NIT",
					className: 'dt-body-center',
				},
				{
					data: "empresa"
				},
				{
					data: "salario",
					className: 'dt-body-right',
					render: (data) => {
						return var_sit_lab.format.moneda.format(data)
					}
				},
			]

			TablaPaginada('table_sl_ILE_aportes', 0, 'desc', '', '', null, columns , null , null, null, 15, null, { data: response['ILE']['APORTES']  } );
			columns = [];
			columns =[
					{
						data: "EPS",
						render: (data) => {
							return data? data: '-';
						}
					},
					{
						data: "AFP",
						render: (data) => {
							return data? data: '-';
						}
					},
					{
						data: "ocupacion",
						className: 'dt-body-center',
						render: (data) => {
							return var_sit_lab.format.numero.format(data) + " %"
						}
					},
					{
						data: "rotacion",
						className: 'dt-body-center',
						render: (data) => {
							return var_sit_lab.format.numero.format(data)
						}
					},
					{
						data: "menor_salario",
						className: 'dt-body-right',
						render: (data) => {
							return var_sit_lab.format.moneda.format(data.replace(',','.'))
						}
					},
					{
						data: "mayor_salario",
						className: 'dt-body-right',
						render: (data) => {
							return var_sit_lab.format.moneda.format(data.replace(',','.'))
						}
					},
					{
						data: "salario_promedio",
						className: 'dt-body-right',
						render: (data) => {
							return (data != 'nan') ? var_sit_lab.format.moneda.format(data.replace(',','.')) : var_sit_lab.format.moneda.format(0)
						}
					},
					{
						data: "fecha_consulta",
						className: 'dt-body-center',
						render: (data) => {
							return data? moment(data).format('DD-MM-YYYY') : '-';
						}
					},
				]
			
			TablaPaginada('table_sl_ILE', 0, 'asc', '', '', null, columns , null , null, null, 15, null, { data: response['ILE']['IL'] } );
		});
	}
	
	var_sit_lab.get_inf_laboralArus = () => {
		
		$.ajax({
			url		:  base_url + 'atencion_cliente/get_inflaboralArus',
			type	: 'POST',
			dataType: 'json',
			data	: {'documento':$("#inp_sl_documento").val(), 'id_solicitud' : $("#id_solicitud").val()}
		}).done( (response) => {

			if ( $.fn.DataTable.isDataTable( '#table_sl_ILArus_aportes' ) ) 
				$('#table_sl_ILArus_aportes').DataTable().destroy();

			if ( $.fn.DataTable.isDataTable( '#table_sl_ILArus' ) ) 
				$('#table_sl_ILArus').DataTable().destroy();
				
			let columns = [];
			columns = [
				{
					data: "periodo",
					className: 'dt-body-center'
				},
				{
					data: "NIT",
					className: 'dt-body-center',
				},
				{
					data: "empresa"
				},
				{
					data: "salario",
					className: 'dt-body-right',
					render: (data) => {
						return var_sit_lab.format.moneda.format(data)
					}
				},
			]

			TablaPaginada('table_sl_ILArus_aportes', 0, 'desc', '', '', null, columns , null , null, null, 15, null, { data: response['ILArus']['APORTES'], order: [[ 0, 'desc' ]]  } );
			columns = [];
			columns =[
					{
						data: "EPS",
						render: (data) => {
							return data? data: '-';
						}
					},
					{
						data: "AFP",
						render: (data) => {
							return data? data: '-';
						}
					},
					{
						data: "ocupacion",
						className: 'dt-body-center',
						render: (data) => {
							return var_sit_lab.format.numero.format(data) + " %"
						}
					},
					{
						data: "rotacion",
						className: 'dt-body-center',
						render: (data) => {
							return var_sit_lab.format.numero.format(data)
						}
					},
					{
						data: "menor_salario",
						className: 'dt-body-right',
						render: (data) => {
							return var_sit_lab.format.moneda.format(data.replace(',','.'))
						}
					},
					{
						data: "mayor_salario",
						className: 'dt-body-right',
						render: (data) => {
							return var_sit_lab.format.moneda.format(data.replace(',','.'))
						}
					},
					{
						data: "salario_promedio",
						className: 'dt-body-right',
						render: (data) => {
							return (data != 'nan') ? var_sit_lab.format.moneda.format(data.replace(',','.')) : var_sit_lab.format.moneda.format(0)
						}
					},
					{
						data: "fecha_consulta",
						className: 'dt-body-center',
						render: (data) => {
							return data? moment(data).format('DD-MM-YYYY') : '-';
						}
					},
				]
			
			TablaPaginada('table_sl_ILArus', 0, 'asc', '', '', null, columns , null , null, null, 15, null, { data: response['ILArus']['IL'] } );
		});
	}
	
	var_sit_lab.get_inf_laboralMareigua = () => {
		$.ajax({
			url		:  base_url + 'atencion_cliente/get_inflaboralMareigua',
			type	: 'POST',
			dataType: 'json',
			data	: {'documento':$("#inp_sl_documento").val(), 'id_solicitud' : $("#id_solicitud").val()}
		}).done( (response) => {
			
			if ( $.fn.DataTable.isDataTable( '#table_sl_ILMareigua_aportes' ) ) 
				$('#table_sl_ILMareigua_aportes').DataTable().destroy();

			if ( $.fn.DataTable.isDataTable( '#table_sl_ILMareigua' ) ) 
				$('#table_sl_ILMareigua').DataTable().destroy();

			let columns = [];
			columns = [
				{
					data: "periodo",
					className: 'dt-body-center'
				},
				{
					data: "NIT",
					className: 'dt-body-center',
				},
				{
					data: "empresa"
				},
				{
					data: "salario",
					className: 'dt-body-right',
					render: (data) => {
						return var_sit_lab.format.moneda.format(data)
					}
				},
			]

			TablaPaginada('table_sl_ILMareigua_aportes', 0, 'desc', '', '', null, columns , null , null, null, 15, null, { data: response['ILMareigua']['APORTES'] } );
			columns = [];
			columns =[
					{
						data: "EPS",
						render: (data) => {
							return data? data: '-';
						}
					},
					{
						data: "AFP",
						render: (data) => {
							return data? data: '-';
						}
					},
					{
						data: "ocupacion",
						className: 'dt-body-center',
						render: (data) => {
							return var_sit_lab.format.numero.format(data) + " %"
						}
					},
					{
						data: "rotacion",
						className: 'dt-body-center',
						render: (data) => {
							return var_sit_lab.format.numero.format(data)
						}
					},
					{
						data: "menor_salario",
						className: 'dt-body-right',
						render: (data) => {
							return var_sit_lab.format.moneda.format(data.replace(',','.'))
						}
					},
					{
						data: "mayor_salario",
						className: 'dt-body-right',
						render: (data) => {
							return var_sit_lab.format.moneda.format(data.replace(',','.'))
						}
					},
					{
						data: "salario_promedio",
						className: 'dt-body-right',
						render: (data) => {
							return (data != 'nan') ? var_sit_lab.format.moneda.format(data.replace(',','.')) : var_sit_lab.format.moneda.format(0)
						}
					},
					{
						data: "fecha_consulta",
						className: 'dt-body-center',
						render: (data) => {
							return data? moment(data).format('DD-MM-YYYY') : '-';
						}
					},
				]
			
			TablaPaginada('table_sl_ILMareigua', 0, 'asc', '', '', null, columns , null , null, null, 15, null, { data: response['ILMareigua']['IL'] } );
		});
	}

	var_sit_lab.update_inf_laboral = (evn) => {
		$(evn).prop('disabled', true).find('i').addClass('fa-spin')
		action = $(evn).data('servicio');

		$.ajax({
			url		:  base_url + 'atencion_cliente/update_inflaboral',
			type	: 'POST',
			dataType: 'json',
			data	: {'documento':$("#inp_sl_documento").val(), 'id_solicitud' : $("#id_solicitud").val(), 'inflaboral' : action}
		}).done( (response) => {
			if (response.status) {
				if (action == 'arus') {
					var_sit_lab.get_inf_laboralArus();
					$(evn).prop('disabled', false).find('i').removeClass('fa-spin');
				}
				if (action == 'experian') {
					var_sit_lab.get_inf_laboralE();
					$(evn).prop('disabled', false).find('i').removeClass('fa-spin');
				}
				if (action == 'mareigua') {
					var_sit_lab.get_inf_laboralMareigua();
					$(evn).prop('disabled', false).find('i').removeClass('fa-spin');
				}
			}
		});
	}

	var_sit_lab.format = {
		moneda : new Intl.NumberFormat('es-CO', {style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0}),
		numero : new Intl.NumberFormat('es-CO', {maximumFractionDigits: 0,})
	}
	
	
