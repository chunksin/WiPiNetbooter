<?php

header("Refresh: 2; url=openjvscontrol.php");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
$mapping = $_POST['mapping'];

echo '<br><br>Starting OpenJVS with mapping<br><b>'.$mapping.'</b>';

$opencommand1 = escapeshellcmd('sudo killall -9 openjvs');
shell_exec($opencommand1 . '> /dev/null 2>/dev/null &');

$opencommand2 = escapeshellcmd('sudo openjvs '.$mapping);
shell_exec($opencommand2 . '> /dev/null 2>/dev/null &');

?>