<?php
require_once('../QRgen/qrcode.php');

define("STITCHSIZE_X",5.0)
define("STITCHSIZE_Y",5.0)
define("MAXJUMP_X",12.5)
define("MAXJUMP_Y",12.5)

$lastPosX=0.0;
$lastPosY=0.0;


function verbose($text){
  fwrite(STDERR,$text);
}

class Stitch
{
	private $x=0;
	private $y=0;
	private $jump=true;
	function __construct( $jump, $x, $y ){
		$this->x=$x;
		$this->y=$y;
		$this->jump=$jump;
	}
	public function dump() {
		if ($this->jump==true){
			verbose ("jump to {$this->x},{$this->x}\n");

		}else{
			verbose( "stitch to {$this->x},{$this->x}\n");

		}
	}

	public function stitch() {
		$newPosX=$this->x*STITCHSIZE_X;
		$newPosY=$this->y+STITCHSIZE_Y;
		while (abs($newPosX -$lastPosX ) > MAXJUMP_X or abs($newPosY -$lastPosY ) > MAXJUMP_X ){
		}
		// jump in steps to new position..
		if ($this->jump==true){
			verbose ("jump to {$this->x},{$this->x}\n");
			fprintf("%02X 

		}else{
			verbose( "stitch to {$this->x},{$this->x}\n");

		}
	}

}

class Patch
{
	private $x=0;
	private $y=0;
	private $width=0;
	private $height=0;
	function __construct(  $x, $y, $width, $height){
		$this->x=$x;
		$this->y=$y;
		$this->width=$width;
		$this->height=$height;
	}
	public function stitch(&$stitchList) {
		verbose( "create stitches\n");
		$stitchList[]=new Stitch(true, $this->x,$this->y);
		$stitchList[]=new Stitch(false, $this->x+$this->width,$this->y+$this->height);
	}

}

class Cell
{
    
	private $dataArray=null;
	private $x=0;
	private $y=0;
	private $value=false;	
	private $text_0_Value='  ';
	private $text_1_Value='██';

	function __construct( &$dataArray, $x, $y, $value ){
		$this->dataArray=$dataArray;
		$this->x=$x;
		$this->y=$y;
		$this->value=$value;	
	}

	public function displayVar() {
		if ($this->value){
			verbose( $this->text_1_Value);
		}else{
			verbose( $this->text_0_Value);
		}
	}

    	public function createPatch(&$patchField) {
		verbose( "Create patch\n");
		$patchField[]=new Patch($this->x,$this->y,1,1);
	}
	

}


verbose( '--------'."\n".'QR Code Generator'."\n".'--------'."\n");
$a = new QR(trim($argv[1]));
print_r ($a);
$text=$a->text(false);
verbose( $text);
verbose( "\n");
$size=$a->dim;
$bitField=array();
$patchField=array();
$stitchList=array();
//---------
//to make later calculations easier, surround the bitfield with a fence of empty ("0") cells and prepare the 2-dimensional array structur
for ($i=0;$i<$size+2;$i++){
	$bitField[$i]=array();
	$bitField[0][$i]=new cell($bitField,-1,-1,false);
	$bitField[$i][$size+1]=new cell($bitField,-1,-1,false);
}
//the last line needs to be completeted seperately
for ($i=0;$i<$size+1;$i++){
	$bitField[$size+1][$i]=new cell($bitField,-1,-1,false);
}
//----------
for ( $y = 0 ; $y < $size ; $y ++ ){
	for ( $x = 0 ; $x < $size ; $x ++ ){
		if (substr($a->img[$y],$x,1)=="1"){
			verbose( "**");
			$bitField[$x][$y]=new cell($bitField,$x,$y,true);		
		}else{
			verbose( "  ");		
			$bitField[$x][$y]=new cell($bitField,$x,$y,false);		
		}
	}
	verbose( "\n");
}
//---- Just dump the bitField 
for ( $y = 0 ; $y < $size ; $y ++ ){
	for ( $x = 0 ; $x < $size ; $x ++ ){
		$bitField[$x][$y]->displayVar();
	}
	verbose( "\n");
}

//---- Let the bitfield create the patchfield
for ( $y = 0 ; $y < $size ; $y ++ ){
	for ( $x = 0 ; $x < $size ; $x ++ ){
		$bitField[$x][$y]->createPatch($patchField);
	}
}
// make the stitchlist out of the patchfield
foreach ($patchField as $patch){
	$patch->stitch($stitchList);
}
// dump the $stitchlist
foreach ($stitchList as $stitch){
	$stitch->dump();
}

?>
