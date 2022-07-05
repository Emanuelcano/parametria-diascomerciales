<style>
	table.modificable input[type=text], table.modificable select {
		border: 0px !important;
		border-bottom: 1px solid #ccc !important;
		height: 20px !important;
		padding-bottom: 0px !important;
		padding-top: 0px !important;
	}
	table.modificable .btn-group-sm>.btn, table.modificable .btn-sm {
		padding: 1px 10px !important;
	}

	a[data-title]:hover:after {
		opacity: 1;
		transition: all 0.1s ease 0.5s;
		visibility: visible;
	}
	a[data-title]:after {
		content: attr(data-title);
		background-color: #000000c9;
		color: #f4f4f4;
		position: absolute;
		padding: 7px;
		white-space: nowrap;
		box-shadow: 1px 1px 3px #222222;
		opacity: 0;
		z-index: 1;
		height: 30px;
		visibility: hidden;
		left: 20px;
		bottom: -6px
	}
	a[data-title] {
		position: relative;
		float: right;
	}

		a[data-title]:hover:after {
			opacity: 1;
			transition: all 0.1s ease 0.5s;
			visibility: visible;
		}

		a[data-title]:after {
			content: attr(data-title);
			background-color: #000000c9;
			color: #f4f4f4;
			position: absolute;
			padding: 7px;
			white-space: nowrap;
			box-shadow: 1px 1px 3px #222222;
			opacity: 0;
			z-index: 1;
			height: 30px;
			visibility: hidden;
			left: 20px;
			bottom: -6px
		}

		a[data-title] {
			position: relative;
			float: right;
		}

		.texto-success {
			color: green;
		}

		.texto-warning {
			color: red;
		}

		.texto-danger {
			color: grey;
		}

		.accordion_gest_agendaemail {
			background-color: #d8d5f9;
			box-shadow: 0px 9px 10px -9px #888888;
			z-index: 1;
			cursor: pointer;
			width: 100%;
			border: none;
			outline: none;
			transition: 0.4s;
		}

		.accordion_gest_agendaemail:hover {
			background-color: #c8bef6;
		}

		.accordion_gest_agendaemail.active {
			background-color: #c8bef6;
		}

		.active.accordion_gest_agendaemail:after {
			content: "\2B9E";
		}

		.accordion_gest_agendaemail:after {
			content: "\2B9F";
			color: black;
			font-weight: bold;
			float: right;
			margin-top: -2em;
		}

		.panel_10 {
			background-color: white;
		}

		.active_panel {
			display: block;
		}

		.gs_laboral {
			background-color: #e0dff5;
		}

		#box_agenda_email th {
			font-weight: 400;
			text-align: center
		}

		#box_agenda_email td {
			font-weight: 700;
		}

		#box_agenda_email .popover {
			border: 0px;
			/* width: 400px; */
			max-width:600px;
		}

		#box_agenda_email .popover-title {
			background-color: #f7f7f7;
			font-size: 14px;
			color: inherit;
		}

		#box_agenda_email .popover-content {
			background-color: inherit;
			color: #333;
			padding: 10px;
			padding-left: 3px;
		}

		.div-table {
			display: table;
			box-sizing: content-box;
			width: 100%;
			border-spacing: 2px;
		}

		.div-table-row {
			display: table-row;
			width: auto;
			clear: both;
		}

		.div-table-col {
			float: left;
			display: table-column;
			border-bottom: 1px solid #e0dddd;
			cursor: pointer;
			font-size: 13px;
			padding: 8px;
			box-sizing: content-box;
			width: 98%;
			text-align: left;
		}

		.div-table-col:hover {
			background-color: #efefef;
		}
		.dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter {
			float: none;
			text-align: right;
			font-size: 10px;
			padding-right: 20px;
		}
