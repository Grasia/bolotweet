<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once INSTALLDIR . '/local/plugins/NotesPDF/lib/fpdf/fpdf.php';

class GenerarPDF extends FPDF
{
    
  static function content() {
      
      $pdf=new GenerarPDF();

$pdf->AddPage();
$pdf->SetFont('Times','',12);
//Aquí escribimos lo que deseamos mostrar...
$pdf->Output('apuntes','I'); 

}
   //Cabecera de página
   function Header()
   {

    $this->Image(Theme::path('logo.png'),10,8,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $this->Cell(0,0,iconv("UTF-8", "ISO-8859-1", "Apuntes Automáticos"),0,1,'C');
   
}

//Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}


}
