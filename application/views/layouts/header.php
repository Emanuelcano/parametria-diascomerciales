<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="<?php echo base_url() ?>assets/images/LOGO2.png" rel="icon">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <title>Solventa - <?php echo $title; ?></title>
    <meta/>

    <!-- Vue n' chat -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/app.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap" rel="stylesheet">

    <!-- Librerias CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css'); ?>"/>
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url('assets/bootstrap-datepicker/css/bootstrap-datepicker.standalone.css'); ?>"/>
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url('assets/datatables/css/jquery.dataTables.min.css'); ?>"/>
    <!-- https://datatables.net/ -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url('assets/datatables/css/buttons.dataTables.min.css'); ?>"/>
    <!-- https://datatables.net/extensions/buttons/ -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url('assets/datatables/css/select.dataTables.min.css'); ?>"/>
    <!--  https://datatables.net/extensions/select/ -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url('assets/datatables/css/responsive.dataTables.min.css'); ?>"/>
    <!--  https://datatables.net/extensions/responsive/ -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url('assets/bootstrap-slider/css/bootstrap-slider.css'); ?>"/>
    <!-- https://seiyria.com/bootstrap-slider/ -->
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url('assets/bootstrap-timepicker/css/bootstrap-timepicker.min.css'); ?>"/>
    <!--http://jdewit.github.io/bootstrap-timepicker/ -->
        <link rel="stylesheet" type="text/css"
          href="<?php echo base_url('assets/bootstrap/fonts/font-awesome.min.css'); ?>"/>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/bootstrap/fonts/ionicons.min.css'); ?>"/>
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminlte/dist/css/AdminLTE.min.css">
    <!-- https://adminlte.io/themes/AdminLTE/index2.html -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminlte/dist/css/skins/_all-skins.min.css">
    <!-- https://adminlte.io/themes/AdminLTE/index2.html -->
    <link rel="stylesheet" href="<?php echo base_url('assets/select2/css/select2.min.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/fileinput.css');?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/sweetalert2-7-33-1.css');?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dualSelectList.css');?>"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/jquery-ui/jquery-ui.css'); ?>"/>
    <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap/css/toastr.css');?>"/>
    <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap/css/toastr.min.css');?>"/>

      <!-- Morris chart -->
      <!--   	<link rel="stylesheet" href="<?= base_url(); ?>assets/adminlte/bower_components/morris.js/morris.css">
      -->    <!-- jvectormap -->
      <!--   	<link rel="stylesheet" href="<?= base_url(); ?>assets/adminlte/bower_components/jvectormap/jquery-jvectormap.css">
      -->    <!-- Date Picker -->
      <!--   	<link rel="stylesheet" href="<?= base_url(); ?>assets/adminlte/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
      -->    <!-- Daterange picker -->
      <!--   	<link rel="stylesheet" href="<?= base_url(); ?>assets/adminlte/bower_components/bootstrap-daterangepicker/daterangepicker.css">
      -->    <!-- bootstrap wysihtml5 - text editor -->
      <!--   	<link rel="stylesheet" href="<?= base_url(); ?>assets/adminlte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
      -->
      <!-- 	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        -->


      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->

    <script type="text/javascript">var base_url = '<?php echo base_url();?>';</script>
    <script type="application/javascript" src="<?php echo base_url(); ?>assets/js/app.js"></script>

   
    

  <style type="text/css">
      .btn-circle.btn-xl {
      width: 70px;
      height: 70px;
      padding: 10px 16px;
      border-radius: 35px;
      font-size: 24px;
      line-height: 1.33;
    }

    .btn-circle {
        width: 30px;
        height: 30px;
        padding: 6px 0px;
        border-radius: 15px;
        text-align: center;
        font-size: 12px;
        line-height: 1.42857;
    }

    #icontoClose {
        
        width: 60px;
        height: 60px;
        border-radius: 50% 0% 0% 50%;
        position: absolute;
        z-index: 10;
        
        top:45%;
        margin-right:auto;

        right: -60px;
        display: block !important;
        
        line-height: 44px;
        font-size: 45px;
        text-align: center;
        -moz-border-radius: 0px 22px 22px 0px;
        -webkit-border-radius: 0px 22px 22px 0px;
        border-radius: 0px 22px 22px 0px;
        
        background: #fff;
        box-shadow: 0 0 5px
        rgba(0,0,0,.11);
        color: #3c3950;
        padding: 7px 0 7px 8px;
        margin-bottom: 4px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        transition: width 2s;
        -webkit-transition: width 1s, background 1s;
        -moz-transition: width 1s, background 1s;
        -o-transition: width 1s, background 1s;
        transition: width 1s, background 1s;
        
    }


    #myModal {
      transition: left 2s;
      position: fixed;
      z-index: 1100;
      left: -600px;
      bottom: 0;
    }

    /* #myModal:hover {
      left: 0px;
      transition: left 2s;
    } */

    .modal-active{
      left: 0px !important;
      transition: left 2s;
    }

    #icontoClose i {
      
      color: white;
      display: block;
      margin: 0 auto;
    }

    .bootstrap-timepicker-widget.dropdown-menu.open {
      z-index: 1900 !important;
    }

  </style>
