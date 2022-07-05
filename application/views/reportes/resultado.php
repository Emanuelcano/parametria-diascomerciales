<style type="text/css">
	table tbody > td {
    width: 8%;
    padding: 0px;
    padding-left: 10px;
    text-align: center;
    background: #7FB3D5;
}
</style>

<div class="container-fluid">
    <div class="row">
    	<div class="col-md-12">
			<table class="table table-bordered table-striped">
	        	<thead style="font-size:smaller";>
	        		<tr>
						<th colspan="3" class="text-center" style="background: #E7E6E6;"></th>
						<th colspan="3" class="text-center" style="background: #AEAAAA;">Solicitudes</th>
						<th colspan="4" class="text-center" style="background: #ACB9CA;">Buro</th>
						<th colspan="2" class="text-center" style="background: #FFC000;">Gestión</th>
						<th colspan="2" class="text-center" style="background: #C6E0B4;">Automática</th>
						<th colspan="8" class="text-center" style="background: #BDD7EE;">Consultor Originación</th>
						<th colspan="2" class="text-center" style="background: #F8CBAD;">Anti Fraude</th>
						<th colspan="4" class="text-center" style="background: #A9D08E;">Desembolso</th>
					</tr>
					<tr>
						<th class="text-center" style="background: #E7E6E6;">Canal</th>
						<th class="text-center" style="background: #E7E6E6;">Tipo</th>
						<th class="text-center" style="background: #E7E6E6;">Periodo</th>
						<th class="text-center" style="background: #AEAAAA;">Total</th>
						<th class="text-center" style="background: #AEAAAA;">Sin Analizar</th>
						<th class="text-center" style="background: #AEAAAA;">%</th>
						<th class="text-center" style="background: #ACB9CA;">Rechazadas</th>
						<th class="text-center" style="background: #ACB9CA;">%</th>
						<th class="text-center" style="background: #ACB9CA;">Aprobadas</th>
						<th class="text-center" style="background: #ACB9CA;">%</th>
						<th class="text-center" style="background: #FFC000;">Sin gestión</th>
						<th class="text-center" style="background: #FFC000;">%</th>
						<th class="text-center" style="background: #C6E0B4;">ANALISIS</th>
						<th class="text-center" style="background: #C6E0B4;">%</th>
						<th class="text-center" style="background: #BDD7EE;">VERIFICADOS</th>
						<th class="text-center" style="background: #BDD7EE;">%</th>
						<th class="text-center" style="background: #BDD7EE;">VALIDADOS</th>
						<th class="text-center" style="background: #BDD7EE;">%</th>
						<th class="text-center" style="background: #BDD7EE;">APROBADOS</th>
						<th class="text-center" style="background: #BDD7EE;">%</th>
						<th class="text-center" style="background: #BDD7EE;">RECHAZADOS</th>
						<th class="text-center" style="background: #BDD7EE;">%</th>
						<th class="text-center" style="background: #F8CBAD;">VISADA</th>
						<th class="text-center" style="background: #F8CBAD;">%</th>
						<th class="text-center" style="background: #A9D08E;">TRANSF.</th>
						<th class="text-center" style="background: #A9D08E;">%</th>
						<th class="text-center" style="background: #A9D08E;">PAGADA</th>
						<th class="text-center" style="background: #A9D08E;">%</th>
					</tr>
	        	</thead>
	        	<tbody style="font-size: 11px">
				<?php 
					if( $datos['tipo_informe'] == 'RESUMIDO')
					{
						$resultados = $datos['resumido'];
					}else{
						$resultados = $datos['detalles'];
					}
				?>
				<?php foreach ($resultados as $key => $row): ?>
					<tr>
						<!-- Canal -->
						<td class="text-center"><?php echo isset($datos['canal'])? $datos['canal'] : ''; ?></td>
						<!-- Tipo solicitud-->
						<td class="text-center"><?php echo isset($datos['tipo_solicitud'])? $datos['tipo_solicitud'] : ''; ?></td>
						<!-- Periodo -->
						<td class="text-center"><?php echo isset($row['periodo'])? $row['periodo']: ''; ?></td>
						<!-- Cant solicitudes -->
						<td class="text-center"><?php echo ($row['solicitudes']!=0) ? $row['solicitudes']: '-'; ?></td>
						<!-- Buro sin analizar -->
						<td class="text-center"><?php echo (isset($row['buro']['sin_analizar']) && $row['buro']['sin_analizar']!=0 ) ? $row['buro']['sin_analizar']: '-'; ?></td>
						<td class="text-center"><?php echo ($row['solicitudes'] != 0 && $row['buro']['sin_analizar'] != 0) ? '%'.number_format(($row['buro']['sin_analizar'] / $row['solicitudes']) * 100, 2) : '-'?></td>
						<!-- Buro rechazadas -->
						<td class="text-center"><?php echo (isset($row['buro']['rechazado']) && $row['buro']['rechazado'] != 0) ? $row['buro']['rechazado']: '-'; ?></td>
						<td class="text-center"><?php echo ($row['solicitudes'] != 0 && $row['buro']['rechazado'] != 0) ? '%'.number_format(($row['buro']['rechazado'] / $row['solicitudes']) * 100, 2) : '-'?></td>
						<!-- Buro aprobadas -->
						<td class="text-center"><?php echo (isset($row['buro']['aprobado']) && $row['buro']['aprobado'] != 0) ? $row['buro']['aprobado']: '-'; ?></td>
						<td class="text-center"><?php echo ($row['solicitudes'] != 0 && $row['buro']['aprobado'] != 0) ? '%'.number_format(( $row['buro']['aprobado'] / $row['solicitudes']) * 100, 2) : '-'?></td>
						<!-- Gestion sin gestion - Muestra la cantidad de solicitudes que entraron en esa fecha y no tuvieron ningun registro-->
						<td class="text-center"><?php echo (isset($row['track_gestion']['sin_gestion']) && $row['track_gestion']['sin_gestion'] != 0) ? $row['track_gestion']['sin_gestion']: '-'; ?></td>
						<td class="text-center"><?php echo (isset($row['buro']['aprobado']) && $row['buro']['aprobado'] != 0 && $row['track_gestion']['sin_gestion'] != 0) ? '%'.number_format(( $row['track_gestion']['sin_gestion'] / $row['buro']['aprobado']) * 100, 2) : '-'?></td>
						<!-- Estado en analisis -->
						<td class="text-center"><?php echo (isset($row['estados']['analisis']) && $row['estados']['analisis'] != 0) ? $row['estados']['analisis']: '-'; ?></td>
						<td class="text-center"><?php echo (isset($row['buro']['aprobado']) && $row['solicitudes'] != 0 && $row['estados']['analisis'] != 0) ? '%'.number_format(( $row['estados']['analisis'] / $row['buro']['aprobado']) * 100, 2) : '-'?></td>
						<!-- Estado en verificado -->
						<td class="text-center"><?php echo (isset($row['estados']['verificado']) && $row['estados']['verificado'] != 0) ? $row['estados']['verificado']: '-'; ?></td>
						<td class="text-center"><?php echo (isset($row['buro']['aprobado']) && $row['buro']['aprobado'] != 0 && $row['estados']['verificado'] != 0) ? '%'.number_format(( $row['estados']['verificado'] / $row['buro']['aprobado']) * 100, 2) : '-'?></td>
						<!-- Estado Validado -->
						<td class="text-center"><?php echo (isset($row['estados']['validado']) && $row['estados']['validado'] != 0) ? $row['estados']['validado']: '-'; ?></td>
						<td class="text-center"><?php echo (isset($row['buro']['aprobado']) && $row['buro']['aprobado'] != 0 && $row['estados']['validado'] != 0) ? '%'.number_format(( $row['estados']['validado'] / $row['buro']['aprobado']) * 100, 2) : '-'?></td>
						<!-- Estado Aprobado -->
						<td class="text-center"><?php echo (isset($row['estados']['aprobado']) && $row['estados']['aprobado'] != 0) ? $row['estados']['aprobado']: '-'; ?></td>
						<td class="text-center"><?php echo (isset($row['buro']['aprobado']) && $row['buro']['aprobado'] != 0 && $row['estados']['aprobado'] != 0) ? '%'.number_format(( $row['estados']['aprobado'] / $row['buro']['aprobado']) * 100, 2) : '-'?></td>
						<!-- Estado Rechazado -->
						<td class="text-center"><?php echo (isset($row['estados']['rechazado']) && $row['estados']['rechazado'] != 0) ? $row['estados']['rechazado']: '-'; ?></td>
						<td class="text-center"><?php echo (isset($row['buro']['aprobado']) && $row['buro']['aprobado'] != 0 && $row['estados']['rechazado'] != 0) ? '%'.number_format(( $row['estados']['rechazado'] / $row['buro']['aprobado']) * 100, 2) : '-'?></td>
						<!-- Anti fraude - VISADA -->
						<td class="text-center"><?php echo (isset($row['visados']['visado']) && $row['visados']['visado'] != 0) ? $row['visados']['visado']: '-'; ?></td>
						<td class="text-center"><?php echo (isset($row['buro']['aprobado']) && $row['buro']['aprobado'] != 0 && $row['visados']['visado'] != 0) ? '%'.number_format(( $row['visados']['visado'] / $row['buro']['aprobado']) * 100, 2) : '-'?></td>
						<!-- Estado Transfiriendo -->
						<td class="text-center"><?php echo (isset($row['estados']['transfiriendo']) && $row['estados']['transfiriendo'] != 0) ? $row['estados']['transfiriendo']: '-'; ?></td>
						<td class="text-center"><?php echo ($row['buro']['aprobado'] && $row['buro']['aprobado'] != 0 && $row['estados']['transfiriendo'] != 0) ? '%'.number_format(( $row['estados']['transfiriendo'] / $row['buro']['aprobado']) * 100, 2) : '-'?></td>
						<!-- Estado Pagado -->
						<td class="text-center"><?php echo (isset($row['estados']['pagado']) && $row['estados']['pagado'] != 0) ? $row['estados']['pagado']: '-'; ?></td>
						<td class="text-center"><?php echo ($row['buro']['aprobado'] && $row['buro']['aprobado'] != 0 && $row['estados']['pagado'] != 0) ? '%'.number_format(( $row['estados']['pagado'] / $row['buro']['aprobado']) * 100, 2) : '-'?></td>
					</tr>
				<?php endforeach;?>
				</tbody>
	        </table>
		</div>
    </div>
