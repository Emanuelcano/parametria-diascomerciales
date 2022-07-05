<link rel="stylesheet" 
	type="text/css" 
	href="<?php echo base_url('assets/icheck/icheck-bootstrap.css'); ?>"
/>
<style type="text/css">
    .loader {
		border: 16px solid #f3f3f3; /* Light grey */
		border-top: 16px solid #3498db; /* Blue */
		border-radius: 50%;
		width: 120px;
		height: 120px;
		animation: spin 2s linear infinite;
		display: none;
		align-items: center;
		justify-content: center;
		margin: 0 auto;
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
</style>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>">
<!-- Esto es para que el header no superponga el buscador -->
<?php //echo $desembolso; die();?>
<div class="box-header with-border" class="col-lg-12"><div class="col-md-12">&nbsp;</div></div>
<!--  -->

	<div class="box-header with-border" class="col-lg-12">
		<div class="col-md-12">
					<H3><strong>CRITERIOS PARA BUSQUEDA AUDITORIA ONLINE</strong></H3>
	
					<div id="section_search_credito" style="background: #FFFFFF; margin-top:10px;">
							<form id="form_search" class="form-horizontal row" method="POST">
								<div class="form-group row">
									<label for="search" class="col-sm-12 control-label "> </label>
									<div class="col-sm-6">
										
									<select class="select2 form-control-lg" style="width: 100%;" name="sl_operadores" id="sl_operadores">
						                <option value=0>.:OPERADOR:.</option>
						                
						                <?php  foreach ($operadores_data as $value) {?>

						                <option value="<?= $value['idoperador'] ?>"><?= $value['idoperador'] . ".-" .$value['nombre_apellido'] ?></option>
						                		
						                <?php } ?>
					                </select>
									</div>
									<button  type="submit" class="btn btn-info col-sm-1" title="Buscar" style="font-size: 12px;"><i class="fa fa-search"></i> BUSCAR</button>
									<button  type="reset" class="btn btn-default col-sm-1" title="Limpiar" style="font-size: 12px;"><i class="fa fa- fa-remove"></i> LIMPIAR</button>
									<a class="btn bg-purple"> <i class="fa fa-archive"></i> CERRAR JORNADA</a>
								</div>
								
							</form>
							<div id="result" style="">
								<table align="center" id="table_search_auditoria" class="table table-responsive table-striped table=hover" width="100%" >
									<thead style="font-size: smaller; ">
										<tr class="info">
											<th></th>
											<th style="text-align: center;">N°</th>
											<th style="text-align: center;">Fecha</th>
											<th style="text-align: center;">Hora</th>
											<th style="text-align: center;">Documento</th>
											<th style="text-align: center;">Solicitante</th>
											<th style="text-align: center;">Tipo</th>
											<th style="text-align: center;">Buro</th>
											<th style="text-align: center;">Estado</th>
											<th style="text-align: center;">Tipo Operacion</th>
										</tr>
									</thead>
									<tbody style="font-size: 12px; text-align: center;" id="tb_result">
									</tbody>
								</table>
							</div>
					</div>

		</div>
	</div>
	
<div class="modal fade" id="compose-modal-wait" tabindex="-1" role="dialog" aria-hidden="true">
           <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        
                        <h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS SE GENERA SU BUSQUEDA </h4>


                        <div class="col-md-12 hide" id="succes">
                            <!-- Primary box -->
                            <div class="box box-solid box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">BUSQUEDA DE PLANTILLAS</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <span id="respuesta"></span>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col -->


                    </div>

                <div class="modal-body">

                                
                    <div class="data"></div>
                    <div class="loader"></div> 
                                
                         
                 </div>





                    <div class="modal-footer clearfix">


                    </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



<div class="modal fade" id="compose-modal-calificar" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
           <div class="modal-dialog modal-lg" style="width: 80%;" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        
                       <div id="box_client_title" class="box box-info">

						    <div class="box-header" id="titulo" style="background-color: #fffdfa;box-shadow: 0px 9px 10px -9px #888888;">
						        <div class="row">
						                
						                <div class="col-md-2 text-center">
						                    <h5 class=""><i class="fa fa-user"></i>
						                    <strong><label id="lbl_solicitante" ></label></strong>
						                    
						                    </h5>
						                </div>
				                        <div class="col-md-2 text-center">
				                            <h5 class=""><i class="fa fa-id-card"></i>
				                            <strong><label id="lbl_documento" ></label></strong>
				                            </h5>
				                        </div>
				                        <div class="col-md-2 text-center">

				                            <h5 class="">
				                                Tipo: <strong><label id="lbl_tipo_solicitud" ></label></strong>
				                            </h5>
				                        </div>
				                        <div class="col-md-2 text-center">
				                            <h5 class="">
				                                Fecha alta: <strong><label id="lbl_fecha_alta" ></label></strong>
				                            </h5>
				                        </div>

				                        <div class="col-md-2 text-center">
				                            <h5 class="">
				                                Fecha aprobado: <strong><label id="lbl_fecha_aprobado" ></label></strong>
				                            </h5>
				                        </div>
				                        <div class="col-md-2 text-center">
				                            <h5 class="">
				                                Monto solicitado: <strong>$<label id="lbl_monto" ></label></strong>
				                            </h5>
				                        </div>
						        </div>
						    </div><!-- end box-header -->
						</div><!-- end box-info -->   


                    </div>

                <div class="modal-body">
                	<form name="frm_califica" method="POST" id="frm_califica">
						<div class="card card-success">
							<div class="card-header">
								<h3 class="card-title text-center">Calificación de servicio</h3>
							</div>
							<div class="card-body">
				                
								<div class="row">
									<input type="hidden" name="txt_hd_solicitud" id="txt_hd_solicitud">
									<input type="hidden" name="txt_hd_track" id="txt_hd_track">
									<input type="hidden" name="txt_hd_operacion" id="txt_hd_operacion">
									<input type="hidden" name="txt_hd_id_auditoria" id="txt_hd_id_auditoria">
									<div class="col-md-2 text-center">

										<label for="exampleFormControlTextarea1"><i class="fa fa-telephone"></i>Numero telefonico</label>
										<select class="select2 form-control-lg" style="width: 100%;" name="sl_tlfcliente" id="sl_tlfcliente"></select>
									</div>
									<div class="col-md-8 text-center">
											<div class="form-group">
											<label for="exampleFormControlTextarea1">Observaciones</label>
											<textarea class="form-control" name="txt_observaciones" id="txt_observaciones" rows="3" required></textarea>
											</div>
									</div>
									<div class="col-md-2 text-left">
										<label for="exampleFormControlTextarea1">Calificación</label>
										<!-- radio -->
										<div class="form-group clearfix">
											<div class="icheck-success d-inline">
											<input type="radio" id="rd_califica1" value="BUENA" name="rd_califica" checked>
											<label for="rd_califica1">
												BUENA
											</label>
											</div>
											<div class="icheck-warning d-inline">
											<input type="radio" id="rd_califica2" value="REVISAR" name="rd_califica">
											<label for="rd_califica2">
												REVISAR
											</label>
											</div>
											<div class="icheck-danger d-inline">
											<input type="radio" id="rd_califica3" value="MALA" name="rd_califica">
											<label for="rd_califica3">
												MALA
											</label>
											</div>
										</div>
									</div>
								</div>
				                  	
							</div>       
						</div>

						<div class="row">
							<div class="col-md-12">
								<table class="table table-bordered table=hover display table-striped" id="resumen-auditoria" style="width: 100%">
									<thead style="background: rgba(103, 58, 183, 0.19);">
										<th>Id</th>
										<th>Fecha</th>
										<th>Solicitud</th>
										<th>Número</th>
										<th>Observación</th>
										<th>Calificación</th>
										<th>Proceso</th>
										<th>Acción</th>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>




                    <div class="modal-footer clearfix">
                    	<div class="row">
		                  		<div class="col-md-6 text-right">
		                  			<button type="submit" class="btn btn-lg btn-success" id="btn_guardar">
								   		<i class="fa fa-save"></i> Guardar
									</button>
								</div>
								<div class="col-md-6 text-left">
		                  			<button class="btn btn-lg btn-default" type="button" data-widget="remove" id="clouse_modal">
								   		<i class="fa fa-times"></i> Cancelar
									</button>
								</div>
		              	</div>
                    </div>
				</form>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    </div><!-- /.modal -->
<script>
	$(document).ready(function(){
		$("#sl_operadores").select2();

		$("#form_search").on('submit', function(event){
			event.preventDefault();
			AuditarOperadorOnline();
		});
	});

	/********************************************************/
	/*** Envío por AJAX del formulario de la calificación ***/
	/********************************************************/
	$('#frm_califica').submit(function (event){

		event.preventDefault();

		let base_url = $("#base_url").val();
		/*** Se verifica si es un create o un update para la ruta ***/
		let url = $('#txt_hd_id_auditoria').val() 
			? 'ApiAuditoriaInterna/actualizarAuditoria/' + $('#txt_hd_id_auditoria').val() 
			: base_url + 'api/ApiAuditoriaInterna/GuardarAuditoria';

		$.ajax({

		data: $('#frm_califica').serialize(),
		url:   url,
		type: 'POST',
		})
		.done(function(respuesta){
			if (respuesta.status.ok) {
				if (respuesta.auditoria.length > 0) {
					let fila = {
						id: respuesta.auditoria[0].id,
						fecha_auditado: respuesta.auditoria[0].fecha_auditado,
						id_solicitud: respuesta.auditoria[0].id_solicitud,
						tlf_cliente: respuesta.auditoria[0].tlf_cliente,
						observaciones: respuesta.auditoria[0].observaciones,
						gestion: respuesta.auditoria[0].gestion,
						proceso: respuesta.auditoria[0].proceso
					};
					$('#resumen-auditoria').dataTable().api().row.add(fila).draw();
					
					$('select option').removeAttr( "selected" );
					$('input[type=radio]').removeAttr( "checked" );
					$('#txt_hd_id_auditoria').val("");
					$('#txt_observaciones').val("");
					rowData = '';
					$('#rd_califica1').prop( "checked", true );

					Swal.fire("Listo!",'',"success");
				}
			} else{
				Swal.fire("Ups!",respuesta.message,"error");
			}
		})
		.fail(function(xhr,err){
			Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
		});
	});		
</script>