<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once INSTALLDIR . '/local/plugins/NotesPDF/lib/fpdf/fpdf.php';

class GenerarPDF extends FPDF {

   var $modo;
   
    function __construct($modo = '') {
        parent::FPDF();

        $this->modo = $modo;

    }
    
     static function contentAuto($idGroup, $notices, $modo) {

        $pdf = new GenerarPDF($modo);
        $pdf->Portada($idGroup);
        $pdf->SetFont('Times', '', 12);

        foreach ($notices as $notice) {
            $pdf->Ln(10);
            $filterContent = $pdf->filtrarContenido($notice->content);
            $pdf->Write(5, $filterContent);
        }

        $pdf->Output('apuntes', 'D');
    }

    static function contentCustom($idGroup, $notices, $modo) {

        global $tipoApuntes;
        $tipoApuntes = $modo;
        
        
        $pdf = new GenerarPDF();

        $pdf->SetFont('Times', '', 12);

        foreach ($notices as $notice) {
            $pdf->Ln(10);
            $filterContent = $pdf->filtrarContenido($notice->content);

            $pdf->Write(5, $filterContent);
        }

        $pdf->Output('apuntes', 'D');
    }

    //Cabecera de página
    function Header() {

        $this->Image(Theme::path('logo.png'), 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(0, 0, 'Apuntes '. $this->modo, 0, 1, 'R');
        $this->Ln(10);

        $this->SetFont('Arial', 'I', 12);
        $this->SetTextColor(199, 199, 199);
        $this->MultiCell(0,6, 'Este documento es únicamente una recopilación de ideas. En ningún caso sustituye al material proporcionado por el profesor.',0);
    }

    //Pie de página
    function Footer() {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
    
    function Portada($idGroup) {
        
        $groupName = NotesPDF::getGroupByID($idGroup)->getBestName();
        
        $this->AddPage();
        $this->Ln(20);
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 0, $groupName . ' - ' . date('dS \of F Y'), 0, 0, 'R');
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0,7, 'Ideas más relevantes',1,0,'C');
        $this->Ln(10);
        
        
    }

    function filtrarContenido($content) {

        return ltrim(preg_replace('/(?:^|\s)!\w{1,64}/', '', $content));
    }

}
