<?php
function createquest(){
  if(checkaccess('quests','create')){
    if(!isset($_GET['commit'])){
      echo '<p class="header">Creating a new Quest</p>';
      echo '<form action="index.php?do=createquest&amp;commit" method="post">';
      echo '<p class="bold">Quest ID will be generated automatically<br/>';
      echo 'Quest Name: <input type="text" name="name" size="30"/><br/>';
      echo 'Category: <input type="text" name="category" size="30"/><br/>';
      echo 'Player Lockout: <input type="text" name="player_lockout" size="30"/><br/>';
      echo 'Quest Lockout: <input type="text" name="quest_lockout" size="30"/><br/>';
      echo 'Quest Description: <textarea rows="2" cols="40" name="description"></textarea><br/>';
      echo '<input type="submit" name="submit" value="Create Quest" /></p>';
      echo '</form>';
    }else{
 //Here we create the quest
      $name = mysql_real_escape_string($_POST['name']);
      $player_lockout = mysql_real_escape_string($_POST['player_lockout']);
      $quest_lockout = mysql_real_escape_string($_POST['quest_lockout']);
      $description = mysql_real_escape_string($_POST['description']);
      $category = mysql_real_escape_string($_POST['description']);
      $id = GetNextId('quests');
      $query = "INSERT INTO quests (id, name, task, player_lockout_time, quest_lockout_time, category) VALUES ('$id', '$name', '$description', '$player_lockout', '$quest_lockout', '$category')";
      $result = mysql_query2($query);
      $query = "INSERT INTO quest_scripts (quest_id, script) VALUES ('$id', '#New Quest - Please Update')";
      $result = mysql_query2($query);
?>
    <SCRIPT language="javascript">
      document.location = "index.php?do=editquest&id=<?echo $id?>";
    </script>
<?php
    exit;
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
