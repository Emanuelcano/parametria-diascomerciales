<div id="tabla_solicitudes" style="display: block;margin-left: 1%; margin-right: 1%;">
    <table data-page-length='10' align="center" id="tp_atencionCliente" class="table table-striped table=hover display" width="100%">
        <thead>
          <tr class="info">
            <th style="width: 6%; text-align: center;">Acción</th>
            <th style="width: 5%; text-align: center;">No Solicitud</th>
            <th style="width: 6%; text-align: center;">Fecha</th>
            <th style="width: 4%; text-align: center;">Hora</th>
            <th style="width: 6%; text-align: center;">Documento</th>
            <th style="width: 13%; text-align: center;">Solicitante</th>
            <th style="width: 8%; text-align: center;">Tipo</th>
            <th style="width: 8%; text-align: center;">Buro</th>
            <th style="width: 8%; text-align: center;">Estado</th>
            <th style="width: 8%; text-align: center;">Cuenta</th>
            <!-- <th style="width: 8%; text-align: center;">Reto</th> -->
            <th style="width: 25%; text-align: center;">Ultima Gestion</th>
          </tr>
        </thead>
        <tbody style="font-size: 12px; text-align: center;">
            <?php $i = 0; ?>
            <?php foreach ($solicitudes as $solicitante): ?>
                <?php if($solicitante['documento'] != '' && isset($solicitante['documento'])): ?>
                    <tr>
                        <td style="height: 5px; padding: 4px;" align="center">
                            <a href="#" data-id_solicitud ="<?php echo $solicitante['id'] ?>" class="btn btn-xs btn-primary solicitud" title="Consultar">
                                <i class="fa fa-cogs" ></i>
                            </a>
                        </td>
                        <td><?php echo $solicitante['id'] ?></td>
                        <td data-sort="<?php echo $solicitante['unixtime'];?>"><?php echo date("d-m-Y", strtotime(substr($solicitante['fecha_ultima_actividad'], 0, 10))) ?></td>
                        <td><?php echo substr($solicitante['fecha_ultima_actividad'], 10) ?></td>
                        <td><?php echo $solicitante['documento'] ?></td>
                        <td style="font-size: 12px; text-align: left;"><?php echo $solicitante['nombres']." ".$solicitante['apellidos'] ?></td>
                        <td><?php echo $solicitante['tipo_solicitud'] ?></td>
                        <td><?php echo $solicitante['respuesta_analisis'] ?></td>
                        <td><?php echo $solicitante['estado'] ?></td>
                        <td><?php echo $solicitante['banco_resultado'] ?></td>
                        <td><?php echo $solicitante['resultado_ultimo_reto'] ?></td>
                        <td style="font-size: 12px; text-align: left;"><?php echo isset($solicitante['last_track']) ? $solicitante['last_track']: ''; ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $('document').ready(function(){

        $("#tabla_solicitudes a.solicitud").on('click', function(event){
           let id_solicitud = $(this).data("id_solicitud");
           consultar_solicitud(id_solicitud);
        })
        
        $('#tp_atencionCliente').DataTable({
            'ordering':true,
            'iDisplayLength': 10,
            "searching": true,
            "info": true,
           // "order":[[1,'desc']],
        });
    })
</script>