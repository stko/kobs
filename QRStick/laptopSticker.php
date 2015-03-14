<?php
require_once('../QRgen/qrcode.php');




function verbose($text){
  fwrite(STDERR,$text);
}
function output($text){
  fwrite(STDOUT,$text);
}



class Cell
{
    
	private $dataArray=null;
	private $x=0;
	private $y=0;
	private $value=false;	
	private $display_0_Value='  ';
	private $display_1_Value='██';

	function __construct( &$dataArray, $x, $y, $value ){
		$this->dataArray=$dataArray;
		$this->x=$x;
		$this->y=$y;
		$this->value=$value;	
	}

	public function displayVar() {
		if (!$this->value){
			verbose( $this->display_1_Value);
		}else{
			verbose( $this->display_0_Value);
		}
	}	
	public function outputVar() {
		if (!$this->value){
			output( $this->y." ".$this->x." 1 1 F\n");
		}else{
			output( '');
		}
	}	
}


verbose( '--------'."\n".'QR vCard Generator'."\n".'--------'."\n");
$a = new QR("BEGIN:VCARD
VERSION:3.0
N:Köhler;Steffen
FN:Steffen Köhler
ORG:Yazaki System Technologies;Engineering
URL:http://koehlers.de/
EMAIL:skoehle6@ford.com
TITLE:techn. Project Leader
TEL;TYPE=work:+49 221 90 38925
TEL;TYPE=cell:+49 1724103598
END:VCARD
");
//print_r ($a);
$text=$a->text(false);
verbose( $text);
verbose( "\n");
$size=$a->dim;
$bitField=array();

//---------
//to make later calculations easier, surround the bitfield with a fence of empty ("0") cells and prepare the 2-dimensional array structur
for ($i=0;$i<$size+2;$i++){
	$bitField[$i]=array();
	$bitField[0][$i]=new cell($bitField,0,$i,false);
	$bitField[$i][0]=new cell($bitField,$i,0,false);
	$bitField[$i][$size+1]=new cell($bitField,$i,$size+1,false);
}
//the last line needs to be completeted seperately
for ($i=0;$i<$size+1;$i++){
	$bitField[$size+1][$i]=new cell($bitField,$size+1,$i,false);
}
//----------
for ( $y = 0 ; $y < $size ; $y ++ ){
	for ( $x = 0 ; $x < $size ; $x ++ ){
		if (substr($a->img[$y],$x,1)=="1"){
			verbose( "**");
			$bitField[$x+1][$y+1]=new cell($bitField,$x+1,$y+1,true);		
		}else{
			verbose( "  ");		
			$bitField[$x+1][$y+1]=new cell($bitField,$x+1,$y+1,false);		
		}
	}
	verbose( "\n");
}
//---- Just dump the bitField 
for ( $y = 0 ; $y < $size +2 ; $y ++ ){
	for ( $x = 0 ; $x < $size +2 ; $x ++ ){
		$bitField[$x][$y]->displayVar();
	}
	verbose( "<\n");
}

//---- create the eps file
output('5770
%!PS-Adobe EPSF-3.0
%%Creator: Zend_Matrixcode_Qrcode
%%Title: QRcode
%%CreationDate: 2014-08-04
%%DocumentData: Clean7Bit
%%LanguageLevel: 2
%%Pages: 1
%%BoundingBox: 0 0 '.strval(($size+4)*10).' '.strval(($size+4)*10).'
10 10 scale
1 1 translate
/F { rectfill } def
0 0 0 setrgbcolor
'); // header
for ( $y = 0 ; $y < $size +2 ; $y ++ ){
	for ( $x = 0 ; $x < $size +2 ; $x ++ ){
		$bitField[$x][$y]->outputVar();
	}
}
output('%%EOF
0
'); // footer



?>
