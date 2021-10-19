<?php
mb_internal_encoding("UTF-8");
$mode = $_GET['mode'];
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="Responsive Header Nav">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
include 'menu.php';
echo '<section><center><p>';
if ($mode == 'main'){
echo '<h1><a href="setup.php">Card Management</a></h1>';}
else {
echo '<h1><a href="cardmanagement.php?mode=main">Card Management</a></h1>';}

$emumode = file_get_contents('/sbin/piforce/emumode.txt');
$nfcmode = file_get_contents('/sbin/piforce/nfcmode.txt');


if ($_GET["command"] == 'nfcwipe') {
$copyfile = $_GET["filetocopy"];
$path_parts = pathinfo($copyfile);
$phpfile = '/var/www/html/cards/'.$mode.'/'.$path_parts['filename'].'.printdata.php';
ini_set('output_buffering', false);

echo '<b><br>Wiping Existing Card Data<br></b>';
$handle = popen('sudo python3 /sbin/piforce/card_emulator/nfcwipe.py', 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
    echo "$buffer";
    flush();
}
pclose($handle);

echo '<br><a href="cardmanagement.php?mode='.$mode.'">Return to Card Management</a>';
}

if ($_GET["command"] == 'nfcwrite') {
$copyfile = $_GET["filetocopy"];
$path_parts = pathinfo($copyfile);
$phpfile = '/var/www/html/cards/'.$mode.'/'.$path_parts['filename'].'.printdata.php';
ini_set('output_buffering', false);

echo '<b><br>Wiping Existing Card Data<br></b>';
$handle = popen('sudo python3 /sbin/piforce/card_emulator/nfcwipe.py', 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
    echo "$buffer";
    flush();
}
pclose($handle);

echo '<b><br>Writing New Card Data<br></b>';
$handle = popen('sudo python3 /sbin/piforce/card_emulator/nfcwrite.py '.$copyfile.' '.$phpfile, 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
    echo "$buffer";
    flush();
}
pclose($handle);

echo '<br><a href="cardmanagement.php?mode='.$mode.'">Return to Card Management</a>';
}

if ($_GET["command"] == 'nfc_check') {
ini_set('output_buffering', false);
$handle = popen('sudo python3 /sbin/piforce/card_emulator/nfccheck.py', 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
    echo "$buffer";
    flush();
}
pclose($handle);
$nfc_check = file_get_contents('/var/log/cardcheck/NFC_Check');
if ($nfc_check == 'none'){
echo '<br>No valid save data found!';}
else {
echo '<br><p><b>Card Contents are displayed below</b></p>';
echo '<img style="-webkit-user-select: none;" src="idcards.php?name=NFC_Check&amp;mode='.$nfc_check.'">';}
echo '<br><br><br><a href="cardmanagement.php?mode=main">Return to Card Management</a>';
}

?>