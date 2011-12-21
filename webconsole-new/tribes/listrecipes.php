<?php
function listrecipes(){
  if (checkaccess('npcs', 'read')){
    echo '<table border="1">';
    echo '<tr><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=id">ID</a></th><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=name">Name</a></th><th>Requirements</th><th>Algorithm</th><th>Persistent</th><th>Uniqueness</th></tr>';
    $query = 'SELECT r.* FROM tribe_recipes AS r';

    if (isset($_GET['sort'])){
      if ($_GET['sort'] == 'id'){
        $query = $query . ' ORDER BY id';
      }else if ($_GET['sort'] == 'name'){
        $query = $query . ' ORDER BY name';
      }
    }
    $result = mysql_query2($query);
    if (mysql_num_rows($result) > 0){
      while ($row = mysql_fetch_array($result)){
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        $T_id = $row['id'];
          echo '<td><a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['id'].'">'.$row['name'].'</a></td>';

        echo '<td>'.$row['requirements'].'</td>';
        echo '<td>'.$row['algorithm'].'</td>';
        echo '<td>'.$row['persistent'].'</td>';
        echo '<td>'.$row['uniqueness'].'</td>';
        echo '</tr>';
      }
      echo '</table>';
    }else{
      echo '</table>';
      echo '<p class="error">No tribe_recipes Found</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
