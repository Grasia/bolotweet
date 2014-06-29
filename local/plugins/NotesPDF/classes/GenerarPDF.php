<?php

/**
 * 
 * BoloTweet 2.0
 *
 * @author   Alvaro Ortego <alvorteg@ucm.es>
 *
 */
require_once INSTALLDIR . '/local/plugins/NotesPDF/lib/fpdf/fpdf.php';

class GenerarPDF extends FPDF {

    var $modo;

    function __construct($modo = '') {
        parent::FPDF();

        $this->modo = $modo;
    }

    static function content($idGroup, $notices, $modo) {

        $pdf = new GenerarPDF($modo);
        $pdf->Portada($idGroup);
        $pdf->SetFont('Times', '', 12);

        $i = 1;

        $authorAndNotice = array();
        $authors = array();
        $days = array();
        
        $date = '';
        
        foreach ($notices as $notice) {
            
            $dateTmp = substr($notice->created,0,10);
            if($dateTmp != $date){
                
                $date = $dateTmp;
                $pdf->Ln(10);
                
                $pdf->SetFont('Arial', 'BU', 12);
                // Escribimos la fecha
                $pdf->Write(5, $date);
                $pdf->Ln(5);
                
                $tags = NotesPDF::getTopTagsInADay($idGroup, $dateTmp);
                //Escribimos los tags
                $pdf->SetFont('Arial', 'I', 12);
                // Escribimos la fecha
                $pdf->Write(5, 'Top tags:  ' . implode(', ',$tags));
                $pdf->Ln(5);
                
                $pdf->SetFont('Times', '', 12);
                
            }
            $pdf->Ln(10);
            $filterContent = $pdf->filtrarContenido($notice->content);
            $filterContent = $filterContent . " [" . $i . "]";
            $pdf->Write(5, $filterContent);

            $authorAndNotice[$i] = strtoupper($notice->getProfile()->getBestName());
            $authors[] = strtoupper($notice->getProfile()->getBestName());
            $i = $i + 1;
        }


        $pdf->Fuentes($authorAndNotice, array_unique($authors));

        $pdf->Output('apuntes.pdf', 'D');
    }

    //Cabecera de página
    function Header() {

        $this->Image(Theme::path('logo.png'), 10, 8, 33);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(0, 0, 'Apuntes ' . $this->modo, 0, 1, 'R');
        $this->Ln(10);

        $this->SetFont('Arial', 'I', 12);
        $this->SetTextColor(199, 199, 199);
        $this->MultiCell(0, 6, 'Este documento es únicamente una recopilación de ideas. En ningún caso sustituye al material proporcionado por el profesor.', 0);
        $this->Ln(5);
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
        $this->Ln(7);
        $this->SetFont('Arial', 'I', 12);
        setlocale(LC_ALL, "es_ES");
        $this->Cell(0, 0, strtoupper($groupName) . ' - ' . strftime("%d de %B del %Y"), 0, 0, 'R');
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 7, 'Ideas Seleccionadas', 1, 0, 'C');
        $this->Ln(10);
    }

    function filtrarContenido($content) {

        return ltrim(preg_replace('/(?:^|\s)!\w{1,64}/', '', $content));
    }

    function Fuentes($authorAndNotice, $authors) {


        $this->addPage();
        $this->SetFont('Arial', 'B', 15);

        $this->Ln(10);

        $this->Cell(0, 7, 'Autores', 1, 0, 'C');
        $this->Ln(5);
        $this->SetFont('Times', '', 12);

        foreach ($authors as $author) {

            $noticeIds = array_keys($authorAndNotice, $author);

            $linea = $author . " ->";

            foreach ($noticeIds as $id) {

                $linea .= " " . "[" . $id . "] ";
            }

            $this->Ln(10);
            $this->Write(5, $linea);
        }
    }

}
