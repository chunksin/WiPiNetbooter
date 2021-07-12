<?php
ini_set('zlib.output_compression', false);
while (@ob_end_flush());
ini_set('implicit_flush', true);
ob_implicit_flush(true);
set_time_limit(0);
header("Cache-Control: no-cache");
header("Pragma: no-cache");
function scan_target(){
        $ipranges = `ip -o -f inet addr show | awk '/scope global/ {print $4}'`;
        $scanranges = explode("\n",rtrim($ipranges));
        echo '<br><b>'.count($scanranges).' IP ranges detected<br><br></b>';
        foreach($scanranges as $scanrange){
        echo 'Scanning '.$scanrange.' for dimms';
	$cmd = escapeshellcmd("nmap --open -oG /sbin/piforce/nmap.txt -p10703 $scanrange");
	echo '<pre>';
	$a = popen($cmd, 'r'); 
	while($b = fgets($a, 4096)) { 
		echo $b;
		flush(); 
	}
	echo '</pre>';
        $results = `cat /sbin/piforce/nmap.txt | awk '/open/ {print $2}'`;
        $dimms = explode("\n",rtrim($results));
        if (count($dimms) == 1){
           echo '<br><span class="offline"><b>No dimms found on this network</span></b><br><br>';
        } else {
        foreach($dimms as $dimm){
           if ($dimm != "Nmap"){
             echo '<b><span class="online">Netdimm found at '.$dimm.'</span></b><br>';
             $handle = fopen("csv/dimms.csv", "a");
             $line = array("Netdimm",$dimm,"Sega Naomi");
             fputcsv($handle, $line);
             fclose($handle);
           }
        echo '<br>';
        }}}
	pclose($a); 
}
?>
<html><head><title>WiPi Netbooter</title></head><body>	
<?php
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="setup.php">Netdimm Scanner</a></h1>';
echo 'This tool will scan the network for netdimms<br><br>';
echo 'It will scan both the wired and wireless networks returning the IP address of any devices found and adding them automatically<br><br>';

echo '<h1><a href="dimmscanner.php?scan=start" class="dropbtn">Start Scan</a></form></h1>';
if ($_GET['scan'] == "start"){
   scan_target();
echo '<br><h1><a href="dimms.php" class="dropbtn">Manage Netdimms</a></h1>';
}
?>
</body></html>