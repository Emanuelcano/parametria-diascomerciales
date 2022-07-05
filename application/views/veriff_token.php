
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Solventa | LogIn</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css'); ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/fonts/font-awesome.min.css'); ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/fonts/ionicons.min.css'); ?>" />
        <link rel="stylesheet" href="<?php echo base_url();?>assets/adminlte/dist/css/AdminLTE.min.css"> <!-- https://adminlte.io/themes/AdminLTE/index2.html -->
        <script src="<?php echo base_url('assets/jquery/jquery-3.4.1.min.js');?>"></script>
        <link rel="stylesheet" href="<?php echo base_url('assets/css/sweetalert2-7-33-1.css');?>" />

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <style>
             a{
              cursor: pointer;
            }
            a.disabled{
                color: grey;
                cursor:not-allowed;
            }

        </style>
    </head>
    <body class="hold-transition login-page">
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <?php //var_dump($this->session->userdata('temp_tocken'))?>
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Solventa <span style="color:#E8BF0A">Col</span><span style="color:#003893">om</span><span style="color:#CE1126">bia</span></b></a>
        </div>
      <!-- /.login-logo -->
        <div class="login-box-body">    
            <h3 class="login-box-msg">Verificación de Clave Token</h3>
                <p>Si ingresa el codigo incorrecto 3 veces su usuario sera bloqueado.</p>
            
                <div class="form-group has-feedback <?php echo form_error('user')?'has-error':''; ?>">
                    <input id="token" type="text" class="form-control" placeholder="Token">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                
                <?php
                    if($this->session->userdata('temp_tocken') != null){
                ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <p style="color:olive; font-style:bold">Su código de seguridad fue enviado por mensaje, el cual tendra una validez de 60seg.  <a class="disabled" id="resend">Reenviar código de seguridad</a></p>
                                
                            </div>
                        </div>
                <?php
                    } 
                ?>
                <div class="row">
                   
                    <div class="col-xs-12">
                      <button type="button" class="btn btn-primary btn-block btn-flat disabled" id="validate">Verificar</button>
                    </div>
                    <div class="col-xs-12">
                        <p style="color:red; font-style:bold"><?php echo $this->session->flashdata('msg_error'); ?></p>
                    </div>
               
                </div>
            
           

        </div>
      <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->


    
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js');?>"></script>
        <script src="<?php echo base_url('assets/js/sweetalert2-7-33-1.js'); ?>" ></script>
        <script src="<?php echo base_url('assets/js/veriff_token.js'); ?>" ></script>

    </body>
</html>