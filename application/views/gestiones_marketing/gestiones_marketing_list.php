<?php if(count($providers) > 0):?>
        <table data-page-length='25' id="table_solicitudes_proveedores" class="table table-bordered table-hover dataTable">
            <thead>
                <tr class="" style="background-color: #59a3d8;">
                
                    <th class="text-center">Solicitud</th>
                    <th class="text-center">Fecha alta</th>
                    <th class="text-center">Documento</th>
                    <th class="text-center">Solicitante</th>
                    <th class="text-center">Paso</th>
                    <th class="text-center">Situacion laboral</th>
                    <th class="text-center">proveedor</th>
                    <th class="text-center">Estado</th>
                    <?php
                    if (isset($providers[0]["fecha_desembolso"])) {
                        echo "<th class='text-center'>Fecha desembolso</th>";
                    }
                    ?>
                    <th class="text-center">Email</th>
                    <th class="text-center">Telefono</th>
                    <th class="text-center">Track ID</th>
                </tr>
            </thead>
			<tbody style="font-size: 12px; text-align: center;">
				<?php $i = 0; ?>
				<?php foreach ($providers as $t):?>
 						<tr>
                             
                            <td class="text-left"><?php echo $t['id'] ?></td>
                            <td class="text-left"><?php echo $t['fecha_alta'] ?></td>
							<td class="text-left"><?php echo $t['documento'] ?></td>
                            <td class="text-left"><?php echo $t['nombre_completo'] ?></td>
                            <td class="text-left"><?php echo $t['paso'] ?></td>
                            <td class="text-left"><?php echo $t['situacion_laboral'] ?></td>
                            <td class="text-left"><?php echo $t['utm_source'] ?></td>
                            <td class="text-left"><?php echo $t['estado'] ?></td>
                            <?php
                                if (isset($providers[0]["fecha_desembolso"])) {
                                    echo "<td class='text-left'>".$t['fecha_desembolso']."</td>";
                                }
                            ?>
                            <td class="text-left"><?php echo $t['email'] ?></td>
                            <td class="text-left"><?php echo $t['telefono'] ?></td>
                            <td class="text-left"><?php echo $t['tracking_id'] ?></td>
						</tr>
				<?php endforeach; ?>
			</tbody>
        </table>

       
    <script>
        $(document).ready(function() {
            $('#table_solicitudes_proveedores').DataTable();
            $('.btnFormEmailTemplate').prop('disabled', true);
        });
    </script>
    <?php endif; ?>
    <?php if(count($providers) <= 0): ?>
        <div class="row">
            <div class="col-md-12 text-center">
                <h1> No hay información para esta consulta</h1>
            </div>
        </div>
        
    <?php endif; ?>