<?php 
    // Handle folder for saving to local server
    if (in_array($pdf_output_type, array('F','FI','FD'))) {
        $sub_folder = date("Ym");
        $pdf_folder_name = "uploads/idcards/".$sub_folder."/";

        // Check if the destination folder exists
        if (!is_dir($pdf_folder_name)) { // If it doesn't exist, try to create it
            if (!mkdir($pdf_folder_name, 0755, TRUE)) { 
                // If the folder(s) can't be created
                $_SESSION['userfeedback'] = "We are unable to present your certificate at the moment. Please try again later.";
                $_SESSION['feedbacktype'] = "error";
        
                redirect(link_back(root_dir_portal()));
            }
        }

        // If you are getting TCPDF ERROR: Unable to create output file: myfile.pdf
        // See solution at https://stackoverflow.com/questions/12304553/tcpdf-save-file-to-folder/32628681#32628681
    }
    else $pdf_folder_name = '';

// -------------------
// HOW-TO add new font
// -------------------
//
// Open a Terminal window and switch to the TCPDF Tools folder e.g cd /Applications/MAMP/htdocs/site_folder_name/TCPDF-master/tools/
// Run a command like the following to locate the font file and convert it
// php tcpdf_addfont.php -i "/Applications/MAMP/htdocs/site_folder_name/TCPDF-master/examples/century_gothic_bold.ttf"
// 
// The command will automatically create the required files inside the tcpdf/fonts folder of your project and displays the name of the font that can be used in your code.
// 
// You should see something like the following 
// 
// >>> Converting fonts for TCPDF:
// *** Output dir set to /Applications/MAMP/htdocs/site_folder_name/TCPDF-master/fonts/
// +++ OK   : /Applications/MAMP/htdocs/site_folder_name/TCPDF-master/examples/century_gothic_bold.ttf added as century_gothic_b
// >>> Process successfully completed!


// TO-DO: If there is an existing file and it's not being overridden, 
// redirect to that file.


// TO-DO: If the existing file is being overridden, 
// delete the previous file from server and database records.
    
        
    // Include the main TCPDF library (search for installation path).
    require_once('TCPDF-master/tcpdf.php');


    // Extend the TCPDF class to create custom Header and Footer
    class MYPDF extends TCPDF {
        //Page header
        public function Header() {
            // get the current page break margin
            $bMargin = $this->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $this->AutoPageBreak;
            // disable auto-page-break
            $this->SetAutoPageBreak(false, 0);
            // set bacground image
            // $img_file = K_PATH_IMAGES.'student-identification-card-model-2.jpg';
            $img_file = DOC_ROOT . FILE_PATH_TO_UK . 'images/pdf-templates/student-identification-card-model-1.jpg';
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
    $pdf->SetTitle($pdf_full_name.' student identification card');
    $pdf->SetSubject($pdf_full_name.' student identification card');
    $pdf->SetKeywords('SEMP, Certificate, Executive, Masters');

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

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
    if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
        require_once(dirname(__FILE__).'/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

        //REMOVE CELL PADDING
        $pdf->SetCellPadding(0);

        // REMOVE TAG TOP AND BOTTOM MARGINS
        $tagvs = array('p' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)));
        $pdf->setHtmlVSpace($tagvs);

    // ---------------------------------------------------------

    // set font
    $pdf->SetFont('century_gothic_b', 'B', 30);
    //$pdf->SetTextColor(47, 52, 144); // Blue

    // add a page
    $pdf->AddPage();

    // $pdf->Ln(54);

    // Vertical alignment with auto-resize of text in cell
    // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
    // $pdf->MultiCell(170, 20, trim($pdf_full_name), 0, 'C', 0, 0, '', '', true, 0, false, true, 20, 'M', true); //clean_name($pdf_full_name)

    $pdf->Ln(90);

    // set font
    $pdf->SetFont('helvetica', 'B', 20); // helvetica

    // Vertical alignment with auto-resize of text in cell
    // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

    /*if(isset($_GET['htmlCell'])){ // render the image with html tags but in a multicell
        $pdf->writeHTMLCell(40, 20, 85, 114, '<img src="'.$pdf_id_image.'" style="height:400px" />', 1, 0, false, true, 'T', false);
    }

    if(isset($_GET['justHtml'])){ // render the image with just html tags
        $pdf->writeHTML('<div style="text-align:center"><img src="'.$pdf_id_image.'" style="height:145px; width:130px; margin-left:300px" /></div>', false, false, true, false);
    }

    if(isset($_GET['transform_image'])){ // Transform the image by cropping it and fitting it to size
        $pdf->StartTransform();
        
        $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => array(255, 0, 0)));
        $pdf->Rect(89, 114, 40, 50, 'CNZ'); //Clipping mask (CNZ style makes your day)
        $pdf->Image($pdf_id_image, 89, 115, 0, 50, '', '', 'M', true, 300, 'C');

        //this would actually cut off a 50 units a in each direction.
        $pdf->StopTransform();
    }

    if(isset($_GET['only_image'])){ // render just the image using the image() function
        $pdf->Image($pdf_id_image, 89, 114, 80, 50, '', '', '', false, 300, '', false, false, 1, 'LM', false, false);
    }*/

    // Image and user details
    $pdf->StartTransform();
    $pdf->SetLineStyle(['width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 4, 'color' => [255, 0, 0]]);
    $pdf->Rect(86, 93, 40, 51, 'CNZ'); // Clipping mask
    $pdf->Image($pdf_id_image, 86, 104, 0, 51, '', '', 'M', true, 300, 'C', false, false, 0, 'CM');
    $pdf->StopTransform();

    // User name
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetTextColor(0, 0, 0); // Black
    $pdf->MultiCell(75, 6, trim($pdf_full_name), 0, 'C', 0, 0, '68', '144', true, 0, false, true, 12, 'M', true);

    // Date range
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(75, 10, ucwords($pdf_start_date . ' - ' . $pdf_end_date), 0, 'C', 0, 0, '68', '154', true, 0, false, true, 10, 'M', true);

    // Programme name
    $pdf->Ln(9);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->MultiCell(75, 6, ucwords($pdf_programme_name), 0, 'C', 0, 0, '68', '164', true, 0, false, true, 12, 'M', true);

    // Student number
    $pdf->Ln(9);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(75, 10, ucwords($pdf_student_number), 0, 'C', 0, 0, '68', '174', true, 0, false, true, 10, 'M', true);

    // Reset pointer to the last page
    $pdf->lastPage();

    // ---------------------------------------------------------
    //Close and output PDF document
    if ($pdfFileName) {
        $pdf->Output($pdf_folder_name.$pdfFileName.'.pdf', $pdf_output_type);
    }
    else {
        $pdf->Output($pdf_folder_name.$pdf_full_name.'_id-card_'.$pdf_student_number.'.pdf', $pdf_output_type);
    }

    //============================================================+
    // END OF FILE
    //============================================================+