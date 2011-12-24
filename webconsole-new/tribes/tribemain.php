<?php
//This is the main tribe page - gives options for displaying/editing tribes

function tribemain()
{
    if (checkaccess('npcs', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=listtribes">List Tribes</a> <br/>';
        echo '<a href="./index.php?do=listrecipes">List Recipes</a> <br/>';
        echo '</div><div class="main">';
    }
    else
    {
        echo 'You are not authorized to use these functions!';
    }
}
?>


