<?
//This is the main crafting page - gives options for displaying/editing crafts

function craftingmain(){
  if (checkaccess('crafting', 'read')){
    echo '<div class="menu_left">';
    echo '<a href="./index.php?do=listpatterns">List Patterns</a> <br/>';
    echo '<hr/><a href="./index.php">Return to main page.</a>';
    echo '</div><div class="main">';
  }
  else{
    echo 'You are not authorized to use these functions!';
  }
}
?>


