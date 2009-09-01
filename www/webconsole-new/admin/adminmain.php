<?php
function adminmain()
{
    if (checkaccess('admin', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php?do=listtips">List Tips</a><br/>';
        echo '<a href="./index.php?do=viewcommands">View Commands</a><br/>'; 
        echo '<a href="./index.php?do=viewserveroptions">View Server Options</a><br/>'; 
        echo '<a href="./index.php?do=listgms">List GMs</a><br/>';
        echo '<hr/><a href="./index.php">Return to main page.</a>';
        echo '</div><div class="main">';
  }
  else
  {
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
