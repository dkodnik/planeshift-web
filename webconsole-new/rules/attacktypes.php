<?php

function listattacktypes()
{
    if (!checkaccess('natres', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    // we need this to translate the weapon_id's later.
    $query = 'SELECT id, name FROM weapon_types';
    $result = mysql_query2($query);
    $weapontypes = array();
    while($row = fetchSqlAssoc($result))
    {
        $weapontypes[$row['id']] = $row['name'];
    }
    // navigation
    $sql = 'SELECT COUNT(*) FROM attack_types';
    $item_count = fetchSqlRow(mysql_query2($sql));
    $sort_column = (isset($_GET['sort_column']) && !empty($_GET['sort_column']) ? escapeSqlString($_GET['sort_column']) : 'id');
    $sort_dir = (isset($_GET['sort_dir']) && $_GET['sort_dir'] == 'DESC' ? 'DESC' : 'ASC');
    $nav = RenderNav(array('do' => 'listattacktypes', 'sort_column' => $sort_column, 'sort_dir' => $sort_dir), $item_count[0]);
    // actual query
    $query = 'SELECT a.id, a.name, i.name AS weaponName, a.weaponType, a.onehand, s.name AS stat FROM attack_types AS a LEFT JOIN skills AS s ON s.skill_id=a.stat LEFT JOIN item_stats AS i ON i.id=a.weaponID';
    $query .= ' ORDER BY '.$sort_column.' '.$sort_dir;
    $query .= $nav['sql'];
    $result = mysql_query2($query);
    $alt = false;
    echo $nav['html'];
    echo "<table>\n";
    echo '<tr><th>'.sort_link('id', 'ID', $sort_column, $sort_dir).'</th>';
    echo '<th>'.sort_link('name', 'Name', $sort_column, $sort_dir).'</th><th>Weapon Name</th><th>Weapon Type</th>';
    echo '<th>'.sort_link('onehand', 'Onehand', $sort_column, $sort_dir).'</th>';
    echo '<th>'.sort_link('stat', 'Stat', $sort_column, $sort_dir).'</th>';
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
        echo '<td>'.htmlentities($row['weaponName']).'</td>';
        echo '<td>';
        if ($row['weaponType'] !== null)
        {
            foreach (explode(' ', $row['weaponType']) as $weaponid)
            {
                echo htmlentities($weapontypes[$weaponid]).' ';
            }
        }
        echo '</td>';
        echo '<td>'.($row['onehand'] === '1' ? 'Yes' : 'No').'</td>';
        echo '<td>'.htmlentities($row['stat']).'</td>';
        if (checkaccess('natres', 'edit'))
        {
            echo '<td><a href="./index.php?do=editattacktypes&amp;id='.$row['id'].'">Edit</a>';
            echo '<a href="./index.php?do=editattacktypes&amp;id='.$row['id'].'&amp;action=delete"> -- Delete</a></td>';
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
    // show "Create" table for all allowed to edit this.
    if (checkaccess('natres', 'edit'))
    {
        $wtype_query = 'SELECT id, name FROM weapon_types';
        $wtype_result = mysql_query2($wtype_query);
        $weapontypes = explode(' ', $row['weaponType']);
        $skills = PrepSelect('skill');
        echo '<p class="header">Create Attack Type</p>';
        echo '<form action="./index.php?do=editattacktypes" method="post">';
        echo "<table>\n";
        echo "<tr><td>Field</td><td>Value</td></tr>\n";
        echo "<tr><td>ID</td><td></td></tr>\n";
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>'."\n";
        echo '<tr><td>WeaponID</td><td>'.DrawItemSelectBox('weaponID', '', true, true).'</td></tr>'."\n";
        echo '<tr><td>WeaponType</td><td>';
        while ($wtype_row = fetchSqlAssoc($wtype_result))
        {
            echo '<input type="checkbox" name="weaponType[]" value="'.$wtype_row['id'].'" />'.$wtype_row['name'].'<br />'."\n";
        }
        echo '</td></tr>'."\n";
        echo '<tr><td>Onehand</td><td><input type="checkbox" name="onehand" value="onehand" /> Check if the attack type is one handed</td></tr>'."\n";
        echo '<tr><td>Stat</td><td>'.DrawSelectBox('skill', $skills, 'stat', '').'</td></tr>'."\n";
        echo '<tr><td></td><td><input type="submit" name="commit" value="Create Attack Type"/></td></tr>'."\n";
        echo "</table></form>\n";
    }
}

function editattacktypes()
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
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Create Attack Type')
    {
        $name = escapeSqlString($_POST['name']);
        $weaponID = ($_POST['weaponID'] === '' ? '0' : escapeSqlString($_POST['weaponID']));
        $weaponType = '';
        if (!isset($_POST['weaponType']))
        {
            $weaponType = 'NULL';
        }
        else
        {
            foreach ($_POST['weaponType'] as $wtype)
            {
                $weaponType .= (is_numeric($wtype) ? escapeSqlString($wtype).' ' : '');
            }
            $weaponType = trim($weaponType); // remove the last space.
        }
        $onehand = (isset($_POST['onehand']) ? '1' : '0');
        $stat = escapeSqlString($_POST['stat']);
        // sanity check on the data
        if (($weaponType == 'NULL' && $weaponID == '0') || ($weaponType != 'NULL' && $weaponID != '0'))
        {
            echo '<p class="error">You must select EITHER a weaponType OR a weaponID (exclusive OR).</p>';
            return;
        }
        $query = "INSERT INTO attack_types (name, weaponID, weaponType, onehand, stat) VALUES ('$name', '$weaponID', ".($weaponType == 'NULL' ? 'NULL' : "'$weaponType'").", '$onehand', '$stat')";
        $result = mysql_query2($query);
        echo '<p class="error">Creation Successful</p>';
        unset($_POST);
        listattacktypes();
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
    if (isset($_POST['commit']) && ($_POST['commit'] == "Update Attack Type"))
    {
        $name = escapeSqlString($_POST['name']);
        $weaponID = ($_POST['weaponID'] === '' ? '0' : escapeSqlString($_POST['weaponID']));
        $weaponType = '';
        if (!isset($_POST['weaponType']))
        {
            $weaponType = 'NULL';
        }
        else
        {
            foreach ($_POST['weaponType'] as $wtype)
            {
                $weaponType .= (is_numeric($wtype) ? escapeSqlString($wtype).' ' : '');
            }
            $weaponType = trim($weaponType); // remove the last space.
        }
        $onehand = (isset($_POST['onehand']) ? '1' : '0');
        $stat = escapeSqlString($_POST['stat']);
        // sanity check on the data
        if (($weaponType == 'NULL' && $weaponID == '0') || ($weaponType != 'NULL' && $weaponID != '0'))
        {
            echo '<p class="error">You must select EITHER a weaponType OR a weaponID (exclusive OR).</p>';
            return;
        }
        $query = "UPDATE attack_types SET name='$name', weaponID='$weaponID', weaponType=".($weaponType == 'NULL' ? 'NULL' : "'$weaponType'").", onehand='$onehand', stat='$stat' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        editattacktypes();
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'delete')
    {
        // confirm delete
        echo '<p class="error">You are about to delete Attack Type id "'.$id.'" </p>';
        echo '<form action="./index.php?do=editattacktypes&amp;id='.$id.'" method="post">';
        echo '<div><input type="submit" name="commit" value="Confirm Delete" /></div>';
        echo '</form>';
    }
    elseif (isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        $query = "DELETE FROM attack_types WHERE id='$id'";
        mysql_query2($query);
        echo '<p class="error">Delete succesfull</p>';
        unset($_POST);
        listattacktypes();
    }
    else
    {
        $query = "SELECT * FROM attack_types WHERE id='$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        $wtype_query = 'SELECT id, name FROM weapon_types';
        $wtype_result = mysql_query2($wtype_query);
        $weapontypes = explode(' ', $row['weaponType']);
        $skills = PrepSelect('skill');
        echo '<p class="header">Edit Attack Type</p>';
        echo '<form action="./index.php?do=editattacktypes&amp;id='.$id.'" method="post">';
        echo "<table>\n";
        echo "<tr><td>Field</td><td>Value</td></tr>\n";
        echo '<tr><td>ID</td><td>'.$row['id']."</td></tr>\n";
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.$row['name'].'" /></td></tr>'."\n";
        echo '<tr><td>WeaponID</td><td>'.DrawItemSelectBox('weaponID', $row['weaponID'], true, true).'</td></tr>'."\n";
        echo '<tr><td>WeaponType</td><td>';
        while ($wtype_row = fetchSqlAssoc($wtype_result))
        {
            echo '<input type="checkbox" name="weaponType[]" value="'.$wtype_row['id'].'"'.(in_array($wtype_row['id'], $weapontypes, true) ? ' checked="checked"' : '').' />'.$wtype_row['name'].'<br />'."\n";
        }
        echo '</td></tr>'."\n";
        echo '<tr><td>Onehand</td><td><input type="checkbox" name="onehand" value="onehand"'.($row['onehand'] === '1' ? ' checked="checked"' : '').' /> Check if the attack type is one handed</td></tr>'."\n";
        echo '<tr><td>Stat</td><td>'.DrawSelectBox('skill', $skills, 'stat', $row['stat']).'</td></tr>'."\n";
        echo '<tr><td></td><td><input type="submit" name="commit" value="Update Attack Type"/></td></tr>'."\n";
        echo "</table></form>\n";
    }
}

function sort_link($column, $label, $sort_col, $sort_dir)
{
    $html = '<a href="./index.php?do=listattacktypes&amp;page='.(isset($_GET['page']) ? $_GET['page'] : '').'&amp;items_per_page='.(isset($_GET['items_per_page']) ? $_GET['items_per_page'] : '').'&amp;sort_column='.$column;
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