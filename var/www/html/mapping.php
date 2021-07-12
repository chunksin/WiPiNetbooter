<?php
include 'menu.php';
include 'devicelist.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';
echo '<h1><a href="openjvs.php">OpenJVS Mapping File Management</a></h1>';
echo '<table class="center" id="options">';
echo '<tr><th>Mapping File</th><th>Actions</th></tr>';

if ($_GET["command"] == 'delete') {
$deletefile = $_GET["filetodelete"];
$command = escapeshellcmd("sudo python /sbin/piforce/delete.py $deletefile");
shell_exec($command);
header ("Location: mapping.php");
}

if(isset($_POST["submit"]))
{
 if(empty($_POST["name"]) || $_POST["name"] == 'New Mapping File')
 {
  $error .= '<label class="text-danger">Filename is required</label>';
 }
 else
 {
  $filename = strtolower($_POST["name"]);
  $result = str_replace(" ", "-", $filename);
 }

 if($error == '')
 {
  $newfile = fopen('/etc/openjvs/games/'.$result, "w");
  fclose($newfile);
  echo "<meta http-equiv='refresh' content='1'>";
  $error = '<label class="text-success">Entry Added Successfully</label>';
  $name = '';
 }
}


$command = escapeshellcmd("sudo python /sbin/piforce/mappingfiles.py");
shell_exec($command);

$mappingfiles = scandir('/etc/openjvs/games');

for ($i = 2; $i < count($mappingfiles); $i++) {

$mappingfilename = $mappingfiles[$i];
$mappingfilepath = '/etc/openjvs/games/'.$mappingfilename;

echo '<tr>';
echo '<td>'.$mappingfilename.'</td>';
echo '<td><a href="editor.php?mode=jvs&mappingfile='.$mappingfilepath.'">edit</a> / <a href="mapping.php?command=delete&filetodelete='.$mappingfilepath.'">delete</a></td>';
echo '</tr>';
}

echo '<tr><form method="post">';
echo '<td><input type="text" name="name" onfocus="this.value=\'\'" placeholder="Enter File Name" class="form-control" value="" /></td>';
echo '<td><input type="submit" name="submit" class="btn btn-info" value="Add Entry" /></td></form></tr></table>';

echo '</p><center></body></html>';
?>