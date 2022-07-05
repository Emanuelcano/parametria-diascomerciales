<style>
    body > div.swal2-container.swal2-center.swal2-fade.swal2-shown > div{
        width:850px !important;
        font-size: 16px!important;
        
    }
    body > div.swal2-container.swal2-center.swal2-fade.swal2-shown > div > div.swal2-header{
        text-align: justify!important;
        text-justify: inter-palabra!important;
    }
    #swal2-content{
        font-size: 17px!important;
        text-align: justify!important;
        text-justify: inter-palabra!important;
    }
</style>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Solventa | LogIn</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.7 -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css'); ?>" />
        <!-- Font Awesome -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/fonts/font-awesome.min.css'); ?>" />
        <!-- Ionicons -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/fonts/ionicons.min.css'); ?>" />
        <!-- Theme style -->
        <link rel="stylesheet" href="<?php echo base_url();?>assets/adminlte/dist/css/AdminLTE.min.css"> <!-- https://adminlte.io/themes/AdminLTE/index2.html -->
        <link rel="stylesheet" href="<?php echo base_url('assets/css/sweetalert2-7-33-1.css');?>" />

        <!-- iCheck -->
        <!-- <link rel="stylesheet" href="../../plugins/iCheck/square/blue.css"> -->
        <!-- jQuery 3 -->
        <script src="<?php echo base_url('assets/jquery/jquery-3.4.1.min.js');?>"></script>
        
          <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
          <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
          <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
          <![endif]-->

          <!-- Google Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body class="hold-transition login-page">
    <!-- /*** URL base ***/ -->
    
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
   
           
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Solventa <span style="color:#E8BF0A">Col</span><span style="color:#003893">om</span><span style="color:#CE1126">bia</span></b></a>
        </div>
      <!-- /.login-logo -->
        <div class="login-box-body">    
            <p class="login-box-msg">Inicio de sesión</p>

            <!-- <form action="login" method="POST"> -->
                <div class="form-group has-feedback">
                    <input id="user" name="user" type="text" class="form-control" placeholder="Usuario">
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input id="password"  type="password" class="form-control" placeholder="Password" autocomplete="off" onkeypress="pulsar(event)" > 
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <!-- <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label class="">
                            <div class="icheckbox_square-blue" aria-checked="false" aria-disabled="false" style="position: relative;"><input type="checkbox" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"><ins class="iCheck-helper" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins></div>    Remember Me
                            </label>
                        </div>
                    </div> -->
                <!-- /.col -->
                    <div class="col-xs-4">
                      <button type="button" id="login-b" class="btn btn-primary btn-block btn-flat">Login</button>
                    </div>
                      <!-- <p style="color:red; font-style:bold"><?php //echo $this->session->flashdata('msg_error'); ?></p> -->
                <!-- /.col -->
                </div>
            <!-- </form> -->
           <!--  <a href="#">I forgot my password</a><br>
            <a href="register.html" class="text-center">Register a new membership</a> -->

        </div>
      <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->

    <!-- Modal Formulario Cambio Clave -->
    <div class="modal fade" id="modalCambioClave" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Clave Vencida</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="anterior">Contraseña Anterior:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="anterior" name="anterior" required autocomplete="off" placeholder="Contraseña Anterior">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="nueva">Nueva Contraseña:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="nueva" name="password" required autocomplete="off" placeholder="Nueva Contraseña">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="repetir">Confirmar Contraseña:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="repetir" name="repetir" required autocomplete="off" placeholder="Confirmar Contraseña">
                            </div>
                        </div>
                        <div class="row text-center">
                            <a class="btn btn-info" id="aEnvio"><i class="fa fa-send"></i> ENVIAR</a>
                        </div>
                    </form>
                    <div class="text-center">
                        <h4 id="mensaje"></h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="btnClose">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Formulario Cambio Clave -->
    <div class="modal fade" id="modalCambioClaveHabilitar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modificar clave</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="nueva">Nueva Contraseña:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="nuevaClave" name="password" required autocomplete="off" placeholder="Nueva Contraseña">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-4">
                                <label for="repetir">Confirmar Contraseña:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="repetirClave" name="repetir" required autocomplete="off" placeholder="Confirmar Contraseña">
                            </div>
                        </div>
                        <div class="row text-center">
                            <a class="btn btn-info" id="aEnvioClave"><i class="fa fa-send"></i> ENVIAR</a>
                        </div>
                    </form>
                    <div class="text-center">
                        <h4 id="mensajeClave"></h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="btnClose">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modales para el Mensaje de éxito -->
    <div class="modal fade" id="modalExitoClave" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="myModalLabel">Cambio exitoso</h3>
                </div>
                <div class="modal-body">
                    <h4 class="bg-success text-center">Contraseña actualizada con éxito</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="btnClose">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalExito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="myModalLabel">Cambio exitoso</h3>
                </div>
                <div class="modal-body">
                    <h4 class="bg-success text-center">Contraseña actualizada con éxito</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="btnClose">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    
        <!-- Bootstrap 3.3.7 -->
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js');?>"></script>
        <!-- iCheck -->
        <!-- <script src="../../plugins/iCheck/icheck.min.js"></script> -->
        <script src="<?php echo base_url('assets/js/sweetalert2-7-33-1.js'); ?>" ></script>
        <script>
            
            // *** Levanta el modal si la contraseña está vencida ***
            $(document).ready(function() {
                
                // *** Envio de cambio de clave habilitado ***
                $('#aEnvioClave').on('click', function(){
                    $('#mensajeClave').text('');
                    if ($('#nuevaClave').val() == '' || ($('#repetirClave').val()) == '') {
                        $('#mensajeClave').attr({'class': 'bg-danger'});
                        $('#mensajeClave').text('Todos los campos son obligatorios');
                    }else if ($('#nuevaClave').val() !== ($('#repetirClave').val())) {
                        $('#mensajeClave').attr({'class': 'bg-danger'});
                        $('#mensajeClave').text('La nueva contraseña y su confirmación son diferentes');
                    } else if ($('#nuevaClave').val().length < 8 ) {
                        $('#mensajeClave').attr({'class': 'bg-danger'});
                        $('#mensajeClave').text('La nueva contraseña debe tener una longitud mínima de 8 caracteres');
                    } else {
                        $.ajax({
                            dataType: "JSON",
                            data: {
                                "newPassword": $('#nuevaClave').val()
                            },
                            url: $("input#base_url").val() + "api/ApiOperadores/cambio_clave_habilitado",
                            type: 'POST',
                        })
                        .done(function(respuesta){
                            if (respuesta.status.ok) {
                                $('#modalCambioClaveHabilitar').modal("hide");
                                $('#modalExitoClave').modal("show");
                            } else {
                                $('#mensajeClave').attr({'class': 'bg-danger'});
                                $('#mensajeClave').text(respuesta.message);
                            }
                        })
                        .fail(function(xhr) {
                            $('#mensajeClave').attr({'class': 'bg-danger'});
                            $('#mensajeClave').text(xhr.responseText);
                        });
                    }
                });
                // *** Enviando el formulario de cambio de contraseña ***
                $('#aEnvio').on('click', function(){
                    $('#mensaje').text('');
                    if ($('#nueva').val() == '' || ($('#anterior').val()) == '' || ($('#repetir').val()) == '') {
                        $('#mensaje').attr({'class': 'bg-danger'});
                        $('#mensaje').text('Todos los campos son obligatorios');
                    } else if ($('#nueva').val() == ($('#anterior').val())) {
                        $('#mensaje').attr({'class': 'bg-danger'});
                        $('#mensaje').text('La nueva contraseña debe ser distinta a la anterior');
                    } else if ($('#nueva').val() !== ($('#repetir').val())) {
                        $('#mensaje').attr({'class': 'bg-danger'});
                        $('#mensaje').text('La nueva contraseña y su confirmación son diferentes');
                    } else if ($('#nueva').val().length < 8 ) {
                        $('#mensaje').attr({'class': 'bg-danger'});
                        $('#mensaje').text('La nueva contraseña debe tener una longitud mínima de 8 caracteres');
                    } else {
                        $.ajax({
                            dataType: "JSON",
                            data: {
                                "oldPassword": $('#anterior').val(),
                                "newPassword": $('#nueva').val()
                            },
                            url: $("#base_url").val() + 'api/operadores/cambio_clave',
                            type: 'POST',
                        })
                        .done(function(respuesta){
                            if (respuesta.status.ok) {
                                $('#modalCambioClave').modal("hide");
                                $('#modalCambioClaveHabilitar').modal("hide");
                                $('#modalExito').modal("show");
                            } else {
                                $('#mensaje').attr({'class': 'bg-danger'});
                                $('#mensaje').text(respuesta.message);
                            }
                        })
                        .fail(function(xhr) {
                            $('#mensaje').attr({'class': 'bg-danger'});
                            $('#mensaje').text(xhr.responseText);
                        });
                    }
                });
                
                $('#login-b').on('click', function(){
                    login();
                });

            });
            
            function pulsar(e) {
                if (e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    $('#login-b').click();
                }
            }

            

            function login() {
                Swal.fire({
                title: '¡Bienvenido!',
                text: 'Bienvenido a SOLVENTA COLOMBIA SAS. LA SEGURIDAD Y PRIVACIDAD DE LOS DATOS E INFORMACIÓN ESTÁ EN SUS MANOS, por ello, recuerda en todo momento su compromiso frente a conocer y aplicar las políticas de seguridad de la información y protección de datos personales establecidas en la organización.  Con la finalidad de asegurar el debido el cumplimiento de normativas legales y directrices internas o externas, SOLVENTA COLOMBIA SAS podrá monitorear, supervisar y vigilar en cualquier momento el cumplimiento y adecuada aplicación de las políticas, lineamientos y demás aspectos que hayan sido generados para salvaguardar la seguridad y privacidad de la información. Finalmente, recuerde que un incumplimiento de las políticas y demás lineamientos puede generar sanciones.',
                icon: 'warning',
                confirmButtonText: 'Aceptar',
                allowOutsideClick: false,
                }).then((result) => {
                    let base_url = $("#base_url").val();
                    $.ajax({
                        url: base_url + 'login/login',
                        type: 'POST',
                        dataType: 'json',
                        data: { "user": $("#user").val(), "password": $("#password").val() },
                    })
                    .done(function (response) {
                        if(response.ok && typeof(response.URL) != "undefined"){
                            window.location.href = response.URL;
                        }else{
                            if(response.cambio_clave_habilitar == '1') {
                                $('#modalCambioClaveHabilitar').modal("show");
                            }
                            if(typeof(response.clave) != "undefined" && !response.clave){
                                $('#modalCambioClave').modal("show");
                            } else{
                                Swal.fire(response.message,"",  "error");
                            }
        
                        }
                    });
                })
                
            }
        </script>
    </body>
</html>