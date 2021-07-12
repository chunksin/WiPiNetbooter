<?php

header("Refresh: 3; url=fwupdate.php");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

echo '<section><center><p>';

$ip = $_GET["ip"];
$version = $_GET["version"];

if ($version == '4.01'){
$fwfile = 'FW_Netdimm_401.bin';}
if ($version == '4.02'){
$fwfile = 'FW_Netdimm_402.bin';}
if ($version == '4.03'){
$fwfile = 'FW_Netdimm_403.bin';}

echo '<p><b>'.$version.' Firmware upgrade file sent to '.$ip.'</p>';
echo '<p>Follow on-screen instructions to upgrade</b></p>';

$cmd = 'sudo python /sbin/piforce/webforcefw.py '.$fwfile.' '.$ip;
$command = escapeshellcmd($cmd);
$output = shell_exec($command);
echo $output;

echo '</p><center></body></html>';

?>