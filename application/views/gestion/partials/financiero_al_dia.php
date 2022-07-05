<div id="box_finaciero_al_dia" class="box box-info">
	<div class="box-header with-border gs_laboral" id="titulo">
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-12" style="text-align: center;">
					<strong>SECTOR FINANCIERO AL DIA</strong>
				</div>
			</div>
		</div>
	</div>
	<div class="box-body" style="font-size: 12px;">
		<?php if (empty($data)) { ?>
			<table class="table table-bordered table-responsive display table_info_endeudamiento">
				<tr><td style="text-align: center">Sin información</td></tr>
			</table>
		<?php } else { ?>
			<table class="table table-bordered table-responsive display table_info_endeudamiento">
				<thead>
				<tr>
					<th rowspan="2" colspan="2">FECHA CORTE</th>
					<th rowspan="2">MODA</th>
					<th rowspan="2">No OBLIG</th>
					<th rowspan="2">TIPO ENT</th>
					<th rowspan="2">NOMBRE ENTIDAD</th>
					<th rowspan="2">CIUDAD</th>
					<th rowspan="2">CAL</th>
					<th rowspan="2">MRC</th>
					<th rowspan="2">TIPO GAR</th>
					<th rowspan="2">FINICIO</th>
					<th colspan="3">No. CUOTAS</th>
					<th rowspan="2">CUPO APROB VLR INIC</th>
					<th rowspan="3">PAGO MINIMO VLR CUOTA</th>
					<th rowspan="2">SIT OBLIG</th>
					<th rowspan="2">NATU REES</th>
					<th rowspan="2">No. REE</th>
					<th rowspan="2">TIP PAG</th>
					<th rowspan="2">F PAGO F EXTIN</th>
				</tr>
				<tr>
					<th>PAC</th>
					<th>PAG</th>
					<th>MOR</th>
				</tr>
				<tr>
					<th>TIPO CONT</th>
					<th>PADE</th>
					<th>LCRE</th>
					<th>EST. CONTR</th>
					<th>CLF</th>
					<th>ORIGEN CARTERA</th>
					<th>SUCURSAL</th>
					<th>EST TITU</th>
					<th>CLS</th>
					<th>COB GAR</th>
					<th>F TERM</th>
					<th>PER</th>
					<th></th>
					<th></th>
					<th>CUPO UTILI SALDO CORT</th>
					<th>VALOR MORA</th>
					<th>RESS</th>
					<th>MOR MAX</th>
					<th>MOD EXT</th>
					<th>F PERM</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($data as $item) { ?>
					<tr>
						<td colspan="2"><?=($item['FechaCorte'])??'-'?></td> <!-- 1 -->
						<td><?=($item['ModalidadCredito'])??'-'?></td> <!-- 4 -->
						<td><?=($item['NumeroObligacion'])??'-'?></td> <!-- 6 -->
						<td><?=($item['TipoEntidad'])??'-'?></td> <!-- 8 -->
						<td><?=($item['NombreEntidad'])??'-'?></td> <!-- 10 -->
						<td><?=($item['Ciudad'])??'-'?></td> <!-- 12 -->
						<td><?=($item['Calidad'])??'-'?></td> <!-- 14 -->
						<td> - </td> <!-- 16 -->
						<td><?=($item['TipoGarantia'])??'-'?></td> <!-- 18 -->
						<td><?=($item['FechaApertura'])??'-'?></td> <!-- 20 -->
						<td><?=($item['NumeroCuotasPactadas'])??'-'?></td> <!-- 22 -->
						<td><?=($item['CuotasCanceladas'])??'-'?></td> <!-- 24 -->
						<td><?=($item['NumeroCuotasMora'])??'-'?></td> <!-- 25 -->
						<td><?=($item['ValorInicial'])??'-'?></td> <!-- 26 -->
						<td><?=($item['ValorCuota'])??'-'?></td> <!-- 28 -->
						<td><?=($item['EstadoObligacion'])??'-'?></td> <!-- 29 -->
						<td><?=($item['NaturalezaReestructuracion'])??'-'?></td> <!-- 31 -->
						<td><?=($item['NumeroReestructuraciones'])??'-'?></td> <!-- 33 -->
						<td><?=($item['TipoPago'])??'-'?></td> <!-- 35 -->
						<td><?=($item['FechaPago'])??'-'?></td> <!-- 37 -->
					</tr>
					<tr>
						<td><?=($item['TipoContrato'])??'-'?></td> <!-- 2 -->
						<td><?=($item['ParticipacionDeuda'])??'-'?></td> <!-- 3 -->
						<td><?=($item['LineaCredito'])??'-'?></td> <!-- 5 -->
						<td><?=($item['EstadoObligacion'])??'-'?></td> <!-- 7 -->
						<td><?=($item['Calificacion'])??'-'?></td> <!-- 9 -->
						<td> - </td> <!-- 11 -->
						<td><?=($item['Sucursal'])??'-'?></td> <!-- 13 -->
						<td><?=($item['EstadoTitular'])??'-'?></td> <!-- 15 -->
						<td> - </td> <!-- 17 -->
						<td><?=($item['CubrimientoGarantia'])??'-'?></td> <!-- 19 -->
						<td><?=($item['FechaTerminacion'])??'-'?></td> <!-- 21 -->
						<td><?=($item['Periodicidad'])??'-'?></td> <!-- 23 -->
						<td colspan="2">&nbsp;</td>
						<td><?=($item['SaldoObligacion'])??'-'?></td> <!-- 27 -->
						<td>&nbsp;</td>
						<td><?=($item['ValorMora'])??'-'?></td> <!-- 30 -->
						<td><?=($item['Reestructurado'])??'-'?></td> <!-- 32 -->
						<td><?=($item['MoraMaxima'])??'-'?></td> <!-- 34 -->
						<td><?=($item['ModoExtincion'])??'-'?></td> <!-- 36 -->
						<td><?=($item['FechaPermanencia'])??'-'?></td> <!-- 38 -->
					</tr>
					<tr>
						<td colspan="4" class="cellComportamientos">COMPORTAMIENTOS</td>
						<td colspan="17"><?=($item['Comportamientos'])??'-'?></td> <!-- 39 -->
					</tr>
					<tr>
						<td colspan="21" style="background-color: white"></td>
					</tr>
				<?php } ?>
				
				</tbody>
			</table>
		<?php } ?>
		
	</div>
</div>
