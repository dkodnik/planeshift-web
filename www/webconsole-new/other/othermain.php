<?php
function othermain(){
    if (checkaccess('other', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=listguilds">List guilds and members</a> <br/>';
        echo '<a href="./index.php?do=listpetitions">List petitions</a> <br/>';
        /*
         * These pages still need to be done!
         *
        echo '<a href="./index.php?do=viewaccounts">View accounts</a> <br/>';
        echo '<a href="./index.php?do=viewgms">View/Edit gms</a> <br/>'; // Admins
        
        echo '<a href="./index.php?do=viewcharacters">View characters</a <br/>';
        echo '<a href="./index.php?do=listtraits">List/Edit traits</a> <br/>'; // SysAdmin
        echo '<a href="./index.php?do=listcommonstrings">List/Edit Common Strings</a> <br/>';*/
        
        echo '<a href="./index.php">Return to main page</a>';
        echo '</div><div class="main">'."\n";
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>