<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 * 
 */
class ApiReporteIndicadores extends REST_Controller 
{

	public $buro_response = array(
								    'rechazado' => 0,
								    'sin_analizar' => 0,
								    'aprobado' => 0,
								);
	public $estado_response = array(
						            'sin_estado' => 0,
									'analisis' => 0,
									'anulado' => 0,
						            'verificado' => 0,
						            'validado' => 0,
						            'rechazado' => 0,
						            'aprobado' => 0,
						            'transfiriendo' => 0,
									'pagado' => 0,
									'anulado' => 0,
							        );
	public $track_gestion_response = array(
							            'solo_gestion_automatica' => 0,
							            'sin_gestion' => 0,
							        );
	public $visado_response = array(
							            'visado' => 0,
							            'rechazado' => 0,
							        );

	public function __construct()
	{
		parent::__construct();
		// MODELOS
/*        if ($this->session->userdata('is_logged_in'))
        {
			// MODELS
   			$this->load->model('reportes/indicadores_model', 'indicadores_model', TRUE);
			// LIBRARIES
			$this->load->library('form_validation');
        } else {
			die('USUARIO NO LOGUEADO');
        }*/

        // MODELS
   			$this->load->model('reportes/Indicadores_model', 'indicadores_model', TRUE);
			// LIBRARIES
			$this->load->library('form_validation');
	}

	public function indicadores_post()
	{

		$formato = strtolower($this->input->post('formato'));
		$formato = (isset($formato) && !empty($formato)) ? $formato = $formato : 'json';
		$data['datos'] = $this->_indicadores();
		switch ($formato) {
			case 'json':
				$this->response($data['datos']);
				break;
			case 'excel':
				$this->excel($data['datos']);
				break;

			default:
				# code...
				break;
		}

	}

	private function _indicadores($desde, $hasta, $canal, $tipo_informe, $tipo_solicitud = null)
	{
		$data = [];
		$data['resumen'] = [];
		$estados =[];
		$track_gestion =[];
		$visado =[];
		$resumen_estados = [];

		//echo '<pre>'; print_r($this->input->post()); echo '</pre>';die;
		$data['canal'] =  strtoupper($canal);
		$data['tipo_informe'] = strtoupper($tipo_informe);
		$data['tipo_solicitud'] = strtoupper($tipo_solicitud);
		$dates = explode('|',$this->input->post('rango_fecha'));
		$data['desde'] = isset($desde);
		$data['hasta'] = isset($hasta);
		$desde = date_to_string($desde, 'Y-m-d', FALSE, 'd-m-Y');
		$hasta = date_to_string($hasta, 'Y-m-d', FALSE, 'd-m-Y');
		$tipo_solicitud = strtoupper($data['tipo_solicitud']) != 'TODOS'? strtoupper($data['tipo_solicitud']):null;

		for($i=$hasta; $i >= $desde;$i = date('Y-m-d', strtotime($i ."- 1 days")))
		{
    		$data['detalles'][$i]['buro'] = $this->buro_response;
    		$data['detalles'][$i]['estados'] = $this->estado_response;
    		$data['detalles'][$i]['track_gestion'] = $this->track_gestion_response;
    		$data['detalles'][$i]['periodo'] = date_to_string($i ,'d-m-Y', FALSE, 'Y-m-d');
    		$data['detalles'][$i]['solicitudes'] = 0;
		}

		// Estado de las solicitudes
		$estados = $this->get_indicadores_estados($desde, $hasta, $tipo_solicitud);
		// Solitudes visado
		$visados = $this->get_solicitudes_visado($desde, $hasta, $tipo_solicitud);
		// Track gestion
		$track_gestion = $this->get_track_gestion($desde, $hasta, $tipo_solicitud);

		foreach ($data['detalles'] as $fecha => $resumen)
		{
			if(isset($estados[$fecha]) && !empty($estados[$fecha]))
			{
				$data['detalles'][$fecha] = $estados[$fecha];
			}
			if(isset($track_gestion[$fecha]) && !empty($track_gestion[$fecha]))
			{
				$data['detalles'][$fecha]['track_gestion'] = $track_gestion[$fecha];
			}
			if(isset($visados[$fecha]) && !empty($visados[$fecha]))
			{
				$data['detalles'][$fecha]['visados'] = $visados[$fecha];
			}	
		}

		// Resumenes
		$resumen_estados = $this->_resumen_estados($desde, $hasta, $estados);
		$resumen_track_gestion = $this->_resumen_track_gestion($track_gestion);
		$resumen_visados = $this->_resumen_visados($visados);
		$data['resumido'][0] = $resumen_estados;
		$data['resumido'][0]['track_gestion'] = $resumen_track_gestion;
		$data['resumido'][0]['visados'] = $resumen_visados;
	
		return $data;
	}

