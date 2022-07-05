<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/diccionario.js');?>"></script>
<script src="<?php echo base_url('assets/gestion/gestion.js?'.microtime());?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets\css\agenda_custom.css'); ?>"/>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="id_skill" value="<?php if (isset($cola))echo $cola?>">
<input type="hidden" id="id_operador" name="id_operador" data-validaciones="<?= $validaciones ?>" data-user="<?php echo $this->session->userdata('user')->first_name." ".$this->session->userdata('user')->last_name; ?>" data-tipo_Operador ="<?php echo $this->session->userdata('tipo_operador'); ?>" value="<?php echo $this->session->userdata('idoperador'); ?>">
<input type="hidden" name="txt_id_soli" id="txt_id_soli" value="<?php if (isset($id_solicitud))echo $id_solicitud?>">
<input type="hidden" name="txt_render_v" id="txt_render_v" value="<?php if (isset($render_view))echo $render_view?>">
<?php

	if (isset($render_view) && $render_view=="true") { ?>
		<script type="text/javascript">
			var id_solicitud = $("#txt_id_soli").val();
			var render_view = $("#txt_render_v").val();

			consultar_solicitud(id_solicitud,render_view);
		</script>
<?php }
	$this->session->set_userdata('render_view',null);
	$this->session->set_userdata('id_solicitud',null);

	$modulo = $this->uri->segment(2);
	$obligatorias = FALSE;
	
	if($modulo == 'atencionCliente' && !is_null($this->session->userdata('horaEntrada')) && !empty($conf_obligatorias) &&  $operadoresAutomaticas ){
		$horaEntrada = $this->session->userdata('horaEntrada');
		$tipo_operador_sesion = $this->session->userdata('tipo_operador');
		$conf_obligatorias = $conf_obligatorias[0];
		$obligatorias = TRUE;
?>
		<input type="hidden" data-modulo="atencionCliente" data-control="<?= $conf_obligatorias->id?>" data-seg_ejecucion="<?= $conf_obligatorias->seg_ejecucion?>" data-porcentaje_alerta_extension="<?= $conf_obligatorias->porcentaje_alerta_extension?>" data-segundos_alert_ext="<?= $conf_obligatorias->segundos_alert_ext?>" data-porcentaje_warning="<?= $conf_obligatorias->porcentaje_warning?>" data-min_get_solicitudes="<?= $conf_obligatorias->min_get_solicitudes?>" data-min_extension="<?= $conf_obligatorias->min_extension?>" data-min_gestion="<?= $conf_obligatorias->min_gestion?>" data-gestion_chats="<?= $conf_obligatorias->min_gestion_chats?>" data-proceso_obligatorio="<?= $conf_obligatorias->min_proceso_obligatorio?>" data-consecutivo="<?= $conf_obligatorias->extensiones_consecutivas?> name="horaEntrada" id="horaEntrada" value="<?php if (isset($horaEntrada))echo $horaEntrada?>">
		<input id="tipo_operador_sesion" type="hidden" value="<?php echo $tipo_operador_sesion;?>">
		<div class="box-header with-border" class="col-lg-12"><div class="col-md-12">&nbsp;</div></div>

		<script type="text/javascript">
			const EXTENSION_TIME = parseInt($("#horaEntrada").data("min_extension"))*60000;    	//tiempo de extension para una gestion. debe venir de BD
			const ALERT_TIME = parseInt($("#horaEntrada").data("segundos_alert_ext")*1000);		//tiempo display alert
			const WARNING_TIME = parseFloat($("#horaEntrada").data("porcentaje_warning"));		//tiempo para que la barra de tiempo pase a apmarillo
			const DANGER_TIME = parseFloat($("#horaEntrada").data("porcentaje_alerta_extension"));		//tiempo para que la barra de tiempo pase a rojo
			const GET_START = parseFloat($("#horaEntrada").data("seg_ejecucion"));		//tiempo para empiecen las consultas de las solicitudes obligatorias
			const GESTION_CHAT = parseFloat($("#horaEntrada").data("gestion_chats"));		//tiempo para gestionar los chats
			const PROCESO_OBLIGATORIO = parseFloat($("#horaEntrada").data("proceso_obligatorio"));		//tiempo durante el cual se mostraran las solicitudes obligatorias
			const EXTENCIONES_CONSECUTIVAS = parseFloat($("#horaEntrada").data("consecutivo"));		//tiempo durante el cual se mostraran las solicitudes obligatorias
			const GET_SOLICITUDES = parseFloat($("#horaEntrada").data("min_get_solicitudes"));		//tiempo durante el cual se mostraran las solicitudes obligatorias
			const TIPO_OPERADOR_SESION = $("#horaEntrada").data("tipo_operador");		//tiempo durante el cual se mostraran las solicitudes obligatorias
			init();
	</script>

<?php 
	}
 ?>

<!-- Esto es para que el header no superponga el buscador -->
<div class="box-header with-border" class="col-lg-12"><div class="col-md-12">&nbsp;</div></div>


	<div id="dashboard_principal" style="display: block; background: #FFFFFF;">
		<div class="box-header with-border" class="col-lg-12">
			<div class="col-md-12">
		    	<?php $this->load->view('gestion/section_search_solicitud',['solicitudes_status' => $solicitudes_status, 'solicitudes_types' => $solicitudes_types, 'operators' => $operators, 'validaciones'=>$validaciones, 'bancos' => $bancos]); ?>
			</div>
		</div>
		

		<div class="col-md-12">
			<div id="texto_sol_ajustes">
				<?php $this->load->view('gestion/box_solicitud_ajustes',[$solicitud_ajustes]); ?>
			</div>
		</div>
		<div class="col-md-12">
			<div id="texto_agenda" data-id-operador = "<?= $idOperador?>">
				<?php $this->load->view('gestion/box_casos_agendados',[$agenda_operadores]); ?>
			</div>
		</div>
		<div class="col-md-12" id="solicitudPendientes" style="margin-bottom: 0.5em; margin-top: 0.5em;">
		</div>
    	<div class="col-md-12">
        	<?php $this->load->view('gestion/table_solicitudes_ajax'); ?>
    	</div>
		<!-- SE SOLICITO QUE SE BORRARA EL 12/05/2022; FUE COMENTADA POR SI EN UN FUTURO LA DESEAN HABILITAR NUEVAMENTE -->       
        <?php //if($this->session->userdata['tipo_operador'] == 2 && $desembolso != 0){ ?>            
			<!-- <div class="col-md-12">-->                    
					<?php //$this->load->view('gestion/table_desembolso_ajax'); ?>
            <!-- </div> -->
        <?php //} ?> 
		<div id="texto" class="col-lg-12" style="display: block; background: #EBEDEF;"></div>
	</div>