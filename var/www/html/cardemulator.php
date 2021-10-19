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
echo '<h1><a href="setup.php">Card Reader Emulator</a></h1>';}
else {
echo '<h1><a href="cardemulator.php?mode=main">Card Reader Emulator</a></h1>';}

$emumode = file_get_contents('/sbin/piforce/emumode.txt');

if ($mode == 'main'){
   echo 'Please select the card reader emulator mode from the list below</br></br>';
   echo '<a href="cardemulator.php?mode=idas"><img src="images/initd.png"></a></br><br>';
   echo '<a href="cardemulator.php?mode=id2"><img src="images/initd2.png"></a></br><br>';
   echo '<a href="cardemulator.php?mode=id3"><img src="images/initdv3e.png"></a></br><br>';
   echo '<a href="cardemulator.php?mode=wmmt"><img src="images/wmmt.gif"></a></br><br>';
   echo '<a href="cardemulator.php?mode=mkgp"><img src="images/mkgp.png"></a></br><br>';
   echo '<a href="cardemulator.php?mode=fzero"><img src="images/FZ.png"></a></br>';
}

if ($mode == 'idas'){
   echo '<b>Emulator Mode: Initial D ('.$emumode.')</b></br></br>';
if ($emumode == 'auto'){
   echo 'Please choose a card from the list or purchase a new card in the game</br><br>';
}
else {
   echo 'Please choose a card from the list or use the form to create a new card</br><br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
}
   $path = '/boot/config/cards/idas/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $i = 0;
   foreach ($files as $file) {
   $path_parts = pathinfo($file);
   if ($path_parts['extension'] == NULL && file_exists("cards/idas/".$file.".printdata.php")){
   echo '<a href="launchcard.php?card='.$file.'&mode=idas&launchmode='.$emumode.'"><img style="-webkit-user-select: none;" src="idcards.php?name='.$file.'&amp;mode=idas"></a><br><br>';
   $i++;
         }
   }

if ($i == 0){
   echo '<b>NO CARDS FOUND</b>';
}

if ($emumode == 'manual'){
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="idas"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';
}

}

if ($mode == 'id2'){
   echo '<b>Emulator Mode: Initial D ver.2 ('.$emumode.')</b></br></br>';
if ($emumode == 'auto'){
   echo 'Please choose a card from the list or purchase a new card in the game</br><br>';
}
else {
   echo 'Please choose a card from the list or use the form to create a new card</br><br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
}
   $path = '/boot/config/cards/id2/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $i = 0;
   foreach ($files as $file) {
   $path_parts = pathinfo($file);
   if ($path_parts['extension'] == NULL && file_exists("cards/id2/".$file.".printdata.php")){
   echo '<a href="launchcard.php?card='.$file.'&mode=id2&launchmode='.$emumode.'"><img style="-webkit-user-select: none;" src="idcards.php?name='.$file.'&amp;mode=id2"></a><br><br>';
   $i++;
         }
   }

if ($i == 0){
   echo '<b>NO CARDS FOUND</b>';
}

if ($emumode == 'manual'){
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="id2"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';
}

}

if ($mode == 'id3'){
   echo '<b>Emulator Mode: Initial D ver.3 ('.$emumode.')</b></br></br>';
if ($emumode == 'auto'){
   echo 'Please choose a card from the list or purchase a new card in the game</br><br>';
}
else {
   echo 'Please choose a card from the list or use the form to create a new card</br><br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
}
   $path = '/boot/config/cards/id3/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $i = 0;
   foreach ($files as $file) {
   $path_parts = pathinfo($file);
   if ($path_parts['extension'] == NULL && file_exists("cards/id3/".$file.".printdata.php")){
   echo '<a href="launchcard.php?card='.$file.'&mode=id3&launchmode='.$emumode.'"><img style="-webkit-user-select: none;" src="idcards.php?name='.$file.'&amp;mode=id3"></a><br><br>';
   $i++;
         }
   }

if ($i == 0){
   echo '<b>NO CARDS FOUND</b>';
}

if ($emumode == 'manual'){
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="id3"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';
}

}

if ($mode == 'wmmt'){
   echo '<b>Emulator Mode: Wangan Midnight</b></br></br>';
   echo 'Please choose a card from the list or you can use the form to create a new card</br></br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
   $path = '/boot/config/cards/wmmt/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   foreach ($files as $file) {
   echo '<a href="launchcard.php?card='.$file.'&mode=wmmt&launchmode=manual"><img style="-webkit-user-select: none;" src="cards.php?name='.$file.'&amp;mode=wmmt"></a><br><br>';
         }
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="wmmt"><input type="hidden" name="launchmode" value="manual"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';

}

if ($mode == 'mkgp'){
   echo '<b>Emulator Mode: Mario Kart GP/GP2</b></br></br>';
   echo 'Please choose a card from the list or you can use the form to create a new card</br></br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
   $path = '/boot/config/cards/mkgp/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   foreach ($files as $file) {
   echo '<a href="launchcard.php?card='.$file.'&mode=mkgp&launchmode=manual"><img style="-webkit-user-select: none;" src="cards.php?name='.$file.'&amp;mode=mkgp"></a><br><br>';
         }
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="mkgp"><input type="hidden" name="launchmode" value="manual"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';

}

if ($mode == 'fzero'){
   echo '<b>Emulator Mode: F-Zero AX ('.$emumode.')</b></br></br>';
if ($emumode == 'auto'){
   echo 'Please choose a card from the list or purchase a new card in the game</br><br>';
}
else {
   echo 'Please choose a card from the list or use the form to create a new card</br><br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
}
   $path = '/boot/config/cards/fzero/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $i = 0;
   foreach ($files as $file) {
   $path_parts = pathinfo($file);
   if ($path_parts['extension'] == NULL && file_exists("cards/fzero/".$file.".printdata.php")){
   echo '<a href="launchcard.php?card='.$file.'&mode=fzero&launchmode='.$emumode.'"><img style="-webkit-user-select: none;" src="fzcards.php?name='.$file.'&amp;mode=fzero"></a><br><br>';
   $i++;
         }
   }

if ($i == 0){
   echo '<b>NO CARDS FOUND</b>';
}

if ($emumode == 'manual'){
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="fzero"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';
}

}
echo '</p><center></body></html>';
?>