<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<script src="<?php echo base_url(); ?>assets/datatables/js/dataTables.keyTable.min.js"></script>
<script src="<?php echo base_url('assets/supervisores/gestionar.js?'.microtime());?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>
<style type="text/css">
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



</style>
<?php 

/*echo '<pre>'; print_r($solicitudes_status); echo '</pre>';

echo '<pre>'; print_r($solicitudes_types); echo '</pre>';

echo '<pre>'; print_r($operators); echo '</pre>';*/
$block_input_status = [];
$block_type_solicitud = [];
$block_operator = [1];
$block_date_range = [];
?>
<input type="hidden" name="base_url" id="base_url" value="<?php echo base_url();?>">
<div id="section_search_solicitud" style="background: #FFFFFF;">
        
        
        <form id="form_search" name="form_search"  method="POST">

            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><strong>CRITERIOS PARA DISTRIBUCION DE LA MORA COBRANZA</strong></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form">
                <div class="card-body">
                  
                
                <!-- FIN SECCION -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Condicion Soliticitud:</label>
                            <div class="input-group">
                               <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <select class="form-control select2-multiple" name="sl_condicion[]" id="sl_condicion" multiple="multiple">
                                        
                                        <option value="vigente"> VIGENTE </option>
                                        <option value="mora"> MORA </option>
                                        
                                        
                                    </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Filtros:</label>
                            <div class="input-group">
                               <span class="input-group-addon"><i class="fa fa-filter"></i></span>
                                   <select class="form-control" name="sl_antiguedad" id="sl_antiguedad">
                                        <option value="0"> TODOS </option>
                                        <option value="cd.dias_atraso"> DIAS ATRASO</option>
                                        <option value="cd.fecha_vencimiento"> FECHA DE VENCIMIENTO</option>
                                        <option value="cd.monto_cobrar"> MONTO A COBRAR </option>
                                    </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Lógico:</label>
                            <div class="input-group">
                               <span class="input-group-addon"><i class="fa fa-random"></i></span>
                                    <select class="form-control" name="sl_logica" id="sl_logica">
                                        <option value="0">.:LOGICO:.</option>
                                        <option value="IGUAL_A"> IGUAL A </option>
                                        <option value="MAYOR_A"> MAYOR A </option>
                                        <option value="MENOR_A"> MENOR A </option>
                                        <option value="DISTINTO_A"> DISTINTO A </option>
                                        <option value="ENTRE"> ENTRE </option>
                                        
                                    </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor:</label>
                                <input class="form-control entero " type="text" id="dias_atrasoA" name="dias_atrasoA"  autocomplete="off">
                                <input class="form-control moneda hide" type="text" id="currency_rangeA" name="currency_rangeA"  autocomplete="off">
                                <input class="form-control hide" type="text" id="date_rangeA" name="date_rangeA"  autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Valor:</label>
                            <input class="form-control entero" type="text" id="dias_atrasoB" name="dias_atrasoB"  autocomplete="off">
                            <input class="form-control moneda hide" type="text" id="currency_rangeB" name="currency_rangeB"  autocomplete="off">
                            <input class="form-control hide" type="text" id="date_rangeB" name="date_rangeB"  autocomplete="off">
                        </div>
                    </div>
                    
                </div>
                <!-- FIN SECCION -->
                
                <!-- FIN SECCION -->
                
                <div class="row" id="div_distributionx">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Equipo:</label>
                            <div class="input-group">
                               <span class="input-group-addon"><i class="fa fa-users"></i></span>
                                    <select class="form-control" name="sl_equipo_asig" id="sl_equipo_asig">
                                        <option value="TODOS" selected="selected"> TODOS </option>
                                        <option value="COLOMBIA"> COLOMBIA </option>
                                        <option value="ARGENTINA"> ARGENTINA</option>
                                        
                                    </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
                

                
                
            </div><!-- /.card-body -->
                 
                

                <div class="card-footer">
                   
        <hr>


                    <div class="row">
                        <div class="col-md-12">
                            <button  type="button" id="btn_preview " class="btn btn-info" onclick="validaciones_btn('btn_preview');" title="Buscar" style="font-size: 12px;"><i class="fa fa-search"></i> Vista Previa</button>
                            <button  type="button" id="btn_distriubcion " class="btn btn-success" onclick="validaciones_btn('btn_distriubcion');" title="Exportar Excel" style="font-size: 12px;"><i class="fa fa-users"></i> Distribuir casos</button>
                            <button  type="reset" id="btn_limpar_base" class="btn btn-default" title="Limpiar" onclick="validaciones_btn('btn_limpar_base');" style="font-size: 12px;"><i class="fa fa- fa-remove"></i> Limpiar Base</button>
                        </div>
                    </div>
                </div>
              </form>
            </div>
            
        

            
        <div class="row">
            <div class="col-md-12">
                
           
                

                
                
                
                
            </div>
            
        </div>


        <div class="row">
            <div class="col-md-12" id="seccion_operadores"></div>
        </div>

        <div class="row">
            <div class="col-md-12">

            
                
                
                <!--input name ="operador_asignado" hidden value="<?php echo $this->session->userdata['idoperador']?>">
                <select disabled class="form-control">
                    <option selected value="<?php echo $this->session->userdata('tipo_operador'); ?>"><?php echo $this->session->userdata('user')->first_name;?></option>                   
                </select-->
            
            
                
                
                
                

                
            </div>
        </div>

            
        </form>
        <div id="result" style="display: none">
            <table align="center" id="table_search" class="table table-responsive table-striped table=hover display" width="100%" >
                <thead style="font-size: smaller; ">
                    <tr class="info">
                        <th></th>
                        <th style="text-align: center;">N°</th>
                        <th style="text-align: center;">Fecha</th>
                        <th style="text-align: center;">Hora</th>
                        <th style="text-align: center;">Documento</th>
                        <th style="text-align: center;">Solicitante</th>
                        <th style="text-align: center;">Tipo</th>
                        <th style="text-align: center;">Buro</th>
                        <th style="text-align: center;">Cuenta</th>
                        <th style="text-align: center;">Reto</th>
                        <th style="text-align: center;">Estado</th>
                        <th style="text-align: center;">Operador</th>
                        <th style="text-align: center;">Última Gestion</th>
                    </tr>
                </thead>
                <tbody style="font-size: 12px; text-align: center;" id="tb_body">
                </tbody>
            </table>
        </div>
