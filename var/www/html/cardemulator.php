<?php

$mode = $_GET['mode'];
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="Responsive Header Nav">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
include 'menu.php';
echo '<section><center><p>';
echo '<h1><a href="setup.php">Card Reader Emulator</a></h1>';

if ($mode == 'main'){
   echo 'Please select the card reader emulator mode from the list below</br></br>';
   echo '<div class="box2">';
   echo '<a href="cardemulator.php?mode=id2">Initial D2</a></div></br>';
   echo '<div class="box2">';
   echo '<a href="cardemulator.php?mode=id3">Initial D3</a></div></br>';
   echo '<div class="box2">';
   echo '<a href="cardemulator.php?mode=wmmt">Wangan Midnight</a></div></br>';
   echo '<div class="box2">';
   echo '<a href="cardemulator.php?mode=mkgp">Mario Kart GP/2</a></div></br>';
   echo '<div class="box2">';
   echo '<a href="cardemulator.php?mode=fzero">F-Zero AX</a></div></br>';
}

if ($mode == 'id2'){
   echo '<b>Emulator Mode: Initial D2</b></br></br>';
   echo 'Please choose a card from the list or use the form to create a new card</br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
   $path = '/boot/config/cards/id2/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   foreach ($files as $file) {
   echo '<a href="launchcard.php?card='.$file.'&mode=id2"><img style="-webkit-user-select: none;" src="cards.php?name='.$file.'&amp;mode=id2"></a><br><br>';
         }
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="id2"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';

}

if ($mode == 'id3'){
   echo '<b>Emulator Mode: Initial D3</b></br></br>';
   echo 'Please choose a card from the list or you can use the form to create a new card</br></br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
   $path = '/boot/config/cards/id3/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   foreach ($files as $file) {
   echo '<a href="launchcard.php?card='.$file.'&mode=id3"><img style="-webkit-user-select: none;" src="cards.php?name='.$file.'&amp;mode=id3"></a><br><br>';
         }
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="id3"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';

}

if ($mode == 'wmmt'){
   echo '<b>Emulator Mode: Wangan Midnight</b></br></br>';
   echo 'Please choose a card from the list or you can use the form to create a new card</br></br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
   $path = '/boot/config/cards/wmmt/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   foreach ($files as $file) {
   echo '<a href="launchcard.php?card='.$file.'&mode=wmmt"><img style="-webkit-user-select: none;" src="cards.php?name='.$file.'&amp;mode=wmmt"></a><br><br>';
         }
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="wmmt"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';

}

if ($mode == 'mkgp'){
   echo '<b>Emulator Mode: Mario Kart GP/GP2</b></br></br>';
   echo 'Please choose a card from the list or you can use the form to create a new card</br></br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the pi powers off</br></br>';
   $path = '/boot/config/cards/mkgp/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   foreach ($files as $file) {
   echo '<a href="launchcard.php?card='.$file.'&mode=mkgp"><img style="-webkit-user-select: none;" src="cards.php?name='.$file.'&amp;mode=mkgp"></a><br><br>';
         }
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="mkgp"><br><br><input type="submit" class="dropbtn" value="Submit"></form><br><br>';

}

if ($mode == 'fzero'){
   echo '<b>Emulator Mode: F-Zero AX</b></br></br>';
   echo 'Please choose a card from the list or you can use the form to create a new card</br></br>';
   echo 'Once the card emulator has been launched it will stay running until a new card is launched or the powers off</br></br>';
   $path = '/boot/config/cards/fzero/';
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   foreach ($files as $file) {
   echo '<a href="launchcard.php?card='.$file.'&mode=fzero"><img style="-webkit-user-select: none;" src="cards.php?name='.$file.'&amp;mode=fzero"></a><br><br>';
         }
   echo '<form action="launchcard.php" method="get">Create New Card:<br><br><input type="text" name="card" onfocus="this.value=\'\'" value="Enter Card Name"><input type="hidden" name="mode" value="fzero"><br><br><input type="submit" value="Submit"></form><br><br>';

}
echo '</p><center></body></html>';
?>