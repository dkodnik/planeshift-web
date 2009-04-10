<?php
function listquests(){
  if(checkaccess('quests', 'read')){
    $query = 'SELECT id, name, category, player_lockout_time, quest_lockout_time, prerequisite FROM quests';
    if(!isset($_GET['sort'])){
      $query = $query.' ORDER BY name ASC';
    }
    else{
      switch($_GET['sort']){
        case 'id':
          $query = $query.' ORDER BY id ASC';
          break;
        case 'category':
          $query = $query.' ORDER BY category ASC';
          break;
        case 'name':
          $query = $query.' ORDER BY name ASC';
          break;
        case 'plock':
          $query = $query.' ORDER BY player_lockout_time ASC'; 
          break;
        case 'qlock':
          $query = $query.' ORDER by quest_lockout_time ASC';
          break;
        default:
        $query = $query.' ORDER BY name ASC';
      }
    }
    $result = mysql_query2($query);
    echo '<table border="1">'."\n";
    echo '<tr><th><a href="./index.php?do=listquests&amp;sort=id">ID</a></th><th><a href="./index.php?do=listquests&amp;sort=category">Category</a></th><th><a href="./index.php?do=listquests&amp;sort=name">Name</a></th><th><a href="./index.php?do=listquests&amp;sort=plock">Player Lockout</a></th><th><a href="./index.php?do=listquests&amp;sort=qlock">Quest Lockout</a></th><th>Prerequisites</th><th>Actions</th></tr>';
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      echo '<tr><td>'.$row['id'].'</td><td>'.$row['category'].'</td><td>'.$row['name'].'</td><td>'.$row['player_lockout_time'];
      echo '</td><td>'.$row['quest_lockout_time'].'</td><td>'.htmlspecialchars($row['prerequisite']).'</td><td>';
      echo '<a href="./index.php?do=readquest&amp;id='.$row['id'].'">Read</a>';
      if (checkaccess('quests', 'edit')){
        echo '<br/><a href="./index.php?do=editquest&amp;id='.$row['id'].'">Edit</a>';
      }
      if (checkaccess('quests', 'delete')){
        echo '<br/><a href="./index.php?do=deletequest&amp;id='.$row['id'].'">Delete</a>';
      }
      echo '</td></tr>';
    }
    echo '</table>'."\n";
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function readquest(){
  if(checkaccess('quests', 'read')){
    if(!isset($_GET['id'])){
      echo '<p class="error">Error: No quest ID specified - Reverting to list quests</p>';
      listquests();
    }else{
      $id = mysql_real_escape_string($_GET['id']);
      $query = 'SELECT name, category, player_lockout_time, quest_lockout_time, prerequisite FROM quests WHERE id='.$id;
      $result = mysql_query2($query);
      $query2 = 'SELECT script FROM quest_scripts WHERE quest_id='.$id;
      $result2 = mysql_query2($query2);
      $row = mysql_fetch_array($result, MYSQL_ASSOC);
      echo 'Quest ID: '.$id."<br/>\n";
      echo 'Quest Name: '.$row['name']."<br/>\n";
      echo 'Quest Category: '.$row['category']."<br/>\n";
      echo 'Player Lockout Time: '.$row['player_lockout_time']."<br/>\n";
      echo 'Quest Lockout Time: '.$row['quest_lockout_time']."<br/>\n";
      echo 'Prerequisites: '.htmlspecialchars($row['prerequisite'])."<br/>\n";
      $row = mysql_fetch_array($result2, MYSQL_ASSOC);
      $script = str_replace("\n", "<br/>\n", $row['script']);
      echo '<hr/>';
      echo 'Quest Script:<br/>'.$script."<br/>\n";
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function npcquests(){
  if (checkaccess('quests', 'read')){
    //Select all NPCs
    $query = 'SELECT c.id, c.name, c.lastname, s.name AS sector FROM characters AS c LEFT JOIN sectors AS s ON c.loc_sector_id = s.id WHERE account_id=9 AND racegender_id<22';
    if (isset($_GET['sort'])){
      if ($_GET['sort'] == 'npc'){
        $query = $query . ' ORDER BY c.name';
      }else if ($_GET['sort'] == 'sector'){
        $query = $query . ' ORDER BY sector, c.name';
      }
    }else{
      $query = $query . ' ORDER BY c.name';
    }
    $result = mysql_query2($query);
    $query = 'SELECT q.name, q.id, s.script FROM quests AS q LEFT JOIN quest_scripts AS s ON q.id=s.quest_id';
    $result_script = mysql_query2($query);

    echo '<table border="1"><tr><th><a href="./index.php?do=npcquests&amp;sort=npc">NPC Name</a></th><th><a href="./index.php?do=npcquests&amp;sort=sector">Sector</a></th><th>Quests</th><th>Starting Quests</th></tr>';
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      $fullname = $row['name'];
      if ($row['lastname'] != ""){
        $fullname = $fullname . ' ' . $row['lastname'];
      }
      
      mysql_data_seek($result_script, 0);
      $namestring = '/'.$fullname.'\:/ims';
      while($scripts = mysql_fetch_array($result_script, MYSQL_ASSOC)){
        $id = $scripts['id'];
        if (preg_match($namestring, $scripts['script']) == 1){
          $AllQuests["$fullname"]["$id"] = $scripts['name'];
        }
        $AllScripts["$id"] = $scripts['script'];
      }

      echo '<tr><td>'.$fullname.' - ';
      echo '</td><td>'.$row['sector'].'</td><td>';
      if(isset($AllQuests["$fullname"])){
        foreach($AllQuests["$fullname"] as $Q_ID => $Q_name){
          $string = '/'.$fullname.'\:.*assign\040quest/ims';
          if (preg_match($string, $AllScripts["$Q_ID"]) == 1){
            $StartQuests["$Q_ID"] = $Q_name;
          }else{
            echo $Q_name . ' - <a href="./index.php?do=readquest&amp;id='.$Q_ID.'">Read</a>';
            if (checkaccess('quests', 'edit')){
              echo ' - <a href="./index.php?do=editquest&amp;id='.$Q_ID.'">Edit</a>';
            }
            echo '<br/>';
          }
        }
      }
      echo '</td><td>';
      if (isset($StartQuests)){
        foreach($StartQuests as $q_id => $q_name){
          echo $q_name . ' - <a href="./index.php?do=readquest&amp;id='.$q_id.'">Read</a>';
          if (checkaccess('quests', 'edit')){
            echo ' - <a href="./index.php?do=editquest&amp;id='.$q_id.'">Edit</a>';
          }
          echo '<br/>';
        }
      }
      unset($StartQuests);
      echo '</td></tr>'."\n";
    }
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function countquests(){
  if (checkaccess('quests', 'read')){
    $query = "SELECT category, COUNT(category) AS count FROM quests GROUP BY category";
    $result = mysql_query2($query);
    echo '<hr/><p>Quest Counts:';
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      echo '<br/>Category: '.$row['category'].' - '.$row['count'];
    }
    echo '</p>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
