<?php 

/*echo '<pre>'; print_r($creditos_status); echo '</pre>';

echo '<pre>'; print_r($creditos_types); echo '</pre>';

echo '<pre>'; print_r($operators); echo '</pre>';*/

?>
<div id="section_search_credito" style="background: #FFFFFF; margin-top:10px;">
        <form id="form_search" class="form-horizontal row" method="POST">
            <div class="form-group row">
                <label for="search" class="col-sm-12 control-label "> </label>
                <div class="col-sm-6">
                    <input id="search" name="search" type="text" class="form-control" placeholder="ID / CEDULA / Nombre Cliente / Telefono">
                </div>
                <div class="col-sm-2">
                    <select class="form-control" id="slc-criterio">
                        <option value="" disabled>Criterio</option>
                        <option value="id">ID Credito</option>
                        <option value="telefono" >Telefono</option>
                        <option value="documento" selected>Documento</option>
                        <option value="nombre">Nombre</option>
                        <option value="apellido" >Apellido</option>
                        
                    </select>
                </div>
                <button  type="submit" class="btn btn-info col-sm-1" title="Buscar" style="font-size: 12px;"><i class="fa fa-search"></i> Buscar</button>
                <button  type="reset" class="btn btn-default col-sm-1" title="Limpiar" style="font-size: 12px;"><i class="fa fa- fa-remove"></i> Limpiar</button>
            </div>
            
        </form>
        <div id="result" style="display: none">
            <table align="center" id="table_search" class="table table-responsive table-striped table=hover display" width="100%" >
                <thead style="font-size: smaller; ">
                    <tr class="info">
                        <th></th>
                        <th style="text-align: left;">Ultima Gestion</th>
                        <th style="text-align: center;">N°</th>
                        <th style="text-align: center;">Documento</th>
                        <th style="text-align: left;">Solicitante</th>
                        <th style="text-align: right;">Monto Prestado</th>
                        <th style="text-align: center;" id="fecha">Fecha Vencimiento</th>
                        <th style="text-align: right;" id="deuda_dia">Deuda al Dia</th>
                        <th style="text-align: center;">Estado</th>
                        <th style="text-align: center;">Gestion</th>
<!--                         <th style="text-align: center;">Solicitud</th>
 -->                    </tr>
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
    $("#section_search_credito #result").hide();
    
    table_search = $("#section_search_credito #table_search").DataTable(
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
                        return '<a href="#" class="btn btn-xs btn-primary credito" title="Consultar" onclick="consultar_credito('+data.id+')"><i class="fa fa-cogs"></i></a>';
                    }, 
                    "orderable": false
                },
                {"data":null,
                    "render": function(data, type, row, meta)
                    {
                        return moment(data.ultima_actividad).format('DD-MM-YYYY h:mm:ss');
                    }, 
                    "orderable": false
                },
                {"data":null,
                    "render": function(data, type, row, meta)
                    {
                        return (typeof(data.id_cliente) != 'undefined')? data.id_cliente:data.id;
                    }
                },
                //{"data":"hours_ultima_actividad","orderable": false},
                {"data":null,
                 "render":function(data, type, row, meta)
                        {
                           return (typeof(data.documento) != 'undefined')? data.documento:''; 
                        },
                 "orderable": false
                },
                {"data":null,
                 "render":function(data, type, row, meta)
                 {
                    return data.nombres+' '+data.apellidos;
                 }
                 ,"orderable": false
                },
                {"data":null,
                    "render":function(data, type, row, meta)
                                {
                                    return formatNumber2(data.monto_prestado);
                                }
                },
                {"data":null,
                    "render": function(data, type, row, meta)
                    {
                        return moment(data.fecha_vencimiento).format('DD-MM-YYYY');
                    }, 
                    "orderable": false
                },
                {"data":null,
                    "render":function(data, type, row, meta)
                                {
                                    return formatNumber2(data.deuda);
                                }
                },
                {"data":"estado"},
                {"data":"last_track"}, 
        ],
        "columnDefs": [ 
            {
                "targets": 0,
                "createdCell": function(td, cellData, rowData, row, col)
                {
                    $(td).attr('style', 'width: 3%; text-align: center;'); 
                }

            },
            {
                "targets": [1,2],
                "createdCell": function(td, cellData, rowData, row, col)
                {
                    $(td).attr('style', 'width: 8%; text-align: center;'); 
                }
            },
            {
                "targets": [3],
                "createdCell": function(td, cellData, rowData, row, col)
                {
                    $(td).attr('style', 'width: 7%;'); 
                }
            },
            {
                "targets": [4],
                "createdCell": function(td, cellData, rowData, row, col)
                {
                    $(td).attr('style', 'width: 10%;'); 
                }
            },
            {
                "targets": [5,6,7,8],
                "createdCell": function(td, cellData, rowData, row, col)
                {     
                    $(td).attr('style', 'width: 7%;'); 
                }
            },
            {
                "targets": [9],
                "createdCell": function(td, cellData, rowData, row, col)
                {     
                    $(td).attr('style', 'width: 10%;'); 
                }
            }          
        ],
    });

    $("#section_search_credito #form_search").on('submit', function(event){
        event.preventDefault();
       // $("#section_search_credito #result").show();

        if($("#slc-criterio").val() == "" || $("#slc-criterio").val() == null || $("#search").val().trim() ==""){
            Swal.fire("Campos Incompletos","Debe ingresar un valor en el campo de busqueda y definir el criterio bajo el cual se realizará la misma ","warning");
        }else{
            buscarCreditoCobranza($("#search").val(), null, null, $("#slc-criterio").val());
        }
    });

    $("#section_search_credito #form_search").on('reset', function(event){
        $("#section_search_credito #result").hide();
    });

    $("#section_search_credito #search").on('keyup', function(){
            if($(this).val().length == 0)
            {
                $("#section_search_credito #result").hide();
                $("#texto").empty();
            }
    });

    function formatNumber2(num){
        var num_parts = num.toString().split(".");
        num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return num_parts.join(",");
    }
})
</script>
