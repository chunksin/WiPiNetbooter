<?php
include 'auditscanresults.php';
$lcdmode = file_get_contents('/sbin/piforce/lcdmode.txt');
$csvfile = 'csv/romsinfo.csv';
$rompath = '/boot/roms/';
$tempfile = tempnam(".", "tmp"); // produce a temporary file name, in the current directory

header("Refresh: 4; url=gamelist.php?display=all");
include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo 'Updating rom names to game list ....';
echo '</p><center></body></html>';

if(!$input = fopen($csvfile,'r')){
    die('could not open existing csv file');
}
if(!$output = fopen($tempfile,'w')){
    die('could not open temporary output file');
}

if ($_GET["rename"] == 'yes') {

for ($x = 1; $x <= $successes; $x++) {
  $oldname = $rompath.${'filename'.$x};
  $newname = $rompath.${'auditname'.$x};
  $renamecmd = escapeshellcmd("sudo python /sbin/piforce/renamecsv.py $oldname $newname $lcdmode");
  shell_exec($renamecmd . '> /dev/null 2>/dev/null &');
}

$i = 1;
while(($data = fgetcsv($input)) !== FALSE){
    $namecheck = ${'auditname'.$i};
    $filename = ${'filename'.$i};
    if ($data[17] == $namecheck) {
        $data[1] = $namecheck;
	if ($i < $successes){$i++;}
    }
    fputcsv($output,$data);
}

fflush($input);
fflush($output);
fclose($input);
fclose($output);

$command = escapeshellcmd("sudo python /sbin/piforce/renamecsv.py $tempfile $csvfile $lcdmode");
shell_exec($command);
}
 
else{
$i = 1;
while(($data = fgetcsv($input)) !== FALSE){
    $namecheck = ${'auditname'.$i};
    $filename = ${'filename'.$i};
    if ($data[17] == $namecheck) {
        $data[1] = $filename;
	if ($i < $successes){$i++;}
    }
    fputcsv($output,$data);
}

fflush($input);
fflush($output);
fclose($input);
fclose($output);

$command = escapeshellcmd("sudo python /sbin/piforce/renamecsv.py $tempfile $csvfile $lcdmode");
shell_exec($command);
}

?>