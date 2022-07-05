<style type="text/css">
    .box-title {
    font-size: 23px!important;
    }
    .dataTables_wrapper {
        margin-top: 15px;
    }
    .select2-container {
    width: 100%!important;
    }
    .active.accordion_10:after {
    content: "\2B9E";
    }
    
</style>

<div id="section_form_crm" style="background: #FFFFFF;">
	<div class="row">
		<div class="col-md-6">
			<H4><strong>CAMPAÑAS MANUALES DESDE CRM </strong></H4>
		</div>
		<div class="col-md-6 text-right">
			<button class="btn btn-success" id="nuevaCampania">NUEVA CAMPAÑA</button>
			<button class="btn btn-warning" id="cerrarCampania" style="display: none">CERRAR</button>
		</div>
	</div>
	<div>
		<form id="form_crm" name="form_crm" class="form-inline" method="POST">
			<div class="panel panel-default" id="campaniaContainer" style="display: none">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12 col-md-6">
							<?php $this->load->view('supervisores/partials/campanias_manuales/datos_campania'); ?>
						</div>
						<div class="col-sm-12 col-md-6">
							<?php $this->load->view('supervisores/partials/campanias_manuales/envios_automaticos'); ?>
						</div>
						<div class="col-sm-12 col-md-6">
							<?php $this->load->view('supervisores/partials/campanias_manuales/gestion_automatica'); ?>
						</div>
					</div>
				</div>
				<div class="panel-footer text-right">
					<button type="button" class="form-control btn btn-info" id="prueba_crm" onclick="probar_campania()" >Prueba</button>
					<button type="button" id="crear_crm" class="form-control btn btn-success" style="display:none" onclick="createCampania()"> Crear </button>
					<button type="button" id="update_crm" class="form-control btn btn-success " style="display:none" onclick="updateCampania()"> Actualizar </button>
				</div>
			</div>
		</form>
	</div>
    
    <div id="result" style="margin-top: 15px">
        <h3 class="box-title"><small><strong>CAMPAÑA CARGADA</strong></small>&nbsp;</h3>
        <table align="center" id="table_crm" class="table table-responsive table-striped table=hover display" width="100%" >
            <thead style="font-size: smaller; ">
                <tr class="info">
                    <th style="text-align: center;">Id</th>
                    <th style="text-align: center;">Descripcion</th>
                    <th style="text-align: center;">Tipo</th>
                    <th style="text-align: center;">Grupo</th>
                    <th style="text-align: center;">Desde</th>
                    <th style="text-align: center;">Hasta</th>
                    <th style="text-align: center;">Acción</th>
                    <th style="text-align: center;">Cretidos por cliente</th>
                    <th style="text-align: center;">Exclusion</th>
                    <th style="text-align: center;">Orden</th>
                    <th style="text-align: center;">Asignar</th>
                    <th style="text-align: center;">Regestionar a dias</th>
                    <th style="text-align: center;">Estado</th>
                    <th style="text-align: center;">Fecha_solicitud</th>
                    <th style="text-align: center;"></th>
                </tr>
            </thead>
            <tbody style="font-size: 12px; text-align: center;" id="tb_body"></tbody>
        </table>
    </div>
    <div id="result_busqueda" class="hide">
        <h3 class="box-title"><small><strong>PRUEBA DE CAMPAÑA</strong></small>&nbsp;</h3>
        <table align="center" id="busqueda_crm" class="table table-responsive table-striped table=hover display" width="100%" >
            <thead style="font-size: smaller; ">
                <tr class="info">
                    <th style="text-align: center;">Unlima Gestion</th>
                    <th style="text-align: center;">N°</th>
                    <th style="text-align: center;">Documento</th>
                    <th style="text-align: center;">Solicitante</th>
                    <th style="text-align: center;">Monto Prestado</th>
                    <th style="text-align: center;">Fecha Vencimiento</th>
                    <th style="text-align: center;">Deuda al Día</th>
                    <th style="text-align: center;">Dias Atraso</th>
                    <th style="text-align: center;">Estado</th>
                    <th style="text-align: center;">Gestion</th>

                </tr>
            </thead>
            <tbody style="font-size: 12px; text-align: center;" id="tb_body"></tbody>
        </table>
    </div>
		<?php $this->load->view('supervisores/partials/campanias_manuales/view_campania'); ?>
    </div>
</div>

