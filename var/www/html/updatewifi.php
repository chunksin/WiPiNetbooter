<?php

$ssid = $_POST['ssid'];
$psk = $_POST['psk'];

$command = escapeshellcmd("sudo python /sbin/piforce/homewifi.py $ssid $psk");
shell_exec($command . '> /dev/null 2>/dev/null &');
header ("Location: wifi.php");
?>