<style>
    /* ALL LOADERS */

    .loader{
    width: 100px;
    height: 100px;
    border-radius: 100%;
    position: relative;
    margin: 0 auto;
    }

    /* LOADER 6 */

    #loader-6{
    top: 40px;
    left: -2.5px;
    }

    #loader-6 span{
        display: inline-block;
        width: 5px;
        height: 30px;
        /* background-color: #605ca8; */
        background-color: rgba(255, 206, 86, 0.75);
    }

    #loader-6 span:nth-child(1){
    animation: grow 1s ease-in-out infinite;
    }

    #loader-6 span:nth-child(2){
    animation: grow 1s ease-in-out 0.15s infinite;
    }

    #loader-6 span:nth-child(3){
    animation: grow 1s ease-in-out 0.30s infinite;
    }

    #loader-6 span:nth-child(4){
    animation: grow 1s ease-in-out 0.45s infinite;
    }

    @keyframes grow{
        0%, 100%{
            -webkit-transform: scaleY(1);
            -ms-transform: scaleY(1);
            -o-transform: scaleY(1);
            transform: scaleY(1);
        }

        50%{
            -webkit-transform: scaleY(1.8);
            -ms-transform: scaleY(1.8);
            -o-transform: scaleY(1.8);
            transform: scaleY(1.8);
        }
    }
</style>

<input  type="hidden" id="base_url" value="<?php echo base_url(); ?>">

