<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

$file = '/boot/config.txt';
$searchfor = 'disable-bt';

$contents = file_get_contents($file);
$pattern = preg_quote($searchfor, '/');
$pattern = "/^.*$pattern.*\$/m";
if(preg_match_all($pattern, $contents, $matches)){

    $bluetooth = 'disabled';
}
else {
   $bluetooth = 'enabled';
}

?>

<section><center><p>
<h1><a href="setup.php">OpenJVS Configuration Menu</a></h1><br>
<div class="box2">
<a href="devicescan.php">Scan and Configure Devices</a><br></div><br>
<div class="box2">
<a href="devices.php">Manage Device Files</a><br></div><br>
<div class="box2">
<a href="mapping.php">Manage Mapping Files</a><br></div><br>
<div class="box2">
<a href="editmappings.php">Update Game Mappings</a><br></div><br>

<?php
if ($bluetooth == 'enabled'){

echo '<div class="box2">';
echo '<a href="bluetooth.php?mode=main">Bluetooth Devices</a></div><br>';
}
?>
<div class="box2">
<a href="openjvscontrol.php">OpenJVS Control</a><br></div><br>
<div class="box2">
<a href="updateopenjvs.php">Update OpenJVS</a><br></div><br>
<div class="box2">
<a href="openjvscontroller.php">Controller Reference</a></div>
</p><center></body></html>


