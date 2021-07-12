<?php

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="Responsive Header Nav">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
include 'menu.php';

?>

<!DOCTYPE html>
<html>
<body>

<?php

$display = $_GET['display'];
$filtertype = $_GET['filter'];
$value = $_GET['value'];
$filename = $_GET['filename'];
$csvfile = 'csv/romsinfo.csv';
$path = '/boot/roms';
$menumode = file_get_contents('/sbin/piforce/menumode.txt');
$openmode = file_get_contents('/sbin/piforce/openmode.txt');
$soundmode = file_get_contents('/sbin/piforce/soundmode.txt');
$navmode = file_get_contents('/sbin/piforce/navmode.txt');
$ffbmode = file_get_contents('/sbin/piforce/ffbmode.txt');

echo '<section><center>';
echo '<div>';

if ($navmode == "navon"){
echo '<button onclick="topFunction()" id="rtnBtn" title="Go to top"><img src="img/rtn.png" /></button>';
}

if ($display == 'all'){

?>

<div class="dropdown">
  <button onclick="SystemFunction()" class="dropbtn">System</button>
  <div id="SystemDropdown" class="dropdown-content">

<?php
$unique_ids = array();
$f = fopen('csv/romsinfo.csv', 'r');
$headers = ($row = fgetcsv($f));
while ($row = fgetcsv($f)) {
    $unique_ids[$row[0]] = true;
}
ksort($unique_ids);
$categories = array_keys($unique_ids);
 foreach($categories as $category => $value) 
  { 
    echo '<a href="/gamelist.php?filter=system&value='.$value.'">'.$value.'</a>';
  }
fclose($f);
?>
 </div>
</div>

<div class="dropdown">
  <button onclick="GenreFunction()" class="dropbtn">Genre</button>
  <div id="GenreDropdown" class="dropdown-content">

<?php
$unique_ids = array();
$f = fopen('csv/romsinfo.csv', 'r');
$headers = ($row = fgetcsv($f));
while ($row = fgetcsv($f)) {
    $unique_ids[$row[8]] = true;
}
ksort($unique_ids);
$categories = array_keys($unique_ids);
 foreach($categories as $category => $value) 
  { 
    echo '<a href="/gamelist.php?filter=genre&value='.$value.'">'.$value.'</a>';
  }
fclose($f);
?>

 </div>
</div>

<div class="dropdown">
  <button onclick="OrientationFunction()" class="dropbtn">Orientation</button>
  <div id="OrientationDropdown" class="dropdown-content">

<?php
$unique_ids = array();
$f = fopen('csv/romsinfo.csv', 'r');
$headers = ($row = fgetcsv($f));
while ($row = fgetcsv($f)) {
    $unique_ids[$row[10]] = true;
}
ksort($unique_ids);
$categories = array_keys($unique_ids);
 foreach($categories as $category => $value) 
  { 
    echo '<a href="/gamelist.php?filter=orientation&value='.$value.'">'.$value.'</a>';
  }
fclose($f);
?>

 </div>
</div>

<div class="dropdown">
  <button onclick="ControlFunction()" class="dropbtn">Controls</button>
  <div id="ControlDropdown" class="dropdown-content">

<?php
$unique_ids = array();
$f = fopen('csv/romsinfo.csv', 'r');
$headers = ($row = fgetcsv($f));
while ($row = fgetcsv($f)) {
    $unique_ids[$row[11]] = true;
}
ksort($unique_ids);
$categories = array_keys($unique_ids);
 foreach($categories as $category => $value) 
  { 
    echo '<a href="/gamelist.php?filter=controls&value='.$value.'">'.$value.'</a>';
  }
fclose($f);
?>

 </div>
</div>

<br><br>

<?php

$files = array_values(array_diff(scandir($path), array('.', '..')));
$games_array = array();
$f = fopen('csv/romsinfo.csv', 'r');
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes")){
                  $games_array[strtoupper(substr($row[4],0,1))] = true;

  }
}
}

$alphabetUpper = range('A', 'Z');
$letters = array_keys($games_array);
 foreach($alphabetUpper as $letter => $value)
  {
    if (in_array($value, $letters)){echo '<li><a href="#anchor'.$value.'" class="scrollLink">'.$value.'</a></li>';}
    else{echo '<li>'.$value.'</li>';}
  }
