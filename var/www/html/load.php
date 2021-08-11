<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

function gzfilesize($filename) {
  $gzfs = FALSE;
  if(($zp = fopen($filename, 'r'))!==FALSE) {
    if(@fread($zp, 2) == "\x1F\x8B") { // this is a gzip'd file
      fseek($zp, -4, SEEK_END);
      if(strlen($datum = @fread($zp, 4))==4)
        extract(unpack('Vgzfs', $datum));
    }
    else // not a gzip'd file, revert to regular filesize function
      $gzfs = filesize($filename);
    fclose($zp);
  }
  return($gzfs);
}

$relaymode = file_get_contents('/sbin/piforce/relaymode.txt');
$zeromode = file_get_contents('/sbin/piforce/zeromode.txt');
$openmode = file_get_contents('/sbin/piforce/openmode.txt');
$ffbmode = file_get_contents('/sbin/piforce/ffbmode.txt');
$rom = $_GET["rom"];
$rompath = '/boot/roms/'.$rom;
$name = $_GET["name"];
$dimm = $_GET["dimm"];
$mapping = $_GET["mapping"];
$ffb = $_GET["ffb"];
$filesize = gzfilesize($rompath);
ini_set('output_buffering', false);
$last = 0;

echo '<p>';

$loadtime = ceil($filesize/4010000);
$totalloadtime = ($loadtime+6);
$redirecttimer = (($totalloadtime+2)*1000);
$frames = ($totalloadtime*10);

?>

<section><center>

<?php
echo '<h1>Loading<br>'.$name.'</h1></center>';
?>

<div id="myProgress">
  <div id="myBar">0%</div>
</div>

<?php

$command = escapeshellcmd('sudo python /sbin/piforce/webforce.py '.$rom.' '.$dimm.' '.$relaymode.' '.$zeromode.' '.$mapping.' '.$ffb);
$output = shell_exec($command . '> /dev/null 2>/dev/null &');

$progress = 0;
while($progress < 100) {
    $handle = popen('sudo tail -n 1 /var/log/progress.txt', 'r');
    $progress = fgets($handle);
    if(($progress > $last && $progress < 100) || $progress == 10){
         echo '<script>';
         echo 'var elem = document.getElementById("myBar");';
         echo 'elem.style.width = '.$progress.' + "%";';
         echo 'elem.innerHTML = '.$progress.'  + "%";';
         echo '</script>';
    }
    $last = $progress;
    ob_flush(); 
    flush();
    sleep(0.1);
    pclose($handle);
}


echo '<script>';
echo 'var elem = document.getElementById("myBar");';
echo 'elem.style.width = 100 + "%";';
echo 'elem.innerHTML = 100  + "%";';
echo '</script>';
echo '<br><center><a href="gamelist.php?display=all#anchor'.$name.'">LOADING COMPLETE</a></center>';
?>

<script type="text/javascript">
<?php
echo 'setTimeout(function(){window.location="gamelist.php?display=all#anchor'.$name.'";}, 2000)';
?>
</script>