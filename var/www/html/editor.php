<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';

$mappingfile = $_GET["mappingfile"];
$devicefile = $_GET["devicefile"];
$mode = $_GET["mode"];

if ($mappingfile != ''){echo '<h1>Mapping File Editor</h1><br>';}
else {echo '<h1>Device File Editor</h1><br>';}

$url = 'editor.php';

if ($mappingfile != ''){
if ($mode == "jvs"){echo '<a href="mapping.php">Return to Mapping Files</a><br><br>';}
if ($mode == "ffb"){echo '<a href="ffbmapping.php">Return to Mapping Files</a><br><br>';}
}
else {echo '<a href="devices.php">Return to Device Files</a><br><br>';}

if (isset($_POST['text']))
{

if ($mappingfile != ''){file_put_contents($mappingfile, $_POST['text']);}
else {file_put_contents($devicefile, $_POST['text']);}
header(sprintf('Location: %s', $url));
echo '<b>File Saved</b>';
exit();

}

if ($mappingfile != ''){$text = file_get_contents($mappingfile);}
else {$text = file_get_contents($devicefile);}

?>

<style type="text/css">
textarea {
width: 500px;
height: 30em;
}

</style>
<!-- HTML form -->
<form action="" method="post">
<textarea name="text"><?php echo htmlspecialchars($text) ?></textarea><br><br>
<input type="submit" class="dropbtn" value="Submit" />
<input type="reset" class="dropbtn" />
</form>