$(document).ready(function() {

    base_url = $("input#base_url").val();
    listaDiasComerciales();
 
    
  });
  

  function listaDiasComerciales() {

    base_url = $("input#base_url").val() + "parametria/diascomerciales/DiasComerciales/listaDiasComerciales";
   
    $.ajax({
      type: "POST",
      url: base_url,
    
      success: function (response) {
        
        $("#cuerpolistaDiasComerciales").html(response);
       TablaParametria('tp_listaDiasComerciales', 2, 'desc');

       
      }
    });

  }

  function TablaParametria(nombreTabla,colOrdenar,fOrdenar){
        var tabla = '#'+nombreTabla;
        var columnaOrdenar = colOrdenar;
        var formaOrdenar = fOrdenar;
    

        $(tabla).DataTable( {
                  "lengthMenu": [[5, 10, 15, 25, 50], [5, 10, 15, 25, 50]],
                  "language": {
                      "sProcessing":     "Procesando...",
                      "sLengthMenu":     "Mostrar&nbsp;&nbsp;&nbsp;&nbsp _MENU_ &nbsp;&nbsp;&nbsp;&nbsp;registros",
                      "sZeroRecords":    "No se encontraron resultados",
                      "sEmptyTable":     "Ningún dato disponible en esta tabla",
                      "sInfo":           "Del _START_ al _END_ de un total de _TOTAL_ reg.",
                      "sInfoEmpty":      "0 registros",
                      "sInfoFiltered":   "(filtrado de _MAX_ reg.)",
                      "sInfoPostFix":    "",
                      "sSearch":         "Buscar:&nbsp;&nbsp;&nbsp",
                      "sUrl":            "",
                      "sInfoThousands":  ",",
                      "sLoadingRecords": "Cargando...",
                      "oPaginate": {
                          "sFirst":    "Primero",
                          "sLast":     "Último",
                          "sNext":     "Sig",
                          "sPrevious": "Ant"
                      },
                      "oAria": {
                          "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                      }
                  },
                  "aaSorting": [[columnaOrdenar,formaOrdenar]]
          }
          );
    
} 



  function crearDiaComercial() {
  


    var fecha = $("#fecha").val();
    var descripcion = $("#descripcion").val();

    const formData = new FormData();
          formData.append("fecha", fecha);
          formData.append("descripcion", descripcion);

     base_url = $("input#base_url").val() + "parametria/diascomerciales/DiasComerciales/crearDiacomercial";

          $.ajax({
            type: "POST",
            url: base_url,
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {
              
              var mydata = JSON.parse(response);
              var mensajeExito = mydata.data;
              var mensajeError = mydata.errors;

              if  ( ('data' in mydata) ){

                alert(mensajeExito);
                listaDiasComerciales();

              }
              else{
                alert(mensajeError);
                
              }
            }
            
          });
}



function nuevoDiaComercial() {


  base_url = $("input#base_url").val() + "parametria/diascomerciales/DiasComerciales/nuevoDiaComercial";

  $.ajax({
    type: "POST",
    url: base_url,

    success: function (response) {
      
      $("#cuerpolistaDiasComerciales").html(response);
      

    }
  }); 
}

function registrarDiaComercial(){

    var fecha = document.getElementById("fecha").value;
    var descripcion = $("#descripcion").val();
    
      const formData = new FormData();
      formData.append("fecha", fecha);
      formData.append("descripcion",descripcion);

      base_url = $("input#base_url").val() + "parametria/diascomerciales/DiasComerciales/registrarDiaComercial";

      $.ajax({
        type: "POST",
        url: base_url,
        data: formData,
        processData: false,
        contentType: false,

        success: function (response) {

                          var mydata = JSON.parse(response);
                          var mensajeExito = mydata.data;
                          var mensajeError = mydata.errors;

                          if  ( ('data' in mydata) ){

                            alert(mensajeExito);
                            listaDiasComerciales();

                          }
                          else{
                            alert(mensajeError);
                            
          }
          
         
        }
      });
}

function cargarDiaComercial (id, accion,fecha) {


  base_url = $("input#base_url").val() + "parametria/diascomerciales/DiasComerciales/cargarDiaComercial";
  const formData = new FormData();

  formData.append("id", id);
  formData.append('fecha', fecha);

  
  $.ajax({
    type: "POST",
    url: base_url,
    data: formData,
    processData: false,
    contentType: false,

    success: function (response) {
      
      $("#cuerpolistaDiasComerciales").html(response);

      if (accion == 'ver') {
            $('input').prop('readonly',true);
            $('button#btnRegistrarDia').css('display','none');  
            $('button#btnActualizarDia').css('display','block');

      }else{
            $('button#btnRegistrarDia').css('display','none');
            $('button#btnActualizarDia').css('display','block');
            $('button#btnActualizarDia').prop('disabled',false);
      }
    }
  });

}

function actualizarDiaComercial () {
  let id = document.getElementById("id").value;
  var fecha = document.getElementById("fecha").value;
  var descripcion = $("#descripcion").val();
  
  Swal.fire({
    title: 'Actualizacion',
    text: 'Estas seguro que quieres actualizar Parentesco ?',
    icon: 'warning',
    confirmButtonText: 'Aceptar',
    cancelButtonText: 'Cancelar',
    showCancelButton: 'true'
  }).then((result) => {
    if (result.value) {

        const formData = new FormData();
        formData.append("fecha", fecha);
        formData.append("descripcion",descripcion);
        formData.append("id", id);

  
        base_url = $("input#base_url").val() + "parametria/diascomerciales/DiasComerciales/actualizarDiaComercial";

        $.ajax({
          type: "POST",
          url: base_url,
          data: formData,
          processData: false,
          contentType: false,

          success: function (response) {
            
            listaDiasComerciales();

          }
        });      
    }
  });
}
