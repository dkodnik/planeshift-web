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
    while($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['skill'].'</td>';
        if (checkaccess('natres', 'edit'))
        {
            echo '<td><a href="./index.php?do=editweapontypes&amp;id='.$row['id'].'">Edit</a></td>';
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
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
        $id = mysql_real_escape_string($_GET['id']);
    }
    elseif (isset($_POST['id']))
    {
        $id = mysql_real_escape_string($_POST['id']);
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
        $skill = mysql_real_escape_string($_POST['skill']);
        $query = "UPDATE weapon_types SET skill='$skill' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        editweapontypes();
    }
    else
    {
        $query = "SELECT * FROM weapon_types WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        $skills = PrepSelect('skill');
        echo '<p class="header">Edit Weapon Type</p>';
        echo '<form action="./index.php?do=editweapontypes&amp;id='.$id.'" method="post">';
        echo "<table>\n";
        echo "<tr><th>Field</th><th>Value</th></tr>\n";
        echo '<tr><td>ID</td><td>'.$row['id']."</td></tr>\n";
        echo '<tr><td>name</td><td>'.$row['name']."</td></tr>\n";
        echo '<tr><td>skill</td><td>'.DrawSelectBox('skill', $skills, 'skill', $row['skill']).'</td></tr>'."\n";
        echo '<tr><td></td><td><input type="submit" name="commit" value="Update Weapon Type"/></td></tr>'."\n";
        echo "</table></form>\n";
    }
}

?>