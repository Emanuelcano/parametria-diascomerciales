<?php $tipo_operador=$this->session->userdata['tipo_operador']; 
if($this->session->userdata['idoperador']==2){
    +redirect(base_url()."logout");
}?>

<!-- Global messages -->
   <div class="global-message" style="display: none;">
       <div class="alert"></div>
   </div>
   <!-- Site wrapper -->
   <header class="main-header">
       <!-- Logo -->
       <a onclick="detenerLoopLLamada();window.location.replace('<?php echo base_url("dashboard"); ?>');" class="logo">
           <!-- logo for regular state and mobile devices-->
           <!-- si el operador es OPERADOR EXTERNO COBRANZA se oculta el logo de la empresa-->
           <?php echo ($tipo_operador == ID_OPERADOR_EXTERNO)? '<i class="fa fa-home fa-2x" style="margin-top: 5px;"></i>':'<img src="'.base_url().'assets/images/LOGO2.png" class="col-lg" style="width: 100%;">' ?>
        </a>
       <!-- Header Navbar: style can be found in header.less -->
       <nav class="navbar navbar-static-top" style="height: 5%;">
                           <span></span>
                       <div class="navbar-custom-menu" style="height: 5%;">
               <ul class="nav navbar-nav" style="height: 5%;">

                    <?php if ($this->session->userdata("tipo_operador") == 1 || $this->session->userdata("tipo_operador") == 4 || $this->session->userdata("tipo_operador") == 5 || $this->session->userdata("tipo_operador") == 6 || $this->session->userdata("tipo_operador") == 9 || $this->session->userdata("tipo_operador") == 18) {
                      
                     ?>
                    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          Central
          
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item" data-switch="activo_ninguno">
            <!-- Message Start -->
            <div class="media">
              
              <div class="media-body text-center">
                <h4 class="dropdown-item-title">
                  Ninguna
                  
                </h4>
              </div>
            </div>
            <!-- Message End -->
          </a>
           <div class="dropdown-divider"></div>
              <a href="#" class="dropdown-item" id="startup-button" data-switch="activo_twilio">
                <div class="media  text-center">
                  
                  <div class="media-body">
                    <h4 class="dropdown-item-title">
                      Twilio
                      
                    </h4>
                  </div>
                </div>
                
              </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item" data-switch="activo_neotell">
            <!-- Message Start -->
            <div class="media text-center">
              
              <div class="media-body">
                <h4 class="dropdown-item-title">
                  Neotell <img width="20px" height="20px" src="<?php echo base_url();?>/assets/images/ban_arg.png" alt="Argentina">
                  
                </h4>
              </div>
            </div>
            <!-- Message End -->
          </a>
          <a href="#" class="dropdown-item" data-switch="activo_neotell_colombia">
            <!-- Message Start -->
            <div class="media text-center">
              
              <div class="media-body">
                <h4 class="dropdown-item-title">
                  Neotell <img width="20px" height="20px" src="<?php echo base_url();?>/assets/images/ban_col.png" alt="Colombia">
                  
                </h4>
              </div>
            </div>
            <!-- Message End -->
          </a>
          
          
        </div>
      </li><?php } ?>
                   <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                       <a href="<?= base_url()."logout" ?>" class="dropdown-toggle">
                           <span class="hidden-xs" style="height: 5%;">
                               Cerrar Sesi&oacute;n
                           </span>
                       </a>
                    </li>
               </ul>
           </div>
       </nav>
       <div class="progress hide" style="margin-bottom:0px;" id="timer-progress-bar-container">
          <div class="progress-bar progress-bar-striped progress-bar-success active" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
          <a class="btn btn-xs btn-danger cronometro-button" id="cronometro-button-danger" role="button" style="margin:0; position:absolute; right:55px" onclick = "restart_solicitudes_obligatorias()"><i class="fa fa-ban"></i></a>
          <a class="btn btn-xs btn-warning cronometro-button disabled" id="cronometro-button-warning" role="button" style="margin:0;  position:absolute; right:30px" onclick = "solicitar_extension(0)"><i class="fa fa-refresh"></i></a>
					<a class="btn btn-xs btn-info cronometro-button disabled" id="cronometro-button-descanso" role="button" style="margin:0;  position:absolute; right:5px" onclick = "temporizador_control_descanso()"><i class="fa fa-pause"></i></a>
          
       </div> 

   </header>

<script>
  $(document).on("mousedown", function (e1) {
    if (e1.which === 2) {
      $(document).one("mouseup", function (e2) {
        if (e1.target === e2.target) {
          var e3 = $.event.fix(e2);
          e3.type = "middleclick";
          $(e2.target).trigger(e3);
        }
      });
    }
  }); 
  $(document).on("middleclick", ".logo", function (e) {
    window.open('<?php echo base_url("dashboard"); ?>', '_blank');
  });
</script>
