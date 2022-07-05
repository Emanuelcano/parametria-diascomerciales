<link rel="stylesheet" href="<?php echo base_url(); ?>assets/report/css/buttons.bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/report/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/report/css/bootstrap.min.css">


<?php
if(isset($datos)){
?>
<div class="row">
	<div class="col-md-12" style="overflow-x: scroll;">
<!--<table id="example" class="table table-striped table-bordered" style="font-size: 10px;">-->
<table data-page-length='10' align="center" id="example" class="table table-striped hover" width="100%" style="font-size: 10px;">

	<thead>
	<tr>
		<th>tnui</th>
		<th>numero_identificacion</th>
		<th>id</th>
		<th>nombres_apellidos</th>
		<th>estado_1</th>
		<th>fecha</th>
		<th>fecha_fin</th>
		<th>titular</th>
		<th>forma_pago</th>
		<th>novedad</th>
		<th>data1</th>
		<th>fecha2</th>
		<th>estado</th>
		<th>fecha_r</th>
		<th>valor_1</th>
		<th>valor_2</th>
		<th>valor_3</th>
		<th>valor_4</th>
		<th>monto_prestado</th>
		<th>valor_5</th>
		<th>valor_6</th>
		<th>valor_7</th>
		<th>fech_vence</th>
		<th>valor_8</th>
		<th>ciudad_1</th>
		<th>code_ciudad</th>
		<th>ciudad_2</th>
		<th>valor_9</th>
		<th>valor_10</th>
		<th>telefono</th>
	</tr>
	</thead>
	<tbody>
	<?php

	foreach ($datos as $reporte)
	{

		echo "
							  <tr>				
								<td>".$reporte['tnui']."</td>
								<td>".$reporte['numero_identificacion']."</td>
								<td>".$reporte['id']."</td>
								<td>".$reporte['nombres_apellidos']."</td>
								<td>".$reporte['estado_1']."</td>
								<td>".$reporte['fecha']."</td>
								<td>".$reporte['fecha_fin']."</td>
								<td>".$reporte['titular']."</td>
								<td>".$reporte['forma_pago']."</td>
								<td>".$reporte['novedad']."</td>
								<td>".$reporte['data1']."</td>
								<td>".$reporte['fecha2']."</td>
								<td>".$reporte['estado']."</td>
								<td>".$reporte['fecha_r']."</td>
								<td>".$reporte['valor_1']."</td>
								<td>".$reporte['valor_2']."</td>
								<td>".$reporte['valor_3']."</td>
								<td>".$reporte['valor_4']."</td>
								<td>".$reporte['monto_prestado']."</td>
								<td>".$reporte['valor_5']."</td>
								<td>".$reporte['valor_6']."</td>
								<td>".$reporte['valor_7']."</td>
								<td>".$reporte['fech_vence']."</td>
								<td>".$reporte['valor_8']."</td>
								<td>".$reporte['ciudad_1']."</td>
								<td>".$reporte['code_ciudad']."</td>
								<td>".$reporte['ciudad_2']."</td>
								<td>".$reporte['valor_9']."</td>
								<td>".$reporte['valor_10']."</td>
								<td>".$reporte['telefono']."</td>
							</tr>
						  ";
	}

	?>

	</tbody>
	<tfoot>
	<tr>
		<th>tnui</th>
		<th>numero_identificacion</th>
		<th>id</th>
		<th>nombres_apellidos</th>
		<th>estado_1</th>
		<th>fecha</th>
		<th>fecha_fin</th>
		<th>titular</th>
		<th>forma_pago</th>
		<th>novedad</th>
		<th>data1</th>
		<th>fecha2</th>
		<th>estado</th>
		<th>fecha_r</th>
		<th>valor_1</th>
		<th>valor_2</th>
		<th>valor_3</th>
		<th>valor_4</th>
		<th>monto_prestado</th>
		<th>valor_5</th>
		<th>valor_6</th>
		<th>valor_7</th>
		<th>fech_vence</th>
		<th>valor_8</th>
		<th>ciudad_1</th>
		<th>code_ciudad</th>
		<th>ciudad_2</th>
		<th>valor_9</th>
		<th>valor_10</th>
		<th>telefono</th>
	</tr>
	</tfoot>
</table>
</div>
	</div>
<?php
	}
?>


<script src="<?php echo base_url('assets/report/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/dataTables.buttons.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/buttons.bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/jszip.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/pdfmake.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/vfs_fonts.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/buttons.html5.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/buttons.print.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/report/js/buttons.colVis.min.js'); ?>"></script>

<script>
	$(document).ready(function() {
		var table = $('#example').DataTable( {
			lengthChange: false,
			buttons: [ 'copy', 'excel', 'pdf', 'csv', 'colvis' ]
		} );

		table.buttons().container()
			.appendTo( '#example_wrapper .col-sm-6:eq(0)' );
	} );
</script>
