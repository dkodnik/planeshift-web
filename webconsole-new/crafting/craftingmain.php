<?php
//This is the main crafting page - gives options for displaying/editing crafts

function craftingmain()
{
    if (checkaccess('crafting', 'read'))
    {
        echo '<div class="menu_left">'."\n";
        echo '<a href="./index.php?do=listcraftitems">List Craftable Items</a> <br/>'."\n";
		echo '<a href="./index.php?do=checkminditemusage">Check Mind Item Usage</a> <br/>'."\n";
        echo '<a href="./index.php?do=listpatterns">List Patterns</a> <br/>'."\n";
        echo '<a href="./index.php?do=listprocess">List Process</a> <br/>'."\n";
        if (checkaccess('crafting', 'create'))
        {
            echo '<a href="./index.php?do=createtransform">Create Transform</a> <br/>'."\n";
            echo '<a href="./index.php?do=createpattern">Create Pattern</a> <br/>'."\n";
        }
        echo '<hr/><a href="./index.php?do=resource">List Natural Resources</a><br/>'."\n";
        echo '<a href="./index.php?do=resourcemap">List Resource Map</a><br/>'."\n";
        echo '<a href="./index.php?do=huntlocations">List Hunt Locations</a><br/>'."\n";
        echo '<hr/><a href="./index.php">Return to main page.</a>'."\n";
        echo '</div><div class="main">'."\n";
    }
    else
    {
        echo 'You are not authorized to use these functions!'."\n";
    }
}
?>


