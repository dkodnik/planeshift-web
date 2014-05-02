<?php
function rulesmain()
{
    if (checkaccess('natres', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=rulesmap">List Map</a><br/>';
        echo '<hr/>';
        echo '<a href="./index.php?do=scripts&amp;type=SI">List SimpleItem Scripts</a> <br/>';
        echo '<a href="./index.php?do=scripts&amp;type=CG">List CharGen Scripts</a> <br/>';
        echo '<a href="./index.php?do=scripts&amp;type=S">List Spell Scripts</a> <br/>';
        echo '<a href="./index.php?do=mscripts">List Math Scripts</a> <br/>';
        echo '<a href="./index.php?do=scripts&amp;type=O">List Other Scripts</a> <br/>';
        echo '<hr/>';
        echo '<a href="./index.php?do=spells">List Spells</a><br/>';
        echo '<a href="./index.php?do=listglyph">Glyphs Used</a><br/>';
        if (checkaccess('spells', 'create'))
        {
            echo '<a href="./index.php?do=createspell">Create Spell</a><br/>';
        }
        echo '<hr/><a href="./index.php?do=waypoint">List Waypoints</a><br/>';
        echo '<a href="./index.php?do=waypointalias">List Waypoint Aliases</a><br/>';
        echo '<a href="./index.php?do=listwaypointlinks">List Waypoint links</a><br/>';
        echo '<a href="./index.php?do=waypointmap">List Waypoint Map</a><br/>';
        echo '<a href="./index.php?do=listpathpoints">List Pathpoints</a><br/>';
        echo '<a href="./index.php?do=location">List Locations</a><br/>';
        echo '<a href="./index.php?do=locationtype">List Location Types</a><br/>';
        echo '<a href="./index.php?do=locationmap">List Location Map</a><br/>';
        echo '<hr/>';
        echo '<a href="./index.php?do=skills">List Skills</a><br/>';
        echo '<a href="./index.php?do=factions">List Factions</a><br/>';
        echo '<a href="./index.php?do=raceinfo">Race Info/Spawn Points</a><br/>';
        echo '<hr/>';
        echo '<a href="./index.php?do=listattacktypes">Attack Types</a><br/>';
        echo '<a href="./index.php?do=listweapontypes">Weapon Types</a><br/>';
        echo '<a href="./index.php?do=liststances">List Stances</a><br/>';
        echo '<a href="./index.php?do=listarmorvsweapon">Armor vs Weapon</a><br/>';
        echo '<hr/>';
        echo '<a href="./index.php?do=listlootmodifiers">List Loot Modifiers</a><br/>';
        echo '<hr/>';
        echo '<a href="./index.php">Return to main page.</a>';
        echo '</div><div class="main">';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
