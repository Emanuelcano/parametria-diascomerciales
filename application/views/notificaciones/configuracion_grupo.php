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
</style>
<script src="<?php echo base_url('assets/js/Chart.min.js');?>"></script>
<script src="<?php echo base_url('assets/js/chartjs-plugin-datalabels.min.js');?>"></script>
    <div id="exTab2" class="container col-md-12">	
          <ul class="nav nav-tabs">
                <li class="active">
                  <a  href="#1" data-toggle="tab">Indicadores</a>
                </li>
                <li><a href="#2" data-toggle="tab">Grupos</a>
                </li>
                
               
          </ul>

          <div class="tab-content ">
            <div class="tab-pane active" id="1">
                <br>
                <h5><b>Indicadores de palabras filtradas</b></h5>	
                <hr>
                <form action="#" method="POST" name="form_search" id="form_search" > 
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label for="text" class="has-tip" data-tip="Este campo se requiere el rango de fechas para realizar la busqueda!">Rango de Fechas</label>
                        <input type="text" name="daterange" id="daterange" style="width: 300px; height:35px;" value="<?php echo date('d-m-Y', strtotime('-1 day'));?> - <?php echo date("d-m-Y");?>" />
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="text" class="has-tip" data-tip="Este campo se requiere filtrar los distintos tipos de origenes para realizar la busqueda!">Canal*</label>
                        <select class="form-control" name="sl_sr_canal" id="sl_sr_canal">
                            <option value=""> .::selecionar canal::. </option>
                            <option value="ORIGINACION"> ORIGINACION </option>
                            <option value="COBRANZAS"> COBRANZAS </option>
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="text" class="has-tip" data-tip="Este campo se requiere para definir el criterio para la busqueda por numero telefonico (+5710254685) o por numero de documento (74589657) ">Criterio</label>
                        <select class="form-control" name="sl_sr_criterio" id="sl_sr_criterio">
                            <option value=""> .::criterio::. </option>
                            <option value="DOCUMENTO"> Documento </option>
                            <option value="TELEFONO"> Telefono </option>
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="text" class="has-tip" data-tip="Inidique el numero telefonico de el cliente o su documento para realizar la busqueda!">Cliente</label>
                        <input class="form-control" type="text" name="txt_sr_cliente" id="txt_sr_cliente"  value="" placeholder="tlf ó doc" />
                    </div>
                    
                    <div class="form-group col-sm-2">
                        <label for="text" class="has-tip" data-tip="Este campo se requiere filtrar los distintos tipos  de grupos existentes para realizar la busqueda!">Grupo</label>
                        <select class="form-control" name="sl_sr_grupo" id="sl_sr_grupo">
                            <option value="0"> .::selecionar grupo::. </option>
                        </select>
                    </div>
                    
                    
                    
                </div>
                <div class="row">
                <div class="form-group col-sm-2">
                        <label for="text" class="has-tip" data-tip="Filtrar las palabras existentes en un grupo determinado, esta busqueda esta encadenada a la seleccion del grupo!">Palabras</label>
                        <select class="form-control select2-multiple" name="sl_sr_palabras[]" id="sl_sr_palabras" multiple="multiple" style="width: 220px;"></select>
                    </div>
                <div class="form-group col-sm-2">
                        <label for="text" class="has-tip" data-tip="Este campo se requiere filtrar los distintos tipos  de grupos existentes para realizar la busqueda!">Medio</label>
                        <select class="form-control" name="sl_sr_medio" id="sl_sr_medio">
                            <option value=""> .::selecionar medio::. </option>
                            <option value="SLACK"> SLACK </option>
                            <option value="EMAIL"> EMAIL </option>
                            <option value="SMS"> SMS </option>
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="text" class="has-tip" data-tip="Filtrar las palabras existentes en un grupo determinado, esta busqueda esta encadenada a la seleccion del grupo!">Medios</label>
                        <select class="form-control select2-multiple" name="sl_sr_medios[]" id="sl_sr_medios" multiple="multiple" style="width: 200px;"></select>
                    </div>
                    <div class="form-group col-sm-3">
                       </br>
                       <button  type="button" class="btn btn-primary" title="registrar" name="btn_search_track" id="btn_search_track" ><i class="fa fa-search"></i> Buscar</button>
                       <button  type="button" class="btn btn-success" title="registrar" name="btn_excel_track" id="btn_excel_track" ><i class="fa fa-file-text"></i> Excel</button>
                       <button  type="button" class="btn btn-warning" title="registrar" name="btn_chart_track" id="btn_chart_track" ><i class="fa fa-signal"></i> Graficas</button>
                    </div>
                    <div id="table_agente_central" class="col-md-12 hide" >
                      <?= $this->load->view('notificaciones/table_search_track', null, true); ?>   
                    </div>
              </form>
                </div>
                <div class="row" id="div_graficos">
                        <div class="col-md-6">
                            <div class="chart">
                              <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart">
                              <div id="graph"></div>
                              
                            </div>
                        </div>
                    </div>
                    <HR>
                    <div class="row">
                        <div class="col-md-6">
                          <div class="chart">
                          <div id="graph2"></div>
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <div class="card">
                                <div class="card-header">
                                <h3 class="card-title"><label id="tot_notificaciones"></label> </h3>
                                </div>
                                <div class="card-body">
                                      <div id="graph3"></div>
                                </div>
                                <div class="card-footer clearfix">

                                </div>
                            </div>


                          
                              
                        </div>
                    </div>
            </div>
            <div class="tab-pane" id="2">
                  <form action="#" method="POST" name="form_notificaciones" id="form_notificaciones" >
                  <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
                  <input type="hidden" id="id_operador" name="id_operador" value="<?php echo $this->session->userdata('idoperador'); ?>">
                      <div class="row">
                          <div class="col-xs-12">
                              <br>
                              <h5><b>Administración de Grupo de palabras</b></h5>	
                              <hr>
                              <!-- AQUI -->
                              <div class="row">
                                  <div class="form-group col-sm-2">

                                    <input type="hidden"  class="form-control" name="id_grupo_hd" id="id_grupo_hd"></input>
                                    
                                    <label for="text" class="has-tip" data-tip="Este campo se requiere para el nombre de el grupo nuevo a definir o actualizar!">Nombre Grupo</label>
                                    <input type="text"  class="form-control " name="name_group" id="name_group" placeholder="" ></input>
                                  </div>
                                  <div class="form-group col-sm-2">
                                  <label for="text" class="has-tip" data-tip="Este campo requiere el medio por el cual se va a notificar cuando se use el grupo definido!">Medio Notificacion</label>
                                    <select class="form-control" name="sl_medio" id="sl_medio">
                                        <option value=""> .::selecionar medio::. </option>
                                        <option value="SLACK"> SLACK </option>
                                        <option value="EMAIL"> EMAIL </option>
                                        <option value="SMS"> SMS </option>
                                    </select>
                                  </div>
                                  <div class="form-group col-sm-2">
                                    <label for="text" class="has-tip" data-tip="Este campo requiere el IDSLACK sin numeral ej:UV2NBJB9Y / email(correo electronico a notificar ej: juridico@solventa.com)/ sms (numero telefonico sin codigo pais ej:357123644)  segun sea el medio selecionado!">Dirección para el medio</label>
                                    
                                        <select class="form-control select2-multiple" name="id_medio[]" id="id_medio" multiple="multiple" style="width: 220px;"></select>
                                  </div>
                            
                                    <div class="form-group col-sm-2">
                                    <label for="text" class="has-tip" data-tip="Este campo se requiere para que el grupo sea notificado para cuando sean envio o recepciones o ambos casos!">Metodos de Notificación</label>
                                        <select class="form-control" name="sl_metodo" id="sl_metodo">
                                            <option value="" selected="selected">metodo</option>
                                            <option value="1"> ENVIO </option>
                                            <option value="2"> RECEPCION </option>
                                            <option value="1,2"> ENVIO/RECEPCION </option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-2">
                                    <label for="text" class="has-tip" data-tip="Este campo se requiere definir siendo el caso exclusivo mensajes de salinetes de operadores saber si es permitido o no enviarlo!">Accion desencadenada</label>
                                        <select class="form-control" name="sl_action" id="sl_action">
                                            <option value="" selected="selected">Accion</option>
                                            <option value="send"> ENVIAR </option>
                                            <option value="block"> BLOQUEAR </option>
                                        </select>
                                    </div>
                                    
                                  
                                  <div class='form-group col-sm-1' id="cancelAgente" style="display:none;float: left;">
                                    <a class="btn btn-danger"  title="Cancelar Editar Agente" onclick="cancelEditAgente();"><i class="fa fa-ban"></i></a>
                                  </div>
                                  <div class="form-group col-sm-2">
                                  </br>
                                      <button  type="button" class="btn btn-success" title="registrar" style="font-size: 12px; width: 100%;" id="registrar-grupo" ><i class="fa fa-check"></i> REGISTRAR GRUPO</button>
                                      <button  type="button" class="btn btn-success" title="actualizar" style="font-size: 12px; width: 100%; display:none" id="actualizar-grupo" ><i class="fa fa-check"></i> ACTUALIZAR GRUPO</button>
                                  </div>

                                </div>
                              <!-- AQUI -->
                              </form>
                              <div id="table_agente_central">
                                <?= $this->load->view('notificaciones/table_grupos', null, true); ?>  
                              </div>
                      </div>
                    </div>
                  </div>
                  
           
          </div>
    </div>