<?php 
          $modulo = $this->uri->segment(2); 
?>
<!--input type="text" id="hdd_datos_centrales" data-modulo="" value='<?php echo $this->session->userdata('datos_centrales');?>'-->
<input type="hidden" id="hdd_leyendo_caso" data-modulo="" value="0">
<input type="hidden" id="hdd_id_operador" data-modulo="" value="<?php echo $this->session->userdata('idoperador');?>">
<input type="hidden" id="hdd_id_agente_w" data-modulo="" value="<?php if ($this->session->userdata('id_agente_wolkvox')){ echo $this->session->userdata('id_agente_wolkvox'); } ?>">
<input type="hidden" id="hdd_id_agente_n" data-modulo="" value="<?php if ($this->session->userdata('id_agente_neotell')){ echo $this->session->userdata('id_agente_neotell'); }?>">
<input type="hidden" id="hdd_tipo_operador" data-modulo="" value="<?php echo $this->session->userdata('tipo_operador');?>">
<input type="hidden" id="hdd_const_voice" data-modulo="" value="<?php echo URL_TWILIO_VOICE;?>">
<input type="hidden" id="base_url" data-modulo="" value="<?php echo base_url(); ?>">
  <script type="text/javascript">
  sessionStorage.datos_centrales= '<?php echo $this->session->userdata('datos_centrales');?>';
  let datos_centrales= JSON.parse(sessionStorage.datos_centrales);
  $(function () {

      $(document).ready(function() {

        $('.tel_num').inputmask('9999999999999',{placeholder:' '});
        let base_url = $("input#base_url").val() + "buscaCodTipificacion";
        
            $.ajax({
              type: "POST",
              url: base_url,
              success: function(respuesta) {
                var registros = eval(respuesta);
                     html="";
                    for (var i = 0; i < registros.length; i++)
                        {
                            html +='<option value="'+registros[i]['cod_central']+'">'+registros[i]['descripcion']+'</option>';

                        }

                    $("#exampleFormControlSelect1").html(html);
                    
                
                
              }
            });
           
        
      });

    

      $('#close_modal').click(function (event){
          event.preventDefault();

          document.getElementById('myModal').classList.remove("modal-active");
          
      });

  });
  var render = $("#txt_render_view").val();
  if (render == "true") {
    
    $(document).ready(function() {
      document.getElementById('myModal').classList = 'modal-active';
    });

  }

  ModalOpen = function (){
    // document.getElementById('').classList = 'modal-active';s
    $('#myModal').addClass('modal-active')
  }
  </script>

