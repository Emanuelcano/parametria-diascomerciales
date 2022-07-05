<?php

function get_end_folder($upload_folder)
{

    $end_folder = $upload_folder.'/'.date('Y').'/'.date('m').'/'.date('d').'/';

    if(!file_exists($end_folder))
    {

        if(!mkdir($end_folder, 0777, true))
        {
            $this->response['status']['ok'] = FALSE;
            $this->response['errors'] = 'No fué posible crear el directorio en .' . $end_folder;

            return FALSE;
        }
    }

    return $end_folder;
}

?>