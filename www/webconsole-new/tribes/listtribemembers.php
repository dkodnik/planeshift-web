<?php

function listtribemembers()
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $tribe_id = '';
    if (isset($_GET['tribe_id']) && is_numeric($_GET['tribe_id'])) 
    {
        $tribe_id = escapeSqlString($_GET['tribe_id']);
    }
    // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('npcs', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Tribes</p>';
        return;
    }
    
    // this one is not a true enum.
    $enumFlags = array('DYNAMIC', 'STATIC');
    $makeEnumDropdown = function ($name, $enumArray, $selected = -1) 
    {
        $output = '';
        $output .= '<select name="'.$name.'">';
        foreach ($enumArray as  $value)
        {
            $output .= '<option value="'.$value.'" '.($value == $selected ? 'selected="selected"' : '').'>'.$value.'</option>';
        }
        $output .= '</select>';
        return $output;
    };
    
    // after the handling of commit, the script will resume with the listing of all members for this tribe.
    if (isset($_POST['commit']) && $_POST['commit'] == 'Create Member')
    {
        $member_id = escapeSqlString($_POST['member_id']);
        $member_type = escapeSqlString($_POST['member_type']);
        $flags = escapeSqlString($_POST['flags']);
        $sql = "SELECT name, character_type FROM characters WHERE id = '$member_id'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
        if (sqlNumRows($result) > 0 && $row['character_type'] != 0)
        {
            $sql = "INSERT INTO tribe_members (tribe_id, member_id, member_type, flags) VALUES ('$tribe_id', '$member_id', '$member_type', '$flags')";
            mysql_query2($sql);
            echo '<p class="error">Member added.</p>';
        }
        else
        {
            echo '<p class="error">Invalid NPC ID, member not added.</p>';
        }
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $member_id = escapeSqlString($_GET['member_id']);
        $sql = "DELETE FROM tribe_members WHERE member_id='$member_id' AND tribe_id='$tribe_id'";
        mysql_query2($sql);
        echo '<p class="error">Delete succesfull</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Save Changes')
    {
        $member_id = escapeSqlString($_GET['member_id']);
        $member_type = escapeSqlString($_POST['member_type']);
        $flags = escapeSqlString($_POST['flags']);
        $sql = "UPDATE tribe_members SET member_type='$member_type', flags='$flags' WHERE member_id = '$member_id' AND tribe_id = '$tribe_id'";
        mysql_query2($sql);
        echo '<p class="error">Update succesfull</p>';
    }
    
    // remove "action" from the $url string, since we do not want the forms to keep editing/deleting.
    $urlParts = array();
    parse_str($_SERVER['QUERY_STRING'], $urlParts);
    if (array_key_exists('action', $urlParts))
    {
        unset($urlParts['action']);
    }
    $url = './index.php?'.htmlentities(http_build_query($urlParts));
    
    // if we print something for any of these actions, nothing else gets printed (no Member list).
    if (isset($_GET['action']) && $_GET['action'] == 'edit')
    {
        // edit form (after editing, the list (if using "list tribe members" will automatically limit to the last edited tribe, consider it a "feature").
        $member_id = escapeSqlString($_GET['member_id']);
        $sql = "SELECT tm.member_type, tm.flags, c.name FROM tribe_members AS tm LEFT JOIN characters AS c ON c.id=tm.member_id WHERE member_id = '$member_id' AND tribe_id = '$tribe_id'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
    
        echo '<p>Editing Members: </p>';
        echo '<form action="'.$url.'" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Member Name</td><td>'.$row['name'].'</td></tr>';
        echo '<tr><td>Member Type</td><td><input type="text" name="member_type" value="'.htmlentities($row['member_type']).'" /></td></tr>';
        echo '<tr><td>Flags</td><td>'.$makeEnumDropdown('flags', $enumFlags, $row['flags']).'</td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Save Changes" /></td></tr>';
        echo '</table>';
        echo '</form>';
        return;
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        $member_id = escapeSqlString($_GET['member_id']);
        echo '<p class="error">You are about to delete tribe member ID "'.$member_id.'" from tribe ID "'.$tribe_id.'"</p>';
        echo '<form action="'.$url.'" method="post">';
        echo '<div><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
        return;
    }
    
    // remove "action" from the $url string, as well as member_id, since we do not want them to reappear automatically in urls on the main listing.
    $urlParts = array();
    parse_str($_SERVER['QUERY_STRING'], $urlParts);
    if (array_key_exists('action', $urlParts))
    {
        unset($urlParts['action']);
    }
    if (array_key_exists('member_id', $urlParts))
    {
        unset($urlParts['member_id']);
    }
    $url = http_build_query($urlParts);
    
    $sql = 'SELECT tm.tribe_id, t.name AS tribe_name, tm.member_id, tm.member_type, c.name, tm.flags FROM tribe_members AS tm LEFT JOIN characters AS c ON c.id=tm.member_id LEFT JOIN tribes AS t ON t.id=tm.tribe_id';

    if ($tribe_id != '')
    {
        $sql .= " WHERE tm.tribe_id='$tribe_id'";
    }
    $sql .= ' ORDER BY t.name, c.name';
    
    $sql2 = "SELECT COUNT(*) FROM tribe_members".($tribe_id == '' ? '': " WHERE tribe_id = '$tribe_id'");
    $item_count = fetchSqlRow(mysql_query2($sql2));
    $nav = RenderNav($url, $item_count[0]);
    $sql .= $nav['sql'];
    echo $nav['html'];
    unset($nav);
    $url = './index.php?'.htmlentities($url);
    
    $result = mysql_query2($sql);
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No Tribe Members Found</p>';
    }
    else
    {
        echo '<table>'."\n";
        echo '<tr><th>Tribe</th><th>Member Name</th><th>Member Type</th><th>Flags</th><th>Actions</th></tr>'."\n";
        
        $alt = false;
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
            echo '<td>'.$row['tribe_name'].'</td>';
            echo '<td><a href="./index.php?do=npc_details&amp;sub=main&amp;npc_id='.$row['member_id'].'">'.$row['name'].'</a></td>';
            echo '<td>'.$row['member_type'].'</td>';
            echo '<td>'.$row['flags'].'</td>';
            echo '<td>';
            if (checkAccess('npcs', 'edit'))
            {
                $myUrl = $url.'&amp;member_id='.$row['member_id'].($tribe_id != '' ? '' : '&amp;tribe_id='.$row['tribe_id']);
                echo '<a href="'.$myUrl.'&amp;action=edit">Edit</a> - <a href="'.$myUrl.'&amp;action=delete">Delete</a>';
            }
            echo '</td>';
            echo '</tr>'."\n";
        }
        echo '</table>'."\n";
    }
    
    if (checkAccess('npcs', 'edit') && $tribe_id != '')
    {
        echo '<hr/><p>Create new Member: </p>';
        echo '<form action="'.$url.'" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Member id</td><td><input type="text" name="member_id" /></td></tr>';
        echo '<tr><td>Member Type</td><td><input type="text" name="member_type" /></td></tr>';
        echo '<tr><td>Flags</td><td>'.$makeEnumDropdown('flags', $enumFlags).'</td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Member" /></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

?>
