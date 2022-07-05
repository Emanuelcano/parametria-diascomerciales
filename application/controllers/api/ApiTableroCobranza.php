<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'third_party/REST_Controller.php';
require_once APPPATH . 'third_party/Format.php';
use Restserver\Libraries\REST_Controller;
/**
 *
 */
class ApiTableroCobranza extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
        // Models
        $this->load->model("tablero_cobranza/TableroCobranza_model", "tablero");
        $this->load->model('operadores/Operadores_model','operadores',TRUE);

		
	}
	/**
	 * Armado de indicadores del tablero de cobranzas
	 * jira: SC80088477-440
	 * @return JSON
	 */
	public function tablero_acuerdos_post(){
		$id_operador = $this->input->post('id_operador');
		$tipo = $this->input->post('tipo');
		$currentDay = (int)date("d");
		if($currentDay <= 15){
			$lastMonth = date('Y-m-d 00:00:00', strtotime(date("Y-m-16")."- 1 month"));
			$lastDayOfLastMonth  = date('Y-m-t 23:59:59', strtotime(date("Y-m-d")."- 1 month"));
			//quincena_anterior
			//DESDE = anio / mes_anterior / 16
			$desde_1 = $lastMonth;
			//HASTA = anio / mes_anterior / ultimo_dia_mes_anterior
			$hasta_1 = $lastDayOfLastMonth;
			//quincena_actual
			//DESDE = anio / mes_actual / 01
			$desde_2 = date("Y-m-01 00:00:00");
			//HASTA = anio / mes_actual / 15
			$hasta_2 = date("Y-m-15 23:59:59");
		}else{
			//SI currentDay > 15
			//quincena_anterior = DESDE = anio / mes_actual / 01
			$desde_1 = date("Y-m-01 00:00:00");
			$hasta_1 = date("Y-m-15 23:59:59");
			//quincena_actual = DESDE = anio / mes_actual / 16
			$desde_2 = date("Y-m-16 00:00:00");
			$hasta_2 = date("Y-m-t 23:59:59");
		}
		if($tipo != 'actual'){
			$fecha = "BETWEEN '$desde_1' AND '$hasta_1'";
			$desde = $desde_1;
			$hasta = $hasta_1;
			$result = $this->tablero->acuerdos_gestiones($fecha,$id_operador);
		}else{
			$fecha = "BETWEEN '$desde_2' AND '$hasta_2'";
			$desde = $desde_2;
			$hasta = $hasta_2;
			$result = $this->tablero->acuerdos_gestiones($fecha,$id_operador);
		}
		if(!empty($result)){
			$status = parent::HTTP_OK;
            $response['status']['ok']	 = TRUE;
			$response['data']= $result;
			$response['fecha']= $fecha;
			$response['desde']= $desde;
			$response['hasta']= $hasta;
		}else{
			$status = parent::HTTP_OK;
			$response['status']['ok'] = FALSE;
		}
		$this->response($response, $status);

	}
	private function _cell_color($cells,$color)
	{ 
		$this->phpexcel->getActiveSheet()->getStyle($cells)->getFill() ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => $color) )); 
		return TRUE;
	}
	public function excel_get($desde,$hasta,$id_operador)
	{
		if($id_operador > 0){
			$id_operador = 'AND id_operador ='.$id_operador;
		}else{
			$id_operador = '';
		}
		$desde = str_replace("%20"," ",$desde);
		$hasta = str_replace("%20"," ",$hasta);
		$fecha = "BETWEEN '$desde' AND '$hasta'";
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
		// ESTILO DE COLUMNAS
    	$this->phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
    	$this->phpexcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
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
		$text_left= array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
		$text_right= array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
		$row_header = $header +2;
		$row_sub_header = $row_header+1;
		$border_style= array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM,'color' => array('rgb' => 'FFFFFF'))));
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_sub_header.':L'.$row_sub_header)->applyFromArray($border_style);
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_sub_header.':L'.$row_sub_header)->applyFromArray($text_center);
    	$this->phpexcel->getActiveSheet()->getStyle('A'.$row_sub_header.':L'.$row_sub_header)->getFont()->setSize($sub_header_font); 
 		$this->phpexcel->getActiveSheet()->setCellValue('A'.$row_sub_header, 'Id Acuerdo');
 		$this->_cell_color('A'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('B'.$row_sub_header, 'Fecha Gestion');
 		$this->_cell_color('B'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('C'.$row_sub_header, 'Fecha Acuerdo');
 		$this->_cell_color('C'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('D'.$row_sub_header, 'Monto Acuerdo');
 		$this->_cell_color('D'.$row_sub_header,'E7E6E6');
 		$this->phpexcel->getActiveSheet()->setCellValue('E'.$row_sub_header, 'Estado Acuerdo');
		$this->_cell_color('E'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('F'.$row_sub_header, 'Dias Atraso');
 		$this->_cell_color('F'.$row_sub_header,'E7E6E6'); 
		$this->phpexcel->getActiveSheet()->setCellValue('G'.$row_sub_header, 'Id Cliente');
		$this->_cell_color('G'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('H'.$row_sub_header, 'Documento');
 		$this->_cell_color('H'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('I'.$row_sub_header, 'Nombres');
		$this->_cell_color('I'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('J'.$row_sub_header, 'Apellidos');
		$this->_cell_color('J'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('K'.$row_sub_header, 'Id Operador');
		$this->_cell_color('K'.$row_sub_header,'E7E6E6');
		$this->phpexcel->getActiveSheet()->setCellValue('L'.$row_sub_header, 'Nombre Apellido');
		$this->_cell_color('L'.$row_sub_header,'E7E6E6');
		// CONTENIDO
		$row_data = $row_sub_header+1;	
		$datos = $this->tablero->generador_excel($fecha,$id_operador);
		foreach($datos as $key => $value){
			$this->phpexcel->getActiveSheet()->getStyle('D'.$row_data.':D'.$row_data)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$this->phpexcel->getActiveSheet()->getStyle('A'.$row_data.':C'.$row_data)->applyFromArray($text_center);
			$this->phpexcel->getActiveSheet()->getStyle('D'.$row_data.':D'.$row_data)->applyFromArray($text_right);
			$this->phpexcel->getActiveSheet()->getStyle('E'.$row_data.':E'.$row_data)->applyFromArray($text_left);
			$this->phpexcel->getActiveSheet()->getStyle('F'.$row_data.':H'.$row_data)->applyFromArray($text_center);
			$this->phpexcel->getActiveSheet()->getStyle('I'.$row_data.':J'.$row_data)->applyFromArray($text_left);
			$this->phpexcel->getActiveSheet()->getStyle('K'.$row_data.':L'.$row_data)->applyFromArray($text_center);
			$this->phpexcel->getActiveSheet()->setCellValue('A'.$row_data, $value['id_acuerdo']);
			$this->phpexcel->getActiveSheet()->getColumnDimension('A')->setWidth("25");
			$this->phpexcel->getActiveSheet()->setCellValue('B'.$row_data, $value['fecha_gestion']);
			$this->phpexcel->getActiveSheet()->setCellValue('C'.$row_data, $value['fecha_acuerdo']);
			$this->phpexcel->getActiveSheet()->setCellValue('D'.$row_data, $value['monto_acuerdo']);
			$this->phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth("20");
			$this->phpexcel->getActiveSheet()->setCellValue('E'.$row_data, $value['estado_acuerdo']);
			$this->phpexcel->getActiveSheet()->setCellValue('F'.$row_data, $value['dias_atraso']);
			$this->phpexcel->getActiveSheet()->getColumnDimension('F')->setWidth("20");
			$this->phpexcel->getActiveSheet()->setCellValue('G'.$row_data, $value['id_cliente']);
			$this->phpexcel->getActiveSheet()->getColumnDimension('G')->setWidth("20");
			$this->phpexcel->getActiveSheet()->setCellValue('H'.$row_data, $value['documento']);
			$this->phpexcel->getActiveSheet()->getColumnDimension('H')->setWidth("20");
			$this->phpexcel->getActiveSheet()->setCellValue('I'.$row_data, $value['nombres']);
			$this->phpexcel->getActiveSheet()->setCellValue('J'.$row_data, $value['apellidos']);
			$this->phpexcel->getActiveSheet()->getColumnDimension('J')->setWidth("20");
			$this->phpexcel->getActiveSheet()->setCellValue('K'.$row_data, $value['id_operador']);
			$this->phpexcel->getActiveSheet()->getColumnDimension('K')->setWidth("30");
			$this->phpexcel->getActiveSheet()->setCellValue('L'.$row_data, $value['nombre_apellido']);
			$this->phpexcel->getActiveSheet()->getColumnDimension('L')->setWidth("20");
			$row_data++;
		}
		header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Disposition: attachment;filename="Cant.Acuerdos-'.$desde.' '.$hasta.'.xlsx"');
		header('Cache-Control: max-age=0');
		header ('Content-Type: application / vnd.ms-excel');
		$objGravar = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objGravar->save('php://output');
		exit();
	}
	public function tableroCobranza_post(){
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		}
		//DEFINICION DE PERIODOS
		$tipoDTablero = $_POST['paramBusqueda'];
		$currentDay = (int)date("m");
		
		if ($tipoDTablero == "general") {
			if($currentDay <= 15){
			
				$lastMonth = date('Y-m-d 00:00:00', strtotime(date("Y-m-16")."- 1 month"));
				$lastDayOfLastMonth  = date('Y-m-t 23:59:59', strtotime(date("Y-m-d")."- 1 month"));
				//quincena_anterior
				//DESDE = anio / mes_anterior / 16
				$desde_1 = $lastMonth;
				//HASTA = anio / mes_anterior / ultimo_dia_mes_anterior
				$hasta_1 = $lastDayOfLastMonth;
				//quincena_actual
				//DESDE = anio / mes_actual / 01
				$desde_2 = date("Y-m-01 00:00:00");
				//HASTA = anio / mes_actual / 15
				$hasta_2 = date("Y-m-15 23:59:59");
			}else{
				//SI currentDay > 15
				//quincena_anterior = DESDE = anio / mes_actual / 01
				$desde_1 = date("Y-m-01 00:00:00");
				$hasta_1 = date("Y-m-15 23:59:59");
				//quincena_actual = DESDE = anio / mes_actual / 16
				$desde_2 = date("Y-m-16 00:00:00");
				$hasta_2 = date("Y-m-t 23:59:59");
			}

			$periodos = [
				'desde_1' => $desde_1,
				'hasta_1' => $hasta_1,
				'desde_2' => $desde_2,
				'hasta_2' => $hasta_2
			];
		}elseif ($tipoDTablero == "actual") {
			$desde_1 = date("Y-m-01 00:00:00");
			$hasta_2_temp = date('Y-m-d 00:00:00', strtotime($desde_1."+ 1 month"));
			$hasta_2 = date('Y-m-d 00:00:00', strtotime($hasta_2_temp."- 1 day"));

			$periodos = [
				'desde_1' => $desde_1,
				'hasta_1' => $hasta_2
			];
		}else{
			$hasta= date("Y-m-01 00:00:00");
			$hasta_1 = date("Y-m-d 00:00:00",  strtotime($hasta."- 1 days"));
			$desde_1 = date('Y-m-d 00:00:00', strtotime($hasta."- 1 month"));

			$periodos = [
				'desde_1' => $desde_1,
				'hasta_1' => $hasta_1
			];
		}
		// var_dump($periodos);die;


		// if($currentDay <= 15){
			
		// 	$lastMonth = date('Y-m-d 00:00:00', strtotime(date("Y-m-16")."- 1 month"));
		// 	$lastDayOfLastMonth  = date('Y-m-t 23:59:59', strtotime(date("Y-m-d")."- 1 month"));
		// 	//quincena_anterior
		// 	//DESDE = anio / mes_anterior / 16
		// 	$desde_1 = $lastMonth;
		// 	//HASTA = anio / mes_anterior / ultimo_dia_mes_anterior
		// 	$hasta_1 = $lastDayOfLastMonth;
		// 	//quincena_actual
		// 	//DESDE = anio / mes_actual / 01
		// 	$desde_2 = date("Y-m-01 00:00:00");
		// 	//HASTA = anio / mes_actual / 15
		// 	$hasta_2 = date("Y-m-15 23:59:59");
		// }else{
		// 	//SI currentDay > 15
		// 	//quincena_anterior = DESDE = anio / mes_actual / 01
		// 	$desde_1 = date("Y-m-01 00:00:00");
		// 	$hasta_1 = date("Y-m-15 23:59:59");
		// 	//quincena_actual = DESDE = anio / mes_actual / 16
		// 	$desde_2 = date("Y-m-16 00:00:00");
		// 	$hasta_2 = date("Y-m-t 23:59:59");
		// }
		
		// $periodos = [
		// 	'desde_1' => $desde_1,
		// 	'hasta_1' => $hasta_1,
		// 	'desde_2' => $desde_2,
		// 	'hasta_2' => $hasta_2
		// ];
		switch ( $this->input->post('paramBusqueda') ) {
			case 'general':
				# GENERAL
				$_model = $this->tablero->getAcuerdosAlcanzados($periodos);
				$table['table'] = "general";
				break;

			case 'actual':
				# actual
				$_model = $this->tablero->getTramoMoraActual($periodos);
				
				$table['table'] = "actual";
				break;
				
			case 'anterior':
				# anterior
				$_model = $this->tablero->getTramoMoraAnterior($periodos);
				$table['table'] = "anterior";
				break;
			
			default:
				# GENERAL
				$_model = $this->tablero->getAcuerdosAlcanzados($periodos);
				$table['table'] = "general";
				break;
		}
		if(!empty($_model)){
			foreach($_model as $value){
				$cobradores[] = array_merge($value,$periodos);
			}
		}
		if(!empty($cobradores) < 1){
			$cobradores == '';
		}
		
		//$cobradores = array("1","3","4,"6","43","56","7","86","3");
		$this->response(['data' =>$cobradores,'table' => $table]);
	}
   
}
