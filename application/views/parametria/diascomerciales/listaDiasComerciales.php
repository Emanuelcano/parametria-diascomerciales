
<table data-page-length='10' align="center" id="tp_listaDiasComerciales" class="table table-striped table=hover display" width="100%">
  <thead>
    <tr class="info">
    <th style="width: 10%; padding: 0px; padding-left:110px;">Id</th>
    <th style="width: 10%; padding: 0px; padding-left: 20px;">Dia</th>
    <th style="width: 10%; padding: 0px; padding-left: 100px;">Fecha</th>
    <th style="width: 20%; padding: 0px; padding-left: 200px;">Descripcion</th>
    <th style="width: 20%; padding: 0px;">&nbsp;</th>
    </tr>
  </thead>
  <tbody>

 <?php foreach( $data as $item) : ?>

    <tr>
      <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
        <?php echo $item['id'];?>
      </td>
    <!--  <td style="display:none"> <?php echo $item['fecha']; ?></td> -->
      <td>
        <?php
        $date= $item['dia_semana'];
        echo $date; 
        ?>
      </td>
      <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">
      <p style="visibility: hidden"> <?php echo $item['fecha']?>   </p>
     
        <?php $fechaCam = $item['fecha'];
            $timestamp = strtotime($fechaCam);
            $newFormato = date("d-m-Y", $timestamp );
            echo  $newFormato;?>
      </td>
      <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;">
        <?php echo $item['descripcion']; ?>
      </td>
     
      
      <td style="height: 5px; padding: 4px;" title="Ver dia" align="center">
        <a class="btn btn-xs bg-navy" id="ver"  onclick="cargarDiaComercial(<?php echo $item['id']; ?> , 'ver');" style="display: <?php $fecha_actual = strtotime(date("d-m-Y"));
          $fecha_entrada = strtotime($newFormato);
          if($fecha_actual > $fecha_entrada)
          {
          echo "none";
          }?>">
          <i class="fa fa-eye" ></i>
        </a>
        <a class="btn btn-xs btn-info" style="display: <?php $fecha_actual = strtotime(date("d-m-Y"));
          $fecha_entrada = strtotime($newFormato);
          if($fecha_actual > $fecha_entrada)
          {
          echo "none";
          }?>" id="editar" title="Actualizar Parentesco"
          onclick="cargarDiaComercial(<?php echo $item['id']; ?> , 'edit'); ">
          <i class="fa fa-pencil-square-o"  ></i>
        </a> 
      </td>
    </tr>

<?php endforeach ?>
</tbody>
</table>
<script src="<?php echo base_url('assets/function.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/operadores/dualSelectList.jquery.js') ?>"></script>

