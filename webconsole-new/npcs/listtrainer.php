<?php
function listtrainer(){
    if (checkaccess('npcs', 'read')){
        $query = "SELECT sk.name AS skill_name, t.skill_id, t.min_rank, t.max_rank, t.player_id, t.min_faction, CONCAT_WS(' ', c.name, c.lastname) AS name, s.name AS sector FROM trainer_skills AS t LEFT JOIN characters AS c on t.player_id=c.id LEFT JOIN sectors AS s ON c.loc_sector_id=s.id LEFT JOIN skills AS sk ON sk.skill_id=t.skill_id";
        if (isset($_GET['sort']))
        {
            if ($_GET['sort'] == 'name')
            {
                $query .= ' ORDER BY name, sector';
            }
            else if ($_GET['sort'] == 'sector')
            {
                $query .= ' ORDER BY sector, name';
            }
        }
        else
        {
            $query .= ' ORDER BY sector, name';
        }
        $result = mysql_query2($query);
        $id = "";
        $skill_result = PrepSelect('skill');
        $skill_box = DrawSelectBox('skill', $skill_result, 'skill_id', '', false);
        echo '<table border="1" cellspacing="0"><tr><th><a href="./index.php?do=listtrainer&amp;sort=name">NPC</a></th><th><a href="./index.php?do=listtrainer&amp;sort=sector">Sector</a></th><th>Details</th></tr>';
        while ($row = fetchSqlAssoc($result))
        {
            if ($id == "")
            {
                echo '<tr><td>';
                $id = $row['player_id'];
                if (checkaccess('npcs', 'edit'))
                {
                    echo '<a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$id.'">'.$row['name'].'</a></td><td>';
                }
                else
                {
                    echo $row['name'].'</td><td>';
                }
                echo $row['sector'].'</td><td>';
                echo '<table border="1" cellspacing="0"><tr><th class="item_wide">Skill</th><th>MinRank</th><th>MaxRank</th><th>Min Faction</th><th>Actions</th></tr>';
            }
            elseif ($id != $row['player_id'])
            {
                if (checkaccess('npcs', 'edit'))
                {
                    echo '<tr><td colspan="5"></td></tr>';
                    echo '<form action="./index.php?do=edittrainer" method="post">';
                    echo '<input type="hidden" name="player_id" value="'.$id.'" />';
                    echo '<tr><td>'.$skill_box.'</td>';
                    echo '<td><input type="text" name="min_rank" size="10"/></td>';
                    echo '<td><input type="text" name="max_rank" size="10"/></td>';
                    echo '<td><input type="text" name="min_faction" size="10"/></td>';
                    echo '<td><input type="submit" name="commit" value="Add" /></td>';
                    echo '</tr>';
                    echo '</form>';
                }
                echo '</table></td></tr><tr><td>';
                $id = $row['player_id'];
                if (checkaccess('npcs', 'edit'))
                {
                    echo '<a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$id.'">'.$row['name'].'</a></td><td>';
                }
                else
                {
                    echo $row['name'].'</td><td>';
                }
                echo $row['sector'].'</td><td>';
                echo '<table border="1" cellspacing="0"><tr><th class="item_wide">Skill</th><th>MinRank</th><th>MaxRank</th><th>Min Faction</th><th>Actions</th></tr>';
            }
            
            if (checkaccess('npcs', 'edit')){
                echo '<tr><form action="./index.php?do=edittrainer" method="post">';
                echo '<input type="hidden" name="player_id" value="'.$id.'" />';
                echo '<input type="hidden" name="skill_id" value="'.$row['skill_id'].'" />';
                echo '<td>'.$row['skill_name'].'</td>';
                echo '<td><input type="text" name="min_rank" value="'.$row['min_rank'].'" size="10"/></td>';
                echo '<td><input type="text" name="max_rank" value="'.$row['max_rank'].'" size="10"/></td>';
                echo '<td><input type="text" name="min_faction" value="'.$row['min_faction'].'" size="10"/></td>';
                echo '<td><input type="submit" name="commit" value="Update" />&nbsp;<input type="submit" name="commit" value="Remove" /></td>';
                echo '</form>';
                echo '</tr>';
            }
            else
            {
                echo '<tr><td>'.$row['skill_name'].'</td><td>'.$row['min_rank'].'</td><td>'.$row['max_rank'].'</td><td>'.$row['min_faction'].'</td><td></td></tr>';
            }
        }
        if (checkaccess('npcs', 'edit'))
        {
            echo '<tr><td colspan="5"></td></tr>';
            echo '<form action="./index.php?do=edittrainer" method="post">';
            echo '<input type="hidden" name="player_id" value="'.$id.'" />';
            echo '<tr><td>'.$skill_box.'</td>';
            echo '<td><input type="text" name="min_rank" size="10"/></td>';
            echo '<td><input type="text" name="max_rank" size="10"/></td>';
            echo '<td><input type="text" name="min_faction" size="10"/></td>';
            echo '<td><input type="submit" name="commit" value="Add" /></td>';
            echo '</tr>';
            echo '</form>';
        }
        echo '</table>'; 
        echo '</td></tr>';
        echo '</table>'; 
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function edittrainer(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_POST['commit'])){
      $skill_id = escapeSqlString($_POST['skill_id']);
      $player_id = escapeSqlString($_POST['player_id']);
      $min_rank = escapeSqlString($_POST['min_rank']);
      $max_rank = escapeSqlString($_POST['max_rank']);
      $min_faction = escapeSqlString($_POST['min_faction']);
      if ($_POST['commit'] == "Add"){
        $query = "INSERT INTO trainer_skills (player_id, skill_id, min_rank, max_rank, min_faction) VALUES ('$player_id', '$skill_id', '$min_rank', '$max_rank', '$min_faction')";
        $result = mysql_query2($query);
      }else if ($_POST['commit'] == "Update"){
        $query = "UPDATE trainer_skills SET min_rank='$min_rank', max_rank='$max_rank', min_faction='$min_faction' WHERE player_id='$player_id' AND skill_id='$skill_id'";
        $result = mysql_query2($query);
      }else if ($_POST['commit'] == "Remove"){
        $query = "DELETE FROM trainer_skills WHERE player_id='$player_id' AND skill_id='$skill_id' AND min_rank='$min_rank' LIMIT 1";
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
