<?php
function createquest(){
  if(checkaccess('quests','create')){
    if(!isset($_GET['commit'])){
      echo '<p class="header">Creating a new Quest</p>';
      echo '<form action="index.php?do=createquest&amp;commit" method="post">';
      echo '<p class="bold">Quest ID will be generated automatically</p>';
      echo '<div><table border="0"> <tr><td>Quest Name:</td><td> <input type="text" name="name" size="30"/></td></tr>';
      echo '<tr><td>Category:</td><td> <input type="text" name="category" size="30"/></td></tr>';
      echo '<tr><td>Player Lockout:</td><td> <input type="text" name="player_lockout" size="30"/></td></tr>';
      echo '<tr><td>Quest Lockout:</td><td> <input type="text" name="quest_lockout" size="30"/></td></tr>';
      echo '<tr><td>Quest Description:</td><td> <textarea rows="2" cols="40" name="description"></textarea></td></tr>';
      echo '<tr><td><input type="submit" name="submit" value="Create Quest" /></td><td></td></tr></table></div>';
      echo '</form>';
    }else{
 //Here we create the quest
      $name = mysql_real_escape_string($_POST['name']);
      $player_lockout = mysql_real_escape_string($_POST['player_lockout']);
      $quest_lockout = mysql_real_escape_string($_POST['quest_lockout']);
      $category = mysql_real_escape_string($_POST['category']);
      $description = mysql_real_escape_string($_POST['description']);
      $id = GetNextId('quests');
      $query = "INSERT INTO quests (id, name, task, player_lockout_time, quest_lockout_time, category) VALUES ('$id', '$name', '$description', '$player_lockout', '$quest_lockout', '$category')";
      $result = mysql_query2($query);
      $query = "INSERT INTO quest_scripts (quest_id, script) VALUES ('$id', '#New Quest - Please Update')";
      $result = mysql_query2($query);

    echo '<SCRIPT language="javascript">';
    echo 'document.location = "index.php?do=editquest&id='.$id.'";';
    echo '</script>';

    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
