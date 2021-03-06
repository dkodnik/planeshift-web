<?php
function listwaypointaliases()
{
    if (!checkaccess('other', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (isset($_POST['commit']) && checkaccess('other', 'edit'))
    {
        if ($_POST['commit'] == 'Update Waypoint Alias' && isset($_POST['id']))
        {
            $id = escapeSqlString($_POST['id']);
            $waypoint_id = escapeSqlString($_POST['waypoint_id']);
            $alias = escapeSqlString($_POST['alias']);
            $rotation_angle = escapeSqlString($_POST['rotation_angle']);
            $query = "UPDATE sc_waypoint_aliases SET wp_id='$waypoint_id', alias='$alias', rotation_angle='$rotation_angle' WHERE id='$id'";
            $result = mysql_query2($query);
            echo '<p class="error">Update Successful</p>';
        }
        elseif($_POST['commit'] == 'Delete' && checkaccess('other', 'delete'))
        {
            $id = escapeSqlString($_POST['id']);
            $query = "DELETE FROM sc_waypoint_aliases WHERE id='$id' LIMIT 1";
            $result = mysql_query2($query);
            echo '<p class="error">Delete Successful</p>';
        }
        elseif($_POST['commit'] == 'Create Waypoint Alias' && checkaccess('other', 'create'))
        {
            $waypoint_id = escapeSqlString($_POST['waypoint_id']);
            $alias = escapeSqlString($_POST['alias']);
            $rotation_angle = escapeSqlString($_POST['rotation_angle']);
            $query = "INSERT INTO sc_waypoint_aliases SET wp_id='$waypoint_id', alias='$alias', rotation_angle='$rotation_angle'";
            $result = mysql_query2($query);
            echo '<p class="error">Creation Successful</p>';
        }
        else
        {
            echo '<p class="error">Invalid Commit found - Returning to listing</p>';
        }
        unset($_POST);
        listwaypointaliases();
        return;
    }
    elseif (checkaccess('other', 'edit') && isset($_POST['action']))
    {
        if ($_POST['action'] == 'Edit')
        {
            $waypoints = PrepSelect('waypoints');
            $id = escapeSqlString($_POST['id']);
            $query = "SELECT wp_id, alias, rotation_angle FROM sc_waypoint_aliases WHERE id='$id'";
            $result = mysql_query2($query);
            $row = fetchSqlAssoc($result);
            $navurl = (isset($_GET['sector']) ? '&amp;sector='.$_GET['sector'] : '' ).(isset($_GET['sort']) ? '&amp;sort='.$_GET['sort'] : '' ).(isset($_GET['limit']) ? '&amp;limit='.$_GET['limit'] : '' );
            echo '<form action="./index.php?do=waypointalias'.$navurl.'" method="post">';
            echo '<table border="1">';
            echo '<tr><td>Waypoint Name: '.DrawSelectBox('waypoints', $waypoints, 'waypoint_id' , $row['wp_id'], false).'</td>';
            echo '<td>Alias: <input type="text" name="alias" value="'.$row['alias'].'" /></td>';
            echo '<td>Rotation angle: <input type="text" name="rotation_angle" value="'.$row['rotation_angle'].'" /></td></tr>';
            echo '</table>';
            echo '<input type="hidden" name="id" value="'.$id.'" />';
            echo '<input type="submit" name="commit" value="Update Waypoint Alias" />';
            echo '</form>';
        }
        else
        {
            unset($_POST['action']);
            echo '<p class="error">Error: Bad action submitted, returning to listing</p>';
            listwaypoints();
        }
        return;
    } // no else, else is the rest of this document, all the above return.

    $query = "SELECT wa.id, wa.wp_id, wa.alias, wp.name AS waypoint_name, s.name AS sector, wa.rotation_angle FROM sc_waypoint_aliases AS wa LEFT JOIN sc_waypoints AS wp ON wp.id=wa.wp_id LEFT JOIN sectors AS s ON s.id=wp.loc_sector_id";
    if (isset($_GET['id']) && $_GET['id']!='')
    {
        $id = escapeSqlString($_GET['id']);  // limit to 1 specific result if requested
        $query .= " WHERE w.id='$id'";
    }
    elseif (isset($_GET['sector']) && $_GET['sector'] != '' && $_GET['sector'] != 0)  // limit to sectors
    {
        $sec = escapeSqlString($_GET['sector']);
        $query .= " WHERE wp.loc_sector_id='$sec'";
    }
    if (isset($_GET['sort']))  // get the sorting right
    {
        switch($_GET['sort'])
        {
            case 'alias':
                $query .= ' ORDER BY wa.alias';
                break;
            case 'waypoint_name':
                $query .= ' ORDER BY waypoint_name';
                break;
            case 'sector':
                $query .= ' ORDER BY sector, wa.alias';
                break;
            default:
                $query .= ' ORDER BY sector, wa.alias';
        }
    }
    else
    {
        $query .= ' ORDER BY sector, wa.alias';
    }
    if (isset($_GET['limit']) && is_numeric($_GET['limit']))  // used for the page seperation
    {
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
    
    /* 
        -----------------------------------   Start page seperator   -----------------------------------
    */
    $sid = 0;
    if (isset($_GET['sector']))
    {
        $sid = $_GET['sector'];
    }
    if ($lim > 30)
    {
        echo '<a href="./index.php?do=waypointalias';
        if (isset($_GET['sort']))
        {
            echo '&amp;sort='.$_GET['sort'];
        }
        echo '&amp;limit='.$prev_lim.'&amp;sector='.$sid.'">Previous Page</a> ';
    }
    echo ' - Displaying records '.$prev_lim.' through '.$lim.' - ';
    $where = ($sid == 0 ? '' : " WHERE w.loc_sector_id=$sid");
    $result2 = mysql_query2('select count(w.id) AS mylimit FROM sc_waypoints AS w'.$where);
    $row2 = fetchSqlAssoc($result2);
    if ($row2['mylimit'] > $lim)
    {
        echo '<a href="./index.php?do=waypointalias';
        if (isset($_GET['sort']))
        {
            echo '&amp;sort='.$_GET['sort'];
        }
        echo '&amp;limit='.($lim+30).'&amp;sector='.$sid.'">Next Page</a>';
    }
    /* 
        -----------------------------------   End page seperator   -----------------------------------
    */
    
    /* 
        -----------------------------------   Start sector selection   -----------------------------------
    */
    $sectors = PrepSelect('sectorid');
    $mySector = 'NULL';
    if (isset($_GET['sector'])){
        $mySector = $_GET['sector'];
    }
    echo ' - <form action="./index.php" method="get"><input type="hidden" name="do" value="waypointalias"/>';
    if (isset($_GET['sort']))
    {
        echo '<input type="hidden" name="sort" value="'.$_GET['sort'].'"/>';
    }
    if (isset($_GET['limit']))
    {
        echo '<input type="hidden" name="limit" value="'.$_GET['limit'].'"/>';
    }
    $urlParams = '';
    echo DrawSelectBox('sectorid', $sectors, 'sector' ,$mySector, true).'<input type="submit" name="submit" value="Limit By Sector" /></form>';
    /* 
        -----------------------------------   End sector selection   -----------------------------------
    */
    
    if (sqlNumRows($result) == 0){
        echo '<p class="error">No Waypoint Aliases</p>';
    }    
    else // Print the table of results
    {  
        if (isset($_GET['limit']))
        {
            $urlParams .= '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector']))
        {
            $urlParams .= '&amp;sector='.$_GET['sector'];
        }
        echo '<table border="1">';
        echo '<tr><th><a href="./index.php?do=waypointalias&amp;sort=waypoint_name'.$urlParams.'">Waypoint Name</a></th>';
        echo '<th><a href="./index.php?do=waypointalias&amp;sort=sector'.$urlParams.'">Sector</a></th>';
        echo '<th><a href="./index.php?do=waypointalias&amp;sort=alias'.$urlParams.'">Alias</a></th>';
        echo '<th>Rotation Angle</th>';
        if (checkaccess('other','edit'))
        {
            echo '<th>Actions</th>';
        }
        echo '</tr>';
        while ($row = fetchSqlAssoc($result))
        {
            echo '<tr>';
            echo '<td><a href="./index.php?do=waypoint&amp;id='.$row['wp_id'].'" >'.$row['waypoint_name'].'</a></td>';
            echo '<td>'.$row['sector'].'</td>';
            echo '<td>'.$row['alias'].'</td>';
            echo '<td>'.$row['rotation_angle'].'</td>';
            if (checkaccess('other', 'edit')) // offer edit buttons
            {
                $navurl = (isset($_GET['sector']) ? '&amp;sector='.$_GET['sector'] : '' ).(isset($_GET['sort']) ? '&amp;sort='.$_GET['sort'] : '' ).(isset($_GET['limit']) ? '&amp;limit='.$_GET['limit'] : '' );
                echo '<td><form action="./index.php?do=waypointalias'.$navurl.'" method="post">';
                echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
                echo '<input type="submit" name="action" value="Edit" />';
                if (checkaccess('other', 'delete'))
                {
                    echo '<br/><input type="submit" name="commit" value="Delete" />';
                }       
                echo '</form></td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    
        if (checkaccess('other', 'create'))  // offer the create alias box
        {
            $waypoints = PrepSelect('waypoints');
            echo '<hr/><p>Create New Waypoint Alias:</p>';
            echo '<form action="./index.php?do=waypointalias" method="post">';
            echo '<table border="1">';
            echo '<tr><td>Waypoint Name: '.DrawSelectBox('waypoints', $waypoints, 'waypoint_id' , '', false).'</td>';
            echo '<td>Alias: <input type="text" name="alias" /></td>';
            echo '<td>Rotation Angle: <input type="text" name="rotation_angle" value="0.0" />degrees</td></tr>';
            echo '</table>';
            echo '<input type="submit" name="commit" value="Create Waypoint Alias" />';
            echo '</form>';
        }
    }
}
?>
