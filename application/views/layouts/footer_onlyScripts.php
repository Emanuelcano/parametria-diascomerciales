<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/toastr.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.bundle.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap/js/popper.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-datepicker/default-config-datepicker.js'); ?>"></script>
<script src="<?php echo base_url('assets/jquery-mask/jquery.mask.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/default-config-datatables.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.altEditor.free.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/i18n/Spanish.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.buttons.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/datatables/js/dataTables.select.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-slider/js/bootstrap-slider.js'); ?>"></script>
<script src="<?php echo base_url('assets/bootstrap-timepicker/js/bootstrap-timepicker.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/adminlte/dist/js/adminlte.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/icheck/icheck.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/select2/js/select2.full.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/fileinput.js');?>"></script>
<script src="<?php echo base_url('assets/js/fileinput_locale_es.js');?>"></script>
<script src="<?php echo base_url('assets/js/autoNumeric.js');?>"></script>
<script src="<?php echo base_url('assets/js/sweetalert2-7-33-1.js');?>" ></script>

<script src="<?php echo base_url('assets/js/jquery.inputmask.js');?>" ></script>
<script src="<?php echo base_url('assets/js/jquery.inputmask.extensions.js');?>" ></script>
<script src="<?php echo base_url('assets/js/jquery.inputmask.date.extensions.js');?>" ></script>
<script src="<?php echo base_url('assets/constantes.js');?>" ></script>



<!-- Chat n' Vue -->
<!-- componente js y css area chat -->
<!--        <link href="<?php echo base_url(); ?>public/chat_files/css/jquery.fullPage.css" type="text/css" rel="stylesheet">-->
<!--        <link href="<?php echo base_url(); ?>public/chat_files/css/estilos.css" type="text/css" rel="stylesheet">-->
<link href="<?php echo base_url(); ?>public/chat_files/css/customchat.css" type="text/css" rel="stylesheet">
<link href="<?php echo base_url(); ?>public/chat_files/css/customchat-movil.css" type="text/css" rel="stylesheet">

<script type="text/javascript" src="<?php echo base_url(); ?>public/chat_files/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>public/chat_files/js/modernizr.js"></script>
<!--<script type="text/javascript" src="<?php echo base_url(); ?>public/chat_files/js/jquery.slimscroll.min.js"></script>-->
<!--<script type="text/javascript" src="<?php echo base_url(); ?>public/chat_files/js/jquery.fullPage.min.js"></script>-->
<script type="text/javascript" src="<?php echo base_url(); ?>public/chat_files/js/customchat.js"></script>

</div>
</body>
</html>


   <script>
     let btn_call_action_id = "";
      var pusher = undefined;
      pusher = new Pusher('665335991888e0778882', {  cluster: 'mt1'});
      var channels = [];

      var pusherprivate = undefined;
      pusherprivate = new Pusher('cd502ed7244bfb455431', {  
        cluster: 'us2',
        authEndpoint :  '<?=URL_VIDEOLLAMADA?>api/Videollamada/auth/<?=base64_encode($this->session->userdata['operadorEncrypter'])?>'
      });

     $('.dropdown-item').on('click', function () {
        switch_valor = $(this).attr('data-switch');

        sessionStorage.switch_valor = switch_valor;
        if (sessionStorage.switch_valor=="activo_neotell") {
  
              if ($("input#hdd_leyendo_caso").val() != "1" && $("input#hdd_tipo_operador").val() == "1" || $("input#hdd_tipo_operador").val() == "4" || $("input#hdd_tipo_operador").val() == "5" || $("input#hdd_tipo_operador").val() == "6" || $("input#hdd_tipo_operador").val() == "9" || $("input#hdd_tipo_operador").val() == "18" ) 
              {
                
                iniciarLoopLLamada();

              }
        }else if (sessionStorage.switch_valor=="activo_twilio") {
          // startupClient()
            
        }else{
          detenerLoopLLamada();
        }

      })
   </script>