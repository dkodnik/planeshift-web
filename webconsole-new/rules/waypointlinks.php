<?php

function listwaypointlinks() 
{
    if (checkaccess('rules', 'read'))
    {
        $query = "SELECT DISTINCT wl.id, wl.name, wl.type, wl.wp1, wl.wp2, wl.flags, CONCAT(' X: ', w.x, ' Y: ', w.y, ' Z: ', w.z) AS wp1_coords, CONCAT(' X: ', ww.x, ' Y: ', ww.y, ' Z: ', ww.z) AS wp2_coords, w.name AS wp1_name, ww.name AS wp2_name, !ISNULL(pp.id) AS has_path FROM sc_waypoint_links AS wl LEFT JOIN sc_waypoints AS w ON wl.wp1=w.id LEFT JOIN sc_waypoints AS ww ON wl.wp2=ww.id LEFT JOIN sc_path_points AS pp ON pp.path_id=wl.id";

        if (isset($_GET['sector']) && $_GET['sector'] != '' && $_GET['sector'] != '0')
        {
            $sec = mysql_real_escape_string($_GET['sector']);
            $query = $query . " WHERE w.loc_sector_id='$sec'";
        }
        
        if (isset($_GET['sort']))
        {
            switch($_GET['sort'])
            {
                case 'id':
                    $query .= ' ORDER BY wl.id';
                    break;
                case 'name':
                    $query .= ' ORDER BY wl.name, w.name';
                    break;
                case 'wp1':
                    $query .= ' ORDER BY w.name, wl.name';
                    break;
                case 'wp2':
                    $query .= ' ORDER BY ww.name, wl.name';
                    break;
            }
        }
        else
        {
            $query .= ' ORDER BY wl.name, w.name';
        }
        if (isset($_GET['limit']) && is_numeric($_GET['limit']))
        {
            $start = $_GET['limit'] - 30;
            $limit = $_GET['limit']; 
            $query = $query . " LIMIT $start, 30"; // mysql usage: limit 1, 10  starts at 1, lasts 10 records, so we want this hardcoded as 30, since we don't allow step sizes here yet.
        }
        else
        {
            $query = $query . ' LIMIT 30';
            $limit = 30;
        }
        $result = mysql_query2($query);
        if (mysql_numrows($result) == 0){
            echo '<p class="error">No Waypoint Links</p>';
        }
        else
        {
            $sid = 0;
            if (isset($_GET['sector']))
            {
                $sid = $_GET['sector'];
            }
            $prev_lim = $limit - 30;
            if ($limit > 30)
            {
                echo '<a href="./index.php?do=listwaypointlinks';
                if (isset($_GET['sort']))
                {
                    echo '&amp;sort='.$_GET['sort'];
                }
                echo '&amp;limit='.$prev_lim.'&amp;sector='.$sid.'">Previous Page</a> ';
            }
            echo ' - Displaying records '.$prev_lim.' through '.$limit.' - ';
            $where = ($sid == 0 ? '' : " LEFT JOIN sc_waypoints AS w ON wl.wp1=w.id WHERE w.loc_sector_id=$sid");
            $result2 = mysql_query2('select count(wl.id) AS mylimit FROM sc_waypoint_links AS wl'.$where);
            $row2 = mysql_fetch_array($result2);
            if ($row2['mylimit'] > $limit)
            {
                echo '<a href="./index.php?do=listwaypointlinks';
                if (isset($_GET['sort']))
                {
                    echo '&amp;sort='.$_GET['sort'];
                }
                $next_lim = $limit + 30;
                echo '&amp;limit='.$next_lim.'&amp;sector='.$sid.'">Next Page</a>';
            }
            
            $sectors = PrepSelect('sectorid');
            echo '<form action="./index.php" method="get"><input type="hidden" name="do" value="listwaypointlinks"/>';
            if (isset($_GET['sort']))
            {
                echo '<input type="hidden" name="sort" value="'.$_GET['sort'].'"/>';
            }
            if (isset($_GET['limit']))
            {
                echo '<input type="hidden" name="limit" value="'.$_GET['limit'].'"/>';
            }
            echo DrawSelectBox('sectorid', $sectors, 'sector' ,$sid, true);
            echo '<input type="submit" name="submit" value="Limit By Sector" /></form>';
            
            echo '<table border="1">';
            echo '<tr><th><a href="./index.php?do=listwaypointlinks&amp;sort=id&limit='.$limit.'&sector='.$sid.'">ID</a></th>';
            echo '<th><a href="./index.php?do=listwaypointlinks&amp;sort=name&limit='.$limit.'&sector='.$sid.'">Name</a></th>';
            echo '<th>Type</th>';
            echo '<th><a href="./index.php?do=listwaypointlinks&amp;sort=wp1&limit='.$limit.'&sector='.$sid.'">wp1</a></th>';
            echo '<th><a href="./index.php?do=listwaypointlinks&amp;sort=wp2&limit='.$limit.'&sector='.$sid.'">wp2</a></th>';
            echo '<th>Flags</th>';
            if (checkaccess('rules','edit'))
            {
                echo '<th>Actions</th>';
            }
            echo '</tr>';
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                echo '<tr>';
                echo '<td>'.$row['id'].'</td>';
                echo '<td>'.$row['name'].'</td>';
                echo '<td>'.$row['type'].'</td>';
                echo '<td>'.$row['wp1_name'].'<br />'.$row['wp1_coords'].'</td>';
                echo '<td>'.$row['wp2_name'].'<br />'.$row['wp2_coords'].'</td>';
                echo '<td>'.$row['flags'].'</td>';
                if (checkaccess('rules', 'edit'))
                {
                    echo '<td><a href="./index.php?do=editwaypointlink&id='.$row['id'].'">Edit</a>';
                    if (checkaccess('rules', 'delete'))
                    {
                        echo '<br/><a href="./index.php?do=deletewaypointlink&id='.$row['id'].'">Delete</a>';
                    }
                    if (checkaccess('rules', 'create') && !$row['has_path'])
                    {
                        echo '<br/><a href="./index.php?do=createpathpoint&path_id='.$row['id'].'">Create Path</a>';
                    }
                    else if ($row['has_path'])
                    {
                        echo '<br/><a href="./index.php?do=editpathpoint&path_id='.$row['id'].'">Edit Path</a>';
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '<hr/>';
        echo '<form action="./index.php?do=createwaypointlink" method="post">';
        echo '<table border="1">';
        echo '<tr><th colspan="2">Create waypoint link</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>';
        echo '<tr><td>Type</td><td><input type="text" name="type" /></td></tr>';
        $waypoints = PrepSelect('waypoints');
        echo '<tr><td>Waypoint 1</td><td>'.DrawSelectBox('waypoints', $waypoints, 'wp1', '', false).'</td></tr>';
        echo '<tr><td>Waypoint 2</td><td>'.DrawSelectBox('waypoints', $waypoints, 'wp2', '', false).'</td></tr>';
        echo '<tr><td>Flags</td><td>';
        $flags = ' '.$row['flags'];
       
        echo '<input type="checkbox" name="flags[]" value="ONEWAY" /> ONEWAY<br/>';
        echo '<input type="checkbox" name="flags[]" value="NO_WANDER" /> NO_WANDER<br/>';
        echo '</td></tr>';
        echo '</table>';
        echo '<input type="submit" name="commit" value="Create Waypoint Link" />';
        echo '</form>';

    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function editwaypointlink()
{
    if (checkaccess('rules', 'edit') && isset($_POST['commit']))
    {
        if (isset($_POST['id']) && is_numeric($_POST['id']))
        {
            $id = mysql_real_escape_string($_POST['id']);
        }
        else
        {
            echo '<p class="error">Invalid ID</p>';
            return;
        }
    
        $name = mysql_real_escape_string($_POST['name']);
        $type = mysql_real_escape_string($_POST['type']);
        $wp1 = mysql_real_escape_string($_POST['wp1']);
        $wp2 = mysql_real_escape_string($_POST['wp2']);
        $flags = '';
        if (isset($_POST['flags']))
            {
            foreach ($_POST['flags'] AS $key => $value)
            {
                $flags .= $value . ', ';
            }
            if (strlen($flags) > 0){
                $flags = substr($flags, 0, -2);
            }
        }
        $flags = mysql_real_escape_string($flags);
        if ($wp1 == $wp2)
        {
            echo '<p class="error">Waypoint 1 may not equal waypoint 2. Update canceled.</p>';
            return;
        }
        $query = "UPDATE sc_waypoint_links SET name='$name', type='$type', wp1='$wp1', wp2='$wp2', flags='$flags' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listwaypointlinks();
    }
    elseif (checkaccess('rules', 'edit'))
    {
        if (isset($_GET['id']) && is_numeric($_GET['id']))
        {
            $id = mysql_real_escape_string($_GET['id']);
        }
        else
        {
            echo '<p class="error">Invalid ID</p>';
            return;
        }
        $query = "SELECT * FROM sc_waypoint_links WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=editwaypointlink" method="post"><input type="hidden" name="id" value="'.$id.'" />';
        echo '<table border="1">';
        echo '<tr><th colspan="2">Edit waypoint link</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.$row['name'].'" /></td></tr>';
        echo '<tr><td>Type</td><td><input type="text" name="type" value="'.$row['type'].'"/></td></tr>';
        $waypoints = PrepSelect('waypoints');
        echo '<tr><td>Waypoint 1</td><td>'.DrawSelectBox('waypoints', $waypoints, 'wp1', $row['wp1'], false).'</td></tr>';
        echo '<tr><td>Waypoint 2</td><td>'.DrawSelectBox('waypoints', $waypoints, 'wp2', $row['wp2'], false).'</td></tr>';
        echo '<tr><td>Flags</td><td>';
        $flags = ' '.$row['flags'];
       
        echo '<input type="checkbox" name="flags[]" value="ONEWAY" '.(strpos($flags, 'ONEWAY') !== false ? 'checked="true"' : '').' /> ONEWAY<br/>';
        echo '<input type="checkbox" name="flags[]" value="NO_WANDER" '.(strpos($flags, 'NO_WANDER') !== false ? 'checked="true"' : '').' /> NO_WANDER<br/>';
        echo '</td></tr>';
        echo '</table>';
        echo '<input type="submit" name="commit" value="Update Waypoint Link" />';
        echo '</form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function createwaypointlink()
{
    if (checkaccess('rules', 'create') && isset($_POST['commit']) && $_POST['commit'] == 'Create Waypoint Link')
    {
        $name = mysql_real_escape_string($_POST['name']);
        $type = mysql_real_escape_string($_POST['type']);
        $wp1 = mysql_real_escape_string($_POST['wp1']);
        $wp2 = mysql_real_escape_string($_POST['wp2']);
        $flags = '';
        if (isset($_POST['flags']))
            {
            foreach ($_POST['flags'] AS $key => $value)
            {
                $flags .= $value . ', ';
            }
            if (strlen($flags) > 0){
                $flags = substr($flags, 0, -2);
            }
        }
        $flags = mysql_real_escape_string($flags);
        if ($wp1 == $wp2)
        {
            echo '<p class="error">Waypoint 1 may not equal waypoint 2. Creation canceled.</p>';
            return;
        }
        $query = "INSERT INTO sc_waypoint_links ( name, type, wp1, wp2, flags ) VALUES ( '$name', '$type', '$wp1', '$wp2', '$flags' )";
        $result = mysql_query2($query);
        echo '<p class="error">Creation Successful</p>';
        unset($_POST);
        listwaypointlinks();
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function deletewaypointlink()
{
    if (checkaccess('rules', 'delete') && isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        if (isset($_POST['id']) && is_numeric($_POST['id']))
        {
            $id = mysql_real_escape_string($_POST['id']);
        }
        else
        {
            echo '<p class="error">Invalid ID</p>';
            return;
        }
        $query = "DELETE FROM sc_waypoint_links WHERE id='$id' LIMIT 1";
        $result = mysql_query2($query);
        $query = "DELETE FROM sc_path_points WHERE path_id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Delete Successful</p>';
        unset($_POST);
        listwaypointlinks();
    }
    elseif (checkaccess('rules', 'delete'))   
    {
        if (isset($_GET['id']) && is_numeric($_GET['id']))
        {
            $id = mysql_real_escape_string($_GET['id']);
        }
        else
        {
            echo '<p class="error">Invalid ID</p>';
            return;
        }
        $query = "SELECT id FROM sc_path_points WHERE path_id='$id'";
        $result = mysql_query2($query);
        $path_point_delete = '';
        if (mysql_num_rows($result) > 0)
        {
            $path_point_delete = ' AND the path associated with it';
        }
        $query = "SELECT w.name AS wp1, ww.name AS wp2 FROM sc_waypoint_links AS wl LEFT JOIN sc_waypoints AS w ON wl.wp1=w.id LEFT JOIN sc_waypoints AS ww ON wl.wp2=ww.id WHERE wl.id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result);
        echo 'You are about to delete the waypoint link between '.$row['wp1'].' and '.$row['wp2'].$path_point_delete.' - Please confirm you wish to do this<br/>';
        echo '<form action="./index.php?do=deletewaypointlink" method="post">';
        echo '<input type="hidden" name="id" value="'.$id.'"/><input type="submit" name="commit" value="Confirm Delete" /></form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}
    
?>
