<?php
header('Content-type: image/jpeg');

$name = $_GET["name"];

$overlay = imagecreatetruecolor(640, 1010);
imagesavealpha($overlay, true);
$colour = imagecolorallocatealpha($overlay, 0, 0, 0, 127);
imagefill($overlay, 0, 0, $colour);

$out = imagecreatetruecolor(640, 1010);
imagesavealpha($out, true);
$colour = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefill($out, 0, 0, $colour);

$sscup = 0;
$mcup = 0;
$includefile = "cards/fzero/".$name.".printdata.php";
$path = "/boot/config/cards/fzero/";
$cardpath = "cardimages/fzero/";
include $includefile;
$cardimage = imagecreatefrompng($cardpath.$card);
$iconfont_path = 'img/RobotoCondensed-Light-FZ.ttf';
$driverfont_path = 'img/Roboto-Light.ttf';
$font_path = 'img/RobotoCondensed-Light.ttf';
$license=str_replace(" ", "  ", $license);
if (strpos($rank2, 'G') !== false) {
$rank2 = str_replace('G', '', $rank2);
$sscup = 1;
}
if (strpos($rank2, '7') !== false) {
$rank2 = str_replace('7', '', $rank2);
$mcup = 1;
}
$rank4=implode('#',str_split($rank4));
$rank5=implode('#',str_split($rank5));
$rank=$rank1."\n".$rank2."\n".$rank3."\n".$rank4."\n".$rank5;

if (substr($mute,0,1) != '4'){
$mute='!'.$mute;}
if (substr($aero,0,1) != '4'){
$aero='!'.$aero;}
if (substr($outer,0,1) != '4'){
$outer='!'.$outer;}
if (substr($port,0,1) != '4'){
$port='!'.$port;}
if (substr($light,0,1) != '4'){
$light='!'.$light;}
if (substr($green,0,1) != '4'){
$green='!'.$green;}

$mute=implode('  ',str_split($mute));
$aero=implode('  ',str_split($aero));
$outer=implode('  ',str_split($outer));
$port=implode('  ',str_split($port));
$light=implode('  ',str_split($light));
$green=implode('  ',str_split($green));

$citylines=$mute."\n".$aero."\n".$outer."\n".$port."\n".$light."\n".$green;
$name=implode(' ',str_split($driver));

$licensetop=125;
$namesize=36;
$ranksize=28;
$licensesize=29;
$licenseleft=128;
$left=90;
$citysize=30;
$cityleft=321;
$nametop=211;
$ranktop=290;
$citytop=490;
$cuptop=324;
$cupsize=46;
$sscupleft=428;
$mcupleft=487;

imagettftext($overlay, $licensesize,$angle,$licenseleft,$licensetop, $textcolour, $font_path, $license);
imagettftext($overlay, $namesize,$angle,$left,$nametop, $textcolour, $driverfont_path, $name);
imagettftext($overlay, $ranksize,$angle,$left,$ranktop, $textcolour, $iconfont_path, $rank);
if ($sscup = 1) {imagettftext($overlay, $cupsize,$angle,$sscupleft,$cuptop, $textcolour, $iconfont_path, "G");}
if ($mcup = 1) {imagettftext($overlay, $cupsize,$angle,$mcupleft,$cuptop, $textcolour, $iconfont_path, "7");}
imagettftext($overlay, $citysize,$angle,$cityleft,$citytop, $textcolour, $iconfont_path, $citylines);

imagecopyresampled($out, $cardimage, 0, 0, 0, 0, 640, 1010, 640, 1010);
imagecopyresampled($out, $overlay, 0, 0, 0, 0, 640, 1010, 640, 1010);
imagepng($out);
imagedestroy($out);

?>