	/***************************************************************************/
	// Estado de las solicitudes segun la fecha
	/***************************************************************************/
	public function get_indicadores_estados($desde, $hasta, $tipo_solicitud)
	{
		$estados = $this->indicadores_model->indicadores_estados($desde, $hasta, $tipo_solicitud);
		return $this->_format_response_indicadores_estados($estados);
	}

	private function _format_response_indicadores_estados($estados)
	{
		$response = [];

		foreach ($estados as $key => $detalle)
		{
			if(empty($response[$detalle['fecha_solicitud']]))
			{
				$response[$detalle['fecha_solicitud']]['buro'] = $this->buro_response;
				$response[$detalle['fecha_solicitud']]['estados'] = $this->estado_response;
				//$response[$detalle['fecha_solicitud']]['fecha'] = date_to_string($detalle['fecha_solicitud'] ,'d-m-Y', FALSE, 'Y-m-d');
				$response[$detalle['fecha_solicitud']]['periodo'] = date_to_string($detalle['fecha_solicitud'] ,'d-m-Y', FALSE, 'Y-m-d');
				$response[$detalle['fecha_solicitud']]['solicitudes'] = 0;
			}
			//$response[$detalle['fecha_solicitud']]['fecha'] = date_to_string($detalle['fecha_solicitud'],'d-m-a');
			$response[$detalle['fecha_solicitud']]['periodo'] = date_to_string($detalle['fecha_solicitud'],'d-m-a');

			// SOLICITUDES
			if($detalle['estado'] == '' || $detalle['estado'] == NULL)
			{
				$estado = 'sin_estado';
			}else{
				$estado =strtolower($detalle['estado']);
			}
			// Este codigo es para garantizar que si aparece un nuevo estado el sistema pueda agregarlo y seguir.
			if(!isset($response[$detalle['fecha_solicitud']]['estados'][$estado]))
			{
				$response[$detalle['fecha_solicitud']]['estados'][$estado] = 0;
			}

			$response[$detalle['fecha_solicitud']]['estados'][$estado] += $detalle['cantidad'];
			
			// BUROS
			if($detalle['respuesta_analisis'] == '' || $detalle['respuesta_analisis'] == NULL)
			{
				$estado_buro = 'sin_analizar';
			}else{
				$estado_buro =strtolower($detalle['respuesta_analisis']);
			}

			// Este codigo es para garantizar que si aparece un nuevo estado el sistema pueda agregarlo y seguir.
			if(!isset($response[$detalle['fecha_solicitud']]['buro'][$estado_buro]))
			{
				$response[$detalle['fecha_solicitud']]['buro'][$estado_buro] = 0;
			}

			$response[$detalle['fecha_solicitud']]['buro'][$estado_buro] += $detalle['cantidad'];

			// Sumo la cantidad de solicitudes
			$response[$detalle['fecha_solicitud']]['solicitudes'] += $detalle['cantidad'];
		}
		return $response;
	}

