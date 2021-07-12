<?php

$filename = $_GET["rom"];
$gamename = $_GET["name"];
$system = $_GET["system"];
$mapping = $_GET["mapping"];
$ffb = $_GET["ffb"];

function pinger($address){
        $command = "fping -c1 -t500 $address";
        exec($command, $output, $status);
        if($status === 0){
            return true;
        }else{
            return false;
        }
    }

$f = fopen("csv/dimms.csv", "r");
$headers = ($row = fgetcsv($f));
$onlinedimms = array();
while (($row = fgetcsv($f)) !== false) {
$dimmname = $row[0];
$ip = $row[1];
$dimmtype = $row[2];

if ($dimmtype == "Sega Naomi"){
   $supported = array("Sega Naomi", "Sammy Atomiswave");}
if ($dimmtype == "Sega Naomi2"){
   $supported = array("Sega Naomi","Sega Naomi2", "Sammy Atomiswave");}
if ($dimmtype == "Sega Chihiro"){
   $supported = array("Sega Chihiro");}
if ($dimmtype == "Sega Triforce"){
   $supported = array("Sega Triforce");}

if (in_array($system, $supported)){
   if (pinger($row[1]) == true){
      $onlinedimms[$ip] = $dimmname;
      $onlydimm = $ip;
   }
 }
}

$count = count($onlinedimms);

if ($count == 1){
    header ("Location: load.php?rom=$filename&name=$gamename&dimm=$onlydimm&mapping=$mapping&ffb=$ffb");
}

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center>';

if ($count == 0){
   echo '<div class="box2"></p>';
   echo '<b>No dimms available for this system</b></p>';
   echo '<h1><form action="gamelist.php?display=all" method="post"><input type="submit" class="bigdropbtn" value="Main Menu"></form></h1>';
   echo '</div>';
}

if ($count > 1){
   echo '<div class="box2"></p>';
   echo '<b>Multiple dimms found for '.$system.'</b></p>';
   foreach($onlinedimms as $ipaddress => $name) {
      echo '<h1><form action="load.php?rom='.$filename.'&name='.$gamename.'&dimm='.$ipaddress.'&mapping='.$mapping.'&ffb='.$ffb.'" method="post"><input type="submit" class="bigdropbtn" value="'.$name.'"></form></h1>';
   }
  echo '</div>';
}

?>