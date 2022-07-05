<div class="col-md-12" style="padding: 0px;">
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Info Cliente</a></li>
			<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Track</a></li>
			<li class=""><a href="#tab_3" data-toggle="tab" aria-expanded="false">Agenda</a></li>
			<li class=""><a href="#tab_4" data-toggle="tab" aria-expanded="false">Bancarios</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab_1">
				<?php $this->load->view('cobranzas/stand_alone_box_cliente_info', $dataInfo); ?>
			</div>
			
			<div class="tab-pane" id="tab_2">
				<?php $this->load->view('cobranzas/stand_alone_box_tracker', $dataTracker); ?>
			</div>
			<div class="tab-pane" id="tab_3">
				<?php $this->load->view('cobranzas/box_agenda_telefono_stand_alone', ["id_solicitud" => $id_solicitud,"documento"=>$documento,"agenda_telefonica" => $agenda_telefonica,"agenda_mail" => $agenda_mail]); ?>
			</div>
			<div class="tab-pane" id="tab_4">
				<?php $this->load->view('cobranzas/box_bancos_stand_alone', ["solicitude" => $solicitude,"banks"=>$banks,"type_account" => $type_account,"analisis" => $analisis,"pagado_txt" => $pagado_txt,"datos_bancarios" => $datos_bancarios]); ?>
			</div>
		</div>
	</div>
</div>
