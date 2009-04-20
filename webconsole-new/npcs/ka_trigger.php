<?php
function ka_trigger(){
  if (checkaccess('npcs', 'read')){
    $query = "SELECT DISTINCT area FROM npc_triggers ORDER BY area";
    $result = mysql_query2($query);
    echo '<table border="1">';
    if (checkaccess('npcs', 'edit')){
      echo '<tr><th>KA</th><th>Action</th></tr>';
    }else{
      echo '<tr><th>KA</th></tr>';
    }
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
      if ($row['area'] != ''){
        echo '<tr><td>';
        echo '<a href="./index.php?do=ka_detail&amp;area='.rawurlencode($row['area']).'">'.$row['area'].'</a>';
        echo '</td>';
        if (checkaccess('npcs', 'edit')){
          echo '<td><form action="./index.php?do=ka_detail&amp;area='.rawurlencode($row['area']).'" method="post"><input type="submit" name="commit" value="Delete KA" /></form></td>';
        }
        echo '</tr>';
      }
    }
    echo '</table>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function ka_detail(){
  if (checkaccess('npcs', 'read')){
    if (isset($_GET['area'])){
      $area = mysql_real_escape_string(urldecode($_GET['area']));
      if (isset($_POST['commit']) && (checkaccess('npcs', 'edit'))){
        if ($_POST['commit'] == "Update Trigger"){
          $tid = mysql_real_escape_string($_POST['trigger_id']);
          $trigger_text = mysql_real_escape_string($_POST['trigger_text']);
          $query = "UPDATE npc_triggers SET trigger_text='$trigger_text' WHERE id='$tid'";
        }else if ($_POST['commit'] == "Update Responses"){
          $tid = mysql_real_escape_string($_POST['trigger_id']);
          $response1 = mysql_real_escape_string($_POST['response1']);
          $response2 = mysql_real_escape_string($_POST['response2']);
          $response3 = mysql_real_escape_string($_POST['response3']);
          $response4 = mysql_real_escape_string($_POST['response4']);
          $response5 = mysql_real_escape_string($_POST['response5']);
          $script = mysql_real_escape_string($_POST['script']);
          $prerequisite = mysql_real_escape_string($_POST['prerequisite']);
          if (isset($_POST['c'])){
            $query = "INSERT INTO npc_responses SET trigger_id='$tid', response1='$response1', response2='$response2', response3='$response3', response4='$response4', response5='$response5', script='$script', prerequisite='$prerequisite'";
          }else{
            $query = "UPDATE npc_responses SET response1='$response1', response2='$response2', response3='$response3', response4='$response4', response5='$response5', script='$script', prerequisite='$prerequisite' WHERE trigger_id='$tid'";
          }
        }else if ($_POST['commit'] == "Create Sub-Trigger"){
          $tid_o = mysql_real_escape_string($_POST['trigger_id']);
          $trigger_text = mysql_real_escape_string($_POST['trigger_text']);
          $query = "SELECT name, lastname FROM characters WHERE id='$id'";
          $result = mysql_query2($query);
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          $npcname = $row['name'];
          if ($row['lastname'] != ''){
            $npcname = $npcname . ' ' .$row['lastname'];
          }
          $tid = GetNextId('npc_triggers');
          $query = "INSERT INTO npc_triggers (id, trigger_text, prior_response_required, area) VALUES ('$tid', '$trigger_text', '$tid_o', '$area')";
          $result = mysql_query2($query);
          $query = "INSERT INTO npc_responses (trigger_id) VALUES ('$tid')";
        }else if ($_POST['commit'] == "Remove"){
          $tid = mysql_real_escape_string($_POST['trigger_id']);
          $query = "DELETE FROM npc_triggers WHERE id='$tid'";
          $result = mysql_query2($query);
          $query = "DELETE FROM npc_responses WHERE trigger_id='$tid'";
        }else if ($_POST['commit'] == "Create New Trigger"){
          $trigger_text = mysql_real_escape_string($_POST['trigger_text']);
          $query = "SELECT name, lastname FROM characters WHERE id='$id'";
          $result = mysql_query2($query);
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          $npcname = $row['name'];
          if ($row['lastname'] != ''){
            $npcname = $npcname . ' ' .$row['lastname'];
          }
          $tid = GetNextId('npc_triggers');
          $query = "INSERT INTO npc_triggers (id, trigger_text, prior_response_required, area) VALUES ('$tid', '$trigger_text', '0', '$area')";
          $result = mysql_query2($query);
          $query = "INSERT INTO npc_responses (trigger_id) VALUES ('$tid')";
        }else if ($_POST['commit'] == "Delete KA"){
          $query = "SELECT id FROM npc_triggers WHERE area='$area'";
          $result = mysql_query2($query);
          while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            $id = $row['id'];
            $q2 = 'DELETE FROM npc_responses WHERE trigger_id='.$id;
            $r2 = mysql_query2($q2);
          }
          $query = "DELETE FROM npc_knowledge_areas WHERE area='$area'";
          $result = mysql_query2($query);
          $query = "DELETE FROM npc_triggers WHERE area='$area'";
          unset($_GET['area']);
        }
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        ka_detail();
      }else{
        $query = "SELECT t.id, t.trigger_text, t.prior_response_required, r.response1, r.response2, r.response3, r.response4, r.response5, r.script, r.prerequisite, o.trigger_text AS prior, o.area as prior_area, r.trigger_id FROM npc_triggers AS t LEFT JOIN npc_responses AS r ON t.id=r.trigger_id LEFT JOIN npc_triggers AS o ON t.prior_response_required=o.id WHERE t.area='$area'";
        if (isset($_GET['trigger'])){
          $t = mysql_real_escape_string($_GET['trigger']);
          $query = $query . " ORDER BY t.id IN ('$t') DESC";
        }else{
          $query = $query . " ORDER BY t.id";
        }
        $result = mysql_query2($query);
        if (mysql_num_rows($result) == 0){
          echo '<p class="error">KA Has no entries</p>';
        }else{
          echo '<table border="1"><tr><th>Trigger</th><th>Response</th>';
          if (checkaccess('npcs', 'edit')){
            echo '<th>Action</th>';
          }
          echo '</tr>';
          while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            $t_id = $row['id'];
            echo '<tr>';
            echo '<td>';
            if (isset($t) && ($t == $t_id)){
              echo '<form action="./index.php?do=ka_detail&amp;area='.rawurlencode($area).'" method="post">';
              echo '<input type="hidden" name="trigger_id" value="'.$row['id'].'" />';
              if ($row['prior'] != ''){
                echo 'Prior Response: '.$row['prior'].'<br/>';
                if ($row['prior_area'] != $area){
                  echo 'From KA: '.$row['prior_area'].'<br/>';
                }
              }
              if (checkaccess('npcs', 'edit')){
                echo '<input type="text" name="trigger_text" value="'.htmlspecialchars($row['trigger_text']).'"/><br/>';
                echo '<input type="submit" name="commit" value="Update Trigger" /></form></td>';
              }else{
                echo htmlspecialchars($row['trigger_text']);
              }
            }else{
              echo '<a href="./index.php?do=ka_detail&amp;area='.rawurlencode($area).'&amp;trigger='.$t_id;
              if ($row['trigger_id'] == ''){
                echo '&amp;c=true';
              }
              echo '">'.htmlspecialchars($row['trigger_text']).'</a></td>';
            }
            echo '<td>';
            if (isset($t) && ($t == $t_id)){
              if (checkaccess('npcs', 'edit')){
                echo '<form action="./index.php?do=ka_detail&amp;area='.rawurlencode($area).'" method="post">';
                echo '<input type="hidden" name="trigger_id" value="'.$row['id'].'" />';
                echo 'Response 1: <textarea name="response1" rows="3" cols="30">'.$row['response1'].'</textarea><br/>';
                echo 'Response 2: <textarea name="response2" rows="3" cols="30">'.$row['response2'].'</textarea><br/>';
                echo 'Response 3: <textarea name="response3" rows="3" cols="30">'.$row['response3'].'</textarea><br/>';
                echo 'Response 4: <textarea name="response4" rows="3" cols="30">'.$row['response4'].'</textarea><br/>';
                echo 'Response 5: <textarea name="response5" rows="3" cols="30">'.$row['response5'].'</textarea><br/>';
                echo '<hr/>';
                echo 'Script: <textarea name="script">'.$row['script'].'</textarea><br/>';
                echo 'Prerequisite: <textarea name="prerequisite">'.$row['prerequisite'].'</textarea><br/>';
                if (isset($_GET['c'])){
                  echo '<input type="hidden" name="c" value="true">';
                }
                echo '<input type="submit" name="commit" value="Update Responses" /><hr/>';
                echo 'New Trigger:<input type="text" name="trigger_text" size="25" /><input type="submit" name="commit" value="Create Sub-Trigger" />';
                echo '</form>';
              }else{
                echo 'Response 1: '.htmlspecialchars($row['response1']).'<br/>';
                echo 'Response 2: '.htmlspecialchars($row['response2']).'<br/>';
                echo 'Response 3: '.htmlspecialchars($row['response3']).'<br/>';
                echo 'Response 4: '.htmlspecialchars($row['response4']).'<br/>';
                echo 'Response 5: '.htmlspecialchars($row['response5']).'<br/>';
                echo '<hr/>';
                echo 'Script: '.htmlspecialchars($row['script']).'<br/>';
                echo 'Prerequisite: '.htmlspecialchars($row['prerequisite']).'<br/>';
              }
            }else{
              if ($row['trigger_id'] == ''){
                echo '&nbsp;';
              }else{
                echo '<a href="./index.php?do=ka_detail&amp;area='.rawurlencode($area).'&amp;trigger='.$t_id.'">+</a>';
              }
            }
            echo '</td>';
            if (checkaccess('npcs', 'edit')){
              echo '<td>';
              echo '<form action="./index.php?do=ka_detail&amp;area='.rawurlencode($area).'" method="post">';
              echo '<input type="hidden" name="trigger_id" value="'.$row['id'].'" />';
              echo '<input type="submit" name="commit" value="Remove" />';
              echo '</form></td>';
            }
            echo '</tr>'."\n";
          }if (checkaccess('npcs', 'edit')){
            echo '<tr><td><form action="./index.php?do=ka_detail&amp;area='.rawurlencode($area).'" method="post">Create New Trigger:<br/><input type="text" name="trigger_text" /><br/><input type="submit" name="commit" value="Create New Trigger" /></form></td>';
            echo '<td>&nbsp;</td><td>&nbsp;</td>';
            echo '</tr>';
          }
          echo '</table>';
        }
       //add new here
      }
    }else{
      echo '<p class="error">Error: No Area selected</p>';
      ka_trigger();
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
