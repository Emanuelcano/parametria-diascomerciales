<style>
.has-tip {
  display: block;
  
  width: 100%;
  float: left;
  margin-bottom: 3px;
  background: url(https://image.freepik.com/free-icon/information-circle_318-27255.jpg)
    no-repeat right center / contain;
  cursor: pointer;
  position: relative;
}
.has-tip:hover:before {
  font-size: 10px;
  content: attr(data-tip);
  display: flex;
  justify-content: center;
  width: 300px;
  color: #ebebeb;
  background: #444;
  border: 1px solid #444;
  padding: 20px;
  bottom: 20px;
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  border-radius: 10px;
  box-shadow: 0 5px 10px #000;
}
.has-tip:hover:after {
  position: absolute;
  left: calc(390px);
  background: #ddf;
  width: 20px;
  height: 20px;
  transform: rotate(0deg);
  border-right: 1px solid #ebebeb;
  border-bottom: 1px solid #ebebeb;
  border-radius:50%;
  bottom: 10px;
  box-shadow: 5px 5px 7px #000;
}
.select2-search__field {
  width: 200px;

}

.loader {
    border: 16px solid #f3f3f3; /* Light grey */
    border-top: 16px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
    display: none;
    align-items: center;
    justify-content: center;
    margin: 0 auto;

    
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.form_grafica{
		margin-left:3%;
	}

	#settings_and_table_container{
		margin-left:1.1%;
	}

	#graph{
		left:-60px;
	}
  .progress.xs, .progress-xs {
  height: 20px;
}
.grillas-words{
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}
.graph3{

  
  overflow-y:scroll; 
  position:relative;
  height: 300px;
}

div.fondo{
    background-color: #e8daef!important;
    border: none!important;
    color: #777!important;
    
  }
  li.active > a{
    background-color: #e8daef!important;
  }
  ul {
    margin-bottom: 10%;
  }
</style>
<script src="<?php echo base_url('assets/js/Chart.min.js');?>"></script>
<script src="<?php echo base_url('assets/js/chartjs-plugin-datalabels.min.js');?>"></script>
    <div id="exTab2" class="container col-md-12">	
          <ul class="nav nav-tabs">
                <li class="fondo active">
                  <a  href="#1" data-toggle="tab">Tiempos</a>
                </li>
                <li class="fondo"><a href="#2" data-toggle="tab">Operadores</a>
                </li>
                
               
          </ul>

          <div class="tab-content ">
            <div class="tab-pane active" id="1">
                <br>
                <h4>Gestion de tiempos para la gestion de chats</h4>
                <h6>Una vez actualizado el tiempo en segundos la configuracion se guarda automaticamente y muestra su equivalente en segundos</h6>
                <hr>
                <form action="#" method="POST" name="form_search" id="form_search" > 
                <div class="row" id="table_tiempos">
                    
                    
                    
                </div>
                
                
                </form>
            </div>
            <div class="tab-pane" id="2">
                  <br>
                  <h4>Asignacion Operadores campaña de chats</h4>
                  <h6>El cambio se efectuara tras checkear el boton para su asignación</h6>
                  <hr>
                    <form action="#" method="POST" name="form_notificaciones" id="form_notificaciones" >
                        <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
                        <div class="row" id="table_operadores"></div>
                                <!-- AQUI -->
                    </form>
                             
            </div>
        </div>
        </div>
                  
           
          </div>
    </div>



<div class="modal fade" id="compose-modal-wait" tabindex="-1" role="dialog" aria-hidden="true">
           <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        
                        <h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS SE GENERA SU BUSQUEDA </h4>


                        <div class="col-md-12 hide" id="succes">
                            <!-- Primary box -->
                            <div class="box box-solid box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">BUSQUEDA DE TRACKING</h3>
                                    <div class="box-tools pull-right">
                                        <button class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                        <button class="btn btn-primary btn-sm" data-widget="remove"><i class="fa fa-times"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <span id="respuesta"></span>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col -->


                    </div>

                <div class="modal-body">

                                
                    <div class="data"></div>
                    <div class="loader"></div> 
                                
                         
                 </div>





                    <div class="modal-footer clearfix">


                    </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


<script type="text/javascript" src="<?php echo base_url('assets/notificaciones/configuracion_grupos.js'); ?>"></script>


<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()


       
})

$( document ).ready(function() {
  renderTabla()
});

