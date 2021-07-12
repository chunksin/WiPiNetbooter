<?php
header("Refresh: 1; url=options.php");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo 'Updating mode to '.$_GET["mode"].' ...';
echo '</p><center></body></html>';

$command = escapeshellcmd('sudo python /sbin/piforce/switchmode.py '.$_GET["mode"]);

shell_exec($command .'> /dev/null 2>/dev/null &');

?>