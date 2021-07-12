<?php
include 'menu.php';
include 'devicelist.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openjvs.php">OpenJVS Device File Management</a></h1>';
echo '<table class="center" id="options">';
echo '<tr><th>Device File</th><th>Status</th><th>Actions</th></tr>';

if ($_GET["command"] == 'enable') {
$enablefile = $_GET["file"];
$without_extension = substr($_GET["file"], 0, strrpos($_GET["file"], "."));
$command = escapeshellcmd("sudo python /sbin/piforce/renamecsv.py $enablefile $without_extension");
shell_exec($command);
header ("Location: devices.php");
}

if ($_GET["command"] == 'disable') {
$disablefile = $_GET["file"];
$command = escapeshellcmd("sudo python /sbin/piforce/renamecsv.py $disablefile $disablefile.disabled");
shell_exec($command);
header ("Location: devices.php");
}

if ($_GET["command"] == 'delete') {
$deletefile = $_GET["file"];
$command = escapeshellcmd("sudo python /sbin/piforce/delete.py $deletefile");
shell_exec($command);
header ("Location: devices.php");
}

$command = escapeshellcmd("sudo python /sbin/piforce/devicefiles.py");
shell_exec($command);

$devicefiles = scandir('/etc/openjvs/devices');

for ($i = 2; $i < count($devicefiles); $i++) {

$devicefilename = $devicefiles[$i];
$devicefilepath = '/etc/openjvs/devices/'.$devicefilename;

$file_parts = pathinfo($devicefilename);
if ($file_parts['extension'] == 'disabled') {
    $status = 'disabled';}
else {$status = 'enabled';}

echo '<tr>';
echo '<td>'.$devicefilename.'</td>';
if ($status == 'enabled'){
echo '<td><b>'.$status.'</b></td>';}
else {
echo '<td>'.$status.'</td>';}
if ($status == 'disabled'){echo '<td><a href="editor.php?devicefile='.$devicefilepath.'">edit</a> / <a href="devices.php?command=enable&file='.$devicefilepath.'">enable</a> / <a href="devices.php?command=delete&file='.$devicefilepath.'">delete</a></td>';}
else {echo '<td><a href="editor.php?devicefile='.$devicefilepath.'">edit</a> / <a href="devices.php?command=disable&file='.$devicefilepath.'">disable</a> / <a href="devices.php?command=delete&file='.$devicefilepath.'">delete</a></td>';}
echo "</tr>";
}
echo '</p><center></body></html>';
?>