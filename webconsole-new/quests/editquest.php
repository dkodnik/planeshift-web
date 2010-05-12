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
      echo '<form action="./index.php?do=editquest&amp;id='.$id.'&amp;commit" method="post"><p><table border="0">';
      echo '<tr><td>Quest ID:</td><td> '.$id."</td></tr>\n";
      echo '<tr><td>Quest Name:</td><td> <input type="text" name="name" value="'.$row['name'].'" />'."</td></tr>\n";
      echo '<tr><td>Quest Category:</td><td> <input type="text" name="category" value="'.$row['category'].'" />'."</td></tr>\n";
      echo '<tr><td>Quest Description:</td><td> <textarea name="task" rows="2" cols="45">'.$row['task']."</textarea></td></tr>\n";
      echo '<tr><td>Player Lockout Time:</td><td> <input type="text" name="player_lockout_time" value="'.$row['player_lockout_time'].'" />'."</td></tr>\n";
      echo '<tr><td>Quest Lockout Time:</td><td> <input type="text" name="quest_lockout_time" value="'.$row['quest_lockout_time'].'" />'."</td></tr>\n";
      echo '<tr><td>Prerequisites:</td><td> <textarea name="prerequisite" rows="2" cols="50">'.htmlspecialchars($row['prerequisite'])."</textarea></td></tr>\n";
      $row = mysql_fetch_array($result2, MYSQL_ASSOC);
      $script = $row['script'];
      echo '</table></p><hr/><p>';
      echo 'Quest Script:<br/><textarea name="script" rows="25" cols="80">'.$script."</textarea><br/>\n";
      echo '<input type="submit" name="submit" value="Update Quest" /><input type="submit" name="submit2" value="save and continue editing" />';
      echo '</p></form>';
    }else{
      $id = mysql_real_escape_string($_GET['id']);
      $name = mysql_real_escape_string($_POST['name']);
      $category = mysql_real_escape_string($_POST['category']);
      $task = mysql_real_escape_string($_POST['task']);
      $player_lockout_time = mysql_real_escape_string($_POST['player_lockout_time']);
      $quest_lockout_time = mysql_real_escape_string($_POST['quest_lockout_time']);
      $prerequisite = mysql_real_escape_string($_POST['prerequisite']);
      $query = "UPDATE quests SET name='$name', category='$category', task='$task', player_lockout_time='$player_lockout_time', quest_lockout_time='$quest_lockout_time', prerequisite='$prerequisite' WHERE id='$id'";
      $result = mysql_query2($query);
      $script = mysql_real_escape_string($_POST['script']);
      $query = "UPDATE quest_scripts SET script='$script' WHERE quest_id='$id'";
      $result = mysql_query2($query);
      if (isset($_POST['submit2']))
      {
        echo '<SCRIPT language="javascript"> document.location = "index.php?do=editquest&id='.$id.'"; </script>';
      }
      else
      {
        echo '<SCRIPT language="javascript"> document.location = "index.php?do=listquests"; </script>';
      }
    exit;
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
