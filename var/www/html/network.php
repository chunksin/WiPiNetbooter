<?php

echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="Responsive Header Nav">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
include 'menu.php';
$wifimode = file_get_contents('/sbin/piforce/wifimode.txt');
?>
<section><center>
<h1><a href="setup.php">Network Setup</a></h1><br>
<form action="scanning.php"><button type="submit" class="dropbtn" value="Cancel">Wifi Setup</button> <a href="wired.php" style="font-weight:normal" class="dropbtn">Wired Setup</a></form>
<br><br>
<?php 
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
?>
The WiPi Netbooter supports multiple network configurations detailed below<br><br>
You can use the Wifi and Wired Setup options to customise the setup for your preferred configuration<br><br>
When the Pi is joined to your home Wifi network you can browse to it from IOS, Linux and Windows 10 devices using the URL <b>http://netbooter.local</b><br><br>
If you are running Android you need to use a network scanner app to locate it, Fing is recommended<br><br>
<b>NOTE:</b> If at any time the Pi becomes unavailable due to a network change it can be reset to the default hotspot mode by creating a file called <b>reset.txt</b> in the boot partition of the SD card and the Pi booted up<br><br>
For advanced setups you can directly edit the network interfaces file <a href="interfaceseditor.php">here</a><br><br>
<b>Default Configuration</b><br><br>
The default configuration is <b>Hotspot Direct</b><br><br>
<img src="img/hsdirect.png" id="largeimg"><br><br>
The Pi broadcasts a wireless network and the wired interface is set to use an IP address of <b>10.0.0.1</b><br><br>
The Netdimm connected to the Pi should be configured to run on <b>10.0.0.2</b><br><br>
The Pi wireless address is <b>192.168.42.1</b><br><br>
Connection is direct to the Pi and to the Netdimm so there is no need for a router or switch<br><br>
<b>Home Wifi Direct</b><br><br>
<img src="img/homedirect.png" id="largeimg"><br><br>
In this mode the Pi is connected via Wifi to your home router and the Pi is directly connected to the Netdimm using <b>10.0.0.1</b><br><br>
This mode is useful if you don't want to change Wifi networks each time you use the Netbooter, the downside is you need to locate your Pi on your home network<br><br>
If you know your home IP address range you can set a static IP address on the Pi<br><br>
<b>Hotspot Router Mode</b><br><br>
<img src="img/hsrouter.png" id="largeimg"><br><br>
The Pi broadcasts a wireless network and is connected via the wired interface to your home router<br><br>
The Netdimm also needs to be connected to your home network so the Pi can netboot games<br><br>
You can use a static or DHCP address for both the Pi and the Netdimm, using static IP addresses is highly recommended<br><br>
One advantage with this setup is that the Pi can be reached via the home router network as well as the hotspot<br><br>
<b>Home Wifi Router Mode</b><br><br>
<img src="img/homerouter.png" id="largeimg"><br><br>
In this mode the Pi is connected via Wifi to your home router and the Netdimm is connected via a network cable<br><br>
This mode is useful if you don't want to change Wifi networks each time you use the Netbooter, the downside is you need to locate your Pi on your home network<br><br>
If you know your home IP address range you can set a static IP address on the Pi<br><br>
A static address is also recommended for the Netdimm<br><br>