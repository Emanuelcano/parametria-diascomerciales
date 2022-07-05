<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    // Incluimos el archivo fpdf
    require_once APPPATH."third_party/fpdf/fpdf.php";

    class CREDIBEL_Pdf extends FPDF{

        public function __construct() {
            parent::__construct();
        }

         // El encabezado del PDF
        public function Header(){
            $this->Image(site_url().'public/images/logo.png',2,2,24);
            $this->SetFont('Arial','B',12);
            $this->Cell(30,10,'Title',1,0,'C');
       }
       // El pie del pdf
       public function Footer(){
           $this->SetY(-10);
           $this->SetFont('Arial','I',8);
           $this->Cell(0,10,'Pag. '.$this->PageNo().'/{nb}            '.date('d-m-Y'),0,0,'C');
      }

    }

?>