<form id="formSearchValidador">
	<div class="form-group row">
		<label for="search" class="col-sm-12 control-label "> </label>
		<div class="col-sm-5 col-sm-offset-2">
			<input id="search" name="search" type="text" value="<?=$origin['search']?>" class="form-control" placeholder="ORDER ID / REFERENCIA">
		</div>
		<div class="col-sm-2">
			<select class="form-control" id="searchType" data-dashlane-rid="07ec93fe4609c24f" data-form-type="other">
				<option value="referencia" <?=($origin['type'] == 'referencia') ? 'selected' : '' ?>>Referencia</option>
				<option value="orderId"  <?=($origin['type'] == 'orderId') ? 'selected' : '' ?>>Order Id</option>
			</select>
		</div>
		<button type="submit" class="btn btn-info col-sm-1" title="Buscar" style="font-size: 12px;" data-dashlane-rid="979a0114a17a39fb" data-dashlane-label="true" data-form-type="action,search"><i class="fa fa-search"></i> Buscar</button>
	</div>
</form>
<hr style="border-top: 1px solid #bdc3c7">
<script>
	$(document).ready(function(){
		$('#formSearchValidador').submit(function(e){
			e.preventDefault();
			if ($('#searchType').val() == 'referencia') {
				window.location.href = base_url + 'payvalida/validador/show/'+$('#search').val();
			} else {
				window.location.href = base_url + 'payvalida/validador/showByOrderId/'+$('#search').val();
			}
		});
	});
</script>
