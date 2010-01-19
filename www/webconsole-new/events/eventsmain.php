<?php

function eventsmain()
{
    if (checkaccess('events', 'read'))
    {
        echo '<div class="menu_left">';
        echo '<a href="./index.php">Return to main page.</a>';
        echo '</div><div class="main">';
  }
  else
  {
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

?>