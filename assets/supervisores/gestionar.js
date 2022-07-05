
/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Para modulo de Supervisor de cobranzas/ sub. Mod. Generacion Campañas Gestionar.js archivo de controlador secundario para las acciones de el modulo de supervisor de cobranzas  Ing. Esthiven Garcia
|
| Controlador de sugundo nivel para las operaciones en ajax del los metodos contenidos del modulo de supervision de cobranza
| tales como las acciones de buscar una campaña, migrar datos masivos a wolkvox, construir excel, borrado masivo, etc.
| 
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/


/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Area de Ready Dome Ing. Esthiven Garcia
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/

$( document ).ready(function() {
    
/*BUSCO LOS BOTONES TIPOFICADOS EN LAS GESTIONES DE LOS OPERADORES BASE->GESTION TABLA->botones_operador*/
    
let base_url = $("#base_url").val();
    

     $.ajax({
        url:  base_url+'api/ApiSupervisores/BuscarBotonesOperador',
        type: 'POST',
        success:function(respuesta){
            //alert(respuesta);
            var registros = eval(respuesta);
            
            html ="";

            for (var i = 0; i < registros.length; i++) {
                
                html +="<option value='"+registros[i]['id']+"''>"+registros[i]['id']+".- "+registros[i]['etiqueta']+"</option>";
                
                
                
            }
            
            
            $('#exclusiones').html(html);
          
        }
    });




});

/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Area de Inicialización de componentes Ing. Esthiven Garcia
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/
$('.moneda').autoNumeric('init', {aSep: '.', aDec: ',', aSign: ''});
//$.fn.select2.defaults.set("theme", "bootstrap");
//$('.js-example-basic-multiple').select2();
 $('#sl_criterios').select2({
    placeholder: '.: Selecciona los criterios :.',
    multiple : true
 });

 $('#sl_equipo').select2({
    placeholder: '.: Selecciona los operadores :.',
    multiple : true
 });

 $('#sl_operadores').select2({
    placeholder: '.: Selecciona los operadores :.',
    multiple : true
 });

 $('#personal').select2({
    placeholder: '.: Selecciona los telefonos de contacto :.',
    multiple : true
 });

 $('#sl_ultimagestion').select2({
    placeholder: '.: Selecciona ultima gestion :.',
    multiple : true
 });

$('#exclusiones').select2({
    placeholder: '.: Excluya gestion :.',
    multiple : true
 });

$('#sl_condicion').select2({
    placeholder: '.: Condicion solicitud :.',
    multiple : true
 });


/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Area de Control de Elementos HTML5  Ing. Esthiven Garcia
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/

$("#sl_tipo_campaing").change(function() {
    $tipo_campaing= $("#sl_tipo_campaing").val();
    if ($tipo_campaing=="PREVIEW") 
      {
        
       $("#div_distribution").removeClass("hide");

      }else{
       $("#div_distribution").addClass("hide"); 

      }

});


$("#sl_central").change(function() {
    buscarCampanias();

});

$("#sl_equipo").change(function() {
   //debugger;
   var equipo  = $('#sl_equipo').val();
   let central = $('#sl_central').val();
   //console.log(equipo);
   let primera = equipo.filter(number => number === 1);
   let segunda = equipo.filter(number => number === 4);
   let tercera = equipo.filter(number => number === 6);
   let cuarta = equipo.filter(number => number === 5);
   
   if (central==0) {
    swal("Atención","No selecciono una central para filtras sus operadores..","error");
    $('#sl_central').focus();
   }else{
    consultaOperadoresActivos(equipo);

   }

});


$("#testbutton").click(function() {
 alert($("#date_range").val());  

});

$("#sl_logica").change(function() {

 

});

$("#sl_criterios").change(function() {

var criterios  = $('#sl_criterios').val();
let primera = criterios.filter(number => number === "conAcuerdo");
let segunda = criterios.filter(number => number === "sinAcuerdo");
let tercera = criterios.filter(number => number === "noContactado");

    if (primera!="" && segunda!="") {
        //console.log("tengo "+primera+" y "+segunda)
        swal("Atención","Los criterios sin acuerdo y con acuerdo no se pueden usar como criterios razonables para la busqueda elija solo uno de ellos","error");
        return false;

    $("#sl_estado").addClass('hide');

    }else if (primera=="conAcuerdo"){

    $("#sl_estado").removeClass('hide');

    }else if (tercera=="noContactado"){

    $("#sl_estado").removeClass('hide');

    }else{
    $("#sl_estado").addClass('hide');

    }



});

