<div id="box_real_extinguido" class="box box-info">
	<div class="box-body" style="font-size: 12px;">
		<?php if (empty($data)) { ?>
			<table class="table table-bordered table-responsive display table_listas_restrictivas">
				<tr><td style="text-align: center">Sin información</td></tr>
			</table>
		<?php } else { ?>
			<table class="table table-bordered table-responsive display table_listas_restrictivas">
				<thead>
				<tr>
					<th>CODIGO</th>
					<th>LISTA</th>
					<th>NOMBRES</th>
					<th>PRIMER APELLIDO</th>
					<th>DOCUMENTO</th>
					<th>DELITO</th>
					<th>COMENTARIOS</th>
					<th>SANCION</th>
					<th>CIUDAD</th>
					<th>PROVINCIA</th>
					<th>PAIS</th>
					<th>LINK</th>
					<th>FUENTE</th>
				</tr>
				<tbody>
				<?php foreach ($data as $item) { ?>
					<tr>
						<td><?=$item['codigoLista']?></td>
						<td><?=$item['nombreLista']?></td>
						<td><?=$item['nombres']?></td>
						<td><?=$item['primerApellidoORazonSocial']?></td>
						<td><?=$item['idIdentfy']?></td>
						<td><?=$item['noticiaDelito']?></td>
						<td><?=$item['comentarios']?></td>
						<td><?=$item['sancion']?></td>
						<td><?=$item['direccionCiudad']?></td>
						<td><?=$item['direccionProvincia']?></td>
						<td><?=$item['direccionPais']?></td>
						<td><a href="<?=$item['link']?>" target="_blank"><?=$data[0]['link']?></a></td>
						<td><?=$item['noticiaFuente']?></td>
					</tr>
				<?php } ?>

				</tbody>
			</table>
		<?php } ?>

	</div>
</div>
