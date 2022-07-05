<!-- Basic scripts -->
</div>
<?php 
    $modulo = $this->uri->segment(2); 
    // var_dump($modulo);die;
    if( ($modulo == "atencionCliente" || $modulo == "cobranzas" || $modulo == "renderGestion" || $modulo == "renderCobranzas") ){
?>

<div class="col-md-2 ___col-vchat-content">
    <div id="vue-app">
        <chats-main></chats-main>
    </div>
</div>
    <?php } ?>
</div>

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
<script type="application/javascript" src="<?php echo base_url(); ?>assets/js/softphone/llamada.js"></script>

<script type="text/javascript" src="<?php  echo base_url('assets/js/twilio.min.js'); ?>"></script>
<script type="text/javascript" src="<?php  echo base_url('assets/js/softphone/quickstart.js'); ?>"></script>
<!-- <script src="https://js.pusher.com/7.0/pusher.min.js"></script>    
<script src="https://js.pusher.com/7.0/pusher-with-encryption.min.js"></script> -->

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.js"></script> -->

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!-- 		[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif] -->

<!--Gestion-->
<!-- <script src="<?php //echo base_url('assets/gestion/gestion.js?'.microtime());?>"></script> -->
</div>
</body>
</html>


   <script>
     var SERVER_LLAMADAS = "";
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
                 SERVER_LLAMADAS = '<?=UP_CASOS_NEOTELL?>'
                iniciarLoopLLamada();

              }
        }else if (sessionStorage.switch_valor=="activo_neotell_colombia") {
  
              if ($("input#hdd_leyendo_caso").val() != "1" && $("input#hdd_tipo_operador").val() == "1" || $("input#hdd_tipo_operador").val() == "4" || $("input#hdd_tipo_operador").val() == "5" || $("input#hdd_tipo_operador").val() == "6" || $("input#hdd_tipo_operador").val() == "9" || $("input#hdd_tipo_operador").val() == "18" ) 
              {
                SERVER_LLAMADAS = '<?=UP_CASOS_NEOTELL_COLOMBIA?>'
                iniciarLoopLLamada();

              }
        }else if (sessionStorage.switch_valor=="activo_twilio") {
          // startupClient()
               SERVER_LLAMADAS = '<?=URL_TWILIO_VOICE?>' 
            
        }else{
          detenerLoopLLamada();
        }

      })
   </script>