</style>
<div id="box_agenda_telf" class="box box-info">
    <div class="box-header with-border" id="titulo">
    </div><!-- end box-header -->
    <div class="box-body" style="font-size: 12px;">
	<input id="solicitud" type="hidden" data-number_soli = "<?=$id_solicitud?>">	
	<input id="client" type="hidden" data-number_doc = "<?=$documento?>>">
	<input id="id_operador" type="hidden" data-number_doc = "<?=$documento?>>" value="<?= $this->session->userdata('idoperador')?>">
        <div class="container-fluid">
            <div class="row">
        
                <div class ="col-sm-12 text-center" style="background-color: #d8d5f9;box-shadow: 0px 9px 10px -9px #888888; z-index: 1;">
                    <h4>DIRECTORIO TELEFÓNICO</h4>
                </div>
                <div class="col-sm-12 cont">
					<th><a class="btn btn-success btn-sm" onclick="mostrarFormularioAgendaTel(<?=$id_solicitud?>,<?=$documento?>)"><i class="fa fa-plus"></i>AGENDAR</a></th>
                    <table class="table modificable " id="table-agenda-telefono">
                        <thead>
                            <th></th>
                            <th>Número</th>
                            
                            <th>Contacto</th>
                            <th>Fuente</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                        </thead>

                        <tbody>
                        </tbody>
                    </table>
                        
                </div>
            </div>
        </div>
    </div>
</div>



<!-- DIRECTORIO MAIL -->
<div id="box_agenda_email" class="box box-info">
	<div class="box-header with-border" id="titulo"></div>
	<input type="hidden" name="inp_age_documento" id="inp_age_documento" value="<?//=$agenda_mail['documento']?>">
	<input type="hidden" name="inp_age_tipo_canal" id="inp_age_tipo_canal" value="<?//=$agenda_mail['tipo_canal']?>">
	<div class="box-body" style="font-size: 12px;">
		<div class="container-fluid">
			<div class="row">
				<div class ="col-sm-12 text-center" style="background-color: #d8d5f9;box-shadow: 0px 9px 10px -9px #888888; z-index: 1;">
					<h4>DIRECTORIO MAIL</h4>
				</div>
				<div class="panel_10 body_agendaemail" >
					<div class="container-fluid">
						<div class="row">
							<div class="box-body" style="font-size: 12px;">
								<div class="container-fluid grid-striped">
									<div class="col-md-12">&nbsp;</div>
									<a class="btn btn-success btn-sm" id="agregarMailAgenda" onclick="mostrarFormularioAgendaMail(<?=$id_solicitud?>,<?=$documento?>)"><i class="fa fa-plus"></i>AGENDAR</a>
									<table class="table table-striped table-bordered display" id="table_agenda_mail">
										<thead>
											<tr class="table-light">
												<th>Cuenta</th>
												<th>Contacto</th>
												<th>Fuente</th>
												<th>Estado</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url('assets/gestion/agenda_telefonica_new.js');?>" ></script>




<script type="text/javascript">
    // $(function () {
    //     $("a.verificacion").click(function (event){
    //         verificarCodigo($(this));
    //     });
        
        
    // });

    $( document ).ready(function() {
        // render_tabla_agenda_mail()
		render_tabla_agendaTlf($("#client").data("number_doc"));
		render_tabla_agenda_mail($("#client").data("number_doc"));
    });