	private function _resumen_estados($desde, $hasta, $estados)
	{
		$response['estados'] = $this->estado_response;
		$response['buro'] = $this->buro_response;
        $response['periodo'] = $desde." AL ".$hasta;
		$response['solicitudes'] = 0;	

        
		foreach ($estados as $key => $value)
		{
			$response['solicitudes'] += $value['solicitudes'];	

			foreach ($value['buro'] as $estado => $cantidad)
			{
				$response['buro'][$estado] += $cantidad; 
			}
			foreach ($value['estados'] as $estado => $cantidad)
			{
				$response['estados'][$estado] += $cantidad; 
			}
		}
		return $response;
	}
	/***************************************************************************/
	// Track gestion de las solicitudes segun la fecha
	/***************************************************************************/
	public function get_track_gestion($desde, $hasta, $tipo_solicitud)
	{
		// Solo gestion automatica
		$solo_gestion_automatica = $this->indicadores_model->indicadores_track_gestion_gestion_automatica($desde, $hasta, $tipo_solicitud);
		// Sin gestion
		$sin_gestion = $this->indicadores_model->indicadores_track_gestion_sin_gestion($desde, $hasta, $tipo_solicitud);
		$track_gestion = $this->_format_response_track_gestion($sin_gestion, $solo_gestion_automatica);
		
		return $track_gestion;
	}

	private function _format_response_track_gestion($sin_gestion = [], $solo_gestion_automatica = [])
	{
		$response = [];

		foreach ($sin_gestion as $key => $value)
		{
			if(empty($response[$value['fecha_solicitud']]))
			{
				$response[$value['fecha_solicitud']] = $this->track_gestion_response;
			}
			$response[$value['fecha_solicitud']]['sin_gestion']	+= $value['cantidad'];
		}
		foreach ($solo_gestion_automatica as $key => $value)
		{
			if(empty($response[$value['fecha_solicitud']]))
			{
				$response[$value['fecha_solicitud']] = $this->track_gestion_response;
			}
			$response[$value['fecha_solicitud']]['solo_gestion_automatica']	+= $value['cantidad'];
		}

		return $response;
	}

	private function _resumen_track_gestion($track_gestion)
	{
		$response = $this->track_gestion_response;
        
		foreach ($track_gestion as $fecha => $value)
		{
			foreach ($value as $estado => $cantidad)
			{
				$response[$estado] += $cantidad; 
			}
		}
		return $response;
	}
	/***************************************************************************/
	// Visado de las solicitudes segun la fecha
	/***************************************************************************/
	public function get_solicitudes_visado($desde, $hasta, $tipo_solicitud)
	{
		$visados = $this->indicadores_model->indicadores_solicitud_visado($desde, $hasta, $tipo_solicitud);
		return $this->_format_response_solicitud_visado($visados);
	}

	private function _format_response_solicitud_visado($visados)
	{
		$response = [];
		foreach ($visados as $key => $vis)
		{
			if( empty($response[$vis['fecha_solicitud']]))
			{
				$response[$vis['fecha_solicitud']] = $this->visado_response;
			}
			if($vis['visado'] == 0)
			{
				$response[$vis['fecha_solicitud']]['rechazado'] += $vis['cantidad'];
			}else if($vis['visado'] == 1)
			{
				$response[$vis['fecha_solicitud']]['visado'] += $vis['cantidad'];
			}
		}
		return $response;
	}

	private function _resumen_visados($visados)
	{
		$response = $this->visado_response;
        
		foreach ($visados as $fecha => $value)
		{
			foreach ($value as $estado => $cantidad)
			{
				$response[$estado] += $cantidad; 
			}
		}
		return $response;
	}