</div>

<a></a>
<script type="text/javascript">
	$(document).ready(($) =>
	{
	 	/*var modulo_reportes = $("#reportes_dashboard_principal");

		$("#btn_excel").on('click', function(){
					event.preventDefault();
				
					let canal= 'SOLVENTA';
					let tipo_solicitud= 'TODOS';
					let rango_fecha= '30-03-2020 | 28-04-2020';
					let tipo_informe= 'RESUMIDO';

					ajax_indicadores_excel(canal, tipo_solicitud, rango_fecha, tipo_informe);
				})
*/
/*		var ajax_indicadores_excel = (canal, tipo_solicitud, rango_fecha, tipo_informe) =>
				{
					$.ajax({
						url: $("#base_url").val()+'api/reportes/solicitudes/indicadores',
						type: 'POST',
						//dataType: 'json',
						data: {'canal':canal, 'tipo_solicitud':tipo_solicitud, 'rango_fecha':rango_fecha, 'tipo_informe':tipo_informe, 'formato': 'excel'},
					})
					.done(function(data) {
						var a = $("<a>");
						a.attr("href",data.file);
						$("body").append(a);
						a.attr("download","file.xls");
						a[0].click();
						a.remove();
					})
					.fail(function() {
						
					})
				}*/
	});
</script>