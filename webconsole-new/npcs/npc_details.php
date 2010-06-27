<?php
function npc_main(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_GET['npc_id'])){
      if (!isset($_POST['commit'])){
        $id = mysql_real_escape_string($_GET['npc_id']);
        $query = 'SELECT name, lastname, description, description_ooc, creation_info, description_life, npc_master_id, character_type, loc_sector_id, loc_x, loc_y, loc_z, loc_instance, loc_yrot, racegender_id, base_agility, base_strength, base_endurance, base_intelligence, base_will, base_charisma, base_hitpoints_max, base_mana_max, npc_impervious_ind, kill_exp, npc_spawn_rule, npc_addl_loot_category_id, creation_time, banker, statue FROM characters WHERE id='.$id;
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$id.'" method="post"><table>';
        echo '<tr><td>First Name/Last Name:</td><td><input type="text" name="first_name" value="'.$row['name'].'" />/<input type="text" name="last_name" value="'.$row['lastname'].'" /></td></tr>';
        echo '<tr><td>Description:</td><td><textarea name="description" rows="4" cols="50">'.$row['description'].'</textarea></td></tr>';
        echo '<tr><td>OOC Description:</td><td><textarea name="description_ooc" rows="4" cols="50">'.$row['description_ooc'].'</textarea></td></tr>';
        echo '<tr><td>Creation Info:</td><td><textarea name="creation_info" rows="4" cols="50">'.$row['creation_info'].'</textarea></td></tr>';
        echo '<tr><td>Life Description:</td><td><textarea name="description_life" rows="4" cols="50">'.$row['description_life'].'</textarea></td></tr>';
        if ($row['character_type'] > 0) // don't show for players
        {
            if ($row['npc_master_id'] == $id){
              echo '<tr><td>This NPC is not using a Template<br/>You can set the master NPC id to</td><td><input type="text" name="npc_master_id" value="'.$row['npc_master_id'].'" /></td></tr>';
            }else{
              echo '<tr><td>This NPC is using NPC <a href="./index.php?do=npc_details&amp;npc_id='.$row['npc_master_id'].'&amp;sub=main">'.$row['npc_master_id'].'</a> as a template<br/>You can set the master NPC id to </td><td><input type="text" name="npc_master_id" value="'.$row['npc_master_id'].'" /></td></tr>';
            }
        }
        else // show only for players (npcs are 0000-00-00 00:00:00
        {
            echo '<tr><td>Creation date/time</td><td>'.$row['creation_time'].'</td></tr>';
        }
        $Sectors = PrepSelect('sectorid');
        echo '<tr><td>Location:</td>';
        echo '<td>';
        echo '<table><tr><th>Sector</th><th>X</th><th>Y</th><th>Z</th><th>Rotation</th><th>Instance</th></tr>';
        echo '<tr><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', $row['loc_sector_id']).'</td><td><input type="text" name="loc_x" value="'.$row['loc_x'].'" size="5"/></td>';
        echo '<td><input type="text" name="loc_y" value="'.$row['loc_y'].'" size="5"/></td>';
        echo '<td><input type="text" name="loc_z" value="'.$row['loc_z'].'" size="5"/></td>';
        echo '<td><input type="text" name="loc_yrot" value="'.$row['loc_yrot'].'" size="5"/></td>';
        echo '<td><input type="text" name="loc_instance" value="'.$row['loc_instance'].'" size="5"/></td></tr></table></td></tr>';
        $Races = PrepSelect('races');
        echo '<tr><td>Race/Gender: </td><td>'.DrawSelectBox('races', $Races, 'racegender_id', $row['racegender_id']).'</td></tr>';
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
        echo '<tr><td>Stats:</td><td><table><tr><td>Agility:</td><td><input type="text" name="base_agility" value="'.$row['base_agility'].'" size="5"/></td></tr>';
        echo '<tr><td>Strength:</td><td><input type="text" name="base_strength" value="'.$row['base_strength'].'" size="5"/></td></tr>';
        echo '<tr><td>Endurance:</td><td><input type="text" name="base_endurance" value="'.$row['base_endurance'].'" size="5"/></td></tr>';
        echo '<tr><td>Intelligence:</td><td><input type="text" name="base_intelligence" value="'.$row['base_intelligence'].'" size="5"/></td></tr>';
        echo '<tr><td>Willpower:</td><td><input type="text" name="base_will" value="'.$row['base_will'].'" size="5"/></td></tr>';
        echo '<tr><td>Charisma:</td><td><input type="text" name="base_charisma" value="'.$row['base_charisma'].'" size="5"/></td></tr></table></td></tr>';
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
        echo '<tr><td>Base HP (0 for auto-calc)</td><td><input type="text" name="base_hitpoints_max" value="'.$row['base_hitpoints_max'].'" size="7" /></td></tr>';
        echo '<tr><td>Base Mana (0 for auto-calc)</td><td><input type="text" name="base_mana_max" value="'.$row['base_mana_max'].'" size="7" /></td></tr>';
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
        if ($row['character_type'] > 0) // don't show for players
        {
            echo '<tr><td>Invulnerable</td><td>';
            if ($row['npc_impervious_ind'] == "Y"){
              echo '<select name="npc_impervious_ind"><option value="N">False</option><option value="Y" selected="true">True</option></select>';
            }else{
              echo '<select name="npc_impervious_ind"><option value="N" selected="true">False</option><option value="Y">True</option></select>';
            }
            echo '</td></tr>';
        }
        echo '<tr><td>Experience</td><td><input type="text" name="kill_exp" value="'.$row['kill_exp'].'" size="7" /></td></tr>';
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
        if ($row['character_type'] == 1) // Only show for real NPCs
        {
            if ($row['banker'] == "1")
            {
                echo '<tr><td>Banker</td><td><input type="checkbox" name="banker" checked="checked" /> </td></tr>';
            }
            else
            {
              echo '<tr><td>Banker</td><td><input type="checkbox" name="banker" /> </td></tr>';
            }
        }
        if ($row['statue'] == "1")
        {
            echo '<tr><td>Statue</td><td><input type="checkbox" name="statue" checked="checked" /> </td></tr>';
        }
        else
        {
          echo '<tr><td>Statue</td><td><input type="checkbox" name="statue" /> </td></tr>';
        }
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
        if ($row['character_type'] > 0) // Don't show for players
        {
            $Spawns = PrepSelect('spawn');
            $Loots = PrepSelect('loot');
            echo '<tr><td>Spawn Rule</td><td>'.DrawSelectBox('spawn', $Spawns, 'npc_spawn_rule', $row['npc_spawn_rule'], TRUE).'</td></tr>';
            echo '<tr><td>Loot Rule</td><td>'.DrawSelectBox('loot', $Loots, 'npc_addl_loot_category_id', $row['npc_addl_loot_category_id'], TRUE).'</td></tr>';
            $Behaviours = PrepSelect('behaviour');
            $B_Regions = PrepSelect('b_region');
            $query = "SELECT npctype, region FROM sc_npc_definitions WHERE char_id='$id'";
            $r2 = mysql_query2($query);
            $row2 = mysql_fetch_array($r2, MYSQL_ASSOC);
            echo '<tr><td>Behaviour/Region</td><td>'.DrawSelectBox('behaviour', $Behaviours, 'sc_npctype', $row2['npctype']).'/'.DrawSelectBox('b_region', $B_Regions, 'sc_region', $row2['region']).'</td></tr>';
        }
        echo '<input type="hidden" name="char_type" value="'.$row['character_type'].'">';
        echo '</table><input type="submit" name="commit" value="update" /></form>';
      }else{
        $id = mysql_real_escape_string($_GET['npc_id']);
        $query = "UPDATE characters SET ";
        $description = mysql_real_escape_string($_POST['description']);
        $query .= "description = '$description', ";
        $firstname = mysql_real_escape_string($_POST['first_name']);
        $query .= "name = '$firstname', ";
        $lastname = mysql_real_escape_string($_POST['last_name']);
        $query .= "lastname = '$lastname', ";
        $description_ooc = mysql_real_escape_string($_POST['description_ooc']);
        $query .= "description_ooc = '$description_ooc', ";
        $creation_info = mysql_real_escape_string($_POST['creation_info']);
        $query .= "creation_info = '$creation_info', ";
        $description_life = mysql_real_escape_string($_POST['description_life']);
        $query .= "description_life = '$description_life', ";
        if ($_POST['char_type'] > 0) // Don't update for players
        {
            $npc_master_id = mysql_real_escape_string($_POST['npc_master_id']);
            $query .= "npc_master_id = '$npc_master_id', ";
        }
        $loc_sector_id = mysql_real_escape_string($_POST['loc_sector_id']);
        $query .= "loc_sector_id = '$loc_sector_id', ";
        $loc_x = mysql_real_escape_string($_POST['loc_x']);
        $query .= "loc_x = '$loc_x', ";
        $loc_y = mysql_real_escape_string($_POST['loc_y']);
        $query .= "loc_y = '$loc_y', ";
        $loc_z = mysql_real_escape_string($_POST['loc_z']);
        $query .= "loc_z = '$loc_z', ";
        $loc_yrot = mysql_real_escape_string($_POST['loc_yrot']);
        $query .= "loc_yrot = '$loc_yrot', ";
        $loc_instance = mysql_real_escape_string($_POST['loc_instance']);
        $query .= "loc_instance = '$loc_instance', ";
        $racegender_id = mysql_real_escape_string($_POST['racegender_id']);
        $query .= "racegender_id = '$racegender_id', ";
        $base_agility= mysql_real_escape_string($_POST['base_agility']);
        $query .= "base_agility = '$base_agility', ";
        $base_strength = mysql_real_escape_string($_POST['base_strength']);
        $query .= "base_strength = '$base_strength', ";
        $base_endurance = mysql_real_escape_string($_POST['base_endurance']);
        $query .= "base_endurance = '$base_endurance', ";
        $base_intelligence = mysql_real_escape_string($_POST['base_intelligence']);
        $query .= "base_intelligence = '$base_intelligence', ";
        $base_will = mysql_real_escape_string($_POST['base_will']);
        $query .= "base_will = '$base_will', ";
        $base_charisma = mysql_real_escape_string($_POST['base_charisma']);
        $query .= "base_charisma = '$base_charisma', ";
        $base_hitpoints_max = mysql_real_escape_string($_POST['base_hitpoints_max']);
        $query .= "base_hitpoints_max = '$base_hitpoints_max', ";
        $base_mana_max = mysql_real_escape_string($_POST['base_mana_max']);
        $query .= "base_mana_max = '$base_mana_max', ";
        if ($_POST['char_type'] > 0) // Don't update for players
        {
            $npc_impervious_ind = mysql_real_escape_string($_POST['npc_impervious_ind']);
            $query .= "npc_impervious_ind = '$npc_impervious_ind', ";
        }
        $kill_exp = mysql_real_escape_string($_POST['kill_exp']);
        $query .= "kill_exp = '$kill_exp', ";
        if ($_POST['char_type'] > 0) // Don't update for players
        {
            $npc_spawn_rule = mysql_real_escape_string($_POST['npc_spawn_rule']);
            $query .= "npc_spawn_rule = '$npc_spawn_rule', ";
            $npc_addl_loot_category_id = mysql_real_escape_string($_POST['npc_addl_loot_category_id']);
            $query .= "npc_addl_loot_category_id = '$npc_addl_loot_category_id', ";
            if (isset($_POST['banker'])) 
            {
                $query .= "banker = '1', ";
            }
            else
            {
                $query .= "banker = '0', ";
            }
        }
        if (isset($_POST['statue'])) 
        {
            $query .= "statue = '1' ";
        }
        else
        {
            $query .= "statue = '0' ";
        }
        $query .= "WHERE id='$id'";
        if ($_POST['char_type'] > 0) // Don't update for players
        {
            $sc_npctype = mysql_real_escape_string($_POST['sc_npctype']);
            $sc_region = mysql_real_escape_string($_POST['sc_region']);
            $query2 = "UPDATE sc_npc_definitions SET npctype='$sc_npctype', region='$sc_region' WHERE char_id='$id'";
            
        }
        $result = mysql_query2($query);
        if ($_POST['char_type'] > 0) // Don't update for players
        {
            $result = mysql_query2($query2);
        }
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        npc_main();
      }
    }else{
      echo '<p class="error">Error: No NPC Selected</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function npc_skills()
{
    if (checkaccess('npcs', 'edit'))
    {
        if (isset($_GET['npc_id']))
        {
            if (isset($_POST['commit']))
            {
                $id = mysql_real_escape_string($_GET['npc_id']);
                $skill_id = mysql_real_escape_string($_POST['skill_id']);
                $query = '';
                if ($_POST['commit'] == 'Remove')
                {
                    $query = "DELETE FROM character_skills WHERE character_id='$id' AND skill_id='$skill_id'";
                }
                else if($_POST['commit'] == 'Add Skill')
                {
                    $skill_rank = mysql_real_escape_string($_POST['skill_rank']);
                    $query = "INSERT INTO character_skills (character_id, skill_id, skill_rank) VALUES ('$id', '$skill_id', '$skill_rank') ON DUPLICATE KEY UPDATE skill_rank='$skill_rank'";
                }
                else if($_POST['commit'] == 'Edit')
                {
                    $skill_rank = mysql_real_escape_string($_POST['skill_rank']);
                    $skill_Z = mysql_real_escape_string($_POST['skill_Z']);
                    $skill_Y = mysql_real_escape_string($_POST['skill_Y']);
                    $query = "UPDATE character_skills SET skill_rank='$skill_rank', skill_Z='$skill_Z', skill_Y='$skill_Y' WHERE character_id='$id' AND skill_id='$skill_id'";
                }
                else
                {
                    echo '<p class="error">Invalid commit!</p>';
                    return;
                }
                $result = mysql_query2($query);
                unset($_POST);
                echo '<p class="error">Update Successful</p>';
                npc_skills();
            }
            else
            {
                $Skill_result = PrepSelect('skill');
                while ($row = mysql_fetch_array($Skill_result, MYSQL_ASSOC))
                {
                    $s_id = $row['skill_id'];
                    $Skills[$s_id] = $row['name'];
                }
                $id = mysql_real_escape_string($_GET['npc_id']);
                $query = 'SELECT skill_id, skill_Z, skill_Y, skill_rank FROM character_skills WHERE character_id='.$id.' ORDER BY skill_id';
                $result = mysql_query2($query);
                echo '<table border="1"><tr><th>Skill</th><th>Rank</th><th>Skill Z</th><th>Skill Y</th><th>Actions</th></tr>';
                if (mysql_num_rows($result) > 0)
                {
                    while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
                    {
                        $s_id = $row['skill_id'];
                        echo '<tr><td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=skills" method="post">';
                        echo '<input type="hidden" name="skill_id" value="'.$s_id.'" />'.$Skills[$s_id].'</td>';
                        echo '<td><input type="text" size="9" name="skill_rank" value="'.$row['skill_rank'].'" /></td>';
                        echo '<td><input type="text" size="9" name="skill_Z" value="'.$row['skill_Z'].'" /></td>';
                        echo '<td><input type="text" size="9" name="skill_Y" value="'.$row['skill_Y'].'" /></td>';
                        echo '<td><input type="submit" name="commit" value="Edit" /></form>';
                        echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=skills" method="post">';
                        echo '<input type="hidden" name="skill_id" value="'.$s_id.'" /><input type="submit" name="commit" value="Remove" /></form></td></tr>';
                    }
                    echo '</table>';
                }
                else
                {
                    echo '</table>';
                    echo '<p class="error">NPC has no skills</p>';
                }
                echo '<p>Add a Skill to this NPC</p>';
                echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=skills" method="post">';
                echo '<table border="1"><tr><th>Skill</th><th>Rank</th><th>Actions</th></tr>';
                echo '<tr><td>'.DrawSelectBox('skill', $Skill_result, 'skill_id', '').'</td><td><input type="text" name="skill_rank" size="7" /></td><td>';
                echo '<input type="submit" name="commit" value="Add Skill" /></td></tr></table></form>';
            }
        }
        else
        {
            echo '<p class="error">Error: No NPC Selected</p>';
        }
    }else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function npc_traits(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_GET['npc_id'])){
      $id = mysql_real_escape_string($_GET['npc_id']);
      if (isset($_POST['commit'])){
        $trait_id = mysql_real_escape_string($_POST['trait_id']);
        if ($_POST['commit'] == "Remove"){
          $query = "DELETE FROM character_traits WHERE character_id='$id' AND trait_id='$trait_id'";
        }else if ($_POST['commit'] == "Add"){
          $query = "INSERT INTO character_traits (character_id, trait_id) VALUES ('$id', '$trait_id')";
        }
        mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        npc_traits();
      }else{
        $query = "SELECT racegender_id FROM characters WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $race = $row['racegender_id'];
        $query = "SELECT id, location, name, cstr_mesh, cstr_material, cstr_texture, shader FROM traits WHERE race_id='$race'";
        $Traits_Result = mysql_query2($query);
        while ($row = mysql_fetch_array($Traits_Result)){
          $t_id = $row['id'];
          $Traits["$t_id"]['id']=$t_id;
          $Traits["$t_id"]['location'] = $row['location'];
          $Traits["$t_id"]['name'] = $row['name'];
          $Traits["$t_id"]['cstr_mesh'] = $row['cstr_mesh'];
          $Traits["$t_id"]['cstr_material'] = $row['cstr_material'];
          $Traits["$t_id"]['cstr_texture'] = $row['cstr_texture'];
          $Traits["$t_id"]['shader'] = $row['shader'];
        }
        $query = "SELECT trait_id FROM character_traits WHERE character_id='$id'";
        $result = mysql_query2($query);
        if (mysql_num_rows($result) > 0){
          echo '<table border="1"><tr><th>Location</th><th>Name</th><th>Image</th><th>Mesh</th><th>Material</th><th>Actions</th></tr>';
          while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            $t_id = $row['trait_id'];
            echo '<tr><td>'.$Traits["$t_id"]['location'].'</td>';
            echo '<td>'.$Traits["$t_id"]['name'].'</td>';
            echo '<td>'.$Traits["$t_id"]['cstr_texture'].'</td>';
            echo '<td>'.$Traits["$t_id"]['cstr_mesh'].'</td>';
            echo '<td>'.$Traits["$t_id"]['cstr_material'].'</td>';
            echo '<td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=traits" method="post"><input type="hidden" name="trait_id" value="'.$t_id.'" /><input type="submit" name="commit" value="Remove" /></form></td></tr>';
          }
          echo '</table>';
        }else{
          echo '<p class="error">NPC has no traits</p>';
        }
        if (mysql_num_rows($Traits_Result) > 0){
          echo '<p>Add a new Trait</p>';
          mysql_data_seek($Traits_Result, 0);
          echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=traits" method="post"><select name="trait_id">';
          while ($row = mysql_fetch_array($Traits_Result, MYSQL_ASSOC)){
            echo '<option value="'.$row['id'].'">Name='.$row['name'].' Location='.$row['location'];
            echo '</option>';
          }
          echo '</select><input type="submit" name="commit" value="Add" /></form>';
        }else{
          echo '<p class="error">No appropriate traits to add to this NPC</p>';
        }
      }
    }else{
      echo '<p class="error">Error: No npc id</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function npc_kas(){
  if (checkaccess('npcs','edit')){
    if (isset($_GET['npc_id'])){
      $id = mysql_real_escape_string($_GET['npc_id']);
      if (isset($_POST['commit'])){
        $area = mysql_real_escape_string($_POST['area']);
        if (isset($_POST['priority'])){
          $priority = mysql_real_escape_string($_POST['priority']);
        }
        if ($_POST['commit'] == 'Remove'){
          $query = "DELETE FROM npc_knowledge_areas WHERE player_id='$id' AND area='$area'";
        }else if ($_POST['commit'] == 'Update Priority'){
          $query = "UPDATE npc_knowledge_areas SET priority='$priority' WHERE player_id='$id' AND area='$area'";
        }else if ($_POST['commit'] == 'Add KA'){
          if ($area == "SELF"){
            $query = "SELECT name, lastname FROM characters WHERE id='$id'";
            $result = mysql_query2($query);
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $name = $row['name'];
            if ($row['lastname'] != ""){
              $name = $name . ' ' .$row['lastname'];
            }
            $query = "INSERT INTO npc_knowledge_areas (player_id, area, priority) VALUES ('$id', '$name', '$priority')";
          }else{
            $query = "INSERT INTO npc_knowledge_areas (player_id, area, priority) VALUES ('$id', '$area', '$priority')";
          }
        }
        $result = mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        npc_kas();
      }else{
        $query = "SELECT area, priority FROM npc_knowledge_areas WHERE player_id='$id' ORDER BY priority";
        $result = mysql_query2($query);
        if (mysql_num_rows($result) > 0){
          echo '<table border="1">';
          echo '<tr><th>Area</th><th>Priority</th><th>Actions</th></tr>';
          while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            echo '<tr>';
            echo '<td><a href="./index.php?do=ka_detail&area='.rawurlencode($row['area']).'">'.$row['area'].'</a></td>';
            echo '<td>'.$row['priority'].'</td>';
            echo '<td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=kas" method="post">';
            echo '<input type="hidden" name="area" value="'.$row['area'].'" />';
            echo '<input type="submit" name="commit" value="Remove" /><br/>';
            echo '<input type="submit" name="commit" value="Update Priority" /><select name="priority">';
            $i = 1;
            while ($i <= 10){
            echo '<option value="'.$i.'"';
            if ($i == $row['priority']){
              echo ' selected="true"';
            }
            echo '>'.$i.'</option>';
            $i++;
            }
            echo '</select>';
            echo '</form></td>';
            echo '</tr>';
          }
          echo '</table>';
        }else{
          echo '<p class="error">NPC has no KAs Assigned</p>';
        }
        $query = "SELECT DISTINCT area FROM npc_triggers ORDER BY area";
        $result = mysql_query2($query);
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        $areas[] = $row['area'];
        }
        $query = "SELECT DISTINCT name, lastname FROM characters WHERE character_type=1 ORDER by name";
        $result = mysql_query2($query);
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
        if ($row['lastname'] == ""){
          $names[] = $row['name'];
        }else{
          $names[] = $row['name'].' '.$row['lastname'];
        }
        }
        $display = array_values(array_diff($areas, $names));
        echo '<p>Add a Knowledge Area to this NPC:</p><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=kas" method="post">';
        echo '<select name="area">';
        echo '<option value="SELF">[Add KA of this NPC]</option>';
        foreach ($display as $name){
        echo '<option value="'.$name.'">'.$name.'</option>';
        }
        echo '</select>';
        echo '<select name="priority">';
        $i = 1;
        while ($i <= 10){
        echo '<option value="'.$i.'">'.$i.'</option>';
        $i++;
        }
        echo '</select>';
        echo '<input type="submit" name="commit" value="Add KA" />';
        echo '</form>';
      }
    }else{
      echo '<p class="error">Error: No npc id</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function npc_items(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_GET['npc_id'])){
      $id = mysql_real_escape_string($_GET['npc_id']);
      if (isset($_POST['commit'])){
        if ($_POST['commit'] == 'Remove'){
          $inst_id = mysql_real_escape_string($_POST['id']);
          $query = "DELETE FROM item_instances WHERE id='$inst_id'";
        }else if ($_POST['commit'] == 'Change Location'){
          $inst_id = mysql_real_escape_string($_POST['id']);
          $slot = mysql_real_escape_string($_POST['slot']);
          $query = "UPDATE item_instances SET location_in_parent='$slot', WHERE id='$inst_id'";
        }else if ($_POST['commit'] == 'Add'){
          $item = mysql_real_escape_string($_POST['item_id']);
          $query = "SELECT MAX(location_in_parent) AS loc FROM item_instances WHERE char_id_owner='$id' AND location_in_parent>15";
          $result = mysql_query2($query);
          $location = '';
          $row = mysql_fetch_array($result, MYSQL_ASSOC);
          $location = $row['loc'];
          $location = ($location == null ? 15 : $location) + 1;
          if ($location > 47){
            $location = 47;
          }

          $query = "SELECT item_max_quality FROM item_stats WHERE id = '$item'";
          $result = mysql_query2($query);
          $row = mysql_fetch_array($result);
          $quality = $row['item_max_quality'];
          $query = "INSERT INTO item_instances (char_id_owner, location_in_parent, stack_count, item_stats_id_standard, item_quality, crafted_quality) VALUES ('$id', $location, '1', '$item', '$quality', '$quality')";
        }else if ($_POST['commit'] == 'Update'){
          $stack_count = mysql_real_escape_string($_POST['stack_count']);
          $item_quality = mysql_real_escape_string($_POST['item_quality']);
          $inst_id = mysql_real_escape_string($_POST['id']);
          $query = "UPDATE item_instances SET stack_count='$stack_count', item_quality='$item_quality', crafted_quality='$item_quality' WHERE id='$inst_id'";
        }
        unset($_POST);
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        npc_items();
      }else{
        $query = "SELECT i.id, i.location_in_parent, i.stack_count, i.item_quality, i.item_stats_id_standard, s.name, s.valid_slots FROM item_instances AS i LEFT JOIN item_stats as s ON s.id=i.item_stats_id_standard WHERE i.char_id_owner='$id' ORDER BY s.name";
        $result = mysql_query2($query);
        if (mysql_num_rows($result) > 0){
          echo '<table border=1><tr><th>Item</th><th>Location</th><th>Count/Quality</th><th>Functions</th></tr>';
          while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            echo '<tr>';
            echo '<td>'.$row['name'].'</td>';
            echo '<td>'.LocationToString($row['location_in_parent']).'</td>';
            echo '<td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=items" method="post"><input type="text" name="stack_count" value="'.$row['stack_count'].'" size="3"/><input type="text" name="item_quality" value="'.$row['item_quality'].'" size="3"/><input type="hidden" name="id" value="'.$row['id'].'"/><input type="submit" name="commit" value="Update"/></form></td>';
            echo '<td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=items" method="post">';
            echo '<input type="hidden" name="id" value="'.$row['id'].'"/>';
            echo '<input type="submit" name="commit" value="Remove" /><br/>';
            echo '<input type="submit" name="commit" value="Change Location" />';
            echo '<select name="slot">';
            $slots = preg_split("/[\s,]+/", $row['valid_slots']);
            foreach ($slots AS $slot){
              switch ($slot){
                case "BULK":
                  $i = 1;
                  while ($i <=32){
                    $j = $i+15;
                    if ($row['location_in_parent'] == $j) 
                    {
                        echo '<option value="'.$j.'" selected="selected">Bulk '.$i.'</option>';
                    }
                    else 
                    {
                        echo '<option value="'.$j.'">Bulk '.$i.'</option>';
                    }
                    $i++;
                  }
                  break;
                case "LEFTHAND":
                  if ($row['location_in_parent'] == 1)
                  {
                    echo '<option value="1" selected="selected">Left Hand</option>';
                  }
                  else 
                  {
                    echo '<option value="1">Left Hand</option>';
                  }
                  break;
                case "RIGHTHAND":
                  if($row['location_in_parent'] == 0)
                  {
                    echo '<option value="0" selected="selected">Right Hand</option>';
                  }
                  else
                  {
                    echo '<option value="0">Right Hand</option>';
                  }
                  break;
                case "BOTHHANDS":
                  if($row['location_in_parent'] == 2)
                  {
                    echo '<option value="2" selected="selected">Both Hands</option>';
                  }
                  else
                  {
                    echo '<option value="2">Both Hands</option>';
                  }
                  break;
                case "LEFTFINGER":
                  if($row['location_in_parent'] == 4)
                  {
                    echo '<option value="4" selected="selected">Left Finger</option>';
                  }
                  else
                  {
                    echo '<option value="4">Left Finger</option>';
                  }
                  break;
                case "RIGHTFINGER":
                  if($row['location_in_parent'] == 3)
                  {
                    echo '<option value="3" selected="selected">Right Finger</option>';
                  }
                  else
                  {
                    echo '<option value="3">Right Finger</option>';
                  }
                  break;
                case "NECK":
                  if($row['location_in_parent'] == 6)
                  {
                    echo '<option value="6" selected="selected">Neck</option>';
                  }
                  else
                  {
                    echo '<option value="6">Neck</option>';
                  }
                  break;
                case "BACK":
                  if($row['location_in_parent'] == 7)
                  {
                    echo '<option value="7" selected="selected">Back</option>';
                  }
                  else
                  {
                    echo '<option value="7">Back</option>';
                  }
                  break;
                case "BELT":
                  if($row['location_in_parent'] == 12)
                  {
                    echo '<option value="12" selected="selected">Belt</option>';
                  }
                  else
                  {
                    echo '<option value="12">Belt</option>';
                  }
                  break;
                case "BRACERS":
                  if($row['location_in_parent'] == 13)
                  {
                    echo '<option value="13" select="select">Bracers</option>';
                  }
                  else
                  {
                    echo '<option value="13">Bracers</option>';
                  }
                  break;
                case "TORSO":
                  if($row['location_in_parent'] == 14)
                  {
                    echo '<option value="14" selected="selected">Torso</option>';
                  }
                  else
                  {
                    echo '<option value="14">Torso</option>';
                  }
                  break;
                case "LEGS":
                  if($row['location_in_parent'] == 11)
                  {
                    echo '<option value="11" selected="selected">Legs</option>';
                  }
                  else
                  {
                    echo '<option value="11">Legs</option>';
                  }
                  break;
                case "HELM":
                  if($row['location_in_parent'] == 5)
                  {
                    echo '<option value="5" selected="selected">Helm</option>';
                  }
                  else
                  {
                    echo '<option value="5">Helm</option>';
                  }
                  break;
                case "GLOVES":
                  if($row['location_in_parent'] == 9)
                  {
                    echo '<option value="9" selected="selected">Gloves</option>';
                  }
                  else
                  {
                    echo '<option value="9">Gloves</option>';
                  }
                  break;
                case "BOOTS":
                  if($row['location_in_parent'] == 10)
                  {
                    echo '<option value="10" selected="selected">Boots</option>';
                  }
                  else
                  {
                    echo '<option value="10">Boots</option>';
                  }
                  break;
                case "ARMS":
                  if($row['location_in_parent'] == 8)
                  {
                    echo '<option value="8" selected="selected">Arms</option>';
                  }
                  else
                  {
                    echo '<option value="8">Arms</option>';
                  }
                  break;
                case "MIND":
                  if($row['location_in_parent'] == 15)
                  {
                    echo '<option value="15" selected="selected">Mind</option>';
                  }
                  else
                  {
                    echo '<option value="15">Mind</option>';
                  }
                  break;
              }
            }
            echo '</select>';
            echo '</form></td>';
            echo '</tr>';
          }
          echo '</table>';
        }else{
          echo '<p class="error">NPC Has no items in Inventory</p>';
        }
        echo '<p>Add a new Item to this NPC</p>';
        $Items = PrepSelect('items');
        echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=items" method="post">';
        echo DrawSelectBox('items', $Items, 'item_id', '');
        echo '<input type="submit" name="commit" value="Add" />';
        echo '</form>';
      }
    }else{
      echo '<p class="error">Error: No npc id</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function npc_training(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_GET['npc_id'])){
      $id = mysql_real_escape_string($_GET['npc_id']);
      if (isset($_POST['commit'])){
        if ($_POST['commit'] == "Remove"){
          $skill_id = mysql_real_escape_string($_POST['skill_id']);
          $query = "DELETE FROM trainer_skills WHERE skill_id='$skill_id' AND player_id='$id'";
        }else if ($_POST['commit'] == "Add"){
          $skill_id = mysql_real_escape_string($_POST['skill_id']);
          $min_rank = mysql_real_escape_string($_POST['min_rank']);
          $max_rank = mysql_real_escape_string($_POST['max_rank']);
          $min_faction = mysql_real_escape_string($_POST['min_faction']);
          $query = "INSERT INTO trainer_skills (player_id, skill_id, min_rank, max_rank, min_faction) VALUES ('$id', '$skill_id', '$min_rank', '$max_rank', '$min_faction') ON DUPLICATE KEY UPDATE min_rank='$min_rank', max_rank='$max_rank', min_faction='$min_faction'";
        }
        $result = mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        npc_training();
      }else{
        $query = "SELECT t.skill_id, t.min_rank, t.max_rank, t.min_faction, s.name FROM trainer_skills AS t LEFT JOIN skills AS s ON t.skill_id=s.skill_id WHERE t.player_id='$id'";
        $result = mysql_query2($query);
        if (mysql_num_rows($result) == 0){
          echo '<p class="error">NPC is not currently a trainer</p>';
        }else{
          echo '<table border="1">';
          echo '<tr><th>Skill</th><th>Min Rank</th><th>Max Rank</th><th>Min Faction</th><th>Actions</th></tr>';
          while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            echo '<tr>';
            echo '<td>'.$row['name'].'</td>';
            echo '<td>'.$row['min_rank'].'</td>';
            echo '<td>'.$row['max_rank'].'</td>';
            echo '<td>'.$row['min_faction'].'</td>';
            echo '<td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=training" method="post"><input type="hidden" name="skill_id" value="'.$row['skill_id'].'"><input type="submit" name="commit" value="Remove" /></form></td>';
            echo '</tr>';
          }
          echo '</table>';
        }
        echo '<p>Add/Replace a Training Skill to this NPC:</p>';
        $Skills = PrepSelect('skill');
        echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=training" method="post">';
        echo '<table border="1"><tr><th>Skill</th><th>Min Rank</th><th>Max Rank</th><th>Min Faction</th><th>Actions</th></tr><tr>';
        echo '<td>'.DrawSelectBox('skill', $Skills, 'skill_id', '').'</td>';
        echo '<td><input type="text" name="min_rank" size="5" value="0"/></td>';
        echo '<td><input type="text" name="max_rank" size="5" value="0"/></td>';
        echo '<td><input type="text" name="min_faction" size="5" value="0"/></td>';
        echo '<td><input type="submit" name="commit" value="Add"/></td>';
        echo '</tr></table>';
        echo '</form>';
      }
    }else{
      echo '<p class="error">Error: No npc id</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function npc_merchant(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_GET['npc_id'])){
      $id = mysql_real_escape_string($_GET['npc_id']);
      if (isset($_POST['commit'])){
        $category_id = mysql_real_escape_string($_POST['category_id']);
        if ($_POST['commit'] == 'Remove'){
          $query = "DELETE FROM merchant_item_categories WHERE category_id = '$category_id' AND player_id = '$id'";
        }else if ($_POST['commit'] == 'Add'){
          $query = "INSERT INTO merchant_item_categories (player_id, category_id) VALUES ('$id', '$category_id')";
        }
        $result = mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        npc_merchant();
      }else{
        $query = "SELECT m.category_id, c.name FROM merchant_item_categories AS m LEFT JOIN item_categories AS c ON m.category_id = c.category_id WHERE m.player_id = '$id'";
        $result = mysql_query2($query);
        if (mysql_num_rows($result) == 0){
          echo '<p class="error">This NPC is not currently a Merchant</p>';
        }else{
          echo '<table border="1"><tr><th>Category</th><th>Actions</th></tr>';
          while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            echo '<tr>';
            echo '<td>'.$row['name'].'</td>';
            echo '<td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=merchant" method="post">';
            echo '<input type="hidden" name="category_id" value="'.$row['category_id'].'" />';
            echo '<input type="submit" name="commit" value="Remove" />';
            echo '</form></td>';
            echo '</tr>';
          }
          echo '</table>';
        }
        $Categories = PrepSelect('category');
        echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=merchant" method="post"><p>Add a merchant category to this NPC:';
        echo DrawSelectBox('category', $Categories, 'category_id', '');
        echo '<input type="submit" name="commit" value="Add"/>';
        echo '</p></form>';
      }
    }else{
      echo '<p class="error">Error: No npc id</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function npc_specific(){
  if (checkaccess('npcs', 'edit')){
    if (isset($_GET['npc_id'])){
      $id = mysql_real_escape_string($_GET['npc_id']);
      if (isset($_POST['commit'])){
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
          $query = "INSERT INTO npc_triggers (id, trigger_text, prior_response_required, area) VALUES ('$tid', '$trigger_text', '$tid_o', '$npcname')";
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
          $query = "INSERT INTO npc_triggers (id, trigger_text, prior_response_required, area) VALUES ('$tid', '$trigger_text', '0', '$npcname')";
          $result = mysql_query2($query);
          $query = "INSERT INTO npc_responses (trigger_id) VALUES ('$tid')";
        }
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        npc_specific();
      }else{
        $query = "SELECT name, lastname FROM characters WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $npcname = $row['name'];
        if ($row['lastname'] != ""){
          $npcname = $npcname . ' ' . $row['lastname'];
        }
        $query = "SELECT t.id, t.trigger_text, t.prior_response_required, r.response1, r.response2, r.response3, r.response4, r.response5, r.script, r.prerequisite, o.trigger_text AS prior, o.area as prior_area, r.trigger_id FROM npc_triggers AS t LEFT JOIN npc_responses AS r ON t.id=r.trigger_id LEFT JOIN npc_triggers AS o ON t.prior_response_required=o.id WHERE t.area='$npcname'";
        if (isset($_GET['trigger'])){
          $t = mysql_real_escape_string($_GET['trigger']);
          $query = $query . " ORDER BY t.id IN ('$t') DESC";
        }else{
          $query = $query . " ORDER BY t.id";
        }
        $result = mysql_query2($query);
        if (mysql_num_rows($result) == 0){
          echo '<p class="error">NPC has no Specific KAs</p>';
        }else{
          echo '<table border="1"><tr><th>Trigger</th><th>Response</th><th>Action</th></tr>';
          while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
            $t_id = $row['id'];
            echo '<tr>';
            echo '<td>';
            if (isset($t) && ($t == $t_id)){
              echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=specific" method="post">';
              echo '<input type="hidden" name="trigger_id" value="'.$row['id'].'" />';
              if ($row['prior'] != ''){
                echo 'Prior Response: '.$row['prior'].'<br/>';
                if ($row['prior_area'] != $npcname){
                  echo 'From KA: '.$row['prior_area'].'<br/>';
                }
              }
              echo '<input type="text" name="trigger_text" value="'.htmlspecialchars($row['trigger_text']).'"/><br/>';
              echo '<input type="submit" name="commit" value="Update Trigger" /></form></td>';
            }else{
              echo '<a href="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=specific&amp;trigger='.$t_id;
              if ($row['trigger_id'] == ''){
                echo '&amp;c=true';
              }
              echo '">'.htmlspecialchars($row['trigger_text']).'</a></td>';
            }
            echo '<td>';
            if (isset($t) && ($t == $t_id)){
              echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=specific" method="post">';
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
              if ($row['trigger_id'] == ''){
                echo '&nbsp;';
              }else{
                echo '<a href="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=specific&amp;trigger='.$t_id.'">+</a>';
              }
            }
            echo '</td><td>';
            echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=specific" method="post">';
            echo '<input type="hidden" name="trigger_id" value="'.$row['id'].'" />';
            echo '<input type="submit" name="commit" value="Remove" />';
            echo '</form></td></tr>'."\n";
          }
          echo '<tr><td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=specific" method="post">Create New Trigger:<br/><input type="text" name="trigger_text" /><br/><input type="submit" name="commit" value="Create New Trigger" /></form></td>';
          echo '<td>&nbsp;</td><td>&nbsp;</td>';
          echo '</tr></table>';
        }
       //add new here
      }
    }else{
      echo '<p class="error">Error: No npc id</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function npcdetails(){
  if (checkaccess('npcs', 'edit')){
    $uri_string = './index.php?do=npc_details';
    if (isset($_GET['npc_id'])){
      if (is_numeric($_GET['npc_id'])){
        $id = mysql_real_escape_string($_GET['npc_id']);
        $query = "SELECT name, lastname, character_type FROM characters WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        //echo '<p class="bold">NPC: '.$id.' - '.$row['name'].' '.$row['lastname'].'</p>';
        echo '<form action="index.php?do=deletenpc&id='.$id.'" method="post" style="margin-bottom: 20px; margin-top: 20px;">';
        echo '<p class="bold" style="float: left; margin: 0pt 5px 0pt 0pt;">NPC: '.$id.' - '.$row['name'].' '.$row['lastname'].'</p>';
        echo '<input type="submit" value="delete NPC"></form>';
        $uri_string = $uri_string.'&amp;npc_id='.$_GET['npc_id'];
      }
    }
    echo '<div class="menu_npc">';
    echo '<a href="'.$uri_string.'&amp;sub=main">Main</a><br/>';
    echo '<a href="'.$uri_string.'&amp;sub=skills">skills</a><br/>';
    echo '<a href="'.$uri_string.'&amp;sub=traits">traits</a><br/>';
    if ($row['character_type'] > 0)   // don't display for players
    {
        echo '<a href="'.$uri_string.'&amp;sub=kas">KA\'s</a><br/>';
    }
    echo '<a href="'.$uri_string.'&amp;sub=items">items</a><br/>';
    if ($row['character_type'] > 0)   // don't display for players
    {
        echo '<a href="'.$uri_string.'&amp;sub=training">training</a><br/>';
        echo '<a href="'.$uri_string.'&amp;sub=merchant">merchant</a><br/>';
        echo '<a href="'.$uri_string.'&amp;sub=specific">Specific KA\'s</a><br/>';
    }
    echo '</div><div class="main_npc">';
    if (isset($_GET['sub'])){
      switch ($_GET['sub']){
        case 'main':
          npc_main();
          break;
        case 'skills':
          npc_skills();
          break;
        case 'traits':
          npc_traits();
          break;
        case 'kas':
          npc_kas();
          break;
        case 'items':
          npc_items();
          break;
        case 'training';
          npc_training();
          break;
        case 'merchant':
          npc_merchant();
          break;
        case 'specific':
          npc_specific();
          break;
        default:
          echo '<p class="error">Please Select an Action</p>';
      }
    }else{
      echo '<p class="error">Please Select an Action</p>';
    }
    echo '</div>';
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
