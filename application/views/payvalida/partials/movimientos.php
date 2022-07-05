<?php 
//var_dump($movimientos);
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-info box-solid">
			<div class="box-header with-border">
				<h3 class="box-title">Info Movimiento</h3>
			</div>
			<div class="box-body">
				<?php if ($movimientos !== false) { ?>
					<div class="col-md-12">
						<div class="box box-info">
							<div class="box-header with-border">
								<h3 class="box-title">Info Database</h3>
							</div>
							<div class="box-body">
								<div class="row listaItems">
									<?php
										$anchoTitulo=5;
										$anchoValue=12-$anchoTitulo
									?>

									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Status:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<?php
										switch ($movimientos['status']) {
											case null:
												$classLavel = 'label-warning';
												$text = 'PENDIENTE';
												break;
											case 'cancelled':
												$classLavel = 'label-danger';
												$text = 'VENCIDA';
												break;
											case 'approved':
												$classLavel = 'label-success';
												$text = 'APROBADA';
												break;
										} ?>
										<span class="label <?=$classLavel?>"><?=$text?></span>
									</div>
									
									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Id:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span><?=$movimientos['id']?></span>
									</div>
									
									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Referencia:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span><?=$movimientos['referencia']?></span>
									</div>
									
									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>pv_order_id:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span><?=$movimientos['pv_order_id']?></span>
									</div>
									
									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Order id:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span><?=$movimientos['order_id']?></span>
									</div>

									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Tipo:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span><?=$movimientos['tipo']?></span>
									</div>
									
									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Cliente:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<div class="row">
											<div class="col-md-6"><strong>Id</strong></div>
											<div class="col-md-6"><?=$movimientos['id_cliente']?></div>
											<div class="col-md-6"><strong>Nombre</strong></div>
											<div class="col-md-6"><?=$cliente['nombres']?></div>
											<div class="col-md-6"><strong>Doc</strong></div>
											<div class="col-md-6"><?=$cliente['documento']?></div>
										</div>
									</div>
									
									<div  class="col-md-12" style="border-top: 1px solid #ecf0f1;top:10px">&nbsp;</div>
									
									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Monto:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span><?=$movimientos['monto']?></span>
									</div>

									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Metodo de pago:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span><?=$movimientos['metodo_de_pago']?></span>
									</div>
									
									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Expiracion:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span>
											<?=date('d/m/Y', strtotime($movimientos['expiracion'])); ?> -   
											( <?=diferenciaFecha($movimientos['expiracion']); ?> )
										</span>
									</div>

									<div  class="col-md-12" style="border-top: 1px solid #ecf0f1;top:10px">&nbsp;</div>
									
									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Error:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span><?=$movimientos['error']?></span>
									</div>

									<div  class="col-md-12" style="border-top: 1px solid #ecf0f1;top:10px">&nbsp;</div>

									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Creacion:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span>
											<?=date('d/m/Y H:i:s', strtotime($movimientos['created_at'])); ?> - 
											( <?=diferenciaFecha($movimientos['created_at']); ?> )
										</span>
									</div>

									<div class="col-md-<?=$anchoTitulo?> text-left">
										<strong>Actualizado:</strong>
									</div>
									<div class="col-md-<?=$anchoValue?>">
										<span>
											<?=date('d/m/Y H:i:s', strtotime($movimientos['updated_at'])); ?> - 
											( <?=diferenciaFecha($movimientos['updated_at']); ?> )
										</span>
									</div>
									
<!--									--><?php //foreach ($movimientos as $titulo => $movimiento) { ?>
<!--										--><?php //if (!in_array($titulo,['checkout','payload','status']) )  { ?>
<!--											<div class="col-md---><?//=$anchoTitulo?><!-- text-left">-->
<!--												<strong>--><?//=$titulo?><!--:</strong>-->
<!--											</div>-->
<!--											<div class="col-md---><?//=$anchoValue?><!--">-->
<!--												<span>--><?//=(!empty($movimiento))?$movimiento:"&nbsp;"?><!--</span>-->
<!--											</div>-->
<!--										--><?php //} ?>
<!--									--><?php //} ?>
									
									
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="box box-info collapsed-box">
							<div class="box-header with-border">
								<h3 class="box-title">Payload</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
									</button>
								</div>
							</div>
							<div class="box-body">
								<pre><?=json_encode(json_decode($movimientos['payload']), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>
							</div>
						</div>
					</div>
				<?php } else {?>
					<div class="col-md-12 text-center">
						<h3 style="color:#7f8c8d">NO HAY DATOS</h3>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php
function diferenciaFecha($fecha) {
	$date = \Carbon\Carbon::parse($fecha)->locale('es_ES');
	$now = \Carbon\Carbon::now();

	$diff = $date->diffForHumans($now);
	if (strpos( $diff, 'después') !== false ) {
		$diff = 'Faltan ' . str_replace('después', '', $diff);
	}
	if (strpos( $diff, 'antes') !== false ) {
		$diff = 'Hace ' . str_replace('antes', '', $diff);
	}
	return $diff;
}
?>