<div class="modal" id="modalPalabras" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Palabras del Grupo <label id="lbl_origen"></label> Seleccionado <label id="lbl_grupo"></label></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="row">
          <div class="form-group col-sm-3">
          <label for="">PALABRA:</label>
            <input type="hidden"  class="form-control" id="txt_origen" name="txt_origen"></input>
            <input type="text" onkeypress="return solo_palabras(event)" class="form-control" id="txt_palabra" name="txt_palabra"></input>
          </div>
          <div class="form-group col-sm-3">
            <label for="">GRUPO:</label>
            <input type="hidden"  class="form-control" id="hd_group" placeholder="GRUPO" value=""></input>
            <input type="text"  class="form-control" id="txt_group" placeholder="GRUPO" value="" readonly></input>
          </div>
          <div class="form-group col-sm-3">
           <button  type="button" class="btn btn-success" title="registrar" name="registrar-palabra" id="registrar-palabra" ><i class="fa fa-check"></i> REGISTRAR PALABRA</button>
          </div>
      </div>

        <span id="table_words"></span>
      </div>
      <div class="modal-footer">
        
        <button type="button" class="btn btn-secondary" data-dismiss="modal">CERRAR</button>
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

  // var button = document.getElementById("btn_chart_track");
  // button.addEventListener("click", function(){
  //         myChartBar.destroy();
  //     });
        
  // var myChartBar;
})

