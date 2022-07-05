<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  
    class Pdf extends CI_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->library('response');
            $this->load->library('CREDIBEL_Pdf');
        }

         public function index($mode=false){
           
            $status=200;
            $page='pdf/pdf';
            $data['title']='CodBarras';
            $data['response']=1;
            $this->response->load($page, $status, $data, $mode);

          }

        

         public function crearpdf(){


          $titulo = $this->input->post('titulo');
          $cuerpo = $this->input->post('cuerpo');
           $ram=rand(9999, 99999999);
          $pdf=new FPDF();
          $pdf->AliasNbPages();
          $pdf->AddPage();
          $pdf->Image(site_url().'public/images/logo.png',2,2,24); 
          $pdf->SetFont('Arial','B',16);
          $pdf->Cell(0,10,$titulo,0,0,'C');
          $pdf->SetY(25);
          $pdf->SetFont('Arial','B',12); 
          $pdf->MultiCell(190,5,utf8_decode($cuerpo),0,'J');
          $pdf->SetY(266);
           $pdf->SetFont('Arial','I',8);
           $pdf->Cell(0,10,'Pag. '.$pdf->PageNo().'/{nb}',0,0,'C');
           // $pdf->Cell(0,10,date('d-m-Y'),0,1,'C');
          $pdf->Output('public/pdf/'.$ram.'_archivo.pdf','F');

          $ruta='public/pdf/'.$ram.'_archivo.pdf';
          echo $ruta;
          }

    
       

    }
  
?>