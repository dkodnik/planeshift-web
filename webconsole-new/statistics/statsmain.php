<?php
function statsmain()
{
    if (checkaccess('statistics', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=statshardware">Hardware Stats</a> <br/>';
        echo '<a href="./index.php?do=liststats&groupid=1">List Stats</a> <br/>';
        
        echo '<hr />';
        echo '<a href="./index.php">Return to main page</a>';
        echo '</div><div class="main">'."\n";
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
