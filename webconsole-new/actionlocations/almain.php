<?php
function almain(){
  if (checkaccess('als', 'read')){
    echo '<div class="menu_left">';
    echo '<a href="./index.php?do=alsector">Action Locations</a> <br/>';
    echo '<a href="./index.php?do=checkbooks">Check Books</a> <br/>';
    echo '<a href="./index.php">Return to main page</a>';
    echo '</div><div class="main">'."\n";
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>