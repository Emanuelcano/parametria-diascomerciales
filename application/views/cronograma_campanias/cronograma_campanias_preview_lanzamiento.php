<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<?php $this->load->view('supervisores/menu/menu_supervisores'); ?>
<div id="section_search_solicitud">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="" id="result">
				<div class="box box-success">
					<div class="box-header with-border"><h3 class="box-title">Lanzamiento preliminar Campañia</h3></div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-6">
								<div class="box box-primary">
									<div class="box-header with-border"><h3 class="box-title">Info Campañia</h3></div>
									<div class="box-body">
										<div class="row" style="margin-bottom: 10px">
											<div class="col-md-4 text-right"><strong>Campaña</strong></div>
											<div class="col-md-8"><?=$campaing['nombre_logica']?></div>
										</div>
										<div class="row" style="margin-bottom: 10px">
											<div class="col-md-4 text-right"><strong>Tipo</strong></div>
											<div class="col-md-8"><?=$campaing['type_logic']?></div>
										</div>
										<div class="row" style="margin-bottom: 10px">
											<div class="col-md-4 text-right"><strong>Template Id</strong></div>
											<div class="col-md-8"><?=$templateId?></div>
										</div>
										<div class="row" style="margin-bottom: 10px">
											<div class="col-md-4 text-right"><strong>Canal</strong></div>
											<div class="col-md-8"><?=($canal==='15140334')?'VENTAS':'COBRANZAS' ?></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="box box-warning">
									<div class="box-header">
										<h3 class="box-title">Resultados envios preliminares</h3>
									</div>

									<div class="box-body no-padding">
										<table class="table table-striped" id="tablePreview">
											<thead>
											<tr>
												<th>Id Solicitud</th>
												<th>Documento</th>
												<th>Status</th>
												<th>Telefono</th>
												<th>Fecha y Hora</th>
												<th>Template</th>
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
					<div class="box-footer text-right">
						<button class="btn btn-info" id="sendPreliminar">Realizar envio preliminar</button>
						<button class="btn btn-success" id="sendTemplates" disabled>Enviar Templates Restantes</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
  	const btnPreliminar = document.getElementById('sendPreliminar');
	const btnSend = document.getElementById('sendTemplates');
	
	btnSend.addEventListener('click', function() {
		btnSend.innerHTML = '<i class="fa fa-refresh fa-spin"></i>';
		btnSend.disabled = true;

		$.ajax({
			type: "POST",
			url: '<?=URL_NOTIFICACIONES?>/api/events/addJob',
			data: { "idEvent": <?=$idEvent?>},
			success: function (response) {
				$.ajax({
					type: "POST",
					url: base_url + '/cronograma_campanias/Cronogramas/markAsSendedPrelanzamiento',
					data: { "id": <?=$idPrelanzamiento?>},
					success: function (response) {
						btnSend.innerHTML = 'ENVIADO';
					}
				});
			}
		});
	})
	
	btnPreliminar.addEventListener('click', function() {
		btnPreliminar.innerHTML = '<i class="fa fa-refresh fa-spin"></i>';
		btnPreliminar.disabled = true;

			$.ajax({
				type: "POST",
				url: base_url + 'api/campanias/envioPreliminarCampaniasWhatsapp',
				data: { 
					"idCampania": <?=$idCampania?>,
					"idTemplate": <?=$templateId?>,
					"canal": <?=$canal?>,
				},
				success: function (response) {
					btnPreliminar.innerHTML = 'Realizar envio preliminar';
					btnSend.removeAttribute("disabled");
					renderTable(response.data);
				},
				error: function (response) {
					alert('Se ha producido un Error');
					btnPreliminar.innerHTML = 'Realizar envio preliminar';
					btnPreliminar.removeAttribute("disabled");
				}
			});
	});
	
	function renderTable(data) {
		console.log(data);
		$( data ).each(function( index, item ) {
			let tr = renderRow(item);
			let body = $('#tablePreview tbody');
			body.append(tr);
		});
	}
	
	function renderRow(data) {
		let tr = $('<tr>');
		let td = $('<td>');
		
		let idSolicitud = td.clone().html(data.idSolicitud);
		let documento = td.clone().html(data.documento);

		let spanStatus = $('<span>');
		spanStatus.addClass('badge');
		if (data.sms_status == 'queued') {
			spanStatus.addClass('bg-yellow')	
		} else if (data.sms_status == 'delivered') {
			spanStatus.addClass('bg-lime')
		} else if (data.sms_status == 'sent') {
			spanStatus.addClass('bg-green')
		} else if (data.sms_status == 'undelivered') {
			spanStatus.addClass('bg-orange')
		} else if (data.sms_status == 'failed') {
			spanStatus.addClass('bg-red')
		}
		spanStatus.html(data.sms_status);

		let status	= td.clone().html(spanStatus);
		let fecha = td.clone().html(data.fecha);
		let telefono = td.clone().html(data.telefono);
		let pre = $('<pre>');
		let template = td.clone().append(pre.html(data.template));
		
		tr.append(idSolicitud);
		tr.append(documento);
		tr.append(status);
		tr.append(fecha);
		tr.append(telefono);
		tr.append(template);
		
		return tr;
	}
</script>



<style>
	#modulo-content, html {
		background-color: #ecf0f5 !important;
	}
</style>


