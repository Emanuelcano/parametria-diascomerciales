<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<style>
/* ALL LOADERS */

.loader{
  width: 100px;
  height: 100px;
  border-radius: 100%;
  position: relative;
  margin: 0 auto;
}

/* LOADER 6 */

#loader-6{
  top: 40px;
  left: -2.5px;
}

#loader-6 span{
    display: inline-block;
    width: 5px;
    height: 30px;
    background-color: #605ca8;
}

#loader-6 span:nth-child(1){
  animation: grow 1s ease-in-out infinite;
}

#loader-6 span:nth-child(2){
  animation: grow 1s ease-in-out 0.15s infinite;
}

#loader-6 span:nth-child(3){
  animation: grow 1s ease-in-out 0.30s infinite;
}

#loader-6 span:nth-child(4){
  animation: grow 1s ease-in-out 0.45s infinite;
}

@keyframes grow{
  0%, 100%{
    -webkit-transform: scaleY(1);
    -ms-transform: scaleY(1);
    -o-transform: scaleY(1);
    transform: scaleY(1);
  }

  50%{
    -webkit-transform: scaleY(1.8);
    -ms-transform: scaleY(1.8);
    -o-transform: scaleY(1.8);
    transform: scaleY(1.8);
  }
}
</style>

<div class="row" style="display: block;  background: #FFFFFF;">
      <div class="col-sm-12 calculo-desembolso" style=" margin-top: 5rem;">
        <div class="box-body">
          <?php //$this->load->view('tesoreria/proyeccion_desembolso',null, true); ?>
        </div>
      </div>
      <div class="col-lg-12" style="display: block;  background: #FFFFFF;">
        <div class="box-body">
            <a class="btn btn-app" id="buttonPrestamos">
              <i class="fa fa-dollar"></i> Prestamos
            </a>
            <a class="btn btn-app" id="buttonImputarPago">
              <span class="badge bg-teal" id="cantImputarPendiente"></span>
              <i class="fa fa-money"></i> Imputar Pago
            </a>
            <a class="btn btn-app" id="buttonImputarPagoArchivo">
              <i class="fa fa-money"></i> Imputacion de archivo
            </a>
            <a class="btn btn-app" id="buttonRespuestaBbva">
              <i class="fa fa-bank"></i> Respuesta BBVA
            </a>
            <a class="btn btn-app" id="buttonRespuestaSantander">
              <i class="fa fa-bank"></i> Respuesta Santander
            </a>
            <a class="btn btn-app" id="buttonRespuestaBanColombia">
              <i class="fa fa-bank"></i> Respuesta BanColombia
            </a>
            <a class="btn btn-app" id="buttonRespuestaBancobogota">
              <i class="fa fa-bank"></i> Respuesta Banco Bogota
            </a>
            <a class="btn btn-app" id="buttonProcesarGasto" >
              <span class="badge bg-teal" id="cantidad_gastos_pendientes"><?= $cantidad_procesar_gasto;?></span>
              <i class="fa fa-money"></i> Procesar Gasto
            </a>
            <a class="btn btn-app" id="buttonValidarDesembolsos">
              <span class="badge bg-teal" id="cantDesembolsoValidar">
                <?= $cantDesembolsoValidar;?>
              </span>
              <i class="fa fa-check"></i> Validar Desembolso
            </a>    
            <a class="btn btn-app" id="buttonImputacion">
              <i class="fa fa-money"></i>Imputacion
            </a>                            
            <a class="btn btn-app" id="buttonGeneracionDebitoAutomatica">
              <i class="fa fa-money"></i> Generacion Manual Debitos
            </a>
            <a class="btn btn-app" id="buttonGeneracionDebitos">
              <i class="fa fa-money"></i> Generacion Debitos
            </a>
            <a class="btn btn-app" id="buttonLecturaEnvios">
              <i class="fa fa-money"></i> Carga RCGA y RNOV
            </a>
            <a class="btn btn-app" id="buttonInformeEnvios">
              <i class="fa fa-bar-chart"></i>Informe Envios
            </a>
            <a class="btn btn-app" id="buttonDevolucion">
              <span class="badge bg-red" id=""><?= $total_devoluciones?></span>
              <i class="fa fa-bars"></i>Devoluciones
            </a>
          </div>
      </div>    
</div>
<div>
  <div class="col-lg-12" id="main" style="display: block">
    
  </div>
</div>
<script src="<?php echo base_url() ?>assets/function.js"></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script src="<?php echo base_url() ?>assets/tesoreria/tesoreria.js"></script>
