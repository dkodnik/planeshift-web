<?php

function listattacks()
{
    if (!checkaccess('natres', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions.</p>';
        return;
    }
    // navigation
    $sql = 'SELECT COUNT(*) FROM attacks';
    $item_count = fetchSqlRow(mysql_query2($sql));
    $sort_column = (isset($_GET['sort_column']) && !empty($_GET['sort_column']) ? escapeSqlString($_GET['sort_column']) : 'id');
    $sort_dir = (isset($_GET['sort_dir']) && $_GET['sort_dir'] == 'DESC' ? 'DESC' : 'ASC');
    $nav = RenderNav(array('do' => 'listattacks', 'sort_column' => $sort_column, 'sort_dir' => $sort_dir), $item_count[0]);
    // actual query
    $query = 'SELECT a.id, a.name, a.attack_description, a.damage, a.attackType, at.name AS attackTypeName, a.delay, a.range, a.outcome FROM attacks AS a LEFT JOIN attack_types AS at ON at.id=a.attackType';
    $query .= ' ORDER BY '.$sort_column.' '.$sort_dir.', name';
    $query .= $nav['sql'];
    $result = mysql_query2($query);
    $alt = false;
    echo $nav['html'];
    echo "<table>\n";
    echo '<tr><th>'.sort_link('id', 'ID', $sort_column, $sort_dir).'</th>';
    echo '<th>'.sort_link('name', 'Name', $sort_column, $sort_dir).'</th>';
    echo '<th>Attack Description</th><th>Damage</th>';
    echo '<th>'.sort_link('attackTypeName', 'Attack Type', $sort_column, $sort_dir).'</th>';
    echo '<th>Delay</th><th>Range</th><th>Outcome</th>';
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
        echo '<td>'.htmlentities($row['attack_description']).'</td>';
        echo '<td>'.htmlentities($row['damage']).'</td>';
        if (checkaccess('natres', 'edit'))
        {
            echo '<td><a href="./index.php?do=editattacktypes&amp;id='.$row['attackType'].'">'.htmlentities($row['attackTypeName']).'</a></td>';
        }
        else
        {
            echo '<td>'.htmlentities($row['attackTypeName']).'</td>';
        }
        echo '<td>'.htmlentities($row['delay']).'</td>';
        echo '<td>'.htmlentities($row['range']).'</td>';
        echo '<td>'.htmlentities($row['outcome']).'</td>';
        if (checkaccess('natres', 'edit'))
        {
            echo '<td><a href="./index.php?do=editattacks&amp;id='.$row['id'].'">Edit</a>';
            echo '<a href="./index.php?do=editattacks&amp;id='.$row['id'].'&amp;action=delete"> -- Delete</a></td>';
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
    // show "Create" table for all allowed to edit this.
    if (checkaccess('natres', 'edit'))
    {
        $attacktypes = PrepSelect('attacktypes');
        $mathscripts = PrepSelect('math_script');
        $scripts = PrepSelect('scripts');
        echo '<p class="header">Create Attack Type</p>';
        echo '<form action="./index.php?do=editattacks" method="post">';
        echo "<table>\n";
        echo "<tr><td>Field</td><td>Value</td></tr>\n";
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>'."\n";
        echo '<tr><td>Image Name</td><td><input type="text" name="image_name" /></td></tr>'."\n";
        echo '<tr><td>Attack Animation</td><td><input type="text" name="attack_anim" /></td></tr>'."\n";
        echo '<tr><td>Attack Description</td><td><textarea name="attack_description" rows="4" cols="60"></textarea></td></tr>'."\n";
        echo '<tr><td>Damage</td><td>'.DrawSelectBox('math_script', $mathscripts, 'damage', '').'</td></tr>'."\n";
        echo '<tr><td>Attack Type</td><td>'.DrawSelectBox('attacktypes', $attacktypes, 'attackType', '', true).'</td></tr>'."\n";
        echo '<tr><td>Outcome</td><td>'.DrawSelectBox('scripts', $scripts, 'outcome', '', true).'</td></tr>'."\n";
        echo '<tr><td>Delay</td><td><textarea name="delay" rows="4" cols="60"></textarea></td></tr>'."\n";
        echo '<tr><td>Range</td><td><textarea name="range" rows="4" cols="60"></textarea></td></tr>'."\n";
        echo '<tr><td>AOE Radius</td><td><textarea name="aoe_radius" rows="4" cols="60"></textarea></td></tr>'."\n";
        echo '<tr><td>AOE Angle Description</td><td><textarea name="aoe_angle" rows="4" cols="60"></textarea></td></tr>'."\n";
        echo '<tr><td>Requirements</td><td><textarea name="requirements"  rows="4" cols="60"></textarea></td></tr>'."\n";
        echo '<tr><td></td><td><input type="submit" name="commit" value="Create Attack" /></td></tr>'."\n";
        echo "</table></form>\n";

    }
}

function editattacks()
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
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Create Attack')
    {
        $name = escapeSqlString($_POST['name']);
        $image_name = escapeSqlString($_POST['image_name']);
        $attack_anim = escapeSqlString($_POST['attack_anim']);
        $attack_description = escapeSqlString($_POST['attack_description']);
        $damage = escapeSqlString($_POST['damage']);
        $attackType = escapeSqlString($_POST['attackType']);
        $delay = escapeSqlString($_POST['delay']);
        $range = escapeSqlString($_POST['range']);
        $aoe_radius = escapeSqlString($_POST['aoe_radius']);
        $aoe_angle = escapeSqlString($_POST['aoe_angle']);
        $outcome = escapeSqlString($_POST['outcome']);
        $requirements = escapeSqlString($_POST['requirements']);
        $query = "INSERT INTO attacks (name, image_name, attack_anim, attack_description, damage, attackType, delay, `range`, aoe_radius, aoe_angle, outcome, requirements) VALUES ('$name', '$image_name', '$attack_anim', '$attack_description', '$damage', '$attackType', '$delay', '$range', '$aoe_radius', '$aoe_angle', '$outcome', '$requirements')";
        $result = mysql_query2($query);
        echo '<p class="error">Creation Successful</p>';
        unset($_POST);
        listattacks();
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
    if (isset($_POST['commit']) && ($_POST['commit'] == "Update Attack"))
    {
        $name = escapeSqlString($_POST['name']);
        $image_name = escapeSqlString($_POST['image_name']);
        $attack_anim = escapeSqlString($_POST['attack_anim']);
        $attack_description = escapeSqlString($_POST['attack_description']);
        $damage = escapeSqlString($_POST['damage']);
        $attackType = escapeSqlString($_POST['attackType']);
        $delay = escapeSqlString($_POST['delay']);
        $range = escapeSqlString($_POST['range']);
        $aoe_radius = escapeSqlString($_POST['aoe_radius']);
        $aoe_angle = escapeSqlString($_POST['aoe_angle']);
        $outcome = escapeSqlString($_POST['outcome']);
        $requirements = escapeSqlString($_POST['requirements']);
        $query = "UPDATE attacks SET name='$name', image_name='$image_name', attack_anim='$attack_anim', attack_description='$attack_description', damage='$damage', attackType='$attackType', delay='$delay', `range`='$range', aoe_radius='$aoe_radius', aoe_angle='$aoe_angle', outcome='$outcome', requirements='$requirements' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        editattacks();
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        echo '<p class="error">You are about to delete Attack id "'.$id.'" </p>';
        echo '<form action="./index.php?do=editattacks&amp;id='.$id.'" method="post">';
        echo '<div><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $query = "DELETE FROM attacks WHERE id='$id'";
        mysql_query2($query);
        echo '<p class="error">Delete succesfull</p>';
        unset($_POST);
        listattacks();
    }
    else
    {
        $query = "SELECT * FROM attacks WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        $attacktypes = PrepSelect('attacktypes');
        $mathscripts = PrepSelect('math_script');
        $scripts = PrepSelect('scripts');
        echo '<p class="header">Edit Attack Type</p>';
        echo '<form action="./index.php?do=editattacks&amp;id='.$id.'" method="post">';
        echo "<table>\n";
        echo "<tr><td>Field</td><td>Value</td></tr>\n";
        echo '<tr><td>ID</td><td>'.$row['id']."</td></tr>\n";
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.htmlentities($row['name']).'" /></td></tr>'."\n";
        echo '<tr><td>Image Name</td><td><input type="text" name="image_name" value="'.htmlentities($row['image_name']).'" /></td></tr>'."\n";
        echo '<tr><td>Attack Animation</td><td><input type="text" name="attack_anim" value="'.htmlentities($row['attack_anim']).'" /></td></tr>'."\n";
        echo '<tr><td>Attack Description</td><td><textarea name="attack_description" rows="4" cols="60">'.htmlentities($row['attack_description']).'</textarea></td></tr>'."\n";
        echo '<tr><td>Damage</td><td>'.DrawSelectBox('math_script', $mathscripts, 'damage', $row['damage']).'</td></tr>'."\n";
        echo '<tr><td>Attack Type</td><td>'.DrawSelectBox('attacktypes', $attacktypes, 'attackType', $row['attackType'], true).'</td></tr>'."\n";
        echo '<tr><td>Outcome</td><td>'.DrawSelectBox('scripts', $scripts, 'outcome', $row['outcome'], true).'</td></tr>'."\n";
        echo '<tr><td>Delay</td><td><textarea name="delay" rows="4" cols="60">'.htmlentities($row['delay']).'</textarea></td></tr>'."\n";
        echo '<tr><td>Range</td><td><textarea name="range" rows="4" cols="60">'.htmlentities($row['range']).'</textarea></td></tr>'."\n";
        echo '<tr><td>AOE Radius</td><td><textarea name="aoe_radius" rows="4" cols="60">'.htmlentities($row['aoe_radius']).'</textarea></td></tr>'."\n";
        echo '<tr><td>AOE Angle Description</td><td><textarea name="aoe_angle" rows="4" cols="60">'.htmlentities($row['aoe_angle']).'</textarea></td></tr>'."\n";
        
        echo '<tr><td>Requirements</td><td><textarea name="requirements"  rows="4" cols="60">'.htmlentities($row['requirements']).'</textarea></td></tr>'."\n";
        echo '<tr><td></td><td><input type="submit" name="commit" value="Update Attack"/></td></tr>'."\n";
        echo "</table></form>\n";
    }
}

function sort_link($column, $label, $sort_col, $sort_dir)
{
    $html = '<a href="./index.php?do=listattacks&amp;page='.(isset($_GET['page']) ? $_GET['page'] : '').'&amp;items_per_page='.(isset($_GET['items_per_page']) ? $_GET['items_per_page'] : '').'&amp;sort_column='.$column;
    if($sort_col == $column && $sort_dir == 'ASC')
    {
        $html .= '&amp;sort_dir=DESC';
    }
    else
    {
        $html .= '&amp;sort_dir=ASC';
    }
    $html .= '">'.$label;
    if($sort_col == $column)
    {
        $html .= '<img src="img/s_'.strtolower($sort_dir).'.png" alt="sort direction" />';
    }
    $html .= '</a>';
    return $html;
}

?>