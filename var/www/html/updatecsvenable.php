<?php

$lcdmode = file_get_contents('/sbin/piforce/lcdmode.txt');
$csvfile = '/var/www/html/csv/romsinfo.csv';
$tempfile = tempnam(".", "tmp"); // produce a temporary file name, in the current directory

if(!$input = fopen($csvfile,'r')){
    die('could not open existing csv file');
}
if(!$output = fopen($tempfile,'w')){
    die('could not open temporary output file');
}

$rom = $_GET['rom'];
$enabled = $_GET['enabled'];

while(($data = fgetcsv($input)) !== FALSE){
    if($data[1] == $rom){
        $data[12] = $enabled;
    }
    fputcsv($output,$data);
}

fflush($input);
fflush($output);
fclose($input);
fclose($output);

$command = escapeshellcmd("sudo python /sbin/piforce/renamecsv.py $tempfile $csvfile $lcdmode");
shell_exec($command);
header ("Location: editgamelist.php");
?>

