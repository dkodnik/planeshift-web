<?php

// This function isn't used until the save form below is done.
//function SelectWeapon($current, $name, $weaponlist)
//{
//    $html = '<select name="'.$name.'">';
//    $html.= '<option value="-1">None</option>';
//    foreach($weaponlist as $row)
//    {
//        $selected = '';
//        if($row['id'] == $current)
//        {
//            $selected = ' selected="selected"';
//        }
//        $html .= '<option value="'.$row['id'].'"'.$selected.'>['.htmlentities($row['armorvsweapon_type']).'] '.htmlentities($row['name']).'</option>';
//    }
//    return $html.'</select>';
//}

function listnpcscombat()
{
    if (checkaccess('npcs', 'read'))
    {
        $sql = 'SELECT c.id, c.name, c.lastname, c.npc_spawn_rule, c.npc_addl_loot_category_id, c.kill_exp, s.name AS sector, c.loc_x, c.loc_y, c.loc_z, c.loc_instance, b.region, i.location_in_parent AS weapon_location, i2.id AS weapon_id, i2.name AS weapon_name, cs.skill_id, sk.name AS skill_name, cs.skill_rank FROM characters as c LEFT JOIN sectors AS s ON c.loc_sector_id=s.id LEFT JOIN sc_npc_definitions AS b ON c.id=b.char_id LEFT JOIN item_instances AS i ON i.char_id_owner=c.id AND (i.location_in_parent = 0 OR i.location_in_parent = 1) LEFT JOIN item_stats AS i2 ON i.item_stats_id_standard = i2.id LEFT JOIN character_skills AS cs ON cs.character_id = c.id LEFT JOIN skills AS sk ON sk.skill_id = cs.skill_id WHERE c.character_type = 1';
        $sql.= ' AND c.npc_impervious_ind=\'N\'';

        $sql2 = "SELECT COUNT(*) FROM characters WHERE character_type = 1 AND npc_impervious_ind='N'";
        $item_count = mysql_fetch_array(mysql_query2($sql2), MYSQL_NUM);
        
        if (isset($_GET['sort']))
        {
            if ($_GET['sort'] == 'id')
            {
                $sql = $sql . ' ORDER BY c.id';
            }
            else if ($_GET['sort'] == 'name')
            {
                $sql = $sql . ' ORDER BY c.name';
            }
            else if ($_GET['sort'] == 'sector')
            {
                $sql = $sql . ' ORDER BY s.name, c.name';
            }
            else if ($_GET['sort'] == 'spawn')
            {
                $sql = $sql . ' ORDER BY c.npc_spawn_rule, c.name';
            }
            else if ($_GET['sort'] == 'loot')
            {
                $sql = $sql . ' ORDER BY c.npc_addl_loot_category_id, c.name';
            }
            else
            {
                $sql = $sql . ' ORDER BY s.name, c.name';
            }
        }
        else
        {
            $sql = $sql . ' ORDER BY sector, name';
        }

        $nav = RenderNav('do=listnpcscombat', $item_count[0]);
        $sql .= $nav['sql'];
        echo $nav['html'];
        unset($nav);
        

        $query = mysql_query2($sql);
        if (mysql_num_rows($query) == 0)
        {
            echo '<p class="error">No NPCs Found</p>';
            return;
        }

        echo '<table border="1">';
        echo '<tr><th><a href="./index.php?do=listnpcscombat&sort=id">ID</a></th><th><a href="./index.php?do=listnpcscombat&sort=name">Name</a></th><th><a href="./index.php?do=listnpcscombat&sort=spawn">Spawn</a>/<a href="./index.php?do=listnpcscombat&sort=loot">Loot</a></th><th>Position</th><th><a href="./index.php?do=listnpcscombat&sort=sector">Sector</a></th><th>Region</th><th>Weapon Right Hand</th><th>Weapon Left Hand</th><th>Skills</th><th>Exp</th><th> </th></tr>';

        $npcs = array();
        while ($row = mysql_fetch_array($query, MYSQL_ASSOC))
        {
            // this if/else will fire some times for each specific npc to parse all it's skills and weapons.
            if(!isset($npcs[$row['id']]))
            {
                // Assigning additional variables to $row before adding them to npcs ($row will get overwritten each iteration)
                $row['weapons'] = array($row['weapon_location'] => array($row['weapon_id'], $row['weapon_name']));
                $row['skills'] = array();
                if($row['skill_id'] != null)
                {
                    $row['skills'][$row['skill_id']] = array($row['skill_name'], $row['skill_rank']);
                }
                unset($row['weapon_id'], $row['weapon_location'], $row['skill_id'], $row['skill_name'], $row['skill_rank']);

                $npcs[$row['id']] = $row;
            }
            else
            {
                // Check for errors before adding the weapons / skills to the npc list. 
                $w = $npcs[$row['id']]['weapons'];
                if(count($w) > 1)
                {
                    echo '<p class="error">Found more than two weapon for NPC #'.$row['id'].'!</p>';
                }
                elseif(isset($w[$row['weapon_location']]))
                {
                    echo '<p class="error">Found more than one weapon in the same location for NPC #'.$row['id'].'!</p>';
                }
                else
                {
                    $w[$row['weapon_location']] = array($row['weapon_id'], $row['weapon_name']);
                    $npcs[$row['id']]['weapons'] = $w;
                }

                $s = $npcs[$row['id']]['skills'];
                if(isset($s[$row['skill_id']]))
                {
                    echo '<p class="error">Found the same skill two times for NPC #'.$row['id'].'!</p>';
                }
                else
                {
                    $s[$row['skill_id']] = array($row['skill_name'], $row['skill_rank']);
                    $npcs[$row['id']]['skills'] = $s;
                }
            }
        }
        foreach($npcs as $row)
        {
            $right = (isset($row['weapons'][0]) ? $row['weapons'][0] : array(-1, 'None'));
            $left = (isset($row['weapons'][1]) ? $row['weapons'][1] : array(-1, 'None'));

            echo '<form action="./index.php?do=npc_details&sub=main&npc_id='.$row['id'].'" method="post"><tr style="white-space: nowrap;">';
            echo '<td>'.$row['id'].'</td>';
            if (checkaccess('npcs', 'edit'))
            {
                echo '<td><a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['id'].'">'.$row['name'].' '.$row['lastname'].'</a></td>';
            }
            else
            {
                echo '<td>'.$row['name'].' '.$row['lastname'].'</td>';
            }
            echo '<td>(<a href="./index.php?do=listspawn&amp;id='.$row['npc_spawn_rule'].'">'.$row['npc_spawn_rule'].'</a>)';
            echo '/(<a href="./index.php?do=listloot&amp;id='.$row['npc_addl_loot_category_id'].'">'.$row['npc_addl_loot_category_id'].'</a>)</td>';
            echo '<td>'.$row['loc_x'].' / '.$row['loc_y'].' / '.$row['loc_z'].' / '.$row['loc_instance'].'</td>';
            echo '<td>'.$row['sector'].'</td>';
            echo '<td>'.$row['region'].'</td>';
            //echo '<td>'.SelectWeapon($right[0], 'righthand', $weaponlist).'</td>';
            //echo '<td>'.SelectWeapon($left[0], 'lefthand', $weaponlist).'</td>';
            echo '<td>'.htmlentities($right[1]).'</td>';
            echo '<td>'.htmlentities($left[1]).'</td>';
            echo '<td>';
            foreach($row['skills'] as $skill)
            {
                echo htmlentities($skill[0]).': '.$skill[1].' ';
            }
            echo '</td>';
            //echo '<td><input type="text" name="kill_exp" value="'.$row['kill_exp'].'" size="2" /></td>';
            echo '<td>'.$row['kill_exp'].'</td>';
            //echo '<td><input type="submit" name="commit" value="Save" /></td>';
            echo '<td></td>';
            echo '</tr></form>';
        }
        echo '</table>';
    }
    else
    {
       echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>