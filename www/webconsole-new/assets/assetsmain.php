<?php
function assetsmain()
{
    if (checkaccess('assets', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=assetsnpc">NPCs</a> <br/>';
        echo '<a href="./index.php?do=assetsitem">Items</a> <br/>';

        echo '<hr />';
        echo '<a href="./index.php">Return to main page</a>';
        echo '</div><div class="main">'."\n";
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
