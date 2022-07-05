<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 * 
 */
class ApiReporteGestion extends REST_Controller 
{
	public function __construct()
	{
		parent::__construct();
		// MODELOS
/*        if ($this->session->userdata('is_logged_in'))
        {
			// MODELS
   			$this->load->model('reportes/Reportes_model', 'reportes_model', TRUE);
			// LIBRARIES
			$this->load->library('form_validation');
        } else {
			die('USUARIO NO LOGUEADO');
        }*/

        // MODELS
   			$this->load->model('reportes/Gestion_model', 'gestion_model', TRUE);
			// LIBRARIES
			$this->load->library('form_validation');
	}

	public function gestion_post()
	{
		$canal = $this->input->post('canal');
		$tipo_solicitud = $this->input->post('tipo_solicitud');
		$respuesta_buro = $this->input->post('respuesta_buro');
		$estado = $this->input->post('estado');
		$operador_asignado = $this->input->post('operador_asignado');
		$desde = $this->input->post('desde');
		$hasta = $this->input->post('hasta');
		$rango_fecha = $this->input->post('rango_fecha');
		if(isset($rango_fecha))
		{
			$dates = explode('|',$this->input->post('rango_fecha'));
			$desde = isset($dates[0])?trim($dates[0]):null;
			$hasta = isset($dates[1])?trim($dates[1]):null;
		}
		$data['canal'] = isset($canal) ? strtoupper($canal) : NULL ;

		//$params['canal'] = isset($canal) ? $canal: NULL;
		$params['tipo_solicitud'] = (isset($tipo_solicitud) && $tipo_solicitud != 'TODOS') ? $tipo_solicitud: NULL;
		$params['respuesta_analisis'] = (isset($respuesta_buro) && $respuesta_buro != 'TODOS') ? $respuesta_buro: NULL;
		$params['estado'] = (isset($estado) && $estado != 'TODOS') ? $estado: NULL;
		$params['operador_asignado'] = (isset($operador_asignado) && $operador_asignado != 'TODOS')? $operador_asignado: NULL;
		$params['desde'] = isset($desde) ? date_to_string($desde, 'Y-m-d', FALSE, 'd-m-Y'): NULL;
		$params['hasta'] = isset($hasta) ? date_to_string($hasta, 'Y-m-d', FALSE, 'd-m-Y'): NULL;
		$data['solicitudes'] = $this->gestion_model->solicitudes($params);
		$this->response($data);
		echo '<pre>'; print_r($solicitudes); echo '</pre>';die;
		echo '<pre>'; var_dump($params); echo '</pre>';
		echo '<pre>'; print_r($this->input->post()); echo '</pre>';die;

	}


	public function excel_get($canal,$tipo_solicitud,$buro,$gestion,$estado,$razo_rechazo,$operador_asignado,$desde,$hasta)
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
        $this->phpexcel->getActiveSheet()->setCellValue('B'.$header,$tipo_solicitud);
		
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

			
		$text_center = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
 		// ESTILOS DEL HEADER
 		// Alto de las celdas
		$row_header = $header +2;


