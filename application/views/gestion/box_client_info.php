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
    
    #box_client_data #callvideo_btn_load {
        display: flex;
        align-items: center;
        text-align: center;
    }
    
    #box_client_data #callvideo_btn_load div {
        padding: 0px 10px;
    }

</style>
<div id="box_client_data" class="box box-info">
    <div class="box-header with-border" id="titulo">
        <div class="row">
            <div class="col-md-12" id="callvideo_btn_load" >
                <?php if (isset($user_videocall)): ?>
                <div class="col-md-2">
                    Video llamadas
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary" id='inicio_llamada'>Iniciar VideoLlamada</button>
                </div>
                <div class="col-md-3">
                    <button class="btn " id='send_link_sms' disabled>Enviar SMS</button>
                </div>
                <div class="col-md-3">
                    <button class="btn " id='send_link_ws' disabled>Enviar Whatsapp</button>
                </div>
                
                <?php endif ?>
            </div>
        </div>
    </div>
    <div class="box-body" style="font-size: 12px;">
        <input id="buro" type="hidden" value="<?php  echo !empty($analisis['buro'])?$analisis['buro']:'';?>">
        <div class="container-fluid grid-striped">
            <div class="css-table">
            <div class="">
            <div class="row css-table-row" >
                <div class="col-md-2 "><small>Tipo:</small></div>
                <div class="col-md-4 " ><strong id="name_type_doc"><?php echo $solicitude['nombre_tipoDocumento'];?></strong></div>
                <div class="col-md-6 " style="color: blue; "><?php echo !empty($analisis['tipo_identificacion'])?$analisis['tipo_identificacion']:'';?></div>
            </div>
            <div class="row css-table-row">
                <div class="col-md-2 "><small>Número:</small></div>
                <div class="col-md-4 " style="vertical-align: middle;"><strong id="document"><?php echo $solicitude['documento']; ?></strong></div>
                <div class="col-md-4 " style="color: blue;"><?php if(!empty($analisis['numero_identificacion']))echo number_format($analisis['numero_identificacion'],0,',','.'); ?></div>
                <div class="col-md-2 " style="color: blue;"><?php echo !empty($analisis['rango_edad'])?$analisis['rango_edad']:''; ?></div>
            </div>
            <div class="row css-table-row">
                <div class="col-md-2"><small>Fecha Exp:</small></div>
                <div class="col-md-4"><strong id="date_delivery"><?php echo date("d-m-Y", strtotime($solicitude['fecha_expedicion'])); ?></strong></div>
                <div class="col-md-4" style="color: blue;"><?php echo !empty($analisis['fecha_expedicion'])?$analisis['fecha_expedicion']:''; ?></div>
                <div class="col-md-2" style="color: blue;"><?php echo !empty($analisis['estado_documento'])?$analisis['estado_documento']:''; ?></div>
            </div>
            <div class="row css-table-row">
                <div class="col-md-2"><small>Nombres:</small></div>
                <div class="col-md-4"><strong id="name"><?php echo $solicitude['nombres']; ?></strong></div>
                <div class="col-md-6" style="color: blue;"><?php echo !empty($analisis['nombres_apellidos'])?$analisis['nombres_apellidos']:''; ?></div>
            </div>
            <div class="row css-table-row">
                <div class="col-md-2"><small>Apellidos:</small></div>
                <div class="col-md-4"><strong id="name"><?php echo $solicitude['apellidos']; ?></strong></div>
                <div class="col-md-6" style="color: blue;"><?php echo !empty($analisis['nombres_apellidos'])?$analisis['nombres_apellidos']:''; ?></div>
            </div>
            <div class="row css-table-row">
                <div class="col-md-2"><small>Fecha Nac:</small></div>
                <div class="col-md-4"><strong id="name"><?php echo ($solicitude['fecha_nacimiento']!='0000-00-00')?date("d-m-Y", strtotime($solicitude['fecha_nacimiento'])):'' ?></strong></div>
                <div class="col-md-6" style="color: blue;"><?php echo !empty($analisis['fecha_nacimiento'])?$analisis['fecha_nacimiento']:''; ?></div>
            </div>   
            </div> 
        </div>
            </div>

            </div>

       <!--  -->
    </div> <!-- end box-body -->