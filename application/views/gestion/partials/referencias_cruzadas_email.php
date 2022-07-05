<br><br>
<br><br>
<div id="box_real_extinguido" class="box box-info">
	<div class="box-header with-border gs_laboral" id="titulo">
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-12" style="text-align: center;">
					<strong>EMAILS</strong>
				</div>
			</div>
		</div>
	</div>
	<div class="box-body" style="font-size: 12px;">
		<?php if (empty($data)) { ?>
			<table class="table table-bordered table-responsive display table_listas_restrictivas">
				<tr><td style="text-align: center">Sin información</td></tr>
			</table>
		<?php } else { ?>
			<table class="table table-bordered table-responsive display table_listas_restrictivas">
				<thead>
				<tr>
					<th colspan="3">DECLARADO EN ESTA SOLICITUD</th>
					<th colspan="11" style="background-color: #ffc000">APARECE EN LAS SOLICITUDES DE</th>
				</tr>
				<tr>
					<th>EMAIL</th>
					<th>FUENTE</th>
					<th>ANTIG</th>
					<th style="background-color: #ffc000">NOMBRES Y APELLIDOS</th>
					<th style="background-color: #ffc000">DOCUMENTO</th>
					<th style="background-color: #ffc000">CONTACTO</th>
					<th style="background-color: #ffc000">FUENTE</th>
					<th style="background-color: #ffc000">ANTIG</th>
					<th style="background-color: #ffc000">No. SOLICITUDES</th>
					<th style="background-color: #ffc000">ES CLIENTE</th>
					<th style="background-color: #ffc000">No. CREDITOS</th>
					<th style="background-color: #ffc000">ATRASOS</th>
					<th style="background-color: #ffc000">ESTADO</th>
					<th style="background-color: #ffc000">DEUDA</th>
				</tr>
				<tbody>
				<?php foreach ($data as $item) { ?>
					<tr>
						<td><?=$item['cuenta']?></td>
						<td><?=$item['fuente_cruzada']?></td>
						<?php
							if (!is_null($item['primer_reporte_cruzado'])) {
								$d1 = new DateTime($item['primer_reporte_cruzado']);
								$d2 = new DateTime('now');
								$interval = $d2->diff($d1);
								$antiguedad = $interval->m + 12*$interval->y;
							} else {
								$antiguedad = "";
							}
						?>
						<td><?=$antiguedad;?></td>
						<td><?=$item['nombres'] . " " . $item['apellidos']?></td>
						<td><?=$item['documento']?></td>
						<td><?=$item['contacto']?></td>
						<td><?=$item['fuente']?></td>
						
						<?php
						if (!is_null($item['primer_reporte'])) {
							$d1 = new DateTime($item['primer_reporte']);
							$d2 = new DateTime('now');
							$interval = $d2->diff($d1);
							$antiguedad2 = $interval->m + 12*$interval->y;
						} else {
							$antiguedad2 = "";
						}
						?>
						<td><?=$antiguedad2;?></td>
						<td><?=$item['nro_solicitudes']?></td>
						<!-- si es_cliente es "NO" compruebo si nro de creditos > a 0 entonces SI es cliente -->
						<td><?=(!is_null($item['es_cliente']) ? 
									"SI" : 
									($item['nro_creditos'] > 0) ? 
											"SI" : 
											"NO" 
							)?></td> 
						<td>
							<?php if  ($item['nro_creditos'] > 0 ) { ?>
								<?=$item['nro_creditos']?>
							<?php  } ?>
						</td>
						<td><?=$item['dias_atraso']?></td>
						<td><?=(is_null($item['estado']) ? "VIGENTE": strtoupper($item['estado'] ))?></td>
						<td style="text-align: right;">
							<?php if  ($item['monto_cobrar'] > 0 ) { ?>
								<?=number_format((float) $item['monto_cobrar'],0, ',','.')?>
							<?php  } ?>
						</td>
					</tr>
				<?php } ?>

				</tbody>
			</table>
		<?php } ?>

	</div>
</div>
