<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Reportes extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('reporte/Reporte_model', 'Reporte_model', true);
		$this->load->model('reportes/Indicadores_model', 'indicadores_model', TRUE);
		$this->load->dbutil();		
	}

	public function index()
	{
		$data['title'] = "Reportes";
		$data['heading'] = 'Reportes';
		$this->load->helper('form'); 
		$query = $this->indicadores_model->get_tipo_solicitud('tipo_solicitud');
		$estados = $this->indicadores_model->get_tipo_solicitud('estado');
		$max = sizeof($query);		
		$data['dato']['TODOS'] = 'TODOS';
		for ($i=0; $i < $max; $i++) { 
			if($query[$i]['tipo_solicitud'] =='PROMOCION'){				
			}else{
				$data['dato'][$query[$i]['tipo_solicitud']]= $query[$i]['tipo_solicitud'];
			}
		}
		$max_ = sizeof($estados);		
		$data['estados']['TODOS'] = 'TODOS';
		for ($i=0; $i < $max_; $i++) { 
			if(!$estados[$i]['estado'] == null){
				$data['estados'][$estados[$i]['estado']]= $estados[$i]['estado'];
			}
		}
        $this->load->view('layouts/adminLTE__header', $data);
		$this->load->view('reportes/reportes_main', $data);
        $this->load->view('layouts/adminLTE__footer', $data);		
	}

	public function reporte()
	{
		$reporte_model		= $this->reporte_model;
		$request			=	$this->input->get();
		$reservation		=	$request['reservation'];

		$arrowFecha 		= explode("-", $reservation);
		$newfecha 			= date("Ymd\TH:m:s", strtotime($reservation . "- 1 month"));

		$fechaComoEntero	= strtotime($newfecha);
		$anio 				= date("Y", $fechaComoEntero);
		$mes 				= date("m", $fechaComoEntero);
		$ultimoDia			= $this->ultimoDiaMes($anio, $mes);

		$reporte = array(
			'datos' => $reporte_model->getReporte($reservation)
		);



		if (!is_null($reporte)) {
			$this->load->view('reporte/result', $reporte);
		} else {
			echo $mensaje = 'Datos Incorrectos, por favor verifique.';
		}
	}

	/**
	 * @param $anio
	 * @param $mes
	 * @return false|string
	 */
	public function ultimoDiaMes($anio, $mes)
	{
		return date("d", (mktime(0, 0, 0, $mes + 1, 1, $anio) - 1));
	}

	/**
	 * @param $values
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function exportExcel()
	{
		$this->load->library('PHPExcel');
		$arquivo = './db/reporte.xlsx';
		$Planilla = $this->phpexcel;

		$reporteRequest			=  $this->reporte();

		$spreadsheet			= new Spreadsheet();
		$sheet					= $spreadsheet->getActiveSheet();
		$i = 1;
		foreach ($reporteRequest as $item => $value) {
			$y = 1;
			foreach ($value as $reg => $data) {
				$sheet->setCellValueByColumnAndRow($y, $i, $data);
				$y++;
			}
			$i++;
		}

		$nomArch = 'reporte_' . date("Ymd-H:m:s") . '.xlsx';

		header('Content-Type: application/vnd.openxmlformats-ifficedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename=' . $nomArch);
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');


		try {
			$writer->save('php://output');
		} catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
			echo "Error al generar archivo.";
		}
	}

	public function vistaReporteVencimiento()
	{
		$this->load->view("reportes/vistaReporteVencimiento");
		return $this;
	}
	
	public function exportar_csv_vencimiento()
	{
		$rs_result=$this->Reporte_model->getVencimiento($this->input->post());
		//var_dump($rs_result);die;
		
		$this->load->library('PHPExcel');
		
		$Planilla = $this->phpexcel;

		$Planilla->setActiveSheetIndex(0);
		$Planilla->getActiveSheet(0)->setCellValue('A1', 'ID SOLICITUD');
        $Planilla->getActiveSheet(0)->setCellValue('B1', 'DOCUMENTO');
        $Planilla->getActiveSheet(0)->setCellValue('C1', 'NOMBRE');
        $Planilla->getActiveSheet(0)->setCellValue('D1', 'APELLIDO');
        $Planilla->getActiveSheet(0)->setCellValue('E1', 'TELEFONO');
        $Planilla->getActiveSheet(0)->setCellValue('F1', 'TIPO SOLICITUD');
        $Planilla->getActiveSheet(0)->setCellValue('G1', 'SITUACION LABORAL');
        $Planilla->getActiveSheet(0)->setCellValue('H1', 'MONTO COBRAR');
        $Planilla->getActiveSheet(0)->setCellValue('I1', 'FECHA VENCIMIENTO');
        $Planilla->getActiveSheet(0)->setCellValue('J1', 'ESTADO');
        $Planilla->getActiveSheet(0)->setCellValue('K1', 'DIAS ATRASO');
        $Planilla->getActiveSheet(0)->setCellValue('L1', 'ID OOPERADOR');
        $Planilla->getActiveSheet(0)->setCellValue('M1', 'OPERADOR');
		
		//recorrer con un foreach	
		$c=1;
		foreach ($rs_result as $key => $row) {		
			
			$c++;
			$Planilla->setActiveSheetIndex(0)->setCellValue('A'.$c, $row->id_solicitud);
            $Planilla->setActiveSheetIndex(0)->setCellValue('B'.$c, $row->documento);
            $Planilla->setActiveSheetIndex(0)->setCellValue('C'.$c, $row->NOMBRE);
            $Planilla->setActiveSheetIndex(0)->setCellValue('D'.$c, $row->APELLIDO);
            $Planilla->setActiveSheetIndex(0)->setCellValue('E'.$c, $row->telefono);
            $Planilla->setActiveSheetIndex(0)->setCellValue('F'.$c, $row->tipo_solicitud);
            $Planilla->setActiveSheetIndex(0)->setCellValue('G'.$c, $row->id_situacion_laboral);
            $Planilla->setActiveSheetIndex(0)->setCellValue('H'.$c, $row->monto_cobrar);
            $Planilla->setActiveSheetIndex(0)->setCellValue('I'.$c, $row->fecha_vencimiento);
			if ($row->estado == '') {	
				$estado = 'Vigente';			
				$Planilla->setActiveSheetIndex(0)->setCellValue('J'.$c, $estado);
			}else {
				$Planilla->setActiveSheetIndex(0)->setCellValue('J'.$c, $row->estado);				
			}
            $Planilla->setActiveSheetIndex(0)->setCellValue('K'.$c, $row->dias_atraso);
            $Planilla->setActiveSheetIndex(0)->setCellValue('L'.$c, $row->ID);
            $Planilla->setActiveSheetIndex(0)->setCellValue('M'.$c, $row->operador);				
			
		}		
		
		$Planilla->getActiveSheet(1)->setTitle('Reporte');
		$file_name='ReporteVencimiento'.date("Ymd").".xls";
        if (file_exists(URL_CSV_FOLDER.$file_name)) {
            unlink(URL_CSV_FOLDER.$file_name);
        }
        $objGravar = PHPExcel_IOFactory::createWriter($Planilla, 'Excel5');
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        header('Cache-Control: max-age=0');
        $objGravar->save(URL_CSV_FOLDER.$file_name);//server
    	echo $file_name;
		
	}

	public function vistaReporteSolicitud()
	{

		$data['title'] = "Reportes";
		$data['heading'] = 'Reportes';
		$this->load->helper('form'); 
		$query = $this->indicadores_model->get_tipo_solicitud('tipo_solicitud');
		$estados = $this->indicadores_model->get_tipo_solicitud('estado');
		$max = sizeof($query);		
		$data['dato']['TODOS'] = 'TODOS';
		for ($i=0; $i < $max; $i++) { 
			if($query[$i]['tipo_solicitud'] =='PROMOCION'){				
			}else{
				$data['dato'][$query[$i]['tipo_solicitud']]= $query[$i]['tipo_solicitud'];
			}
		}
		$max_ = sizeof($estados);		
		$data['estados']['TODOS'] = 'TODOS';
		for ($i=0; $i < $max_; $i++) { 
			if(!$estados[$i]['estado'] == null){
				$data['estados'][$estados[$i]['estado']]= $estados[$i]['estado'];
			}
		}

		$this->load->view("reportes/reporte_solicitudes", $data);
		return $this;
	}

	public function vistaReporteOriginacion()
	{
		$this->load->view("reportes/reporte_originacion");
		return $this;
	}

	public function datosOperadores()
	{
	
		$mostrarOpe = $this->Reporte_model->MostrarOperadores($this->input->post());
		echo json_encode($mostrarOpe);
	}

	public function exportar_reporte_asignacion()
	{
		$datosExcel = $this->Reporte_model->DatosAsignacion($this->input->post());
		//var_dump($datosExcel);die;
		
		$this->load->library('PHPExcel');
		if (ENVIRONMENT == 'development') {
			ini_set('memory_limit', '5G');
		}		
			//var_dump($datosExcel);die;
		$hoja = $this->phpexcel;
// Se agregan los titulos del reporte
		$hoja->setActiveSheetIndex(0);
		$hoja->getActiveSheet(0)->setCellValue('A1', 'ID SOLICITUD');
        $hoja->getActiveSheet(0)->setCellValue('B1', 'PASO');
        $hoja->getActiveSheet(0)->setCellValue('C1', 'FECHA ALTA');
        $hoja->getActiveSheet(0)->setCellValue('D1', 'FECHA ANALISIS');
        $hoja->getActiveSheet(0)->setCellValue('E1', 'DOCUMENTO');
        $hoja->getActiveSheet(0)->setCellValue('F1', 'NOMBRES');
        $hoja->getActiveSheet(0)->setCellValue('G1', 'APELLIDOS');
        $hoja->getActiveSheet(0)->setCellValue('H1', 'NOMBRE SITUACION');
        $hoja->getActiveSheet(0)->setCellValue('I1', 'TIPO SOLICITUD');
        $hoja->getActiveSheet(0)->setCellValue('J1', 'SITUACION LABORAL');
        $hoja->getActiveSheet(0)->setCellValue('K1', 'RESPUESTA ANALISIS');
        $hoja->getActiveSheet(0)->setCellValue('L1', 'ESTADO');
        $hoja->getActiveSheet(0)->setCellValue('M1', 'ID OPERADOR');
        $hoja->getActiveSheet(0)->setCellValue('N1', 'OPERADOR');
        $hoja->getActiveSheet(0)->setCellValue('O1', 'FECHA ASIGNADO');
        $hoja->getActiveSheet(0)->setCellValue('P1', 'FECHA APROBADO');
        $hoja->getActiveSheet(0)->setCellValue('Q1', 'HORA APROBADO');
//Se agregan los datos de la BD
		$c=2;
		foreach ($datosExcel as $fila) {					
			$hoja->setActiveSheetIndex(0)->setCellValue('A'.$c, $fila['idsolicitud']);
			$hoja->setActiveSheetIndex(0)->setCellValue('B'.$c, $fila['paso']);
			$hoja->setActiveSheetIndex(0)->setCellValue('C'.$c, $fila['fecha_alta']);
			$hoja->setActiveSheetIndex(0)->setCellValue('D'.$c, $fila['fecha_analisis']);
			$hoja->setActiveSheetIndex(0)->setCellValue('E'.$c, $fila['documento']);
			$hoja->setActiveSheetIndex(0)->setCellValue('F'.$c, $fila['nombres']);
			$hoja->setActiveSheetIndex(0)->setCellValue('G'.$c, $fila['apellidos']);
			$hoja->setActiveSheetIndex(0)->setCellValue('H'.$c, $fila['nombre_situacion']);
			$hoja->setActiveSheetIndex(0)->setCellValue('I'.$c, $fila['tipo_solicitud']);
			$hoja->setActiveSheetIndex(0)->setCellValue('J'.$c, $fila['id_situacion_laboral']);
			$hoja->setActiveSheetIndex(0)->setCellValue('K'.$c, $fila['respuesta_analisis']);
			$hoja->setActiveSheetIndex(0)->setCellValue('L'.$c, $fila['estado']);
			$hoja->setActiveSheetIndex(0)->setCellValue('M'.$c, $fila['idoperador']);
			$hoja->setActiveSheetIndex(0)->setCellValue('N'.$c, $fila['operador']);
			$hoja->setActiveSheetIndex(0)->setCellValue('O'.$c, $fila['fecha_asignado']);
			$hoja->setActiveSheetIndex(0)->setCellValue('P'.$c, $fila['fecha_aprobado']);
			$hoja->setActiveSheetIndex(0)->setCellValue('Q'.$c, $fila['hora_aprobado']);
			$c++;
		}		
		
		$hoja->getActiveSheet(1)->setTitle('Asignacion');
		$Nombre_doc='Reporte_Asignacion'.date("Ymd").".xls"; // cambiar extension a .csv para descargar csv
        if (file_exists(URL_CSV_FOLDER.$Nombre_doc)) {
            unlink(URL_CSV_FOLDER.$Nombre_doc);
        }
        $objGravar = PHPExcel_IOFactory::createWriter($hoja, 'Excel5');// Cambia a CSV para descargar formato csv
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment;filename="'.$Nombre_doc.'"');
        header('Cache-Control: max-age=0');
        $objGravar->save(URL_CSV_FOLDER.$Nombre_doc);
    	echo $Nombre_doc;
		
	}

	public function exportar_casos_devueltos()
	{
		$casos_devueltos = $this->Reporte_model->datosCasosDevueltos($this->input->post());

		$this->load->library('PHPExcel');

		$datos = $this->phpexcel;
// Se agregan los titulos del reporte
		$datos->setActiveSheetIndex(0);
		$datos->getActiveSheet(0)->setCellValue('A1', 'ID SOLICITUD');
        $datos->getActiveSheet(0)->setCellValue('B1', 'DOCUMENTO');
        $datos->getActiveSheet(0)->setCellValue('C1', 'NOMBRES');
        $datos->getActiveSheet(0)->setCellValue('D1', 'APELLIDOS');
        $datos->getActiveSheet(0)->setCellValue('E1', 'NOMBRE_SITUACION');
        $datos->getActiveSheet(0)->setCellValue('F1', 'OPERADOR_GESTION');
        $datos->getActiveSheet(0)->setCellValue('G1', 'ESTADO_SOLICITUD');
        $datos->getActiveSheet(0)->setCellValue('H1', 'OPERADOR_DEVUELTO');
        $datos->getActiveSheet(0)->setCellValue('I1', 'COMENTARIO');
//Se agregan los datos de la BD
		$c=2;
		foreach ($casos_devueltos as $fila) {					
			$datos->setActiveSheetIndex(0)->setCellValue('A'.$c, $fila['id_solic']);
			$datos->setActiveSheetIndex(0)->setCellValue('B'.$c, $fila['documento']);
			$datos->setActiveSheetIndex(0)->setCellValue('C'.$c, $fila['nombres']);
			$datos->setActiveSheetIndex(0)->setCellValue('D'.$c, $fila['apellidos']);
			$datos->setActiveSheetIndex(0)->setCellValue('E'.$c, $fila['nombre_situacion']);
			$datos->setActiveSheetIndex(0)->setCellValue('F'.$c, $fila['operador_gestion']);
			$datos->setActiveSheetIndex(0)->setCellValue('G'.$c, $fila['estado_solicitud']);
			$datos->setActiveSheetIndex(0)->setCellValue('H'.$c, $fila['operador_devuelve']);
			if (empty($fila['comentarios'])) {
				$datos->setActiveSheetIndex(0)->setCellValue('I'.$c, ' ');
			}else {
				$datos->setActiveSheetIndex(0)->setCellValue('I'.$c, $fila['comentarios']);
			}
			$c++;
		}		
		
		$datos->getActiveSheet(1)->setTitle('Casos Devueltos');
		$Nombre_doc='Casos_Devueltos'.date("Ymd").".xls"; // cambiar extension a .csv para descargar csv
        if (file_exists(URL_CSV_FOLDER.$Nombre_doc)) {
            unlink(URL_CSV_FOLDER.$Nombre_doc);
        }
        $objGravar = PHPExcel_IOFactory::createWriter($datos, 'Excel5');// Cambia a CSV para descargar formato csv
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment;filename="'.$Nombre_doc.'"');
        header('Cache-Control: max-age=0');
        $objGravar->save(URL_CSV_FOLDER.$Nombre_doc);
    	echo $Nombre_doc;
	}

	public function datosOperadoresFraude()
	{
		$mostrarOpeFraude = $this->Reporte_model->operadoresFraude();
		echo json_encode($mostrarOpeFraude);	
	}

	public function vistaReporteContable()
	{
		$this->load->view("reportes/reporte_contable");
	}

	public function exportar_reporte_gastos()
	{
		$datos = $this->Reporte_model->reporte_gastos($this->input->post());
		$this->load->library('PHPExcel');
		
		$hoja = $this->phpexcel;

		$hoja->setActiveSheetIndex(0);
		$hoja->getActiveSheet(0)->setCellValue('A1', 'ID GASTO');
        $hoja->getActiveSheet(0)->setCellValue('B1', 'DOCUMENTO');
        $hoja->getActiveSheet(0)->setCellValue('C1', 'DENOMINACION');
        $hoja->getActiveSheet(0)->setCellValue('D1', 'CONCEPTO');
        $hoja->getActiveSheet(0)->setCellValue('E1', 'NRO FACTURA');
        $hoja->getActiveSheet(0)->setCellValue('F1', 'FECHA EMISION');
        $hoja->getActiveSheet(0)->setCellValue('G1', 'FECHA VENCIMIENTO');
        $hoja->getActiveSheet(0)->setCellValue('H1', 'EXENTO');
        $hoja->getActiveSheet(0)->setCellValue('I1', 'SUB TOTAL');
        $hoja->getActiveSheet(0)->setCellValue('J1', 'DESCUENTO');
        $hoja->getActiveSheet(0)->setCellValue('K1', 'IMPUESTOS');
        $hoja->getActiveSheet(0)->setCellValue('L1', 'RETEFUENTE');
        $hoja->getActiveSheet(0)->setCellValue('M1', 'RETEICA');
        $hoja->getActiveSheet(0)->setCellValue('N1', 'IMPUESTO CONSUMO');
        $hoja->getActiveSheet(0)->setCellValue('O1', 'TOTAL PAGAR');
        $hoja->getActiveSheet(0)->setCellValue('P1', 'FECHA CREACION');
        $hoja->getActiveSheet(0)->setCellValue('Q1', 'ESTADO');

		$c=2;
		foreach ($datos as $fila) {					
			$hoja->setActiveSheetIndex(0)->setCellValue('A'.$c, $fila['id_gasto']);
			$hoja->setActiveSheetIndex(0)->setCellValue('B'.$c, $fila['nro_documento']);
			$hoja->setActiveSheetIndex(0)->setCellValue('C'.$c, $fila['denominacion']);
			$hoja->setActiveSheetIndex(0)->setCellValue('D'.$c, $fila['concepto']);
			$hoja->setActiveSheetIndex(0)->setCellValue('E'.$c, $fila['nro_factura']);
			$hoja->setActiveSheetIndex(0)->setCellValue('F'.$c, $fila['fecha_emision']);
			$hoja->setActiveSheetIndex(0)->setCellValue('G'.$c, $fila['fecha_vencimiento']);
			$hoja->setActiveSheetIndex(0)->setCellValue('H'.$c, $fila['exento']);
			$hoja->setActiveSheetIndex(0)->setCellValue('I'.$c, $fila['sub_total']);
			$hoja->setActiveSheetIndex(0)->setCellValue('J'.$c, $fila['descuento']);
			$hoja->setActiveSheetIndex(0)->setCellValue('K'.$c, $fila['impuesto']);
			$hoja->setActiveSheetIndex(0)->setCellValue('L'.$c, $fila['retefuente']);
			$hoja->setActiveSheetIndex(0)->setCellValue('M'.$c, $fila['reteica']);
			$hoja->setActiveSheetIndex(0)->setCellValue('N'.$c, $fila['impuesto_consumo']);
			$hoja->setActiveSheetIndex(0)->setCellValue('O'.$c, $fila['total_pagar']);
			$hoja->setActiveSheetIndex(0)->setCellValue('P'.$c, $fila['fecha_creacion']);
			$hoja->setActiveSheetIndex(0)->setCellValue('Q'.$c, $fila['estado']);
			$c++;
		}		

		$hoja->getActiveSheet(1)->setTitle('Reporte de Gastos');
		$Nombre_doc='ReporteGastos'.date("Ymd").".xls"; // cambiar extension a .csv para descargar csv
        if (file_exists(URL_CSV_FOLDER.$Nombre_doc)) {
			unlink(URL_CSV_FOLDER.$Nombre_doc);
        }
        $objGravar = PHPExcel_IOFactory::createWriter($hoja, 'Excel5');// Cambia a CSV para descargar formato csv
        header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Disposition: attachment;filename="'.$Nombre_doc.'"');
        $objGravar->save(URL_CSV_FOLDER.$Nombre_doc);
    	echo $Nombre_doc;
	}

	public function exportar_reporte_cobros(){ 
		$Nombre_doc='ReporteCobros'.date("Ymd").".csv";

		header("Content-Description: File Transfer"); 
		header("Content-Disposition: attachment; filename=$Nombre_doc"); 
		header("Content-Type: application/csv ");
		
		$fecha_inicial = date("Y-m-d", strtotime($_POST['sl_desde_cobro']));
		$fecha_final = date("Y-m-d", strtotime($_POST['sl_hasta_cobro']));
		$datos = $this->Reporte_model->reporte_cobros($fecha_inicial, $fecha_final);
		/* file creation */
		$file = fopen(URL_CSV_FOLDER.$Nombre_doc,'w'); 
		// $header = array("Username","Name","Gender","Email"); 
		$cabecera =['ID CREDITO'
		,'TIPO DE COMPROBANTE'
		,'CONSECUTIVO'
		,'DOCUMENTO TERCERO'
		,'SUCURSAL'
		,'CODIGO CENTRO'
		,'FECHA ELABORACION'
		,'SIGLA MONEDA'
		,'TASA CAMBIO'
		,'NOMBRE DE CLIENTE'
		,'EMAIL CONTACTO'
		,'ORDEN COMPRA'
		,'ORDEN ENTREGA'
		,'FECHA ORDEN ENTREGA'
		,'CODIGO PRODUCTO'
		,'DESCRIPCION'
		,'DOCUMENTO VENDEDOR'
		,'BODEGA'
		,'CANTIDAD PRODUCTO'
		,'VALOR UNITARIO'
		,'VALOR DESCUENTO'
		,'BASE AIU'
		,'COD IMPUESTO CARGO'
		,'COD IMPUESTO CARGO 2'
		,'COD IMPUESTO RETENCION'
		,'COD RETEL CA'
		,'COD RETEL IVA'
		,'COD FORMA DE PAGO'
		,'VALOR FORMA DE PAGO'
		,'FECHA VENCIMIENTO'
		,'FECHA COBRO'
		,'TELEFONO'
		,'DIRECCION 1'
		,'CIUDAD 1'
		,'DEPARTAMENTO'
		,'DIRECCION 2'
		,'CIUDAD 2'];

		$header = $cabecera;
		fputcsv($file, $header);
		foreach ($datos as $key=>$fila){ 
			if (is_null($fila['direccion_cliente']) && is_null($fila['direccion_cliente2'])) {
				continue;
			}		

			if($fila['valor_unitario2'] > 0){
				$valorUnitario = floatval($fila['valor_unitario']) + floatval($fila['valor_unitario2']);
			}else{
				$valorUnitario = floatval($fila['valor_unitario']);
			}	
			$unitario = number_format($valorUnitario, 2, '.', '.');

			if($fila['valor_forma_pago2'] > 0){
				$valorFormaPago = floatval($fila['valor_forma_pago']) + floatval($fila['valor_forma_pago2']);
			}else{
				$valorFormaPago = floatval($fila['valor_forma_pago']);
			}
			$forma_pago = number_format($valorFormaPago, 2, '.', '.');
			
				$line = [];
				$line = [
				$fila['idc']
				, $fila['tipo_de_comprobante']
				, $fila['consecutivo']
				, $fila['identificacion_tercero']
				, $fila['sucursal']
				, $fila['codigo_centro']
				, $fila['fecha_elaboracion']
				, $fila['sigla_moneda']
				, $fila['tasa_cambio']
				, $fila['nombre_cliente']
				, $fila['email_contacto']
				, $fila['orden_compra']
				, $fila['orden_entrega']
				, $fila['fecha_orden_entrega']
				, $fila['codigo_producto']
				, $fila['descripcion_producto']
				, $fila['identificador_vendedor']
				, $fila['codigo_bodega']
				, $fila['cantidad_producto']
				, $unitario
				, $fila['valor_descuento']
				, $fila['base_AIU']
				, $fila['codigo_impuesto_cargo']
				, $fila['codigo_impuesto_cargo_dos']
				, $fila['codigo_impuesto_retencion']
				, $fila['codigo_reteICA']
				, $fila['codigo_reteIVA']
				, $fila['codigo_forma_pago']
				, $forma_pago
				, $fila['fecha_vencimiento'] 
				, $fila['fecha_cobro']
				, $fila['numero_contacto']
				, $fila['direccion_cliente']
				, $fila['ciudad_cliente']	 
				, $fila['departamento_cliente']
				, $fila['direccion_cliente2']
				, $fila['ciudad_cliente2']];
				fputcsv($file,$line); 
			
		}
		echo $Nombre_doc;
		fclose($file);
	}
}

