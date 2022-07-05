<?php
defined('BASEPATH') or exit('No direct script access allowed');

set_time_limit(0);

class Envios extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('formato_helper');  
        $this->load->helper('file');
        $this->load->helper('requests_helper');   
        $this->load->helper('folder_creator_helper');  
        $this->load->helper('date_helper');   

        $this->load->library('form_validation');

        $this->db_maestro           = $this->load->database("maestro", TRUE);
        $this->db_respuestas_bancos = $this->load->database("respuestas_bancos", TRUE);

        $this->load->model('CreditoDetalle_model',      'creditoDetalle_model', TRUE);
        $this->load->model('Cliente_model',             'cliente_model', TRUE);
        $this->load->model("RespuestasBancos_model",    'respuestasbancos_model', TRUE);

        $this->medio_pago           = 'DEBITO AUTOMATICO';
        $this->dataRcga             = [];
        $this->dataRnov             = [];
        $this->end_folder           = 'uploads/bancolombia/respuestas';

        $this->fecha_inicio         = "";
        $this->fecha_finalizacion   = "";
        
    }

    public function vistaDebitoAutomaticoBancolombiaRespuestaEnvios()
    {
        $archivos_lista = $this->respuestasbancos_model->getArchivosEnviosLista();
        
        $data = array(
            'title' => "Debito Automático RCGA",
            'banco' => "bancolombia",
            'archivos_lista'  => $archivos_lista
        );

        $this->load->view('tesoreria/vista_debito_automatico_respuesta_envios', $data);
    }

    public function procesarRespuestaEnvio()
    {
        
        header('Content-Type: text/html; charset=ISO-8859-1');

        set_time_limit(0);

        $this->config_file['upload_path']   = get_end_folder($this->end_folder);
        $this->config_file['allowed_types'] = 'txt';
        $this->config_file['overwrite']     = FALSE;

        $config['form']                     = "lecturarcga";
        $nombre_origen                      = $_FILES['fileLecturarcga']['name'];
        $file_tmp                           = $_FILES['fileLecturarcga']['tmp_name'];
        $rutaToday                          = get_end_folder($this->end_folder) . $nombre_origen;
        $response                           = array_base();

        $response['response']['respuesta']  = false;
        $response['response']['errors']     = [];
        $response['response']['mensaje']    = "Tipo de archivo erroneo. Solo archivos RCGA o RNOV con extension .txt.";

        if((substr($nombre_origen, 0, 4) == "RCGA") || (substr($nombre_origen, 0, 4) == "RNOV"))
        {

            $response['response']['respuesta']  = false;
            $response['response']['errors']     = [];
            $response['response']['mensaje']    = "Archivo: " . $nombre_origen . ", procesado anteriormente.";

            if(($this->respuestasbancos_model->fileExistInRcgaDB($nombre_origen) == false) && ($this->respuestasbancos_model->fileExistInRnovDB($nombre_origen) == false) && !file_exists($rutaToday))
            {

                move_uploaded_file($file_tmp, get_end_folder($this->end_folder) . $nombre_origen);

                $full_path_file = get_end_folder($this->end_folder) . $nombre_origen;
                $file = new SplFileObject($full_path_file);

                foreach ($file as $k) 
                {
                    $line = cleanString($file->current());
                    $this->processLine($nombre_origen, $line);
                }

                if(count($this->dataRcga) >= 1)
                    $this->db->insert_batch('respuestas_bancos.debitos_bancolombia_rcga', $this->dataRcga);

                if(count($this->dataRnov) >= 1)
                    $this->db->insert_batch('respuestas_bancos.debitos_bancolombia_rnov', $this->dataRnov);

                $response['response']['respuesta']  = true;
                $response['response']['errors']     = [];
                $response['success']                = true;
                $response['response']['mensaje']    = "Archivo: " . $nombre_origen . ", subido correctamente.";                 
            }

        }

        echo json_encode($response);
    }

    protected function processLine($nombre_origen, $line)
    {
        
        $fechaSubida = strtotime(substr($nombre_origen, 11, 8));
        $fechaSubida = date('Y-m-d', $fechaSubida);

        if (substr($nombre_origen, 0, 4) == 'RCGA')
        {

            if(substr($line,0,1) == 6)
            {

                $this->dataRcga[] =  array(
                    'id_detalle_credito'    => substr($line, 80, 6),
                    'fecha'                 => date('Y-m-d H:i:s'),
                    'monto'                 => sprintf("%.2f", (substr($line, 62,  17)) / 100),
                    'numero_cuenta'         => substr($line, 43, 17),
                    'medio_pago'            => 'DEBITO AUTOMATICO',
                    'fecha_a_debitar'       => substr($line, 174, 8),
                    'referencia'            => $nombre_origen,
                    'cod_resp'              => substr($line, 171, 3),
                    'fecha_subida'          => $fechaSubida
                );

            }
        }

        if (substr($nombre_origen, 0, 4) == 'RNOV')
        {

            $r = explode(",",$line);
                
            if ($r[0] != '') 
            {
                
                $date = DateTime::createFromFormat('dmY', $r[12]);
                if($date)
                    $this->fecha_inicio = $date->format('Ymd');

                $date = DateTime::createFromFormat('dmY', $r[13]);
                if($date)
                    $this->fecha_finalizacion = $date->format('Ymd'); 

                $this->dataRnov[] =  array(
                    'fecha'                 => date('Y-m-d H:i:s'),
                    'medio_pago'            => 'DEBITO AUTOMATICO',
                    'referencia'            => $nombre_origen,
                    'convenio'              => $r[0],
                    'documento'             => $r[3],
                    'numero_cuenta'         => $r[5],
                    'cod_banco'             => $r[7],
                    'id_detalle_credito'    => $r[8],
                    'fecha_inicio'          => $this->fecha_inicio,
                    'fecha_finalizacion'    => $this->fecha_finalizacion,
                    'tipo_novedad'          => $r[14],
                    'reintentos'            => $r[15],
                    'criterio_aplicacion'   => $r[16],
                    'frecuencia'            => $r[17],
                    'dias'                  => $r[18],
                    'dia_pago'              => $r[19],
                    'debito_parcial'        => $r[20],
                    'cod_resp'              => $r[21],
                    'fecha_subida'          => $fechaSubida
                );
            }
        }
    }

    public function vistaDebitoAutomaticoBancolombiaInfomeEnvios()
    {
        $data = array(
            'title' => "Informe Envios",
            'banco' => "bancolombia"
        );
        
        $this->load->view('tesoreria/vista_debito_automatico_informe_envios', $data);
    }

    public function procesarInformeEnvios()
    {
        //echo json_encode("");
        $nombreArchivos             = "";
        $totalesXCodRespRcga        = "RCGA: ";
        $totalesXCodRespRnov        = "RNOV: ";

        $enviosRcga                 = $this->respuestasbancos_model->getEnviosPorDiaRcga($this->input->post("fechaEnvio"));
        $enviosRnov                 = $this->respuestasbancos_model->getEnviosPorDiaRnov($this->input->post("fechaEnvio"));
        $nombreArchivosRcga         = $this->respuestasbancos_model->getNombreArchivos($this->input->post("fechaEnvio"), "rcga");
        $nombreArchivosRnov         = $this->respuestasbancos_model->getNombreArchivos($this->input->post("fechaEnvio"), "rnov");
        $cantidadCasosRcga          = $this->respuestasbancos_model->getTotalEnviosXFecha($this->input->post("fechaEnvio"), "rcga");
        $cantidadCasosRnov          = $this->respuestasbancos_model->getTotalEnviosXFecha($this->input->post("fechaEnvio"), "rnov");
        $totalXCodRespRcga          = $this->respuestasbancos_model->getTotalXCodResp($this->input->post("fechaEnvio"), "rcga");
        $totalXCodRespRnov          = $this->respuestasbancos_model->getTotalXCodResp($this->input->post("fechaEnvio"), "rnov");

        //print_r($totalXCodRespRcga);
        //print_r($totalXCodRespRnov);

        foreach($nombreArchivosRcga as $nombreArchivo)
            $nombreArchivos .= $nombreArchivo->archivo . " ";

        foreach($nombreArchivosRnov as $nombreArchivo)
            $nombreArchivos .= $nombreArchivo->archivo . " ";
        
        foreach($totalXCodRespRcga as $XCodRespRcga)
            $totalesXCodRespRcga .= $XCodRespRcga->codigo_respuesta . ": " . $XCodRespRcga->total . "  ";

        foreach($totalXCodRespRnov as $XCodRespRnov)
            $totalesXCodRespRnov .= $XCodRespRnov->codigo_respuesta . ": " . $XCodRespRnov->total . "  ";

        $data = array(  
                        "enviosRnov"            => $enviosRnov, 
                        "enviosRcga"            => $enviosRcga, 
                        "nombreArchivos"        => $nombreArchivos, 
                        "cantidadCasosRcga"     => $cantidadCasosRcga->total, 
                        "cantidadCasosRnov"     => $cantidadCasosRnov->total,
                        "totalXCodRespRcga"     => $totalesXCodRespRcga,
                        "totalXCodRespRnov"     => $totalesXCodRespRnov
                    );

        echo json_encode($data);
    }

}