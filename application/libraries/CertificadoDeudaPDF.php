<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    // Incluimos el archivo fpdf
// require_once APPPATH."third_party/fpdf/fpdf.php";
use Fpdf\Fpdf;
//Extendemos la clase Pdf de la clase fpdf para que herede todas sus variables y funciones
class CertificadoDeudaPDF extends FPDF{

    public function __construct() {
        parent::__construct();
        
    }
    protected $B = 0;
    protected $I = 0;
    protected $U = 0;
    protected $HREF = '';

    public function Header(){
        //logo
        $this->Image('Logo_Solventa.png',10,10,45); 
    }    

/* PRIMER CERTIFCADO DE DEUDA
    public function Body($data, $nombreArchivo, $fechas){
        if ($data[0]['abonos'] == '0') {
            $abonos = '0,00';
        }else {
            $abonos = $data[0]['abonos'];
        }
        $p1 = utf8_decode('SOLVENTA COLOMBIA S.A.S., certifica que el(a) señor(a) '.$data[0]['nombre'].' '.$data[0]['apellidos'].' identificado(a) con cédula de ciudadanía No. '.$data[0]['documento'].', registra a su nombre el préstamo No. '.$data[0]['id'].'-'.$data[0]['n_cuota'].' bajo la modalidad de microcrédito, el cual a la fecha posee los siguientes saldos, intereses y demás rendimientos:');
        $tabla1=1;
        $titulos_tabla1 = ['CONDICIONES INICIALMENTE PACTADAS', 'FECHA DE OTORGAMIENTO', 'FECHA DE VENCIMIENTO', 'CAPITAL PRESTADO', 'NUMERO CUOTAS', 'INTERES REMURATORIO', 'INTERES MORATORIO', 'IVA', 'VALOR CUOTA'];
        $datos_tabla1 = [$data[0]['fecha_otorgamiento'], $data[0]['fecha_vencimiento'], '$'.$data[0]['capital_prestado'], $data[0]['n_cuota'], $data[0]['tasa_interes'].'%', $data[0]['tasa_interes'].'% * 1,5', $data[0]['iva'].'%', '$'.$data[0]['valor_cuota']];
        $tabla2=2;
        $titulos_tabla2 = ['RUBROS CAUSADOS POR DIAS DE ATRASO', 'DIAS DE MORA', 'INTERES MORATORIO', 'HONORARIOS COBRANZA', 'TOTAL CAUSADO'];
        $datos_tabla2 = [$data[0]['dias_atraso'], '$'.$data[0]['interes_moratorio'], '$'.$data[0]['honorarios_cobranza'], '$'.$data[0]['total_causado']];
        $tabla3=3;
        $titulos_tabla3 = ['SALDO A LA FECHA', 'VALOR CUOTA', 'VALORES POR DIAS DE ATRASO', 'ABONOS', 'TOTAL A PAGAR'];
        $datos_tabla3 = ['$'.$data[0]['valor_cuota'], '$'.$data[0]['total_causado'], '$'.$abonos, '$'.$data[0]['total_pagar']]; 
        
        $fuenteP ='arial';
        $size_fuenteP=9;
        $size_tituloG=18;
        $size_subtituloP=12;
        $size_parrafo=10;
        
        $objeto->SetMargins(15, 12.5, 15); 
        $objeto->SetAutoPageBreak(true,12.5);
        
        $objeto->AddPage();// Agregamos una página

        //titulo (Resumen Diario)
        $objeto->SetFont($fuenteP,'B',$size_tituloG); // Estilos de fuente
        $objeto->SetY(35);
        $objeto->SetX(10);
        $objeto->Cell(185,8,iconv('UTF-8', 'Windows-1252','CERTIFICADO DE DEUDA'),0,0,'C'); // titulos
        $objeto->SetFont($fuenteP,'B',$size_tituloG); // Estilos de fuente
        $objeto->SetY(43);
        $objeto->SetX(10);
        $objeto->Cell(185,8,iconv('UTF-8', 'Windows-1252','Crédito No.'.$data[0]['id'].'-'.$data[0]['n_cuota']),0,0,'C'); // titulos
        $objeto->Ln(15); // Saltos de lineas
        
        $objeto->SetFont($fuenteP,'',$size_fuenteP);
        $objeto->MultiCell(178, 6, $p1,'C');
        $objeto->Ln(10);

        $objeto->SetX(62);
        $this->crear_tablas($objeto ,$titulos_tabla1, $datos_tabla1, $tabla1);
        $objeto->Ln(8);

        $objeto->SetX(62);
        $this->crear_tablas($objeto ,$titulos_tabla2, $datos_tabla2, $tabla2);
        $objeto->Ln(8);

        $objeto->SetX(62);
        $this->crear_tablas($objeto ,$titulos_tabla3, $datos_tabla3, $tabla3);
        $objeto->Ln(6);

        $objeto->SetFont($fuenteP,'',$size_fuenteP);
        $objeto->SetY(223);
        $objeto->SetX(15);
        $objeto->Cell(185,8,iconv('UTF-8', 'Windows-1252','Medios de pago via transferencia:'),0,0,'L');

        $objeto->SetFont($fuenteP,'',$size_fuenteP);
        $objeto->SetY(235);
        $objeto->SetX(15);
        $objeto->MultiCell(178, 4, iconv('UTF-8', 'Windows-1252','•       Pago en efectivo mediante Efecty: Acércate a la sucursal más cercana con tu cédula 
        y número de convenio de recaudo. Número de convenio de recaudo: 111694.'), 'C');
        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BBVA: Cuenta Corriente 00130833000100024482'), 'C');
        $objeto->Ln(5);
        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       AV VILLAS: Cuenta Corriente 17214552'), 'C');
        $objeto->Ln(5);
        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       CAJA SOCIAL: Cuenta Corriente 21003863935'), 'C');
        $objeto->Ln(5);
        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCO POPULAR: Cuenta Ahorros 22002213887-9'), 'C');
        $objeto->Ln(5);
        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCO DE OCCIDENTE: Cuenta Ahorros 232836379'), 'C');

        $this->Footer();

        $objeto->AddPage();

        $objeto->SetFont($fuenteP,'',$size_fuenteP);
        $objeto->SetY(30);
        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCO DE BOGOTÁ: Cuenta Ahorros 359056975'), 'C');
        $objeto->Ln(5);
        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCO ITAÚ: Cuenta Corriente 01441443-7'), 'C');
        $objeto->Ln(5);
        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCOLOMBIA: Cuenta Ahorros 201-043345-13'), 'C');
        $objeto->Ln(10);
        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','Solventa Colombia SAS, NIT: 901255144-5'), 'C');
        $objeto->Ln(10);
        $objeto->SetX(15);
        $objeto->MultiCell(178, 4, iconv('UTF-8', 'Windows-1252','El presente certificado se expide el día '.$fechas.', en la ciudad de Bogotá D.C., a solicitud del titular de los datos.'), 'C');
        $objeto->Ln(5);

        $objeto->SetX(15);
        $objeto->Cell(178, 6, iconv('UTF-8', 'Windows-1252','Cordialmente,'), 'C');
        $this->Image('firma.PNG',15,77,25); 

        $objeto->SetFont($fuenteP,'B',$size_fuenteP);
        $objeto->SetY(93);
        $objeto->SetX(15);
        $objeto->Cell(178, 4, iconv('UTF-8', 'Windows-1252', 'Miguel Moreno'), 'C');
        
        $objeto->Ln(4);
        $objeto->SetX(15);
        $objeto->Cell(178, 4, iconv('UTF-8', 'Windows-1252', 'Supervisor Cobranzas'), 'C');
        
        $objeto->Ln(4);
        $objeto->SetX(15);
        $objeto->Cell(178, 4, iconv('UTF-8', 'Windows-1252', 'SOLVENTA COLOMBIA S.A.S.'), 'C');
        $this->guardarPDF($objeto, $nombreArchivo);

    }

    public function crear_tablas($objeto, $titulos_tabla, $datos_tabla, $tabla)
    {
                
        $fuenteP ='arial';
        $size_fuenteP=9;
        $size_tituloG=18;
        $size_subtituloP=12;
        $size_parrafo=10;
        
        $objeto->SetTextColor(0);
        $objeto->SetFont($fuenteP,'B',$size_fuenteP);
        $objeto->SetFillColor(216,216,216);
        $objeto->Cell(90, 6, iconv('UTF-8', 'Windows-1252', $titulos_tabla[0]),1,1, 'C',1);

        $a = 0;
        $totales = count($datos_tabla);
        for ($i=1; $i < count($titulos_tabla); $i++) { 
            $objeto->SetTextColor(0);
        if ($a == ($totales-1)) {
            $objeto->SetFont($fuenteP,'B',$size_fuenteP);
            $objeto->SetFillColor(216,216,216);
        }else{
            $objeto->SetFont($fuenteP,'',$size_fuenteP);
            $objeto->SetFillColor(243,243,243);
        }
            $objeto->SetX(62);
            $objeto->Cell(65, 6, iconv('UTF-8', 'Windows-1252', $titulos_tabla[$i]),1,0, 'L',1);
            if ($tabla == 3 && $a == 2) {
                $objeto->SetTextColor(17,184,0);
                $objeto->Cell(25, 6, iconv('UTF-8', 'Windows-1252', $datos_tabla[$a]),1,1, 'R',1);
            }else{
                $objeto->Cell(25, 6, iconv('UTF-8', 'Windows-1252', $datos_tabla[$a]),1,1, 'R',1);
            }
            $a++;      
        }
    }
*/

/* SEGUNDO CERTIFICADO DE DEUDA */
function body($data, $nombreArchivo, $fechas){

    if (!isset($data[0]['abonos']) || $data[0]['abonos'] == '0') {
        $abonos = '0,00';
    }else {
        $abonos = $data[0]['abonos'];
    }
    
    $p1 = utf8_decode('SOLVENTA COLOMBIA S.A.S., certifica que el(a) señor(a) mencionada registra a su nombre un préstamos personal bajo la modalidad de microcrédito incremental revolvente, con el siguiente saldo actualizado a la fecha de día');
    $p2 = utf8_decode('Medios de pago para cancelación');
    $total_formateado=number_format(round($data[0]['monto_cobrar']));
    $titulos_tabla = ['FECHA VENCIMIENTO', 'DIAS MORA', 'SALDO ACTUALIZADO'];
    $datos_tabla = [$data[0]['fecha_vencimiento'], $data[0]['dias_atraso'], '$'.$total_formateado];
    
    $fuenteP ='arial';
    $size_fuenteP=9;
    $size_tituloG=18;
    $size_subtituloG=13;
    $size_subtituloP=12;
    $size_parrafo=10;
    $size_fuenteS = 8;

    $this->SetMargins(15, 12.5, 15); 
    $this->SetAutoPageBreak(true,12.5);
    $this->AddPage();

    $this->SetFont($fuenteP,'B',$size_subtituloP);
    $this->SetY(35);
    $this->SetX(17);
    $this->WriteHTML('<b>CERTIFICADO DE DEUDA</b> de <b>'.$data[0]['nombre'].' '.$data[0]['apellidos'].'</b> C.C.: <b>'.$data[0]['documento'].'</b> al <b>'.$fechas.'</b>');

    $this->Ln(10); // Saltos de lineas
    
    $this->SetFont($fuenteP,'',$size_fuenteP);
    $this->MultiCell(178, 5, $p1,'C');
    $this->Ln(10);

    $this->SetX(62);
    $this->crear_tablas($titulos_tabla, $datos_tabla);
    $this->Ln(10);
    
    $this->SetFont($fuenteP,'',$size_fuenteP);
    $this->SetX(17);
    $this->Cell(60,8,$p2,'C');
    
    $this->Ln(15);
    $this->SetFont($fuenteP,'',$size_fuenteP);
    $this->SetX(22);
    $this->Cell(178, 4, iconv('UTF-8', 'Windows-1252',"•       Efecty: Pago en efectivo mediante una sucursal con tu cédula y número de convenio de recaudo 111694."), 'C');
    $this->Ln(5);
    $this->SetX(22);
    $this->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCOLOMBIA: Cuenta Ahorros 201-043345-13.'), 'C');
    $this->Ln(5);
    $this->SetX(22);
    $this->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BBVA: Cuenta Corriente 00130833000100024482'), 'C');
    $this->Ln(5);
    $this->SetX(22);
    $this->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       AV VILLAS: Cuenta Corriente 17214552'), 'C');
    $this->Ln(5);
    $this->SetX(22);
    $this->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       CAJA SOCIAL: Cuenta Corriente 21003863935'), 'C');
    $this->Ln(5);
    $this->SetX(22);
    $this->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCO POPULAR: Cuenta Ahorros 22002213887-9'), 'C');
    $this->Ln(5);
    $this->SetX(22);
    $this->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCO DE OCCIDENTE: Cuenta Ahorros 232836379'), 'C');
    $this->Ln(5);
    $this->SetX(22);
    $this->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCO DE BOGOTA: Cuenta Ahorros 359056975'), 'C');
    $this->Ln(5);
    $this->SetX(22);
    $this->Cell(178, 6, iconv('UTF-8', 'Windows-1252','•       BANCO ITAU: Cuenta Corriente 01441443-7'), 'C');

    $this->Ln(15);
    $this->SetFont($fuenteP,'',$size_fuenteP);
    $this->SetX(17);
    $this->Cell(178, 4, iconv('UTF-8', 'Windows-1252',"Titulariada: Solventa Colombia SAS, NIT: 9012255144-5"), 'C');
    $this->Ln(8);
    $this->SetX(17);
    $this->Cell(178, 4, iconv('UTF-8', 'Windows-1252',"El presente certificado en la ciudad de Bogotá D.C., a solicitud del titular de los datos."), 'C');
    $this->Ln(8);
    $this->SetFont($fuenteP,'',$size_fuenteS);
    $this->SetX(17);
    $this->Cell(178, 4, iconv('UTF-8', 'Windows-1252',"Los datos de gastos, intereses e impuestos radican en el contrato y pagare firmado electronicamente por el usuario y en su email declarado"), 'C');
    
    $this->Ln(12);
    $this->SetX(17);
    $this->Cell(178, 4, iconv('UTF-8', 'Windows-1252',"Cordialmente,"), 'C');
    $this->Image('Logo_Solventa.png',30,200,35); 

    $this->Ln(23);
    $this->SetFont($fuenteP,'',$size_fuenteP);
    $this->SetX(17);
    $this->WriteHTML('<b>SOLVENTA COLOMBIA S.A.S.</b>');
    $this->Ln(5);
    $this->SetX(17);
    $this->Cell(178, 4, iconv('UTF-8', 'Windows-1252',"NIT: 901255144-5"), 'C');
    }


