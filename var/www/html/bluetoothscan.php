<?php
header("Refresh: 1; url=bluetooth.php?mode=results");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center>';
echo '<h1>Scanning Bluetooth ...</h1>';

ini_set('output_buffering', false);

echo '<b><br>Searching for devices<br><br></b>';
$handle = popen('sudo python3 /sbin/piforce/bluetoothscan.py', 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
    echo "$buffer";
    flush();
    echo '<br>';
}
pclose($handle);

echo '<b>Scan Complete</b>';
?>