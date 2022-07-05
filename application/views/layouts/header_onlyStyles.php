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
