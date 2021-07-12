<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';

?>

<h1><a href="setup.php">Firmware Update</a></h1>
<p>This feature will update your Type 1 Netdimm's firmware code</p>
<p>There are three versions available:</p>
<p><b>4.01</b> - this enables support for loading games using Sega's CF card adapter and cards<br /><b>4.02</b> - this enables support for loading games from third party CF cards<br /><b>4.03</b> - this enables support for loading games from CD-R media<br />
<p><b>IMPORTANT</b> - first check which firmware version you are running, you can check this from the Naomi service menu<br />
<p><b>If you are running version 3.03 or 3.17 you need to update to version 4.01 *before* you update to version 4.02</b><br />
<p><b>If you do not need to update, it is always safer not to! If you lose power during the update you may break your Netdimm</b><br />
<p><b>If you wish to continue and update, reboot your Naomi so that it is waiting on the 'CHECKING NETWORK' screen</b><br />
<p><b>Once the update has been sent to the Netdimm, the Naomi will display an update prompt on the monitor</b><br />

<?php

function pinger($address){
        $command = "fping -c1 -t500 $address";
        exec($command, $output, $status);
        if($status === 0){
            return true;
        }else{
            return false;
        }
    }

$f = fopen("csv/dimms.csv", "r");
$headers = ($row = fgetcsv($f));
$i=1;
$row = fgetcsv($f);
if ($i == 1 and $row[1] == null){
echo '<b><div class="offline">No Netdimms Configured</div></b><br>';
echo '<h1><a href="dimms.php" class="dropbtn">Manage Netdimms</a></h1>';}
else{
rewind($f);
$headers = ($row = fgetcsv($f));
echo '<p>Select Dimm to update:';
while (($row = fgetcsv($f)) !== false) {
echo '<div class="box1">';
echo '<html><body><table class="center" id="dimms">';

        echo "<tr>";
        foreach ($row as $cell) {

                echo '<td><b>Name</b></td>';
                echo '<td>'.$row[0].'</td>';
                echo '<tr><td><b>IP Address</b></td>';
                if (pinger($row[1]) == true){
                echo '<form action="fwupdateconfirm.php" method="post">';
                echo '<td><b><span class="online">'.$row[1].' (ONLINE)</span></b></td>';
                echo '<input type="hidden" name="ip" value="'.$row[1].'" />';
                echo '<tr><td><b>Type<b></td>';
                echo '<td>'.$row[2].'</td></tr>';
                echo '<tr><td><b>Version</b></td><td><select name="version"><option value="4.01">4.01</option><option value="4.02">4.02</option><option value="4.03">4.03</option></select></td></tr>';
                echo '</table><br>';
                echo '<input type="submit" class="dropbtn" value="Update"></form>';
                }
                else {
                echo '<td><b><span class="offline">'.$row[1].' (OFFLINE)</span></b></td>';
                echo '<tr><td><b>Type<b></td>';
                echo '<td>'.$row[2].'</td></tr>';
                echo '</table>';
                echo '<br><form action="fwupdate.php">';
                echo '<button type="submit" class="dropbtn" value="Retry">Retry</button></form>';
                }
                $i++;
                break;
        }
        echo "</tr></table></div><br>";
}}
fclose($f);

?>