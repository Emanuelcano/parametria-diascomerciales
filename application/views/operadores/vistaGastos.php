<span class="hidden-xs">
	<?php
	if (empty($this->session->userdata("sesion"))) {
		redirect(base_url() . "auth/logout");
	}
	$usuario     = $this->session->userdata("username");
	$tipoUsuario = $this->session->userdata("tipo");
	?>
</span>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario;?>">

<div id="dashboard_principal" style="display: block; background: #FFFFFF;">

    <div class="row">
    	<!-- Main content -->
      	<section class="content">

            <div class="col-lg-12" id="cuerpoGastos" style="display: block">

              <table data-page-length='10' align="center" id="tp_Indicadores" class="table table-striped table=hover display" width="100%">
                <thead>
                  <tr class="info">
                  	<th style="width: 8%; padding: 0px; padding-left: 10px;">Solicitado</th>
                    <th style="width: 8%; padding: 0px; padding-left: 10px;">Nro Factura</th>
                    <th style="width: 50%; padding: 0px; padding-left: 10px;">Beneficiario</th>
                    <th style="width: 8%; padding: 0px; padding-left: 10px;">Vencimiento</th>
                    <th style="width: 10%; padding: 0px; padding-left: 10px;">Monto Pagar</th>
                    <th style="width: 8%; padding: 0px; padding-left: 10px;">Estado</th>
                    <th style="width: 8%; padding: 0px;">&nbsp;</th>
                  </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">25-11-2019</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">3021111222222</td>

                        <td style="padding: 0px; font-size: 12px; vertical-align: middle;">DENOMINACION DEL BENEFICIARIO</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">28-11-2019</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">145.550,00</td>

                        <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;">PENDIENTE</td>

                        <td style="height: 5px; padding: 4px;" align="center">
                          	<a class="btn btn-xs bg-navy" title="Ver Datos del gasto"
                            	onclick="cargarbeneficiario();">
                            	<i class="fa fa-eye" ></i>
                          	</a>
                          	<a class="btn btn-xs btn-success" title="Actualizar Datos del gasto"
                              		onclick="cargarbeneficiario();">
                              <i class="fa fa-pencil-square-o" ></i>
                          	</a>
                          	<a class="btn btn-xs btn-danger" title="Anular autorización del gasto"
                              		onclick="cargarbeneficiario();">
                              <i class="fa fa-ban" ></i>
                          	</a>
                        </td>
                    </tr>

                    <tr>
                    	<td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">25-11-2019</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">8767676</td>

                        <td style="padding: 0px; font-size: 12px; vertical-align: middle;">DENOMINACION DEL BENEFICIARIO</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">29-11-2019</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">88.900,00</td>

                        <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;">RECHAZADO</td>

                        <td style="height: 5px; padding: 4px;" align="center">
                          	<a class="btn btn-xs bg-navy" title="Ver Datos del gasto"
                            	onclick="cargarbeneficiario();">
                            	<i class="fa fa-eye" ></i>
                          	</a>
                          	<a class="btn btn-xs btn-success" title="Actualizar Datos del gasto" disabled='true'
                              		onclick="cargarbeneficiario();">
                              <i class="fa fa-pencil-square-o" ></i>
                          	</a>
                          	<a class="btn btn-xs btn-danger" title="Anular autorización del gasto" disabled='true'
                              		onclick="cargarbeneficiario();">
                              <i class="fa fa-ban" ></i>
                          	</a>
                        </td>
                    </tr>

                    <tr>
                    	<td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">25-11-2019</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">564445</td>

                        <td style="padding: 0px; font-size: 12px; vertical-align: middle;">DENOMINACION DEL BENEFICIARIO</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">27-11-2019</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">650.000,00</td>

                        <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;">APROBADO</td>

                        <td style="height: 5px; padding: 4px;" align="center">
                          	<a class="btn btn-xs bg-navy" title="Ver Datos del gasto"
                            	onclick="cargarbeneficiario();">
                            	<i class="fa fa-eye" ></i>
                          	</a>
                          	<a class="btn btn-xs btn-success" title="Actualizar Datos del gasto" disabled='true'
                              		onclick="cargarbeneficiario();">
                              <i class="fa fa-pencil-square-o" ></i>
                          	</a>
                          	<a class="btn btn-xs btn-danger" title="Anular autorización del gasto" disabled='true'
                              		onclick="cargarbeneficiario();">
                              <i class="fa fa-ban" ></i>
                          	</a>
                        </td>
                    </tr>

                    <tr>
                    	<td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">25-11-2019</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">423242</td>

                        <td style="padding: 0px; font-size: 12px; vertical-align: middle;">DENOMINACION DEL BENEFICIARIO</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">29-11-2019</td>

                        <td style="padding: 0px; font-size: 12px; text-align: center; vertical-align: middle;">230.000,00</td>

                        <td style="padding: 0px; font-size: 12px; vertical-align: middle; text-align: center;">PAGADO</td>

                        <td style="height: 5px; padding: 4px;" align="center">
                          	<a class="btn btn-xs bg-navy" title="Ver Datos del gasto"
                            	onclick="cargarbeneficiario();">
                            	<i class="fa fa-eye" ></i>
                          	</a>
                          	<a class="btn btn-xs btn-success" title="Actualizar Datos del gasto" disabled='true'
                              		onclick="cargarbeneficiario();">
                              <i class="fa fa-pencil-square-o" ></i>
                          	</a>
                          	<a class="btn btn-xs btn-danger" title="Anular autorización del gasto" disabled='true'
                              		onclick="cargarbeneficiario();">
                              <i class="fa fa-ban" ></i>
                          	</a>
                        </td>
                    </tr>

                </tbody>
              </table>
            </div>

      	</section>
    	<!-- /.content -->
    </div>

</div>

