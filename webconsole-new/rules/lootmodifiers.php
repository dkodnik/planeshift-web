<?php

function listLootModifiers() 
{
    if (!checkaccess('crafting', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    
    $query = 'SELECT * from loot_modifiers';
    if (isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $query .= ' WHERE id = ' . escapeSqlString($_GET['id']);
    }
    if (isset($_GET['sort']))
    {
        switch($_GET['sort'])
        {
            case 'name':
                $query .= ' ORDER BY name';
                break;
            case 'modifier_type':
                $query .= ' ORDER BY modifier_type, name';
                break;
            case 'probability':
                $query .= ' ORDER BY modifier_type, probability';
                break;
            default:
                $query .= ' ORDER BY modifier_type, name';
        }
    }
    else
    {
        $query .= ' ORDER BY modifier_type, name';
    }
    $result = mysql_query2($query);
    echo '<table border="1"><tr><th>ID</th><th><a href="./index.php?do=listlootmodifiers&amp;sort=modifier_type">Modifier Type</a></th>';
    echo '<th><a href="./index.php?do=listlootmodifiers&amp;sort=name">Name</a></th><th>';
    echo '<a href="./index.php?do=listlootmodifiers&amp;sort=probability">Probability</a></th>';
    echo '<th>Cost Modifier</th><th>Mesh</th><th>Icon</th><th>Not Usable With</th><th>actions</th></tr>';
    while ($row = fetchSqlAssoc($result))
    {
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['modifier_type'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['probability'].'</td>';
        echo '<td>'.$row['cost_modifier'].'</td>';
        echo '<td>'.$row['mesh'].'</td>';
        echo '<td>'.$row['icon'].'</td>';
        echo '<td>'.$row['not_usable_with'].'</td>';
        echo '<td><form action="./index.php?do=editlootmodifiers" method="post"><div><input type="hidden" name="id" value="'.$row['id'].'" />';
        if (checkaccess('crafting', 'edit')) 
        {
            echo '<input type="submit" name="action" value="Edit" />';
            if (checkaccess('crafting', 'delete')) 
            {
                echo '<input type="submit" name="action" value="Delete" />';
            }
        }
        echo '</div></form></td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<hr />';
    if (checkaccess('crafting', 'create')) 
    {
        echo '<h3> Add new loot modifier: </h3>';
        echo '<form action="./index.php?do=editlootmodifiers" method="post">';
        echo '<table>';
        echo '<tr><td>Modifier Type: </td><td><select name="modifier_type"><option value="prefix">Prefix</option><option value="suffix">Suffix</option><option value="adjective">Adjective</option></select></td></tr>';
        echo '<tr><td>Name: </td><td><input type="text" name="name" /></td></tr>';
        echo '<tr><td>Effect: </td><td><textarea name="effect" cols="80" rows="5"></textarea></td></tr>';
        echo '<tr><td>Probability: </td><td><input type="text" name="probability" /></td></tr>';
        echo '<tr><td>Stat Req Modifier: </td><td><textarea name="stat_req_modifier" cols="80" rows="5"></textarea></td></tr>';
        echo '<tr><td>Cost Modifier: </td><td><input type="text" name="cost_modifier" /></td></tr>';
        echo '<tr><td>Mesh: </td><td><input type="text" name="mesh" /></td></tr>';
        echo '<tr><td>Icon: </td><td><input type="text" name="icon" /></td></tr>';
        echo '<tr><td>Not Usable With: </td><td><input type="text" name="not_usable_with" /></td></tr>';
        echo '<tr><td>Equip Script: </td><td><textarea name="equip_script" cols="80" rows="5"></textarea></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="action" value="Create" /></td></tr>';
        echo '</table></form>';
    }
}


function editLootModifiers() 
{
    if (!checkaccess('crafting', 'edit')) 
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $id = -1;
    if (isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > -1)
    {
        $id = escapeSqlString($_POST['id']);
    }

    if (isset($_POST['action'])) 
    {
        if ($id == -1 && $_POST['action'] != 'Create')
        {
            echo '<p class="error">Invalid ID</p>';
            return;
        }
        
        if ($_POST['action'] == 'Edit')
        {
            $query = "SELECT * from loot_modifiers WHERE id='$id'";
            $result = mysql_query2($query);
            $row = fetchSqlAssoc($result);
            echo '<h3> Edit loot modifier #'.$id.': </h3>';
            echo '<table><form action="./index.php?do=editlootmodifiers" method="post">';
            $selected = ' SELECTED="SELECTED"';
            echo '<input type="hidden" name="id" value="'.$id.'" />';
            echo '<tr><td>Modifier Type: </td><td><select name="modifier_type">';
            echo '<option value="prefix"'.($row['modifier_type'] == 'prefix' ? $selected : '').'>Prefix</option>';
            echo '<option value="suffix"'.($row['modifier_type'] == 'suffix' ? $selected : '').'>Suffix</option>';
            echo '<option value="adjective"'.($row['modifier_type'] == 'adjective' ? $selected : '').'>Adjective</option></select></td></tr>';
            echo '<tr><td>Name: </td><td><input type="text" name="name" value="'.$row['name'].'" /></td></tr>';
            echo '<tr><td>Effect: </td><td><textarea name="effect" cols="80" rows="5">'.htmlspecialchars($row['effect']).'</textarea></td></tr>';
            echo '<tr><td>Probability: </td><td><input type="text" name="probability" value="'.$row['probability'].'" /></td></tr>';
            echo '<tr><td>Stat Req Modifier: </td><td><textarea name="stat_req_modifier" cols="80" rows="5">'.htmlspecialchars($row['stat_req_modifier']).'</textarea></td></tr>';
            echo '<tr><td>Cost Modifier: </td><td><input type="text" name="cost_modifier" value="'.$row['cost_modifier'].'" /></td></tr>';
            echo '<tr><td>Mesh: </td><td><input type="text" name="mesh" value="'.$row['mesh'].'" /></td></tr>';
            echo '<tr><td>Icon: </td><td><input type="text" name="icon" value="'.$row['icon'].'" /></td></tr>';
            echo '<tr><td>Not Usable With: </td><td><input type="text" name="not_usable_with" value="'.$row['not_usable_with'].'" /></td></tr>';
            echo '<tr><td>Equip Script: </td><td><textarea name="equip_script" cols="80" rows="5">'.htmlspecialchars($row['equip_script']).'</textarea></td></tr>';
            echo '<tr><td colspan="2"><input type="submit" name="action" value="Save Changes" /></td></tr>';
            echo '</form></table>';
        }
        elseif ($_POST['action'] == 'Save Changes')
        {
            $modifier_type = escapeSqlString($_POST['modifier_type']);
            $name = escapeSqlString($_POST['name']);
            $effect = escapeSqlString($_POST['effect']);
            $probability = escapeSqlString($_POST['probability']);
            $stat_req_modifier = escapeSqlString($_POST['stat_req_modifier']);
            $cost_modifier = escapeSqlString($_POST['cost_modifier']);
            $mesh = escapeSqlString($_POST['mesh']);
            $icon = escapeSqlString($_POST['icon']);
            $not_usable_with = escapeSqlString($_POST['not_usable_with']);
            $equip_script = escapeSqlString($_POST['equip_script']);
            $query = "UPDATE loot_modifiers SET modifier_type='$modifier_type', name='$name', effect='$effect', probability='$probability', stat_req_modifier='$stat_req_modifier', ";
            $query .= "cost_modifier='$cost_modifier', mesh='$mesh', icon='$icon', not_usable_with='$not_usable_with', equip_script='$equip_script' WHERE id='$id'";
            mysql_query2($query);
            echo '<p class="error">Succesfully updated entry with ID '.$id.'.</p>';
            unset($_POST);
            listLootModifiers();
            return;
        }
        elseif ($_POST['action'] == 'Create')
        {
            if (!checkaccess('crafting', 'create')) 
            {
                echo '<p class="error">You are not authorized to create loot modifiers</p>';
                return;
            }
            $modifier_type = escapeSqlString($_POST['modifier_type']);
            $name = escapeSqlString($_POST['name']);
            $effect = escapeSqlString($_POST['effect']);
            $probability = escapeSqlString($_POST['probability']);
            $stat_req_modifier = escapeSqlString($_POST['stat_req_modifier']);
            $cost_modifier = escapeSqlString($_POST['cost_modifier']);
            $mesh = escapeSqlString($_POST['mesh']);
            $icon = escapeSqlString($_POST['icon']);
            $not_usable_with = escapeSqlString($_POST['not_usable_with']);
            $equip_script = escapeSqlString($_POST['equip_script']);
            $query = "INSERT INTO loot_modifiers SET modifier_type='$modifier_type', name='$name', effect='$effect', probability='$probability', stat_req_modifier='$stat_req_modifier', ";
            $query .= "cost_modifier='$cost_modifier', mesh='$mesh', icon='$icon', not_usable_with='$not_usable_with', equip_script='$equip_script'";
            mysql_query2($query);
            echo '<p class="error">Succesfully created new loot modifier.</p>';
            unset($_POST);
            listLootModifiers();
            return;
        }
        elseif ($_POST['action'] == 'Delete') 
        {
            if (!checkaccess('crafting', 'delete')) 
            {
                echo '<p class="error">You are not authorized to delete loot modifiers</p>';
                return;
            }
            $query = "DELETE FROM loot_modifiers WHERE id='$id' LIMIT 1";
            mysql_query2($query);
            echo '<p class="error">Entry with ID '.$id.' was succesfully deleted.</p>';
            unset($_POST);
            listLootModifiers();
            return;
        }
        else
        {
            echo '<p class="error">Unknown commit command.</p>';
        }
    }
}

?>
