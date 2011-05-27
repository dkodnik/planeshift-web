 <?php
function listnpctypes() 
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions!</p>';
        return;
    }
    $query = 'SELECT id, name, parents, ang_vel, vel, collision, out_of_bounds, in_bounds, falling, script FROM sc_npctypes';
    if (isset($_GET['id']) && $_GET['id']!='')
    {
        $id = mysql_real_escape_string($_GET['id']);
        $query .= " WHERE w.id='$id'";
    }
    if (isset($_GET['sort']))
    {
        switch($_GET['sort'])
        {
            case 'id':
                $query .= ' ORDER BY id';
                break;
            case 'name':
                $query .= ' ORDER BY name';
                break;
            case 'parents':
                $query .= ' ORDER BY parents';
                break;
            default:
                $query .= ' ORDER BY name';
        }
    }
    else
    {
        $query .= ' ORDER BY name';
    }
    if (isset($_GET['limit']) && is_numeric($_GET['limit'])){
        $prev_lim = $_GET['limit'] - 30;
        $lim = $_GET['limit'];
        $query = $query . " LIMIT $prev_lim, 30"; // limit 1, 10 is offset 1, taking 10 records.
    }
    else
    {
        $query = $query . " LIMIT 30";
        $lim = 30;
        $prev_lim = 0;
    }
    $result = mysql_query2($query);
    if (mysql_numrows($result) == 0)
    {
        echo '<p class="error">No NPC Types were found.</p>';
        return;
    }
    if ($lim > 30)
    {
        echo '<a href="./index.php?do=listnpctypes';
        if (isset($_GET['sort']))
        {
            echo '&amp;sort='.$_GET['sort'];
        }
        echo '&amp;limit='.$prev_lim.'">Previous Page</a> ';
    }
    echo ' - Displaying records '.$prev_lim.' through '.$lim.' - ';
    $where = (isset($id) ? "WHERE id = $id" : '');
    $result2 = mysql_query2('select count(id) AS mylimit FROM sc_npctypes AS w'.$where);
    $row2 = mysql_fetch_array($result2);
    if ($row2['mylimit'] > $lim)
    {
        echo '<a href="./index.php?do=listnpctypes';
        if (isset($_GET['sort']))
        {
            echo '&amp;sort='.$_GET['sort'];
        }
        echo '&amp;limit='.($lim+30).'">Next Page</a>';
    }
    echo '<table border="1">';
    $limit = (isset($_GET['limit']) ? '&amp;limit='.$_GET['limit'] : '');
    echo '<tr><th><a href="./index.php?do=listnpctypes&amp;sort=id'.$limit.'">ID</a></th>';
    echo '<th><a href="./index.php?do=listnpctypes&amp;sort=name'.$limit.'">Name</a></th>';
    echo '<th><a href="./index.php?do=listnpctypes&amp;sort=parents'.$limit.'">Parents</a></th>';
    echo '<th>Ang Vel</th><th>Vel</th><th>Collision</th><th>Out Of Bounds</th><th>In Bounds</th><th>Falling</th>';

    if (checkaccess('npcs', 'edit'))
    {
        echo '<th>actions</th>';
    }
    echo '</tr>';
    
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
    {
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['parents'].'</td>';
        echo '<td>'.$row['ang_vel'].'</td>';
        echo '<td>'.$row['vel'].'</td>';
        echo '<td>'.$row['collision'].'</td>';
        echo '<td>'.$row['out_of_bounds'].'</td>';
        echo '<td>'.$row['in_bounds'].'</td>';
        echo '<td>'.$row['falling'].'</td>';
        if (checkaccess('npcs', 'edit'))
        {
            echo '<td><form action="./index.php?do=editnpctypes" method="post">';
            echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
            echo '<input type="submit" name="action" value="Edit" />';
            if (checkaccess('npcs', 'delete'))
            {
                echo '<br/><input type="submit" name="action" value="Delete" />';
            }
            echo '</form></td>';
        }
        echo '</tr>';
    }
    echo '</table>';
    if (checkaccess('npcs', 'create'))
    {
        echo '<hr><table border="1"><form action="./index.php?do=createnpctypes" method="post">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" size="100" name="name"></td></tr>';
        echo '<tr><td>Parents</td><td><input type="text" size="100" name="parents"></td></tr>';
        echo '<tr><td>Ang Vel</td><td><input type="text" name="ang_vel"></td></tr>';
        echo '<tr><td>Vel</td><td><input type="text" name="vel"</td></tr>';
        echo '<tr><td>Collision</td><td><input type="text" name="collision"></td></tr>';
        echo '<tr><td>Out Of Bounds</td><td><input type="text" name="out_of_bounds"></td></tr>';
        echo '<tr><td>In Bounds</td><td><input type="text" name="in_bounds"></td></tr>';
        echo '<tr><td>Falling</td><td><input type="text" name="falling"></td></tr>';
        echo '<tr><td>Script</td><td><textarea rows="15" cols="80" name="script"></textarea></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="action" value="Create"></td></tr>';
        echo '</form></table>';
    }
}

