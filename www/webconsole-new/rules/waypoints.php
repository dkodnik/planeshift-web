<?php
function listwaypoints(){
  if (checkaccess('rules', 'read')){
    if (isset($_POST['commit']) && checkaccess('rules', 'edit')){
      if ($_POST['commit'] == "Update Waypoint" && isset($_POST['id'])){
        $id = mysql_real_escape_string($_POST['id']);
        $name = mysql_real_escape_string($_POST['name']);
        $group = mysql_real_escape_string($_POST['group']);
        $sector = mysql_real_escape_string($_POST['loc_sector_id']);
        $x = mysql_real_escape_string($_POST['x']);
        $y = mysql_real_escape_string($_POST['y']);
        $z = mysql_real_escape_string($_POST['z']);
        $radius = mysql_real_escape_string($_POST['radius']);
        $flags = '';
        foreach ($_POST['flags'] AS $key => $value){
          $flags = $flags . $value . ', ';
        }
        if (strlen($flags) > 0){
          $flags = substr($flags, 0, -2);
        }
        $flag = mysql_real_escape_string($flags);
        $query = "UPDATE sc_waypoints SET name='$name', wp_group='$group', loc_sector_id='$sector', x='$x', y='$y', z='$z', radius='$radius', flags='$flag' WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
      }else if($_POST['commit'] == "Confirm Delete" && checkaccess('rules', 'delete')){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "DELETE FROM sc_waypoints WHERE id='$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
      }else if($_POST['commit'] == "Create Waypoint" && checkaccess('rules', 'create')){
        $name = mysql_real_escape_string($_POST['name']);
        $group = mysql_real_escape_string($_POST['group']);
        $sector = mysql_real_escape_string($_POST['loc_sector_id']);
        $x = mysql_real_escape_string($_POST['x']);
        $y = mysql_real_escape_string($_POST['y']);
        $z = mysql_real_escape_string($_POST['z']);
        $radius = mysql_real_escape_string($_POST['radius']);
        $flags = '';
        foreach ($_POST['flags'] AS $key => $value){
          $flags = $flags . $value . ', ';
        }
        if (strlen($flags) > 0){
          $flags = substr($flags, 0, -2);
        }
        $flag = mysql_real_escape_string($flags);
        $query = "INSERT INTO sc_waypoints SET name='$name', wp_group='$group', loc_sector_id='$sector', x='$x', y='$y', z='$z', radius='$radius', flags='$flag'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
      }else{
        echo '<p class="error">Invalid Commit found - Returning to listing</p>';
      }
      unset($_POST);
      listwaypoints();
      return;
    }else if (checkaccess('rules', 'edit') && isset($_POST['action'])){
      $id = mysql_real_escape_string($_POST['id']);
      if ($_POST['action'] == 'Edit'){
        $query = "SELECT * FROM sc_waypoints WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        echo '<form action="./index.php?do=waypoint" method="post"><input type="hidden" name="id" value="'.$row['id'].'" />';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" value="'.$row['name'].'" /></td></tr>';
        echo '<tr><td>Group</td><td><input type="text" name="group" value="'.$row['wp_group'].'"/></td></tr>';
        $Sectors = PrepSelect('sectorid');
        echo '<tr><td>Sector</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', $row['loc_sector_id']).'</td></tr>';
        echo '<tr><td>X</td><td><input type="text" name="x" value="' . $row['x'] . '"/></td></tr>';
        echo '<tr><td>Y</td><td><input type="text" name="y" value="' . $row['y'] . '"/></td></tr>';
        echo '<tr><td>Z</td><td><input type="text" name="z" value="' . $row['z'] . '"/></td></tr>';
        echo '<tr><td>Radius</td><td><input type="text" name="radius" value="'.$row['radius'].'" /></td></tr>';
        echo '<tr><td>Flags</td><td>';
        $flags = ' '.$row['flags'];
        if (strpos($flags, 'ALLOW_RETURN')){
          echo 'ALLOW_RETURN: <input type="checkbox" name="flags[]" value="ALLOW_RETURN" checked="true" /><br/>';
        }else{
          echo 'ALLOW_RETURN: <input type="checkbox" name="flags[]" value="ALLOW_RETURN" /><br/>';
        }
        if (strpos($flags, 'UNDERGROUND')){
          echo 'UNDERGROUND: <input type="checkbox" name="flags[]" value="UNDERGROUND" checked="true" /><br/>';
        }else{
          echo 'UNDERGROUND: <input type="checkbox" name="flags[]" value="UNDERGROUND" /><br/>';
        }
        if (strpos($flags, 'UNDERWATER')){
          echo 'UNDERWATER: <input type="checkbox" name="flags[]" value="UNDERWATER" checked="true" /><br/>';
        }else{
          echo 'UNDERWATER: <input type="checkbox" name="flags[]" value="UNDERWATER" /><br/>';
        }
        if (strpos($flags, 'PRIVATE')){
          echo 'PRIVATE: <input type="checkbox" name="flags[]" value="PRIVATE" checked="true"/><br/>';
        }else{
          echo 'PRIVATE: <input type="checkbox" name="flags[]" value="PRIVATE" /><br/>';
        }
        if (strpos($flags, 'PUBLIC')){
          echo 'PUBLIC: <input type="checkbox" name="flags[]" value="PUBLIC" checked="true" /<br/>';
        }else{
          echo 'PUBLIC: <input type="checkbox" name="flags[]" value="PUBLIC" /><br/>';
        }
        if (strpos($flags, 'CITY')){
          echo 'CITY: <input type="checkbox" name="flags[]" value="CITY" checked="true"/><br/>';
        }else{
          echo 'CITY: <input type="checkbox" name="flags[]" value="CITY" /><br/>';
        }
        if (strpos($flags, 'INDOOR')){
          echo 'INDOOR: <input type="checkbox" name="flags[]" value="INDOOR" checked="true"/><br/>';
        }else{
          echo 'INDOOR: <input type="checkbox" name="flags[]" value="INDOOR" /><br/>';
        }
        if (strpos($flags, 'PATH')){
          echo 'PATH: <input type="checkbox" name="flags[]" value="PATH" checked="true"/><br/>';
        }else{
          echo 'PATH: <input type="checkbox" name="flags[]" value="PATH" /><br/>';
        }
        if (strpos($flags, 'ROAD')){
          echo 'ROAD: <input type="checkbox" name="flags[]" value="ROAD" checked="true"/><br/>';
        }else{
          echo 'ROAD: <input type="checkbox" name="flags[]" value="ROAD" /><br/>';
        }
        if (strpos($flags, 'GROUND')){
          echo 'GROUND: <input type="checkbox" name="flags[]" value="GROUND" checked="true"/><br/>';
        }else{
          echo 'GROUND: <input type="checkbox" name="flags[]" value="GROUND" /><br/>';
        }

        echo '</td></tr>';
        echo '</table>';
        echo '<input type="submit" name="commit" value="Update Waypoint" />';
        echo '</form>';
      }else if (($_POST['action'] == 'Delete') && checkaccess('rules', 'delete')){
        $id = mysql_real_escape_string($_POST['id']);
        $query = "SELECT id FROM sc_waypoint_links WHERE wp1='$id' OR wp2='$id'";
        $result = mysql_query2($query);
        if (mysql_num_rows($result) > 0)
        {
            echo '<p class="error">There are waypoint links still using this waypoint, it may not be deleted.</p>';
            echo '<p>Below are links to all such waypoints.<br /><br />';
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
            {
                echo '<a href="./index.php?do=editwaypointlink&id='.$row['id'].'">'.$row['id'].'</a><br />';
            }
            echo '</p>';
            return;
        }
        $query = "SELECT name FROM sc_waypoints WHERE id='$id'";
        $result = mysql_query2($query);
        $row = mysql_fetch_array($result);
        echo 'You are about to delete waypoint '.$row['name'].' - Please confirm you wish to do this<br/>';
        echo '<form action="./index.php?do=waypoint" method="post">';
        echo '<input type="hidden" name="id" value="'.$id.'"/><input type="submit" name="commit" value="Confirm Delete" /></form>';
      }else{
        unset($_POST['action']);
        echo '<p class="error">Error: Bad action submitted</p>';
        listwaypoints();
        return;
      }
    }else{
      $query = "SELECT w.id, w.name, w.wp_group, w.x, w.y, w.z, w.radius, w.flags, w.loc_sector_id, s.name AS sector FROM sc_waypoints AS w LEFT JOIN sectors AS s on s.id=w.loc_sector_id";
      if (isset($_GET['id']) && $_GET['id']!=''){
        $id = mysql_real_escape_string($_GET['id']);
        $query .= " WHERE w.id='$id'";
      }
      elseif (isset($_GET['sector']) && $_GET['sector'] != '' && $_GET['sector'] != 0){
        $sec = mysql_real_escape_string($_GET['sector']);
        $query .= " WHERE w.loc_sector_id='$sec'";
      }
      if (isset($_GET['sort'])){
        switch($_GET['sort']){
          case 'name':
            $query .= ' ORDER BY w.name';
            break;
          case 'group':
            $query .= ' ORDER BY w.wp_group';
            break;
          case 'sector':
            $query .= ' ORDER BY sector, name';
            break;
          default:
            $query .= ' ORDER BY sector, name';
        }
      }else{
        $query .= ' ORDER BY sector, name';
      }
      if (isset($_GET['limit']) && is_numeric($_GET['limit'])){
        $prev_lim = $_GET['limit'] - 30;
        $lim = $_GET['limit'];
        $query = $query . " LIMIT $prev_lim, 30"; // limit 1, 10 is offset 1, taking 10 records.
      }else{
        $query = $query . " LIMIT 30";
        $lim = 30;
        $prev_lim = 0;
      }
      $result = mysql_query2($query);
      if (mysql_numrows($result) == 0){
        echo '<p class="error">No Waypoints</p>';
      }else{
        $sid = 0;
        if (isset($_GET['sector']))
        {
            $sid = $_GET['sector'];
        }
        if ($lim > 30){
          echo '<a href="./index.php?do=waypoint';
          if (isset($_GET['sort'])){
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
          echo '<a href="./index.php?do=waypoint';
          if (isset($_GET['sort'])){
            echo '&amp;sort='.$_GET['sort'];
          }
          echo '&amp;limit='.($lim+30).'&amp;sector='.$sid.'">Next Page</a>';
        }
        $Sectors = PrepSelect('sectorid');
        if (!isset($_GET['sector'])){
          $_GET['sector']="NULL";
        }
        echo ' - <form action="./index.php" method="get"><input type="hidden" name="do" value="waypoint"/>';
        if (isset($_GET['sort'])){
          echo '<input type="hidden" name="sort" value="'.$_GET['sort'].'"/>';
        }
        if (isset($_GET['limit'])){
          echo '<input type="hidden" name="limit" value="'.$_GET['limit'].'"/>';
        }
        echo DrawSelectBox('sectorid', $Sectors, 'sector' ,$_GET['sector'], true).'<input type="submit" name="submit" value="Limit By Sector" /></form>';
        if ($_GET['sector'] == "NULL"){
          unset($_GET['sector']);
        }
        echo '<table border="1">';
        echo '<tr><th><a href="./index.php?do=waypoint&amp;sort=name';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector'])){
          echo '&amp;sector='.$_GET['sector'];
        }
        echo '">Name</a></th>';
        echo '<th><a href="./index.php?do=waypoint&amp;sort=group';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector'])){
          echo '&amp;sector='.$_GET['sector'];
        }
        echo '">Group</a></th>';
        echo '<th><a href="./index.php?do=waypoint&amp;sort=sector';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector'])){
          echo '&amp;sector='.$_GET['sector'];
        }
        echo '">Sector</a></th><th>X</th><th>Y</th><th>Z</th><th>Radius</th><th>flags</th>';
        if (checkaccess('rules','edit')){
          echo '<th>Actions</th>';
        }
        echo '</tr>';
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)){
          echo '<tr>';
          echo '<td>'.$row['name'].'</td>';
          echo '<td>'.$row['wp_group'].'</td>';
          echo '<td>'.$row['sector'].'</td>';
          echo '<td>'.$row['x'].'</td>';
          echo '<td>'.$row['y'].'</td>';
          echo '<td>'.$row['z'].'</td>';
          echo '<td>'.$row['radius'].'</td>';
          echo '<td>'.$row['flags'].'</td>';
          if (checkaccess('rules', 'edit')){
            echo '<td><form action="./index.php?do=waypoint" method="post">';
            echo '<input type="hidden" name="id" value="'.$row['id'].'" />';
            echo '<input type="submit" name="action" value="Edit" />';
            if (checkaccess('rules', 'delete')){
              echo '<br/><input type="submit" name="action" value="Delete" />';
            }
            echo '</form></td>';
          }
          echo '</tr>';
        }
        echo '</table>';
        if (checkaccess('rules', 'create')){
        echo '<hr/><p>Create New Waypoint:</p><form action="./index.php?do=waypoint" method="post">';
        echo '<table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>Name</td><td><input type="text" name="name" /></td></tr>';
        echo '<tr><td>Group</td><td><input type="text" name="group"/></td></tr>';
        $Sectors = PrepSelect('sectorid');
        echo '<tr><td>Sector</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', '' ).'</td></tr>';
        echo '<tr><td>X</td><td><input type="text" name="x"/></td></tr>';
        echo '<tr><td>Y</td><td><input type="text" name="y"/></td></tr>';
        echo '<tr><td>Z</td><td><input type="text" name="z"/></td></tr>';
        echo '<tr><td>Radius</td><td><input type="text" name="radius" /></td></tr>';
        echo '<tr><td>Flags</td><td>';
        $flags = ' '.$row['flags'];
          echo 'ALLOW_RETURN: <input type="checkbox" name="flags[]" value="ALLOW_RETURN" /><br/>';
          echo 'UNDERGROUND: <input type="checkbox" name="flags[]" value="UNDERGROUND" /><br/>';
          echo 'UNDERWATER: <input type="checkbox" name="flags[]" value="UNDERWATER" /><br/>';
          echo 'PRIVATE: <input type="checkbox" name="flags[]" value="PRIVATE" /><br/>';
          echo 'PUBLIC: <input type="checkbox" name="flags[]" value="PUBLIC" /><br/>';
          echo 'CITY: <input type="checkbox" name="flags[]" value="CITY" /><br/>';
          echo 'INDOOR: <input type="checkbox" name="flags[]" value="INDOOR" /><br/>';
          echo 'PATH: <input type="checkbox" name="flags[]" value="PATH" /><br/>';
          echo 'ROAD: <input type="checkbox" name="flags[]" value="ROAD" /><br/>';
          echo 'GROUND: <input type="checkbox" name="flags[]" value="GROUND" /><br/>';
        echo '</td></tr>';
        echo '</table>';
        echo '<input type="submit" name="commit" value="Create Waypoint" />';
        echo '</form>';

        }
      }
    }
  }else{
    echo '<p class="error">You are not authorized to use these functions</p>';
  }
}
?>
