<?php
function listfactions(){
  if (checkaccess('npcs', 'read')){
    if (isset($_POST['commit']) && checkaccess('npcs', 'edit')){
      if ($_POST['commit'] == "Commit Edit"){
        $id = escapeSqlString($_POST['id']);
        $faction_description = escapeSqlString($_POST['faction_description']);
        $faction_character = escapeSqlString($_POST['faction_character']);
        $faction_weight = escapeSqlString($_POST['faction_weight']);
        $query = "UPDATE factions SET faction_description='$faction_description', faction_character='$faction_character', faction_weight='$faction_weight' WHERE id='$id'";
        $result = mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        listfactions();
        return;
      }else{
        echo '<p class="error">Bad Commit, returning to listing</p>';
        unset($_POST);
        listfactions();
        return;
      }
    }else if (isset($_POST['action']) && checkaccess('npcs', 'edit')){
      if ($_POST['action'] == "Edit"){
        $id = escapeSqlString($_POST['id']);
        $query = "SELECT * FROM factions WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo '<form action="./index.php?do=factions" method="post">';
        echo '<input type="hidden" name="id" value="'.$id.'" />';
        echo '<table border="1"><tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name:</td><td>'.$row['faction_name'].'</td></tr>';
        echo '<tr><td>Description:</td><td><textarea name="faction_description" row="4" cols="40">'.$row['faction_description'].'</textarea></td></tr>';
        echo '<tr><td>Character:</td><td><textarea name="faction_character" row="4" cols="40">'.$row['faction_character'].'</textarea></td></tr>';
        echo '<tr><td>Weight:</td><td><input type="text" name="faction_weight" value="'.$row['faction_weight'].'" /></td></tr>';
        echo '</table>';
        echo '<input type="submit" name="commit" value="Commit Edit" />';
        echo '</form>';
      }else{
        echo '<p class="error">Bad Action, returning to listing</p>';
        unset($_POST);
        listfactions();
        return;
      }
    }else{
      $query = "SELECT * FROM factions";
      $result = mysql_query2($query);
      if (sqlNumRows($result) == 0 ){
        echo 'No Skills Found!';
      }
      echo '<table border="1"><tr><th>Faction</th><th>Description</th><th>Character</th><th>Weight</th>';
      if (checkaccess('npcs', 'edit')){
        echo '<th>Actions</th>';
      }
      echo '</tr>';
      while ($row = fetchSqlAssoc($result)){
        echo '<tr>';
        echo '<td>'.$row['faction_name'].'</td>';
        echo '<td>'.$row['faction_description'].'</td>';
        echo '<td>'.$row['faction_character'].'</td>';
        echo '<td>'.$row['faction_weight'].'</td>';
        if (checkaccess('npcs', 'edit')){
          echo '<td>';
          echo '<form action="./index.php?do=factions" method="post">';
          echo '<input type="hidden" name="id" value="'.$row['id'].'"/>';
          echo '<input type="submit" name="action" value="Edit" />';
          echo '</form></td>';
        }
        echo '</tr>';
      }
      echo '</table>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
