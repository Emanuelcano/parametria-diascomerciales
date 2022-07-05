<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<?php $this->load->view('supervisores/menu/menu_supervisores'); ?>
<div id="section_search_solicitud" style="background: #FFFFFF;">
	<?php if (!empty($prelanzamiento)) { ?>
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="box box-primary">
					<div class="box-header with-border"><h3 class="box-title">Campanias Listas para lanzarse</h3></div>
					<table class="table table-condensed" id="table-events">
						<thead>
						<tr>
							<th>ID</th>
							<th>Campania</th>
							<th>Tipo</th>
							<th id="action-column">Acciones</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($prelanzamiento as $item): ?>
							<tr>
								<td><?php echo $item['id_logica']; ?></td>
								<td><?php echo $item['nombre_logica']; ?></td>
								<td><?php echo $item['type_logic']; ?></td>
								<td style="width: 150px">
									<a href="<?php echo base_url('cronograma_campanias/Cronogramas/previewLanzamiento/'.$item['id_logica'].'/'.$item['id_template'].'/'.$item['id_event'].'/'.$item['prelanzamiento_id']); ?>" class="btn btn-primary btn-xs">Envio preliminar</a>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<div class="overlay" id="loadingListCronograma" style="display: none"><i
								class="fa fa-refresh fa-spin"></i></div>
				</div>
			</div>
		</div>
	<?php } ?>
	<H3><strong>Agendar Campañas</strong></H3>
	<div class="row">
		<div class="col-md-12 text-right">
			<button  type="button" id="btn_new_campaing" class="btn btn-success" title="Nueva Campañia" style="font-size: 12px;"><i class="fa fa-plus"></i> Nueva Campañia</button>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="" id="result">
			<table align="center" id="table_campania" class="table table-responsive table-striped table=hover display" width="100%" >
				<thead style="font-size: smaller; ">
				<tr class="info">
					<th style="text-align: center;">ID</th>
					<th style="text-align: center;">Campañia</th>
					<th style="text-align: center;">Proveedor</th>
					<th style="text-align: center;">Tipo</th>
					<th style="text-align: center;">Estado</th>
					<th style="text-align: center;">Accion</th>
				</tr>
				</thead>
				<tbody style="font-size: 12px; text-align: center;" id="tb_body">
				<?php foreach ($campanias as $k => $campania) { ?>
					<tr>
						<td><?=$campania['id_logica']?></td>
						<td><?=$campania['nombre_logica']?></td>
						<td><?=$campania['proveedor']?></td>
						<td><?=$campania['type_logic']?></td>
						<td><?=($campania['estado']==1) ? "Activo" : "Inactivo"; ?></td>
						<td>
							<a href="<?php echo base_url();?>cronograma_campanias/Cronogramas/edit/<?=$campania['id_logica']?>" id='btn_edit_campaing' class='btn btn-xs btn-primary ajustar-credito' ><i class='fa fa-pencil'></i></a>
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo asset('assets/cronograma_campanias/index.js'); ?>"></script>

<!--<script type="text/javascript" src="--><?php //echo base_url('assets/supervisores/configuracion_centrales.js'); ?><!--"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/supervisores/gestionar_campanias.js'); ?><!--"></script>-->
<script type="text/javascript" src="<?php echo base_url('assets/caret/jquery.caret.min.js'); ?>"></script>
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/clock-timepicker/jquery-clock-timepicker.min.js'); ?><!--"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/ddslick/jquery.ddslick.min.js'); ?><!--"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url('assets/taginput/bootstrap-tagsinput.js'); ?><!--"></script>-->