    	// ESTILOS DE SUB-HEADER
		$row_sub_header = $row_header+1;
    	// Bordes
		$border_style= array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM,'color' => array('rgb' => 'FFFFFF'))));
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_sub_header.':L'.$row_sub_header)->applyFromArray($border_style);
    	// Centrado
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_sub_header.':L'.$row_sub_header)->applyFromArray($text_center);
    	// Tamaño de fuente
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_sub_header.':L'.$row_sub_header)->getFont()->setSize($sub_header_font); 
 		
 		$this->phpexcel->getActiveSheet()->setCellValue('A'.$row_sub_header, 'Canal');
 		$this->_cell_color('A'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('B'.$row_sub_header, 'Tipo');
 		$this->_cell_color('B'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('C'.$row_sub_header, 'ID');
 		$this->_cell_color('C'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('D'.$row_sub_header, 'Fecha Alta');
 		$this->_cell_color('D'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('E'.$row_sub_header, 'Documento');
		$this->_cell_color('E'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('F'.$row_sub_header, 'Nombres');
 		$this->_cell_color('F'.$row_sub_header,'E7E6E6'); 
		$this->phpexcel->getActiveSheet()->setCellValue('G'.$row_sub_header, 'Apellidos');
		$this->_cell_color('G'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('H'.$row_sub_header, 'Buros');
 		$this->_cell_color('H'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('I'.$row_sub_header, 'Estado');
		$this->_cell_color('I'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('J'.$row_sub_header, 'ID Operador');
		$this->_cell_color('J'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('K'.$row_sub_header, 'Operador');
		$this->_cell_color('K'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('L'.$row_sub_header, 'Ultima Gestion');
		$this->_cell_color('L'.$row_sub_header,'E7E6E6');
		// $row_data = $row_sub_header+1;
		// $datos = $this->_gestion($canal,$tipo_solicitud,$buro,$gestion,$estado,$razo_rechazo,$operador_asignado,$desde,$hasta);

		// CONTENIDO
		$row_data = $row_sub_header+1;	
		$datos = $this->_gestion($tipo_solicitud,$buro,$estado,$operador_asignado,$desde,$hasta,$canal);
		foreach($datos as $key => $value){			
		$this->phpexcel->getActiveSheet()->getStyle('A'.$row_data.':AB'.$row_data)->applyFromArray($text_center);
		$this->phpexcel->getActiveSheet()->setCellValue('A'.$row_data, $canal);
		$this->phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth("25");
		$this->phpexcel->getActiveSheet()->setCellValue('B'.$row_data, $value['tipo_solicitud']);
		$this->phpexcel->getActiveSheet()->setCellValue('C'.$row_data, $value['id']);
		$this->phpexcel->getActiveSheet()->setCellValue('D'.$row_data, $value['fecha_alta']);
		$this->phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth("20");
		$this->phpexcel->getActiveSheet()->setCellValue('E'.$row_data, $value['documento']);
		$this->phpexcel->getActiveSheet()->setCellValue('F'.$row_data, $value['nombres']);
		$this->phpexcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
		$this->phpexcel->getActiveSheet()->setCellValue('G'.$row_data, $value['apellidos']);
		$this->phpexcel->getActiveSheet()->getColumnDimension('G')->setWidth("20");
		$this->phpexcel->getActiveSheet()->setCellValue('H'.$row_data, $value['respuesta_analisis']);
		$this->phpexcel->getActiveSheet()->getColumnDimension('H')->setWidth("20");
		$this->phpexcel->getActiveSheet()->setCellValue('I'.$row_data, $value['estado']);
		$this->phpexcel->getActiveSheet()->setCellValue('J'.$row_data, $value['operador_asignado']);
		$this->phpexcel->getActiveSheet()->getColumnDimension('J')->setWidth("20");
		$this->phpexcel->getActiveSheet()->setCellValue('K'.$row_data, $value['operador_nombre_apellido']);
		$this->phpexcel->getActiveSheet()->getColumnDimension('K')->setWidth("30");
		$this->phpexcel->getActiveSheet()->setCellValue('L'.$row_data, $value['fecha_ultima_actividad']);
		$this->phpexcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");
		$row_data++;
		}
		header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Disposition: attachment;filename="Gestion-'.$desde.'_'.$hasta.'.xlsx"');
		header('Cache-Control: max-age=0');
		header ('Content-Type: application / vnd.ms-excel');
		$objGravar = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objGravar->save('php://output');
		exit();
	}

	private function _gestion($tipo_solicitud,$buro,$estado,$operador_asignado,$desde,$hasta,$canal)
	{	
		$params['tipo_solicitud'] = (isset($tipo_solicitud) && $tipo_solicitud != 'TODOS') ? $tipo_solicitud: NULL;
		$params['respuesta_analisis'] = (isset($buro) && $buro != 'TODOS') ? $buro: NULL;
		$params['estado'] = (isset($estado) && $estado != 'TODOS') ? $estado: NULL;
		$params['operador_asignado'] = (isset($operador_asignado) && $operador_asignado != 'TODOS')? $operador_asignado: NULL;
		$params['desde'] = isset($desde) ? date_to_string($desde, 'Y-m-d', FALSE, 'd-m-Y'): NULL;
		$params['hasta'] = isset($hasta) ? date_to_string($hasta, 'Y-m-d', FALSE, 'd-m-Y'): NULL;
		return $data['solicitudes'] = $this->gestion_model->solicitudes($params);
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