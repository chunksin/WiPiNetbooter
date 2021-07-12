<?php
header("Refresh: 4; url=wifi.php");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center>';
echo '<h1>Scanning WiFi ...</h1>';
$command = escapeshellcmd('sudo python /sbin/piforce/wifiscan.py &');
shell_exec($command . '> /dev/null 2>/dev/null &');
?>