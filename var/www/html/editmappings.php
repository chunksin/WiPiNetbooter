<?php
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openjvs.php">Update Game Mappings</a></h1>';
echo '<html><body><table class="center" id="options">';
echo '<tr><th>Game Name</th><th>Control Type</th><th>Current Mapping File</th><th>New Mapping File</th></tr>';

$mappingfiles = scandir('/etc/openjvs/games');
$mappingfilename = $mappingfiles[$i];
$mappingfilepath = '/etc/openjvs/games/'.$mappingfilename;

$path = '/boot/roms/';
$files = array_values(array_diff(scandir($path), array('.', '..')));
$f = fopen("csv/romsinfo.csv", "r");
while (($row = fgetcsv($f)) !== false) {
        echo "<tr>";
        foreach ($row as $cell) {
             if (in_array($row[1], $files)){
                echo '<td>'.$row[4].'</td>';
                echo '<td>'.$row[11].'</td>';
                echo '<td>'.$row[14].'</td>';
                echo '<td><form method="POST" action="updatecsvmapping.php"><select name="mapping">';
                for ($i = 2; $i < count($mappingfiles); $i++) {
                   $mappingfilename = $mappingfiles[$i];
                   $value = $row[1].'#'.$mappingfilename;
                   echo '<option value="'.$value.'">'.$mappingfilename.'</option>';}
                echo '</select>';
                echo '<input type="submit" /></form></td>';
                break;
             }
        }
        echo "</tr>";
}
fclose($f);
echo "</table></center></body></html>";
?>