<table data-page-length='10' align="center" id="tp_Bancos" class="table table-striped" table= "hover display" width="100%">
    <thead>
        <tr style="text-align: center; height: 25px; ">
            <th colspan="3" style="padding: 0px; ">&nbsp;</th>
            <th colspan="4" style="padding: 0px; padding-left: 10px; text-align: center; background: #BB8FCE;">Unidades</th>
            <th colspan="6" style="padding: 0px; padding-left: 10px; text-align: center; background: #7FB3D5;">Pesos $</th>
            <th colspan="2" style="padding: 0px;">&nbsp;</th>
        </tr>
        <tr class="info" style="text-align: center; height: 25px;">
            <th style="width: 12%; padding: 0px; padding-left: 10px; text-align: center;">Banco</th>
            <th style="width: 8%; padding: 0px; padding-left: 10px; text-align: center;">Cuenta</th>
            <th style="width: 3%; padding: 0px; padding-left: 10px; text-align: center;">Tipo</th>
            <th style="width: 5%; padding: 0px; padding-left: 10px; text-align: center; background: #BB8FCE;">Tope Mes</th>
            <th style="width: 5%; padding: 0px; padding-left: 10px; text-align: center; background: #E8DAEF;">Acumuladas</th>
            <th style="width: 5%; padding: 0px; padding-left: 10px; text-align: center; background: #BB8FCE;">Quedan</th>
            <th style="width: 5%; padding: 0px; padding-left: 10px; text-align: center; background: #E8DAEF;">Hoy</th>
            <th style="width: 8%; padding: 0px; padding-left: 10px; text-align: center; background: #A2D9CE;">Apertura Dia</th>
            <th style="width: 8%; padding: 0px; padding-left: 10px; text-align: center; background: #D5F5E3;">Pagos del d&iacute;a</th>
            <th style="width: 8%; padding: 0px; padding-left: 10px; text-align: center; background: #A2D9CE;">Saldo</th>
            <th style="width: 8%; padding: 0px; padding-left: 10px; text-align: center; background: #A9CCE3;">Tope Mes</th>
            <th style="width: 8%; padding: 0px; padding-left: 10px; text-align: center; background: #7FB3D5;">Acumulado</th>
            <th style="width: 8%; padding: 0px; padding-left: 10px; text-align: center; background: #A9CCE3;">Quedan</th>
            <th style="width: 5%; padding: 0px; padding-left: 10px; text-align: center;">Estado</th>
            <th style="width: 10%; padding: 0px;">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($cuentasBancarias as $cuentaBancaria):?>
            <tr>
                <td style="padding: 0px; font-size: 12px; vertical-align: middle;"><?= $cuentaBancaria->Nombre_Banco?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle;"><?= $cuentaBancaria->numero_cuenta?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;"><?= $cuentaBancaria->codigo_TipoCuenta?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;"><?= $cuentaBancaria->maximo_unidades_mes?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;"><?= $cuentaBancaria->unidad_acumulada_mes?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;"><?= $cuentaBancaria->quedan?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;"><?= $cuentaBancaria->quedandia?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;"><?= number_format($cuentaBancaria->saldo_apertura,2,",",".")?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;"><?= number_format($cuentaBancaria->pesosacumuladodia,2,",",".")?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;"><?= number_format($cuentaBancaria->quedanpesosdia,2,",",".")?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;"><?= number_format($cuentaBancaria->maximo_pesos_mes,2,",",".")?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;"><?= number_format($cuentaBancaria->pesos_acumulado_mes,2,",",".")?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: right;"><?= number_format($cuentaBancaria->quedanpesos,2,",",".")?></td>

                <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;"><?= $cuentaBancaria->nombre_estado?></td>

                <td style="height: 5px; padding: 4px;" align="center">
                    <a href="#" class="btn btn-xs bg-navy" title="Ver Datos de la cuenta">
                        <i class="fa fa-eye" ></i>
                    </a>
                    <a href="#" class="btn btn-xs bg-yellow" title="Cambiar Estado">
                        <i class="fa fa-exchange" ></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>