<?php
function editquest(){
  if(checkaccess('quests', 'read')){
    if(!isset($_GET['id'])){
      echo '<p class="error">Error: No quest ID specified - Reverting to list quests</p>';
      listquests();
    }else if(!isset($_GET['commit'])){
      $id = mysql_real_escape_string($_GET['id']);
      $query = 'SELECT name, category, player_lockout_time, quest_lockout_time, prerequisite, task FROM quests WHERE id='.$id;
      $result = mysql_query2($query);
      $query2 = 'SELECT script FROM quest_scripts WHERE quest_id='.$id;
      $result2 = mysql_query2($query2);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      echo '<form action="./index.php?do=editquest&amp;id='.$id.'&amp;commit" method="post"><p>';
      echo 'Quest ID: '.$id."<br/>\n";
      echo 'Quest Name: <input type="text" name="name" value="'.$row['name'].'" />'."<br/>\n";
      echo 'Quest Category: <input type="text" name="category" value="'.$row['category'].'" />'."<br/>\n";
      echo 'Quest Description: <textarea name="task" rows="2" cols="45">'.$row['task']."</textarea><br/>\n";
      echo 'Player Lockout Time: <input type="text" name="player_lockout_time" value="'.$row['player_lockout_time'].'" />'."<br/>\n";
      echo 'Quest Lockout Time: <input type="text" name="quest_lockout_time" value="'.$row['quest_lockout_time'].'" />'."<br/>\n";
      echo 'Prerequisites: <textarea name="prerequisite" rows="2" cols="50">'.htmlspecialchars($row['prerequisite'])."</textarea><br/>\n";
      $row = mysql_fetch_array($result2, MYSQL_ASSOC);
      $script = $row['script'];
      echo '</p><hr/><p>';
      echo 'Quest Script:<br/><textarea name="script" rows="25" cols="80">'.$script."</textarea><br/>\n";
      echo '<input type="submit" name="submit" value="Update Quest" />';
      echo '</p></form>';
    }else{
      $id = mysql_real_escape_string($_GET['id']);
      $name = mysql_real_escape_string($_POST['name']);
      $category = mysql_real_escape_string($_POST['category']);
      $player_lockout_time = mysql_real_escape_string($_POST['player_lockout_time']);
      $quest_lockout_time = mysql_real_escape_string($_POST['quest_lockout_time']);
      $prerequisite = mysql_real_escape_string($_POST['prerequisite']);
      $query = "UPDATE quests SET name='$name', category='$category', player_lockout_time='$player_lockout_time', quest_lockout_time='$quest_lockout_time', prerequisite='$prerequisite' WHERE id='$id'";
      $result = mysql_query2($query);
      $script = mysql_real_escape_string($_POST['script']);
      $query = "UPDATE quest_scripts SET script='$script' WHERE quest_id='$id'";
      $result = mysql_query2($query);
?>
    <SCRIPT language="javascript">
      document.location = "index.php?do=listquests";
    </script>
<?php
    exit;
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }

}
?>
