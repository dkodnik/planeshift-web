<?php
function adminmain()
{
    if (checkaccess('admin', 'read'))
    {
        echo '<div class="menu_left">'."\n";
        echo '<a href="./index.php?do=listtips">List Tips</a><br/>'."\n";
        echo '<a href="./index.php?do=viewcommands">View Commands</a><br/>'."\n"; 
        echo '<a href="./index.php?do=viewserveroptions">View Server Options</a><br/>'."\n"; 
        echo '<a href="./index.php?do=listgms">List GMs</a><br/>'."\n";
        echo '<hr/><a href="./index.php">Return to main page.</a>'."\n";
        echo '</div><div class="main">';
  }
  else
  {
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
