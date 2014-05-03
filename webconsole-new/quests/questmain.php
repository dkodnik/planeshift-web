<?php
//This is the main quest page - gives options for displaying/editing quests

function questmain(){
  if (checkaccess('quests', 'read')){
    echo '<div class="menu_left">'."\n";
    echo '<a href="./index.php?do=listquests">List Quests</a> <br/>'."\n";
    if (checkaccess('quests', 'create')){
      echo '<a href="./index.php?do=createquest">Create Quest</a> <br/>'."\n";
    }
    echo '<a href="./index.php?do=npcquests">List By NPC</a> <br/>'."\n";
    echo '<a href="./index.php">Return to main page.</a>'."\n";
    echo '</div><div class="main">'."\n";
  }
  else{
    echo 'You are not authorized to use these functions!'."\n";
  }
}
?>


