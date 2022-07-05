
function listarDistribucionCobranzas() {
	$('#main').show();
    $("#main-ausencias").hide();
	$("#main-horarios").hide();
	$("#tp_HorarioOperadores").css("display", "none");
	user_modulos = [];
	base_url = $("input#base_url").val() + "supervisores/Supervisores/VistaDistribucionCobranzas";
	$.ajax({
		type: "POST",
		url: base_url,
		success: function (response) {
			$("#main").html(response);
			$("#cargando").css("display", "none");
			TablaPaginada('tp_Operadores', 0, 'asc');
		}
	})
}

function validaciones_btn(button){

    if (button=="btn_preview") {
        DistribucionPreview();

        }else if(button=="btn_distriubcion"){
        Distribuir();

        }else if(button=="btn_limpar_base"){
        limpiarBase();
            
        }else{
            return false;

    }

    
}

function DistribucionPreview(){

    let base_url = $("#base_url").val();
    
      var $btn = $('#btn');
      var $data = $('.data');
      var $loader = $('.loader');
    
     $.ajax({
         dataType: "JSON",
         data:$('#form_search').serialize(),
         url:   base_url+'api/ApiSupervisores/BuscarCreditosxEquipoTurno',
         type: 'POST',
         beforeSend: function(request) {
          
          
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
              //console.log(respuesta);
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

    function Distribuir(){

        let base_url = $("#base_url").val();
        
          var $btn = $('#btn');
          var $data = $('.data');
          var $loader = $('.loader');
        
         $.ajax({
             dataType: "JSON",
             data:$('#form_search').serialize(),
             url:   base_url+'api/ApiSupervisores/DistribuirCasos',
             type: 'POST',
             beforeSend: function(request) {
              
              
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
                  //console.log(respuesta);
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