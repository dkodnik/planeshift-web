<?php
// impervious value is ignored if search value is given.
function listnpcs($impervious, $search = null)
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
    // notice that npc_search.php also uses this function to list its results, care should be taken not to break functionality when making changes here.
    $query = 'SELECT c.id, c.name, c.lastname, c.description, c.npc_spawn_rule, c.npc_addl_loot_category_id, s.name AS sector, c.loc_x, c.loc_y, c.loc_z, c.loc_instance, b.npctype AS behavior, ';
    $query .= 'b.region, r.name AS race, r.sex FROM characters as c LEFT JOIN sectors AS s ON c.loc_sector_id=s.id LEFT JOIN sc_npc_definitions AS b ON c.id=b.char_id LEFT JOIN race_info AS r ON c.racegender_id=r.id ';
    
    if ($search != null)
    {
        $query .= $search;
    }
    else
    { // $impervious contains text, not a boolean.
        $query .= 'WHERE (c.character_type=1 OR c.character_type=3) AND c.npc_impervious_ind='.($impervious == 'true' ? "'Y'": "'N'");
    }
    
    if (isset($_GET['sort']))
    {
        if ($_GET['sort'] == 'id')
        {
            $query = $query . ' ORDER BY id';
        }
        else if ($_GET['sort'] == 'name')
        {
            $query = $query . ' ORDER BY name';
        }
        else if ($_GET['sort'] == 'sector')
        {
            $query = $query . ' ORDER BY sector, name';
        }
        else if ($_GET['sort'] == 'spawn')
        {
            $query = $query . ' ORDER BY npc_spawn_rule, name';
        }
        else if ($_GET['sort'] == 'loot')
        {
            $query = $query . ' ORDER BY npc_addl_loot_category_id, name';
        }
        else if ($_GET['sort'] == 'behavior')
        {
            $query = $query . ' ORDER by behavior, name';
        }
        else if ($_GET['sort'] == 'race')
        {
            $query = $query . ' ORDER by race, sector, name';
        }
        else if ($_GET['sort'] == 'sex')
        {
            $query = $query . ' ORDER by sex, sector, name';
        }
        else
        {
            $query = $query . ' ORDER BY sector, name';
        }
    }
    else
    {
        $query = $query . ' ORDER BY sector, name';
    }
    $result = mysql_query2($query);
    if (sqlNumRows($result) > 0)
    {
        echo '<table border="1">'."\n";
        echo '<tr><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=id">ID</a></th><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=name">Name</a></th>';
        echo '<th><a href="./index.php?do='.$_GET['do'].'&amp;sort=race">Race</a></th><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=sex">Gender</a></th>';
        echo '<th>Description</th><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=spawn">Spawn</a>/<a href="./index.php?do='.$_GET['do'].'&amp;sort=loot">Loot</a></th>';
        echo '<th>Position</th><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=sector">Sector</a></th><th><a href="./index.php?do='.$_GET['do'].'&amp;sort=behavior">Behavior</a></th><th>Region</th></tr>'."\n";

        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr>';
            echo '<td>'.$row['id'].'</td>';
            echo '<td><a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['id'].'">'.$row['name'].' '.$row['lastname'].'</a></td>';

            echo '<td>'.$row['race'].'</td>';
            echo '<td>'.$row['sex'].'</td>';
            echo '<td>'.$row['description'].'</td>';
            echo '<td>(<a href="./index.php?do=listspawn&amp;id='.$row['npc_spawn_rule'].'">'.$row['npc_spawn_rule'].'</a>)';
            echo '/(<a href="./index.php?do=listloot&amp;id='.$row['npc_addl_loot_category_id'].'">'.$row['npc_addl_loot_category_id'].'</a>)</td>';
            echo '<td>'.$row['loc_x'].' / '.$row['loc_y'].' / '.$row['loc_z'].' / '.$row['loc_instance'].'</td>';
            echo '<td>'.$row['sector'].'</td>';
            echo '<td>'.$row['behavior'].'</td>';
            echo '<td>'.$row['region'].'</td>';
            echo '</tr>'."\n";
        }
        echo '</table>';
    }
    else
    {
        echo '<p class="error">No NPCs Found</p>';
    }
}
?>
