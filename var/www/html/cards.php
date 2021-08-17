<?php
header('Content-type: image/jpeg');

$name = $_GET["name"];
$mode = $_GET["mode"];

if ($mode == "id2"){
$cardimage = imagecreatefromjpeg('img/ID2.jpg');
$path = "/boot/config/cards/id2/";
}

if ($mode == "id3"){
$cardimage = imagecreatefromjpeg('img/ID3.jpg');
$path = "/boot/config/cards/id3/";
}

if ($mode == "fzero"){
$cardimage = imagecreatefromjpeg('img/FZAX.jpg');
$path = "/boot/config/cards/fzero/";
}

if ($mode == "mkgp"){
$cardimage = imagecreatefromjpeg('img/MKGP.jpg');
$path = "/boot/config/cards/mkgp/";
}

if ($mode == "wmmt"){
$cardimage = imagecreatefromjpeg('img/WMMT.jpg');
$path = "/boot/config/cards/wmmt/";
}

$textcolour = imagecolorallocate($cardimage, 0, 0, 0);
$namefont_path = 'img/Barlow-Light.ttf';
$font_path = 'img/BarlowCondensed-Light.ttf';

if ($mode == "wmmt" || $mode == "mkgp"){
$textcolour = imagecolorallocate($cardimage, 153,50,204);
$font_path = 'img/BarlowCondensed-Light.ttf';
}

$saved ="LAST SAVE:";
$filename = $path.$name;
$lastModifiedTimestamp = filemtime($filename);
$date =date("M d Y", $lastModifiedTimestamp);
$time =date("H:i", $lastModifiedTimestamp);

if ($mode == "id2" || $mode == "id3"){
$textsize=20;
$angle=0;
$left=115;
$texttop=75;
$savedsize=18;
$savedtop=115;
$datesize=18;
$datetop=145;
$timeleft=115;
$timesize=18;
$timetop=170;
}

if ($mode == "fzero"){
$textsize=15;
$angle=0;
$left=35;
$texttop=81;
$savedsize=15;
$savedtop=118;
$datesize=15;
$datetop=138;
$timeleft=120;
$timesize=15;
$timetop=138;
}

if ($mode == "mkgp"){
$textsize=24;
$angle=0;
$left=60;
$texttop=72;
$savedsize=16;
$savedtop=97;
$datesize=16;
$datetop=117;
$timeleft=150;
$timesize=16;
$timetop=117;
}

if ($mode == "wmmt"){
$textsize=24;
$angle=0;
$left=50;
$texttop=70;
$savedsize=20;
$savedtop=102;
$datesize=20;
$datetop=137;
$timeleft=50;
$timesize=20;
$timetop=172;
}

imagettftext($cardimage, $textsize,$angle,$left,$texttop, $textcolour, $namefont_path, strtoupper($name));
imagettftext($cardimage, $datesize,$angle,$left,$datetop, $textcolour, $font_path, strtoupper($date));
imagettftext($cardimage, $savedsize,$angle,$left,$savedtop, $textcolour, $font_path, $saved);
imagettftext($cardimage, $timesize,$angle,$timeleft,$timetop, $textcolour, $font_path, $time);


imagejpeg($cardimage);
imagedestroy($cardimage);

?>