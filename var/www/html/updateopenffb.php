<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openffb.php">OpenFFB Updater</a></h1><br>';

$confirm = $_GET["confirm"];

echo '<div class="box2"></p>';
echo '<b>This will update OpenFFB to the latest version</b><br><br>';
echo '<b>Are you sure?</b><br><br>';
echo '<form action="updateopenffb.php?confirm=yes" method="post">';
echo '<button type="submit" class="dropbtn" value="Confirm">Confirm</button></form><br>';
echo '<form action="openffb.php">';
echo '<button type="submit" class="dropbtn" value="Cancel">Cancel</button></form><br></div>';

if ($confirm == "yes"){

echo '<br>';
ini_set('output_buffering', false);
    $handle = popen('sudo /root/update-openffb.sh', 'r');
    while(!feof($handle)) {
      $buffer = fgets($handle);
      echo "$buffer";
      flush();
}
pclose($handle);
echo '<br><br><a href="openffb.php">Return to OpenFFB Menu</a>';
}

?>