$('.select2-multiple').select2({
    placeholder: '.: Operador :.',
    multiple: true
  });

  $(function() {
  $('input[name="daterange"]').daterangepicker({
    opens: 'left',
    timePicker: false,
    pickSeconds: false,
    timePicker24Hour: false,
    locale: {"format": "DD-MM-YYYY",cancelLabel: 'Cancelar'},

  }, function(start, end, label) {
    console.log("A new date selecti,on was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
  });
  
});
$("#sl_sr_canal").change(function(){
        origen = $("#sl_sr_canal").val();

        let base_url = $("#base_url").val();
        $.ajax({
            url: base_url + 'api/ApiNotificaciones/mostrarGrupos',
            data:{origen:origen},
            type: 'GET',
            
        })
        .done(function (response) {
            var registros = eval(response);
             let regis = registros.data;
             console.log(regis[0].id_grupo_notificacion)
            for (var i = 0; i < regis.length; i++) {
              $("#sl_sr_grupo").append( '<option value="'+regis[i].id_grupo_notificacion+'">'+regis[i].nombre_grupo+'</option>')
            }
        })
        .fail(function (response) {
            console.log(response.error);
        })
        .always(function (response) {
            //console.log("complete");
        });
});

$("#sl_sr_grupo").change(function(){
        name_grupo = $("#sl_sr_grupo").val();
        origen = $("#sl_sr_canal").val();
// console.log(name_grupo,origen)
        let base_url = $("#base_url").val();
        $.ajax({
            url: base_url + 'api/ApiNotificaciones/mostrarPalabras',
            data:{id:name_grupo,origen:origen},
            type: 'POST',
            
        })
        .done(function (response) {
            var registros = eval(response);
            
            for (var i = 0; i < registros.length; i++) {
              $("#sl_sr_palabras").append( '<option value="'+registros[i]['palabra']+'">'+registros[i]['palabra']+'</option>')
            }
        })
        .fail(function (response) {
            console.log(response.error);
        })
        .always(function (response) {
            //console.log("complete");
        });
});


$("#sl_sr_medio").change(function(){

  base_url = base_url +"api/ApiNotificaciones/searchMedio";
  medio = $("#sl_sr_medio").val(); 
  $.ajax({
            type: "POST",
            url: base_url,
            data: {service:medio},
            success: function (response) {
              html = "";
              var registros = eval(response.data);
              if (medio =="SLACK")
              {
                for (var i = 0; i < registros.length; i++) {
                  $("#sl_sr_medios").append( '<option value="'+registros[i]['slack_id']+'">'+registros[i]['nombre']+ " - " + registros[i]['email']+'</option>')
                }

              }else if(medio =="EMAIL"){
                for (var i = 0; i < registros.length; i++) {
                  $("#sl_sr_medios").append( '<option value="'+registros[i]['mail']+'">'+registros[i]['nombre_pila']+ " - " + registros[i]['mail']+'</option>')
                }
              }else if(medio =="SMS"){
                for (var i = 0; i < registros.length; i++) {
                  $("#sl_sr_medios").append( '<option value="'+registros[i]['wathsapp']+'">'+registros[i]['nombre_pila']+ " - " + registros[i]['wathsapp']+'</option>')
                }
              }
              
            }
        });


});
$("#btn_search_track").click(function(){
// debugger;
$("#table_agente_central").removeClass("hide");
$("#div_graficas").addClass("hide");

  let base_url = $("input#base_url").val();
  var $btn = $('#btn');
  var $data = $('.data');
  var $loader = $('.loader');

  $.ajax({
     dataType: "JSON",
     data:$('#form_search').serialize(),
     url:   base_url+"api/ApiNotificaciones/searchTracking",
     type: 'POST',
     beforeSend: function(request) {
      
     
      $("#compose-modal-wait").modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        });

      $loader.show();
     }
    }).done(function(respuesta){

      $('#tbl_search_track').DataTable().destroy();
      setTimeout(function(){

       $loader.hide();
       $("#compose-modal-wait").modal('hide');
       
      }, 1000);
      
      // alert(respuesta.data);
     
      if (respuesta==="ERROR EN TRANSFERENCIA") {
                
                swal.fire("Error","Ocurrio un error mientras se realizaba la busqueda!"+respuesta,"error");
                

      }else if (respuesta.data!="") {
        var registros2 = eval(respuesta.data);
        console.log(registros2)
                var html = '';
                var enviado = '';
                
            for (var i = 0; i < registros2.length; i++) {
                if(registros2[i]['operador'] =="" || registros2[i]['operador']==null){
                  enviado = "Cliente"
                }else{
                  enviado =  registros2[i]['operador'];
                }
                html +=" <tr>";
                html +=" <td>"+registros2[i]['fecha_notificacion']+"</td>";
                html +=" <td>"+registros2[i]['num_contacto']+"</td>";
                html +=" <td>"+registros2[i]['nombre_grupo']+"</td>";
                html +=" <td>"+registros2[i]['mensaje_filtrado']+"</td>";
                html +=" <td>"+registros2[i]['palabras']+"</td>";
                html +=" <td>"+registros2[i]['notificado']+"</td>";
                html +=" <td>"+enviado+"</td>";
                html +=" <td>"+registros2[i]['origen']+"</td>";
                html +=" </tr>";
                
            }
            
            $('#tbl_body_search').html(html);
            // $('#tbl_search_track').DataTable().ajax.reload()
            
            let table = $('#tbl_search_track').DataTable( {
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

      }



    }).fail(function(xhr,err){
      $loader.hide();
      Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
    })




});
$("#btn_excel_track").click(function(){
// debugger;
  let base_url = $("input#base_url").val();
  var $btn = $('#btn');
  var $data = $('.data');
  var $loader = $('.loader');

  $.ajax({
     dataType: "JSON",
     data:$('#form_search').serialize(),
     url:   base_url+"api/ApiNotificaciones/reportExcelTracking",
     type: 'POST',
     beforeSend: function(request) {
      
     
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
      
      let url = base_url+"public/csv/"+respuesta.file;
				window.open(url, '_self');




    }).fail(function(xhr,err){
      $loader.hide();
      Swal.fire("Atencion!","readyState: "+xhr.readyState+"\nstatus: "+xhr.status+"\n \n responseText: "+xhr.responseText,"error");
    })




});
// $("#btn_excel_track").click(function(){
  $(document).on('click', '#btn_chart_track', function(){
    $("#div_graficos").removeClass("hide");
    $("#table_agente_central").addClass("hide");
    


		var $loader = $('.loader');
		let base_url = $("#base_url").val();
	
		$.ajax({
			dataType: "JSON",
			data:$('#form_search').serialize(),
			url: base_url + '/api/ApiNotificaciones/getTracksStats',
			type: 'POST',
			beforeSend: function() {
					$("#compose-modal-wait").modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    });
                    $loader.show();
				}
			,success: function(respuesta){
				setTimeout(function(){
                    $loader.hide();
                    $("#compose-modal-wait").modal('hide');
                    }, 700);
                    // console.log(respuesta)
                    //  console.log(respuesta['dato']['rs_response'])
                    //  console.log(respuesta['dato']['rs_response'][0]['cantindad'])
                  
                    $('#graph').empty();
                    $('#graph').append('<canvas id="myChartBar" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 812px;" width="885" height="272"></canvas>');
                    var labelsBar = [];
                    var dataBarDonut = [];
                    var dataBar = [];
                    var labelsBarDonut = [];
                  


                    respuesta['dato']['rs_response'].map(function (dato, index) {
                      
                      labelsBar[index] = dato['nombre_grupo']+ ":" +dato['origen'] ;
                      // console.log(labelsBar)
                      dataBar[index] = parseInt(dato['cantidad'])         
                      // console.log(dataBar)
                    });
                    
                    renderGraph('myChartBar', 'bar', dataBar, labelsBar);
                    // console.log(respuesta['dato']['rs_response'])
                    // console.log(respuesta['dato']['rs_response_donus'])
                    respuesta['dato']['rs_response_donus'].map(function (dato, index) {
                      
                      labelsBarDonut[index] = dato['origen'];
                          // console.log(labelsBar)
                      dataBarDonut[index] = parseInt(dato['cantidad'])        
                      // console.log(dataBar)
                      renderDonusGraph('donutChart', 'bar', dataBarDonut, labelsBarDonut);
                    });
                   
                    // console.log(labelsBar)
                    // console.log(dataBar)
                    // console.log(datasets)
                    
                   
                     
                      
                      // 

                    
				$('#settings_and_table_container').css('display','block');
				$('#modalSetDataMarketing').modal('hide');
			},error: function (respuesta) {
					setTimeout(function(){
                    $loader.hide();
                    $("#modalLoading").modal('hide');
                    }, 700);
					console.log("respuesta error");
					// console.log(respuesta) ;
			}
		});

	});

  //Render graficos y opciones para la grafica
  function renderGraph(el, type, data, labelData) {
    let ctx = $('#'+el).get(0).getContext('2d');
 
    // console.log(labelData)
        backgroundColorBar = [
                      'rgba(255, 99, 132, 0.75)', // red
                      'rgba(139, 195, 74, 0.75)', // green
                      'rgba(54, 162, 235, 0.75)', // blue
                      'rgba(75, 192, 192, 0.75)', // green
                      'rgba(255, 206, 86, 0.75)', // yellow
                      'rgba(153, 102, 255, 0.75)', // purple
                      'rgba(255, 159, 64, 0.75)', // orange
                      'purple',
                      'blue',
                      'yellow',
                      'rgba(191, 0, 0, 0.75)', // otro rojo
                      'red',
                      'orange'
                    ];

        datasetsBar = [
                      {
                        label: '',
                        data: data,						
                        backgroundColor: backgroundColorBar,
                        borderWidth: 1,
                        datalabels: {
                          anchor: 'end',
                          align: 'end',
                          offset: 10,
                          labels: {
                            value: {
                              font: {
                                weight: 'bold',
                              }
                            }
                          },
                          formatter: function(value, context) {
                            return value;
                          }
                        }  
                      }
                    ];

                    // if (myChartBar) {
                    //   myChartBar.destroy();
                    // }
                   var myChartBar = new Chart(ctx, {
                      type: type,
                      data: {
                          labels: labelData,
                          datasets: datasetsBar
                      },
                      options: {
                        tooltips: {enabled: true},
                        // hover: {mode: null},
                          plugins: {
                              datalabels: {
                                  color: '#000000',
                                  align: 'end',
                              }
                          },
                          responsive              : true,
                          maintainAspectRatio     : false,
                          datasetFill             : false,
                        
                        
                          legend: {
                            display:false
                          },
                          title: {
                            display: true,
                            text: `TABLERO DE GRUPOS`
                          },
                        onClick:function(evt){
                          var origen = $("#sl_sr_canal").val();

                          if (origen == ""){
                                  Swal.fire({
                                      title: 'Información',
                                      text: 'Recuerde que para generar el cuadro estadistico detallado de las palabras debera seleccionar antes un canal de origen de las notificaciones a consultar.',
                                      icon: 'info',
                                      confirmButtonText: 'Aceptar',
                                      // cancelButtonText: 'Cancelar',
                                      showCancelButton: 'false'
                                  })
                          }else{

                            var activePoints = myChartBar.getElementsAtEventForMode(evt, 'point', myChartBar.options);
                            var firstPoint = activePoints[0];
                            var label = myChartBar.data.labels[firstPoint._index];
                            var value = myChartBar.data.datasets[firstPoint._datasetIndex].data[firstPoint._index];
                            // alert(label + ": " + value);
                            var arrayForm = $('#form_search').serialize();
                            renderTableWords(arrayForm,label,origen,value)
                            let datospie = myChartBar.data.datasets[0].data
                            let cienporciento=0
                            let totalSolicitudes = 0;
                            datospie.forEach(function(a){cienporciento += a;});
                          
                            $('#graph2').empty();
                            $('#graph2').append('<canvas id="myChartPie" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%; display: block; width: 812px;" width="885" height="272"></canvas>');
                            var labelsPie = ["Notificaciones Totales","Grupo "+label];
                            var dataPie = [];
  
                            var data2 = [{
                                data: [cienporciento,value],
                                labels: labelsPie,
                                backgroundColor: [
                                    "#4b77a9",
                                    "#5f255f",
                                    "#d21243",
                                    "#B27200"
                                ],
                                borderColor: "#fff"
                            }];

                            

                            // console.log(data2)
  
                            var options = {
                                
                                plugins: {
                                    datalabels: {
                                        formatter: (value, ctx) => {
                                          // console.log(value)
                                          if (typeof value === 'number')
                                          {
                                            let sum = 0;
                                            let dataArr = ctx.chart.data.datasets[0].data;
                                            dataArr.map(data => {
                                                sum += data;
                                            });
                                            let percentage = (value*100 / sum).toFixed(2)+"%";
                                            return percentage;
                                          }else{
                                            return value
                                          }

                                           
                                        },
                                        color: '#fff',
                                    }
                                },
                                  title: {
                                    display: true,
                                    text: `GRUPO SOBRE LA BASE TOTAL DE NOTIFICACIONES`
                                  },
                            };
  
                            var ctx = document.getElementById("myChartPie").getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: labelsPie,
                                    datasets: data2
                                },
                                options: options
                            });
                          }
                        
                        }
                      }
                });

               

      
    }

   
    function renderDonusGraph(el, type, data, labelData) {
        // let ctx = $('#'+el).get(0).getContext('2d');
        var donutChartCanvas =  $('#'+el).get(0).getContext('2d');
    
        var donutData        = {
          labels: labelData,
          datasets: [
            {
              data: data,
              backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#d2d6de'],
            }
          ]
        }
        var donutOptions     = {
          maintainAspectRatio : false,
          responsive : true,
        }
        //Create pie or douhnut chart
        // You can switch between pie and douhnut using the method below.
        var myChart =  new Chart(donutChartCanvas, {
          type: 'doughnut',
          data: donutData,
          options: donutOptions,
          title: {
            display: true,
            text: `ALERTAS POR ORIGEN`
				  },
          onClick:function(e){
                  
          }
        })

        // $("#"+el).click(function(evt){
        document.getElementById(el).onclick = function (evt) {
            var activePoints = myChart.getElementsAtEventForMode(evt, 'point', myChart.options);
            var firstPoint = activePoints[0];
            var label = myChart.data.labels[firstPoint._index];
            var value = myChart.data.datasets[firstPoint._datasetIndex].data[firstPoint._index];
            alert(label + ": " + value);
        };

    }

    //-------------
    //- donut CHART -
    //-------------
   