</head>
<body class="hold-transition skin-purple fixed sidebar-collapse"> <!--sidebar-mini-->
  <div class="wrapper" style="overflow: visible">

    <!-- componente de detalle de creditos -->
    <div class="modal" id="detalle-proyeccion" tabindex="-1" role="dialog">
      <div class="modal-dialog" style="width:95%;" role="document">
        <div class="modal-content">
          <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title">Detalle de Pago</label></h3>
          </div>
          <div class="modal-body">
                <?php 
                    if($this->session->userdata['tipo_operador'] == 13){
                ?>
                    <div class="row" style="margin:0px;">
                      <div class="col-md-12 text-right">
                        <a class ="btn btn-success" id="enviarDetalle" style="margin-bottom:10px"><i class="fa fa-send"></i> ENVIAR DETALLE POR EMAIL</a><br>
                      </div>
                    </div>
                <?php 
                   }
                ?>
                <div class="row" style="margin:0px;">
                    <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4>INFORMACION DEL CREDITO</h4></div>
                    <div class="col-sm-12">
                        <table class="table text-center" id="detalle-credito-proyeccion">
                            <thead>
                                <th style='padding: 10px;'>OTORGAMIENTO</th>
                                <th style='padding: 10px;'>CAPITAL PRESTADO</th>
                                <th style='padding: 10px;'>PLAZO</th>
                                <th style='padding: 10px;'>PRIMER VENCIMIENTO</th>
                                <th style='padding: 10px;'>MONTO DEVOLVER</th>
                                <th style='padding: 10px; background: rgba(3, 169, 244, 0.23);'>DESCUENTOS</th>
                                <th style='padding: 10px;background: rgba(139, 195, 74, 0.2);'>DEUDA AL DIA</th>
                                <th style='padding: 10px;background: rgba(139, 195, 74, 0.2);'>TOTAL COBRADO</th>
                                <th style='padding: 10px;background: rgba(139, 195, 74, 0.2);'>ULTIMO COBRO</th>
                                <th style='padding: 10px;'>ESTADO</th>
                                <th style='padding: 10px;'>DIAS ATRASO</th>
                                <th style='padding: 10px;'>CUOTAS EN MORA</th>
                                
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="col-md-12 text-center" style="background-color: #d8d5f9; box-shadow: 0px 9px 10px -9px #888888; z-index: 1;"><h4 >CUOTAS DEL CREDITO</h4></div>
                    <div class="col-sm-12">
                        <table id="tabla-detalle-pago" class="table text-center table-bordered" style="font-size:11px">
                            <thead>
                                <th style='background: ;'>CUOTA</th>
                                <th style='background: ;'>VENCIMIENTO</th>
                                <th style='background: ;'>CAPITAL</th>
                                <th style='background: ;'>INTERÉS</th>
                                <th style='background: ;'>SEGURO</th>
                                <th style='background: ;'>ADMINISTRACIÓN</th>
                                <th style='background: ;'>TECNOLOGÍA</th>
                                <th style='background: ;'>IVA</th>
                                <th style='background: rgba(255, 235, 59, 0.09);'>MONTO POR CUOTA</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>DÍAS DE ATRASO</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>INTERÉS MORA</th>

                                <th style='background: rgba(244, 67, 54, 0.25);'>HONORARIO SMS-IVR-MAIL</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>HONORARIOS RASTREO</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>HONORARIOS PREJURIDICO</th>
                                <th style='background: rgba(244, 67, 54, 0.25);'>HONORARIOS BPO</th>

                                <th style='background: rgba(3, 169, 244, 0.23);'>DESCUENTO</th>
                                <th style='background: rgba(139, 195, 74, 0.4);'>MONTO A COBRAR</th>
                                <th style='background: rgba(139, 195, 74, 0.38);'>MONTO COBRADO</th>
                                <th style='background: rgba(139, 195, 74, 0.38);'>FECHA DE COBRO</th>
                                <th style='background: rgba(158, 158, 158, 0.18);'>ESTADO</th>
                                <th></th>
                            </thead>
                            <tbody class="principal">
                                
                            </tbody>
                        </table>
                    </div>
              </div>
                        
          </div>
        </div>
      </div>
    </div>


    <!-- componente de llamadas -->
    <?php if (($modulo == "atencionCliente" || $modulo == "gestionSinComunicacion" || $modulo == "cobranzas" || $modulo == "renderGestion" || $modulo == "renderCobranzas") && ($this->session->userdata('tipo_operador') == 1 || $this->session->userdata('tipo_operador') == 4 || $this->session->userdata('tipo_operador') == 5 || $this->session->userdata('tipo_operador') == 6 || $this->session->userdata('tipo_operador') == 9 || $this->session->userdata("tipo_operador") == 18)) { ?>
    <?php ($modulo == "gestionSinComunicacion")? $class_hide = "hide":$class_hide ="" ?>  
        <div class="modal-dialog modal-notify <?php echo $class_hide?>" id="myModal" role="document">
          
          <div class="modal-content">
            
            <div class="modal-header bg-green">
            
            
            <button type="button" id="close_modal" class="close"  aria-label="Close">
            <span aria-hidden="true" class="white-text">Estado: </span> <span aria-hidden="true" id="span_estado" class="white-text"></span>     
            <span aria-hidden="true" class="white-text">×</span>
              </button>
              <button type="button" id="btn_cambio_estado" class="close"  aria-label="Disponible">
               <span aria-hidden="true" class="white-text"><i class="fa fa-check-circle"></i></span>
              </button>
              <p class="heading">Estatus llamada:<strong><?php if (isset($nombre_customer))echo $nombre_customer?></strong><span id="body_status_call" style="color:black;"></span></p>

              <p class="heading"><?php if (isset($documento)) echo "Documento:". $documento?></p>
              
              <p class="heading"><?php if (isset($monto_disponible)) echo "Monto disponible:". $monto_disponible?></p>

                <!--label class="text-rigth">tiempo de duracion:12:00:45</label-->
            </div>
            
            <div class="modal-body">
                <div id="icontoClose" class="bg-green" onclick="ModalOpen()">
                    <i class="fa fa-phone centrado"></i>
                </div>

                <section class="center-column hide">
                <h2 class="instructions">Make a Call</h2>
                <div id="call-controls" class="hide">
                  <form>
                    <label for="phone-number"
                      >Enter a phone number or client name</label
                    >
                    <input id="phone-number" type="text" placeholder="+15552221234" />
                    <button id="button-call" type="submit">Call</button>
                  </form>
                  
                  <div id="incoming-call" class="hide">
                    <h2>Incoming Call Controls</h2>
                    <p class="instructions">
                      Incoming Call from <span id="incoming-number"></span>
                    </p>
                    <button id="button-accept-incoming">Accept</button>
                    <button id="button-reject-incoming">Reject</button>
                    <!-- <button id="button-hangup-incoming" class="hide">Hangup</button> -->
                  </div>
                  <div id="volume-indicators" class="hide">
                    <label>Mic Volume</label>
                    <div id="input-volume"></div>
                    <br /><br />
                    <label>Speaker Volume</label>
                    <div id="output-volume"></div>
                  </div>
                </div>
              </section>
              <div class="row" style="margin: 0;">
                
                <div class="col-sm-6">
                    <h4>Numero de Telefono</h4>
                    <div class="form-group">
                        <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
                        <input class="form-control input-lg" type="text" id="txt_num_man" name="txt_num_man" value="<?php if (isset($telefono))echo $telefono?>">
                    </div>
                    <div class="row" style="margin-top: 0.75em;">
                      <div class="col-sm-3 ">  
                      <button type="button" class="btn btn-default num_press" value="1">1</button>    
                     </div>
                     <div class="col-sm-3 ">  
                      <button type="button" class="btn btn-default num_press" value="2">2</button>    
                     </div>
                     <div class="col-sm-3 ">  
                      <button type="button" class="btn btn-default num_press" value="3">3</button>    
                     </div> 
                    </div>
                    
                     <div class="row" style="margin-top: 0.75em;">
                       <div class="col-sm-3 ">  
                        <button type="button" class="btn btn-default num_press" value="4">4</button>    
                       </div>
                       <div class="col-sm-3 ">  
                        <button type="button" class="btn btn-default num_press" value="5">5</button>    
                       </div>
                       <div class="col-sm-3 ">  
                        <button type="button" class="btn btn-default num_press" value="6">6</button>    
                       </div>
                     </div>
                     
                     <div class="row" style="margin-top: 0.75em;">
                       <div class="col-sm-3 ">  
                        <button type="button" class="btn btn-default num_press" value="7">7</button>    
                       </div>
                       <div class="col-sm-3 ">  
                        <button type="button" class="btn btn-default num_press" value="8">8</button>    
                       </div>
                       <div class="col-sm-3 ">  
                        <button type="button" class="btn btn-default num_press" value="9">9</button>    
                       </div>
                     </div>
                     
                     <div class="row" style="margin-top: 0.75em;">
                       <div class="col-sm-3 ">  
                        <button type="button" class="btn btn-default num_press" value="*">*</button>    
                       </div>
                       <div class="col-sm-3 ">  
                        <button type="button" class="btn btn-default num_press" value="0">0</button>    
                       </div>
                       <div class="col-sm-3 ">  
                        <button type="button" class="btn btn-default num_press" value="#">#</button>    
                       </div>
                       
                     </div>
                     
                  </div>
                  <div class="col-sm-6">
                    <h4>Codificación</h4>
                    <div class="form-group">
                      
                      <select class="form-control" id="exampleFormControlSelect1"></select>
                    </div>
                    <!--div class="form-group">
                      <label for="exampleFormControlSelect1">Codigo de Actividad 2</label>
                      <select class="form-control" id="exampleFormControlSelect2">
                        
                        
                      </select>
                    </div-->
                    <div class="form-group">
                      <label for="exampleFormControlTextarea1">Comentario</label>
                      <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"><?php if (isset($telefono))echo "Tlf: ".$telefono?></textarea>
                    </div>
                    <div class="row">
                      <div class="col-sm-6">
                          <button type="button" id ="btn_cod" class="btn btn-primary btn-sm" style="border-radius: 15px;">Codificar</button>
                      </div>
                      <div class="col-sm-6">
                          <button type="button" id ="btn_codready" class="btn btn-success btn-sm" style="border-radius: 15px;" >Codificar + Ready</button>
                      </div>
                      <div class="col-md-12" style="margin-top: 0.75em;">
                         <div class="input-group">
                           <span class="input-group-addon btn-success" title="Transferir llamada" id="btn_transferir"><i class="fa fa-phone"></i></span>
                            <input class="form-control" type="text" id="txt_num_trans" name="txt_num_trans" value="" placeholder="ext.">
                            
                        </div>
                      </div>
                       
                      
                    </div>
                     
                  </div>
                  
              </div>
            </div>
            
            <div class="modal-footer flex-center">
                <button type="button" id="btn_desmute" class="btn btn-primary btn-circle btn-xl" title="Desmutear"><i class="fa fa-microphone"></i></button>
                <button type="button" id="btn_mute" class="btn btn-danger btn-circle btn-xl" title="Mutear"><i class="fa fa-microphone-slash"></i></button>
                <button type="button" id="btn_call" class="btn btn-success btn-circle btn-xl" title="Marcar/Atender"><i class="fa fa-phone"></i></button>
                <button type="button" id="btn_hang" class="btn btn-danger btn-circle btn-xl" title="Colgar"><i class="fa fa-phone"></i></button>
                <button type="button" id="btn_hold" class="btn btn-info btn-circle btn-xl" title="Hold"><i class="fa fa-pause-circle"></i></button>
                <button type="button" id="btn_reprograming" class="btn btn-primary btn-circle btn-xl" title="Reprogramar"><i class="fa fa-calendar"></i></button>
                <button type="button" id="btn_aux" class="btn btn-warning btn-circle btn-xl" title="Llamada auxiliar"><i class="fa fa-retweet"></i></button>
              
            </div>
          </div>
          
        </div>
    <?php } ?>
<input type="hidden" name="txt_tipo_operador" id="txt_tipo_operador" value="<?php echo $this->session->userdata['tipo_operador'];?>">
    <!-- validacion de espacio para el chat segun el tipo de usuario -->
    <div class="row" id="modulo-content">
    
        <?php
        if(!isset($this->session->userdata['tipo_operador'])){redirect(base_url()."login");}
          $tipo_operador=$this->session->userdata['tipo_operador'];
          // var_dump($modulo);die;
          if( ($modulo == "atencionCliente" || $modulo == "cobranzas" || $modulo == "renderGestion" || $modulo == "renderCobranzas") && ($tipo_operador == 1 || $tipo_operador == 4 || $tipo_operador == 5 || $tipo_operador == 6 || $tipo_operador == 11) ) {?>
              <div class="col-md-10 ___col-body-content">
          <?php } else {?>
              <div class="col-md-12 ___col-body-content">
          <?php } ?>


