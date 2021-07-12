<?php
echo '<html><body><table border = "1">';
$f = fopen("csv/romsinfo.csv", "r");
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