function renderTableWords(arrayForm,label,origen,value)
{
  let base_url = $("#base_url").val();
  $("#tot_notificaciones").html("Palabras usadas en el grupo seleccionado: "+value+" notificaciones ");
  // // Obtener la referencia del elemento body
  var body = document.getElementById("graph3");
  // const formDataGrupo = new FormData();
  //       formDataGrupo.append("dataForm", arrayForm);
  //       formDataGrupo.append("id", label);
  //       formDataGrupo.append("origen", id_medio);

var dataform = $('#form_search').serialize();
  

  $.ajax({
            type: "POST",
            dataType: "JSON",
            url: base_url + 'api/ApiNotificaciones/mostrarPalabras2',
            data:{dataForm:dataform,id:label,origen:origen},
            success: function (response) {
              var wordtable='';
              var registros3 = eval(response.data);
              //  console.log(response.data)
              wordtable += '<div class="row" style="overflow-y:scroll;position:relative;height: 200px;">';
             


              for (var i = 0; i < registros3.length ; i++) {
                var porcentaje = formaterPercentange(value, registros3[i]['cantidad']) 
                      let percentage = (registros3[i]['cantidad']*100 / value).toFixed(2)
                      if (percentage <  10)
                      {
                        var color = "green"
                      }else if (percentage >  10 && percentage <  30){
                        var color = "blue"
                      }else if (percentage >  30 && percentage <  60){
                        var color = "yellow"
                      }else if (percentage >  60 && percentage <  100){
                        var color = "red"
                      

                      }
                wordtable += '<div class="col-md-3">';
                wordtable += `<div class="grillas-words"><strong>${registros3[i]['palabras']}</strong><span class="badge bg-${color}">${registros3[i]['cantidad']}</span></div>`;
                wordtable += '</div>';
              }
              wordtable += '</div>';
              $("#graph3").html(wordtable);

            }
        });
}

