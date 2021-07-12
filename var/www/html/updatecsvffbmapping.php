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

$posted = $_POST['mapping'];

$pieces = explode('#', $posted);
$rom = $pieces[0];
$mapping = $pieces[1];

while(($data = fgetcsv($input)) !== FALSE){
    if($data[1] == $rom){
        $data[15] = $mapping;
    }
    fputcsv($output,$data);
}

fflush($input);
fflush($output);
fclose($input);
fclose($output);

$command = escapeshellcmd("sudo python /sbin/piforce/renamecsv.py $tempfile $csvfile $lcdmode");
shell_exec($command);
header ("Location: editffbmappings.php");
?>