function renderTabla()
{
  let base_url = $("#base_url").val();
        $.ajax({
            url: base_url + 'api/ApiSupervisores/listar_configuraciones',
            type: 'POST',
            
        })
        .done(function (response) {
            // console.log(response)
            var registros2 = eval(response.data.config);
        // console.log(registros2)
                var html = '';
                var enviado = '';
                html ="<table id='tbl_table_tiempos' class='table table-responsive table-bordered'><thead>";
                html +="<tr style='background-color:#d9edf7;'>";
                html +='<th style="width: 5%; padding: 0px; padding-left: 10px;">PRIORIDAD</th>';
                html +='<th style="width: 5%; padding: 0px; padding-left: 10px;"> TIEMPO RESPUESTA </th>';
                html +='<th style="width: 5%; padding: 0px; padding-left: 10px;"> TIEMPO ESPERA </th>';

                html +="</tr>";
                html +="</thead><tbody>";
            
            for (var i = 0; i < registros2.length; i++) {
                var min_res = parseInt(registros2[i]['tiempo_respuesta'])
                var min_wait = parseInt(registros2[i]['tiempo_espera'])
                if (min_res==0){
                     min_res = "00:00"
                }else{

                     min_res = secondsToString(registros2[i]['tiempo_respuesta'])

                }
                if (min_wait==0){
                     min_wait = "00:00"
                }else{
                     min_wait = secondsToString(registros2[i]['tiempo_espera'])

                }
                
                html +=" <tr>";
                html +=" <td>"+registros2[i]['id']+".-"+registros2[i]['prioridad']+"</td>";
                html +=' <td><input type="number" name="txt_time_resp_'+registros2[i]['id']+'" onkeypress="return valideKey(event);" data-id="'+registros2[i]['id']+'" data-campo="tiempo_respuesta" id="txt_time_resp_'+registros2[i]['id']+'" value="'+registros2[i]['tiempo_respuesta']+'"> seg <strong><label id="" >'+min_res+'</label></strong> min </td>';
                html +=' <td><input type="number" name="txt_time_wait_'+registros2[i]['id']+'" onkeypress="return valideKey(event);" data-id="'+registros2[i]['id']+'" data-campo="tiempo_espera"  id="txt_time_wait_'+registros2[i]['id']+'" value="'+registros2[i]['tiempo_espera']+'"> seg <strong><label id="" >'+min_wait+'</label></strong> min </td>';
               
                html +=" </tr>";
                
            }
            html +="</tbody></table>";
            
            $('#table_tiempos').html(html);
            // $('#tbl_search_track').DataTable().ajax.reload()
            
            let table = $('#tbl_table_tiempos').DataTable( {
                            searching: true,
                            autoFill: true,
                            pageLength: 7,
                            bFilter: false,
                            bSort: true,
                            responsive: true,
                            colReorder: true,
                            select: true,
                            'language': {
                                'decimal': ',',
                                'thousands': '.',
                                'lengthMenu': 'Mostrando _MENU_ registros por pagina',
                                'zeroRecords': 'Nothing found - sorry',
                                'info': 'Mostrando pagina _PAGE_ de _PAGES_',
                                'infoEmpty': 'No records available',
                                'infoFiltered': '(filtered from _MAX_ total records)'
                            },
                            'pagingType': 'full_numbers',
                            
            });

            var registros = eval(response.data.operadores);
        // console.log(registros2)
                var html = '';
                var enviado = '';
                html ="<table id='tbl_table_operadores' class='table table-responsive table-bordered'><thead>";
                html +="<tr style='background-color:#d9edf7;'>";
                html +='<th style="width: 5%; padding: 0px; padding-left: 10px;">OPERADOR</th>';
                html +='<th style="width: 5%; padding: 0px; padding-left: 100px;"> ACTIVAR </th>';

                html +="</tr>";
                html +="</thead><tbody>";
            
            for (var i = 0; i < registros.length; i++) {
                


                
                
                html +=" <tr>";
                html +=" <td>"+registros[i]['idoperador']+".-"+registros[i]['nombre_apellido']+" ("+registros[i]['tipo_operador']+")"+"</td>";
                html +=' <td><input type="checkbox" data-id="'+registros[i]['idoperador']+'" data-tipo="'+registros[i]['idope']+'" id="chk_cg_stat_'+registros[i]['idoperador']+'" name="chk_cg_stat[]" ></td>';
                html +=" </tr>";

                
                
            }
            html +="</tbody></table>";
            
            $('#table_operadores').html(html);
            for (var i = 0; i < registros.length; i++) {
              if(registros[i]['check_state']==1){
                  $( "#chk_cg_stat_"+registros[i]['idoperador'] ).prop( "checked", true );

                }else{
                  $( "#chk_cg_stat_"+registros[i]['idoperador'] ).prop( "checked", false );

                }
            }
            // $('#tbl_search_track').DataTable().ajax.reload()
            
            let table2 = $('#tbl_table_operadores').DataTable( {
                            searching: true,
                            autoFill: true,
                            pageLength: 7,
                            bFilter: false,
                            bSort: true,
                            responsive: true,
                            colReorder: true,
                            select: true,
                            'language': {
                                'decimal': ',',
                                'thousands': '.',
                                'lengthMenu': 'Mostrando _MENU_ registros por pagina',
                                'zeroRecords': 'Nothing found - sorry',
                                'info': 'Mostrando pagina _PAGE_ de _PAGES_',
                                'infoEmpty': 'No records available',
                                'infoFiltered': '(filtered from _MAX_ total records)'
                            },
                            'pagingType': 'full_numbers',
                            
            });


        })
        .fail(function (response) {
            console.log(response.error);
        })
        .always(function (response) {
            //console.log("complete");
        });
}
function secondsToString(seconds) {
//   var hour = Math.floor(seconds / 3600);
//   hour = (hour < 10)? '0' + hour : hour;
  var minute = Math.floor((seconds / 60) % 60);
  minute = (minute < 10)? '0' + minute : minute;
  var second = seconds % 60;
  second = (second < 10)? '0' + second : second;
//   return hour + ':' + minute + ':' + second;
  return  minute + ':' + second;
}


