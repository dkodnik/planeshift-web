<?php

function listarmorvsweapon()
{
    if (!checkaccess('natres', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    $query = 'SELECT * from armor_vs_weapon';
    $result = mysql_query2($query);
    $alt = false;
    echo "<table>\n";
    echo '<tr><th>ID</th><th>1a</th><th>1b</th><th>1c</th><th>2a</th><th>2b</th><th>2c</th><th>3a</th><th>3b</th><th>3c</th><th>weapon type</th>';
    if (checkaccess('natres', 'edit'))
    {
        echo '<th>actions</th>';
    }
    echo "</tr>\n";
    while($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        echo '<tr class="color_'.(($alt = !$alt) ? 'a' : 'b').'">';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['1a'].'</td>';
        echo '<td>'.$row['1b'].'</td>';
        echo '<td>'.$row['1c'].'</td>';
        echo '<td>'.$row['2a'].'</td>';
        echo '<td>'.$row['2b'].'</td>';
        echo '<td>'.$row['2c'].'</td>';
        echo '<td>'.$row['3a'].'</td>';
        echo '<td>'.$row['3b'].'</td>';
        echo '<td>'.$row['3c'].'</td>';
        echo '<td>'.$row['weapon_type'].'</td>';
        if (checkaccess('natres', 'edit'))
        {
            echo '<td><a href="./index.php?do=editarmorvsweapon&amp;id='.$row['id'].'">Edit</a></td>';
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
}
// 	id 	1a 	1b 	1c 	1d 	2a 	2b 	2c 	3a 	3b 	3c 	weapon_type

function editarmorvsweapon()
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
    if (isset($_POST['commit']) && ($_POST['commit'] == "Update Armor vs Weapon"))
    {  // star variables with underscores, php does not allow variables to start with numbers.
        $_1a = mysql_real_escape_string($_POST['1a']);
        $_1b = mysql_real_escape_string($_POST['1b']);
        $_1c = mysql_real_escape_string($_POST['1c']);
        $_2a = mysql_real_escape_string($_POST['2a']);
        $_2b = mysql_real_escape_string($_POST['2b']);
        $_2c = mysql_real_escape_string($_POST['2c']);
        $_3a = mysql_real_escape_string($_POST['3a']);
        $_3b = mysql_real_escape_string($_POST['3b']);
        $_3c = mysql_real_escape_string($_POST['3c']);
        // notice that the sql fields do not have an underscore in front of them.
        $query = "UPDATE armor_vs_weapon SET 1a='$_1a', 1b='$_1b', 1c='$_1c', 2a='$_2a', 2b='$_2b', 2c='$_2c', 3a='$_3a', 3b='$_3b', 3c='$_3c' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        editarmorvsweapon();
    }
    else
    {
        $query = "SELECT * from armor_vs_weapon WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<p class="header">Edit Armor vs Weapon</p>';
        echo '<form action="./index.php?do=editarmorvsweapon&amp;id='.$id.'" method="post">';
        echo "<table>\n";
        echo "<tr><th>Field</th><th>Value</th></tr>\n";
        echo '<tr><td>ID</td><td>'.$row['id']."</td></tr>\n";
        echo '<tr><td>Weapon Type</td><td>'.$row['weapon_type']."</td></tr>\n";
        echo '<tr><td>1a</td><td><input type="text" name="1a" value="'.$row['1a'].'"></td></tr>'."\n";
        echo '<tr><td>1b</td><td><input type="text" name="1b" value="'.$row['1b'].'"></td></tr>'."\n";
        echo '<tr><td>1c</td><td><input type="text" name="1c" value="'.$row['1c'].'"></td></tr>'."\n";
        echo '<tr><td>2a</td><td><input type="text" name="2a" value="'.$row['2a'].'"></td></tr>'."\n";
        echo '<tr><td>2b</td><td><input type="text" name="2b" value="'.$row['2b'].'"></td></tr>'."\n";
        echo '<tr><td>2c</td><td><input type="text" name="2c" value="'.$row['2c'].'"></td></tr>'."\n";
        echo '<tr><td>3a</td><td><input type="text" name="3a" value="'.$row['3a'].'"></td></tr>'."\n";
        echo '<tr><td>3b</td><td><input type="text" name="3b" value="'.$row['3b'].'"></td></tr>'."\n";
        echo '<tr><td>3c</td><td><input type="text" name="3c" value="'.$row['3c'].'"></td></tr>'."\n";
        echo '<tr><td></td><td><input type=submit name="commit" value="Update Armor vs Weapon"/></td></tr>'."\n";
        echo "</table></form>\n";
    }
}

?>