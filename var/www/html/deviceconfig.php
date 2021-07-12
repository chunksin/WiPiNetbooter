<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<table class="center" id="options">';
echo '<tr><th>Control</th><th>Input</th></tr>';

ini_set('output_buffering', false);
    $handle = popen('sudo python3 /sbin/piforce/configuration.py '.$_GET["path"], 'r');
    while(!feof($handle)) {
      $buffer = fgets($handle);
      echo "$buffer";
      flush();
}
pclose($handle);
echo '</table>';

echo '<br><a href="devicescan.php">Return to Device Scan</a>'

?>