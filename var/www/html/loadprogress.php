<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

$name = $_GET["name"];

echo '<p>';
?>

<section><center>

<?php
echo '<h1><a href="gamelist.php?display=all#anchor'.$name.'">Loading<br>'.$name.'</a></h1></center>';
?>

<div id="myProgress">
  <div id="myBar">0%</div>
</div>

<?php

$handle = popen('sudo tail -n 1 /var/log/progress.txt', 'r');
$progress = fgets($handle);
pclose($handle);

if ((is_int($progress) && $progress < 100) || $progress != 'COMPLETE'){
    echo '<script>';
    echo 'var elem = document.getElementById("myBar");';
    echo 'elem.style.width = '.$progress.' + "%";';
    echo 'elem.innerHTML = '.$progress.'  + "%";';
    echo 'setTimeout(function(){window.location="loadprogress.php?name='.$name.'";}, 800)';
    echo '</script>';
}
else{
echo '<script>';
echo 'var elem = document.getElementById("myBar");';
echo 'elem.style.width = 100 + "%";';
echo 'elem.innerHTML = 100  + "%";';
echo '</script>';
echo '<br><center><a href="gamelist.php?display=all#anchor'.$name.'">LOADING COMPLETE</a></center>';
echo '<script type="text/javascript">';
echo 'setTimeout(function(){window.location="gamelist.php?display=all#anchor'.$name.'";}, 2000)';
echo '</script>';
}
?>
