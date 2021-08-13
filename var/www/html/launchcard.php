<?php

header("Refresh: 5; url=cardemulator.php?mode=main");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
$card = $_GET["card"];
$mode = $_GET["mode"];
$emuport = '';
$devices = array();
$devices = glob('/dev' . '/ttyUSB*');

if (readlink("/dev/COM1")){
echo 'COM1 is present - checking ports<br>';
$comport = readlink("/dev/COM1");
$compath = '/dev/'.$comport;
echo 'COM1 path: '.$compath.'<br>';
foreach ($devices as $device) {
    if ($device != $compath){
       $emuport = $device;
    }
  }
}
else{
$emuport = '/dev/ttyUSB0';
}

if(empty($devices) || $emuport == null){
   echo '<br><b>No serial adaptor detected<br>';
   echo 'Please check connections</b>';}
else {
echo '<br>Card emulator will launch on: '.$emuport.'<br><br>';
echo '<b>Starting card emulator with card '.$card.'</b>';

$command1 = escapeshellcmd('sudo python /sbin/piforce/card_emulator/cardlog.py /boot/config/cards/'.$mode.'/'.$card);
shell_exec($command1 . '> /dev/null 2>/dev/null &');
$command2 = escapeshellcmd('sudo python /sbin/piforce/card_emulator/'.$mode.'cardemu.py -cp '.$emuport.' -f /boot/config/cards/'.$mode.'/'.$card);
shell_exec($command2 . '> /dev/null 2>/dev/null &');
}

?>