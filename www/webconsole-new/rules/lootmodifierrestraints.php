<?php

function lootmodifierrestraints()
{
    // block unauthorized access
    if (!checkaccess('natres', 'read')) 
    {
        echo '<p class="error">You are not authorized to view Loot Modifier Restraints</p>';
        return;
    }
    if (isset($_POST['commit']) && !checkaccess('natres', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Loot Modifier Restraints</p>';
        return;
    }
    
    // after the handling of commit, the script will resume with the listing of all Loot modifier restraints.
    if (isset($_POST['commit']) && $_POST['commit'] == 'Create Restraint')
    {
        $loot_modifier_id = escapeSqlString($_POST['loot_modifier_id']);
        $item_id = escapeSqlString($_POST['item_id']);
        $allowed = escapeSqlString($_POST['allowed']);
        $sql = "INSERT INTO loot_modifiers_restrains (loot_modifier_id, item_id, allowed) VALUES ('$loot_modifier_id', '$item_id', '$allowed')";
        mysql_query2($sql);
        echo '<p class="error">Restraint added.</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $loot_modifier_id = escapeSqlString($_POST['loot_modifier_id']);
        $item_id = escapeSqlString($_POST['item_id']);
        $sql = "DELETE FROM loot_modifiers_restrains WHERE loot_modifier_id='$loot_modifier_id' AND item_id='$item_id'";
        mysql_query2($sql);
        echo '<p class="error">Delete succesfull</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Save Changes')
    {
        $loot_modifier_id = escapeSqlString($_POST['loot_modifier_id']);
        $item_id = escapeSqlString($_POST['item_id']);
        $allowed = escapeSqlString($_POST['allowed']);
        $sql = "UPDATE loot_modifiers_restrains SET allowed='$allowed' WHERE loot_modifier_id='$loot_modifier_id' AND item_id='$item_id'";
        mysql_query2($sql);
        echo '<p class="error">Update succesfull</p>';
    }
    
    $allowedList = array('Y', 'N');
    $makeListDropdown = function ($name, $list, $selected = -1) 
    {
        $output = '';
        $output .= '<select name="'.$name.'">';
        foreach ($list as $value)
        {
            $output .= '<option value="'.$value.'" '.($value == $selected ? 'selected="selected"' : '').'>'.$value.'</option>';
        }
        $output .= '</select>';
        return $output;
    };
    
    // if we print something for any of these actions, nothing else gets printed (no loot modifier restraints list).
    if (isset($_GET['action']) && $_GET['action'] == 'edit')
    {
        // edit form
        $loot_modifier_id = escapeSqlString($_GET['loot_modifier_id']);
        $item_id = escapeSqlString($_GET['item_id']);
        $sql = "SELECT concat(lm.modifier_type, ' ', lm.name) AS modifier_name, i.name AS item_name, allowed FROM loot_modifiers_restrains LEFT JOIN ";
        $sql .= "item_stats AS i ON i.id = item_id LEFT JOIN loot_modifiers AS lm ON lm.id = loot_modifier_id WHERE loot_modifier_id='$loot_modifier_id' AND item_id='$item_id'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
    
        echo '<p>Editing Restraint: </p>';
        echo '<form action="./index.php?do=lootmodifierrestraints" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Loot Modifier</td><td><input type="hidden" name="loot_modifier_id" value="'.$loot_modifier_id.'" />'.$row['modifier_name'].'</td></tr>';
        echo '<tr><td>Item</td><td><input type="hidden" name="item_id" value="'.$item_id.'" />'.$row['item_name'].'</td></tr>';
        echo '<tr><td>allowed</td><td>'.$makeListDropdown('allowed', $allowedList, $row['allowed']).'</td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Save Changes" /></td></tr>';
        echo '</table>';
        echo '</form>';
        return;
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        $loot_modifier_id = escapeSqlString($_GET['loot_modifier_id']);
        $item_id = escapeSqlString($_GET['item_id']);
        echo '<p class="error">You are about to delete a restraint for Loot Modifier Id: "'.$loot_modifier_id.'" Combined with Item ID: "'.$item_id.'" </p>';
        echo '<form action="./index.php?do=lootmodifierrestraints" method="post">';
        echo '<div><input type="hidden" name="loot_modifier_id" value="'.$loot_modifier_id.'" /><input type="hidden" name="item_id" value="'.$item_id.'" />';
        echo '<input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
        return;
    }
    
    // Display the main list
    $sql = "SELECT loot_modifier_id, concat(lm.modifier_type, ' ', lm.name) AS modifier_name, item_id, i.name AS item_name, allowed FROM loot_modifiers_restrains ";
    $sql .= "LEFT JOIN item_stats AS i ON i.id = item_id LEFT JOIN loot_modifiers AS lm ON lm.id = loot_modifier_id ORDER BY i.name, lm.modifier_type, lm.name";
    
    $sql2 = "SELECT COUNT(*) FROM loot_modifiers_restrains";
    $item_count = fetchSqlRow(mysql_query2($sql2));
    $nav = RenderNav('do=lootmodifierrestraints', $item_count[0]);
    $sql .= $nav['sql'];
    echo $nav['html'];
    unset($nav);
    
    $result = mysql_query2($sql);
    
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No Loot Modifier Restraints found.</p>';
    }
    else
    {
        // main list
        echo '<table>'."\n";
        echo '<tr><th>Modifier name</th><th>Item Name</th><th>Allowed</th><th>Actions</th></tr>'."\n";
        
        $alt = false;
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
            echo '<td>'.htmlentities($row['modifier_name']).'</td>';
            echo '<td>'.htmlentities($row['item_name']).'</td>';
            echo '<td>'.htmlentities($row['allowed']).'</td>';
            echo '<td>';
            if (checkAccess('natres', 'edit'))
            {
                $url = './index.php?do=lootmodifierrestraints&amp;loot_modifier_id='.$row['loot_modifier_id'].'&amp;item_id='.$row['item_id'];
                echo '<a href="'.$url.'&amp;action=edit">Edit</a> - <a href="'.$url.'&amp;action=delete">Delete</a>';
            }
            echo '</td>';
            echo '</tr>'."\n";
        }
        echo '</table>'."\n";
    }
    if (checkAccess('natres', 'edit'))
    {
        // create form
        echo '<hr/><p>Create new Restraint: </p>';
        echo '<form action="./index.php?do=lootmodifierrestraints" method="post">';
        echo '<table border="1">';
        $lootModifiers = PrepSelect('lootmodifiers');
        echo '<tr><td>Loot Modifier</td><td>'.DrawSelectBox('lootmodifiers', $lootModifiers, 'loot_modifier_id', '').'</td></tr>';
        echo '<tr><td>Item</td><td>'.DrawItemSelectBox('item_id').'</td></tr>';
        echo '<tr><td>allowed</td><td>'.$makeListDropdown('allowed', $allowedList).'</td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Restraint" /></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

?>