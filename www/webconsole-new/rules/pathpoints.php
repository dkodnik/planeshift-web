<?php
function listpathpoints() 
{
    if (checkaccess('rules', 'read'))
    {
        $query = "SELECT DISTINCT p.path_id, s.name AS sector, wl.wp1, wl.wp2, CONCAT(' X: ', w.x, ' Y: ', w.y, ' Z: ', w.z) AS wp1_coords, CONCAT(' X: ', ww.x, ' Y: ', ww.y, ' Z: ', ww.z) AS wp2_coords, w.name AS wp1_name, ww.name AS wp2_name FROM sc_path_points AS p LEFT JOIN sc_waypoint_links AS wl ON p.path_id=wl.id LEFT JOIN sc_waypoints AS w ON wl.wp1=w.id LEFT JOIN sc_waypoints AS ww ON wl.wp2=ww.id LEFT JOIN sectors AS s ON w.loc_sector_id=s.id";

        if (isset($_GET['sector']) && $_GET['sector'] != '' && $_GET['sector'] != '0')
        {
            $sec = mysql_real_escape_string($_GET['sector']);
            $query = $query . " WHERE w.loc_sector_id='$sec'";
        }
      
        if (isset($_GET['sort']))
        {
            switch($_GET['sort'])
            {
                case 'path_id':
                    $query .= ' ORDER BY p.path_id';
                    break;
                case 'sector':
                    $query .= ' ORDER BY s.name, w.name';
                    break;
                case 'wp1':
                    $query .= ' ORDER BY w.name, ww.name';
                    break;
                case 'wp2':
                    $query .= ' ORDER BY ww.name, w.name';
                    break;
            }
        }
        else
        {
            $query .= ' ORDER BY p.path_id';
        }
        if (isset($_GET['limit']) && is_numeric($_GET['limit']))
        {
            $start = $_GET['limit'] - 30;
            $limit = $_GET['limit'];
            $query = $query . " LIMIT $start, 30"; // limit 1, 10 is offset 1, 30 records.
        }
        else
        {
            $query = $query . ' LIMIT 30';
            $limit = 30;
        }
        $result = mysql_query2($query);
        if (mysql_numrows($result) == 0){
            echo '<p class="error">No Paths Found</p>';
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
                echo '<a href="./index.php?do=listpathpoints';
                if (isset($_GET['sort']))
                {
                    echo '&amp;sort='.$_GET['sort'];
                }
                echo '&amp;limit='.$prev_lim.'&amp;sector='.$sid.'">Previous Page</a> ';
            }
            echo ' - Displaying records '.$prev_lim.' through '.$limit.' - ';
            $where = ($sid == 0 ? '' : " LEFT JOIN sc_waypoint_links AS wl ON p.path_id=wl.id LEFT JOIN sc_waypoints AS w ON wl.wp1=w.id WHERE w.loc_sector_id=$sid");
            $result2 = mysql_query2('select count(p.id) AS mylimit FROM sc_path_points AS p'.$where);
            $row2 = mysql_fetch_array($result2);
            if ($row2['mylimit'] > $limit)
            {
                echo '<a href="./index.php?do=listpathpoints';
                if (isset($_GET['sort']))
                {
                    echo '&amp;sort='.$_GET['sort'];
                }
                $next_lim = $limit + 30;
                echo '&amp;limit='.$next_lim.'&amp;sector='.$sid.'">Next Page</a>';
            }
            $sectors = PrepSelect('sectorid');
            echo '<form action="./index.php" method="get"><input type="hidden" name="do" value="listpathpoints"/>';
            $sid = 0;
            if (isset($_GET['sector']))
            {
                $sid = $_GET['sector'];
            }
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
            echo '<tr><th><a href="./index.php?do=listpathpoints&amp;sort=path_id&limit='.$limit.'&sector='.$sid.'">Path ID</a></th>';
            echo '<th><a href="./index.php?do=listpathpoints&amp;sort=sector&limit='.$limit.'&sector='.$sid.'">Starting Sector</a></th>';
            echo '<th><a href="./index.php?do=listpathpoints&amp;sort=wp1&limit='.$limit.'&sector='.$sid.'">wp1</a></th>';
            echo '<th><a href="./index.php?do=listpathpoints&amp;sort=wp2&limit='.$limit.'&sector='.$sid.'">wp2</a></th>';
            if (checkaccess('rules','edit'))
            {
                echo '<th>Actions</th>';
            }
            echo '</tr>';
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
            {
                echo '<tr>';
                echo '<td>'.$row['path_id'].'</td>';
                echo '<td>'.$row['sector'].'</td>';
                echo '<td>'.$row['wp1_name'].'<br />'.$row['wp1_coords'].'</td>';
                echo '<td>'.$row['wp2_name'].'<br />'.$row['wp2_coords'].'</td>';
                if (checkaccess('rules', 'edit'))
                {
                    echo '<td><a href="./index.php?do=editpathpoint&path_id='.$row['path_id'].'">Edit</a>';
                    if (checkaccess('rules', 'delete'))
                    {
                        echo '<br/><a href="./index.php?do=deletepathpoint&path_id='.$row['path_id'].'">Delete</a>';
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '<hr/>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function editpathpoint() 
{
    if (checkaccess('rules', 'edit') && isset($_POST['commit']) && $_POST['commit'] == 'Save Changes')
    {
        if (isset($_POST['path_id']) && is_numeric($_POST['path_id']))  // the form sends both post and get for this value, we use post because it's less likely to be user influenced. (accidentally)
        {
            $path_id = mysql_real_escape_string($_POST['path_id']);
        }
        else
        {
            echo '<p class="error">Invalid Path ID</p>';
            return;
        }
        $prev_id = 0;
        for($i = 0; count($_POST['id']) > $i; $i++)
        {
            $id = mysql_real_escape_string($_POST['id'][$i]);
            $x = mysql_real_escape_string($_POST['x'][$i]);
            $y = mysql_real_escape_string($_POST['y'][$i]);
            $z = mysql_real_escape_string($_POST['z'][$i]);
            $loc_sector_id = mysql_real_escape_string($_POST['loc_sector_id'][$i]);
            if ($id != -1)
            {
                if ($x != '' && $y != '' && $z != '' && $loc_sector_id != '')  // we don't process any lines that are not completely filled out.
                {
                    $query = "UPDATE sc_path_points SET prev_point='$prev_id', x='$x', y='$y', z='$z', loc_sector_id='$loc_sector_id' WHERE id='$id'";
                    mysql_query2($query);
                    $prev_id = $id;
                }
                else
                {
                    echo "<p class=\"error\"> Warning, ignored line with X: $x  Y: $y  Z: $z  sector_id: $loc_sector_id because one or more of the fields contained no data.</p>";
                }
            }
            else // if id = -1, this is a new entry, we insert the data, and ask for the last auto-generated ID (should be our query)
            {
                if ($x != '' && $y != '' && $z != '' && $loc_sector_id != '')  // we don't process any lines that are not completely filled out.
                {
                    $query = "INSERT INTO sc_path_points (path_id, prev_point, x, y, z, loc_sector_id) VALUES ('$path_id', '$prev_id', '$x', '$y', '$z', '$loc_sector_id')";
                    mysql_query2($query);
                    $query = "SELECT id FROM sc_path_points WHERE path_id='$path_id' AND prev_point='$prev_id' AND x='$x' AND y='$y' AND z='$z' AND loc_sector_id='$loc_sector_id'";
                    $result = mysql_query2($query);  // We assume here, that there are no identical entries. (there should not be)
                    $row = mysql_fetch_array($result, MYSQL_ASSOC); 
                    $prev_id = $row['id']; // We could also have used mysql_insert_id here, but that is not guaranteed to be correct if multiple users are inserting at the same time.
                }
                else
                {
                    echo "<p class=\"error\"> Warning, ignored line with X: $x  Y: $y  Z: $z  sector_id: $loc_sector_id because one or more of the fields contained no data.</p>";
                }
            }
        }
        echo '<p class="error">Data succesfully updated.</p>';
        unset($_POST);
        editpathpoint();
    }
    elseif (checkaccess('rules', 'edit'))   // This part handles insert/delete as well.
    {
        if (isset($_GET['path_id']) && is_numeric($_GET['path_id']))
        {
            $path_id = $_GET['path_id'];
        }
        else
        {
            echo '<p class="error">Invalid Path ID</p>';
            return;
        }
        $insert = -1; // set it to an initial value that will never be encountered, so it does nothing if it is unset.
        if (isset($_GET['insert']) && is_numeric($_GET['insert']))
        {
            $insert = $_GET['insert'];
        }
        if (isset($_GET['delete']) && is_numeric($_GET['delete']))
        {
            $delete_id = mysql_real_escape_string($_GET['delete']);
            $query = "SELECT prev_point FROM sc_path_points WHERE id='$delete_id'";
            $result = mysql_query2($query);
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            $prev_id = $row['prev_point'];
            $query = "DELETE FROM sc_path_points WHERE id='$delete_id' LIMIT 1";
            mysql_query2($query);
            $query = "UPDATE sc_path_points SET prev_point='$prev_id' WHERE prev_point='$delete_id' AND path_id='$path_id' LIMIT 1";
            mysql_query2($query); // We set the next entry to refer to the one before the deleted enty (so the chain remains unbroken).
            echo '<p class="error">Line successfully deleted.</p>';
        }
        $query = "SELECT pp.id, s.name AS sector_name, pp.prev_point, pp.x, pp.y, pp.z, pp.loc_sector_id FROM sc_path_points AS pp LEFT JOIN sectors AS s ON s.id=pp.loc_sector_id WHERE path_id='$path_id'";
        $result = mysql_query2($query);
        echo '<form action="./index.php?do=editpathpoint&path_id='.$path_id.'" method="post"><input type="hidden" name="path_id" value="'.$path_id.'" />';
        echo '<p class="header">Path Points listing for waypoint link: '.$path_id.'</p>';
        echo '<p>Please note that neither insert nor delete "remember" any other changes you make, so hit "Save Changes" first. (You can however edit multiple lines at once, and you do not need to save "delete" iteself.)</p>';
        echo '<table border="1">';
        echo '<tr><th>X</th><th>Y</th><th>Z</th><th>Sector</th><th>Actions</th></tr>';
        $prev = 0;
        $count_down = mysql_num_rows($result) - 1;
        $sectors = PrepSelect('sectorid');
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) // This slightly more complex loop serves to get the entries in proper order.
        {
            if ($prev == $insert)
            {
                echo '<tr>';
                echo '<td><input type="hidden" name="id[]" value="-1" /><input type="text" name="x[]" value="" /></td>';
                echo '<td><input type="text" name="y[]" value="" /></td>';
                echo '<td><input type="text" name="z[]" value="" /></td>';
                echo '<td>'.DrawSelectBox('sectorid', $sectors, 'loc_sector_id[]', '', true).'</td></tr>';
                $insert = -1; // set it to a value that will never be encountered.
            }
            if ($row['prev_point'] == $prev && ($prev == 0 || $count_down == 0)) // In these 2 cases we have the first and last point, they equal the position of the waypoint 1/2 in the waypoint link, and thus can not be changed.
            {
                echo '<tr>';
                echo '<td><input type="hidden" name="id[]" value="'.$row['id'].'" /><input type="hidden" name="x[]" value="'.$row['x'].'" />'.$row['x'].'</td>';
                echo '<td><input type="hidden" name="y[]" value="'.$row['y'].'" />'.$row['y'].'</td>';
                echo '<td><input type="hidden" name="z[]" value="'.$row['z'].'" />'.$row['z'].'</td>';
                echo '<td><input type="hidden" name="loc_sector_id[]" value="'.$row['loc_sector_id'].'" />'.$row['sector_name'].'</td>';
                if ($prev == 0)
                {
                    echo '<td><a href="./index.php?do=editpathpoint&path_id='.$path_id.'&insert='.$row['id'].'">Insert</a></td>';
                }
                echo '</tr>';
                $prev = $row['id'];
                mysql_data_seek($result, 0);
                $count_down--;
            }
            else if ($row['prev_point'] == $prev) {
                echo '<tr>';
                echo '<td><input type="hidden" name="id[]" value="'.$row['id'].'" /><input type="text" name="x[]" value="'.$row['x'].'" /></td>';
                echo '<td><input type="text" name="y[]" value="'.$row['y'].'" /></td>';
                echo '<td><input type="text" name="z[]" value="'.$row['z'].'" /></td>';
                echo '<td>'.DrawSelectBox('sectorid', $sectors, 'loc_sector_id[]', $row['loc_sector_id']).'</td>';
                echo '<td><a href="./index.php?do=editpathpoint&path_id='.$path_id.'&insert='.$row['id'].'">Insert</a><br><br><a href="./index.php?do=editpathpoint&path_id='.$path_id.'&delete='.$row['id'].'">Delete</a></td></tr>';
                $prev = $row['id'];
                mysql_data_seek($result, 0);
                $count_down--;
            } // No else, we want the loop to continue, so else { continue; } could be done, but just a waste of space. :)
        }
        echo '<tr><td colspan="5"><input type="submit" name="commit" value="Save Changes" /></td></tr>';
        echo '</table></form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function createpathpoint()
{
    if (checkaccess('rules', 'create') && isset($_POST['commit']) && $_POST['commit'] == 'Create Path')
    {
        if (isset($_POST['path_id']) && is_numeric($_POST['path_id']))
        {
            $path_id = mysql_real_escape_string($_POST['path_id']);
        }
        else
        {
            echo '<p class="error">Invalid Path ID</p>';
            return;
        }
        $prev_id = 0;
        for ($i = 0; $i < count($_POST['x']); $i++)
        {
            $x = mysql_real_escape_string($_POST['x'][$i]);
            $y = mysql_real_escape_string($_POST['y'][$i]);
            $z = mysql_real_escape_string($_POST['z'][$i]);
            $loc_sector_id = mysql_real_escape_string($_POST['loc_sector_id'][$i]);
            if ($x != '' && $y != '' && $z != '' && $loc_sector_id != '')  // we don't process any lines that are not completely filled out.
            {
                $query = "INSERT INTO sc_path_points (path_id, prev_point, x, y, z, loc_sector_id) VALUES ('$path_id', '$prev_id', '$x', '$y', '$z', '$loc_sector_id')";
                mysql_query2($query);
                $query = "SELECT id FROM sc_path_points WHERE path_id='$path_id' AND prev_point='$prev_id' AND x='$x' AND y='$y' AND z='$z' AND loc_sector_id='$loc_sector_id'";
                $result = mysql_query2($query);  // We assume here, that there are no identical entries. (there should not be)
                $row = mysql_fetch_array($result, MYSQL_ASSOC); 
                $prev_id = $row['id']; // We could also have used mysql_insert_id here, but that is not guaranteed to be correct if multiple users are inserting at the same time.
            }
            else
            {
                echo "<p class=\"error\"> Warning, ignored line with X: $x  Y: $y  Z: $z  sector_id: $loc_sector_id because one or more of the fields contained no data.</p>";
            }
        }
        echo '<p class="error">Path created succesfully.</p>';
        unset($_POST);
        listpathpoints();
    }
    elseif (checkaccess('rules','create') && isset($_POST['add'])) // The user wants more input fields.
    {
        if (isset($_POST['path_id']) && is_numeric($_POST['path_id']))
        {
            $path_id = mysql_real_escape_string($_POST['path_id']);
        }
        else
        {
            echo '<p class="error">Invalid Path ID</p>';
            return;
        }
        $sectors = PrepSelect('sectorid');
        echo '<p class="bold">Create Path Point</p>'."\n"; // new path point
        echo 'If you leave any row empty, it will not be added.';
        echo '<form action="./index.php?do=createpathpoint" method="post" /><table border="1">';
        echo '<tr><th>X</th><th>Y</th><th>Z</th><th>Sector</th></tr>';
        // This is the first line, this time we get the data from the $_POST (unlike the first time, see the next elseif).
        echo '<td><input type="hidden" name="path_id" value="'.$path_id.'" /><input type="hidden" name="x[]" value="'.$_POST['x'][0].'" />'.$_POST['x'][0].'</td>';
        echo '<td><input type="hidden" name="y[]" value="'.$_POST['y'][0].'" />'.$_POST['y'][0].'</td>';
        echo '<td><input type="hidden" name="z[]" value="'.$_POST['z'][0].'" />'.$_POST['z'][0].'</td>';
        echo '<td><input type="hidden" name="loc_sector_id[]" value="'.$_POST['loc_sector_id'][0].'" /><input type="hidden" name="wp1_name" value="'.$_POST['wp1_name'].'" />'.$_POST['wp1_name'].'</td></tr>';
        for ($i = 1; $i < count($_POST['x'])-1; $i++) // count either of the values to see how many lines there already were. Start at 1 and go to count-1 to avoid printing the first and last point again.
        {
            echo '<tr>';
            echo '<td><input type="text" name="x[]" value="'.$_POST['x'][$i].'" /></td>';
            echo '<td><input type="text" name="y[]" value="'.$_POST['y'][$i].'" /></td>';
            echo '<td><input type="text" name="z[]" value="'.$_POST['z'][$i].'" /></td>';
            echo '<td>'.DrawSelectBox('sectorid', $sectors, 'loc_sector_id[]', ''.$_POST['loc_sector_id'][$i].'', true).'</td></tr>';
        }
        for ($i = 0; $i < $_POST['more_fields']; $i++) // form was used before, show all existing rows, and provide new space.
        { 
            echo '<tr>';
            echo '<td><input type="text" name="x[]" value="" /></td>';
            echo '<td><input type="text" name="y[]" value="" /></td>';
            echo '<td><input type="text" name="z[]" value="" /></td>';
            echo '<td>'.DrawSelectBox('sectorid', $sectors, 'loc_sector_id[]', '', true).'</td></tr>';
        }
        // This is the last line, this time we get the data from the $_POST (unlike the first time, see the next elseif).
        $last = count($_POST['x'])-1; // get the last entry. 
        echo '<tr><td><input type="hidden" name="path_id" value="'.$path_id.'" /><input type="hidden" name="x[]" value="'.$_POST['x'][$last].'" />'.$_POST['x'][$last].'</td>';
        echo '<td><input type="hidden" name="y[]" value="'.$_POST['y'][$last].'" />'.$_POST['y'][$last].'</td>';
        echo '<td><input type="hidden" name="z[]" value="'.$_POST['z'][$last].'" />'.$_POST['z'][$last].'</td>';
        echo '<td><input type="hidden" name="loc_sector_id[]" value="'.$_POST['loc_sector_id'][$last].'" /><input type="hidden" name="wp2_name" value="'.$_POST['wp2_name'].'" />'.$_POST['wp2_name'].'</td></tr>';
        echo '<tr><td colspan="2">Add <input type="text" name="more_fields" value="0"> more fields to this form <input type=submit name="add" value="add"/></td></tr>';
        echo '<tr><td></td><td><input type=submit name="commit" value="Create Path"/></td></tr>'; 
        echo '</table></form>'."\n";


        }
    elseif (checkaccess('rules', 'create'))
    {
        if (isset($_GET['path_id']) && is_numeric($_GET['path_id']))
        {
            $path_id = mysql_real_escape_string($_GET['path_id']);
        }
        else
        {
            echo '<p class="error">Invalid Path ID</p>';
            return;
        }
        $query = "SELECT id FROM sc_path_points WHERE path_id='$path_id'";
        $result = mysql_query2($query);
        if (mysql_num_rows($result) > 0)
        {
            echo '<p class="error">A path for this waypoint ('.$path_id.') already exists.</p>';
            return;
        }
        $sectors = PrepSelect('sectorid');
        echo '<p class="bold">Create Path Point</p>'."\n"; // new path point
        echo 'If you leave any row empty, it will not be added.';
        echo '<form action="./index.php?do=createpathpoint" method="post" /><table border="1">';
        echo '<tr><th>X</th><th>Y</th><th>Z</th><th>Sector</th></tr>';
        $query = "SELECT wp1.x AS wp1_x, wp1.y AS wp1_y, wp1.z AS wp1_z, wp1.loc_sector_id AS wp1_loc, s1.name AS wp1_name, wp2.x AS wp2_x, wp2.y AS wp2_y, wp2.z AS wp2_z, wp2.loc_sector_id AS wp2_loc, s2.name AS wp2_name FROM sc_waypoint_links AS wl LEFT JOIN sc_waypoints AS wp1 ON wl.wp1=wp1.id LEFT JOIN sc_waypoints AS wp2 ON wl.wp2=wp2.id LEFT JOIN sectors AS s1 ON s1.id=wp1.loc_sector_id LEFT JOIN sectors AS s2 ON s2.id=wp2.loc_sector_id WHERE wl.id='$path_id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        // This is the first line, get data from the waypoint link (sql), to obtain x/y/z for wp1.
        echo '<td><input type="hidden" name="path_id" value="'.$path_id.'" /><input type="hidden" name="x[]" value="'.$row['wp1_x'].'" />'.$row['wp1_x'].'</td>';
        echo '<td><input type="hidden" name="y[]" value="'.$row['wp1_y'].'" />'.$row['wp1_y'].'</td>';
        echo '<td><input type="hidden" name="z[]" value="'.$row['wp1_z'].'" />'.$row['wp1_z'].'</td>';
        echo '<td><input type="hidden" name="loc_sector_id[]" value="'.$row['wp1_loc'].'" /><input type="hidden" name="wp1_name" value="'.$row['wp1_name'].'" />'.$row['wp1_name'].'</td></tr>';
        for ($i = 0; $i < 3; $i++) // form wasn't used before, show 3 rows to give the user some space.
        { 
            echo '<tr>';
            echo '<td><input type="text" name="x[]" value="" /></td>';
            echo '<td><input type="text" name="y[]" value="" /></td>';
            echo '<td><input type="text" name="z[]" value="" /></td>';
            echo '<td>'.DrawSelectBox('sectorid', $sectors, 'loc_sector_id[]', '', true).'</td></tr>';
        }
        // This is the last line, get data from the waypoint link (sql), to obtain x/y/z for wp2.
        echo '<tr><td><input type="hidden" name="path_id" value="'.$path_id.'" /><input type="hidden" name="x[]" value="'.$row['wp2_x'].'" />'.$row['wp2_x'].'</td>';
        echo '<td><input type="hidden" name="y[]" value="'.$row['wp2_y'].'" />'.$row['wp2_y'].'</td>';
        echo '<td><input type="hidden" name="z[]" value="'.$row['wp2_z'].'" />'.$row['wp2_z'].'</td>';
        echo '<td><input type="hidden" name="loc_sector_id[]" value="'.$row['wp2_loc'].'" /><input type="hidden" name="wp2_name" value="'.$row['wp2_name'].'" />'.$row['wp2_name'].'</td></tr>';
        echo '<tr><td colspan="2">Add <input type="text" name="more_fields" value="0"> more fields to this form <input type=submit name="add" value="add"/></td></tr>';
        echo '<tr><td></td><td><input type=submit name="commit" value="Create Path"/></td></tr>'; 
        echo '</table></form>'."\n";

    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

function deletepathpoint() 
{
    if (checkaccess('rules', 'delete') && isset($_POST['commit']) && $_POST['commit'] == 'Confirm Delete')
    {
        if (isset($_POST['path_id']) && is_numeric($_POST['path_id']))
        {
            $path_id = mysql_real_escape_string($_POST['path_id']);
        }
        else
        {
            echo '<p class="error">Invalid Path ID</p>';
            return;
        }
        $query = "DELETE FROM sc_path_points WHERE path_id='$path_id'";
        $result = mysql_query2($query);
        echo '<p class="error">Delete Successful</p>';
        unset($_POST);
        listpathpoints();
    }
    elseif (checkaccess('rules', 'delete'))   
    {
        if (isset($_GET['path_id']) && is_numeric($_GET['path_id']))
        {
            $path_id = mysql_real_escape_string($_GET['path_id']);
        }
        else
        {
            echo '<p class="error">Invalid Path ID</p>';
            return;
        }
        $query = "SELECT DISTINCT s.name AS sector, w.name AS wp1_name, ww.name AS wp2_name FROM sc_path_points AS p LEFT JOIN sc_waypoint_links AS wl ON p.path_id=wl.id LEFT JOIN sc_waypoints AS w ON wl.wp1=w.id LEFT JOIN sc_waypoints AS ww ON wl.wp2=ww.id LEFT JOIN sectors AS s ON w.loc_sector_id=s.id WHERE p.path_id='$path_id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result);
        echo 'You are about to delete all path_points between '.$row['wp1_name'].' and '.$row['wp2_name'].' belonging to waypoint link '.$path_id.' - Please confirm you wish to do this<br/>';
        echo '<form action="./index.php?do=deletepathpoint" method="post">';
        echo '<input type="hidden" name="path_id" value="'.$path_id.'" /><input type="submit" name="commit" value="Confirm Delete" /></form>';
    }
    else
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
    }
}

?>