<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-12 text-center" style="background-color: #dfdfdf">
				<h4><?= $day ?></h4>
			</div>
			<div class="col-md-10">
				<div class="row">
					<div class="col-md-12 text-center" >
						<input class="time <?= ($weekend) ? 'weekend' : 'weekday' ?>" id="hour_<?=$day?>" type="text" value="12:00"/>
					</div>
					<div class="col-md-12" >
						<select id="select-mensaje-<?=$day?>" class="select-mensaje"></select>
					</div>
				</div>
			</div>
			<div class="col-md-2 text-center" style="padding:0px; height: 91px;">
				<button id="btn_add_time_<?=$day?>" onclick="save_mensaje_programado('<?=$day?>')" class="btn btn-xs btn-success" style="margin-top:34px"><i class="fa fa-check"></i></button>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table class="stripe" id="tabla_mensajes_programados_<?=$day?>">
				</table>
			</div>
		</div>
	</div>
</div>
