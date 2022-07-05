
<div class="content-wrapper" id="form_Convenio" style="display: block; height: auto;">
    <div class="overlay" align="center" id="divCargando" style="display: none;">
      <br>
      <i class="fa fa-refresh fa-spin" style="font-size: 22px;"></i>
    </div>
    <div class="row">
      <!-- Main content -->
      <section class="content" style="height: auto;">
        <div class="col-md-12" align="center" style="height: auto;">
            <!-- Horizontal Form -->
          <div class="login-box-body" style="height: auto; text-align: left; font-size: 14px;">
            <strong>Dias Comerciales</strong>
            
            <a
              href="<?php echo base_url() ?>parametria/parametria"
              class="btn btn-xs bg-navy pull-right" title="Volver">
              <i class="fas fa-redo" ></i>&nbsp;&nbsp;&nbsp;Volver
            </a>
            <a 
              class="btn btn-xs bg-blue pull-right" 
              title="Registrar Dia" 
              onClick="nuevoDiaComercial()">
              <i class="fa fa-user-plus"></i>&nbsp;&nbsp;&nbsp;Nuevo Dia
            </a>
            <hr style="margin-top: 4px;">
            <input type="hidden" id="idDia">
            <main role="main" style="margin-top: -10px;">
              <section id="formSection">
                <div class="box-body" style="margin-top: -10px;">
                  <div class="row">
                    <div class="col-lg-12" id="cuerpolistaDiasComerciales" style="display: block">
                      
                    </div>
                  </div>
                </div>
              </section>
            </main>
          </div>

        </div>

      </section>
    <!-- /.content -->
    </div>
  </div>
<script src="<?php echo base_url('assets/function.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/sweetalert2.js') ?>"></script>

<script src="<?php echo base_url() ?>assets/js/parametria/diascomerciales/diasComerciales.js"></script>
