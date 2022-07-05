<?php //echo "<pre>";print_r($data);echo "</pre>";?>

<div class="box box-info" id="tablaOperadores"  style="display: block;">
	<div class="box-header with-border" id="titulo">
		<h6 class="box-title"><small><strong>Operadores</strong></small></h6>
	</div>
	<main role="main">
		<section class="content">
			
			<div class="box-body" style="margin-top: -10px;">
				<div class="row">

					<div class="col-lg-12 text-right" style="display: block; padding-bottom:5px;">
						<a class="btn btn-success" title="Registrar operador" onClick="nuevoOperador()"><i class="fa fa-user-plus"></i> Nuevo Operador</a>
					</div>

					<div class="col-lg-12" id="cuerpoOperadors" style="display: block">

						<table data-page-length='10' align="center" id="tp_Operadores" class="table table-striped table=hover display" width="100%">
						<thead>
							<tr class="info">
							<th style="width: 10%; padding: 0px; padding-left: 10px;">Id</th>
							<th style="width: 50%; padding: 0px; padding-left: 10px;">Denominacion</th>
							<th style="width: 10%; padding: 0px; padding-left: 10px;">Tipo</th>
							<th style="width: 10%; padding: 0px; padding-left: 10px;">Estado</th>
							<th style="width: 20%; padding: 0px;">&nbsp;</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($data['lista_operadores'] as $item): ?>
								<tr>
									<td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
										<?php echo $item['idoperador'];?>
									</td>
									<td style="padding: 0px; font-size: 12px; vertical-align: middle;">
										<?php echo $item['nombre_apellido'];?>
									</td>
									<td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;">
										<?php echo $item['descripcion'];?>
									</td>
									<td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;">
										<?php 
										if($item['estado'] == '1'){echo "Activo"; }
										else { echo "Inactivo"; }
										?>
									</td>
									<td style="height: 5px; padding: 4px;" align="center">
									<a class="btn btn-xs bg-navy" id="ver" title="Ver Datos del Operador"
										onclick="cargarOperador(<?php echo $item['idoperador']; ?>, 'edit',<?php echo $item['id_usuario']; ?>);">
										<i class="fa fa-eye" ></i>
									</a>
									<a class="btn btn-xs btn-info" id="editar" title="Actualizar Datos del operador"
										onclick="cargarOperador(<?php echo $item['idoperador']; ?>, this, <?php echo $item['id_usuario']; ?>);">
										<i class="fa fa-pencil-square-o" ></i>
									</a>
									<a class="btn btn-xs bg-yellow" title="Cambiar Estado"
										onclick="cambiarEstado(<?php echo $item['idoperador']; ?>, <?php echo $item['estado'];?>);">
										<i class="fa fa-exchange" ></i>
									</a>
									</td>
								</tr>

							<?php endforeach ?>

						</tbody>
						</table>
					</div>
				</div>

			</div>
				
		</section>
	</main>

</div>
	
<script src="<?php echo base_url('assets/js/operadores/dualSelectList.jquery.js') ?>"></script>



