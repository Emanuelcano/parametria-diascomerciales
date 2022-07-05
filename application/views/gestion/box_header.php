<style type="text/css">
	
	#box_gestion_header th {
		text-align: center;
	}
	#box_gestion_header td:first-child {
		width: 6%;
	}

	#box_gestion_header .container-fluid {
		padding-left: 0px;
		padding-right: 0px;
	}

	#box_gestion_header td {
		vertical-align: middle;
	}

	#box_gestion_header table {
		margin-bottom: 0px;
	}

	#box_gestion_header td#td_agendar select,
	#box_gestion_header td#td_agendar input[type="time"],
	#box_gestion_header td#td_agendar input[type="date"] {
		padding: 0px;
		margin: 0px 1px;
		font-size: 11px;
	}
	#box_gestion_header td.hd_center {
		text-align: center;
	}

	#box_gestion_header td.hd_monto {
		text-align: right;
	}

	#box_gestion_header td.aprv {
		background-color: lightgreen;
	}

	#box_gestion_header td.aprv i {
		color: #f2f2f2;
	}
	#box_gestion_header td.rechazado {
		background-color: #ff9595;
	}
	#box_gestion_header td.rechazado i {
		width: 20px;
		height: 20px;
		font-size: 15px;
		line-height: 20px;
		position: relative;
		color: #f2f2f2;
		background: #ff0000;
		border-radius: 50%;
		text-align: center;
		left: 0px;
		top: 0;
		text-align: center;
	}
	#box_gestion_header input[type="date" i]::-webkit-calendar-picker-indicator {
		margin-inline-start: 0px
	}
	#box_gestion_header input[type="time" i]::-webkit-calendar-picker-indicator {
		margin-left: 0px;
	}

