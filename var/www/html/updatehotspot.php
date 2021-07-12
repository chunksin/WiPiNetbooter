<?php

$ssid = $_POST['ssid'];
$psk = $_POST['psk'];

$command = escapeshellcmd("sudo python /sbin/piforce/hotspotwifi.py $ssid $psk");
shell_exec($command);
header ("Location: wifi.php");
?>

