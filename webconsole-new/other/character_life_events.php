<?php

function characterlifeevents() 
{
    // block unauthorized access
    if (isset($_POST['commit']) && !checkaccess('other', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Life Events</p>';
        return;
    }
    
    // after the handling of commit, the script will resume with the listing of all creation events.
    if (isset($_POST['commit']) && $_POST['commit'] == 'Create Event')
    {
        $name = escapeSqlString($_POST['name']);
        $description = escapeSqlString($_POST['description']);
        $cp_cost = escapeSqlString($_POST['cp_cost']);
        $scriptname = escapeSqlString($_POST['scriptname']);
        $is_base = escapeSqlString($_POST['is_base']);
        $sql = "INSERT INTO char_create_life (name, description, cp_cost, scriptname, is_base) VALUES ('$name', '$description', '$cp_cost', '$scriptname', '$is_base')";
        mysql_query2($sql);
        echo '<p class="error">Event added.</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $id = escapeSqlString($_POST['id']);
        $sql = "DELETE FROM char_create_life WHERE id='$id'";
        mysql_query2($sql);
        echo '<p class="error">Delete succesfull</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Save Changes')
    {
        $id = escapeSqlString($_POST['id']);
        $name = escapeSqlString($_POST['name']);
        $description = escapeSqlString($_POST['description']);
        $cp_cost = escapeSqlString($_POST['cp_cost']);
        $scriptname = escapeSqlString($_POST['scriptname']);
        $is_base = escapeSqlString($_POST['is_base']);
        $sql = "UPDATE char_create_life SET name='$name', description='$description', cp_cost='$cp_cost', scriptname='$scriptname', is_base='$is_base' WHERE id = '$id'";
        mysql_query2($sql);
        echo '<p class="error">Update succesfull</p>';
    }
    
    $baseList = array('Y', 'N');
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
    
    // if we print something for any of these actions, nothing else gets printed (no life event list).
    if (isset($_GET['action']) && $_GET['action'] == 'edit')
    {
        // edit form
        $id = escapeSqlString($_GET['id']);
        $sql = "SELECT name, description, cp_cost, scriptname, is_base FROM char_create_life WHERE id = '$id'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
    
        echo '<p>Editing Creation Event: </p>';
        echo '<form action="./index.php?do=characterlifeevents" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>ID</td><td><input type="hidden" name="id" value="'.$id.'" />'.$id.'</td></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.htmlentities($row['name']).'" /></td></tr>';
        echo '<tr><td>Description</td><td><input type="text" name="description" value="'.htmlentities($row['description']).'" /></td></tr>';
        echo '<tr><td>CP Cost</td><td><input type="text" name="cp_cost" value="'.htmlentities($row['cp_cost']).'" /></td></tr>';
        $scripts = PrepSelect('scripts');
        echo '<tr><td>Script Name</td><td>'.DrawSelectBox('scripts', $scripts, 'scriptname', $row['scriptname']).'</td></tr>';
        echo '<tr><td>Is Base</td><td>'.$makeListDropdown('is_base', $baseList, $row['is_base']).'</td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Save Changes" /></td></tr>';
        echo '</table>';
        echo '</form>';
        return;
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        $id = escapeSqlString($_GET['id']);
        echo '<p class="error">You are about to delete Life Event id "'.$id.'" </p>';
        echo '<form action="./index.php?do=characterlifeevents" method="post">';
        echo '<div><input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
        return;
    }
    
    // Display the main list
    $sql = "SELECT id, name, description, cp_cost, scriptname, is_base FROM char_create_life ORDER BY name";
    $result = mysql_query2($sql);
    
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No Character Life Event data found.</p>';
    }
    else
    {
        // main list
        echo '<table border="1">'."\n";
        echo '<tr><th>ID</th><th>Name</th><th>Description</th><th>CP Cost</th><th>Script Name</th><th>Is Base</th><th>Actions</th></tr>'."\n";
        
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr>';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.htmlentities($row['name']).'</td>';
            echo '<td>'.htmlentities($row['description']).'</td>';
            echo '<td>'.htmlentities($row['cp_cost']).'</td>';
            echo '<td>'.htmlentities($row['scriptname']).'</td>';
            echo '<td>'.htmlentities($row['is_base']).'</td>';
            echo '<td>';
            if (checkAccess('other', 'edit'))
            {
                $url = './index.php?do=characterlifeevents&amp;id='.$row['id'];
                echo '<a href="'.$url.'&amp;action=edit">Edit</a> - <a href="'.$url.'&amp;action=delete">Delete</a>';
            }
            echo '</td>';
            echo '</tr>'."\n";
        }
        echo '</table>'."\n";
    }
    if (checkAccess('other', 'edit'))
    {
        // create form
        echo '<hr/><p>Create new Creation Event: </p>';
        echo '<form action="./index.php?do=characterlifeevents" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>';
        echo '<tr><td>Description</td><td><input type="text" name="description" /></td></tr>';
        echo '<tr><td>CP Cost</td><td><input type="text" name="cp_cost" /></td></tr>';
        $scripts = PrepSelect('scripts');
        echo '<tr><td>Script Name</td><td>'.DrawSelectBox('scripts', $scripts, 'scriptname', '').'</td></tr>';
        echo '<tr><td>Is Base</td><td>'.$makeListDropdown('is_base', $baseList).'</td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Event" /></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

?>