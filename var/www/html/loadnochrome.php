<?php

set_time_limit(0);
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

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

echo '<p>';

?>

<section><center>

<?php
echo '<h1><a href="gamelist.php?display=all#anchor'.$name.'">Loading<br>'.$name.'</a></h1></center>';
?>

<?php

$command = escapeshellcmd('sudo python /sbin/piforce/webforce.py '.$rom.' '.$dimm.' '.$relaymode.' '.$zeromode.' '.$mapping.' '.$ffb);
$output = shell_exec($command . '> /dev/null 2>/dev/null &');

$progress = 100;
while(is_int($progress) && $progress != 0 || $progress == 'COMPLETE'){
$handle = popen('sudo tail -n 1 /var/log/progress.txt', 'r');
$progress = fgets($handle);
pclose($handle);
sleep(0.1);
}

?>

<script type="text/javascript">
<?php
echo 'setTimeout(function(){window.location="loadprogress.php?name='.$name.'";}, 1)';
?>
</script>