<?php
function statsmain()
{
    if (checkaccess('statistics', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=statshardware">Hardware</a> <br/>';
        echo '<a href="./index.php?do=liststats&amp;groupid=1">New Accounts</a> <br/>';
		echo '<a href="./index.php?do=liststats&amp;groupid=2">New Accounts (with at least one login)</a> <br/>';
		echo '<a href="./index.php?do=liststats&amp;groupid=17">New Characters</a> <br/>';
		echo '<a href="./index.php?do=liststats&amp;groupid=18">New Characters (with time>0)</a> <br/>';
		echo '<a href="./index.php?do=liststats_retention&amp;groupid=3">Retention time</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=4">Char Stats: Strength</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=5">Char Stats: Endurance</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=6">Char Stats: Agility</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=7">Char Stats: Intelligence</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=8">Char Stats: Will</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=9">Char Stats: Charisma</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=10">Skill: Sword</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=11">Skill: Light Armor</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=12">Skill: Medium Armor</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=13">Skill: Heavy Armor</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=14">Skill: Crystal Way</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=15">Skill: Melee</a> <br/>';
		echo '<a href="./index.php?do=liststats_charstats&amp;groupid=16">Char Money</a> <br/>';

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