	public function excel_get($desde, $hasta, $canal, $tipo_informe, $tipo_solicitud)
	{
		$sub_header_font = 10;
		$header_font = 12;
		$header = 1;
		$index = 0;

		$this->load->library('PHPExcel','phpexcel');
	    
	    $this->phpexcel->setActiveSheetIndex(0);
        $this->phpexcel->getActiveSheet()->setTitle('Indicadores');

        // ENCABEZADO

		$this->phpexcel->getActiveSheet()->getStyle('A'.$header)->getFont()->setBold(true)->setSize($sub_header_font);
        $this->phpexcel->getActiveSheet()->setCellValue('A'.$header,'Fecha generación:');
        $this->phpexcel->getActiveSheet()->setCellValue('B'.$header,date('d-m-Y'));
		$this->phpexcel->getActiveSheet()->getStyle('A'.++$header)->getFont()->setBold(true)->setSize($sub_header_font);
        $this->phpexcel->getActiveSheet()->setCellValue('A'.$header,'Hora generación:');
        $this->phpexcel->getActiveSheet()->setCellValue('B'.$header,date('H:i:s'));
		$this->phpexcel->getActiveSheet()->getStyle('A'.++$header)->getFont()->setBold(true)->setSize($sub_header_font);
		$this->phpexcel->getActiveSheet()->setCellValue('A'.$header,'Desde:');
        $this->phpexcel->getActiveSheet()->setCellValue('B'.$header,$desde);
		$this->phpexcel->getActiveSheet()->getStyle('A'.++$header)->getFont()->setBold(true)->setSize($sub_header_font);
        $this->phpexcel->getActiveSheet()->setCellValue('A'.$header,'Hasta:');
        $this->phpexcel->getActiveSheet()->setCellValue('B'.$header,$hasta);
		$this->phpexcel->getActiveSheet()->getStyle('A'.++$header)->getFont()->setBold(true)->setSize($sub_header_font);
        $this->phpexcel->getActiveSheet()->setCellValue('A'.$header,'Forma:');
        $this->phpexcel->getActiveSheet()->setCellValue('B'.$header,$tipo_informe);

		// ESTILO DE COLUMNAS
    	$this->phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getStyle('C')->getAlignment()->setWrapText(true); 
    	$this->phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('M')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getStyle('M')->getAlignment()->setWrapText(true); 
    	$this->phpexcel->getActiveSheet()->getColumnDimension('N')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('O')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('P')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('R')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('S')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('T')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('U')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('V')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('W')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('X')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('Y')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('Z')->setWidth(8);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('AA')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('AB')->setWidth(8);

		$text_center = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
 		// ESTILOS DEL HEADER
 		// Alto de las celdas
		$row_header = $header +2;
 		$this->phpexcel->getActiveSheet()->getRowDimension($row_header)->setRowHeight(30);
    	// Uniendo celdas
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_header.':AB'.$row_header)->applyFromArray($text_center);
    	$this->phpexcel->getActiveSheet()->mergeCells('A'.$row_header.':C'.$row_header);
 		$this->_cell_color('A'.$row_header,'E7E6E6');
    	$this->phpexcel->getActiveSheet()->mergeCells('D'.$row_header.':F'.$row_header)->setCellValue('D'.$row_header, 'SOLICITUDES');
		$this->phpexcel->getActiveSheet()->getStyle('D'.$row_header)->getFont()->setBold(true)->setSize($header_font);
 		$this->_cell_color('D'.$row_header,'AEAAAA');
    	$this->phpexcel->getActiveSheet()->mergeCells('G'.$row_header.':J'.$row_header)->setCellValue('G'.$row_header, 'BURO');
		$this->phpexcel->getActiveSheet()->getStyle('G'.$row_header)->getFont()->setBold(true)->setSize($header_font);
 		$this->_cell_color('G'.$row_header,'ACB9CA');
    	$this->phpexcel->getActiveSheet()->mergeCells('k'.$row_header.':N'.$row_header)->setCellValue('k'.$row_header, 'GESTIÓN');
		$this->phpexcel->getActiveSheet()->getStyle('K'.$row_header)->getFont()->setBold(true)->setSize($header_font);
 		$this->_cell_color('K'.$row_header,'C6E0B4');
		$this->phpexcel->getActiveSheet()->mergeCells('O'.$row_header.':V'.$row_header)->setCellValue('O'.$row_header, 'CONSULTOR ORIGINACIÓN');
		$this->phpexcel->getActiveSheet()->getStyle('O'.$row_header)->getFont()->setBold(true)->setSize($header_font);
 		$this->_cell_color('O'.$row_header,'BDD7EE');
		$this->phpexcel->getActiveSheet()->mergeCells('W'.$row_header.':X'.$row_header)->setCellValue('W'.$row_header, 'ANTI FRAUDE');
		$this->phpexcel->getActiveSheet()->getStyle('W'.$row_header)->getFont()->setBold(true)->setSize($header_font);
 		$this->_cell_color('W'.$row_header,'F8CBAD');
		$this->phpexcel->getActiveSheet()->mergeCells('Y'.$row_header.':AB'.$row_header)->setCellValue('Y'.$row_header, 'DESEMBOLSO');
		$this->phpexcel->getActiveSheet()->getStyle('Y'.$row_header)->getFont()->setBold(true)->setSize($header_font);
 		$this->_cell_color('Y'.$row_header,'A9D08E');

