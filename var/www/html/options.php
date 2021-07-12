<?php

$powermode = file_get_contents('/sbin/piforce/powerfile.txt');
$bootmode = file_get_contents('/sbin/piforce/bootfile.txt');
$bootrom = file_get_contents('/var/www/logs/log.txt');
$menumode = file_get_contents('/sbin/piforce/menumode.txt');
$relaymode = file_get_contents('/sbin/piforce/relaymode.txt');
$lcdmode = file_get_contents('/sbin/piforce/lcdmode.txt');
$zeromode = file_get_contents('/sbin/piforce/zeromode.txt');
$openmode = file_get_contents('/sbin/piforce/openmode.txt');
$soundmode = file_get_contents('/sbin/piforce/soundmode.txt');
$navmode = file_get_contents('/sbin/piforce/navmode.txt');
$openffbmode = file_get_contents('/sbin/piforce/ffbmode.txt');

$csvfile = 'csv/romsinfo.csv';
$path = '/boot/roms';

$lastgamearray = explode(" ", $bootrom);
$lastgame = $lastgamearray[0];

$f = fopen($csvfile, "r");
 while ($row = fgetcsv($f)) {
   if ($row[1] == $lastgame){
     $gamename = $row[4];
   }
}

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center>';
echo '<h1><a href="gamelist.php?display=all">Options Menu</a></h1><br>';
echo '<html><body><table class="center" id="options"><tr><th>Option</th><th>Setting</th><th>Action</th></tr>';
if ($menumode == 'simple'){echo '<tr><td>Simple Menu</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=advanced">disable</a></td></tr>';}
if ($menumode == 'advanced'){echo '<tr><td>Simple Menu</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=simple">enable</a></td></tr>';}
if ($powermode == 'always-on'){echo '<tr><td>Power Saver</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=auto-off">enable</a></td></tr>';}
if ($powermode == 'auto-off'){echo '<tr><td>Power Saver</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=always-on">disable</a></td></tr>';}
if ($bootmode == 'multi'){echo '<tr><td>Single Boot</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=single">enable</a></td></tr>';}
if ($bootmode == 'single'){echo '<tr><td>Single Boot</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=multi">disable</a></td></tr>';}
if ($relaymode == 'relayon'){echo '<tr><td>Relay Reboot</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=relayoff">disable</a></td></tr>';}
if ($relaymode == 'relayoff'){echo '<tr><td>Relay Reboot</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=relayon">enable</a></td></tr>';}
if ($zeromode == 'hackon'){echo '<tr><td>Time Hack</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=hackoff">disable</a></td></tr>';}
if ($zeromode == 'hackoff'){echo '<tr><td>Time Hack</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=hackon">enable</a></td></tr>';}
if ($soundmode == 'soundon'){echo '<tr><td>Video Sound</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=soundoff">disable</a></td></tr>';}
if ($soundmode == 'soundoff'){echo '<tr><td>Video Sound</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=soundon">enable</a></td></tr>';}
if ($navmode == 'navon'){echo '<tr><td>Nav Button</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=navoff">disable</a></td></tr>';}
if ($navmode == 'navoff'){echo '<tr><td>Nav Button</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=navon">enable</a></td></tr>';}
if ($openmode == 'openon'){echo '<tr><td>OpenJVS</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=openoff">disable</a></td></tr>';}
if ($openmode == 'openoff'){echo '<tr><td>OpenJVS</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=openon">enable</a></td></tr>';}
if ($openffbmode == 'ffbon'){echo '<tr><td>OpenFFB</td><td><b>enabled</b></td><td><a href="switchmode.php?mode=ffboff">disable</a></td></tr>';}
if ($openffbmode == 'ffboff'){echo '<tr><td>OpenFFB</td><td><b>disabled</b></td><td><a href="switchmode.php?mode=ffbon">enable</a></td></tr>';}
if ($lcdmode == 'LCD16'){echo '<tr><td>LCD Mode</td><td><b>16x2</b></td><td><a href="switchmode.php?mode=LCD35">3.5 touch</a></td></tr></table>';}
if ($lcdmode == 'LCD35'){echo '<tr><td>LCD Mode</td><td><b>3.5 touch</b></td><td><a href="switchmode.php?mode=LCD16">16x2</a></td></tr></table>';}
echo '<table class="center" id="options"><tr></tr>';
if ($lastgame !== ''){echo '<tr><td><b>Last Game Played: </td><td>'.$gamename.'</td></tr></table>';}
else {echo '<tr><td><b>Last Game Played: </td><td>Unknown</td></tr></table>';}
echo '</html>';
?>