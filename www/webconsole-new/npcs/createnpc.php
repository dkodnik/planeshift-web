<?php

function createnpc()
{
    if(checkaccess('npcs', 'create'))
    {
        echo '<p class="header">Create a New NPC</p>';
        
        if(isset($_POST['submit']))
        {
            // Create the npc
            $doit = true;
            $fields = array('NPC Master' => 'npc_master_id', 'Name' => 'npcname', 'Description' => 'description', 'Race' => 'race', 'Stats' => 'stats', 'HP' => 'hp', 'Sector' => 'sector', 'Position' => 'position', 'Spawn Rule' => 'spawn', 'Weapon' => 'weapon', 'Behavior' => 'behavior', 'Skill Rank' => 'skill_value', 'Exp' => 'exp');
            foreach($fields as $name => $field)
            {
                if(!isset($_POST[$field]) || $_POST[$field] === '')
                {
                    echo '<p class="error">You have to fill the "'.$name.'" field, too.</p>';
                    $doit = false;
                    break;
                }
            }
            
            if($doit)
            {
                $npc_master_id = $_POST['npc_master_id'];
                $npcname = $_POST['npcname'];
                $description = str_replace("\r", '', $_POST['description']);
                $race = $_POST['race'];
                $stats = $_POST['stats'];
                $hp = $_POST['hp'];
                $sector = $_POST['sector'];
                $position = $_POST['position'];
                $spawnrule = $_POST['spawn'];
                $weapon = $_POST['weapon'];
                $region = $_POST['region'];
                $behavior = $_POST['behavior'];
                $skill_value = $_POST['skill_value'];
                $exp = $_POST['exp'];
                
                // transforms stats to single elements
                $stat_str = strtok($stats, ",");
                $stat_agi = strtok(",");
                $stat_end = strtok(",");
                $stat_int = strtok(",");
                $stat_wil = strtok(",");
                $stat_cha = strtok(",");
                
                // transforms position to single elements
                $locx = strtok($position, ",");
                $locy = strtok(",");
                $locz = strtok(",");
                $locrot = strtok(",");
                
                $int_fields = array('NPC Master' => $npc_master_id, 'Race' => $race, 'Stats (S)' => $stat_str, 'Stats (A)' => $stat_agi, 'Stats (E)' => $stat_end, 'Stats (I)' => $stat_int, 'Stats (W)' => $stat_wil, 'Stats(C)' => $stat_cha, 'HP' => $hp, 'Sector' => $sector, 'X' => $locx, 'Y' => $locy, 'Z' => $locz, 'Rot' => $locrot, 'Spawn Rule' => $spawnrule, 'Weapon' => $weapon, 'Exp' => $exp);
                foreach($int_fields as $name => $field)
                {
                    if(!is_numeric($field))
                    {
                        echo '<p class="error">You have to enter a number for the field "'.$name.'".</p>';
                        $doit = false;
                    }
                }
            }
            if($doit)
            {
                $sql = 'SELECT item_skill_id_1 FROM item_stats WHERE id='.$weapon;
                $query = mysql_query2($sql);
                $line = mysql_fetch_array($query, MYSQL_NUM);
                $skill = $line[0];
                
                $newnpcid = getNextId('characters', 'id');
                
                $sql = "INSERT INTO characters (id, npc_master_id, name, racegender_id, character_type, base_strength, base_agility, base_endurance, base_intelligence, base_will, base_charisma, base_hitpoints_max, mod_hitpoints, stamina_physical, stamina_mental, loc_sector_id, loc_x, loc_y, loc_z, loc_yrot, npc_spawn_rule, npc_impervious_ind, account_id, description, kill_exp) VALUES ($newnpcid, $npc_master_id, '".mysql_escape_string($npcname)."', $race, 1, $stat_str, $stat_agi, $stat_end, $stat_int, $stat_wil, $stat_cha, $hp, $hp, 100, 100, $sector, $locx, $locy, $locz, $locrot, $spawnrule, 'N', 9,'".mysql_escape_string($description)."', $exp)";
                mysql_query2($sql);
                
                if ($skill != '')
                {
                    $sql = "INSERT INTO character_skills VALUES ($newnpcid, $skill, 0, 0, $skill_value)";
                    mysql_query2($sql);
                }
                if ($region != -1 || $behavior != 'None')
                {
                    $sql = "INSERT INTO sc_npc_definitions (char_id, name, npctype, region, console_debug) VALUES ($newnpcid, '".mysql_escape_string($npcname)."','".mysql_escape_string($behavior)."','".mysql_escape_string($region)."','N')";
                    mysql_query2($sql);
                }
                
                echo '<p>The NPC was successfully created. To edit it, <a href="./index.php?do=npc_details&sub=main&npc_id='.$newnpcid.'">click here</a></p>';
                $_POST = array();
            }
        }
        
        echo '<form action="index.php?do=createnpc" method="post"><table>';
        echo '<tr class="color_b"><td>NPC Master: </td><td><input type="text" name="npc_master_id" size="5" value="'.(isset($_POST['npc_master_id']) ? htmlentities($_POST['npc_master_id']) : '0').'" /></td></tr>';
        echo '<tr class="color_a"><td>Name: </td><td><input type="text" name="npcname" value="'.(isset($_POST['npcname']) ? htmlentities($_POST['npcname']) : '').'" /></td></tr>';
        echo '<tr class="color_b"><td>Description: </td><td><textarea name="description" style="width: 400px; height: 100px;">'.(isset($_POST['description']) ? htmlentities($_POST['description']) : '').'</textarea></td></tr>';
        echo '<tr class="color_a"><td>Race: </td><td>'.DrawSelectBox('races', PrepSelect('races'), 'race', (isset($_POST['race']) ? $_POST['race'] : '')).'</td></tr>';
        echo '<tr class="color_b"><td>Stats(S,A,E,I,W,C): </td><td><input type="text" name="stats" value="'.(isset($_POST['stats']) ? htmlentities($_POST['stats']) : '0,0,0,0,0,0').'" /></td></tr>';
        echo '<tr class="color_a"><td>HP: </td><td><input type="text" name="hp" size="5" value="'.(isset($_POST['hp']) ? htmlentities($_POST['hp']) : '').'" /></td></tr>';
        echo '<tr class="color_b"><td>Sector: </td><td>'.DrawSelectBox('sectorid', PrepSelect('sectorid'), 'sector', (isset($_POST['sector']) ? $_POST['sector'] : '')).'</td></tr>';
        echo '<tr class="color_a"><td>X,Y,Z,Rot: </td><td><input type="text" name="position" value="'.(isset($_POST['position']) ? htmlentities($_POST['position']) : '0,0,0,0').'" /></td></tr>';
        echo '<tr class="color_b"><td>Spawn Rule: </td><td>'.DrawSelectBox('spawn', PrepSelect('spawn'), 'spawn', (isset($_POST['spawn']) ? $_POST['spawn'] : '')).'</td></tr>';
        echo '<tr class="color_a"><td>Weapon: </td><td>'.DrawSelectBox('weapon', PrepSelect('weapon'), 'weapon', (isset($_POST['weapon']) ? $_POST['weapon'] : -1)).'</td></tr>';
        echo '<tr class="color_b"><td>Weapon Skill Rank: </td><td><input type="text" name="skill_value" size="8" value="'.(isset($_POST['skill_value']) ? htmlentities($_POST['skill_value']) : '').'" /></td></tr>';
        echo '<tr class="color_b"><td>Behavior: </td><td>'.DrawSelectBox('behaviour', PrepSelect('behaviour'), 'behavior', (isset($_POST['behavior']) ? $_POST['behavior'] : '')).'</td></tr>';
        echo '<tr class="color_a"><td>Region: </td><td>'.DrawSelectBox('b_region', PrepSelect('b_region'), 'region', (isset($_POST['region']) ? $_POST['region'] : '')).'</td></tr>';
        echo '<tr class="color_a"><td>Exp: </td><td><input type="text" name="exp" size="5" value="'.(isset($_POST['exp']) ? htmlentities($_POST['exp']) : '').'" /></td></tr>';
        echo '</table>';
        echo '<input type="submit" name="submit" value="Create NPC" /><input type="reset" value="Reset" /></form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