    	// ESTILOS DE SUB-HEADER
		$row_sub_header = $row_header+1;
    	// Bordes
		$border_style= array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM,'color' => array('rgb' => 'FFFFFF'))));
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_sub_header.':AB'.$row_sub_header)->applyFromArray($border_style);
    	// Centrado
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_sub_header.':AB'.$row_sub_header)->applyFromArray($text_center);
    	// Tamaño de fuente
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_sub_header.':AB'.$row_sub_header)->getFont()->setSize($sub_header_font); 
 		
 		$this->phpexcel->getActiveSheet()->setCellValue('A'.$row_sub_header, 'Canal');
 		$this->_cell_color('A'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('B'.$row_sub_header, 'Tipo');
 		$this->_cell_color('B'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('C'.$row_sub_header, 'Periodo');
 		$this->_cell_color('C'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('D'.$row_sub_header, 'Total');
 		$this->_cell_color('D'.$row_sub_header,'AEAAAA');
 		$this->phpexcel->getActiveSheet()->setCellValue('E'.$row_sub_header, 'Sin Analizar');
 		$this->_cell_color('E'.$row_sub_header,'AEAAAA');
 		$this->phpexcel->getActiveSheet()->setCellValue('F'.$row_sub_header, '%');
 		$this->_cell_color('F'.$row_sub_header,'AEAAAA');
 		$this->phpexcel->getActiveSheet()->setCellValue('G'.$row_sub_header, 'Rechazadas');
 		$this->_cell_color('G'.$row_sub_header,'ACB9CA');
 		$this->phpexcel->getActiveSheet()->setCellValue('H'.$row_sub_header, '%');
 		$this->_cell_color('H'.$row_sub_header,'ACB9CA');
 		$this->phpexcel->getActiveSheet()->setCellValue('I'.$row_sub_header, 'Aprobadas');
 		$this->_cell_color('I'.$row_sub_header,'ACB9CA');
 		$this->phpexcel->getActiveSheet()->setCellValue('J'.$row_sub_header, '%');
 		$this->_cell_color('J'.$row_sub_header,'ACB9CA');
 		$this->phpexcel->getActiveSheet()->setCellValue('K'.$row_sub_header, 'Sin gestión');
 		$this->_cell_color('K'.$row_sub_header,'C6E0B4');
 		$this->phpexcel->getActiveSheet()->setCellValue('L'.$row_sub_header, '%');
 		$this->_cell_color('L'.$row_sub_header,'C6E0B4');
 		$this->phpexcel->getActiveSheet()->setCellValue('M'.$row_sub_header, 'Automática [ANALISIS]');
 		$this->_cell_color('M'.$row_sub_header,'C6E0B4');
 		$this->phpexcel->getActiveSheet()->setCellValue('N'.$row_sub_header, '%');
 		$this->_cell_color('N'.$row_sub_header,'C6E0B4');
 		$this->phpexcel->getActiveSheet()->setCellValue('O'.$row_sub_header, 'VERIFICADOS');
 		$this->_cell_color('O'.$row_sub_header,'BDD7EE');
 		$this->phpexcel->getActiveSheet()->setCellValue('P'.$row_sub_header, '%');
 		$this->_cell_color('P'.$row_sub_header,'BDD7EE');
 		$this->phpexcel->getActiveSheet()->setCellValue('Q'.$row_sub_header, 'VALIDADOS');
 		$this->_cell_color('Q'.$row_sub_header,'BDD7EE');
 		$this->phpexcel->getActiveSheet()->setCellValue('R'.$row_sub_header, '%');
 		$this->_cell_color('R'.$row_sub_header,'BDD7EE');
 		$this->phpexcel->getActiveSheet()->setCellValue('S'.$row_sub_header, 'APROBADOS');
 		$this->_cell_color('S'.$row_sub_header,'BDD7EE');
 		$this->phpexcel->getActiveSheet()->setCellValue('T'.$row_sub_header, '%');
 		$this->_cell_color('T'.$row_sub_header,'BDD7EE');
 		$this->phpexcel->getActiveSheet()->setCellValue('U'.$row_sub_header, 'RECHAZADOS');
 		$this->_cell_color('U'.$row_sub_header,'BDD7EE');
 		$this->phpexcel->getActiveSheet()->setCellValue('V'.$row_sub_header, '%');
 		$this->_cell_color('V'.$row_sub_header,'BDD7EE');
 		$this->phpexcel->getActiveSheet()->setCellValue('W'.$row_sub_header, 'VISADA');
 		$this->_cell_color('W'.$row_sub_header,'F8CBAD');
 		$this->phpexcel->getActiveSheet()->setCellValue('X'.$row_sub_header, '%');
 		$this->_cell_color('X'.$row_sub_header,'F8CBAD');
 		$this->phpexcel->getActiveSheet()->setCellValue('Y'.$row_sub_header, 'TRANSF.');
 		$this->_cell_color('Y'.$row_sub_header,'A9D08E');
 		$this->phpexcel->getActiveSheet()->setCellValue('Z'.$row_sub_header, '%');
 		$this->_cell_color('Z'.$row_sub_header,'A9D08E');
 		$this->phpexcel->getActiveSheet()->setCellValue('AA'.$row_sub_header, 'PAGADA');
 		$this->_cell_color('AA'.$row_sub_header,'A9D08E');
 		$this->phpexcel->getActiveSheet()->setCellValue('AB'.$row_sub_header, '%');
 		$this->_cell_color('AB'.$row_sub_header,'A9D08E');
		

 		// CONTENIDO
		$row_data = $row_sub_header+1;
		// Obtengo los datos del reporte
		$datos = $this->_indicadores($desde, $hasta, $canal, $tipo_informe, $tipo_solicitud);

		if($tipo_informe == 'RESUMIDO'){
			$list = $datos['resumido'];
		}else if($tipo_informe == 'DETALLADO')
		{
			$list = $datos['detalles'];

		}
 		foreach ($list as $key => $row)
 		{
 			// Bordes de los resultados.
 			$row_style= array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('rgb' => 'C5D9F1'))));
    		$this->phpexcel->getActiveSheet()->getStyle('A'.$row_data.':AB'.$row_data)->applyFromArray($row_style);
 			// Cebreado
 			if($index % 2) { $this->_row_color('A'.$row_data,'AB'.$row_data,'E7E6E6'); }

 			// Formateo el resultado para mostrar
 			$data = $this->_format_data($row);
    		
    		$this->phpexcel->getActiveSheet()->getStyle('A'.$row_data.':AB'.$row_data)->applyFromArray($text_center);
    		// CANAL
 			$this->phpexcel->getActiveSheet()->setCellValue('A'.$row_data, $canal);
	 	 	// TIPO DE SOLICITUDES
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('B'.$row_data, $tipo_solicitud);
	 	 	// PERIODO
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('C'.$row_data, $data['periodo']);
	 	 	// CANTIDAD DE SOLICITUDES
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('D'.$row_data, $data['solicitudes']);
	 	 	// BURO SIN ANALIZAR
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('E'.$row_data, $data['buro']['sin_analizar']);
	 	 	// PORCENTAJE SIN ANALIZAR
	 	 	// =SI.ERROR((K8/I8);"-")
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('F'.$row_data, '=IFERROR(E'.$row_data.'/'.'D'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('F'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// BURO RECHAZADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('G'.$row_data, $data['buro']['rechazado']);
	 	 	// PORCENTAJE RECHAZADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('H'.$row_data, '=IFERROR(G'.$row_data.'/'.'D'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('H'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// BURO APROBADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('I'.$row_data, $data['buro']['aprobado']);
	 	 	// PORCENTAJE BURO APROBADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('J'.$row_data, '=IFERROR(I'.$row_data.'/'.'D'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('J'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// TRACK GESTION - SIN GESTION
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('K'.$row_data, $data['track_gestion']['sin_gestion']);
	 	 	// PORCENTAJE TRACK GESTION - SIN GESTION
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('L'.$row_data, '=IFERROR(K'.$row_data.'/'.'I'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('L'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// ESTADO ANALISIS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('M'.$row_data, $data['estados']['analisis']);
	 	 	// PORCENTAJE ESTADO ANALISIS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('N'.$row_data, '=IFERROR(M'.$row_data.'/'.'I'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('N'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// ESTADO VERIFICADO
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('O'.$row_data, $data['estados']['verificado']);
	 	 	// PORCENTAJE ESTADO VERIFICADO
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('P'.$row_data, '=IFERROR(O'.$row_data.'/'.'I'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('P'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// ESTADO VALIDADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('Q'.$row_data, $data['estados']['validado']);
	 	 	// PORCENTAJE ESTADO VALIDADO
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('R'.$row_data, '=IFERROR(Q'.$row_data.'/'.'I'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('R'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// ESTADO APROBADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('S'.$row_data, $data['estados']['aprobado']);
	 	 	// PORCENTAJE ESTADO APROBADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('T'.$row_data, '=IFERROR(S'.$row_data.'/'.'I'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('T'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// ESTADO RECHAZADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('U'.$row_data, $data['estados']['rechazado']);
	 	 	// PORCENTAJE ESTADO RECHAZADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('V'.$row_data, '=IFERROR(U'.$row_data.'/'.'I'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('V'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// VISADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('W'.$row_data, $data['visados']['visado']);
	 	 	// PORCENTAJE VISADOS
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('X'.$row_data, '=IFERROR(W'.$row_data.'/'.'I'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('X'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// ESTADO TRANSFIRIENDO
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('Y'.$row_data, $data['estados']['transfiriendo']);
	 	 	// PORCENTAJE ESTADO TRANSFIRIENDO
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('Z'.$row_data, '=IFERROR(Y'.$row_data.'/'.'I'.$row_data.',"-")');
	 	 	$this->phpexcel->getActiveSheet()->getStyle('Z'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
	 	 	// ESTADO PAGADO
	 	 	$this->phpexcel->getActiveSheet()->setCellValue('AA'.$row_data, $data['estados']['pagado']);
 			// PORCENTAJE ESTADO PAGADO
 			$this->phpexcel->getActiveSheet()->setCellValue('AB'.$row_data, '=IFERROR(AA'.$row_data.'/'.'I'.$row_data.',"-")');
 			//echo '<pre>'; print_r('=SI.ERROR(AA'.$row_data.'/'.'I'.$row_data.';"-")'); echo '</pre>';die;
	 	 	$this->phpexcel->getActiveSheet()->getStyle('AB'.$row_data)->getNumberFormat()->applyFromArray(["code" => PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE]);
			
			$row_data++;
			$index++;
 		}
 		// RESULTADO TOTAL
 		// Centrado
		$this->phpexcel->getActiveSheet()->getStyle('C'.$row_data.':D'.$row_data)->applyFromArray($text_center);
		// Negrita y tamaño
		$this->phpexcel->getActiveSheet()->getStyle('C'.$row_data.':D'.$row_data)->getFont()->setBold(true)->setSize($header_font);

 		// Titulo
 		$this->phpexcel->getActiveSheet()->setCellValue('C'.$row_data, 'Total:');
 		// Suma
 		$this->phpexcel->getActiveSheet()->setCellValue('D'.$row_data, '=SUM(D'. ($row_sub_header + 1) .':'.'D'. ($row_data -1) .')');
		//$objGravar->save($arquivo);
		header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Disposition: attachment;filename="Indicadores-'.$desde.'_'.$hasta.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objGravar = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objGravar->save('php://output');
		exit();
		
	}

	private function csv($data)
	{
		header('content-type: text/csv');
		header('Content-Disposition: attachment;filename="ReporteEgresos'.date("Ymd").'.xlsx"');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0');
		header('Pragma: no-cache');
		header('Expires: 0');
	}

	private function _format_data($data)
	{
		$result = [];
		$result['periodo'] 		= $data['periodo'];
		$result['solicitudes'] 	= (isset($data['solicitudes']) && $data['solicitudes'] != 0 )?$data['solicitudes']:'-';
		$result['buro']['sin_analizar'] = (isset($data['buro']['sin_analizar']) && $data['buro']['sin_analizar']!=0 ) ? $data['buro']['sin_analizar']: '-';
		$result['buro']['rechazado'] = (isset($data['buro']['rechazado']) && $data['buro']['rechazado'] != 0) ? $data['buro']['rechazado']: '-';
		$result['buro']['aprobado'] = (isset($data['buro']['aprobado']) && $data['buro']['aprobado'] != 0) ? $data['buro']['aprobado']: '-';
		$result['track_gestion']['sin_gestion'] = (isset($data['track_gestion']['sin_gestion']) && $data['track_gestion']['sin_gestion'] != 0) ? $data['track_gestion']['sin_gestion']: '-';
		$result['estados']['analisis'] = (isset($data['estados']['analisis']) && $data['estados']['analisis'] != 0) ? $data['estados']['analisis']: '-';
		$result['estados']['verificado'] = (isset($data['estados']['verificado']) && $data['estados']['verificado'] != 0) ? $data['estados']['verificado']: '-';
		$result['estados']['validado'] = (isset($data['estados']['validado']) && $data['estados']['validado'] != 0) ? $data['estados']['validado']: '-';
		$result['estados']['aprobado'] = (isset($data['estados']['aprobado']) && $data['estados']['aprobado'] != 0) ? $data['estados']['aprobado']: '-';
		$result['estados']['rechazado'] = (isset($data['estados']['rechazado']) && $data['estados']['rechazado'] != 0) ? $data['estados']['rechazado']: '-';
		$result['visados']['visado'] = (isset($data['visados']['visado']) && $data['visados']['visado'] != 0) ? $data['visados']['visado']: '-';
		$result['estados']['transfiriendo'] = (isset($data['estados']['transfiriendo']) && $data['estados']['transfiriendo'] != 0) ? $data['estados']['transfiriendo']: '-'; 
		$result['estados']['pagado'] = (isset($data['estados']['pagado']) && $data['estados']['pagado'] != 0) ? $data['estados']['pagado']: '-'; 


		return $result;
	}

	private function _cell_color($cells,$color)
	{ 
		$this->phpexcel->getActiveSheet()->getStyle($cells)->getFill() ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => $color) )); 
		return TRUE;
	}

	private function _row_color($from,$to,$color)
	{ 
		$this->phpexcel->getActiveSheet()->getStyle("$from:$to")->getFill() ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => $color) )); 
		return TRUE;
	}
}