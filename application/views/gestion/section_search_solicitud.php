<style>
    a.disabled { 
            pointer-events: none; 
            cursor: not-allowed;
        } 
</style>
<script type="text/javascript" src="<?php echo base_url('assets/daterangepicker/js/daterangepicker.min.js');?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/daterangepicker/css/daterangepicker.css') ?>"/>
<?php 
$block_input_status = [];
$block_type_solicitud = [];
$block_operator = [1];
$block_date_range = [];
$id_operador = $this->session->userdata['idoperador'];
?>
<div id="section_search_solicitud" style="background: #FFFFFF; margin-top:1%">
        <form id="form_search" class="form-inline col-md-12 " style="padding:0px;" method="POST">
            <input id="search" name="search" type="text" class="form-control" placeholder="ID / CEDULA / Nombre Cliente / Telefono / Mail a BUSCAR" style="width: 15%;">
            <select class="form-control" name="criterio" style="width: 12%;">
                        <option value="" disabled>.:CRITERIO:.</option>
                        <option value="id">ID Solicitud</option>
                        <option value="telefono" >Telefono</option>
                        <option value="documento" selected>Documento</option>
                        <option value="nombre">Nombre</option>
                        <option value="apellido" >Apellido</option>
                        <option value="email" >Email</option>
                        
            </select>
            <?php if(!in_array($this->session->userdata('tipo_operador'),$block_input_status)): ?>
                <select class="form-control" name="estado" style="width: 12%;">
                    <option value="">.:ESTADO:.</option>
                    <?php 
                        if($this->session->userdata('tipo_operador') == '11'){?>
                            <option value="VISADO">APRO. VISADOS</option>
                            <option value="NOVISADO">APRO. NO VISADOS</option>
                    <?php    
                    }
                    foreach ($solicitudes_status as $index => $status): ?>
                        <optgroup label="<?php echo ucfirst($index); ?>">
                        <?php foreach ($status as $key => $value): ?>
                            <option value="<?php echo strtoupper($value['value']); ?>"><?php echo mb_strtoupper($value['label'])?></option>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
           
            <?php if(!in_array($this->session->userdata('tipo_operador'),$block_type_solicitud)): ?>
                <select class="form-control" name="tipo_solicitud">
                    <option value="">.:TIPO:.</option>
                    <?php foreach ($solicitudes_types as $key => $value): ?>
                        <option value="<?php echo strtoupper($value['value']); ?>"><?php echo mb_strtoupper($value['label'])?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            
            <?php if(!in_array($this->session->userdata('tipo_operador'),$block_operator)): ?>            
                <select class="form-control" name="operador_asignado" style="width: 15%;">
                    <option value="">.:OPERADOR:.</option>
                    <?php foreach ($operators as $key => $operator): ?>
                        <option value="<?php echo strtoupper($operator['idoperador']); ?>"><?php echo mb_strtoupper($operator['nombre_pila'])?></option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input name ="operador_asignado" hidden value="<?php echo $id_operador?>">
                <select disabled class="form-control">
                    <option selected value="<?php echo $this->session->userdata('tipo_operador'); ?>"><?php echo $this->session->userdata('user')->first_name;?></option>                   
                </select>
            <?php endif; ?>
            <?php if(!in_array($this->session->userdata('tipo_operador'),$block_date_range)): ?>
                <input type="text" id="date_range" style="width: 15%;" name="date_range" class="form-control" autocomplete="off">
            <?php endif; ?>
            <button  type="submit" class="btn btn-primary" title="Buscar" style="font-size: 10px;"><i class="fa fa-search"></i></button>
            <button  type="reset" class="btn btn-default" title="Limpiar" style="font-size: 10px;"><i class="fa fa- fa-remove"></i></button>
            <?php 
                if(($this->session->userdata('tipo_operador') == 11) 
                || ($this->session->userdata('tipo_operador') == 2) 
                ||($this->session->userdata('tipo_operador') == 9) ): 
            ?>
                <button id="btnPorVisar"  type="button" class="btn btn-warning mostratTitulo" title="Por visar" style="font-size: 10px;" onclick="casosPorVisar();"><i class="fa fa-list"></i></button>
                <button type="button" id="mostratTodasLasSolicitudes" class="btn btn-warning" title="Todas" onclick="tableInit(ocultar = true);" style="font-size: 10px;"><i class="fa fa-bars"></i></button>
            <?php endif; ?>
            <?php 
                if(($this->session->userdata('tipo_operador') == 1) 
                || ($this->session->userdata('tipo_operador') == 2) 
                ||($this->session->userdata('tipo_operador') == 4)
                ||($this->session->userdata('tipo_operador') == 5)
                ||($this->session->userdata('tipo_operador') == 9)
                ||($this->session->userdata('tipo_operador') == 12) ): 
            ?>
                <button type="button" id="gestionRechazada" class="btn btn-danger" title="Transferencias Rechazadas" onclick="transferenciaRechazada()" style="font-size: 10px;"><i class="fa fa-exclamation"></i></button>
            <?php endif; ?>             
        </form>


          
        


            <?php if($this->session->userdata('tipo_operador') == 11){ ?>
                <div class="col-md-2" style="padding:0px;" id="slc_bancos">
                    <select class="form-control" name="bancos" id="select-bancos" style="width: 80%; display:inline-block">
                        <option value="0">.:Todos los bancos:.</option>
                        <?php foreach ($bancos as $key => $banco): ?>
                            <option value="<?php echo strtoupper($banco['id_Banco']); ?>"><?php echo mb_strtoupper($banco['Nombre_Banco'])?></option>
                            <?php endforeach; ?>
                    </select>  
                        
                    <button id="buscar-banco" class="btn  btn-primary" onclick="" title="buscar"> <i class="fa fa-search"></i></button>
                </div>
            <?php } ?> 

            <!--Agrego filtros nuevos si el operador es tipo 1-->
            <?php if($this->session->userdata('tipo_operador') == 1){ ?>
                <div id="botones_filtro" class="col-md-3" style="margin-top: 7px;">
                    <button id="sol_hoy" class="btn btn-xs btn-info" onclick="listarxregistro('hoy')" title="Hoy">HOY</button>
                    <button id="sol_ayer" class="btn btn-xs btn-warning" onclick="listarxregistro('ayer')" title="Hoy">AYER</button>
                    <button id="sol_72" class="btn btn-xs btn-success" onclick="listarxregistro('72')" title="Hoy">72 HS</button>  
                    <button id="sol_validaciones" class="btn btn-xs btn-warning" onclick="listarxregistro('validaciones')" title="validaciones">VALIDACIONES <span class="badge"><?= $validaciones ?></span></button>
                </div>
            <?php } ?>   
            
            <?php if(($this->session->userdata('tipo_operador') == 11) 
				|| ($this->session->userdata('tipo_operador') == 2) 
				||($this->session->userdata('tipo_operador') == 9) ): ?>
			<div class="col-lg-12" id="porVisarTotal" style="padding:0!important">
				<div id="porVisarTableContenedor" class="col-lg-12" style="display:none;height:108px;padding-top:5px;border-bottom: 1px solid #f4f4f4;">
					<h5 id="tituloCasosVisar" class="col-md-12" style="display:none;margin-left:0%;background-color: #f7f1fb!important;font-weight: 500;height: 24px;padding-top: 4px;font-size: 15px;">CASOS POR VISAR</h5>
					
					<div class="col-md-10 row" style="margin-right: 0%;margin-left:-1%;padding-right: 0px;" id="porVisarTable">
						
					</div>
					
					<div class="col-md-2 row" id="porVisarTotalTable" style="margin-left:0; padding-left: 0px;padding-right: 0px;width:18%">
					
					</div>
				</div>
				
			</div>
			
        <?php endif; ?>     
        <div id="result" style="display: none">
        
            <table align="center" id="table_search" class="table table-responsive table-striped table=hover display" width="100%" >
                <thead class="info" style="font-size: smaller; ">
                        <th></th>
                        <th style="text-align: center;">N°</th>
                        <th style="text-align: center;">Fecha</th>
                        <th style="text-align: center;">Hora</th>
                        <th style="text-align: center;">Documento</th>
                        <th style="text-align: center;">Solicitante</th>
                        <th style="text-align: center;">Situción Laboral</th>
                        <th style="text-align: center;">Paso</th>
                        <th style="text-align: center;">Tipo</th>
                        <th style="text-align: center;">Buro</th>
                        <th style="text-align: center;">Cuenta</th>
                        <!-- <th style="text-align: center;">Reto</th> -->
                        <th style="text-align: center;">Estado</th>
                        <th style="text-align: center;">Operador</th>
                        <th style="text-align: center;">Última Gestion</th>
                </thead>
                <tbody style="font-size: 12px; text-align: center;">
                </tbody>
            </table>
        </div>
        
