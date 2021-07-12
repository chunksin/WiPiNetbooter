<?php
header ("Location: index.html");
echo '<html><body><p align="center"><font face="verdana" color="grey">';
echo '</br>';

$command = escapeshellcmd('sudo python /sbin/piforce/reboot.py');
shell_exec($command . '> /dev/null 2>/dev/null &');
?>