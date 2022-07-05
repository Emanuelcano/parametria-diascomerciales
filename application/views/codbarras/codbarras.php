<div class="row contenido"  >
  <div class='col-md-2'> 
    <div class="card card-register ">
       <div class="card-header">Crear Codigo de barra</div>
       <div class="card-body">
         
          <form role="form" id="consultaPlastico" method="post" action="">
             <fieldset>
                <legend>Producto</legend>
                <div class="form-group">
                   
                      <input type="text" name="Producto"id="Producto" required>
                 
                </div>
             </fieldset>
             
           <a id='crear' class='btn btn-primary'>Crear Cd. Barra</a>
          </form>
      </div>
    </div>
  </div> 
  <div class='col-md-2' id='divpdf' style='display:none'> 
    <div class="card card-register ">
     <div class="card-header">Codigo creado</div>
     <div class="card-body">
        <img id='imagen' style="width: 150px;" src="" >
        <div id='nombreimg'></div>
    </div>
    </div>
  </div>
</div>

 
<br>



 <script type="text/javascript">
  $( "#crear" ).click(function(event){
            event.preventDefault();
		crearcdb();
  })

        function crearcdb(){
          
            Producto=$("#Producto").val();
		    var urll="<?php echo site_url(); ?>index.php/CodBarras/generarCodBarrasPng";

            $.ajax(
                {
                    type:"post",
                    url: urll,
                    data:{Producto:Producto},
                    async : false,
                    dataType : 'html',
                    success:function(data)
                    {
                       if (data!='' ) {

                    $("#imagen").attr('src', data);
                    $("#nombreimg").text(Producto);
                    $("#divpdf").show();
                    $('#imagen').show(3000);
                    $("#Producto").val('');
                    
                  
                    }else{
                   
                  }
                    }
                    
                }
            );
       


        }


</script>