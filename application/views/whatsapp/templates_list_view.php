    <?php if(count($templates) > 0): ?>
        <table id="table_templates" class="table table-bordered table-hover dataTable">
            <thead>
                <tr class="" style="background-color: #59a3d8;">
                    <th></th>
                    <th class="text-center">Id</th>
                    <th class="text-center">Contenido</th>
                    <th class="text-center">Canal</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Operador</th>
                    <th class="text-center">Fecha de Creación</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
			<tbody style="font-size: 12px; text-align: center;">
				<?php $i = 0; ?>
				<?php foreach ($templates as $t):
                    $date = new DateTime($t['creation_date']); ?>
 						<tr>
                            <td class="text-left"> <input type="checkbox" name="template_checkbox[]" class="form-check-input template_checkbox" value="<?php echo $t['id'] ?>" data-canal="<?php echo $t['canal'] ?>" data-proveedor="<?php echo $t['proveedor'] ?>" data-template-message="<?php echo $t['msg_string'] ?>" data-status="<?php echo $t['estado'] ?>"></td>
							<td class="text-left"><?php echo $t['id'] ?></td>
							<td class="text-left"><?php echo $t['msg_string'] ?></td>
							<td class="text-left" nowrap><?php echo ($t['canal'] == '1,2' ? 'Ventas | Cobranzas' : ($t['canal'] == 1 ? 'ventas' : 'cobranzas')) ?></td>
                            <td class="text-left"><?php echo ($t['estado'] == 1 ? 'activo' : 'inactivo') ?></td>
							<td class="text-left" nowrap><?php echo $t['nombre_apellido']  ?></td>
							<td class="text-left"><?php echo $date->format('d-m-Y');  ?></td>
							<td class="text-left">
                                <a class="btn btn-xs btn-primary btnFormTemplate" data-template="<?php echo $t['id'] ?>" title="Ver Template"><i class="fa fa-gears"></i></a> 
                                <a class="btn btn-xs btn-warning btnChangeStatus" data-status="<?php echo $t['estado']?>" data-template="<?php echo $t['id'] ?>" title="Cambiar estado"><i class="fa fa-exchange" aria-hidden="true"></i></a> 
                            </td>
						</tr>
				
				<?php endforeach; ?>
			</tbody>
        </table>
        <?php endif;
            if(count($templates_email) > 0): ?>
        <table id="table_templates" class="table table-bordered table-hover dataTable">
            <thead>
                <tr class="" style="background-color: #59a3d8;">
                    <th></th>
                    <th class="text-center">Id</th>
                    <th class="text-center">Asunto</th>
                    <th class="text-center">Nombre Template</th>
                    <th class="text-center">Canal</th>
                    <th class="text-center">Operador</th>
                    <th class="text-center">Fecha de Creación</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
			<tbody style="font-size: 12px; text-align: center;">
				<?php $i = 0; ?>
				<?php foreach ($templates_email as $t):
                    $date = new DateTime($t['creation_date']); ?>
 						<tr>
                            <td class="text-left"> <input type="checkbox" name="template_checkbox[]" class="form-check-input template_checkbox" value="<?php echo $t['id'] ?>" data-canal="<?php echo $t['canal'] ?> " data-id_logica="<?php echo $t['id_logica'] ?>"></td>
							<td class="text-left"><?php echo $t['id'] ?></td>
							<td class="text-left"><?php echo $t['asunto'] ?></td>
                            <td class="text-left"><?php echo $t['nombre'] ?></td>
							<td class="text-left" nowrap><?php echo ($t['canal'] == '1,2' ? 'Ventas | Cobranzas' : ($t['canal'] == 1 ? 'ventas' : 'cobranzas')) ?></td>
							<td class="text-left" nowrap><?php echo $t['nombre_apellido']  ?></td>
							<td class="text-left"><?php echo $date->format('d-m-Y');  ?></td>
							<td class="text-left">
                                <a class="btn btn-xs btn-primary btnFormEmailTemplate" disabled="disabled" data-template="<?php echo $t['id'] ?>" title="Ver Template">
                                    <i class="fa fa-gears"></i>
                                </a> 
                                <a type="button" class="btn btn-xs btn-success btnpopover" data-template_id="<?php echo $t['id'] ?>" data-html="true" data-container="body" data-toggle="popover" data-placement="left" data-content="">
                                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                </a>
                            </td>
						</tr>
                        
				<?php endforeach; ?>
			</tbody>
        </table>
    <?php endif; ?>
    <script>
        $(document).ready(function() {
            $('#table_templates').DataTable();
            $('.btnFormEmailTemplate').prop('disabled', true);
        });
    </script>