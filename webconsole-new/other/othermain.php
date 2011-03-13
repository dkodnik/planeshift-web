<?php
function othermain()
{
    if (checkaccess('other', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=listguilds">List guilds and members</a> <br/>';
        echo '<a href="./index.php?do=listpetitions">List petitions</a> <br/>';
        echo '<a href="./index.php?do=listaccounts">List accounts</a> <br/>';
        echo '<a href="./index.php?do=listcharacters">List characters</a> <br/>';
        echo '<a href="./index.php?do=liststats&amp;groupid=1">List Stats</a> <br/>';
        echo '<a href="./index.php?do=listtraits">List traits</a> <br/>'; 
        echo '<a href="./index.php?do=showraces">Traits per race</a> <br/>'; 
        echo '<a href="./index.php?do=events">List GM Events</a><br/>';
        echo '<a href="./index.php?do=events">List GM Events</a><br/>';
        //echo '<a href="./index.php?do=listcommonstrings">List Common Strings</a> <br/>'; commented pending a decision on what to do with this since there is no more such table, but the information is still out there.
        
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