fclose($f);

echo '<br><br></div>';
echo '<a id="anchorTOP" class="anchors"></a>';


   $lastname = 'aaaa';
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes")){
                  echo '<div class="box1">';
                  $i++;
                  $system = $row[0];
                  $filename = $row[1];
                  $image = $row[2];
                  $gamename = $row[4];
                  $mapping = $row[14];
                  $ffb = $row[15];
                  $lastletter = strtoupper(substr($lastname,0,1));
                  $thisletter = strtoupper(substr($gamename,0,1));
                  if (strcmp($lastletter, $thisletter) < 0 ){
                       echo '<a id="anchor'.$thisletter.'" class="anchors"></a>';
                  }
                  echo '<a id="anchor'.$gamename.'" class="anchors"></a>';                  
                  $lastname = $gamename;
                  if ($menumode == 'advanced'){
                  echo '<a href="gamelist.php?filename='.$row[1].'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="gamelist.php?filename='.$row[1].'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div>';}
                  else {echo '<b><a href="gamelist.php?filename='.$row[1].'">'.$gamename.'</a></b><br>';}}
                  else {
                  echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br></br>';}
                  else {echo '<b><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a><br>';}}
                  echo '</div><br>';
                  break;
              }
         }
     }
   fclose($f);

   if ($i == 0){
      echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
   }

}

if ($filename !== null && $display != "all") {
    $f = fopen($csvfile, "r");
    while ($row = fgetcsv($f)) {
        if ($row[1] == $filename) {
            $system = $row[0];
            $image = $row[2];
            $video = $row[3];
            $gamename = $row[4];
            $manufacturer = $row[6];
            $year = $row[7];
            $genre = $row[8];
            $rating = $row[9];
            $orientation = $row[10];
            $controls = $row[11];
            $enabled = $row[12];
            $fave = $row[13];
            $mapping = $row[14];
            $ffb = $row[15];
            if ($filtertype != null){
                 echo '<a href="gamelist.php?filter='.$filtertype.'&value='.$value.'#anchor'.$gamename.'"><img src="images/'.$image.'"></a><br><br>';
                 echo '<h1><a href="gamelist.php?filter='.$filtertype.'&value='.$value.'#anchor'.$gamename.'">'.$gamename.'</a><h1>';}
            elseif ($display != null){
                 echo '<a href="gamelist.php?display='.$display.'#anchor'.$gamename.'"><img src="images/'.$image.'"></a><br><br>';
                 echo '<h1><a href="gamelist.php?display='.$display.'#anchor'.$gamename.'">'.$gamename.'</a><h1>';}
            else {
                 echo '<a href="gamelist.php?display=all#anchor'.$gamename.'"><img src="images/'.$image.'"></a><br><br>';
                 echo '<h1><a href="gamelist.php?display=all#anchor'.$gamename.'">'.$gamename.'</a><h1>';
            }
            echo '<h1><form action="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'" method="post"><input type="submit" class="bigdropbtn" value="Launch Game"></form></h1>';
            if ($video !== ''){
              if ($soundmode == "soundoff"){
                echo '<video height=240 width=320 controls autoplay playsinline muted loop id="myVideo"><source src="/videos/'.$video.'" type="video/mp4"></video><br><br>';}
              else {
                echo '<video height=240 width=320 controls autoplay playsinline loop id="myVideo"><source src="/videos/'.$video.'" type="video/mp4"></video><br><br>';}
            }
            echo '<table id="gameinfo" style="width:100%"><tr><td><b>System</b></td><td>'.$system.'</td><td><b>Manufacturer</b></td><td>'.$manufacturer.'</td></tr><tr><td><b>Year</b></td><td>'.$year.'</td><td><b>Genre</b></td><td>'.$genre.'</td></tr><tr><td><b>Controls</b></td><td>'.$controls.'</td><td><b>Orientation</b></td><td>'.$orientation.'</td></tr>';
            if ($openmode == 'openon'){
                $command = escapeshellcmd('sudo python /sbin/piforce/devicelist.py');
                shell_exec($command);
                include 'devicelist.php';
                echo '<tr><td><b>Mapping</b></td><td>'.$mapping.'</td><td><b>Controllers</b></td><td>'.$enableddevices.'</td></tr>';}
            echo '</table><br>';
            break;
        }
    }
   fclose($f);
if ($fave !== 'Yes'){
echo '<a href="updatecsvfave.php?rom='.$filename.'&fave=Yes">Add to Favourites</a>';
} else{
echo '<a href="updatecsvfave.php?rom='.$filename.'&fave=No">Remove from Favourites</a>';
}}


