<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

    // Incluimos el archivo BarcodeGeneratorPNG
    require_once APPPATH."third_party/barcode/BarcodeGenerator.php";
    require_once APPPATH."third_party/barcode/BarcodeGeneratorPNG.php";


    //Extendemos la clase Pdf de la clase fpdf para que herede todas sus variables y funciones
    class CREDIBEL_BarcodeGeneratorPNG extends Picqer\Barcode\BarcodeGeneratorPNG {

    }
?>