$('body').on('change','#tbl_table_tiempos input',function(event){

// id_dksele = $(this).attr('id_dk');
nametxt = $(this).attr('name');
valortxt = $(this).attr('value');
id = $(this).attr('data-id');
campo = $(this).attr('data-campo');
var txtnew_val= $("#"+nametxt).val()

var parametros = {   
  "id"      : id, 
  "campo"   : campo, 
  "new_val" : txtnew_val, 
};
let base_url = $("#base_url").val();

$.ajax({
        url:base_url + 'api/ApiSupervisores/cambiar_parametros',
        type:'POST',
        data:parametros,
        success:function(respuesta){
          // toastr["error"]("No se pudo actualizar el Agente", "ERROR");
          toastr["success"]("Se actualizo correctamente el campo", "Actualizado");
            renderTabla()
         
          // swal("Verifique!", "No posee permisos para entrar en este sistema o ejecutar esta accion contacte a dpto de sistemas", "error");

         

        }
    });

});

$('body').on('change','#tbl_table_operadores input',function(event){
  // chk_cg_stat
  let base_url = $("#base_url").val();
  id_operador = $(this).attr('data-id');
  tipo_operador = $(this).attr('data-tipo');
  checked = $(this).attr('checked');
  var url_body ='';
  var mensaje ='';
  var datos = {   
    "id_operador" : id_operador, 
    "tipo_operador" : tipo_operador, 
  };
  if ($(this).is(":checked")) {
    var swal_title ='El operador ya se encuentra activo en otra campaña de gestión automática.'
    var swal_mensaje ="¿Desea DESACTIVARLO en esa campaña y activarlo en esta?"
  }else{
    var swal_title ='El operador ya se encuentra activo en las campañas de chat .'
    var swal_mensaje ="¿Desea DESACTIVARLO en esta campaña ?"

  }  
  Swal.fire({
  title: swal_title,
  text:  swal_mensaje,
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Si!'
}).then((result) => {
  if (result.value) {
    if ($(this).is(":checked")) {
      datos['activo'] = 1
      url_body = 'api/ApiSupervisores/cambiar_estado_operador'
      mensaje = "Operador desvinculado de campañas automaticas activado en campaña de chat"
    }else{

      datos['activo'] = 0
      url_body = 'api/ApiSupervisores/desactivar_estado_operador'
      mensaje = "Operador desactivado en campaña de chat"
    }

        $.ajax({
                url:base_url + url_body ,
                type:'POST',
                data:datos,
                success:function(respuesta){
                    // alert(respuesta)
                    // renderTabla()
                
                  toastr["success"](mensaje, "Actualizado");
                

                }
            });
  }else{
    renderTabla()
    toastr["info"]("Accion cancelada Agente no asignado", "Aviso");
  }
})


 

});

function valideKey(evt){
    
    // code is the decimal ASCII representation of the pressed key.
    var code = (evt.which) ? evt.which : evt.keyCode;
    
    if(code>=48 && code<=57) { // is a number.
      return true;
    } else{ // other keys.
      return false;
    }
}



</script>