function render_tabla_agenda_mail(documento =null) {
	if (documento== null){
		let documento = $("#client").data('number_doc');
    }
	var id_soli = $("#solicitud").data('number_soli');
	var docu = $("#client").data('number_doc');

    
    var hoy = new Date();
    var hoy_moment = moment(hoy, "YYYY-MM-DD");
    
    $.ajax({
        url: base_url + 'atencion_cliente/agendaMail/' + documento,
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#render_tabla_agenda_mail").html(loading);
        },
        success: function (respuesta) {
            var registros = eval(respuesta);
            

            html = '<table class="table modificable" id="table_agenda_mail">';
            html += '<thead>';
				html += '<th></th>';
				html += '<th>Cuenta</th>';
				html += '<th>Contacto</th>';
				html += '<th>Fuente</th>';
				html += '<th>Estado</th>';
				html += '<th>Editar</th>';
			html += '</thead>';
			html += '<tbody>';
			// console.log(registros.data)
            $.each(registros.data, function (j, valor) {
				// console.log(valor["cuenta"] )
                let botton, fuente_tlf = "";
                let class_icono = "";
                var btn_servicio_ws, btn_servicio_sms = "";
                
				if (valor["fuente"] == "PERSONAL" || 
				    valor["fuente"] == "PERSONAL DECLARADO" ||  
					valor["fuente"] == "PERSONAL LLAMADA" ||
					valor["fuente"] == "PERSONAL WHATSAPP"||
					valor["fuente"] == "PERSONAL ANTERIOR"||
					valor["fuente"] == "REFERENCIA")
				{


					html += "<tr>";
                        html +=' <td><div class="form-check"><input type="checkbox" class="form-check-input" id="ch-agen-'+valor["id"]+'" value ="'+valor["id"]+'" name="ch-telefonos" data-cuenta="'+valor["cuenta"]+'" data-estado="'+valor["estado"]+'"></div></td>';
                        html +=' <td class="numero">'+ valor["cuenta"] +'</td>';
                                            
                        html +='<td><input type="text" class="form-control" id="contacto-agen-'+valor["id"]+'" value="'+valor["contacto"]+'"></td>';
                        html +='<td>';
                        html +=' <select class="form-control" id="fuente-mail-'+valor["id"]+'">';
                        html +=' <option value="PERSONAL" '+ (valor["fuente"].toUpperCase() == "PERSONAL"?'selected':'') +'>Personal</option>';
                        html +=' <option value="PERSONAL DECLARADO" '+ (valor["fuente"].toUpperCase() == "PERSONAL DECLARADO"?'selected':'') +'>Personal Declarado</option>';
                        html +=' <option value="PERSONAL ANTERIOR" '+ (valor["fuente"].toUpperCase() == "PERSONAL ANTERIOR"?'selected':'') +'>Personal Anterior</option>';
                        html +=' <option value="PERSONAL LLAMADA" '+ (valor["fuente"].toUpperCase() == "PERSONAL LLAMADA"?'selected':'') +'>Personal Llamada</option>';
                        html +=' <option value="PERSONAL WHATSAPP" '+ (valor["fuente"].toUpperCase() == "PERSONAL WHATSAPP"?'selected':'') +'>Personal WhatsApp</option>';
                        html +=' <option value="REFERENCIA" '+ (valor["fuente"].toUpperCase() == "REFERENCIA"?'selected':'') +'>Referencia</option>';
                        html +='</select>';
                        html +='</td>';
                        html +='<td>';
                        html +='<select class="form-control" id="estado-mail-'+valor["id"]+'">';
                        html +='<option value="1" '+ (valor["estado"] == 1 ? 'selected': '' ) +'>Activo</option>';
                        html +='<option value="0" '+ (valor["estado"] == 0 ? 'selected': '' ) +'>Fuera de servicio</option>';
                        html +='</select>';
                        html +='</td>';
                        html +='<td><a class="btn btn-info btn-sm" onclick="guardarCambioMail('+valor["id"]+','+documento+')"><i class="fa fa-save"></i></a></td>';



                        // html += '<td class="numero" data-id="'+valor["id"]+'">' + valor['numero'] + '</td>';
                        // html += '<td><div class="form-group" style="margin-bottom: 0px;"><span>' + valor["contacto"] + '</span></div></td>';
                        // html += '<td>' + fuente_tlf + '</td>';
                        // html += '<td data-numero="'+valor['numero']+'"> <select class="form-control selectpicker slc_estado_telefono" data-id="' + valor["id"] + '" data-documento="' + valor["documento"] + '" id="slc_estado_telefono"><option value="1" ' + slc_activo + '>Activo</option><option value="0" ' + slct_inactivo + '>Fuera de servicio</option></select></td>';
                        // html += '<td data-id="'+valor["id"]+'">'+btn_edit+'</td>'
                        html += '</tr>';

				}
            });
            html += '</tbody>';
			// console.log(html)
            $('#table_agenda_mail').html(html);
            TablaPaginada("table_agenda_mail", 0, "asc", "", "");
               
        }
    });
}
function mostrarFormularioAgendaTel(id_solicitud = null,documento = null)
{
		// alert("PRUEBA")
	var datos = {
		id_solicitud: id_solicitud,
		documento: documento,
		tipo:"tel"
	};
	// console.log(datos)
	var obj2 = {function:'agendarTelefono', data:datos};
	window.parent.postMessage(JSON.stringify(obj2), '*');
}
function mostrarFormularioAgendaMail(id_solicitud = null,documento = null)
{
		// alert("PRUEBA")
	var datos = {
		id_solicitud: id_solicitud,
		documento: documento,
		tipo:"mail"
	};
	// console.log(datos)
	var obj2 = {function:'agendarMail', data:datos};
	window.parent.postMessage(JSON.stringify(obj2), '*');
}

