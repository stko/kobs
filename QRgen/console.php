<?php
require_once('qrcode.php');
echo '--------'."\n".'QR Code Generator'."\n".'--------'."\n";
echo "\n".'Please enter the data you want to encode:'."\n";
$a = new QR(trim(fgets(STDIN)));
$text=$a->text(true);
echo $text;
echo strlen($text);
fgets(STDIN);
?>