<section style="margin-top:40px;" class="content">
<div class="row" >

    <div class="col-sm-12">
        <div class="panel panel-warning">
            <div class="panel-heading text-center">Evolucion Mora</div>
            <div class="panel-body">


                <div class="panel col-sm-2">
                    <div class="panel-body" style="border: 1px solid #bce8f1;">
                        <div class="input-group">
                            <label for="tipo-credito" class="col-form-label">Tipo credito</label>
                            <select class="form-control" id="tipo-credito">
                                <option value="" disabled selected >Seleccione</option>
                                <option value="PRIMARIA">Primaria</option>
                                <option value="RETANQUEO">Retanqueo</option>
                                <option value="TODOS">Todos</option>
                                
                            </select>
                        </div>

                    </div>
                </div>

                <div class="panel col-sm-1" style ="padding: 0;">
                    <div class="panel-body" style="border: 1px solid #bce8f1;">
                        <div class="input-group">
                            <label for="rango" class="col-form-label">Rango dias</label>
                            <select class="form-control" id="rango">
                                <option value="" disabled selected >Seleccione</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="35">35</option>
                                <option value="40">40</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="panel col-sm-2" >
                    <div class="panel-body" style="border: 1px solid #faebcc;">
                        <div class="input-group">
                            <label for="vencimiento1" class="col-form-label">Vencimiento 1</label>
                            <select class="form-control vencimiento" id="vencimiento1">
                                <option value="" disabled selected >Seleccione</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="panel col-sm-2" style ="padding: 0;">
                    <div class="panel-body" style="border: 1px solid #faebcc;">
                        <div class="input-group">
                            <label for="vencimiento2" class="col-form-label">Vencimiento 2</label>
                            <select class="form-control vencimiento" id="vencimiento2" >
                                <option value="" disabled selected >Seleccione</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="panel col-sm-2" >
                    <div class="panel-body" style="border: 1px solid #faebcc;">
                        <div class="input-group">
                            <label for="vencimiento3" class="col-form-label">Vencimiento 3</label>
                            <select class="form-control vencimiento" id="vencimiento3" >
                                <option value="" disabled selected >Seleccione</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="panel col-sm-2" style ="padding: 0;">
                    <div class="panel-body" style="border: 1px solid #faebcc;">
                        <div class="input-group">
                            <label for="vencimiento4" class="col-form-label">Vencimiento 4</label>
                            <select class="form-control vencimiento" id="vencimiento4" >
                                <option value="" disabled selected >Seleccione</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="col-sm-1 text-center" style ="padding-top: 25px;">
                    <a class="btn btn-success">APLICAR</a>
                </div>

            </div>
        </div>

    </div>
    <div class="col-md-12 text-center">
        <div class="panel panel-success">
            <div class="panel-heading text-center">CURVAS DE MORA POR VENCIMIENTO </div>
            <div class="panel-body">
                <canvas id="myChartLine" width="15" height="3"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-12 text-center">
        <div class="panel panel-info">
        <div class="panel-heading text-center">DETALLE DE CICLO</div>
        <div class="panel-body">

                <div class="panel col-sm-3 tabla1" style ="padding: 0;">
                    <div class="panel-heading text-center" style="border: 1px solid #bce8f1;">VENCIMIENTO 1</div>
                    <div class="panel-body" style="border: 1px solid #bce8f1;padding: 3px;">
                        <div class="col-sm-6 text-left fecha-vencimiento"  style ="padding: 0;"></div>
                        <div class="col-sm-6 text-right total-creditos" style ="padding: 0;"></div>
                    </div>
                    <table class="table table-striped table-bordered text-center" >
                        <thead>
                            <th>DIA</th>
                            <th>FECHA</th>
                            <th>%</th>
                            
                        </thead>
                        <tbody>
                            <tr></tr>
                        </tbody>
                    </table>

                </div>

                <div class="panel col-sm-3 tabla2" >
                    <div class="panel-heading text-center"  style="border: 1px solid #faebcc;">VENCIMIENTO 2</div>
                    <div class="panel-body" style="border: 1px solid #faebcc;padding: 3px;">
                        <div class="col-sm-6 text-left fecha-vencimiento"  style ="padding: 0;"></div>
                        <div class="col-sm-6 text-right total-creditos" style ="padding: 0;"></div>
                    </div>
                    <table class="table table-striped table-bordered text-center" >
                        <thead>
                            <th>DIA</th>
                            <th>FECHA</th>
                            <th>%</th>
                            
                        </thead>
                        <tbody>
                            <tr></tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="panel col-sm-3 tabla3" style ="padding: 0;">
                    <div class="panel-heading text-center"  style="border: 1px solid #bce8f1;">VENCIMIENTO 3</div>
                    <div class="panel-body" style="border: 1px solid #bce8f1;padding: 3px;">                        
                        <div class="col-sm-6 text-left fecha-vencimiento"  style ="padding: 0;"></div>
                        <div class="col-sm-6 text-right total-creditos" style ="padding: 0;"></div>
                    </div>
                    <table class="table table-striped table-bordered text-center">
                        <thead>
                            <th>DIA</th>
                            <th>FECHA</th>
                            <th>%</th>
                            
                        </thead>
                        <tbody>
                            <tr></tr>
                        </tbody>
                    </table>
                </div>

                <div class="panel col-sm-3 tabla4" >
                    <div class="panel-heading text-center" style="border: 1px solid #faebcc;">VENCIMIENTO 4</div>
                    <div class="panel-body" style="border: 1px solid #faebcc;padding: 3px;">
                        <div class="col-sm-6 text-left fecha-vencimiento"  style ="padding: 0;"></div>
                        <div class="col-sm-6 text-right total-creditos" style ="padding: 0;"></div>
                    </div>
                    <table class="table table-striped table-bordered text-center" >
                        <thead>
                            <th>DIA</th>
                            <th>FECHA</th>
                            <th>%</th>
                            
                        </thead>
                        <tbody>
                            <tr></tr>
                        </tbody>
                    </table>
                </div>
        
        </div>
    </div>


</div>
</section>

<!-- Modal para el Loading -->
<div class="modal fade" id="modalLoading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <!-- Para el loading -->
    <div style="padding-top: 20%;">
        <div id="main" style="marging-top: 100px;"></div>
    </div>
</div>

<script src="<?php echo base_url('assets/js/Chart.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/moment/moment.min.js');?>"></script>

