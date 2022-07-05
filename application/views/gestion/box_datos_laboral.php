<style type="text/css">
    .grid-striped .row:nth-of-type(odd) { background-color: rgba(0,0,0,.05);}

    .css-table-row {
        padding: 10px;
        margin: 2px;
        
    }

    .css-table-row div {
        display: table-cell;
        padding: 0 6px;
    }
    
    .telefono_row {
        height: 32px;
        margin-left: -15px;
        margin-right: -15px;
    }

</style>

<div id="box_client_data" class="box box-info">
    <div class="box-header with-border" id="titulo">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4">
                    <small><strong>Datos Laborales</strong></small>
                </div>               
            </div>
        </div>
    </div><!-- end box-header -->
    <?php
        $fondo_situacion = '';        
        if (isset($solicitude['antiguedad_laboral'])){
            if($analisis['antiguedad_laboral'] < 4 || is_null($analisis['antiguedad_laboral'])){
                $fondo_situacion = "bg-danger";
            } else {            
                $fondo_situacion = "bg-success";
            }
        }
    ?>
    <div class="box-body" style="font-size: 12px;">
        <div class="container-fluid grid-striped">
            <div class="css-table">           
            <div class="row css-table-row <?php echo $fondo_situacion ?>">                
                 <div class="col-md-1">
                    <!-- Icono de mail validado o no validado--> 
                    
                    <?php if (isset($solicitude['antiguedad_laboral'])){
                        if($analisis['antiguedad_laboral']<4 || is_null($analisis['antiguedad_laboral'])){ ?>
                        <i style="font-size: 15px; margin-right: 8px; color: red" title="Antiguedad Laboral: <?php echo isset($analisis['antiguedad_laboral'])?$analisis['antiguedad_laboral']:''; ?>" class="fa fa-times-circle"></i>
                    <?php }else{ ?>
                        <i style="font-size: 15px; margin-right: 8px; color: green" title="Antiguedad Laboral: <?php echo isset($analisis['antiguedad_laboral'])?$analisis['antiguedad_laboral']:''; ?>"  class="fa fa-check"></i>                     
                    <?php }
                    }?>
                </div>
                <div class="col-md-2"><small>Laboral:</small></div>  
                <div class="col-md-9"><strong id="salary"><?php echo !empty($solicitude['nombre_situacion'])?$solicitude['nombre_situacion']:''; ?> </strong>
                    <?php  if (isset($analisis['antiguedad_laboral'])){
                        $antiguedad_laboral = $analisis['antiguedad_laboral'];
                    } else {
                        $antiguedad_laboral = NULL;
                    }
                    if ($solicitude['id_situacion_laboral'] != 4){
                      if(!empty($analisis['nit_aportante'])){?>
                            - <strong style="color: blue;"><?php echo $analisis['nit_aportante']; ?>  </strong>
                        <?php } else if($antiguedad_laboral == 0){ ?> 
                            <strong style="color: red;">BURO SIN INF LABORAL</strong>
                        <?php   
                        }   ?> 
                        <?php if(!empty($analisis['razon_social_aportante'])){?>    
                            - <strong style="color: blue;"><?php echo $analisis['razon_social_aportante']; ?></strong>
                        <?php }      
                    } else {
                        if(isset($solicitude['actividad'])){
                        ?>    
                            - <strong style="color: blue;"><?php echo $solicitude['actividad']; ?></strong>
                        <?php }  
                        if(isset($solicitude['actividad_direccion'])){?>    
                            - <strong style="color: blue;"><?php echo $solicitude['actividad_direccion']; ?></strong>
                        <?php }     
                    }
                                 
                        
                        $fecha_consulta = new DateTime(isset($analisis['fecha_consulta'])?$analisis['fecha_consulta']:'now');
                        $fecha_hoy      = new DateTime("now");
                        $diff = $fecha_consulta->diff($fecha_hoy);
                    if(isset($solicitude['id_situacion_laboral']) && $solicitude['id_situacion_laboral'] != 4){    
                        if($diff->days > 30 || is_null($antiguedad_laboral) ){?>
                            <i id="icono_reenviar_datos" style="font-size: 20px; float: right; color: #4F9EB5;cursor:pointer" title="Reenviar Datos" class="fa fa-refresh" onclick="reenvioDatos();"></i>
                    <?php } 
                    }?>            
                </div>
            </div>
            <div class="row css-table-row">                
                <div class="col-md-1"></div>
                <div class="col-md-2"><small>Declarado:</small></div>
                <div class="col-md-9"><strong id="salary" style="margin-right: 50px"><?php echo '$'. number_format($solicitude['ingreso_mensual'],2,",",".") ?></strong>  
                    <?php if(isset($analisis['ingreso_estimado'])){ ?>
                        Estimado: <strong id="salary"><?php echo '$'. number_format($analisis['ingreso_estimado'],2,",",".")?></strong>
                    <?php }?> 
                    <?php if(isset($analisis['ingreso_real_reciente'])){ ?>
                        &nbsp;&nbsp; Real: <strong id="salary"><?php echo '$'. number_format($analisis['ingreso_real_reciente'],2,",",".")?></strong>
                    <?php }?>            
                </div>
            </div>   
        </div>
            </div>

            </div>

       <!--  -->
    </div> <!-- end box-body -->