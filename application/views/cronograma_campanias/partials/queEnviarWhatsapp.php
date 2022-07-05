<style>
	#previewTemplateWhatsapp {
		white-space: pre-wrap;       /* Since CSS 2.1 */
	}
</style>
<div class="row">
	<div class="col-md-6" >
		<div class="box box-success" id="box-seleccion-template">
			<div class="box-header with-border">
				<h3 class="box-title">Seleccion Templates</h3>
			</div>
			<div class="box-body">
				<div class="col-md-12" >
					<select name="templateWhatsapp" id="templateWhatsapp" style="width: 100%">
						<option value="">Seleccione una opción</option>
						<?php foreach ($templates as $template): ?>
							<option value="<?php echo $template['id']; ?>" ><?=$template['msg_string']; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-12 text-center">
							<h3>Preview del Template Seleccionado</h3>
						</div>
						<div class="col-md-12">
							<?php if (!empty($campaign['whatsapp_template_id'])) { ?>
								<?php foreach ($templates as $template): ?>
									<?php if ($template['id'] === $campaign['whatsapp_template_id']) { ?>
										<pre id="previewTemplateWhatsapp"><?php echo $template['msg_string']; ?></pre>
									<?php } ?>
								<?php endforeach; ?>
							<?php } else { ?>
								<pre id="previewTemplateWhatsapp"></pre>
							<?php } ?>
						</div>
						<div class="col-md-12 text-right">
							<button class="btn btn-success" id="addWhatsappTemplate">Agregar</button>
						</div>
					</div>
				</div>
			</div>
			<div class="overlay" id="loadingEnvioWhatsapp" style="display: none">
				<i class="fa fa-refresh fa-spin"></i>
			</div>
		</div>
	</div>
	<div class="col-md-6" style="border-left: 1px solid #f4f4f4">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Templates Agregados</h3>
			</div>
			<div class="box-body">
				<table class="table table-striped" >
					<thead>
						<tr>
							<th style="width: 100px;">Template Id</th>
							<th>Texto Template</th>
							<th style="width: 90px">Acciones</th>
						</tr>
					</thead>
					<tbody id="tabla-templates-whatsapp">
					<tr>
						<td>1.</td>
						<td>Update software</td>
						<td>
							dasdasdas
						</td>
					</tr>
					
				</tbody></table>
			</div>
			<div class="overlay" id="loadingTemplateAgregados" style="display: none">
				<i class="fa fa-refresh fa-spin"></i>
			</div>
		</div>
	</div>
</div>


