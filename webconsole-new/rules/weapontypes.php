<?php

function listweapontypes()
{
    if (!checkaccess('natres', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $query = 'SELECT w.id, w.name, s.name AS skill FROM weapon_types AS w LEFT JOIN skills AS s ON s.skill_id=w.skill';
    $result = mysql_query2($query);
    $alt = false;
    echo "<table>\n";
    echo '<tr><th>ID</th><th>name</th><th>skill</th>';
    if (checkaccess('natres', 'edit'))
    {
        echo '<th>actions</th>';
    }
    echo "</tr>\n";
    while($row = fetchSqlAssoc($result))
    {
        echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.htmlentities($row['name']).'</td>';
        echo '<td>'.htmlentities($row['skill']).'</td>';
        if (checkaccess('natres', 'edit'))
        {
            echo '<td><a href="./index.php?do=editweapontypes&amp;id='.$row['id'].'">Edit</a>';
            echo '<a href="./index.php?do=editweapontypes&amp;id='.$row['id'].'&amp;action=delete"> -- Delete</a></td>';
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
    // show "Create" table for all allowed to edit this.
    if (checkaccess('natres', 'edit'))
    {
        $skills = PrepSelect('skill');
        echo '<p class="header">Create Weapon Type</p>';
        echo '<form action="./index.php?do=editweapontypes" method="post">';
        echo "<table>\n";
        echo "<tr><td>Field</td><td>Value</td></tr>\n";
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>'."\n";
        echo '<tr><td>Damage</td><td>'.DrawSelectBox('skill', $skills, 'skill', '').'</td></tr>'."\n";
        echo '<tr><td></td><td><input type="submit" name="commit" value="Create Weapon Type" /></td></tr>'."\n";
        echo "</table></form>\n";

    }
}

function editweapontypes()
{
    if (!checkaccess('natres', 'edit'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $id = -1;
    if (isset($_GET['id']))
    {
        $id = escapeSqlString($_GET['id']);
    }
    elseif (isset($_POST['id']))
    {
        $id = escapeSqlString($_POST['id']);
    }
    // this one doesn't use ID, so we need to put it here.
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Create Weapon Type')
    {
        $name = escapeSqlString($_POST['name']);
        $skill = escapeSqlString($_POST['skill']);
        $query = "INSERT INTO weapon_types (name, skill) VALUES ('$name', '$skill')";
        $result = mysql_query2($query);
        echo '<p class="error">Creation Successful</p>';
        unset($_POST);
        listweapontypes();
        return;
    }
    else
    {
        echo '<p class="error">Cannot edit without ID</p>';
        return;
    }
    if (!is_numeric($id) || 0 > $id)
    {
        echo '<p class="error">Invalid ID</p>';
        return;
    }
    if (isset($_POST['commit']) && ($_POST['commit'] == "Update Weapon Type"))
    {
        $name = escapeSqlString($_POST['name']);
        $skill = escapeSqlString($_POST['skill']);
        $query = "UPDATE weapon_types SET name='$name', skill='$skill' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        editweapontypes();
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        echo '<p class="error">You are about to delete Weapon Type id "'.$id.'" </p>';
        echo '<form action="./index.php?do=editweapontypes&amp;id='.$id.'" method="post">';
        echo '<div><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $query = "DELETE FROM weapon_types WHERE id='$id'";
        mysql_query2($query);
        echo '<p class="error">Delete succesfull</p>';
        unset($_POST);
        listweapontypes();
    }
    else
    {
        $query = "SELECT * FROM weapon_types WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        $skills = PrepSelect('skill');
        echo '<p class="header">Edit Weapon Type</p>';
        echo '<form action="./index.php?do=editweapontypes&amp;id='.$id.'" method="post">';
        echo "<table>\n";
        echo "<tr><th>Field</th><th>Value</th></tr>\n";
        echo '<tr><td>ID</td><td>'.$row['id']."</td></tr>\n";
        echo '<tr><td>name</td><td><input type="text" name="name" value="'.htmlentities($row['name']).'" /></td></tr>'."\n";
        echo '<tr><td>skill</td><td>'.DrawSelectBox('skill', $skills, 'skill', $row['skill']).'</td></tr>'."\n";
        echo '<tr><td></td><td><input type="submit" name="commit" value="Update Weapon Type"/></td></tr>'."\n";
        echo "</table></form>\n";
    }
}

?>