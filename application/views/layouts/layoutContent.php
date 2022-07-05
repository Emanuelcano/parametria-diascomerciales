<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
	<div class="col-md-12" align="center" id="divLiquidar" style="display: block; height: 100%;margin-top: 45px">
		<?php if (!empty($buttons)) { ?>
			<div class="box-header with-border" class="col-lg-12">
				<?=$buttons ?>
			</div>
		<?php } ?>
	</div>
</div>

<div id="dashboard_principal">
	<section class="content">
		<div class="col-lg-12" id="main" style="display: block;margin-top: 15px">
			<?=$layoutContent?>
		</div>
	</section>
</div>
