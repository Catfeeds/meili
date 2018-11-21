<?php
error_reporting(E_ERROR);
require_once './ThinkPHP_3.2.3/Library/Vendor/Pay/WxPay/example/phpqrcode/phpqrcode.php';
$url = urldecode($_GET["data"]);
QRcode::png($url);
//QRcode::png($url,'./aa.png',QR_ECLEVEL_L,3,4,true);

//$text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false