<?php

include 'menu.php';
include 'btlist.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center>';
$mode = $_GET['mode'];

if ($mode == 'main'){
echo '<h1><a href="openjvs.php">Bluetooth Devices</a></h1><br>';
echo 'The Pi can support bluetooth devices for use with OpenJVS<br><br>';
echo 'To add a new device it needs to be powered on and in discovery mode<br><br>';
echo 'The Pi will scan for new devices for 15 seconds and display any it finds in a drop down menu<br><br>';
echo 'Use the Pair Device option to initiate bluetooth pairing<br><br>';
echo 'If pairing fails the device can be removed from the scan results page<br><br>';
echo '<br><a href="bluetoothscan.php" style="font-weight:normal" class="dropbtn">Start Scan</a><br><br><br>';
$connected = shell_exec('sudo bluetoothctl devices');
$btarray = explode('Device ', $connected);
echo 'Detected Devices:<br><br><b>';
if ($connected != ''){
foreach ($btarray as &$device) {
if ($device != ''){
echo substr($device, 17);
echo '<br>';}}}
else {echo 'None';}
}

if ($mode == 'results'){
echo '<h1><a href="bluetooth.php?mode=main">Bluetooth Devices</a></h1><br>';
echo 'To pair a device select it from the drop down list and press the Pair Device button<br><br>';
$connected = shell_exec('sudo bluetoothctl devices');
$btarray = explode('Device ', $connected);
echo 'Detected devices:<br><br><b>';
if ($connected != ''){
foreach ($btarray as &$value) {
if ($value != ''){
echo substr($value, 17);
echo '<br>';}}}
else {echo 'None';}
echo '</b><br><br><div class="box2"><br>';
echo '<form action="bluetooth.php?mode=results" method="post" id="form1">';
echo '<select name="mac">';
  foreach ($btarray as &$value) {
  if ($value != ''){
  $mac = substr($value, 0, 17);
  $name = substr($value, 17);
  echo '<option value="'.$mac.'">'.$name.'</option>';}}
echo '</select><br><br>';
echo '<input type="submit" class="dropbtn" name="pair" value="Pair Device"> <input type="submit" class="dropbtn" name="remove" value="Remove Device"><br><br><a href="bluetoothscan.php" style="font-weight:normal" class="dropbtn">Rescan</a><br><br></div>';
echo '<br><br></b>';

if(isset($_POST["pair"]))
{
$mac = $_POST["mac"];
$i = 0;
ini_set('output_buffering', false);
    $handle = popen('sudo python3 /sbin/piforce/bluetoothpair.py add '.$mac, 'r');
    while(!feof($handle) && $i <=10) {
      $i++;
      $buffer = fgets($handle, 2000);
      echo "$buffer";
      echo '<br>';
      flush();
}
pclose($handle);

echo '<br><b><a href="bluetooth.php?mode=results">Pairing attempt complete</a></b>';
}
if(isset($_POST["remove"]))
{
$mac = $_POST["mac"];
$i = 0;
ini_set('output_buffering', false);
    $handle = popen('sudo python3 /sbin/piforce/bluetoothpair.py remove '.$mac, 'r');
    while(!feof($handle) && $i <=10) {
      $i++;
      $buffer = fgets($handle, 2000);
      echo "$buffer";
      echo '<br>';
      flush();
}
pclose($handle);

echo '<b><a href="bluetooth.php?mode=results">Removal attempt complete</a></b>';
}
}

echo '</p><center></body></html>';