<?php
function statsmain()
{
    if (checkaccess('statistics', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=statshardware">Hardware</a> <br/>';
        echo '<a href="./index.php?do=liststats&groupid=1">List New Accounts</a> <br/>';
		echo '<a href="./index.php?do=liststats&groupid=2">List New Accounts (with at least one login)</a> <br/>';
		echo '<a href="./index.php?do=liststats_retention&groupid=3">Retention time</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&groupid=4">Char Stats: Strength</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&groupid=5">Char Stats: Endurance</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&groupid=6">Char Stats: Agility</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&groupid=7">Char Stats: Intelligence</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&groupid=8">Char Stats: Will</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&groupid=9">Char Stats: Charisma</a> <br/>';
        
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
