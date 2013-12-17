<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once INSTALLDIR . '/local/plugins/NotesPDF/lib/fpdf/fpdf.php';

class GenerarPDF extends FPDF {

    static function contentAuto($idGroup, $notices, $modo) {

        $pdf = new GenerarPDF();

        $pdf->AddPage();
        $pdf->SetFont('Times', '', 12);

        foreach ($notices as $notice) {
            $pdf->Ln(10);
            $filterContent = $pdf->filtrarContenido($notice->content);
            $pdf->Write(5, $filterContent);
        }

        $pdf->Output('apuntes', 'D');
    }

    static function contentCustom($idGroup, $notices, $modo) {

        $pdf = new GenerarPDF();

        $pdf->AddPage();
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
        $this->Cell(0, 0, 'Apuntes Automáticos', 0, 1, 'C');
        $this->Ln(10);

        $this->SetFont('Arial', 'I', 12);
        $this->SetTextColor(199, 199, 199);
        $this->Write(5, 'Este documento es únicamente una recopilación de ideas. En ningún momento sustituye a los apuntes impartidos por el profesor.');
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
    
    function Portada() {
        
        
    }

    function filtrarContenido($content) {

        return ltrim(preg_replace('/(?:^|\s)!\w{1,64}/', '', $content));
    }

}
