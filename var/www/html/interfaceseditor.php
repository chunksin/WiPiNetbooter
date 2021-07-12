<?php

include 'menu.php';
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<link rel="stylesheet" href="css/sidebarstyles.css">';
echo '<section><center><p>';

echo '<h1><a href=network.php>Interfaces File Editor</a></h1><br>';

$url = 'interfaceseditor.php';
$interfacesfile = '/etc/network/interfaces';

if (isset($_POST['text']))
{

file_put_contents($interfacesfile, $_POST['text']);
header(sprintf('Location: '.$url));
echo '<b><a href="interfaceseditor.php">File Saved</a></b>';
exit();

}

$text = file_get_contents($interfacesfile);

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