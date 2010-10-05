<?php
function listwaypointaliases()
{
    if (!checkaccess('rules', 'read'))
    {
        echo '<p class="error">You are not authorized to use these functions</p>';
        return;
    }
    if (isset($_POST['commit']) && checkaccess('rules', 'edit'))
    {
        if ($_POST['commit'] == 'Update Waypoint Alias' && isset($_POST['id']))
        {
            $id = mysql_real_escape_string($_POST['id']);
            $waypoint_id = mysql_real_escape_string($_POST['waypoint_id']);
            $alias = mysql_real_escape_string($_POST['alias']);
            $query = "UPDATE sc_waypoint_aliases SET wp_id='$waypoint_id', alias='$alias' WHERE id='$id'";
            $result = mysql_query2($query);
            echo '<p class="error">Update Successful</p>';
        }
        elseif($_POST['commit'] == 'Delete' && checkaccess('rules', 'delete'))
        {
            $id = mysql_real_escape_string($_POST['id']);
            $query = "DELETE FROM sc_waypoint_aliases WHERE id='$id' LIMIT 1";
            $result = mysql_query2($query);
            echo '<p class="error">Delete Successful</p>';
        }
        elseif($_POST['commit'] == 'Create Waypoint Alias' && checkaccess('rules', 'create'))
        {
            $waypoint_id = mysql_real_escape_string($_POST['waypoint_id']);
            $alias = mysql_real_escape_string($_POST['alias']);
            $query = "INSERT INTO sc_waypoint_aliases SET wp_id='$waypoint_id', alias='$alias'";
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
    elseif (checkaccess('rules', 'edit') && isset($_POST['action']))
    {
        if ($_POST['action'] == 'Edit')
        {
            $waypoints = PrepSelect('waypoints');
            $id = mysql_real_escape_string($_POST['id']);
            $query = "SELECT wp_id, alias FROM sc_waypoint_aliases WHERE id='$id'";
            $result = mysql_query2($query);
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            echo '<form action="./index.php?do=waypointalias" method="post">';
            echo '<table border="1">';
            echo '<tr><td>Waypoint Name: '.DrawSelectBox('waypoints', $waypoints, 'waypoint_id' , $row['wp_id'], false).'</td>';
            echo '<td>Alias: <input type="text" name="alias" value="'.$row['alias'].'" /></td></tr>';
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

    $query = "SELECT wa.id, wa.wp_id, wa.alias, wp.name AS waypoint_name, s.name AS sector FROM sc_waypoint_aliases AS wa LEFT JOIN sc_waypoints AS wp ON wp.id=wa.wp_id LEFT JOIN sectors AS s ON s.id=wp.loc_sector_id";
    if (isset($_GET['id']) && $_GET['id']!='')
    {
        $id = mysql_real_escape_string($_GET['id']);  // limit to 1 specific result if requested
        $query .= " WHERE w.id='$id'";
    }
    elseif (isset($_GET['sector']) && $_GET['sector'] != '' && $_GET['sector'] != 0)  // limit to sectors
    {
        $sec = mysql_real_escape_string($_GET['sector']);
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
    $row2 = mysql_fetch_array($result2);
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
    
    if (mysql_numrows($result) == 0){
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
        if (checkaccess('rules','edit'))
        {
            echo '<th>Actions</th>';
        }
        echo '</tr>';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
            echo '<tr>';
            echo '<td><a href="./index.php?do=waypoint&amp;id='.$row['wp_id'].'" >'.$row['waypoint_name'].'</a></td>';
            echo '<td>'.$row['sector'].'</td>';
            echo '<td>'.$row['alias'].'</td>';
            if (checkaccess('rules', 'edit')) // offer edit buttons
            {
                echo '<td><form action="./index.php?do=waypointalias" method="post">';
                echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
                echo '<input type="submit" name="action" value="Edit" />';
                if (checkaccess('rules', 'delete'))
                {
                    echo '<br/><input type="submit" name="commit" value="Delete" />';
                }       
                echo '</form></td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    
        if (checkaccess('rules', 'create'))  // offer the create alias box
        {
            $waypoints = PrepSelect('waypoints');
            echo '<hr/><p>Create New Waypoint Alias:</p>';
            echo '<form action="./index.php?do=waypointalias" method="post">';
            echo '<table border="1">';
            echo '<tr><td>Waypoint Name: '.DrawSelectBox('waypoints', $waypoints, 'waypoint_id' , '', false).'</td>';
            echo '<td>Alias: <input type="text" name="alias" /></td></tr>';
            echo '</table>';
            echo '<input type="submit" name="commit" value="Create Waypoint Alias" />';
            echo '</form>';
        }
    }
}
?>