<?php
//This is the main crafting page - gives options for displaying/editing crafts

function craftingmain()
{
    if (checkaccess('crafting', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=listcraftitems">List Craftable Items</a> <br/>';
		echo '<a href="./index.php?do=checkminditemusage">Check Mind Item Usage</a> <br/>';
        echo '<a href="./index.php?do=listpatterns">List Patterns</a> <br/>';
        echo '<a href="./index.php?do=listprocess">List Process</a> <br/>';
        if (checkaccess('crafting', 'create'))
        {
            echo '<a href="./index.php?do=createtransform">Create Transform</a> <br/>';
            echo '<a href="./index.php?do=createpattern">Create Pattern</a> <br/>';
        }
        echo '<hr/><a href="./index.php?do=resource">List Natural Resources</a><br/>';
        echo '<a href="./index.php?do=resourcemap">List Resource Map</a><br/>';
        echo '<a href="./index.php?do=huntlocations">List Hunt Locations</a><br/>';
        echo '<hr/><a href="./index.php">Return to main page.</a>';
        echo '</div><div class="main">';
    }
    else
    {
        echo 'You are not authorized to use these functions!';
    }
}
?>


