<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

echo '<section><center><p>';

$ip = $_POST["ip"];
$version = $_POST["version"];

echo '<div class="box2"></p>';
echo 'You are sending version <b>'.$version.'</b> to <b>'.$ip.'</b><br><br>';
echo '<b>Are you sure?</b><br><br>';
echo '<form action="fwupdatesend.php?ip='.$ip.'&version='.$version.'" method="post">';
echo '<button type="submit" class="dropbtn" value="Confirm">Confirm</button></form><br>';
echo '<form action="fwupdate.php">';
echo '<button type="submit" class="dropbtn" value="Cancel">Cancel</button></form>';
echo '</p></div><center></body></html>';

?>