function render_tabla_agendaTlf(documento =null) {
	if (documento== null){
		let documento = $("#client").data('number_doc');
    }
	var id_soli = $("#solicitud").data('number_soli');
	var docu = $("#client").data('number_doc');

    
    var hoy = new Date();
    var hoy_moment = moment(hoy, "YYYY-MM-DD");
    
    $.ajax({
        url: base_url + 'atencion_cliente/agendaTelefonica/' + documento,
        type: 'GET',
        dataType: 'json',
        beforeSend: function () {
            var loading =
                '<div class="loader" id="loader-6">' +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "<span></span>" +
                "</div>";
            $("#table-agenda-telefono").html(loading);
        },
        success: function (respuesta) {
            var registros = eval(respuesta);
            

            html = '<table class="table modificable" id="table-agenda-telefono">';
            html += '<thead>';
				html += '<th></th>';
				html += '<th>Número</th>';
				html += '<th>Contacto</th>';
				html += '<th>Fuente</th>';
				html += '<th>Estado</th>';
				html += '<th>Editar</th>';
			html += '</thead>';
			html += '<tbody>';
            $.each(registros, function (j, valor) {

                let botton, fuente_tlf = "";
                let class_icono = "";
                var btn_servicio_ws, btn_servicio_sms = "";

                if (valor["departamento"] != "" && valor["departamento"] != null) {
                    botton = valor["ciudad"] + " / " + valor["departamento"];
                } else {
                    botton = '<i class="fa fa-map-marker text-red"> Agregar </i>';
                }
                
                if (valor["tipo"] == 'MOVIL') {
                    class_icono = '<i class="fa fa-mobile" aria-hidden="true"></i>';
                } else {
                    class_icono = '<i class="fa fa-phone" aria-hidden="true"></i>';
                }
                switch (valor["fuente"]) {
                    case "PERSONAL":
                        fuente_tlf = "Personal";
                        break;
                     case "PERSONAL DECLARADO":
                        fuente_tlf = "Personal Declarado";
                        break;
                     case "PERSONAL LLAMADA":
                        fuente_tlf = "Personal Llamada";
                        break;
                    case "PERSONAL WHATSAPP":
                        fuente_tlf = "Personal Whatsapp";
                        break;
                     case "PERSONAL ANTERIOR":
                        fuente_tlf = "Personal Anterior";
                        break;
                    case "REFERENCIA":
                        fuente_tlf = "Referencia";
                        break;
                    case "LABORAL":
                        fuente_tlf = "Laboral";
                        break;
                    case "BURO_CELULAR":
                        fuente_tlf = "Buro - Celular - D";
                        break;
                    case "BURO_CELULAR_T":
                        fuente_tlf = "Buro - Celular - T";
                        break;
                    case "BURO_LABORAL":
                        fuente_tlf = "Buro - Laboral - D";
                        break;
                    case "BURO_RESIDENCIAL":
                        fuente_tlf = "Buro - Residencial - D";
                        break;
                }
                if (valor["primer_reporte"] != null && valor["primer_reporte"] != "") {
                    var primer_reporte = valor["primer_reporte"]
                    var primer_reporte_moment = moment(primer_reporte, "YYYY-MM-DD");
                    antiguedad = hoy_moment.diff(primer_reporte_moment, 'months');

                } else {
                    antiguedad = 0;
                }
                if (valor["verificado_llamada"] == null) {
                    valor["verificado_llamada"] = 0;
                }
                if (valor["verificado_sms"] == null) {
                    valor["verificado_sms"] = 0;
                }
                if (valor["verificado_whatsapp"] == null) {
                    valor["verificado_whatsapp"] = 0;
                }
                 
				if (valor["estado"] == 0) {
                    slc_activo = '';
                    slct_inactivo = 'selected';
                } else {
                    slc_activo = 'selected';
                    slct_inactivo = '';
                }
				
                if (valor["fuente"] != 'PERSONAL DECLARADO') {
                    btn_edit = '<button class="btn btn-sm  btn_edit_num" style="background:#0073b7;color:white;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
                } else {
                    btn_edit = '<button disabled="true" class="btn btn-sm  btn_edit_num" style="background:gris;color:white;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>';
                }
				if (valor["fuente"] == "PERSONAL" || 
				    valor["fuente"] == "PERSONAL DECLARADO" ||  
					valor["fuente"] == "PERSONAL LLAMADA" ||
					valor["fuente"] == "PERSONAL WHATSAPP"||
					valor["fuente"] == "PERSONAL ANTERIOR"||
					valor["fuente"] == "REFERENCIA")
				{


					html += "<tr>";
                        html +=' <td><div class="form-check"><input type="checkbox" class="form-check-input" id="ch-tel-'+valor["id"]+'" value ="'+valor["id"]+'" name="ch-telefonos" data-numero_telefono="'+valor["numero"]+'" data-estado="'+valor["estado"]+'"></div></td>';
                        html +=' <td class="numero">'+ valor["numero"] +'</td>';
                                            
                        html +='<td><div class="form-group" style="margin-bottom: 0px;" ><input type="text" class="form-control" id="contacto-tel-'+valor["id"]+'" value="'+valor["contacto"]+'"></div></td>';
                        html +='<td>';
                        
                        html +=' <select class="form-control" id="fuente-tel-'+valor["id"]+'">';
                        html +=' <option value="PERSONAL" '+ (valor["fuente"].toUpperCase() == "PERSONAL"?'selected':'') +'>Personal</option>';
                        html +=' <option value="PERSONAL DECLARADO" '+ (valor["fuente"].toUpperCase() == "PERSONAL DECLARADO"?'selected':'') +'>Personal Declarado</option>';
                        html +=' <option value="PERSONAL ANTERIOR" '+ (valor["fuente"].toUpperCase() == "PERSONAL ANTERIOR"?'selected':'') +'>Personal Anterior</option>';
                        html +=' <option value="PERSONAL LLAMADA" '+ (valor["fuente"].toUpperCase() == "PERSONAL LLAMADA"?'selected':'') +'>Personal Llamada</option>';
                        html +=' <option value="PERSONAL WHATSAPP" '+ (valor["fuente"].toUpperCase() == "PERSONAL WHATSAPP"?'selected':'') +'>Personal WhatsApp</option>';
                        html +=' <option value="REFERENCIA" '+ (valor["fuente"].toUpperCase() == "REFERENCIA"?'selected':'') +'>Referencia</option>';
                        html +='</select>';
                        html +='<input type="hidden" id="tipo-tel-'+valor["id"]+'" value="'+valor["tipo"].toUpperCase()+'" ><input type="hidden" id="parentesco-tel-'+valor["id"]+'" value="'+valor["id_parentesco"].toUpperCase()+'" >';
                        html +='</td>';
                        html +='<td>';
                        html +='<select class="form-control" id="estado-tel-'+valor["id"]+'">';
                        html +='<option value="1" '+ (valor["estado"] == 1 ? 'selected': '' ) +'>Activo</option>';
                        html +='<option value="0" '+ (valor["estado"] == 0 ? 'selected': '' ) +'>Fuera de servicio</option>';
                        html +='</select>';
                        html +='</td>';
                        html +='<td><a class="btn btn-info btn-sm" onclick="guardarCambioTlf('+valor["id"]+','+documento+')"><i class="fa fa-save"></i></a></td>';



                        // html += '<td class="numero" data-id="'+valor["id"]+'">' + valor['numero'] + '</td>';
                        // html += '<td><div class="form-group" style="margin-bottom: 0px;"><span>' + valor["contacto"] + '</span></div></td>';
                        // html += '<td>' + fuente_tlf + '</td>';
                        // html += '<td data-numero="'+valor['numero']+'"> <select class="form-control selectpicker slc_estado_telefono" data-id="' + valor["id"] + '" data-documento="' + valor["documento"] + '" id="slc_estado_telefono"><option value="1" ' + slc_activo + '>Activo</option><option value="0" ' + slct_inactivo + '>Fuera de servicio</option></select></td>';
                        // html += '<td data-id="'+valor["id"]+'">'+btn_edit+'</td>'
                        html += '</tr>';

				}
            });
            html += '</tbody>';
            $('#table-agenda-telefono').html(html);
            TablaPaginada("table-agenda-telefono", 0, "asc", "", "");
               
        }
    });
}



