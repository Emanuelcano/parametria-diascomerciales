<style type="text/css">
	td.details-control 
	{
	    background: url(<?php echo base_url('assets/images/details_open.png'); ?>) no-repeat center center;
	    cursor: pointer;
	}
	td.closeit
	{
    	background: url(<?php echo base_url('assets/images/details_close.png'); ?>) no-repeat center center;
	}
	.cash
	{
    	background: url(<?php echo base_url('assets/images/money.png'); ?>) no-repeat center center;
	}
	.credit-card
	{
    	background: url(<?php echo base_url('assets/images/credit_card.png'); ?>) no-repeat center center;
	}
</style>
<div id="box_list_credits" class="box box-info">
    <div class="box-header with-border" id="titulo">
    	<input id="client" type="hidden" 
    			data-name= "<?php echo $solicitude['nombres']. " " . $solicitude['apellidos']; ?>" 
    			data-email = "<?php echo $solicitude['email']; ?>" 
    			data-mobilephone = "<?php echo $solicitude['telefono']; ?>" 
    			data-number_doc = "<?php echo $solicitude['documento']; ?>"

    	>
    	<input id="epayco" type="hidden" 
    			data-test= "<?php echo TEST_E_PAYCO;?>" 
    			data-confirmation = "<?php echo URL_CONFIRMATION;?>" 
    			data-response = "" 
    	>
	</div>
    <div class="box-body">
    <?php if(isset($credits)): ?>
		<table id="table_credits" class="table table-striped table=hover display">
			<thead>
				<tr>
					<th></th>
					<th>Id credito</th>
					<th>Otorgamiento</th>
					<th>Prestado</th>
					<th>Plazo</th>
					<th>Devuelve</th>
					<th>Fecha final</th>
					<th>Estado</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($credits as $key => $credit): ?>
				<tr style="background-color: #b7bdc5">
					<td class="details-control"></td>
					<td><?php echo isset($credit['id'])?$credit['id']:''; ?></td>
					<td><?php echo isset($credit['fecha_otorgamiento'])?$credit['fecha_otorgamiento']:''; ?></td>
					<td><?php echo isset($credit['monto_prestado'])?'$ '.number_format($credit['monto_prestado'],0,',','.'):'$ '.'0'; ?></td>
					<td><?php echo isset($credit['plazo'])?$credit['plazo']:''; ?></td>
					<td><?php echo isset($credit['monto_devolver'])?'$ '.number_format($credit['monto_devolver'],0,',','.'):'$ '.'0'; ?></td>
					<td><?php echo isset($credit['fecha_finalizacion'])?$credit['fecha_finalizacion']:''; ?></td>
					<td><?php echo isset($credit['estado'])?strtoupper($credit['estado']):''; ?></td>
				</tr>
				<tr style="display: none">
					<td colspan="8">
						<table class="table table-striped table=hover display"  border="0" style="padding-left:50px; text-align: center;">
							<thead style="text-align: center;">
								<tr>
									<th style="text-align: center;">Cuota</th>
									<th style="text-align: center;">Vencimiento</th>
									<th style="text-align: center;">Valor cuota</th>
									<th style="text-align: center;">Días atraso</th>
									<th style="text-align: center;">Mora</th>
									<th style="text-align: center;">Descuento</th>
									<th style="text-align: center;">A cobrar</th>
									<th style="text-align: center;">Fecha Cob</th>
									<th style="text-align: center;">Monto Cob</th>
									<th style="text-align: center;">Estado</th>
									<th style="text-align: center;">Voucher</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($credit['quotas'] as $key => $quota): ?>
								<tr>
									<td><?php echo isset($quota['numero_cuota'])?$quota['numero_cuota']:'';?></td>
									<td><?php echo isset($quota['fecha_vencimiento'])?$quota['fecha_vencimiento']:'';?></td>
									<td><?php echo isset($quota['monto_cuota'])?'$ '.number_format($quota['monto_cuota'],0,',','.'):'$ '.'0';?></td>
									<td><?php echo $quota['dias_atraso']>0?$quota['dias_atraso']:'';?></td>
									<td><?php echo $quota['interes_mora']>0?'$ '.number_format($quota['interes_mora'],0,',','.'):'';?></td>
									<td><?php echo $quota['descuento']>0?'$ '.number_format($quota['descuento'],0,',','.'):'';?></td>
									<td><?php echo isset($quota['monto_cobrar'])?'$ '.number_format($quota['monto_cobrar'],0,',','.'):'$ '.'0';?></td>
									<td><?php echo isset($quota['fecha_cobro'])?$quota['fecha_cobro']:'';?></td>
									<td><?php echo $quota['monto_cobrado']>0?'$ '.number_format($quota['monto_cobrado'],0,',','.'):'';?></td>
									<td><?php echo isset($quota['estado'])?$quota['estado']:'';?></td>
									<td>
										<div class="row">											
                                                                                    <a href="#" data-action="cash" class="pay_it" data-credit="<?php echo $credit['id']; ?>" data-quota="<?php echo $quota['numero_cuota']; ?>" 
                                                                                    data-amount="<?php echo $quota['monto_cobrar']; ?>" data-id_quota="<?php echo $quota['id']; ?>" data-key="<?php echo TOKEN_E_PAYCO_CASH; ?>" data-test="<?php echo TEST_E_PAYCO;?>" ><img src="<?php echo base_url('assets/images/money.png')?>" style="width:35%;"></a>
											
                                                                                    <!-- <div class="col-md-6">
                                                                                            <a href="#" data-action="transfer" class="pay_it" data-credit="<?php echo $credit['id']; ?>" data-quota="<?php echo $quota['numero_cuota']; ?>"  data-amount="<?php echo $quota['monto_cobrar']; ?>" data-id_quota="<?php echo $quota['id']; ?>" data-key="<?php echo TOKEN_E_PAYCO_TRANSFER; ?>"><img src="<?php echo base_url('assets/images/credit_card.png')?>" style="width:30%;"></a>
                                                                                    </div> -->
										</div>
									</td>
								</tr>
								<?php endforeach;?>
							</tbody>
						</table>
					</td>
				</tr> 
			<?php endforeach;?>
			</tbody>
		</table>
	<?php endif; ?> 
    </div>
