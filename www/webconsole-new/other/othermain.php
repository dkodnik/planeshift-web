<?php
function othermain()
{
    if (checkaccess('other', 'read'))
    {
        echo '<div class="menu_left">'."\n";
        echo '<a href="./index.php?do=listguilds">List guilds and members</a> <br/>'."\n";
        echo '<a href="./index.php?do=listpetitions">List petitions</a> <br/>'."\n";
        echo '<a href="./index.php?do=listaccounts">List accounts</a> <br/>'."\n";
        echo '<a href="./index.php?do=listcharacters">List characters</a> <br/>'."\n";
        echo '<a href="./index.php?do=listtraits">List traits</a> <br/>'."\n"; 
        echo '<a href="./index.php?do=showraces">Traits per race</a> <br/>'."\n"; 
        echo '<a href="./index.php?do=characteraffinity">Character affinity</a> <br/>'."\n"; 
        echo '<a href="./index.php?do=charactercreationevents">Character creation events</a> <br/>'."\n"; 
        echo '<a href="./index.php?do=characterlifeevents">Character life events</a> <br/>'."\n"; 
        echo '<a href="./index.php?do=lifeeventrelations">Life event relations</a> <br/>'."\n"; 
        echo '<a href="./index.php?do=events">List GM Events</a><br/>'."\n";
        echo '<a href="./index.php?do=servernews">Server News</a><br/>'."\n";
        //echo '<a href="./index.php?do=listcommonstrings">List Common Strings</a> <br/>'; commented pending a decision on what to do with this since there is no more such table, but the information is still out there.
        
        echo '<hr />'."\n";
        echo '<a href="./index.php">Return to main page</a>'."\n";
        echo '</div><div class="main">'."\n";
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
