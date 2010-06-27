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
    echo '<table border="1">';
    // area=dummy_place_holder gets sent because the next script expects a GET['area'] even though we use the post later on. Eventually this will need to be redesigned.
    echo '<form action="./index.php?do=ka_detail&amp;area=dummy_place_holder" method="post">Create New Trigger:';
    echo '<tr><td>KA area name: </td><td><input type="text" name="area" value="" /></td></tr>';
    echo '<tr><td>KA trigger text: </td><td><input type="text" name="trigger_text" /></td></tr>';
    echo '<tr><td><input type="submit" name="commit" value="Create New KA Area" /></td><td></td></tr></form>';
    echo '</table>';    
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function ka_detail(){
  if (checkaccess('npcs', 'read')){
    if (isset($_GET['area'])){
      $area = mysql_real_escape_string(urldecode($_GET['area']));
      if ((isset($_POST['commit']) || isset($_GET['commit'])) && (checkaccess('npcs', 'edit'))){
        if ($_POST['commit'] == "Update Trigger"){
          $tid = mysql_real_escape_string($_POST['trigger_id']);
          $trigger_text = mysql_real_escape_string($_POST['trigger_text']);
          $query = "UPDATE npc_triggers SET trigger_text='$trigger_text' WHERE id='$tid'";
        }else if ($_POST['commit'] == 'add action to script')
        {
            $responseid = $_GET['responseid'];
            $area = $_GET['area'];
            $scriptelem = $_POST['scriptelem'];
            $skillname = $_POST['skillname'];
            $questname = $_POST['questname'];
            $offeritem = $_POST['offeritem'];
            $giveexp = $_POST['giveexp'];
            $givemoney = $_POST['givemoney'];

            // get current script
            $query = "select script from npc_responses where id=$responseid";
            $result = mysql_query2($query);
            $line = mysql_fetch_array($result, MYSQL_NUM);
            $script = $line[0];

            // remove last part
            if ($script=='') 
            {
                $script = '<response>';
            } 
            else 
            {
                $pos = strpos($script, '</response>');
                $script = substr($script,0,$pos);
            }

            if ($scriptelem=='respond') 
            {
                $script = $script . '<respond/>';
            } 
            else if ($scriptelem=='animgreet') 
            {
                $script = $script . '<action anim='.greet.'/>';
            } 
            else if ($scriptelem=='train') 
            {
                $script = $script . '<train skill='.$skillname.'/>';
            } 
            else if ($scriptelem=='assignquest') 
            {
                $script = $script . '<assign q1='.$questname.'/>';
            } 
            else if ($scriptelem=='completequest') 
            {
                $script = $script . '<complete quest_id="'.$questname.'/>';
            } 
            else if ($scriptelem=='offeritem') 
            {
                $script = $script . '<offer>';
                if (strpos($offeritem, ',')) 
                {
                    $tok = strtok($offeritem, ',');
                    $script = $script . '<item id='.$tok.'/>';  
                    while ($tok = strtok(','))
                    {
                        $script = $script . "<item id=".$tok."/>";
                    }
                } 
                else 
                {
                    $script = $script . '<item id='.$offeritem.'/>';
                }
                $script = $script . '</offer>';

            } else if ($scriptelem=='giveexp') {
                $script = $script . "<run scr=\"give_exp\" param0=\"$giveexp\" />";
            } else if ($scriptelem=='givemoney') {
                $script = $script . "<money value=\"$givemoney\" />";
            }

            $script = $script . "</response>";
            $query = "update npc_responses set script='".$script."' where id=$responseid";
            //echo "$query"; 
            $result = mysql_query2($query);

            // redirect on same page
            echo "<SCRIPT language=\"javascript\">";
            echo '  this.location = "index.php?do=ka_detail&area='.$area.'&trigger='.$responseid.'";';
            echo "  </script>";        
        }else if ($_POST['commit'] == "Update Responses"){
            $tid = mysql_real_escape_string($_POST['trigger_id']);
            $response1 = mysql_real_escape_string($_POST['response1']);
            $response2 = mysql_real_escape_string($_POST['response2']);
            $response3 = mysql_real_escape_string($_POST['response3']);
            $response4 = mysql_real_escape_string($_POST['response4']);
            $response5 = mysql_real_escape_string($_POST['response5']);
            $script = mysql_real_escape_string($_POST['script']);
            $prerequisite = mysql_real_escape_string($_POST['prerequisite']);
            $audio_path1 = mysql_real_escape_string($_POST['audio_path1']);
            $audio_path2 = mysql_real_escape_string($_POST['audio_path2']);
            $audio_path3 = mysql_real_escape_string($_POST['audio_path3']);
            $audio_path4 = mysql_real_escape_string($_POST['audio_path4']);
            $audio_path5 = mysql_real_escape_string($_POST['audio_path5']);
            if (isset($_POST['c'])){
                $query = "INSERT INTO npc_responses SET trigger_id='$tid', response1='$response1', response2='$response2', response3='$response3', response4='$response4', response5='$response5', script='$script', prerequisite='$prerequisite', audio_path1='$audio_path1', audio_path2='$audio_path2', audio_path3='$audio_path3', audio_path4='$audio_path4', audio_path5='$audio_path5'";
            }else{
                $query = "UPDATE npc_responses SET response1='$response1', response2='$response2', response3='$response3', response4='$response4', response5='$response5', script='$script', prerequisite='$prerequisite', audio_path1='$audio_path1', audio_path2='$audio_path2', audio_path3='$audio_path3', audio_path4='$audio_path4', audio_path5='$audio_path5' WHERE trigger_id='$tid'";
            }
        }else if ($_POST['commit'] == "Create Sub-Trigger"){
            $tid_o = mysql_real_escape_string($_POST['trigger_id']);
            $trigger_text = mysql_real_escape_string($_POST['trigger_text']);
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
          $tid = GetNextId('npc_triggers');
          $query = "INSERT INTO npc_triggers (id, trigger_text, prior_response_required, area) VALUES ('$tid', '$trigger_text', '0', '$area')";
          $result = mysql_query2($query);
          $query = "INSERT INTO npc_responses (trigger_id) VALUES ('$tid')";
        }else if ($_POST['commit'] == "Create New KA Area")
        {  // This one is identical to the one above, save for the use of POST area instead of GET. (and of course the redirect)
            $area = mysql_real_escape_string($_POST['area']);
            $trigger_text = mysql_real_escape_string($_POST['trigger_text']);
            $tid = GetNextId('npc_triggers');
            $query = "INSERT INTO npc_triggers (id, trigger_text, prior_response_required, area) VALUES ('$tid', '$trigger_text', '0', '$area')";
            $result = mysql_query2($query);
            $query = "INSERT INTO npc_responses (trigger_id) VALUES ('$tid')";
            $result = mysql_query2($query);
            echo '<p class="error">Area Addition Successful</p>';
            unset($_GET);
            ka_trigger();
            exit();
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
        $query = "SELECT t.id, t.trigger_text, t.prior_response_required, r.id AS r_id, r.response1, r.response2, r.response3, r.response4, r.response5, r.script, r.prerequisite, r.audio_path1, r.audio_path2, r.audio_path3, r.audio_path4, r.audio_path5, o.trigger_text AS prior, o.area as prior_area, r.trigger_id FROM npc_triggers AS t LEFT JOIN npc_responses AS r ON t.id=r.trigger_id LEFT JOIN npc_triggers AS o ON t.prior_response_required=o.id WHERE t.area='$area'";
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
          echo '<td rowspan="'.(mysql_num_rows($result)+2).'">The following NPC use this KA:<br />';
          $query2 = "SELECT c.id, c.name FROM npc_knowledge_areas AS nka LEFT JOIN characters AS c ON c.id=nka.player_id WHERE area='$area'";
          $result2 = mysql_query2($query2);
          while ($row2 = mysql_fetch_array($result2, MYSQL_ASSOC)) 
          {
            echo '<a href="./index.php?do=npc_details&sub=kas&npc_id='.$row2['id'].'">'.$row2['name'].'</a><br />';
          }
          // npc list
          echo '</td>';
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
                echo 'Audio Path 1: <input type="text" name="audio_path1" value="'.$row['audio_path1'].'"><br/>';
                echo 'Response 2: <textarea name="response2" rows="3" cols="30">'.$row['response2'].'</textarea><br/>';
                echo 'Audio Path 2: <input type="text" name="audio_path2" value="'.$row['audio_path2'].'"><br/>';
                echo 'Response 3: <textarea name="response3" rows="3" cols="30">'.$row['response3'].'</textarea><br/>';
                echo 'Audio Path 3: <input type="text" name="audio_path3" value="'.$row['audio_path3'].'"><br/>';
                echo 'Response 4: <textarea name="response4" rows="3" cols="30">'.$row['response4'].'</textarea><br/>';
                echo 'Audio Path 4: <input type="text" name="audio_path4" value="'.$row['audio_path4'].'"><br/>';
                echo 'Response 5: <textarea name="response5" rows="3" cols="30">'.$row['response5'].'</textarea><br/>';
                echo 'Audio Path 5: <input type="text" name="audio_path5" value="'.$row['audio_path5'].'"><br/>';
                echo '<hr/>';
                

                echo 'Script: <textarea name="script">'.$row['script'].'</textarea><br/>';
                echo 'Prerequisite: <textarea name="prerequisite">'.$row['prerequisite'].'</textarea><br/>';
                if (isset($_GET['c'])){
                  echo '<input type="hidden" name="c" value="true">';
                }
                echo '<input type="submit" name="commit" value="Update Responses" /><hr/>';
                echo 'New Trigger:<input type="text" name="trigger_text" size="25" /><input type="submit" name="commit" value="Create Sub-Trigger" />';
                echo '</form>';
                echo '<table><tr><form action="index.php?do=ka_detail&area='.rawurlencode($area).'&responseid='.$row['r_id'].'" method="POST">';
                echo '<td>action</td><td>use only for train</td><td>quest name</td></tr>';
                echo '<tr><td>';

                echo '<select name="scriptelem">';
                echo '<option value="respond">Say one response</option>';
                echo '<option value="animgreet">Play greet animation</option>';
                echo '<option value="train">Train player</option>';
                echo '<option value="assignquest">Assign quest</option>';
                echo '<option value="completequest">Complete quest</option>';
                echo '<option value="offeritem">Give Item</option>';
                echo '<option value="giveexp">Give Exp</option>';
                echo '<option value="givemoney">Give Money</option>';
                echo '</select></td>';

                // skill field
                $query2 = 'select skill_id,name from skills ';
                $result2 = mysql_query2($query2);
                echo '<td><select name="skillname">';
                echo '<option value="empty"></option>';
                while ($line2 = mysql_fetch_array($result2, MYSQL_NUM))
                {
                    echo '<option value="'.$line2[1].'">'.$line2[1].'</option>';
                }
                echo '</select></td>';

                // quest, items, exp fields
                echo '<td><input type="text" name="questname"></td></tr><tr><td>items id given</td><td>exp given</td><td>money (C,O,H,T)</td></tr>';
                echo '<tr><td><input type="text" name="offeritem"></td><td><input type="text" name="giveexp" size="6"></td><td><input type="text" name="givemoney" size="8"></td></TR>';
                echo '<tr><td><input type="submit" name="commit" value="add action to script"></td><td></td></tr></form></table>';
              }else{
                echo 'Response 1: '.htmlspecialchars($row['response1']).'<br/>';
                echo 'Audio Path 1: '.$row['audio_path1'].'<br/>';
                echo 'Response 2: '.htmlspecialchars($row['response2']).'<br/>';
                echo 'Audio Path 2: '.$row['audio_path2'].'<br/>';
                echo 'Response 3: '.htmlspecialchars($row['response3']).'<br/>';
                echo 'Audio Path 3: '.$row['audio_path3'].'<br/>';
                echo 'Response 4: '.htmlspecialchars($row['response4']).'<br/>';
                echo 'Audio Path 4: '.$row['audio_path4'].'<br/>';
                echo 'Response 5: '.htmlspecialchars($row['response5']).'<br/>';
                echo 'Audio Path 5: '.$row['audio_path5'].'<br/>';
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