</style>
<div id="box_gestion_header" class="row">
	<div class="col-md-9" style="margin-top: 10px; padding-right: 3px;">
		<div id="box_client_data" class="box box-info">
			<div class="box-header with-border gs_laboral" id="titulo">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-12" style="text-align: center;">
							<strong>SOLICITANTE</strong>
						</div>
					</div>
				</div>
			</div>
			<div class="box-body" style=" font-size: 12px;">
				<div class="container-fluid grid-striped">
					<table class="table table-bordered table-responsive display">
						<thead>
							<tr class="table-light">
								<th>NUMERO</th>
								<th>ALTA</th>
								<th>SOLICITANTE</th>
								<th>TIPO</th>
								<th>SERVICIO</th>
								<th>CONSULTADO</th>
								<th>RESULTADO</th>
								<th><i class="fa fa-volume-control-phone fa-lg"></i></th>
								<!-- <th><i class="fa fa-whatsapp fa-lg"></i></th> -->
								<th><i class="fa fa-inbox fa-lg"></i></th>
								<th><i class="fa fa-bank fa-lg"></i></th>
								<th>PASO</th>
								<th>ESTADO</th>
							</tr>
						</thead>
						<tbody>
							<tr style="height: 38px">
								<td class='hd_center'><?=$solicitude[0]['id'];?></td>
								<td class='hd_center'>
									<?php
										if (isset($solicitude[0]['fecha_alta'])) {
											$date = new DateTime($solicitude[0]['fecha_alta']);
											echo $date->format('d-m-Y H:i');
										} else 
											echo '';
									?>
								<td><?=$solicitude[0]['nombres'].' '.$solicitude[0]['apellidos'];?></td>
								<td><?=isset($solicitude[0]['tipo_solicitud'])?$solicitude[0]['tipo_solicitud']:''; ?>
								</td>
								<td><?=!empty($analisis['buro'])?$analisis['buro']:'';?></td>
								<td class='hd_center'>
									<?php
										if (!empty($analisis['fecha_consulta'])) {
											$date = new DateTime($analisis['fecha_consulta']);
											echo $date->format('d-m-Y H:i');
										} else 
											echo '';
									?>
								</td>
								<?php
									if (isset($solicitude[0]['respuesta_analisis'])){
										if ($solicitude[0]['respuesta_analisis'] == 'APROBADO')
											$class_resultado = 'aprv ';
										if ($solicitude[0]['respuesta_analisis'] == 'RECHAZADO')
											$class_resultado = 'rechazado ';
									} else 
										$class_resultado = ' ' ;
								?>
								<td class='<?=$class_resultado?> hd_center'>
									<?=$solicitude[0]['respuesta_analisis'];?>
								</td>

								<?php
									if (isset($solicitude[0]['validacion_telefono'])) {
										if ($solicitude[0]['validacion_telefono'] == 1) 
											echo '<td class="aprv hd_center" style="padding: 0px;"><span style="border-radius: 50%;padding: 4px; background-color: #00a65a"><i class="fa fa-check fa-lg"></i></span></td>';
										else 
											echo '<td class="rechazado hd_center" style="padding: 4px;"><i class="fa fa-times" aria-hidden="true"></i></td>';
									} else
										echo '<td></td>';
								?>
								<!-- <td class='hd_center'>&#129300;</td> -->
								<?php
									if (isset($solicitude[0]['validacion_mail'])) {
										if ($solicitude[0]['validacion_mail'] == 1) 
										echo '<td class="aprv hd_center" style="padding: 0px;"><span style="border-radius: 50%;padding: 4px; background-color: #00a65a"><i class="fa fa-check fa-lg"></i></span></td>';
										else 
											echo '<td class="rechazado hd_center" style="padding: 4px;"><i class="fa fa-times"></i></td>';
									}  else
										echo '<td></td>';
								?>
								<?php
									if(isset($bank['respuesta']) && !empty($bank['respuesta'])) {
										if ($bank['respuesta'] == 'ACEPTADA') 
										echo '<td class="aprv hd_center" style="padding: 0px;"><span style="border-radius: 50%;padding: 4px; background-color: #00a65a"><i class="fa fa-check fa-lg"></i></span></td>';
										else 
											echo '<td class="rechazado hd_center" style="padding: 4px;"><i class="fa fa-times"></i></td>';
									} else
										echo '<td></td>';
								?>
								<td class='hd_center'>
									<span id='paso-solicitud' style="font-weight: bold;font-size: 10px;"><?=isset($solicitude[0]['paso'])?$solicitude[0]['paso'] . '' :''; ?></span>
                    				<a href="#" onClick="$('#detallePaso').modal();"><i class="fa fa-eye fa-lg"></i></a>
								</td>
								<td class='hd_center '>
									<?php if(!empty($solicitude[0]['estado'])): ?>
										<?php if($solicitude[0]['estado'] == 'APROBADO'){ ?>
											<i style="font-size: 11px; color: green" class="fa fa-check">&nbsp;<label style="font-family: arial;"><?php echo $solicitude[0]['estado'];?></label></i>
										<?php }else if($solicitude[0]['estado'] == 'RECHAZADO' || $solicitude[0]['estado'] == 'ANULADO'){ ?>
											<i style="font-size: 11px; color: red" class="fa fa-times-circle">&nbsp;<label style="font-family: arial;"><?php echo $solicitude[0]['estado'];?></label></i>
										<?php }else if($solicitude[0]['estado'] == 'ANALISIS'){ ?>
											<i style="font-size: 11px; color: teal" class="fa fa-cogs">&nbsp;<label style="font-family: arial;"><?php echo $solicitude[0]['estado'];?></label></i>
										<?php }else if($solicitude[0]['estado'] == 'VERIFICADO'){ ?>
											<i style="font-size: 11px; color: orange" class="fa fa-eye">&nbsp;<label style="font-family: arial;"><?php echo $solicitude[0]['estado'];?></label></i>
										<?php }else if($solicitude[0]['estado'] == 'VALIDADO'){ ?>
											<i style="font-size: 11px; color: brown" class="fa fa-check-square-o">&nbsp;<label style="font-family: arial;"><?php echo $solicitude[0]['estado'];?></label></i>
										<?php }else if($solicitude[0]['estado'] == 'TRANSFIRIENDO'){ ?>
											<i style="font-size: 11px; color: purple" class="fa fa-bank">&nbsp;<label style="font-family: arial;"><?php echo $solicitude[0]['estado'];?></label></i>
										<?php }else if($solicitude[0]['estado'] == 'PAGADO'){ ?>
											<i style="font-size: 11px; color: blue" class="fa fa-money">&nbsp;<label  style="font-family: arial;"><?php echo $solicitude[0]['estado'];?></label></i>
										<?php } ?>
									<?php endif ?> 
								</td>
							</tr>
							<tr><td colspan="13" style="padding: 4px 0px; border-left-style: hidden; border-right-style: hidden;"></td></tr>
							<tr>
								<td style="font-weight: bold;">ALERTA</td>
								<td colspan="12" style="font-size: 12px;font-weight: bold;">
								<?php
									
									$val = false;
									if(!empty($analisis) && $analisis["regla"] == 1000 || ($solicitude[0]['operador_asignado'] == 108 && $solicitude[0]['tipo_solicitud'] == 'PRIMARIA')):
										echo "<span class='text-blue'>PRIMARIO AUTOMATICO<span class='text-red'> - REQUIERE VERIFICACION DE IDENTIDAD </span></span>";
										$val = true;
									endif;
									if(!isset($analisis['antiguedad_telefono']) ||  !isset($analisis['nit_aportante']) || $analisis['antiguedad_telefono'] == 0 || $solicitude[0]['id_situacion_laboral'] == 3 || is_null($analisis['nit_aportante']) || $analisis['nit_aportante'] ==''):
										if ($val) echo " || ";
										echo "<span class='text-red'>REQUIERE VIDEO EN LA VERIFICACION DE IDENTIDAD</span>";
										$val = true;
									endif;
									if((count($pagare_revolvente) > 0)):
										if ($val) echo " || ";
										echo "<span class='text-blue'>CLIENTE CON PAGARE REVOLVENTE</span>";
										$val = true;
									endif;
									if(isset($solicitud_referencias)):
										$S_ref = $solicitud_referencias;
										$estados = [
											'read' 			=> ['Leido', 'green'],
											'queued' 		=> ['No Recibido','red'], 
											'delivered' 	=> ['Recibido','yellow'], 
											'sent' 			=> ['No Recibido', 'red'], 
											'undelivered' 	=> ['No Recibido', 'red'], 
											'failed' 		=> ['No Recibido', 'red']
										];
										if ($val) echo " || ";
										$ref_whatsapp = 	"<span class='text-red'> REFERENCIAS WHATSAPP: </span>";
										$ref_whatsapp .= 	"<span class='text-".$estados[$S_ref->sms_status][1]."'> Estado Mensaje: ".$estados[$S_ref->sms_status][0]."</span> ";
										$ref_whatsapp .= 	($S_ref->respuesta_chat_validacion != '')? ' - Respuesta: '. $S_ref->respuesta_chat_validacion : '';
										echo $ref_whatsapp;
									endif;
								?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3" style="margin-top: 10px;">
		<div id="box_client_data" class="box box-info" >
			<div class="box-header with-border gs_laboral" id="titulo">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-12" style="text-align: center;">
							<strong>HISTORIAL</strong>
							<a id="close_solicitude" href="#" title="Cerrar y continuar gestionando otra solicitud" class="pull-right" 
								style="position: absolute; right: 20px; z-index: 1000; top: -5px;">
									<i class="fa fa-close icon_close"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="box-body" style=" font-size: 12px;">
				<div class="container-fluid grid-striped">
					<table class="table table-bordered table-responsive display">
						<thead>
							<tr class="table-light">
								<th style="padding: 8px 0px;">CREDITOS</th>
								<th>ATRASOS</th>
								<th>SITUACION</th>
								<th>DISPONIBLE</th>
							</tr>
						</thead>
						<tbody>
							<tr style="height: 38px">
								<td class='hd_center' style="font-size : 10px">
									<?=isset($cantidad_creditos[0]['COUNT(id)'])?$cantidad_creditos[0]['COUNT(id)']:''; ?>
								</td>
								<td>
									<?php 
										$update_nivel = TRUE;
										if(isset($atrasos)):
											for ($i = 0; $i < count($atrasos); $i++) {
												if ($i != 0) echo "-";
												echo ($atrasos[$i]['dias_atraso']);

												if($atrasos[$i]['dias_atraso'] > 39) $update_nivel = FALSE;
											}
										endif;
									?>
								</td>
								<?php
									if(isset($solicitude[0]['id_credit']) && !empty($solicitude[0]['id_credit'])) {
										echo '<td class="estado-credito" style="color: '.$solicitude[0]['color_credit'].'" >[' . strtoupper($solicitude[0]['status_credit']) . ']</td>';
									} else
										echo '<td class="estado-credito"></td>';
								?>
								<td class='hd_monto'>
									<?php 
										if($this->session->userdata['tipo_operador'] == 1 || $this->session->userdata['tipo_operador'] == 9 || $this->session->userdata['tipo_operador'] == 2 || $this->session->userdata['tipo_operador'] == 18 || $this->session->userdata['tipo_operador'] == 5 ){
											echo '<a class="btn btn-xs btn-warning" id="update-niveles"  style ="float:left" data-id-cliente="'. $solicitude[0]['id_cliente'].'"> <i class="fa fa-refresh"></i></a>';
										}
									?>
									<?="$ ".number_format($proximo_monto, 0, ',', '.')?>
								</td>
							</tr>
							<tr><td colspan="4" style="padding: 4px 0px; border-left-style: hidden; border-right-style: hidden;"></td></tr>
							<tr>
								<td style="font-weight: bold; padding: 8px 0px;">AGENDAR</td> 
								<td style="padding: 0px;" id="td_agendar_del" colspan="3" class='<?=(count($agenda_operadores)>0)? '': 'hidden'?>'> 
									<button class="btn btn-block btn-danger btn-sm col-md-offset-2 col-md-7 " onClick="var_agendarcita.delSolicitud(this)" data-id_agenda="<?=isset($agenda_operadores[0]['id'])? $agenda_operadores[0]['id'] : '' ?>">Eliminar Agendado</button>
								</td>
								<td style="padding: 0px;" id="td_agendar" colspan="3" class='<?=(count($agenda_operadores)>0)? 'hidden': ''?>'> 
									<?php
										$date = date_create();
										$inp_hoy = date_format($date, 'Y-m-d');
									?>
									<input class="form-control input-sm col-md-4" type="date" min="<?=$inp_hoy?>" id="inp_fecha" autocomplete="off" style="width: 32%;">
									<input class="form-control input-sm col-md-2" type="time" id="inp_hora" autocomplete="off" style="width: 21%; ">										
									<select class="form-control input-sm col-md-4" name="inp_motivo" id="inp_motivo">
										<option value=''>...</option>
										<option value='LLAMAR'>LLAMAR</option>
										<option value='DOCUMENTO'>DOCUMENTO</option>
										<option value='VALIDAR'>VALIDAR</option>
									</select>
									
									<button type="button" class="btn btn-primary btn-sm" style="padding: 5px 7px;" onclick="var_agendarcita.agendar(<?=$solicitude[0]['id'];?>,<?=$this->session->userdata('idoperador');?>,'<?=$solicitude[0]['nombres'];?>','<?=$solicitude[0]['apellidos']?>')"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$("document").ready(function(){
	
		$("#update-niveles").on('click', function(event){
				let base_url = $("#base_url").val();

				$.ajax({
					type: "GET",
					url: base_url + 'api/solicitud/update_niveles/'+ $('#update-niveles').data('id-cliente'),
				})
				.done(function(response) {
					if (response.ok) {
						Swal.fire("", response.message, "success");
					}else {
						Swal.fire("", response.message, "error");
					}
				})
				.fail(function(xhr) {
					Swal.fire("", "El regisgtro no pudo ser actualizado", "error");
					
				});
			
		});    

                
    });
</script>