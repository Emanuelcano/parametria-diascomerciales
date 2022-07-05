<?php
//defined('BASEPATH') or exit('No direct script access allowed');

class Auditoria extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata("is_logged_in")) 
        {
			$this->load->model("tesoreria/Tesoreria_model", "creditos");

		} else
		{
			redirect(base_url()."login/logout");
		}
	}


	public function index()
	{
		$title['title'] = "Auditoria";
		$this->load->view('layouts/header',$title);
		$this->load->view('layouts/nav');
		$this->load->view('layouts/sidebar');
        $this->load->view('auditoria/auditoria');
        $this->load->view('layouts/footer');
	}

	public function indicadores(){

		

		$time_start = microtime(true);
	 
		
	 
	 

		$tipo_repo   = $this->input->post('slc_tiporepo');
		$dFechadeR   = $this->input->post('fecde');
        $dia       = substr($dFechadeR, 8, 2); //29/07/2017
        $mes       = substr($dFechadeR, 5, 2);
        $anio      = substr($dFechadeR, 0, 4);
        $fechadesde = $anio .'-'. $mes .'-'.  $dia;

        $dFechahR   = $this->input->post('fech');
        $dia       = substr($dFechahR, 8, 2); //29/07/2017
        $mes       = substr($dFechahR, 5, 2);
        $anio      = substr($dFechahR, 0, 4);
        $fechahasta = $anio .'-'. $mes .'-'.  $dia;


		if ($tipo_repo==1) {    //INGRESO
		$data['indicadores'] = $this->creditos->getIngresos($fechadesde,$fechahasta);
		$data['direccion'] = 'formIngresos';
		}else{    //EGRESO

		$data['indicadores'] = $this->creditos->getEgresosPagado($fechadesde,$fechahasta);
		$data['direccion'] = 'formEgresos';
		}

		$time_end = microtime(true);
		$time_total = $time_end - $time_start;
		
		echo round($time_total,2)." segundos";
		
		//$this->output->enable_profiler(TRUE);
		$this->load->view('auditoria/indicadores',['data' => $data]);

		

	}

	

    public function generarExcelEgresos($fechadesde,$fechahasta){
		$this->load->library('PHPExcel');
		$arquivo = './db/reporte.xlsx';
		$Planilla = $this->phpexcel;

		

		$Planilla->setActiveSheetIndex(0)->setCellValue('A1', 'Tipo Doc');
		$Planilla->setActiveSheetIndex(0)->setCellValue('B1', 'Documento');
		$Planilla->setActiveSheetIndex(0)->setCellValue('C1', 'Nombre Apellido');
		$Planilla->setActiveSheetIndex(0)->setCellValue('D1', 'Monto');
		$Planilla->setActiveSheetIndex(0)->setCellValue('E1', 'Fecha');
		$Planilla->setActiveSheetIndex(0)->setCellValue('F1', 'Banco');
		$Planilla->setActiveSheetIndex(0)->setCellValue('G1', 'Ruta');




		$c = 1;
		foreach( $this->creditos->getEgresosPagado($fechadesde,$fechahasta) as $row ):
			$c++;
			$Planilla->setActiveSheetIndex(0)->setCellValue( 'A' . $c, $row->tipo_doc );
			$Planilla->setActiveSheetIndex(0)->setCellValue( 'B' . $c, $row->documento );
			$Planilla->setActiveSheetIndex(0)->setCellValue( 'C' . $c, $row->nombres." ".$row->apellidos );
			$Planilla->setActiveSheetIndex(0)->setCellValue( 'D' . $c, $row->MONTO );
			$Planilla->getActiveSheet()->getStyle("D{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('D'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue( 'E' . $c, $row->fecha_carga );
			$Planilla->setActiveSheetIndex(0)->setCellValue( 'F' . $c, $row->Nombre_Banco );
			$Planilla->setActiveSheetIndex(0)->setCellValue( 'G' . $c, $row->ruta_txt );
		endforeach;

		$Planilla->getActiveSheet()->setTitle('Planilla 1');

		$objGravar = PHPExcel_IOFactory::createWriter($Planilla, 'Excel2007');
		//$objGravar->save($arquivo);
		header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Disposition: attachment;filename="ReporteEgresos'.date("Ymd").'.xlsx"');
		header('Cache-Control: max-age=0');
		$objGravar->save('php://output');
		//echo 'Planilla Gerada com sucesso !  :)';

	}


	public function generarExcelIngresos($fechadesde,$fechahasta){
		$this->load->library('PHPExcel');
		$arquivo = './db/reporte.xlsx';
		$Planilla = $this->phpexcel;

		

		$Planilla->setActiveSheetIndex(0)->setCellValue('A1', 'Tipo Documento');
		$Planilla->setActiveSheetIndex(0)->setCellValue('B1', 'Documento');
		$Planilla->setActiveSheetIndex(0)->setCellValue('C1', 'Nombres');
		$Planilla->setActiveSheetIndex(0)->setCellValue('D1', 'Apellidos');
		$Planilla->setActiveSheetIndex(0)->setCellValue('E1', 'Fecha Otorgamiento');
		$Planilla->setActiveSheetIndex(0)->setCellValue('F1', 'Monto Prestado');
		$Planilla->setActiveSheetIndex(0)->setCellValue('G1', 'Plazo');
		$Planilla->setActiveSheetIndex(0)->setCellValue('H1', 'ref_epayco');
		$Planilla->setActiveSheetIndex(0)->setCellValue('I1', 'Fecha Vencimiento');
		$Planilla->setActiveSheetIndex(0)->setCellValue('J1', 'Fecha Cobro');
		$Planilla->setActiveSheetIndex(0)->setCellValue('K1', 'Monto Cobrado');
		$Planilla->setActiveSheetIndex(0)->setCellValue('L1', 'Dias de Plazo');
		$Planilla->setActiveSheetIndex(0)->setCellValue('M1', 'Dias de Pago');
		$Planilla->setActiveSheetIndex(0)->setCellValue('N1', 'Dias de Mora');
		$Planilla->setActiveSheetIndex(0)->setCellValue('O1', 'Tasa de Interes');
		$Planilla->setActiveSheetIndex(0)->setCellValue('P1', 'Seguro');
		$Planilla->setActiveSheetIndex(0)->setCellValue('Q1', 'Administracion');
		$Planilla->setActiveSheetIndex(0)->setCellValue('R1', 'tecnologia');
		$Planilla->setActiveSheetIndex(0)->setCellValue('S1', 'iva');
		$Planilla->setActiveSheetIndex(0)->setCellValue('T1', 'interes mora');
		$Planilla->setActiveSheetIndex(0)->setCellValue('U1', 'INTERES');
		$Planilla->setActiveSheetIndex(0)->setCellValue('V1', 'SEGURO');
		$Planilla->setActiveSheetIndex(0)->setCellValue('W1', 'ADMINISTRACION');
		$Planilla->setActiveSheetIndex(0)->setCellValue('X1', 'TECNOLOGIA');
		$Planilla->setActiveSheetIndex(0)->setCellValue('Y1', 'IVA');
		$Planilla->setActiveSheetIndex(0)->setCellValue('Z1', 'TOTAL TIEMPO');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AA1', 'INTERES');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AB1', 'SEGURO');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AC1', 'ADMINISTRACION');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AD1', 'TECNOLOGIA');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AE1', 'IVA');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AF1', 'TOTAL A TIEMPO');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AG1', 'INTERES MORA');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AH1', 'TECNOLOGIA MORA');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AI1', 'TOTAL MORA');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AJ1', 'TOTAL COBRAR');
		$Planilla->setActiveSheetIndex(0)->setCellValue('AK1', 'DIFERENCIA');
		
		$Planilla->getActiveSheet()->getStyle('A1:S1')->applyFromArray
	          (
	            array('fill' =>
	                    array(
	                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
	                      'color' =>
	                        array(
	                          'rgb' => 'AFAFAF'
	                        )
	                  ),
	                  'font' =>
	                    array(
	                      'bold' => true
	                  )
	            )
	          );


		$Planilla->getActiveSheet()->getStyle('T1:Y1')->applyFromArray
	          (
	            array('fill' =>
	                    array(
	                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
	                      'color' =>
	                        array(
	                          'rgb' => '9BC2E6'
	                        )
	                  ),
	                  'font' =>
	                    array(
	                      'bold' => true
	                  )
	            )
	          );


        $Planilla->getActiveSheet()->getStyle('Z1:AF1')->applyFromArray
	          (
	            array('fill' =>
	                    array(
	                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
	                      'color' =>
	                        array(
	                          'rgb' => 'FFFF00'
	                        )
	                  ),
	                  'font' =>
	                    array(
	                      'bold' => true
	                  )
	            )
	          );

      $Planilla->getActiveSheet()->getStyle('AG1:AJ1')->applyFromArray
	          (
	            array('fill' =>
	                    array(
	                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
	                      'color' =>
	                        array(
	                          'rgb' => 'FFC000'
	                        )
	                  ),
	                  'font' =>
	                    array(
	                      'bold' => true
	                  )
	            )
	          );

      $Planilla->getActiveSheet()->getStyle('AK1')->applyFromArray
	          (
	            array('fill' =>
	                    array(
	                      'type' => PHPExcel_Style_Fill::FILL_SOLID,
	                      'color' =>
	                        array(
	                          'rgb' => 'A9D08E'
	                        )
	                  ),
	                  'font' =>
	                    array(
	                      'bold' => true
	                  )
	            )
	          );

      




		$c = 1;
		foreach( $this->creditos->getIngresos($fechadesde,$fechahasta) as $row ):
			$c++;

			$Planilla->setActiveSheetIndex(0)->setCellValue('A' . $c, $row->tipo_doc);
			$Planilla->setActiveSheetIndex(0)->setCellValue('B' . $c, $row->documento);
			$Planilla->setActiveSheetIndex(0)->setCellValue('C' . $c, $row->nombres);
			$Planilla->setActiveSheetIndex(0)->setCellValue('D' . $c, $row->apellidos);
			$Planilla->setActiveSheetIndex(0)->setCellValue('E' . $c, $row->fecha_otorgamiento);
			$Planilla->setActiveSheetIndex(0)->setCellValue('F' . $c, $row->monto_prestado);
			$Planilla->setActiveSheetIndex(0)->setCellValue('G' . $c, $row->plazo);
			$Planilla->setActiveSheetIndex(0)->setCellValue('H' . $c, $row->ref_epayco);
			$Planilla->setActiveSheetIndex(0)->setCellValue('I' . $c, $row->fecha_vencimiento);
			$Planilla->setActiveSheetIndex(0)->setCellValue('J' . $c, $row->fecha_cobro);
			$Planilla->setActiveSheetIndex(0)->setCellValue('K' . $c, $row->monto_cobrado);
			$Planilla->getActiveSheet()->getStyle("K{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('K'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('L' . $c, $row->dias_plazo);
			$Planilla->getActiveSheet()->getStyle("L{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->setActiveSheetIndex(0)->setCellValue('M' . $c, $row->dias_pago);
			$Planilla->getActiveSheet()->getStyle("M{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->setActiveSheetIndex(0)->setCellValue('N' . $c, $row->dias_mora);
			$Planilla->getActiveSheet()->getStyle("N{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->setActiveSheetIndex(0)->setCellValue('O' . $c, $row->tasa_interes);
			$Planilla->setActiveSheetIndex(0)->setCellValue('P' . $c, $row->seguro);
			$Planilla->setActiveSheetIndex(0)->setCellValue('Q' . $c, $row->administracion);
			$Planilla->getActiveSheet()->getStyle("Q{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('Q'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('R' . $c, $row->tecnologia);
			$Planilla->getActiveSheet()->getStyle("R{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('R'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('S' . $c, $row->iva);
			$Planilla->getActiveSheet()->getStyle("S{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('S'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('T' . $c, $row->interes_mora);
			$Planilla->getActiveSheet()->getStyle("T{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('T'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('U' . $c, '=+F'.$c.'*((1+(O'.$c.'/100))^((L'.$c.')/360)-1)');
			$Planilla->getActiveSheet()->getStyle("U{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('U'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('V' . $c, '=+F'.$c.'*P'.$c.'/100');
			$Planilla->getActiveSheet()->getStyle("V{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('V'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('W' . $c, '=+Q'.$c);
			$Planilla->getActiveSheet()->getStyle("W{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('W'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('X' . $c, '=+R'.$c.'*L'.$c);//
			$Planilla->getActiveSheet()->getStyle("X{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('X'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('Y' . $c, '=+(W'.$c.'+X'.$c.')*S'.$c.'/100');//
			$Planilla->getActiveSheet()->getStyle("Y{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('Y'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('Z' . $c, '=SUM(U'.$c.':Y'.$c.')+F'.$c);//
			$Planilla->getActiveSheet()->getStyle("Z{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('Z'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AA' . $c, '=+F'.$c.'*((1+(O'.$c.'/100))^((M'.$c.'-N'.$c.')/360)-1)');//
			$Planilla->getActiveSheet()->getStyle("AA{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AA'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AB' . $c, '=+F'.$c.'*P'.$c.'/100');//
			$Planilla->getActiveSheet()->getStyle("AB{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AB'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AC' . $c, '=+Q'.$c);//
			$Planilla->getActiveSheet()->getStyle("AC{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AC'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AD' . $c, '=+R'.$c.'*(M'.$c.'-N'.$c.')');//
			$Planilla->getActiveSheet()->getStyle("AD{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AD'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AE' . $c, '=+(AC'.$c.'+AD'.$c.')*S'.$c.'/100');//
			$Planilla->getActiveSheet()->getStyle("AE{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AE'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AF' . $c, '=SUM(AA'.$c.':AE'.$c.')+F'.$c);//
			$Planilla->getActiveSheet()->getStyle("AF{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AF'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AG' . $c, '=((((T'.$c.'/100)*1.5)/365)*N'.$c.')*(AF'.$c.'-(AE'.$c.'+AB'.$c.'))');//
			$Planilla->getActiveSheet()->getStyle("AG{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AG'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AH' . $c, '=+R'.$c.'*N'.$c);//
			$Planilla->getActiveSheet()->getStyle("AH{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AH'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AI' . $c, '=+AG'.$c.'+AH'.$c);//
			$Planilla->getActiveSheet()->getStyle("AI{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AI'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AJ' . $c, '=+AI'.$c.'+AF'.$c);//
			$Planilla->getActiveSheet()->getStyle("AJ{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AJ'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
			$Planilla->setActiveSheetIndex(0)->setCellValue('AK' . $c, '=+K'.$c.'-AJ'.$c);
			$Planilla->getActiveSheet()->getStyle("AK{$c}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$Planilla->getActiveSheet()->getStyle('AK'.$c)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);





			
		endforeach;

		$Planilla->getActiveSheet()->setTitle('Reporte Ingresos');

		$objGravar = PHPExcel_IOFactory::createWriter($Planilla, 'Excel2007');
		//$objGravar->save($arquivo);
		header("Content-Type:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header('Content-Disposition: attachment;filename="ReporteIngresos'.date("Ymd").'.xlsx"');
		header('Cache-Control: max-age=0');
		$objGravar->save('php://output');
		//echo 'Planilla Gerada com sucesso !  :)';

	}

	

}


