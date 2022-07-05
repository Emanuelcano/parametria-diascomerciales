<style>
    text[text-anchor=start]
    {
        display: none;
    }
    text[text-anchor=end]
    {
        display: none;
    }
    text[text-anchor=middle]
    {
        display: none;
    }
    .altura
    {
        height: 130px;
    }
    .altura-titulo
    {
        height: 50px;
        vertical-align: middle;
        text-align: center;
    }
    h5
    {
        font-weight: bold;
    }
</style>
<?php 
/*echo '<pre>'; print_r($indicadores); echo '</pre>';
echo '<pre>'; print_r($ranges); echo '</pre>';*/
?>
<div id="box_metrics" class="box box-info">
    <div class="box-header with-border"><b>Indicadores de Análisis</b></div><!-- end box-header -->
        <div class="box-body">
            <div class="container-fluid">  
                <div class="row">

                    <?php foreach ($ranges as $key => $range):?>
                        <div class="col-xs-1">
                            <h5 class="altura-titulo"><small><?php echo strtoupper($range['denominacion']) ?></small></h5>
                            <div id="<?php echo $range['campo']?>" data-value="<?php echo isset($indicadores[$range['campo']])? $indicadores[$range['campo']]:0; ?>" data-min="<?php echo $range['valor_minimo'] ?>" data-max="<?php echo $range['valor_maximo'] ?>" class="altura metric"></div>
                             <p class="text-center"><b><?php echo (isset($indicadores[$range['tabla'].'_'.$range['campo']]))? $indicadores[$range['tabla'].'_'.$range['campo']]:'' ?></b></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div><!-- end container-fluid -->
      </div><!-- end box-body -->
    </div>

<script type="text/javascript" src="<?= base_url('assets/indicadores/charts_loader.js') ?>"></script>
<script type="text/javascript">
    $('document').ready(function(){
        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart(index, elem)
        {
            // console.log(elem);
            $(".metric").each(function(index, elem)
            {

                let diff = $(this).data("max")-$(this).data("min");
                let min = $(this).data("min");
                let max = $(this).data("max");
                let third = diff/3;
                let two_thirds = third*2;
                let data = google.visualization.arrayToDataTable([
                                                                    ['Label', 'Value'],
                                                                    ['',$(this).data("value")]
                                                                    ]);
                let options = {
                    height: 130,
                    min: $(this).data("min"),
                    max: $(this).data("max"),
                    redFrom: min, redTo: third + min,
                    yellowFrom:third + min, yellowTo: two_thirds + min,
                    greenFrom: two_thirds + min, greenTo: max,
                    minorTicks: 5
                };
                let chart = new google.visualization.Gauge(elem);
                chart.draw(data, options);        
            })
        }
    })
</script>