function formaterPercentange(sum, value)
{
 
  let percentage = (value*100 / sum).toFixed(2)+"%";
  return percentage;
}

$("#sl_medio").change(function(){
      let base_url = $("input#base_url").val();
      var medio = $("#sl_medio").val();
      $('#id_medio').select2('data', null)
    //   alert(medio);
      if(medio == "SLACK")
      {
          $('#id_medio').attr('placeholder','ID SLACK');
      }else if(medio == "EMAIL"){
            $('#id_medio').attr('placeholder','DIRECCION EMAIL');
      }else if(medio == "SMS"){
            $('#id_medio').attr('placeholder','NUM TELF');

      }
      base_url = base_url +"api/ApiNotificaciones/searchMedio";
      $.ajax({
            type: "POST",
            url: base_url,
            data: {service:medio},
            success: function (response) {
              html = "";
              var registros = eval(response.data);
              if (medio =="SLACK")
              {
                for (var i = 0; i < registros.length; i++) {
                  $("#id_medio").append( '<option value="'+registros[i]['slack_id']+'">'+registros[i]['nombre']+ " - " + registros[i]['email']+'</option>')
                }

              }else if(medio =="EMAIL"){
                for (var i = 0; i < registros.length; i++) {
                  $("#id_medio").append( '<option value="'+registros[i]['mail']+'">'+registros[i]['nombre_pila']+ " - " + registros[i]['mail']+'</option>')
                }
              }else if(medio =="SMS"){
                for (var i = 0; i < registros.length; i++) {
                  $("#id_medio").append( '<option value="'+registros[i]['wathsapp']+'">'+registros[i]['nombre_pila']+ " - " + registros[i]['wathsapp']+'</option>')
                }
              }
              
            }
        });

});
    $('#registrar-grupo').click(function (event){
      let base_url = $("input#base_url").val();
      let name_group = $("#name_group").val();
      let sl_medio = $("#sl_medio").val();
      let id_medio = $("#id_medio").select2('val');
      let sl_metodo = $("#sl_metodo").val();
      let sl_action = $("#sl_action").val();


      base_url = base_url + "api/ApiNotificaciones/insertGrupo";

        const formDataGrupo = new FormData();
        formDataGrupo.append("name_group", name_group);
        formDataGrupo.append("sl_medio", sl_medio);
        formDataGrupo.append("id_medio", id_medio);
        formDataGrupo.append("sl_metodo", sl_metodo);
        formDataGrupo.append("sl_action", sl_action);
        console.log(formDataGrupo)
        $.ajax({
            type: "POST",
            url: base_url,
            // data:$('#form_notificaciones').serialize(),
            // dataType: "JSON",
            data: formDataGrupo,
            processData: false,
            contentType: false,
            success: function (response) {
              if (response.ok) {
                Swal.fire({
                    title: 'Correcto',
                    text: response.data,
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                    // cancelButtonText: 'Cancelar',
                    showCancelButton: 'false'
                })
                initTableAgenteCentral()
                
              }else{
                Swal.fire({
                    title: 'Error',
                    text: 'Erro al intentar insertar palabra en la base',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    // cancelButtonText: 'Cancelar',
                    showCancelButton: 'false'
                })
              }
            }
        });


    });
 