</div>


<div class="modal fade" id="compose-modal-plantillas" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" style="width:85%;font-size: 11px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-tags"></i> BUSQUEDA DE PLANTILLAS </h4>


                        <div class="col-md-12 hide" id="succes">
                            <!-- Primary box -->
                            <div class="box box-solid box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">BUSQUEDA DE PLANTILLAS</h3>
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

        <div class="card">
            
            <div class="row">
                <div class="col-md-12">
                    <div id="Res_plantillas_list" class="col-lg-12" >

                    </div>
                </div>
            </div>
         </div>
    </div>





                    <div class="modal-footer clearfix">


                    </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<div class="modal fade" id="compose-modal-wait" tabindex="-1" role="dialog" aria-hidden="true">
           <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        
                        <h4 class="modal-title"><i class="fa fa-time"></i> POR FAVOR ESPERE MIENTRAS SE GENERA SU BUSQUEDA </h4>


                        <div class="col-md-12 hide" id="succes">
                            <!-- Primary box -->
                            <div class="box box-solid box-primary">
                                <div class="box-header">
                                    <h3 class="box-title">BUSQUEDA DE PLANTILLAS</h3>
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



<script type="text/javascript">
    $('document').ready(function(){
 $('#tp_campanias').DataTable(
  {
    "language": {
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
    'pageLength': 10,
    'order': [[ 0, "asc" ]],
    'columns':[
    {'data':'id_cliente'},
    {'data':'nombres'},
    {'data':'apellidos'},
    {'data':'documento'},
    {'data':'monto_cobrar'},
    {'data':'dias_atraso'},
    {"data":"telefono_0", 
        "render": function(data, type, row, meta)
        {
           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    },
    {"data":"telefono_1", 
        "render": function(data, type, row, meta)
        {
           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    },
    {"data":"telefono_2", 
        "render": function(data, type, row, meta)
        {
           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    },
    {"data":"telefono_3", 
        "render": function(data, type, row, meta)
        {
           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    },
    {"data":"telefono_4", 
        "render": function(data, type, row, meta)
        {
           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    },
    {"data":"telefono_5", 
        "render": function(data, type, row, meta)
        {
           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    },
    {"data":"telefono_6", 
        "render": function(data, type, row, meta)
        {
           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    },
    {"data":"telefono_7", 
        "render": function(data, type, row, meta)
        {
           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    },
    {"data":"telefono_8", 
        "render": function(data, type, row, meta)
        {
           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    },
    {"data":"telefono_9", 
        "render": function(data, type, row, meta)
        {

           var respuesta = data;
            if(data == ""){
                respuesta = " ";
            }

            return respuesta;
        }
    }
                
        ],

        "columnDefs": [ 
            {
                "targets": 0,
                "orderable": false,
                "createdCell": function(td, cellData, rowData, row, col)
                {
                    $(td).attr('style', 'width: 2%; text-align: center;'); 
                }

            },
            
            {
                "targets": [1,2,3,4],
                "createdCell": function(td, cellData, rowData, row, col)
                {
                    $(td).attr('style', 'width: 10%; text-align: center;'); 
                    
                }
            },
            {
                "targets": 5,
                "createdCell": function(td, cellData, rowData, row, col)
                {
                    $(td).attr('style', 'text-align: center; width: 10%;'); 
                }
            },
            {
                "targets": [6,8,11],
                "createdCell": function(td, cellData, rowData, row, col)
                {     
                    $(td).attr('style', 'width: 7%;'); 
                }
            },
            {
                "targets": [7,9,10],
                "createdCell": function(td, cellData, rowData, row, col)
                {     
                    $(td).attr('style', 'width: 1%;'); 
                }
            }        
        ],
        
      });
   

    $("#section_search_solicitud #form_search").on('reset', function(event){
        $("#section_search_solicitud #result").hide();
        $("#tabla_solicitudes").show();
        $("#tabla_desembolso").show();
        $("#botones_filtro").show();
    });

    $("#section_search_solicitud #search").on('keyup', function(){
            if($(this).val().length == 0)
            {
                $("#section_search_solicitud #result").hide();
                $("#tabla_solicitudes").show();
                $("#tabla_desembolso").show();
                $("#botones_filtro").show();
                $("#texto").empty();
            }else{

                $("#tabla_solicitudes").hide();
                $("#tabla_desembolso").hide();
                $("#botones_filtro").hide();              
            }
    });
   $('#date_rangeA,#date_rangeB').datepicker(
        {
            autoUpdateInput: false,
           
            "locale": 
            {
                "format": "DD-MM-YYYY",
                "daysOfWeek": ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
                "firstDay": 1
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment(),
            timePicker: false,
        }

        
    );
    //Date range as a button
    $('#date_range').daterangepicker(
        {
            autoUpdateInput: false,
            ranges   : 
            {
              'Hoy'       : [moment(), moment()],
              'Ayer'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
              'Últimos 7 Días' : [moment().subtract(6, 'days'), moment()],
              'Últimos 30 Días': [moment().subtract(29, 'days'), moment()],
              'Mes Anterior'  : [moment().startOf('month'), moment().endOf('month')],
              'Últimos Mes'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "locale": 
            {
                "format": "DD-MM-YYYY",
                "separator": " | ",
                "applyLabel": "Guardar",
                "cancelLabel": "Cancelar",
                "fromLabel": "Desde",
                "toLabel": "Hasta",
                "customRangeLabel": "Personalizar",
                "daysOfWeek": ["Do","Lu","Ma","Mi","Ju","Vi","Sa"],
                "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
                "firstDay": 1
            },
            startDate: moment().subtract(29, 'days'),
            endDate  : moment(),
            timePicker: false,
        },

        function (start, end) 
        {
            $('#daterange-btn span').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'))
        }
    );

    $('#date_range').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' | ' + picker.endDate.format('DD-MM-YYYY'));
        });

    $('#date_range').on('cancel.daterangepicker', function(ev, picker)
    {
      $(this).val('');
    });


})
</script>