if ($filtertype == "genre" && $filename == null) {
   echo '<div><a href="gamelist.php?display=all"></div><h1>'.$value.' Games</h1></a></h1></div><br>';
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes") and ($row[8] == $value)){
                  echo '<div class="box1">';
                  $i++;
                  $system = $row[0];
                  $filename = $row[1];
                  $image = $row[2];
                  $gamename = $row[4];
                  $mapping = $row[14];
                  $ffb = $row[15];
                  echo '<a id="anchor'.$gamename.'" class="anchors"></a>';
                  if ($menumode == 'advanced'){
                  echo '<a href="gamelist.php?filename='.$row[1].'&filter='.$filtertype.'&value='.$value.'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="gamelist.php?filename='.$row[1].'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                  else {echo '<b><a href="gamelist.php?filename='.$filename.'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b></br>';}}
                  else {
                  echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                  else {echo '<b><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></br>';}}
                  echo '</div><br>';
                  break;
              }
         }
     }
   fclose($f);

   if ($i == 0){
      echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
   }
}

if ($filtertype == "system" && $filename == null) {
   echo '<a href="gamelist.php?display=all"><img src="img/'.$value.'.png"></a><br><br>';
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes") and ($row[0] == $value)){
                  echo '<div class="box1">';
                  $i++;
                  $system = $row[0];
                  $filename = $row[1];
                  $image = $row[2];
                  $gamename = $row[4];
                  $mapping = $row[14];
                  $ffb = $row[15];
                  echo '<a id="anchor'.$gamename.'" class="anchors"></a>';
                  if ($menumode == 'advanced'){
                  echo '<a href="gamelist.php?filename='.$filename.'&filter='.$filtertype.'&value='.$value.'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="gamelist.php?filename='.$row[1].'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                  else {echo '<b><a href="gamelist.php?filename='.$filename.'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b></br>';}}
                  else {
                  echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                  else {echo '<b><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></br>';}}
                  echo '</div><br>';
                  break;
              }
         }
     }
   fclose($f);

   if ($i == 0){
      echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
   }
}

if ($filtertype == "orientation" && $filename == null) {
   echo '<div><a href="gamelist.php?display=all"></div><h1>'.$value.' Games</h1></a></div><br>';   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes") and ($row[10] == $value)){
                  echo '<div class="box1">';
                  $i++;
                  $system = $row[0];
                  $filename = $row[1];
                  $image = $row[2];
                  $gamename = $row[4];
                  $mapping = $row[14];
                  $ffb = $row[15];
                  echo '<a id="anchor'.$gamename.'" class="anchors"></a>';
                  if ($menumode == 'advanced'){
                  echo '<a href="gamelist.php?filename='.$filename.'&filter='.$filtertype.'&value='.$value.'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="gamelist.php?filename='.$row[1].'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                  else {echo '<b><a href="gamelist.php?filename='.$row[1].'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b></br>';}}
                  else {
                  echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                  else {echo '<b><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></br>';}}
                  echo '</div><br>';
                  break;
              }
         }
     }
   fclose($f);

   if ($i == 0){
      echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
   }
}

