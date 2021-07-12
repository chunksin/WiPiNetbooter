<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';

?>

<h1><a href=setup.php>Rom File Audit</a></h1>
<p>This feature will scan your rom files and identify them</p>
<p>The process runs as follows:</p>
<p><b>Rom Audit Scan</b> - the scan will attempt to identify your roms using the file header<br>
<b>Rom Audit Results</b> - the results from the scan are displayed for review<br>
<b>Rom Audit Save</b> - write the audit results into the game list using your file names<br>
<b>Rom Audit Rename</b> - saves to game list and updates your rom file names to a standard format<br><br>
<a href="romaudit.php">Start Audit Scan</a>