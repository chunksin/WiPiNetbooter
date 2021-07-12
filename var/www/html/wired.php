<?php

include 'menu.php';
$wifimode = file_get_contents('/sbin/piforce/wifimode.txt');
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';

if(isset($_POST["submit"]))
{
$ip = $_POST["ip"];
$sm = $_POST["sm"];
$gw = $_POST["gw"];

if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {

 if($error == '')
 {
  if ($gw == ''){
     $gw = 'none';
  }
  $command = escapeshellcmd("sudo python /sbin/piforce/setstatic.py wired '$wifimode' '$ip' '$sm' '$gw'");
  shell_exec($command);
  $error = '<font color="green"><b>Wired Settings Updated</b></font>';
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

if(isset($_POST["dhcp"]))
{

 if($error == '')
 {
  $command = escapeshellcmd("sudo python /sbin/piforce/setdhcp.py wired '$wifimode'");
  shell_exec($command);
  $error = '<font color="green"><b>Wired Settings Updated</b></font>';
  $rebootcommand = escapeshellcmd("sudo python /sbin/piforce/reboot.py");
  shell_exec($rebootcommand . '> /dev/null 2>/dev/null &');
 }
}


echo '<section><center><h1>';
echo '<h1><a href="network.php">Wired Setup</a></h1>';
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
}
else {
echo 'The Pi is currently set up in Home WiFi mode<br><br>';
}
echo 'Use the form below to update the static IP address on the wired interface<br><br>';
echo '<b>NOTE:</b> Do not provide a Gateway unless you are connecting the wired interface to your router<br><br>';
echo 'The Pi will reboot and update the settings.<br><br>';
echo '<div class="box2"><br>';
echo '<form method="post" id="form1">';
echo '<b><label for="ip">IP Address: </label>';
echo '<input type="text" size="10" id="ip" name="ip"><br><br>';
echo '<label for="sm">Subnet Mask: </label>';
echo '<input type="text" size="10" id="sm" name="sm"><br><br>';
echo '<label for="gw">Gateway: </label>';
echo '<input type="text" size="10" id="gw" name="gw"></b><br><br>';
echo '<input type="submit" name="submit" class="dropbtn" value="Apply and Reboot"><br><br></div>';
echo '<br><br>';
echo 'If you would like to change the wired interface to DHCP use the button below<br><br>';
echo 'It is not recommended to run DHCP on wireless and wired at the same time as it may cause connection issues<br><br>';
echo '<input type="submit" class="dropbtn" name="dhcp" value="Wired DHCP">';
echo '</form>';

?>
<br><br>
<?php echo $error; ?>
</p><center></body></html>