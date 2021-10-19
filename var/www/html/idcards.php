<?php
header('Content-type: image/jpeg');

$name = $_GET["name"];
$mode = $_GET["mode"];

$overlay = imagecreatetruecolor(370, 270);
imagesavealpha($overlay, true);
$colour = imagecolorallocatealpha($overlay, 0, 0, 0, 127);
imagefill($overlay, 0, 0, $colour);

$out = imagecreatetruecolor(644, 1020);
imagesavealpha($out, true);
$colour = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefill($out, 0, 0, $colour);

if ($mode == "idas"){
$includefile = "cards/idas/".$name.".printdata.php";
$path = "/boot/config/cards/idas/";
$cardpath = "cardimages/idas/";
include $includefile;
$cardimage = imagecreatefrompng($cardpath.$card);
$font_path = 'img/RobotoCondensed-Light-ID.ttf';
$iconarray = array("","","","","","","","","");
$keyno = "".$keyno;
$areaarray = str_split($areas);
$i=0;
$areaprintarray = array();
foreach($areaarray as $area){
if ($area == '1'){
   array_push($areaprintarray,$iconarray[$i]);
   $i++;}
else {
   $areaprintarray[$i] = "";
   $i++;}
}
}

if ($mode == "id2"){
$includefile = "cards/id2/".$name.".printdata.php";
$path = "/boot/config/cards/id2/";
$cardpath = "cardimages/id2/";
include $includefile;
$cardimage = imagecreatefrompng($cardpath.$card);
$font_path = 'img/RobotoCondensed-Light-ID2.ttf';
$iconarray = array("","","","","","","","");
$keyno = "".$keyno;
$areaarray = str_split($areas);
$i=0;
$areaprintarray = array();
foreach($areaarray as $area){
if ($area == '1'){
   array_push($areaprintarray,$iconarray[$i]);
   $i++;}
else {
   $areaprintarray[$i] = "";
   $i++;}
}
$racerarray = array();
if ($racerlevel > 0){
$singlestars = $racerlevel % 2;
$doublestars = intdiv($racerlevel,2);
for ($x = 1; $x <= $doublestars; $x++) {
       array_push($racerarray,"");
       if ($x == 5){
           array_push($racerarray,"");}
       if ($x == 10){
           array_push($racerarray,"");}
       if ($x == 15){
           array_push($racerarray,"");}
}
for ($x = 1; $x <= $singlestars; $x++) {
       array_push($racerarray,"");
}
}
}

if ($mode == "id3"){
$includefile = "cards/id3/".$name.".printdata.php";
$path = "/boot/config/cards/id3/";
$cardpath = "cardimages/id3/";
include $includefile;
$cardimage = imagecreatefrompng($cardpath.$card);
$font_path = 'img/RobotoCondensed-Light-ID3.ttf';
$iconarray = array("","","","","","","","","");
$keyno = "".$keyno;
$areaarray = str_split($areas);
$i=0;
$areaprintarray = array();
foreach($areaarray as $area){
if ($area == '1'){
   array_push($areaprintarray,$iconarray[$i]);
   $i++;}
else {
   $areaprintarray[$i] = "";
   $i++;}
}
$racerarray = array();
if ($racerlevel > 0){
$singlestars = $racerlevel % 2;
$doublestars = intdiv($racerlevel,2);
for ($x = 1; $x <= $doublestars; $x++) {
       array_push($racerarray,"");
       if ($x == 5){
           array_push($racerarray,"");}
       if ($x == 10){
           array_push($racerarray,"");}
       if ($x == 15){
           array_push($racerarray,"");}
}
for ($x = 1; $x <= $singlestars; $x++) {
       array_push($racerarray,"");
}
}
}

$namesize=56;
$left=4;
$nametop=75;
$carkeysize=28;
$carline1top=130;
$carline2top=166;
$keyleft=250;
$keytop=75;
$areatop=210;
$areasize=28;
$racersize=28;
$racertop=256;

if ($mode == "idas"){
$nametop=70;
$carline1top=125;
$carline2top=161;
$keytop=70;
$areatop=205;
}

imagettftext($overlay, $namesize,$angle,$left,$nametop, $textcolour, $font_path, $drivername);
imagettftext($overlay, $carkeysize,$angle,$keyleft,$keytop, $textcolour, $font_path, $keyno);
imagettftext($overlay, $carkeysize,$angle,$left,$carline1top, $textcolour, $font_path, $carline1);
imagettftext($overlay, $carkeysize,$angle,$left,$carline2top, $textcolour, $font_path, $carline2);
imagettftext($overlay, $areasize,$angle,$left,$areatop, $textcolour, $font_path, implode($areaprintarray));
imagettftext($overlay, $racersize,$angle,$left,$racertop, $textcolour, $font_path, implode($racerarray));

imagecopyresampled($out, $cardimage, 0, 0, 0, 0, 644, 1020, 644, 1020);
imagecopyresampled($out, $overlay, 224, 84, 0, 0, 370, 270, 370, 270);
imagepng($out);
imagedestroy($out);

?>