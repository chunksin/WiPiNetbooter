<?php
echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter</title>';
echo '<meta name="description" content="Responsive Header Nav">';
echo '<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1">';
?>

<div id="myNav" class="overlay">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <div class="overlay-content">
    <a href="gamelist.php?display=all">Game List</a>
    <a href="gamelist.php?display=faves">Favourites</a>
    <a href="options.php">Options</a>
    <a href="setup.php">Setup</a><br>
    <a href="shutdown.php">Shutdown</a>
  </div>
</div>

<br>

<span style="font-size:20px;cursor:pointer" onclick="openNav()">&#9776; Menu</span>

<script>
function openNav() {
  document.getElementById("myNav").style.width = "100%";
}

function closeNav() {
  document.getElementById("myNav").style.width = "0%";
}
</script>

<script type="text/javascript" src="../package/jquery.min.js"></script>
<script type="text/javascript">
var clicked = false, clickY;
$(document).on({
    'mousemove': function(e) {
        clicked && updateScrollPos(e);
    },
    'mousedown': function(e) {
        clicked = true;
        clickY = e.pageY;
    },
    'mouseup': function() {
        clicked = false;
        $('html').css('cursor', 'auto');
    }
});

var updateScrollPos = function(e) {
    $('html').css('cursor', 'row-resize');
    $(window).scrollTop($(window).scrollTop() + (clickY - e.pageY));
}

</script>