</div>

<script type="text/javascript">
    $('document').ready(function(){
        
        $.fn.dataTable.Api.register( 'processing()', function () {
            return this.iterator( 'table', function ( settings ) {
                settings.clearCache = true;
            } );
        } );
            // EVENTOS
            // Dibujo la busqueda de la solicitud
        $("#section_search_solicitud #result").hide();
            table_search = $("#section_search_solicitud #table_search").DataTable(
            {
                'iDisplayLength': 10,
                "responsive":true,
                //'dom': 'Bfrtip',
                "processing":true,
                "language": spanish_lang,
                'paging':true,
                'info':true,
                "searching": true,
                'columns':[
                    {"data":null, 
                        "render": function(data, type, row, meta)
                        {
                            return '<a href="#" class="btn btn-xs btn-primary solicitud" title="Consultar" onclick="consultar_solicitud('+data.id+')"><i class="fa fa-cogs"></i></a>';
                        },"orderable": false,
                    },
                    {"data":"id"},
                    {"data":"date_ultima_actividad"},
                    {"data":"hours_ultima_actividad"},
                    {"data":"documento"},
                    {"data":null,
                    "render":function(data, type, row, meta)
                    {
                        return data.nombres+' '+data.apellidos;
                    }
                    
                    },
                    {"data":"nombre_situacion",
                        // "render":function(data, type, row, meta){
                        //     if(data.nombre_situacion != null)
                        //     {
                        //         return data.nombre_situacion.toUpperCase().trim();

                        //     }else{
                        //         return '';
                        //     }
                        // }
                    },
                    {"data":"paso"},
                    {"data":"tipo_solicitud"},
                    {"data":null,
                            "render":function(data, type, row, meta)
                            {
                                if(data.respuesta_analisis != null)
                                {
                                    if(data.respuesta_analisis.toUpperCase()=="APROBADO")
                                    {
                                        return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                                    }else if(data.respuesta_analisis.toUpperCase()=="RECHAZADO")
                                    {
                                        return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                                    }else{
                                        return '';
                                    }
                                }else{
                                    return '';
                                }
                            },
                            "orderable": true
                    },
                    {"data":null,
                            "render":function(data, type, row, meta)
                            {
                                if(data.banco_resultado != null)
                                {
                                    if(data.banco_resultado.toUpperCase()=="ACEPTADA")
                                    {
                                        return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                                    }else if(data.banco_resultado.toUpperCase()=="RECHAZADA")
                                    {
                                        return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                                    }else{
                                        return '';
                                    }
                                }else{
                                    return '';
                                }
                            },
                            "orderable": false
                    },
                    /*{"data":null,
                        "render":function(data, type, row, meta)
                            {
                                if(data.resultado_ultimo_reto != null)
                                {
                                    if(data.resultado_ultimo_reto.toUpperCase()=="CORRECTA")
                                    {
                                        return '<i class="fa fa-check-square-o" style="color:green; font-size:20px"></i>';
                                    }else if(data.resultado_ultimo_reto.toUpperCase()=="INCORRECTA")
                                    {
                                        return '<i class="fa fa-close" style="color:red; font-size:20px"></i>';
                                    }else{
                                        return '';
                                    }
                                }else{
                                        return '';
                                }
                            },
                            "orderable": false
                    },*/
                    {"data":"estado"},
                    {"data":"operador_nombre_pila",},
                    {"data":"last_track","orderable": false}
            ],
            "columnDefs": [ 
                    {
                        "targets": 0,
                        "orderable": false,
                        "createdCell": function(td, cellData, rowData, row, col)
                        {
                            $(td).attr('style', 'width: 3%; text-align: center;'); 
                        }

                    },
                    {
                        "targets": 1,
                        "createdCell": function(td, cellData, rowData, row, col)
                        {
                            $(td).attr('style', 'width: 7%; text-align: center;'); 
                        }
                    },
                    {
                        "targets": [1,2,3,4],
                        "createdCell": function(td, cellData, rowData, row, col)
                        {
                            $(td).attr('style', 'width: 7%;'); 
                        }
                    },
                    {
                        "targets": 5,
                        "createdCell": function(td, cellData, rowData, row, col)
                        {
                            $(td).attr('style', 'text-align: left; width: 10%;'); 
                        }
                    },
                    {
                        "targets": [7,6,8,11,12,13],
                        "createdCell": function(td, cellData, rowData, row, col)
                        {     
                            $(td).attr('style', 'width: 7%;'); 
                        }
                    },
                    {
                        "targets": [9,10],
                        "createdCell": function(td, cellData, rowData, row, col)
                        {     
                            $(td).attr('style', 'width: 1%;'); 
                        }
                    }            
            ],
        });

        $("#section_search_solicitud #form_search").on('submit', function(event){
            event.preventDefault();
            $("#tabla_solicitudes").hide();
            $("#solicitudPendientes").hide();
            $("#tabla_desembolso").hide();
            //$("#botones_filtro").hide();        
            $("#section_search_solicitud #result").show();

            var data = $(this).serialize();
            buscarCredito(data);
        });
        $("#buscar-banco").on('click', function(event){
            

            var data = {'banco': $("#select-bancos").val()};
            listarSolicitudes(data);
        });

        $("#section_search_solicitud #form_search").on('reset', function(event){
            $("#section_search_solicitud #result").hide();        
            $("#tabla_solicitudes").show();
            $("#solicitudPendientes").show();
            $("#tabla_desembolso").show();
            $("#botones_filtro").show();
        });

        $("#section_search_solicitud #search").on('keyup', function(){
                if($(this).val().length == 0)
                {
                    $("#section_search_solicitud #result").hide();               
                    $("#tabla_solicitudes").show();
                     $("#solicitudPendientes").show();
                    $("#tabla_desembolso").show();
                    $("#botones_filtro").show();
                    $("#texto").empty();
                }else{                
                    $("#tabla_solicitudes").hide();
                    $("#solicitudPendientes").hide();
                    $("#tabla_desembolso").hide();
                    $("#botones_filtro").hide();
                }
        });

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

    });
</script>
