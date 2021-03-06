<?php
function npc_main()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (!isset($_GET['npc_id']))
    {
        echo '<p class="error">Error: No NPC Selected</p>';
    }
    // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) {
        echo '<p class="error">You are not authorized to edit NPCs</p>';
        return;
    }
    if (!isset($_POST['commit']))
    {
        $enumCharType = array('NPC', 'PET', 'MOUNT');
        $makeEnumDropdown = function ($name, $enumArray, $selected = -1) 
        {
            $output = '';
            $output .= '<select name="'.$name.'">';
            foreach ($enumArray as $key => $value)
            { // +1 because we want to skip the real element 0 (players), which we don't want to be able to change to.
                $output .= '<option value="'.($key + 1).'" '.(($key + 1) == $selected ? 'selected="selected"' : '').'>'.$value.'</option>';
            }
            $output .= '</select>';
            return $output;
        };
        $id = escapeSqlString($_GET['npc_id']);
        $query = 'SELECT name, lastname, description, description_ooc, creation_info, description_life, npc_master_id, character_type, loc_sector_id, loc_x, loc_y, loc_z, loc_instance, loc_yrot, racegender_id, base_hitpoints_max, base_mana_max, npc_impervious_ind, kill_exp, npc_spawn_rule, npc_addl_loot_category_id, creation_time, banker, statue FROM characters WHERE id='.$id;
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo '<form action="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$id.'" method="post" id="npc_details_form"><table>';
        echo '<tr><td>First Name/Last Name:</td><td><input type="text" name="first_name" value="'.$row['name'].'" />/<input type="text" name="last_name" value="'.$row['lastname'].'" /><span id="lastNameWarning" class="warning"></span></td></tr>';
        echo '<tr><td>Description:</td><td><textarea name="description" rows="4" cols="50">'.$row['description'].'</textarea></td></tr>';
        echo '<tr><td>OOC Description:</td><td><textarea name="description_ooc" rows="4" cols="50">'.$row['description_ooc'].'</textarea></td></tr>';
        echo '<tr><td>Creation Info:</td><td><textarea name="creation_info" rows="4" cols="50">'.$row['creation_info'].'</textarea></td></tr>';
        echo '<tr><td>Life Description:</td><td><textarea name="description_life" rows="4" cols="50">'.$row['description_life'].'</textarea></td></tr>';
        if ($row['character_type'] > 0) // don't show for players
        {
            $masterText = "Setting a master ID means the following data are loaded from the master instead: \n";
            $masterText .= "* Optionally base HP/mana \n";
            $masterText .= "* Skills \n";
            $masterText .= "* Traits \n";
            $masterText .= "* Inventory \n";
            $masterText .= "* Variables \n";
            $masterText .= "* Merchant status \n";
            $masterText .= "* Trainer status \n";
            $masterText .= "* Spells \n";
            if ($row['npc_master_id'] == $id)
            {
                echo '<tr><td>This NPC is not using a Template<br/>You can set the <span title="'.$masterText.'" style="text-decoration: underline;">master</span> NPC id to</td><td><input type="text" name="npc_master_id" value="'.$row['npc_master_id'].'" /></td></tr>';
            }
            else
            {
                echo '<tr><td>This NPC is using NPC <a href="./index.php?do=npc_details&amp;npc_id='.$row['npc_master_id'].'&amp;sub=main">'.$row['npc_master_id'].'</a> as a template<br/>You can set the <span title="'.$masterText.'" style="text-decoration: underline;">master</span> NPC id to </td><td><input type="text" name="npc_master_id" value="'.$row['npc_master_id'].'" /></td></tr>';
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
        if ($row['character_type'] > 0) // Don't show for players
        {
            echo '<tr><td>Character Type</td><td>'.$makeEnumDropdown('character_type', $enumCharType, $row['character_type']).'</td></tr>';
        }
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
        echo '<tr><td>Base HP (0 for auto-calc)</td><td><input type="text" name="base_hitpoints_max" value="'.$row['base_hitpoints_max'].'" size="18" /> ';
        echo '<input type="checkbox" name="base_hitpoints_null" onclick="changeHitpointsText()" /> Use Master Value.</td></tr>';
        echo '<tr><td>Base Mana (0 for auto-calc)</td><td><input type="text" name="base_mana_max" value="'.$row['base_mana_max'].'" size="18" /> ';
        echo '<input type="checkbox" name="base_mana_null"  onclick="changeManaText()" /> Use Master Value.</td></tr>';
        echo '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
        if ($row['character_type'] > 0) // don't show for players
        {
            echo '<tr><td>Invulnerable</td><td>';
            if ($row['npc_impervious_ind'] == "Y")
            {
                echo '<select name="npc_impervious_ind"><option value="N">False</option><option value="Y" selected="selected">True</option></select>';
            }
            else
            {
                echo '<select name="npc_impervious_ind"><option value="N" selected="selected">False</option><option value="Y">True</option></select>';
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
            $row2 = fetchSqlAssoc($r2);
            echo '<tr><td>Behaviour/Region</td><td>'.DrawSelectBox('behaviour', $Behaviours, 'sc_npctype', $row2['npctype']).'/'.DrawSelectBox('b_region', $B_Regions, 'sc_region', $row2['region'], true).'</td></tr>';
        }
        echo '<tr><td colspan="2"><input type="hidden" name="char_type" value="'.$row['character_type'].'" /><input type="submit" name="commit" value="update" /></td></tr>';
        echo '</table></form>';
        // this javascript applies to the code above. Needs to be after the form in order to reference it.
        echo '<script type="text/javascript">//<![CDATA[
                var old_hitpoints = 0;
                var old_manapoints = 0;
                function changeHitpointsText()
                {
                    if (document.getElementById("npc_details_form").base_hitpoints_null.checked) 
                    {
                        old_hitpoints = document.getElementById("npc_details_form").base_hitpoints_max.value;
                        document.getElementById("npc_details_form").base_hitpoints_max.value = "Using Master Value";
                    }
                    else
                    {
                        document.getElementById("npc_details_form").base_hitpoints_max.value = old_hitpoints;
                    }
                }
                function changeManaText()
                {
                    if (document.getElementById("npc_details_form").base_mana_null.checked) 
                    {
                        old_manapoints = document.getElementById("npc_details_form").base_mana_max.value
                        document.getElementById("npc_details_form").base_mana_max.value = "Using Master Value";
                    }
                    else
                    {
                        document.getElementById("npc_details_form").base_mana_max.value = old_manapoints;
                    }
                }
                function changeLastName()
                {
                    if (document.getElementById("npc_details_form").last_name.value.indexOf(" ") >= 0)
                    {
                        document.getElementById("npc_details_form").last_name.value = document.getElementById("npc_details_form").last_name.value.replace(/ /g, "")
                        document.getElementById("lastNameWarning").innerHTML = "You cannot use spaces in last name, they were automatically removed.";
                    }
                }
                document.getElementById("npc_details_form").last_name.addEventListener("input", changeLastName);
                ';
        if ($row['base_hitpoints_max'] == null)
        {
            echo 'document.getElementById("npc_details_form").base_hitpoints_null.click();';
        }
        if ($row['base_mana_max'] == null)
        {
            echo 'document.getElementById("npc_details_form").base_mana_null.click();';
        }
        echo ' //]]></script>'."\n"; // End of java script started at previous comment.
    }
    else
    {
        if ($_POST['char_type'] > 0) // Don't check for players
        {
            if ($_POST['npc_master_id'] != 0 && $_POST['npc_master_id'] != $_GET['npc_id'])
            {
                $npc_master_id = escapeSqlString($_POST['npc_master_id']);
                $query = "SELECT id, npc_master_id, character_type FROM characters WHERE id = '$npc_master_id'";
                $result = mysql_query2($query);
                $row = fetchSqlAssoc($result);
                if (sqlNumRows($result) < 1)
                {
                    echo '<p class="error">Invalid npc_master_id, no such id exists.</p>';
                    return;
                }
                if ($row['npc_master_id'] != 0 && $row['id'] != $row['npc_master_id'])
                {
                    echo '<p class="error">Invalid npc_master_id, the target is refering to another master.</p>';
                    return;
                }
                if ($row['character_type'] == 0)
                {
                    echo '<p class="error">Invalid npc_master_id, target is a player.</p>';
                    return;
                }
            }
        }
        $id = escapeSqlString($_GET['npc_id']);
        $query = "UPDATE characters SET ";
        $description = escapeSqlString($_POST['description']);
        $query .= "description = '$description', ";
        $firstname = escapeSqlString($_POST['first_name']);
        $query .= "name = '$firstname', ";
        $lastname = escapeSqlString($_POST['last_name']);
        if (strpos($lastname, ' ') !== false)
        {
            echo '<p class="error">You can not use spaces in the NPC last name field, put multiple names in the Name field instead.</p>';
            return;
        }
        $query .= "lastname = '$lastname', ";
        $description_ooc = escapeSqlString($_POST['description_ooc']);
        $query .= "description_ooc = '$description_ooc', ";
        $creation_info = escapeSqlString($_POST['creation_info']);
        $query .= "creation_info = '$creation_info', ";
        $description_life = escapeSqlString($_POST['description_life']);
        $query .= "description_life = '$description_life', ";
        if ($_POST['char_type'] > 0) // Don't update for players
        {
            $npc_master_id = escapeSqlString($_POST['npc_master_id']);
            $query .= "npc_master_id = '$npc_master_id', ";
        }
        $loc_sector_id = escapeSqlString($_POST['loc_sector_id']);
        $query .= "loc_sector_id = '$loc_sector_id', ";
        $loc_x = escapeSqlString($_POST['loc_x']);
        $query .= "loc_x = '$loc_x', ";
        $loc_y = escapeSqlString($_POST['loc_y']);
        $query .= "loc_y = '$loc_y', ";
        $loc_z = escapeSqlString($_POST['loc_z']);
        $query .= "loc_z = '$loc_z', ";
        $loc_yrot = escapeSqlString($_POST['loc_yrot']);
        $query .= "loc_yrot = '$loc_yrot', ";
        $loc_instance = escapeSqlString($_POST['loc_instance']);
        $query .= "loc_instance = '$loc_instance', ";
        $racegender_id = escapeSqlString($_POST['racegender_id']);
        $query .= "racegender_id = '$racegender_id', ";
        if ($_POST['char_type'] > 0) // Don't update for players
        {
            $characterType = escapeSqlString($_POST['character_type']);
            $query .= "character_type = $characterType, ";
        }
        if (isset($_POST['base_hitpoints_null'])) 
        {
            $query .= "base_hitpoints_max = null, ";
        }
        else
        {
            $base_hitpoints_max = escapeSqlString($_POST['base_hitpoints_max']);
            $query .= "base_hitpoints_max = '$base_hitpoints_max', ";
        }
        if (isset($_POST['base_mana_null']))
        {
            $query .= "base_mana_max = null, ";
        }
        else
        {
            $base_mana_max = escapeSqlString($_POST['base_mana_max']);
            $query .= "base_mana_max = '$base_mana_max', ";
        }
        if ($_POST['char_type'] > 0) // Don't update for players
        {
            $npc_impervious_ind = escapeSqlString($_POST['npc_impervious_ind']);
            $query .= "npc_impervious_ind = '$npc_impervious_ind', ";
        }
        $kill_exp = escapeSqlString($_POST['kill_exp']);
        $query .= "kill_exp = '$kill_exp', ";
        if ($_POST['char_type'] > 0) // Don't update for players
        {
            $npc_spawn_rule = escapeSqlString($_POST['npc_spawn_rule']);
            $query .= "npc_spawn_rule = '$npc_spawn_rule', ";
            $npc_addl_loot_category_id = escapeSqlString($_POST['npc_addl_loot_category_id']);
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
            $sc_npctype = escapeSqlString($_POST['sc_npctype']);
            $sc_region = escapeSqlString($_POST['sc_region']);
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
}

function npc_skills($masterId)
{
    if (checkaccess('npcs', 'read'))
    {
        if (isset($_GET['npc_id']))
        {
            // block unauthorized access
            if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) {
                echo '<p class="error">You are not authorized to edit NPCs</p>';
                return;
            }
            if (isset($_POST['commit']))
            {
                if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) {
                    echo '<p class="error">You are not authorized to edit NPCs</p>';
                    return;
                }
                $id = escapeSqlString($_GET['npc_id']);
                $skill_id = escapeSqlString($_POST['skill_id']);
                $query = '';
                if ($_POST['commit'] == 'Remove')
                {
                    $query = "DELETE FROM character_skills WHERE character_id='$id' AND skill_id='$skill_id'";
                }
                else if($_POST['commit'] == 'Add Skill')
                {
                    $skill_rank = escapeSqlString($_POST['skill_rank']);
                    $query = "INSERT INTO character_skills (character_id, skill_id, skill_rank) VALUES ('$id', '$skill_id', '$skill_rank') ON DUPLICATE KEY UPDATE skill_rank='$skill_rank'";
                }
                else if($_POST['commit'] == 'Edit')
                {
                    $skill_rank = escapeSqlString($_POST['skill_rank']);
                    $skill_Z = escapeSqlString($_POST['skill_Z']);
                    $skill_Y = escapeSqlString($_POST['skill_Y']);
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
                npc_skills($masterId);
            }
            else
            {
                if ($masterId > 0)
                {
                    echo '<p>This NPC is using this <a href="./index.php?do=npc_details&npc_id='.$masterId.'&sub=skills">master NPC</a>, and ';
                    echo 'receives its skills from that master.</p>';
                    return;
                }
                $Skill_result = PrepSelect('skill');
                while ($row = fetchSqlAssoc($Skill_result))
                {
                    $s_id = $row['skill_id'];
                    $Skills[$s_id] = $row['name'];
                }
                $id = escapeSqlString($_GET['npc_id']);
                $query = 'SELECT skill_id, skill_Z, skill_Y, skill_rank FROM character_skills WHERE character_id='.$id.' ORDER BY skill_id';
                $result = mysql_query2($query);
                echo '<table border="1"><tr><th>Skill</th><th>Rank</th><th>Skill Z</th><th>Skill Y</th><th>Actions</th></tr>';
                if (sqlNumRows($result) > 0)
                {
                    while ($row = fetchSqlAssoc($result))
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

function npc_traits($masterId){
  if (checkaccess('npcs', 'read')){
    if (isset($_GET['npc_id'])){
      $id = escapeSqlString($_GET['npc_id']);
      // block unauthorized access
      if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) {
          echo '<p class="error">You are not authorized to edit NPCs</p>';
          return;
      }
      if (isset($_POST['commit'])){
        $trait_id = escapeSqlString($_POST['trait_id']);
        if ($_POST['commit'] == "Remove"){
          $query = "DELETE FROM character_traits WHERE character_id='$id' AND trait_id='$trait_id'";
        }else if ($_POST['commit'] == "Add"){
          $query = "INSERT INTO character_traits (character_id, trait_id) VALUES ('$id', '$trait_id')";
        }
        mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        npc_traits($masterId);
      }else{
        if ($masterId > 0)
        {
            echo '<p>This NPC is using this <a href="./index.php?do=npc_details&npc_id='.$masterId.'&sub=traits">master NPC</a>, and ';
            echo 'receives its traits from that master.</p>';
            return;
        }
        $query = "SELECT racegender_id FROM characters WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        $race = $row['racegender_id'];
        $query = "SELECT id, location, name, cstr_mesh, cstr_material, cstr_texture, shader FROM traits WHERE race_id='$race'";
        $Traits_Result = mysql_query2($query);
        while ($row = fetchSqlAssoc($Traits_Result)){
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
        if (sqlNumRows($result) > 0){
          echo '<table border="1"><tr><th>Location</th><th>Name</th><th>Image</th><th>Mesh</th><th>Material</th><th>Actions</th></tr>';
          while ($row = fetchSqlAssoc($result)){
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
        if (sqlNumRows($Traits_Result) > 0){
          echo '<p>Add a new Trait</p>';
          sqlSeek($Traits_Result, 0);
          echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=traits" method="post"><select name="trait_id">';
          while ($row = fetchSqlAssoc($Traits_Result)){
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

function npc_kas()
{
    if (!checkaccess('npcs','read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (!isset($_GET['npc_id']))
    {
        echo '<p class="error">Error: No npc id</p>';
        return;
    }
    $id = escapeSqlString($_GET['npc_id']);
    // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) 
    {
          echo '<p class="error">You are not authorized to edit NPCs</p>';
          return;
    }
    
    if (isset($_POST['commit']))
    {
        $query = '';
        $area = escapeSqlString($_POST['area']);
        if ($_POST['commit'] == 'Remove')
        {
            $query = "DELETE FROM npc_knowledge_areas WHERE player_id='$id' AND area='$area'";
        }
        else if ($_POST['commit'] == 'Update Priority')
        {
            $priority = escapeSqlString($_POST['priority']);
            $query = "UPDATE npc_knowledge_areas SET priority='$priority' WHERE player_id='$id' AND area='$area'";
        }
        else if ($_POST['commit'] == 'Add KA')
        {
            $priority = escapeSqlString($_POST['priority']);
            if ($area == "SELF")
            {
                $query = "SELECT name, lastname FROM characters WHERE id='$id'";
                $result = mysql_query2($query);
                $row = fetchSqlAssoc($result);
                $name = $row['name'];
                if ($row['lastname'] != ""){
                  $name = $name . ' ' .$row['lastname'];
                }
                $query = "INSERT INTO npc_knowledge_areas (player_id, area, priority) VALUES ('$id', '$name', '$priority')";
            }
            else
            {
                $query = "INSERT INTO npc_knowledge_areas (player_id, area, priority) VALUES ('$id', '$area', '$priority')";
            }
        }
        mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        npc_kas();
    }
    else
    {
        // find npc name
        $query = "SELECT name,lastname FROM characters WHERE id='$id'";
        $result = mysql_query2($query);
        if (sqlNumRows($result) > 0)
        {
            $row = fetchSqlAssoc($result);
            $npcname = trim($row['name']." ".$row['lastname']);
            // find the knowledge area "scripts" for the NPC
            $query2 = "SELECT id, script FROM quest_scripts WHERE quest_id='-1' and script like '%".$npcname.":%'";
            echo '<div><span class="bold">KA Scripts:</span><br/>';
            $result2 = mysql_query2($query2);
            if (sqlNumRows($result2) > 0)
            {
                echo 'The following KA scripts are present for '.$npcname.': <br/>';
                while ($row2 = fetchSqlAssoc($result2))
                {
                    echo ' <a href="index.php?do=ka_scripts&amp;sub=Read&amp;areaid='.$row2['id'].'"> '.$row2['id'].' </a> ';
                    echo '( <a href="index.php?do=validatequest&amp;id=-1&amp;script_id='.$row2['id'].'"> Validate </a> ';
                    if (checkaccess('npcs', 'edit'))
                    {
                        echo '| <a href="index.php?do=ka_scripts&amp;sub=Edit&amp;areaid='.$row2['id'].'"> Edit </a>';
                    }
                    echo ' ) <br/>';
                }
            } 
            else 
            {
                echo 'None found. ';
                echo '<form method="post" action="index.php?do=ka_scripts&amp;sub=New"><div><input type="hidden" name="name" value="'.$npcname.'"/>';
                echo '<input type="submit" name="commit" value="Create KA Script"/></div></form>';
            }
            echo '</div>';
        }
        echo '<p class="bold">Knowledge Areas: </p>';
        // find all knowledge area "triggers" for the NPC
        $query = "SELECT area, priority FROM npc_knowledge_areas WHERE player_id='$id' ORDER BY priority";
        $result = mysql_query2($query);
        if (sqlNumRows($result) > 0)
        {
            echo '<table border="1">';
            echo '<tr><th>Area</th><th>Priority</th><th>Actions</th></tr>';
            while ($row = fetchSqlAssoc($result))
            {
                echo '<tr>';
                echo '<td><a href="./index.php?do=ka_detail&amp;area='.htmlentities($row['area']).'">'.$row['area'].'</a></td>';
                echo '<td>'.$row['priority'].'</td>';
                echo '<td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=kas" method="post"><div>';
                echo '<input type="hidden" name="area" value="'.$row['area'].'" />';
                echo '<input type="submit" name="commit" value="Remove" /><br/>';
                echo '<input type="submit" name="commit" value="Update Priority" /><select name="priority">';
                for ($i = 1; $i <= 10; $i++)
                {
                    echo '<option value="'.$i.'" '.($i == $row['priority'] ? 'selected="selected"' : '').'>'.$i.'</option>';
                }
                echo '</select>';
                echo '</div></form></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        else
        {
            echo '<p class="error">NPC has no KAs Assigned</p>';
        }
        $query = "SELECT DISTINCT area FROM npc_triggers ORDER BY area";
        $result = mysql_query2($query);
        while ($row = fetchSqlAssoc($result))
        {
            $areas[] = $row['area'];
        }
        $query = "SELECT DISTINCT name, lastname FROM characters WHERE character_type=1 ORDER by name";
        $result = mysql_query2($query);
        while ($row = fetchSqlAssoc($result))
        {
            if ($row['lastname'] == "")
            {
                $names[] = $row['name'];
            }
            else
            {
                $names[] = $row['name'].' '.$row['lastname'];
            }
        }
        /* what this does is creating a list of all KA areas (areas of conversation), and then subtracting the list of all known npcs from that
           (since those are "personal" scripts). Then, it offers whatever is left to be assigned as "general" scripts. */
        $display = array_values(array_diff($areas, $names)); 
        echo '<p>Add a Knowledge Area to this NPC:</p><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=kas" method="post">';
        echo '<div><select name="area">';
        echo '<option value="SELF">[Add KA of this NPC]</option>';
        foreach ($display as $name)
        {
            echo '<option value="'.$name.'">'.$name.'</option>';
        }
        echo '</select>';
        echo '<select name="priority">';
        for ($i = 1; $i <= 10; $i++)
        {
            echo '<option value="'.$i.'">'.$i.'</option>';
        }
        echo '</select>';
        echo '<input type="submit" name="commit" value="Add KA" />';
        echo '</div></form>';
    }
}

function npc_items($masterId){
  if (checkaccess('npcs', 'read')){
    if (isset($_GET['npc_id'])){
      $id = escapeSqlString($_GET['npc_id']);

      // block unauthorized access
      if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) {
          echo '<p class="error">You are not authorized to edit NPCs</p>';
          return;
      }
      if (isset($_POST['commit'])){
        if ($_POST['commit'] == 'Remove'){
          $inst_id = escapeSqlString($_POST['id']);
          $query = "DELETE FROM item_instances WHERE id='$inst_id'";
        }else if ($_POST['commit'] == 'Change Location'){
          $inst_id = escapeSqlString($_POST['id']);
          $slot = escapeSqlString($_POST['slot']);
          $query = "UPDATE item_instances SET location_in_parent='$slot' WHERE id='$inst_id'";
        }else if ($_POST['commit'] == 'Add'){
          $item = escapeSqlString($_POST['item_id']);
          $query = "SELECT MAX(location_in_parent) AS loc FROM item_instances WHERE char_id_owner='$id' AND location_in_parent>15";
          $result = mysql_query2($query);
          $location = '';
          $row = fetchSqlAssoc($result);
          $location = $row['loc'];
          $location = ($location == null ? 15 : $location) + 1;
          if ($location > 47){
            $location = 47;
          }

          $query = "SELECT item_max_quality FROM item_stats WHERE id = '$item'";
          $result = mysql_query2($query);
          $row = fetchSqlAssoc($result);
          $quality = $row['item_max_quality'];
          $query = "INSERT INTO item_instances (char_id_owner, location_in_parent, stack_count, item_stats_id_standard, item_quality, crafted_quality) VALUES ('$id', $location, '1', '$item', '$quality', '$quality')";
        }else if ($_POST['commit'] == 'Update'){
          $stack_count = escapeSqlString($_POST['stack_count']);
          $item_quality = escapeSqlString($_POST['item_quality']);
          $inst_id = escapeSqlString($_POST['id']);
          $query = "UPDATE item_instances SET stack_count='$stack_count', item_quality='$item_quality', crafted_quality='$item_quality' WHERE id='$inst_id'";
        }
        unset($_POST);
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        npc_items($masterId);
      }else{
        if ($masterId > 0)
        {
            echo '<p>This NPC is using this <a href="./index.php?do=npc_details&npc_id='.$masterId.'&sub=items">master NPC</a>, and ';
            echo 'receives its inventory from that master.</p>'."\n";
            return;
        }
        $query = "SELECT i.id, i.location_in_parent, i.stack_count, i.item_name, i.item_quality, i.item_stats_id_standard, s.name, s.valid_slots FROM item_instances AS i LEFT JOIN item_stats as s ON s.id=i.item_stats_id_standard WHERE i.char_id_owner='$id' ORDER BY s.name";
        $result = mysql_query2($query);
        if (sqlNumRows($result) > 0){
          echo '<table border="1"><tr><th>Item</th><th>Location</th><th>Count/Quality</th><th>Functions</th></tr>'."\n";
          while ($row = fetchSqlAssoc($result)){
            echo '<tr>';
            echo '<td>'.$row['name'];
            if ($row['item_name'] != null && trim($row['item_name']) != '')
            {
                echo ' (Renamed to "'.htmlentities($row['item_name']).'")';
            }
            echo '</td>'."\n";
            echo '<td>'.LocationToString($row['location_in_parent']).'</td>'."\n";
            echo '<td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=items" method="post"><div>'."\n";
            echo '<input type="text" name="stack_count" value="'.$row['stack_count'].'" size="3"/>'."\n";
            echo '<input type="text" name="item_quality" value="'.$row['item_quality'].'" size="3"/><input type="hidden" name="id" value="'.$row['id'].'"/>'."\n";
            echo '<input type="submit" name="commit" value="Update"/></div></form></td>'."\n";
            echo '<td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=items" method="post"><div>'."\n";
            echo '<input type="hidden" name="id" value="'.$row['id'].'"/>'."\n";
            echo '<input type="submit" name="commit" value="Remove" /><br/>'."\n";
            echo '<input type="submit" name="commit" value="Change Location" />'."\n";
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
            echo '</select>'."\n";
            echo '</div></form></td>';
            echo '</tr>';
          }
          echo '</table>'."\n";
        }else{
          echo '<p class="error">NPC Has no items in Inventory</p>';
        }
        echo '<p>Add a new Item to this NPC</p>'."\n";
        echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=items" method="post"><div>';
        echo DrawItemSelectBox('item_id');
        echo '<input type="submit" name="commit" value="Add" />';
        echo '</div></form>'."\n";
      }
    }else{
      echo '<p class="error">Error: No npc id</p>';
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}

function npc_training($masterId){
  if (checkaccess('npcs', 'read')){
    if (isset($_GET['npc_id'])){
      $id = escapeSqlString($_GET['npc_id']);
      // block unauthorized access
      if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) {
          echo '<p class="error">You are not authorized to edit NPCs</p>';
          return;
      }
      if (isset($_POST['commit'])){
        if ($_POST['commit'] == "Remove"){
          $skill_id = escapeSqlString($_POST['skill_id']);
          $query = "DELETE FROM trainer_skills WHERE skill_id='$skill_id' AND player_id='$id'";
        }else if ($_POST['commit'] == "Add"){
          $skill_id = escapeSqlString($_POST['skill_id']);
          $min_rank = escapeSqlString($_POST['min_rank']);
          $max_rank = escapeSqlString($_POST['max_rank']);
          $min_faction = escapeSqlString($_POST['min_faction']);
          $query = "INSERT INTO trainer_skills (player_id, skill_id, min_rank, max_rank, min_faction) VALUES ('$id', '$skill_id', '$min_rank', '$max_rank', '$min_faction') ON DUPLICATE KEY UPDATE min_rank='$min_rank', max_rank='$max_rank', min_faction='$min_faction'";
        }
        $result = mysql_query2($query);
        unset($_POST);
        echo '<p class="error">Update Successful</p>';
        npc_training($masterId);
      }else{
        if ($masterId > 0)
        {
            echo '<p>This NPC is using this <a href="./index.php?do=npc_details&npc_id='.$masterId.'&sub=training">master NPC</a>, and ';
            echo 'receives its training status from that master.</p>';
            return;
        }
        $query = "SELECT t.skill_id, t.min_rank, t.max_rank, t.min_faction, s.name FROM trainer_skills AS t LEFT JOIN skills AS s ON t.skill_id=s.skill_id WHERE t.player_id='$id'";
        $result = mysql_query2($query);
        if (sqlNumRows($result) == 0){
          echo '<p class="error">NPC is not currently a trainer</p>';
        }else{
          echo '<table border="1">';
          echo '<tr><th>Skill</th><th>Min Rank</th><th>Max Rank</th><th>Min Faction</th><th>Actions</th></tr>';
          while ($row = fetchSqlAssoc($result)){
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

function npc_merchant($masterId)
{
    if (checkaccess('npcs', 'read'))
    {
        if (isset($_GET['npc_id']))
        {
            $id = escapeSqlString($_GET['npc_id']);
            // block unauthorized access
            if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) 
            {
                echo '<p class="error">You are not authorized to edit NPCs</p>';
                return;
            }
            if (isset($_POST['commit']))
            {
                $category_id = escapeSqlString($_POST['category_id']);
                if ($_POST['commit'] == 'Remove')
                {
                    $query = "DELETE FROM merchant_item_categories WHERE category_id = '$category_id' AND player_id = '$id'";
                }
                else if ($_POST['commit'] == 'Add')
                {
                    $query = "INSERT INTO merchant_item_categories (player_id, category_id) VALUES ('$id', '$category_id')";
                }
                $result = mysql_query2($query);
                unset($_POST);
                echo '<p class="error">Update Successful</p>';
                npc_merchant($masterId);
            }
            else
            {
                if ($masterId > 0)
                {
                    echo '<p>This NPC is using this <a href="./index.php?do=npc_details&npc_id='.$masterId.'&sub=merchant">master NPC</a>, and ';
                    echo 'receives its merchant status from that master.</p>';
                    return;
                }
                $query = "SELECT m.category_id, c.name FROM merchant_item_categories AS m LEFT JOIN item_categories AS c ON m.category_id = c.category_id WHERE m.player_id = '$id'";
                $result = mysql_query2($query);
                if (sqlNumRows($result) == 0)
                {
                    echo '<p class="error">This NPC is not currently a Merchant</p>';
                }
                else
                {
                    echo '<table border="1"><tr><th>Category</th><th>Actions</th></tr>';
                    while ($row = fetchSqlAssoc($result))
                    {
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
        }
        else
        {
            echo '<p class="error">Error: No npc id</p>';
        }
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function npc_specific()
{
    // use functionality from ka_detail, which has it all already, the npc-submenu has already set the required area=fullname GET var.
    include "ka_trigger.php";
    ka_detail();
}

function npcdetails(){
  if (checkaccess('npcs', 'read')){
    $uri_string = './index.php?do=npc_details';
    // we will use this to tell the display functions if the NPC is using a master or not.
    $masterId = -1;
    if (isset($_GET['npc_id'])){
      if (is_numeric($_GET['npc_id'])){
        $id = escapeSqlString($_GET['npc_id']);
        $query = "SELECT name, lastname, npc_master_id, character_type FROM characters WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        //echo '<p class="bold">NPC: '.$id.' - '.$row['name'].' '.$row['lastname'].'</p>';
        echo '<p class="bold" style="float: left; margin: 0pt 5px 0pt 0pt;">NPC: '.$id.' - '.$row['name'].' '.$row['lastname'].'</p>';
        $fullname = trim($row['name'].' '.$row['lastname']);
        if (checkaccess('npcs', 'delete'))
        {
            echo '<form action="index.php?do=deletenpc&amp;id='.$id.'" method="post">';
            if ($row['character_type'] == 1 || $row['character_type'] == 3) 
            {
                echo '<p style="margin: 0pt 5px 0pt 0pt;"><input type="submit" value="delete NPC" /></p>';
            }
            echo '</form>';
        }
        echo "<br/>\n";
        $uri_string = $uri_string.'&amp;npc_id='.$id;
        if ($row['character_type'] > 0 && $row['npc_master_id'] != 0 && $row['npc_master_id'] != $id) // check if the npc is using a master, if so, set it.
        {
            $masterId = $row['npc_master_id'];
        }
      }
    }
    echo '<div class="menu_npc">';
    echo '<a href="'.$uri_string.'&amp;sub=main">Main</a><br/>';
    echo '<a href="'.$uri_string.'&amp;sub=skills">skills</a><br/>';
    echo '<a href="'.$uri_string.'&amp;sub=traits">traits</a><br/>';
    echo '<a href="'.$uri_string.'&amp;sub=factions">Factions</a><br/>';
    echo '<a href="'.$uri_string.'&amp;sub=variables">Variables</a><br/>';
    if ($row['character_type'] > 0)   // don't display for players
    {
        echo '<a href="'.$uri_string.'&amp;sub=kas">KA\'s</a><br/>';
        echo '<a href="./index.php?do=npcquests&amp;npc_id='.$_GET['npc_id'].'">Quests</a><br/>';
    }
    echo '<a href="'.$uri_string.'&amp;sub=items">items</a><br/>';
    if ($row['character_type'] > 0)   // don't display for players
    {
        echo '<a href="'.$uri_string.'&amp;sub=training">training</a><br/>';
        echo '<a href="'.$uri_string.'&amp;sub=merchant">merchant</a><br/>';
        // set area so the function called can use ka_trigger.php->ka_detail().
        echo '<a href="'.$uri_string.'&amp;sub=specific&amp;area='.$fullname.'">Specific KA\'s</a><br/>';
    }
    echo '</div><div class="main_npc">';
    if (isset($_GET['sub'])){
      switch ($_GET['sub']){
        case 'factions':
          npc_factions();
          break;
        case 'variables':
          npc_variables($masterId);
          break;
        case 'main':
          npc_main();
          break;
        case 'skills':
          npc_skills($masterId);
          break;
        case 'traits':
          npc_traits($masterId);
          break;
        case 'kas':
          npc_kas();
          break;
        case 'items':
          npc_items($masterId);
          break;
        case 'training';
          npc_training($masterId);
          break;
        case 'merchant':
          npc_merchant($masterId);
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
    echo '<p class="error">You are not authorized to view NPC details</p>';
  }
}

function npc_factions()
{
    if (checkaccess('npcs', 'read'))
    {
        if (isset($_GET['npc_id']))
        {
            if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) {
                echo '<p class="error">You are not authorized to use these functions</p>';
                return;
            }
            if (isset($_POST['commit']))
            {
                $id = escapeSqlString($_GET['npc_id']);
                $faction_id = escapeSqlString($_POST['faction_id']);
                $query = '';
                if ($_POST['commit'] == 'Remove')
                {
                    $query = "DELETE FROM character_factions WHERE character_id='$id' AND faction_id='$faction_id'";
                }
                else if($_POST['commit'] == 'Add Faction')
                {
                    $faction_value = escapeSqlString($_POST['faction_value']);
                    $query = "INSERT INTO character_factions (character_id, faction_id, value) VALUES ('$id', '$faction_id', '$faction_value') ON DUPLICATE KEY UPDATE value='$faction_value'";
                }
                else if($_POST['commit'] == 'Edit')
                {
                    $faction_value = escapeSqlString($_POST['faction_value']);
                    $query = "UPDATE character_factions SET value='$faction_value'WHERE character_id='$id' AND faction_id='$faction_id'";
                }
                else
                {
                    echo '<p class="error">Invalid commit!</p>';
                    return;
                }
                $result = mysql_query2($query);
                unset($_POST);
                echo '<p class="error">Update Successful</p>';
                npc_factions();
            }
            else
            {
                $faction_result = PrepSelect('factions');
                while ($row = fetchSqlAssoc($faction_result))
                {
                    $f_id = $row['id'];
                    $factions[$f_id] = $row['faction_name'];
                }
                $id = escapeSqlString($_GET['npc_id']);
                $query = 'SELECT faction_id, value FROM character_factions WHERE character_id='.$id.' ORDER BY faction_id';
                $result = mysql_query2($query);
                echo '<table border="1"><tr><th>Faction</th><th>Value</th><th>Actions</th></tr>';
                if (sqlNumRows($result) > 0)
                {
                    while ($row = fetchSqlAssoc($result))
                    {
                        $f_id = $row['faction_id'];
                        echo '<tr><td><form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=factions" method="post">';
                        echo '<input type="hidden" name="faction_id" value="'.$f_id.'" />'.$factions[$f_id].'</td>';
                        echo '<td><input type="text" size="9" name="faction_value" value="'.$row['value'].'" /></td>';
                        echo '<td><input type="submit" name="commit" value="Edit" /></form>';
                        echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=factions" method="post">';
                        echo '<input type="hidden" name="faction_id" value="'.$f_id.'" /><input type="submit" name="commit" value="Remove" /></form></td></tr>';
                    }
                    echo '</table>';
                }
                else
                {
                    echo '</table>';
                    echo '<p class="error">NPC has no factions</p>';
                }
                echo '<p>Add a Faction to this NPC</p>';
                echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=factions" method="post">';
                echo '<table border="1"><tr><th>Faction</th><th>Value</th><th>Actions</th></tr>';
                echo '<tr><td>'.DrawSelectBox('factions', $faction_result, 'faction_id', '').'</td><td><input type="text" name="faction_value" size="7" /></td><td>';
                echo '<input type="submit" name="commit" value="Add Faction" /></td></tr></table></form>';
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
function npc_variables($masterId)
{
    if (checkaccess('npcs', 'read'))
    {
        if (isset($_GET['npc_id']))
        {
            if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) {
                echo '<p class="error">You are not authorized to use these functions</p>';
                return;
            }
            if (isset($_POST['commit']))
            {
                $id = escapeSqlString($_GET['npc_id']);
                $variable_name = escapeSqlString($_POST['variable_name']);
                if (trim($variable_name) == '')
                {
                    echo '<p class="error">Invalid variable name.</p>';
                    return;
                }
                $query = '';
                if ($_POST['commit'] == 'Remove')
                {
                    $query = "DELETE FROM character_variables WHERE character_id='$id' AND name='$variable_name'";
                }
                else if($_POST['commit'] == 'Add Variable')
                {
                    $variable_value = escapeSqlString($_POST['variable_value']);
                    $query = "INSERT INTO character_variables (character_id, name, value) VALUES ('$id', '$variable_name', '$variable_value')";
                }
                else if($_POST['commit'] == 'Edit')
                {
                    $variable_value = escapeSqlString($_POST['variable_value']);
                    $query = "UPDATE character_variables SET value='$variable_value' WHERE character_id='$id' AND name='$variable_name'";
                }
                else
                {
                    echo '<p class="error">Invalid commit!</p>';
                    return;
                }
                $result = mysql_query2($query);
                unset($_POST);
                echo '<p class="error">Update Successful</p>';
                npc_variables($masterId);
            }
            else
            {
                if ($masterId > 0)
                {
                    echo '<p>This NPC is using this <a href="./index.php?do=npc_details&npc_id='.$masterId.'&sub=variables">master NPC</a>, and ';
                    echo 'receives its variables from that master.</p>';
                    return;
                }
                $id = escapeSqlString($_GET['npc_id']);
                $query = 'SELECT name, value FROM character_variables WHERE character_id='.$id.' ORDER BY name';
                $result = mysql_query2($query);
                if (sqlNumRows($result) > 0)
                {
                    echo '<div class="table">'."\n";
                    echo '<div class="tr">'."\n";
                    echo '<div class="th">Variable</div><div class="th">Value</div><div class="th">Actions</div>'."\n";
                    echo '</div>'."\n";
                    while ($row = fetchSqlAssoc($result))
                    {
                        echo '<form class="tr" action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=variables" method="post">'."\n";
                        echo '<div class="td"><input type="hidden" name="variable_name" value="'.htmlentities($row['name']).'" />'.htmlentities($row['name']).'</div>'."\n";
                        echo '<div class="td"><input type="text" size="9" name="variable_value" value="'.htmlentities($row['value']).'" /></div>'."\n";
                        echo '<div class="td"><input type="submit" name="commit" value="Edit" /><br /><input type="submit" name="commit" value="Remove" /></div>'."\n";
                        echo '</form>'."\n"; // ends tr
                    }
                    echo '</div>'."\n"; // ends table
                }
                else
                {
                    echo '<p class="error">NPC has no variables</p>';
                }
                echo '<p>Add a variable to this NPC</p>';
                echo '<form action="./index.php?do=npc_details&amp;npc_id='.$id.'&amp;sub=variables" method="post">';
                echo '<table border="1"><tr><th>Variable</th><th>Value</th><th>Actions</th></tr>';
                echo '<tr><td><input type="text" name="variable_name" size="7" /></td><td><input type="text" name="variable_value" size="7" /></td><td>';
                echo '<input type="submit" name="commit" value="Add Variable" /></td></tr></table></form>';
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
?>
