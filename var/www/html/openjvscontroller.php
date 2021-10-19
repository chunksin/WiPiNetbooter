<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
?>

<section><center><p>
<h1><a href="openjvs.php">OpenJVS Controller Reference</a></h1>
<img src="img/openjvs-controller.png"><br><br>
<table class="center" id="options"><tr><th>Mapping Keyword</th><th>Diagram Label</th><th>Purpose</th><th>Type</th></tr>
<?php
$f = fopen("/sbin/piforce/mastermapping.csv", "r");
while (($line = fgetcsv($f)) !== false) {
        echo "<tr>";
        foreach ($line as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>\n";
}
fclose($f);
echo "</table></body></html>";
?>