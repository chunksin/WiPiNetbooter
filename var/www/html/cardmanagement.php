<?php
mb_internal_encoding("UTF-8");
$mode = $_GET['mode'];
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="Responsive Header Nav">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
include 'menu.php';
echo '<section><center><p>';
if ($mode == 'main'){
echo '<h1><a href="setup.php">Card Data Management</a></h1>';}
else {
echo '<h1><a href="cardmanagement.php?mode=main">Card Data Management</a></h1>';}

if ($_GET["command"] == 'delete') {
$deletefile = $_GET["filetodelete"];
$command1 = escapeshellcmd("sudo python /sbin/piforce/delete.py $deletefile");
shell_exec($command1);
$path_parts = pathinfo($deletefile);
$phpfile = '/var/www/html/cards/'.$mode.'/'.$path_parts['filename'].'.printdata.php';
$command2 = escapeshellcmd("sudo python /sbin/piforce/delete.py $phpfile");
shell_exec($command2);
header ("Location: cardmanagement.php");
}

$emumode = file_get_contents('/sbin/piforce/emumode.txt');
$nfcmode = file_get_contents('/sbin/piforce/nfcmode.txt');

if ($mode == 'main'){
   echo 'Please select the card reader emulator mode from the list below</br></br>';
   if ($nfcmode == 'nfcon'){
       echo 'You can use the buttons below to check the contents of an NFC card or wipe it ready for use<br><br>';
       echo 'Press the button then place the card on the reader within 10 seconds to scan it<br><br>';
       echo '<br><p><a href="cardactions.php?command=nfc_check" style="font-weight:normal" class="dropbtn">Check NFC Card</a>  <a href="cardactions.php?mode=main&command=nfcwipe" style="font-weight:normal" class="dropbtn">Wipe NFC Card</a></p><br>';
   }
   echo '<a href="cardmanagement.php?mode=idas"><img src="images/initd.png"></a></br><br>';
   echo '<a href="cardmanagement.php?mode=id2"><img src="images/initd2.png"></a></br><br>';
   echo '<a href="cardmanagement.php?mode=id3"><img src="images/initdv3e.png"></a></br><br>';
   echo '<a href="cardmanagement.php?mode=fzero"><img src="images/FZ.png"></a></br><br>';

}

if ($_GET["command"] == 'nfc') {
$copyfile = $_GET["filetocopy"];
$path_parts = pathinfo($copyfile);
$phpfile = '/var/www/html/cards/'.$mode.'/'.$path_parts['filename'].'.printdata.php';
$command1 = escapeshellcmd("sudo python3 /sbin/piforce/card_emulator/nfcwrite.py $copyfile $phpfile");
shell_exec($command1 . '> /dev/null 2>/dev/null &');
header ("Location: cardmanagement.php");
}

if ($mode == 'idas'){
   echo '<b>Initial D Cards</b></br></br>';

if ($nfcmode == 'nfcon'){
   echo 'Cards can be deleted permanently or copied to NFC card</br></br>';
}
else {
   echo 'Cards can be deleted permanently using the link below</br></br>';
}
   echo '<html><body><table class="center" id="options"><tr><th>Driver</th><th>Car</th><th>Saved</th><th>Action</th>'; if($nfcmode == 'nfcon'){echo '<th>Action</th>';} echo '</tr>';
   $path = '/boot/config/cards/idas/';
   $files = scandir($path);
   $files = array_diff(scandir($path), array('.', '..'));
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/idas/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          echo '<tr><td>'.$drivername.'<td>'.$carline1.' '.$carline2.'<td>'.$timestamp.'<td>'; if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=idas&command=nfcwrite&filetocopy='.$fullfile.'">NFC Copy</a><td>';} echo '<a href="cardmanagement.php?mode=idas&command=delete&filetodelete='.$fullfile.'">delete</a><tr>';}}
      else {
      if ($path_parts['extension'] == NULL){
          echo '<tr><td>ORPHAN<td>NO CAR DATA<td>'.$timestamp.'<td><a href="cardmanagement.php?mode=idas&command=delete&filetodelete='.$fullfile.'">delete</a><td><tr>';}}

}
echo '</table><br><br>';
if($nfcmode == 'nfcon'){
echo 'To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader<br><br>The card will be wiped and the reader will confirm a successful write with two beeps';
}
}

