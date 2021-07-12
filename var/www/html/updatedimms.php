<?php
header("Refresh: 1; url=dimms.php");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';

$filename = "csv/dimms.csv";
$linenum = $_GET["linenum"];
$action = $_GET["action"];

if ($action == "delete"){
echo 'Deleting '.$_GET["name"].' ...';
echo '</p><center></body></html>';
DelLine($filename, $linenum);
}

if ($action == "update"){
echo 'Updating '.$_GET["name"].' ...';
echo '</p><center></body></html>';
$name = $_GET["name"];
$ip = $_GET["ip"];
$type = $_GET["type"];

$update = $name. ",". $ip. ",". $type. "\n";

UpdateLine($filename, $linenum, $update);
}


function DelLine($filename, $linenum){
  
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

function UpdateLine($filename, $linenum, $update){
  
$arr = file($filename);
$lineToUpdate = $linenum;
$arr["$lineToUpdate"] = $update;

if (!$fp = fopen($filename, 'w+')){
        print "Cannot open file ($filename)";
         exit;
    }

if($fp){
    foreach($arr as $line) { fwrite($fp,$line); }
    fclose($fp);
    }

echo "Entry was updated successfully!";
}


?>