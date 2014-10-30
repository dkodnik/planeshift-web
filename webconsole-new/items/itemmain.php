<?php
function itemmain()
{
    if (checkaccess('items', 'read'))
    {
        echo '<div class="menu_left">'."\n";
        echo '<a href="./index.php?do=listitems">List items</a> <br/>'."\n";
		echo '<a href="./index.php?do=compareitems">Compare items</a> <br/>'."\n";
        echo '<a href="./index.php?do=listitemicons">List item icons</a> <br/>'."\n";
        echo '<a href="./index.php?do=finditem">Locate Items</a> <br/>';
        if (checkaccess('items', 'create'))
        {
            echo '<a href="./index.php?do=createitem">Create Item</a> <br/>';
            echo '<a href="./index.php?do=editcategory">Edit Categories</a> <br/>';
        }
        echo '<a href="./index.php">Return to main page.</a>'."\n";
        echo '</div><div class="main">'."\n";
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
