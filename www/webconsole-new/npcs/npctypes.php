 <?php
function listnpctypes() 
{
    if (!checkaccess('npcs', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions!</p>';
        return;
    }
    $query = 'SELECT id, name, parents, ang_vel, vel, collision, out_of_bounds, in_bounds, falling, template, script FROM sc_npctypes';
    if (isset($_GET['template']) && $_GET['template']=='1')
    {
        $template = escapeSqlString($_GET['template']);
        $query .= " WHERE template=1";
    }
    else
    {
        $query .= " WHERE template=0";
    }
    if (isset($_GET['id']) && $_GET['id']!='')
    {
        $id = escapeSqlString($_GET['id']);
        $query .= " AND id='$id'";
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
    if (sqlNumRows($result) == 0)
    {
        echo '<p class="error">No NPC Types were found.</p>';
    }
    else 
    {
        if ($lim > 30)
        {
            echo '<a href="./index.php?do=listnpctypes';
            if (isset($_GET['sort']))
            {
                echo '&amp;sort='.$_GET['sort'];
            }
            if (isset($_GET['template']))
            {
                echo '&amp;template='.$_GET['template'];
            }
            echo '&amp;limit='.$prev_lim.'">Previous Page</a> ';
        }
        echo ' - Displaying records '.$prev_lim.' through '.$lim.' - ';
        $where = (isset($id) ? "WHERE id = $id" : '');
        $result2 = mysql_query2('select count(id) AS mylimit FROM sc_npctypes AS w'.$where);
        $row2 = fetchSqlAssoc($result2);
        if ($row2['mylimit'] > $lim)
        {
            echo '<a href="./index.php?do=listnpctypes';
            if (isset($_GET['sort']))
            {
                echo '&amp;sort='.$_GET['sort'];
            }
            if (isset($_GET['template']))
            {
                echo '&amp;template='.$_GET['template'];
            }
            echo '&amp;limit='.($lim+30).'">Next Page</a>';
        }
        echo '<table border="1">';
        $limit = (isset($_GET['limit']) ? '&amp;limit='.$_GET['limit'] : '');
        $sort = (isset($_GET['sort']) ? '&amp;sort='.$_GET['sort'] : '');
        $show = (isset($_GET['show']) ? '&amp;show='.$_GET['show'] : '');
        $template = (isset($_GET['template']) ? '&amp;template='.$_GET['template'] : '');
        $options = $template.$limit.$show.$sort;
 
        echo '<tr><th><a href="./index.php?do=listnpctypes&amp;sort=id'.$template.$limit.$show.'">ID</a></th>';
        echo '<th><a href="./index.php?do=listnpctypes&amp;sort=name'.$template.$limit.$show.'">Name</a></th>';
        echo '<th><a href="./index.php?do=listnpctypes&amp;sort=parents'.$template.$limit.$show.'">Parents</a></th>';
        echo '<th>Ang Vel</th><th>Vel</th><th>Collision</th><th>Out Of Bounds</th><th>In Bounds</th><th>Falling</th>';
        echo '<th>Script (<a href="./index.php?do=listnpctypes&amp;show=yes'.$sort.$template.$limit.'">Show</a>/<a href="./index.php?do=listnpctypes&amp;show=no'.$sort.$template.$limit.'">Hide</a>)</th>';

        if (checkaccess('npcs', 'edit'))
        {
            echo '<th>actions</th>';
        }
        echo '<th>NPCs</th></tr>';
        
        while ($row = fetchSqlAssoc($result))
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
            if (isset($_GET['show']) && $_GET['show']=='yes')
            {
                echo '<td><textarea  rows="15" cols="100">'.$row['script'].'</textarea></td>';
            }
            else
            {
                echo '<td>Hidden</td>';
            }

            if (checkaccess('npcs', 'edit'))
            {

                echo '<td><form action="./index.php?do=editnpctypes'.$options.'" method="post">';
                echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
                echo '<input type="submit" name="action" value="Edit" />';
                if (checkaccess('npcs', 'delete'))
                {
                    echo '<br/><input type="submit" name="action" value="Delete" />';
                }
                echo '</form></td>';
            }

	    {
	        echo '<td>';
                $query2 = 'SELECT npc_def.char_id, npc_def.name FROM sc_npc_definitions npc_def, sc_npctypes type where type.name=npc_def.npctype and type.id = '.$row['id'];
                $result2 = mysql_query2($query2);
                while ($row2 = fetchSqlAssoc($result2))
        	{
		   echo '<a href="./index.php?do=npc_details&npc_id='.$row2['char_id'].'&sub=main">'.$row2['name']."</a><br>";
                }
	        echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    if (checkaccess('npcs', 'create'))
    {
        echo '<hr><table border="1"><form action="./index.php?do=createnpctypes'.$options.'" method="post">';
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
	if (isset($_GET['template']) && $_GET['template']=='1')
        {
            echo '<tr><td>Template</td><td><input type="checkbox" name="db_template" checked="checked" /> </td></tr>';
        }
        else
        {
            echo '<tr><td>Template</td><td><input type="checkbox" name="db_template" /> </td></tr>';
        }
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

    #
    # Keep track of user selection for listings.
    #
    $limit = (isset($_GET['limit']) ? '&amp;limit='.$_GET['limit'] : '');
    $sort = (isset($_GET['sort']) ? '&amp;sort='.$_GET['sort'] : '');
    $show = (isset($_GET['show']) ? '&amp;show='.$_GET['show'] : '');
    $template = (isset($_GET['template']) ? '&amp;template='.$_GET['template'] : '');
    $options = $template.$limit.$show.$sort;

    $id = escapeSqlString($_POST['id']);
    $action = $_POST['action'];
    if ($action == 'Delete')
    {
        if (!checkaccess('npcs', 'delete'))
        {
            echo '<p class="error">You are not authorized to use these functions!</p>';
            return;
        }
        $query = "DELETE FROM sc_npctypes WHERE id='$id' LIMIT 1";
        mysql_query2($query);
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
        $query = "SELECT id, name, parents, ang_vel, vel, collision, out_of_bounds, in_bounds, falling, script, template FROM sc_npctypes WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo '<hr><table border="1"><form action="./index.php?do=editnpctypes'.$options.'" method="post">';
        echo '<input type="hidden" name="id" value="'.$id.'">';
        echo '<tr><th>Field</th><th>Value</th><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" size="50" name="name" value="'.$row['name'].'"></td>';
        echo '<td>Parents</td><td><input type="text" size="50" name="parents" value="'.$row['parents'].'"></td></tr>';
        echo '<tr><td>Ang Vel</td><td><input type="text" name="ang_vel" value="'.$row['ang_vel'].'"></td>';
        echo '<td>Vel</td><td><input type="text" name="vel" value="'.$row['vel'].'"> (&lt;custom vel&gt;,$walk,$run)</td></tr>';
        echo '<tr><td>Collision</td><td><input type="text" name="collision" value="'.$row['collision'].'"></td>';
        echo '<td>Out Of Bounds</td><td><input type="text" name="out_of_bounds" value="'.$row['out_of_bounds'].'"></td></tr>';
        echo '<tr><td>In Bounds</td><td><input type="text" name="in_bounds" value="'.$row['in_bounds'].'"></td>';
        echo '<td>Falling</td><td><input type="text" name="falling" value="'.$row['falling'].'"></td></tr>';
        echo '<tr><td>Script</td><td colspan="3"><textarea rows="35" cols="160" name="script">'.$row['script'].'</textarea></td></tr>';
	if ($row['template'] == "1")
        {
            echo '<tr><td>Template</td><td><input type="checkbox" name="db_template" checked="checked" /> </td><td></td><td></td></tr>';
        }
        else
        {
            echo '<tr><td>Template</td><td><input type="checkbox" name="db_template" /> </td><td></td><td></td></tr>';
        }
        echo '<tr><td colspan="4"><input type="submit" name="action" value="Submit Changes"><input type="submit" name="action" value="Cancel"></td></tr>';
        echo '</form></table>';
    }
    elseif ($action == 'Submit Changes')
    {
        if (!checkaccess('npcs', 'edit'))
        {
            echo '<p class="error">You are not authorized to use these functions!</p>';
            return;
        }
        $name = escapeSqlString($_POST['name']);
        $parents = escapeSqlString($_POST['parents']);
        $ang_vel = escapeSqlString($_POST['ang_vel']);
        $vel = escapeSqlString($_POST['vel']);
        $collision = escapeSqlString($_POST['collision']);
        $out_of_bounds = escapeSqlString($_POST['out_of_bounds']);
        $in_bounds = escapeSqlString($_POST['in_bounds']);
        $falling = escapeSqlString($_POST['falling']);
        $db_template = escapeSqlString($_POST['db_template']);
        if ($db_template == "on")
        {
           $db_template = "1";
        }
        else
        {
           $db_template = "0";
        }
        $script = escapeSqlString($_POST['script']);
        $query = "UPDATE sc_npctypes SET name='$name', parents='$parents', ang_vel='$ang_vel', vel='$vel', collision='$collision', out_of_bounds='$out_of_bounds', in_bounds='$in_bounds', falling='$falling', script='$script', template='$db_template' WHERE id='$id'";
        mysql_query2($query);
        echo '<p class="error">Update of npctype with id '.$id.' succesful</p>';
        listnpctypes();
    }
    elseif ($action == 'Cancel')
    {
        echo '<p class="error">Update of npctype with id '.$id.' canceled</p>';
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
    $name = escapeSqlString($_POST['name']);
    $parents = escapeSqlString($_POST['parents']);
    $ang_vel = escapeSqlString($_POST['ang_vel']);
    $vel = escapeSqlString($_POST['vel']);
    $collision = escapeSqlString($_POST['collision']);
    $out_of_bounds = escapeSqlString($_POST['out_of_bounds']);
    $in_bounds = escapeSqlString($_POST['in_bounds']);
    $falling = escapeSqlString($_POST['falling']);
    $db_template = escapeSqlString($_POST['db_template']);
    if ($db_template == "on")
    {
        $db_template = "1";
    }
    else
    {
        $db_template = "0";
    }
    $script = escapeSqlString($_POST['script']);
    $query = "INSERT INTO sc_npctypes SET name='$name', parents='$parents', ang_vel='$ang_vel', vel='$vel', collision='$collision', out_of_bounds='$out_of_bounds', in_bounds='$in_bounds', falling='$falling', template='$db_template', script='$script'";
    mysql_query2($query);
    echo '<p class="error">Creation of npctype succesful</p>';
    listnpctypes();
}

