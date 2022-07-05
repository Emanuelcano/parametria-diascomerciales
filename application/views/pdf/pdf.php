<div class="row contenido"  >
  <div class='col-md-6'> 
    <div class="card card-register ">
       <div class="card-header">Crear PDF</div>
       <div class="card-body">
         
          <form role="form" id="consultaPlastico" method="post" action="">
             <fieldset>
                <legend>Titulo</legend>
                <div class="form-group">
                   
                      <input type="text" name="titulo"id="titulo" required>
                 
                </div>
             </fieldset>
             <fieldset>
              <legend>Cuerpo</legend>
              <div class="form-group">
                 <textarea id='cuerpo' rows="10" cols="100"></textarea>
              </div>
           </fieldset>
           <a id='crear' class='btn btn-primary'>Crear Pdf</a>
          </form>
      </div>
    </div>
  </div> 
  <div class='col-md-2' id='divpdf' style='display:none'> 
    <div class="card card-register ">
     <div class="card-header">PDF creado</div>
     <div class="card-body">
       <a id='ver' class='' target="_blanck" style=''><img id='imagenpdf' src='public/images/pdf.png' style="width: 150px;" src="" >
          <br>  Ver Pdf
        </a>
    </div>
    </div>
  </div>
</div>




 <script type="text/javascript">
  $( "#crear" ).click(function(event){
            event.preventDefault();
		crearcdb();
  })

        function crearcdb(){
          
            titulo=$("#titulo").val();
            cuerpo=$("#cuerpo").val();

            if (titulo=='' || cuerpo=='') {
                alert('Los dos campos son obligatorios');

            }else{


    				var urll="<?php echo site_url(); ?>index.php/Pdf/crearpdf";

                $.ajax(
                    {
                        type:"post",
                        url: urll,
                        data:{titulo:titulo,cuerpo:cuerpo},
                        async : false,
                        dataType : 'html',
                        success:function(data)
                        {
                           if (data!='' ) {

                        $("#ver").attr('href', data);
                        $("#divpdf").show();
                        
                        $('#imagenpdf').toggle(3000, function() {
                          $('#imagenpdf').show();
                        });
                        
                      
                        }else{
                       
                      }
                        }
                        
                    }
                );
           
            }

        }


</script>