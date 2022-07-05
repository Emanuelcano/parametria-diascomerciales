
<div id="box_client_title" data-id_cliente="<?= $solicitude["id_cliente"] ?>" data-documento="<?= $solicitude["documento"] ?>"class="box box-info">
    <div class="box-header" id="titulo" style="background-color: #fffdfa;box-shadow: 0px 9px 10px -9px #888888;">
        <div class="row">
            <input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>" data-tipo-operador="<?php echo $this->session->userdata('tipo_operador'); ?>">
                <div class="col-md-4 text-center">
                    <h4 class=""><i class="fa fa-user"></i>
                    <?php if(!empty($solicitude['nombres'])): ?>
                        <?php echo $solicitude['nombres'].' '.$solicitude['apellidos'];
                    endif ?>
                    </h4>
                </div>

                <?php 
                    //si el operador es externo no mostramos esta informacion del cliente
                    if($this->session->userdata['tipo_operador'] != ID_OPERADOR_EXTERNO){  ?>
                        <div class="col-md-2 text-center">
                            <h4 class=""><i class="fa fa-id-card"></i>
                            <?php if(!empty($solicitude['documento'])): ?>
                                <?php echo '<input type="hidden" id="document" name="document" value="'.$solicitude['documento'].'">'.$solicitude['documento'];
                            endif ?>
                            </h4>
                        </div>
                        <div class="col-md-2 text-center">

                            <h4 class=""><i class="fa fa-phone"></i>
                                <?php echo $solicitude['telefono']; ?>
                            </h4>
                        </div>
                        <div class="col-md-3 text-center">
                            <h4 class=""><i class="fa fa-envelope"></i>
                                <?php echo $solicitude['email']; ?>
                            </h4>
                        </div>
                <?php 
                    } else{
                        echo '<div class="col-md-7 text-center"></div>';
                } ?>
                <div class="col-md-1 text-center">
                    <a id="close_credito" href="#" title="Cerrar y continuar gestionando otro crédito"><i class="fa fa-close icon_close"></i></a>
                </div>
        </div>
    </div><!-- end box-header -->
</div><!-- end box-info -->

<script type="text/javascript">
    $("#close_credito").on('click', function()
    {
		endGestion(function() {
			$.ajax({
				url: base_url + 'api/solicitud/checkSolicitudHasTrackToday',
				type: 'POST',
				data: { "id_solicitud": $('#id_solicitud').val()},
			}).done(function (response) {
				if (response.data === false) {
					//no hay track, por tanto no realizo ninguna accion. Trackeo que cerro sin realizar accion
					let id_solicitud = $("#id_solicitud").val();
					let id_operador = $("#id_operador").val();
					saveTrackCredito('[CAMPAÑA MANUAL CERRADO SIN GESTION]', 182, id_solicitud, id_operador, function(){} );
				}

				pararCronometroTimer();
				setTimeout(() => { console.log('3cronometro parado')}, 500);
				$("#timer-progress-bar-container").addClass('hide');
				if(typeof(pusher) != "undefined"){
					$.each(channels, function( index, value ) {
						pusher.unsubscribe(value.name);
						value.unbind();
					});
				}

				let campaniaId = $("#id_campania").val();
				if (campaniaId !== '0') {
					location.reload();  
				}
				
				detenerLoopLLamada();
				$("#dashboard_principal #texto").html("");
				$("#dashboard_principal #texto").html("");
				$(".daterangepicker.ltr.single.auto-apply.opensright").remove("");
				$("#section_search_credito #form_search").show();
				$("#section_search_credito #result").show();
				$(".desempenho").show();
				$("#separador_cobranzas").show();
				$("#result").removeClass('hide');
				$("#creditos").removeClass('hide');
				let tipo = $("#id_operador").data('tipo-operador');
				
				
			});
		});
    });
</script>