$("#sl_logica").change(function() {


 if ($("#sl_antiguedad").val()==="c.fecha_vencimiento"){

    if ($("#sl_logica").val()=="MAYOR_A"){
     //console.log($("#sl_antiguedad").val()+" "+$("#sl_logica").val());
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="IGUAL_A"){
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');


    }else if($("#sl_logica").val()=="MENOR_A"){
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="DISTINTO_A"){
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="ENTRE"){
    $("#date_rangeA, #date_rangeB").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }

}else if ($("#sl_antiguedad").val()==="c.monto_cobrar"){

    if ($("#sl_logica").val()=="MAYOR_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="IGUAL_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="MENOR_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="DISTINTO_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="ENTRE"){
    $("#currency_rangeA, #currency_rangeB").removeClass('hide');
    $("#date_rangeA, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }
}else if ($("#sl_antiguedad").val()==="c.dias_atraso"){

    if ($("#sl_logica").val()=="MAYOR_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

     }else if($("#sl_logica").val()=="IGUAL_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="MENOR_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="DISTINTO_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="ENTRE"){
    $("#dias_atrasoA, #dias_atrasoB").removeClass('hide');
    $("#date_rangeA, #date_rangeB, #currency_rangeB, #currency_rangeA").addClass('hide');
    }

}

});

/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Area de Definicion de Funciones Ing. Esthiven Garcia
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/

var paginacion = 1;
function solicitudeUpdate(idSolicitude, id_operador, type_contact=0, message ="", fecha_alta)
{
    $.ajax({
        url: base_url+'api/solicitud/actualizar',
        type: 'POST',
        dataType: 'json',
        data: {"id_solicitud": idSolicitude,"estado": message,"id_operador":id_operador,"action":message},
    })
    .done(function(response) {
        if(response.status.ok)
        {
            var estado = response.solicitud[0].estado;
            $("#box_botones_gestion #analysis_buro").val(response.solicitud[0].respuesta_analisis);
            $("#box_botones_gestion #solicitud_status").val(estado);
            let gestion = "["+message+"]";
            saveTrack(gestion, type_contact, idSolicitude, id_operador);
            let analysis_buro = $("#box_botones_gestion #analysis_buro").val();
            let solicitud_status = $("#box_botones_gestion #solicitud_status").val();
            button_status(analysis_buro, solicitud_status);
            //Muestro el nuevo estado
            if (estado == 'VISADO' || estado == 'RECHAZADO' || estado == 'ESCALADO ANALIZADO'){
                var row = document.getElementById('icono');
                if(row){
                    row.parentElement.parentElement.remove(row); 
                }                
            }
            if (estado == 'VERIFICADO')
            {
                $("#nombre_estado").html('<i style="font-size: 20px; margin-right: 8px; color: orange" class="fa fa-eye">&nbsp;<label style="font-family: arial;">'+estado+'</label></i>');
                toastr["info"]("VERIFICADO", "ESTADO:");
            } 
            else if (estado == 'VALIDADO')
            {
                $("#nombre_estado").html('<i style="font-size: 20px; margin-right: 8px; color: brown" class="fa fa-check-square-o">&nbsp;<label style="font-family: arial;">'+estado+'</label></i>');
                 toastr["info"]("VALIDADO", "ESTADO:");
            }
            else if (estado == 'APROBADO')
            {
                 $("#nombre_estado").html('<i style="font-size: 20px; margin-right: 8px; color: green" class="fa fa-check">&nbsp;<label style="font-family: arial;">'+estado+'</label></i>');
                 toastr["success"]("APROBADO", "ESTADO:");
            } 
            else if (estado == 'VISADO')
            {
                toastr["info"]("VISADO", "ESTADO:");
            } 
            else if (estado == 'RECHAZADO')
            {
                $("#nombre_estado").html('<i style="font-size: 20px; margin-right: 8px; color: red" class="fa fa-times-circle">&nbsp;<label style="font-family: arial;">'+estado+'</label></i>');
                toastr["error"]("RECHAZADO", "ESTADO:");
            }else if (estado == 'ESCALADO ANALIZADO')
            {                
                toastr["info"]("ESCALADO ANALIZADO", "ESTADO:");
                $('#analizado').prop('disabled', true);
            } 
        }
    })
    .fail(function(response) {
        if(message == 'RECHAZADO')
        {           
            if(toastr["error"]("Por favor ingrese un comentario de tipo RECHAZO", "NO SE RECHAZO")){
                $('#rejected').prop('disabled', false);
            }
            window.location.href = '#box_tracker';            
        }
    })
    .always(function() {

    });
}


function validaciones(button){

    var sl_central  = $('#sl_central').val();
    var sl_campania  = $('#sl_campania').val();
    var sl_tipo_solicitud  = $('#sl_tipo_solicitud').val();
    var sl_antiguedad  = $('#sl_antiguedad').val();
    var sl_logica  = $('#sl_logica').val();
    var currency_rangeA  = $('#currency_rangeA').val();
    var currency_rangeB  = $('#currency_rangeB').val();
    var date_rangeA  = $('#date_rangeA').val();
    var date_rangeB  = $('#date_rangeB').val();
    var dias_atrasoA  = $('#dias_atrasoA').val();
    var dias_atrasoB  = $('#dias_atrasoB').val();
    var sl_limite  = $('#sl_limite').val();
 


    /*if (primera!="" && segunda!="") {
    swal("Atención","Los criterios sin acuerdo y con acuerdo no se pueden usar como criterios razonables para la busqueda elija solo uno de ellos","error");
    return false;

    }else if (sl_central==0 && sl_campania==0 && sl_antiguedad==0 && sl_logica==0) {
    
    swal("Atención","Los criterios de busqueda requieren los campos de: central,campañia,criterios,antiguedad, logica y almenos un rango uno de ellos","error");
    return false;
    

    }else{*/



        if (button=="btn_search") {
        buscarCampania();

        }else if(button=="btn_comunicacion"){
        migrarCampania();

        }else if(button=="btn_csv"){
        generaCsv();


        }else if(button=="btn_plantilla"){

        generaTabModal();

        }else if(button=="btn_clear_campania"){

        clearcampania();
            
        }else{
            return false;

        }

    //}
}


/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Buscar Campañas para modulo de Supervisor de cobranzas/ sub. Mod. Generacion Campañas  Ing. Esthiven Garcia
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/


function buscarCampania(){

let base_url = $("#base_url").val();

  var $btn = $('#btn');
  var $data = $('.data');
  var $loader = $('.loader');

 $.ajax({
     dataType: "JSON",
     data:$('#form_search').serialize(),
     url:   base_url+'api/ApiSupervisores/BuscarDatosConstruidos',
     type: 'POST',
     beforeSend: function(request) {
      
      request.setRequestHeader("Authorization", "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEiLCJpYXQiOjE1ODk0MTUyMjQsImV4cCI6MTU4OTQxODgyNCwiaG9zdG5hbWUiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODA4NVwvQmFja2VkbkNvbG9tYmlhXC8iLCJhcGkiOiJBcGlfZGF0YVRyYW5zZmVyXC9BcGlTZWFyY2hDYW1wYW5pYSIsImNyZWF0ZWQiOiIyMDIwLTA1LTEzIDE5OjA1OjQ0IiwibW9kaWZpZWQiOiIyMDIwLTA1LTEzIDIwOjA1OjQ0IiwidGltZSI6MTU4OTQxNTIyNH0.DH0b1bSd25ubdrb6ANET7HTclWy1aFGUKdJ1y2k2Z4A");
      //$("#compose-modal-wait").modal('show');
      $("#compose-modal-wait").modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });

      $loader.show();
     }
    }).done(function(respuesta){


      setTimeout(function(){

       $loader.hide();
       $("#compose-modal-wait").modal('hide');
       
      }, 1000);

     
      if (respuesta==="ERROR EN TRANSFERENCIA") {
                
                swal.fire("Error","Ocurrio un error mientras se realizaba la busqueda!"+respuesta,"error");
                

      }else if (respuesta!="") {
          //$("#body_campanias").html(respuesta);
          $("#tp_campanias").dataTable().api().clear().draw();

          $("#tp_campanias").dataTable().api().rows.add(respuesta.data); // Add new data
          $("#tp_campanias").dataTable().api().columns.adjust().draw(); // Redraw the DataTable
      

      }



    }).fail(function(xhr,err){
      $loader.hide();
      Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
    })
    return;
   

}