function editnpctypes() 
{
    if (!isset($_POST['action']))
    {
        echo '<p class="error">No action specified.</p>';
        return;
    }
    if (!isset($_POST['id']) || !ctype_digit($_POST['id'])) 
    {
        echo '<p class="error">Invalid ID.</p>';
        return;
    }
    $id = mysql_real_escape_string($_POST['id']);
    $action = $_POST['action'];
    if ($action == 'Delete')
    {
        if (!checkaccess('npcs', 'delete'))
        {
            echo '<p class="error">You are not authorized to use these functions!</p>';
            return;
        }
        $query = "DELETE FROM sc_npctypes WHERE id='$id' LIMIT 1";
        mysql_query($query);
        echo '<p class="error">Delete of entry with id "'.$id.'" succesful</p>';
        listnpctypes();
        return;
    }
    elseif ($action == 'Edit')
    {
        if (!checkaccess('npcs', 'edit'))
        {
            echo '<p class="error">You are not authorized to use these functions!</p>';
            return;
        }
        $query = "SELECT id, name, parents, ang_vel, vel, collision, out_of_bounds, in_bounds, falling, script FROM sc_npctypes WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<hr><table border="1"><form action="./index.php?do=editnpctypes" method="post">';
        echo '<input type="hidden" name="id" value="'.$id.'">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" size="100" name="name" value="'.$row['name'].'"></td></tr>';
        echo '<tr><td>Parents</td><td><input type="text" size="100" name="parents" value="'.$row['parents'].'"></td></tr>';
        echo '<tr><td>Ang Vel</td><td><input type="text" name="ang_vel" value="'.$row['ang_vel'].'"></td></tr>';
        echo '<tr><td>Vel</td><td><input type="text" name="vel" value="'.$row['vel'].'"></td></tr>';
        echo '<tr><td>Collision</td><td><input type="text" name="collision" value="'.$row['collision'].'"></td></tr>';
        echo '<tr><td>Out Of Bounds</td><td><input type="text" name="out_of_bounds" value="'.$row['out_of_bounds'].'"></td></tr>';
        echo '<tr><td>In Bounds</td><td><input type="text" name="in_bounds" value="'.$row['in_bounds'].'"></td></tr>';
        echo '<tr><td>Falling</td><td><input type="text" name="falling" value="'.$row['falling'].'"></td></tr>';
        echo '<tr><td>Script</td><td><textarea rows="15" cols="80" name="script">'.$row['script'].'</textarea></td></tr>';
        echo '<tr><td colspan="2"><input type="submit" name="action" value="Submit Changes"></td></tr>';
        echo '</form></table>';
    }
    elseif ($action == 'Submit Changes')
    {
        if (!checkaccess('npcs', 'edit'))
        {
            echo '<p class="error">You are not authorized to use these functions!</p>';
            return;
        }
        $name = mysql_real_escape_string($_POST['name']);
        $parents = mysql_real_escape_string($_POST['parents']);
        $ang_vel = mysql_real_escape_string($_POST['ang_vel']);
        $vel = mysql_real_escape_string($_POST['vel']);
        $collision = mysql_real_escape_string($_POST['collision']);
        $out_of_bounds = mysql_real_escape_string($_POST['out_of_bounds']);
        $in_bounds = mysql_real_escape_string($_POST['in_bounds']);
        $falling = mysql_real_escape_string($_POST['falling']);
        $script = mysql_real_escape_string($_POST['script']);
        $query = "UPDATE sc_npctypes SET name='$name', parents='$parents', ang_vel='$ang_vel', vel='$vel', collision='$collision', out_of_bounds='$out_of_bounds', in_bounds='$in_bounds', falling='$falling', script='$script' WHERE id='$id'";
        mysql_query2($query);
        echo '<p class="error">Update of npctype with id '.$id.' succesful</p>';
        listnpctypes();
    }
    else
    {
        echo '<p class="error">Unknown action: "'.htmlentities($action).'"</p>';
    }
}

function createnpctypes()
{
    if (!checkaccess('npcs', 'create'))
    {
        echo '<p class="error">You are not authorized to use these functions!</p>';
        return;
    }
    $name = mysql_real_escape_string($_POST['name']);
    $parents = mysql_real_escape_string($_POST['parents']);
    $ang_vel = mysql_real_escape_string($_POST['ang_vel']);
    $vel = mysql_real_escape_string($_POST['vel']);
    $collision = mysql_real_escape_string($_POST['collision']);
    $out_of_bounds = mysql_real_escape_string($_POST['out_of_bounds']);
    $in_bounds = mysql_real_escape_string($_POST['in_bounds']);
    $falling = mysql_real_escape_string($_POST['falling']);
    $script = mysql_real_escape_string($_POST['script']);
    $query = "INSERT INTO sc_npctypes SET name='$name', parents='$parents', ang_vel='$ang_vel', vel='$vel', collision='$collision', out_of_bounds='$out_of_bounds', in_bounds='$in_bounds', falling='$falling', script='$script'";
    mysql_query2($query);
    echo '<p class="error">Creation of npctype succesful</p>';
    listnpctypes();
}