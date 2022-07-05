<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Reporte extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('reporte/reporte_model', '', true);
	}

	public function index()
	{
		$title['title'] = "Reporte";
		$this->load->view('layouts/header',$title);
		$this->load->view('layouts/nav');
		$this->load->view('layouts/sidebar');
		$this->load->view('reporte/reporte');
		$this->load->view('layouts/footer');
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
}
