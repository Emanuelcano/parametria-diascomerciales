<?php 
    foreach($solicitud_ajustes as $key=>$value):
        $data_content = $value->id_solicitud .' - '. $value->descrip_tipo .' - '. $value->descrip_clase . '<br>';
        switch ($value->estado) {
            case '0' : $data_content .= "Por Procesar";   break;
            case '1' : $data_content .= "Procesado";      break;
            case '2' : $data_content .= "Anulado";        break;
            case '3' : $data_content .= "No valida";      break;
        }
        echo "<span class='btn btn-primary col-md-4'onclick='var_agendarcita.showSolicitudAjustes(this)' data-id=".$value->id." data-id_solicitud=".$value->id_solicitud.">".$data_content."</span>";
    endforeach;
?>
