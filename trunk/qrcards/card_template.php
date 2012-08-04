<?php


/**
 * Creates an PDF  document using TCPDF
 * @author Steffen Koehler
 * @since 2008-03-04
 */

require_once('tcpdf/config/lang/ger.php');
require_once('tcpdf/tcpdf.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $image_file = dirname(__FILE__).'/images/Klobslogo.jpg';
        $this->Image($image_file, 10, 7, 30, '', 'JPG', 'http://kobs.googlecode.com', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, 'Shojikido Mitglieds- Karte', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}



function printCard($cardContent){

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Klobs (kobs.googlecode.com');
	$pdf->SetTitle('Klobs Mitgliederkarte');
	$pdf->SetSubject('Klobs Mitgliedskarte');
	$pdf->SetKeywords('Klobs Mitgliedskarte');

	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	$pdf->setLanguageArray($l);

	// ---------------------------------------------------------

	// set font
	$pdf->SetFont('times', 'BI', 12);

	// add a page
	$pdf->AddPage();

	// set some text to print
$txt = <<<EOD
Die Shojikido Mitglieder- Karte zum Selberdrucken:

Einfach auf festes Papier Drucken, Unterschreiben, Ausschneiden, in der Mitte falten - und Fertig!
EOD;

	// print a block of text using Write()
	$pdf->Write($h=0, $txt, $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);

	$x_baseline=15;
	$y_baseline=60;
	$y_offset=0;
	$x_size=86;
	$y_size=54;
	$y_gap=10;
	// a few values to center the images
	$team_size=53;
	$logo_size=26;
	$sjkd_size=42;
	for ($prints=0;$prints<3;$prints++){
		$y_offset = $prints * ( $y_size + $y_gap );
		$pdf->Rect($x_baseline, $y_baseline+$y_offset, $x_size, $y_size, 'D', '', '');
		$pdf->Rect($x_baseline+$x_size, $y_baseline+$y_offset, $x_size, $y_size, 'D', '', '');
		$pdf->Image(dirname(__FILE__).'/images/shojikido-team.jpg', $x_baseline+($x_size-$team_size)/2, $y_baseline+$y_offset+3, $team_size, '', 'JPG', 'http://mitglieder.shojikido.de', 'T', false, 300, '', false, false, 0, false, false, false);
		$pdf->Image(dirname(__FILE__).'/images/DKV-Logo.jpg', $x_baseline+39.5, $y_baseline+$y_offset+($y_size-$logo_size)/2, $logo_size, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$pdf->Image('@'.$cardContent[5], $x_baseline+10, $y_baseline+$y_offset+($y_size-$logo_size)/2, '', $logo_size, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		//placing the picture
		$pdf->Image(dirname(__FILE__).'/images/sjkd-schriftzug.jpg', $x_baseline+74, $y_baseline+$y_offset+($y_size-$sjkd_size)/2, '', $sjkd_size, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		$pdf->Rect($x_baseline+4, $y_baseline+$y_offset+46, 44, 6, 'DF', '', array(220, 220, 200));


		// page 2
		$qrcode_size=40;
		$pdf->write2DBarcode($cardContent[2], 'QRCODE,H', $x_baseline+$x_size+($x_size-$qrcode_size)/2, $y_baseline+$y_offset+($y_size-$qrcode_size)/2, $qrcode_size, $qrcode_size, '', 'N');


		//placing the texts, sorted by fonts
		$pdf->SetFont('helvetica', 'B', 12);
		$pdf->Text($x_baseline+4, $y_baseline+$y_offset+8, $cardContent[0]);

		$pdf->SetFont('times', 'I', 8);
		$pdf->Text($x_baseline+48, $y_baseline+$y_offset+47, 'www.shojikido.de');

		$pdf->SetFont('times', '', 8);
		$pdf->Text($x_baseline+4, $y_baseline+$y_offset+42, $cardContent[1]);
		$pdf->Text($x_baseline+$x_size+($x_size-$qrcode_size)/2, $y_baseline+$y_offset+($y_size-$qrcode_size)/2-3, $cardContent[3]);
		$pdf->Text($x_baseline+$x_size+($x_size-$qrcode_size)/2, $y_baseline+$y_offset+($y_size-$qrcode_size)/2+$qrcode_size+1, $cardContent[4]);
	}
	// ---------------------------------------------------------





	//Close and output PDF document
	$pdf->Output('KLOBS-Mitgliedskarte.pdf', 'I');

}



/*
	$printText=array();
	$printText[0]= "KLaus Mustermann"; // Name
	$printText[1]= "01.01.2222 SVB_lalala"; // Description Text
	$printText[3]="4711"; // QRCode Description Text
	$printText[2]= "KLOBS4711"; // QRCode content
	$printText[4]= "Klas Mustermann (heute)"; // QRCode add. Text
	printCard($printText);

*/
//============================================================+
// END OF FILE                                                
//============================================================+