    function WriteHTML($html)
    {
        // HTML parser
        $html = str_replace("\n",' ',$html);
        $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)
        {
            if($i%2==0)
            {
                // Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                else
                    $this->Write(4,$e);
                }
            else
            {
                // Tag
                if($e[0]=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else
                {
                    // Extract attributes
                    $a2 = explode(' ',$e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach($a2 as $v)
                    {
                        if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag,$attr);
                }
            }
        }
    }

    public function crear_tablas($titulos_tabla, $datos_tabla)
    {
        $fuenteP ='arial';
        $size_fuenteP=9;
        $size_tituloG=18;
        $size_subtituloP=12;
        $size_parrafo=10;

        $a = 0;
        $totales = count($datos_tabla);
        for ($i=0; $i < count($titulos_tabla); $i++) { 
            $this->SetTextColor(0);
            if ($titulos_tabla[$i] == 'SALDO ACTUALIZADO') {
                $this->SetFont($fuenteP,'B',$size_fuenteP);
                $this->SetFillColor(215,215,215);
            }else{
                $this->SetFont($fuenteP,'',$size_fuenteP);
                $this->SetFillColor(243,243,243);
            }
            $this->SetX(35);
            $this->Cell(102, 6, iconv('UTF-8', 'Windows-1252', $titulos_tabla[$i]),1,0, 'L',1);
            if ($a == 2) {
                $this->SetFont($fuenteP,'B',$size_fuenteP);
            }
            $this->Cell(35, 6, iconv('UTF-8', 'Windows-1252', $datos_tabla[$a]),1,1, 'L',1);
            
            $a++;      
        }
    }

    function OpenTag($tag, $attr)
    {
        // Opening tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF = $attr['HREF'];
        if($tag=='BR')
            $this->Ln(5);
    }

    function CloseTag($tag)
    {
        // Closing tag
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF = '';
    }

    function SetStyle($tag, $enable)
    {
        // Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach(array('B', 'I', 'U') as $s)
        {
            if($this->$s>0)
                $style .= $s;
        }
        $this->SetFont('',$style);
    }

    function PutLink($URL, $txt)
    {
        // Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }
    
    //justy
    function Justify($text, $w, $h)
    {
        $tab_paragraphe = explode("\n", $text);
        $nb_paragraphe = count($tab_paragraphe);
        $j = 0;

        while ($j<$nb_paragraphe) {

            $paragraphe = $tab_paragraphe[$j];
            $tab_mot = explode(' ', $paragraphe);
            $nb_mot = count($tab_mot);

            // Handle strings longer than paragraph width
            $tab_mot2 = array();
            $k = 0;
            $l = 0;
            while ($k<$nb_mot) {

                $len_mot = strlen ($tab_mot[$k]);
                if ($len_mot<($w-5) )
                {
                    $tab_mot2[$l] = $tab_mot[$k];
                    $l++;    
                } else {
                    $m=0;
                    $chaine_lettre='';
                    while ($m<$len_mot) {

                        $lettre = substr($tab_mot[$k], $m, 1);
                        $len_chaine_lettre = $this->GetStringWidth($chaine_lettre.$lettre);

                        if ($len_chaine_lettre>($w-7)) {
                            $tab_mot2[$l] = $chaine_lettre . '-';
                            $chaine_lettre = $lettre;
                            $l++;
                        } else {
                            $chaine_lettre .= $lettre;
                        }
                        $m++;
                    }
                    if ($chaine_lettre) {
                        $tab_mot2[$l] = $chaine_lettre;
                        $l++;
                    }

                }
                $k++;
    }

        // Justified lines
        $nb_mot = count($tab_mot2);
        $i=0;
        $ligne = '';
        while ($i<$nb_mot) {

            $mot = $tab_mot2[$i];
            $len_ligne = $this->GetStringWidth($ligne . ' ' . $mot);

            if ($len_ligne>($w-5)) {

                $len_ligne = $this->GetStringWidth($ligne);
                $nb_carac = strlen ($ligne);
                $ecart = (($w-2) - $len_ligne) / $nb_carac;
                $this->_out(sprintf('BT %.3F Tc ET',$ecart*$this->k));
                $this->MultiCell($w,$h,$ligne);
                $ligne = $mot;

            } else {

                if ($ligne)
                {
                    $ligne .= ' ' . $mot;
                } else {
                    $ligne = $mot;
                }

            }
            $i++;
        }

                // Last line
                $this->_out('BT 0 Tc ET');
                $this->MultiCell($w,$h,$ligne);
                $j++;
        }
    }
    // El pie del pdf
    public function Footer(){
        $this->Image('footer.PNG',0,276,212);   
    }
    
}