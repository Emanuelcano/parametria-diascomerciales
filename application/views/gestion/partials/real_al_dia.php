<div id="box_finaciero_al_dia" class="box box-info">
	<div class="box-header with-border gs_real_al_dia" id="titulo">
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-12" style="text-align: center; background-color: #efe4b0">
					<strong>SECTOR REAL AL DIA</strong>
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
						<th rowspan="3">FECHA CORTE</th>
						<th rowspan="2">TIPO CONT</th>
						<th rowspan="2">No OBLIG</th>
						<th rowspan="2">NOMBRE ENTIDAD</th>
						<th rowspan="2">CIUDAD</th>
						<th rowspan="2">CALD</th>
						<th rowspan="2">VIG</th>
						<th rowspan="3">CLA PER</th>
						<th rowspan="2">F INICIO</th>
						<th colspan="3">No. CUOTRAS</th>
						<th rowspan="2">CUPO APROV VLR INIC</th>
						<th rowspan="2">PAGO MINIMO VLR CUOTA</th>
						<th rowspan="2">SIT OBLIG</th>
						<th rowspan="2">TIP PAG</th>
						<th rowspan="2">REF</th>
						<th rowspan="2">F PAGO F EXTIN</th>
					</tr>
					<tr>
						<th>PAC</th>
						<th>PAG</th>
						<th>MOR</th>
					</tr>
					<tr>
						<th>CATE LCRE</th>
						<th>EST. CONTR</th>
						<th>TIPO EMPR</th>
						<th>SUCURSAL</th>
						<th>EST TITU</th>
						<th>MES</th>
						<th>F TERM</th>
						<th>PER</th>
						<th colspan="2">&nbsp;</th>
						<th>CUPO UTILI SALDO CORT</th>
						<th>VALOR CARGO FIJO</th>
						<th>VALOR MORA</th>
						<th>MODO EXT</th>
						<th>MOR MAX</th>
						<th>F PERM</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($data as $item) { ?>
						<tr>
							<td><?=($item['FechaCorte'])??'-'?></td> <!-- 1 -->
							<td><?=($item['TipoContrato'])??'-'?></td> <!-- 2 -->
							<td><?=($item['NumeroObligacion'])??'-'?></td> <!-- 4 -->
							<td><?=($item['NombreEntidad'])??'-'?></td> <!-- 6 -->
							<td><?=($item['Ciudad'])??'-'?></td> <!-- 8 -->
							<td><?=($item['Calidad'])??'-'?></td> <!-- 10 -->
							<td><?=($item['Vigencia'])??'-'?></td> <!-- 12 -->
							<td><?=($item['ClausulaPermanencia'])??'-'?></td> <!-- 14 -->
							<td><?=($item['FechaApertura'])??'-'?></td> <!-- 15 -->
							<td><?=($item['NumeroCuotasPactadas'])??'-'?></td> <!-- 17 -->
							<td><?=($item['CuotasCanceladas'])??'-'?></td> <!-- 19 -->
							<td><?=($item['NumeroCuotasMora'])??'-'?></td> <!-- 20 -->
							<td><?=($item['ValorInicial'])??'-'?></td> <!-- 21 -->
							<td><?=($item['ValorCuota'])??'-'?></td> <!-- 23 -->
							<td><?=($item['EstadoObligacion'])??'-'?></td> <!-- 25 -->
							<td><?=($item['TipoPago'])??'-'?></td> <!-- 27 -->
							<td><?=($item['ModoExtincion'])??'-'?></td> <!-- 29 -->
							<td><?=($item['FechaPago'])??'-'?></td> <!-- 31 -->
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><?=($item['LineaCredito'])??'-'?></td> <!-- 3 -->
							<td><?=($item['EstadoObligacion'])??'-'?></td> <!-- 5 -->
							<td><?=($item['TipoEntidad'])??'-'?></td> <!-- 7 -->
							<td><?=($item['Sucursal'])??'-'?></td> <!-- 9 -->
							<td><?=($item['EstadoTitular'])??'-'?></td> <!-- 11 -->
							<td> - </td> <!-- 13 -->
							<td>&nbsp;</td>
							<td><?=($item['FechaTerminacion'])??'-'?></td> <!-- 16 -->
							<td><?=($item['Periodicidad'])??'-'?></td> <!-- 18 -->
							<td colspan="2">&nbsp;</td>
							<td><?=($item['SaldoObligacion'])??'-'?></td> <!-- 22 -->
							<td><?=($item['ValorCargoFijo'])??'-'?></td> <!-- 24 -->
							<td><?=($item['ValorMora'])??'-'?></td> <!-- 26 -->
							<td><?=($item['ModoExtincion'])??'-'?></td> <!-- 28 -->
							<td><?=($item['MoraMaxima'])??'-'?></td> <!-- 30 -->
							<td><?=($item['FechaPermanencia'])??'-'?></td> <!-- 32 -->
						</tr>
						<tr>
							<td colspan="3" class="cellComportamientos">COMPORTAMIENTOS</td>
							<td colspan="15"><?=($item['Comportamientos'])??'-'?></td> <!-- 32 -->
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
