<?php

function listnpcscombat()
{
    if (checkaccess('npcs', 'read'))
    {
        $sql = "SELECT c.id, c.name, c.lastname, c.npc_spawn_rule, c.npc_addl_loot_category_id, c.kill_exp, c.npc_master_id, s.name AS sector, c.loc_x, ";
        $sql .= "c.loc_y, c.loc_z, c.loc_instance, sc_npc_d.region FROM characters as c LEFT JOIN sectors AS s ON c.loc_sector_id = s.id ";
        $sql .= "LEFT JOIN sc_npc_definitions AS sc_npc_d ON c.id = sc_npc_d.char_id WHERE c.character_type = 1 AND c.npc_impervious_ind='N'";
        
        $sorting = array('id' => 'c.id', 'name' => 'c.name', 'sector' => 's.name, c.name', 'spawn' => 'c.npc_spawn_rule, c.name', 'loot' => 'c.npc_addl_loot_category_id, c.name');
        if (isset($_GET['sort']) && array_key_exists($_GET['sort'], $sorting))
        {
            $sql .= ' ORDER BY '.$sorting[$_GET['sort']];
        }
        else
        {
            $sql .= ' ORDER BY sector, name';
        }

        $sql2 = "SELECT COUNT(*) FROM characters WHERE character_type = 1 AND npc_impervious_ind='N'";
        $item_count = fetchSqlRow(mysql_query2($sql2));
        $nav = RenderNav('do=listnpcscombat', $item_count[0]);
        $sql .= $nav['sql'];
        echo $nav['html'];
        unset($nav);
        
        $query = mysql_query2($sql);
        if (sqlNumRows($query) == 0)
        {
            echo '<p class="error">No NPCs Found</p>';
            return;
        }

        $url = './index.php?do=listnpcscombat&amp;'.(isset($_GET['page']) ? 'page='.$_GET['page'].'&amp;' : '');
        $url .= (isset($_GET['items_per_page']) ? 'items_per_page='.$_GET['items_per_page'].'&amp;' : '');
        echo '<table border="1">';
        echo '<tr><th><a href="'.$url.'sort=id">ID</a></th><th><a href="'.$url.'sort=name">Name</a></th><th><a href="'.$url.'sort=spawn">Spawn</a>/<a href="'.$url.'sort=loot">Loot</a></th><th>Loc X/Y/Z/Instance</th><th><a href="'.$url.'sort=sector">Sector</a></th><th>Region</th><th>Weapon Right Hand</th><th>Weapon Left Hand</th><th>Skills</th><th>Exp</th></tr>';

        while ($row = fetchSqlAssoc($query))
        {
            // get skills and weapons from master NPC if set, otherwise get own. if master is set to this npcs own ID (no master) this works too.
            $id = ($row['npc_master_id'] != 0 ? $row['npc_master_id'] : $row['id']);

            echo '<tr style="white-space: nowrap;">';
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
            $right = '';
            $left = '';
            $sql2 = "SELECT i.location_in_parent AS weapon_location, ist.name AS weapon_name FROM item_instances AS i LEFT JOIN item_stats AS ist ";
            $sql2 .= "ON i.item_stats_id_standard = ist.id WHERE i.char_id_owner = '$id' AND (i.location_in_parent = 0 OR i.location_in_parent = 1)";
            $query2 = mysql_query2($sql2);
            if (sqlNumRows($query2) > 2)
            {
                echo '<p class="error">NPC #$id has too many weapons.</p>';
            } // we expect 2 results max.
            while ($row2 = fetchSqlAssoc($query2))
            {
                if ($row2['weapon_location'] == 0)
                {
                    $right = $row2['weapon_name'];
                }
                else 
                {
                    $left = $row2['weapon_name'];
                }
            }
            echo '<td>'.htmlentities($right).'</td>';
            echo '<td>'.htmlentities($left).'</td>';
            echo '<td>';
            $sql2 = "SELECT sk.name AS skill_name, cs.skill_rank FROM character_skills AS cs LEFT JOIN skills AS sk ON sk.skill_id = cs.skill_id WHERE cs.character_id = '$id'";
            $query2 = mysql_query2($sql2);
            while ($row2 = fetchSqlAssoc($query2))
            {
                echo htmlentities($row2['skill_name']).': '.$row2['skill_rank'].' ';
            }
            echo '</td>';
            echo '<td>'.$row['kill_exp'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    else
    {
       echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
?>