<script type="text/javascript">
	function matchStart(params, data) {
		// If there are no search terms, return all of the data
		if ($.trim(params.term) === '') {
			return data;
		}

		// Skip if there is no 'children' property
		if (typeof data.children === 'undefined') {
			return null;
		}

		// `data.children` contains the actual options that we are matching against
		var filteredChildren = [];
		$.each(data.children, function (idx, child) {
			if (child.text.toUpperCase().indexOf(params.term.toUpperCase()) >= 0) {
				filteredChildren.push(child);
			}
		});

		// If we matched any of the timezone group's children, then set the matched children on the group
		// and return the group object
		if (filteredChildren.length) {
			var modifiedData = $.extend({}, data, true);
			modifiedData.children = filteredChildren;

			// You can return modified objects from here
			// This includes matching the `children` how you want in nested data sets
			return modifiedData;
		}

		// Return `null` if the term should not be displayed
		return null;
	}
	
    function validate() {
		// 	if($('#tipoo').val() === 'PREVENTIVA'){
		// 		$("#input").addClass("hide");
		// 	}
	  //
		// 	if($('#tipoo').val() === 'MORA'){
		// 		$("#input").removeClass("hide");
		// 	}
		//	
		// 	if($('#grupo').val() == 'TODAS'){
		// 		$("#input_clientes").removeClass("hide");
		// 	}
		// 	if($('#grupo').val() == 'RETANQUEO'){
		// 		$("#input_clientes").removeClass("hide");
		// 	}
		// 	if($('#grupo').val() == 'PRIMARIA'){
		// 		$("#input_clientes").addClass("hide");
		// 	}
		//	
		//	
	  // console.log('aaaa');
			
		let tipoo = $('#tipoo').val(); 
		
        if( tipoo === 'PREVENTIVA'){
            $("#input").hide(); //
			$("#grupo").show(); //
			$("#grupoVentas").hide(); //
			$("#grupoVentas").val(''); //
        }
		if(tipoo === 'MORA'){
            $("#input").show(); //
			$("#grupo").show(); //
			$("#grupoVentas").hide(); //
			$("#grupoVentas").val(''); //
        }
		if(tipoo === 'VENTAS'){
			$("#input").hide(); //
			$("#input_clientes").hide(); //
			$("#grupo").hide(); //
			$("#grupoVentas").show(); //
		}

		let grupoVentas = $('#grupoVentas').val();
		if (grupoVentas === 'TODOS') {
			$("#input_ventas").hide();
		}
		if (grupoVentas === 'ALTA_CLIENTE') {
			// input tipo DATE para seleccionar una fecha
			$("#input_ventas").show();
			showInputVentas('ventas_alta_cliente_input');
		}
		if (grupoVentas === 'ULTIMO_OTORGAMIENTO') {
			// input tipo DATE para seleccionar una fecha
			$("#input_ventas").show();
			showInputVentas('ventas_ultimo_otorgamiento_input');
		}
		if (grupoVentas === 'CANTIDAD_CREDITOS') {
			// input que solo permita un valor numérico
			$("#input_ventas").show();
			showInputVentas('ventas_cantidad_creditos_input');
		}
		if (grupoVentas === 'MAYOR_ATRASO') {
			// input que solo permita un valor numérico
			$("#input_ventas").show();
			showInputVentas('ventas_mayor_atraso_input');
		}
		
		if (tipoo != 'VENTAS') {
			$("#grupoVentas").hide();		//
			$("#ventas_mayor").hide();		//
			$("#ventas_menor").hide();		//
			if($('#grupo').val() == 'TODAS'){
				$("#input_clientes").show();
				// $("#input").hide();
			}
			if($('#grupo').val() == 'RETANQUEO'){
				$("#input_clientes").show();
			}
			if($('#grupo').val() == 'PRIMARIA'){
				$("#input_clientes").hide();
			}	
		}
    }
    $('#exclusiones').select2({
        placeholder: '.: Exclusiones :.',
        multiple : true
     });
	$('#operadores').select2({
		placeholder: '.: Seleccione los tipos de Operadores :.',
		multiple : true
	});
    function ValidarNumeros(event){
        const reg = new RegExp(/^\d+$/, 'g');
        //const cadena = reg.test(event.key);
        if (!reg.test(event.key))
        {
            event.preventDefault();
        }
    }
    listar_campanias_crm();
    $( document ).ready(function() {
	    $("#automatico").change(function(){
			if ($(this).is(":checked") ) {
				$("#opcionesAutomatico").show();
			} else {
				$("#opcionesAutomatico").hide();
			}
		});		
			
		$("#cerrarCampania").click(function(){
			$("#campaniaContainer").hide();
			$("#cerrarCampania").hide();
			$("#nuevaCampania").show();
			
		});
		$("#nuevaCampania").click(function(){
			$("#campaniaContainer").show();
			$("#view").addClass('hide');
			$('#result').removeClass('hide');
			$('#form_crm').removeClass('hide');
			$("#nuevaCampania").hide();
			$("#cerrarCampania").show();	
		});	
			
	  	cargar_select();
		$('#canal').select2({
			minimumResultsForSearch: Infinity
		});
		$('#templateWhatsapp').select2({
			matcher: matchStart
		});
		$('#templateSMS').select2({
			matcher: matchStart
		});
		
		$('#templateEmail').select2({
			matcher: matchStart
		});
    });
		
	function showInputVentas(id) {
		$("#ventas_alta_cliente_input").hide();
		$("#ventas_alta_cliente_input").val('');
		$("#ventas_ultimo_otorgamiento_input").hide();
		$("#ventas_ultimo_otorgamiento_input").val('');
		$("#ventas_cantidad_creditos_input").hide();
		$("#ventas_cantidad_creditos_input").val('');
		$("#ventas_mayor_atraso_input").hide();
		$("#ventas_mayor_atraso_input").val('');
		$(`#${id}`).show();
		if (id === 'ventas_mayor_atraso_input') {
			$("#ventas_menor").show();
			$("#ventas_mayor").hide();
		} else {
			$("#ventas_mayor").show();
			$("#ventas_menor").hide();
		}
	}
</script>
<script src="<?php echo base_url('assets/js/Chart.min.js');?>"></script>
<script src="<?php echo base_url('assets/js/chartjs-plugin-datalabels.min.js');?>"></script>
