<?php
//var_dump($logs);
?>
<div class="row">
	<div class="col-md-12">
		<div class="box box-warning box-solid">
			<div class="box-header with-border">
				<h3 class="box-title">Logs</h3>
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
					</button>
				</div>
			</div>
				<?php if ($logs !== false) { ?>
					<div class="box-body ">
						<div class="col-md-12">
							<div class="box box-warning">
								<div class="box-header with-border">
									<h3 class="box-title">Referencias encontradas en Logs</h3>
								</div>
								<div class="box-body">
									<div class="row">
										<?php foreach ($logs as $log) { ?>
											<h2>Archivo: <?=$log->file?></h2>
											<pre><?php foreach ($log->lines as $line) { ?><?=$line . PHP_EOL?><?php }?></pre>
										<?php }?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer text-right">
						<button class="btn btn-warning">Reenviar</button>
					</div>
				<?php } else {?>
					<div class="box-body ">
						<div class="col-md-12 text-center">
							<h3 style="color:#7f8c8d">NO HAY DATOS</h3>
						</div>
					</div>
				<?php } ?>
		</div>
	</div>
</div>



