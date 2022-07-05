<span class="hidden-xs">
	<?php
	    $usuario     = $this->session->userdata("username");
	    $tipoUsuario = $this->session->userdata("tipo");
	?>
</span>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="usuario_session" value="<?php echo $usuario?>">
<input type="hidden" id="tipo" value="<?php echo $tipoUsuario;?>">
<br>
<br>
<br>
<div>
    <div class="col-lg-12" id="main" style="display: block;">
        <input type="hidden" name="param" id="param">
        <input id="desde_2" type="hidden">
        <input id="hasta_2" type="hidden">
        <input id="desde_1" type="hidden">
        <input id="hasta_1" type="hidden">
        <?php 
            /**
             * Return view in anthoer view with all data
             */
            $data['indicadores'] = []; echo $this->load->view('tablero_cobranza/indicadores_cobranza', ['data'=>$data], TRUE);
        ?>
    </div>
</div>
<script src="<?php echo base_url('assets/function.js'); ?>"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.colVis.min.js"></script>
<script src="<?php echo base_url('assets/js/tablero_cobranza/tablero.js') ?>"></script>