$('#actualizar-grupo').click(function (event){
      $("#registrar-grupo").css("display", "block");
      $("#actualizar-grupo").css("display", "none");
    
    let id_grupo_hd = $("#id_grupo_hd").val();
    let name_group = $("#name_group").val(); 
    let sl_medio = $("#sl_medio").val();   
    let id_medio = $("#id_medio").val();  
    let sl_metodo = $("#sl_metodo").val();  
    let sl_action = $("#sl_action").val();  

    Swal.fire({
        title: 'Cambio de estado',
        text: 'Estas seguro de que quieres ACTUALIZAR grupo?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {
            var id = $("#actualizar-agente").data("id");
            const formData = new FormData();
            formData.append("id_grupo", id_grupo_hd);
            formData.append("name_group", name_group);
            formData.append("sl_medio", sl_medio);
            formData.append("id_medio", id_medio);
            formData.append("sl_metodo", sl_metodo);
            formData.append("sl_action", sl_action);
            var base_url = $("input#base_url").val() + "api/ApiNotificaciones/grupo_update";
            $.ajax({
                type: "POST",
                url: base_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log(response)
                    if (response.errors) {
                        toastr.error("No se pudo actualizar el Agente", "ERROR");
                    } else {
                        toastr.success("Se actualizo correctamente", "Actualizado");
                        
                            $("#name_group").val("");
                            $("#sl_medio").val("");
                            $("#id_medio").val("");
                            $("#sl_metodo").val("");
                            $("#sl_action").val("");
                        initTableAgenteCentral();
                    }
                }
            });
        }
    });

});


    $('#registrar-palabra').click(function (event){
    let base_url = $("#base_url").val();
    let txt_palabra = $("#txt_palabra").val();
    let hd_group = $("#hd_group").val();
    let origen = $("#txt_origen").val();
    // console.log({palabra:txt_palabra,grupo:hd_group})
    // if (txt_palabra=="") {
        base_url = base_url + "api/ApiNotificaciones/insertWord";

        const formDatainsert = new FormData();
        formDatainsert.append("txt_palabra", txt_palabra);
        formDatainsert.append("hd_group", hd_group);
        formDatainsert.append("origen", origen);



        $.ajax({
            type: "POST",
            url: base_url,
            data: formDatainsert,
            processData: false,
            contentType: false,
            success: function (response) {
              if (response.ok) {
                Swal.fire({
                    title: 'Correcto',
                    text: response.data,
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                    // cancelButtonText: 'Cancelar',
                    showCancelButton: 'false'
                })
                mostrarPalabras(hd_group)
                
              }else{
                Swal.fire({
                    title: 'Error',
                    text: 'Erro al intentar insertar palabra en la base',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    // cancelButtonText: 'Cancelar',
                    showCancelButton: 'false'
                })
              }
            }
        });
    // }else{
    //     Swal.fire({
    //         title: 'Error',
    //         text: 'Intento de registro de palabra fallido motivo columnas vacias',
    //         icon: 'error',
    //         confirmButtonText: 'Aceptar',
    //         // cancelButtonText: 'Cancelar',
    //         showCancelButton: 'false'
    //     })
    // }

});
$('body').on('click','#palabras_table a[id="btn_editar_palabra"]',function(event){
  
  

  event.preventDefault();
      let palabra = $(this).attr('data-palabra');
      let hd_group = $("#hd_group").val();
      let base_url = $("#base_url").val();
      let origen = $("#txt_origen").val();
      let new_word = $("#txt_palabra").val();
          base_url = base_url + "api/ApiNotificaciones/editWord";
  if (new_word != "") 
  {
    Swal.fire({
        title: 'Edicion de Palabra',
        text: 'Estas seguro de que quieres ACTUALIZAR esta palabra ('+palabra+') por ('+new_word+') ?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {

         

                const formDatainsert = new FormData();
                formDatainsert.append("txt_palabra", palabra);
                formDatainsert.append("hd_group", hd_group);
                formDatainsert.append("origen", origen);
                formDatainsert.append("new_word", new_word);
                $.ajax({
                    type: "POST",
                    url: base_url,
                    data: formDatainsert,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                      if (response.ok) {
                        Swal.fire({
                            title: 'Correcto',
                            text: response.data,
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                            // cancelButtonText: 'Cancelar',
                            showCancelButton: 'false'
                        })
                        toastr.success("Se actualizo correctamente", "Actualizado");
                        mostrarPalabras(hd_group)
                        
                      }else{
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al intentar editar palabra en la base',
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            // cancelButtonText: 'Cancelar',
                            showCancelButton: 'false'
                        })
                        toastr.error("No se pudo actualizar el Agente", "ERROR");
                      }
                    }
                });


            
        }
    });



  }else{
    Swal.fire({
        title: 'Error',
        text: 'Requiere completar el campo PALABRA con el texto que reemplazara este registro',
        icon: 'error',
        confirmButtonText: 'Aceptar',
        // cancelButtonText: 'Cancelar',
        showCancelButton: 'false'
    })
    $("#txt_palabra").focus();
  }
      
  
  });

