<?php

// Include the main TCPDF library (search for installation path).
require_once('TCPDF-master/tcpdf.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF
{
    //Page header
    public function Header()
    {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        // set bacground image
        // $img_file = K_PATH_IMAGES.'professional-certificate-model-7.jpg';
        $img_file = DOC_ROOT . FILE_PATH_TO_UK . 'images/pdf-templates/default-certificate-model.jpg';

        $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(SITE_NAME);
$pdf->SetAuthor(SITE_NAME);
$pdf->SetTitle('Professional Course e-Certificate');
$pdf->SetSubject('Professional Course e-Certificate');
$pdf->SetKeywords('Professional Course, Certificate');

// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(20.7, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

// remove default footer
$pdf->setPrintFooter(false);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

//REMOVE CELL PADDING
$pdf->SetCellPadding(0);

// REMOVE TAG TOP AND BOTTOM MARGINS
$tagvs = array('p' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)));
$pdf->setHtmlVSpace($tagvs);

// ---------------------------------------------------------

// set font
$pdf->SetFont('century_gothic_b', 'B', 25);
//$pdf->SetTextColor(47, 52, 144); // Blue

// add a page
$pdf->AddPage();

$pdf->Ln(70);

// Set the x-coordinate to center the box
$pdf->SetX(40);

// Vertical alignment with auto-resize of text in cell
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
$pdf->MultiCell(130, 20, trim($pdf_full_name), 0, 'C', 0, 0, '', '', true, 0, false, true, 20, 'M', true); //clean_name($pdf_full_name)

$pdf->Ln(38);

// Set the x-coordinate to center the box
$pdf->SetX(40);

// set font
$pdf->SetFont('century_gothic_b', 'B', 26); // helvetica
$pdf->SetTextColor(237, 29, 36); // red

// Vertical alignment with auto-resize of text in cell
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
$pdf->MultiCell(130, 25, ucwords($pdf_prog_name), 0, 'C', 0, 0, '', '', true, 0, false, true, 25, 'M', true);

$pdf->Ln(46);

// set font
$pdf->SetFont('century_gothic_b', 'B', 10);
$pdf->SetTextColor(0, 0, 0); // Black

// Vertical alignment with auto-resize of text in cell
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
$pdf->MultiCell(170, 20, ucwords($pdf_prog_completion_date . '.'), 0, 'C', 0, 0, '', '', true, 0, false, true, 20, 'M', true);

$pdf->Ln(35);

// set font
$pdf->SetFont('century_gothic', '', 10);

// Vertical alignment with auto-resize of text in cell
// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
$pdf->MultiCell(58, 10, ucwords($pdf_certificate_number), 0, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M', true);

if ($pdf_co_issue_cert_biz_logo && $pdf_co_issue_cert_biz_text) {
    $pdf->Ln(-5);

    // set font
    $pdf->SetFont('century_gothic', '', 8);
    $pdf->SetTextColor(132, 139, 144); // grey

    // Vertical alignment with auto-resize of text in cell
    // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    $pdf->MultiCell(60, 10, $pdf_co_issue_cert_biz_text, 0, 'C', 0, 0, 70, 183, true, 0, false, true, 10, 'M', true);

    $pdf->Ln(64);

    $pdf->Image($pdf_co_issue_cert_biz_logo, 82, 195, 30, 30, '', '', 'M', true, 300, 'T', false, false, 0, 'CM');
}

$pdf->Ln(-1); // Adjust spacing as needed
$pdf->SetFont('century_gothic', '', 10);
// $pdf->SetTextColor(0, 0, 0);
$pdf->MultiCell(278, 10, $membership_number, 0, 'C', 0, 0, '', '', true, 0, false, true, 10, 'M', true);

// ---------------------------------------------------------

//Close and output PDF document
if ($pdfFileName) {
    $pdf->Output($pdf_folder_name . $pdfFileName . '.pdf', $pdf_output_type);
} else {
    $pdf->Output($pdf_folder_name . $pdf_full_name . '.pdf', $pdf_output_type);
    // $pdf->Output($pdf_folder_name . 'professional_course_certificate_' . $pdfFileName . '.pdf', $pdf_output_type);
}

        //============================================================+
        // END OF FILE
        //============================================================+