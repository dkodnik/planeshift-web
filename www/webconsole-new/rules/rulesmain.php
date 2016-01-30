<?php
function rulesmain()
{
    if (checkaccess('natres', 'read'))
    {
        echo '<div class="menu_left">'."\n";
        echo '<a href="./index.php?do=rulesmap">List Map</a><br/>'."\n";
        echo '<hr/>'."\n";
        echo '<a href="./index.php?do=scripts&amp;type=SI">List SimpleItem Scripts</a> <br/>'."\n";
        echo '<a href="./index.php?do=scripts&amp;type=CG">List CharGen Scripts</a> <br/>'."\n";
        echo '<a href="./index.php?do=scripts&amp;type=S">List Spell Scripts</a> <br/>'."\n";
        echo '<a href="./index.php?do=mscripts">List Math Scripts</a> <br/>'."\n";
        echo '<a href="./index.php?do=scripts&amp;type=A">List Special Attack Scripts</a> <br/>'."\n";
        echo '<a href="./index.php?do=scripts&amp;type=O">List Other Scripts</a> <br/>'."\n";
        echo '<hr/>'."\n";
        echo '<a href="./index.php?do=spells">List Spells</a><br/>'."\n";
        echo '<a href="./index.php?do=listglyph">Glyphs Used</a><br/>'."\n";
        if (checkaccess('spells', 'create'))
        {
            echo '<a href="./index.php?do=createspell">Create Spell</a><br/>'."\n";
        }
        echo '<hr/><a href="./index.php?do=waypoint">List Waypoints</a><br/>'."\n";
        echo '<a href="./index.php?do=waypointalias">List Waypoint Aliases</a><br/>'."\n";
        echo '<a href="./index.php?do=listwaypointlinks">List Waypoint links</a><br/>'."\n";
        echo '<a href="./index.php?do=waypointmap">List Waypoint Map</a><br/>'."\n";
        echo '<a href="./index.php?do=listpathpoints">List Pathpoints</a><br/>'."\n";
        echo '<a href="./index.php?do=location">List Locations</a><br/>'."\n";
        echo '<a href="./index.php?do=locationtype">List Location Types</a><br/>'."\n";
        echo '<a href="./index.php?do=locationmap">List Location Map</a><br/>'."\n";
        echo '<hr/>'."\n";
        echo '<a href="./index.php?do=skills">List Skills</a><br/>'."\n";
        echo '<a href="./index.php?do=factions">List Factions</a><br/>'."\n";
        echo '<a href="./index.php?do=raceinfo">Race Info/Spawn Points</a><br/>'."\n";
        echo '<hr/>'."\n";
        echo '<a href="./index.php?do=listattacks">List Attacks</a><br/>'."\n";
        echo '<a href="./index.php?do=listattacktypes">Attack Types</a><br/>'."\n";
        echo '<a href="./index.php?do=listweapontypes">Weapon Types</a><br/>'."\n";
        echo '<a href="./index.php?do=liststances">List Stances</a><br/>'."\n";
        echo '<a href="./index.php?do=listarmorvsweapon">Armor vs Weapon</a><br/>'."\n";
        echo '<hr/>'."\n";
        echo '<a href="./index.php?do=listlootmodifiers">List Loot Modifiers</a><br/>'."\n";
        echo '<hr/>'."\n";
        echo '<a href="./index.php">Return to main page.</a>'."\n";
        echo '</div><div class="main">'."\n";
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
