<?php

include 'menu.php';
include 'wifilist.php';
$wifimode = file_get_contents('/sbin/piforce/wifimode.txt');
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

if(isset($_POST["submit"]))
{

$ip = $_POST["ip"];
$sm = $_POST["sm"];
$gw = $_POST["gw"];

if($ip != ''){
if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
  $command = escapeshellcmd("sudo python /sbin/piforce/setstatic.py wireless '$wifimode' '$ip' '$sm' '$gw'");
  shell_exec($command);
}
else{
  $error .= '<font color="red"><b>IP Address is invalid</b></font><br>';
}
}

 if(empty($_POST["manssid"]))
 {
  $ssid = $_POST['ssid'];
 }
 else
 {
  $ssid = $_POST['manssid'];
 }
 if(empty($_POST["psk"]))
 {
  $error .= '<font color="red"><b> Password is required</b></font>';
 }
 else if(strlen($_POST["psk"]) < 8)
 {
  $error .= '<font color="red"><b> Password must be at least 8 characters</b></font><br>';
 }
 else
 {
  $psk = $_POST['psk'];
 }

 if($error == '')
 {
  $wificommand = escapeshellcmd("sudo python /sbin/piforce/wificopy.py '$ssid' '$psk'");
  shell_exec($wificommand);
  $error = '<font color="green"><b>Wifi Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = escapeshellcmd("sudo python /sbin/piforce/reboot.py");
  shell_exec($rebootcommand . '> /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}

if(isset($_POST["hotspotsubmit"]))
{
 if(empty($_POST["manssid"]))
 {
  $ssid = $_POST['ssid'];
 }
 else
 {
  $ssid = $_POST['manssid'];
 }
 if(empty($_POST["psk"]))
 {
  $error .= '<font color="red"><b> Password is required</b></font>';
 }
 else if(strlen($_POST["psk"]) < 8)
 {
  $error .= '<font color="red"><b> Password must be at least 8 characters</b></font><br>';
 }
 else
 {
  $psk = $_POST['psk'];
 }

 if($error == '')
 {
  $command = escapeshellcmd("sudo python /sbin/piforce/hotspotupdate.py '$ssid' '$psk'");
  shell_exec($command);
  $error = '<font color="green"><b>HotSpot Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = escapeshellcmd("sudo python /sbin/piforce/reboot.py");
  shell_exec($rebootcommand . '> /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}

if(isset($_POST["homesubmit"]))
{
 if(empty($_POST["manssid"]))
 {
  $ssid = $_POST['ssid'];
 }
 else
 {
  $ssid = $_POST['manssid'];
 }
 if(empty($_POST["psk"]))
 {
  $error .= '<font color="red"><b> Password is required</b></font>';
 }
 else if(strlen($_POST["psk"]) < 8)
 {
  $error .= '<font color="red"><b> Password must be at least 8 characters</b></font><br>';
 }
 else
 {
  $psk = $_POST['psk'];
 }

 if($error == '')
 {
  $command = escapeshellcmd("sudo python /sbin/piforce/homeupdate.py '$ssid' '$psk'");
  shell_exec($command);
  $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = escapeshellcmd("sudo python /sbin/piforce/reboot.py");
  shell_exec($rebootcommand . '> /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}

if(isset($_POST["static"]))
{
$ip = $_POST["ip"];
$sm = $_POST["sm"];
$gw = $_POST["gw"];

if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {

 if($error == '')
 {
  $command = escapeshellcmd("sudo python /sbin/piforce/setstatic.py wireless '$wifimode' '$ip' '$sm' '$gw'");
  shell_exec($command);
  $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = escapeshellcmd("sudo python /sbin/piforce/reboot.py");
  shell_exec($rebootcommand . '> /dev/null 2>/dev/null &');
  $ssid = '';
  $psk = '';
 }
}
else{
  $error .= '<font color="red"><b>IP Address is invalid</b></font><br>';
}
}

if(isset($_POST["wifidhcp"]))
{

 if($error == '')
 {
  $command = escapeshellcmd("sudo python /sbin/piforce/setdhcp.py wireless &");
  shell_exec($command);
  $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = escapeshellcmd("sudo python /sbin/piforce/reboot.py");
  shell_exec($rebootcommand . '> /dev/null 2>/dev/null &');
 }
}

if(isset($_POST["hotspotrestore"]))
{

 if($error == '')
 {
  $command = escapeshellcmd("sudo python /sbin/piforce/hotspotwifi.py &");
  shell_exec($command);
  $error = '<font color="green"><b>WiFi Settings Updated<br>Rebooting ...</b></font>';
  $rebootcommand = escapeshellcmd("sudo python /sbin/piforce/reboot.py");
  shell_exec($rebootcommand . '> /dev/null 2>/dev/null &');
 }
}


echo '<section><center><h1>';
echo '<h1><a href="network.php">WiFi Setup</a></h1>';
$wiredip = `ip -o -f inet addr show | awk '/eth0/ {print $4}'`;
$wirelessip = `ip -o -f inet addr show | awk '/wlan0/ {print $4}'`;
$wiredstatus =  `ip -o -f inet addr show | awk '/eth0/ {print $9}'`;
$wirelessstatus = `ip -o -f inet addr show | awk '/wlan0/ {print $9}'`;
$ssid = `iwgetid -r`;
if ($wiredstatus == "dynamic\n"){$wiredtype = "DHCP";}else{$wiredtype = "Static";}
if ($wirelessstatus == "dynamic\n"){$wirelesstype = "DHCP";}else{$wirelesstype = "Static";}
echo 'Wireless IP: <b>'.$wirelessip.' ('.$wirelesstype.')</b><br>';
echo 'Wired IP: <b>'.$wiredip.' ('.$wiredtype.')</b><br><br>';
if ($wifimode == 'hotspot'){
echo 'Current Wifi Mode: <b>HotSpot</b><br><br>';}
else {echo 'Current Wifi Mode: <b>Home WiFi</b><br>Current SSID: <b>'.$ssid.'</b><br><br>';}
if ($wifimode == 'hotspot'){
echo 'The Pi is currently set up in HotSpot mode broadcasting its own WiFi network<br><br>';
echo 'Use the form below to enter your home network details then press the Apply button to disable HotSpot Mode<br><br>';
echo 'Either choose your Wifi SSID from the drop down, or manually type it into the box<br><br>';
echo 'The Pi will reboot and connect to your home WiFi network<br><br>You also can provide a static IP address for your home network or leave blank for DHCP<br><br>Hotspot mode can be restored later<br><br>';

echo '<form method="post" id="form1">';
echo '<div class="box2"><br>';
echo '<b><label for="ssid">WiFi SSID: </label>';
echo '<select name="ssid">';
  for ($i = 1; $i <= $ssids; $i++) {
  $name = ${'name'.$i};
  echo '<option value="'.$name.'">'.$name.'</option>';}
echo '</select><br><br>';
echo '<label for="manssid">WiFi SSID: </label>';
echo '<input type="text" size="10" id="manssid" name="manssid"><br><br>';
echo '<label for="psk">Password: </label>';
echo '<input type="text" size="10" id="psk" name="psk"><br><br>';
echo '===OPTIONAL===<br><br>';
echo '<label for="psk">IP Address: </label>';
echo '<input type="text" size="10" id="ip" name="ip"><br><br>';
echo '<label for="psk">Subnet Mask: </label>';
echo '<input type="text" size="10" id="sm" name="sm"><br><br>';
echo '<label for="psk">Gateway: </label>';
echo '<input type="text" size="10" id="gw" name="gw"></b><br><br>';
echo '<input type="submit" name="submit" class="dropbtn" value="Apply and Reboot">';
echo '</form></div><br><br>';
echo 'If you would like to change the default HotSpot settings use the form below<br><br>';
echo '<form action="wifi.php" method="post" id="form2">';
echo '<div class="box2"><br>';
echo '<b><label for="ssid">HotSpot SSID: </label>';
echo '<input type="text" size="10" id="ssid" name="ssid"><br><br>';
echo '<label for="psk">New Password: </label>';
echo '<input type="text" size="10" id="psk" name="psk"></b><br><br>';
echo '<input type="submit" class="dropbtn" name="hotspotsubmit" value="Apply and Reboot"></div></form>';

} else{
echo 'The Pi is currently set up in Home WiFi mode<br><br>';
echo 'To change your home network details use the form below and press Apply<br><br>';
echo 'Either choose your Wifi SSID from the drop down or manually type it into the box<br><br>';
echo 'The Pi will reboot and connect to your new home WiFi network<br><br>';
echo '<div class="box2"><br>';
echo '<form action="wifi.php" method="post" id="form1">';
echo '<b><label for="ssid">WiFi SSID: </label>';
echo '<select name="ssid">';
  for ($i = 1; $i <= $ssids; $i++) {
  $name = ${'name'.$i};
  echo '<option value="'.$name.'">'.$name.'</option>';}
echo '</select><br><br>';
echo '<label for="manssid">WiFi SSID: </label>';
echo '<input type="text" size="10" id="manssid" name="manssid"><br><br>';
echo '<label for="psk">Password: </label>';
echo '<input type="text" size="10" id="psk" name="psk"></b><br><br>';
echo '<input type="submit" class="dropbtn" name="homesubmit" value="Apply and Reboot"><br><br></div>';
echo '<br><br>';

echo 'Use the form below to set or update the static IP address on the wireless interface<br><br>';
echo 'The Pi will reboot and update the settings<br><br>';
echo '<div class="box2"><br>';
echo '<form method="post" id="form1">';
echo '<b><label for="ip">IP Address: </label>';
echo '<input type="text" size="10" id="ip" name="ip"><br><br>';
echo '<label for="sm">Subnet Mask: </label>';
echo '<input type="text" size="10" id="sm" name="sm"><br><br>';
echo '<label for="gw">Gateway: </label>';
echo '<input type="text" size="10" id="gw" name="gw"></b><br><br>';
echo '<input type="submit" name="static" class="dropbtn" value="Apply and Reboot"><br><br></div>';
echo '<br><br>';

if ($wirelesstype == "Static"){
echo 'To return to DHCP mode use the button below<br><br>';
echo '<input type="submit" class="dropbtn" name="wifidhcp" value="Wireless DHCP">';
echo '<br><br>';
}

echo 'To return to hotspot mode use the button below<br><br>';
echo '<input type="submit" class="dropbtn" name="hotspotrestore" value="Hotspot Mode">';
echo '</form>';

}

?>
<br><br>
<?php echo $error; ?>
</p><center></body></html>