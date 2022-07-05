<form name="frm_campania" id="frm_campania" class="form-group" role="form" method="POST">
	<input type="hidden" name="txt_hd_id_camp" id="txt_hd_id_camp" value="<?=(isset($campaignId)? $campaignId :'')?>">
	<div class="box box-info">
		<div class="box-header with-border">
			<h3 class="box-title">Info Campaña</h3>
		</div>
		<div class="box-body">
			<div class="col-md-4">
				<div class="form-group">
					<label for="campaignTitle">Titulo</label>
					<input type="text" name="campaignTitle" class="form-control input-sm" id="campaignTitle" placeholder="Titulo">
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="campaignStatus">Estado</label>
					<select name="campaignStatus" class="form-control input-sm" id="campaignStatus">
						<option value="1">Habilitado</option>
						<option value="0">Deshabilitado</option>
					</select>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="campaignColor">Color</label>
					<select name="campaignColor" class="form-control input-sm" id="campaignColor">
						<option value="0">Seleccionar</option>
						<option style="color:#0071c5;" value="#0071c5">&#9724; Azul oscuro</option>
						<option style="color:#40E0D0;" value="#40E0D0">&#9724; Turquesa</option>
						<option style="color:#008000;" value="#008000">&#9724; Verde</option>
						<option style="color:#FFD700;" value="#FFD700">&#9724; Amarillo</option>
						<option style="color:#FF8C00;" value="#FF8C00" selected="selected">&#9724; Naranja</option>
						<option style="color:#FF0000;" value="#FF0000">&#9724; Rojo</option>
						<option style="color:#000;" value="#000">&#9724; Negro</option>
					</select>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="campaignProvider">Proveedores</label>
					<select name="campaignProvider" class="form-control input-sm" id="campaignProvider">
						<?php  foreach ($proveedores as $value) {?>
							<option value="<?php echo $value['id_proveedor'];?>" selected="selected"><?php echo $value['nombre_proveedor'];?></option>
						<?php }  ?>
					</select>
				</div>
			</div>
			<div class="col-md-4">
				<div style="width: 100%;display: inline-block" id="containerCampaingService">
					<div class="form-group">
						<label for="campaignService">Servicio</label>
						<select name="campaignService" class="form-control input-sm" id="campaignService" required="required">
							<option value="SMS" selected="selected">SMS</option>
							<option value="MAIL">EMAIL</option>
							<option value="WSP">WHATSAP</option>
							<option value="IVR">IVR</option>
						</select>
					</div>
				</div>
				<div style="width: 45%;margin-left: 4%; display: none" id="containerCampaignChannel">
					<div class="form-group">
						<label for="campaignService">Canal</label>
						<select name="canal" class="form-control input-sm" id="canal" required="required">
							<option value="">.: Seleccione :.</option>
							<option value="15185188">Cobranzas</option>
							<option value="15140334">Ventas</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="campaignMode">Modalidad</label>
					<select name="campaignMode" class="form-control input-sm" id="campaignMode">
						<option value="ALEATORIO">Aleatorio</option>
						<option value="PREDETERMINADO">Predeterminado</option>
					</select>
				</div>
			</div>
		</div>
		<div class="box-footer text-right">
			<button type="button" class="btn btn-success" id="btn_save_campaing">Guardar</button>
		</div>
		<div class="overlay" id="loadingDetails" style="display: none">
			<i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
</form>