$('body').on('click','#palabras_table a[id="btn_eliminar_palabra"]',function(event){
  
  event.preventDefault();
      let palabra = $(this).attr('data-palabra');
      let hd_group = $("#hd_group").val();
      let base_url = $("#base_url").val();
      let origen = $("#txt_origen").val();
      let new_word = $("#txt_palabra").val();
          base_url = base_url + "api/ApiNotificaciones/deleteWord";
  
    Swal.fire({
        title: 'Edicion de Palabra',
        text: 'Estas seguro de que quieres ELIMINAR esta palabra la siguiente accion es permanente y dejara de filtrar esta palabra ('+palabra+')?',
        icon: 'warning',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        showCancelButton: 'true'
    }).then((result) => {
        if (result.value) {

         

                const formDatainsert = new FormData();
                formDatainsert.append("txt_palabra", palabra);
                formDatainsert.append("hd_group", hd_group);
                formDatainsert.append("origen", origen);
                formDatainsert.append("new_word", new_word);
                $.ajax({
                    type: "POST",
                    url: base_url,
                    data: formDatainsert,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                      if (response.ok) {
                        Swal.fire({
                            title: 'Correcto',
                            text: response.data,
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                            // cancelButtonText: 'Cancelar',
                            showCancelButton: 'false'
                        })
                        toastr.success("Se elimino correctamente", "Eliminado");
                        mostrarPalabras(hd_group)
                        
                      }else{
                        Swal.fire({
                            title: 'Error',
                            text: 'Error al intentar eliminar palabra en la base',
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            // cancelButtonText: 'Cancelar',
                            showCancelButton: 'false'
                        })
                        toastr.error("No se pudo actualizar el Agente", "ERROR");
                      }
                    }
                });


            
        }
    });



  
      

});


