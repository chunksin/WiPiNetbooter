<?php
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center>';
echo '<h1><a href="setup.php">Edit Game List</a></h1>';
echo '<html><body><table class="center" id="options">';
echo '<tr><th>Game Name</th><th>Orientation</th><th>Controls</th><th>Enabled</th></tr>';
$path = '/boot/roms/';
$files = array_values(array_diff(scandir($path), array('.', '..')));
$f = fopen("csv/romsinfo.csv", "r");
while (($row = fgetcsv($f)) !== false) {
        echo "<tr>";
        foreach ($row as $cell) {
             if (in_array($row[1], $files)){
                if ($row[12] == 'Yes') {$toggle = 'No';} else {$toggle = 'Yes';}
                echo '<td>'.$row[4].'</td>';
                echo '<td>'.$row[10].'</td>';
                echo '<td>'.$row[11].'</td>';
                echo '<td><a href="updatecsvenable.php?rom='.$row[1].'&enabled='.$toggle.'">'.$row[12].'</a></td>';
                break;
             }
        }
        echo "</tr>";
}
fclose($f);
echo "</table></center></body></html>";
?>