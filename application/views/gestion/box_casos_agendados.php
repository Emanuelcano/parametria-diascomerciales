<?php 
    foreach($agenda_operadores as $key=>$value):
        $nombre = explode(" ", $value['nombres']);
        $apellidos = explode(" ", $value['apellidos']);
?>
        <div class="col-md-1 col-sm-2 col-xs-2" id="box_<?=$value['id_solicitud']?>" style="padding: 3px" >
            <div class="box <?= $value['box-color'][1]?>">
                <div class="alert alert-custom <?= $value['box-color'][0]?> alert-dismissible box-agenda box-body" id="agenda_<?=$value['id']?>" style="color: black !important;">
                    <button class="close" onClick="var_agendarcita.delSolicitud(this)" data-id_agenda="<?= $value['id']?>" data-id_solicitud="<?= $value['id_solicitud'] ?>">
                        <span class="text-danger" style="color: black !important;">&times;</span>
                    </button>
                    <div class="small box-agenda-text box-body" onClick="var_agendarcita.showSolicitud(this)" style="cursor:pointer; padding: 3px; font-size: 75%"
                        data-id_solicitud="<?= $value['id_solicitud'] ?>" id="agenda_<?=$value['id']?>">
                        <span><?= $value['id_solicitud'] ?></span></br>
                        <span style='font-size : 9px'><?= $nombre[0]." ".$apellidos[0] ?></span></br>
                        <span><?= $value['fecha_hora_llamar'] ?></span></br>
                        <span><strong><?= $value['motivo'] ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
<?php
    endforeach;
?>
