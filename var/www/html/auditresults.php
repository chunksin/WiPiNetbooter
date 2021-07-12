<?php
include 'menu.php';
include 'auditscanresults.php';
ini_set('auto_detect_line_endings',TRUE);
$csvfile = 'csv/romsinfo.csv';
$f = fopen($csvfile, "r");
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1>Rom Audit Results</h1>';
echo '<br><b>Files Successfully Identified</b><br><br>';
echo '<table class="center" id="options">';
echo '<tr><th>Romfile Name</th><th>Game Name</th></tr>';

for ($i = 1; $i <= $successes; $i++) {

$filename = ${'filename'.$i};
$gamename = ${'gamename'.$i};

echo '<tr><td>'.$filename.'</td><td><font color="green">'.$gamename.'</td></tr>';
}
echo '</table>';

if ($duplicates > 0){

echo '<br><b>Duplicate Files Detected</b><br><br>';
echo '<table class="center" id="options">';
echo '<tr><th>Romfile Name</th></tr>';
for ($i = 1; $i <= $duplicates; $i++) {
$duplicate = ${'duplicate'.$i};
echo '<tr><td><font color="blue">'.$duplicate.'</td></tr>';
}
echo '</table>';
}

if ($failures > 0){

echo '<br><b>File Audit Failures</b><br><br>';
echo '<table class="center" id="options">';
echo '<tr><th>Romfile Name</th></tr>';
for ($i = 1; $i <= $failures; $i++) {
$failure = ${'failure'.$i};
echo '<tr><td><font color="red">'.$failure.'</td></tr>';
}
echo '</table>';
}

echo '<br><a href="saveaudit.php?rename=no">Rom Audit Save</a>';
echo '<br><br><a href="saveaudit.php?rename=yes">Rom Audit Rename</a>';

echo '</p><center></body></html>';
?>