/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Vaciado de Campañas manera masiva en wolkvox para modulo de Supervisor de cobranzas/ sub. Mod. Generacion Campañas  Ing. Esthiven Garcia
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/
function clearcampania(){

    //console.log($("#sl_antiguedad").val());

let base_url = $("#base_url").val();

  var $btn = $('#btn');
  var $data = $('.data');
  var $loader = $('.loader');
  var sl_campania = $("#sl_central ").val();

if (sl_campania!=0){

$.ajax({
      dataType: "JSON",
      data:$('#form_search').serialize(),
      url:   base_url+'api/ApiSupervisores/ClearCampanias',
      type: 'POST',
     beforeSend: function() {
      // aquí puedes poner el código paraque te muestre el gif

      $("#compose-modal-wait").modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });

      $loader.show();
     }
    }).done(function(respuesta){


      setTimeout(function(){

       $loader.hide();
       $("#compose-modal-wait").modal('hide');
       
      }, 1000);


      if (respuesta==="ERROR EN TRANSFERENCIA") {
                
                Swal.fire("Error","Ocurrio un error mientras se generaba el archivo csv!"+respuesta,"error");
                

            }else if (respuesta!="") {
                
                Swal.fire("Info","data limpiada con exito de la campaña!","success");

            

            }



    }).fail(function(xhr,err){
      $loader.hide();
      Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
    })
    return;
  }else{
    Swal.fire("Info","Error debe seleccional primero una campaña a limpiar!","error");
  }
 
}

/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Migracion de datos masivos para Campaña en wolkvox para modulo de Supervisor de cobranzas/ sub. Mod. Generacion Campañas  Ing. Esthiven Garcia
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/

function migrarCampania(){

    //console.log($("#sl_antiguedad").val());

let base_url = $("#base_url").val();

  var $btn = $('#btn');
  var $data = $('.data');
  var $loader = $('.loader');

 $.ajax({
      dataType: "JSON",
      data:$('#form_search').serialize(),
      url:   base_url+'api/ApiSupervisores/GenerarTransmasiva',
      type: 'POST',
     beforeSend: function() {
      // aquí puedes poner el código paraque te muestre el gif

      $("#compose-modal-wait").modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });

      $loader.show();
     }
    }).done(function(respuesta){

      //alert(respuesta);
      setTimeout(function(){

       $loader.hide();
       $("#compose-modal-wait").modal('hide');
       
      }, 1000);


      if (respuesta==="ERROR EN TRANSFERENCIA") {
                
                Swal.fire("Error","Ocurrio un error mientras se generaba el archivo csv!"+respuesta,"error");
                

            }else if (respuesta!="") {
                
                Swal.fire("Info","data migrada con exito a la central!","success");

            

            }



    }).fail(function(xhr,err){
      $loader.hide();
      Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
    })
    return;
   

}

/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Generación de Archivo CSV de Campaña en wolkvox para modulo de Supervisor de cobranzas/ sub. Mod. Generacion Campañas  Ing. Esthiven Garcia
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/


function generaCsv(){


    let base_url = $("#base_url").val();

var $btn = $('#btn');
var $data = $('.data');
var $loader = $('.loader');


    Swal.fire({

                  title: 'Desea definir esta logica como una plantilla para proximos usos?',
                  type: 'warning',
                  html: '<div class="row"><div class="col-md-12"><p><label class="control-label">Nombre plantilla<star>*</star></label><input type="text" name="input-field" id="input-field"  class="form-control" placeholder="nombre plantilla..."/></p></div><div class="col-md-12"><p><label class="control-label">Descripcion <star>*</star></label></p><textarea rows="4" cols="50" name="input-field2" id="input-field2"  class="form-control" placeholder="descripcion plantilla..."></textarea></div></div>',
                  showCloseButton: true,
                  focusConfirm: false,
                  showCancelButton: true,
                  allowOutsideClick: false,
                  customClass: 'Swalfire2-overflow',
                  
               })
                .then((result) => {

                var name= $('#input-field').val();
                var descripcion= $('#input-field2').val();

                  if (result.value) {
                          if (name=="") {
                          Swal.fire("Verifique!", "Debe indicar una Fecha Desde para emitir el reporte", "error");
                          return false;
                          }else  if (descripcion=="") {
                          Swal.fire("Verifique!", "Debe indicar una Fecha Hasta para emitir el reporte", "error");
                          return false;
                          }else{


                                     $.ajax({
                                          data:$('#form_search').serialize()+"&name="+name+"&descripcion="+descripcion,
                                          url:   base_url+'api/ApiSupervisores/GenerarCsv',
                                          type: 'POST',
                                         beforeSend: function() {
                                          // aquí puedes poner el código paraque te muestre el gif

                                          $("#compose-modal-wait").modal({
                                                show: true,
                                                backdrop: 'static',
                                                keyboard: false
                                            });

                                          $loader.show();
                                         }
                                        }).done(function(respuesta){


                                          setTimeout(function(){

                                           $loader.hide();
                                           $("#compose-modal-wait").modal('hide');
                                           
                                          }, 1000);


                                          //swal.fire("info",respuesta,"info");
                                           if (respuesta==="ERROR EN TRANSFERENCIA") {
                                                    
                                                    swal.fire("Error","Ocurrio un error mientras se generaba el archivo csv!"+respuesta,"error");
                                                    

                                            }else{
                                                let url = base_url+"/"+respuesta;
                                                window.open(url, '_self');

                                            }



                                        }).fail(function(xhr,err){
                                          $loader.hide();
                                          Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
                                        })
                                        return;

                          
                             
                          return false;
                          }
                  } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                  ) {
                    Swal.fire(
                      'Cancelado',
                      'Definicion de plantilla cancelada se generara el csv!',
                      'warning'
                    );


                          $.ajax({
                              data:$('#form_search').serialize(),
                              url:   base_url+'api/ApiSupervisores/GenerarCsv',
                              type: 'POST',
                             beforeSend: function() {
                              // aquí puedes poner el código paraque te muestre el gif

                              $("#compose-modal-wait").modal('show');

                              $loader.show();
                             }
                            }).done(function(respuesta){


                              setTimeout(function(){

                               $loader.hide();
                               $("#compose-modal-wait").modal('hide');
                               
                              }, 1000);


                               if (respuesta==="ERROR EN TRANSFERENCIA") {
                                        
                                        swal.fire("Error","Ocurrio un error mientras se generaba el archivo csv!"+respuesta,"error");
                                        

                                    }else if (respuesta!="") {
                                        let url = base_url+"/"+respuesta;
                                        window.open(url, '_self');

                                    }



                            }).fail(function(xhr,err){
                              $loader.hide();
                              Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
                            })
                            return;


                  }
                })
}