</div>
<script type="text/javascript" src="https://checkout.epayco.co/checkout.js"></script>
<script type="text/javascript">
	$('document').ready(function(){

    // Add event listener for opening and closing details
	$("#box_list_credits #table_credits tbody").on('click','td.details-control', function (){
		var table_credits = $("#box_list_credits #table_credits");
        var tr = $(this).closest('tr').next('tr');
        if (tr.hasClass('shown')) {
            tr.hide();
            tr.removeClass('shown');
            $(this).removeClass('closeit');
        }
        else {
            tr.show();
            tr.addClass('shown');
            tr.addClass('shown');
            $(this).addClass('closeit');
        }
  	});

	$(".pay_it").on("click", function(event)
	{
		event.preventDefault();
		let test = $(this).data("test");
		let key = $(this).data("key");
		let client = $("#box_list_credits #client");
		let epayco = $("#box_list_credits #epayco");

		var handler = ePayco.checkout.configure({
			key: key,
			test: epayco.data("test")
		});
		var data={
			//Parametros compra (obligatorio)
			name: "Cuota"+ $(this).data("numero_cuota") +" - TEST FORZADO ADMIN",
			description: "Cuota "+ $(this).data("quota")+" - "+"Crédito " + $(this).data("credit"),
			invoice: $(this).data("id_quota")+"-"+ new Date().getTime(), //id del credito, UNICO NO DEBE REPETIRSE. UNA VEZ QUE ESTÁ PAGADO. 123-HMI
			currency: "cop",
			amount: Math.ceil($(this).data("amount")),
			tax_base: "0",
			tax: "0",
			country: "co",
			lang: "es",

			//Onpage="false" - Standard="true"
			external: "false",

			//Atributos opcionales
			extra1: "extra1",
			extra2: "extra2",
			extra3: "extra3",
			confirmation: epayco.data("confirmation"),
			response: epayco.data("response"),

			//Atributos cliente
			name_billing: client.data("name"),
			type_doc_billing: "cc",
			mobilephone_billing: client.data("mobilephone"),
			number_doc_billing: client.data("number_doc"),
			email_billing: client.data("email"),
			}
			handler.open(data);
	});

  });
</script>