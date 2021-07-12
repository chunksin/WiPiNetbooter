<?php
header("Refresh: 1; url=dimms.php");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo 'Deleting '.$_GET["name"].' ...';
echo '</p><center></body></html>';

$filename = "csv/dimms.csv";
$linenum = $_GET["linenum"];

delLineFromFile($filename, $linenum);

function delLineFromFile($filename, $linenum){
  
$arr = file($filename);
$lineToDelete = $linenum;
unset($arr["$lineToDelete"]);

if (!$fp = fopen($filename, 'w+')){
        print "Cannot open file ($filename)";
         exit;
    }

if($fp){
    foreach($arr as $line) { fwrite($fp,$line); }
    fclose($fp);
    }

echo "Entry was deleted successfully!";
}

?>