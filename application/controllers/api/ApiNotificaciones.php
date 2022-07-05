<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiNotificaciones extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		// MODELS
        $this->load->model('notificaciones/Notificaciones_model','notificaciones',TRUE);
       

		// LIBRARIES
		$this->load->library('form_validation');
        $this->load->helper('encrypt');
        $this->load->helper('formato_helper');
	}

    public function tableGrupos_get()
    {
        $rs_consulta = $this->notificaciones->get_all_groups_words();
                if(!empty($rs_consulta)){
				    $status = parent::HTTP_OK;
				    $response =  
                    [
                        'code' => $status, 
                        'ok' => TRUE,
                        'data' => $rs_consulta
                    ];
               }else
               {
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response =  
                    [
                        'code' => $status, 
                        'ok' => FALSE,
                        'error' => "Falló al consultar los grupos"
                    ];

			   }                

	       
        
       	$this->response($response,$status);
    }

    public function mostrarGrupos_get()
    {
        $rs_consulta = $this->notificaciones->mostrarGruposxOrigen($this->input->get('origen'));
                if(!empty($rs_consulta)){
				    $status = parent::HTTP_OK;
				    $response =  
                    [
                        'code' => $status, 
                        'ok' => TRUE,
                        'data' => $rs_consulta
                    ];
               }else
               {
                    $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                    $response =  
                    [
                        'code' => $status, 
                        'ok' => FALSE,
                        'error' => "Falló al consultar los grupos"
                    ];

			   }                

	       
        
       	$this->response($response,$status);
    }

    public function mostrarPalabras_post()
    {
        echo json_encode(search_word_by_grupo($this->input->post('id'),$this->input->post('origen')));
    }
    public function mostrarPalabras2_post()
    {
        $arrayData = $this->input->post();
        //   var_dump($arrayData);die;
        $m = explode("&",$arrayData['dataForm']);
        $n= [];
        foreach ($m as $key => $value) 
        {
            $i = str_replace("%20"," ",$value);
            list($index,$value) = explode("=",$i);
            $n[$index] = $value;
        }
        $n['id']=$arrayData['id'];
        $n['origen']=$arrayData['origen'];
        // var_dump($n);die;

        $rs_datos = $this->notificaciones->search_group_id($n);

        // var_dump($rs_datos[0]['id_grupo_notificacion']);die;
         $arraySoloGrupo =  search_word_by_grupo($rs_datos[0]['id_grupo_notificacion'],$this->input->post('origen'));
         
         $msgsinespacios = $this->notificaciones->search_notificaciones_words($n,$rs_datos[0]['id_grupo_notificacion'],$this->input->post('origen'));
         
         
         $arraydata = [];
         $arraydata2 = [];


          foreach ($msgsinespacios as $key => $value) {
                $toStringWords = $value['palabras'];
                $signos = array("{","[","}","]",'"',":",'"palabra":',"palabra:");
                $words = str_replace($signos, "", strtolower($toStringWords));
                $words2 = explode(",", $words);
                $arraydata =  array_merge($arraydata , $words2);
               
            }
           
            $predata = array_count_values($arraydata);
            $con =0;
            foreach ($predata as $key => $value) {
                $con ++;
                $arraydata2[] = [
                   "id" => $con,
                   "palabras" =>  $key,
                    "cantidad" => $value
                ];
            }
            // var_dump($arraydata2);die;

            $status = parent::HTTP_OK;
            $response =  
            [
                'code' => $status, 
                'ok' => TRUE,
                'data' => json_encode($arraydata2)
            ];
            $this->response($response,$status);

    }
    public function searchTracking_post()
    {
        // var_dump($this->input->post());die;
        if (!empty($this->input->post()))
        {
            $status = parent::HTTP_OK;
				    $response =  
                    [
                        'code' => $status, 
                        'ok' => TRUE,
                        'data' => json_encode($this->notificaciones->search_track($this->input->post()))
                    ];

        }else{
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response =  
                    [
                        'code' => $status, 
                        'ok' => FALSE,
                        'error' => "Falló al consultar los track"
                    ];
        }
        $this->response($response,$status);
    }

    public function reportExcelTracking_post()
    {
        // var_dump($this->input->post());die;
        if (!empty($this->input->post()))
        {

            $array_data =  $this->notificaciones->search_track($this->input->post());

            ob_start();
            $this->load->library('PHPExcel');
            $hoja = $this->phpexcel;
            // Se agregan los encabezados del reporte
            $hoja->setActiveSheetIndex(0);
            $hoja->getActiveSheet(0)->setCellValue('A1', 'FECHA NOTIFICACION');
            $hoja->getActiveSheet(0)->setCellValue('B1', 'NOMBRE GRUPO');
            $hoja->getActiveSheet(0)->setCellValue('C1', 'PALABRAS');
            $hoja->getActiveSheet(0)->setCellValue('D1', 'MEDIOS NOTICICACION');
            $hoja->getActiveSheet(0)->setCellValue('E1', 'OPERADOR');
            $hoja->getActiveSheet(0)->setCellValue('F1', 'ORIGEN');
            $hoja->getActiveSheet(0)->setCellValue('G1', 'ACCION');
         
           
            //Se agregan los datos de la BD
            $c=2;
    
            foreach ($array_data as $fila) {
                $hoja->setActiveSheetIndex(0)->setCellValue('A'.$c, $fila->fecha_notificacion);
                $hoja->setActiveSheetIndex(0)->setCellValue('B'.$c, $fila->nombre_grupo);
                $hoja->setActiveSheetIndex(0)->setCellValue('C'.$c, $fila->palabras);
                $hoja->setActiveSheetIndex(0)->setCellValue('D'.$c, $fila->medios_notificacion);
                $hoja->setActiveSheetIndex(0)->setCellValue('E'.$c, $fila->operador);
                $hoja->setActiveSheetIndex(0)->setCellValue('F'.$c, $fila->origen);
                $hoja->setActiveSheetIndex(0)->setCellValue('G'.$c, $fila->action);
               
                
                $c++;
            }
            $hoja->getActiveSheet(1)->setTitle('Planilla 1');
            $nombre_archivo = "reportePalabrasTracking".date("Ymd").".xls";
            if (file_exists(URL_CSV_FOLDER.$nombre_archivo)) {
                unlink(URL_CSV_FOLDER.$nombre_archivo);
            }
    
            $objGravar = PHPExcel_IOFactory::createWriter($hoja, 'Excel5');// Cambia a CSV para descargar formato csv
            header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
            header('Cache-Control: max-age=0');
            $objGravar->save(URL_CSV_FOLDER.$nombre_archivo);

            $status = parent::HTTP_OK;
				    $response =  
                    [
                        'code' => $status, 
                        'ok' => TRUE,
                        'data' => "Excel Generado con exito!",
                        'file' => $nombre_archivo,
                    ];

        }else{
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response =  
                    [
                        'code' => $status, 
                        'ok' => FALSE,
                        'error' => "Excel no Generado ocurrio un error!",
                    ];
        }
        $this->response($response,$status);
    }

    public function reprocesamiento_notificaciones_post()
    {
        echo json_encode($this->notificaciones->reprocesamiento_notificaciones());
    }
    public function insertWord_post() 
    {
        $word = $this->input->post("txt_palabra");
        $origen = $this->input->post("origen");
        $group = $this->input->post("hd_group");
        if(empty($word) || empty($group) )
        {
            echo "Intento de registro de palabra fallido motivo columnas vacias";
        }else{
            // var_dump(search_word_by_word($this->input->post("txt_palabra")));
            if (empty(search_word_by_word($word,$origen)))
            {

                if($origen == "ORIGINACION") //1 originacion 2 cobranzas
                {

                    $file = fopen(realpath("docs/database/base_palabras_filtros.txt"), "a");
                }else{
                    $file = fopen(realpath("docs/database/base_palabras_filtrosCobranzas.txt"), "a");

                }


                fwrite($file,  $word.'|'.$group ."," ."\n");
                fclose($file);


                $status = parent::HTTP_OK;
				    $response =  
                    [
                        'code' => $status, 
                        'ok' => TRUE,
                        'data' => "Palabra insertada con exito!"
                    ];
            }else{
                // echo "Palabra ya registrada previamente";
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response =  
                [
                    'code' => $status, 
                    'ok' => FALSE,
                    'error' => "Palabra ya registrada previamente"
                ];
            }

        }
        $this->response($response,$status);
        
    }
    public function editWord_post() 
    {
        $word = $this->input->post("txt_palabra");
        $origen = $this->input->post("origen");
        $group = $this->input->post("hd_group");
        $new_word = $this->input->post("new_word");
        // var_dump($this->input->post());die;
        if(empty($word) || empty($group) )
        {
            echo "Intento de eliminacion de palabra fallido motivo columnas vacias";
        }else{
            // var_dump(search_word_by_word($this->input->post("txt_palabra")));
            if (!empty(search_word_by_word($word,$origen)))
            {

                if($origen == "ORIGINACION") //1 originacion 2 cobranzas
                {

                    $file = realpath("docs/database/base_palabras_filtros.txt");
                }else{
                    $file = realpath("docs/database/base_palabras_filtrosCobranzas.txt");

                }

                $n = $word."|".$group.",";
                $new_word = $new_word."|".$group.",";
                // var_dump($n,$new_word);die;d
                // $arr = array("$n\n","$n\r\n", "$n\r");
                //  var_dump($file);die;

                $content = file_get_contents($file);
                $content = str_replace($n, $new_word , $content);
                
                $a = file_put_contents($file, $content);
                
                // var_dump($a);die;

                $status = parent::HTTP_OK;
				    $response =  
                    [
                        'code' => $status, 
                        'ok' => TRUE,
                        'data' => "Palabra editada con exito!"
                    ];
            }else{
                // echo "Palabra ya registrada previamente";
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response =  
                [
                    'code' => $status, 
                    'ok' => FALSE,
                    'error' => "Palabra no encontrada previamente"
                ];
            }

        }
        $this->response($response,$status);
        
    }
    
    public function deleteWord_post() 
    {
        $word = $this->input->post("txt_palabra");
        $origen = $this->input->post("origen");
        $group = $this->input->post("hd_group");
        // var_dump($this->input->post());die;
        if(empty($word) || empty($group) )
        {
            echo "Intento de eliminacion de palabra fallido motivo columnas vacias";
        }else{
            // var_dump(search_word_by_word($this->input->post("txt_palabra")));
            if (!empty(search_word_by_word($word,$origen)))
            {

                if($origen == "ORIGINACION") //1 originacion 2 cobranzas
                {

                    $file = realpath("docs/database/base_palabras_filtros.txt");
                }else{
                    $file = realpath("docs/database/base_palabras_filtrosCobranzas.txt");

                }

                $n = $word."|".$group.",";
                $arr = array("$n\n","$n\r\n", "$n\r");
                //  var_dump($file);die;

                $content = file_get_contents($file);
                $content = str_replace($arr, '', $content);
                
                $a = file_put_contents($file, $content);
                
                // var_dump($a);die;

                $status = parent::HTTP_OK;
				    $response =  
                    [
                        'code' => $status, 
                        'ok' => TRUE,
                        'data' => "Palabra eliminada con exito!"
                    ];
            }else{
                // echo "Palabra ya registrada previamente";
                $status = parent::HTTP_INTERNAL_SERVER_ERROR;
                $response =  
                [
                    'code' => $status, 
                    'ok' => FALSE,
                    'error' => "Palabra no encontrada previamente"
                ];
            }

        }
        $this->response($response,$status);
        
    }
    public function insertGrupo_post()
    {
        $mensajeSaliente = 
        "*ALERTA WHATSAPP {TIPO_ENVIO}:*\n
        *Canal:*: {ORIGEN}\n
        *Operador:*: {OPERADOR}\n
        *Cliente:* {CLIENTE}\n
        *Documento:* {DOCUMENTO}\n
        *Telefono:* {NUMERO_CLIENT}\n
        *Alerta:* {PALABRA_MENSAJE}\n
        *Mensaje emitido:* {MENSAJE}"; 
        
        $mensajeEntrante = 
        "*ALERTA WHATSAPP {TIPO_ENVIO}:*\n
        *Canal:*: {ORIGEN}\n
        *Cliente:* {CLIENTE}\n
        *Documento:* {DOCUMENTO}\n
        *Telefono:* {NUMERO_CLIENT}\n
        *Alerta:* {PALABRA_MENSAJE}\n
        *Mensaje emitido:* {MENSAJE}"; 
        
        $data=
        [
            "nombre_grupo"=>$this->input->post("name_group"),
            "medios_notificacion"=>$this->input->post("sl_medio"),
            "mensaje_notificar"=>($this->input->post("sl_metodo")=="2" || $this->input->post("sl_metodo")=="1,2")?$mensajeEntrante:$mensajeSaliente,
            "metodo_notificacion"=>$this->input->post("sl_metodo"),
            "action"=>$this->input->post("sl_action"),
            "estatus"=>1,
        ];

        if ($this->input->post("sl_medio")=="SLACK") {
            $data["notificados_slack"] = $this->input->post("id_medio");
        }else if ($this->input->post("sl_medio")=="EMAIL") {
            $data["notificados_correos"] = $this->input->post("id_medio");
        }else if ($this->input->post("sl_medio")=="SMS") {
            $data["notificados_sms"] = $this->input->post("id_medio");
        }
        // var_dump($data);die;
        if ( $this->notificaciones->insert_groups_words($data) ) {
            $status = parent::HTTP_OK;
            $response =  
            [
                'code' => $status, 
                'ok' => TRUE,
                'data' => "Grupo insertada con exito!"
            ];
        }else{
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response =  
            [
                'code' => $status, 
                'ok' => FALSE,
                'error' => "Inserción del grupo fallida"
            ];
        }
        $this->response($response,$status);
    }

    public function get_grupo_update_post()
    {   
        $id = $this->input->post("id");
        $rs_result= $this->notificaciones->get_grupo_update($id);
        if (!empty($rs_result)) {
            // var_dump("hola");die;
            $status = parent::HTTP_OK;
            $response =  
            [
                'code' => $status, 
                'ok' => TRUE,
                'data' => $rs_result[0]
            ];
        }else{
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response =  
            [
                'code' => $status, 
                'ok' => FALSE,
                'error' => "Error en la consulta de id de grupo: ".$id
            ];
        }
        $this->response($response,$status);
    }

    public function grupo_update_post()
    {
        // var_dump( $this->input->post());
        $id = $this->input->post("id_grupo");
        $mensajeSaliente = 
        "*ALERTA WHATSAPP {TIPO_ENVIO}:*\n
        *Canal:*: {ORIGEN}\n
        *Operador:*: {OPERADOR}\n
        *Cliente:* {CLIENTE}\n
        *Documento:* {DOCUMENTO}\n
        *Telefono:* {NUMERO_CLIENT}\n
        *Alerta:* {PALABRA_MENSAJE}\n
        *Mensaje emitido:* {MENSAJE}"; 
        
        $mensajeEntrante = 
        "*ALERTA WHATSAPP {TIPO_ENVIO}:*\n
        *Canal:*: {ORIGEN}\n
        *Cliente:* {CLIENTE}\n
        *Documento:* {DOCUMENTO}\n
        *Telefono:* {NUMERO_CLIENT}\n
        *Alerta:* {PALABRA_MENSAJE}\n
        *Mensaje emitido:* {MENSAJE}"; 
    


        $data=
        [
            "nombre_grupo"=>$this->input->post("name_group"),
            "medios_notificacion"=>$this->input->post("sl_medio"),
            "mensaje_notificar"=>($this->input->post("sl_metodo")=="2" || $this->input->post("sl_metodo")=="1,2")?$mensajeEntrante:$mensajeSaliente,
            "metodo_notificacion"=>$this->input->post("sl_metodo"),
            "action"=>$this->input->post("sl_action"),
            "estatus"=>1,
        ];
        if ($this->input->post("sl_medio")=="SLACK") {
            $data["notificados_slack"] = $this->input->post("id_medio");
        }else if ($this->input->post("sl_medio")=="EMAIL") {
            $data["notificados_correos"] = $this->input->post("id_medio");
        }else if ($this->input->post("sl_medio")=="SMS") {
            $data["notificados_sms"] = $this->input->post("id_medio");
        }
        if ( $this->notificaciones->update_groups_words($data,$id) ) {
            $status = parent::HTTP_OK;
            $response =  
            [
                'code' => $status, 
                'ok' => TRUE,
                'data' => "Grupo Actualizado con exito!"
            ];
        }else{
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response =  
            [
                'code' => $status, 
                'ok' => FALSE,
                'error' => "Actualización del grupo fallido"
            ];
        }
        $this->response($response,$status);

    }
    public function cambioEstadoGrupo_post()
    {
        //  var_dump( $this->input->post());die;
        $id = $this->input->post("id_agente");
        $data=
        [
            "estatus" => $this->input->post("id_estado"),
        ];
        
        if ( $this->notificaciones->update_groups_words($data,$id) ) {
            $status = parent::HTTP_OK;
            $response =  
            [
                'code' => $status, 
                'ok' => TRUE,
                'data' => "Grupo Actualizado con exito!"
            ];
        }else{
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response =  
            [
                'code' => $status, 
                'ok' => FALSE,
                'error' => "Actualización del grupo fallido"
            ];
        }
        $this->response($response,$status);

    }
    public function searchMedio_post()
    {
       $service = $this->input->post("service");
       $rs_response = $this->notificaciones->search_por_service($service);
    //    var_dump($rs_response);die; 
        if ($rs_response) {
            $status = parent::HTTP_OK;
            $response =  
            [
                'code' => $status, 
                'ok' => TRUE,
                'data' => $rs_response
            ];
        }else{
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response =  
            [
                'code' => $status, 
                'ok' => FALSE,
                'error' => "Servicio no Encontrado"
            ];
        }
        $this->response($response,$status);
    }



  

    //obtengo data del modelo y la retorno a su metodo correspondiente
    public function getTracksStats_post() {
        
        // var_dump($this->input->post());die;
        $arrayData = $this->input->post();
        $arrayToGraph = [];
        
        $arrayToGraph['rs_response'] = $this->notificaciones->search_track_stats($arrayData);
        $arrayToGraph['rs_response_donus'] = $this->notificaciones->search_track_donut_stats($arrayData);

        //  var_dump($arrayToGraph);die;

        if (!empty($arrayToGraph))
        {
            $status = parent::HTTP_OK;
				    $response =  
                    [
                        'code' => $status, 
                        'ok' => TRUE,
                        'dato' => $arrayToGraph
                    ];

        }else{
            $status = parent::HTTP_INTERNAL_SERVER_ERROR;
            $response =  
                    [
                        'code' => $status, 
                        'ok' => FALSE,
                        'error' => "Falló al consultar los track"
                    ];
        }
        $this->response($response,$status);

        
    }

    
}