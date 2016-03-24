<?php

function charactercreationevents() 
{
    // block unauthorized access
    if (!checkaccess('other', 'read')) 
    {
        echo '<p class="error">You are not authorized to view Creation Events</p>';
        return;
    }
    if (isset($_POST['commit']) && !checkaccess('other', 'edit')) 
    {
        echo '<p class="error">You are not authorized to edit Creation Events</p>';
        return;
    }
    
    // after the handling of commit, the script will resume with the listing of all creation events.
    if (isset($_POST['commit']) && $_POST['commit'] == 'Create Event')
    {
        $name = escapeSqlString($_POST['name']);
        $description = escapeSqlString($_POST['description']);
        $cp_cost = escapeSqlString($_POST['cp_cost']);
        $scriptname = escapeSqlString($_POST['scriptname']);
        $choice_area = escapeSqlString($_POST['choice_area']);
        $sql = "INSERT INTO character_creation (name, description, cp_cost, scriptname, choice_area) VALUES ('$name', '$description', '$cp_cost', '$scriptname', '$choice_area')";
        mysql_query2($sql);
        echo '<p class="error">Event added.</p>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $id = escapeSqlString($_POST['id']);
        $sql = "DELETE FROM character_creation WHERE id='$id'";
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
        $choice_area = escapeSqlString($_POST['choice_area']);
        $sql = "UPDATE character_creation SET name='$name', description='$description', cp_cost='$cp_cost', scriptname='$scriptname', choice_area='$choice_area' WHERE id = '$id'";
        mysql_query2($sql);
        echo '<p class="error">Update succesfull</p>';
    }
    
    // if we print something for any of these actions, nothing else gets printed (no creation event list).
    if (isset($_GET['action']) && $_GET['action'] == 'edit')
    {
        // edit form
        $id = escapeSqlString($_GET['id']);
        $sql = "SELECT name, description, cp_cost, scriptname, choice_area FROM character_creation WHERE id = '$id'";
        $result = mysql_query2($sql);
        $row = fetchSqlAssoc($result);
    
        echo '<p>Editing Creation Event: </p>';
        echo '<form action="./index.php?do=charactercreationevents" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>ID</td><td><input type="hidden" name="id" value="'.$id.'" />'.$id.'</td></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.htmlentities($row['name']).'" /></td></tr>';
        echo '<tr><td>Description</td><td><textarea name="description" rows="6" cols="55" >'.htmlentities($row['description']).'</textarea></td></tr>';
        echo '<tr><td>CP Cost</td><td><input type="text" name="cp_cost" value="'.htmlentities($row['cp_cost']).'" /></td></tr>';
        $scripts = PrepSelect('scripts');
        echo '<tr><td>Script Name</td><td>'.DrawSelectBox('scripts', $scripts, 'scriptname', $row['scriptname']).'</td></tr>';
        echo '<tr><td>Choice Area</td><td><input type="text" name="choice_area" value="'.htmlentities($row['choice_area']).'" /></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Save Changes" /></td></tr>';
        echo '</table>';
        echo '</form>';
        return;
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        $id = escapeSqlString($_GET['id']);
        echo '<p class="error">You are about to delete Creation Event id "'.$id.'" </p>';
        echo '<form action="./index.php?do=charactercreationevents" method="post">';
        echo '<div><input type="hidden" name="id" value="'.$id.'" /><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
        return;
    }
    
    $sort_col = (isset($_GET['sort_col']) ? $_GET['sort_col'] : 'choice_area');
    $sort_dir = (isset($_GET['sort_dir']) ? $_GET['sort_dir'] : 'asc');
    
    $makeSortUrl = function($colName) use (&$sort_col, &$sort_dir)
    {
        // we use htmlentities on the whole string, so not using &amp; here.
        $base = 'index.php?do=charactercreationevents';
        if ($colName == $sort_col && $sort_dir == 'asc')
        {
            return htmlentities($base.'&sort_col='.$colName.'&sort_dir=desc');
        }// basically, else.
        return htmlentities($base.'&sort_col='.$colName.'&sort_dir=asc');
    };
    
    $sort_col = escapeSqlString($sort_col);
    $sort_dir = escapeSqlString($sort_dir);
    
    // Display the main list
    $sql = "SELECT id, name, description, cp_cost, scriptname, choice_area FROM character_creation ORDER BY $sort_col $sort_dir, name";
    $result = mysql_query2($sql);
    
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No Character Creation Event data found.</p>';
    }
    else
    {
        // main list
        echo '<table border="1">'."\n";
        echo '<tr><th><a href="'.$makeSortUrl('id').'">ID</a></th><th><a href="'.$makeSortUrl('name').'">Name</a></th><th>Description</th>';
        echo '<th>CP Cost</th><th>Script Name</th><th><a href="'.$makeSortUrl('choice_area').'">Choice Area</a></th><th>Actions</th></tr>'."\n";
        
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr>';
            echo '<td>'.$row['id'].'</td>';
            echo '<td>'.htmlentities($row['name']).'</td>';
            echo '<td>'.htmlentities($row['description']).'</td>';
            echo '<td>'.htmlentities($row['cp_cost']).'</td>';
            echo '<td>'.htmlentities($row['scriptname']).'</td>';
            echo '<td>'.htmlentities($row['choice_area']).'</td>';
            echo '<td>';
            if (checkAccess('other', 'edit'))
            {
                $url = './index.php?do=charactercreationevents&amp;id='.$row['id'];
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
        echo '<form action="./index.php?do=charactercreationevents" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>';
        echo '<tr><td>Description</td><td><textarea name="description" rows="6" cols="55" ></textarea></td></tr>';
        echo '<tr><td>CP Cost</td><td><input type="text" name="cp_cost" /></td></tr>';
        $scripts = PrepSelect('scripts');
        echo '<tr><td>Script Name</td><td>'.DrawSelectBox('scripts', $scripts, 'scriptname', '').'</td></tr>';
        echo '<tr><td>Choice Area</td><td><input type="text" name="choice_area" /></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="commit" value="Create Event" /></td></tr>';
        echo '</table>';
        echo '</form>';
    }
}

?>