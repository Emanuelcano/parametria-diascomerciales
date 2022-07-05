<style>
  div.fondo{
    background-color: #e8daef!important;
    border: none!important;
    color: #777!important;
    
  }
  li.active > a{
    background-color: #e8daef!important;
  }
  ul {
    margin-bottom: 10%;
  }
  
</style>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>">
<div class="contenedor">
  <ul class="nav nav-tabs">
    <li class="active fondo" id="mostrarOperdoresVentas"><a href="#operadoresVentas" id='configO' data-toggle="tab">Operador(es) receptores</a></li>
    <li class="fondo" id="mostrarConfiguraciones"><a href="#configuraciones" id="configA" data-toggle="tab">Configuraciones</a></li>

  </ul>

  <div class="tab-content">
    <div class="col-lg-12 tab-pane fade in active" id="operadoresVentas"  style="padding: 0.5%;">

      
      <div id="" >
        <?= $this->load->view('supervisores/supervisor_ventas/tabla_operadores', null, true); ?>  
      </div>

    </div>

    <div class="col-lg-12 tab-pane fade" id="configuraciones" style="padding: 0!important;">
      <!-- style="overflow: auto;" -->
      <div id="" >
        <?= $this->load->view('supervisores/supervisor_ventas/tabla_configuracion_gestion_obligatoria', null, true); ?>  
      </div>

    </div>


  </div>
</div>
<script src="<?php echo base_url('assets/js/sweetalert2.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/supervisores/ventas/ventas.js'); ?>"></script>