if ($filtertype == "controls" && $filename == null) {
   echo '<div><a href="gamelist.php?display=all"></div><h1>Games with '.strtolower($value).' '.$filtertype.'</h1></a></div><br>';
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[12] == "Yes") and ($row[11] == $value)){
                  echo '<div class="box1">';
                  $i++;
                  $system = $row[0];
                  $filename = $row[1];
                  $image = $row[2];
                  $gamename = $row[4];
                  $mapping = $row[14];
                  $ffb = $row[15];
                  echo '<a id="anchor'.$gamename.'" class="anchors"></a>';
                  if ($menumode == 'advanced'){
                  echo '<a href="gamelist.php?filename='.$row[1].'&filter='.$filtertype.'&value='.$value.'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="gamelist.php?filename='.$row[1].'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                  else {echo '<b><a href="gamelist.php?filename='.$row[1].'&filter='.$filtertype.'&value='.$value.'">'.$gamename.'</a></b></br>';}}
                  else {
                  echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'"><img src="images/'.$image.'"></a></br></br>';
                  if ($row[13] == "Yes"){echo '<b><div class="parent"><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';}
                  else {echo '<b><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></br>';}}
                  echo '</div><br>';
                  break;
              }
         }
     }
   fclose($f);

   if ($i == 0){
      echo '<div><a href="gamelist.php?display=all"></div>NO GAMES FOUND</a></div>';
   }
}

if ($display == "faves" && $filename == null) {
   echo '<div><a href="gamelist.php?display=all"></div><h1>Favourite Games</h1></a></div><br>';
   $i = 0;
   $files = array_values(array_diff(scandir($path), array('.', '..')));
   $f = fopen($csvfile, "r");
   while (($row = fgetcsv($f)) !== false) {
        foreach ($row as $cell) {
              if ((in_array($row[1], $files)) and ($row[13] == "Yes")){
                  echo '<div class="box1">';
                  $i++;
                  $system = $row[0];
                  $filename = $row[1];
                  $image = $row[2];
                  $gamename = $row[4];
                  $mapping = $row[14];
                  $ffb = $row[15];
                  echo '<a id="anchor'.$gamename.'" class="anchors"></a>';
                  if ($menumode == 'advanced'){
                  echo '<a href="gamelist.php?filename='.$row[1].'&display='.$display.'"><img src="images/'.$image.'"></a></br></br>';
                  echo '<b><div class="parent"><a href="gamelist.php?filename='.$row[1].'&display='.$display.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div>';
                  }
                  else {
                  echo '<a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'"><img src="images/'.$image.'"></a></br></br>';
                  echo '<b><div class="parent"><a href="loadcheck.php?rom='.$filename.'&name='.$gamename.'&system='.$system.'&mapping='.$mapping.'&ffb='.$ffb.'">'.$gamename.'</a></b><img src="img/fave.png" class="over-img"/></div></br>';
                  }
                  echo '</div><br>';
                  break;
              }
         }
     }
   fclose($f);

   if ($i == 0){
      echo '<div><a href="gamelist.php?display=all"></div>NO FAVOURITES FOUND</a></div>';
   }
}


echo '</div>';

?>

<script>

function SystemFunction() {
  document.getElementById("SystemDropdown").classList.toggle("show");
  document.getElementById("GenreDropdown").classList.remove("show");
  document.getElementById("OrientationDropdown").classList.remove("show");
  document.getElementById("ControlDropdown").classList.remove("show");
}

function GenreFunction() {
  document.getElementById("SystemDropdown").classList.remove("show");
  document.getElementById("GenreDropdown").classList.toggle("show");
  document.getElementById("OrientationDropdown").classList.remove("show");
  document.getElementById("ControlDropdown").classList.remove("show");

}

function OrientationFunction() {
  document.getElementById("SystemDropdown").classList.remove("show");
  document.getElementById("GenreDropdown").classList.remove("show");
  document.getElementById("OrientationDropdown").classList.toggle("show");
  document.getElementById("ControlDropdown").classList.remove("show");

}

function ControlFunction() {
  document.getElementById("SystemDropdown").classList.remove("show");
  document.getElementById("GenreDropdown").classList.remove("show");
  document.getElementById("OrientationDropdown").classList.remove("show");
  document.getElementById("ControlDropdown").classList.toggle("show");

}

rtnbutton = document.getElementById("rtnBtn");
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 350 || document.documentElement.scrollTop > 350) {
    rtnbutton.style.display = "block";
  } else {
    rtnbutton.style.display = "none";
  }
}

function topFunction() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}

</script>

</p><center>
     
</body>
</html>