function buscarmedios()
{
  let base_url = $("input#base_url").val();
      var medio = $("#sl_medio").val();
      $('#id_medio').select2('data', null)
    //   alert(medio);
      if(medio == "SLACK")
      {
          $('#id_medio').attr('placeholder','ID SLACK');
      }else if(medio == "EMAIL"){
            $('#id_medio').attr('placeholder','DIRECCION EMAIL');
      }else if(medio == "SMS"){
            $('#id_medio').attr('placeholder','NUM TELF');

      }
      base_url = base_url +"api/ApiNotificaciones/searchMedio";
      $.ajax({
            type: "POST",
            url: base_url,
            data: {service:medio},
            success: function (response) {
              html = "";
              var registros = eval(response.data);
              if (medio =="SLACK")
              {
                for (var i = 0; i < registros.length; i++) {
                  $("#id_medio").append( '<option value="'+registros[i]['slack_id']+'">'+registros[i]['nombre']+'</option>')
                }

              }else if(medio =="EMAIL"){
                for (var i = 0; i < registros.length; i++) {
                  $("#id_medio").append( '<option value="'+registros[i]['mail']+'">'+registros[i]['nombre_pila']+'</option>')
                }
              }else if(medio =="SMS"){
                for (var i = 0; i < registros.length; i++) {
                  $("#id_medio").append( '<option value="'+registros[i]['wathsapp']+'">'+registros[i]['nombre_pila']+'</option>')
                }
              }
              
            }
        });
}

</script>