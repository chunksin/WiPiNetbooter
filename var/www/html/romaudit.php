<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1>Rom Audit Scan</h1>';

ini_set('output_buffering', false);
    $handle = popen('sudo python3 /sbin/piforce/auditnames.py '.$_GET["path"], 'r');
    while(!feof($handle)) {
      $buffer = fgets($handle);
      echo "$buffer";
      flush();
}
pclose($handle);

echo '<br><a href="auditresults.php">Go to detailed results</a>'

?>