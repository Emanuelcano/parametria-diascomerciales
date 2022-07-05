<div class="panel panel-default">
	<div class="panel-heading">
		<H4><strong>ENVIAR AUTOMATICAMENTE AL ABRIR EL CASO </strong></H4>
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-9">
				<span>Whatsapp</span><br>
				<select style="width: 100%;" class="form-control" name="templateWhatsapp" id="templateWhatsapp">
					<option value="0">&nbsp;</option>
					<?php
					$lastGroup = "";
					foreach ($data['templatesWhatsapp'] as $templateWhatsapp) {
					if ($templateWhatsapp['grupo'] != $lastGroup) {
					$lastGroup = $templateWhatsapp['grupo'];
					?> <optgroup label="<?=$templateWhatsapp['grupo']?>"> <?php
						?> <option value="<?=$templateWhatsapp['id']?>"><?=$templateWhatsapp['msg_string']?></option> <?php
						} else {
							?> <option value="<?=$templateWhatsapp['id']?>"><?=$templateWhatsapp['msg_string']?></option> <?php
						}
						} ?>
				</select>
			</div>
			<div class="col-sm-3">
				<span>Canal</span><br>
				<select name="canal" id="canal" class="from-control">
					<option value="0">&nbsp;</option>
					<option value="15140334">ORIGINACION</option>
					<option value="15185188">COBRANZA</option>
				</select>
			</div>
		</div>
		<div class="row" style="margin-top: 18px;">	
			<div class="col-sm-12">
				<span>SMS</span><br>
				<select style="width: 100%;" class="form-control" name="templateSMS" id="templateSMS">
					<option value="0">&nbsp;</option>
					<?php
					$lastGroup = "";
					foreach ($data['templatesSMS'] as $templateSms) {
					if ($templateSms['grupo'] != $lastGroup) {
					$lastGroup = $templateSms['grupo'];
					?> <optgroup label="<?=$templateSms['grupo']?>"> <?php
						?> <option value="<?=$templateSms['id']?>"><?=$templateSms['msg_string']?></option> <?php
						} else {
							?> <option value="<?=$templateSms['id']?>"><?=$templateSms['msg_string']?></option> <?php
						}
						} ?>
				</select>
			</div>
		</div>
		<div class="row" style="margin-top: 18px;">
			<div class="col-sm-12">
				<span>Email</span><br>
				<select style="width: 100%;" class="form-control" name="templateEmail" id="templateEmail">
					<option value="0">&nbsp;</option>
					<?php foreach ($data['templatesEmail'] as $templateEmail) {
						?> <option value="<?=$templateEmail['id']?>"><?=$templateEmail['nombre_template']?></option> <?php
					} ?>
				</select>
			</div>
		</div>
	</div>
</div>
