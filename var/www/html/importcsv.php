<?php

$csvfile = '/var/www/html/csv/romsinfo.csv';
$newfile = '/boot/config/romsinfo.csv';

$command = escapeshellcmd("sudo python /sbin/piforce/importcsv.py $newfile $csvfile");
shell_exec($command);
header ("Location: options.php");
?>

