<?php
function listlocations(){
  if (checkaccess('npcs', 'read')){
    if (isset($_POST['commit']) && checkaccess('npcs', 'edit')){
      if ($_POST['commit'] == "Update Location"){
        $id = escapeSqlString($_POST['id']);
        $name = escapeSqlString($_POST['name']);
        $loc_sector_id = escapeSqlString($_POST['loc_sector_id']);
        $x = escapeSqlString($_POST['x']);
        $y = escapeSqlString($_POST['y']);
        $z = escapeSqlString($_POST['z']);
        $radius = escapeSqlString($_POST['radius']);
        $angle = escapeSqlString($_POST['angle']);
        $id_prev_loc_in_region = escapeSqlString($_POST['previous']);
        $type_id = escapeSqlString($_POST['type']);
        $query = "UPDATE sc_locations SET name='$name', loc_sector_id='$loc_sector_id', x='$x', y='$y', z='$z', radius='$radius', angle='$angle', id_prev_loc_in_region='$id_prev_loc_in_region', type_id='$type_id' WHERE id = '$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlocations();
        return;
      }else if ($_POST['commit'] == "Confirm Delete" && checkaccess('npcs', 'delete')){
        $id = escapeSqlString($_POST['id']);
        $query = "DELETE FROM sc_locations WHERE id = '$id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlocations();
        return;
      }else if ($_POST['commit'] == "Create Location" && checkaccess('npcs', 'create')){
        $name = escapeSqlString($_POST['name']);
        $loc_sector_id = escapeSqlString($_POST['loc_sector_id']);
        $x = escapeSqlString($_POST['x']);
        $y = escapeSqlString($_POST['y']);
        $z = escapeSqlString($_POST['z']);
        $radius = escapeSqlString($_POST['radius']);
        $angle = escapeSqlString($_POST['angle']);
        $id_prev_loc_in_region = escapeSqlString($_POST['previous']);
        $type_id = escapeSqlString($_POST['type']);
        $query = "INSERT INTO sc_locations SET name='$name', loc_sector_id='$loc_sector_id', x='$x', y='$y', z='$z', radius='$radius', angle='$angle', id_prev_loc_in_region='$id_prev_loc_in_region', type_id='$type_id'";
        $result = mysql_query2($query);
        echo '<p class="error">Update Successful</p>';
        unset($_POST);
        listlocations();
        return;
      }else{
        echo '<p class="error">Bad Commit, return to listing</p>';
        unset($_POST);
        listlocations();
        return;
      }
    }else if (isset($_POST['action']) && checkaccess('npcs', 'edit')){
      if ($_POST['action'] == "Edit"){
        $id = escapeSqlString($_POST['id']);
        $query = "SELECT * FROM sc_locations WHERE id = '$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        $navurl = (isset($_GET['sector']) ? '&amp;sector='.$_GET['sector'] : '' ).(isset($_GET['sort']) ? '&amp;sort='.$_GET['sort'] : '' ).(isset($_GET['limit']) ? '&amp;limit='.$_GET['limit'] : '' );
        echo '<form action="./index.php?do=location'.$navurl.'" method="post"><input type="hidden" name="id" value="'.$id.'" /><table border="1">';
        echo '<tr><th>Field</th><th>Value</th></tr>';
        echo '<tr><td>ID:</td><td>'.$id.'</td></tr>';
        echo '<tr><td>Name:</td><td><input type="text" name="name" value="'.$row['name'].'"/></td></tr>';
        $Sectors = PrepSelect('sectorid');
        echo '<tr><td>Sector:</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', $row['loc_sector_id']).'</td></tr>';
        echo '<tr><td>X:</td><td><input type="text" name="x" value="'.$row['x'].'"/></td></tr>';
        echo '<tr><td>Y:</td><td><input type="text" name="y" value="'.$row['y'].'" /></td></tr>';
        echo '<tr><td>Z:</td><td><input type="text" name="z" value="'.$row['z'].'" /></td></tr>';
        echo '<tr><td>Radius:</td><td><input type="text" name="radius" value="'.$row['radius'].'"/></td></tr>';
        echo '<tr><td>Angle:</td><td><input type="text" name="angle" value="'.$row['angle'].'" /></td></tr>';
        echo '<tr><td>Flags:</td><td>Not Supported</td></tr>';
        $Locations = PrepSelect('locations');
        echo '<tr><td>Previous Location</td><td>'.DrawSelectBox('locations', $Locations, 'previous', $row['id_prev_loc_in_region'], true).'</td></tr>';
        $LocationTypes = PrepSelect('location_type');
        echo '<tr><td>Location Type</td><td>'.DrawSelectBox('location_type',$LocationTypes,'type',$row['type_id'], true).'</td></tr>';

        echo '</table><input type="submit" name="commit" value="Update Location"/>';
        echo '</form>';
      }else if ($_POST['action'] == "Delete" && checkaccess('npcs', 'delete')){
        $id = escapeSqlString($_POST['id']);
        $query = "SELECT name FROM sc_locations WHERE id = '$id'";
        $result = mysql_query2($query);
        $row = fetchSqlAssoc($result);
        echo '<p>You are about to delete the location '.$row['name'].' id '.$id.' Please confirm:</p>';
        echo '<form action="./index.php?do=location" method="post"><input type="hidden" name="id" value="'.$id.'" />';
        echo '<input type="submit" name="commit" value="Confirm Delete" /></form>';
      }else{
        echo '<p class="error">Bad Action - Returning to list</p>';
        unset($_POST);
        listlocations();
        return;
      }
    }else{
      $query = "SELECT l.id, l.type_id, lt.name AS typename, l.id_prev_loc_in_region, l.name, l.x, l.y, l.z, l.radius, l.angle, l.flags, l.loc_sector_id, s.name AS sector FROM sc_locations AS l LEFT JOIN sectors AS s ON l.loc_sector_id = s.id LEFT JOIN sc_location_type AS lt ON l.type_id = lt.id";
      if (isset($_GET['id']))
      {
        $id = escapeSqlString($_GET['id']);
        $query .= " WHERE l.id='$id'";
      }
      elseif (isset($_GET['type']))
      {
        $type = escapeSqlString($_GET['type']);
        $query .= " WHERE l.type_id='$type'";
      }
      elseif (isset($_GET['sector']) && $_GET['sector'] != '' && $_GET['sector'] != 0){
        $sec = escapeSqlString($_GET['sector']);
        $query .= " WHERE l.loc_sector_id='$sec'";
      }
      if (isset($_GET['sort'])){
        switch ($_GET['sort']){
          case 'name':
            $query = $query . " ORDER BY name";
            break;
          case 'sector':
            $query = $query . " ORDER BY sector";
            break;
          case 'type':
            $query = $query . " ORDER BY typename";
            break;
          default:
            echo '<p class="error">Bad sort method - No sort used</p>';
        }
      }
      if (isset($_GET['limit'])){
        $lim = escapeSqlString($_GET['limit']);
        $ll = $lim - 30;
        $query = $query . " LIMIT $ll, 30"; // limit 1, 10 uses offset 1, then 10 records.
      }else{
        $query = $query . " LIMIT 0, 30";
        $ll = 0;
        $lim=30;
      }
      $result = mysql_query2($query);
      if (sqlNumRows($result) == 0){
        echo '<p class="error">No Locations in DataBase</p>';
      }else{
        $sid = 0;
        if (isset($_GET['sector']))
        {
          $sid = $_GET['sector'];
        }
        if ($lim> 30){
          echo '<a href="./index.php?do=location';
          if (isset($_GET['sort'])){
            echo '&amp;sort='.$_GET['sort'];
          }
          echo '&amp;limit='.$ll.'&amp;sector='.$sid.'">Previous Page</a>';
        }
        echo ' - Displaying records '.$ll.' through '.$lim.' - ';
        $result2 = mysql_query2('select count(id) AS mylimit FROM sc_locations');
        $row2 = fetchSqlAssoc($result2);
        if ($row2['mylimit'] > $lim)
        {
          echo '<a href="./index.php?do=location';
          if (isset($_GET['sort'])){
            echo '&amp;sort='.$_GET['sort'];
          }
          $lu = $lim + 30;
          echo '&amp;limit='.$lu.'&amp;sector='.$sid.'">Next Page</a>';;
        }
        $Sectors = PrepSelect('sectorid');
        if (!isset($_GET['sector'])){
          $_GET['sector']="NULL";
        }
        echo ' - <form action="./index.php" method="get"><input type="hidden" name="do" value="location"/>';
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
        echo '<tr><th>ID</th><th><a href="./index.php?do=location&amp;sort=name';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector'])){
          echo '&amp;sector='.$sid;
        }
        echo '">name</a></th>';
        echo '<th><a href="./index.php?do=location&amp;sort=sector';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector'])){
          echo '&amp;sector='.$sid;
        }
        echo '">Sector</a></th>';
        echo '<th>X</th>';
        echo '<th>Y</th>';
        echo '<th>Z</th>';
        echo '<th>Radius</th>';
        echo '<th>Angle</th>';
        echo '<th>Flags<br/>Not Supported</th>';
        echo '<th>Previous Location</th>';
        echo '<th><a href="./index.php?do=location&amp;sort=type';
        if (isset($_GET['limit'])){
          echo '&amp;limit='.$_GET['limit'];
        }
        if (isset($_GET['sector'])){
          echo '&amp;sector='.$sid;
        }
        echo '">Type</a></th>';
        if (checkaccess('npcs', 'edit')){
          echo '<th>Actions</th>';
        }
        echo '</tr>';
        while ($row = fetchSqlAssoc($result)){
          echo '<tr>';
          echo '<td>'.$row['id'].'</td>';
          echo '<td>'.$row['name'].'</td>';
          echo '<td>'.$row['sector'].'</td>';
          echo '<td>'.$row['x'].'</td>';
          echo '<td>'.$row['y'].'</td>';
          echo '<td>'.$row['z'].'</td>';
          echo '<td>'.$row['radius'].'</td>';
          echo '<td>'.$row['angle'].'</td>';
          echo '<td>'.$row['flags'].'</td>';
          echo '<td>'.$row['id_prev_loc_in_region'].'</td>';
          echo '<td>'.$row['typename'].'</td>';
          if (checkaccess('npcs', 'edit')){
            $navurl = (isset($_GET['sector']) ? '&amp;sector='.$_GET['sector'] : '' ).(isset($_GET['sort']) ? '&amp;sort='.$_GET['sort'] : '' ).(isset($_GET['limit']) ? '&amp;limit='.$_GET['limit'] : '' );
            echo '<td><form action="./index.php?do=location'.$navurl.'" method="post"><input type="hidden" name="id" value="'.$row['id'].'"/>';
            echo '<input type="submit" name="action" value="Edit"/>';
            echo '<br/><input type="submit" name="action" value="Delete"/>';
            echo '</form></td>';
          }
          echo '</tr>';
        }
        echo '</table>';
        if (checkaccess('npcs', 'create')){
          echo '<hr/>Create New Location:';
          echo '<form action="./index.php?do=location" method="post"><table border="1">';
          echo '<tr><th>Field</th><th>Value</th></tr>';
          echo '<tr><td>Name:</td><td><input type="text" name="name" /></td></tr>';
          $Sectors = PrepSelect('sectorid');
          echo '<tr><td>Sector:</td><td>'.DrawSelectBox('sectorid', $Sectors, 'loc_sector_id', '').'</td></tr>';
          echo '<tr><td>X:</td><td><input type="text" name="x" /></td></tr>';
          echo '<tr><td>Y:</td><td><input type="text" name="y" /></td></tr>';
          echo '<tr><td>Z:</td><td><input type="text" name="z" /></td></tr>';
          echo '<tr><td>Radius:</td><td><input type="text" name="radius" /></td></tr>';
          echo '<tr><td>Angle:</td><td><input type="text" name="angle" /></td></tr>';
          echo '<tr><td>Flags:</td><td>Not Supported</td></tr>';
          $Locations = PrepSelect('locations');
          echo '<tr><td>Previous Location</td><td>'.DrawSelectBox('locations', $Locations, 'previous', '', true).'</td></tr>';
          $LocationTypes = PrepSelect('location_type');

          echo '<tr><td>Location Type</td><td>'.DrawSelectBox('location_type',$LocationTypes,'type','', true).'</td></tr>';
          echo '</table><input type="submit" name="commit" value="Create Location"/>';
          echo '</form>';
        }
      }
    }
  }else{
    echo '<p class="error">You are not authorised to use these functions.</p>';
  }
}
?>