function antiguedadActivationButtons(antiguedad,logica){


    if (antiguedad==="c.fecha_vencimiento"){

    if (logica=="MAYOR_A"){
     
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if(logica=="IGUAL_A"){
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');


    }else if(logica=="MENOR_A"){
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if(logica=="DISTINTO_A"){
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if(logica=="ENTRE"){
    $("#date_rangeA, #date_rangeB").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }

}else if (antiguedad==="c.monto_cobrar"){

    if (logica=="MAYOR_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if(logica=="IGUAL_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if(logica=="MENOR_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if(logica=="DISTINTO_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if(logica=="ENTRE"){
    $("#currency_rangeA, #currency_rangeB").removeClass('hide');
    $("#date_rangeA, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }
}else if (antiguedad==="c.dias_atraso"){

    console.log($("#sl_logica").val());

    if (logica=="MAYOR_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

     }else if(logica=="IGUAL_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

    }else if(logica=="MENOR_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

    }else if(logica=="DISTINTO_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

    }else if(logica=="ENTRE"){
    $("#dias_atrasoA, #dias_atrasoB").removeClass('hide');
    $("#date_rangeA, #date_rangeB, #currency_rangeB, #currency_rangeA").addClass('hide');
    }else{
    
    $("#dias_atrasoA, #dias_atrasoB,#date_rangeA, #date_rangeB, #currency_rangeB, #currency_rangeA").addClass('hide');
    }



}

}


$("#sl_antiguedad").change(function() {
    //console.log($("#sl_antiguedad").val()+" "+$("#sl_logica").val());

 if ($("#sl_antiguedad").val()==="c.fecha_vencimiento"){

    if ($("#sl_logica").val()=="MAYOR_A"){
     //console.log($("#sl_antiguedad").val()+" "+$("#sl_logica").val());
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="IGUAL_A"){
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');


    }else if($("#sl_logica").val()=="MENOR_A"){
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="DISTINTO_A"){
    $("#date_rangeA").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="ENTRE"){
    $("#date_rangeA, #date_rangeB").removeClass('hide');
    $("#currency_rangeA, #currency_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }

}else if ($("#sl_antiguedad").val()==="c.monto_cobrar"){

    if ($("#sl_logica").val()=="MAYOR_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="IGUAL_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="MENOR_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="DISTINTO_A"){
    $("#currency_rangeA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="ENTRE"){
    $("#currency_rangeA, #currency_rangeB").removeClass('hide');
    $("#date_rangeA, #date_rangeB, #dias_atrasoA, #dias_atrasoB").addClass('hide');

    }
}else if ($("#sl_antiguedad").val()==="c.dias_atraso"){

    if ($("#sl_logica").val()=="MAYOR_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

     }else if($("#sl_logica").val()=="IGUAL_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="MENOR_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="DISTINTO_A"){
    $("#dias_atrasoA").removeClass('hide');
    $("#date_rangeA, #currency_rangeB, #date_rangeB, #currency_rangeA, #dias_atrasoB").addClass('hide');

    }else if($("#sl_logica").val()=="ENTRE"){
    $("#dias_atrasoA, #dias_atrasoB").removeClass('hide');
    $("#date_rangeA, #date_rangeB, #currency_rangeB, #currency_rangeA").addClass('hide');
    }

}

});




function buscarCampanias(){
//console.log(base_url+'ApiBuscaCampanias');
let base_url = $("#base_url").val();
    $sl_central = $("#sl_central").val();

    $.ajax({
        dataType: "json",
        data: {"buscar": $sl_central},
        url:   base_url+'ApiBuscaCampanias',
        type: 'POST',
        beforeSend: function(){
            //Lo que se hace antes de enviar el formulario
            },
        success: function(respuesta){
            //lo que se si el destino devuelve algo
                //console.log(respuesta);
                 var registros = eval(respuesta);

                 html='<option value="0">- primero seleccion una Central -</option>';
            for (var i = 0; i < registros.length; i++) {
                html +='<option value="'+registros[i]['id_campania']+'">'+registros[i]['id_campania']+' - '+registros[i]['nombre']+'</option>';
                
            }

                $("#sl_campania").html(html);
                
                
        },
        error:  function(xhr,err){
            alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText);
        }
    });

    $.ajax({
        dataType: "json",
        data: {"buscar": $sl_central},
        url:   base_url+'ApiBuscaCriterios',
        type: 'POST',
        beforeSend: function(){
            //Lo que se hace antes de enviar el formulario
            },
        success: function(response){
            //lo que se si el destino devuelve algo
                //console.log(response);
                 var regis = eval(response);

                 html='<option value="0">- primero seleccion una Central -</option>';
            for (var i = 0; i < regis.length; i++) {
                html +='<option value="'+regis[i]['endpoint']+'">'+regis[i]['central']+' - '+regis[i]['criterio_name']+'</option>';
                
            }

                $("#sl_criterios").html(html);
                //$("#sl_campania").selectpicker('refresh');
        },
        error:  function(xhr,err){
            alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText);
        }
    });



    
}


function consultaOperadoresActivos(equipos=null) {

let base_url = $("#base_url").val();
    $sl_central = $("#sl_central").val();
    
    //console.log($sl_central,$sl_equipos);
    
  $.ajax({
        dataType: "JSON",
        data: {"buscar": $sl_central,"sl_equipos": equipos},
        url:   base_url+'ApiBuscaOperadores',
        type: 'POST',
        beforeSend: function(){
            //Lo que se hace antes de enviar el formulario
            },
        success: function(response){
            //lo que se si el destino devuelve algo
                //console.log(response);
                 var regis = eval(response);

                  html2='';
            for (var i = 0; i < regis.length; i++) {
                html2 +='<option value="'+regis[i]['id_agente']+'" data-id_operador="'+regis[i]['id_operador']+'" data-tipo_operador="'+regis[i]['tipo_operador']+'" data-operador="'+regis[i]['estado']+'" >'+regis[i]['id_agente']+' - '+regis[i]['nombre_apellido']+'</option>';
                
            }

                $("#sl_operadores").html(html2);
                //$("#sl_campania").selectpicker('refresh');
        },
        error:  function(xhr,err){
            alert("readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText);
        }
    });
}

/*
|-------------------------------------------------------------------------------------------------------------------------------------------------
| Control de modal para plantillas de campañas para modulo de Supervisor de cobranzas/ sub. Mod. Generacion Campañas  Ing. Esthiven Garcia
|-------------------------------------------------------------------------------------------------------------------------------------------------
*/
function generaTabModal(){
$('#compose-modal-plantillas').modal('show'); 

    let base_url = $("#base_url").val();
    var valor = $("#sl_central").val();

    $.ajax({
        url:   base_url+'ApiBuscaPlantillas',
        type:'POST',
        data:{buscar:valor},
        success:function(respuesta){
            //alert(respuesta);
            var registros = eval(respuesta);
            html ='<input type="hidden" id="rowidMedida"/><input type="hidden" id="rowcantMedida"/>';
            html +="<table id='Res_plantillas_table' class='table table-responsive table-bordered'><thead>";
            html +="<tr>";
            html +="<th>#</th>";
            html +="<th> Nombre </th>";
            html +="<th> Descripcion </th>";
            html +="<th> Campañas </th>";
            html +="<th> Criterio </th>";
            html +="<th> Antiguedad </th>";
            html +="<th> Logica </th>";
            html +="<th> Limite </th>";
            html +="<th> dias atrasoA </th>";
            html +="<th> dias atrasoB </th>";
            html +="<th> Monto A </th>";
            html +="<th> Monto B </th>";
            html +="<th> Fecha A </th>";
            html +="<th> Fecha B </th>";
            html +="<th> Estado </th>";
            

            html +="<th>Accion</th>";
            html +="</tr>";
            html +="</thead><tbody>";
            
            for (var i = 0; i < registros.length; i++) {
                
                if (registros[i]['bEstatus']==0) {
                    $btclass="btn-danger";
                    $clase="fa fa-circle";
                }else{
                    $btclass="btn-success";
                    $clase="fa fa-check-circle";
                    

                }

                html +="<tr>";
                html +="<td>"+registros[i]['id']+"</td>";
                html +="<td>"+registros[i]['plantilla_name']+"</td>";
                html +="<td>"+registros[i]['descripcion_plantilla']+"</td>",
                html +="<td>"+registros[i]['sl_campania']+"</td>";
                html +="<td>"+registros[i]['criterios']+"</td>";
                html +="<td>"+registros[i]['sl_antiguedad']+"</td>";
                html +="<td>"+registros[i]['sl_logica']+"</td>";
                html +="<td>"+registros[i]['sl_limite']+"</td>";
                html +="<td>"+registros[i]['dias_atrasoA']+"</td>";
                html +="<td>"+registros[i]['dias_atrasoB']+"</td>";
                html +="<td>"+registros[i]['currency_rangeA']+"</td>";
                html +="<td>"+registros[i]['currency_rangeB']+"</td>";
                html +="<td>"+registros[i]['date_rangeA']+"</td>";
                html +="<td>"+registros[i]['date_rangeB']+"</td>";
                html +="<td>"+registros[i]['sl_estado']+"</td>";
                html +="<td><button id='btneliminar_pro' class='btn btn-danger btn-simple btn-xs' rel='tooltip' idinsumo='"+registros[i]['id']+"' type='button' value='"+registros[i]['id']+"'><i class='fa fa-trash'></i></button></td>";
                html +="</tr>";
                
            }
            
            html +="</tbody></table>";
            $('#Res_plantillas_list').html(html);


                        var table = $('#Res_plantillas_table').DataTable({

                            "keys": true,
                            "select":true,
                            language: {
                                        "sProcessing":     "Procesando...",
                                        "sLengthMenu":     "Mostrar _MENU_ registros",
                                        "sZeroRecords":    "No se encontraron resultados",
                                        "sEmptyTable":     "Ningún dato disponible en esta tabla",
                                        "sInfo":           "Del _START_ al _END_ de un total de _TOTAL_ reg.",
                                        "sInfoEmpty":      "0 registros",
                                        "sInfoFiltered":   "(filtrado de _MAX_ reg.)",
                                        "sInfoPostFix":    "",
                                        "sSearch":         "Buscar:",
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
                            
                         
                            
                            keys: {
                               keys: [ 13 /* ENTER */, 38 /* UP */, 40 /* DOWN */ ]
                            }
                        });


                        table.on( 'key', function (e, datatable, key, cell, originalEvent) {

                            if(key === 13){
                                var data = table.row(cell.index().row).data();
                                
                                $("#sl_campania").val(data[3]).trigger('change.select2');
                                

                                var Values = new Array();
                                Values.push(data[4]);
                                                                
                                    data_replace = data[4].replace(/"/g, "");
                                var data_replace = data_replace.replace("[", "");
                                    data_replace = data_replace.replace("]", "");
                                    
                                    

                                var res = data_replace.split(",");
                                


                                $("#sl_criterios").val(res).trigger('change.select2');
                                //$("#sl_criterios").trigger('change.select2');
                                //$("#sl_criterios").attr('val', data[4]).trigger('change.select2');//val(data[4]).trigger('change.select2');
                                $("#sl_antiguedad").val(data[5]).trigger('change.select2');
                                //console.log(data);
                                //console.log(data[5]);
                                //sl_antiguedad,dias_atrasoA,dias_atrasoB,currency_rangeA,currency_rangeB,date_rangeA,date_rangeB
                                antiguedadActivationButtons(data[5],data[6]);
                                $("#sl_logica").val(data[6]).trigger('change.select2');
                                $("#sl_limite").val(data[7]).trigger('change.select2');
                                $("#dias_atrasoA").val(data[8]).removeClass('hide');

                                $("#dias_atrasoB").val(data[9]);
                                $("#currency_rangeA").val(data[10]);
                                $("#currency_rangeB").val(data[11]);
                                if (data[12]!="0000-00-00"){ $("#date_rangeA").val(data[12]);}else{$("#date_rangeA").val('');}
                                if (data[13]!="0000-00-00"){ $("#date_rangeB").val(data[13]);}else{$("#date_rangeB").val('');}
                                $("#sl_estado").val(data[14]).removeClass('hide');
                                buscarCampania();
                                

                                $("#compose-modal-plantillas").modal('hide');        
                                
                            }
                        } ).on( 'key-focus', function (e, datatable, cell) {
                               $(table.row(cell.index().row).node()).addClass('selected');
                        } ).on( 'key-blur', function (e, datatable, cell) {
                             $(table.row(cell.index().row).node()).removeClass('selected');
                        } );





                        

            
        }
    });
}

function solicitudeUpdateStep(idSolicitude, id_operador, step, type_contact=0, message ="")
{
    $.ajax({
        url: base_url+'api/solicitud/actualizar',
        type: 'POST',
        dataType: 'json',
        data: {"id_solicitud": idSolicitude,"id_operador":id_operador,"action":message, 'paso':step},
    })
    .done(function(response) {
        if(response.status.ok)
        {
            let gestion = "["+message+"]";
            saveTrack(gestion, type_contact, idSolicitude, id_operador);
        }
    })
    .fail(function(response) {
        window.location.href = response.responseJSON.redirect;
    })
    .always(function() {

    });
}

//Actualiza las imagenes pegandole a un endpoint 
function solicitudeUpdateImage(id_solicitud)
{  
    $.ajax({
        url: base_url+'atencion_cliente/Gestion/update_image',
        type: 'POST',
        dataType: 'html',
        data: {"id_solicitud": id_solicitud},
    })
    .done(function(response) {  
            
            if ($("#box_galery").parent().html(response)){
                toastr["success"]("Se actualizo correctamente", "IMAGENES ACTUALIZADAS");
            } else {
                toastr["error"]("Error al actualizar", "ERROR");
            };
    })
    .fail(function(response) {
        window.location.href = response.responseJSON.redirect;
    })
    .always(function() {

    });
}

/***************************************************************************/
// Tracker
/***************************************************************************/

var get_track = (id_solicitud) =>
{
    console.log("id_solicitud", id_solicitud);
    $.ajax({
        url: base_url+'solicitud/gestion/track/'+id_solicitud,
        type: 'GET',
        dataType: 'html',
    })
    .done(function(response) 
    {  
        console.log("response", response);
        $("#tracker").text();
        $("#tracker").css('background-color','');
        $("#tracker").html(response);
    })
    .fail(function(response) {
        console.log('error');
    })
    .always(function() {

    });
}
/***************************************************************************/
// Chat Whatsapp
/***************************************************************************/ 

var get_chat_whatsapp = (id_solicitud) =>
{
    console.log("id_solicitud", id_solicitud);
    $.ajax({
        url: base_url+'solicitud/gestion/whatsapp/'+id_solicitud+'/'+paginacion,
        type: 'GET',
    })
    .done(function(response) {
        console.log("response", response);
        $("#whatsapp").html(response);
    })
    .fail(function() {
    })
    .always(function() {
    });

}

function button_status()
{
    // buttons
    let verified = $("#box_botones_gestion #verified");
    let validated = $("#box_botones_gestion #validated");
    let approved = $("#box_botones_gestion #approved");
    let visado = $("#box_botones_gestion #visado");
    let analizado = $("#box_botones_gestion #analizado");
    let rejected = $("#box_botones_gestion #rejected");
    let analysis_buro = $("#box_botones_gestion #analysis_buro").val();
    let solicitud_status = $("#box_botones_gestion #solicitud_status").val();
    let step = $("#box_botones_gestion #step").val();
    let tipo_operador = $("#box_botones_gestion #tipo_operador").val();

    if(tipo_operador == "AUDITOR VENTAS")
    {
        $(verified).prop('disabled',true);
        $(validated).prop('disabled',true);            
        $(approved).prop('disabled',true);
        $(visado).prop('disabled',true);
        $(analizado).prop('disabled',false);
        $(rejected).prop('disabled',false);

    } else if(analysis_buro == "APROBADO" && step >= 16)
    {
        
        console.log("solicitud_status", solicitud_status);
        if(solicitud_status == "")
        {
            $(verified).prop('disabled',false);
            $(validated).prop('disabled',true);
            $(visado).prop('disabled',true);
            $(analizado).prop('disabled',true);
            $(approved).prop('disabled',true);
            $(rejected).prop('disabled',false);
        }
        else if(solicitud_status == "ANALISIS")
        {
            $(verified).prop('disabled',false);
            $(validated).prop('disabled',true);
            $(visado).prop('disabled',true);
            $(analizado).prop('disabled',false);
            $(approved).prop('disabled',true);
            $(rejected).prop('disabled',false);
        }
        else if(solicitud_status == "VERIFICADO")
        {
            $(verified).prop('disabled',true);
            $(validated).prop('disabled',false);
            $(visado).prop('disabled',true);
            $(analizado).prop('disabled',false);
            $(approved).prop('disabled',true);
            $(rejected).prop('disabled',false);
        }else if(solicitud_status == "VALIDADO")
        {
            $(verified).prop('disabled',true);
            $(validated).prop('disabled',true);
            $(visado).prop('disabled',true);
            $(analizado).prop('disabled',false);
            $(approved).prop('disabled',false);
            $(rejected).prop('disabled',false);
        }else if(solicitud_status == "APROBADO")
        {
            $(verified).prop('disabled',true);
            $(validated).prop('disabled',true);            
            $(approved).prop('disabled',true);           
            $(visado).prop('disabled',false);
            $(analizado).prop('disabled',false);
            $(rejected).prop('disabled',false);
        }else if(solicitud_status == "VISADO")
        {
            $(verified).prop('disabled',true);
            $(validated).prop('disabled',true);            
            $(approved).prop('disabled',true);
            $(visado).prop('disabled',true);
            $(analizado).prop('disabled',false);
            $(rejected).prop('disabled',false);
         
        }else          
        {
            $(verified).prop('disabled',true);
            $(validated).prop('disabled',true);
            $(visado).prop('disabled',true);
            $(analizado).prop('disabled',true);
            $(approved).prop('disabled',true);
            $(rejected).prop('disabled',false);
        }
    }else{
        $(verified).prop('disabled',true);
        $(validated).prop('disabled',true);
        $(visado).prop('disabled',true);
        $(analizado).prop('disabled',true);
        $(approved).prop('disabled',true);
        $(rejected).prop('disabled',false);
    }
}


function saveTrack(comment, typeContact, idSolicitude, idOperator)
{
    $('#btn_save_comment').addClass('disabled');
    $.ajax({
        url: base_url+'api/track_gestion',
        type: 'POST',
        dataType: 'json',
        data: {'observaciones':comment, 'id_tipo_gestion':typeContact, 'id_solicitud':idSolicitude, 'id_operador':idOperator}
    })
    .done(function(response) {
        if(response.status.ok)
        {
            //addElemTimeLine(response.comment);
        }
    })
    .fail(function(response)
    {
        window.location.href = response.responseJSON.redirect;

    })
    .always(function() {
        $('#btn_save_comment').removeClass('disabled');
    });
}

function consultar_solicitud(id_solicitud)
{
    event.preventDefault();
    let base_url = $("#base_url").val();
    //console.log(base_url+'solicitud/'+id_solicitud);
    $.ajax({
        url: base_url+'solicitud/'+id_solicitud,
        type: 'GET',
    })
    .done(function(response) {
        $("#tabla_desembolso").hide();
        $("#tabla_solicitudes").hide();
        $("#botones_filtro").hide();
        $("#section_search_solicitud #form_search").hide();
        $("#section_search_solicitud #result").hide();
        $("#texto").text();
        $("#texto").html(response);
        get_track(id_solicitud);
        get_chat_whatsapp(id_solicitud);
    })
    .fail(function() {
    })
    .always(function() {
    });

}

// Pantalla de comparacion
function compareImages(elem)
{
    event.preventDefault();
    $("#compare").show();
    if($(elem).hasClass("screen_1")){
        $("#screen_1 img").attr("src",$(elem).data("src"));
        $("#box_galery .images .screen_1").each(function(index,elem){
            $(elem).removeClass("btn-primary").addClass("btn-default");
        })
        $(elem).addClass("btn-primary");
    }else if($(elem).hasClass("screen_2"))
    {
        $("#screen_2 img").attr("src",$(elem).data("src"));
        $("#box_galery .images .screen_2").each(function(index,elem){
            $(elem).removeClass("btn-primary").addClass("btn-default");
        })
        $(elem).addClass("btn-primary");
    }
}
// Agrega un elemento a la galeria
function addImage(image, position='after')
{
    let date = new Date();
    html = '<div class="col-md-2 item-galery" style="margin-left: 100px; width: 152px">';
    html +=     '<img class="img-thumbnail" src="'+image.uri+'?'+date.getTime()+'">';
    html +=     '<div class="caption">';
    html +=         '<p style="font-size: smaller">'+image.descripcion+'</p>';
    html +=         '<a href="#" class="screen_1 btn btn-default" onclick="compareImages(this)" data-src="'+image.uri+'">1</a>';
    html +=         '<a href="#" class="screen_2 btn btn-default" onclick="compareImages(this)" data-src="'+image.uri+'">2</a>';
    html +=     '</div>';
    html +='</div>';

    if(position == 'after')
    {
        $("#box_galery #fotos").append(html);
    }else{
        $("#box_galery #fotos").prepend(html);
    }
}
function addDoc(doc, position='before')
{
    html = '<a href="'+doc.uri+'" target="_blank" class="list-group-item" style="font-size:smaller;">'+doc.descripcion+'</a>';
    if(position == 'after')
    {
        $("#box_documento #field_files").append(html);
    }else{
        $("#box_documento #field_files").prepend(html);
    }
}



function buscarCredito(search)
{
    let base_url = $("#base_url").val();
    table_search.processing( true );
    $.ajax({
        url: base_url+'api/solicitud/buscar/',
        type: 'POST',
        dataType: 'json',
        data:search,
    })
    .done(function(response) {
        table_search.processing( false );
        table_search.clear();
        table_search.rows.add(response.solicitude);
        table_search.draw();
        $("#section_search_solicitud #result").show();

    })
    .fail(function(response) {
        //console.log("error");
    })
    .always(function(response) {
        //console.log("complete");
    });

}

function resetTelefono(id_solicitud)
{
    event.preventDefault();
    //let id_solicitud = $(elem).data("id_solicitud");
    let base_url = $("#base_url").val();
    //console.log(base_url+'atencion_cliente/Gestion/resetTelefono/'+id_solicitud);
    $.ajax({
        url: base_url+'atencion_cliente/Gestion/resetTelefono/'+id_solicitud,
        type: 'GET',
    })
    .done(function(response) {
        $("#celdaValTelefono").html('&nbsp;-&nbsp;0');
    })
    .fail(function() {
        //console.log("error");
    })
    .always(function() {
        //console.log("complete");
    });

}

function resetEmail(id_solicitud)
{
    event.preventDefault();
    //let id_solicitud = $(elem).data("id_solicitud");
    let base_url = $("#base_url").val();
    //console.log(base_url+'atencion_cliente/Gestion/resetEmail/'+id_solicitud);
    $.ajax({
        url: base_url+'atencion_cliente/Gestion/resetEmail/'+id_solicitud,
        type: 'GET',
    })
    .done(function(response) {
        $("#celdaValEmail").html('&nbsp;-&nbsp;0');
    })
    .fail(function() {
        //console.log("error");
    })
    .always(function() {
        //console.log("complete");
    });

}

function agendar(id_solicitud,id_operador,nombres,apellidos)
{

    event.preventDefault();
    let fecha_agenda = $("#fecha_agenda").val();
    let hora_agenda = $("#hora_agenda").val();
    console.log(id_solicitud+' '+id_operador+' '+nombres+' '+apellidos+' '+fecha_agenda+' '+hora_agenda);
    let base_url = $("#base_url").val();
    //console.log(base_url+'atencion_cliente/Gestion/resetEmail/'+id_solicitud);
    $.ajax({
        url: base_url+'api/agendar',
        type: 'POST',
        dataType: 'json',
        data: {'id_solicitud':id_solicitud, 'id_operador':id_operador, 'nombres':nombres, 'apellidos':apellidos, 'fecha_agenda':fecha_agenda, 'hora_agenda':hora_agenda}
    })
    .done(function(response) {
        console.log(response);
        $("#texto").html("");
        //recargar lista de agendados

    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });

}

function send_sms(id_solicitud)
{
    // event.preventDefault();
    let base_url = $("#base_url").val();
    var send = false;
    $.ajax({
        url: base_url+'api/enviar_sms/codigo_validacion/'+id_solicitud,
        type: 'GET',
        "async":false,
        "beforeSend":function(xhr){$("#btn_send_sms").hide();}
    })
    .done(function(response) {
        if(response.status.ok)
        {
            send = true;  
        }else{
            send = false;  
        }
    })
    .fail(function() {
        console.log("error");
    });

    return send;
}

function send_email_validation(id_solicitud)
{
    // event.preventDefault();
    let base_url = $("#base_url").val();
    var send = false;
    $.ajax({
        url: base_url+'api/enviar_email/codigo_validacion/'+id_solicitud,
        type: 'GET',
        "async":false,
        "beforeSend":function(xhr){$("#btn_send_email").hide();}
    })
    .done(function(response) {
        if(response.status.ok)
        {
            send = true;  
        }else{
            send = false;  
        }
    })
    .fail(function() {
        console.log("error");
    });

    return send;
}

function edit_solicitud_bank(id_solicitud, data)
{
    let base_url = $("#base_url").val();
    data.id_solicitud = id_solicitud;
    $.ajax({
        url: base_url+'api/solicitud/banco/actualizar',
        data : data,
        type: 'POST',
    })
    .done(function(response) {
        console.log("done");
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    })
}



function edit_solicitud_data_client(id_solicitud, data)
{
    var send = false;
    let base_url = $("#base_url").val();
    data.id_solicitud = id_solicitud;
    $.ajax({
        url: base_url+'api/solicitud/actualizar/cliente',
        data : data,
        type: 'POST',
        "async":false,
    })
    .done(function(response) {
         if(response.status.ok)
        {
            send = response;  
        }else{
            send = false;  
        }
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    })
    
    return send;
}
function send_sms_desembolso(id_usuario)
{
    // event.preventDefault();
    let base_url = $("#base_url").val();
    let data = {'id_usuario':id_usuario};
    var send = false;
    $.ajax({
        url: base_url+'EnviarSms/EnviarSms',
        type: 'POST',
        data : data,
    })
    .done(function(response) {
        if(response.status == 200)
        {
            send = true;  
        }else{
            send = false;  
        }
    })
    .fail(function() {
        console.log("error");
    });

    return send;
}
var toastr;
if(toastr){
toastr.options = {
  "closeButton": false,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-top-center",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}
}

function reenvioValidarCuenta(){    
    //Guardar lo que esta solicitud_datos_bacarios a solicitud_datos_bacarios_intentos    
    var id_solicitud       = $("#id_solicitud").val();
    var numero_cuenta      = $("#nro_cuenta_original").val(); 
    var id_banco           = $("#client_bank").val();
    var id_tipo_cuenta     = $("#client_account").val();
    var id_operador        = $("#id_operador").val();
    var numero_cuenta_ant  = $("#cuenta_antigua").val(); 
    var banco_ant          = $("#banco_antiguo").val();
    var tipo_cuenta_ant    = $("#tipo_cuenta_antigua").val();
    var nombre_operador    = $("#operador_nombre").val(); 
    var nombre_banco       = $("#client_bank option:selected").html();
    var nombre_tipo_cuenta = $("#client_account option:selected").html();
    var buro               = $("#buro").html(); 
    
    if(!numero_cuenta || !id_banco || !id_tipo_cuenta){
        toastr["warning"]("Debe ingresar un número de cuenta, banco y tipo de cuenta", "ACTUALIZACION DE CUENTA");
    } else {       
        var base_url = $("#base_url").val();
        var data = {
                "id_solicitud"       : id_solicitud,
                "numero_cuenta"      : numero_cuenta,
                "id_banco"           : id_banco,
                "id_tipo_cuenta"     : id_tipo_cuenta,
                "numero_cuenta_ant"  : numero_cuenta_ant,
                "banco_ant"          : banco_ant,
                "tipo_cuenta_ant"    : tipo_cuenta_ant,
                "id_operador"        : id_operador,
                "nombre_operador"    : nombre_operador,
                "nombre_banco"       : nombre_banco,
                "nombre_tipo_cuenta" : nombre_tipo_cuenta,
                "buro"               : buro
        }        
        var base_url = $("input#base_url").val() + "gestion/Galery/actualizarNumeroCuenta";
        $.ajax({
        type: "POST",
                url: base_url,
                data: data,
                dataType : 'json',
                success:function(response){
                    //actualizar espacio de trackeo
                    //addElemTimeLine(response.track);  
                    $('#banco_antiguo').val(banco_nombre);
                    $('#tipo_cuenta_antigua').val(tipo_nombre);
                    $('#cuenta_antigua').val(numero_cuenta);
                    if(response.update_cbu == "APROBADO"){
                        toastr["success"](response.update_cbu, "CUENTA ACTUALIZADA");  
                        $('.fondo').removeClass("bg-danger").addClass("bg-success");
                        $('#icono_cuenta').removeClass("fa-times-circle").addClass("fa-times-check");
                        $('#icono_cuenta').css('color','green');

                    } else {
                        toastr["error"](response.update_cbu, "CUENTA ACTUALIZADA");   
                        $('.fondo').removeClass("bg-success").addClass("bg-danger");
                        $('#icono_cuenta').removeClass("fa-times-check").addClass("fa-times-circle");
                        $('#icono_cuenta').css('color','red');
                    }
                }
        });
    }
}

function verificar_familiar(){
    var verificacion     = $("#familiar").val();
    var id_ref           = $("#id_ref_family").val();
    var id_solicitud     = $("#id_solicitud").val();
    var tipo             = "Referencia Familiar"
    var id_operador      = $("#id_operador").val();
    var nombre_operador  = $("#operador_nombre").val();
    var referencia_tipo  = $("#referencia_tipo").val();
     if(verificacion == ""){
        toastr["warning"]("Por favor seleccionar un valor de verificacion", "VERIFICAR");
    } else {
        var base_url = $("#base_url").val()+ "gestion/Galery/verificar";
        var data = {
                    "verificacion"    : verificacion,
                    "id_ref"          : id_ref,
                    "id_solicitud"    : id_solicitud,
                    "tipo"            : tipo,
                    "id_operador"     : id_operador,
                    "nombre_operador" : nombre_operador,
                    "referencia_tipo" : referencia_tipo
        } 
        $.ajax({
        type: "POST",
                url: base_url,
                data: data,
                dataType :'json',
                success:function(response){ 
                    //addElemTimeLine(response.track);  
                    toastr["success"](response.response, "VERIFICAR");
                    $('#estado_familiar').html(verificacion); 
                }
        });
    };
}

function verificar_personal(){
    var verificacion = $("#personal").val();
    var id_ref = $("#id_ref_personal").val();
    var id_solicitud     = $("#id_solicitud").val();
    var tipo             = "Referencia Familiar"
    var id_operador      = $("#id_operador").val();
    var nombre_operador  = $("#operador_nombre").val();
    var referencia_tipo  = $("#referencia_tipo_personal").val();
     if(verificacion == ""){
        toastr["warning"]("Por favor seleccionar un valor de verificacion", "VERIFICAR");
    } else {
        var base_url = $("#base_url").val()+ "gestion/Galery/verificar";
        var data = {
                "verificacion"    : verificacion,
                "id_ref"          : id_ref,
                "id_solicitud"    : id_solicitud,
                "tipo"            : tipo,
                "id_operador"     : id_operador,
                "nombre_operador" : nombre_operador,
                "referencia_tipo" : referencia_tipo
        } 
        $.ajax({
        type: "POST",
                url: base_url,
                data: data,
                dataType :'json',
                success:function(response){
                    //addElemTimeLine(response.track);  
                    toastr["success"](response.response, "VERIFICAR");
                    $('#estado_personal').html(verificacion);
                }
        });
    };
}

function ValidarNumeros(event){
    const reg = new RegExp(/^\d+$/, 'g');
    //const cadena = reg.test(event.key);
    if (!reg.test(event.key))
    {
        event.preventDefault();
    }
}

function reenvioDatos(){      
    var id_solicitud       = $("#id_solicitud").val();  
    var base_url = $("#base_url").val();
        var data = {
                "id_solicitud"       : id_solicitud              
        }        
        var base_url = $("input#base_url").val() + "gestion/Galery/actualizarDatos";
        $.ajax({
        type: "POST",
                url: base_url,
                data: data,
                dataType : 'json',
                success:function(response){
                    //actualizar toda la solicitud
                    consultar_solicitud(id_solicitud);
                }
        });
}

function desbloquear_usuario(email)
{
    var email = $("#email").text();
    var base_url = $("input#base_url").val() + 'api/desbloquear_usuario';
    var data = {
        "email" : email              
    }       
    var send = false;
    $.ajax({
        url: base_url,
        type: 'POST',
        data: data,
        dataType : 'json',
        "async":false,
        "beforeSend":function(xhr){$("#desbloquear").hide();}
    })
    .done(function(response) {
        if(response.status.ok)
        {
            desbloquear = toastr["success"]("Se desbloqueo el usuario correctamente", "Desbloqueo de Email");
        }
        else
        {
            desbloquear = toastr["error"]("Error al desbloquear usuario", "Desbloqueo de Email");
        }
    })
    .fail(function() {
        console.log("error");
    });

    return desbloquear;
}



$("#chk_operadores").click(function(){
    if($("#chk_operadores").is(':checked') ){
        $("#sl_operadores > option").prop("selected","selected");
        $("#sl_operadores").trigger("change");
    }else{
        $("#sl_operadores > option").removeAttr("selected");
         $("#sl_operadores").trigger("change");
     }
});