function TablaPaginada(
	nombreTabla,
	colOrdenar,
	fOrdenar,
	colOrdenar2 = "",
	fOrdenar2 = "",
	ajax = null,
	columns = null,
	columnDefs = null,
	options_dt = null,
	createdRow = null,
	pageLength = null,
	footerCallback = null,
	extras = null
) {
	var tabla = "#" + nombreTabla;
	var columnaOrdenar = colOrdenar;
	var formaOrdenar = fOrdenar;

	if (colOrdenar2 == "") {
		var columnaOrdenar2 = colOrdenar;
		var formaOrdenar2 = fOrdenar;
	} else {
		var columnaOrdenar2 = colOrdenar2;
		var formaOrdenar2 = fOrdenar2;
	}
	
	//alert(columnaOrdenar2+formaOrdenar2)

	let options = {
		
		lengthMenu: [
			[5, 10, 15, 25, 50],
			[5, 10, 15, 25, 50],
			
		],
	
		//"aaSorting": [[columnaOrdenar,formaOrdenar], [columnaOrdenar2,formaOrdenar2]],
		order: [],
		language: {
			
			sProcessing: "Procesando...",
			sLengthMenu: "Mostrar _MENU_ registros",
			sZeroRecords: "No se encontraron resultados",
			sEmptyTable: "Ningún dato disponible en esta tabla",
			sInfo: "Del _START_ al _END_ de un total de _TOTAL_ reg.",
			sInfoEmpty: "0 registros",
			sInfoFiltered: "(filtrado de _MAX_ reg.)",
			sInfoPostFix: "",
			sSearch: "Buscar:",
			sUrl: "",
			sInfoThousands: ",",
			sLoadingRecords: "Cargando...",
			oPaginate: {
				sFirst: "Primero",
				sLast: "Último",
				sNext: "Sig",
				sPrevious: "Ant"
			},
			oAria: {
				sSortAscending:
					": Activar para ordenar la columna de manera ascendente",
				sSortDescending:
					": Activar para ordenar la columna de manera descendente"
			}
		}
	};
	
	if (ajax !== null) {
		options.ajax = ajax;
	}
	if (columns !== null) {
		options.columns = columns;
	}
	if (columnDefs !== null) {
		options.columnDefs = columnDefs;
	}

	if (options_dt !== null) {
		options.order = options_dt.order;
		options.createdRow = options_dt.createdRow;
	}
	if (createdRow !== null) {
		options.createdRow = createdRow;
	}

	if(pageLength !== null){
		options.displayLength = pageLength;
	}

	if(footerCallback !== null){
		options.footerCallback = footerCallback;
	}
	if(extras !== null){
		$.each(extras, function(i,el){
			options[i] = el
		})
	}
	auxTabla = $(tabla).DataTable(options);
}



</script>