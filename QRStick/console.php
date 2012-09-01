<?php
require_once('../QRgen/qrcode.php');

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
			echo "jump to {$this->x},{$this->y}\n";
		}else{
			echo "stitch to {$this->x},{$this->y}\n";

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
		echo "create stitches\n";
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
			echo $this->text_1_Value;
		}else{
			echo $this->text_0_Value;
		}
	}

    	public function createPatch(&$patchField) {
		if ($this->value){
			echo "Create patch\n";
			$patchField[]=new Patch($this->x,$this->y,1,1);
		}
	}
	

}


echo '--------'."\n".'QR Code Generator'."\n".'--------'."\n";
$a = new QR(trim($argv[1]));
print_r ($a);
$text=$a->text(false);
echo $text;
echo "\n";
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
			echo "**";
			$bitField[$x][$y]=new cell($bitField,$x,$y,true);		
		}else{
			echo "  ";		
			$bitField[$x][$y]=new cell($bitField,$x,$y,false);		
		}
	}
	echo "\n";
}
//---- Just dump the bitField 
for ( $y = 0 ; $y < $size ; $y ++ ){
	for ( $x = 0 ; $x < $size ; $x ++ ){
		$bitField[$x][$y]->displayVar();
	}
	echo "\n";
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
