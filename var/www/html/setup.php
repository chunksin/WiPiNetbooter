<?php

include 'menu.php';
$openjvsmode = file_get_contents('/sbin/piforce/openmode.txt');
$openffbmode = file_get_contents('/sbin/piforce/ffbmode.txt');
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

echo '<section><center>';
echo '<h1><a href="gamelist.php?display=all">Setup Menu</a></h1><br>';
echo '<div class="box2">';
echo '<a href="editgamelist.php">Edit Game List</a></div><br>';
echo '<div class="box2">';
echo '<a href="dimms.php">Manage Netdimms</a></div><br>';
echo '<div class="box2">';
echo '<a href="fwupdate.php">Update Netdimm Firmware</a></div><br>';
echo '<div class="box2">';
echo '<a href="cardemulator.php?mode=main">Card Reader Emulator</a></div><br>';
echo '<div class="box2">';
echo '<a href="importcsv.php">Import CSV from Boot Drive</a></div><br>';
echo '<div class="box2">';
echo '<a href="dumpcsv.php">View CSV Raw Data</a></div><br>';
if ($openjvsmode == 'openon'){
echo '<div class="box2">';
echo '<a href="openjvs.php">OpenJVS Configuration</a></div><br>';}
if ($openffbmode == 'ffbon'){
echo '<div class="box2">';
echo '<a href="openffb.php">OpenFFB Configuration</a></div><br>';}
echo '<div class="box2">';
echo '<a href="network.php">Network Configuration</a></div><br>';
echo '<div class="box2">';
echo '<a href="reboot.php">Reboot Raspberry Pi</a><br></div><br>';
echo '</p><center></body></html>';
?>