<script>
    var fechas=[];
    var chart;
    $(document).ready(function () {
        
        get_dates();
        
        $('select.vencimiento').on('change',function () {
            update_fechas(this);
        });


        $('.btn.btn-success').on('click',function () {
            show_grafica();
        });

        
        
    });

    
    function update_fechas(elemento) {
        var seleccionados = [];
        $('select.vencimiento').each(function(index){ seleccionados.push($(this).val()); });

        console.log( seleccionados );
        $('select.vencimiento option.opcion').removeClass("bg-warning");
        $('select.vencimiento option.opcion').prop("disabled", false);

        for (let index = 0; index < seleccionados.length; index++) {
            
            if (seleccionados[index] != null ) {
                
                $('select.vencimiento option[value="'+seleccionados[index]+'"]').each(function(el){ 
                    
                    if($(this).parent("select").val() != seleccionados[index]){
                        $(this).prop("disabled", true);
                        $(this).addClass("bg-warning");
                    }
                });
            }

        }
        
    }

    function get_dates() {
        
        
        $.ajax({
            dataType: "JSON",
            url: base_url + 'api/fechasVencimientoFront',
            type: 'GET',
            })
            .done(function(response){
                if(response.status.ok) { 
                    var fechas = response.data;
                    for (let i = (fechas.length-1); i >= 0; i--) {
                        $('select.vencimiento').append("<option class='opcion' value='"+fechas[i].segundo_vencimiento+"'> "+ moment(fechas[i].segundo_vencimiento).format('DD-MM-YYYY') +" </option>");            
                    }

                } else {
                    Swal.fire('Los sentimos', response.message, 'warning');
                    $('select.vencimiento').prop('disabled', true);
                }
                
            })
            .fail(function(xhr) {
                $('select.vencimiento').prop('disabled', true);
                
                Swal.fire("¡Atención!", 
                    `readyState: ${xhr.readyState}
                        status: ${xhr.status}
                        responseText: ${xhr.responseText}`,
                    "error"
                );
            });
        
    }

    function show_grafica(){
        
        let rango = $("#rango").val();
        var labels = [];
        var seleccionados = [];
            $('select.vencimiento').each(function(index){ seleccionados.push($(this).val()); });

        
        if (!seleccionados.includes(null) && $("#rango").val() != null && $("#tipo-credito").val() != null) {
    
            for (let index = -3; index < rango; index++) {
                labels.push(index);  
            }

            $.ajax({
            dataType: "JSON",
            data: {
                "tipo": $("#tipo-credito").val(), 
                "rango": $("#rango").val(),
                "vencimiento": seleccionados
            },
            url: base_url + 'mora/evolucion/grafica',
            type: 'POST',
            beforeSend: function() {
                var loading =
                    '<div class="loader" id="loader-6">' +
                    "<span></span>" +
                    "<span></span>" +
                    "<span></span>" +
                    "<span></span>" +
                    "</div>";
                $("#main").html(loading);
                $('#main').show();
                $('#modalLoading').modal("show");
            }
            })
            .done(function(response){
                $('#main').hide();
                $('#modalLoading').modal("hide");
                if($('#attached_docs :input[value=""]').length)
                for (let id in chart.instances) {
                    chart.instances[id].destroy();
                }
                
                var etiquetas = data = valores = [];


                for (let index = 0; index < response.length; index++) {
                    $(".tabla"+(index+1)+" table tbody").html("");
                    let valor = response[index].valores;
                    var valores = [];
                    $(".tabla"+(index+1)+" .fecha-vencimiento").html(response[index].vencimiento);
                    $(".tabla"+(index+1)+" .total-creditos").html(formatNumber(response[index].total));

                    for (let i = 0; i < valor.length; i++) {

                        valores.push(valor[i]["porcentaje"]);
                        $(".tabla"+(index+1)+" table tbody").append('<tr><td>'+valor[i]["rango"]+'</td><td>'+valor[i]["fecha"]+'</td><td>'+valor[i]["porcentaje"]+'</td></tr>');
                        
                    }


                    data.push(
                        {
                            label: response[index].vencimiento,
                            data: valores,
                            borderColor: [
                                'rgba('+Math.floor(Math.random() * 255)+', '+Math.floor(Math.random() * 255)+', '+Math.floor(Math.random() * 255)+', '+Math.floor(Math.random() * 255)+')',
                            ],
                            borderWidth: 3,
                            fill: false
                        }
                    );
                }

                var ctx = document.getElementById('myChartLine').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: data
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                
                
            })
            .fail(function(xhr) {
                $('#modalLoading').modal("hide");
                Swal.fire("¡Atención!", 
                    `readyState: ${xhr.readyState}
                        status: ${xhr.status}
                        responseText: ${xhr.responseText}`,
                    "error"
                );
            });
        } else {
            Swal.fire("¡Atención!", "Todos los campos son obligatorios","error");
        }

    }
    function formatNumber(numero) {
        let num = parseFloat(numero).toFixed(2);
        var num_parts = num.toString().split(".");
        num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        return num_parts.join(",");
    }
</script>