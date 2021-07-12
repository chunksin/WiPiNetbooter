<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openjvs.php">OpenJVS Updater</a></h1><br>';

$confirm = $_GET["confirm"];

echo '<div class="box2"></p>';
echo '<b>This will update OpenJVS to the latest version</b><br><br>';
echo '<b>Are you sure?</b><br><br>';
echo '<form action="updateopenjvs.php?confirm=yes" method="post">';
echo '<button type="submit" class="dropbtn" value="Confirm">Confirm</button></form><br>';
echo '<form action="openjvs.php">';
echo '<button type="submit" class="dropbtn" value="Cancel">Cancel</button></form><br></div>';

if ($confirm == "yes"){

echo '<br>';
ini_set('output_buffering', false);
    $handle = popen('sudo /root/update-openjvs.sh', 'r');
    while(!feof($handle)) {
      $buffer = fgets($handle);
      echo "$buffer";
      flush();
}
pclose($handle);
echo '<br><br><a href="openjvs.php">Return to OpenJVS Menu</a>';
}

?>