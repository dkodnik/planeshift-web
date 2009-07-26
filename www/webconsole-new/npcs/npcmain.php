<?php
//This is the main quest page - gives options for displaying/editing quests

function npcmain(){
  if (checkaccess('npcs', 'read')){
    echo '<div class="menu_left">';
    echo '<a href="./index.php?do=searchnpc">Search for NPCs</a> <br/>';
    echo '<a href="./index.php?do=listnpcs">List NPCs (invuln)</a> <br/>';
    echo '<a href="./index.php?do=listvuln">List NPCs (vuln)</a> <br/>';
    echo '<a href="./index.php?do=listtrainer">List Trainers</a> <br/>';
    echo '<a href="./index.php?do=listmerchant">List Merchants</a> <br/>';
    echo '<a href="./index.php?do=listspawn">List Spawn Rules</a> <br/>';
    echo '<a href="./index.php?do=listloot">List Loot Rules</a> <br/><hr/>';
    echo '<a href="./index.php?do=synonyms">List Synonyms</a> <br/>';
    echo '<a href="./index.php?do=ka_trigg">List Trigger KAs</a> <br/>';
    echo '<a href="./index.php?do=ka_scripts">List KA Scripts</a> <br/>';
    echo '<hr/>';
    echo '<a href="./index.php?do=checknpctriggers">Check NPC Triggers</a> <br/>';
    echo '<a href="./index.php?do=checknpcloaded">Check NPC Loaded</a> <br/>';
    echo '<hr/><a href="./index.php">Return to main page.</a>';
    echo '</div><div class="main">';
  }
  else{
    echo 'You are not authorized to use these functions!';
  }
}
?>


