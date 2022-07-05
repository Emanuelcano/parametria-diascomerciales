<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

set_time_limit(0);

class Imputacion extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('formato_helper');  
        $this->load->helper('file');
        $this->load->helper('requests_helper');   
        $this->load->helper('folder_creator_helper');   

        $this->load->library('form_validation');

        $this->db_maestro           = $this->load->database("maestro", TRUE);
        $this->db_respuestas_bancos = $this->load->database("respuestas_bancos", TRUE);

        $this->load->model('CreditoDetalle_model',      'creditoDetalle_model', TRUE);
        $this->load->model('Cliente_model',             'cliente_model', TRUE);
        $this->load->model("RespuestasBancos_model",    'respuestasbancos_model', TRUE);

        $this->medio_pago           = 'DEBITO AUTOMATICO';
        $this->data                 = [];
        $this->carpeta_sin_procesar = 'uploads/imputacion/sinprocesar';
        $this->carpeta_procesados   = 'uploads/imputacion/procesados';
        
    }

    public function vistaImputacion()
    {

        $data = array(
            'title' => "Imputacion",
            'banco' => "bancolombia"
        );

        $this->load->view('tesoreria/vista_imputacion',$data);
    }

    public function subirArchivoImputacion()
    {
        header('Content-Type: text/html; charset=ISO-8859-1');

        set_time_limit(0);

        $this->config_file['upload_path']   = get_end_folder($this->carpeta_sin_procesar);
        $this->config_file['allowed_types'] = 'txt,xls';
        $this->config_file['overwrite']     = FALSE;

        $config['form']                     = "lectura";
        $nombre_origen                      = $_FILES['fileLectura']['name'];
        $file_tmp                           = $_FILES['fileLectura']['tmp_name'];
        $rutaToday                          = get_end_folder($this->carpeta_sin_procesar) . $nombre_origen;
        $response                           = array_base();

        $response['response']['respuesta']  = false;
        $response['response']['errors']     = [];
        $response['response']['mensaje']    = "Tipo de archivo erroneo. Solo archivos CONSOLIDADOS con extension .txt o .xls.";

        if (substr($nombre_origen, 0, 3) == "Car")
        {
            $response['response']['respuesta']  = false;
            $response['response']['errors']     = [];
            $response['response']['mensaje']    = "Archivo: " . $nombre_origen . ", procesado anteriormente.";

            if(($this->respuestasbancos_model->fileExistInDB($nombre_origen) == false) && !file_exists($rutaToday)  )
            {
                $response['response']['respuesta']  = true;
                $response['response']['errors']     = [];
                $response['success']                = true;
                $response['response']['mensaje']    = "Archivo: " . $nombre_origen . ", subido correctamente.";

                move_uploaded_file($file_tmp, get_end_folder($this->carpeta_sin_procesar) . $nombre_origen);
            }
            
        }

        if((substr($nombre_origen, 0, 3) == "REC") && ((substr($nombre_origen, 17, 6) == "090652") || (substr($nombre_origen, 17, 6) == "010232")) && (substr($nombre_origen, 24, 4) == "0000"))
        {

            $response['response']['respuesta']  = false;
            $response['response']['errors']     = [];
            $response['response']['mensaje']    = "Archivo: " . $nombre_origen . ", procesado anteriormente.";

            if(($this->respuestasbancos_model->fileExistInDB($nombre_origen) == false) && !file_exists($rutaToday)  )
            {

                $response['response']['respuesta']  = true;
                $response['response']['errors']     = [];
                $response['success']                = true;
                $response['response']['mensaje']    = "Archivo: " . $nombre_origen . ", subido correctamente.";

                move_uploaded_file($file_tmp, get_end_folder($this->carpeta_sin_procesar) . $nombre_origen);
            }

        }

        echo json_encode($response);
    }

    public function insertArchivoADbCron()
    {
        header('Content-Type: text/html; charset=ISO-8859-1');

        set_time_limit(0);

        // CHECK VARIABLE SUBIDA CRON OK
        if($this->respuestasbancos_model->getStatusCron()->status != 1)
        {

            $folder = get_end_folder($this->carpeta_sin_procesar);

            $archivos = array_diff(scandir($folder), array('.', '..')); 

            if (count($archivos) >= 1) 
            {

                $this->respuestasbancos_model->setStatusCron(1);

                foreach($archivos as $archivo)
                {

                    $full_path_file = get_end_folder($this->carpeta_sin_procesar) . $archivo;

                    if($this->respuestasbancos_model->fileExistInDB($archivo) == false)
                    {                
                        
                        if (substr($archivo, 0, 5) == "Carga")
                        {
                            
                            $reader = new Xls();
                            $reader->setReadDataOnly(true);
                            $spreadsheet = $reader->load($full_path_file);
                            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                            foreach ($sheetData as $row)
                            {

                                if ($row['B'] == "00000000359056975") 
                                {
                                    $this->medio_pago = 'DEBITO AUTOMATICO';
                                    $this->processRow($row, $archivo);
                                }

                            }

                        }else{

                            $file = new SplFileObject($full_path_file);
                        
                            $this->medio_pago = (substr($archivo, 17, 6) == '090652')? 'RECAUDO' : 'DEBITO AUTOMATICO' ;
                            
                            foreach ($file as $k => $line) 
                            {
                                
                                $line = utf8_decode($line);
                                $line = preg_replace('/[-?]/', 'X', $line);
                                $line = cleanString($line);
    
                                if(substr($line,0,1) == 1)
                                    $fechaRecaudo = substr($line, 49, 8);
    
                                if(substr($line,0,1) == 6)
                                    $this->processLine($line, $full_path_file, $archivo);
    
                            }

                        }

                        $this->moverCargados($full_path_file, $archivo);

                    }

                }

                if(count($this->data) >= 1)
                    $this->respuestasbancos_model->insertBatch($this->data);   

            }
        }

        $this->respuestasbancos_model->setStatusCron(0);
    }

    protected function moverCargados($full_path_file, $archivo)
    {
        rename($full_path_file, get_end_folder($this->carpeta_procesados) . $archivo);
    }

    protected function processRow($row, $nombre_origen)
    {

        $monto = str_replace(array('$', ',' , '.'), "", $row['F']); 

        $date = date('Y-m-d', strtotime(str_replace('/', '-',  $row['E'])));

        $this->data[] =  array(

            'id_detalle_credito'    => $row['Z'],
            'fecha'                 => date('Y-m-d H:i:s'),
            'monto'                 => sprintf("%.2f", $monto / 100),
            'medio_pago'            => $this->medio_pago,
            'convenio'              => '',
            'fecha_pago'            => $date,
            'imputado'              => 0,
            'en_proceso'            => 0,
            'referencia'            => $nombre_origen,
            'cod_resp'              => strtoupper(substr($row['AJ'], 0, 3))

        );

    }

    protected function processLine($line, $full_path_file, $nombre_origen)
    {
        $rexc = array();

        switch ($this->medio_pago)
        {

            case 'RECAUDO':

                $documento = trim(preg_replace("/[^0-9]/", "",substr($line, 80,  30)));

                // Verifica si cliente existe por documento
                switch (count($this->cliente_model->getClienteBy(array("documento" => $documento)))) 
                {

                    case 0:

                        $rexc['monto_total']    = sprintf("%.2f", (substr($line, 62,  17)) / 100);
                        $rexc['ruta_back_txt']  = $full_path_file;
                        $rexc['nombre_archivo'] = str_replace(".txt", "", $nombre_origen);
                        $rexc['fecha_recaudo']  = (substr($line, 174, 8));
                        $rexc['origen_pago']    = "CONV_090652";
                        $rexc['documento']      = substr($line, 80,  30);
                        $rexc['imputado']       = 0;

                        $this->db_maestro->insert("recaudo_sin_imputar", $rexc);

                        break;

                    case 1:

                        $id_credito_detalle = $this->creditoDetalle_model->get_ultima_cuota_por_documento($documento);

                        $this->data[] =  array(

                            'id_detalle_credito'    => $id_credito_detalle->id,
                            'fecha'                 => date('Y-m-d H:i:s'),
                            'monto'                 => sprintf("%.2f", (substr($line, 62,  17)) / 100),
                            'medio_pago'            => $this->medio_pago,
                            'convenio'              => substr($nombre_origen, 17, 6),
                            'fecha_pago'            => substr($line, 174, 8),
                            'imputado'                => 0,
                            'en_proceso'            => 0,
                            'referencia'            => $nombre_origen,
                            'cod_resp'              => substr($line, 171, 3)

                        );

                        break;

                    default:

                        $rexc['monto_total']    = sprintf("%.2f", (substr($line, 62,  17)) / 100);
                        $rexc['ruta_back_txt']  = $full_path_file;
                        $rexc['nombre_archivo'] = str_replace(".txt", "", $nombre_origen);
                        $rexc['fecha_recaudo']  = (substr($line, 174, 8));
                        $rexc['origen_pago']    = "CONV_090652";
                        $rexc['documento']      = substr($line, 80,  30);
                        $rexc['imputado']       = 0;

                        $this->db_maestro->insert("recaudo_sin_imputar", $rexc);

                        break;

                }

                break;

            case 'DEBITO AUTOMATICO':

                $this->data[] =  array(

                    'id_detalle_credito'    => substr($line, 80, 6),
                    'fecha'                 => date('Y-m-d H:i:s'),
                    'monto'                 => sprintf("%.2f", (substr($line, 62,  17)) / 100),
                    'medio_pago'            => $this->medio_pago,
                    'convenio'              => substr($nombre_origen, 17, 6),
                    'fecha_pago'            => substr($line, 174, 8),
                    'imputado'              => 0,
                    'en_proceso'            => 0,
                    'referencia'            => $nombre_origen,
                    'cod_resp'              => substr($line, 171, 3)

                );

                break;                                

            default:
                break;

        }

    }

}