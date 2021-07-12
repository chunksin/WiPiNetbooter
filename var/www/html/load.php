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

$command = escapeshellcmd('sudo python /sbin/piforce/webforce.py '.$rom.' '.$dimm.' '.$relaymode.' '.$zeromode.' '.$mapping.' '.$ffb);
$output = shell_exec($command . '> /dev/null 2>/dev/null &');

echo '<p>';

$loadtime = ceil($filesize/4010000);
$totalloadtime = ($loadtime+6);
$redirecttimer = (($totalloadtime+2)*1000);
$frames = ($totalloadtime*10);

?>

<section><center>
<body onload="move()">

<?php
echo '<h1>Loading<br>'.$name.'<h1></center>';
?>

<div id="myProgress">
  <div id="myBar">0%</div>
</div>

<script type="text/javascript">
<?php
echo 'setTimeout(function(){window.location="gamelist.php?display=all#anchor'.$name.'";}, '.$redirecttimer.')';
?>
</script>

<script>
var i = 0;
function move() {
  if (i == 0) {
    i = 1;
    var elem = document.getElementById("myBar");
    var width = 0;

<?php
echo 'var id = setInterval(frame, '.$frames.');';
?>

    function frame() {
      if (width >= 100) {
        clearInterval(id);
        i = 0;
      } else {
        width++;
        elem.style.width = width + "%";
        elem.innerHTML = width  + "%";
      }
    }
  }
}
</script>