if ($mode == 'id2'){
   echo '<b>Initial D ver.2 Cards</b></br></br>';

if ($nfcmode == 'nfcon'){
   echo 'Cards can be deleted permanently or copied to NFC card</br></br>';
}
else {
   echo 'Cards can be deleted permanently using the link below</br></br>';
}
   echo '<html><body><table class="center" id="options"><tr><th>Driver</th><th>Car</th><th>Saved</th><th>Action</th>'; if($nfcmode == 'nfcon'){echo '<th>Action</th>';} echo '</tr>';
   $path = '/boot/config/cards/id2/';
   $files = scandir($path);
   $files = array_diff(scandir($path), array('.', '..'));
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/id2/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          echo '<tr><td>'.$drivername.'<td>'.$carline1.' '.$carline2.'<td>'.$timestamp.'<td>'; if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id2&command=nfcwrite&filetocopy='.$fullfile.'">NFC Copy</a><td>';} echo '<a href="cardmanagement.php?mode=id2&command=delete&filetodelete='.$fullfile.'">delete</a><tr>';}}
      else {
      if ($path_parts['extension'] == NULL){
          echo '<tr><td>ORPHAN<td>NO CAR DATA<td>'.$timestamp.'<td><a href="cardmanagement.php?mode=id2&command=delete&filetodelete='.$fullfile.'">delete</a><td><tr>';}}

}
echo '</table><br><br>';
if($nfcmode == 'nfcon'){
echo 'To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader<br><br>The card will be wiped and the reader will confirm a successful write with two beeps';
}
}

if ($mode == 'id3'){
   echo '<b>Initial D ver.3 Cards</b></br></br>';

if ($nfcmode == 'nfcon'){
   echo 'Cards can be deleted permanently or copied to NFC card</br></br>';
}
else {
   echo 'Cards can be deleted permanently using the link below</br></br>';
}
   echo '<html><body><table class="center" id="options"><tr><th>Driver</th><th>Car</th><th>Saved</th><th>Action</th>'; if($nfcmode == 'nfcon'){echo '<th>Action</th>';} echo '</tr>';
   $path = '/boot/config/cards/id3/';
   $files = scandir($path);
   $files = array_diff(scandir($path), array('.', '..'));
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/id3/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          echo '<tr><td>'.$drivername.'<td>'.$carline1.' '.$carline2.'<td>'.$timestamp.'<td>'; if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id3&command=nfcwrite&filetocopy='.$fullfile.'">NFC Copy</a><td>';} echo '<a href="cardmanagement.php?mode=id3&command=delete&filetodelete='.$fullfile.'">delete</a><tr>';}}
      else {
      if ($path_parts['extension'] == NULL){
          echo '<tr><td>ORPHAN<td>NO CAR DATA<td>'.$timestamp.'<td><a href="cardmanagement.php?mode=id3&command=delete&filetodelete='.$fullfile.'">delete</a><td><tr>';}}

}
echo '</table><br><br>';
if($nfcmode == 'nfcon'){
echo 'To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader<br><br>The card will be wiped and the reader will confirm a successful write with two beeps';
}
}

if ($mode == 'fzero'){
   echo '<b>F-Zero AX Cards</b></br></br>';

if ($nfcmode == 'nfcon'){
   echo 'Cards can be deleted permanently or copied to NFC card</br></br>';
}
else {
   echo 'Cards can be deleted permanently using the link below</br></br>';
}
   echo '<html><body><table class="center" id="options"><tr><th>Driver</th><th>License</th><th>Saved</th><th>Action</th>'; if($nfcmode == 'nfcon'){echo '<th>Action</th>';} echo '</tr>';
   $path = '/boot/config/cards/fzero/';
   $files = scandir($path);
   $files = array_diff(scandir($path), array('.', '..'));
   foreach($files as $file){
      $path_parts = pathinfo($file);
      $fullfile = $path.$file;
      $lastModifiedTimestamp = filemtime($fullfile);
      $timestamp = date("M d Y", $lastModifiedTimestamp);
      $includefile = "cards/fzero/".$file.".printdata.php";
      if (file_exists($includefile)){
      include $includefile;
      if ($path_parts['extension'] == NULL){
          echo '<tr><td>'.$driver.'<td>'.$license.'<td>'.$timestamp.'<td>'; if ($nfcmode == 'nfcon'){echo '<a href="cardactions.php?mode=id3&command=nfcwrite&filetocopy='.$fullfile.'">NFC Copy</a><td>';} echo '<a href="cardmanagement.php?mode=fzero&command=delete&filetodelete='.$fullfile.'">delete</a><tr>';}}
      else {
      if ($path_parts['extension'] == NULL){
          echo '<tr><td>ORPHAN<td>NO LICENSE DATA<td>'.$timestamp.'<td><a href="cardmanagement.php?mode=fzero&command=delete&filetodelete='.$fullfile.'">delete</a><td><tr>';}}

}
echo '</table><br><br>';
if($nfcmode == 'nfcon'){
echo 'To copy card data to an NFC card select NFC Copy then place an NFC card onto the reader<br><br>The card will be wiped and the reader will confirm a successful write with two beeps';
}
}


?>