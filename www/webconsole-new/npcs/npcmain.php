<?php
//This is the main quest page - gives options for displaying/editing quests

function npcmain(){
    if (checkaccess('npcs', 'read'))
    {
        echo '<div class="menu_left">'."\n";
        echo '<a href="./index.php?do=searchnpc&amp;char_type=1">Search for NPCs</a> <br/>'."\n";
        echo '<a href="./index.php?do=searchnpc&amp;char_type=0">Search for Players</a> <br/>'."\n";
        echo '<a href="./index.php?do=searchnpc&amp;char_type=2">Search for Pets</a> <br/>'."\n";
        echo '<a href="./index.php?do=searchnpc&amp;char_type=3">Search for Mounts</a> <br/>'."\n";
        echo '<hr/>'."\n";
        echo '<a href="./index.php?do=createnpc">Create NPC</a> <br/>'."\n";
        echo '<a href="./index.php?do=listnpcs">List NPCs (invuln)</a> <br/>'."\n";
        echo '<a href="./index.php?do=listvuln">List NPCs (vuln)</a> <br/>'."\n";
        echo '<a href="./index.php?do=listnpcscombat">List NPCs (combat view)</a> <br/>'."\n";
        echo '<a href="./index.php?do=listnpcsector">List Invul. NPCs by Sector</a> <br/>'."\n";
        echo '<a href="./index.php?do=viewnpcmap">View NPC Map</a><br/>'."\n";
        echo '<a href="./index.php?do=listnpctypes&amp;template=1">List NPC Type Templates</a> <br/>'."\n";
        echo '<a href="./index.php?do=listnpctypes">List NPC Types</a> <br/>'."\n";
        echo '<hr/>'."\n";
        echo '<a href="./index.php?do=listtrainer">List Trainers</a> <br/>'."\n";
        echo '<a href="./index.php?do=listmerchant">List Merchants</a> <br/>'."\n";
        echo '<a href="./index.php?do=listspawn">List Spawn Rules</a> <br/>'."\n";
        echo '<a href="./index.php?do=listloot">List Loot Rules</a> <br/>'."\n";
        echo '<hr/>';
        echo '<a href="./index.php?do=synonyms">List Synonyms</a> <br/>'."\n";
        echo '<a href="./index.php?do=ka_trigg">List Trigger KAs</a> <br/>'."\n";
        echo '<a href="./index.php?do=ka_scripts">List KA Scripts</a> <br/>'."\n";
        echo '<a href="./index.php?do=findtrigger">Find word in KA</a> <br/>'."\n";
        echo '<hr/>'."\n";
        echo '<a href="./index.php?do=checknpctriggers">Check NPC Triggers</a> <br/>'."\n";
        echo '<a href="./index.php?do=checknpcchar">List NPCs and Base Dialog</a> <br/>'."\n";
        echo '<a href="./index.php?do=checknpcloaded">Check NPC Loaded</a> <br/>'."\n";
        echo '<a href="./index.php?do=checktrainers">Check NPC Trainers</a> <br/>'."\n";
        echo '<hr/><a href="./index.php">Return to main page.</a>'."\n";
        echo '</div><div class="main">'."\n";
    }
    else
    {
        echo 'You are not authorized to use these functions!'."\n";
    }
}
?>


