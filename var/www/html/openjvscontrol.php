<?php
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openjvs.php">OpenJVS Control</a></h1>';
echo '<html><body>';

$mappingfiles = scandir('/etc/openjvs/games');
$mappingfilename = $mappingfiles[$i];
$mappingfilepath = '/etc/openjvs/games/'.$mappingfilename;

echo '<br>To run OpenJVS in stand alone mode select a mapping file and press the Launch button<br><br>';

echo '<form method="POST" action="launchopenjvs.php"><select name="mapping">';
for ($i = 2; $i < count($mappingfiles); $i++) {
    $mappingfilename = $mappingfiles[$i];
    $value = $mappingfilename;
    echo '<option value="'.$value.'">'.$mappingfilename.'</option>';}
echo '</select>';
echo '  <input type="submit" class="dropbtn" value="Launch" /></form>';

?>