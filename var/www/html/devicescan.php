<?php
$command = escapeshellcmd('sudo python /sbin/piforce/devicelist.py');
shell_exec($command);
include 'menu.php';
include 'devicelist.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openjvs.php">OpenJVS Device Scan</a></h1><br>';
echo 'Number of devices found: '.$devices.'<br><br>';
echo 'Please select a device below to configure it for OpenJVS<br>';
echo '<br><a href="devicescan.php">Rescan Devices</a><br>';
echo '<br><table class="center" id="options">';
echo '<tr><th>Device Name</th><th>Device Path</th><th>Config File</th><th>Actions</th></tr>';

for ($i = 1; $i <= $devices; $i++) {

$filename = ${'file'.$i};
$disabledfilename = $filename.'.disabled';

if ($_GET["command"] == 'rescan') {
header ("Location: devicescan.php");
}

if ($_GET["command"] == 'enable') {
$enablefile = $_GET["file"];
$without_extension = substr($_GET["file"], 0, strrpos($_GET["file"], "."));
$command = escapeshellcmd("sudo python /sbin/piforce/renamecsv.py $enablefile $without_extension");
shell_exec($command);
header ("Location: devicescan.php");
}

if ($_GET["command"] == 'disable') {
$disablefile = $_GET["file"];
$command = escapeshellcmd("sudo python /sbin/piforce/renamecsv.py $disablefile $disablefile.disabled");
shell_exec($command);
header ("Location: devicescan.php");
}

if ($_GET["command"] == 'delete') {
$deletefile = $_GET["file"];
$command = escapeshellcmd("sudo python /sbin/piforce/delete.py $deletefile");
shell_exec($command);
header ("Location: devicescan.php");
}

if (file_exists($filename)) {
    $status = 'enabled';
} else if (file_exists($disabledfilename)) {
    $status = 'disabled';
} else {
    $status = 'not found';
}

echo '<tr>';
echo '<td>'.${'name'.$i}.'</td>';
echo '<td>'.${'path'.$i}.'</td>';
if ($status == 'enabled') {
echo '<td><b>'.$status.'</b></td>';}
else {echo '<td>'.$status.'</td>';}
if ($status == 'disabled'){echo '<td><a href="devicescan.php?command=enable&file='.$disabledfilename.'">enable</a> / <a href="devicescan.php?command=delete&file='.$disabledfilename.'">delete</a></td>';}
else if ($status == 'enabled'){echo '<td><a href="devicescan.php?command=disable&file='.$filename.'">disable</a>';}
else {echo '<td><a href="deviceconfig.php?path='.${'path'.$i}.'">configure</a></td>';}
echo "</tr>";
}
echo '</table>';
echo '</p><center></body></html>';
?>