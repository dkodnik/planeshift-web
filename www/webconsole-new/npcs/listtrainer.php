<?
function listtrainer(){
  if (checkaccess('npcs', 'read')){
    $query = "SELECT skill_id, name FROM skills";
    $result = mysql_query2($query);
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $S_id = $row['skill_id'];
      $Skills["$S_id"] = $row['name'];
    }
    $query = "SELECT t.skill_id, t.min_rank, t.max_rank, t.player_id, t.min_faction FROM trainer_skills AS t";
    $result = mysql_query2($query);
    while (list($skill_id, $min_rank, $max_rank, $player_id, $min_faction) = mysql_fetch_array($result)){
      $Trainer["$player_id"]["$skill_id"]['min_rank'] = $min_rank;
      $Trainer["$player_id"]["$skill_id"]['max_rank'] = $max_rank;
      $Trainer["$player_id"]["$skill_id"]['min_faction'] = $min_faction;
      $Trainer["$player_id"]["$skill_id"]['skill'] = $skill_id;
    }
    $skill_result = PrepSelect('skill');
    $query = "SELECT DISTINCT t.player_id, CONCAT_WS(' ', c.name, c.lastname) AS name, s.name AS sector FROM trainer_skills AS t LEFT JOIN characters AS c on t.player_id=c.id LEFT JOIN sectors AS s ON c.loc_sector_id=s.id";
    if (isset($_GET['sort'])){
      if ($_GET['sort'] == 'name'){
        $query = $query . ' ORDER BY name, sector';
      }else if ($_GET['sort'] == 'sector'){
        $query = $query . ' ORDER BY sector, name';
      }
    }else{
      $query = $query . ' ORDER BY sector, name';
    }
    $result = mysql_query2($query);
    echo '<table border="1"><tr><th><a href="./index.php?do=listtrainer&amp;sort=name">NPC</a></th><th><a href="./index.php?do=listtrainer&amp;sort=sector">Sector</a></th><th>Details</th></tr>';
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      echo '<tr><td>';
      $T_id = $row['player_id'];
      if (checkaccess('npcs', 'edit')){
        echo '<a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$T_id.'">'.$row['name'].'</a></td><td>';
      }else{
        echo $row['name'].'</td><td>';
      }
      echo $row['sector'].'</td><td>';
      foreach ($Trainer["$T_id"] AS $t){
        if (checkaccess('npcs', 'edit')){
          echo '<form action="./index.php?do=edittrainer" method="post">';
          echo '<input type="hidden" name="player_id" value="'.$T_id.'" />';
          echo '<input type="hidden" name="skill_id" value="'.$t['skill'].'" />';
          echo '<table border="1"><tr><th class="item_wide">Skill</th><th>MinRank</th><th>MaxRank</th><th>Min Faction</th><th>Actions</th></tr>';
          $skill = $t['skill'];
          echo '<tr><td>'.htmlspecialchars($Skills["$skill"]).'</td>';
          echo '<td><input type="text" name="min_rank" value="'.$t['min_rank'].'" size="10"/></td>';
          echo '<td><input type="text" name="max_rank" value="'.$t['max_rank'].'" size="10"/></td>';
          echo '<td><input type="text" name="min_faction" value="'.$t['min_faction'].'" size="10"/></td>';
          echo '<td><input type="submit" name="commit" value="Update" /><br/><input type="submit" name="commit" value="Remove" /></td>';
          echo '</tr></table>';
          echo '</form>';
        }else{
          echo '<table border="1"><tr><th class="item_wide">Skill</th><th>MinRank</th><th>MaxRank</th><th>Min Faction</th></tr>';
          $skill = $t['skill'];
          echo '<tr><td>'.htmlspecialchars($Skills["$skill"]).'</td><td>'.$t['min_rank'].'</td><td>'.$t['max_rank'].'</td><td>'.$t['min_faction'].'</tr>';
          echo '</table>';
        }
      }
      if (checkaccess('npcs', 'edit')){
        echo '<hr/><form action="./index.php?do=edittrainer" method="post">';
        echo '<input type="hidden" name="player_id" value="'.$T_id.'" />';
        echo '<table border="1"><tr><th class="item_wide">Skill</th><th>MinRank</th><th>MaxRank</th><th>Min Faction</th></tr>';
        echo '<tr><td>'.DrawSelectBox('skill', $skill_result, 'skill_id', '').'</td>';
        echo '<td><input type="text" name="min_rank" size="10"/></td>';
        echo '<td><input type="text" name="max_rank" size="10"/></td>';
        echo '<td><input type="text" name="min_faction" size="10"/></td>';
        echo '</tr>';
        echo '</table>';
        echo '<input type="submit" name="commit" value="Add" />';
        echo '</form>';
      }
      echo '</td></tr>';
    }
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function edittrainer(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_POST['commit'])){
      $skill_id = mysql_real_escape_string($_POST['skill_id']);
      $player_id = mysql_real_escape_string($_POST['player_id']);
      $min_rank = mysql_real_escape_string($_POST['min_rank']);
      $max_rank = mysql_real_escape_string($_POST['max_rank']);
      $min_faction = mysql_real_escape_string($_POST['min_faction']);
      if ($_POST['commit'] == "Add"){
        $query = "INSERT INTO trainer_skills (player_id, skill_id, min_rank, max_rank, min_faction) VALUES ('$player_id', '$skill_id', '$min_rank', '$max_rank', '$min_faction')";
        $result = mysql_query2($query);
      }else if ($_POST['commit'] == "Update"){
        $query = "UPDATE trainer_skills SET min_rank='$min_rank', max_rank='$max_rank', min_faction='$min_faction' WHERE player_id='$player_id' AND skill_id='$skill_id'";
        $result = mysql_query2($query);
      }else if ($_POST['commit'] == "Remove"){
        $query = "DELETE FROM trainer_skills WHERE player_id='$player_id' AND skill_id='$skill_id'";
        $result = mysql_query2($query);
      }
      echo '<p class="error">Update Successful</p>';
      listtrainer();
    }else{
      echo '<p class="error">Error: No Commit value found, Edit Aborted</p>';
      listtrainer();
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

?>
