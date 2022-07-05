<?php 
//var_dump($payvalida); 
?>

<div class="box box-success box-solid">
	<div class="box-header with-border">
		<h3 class="box-title">Info Payvalida</h3>
		
	</div>
	<div class="box-body">
		<?php if ($haypayvalida) { ?> 
			<div class="col-md-12">
				<div class="box box-success ">
					<?php 
						$anchoTitulo=5; 
						$anchoValue=12-$anchoTitulo
					?>
					<div class="box-header with-border">
						<h3 class="box-title">Datos Transaccion</h3>
					</div>
					<div class="box-body tableList">
						<div class="row">
							<div class="col-md-<?=$anchoTitulo?> text-left">
								<strong>ORDER ID:</strong>
							</div>
							<div class="col-md-<?=$anchoValue?>">
								<span id="modal_orden"><?=$payvalida['ORDER'] ?></span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-<?=$anchoTitulo?> text-left">
								<strong>REFERENCE</strong>
							</div>
							<div class="col-md-<?=$anchoValue?>">
								<span id="modal_orden"><?=$payvalida['REFERENCE'] ?></span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-<?=$anchoTitulo?> text-left">
								<strong>CODE <small>(pv_order_id)</small> </strong>
							</div>
							<div class="col-md-<?=$anchoValue?>">
								<span id="modal_orden"><?=$payvalida['CODE'] ?></span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-<?=$anchoTitulo?> text-left">
								<strong>STATE</strong>
							</div>
							<div class="col-md-<?=$anchoValue?>">
								<span id="modal_orden">
									<?php 
									switch ($payvalida['STATE']) {
										case 'PENDIENTE':
											$classLavel = 'label-warning';
											break;
										case 'VENCIDA':
											$classLavel = 'label-danger';
											break;
										case 'APROBADA':
											$classLavel = 'label-success';
											break;
									} ?>
									<span class="label <?=$classLavel?>"><?=$payvalida['STATE'] ?></span>
								</span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-<?=$anchoTitulo?> text-left">
								<strong>CURRENCY</strong>
							</div>
							<div class="col-md-<?=$anchoValue?>">
								<span id="modal_orden"><?=$payvalida['CURRENCY'] ?></span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-<?=$anchoTitulo?> text-left">
								<strong>AMOUNT</strong>
							</div>
							<div class="col-md-<?=$anchoValue?>">
								<span id="modal_orden"><?=$payvalida['AMOUNT'] ?></span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-<?=$anchoTitulo?> text-left">
								<strong>METHOD</strong>
							</div>
							<div class="col-md-<?=$anchoValue?>">
								<span id="modal_orden"><?=$payvalida['PAYMNENT_METHOD'] ?></span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-<?=$anchoTitulo?> text-left">
								<strong>CREATION_DATE</strong>
							</div>
							<div class="col-md-<?=$anchoValue?>">
								<span id="modal_orden"><?=$payvalida['CREATION_DATE'] ?></span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-<?=$anchoTitulo?> text-left">
								<strong>UDPATE_DATE</strong>
							</div>
							<div class="col-md-<?=$anchoValue?>">
								<span id="modal_orden"><?=$payvalida['UDPATE_DATE'] ?></span>
							</div>
						</div>
					</div>
				</div>	
			</div>
			<div class="col-md-12">
				<div class="box box-success">
					<div class="box-header with-border">
						<h3 class="box-title">Respuesta Solventa a Notificacion</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
							</button>
						</div>
					</div>
					<div class="box-body">
						<?php if ($payvalida['NOTIFICATION_RESPONSE'] != '') { ?>
							<pre><?=htmlspecialchars($payvalida['NOTIFICATION_RESPONSE'])?></pre>
						<?php } else { ?>
							<div class="col-md-12 text-center">
								<h4 style="color:#7f8c8d">Notificacion aun no enviada</h4>
							</div>
						<?php }?>
					</div>
				</div>
			</div>
		<?php } else {?>
				<div class="col-md-12 text-center">
					<h3 style="color:#7f8c8d">NO HAY DATOS</h3>
				</div>
		<?php } ?>
	</div>
	
	<div class="box-footer text-center">
		
	</div>
</div>
<script>
	$(document).ready(function() {
		// $('#tablePayvalida').DataTable();
	});
</script>
<style>
	.tableList .row {
		margin-bottom: 5px;
	}
</style>
