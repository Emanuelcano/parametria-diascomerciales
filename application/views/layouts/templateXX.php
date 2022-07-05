<!DOCTYPE html>
<html lang="es">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="">
      <meta name="author" content="">
      <title>Mastercard - Dashboard</title>

      <link href="<?php echo site_url('public/vendor/fontawesome-free/css/all.min.css');?>" rel="stylesheet" type="text/css">
      <link  rel="icon" type="image/png" href="<?php echo site_url('public/images/favicon.ico');?>"/>   
      <link rel="stylesheet" href="<?php echo site_url('public/css/inicial.css');?>" id='stilo'>
      <link href="<?php echo site_url('public/css/jquery-ui.css');?>" rel="stylesheet">    
      <script src="<?php echo site_url('public/vendor/jquery/jquery.min.js');?>"></script>
      <script src="<?php echo site_url('public/js/custom.js');?>"></script>
      <script src="<?php echo site_url('public/vendor/jquery/jquery-ui.js');?>"></script>   
   </head>
   <body id="page-top">
      <nav class="navbar navbar-expand navbar-dark bg-dark static-top">
         <a class="navbar-brand mr-1" href="/"><img    style="width: 150px;"  src="<?php echo site_url('public/images/logo.png');?>"></a>
         <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
         <i class="fas fa-bars"></i>
         </button>
         <!-- Navbar Search -->
         <div class="d-none d-md-inline-block ml-auto mr-0 mr-md-3 my-2 my-md-0">
         </div>
         <!-- Navbar -->
         <ul class="navbar-nav ml-auto ml-md-0">
            <li class="nav-item dropdown no-arrow">
               <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fas fa-user-circle fa-fw"></i>
               </a>
               <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
               </div>
            </li>
         </ul>
      </nav>
      <div id="wrapper">
         <!-- Sidebar -->
         <ul class="sidebar navbar-nav">
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fas fa-fw fa-folder"></i>
               <span>Accesos</span>
               </a>
               <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                  <h6 class="dropdown-header">Paginas</h6>
                 <a class="dropdown-item" href="<?php echo site_url('Atencion');?>">Atencion</a>
                 <a class="dropdown-item" href="<?php echo site_url('Productos');?>">Productos</a>
                 <a class="dropdown-item" href="<?php echo site_url('Tesoreria');?>">Tesoreria</a>
                 <a class="dropdown-item" href="<?php echo site_url('Soporte');?>">Soporte</a>
                 
               </div>
            </li>            
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fas fa-fw fa-folder"></i>
               <span>Estilos</span>
               </a>
               <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                
                  <a class="dropdown-item" href="<?php echo site_url('TemplateC');?>">Estilos</a>
                  <!-- <a class="dropdown-item" href="<?php echo site_url('TestConsulta/tcreditoConsultaCuenta/1');?>">Test Consulta Pin (api)</a> -->
               </div>
            </li>         
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fas fa-fw fa-folder"></i>
               <span>Cod. Barra</span>
               </a>
               <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                
                  <a class="dropdown-item" href="<?php echo site_url('CodBarras');?>">Ejemplo CB</a>
                  
               </div>
            </li>        
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fas fa-fw fa-folder"></i>
               <span>PDF</span>
               </a>
               <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                
                  <a class="dropdown-item" href="<?php echo site_url('Pdf');?>">Ejemplo Pdf</a>
                  
               </div>
            </li>      
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
               <i class="fas fa-fw fa-folder"></i>
               <span>CRUD Modulos</span>
               </a>
               <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                
                  <a class="dropdown-item" href="<?php echo site_url('CrudModulo');?>">Modulos</a>
                  
               </div>
            </li>
            
         </ul>

         <div id="content-wrapper">
            
               <div class="container-fluid">
                  <div class="">
                     <?php echo $contents; ?>
                  </div>
               </div>
            
            <!-- Sticky Footer -->
            <footer style='padding-top: 50px;'>
              <div id='mota' style='display:none' class='text-center'>
                 <img src="<?php echo site_url('public/images/mota.png');?>">

               </div>
                  <div class="copyright text-center my-auto">
                     <span>Solventa 2019</span>
                  </div>
            </footer>
         </div>
         <!-- /.content-wrapper -->
      </div>
      <!-- /#wrapper -->
      <!-- Scroll to Top Button-->
      <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
      </a>
      <!-- Logout Modal-->
      <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Formulario Embebido</h5>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                  </button>
               </div>
               <div class="modal-body">
                  aqui iría cualquier formulario
               </div>
            </div>
         </div>
      </div>

      <script src="<?php echo site_url('public/vendor/jquery/jquery.min.js');?>"></script>
      <!-- Bootstrap core JavaScript-->
      <script src="<?php echo site_url('public/vendor/bootstrap/js/bootstrap.bundle.min.js');?>"></script>
      <!-- Core plugin JavaScript-->
       <script src="<?php echo site_url('public/vendor/jquery-easing/jquery.easing.min.js');?>"></script> 
      
      <script src="<?php echo site_url('public/js/custom.js');?>"></script>
      <script src="<?php echo site_url('public/vendor/datatables/jquery.dataTables.js');?>"></script>
      <script src="<?php echo site_url('public/vendor/datatables/dataTables.bootstrap4.js');?>"></script> 
      <!-- bootbox-->
       <script src="<?php echo site_url('public/js/sb-admin.min.js');?>"></script> 
       <script src="<?php echo site_url('public/vendor/bootbox/bootbox.js');?>"></script> 

      <!-- Vuejs compiled javascript -->
      <script src="<?php echo site_url('public/js/app.js');?>"></script>

   </body>
</html>


<script type="text/javascript">
  
      buscarestilos();
        function buscarestilos(){
          
        
      var urll="<?php echo site_url(); ?>index.php/TemplateC/BuscarEstilo";

            $.ajax(
                {
                    type:"post",
                    url: urll,
                    data:{},
                    async : false,
                    dataType : 'json',
                    success:function(data)
                    {
                       if (data!='' ) {
                    $(data).each(function(i){
                      

                    var nombre_archivo=this.nombre_archivo;
                    var nombre=this.nombre;
                   var url ='<?php echo base_url(); ?>public/css/'+nombre_archivo;
                   
                    var vinculo = $('#stilo').attr('href',url);

                   if (nombre=='Navidad') {
                    $('#mota').show();
                    
                   }
                    
                    })
                    }else{
                   
                  }
                    }
                    
                }
            );
       


        }


</script>