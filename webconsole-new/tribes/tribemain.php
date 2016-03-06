<?php
//This is the main tribe page - gives options for displaying/editing tribes

function tribemain()
{
    if (checkaccess('npcs', 'read'))
    {
        echo '<div class="menu_left">'."\n";
        echo '<a href="./index.php?do=listtribes">List Tribes</a> <br/>'."\n";
        echo '<a href="./index.php?do=listrecipes">List Recipes</a> <br/>'."\n";
        echo '<a href="./index.php?do=listtribemembers">List Tribe Members</a> <br/>'."\n";
        echo '</div><div class="main">'."\n";
    }
    else
    {
        echo 'You are not authorized to use these functions!'."\n";
    }
}
?>